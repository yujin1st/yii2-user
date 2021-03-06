<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yujin1st\users\models\Profile $profile
 */

$this->title = Yii::t('users', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('users')]) ?>

<div class="row">
  <div class="col-md-3">
    <?= $this->render('_menu') ?>
  </div>
  <div class="col-md-9">
    <div class="panel panel-default">
      <div class="panel-heading">
        <?= Html::encode($this->title) ?>
      </div>
      <div class="panel-body">
        <?php $form = \yii\widgets\ActiveForm::begin([
          'id' => 'profile-form',
          'options' => ['class' => 'form-horizontal'],
          'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
          ],
          'enableAjaxValidation' => true,
          'enableClientValidation' => false,
          'validateOnBlur' => false,
        ]); ?>

        <?= $form->field($model, 'name') ?>

        <?= $form->field($model, 'public_email') ?>

        <?= $form->field($model, 'website') ?>

        <?= $form->field($model, 'location') ?>

        <?= $form->field($model, 'timezone')->dropDownList(\yii\helpers\ArrayHelper::map(\yujin1st\users\helpers\Timezone::getAll(), 'timezone', 'name')); ?>

        <?= $form->field($model, 'gravatar_email')->hint(\yii\helpers\Html::a(Yii::t('users', 'Change your avatar at Gravatar.com'), 'http://gravatar.com')) ?>

        <?= $form->field($model, 'bio')->textarea() ?>

        <div class="form-group">
          <div class="col-lg-offset-3 col-lg-9">
            <?= \yii\helpers\Html::submitButton(Yii::t('users', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
            <br>
          </div>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>
