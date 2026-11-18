<?php

namespace App\Laravue\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Stevebauman\Location\Facades\Location;
use WhichBrowser\Parser;

/**
 * Class User
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Role[] $roles
 *
 * @method static User create(array $user)
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'whatsapp_number',
        'password',
        'status',
        'jabatan',
        'branch_id',
        'division_id',
        'can_process_disposition',
        'signature',
        'active',
        'last_login_at',
        'last_login_ip',
        'force_logout',
        'device',
        'browser',
        'platform',
        'last_login_location',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'       => 'datetime',
        'last_login_at'           => 'datetime',
        'active'                  => 'boolean',
        'force_logout'            => 'boolean',
        'can_process_disposition' => 'boolean',
    ];

    protected $attributes = [
        'active' => true,
        'status' => 'active',
    ];

    /**
     * Set permissions guard to API by default
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * @inheritdoc
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @inheritdoc
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Check if user has a permission
     * @param String
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        foreach ($this->roles as $role) {
            if (in_array($permission, $role->permissions->pluck('name')->toArray())) {
                return true;
            }
        }
        return false;
    }

    // =========================================================
    // ROLE HELPER METHODS
    // =========================================================

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isKasir()
    {
        return $this->hasRole('kasir');
    }

    public function isManager()
    {
        return $this->hasRole('manager');
    }

    // public function isDireksi()
    // {
    //     return $this->hasRole('direksi');
    // }

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // =========================================================
    // LOGIN TRACKING
    // =========================================================

    public function updateLoginInfo($request)
    {
        // =========================
        // AMBIL IP ASLI
        // =========================
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();

        if ($ip && strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        // =========================
        // USER AGENT
        // =========================
        $userAgent = $request->userAgent() ?? '';

        // =========================
        // PARSE DEVICE/BROWSER/OS
        // =========================
        $parser = new Parser($userAgent);

        // =========================
        // BROWSER
        // =========================
        $browser = 'Unknown Browser';

        if (!empty($parser->browser->name)) {
            $browser = $parser->browser->name;

            if (!empty($parser->browser->version->value)) {
                $browser .= ' ' . $parser->browser->version->value;
            }
        }

        // =========================
        // PLATFORM / OS
        // =========================
        $platform = 'Unknown OS';

        if (!empty($parser->os->name)) {
            $platform = $parser->os->name;

            if (!empty($parser->os->version->value)) {
                $platform .= ' ' . $parser->os->version->value;
            }
        }

        // =========================
        // DEVICE
        // =========================
        $device = null;

        if (!empty($parser->device->model)) {
            $device = $parser->device->model;
        }

        if (!$device && !empty($parser->device->manufacturer)) {
            $device = $parser->device->manufacturer;
        }

        if (!$device && !empty($parser->device->type)) {
            $device = ucfirst($parser->device->type);
        }

        if (!$device) {
            $type = strtolower($parser->device->type ?? '');

            switch ($type) {
                case 'desktop':
                    $device = 'Desktop';
                    break;

                case 'tablet':
                    $device = 'Tablet';
                    break;

                case 'mobile':
                case 'smartphone':
                    $device = 'Mobile';
                    break;

                default:
                    $device = 'Unknown Device';
            }
        }

        // =========================
        // LOKASI LOGIN
        // =========================
        $location = null;

        if (!in_array($ip, ['127.0.0.1', '::1'])) {
            try {
                $position = Location::get($ip);

                if ($position) {
                    $parts = array_filter([
                        $position->cityName ?? null,
                        $position->regionName ?? null,
                        $position->countryName ?? null,
                    ]);

                    $location = implode(', ', $parts);
                }
            } catch (\Throwable $e) {
                \Log::error('GeoIP Error', [
                    'message' => $e->getMessage(),
                    'ip'      => $ip,
                ]);
            }
        }

        // =========================
        // DEBUG LOG
        // =========================
        \Log::info('Login Info Debug', [
            'ip'         => $ip,
            'user_agent' => $userAgent,
            'device'     => $device,
            'browser'    => $browser,
            'platform'   => $platform,
            'location'   => $location,
        ]);

        // =========================
        // UPDATE USER
        // =========================
        $this->update([
            'last_login_at'       => now(),
            'last_login_ip'       => $ip,
            'device'              => $device,
            'browser'             => $browser,
            'platform'            => $platform,
            'last_login_location' => $location,
            'active'              => true,
        ]);
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeRecentlyLoggedIn($query, $minutes = 30)
    {
        return $query->where('last_login_at', '>=', now()->subMinutes($minutes));
    }

    // =========================================================
    // STATUS HELPERS
    // =========================================================

    public function isActive()
    {
        return $this->active === true;
    }

    public function deactivate()
    {
        $this->update(['active' => false]);
    }

    public function activate()
    {
        $this->update(['active' => true]);
    }

    /**
     * Get user status label
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'active'   => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'rejected' => 'Ditolak',
            'disabled' => 'Dinonaktifkan',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Helper method untuk mendapatkan role name
     */
    public function getRoleAttribute()
    {
        try {
            $roles = $this->getRoleNames();
            return !empty($roles) ? $roles[0] : 'user';
        } catch (\Exception $e) {
            return 'user';
        }
    }

    public function getRoleNames()
    {
        return $this->roles->pluck('name');
    }
}