<?php namespace Albrightlabs\RainlabUserCognito;

use App;
use Event;
use Flash;
use RainLab\User\Models\User;
use ValidationException;
use Illuminate\Http\Request;
use System\Classes\PluginBase;
use BlackBits\LaravelCognitoAuth\CognitoClient;
use AlbrightLabs\RainlabUserCognito\Providers\CognitoAuthServiceProvider;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
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
