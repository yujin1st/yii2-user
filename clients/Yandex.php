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

use yii;
use yii\authclient\clients\YandexOAuth as BaseYandex;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Yandex extends BaseYandex implements ClientInterface
{
  /** @inheritdoc */
  public function getEmail() {
    $emails = isset($this->getUserAttributes()['emails'])
      ? $this->getUserAttributes()['emails']
      : null;

    if ($emails !== null && isset($emails[0])) {
      return $emails[0];
    } else {
      return null;
    }
  }

  /** @inheritdoc */
  public function getUsername() {
    return isset($this->getUserAttributes()['login'])
      ? $this->getUserAttributes()['login']
      : null;
  }

  /** @inheritdoc */
  protected function defaultTitle() {
    return Yii::t('user', 'Yandex');
  }
}
