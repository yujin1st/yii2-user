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

use yii\db\ActiveRecord;
use yujin1st\users\models\query\ProfileQuery;
use yujin1st\users\traits\ModuleTrait;

/**
 * This is the model class for table "profile".
 *
 * @property integer $userId
 * @property string $name
 * @property string $public_email
 * @property string $gravatar_email
 * @property string $gravatar_id
 * @property string $location
 * @property string $website
 * @property string $bio
 * @property string $timezone
 * @property User $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class Profile extends ActiveRecord
{
  use ModuleTrait;
  /** @var \yujin1st\users\Module */
  protected $module;

  /** @inheritdoc */
  public function init() {
    $this->module = \Yii::$app->getModule('users');
  }

  /**
   * Returns avatar url or null if avatar is not set.
   *
   * @param  int $size
   * @return string|null
   */
  public function getAvatarUrl($size = 200) {
    $protocol = \Yii::$app->request->isSecureConnection ? 'https' : 'http';

    return $protocol . '://gravatar.com/avatar/' . $this->gravatar_id . '?s=' . $size;
  }

  /**
   * @return \yii\db\ActiveQueryInterface
   */
  public function getUser() {
    return $this->hasOne($this->module->modelMap['User'], ['id' => 'userId']);
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      'bioString' => ['bio', 'string'],
      'timeZoneValidation' => ['timezone', 'validateTimeZone'],
      'publicEmailPattern' => ['public_email', 'email'],
      'gravatarEmailPattern' => ['gravatar_email', 'email'],
      'websiteUrl' => ['website', 'url'],
      'nameLength' => ['name', 'string', 'max' => 255],
      'publicEmailLength' => ['public_email', 'string', 'max' => 255],
      'gravatarEmailLength' => ['gravatar_email', 'string', 'max' => 255],
      'locationLength' => ['location', 'string', 'max' => 255],
      'websiteLength' => ['website', 'string', 'max' => 255],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
      'name' => \Yii::t('users', 'Name'),
      'public_email' => \Yii::t('users', 'Email (public)'),
      'gravatar_email' => \Yii::t('users', 'Gravatar email'),
      'location' => \Yii::t('users', 'Location'),
      'website' => \Yii::t('users', 'Website'),
      'bio' => \Yii::t('users', 'Bio'),
      'timezone' => \Yii::t('users', 'Time zone'),
    ];
  }

  /**
   * Validates the timezone attribute.
   * Adds an error when the specified time zone doesn't exist.
   *
   * @param string $attribute the attribute being validated
   */
  public function validateTimeZone($attribute) {
    if (!in_array($this->$attribute, timezone_identifiers_list())) {
      $this->addError($attribute, \Yii::t('users', 'Time zone is not valid'));
    }
  }

  /**
   * Get the user's time zone.
   * Defaults to the application timezone if not specified by the user.
   *
   * @return \DateTimeZone
   */
  public function getTimeZone() {
    try {
      return new \DateTimeZone($this->timezone);
    } catch (\Exception $e) {
      // Default to application time zone if the user hasn't set their time zone
      return new \DateTimeZone(\Yii::$app->timeZone);
    }
  }

  /**
   * Set the user's time zone.
   *
   * @param \DateTimeZone $timeZone the timezone to save to the user's profile
   */
  public function setTimeZone(\DateTimeZone $timeZone) {
    $this->setAttribute('timezone', $timeZone->getName());
  }

  /**
   * Converts DateTime to user's local time
   *
   * @param \DateTime the datetime to convert
   * @return \DateTime
   */
  public function toLocalTime(\DateTime $dateTime = null) {
    if ($dateTime === null) {
      $dateTime = new \DateTime();
    }

    return $dateTime->setTimezone($this->getTimeZone());
  }

  /**
   * @inheritdoc
   */
  public function beforeSave($insert) {
    if ($this->isAttributeChanged('gravatar_email')) {
      $this->setAttribute('gravatar_id', md5(strtolower(trim($this->getAttribute('gravatar_email')))));
    }

    return parent::beforeSave($insert);
  }

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return '{{%profile}}';
  }


  /**
   * @inheritdoc
   * @return ProfileQuery the active query used by this AR class.
   */
  public static function find() {
    return new ProfileQuery(get_called_class());
  }


}
