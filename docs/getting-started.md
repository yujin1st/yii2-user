# Getting started with Yii2-user

Yii2-user is designed to work out of the box. It means that installation requires
minimal steps. Only one configuration step should be taken and you are ready to
have user management on your Yii2 website.

> If you're using Yii2 advanced template, you should read [this article](usage-with-advanced-template.md) firstly.

### 1. Download

Yii2-user can be installed using composer. Run following command to download and
install Yii2-user:

```bash
composer require "yujin1st/yii2-user:0.9.*@dev"
```

### 2. Configure

> **NOTE:** Make sure that you don't have `user` component configuration in your config files.

Add following lines to your main configuration file:

```php
'modules' => [
    'user' => [
        'class' => 'yujin1st\user\Module',
    ],
],
```

For initialising rbac roles and permissions use event. yujin1st\user\Module::EVENT_COLLECT_ROLES
```php
   'on collectRoles' => function ($event) {
      /** @var $event \yujin1st\user\events\RbacEvent */
      $event->addClass(\rbac\Access::className());
    }
```

### 3. Update database schema and init roles

The last thing you need to do is updating your database schema by applying the
migrations. Make sure that you have properly configured `db` application component
and run the following commands:

Setup rbac migration: 
```bash
$ php yii migrate --migrationPath=@yii/rbac/migrations
```
Setup users module migration
```bash
$ php yii migrate --migrationPath=@vendor/yujin1st/yii2-user/migrations
```

Setup all rbac rules
```bash
$ php yii user/rbac/init
```





## Where do I go now?

You have Yii2-user installed. Now you can check out the [list of articles](README.md)
for more information.

## Troubleshooting

If you're having troubles with Yii2-user, make sure to check out the 
[troubleshooting guide](troubleshooting.md).
