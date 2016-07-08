<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\models;

use yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yujin1st\users\models\query\TokenQuery;
use yujin1st\users\traits\ModuleTrait;

/**
 * Token Active Record model.
 *
 * @property integer $userId
 * @property string $code
 * @property integer $createTime
 * @property integer $type
 * @property string $url
 * @property bool $isExpired
 * @property User $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Token extends ActiveRecord
{
  use ModuleTrait;

  const TYPE_CONFIRMATION = 0;
  const TYPE_RECOVERY = 1;
  const TYPE_CONFIRM_NEW_EMAIL = 2;
  const TYPE_CONFIRM_OLD_EMAIL = 3;

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getUser() {
    return $this->hasOne($this->module->modelMap['User'], ['id' => 'userId']);
  }

  /**
   * @return string
   */
  public function getUrl() {
    switch ($this->type) {
      case self::TYPE_CONFIRMATION:
        $route = '/users/registration/confirm';
        break;
      case self::TYPE_RECOVERY:
        $route = '/users/recovery/reset';
        break;
      case self::TYPE_CONFIRM_NEW_EMAIL:
      case self::TYPE_CONFIRM_OLD_EMAIL:
        $route = '/users/settings/confirm';
        break;
      default:
        throw new \RuntimeException();
    }

    return Url::to([$route, 'id' => $this->userId, 'code' => $this->code], true);
  }

  /**
   * @return bool Whether token has expired.
   */
  public function getIsExpired() {
    switch ($this->type) {
      case self::TYPE_CONFIRMATION:
      case self::TYPE_CONFIRM_NEW_EMAIL:
      case self::TYPE_CONFIRM_OLD_EMAIL:
        $expirationTime = $this->module->confirmWithin;
        break;
      case self::TYPE_RECOVERY:
        $expirationTime = $this->module->recoverWithin;
        break;
      default:
        throw new \RuntimeException();
    }

    return ($this->createTime + $expirationTime) < time();
  }

  /** @inheritdoc */
  public function beforeSave($insert) {
    if ($insert) {
      static::deleteAll(['userId' => $this->userId, 'type' => $this->type]);
      $this->setAttribute('createTime', time());
      $this->setAttribute('code', Yii::$app->security->generateRandomString());
    }

    return parent::beforeSave($insert);
  }

  /** @inheritdoc */
  public static function tableName() {
    return '{{%token}}';
  }

  /** @inheritdoc */
  public static function primaryKey() {
    return ['userId', 'code', 'type'];
  }

  /**
   * @inheritdoc
   * @return TokenQuery the active query used by this AR class.
   */
  public static function find() {
    return new TokenQuery(get_called_class());
  }

}
