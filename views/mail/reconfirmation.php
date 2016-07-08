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

/**
 * @var yujin1st\users\models\Token $token
 */
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
  <?= Yii::t('users', 'Hello') ?>,
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
  <?= Yii::t('users', 'We have received a request to change the email address for your account on {0}', Yii::$app->name) ?>.
  <?= Yii::t('users', 'In order to complete your request, please click the link below') ?>.
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
  <?= Html::a(Html::encode($token->getUrl()), $token->getUrl()); ?>
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
  <?= Yii::t('users', 'If you cannot click the link, please try pasting the text into your browser') ?>.
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
  <?= Yii::t('users', 'If you did not make this request you can ignore this email') ?>.
</p>
