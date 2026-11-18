<template>
  <div class="laporan-produk">
    <el-card shadow="never">
      <div slot="header" class="header">
        <span>Laporan Penjualan Per Produk</span>
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

          <el-date-picker
            v-model="tanggal"
            type="daterange"
            range-separator="-"
            start-placeholder="Dari"
            end-placeholder="Sampai"
            value-format="yyyy-MM-dd"
            @change="pilihTanggalManual"
          />
          <el-button type="primary" icon="el-icon-search" @click="muat">
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
        <el-col :span="8">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">Produk Terjual</div>
            <div class="stat-value">{{ ringkasan.total_produk_terjual || 0 }}</div>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">Total Qty</div>
            <div class="stat-value">{{ ringkasan.total_qty || 0 }}</div>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card shadow="hover" class="stat">
            <div class="stat-label">Total Penjualan</div>
            <div class="stat-value">
              Rp {{ formatRupiah(ringkasan.total_penjualan) }}
            </div>
          </el-card>
        </el-col>
      </el-row>

      <el-table :data="detail" border stripe style="width: 100%">
        <el-table-column type="index" label="#" width="60" />
        <el-table-column prop="produk_kode" label="Kode" width="140" />
        <el-table-column prop="produk_nama" label="Nama Produk" />
        <el-table-column label="Terjual" align="center" width="120">
          <template slot-scope="s">
            {{ s.row.total_qty }} {{ s.row.satuan }}
          </template>
        </el-table-column>
        <el-table-column label="Transaksi" align="center" width="100">
          <template slot-scope="s">
            {{ s.row.total_transaksi }}
          </template>
        </el-table-column>
        <el-table-column label="Total Penjualan" align="right">
          <template slot-scope="s">
            <b>Rp {{ formatRupiah(s.row.total_penjualan) }}</b>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { laporanPerProduk } from '@/api/laporan';

export default {
  name: 'LaporanProduk',
  data() {
    return {
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
      ringkasan: {},
      detail: [],
    };
  },
  created() {
    const today = new Date().toISOString().slice(0, 10);
    this.tanggal = [today, today];

    // this.filterBulan = Number(today.split('-')[1]);

    this.muat();
  },
  methods: {
    async muat() {
      if (!this.tanggal || this.tanggal.length < 2) {
        return;
      }
      try {
        const res = await laporanPerProduk({
          dari: this.tanggal[0],
          sampai: this.tanggal[1],
        });
        this.ringkasan = res.data.ringkasan;
        this.detail = res.data.detail;
      } catch (e) {
        this.$message.error('Gagal memuat laporan');
      }
    },
    formatRupiah(v) {
      return Number(v || 0).toLocaleString('id-ID');
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
    // TODAY
    // =====================================================

    getToday() {
      const date = new Date();
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
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
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.filter {
  display: flex;
  gap: 8px;
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
</style>
