import { asyncRoutes, constantRoutes } from '@/router';

/**
 * ============================================================
 * CHECK ROUTE ACCESS
 * ============================================================
 *
 * Mendukung:
 *
 * meta.permission  -> string / array
 * meta.permissions -> string / array
 * meta.roles       -> string / array
 *
 * Aturan:
 * 1. Jika route memiliki roles, user harus memiliki salah satu role.
 * 2. Jika route memiliki permission, user harus memiliki salah satu
 *    permission.
 * 3. Jika route memiliki roles DAN permission, keduanya harus terpenuhi.
 * 4. Jika route tidak memiliki restriction, route boleh diakses.
 *
 * @param {Array} roles
 * @param {Array} permissions
 * @param {Object} route
 * @returns {Boolean}
 */
function canAccess(roles = [], permissions = [], route) {
  const meta = route.meta || {};

  const userRoles = Array.isArray(roles) ? roles : [];
  const userPermissions = Array.isArray(permissions)
    ? permissions
    : [];

  // ==========================================================
  // ROLE
  // ==========================================================

  let hasRole = true;

  if (meta.roles) {
    const requiredRoles = Array.isArray(meta.roles)
      ? meta.roles
      : [meta.roles];

    hasRole = requiredRoles.some(role =>
      userRoles.includes(role)
    );
  }

  // ==========================================================
  // PERMISSION
  // ==========================================================

  let hasPermission = true;

  /**
   * Support:
   *
   * meta.permission
   * meta.permissions
   *
   * supaya kompatibel dengan route lama maupun route baru.
   */
  const routePermissions = meta.permission || meta.permissions;

  if (routePermissions) {
    const requiredPermissions = Array.isArray(routePermissions)
      ? routePermissions
      : [routePermissions];

    hasPermission = requiredPermissions.some(permission =>
      userPermissions.includes(permission)
    );
  }

  // ==========================================================
  // HAS RESTRICTION
  // ==========================================================

  const hasRoleRestriction = !!meta.roles;
  const hasPermissionRestriction = !!routePermissions;

  // Tidak mempunyai restriction
  if (!hasRoleRestriction && !hasPermissionRestriction) {
    return true;
  }

  // Jika hanya role
  if (hasRoleRestriction && !hasPermissionRestriction) {
    return hasRole;
  }

  // Jika hanya permission
  if (!hasRoleRestriction && hasPermissionRestriction) {
    return hasPermission;
  }

  // Jika memiliki role DAN permission,
  // keduanya wajib terpenuhi.
  return hasRole && hasPermission;
}

/**
 * ============================================================
 * FILTER ASYNC ROUTES
 * ============================================================
 *
 * Filtering dilakukan secara recursive.
 *
 * Contoh:
 *
 * /admin
 *   └── /content
 *        ├── produk
 *        ├── berita
 *        └── gallery
 *
 * Jika petugas hanya punya:
 *
 * view menu produk
 * view menu berita
 *
 * maka hasilnya:
 *
 * /admin
 *   └── /content
 *        ├── produk
 *        └── berita
 *
 * gallery otomatis dibuang.
 *
 * Jika seluruh child tidak boleh diakses,
 * parent/group juga dibuang.
 *
 * @param {Array} routes
 * @param {Array} roles
 * @param {Array} permissions
 * @returns {Array}
 */
function filterAsyncRoutes(routes, roles, permissions) {
  const res = [];

  routes.forEach(route => {
    const tmp = { ...route };

    // ========================================================
    // 1. CHECK ACCESS ROUTE ITSELF
    // ========================================================

    if (!canAccess(roles, permissions, tmp)) {
      return;
    }

    // ========================================================
    // 2. FILTER CHILDREN
    // ========================================================

    if (tmp.children && tmp.children.length > 0) {
      tmp.children = filterAsyncRoutes(
        tmp.children,
        roles,
        permissions
      );

      // ======================================================
      // 3. JIKA SEMUA CHILD HILANG
      // ======================================================
      //
      // Route parent/group seperti:
      //
      // /admin
      // /admin/content
      //
      // tidak boleh tetap tampil kalau tidak mempunyai
      // child yang dapat diakses.
      //
      // Tetapi route leaf tanpa child tetap dipertahankan.
      //

      if (tmp.children.length === 0) {
        return;
      }
    }

    // ========================================================
    // 4. ROUTE LOLOS
    // ========================================================

    res.push(tmp);
  });

  return res;
}

const state = {
  routes: [],
  addRoutes: [],
};

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes;
    state.routes = constantRoutes.concat(routes);
  },
};

const actions = {
  generateRoutes({ commit }, { roles = [], permissions = [] }) {
    return new Promise(resolve => {
      let accessedRoutes = [];

      // ======================================================
      // ADMIN
      // ======================================================
      //
      // Admin tetap mendapatkan seluruh asyncRoutes.
      //
      if (roles.includes('admin')) {
        accessedRoutes = asyncRoutes || [];
      } else {
        // ====================================================
        // PETUGAS / ROLE LAIN
        // ====================================================

        accessedRoutes = filterAsyncRoutes(
          asyncRoutes || [],
          roles,
          permissions
        );
      }

      commit('SET_ROUTES', accessedRoutes);

      resolve(accessedRoutes);
    });
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
