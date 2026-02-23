<?php namespace Albrightlabs\RainlabUserCognito;

use App;
use Schema;
use RainLab\User\Models\User;
use System\Classes\PluginBase;
use Albrightlabs\RainlabUserCognito\Classes\CognitoClient;
use Albrightlabs\RainlabUserCognito\Classes\CognitoGuard;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Foundation\Application;

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
     *
     * Register the cognito auth driver directly here (not via service provider)
     * to ensure it's registered AFTER RainLab.User replaces the auth singleton.
     */
    public function register()
    {
        // Register CognitoClient singleton
        $this->app->singleton(CognitoClient::class, function (Application $app) {
            $config = [
                'credentials' => config('cognito.credentials'),
                'region'      => config('cognito.region'),
                'version'     => config('cognito.version')
            ];

            return new CognitoClient(
                new CognitoIdentityProviderClient($config),
                config('cognito.app_client_id'),
                config('cognito.app_client_secret'),
                config('cognito.user_pool_id')
            );
        });

        // Register cognito auth driver on the current auth manager
        // This runs after RainLab.User has replaced the auth singleton
        $this->app['auth']->extend('cognito', function (Application $app, $name, array $config) {
            $guard = new CognitoGuard(
                $name,
                $app->make(CognitoClient::class),
                $app['auth']->createUserProvider($config['provider']),
                $app['session.store'],
                $app['request']
            );

            $guard->setCookieJar($app['cookie']);
            $guard->setDispatcher($app['events']);
            $guard->setRequest($app->refresh('request', $guard, 'setRequest'));

            return $guard;
        });
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
        User::extend(function (User $model) {
            $fillable = ['is_cognito_user'];
            if (Schema::hasColumn($model->getTable(), 'is_activated')) {
                $fillable[] = 'is_activated';
            }
            $model->addFillable($fillable);
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
