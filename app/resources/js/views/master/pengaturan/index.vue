<template>
  <div class="pengaturan-page">
    <el-card
      v-loading="loading"
      shadow="never"
    >
      <div slot="header" class="header">
        <div>
          <span class="title">Pengaturan</span>
          <div class="subtitle">
            Kelola konfigurasi aplikasi POS
          </div>
        </div>

        <el-button
          type="primary"
          icon="el-icon-check"
          :loading="saving"
          @click="simpan"
        >
          Simpan Pengaturan
        </el-button>
      </div>

      <el-alert
        title="Perubahan pengaturan akan disimpan ke server."
        type="info"
        :closable="false"
        show-icon
        class="info"
      />

      <el-table
        :data="settings"
        border
        stripe
        style="width: 100%"
      >
        <el-table-column
          type="index"
          label="No"
          width="60"
          align="center"
        />

        <el-table-column
          prop="key"
          label="Key"
          width="260"
        />

        <el-table-column
          label="Tipe"
          width="130"
          align="center"
        >
          <template slot-scope="scope">
            <el-tag size="small">
              {{ scope.row.tipe || 'string' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column
          label="Value"
          min-width="400"
        >
          <template slot-scope="scope">

            <!-- JSON -->
            <el-input
              v-if="scope.row.tipe === 'json'"
              v-model="scope.row.value"
              type="textarea"
              :rows="3"
              placeholder="JSON"
            />

            <!-- BOOLEAN -->
            <el-switch
              v-else-if="scope.row.tipe === 'boolean'"
              v-model="scope.row._boolean"
              active-text="Ya"
              inactive-text="Tidak"
            />

            <!-- IMAGE / LOGO -->
            <div
              v-else-if="scope.row.tipe === 'image'"
              class="logo-setting"
            >

              <div
                v-if="scope.row.value"
                class="logo-preview"
              >
                <el-image
                  :src="'/storage/' + scope.row.value"
                  fit="contain"
                  :preview-src-list="['/storage/' + scope.row.value]"
                />
              </div>

              <div class="logo-actions">

                <el-upload
                  action=""
                  :show-file-list="false"
                  :before-upload="beforeLogoUpload"
                  :http-request="handleLogoUpload"
                >
                  <el-button
                    size="small"
                    type="primary"
                    icon="el-icon-upload2"
                    :loading="logoUploading"
                  >
                    {{ scope.row.value ? 'Ganti Logo' : 'Upload Logo' }}
                  </el-button>
                </el-upload>

                <el-button
                  v-if="scope.row.value"
                  size="small"
                  type="danger"
                  icon="el-icon-delete"
                  :loading="logoDeleting"
                  @click="hapusLogo"
                >
                  Hapus
                </el-button>

              </div>

              <div class="logo-help">
                JPG, JPEG, PNG, WEBP — maksimal 2 MB
              </div>

            </div>

            <!-- NUMBER / STRING -->
            <el-input
              v-else
              v-model="scope.row.value"
              :type="
                scope.row.tipe === 'number'
                  ? 'number'
                  : 'text'
              "
              placeholder="Value"
            />

          </template>
        </el-table-column>
      </el-table>

      <div
        v-if="!settings.length && !loading"
        class="empty"
      >
        Belum ada pengaturan.
      </div>
    </el-card>
  </div>
</template>

<script>
import {
  getPengaturan,
  updatePengaturan,
  uploadLogoKedai,
  deleteLogoKedai,
} from '@/api/master';

export default {
  name: 'Pengaturan',

  data() {
    return {
      loading: false,
      saving: false,
      settings: [],

      logoUploading: false,
      logoDeleting: false,
    };
  },

  created() {
    this.muat();
  },

  methods: {
    async muat() {
      this.loading = true;

      try {
        const res = await getPengaturan();

        const data =
          res.data && Array.isArray(res.data)
            ? res.data
            : [];

        this.settings = data.map(item => ({
          id: item.id,
          key: item.key,
          value:
            item.value === null ||
            typeof item.value === 'undefined'
              ? ''
              : item.value,
          tipe: item.tipe || 'string',
          _boolean: this.toBoolean(item.value),
        }));
      } catch (e) {
        this.tampilkanError(
          e,
          'Gagal memuat pengaturan'
        );
      } finally {
        this.loading = false;
      }
    },

    toBoolean(value) {
      return (
        value === true ||
        value === 1 ||
        value === '1' ||
        value === 'true' ||
        value === 'TRUE'
      );
    },

    buildValue(item) {
      if (item.tipe === 'image') {
        return item.value || null;
      }

      if (item.tipe === 'boolean') {
        return item._boolean ? '1' : '0';
      }

      if (item.tipe === 'number') {
        if (
          item.value === '' ||
          item.value === null ||
          typeof item.value === 'undefined'
        ) {
          return null;
        }

        return Number(item.value);
      }

      if (item.tipe === 'json') {
        return item.value || null;
      }

      return item.value === ''
        ? null
        : item.value;
    },

    async simpan() {
      this.saving = true;

      try {
        const payload = {
          settings: this.settings.map(item => ({
            key: item.key,
            value: this.buildValue(item),
            tipe: item.tipe || 'string',
          })),
        };

        const res = await updatePengaturan(payload);

        this.$message.success(
          res.data.message ||
            'Pengaturan berhasil diperbarui'
        );

        await this.muat();
      } catch (e) {
        this.tampilkanError(
          e,
          'Gagal menyimpan pengaturan'
        );
      } finally {
        this.saving = false;
      }
    },

    tampilkanError(error, fallback) {
      const message =
        error &&
        error.response &&
        error.response.data &&
        error.response.data.message
          ? error.response.data.message
          : fallback;

      this.$message.error(message);
    },

    getLogoUrl(value) {
      if (!value) {
        return '';
      }

      // Jika API sudah mengembalikan URL lengkap
      if (
        value.startsWith('http://') ||
        value.startsWith('https://')
      ) {
        return value;
      }

      // Jika berupa path /storage/...
      return value;
    },

    beforeLogoUpload(file) {
      const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
      ];

      if (!allowedTypes.includes(file.type)) {
        this.$message.error(
          'Logo harus berformat JPG, JPEG, PNG, atau WEBP.'
        );

        return false;
      }

      if (file.size / 1024 / 1024 > 2) {
        this.$message.error(
          'Ukuran logo maksimal 2 MB.'
        );

        return false;
      }

      return true;
    },

    async handleLogoUpload(options) {
      this.logoUploading = true;

      try {
        const res = await uploadLogoKedai(
          options.file
        );

        const data =
          res.data && res.data.data
            ? res.data.data
            : null;

        if (data) {
          const logo = this.settings.find(
            item => item.key === 'logo_kedai'
          );

          if (logo) {
            logo.value = data.value;
            logo.tipe = 'image';
          }
        }

        this.$message.success(
          res.data.message ||
          'Logo kedai berhasil diupload.'
        );
      } catch (e) {
        this.tampilkanError(
          e,
          'Gagal mengupload logo kedai'
        );
      } finally {
        this.logoUploading = false;
      }
    },

    async hapusLogo() {
      try {
        await this.$confirm(
          'Logo kedai akan dihapus. Lanjutkan?',
          'Konfirmasi',
          {
            type: 'warning',
          }
        );
      } catch (e) {
        return;
      }

      this.logoDeleting = true;

      try {
        const res = await deleteLogoKedai();

        const logo = this.settings.find(
          item => item.key === 'logo_kedai'
        );

        if (logo) {
          logo.value = '';
          logo.tipe = 'image';
        }

        this.$message.success(
          res.data.message ||
          'Logo kedai berhasil dihapus.'
        );
      } catch (e) {
        this.tampilkanError(
          e,
          'Gagal menghapus logo kedai'
        );
      } finally {
        this.logoDeleting = false;
      }
    },
  },
};
</script>

<style scoped>
.pengaturan-page {
  padding: 0;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title {
  font-size: 16px;
  font-weight: 600;
}

.subtitle {
  margin-top: 4px;
  font-size: 12px;
  color: #909399;
}

.info {
  margin-bottom: 16px;
}

.empty {
  padding: 40px;
  text-align: center;
  color: #909399;
}

@media (max-width: 768px) {
  .header {
    align-items: flex-start;
    flex-direction: column;
    gap: 12px;
  }
}
.logo-setting {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 14px;
}

.logo-preview {
  width: 90px;
  height: 70px;
  border: 1px solid #ebeef5;
  border-radius: 6px;
  padding: 5px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-preview .el-image {
  width: 100%;
  height: 100%;
}

.logo-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.logo-help {
  width: 100%;
  font-size: 11px;
  color: #909399;
}

@media (max-width: 768px) {
  .logo-setting {
    align-items: flex-start;
    flex-direction: column;
  }

  .logo-actions {
    width: 100%;
    flex-wrap: wrap;
  }
}
</style>
