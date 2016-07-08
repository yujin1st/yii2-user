<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\Nav;
use yii\web\View;
use yujin1st\users\models\User;

/**
 * @var View $this
 * @var User $user
 * @var string $content
 */

$this->title = Yii::t('users', 'Update user account');
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
            ['label' => Yii::t('users', 'Account details'), 'url' => ['/users/admin/update', 'id' => $user->id]],
            ['label' => Yii::t('users', 'Profile details'), 'url' => ['/users/admin/update-profile', 'id' => $user->id]],
            ['label' => Yii::t('users', 'Information'), 'url' => ['/users/admin/info', 'id' => $user->id]],
            [
              'label' => Yii::t('users', 'Assignments'),
              'url' => ['/users/admin/assignments', 'id' => $user->id],
            ],
            '<hr>',
            [
              'label' => Yii::t('users', 'Confirm'),
              'url' => ['/users/admin/confirm', 'id' => $user->id],
              'visible' => !$user->isConfirmed,
              'linkOptions' => [
                'class' => 'text-success',
                'data-method' => 'post',
                'data-confirm' => Yii::t('users', 'Are you sure you want to confirm this user?'),
              ],
            ],
            [
              'label' => Yii::t('users', 'Block'),
              'url' => ['/users/admin/block', 'id' => $user->id],
              'visible' => !$user->isBlocked,
              'linkOptions' => [
                'class' => 'text-danger',
                'data-method' => 'post',
                'data-confirm' => Yii::t('users', 'Are you sure you want to block this user?'),
              ],
            ],
            [
              'label' => Yii::t('users', 'Unblock'),
              'url' => ['/users/admin/block', 'id' => $user->id],
              'visible' => $user->isBlocked,
              'linkOptions' => [
                'class' => 'text-success',
                'data-method' => 'post',
                'data-confirm' => Yii::t('users', 'Are you sure you want to unblock this user?'),
              ],
            ],
            [
              'label' => Yii::t('users', 'Delete'),
              'url' => ['/users/admin/delete', 'id' => $user->id],
              'linkOptions' => [
                'class' => 'text-danger',
                'data-method' => 'post',
                'data-confirm' => Yii::t('users', 'Are you sure you want to delete this user?'),
              ],
            ],
          ],
        ]) ?>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <div class="panel panel-default">
      <div class="panel-body">
        <?= $content ?>
      </div>
    </div>
  </div>
</div>
