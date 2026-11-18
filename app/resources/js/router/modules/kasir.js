import Layout from '@/layout';

const kasirRoutes = [
  // =========================================================
  // KASIR
  // =========================================================

  {
    path: '/kasir',
    component: Layout,
    redirect: '/kasir/open-kas',
    name: 'Kasir',

    meta: {
      title: 'Kasir',
      icon: 'dollar',
      permission: 'view menu sesi kasir',
    },

    children: [
      {
        path: 'open-kas',
        name: 'OpenKas',
        component: () => import('@/views/kasir/OpenKas.vue'),

        meta: {
          title: 'Buka / Tutup Kas',
          icon: 'el-icon-wallet',
          permission: 'view menu sesi kasir',
          managePermission: 'open sesi kasir',
        },
      },

      {
        path: 'transaksi',
        name: 'TransaksiKasir',
        component: () => import('@/views/kasir/TransaksiKasir.vue'),

        meta: {
          title: 'Transaksi',
          icon: 'el-icon-shopping-cart-full',
          permission: 'view menu transaksi',
          managePermission: 'manage transaksi',
        },
      },
    ],
  },

  // =========================================================
  // LAPORAN
  // =========================================================

  {
    path: '/laporan',
    component: Layout,
    name: 'Laporan',

    meta: {
      title: 'Laporan',
      icon: 'chart',
      permission: 'view menu laporan',
    },

    children: [
      {
        path: 'transaksi',
        name: 'LaporanTransaksi',
        component: () => import('@/views/laporan/LaporanTransaksi.vue'),

        meta: {
          title: 'Laporan Transaksi',
          icon: 'el-icon-money',
          permission: 'view menu laporan',
        },
      },

      {
        path: 'harian',
        name: 'LaporanHarian',
        component: () => import('@/views/laporan/LaporanHarian.vue'),

        meta: {
          title: 'Laporan Harian',
          icon: 'el-icon-notebook-1',
          permission: 'view menu laporan',
        },
      },

      {
        path: 'shift',
        name: 'LaporanShift',
        component: () => import('@/views/laporan/LaporanShift.vue'),

        meta: {
          title: 'Laporan Shift Kasir',
          icon: 'el-icon-suitcase',
          permission: 'view menu laporan',
        },
      },

      {
        path: 'produk',
        name: 'LaporanProduk',
        component: () => import('@/views/laporan/LaporanProduk.vue'),

        meta: {
          title: 'Laporan Produk',
          icon: 'el-icon-goods',
          permission: 'view menu laporan',
        },
      },

      {
        path: 'kategori',
        name: 'LaporanKategori',
        component: () => import('@/views/laporan/LaporanKategori.vue'),

        meta: {
          title: 'Laporan Kategori',
          icon: 'el-icon-collection-tag',
          permission: 'view menu laporan',
        },
      },

      {
        path: 'laba-rugi',
        name: 'LaporanLabaRugi',
        component: () => import('@/views/laporan/LaporanLabaRugi.vue'),

        meta: {
          title: 'Laporan Laba Rugi',
          icon: 'el-icon-s-data',
          permission: 'view menu laporan',
        },
      },

      {
        path: 'stok',
        name: 'LaporanStok',
        component: () => import('@/views/laporan/LaporanStok.vue'),

        meta: {
          title: 'Laporan Stok',
          icon: 'el-icon-receiving',
          permission: 'view menu laporan',
        },
      },
    ],
  },
];

export default kasirRoutes;
