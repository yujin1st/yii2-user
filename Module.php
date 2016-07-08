<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users;

use yii;
use yii\authclient\Collection;
use yii\base\Module as BaseModule;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;
use yujin1st\users\events\BuildUserMenuEvent;
use yujin1st\users\events\RbacEvent;
use yujin1st\users\rbac\Access;

/**
 * This is the main module class for the Yii2-user.
 *
 * @property array $modelMap
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Module extends BaseModule
{
  const VERSION = '1.0.0-dev';

  /** Email is changed right after user enter's new email address. */
  const STRATEGY_INSECURE = 0;

  /** Email is changed after user clicks confirmation link sent to his new email address. */
  const STRATEGY_DEFAULT = 1;

  /** Email is changed after user clicks both confirmation links sent to his old and new email addresses. */
  const STRATEGY_SECURE = 2;

  /** @var bool Whether to show flash messages. */
  public $enableFlashMessages = false;

  /** @var bool Whether to enable registration. */
  public $enableRegistration = true;

  /** @var bool Whether to remove password field from registration form. */
  public $enableGeneratingPassword = false;

  /** @var bool Whether user has to confirm his account. */
  public $enableConfirmation = true;

  /** @var bool Whether to allow logging in without confirmation. */
  public $enableUnconfirmedLogin = false;

  /** @var bool Whether to enable password recovery. */
  public $enablePasswordRecovery = true;

  /** @var int Email changing strategy. */
  public $emailChangeStrategy = self::STRATEGY_DEFAULT;

  /** @var int The time you want the user will be remembered without asking for credentials. */
  public $rememberFor = 1209600; // two weeks

  /** @var int The time before a confirmation token becomes invalid. */
  public $confirmWithin = 86400; // 24 hours

  /** @var int The time before a recovery token becomes invalid. */
  public $recoverWithin = 21600; // 6 hours

  /** @var int Cost parameter used by the Blowfish hash algorithm. */
  public $cost = 10;

  /** @var array An array of administrator's usernames. */
  public $admins = [];

  /** @var string The Administrator permission name. */
  public $adminPermission;

  /** @var array Mailer configuration */
  public $mailer = [];

  /** @var array Model map */
  public $modelMap = [];

  /** global event for collecting app rbac rules */
  const EVENT_COLLECT_ROLES = 'collectRoles';

  const EVENT_BUILD_USER_MENU = 'buildUserMenu';

  /**
   * @var string The prefix for user module URL.
   *
   * @See [[GroupUrlRule::prefix]]
   */
  public $urlPrefix = 'user';


  /** @var array The rules to be used in URL management. */
  public $urlRules = [
    '<id:\d+>' => 'profile/show',
    '<action:(login|logout)>' => 'security/<action>',
    '<action:(register|resend)>' => 'registration/<action>',
    'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
    'forgot' => 'recovery/request',
    'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
    'settings/<action:\w+>' => 'settings/<action>'
  ];


  /** @var array Model's map */
  private $_modelMap = [
    'User' => 'yujin1st\users\models\User',
    'Account' => 'yujin1st\users\models\Account',
    'Profile' => 'yujin1st\users\models\Profile',
    'Token' => 'yujin1st\users\models\Token',
    'RegistrationForm' => 'yujin1st\users\models\RegistrationForm',
    'ResendForm' => 'yujin1st\users\models\ResendForm',
    'LoginForm' => 'yujin1st\users\models\LoginForm',
    'SettingsForm' => 'yujin1st\users\models\SettingsForm',
    'RecoveryForm' => 'yujin1st\users\models\RecoveryForm',
    'UserSearch' => 'yujin1st\users\models\search\UserSearch',
  ];

  /** @inheritdoc */
  public function init() {
    parent::init();
    $this->bootstrap();
  }

  /**
   *
   */
  public function setUrlRules() {
    $configUrlRule = [
      'prefix' => $this->urlPrefix,
      'rules' => $this->urlRules,
    ];

    if ($this->urlPrefix != 'users') {
      $configUrlRule['routePrefix'] = 'users';
    }

    $configUrlRule['class'] = 'yii\web\GroupUrlRule';
    $rule = Yii::createObject($configUrlRule);

    Yii::$app->urlManager->addRules([$rule], false);
  }

  /** @inheritdoc */
  public function bootstrap() {
    $app = Yii::$app;

    /** @var \yii\db\ActiveRecord $modelName */
    $this->_modelMap = array_merge($this->_modelMap, $this->modelMap);
    foreach ($this->_modelMap as $name => $definition) {
      $class = "yujin1st\\users\\models\\" . $name;
      Yii::$container->set($class, $definition);
      $modelName = is_array($definition) ? $definition['class'] : $definition;
      $this->modelMap[$name] = $modelName;
    }

    if ($app instanceof ConsoleApplication) {
      $this->controllerNamespace = 'yujin1st\users\commands';
    } else {
      $this->controllerNamespace = 'yujin1st\users\controllers';
      $this->viewPath = '@yujin1st/users/views';

      Yii::$container->set('yii\web\User', [
        'enableAutoLogin' => true,
        'loginUrl' => ['/users/security/login'],
        'identityClass' => $this->modelMap['User'],
      ]);

      if (!$app->has('authClientCollection')) {
        $app->set('authClientCollection', [
          'class' => Collection::className(),
        ]);
      }
    }

    $this->setUrlRules();

    if (!isset($app->get('i18n')->translations['users*'])) {
      $app->get('i18n')->translations['users*'] = [
        'class' => PhpMessageSource::className(),
        'basePath' => __DIR__ . '/messages',
        'sourceLanguage' => 'en-US'
      ];
    }

    $this->on(Module::EVENT_COLLECT_ROLES, function ($event) {
      /** @var $event RbacEvent */
      $event->addClass(Access::className());
    });


    Yii::$container->set('yujin1st\users\Mailer', $this->mailer);
  }


  /**
   * Collecting side menu over modules
   */
  public function getUserMenu() {
    $event = new BuildUserMenuEvent();
    $this->trigger(self::EVENT_BUILD_USER_MENU, $event);
    $menu = [];
    foreach ($event->items as $block => $items) {
      foreach ($items as $item) {
        $menu[] = $item;
      }
    }
    return $menu;
  }

}
