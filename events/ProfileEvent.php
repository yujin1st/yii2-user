<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\events;

use yii\base\Event;
use yujin1st\users\models\Profile;

/**
 * @property Profile $model
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ProfileEvent extends Event
{
  /**
   * @var Profile
   */
  private $_profile;

  /**
   * @return Profile
   */
  public function getProfile() {
    return $this->_profile;
  }

  /**
   * @param Profile $form
   */
  public function setProfile(Profile $form) {
    $this->_profile = $form;
  }
}
