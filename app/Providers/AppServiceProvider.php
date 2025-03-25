<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
 
    public function register()
    {
        //
    }
   
    public function boot()
    {
        Paginator::useBootstrap();

        if(Schema::hasTable('categories'))
        {
            $categories = Category::withCount('posts')->orderBy('posts_count', 'DESC')->take(10)->get();
            View::Share('nabbar_categories',$categories);

            $categories = Category::withCount('posts')->orderBy('posts_count', 'DESC')->take(10)->get();
            View::Share('setting',$categories);

            $setting = Setting::find(1);
            View::Share('setting',$setting);

        }

    }
}
