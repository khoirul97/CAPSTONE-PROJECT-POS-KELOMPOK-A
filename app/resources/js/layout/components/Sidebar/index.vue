<template>
  <div :class="{'has-logo':showLogo}" class="sidebar-inner">
    <logo :collapse="isCollapse" />
    <Username :collapse="isCollapse" />
    <el-scrollbar wrap-class="scrollbar-wrapper">
      <el-menu
        :show-timeout="200"
        :default-active="activeMenu"
        :default-openeds="openeds"
        :collapse="isCollapse"
        background-color="#304156"
        text-color="#bfcbd9"
        active-text-color="#409EFF"
        mode="vertical"
      >
        <sidebar-item
          v-for="route in routes"
          :key="route.path"
          :item="route"
          :base-path="route.path"
        />
      </el-menu>
    </el-scrollbar>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import SidebarItem from './SidebarItem';
import Logo from './Logo';
import Username from './Username';
import variables from '@/styles/variables.scss';

export default {
  components: { SidebarItem, Logo, Username },
  computed: {
    ...mapGetters(['sidebar', 'permission_routers']),
    routes() {
      return this.$store.state.permission.routes;
    },
    showLogo() {
      return this.$store.state.settings.sidebarLogo;
    },
    variables() {
      return variables;
    },
    isCollapse() {
      return !this.sidebar.opened;
    },
    activeMenu() {
      const { meta, path } = this.$route;
      return meta && meta.activeMenu ? meta.activeMenu : path;
    },
    openeds() {
      return this.getOpeneds(this.routes);
    },
  },
  methods: {
    getOpeneds(routes, basePath = '') {
      let res = [];

      routes.forEach(route => {
        if (route.children && route.children.length > 0) {
          const fullPath = basePath
            ? `${basePath}/${route.path}`.replace(/\/+/g, '/')
            : route.path;

          res.push(fullPath);
          res = res.concat(this.getOpeneds(route.children, fullPath));
        }
      });

      return res;
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@/styles/variables.scss';

/* hanya isi dalam sidebar */
.sidebar-inner {
  height: 100%;
}

.sidebar-inner {
  height: 100%;
  display: flex;
  flex-direction: column;
}

/* el-scrollbar jadi area utama */
.sidebar-inner .el-scrollbar {
  flex: 1;
  height: 0; // ⬅️ WAJIB (trik flex biar scroll jalan)
}

/* wrapper scroll dari Element UI */
.sidebar-inner .scrollbar-wrapper {
  overflow-y: auto;
  overflow-x: hidden;
  height: 100%;
}

/* optional biar menu full */
.el-menu {
  height: 100%;
  border-right: none;
}
</style>
