<?php
namespace yujin1st\users\commands;

use yii;
use yii\console\Controller;
use yii\helpers\Console;
use yujin1st\users\rbac\Rbac;

/**
 * Setup rbac rules
 *
 * @package yujin1st\users\commands
 */
class RbacController extends Controller
{
  public $defaultAction = 'init';

  /**
   * Init all roles
   */
  public function actionInit() {
    $rbac = new Rbac();
    $rbac->initRolesAndActions();
    $this->stdout(Yii::t('user', 'Roles updated') . "\n", Console::FG_GREEN);
  }

}
