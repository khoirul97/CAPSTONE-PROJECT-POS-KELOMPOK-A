<template>
  <div :class="classObj" class="app-wrapper">
    <div
      v-if="device==='mobile' && sidebar.opened"
      class="drawer-bg"
      @click="handleClickOutside"
    />

    <!-- SIDEBAR (CARD KIRI) -->
    <sidebar class="sidebar-container" />

    <!-- MAIN (CARD KANAN) -->
    <div class="main-container">
      <div :class="{'fixed-header': fixedHeader}">
        <navbar />
        <!-- <tags-view v-if="needTagsView" /> -->
      </div>

      <app-main />

      <!-- <right-panel v-if="showSettings">
        <settings />
      </right-panel> -->
    </div>
  </div>
</template>

<script>
import { Navbar, Sidebar, AppMain } from './components';
import ResizeMixin from './mixin/resize-handler.js';
import { mapState } from 'vuex';

export default {
  name: 'Layout',
  components: {
    AppMain,
    Navbar,
    Sidebar,
  },
  mixins: [ResizeMixin],
  computed: {
    ...mapState({
      sidebar: state => state.app.sidebar,
      device: state => state.app.device,
      showSettings: state => state.settings.showSettings,
      needTagsView: state => state.settings.tagsView,
      fixedHeader: state => state.settings.fixedHeader,
    }),
    classObj() {
      return {
        hideSidebar: !this.sidebar.opened,
        openSidebar: this.sidebar.opened,
        withoutAnimation: this.sidebar.withoutAnimation,
        mobile: this.device === 'mobile',
      };
    },
  },
  methods: {
    handleClickOutside() {
      this.$store.dispatch('app/closeSideBar', { withoutAnimation: false });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "~@/styles/mixin.scss";
@import "~@/styles/variables.scss";

/* =========================
   WRAPPER (FLEX LAYOUT)
========================= */
.app-wrapper {
  display: flex;
  height: 100vh;
  width: 100%;
  padding: 7px;
  box-sizing: border-box;

  overflow: hidden; // ⬅️ penting: stop scroll di luar

  &.mobile.openSidebar {
    position: fixed;
    top: 0;
  }
}

/* =========================
   SIDEBAR (CARD KIRI)
========================= */
.sidebar-container {
  width: $sideBarWidth;
  margin-left: 2px;
  margin-right: 2px;
  margin-top: auto;
  margin-bottom: auto;

  border-radius: 12px;
  overflow: hidden;

  box-shadow: 0 13px 15px rgba(0,0,0,0.16),
              0 13px 15px rgba(0,0,0,0.16);
  max-height: 98.2%;
}

/* collapse */
.hideSidebar .sidebar-container {
  width: 54px;
}

/* =========================
   MAIN CONTAINER (CARD KANAN)
========================= */
.main-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  max-height: 99%;

  border-radius: 12px;
  border: 1px solid rgba(0,0,0,0.16);
  overflow: hidden;

  box-shadow: 0 13px 15px rgba(0,0,0,0.16),
              0 13px 15px rgba(0,0,0,0.16);
}

.main-container > *:last-child {
  flex: 1;
  overflow-y: auto; // ⬅️ INI KUNCI SCROLL
}

/* =========================
   HEADER (SUDAH TIDAK FIXED)
========================= */
.fixed-header {
  position: relative; /* ⬅️ penting */
  width: 100%;
  z-index: 9;

  border-bottom: 1px solid #eee;
}

/* =========================
   OVERLAY MOBILE
========================= */
.drawer-bg {
  background: #000;
  opacity: 0.3;
  width: 100%;
  top: 0;
  height: 100%;
  position: absolute;
  z-index: 999;
}

/* =========================
   MOBILE FIX
========================= */
.mobile .sidebar-container {
  position: fixed;
  z-index: 1001;
}

.mobile .main-container {
  margin-left: 0;
}

.sidebar-container {
  display: flex;
  flex-direction: column;
}

.sidebar-container .el-scrollbar {
  flex: 1;
}

.sidebar-container .el-scrollbar {
  flex: 1;
  height: 100%;
}

.sidebar-container .el-scrollbar__wrap {
  overflow-y: auto; // ⬅️ SCROLL SIDEBAR
  overflow-x: hidden;
}

.main-container > *:last-child {
  flex: 1;
  overflow-y: auto;
}

.sidebar-container .el-scrollbar__wrap {
  scroll-behavior: smooth;
}
</style>
