<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user;

use yii;
use yii\authclient\Collection;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;
use yujin1st\user\events\RbacEvent;
use yujin1st\user\rbac\Access;
use yujin1st\user\rbac\Rbac;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{
  /** @var array Model's map */
  private $_modelMap = [
    'User' => 'yujin1st\user\models\User',
    'Account' => 'yujin1st\user\models\Account',
    'Profile' => 'yujin1st\user\models\Profile',
    'Token' => 'yujin1st\user\models\Token',
    'RegistrationForm' => 'yujin1st\user\models\RegistrationForm',
    'ResendForm' => 'yujin1st\user\models\ResendForm',
    'LoginForm' => 'yujin1st\user\models\LoginForm',
    'SettingsForm' => 'yujin1st\user\models\SettingsForm',
    'RecoveryForm' => 'yujin1st\user\models\RecoveryForm',
    'UserSearch' => 'yujin1st\user\models\search\UserSearch',
  ];

  /** @inheritdoc */
  public function bootstrap($app) {
    /** @var Module $module */
    /** @var \yii\db\ActiveRecord $modelName */
    if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module) {
      $this->_modelMap = array_merge($this->_modelMap, $module->modelMap);
      foreach ($this->_modelMap as $name => $definition) {
        $class = "yujin1st\\user\\models\\" . $name;
        Yii::$container->set($class, $definition);
        $modelName = is_array($definition) ? $definition['class'] : $definition;
        $module->modelMap[$name] = $modelName;
      }

      if ($app instanceof ConsoleApplication) {
        $module->controllerNamespace = 'yujin1st\user\commands';
      } else {
        Yii::$container->set('yii\web\User', [
          'enableAutoLogin' => true,
          'loginUrl' => ['/user/security/login'],
          'identityClass' => $module->modelMap['User'],
        ]);

        $configUrlRule = [
          'prefix' => $module->urlPrefix,
          'rules' => $module->urlRules,
        ];

        if ($module->urlPrefix != 'user') {
          $configUrlRule['routePrefix'] = 'user';
        }

        $configUrlRule['class'] = 'yii\web\GroupUrlRule';
        $rule = Yii::createObject($configUrlRule);

        $app->urlManager->addRules([$rule], false);

        if (!$app->has('authClientCollection')) {
          $app->set('authClientCollection', [
            'class' => Collection::className(),
          ]);
        }
      }

      if (!isset($app->get('i18n')->translations['user*'])) {
        $app->get('i18n')->translations['user*'] = [
          'class' => PhpMessageSource::className(),
          'basePath' => __DIR__ . '/messages',
          'sourceLanguage' => 'en-US'
        ];
      }

      Yii::$app->on(Rbac::EVENT_COLLECT_ROLES, function ($event) {
        /** @var $event RbacEvent */
        $event->addClass(Access::className());
      });

      
      Yii::$container->set('yujin1st\user\Mailer', $module->mailer);
    }
  }


}
