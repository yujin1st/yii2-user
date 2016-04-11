<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\db\Schema;
use yujin1st\user\migrations\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class m140403_174025_create_account_table extends Migration
{
  public function up() {
    $this->createTable('{{%social_account}}', [
      'id' => $this->primaryKey(),
      'userId' => $this->integer(),
      'provider' => $this->string()->notNull(),
      'clientId' => $this->string()->notNull(),
      'data' => Schema::TYPE_TEXT,

      'code' => $this->string(32),
      'createTime' => $this->integer(),
      'email' => $this->string(),
      'username' => $this->string(),
    ], $this->tableOptions);


    $this->createIndex('account_unique_code', '{{%social_account}}', 'code', true);
    $this->createIndex('account_unique', '{{%social_account}}', ['provider', 'clientId'], true);
    $this->addForeignKey('fk_user_account', '{{%social_account}}', 'userId', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
  }

  public function down() {
    $this->dropTable('{{%social_account}}');
  }
}
