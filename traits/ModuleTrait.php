<?php


namespace yujin1st\users\traits;

use yujin1st\users\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package yujin1st\users\traits
 */
trait ModuleTrait
{
  /**
   * @return Module
   */
  public function getModule() {
    return \Yii::$app->getModule('users');
  }
}
