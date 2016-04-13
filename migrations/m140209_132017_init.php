<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yujin1st\user\migrations\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class m140209_132017_init extends Migration
{
  public function up() {
    $this->createTable('{{%user}}', [
      'id' => $this->primaryKey(),
      'username' => $this->string(255),
      'email' => $this->string(255),
      'passwordHash' => $this->string(60)->notNull(),
      'authKey' => $this->string(32)->notNull(),
      'confirmTime' => $this->integer(),
      'unconfirmedEmail' => $this->string(),
      'blockedAt' => $this->integer(),
      'registrationIp' => $this->bigInteger(),
      'createTime' => $this->integer()->notNull(),
      'updateTime' => $this->integer()->notNull(),
      'flags' => $this->integer()->notNull()->defaultValue(0),
    ], $this->tableOptions);

    //$this->createIndex('user_unique_username', '{{%user}}', 'username', true);
    //$this->createIndex('user_unique_email', '{{%user}}', 'email', true);

    $this->createTable('{{%profile}}', [
      'userId' => $this->primaryKey(),
      'name' => $this->string(),
      'public_email' => $this->string(),
      'gravatar_email' => $this->string(),
      'gravatar_id' => $this->string(32),
      'location' => $this->string(),
      'website' => $this->string(),
      'bio' => $this->text(),
      'timezone' => $this->string()
    ], $this->tableOptions);

    $this->addForeignKey('fk_user_profile', '{{%profile}}', 'userId', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
  }

  public function down() {
    $this->dropTable('{{%profile}}');
    $this->dropTable('{{%user}}');
  }
}
