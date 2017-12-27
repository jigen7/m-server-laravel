<?php namespace App\Modules;
class ModuleServiceProvider extends  \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $modules = config("module.modules");
        while (list(,$module) = each($modules)) {
            if(file_exists(__DIR__.'/'.$module.'/routes.php')) {
                include __DIR__.'/'.$module.'/routes.php';
            }
            if(is_dir(__DIR__.'/'.$module.'/Views')) {
                $this->loadViewsFrom(__DIR__.'/'.$module.'/Views', $module);
            }

            /* if(is_dir(__DIR__.'/'.$module.'/Helpers')) {
                 foreach (glob(__DIR__.'/'.$module.'/Helpers/*.php') as $filename) { 
                   require_once($filename); 
            } } */
        }
    }

    public function register(){}

}