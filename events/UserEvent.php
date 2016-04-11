<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user\events;

use yii\base\Event;
use yujin1st\user\models\User;

/**
 * @property User $model
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserEvent extends Event
{
  /**
   * @var User
   */
  private $_user;

  /**
   * @return User
   */
  public function getUser() {
    return $this->_user;
  }

  /**
   * @param User $form
   */
  public function setUser(User $form) {
    $this->_user = $form;
  }
}
