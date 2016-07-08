<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\helpers;

use yii\helpers\ArrayHelper;

/**
 * Password helper.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Timezone
{

  /**
   * Get all of the time zones with the offsets sorted by their offset
   *
   * @return array
   */
  public static function getAll() {
    $timeZones = [];
    $timeZoneIdentifiers = \DateTimeZone::listIdentifiers();

    foreach ($timeZoneIdentifiers as $timeZone) {
      $date = new \DateTime('now', new \DateTimeZone($timeZone));
      $offset = $date->getOffset() / 60 / 60;
      $timeZones[] = ['timezone' => $timeZone, 'name' => "{$timeZone} (UTC " . ($offset > 0 ? '+' : '') . "{$offset})", 'offset' => $offset];
    }

    ArrayHelper::multisort($timeZones, 'offset', SORT_DESC, SORT_NUMERIC);

    return $timeZones;
  }
}
