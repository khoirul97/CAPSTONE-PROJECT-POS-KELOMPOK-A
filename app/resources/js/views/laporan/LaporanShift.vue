<template>
  <div class="laporan-shift">
    <el-card shadow="never">
      <div slot="header" class="header">
        <span>Laporan Per Shift</span>

        <div class="filter">
          <el-date-picker
            v-model="tanggal"
            type="daterange"
            range-separator="-"
            start-placeholder="Dari"
            end-placeholder="Sampai"
            value-format="yyyy-MM-dd"
            @change="muat"
          />

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

      <el-row :gutter="12" class="ringkasan">
        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Total Shift
            </div>

            <div class="stat-value">
              {{ ringkasan.total_sesi || 0 }}
            </div>
          </el-card>
        </el-col>

        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Total Penjualan
            </div>

            <div class="stat-value">
              Rp {{ formatRupiah(ringkasan.total_penjualan) }}
            </div>
          </el-card>
        </el-col>

        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Total Tunai
            </div>

            <div class="stat-value">
              Rp {{ formatRupiah(ringkasan.total_tunai) }}
            </div>
          </el-card>
        </el-col>

        <el-col :span="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              Total Selisih
            </div>

            <div
              class="stat-value"
              :class="selisihClass(ringkasan.total_selisih)"
            >
              Rp {{ formatRupiah(ringkasan.total_selisih) }}
            </div>
          </el-card>
        </el-col>
      </el-row>

      <el-table
        v-loading="loading"
        :data="detail"
        border
        stripe
        style="width: 100%"
      >
        <el-table-column
          prop="kode"
          label="Kode Sesi"
          width="180"
        />

        <el-table-column
          prop="kasir"
          label="Kasir"
          width="140"
        />

        <el-table-column
          prop="penutup"
          label="Penutup"
          width="140"
        />

        <el-table-column
          label="Waktu Buka"
          width="170"
        >
          <template slot-scope="s">
            {{ formatDateTime(s.row.waktu_buka) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Waktu Tutup"
          width="170"
        >
          <template slot-scope="s">
            {{ formatDateTime(s.row.waktu_tutup) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Status"
          width="90"
          align="center"
        >
          <template slot-scope="s">
            <el-tag
              :type="
                s.row.status === 'open'
                  ? 'success'
                  : 'info'
              "
            >
              {{ s.row.status }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column
          label="Kas Awal"
          align="right"
          min-width="140"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.kas_awal) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Total Transaksi"
          align="center"
          width="120"
        >
          <template slot-scope="s">
            {{ s.row.total_transaksi || 0 }}
          </template>
        </el-table-column>

        <el-table-column
          label="Total Penjualan"
          align="right"
          min-width="150"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.total_penjualan) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Total Tunai"
          align="right"
          min-width="150"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.total_tunai) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Kas Sistem"
          align="right"
          min-width="150"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.kas_akhir_sistem) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Kas Fisik"
          align="right"
          min-width="150"
        >
          <template slot-scope="s">
            <span v-if="s.row.kas_akhir_fisik !== null">
              Rp {{ formatRupiah(s.row.kas_akhir_fisik) }}
            </span>

            <span v-else>
              -
            </span>
          </template>
        </el-table-column>

        <el-table-column
          label="Selisih"
          align="right"
          min-width="150"
        >
          <template slot-scope="s">
            <span :class="selisihClass(s.row.selisih)">
              Rp {{ formatRupiah(s.row.selisih) }}
            </span>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { laporanPerShift } from '@/api/laporan';

export default {
  name: 'LaporanShift',

  data() {
    return {
      tanggal: [],
      ringkasan: {
        total_sesi: 0,
        total_transaksi: 0,
        total_penjualan: 0,
        total_tunai: 0,
        total_non_tunai: 0,
        total_selisih: 0,
      },
      detail: [],
      loading: false,
    };
  },

  created() {
    const today = new Date()
      .toISOString()
      .slice(0, 10);

    this.tanggal = [
      today,
      today,
    ];

    this.muat();
  },

  methods: {
    async muat() {
      if (
        !this.tanggal ||
        this.tanggal.length < 2
      ) {
        return;
      }

      this.loading = true;

      try {
        const res = await laporanPerShift({
          dari: this.tanggal[0],
          sampai: this.tanggal[1],
        });

        /*
         * Response Laravel:
         *
         * {
         *   data: {
         *     periode: {},
         *     ringkasan: {},
         *     detail: []
         *   }
         * }
         */
        const result = res.data;

        this.ringkasan = result.ringkasan || {
          total_sesi: 0,
          total_transaksi: 0,
          total_penjualan: 0,
          total_tunai: 0,
          total_non_tunai: 0,
          total_selisih: 0,
        };

        this.detail = result.detail || [];
      } catch (e) {
        this.ringkasan = {
          total_sesi: 0,
          total_transaksi: 0,
          total_penjualan: 0,
          total_tunai: 0,
          total_non_tunai: 0,
          total_selisih: 0,
        };

        this.detail = [];

        this.$message.error(
          e &&
          e.response &&
          e.response.data &&
          e.response.data.message
            ? e.response.data.message
            : 'Gagal memuat laporan'
        );
      } finally {
        this.loading = false;
      }
    },

    selisihClass(v) {
      const n = Number(v || 0);

      if (n < 0) {
        return 'text-red';
      }

      if (n > 0) {
        return 'text-green';
      }

      return '';
    },

    formatRupiah(v) {
      return Number(v || 0).toLocaleString(
        'id-ID'
      );
    },

    formatDateTime(v) {
      if (!v) {
        return '-';
      }

      return new Date(v).toLocaleString(
        'id-ID'
      );
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
  },
};
</script>

<style scoped>
.laporan-shift {
  width: 100%;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.filter {
  display: flex;
  gap: 8px;
  align-items: center;
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

.ringkasan {
  margin-bottom: 16px;
}

.stat {
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #909399;
}

.stat-value {
  font-size: 18px;
  font-weight: 700;
  margin-top: 6px;
}

.text-red {
  color: #f56c6c;
}

.text-green {
  color: #67c23a;
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    align-items: flex-start;
  }

  .filter {
    width: 100%;
    flex-wrap: wrap;
  }

  .filter .el-date-editor {
    width: 100%;
  }

  .filter .el-button {
    width: 100%;
  }
}
</style>
