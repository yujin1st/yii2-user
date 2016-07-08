<?php

if (Yii::$app->user->getIsGuest()) {
  echo \yii\helpers\Html::a('Login', ['/users/security/login']);
  echo \yii\helpers\Html::a('Registration', ['/users/registration/register']);
} else {
  echo \yii\helpers\Html::a('Logout', ['/users/security/logout']);
}

echo $content;
