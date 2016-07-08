<?php

namespace yujin1st\users\controllers;

use yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yujin1st\users\models\RoleForm;
use yujin1st\users\rbac\Access;

/**
 * Role management
 *
 * @package yujin1st\users\controllers
 */
class RoleController extends Controller
{


  /**
   * @return array
   */
  public function behaviors() {
    return [
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'delete' => ['post'],
        ],
      ],
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          [
            'actions' => ['index'],
            'allow' => true,
            'roles' => [Access::USER_VIEW],
          ],
          [
            'allow' => true,
            'roles' => [Access::USER_UPDATE],
          ],
        ],
      ]
    ];
  }

  /**
   * Role list
   *
   * @return string
   */
  public function actionIndex() {
    $roles = Yii::$app->authManager->getRoles();
    $dataProvider = new ArrayDataProvider(['allModels' => $roles]);
    return $this->render('index', [
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * View role
   *
   * @param $name
   * @return string
   * @throws NotFoundHttpException
   */
  public function actionView($id) {
    $role = Yii::$app->authManager->getRole($id);
    if (!$role) throw new NotFoundHttpException('Запрошенная страница не существует');
    $model = new RoleForm();
    $model->auth = Yii::$app->authManager;

    return $this->render('view', [
      'role' => $role,
      'model' => $model,
    ]);
  }

  /**
   * Create role
   *
   * @return string
   */
  public function actionCreate() {
    $form = new RoleForm();
    $form->isNewRecord = true;
    $form->auth = Yii::$app->authManager;

    if ($form->load(Yii::$app->request->post()) && $form->save()) {
      Yii::$app->session->setFlash('success', 'Роль создана');
      return $this->redirect(['view', 'id' => $form->name]);
    } else {
      return $this->render('create', [
        'form' => $form,
      ]);
    }


  }

  /**
   * Role delete
   *
   * @param $id
   * @return \yii\web\Response
   */
  public function actionDelete($id) {
    Yii::$app->authManager->getRole($id);
    //if (Yii::$app->authManager->remove($id)) {
    //  Yii::$app->session->setFlash('success', 'Роль удалена');
    //}

    Yii::$app->session->setFlash('success', 'Удаление ролей не возможно');

    return $this->redirect(['index']);
  }

  /**
   * Role update
   *
   * @param $id
   * @return string
   * @throws NotFoundHttpException
   */
  public function actionUpdate($id) {
    $role = Yii::$app->authManager->getRole($id);
    if (!$role) throw new NotFoundHttpException('Запрошенная страница не существует');

    $form = new RoleForm();
    $form->auth = Yii::$app->authManager;
    $form->isNewRecord = false;
    $form->role = $role;

    if ($form->load(Yii::$app->request->post()) && $form->save()) {
      Yii::$app->session->setFlash('success', 'Роль отредактирована');
      return $this->redirect(['view', 'id' => $role->name]);
    } else {
      return $this->render('update', [
        'role' => $role,
        'form' => $form,
      ]);
    }
  }

}
