<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends BaseController
{
    const ITEM_PER_PAGE = 15;

    /**
     * Recent Activity untuk Dashboard.
     * Hanya Admin.
     */
    public function recent(Request $request)
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Only administrator can access activity logs.');
        }

        $limit = (int) $request->get('limit', 15);

        if ($limit <= 0) {
            $limit = 15;
        }

        if ($limit > 100) {
            $limit = 100;
        }

        $query = ActivityLog::with([
            'user:id,name',
        ]);

        /*
         * Module.
         */
        if ($request->filled('module')) {
            $query->where(
                'module',
                $request->get('module')
            );
        }

        /*
         * User.
         */
        if ($request->filled('user_id')) {
            $query->where(
                'user_id',
                $request->get('user_id')
            );
        }

        /*
         * Action.
         */
        if ($request->filled('action')) {
            $query->where(
                'action',
                $request->get('action')
            );
        }

        /*
         * Keyword.
         */
        if ($request->filled('keyword')) {
            $keyword = trim(
                $request->get('keyword')
            );

            $query->where(function ($q) use ($keyword) {
                $q->where(
                    'description',
                    'like',
                    '%' . $keyword . '%'
                )
                ->orWhere(
                    'module',
                    'like',
                    '%' . $keyword . '%'
                )
                ->orWhere(
                    'action',
                    'like',
                    '%' . $keyword . '%'
                )
                ->orWhereHas('user', function ($user) use ($keyword) {
                    $user->where(
                        'name',
                        'like',
                        '%' . $keyword . '%'
                    );
                });
            });
        }

        return response()->json(
            $query
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit)
        );
    }

    /**
     * Semua Activity Log.
     * Hanya Admin.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Only administrator can access activity logs.');
        }

        $limit = (int) Arr::get(
            $request->all(),
            'limit',
            self::ITEM_PER_PAGE
        );

        if ($limit <= 0) {
            $limit = self::ITEM_PER_PAGE;
        }

        if ($limit > 100) {
            $limit = 100;
        }

        $query = ActivityLog::with([
            'user:id,name',
        ]);

        /*
         * Keyword.
         */
        if ($request->filled('keyword')) {
            $keyword = trim(
                $request->get('keyword')
            );

            $query->where(function ($q) use ($keyword) {
                $q->where(
                    'description',
                    'like',
                    '%' . $keyword . '%'
                )
                ->orWhere(
                    'module',
                    'like',
                    '%' . $keyword . '%'
                )
                ->orWhere(
                    'action',
                    'like',
                    '%' . $keyword . '%'
                )
                ->orWhereHas('user', function ($user) use ($keyword) {
                    $user->where(
                        'name',
                        'like',
                        '%' . $keyword . '%'
                    );
                });
            });
        }

        /*
         * Module.
         */
        if ($request->filled('module')) {
            $query->where(
                'module',
                $request->get('module')
            );
        }

        /*
         * Action.
         */
        if ($request->filled('action')) {
            $query->where(
                'action',
                $request->get('action')
            );
        }

        /*
         * User.
         */
        if ($request->filled('user_id')) {
            $query->where(
                'user_id',
                $request->get('user_id')
            );
        }

        return response()->json(
            $query
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit)
        );
    }
}