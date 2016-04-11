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

use yii\db\ActiveQuery;
use yujin1st\user\models\User;

/**
 *
 * @author yujin1st
 */
class UserQuery extends ActiveQuery
{
  /**
   * @inheritdoc
   * @return User|array
   */
  public function all($db = null) {
    return parent::all($db);
  }

  /**
   * @inheritdoc
   * @return User|array|null
   */
  public function one($db = null) {
    return parent::one($db);
  }

  /**
   * Finds a user by the given id.
   *
   * @param int $id User id to be used on search.
   *
   * @return $this
   */
  public function byId($id) {
    return $this->andWhere(['id' => $id]);
  }

  /**
   * Finds a user by the given username.
   *
   * @param string $username Username to be used on search.
   *
   * @return $this
   */
  public function byUsername($username) {
    return $this->andWhere(['username' => $username]);
  }

  /**
   * Finds a user by the given email.
   *
   * @param string $email Email to be used on search.
   *
   * @return $this
   */
  public function byEmail($email) {
    return $this->andWhere(['email' => $email]);
  }

  /**
   * Finds a user by the given email.
   *
   * @param string $usernameOrEmail Email to be used on search.
   *
   * @return $this
   */
  public function byUsernameOrEmail($usernameOrEmail) {
    if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
      return $this->byEmail($usernameOrEmail);
    }

    return $this->byUsername($usernameOrEmail);
  }


}
