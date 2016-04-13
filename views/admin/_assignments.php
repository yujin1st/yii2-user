<?php

/*
 * This file is part of the yujin1st project
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


/**
 * @var yii\web\View $this
 * @var yujin1st\user\models\User $user
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>

<?php $this->beginContent('@yujin1st/user/views/admin/update.php', ['user' => $user]) ?>

<?= yii\bootstrap\Alert::widget([
  'options' => [
    'class' => 'alert-info',
  ],
  'body' => Yii::t('user', 'You can assign multiple roles or permissions to user by using the form below'),
]) ?>


<?php $form = ActiveForm::begin([
  'layout' => 'horizontal',
  'enableAjaxValidation' => true,
  'enableClientValidation' => false,
  'fieldConfig' => [
    'horizontalCssClasses' => [
      'wrapper' => 'col-sm-9',
    ],
  ],
]); ?>

<?= $form->field($user, 'roles')->checkboxList(\yii\helpers\ArrayHelper::map(
  Yii::$app->authManager->getRoles(), 'name', function ($model
) {
  /** @var \yii\rbac\Role $model */
  return $model->description ?: $model->name;
})) ?>

<div class="form-group">
  <div class="col-lg-offset-3 col-lg-9">
    <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
  </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
