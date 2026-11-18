<template>
  <el-breadcrumb class="app-breadcrumb" separator="/">
    <transition-group name="breadcrumb">
      <el-breadcrumb-item
        v-for="(item, index) in levelList"
        :key="item.path"
      >
        <span
          v-if="item.redirect === 'noredirect' || index === levelList.length - 1"
          class="no-redirect"
        >
          {{ generateTitle(item.meta.title) }}
        </span>
        <a v-else @click.prevent="handleLink(item)">
          {{ generateTitle(item.meta.title) }}
        </a>
      </el-breadcrumb-item>
    </transition-group>
  </el-breadcrumb>
</template>

<script>
import { generateTitle } from '@/utils/i18n';
import pathToRegexp from 'path-to-regexp';

export default {
  name: 'Breadcrumb',
  data() {
    return {
      levelList: [],
    };
  },
  watch: {
    $route() {
      this.getBreadcrumb();
    },
  },
  created() {
    this.getBreadcrumb();
  },
  methods: {
    generateTitle,

    // 🔥 helper flatten semua route + fullPath
    getAllRoutes() {
      const res = [];

      const loop = (routes, parentPath = '') => {
        routes.forEach(r => {
          const fullPath = `${parentPath}/${r.path}`.replace(/\/+/g, '/');

          res.push({
            ...r,
            fullPath,
          });

          if (r.children) {
            loop(r.children, fullPath);
          }
        });
      };

      loop(this.$router.options.routes);
      return res;
    },

    getBreadcrumb() {
      let matched = this.$route.matched.filter(item => item.name);

      // tambah dashboard kalau bukan root
      if (
        matched.length &&
        matched[0].name.toLowerCase() !== 'dashboard'
      ) {
        matched = [
          { path: '/dashboard', meta: { title: 'dashboard' }},
          ...matched,
        ];
      }

      // 🔥 FIX: handle activeMenu dengan exact match
      const last = matched[matched.length - 1];

      if (last?.meta?.activeMenu) {
        const parentPath = last.meta.activeMenu;

        const exists = matched.some(m => m.path === parentPath);

        if (!exists) {
          const routes = this.getAllRoutes();

          const parentRoute = routes.find(
            r => r.fullPath === parentPath
          );

          if (parentRoute) {
            matched.splice(1, 0, {
              path: parentRoute.fullPath,
              meta: {
                title: parentRoute.meta?.title || 'Unknown',
              },
            });
          }
        }
      }

      this.levelList = matched.filter(
        item =>
          item.meta &&
          item.meta.title &&
          item.meta.breadcrumb !== false
      );
    },

    pathCompile(path) {
      const { params } = this.$route;
      const toPath = pathToRegexp.compile(path);
      return toPath(params);
    },

    handleLink(item) {
      const { redirect, path } = item;
      if (redirect) {
        this.$router.push(redirect);
        return;
      }
      this.$router.push(this.pathCompile(path));
    },
  },
};
</script>

<style scoped lang="scss">
.app-breadcrumb.el-breadcrumb {
  display: inline-block;
  font-size: 14px;
  line-height: 50px;
  margin-left: 10px;

  .no-redirect {
    color: #97a8be;
    cursor: text;
  }
}
</style>
