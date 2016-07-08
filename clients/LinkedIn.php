<?php

/*
 * This file is part of the yujin1st project
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace yujin1st\users\clients;

use yii\authclient\clients\LinkedIn as BaseLinkedIn;

/**
 * @author Sam Mousa <sam@mousa.nl>
 */
class LinkedIn extends BaseLinkedIn implements ClientInterface
{
  /** @inheritdoc */
  public function getEmail() {
    return isset($this->getUserAttributes()['email-address'])
      ? $this->getUserAttributes()['email-address']
      : null;
  }

  /** @inheritdoc */
  public function getUsername() {
    return;
  }
}
