<template>
  <div class="open-kas-page">
    <el-card class="box-card" shadow="always">
      <div slot="header" class="clearfix">
        <span class="title">Buka Kas</span>
      </div>

      <div v-if="sesiAktif" class="sesi-aktif">
        <el-alert title="Anda sudah membuka kas" type="success" :closable="false" show-icon />
        <el-descriptions :column="1" border class="mt-4">
          <el-descriptions-item label="Kode Sesi">
            {{ sesiAktif.kode }}
          </el-descriptions-item>
          <el-descriptions-item label="Waktu Buka">
            {{ formatDateTime(sesiAktif.waktu_buka) }}
          </el-descriptions-item>
          <el-descriptions-item label="Kas Awal">
            Rp {{ formatRupiah(sesiAktif.kas_awal) }}
          </el-descriptions-item>
          <!-- ✅ TAMBAHKAN INI -->
          <el-descriptions-item label="Catatan Buka">
            <span v-if="sesiAktif.catatan_buka">
              {{ sesiAktif.catatan_buka }}
            </span>
            <span v-else class="text-muted">-</span>
          </el-descriptions-item>
        </el-descriptions>

        <div class="actions mt-4">
          <el-button type="primary" icon="el-icon-shopping-cart-2" @click="goToTransaksi">
            Mulai Transaksi
          </el-button>
          <el-button type="danger" icon="el-icon-lock" @click="showTutupDialog = true">
            Tutup Kas
          </el-button>
        </div>
      </div>

      <div v-else>
        <el-form ref="formBuka" :model="formBuka" :rules="rulesBuka" label-position="top">
          <!-- <el-form-item label="Kas Awal (Rp)" prop="kas_awal">
            <el-input-number v-model="formBuka.kas_awal" :min="0" :step="10000" :precision="0" style="width: 100%" />
          </el-form-item> -->

          <el-form-item label="Kas Awal (Rp)" prop="kas_awal">
            <el-input
              v-model="kasAwalCurrency"
              placeholder="Contoh: 1.500.000"
              clearable
              style="width: 100%"
            >
              <template slot="prepend">Rp</template>
            </el-input>
          </el-form-item>

          <el-form-item label="Catatan (opsional)" prop="catatan">
            <el-input v-model="formBuka.catatan" type="textarea" :rows="3" placeholder="Contoh: Modal awal shift pagi" />
          </el-form-item>

          <el-button type="primary" :loading="loadingBuka" icon="el-icon-unlock" style="width: 100%" @click="submitBuka">
            Buka Kas
          </el-button>
        </el-form>
      </div>
    </el-card>

    <!-- Dialog Tutup Kas -->
    <el-dialog title="Tutup Kas" :visible.sync="showTutupDialog" width="480px">
      <el-form ref="formTutup" :model="formTutup" :rules="rulesTutup" label-position="top">
        <el-alert type="info" :closable="false" show-icon class="mb-3" title="Masukkan jumlah uang fisik di kasir. Sistem akan menghitung selisih." />

        <!-- <el-form-item label="Kas Akhir Fisik (Rp)" prop="kas_akhir_fisik">
          <el-input-number v-model="formTutup.kas_akhir_fisik" :min="0" :step="10000" :precision="0" style="width: 100%" />
        </el-form-item> -->

        <el-form-item label="Kas Akhir Fisik (Rp)" prop="kas_akhir_fisik">
          <el-input
            v-model="kasAkhirFisikCurrency"
            placeholder="Contoh: 1.500.000"
            clearable
            style="width: 100%"
          >
            <template slot="prepend">Rp</template>
          </el-input>
        </el-form-item>

        <el-form-item label="Catatan (opsional)" prop="catatan">
          <el-input v-model="formTutup.catatan" type="textarea" :rows="3" placeholder="Contoh: Uang kurang Rp 5.000" />
        </el-form-item>
      </el-form>

      <span slot="footer">
        <el-button @click="showTutupDialog = false">Batal</el-button>
        <el-button type="danger" :loading="loadingTutup" @click="submitTutup">
          Tutup Kas
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { cekSesiAktif, bukaKas, tutupKas } from '@/api/sesiKasir';

export default {
  name: 'OpenKas',
  data() {
    return {
      sesiAktif: null,
      loadingBuka: false,
      loadingTutup: false,
      showTutupDialog: false,
      formBuka: {
        kas_awal: null,
        catatan: '',
      },
      formTutup: {
        kas_akhir_fisik: null,
        catatan: '',
      },
      rulesBuka: {
        kas_awal: [
          { required: true, message: 'Kas awal wajib diisi', trigger: 'blur' },
        ],
      },
      rulesTutup: {
        kas_akhir_fisik: [
          { required: true, message: 'Kas akhir wajib diisi', trigger: 'blur' },
        ],
      },
    };
  },
  computed: {
    kasAwalCurrency: {
      get() {
        if (!this.formBuka) {
          return '';
        }
        return this.formatInputNumber(this.formBuka.kas_awal);
      },
      set(val) {
        if (!this.formBuka) {
          return;
        }
        const digits = String(val || '').replace(/\D/g, '');
        this.$set(this.formBuka, 'kas_awal', digits);
      },
    },
    kasAkhirFisikCurrency: {
      get() {
        if (!this.formTutup) {
          return '';
        }
        return this.formatInputNumber(this.formTutup.kas_akhir_fisik);
      },
      set(val) {
        if (!this.formTutup) {
          return;
        }
        const digits = String(val || '').replace(/\D/g, '');
        this.$set(this.formTutup, 'kas_akhir_fisik', digits);
      },
    },
  },
  created() {
    this.loadSesiAktif();
    // console.log('SESI AKTIF : ', this.sesiAktif);
  },
  methods: {
    async loadSesiAktif() {
      try {
        const res = await cekSesiAktif();
        this.sesiAktif = res.data;
      } catch (e) {
        if (this.sesiAktif === null) {
          return;
        }
        this.$message.error('Gagal memuat sesi kasir');
      }
    },

    submitBuka() {
      this.$refs.formBuka.validate(async(valid) => {
        if (!valid) {
          return;
        }
        this.loadingBuka = true;
        try {
          const res = await bukaKas(this.formBuka);
          this.$message.success(res.message);
          this.sesiAktif = res.data;
          this.formBuka = { kas_awal: 100000, catatan: '' };
        } catch (e) {
          this.$message.error(e.response.data.message || 'Gagal buka kas');
        } finally {
          this.loadingBuka = false;
        }
      });
    },

    submitTutup() {
      this.$refs.formTutup.validate(async(valid) => {
        if (!valid) {
          return;
        }
        this.loadingTutup = true;
        try {
          const res = await tutupKas(this.sesiAktif.id, this.formTutup);
          this.$message.success(res.message);
          this.showTutupDialog = false;
          this.sesiAktif = null;
          this.formTutup = { kas_akhir_fisik: 0, catatan: '' };

          // Tampilkan ringkasan
          this.tampilRingkasan(res.ringkasan);
        } catch (e) {
          this.$message.error(e.response.data.message || 'Gagal tutup kas');
        } finally {
          this.loadingTutup = false;
        }
      });
    },

    tampilRingkasan(r) {
      this.$alert(
        `<div style="line-height:2">
          <b>Kas Awal:</b> Rp ${this.formatRupiah(r.kas_awal)}<br/>
          <b>Total Tunai:</b> Rp ${this.formatRupiah(r.total_tunai)}<br/>
          <b>Kas Akhir (Sistem):</b> Rp ${this.formatRupiah(r.kas_akhir_sistem)}<br/>
          <b>Kas Akhir (Fisik):</b> Rp ${this.formatRupiah(r.kas_akhir_fisik)}<br/>
          <b>Selisih:</b>
          <span style="color:${r.selisih < 0 ? 'red' : 'green'}">
            Rp ${this.formatRupiah(r.selisih)}
          </span>
        </div>`,
        'Ringkasan Tutup Kas',
        { dangerouslyUseHTMLString: true }
      );
    },

    goToTransaksi() {
      this.$router.push({ name: 'TransaksiKasir' });
    },

    formatInputNumber(value) {
      if (
        value === null ||
        value === undefined ||
        value === ''
      ) {
        return '';
      }

      /*
       * Nilai limit diproses sebagai STRING.
       *
       * Jangan gunakan:
       *
       * Number()
       * Math.trunc()
       * toLocaleString()
       *
       * karena nilai dapat besar dan tidak perlu
       * mengalami konversi floating point JavaScript.
       */

      let str = String(value).trim();

      /*
       * Jika backend mengirim decimal:
       *
       * 222222222222.00
       *
       * ambil bagian sebelum titik decimal.
       *
       * Hanya dilakukan jika formatnya benar-benar:
       *
       * angka.angka
       */

      if (/^\d+\.\d+$/.test(str)) {
        str = str.split('.')[0];
      }

      /*
       * Hapus separator ribuan dan karakter lain.
       *
       * Contoh:
       *
       * 222.222.222.222
       *
       * menjadi:
       *
       * 222222222222
       */

      const digits =
        str.replace(/\D/g, '');

      if (digits === '') {
        return '';
      }

      /*
       * Format manual menjadi separator ribuan.
       *
       * 222222222222
       *
       * menjadi:
       *
       * 222.222.222.222
       */

      return digits.replace(
        /\B(?=(\d{3})+(?!\d))/g,
        '.'
      );
    },

    // =====================================================
    // PARSE INPUT NUMBER
    // =====================================================

    parseInputNumber(value) {
      if (
        value === null ||
        value === undefined ||
        value === ''
      ) {
        return '';
      }

      const str =
        String(value).trim();

      /*
       * PENTING
       *
       * Backend dapat mengirim:
       *
       * 222222222222.00
       *
       * Jangan menghapus semua karakter
       * non-digit terlebih dahulu karena:
       *
       * 222222222222.00
       *
       * akan menjadi:
       *
       * 22222222222200
       *
       * yang SALAH.
       */

      if (/^\d+\.\d+$/.test(str)) {
        return str.split('.')[0];
      }

      /*
       * Untuk input user:
       *
       * 222.222.222.222
       *
       * menjadi:
       *
       * 222222222222
       */

      const digits =
        str.replace(/\D/g, '');

      if (digits === '') {
        return '';
      }

      return digits;
    },

    formatRupiah(v) {
      const n = Number(v || 0);
      return n.toLocaleString('id-ID');
    },
    formatDateTime(v) {
      if (!v) {
        return '-';
      }
      return new Date(v).toLocaleString('id-ID');
    },
  },
};
</script>

<style scoped>
.open-kas-page {
  max-width: 560px;
  margin: 40px auto;
}

.title {
  font-size: 18px;
  font-weight: 600;
}

.mt-4 {
  margin-top: 16px;
}

.mb-3 {
  margin-bottom: 12px;
}

.actions {
  display: flex;
  gap: 8px;
}

.text-muted {
  color: #c0c4cc;
}
</style>
