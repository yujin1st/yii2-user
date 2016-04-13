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
  const ROLE_USER_MANAGER = 'user_manager';
  const ROLE_ADMIN = 'admin';


  const USER_VIEW = 'user_view';
  const USER_CREATE = 'user_create';
  const USER_UPDATE = 'user_update';
  const USER_CHANGE_SECURITY_INFO = 'user_change_security_info';
  const USER_CHANGE_PROFILE_INFO = 'user_change_profile_info';

  /**
   * Rules descriptions
   *
   * @return array
   */
  public function descriptions() {
    return [
      self::USER_VIEW => 'Просмотр пользователей',
      self::USER_CREATE => 'Добавление пользователей',
      self::USER_UPDATE => 'Редактирование пользователей',
      self::USER_CHANGE_SECURITY_INFO => 'Редактирование данных для входа',
      self::USER_CHANGE_PROFILE_INFO => 'Редактирование персональных данных',
    ];
  }

  /**
   * Roles titles
   *
   * @return array
   */
  public function rolesTitles() {
    return [
      self::ROLE_USER => 'Пользователь',
      self::ROLE_USER_MANAGER => 'Администратор пользователей',
      self::ROLE_ADMIN => 'Администратор',
    ];
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
      self::ROLE_USER_MANAGER => [
        self::USER_VIEW,
        self::USER_CREATE,
        self::USER_UPDATE,
        self::USER_CHANGE_SECURITY_INFO,
        self::USER_CHANGE_PROFILE_INFO,
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
      self::ROLE_USER_MANAGER
    ];
  }

  /**
   * Groups and section for managing roles on site
   *
   * @return array
   */
  public function roleGroups() {
    return [
      [
        'label' => 'Пользователи',
        'actions' => [
          self::USER_VIEW,
          self::USER_CREATE,
          self::USER_UPDATE,
          self::USER_CHANGE_SECURITY_INFO,
          self::USER_CHANGE_PROFILE_INFO,
        ],
      ]
    ];
  }
}

 
