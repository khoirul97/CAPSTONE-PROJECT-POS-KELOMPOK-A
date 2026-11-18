import Layout from '@/layout';

const adminRoutes = {
  path: '/administrator',
  component: Layout,
  redirect: '/administrator/users',
  name: 'Administrator',
  alwaysShow: true,

  meta: {
    title: 'administrator',
    icon: 'admin',
    permission: 'view menu administrator',
    roles: ['admin'],
  },

  children: [
    {
      path: 'users',
      component: () => import('@/views/users/List'),
      name: 'UserList',

      meta: {
        title: 'users',
        icon: 'user',
        permission: 'view menu administrator',
        managePermission: 'manage user',
      },
    },

    {
      path: 'users/edit/:id(\\d+)',
      component: () => import('@/views/users/UserProfile'),
      name: 'UserProfile',

      hidden: true,

      meta: {
        title: 'userProfile',
        noCache: true,
        permission: 'view menu administrator',
        managePermission: 'manage user',
        activeMenu: '/administrator/users',
      },
    },

    {
      path: 'active-users',
      component: () => import('@/views/administration/ActiveUsers.vue'),
      name: 'ActiveUsers',

      meta: {
        title: 'activeUsers',
        icon: 'peoples',
        permission: 'view menu administrator',
        managePermission: 'manage user',
      },
    },

    {
      path: 'roles',
      component: () => import('@/views/role-permission/List'),
      name: 'RoleList',

      meta: {
        title: 'rolePermission',
        icon: 'role',
        permission: 'view menu permission',
        managePermission: 'manage permission',
      },
    },

    {
      path: 'logs',
      component: () => import('@/views/logs/index'),
      name: 'LogList',

      meta: {
        title: 'logApp',
        icon: 'documentation',
        permission: 'view menu permission',
        managePermission: 'manage permission',
      },
    },
  ],
};

export default adminRoutes;
