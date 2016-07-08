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
use yujin1st\users\Mailer;
use yujin1st\users\traits\ModuleTrait;

/**
 * ResendForm gets user email address and validates if user has already confirmed his account. If so, it shows error
 * message, otherwise it generates and sends new confirmation token to user.
 *
 * @property User $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ResendForm extends Model
{
  use ModuleTrait;
  /** @var string */
  public $email;

  /** @var User */
  private $_user;

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

  /**
   * @return User
   */
  public function getUser() {
    if ($this->_user === null) {
      $this->_user = User::findUserByEmail($this->email);
    }

    return $this->_user;
  }

  /** @inheritdoc */
  public function rules() {
    return [
      'emailRequired' => ['email', 'required'],
      'emailPattern' => ['email', 'email'],
      'emailExist' => ['email', 'exist', 'targetClass' => $this->module->modelMap['User']],
      'emailConfirmed' => [
        'email',
        function () {
          if ($this->user != null && $this->user->getIsConfirmed()) {
            $this->addError('email', Yii::t('user', 'This account has already been confirmed'));
          }
        }
      ],
    ];
  }

  /** @inheritdoc */
  public function attributeLabels() {
    return [
      'email' => Yii::t('user', 'Email'),
    ];
  }

  /** @inheritdoc */
  public function formName() {
    return 'resend-form';
  }

  /**
   * Creates new confirmation token and sends it to the user.
   *
   * @return bool
   */
  public function resend() {
    if (!$this->validate()) {
      return false;
    }
    /** @var Token $token */
    $token = Yii::createObject([
      'class' => Token::className(),
      'userId' => $this->user->id,
      'type' => Token::TYPE_CONFIRMATION,
    ]);
    $token->save(false);
    $this->mailer->sendConfirmationMessage($this->user, $token);
    Yii::$app->session->setFlash('info', Yii::t('user', 'A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.'));

    return true;
  }
}
