<?php

/*
 * This file is part of the yujin1st project.
 *
 * (c) yujin1st project <http://github.com/yujin1st/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yujin1st\users\events;

use yii\base\Event;
use yujin1st\users\models\RecoveryForm;
use yujin1st\users\models\Token;

/**
 * @property Token $token
 * @property RecoveryForm $form
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ResetPasswordEvent extends Event
{
  /**
   * @var RecoveryForm
   */
  private $_form;

  /**
   * @var Token
   */
  private $_token;

  /**
   * @return Token
   */
  public function getToken() {
    return $this->_token;
  }

  /**
   * @param Token $token
   */
  public function setToken(Token $token) {
    $this->_token = $token;
  }

  /**
   * @return RecoveryForm
   */
  public function getForm() {
    return $this->_form;
  }

  /**
   * @param RecoveryForm $form
   */
  public function setForm(RecoveryForm $form = null) {
    $this->_form = $form;
  }
}
