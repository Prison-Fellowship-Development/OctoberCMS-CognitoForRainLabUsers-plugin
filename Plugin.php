<?php namespace Albrightlabs\RainlabUserCognito;

use App;
use RainLab\User\Models\User;
use System\Classes\PluginBase;
use AlbrightLabs\RainlabUserCognito\Providers\CognitoAuthServiceProvider;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    public $require = [
        'RainLab.User',
    ];

    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name' => 'RainLab User Cognito',
            'description' => 'Extends RainLab User for AWS Cognito support.',
            'author' => 'Albright Labs LLC',
            'icon' => 'icon-leaf'
        ];
    }

    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        /**
         * Register auth provider for AWS Cognito
         */
        App::register(CognitoAuthServiceProvider::class);
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
        User::extend(function (User $model) {
            $model->addFillable(['is_cognito_user', 'is_activated', 'password', 'password_confirmation']);
        });
    }

    /**
     * registerComponents used by the frontend.
     */
    public function registerComponents()
    {
        return [
            'Albrightlabs\RainlabUserCognito\Components\Login'                => 'Login',
            'Albrightlabs\RainlabUserCognito\Components\ResetPassword'        => 'ResetPassword',
            'Albrightlabs\RainlabUserCognito\Components\RequestResetPassword' => 'RequestResetPassword',
        ];
    }
}
