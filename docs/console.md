# Console commands

## Setup
To enable console commands, you need to add module into console config of you app.
`/config/console.php` in yii2-app-basic template, or `/console/config/main.php` in yii2-app-advanced.

```php

    return [
        'id' => 'app-console',
        'modules' => [
            'user' => [
                'class' => 'yujin1st\users\Module',
            ],
        ],
        ...

```

## Available console actions

- **users/confirm** Confirms a user.
- **users/create** Creates new user account.
- **users/delete** Deletes a user.
- **users/password** Updates user's password.

### users/confirm
Confirms a user by setting confirmTime field to current time.

```sh

./yii users/confirm <search> [...options...]

- search (required): string
  Email or username

```

### users/create
This command creates new user account. If password is not set, this command will generate new 8-char password.
After saving user to database, this command uses mailer component to send credentials (username and password) to
user via email.


```sh

./yii users/create <email> <username> [password] [...options...]

- email (required): string
  Email address

- username (required): string
  Username

- password: null|string
  Password (if null it will be generated automatically)

```

### users/delete
Deletes a user.

```sh

./yii users/delete <search> [...options...]

- search (required): string
  Email or username

```

### users/password
Updates user's password to given.

```sh

./yii users/password <search> <password> [...options...]

- search (required): string
  Email or username

- password (required): string
  New password


```
