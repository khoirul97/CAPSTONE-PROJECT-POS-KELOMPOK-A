
<template>
  <div class="laporan-laba-rugi">
    <el-card shadow="never">
      <!-- HEADER -->
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
            format="dd-MM-yyyy"
            :clearable="false"
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
        <span class="periode-label">Periode laporan:</span>
        <strong>
          {{ formatPeriode(tanggal[0], tanggal[1]) }}
        </strong>
      </div>

      <!-- RINGKASAN -->
      <el-row :gutter="12" class="ringkasan">
        <el-col :xs="12" :sm="12" :md="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">Penjualan Bersih</div>
            <div class="stat-value">
              Rp {{ formatRupiah(laporan.pendapatan.penjualan) }}
            </div>
          </el-card>
        </el-col>

        <el-col :xs="12" :sm="12" :md="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">Harga Pokok Penjualan</div>
            <div class="stat-value">
              Rp {{ formatRupiah(laporan.beban.hpp) }}
            </div>
          </el-card>
        </el-col>

        <el-col :xs="12" :sm="12" :md="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">Total Diskon</div>
            <div
              class="stat-value"
              :class="nilaiClass(laporan.pendapatan.diskon)"
            >
              Rp {{ formatRupiah(laporan.pendapatan.diskon) }}
            </div>
          </el-card>
        </el-col>

        <el-col :xs="12" :sm="12" :md="6">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">
              {{ labaSetelahBeban >= 0 ? 'Laba' : 'Rugi' }}
            </div>
            <div
              class="stat-value"
              :class="nilaiClass(labaSetelahBeban)"
            >
              Rp {{ formatRupiah(Math.abs(labaSetelahBeban)) }}
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!-- KETERANGAN -->
      <el-alert
        title="Informasi perhitungan"
        type="info"
        :closable="false"
        show-icon
        class="info-laporan"
      >
        Pajak ditampilkan sebagai informasi pembayaran pelanggan dan tidak
        dihitung sebagai pendapatan usaha. Hasil usaha belum dapat disebut
        laba bersih akuntansi apabila masih ada beban operasional atau beban
        lainnya yang belum dicatat.
      </el-alert>

      <!-- ISI LAPORAN -->
      <div class="laporan-container">
        <!-- PENDAPATAN -->
        <div class="section">
          <div class="section-title">
            <i class="el-icon-s-data" />
            PENDAPATAN
          </div>

          <div class="row">
            <span>Penjualan Kotor</span>
            <span>Rp {{ formatRupiah(laporan.pendapatan.penjualan_kotor) }}</span>
          </div>

          <div class="row">
            <span>Diskon Penjualan</span>
            <span class="diskon">
              - Rp {{ formatRupiah(laporan.pendapatan.diskon) }}
            </span>
          </div>

          <div class="row subtotal-row">
            <span>Penjualan Bersih</span>
            <span>
              Rp {{ formatRupiah(laporan.pendapatan.penjualan) }}
            </span>
          </div>

          <div class="row">
            <span>Pendapatan Lain-lain</span>
            <span>
              Rp {{ formatRupiah(laporan.pendapatan.pendapatan_lain) }}
            </span>
          </div>

          <div class="row total">
            <span>TOTAL PENDAPATAN</span>
            <span>Rp {{ formatRupiah(totalPendapatan) }}</span>
          </div>
        </div>

        <!-- INFORMASI PEMBAYARAN -->
        <div class="section">
          <div class="section-title">
            <i class="el-icon-money" />
            INFORMASI PEMBAYARAN PELANGGAN
          </div>

          <div class="row">
            <span>Jumlah Transaksi</span>
            <span>
              {{ formatRupiah(laporan.pembayaran.jumlah_transaksi) }} nota
            </span>
          </div>

          <div class="row">
            <span>Pajak yang Dipungut</span>
            <span>
              Rp {{ formatRupiah(laporan.pendapatan.pajak) }}
            </span>
          </div>

          <div class="row">
            <span>Total Pembayaran Pelanggan</span>
            <span class="payment-total">
              Rp {{ formatRupiah(laporan.pembayaran.total_pembayaran) }}
            </span>
          </div>

          <div class="info-note">
            Total pembayaran pelanggan merupakan jumlah tagihan transaksi
            termasuk pajak. Nilai ini bukan total pendapatan usaha.
          </div>
        </div>

        <!-- HPP -->
        <div class="section">
          <div class="section-title">
            <i class="el-icon-shopping-cart-full" />
            HARGA POKOK PENJUALAN
          </div>

          <div class="row">
            <span>Harga Pokok Penjualan (HPP)</span>
            <span>
              Rp {{ formatRupiah(laporan.beban.hpp) }}
            </span>
          </div>

          <div class="row total">
            <span>TOTAL HPP</span>
            <span>
              Rp {{ formatRupiah(laporan.beban.hpp) }}
            </span>
          </div>
        </div>

        <!-- LABA KOTOR -->
        <!-- <div
          class="section highlight"
          :class="{
            'negative-section': laporan.laba_kotor < 0
          }"
        >
          <div class="row big">
            <span>
              {{ laporan.laba_kotor >= 0 ? 'LABA KOTOR' : 'RUGI KOTOR' }}
            </span>
            <span>
              Rp {{ formatRupiah(Math.abs(laporan.laba_kotor)) }}
            </span>
          </div>

          <div class="calculation-note">
            Penjualan bersih dikurangi HPP.
          </div>
        </div>

        <div class="section">
          <div class="section-title">
            <i class="el-icon-wallet" />
            BEBAN OPERASIONAL DAN BEBAN LAINNYA
          </div>

          <div class="row">
            <span>Beban Operasional</span>
            <span>
              Rp {{ formatRupiah(laporan.beban.operasional) }}
            </span>
          </div>

          <div class="row">
            <span>Beban Lain-lain</span>
            <span>
              Rp {{ formatRupiah(laporan.beban.beban_lain) }}
            </span>
          </div>

          <div class="row total">
            <span>TOTAL BEBAN SELAIN HPP</span>
            <span>
              Rp {{ formatRupiah(totalBebanLain) }}
            </span>
          </div>
        </div> -->

        <div
          class="section hasil-usaha"
          :class="{
            'rugi-section': labaSetelahBeban < 0
          }"
        >
          <div class="row big">
            <span>
              {{ labaSetelahBeban >= 0
                ? 'LABA USAHA'
                : 'RUGI USAHA'
              }}
            </span>

            <span>
              Rp {{ formatRupiah(Math.abs(labaSetelahBeban)) }}
            </span>
          </div>

          <div class="calculation-note">
            Total pendapatan dikurangi HPP dan beban lainnya yang tercatat.
            Belum termasuk beban yang belum dimasukkan ke sistem.
          </div>
        </div>
      </div>

      <!-- FOOTER -->
      <div class="footer-laporan">
        <span>
          Data laporan berdasarkan transaksi dengan status selesai.
        </span>

        <el-button
          type="text"
          icon="el-icon-refresh"
          :loading="loading"
          @click="muat"
        >
          Muat ulang
        </el-button>
      </div>
    </el-card>
  </div>
</template>

<script>

import {
  laporanLabaRugi,
} from '@/api/laporan';

function createDefaultLaporan() {
  return {
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

    pembayaran: {
      jumlah_transaksi: 0,
      total_pembayaran: 0,
    },

    beban: {
      hpp: 0,
      operasional: 0,
      beban_lain: 0,
    },

    laba_kotor: 0,
    hasil_usaha_sebelum_beban_operasional: 0,
  };
}

export default {
  name: 'LaporanLabaRugi',

  data() {
    return {
      tanggal: [],
      loading: false,
      laporan: createDefaultLaporan(),
    };
  },

  computed: {
    // Penjualan bersih + pendapatan lain.
    // Pajak tidak dimasukkan sebagai pendapatan.
    totalPendapatan() {
      return (
        this.toNumber(this.laporan.pendapatan.penjualan) +
        this.toNumber(this.laporan.pendapatan.pendapatan_lain)
      );
    },

    // Beban selain HPP.
    totalBebanLain() {
      return (
        this.toNumber(this.laporan.beban.operasional) +
        this.toNumber(this.laporan.beban.beban_lain)
      );
    },

    // Total seluruh beban.
    totalBeban() {
      return (
        this.toNumber(this.laporan.beban.hpp) +
        this.totalBebanLain
      );
    },

    // Hasil usaha setelah seluruh beban yang tercatat.
    labaSetelahBeban() {
      return this.totalPendapatan - this.totalBeban;
    },
  },

  created() {
    const today = this.getToday();

    this.tanggal = [today, today];

    this.muat();
  },

  methods: {
    async muat() {
      if (
        !this.tanggal ||
        this.tanggal.length !== 2 ||
        !this.tanggal[0] ||
        !this.tanggal[1]
      ) {
        return;
      }

      if (this.tanggal[0] > this.tanggal[1]) {
        this.$message.warning(
          'Tanggal awal tidak boleh melebihi tanggal akhir.'
        );
        return;
      }

      this.loading = true;

      try {
        const res = await laporanLabaRugi({
          dari: this.tanggal[0],
          sampai: this.tanggal[1],
        });

        // Mendukung response:
        // { data: { pendapatan: {}, beban: {} } }
        // maupun { pendapatan: {}, beban: {} }
        const response = res && res.data
          ? res.data
          : {};

        const data = response.data || response;

        const pendapatan = data.pendapatan || {};
        const pembayaran = data.pembayaran || {};
        const beban = data.beban || {};

        /*
         * Kompatibilitas API:
         *
         * API lama:
         * pendapatan.total_transaksi
         *
         * API baru:
         * pembayaran.total_pembayaran
         */
        const totalPembayaran = this.toNumber(
          pembayaran.total_pembayaran !== undefined
            ? pembayaran.total_pembayaran
            : pendapatan.total_transaksi
        );

        /*
         * Jumlah nota:
         * API baru: pembayaran.jumlah_transaksi
         * API lama: pendapatan.jumlah_transaksi
         */
        const jumlahTransaksi = this.toNumber(
          pembayaran.jumlah_transaksi !== undefined
            ? pembayaran.jumlah_transaksi
            : pendapatan.jumlah_transaksi
        );

        const penjualanKotor = this.toNumber(
          pendapatan.penjualan_kotor
        );

        const diskon = this.toNumber(
          pendapatan.diskon
        );

        const penjualanBersih = pendapatan.penjualan !== undefined
          ? this.toNumber(pendapatan.penjualan)
          : penjualanKotor - diskon;

        const hpp = this.toNumber(beban.hpp);
        const operasional = this.toNumber(beban.operasional);
        const bebanLain = this.toNumber(beban.beban_lain);

        const pendapatanLain = this.toNumber(
          pendapatan.pendapatan_lain
        );

        const labaKotor = data.laba_kotor !== undefined
          ? this.toNumber(data.laba_kotor)
          : penjualanBersih - hpp;

        this.laporan = {
          periode: {
            dari: data.periode && data.periode.dari
              ? data.periode.dari
              : this.tanggal[0],

            sampai: data.periode && data.periode.sampai
              ? data.periode.sampai
              : this.tanggal[1],
          },

          pendapatan: {
            penjualan_kotor: penjualanKotor,
            diskon: diskon,
            penjualan: penjualanBersih,
            pajak: this.toNumber(
              data.pajak && data.pajak.penjualan !== undefined
                ? data.pajak.penjualan
                : pendapatan.pajak
            ),
            total_transaksi: totalPembayaran,
            pendapatan_lain: pendapatanLain,
          },

          pembayaran: {
            jumlah_transaksi: jumlahTransaksi,
            total_pembayaran: totalPembayaran,
          },

          beban: {
            hpp: hpp,
            operasional: operasional,
            beban_lain: bebanLain,
          },

          laba_kotor: labaKotor,

          hasil_usaha_sebelum_beban_operasional:
            data.hasil_usaha_sebelum_beban_operasional !== undefined
              ? this.toNumber(
                data.hasil_usaha_sebelum_beban_operasional
              )
              : (
                data.laba_sebelum_beban_operasional !== undefined
                  ? this.toNumber(
                    data.laba_sebelum_beban_operasional
                  )
                  : labaKotor - operasional - bebanLain
              ),
        };
      } catch (error) {
        console.error(
          'Gagal memuat laporan laba rugi:',
          error
        );

        this.$message.error(
          error.response &&
          error.response.data &&
          error.response.data.message
            ? error.response.data.message
            : 'Gagal memuat laporan laba rugi.'
        );
      } finally {
        this.loading = false;
      }
    },

    toNumber(value) {
      const number = Number(value);

      return Number.isFinite(number)
        ? number
        : 0;
    },

    formatRupiah(value) {
      return this.toNumber(value).toLocaleString(
        'id-ID',
        {
          maximumFractionDigits: 0,
        }
      );
    },

    nilaiClass(value) {
      return this.toNumber(value) >= 0
        ? 'nilai-positif'
        : 'nilai-negatif';
    },

    getToday() {
      const date = new Date();

      const year = date.getFullYear();

      const month = String(
        date.getMonth() + 1
      ).padStart(2, '0');

      const day = String(
        date.getDate()
      ).padStart(2, '0');

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

        if (bulan < 1 || bulan > 12) {
          return value;
        }

        return `${hari} ${namaBulan[bulan - 1]} ${tahun}`;
      };

      return `${format(dari)} - ${format(sampai)}`;
    },
  },
};
</script>

<style scoped>
.laporan-laba-rugi {
  width: 100%;
}

/* HEADER */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.title {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.filter {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* PERIODE */
.periode-laporan {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 5px;
  margin-bottom: 16px;
  padding: 11px 12px;
  background: #f5f7fa;
  border-radius: 5px;
  font-size: 13px;
  color: #606266;
}

.periode-label {
  color: #909399;
}

.periode-laporan strong {
  color: #303133;
  font-weight: 600;
}

/* RINGKASAN */
.ringkasan {
  margin-bottom: 16px;
}

.stat {
  height: 100%;
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #909399;
  line-height: 1.5;
}

.stat-value {
  font-size: 18px;
  font-weight: 700;
  margin-top: 8px;
  color: #303133;
  overflow-wrap: anywhere;
}

.nilai-positif {
  color: #67c23a;
}

.nilai-negatif {
  color: #f56c6c;
}

/* INFO */
.info-laporan {
  margin-bottom: 18px;
}

/* CONTAINER */
.laporan-container {
  width: 100%;
  max-width: 850px;
  margin: 0 auto;
  border: 1px solid #ebeef5;
  border-radius: 5px;
  overflow: hidden;
  background: #fff;
}

.section {
  padding: 14px 22px;
  border-bottom: 1px solid #ebeef5;
}

.section:last-child {
  border-bottom: none;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 13px;
  font-weight: 700;
  color: #303133;
  margin-bottom: 9px;
  letter-spacing: 0.2px;
}

.section-title i {
  color: #409eff;
  font-size: 15px;
}

/* ROW */
.row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  padding: 7px 0;
  font-size: 13px;
  line-height: 1.5;
  color: #606266;
}

.row span:first-child {
  flex: 1;
}

.row span:last-child {
  min-width: 180px;
  text-align: right;
  font-weight: 500;
  font-variant-numeric: tabular-nums;
}

/* DISKON */
.diskon {
  color: #f56c6c !important;
}

/* SUBTOTAL */
.subtotal-row {
  margin-top: 4px;
  padding-top: 10px;
  border-top: 1px dashed #dcdfe6;
  font-weight: 600;
  color: #303133;
}

.subtotal-row span:last-child {
  font-weight: 700;
}

/* TOTAL */
.row.total {
  margin-top: 7px;
  padding-top: 11px;
  border-top: 1px dashed #dcdfe6;
  font-weight: 700;
  color: #303133;
}

.row.total span:last-child {
  font-weight: 700;
}

/* INFORMASI PEMBAYARAN */
.payment-total {
  color: #409eff !important;
  font-weight: 700 !important;
}

.info-note {
  margin-top: 8px;
  padding: 9px 11px;
  background: #f5f7fa;
  border-radius: 4px;
  color: #909399;
  font-size: 12px;
  line-height: 1.6;
}

/* LABA KOTOR */
.highlight {
  background: #f5faff;
}

.highlight .row {
  color: #409eff;
}

/* HASIL USAHA */
.hasil-usaha {
  background: #f0f9eb;
}

.hasil-usaha .row {
  color: #67c23a;
}

.negative-section,
.rugi-section {
  background: #fef0f0;
}

.negative-section .row,
.rugi-section .row {
  color: #f56c6c;
}

.big {
  font-size: 15px;
  font-weight: 700;
}

.big span:last-child {
  font-size: 16px;
  font-weight: 700;
}

.calculation-note {
  margin-top: 5px;
  font-size: 11px;
  color: #909399;
  line-height: 1.5;
}

/* FOOTER */
.footer-laporan {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
  color: #909399;
  font-size: 12px;
}

/* TABLET */
@media (max-width: 992px) {
  .stat-value {
    font-size: 16px;
  }

  .laporan-container {
    max-width: 100%;
  }
}

/* MOBILE */
@media (max-width: 768px) {
  .header {
    display: block;
  }

  .title {
    margin-bottom: 12px;
  }

  .filter {
    flex-direction: column;
    align-items: stretch;
    width: 100%;
  }

  .filter .el-date-editor {
    width: 100% !important;
  }

  .filter .el-button {
    width: 100%;
  }

  .ringkasan {
    margin-bottom: 10px;
  }

  .stat {
    margin-bottom: 10px;
  }

  .stat-value {
    font-size: 15px;
  }

  .section {
    padding: 12px 13px;
  }

  .row {
    font-size: 12px;
    gap: 8px;
  }

  .row span:last-child {
    min-width: 125px;
  }

  .section-title {
    font-size: 12px;
  }

  .big {
    font-size: 13px;
  }

  .big span:last-child {
    font-size: 14px;
  }

  .footer-laporan {
    align-items: flex-start;
  }
}

/* MOBILE KECIL */
@media (max-width: 380px) {
  .row span:last-child {
    min-width: 110px;
  }

  .stat-value {
    font-size: 13px;
  }

  .section {
    padding: 11px 10px;
  }
}
</style>
