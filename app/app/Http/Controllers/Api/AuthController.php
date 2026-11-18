<?php
/**
 * File AuthController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Laravue\Models\User;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        // Cek email/password salah
        if (!Auth::attempt($credentials)) {
            return response()->json(new JsonResponse([], 'Email atau Password tidak tepat'), Response::HTTP_UNAUTHORIZED);
        }

        $currentUser = Auth::user();
        $user = $request->user();

        // Update login info
        $user->updateLoginInfo($request);

        // Cek status akun untuk non-admin
        if(!$currentUser->isAdmin()) {
            if ($user->status === 'inactive') {
                Auth::logout();
                return response()->json(new JsonResponse([], 'Akun belum aktif, mohon menunggu persetujuan dari admin'), Response::HTTP_UNAUTHORIZED);
            }
            if ($user->status === 'rejected') {
                Auth::logout();
                return response()->json(new JsonResponse([], 'Pendaftaran Akun telah ditolak'), Response::HTTP_UNAUTHORIZED);
            }
            if ($user->status === 'disabled') {
                Auth::logout();
                return response()->json(new JsonResponse([], 'Akun telah dinonaktifkan'), Response::HTTP_UNAUTHORIZED);
            }
        }

        return response()->json(new JsonResponse(new UserResource($user)), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Auth::guard('web')->logout();
        
        if ($user) {
            $user->update([
                'active' => false,
                'force_logout' => false,
            ]);
        }
        
        return response()->json((new JsonResponse())->success([]), Response::HTTP_OK);
    }
}