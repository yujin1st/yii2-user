<?php

/**
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace yujin1st\user\commands;

use yii;
use yii\console\Controller;
use yii\helpers\Console;
use yujin1st\user\models\User;
use yujin1st\user\rbac\Access;

/**
 * Confirms a user.
 *
 * @property \yujin1st\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class DefaultController extends Controller
{

  /**
   * Confirms a user by setting confirmTime field to current time.
   *
   * @param string $search Email or username
   */
  public function actionConfirm($search) {
    $user = User::findIdentityByUsernameOrEmail($search);
    if ($user === null) {
      $this->stdout(Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
    } else {
      if ($user->confirm()) {
        $this->stdout(Yii::t('user', 'User has been confirmed') . "\n", Console::FG_GREEN);
      } else {
        $this->stdout(Yii::t('user', 'Error occurred while confirming user') . "\n", Console::FG_RED);
      }
    }
  }

  /**
   * This command creates new user account. If password is not set, this command will generate new 8-char password.
   * After saving user to database, this command uses mailer component to send credentials (username and password) to
   * user via email.
   *
   * @param string $email Email address
   * @param string $username Username
   * @param null|string $password Password (if null it will be generated automatically)
   */
  public function actionCreate($email, $username, $password = null) {
    $user = new User ([
      'scenario' => 'create',
      'email' => $email,
      'username' => $username,
      'password' => $password,
    ]);

    if ($user->create()) {
      $this->stdout(Yii::t('user', 'User has been created') . "!\n", Console::FG_GREEN);
    } else {
      $this->stdout(Yii::t('user', 'Please fix following errors:') . "\n", Console::FG_RED);
      foreach ($user->errors as $errors) {
        foreach ($errors as $error) {
          $this->stdout(' - ' . $error . "\n", Console::FG_RED);
        }
      }
    }
  }

  /**
   * Deletes a user.
   *
   * @param string $search Email or username
   */
  public function actionDelete($search) {
    if ($this->confirm(Yii::t('user', 'Are you sure? Deleted user can not be restored'))) {
      $user = User::findIdentityByUsernameOrEmail($search);
      if ($user === null) {
        $this->stdout(Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
      } else {
        if ($user->delete()) {
          $this->stdout(Yii::t('user', 'User has been deleted') . "\n", Console::FG_GREEN);
        } else {
          $this->stdout(Yii::t('user', 'Error occurred while deleting user') . "\n", Console::FG_RED);
        }
      }
    }
  }

  /**
   * Updates user's password to given.
   *
   * @param string $search Email or username
   * @param string $password New password
   */
  public function actionPassword($search, $password) {
    $user = User::findIdentityByUsernameOrEmail($search);
    if ($user === null) {
      $this->stdout(Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
    } else {
      if ($user->resetPassword($password)) {
        $this->stdout(Yii::t('user', 'Password has been changed') . "\n", Console::FG_GREEN);
      } else {
        $this->stdout(Yii::t('user', 'Error occurred while changing password') . "\n", Console::FG_RED);
      }
    }
  }

  /**
   * Назначение пользователю прав администратора
   *
   * @param $search
   */
  public function actionAdmin($search) {
    $user = User::findIdentityByUsernameOrEmail($search);
    if ($user) {
      $auth = Yii::$app->authManager;
      $auth->revokeAll($user->id);
      foreach (Access::$adminRoles as $role) {
        $auth->assign($auth->getRole($role), $person->id);
      }
    } else {
      $this->stdout(Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
    }
  }

}
