<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\models\query;

use yii\db\ActiveQuery;
use yujin1st\users\models\Token;

/**
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */
class TokenQuery extends ActiveQuery
{

  /**
   * @inheritdoc
   * @return Token|array
   */
  public function all($db = null) {
    return parent::all($db);
  }

  /**
   * @inheritdoc
   * @return Token|array|null
   */
  public function one($db = null) {
    return parent::one($db);
  }
}
