<template>
  <div class="transaksi-page">
    <el-row :gutter="16">
      <!-- =====================================================
           KIRI: DAFTAR PRODUK
           ===================================================== -->
      <el-col :span="15">
        <el-card shadow="never">
          <div slot="header">
            <div class="filter-produk">
              <el-input
                ref="inputCari"
                v-model="keyword"
                placeholder="Cari produk / scan barcode..."
                prefix-icon="el-icon-search"
                clearable
                @input="cariProdukTransaksiDebounce"
                @clear="resetPencarian"
              />

              <el-select
                v-model="kategoriId"
                placeholder="Semua Kategori"
                clearable
                filterable
                @change="filterKategori"
              >
                <el-option
                  v-for="kategori in daftarKategori"
                  :key="kategori.id"
                  :label="kategori.nama"
                  :value="kategori.id"
                />
              </el-select>
            </div>
          </div>

          <!-- Loading -->
          <div v-loading="loadingProduk" class="produk-container">
            <!-- Daftar Produk -->
            <div
              v-if="daftarProduk.length > 0"
              class="produk-grid"
            >
              <div
                v-for="p in daftarProduk"
                :key="p.id"
                class="produk-item"
                :class="{ disabled: p.stok <= 0 }"
                @click="tambahKeKeranjang(p)"
              >
                <!-- FOTO PRODUK -->
                <div class="produk-gambar">
                  <img
                    v-if="p.gambar"
                    :src="'/storage/' + p.gambar"
                    :alt="p.nama"
                    @error="gambarError"
                  >
                  <div
                    v-else
                    class="produk-gambar-placeholder"
                  >
                    <i class="el-icon-picture-outline" />
                  </div>
                </div>

                <!-- INFORMASI PRODUK -->
                <div class="produk-info">
                  <div class="produk-nama">
                    {{ p.nama }}
                  </div>

                  <div class="produk-kode">
                    {{ p.kode }}
                  </div>

                  <div class="produk-harga">
                    Rp {{ formatRupiah(p.harga_jual) }}
                  </div>

                  <div
                    class="produk-stok"
                    :class="{
                      'stok-rendah':
                        p.stok <= p.stok_minimal
                    }"
                  >
                    Stok: {{ p.stok }} {{ p.satuan }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Tidak ada produk -->
            <el-empty
              v-else
              description="Produk tidak ditemukan"
            />
          </div>

          <!-- PAGINATION -->
          <div
            v-if="totalProduk > 0"
            class="produk-pagination"
          >
            <el-pagination
              background
              layout="prev, pager, next"
              :current-page="halamanProduk"
              :page-size="20"
              :total="totalProduk"
              :pager-count="5"
              @current-change="ubahHalaman"
            />
          </div>
        </el-card>
      </el-col>

      <!-- =====================================================
           KANAN: KERANJANG
           ===================================================== -->
      <el-col :span="9">
        <el-card
          shadow="never"
          class="keranjang-card"
        >
          <div
            slot="header"
            class="keranjang-header"
          >
            <span>Keranjang</span>

            <el-tag type="info">
              {{ keranjang.length }} item
            </el-tag>
          </div>

          <!-- Keranjang kosong -->
          <div
            v-if="keranjang.length === 0"
            class="empty-keranjang"
          >
            <i class="el-icon-shopping-cart-2" />
            <p>Belum ada item</p>
          </div>

          <!-- Keranjang -->
          <div
            v-else
            class="keranjang-list"
          >
            <div
              v-for="(item, idx) in keranjang"
              :key="item.produk_id"
              class="keranjang-item"
            >
              <!-- NOMOR URUT -->
              <div class="item-nomor">
                {{ idx + 1 }}
              </div>

              <!-- GAMBAR PRODUK -->
              <div class="item-gambar">
                <img
                  v-if="item.gambar"
                  :src="'/storage/' + item.gambar"
                  :alt="item.nama"
                  @error="gambarKeranjangError"
                >

                <div
                  v-else
                  class="item-gambar-placeholder"
                >
                  <i class="el-icon-picture-outline" />
                </div>
              </div>

              <!-- INFORMASI PRODUK -->
              <div class="item-content">
                <div class="item-nama">
                  {{ item.nama }}
                </div>

                <div class="item-harga">
                  Rp {{ formatRupiah(item.harga) }}
                </div>

                <div class="item-control">
                  <el-input-number
                    v-model="item.qty"
                    :min="1"
                    :max="item.stok"
                    size="mini"
                    @change="hitungTotal"
                  />

                  <el-button
                    type="text"
                    icon="el-icon-delete"
                    class="btn-hapus"
                    @click="hapusItem(idx)"
                  />
                </div>
              </div>

              <!-- SUBTOTAL -->
              <div class="item-subtotal">
                Rp {{ formatRupiah(item.harga * item.qty) }}
              </div>
            </div>
          </div>

          <el-divider />

          <!-- RINGKASAN -->
          <div class="ringkasan">
            <div class="baris">
              <span>Subtotal</span>
              <span>
                Rp {{ formatRupiah(subtotal) }}
              </span>
            </div>

            <div class="baris">
              <span>Diskon</span>

              <el-input-number
                v-model="diskon"
                :min="0"
                :max="subtotal"
                size="mini"
                style="width: 140px"
                @change="hitungTotal"
              />
            </div>

            <div class="baris">
              <span>Pajak</span>

              <el-input-number
                v-model="pajak"
                :min="0"
                size="mini"
                style="width: 140px"
                @change="hitungTotal"
              />
            </div>

            <el-divider />

            <div class="baris total">
              <span>TOTAL</span>

              <span>
                Rp {{ formatRupiah(total) }}
              </span>
            </div>
          </div>

          <!-- AKSI -->
          <div class="aksi-bawah">
            <el-button
              type="info"
              icon="el-icon-refresh"
              @click="resetKeranjang"
            >
              Reset
            </el-button>

            <el-button
              type="primary"
              icon="el-icon-check"
              :disabled="keranjang.length === 0"
              @click="showBayarDialog = true"
            >
              Bayar
            </el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- =====================================================
         DIALOG PEMBAYARAN
         ===================================================== -->
    <el-dialog
      title="Pembayaran"
      :visible.sync="showBayarDialog"
      width="480px"
    >
      <div class="total-bayar">
        Total:
        <b>
          Rp {{ formatRupiah(total) }}
        </b>
      </div>

      <el-form label-position="top">
        <el-form-item label="Metode Pembayaran">
          <el-radio-group v-model="metodePembayaran">
            <el-radio-button label="cash">
              Tunai
            </el-radio-button>

            <el-radio-button label="transfer">
              Transfer
            </el-radio-button>

            <el-radio-button label="qris">
              QRIS
            </el-radio-button>
          </el-radio-group>
        </el-form-item>

        <el-form-item
          v-if="metodePembayaran === 'cash'"
          label="Jumlah Bayar (Rp)"
        >
          <el-input
            v-model="bayarCurrency"
            placeholder="Contoh: 100.000"
            clearable
            style="width: 100%"
          >
            <template slot="prepend">
              Rp
            </template>
          </el-input>

          <div class="quick-cash">
            <el-button
              v-for="n in quickCash"
              :key="n"
              size="mini"
              @click="bayar = n"
            >
              {{ formatRupiah(n) }}
            </el-button>

            <el-button
              size="mini"
              type="primary"
              @click="bayar = total"
            >
              Uang Pas
            </el-button>
          </div>
        </el-form-item>

        <el-form-item label="Catatan">
          <el-input
            v-model="catatan"
            placeholder="Opsional"
          />
        </el-form-item>

        <el-alert
          v-if="metodePembayaran === 'cash'"
          :title="`Kembalian: Rp ${formatRupiah(kembalian)}`"
          :type="kembalian >= 0 ? 'success' : 'error'"
          :closable="false"
          show-icon
        />
      </el-form>

      <span slot="footer">
        <el-button
          @click="showBayarDialog = false"
        >
          Batal
        </el-button>

        <el-button
          type="primary"
          :loading="loadingBayar"
          :disabled="
            metodePembayaran === 'cash' &&
              kembalian < 0
          "
          @click="submitTransaksi"
        >
          Selesaikan
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { cariProdukTransaksi } from '@/api/produk';
import { simpanTransaksi } from '@/api/transaksi';
import { cekSesiAktif } from '@/api/sesiKasir';
import { getPengaturanStruk, getKategori } from '@/api/master';

// import JsPDF from 'jspdf';
// import html2canvas from 'html2canvas';

export default {
  name: 'TransaksiKasir',

  data() {
    return {
      keyword: '',
      daftarProduk: [],

      kategoriId: null,
      daftarKategori: [],

      // =====================================================
      // PAGINATION PRODUK
      // =====================================================
      halamanProduk: 1,
      totalProduk: 0,
      loadingProduk: false,

      // =====================================================
      // KERANJANG
      // =====================================================
      keranjang: [],
      diskon: 0,
      pajak: 0,

      // =====================================================
      // PEMBAYARAN
      // =====================================================
      bayar: null,
      catatan: '',
      metodePembayaran: 'cash',
      showBayarDialog: false,
      loadingBayar: false,

      timerCari: null,

      quickCash: [
        10000,
        20000,
        50000,
        100000,
      ],

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
    };
  },

  computed: {
    subtotal() {
      return this.keranjang.reduce(
        (sum, item) =>
          sum +
          Number(item.harga || 0) *
          Number(item.qty || 0),
        0
      );
    },

    total() {
      return Math.max(
        0,
        this.subtotal -
          Number(this.diskon || 0) +
          Number(this.pajak || 0)
      );
    },

    kembalian() {
      return (
        Number(this.bayar || 0) -
        Number(this.total || 0)
      );
    },

    // =====================================================
    // FORMAT INPUT PEMBAYARAN
    // =====================================================
    bayarCurrency: {
      get() {
        if (
          this.bayar === null ||
          this.bayar === undefined ||
          this.bayar === ''
        ) {
          return '';
        }

        return this.formatInputNumber(
          this.bayar
        );
      },

      set(val) {
        const digits = String(
          val || ''
        ).replace(/\D/g, '');

        this.bayar =
          digits === ''
            ? null
            : Number(digits);
      },
    },
  },

  // =======================================================
  // CREATED
  // =======================================================
  async created() {
    try {
      const res = await cekSesiAktif();

      if (!res.data) {
        this.$message.warning('Buka kas terlebih dahulu');

        this.$router.push({
          name: 'OpenKas',
        });

        return;
      }

      await this.muatPengaturan();
      await this.muatKategori();
      await this.muatProduk();
    } catch (e) {
      console.error(
        'Gagal inisialisasi halaman transaksi:',
        e
      );

      this.$message.error(
        'Gagal memuat halaman transaksi'
      );
    }
  },

  mounted() {
    if (
      this.$refs.inputCari &&
      this.$refs.inputCari.focus
    ) {
      this.$refs.inputCari.focus();
    }
  },

  beforeDestroy() {
    clearTimeout(this.timerCari);
  },

  methods: {
    // =====================================================
    // PRODUK
    // =====================================================

    async muatProduk() {
      this.loadingProduk = true;

      try {
        const res = await cariProdukTransaksi(
          this.keyword,
          this.halamanProduk,
          this.kategoriId
        );

        const pagination = res;

        this.daftarProduk =
          pagination.data || [];

        this.halamanProduk =
          pagination.current_page || 1;

        this.totalProduk =
          pagination.total || 0;
      } catch (e) {
        console.error(
          'Gagal memuat produk:',
          e
        );

        this.$message.error(
          'Gagal memuat produk'
        );

        this.daftarProduk = [];
        this.totalProduk = 0;
      } finally {
        this.loadingProduk = false;
      }
    },

    async muatKategori() {
      try {
        const res = await getKategori();

        this.daftarKategori =
          res.data && Array.isArray(res.data.data)
            ? res.data.data
            : Array.isArray(res.data)
              ? res.data
              : [];
      } catch (e) {
        console.error(
          'Gagal memuat kategori:',
          e
        );

        this.daftarKategori = [];

        this.$message.warning(
          'Gagal memuat kategori'
        );
      }
    },

    filterKategori() {
      this.halamanProduk = 1;
      this.muatProduk();
    },

    cariProdukTransaksiDebounce() {
      clearTimeout(this.timerCari);

      this.timerCari = setTimeout(() => {
        this.halamanProduk = 1;
        this.muatProduk();
      }, 300);
    },

    resetPencarian() {
      this.keyword = '';
      this.halamanProduk = 1;
      this.muatProduk();
    },

    ubahHalaman(page) {
      this.halamanProduk = page;
      this.muatProduk();
    },

    gambarError(event) {
      event.target.style.display = 'none';

      if (event.target.parentNode) {
        event.target.parentNode.classList.add(
          'gambar-error'
        );
      }
    },

    // =====================================================
    // KERANJANG
    // =====================================================

    tambahKeKeranjang(p) {
      if (Number(p.stok || 0) <= 0) {
        this.$message.warning(
          'Stok habis'
        );

        return;
      }

      const existing =
        this.keranjang.find(
          item =>
            item.produk_id === p.id
        );

      if (existing) {
        if (
          existing.qty >=
          Number(p.stok || 0)
        ) {
          this.$message.warning(
            'Stok tidak mencukupi'
          );

          return;
        }

        existing.qty += 1;
      } else {
        this.keranjang.push({
          produk_id: p.id,
          kode: p.kode || '',
          nama: p.nama,
          harga: Number(
            p.harga_jual || 0
          ),
          qty: 1,
          stok: Number(
            p.stok || 0
          ),
          gambar:
            p.gambar || null,
        });
      }

      this.hitungTotal();
    },

    hapusItem(idx) {
      this.keranjang.splice(idx, 1);
      this.hitungTotal();
    },

    resetKeranjang() {
      this.keranjang = [];
      this.diskon = 0;
      this.pajak = 0;
      this.bayar = null;
      this.catatan = '';
      this.metodePembayaran = 'cash';
    },

    hitungTotal() {
      // Total dihitung melalui computed property.
    },

    // =====================================================
    // PENGATURAN KEDAI
    // =====================================================

    async muatPengaturan() {
      try {
        const res = await getPengaturanStruk();

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
            pengaturan[item.key] =
              item.value;
          }
        });

        this.pengaturan = {
          ...this.pengaturan,
          ...pengaturan,
        };
      } catch (e) {
        console.error(
          'Gagal memuat pengaturan:',
          e
        );

        this.$message.warning(
          'Pengaturan kedai gagal dimuat. Struk menggunakan data default.'
        );
      }
    },

    // =====================================================
    // TRANSAKSI
    // =====================================================

    async submitTransaksi() {
      if (
        this.keranjang.length === 0
      ) {
        return;
      }

      // Validasi pembayaran tunai
      if (
        this.metodePembayaran === 'cash' &&
        Number(this.bayar || 0) <
          Number(this.total || 0)
      ) {
        this.$message.warning(
          'Jumlah pembayaran tidak mencukupi'
        );

        return;
      }

      this.loadingBayar = true;

      // ===================================================
      // SNAPSHOT SEBELUM KERANJANG RESET
      // ===================================================
      const snapshot = {
        kode: '',
        items: this.keranjang.map(
          item => ({
            produk_id:
              item.produk_id,
            kode:
              item.kode || '',
            nama:
              item.nama,
            harga:
              Number(
                item.harga || 0
              ),
            qty:
              Number(
                item.qty || 0
              ),
            gambar:
              item.gambar ||
              null,
          })
        ),

        subtotal:
          Number(
            this.subtotal || 0
          ),

        diskon:
          Number(
            this.diskon || 0
          ),

        pajak:
          Number(
            this.pajak || 0
          ),

        total:
          Number(
            this.total || 0
          ),

        bayar:
          this.metodePembayaran ===
          'cash' ? Number(this.bayar || 0) : Number(this.total || 0),

        kembalian:
          this.metodePembayaran ===
          'cash' ? Number(this.kembalian || 0) : 0,

        metode_pembayaran:
          this.metodePembayaran,

        catatan:
          this.catatan || '',

        tanggal:
          new Date(),
      };

      try {
        // =================================================
        // PAYLOAD API
        // =================================================
        const payload = {
          items:
            this.keranjang.map(
              item => ({
                produk_id:
                  item.produk_id,
                qty:
                  Number(
                    item.qty || 0
                  ),
              })
            ),

          diskon:
            Number(
              this.diskon || 0
            ),

          pajak:
            Number(
              this.pajak || 0
            ),

          bayar:
            this.metodePembayaran ===
            'cash' ? Number(this.bayar || 0) : Number(this.total || 0),

          metode_pembayaran:
            this.metodePembayaran,

          catatan:
            this.catatan || '',
        };

        // =================================================
        // SIMPAN TRANSAKSI
        // =================================================
        const res =
          await simpanTransaksi(
            payload
          );

        this.$message.success(
          res.message ||
          'Transaksi berhasil'
        );

        // =================================================
        // AMBIL DATA TRANSAKSI DARI RESPONSE
        // =================================================
        const dataCetak =
          this.normalisasiDataCetak(
            res,
            snapshot
          );

        // =================================================
        // CETAK STRUK (JsPDF + html2canvas → print)
        // =================================================
        await this.cetakStruk(dataCetak);

        // =================================================
        // RESET FORM
        // =================================================
        this.showBayarDialog = false;

        this.resetKeranjang();

        // =================================================
        // REFRESH PRODUK
        // =================================================
        await this.muatProduk();
      } catch (e) {
        console.error(
          'Transaksi gagal:',
          e
        );

        const message =
          e.response &&
          e.response.data &&
          e.response.data.message
            ? e.response.data.message
            : 'Transaksi gagal';

        this.$message.error(message);
      } finally {
        this.loadingBayar = false;
      }
    },

    // =====================================================
    // NORMALISASI DATA CETAK
    // =====================================================

    normalisasiDataCetak(
      response,
      snapshot
    ) {
      let data = null;

      if (
        response &&
        response.data &&
        response.data.data
      ) {
        data =
          response.data.data;
      } else if (
        response &&
        response.data
      ) {
        data =
          response.data;
      } else if (response) {
        data = response;
      }

      if (
        !data ||
        typeof data !== 'object'
      ) {
        return snapshot;
      }

      const hasil = {
        ...snapshot,
        ...data,
      };

      hasil.kode =
        data.kode ||
        data.nomor_struk ||
        data.no_struk ||
        data.kode_transaksi ||
        snapshot.kode ||
        '';

      if (Array.isArray(data.items)) {
        hasil.items = data.items;
      } else {
        hasil.items = snapshot.items;
      }

      hasil.metode_pembayaran =
        data.metode_pembayaran ||
        data.metodePembayaran ||
        snapshot.metode_pembayaran;

      hasil.tanggal =
        data.tanggal ||
        data.created_at ||
        snapshot.tanggal;

      hasil.subtotal = Number(
        data.subtotal !== undefined
          ? data.subtotal
          : snapshot.subtotal
      );

      hasil.diskon = Number(
        data.diskon !== undefined
          ? data.diskon
          : snapshot.diskon
      );

      hasil.pajak = Number(
        data.pajak !== undefined
          ? data.pajak
          : snapshot.pajak
      );

      hasil.total = Number(
        data.total !== undefined
          ? data.total
          : snapshot.total
      );

      hasil.bayar = Number(
        data.bayar !== undefined
          ? data.bayar
          : snapshot.bayar
      );

      hasil.kembalian = Number(
        data.kembalian !== undefined
          ? data.kembalian
          : snapshot.kembalian
      );

      hasil.catatan =
        data.catatan !== undefined
          ? data.catatan
          : snapshot.catatan;

      return hasil;
    },

    // =====================================================
    // BUILD HTML STRUK (untuk di-render ke canvas)
    // =====================================================

    // =====================================================
    // BUILD HTML STRUK
    // =====================================================
    buildStrukHtml(data) {
      const format = (value) =>
        Number(value || 0).toLocaleString('id-ID', {
          maximumFractionDigits: 0,
        });

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

      // 2 baris per item: nama produk, lalu "qty x harga ... subtotal"
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

      const catatanHtml = data.catatan
        ? `
          <div class="catatan">
            <strong>Catatan:</strong><br>
            ${this.escapeHtml(data.catatan)}
          </div>
        `
        : '';

      const metode = this.namaMetode(data.metode_pembayaran);

      return `
        <!DOCTYPE html>
        <html lang="id">
        <head>
          <meta charset="UTF-8">
          <title>Struk ${this.escapeHtml(data.kode || '')}</title>
          <style>
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
              width: 44mm;
              max-width: 44mm;
              margin: 0 auto;
              padding: 3mm 0 6mm 0;
              overflow: hidden;
            }

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

            .garis {
              border-top: 1px dashed #000;
              margin: 6px 0;
            }

            .info {
              font-size: 10.5px;
              font-weight: 600;
              line-height: 1.5;
            }

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

            .catatan {
              margin-top: 6px;
              padding-top: 5px;
              border-top: 1px dashed #000;
              font-size: 10.5px;
              font-weight: 600;
              line-height: 1.4;
            }

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

              .item, .baris, .catatan {
                page-break-inside: avoid;
              }
            }
          </style>
        </head>
        <body>
          <div class="struk">
            <div class="header">
              ${logoKedai ? `<img src="/storage/${this.escapeHtml(logoKedai)}" class="logo-kedai" alt="Logo Kedai">` : ''}
              <div class="nama-toko">
                ${this.escapeHtml(namaKedai)}
              </div>
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
    // FORMAT
    // =====================================================

    formatRupiah(value) {
      return Number(value || 0).toLocaleString(
        'id-ID',
        {
          maximumFractionDigits: 0,
        }
      );
    },

    // =====================================================
    // GAMBAR KERANJANG
    // =====================================================

    gambarKeranjangError(event) {
      event.target.style.display = 'none';

      if (event.target.parentNode) {
        event.target.parentNode.classList.add(
          'gambar-error'
        );
      }
    },

    // =====================================================
    // FORMAT INPUT NUMBER
    // =====================================================

    formatInputNumber(value) {
      if (
        value === null ||
        value === undefined ||
        value === ''
      ) {
        return '';
      }

      let str = String(value).trim();

      if (/^\d+\.\d+$/.test(str)) {
        str = str.split('.')[0];
      }

      const digits = str.replace(/\D/g, '');

      if (digits === '') {
        return '';
      }

      return digits.replace(
        /\B(?=(\d{3})+(?!\d))/g,
        '.'
      );
    },
  },
};
</script>

<style scoped>
.transaksi-page {
  padding: 16px;
}

/* =====================================================
   PRODUK
   ===================================================== */

.produk-container {
  min-height: 200px;
}

.produk-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 8px;
  max-height: 70vh;
  overflow-y: auto;
  padding: 2px;
}

.produk-item {
  border: 1px solid #ebeef5;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  /* background: #fff; */
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.produk-item:hover {
  border-color: #409eff;
  box-shadow: 0 2px 8px rgba(64, 158, 255, 0.2);
  transform: translateY(-1px);
}

.produk-item.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* =====================================================
   GAMBAR PRODUK
   ===================================================== */

.produk-gambar {
  width: 100%;
  height: 145px;
  background: #f5f7fa;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.produk-gambar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.produk-gambar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c0c4cc;
}

.produk-gambar-placeholder i {
  font-size: 48px;
}

.produk-gambar.gambar-error {
  color: #c0c4cc;
}

/* =====================================================
   INFORMASI PRODUK
   ===================================================== */

.produk-info {
  padding: 10px;
}

.produk-nama {
  font-weight: 600;
  font-size: 13px;
  line-height: 18px;
  margin-bottom: 4px;
  min-height: 36px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.produk-kode {
  font-size: 11px;
  color: #909399;
  margin-bottom: 4px;
}

.produk-harga {
  color: #f56c6c;
  font-weight: 600;
  font-size: 13px;
}

.produk-stok {
  font-size: 11px;
  color: #67c23a;
  margin-top: 4px;
}

.stok-rendah {
  color: #e6a23c;
}

/* =====================================================
   PAGINATION
   ===================================================== */

.produk-pagination {
  display: flex;
  justify-content: center;
  padding: 16px 0 4px;
  border-top: 1px solid #ebeef5;
  margin-top: 12px;
}

.produk-pagination .el-pagination {
  white-space: nowrap;
}

/* =====================================================
   KERANJANG
   ===================================================== */

.keranjang-card {
  height: calc(100vh);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.keranjang-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.empty-keranjang {
  text-align: center;
  color: #c0c4cc;
  padding: 40px 0;
}

.empty-keranjang i {
  font-size: 48px;
}

.keranjang-list {
  max-height: 30vh;
  overflow-y: auto;
  padding: 4px 8px;
}

/* ITEM KERANJANG */

.keranjang-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 0;
  border-bottom: 1px dashed #ebeef5;
}

.keranjang-item:last-child {
  border-bottom: none;
}

/* NOMOR */

.item-nomor {
  width: 22px;
  min-width: 22px;
  text-align: center;
  font-size: 11px;
  font-weight: 600;
  color: #909399;
}

/* GAMBAR */

.item-gambar {
  width: 48px;
  height: 48px;
  min-width: 48px;
  border-radius: 5px;
  overflow: hidden;
  background: #f5f7fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

.item-gambar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.item-gambar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c0c4cc;
}

.item-gambar-placeholder i {
  font-size: 22px;
}

.item-gambar.gambar-error {
  color: #c0c4cc;
}

/* INFORMASI */

.item-content {
  flex: 1;
  min-width: 0;
}

.item-nama {
  font-weight: 600;
  font-size: 12px;
  line-height: 16px;
  margin-bottom: 2px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-harga {
  font-size: 11px;
  color: #909399;
  margin-bottom: 4px;
}

/* QTY + HAPUS */

.item-control {
  display: flex;
  align-items: center;
  gap: 4px;
}

.item-control .el-input-number {
  width: 105px;
}

.btn-hapus {
  color: #f56c6c;
  padding: 2px 4px;
  margin-left: 0;
}

.btn-hapus:hover {
  color: #f78989;
}

/* SUBTOTAL */

.item-subtotal {
  min-width: 95px;
  text-align: right;
  font-weight: 600;
  color: #f56c6c;
  font-size: 12px;
  white-space: nowrap;
}

/* =====================================================
   RINGKASAN
   ===================================================== */

.ringkasan .baris {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
}

.ringkasan .total {
  font-size: 18px;
  font-weight: 700;
  color: #f56c6c;
}

/* =====================================================
   BUTTON
   ===================================================== */

.aksi-bawah {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-bottom: 16px;
}

.aksi-bawah .el-button {
  flex: 1;
}

/* =====================================================
   PEMBAYARAN
   ===================================================== */

.total-bayar {
  font-size: 20px;
  text-align: center;
  padding: 12px;
  background: #f5f7fa;
  border-radius: 6px;
  margin-bottom: 16px;
}

.quick-cash {
  margin-top: 8px;
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}

/* =====================================================
   RESPONSIVE
   ===================================================== */

@media (max-width: 992px) {
  .transaksi-page {
    padding: 10px;
  }

  .produk-grid {
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  }

  .produk-gambar {
    height: 125px;
  }
}

@media (max-width: 768px) {
  .produk-grid {
    max-height: none;
  }

  .keranjang-card {
    height: auto;
    margin-top: 16px;
  }

  .produk-pagination {
    overflow-x: auto;
    justify-content: center;
  }
}
.filter-produk {
  display: flex;
  gap: 8px;
  align-items: center;
}

.filter-produk .el-input {
  flex: 1;
}

.filter-produk .el-select {
  width: 200px;
}

@media (max-width: 768px) {
  .filter-produk {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-produk .el-select {
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
