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
 * @var yujin1st\user\Module $module
 */
?>

<?php if ($module->enableFlashMessages): ?>
  <div class="row">
    <div class="col-xs-12">
      <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>
          <div class="alert alert-<?= $type ?>">
            <?= $message ?>
          </div>
        <?php endif ?>
      <?php endforeach ?>
    </div>
  </div>
<?php endif ?>
