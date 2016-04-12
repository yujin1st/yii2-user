<?php

namespace yujin1st\user\rbac;

use yii;

/**
 * List of rbac rules and roles
 *
 * @package rbac
 */
class Access extends yii\base\Component implements AccessInterface
{

  const ROLE_USER = 'user';
  const ROLE_ADMIN = 'admin';

  /**
   * Roles descriptions
   *
   * @return array
   */
  public function descriptions() {
    return [];
  }

  /**
   * Roles titles
   *
   * @return array
   */
  public function rolesTitles() {
    return [];
  }

  /**
   * Roles permissions
   *
   * @return mixed
   */
  public function rolesPermissions() {
    return [
      self::ROLE_USER => [
      ],
      self::ROLE_ADMIN => [
      ],
    ];
  }

  /**
   * Default admin roles
   *
   * @return array
   */
  public function adminRoles() {
    return [
      self::ROLE_USER,
      self::ROLE_ADMIN,
    ];
  }

  /**
   * Groups and section for managing roles on site
   *
   * @return array
   */
  public function roleGroups() {
    return [];
  }
}

 
