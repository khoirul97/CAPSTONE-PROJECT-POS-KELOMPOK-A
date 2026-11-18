<?php
/**
 * File UserController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Permission;
use App\Laravue\Models\Role;
use App\Laravue\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */

    public function index(Request $request)
    {
        $searchParams = $request->all();
        $userQuery = User::query();
        $limit = Arr::get(
            $searchParams,
            'limit',
            static::ITEM_PER_PAGE
        );
        $all = Arr::get(
            $searchParams,
            'all',
            false
        );
        $role = Arr::get(
            $searchParams,
            'role',
            ''
        );
        $keyword = Arr::get(
            $searchParams,
            'keyword',
            ''
        );
        // ==========================================================
        // FILTER ROLE
        // ==========================================================
        if (!empty($role)) {
            $userQuery->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }
        // ==========================================================
        // FILTER KEYWORD
        // name OR email OR whatsapp_number
        // ==========================================================
        if (!empty($keyword)) {
            $userQuery->where(function ($q) use ($keyword) {
                $q->where(
                    'name',
                    'LIKE',
                    '%' . $keyword . '%'
                )
                ->orWhere(
                    'email',
                    'LIKE',
                    '%' . $keyword . '%'
                )
                ->orWhere(
                    'whatsapp_number',
                    'LIKE',
                    '%' . $keyword . '%'
                );
            });
        }
        // ==========================================================
        // RETURN ALL
        // ==========================================================
        if ($all) {
            return UserResource::collection(
                $userQuery->get()
            );
        }
        // ==========================================================
        // PAGINATION
        // ==========================================================
        return UserResource::collection(
            $userQuery->paginate($limit)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'password' => ['required', 'min:6'],
                    'confirmPassword' => 'same:password',
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }

        // ✅ ambil dulu semua input
        $params = $request->all();

        $user = User::create([
            'name' => $params['name'],
            'email' => $params['email'],
            'whatsapp_number' => $params['whatsapp_number'],
            'password' => Hash::make($params['password']),
            'jabatan' => $params['jabatan'],
        ]);

        $role = Role::findByName($params['role']);
        $user->syncRoles($role);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function statistics(Request $request)
    {
        $total = User::count();

        return response()->json([
            'total' => $total,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->isAdmin()) {
            return response()->json(['error' => 'Admin can not be modified'], 403);
        }

        $currentUser = Auth::user();

        if (
            !$currentUser->isAdmin()
            && $currentUser->id !== $user->id
            && !$currentUser->hasPermission(\App\Laravue\Acl::PERMISSION_USER_MANAGE)
        ) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $validator = Validator::make($request->all(), $this->getValidationRules(false));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }

        // =========================
        // 🔥 CEK EMAIL
        // =========================
        $email = $request->get('email');
        $found = User::where('email', $email)->first();

        if ($found && $found->id !== $user->id) {
            return response()->json(['error' => 'Email has been taken'], 403);
        }

        // =========================
        // 🔥 UPDATE DATA USER
        // =========================
        $user->name = $request->get('name');
        $user->email = $email;
        $user->whatsapp_number = $request->get('whatsapp_number');
        $user->jabatan = $request->get('jabatan');

        $user->save();

        // =========================
        // 🔥 UPDATE ROLE
        // =========================
        if ($request->has('role')) {
            $role = Role::findByName($request->get('role'));
            $user->syncRoles([$role]);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function updatePermissions(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->isAdmin()) {
            return response()->json(['error' => 'Admin can not be modified'], 403);
        }

        $permissionIds = $request->get('permissions', []);
        $rolePermissionIds = array_map(
            function($permission) {
                return $permission['id'];
            },

            $user->getPermissionsViaRoles()->toArray()
        );

        $newPermissionIds = array_diff($permissionIds, $rolePermissionIds);
        $permissions = Permission::allowed()->whereIn('id', $newPermissionIds)->get();
        $user->syncPermissions($permissions);
        return new UserResource($user);
    }

    public function updatePassword(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => ['required', 'min:6'],
            'confirmPassword' => 'same:password',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();
    
            return response()->json(['success', 'Password updated successfully!']);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            return response()->json(['error' => 'Akses Ditolak'], 403);
        }

        if ($currentUser->isAdmin()) {
            $user = User::findOrFail($id);
            $user->status = $request->get('status');
            $user->force_logout = $request->get('force_logout');
            $user->active = $request->get('active');
            $user->save();

            return new UserResource($user);
        } else {
            return response()->json(['error' => 'Akses Ditolak'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return response()->json(['error' => 'Ehhh! Can not delete admin user'], 403);
        }

        try {
            $user->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

    /**
     * Get permissions from role
     *
     * @param User $user
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function permissions(User $user)
    {
        try {
            return new JsonResponse([
                'user' => PermissionResource::collection($user->getDirectPermissions()),
                'role' => PermissionResource::collection($user->getPermissionsViaRoles()),
            ]);
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules($isNew = true)
    {
        return [
            'name' => 'required',
            'email' => $isNew ? 'required|email|unique:users' : 'required|email',
            'roles' => [
                'required',
                'array'
            ],
        ];
    }
}
