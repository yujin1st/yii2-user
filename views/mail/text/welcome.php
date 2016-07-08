<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var yujin1st\users\models\User
 */
?>
<?= Yii::t('users', 'Hello') ?>,

<?= Yii::t('users', 'Your account on {0} has been created', Yii::$app->name) ?>.
<?php if ($module->enableGeneratingPassword): ?>
  <?= Yii::t('users', 'We have generated a password for you') ?>:
  <?= $user->password ?>
<?php endif ?>

<?php if ($token !== null): ?>
  <?= Yii::t('users', 'In order to complete your registration, please click the link below') ?>.

  <?= $token->url ?>

  <?= Yii::t('users', 'If you cannot click the link, please try pasting the text into your browser') ?>.
<?php endif ?>

<?= Yii::t('users', 'If you did not make this request you can ignore this email') ?>.
