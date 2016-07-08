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
      'label' => Yii::t('users', 'Users'),
      'url' => ['/users/admin/index'],
    ],
    [
      'label' => Yii::t('users', 'Roles'),
      'url' => ['/users/role/index'],
    ],
    [
      'label' => Yii::t('users', 'Create'),
      'items' => [
        [
          'label' => Yii::t('users', 'New user'),
          'url' => ['/users/admin/create'],
        ],
        [
          'label' => Yii::t('users', 'New role'),
          'url' => ['/users/role/create'],
        ],
      ],
    ],
  ],
]) ?>
