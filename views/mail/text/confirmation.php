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
 * @var yujin1st\users\models\User $user
 * @var yujin1st\users\models\Token $token
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'Thank you for signing up on {0}', Yii::$app->name) ?>.
<?= Yii::t('user', 'In order to complete your registration, please click the link below') ?>.

<?= $token->url ?>

<?= Yii::t('user', 'If you cannot click the link, please try pasting the text into your browser') ?>.

<?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
