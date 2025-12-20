<?php

namespace App\Providers;

use App\Models\Menuoperations;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
   /**
    * Register any application services.
    *
    * @return void
    */
   public function register()
   {
      //
   }

   /**
    * Bootstrap any application services.
    *
    * @return void
    */
   public function boot(Request $request)
   {
      Paginator::useBootstrap();
      $requestMethod = $request->method();
      $currentUrl = $request->path();
      $pathSegments = explode('/', ltrim($currentUrl, "/"));
      $current_page =  $pathSegments[0];
      $layout_menu_exist = false;
      if (Schema::hasTable('menuoperations')) {
         $layout_menu_exist = Menuoperations::where('url_path', $current_page)->value('menu_path');
      } else {
         $layout_menu_exist = false;
      }
      if (trim(request()->path()) == '/' || $current_page == 'home') {
         view()->share('path', 'Dashboard');
      } else {
         view()->share('path', $current_page);
      }
      view()->share('layout_menu_exist', $layout_menu_exist);
      view()->share('requestMethod', $requestMethod);


      View::composer('*', function ($view) {
         if (Auth::check()) {
            $userId = Auth::id();

            $notifications = Notifications::where('user_id', $userId)
               ->orderBy('created_at', 'desc')
               ->take(10)
               ->get();

            $view->with('G_notifications', $notifications);
         } else {
            $view->with('G_notifications', collect()); // empty collection if not logged in
         }
      });

   }
}
