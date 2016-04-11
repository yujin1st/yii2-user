<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace yujin1st\user\widgets;

use yii\base\Widget;
use yujin1st\user\models\LoginForm;

/**
 * Login for widget.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Login extends Widget
{
  /**
   * @var bool
   */
  public $validate = true;

  /**
   * @inheritdoc
   */
  public function run() {
    return $this->render('login', [
      'model' => \Yii::createObject(LoginForm::className()),
    ]);
  }
}
