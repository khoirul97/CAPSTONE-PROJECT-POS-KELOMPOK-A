<template>
  <div class="kategori-page">
    <el-card shadow="never">
      <div slot="header" class="header">
        <div>
          <span class="title">Kategori Produk</span>
          <div class="subtitle">
            Kelola kategori produk yang digunakan dalam transaksi
          </div>
        </div>

        <el-button
          type="primary"
          icon="el-icon-plus"
          @click="bukaTambah"
        >
          Tambah Kategori
        </el-button>
      </div>

      <!-- FILTER -->
      <div class="filter">
        <el-input
          v-model="query.q"
          clearable
          placeholder="Cari nama kategori..."
          prefix-icon="el-icon-search"
          class="search-input"
          @keyup.enter.native="cari"
          @clear="cari"
        />

        <el-button
          type="primary"
          icon="el-icon-search"
          @click="cari"
        >
          Cari
        </el-button>
      </div>

      <!-- TABLE -->
      <el-table
        v-loading="loading"
        :data="data"
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
          prop="nama"
          label="Nama Kategori"
          min-width="220"
        />

        <el-table-column
          prop="deskripsi"
          label="Deskripsi"
          min-width="300"
          show-overflow-tooltip
        >
          <template slot-scope="scope">
            {{ scope.row.deskripsi || '-' }}
          </template>
        </el-table-column>

        <el-table-column
          prop="produk_count"
          label="Jumlah Produk"
          width="140"
          align="center"
        >
          <template slot-scope="scope">
            <el-tag type="info">
              {{ scope.row.produk_count || 0 }} produk
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column
          label="Dibuat"
          width="170"
        >
          <template slot-scope="scope">
            {{ formatDate(scope.row.created_at) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Aksi"
          width="150"
          fixed="right"
          align="center"
        >
          <template slot-scope="scope">
            <el-button
              type="primary"
              icon="el-icon-edit"
              circle
              size="mini"
              title="Edit"
              @click="bukaEdit(scope.row)"
            />

            <el-button
              type="danger"
              icon="el-icon-delete"
              circle
              size="mini"
              title="Hapus"
              @click="hapus(scope.row)"
            />
          </template>
        </el-table-column>
      </el-table>

      <!-- PAGINATION -->
      <div class="pagination">
        <el-pagination
          background
          layout="total, sizes, prev, pager, next, jumper"
          :current-page="query.page"
          :page-size="query.per_page"
          :page-sizes="[15, 30, 50, 100]"
          :total="total"
          @current-change="ubahHalaman"
          @size-change="ubahUkuran"
        />
      </div>
    </el-card>

    <!-- DIALOG -->
    <el-dialog
      :title="form.id ? 'Edit Kategori' : 'Tambah Kategori'"
      :visible.sync="dialogVisible"
      width="500px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="form"
        :model="form"
        :rules="rules"
        label-width="120px"
      >
        <el-form-item label="Nama" prop="nama">
          <el-input
            v-model="form.nama"
            maxlength="100"
            show-word-limit
            placeholder="Nama kategori"
          />
        </el-form-item>

        <el-form-item label="Deskripsi" prop="deskripsi">
          <el-input
            v-model="form.deskripsi"
            type="textarea"
            :rows="4"
            placeholder="Deskripsi kategori"
          />
        </el-form-item>
      </el-form>

      <span slot="footer">
        <el-button @click="dialogVisible = false">
          Batal
        </el-button>

        <el-button
          type="primary"
          :loading="saving"
          @click="simpan"
        >
          Simpan
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import {
  getKategori,
  createKategori,
  updateKategori,
  deleteKategori,
} from '@/api/master';

export default {
  name: 'Kategori',

  data() {
    return {
      loading: false,
      saving: false,

      data: [],
      total: 0,

      query: {
        page: 1,
        per_page: 15,
        q: '',
      },

      dialogVisible: false,

      form: {
        id: null,
        nama: '',
        deskripsi: '',
      },

      rules: {
        nama: [
          {
            required: true,
            message: 'Nama kategori wajib diisi',
            trigger: 'blur',
          },
          {
            max: 100,
            message: 'Maksimal 100 karakter',
            trigger: 'blur',
          },
        ],
      },
    };
  },

  created() {
    this.muat();
  },

  methods: {
    async muat() {
      this.loading = true;

      try {
        const res = await getKategori(this.query);

        this.data = res.data || [];
        this.total = res.total || 0;
      } catch (e) {
        this.tampilkanError(e, 'Gagal memuat kategori');
      } finally {
        this.loading = false;
      }
    },

    cari() {
      this.query.page = 1;
      this.muat();
    },

    ubahHalaman(page) {
      this.query.page = page;
      this.muat();
    },

    ubahUkuran(size) {
      this.query.per_page = size;
      this.query.page = 1;
      this.muat();
    },

    bukaTambah() {
      this.form = {
        id: null,
        nama: '',
        deskripsi: '',
      };

      this.dialogVisible = true;

      this.$nextTick(() => {
        if (this.$refs.form) {
          this.$refs.form.clearValidate();
        }
      });
    },

    bukaEdit(row) {
      this.form = {
        id: row.id,
        nama: row.nama || '',
        deskripsi: row.deskripsi || '',
      };

      this.dialogVisible = true;

      this.$nextTick(() => {
        if (this.$refs.form) {
          this.$refs.form.clearValidate();
        }
      });
    },

    async simpan() {
      this.$refs.form.validate(async valid => {
        if (!valid) {
          return;
        }

        this.saving = true;

        try {
          let res;

          const payload = {
            nama: this.form.nama,
            deskripsi: this.form.deskripsi || null,
          };

          if (this.form.id) {
            res = await updateKategori(this.form.id, payload);
          } else {
            res = await createKategori(payload);
          }

          this.$message.success(
            res.data.message || 'Kategori berhasil disimpan'
          );

          this.dialogVisible = false;
          this.muat();
        } catch (e) {
          this.tampilkanError(e, 'Gagal menyimpan kategori');
        } finally {
          this.saving = false;
        }
      });
    },

    hapus(row) {
      this.$confirm(
        `Hapus kategori "${row.nama}"?`,
        'Konfirmasi',
        {
          confirmButtonText: 'Hapus',
          cancelButtonText: 'Batal',
          type: 'warning',
        }
      )
        .then(async() => {
          try {
            const res = await deleteKategori(row.id);

            this.$message.success(
              res.data.message || 'Kategori berhasil dihapus'
            );

            this.muat();
          } catch (e) {
            this.tampilkanError(
              e,
              'Kategori tidak dapat dihapus'
            );
          }
        })
        .catch(() => {});
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

    formatDate(value) {
      if (!value) {
        return '-';
      }

      return new Date(value).toLocaleDateString('id-ID');
    },
  },
};
</script>

<style scoped>
.kategori-page {
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

.filter {
  display: flex;
  align-items: center;
  margin-bottom: 16px;
  gap: 8px;
}

.search-input {
  width: 320px;
}

.pagination {
  padding: 20px 0 5px;
  text-align: right;
}

@media (max-width: 768px) {
  .header {
    align-items: flex-start;
    flex-direction: column;
    gap: 12px;
  }

  .filter {
    flex-wrap: wrap;
  }

  .search-input {
    width: 100%;
  }

  .pagination {
    overflow-x: auto;
  }
}
</style>
