<template>
  <div class="laporan-transaksi">

    <el-card shadow="never">

      <!-- =====================================================
           HEADER
           ===================================================== -->

      <div slot="header" class="header">

        <div class="title">
          Laporan Transaksi
        </div>

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

          <el-card shadow="hover" class="stat">

            <div class="stat-label">
              Total Transaksi
            </div>

            <div class="stat-value">
              {{ formatNumber(ringkasan.total_transaksi) }}
            </div>

          </el-card>

        </el-col>

        <el-col :span="6">

          <el-card shadow="hover" class="stat">

            <div class="stat-label">
              Total Pembayaran
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

            <div class="stat-value tunai">
              Rp {{ formatRupiah(ringkasan.total_tunai) }}
            </div>

          </el-card>

        </el-col>

        <el-col :span="6">

          <el-card shadow="hover" class="stat">

            <div class="stat-label">
              Total Non Tunai
            </div>

            <div class="stat-value non-tunai">
              Rp {{ formatRupiah(ringkasan.total_non_tunai) }}
            </div>

          </el-card>

        </el-col>

      </el-row>

      <!-- =====================================================
           TABEL TRANSAKSI
           ===================================================== -->

      <div
        v-loading="loading"
        class="table-container"
      >

        <el-table
          :data="transaksi"
          border
          stripe
          size="small"
          empty-text="Tidak ada transaksi"
        >

          <el-table-column
            type="index"
            label="No"
            width="55"
            align="center"
          />

          <el-table-column
            label="No. Struk"
            min-width="150"
          >

            <template slot-scope="scope">

              <span class="kode-transaksi">
                {{ scope.row.kode || '-' }}
              </span>

            </template>

          </el-table-column>

          <el-table-column
            label="Kasir"
            min-width="150"
          >
            <template slot-scope="scope">
              {{ scope.row.nama_kasir || '-' }}
            </template>
          </el-table-column>

          <el-table-column
            label="Tanggal"
            width="155"
          >

            <template slot-scope="scope">

              {{ formatTanggal(scope.row.tanggal) }}

            </template>

          </el-table-column>

          <el-table-column
            label="Pembayaran"
            width="110"
            align="center"
          >

            <template slot-scope="scope">

              <el-tag
                v-if="scope.row.metode_pembayaran === 'cash'"
                type="success"
                size="mini"
              >
                Tunai
              </el-tag>

              <el-tag
                v-else-if="scope.row.metode_pembayaran === 'qris'"
                type="warning"
                size="mini"
              >
                QRIS
              </el-tag>

              <el-tag
                v-else
                type="info"
                size="mini"
              >
                Transfer
              </el-tag>

            </template>

          </el-table-column>

          <el-table-column
            label="Status"
            width="110"
            align="center"
          >
            <template slot-scope="scope">
              <el-tag
                :type="scope.row.status === 'batal' ? 'danger' : 'success'"
                size="mini"
              >
                {{ formatStatus(scope.row.status) }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column
            label="Subtotal"
            min-width="130"
            align="right"
          >

            <template slot-scope="scope">

              Rp {{ formatRupiah(scope.row.subtotal) }}

            </template>

          </el-table-column>

          <el-table-column
            label="Diskon"
            min-width="110"
            align="right"
          >

            <template slot-scope="scope">

              Rp {{ formatRupiah(scope.row.diskon) }}

            </template>

          </el-table-column>

          <el-table-column
            label="Pajak"
            min-width="110"
            align="right"
          >

            <template slot-scope="scope">

              Rp {{ formatRupiah(scope.row.pajak) }}

            </template>

          </el-table-column>

          <el-table-column
            label="Total"
            min-width="140"
            align="right"
          >

            <template slot-scope="scope">

              <strong>
                Rp {{ formatRupiah(scope.row.total) }}
              </strong>

            </template>

          </el-table-column>

          <el-table-column
            label="Aksi"
            min-width="270"
            fixed="right"
            align="center"
          >
            <template slot-scope="scope">

              <el-button
                type="text"
                icon="el-icon-view"
                @click="lihatDetail(scope.row)"
              >
                Detail
              </el-button>

              <el-button
                type="text"
                icon="el-icon-printer"
                @click="cetakStruk(scope.row)"
              >
                Cetak
              </el-button>

              <el-button
                v-if="scope.row.status !== 'batal'"
                type="text"
                icon="el-icon-circle-close"
                style="color: #F56C6C"
                :loading="batalLoadingId === scope.row.id"
                @click="konfirmasiBatal(scope.row)"
              >
                Batalkan
              </el-button>

              <el-tag
                v-else
                type="danger"
                size="mini"
              >
                Dibatalkan
              </el-tag>

            </template>
          </el-table-column>

        </el-table>

        <!-- =====================================================
             PAGINATION
             ===================================================== -->

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

    <!-- =====================================================
         DIALOG DETAIL
         ===================================================== -->

    <el-dialog
      title="Detail Transaksi"
      :visible.sync="showDetail"
      width="700px"
    >

      <div
        v-if="detailTransaksi"
        class="detail-transaksi"
      >

        <div class="detail-header">

          <div>

            <div class="detail-label">
              No. Struk
            </div>

            <div class="detail-value kode-transaksi">
              {{ detailTransaksi.kode || '-' }}
            </div>

          </div>

          <div class="detail-right">

            <div class="detail-label">
              Tanggal
            </div>

            <div class="detail-value">
              {{ formatTanggal(detailTransaksi.tanggal) }}
            </div>

          </div>

          <div class="detail-right">
            <div class="detail-label">
              Status
            </div>

            <div class="detail-value">
              <el-tag
                :type="detailTransaksi.status === 'batal' ? 'danger' : 'success'"
                size="mini"
              >
                {{ formatStatus(detailTransaksi.status) }}
              </el-tag>
            </div>
          </div>

        </div>

        <el-divider />

        <el-table
          :data="detailTransaksi.items"
          border
          size="small"
        >

          <el-table-column
            type="index"
            label="No"
            width="50"
            align="center"
          />

          <el-table-column
            label="Produk"
            min-width="220"
          >

            <template slot-scope="scope">

              <div class="detail-produk">

                <strong>
                  {{ scope.row.nama }}
                </strong>

                <small>
                  {{ scope.row.kode || '-' }}
                </small>

              </div>

            </template>

          </el-table-column>

          <el-table-column
            label="Qty"
            width="70"
            align="center"
          >

            <template slot-scope="scope">
              {{ scope.row.qty }}
            </template>

          </el-table-column>

          <el-table-column
            label="Harga"
            width="120"
            align="right"
          >

            <template slot-scope="scope">
              Rp {{ formatRupiah(scope.row.harga) }}
            </template>

          </el-table-column>

          <el-table-column
            label="Subtotal"
            width="130"
            align="right"
          >

            <template slot-scope="scope">
              Rp {{ formatRupiah(scope.row.subtotal) }}
            </template>

          </el-table-column>

        </el-table>

        <div class="detail-ringkasan">

          <div class="detail-row">

            <span>
              Subtotal
            </span>

            <span>
              Rp {{ formatRupiah(detailTransaksi.subtotal) }}
            </span>

          </div>

          <div class="detail-row">

            <span>
              Diskon
            </span>

            <span>
              Rp {{ formatRupiah(detailTransaksi.diskon) }}
            </span>

          </div>

          <div class="detail-row">

            <span>
              Pajak
            </span>

            <span>
              Rp {{ formatRupiah(detailTransaksi.pajak) }}
            </span>

          </div>

          <div class="detail-row total">

            <span>
              TOTAL
            </span>

            <span>
              Rp {{ formatRupiah(detailTransaksi.total) }}
            </span>

          </div>

          <div
            v-if="detailTransaksi.metode_pembayaran === 'cash'"
            class="detail-row"
          >

            <span>
              Bayar
            </span>

            <span>
              Rp {{ formatRupiah(detailTransaksi.bayar) }}
            </span>

          </div>

          <div
            v-if="detailTransaksi.metode_pembayaran === 'cash'"
            class="detail-row kembalian"
          >

            <span>
              Kembalian
            </span>

            <span>
              Rp {{ formatRupiah(detailTransaksi.kembalian) }}
            </span>

          </div>

          <div class="detail-row">

            <span>
              Pembayaran
            </span>

            <span>
              {{ namaMetode(detailTransaksi.metode_pembayaran) }}
            </span>

          </div>

          <div class="detail-row">
            <span>Status</span>

            <span>
              <el-tag
                :type="detailTransaksi.status === 'batal' ? 'danger' : 'success'"
                size="mini"
              >
                {{ formatStatus(detailTransaksi.status) }}
              </el-tag>
            </span>
          </div>

        </div>

        <div
          v-if="detailTransaksi.catatan"
          class="detail-catatan"
        >

          <strong>
            Catatan:
          </strong>

          {{ detailTransaksi.catatan }}

        </div>

      </div>

      <span slot="footer">

        <el-button
          @click="showDetail = false"
        >
          Tutup
        </el-button>

        <el-button
          type="primary"
          icon="el-icon-printer"
          :loading="cetakLoading"
          @click="cetakStruk(detailTransaksi)"
        >
          Cetak Struk
        </el-button>

      </span>

    </el-dialog>

  </div>
</template>

<script>

import {
  laporanTransaksi,
  daftarKasir,
} from '@/api/laporan';

import {
  batalTransaksi as apiBatalTransaksi,
} from '@/api/transaksi';

import Resource from '@/api/resource';
const IdentityStore = new Resource('identity_store');

// import JsPDF from 'jspdf';

export default {

  name: 'LaporanTransaksi',

  data() {
    return {

      // =====================================================
      // LAPORAN
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

      loading: false,

      transaksi: [],

      halaman: 1,

      limit: 20,

      total: 0,

      // =====================================================
      // PENGATURAN KEDAI
      // =====================================================

      pengaturan: {

        nama_kedai: '',

        alamat_kedai: '',

        email_kedai: '',

        telepon_kedai: '',

        logo_kedai: '',

        mata_uang: 'IDR',

        pajak_persen: 0,

        struk_header: '',

        struk_footer: '',

      },

      // =====================================================
      // RINGKASAN
      // =====================================================

      ringkasan: {

        total_transaksi: 0,

        total_penjualan: 0,

        total_tunai: 0,

        total_non_tunai: 0,

        total_diskon: 0,

        total_pajak: 0,

      },

      // =====================================================
      // DETAIL
      // =====================================================

      showDetail: false,

      detailTransaksi: null,

      // =====================================================
      // CETAK PDF
      // =====================================================

      cetakLoading: false,

      batalLoadingId: null,

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

    await this.muatPengaturan();

    await this.muatDaftarKasir();

    this.muat();
  },

  methods: {

    // =====================================================
    // LOAD PENGATURAN
    // =====================================================

    async muatPengaturan() {
      try {
        const res = await IdentityStore.list();

        let data = [];

        if (Array.isArray(res.data)) {
          data = res.data;
        } else if (
          res.data &&
          Array.isArray(res.data.data)
        ) {
          data = res.data.data;
        }

        const pengaturan = {};

        data.forEach(item => {
          if (item.key) {
            pengaturan[item.key] = item.value;
          }
        });

        this.pengaturan = {
          ...this.pengaturan,
          ...pengaturan,
        };
      } catch (e) {
        console.error('Gagal memuat pengaturan:', e);

        this.$message.warning(
          'Pengaturan kedai gagal dimuat. Struk menggunakan data default.'
        );
      }
    },

    // =====================================================
    // LOAD DATA LAPORAN
    // =====================================================

    async muat() {
      if (!this.tanggal || this.tanggal.length < 2) {
        return;
      }
      this.halaman = 1;
      await this.ambilData();
    },

    async ambilData() {
      this.loading = true;
      try {
        const res = await laporanTransaksi({
          dari: this.tanggal[0],
          sampai: this.tanggal[1],
          user_id: this.userId || undefined,
          page: this.halaman,
          limit: this.limit,
        });

        const result = res.data || {};

        this.ringkasan = result.ringkasan || {
          total_transaksi: 0,
          total_penjualan: 0,
          total_tunai: 0,
          total_non_tunai: 0,
          total_diskon: 0,
          total_pajak: 0,
        };

        const pagination = result.data || {};

        this.transaksi = pagination.data || [];

        this.halaman = pagination.current_page || 1;

        this.total = pagination.total || 0;

        if (pagination.per_page) {
          this.limit = Number(pagination.per_page);
        }
      } catch (e) {
        console.error('Gagal memuat laporan transaksi:', e);

        this.$message.error('Gagal memuat laporan transaksi');

        this.transaksi = [];
        this.total = 0;
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
    // PAGINATION
    // =====================================================

    ubahHalaman(page) {
      this.halaman = page;
      this.ambilData();
    },

    // =====================================================
    // DETAIL
    // =====================================================

    lihatDetail(row) {
      this.detailTransaksi = row;
      this.showDetail = true;
    },

    // =====================================================
    // BUILD HTML STRUK
    // =====================================================
    buildStrukHtml(data) {
      // ===================================================
      // FORMAT ANGKA
      // ===================================================
      const format = (value) =>
        Number(value || 0).toLocaleString('id-ID', {
          maximumFractionDigits: 0,
        });

      // ===================================================
      // PENGATURAN KEDAI
      // ===================================================
      const namaKedai =
        this.pengaturan.struk_header ||
        this.pengaturan.nama_kedai ||
        'TOKO';

      const alamatKedai = this.pengaturan.alamat_kedai || '';
      const teleponKedai = this.pengaturan.telepon_kedai || '';
      const emailKedai = this.pengaturan.email_kedai || '';
      const logoKedai = this.pengaturan.logo_kedai || '';

      const footerStruk =
        this.pengaturan.struk_footer ||
        'Terima kasih telah berkunjung';

      // ===================================================
      // TANGGAL
      // ===================================================
      const tanggal = new Date(data.tanggal);

      const tanggalStr = tanggal.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
      });

      const waktuStr = tanggal.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
      });

      // ===================================================
      // ITEM
      // Ditulis 2 baris per item (nama produk di barisnya
      // sendiri, lalu "qty x harga ... subtotal" di baris
      // berikutnya). Format tabel 5 kolom yang lama gampang
      // "terpotong" di kertas 58mm karena kolom harga/qty
      // dipaksa nowrap padahal lebarnya sudah dikunci sempit
      // (table-layout: fixed) — begitu angka lebih lebar dari
      // kolomnya, sisanya meluber ke luar area cetak.
      // Format 2 baris ini tidak butuh kolom sempit sama
      // sekali sehingga jauh lebih aman untuk printer thermal.
      // ===================================================
      const items = data.items || [];

      const itemsHtml = items
        .map((item, index) => {
          const qty = Number(item.qty || 0);
          const harga = Number(item.harga || 0);
          const subtotal = harga * qty;

          return `
            <div class="item">
              <div class="item-nama">#${index + 1}. ${this.escapeHtml(item.nama)}</div>
              <div class="item-detail">
                <span>${qty} x ${format(harga)}</span>
                <span>${format(subtotal)}</span>
              </div>
            </div>
          `;
        })
        .join('');

      // ===================================================
      // CATATAN
      // ===================================================
      const catatanHtml = data.catatan
        ? `
          <div class="catatan">
            <strong>Catatan:</strong><br>
            ${this.escapeHtml(data.catatan)}
          </div>
        `
        : '';

      // ===================================================
      // METODE PEMBAYARAN
      // ===================================================
      const metode = this.namaMetode(data.metode_pembayaran);

      const statusValue = String(data.status || '').toLowerCase();
      const status = statusValue === 'batal' ? 'Batal' : 'Berhasil';

      // ===================================================
      // RETURN HTML
      // ===================================================
      return `
        <!DOCTYPE html>
        <html lang="id">
        <head>
          <meta charset="UTF-8">
          <title>Struk ${this.escapeHtml(data.kode || '')}</title>
          <style>
            /* ============================================
              SETUP KERTAS THERMAL 58MM (RPP02N)
              Kertas fisik 58mm, tapi area cetak yang benar-
              benar aman (tidak kena potong driver/printer)
              biasanya sekitar 48-50mm. Semua ukuran dikunci
              ke satuan mm tetap (BUKAN persen dari lebar
              window popup) supaya hasil cetak konsisten di
              semua printer/driver, walaupun window preview-
              nya lebar.
              ============================================ */
            @page {
              size: 58mm auto;
              margin: 0;
            }

            * {
              box-sizing: border-box;
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
              word-wrap: break-word;
              overflow-wrap: break-word;
            }

            html, body {
              margin: 0;
              padding: 0;
              width: 58mm;
              background: #fff;
            }

            body {
              font-family: Arial, Helvetica, sans-serif;
              font-size: 12px;
              font-weight: 600;
              color: #000;
              line-height: 1.45;
            }

            .struk {
              /* PENTING: kertas fisik 58mm, TAPI print-head
                 printer thermal 58mm (termasuk RPP02N) rata-rata
                 cuma selebar ~46-48mm. Sisa lebar kertas di kiri
                 kanan itu area yang TIDAK BISA dicetak sama
                 sekali oleh head-nya, jadi walaupun CSS-nya benar,
                 kontennya tetap akan kepotong kalau masih dibuat
                 mepet ke 54-58mm. Di sini kontennya sengaja dibuat
                 jauh lebih sempit (44mm) dan diberi margin auto
                 supaya ada ruang aman di kedua sisi. */
              width: 44mm;
              max-width: 44mm;
              margin: 0 auto;
              padding: 3mm 0 6mm 0;
              overflow: hidden;
            }

            /* ============================================
              HEADER TOKO
              ============================================ */
            .header {
              text-align: center;
              margin-bottom: 6px;
            }

            .nama-toko {
              font-size: 15px;
              font-weight: 700;
              margin-bottom: 4px;
              line-height: 1.3;
            }

            .alamat {
              font-size: 7px;
              font-weight: 600;
              line-height: 1.4;
            }

            /* ============================================
              GARIS PEMISAH
              ============================================ */
            .garis {
              border-top: 1px dashed #000;
              margin: 6px 0;
            }

            /* ============================================
              INFO TRANSAKSI
              ============================================ */
            .info {
              font-size: 10.5px;
              font-weight: 600;
              line-height: 1.5;
            }

            /* ============================================
              DAFTAR ITEM (2 baris per item, tanpa kolom
              sempit supaya tidak ada yang bisa "meluber"
              ke luar kertas)
              ============================================ */
            .item {
              margin: 4px 0;
            }

            .item-nama {
              font-size: 11px;
              font-weight: 700;
              margin-bottom: 1px;
            }

            .item-detail {
              display: flex;
              justify-content: space-between;
              gap: 6px;
              font-size: 10.5px;
              font-weight: 600;
            }

            .item-detail span:first-child {
              flex: 1;
              min-width: 0;
            }

            .item-detail span:last-child {
              text-align: right;
              white-space: nowrap;
              flex-shrink: 0;
            }

            /* ============================================
              RINGKASAN PEMBAYARAN
              ============================================ */
            .ringkasan {
              margin-top: 4px;
            }

            .baris {
              display: flex;
              justify-content: space-between;
              gap: 8px;
              padding: 2px 0;
              font-size: 10.5px;
              font-weight: 600;
            }

            .baris span:first-child {
              flex-shrink: 0;
            }

            .baris span:last-child {
              text-align: right;
              white-space: nowrap;
            }

            .total {
              font-size: 13px;
              font-weight: 700;
              padding: 5px 0;
            }

            .kembalian {
              font-weight: 700;
            }

            /* ============================================
              CATATAN
              ============================================ */
            .catatan {
              margin-top: 6px;
              padding-top: 5px;
              border-top: 1px dashed #000;
              font-size: 10.5px;
              font-weight: 600;
              line-height: 1.4;
            }

            /* ============================================
              FOOTER
              ============================================ */
            .footer {
              text-align: center;
              margin-top: 10px;
              font-size: 7px;
              font-weight: 600;
              line-height: 1.5;
            }

            .terima-kasih {
              font-weight: 700;
              margin-bottom: 3px;
              font-size: 7px;
            }

            /* ============================================
              PRINT
              ============================================ */
            @media print {
              html, body {
                width: 58mm;
                margin: 0;
                padding: 0;
              }

              .struk {
                width: 44mm;
                max-width: 44mm;
                margin: 0 auto;
                padding: 2mm 0 5mm 0;
              }

              /* Pastikan tidak ada page break di tengah item */
              .item, .baris, .catatan {
                page-break-inside: avoid;
              }
            }
          </style>
        </head>
        <body>
          <div class="struk">
            <div class="header">
              ${logoKedai ? `<img src="/storage/${this.escapeHtml(logoKedai)}" class="logo-kedai" width="48" alt="Logo Kedai">` : ''}

              <div class="nama-toko">${this.escapeHtml(namaKedai)}</div>
              ${alamatKedai ? `<div class="alamat">${this.escapeHtml(alamatKedai)}</div>` : ''}
              ${teleponKedai ? `<div class="alamat">Telp. ${this.escapeHtml(teleponKedai)}</div>` : ''}
              ${emailKedai ? `<div class="alamat">${this.escapeHtml(emailKedai)}</div>` : ''}
            </div>

            <div class="garis"></div>

            <div class="info">
              <div>No. Struk: ${this.escapeHtml(data.kode || '-')}</div>
              <div>Tanggal: ${tanggalStr}</div>
              <div>Waktu: ${waktuStr}</div>
              <div>Pembayaran: ${this.escapeHtml(metode)}</div>
              <div>Status: ${status}</div>
            </div>

            <div class="garis"></div>

            <div class="items">${itemsHtml}</div>

            <div class="garis"></div>

            <div class="ringkasan">
              <div class="baris">
                <span>Subtotal</span>
                <span>Rp ${format(data.subtotal)}</span>
              </div>
              <div class="baris">
                <span>Diskon</span>
                <span>Rp ${format(data.diskon)}</span>
              </div>
              <div class="baris">
                <span>Pajak</span>
                <span>Rp ${format(data.pajak)}</span>
              </div>
              <div class="garis"></div>
              <div class="baris total">
                <span>TOTAL</span>
                <span>Rp ${format(data.total)}</span>
              </div>
              ${data.metode_pembayaran === 'cash' ? `
                    <div class="baris">
                      <span>Bayar</span>
                      <span>Rp ${format(data.bayar)}</span>
                    </div>
                    <div class="baris kembalian">
                      <span>Kembalian</span>
                      <span>Rp ${format(data.kembalian)}</span>
                    </div>
                  ` : ''}
            </div>

            ${catatanHtml}

            <div class="footer">
              <div class="terima-kasih">${this.escapeHtml(footerStruk)}</div>
              <div>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
            </div>
          </div>

          <script>
            window.onload = function () {
              setTimeout(function () {
                window.focus();
                window.print();
              }, 300);
            };
            window.onafterprint = function () {
              setTimeout(function () {
                window.close();
              }, 300);
            };
          <\/script>
        </body>
        </html>
      `;
    },

    // =====================================================
    // CETAK STRUK (POPUP PRINT)
    // =====================================================
    cetakStruk(data) {
      if (!data) {
        return;
      }

      // Window preview dibuat sempit (mendekati lebar kertas)
      // supaya tampilan sebelum print sudah mencerminkan hasil
      // cetak yang sebenarnya. Lebar window ini TIDAK memengaruhi
      // ukuran kertas cetak — itu sudah dikunci lewat @page &
      // lebar mm di dalam HTML struk sendiri.
      const printWindow = window.open(
        '',
        '_blank',
        'width=1200,height=600'
      );

      if (!printWindow) {
        this.$message.warning(
          'Popup diblokir browser. Izinkan popup untuk mencetak struk.'
        );
        return;
      }

      const html = this.buildStrukHtml(data);

      printWindow.document.open();
      printWindow.document.write(html);
      printWindow.document.close();
    },

    // =====================================================
    // FORMAT RUPIAH
    // =====================================================

    formatRupiah(value) {
      return Number(value || 0).toLocaleString('id-ID', {
        maximumFractionDigits: 0,
      });
    },

    // =====================================================
    // FORMAT NUMBER
    // =====================================================

    formatNumber(value) {
      return Number(value || 0).toLocaleString('id-ID');
    },

    // =====================================================
    // FORMAT TANGGAL
    // =====================================================

    formatTanggal(value) {
      if (!value) {
        return '-';
      }

      const date = new Date(value);

      if (Number.isNaN(date.getTime())) {
        return value;
      }

      return (
        date.toLocaleDateString('id-ID', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
        }) +
        ' ' +
        date.toLocaleTimeString('id-ID', {
          hour: '2-digit',
          minute: '2-digit',
        })
      );
    },

    // =====================================================
    // NAMA METODE PEMBAYARAN
    // =====================================================

    namaMetode(value) {
      if (value === 'cash') {
        return 'Tunai';
      }

      if (value === 'qris') {
        return 'QRIS';
      }

      if (value === 'transfer') {
        return 'Transfer';
      }

      return value || '-';
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
    // ESCAPE HTML
    // =====================================================

    escapeHtml(value) {
      return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    },

    // =====================================================
    // FORMAT PERIODE
    // =====================================================

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

    async muatDaftarKasir() {
      try {
        const res = await daftarKasir();

        console.log('Response daftar kasir:', res);

        // Kemungkinan 1:
        // axios response langsung berisi array
        if (Array.isArray(res)) {
          this.daftarKasir = res;
          return;
        }

        // Kemungkinan 2:
        // axios response -> data berisi array
        if (res && Array.isArray(res.data)) {
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
          this.daftarKasir = res.data.data;
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

    async ubahKasir() {
      this.halaman = 1;
      await this.muat();
    },

    formatStatus(status) {
      if (!status) {
        return '-';
      }

      const value = String(status).toLowerCase();

      if (value === 'batal') {
        return 'Batal';
      }

      return value.charAt(0).toUpperCase() + value.slice(1);
    },

    // =====================================================
    // KONFIRMASI BATAL TRANSAKSI
    // =====================================================

    konfirmasiBatal(row) {
      if (!row || !row.id) {
        this.$message.error('ID transaksi tidak ditemukan.');
        return;
      }

      if (row.status === 'batal') {
        this.$message.warning('Transaksi sudah dibatalkan.');
        return;
      }

      this.$confirm(
        `Apakah Anda yakin ingin membatalkan transaksi ${row.kode || ''}?`,
        'Konfirmasi Pembatalan',
        {
          confirmButtonText: 'Ya, Batalkan',
          cancelButtonText: 'Tidak',
          type: 'warning',
          confirmButtonClass: 'el-button--danger',
        }
      ).then(() => {
        this.prosesBatalTransaksi(row);
      }).catch(() => {
        // Pembatalan konfirmasi oleh pengguna
      });
    },

    // =====================================================
    // PROSES BATAL TRANSAKSI
    // =====================================================

    async prosesBatalTransaksi(row) {
      if (this.batalLoadingId) {
        return;
      }

      this.batalLoadingId = row.id;

      try {
        await apiBatalTransaksi(row.id);

        // Update status pada baris tabel
        this.$set(row, 'status', 'batal');

        // Update status pada dialog detail jika sedang terbuka
        if (
          this.detailTransaksi &&
          this.detailTransaksi.id === row.id
        ) {
          this.$set(this.detailTransaksi, 'status', 'batal');
        }

        this.$message.success(
          'Transaksi berhasil dibatalkan.'
        );

        // Muat ulang laporan dan ringkasan dari server
        await this.ambilData();
      } catch (error) {
        console.error(
          'Gagal membatalkan transaksi:',
          error
        );

        this.$message.error(
          error.response &&
          error.response.data &&
          error.response.data.message
            ? error.response.data.message
            : 'Gagal membatalkan transaksi.'
        );
      } finally {
        this.batalLoadingId = null;
      }
    },

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

.laporan-transaksi {
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

.tunai {
  color: #67c23a;
}

.non-tunai {
  color: #409eff;
}

/* =====================================================
   TABLE
   ===================================================== */

.table-container {
  width: 100%;
}

.kode-transaksi {
  font-weight: 600;
  color: #409eff;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  padding: 16px 0 4px;
}

/* =====================================================
   DETAIL
   ===================================================== */

.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.detail-right {
  text-align: right;
}

.detail-label {
  font-size: 11px;
  color: #909399;
  margin-bottom: 3px;
}

.detail-value {
  font-size: 13px;
  color: #303133;
  font-weight: 600;
}

.detail-produk {
  display: flex;
  flex-direction: column;
}

.detail-produk strong {
  font-size: 12px;
}

.detail-produk small {
  color: #909399;
  font-size: 10px;
  margin-top: 2px;
}

.detail-ringkasan {
  width: 360px;
  margin-left: auto;
  margin-top: 16px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 5px 0;
  font-size: 13px;
}

.detail-row.total {
  margin-top: 5px;
  padding-top: 8px;
  border-top: 1px dashed #dcdfe6;
  font-size: 16px;
  font-weight: 700;
}

.detail-row.kembalian {
  color: #67c23a;
  font-weight: 600;
}

.detail-catatan {
  margin-top: 16px;
  padding: 10px;
  background: #f5f7fa;
  border-radius: 4px;
  font-size: 12px;
  color: #606266;
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

.filter {
  display: flex;
  gap: 8px;
  align-items: center;
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

  .detail-ringkasan {
    width: 100%;
  }
}
.logo-kedai {
  display: block;
  width: 18mm;
  max-width: 18mm;
  height: auto;
  max-height: 18mm;
  object-fit: contain;
  margin: 0 auto 3mm;
}
</style>
