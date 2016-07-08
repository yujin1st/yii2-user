<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\models\search;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yujin1st\users\models\User;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
  /** @var string */
  public $username;

  /** @var string */
  public $email;

  /** @var int */
  public $createTime;

  /** @var string */
  public $registrationIp;


  /** @inheritdoc */
  public function rules() {
    return [
      'fieldsSafe' => [['username', 'email', 'registrationIp', 'createTime'], 'safe'],
      'createdDefault' => ['createTime', 'default', 'value' => null],
    ];
  }

  /** @inheritdoc */
  public function attributeLabels() {
    return [
      'username' => Yii::t('user', 'Username'),
      'email' => Yii::t('user', 'Email'),
      'createTime' => Yii::t('user', 'Registration time'),
      'registrationIp' => Yii::t('user', 'Registration ip'),
    ];
  }

  /**
   * @param $params
   *
   * @return ActiveDataProvider
   */
  public function search($params) {
    $query = User::find();

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    if (!($this->load($params) && $this->validate())) {
      return $dataProvider;
    }

    if ($this->createTime !== null) {
      $date = strtotime($this->createTime);
      $query->andFilterWhere(['between', 'createTime', $date, $date + 3600 * 24]);
    }

    $query->andFilterWhere(['like', 'username', $this->username])
      ->andFilterWhere(['like', 'email', $this->email])
      ->andFilterWhere(['registrationIp' => $this->registrationIp]);

    return $dataProvider;
  }
}
