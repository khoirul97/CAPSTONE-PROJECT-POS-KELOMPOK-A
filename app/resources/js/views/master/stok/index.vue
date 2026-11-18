<template>
  <div class="stok-page">
    <el-card shadow="never">

      <!-- =====================================================
           HEADER
           ===================================================== -->

      <div slot="header" class="header">

        <div>
          <div class="title">
            Stok Produk
          </div>

          <div class="subtitle">
            Kelola dan pantau persediaan produk
          </div>
        </div>

        <div class="header-actions">

          <el-button
            v-permission="'manage stok'"
            type="primary"
            icon="el-icon-plus"
            @click="openStokMasuk"
          >
            Stok Masuk
          </el-button>

          <el-button
            v-permission="'manage stok'"
            icon="el-icon-edit"
            @click="openPenyesuaian"
          >
            Penyesuaian
          </el-button>

        </div>

      </div>

      <!-- =====================================================
           FILTER
           ===================================================== -->

      <div class="filter-container">

        <el-input
          v-model="keyword"
          clearable
          placeholder="Cari kode / nama produk..."
          prefix-icon="el-icon-search"
          class="search-input"
          @keyup.enter.native="handleSearch"
        />

        <el-button
          type="primary"
          icon="el-icon-search"
          @click="handleSearch"
        >
          Cari
        </el-button>

        <el-button
          icon="el-icon-refresh"
          @click="resetSearch"
        >
          Reset
        </el-button>

      </div>

      <!-- =====================================================
           TABLE
           ===================================================== -->

      <el-table
        v-loading="loading"
        :data="list"
        border
        stripe
        fit
        empty-text="Tidak ada data stok"
      >

        <el-table-column
          type="index"
          label="No"
          width="60"
          align="center"
        />

        <el-table-column
          prop="kode"
          label="Kode"
          width="130"
        />

        <el-table-column
          label="Produk"
          min-width="250"
        >
          <template slot-scope="scope">
            <div class="produk-wrapper">
              <el-image
                v-if="scope.row.gambar"
                :src="'/storage/' + scope.row.gambar"
                fit="cover"
                class="produk-image"
                :preview-src-list="['/storage/' + scope.row.gambar]"
              />

              <div class="produk-info">
                <strong>
                  {{ scope.row.nama || '-' }}
                </strong>

                <small>
                  {{ scope.row.kode || '-' }}
                </small>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column
          label="Kategori"
          min-width="150"
        >
          <template slot-scope="{ row }">
            {{ row.kategori.nama || '-' }}
          </template>
        </el-table-column>

        <el-table-column
          label="Stok"
          width="120"
          align="right"
        >
          <template slot-scope="{ row }">
            <span :class="getStokClass(row)">
              {{ formatNumber(row.stok) }}
              {{ row.satuan || '' }}
            </span>
          </template>
        </el-table-column>

        <el-table-column
          label="Stok Minimal"
          width="130"
          align="right"
        >
          <template slot-scope="{ row }">
            {{ formatNumber(row.stok_minimal) }}
            {{ row.satuan || '' }}
          </template>
        </el-table-column>

        <el-table-column
          label="Status"
          width="130"
          align="center"
        >
          <template slot-scope="{ row }">

            <el-tag
              v-if="isHabis(row)"
              type="danger"
              size="small"
            >
              Habis
            </el-tag>

            <el-tag
              v-else-if="isMinimum(row)"
              type="warning"
              size="small"
            >
              Minimum
            </el-tag>

            <el-tag
              v-else
              type="success"
              size="small"
            >
              Aman
            </el-tag>

          </template>
        </el-table-column>

      </el-table>

      <!-- =====================================================
           PAGINATION
           ===================================================== -->

      <div class="pagination-container">

        <el-pagination
          background
          layout="total, prev, pager, next, jumper"
          :current-page="page"
          :page-size="limit"
          :total="total"
          @current-change="handlePageChange"
        />

      </div>

    </el-card>

    <!-- =======================================================
         STOK MASUK
         ======================================================= -->

    <el-dialog
      title="Stok Masuk"
      :visible.sync="dialogStokMasuk"
      width="500px"
      @closed="resetForm"
    >

      <el-form
        ref="stokMasukForm"
        :model="form"
        :rules="rules"
        label-width="130px"
      >

        <el-form-item
          label="Produk"
          prop="produk_id"
        >
          <el-select
            v-model="form.produk_id"
            filterable
            placeholder="Pilih produk"
            style="width: 100%;"
          >
            <el-option
              v-for="item in produkOptions"
              :key="item.id"
              :label="getProdukLabel(item)"
              :value="item.id"
            />
          </el-select>
        </el-form-item>

        <el-form-item
          label="Jumlah"
          prop="jumlah"
        >
          <el-input-number
            v-model="form.jumlah"
            :min="1"
            :precision="0"
            controls-position="right"
            style="width: 100%;"
          />
        </el-form-item>

        <el-form-item
          label="Keterangan"
          prop="keterangan"
        >
          <el-input
            v-model="form.keterangan"
            type="textarea"
            :rows="3"
            maxlength="255"
            show-word-limit
            placeholder="Keterangan stok masuk"
          />
        </el-form-item>

      </el-form>

      <div slot="footer">

        <el-button @click="dialogStokMasuk = false">
          Batal
        </el-button>

        <el-button
          v-permission="'manage stok'"
          type="primary"
          :loading="submitLoading"
          @click="submitStokMasuk"
        >
          Simpan
        </el-button>

      </div>

    </el-dialog>

    <!-- =======================================================
         PENYESUAIAN STOK
         ======================================================= -->

    <el-dialog
      title="Penyesuaian Stok"
      :visible.sync="dialogPenyesuaian"
      width="500px"
      @closed="resetForm"
    >

      <el-form
        ref="penyesuaianForm"
        :model="form"
        :rules="rulesPenyesuaian"
        label-width="130px"
      >

        <el-form-item
          label="Produk"
          prop="produk_id"
        >
          <el-select
            v-model="form.produk_id"
            filterable
            placeholder="Pilih produk"
            style="width: 100%;"
          >
            <el-option
              v-for="item in produkOptions"
              :key="item.id"
              :label="getProdukLabel(item)"
              :value="item.id"
            />
          </el-select>
        </el-form-item>

        <el-form-item
          label="Stok Aktual"
          prop="jumlah"
        >
          <el-input-number
            v-model="form.jumlah"
            :min="0"
            :precision="0"
            controls-position="right"
            style="width: 100%;"
          />
        </el-form-item>

        <el-form-item
          label="Keterangan"
          prop="keterangan"
        >
          <el-input
            v-model="form.keterangan"
            type="textarea"
            :rows="3"
            maxlength="255"
            show-word-limit
            placeholder="Alasan penyesuaian stok"
          />
        </el-form-item>

      </el-form>

      <div slot="footer">

        <el-button @click="dialogPenyesuaian = false">
          Batal
        </el-button>

        <el-button
          v-permission="'manage stok'"
          type="primary"
          :loading="submitLoading"
          @click="submitPenyesuaian"
        >
          Simpan
        </el-button>

      </div>

    </el-dialog>

  </div>
</template>

<script>
import {
  stokMasuk,
  penyesuaianStok,
} from '@/api/stok';

import {
  listProduk,
} from '@/api/produk';

export default {
  name: 'Stok',

  data() {
    return {
      loading: false,
      submitLoading: false,

      list: [],

      page: 1,
      limit: 20,
      total: 0,

      keyword: '',

      produkOptions: [],

      dialogStokMasuk: false,
      dialogPenyesuaian: false,

      form: {
        produk_id: null,
        jumlah: 0,
        keterangan: '',
      },

      rules: {
        produk_id: [
          {
            required: true,
            message: 'Produk wajib dipilih',
            trigger: 'change',
          },
        ],
        jumlah: [
          {
            required: true,
            message: 'Jumlah wajib diisi',
            trigger: 'blur',
          },
        ],
      },

      rulesPenyesuaian: {
        produk_id: [
          {
            required: true,
            message: 'Produk wajib dipilih',
            trigger: 'change',
          },
        ],
        jumlah: [
          {
            required: true,
            message: 'Stok aktual wajib diisi',
            trigger: 'blur',
          },
        ],
      },
    };
  },

  created() {
    this.getList();
  },

  methods: {
    // =====================================================
    // LIST STOK PRODUK
    // =====================================================
    async getList() {
      this.loading = true;

      try {
        const response = await listProduk({
          page: this.page,
          limit: this.limit,
          q: this.keyword || undefined,
          is_active: 1,
        });

        const paginator = response.data.data !== undefined
          ? response.data
          : response;

        this.list = Array.isArray(paginator.data) ? paginator.data : [];
        this.total = Number(paginator.total || 0);
        this.page = Number(paginator.current_page || this.page);
        this.limit = Number(paginator.per_page || this.limit);
      } catch (error) {
        this.list = [];
        this.total = 0;
        this.$message.error('Gagal memuat data stok');
      } finally {
        this.loading = false;
      }
    },

    // =====================================================
    // LOAD PRODUK UNTUK DROPDOWN
    // (loop pagination karena ProdukController max per_page = 20)
    // =====================================================
    async loadProduk() {
      try {
        const all = [];
        let page = 1;
        let lastPage = 1;

        do {
          const response = await listProduk({
            page,
            limit: 20,
            is_active: 1,
          });

          const paginator = response.data.data !== undefined
            ? response.data
            : response;

          all.push(...(paginator.data || []));

          lastPage = Number(paginator.last_page || 1);
          page++;
        } while (page <= lastPage);

        this.produkOptions = all;
      } catch (error) {
        this.produkOptions = [];
        this.$message.error('Gagal memuat daftar produk');
      }
    },

    handleSearch() {
      this.page = 1;
      this.getList();
    },

    resetSearch() {
      this.keyword = '';
      this.page = 1;
      this.getList();
    },

    handlePageChange(page) {
      this.page = page;
      this.getList();
    },

    openStokMasuk() {
      this.resetForm();
      this.dialogStokMasuk = true;
      this.loadProduk();
    },

    openPenyesuaian() {
      this.resetForm();
      this.dialogPenyesuaian = true;
      this.loadProduk();
    },

    submitStokMasuk() {
      this.$refs.stokMasukForm.validate(async valid => {
        if (!valid) {
          return;
        }

        this.submitLoading = true;

        try {
          await stokMasuk({
            produk_id: this.form.produk_id,
            jumlah: this.form.jumlah,
            keterangan: this.form.keterangan,
          });

          this.$message.success('Stok berhasil ditambahkan');
          this.dialogStokMasuk = false;
          this.getList();
        } catch (error) {
          this.$message.error(
            this.getErrorMessage(error, 'Gagal menambahkan stok')
          );
        } finally {
          this.submitLoading = false;
        }
      });
    },

    submitPenyesuaian() {
      this.$refs.penyesuaianForm.validate(async valid => {
        if (!valid) {
          return;
        }

        this.submitLoading = true;

        try {
          // Backend minta "stok_baru", bukan "jumlah"
          await penyesuaianStok({
            produk_id: this.form.produk_id,
            stok_baru: this.form.jumlah,
            keterangan: this.form.keterangan,
          });

          this.$message.success('Stok berhasil disesuaikan');
          this.dialogPenyesuaian = false;
          this.getList();
        } catch (error) {
          this.$message.error(
            this.getErrorMessage(error, 'Gagal melakukan penyesuaian stok')
          );
        } finally {
          this.submitLoading = false;
        }
      });
    },

    resetForm() {
      this.form = {
        produk_id: null,
        jumlah: 0,
        keterangan: '',
      };

      this.$nextTick(() => {
        if (this.$refs.stokMasukForm) {
          this.$refs.stokMasukForm.resetFields();
        }
        if (this.$refs.penyesuaianForm) {
          this.$refs.penyesuaianForm.resetFields();
        }
      });
    },

    getProdukLabel(item) {
      if (item.kode) {
        return `${item.kode} - ${item.nama}`;
      }
      return item.nama;
    },

    isHabis(row) {
      return Number(row.stok || 0) <= 0;
    },

    isMinimum(row) {
      return (
        Number(row.stok || 0) > 0 &&
        Number(row.stok || 0) <= Number(row.stok_minimal || 0)
      );
    },

    getStokClass(row) {
      if (this.isHabis(row)) {
        return 'stok-habis';
      }
      if (this.isMinimum(row)) {
        return 'stok-minimum';
      }
      return 'stok-aman';
    },

    formatNumber(value) {
      return Number(value || 0).toLocaleString('id-ID');
    },

    getErrorMessage(error, fallback) {
      if (error.response.data.message) {
        return error.response.data.message;
      }
      return fallback;
    },
  },
};
</script>

<style lang="scss" scoped>
.stok-page {
  padding: 20px;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.title {
  font-size: 18px;
  font-weight: 600;
}

.subtitle {
  margin-top: 5px;
  color: #909399;
  font-size: 13px;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.search-input {
  width: 300px;
}

.stok-aman {
  color: #67c23a;
  font-weight: 600;
}

.stok-minimum {
  color: #e6a23c;
  font-weight: 600;
}

.stok-habis {
  color: #f56c6c;
  font-weight: 600;
}

.pagination-container {
  display: flex;
  justify-content: flex-end;
  padding-top: 20px;
}

@media (max-width: 768px) {
  .stok-page {
    padding: 10px;
  }

  .header {
    display: block;
  }

  .header-actions {
    margin-top: 12px;
    flex-wrap: wrap;
  }

  .header-actions .el-button {
    flex: 1;
  }

  .search-input {
    width: 100%;
  }

  .filter-container .el-button {
    flex: 1;
  }

  .pagination-container {
    justify-content: center;
    overflow-x: auto;
  }
}

.produk-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
}

.produk-image {
  width: 42px;
  height: 42px;
  object-fit: cover;
  border: 1px solid #ebeef5;
  border-radius: 4px;
  flex-shrink: 0;
}
</style>
