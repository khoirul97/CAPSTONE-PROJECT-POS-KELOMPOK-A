<template>
  <div class="laporan-stok">
    <el-card shadow="never">
      <!-- HEADER -->
      <div slot="header" class="header">
        <div class="title">
          Laporan Stok
        </div>

        <div class="filter">
          <el-input
            v-model="keyword"
            clearable
            placeholder="Cari kode atau nama produk"
            prefix-icon="el-icon-search"
            style="width: 260px"
            @keyup.enter.native="muat"
            @clear="muat"
          />

          <el-select
            v-model="kategoriId"
            clearable
            filterable
            placeholder="Semua Kategori"
            style="width: 180px"
            @change="muat"
          >
            <el-option
              v-for="kategori in kategoriList"
              :key="kategori.id"
              :label="kategori.nama"
              :value="kategori.id"
            />
          </el-select>

          <el-button
            type="primary"
            icon="el-icon-search"
            :loading="loading"
            @click="muat"
          >
            Tampilkan
          </el-button>
        </div>
      </div>

      <!-- RINGKASAN -->
      <el-row :gutter="12" class="ringkasan">
        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Total Produk
            </div>
            <div class="stat-value">
              {{ formatNumber(ringkasan.total_produk) }}
            </div>
          </el-card>
        </el-col>

        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Total Stok
            </div>
            <div class="stat-value">
              {{ formatNumber(ringkasan.total_stok) }}
            </div>
          </el-card>
        </el-col>

        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Stok Menipis
            </div>
            <div class="stat-value menipis">
              {{ formatNumber(ringkasan.stok_menipis) }}
            </div>
          </el-card>
        </el-col>

        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Stok Habis
            </div>
            <div class="stat-value habis">
              {{ formatNumber(ringkasan.stok_habis) }}
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!-- TABEL STOK -->
      <div v-loading="loading" class="table-container">
        <el-table
          :data="produk"
          border
          stripe
          size="small"
          empty-text="Tidak ada data stok"
        >
          <el-table-column
            type="index"
            label="No"
            width="60"
            align="center"
          />

          <el-table-column
            label="Produk"
            min-width="250"
          >
            <template slot-scope="scope">
              <div class="produk-wrapper">
                <!-- <img
                  :src="'/storage/' + scope.row.gambar"
                  class="produk-image"
                  alt="Produk"
                > -->

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
            <template slot-scope="scope">
              {{ scope.row.kategori_nama || scope.row.kategori || '-' }}
            </template>
          </el-table-column>

          <el-table-column
            label="Satuan"
            width="100"
            align="center"
          >
            <template slot-scope="scope">
              {{ scope.row.satuan || '-' }}
            </template>
          </el-table-column>

          <el-table-column
            label="Harga Beli"
            min-width="130"
            align="right"
          >
            <template slot-scope="scope">
              Rp {{ formatRupiah(scope.row.harga_beli) }}
            </template>
          </el-table-column>

          <el-table-column
            label="Harga Jual"
            min-width="130"
            align="right"
          >
            <template slot-scope="scope">
              Rp {{ formatRupiah(scope.row.harga_jual) }}
            </template>
          </el-table-column>

          <el-table-column
            label="Stok"
            width="100"
            align="center"
          >
            <template slot-scope="scope">
              <strong
                :class="{
                  'stok-habis': Number(scope.row.stok) <= 0,
                  'stok-menipis': isStokMenipis(scope.row),
                  'stok-aman': isStokAman(scope.row),
                }"
              >
                {{ formatNumber(scope.row.stok) }}
              </strong>
            </template>
          </el-table-column>

          <el-table-column
            label="Stok Minimum"
            width="120"
            align="center"
          >
            <template slot-scope="scope">
              {{ formatNumber(scope.row.stok_minimal) }}
            </template>
          </el-table-column>

          <el-table-column
            label="Status"
            width="120"
            align="center"
          >
            <template slot-scope="scope">
              <el-tag
                v-if="Number(scope.row.stok) <= 0"
                type="danger"
                size="mini"
              >
                Habis
              </el-tag>

              <el-tag
                v-else-if="isStokMenipis(scope.row)"
                type="warning"
                size="mini"
              >
                Menipis
              </el-tag>

              <el-tag
                v-else
                type="success"
                size="mini"
              >
                Aman
              </el-tag>
            </template>
          </el-table-column>
        </el-table>

        <!-- PAGINATION -->
        <div
          v-if="total > 0"
          class="pagination"
        >
          <el-pagination
            background
            layout="total, prev, pager, next, jumper"
            :current-page="halaman"
            :page-size="limit"
            :total="total"
            :pager-count="5"
            @current-change="ubahHalaman"
          />
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
import { laporanStok } from '@/api/laporan';
import { getKategoriStok } from '@/api/kategori';

export default {
  name: 'LaporanStok',

  data() {
    return {
      loading: false,

      keyword: '',
      kategoriId: null,

      kategoriList: [],

      produk: [],

      halaman: 1,
      limit: 20,
      total: 0,

      ringkasan: {
        total_produk: 0,
        total_stok: 0,
        stok_menipis: 0,
        stok_habis: 0,
      },
    };
  },

  created() {
    this.muatKategori();
    this.muat();
  },

  methods: {
    async muat() {
      this.halaman = 1;
      await this.ambilData();
    },

    async ambilData() {
      this.loading = true;

      try {
        const res = await laporanStok({
          keyword: this.keyword || undefined,
          kategori_id: this.kategoriId || undefined,
          page: this.halaman,
          limit: this.limit,
        });

        console.log('LAPORAN STOK:', res);

        const result = res.data || {};

        this.ringkasan = result.ringkasan || {
          total_produk: 0,
          total_stok: 0,
          stok_menipis: 0,
          stok_habis: 0,
        };

        const pagination = result.data || {};

        this.produk = pagination.data || [];
        this.halaman = pagination.current_page || 1;
        this.total = pagination.total || 0;

        if (pagination.per_page) {
          this.limit = Number(pagination.per_page);
        }
      } catch (error) {
        console.error('Gagal memuat laporan stok:', error);

        this.$message.error('Gagal memuat laporan stok');

        this.produk = [];
        this.total = 0;

        this.ringkasan = {
          total_produk: 0,
          total_stok: 0,
          stok_menipis: 0,
          stok_habis: 0,
        };
      } finally {
        this.loading = false;
      }
    },

    async muatKategori() {
      try {
        const res = await getKategoriStok({
          page: 1,
          per_page: 1000,
        });

        console.log('KATEGORI:', res);

        const result = res.data || {};

        if (Array.isArray(result)) {
          this.kategoriList = result;
          return;
        }

        if (Array.isArray(result.data)) {
          this.kategoriList = result.data;
          return;
        }

        if (
          result.data &&
          Array.isArray(result.data.data)
        ) {
          this.kategoriList = result.data.data;
          return;
        }

        this.kategoriList = [];
      } catch (error) {
        console.error('Gagal memuat kategori:', error);
        this.kategoriList = [];
      }
    },

    ubahHalaman(page) {
      this.halaman = page;
      this.ambilData();
    },

    isStokMenipis(row) {
      const stok = Number(row.stok || 0);
      const stokMinimum = Number(row.stok_minimal || 0);

      return stok > 0 && stok <= stokMinimum;
    },

    isStokAman(row) {
      const stok = Number(row.stok || 0);
      const stokMinimum = Number(row.stok_minimal || 0);

      return stok > stokMinimum;
    },

    formatRupiah(value) {
      return Number(value || 0).toLocaleString('id-ID', {
        maximumFractionDigits: 0,
      });
    },

    formatNumber(value) {
      return Number(value || 0).toLocaleString('id-ID');
    },
  },
};
</script>

<style scoped>
.laporan-stok {
  padding: 0;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.title {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
  white-space: nowrap;
}

.filter {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  flex-wrap: wrap;
}

.ringkasan {
  margin-bottom: 20px;
}

.stat {
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #909399;
}

.stat-value {
  margin-top: 6px;
  font-size: 18px;
  font-weight: 700;
  color: #303133;
}

.menipis {
  color: #e6a23c;
}

.habis {
  color: #f56c6c;
}

.table-container {
  width: 100%;
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

.produk-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.produk-info strong {
  font-size: 12px;
  color: #303133;
  word-break: break-word;
}

.produk-info small {
  margin-top: 3px;
  font-size: 11px;
  color: #909399;
}

.stok-habis {
  color: #f56c6c;
}

.stok-menipis {
  color: #e6a23c;
}

.stok-aman {
  color: #67c23a;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  padding: 16px 0 4px;
}

@media (max-width: 992px) {
  .header {
    display: block;
  }

  .title {
    margin-bottom: 12px;
  }

  .filter {
    justify-content: flex-start;
  }
}

@media (max-width: 768px) {
  .filter {
    display: flex;
    flex-direction: column;
    align-items: stretch;
  }

  .filter .el-input,
  .filter .el-select,
  .filter .el-button {
    width: 100% !important;
  }

  .stat {
    margin-bottom: 12px;
  }
}
</style>
