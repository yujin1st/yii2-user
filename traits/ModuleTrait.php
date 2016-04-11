<?php


namespace yujin1st\user\traits;

use yujin1st\user\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package yujin1st\user\traits
 */
trait ModuleTrait
{
  /**
   * @return Module
   */
  public function getModule() {
    return \Yii::$app->getModule('user');
  }
}
