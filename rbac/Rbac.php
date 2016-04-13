<?php

namespace yujin1st\user\rbac;

use yii;
use yujin1st\user\events\RbacEvent;
use yujin1st\user\models\User;
use yujin1st\user\Module;

/**
 *
 * Class for managing app rbac rules
 *
 * @package rbac
 */
class Rbac extends yii\base\Component
{

  /**
   * init roles for extension class
   *
   * @param $access AccessInterface
   */
  private function initRolesForClass($access) {
    $auth = Yii::$app->authManager;
    $descriptions = $access->descriptions();
    foreach ($descriptions as $permissionName => $description) {
      $permission = $auth->getPermission($permissionName);
      if (!$permission) {
        $permission = $auth->createPermission($permissionName);
        $permission->description = $descriptions[$permissionName];
        $auth->add($permission);
      }
    }

    $rolesPermissions = $access->rolesPermissions();
    $rolesTitles = $access->rolesTitles();
    foreach ($rolesPermissions as $roleName => $permissions) {
      $role = $auth->getRole($roleName);
      if (!$role) {
        $role = $auth->createRole($roleName);
        $role->description = $rolesTitles[$roleName];
        $auth->add($role);
      }

      $auth->removeChildren($role);

      foreach ($permissions as $permissionName) {
        $permission = $auth->getPermission($permissionName);
        $auth->addChild($role, $permission);
      }
    }

  }

  /**
   * Collecting and initialising roles over all app
   */
  public function initRolesAndActions() {
    $classes = $this->loadClasses();
    foreach ($classes as $item) {
      $this->initRolesForClass($item);
    }
  }

  /**
   * @return AccessInterface[]
   */
  public function loadClasses() {
    $event = new RbacEvent();
    Yii::$app->trigger(Module::EVENT_COLLECT_ROLES, $event);
    return $event->classes;
  }


  /**
   * Set admin roles to user
   *
   * @param $user User
   */
  public function setAdminRole($user) {
    $classes = $this->loadClasses();

    $auth = Yii::$app->authManager;
    $auth->revokeAll($user->id);

    foreach ($classes as $item) {
      foreach ($item->adminRoles() as $role) {
        $auth->assign($auth->getRole($role), $user->id);
      }
    }

  }
}
