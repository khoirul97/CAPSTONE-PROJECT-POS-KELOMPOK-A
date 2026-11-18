<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StokMutasi extends Model
{
    use SoftDeletes;

    protected $table = 'stok_mutasi';

    protected $fillable = [
        'produk_id',
        'user_id',
        'tipe',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'referensi',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    /**
     * Relasi ke produk.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Relasi ke user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat perubahan stok.
     *
     * Tipe:
     * - masuk        => jumlah ditambahkan ke stok
     * - keluar       => jumlah dikurangi dari stok
     * - penyesuaian  => jumlah dianggap sebagai stok baru
     */
    public static function catat(
        Produk $produk,
        $userId,
        string $tipe,
        int $jumlah,
        ?string $referensi = null,
        ?string $keterangan = null
    ) {
        if ($jumlah < 0) {
            throw new InvalidArgumentException(
                'Jumlah stok tidak boleh negatif.'
            );
        }

        $tipe = strtolower(trim($tipe));

        if (!in_array($tipe, [
            'masuk',
            'keluar',
            'penyesuaian',
        ], true)) {
            throw new InvalidArgumentException(
                'Tipe mutasi stok tidak valid.'
            );
        }

        return DB::transaction(function () use (
            $produk,
            $userId,
            $tipe,
            $jumlah,
            $referensi,
            $keterangan
        ) {
            /*
             * Ambil produk terbaru dengan lock.
             *
             * Ini penting ketika ada dua transaksi bersamaan,
             * supaya stok tidak race condition.
             */
            $produkTerkunci = Produk::where('id', $produk->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stokSebelum = (int) $produkTerkunci->stok;

            switch ($tipe) {
                case 'masuk':
                    $stokSesudah = $stokSebelum + $jumlah;
                    break;

                case 'keluar':
                    if ($stokSebelum < $jumlah) {
                        throw new \Exception(
                            "Stok produk {$produkTerkunci->nama} tidak mencukupi. " .
                            "Tersedia: {$stokSebelum}, diperlukan: {$jumlah}."
                        );
                    }

                    $stokSesudah = $stokSebelum - $jumlah;
                    break;

                case 'penyesuaian':
                    /*
                     * Untuk penyesuaian:
                     *
                     * $jumlah = stok baru
                     *
                     * Contoh:
                     * stok sekarang = 10
                     * stok baru     = 7
                     *
                     * maka:
                     * stok_sebelum = 10
                     * stok_sesudah = 7
                     * jumlah       = 7
                     */
                    $stokSesudah = $jumlah;
                    break;

                default:
                    throw new InvalidArgumentException(
                        'Tipe mutasi stok tidak valid.'
                    );
            }

            /*
             * Update stok produk.
             *
             * Hanya field stok yang diubah.
             */
            $produkTerkunci->stok = $stokSesudah;
            $produkTerkunci->save();

            /*
             * Untuk penyesuaian, jumlah secara database tetap
             * mengikuti nilai stok baru karena controller
             * mengirim stok_baru.
             */
            $jumlahMutasi = $jumlah;

            return self::create([
                'produk_id' => $produkTerkunci->id,
                'user_id' => $userId,
                'tipe' => $tipe,
                'jumlah' => $jumlahMutasi,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'referensi' => $referensi,
                'keterangan' => $keterangan,
            ]);
        });
    }
}