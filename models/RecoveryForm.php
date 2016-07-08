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
use yii\helpers\ArrayHelper;
use yujin1st\users\Mailer;
use yujin1st\users\traits\ModuleTrait;

/**
 * Model for collecting data on password recovery.
 *
 * @property \yujin1st\users\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryForm extends Model
{
  use ModuleTrait;
  /** @var string */
  public $email;

  /** @var string */
  public $password;

  /** @var User */
  protected $user;

  /** @var Mailer */
  protected $mailer;


  /**
   * @param Mailer $mailer
   * @param array $config
   */
  public function __construct(Mailer $mailer, $config = []) {
    $this->mailer = $mailer;
    parent::__construct($config);
  }

  /** @inheritdoc */
  public function attributeLabels() {
    return [
      'email' => Yii::t('user', 'Email'),
      'password' => Yii::t('user', 'Password'),
    ];
  }

  /** @inheritdoc */
  public function scenarios() {
    $scenarios = parent::scenarios();
    return ArrayHelper::merge($scenarios, [
      'request' => ['email'],
      'reset' => ['password'],
    ]);
  }

  /** @inheritdoc */
  public function rules() {
    return [
      'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
      'emailRequired' => ['email', 'required'],
      'emailPattern' => ['email', 'email'],
      'emailExist' => [
        'email',
        'exist',
        'targetClass' => $this->module->modelMap['User'],
        'message' => Yii::t('user', 'There is no user with this email address'),
      ],
      'emailUnconfirmed' => [
        'email',
        function ($attribute) {
          $this->user = User::findUserByEmail($this->email);
          if ($this->user !== null && $this->module->enableConfirmation && !$this->user->isConfirmed) {
            $this->addError($attribute, Yii::t('user', 'You need to confirm your email address'));
          }
          if ($this->user->isBlocked) {
            $this->addError($attribute, Yii::t('user', 'Your account has been blocked'));
          }
        }
      ],
      'passwordRequired' => ['password', 'required'],
      'passwordLength' => ['password', 'string', 'min' => 6],
    ];
  }

  /**
   * Sends recovery message.
   *
   * @return bool
   */
  public function sendRecoveryMessage() {
    if ($this->validate()) {
      /** @var Token $token */
      $token = Yii::createObject([
        'class' => Token::className(),
        'userId' => $this->user->id,
        'type' => Token::TYPE_RECOVERY,
      ]);

      if (!$token->save(false)) {
        return false;
      }

      if (!$this->mailer->sendRecoveryMessage($this->user, $token)) {
        return false;
      }

      Yii::$app->session->setFlash('info',
        Yii::t('user', 'An email has been sent with instructions for resetting your password')
      );

      return true;
    }

    return false;
  }

  /**
   * Resets user's password.
   *
   * @param Token $token
   *
   * @return bool
   */
  public function resetPassword(Token $token) {
    if (!$this->validate() || $token->user === null) {
      return false;
    }

    if ($token->user->resetPassword($this->password)) {
      Yii::$app->session->setFlash('success', Yii::t('user', 'Your password has been changed successfully.'));
      $token->delete();
    } else {
      Yii::$app->session->setFlash('danger', Yii::t('user', 'An error occurred and your password has not been changed. Please try again later.'));
    }

    return true;
  }

  /**
   * @inheritdoc
   */
  public function formName() {
    return 'recovery-form';
  }
}
