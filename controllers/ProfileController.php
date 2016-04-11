<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user\controllers;

use yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yujin1st\user\models\Profile;

/**
 * ProfileController shows users profiles.
 *
 * @property \yujin1st\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ProfileController extends Controller
{


  /** @inheritdoc */
  public function behaviors() {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          ['allow' => true, 'actions' => ['index'], 'roles' => ['@']],
          ['allow' => true, 'actions' => ['show'], 'roles' => ['?', '@']],
        ],
      ],
    ];
  }

  /**
   * Redirects to current user's profile.
   *
   * @return \yii\web\Response
   */
  public function actionIndex() {
    return $this->redirect(['show', 'id' => Yii::$app->user->getId()]);
  }

  /**
   * Shows user's profile.
   *
   * @param int $id
   *
   * @return \yii\web\Response
   * @throws \yii\web\NotFoundHttpException
   */
  public function actionShow($id) {
    $profile = Profile::find()->byId($id)->one();

    if ($profile === null) {
      throw new NotFoundHttpException();
    }

    return $this->render('show', [
      'profile' => $profile,
    ]);
  }
}
