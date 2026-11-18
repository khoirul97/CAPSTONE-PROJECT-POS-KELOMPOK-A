<template>
  <div class="laporan-kategori">
    <el-card shadow="never">

      <!-- =====================================================
           HEADER
           ===================================================== -->

      <div slot="header" class="header">
        <div>
          <div class="title">
            Laporan Penjualan Per Kategori
          </div>

          <div class="subtitle">
            Ringkasan penjualan berdasarkan kategori produk
          </div>
        </div>
      </div>

      <!-- =====================================================
           FILTER
           ===================================================== -->

      <div class="filter-container">

        <!-- FILTER BULAN -->
        <span style="display: flex; align-items: center;">Filter Bulan</span>
        <el-select
          v-model="filterBulan"
          clearable
          placeholder="Pilih Bulan"
          style="width: 160px"
          @change="pilihBulan"
        >
          <el-option
            v-for="b in daftarBulan"
            :key="b.value"
            :label="b.label"
            :value="b.value"
          />
        </el-select>

        <el-date-picker
          v-model="tanggal"
          type="daterange"
          range-separator="-"
          start-placeholder="Tanggal Mulai"
          end-placeholder="Tanggal Akhir"
          format="dd-MM-yyyy"
          value-format="yyyy-MM-dd"
          class="filter-date"
          @change="pilihTanggalManual"
        />

        <el-input
          v-model="keyword"
          clearable
          placeholder="Cari kategori..."
          prefix-icon="el-icon-search"
          class="filter-keyword"
          @keyup.enter.native="handleFilter"
        />

        <el-button
          type="primary"
          icon="el-icon-search"
          @click="handleFilter"
        >
          Cari
        </el-button>

        <el-button
          icon="el-icon-refresh"
          @click="resetFilter"
        >
          Reset
        </el-button>

      </div>

      <!-- PERIODE -->
      <div
        v-if="tanggal && tanggal.length === 2"
        class="periode-laporan"
      >
        <span class="periode-label">
          Periode:
        </span>

        <strong>
          {{ formatPeriode(tanggal[0], tanggal[1]) }}
        </strong>
      </div>

      <!-- =====================================================
           SUMMARY
           ===================================================== -->

      <el-row
        :gutter="12"
        class="summary-row"
      >

        <el-col :xs="24" :sm="12" :md="6">
          <div class="summary-card">
            <div class="summary-icon">
              <i class="el-icon-collection-tag" />
            </div>

            <div class="summary-content">
              <div class="summary-label">
                Total Kategori
              </div>

              <div class="summary-value">
                {{ summary.total_kategori }}
              </div>
            </div>
          </div>
        </el-col>

        <el-col :xs="24" :sm="12" :md="6">
          <div class="summary-card">
            <div class="summary-icon">
              <i class="el-icon-goods" />
            </div>

            <div class="summary-content">
              <div class="summary-label">
                Total Produk
              </div>

              <div class="summary-value">
                {{ summary.total_produk }}
              </div>
            </div>
          </div>
        </el-col>

        <el-col :xs="24" :sm="12" :md="6">
          <div class="summary-card">
            <div class="summary-icon">
              <i class="el-icon-shopping-cart-full" />
            </div>

            <div class="summary-content">
              <div class="summary-label">
                Total Terjual
              </div>

              <div class="summary-value">
                {{ summary.total_terjual }}
              </div>
            </div>
          </div>
        </el-col>

        <el-col :xs="24" :sm="12" :md="6">
          <div class="summary-card">
            <div class="summary-icon">
              <i class="el-icon-money" />
            </div>

            <div class="summary-content">
              <div class="summary-label">
                Total Omzet
              </div>

              <div class="summary-value">
                {{ formatRupiah(summary.total_omzet) }}
              </div>
            </div>
          </div>
        </el-col>

      </el-row>

      <!-- =====================================================
           TABLE
           ===================================================== -->

      <el-table
        v-loading="loading"
        :data="list"
        border
        stripe
        fit
        highlight-current-row
        empty-text="Tidak ada data"
        class="kategori-table"
      >

        <el-table-column
          type="index"
          label="No"
          width="60"
          align="center"
        />

        <el-table-column
          prop="kategori"
          label="Kategori"
          min-width="220"
        />

        <el-table-column
          prop="total_produk"
          label="Produk"
          width="120"
          align="center"
        />

        <el-table-column
          prop="total_terjual"
          label="Terjual"
          width="120"
          align="right"
        />

        <el-table-column
          prop="total_transaksi"
          label="Transaksi"
          width="120"
          align="right"
        />

        <el-table-column
          label="Omzet"
          min-width="160"
          align="right"
        >
          <template slot-scope="{ row }">
            {{ formatRupiah(row.total_omzet) }}
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
  </div>
</template>

<script>
import { getLaporanKategori } from '@/api/laporan';

export default {
  name: 'LaporanKategori',

  data() {
    return {
      loading: false,

      list: [],

      page: 1,
      limit: 15,
      total: 0,

      keyword: '',

      tanggal: [],

      filterBulan: null,

      daftarBulan: [
        { value: 1, label: 'Januari' },
        { value: 2, label: 'Februari' },
        { value: 3, label: 'Maret' },
        { value: 4, label: 'April' },
        { value: 5, label: 'Mei' },
        { value: 6, label: 'Juni' },
        { value: 7, label: 'Juli' },
        { value: 8, label: 'Agustus' },
        { value: 9, label: 'September' },
        { value: 10, label: 'Oktober' },
        { value: 11, label: 'November' },
        { value: 12, label: 'Desember' },
      ],

      summary: {
        total_kategori: 0,
        total_produk: 0,
        total_terjual: 0,
        total_omzet: 0,
      },
    };
  },

  created() {
    const today = new Date().toISOString().slice(0, 10);
    this.tanggal = [today, today];
    this.getList();
  },

  methods: {
    async getList() {
      this.loading = true;

      try {
        const params = {
          page: this.page,
          limit: this.limit,
          keyword: this.keyword,
        };

        if (this.tanggal && this.tanggal.length === 2) {
          params.dari = this.tanggal[0];
          params.sampai = this.tanggal[1];
        }

        const response = await getLaporanKategori(params);
        const data = response.data || response;

        this.list = data.data || [];
        this.total = Number(data.total || 0);

        if (data.summary) {
          this.summary = {
            total_kategori: Number(data.summary.total_kategori || 0),
            total_produk: Number(data.summary.total_produk || 0),
            total_terjual: Number(data.summary.total_terjual || 0),
            total_omzet: Number(data.summary.total_omzet || 0),
          };
        } else {
          this.calculateSummary();
        }
      } catch (error) {
        this.list = [];
        this.total = 0;
        this.$message({
          type: 'error',
          message: 'Gagal memuat laporan kategori',
        });
      } finally {
        this.loading = false;
      }
    },

    calculateSummary() {
      this.summary = {
        total_kategori: this.list.length,

        total_produk: this.list.reduce(
          (total, item) =>
            total + Number(item.total_produk || 0),
          0
        ),

        total_terjual: this.list.reduce(
          (total, item) =>
            total + Number(item.total_terjual || 0),
          0
        ),

        total_omzet: this.list.reduce(
          (total, item) =>
            total + Number(item.total_omzet || 0),
          0
        ),
      };
    },

    handleFilter() {
      this.page = 1;
      this.getList();
    },

    resetFilter() {
      this.keyword = '';
      this.tanggal = [];
      this.page = 1;

      this.getList();
    },

    handlePageChange(page) {
      this.page = page;
      this.getList();
    },

    formatRupiah(value) {
      const number = Number(value || 0);

      return `Rp ${number.toLocaleString(
        'id-ID'
      )}`;
    },

    // =====================================================
    // TODAY
    // =====================================================

    getToday() {
      const date = new Date();
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },

    formatPeriode(dari, sampai) {
      if (!dari || !sampai) {
        return '-';
      }

      const format = (value) => {
        const parts = value.split('-');

        if (parts.length !== 3) {
          return value;
        }

        const tahun = Number(parts[0]);
        const bulan = Number(parts[1]);
        const hari = Number(parts[2]);

        const namaBulan = [
          'Januari',
          'Februari',
          'Maret',
          'April',
          'Mei',
          'Juni',
          'Juli',
          'Agustus',
          'September',
          'Oktober',
          'November',
          'Desember',
        ];

        return `${hari} ${namaBulan[bulan - 1]} ${tahun}`;
      };

      return `${format(dari)} - ${format(sampai)}`;
    },

    // =====================================================
    // FILTER BULAN
    // =====================================================

    pilihBulan(bulan) {
      if (!bulan) {
        // Kalau dikosongkan, reset ke hari ini
        const today = this.getToday();
        this.tanggal = [today, today];
        this.page = 1;
        this.getList();
        return;
      }

      const now = new Date();
      const tahun = now.getFullYear();

      // Awal bulan
      const awal = new Date(tahun, bulan - 1, 1);
      // Akhir bulan (hari ke-0 dari bulan berikutnya)
      const akhir = new Date(tahun, bulan, 0);

      const format = (d) => {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
      };

      this.tanggal = [format(awal), format(akhir)];
      this.page = 1;
      this.getList();
    },

    pilihTanggalManual() {
      // Kalau user memilih range manual, reset pilihan bulan
      this.filterBulan = null;
      this.page = 1;
      this.getList();
    },
  },
};
</script>

<style lang="scss" scoped>
.laporan-kategori {
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

.filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.filter-date {
  width: 320px;
}

.filter-keyword {
  width: 240px;
}

.summary-row {
  margin-bottom: 20px;
}

.summary-card {
  display: flex;
  align-items: center;
  min-height: 90px;
  padding: 16px;
  margin-bottom: 12px;
  border: 1px solid #ebeef5;
  border-radius: 6px;
  background: #fff;
}

.summary-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  margin-right: 14px;
  border-radius: 8px;
  background: #f5f7fa;
  font-size: 24px;
}

.summary-content {
  min-width: 0;
}

.summary-label {
  margin-bottom: 5px;
  color: #909399;
  font-size: 13px;
}

.summary-value {
  color: #303133;
  font-size: 20px;
  font-weight: 600;
}

.kategori-table {
  width: 100%;
}

.pagination-container {
  display: flex;
  justify-content: flex-end;
  padding-top: 20px;
}

@media (max-width: 768px) {
  .laporan-kategori {
    padding: 10px;
  }

  .filter-date,
  .filter-keyword {
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

/* =====================================================
   PERIODE LAPORAN
   ===================================================== */

.periode-laporan {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-bottom: 12px;
  padding: 10px 12px;
  background: #f5f7fa;
  border-radius: 4px;
  font-size: 13px;
  color: #606266;
  justify-content: center;
}

.periode-label {
  color: #909399;
}

.periode-laporan strong {
  color: #303133;
  font-weight: 600;
}
</style>
