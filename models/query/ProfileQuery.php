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
use yujin1st\user\models\Profile;

/**
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */
class ProfileQuery extends ActiveQuery
{

  /**
   * Finds an profile by id.
   *
   * @param  integer $id
   * @return ProfileQuery
   */
  public function byId($id) {
    return $this->andWhere(['userId' => $id]);
  }


  /**
   * @inheritdoc
   * @return Profile|array
   */
  public function all($db = null) {
    return parent::all($db);
  }

  /**
   * @inheritdoc
   * @return Profile|array|null
   */
  public function one($db = null) {
    return parent::one($db);
  }
}
