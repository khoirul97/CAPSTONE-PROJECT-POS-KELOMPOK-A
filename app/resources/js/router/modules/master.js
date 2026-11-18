import Layout from '@/layout';

const masterRoutes = [
  {
    path: '/master',
    component: Layout,
    redirect: '/master/kategori',
    name: 'Master',

    meta: {
      title: 'Master Produk',
      icon: 'list',
    },

    children: [
      {
        path: 'kategori',
        component: () => import('@/views/master/kategori/index'),
        name: 'Kategori',

        meta: {
          title: 'Kategori',
          icon: 'el-icon-collection-tag',
          permission: 'view menu kategori',
          managePermission: 'manage kategori',
        },
      },

      {
        path: 'produk',
        component: () => import('@/views/master/produk/index'),
        name: 'Produk',

        meta: {
          title: 'Produk',
          icon: 'el-icon-goods',
          permission: 'view menu produk',
          managePermission: 'manage produk',
        },
      },

      {
        path: 'stok',
        component: () => import('@/views/master/stok/index'),
        name: 'Stok',

        meta: {
          title: 'Stok',
          icon: 'el-icon-box',
          permission: 'view menu stok',
          managePermission: 'manage stok',
        },
      },

      {
        path: 'pengaturan',
        component: () => import('@/views/master/pengaturan/index'),
        name: 'Pengaturan',

        meta: {
          title: 'Pengaturan',
          icon: 'el-icon-setting',
          permission: 'view menu pengaturan',
          managePermission: 'manage pengaturan',
        },
      },
    ],
  },
];

export default masterRoutes;
