<?php

namespace App\Http\Controllers;

use App\Models\AllowedUrls;
use App\Models\Menu;
use App\Models\MenuTitle;
use App\Models\Module;
use App\Models\ModuleMenus;
use App\Models\OtherMenuOperationUrls;
use App\Models\Roles;
use App\Models\SubMenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
   public function index()
   {
      $menus = Menu::with(['module', 'subMenus', 'OperationUrls'])->get();

      return view('menus.menu_management', compact('menus'));
   }

   public function getMenusByModule(Request $request)
   {
      $moduleId = $request->query('module_id');
      $menus = Menu::where('module_id', $moduleId)->get(['id', 'name']);
      return response()->json(['menus' => $menus]);
   }

   public function getSubMenusByMenu(Request $request)
   {
      $menuId = $request->query('menu_id');
      $subMenus = SubMenu::where('menu_id', $menuId)->get(['id', 'name']);
      return response()->json(['subMenus' => $subMenus]);
   }

   public function showMenuForm($id = null)
   {
      $menu = $id ? Menu::with('allowedUrls')->findOrFail($id) : null;
      $modules = Module::all();
      $menuTitles = MenuTitle::all();
      $roles = Roles::all();
      return view('menus.menu_form', compact('menu', 'modules', 'menuTitles', 'roles'));
   }

   public function storeMenu(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:100',
         'display_name' => 'required|string|max:100',
         'module_id' => 'required|exists:modules,id',
         'menu_title_id' => 'required|exists:menu_titles,id',
         'url' => [
            'nullable',
            'string',
            'max:200',
            function ($attribute, $value, $fail) use ($request) {
               if ($value) {
                  // Get the route collection
                  $routeCollection = app('router')->getRoutes();

                  // Normalize input value (remove leading slash)
                  $value = ltrim($value, '/');

                  $routeExists = false;

                  foreach ($routeCollection as $route) {
                     if ($route->getName() === $value) {
                        $routeExists = true;
                        break;
                     }
                  }

                  if (!$routeExists) {
                     $fail('The URL does not exist in the defined routes.');
                  }

                  // Check for duplicate URL in the Menu table
                  $existingUrl = Menu::where('url', $request->url);
                  if ($request->menu_id) {
                     $existingMenu = Menu::find($request->menu_id);
                     if ($existingMenu && $existingMenu->url === $value) {
                        return; // No validation error
                     }
                     $existingUrl->where('id', '!=', $request->menu_id);
                  }
                  if ($existingUrl->exists()) {
                     $fail('The URL already exists in the menu table.');
                  }
               }
            }
         ],
         'icon' => 'required|string|max:100',
         'role_ids' => 'required|array',
         'role_ids.*' => 'exists:roles,id',
      ]);

      if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
      }

      $menu = $request->input('menu_id') ? Menu::findOrFail($request->input('menu_id')) : new Menu;

      $menu = DB::transaction(function () use ($menu, $request) {
         $menu->fill($request->only([
            'name',
            'display_name',
            'module_id',
            'menu_title_id',
            'url',
            'icon',
            'is_active',
            'is_documented',
            'action_menu',
            'default_menu'
         ]))->save();

         if ($request->has('role_ids')) {
            AllowedUrls::where('url', $menu->url)->whereNull('user_id')->delete();
            foreach ($request->role_ids as $roleId) {
               AllowedUrls::updateOrCreate(
                  [
                     'module_id' => $request->input('module_id'),
                     'menu_id' => $menu->id,
                     'role_id' => $roleId,
                  ],
                  [
                     'url' => $menu->url,
                  ]
               );
            }
         }

         if ($request->has('assign_menu_to')) {
            ModuleMenus::where('menu_id', $menu->id)->delete();
            foreach ($request->assign_menu_to as $module_id) {
               ModuleMenus::create([
                  'module_id' => $module_id,
                  'menu_id' => $menu->id,
               ]);
            }
         }
         return $menu; // Return the updated menu instance
      });

      return response()->json(['message' => 'Menu saved successfully', 'menu_id' => $menu->id]);
   }

   public function showSubMenuForm($id = null)
   {
      $subMenu = $id ? SubMenu::with('allowedUrls')->findOrFail($id) : null;
      $modules = Module::all();
      $roles = Roles::all();
      $menus = $subMenu ? Menu::where('module_id', $subMenu->menu->module_id)->get() : collect();
      return view('menus.sub_menu_form', compact('subMenu', 'modules', 'roles', 'menus'));
   }


   public function storeSubMenu(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:100',
         'display_name' => 'required|string|max:100',
         'menu_id' => 'required|exists:menus,id',
         'url' => [
            'required',
            'string',
            'max:200',
            function ($attribute, $value, $fail) use ($request) {
               if ($value) {
                  $routeCollection = app('router')->getRoutes();

                  // Normalize the URL
                  $normalizedValue = ltrim($value, '/');

                  $routeExists = false;

                  foreach ($routeCollection as $route) {
                     if ($route->uri() === $normalizedValue || $route->getName() === $normalizedValue) {
                        $routeExists = true;
                        break;
                     }
                  }

                  if (!$routeExists) {
                     $fail('The URL does not exist in the defined routes.');
                  }

                  // Check for duplicate URL in SubMenu table
                  $existingUrl = SubMenu::where('url', $request->url);
                  if ($request->input('sub_menu_id')) {
                     $existingSubMenu = SubMenu::find($request->input('sub_menu_id'));
                     if ($existingSubMenu && $existingSubMenu->url === $value) {
                        return;
                     }
                     $existingUrl->where('id', '!=', $request->sub_menu_id);
                  }
                  if ($existingUrl->exists()) {
                     $fail('The URL already exists in the submenu table.');
                  }
               }
            }
         ],
         'order' => 'integer|min:0',
         'role_ids' => 'required|array',
         'role_ids.*' => 'exists:roles,id',
      ]);

      if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
      }

      $subMenu = $request->sub_menu_id ? SubMenu::findOrFail($request->sub_menu_id) : new SubMenu;

      $subMenu = DB::transaction(function () use ($request, $subMenu) {
         $subMenu->fill($request->only([
            'name',
            'display_name',
            'menu_id',
            'url',
            'icon',
            'order',
            'is_active',
            'is_documented',
            'action_menu'
         ]))->save();

         if ($request->has('role_ids')) {
            AllowedUrls::where('url', $subMenu->url)
               ->whereNull('user_id')->delete();
            $module_id = Menu::find($request->input('menu_id'))->module_id;
            AllowedUrls::where('menu_id', $subMenu->menu_id)
               ->whereNull('sub_menu_id')
               ->whereNull('url')
               ->delete();

            foreach ($request->input('role_ids') as $roleId) {

               AllowedUrls::create(
                  [
                     'module_id' => $module_id,
                     'role_id' => $roleId,
                     'menu_id' => $subMenu->menu_id,
                     'sub_menu_id' => $subMenu->id,
                     'url' => $subMenu->url,
                  ]
               );
            }
         }

         return $subMenu;
      });

      return response()->json(['message' => 'Sub-Menu saved successfully', 'sub_menu_id' => $subMenu->id]);
   }


   public function showUrlForm($id = null)
   {
      $operationUrl = $id ? OtherMenuOperationUrls::with('allowedUrls')->findOrFail($id) : null;
      $modules = Module::all();
      $roles = Roles::all();
      $menus = $operationUrl ? Menu::where('module_id', $operationUrl->module_id)->get() : collect();
      $subMenus = $operationUrl && $operationUrl->menu_id ? SubMenu::where('menu_id', $operationUrl->menu_id)->get() : collect();
      return view('menus.url_form', compact('operationUrl', 'modules', 'roles', 'menus', 'subMenus'));
   }


   public function storeUrl(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'module_id' => 'required|exists:modules,id',
         'menu_id' => 'required|exists:menus,id',
         'sub_menu_id' => 'nullable|exists:sub_menus,id',
         'url' => [
            'required',
            'string',
            'max:200',
            function ($attribute, $value, $fail) use ($request) {
               if ($value) {
                  $routeCollection = app('router')->getRoutes();

                  // Normalize the URL
                  $normalizedValue = ltrim($value, '/');

                  $routeExists = false;

                  foreach ($routeCollection as $route) {
                     if ($route->uri() === $normalizedValue || $route->getName() === $normalizedValue) {
                        $routeExists = true;
                        break;
                     }
                  }

                  if (!$routeExists) {
                     $fail('The URL does not exist in the defined routes.');
                  }

                  $existingUrl = OtherMenuOperationUrls::where('url', $value);
                  if ($request->input('url_id')) {
                     $existingOperationUrl = OtherMenuOperationUrls::find($request->input('url_id'));
                     if ($existingOperationUrl && $existingOperationUrl->url === $value) {
                        return;
                     }
                     $existingUrl->where('id', '!=', $request->url_id);
                  }
                  if ($existingUrl->exists()) {
                     $fail('The URL already exists in the operation URLs table.');
                  }
               }
            }
         ],
         'role_ids' => 'required|array',
         'role_ids.*' => 'exists:roles,id',
      ]);

      if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
      }

      $operationUrl = $request->input('url_id') ? OtherMenuOperationUrls::findOrFail($request->input('url_id')) : new OtherMenuOperationUrls;

      $operationUrl = DB::transaction(function () use ($request, $operationUrl) {
         $operationUrl->fill($request->only([
            'module_id',
            'menu_id',
            'sub_menu_id',
            'name',
            'url',
         ]))->save();

         if ($request->has('role_ids')) {
            AllowedUrls::where('url', $operationUrl->url)
               ->whereNull('user_id')->delete();
            AllowedUrls::where('menu_id',  $request->input('menu_id'))
               ->whereNull('sub_menu_id')
               ->whereNull('url')
               ->delete();
            foreach ($request->input('role_ids') as $roleId) {
               AllowedUrls::create(
                  [
                     'module_id' => $request->input('module_id'),
                     'role_id' => $roleId,
                     'menu_id' => $request->input('menu_id'),
                     'sub_menu_id' => $request->input('sub_menu_id') ?? null,
                     'url' => $operationUrl->url,
                  ]
               );
            }
         }

         return $operationUrl;
      });

      return response()->json(['message' => 'URL saved successfully', 'url_id' => $operationUrl->id]);
   }
}
