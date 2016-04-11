<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user\traits;

use yii\authclient\ClientInterface;
use yii\base\Model;
use yii\web\IdentityInterface;
use yujin1st\user\events\AuthEvent;
use yujin1st\user\events\ConnectEvent;
use yujin1st\user\events\FormEvent;
use yujin1st\user\events\ProfileEvent;
use yujin1st\user\events\ResetPasswordEvent;
use yujin1st\user\events\UserEvent;
use yujin1st\user\models\Account;
use yujin1st\user\models\Profile;
use yujin1st\user\models\RecoveryForm;
use yujin1st\user\models\Token;
use yujin1st\user\models\User;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait EventTrait
{
  /**
   * @param  Model $form
   * @return FormEvent
   * @throws \yii\base\InvalidConfigException
   */
  protected function getFormEvent($form) {
    return \Yii::createObject(['class' => FormEvent::className(), 'form' => $form]);
  }

  /**
   * @param  User|IdentityInterface $user
   * @return UserEvent
   * @throws \yii\base\InvalidConfigException
   */
  protected function getUserEvent($user) {
    return \Yii::createObject(['class' => UserEvent::className(), 'user' => $user]);
  }

  /**
   * @param  Profile $profile
   * @return ProfileEvent
   * @throws \yii\base\InvalidConfigException
   */
  protected function getProfileEvent($profile) {
    return \Yii::createObject(['class' => ProfileEvent::className(), 'profile' => $profile]);
  }


  /**
   * @param  Account $account
   * @param  User $user
   * @return ConnectEvent
   * @throws \yii\base\InvalidConfigException
   */
  protected function getConnectEvent($account, $user) {
    return \Yii::createObject(['class' => ConnectEvent::className(), 'account' => $account, 'user' => $user]);
  }

  /**
   * @param  Account $account
   * @param  ClientInterface $client
   * @return AuthEvent
   * @throws \yii\base\InvalidConfigException
   */
  protected function getAuthEvent($account, $client) {
    return \Yii::createObject(['class' => AuthEvent::className(), 'account' => $account, 'client' => $client]);
  }

  /**
   * @param  Token $token
   * @param  RecoveryForm $form
   * @return ResetPasswordEvent
   * @throws \yii\base\InvalidConfigException
   */
  protected function getResetPasswordEvent($token, $form = null) {
    return \Yii::createObject(['class' => ResetPasswordEvent::className(), 'token' => $token, 'form' => $form]);
  }
}
