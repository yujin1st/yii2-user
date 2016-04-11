<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user\controllers;

use yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yujin1st\user\models\Account;
use yujin1st\user\models\RegistrationForm;
use yujin1st\user\models\ResendForm;
use yujin1st\user\models\User;
use yujin1st\user\traits\AjaxValidationTrait;
use yujin1st\user\traits\EventTrait;

/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property \yujin1st\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationController extends Controller
{
  use AjaxValidationTrait;
  use EventTrait;

  /**
   * Event is triggered after creating RegistrationForm class.
   * Triggered with \yujin1st\user\events\FormEvent.
   */
  const EVENT_BEFORE_REGISTER = 'beforeRegister';

  /**
   * Event is triggered after successful registration.
   * Triggered with \yujin1st\user\events\FormEvent.
   */
  const EVENT_AFTER_REGISTER = 'afterRegister';

  /**
   * Event is triggered before connecting user to social account.
   * Triggered with \yujin1st\user\events\UserEvent.
   */
  const EVENT_BEFORE_CONNECT = 'beforeConnect';

  /**
   * Event is triggered after connecting user to social account.
   * Triggered with \yujin1st\user\events\UserEvent.
   */
  const EVENT_AFTER_CONNECT = 'afterConnect';

  /**
   * Event is triggered before confirming user.
   * Triggered with \yujin1st\user\events\UserEvent.
   */
  const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

  /**
   * Event is triggered before confirming user.
   * Triggered with \yujin1st\user\events\UserEvent.
   */
  const EVENT_AFTER_CONFIRM = 'afterConfirm';

  /**
   * Event is triggered after creating ResendForm class.
   * Triggered with \yujin1st\user\events\FormEvent.
   */
  const EVENT_BEFORE_RESEND = 'beforeResend';

  /**
   * Event is triggered after successful resending of confirmation email.
   * Triggered with \yujin1st\user\events\FormEvent.
   */
  const EVENT_AFTER_RESEND = 'afterResend';


  /** @inheritdoc */
  public function behaviors() {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          ['allow' => true, 'actions' => ['register', 'connect'], 'roles' => ['?']],
          ['allow' => true, 'actions' => ['confirm', 'resend'], 'roles' => ['?', '@']],
        ],
      ],
    ];
  }

  /**
   * Displays the registration page.
   * After successful registration if enableConfirmation is enabled shows info message otherwise redirects to home page.
   *
   * @return string
   * @throws \yii\web\HttpException
   */
  public function actionRegister() {
    if (!$this->module->enableRegistration) {
      throw new NotFoundHttpException();
    }

    /** @var RegistrationForm $model */
    $model = Yii::createObject(RegistrationForm::className());
    $event = $this->getFormEvent($model);

    $this->trigger(self::EVENT_BEFORE_REGISTER, $event);

    $this->performAjaxValidation($model);

    if ($model->load(Yii::$app->request->post()) && $model->register()) {

      $this->trigger(self::EVENT_AFTER_REGISTER, $event);

      return $this->render('/message', [
        'title' => Yii::t('user', 'Your account has been created'),
        'module' => $this->module,
      ]);
    }

    return $this->render('register', [
      'model' => $model,
      'module' => $this->module,
    ]);
  }

  /**
   * Displays page where user can create new account that will be connected to social account.
   *
   * @param string $code
   *
   * @return string
   * @throws NotFoundHttpException
   */
  public function actionConnect($code) {
    $account = Account::find()->byCode($code)->one();

    if ($account === null || $account->getIsConnected()) {
      throw new NotFoundHttpException();
    }

    /** @var User $user */
    $user = Yii::createObject([
      'class' => User::className(),
      'scenario' => 'connect',
      'username' => $account->username,
      'email' => $account->email,
    ]);

    $event = $this->getConnectEvent($account, $user);

    $this->trigger(self::EVENT_BEFORE_CONNECT, $event);

    if ($user->load(Yii::$app->request->post()) && $user->create()) {
      $account->connect($user);
      $this->trigger(self::EVENT_AFTER_CONNECT, $event);
      Yii::$app->user->login($user, $this->module->rememberFor);
      return $this->goBack();
    }

    return $this->render('connect', [
      'model' => $user,
      'account' => $account,
    ]);
  }

  /**
   * Confirms user's account. If confirmation was successful logs the user and shows success message. Otherwise
   * shows error message.
   *
   * @param int $id
   * @param string $code
   *
   * @return string
   * @throws \yii\web\HttpException
   */
  public function actionConfirm($id, $code) {
    $user = User::findIdentity($id);

    if ($user === null || $this->module->enableConfirmation == false) {
      throw new NotFoundHttpException();
    }

    $event = $this->getUserEvent($user);

    $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);

    $user->attemptConfirmation($code);

    $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

    return $this->render('/message', [
      'title' => Yii::t('user', 'Account confirmation'),
      'module' => $this->module,
    ]);
  }

  /**
   * Displays page where user can request new confirmation token. If resending was successful, displays message.
   *
   * @return string
   * @throws \yii\web\HttpException
   */
  public function actionResend() {
    if ($this->module->enableConfirmation == false) {
      throw new NotFoundHttpException();
    }

    /** @var ResendForm $model */
    $model = Yii::createObject(ResendForm::className());
    $event = $this->getFormEvent($model);

    $this->trigger(self::EVENT_BEFORE_RESEND, $event);

    $this->performAjaxValidation($model);

    if ($model->load(Yii::$app->request->post()) && $model->resend()) {

      $this->trigger(self::EVENT_AFTER_RESEND, $event);

      return $this->render('/message', [
        'title' => Yii::t('user', 'A new confirmation link has been sent'),
        'module' => $this->module,
      ]);
    }

    return $this->render('resend', [
      'model' => $model,
    ]);
  }
}
