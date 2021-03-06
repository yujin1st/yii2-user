<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\models;

use yii;
use yii\base\Model;
use yujin1st\users\helpers\Password;
use yujin1st\users\Mailer;
use yujin1st\users\Module;
use yujin1st\users\traits\ModuleTrait;

/**
 * SettingsForm gets user's username, email and password and changes them.
 *
 * @property User $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsForm extends Model
{
  use ModuleTrait;

  /** @var string */
  public $email;

  /** @var string */
  public $username;

  /** @var string */
  public $new_password;

  /** @var string */
  public $current_password;

  /** @var Mailer */
  protected $mailer;

  /** @var User */
  private $_user;

  /** @return User */
  public function getUser() {
    if ($this->_user == null) {
      $this->_user = Yii::$app->user->identity;
    }

    return $this->_user;
  }

  /** @inheritdoc */
  public function __construct(Mailer $mailer, $config = []) {
    $this->mailer = $mailer;
    $this->setAttributes([
      'username' => $this->user->username,
      'email' => $this->user->unconfirmedEmail ?: $this->user->email,
    ], false);
    parent::__construct($config);
  }

  /** @inheritdoc */
  public function rules() {
    return [
      'usernameRequired' => ['username', 'required'],
      'usernameTrim' => ['username', 'filter', 'filter' => 'trim'],
      'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
      'usernamePattern' => ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
      'emailRequired' => ['email', 'required'],
      'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
      'emailPattern' => ['email', 'email'],
      'emailUsernameUnique' => [
        ['email', 'username'], 'unique', 'when' => function ($model, $attribute) {
          return $this->user->$attribute != $model->$attribute;
        }, 'targetClass' => $this->module->modelMap['User']
      ],
      'newPasswordLength' => ['new_password', 'string', 'min' => 6],
      'currentPasswordRequired' => ['current_password', 'required'],
      'currentPasswordValidate' => [
        'current_password', function ($attr) {
          if (!Password::validate($this->$attr, $this->user->passwordHash)) {
            $this->addError($attr, Yii::t('users', 'Current password is not valid'));
          }
        }
      ],
    ];
  }

  /** @inheritdoc */
  public function attributeLabels() {
    return [
      'email' => Yii::t('users', 'Email'),
      'username' => Yii::t('users', 'Username'),
      'new_password' => Yii::t('users', 'New password'),
      'current_password' => Yii::t('users', 'Current password'),
    ];
  }

  /** @inheritdoc */
  public function formName() {
    return 'settings-form';
  }

  /**
   * Saves new account settings.
   *
   * @return bool
   */
  public function save() {
    if ($this->validate()) {
      $this->user->scenario = 'settings';
      $this->user->username = $this->username;
      $this->user->password = $this->new_password;
      if ($this->email == $this->user->email && $this->user->unconfirmedEmail != null) {
        $this->user->unconfirmedEmail = null;
      } elseif ($this->email != $this->user->email) {
        switch ($this->module->emailChangeStrategy) {
          case Module::STRATEGY_INSECURE:
            $this->insecureEmailChange();
            break;
          case Module::STRATEGY_DEFAULT:
            $this->defaultEmailChange();
            break;
          case Module::STRATEGY_SECURE:
            $this->secureEmailChange();
            break;
          default:
            throw new \OutOfBoundsException('Invalid email changing strategy');
        }
      }

      return $this->user->save();
    }

    return false;
  }

  /**
   * Changes user's email address to given without any confirmation.
   */
  protected function insecureEmailChange() {
    $this->user->email = $this->email;
    Yii::$app->session->setFlash('success', Yii::t('users', 'Your email address has been changed'));
  }

  /**
   * Sends a confirmation message to user's email address with link to confirm changing of email.
   */
  protected function defaultEmailChange() {
    $this->user->unconfirmedEmail = $this->email;
    /** @var Token $token */
    $token = Yii::createObject([
      'class' => Token::className(),
      'userId' => $this->user->id,
      'type' => Token::TYPE_CONFIRM_NEW_EMAIL,
    ]);
    $token->save(false);
    $this->mailer->sendReconfirmationMessage($this->user, $token);
    Yii::$app->session->setFlash('info', Yii::t('users', 'A confirmation message has been sent to your new email address'));
  }

  /**
   * Sends a confirmation message to both old and new email addresses with link to confirm changing of email.
   *
   * @throws \yii\base\InvalidConfigException
   */
  protected function secureEmailChange() {
    $this->defaultEmailChange();
    /** @var Token $token */
    $token = Yii::createObject([
      'class' => Token::className(),
      'userId' => $this->user->id,
      'type' => Token::TYPE_CONFIRM_OLD_EMAIL,
    ]);
    $token->save(false);
    $this->mailer->sendReconfirmationMessage($this->user, $token);

    // unset flags if they exist
    $this->user->flags &= ~User::NEW_EMAIL_CONFIRMED;
    $this->user->flags &= ~User::OLD_EMAIL_CONFIRMED;
    $this->user->save(false);

    Yii::$app->session->setFlash('info', Yii::t('users', 'We have sent confirmation links to both old and new email addresses. You must click both links to complete your request'));
  }
}
