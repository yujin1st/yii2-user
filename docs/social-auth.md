# Authentication via social networks

Yii2-user provides user registration and login using social sites credentials. It
also allows to connect multiple social networks to user account and use them to
log in.

## Getting started

To get started you should configure `authClientCollection` application component:

```php
...
'components' => [
    ...
    'authClientCollection' => [
        'class'   => \yii\authclient\Collection::className(),
        'clients' => [
            // here is the list of clients you want to use
            // you can read more in the "Available clients" section
        ],
    ],
    ...
],
...
```

## Available clients

Here is the list of clients supported by the module:

- [Facebook](#facebook)
- [Twitter](#twitter)
- [Google](#google)
- [Github](#github)
- [VKontakte](#vkontakte)
- [Yandex](#yandex)

### Facebook

- You can register new application and get secret keys [here](https://developers.facebook.com/apps).

```php
'facebook' => [
    'class'        => 'yujin1st\user\clients\Facebook',
    'clientId'     => 'APP_ID',
    'clientSecret' => 'APP_SECRET',
],
```

### Twitter

- You can register new application and get secret keys [here](https://dev.twitter.com/apps/new).

> NOTE: Current version of Twitter API does not provide user's email address, so we can't register user without making him enter his email address

```php
'twitter' => [
    'class'          => 'yujin1st\user\clients\Twitter',
    'consumerKey'    => 'CONSUMER_KEY',
    'consumerSecret' => 'CONSUMER_SECRET',
],
```

### Google

- You can register new application and get secret keys [here](https://console.developers.google.com/project).
- First of all you need to enable Google+ API in `APIs & auth` section.
- Then you need to create new client id on `APIs & auth > Credentials` section
- `Authorized JavaScript origins` should contain url like `http://localhost`
- `Authorized redirect URIs` should contain url like `http://localhost/user/security/auth?authclient=google`

```php
'google' => [
    'class'        => 'yujin1st\user\clients\Google',
    'clientId'     => 'clientId',
    'clientSecret' => 'CLIENT_SECRET',
],
```

### Github

- You can register new application and get secret keys [here](https://github.com/settings/applications/new).

```php
'github' => [
    'class'        => 'yujin1st\user\clients\GitHub',
    'clientId'     => 'clientId',
    'clientSecret' => 'CLIENT_SECRET',
],
```

### VKontakte

- You can register new application and get secret keys [here](http://vk.com/editapp?act=create).

```php
'vkontakte' => [
    'class'        => 'yujin1st\user\clients\VKontakte',
    'clientId'     => 'clientId',
    'clientSecret' => 'CLIENT_SECRET',
]
```

### Yandex

- You can register new application and get secret keys [here](https://oauth.yandex.com/client/new).
- Make sure that you have enabled access to email address in `Yandex.Passport API` section.
- Also you should set the callback url to url like `http://localhost/user/security/auth?authclient=yandex`.

```php
'yandex' => [
    'class'        => 'yujin1st\user\clients\Yandex',
    'clientId'     => 'clientId',
    'clientSecret' => 'CLIENT_SECRET'
],
```

## Configuration example

The following config allows to log in using 3 networks (Twitter, Facebook and Google):

```php
'authClientCollection' => [
    'class' => yii\authclient\Collection::className(),
    'clients' => [
        'facebook' => [
            'class'        => 'yujin1st\user\clients\Facebook',
            'clientId'     => 'clientId',
            'clientSecret' => 'CLIENT_SECRET',
        ],
        'twitter' => [
            'class'          => 'yujin1st\user\clients\Twitter',
            'consumerKey'    => 'CONSUMER_KEY',
            'consumerSecret' => 'CONSUMER_SECRET',
        ],
        'google' => [
            'class'        => 'yujin1st\user\clients\Google',
            'clientId'     => 'clientId',
            'clientSecret' => 'CLIENT_SECRET',
        ],
    ],
],
```

## How to access social network attributes

You can fill user's profile with data provided by social network, here is quick example of how to fill profile name
with the name provided via facebook:

```php
// plase this code somewhere in your config files (bootstrap.php in case of using advanced app template, web.php in case
// of using basic app template

use yujin1st\user\controllers\SecurityController; 

Event::on(SecurityController::class, SecurityController::EVENT_AFTER_AUTHENTICATE, function (AuthEvent $e) {
    // if user account was not created we should not continue
    if ($e->account->user === null) {
        return;
    }

    // we are using switch here, because all networks provide different sets of data
    switch ($e->client->getName()) {
        case 'facebook':
            $e->account->user->profile->updateAttributes([
                'name' => $e->client->getUserAttributes()['name'],
            ]);
        case 'vkontakte':
            // some different logic
    }
    
    // after saving all user attributes will be stored under account model
    // Yii::$app->identity->user->accounts['facebook']->decodedData
});
```
