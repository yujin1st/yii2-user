<?php

/**
 * This file is part of the yujin1st project
 *
 * (c) yujin1st project <http://github.com/yujin1st>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */

namespace yujin1st\user\clients;

use yii\authclient\clients\Facebook as BaseFacebook;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Facebook extends BaseFacebook implements ClientInterface
{
  /** @inheritdoc */
  public function getEmail() {
    return isset($this->getUserAttributes()['email'])
      ? $this->getUserAttributes()['email']
      : null;
  }

  /** @inheritdoc */
  public function getUsername() {
    return;
  }
}
