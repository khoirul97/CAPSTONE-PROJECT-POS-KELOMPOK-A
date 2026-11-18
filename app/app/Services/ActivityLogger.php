<?php

namespace App\Services;

use App\Laravue\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Catat aktivitas.
     */
    public static function log(
        string $action,
        ?string $module = null,
        ?string $description = null,
        ?Model $subject = null,
        array $properties = []
    ): ?ActivityLog {
        try {
            /*
             * Jangan mencatat ActivityLog itu sendiri.
             */
            if ($subject instanceof ActivityLog) {
                return null;
            }

            $user = Auth::user();

            return ActivityLog::create([
                'user_id' => $user ? $user->id : null,

                'action' => $action,

                'module' => $module,

                'description' => $description,

                'subject_type' => $subject
                    ? get_class($subject)
                    : null,

                'subject_id' => $subject
                    ? $subject->getKey()
                    : null,

                'properties' => !empty($properties)
                    ? $properties
                    : null,

                'ip_address' => app()->runningInConsole()
                    ? null
                    : request()->ip(),

                'user_agent' => app()->runningInConsole()
                    ? null
                    : request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            /*
             * Activity logging tidak boleh membuat
             * proses utama aplikasi gagal.
             */
            report($e);

            return null;
        }
    }

    /**
     * CREATED.
     */
    public static function created(
        Model $model,
        ?string $description = null,
        array $properties = []
    ): ?ActivityLog {
        return self::log(
            'created',
            self::getModule($model),
            $description ?: self::makeDescription(
                'created',
                $model
            ),
            $model,
            $properties
        );
    }

    /**
     * UPDATED.
     */
    public static function updated(
        Model $model,
        ?string $description = null,
        array $properties = []
    ): ?ActivityLog {
        return self::log(
            'updated',
            self::getModule($model),
            $description ?: self::makeDescription(
                'updated',
                $model
            ),
            $model,
            $properties
        );
    }

    /**
     * DELETED.
     */
    public static function deleted(
        Model $model,
        ?string $description = null,
        array $properties = []
    ): ?ActivityLog {
        return self::log(
            'deleted',
            self::getModule($model),
            $description ?: self::makeDescription(
                'deleted',
                $model
            ),
            $model,
            $properties
        );
    }

    /**
     * RESTORED.
     */
    public static function restored(
        Model $model,
        ?string $description = null,
        array $properties = []
    ): ?ActivityLog {
        return self::log(
            'restored',
            self::getModule($model),
            $description ?: self::makeDescription(
                'restored',
                $model
            ),
            $model,
            $properties
        );
    }

    /**
     * CUSTOM ACTIVITY.
     */
    public static function custom(
        string $module,
        string $description,
        ?Model $subject = null,
        array $properties = []
    ): ?ActivityLog {
        return self::log(
            'custom',
            $module,
            $description,
            $subject,
            $properties
        );
    }

    /**
     * LOGIN.
     */
    public static function login($user): ?ActivityLog
    {
        return self::log(
            'login',
            'auth',
            'Login ke sistem',
            $user instanceof Model ? $user : null,
            [
                'user_id' => $user->id ?? null,
            ]
        );
    }

    /**
     * LOGOUT.
     */
    public static function logout($user): ?ActivityLog
    {
        return self::log(
            'logout',
            'auth',
            'Logout dari sistem',
            $user instanceof Model ? $user : null,
            [
                'user_id' => $user->id ?? null,
            ]
        );
    }

    /**
     * Nama module berdasarkan table model.
     */
    public static function getModule(Model $model): string
    {
        return $model->getTable();
    }

    /**
     * Buat deskripsi otomatis.
     */
    public static function makeDescription(
        string $action,
        Model $model
    ): string {
        $label = self::getModelLabel($model);

        switch ($action) {
            case 'created':
                return 'Menambahkan ' . $label;

            case 'updated':
                return 'Mengubah ' . $label;

            case 'deleted':
                return 'Menghapus ' . $label;

            case 'restored':
                return 'Memulihkan ' . $label;

            default:
                return ucfirst($action) . ' ' . $label;
        }
    }

    /**
     * Mendapatkan label model secara otomatis.
     */
    public static function getModelLabel(Model $model): string
    {
        $table = $model->getTable();

        /*
         * Field umum pada model Anda.
         */
        $candidateFields = [
            'nama',
            'name',
            'judul',
            'title',
            'nama_produk',
            'subjek',
            'pertanyaan',
            'key',
            'slug',
            'kode',
            'nomor',
            'nomer_register',
            'email',
        ];

        foreach ($candidateFields as $field) {
            try {
                $value = $model->getAttribute($field);

                if (
                    $value !== null &&
                    $value !== '' &&
                    is_scalar($value)
                ) {
                    return $table .
                        ' "' .
                        self::truncate((string) $value) .
                        '"';
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        $id = $model->getKey();

        if ($id !== null) {
            return $table . ' #' . $id;
        }

        return $table;
    }

    /**
     * Potong string panjang.
     */
    protected static function truncate(
        string $value,
        int $length = 100
    ): string {
        $value = trim($value);

        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return mb_substr(
            $value,
            0,
            $length
        ) . '...';
    }

    /**
     * Bersihkan data sensitif.
     */
    public static function sanitizeAttributes(
        array $attributes
    ): array {
        $hidden = [
            'password',
            'password_confirmation',
            'remember_token',
            'api_token',
            'access_token',
            'refresh_token',
            'token',
            'secret',
            'signature',
            'signature_data',
        ];

        foreach ($hidden as $field) {
            unset($attributes[$field]);
        }

        /*
         * Jangan menyimpan content yang terlalu besar.
         */
        foreach ($attributes as $key => $value) {
            if (
                is_string($value) &&
                mb_strlen($value) > 500
            ) {
                $attributes[$key] =
                    mb_substr($value, 0, 500) . '...';
            }
        }

        return $attributes;
    }
}