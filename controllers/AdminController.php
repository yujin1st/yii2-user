<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\controllers;

use yii;
use yii\base\ExitException;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yujin1st\users\models\Profile;
use yujin1st\users\models\search\UserSearch;
use yujin1st\users\models\User;
use yujin1st\users\Module;
use yujin1st\users\rbac\Access;
use yujin1st\users\traits\EventTrait;

/**
 * AdminController allows you to administrate users.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class AdminController extends Controller
{
  use EventTrait;

  /**
   * Event is triggered before creating new user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_CREATE = 'beforeCreate';

  /**
   * Event is triggered after creating new user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_CREATE = 'afterCreate';

  /**
   * Event is triggered before updating existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_UPDATE = 'beforeUpdate';

  /**
   * Event is triggered after updating existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_UPDATE = 'afterUpdate';

  /**
   * Event is triggered before updating existing user's profile.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';

  /**
   * Event is triggered after updating existing user's profile.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';

  /**
   * Event is triggered before updating existing user's roles.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_ROLES_UPDATE = 'beforeRolesUpdate';

  /**
   * Event is triggered after updating existing user's roles.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_ROLES_UPDATE = 'afterRolesUpdate';

  /**
   * Event is triggered before confirming existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

  /**
   * Event is triggered after confirming existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_CONFIRM = 'afterConfirm';

  /**
   * Event is triggered before deleting existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_DELETE = 'beforeDelete';

  /**
   * Event is triggered after deleting existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_DELETE = 'afterDelete';

  /**
   * Event is triggered before blocking existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_BLOCK = 'beforeBlock';

  /**
   * Event is triggered after blocking existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_BLOCK = 'afterBlock';

  /**
   * Event is triggered before unblocking existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_BEFORE_UNBLOCK = 'beforeUnblock';

  /**
   * Event is triggered after unblocking existing user.
   * Triggered with \yujin1st\users\events\UserEvent.
   */
  const EVENT_AFTER_UNBLOCK = 'afterUnblock';


  /** @inheritdoc */
  public function behaviors() {
    return [
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'delete' => ['post'],
          'confirm' => ['post'],
          'block' => ['post'],
        ],
      ],
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          [
            'allow' => true,
            'roles' => [Access::USER_VIEW],
          ],
        ],
      ],
    ];
  }

  /**
   * Lists all User models.
   *
   * @return mixed
   */
  public function actionIndex() {
    Url::remember('', 'actions-redirect');
    $searchModel = Yii::createObject([
      'class' => UserSearch::className(),
    ]);
    $dataProvider = $searchModel->search(Yii::$app->request->get());

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
    ]);
  }

  /**
   * Creates a new User model.
   * If creation is successful, the browser will be redirected to the 'index' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    /** @var User $user */
    $user = Yii::createObject([
      'class' => User::className(),
      'scenario' => User::SCENARIO_CREATE,
    ]);
    $user->scenario = User::SCENARIO_CREATE;
    $event = $this->getUserEvent($user);

    $this->performAjaxValidation($user);

    $this->trigger(self::EVENT_BEFORE_CREATE, $event);
    if ($user->load(Yii::$app->request->post()) && $user->create()) {
      Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));
      $this->trigger(self::EVENT_AFTER_CREATE, $event);
      return $this->redirect(['update', 'id' => $user->id]);
    }

    return $this->render('create', [
      'user' => $user,
    ]);
  }

  /**
   * Updates an existing User model.
   *
   * @param int $id
   *
   * @return mixed
   */
  public function actionUpdate($id) {
    Url::remember('', 'actions-redirect');
    $user = $this->findModel($id);
    $user->scenario = User::SCENARIO_UPDATE;
    $event = $this->getUserEvent($user);
    $this->performAjaxValidation($user);

    $this->trigger(self::EVENT_BEFORE_UPDATE, $event);
    if ($user->load(Yii::$app->request->post()) && $user->save()) {
      Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Account details have been updated'));
      $this->trigger(self::EVENT_AFTER_UPDATE, $event);
      return $this->refresh();
    }

    return $this->render('_account', [
      'user' => $user,
    ]);
  }

  /**
   * Updates an existing profile.
   *
   * @param int $id
   *
   * @return mixed
   */
  public function actionUpdateProfile($id) {
    Url::remember('', 'actions-redirect');
    $user = $this->findModel($id);
    $profile = $user->profile;

    if ($profile == null) {
      $profile = new Profile();
      $profile->link('user', $user);
    }
    $event = $this->getProfileEvent($profile);

    $this->performAjaxValidation($profile);

    $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);

    if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
      Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Profile details have been updated'));
      $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
      return $this->refresh();
    }

    return $this->render('_profile', [
      'user' => $user,
      'profile' => $profile,
    ]);
  }

  /**
   * Shows information about user.
   *
   * @param int $id
   *
   * @return string
   */
  public function actionInfo($id) {
    Url::remember('', 'actions-redirect');
    $user = $this->findModel($id);

    return $this->render('_info', [
      'user' => $user,
    ]);
  }

  /**
   * If "yujin1st/yii2-rbac" extension is installed, this page displays form
   * where user can assign multiple auth items to user.
   *
   * @param int $id
   *
   * @return string
   * @throws NotFoundHttpException
   */
  public function actionAssignments($id) {
    Url::remember('', 'actions-redirect');
    $user = $this->findModel($id);
    $user->scenario = User::SCENARIO_UPDATE_ROLES;

    $event = $this->getUserEvent($user);

    $this->performAjaxValidation($user);

    $this->trigger(self::EVENT_BEFORE_ROLES_UPDATE, $event);
    if ($user->load(Yii::$app->request->post()) && $user->save()) {
      Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Account roles have been updated'));
      $this->trigger(self::EVENT_BEFORE_ROLES_UPDATE, $event);
      return $this->refresh();
    }

    return $this->render('_assignments', [
      'user' => $user,
    ]);
  }

  /**
   * Confirms the User.
   *
   * @param int $id
   *
   * @return Response
   */
  public function actionConfirm($id) {
    $model = $this->findModel($id);
    $event = $this->getUserEvent($model);

    $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);
    $model->confirm();
    $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

    Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been confirmed'));

    return $this->redirect(Url::previous('actions-redirect'));
  }

  /**
   * Deletes an existing User model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param int $id
   *
   * @return mixed
   */
  public function actionDelete($id) {
    if ($id == Yii::$app->user->getId()) {
      Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not remove your own account'));
    } else {
      $model = $this->findModel($id);
      $event = $this->getUserEvent($model);
      $this->trigger(self::EVENT_BEFORE_DELETE, $event);
      $model->delete();
      $this->trigger(self::EVENT_AFTER_DELETE, $event);
      Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been deleted'));
    }

    return $this->redirect(['index']);
  }

  /**
   * Blocks the user.
   *
   * @param int $id
   *
   * @return Response
   */
  public function actionBlock($id) {
    if ($id == Yii::$app->user->getId()) {
      Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not block your own account'));
    } else {
      $user = $this->findModel($id);
      $event = $this->getUserEvent($user);
      if ($user->getIsBlocked()) {
        $this->trigger(self::EVENT_BEFORE_UNBLOCK, $event);
        $user->unblock();
        $this->trigger(self::EVENT_AFTER_UNBLOCK, $event);
        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been unblocked'));
      } else {
        $this->trigger(self::EVENT_BEFORE_BLOCK, $event);
        $user->block();
        $this->trigger(self::EVENT_AFTER_BLOCK, $event);
        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been blocked'));
      }
    }

    return $this->redirect(Url::previous('actions-redirect'));
  }

  /**
   * Finds the User model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return User the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    $user = User::findIdentity($id);
    if ($user === null) {
      throw new NotFoundHttpException('The requested page does not exist');
    }

    return $user;
  }

  /**
   * Performs AJAX validation.
   *
   * @param array|Model $model
   *
   * @throws ExitException
   */
  protected function performAjaxValidation($model) {
    if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
      if ($model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        echo json_encode(ActiveForm::validate($model));
        Yii::$app->end();
      }
    }
  }
}
