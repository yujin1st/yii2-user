<?php
/**
 * @link http://yujin1st.ru
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */

/* @var $this yii\web\View */

/* @var $role \yii\rbac\Role */
/* @var $form yujin1st\user\models\RoleForm */

$this->title = ($role->description ?: $role->name) . ': Редактирование';
$this->params['breadcrumbs'][] = ['label' => 'Роли', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $role->description ?: $role->name, 'url' => ['view', 'id' => $role->name]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="user-view">

  <?= $this->render('_form', [
    'model' => $form,
  ]) ?>

</div>
