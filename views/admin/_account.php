<?php

/*
 * This file is part of the yujin1st project
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/*
 * @var yii\web\View $this
 * @var yujin1st\user\models\User $user
 */

?>

<?php $this->beginContent('@yujin1st/user/views/admin/update.php', ['user' => $user]) ?>

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
    <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
  </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
