<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\controllers;

use yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yujin1st\users\models\RecoveryForm;
use yujin1st\users\models\Token;
use yujin1st\users\traits\AjaxValidationTrait;
use yujin1st\users\traits\EventTrait;

/**
 * RecoveryController manages password recovery process.
 *
 * @property \yujin1st\users\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends Controller
{
  use AjaxValidationTrait;
  use EventTrait;

  /**
   * Event is triggered before requesting password reset.
   * Triggered with \yujin1st\users\events\FormEvent.
   */
  const EVENT_BEFORE_REQUEST = 'beforeRequest';

  /**
   * Event is triggered after requesting password reset.
   * Triggered with \yujin1st\users\events\FormEvent.
   */
  const EVENT_AFTER_REQUEST = 'afterRequest';

  /**
   * Event is triggered before validating recovery token.
   * Triggered with \yujin1st\users\events\ResetPasswordEvent. May not have $form property set.
   */
  const EVENT_BEFORE_TOKEN_VALIDATE = 'beforeTokenValidate';

  /**
   * Event is triggered after validating recovery token.
   * Triggered with \yujin1st\users\events\ResetPasswordEvent. May not have $form property set.
   */
  const EVENT_AFTER_TOKEN_VALIDATE = 'afterTokenValidate';

  /**
   * Event is triggered before resetting password.
   * Triggered with \yujin1st\users\events\ResetPasswordEvent.
   */
  const EVENT_BEFORE_RESET = 'beforeReset';

  /**
   * Event is triggered after resetting password.
   * Triggered with \yujin1st\users\events\ResetPasswordEvent.
   */
  const EVENT_AFTER_RESET = 'afterReset';


  /** @inheritdoc */
  public function behaviors() {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          ['allow' => true, 'actions' => ['request', 'reset'], 'roles' => ['?']],
        ],
      ],
    ];
  }

  /**
   * Shows page where user can request password recovery.
   *
   * @return string
   * @throws \yii\web\NotFoundHttpException
   */
  public function actionRequest() {
    if (!$this->module->enablePasswordRecovery) {
      throw new NotFoundHttpException();
    }

    /** @var RecoveryForm $model */
    $model = Yii::createObject([
      'class' => RecoveryForm::className(),
      'scenario' => 'request',
    ]);
    $event = $this->getFormEvent($model);

    $this->performAjaxValidation($model);
    $this->trigger(self::EVENT_BEFORE_REQUEST, $event);

    if ($model->load(Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
      $this->trigger(self::EVENT_AFTER_REQUEST, $event);
      return $this->render('/message', [
        'title' => Yii::t('users', 'Recovery message sent'),
        'module' => $this->module,
      ]);
    }

    return $this->render('request', [
      'model' => $model,
    ]);
  }

  /**
   * Displays page where user can reset password.
   *
   * @param int $id
   * @param string $code
   *
   * @return string
   * @throws \yii\web\NotFoundHttpException
   */
  public function actionReset($id, $code) {
    if (!$this->module->enablePasswordRecovery) {
      throw new NotFoundHttpException();
    }

    /** @var Token $token */
    $token = Token::find()->where(['userId' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY])->one();
    $event = $this->getResetPasswordEvent($token);

    $this->trigger(self::EVENT_BEFORE_TOKEN_VALIDATE, $event);

    if ($token === null || $token->isExpired || $token->user === null) {
      $this->trigger(self::EVENT_AFTER_TOKEN_VALIDATE, $event);
      Yii::$app->session->setFlash('danger', Yii::t('users', 'Recovery link is invalid or expired. Please try requesting a new one.'));
      return $this->render('/message', [
        'title' => Yii::t('users', 'Invalid or expired link'),
        'module' => $this->module,
      ]);
    }

    /** @var RecoveryForm $model */
    $model = Yii::createObject([
      'class' => RecoveryForm::className(),
      'scenario' => 'reset',
    ]);
    $event->setForm($model);

    $this->performAjaxValidation($model);
    $this->trigger(self::EVENT_BEFORE_RESET, $event);

    if ($model->load(Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
      $this->trigger(self::EVENT_AFTER_RESET, $event);
      return $this->render('/message', [
        'title' => Yii::t('users', 'Password has been changed'),
        'module' => $this->module,
      ]);
    }

    return $this->render('reset', [
      'model' => $model,
    ]);
  }
}
