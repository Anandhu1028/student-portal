<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\AllowedUrls;
use App\Models\Employees;
use App\Models\Menu;
use App\Models\OtherMenuOperationUrls;
use App\Models\Roles;
use App\Models\SubMenu;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{

   public function __construct()
   {
      $this->middleware('auth');
   }

   public function manageUser($userId = null)
   {
      $logged_user_category = Auth::user()->roles->unique_key;
      $user = $userId ? User::findOrFail($userId) : null;

      $emp_code = '';
      $hire_date = date('d-m-Y');
      if ($userId) {
         $emp_data = Employees::where('user_id', $userId)->first();
         $emp_code = $emp_data->emp_code;
         $hire_date =  date('d-m-Y', strtotime($emp_data->hire_date));
      }


      $selectedModule = $user->default_module_id ?? null;
      $selectedMenu = $user->default_menu_id ?? null;


      return view('employees.manage_employee', compact(
         'userId',
         'user',
         'selectedModule',
         'selectedMenu',
         'emp_code',
         'hire_date',
         'logged_user_category'
      ));
   }



   public function storeUser(Request $request)
   {

      $rules = [
         'name' => 'required|string|max:255',
         'email' => 'required|email|unique:users,email,' . $request->user_id,
         'phone_number' => 'nullable|string|max:20',
         'role_id' => 'required|exists:roles,id',
         'password' => $request->user_id
            ? 'nullable|string|min:6|confirmed'
            : 'required|string|min:6|confirmed',
         'hire_date' => 'required|date_format:d-m-Y',
         'profile_picture' => 'nullable|image|max:2048',
         'branch_id' => 'required|exists:branches,id',
         'default_module' => 'required|exists:modules,id',
         'default_menu' => 'required|exists:menus,id',
         'default_sub_menu' => 'nullable|exists:sub_menus,id',
         'is_active' => 'sometimes|boolean',
      ];

      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
         return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
         ], 422);
      }

      $default_module = $request->input('default_module');
      $default_menu = $request->input('default_menu');
      $default_sub_menu = $request->input('default_sub_menu') ?? null;

      $is_sub_available = SubMenu::where('menu_id', $default_menu)->exists();

      if (($default_sub_menu === '' || $default_sub_menu === null) && $is_sub_available) {
         return response()->json(['message' => 'Please select a sub menu.'], 404);
      }

      // Check if the menu belongs to the selected module
      $menu_belongs_to_module = Menu::where('module_id', $default_module)
         ->where('id', $default_menu)
         ->exists();

      if (!$menu_belongs_to_module) {
         return response()->json(['message' => 'Menu ID does not belong to the selected module. Please refresh and try again.'], 404);
      }

      // If a submenu is selected, verify it belongs to the selected menu
      if ($default_sub_menu !== null && $default_sub_menu !== '') {
         $sub_menu_belongs_to_menu = SubMenu::where('menu_id', $default_menu)
            ->where('id', $default_sub_menu)
            ->exists();

         if (!$sub_menu_belongs_to_menu) {
            return response()->json(['message' => 'Sub Menu ID does not belong to the selected menu. Please refresh and try again.'], 404);
         }
      }

      if ($request->user_id) {
         $user = User::find($request->user_id);
         $employee = Employees::where('user_id', $request->user_id)->first();
         if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
         }
         if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
         }
         $new_user = false;
      } else {
         $user = new User();
         $employee = new Employees();
         $new_user = true;
      }

      $user->name = $request->input('name');
      $user->email =  $request->input('email');
      $user->phone_number =  $request->input('phone_number') ?? null;
      $user->role_id =  $request->input('role_id') ?? null;
      $user->branch_id =  $request->input('branch_id') ?? null;
      $user->default_module_id =  $default_module;
      $user->default_menu_id =  $default_menu;
      $user->default_sub_menu_id =  $default_sub_menu;
      $user->is_active = $request->has('is_active') ? (bool) $request->is_active : true;

      if ($request->filled('password')) {
         $user->password = bcrypt($request->password);
      }

      if ($request->hasFile('profile_picture')) {
         $path = $request->file('profile_picture')->store('profile_pictures', 'public');
         $user->profile_picture = $path;
      } else if ($new_user) {
         $user->profile_picture = 'profile_pictures/default.png';
      }

      $user->save();
      $userId = $user->id;
      $hire_date = date('Y-m-d', strtotime($request->hire_date));
      $employee->emp_code = $request->input('emp_code') ?? '';
      $employee->user_id  = $userId;
      $employee->first_name = $request->input('name');
      $employee->email  = $request->input('email');
      $employee->phone_number  = $request->input('phone_number') ?? null;
      $employee->hire_date  = $hire_date;
      $employee->save();

      if ($new_user) {
         $user_modules = UserModule::where('user_id', Auth::id())->pluck('module_id')->toArray();

         // Get modules allowed for the new user's role, but limited to creator's modules
         $modules = AllowedUrls::whereIn('module_id', $user_modules)
            ->where('role_id', $user->role_id)
            ->pluck('module_id')
            ->unique()
            ->toArray();

         if (!empty($modules)) {
            $bulkInsert = collect($modules)->map(function ($moduleId) use ($userId) {
               return [
                  'user_id'    => $userId,
                  'module_id'  => $moduleId
               ];
            })->toArray();

            UserModule::insert($bulkInsert); // ✅ bulk insert once
         }

         $permission_form = view('employees.partials.permission_form', compact(
            'userId',
            'user',
         ))->render();
      } else {
         $permission_form = '';
      }

      return response()->json(['message' => 'User saved successfully', 'id' => $userId, 'permission_form' => $permission_form]);
   }

   public function autocompleteMenus(Request $request)
   {
      $menus = Menu::where('module_id', $request->module_id)
         ->where('is_active', true)
         ->limit(20)->get(['id', 'name']);

      return response()->json($menus); // ✅ Explicit JSON response
   }

   public function autocompleteSubMenus(Request $request)
   {
      $subs = SubMenu::where('menu_id', $request->menu_id)
         ->where('is_active', true)
         ->limit(50)->get(['id', 'name', 'menu_id']);
      return response()->json($subs); // ✅ Explicit JSON response
   }

   public function viewEmployees()
   {
      $user_categories = Roles::all();

      $auth_user = Auth::user();
      $role_name = $auth_user->roles->role_name;
      $emp_dataAr = Employees::with('users:id,is_active,profile_picture,role_id');
      if (!in_array($role_name, ['Admin', 'Super Admin'])) {
         $emp_dataAr = $emp_dataAr->where('id', $auth_user->id);
      } else if ($role_name == 'Admin') {
         $emp_dataAr = $emp_dataAr->whereHas('users', function ($q) {
            $q->whereHas('roles', function ($r) {
               $r->where('role_name', '!=', 'Super Admin');
            });
         });
      }
      $emp_dataAr = $emp_dataAr->paginate(100);

      return view('employees.view_employees', ['emp_dataAr' => $emp_dataAr, 'user_categories' => $user_categories]);
   }

   public function updateEmpStatus(Request $request)
   {
      $rules = [
         'emp_id' => 'required|string|max:255',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
         return response()->json([
            'status' => 'error',
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp;An error occurred. Please try refreshing the page.'
         ], 404);
      }

      $userId = $request->input('emp_id');
      if ($userId == Auth::id()) {
         return response()->json([
            'status' => 'error',
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp;You are not permitted to update the status of your own account.'
         ], 404);
      } else if (Auth::user()->role_id != 1) {
         return response()->json([
            'status' => 'error',
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp;Not allowed'
         ], 404);
      }
      $user = User::find($userId);
      if ($user) {
         $newStatus = $user->is_active ? 0 : 1;

         $user->is_active = $newStatus;
         $user->save();
         return response()->json([
            'status' => 'success',
            'message' => '<i class="fa fa-check-circle text-success"></i>&nbsp;User status updated successfully.',
            'new_status' => $newStatus,
            'emp_id' => $userId
         ]);
      } else {
         return response()->json([
            'status' => 'error',
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp;User not found.'
         ], 404);
      }
   }

   public function updateEmpRole(Request $request)
   {
      $rules = [
         'emp_id' => 'required|string|max:255',
         'user_category' => 'required|string|max:255',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
         return response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp; An error occurred. Please try refreshing the page.'
         ], 404);
      }

      $userId = $request->input('emp_id');
      if ($userId == Auth::id()) {
         return response()->json([
            'status' => 'error',
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp;You are not permitted to update the status of your own account.'
         ], 404);
      } else if (Auth::user()->role_id != 1) {
         return response()->json([
            'status' => 'error',
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp;Not allowed'
         ], 404);
      }
      $user = User::find($userId);
      if ($user) {


         $user->role_id = $request->input('user_category');
         $user->save();
         return response()->json([
            'status' => 'success',
            'message' => '<i class="fa fa-check-circle text-success"></i>&nbsp;User Role updated successfully.',
            'emp_id' => $userId
         ]);
      } else {
         return response()->json([
            'status' => 'error',
            'message' => '<i class="fa fa-times text-danger"></i>&nbsp;User not found.'
         ], 404);
      }
   }

   public function UsernameAutocomplete(Request $request)
   {
      $term = $request->get('term');  // Search term

      $employees = Employees::where('first_name', 'LIKE', '%' . $term . '%')
         ->orWhere('last_name', 'LIKE', '%' . $term . '%')
         ->orWhere('email', 'LIKE', '%' . $term . '%')  // Add email search
         ->select('id', 'first_name', 'last_name', 'email')  // Select relevant fields
         ->limit(50)  // Limit results to 50
         ->get()
         ->map(function ($employee) {
            $employeeLabel = $employee->first_name . ' ' . $employee->last_name . ' ' .  $employee->email;
            return [
               'label' => $employeeLabel,
               'value' => $employee->id,
            ];
         });

      return response()->json($employees);
   }

   public function manageUserPermissions(Request $request)
   {
      $userId = $request->input('user_id', '');
      if (!$userId) {
         return response()->json([
            'message' => 'User ID is required.'
         ], 422);
      }
      $selectedUser = User::findOrFail($userId);

      if (!$selectedUser) {
         return response()->json([
            'message' => 'User not found.'
         ], 404);
      }

      $roleId = $selectedUser->role_id;
      $permissions = $request->input('permission', []);
      if (count($permissions) == 0) {
         return response()->json([
            'message' => 'Something went wrong, Please try again.'
         ]);
      }

      $auth_user = Auth::user();
      $role_name = $auth_user->roles->role_name;

      if (!in_array($role_name, ['Admin', 'Super Admin'])) {
         return response()->json([
            'message' => 'You are not allowed to do the operation.'
         ], 422);
      }



      foreach ($permissions as $key => $value) {
         $value = (int) $value;
         $data = $this->resolvePermissionKey($key);
         if (!$data['url']) continue;



         $roleHasPermission = AllowedUrls::where('role_id', $roleId)
            ->where('url', $data['url'])
            ->where('allowed', true)
            ->exists();



         if ($roleHasPermission) {
            if ($value === 0) {
               AllowedUrls::updateOrCreate(
                  ['user_id' => $userId, 'url' => $data['url']],
                  [
                     'role_id' => $roleId,
                     'menu_id' => $data['menu_id'],
                     'sub_menu_id' => $data['sub_menu_id'],
                     'allowed' => false,
                  ]
               );
            } else {
               AllowedUrls::where('user_id', $userId)->where('url', $data['url'])->delete();
            }
         } else {
            if ($value === 1) {
               AllowedUrls::updateOrCreate(
                  ['user_id' => $userId, 'url' => $data['url']],
                  [
                     'role_id' => $roleId,
                     'menu_id' => $data['menu_id'],
                     'sub_menu_id' => $data['sub_menu_id'],
                     'allowed' => true,
                  ]
               );
            } else {
               AllowedUrls::where('user_id', $userId)->where('url', $data['url'])->delete();
            }
         }
      }
      return response()->json([
         'message' => 'Permissions updated successfully.'
      ]);
   }

   private function resolvePermissionKey($key)
   {
      if (str_starts_with($key, 'menu-')) {
         $menuId = (int) str_replace('menu-', '', $key);
         $menu = Menu::find($menuId);
         return ['url' => $menu->url ?? null, 'menu_id' => $menuId, 'sub_menu_id' => null];
      }
      if (str_starts_with($key, 'sub-')) {
         $subMenuId = (int) str_replace('sub-', '', $key);
         $sub = SubMenu::find($subMenuId);
         return ['url' => $sub->url ?? null, 'menu_id' => $sub->menu_id, 'sub_menu_id' => $subMenuId];
      }
      if (str_starts_with($key, 'extra-')) {
         $extraId = (int) str_replace('extra-', '', $key);
         $extra = OtherMenuOperationUrls::find($extraId);
         return ['url' => $extra->url ?? null, 'menu_id' => $extra->menu_id, 'sub_menu_id' => $extra->sub_menu_id];
      }
      return ['url' => null, 'menu_id' => null, 'sub_menu_id' => null];
   }

   public function updatePassword(Request $request)
   {
      $userId = $request->input('user_id', '');
      if (!$userId) {
         return response()->json([
            'message' => 'User ID is required.'
         ], 422);
      }

      $auth_user = Auth::user();
      $role_name = $auth_user->roles->role_name;

      // Check permission: user can update own password or Admin / Super Admin can update anyone
      if (!($auth_user->id == (int) $userId || in_array($role_name, ['Admin', 'Super Admin']))) {
         return response()->json([
            'message' => 'You are not allowed to change the password.'
         ], 422);
      }

      $user = User::find($userId);
      if (!$user) {
         return response()->json([
            'message' => 'User not found.'
         ], 404);
      }

      // Validation
      $validator = Validator::make($request->all(), [
         'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
            if (!Hash::check($value, $user->password)) {
               $fail('The current password is incorrect.');
            }
         }],
         'password' => ['required', 'string', 'min:8', 'confirmed'],
      ], [
         'current_password.required' => 'Please enter your current password.',
         'password.required' => 'Please enter a new password.',
         'password.confirmed' => 'The new password and confirmation do not match.',
         'password.min' => 'The new password must be at least 8 characters long.',
      ]);

      if ($validator->fails()) {
         return response()->json([
            'errors' => $validator->errors()
         ], 422);
      }

      // Update password
      $user->password = Hash::make($request->password);
      $user->save();

      return response()->json([
         'message' => 'Password updated successfully.'
      ]);
   }
}
