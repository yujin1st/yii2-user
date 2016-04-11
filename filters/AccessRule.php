<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\user\filters;

/**
 * Access rule class for simpler RBAC.
 *
 * @see http://yii2-user.dmeroff.ru/docs/custom-access-control
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AccessRule extends \yii\filters\AccessRule
{
  /**
   * @inheritdoc
   * */
  protected function matchRole($user) {
    if (empty($this->roles)) {
      return true;
    }

    foreach ($this->roles as $role) {
      if ($role === '?') {
        if (\Yii::$app->user->isGuest) {
          return true;
        }
      } elseif ($role === '@') {
        if (!\Yii::$app->user->isGuest) {
          return true;
        }
      } elseif ($role === 'admin') {
        if (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isAdmin) {
          return true;
        }
      }
    }

    return false;
  }
}