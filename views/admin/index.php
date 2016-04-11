<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\Pjax;
use yujin1st\user\models\search\UserSearch;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', [
  'module' => Yii::$app->getModule('user'),
]) ?>

<?= $this->render('/admin/_menu') ?>

<?php Pjax::begin() ?>

<?= GridView::widget([
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'layout' => "{items}\n{pager}",
  'columns' => [
    'username',
    'email:email',
    [
      'attribute' => 'registrationIp',
      'value' => function ($model) {
        return $model->registrationIp == null
          ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
          : $model->registrationIp;
      },
      'format' => 'html',
    ],
    [
      'attribute' => 'createTime',
      'value' => function ($model) {
        if (extension_loaded('intl')) {
          return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->createTime]);
        } else {
          return date('Y-m-d G:i:s', $model->createTime);
        }
      },
      'filter' => DatePicker::widget([
        'model' => $searchModel,
        'attribute' => 'createTime',
        'dateFormat' => 'php:Y-m-d',
        'options' => [
          'class' => 'form-control',
        ],
      ]),
    ],
    [
      'header' => Yii::t('user', 'Confirmation'),
      'value' => function ($model) {
        if ($model->isConfirmed) {
          return '<div class="text-center"><span class="text-success">' . Yii::t('user', 'Confirmed') . '</span></div>';
        } else {
          return Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
            'class' => 'btn btn-xs btn-success btn-block',
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
          ]);
        }
      },
      'format' => 'raw',
      'visible' => Yii::$app->getModule('user')->enableConfirmation,
    ],
    [
      'header' => Yii::t('user', 'Block status'),
      'value' => function ($model) {
        if ($model->isBlocked) {
          return Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $model->id], [
            'class' => 'btn btn-xs btn-success btn-block',
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
          ]);
        } else {
          return Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
            'class' => 'btn btn-xs btn-danger btn-block',
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
          ]);
        }
      },
      'format' => 'raw',
    ],
    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{update} {delete}',
    ],
  ],
]); ?>

<?php Pjax::end() ?>
