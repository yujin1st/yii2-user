<?php

/*
 * This file is part of the yujin1st project
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\Nav;

?>

<?= Nav::widget([
  'options' => [
    'class' => 'nav-tabs',
    'style' => 'margin-bottom: 15px',
  ],
  'items' => [
    [
      'label' => Yii::t('user', 'Users'),
      'url' => ['/user/admin/index'],
    ],
    [
      'label' => Yii::t('user', 'Roles'),
      'url' => ['/user/role/index'],
    ],
    [
      'label' => Yii::t('user', 'Create'),
      'items' => [
        [
          'label' => Yii::t('user', 'New user'),
          'url' => ['/user/admin/create'],
        ],
        [
          'label' => Yii::t('user', 'New role'),
          'url' => ['/user/role/create'],
        ],
      ],
    ],
  ],
]) ?>
