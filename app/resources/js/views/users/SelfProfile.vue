<template>
  <div class="app-container" style="padding: 10px">
    <el-form v-if="user" :model="user">
      <el-row :gutter="12">
        <el-col :xs="24" :sm="11" :md="8" :lg="6" :xl="6">
          <user-card :user="user" />
        </el-col>
        <el-col :xs="24" :sm="13" :md="16" :lg="18" :xl="18">
          <user-information-employee :user="user" />
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
// import UserBio from './components/UserBio';
import UserCard from './components/UserCard';
import UserInformationEmployee from './components/UserInformationEmployee';

export default {
  name: 'SelfProfile',
  components: { UserCard, UserInformationEmployee },
  data() {
    return {
      user: {},
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
