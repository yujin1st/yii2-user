<?php

namespace yujin1st\user\rbac;

use yii;

/**
 * Список всех возможных действий и их описание
 *
 * @package rbac
 */
class Access
{

  const ACTION = 'action';

  const ROLE_USER = 'user';
  const ROLE_ADMIN = 'admin';


  public static $descriptions = [
    self::ACTION => 'Действие',
  ];

  public static $rolesTitles = [
    self::ACTION => 'Действие',
  ];

  public static $rolesPermissions = [
    self::ROLE_USER => [
    ],
    self::ROLE_ADMIN => [
    ],
  ];

  /** @var  array права для администратора,см rbac/admin */
  public static $adminRoles = [
    self::ROLE_USER,
    self::ROLE_ADMIN,
  ];

  /** @var array группы ролей для раздела "управление ролями" */
  public static $roleGroups = [


    [
      'label' => 'Пользователи',
      'actions' => [
      ]
    ],

  ];

  /**
   * Создание ролей и действий
   */
  public static function initRolesAndActions() {
    $auth = Yii::$app->authManager;

    foreach (Access::$descriptions as $permissionName => $description) {
      $permission = $auth->getPermission($permissionName);
      if (!$permission) {
        $permission = $auth->createPermission($permissionName);
        $permission->description = Access::$descriptions[$permissionName];
        $auth->add($permission);
      }
    }

    foreach (Access::$rolesPermissions as $roleName => $permissions) {
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

}
