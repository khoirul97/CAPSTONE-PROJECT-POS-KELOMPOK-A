<template>
  <div class="app-container">
    <el-form v-if="user" :model="user">
      <el-row :gutter="20">
        <el-col :xs="24" :sm="11" :md="8" :lg="6" :xl="6">
          <user-card :user="user" />
          <!-- <user-bio /> -->
        </el-col>
        <el-col :xs="24" :sm="13" :md="16" :lg="18" :xl="18">
          <user-information-employee :user="user" />
        </el-col>
      </el-row>
    </el-form>
  </div>
  <!-- Backup Old el-col
  <div class="app-container">
    <el-form v-if="user" :model="user">
      <el-row :gutter="20">
        <el-col :span="6">
          <user-card :user="user" />
          <user-bio />
        </el-col>
        <el-col :span="18">
          <user-activity :user="user" />
        </el-col>
      </el-row>
    </el-form>
  </div> -->
</template>

<script>
import Resource from '@/api/resource';
// import UserBio from './components/UserBio';
import UserCard from './components/UserCard';
import UserInformationEmployee from './components/UserInformationEmployee';

const userResource = new Resource('users');

export default {
  name: 'EditUser',
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
