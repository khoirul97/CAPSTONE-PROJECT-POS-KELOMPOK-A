<template>
  <div>
    <el-card v-if="user.name" style="border-radius: 12px;">
      <div v-if="$route.params.id && $route.params.id != $store.getters.userId" style="text-align: right;">
        <el-button v-if="registrasi.status === 'inactive'" type="success" size="small" @click="showVerifikasi"><i class="el-icon-check" /> SETUJU</el-button>
        <el-button v-if="registrasi.status === 'inactive'" type="danger" size="small" @click="showTolak"><i class="el-icon-close" /> TOLAK</el-button>
        <el-button v-if="registrasi.status === 'disabled'" type="success" size="small" @click="showAktifkan"><i class="el-icon-check" /> AKTIFKAN</el-button>
        <el-button v-if="registrasi.status === 'active'" type="danger" size="small" @click="showNonaktifkan"><i class="el-icon-close" /> NONAKTIFKAN</el-button>
      </div>
      <el-tabs v-model="activeActivity" @tab-click="handleClick">
        <el-tab-pane v-if="$route.params.id && $route.params.id != $store.getters.userId" v-loading="updating" autocomplete="off" label="Akun" name="third">
          <el-form
            ref="userRefAdmin"
            :model="user_profile_by_admin"
          >
            <el-form-item
              label="Nama (Login Pengguna)"
              prop="name"
              :rules="[
                {
                  required: true,
                  message: 'Harap masukkan nama',
                  trigger: 'blur',
                },
              ]"
            >
              <el-input v-model="user_profile_by_admin.name" v-role="['admin']" :disabled="user.roles[0] === 'admin'" />
              <el-input v-model="user_profile_by_admin.name" v-role="['petugas']" disabled />
            </el-form-item>
            <el-form-item
              label="Email (Login)"
              prop="email"
              :rules="[
                {
                  required: true,
                  message: 'Silakan masukkan alamat email',
                  trigger: 'change',
                },
                {
                  type: 'email',
                  message: 'Harap masukan alamat email yang benar',
                  trigger: ['change'],
                },
              ]"
            >
              <el-input v-model="user_profile_by_admin.email" v-role="['admin']" autocomplete="off" :disabled="user.roles[0] === 'admin'" />
              <el-input v-model="user_profile_by_admin.email" v-role="['director', 'manager']" autocomplete="off" disabled />
            </el-form-item>
            <el-form-item :label="$t('user.role')" prop="role">
              <el-select v-model="user_profile_by_admin.role" placeholder="Please select role">
                <el-option v-for="item in nonAdminRoles" :key="item" :label="item | uppercaseFirst" :value="item" />
              </el-select>
            </el-form-item>
            <el-form-item :label="'WhatsApp Number'" prop="whatsapp_number">
              <el-input v-model="user_profile_by_admin.whatsapp_number" v-role="['admin']" :disabled="user.roles[0] === 'admin'" />
            </el-form-item>
            <el-form-item v-if="!user_profile_by_admin.roles.includes('admin')" :label="'Jabatan'" prop="jabatan">
              <el-input v-model="user_profile_by_admin.jabatan" v-role="['admin']" :disabled="user.roles[0] === 'admin'" />
            </el-form-item>
            <el-form-item>
              <el-button v-role="['admin']" type="primary" :disabled="user.roles[0] === 'admin'" @click="onSubmitByAdmin">
                Update
              </el-button>
              <el-button v-role="['director', 'manager']" type="primary" disabled @click="onSubmitByAdmin">
                Update
              </el-button>
            </el-form-item>
            <el-form-item>
              <!-- <img v-if="user_profile_by_admin.avatar" :src="'/storage/avatars/' + user_profile_by_admin.avatar" width="200" height="200"> -->
              <!-- <pan-thumb :image="image" /> -->
              <!-- <br> -->
              <!-- <el-button v-role="['admin']" type="success" @click="dialog=true">
                <i class="el-icon-upload" /> Ubah Avatar Profil
              </el-button> -->
            </el-form-item>
            <el-form-item>
              <!-- <br> -->
              <el-button v-role="['admin']" type="warning" @click="dialogUpdatePasswordAdmin=true">
                <svg-icon icon-class="password" /> Ubah Password
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        <el-tab-pane v-if="$route.params.id == null" v-loading="updating" autocomplete="off" label="Akun" name="third">
          <el-form
            ref="userRefSelf"
            :model="user_profile_by_self_user"
          >
            <el-form-item
              label="Nama (Login Pengguna)"
              prop="name"
              :rules="[
                {
                  required: true,
                  message: 'Harap masukkan nama',
                  trigger: 'blur',
                },
              ]"
            >
              <el-input v-model="user_profile_by_self_user.name" :disabled="user.roles[0] === 'admin'" />
            </el-form-item>
            <el-form-item
              label="Email (Login)"
              prop="email"
              :rules="[
                {
                  required: true,
                  message: 'Silakan masukkan alamat email',
                  trigger: 'change',
                },
                {
                  type: 'email',
                  message: 'Silakan masukkan alamat email yang benar',
                  trigger: ['change'],
                },
              ]"
            >
              <el-input v-model="user_profile_by_self_user.email" autocomplete="off" :disabled="user.roles[0] === 'admin'" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" :disabled="user.roles[0] === 'admin'" @click="onSubmitBySelfUser">
                Update
              </el-button>
            </el-form-item>
            <el-form-item>
              <!-- <img v-if="user_profile_by_admin.avatar" :src="'/storage/avatars/' + user_profile_by_admin.avatar" width="200" height="200"> -->
              <!-- <pan-thumb :image="image" /> -->
              <br>
              <!-- <el-button type="success" @click="dialogUser=true">
                <i class="el-icon-upload" /> Ubah Avatar Profil
              </el-button> -->
            </el-form-item>
            <el-form-item>
              <!-- <br> -->
              <el-button type="warning" @click="dialogUpdatePasswordSelfUser=true">
                <svg-icon icon-class="password" /> Ubah Password
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
      </el-tabs>
    </el-card>

    <el-dialog width="80%" :title="'Perbarui Password'" :visible.sync="dialogUpdatePasswordAdmin">
      <div v-loading="passwordUpdating" class="form-container">
        <el-form ref="updatePasswordAdmin" :rules="rules" :model="updatePassword" label-position="left">
          <el-form-item :label="$t('user.password')" prop="password">
            <el-input v-model="updatePassword.password" show-password />
          </el-form-item>
          <el-form-item :label="$t('user.confirmPassword')" prop="confirmPassword">
            <el-input v-model="updatePassword.confirmPassword" show-password />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer" style="text-align: right;">
          <el-button @click="dialogUpdatePasswordAdmin = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updatePasswordAdmin()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
    <el-dialog width="80%" :title="'Perbarui Password'" :visible.sync="dialogUpdatePasswordSelfUser">
      <div v-loading="passwordUpdating" class="form-container">
        <el-form ref="updatePasswordSelfUser" :rules="rules" :model="updatePassword" label-position="left">
          <el-form-item :label="$t('user.password')" prop="password">
            <el-input v-model="updatePassword.password" show-password />
          </el-form-item>
          <el-form-item :label="$t('user.confirmPassword')" prop="confirmPassword">
            <el-input v-model="updatePassword.confirmPassword" show-password />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer" style="text-align: right;">
          <el-button @click="dialogUpdatePasswordSelfUser = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updatePasswordSelfUser()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Resource from '@/api/resource';
// import EmployeeResource from '@/api/employee';
import permission from '@/directive/permission'; // Permission directive
import role from '@/directive/role'; // role directive
import UserResource from '@/api/user';
// import checkPermission from '@/utils/permission'; // Permission checking
// import ImageCropper from '@/components/ImageCropper';
// import PanThumb from '@/components/PanThumb';
// import myUpload from './AvatarCropper.vue';
import ResourcePassword from '@/api/user';

const userResource = new Resource('users');
const UserResourcePassword = new ResourcePassword();
const usersResource = new UserResource();

// const employeeResource = new EmployeeResource();

export default {
  directives: { permission, role },
  // components: { VueAvatar },
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
          whatsapp_number: '',
          avatar: '',
          role: '',
          roles: [],
          employee: [
            {
              id: 0,
            },
          ],
        };
      },
    },
  },
  data() {
    var validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.updatePassword.password) {
        callback(new Error('Password tidak cocok!'));
      } else {
        callback();
      }
    };
    return {
      roles: ['admin', 'manager', 'kasir'],
      nonAdminRoles: ['admin', 'manager', 'kasir'],
      user_profile_by_admin: {
        id: null,
        name: '',
        email: '',
        whatsapp_number: '',
        avatar: '',
        jabatan: '',
        roles: [],
        role: '',
      },
      rotation: 0,
      scale: 1,
      dialog: false,
      dialogUser: false,
      imageUrl: '',
      user_profile_by_self_user: {
        id: null,
        name: '',
        email: '',
        whatsapp_number: '',
        avatar: '',
        jabatan: '',
        role: '',
      },
      activeActivity: 'third',
      carouselImages: [
        'https://cdn.laravue.dev/photo1.png',
        'https://cdn.laravue.dev/photo2.png',
        'https://cdn.laravue.dev/photo3.jpg',
        'https://cdn.laravue.dev/photo4.jpg',
      ],
      updating: false,
      dialogUpdatePasswordAdmin: false,
      dialogUpdatePasswordSelfUser: false,
      passwordUpdating: false,
      rules: {
        password: [{ required: true, message: 'Password Harus diisi', trigger: 'blur' }],
        confirmPassword: [{ validator: validateConfirmPassword, trigger: 'blur' }],
      },
      updatePassword: {
        password: '',
        confirmPassword: '',
      },
      registrasi: {
        name: '',
        email: '',
        whatsapp_number: '',
        status: '',
        role: '',
        created_at: '',
        updated_at: '',
      },
      statusAplikasi: [
        {
          id: '1',
          name: 'disabled',
          label: 'TIDAK AKTIF',
        },
        {
          id: '2',
          name: 'inactive',
          label: 'BELUM AKTIF',
        },
        {
          id: '3',
          name: 'rejected',
          label: 'DITOLAK',
        },
        {
          id: '4',
          name: 'active',
          label: 'AKTIF',
        },
      ],
      updatestatus: {
        status: '',
      },
    };
  },
  created() {
    if (this.$route.params.id !== undefined) {
      this.getUserProfileByAdmin();
    }
    this.getUserProfileBySelfUser();
    // console.log(this.$store.getters.userId, 'store');
    // console.log(this.$route.params.id, 'params');
    this.getDetail();
  },
  methods: {
    handleClick(tab, event) {
      console.log('Switching tab ', tab, event);
    },
    // async getUserProfileByAdmin() {
    //   const { data } = await userResource.get(this.$route.params.id);
    //   this.user_profile_by_admin = data;
    // },
    async getUserProfileByAdmin() {
      const { data } = await userResource.get(this.$route.params.id);

      this.user_profile_by_admin = {
        ...data,
        role: data.roles && data.roles.length ? data.roles[0] : '', // ✅ mapping penting
      };
    },
    async getUserProfileBySelfUser() {
      // const { data } = await userResource.get(this.$store.getters.userId);
      const data = await this.$store.dispatch('user/getInfo');
      this.user_profile_by_self_user = data;
      // console.log(data.id, 'ID USER');
    },
    saveAvatarProfileByAdmin() {
      var img = this.$refs.vueavatar.getImageScaled();
      this.$refs.image.src = img.toDataURL();
      var img_ref = this.$refs.image.src;

      const base64 = img_ref;
      // console.log(base64);

      var payload = {
        name: this.user_profile_by_admin.name,
        email: this.user_profile_by_admin.email,
        whatsapp_number: this.user_profile_by_admin.whatsapp_number,
        role: this.user_profile_by_admin.role,
        roles: this.user_profile_by_admin.roles,
        jabatan: this.user_profile_by_admin.jabatan,
        avatar: base64,
      };
      // console.log(resData);
      userResource
        .update(this.user_profile_by_admin.id, payload)
        .then(response => {
          this.updating = false;
          this.$message({
            message: 'Avatar Pengguna telah berhasil diperbarui',
            type: 'success',
            duration: 5 * 1000,
          });
          this.dialog = false;
          this.getUserProfileByAdmin();
          this.getUserProfileBySelfUser();
          // this.getEmployeeBySelfUser();
          this.$store.dispatch('user/getInfo');
        })
        .catch(error => {
          console.log(error);
          this.updating = false;
        });
    },
    saveAvatarProfileBySelfUser() {
      var img = this.$refs.vueavatar2.getImageScaled();
      this.$refs.image2.src = img.toDataURL();
      var img_ref = this.$refs.image2.src;

      const base64 = img_ref;
      // console.log(base64);

      var payload = {
        name: this.user_profile_by_self_user.name,
        email: this.user_profile_by_self_user.email,
        whatsapp_number: this.user_profile_by_self_user.whatsapp_number,
        roles: this.user_profile_by_self_user.roles,
        avatar: base64,
      };
      // console.log(resData);
      userResource
        .update(this.user_profile_by_self_user.id, payload)
        .then(response => {
          this.updating = false;
          this.$message({
            message: 'Avatar Pengguna telah berhasil diperbarui',
            type: 'success',
            duration: 5 * 1000,
          });
          this.dialogUser = false;
          this.$store.dispatch('user/getInfo');
        })
        .catch(error => {
          console.log(error);
          this.updating = false;
        });
    },
    onImageReady() {
      this.scale = 1;
      this.rotation = 0;
    },
    onSubmitByAdmin() {
      this.$refs.userRefAdmin.validate(valid => {
        if (!valid) {
          return;
        }

        this.updating = true;

        // 🔥 WAJIB
        this.user_profile_by_admin.roles = [this.user_profile_by_admin.role];

        // ✅ Konversi boolean ke integer 0/1 untuk backend
        const submitData = {
          ...this.user_profile_by_admin,
        };

        const formData = new FormData();

        // append field biasa
        Object.keys(submitData).forEach(key => {
          const value = submitData[key];

          // 🔥 skip null / undefined / empty string
          if (value === null || value === undefined || value === '') {
            return;
          }

          if (Array.isArray(value)) {
            value.forEach(val => {
              formData.append(`${key}[]`, val);
            });
          } else {
            formData.append(key, value ?? '');
          }
        });

        // 🔥 penting untuk Laravel PUT
        formData.append('_method', 'PUT');

        userResource
          .updateFormData(this.user_profile_by_admin.id, formData)
          .then(() => {
            this.$message({
              message: 'User berhasil diupdate',
              type: 'success',
            });
          })
          .finally(() => {
            this.updating = false;
          });
      });
    },
    onSubmitBySelfUser() {
      var payload = {
        name: this.user_profile_by_self_user.name,
        email: this.user_profile_by_self_user.email,
        whatsapp_number: this.user_profile_by_self_user.whatsapp_number,
        roles: this.user_profile_by_self_user.roles,
        // avatar: this.user_profile_by_self_user.avatar,
      };

      this.$refs.userRefSelf.validate(valid => {
        if (valid) {
          this.updating = true;
          userResource
            .update(this.user_profile_by_self_user.id, payload)
            .then(response => {
              this.updating = false;
              this.$message({
                message: 'Informasi pengguna telah berhasil diperbarui',
                type: 'success',
                duration: 5 * 1000,
              });
            })
            .catch(error => {
              console.log(error);
              this.updating = false;
            });
        }
      });
    },
    updatePasswordAdmin() {
      var payload = {
        password: this.updatePassword.password,
        confirmPassword: this.updatePassword.confirmPassword,
      };

      this.$refs.updatePasswordAdmin.validate(valid => {
        if (valid) {
          this.updating = true;
          UserResourcePassword
            .updatePassword(this.user_profile_by_admin.id, payload)
            .then(response => {
              this.updating = false;
              this.$message({
                message: 'Password telah berhasil diperbarui',
                type: 'success',
                duration: 5 * 1000,
              });
              this.dialogUpdatePasswordAdmin = false;
            })
            .catch(error => {
              console.log(error);
              this.updating = false;
            });
        }
      });
    },
    updatePasswordSelfUser() {
      var payload = {
        password: this.updatePassword.password,
        confirmPassword: this.updatePassword.confirmPassword,
      };

      this.$refs.updatePasswordSelfUser.validate(valid => {
        if (valid) {
          this.updating = true;
          UserResourcePassword
            .updatePassword(this.user_profile_by_self_user.id, payload)
            .then(response => {
              this.updating = false;
              this.$message({
                message: 'Password telah berhasil diperbarui',
                type: 'success',
                duration: 5 * 1000,
              });
              this.dialogUpdatePasswordSelfUser = false;
            })
            .catch(error => {
              console.log(error);
              this.updating = false;
            });
        }
      });
    },
    async getDetail() {
      var data = await userResource.get(this.$route.params.id);
      this.registrasi = data.data;
      // console.log('edit lib : ', this.dataPermintaan);
    },
    convertDate(datetime) {
      var options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      };
      var date = new Date(datetime).toLocaleString('id', options);
      var time = new Date(datetime).toLocaleTimeString([], { hour: '2-digit', hour12: false, minute: '2-digit' });
      return date + ' ' + time;
      // return date;
    },
    updateRegistrasi() {
      var payload = {
        name: this.registrasi.name,
        // branch_id: this.registrasi.branch_id,
        email: this.registrasi.email,
        whatsapp_number: this.registrasi.whatsapp_number,
        // position: this.registrasi.position,
      };
      this.$refs['updateRegistrasiForm'].validate((valid) => {
        if (valid) {
          this.registrasiUpdating = true;
          usersResource
            .updateRegistrasi(this.$route.params.id, payload)
            .then(response => {
              this.$message({
                message: 'Data Registrasi telah berhasil di edit.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.registrasiUpdating = false;
              // this.dialogUpdateFormVisible = false;
              this.getDetail();
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              // this.registrasiUpdating = false;
            });
        } else {
          console.log('kesalahan kirim!!');
          return false;
        }
      });
    },
    showVerifikasi() {
      this.$msgbox({
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin ingin menyetujui pendaftaran Akun ini?',
        showCancelButton: true,
        confirmButtonText: 'Setuju',
        cancelButtonText: 'Batalkan',
        center: true,
        type: 'warning',
      }).then(() => {
        const payload = {
          status: 'active',
        };

        this.Verifikasi(payload);
      }).catch(() => {
        // User canceled the action
      });
    },
    Verifikasi(payload) {
      this.statusUpdating = true;
      usersResource
        .updateStatus(this.$route.params.id, payload)
        .then(response => {
          this.$message({
            message: 'Akun berhasil di verifikasi',
            type: 'success',
            duration: 5 * 1000,
          });
          this.statusUpdating = false;
          this.getDetail();
          this.handleFilter();
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          // this.registrasiUpdating = false;
        });
    },
    showTolak() {
      this.$msgbox({
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin ingin menolak pendaftaran Akun ini?',
        showCancelButton: true,
        confirmButtonText: 'Tolak',
        cancelButtonText: 'Batalkan',
        center: true,
        type: 'warning',
      }).then(() => {
        const payload = {
          status: 'rejected',
        };

        this.Tolak(payload);
      }).catch(() => {
        // User canceled the action
      });
    },
    Tolak(payload) {
      this.statusUpdating = true;
      usersResource
        .updateStatus(this.$route.params.id, payload)
        .then(response => {
          this.$message({
            message: 'Akun berhasil ditolak',
            type: 'success',
            duration: 5 * 1000,
          });
          this.statusUpdating = false;
          this.getDetail();
          this.handleFilter();
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          // this.registrasiUpdating = false;
        });
    },
    showNonaktifkan() {
      this.$msgbox({
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin ingin menonaktifkan Akun ini?',
        showCancelButton: true,
        confirmButtonText: 'Nonaktifkan',
        cancelButtonText: 'Batalkan',
        center: true,
        type: 'warning',
      }).then(() => {
        const payload = {
          status: 'disabled',
          force_logout: true,
          active: false,
        };

        this.Nonaktifkan(payload);
      }).catch(() => {
        // User canceled the action
      });
    },
    Nonaktifkan(payload) {
      this.statusUpdating = true;
      usersResource
        .updateStatus(this.$route.params.id, payload)
        .then(response => {
          this.$message({
            message: 'Akun berhasil di nonaktifkan',
            type: 'success',
            duration: 5 * 1000,
          });
          this.statusUpdating = false;
          this.getDetail();
          this.handleFilter();
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          // this.registrasiUpdating = false;
        });
    },
    showAktifkan() {
      this.$msgbox({
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin ingin mengaktifkan kembali Akun ini?',
        showCancelButton: true,
        confirmButtonText: 'Aktifkan',
        cancelButtonText: 'Batalkan',
        center: true,
        type: 'warning',
      }).then(() => {
        const payload = {
          status: 'active',
          force_logout: false,
          active: true,
        };

        this.Aktifkan(payload);
      }).catch(() => {
        // User canceled the action
      });
    },
    Aktifkan(payload) {
      this.statusUpdating = true;
      usersResource
        .updateStatus(this.$route.params.id, payload)
        .then(response => {
          this.$message({
            message: 'Akun berhasil di aktifkan kembali',
            type: 'success',
            duration: 5 * 1000,
          });
          this.statusUpdating = false;
          this.getDetail();
          this.handleFilter();
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          // this.registrasiUpdating = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.user-activity {
  .user-block {
    .username, .description {
      display: block;
      margin-left: 50px;
      padding: 2px 0;
    }
    img {
      width: 40px;
      height: 40px;
      float: left;
    }
    :after {
      clear: both;
    }
    .img-circle {
      border-radius: 50%;
      border: 2px solid #d2d6de;
      padding: 2px;
    }
    span {
      font-weight: 500;
      font-size: 12px;
    }
  }
  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;
    .image {
      width: 100%;
    }
    .user-images {
      padding-top: 20px;
    }
  }
  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;
    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }
    .link-black {
      &:hover, &:focus {
        color: #999;
      }
    }
  }
  .el-carousel__item h3 {
    color: #475669;
    font-size: 14px;
    opacity: 0.75;
    line-height: 200px;
    margin: 0;
  }

  .el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
  }

  .el-carousel__item:nth-child(2n+1) {
    background-color: #d3dce6;
  }
}
</style>

<style scoped>
.avatar-uploader .avatar {
  width: 178px;
  height: 178px;
  display: block;
}
</style>
