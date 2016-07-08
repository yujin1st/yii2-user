<?php
/**
 * @link http://yujin1st.ru
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */

use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $role \yii\rbac\Role */
/* @var $model yujin1st\users\models\RoleForm */

$this->title = $role->description ?: $role->name;
$this->params['breadcrumbs'][] = ['label' => 'Роли', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-view">
  <p>
    <?= Html::a('Редактировать', ['update', 'id' => $role->name], ['class' => 'btn btn-primary']) ?>
    <? //= Html::a(Icon::show('remove', [], Icon::FA) . 'Удалить', ['delete', 'id' => $role->name], [
    //  'class' => 'btn btn-danger',
    //  'data' => [
    //    'confirm' => 'Вы действительно хотите удалить роль?',
    //    'method' => 'post',
    //  ],
    //]) ?>
  </p>

  <?= DetailView::widget([
    'model' => $role,
    'attributes' => [

      ['attribute' => 'name', 'label' => 'Название'],
      ['attribute' => 'description', 'label' => 'Описание'],
    ],
  ]) ?>

  <legend>Права доступа</legend>

  <?php
  $actions = [];
  $permissions = Yii::$app->authManager->getPermissionsByRole($role->name);
  foreach ($permissions as $permission) {
    $actions[] = $permission->name;
  }
  $list = [];
  foreach ($model->groups as $group) {
    $items = [];
    foreach ($group['actions'] as $action) {
      if (in_array($action, $actions)) $items[] = Html::tag('li', $model->descriptions[$action]);
    }
    $items = Html::tag('ul', implode('', $items));
    $list[] = ['label' => $group['label'], 'value' => $items, 'format' => 'raw'];
  }
  ?>


  <?= DetailView::widget([
    'model' => $role,
    'attributes' => $list,
  ]) ?>


</div>
