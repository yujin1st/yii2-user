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
 * @var yii\web\View
 * @var yujin1st\users\models\User
 */
?>

<?php $this->beginContent('@yujin1st/users/views/admin/update.php', ['user' => $user]) ?>

<table class="table">
  <tr>
    <td><strong><?= Yii::t('users', 'Registration time') ?>:</strong></td>
    <td><?= Yii::t('users', '{0, date, MMMM dd, YYYY HH:mm}', [$user->createTime]) ?></td>
  </tr>
  <?php if ($user->registrationIp !== null): ?>
    <tr>
      <td><strong><?= Yii::t('users', 'Registration IP') ?>:</strong></td>
      <td><?= $user->registrationIp ?></td>
    </tr>
  <?php endif ?>
  <tr>
    <td><strong><?= Yii::t('users', 'Confirmation status') ?>:</strong></td>
    <?php if ($user->isConfirmed): ?>
      <td class="text-success"><?= Yii::t('users', 'Confirmed at {0, date, MMMM dd, YYYY HH:mm}', [$user->confirmTime]) ?></td>
    <?php else: ?>
      <td class="text-danger"><?= Yii::t('users', 'Unconfirmed') ?></td>
    <?php endif ?>
  </tr>
  <tr>
    <td><strong><?= Yii::t('users', 'Block status') ?>:</strong></td>
    <?php if ($user->isBlocked): ?>
      <td class="text-danger"><?= Yii::t('users', 'Blocked at {0, date, MMMM dd, YYYY HH:mm}', [$user->blockedAt]) ?></td>
    <?php else: ?>
      <td class="text-success"><?= Yii::t('users', 'Not blocked') ?></td>
    <?php endif ?>
  </tr>
</table>

<?php $this->endContent() ?>
