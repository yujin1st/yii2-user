<?php

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\UpdatePage;
use yujin1st\users\tests\FunctionalTester;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that user update works');

$loginPage = LoginPage::openBy($I);
$user = $I->getFixture('user')->getModel('user');
$loginPage->login($user->email, 'qwerty');

$page = UpdatePage::openBy($I, ['id' => $user->id]);

$page->update('user', 'updated_user@example.com', 'new_pass');
$I->see('Account details have been updated');

Yii::$app->user->logout();
LoginPage::openBy($I)->login('updated_user@example.com', 'new_pass');
$I->see('Logout');
