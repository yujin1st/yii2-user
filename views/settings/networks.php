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
use yujin1st\users\widgets\Connect;

/*
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */

$this->title = Yii::t('users', 'Networks');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('users')]) ?>

<div class="row">
  <div class="col-md-3">
    <?= $this->render('_menu') ?>
  </div>
  <div class="col-md-9">
    <div class="panel panel-default">
      <div class="panel-heading">
        <?= Html::encode($this->title) ?>
      </div>
      <div class="panel-body">
        <div class="alert alert-info">
          <p><?= Yii::t('users', 'You can connect multiple accounts to be able to log in using them') ?>.</p>
        </div>
        <?php $auth = Connect::begin([
          'baseAuthUrl' => ['/users/security/auth'],
          'accounts' => $user->accounts,
          'autoRender' => false,
          'popupMode' => false,
        ]) ?>
        <table class="table">
          <?php foreach ($auth->getClients() as $client): ?>
            <tr>
              <td style="width: 32px; vertical-align: middle">
                <?= Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]) ?>
              </td>
              <td style="vertical-align: middle">
                <strong><?= $client->getTitle() ?></strong>
              </td>
              <td style="width: 120px">
                <?= $auth->isConnected($client) ?
                  Html::a(Yii::t('users', 'Disconnect'), $auth->createClientUrl($client), [
                    'class' => 'btn btn-danger btn-block',
                    'data-method' => 'post',
                  ]) :
                  Html::a(Yii::t('users', 'Connect'), $auth->createClientUrl($client), [
                    'class' => 'btn btn-success btn-block',
                  ])
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
        <?php Connect::end() ?>
      </div>
    </div>
  </div>
</div>
