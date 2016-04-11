<?php

/*
 * This file is part of the yujin1st project
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace yujin1st\user\traits;

use yii;
use yii\base\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait AjaxValidationTrait
{
  /**
   * Performs ajax validation.
   *
   * @param Model $model
   *
   * @throws \yii\base\ExitException
   */
  protected function performAjaxValidation(Model $model) {
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      echo json_encode(ActiveForm::validate($model));
      Yii::$app->end();
    }
  }
}
