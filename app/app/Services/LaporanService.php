<?php

namespace App\Services;

use App\Laravue\Models\Transaksi;
use App\Laravue\Models\TransaksiDetail;
use App\Laravue\Models\SesiKasir;
use App\Laravue\Models\Produk;
use App\Laravue\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanService
{
    /**
     * Mendapatkan user yang sedang login.
     */
    private function currentUser()
    {
        return Auth::user();
    }

    /**
     * Memeriksa apakah user login memiliki role kasir.
     */
    private function isKasirLogin()
    {
        $user = $this->currentUser();

        return $user && $user->isKasir();
    }

    /**
     * Memaksa filter user_id untuk kasir login.
     *
     * Admin/manager tetap menggunakan filter yang diminta.
     */
    private function effectiveUserId($requestedUserId = null)
    {
        $user = $this->currentUser();

        if ($user && $user->isKasir()) {
            return $user->id;
        }

        return $requestedUserId;
    }

    /**
     * Membatasi query berdasarkan pemilik data.
     *
     * Contoh:
     * filterKasir($query, 'user_id');
     * filterKasir($query, 'transaksi.user_id');
     */
    private function filterKasir($query, $column = 'user_id')
    {
        $user = $this->currentUser();

        if ($user && $user->isKasir()) {
            $query->where($column, $user->id);
        }

        return $query;
    }

    /**
     * Laporan penjualan harian berdasarkan tanggal dan kasir.
     */
    public function harian($dari, $sampai, $userId = null)
    {
        $userId = $this->effectiveUserId($userId);

        $periodeMulai = Carbon::parse($dari)->startOfDay();
        $periodeSampai = Carbon::parse($sampai)->endOfDay();

        $baseQuery = Transaksi::selesai()
            ->whereBetween('created_at', [
                $periodeMulai,
                $periodeSampai,
            ]);

        $this->filterKasir($baseQuery);

        if ($userId !== null && $userId !== '') {
            $baseQuery->where('user_id', $userId);
        }

        $data = (clone $baseQuery)
            ->with('kasir:id,name,email')
            ->select(
                'user_id',
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(subtotal) as total_subtotal'),
                DB::raw('SUM(diskon) as total_diskon'),
                DB::raw('SUM(pajak) as total_pajak'),
                DB::raw('SUM(total) as total_penjualan'),
                DB::raw("
                    SUM(
                        CASE
                            WHEN metode_pembayaran = 'cash'
                            THEN total
                            ELSE 0
                        END
                    ) as total_tunai
                "),
                DB::raw("
                    SUM(
                        CASE
                            WHEN metode_pembayaran != 'cash'
                            THEN total
                            ELSE 0
                        END
                    ) as total_non_tunai
                ")
            )
            ->groupBy('tanggal', 'user_id')
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get()
            ->map(function ($row) {
                $kasir = $row->kasir;

                return [
                    'tanggal' => $row->tanggal,
                    'user_id' => $row->user_id,
                    'username' => $kasir ? $kasir->email : null,
                    'nama_kasir' => $kasir ? $kasir->name : '-',
                    'total_transaksi' => (int) $row->total_transaksi,
                    'total_subtotal' => (float) $row->total_subtotal,
                    'total_diskon' => (float) $row->total_diskon,
                    'total_pajak' => (float) $row->total_pajak,
                    'total_penjualan' => (float) $row->total_penjualan,
                    'total_tunai' => (float) $row->total_tunai,
                    'total_non_tunai' => (float) $row->total_non_tunai,
                ];
            });

        $ringkasan = [
            'total_transaksi' => (int) $data->sum('total_transaksi'),
            'total_penjualan' => (float) $data->sum('total_penjualan'),
            'total_tunai' => (float) $data->sum('total_tunai'),
            'total_non_tunai' => (float) $data->sum('total_non_tunai'),
            'total_diskon' => (float) $data->sum('total_diskon'),
            'total_pajak' => (float) $data->sum('total_pajak'),
        ];

        $detailKasir = $data
            ->groupBy('tanggal')
            ->map(function ($transaksiTanggal) {
                return $transaksiTanggal->values();
            });

        return [
            'periode' => [
                'dari' => $dari,
                'sampai' => $sampai,
            ],
            'filter' => [
                'user_id' => $userId,
            ],
            'ringkasan' => $ringkasan,
            'detail' => $data->values(),
            'detail_kasir' => $detailKasir,
        ];
    }

    /**
     * Laporan per shift / sesi kasir.
     *
     * Kasir hanya melihat sesi yang dibukanya sendiri.
     */
    public function perShift($dari, $sampai)
    {
        $query = SesiKasir::with([
            'kasir',
            'penutup',
        ])->whereBetween('waktu_buka', [
            Carbon::parse($dari)->startOfDay(),
            Carbon::parse($sampai)->endOfDay(),
        ]);

        $this->filterKasir($query, 'user_id');

        $data = $query
            ->orderByDesc('waktu_buka')
            ->get()
            ->map(function ($sesi) {
                /*
                 * Hitung ringkasan sesi yang masih terbuka.
                 * Tidak melakukan refresh() agar hasil hitungan
                 * yang belum disimpan tidak hilang.
                 */
                if ($sesi->status === 'open') {
                    $sesi->hitungRingkasan();
                }

                $kasir = $sesi->kasir;
                $penutup = $sesi->penutup;

                return [
                    'id' => $sesi->id,
                    'kode' => $sesi->kode,
                    'kasir' => $kasir ? $kasir->name : '-',
                    'username_kasir' => $kasir ? $kasir->email : null,
                    'penutup' => $penutup ? $penutup->name : '-',
                    'username_penutup' => $penutup ? $penutup->email : null,
                    'waktu_buka' => $sesi->waktu_buka,
                    'waktu_tutup' => $sesi->waktu_tutup,
                    'status' => $sesi->status,
                    'kas_awal' => (float) $sesi->kas_awal,
                    'kas_akhir_sistem' => (float) $sesi->kas_akhir_sistem,
                    'kas_akhir_fisik' => $sesi->kas_akhir_fisik !== null
                        ? (float) $sesi->kas_akhir_fisik
                        : null,
                    'selisih' => (float) $sesi->selisih,
                    'total_transaksi' => (int) $sesi->total_transaksi,
                    'total_penjualan' => (float) $sesi->total_penjualan,
                    'total_tunai' => (float) $sesi->total_tunai,
                    'total_non_tunai' => (float) $sesi->total_non_tunai,

                    // ✅ TAMBAHKAN INI
                    'catatan_buka' => $sesi->catatan_buka,
                    'catatan_tutup' => $sesi->catatan_tutup,
                ];
            });

        return [
            'periode' => [
                'dari' => $dari,
                'sampai' => $sampai,
            ],
            'ringkasan' => [
                'total_sesi' => $data->count(),
                'total_transaksi' => (int) $data->sum('total_transaksi'),
                'total_penjualan' => (float) $data->sum('total_penjualan'),
                'total_tunai' => (float) $data->sum('total_tunai'),
                'total_non_tunai' => (float) $data->sum('total_non_tunai'),
                'total_selisih' => (float) $data->sum('selisih'),
            ],
            'detail' => $data->values(),
        ];
    }

    /**
     * Laporan per produk / best seller.
     */
    public function perProduk($dari, $sampai, $limit = 50)
    {
        $query = TransaksiDetail::query()
            ->join(
                'transaksi',
                'transaksi.id',
                '=',
                'transaksi_detail.transaksi_id'
            )
            ->join(
                'produk',
                'produk.id',
                '=',
                'transaksi_detail.produk_id'
            )
            ->where('transaksi.status', 'selesai')
            ->whereBetween('transaksi.created_at', [
                Carbon::parse($dari)->startOfDay(),
                Carbon::parse($sampai)->endOfDay(),
            ]);

        $this->filterKasir($query, 'transaksi.user_id');

        $data = $query
            ->select(
                'produk.id as produk_id',
                'produk.kode as produk_kode',
                'produk.nama as produk_nama',
                'produk.satuan',
                DB::raw('SUM(transaksi_detail.qty) as total_qty'),
                DB::raw('SUM(transaksi_detail.subtotal) as total_penjualan'),
                DB::raw('COUNT(DISTINCT transaksi_detail.transaksi_id) as total_transaksi'),
                DB::raw('AVG(transaksi_detail.harga) as harga_rata')
            )
            ->groupBy(
                'produk.id',
                'produk.kode',
                'produk.nama',
                'produk.satuan'
            )
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();

        return [
            'periode' => [
                'dari' => $dari,
                'sampai' => $sampai,
            ],
            'ringkasan' => [
                'total_produk_terjual' => $data->count(),
                'total_qty' => (int) $data->sum('total_qty'),
                'total_penjualan' => (float) $data->sum('total_penjualan'),
            ],
            'detail' => $data,
        ];
    }

    /**
     * Laporan per kategori.
     */
    public function perKategori(
        $dari = null,
        $sampai = null,
        $keyword = null,
        $page = 1,
        $limit = 15
    ) {
        $query = DB::table('transaksi_detail as td')
            ->join('produk as p', 'p.id', '=', 'td.produk_id')
            ->join('kategori as k', 'k.id', '=', 'p.kategori_id')
            ->join('transaksi as t', 't.id', '=', 'td.transaksi_id')
            ->where('t.status', 'selesai');

        $this->filterKasir($query, 't.user_id');

        if ($dari && $sampai) {
            $query->whereBetween('t.created_at', [
                Carbon::parse($dari)->startOfDay(),
                Carbon::parse($sampai)->endOfDay(),
            ]);
        }

        if ($keyword) {
            $query->where('k.nama', 'like', "%{$keyword}%");
        }

        $query->groupBy('k.id', 'k.nama')
            ->select(
                'k.nama as kategori',
                DB::raw('COUNT(DISTINCT p.id) as total_produk'),
                DB::raw('SUM(td.qty) as total_terjual'),
                DB::raw('COUNT(DISTINCT t.id) as total_transaksi'),
                DB::raw('SUM(td.subtotal) as total_omzet')
            );

        $total = DB::query()
            ->fromSub(clone $query, 'kategori_laporan')
            ->count();

        $items = $query
            ->orderByDesc('total_omzet')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return [
            'data' => $items,
            'total' => $total,
            'summary' => [
                'total_kategori' => $items->count(),
                'total_produk' => $items->sum('total_produk'),
                'total_terjual' => $items->sum('total_terjual'),
                'total_omzet' => $items->sum('total_omzet'),
            ],
        ];
    }

    /**
     * Laporan laba rugi.
     *
     * Penjualan bersih = subtotal - diskon.
     * HPP = qty x harga beli produk saat laporan dibuat.
     * Pajak penjualan ditampilkan terpisah dan tidak dianggap pendapatan.
     *
     * Catatan: beban operasional belum tersedia pada skema saat ini.
     */
    public function labaRugi($dari, $sampai)
    {
        $periodeMulai = Carbon::parse($dari)->startOfDay();
        $periodeSampai = Carbon::parse($sampai)->endOfDay();

        // Hanya transaksi selesai yang masuk laporan laba rugi.
        $transaksi = Transaksi::selesai()
            ->whereBetween('created_at', [
                $periodeMulai,
                $periodeSampai,
            ]);

        // Kasir hanya dapat melihat transaksi miliknya sendiri.
        $this->filterKasir($transaksi);

        $penjualanKotor = (float) (clone $transaksi)->sum('subtotal');
        $diskon = (float) (clone $transaksi)->sum('diskon');
        $pajak = (float) (clone $transaksi)->sum('pajak');
        $totalPembayaran = (float) (clone $transaksi)->sum('total');
        $jumlahTransaksi = (int) (clone $transaksi)->count();

        // Diskon mengurangi pendapatan, sedangkan pajak tidak ditambahkan
        // ke pendapatan apabila pajak dipungut dari pelanggan.
        // $penjualanBersih = max(0, $penjualanKotor - $diskon);
        $penjualanBersih = $penjualanKotor - $diskon;

        // HPP dihitung dari qty detail x harga beli produk.
        // Catatan: ini memakai harga_beli produk saat laporan dibuat,
        // bukan harga beli historis pada tanggal transaksi.
        $hppQuery = TransaksiDetail::query()
            ->join(
                'transaksi',
                'transaksi.id',
                '=',
                'transaksi_detail.transaksi_id'
            )
            ->join(
                'produk',
                'produk.id',
                '=',
                'transaksi_detail.produk_id'
            )
            ->where('transaksi.status', 'selesai')
            ->whereBetween('transaksi.created_at', [
                $periodeMulai,
                $periodeSampai,
            ]);

        $this->filterKasir($hppQuery, 'transaksi.user_id');

        $hpp = (float) $hppQuery
            ->selectRaw('
                COALESCE(
                    SUM(
                        transaksi_detail.qty *
                        COALESCE(produk.harga_beli, 0)
                    ),
                    0
                ) as total_hpp
            ')
            ->value('total_hpp');

        $labaKotor = $penjualanBersih - $hpp;

        // Belum ada tabel/pencatatan beban operasional dan beban lain.
        // Jangan mengarang nominal beban yang belum dicatat.
        $bebanOperasional = 0;
        $bebanLain = 0;
        $totalBeban = $hpp + $bebanOperasional + $bebanLain;

        // Nilai ini sama dengan laba kotor untuk sementara karena
        // beban operasional dan beban lain belum dicatat.
        // $labaSebelumBebanOperasional =
        //     $penjualanBersih - $totalBeban;
        $hasilUsahaSebelumBebanOperasional =
            $labaKotor - $bebanOperasional - $bebanLain;

        return [
            'periode' => [
                'dari' => $dari,
                'sampai' => $sampai,
            ],

            'pendapatan' => [
                'penjualan_kotor' => $penjualanKotor,
                'diskon' => $diskon,
                'penjualan' => $penjualanBersih,
                'pendapatan_lain' => 0,
                'total' => $penjualanBersih,
            ],

            'pajak' => [
                'penjualan' => $pajak,
            ],

            'pembayaran' => [
                'jumlah_transaksi' => $jumlahTransaksi,
                'total_pembayaran' => $totalPembayaran,
            ],

            'beban' => [
                'hpp' => $hpp,
                'operasional' => $bebanOperasional,
                'beban_lain' => $bebanLain,
                'total' => $totalBeban,
            ],

            'laba_kotor' => $labaKotor,

            'hasil_usaha_sebelum_beban_operasional' =>
                $hasilUsahaSebelumBebanOperasional,

            'catatan' => 'Hasil usaha belum merupakan laba bersih final karena beban operasional, beban lain, dan pajak penghasilan belum dicatat. HPP menggunakan harga beli produk saat laporan dibuat.',
        ];
    }

    /**
     * Laporan transaksi / daftar struk.
     */
    public function transaksi(
        $dari,
        $sampai,
        $page = 1,
        $limit = 20,
        $userId = null
    ) {
        /*
         * Jangan mempercayai user_id dari frontend untuk kasir.
         */
        $userId = $this->effectiveUserId($userId);

        $periodeMulai = Carbon::parse($dari)->startOfDay();
        $periodeSampai = Carbon::parse($sampai)->endOfDay();

        $ringkasanQuery = Transaksi::selesai()
            ->whereBetween('created_at', [
                $periodeMulai,
                $periodeSampai,
            ]);

        $this->filterKasir($ringkasanQuery);

        if ($userId !== null && $userId !== '') {
            $ringkasanQuery->where('user_id', $userId);
        }

        $ringkasan = [
            'total_transaksi' => (int) (clone $ringkasanQuery)->count(),
            'total_penjualan' => (float) (clone $ringkasanQuery)->sum('total'),
            'total_tunai' => (float) (clone $ringkasanQuery)
                ->where('metode_pembayaran', 'cash')
                ->sum('total'),
            'total_non_tunai' => (float) (clone $ringkasanQuery)
                ->where('metode_pembayaran', '!=', 'cash')
                ->sum('total'),
            'total_diskon' => (float) (clone $ringkasanQuery)->sum('diskon'),
            'total_pajak' => (float) (clone $ringkasanQuery)->sum('pajak'),
        ];

        $query = Transaksi::query()
            ->with('kasir:id,name,email')
            ->whereBetween('created_at', [
                $periodeMulai,
                $periodeSampai,
            ]);

        $this->filterKasir($query);

        if ($userId !== null && $userId !== '') {
            $query->where('user_id', $userId);
        }

        $paginator = $query
            ->orderByDesc('created_at')
            ->paginate($limit, ['*'], 'page', $page);

        $transaksiIds = $paginator
            ->getCollection()
            ->pluck('id')
            ->values();

        $details = collect();

        if ($transaksiIds->isNotEmpty()) {
            $details = TransaksiDetail::query()
                ->join(
                    'produk',
                    'produk.id',
                    '=',
                    'transaksi_detail.produk_id'
                )
                ->whereIn('transaksi_detail.transaksi_id', $transaksiIds)
                ->select(
                    'transaksi_detail.id',
                    'transaksi_detail.transaksi_id',
                    'transaksi_detail.produk_id',
                    'transaksi_detail.qty',
                    'transaksi_detail.harga',
                    'transaksi_detail.subtotal',
                    'produk.kode as produk_kode',
                    'produk.nama as produk_nama',
                    'produk.satuan'
                )
                ->orderBy('transaksi_detail.id')
                ->get()
                ->groupBy('transaksi_id');
        }

        $data = $paginator
            ->getCollection()
            ->map(function ($transaksi) use ($details) {
                $items = $details
                    ->get($transaksi->id, collect())
                    ->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'produk_id' => $detail->produk_id,
                            'kode' => $detail->produk_kode,
                            'nama' => $detail->produk_nama,
                            'satuan' => $detail->satuan,
                            'qty' => (int) $detail->qty,
                            'harga' => (float) $detail->harga,
                            'subtotal' => (float) $detail->subtotal,
                        ];
                    })
                    ->values();

                return [
                    'id' => $transaksi->id,
                    'kode' => $transaksi->kode,
                    'tanggal' => $transaksi->created_at,
                    'user_id' => $transaksi->user_id,
                    'username' => $transaksi->kasir
                        ? $transaksi->kasir->email
                        : null,
                    'nama_kasir' => $transaksi->kasir
                        ? $transaksi->kasir->name
                        : null,

                    // Status transaksi
                    'status' => $transaksi->status,

                    'subtotal' => (float) $transaksi->subtotal,
                    'diskon' => (float) $transaksi->diskon,
                    'pajak' => (float) $transaksi->pajak,
                    'total' => (float) $transaksi->total,
                    'bayar' => (float) $transaksi->bayar,
                    'kembalian' => (float) (
                        $transaksi->bayar - $transaksi->total
                    ),
                    'metode_pembayaran' => $transaksi->metode_pembayaran,
                    'catatan' => $transaksi->catatan,
                    'items' => $items,
                ];
            });

        $paginator->setCollection($data);

        return [
            'periode' => [
                'dari' => $dari,
                'sampai' => $sampai,
            ],
            'filter' => [
                'user_id' => $userId,
            ],
            'ringkasan' => $ringkasan,
            'data' => $paginator,
        ];
    }

    /**
     * Laporan stok produk.
     *
     * Inventaris bersifat global dan tidak memiliki user_id.
     */
    public function stok(
        $keyword = null,
        $kategoriId = null,
        $page = 1,
        $limit = 20
    ) {
        $baseQuery = Produk::query()
            ->where('is_active', true);

        if ($keyword) {
            $baseQuery->where(function ($query) use ($keyword) {
                $query->where('kode', 'like', '%' . $keyword . '%')
                    ->orWhere('nama', 'like', '%' . $keyword . '%');
            });
        }

        if ($kategoriId) {
            $baseQuery->where('kategori_id', $kategoriId);
        }

        $ringkasan = [
            'total_produk' => (int) (clone $baseQuery)->count(),
            'total_stok' => (float) (clone $baseQuery)->sum('stok'),
            'stok_menipis' => (int) (clone $baseQuery)
                ->where('stok', '>', 0)
                ->whereColumn('stok', '<=', 'stok_minimal')
                ->count(),
            'stok_habis' => (int) (clone $baseQuery)
                ->where('stok', '<=', 0)
                ->count(),
        ];

        $paginator = (clone $baseQuery)
            ->with('kategori')
            ->orderBy('nama')
            ->paginate($limit, ['*'], 'page', $page);

        $data = $paginator
            ->getCollection()
            ->map(function ($produk) {
                return [
                    'id' => $produk->id,
                    'kode' => $produk->kode,
                    'nama' => $produk->nama,
                    'kategori_id' => $produk->kategori_id,
                    'kategori_nama' => $produk->kategori
                        ? $produk->kategori->nama
                        : null,
                    'satuan' => $produk->satuan,
                    'harga_beli' => (float) $produk->harga_beli,
                    'harga_jual' => (float) $produk->harga_jual,
                    'stok' => (float) $produk->stok,
                    'stok_minimal' => (float) $produk->stok_minimal,
                    'gambar_url' => $produk->gambar_url,
                    'gambar' => $produk->gambar,
                ];
            });

        $paginator->setCollection($data);

        return [
            'ringkasan' => $ringkasan,
            'data' => $paginator,
        ];
    }

    /**
     * Ringkasan dashboard hari ini.
     */
    public function dashboardHariIni()
    {
        $hariIni = Carbon::today();

        $transaksi = Transaksi::selesai()
            ->whereDate('created_at', $hariIni);

        $this->filterKasir($transaksi);

        $totalProdukQuery = TransaksiDetail::query()
            ->join(
                'transaksi',
                'transaksi.id',
                '=',
                'transaksi_detail.transaksi_id'
            )
            ->where('transaksi.status', 'selesai')
            ->whereDate('transaksi.created_at', $hariIni);

        $this->filterKasir($totalProdukQuery, 'transaksi.user_id');

        $totalProdukTerjual = (int) $totalProdukQuery
            ->sum('transaksi_detail.qty');

        $perKasirQuery = Transaksi::selesai()
            ->with('kasir:id,name,email')
            ->whereDate('created_at', $hariIni);

        $this->filterKasir($perKasirQuery);

        $perKasir = $perKasirQuery
            ->get()
            ->groupBy('user_id')
            ->map(function ($transaksiKasir) {
                $transaksiPertama = $transaksiKasir->first();
                $kasir = $transaksiPertama->kasir;

                return [
                    'user_id' => $transaksiPertama->user_id,
                    'username' => $kasir ? $kasir->email : null,
                    'nama_kasir' => $kasir ? $kasir->name : null,
                    'total_transaksi' => $transaksiKasir->count(),
                    'total_penjualan' => (float) $transaksiKasir->sum('total'),
                    'total_tunai' => (float) $transaksiKasir
                        ->where('metode_pembayaran', 'cash')
                        ->sum('total'),
                    'total_non_tunai' => (float) $transaksiKasir
                        ->where('metode_pembayaran', '!=', 'cash')
                        ->sum('total'),
                ];
            })
            ->values();

        return [
            'tanggal' => $hariIni->format('Y-m-d'),
            'total_transaksi' => (int) (clone $transaksi)->count(),
            'total_penjualan' => (float) (clone $transaksi)->sum('total'),
            'total_tunai' => (float) (clone $transaksi)
                ->where('metode_pembayaran', 'cash')
                ->sum('total'),
            'total_non_tunai' => (float) (clone $transaksi)
                ->where('metode_pembayaran', '!=', 'cash')
                ->sum('total'),
            'total_produk_terjual' => $totalProdukTerjual,
            'per_kasir' => $perKasir,
        ];
    }

    /**
     * Daftar kasir untuk filter laporan.
     *
     * Kasir hanya memperoleh data dirinya sendiri.
     */
    public function daftarKasir()
    {
        $query = User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name');

        $user = $this->currentUser();

        if ($user && $user->isKasir()) {
            $query->where('id', $user->id);
        }

        return $query
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'label' => $user->name . ' (' . $user->email . ')',
                ];
            })
            ->values();
    }
}