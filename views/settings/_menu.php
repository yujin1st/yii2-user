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
use yii\widgets\Menu;

/** @var yujin1st\users\models\User $user */
$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;
/** @var \yujin1st\users\Module $module */
$module = Yii::$app->getModule('users');
$items = \yii\helpers\ArrayHelper::merge([
  ['label' => Yii::t('users', 'Profile'), 'url' => ['/users/settings/profile']],
  ['label' => Yii::t('users', 'Account'), 'url' => ['/users/settings/account']],
  ['label' => Yii::t('users', 'Networks'), 'url' => ['/users/settings/networks'], 'visible' => $networksVisible],
], $module->getUserMenu());

?>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">
      <?= Html::img($user->profile->getAvatarUrl(24), [
        'class' => 'img-rounded',
        'alt' => $user->username,
      ]) ?>
      <?= $user->username ?>
    </h3>
  </div>
  <div class="panel-body">
    <?= Menu::widget([
      'encodeLabels' => false,
      'options' => [
        'class' => 'nav nav-pills nav-stacked',
      ],
      'items' => $items,
    ]) ?>
  </div>
</div>
