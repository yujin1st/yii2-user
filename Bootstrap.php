<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users;

use yii\base\BootstrapInterface;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{

  /** @inheritdoc */
  public function bootstrap($app) {
  }
}
