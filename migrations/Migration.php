<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\migrations;

use yii;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Migration extends \yii\db\Migration
{
  /**
   * @var string
   */
  protected $tableOptions;

  /**
   * @inheritdoc
   */
  public function init() {
    parent::init();

    switch (Yii::$app->db->driverName) {
      case 'mysql':
        $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        break;
      case 'pgsql':
        $this->tableOptions = null;
        break;
      default:
        throw new \RuntimeException('Your database is not supported!');
    }
  }
}
