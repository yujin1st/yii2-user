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
use yujin1st\users\rbac\AccessInterface;

/**
 * @property array $classes
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */
class RbacEvent extends Event
{

  /**
   * @return string[]|AccessInterface[];
   */
  private $_classes = [];

  /**
   * @param $name string|AccessInterface
   */
  public function addClass($name) {
    if (is_string($name)) $name = new $name;
    $this->_classes[] = $name;
  }

  /**
   * @return AccessInterface[];
   */
  public function getClasses() {
    return $this->_classes;
  }

}
