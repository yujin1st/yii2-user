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
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yujin1st\user\models\LoginForm $model
 * @var string $action
 */

?>

<?php if (Yii::$app->user->isGuest): ?>
  <?php $form = ActiveForm::begin([
    'id' => 'login-widget-form',
    'action' => Url::to(['/user/security/login']),
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnBlur' => false,
    'validateOnType' => false,
    'validateOnChange' => false,
  ]) ?>

  <?= $form->field($model, 'login')->textInput(['placeholder' => 'Login']) ?>

  <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>

  <?= $form->field($model, 'rememberMe')->checkbox() ?>

  <?= Html::submitButton(Yii::t('user', 'Sign in'), ['class' => 'btn btn-primary btn-block']) ?>

  <?php ActiveForm::end(); ?>
<?php else: ?>
  <?= Html::a(Yii::t('user', 'Logout'), ['/user/security/logout'], [
    'class' => 'btn btn-danger btn-block',
    'data-method' => 'post'
  ]) ?>
<?php endif ?>
