<?php
namespace yujin1st\user\commands;

use yii;
use yii\console\Controller;
use yii\helpers\Console;
use yujin1st\user\rbac\Access;

/**
 * Setup rbac rules
 *
 * @package yujin1st\user\commands
 */
class RbacController extends Controller
{
  public $defaultAction = 'init';

  /**
   * Запуск всего процесса
   */
  public function actionInit() {
    // Создание ролей и действий
    Access::initRolesAndActions();
    $this->stdout(Yii::t('user', 'Roles updated') . "\n", Console::FG_GREEN);

  }

}
