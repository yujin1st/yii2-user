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
use yujin1st\users\widgets\Connect;

/**
 * @var yii\web\View $this
 * @var yujin1st\users\models\LoginForm $model
 * @var yujin1st\users\Module $module
 */

$this->title = Yii::t('users', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('users')]) ?>

<div class="row">
  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
      </div>
      <div class="panel-body">
        <?php $form = ActiveForm::begin([
          'id' => 'login-form',
          'enableAjaxValidation' => true,
          'enableClientValidation' => false,
          'validateOnBlur' => false,
          'validateOnType' => false,
          'validateOnChange' => false,
        ]) ?>

        <?= $form->field($model, 'login', ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]) ?>

        <?= $form->field($model, 'password', ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2']])->passwordInput()->label(Yii::t('users', 'Password') . ($module->enablePasswordRecovery ? ' (' . Html::a(Yii::t('users', 'Forgot password?'), ['/users/recovery/request'], ['tabindex' => '5']) . ')' : '')) ?>

        <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '4']) ?>

        <?= Html::submitButton(Yii::t('users', 'Sign in'), ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']) ?>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
    <?php if ($module->enableConfirmation): ?>
      <p class="text-center">
        <?= Html::a(Yii::t('users', 'Didn\'t receive confirmation message?'), ['/users/registration/resend']) ?>
      </p>
    <?php endif ?>
    <?php if ($module->enableRegistration): ?>
      <p class="text-center">
        <?= Html::a(Yii::t('users', 'Don\'t have an account? Sign up!'), ['/users/registration/register']) ?>
      </p>
    <?php endif ?>
    <?= Connect::widget([
      'baseAuthUrl' => ['/users/security/auth'],
    ]) ?>
  </div>
</div>
