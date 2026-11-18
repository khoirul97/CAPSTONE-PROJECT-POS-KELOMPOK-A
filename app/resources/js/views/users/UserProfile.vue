<template>
  <div class="app-container" style="padding: 10px">
    <el-form v-if="user" :model="user">
      <el-row :gutter="10">
        <el-col :xs="24" :sm="11" :md="8" :lg="6" :xl="6">
          <user-card :user="user" />
          <!-- <user-bio /> -->
        </el-col>
        <el-col :xs="24" :sm="13" :md="16" :lg="18" :xl="18">
          <user-information :user="user" />
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import Resource from '@/api/resource';
// import UserBio from './components/UserBio';
import UserCard from './components/UserCard';
import UserInformation from './components/UserInformation';

const userResource = new Resource('users');
export default {
  name: 'EditUser',
  components: {
    // UserBio,
    UserCard,
    UserInformation },
  data() {
    return {
      user: {},
    };
  },
  watch: {
    '$route': 'getUser',
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    const currentUserId = this.$store.getters.userId;
    if (id === currentUserId) {
      this.$router.push('/profile/edit');
      return;
    }
    this.getUser(id);
  },
  methods: {
    async getUser(id) {
      const { data } = await userResource.get(id);
      this.user = data;
    },
  },
};
</script>
