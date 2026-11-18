<template>
  <div class="app-container" style="padding: 10px;">
    <el-card class="header-card" style="border-radius: 12px;">
      <div class="header-content">
        <img id="LOGO_COMPANY" class="logo_file" :src="logo" alt="Logo">
        <div class="header-text">
          <h1>Daftar Pengguna</h1>
          <p class="subtitle">
            Kelola pengguna untuk Dashboard manajemen website Capstone Project
          </p>
        </div>
      </div>
    </el-card>
    <el-card style="border-radius: 12px; margin-bottom: 1%;">
      <br>
      <div class="filter-container">
        <el-input
          v-model="query.keyword"
          placeholder="Keyword"
          class="filter-item"
          @keyup.enter.native="handleFilter"
        />

        <el-select
          v-model="query.role"
          placeholder="Role"
          clearable
          filterable
          class="filter-item"
          @change="handleFilter"
        >
          <el-option
            v-for="item in roles"
            :key="item"
            :label="item | uppercaseFirst"
            :value="item"
          />
        </el-select>

        <el-button
          type="primary"
          icon="el-icon-search"
          class="filter-item"
          @click="handleFilter"
        >
          Search
        </el-button>

        <!-- 🔥 langsung sejajar -->
        <el-button
          type="primary"
          icon="el-icon-plus"
          class="filter-item"
          @click="handleCreate"
        >
          Add
        </el-button>

        <el-button
          :loading="downloading"
          type="primary"
          icon="el-icon-download"
          class="filter-item"
          @click="handleDownload"
        >
          Export
        </el-button>
      </div>

      <div class="table-wrapper">
        <el-table
          v-loading="loading"
          :data="list"
          stripe
          border
          class="pro-table"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266', fontWeight: 600 }"
        >
          <el-table-column fixed align="center" label="No" width="60">
            <template slot-scope="scope">
              <!-- <span>{{ scope.row.index }}</span> -->
              <el-tag type="primary" size="mini">
                <strong>{{ scope.row.index }}</strong>
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column fixed prop="name" sortable label="Name" width="240">
            <!-- <template slot-scope="scope">
              <span>{{ scope.row.name }}</span>
            </template> -->
            <template slot-scope="scope">
              <router-link
                v-if="canEdit(scope.row)"
                :to="getEditRoute(scope.row)"
              >
                <span><b style="text-decoration: none;">{{ scope.row.name }}</b></span>
              </router-link>
              <el-tag
                v-if="scope.row.status === 'active'"
                size="mini"
                type="success"
              >
                {{ scope.row.status }}
              </el-tag>
              <el-tag
                v-else
                size="mini"
                type="danger"
              >
                {{ scope.row.status }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column align="left" label="Email" width="200">
            <template slot-scope="scope">
              <span>{{ scope.row.email }}</span>
            </template>
          </el-table-column>

          <el-table-column align="left" label="No WhatsApp" width="110">
            <template slot-scope="scope">
              <span>{{ scope.row.whatsapp_number }}</span>
            </template>
          </el-table-column>

          <el-table-column align="center" label="Role" width="120">
            <template slot-scope="scope">
              <span>{{ scope.row.roles.join(', ') }}</span>
            </template>
          </el-table-column>

          <el-table-column align="left" label="Jabatan" width="150">
            <template slot-scope="scope">
              <span>{{ scope.row.jabatan }}</span>
            </template>
          </el-table-column>

          <el-table-column align="left" label="Actions" min-width="220">
            <template slot-scope="scope">
              <router-link
                v-if="canEdit(scope.row)"
                :to="getEditRoute(scope.row)"
              >
                <el-button
                  v-permission="['manage user']"
                  type="primary"
                  size="small"
                  icon="el-icon-edit"
                >
                  Edit
                </el-button>
              </router-link>
              <el-button v-if="!scope.row.roles.includes('admin')" v-permission="['manage permission']" type="warning" size="small" icon="el-icon-edit" @click="handleEditPermissions(scope.row.id);">
                Permissions
              </el-button>
              <el-button v-if="scope.row.roles.includes('visitor')" v-permission="['manage user']" type="danger" size="small" icon="el-icon-delete" @click="handleDelete(scope.row.id, scope.row.name);">
                Delete
              </el-button>
            </template>
          </el-table-column>
        </el-table>

        <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
      </div>
    </el-card>

    <el-dialog :visible.sync="dialogPermissionVisible" :title="'Edit Permissions - ' + currentUser.name">
      <div v-if="currentUser.name" v-loading="dialogPermissionLoading" class="form-container">
        <div class="permissions-container">
          <div class="block">
            <el-form :model="currentUser" label-width="80px" label-position="top">
              <el-form-item label="Menus">
                <el-tree ref="menuPermissions" :data="normalizedMenuPermissions" :default-checked-keys="permissionKeys(userMenuPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
              </el-form-item>
            </el-form>
          </div>
          <div class="block">
            <el-form :model="currentUser" label-width="80px" label-position="top">
              <el-form-item label="Permissions">
                <el-tree ref="otherPermissions" :data="normalizedOtherPermissions" :default-checked-keys="permissionKeys(userOtherPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
              </el-form-item>
            </el-form>
          </div>
          <div class="clear-left" />
        </div>
        <div style="text-align:right;">
          <el-button type="danger" @click="dialogPermissionVisible=false">
            {{ $t('permission.cancel') }}
          </el-button>
          <el-button type="primary" @click="confirmPermission">
            {{ $t('permission.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog :title="'Create new user'" :visible.sync="dialogFormVisible">
      <div v-loading="userCreating" class="form-container">
        <el-form ref="userForm" :rules="rules" :model="newUser" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="$t('user.role')" prop="role">
            <el-select v-model="newUser.role" class="filter-item" placeholder="Please select role">
              <el-option v-for="item in nonAdminRoles" :key="item" :label="item | uppercaseFirst" :value="item" />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('user.name')" prop="name">
            <el-input v-model="newUser.name" />
          </el-form-item>
          <el-form-item :label="$t('user.email')" prop="email">
            <el-input v-model="newUser.email" />
          </el-form-item>
          <el-form-item :label="'WhatsApp Number'" prop="whatsapp_number">
            <el-input v-model="newUser.whatsapp_number" />
          </el-form-item>
          <el-form-item :label="$t('user.password')" prop="password">
            <el-input v-model="newUser.password" show-password />
          </el-form-item>
          <el-form-item :label="$t('user.confirmPassword')" prop="confirmPassword">
            <el-input v-model="newUser.confirmPassword" show-password />
          </el-form-item>
          <el-form-item :label="'Jabatan'" prop="jabatan">
            <el-input v-model="newUser.jabatan" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createUser()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import UserResource from '@/api/user';
import Resource from '@/api/resource';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission'; // Permission checking
import logo from '@/assets/login/logo_capstone_project.png';

const userResource = new UserResource();
const permissionResource = new Resource('permissions');

export default {
  name: 'UserList',
  components: { Pagination },
  directives: { waves, permission },
  data() {
    var validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.newUser.password) {
        callback(new Error('Password is mismatched!'));
      } else {
        callback();
      }
    };
    return {
      list: null,
      total: 0,
      loading: true,
      downloading: false,
      userCreating: false,
      signatureFile: null,
      signaturePreview: null,
      logo: logo,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        division_id: '',
      },
      roles: ['admin', 'manager', 'kasir'],
      nonAdminRoles: ['admin', 'manager', 'kasir'],
      newUser: {},
      dialogFormVisible: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      currentUserId: 0,
      currentUser: {
        name: '',
        permissions: [],
        rolePermissions: [],
      },
      rules: {
        role: [{ required: true, message: 'Role is required', trigger: 'change' }],
        name: [{ required: true, message: 'Name is required', trigger: 'blur' }],
        email: [
          { required: true, message: 'Email is required', trigger: 'blur' },
          { type: 'email', message: 'Please input correct email address', trigger: ['blur', 'change'] },
        ],
        password: [{ required: true, message: 'Password is required', trigger: 'blur' }],
        confirmPassword: [{ validator: validateConfirmPassword, trigger: 'blur' }],
      },
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
    };
  },
  computed: {
    normalizedMenuPermissions() {
      let tmp = [];
      this.currentUser.permissions.role.forEach(permission => {
        tmp.push({
          id: permission.id,
          name: permission.name,
          disabled: true,
        });
      });
      const rolePermissions = {
        id: -1, // Just a faked ID
        name: 'Inherited from role',
        disabled: true,
        children: this.classifyPermissions(tmp).menu,
      };

      tmp = this.menuPermissions.filter(permission => !this.currentUser.permissions.role.find(p => p.id === permission.id));
      const userPermissions = {
        id: 0, // Faked ID
        name: 'Extra menus',
        children: tmp,
        disabled: tmp.length === 0,
      };

      return [rolePermissions, userPermissions];
    },
    normalizedOtherPermissions() {
      let tmp = [];
      this.currentUser.permissions.role.forEach(permission => {
        tmp.push({
          id: permission.id,
          name: permission.name,
          disabled: true,
        });
      });
      const rolePermissions = {
        id: -1,
        name: 'Inherited from role',
        disabled: true,
        children: this.classifyPermissions(tmp).other,
      };

      tmp = this.otherPermissions.filter(permission => !this.currentUser.permissions.role.find(p => p.id === permission.id));
      const userPermissions = {
        id: 0,
        name: 'Extra permissions',
        children: tmp,
        disabled: tmp.length === 0,
      };

      return [rolePermissions, userPermissions];
    },
    userMenuPermissions() {
      return this.classifyPermissions(this.userPermissions).menu;
    },
    userOtherPermissions() {
      return this.classifyPermissions(this.userPermissions).other;
    },
    userPermissions() {
      return this.currentUser.permissions.role.concat(this.currentUser.permissions.user);
    },
  },
  created() {
    this.resetNewUser();
    this.getList();
    if (checkPermission(['manage permission'])) {
      this.getPermissions();
    }
  },
  methods: {
    checkPermission,
    async getPermissions() {
      const { data } = await permissionResource.list({});
      const { all, menu, other } = this.classifyPermissions(data);
      this.permissions = all;
      this.menuPermissions = menu;
      this.otherPermissions = other;
    },

    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await userResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleCreate() {
      this.resetNewUser();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('This will permanently delete user ' + name + '. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        userResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: 'Delete completed',
          });
          this.handleFilter();
        }).catch(error => {
          console.log(error);
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    async handleEditPermissions(id) {
      this.currentUserId = id;
      this.dialogPermissionLoading = true;
      this.dialogPermissionVisible = true;
      const found = this.list.find(user => user.id === id);
      const { data } = await userResource.permissions(id);
      this.currentUser = {
        id: found.id,
        name: found.name,
        permissions: data,
      };
      this.dialogPermissionLoading = false;
      this.$nextTick(() => {
        this.$refs.menuPermissions.setCheckedKeys(this.permissionKeys(this.userMenuPermissions));
        this.$refs.otherPermissions.setCheckedKeys(this.permissionKeys(this.userOtherPermissions));
      });
    },
    createUser() {
      this.$refs['userForm'].validate((valid) => {
        if (!valid) {
          return;
        }

        this.userCreating = true;

        // ✅ wajib
        this.newUser.roles = [this.newUser.role];

        // ✅ Konversi boolean ke integer 0/1
        const userData = {
          ...this.newUser,
        };

        const formData = new FormData();

        Object.keys(userData).forEach(key => {
          if (Array.isArray(userData[key])) {
            userData[key].forEach(val => {
              formData.append(`${key}[]`, val);
            });
          } else {
            formData.append(key, userData[key]);
          }
        });

        userResource.storeFormData(formData)
          .then(() => {
            this.$message({
              message: `New user ${this.newUser.name} berhasil dibuat`,
              type: 'success',
            });

            this.resetNewUser();
            this.dialogFormVisible = false;
            this.handleFilter();
          })
          .finally(() => {
            this.userCreating = false;
          });
      });
    },
    resetNewUser() {
      this.newUser = {
        name: '',
        email: '',
        whatsapp_number: '',
        password: '',
        jabatan: '',
        confirmPassword: '',
        role: '',
      };
    },
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['id', 'user_id', 'name', 'email', 'whatsapp_number', 'jabatan', 'role'];
        const filterVal = ['index', 'id', 'name', 'email', 'whatsapp_number', 'jabatan', 'role'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'user-list',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => v[j]));
    },
    permissionKeys(permissions) {
      return permissions.map(permssion => permssion.id);
    },
    classifyPermissions(permissions) {
      const all = []; const menu = []; const other = [];
      permissions.forEach(permission => {
        const permissionName = permission.name;
        all.push(permission);
        if (permissionName.startsWith('view menu')) {
          menu.push(this.normalizeMenuPermission(permission));
        } else {
          other.push(this.normalizePermission(permission));
        }
      });
      return { all, menu, other };
    },

    normalizeMenuPermission(permission) {
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name.substring(10)), disabled: permission.disabled || false };
    },

    normalizePermission(permission) {
      const disabled = permission.disabled || permission.name === 'manage permission';
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name), disabled: disabled };
    },

    confirmPermission() {
      const checkedMenu = this.$refs.menuPermissions.getCheckedKeys();
      const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
      const checkedPermissions = checkedMenu.concat(checkedOther);
      this.dialogPermissionLoading = true;

      userResource.updatePermission(this.currentUserId, { permissions: checkedPermissions }).then(response => {
        this.$message({
          message: 'Permissions has been updated successfully',
          type: 'success',
          duration: 5 * 1000,
        });
        this.dialogPermissionLoading = false;
        this.dialogPermissionVisible = false;
      });
    },

    canEdit(row) {
      // semua boleh edit, nanti routing yang beda
      return true;
    },

    getEditRoute(row) {
      const isAdmin = row.roles.includes('admin');
      const isSelf = row.id === this.$store.getters.userId;

      if (isAdmin && isSelf) {
        return '/profile/edit';
      }

      return `/administrator/users/edit/${row.id}`;
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}

// Header Styles
.header-card {
  margin-bottom: 20px;
  border: none;
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);

  .header-content {
    display: flex;
    align-items: center;
    gap: 30px;
  }

  .logo_file {
    height: 60px;
    object-fit: contain;
  }

  .header-text {
    flex: 1;

    h1 {
      margin: 0;
      font-size: 24px;

      em {
        font-style: italic;
        color: #409eff;
      }
    }

    .subtitle {
      margin: 10px 0 0;
      font-size: 14px;
      line-height: 1.5;

      em {
        font-style: italic;
        font-weight: 500;
      }
    }
  }
}

.filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
}

.filter-item {
  min-width: 120px;
  display: flex;
  align-items: center;
}

/* Samakan tinggi semua */
.filter-item .el-input__inner,
.filter-item .el-select .el-input__inner {
  height: 36px;
  line-height: 36px;
}

.filter-item.el-button {
  height: 36px;
  padding: 0 15px;
}

.filter-container ::v-deep .el-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

/* Desktop */
@media (min-width: 768px) {
  .filter-container {
    justify-content: flex-start;
  }

  .filter-item {
    width: auto;
  }
}

/* Mobile */
@media (max-width: 767px) {
  .filter-container {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-item {
    width: 100%;
  }
}

/* ================= TABLE ================= */
.table-wrapper {
  width: 100%;
  overflow-x: auto;
}

.pro-table {
  width: 100%;
  min-width: 900px; // biar tidak hancur di mobile
  border-radius: 12px;
}

// .pro-table >>> .el-table__row {
//   cursor: pointer;
// }
.action-buttons {
  display: flex;
  gap: 10px;
}

/* MOBILE */
@media (max-width: 767px) {
  .action-buttons {
    width: 100%;
    justify-content: center; // 🔥 center horizontal
  }

  .action-buttons .el-button {
    width: auto; // biar tidak full width
    min-width: 120px;
  }
}
.action-buttons {
  display: flex;
  gap: 10px;
  align-items: flex-end; // 🔥 biar sejajar dengan Search
}

@media (max-width: 767px) {
  .filter-container ::v-deep .el-button {
    display: flex;
    align-items: center;
    justify-content: center; // 🔥 ini yang bikin center
    text-align: center;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
  }

  .filter-container ::v-deep .el-button [class*="el-icon"] {
    display: inline-flex;
    align-items: center;
  }
}

@media (max-width: 767px) {
  /* 🔥 paksa isi tombol center */
  ::v-deep .el-button {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100%;
  }

  /* 🔥 center isi dalam button */
  ::v-deep .el-button > span {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100%;
  }

  /* 🔥 rapihin icon */
  ::v-deep .el-button i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 6px;
  }
}

// Perbaikan Layout Mobile
@media (max-width: 768px) {
  .app-container {
    padding: 10px;
  }

  .header-card {
    .header-content {
      flex-direction: column; // Logo dan teks jadi atas-bawah
      text-align: center;
      gap: 15px;
    }

    .logo_file {
      height: 50px;
    }

    .header-text h1 {
      font-size: 20px;
    }
  }

  // Row input dan tombol tambah
  .el-col {
    width: 100% !important; // Input dan tombol jadi full width
    margin-bottom: 10px;
  }

  .text-right {
    text-align: left;
    float: none;

    .el-button {
      width: 100%; // Tombol "Tambah" jadi lebar penuh
    }
  }

  // Dialog lebar penuh di mobile
  ::v-deep .el-dialog {
    width: 95% !important;
    margin-top: 5vh !important;
  }
}
</style>
