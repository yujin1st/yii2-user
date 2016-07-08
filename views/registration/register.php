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
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yujin1st\users\models\User $user
 * @var yujin1st\users\Module $module
 */

$this->title = Yii::t('users', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
      </div>
      <div class="panel-body">
        <?php $form = ActiveForm::begin([
          'id' => 'registration-form',
          'enableAjaxValidation' => true,
          'enableClientValidation' => false,
        ]); ?>

        <?= $form->field($model, 'email') ?>

        <?= $form->field($model, 'username') ?>

        <?php if ($module->enableGeneratingPassword == false): ?>
          <?= $form->field($model, 'password')->passwordInput() ?>
        <?php endif ?>

        <?= Html::submitButton(Yii::t('users', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
    <p class="text-center">
      <?= Html::a(Yii::t('users', 'Already registered? Sign in!'), ['/users/security/login']) ?>
    </p>
  </div>
</div>
