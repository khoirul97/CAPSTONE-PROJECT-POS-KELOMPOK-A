<template>
  <div v-if="!item.hidden && item.children" class="menu-wrapper">

    <template v-if="oneChildResult.show && oneChildResult.child && (!oneChildResult.child.children || oneChildResult.child.noShowingChildren) && !item.alwaysShow">
      <app-link :to="resolvePath(oneChildResult.child.path)">
        <el-menu-item
          :index="resolvePath(oneChildResult.child.path)"
          :class="{ 'submenu-title-noDropdown': !isNest }"
        >
          <item
            v-if="oneChildResult.child.meta"
            :icon="oneChildResult.child.meta.icon || item.meta.icon"
            :title="generateTitle(oneChildResult.child.meta.title)"
          />

          <span v-if="!isCollapse" class="badge-container">
            <span v-if="showDispoBadge(oneChildResult.child) && unreadDispoCount > 0" class="badge dispo">
              {{ unreadDispoCount > 99 ? '99+' : unreadDispoCount }}
            </span>
            <span v-if="showCcBadge(oneChildResult.child) && unreadCcCount > 0" class="badge cc">
              {{ unreadCcCount > 99 ? '99+' : unreadCcCount }}
            </span>
          </span>
        </el-menu-item>
      </app-link>
    </template>

    <el-submenu v-else :index="resolvePath(item.path)">
      <template slot="title">
        <item
          v-if="item.meta"
          :icon="item.meta.icon"
          :title="generateTitle(item.meta.title)"
        />
        <span v-if="!isCollapse" class="badge-container">
          <span v-if="showDispoBadge(item) && unreadDispoCount > 0" class="badge dispo">
            {{ unreadDispoCount > 99 ? '99+' : unreadDispoCount }}
          </span>
          <span v-if="showCcBadge(item) && unreadCcCount > 0" class="badge cc">
            {{ unreadCcCount > 99 ? '99+' : unreadCcCount }}
          </span>
        </span>
      </template>

      <template v-for="child in visibleChildren">
        <sidebar-item
          v-if="child.children && child.children.length > 0"
          :key="child.path"
          :is-nest="true"
          :item="child"
          :base-path="resolvePath(child.path)"
          class="nest-menu"
        />

        <app-link v-else :key="child.name" :to="resolvePath(child.path)">
          <el-menu-item :index="resolvePath(child.path)">
            <item
              v-if="child.meta"
              :icon="child.meta.icon"
              :title="generateTitle(child.meta.title)"
            />
            <span v-if="!isCollapse" class="badge-container">
              <span v-if="showDispoBadge(child) && unreadDispoCount > 0" class="badge dispo">
                {{ unreadDispoCount > 99 ? '99+' : unreadDispoCount }}
              </span>
              <span v-if="showCcBadge(child) && unreadCcCount > 0" class="badge cc">
                {{ unreadCcCount > 99 ? '99+' : unreadCcCount }}
              </span>
            </span>
          </el-menu-item>
        </app-link>
      </template>
    </el-submenu>

  </div>
</template>

<script>
import path from 'path';
import { isExternal } from '@/utils/validate';
import Item from './Item';
import AppLink from './Link';
import { generateTitle } from '@/utils/i18n';

export default {
  name: 'SidebarItem',
  components: { Item, AppLink },
  props: {
    item: { type: Object, required: true },
    isNest: { type: Boolean, default: false },
    basePath: { type: String, default: '' },
  },
  computed: {
    visibleChildren() {
      return (this.item.children || []).filter(child => !child.hidden);
    },
    unreadDispoCount() {
      return this.$store.state.notification?.unreadCount || 0;
    },
    unreadCcCount() {
      return this.$store.state.notification?.unreadCcCount || 0;
    },
    isCollapse() {
      return !this.$store.getters.sidebar?.opened;
    },
    oneChildResult() {
      const children = this.item.children || [];
      const showingChildren = children.filter(i => !i.hidden);
      if (showingChildren.length === 1) {
        return { show: true, child: showingChildren[0] };
      }
      if (showingChildren.length === 0) {
        return {
          show: true,
          child: { ...this.item, path: '', noShowingChildren: true },
        };
      }
      return { show: false, child: null };
    },
  },
  methods: {
    resolvePath(routePath) {
      if (isExternal(routePath)) {
        return routePath;
      }
      return path.resolve(this.basePath, routePath);
    },
    generateTitle,
    showDispoBadge(menu) {
      const roles = this.$store.getters.roles || [];
      if (roles.includes('admin')) {
        return false;
      }
      return menu.meta && menu.meta.badgeType === 'dispo';
    },
    showCcBadge(menu) {
      return menu.meta && menu.meta.badgeType === 'cc';
    },
  },
};
</script>

<style scoped>
/* ✅ BADGE POSITIONING (TIDAK MERUSAK FLEX ASLI) */
.badge-container {
  position: absolute;
  right: 15px; /* Sesuaikan jarak dari kanan */
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  gap: 4px;
  pointer-events: none; /* Supaya tidak mengganggu klik menu */
}

.badge {
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: bold;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.badge.dispo { background-color: #f56c6c; }
.badge.cc { background-color: #f56c6c; }
</style>

<style>
/* ✅ FIX UNTUK ELEMENT UI COLLAPSE MODE */
.el-menu-item, .el-submenu__title {
  position: relative !important; /* Wajib agar position: absolute badge berfungsi */
}

/* Style Asli Anda */
.el-menu-item.is-active {
  color: #1890ff !important;
  font-weight: bold !important;
}

#app .sidebar-container .el-submenu .el-menu-item.is-active {
  color: #1890ff !important;
  font-weight: bold;
}

#app .sidebar-container .el-submenu .el-menu-item:hover {
  background-color: #263445;
}

.el-submenu__title i {
    color: #000 !important;
    font-weight: bold !important;
}

.el-menu--vertical .nest-menu .el-submenu > .el-submenu__title:hover,
.el-menu--vertical .el-menu-item:hover {
    color: #233f82 !important;
    font-weight: bold;
    background-color: #263445;
}
</style>
