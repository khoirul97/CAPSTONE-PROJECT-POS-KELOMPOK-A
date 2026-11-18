<template>
  <div class="laporan-laba-rugi">

    <el-card shadow="never">

      <!-- =====================================================
           HEADER
           ===================================================== -->
      <div slot="header" class="header">

        <div class="title">
          Laporan Laba Rugi
        </div>

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

        <!-- PENJUALAN BERSIH -->
        <el-col :span="6">
          <el-card
            shadow="hover"
            class="stat"
          >

            <div class="stat-label">
              Penjualan Bersih
            </div>

            <div class="stat-value">
              Rp {{ formatRupiah(laporan.pendapatan.penjualan) }}
            </div>

          </el-card>
        </el-col>

        <!-- HPP -->
        <el-col :span="6">
          <el-card
            shadow="hover"
            class="stat"
          >

            <div class="stat-label">
              HPP
            </div>

            <div class="stat-value">
              Rp {{ formatRupiah(laporan.beban.hpp) }}
            </div>

          </el-card>
        </el-col>

        <!-- LABA KOTOR -->
        <el-col :span="6">
          <el-card
            shadow="hover"
            class="stat"
          >

            <div class="stat-label">
              Laba Kotor
            </div>

            <div class="stat-value laba-kotor">
              Rp {{ formatRupiah(laporan.laba_kotor) }}
            </div>

          </el-card>
        </el-col>

        <!-- LABA BERSIH -->
        <el-col :span="6">
          <el-card
            shadow="hover"
            class="stat"
          >

            <div class="stat-label">
              {{ laporan.laba_bersih >= 0
                ? 'Laba Bersih'
                : 'Rugi Bersih'
              }}
            </div>

            <div
              class="stat-value"
              :class="{
                'laba': laporan.laba_bersih >= 0,
                'rugi': laporan.laba_bersih < 0
              }"
            >
              Rp {{ formatRupiah(Math.abs(laporan.laba_bersih)) }}
            </div>

          </el-card>
        </el-col>

      </el-row>

      <!-- =====================================================
           LAPORAN
           ===================================================== -->
      <div class="laporan-container">

        <!-- ===================================================
             PENDAPATAN
             =================================================== -->
        <div class="section">

          <div class="section-title">
            PENDAPATAN
          </div>

          <!-- Penjualan Kotor -->
          <div class="row">

            <span>
              Penjualan Kotor
            </span>

            <span>
              Rp {{ formatRupiah(laporan.pendapatan.penjualan_kotor) }}
            </span>

          </div>

          <!-- Diskon -->
          <div class="row">

            <span>
              Diskon Penjualan
            </span>

            <span class="diskon">
              - Rp {{ formatRupiah(laporan.pendapatan.diskon) }}
            </span>

          </div>

          <!-- Penjualan Bersih -->
          <div class="row subtotal-row">

            <span>
              Penjualan Bersih
            </span>

            <span>
              Rp {{ formatRupiah(laporan.pendapatan.penjualan) }}
            </span>

          </div>

          <!-- Pajak -->
          <div class="row">

            <span>
              Pajak
            </span>

            <span>
              Rp {{ formatRupiah(laporan.pendapatan.pajak) }}
            </span>

          </div>

          <!-- Total Transaksi -->
          <div class="row">

            <span>
              Total Transaksi
            </span>

            <span>
              Rp {{ formatRupiah(laporan.pendapatan.total_transaksi) }}
            </span>

          </div>

          <!-- Pendapatan Lain -->
          <div class="row">

            <span>
              Pendapatan Lain-lain
            </span>

            <span>
              Rp {{ formatRupiah(laporan.pendapatan.pendapatan_lain) }}
            </span>

          </div>

          <!-- Total Pendapatan -->
          <div class="row total">

            <span>
              TOTAL PENDAPATAN
            </span>

            <span>
              Rp {{ formatRupiah(totalPendapatan) }}
            </span>

          </div>

        </div>

        <!-- ===================================================
             HPP
             =================================================== -->
        <div class="section">

          <div class="section-title">
            HARGA POKOK PENJUALAN
          </div>

          <div class="row">

            <span>
              Harga Pokok Penjualan (HPP)
            </span>

            <span>
              Rp {{ formatRupiah(laporan.beban.hpp) }}
            </span>

          </div>

          <div class="row total">

            <span>
              TOTAL HPP
            </span>

            <span>
              Rp {{ formatRupiah(laporan.beban.hpp) }}
            </span>

          </div>

        </div>

        <!-- ===================================================
             LABA KOTOR
             =================================================== -->
        <div class="section highlight">

          <div class="row big">

            <span>
              LABA KOTOR
            </span>

            <span>
              Rp {{ formatRupiah(laporan.laba_kotor) }}
            </span>

          </div>

        </div>

        <!-- ===================================================
             BEBAN OPERASIONAL
             =================================================== -->
        <div class="section">

          <div class="section-title">
            BEBAN OPERASIONAL
          </div>

          <div class="row">

            <span>
              Beban Operasional
            </span>

            <span>
              Rp {{ formatRupiah(laporan.beban.operasional) }}
            </span>

          </div>

          <div class="row">

            <span>
              Beban Lain-lain
            </span>

            <span>
              Rp {{ formatRupiah(laporan.beban.beban_lain) }}
            </span>

          </div>

          <div class="row total">

            <span>
              TOTAL BEBAN
            </span>

            <span>
              Rp {{ formatRupiah(totalBeban) }}
            </span>

          </div>

        </div>

        <!-- ===================================================
             LABA BERSIH
             =================================================== -->
        <div
          class="section laba-bersih"
          :class="{
            'rugi-section': laporan.laba_bersih < 0
          }"
        >

          <div class="row big">

            <span>
              {{ laporan.laba_bersih >= 0
                ? 'LABA BERSIH'
                : 'RUGI BERSIH'
              }}
            </span>

            <span>
              Rp {{ formatRupiah(Math.abs(laporan.laba_bersih)) }}
            </span>

          </div>

        </div>

      </div>

    </el-card>

  </div>
</template>

<script>

import {
  laporanLabaRugi,
} from '@/api/laporan';

export default {

  name: 'LaporanLabaRugi',

  data() {
    return {
      tanggal: [],

      loading: false,

      laporan: {

        periode: {
          dari: null,
          sampai: null,
        },

        pendapatan: {

          penjualan_kotor: 0,

          diskon: 0,

          penjualan: 0,

          pajak: 0,

          total_transaksi: 0,

          pendapatan_lain: 0,

        },

        beban: {

          hpp: 0,

          operasional: 0,

          beban_lain: 0,

        },

        laba_kotor: 0,

        laba_bersih: 0,

      },
    };
  },

  computed: {

    /*
     * Penjualan bersih + pendapatan lain.
     *
     * Pajak TIDAK dimasukkan ke pendapatan karena
     * pajak hanya ditampilkan sebagai informasi transaksi.
     */
    totalPendapatan() {
      return (
        Number(
          this.laporan.pendapatan.penjualan || 0
        ) +

        Number(
          this.laporan.pendapatan.pendapatan_lain || 0
        )
      );
    },

    /*
     * Total beban.
     */
    totalBeban() {
      return (
        Number(
          this.laporan.beban.hpp || 0
        ) +

        Number(
          this.laporan.beban.operasional || 0
        ) +

        Number(
          this.laporan.beban.beban_lain || 0
        )
      );
    },

  },

  created() {
    const today =
      this.getToday();

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
        const res =
          await laporanLabaRugi({

            dari:
              this.tanggal[0],

            sampai:
              this.tanggal[1],

          });

        /*
         * Response API:
         *
         * {
         *   data: {
         *     periode: {},
         *     pendapatan: {},
         *     beban: {},
         *     laba_kotor: 0,
         *     laba_bersih: 0
         *   }
         * }
         */

        const data =
          res.data.data || res.data;

        const pendapatan =
          data.pendapatan || {};

        const beban =
          data.beban || {};

        this.laporan = {

          periode:
            data.periode || {
              dari: null,
              sampai: null,
            },

          pendapatan: {

            penjualan_kotor:
              Number(
                pendapatan.penjualan_kotor || 0
              ),

            diskon:
              Number(
                pendapatan.diskon || 0
              ),

            penjualan:
              Number(
                pendapatan.penjualan || 0
              ),

            pajak:
              Number(
                pendapatan.pajak || 0
              ),

            total_transaksi:
              Number(
                pendapatan.total_transaksi || 0
              ),

            pendapatan_lain:
              Number(
                pendapatan.pendapatan_lain || 0
              ),
          },
          beban: {
            hpp:
              Number(
                beban.hpp || 0
              ),
            operasional:
              Number(
                beban.operasional || 0
              ),
            beban_lain:
              Number(
                beban.beban_lain || 0
              ),
          },
          laba_kotor:
            Number(
              data.laba_kotor || 0
            ),

          laba_bersih:
            Number(
              data.laba_bersih || 0
            ),
        };
      } catch (e) {
        console.error(e);
        this.$message.error(
          'Gagal memuat laporan laba rugi'
        );
      } finally {
        this.loading = false;
      }
    },

    formatRupiah(value) {
      return Number(
        value || 0
      ).toLocaleString(
        'id-ID',
        {
          maximumFractionDigits: 0,
        }
      );
    },

    getToday() {
      const date =
        new Date();

      const year =
        date.getFullYear();

      const month =
        String(
          date.getMonth() + 1
        ).padStart(
          2,
          '0'
        );

      const day =
        String(
          date.getDate()
        ).padStart(
          2,
          '0'
        );

      return `${year}-${month}-${day}`;
    },

    formatPeriode(dari, sampai) {
      if (
        !dari ||
        !sampai
      ) {
        return '-';
      }

      const format = (value) => {
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

        return `${hari} ${namaBulan[bulan - 1]} ${tahun}`;
      };

      return `${format(dari)} - ${format(sampai)}`;
    },

  },

};

</script>

<style scoped>

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.filter {
  display: flex;
  gap: 8px;
}

/* =====================================================
   PERIODE
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
  font-size: 18px;
  font-weight: 700;
  margin-top: 6px;
  color: #303133;
}

.laba {
  color: #67c23a;
}

.rugi {
  color: #f56c6c;
}

.laba-kotor {
  color: #409eff;
}

/* =====================================================
   CONTAINER
   ===================================================== */

.laporan-container {
  border: 1px solid #ebeef5;
  border-radius: 4px;
  overflow: hidden;
  max-width: 50%;
}

.section {
  padding: 10px 20px;
  border-bottom: 1px solid #ebeef5;
}

.section:last-child {
  border-bottom: none;
}

.section-title {
  font-size: 13px;
  font-weight: 700;
  color: #303133;
  margin-bottom: 4px;
  text-transform: uppercase;
}

/* =====================================================
   ROW
   ===================================================== */

.row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
  font-size: 13px;
  line-height: 1.4;
  color: #606266;
}

.row span:last-child {
  text-align: right;
  min-width: 180px;
}

/* =====================================================
   DISKON
   ===================================================== */

.diskon {
  color: #f56c6c;
}

/* =====================================================
   SUBTOTAL
   ===================================================== */

.subtotal-row {
  margin-top: 3px;
  padding-top: 6px;
  border-top: 1px dashed #dcdfe6;
  font-weight: 600;
  color: #303133;
}

/* =====================================================
   TOTAL
   ===================================================== */

.row.total {
  margin-top: 5px;
  padding-top: 7px;
  border-top: 1px dashed #dcdfe6;
  font-weight: 700;
  color: #303133;
}

/* =====================================================
   LABA KOTOR
   ===================================================== */

.highlight {
  background: #f5faff;
}

.highlight .row {
  color: #409eff;
}

/* =====================================================
   LABA BERSIH
   ===================================================== */

.laba-bersih {
  background: #f0f9eb;
}

.laba-bersih .row {
  color: #67c23a;
}

.rugi-section {
  background: #fef0f0;
}

.rugi-section .row {
  color: #f56c6c;
}

.big {
  font-size: 16px;
  font-weight: 700;
}

/* =====================================================
   MOBILE
   ===================================================== */

@media (max-width: 768px) {

  .header {
    display: block;
  }

  .filter {
    margin-top: 12px;
    flex-direction: column;
  }

  .filter .el-date-editor {
    width: 100% !important;
  }

  .filter .el-button {
    width: 100%;
  }

  .stat {
    margin-bottom: 12px;
  }

  .row {
    font-size: 12px;
  }

  .row span:last-child {
    min-width: 130px;
  }

  .laporan-container {
    max-width: 100%;
  }

}

</style>
