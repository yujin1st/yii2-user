<?php

namespace yujin1st\users\tests;

use Codeception\Specify;
use tests\codeception\_fixtures\UserFixture;
use yii;
use yii\codeception\TestCase;
use yujin1st\users\models\User;

/**
 * Test suite for User active record class.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserTest extends TestCase
{
  use Specify;

  /**
   * @var User
   */
  protected $user;

  /**
   * @inheritdoc
   */
  public function fixtures() {
    return [
      'user' => [
        'class' => UserFixture::className(),
        'dataFile' => '@tests/codeception/_fixtures/data/init_user.php',
      ],
    ];
  }

  public function testRegister() {
    $this->specify('user should be registered', function () {
      $user = new User(['scenario' => 'register']);
      $user->username = 'tester';
      $user->email = 'tester@example.com';
      $user->password = 'tester';
      verify($user->register())->true();
      verify($user->username)->equals('tester');
      verify($user->email)->equals('tester@example.com');
      verify(Yii::$app->getSecurity()->validatePassword('tester', $user->passwordHash))->true();
    });

    $this->specify('profile should be created after registration', function () {
      $user = new User(['scenario' => 'register']);
      $user->username = 'john_doe';
      $user->email = 'john_doe@example.com';
      $user->password = 'qwerty';
      verify($user->register())->true();
    });
  }

  public function testBlocking() {
    $this->specify('user can be blocked and unblocked', function () {
      $user = $this->getFixture('user')->getModel('user');
      $authKey = $user->authKey;
      verify('user is not blocked', $user->getIsBlocked())->false();
      $user->block();
      verify('user is blocked', $user->getIsBlocked())->true();
      verify('authKey has been changed', $user->authKey)->notEquals($authKey);
      $user->unblock();
      verify('user is unblocked', $user->getIsBlocked())->false();
    });
  }

  public function testenableConfirmation() {
    \Yii::$app->getModule('user')->enableConfirmation = true;

    $this->specify('should return correct user confirmation status', function () {
      $user = $this->getFixture('user')->getModel('user');
      verify('user is confirmed', $user->getIsConfirmed())->true();
      $user = $this->getFixture('user')->getModel('unconfirmed');
      verify('user is not confirmed', $user->getIsConfirmed())->false();
    });

    /*$this->specify('correct user confirmation url should be returned', function () {
        $user = User::findOne(1);
        verify('url is null for confirmed user', $user->getConfirmationUrl())->null();
        $user = User::findOne(2);
        $needle = \Yii::$app->getUrlManager()->createAbsoluteUrl(['/user/registration/confirm',
            'id' => $user->id,
            'token' => $user->confirmationToken
        ]);
        verify('url contains correct id and confirmation token for unconfirmed user', $user->getConfirmationUrl())->contains($needle);
    });

    $this->specify('confirmation token should become invalid after specified time', function () {
        \Yii::$app->getModule('user')->confirmWithin = $expirationTime = 86400;
        $user = new User([
            'confirmationToken' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
            'confirmationSentAt' => time()
        ]);
        verify($user->getIsConfirmationPeriodExpired())->false();
        $user = new User([
            'confirmationToken'   => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
            'confirmationSentAt' => time() - $expirationTime - 1
        ]);
        verify($user->getIsConfirmationPeriodExpired())->true();
    });

    $this->specify('user should be confirmed by updating confirmTime field', function () {
        $user = User::findOne(2);
        verify($user->confirmTime)->null();
        $user->confirm();
        verify($user->confirmTime)->notNull();
    });*/
  }

  /*    public function testEmailSettings()
      {
          $this->user = User::findOne(1);
          $this->user->scenario = 'update_email';
          $this->user->unconfirmedEmail = 'new_email@example.com';
          $this->user->current_password = 'qwerty';
          $this->user->updateEmail();
  
          $this->specify('email should be updated', function () {
              verify($this->user->email)->equals('new_email@example.com');
              verify($this->user->unconfirmedEmail)->null();
          });
  
          \Yii::$app->getModule('user')->enableConfirmation = true;
  
          $this->specify('confirmation message should be sent if enableConfirmation is enabled', function () {
              $this->user->unconfirmedEmail = 'another_email@example.com';
              $this->user->current_password = 'qwerty';
              $this->user->updateEmail();
              verify($this->user->email)->equals('new_email@example.com');
              verify($this->user->unconfirmedEmail)->equals('another_email@example.com');
          });
  
          $this->specify('email reconfirmation should be reset', function () {
              $this->user->resetEmailUpdate();
              verify($this->user->email)->equals('new_email@example.com');
              verify($this->user->unconfirmedEmail)->null();
              verify($this->user->confirmationSentAt)->null();
              verify($this->user->confirmationToken)->null();
          });
      }
  
      public function testRecoverable()
      {
          $this->user = User::findOne(1);
          $this->user->sendRecoveryMessage();
  
          $this->specify('correct user confirmation url should be returned', function () {
              $needle = \Yii::$app->getUrlManager()->createAbsoluteUrl(['/user/recovery/reset',
                  'id' => $this->user->id,
                  'token' => $this->user->recoveryToken
              ]);
              verify($this->user->getRecoveryUrl())->contains($needle);
          });
  
          $this->specify('confirmation token should become invalid after specified time', function () {
              \Yii::$app->getModule('user')->recoverWithin = $expirationTime = 86400;
              $user = new User([
                  'recoveryToken' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
                  'recoverySentAt' => time()
              ]);
              verify($user->getIsRecoveryPeriodExpired())->false();
              $user = new User([
                  'recoveryToken'   => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
                  'recoverySentAt' => time() - $expirationTime - 1
              ]);
              verify($user->getIsRecoveryPeriodExpired())->true();
          });
      }*/
}
