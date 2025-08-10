<?php namespace TData\News;

use Backend;
use Event;
use System\Classes\PluginBase;
use Illuminate\Http\Request;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/4.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'News',
            'description' => 'API + админка новостей',
            'author'      => 'TData',
            'icon'        => 'icon-newspaper-o',
        ];
    }

    public function boot()
    {
        /*
         * Отключаем CSRF-проверку для всех маршрутов, начинающихся с "api/"
         * Это позволяет SPA спокойно слать POST/PUT/DELETE без токена.
         */
        Event::listen('cms.route.beforeCheck', function($uri) {
            if (strpos($uri, 'api/') === 0) {
                Request::instance()->setSkipCsrfToken(true);
            }
        });
    }

    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        //
    }

  
    /**
     * registerComponents used by the frontend.
     */
    // public function registerComponents()
    // {
    //     return []; // Remove this line to activate

    //     return [
    //         'TData\News\Components\MyComponent' => 'myComponent',
    //     ];
    // }

    
    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return [
            'tdata.news.manage' => [
                'tab' => 'News',
                'label' => 'Управление новостями'
            ],
        ];
    }

    /**
     * registerNavigation used by the backend.
     */
   public function registerNavigation()
    {
        return [
            'news' => [
                'label'       => 'Новости',
                'url'         => Backend::url('tdata/news/news'),
                'icon'        => 'icon-newspaper-o',
                'permissions' => ['tdata.news.*'],
                'order'       => 500,
            ],
        ];
    }
}
