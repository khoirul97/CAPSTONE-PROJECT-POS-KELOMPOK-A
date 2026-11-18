<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ActiveUsersController extends BaseController
{
    // public function index(Request $request)
    // {
    //     // Cek permission
    //     if (!$request->user()->hasPermissionTo('manage permission')) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
    //         ], 403);
    //     }

    //     try {
    //         $users = User::recentlyLoggedIn(60)
    //                     ->get()
    //                     ->map(function ($user) {
    //                         return [
    //                             'id' => $user->id,
    //                             'name' => $user->name,
    //                             'email' => $user->email,
    //                             'last_login_at' => $user->last_login_at,
    //                             'last_login_ip' => $user->last_login_ip,
    //                             'role' => $user->role,
    //                             'status' => $user->status,
    //                             'active' => $user->active,
    //                         ];
    //                     });

    //         return response()->json([
    //             'success' => true,
    //             'users' => $users,
    //             'count' => $users->count()
    //         ]);

    //     } catch (\Exception $e) {
    //         \Log::error('Error fetching active users: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat mengambil data user',
    //             'users' => []
    //         ], 500);
    //     }
    // }

    const ITEM_PER_PAGE = 10;

    public function index(Request $request)
    {
        // Cek permission
        if (!$request->user()->hasPermissionTo('manage permission')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
            ], 403);
        }

        try {
            // =========================
            // 🔥 PARAMETER
            // =========================
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);
            $keyword = $request->get('keyword', '');
            $status = $request->get('status', '');
            $role = $request->get('role', '');
            $active = $request->get('active', '');

            // 🔥 sorting (tanpa default dulu)
            $sortField = $request->get('sortField');
            $sortOrder = $request->get('sortOrder', 'desc');

            \Log::info('Active users filter params:', [
                'keyword' => $keyword,
                'status' => $status,
                'role' => $role,
                'active' => $active,
                'sortField' => $sortField,
                'sortOrder' => $sortOrder
            ]);

            // =========================
            // 🔥 VALIDASI SORT FIELD
            // =========================
            $allowedSortFields = [
                'id', 'name', 'email', 'last_login_at', 'last_login_ip',
                'role', 'status', 'active', 'created_at', 'updated_at'
            ];

            if (!empty($sortField) && !in_array($sortField, $allowedSortFields)) {
                $sortField = null;
            }

            // =========================
            // 🔥 BASE QUERY
            // =========================
            $usersQuery = User::with('roles');

            // =========================
            // 🔥 FILTER
            // =========================

            // Keyword
            if (!empty($keyword)) {
                $usersQuery->where(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('last_login_ip', 'LIKE', '%' . $keyword . '%');
                });
            }

            // Status
            if (!empty($status)) {
                $usersQuery->where('status', $status);
            }

            // Role
            if (!empty($role)) {
                $usersQuery->whereHas('roles', function ($query) use ($role) {
                    $query->where('name', $role);
                });
            }

            // Active
            if ($active !== '' && $active !== null) {
                if ($active === 'true' || $active === true || $active === '1' || $active === 1) {
                    $usersQuery->where('active', true);
                } elseif ($active === 'false' || $active === false || $active === '0' || $active === 0) {
                    $usersQuery->where('active', false);
                }
            }

            // =========================
            // 🔥 SORTING
            // =========================
            if (empty($sortField)) {

                // ✅ DEFAULT
                $usersQuery->orderBy('active', 'desc')
                        ->orderBy('last_login_at', 'desc');

            } elseif ($sortField === 'role') {

                $usersQuery->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->orderBy('roles.name', $sortOrder)
                    ->orderBy('users.active', 'desc')
                    ->orderBy('users.last_login_at', 'desc')
                    ->select('users.*');

            } else {

                $usersQuery->orderBy($sortField, $sortOrder);

                // 🔥 fallback sorting biar stabil
                if ($sortField !== 'active') {
                    $usersQuery->orderBy('active', 'desc');
                }

                if ($sortField !== 'last_login_at') {
                    $usersQuery->orderBy('last_login_at', 'desc');
                }
            }

            // =========================
            // 🔥 PAGINATION
            // =========================
            $paginatedUsers = $usersQuery->paginate($limit, ['*'], 'page', $page);

            // =========================
            // 🔥 TRANSFORM DATA
            // =========================
            $transformedUsers = $paginatedUsers->getCollection()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_login_at' => $user->last_login_at,
                    'last_login_ip' => $user->last_login_ip,

                    // tambahan
                    'device' => $user->device,
                    'browser' => $user->browser,
                    'platform' => $user->platform,
                    'location' => $user->last_login_location,

                    'role' => $user->role,
                    'status' => $user->status,
                    'active' => (bool) $user->active,
                    'roles' => $user->roles->pluck('name')->toArray(),
                ];
            });

            $paginatedUsers->setCollection($transformedUsers);

            return response()->json([
                'success' => true,
                'users' => $paginatedUsers,
                'count' => $paginatedUsers->total()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching active users: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user',
                'users' => []
            ], 500);
        }
    }

    public function logoutUser(Request $request, $userId)
    {
        $admin = $request->user();
        
        // Cek jika admin memiliki permission
        if (!$admin->hasPermissionTo('manage permission')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Reset remember token dan set active ke false
        $user->update([
            'remember_token' => null,
            'force_logout' => true,
            'active' => false,
        ]);

        return response()->json([
            'message' => 'User telah di-logout secara paksa.'
        ]);
    }

    public function forceLogoutAll(Request $request)
    {
        $admin = $request->user();
        
        if (!$admin->hasPermissionTo('manage permission')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Reset semua user kecuali admin yang sedang login
        User::where('id', '!=', $admin->id)->update([
            'remember_token' => null,
            'force_logout' => true,
            'active' => false,
        ]);

        return response()->json([
            'message' => 'Semua user telah di-logout secara paksa.'
        ]);
    }

    public function resetForceLogoutAll(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            return response()->json(['error' => 'Akses Ditolak'], 403);
        }
        $admin = $request->user();
        
        if (!$admin->hasPermissionTo('manage permission')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Reset semua user kecuali admin yang sedang login
        User::where('id', '!=', $admin->id)->update([
            'remember_token' => null,
            'force_logout' => false,
            'active' => false,
        ]);

        return response()->json([
            'message' => 'Semua user telah di-logout secara paksa.'
        ]);
    }

    public function activateUser(Request $request, $userId)
    {
        $admin = $request->user();
        
        if (!$admin->hasPermissionTo('manage permission')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'force_logout' => false,
            'active' => true,
        ]);

        return response()->json([
            'message' => 'User telah diaktifkan kembali'
        ]);
    }

    // TAMBAHKAN METHOD DEACTIVATE
    public function deactivateUser(Request $request, $userId)
    {
        $admin = $request->user();
        
        if (!$admin->hasPermissionTo('manage permission')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'force_logout' => true,
            'active' => false,
        ]);

        return response()->json([
            'message' => 'User telah dinonaktifkan'
        ]);
    }
}