<?php

/*
 * This file is part of the yujin1st project
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace yujin1st\user\clients;

use yii\authclient\clients\GitHub as BaseGitHub;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class GitHub extends BaseGitHub implements ClientInterface
{
  /** @inheritdoc */
  public function getEmail() {
    return isset($this->getUserAttributes()['email'])
      ? $this->getUserAttributes()['email']
      : null;
  }

  /** @inheritdoc */
  public function getUsername() {
    return isset($this->getUserAttributes()['login'])
      ? $this->getUserAttributes()['login']
      : null;
  }
}
