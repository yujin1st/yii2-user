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
use yujin1st\users\traits\ModuleTrait;

/**
 * LoginForm get user's login and password, validates them and logs the user in. If user has been blocked, it adds
 * an error to login form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginForm extends Model
{
  use ModuleTrait;

  /** @var string User's email or username */
  public $login;

  /** @var string User's plain password */
  public $password;

  /** @var string Whether to remember the user */
  public $rememberMe = false;

  /** @var \yujin1st\users\models\User */
  protected $user;


  /** @inheritdoc */
  public function attributeLabels() {
    return [
      'login' => Yii::t('users', 'Login'),
      'password' => Yii::t('users', 'Password'),
      'rememberMe' => Yii::t('users', 'Remember me next time'),
    ];
  }

  /** @inheritdoc */
  public function rules() {
    return [
      'requiredFields' => [['login', 'password'], 'required'],
      'loginTrim' => ['login', 'trim'],
      'passwordValidate' => [
        'password',
        function ($attribute) {
          if ($this->user === null || !Password::validate($this->password, $this->user->passwordHash)) {
            $this->addError($attribute, Yii::t('users', 'Invalid login or password'));
          }
        }
      ],
      'confirmationValidate' => [
        'login',
        function ($attribute) {
          if ($this->user !== null) {
            $confirmationRequired = $this->module->enableConfirmation && !$this->module->enableUnconfirmedLogin;
            if ($confirmationRequired && !$this->user->getIsConfirmed()) {
              $this->addError($attribute, Yii::t('users', 'You need to confirm your email address'));
            }
            if ($this->user->getIsBlocked()) {
              $this->addError($attribute, Yii::t('users', 'Your account has been blocked'));
            }
          }
        }
      ],
      'rememberMe' => ['rememberMe', 'boolean'],
    ];
  }

  /**
   * Validates form and logs the user in.
   *
   * @return bool whether the user is logged in successfully
   */
  public function login() {
    if ($this->validate()) {
      return Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0);
    } else {
      return false;
    }
  }

  /** @inheritdoc */
  public function formName() {
    return 'login-form';
  }

  /** @inheritdoc */
  public function beforeValidate() {
    if (parent::beforeValidate()) {
      $this->user = User::findIdentityByUsernameOrEmail(trim($this->login));
      return true;
    } else {
      return false;
    }
  }
}
