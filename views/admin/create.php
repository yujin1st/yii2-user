<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yujin1st\users\models\User $user
 */

$this->title = Yii::t('users', 'Create a user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('users', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('/_alert', [
  'module' => Yii::$app->getModule('users'),
]) ?>

<?= $this->render('_menu') ?>

<div class="row">
  <div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-body">
        <?= Nav::widget([
          'options' => [
            'class' => 'nav-pills nav-stacked',
          ],
          'items' => [
            ['label' => Yii::t('users', 'Account details'), 'url' => ['/users/admin/create']],
            [
              'label' => Yii::t('users', 'Profile details'), 'options' => [
              'class' => 'disabled',
              'onclick' => 'return false;',
            ]
            ],
            [
              'label' => Yii::t('users', 'Information'), 'options' => [
              'class' => 'disabled',
              'onclick' => 'return false;',
            ]
            ],
          ],
        ]) ?>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="alert alert-info">
          <?= Yii::t('users', 'Credentials will be sent to the user by email') ?>.
          <?= Yii::t('users', 'A password will be generated automatically if not provided') ?>.
        </div>
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

        <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>

        <div class="form-group">
          <div class="col-lg-offset-3 col-lg-9">
            <?= Html::submitButton(Yii::t('users', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
          </div>
        </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>
