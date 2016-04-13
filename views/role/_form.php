<?php
/**
 * @link http://yujin1st.ru
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yujin1st\user\models\RoleForm;

/* @var $this yii\web\View */
/* @var $model RoleForm */
/* @var $form \yii\bootstrap\ActiveForm */
?>

<div class="user-form">

  <?php $form = \yii\bootstrap\ActiveForm::begin([
  ]); ?>

  <?= $form->errorSummary($model); ?>

  <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

  <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

  <legend>Права доступа</legend>

  <?php foreach ($model->groups as $group): ?>
    <?php
    $actions = [];
    foreach ($group['actions'] as $action) {
      $actions[$action] = $model->descriptions[$action];
    }
    ?>

    <?= $form->field($model, 'actions')->label($group['label'])->checkboxList($actions, ['unselect' => null]) ?>
  <?php endforeach; ?>


  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-success']) ?>
      <?= Html::a('Отмена', $model->isNewRecord ? ['index'] : ['view', 'id' => $model->name]) ?>
    </div>
  </div>


  <?php ActiveForm::end(); ?>

</div>
