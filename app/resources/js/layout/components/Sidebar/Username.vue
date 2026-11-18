<template>
  <div>
    <el-tooltip :content="$store.getters.name" effect="dark" placement="right" :hide-after="2000">
      <div class="sidebar-username-container" :class="{'collapse':collapse}">
        <transition name="sidebarusernameFade">
          <router-link v-if="collapse" key="collapse" class="sidebar-username-link" :to="`/profile/edit`">
            <!-- <img v-if="user.avatar" :src="'/storage/avatars/'+$store.getters.avatar" class="sidebar-username" style="border: 1px solid rgba(0,0,0,0.16);"> -->
            <img src="@/assets/login/default_avatar.png" class="sidebar-username" style="border: 1px solid rgba(0,0,0,0.16);">
            <h1 class="sidebar-title" style="font-weight: normal;"><span v-if="user.name">{{ user.name.slice(0, 20) }}</span></h1>
          </router-link>
          <router-link v-else key="expand" class="sidebar-username-link" :to="`/profile/edit`">
            <!-- <img v-if="user.avatar" :src="'/storage/avatars/'+$store.getters.avatar" class="sidebar-username" style="border: 1px solid rgba(0,0,0,0.16);"> -->
            <img src="@/assets/login/default_avatar.png" class="sidebar-username" style="border: 1px solid rgba(0,0,0,0.16);">
            <h1 v-if="user.name" class="sidebar-title" style="font-weight: normal;">{{ user.name.slice(0, 20) }} </h1>
          </router-link>
        </transition>
      </div>
    </el-tooltip>
  </div>
</template>

<script>
export default {
  name: 'Sidebarusername',
  props: {
    collapse: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      title: 'Username',
      username: '',
      // user: {},
      user: {
        type: Object,
        default: () => {
          return {
            name: '',
            email: '',
            // avatar: '',
            roles: [],
          };
        },
      },
    };
  },
  watch: {
    '$route': 'getUser',
  },
  created() {
    this.getUser();
  },
  methods: {
    async getUser() {
      const data = await this.$store.dispatch('user/getInfo');
      this.user = data;
    },
  },
};
</script>

<style lang="scss" scoped>
  .sidebarusernameFade-enter-active {
    transition: opacity 1.5s;
  }

  .sidebarusernameFade-enter,
  .sidebarusernameFade-leave-to {
    opacity: 0;
  }

  .sidebar-username-container {
    position: relative;
    min-width: 100%;
    height: 50px;
    line-height: 50px;
    // background: #284181;
    text-align: center;
    overflow: hidden;

    & .sidebar-username-link {
      height: 100%;
      width: 100%;

      & .sidebar-username {
        width: 40px;
        height: 40px;
        vertical-align: middle;
        margin-right: 12px;
        border-radius: 100%;
      }

      & .sidebar-title {
        display: inline-block;
        margin: 0;
        color: #fff;
        line-height: 50px;
        font-size: 14px;
        font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
        vertical-align: middle;
      }
    }

    &.collapse {
      .sidebar-username {
        margin-right: 0px;
      }
    }
  }
</style>
