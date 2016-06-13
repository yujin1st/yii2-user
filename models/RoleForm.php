<?php
/**
 * @link http://yujin1st.ru
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 */

namespace yujin1st\user\models;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\rbac\Role;
use yujin1st\user\rbac\Rbac;

/**
 * Role form
 *
 * @property \yii\rbac\Role role
 * @property array groups
 * @property array descriptions
 */
class RoleForm extends Model
{
  /** @var  string */
  public $oldName;
  /** @var  string */
  public $name;
  /** @var  string */
  public $description;
  /** @var  Role */
  public $_role;
  /** @var  array */
  public $actions = [];
  /** @var  boolean */
  public $isNewRecord;
  /** @var  \yii\rbac\ManagerInterface */
  public $auth;
  /** @var  array */
  public $groups = [];
  /** @var  array */
  public $descriptions = [];

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      // username and password are both required
      [['name'], 'required'],
      [['name'], 'string', 'max' => 64],
      [['actions'], 'safe'],
      [['description'], 'string'],
    ];
  }

  /**
   *
   * @return array
   */
  public function attributeLabels() {
    return [
      'name' => 'Имя (на латинице!)',
      'description' => 'Описание',
    ];
  }

  /**
   * @param bool $validate
   * @return bool
   */
  public function save($validate = true) {
    if ($validate && !$this->validate()) return false;

    if ($this->isNewRecord) {
      $this->role = $this->auth->createRole($this->name);
      $this->role->description = $this->description;
      if (!$this->auth->add($this->role)) return false;
    } else {
      $this->role->name = $this->name;
      $this->role->description = $this->description;
      if (!$this->auth->update($this->oldName, $this->role)) return false;
      $this->auth->removeChildren($this->role);
    }

    foreach ($this->actions as $action) {
      $this->auth->addChild($this->role, $this->auth->getPermission($action));
    }

    return true;

  }

  /**
   * @return Role
   */
  public function getRole() {
    return $this->_role;
  }

  /**
   * @param Role $role
   */
  public function setRole($role) {
    $this->_role = $role;
    $this->name = $role->name;
    $this->oldName = $role->name;
    $this->description = $role->description;

    if (!$this->isNewRecord) {
      $permissions = $this->auth->getPermissionsByRole($role->name);
      foreach ($permissions as $permission) {
        $this->actions[] = $permission->name;
      }
    }
  }

  public function init() {
    $this->loadRoles();
  }

  /**
   *
   */
  private function loadRoles() {
    /** @var Rbac $rbac */
    $rbac = Yii::createObject(['class' => Rbac::className()]);
    $classes = $rbac->loadClasses();
    foreach ($classes as $class) {
      $this->groups = ArrayHelper::merge($this->groups, $class->roleGroups());
      $this->descriptions = ArrayHelper::merge($this->descriptions, $class->descriptions());
    }

  }

}
