# List of available actions

Yii2-user includes a lot of actions, which you can access by creating URLs for them. Here is the table of available
actions which contains route and short description of each action. You can create URLs for them using special Yii
helper `\yii\helpers\Url::to()`.

- **/users/registration/register** Displays registration form
- **/users/registration/resend**   Displays resend form
- **/users/registration/confirm**  Confirms a user (requires *id* and *token* query params)
- **/users/security/login**        Displays login form
- **/users/security/logout**       Logs the user out (available only via POST method)
- **/users/recovery/request**      Displays recovery request form
- **/users/recovery/reset**        Displays password reset form (requires *id* and *token* query params)
- **/users/settings/profile**      Displays profile settings form
- **/users/settings/account**      Displays account settings form (email, username, password)
- **/users/settings/networks**     Displays social network accounts settings page
- **/users/profile/show**          Displays user's profile (requires *id* query param)
- **/users/admin/index**           Displays user management interface

## Example of menu

You can add links to registration, login and logout as follows:

```php
Yii::$app->user->isGuest ?
    ['label' => 'Sign in', 'url' => ['/users/security/login']] :
    ['label' => 'Sign out (' . Yii::$app->user->identity->username . ')',
        'url' => ['/users/security/logout'],
        'linkOptions' => ['data-method' => 'post']],
['label' => 'Register', 'url' => ['/users/registration/register'], 'visible' => Yii::$app->user->isGuest]
```
