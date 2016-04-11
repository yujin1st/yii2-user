<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user\models\query;

use yii\authclient\ClientInterface;
use yii\db\ActiveQuery;
use yujin1st\user\models\Account;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AccountQuery extends ActiveQuery
{
  /**
   * Finds an account by code.
   *
   * @param  string $code
   * @return AccountQuery
   */
  public function byCode($code) {
    return $this->andWhere(['code' => md5($code)]);
  }

  /**
   * Finds an account by id.
   *
   * @param  integer $id
   * @return AccountQuery
   */
  public function byId($id) {
    return $this->andWhere(['id' => $id]);
  }

  /**
   * Finds an account by userId.
   *
   * @param  integer $userId
   * @return AccountQuery
   */
  public function byUser($userId) {
    return $this->andWhere(['userId' => $userId]);
  }

  /**
   * Finds an account by client.
   *
   * @param  ClientInterface $client
   * @return AccountQuery
   */
  public function byClient(ClientInterface $client) {
    return $this->andWhere([
      'provider' => $client->getId(),
      'clientId' => $client->getUserAttributes()['id'],
    ]);
  }


  /**
   * @inheritdoc
   * @return Account|array
   */
  public function all($db = null) {
    return parent::all($db);
  }

  /**
   * @inheritdoc
   * @return Account|array|null
   */
  public function one($db = null) {
    return parent::one($db);
  }
}
