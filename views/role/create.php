<?php
/* @var $this yii\web\View */

/* @var $form \yujin1st\user\models\RoleForm */

$this->title = 'Новая роль';
$this->params['breadcrumbs'][] = ['label' => 'Роли', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">

  <?= $this->render('_form', [
    'model' => $form,
  ]) ?>

</div>
