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

?>

<?php $this->beginContent('@yujin1st/user/views/admin/update.php', ['user' => $user]) ?>

<?= yii\bootstrap\Alert::widget([
  'options' => [
    'class' => 'alert-info',
  ],
  'body' => Yii::t('user', 'You can assign multiple roles or permissions to user by using the form below'),
]) ?>

<?php $this->endContent() ?>
