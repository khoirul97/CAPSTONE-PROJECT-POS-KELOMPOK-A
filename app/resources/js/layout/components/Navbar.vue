<template>
  <div class="navbar">

    <!-- HAMBURGER -->
    <hamburger
      id="hamburger-container"
      :is-active="sidebar.opened"
      class="hamburger-container"
      @toggleClick="toggleSideBar"
    />

    <!-- BREADCRUMB -->
    <breadcrumb
      id="breadcrumb-container"
      class="breadcrumb-container"
    />

    <!-- RIGHT MENU -->
    <div class="right-menu">

      <!-- DARK MODE -->
      <DarkModeToggle
        id="darkmode-toggle"
        :is-active="sidebar.opened"
        class="right-menu-item hover-effect"
      />

      <!-- FULLSCREEN -->
      <screenfull
        id="screenfull"
        class="right-menu-item hover-effect"
      />

      <!-- DESKTOP ONLY -->
      <template v-if="device !== 'mobile'">

        <!-- SEARCH -->
        <search
          id="header-search"
          class="right-menu-item"
        />

        <!-- SIZE -->
        <el-tooltip
          :content="$t('navbar.size')"
          effect="dark"
          placement="bottom"
        >
          <size-select
            id="size-select"
            class="right-menu-item hover-effect"
          />
        </el-tooltip>

        <!-- LANGUAGE -->
        <lang-select
          class="right-menu-item hover-effect"
        />

      </template>

      <!-- AVATAR -->
      <el-dropdown
        class="avatar-container right-menu-item hover-effect"
        trigger="click"
      >

        <div class="avatar-wrapper">

          <el-tooltip
            :content="$store.getters.name"
            effect="dark"
            placement="bottom"
            :hide-after="2000"
          >
            <img
              :src="photo_profile"
              class="user-avatar"
            >
          </el-tooltip>

          <i class="el-icon-caret-bottom" />

        </div>

        <el-dropdown-menu slot="dropdown">

          <!-- USER NAME -->
          <el-dropdown-item>
            <router-link
              to="/profile/edit"
              style="display:block;"
            >
              <b>{{ $store.getters.name }}</b>
            </router-link>
          </el-dropdown-item>

          <!-- DASHBOARD -->
          <router-link to="/">
            <el-dropdown-item>
              {{ $t('navbar.dashboard') }}
            </el-dropdown-item>
          </router-link>

          <!-- MY ACTIVITY REPORT -->
          <router-link
            v-if="roles[0] === 'user' && employee[0] != []"
            v-role="['user']"
            :to="{
              name: 'My Activity Report',
              params: {
                id: employee[0].id
              }
            }"
          >
            <el-dropdown-item>
              {{ $t('route.myActivityReport') }}
            </el-dropdown-item>
          </router-link>

          <!-- PROFILE -->
          <router-link
            v-show="userId !== null"
            to="/profile/edit"
          >
            <el-dropdown-item>
              {{ $t('navbar.profile') }}
            </el-dropdown-item>
          </router-link>

          <!-- LOGOUT -->
          <el-dropdown-item>
            <span
              style="display:block;"
              @click="logout"
            >
              {{ $t('navbar.logOut') }}
            </span>
          </el-dropdown-item>

        </el-dropdown-menu>

      </el-dropdown>

    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import Breadcrumb from '@/components/Breadcrumb';
import Hamburger from '@/components/Hamburger';
import Screenfull from '@/components/Screenfull';
import SizeSelect from '@/components/SizeSelect';
import LangSelect from '@/components/LangSelect';
import Search from '@/components/HeaderSearch';

import permission from '@/directive/permission';
import role from '@/directive/role';

import DarkModeToggle from '@/components/DarkModeToggle';

import photo_profile from '@/assets/login/default_avatar.png';

export default {
  directives: {
    permission,
    role,
  },

  components: {
    Breadcrumb,
    Hamburger,
    Screenfull,
    DarkModeToggle,
    SizeSelect,
    LangSelect,
    Search,
  },

  data() {
    return {
      user: {
        type: Object,
        default: () => {
          return {
            name: '',
            email: '',
            avatar: '',
            roles: [],
          };
        },
      },

      photo_profile: photo_profile,
    };
  },

  computed: {
    ...mapGetters([
      'sidebar',
      'name',
      'avatar',
      'device',
      'userId',
      'roles',
      'employee',
    ]),
  },

  created() {
    this.getUser();
  },

  methods: {

    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },

    async logout() {
      await this.$store.dispatch('user/logout');

      this.$router.replace({
        path: '/login',
      });
    },

    async getUser() {
      const data = await this.$store.dispatch('user/getInfo');

      this.user = data;

      console.log(data);
    },

  },
};
</script>

<style lang="scss" scoped>

.navbar {
  height: 50px;
  width: 100%;
  overflow: hidden;
  position: relative;
  background: #fff;
  border-bottom: 1px solid rgba(0, 0, 0, 0.16);
  box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);

  // ==========================================================
  // HAMBURGER
  // ==========================================================

  .hamburger-container {
    line-height: 55px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background 0.3s;
    -webkit-tap-highlight-color: transparent;

    &:hover {
      background: rgba(0, 0, 0, 0.025);
    }
  }

  // ==========================================================
  // BREADCRUMB
  // ==========================================================

  .breadcrumb-container {
    float: left;
  }

  // ==========================================================
  // RIGHT MENU
  // ==========================================================

  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 18px;
      color: #5a5e66;
      vertical-align: text-bottom;

      &.hover-effect {
        cursor: pointer;
        transition: background 0.3s;

        &:hover {
          background: rgba(0, 0, 0, 0.025);
        }
      }
    }

    // ========================================================
    // AVATAR
    // ========================================================

    .avatar-container {
      margin-right: 30px;

      .avatar-wrapper {
        margin-top: 5px;
        position: relative;

        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;

          border-radius: 100%;
          border: 2px solid rgba(0, 0, 0, 0.16);

          object-fit: cover;
        }

        .el-icon-caret-bottom {
          cursor: pointer;

          position: absolute;
          right: -20px;
          top: 25px;

          font-size: 12px;
        }
      }
    }
  }
}

// ============================================================
// TABLET
// ============================================================

@media screen and (max-width: 992px) {

  .navbar {

    .right-menu {

      .right-menu-item {
        padding: 0 6px;
        font-size: 17px;
      }

      .avatar-container {
        margin-right: 20px;
      }
    }
  }
}

// ============================================================
// MOBILE
// max-width: 768px
// ============================================================

@media screen and (max-width: 768px) {

  .navbar {
    height: 50px;
    width: 100%;

    position: relative;
    overflow: hidden;

    // ========================================================
    // HAMBURGER
    // ========================================================

    .hamburger-container {
      position: absolute;

      left: 0px;
      top: 50%;
      transform: translateY(-50%);

      width: 44px;
      min-width: 44px;
      height: 50px;

      margin: 0 !important;
      padding: 0 !important;

      float: none;

      display: flex;
      align-items: center;
      justify-content: center;

      cursor: pointer;

      z-index: 10;
    }

    // ========================================================
    // BREADCRUMB
    // ========================================================

    .breadcrumb-container {

      position: absolute;

      left: 44px;
      top: 0;

      height: 50px;

      /*
       * 44px  = hamburger
       * 190px = area menu kanan
       */
      width: calc(100% - 44px - 190px);
      max-width: calc(100% - 44px - 190px);

      margin: 0;
      padding: 0;

      float: none;

      line-height: 50px;

      overflow: hidden;

      white-space: nowrap;
      text-overflow: ellipsis;

      z-index: 5;

      box-sizing: border-box;
    }

    // ========================================================
    // RIGHT MENU
    // ========================================================

    .right-menu {

      position: absolute;

      right: 0;
      top: 0;

      height: 50px;

      display: flex;
      align-items: center;

      float: none;

      line-height: 50px;

      /*
       * Jangan biarkan right-menu mengecil
       */
      flex-shrink: 0;

      /*
       * Background supaya breadcrumb tidak masuk
       * ke area menu kanan
       */
      background: #fff;

      z-index: 20;

      /*
       * Sedikit pemisah dari breadcrumb
       */
      // box-shadow: -4px 0 8px rgba(0, 0, 0, 0.03);

      // ======================================================
      // ALL RIGHT MENU ITEMS
      // ======================================================

      .right-menu-item {

        display: flex;

        align-items: center;
        justify-content: center;

        width: 30px;
        min-width: 30px;

        height: 50px;

        padding: 0 !important;
        margin: 5px !important;

        font-size: 16px !important;

        line-height: 50px;

        vertical-align: middle;

        box-sizing: border-box;

        flex-shrink: 0;
      }

      // ======================================================
      // AVATAR
      // ======================================================

      .avatar-container {

        width: 40px !important;
        min-width: 40px !important;

        height: 50px;

        margin-right: 5px !important;

        padding: 0 !important;

        display: flex;

        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        .avatar-wrapper {

          position: relative;

          width: 40px;
          height: 50px;

          margin: 0;

          display: flex;

          align-items: center;
          justify-content: center;

          .user-avatar {

            cursor: pointer;

            width: 32px;
            height: 32px;

            border-radius: 50%;

            border: 1px solid rgba(0, 0, 0, 0.16);

            object-fit: cover;
          }

          .el-icon-caret-bottom {

            cursor: pointer;

            position: absolute;

            right: -5px;
            top: 21px;

            font-size: 8px;
          }
        }
      }
    }
  }
}

// ============================================================
// MOBILE SMALL
// max-width: 375px
// ============================================================

@media screen and (max-width: 375px) {

  .navbar {

    // ========================================================
    // HAMBURGER
    // ========================================================

    .hamburger-container {

      width: 40px;
      min-width: 40px;

      height: 50px;
    }

    // ========================================================
    // BREADCRUMB
    // ========================================================

    .breadcrumb-container {

      left: 40px;

      /*
       * Area kanan dibuat lebih kecil
       */
      width: calc(100% - 40px - 175px);
      max-width: calc(100% - 40px - 175px);

      height: 50px;

      line-height: 50px;

      font-size: 12px;
    }

    // ========================================================
    // RIGHT MENU
    // ========================================================

    .right-menu {

      height: 50px;

      .right-menu-item {

        width: 28px;
        min-width: 28px;

        height: 50px;

        font-size: 15px !important;
      }

      // ======================================================
      // AVATAR
      // ======================================================

      .avatar-container {

        width: 36px !important;
        min-width: 36px !important;

        margin-right: 3px !important;

        .avatar-wrapper {

          width: 36px;
          height: 50px;

          .user-avatar {

            width: 30px;
            height: 30px;

            border-width: 1px;
          }

          .el-icon-caret-bottom {

            right: -5px;
            top: 21px;

            font-size: 7px;
          }
        }
      }
    }
  }
}

// ============================================================
// VERY SMALL MOBILE
// max-width: 340px
// ============================================================

@media screen and (max-width: 340px) {

  .navbar {

    // ========================================================
    // HAMBURGER
    // ========================================================

    .hamburger-container {

      width: 38px;
      min-width: 38px;
    }

    // ========================================================
    // BREADCRUMB
    // ========================================================

    .breadcrumb-container {

      left: 38px;

      width: calc(100% - 38px - 168px);
      max-width: calc(100% - 38px - 168px);

      font-size: 11px;
    }

    // ========================================================
    // RIGHT MENU
    // ========================================================

    .right-menu {

      .right-menu-item {

        width: 27px;
        min-width: 27px;

        font-size: 14px !important;
      }

      .avatar-container {

        width: 34px !important;
        min-width: 34px !important;

        margin-right: 2px !important;

        .avatar-wrapper {

          width: 34px;

          .user-avatar {

            width: 28px;
            height: 28px;
          }

          .el-icon-caret-bottom {

            right: -5px;

            top: 21px;

            font-size: 7px;
          }
        }
      }
    }
  }
}

// ============================================================
// DARK MODE
// ============================================================

.dark-mode {

  .navbar {

    background: #000;

    border-bottom-color: rgba(255, 255, 255, 0.1);

    .right-menu {

      background: #000;

      .right-menu-item {

        color: #ddd;

        &.hover-effect:hover {

          background: rgba(255, 255, 255, 0.05);
        }
      }
    }
  }
}

</style>

