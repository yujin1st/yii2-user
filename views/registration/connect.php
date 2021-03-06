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
 * @var yii\widgets\ActiveForm $form
 * @var yujin1st\users\models\User $model
 * @var yujin1st\users\models\Account $account
 */

$this->title = Yii::t('users', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
      </div>
      <div class="panel-body">
        <div class="alert alert-info">
          <p>
            <?= Yii::t('users', 'In order to finish your registration, we need you to enter following fields') ?>:
          </p>
        </div>
        <?php $form = ActiveForm::begin([
          'id' => 'connect-account-form',
        ]); ?>

        <?= $form->field($model, 'email') ?>

        <?= $form->field($model, 'username') ?>

        <?= Html::submitButton(Yii::t('users', 'Continue'), ['class' => 'btn btn-success btn-block']) ?>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
    <p class="text-center">
      <?= Html::a(Yii::t('users', 'If you already registered, sign in and connect this account on settings page'), ['/users/settings/networks']) ?>.
    </p>
  </div>
</div>
