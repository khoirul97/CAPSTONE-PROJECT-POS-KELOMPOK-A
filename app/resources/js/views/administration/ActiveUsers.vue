<template>
  <div class="active-users" style="padding: 10px;">
    <el-card class="box-card" style="border-radius: 12px;">
      <div slot="header" class="clearfix">
        <el-tag class="status-tag" round>
          <span
            class="status-indicator"
            :class="'status-active'"
          />
          <span class="status-text">Active Users</span>
        </el-tag>
        <el-button
          style="float: right; padding: 3px 0"
          type="text"
          :loading="loading"
          @click="refreshUsers"
        >
          Refresh
        </el-button>
      </div>

      <!-- Filter Section -->
      <el-row :gutter="5" style="padding-bottom: 10px;">
        <el-col :xs="12" :sm="12" :md="12" :lg="6" :xl="6">
          <el-select
            v-model="query.status"
            placeholder="Filter Status"
            clearable
            style="width: 100%;"
            class="filter-item"
            @change="handleFilter"
          >
            <el-option
              v-for="item in statusOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-col>
        <el-col :xs="12" :sm="12" :md="12" :lg="6" :xl="6">
          <el-select
            v-model="query.role"
            placeholder="Filter Role"
            clearable
            style="width: 100%;"
            class="filter-item"
            @change="handleFilter"
          >
            <el-option
              v-for="item in roleOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-col>
        <el-col :xs="12" :sm="12" :md="12" :lg="6" :xl="6">
          <el-select
            v-model="query.active"
            placeholder="Filter Login"
            clearable
            style="width: 100%;"
            class="filter-item"
            @change="handleFilter"
          >
            <el-option label="Yes" :value="true" />
            <el-option label="No" :value="false" />
          </el-select>
        </el-col>
        <el-col :xs="12" :sm="12" :md="12" :lg="6" :xl="6">
          <el-input
            v-model="query.keyword"
            placeholder="Pencarian"
            clearable
            @clear="handleFilter"
            @keyup.enter.native="handleFilter"
          >
            <el-button slot="append" type="primary" icon="el-icon-search" @click="handleFilter" />
          </el-input>
        </el-col>
      </el-row>

      <div class="table-container">
        <el-table
          v-loading="loading"
          :data="users"
          style="width: 100%"
          empty-text="No active users found"
          stripe
          border
          fit
          class="pro-table"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266', fontWeight: 600 }"
          @sort-change="handleSortChange"
        >
          <el-table-column fixed align="center" label="No" width="80">
            <template slot-scope="scope">
              <!-- <span>{{ (query.page - 1) * query.limit + scope.$index + 1 }}</span> -->
              <el-tag type="primary" size="normal">
                <strong>{{ (query.page - 1) * query.limit + scope.$index + 1 }}</strong>
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column
            fixed
            prop="name"
            label="Name"
            min-width="220"
            sortable="custom"
            :sort-orders="['ascending', 'descending']"
          >
            <template slot-scope="scope">
              <div v-if="scope.row.role !== 'user'">
                <router-link
                  v-if="!scope.row.roles.includes('admin')"
                  :to="'/administrator/users/edit/'+scope.row.id"
                  class="link-primary"
                >
                  <b style="text-decoration: none;"><span>{{ scope.row.name }}</span></b>
                </router-link>
                <router-link
                  v-if="scope.row.roles.includes('admin') && scope.row.id !== userId"
                  :to="'/administrator/users/edit/'+scope.row.id"
                  class="link-primary"
                >
                  <b style="text-decoration: none;"><span>{{ scope.row.name }}</span></b>
                </router-link>
                <router-link
                  v-if="scope.row.roles.includes('admin') && scope.row.id === userId"
                  :to="'/profile/edit'"
                  class="link-primary"
                >
                  <b style="text-decoration: none;"><span>{{ scope.row.name }}</span></b>
                </router-link>
                <el-tag
                  v-if="currentUserId && scope.row.id === currentUserId"
                  size="mini"
                  type="success"
                >
                  You
                </el-tag>
              </div>
              <div v-else>
                <router-link
                  :to="{ name: 'RegistrationsDetail', params: { id: scope.row.id } }"
                  class="link-primary"
                >
                  <b style="text-decoration: none;">{{ scope.row.name }}</b>
                </router-link>
              </div>
            </template>
          </el-table-column>

          <el-table-column
            prop="email"
            label="Email"
            min-width="220"
            sortable="custom"
            :sort-orders="['ascending', 'descending']"
          />

          <el-table-column
            prop="role"
            label="Role"
            width="100"
            sortable="custom"
            align="center"
            :sort-orders="['ascending', 'descending']"
          >
            <template slot-scope="scope">
              <el-tag :type="scope.row.role === 'admin' ? 'danger' : 'primary'">
                {{ scope.row.role }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column
            prop="active"
            label="Login"
            width="100"
            sortable="custom"
            align="center"
            :sort-orders="['ascending', 'descending']"
          >
            <template slot-scope="scope">
              <el-tag :type="scope.row.active ? 'success' : 'danger'">
                {{ scope.row.active ? 'Yes' : 'No' }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column
            prop="last_login_at"
            label="Last Login"
            width="180"
            sortable="custom"
            :sort-orders="['ascending', 'descending']"
          >
            <template slot-scope="scope">
              {{ formatDate(scope.row.last_login_at) }}
            </template>
          </el-table-column>

          <el-table-column
            prop="last_login_ip"
            label="IP Address"
            width="120"
            sortable="custom"
            :sort-orders="['ascending', 'descending']"
          />

          <el-table-column prop="device" label="Device" width="120" />
          <el-table-column prop="platform" label="Platform" width="120" />
          <el-table-column prop="browser" label="Browser" width="120" />
          <el-table-column prop="location" label="Location" min-width="180" />

          <el-table-column
            prop="status"
            label="Status"
            width="100"
            sortable="custom"
            align="center"
            :sort-orders="['ascending', 'descending']"
          >
            <template slot-scope="scope">
              <el-tag :type="getStatusType(scope.row.status)">
                {{ scope.row.status }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column label="Actions" min-width="200" :sortable="false" :fixed="isMobile ? false : 'right'">
            <template slot-scope="scope">
              <!-- Tombol Logout -->
              <el-button
                v-if="currentUserId && scope.row.id !== currentUserId"
                size="mini"
                type="danger"
                :loading="logoutLoading[scope.row.id]"
                @click="handleLogout(scope.row)"
              >
                Logout
              </el-button>

              <!-- Tombol Activate (jika user inactive) -->
              <el-button
                v-if="!scope.row.active && currentUserId && scope.row.id !== currentUserId"
                size="mini"
                type="success"
                :loading="activateLoading[scope.row.id]"
                @click="handleActivate(scope.row)"
              >
                Activate
              </el-button>

              <!-- Tombol Deactivate (jika user active) -->
              <el-button
                v-if="scope.row.active && currentUserId && scope.row.id !== currentUserId"
                size="mini"
                type="warning"
                :loading="deactivateLoading[scope.row.id]"
                @click="handleDeactivate(scope.row)"
              >
                Deactivate
              </el-button>

              <!-- Tampilkan pesan untuk user sendiri -->
              <span v-if="currentUserId && scope.row.id === currentUserId">
                Current User
              </span>
            </template>
          </el-table-column>
        </el-table>

        <!-- Pagination -->
        <pagination
          v-show="total > 0"
          :total="total"
          :page.sync="query.page"
          :limit.sync="query.limit"
          @pagination="fetchUsers"
        />
      </div>
      <div style="margin-top: 20px; text-align: right">
        <el-button
          type="success"
          :loading="resetLogoutAllLoading"
          :disabled="users.length === 0 || users.length === 1 && users[0].id === currentUserId"
          @click="handleResetLogoutAll"
        >
          Reset Logout All Users
        </el-button>
        <el-button
          type="danger"
          :loading="logoutAllLoading"
          :disabled="users.length === 0 || users.length === 1 && users[0].id === currentUserId"
          @click="handleLogoutAll"
        >
          Logout All Users
        </el-button>
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Pagination from '@/components/Pagination'; // Pastikan komponen Pagination sudah ada
import {
  getActiveUsers,
  logoutUser,
  logoutAllUsers,
  resetLogoutAllUsers,
  activateUser,
  deactivateUser,
} from '@/api/administration';

export default {
  name: 'ActiveUsers',
  components: {
    Pagination,
  },
  data() {
    return {
      users: [],
      total: 0,
      loading: false,
      logoutLoading: {},
      logoutAllLoading: false,
      resetLogoutAllLoading: false,
      activateLoading: {},
      deactivateLoading: {},
      isMobile: window.innerWidth <= 767,
      query: {
        page: 1,
        limit: 10,
        keyword: '',
        status: '',
        role: '',
        active: null,
        sortField: null,
        sortOrder: null,
      },
      statusOptions: [
        { value: 'active', label: 'Active' },
        { value: 'inactive', label: 'Inactive' },
        { value: 'disabled', label: 'Disabled' },
        { value: 'rejected', label: 'Rejected' },
      ],
      roleOptions: [
        { value: 'admin', label: 'Admin' },
        { value: 'manager', label: 'Manager' },
        { value: 'kasir', label: 'Kasir' },
      ],
    };
  },
  computed: {
    ...mapGetters([
      'userId',
      'name',
      'roles',
    ]),
    currentUserId() {
      return this.userId;
    },
  },
  created() {
    console.log('Current user ID:', this.userId);
    console.log('Current user name:', this.name);
    console.log('Current user roles:', this.roles);
    this.fetchUsers();
  },
  mounted() {
    window.addEventListener('resize', () => {
      this.isMobile = window.innerWidth <= 767;
    });
  },
  methods: {
    async fetchUsers() {
      this.loading = true;
      try {
        const response = await getActiveUsers(this.query);
        if (response.success) {
          this.users = response.users.data;
          this.total = response.users.total;
          console.log('ACTIVE USERS : ', response.users.data);
        } else {
          this.$message.error(response.message || 'Failed to fetch active users');
        }
      } catch (error) {
        console.error('Error fetching active users:', error);
        this.$message.error('Failed to fetch active users');
      } finally {
        this.loading = false;
      }
    },

    handleFilter() {
      this.query.page = 1;
      this.fetchUsers();
    },

    handleSortChange({ column, prop, order }) {
      if (prop && order) {
        this.query.sortField = prop;
        this.query.sortOrder = order === 'ascending' ? 'asc' : 'desc';
      } else {
        // 🔥 RESET ke default backend
        this.query.sortField = null;
        this.query.sortOrder = null;
      }
      this.fetchUsers();
    },

    getStatusType(status) {
      const map = {
        active: 'success',
        inactive: 'danger',
        disabled: 'danger',
        rejected: 'danger',
      };
      return map[status] || 'info';
    },

    async handleLogout(user) {
      this.$set(this.logoutLoading, user.id, true);

      try {
        await this.$confirm(
          `Are you sure you want to logout ${user.name}?`,
          'Confirm Logout',
          {
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }
        );

        await logoutUser(user.id);
        this.$message.success(`${user.name} has been logged out`);
        this.fetchUsers();
      } catch (error) {
        if (error !== 'cancel') {
          console.error('Logout error:', error);
          this.$message.error('Failed to logout user');
        }
      } finally {
        this.$set(this.logoutLoading, user.id, false);
      }
    },

    async handleLogoutAll() {
      try {
        await this.$confirm(
          'Are you sure you want to logout all users?',
          'Confirm Logout All',
          {
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }
        );
        this.logoutAllLoading = true;
        await logoutAllUsers();
        this.$message.success('All users have been logged out');
        this.fetchUsers();
      } catch (error) {
        if (error !== 'cancel') {
          console.error('Logout all error:', error);
          this.$message.error('Failed to logout all users');
        }
      } finally {
        this.logoutAllLoading = false;
      }
    },

    async handleResetLogoutAll() {
      try {
        await this.$confirm(
          'Are you sure you want to reset logout all users?',
          'Confirm Reset Logout All',
          {
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }
        );
        this.resetLogoutAllLoading = true;
        await resetLogoutAllUsers();
        this.$message.success('All users have been reset');
        this.fetchUsers();
      } catch (error) {
        if (error !== 'cancel') {
          console.error('Reset Logout all error:', error);
          this.$message.error('Failed to reset logout all users');
        }
      } finally {
        this.resetLogoutAllLoading = false;
      }
    },

    async handleActivate(user) {
      this.$set(this.activateLoading, user.id, true);

      try {
        await this.$confirm(
          `Activate ${user.name}?`,
          'Confirm Activation',
          {
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            type: 'info',
          }
        );

        await activateUser(user.id);
        this.$message.success(`${user.name} has been activated`);
        this.fetchUsers();
      } catch (error) {
        if (error !== 'cancel') {
          console.error('Activate error:', error);
          this.$message.error('Failed to activate user');
        }
      } finally {
        this.$set(this.activateLoading, user.id, false);
      }
    },

    async handleDeactivate(user) {
      this.$set(this.deactivateLoading, user.id, true);

      try {
        await this.$confirm(
          `Deactivate ${user.name}?`,
          'Confirm Deactivation',
          {
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }
        );

        await deactivateUser(user.id);
        this.$message.success(`${user.name} has been deactivated`);
        this.fetchUsers();
      } catch (error) {
        if (error !== 'cancel') {
          console.error('Deactivate error:', error);
          this.$message.error('Failed to deactivate user');
        }
      } finally {
        this.$set(this.deactivateLoading, user.id, false);
      }
    },

    refreshUsers() {
      this.fetchUsers();
    },

    formatDate(dateString) {
      if (!dateString) {
        return '-';
      }

      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
      }).replace(/(\d+)\/(\d+)\/(\d+),?/, '$3-$1-$2');
    },
  },
};
</script>

<style scoped>
.active-users {
  padding: 20px;
}

.el-button + .el-button {
  margin-left: 5px;
}

.filter-item {
  margin-bottom: 10px;
}

.stats-container {
  margin-bottom: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  padding: 15px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 10px;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
  font-size: 18px;
  background: #f0f2f5;
}

.stat-info {
  flex: 1;
}

.stat-label {
  font-size: 12px;
  color: #606266;
  margin-bottom: 5px;
}

.stat-value {
  font-size: 20px;
  font-weight: bold;
  color: #303133;
}

.status-tag {
  background-color: transparent !important;
  border: none !important;
  font-weight: bold;
  cursor: default;
  transition: text-shadow 0.3s ease;
  color: inherit;
  display: inline-flex;
  align-items: center;
  padding: 4px 8px;
  height: auto;
}

.status-tag.active {
  color: #21ba45;
}

.status-tag.inactive {
  color: #db2828;
}

.status-tag.active:hover {
  text-shadow:
    0 0 2px white,
    0 0 5px #21ba45;
}

.status-tag.inactive:hover {
  text-shadow:
    0 0 2px white,
    0 0 5px #db2828;
}

.status-indicator {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 8px;
  flex-shrink: 0;
  position: relative;
  top: -1px; /* Fine-tuning untuk vertical alignment */
}

.status-text {
  font-size: x-large;
  line-height: 1;
  display: inline-flex;
  align-items: center;
}

.status-active {
  background: #52c41a;
  animation: pulse-green 2s infinite;
  box-shadow: 0 0 0 0 rgba(82, 196, 26, 0.7);
}

.status-nonjob {
  background: #faad14;
  animation: pulse-yellow 2s infinite;
  box-shadow: 0 0 0 0 rgba(250, 173, 20, 0.7);
}

.status-inactive {
  background: #ff4d4f;
  animation: pulse-red 2s infinite;
  box-shadow: 0 0 0 0 rgba(255, 77, 79, 0.7);
}

/* Animations */
@keyframes pulse-green {
  0% {
    box-shadow: 0 0 0 0 rgba(82, 196, 26, 0.7);
  }
  70% {
    box-shadow: 0 0 0 6px rgba(82, 196, 26, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(82, 196, 26, 0);
  }
}

@keyframes pulse-yellow {
  0% {
    box-shadow: 0 0 0 0 rgba(250, 173, 20, 0.7);
  }
  70% {
    box-shadow: 0 0 0 6px rgba(250, 173, 20, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(250, 173, 20, 0);
  }
}

@keyframes pulse-red {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 77, 79, 0.7);
  }
  70% {
    box-shadow: 0 0 0 6px rgba(255, 77, 79, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 77, 79, 0);
  }
}

/* Untuk memastikan header card ter-align dengan baik */
.clearfix {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.clearfix::before,
.clearfix::after {
  display: none; /* Menonaktifkan clearfix default jika mengganggu */
}

/* ================= TABLE ================= */
.table-wrapper {
  overflow-x: auto;
}

.pro-table {
  border-radius: 8px;
  overflow: hidden;
  min-width: 600px;
}

/* .pro-table >>> .el-table__row {
  cursor: pointer;
} */

/* Enable horizontal scroll on mobile */

/* Gunakan pendekatan ini */
.table-container {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

@media (max-width: 767px) {
  /* Memastikan tabel memiliki ruang yang cukup untuk di-scroll */
  .pro-table {
    width: 100%;
  }

  /* Memaksa elemen internal tabel untuk tidak memotong konten */
  .el-table__header, .el-table__body {
    table-layout: auto !important;
  }
}

/* Better touch scrolling for mobile */
.el-table__body-wrapper::-webkit-scrollbar {
  height: 6px;
}

.el-table__body-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.el-table__body-wrapper::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.el-table__body-wrapper::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
