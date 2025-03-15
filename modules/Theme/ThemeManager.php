<?php
namespace Modules\Theme;

use Illuminate\Support\Facades\File;
use Modules\Theme\Abstracts\AbstractThemeProvider;

class ThemeManager
{
    protected static $_all = [];

    public static function current(){
        return config('bc.active_theme','base');
    }

    /**
     * Get the provider class for the current active theme.
     *
     * This method retrieves the provider class for the currently active theme
     * by calling the getProviderClass method with the result of the current method.
     *
     * @return string The fully qualified class name of the current theme's provider.
     */
    public static function currentProvider(){
        return static::getProviderClass(static::current());
    }


    /**
     * Get the fully qualified class name of the theme provider for a given theme.
     *
     * This method constructs and returns the namespace path to the ThemeProvider class
     * for the specified theme.
     *
     * @param string $theme The name of the theme.
     * @return string The fully qualified class name of the theme provider.
     */
    public static function getProviderClass($theme){
        return "\\Themes\\".ucfirst($theme)."\\ThemeProvider";
    }


    public static function all(){
        if(empty(static::$_all)){
            static::loadAll();
        }
        return static::$_all;
    }

    protected static function loadAll(){
        $listThemes = array_map('basename', File::directories(base_path("themes")));
        foreach ($listThemes as $theme){
            if($theme == "Base") continue;
            $class = static::getProviderClass($theme);
            if(class_exists($class)){
                self::$_all[$theme] = $class;
            }
        }
    }
    /**
     * @param $theme
     * @return bool|AbstractThemeProvider
     */
    public static function theme($theme){
        $all = static::all();
        return $all[$theme] ?? false;
    }
}
