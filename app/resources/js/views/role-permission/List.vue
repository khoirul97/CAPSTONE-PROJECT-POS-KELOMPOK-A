<template>
  <div class="app-container" style="padding: 10px;">
    <el-card class="header-card" style="border-radius: 12px;">
      <div class="header-content">
        <img id="LOGO_COMPANY" class="logo_file" :src="logo" alt="Logo">
        <div class="header-text">
          <h1>Perizinan Aktor (Role Permission)</h1>
          <p class="subtitle">
            Kelola role dan perizinannnya untuk Dashboard manajemen website Capstone Project
          </p>
        </div>
      </div>
    </el-card>
    <el-card style="border-radius: 12px; margin-bottom: 1%;">
      <div class="table-wrapper">
        <el-table
          v-loading="loading"
          :data="list"
          fit
          stripe
          border
          class="pro-table"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266', fontWeight: 600 }"
          style="width: 100%;"
        >
          <el-table-column align="center" label="No" width="80">
            <template slot-scope="scope">
              <!-- <span>{{ scope.row.index }}</span> -->
              <el-tag type="primary" size="normal">
                <strong>{{ scope.row.index }}</strong>
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column width="150" align="center" :label="$t('table.name')">
            <template slot-scope="scope">
              <span>{{ scope.row.name | uppercaseFirst }}</span>
            </template>
          </el-table-column>

          <el-table-column align="left" :label="$t('table.description')">
            <template slot-scope="scope">
              <span>{{ scope.row.description }}</span>
            </template>
          </el-table-column>

          <el-table-column v-if="checkPermission(['manage permission'])" align="center" label="Actions" width="200">
            <template slot-scope="scope">
              <el-button v-if="scope.row.name !== 'admin'" v-permission="['manage permission']" type="primary" size="small" icon="el-icon-edit" @click="handleEditPermissions(scope.row.id);">
                {{ $t('permission.editPermission') }}
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>

    <el-dialog :visible.sync="dialogVisible" :title="'Edit Permissions - ' + currentRole.name">
      <div v-loading="dialogLoading" class="form-container">
        <div class="permissions-container">
          <div class="block">
            <el-form :model="currentRole" label-width="80px" label-position="top">
              <el-form-item label="Menus">
                <el-tree ref="menuPermissions" :data="menuPermissions" :default-checked-keys="permissionKeys(roleMenuPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
              </el-form-item>
            </el-form>
          </div>
          <div class="block">
            <el-form :model="currentRole" label-width="80px" label-position="top">
              <el-form-item label="Permissions">
                <el-tree ref="otherPermissions" :data="otherPermissions" :default-checked-keys="permissionKeys(roleOtherPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
              </el-form-item>
            </el-form>
          </div>
          <div class="clear-left" />
        </div>
        <div style="text-align:right;">
          <el-button type="danger" @click="dialogVisible=false">
            {{ $t('permission.cancel') }}
          </el-button>
          <el-button type="primary" @click="confirmPermission">
            {{ $t('permission.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Resource from '@/api/resource';
import RoleResource from '@/api/role';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive (v-permission)
import checkPermission from '@/utils/permission'; // Permission checking
import logo from '@/assets/login/logo_capstone_project.png';

const roleResource = new RoleResource();
const permissionResource = new Resource('permissions');

export default {
  name: 'RoleList',
  directives: { waves, permission },
  data() {
    return {
      currentRoleId: 1,
      list: [],
      logo: logo,
      loading: true,
      dialogLoading: false,
      dialogVisible: false,
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
    };
  },
  computed: {
    currentRole() {
      const found = this.list.find(role => role.id === this.currentRoleId);
      if (found === undefined) {
        return { name: '', permissions: [] };
      }

      return found;
    },
    rolePermissions() {
      return this.classifyPermissions(this.currentRole.permissions).all;
    },
    roleMenuPermissions() {
      return this.classifyPermissions(this.currentRole.permissions).menu;
    },
    roleOtherPermissions() {
      return this.classifyPermissions(this.currentRole.permissions).other;
    },
  },
  created() {
    this.getRoles();
    this.getPermissions();
  },

  methods: {
    checkPermission,
    async getRoles() {
      this.loading = true;
      const { data } = await roleResource.list({});
      this.list = data;
      this.list.forEach((role, index) => {
        role['index'] = index + 1;
        role['description'] = this.$t('roles.description.' + role.name);
      });
      this.loading = false;
    },

    async getPermissions() {
      const { data } = await permissionResource.list({});
      const { all, menu, other } = this.classifyPermissions(data);
      this.permissions = all;
      this.menuPermissions = menu;
      this.otherPermissions = other;
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
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name.substring(10)) };
    },

    normalizePermission(permission) {
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name), disabled: permission.name === 'manage permission' };
    },

    permissionKeys(permissions) {
      return permissions.map(permssion => permssion.id);
    },

    handleEditPermissions(id) {
      this.dialogVisible = true;
      this.currentRoleId = id;
      this.$nextTick(() => {
        this.$refs.menuPermissions.setCheckedKeys(this.permissionKeys(this.roleMenuPermissions));
        this.$refs.otherPermissions.setCheckedKeys(this.permissionKeys(this.roleOtherPermissions));
      });
    },

    confirmPermission() {
      const checkedMenu = this.$refs.menuPermissions.getCheckedKeys();
      const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
      const checkedPermissions = checkedMenu.concat(checkedOther);
      this.dialogLoading = true;

      roleResource.update(this.currentRole.id, { permissions: checkedPermissions }).then(response => {
        this.$message({
          message: 'Permissions has been updated successfully',
          type: 'success',
          duration: 5 * 1000,
        });
        this.dialogLoading = false;
        this.dialogVisible = false;
        this.getRoles();
      });
    },
  },
};
</script>

<style lang="scss" scoped>
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

.permissions-container {
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

/* ================= TABLE FIX ================= */
.table-wrapper {
  width: 100%;
  overflow-x: auto; /* Memungkinkan scroll horizontal */
  -webkit-overflow-scrolling: touch; /* Biar scroll smooth di iOS */
}

.pro-table {
  /* Pastikan lebar minimum cukup besar sehingga muncul scrollbar saat dibuka di layar kecil */
  min-width: 800px;
}

/* Memastikan elemen internal Element UI tidak memotong scroll */
::v-deep .el-table__header-wrapper,
::v-deep .el-table__body-wrapper {
  overflow: visible !important;
}

::v-deep .el-table {
  overflow: visible !important;
}

/* Optimasi Mobile */
@media (max-width: 768px) {
  .app-container {
    padding: 10px; // Mengurangi padding kontainer di mobile
  }

  .pro-table {
    min-width: 700px; // Sesuaikan dengan jumlah kolom Anda
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
