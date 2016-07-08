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
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\Url;
use yujin1st\users\clients\ClientInterface;
use yujin1st\users\models\query\AccountQuery;
use yujin1st\users\traits\ModuleTrait;

/**
 * @property integer $id          Id
 * @property integer $userId     User id, null if account is not bind to user
 * @property string $provider    Name of service
 * @property string $clientId   Account id
 * @property string $data        Account properties returned by social network (json encoded)
 * @property string $decodedData Json-decoded properties
 * @property string $code
 * @property integer $createTime
 * @property string $email
 * @property string $username
 *
 * @property User $user        User that this account is connected for.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Account extends ActiveRecord
{
  use ModuleTrait;

  /** @var */
  private $_data;

  /** @inheritdoc */
  public static function tableName() {
    return '{{%social_account}}';
  }

  /**
   * @inheritdoc
   * @return AccountQuery the active query used by this AR class.
   */
  public static function find() {
    return new AccountQuery(get_called_class());
  }

  /**
   * @return User
   */
  public function getUser() {
    return $this->hasOne($this->module->modelMap['User'], ['id' => 'userId']);
  }

  /**
   * @return bool Whether this social account is connected to user.
   */
  public function getIsConnected() {
    return $this->userId != null;
  }

  /**
   * @return mixed Json decoded properties.
   */
  public function getDecodedData() {
    if ($this->_data == null) {
      $this->_data = Json::decode($this->data);
    }

    return $this->_data;
  }

  /**
   * Returns connect url.
   *
   * @return string
   */
  public function getConnectUrl() {
    $code = \Yii::$app->security->generateRandomString();
    $this->updateAttributes(['code' => md5($code)]);

    return Url::to(['/user/registration/connect', 'code' => $code]);
  }

  /**
   * @param User $user
   * @return int
   */
  public function connect(User $user) {
    return $this->updateAttributes([
      'username' => null,
      'email' => null,
      'code' => null,
      'userId' => $user->id,
    ]);
  }

  /**
   * @param BaseClientInterface $client
   * @return Account
   * @throws \yii\base\InvalidConfigException
   */
  public static function create(BaseClientInterface $client) {
    /** @var Account $account */
    $account = Yii::createObject([
      'class' => Account::className(),
      'provider' => $client->getId(),
      'clientId' => $client->getUserAttributes()['id'],
      'data' => Json::encode($client->getUserAttributes()),
    ]);

    if ($client instanceof ClientInterface) {
      $account->setAttributes([
        'username' => $client->getUsername(),
        'email' => $client->getEmail(),
      ], false);
    }

    if (($user = static::fetchUser($account)) instanceof User) {
      $account->userId = $user->id;
    }

    $account->save(false);

    return $account;
  }

  /**
   * Tries to find an account and then connect that account with current user.
   *
   * @param BaseClientInterface $client
   */
  public static function connectWithUser(BaseClientInterface $client) {
    if (\Yii::$app->user->isGuest) {
      \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Something went wrong'));
      return;
    }

    $account = static::fetchAccount($client);

    if ($account->user === null) {
      /** @noinspection PhpParamsInspection */
      $account->link('user', \Yii::$app->user->identity);
      \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account has been connected'));
    } else {
      \Yii::$app->session->setFlash('danger', \Yii::t('user', 'This account has already been connected to another user'));
    }
  }

  /**
   * Tries to find account, otherwise creates new account.
   *
   * @param BaseClientInterface $client
   *
   * @return Account
   * @throws \yii\base\InvalidConfigException
   */
  protected static function fetchAccount(BaseClientInterface $client) {
    $account = self::find()->byClient($client)->one();

    if (null === $account) {
      /** @var Account $account */
      $account = Yii::createObject([
        'class' => Account::className(),
        'provider' => $client->getId(),
        'clientId' => $client->getUserAttributes()['id'],
        'data' => Json::encode($client->getUserAttributes()),
      ]);
      $account->save(false);
    }

    return $account;
  }

  /**
   * Tries to find user or create a new one.
   *
   * @param Account $account
   *
   * @return User|bool False when can't create user.
   */
  protected static function fetchUser(Account $account) {
    $user = User::findUserByEmail($account->email);

    if (null !== $user) {
      return $user;
    }
    $user = Yii::createObject([
      'class' => User::className(),
      'scenario' => User::SCENARIO_CONNECT,
      'username' => $account->username,
      'email' => $account->email,
    ]);

    if (!$user->validate(['email'])) {
      $account->email = null;
    }

    if (!$user->validate(['username'])) {
      $account->username = null;
    }

    return $user->create() ? $user : false;
  }

}
