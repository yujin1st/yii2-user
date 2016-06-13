<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user\models;

use yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yujin1st\user\helpers\Password;
use yujin1st\user\Mailer;
use yujin1st\user\models\query\UserQuery;
use yujin1st\user\Module;
use yujin1st\user\rbac\Access;
use yujin1st\user\traits\ModuleTrait;

/**
 * User ActiveRecord model.
 *
 * @property bool $isAdmin
 * @property bool $isBlocked
 * @property bool $isConfirmed
 *
 * Database fields:
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $unconfirmedEmail
 * @property string $passwordHash
 * @property string $authKey
 * @property integer $registrationIp
 * @property integer $confirmTime
 * @property integer $blockedAt
 * @property integer $createTime
 * @property integer $updateTime
 * @property integer $flags
 *
 * @property array $roles
 *
 * Defined relations:
 * @property Account[] $accounts
 * @property Profile $profile
 *
 * Dependencies:
 * @property-read Module $module
 * @property-read Mailer $mailer
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
  use ModuleTrait;

  const BEFORE_CREATE = 'beforeCreate';
  const AFTER_CREATE = 'afterCreate';
  const BEFORE_REGISTER = 'beforeRegister';
  const AFTER_REGISTER = 'afterRegister';

  // following constants are used on secured email changing process
  const OLD_EMAIL_CONFIRMED = 0b1;
  const NEW_EMAIL_CONFIRMED = 0b10;

  /** @var string Plain password. Used for model validation. */
  public $password;

  /** @var  array User roles */
  public $_roles;

  /** @var Profile|null */
  private $_profile;

  /** @var string Default username regexp */
  public static $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';

  const SCENARIO_CREATE = 'create';
  const SCENARIO_UPDATE = 'update';
  const SCENARIO_UPDATE_ROLES = 'update_roles';
  const SCENARIO_CONNECT = 'connect';
  const SCENARIO_REGISTER = 'register';
  const SCENARIO_REGISTER_MANUALLY = 'register_manually';

  /**
   * @inheritdoc
   * @return UserQuery the active query used by this AR class.
   */
  public static function find() {
    return new UserQuery(get_called_class());
  }

  /**
   * @return Mailer
   * @throws \yii\base\InvalidConfigException
   */
  protected function getMailer() {
    return Yii::$container->get(Mailer::className());
  }

  /**
   * @return bool Whether the user is confirmed or not.
   */
  public function getIsConfirmed() {
    return $this->confirmTime != null;
  }

  /**
   * @return bool Whether the user is blocked or not.
   */
  public function getIsBlocked() {
    return $this->blockedAt != null;
  }

  /**
   * @return bool Whether the user is an admin or not.
   */
  public function getIsAdmin() {
    return (\Yii::$app->getAuthManager() && $this->module->adminPermission ? \Yii::$app->user->can($this->module->adminPermission) : false) || in_array($this->username, $this->module->admins);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getProfile() {
    return $this->hasOne($this->module->modelMap['Profile'], ['userId' => 'id']);
  }

  /**
   * @param Profile $profile
   */
  public function setProfile(Profile $profile) {
    $this->_profile = $profile;
  }

  /**
   * @return Account[] Connected accounts ($provider => $account)
   */
  public function getAccounts() {
    $connected = [];
    $accounts = $this->hasMany($this->module->modelMap['Account'], ['userId' => 'id'])->all();

    /** @var Account $account */
    foreach ($accounts as $account) {
      $connected[$account->provider] = $account;
    }

    return $connected;
  }

  /** @inheritdoc */
  public function getId() {
    return $this->getAttribute('id');
  }

  /** @inheritdoc */
  public function getAuthKey() {
    return $this->getAttribute('authKey');
  }

  /** @inheritdoc */
  public function attributeLabels() {
    return [
      'username' => Yii::t('user', 'Username'),
      'email' => Yii::t('user', 'Email'),
      'registrationIp' => Yii::t('user', 'Registration ip'),
      'unconfirmedEmail' => Yii::t('user', 'New email'),
      'password' => Yii::t('user', 'Password'),
      'createTime' => Yii::t('user', 'Registration time'),
      'confirmTime' => Yii::t('user', 'Confirmation time'),
      'roles' => Yii::t('user', 'Roles'),
    ];
  }

  /** @inheritdoc */
  public function behaviors() {
    return [
      [
        'class' => TimestampBehavior::className(),
        'createdAtAttribute' => 'createTime',
        'updatedAtAttribute' => 'updateTime',
      ],
    ];
  }

  /** @inheritdoc */
  public function scenarios() {
    $scenarios = parent::scenarios();
    return ArrayHelper::merge($scenarios, [
      self::SCENARIO_REGISTER => ['username', 'email', 'password'],
      self::SCENARIO_REGISTER_MANUALLY => ['username', 'email', 'password'],
      self::SCENARIO_UPDATE_ROLES => ['roles'],
      self::SCENARIO_CREATE => ['username', 'email', 'password'],
      self::SCENARIO_CONNECT => ['username', 'email'],
      self::SCENARIO_UPDATE => ['username', 'email', 'password'],

      'settings' => ['username', 'email', 'password'],
    ]);
  }

  /** @inheritdoc */
  public function rules() {
    return [
      // username rules
      'usernameRequired' => [
        'username', 'required', 'on' => [
          self::SCENARIO_REGISTER,
          self::SCENARIO_CREATE,
          self::SCENARIO_CONNECT,
          self::SCENARIO_UPDATE,
          self::SCENARIO_REGISTER_MANUALLY
        ]
      ],
      'usernameMatch' => ['username', 'match', 'pattern' => static::$usernameRegexp],
      'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
      'usernameUnique' => ['username', 'unique', 'message' => Yii::t('user', 'This username has already been taken')],
      'usernameTrim' => ['username', 'trim'],

      // email rules
      'emailRequired' => [
        'email', 'required', 'on' => [
          self::SCENARIO_REGISTER,
          self::SCENARIO_CONNECT,
        ]
      ],
      'emailPattern' => ['email', 'email'],
      'emailLength' => ['email', 'string', 'max' => 255],
      'emailUnique' => ['email', 'unique', 'message' => Yii::t('user', 'This email address has already been taken')],
      'emailTrim' => ['email', 'trim'],

      // password rules
      'passwordRequired' => ['password', 'required', 'on' => ['register']],
      'passwordLength' => ['password', 'string', 'min' => 6, 'on' => ['register', 'create']],
    ];
  }

  /** @inheritdoc */
  public function validateAuthKey($authKey) {
    return $this->getAttribute('authKey') === $authKey;
  }

  /**
   * Creates new user account. It generates password if it is not provided by user.
   *
   * @return bool
   */
  public function create() {
    if ($this->getIsNewRecord() == false) {
      throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
    }

    $this->confirmTime = time();
    $this->password = $this->password == null ? Password::generate(8) : $this->password;

    $this->trigger(self::BEFORE_CREATE);

    if (!$this->save()) {
      return false;
    }

    if ($this->email) $this->mailer->sendWelcomeMessage($this, null, true);
    $this->trigger(self::AFTER_CREATE);

    return true;
  }

  /**
   * This method is used to register new user account. If Module::enableConfirmation is set true, this method
   * will generate new confirmation token and use mailer to send it to the user.
   *
   * @return bool
   */
  public function register() {
    if ($this->getIsNewRecord() == false) {
      throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
    }

    $this->confirmTime = $this->module->enableConfirmation ? null : time();
    $this->password = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;

    $this->trigger(self::BEFORE_REGISTER);

    if (!$this->save()) {
      return false;
    }

    if ($this->module->enableConfirmation) {
      /** @var Token $token */
      $token = Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
      $token->link('user', $this);
    }

    if ($this->email) $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);

    $this->trigger(self::AFTER_REGISTER);

    return true;
  }

  /**
   * Attempts user confirmation.
   *
   * @param string $code Confirmation code.
   *
   * @return boolean
   */
  public function attemptConfirmation($code) {
    $token = Token::find()->where([
      'userId' => $this->id,
      'code' => $code,
      'type' => Token::TYPE_CONFIRMATION,
    ])->one();

    if ($token instanceof Token && !$token->isExpired) {
      $token->delete();
      if (($success = $this->confirm())) {
        Yii::$app->user->login($this, $this->module->rememberFor);
        $message = Yii::t('user', 'Thank you, registration is now complete.');
      } else {
        $message = Yii::t('user', 'Something went wrong and your account has not been confirmed.');
      }
    } else {
      $success = false;
      $message = Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.');
    }

    Yii::$app->session->setFlash($success ? 'success' : 'danger', $message);

    return $success;
  }

  /**
   * This method attempts changing user email. If user's "unconfirmedEmail" field is empty is returns false, else if
   * somebody already has email that equals user's "unconfirmedEmail" it returns false, otherwise returns true and
   * updates user's password.
   *
   * @param string $code
   *
   * @return bool
   * @throws \Exception
   */
  public function attemptEmailChange($code) {
    /** @var Token $token */
    $token = Token::find()->where(['userId' => $this->id, 'code' => $code])
      ->andWhere(['in', 'type', [Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL]])
      ->one();

    if (empty($this->unconfirmedEmail) || $token === null || $token->isExpired) {
      Yii::$app->session->setFlash('danger', Yii::t('user', 'Your confirmation token is invalid or expired'));
    } else {
      $token->delete();

      if (empty($this->unconfirmedEmail)) {
        Yii::$app->session->setFlash('danger', Yii::t('user', 'An error occurred processing your request'));
      } elseif (User::find()->andWhere(['email' => $this->unconfirmedEmail])->exists() == false) {
        if ($this->module->emailChangeStrategy == Module::STRATEGY_SECURE) {
          switch ($token->type) {
            case Token::TYPE_CONFIRM_NEW_EMAIL:
              $this->flags |= self::NEW_EMAIL_CONFIRMED;
              Yii::$app->session->setFlash('success', Yii::t('user', 'Awesome, almost there. Now you need to click the confirmation link sent to your old email address'));
              break;
            case Token::TYPE_CONFIRM_OLD_EMAIL:
              $this->flags |= self::OLD_EMAIL_CONFIRMED;
              Yii::$app->session->setFlash('success', Yii::t('user', 'Awesome, almost there. Now you need to click the confirmation link sent to your new email address'));
              break;
          }
        }

        if ($this->module->emailChangeStrategy == Module::STRATEGY_DEFAULT || ($this->flags & self::NEW_EMAIL_CONFIRMED && $this->flags & self::OLD_EMAIL_CONFIRMED)) {
          $this->email = $this->unconfirmedEmail;
          $this->unconfirmedEmail = null;
          Yii::$app->session->setFlash('success', Yii::t('user', 'Your email address has been changed'));
        }
        $this->save(false);
      }
    }
  }

  /**
   * Confirms the user by setting 'confirmTime' field to current time.
   */
  public function confirm() {
    return (bool)$this->updateAttributes(['confirmTime' => time()]);
  }

  /**
   * Resets password.
   *
   * @param string $password
   *
   * @return bool
   */
  public function resetPassword($password) {
    return (bool)$this->updateAttributes(['passwordHash' => Password::hash($password)]);
  }

  /**
   * Blocks the user by setting 'blockedAt' field to current time and regenerates authKey.
   *
   * @return bool
   */
  public function block() {
    return (bool)$this->updateAttributes([
      'blockedAt' => time(),
      'authKey' => Yii::$app->security->generateRandomString(),
    ]);
  }

  /**
   * UnBlocks the user by setting 'blockedAt' field to null.
   *
   * @return bool
   */
  public function unblock() {
    return (bool)$this->updateAttributes(['blockedAt' => null]);
  }

  /**
   * Generates new username based on email address, or creates new username
   * like "user1".
   */
  public function generateUsername() {
    // try to use name part of email
    $this->username = explode('@', $this->email)[0];
    if ($this->validate(['username'])) {
      return $this->username;
    }

    // generate username like "user1", "user2", etc...
    while (!$this->validate(['username'])) {
      $row = (new Query())
        ->from('{{%user}}')
        ->select('MAX(id) as id')
        ->one();

      $this->username = 'user' . ++$row['id'];
    }

    return $this->username;
  }

  /** @inheritdoc */
  public function beforeSave($insert) {
    if ($insert) {
      $this->setAttribute('authKey', Yii::$app->security->generateRandomString());
      if (Yii::$app instanceof WebApplication) {
        $this->setAttribute('registrationIp', Yii::$app->request->userIP);
      }
    }

    if (!empty($this->password)) {
      $this->setAttribute('passwordHash', Password::hash($this->password));
    }

    return parent::beforeSave($insert);
  }

  /**
   * Assign roles to new user
   * Overwrite this method for own purposes
   */
  public function assignRoles() {
    $auth = Yii::$app->authManager;
    $auth->assign($auth->getRole(Access::ROLE_USER), $this->id);
  }

  /** @inheritdoc */
  public function afterSave($insert, $changedAttributes) {
    parent::afterSave($insert, $changedAttributes);
    if ($insert) {
      if ($this->_profile == null) {
        $this->_profile = Yii::createObject(Profile::className());
      }
      $this->_profile->link('user', $this);
    }

    if ($insert) {
      $this->assignRoles();
    }

    if ($this->scenario == self::SCENARIO_UPDATE_ROLES) {
      $auth = Yii::$app->authManager;
      $auth->revokeAll($this->id);
      foreach ($this->_roles as $role) {
        $auth->assign($auth->getRole($role), $this->id);
      }
    }
  }

  /** @inheritdoc */
  public static function tableName() {
    return '{{%user}}';
  }

  /** @inheritdoc */
  public static function findIdentity($id) {
    return static::findOne($id);
  }

  /**
   * Finds a user by the given username.
   *
   * @param string $username Username to be used on search.
   *
   * @return User
   */
  public static function findIdentityByUsername($username) {
    return self::find()->andWhere(['username' => $username])->one();
  }

  /**
   * Finds a user by the given email.
   *
   * @param string $email Email to be used on search.
   *
   * @return  User
   */
  public static function findUserByEmail($email) {
    return self::find()->andWhere(['email' => $email])->one();
  }

  /**
   * Finds a user by the given username or email.
   *
   * @param string $usernameOrEmail Username or email to be used on search.
   *
   * @return  User
   */
  public static function findIdentityByUsernameOrEmail($usernameOrEmail) {
    if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
      return self::findUserByEmail($usernameOrEmail);
    }
    return User::findIdentityByUsername($usernameOrEmail);
  }

  /** @inheritdoc */
  public static function findIdentityByAccessToken($token, $type = null) {
    throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
  }


  /**
   * @return array
   */
  public function getRoles() {
    $auth = Yii::$app->authManager;
    return $this->isNewRecord ? [] : ArrayHelper::map($auth->getRolesByUser($this->id), 'name', 'name');
  }

  /**
   * @return array
   */
  public function getRolesTitles() {
    $titles = [];
    foreach ($this->roles as $role) {
      $record = Yii::$app->authManager->getRole($role);
      $titles[] = $record->description ?: $record->name;
    }
    return implode(', ', $titles);
  }

  /**
   * @param array $roles
   */
  public function setRoles($roles) {
    $this->_roles = $roles ?: [];
  }


}
