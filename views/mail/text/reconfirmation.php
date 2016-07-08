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
 * @var yujin1st\users\models\Token $token
 */
?>
<?= Yii::t('users', 'Hello') ?>,

<?= Yii::t('users', 'We have received a request to change the email address for your account on {0}', Yii::$app->name) ?>.
<?= Yii::t('users', 'In order to complete your request, please click the link below') ?>.

<?= $token->url ?>

<?= Yii::t('users', 'If you cannot click the link, please try pasting the text into your browser') ?>.

<?= Yii::t('users', 'If you did not make this request you can ignore this email') ?>.
