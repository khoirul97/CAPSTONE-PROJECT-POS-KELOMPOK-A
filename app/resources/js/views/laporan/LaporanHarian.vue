<template>
  <div class="laporan-harian">

    <el-card shadow="never">

      <!-- =====================================================
           HEADER
           ===================================================== -->
      <div slot="header" class="header">

        <span>
          Laporan Penjualan Harian
        </span>

        <div class="filter">

          <!-- FILTER BULAN -->
          <span style="display: flex; align-items: center;">Filter Bulan</span>
          <el-select
            v-model="filterBulan"
            clearable
            placeholder="Pilih Bulan"
            style="width: 150px"
            @change="pilihBulan"
          >
            <el-option
              v-for="b in daftarBulan"
              :key="b.value"
              :label="b.label"
              :value="b.value"
            />
          </el-select>

          <!-- TANGGAL -->
          <el-date-picker
            v-model="tanggal"
            type="daterange"
            range-separator="-"
            start-placeholder="Dari"
            end-placeholder="Sampai"
            value-format="yyyy-MM-dd"
            @change="pilihTanggalManual"
          />

          <!-- KASIR -->
          <el-select
            v-model="userId"
            clearable
            filterable
            placeholder="Semua Kasir"
            style="width: 220px"
            @change="ubahKasir"
          >
            <el-option
              v-for="kasir in daftarKasir"
              :key="kasir.id"
              :label="kasir.name"
              :value="kasir.id"
            />
          </el-select>

          <!-- TAMPILKAN -->
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

      <!-- =====================================================
           PERIODE
           ===================================================== -->
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
           RINGKASAN
           ===================================================== -->
      <el-row
        :gutter="12"
        class="ringkasan"
      >

        <el-col :span="6">

          <el-card
            shadow="hover"
            class="stat"
          >
            <div class="stat-label">
              Total Transaksi
            </div>

            <div class="stat-value">
              {{ formatNumber(ringkasan.total_transaksi) }}
            </div>
          </el-card>

        </el-col>

        <el-col :span="6">

          <el-card
            shadow="hover"
            class="stat"
          >
            <div class="stat-label">
              Total Penjualan
            </div>

            <div class="stat-value">
              Rp {{ formatRupiah(ringkasan.total_penjualan) }}
            </div>
          </el-card>

        </el-col>

        <el-col :span="6">

          <el-card
            shadow="hover"
            class="stat"
          >
            <div class="stat-label">
              Tunai
            </div>

            <div class="stat-value">
              Rp {{ formatRupiah(ringkasan.total_tunai) }}
            </div>
          </el-card>

        </el-col>

        <el-col :span="6">

          <el-card
            shadow="hover"
            class="stat"
          >
            <div class="stat-label">
              Non-Tunai
            </div>

            <div class="stat-value">
              Rp {{ formatRupiah(ringkasan.total_non_tunai) }}
            </div>
          </el-card>

        </el-col>

      </el-row>

      <!-- =====================================================
           TABEL DETAIL
           ===================================================== -->
      <el-table
        :data="detail"
        border
        stripe
        style="width: 100%"
      >

        <el-table-column
          prop="tanggal"
          label="Tanggal"
          width="120"
        />

        <el-table-column
          prop="nama_kasir"
          label="Kasir"
          min-width="150"
        />

        <el-table-column
          prop="total_transaksi"
          label="Transaksi"
          width="100"
          align="center"
        />

        <el-table-column
          label="Subtotal"
          align="right"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.total_subtotal) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Diskon"
          align="right"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.total_diskon) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Pajak"
          align="right"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.total_pajak) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Tunai"
          align="right"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.total_tunai) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Non-Tunai"
          align="right"
        >
          <template slot-scope="s">
            Rp {{ formatRupiah(s.row.total_non_tunai) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Total"
          align="right"
        >
          <template slot-scope="s">
            <b>
              Rp {{ formatRupiah(s.row.total_penjualan) }}
            </b>
          </template>
        </el-table-column>

      </el-table>

    </el-card>

  </div>
</template>

<script>

import {
  laporanHarian,
  daftarKasir,
} from '@/api/laporan';

export default {

  name: 'LaporanHarian',

  data() {
    return {

      // =====================================================
      // FILTER
      // =====================================================

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

      userId: null,

      daftarKasir: [],

      // =====================================================
      // LAPORAN
      // =====================================================

      loading: false,

      ringkasan: {
        total_transaksi: 0,
        total_penjualan: 0,
        total_tunai: 0,
        total_non_tunai: 0,
        total_diskon: 0,
        total_pajak: 0,
      },

      detail: [],
    };
  },

  // =======================================================
  // CREATED
  // =======================================================

  async created() {
    const today = this.getToday();

    this.tanggal = [
      today,
      today,
    ];

    // this.filterBulan = Number(today.split('-')[1]);

    await this.muatDaftarKasir();

    await this.muat();
  },

  methods: {

    // =====================================================
    // LOAD DAFTAR KASIR
    // Sama seperti Laporan Transaksi
    // =====================================================

    async muatDaftarKasir() {
      try {
        const res = await daftarKasir();

        console.log(
          'Response daftar kasir:',
          res
        );

        // Kemungkinan 1:
        // request mengembalikan array langsung
        if (Array.isArray(res)) {
          this.daftarKasir = res;

          return;
        }

        // Kemungkinan 2:
        // axios response -> data berisi array
        if (
          res &&
          Array.isArray(res.data)
        ) {
          this.daftarKasir = res.data;

          return;
        }

        // Kemungkinan 3:
        // response -> data -> data berisi array
        if (
          res &&
          res.data &&
          Array.isArray(res.data.data)
        ) {
          this.daftarKasir =
            res.data.data;

          return;
        }

        this.daftarKasir = [];

        console.warn(
          'Format response daftar kasir tidak dikenali:',
          res
        );
      } catch (e) {
        console.error(
          'Gagal memuat daftar kasir:',
          e
        );

        this.daftarKasir = [];

        this.$message.warning(
          'Daftar kasir gagal dimuat.'
        );
      }
    },

    // =====================================================
    // LOAD DATA LAPORAN
    // =====================================================

    async muat() {
      if (
        !this.tanggal ||
        this.tanggal.length < 2
      ) {
        return;
      }

      this.loading = true;

      try {
        const res =
          await laporanHarian({

            dari:
              this.tanggal[0],

            sampai:
              this.tanggal[1],

            user_id:
              this.userId || undefined,
          });

        const result =
          res.data || {};

        this.ringkasan =
          result.ringkasan || {

            total_transaksi: 0,

            total_penjualan: 0,

            total_tunai: 0,

            total_non_tunai: 0,

            total_diskon: 0,

            total_pajak: 0,
          };

        this.detail =
          result.detail || [];
      } catch (e) {
        console.error(
          'Gagal memuat laporan harian:',
          e
        );

        this.$message.error(
          'Gagal memuat laporan harian'
        );

        this.detail = [];

        this.ringkasan = {

          total_transaksi: 0,

          total_penjualan: 0,

          total_tunai: 0,

          total_non_tunai: 0,

          total_diskon: 0,

          total_pajak: 0,
        };
      } finally {
        this.loading = false;
      }
    },

    // =====================================================
    // PERUBAHAN KASIR
    // Sama seperti Laporan Transaksi
    // =====================================================

    async ubahKasir() {
      await this.muat();
    },

    // =====================================================
    // FORMAT RUPIAH
    // =====================================================

    formatRupiah(value) {
      return Number(
        value || 0
      ).toLocaleString(
        'id-ID'
      );
    },

    // =====================================================
    // FORMAT NUMBER
    // =====================================================

    formatNumber(value) {
      return Number(
        value || 0
      ).toLocaleString(
        'id-ID'
      );
    },

    // =====================================================
    // TODAY
    // =====================================================

    getToday() {
      const date =
        new Date();

      const year =
        date.getFullYear();

      const month =
        String(
          date.getMonth() + 1
        ).padStart(2, '0');

      const day =
        String(
          date.getDate()
        ).padStart(2, '0');

      return `${year}-${month}-${day}`;
    },

    // =====================================================
    // FORMAT PERIODE
    // =====================================================

    formatPeriode(
      dari,
      sampai
    ) {
      if (
        !dari ||
        !sampai
      ) {
        return '-';
      }

      const format = (
        value
      ) => {
        const parts =
          value.split('-');

        if (
          parts.length !== 3
        ) {
          return value;
        }

        const tahun =
          Number(parts[0]);

        const bulan =
          Number(parts[1]);

        const hari =
          Number(parts[2]);

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

        return `${hari} ${
          namaBulan[bulan - 1]
        } ${tahun}`;
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
        this.muat();
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
      this.muat();
    },

    pilihTanggalManual() {
      // Kalau user memilih range manual, reset pilihan bulan
      this.filterBulan = null;
      this.muat();
    },
  },
};

</script>

<style scoped>

.laporan-harian {
  padding: 0;
}

/* =====================================================
   HEADER
   ===================================================== */

.header {

  display: flex;

  justify-content: space-between;

  align-items: center;
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

/* =====================================================
   RINGKASAN
   ===================================================== */

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

  color: #303133;
}

/* =====================================================
   RESPONSIVE
   ===================================================== */

@media (max-width: 992px) {

  .header {

    display: block;
  }

  .filter {

    margin-top: 12px;
  }
}

@media (max-width: 768px) {

  .filter {

    flex-direction: column;

    align-items: stretch;
  }

  .filter .el-date-editor,

  .filter .el-select,

  .filter .el-button {

    width: 100% !important;
  }

  .stat {

    margin-bottom: 12px;
  }
}

</style>
