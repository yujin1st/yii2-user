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
 * @var yii\web\View $this
 * @var yujin1st\users\Module $module
 */

$this->title = $title;

?>

<?= $this->render('/_alert', [
  'module' => $module,
]) ?>
