<?php
/**
 * @link http://yujin1st.ru
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ArrayDataProvider */

$this->title = 'Роли';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/admin/_menu') ?>


<!--
<p>
  <?php if (Yii::$app->user->can(\yujin1st\user\rbac\Access::USER_UPDATE)): ?>
    <? //= Html::a('Новая роль', ['create'], ['class' => 'btn btn-success']) ?>
  <?php endif; ?>
</p>
-->
<?= GridView::widget([
  'dataProvider' => $dataProvider,
  'columns' => [
    ['class' => 'yii\grid\SerialColumn'],

    [
      'attribute' => 'name',
      'header' => 'Название',
      'format' => 'raw',
      'value' => function ($model) {
        /** @var \yii\rbac\Role $model */
        return Html::a($model->name, ['view', 'id' => $model->name]);
      },
    ],
    ['attribute' => 'description', 'header' => 'Описание'],

    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{view} {update}'
    ],
  ],
]); ?>
