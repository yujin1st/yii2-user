<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yujin1st\users\migrations\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m140504_130429_create_token_table extends Migration
{
  public function up() {
    $this->createTable('{{%token}}', [
      'userId' => $this->integer()->notNull(),
      'code' => $this->string(32)->notNull(),
      'createTime' => $this->integer()->notNull(),
      'type' => $this->integer()->notNull(),
    ], $this->tableOptions);

    $this->createIndex('token_unique', '{{%token}}', ['userId', 'code', 'type'], true);
    $this->addForeignKey('fk_user_token', '{{%token}}', 'userId', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
  }

  public function down() {
    $this->dropTable('{{%token}}');
  }
}
