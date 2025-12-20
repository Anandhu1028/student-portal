<?php

namespace App\Services;

use App\Models\AllowedUrls;
use App\Models\Menu;
use App\Models\MenuOrder;
use Illuminate\Support\Facades\Session;

class UserMenuService
{
   public function saveUserMenusInSession($user)
   {
      // 1. Get user modules (active)
      $userModules = $user->modules()->where('is_active', true)->pluck('modules.id')->toArray();

      if (empty($userModules)) {
         Session::put('menus', []);
         return;
      }

      // 2. Get permitted menu and submenu IDs from menu_permissions

      $menuPermissions = AllowedUrls::query()
         ->where(function ($q) use ($user) {
            $q->where('role_id', $user->role_id)
               ->where('allowed', true);
         })
         ->whereNotIn('url', function ($sub) use ($user) {
            $sub->select('url')
               ->from('allowed_urls')
               ->where('user_id', $user->id)
               ->where('allowed', false);
         })
         ->get();


      $permittedMenuIds = $menuPermissions->pluck('menu_id')->unique()->toArray();
      $permittedSubMenuIds = $menuPermissions->pluck('sub_menu_id')->filter()->unique()->toArray();

      if (empty($permittedMenuIds)) {
         Session::put('menus', []);
         return;
      }

      // 3. Load menus with menu titles and permitted submenus
      $menus = Menu::with(['menuTitle', 'subMenus' => function ($q) use ($permittedSubMenuIds) {
         $q->where('is_active', true)
            ->whereIn('id', $permittedSubMenuIds)
            ->orderBy('order');
      }])
         ->whereIn('id', $permittedMenuIds)
         ->whereIn('module_id', $userModules)
         ->where('is_active', true)
         ->get();

      // 4. Load menu orders
      $menuOrders = MenuOrder::whereIn('module_id', $userModules)
         ->whereIn('menu_id', $menus->pluck('id'))
         ->get()
         ->keyBy(fn($item) => $item->module_id . '_' . $item->menu_id);

      // 5. Structure the menu data
      $structured = [];

      foreach ($menus as $menu) {
         $titleName = $menu->menuTitle ? $menu->menuTitle->name : 'Default';

         if (!isset($structured[$titleName])) {
            $structured[$titleName] = [];
         }

         $orderKey = $menu->module_id . '_' . $menu->id;
         $order = $menuOrders[$orderKey]->order ?? 0;

         $menuData = [
            'name' => $menu->name,
            'url' => $menu->url,
            'action_menu' => $menu->action_menu,
            'icon' => $menu->icon,
            'order' => $order,
         ];

         if ($menu->subMenus->count()) {
            $submenus = $menu->subMenus->map(function ($sub) {
               return [
                  'name' => $sub->name,
                  'url' => $sub->url,
                  'action_menu' => $sub->action_menu,
                  'icon' => $sub->icon,
                  'order' => $sub->order,
               ];
            })->sortBy('order')->values()->toArray();

            $menuData['submenus'] = $submenus;
         }

         $structured[$titleName][$menu->name] = $menuData;
      }
      // 6. Sort inside each group
      foreach ($structured as $title => $items) {
         $structured[$title] = collect($items)->sortBy('order')->toArray();
      }

      Session::put('menus', $structured);
   }
}
