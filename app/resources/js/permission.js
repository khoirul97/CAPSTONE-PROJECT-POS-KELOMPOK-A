import router from './router';
import store from './store';
import { Message } from 'element-ui';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
import { isLogged } from '@/utils/auth';
import getPageTitle from '@/utils/get-page-title';

NProgress.configure({ showSpinner: false });

// 🔥 whitelist pakai prefix (bukan exact match)
const whiteList = ['/login', '/auth-redirect'];

// 🔥 helper untuk cek whitelist + dynamic route
function isWhiteListed(path) {
  // exact match
  if (whiteList.includes(path)) {
    return true;
  }

  // // 🔥 allow verify-document dengan token
  // if (path.startsWith('/verify-document')) {
  //   return true;
  // }

  return false;
}

router.beforeEach(async(to, from, next) => {
  NProgress.start();

  document.title = getPageTitle(to.meta.title);

  const isUserLogged = isLogged();

  if (isUserLogged) {
    if (to.path === '/login') {
      next({ path: '/', replace: true });
      NProgress.done();
      return;
    }

    const hasRoles = store.getters.roles && store.getters.roles.length > 0;

    if (hasRoles) {
      next();
    } else {
      try {
        const { roles, permissions } = await store.dispatch('user/getInfo');

        const accessRoutes = await store.dispatch('permission/generateRoutes', {
          roles,
          permissions,
        });

        accessRoutes.forEach(route => {
          router.addRoute(route);
        });

        next({ ...to, replace: true });
      } catch (error) {
        await store.dispatch('user/resetToken');
        Message.error(error.message || 'Has Error');
        next(`/login?redirect=${to.path}`);
        NProgress.done();
      }
    }
  } else {
    // 🔥 pakai function, bukan includes langsung
    if (isWhiteListed(to.path)) {
      next();
    } else {
      next(`/login?redirect=${to.path}`);
      NProgress.done();
    }
  }
});

router.afterEach(() => {
  NProgress.done();
});
