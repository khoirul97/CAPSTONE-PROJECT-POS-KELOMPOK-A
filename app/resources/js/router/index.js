import Vue from 'vue';
import Router from 'vue-router';

/**
 * Layzloading will create many files and slow on compiling, so best not to use lazyloading on devlopment.
 * The syntax is lazyloading, but we convert to proper require() with babel-plugin-syntax-dynamic-import
 * @see https://doc.laravue.dev/guide/advanced/lazy-loading.html
 */

Vue.use(Router);

/* Layout */
import Layout from '@/layout';

/* Router for modules */
import adminRoutes from './modules/admin';
import kasirRoutes from './modules/kasir';
import masterRoutes from './modules/master';

/**
 * Sub-menu only appear when children.length>=1
 * @see https://doc.laravue.dev/guide/essentials/router-and-nav.html
 */

/**
 * hidden: true
 * alwaysShow: true
 * redirect: noredirect
 * name: 'router-name'
 * meta:
 *   roles
 *   permissions
 *   title
 *   icon
 *   noCache
 *   breadcrumb
 *   affix
 */

export const constantRoutes = [

  /*
  |--------------------------------------------------------------------------
  | REDIRECT
  |--------------------------------------------------------------------------
  */

  {
    path: '/redirect',

    component: Layout,

    hidden: true,

    children: [
      {
        path: '/redirect/:path*',

        component: () => import('@/views/redirect/index'),
      },
    ],
  },

  /*
  |--------------------------------------------------------------------------
  | LOGIN
  |--------------------------------------------------------------------------
  */

  {
    path: '/login',

    component: () => import('@/views/login/index'),

    hidden: true,
  },

  /*
  |--------------------------------------------------------------------------
  | AUTH REDIRECT
  |--------------------------------------------------------------------------
  */

  {
    path: '/auth-redirect',

    component: () => import('@/views/login/AuthRedirect'),

    hidden: true,
  },

  /*
  |--------------------------------------------------------------------------
  | 404
  |--------------------------------------------------------------------------
  */

  {
    path: '/404',

    redirect: {
      name: 'Page404',
    },

    component: () => import('@/views/error-page/404'),

    hidden: true,
  },

  /*
  |--------------------------------------------------------------------------
  | 401
  |--------------------------------------------------------------------------
  */

  {
    path: '/401',

    component: () => import('@/views/error-page/401'),

    hidden: true,
  },

  /*
  |--------------------------------------------------------------------------
  | DASHBOARD
  |--------------------------------------------------------------------------
  */

  {
    path: '',

    component: Layout,

    redirect: 'dashboard',

    children: [
      {
        path: 'dashboard',

        component: () => import('@/views/dashboard/index'),

        name: 'Dashboard',

        meta: {
          title: 'dashboard',
          icon: 'component',
          noCache: false,
        },
      },
    ],
  },

  /*
  |--------------------------------------------------------------------------
  | PROFILE
  |--------------------------------------------------------------------------
  */

  {
    path: '/profile',

    component: Layout,

    redirect: '/profile/edit',

    hidden: true,

    children: [
      {
        path: 'edit',

        component: () => import('@/views/users/SelfProfile'),

        name: 'SelfProfile',

        meta: {
          title: 'userProfile',
          icon: 'user',
          noCache: true,
        },
      },
    ],
  },
];

/*
|--------------------------------------------------------------------------
| ASYNC ROUTES
|--------------------------------------------------------------------------
*/

export const asyncRoutes = [

  // permissionRoutes,

  // adminContentRoutes,

  ...kasirRoutes,

  ...masterRoutes,

  adminRoutes,

  {
    path: '*',

    name: 'Page404',

    redirect: '/404',

    hidden: true,
  },
];

/*
|--------------------------------------------------------------------------
| CREATE ROUTER
|--------------------------------------------------------------------------
*/

const createRouter = () => new Router({

  // mode: 'history', // require service support

  scrollBehavior: () => ({
    y: 0,
  }),

  base: process.env.MIX_LARAVUE_PATH,

  routes: constantRoutes,
});

/*
|--------------------------------------------------------------------------
| ROUTER INSTANCE
|--------------------------------------------------------------------------
*/

const router = createRouter();

/*
|--------------------------------------------------------------------------
| RESET ROUTER
|--------------------------------------------------------------------------
*/

// Detail see:
// https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465

export function resetRouter() {
  const newRouter = createRouter();

  router.matcher = newRouter.matcher;
}

/*
|--------------------------------------------------------------------------
| EXPORT
|--------------------------------------------------------------------------
*/

export default router;
