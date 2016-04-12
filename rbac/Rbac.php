<?php

namespace yujin1st\user\rbac;

use yii;
use yujin1st\user\events\RbacEvent;

/**
 *
 * Class for managing app rbac rules
 *
 * @package rbac
 */
class Rbac extends yii\base\Component
{
  /** global event for collecting app rbac rules */
  const EVENT_COLLECT_ROLES = 'user.collectRoles';

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
    foreach ($rolesPermissions as $roleName => $permissions) {
      $role = $auth->getRole($roleName);
      if (!$role) {
        $role = $auth->createRole($roleName);
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
    $event = new RbacEvent();
    Yii::$app->trigger(self::EVENT_COLLECT_ROLES, $event);

    if ($event->classes) foreach ($event->classes as $item) {
      if (is_string($item)) {
        $this->initRolesForClass(new $item);
      } elseif ($item instanceof AccessInterface) {
        $this->initRolesForClass($item);
      }
    }

  }

}
