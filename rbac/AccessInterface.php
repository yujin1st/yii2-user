<?php

namespace yujin1st\user\rbac;

use yii;

/**
 * Base interface for class with roles and rules descriptions 
 *
 * @package rbac
 */
interface AccessInterface
{

  /**
   * Roles descriptions
   *
   *  return [
   *    self::ACTION => 'action description',
   *  ];
   *
   * @return array
   */
  public function descriptions();

  /**
   * Roles titles
   *
   * return [
   *   self::ACTION => 'action title',
   * ];*
   *
   * @return array
   */
  public function rolesTitles();

  /**
   * Roles Permissions
   *
   * return [
   *   self::ROLE => [
   *     self::ACTION,
   *   ],
   * ];
   *
   * @return mixed
   */
  public function rolesPermissions();


  /**
   * Default admin roles
   *
   * return [
   *   self::ROLE_USER,
   *   self::ROLE_ADMIN,
   * ];
   *
   * @return array
   */
  public function adminRoles();

  /**
   * Groups and section for managing roles on site
   *
   * @return array
   */
  public function roleGroups();

}
