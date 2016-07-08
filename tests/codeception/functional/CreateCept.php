<?php

use tests\codeception\_pages\CreatePage;
use tests\codeception\_pages\LoginPage;
use yujin1st\users\tests\FunctionalTester;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that user creation works');

$loginPage = LoginPage::openBy($I);
$user = $I->getFixture('user')->getModel('user');
$loginPage->login($user->email, 'qwerty');

$page = CreatePage::openBy($I);

$I->amGoingTo('try to create user with empty fields');
$page->create('', '', '');
$I->expectTo('see validations errors');
$I->see('Username cannot be blank.');
$I->see('Email cannot be blank.');

$page->create('toster', 'toster@example.com', 'toster');
$I->see('User has been created');

Yii::$app->user->logout();
LoginPage::openBy($I)->login('toster@example.com', 'toster');
$I->see('Logout');
