<template>
  <div class="produk-page">
    <el-card shadow="never">
      <div slot="header" class="header">
        <div>
          <span class="title">Produk</span>
          <div class="subtitle">
            Kelola produk, harga, stok dan status produk
          </div>
        </div>

        <el-button
          type="primary"
          icon="el-icon-plus"
          @click="bukaTambah"
        >
          Tambah Produk
        </el-button>
      </div>

      <!-- FILTER -->
      <div class="filter">
        <el-input
          v-model="query.q"
          clearable
          placeholder="Cari kode / nama produk..."
          prefix-icon="el-icon-search"
          class="search-input"
          @keyup.enter.native="cari"
          @clear="cari"
        />

        <el-select
          v-model="query.kategori_id"
          clearable
          filterable
          placeholder="Semua kategori"
          class="kategori-select"
          @change="cari"
        >
          <el-option
            v-for="item in kategori"
            :key="item.id"
            :label="item.nama"
            :value="item.id"
          />
        </el-select>

        <el-select
          v-model="filterStok"
          placeholder="Filter stok"
          class="stok-select"
          @change="ubahFilterStok"
        >
          <el-option
            label="Semua stok"
            value="semua"
          />

          <el-option
            label="Stok rendah"
            value="rendah"
          />
        </el-select>

        <el-select
          v-model="filterStatus"
          placeholder="Status"
          class="status-select"
          @change="ubahFilterStatus"
        >
          <el-option
            label="Semua status"
            value="semua"
          />

          <el-option
            label="Aktif"
            :value="true"
          />

          <el-option
            label="Tidak aktif"
            :value="false"
          />
        </el-select>

        <el-button
          type="primary"
          icon="el-icon-search"
          @click="cari"
        >
          Cari
        </el-button>
      </div>

      <el-button-group class="view-switch">
        <el-button
          :type="viewMode === 'grid' ? 'primary' : 'default'"
          icon="el-icon-menu"
          @click="viewMode = 'grid'"
        >
          Grid
        </el-button>

        <el-button
          :type="viewMode === 'table' ? 'primary' : 'default'"
          icon="el-icon-tickets"
          @click="viewMode = 'table'"
        >
          Table
        </el-button>
      </el-button-group>

      <el-divider />

      <!-- GRID VIEW -->
      <div
        v-if="viewMode === 'grid'"
        v-loading="loading"
        class="produk-grid"
      >
        <el-row :gutter="16">
          <el-col
            v-for="item in data"
            :key="item.id"
            :xs="24"
            :sm="12"
            :md="8"
            :lg="6"
            :xl="4"
            class="produk-grid-col"
          >
            <el-card
              shadow="hover"
              class="produk-card"
              :body-style="{ padding: '0px' }"
            >
              <!-- GAMBAR -->
              <div class="produk-card-image">
                <el-image
                  v-if="item.gambar"
                  :src="'/storage/' + item.gambar"
                  fit="cover"
                  :preview-src-list="['/storage/' + item.gambar]"
                  class="produk-grid-image"
                >
                  <div slot="error" class="produk-grid-no-image">
                    <i class="el-icon-picture-outline" />
                  </div>
                </el-image>

                <div v-else class="produk-grid-no-image">
                  <i class="el-icon-picture-outline" />
                </div>

                <el-tag
                  size="mini"
                  :type="item.is_active ? 'success' : 'info'"
                  class="produk-status"
                >
                  {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                </el-tag>
              </div>

              <!-- INFORMASI PRODUK -->
              <div class="produk-card-content">
                <div class="produk-kode">
                  {{ item.kode }}
                </div>

                <div class="produk-card-name" :title="item.nama">
                  {{ item.nama }}
                </div>

                <div class="produk-card-category">
                  <i class="el-icon-collection-tag" />
                  {{ item.kategori ? item.kategori.nama : '-' }}
                </div>

                <div class="produk-card-price">
                  Rp {{ formatRupiah(item.harga_jual) }}
                </div>

                <div class="produk-card-detail">
                  <span>Harga Beli</span>
                  <span>Rp {{ formatRupiah(item.harga_beli) }}</span>
                </div>

                <div class="produk-card-detail">
                  <span>Stok</span>
                  <el-tag
                    size="mini"
                    :type="isStokRendah(item) ? 'danger' : 'success'"
                  >
                    {{ item.stok }} {{ item.satuan || 'pcs' }}
                  </el-tag>
                </div>

                <br>

                <!-- AKSI -->
                <div class="produk-card-actions">
                  <el-button
                    type="primary"
                    size="mini"
                    icon="el-icon-edit"
                    @click="bukaEdit(item)"
                  >
                    Edit
                  </el-button>

                  <el-button
                    type="danger"
                    size="mini"
                    icon="el-icon-delete"
                    @click="hapus(item)"
                  >
                    Hapus
                  </el-button>
                </div>
              </div>
            </el-card>
          </el-col>
        </el-row>

        <div v-if="!loading && data.length === 0" class="produk-empty">
          <i class="el-icon-box" />
          <p>Produk tidak ditemukan</p>
        </div>
      </div>

      <!-- TABLE VIEW -->
      <el-table
        v-if="viewMode === 'table'"
        v-loading="loading"
        :data="data"
        border
        stripe
        style="width: 100%"
      >
        <el-table-column
          type="index"
          label="No"
          width="55"
          align="center"
        />

        <!-- GAMBAR -->
        <el-table-column
          label="Gambar"
          width="90"
          align="center"
        >
          <template slot-scope="scope">
            <div
              v-if="scope.row.gambar"
              class="table-image-wrapper"
            >
              <el-image
                :src="'/storage/' + scope.row.gambar"
                fit="cover"
                class="table-image"
                :preview-src-list="['/storage/' + scope.row.gambar]"
              >
                <div
                  slot="error"
                  class="image-error"
                >
                  <i class="el-icon-picture-outline" />
                </div>
              </el-image>
            </div>

            <div
              v-else
              class="no-image"
            >
              <i class="el-icon-picture-outline" />
            </div>
          </template>
        </el-table-column>

        <el-table-column
          prop="kode"
          label="Kode"
          width="150"
        />

        <el-table-column
          label="Produk"
          min-width="230"
        >
          <template slot-scope="scope">
            <div class="produk-name">
              {{ scope.row.nama }}
            </div>

            <div class="produk-desc">
              {{ scope.row.deskripsi || '-' }}
            </div>
          </template>
        </el-table-column>

        <el-table-column
          label="Kategori"
          width="160"
        >
          <template slot-scope="scope">
            {{
              scope.row.kategori
                ? scope.row.kategori.nama
                : '-'
            }}
          </template>
        </el-table-column>

        <el-table-column
          label="Harga Beli"
          width="140"
          align="right"
        >
          <template slot-scope="scope">
            Rp {{ formatRupiah(scope.row.harga_beli) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Harga Jual"
          width="140"
          align="right"
        >
          <template slot-scope="scope">
            Rp {{ formatRupiah(scope.row.harga_jual) }}
          </template>
        </el-table-column>

        <el-table-column
          label="Stok"
          width="120"
          align="center"
        >
          <template slot-scope="scope">
            <el-tag
              :type="
                isStokRendah(scope.row)
                  ? 'danger'
                  : 'success'
              "
            >
              {{ scope.row.stok }}
              {{ scope.row.satuan || 'pcs' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column
          label="Status"
          width="100"
          align="center"
        >
          <template slot-scope="scope">
            <el-tag
              :type="
                scope.row.is_active
                  ? 'success'
                  : 'info'
              "
            >
              {{
                scope.row.is_active
                  ? 'Aktif'
                  : 'Nonaktif'
              }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column
          label="Aksi"
          width="150"
          fixed="right"
          align="center"
        >
          <template slot-scope="scope">
            <el-button
              type="primary"
              icon="el-icon-edit"
              circle
              size="mini"
              title="Edit"
              @click="bukaEdit(scope.row)"
            />

            <el-button
              type="danger"
              icon="el-icon-delete"
              circle
              size="mini"
              title="Hapus"
              @click="hapus(scope.row)"
            />
          </template>
        </el-table-column>
      </el-table>

      <!-- PAGINATION -->
      <div class="pagination">
        <el-pagination
          background
          layout="total, sizes, prev, pager, next, jumper"
          :current-page="query.page"
          :page-size="query.per_page"
          :page-sizes="[20, 40, 60, 100]"
          :total="total"
          @current-change="ubahHalaman"
          @size-change="ubahUkuran"
        />
      </div>
    </el-card>

    <!-- FORM -->
    <el-dialog
      :title="
        form.id
          ? 'Edit Produk'
          : 'Tambah Produk'
      "
      :visible.sync="dialogVisible"
      width="700px"
      :close-on-click-modal="false"
      @closed="resetFile"
    >
      <el-form
        ref="form"
        :model="form"
        :rules="rules"
        label-width="125px"
      >
        <!-- GAMBAR -->
        <el-form-item label="Gambar">
          <div class="upload-container">

            <!-- PREVIEW GAMBAR -->
            <div
              v-if="form.gambar"
              class="image-preview"
            >
              <el-image
                :src="getGambarUrl(form.gambar)"
                fit="cover"
                :preview-src-list="[getGambarUrl(form.gambar)]"
                class="produk-form-image"
              >
                <div
                  slot="error"
                  class="produk-grid-no-image"
                >
                  <i class="el-icon-picture-outline" />
                </div>
              </el-image>

              <!-- HAPUS GAMBAR -->
              <button
                type="button"
                class="remove-image"
                title="Hapus gambar"
                @click="hapusPreview"
              >
                <i class="el-icon-close" />
              </button>
            </div>

            <!-- BELUM ADA GAMBAR -->
            <div
              v-else
              class="upload-box"
              @click="pilihGambar"
            >
              <i class="el-icon-plus" />
              <span>Pilih Gambar</span>
            </div>

            <input
              ref="fileInput"
              type="file"
              accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
              class="hidden-file"
              @change="gambarDipilih"
            >

            <div class="upload-help">
              Format: JPG, JPEG, PNG, WEBP.
              Maksimal 2 MB.
            </div>

            <el-button
              v-if="form.gambar"
              type="text"
              icon="el-icon-refresh"
              @click="pilihGambar"
            >
              Ganti gambar
            </el-button>

          </div>
        </el-form-item>

        <el-form-item
          label="Kategori"
          prop="kategori_id"
        >
          <el-select
            v-model="form.kategori_id"
            clearable
            filterable
            placeholder="Pilih kategori"
            style="width: 100%"
          >
            <el-option
              v-for="item in kategori"
              :key="item.id"
              :label="item.nama"
              :value="item.id"
            />
          </el-select>
        </el-form-item>

        <el-form-item
          label="Kode"
          prop="kode"
        >
          <el-input
            v-model="form.kode"
            maxlength="100"
            placeholder="Kosongkan untuk generate otomatis"
          />

          <div class="form-help">
            Jika dikosongkan, kode akan dibuat
            otomatis oleh server.
          </div>
        </el-form-item>

        <el-form-item
          label="Nama Produk"
          prop="nama"
        >
          <el-input
            v-model="form.nama"
            maxlength="150"
            show-word-limit
            placeholder="Nama produk"
          />
        </el-form-item>

        <el-form-item label="Deskripsi">
          <el-input
            v-model="form.deskripsi"
            type="textarea"
            :rows="3"
            placeholder="Deskripsi produk"
          />
        </el-form-item>

        <el-row :gutter="12">
          <el-col :span="12">
            <el-form-item
              label="Harga Beli"
              prop="harga_beli"
            >
              <el-input
                v-model="form.harga_beli"
                type="number"
                min="0"
              >
                <template slot="prepend">
                  Rp
                </template>
              </el-input>
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item
              label="Harga Jual"
              prop="harga_jual"
            >
              <el-input
                v-model="form.harga_jual"
                type="number"
                min="0"
              >
                <template slot="prepend">
                  Rp
                </template>
              </el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="12">
          <el-col :span="12">
            <el-form-item
              label="Stok"
              prop="stok"
            >
              <el-input
                v-model="form.stok"
                type="number"
                min="0"
                :disabled="!!form.id"
              />

              <div
                v-if="form.id"
                class="form-help"
              >
                Stok sebaiknya dikelola melalui
                transaksi/mutasi stok.
              </div>
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item
              label="Stok Minimal"
              prop="stok_minimal"
            >
              <el-input
                v-model="form.stok_minimal"
                type="number"
                min="0"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="12">
          <el-col :span="12">
            <el-form-item label="Satuan">
              <el-input
                v-model="form.satuan"
                maxlength="20"
                placeholder="pcs"
              />
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item label="Status">
              <el-switch
                v-model="form.is_active"
                active-text="Aktif"
                inactive-text="Nonaktif"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>

      <span slot="footer">
        <el-button
          @click="dialogVisible = false"
        >
          Batal
        </el-button>

        <el-button
          type="primary"
          :loading="saving"
          @click="simpan"
        >
          Simpan
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import {
  getProduk,
  createProduk,
  updateProduk,
  deleteProduk,
  getKategori,
} from '@/api/master';

export default {
  name: 'Produk',

  data() {
    return {
      loading: false,
      saving: false,

      data: [],
      total: 0,

      kategori: [],

      viewMode: 'table',

      filterStok: 'semua',
      filterStatus: 'semua',

      query: {
        page: 1,
        per_page: 20,
        q: '',
        kategori_id: null,
        stok_rendah: null,
        is_active: null,
      },

      dialogVisible: false,

      form: {
        id: null,
        kategori_id: null,
        kode: '',
        nama: '',
        deskripsi: '',
        harga_beli: 0,
        harga_jual: 0,
        stok: 0,
        stok_minimal: 0,
        satuan: 'pcs',
        gambar: '',
        is_active: true,
      },

      /*
       * File baru yang akan dikirim ke Laravel.
       */
      imageFile: null,

      rules: {
        nama: [
          {
            required: true,
            message: 'Nama produk wajib diisi',
            trigger: 'blur',
          },
        ],

        harga_beli: [
          {
            required: true,
            message: 'Harga beli wajib diisi',
            trigger: 'blur',
          },
        ],

        harga_jual: [
          {
            required: true,
            message: 'Harga jual wajib diisi',
            trigger: 'blur',
          },
        ],

        stok: [
          {
            required: true,
            message: 'Stok wajib diisi',
            trigger: 'blur',
          },
        ],

        stok_minimal: [
          {
            required: true,
            message: 'Stok minimal wajib diisi',
            trigger: 'blur',
          },
        ],
      },
    };
  },

  created() {
    this.muatKategori();
    this.muat();
  },

  beforeDestroy() {
    this.hapusObjectUrl();
  },

  methods: {
    async muat() {
      this.loading = true;

      try {
        const params = {
          page: this.query.page,
          per_page: this.query.per_page,
        };

        if (this.query.q) {
          params.q = this.query.q;
        }

        if (this.query.kategori_id) {
          params.kategori_id =
            this.query.kategori_id;
        }

        if (this.query.stok_rendah !== null) {
          params.stok_rendah =
            this.query.stok_rendah;
        }

        if (this.query.is_active !== null) {
          params.is_active =
            this.query.is_active;
        }

        const res = await getProduk(params);

        this.data = res.data || [];
        this.total = res.total || 0;
      } catch (e) {
        this.tampilkanError(
          e,
          'Gagal memuat produk'
        );
      } finally {
        this.loading = false;
      }
    },

    async muatKategori() {
      try {
        const res = await getKategori({
          page: 1,
          per_page: 500,
        });

        this.kategori = res.data || [];
      } catch (e) {
        this.tampilkanError(
          e,
          'Gagal memuat kategori'
        );
      }
    },

    cari() {
      this.query.page = 1;
      this.muat();
    },

    ubahFilterStok(value) {
      this.query.stok_rendah =
        value === 'rendah'
          ? true
          : null;

      this.cari();
    },

    ubahFilterStatus(value) {
      if (value === 'semua') {
        this.query.is_active = null;
      } else {
        this.query.is_active = value;
      }

      this.cari();
    },

    ubahHalaman(page) {
      this.query.page = page;
      this.muat();
    },

    ubahUkuran(size) {
      this.query.per_page = size;
      this.query.page = 1;
      this.muat();
    },

    resetForm() {
      this.hapusObjectUrl();

      this.form = {
        id: null,
        kategori_id: null,
        kode: '',
        nama: '',
        deskripsi: '',
        harga_beli: 0,
        harga_jual: 0,
        stok: 0,
        stok_minimal: 0,
        satuan: 'pcs',
        gambar: '',
        is_active: true,
      };

      this.imageFile = null;
    },

    bukaTambah() {
      this.resetForm();

      this.dialogVisible = true;

      this.$nextTick(() => {
        if (this.$refs.form) {
          this.$refs.form.clearValidate();
        }
      });
    },

    bukaEdit(row) {
      this.hapusObjectUrl();

      this.form = {
        id: row.id,
        kategori_id:
          row.kategori_id || null,
        kode: row.kode || '',
        nama: row.nama || '',
        deskripsi: row.deskripsi || '',
        harga_beli:
          row.harga_beli || 0,
        harga_jual:
          row.harga_jual || 0,
        stok: row.stok || 0,
        stok_minimal:
          row.stok_minimal || 0,
        satuan: row.satuan || 'pcs',
        gambar: row.gambar || '',
        is_active:
          row.is_active !== false,
      };

      this.imageFile = null;

      this.dialogVisible = true;

      this.$nextTick(() => {
        if (this.$refs.form) {
          this.$refs.form.clearValidate();
        }
      });
    },

    pilihGambar() {
      if (this.$refs.fileInput) {
        this.$refs.fileInput.click();
      }
    },

    getGambarUrl(gambar) {
      if (!gambar) {
        return '';
      }

      // Kalau sudah berupa URL lengkap
      if (
        gambar.indexOf('http://') === 0 ||
        gambar.indexOf('https://') === 0 ||
        gambar.indexOf('blob:') === 0 ||
        gambar.indexOf('data:') === 0
      ) {
        return gambar;
      }

      return '/storage/' + gambar;
    },

    gambarDipilih(event) {
      const files = event.target.files;

      if (!files || !files.length) {
        return;
      }

      const file = files[0];

      /*
       * Validasi tipe.
       */
      const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
      ];

      if (!allowedTypes.includes(file.type)) {
        this.$message.error(
          'Format gambar harus JPG, JPEG, PNG atau WEBP.'
        );

        this.resetInputFile();
        return;
      }

      /*
       * Validasi ukuran maksimal 2 MB.
       */
      const maxSize =
        2 * 1024 * 1024;

      if (file.size > maxSize) {
        this.$message.error(
          'Ukuran gambar maksimal 2 MB.'
        );

        this.resetInputFile();
        return;
      }

      /*
       * Hapus object URL sebelumnya.
       */
      this.hapusObjectUrl();

      this.imageFile = file;

      // Preview gambar baru menggunakan blob URL
      this.form.gambar = URL.createObjectURL(file);
    },

    resetInputFile() {
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
    },

    hapusPreview() {
      this.hapusObjectUrl();

      this.imageFile = null;
      this.form.gambar = '';

      this.resetInputFile();
    },

    hapusObjectUrl() {
      if (
        this.form.gambar &&
        this.form.gambar.indexOf('blob:') === 0
      ) {
        URL.revokeObjectURL(this.form.gambar);
      }
    },

    resetFile() {
      this.hapusObjectUrl();

      this.imageFile = null;

      this.resetInputFile();
    },

    buatFormData() {
      const formData = new FormData();

      formData.append(
        'kategori_id',
        this.form.kategori_id || ''
      );

      formData.append(
        'kode',
        this.form.kode || ''
      );

      formData.append(
        'nama',
        this.form.nama
      );

      formData.append(
        'deskripsi',
        this.form.deskripsi || ''
      );

      formData.append(
        'harga_beli',
        Number(
          this.form.harga_beli || 0
        )
      );

      formData.append(
        'harga_jual',
        Number(
          this.form.harga_jual || 0
        )
      );

      /*
       * Stok hanya dikirim ketika create.
       */
      if (!this.form.id) {
        formData.append(
          'stok',
          Number(this.form.stok || 0)
        );
      }

      formData.append(
        'stok_minimal',
        Number(
          this.form.stok_minimal || 0
        )
      );

      formData.append(
        'satuan',
        this.form.satuan || 'pcs'
      );

      formData.append(
        'is_active',
        this.form.is_active ? '1' : '0'
      );

      /*
       * File hanya dikirim jika user memilih
       * gambar baru.
       */
      if (this.imageFile) {
        formData.append(
          'gambar',
          this.imageFile
        );
      }

      /*
       * Laravel method spoofing.
       *
       * PUT + multipart kadang bermasalah pada
       * PHP. Karena itu update dikirim POST
       * dengan _method=PUT.
       */
      if (this.form.id) {
        formData.append(
          '_method',
          'PUT'
        );
      }

      return formData;
    },

    async simpan() {
      this.$refs.form.validate(
        async valid => {
          if (!valid) {
            return;
          }

          this.saving = true;

          try {
            const formData =
              this.buatFormData();

            let res;

            if (this.form.id) {
              res = await updateProduk(
                this.form.id,
                formData
              );
            } else {
              res = await createProduk(
                formData
              );
            }

            this.$message.success(
              res.message ||
                'Produk berhasil disimpan'
            );

            this.dialogVisible = false;

            this.muat();
          } catch (e) {
            this.tampilkanError(
              e,
              'Gagal menyimpan produk'
            );
          } finally {
            this.saving = false;
          }
        }
      );
    },

    hapus(row) {
      this.$confirm(
        `Hapus produk "${row.nama}"?`,
        'Konfirmasi',
        {
          confirmButtonText: 'Hapus',
          cancelButtonText: 'Batal',
          type: 'warning',
        }
      )
        .then(async() => {
          try {
            const res =
              await deleteProduk(row.id);

            this.$message.success(
              res.message ||
                'Produk berhasil dihapus'
            );

            this.muat();
          } catch (e) {
            this.tampilkanError(
              e,
              'Produk tidak dapat dihapus'
            );
          }
        })
        .catch(() => {});
    },

    isStokRendah(row) {
      return (
        Number(row.stok || 0) <=
        Number(
          row.stok_minimal || 0
        )
      );
    },

    formatRupiah(value) {
      return Number(
        value || 0
      ).toLocaleString('id-ID');
    },

    tampilkanError(error, fallback) {
      let message = fallback;

      if (
        error &&
        error.response &&
        error.response.data
      ) {
        const response =
          error.response.data;

        if (response.message) {
          message = response.message;
        }

        /*
         * Laravel validation error.
         */
        if (
          response.errors &&
          typeof response.errors ===
            'object'
        ) {
          const errors = [];

          Object.keys(
            response.errors
          ).forEach(key => {
            const item =
              response.errors[key];

            if (Array.isArray(item)) {
              errors.push(
                ...item
              );
            }
          });

          if (errors.length) {
            message =
              errors.join('\n');
          }
        }
      }

      this.$message({
        type: 'error',
        message,
        duration: 5000,
      });
    },
  },
};
</script>

<style scoped>
.produk-page {
  padding: 0;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title {
  font-size: 16px;
  font-weight: 600;
}

.subtitle {
  margin-top: 4px;
  font-size: 12px;
  color: #909399;
}

.filter {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  margin-bottom: 16px;
  gap: 8px;
}

.search-input {
  width: 280px;
}

.kategori-select {
  width: 200px;
}

.stok-select {
  width: 150px;
}

.status-select {
  width: 150px;
}

.produk-name {
  font-weight: 600;
}

.produk-desc {
  margin-top: 3px;
  font-size: 12px;
  color: #909399;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.form-help {
  margin-top: 4px;
  color: #909399;
  font-size: 11px;
  line-height: 1.4;
}

/* =====================================================
   IMAGE TABLE
   ===================================================== */

.table-image-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
}

.table-image {
  width: 55px;
  height: 55px;
  border-radius: 6px;
  border: 1px solid #ebeef5;
}

.image-error {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 55px;
  height: 55px;
  color: #c0c4cc;
  background: #f5f7fa;
}

.no-image {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 55px;
  height: 55px;
  margin: auto;
  border-radius: 6px;
  color: #c0c4cc;
  background: #f5f7fa;
  border: 1px solid #ebeef5;
  font-size: 22px;
}

/* =====================================================
   UPLOAD
   ===================================================== */

.upload-container {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.upload-box {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 150px;
  height: 150px;
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  background: #fafafa;
  color: #909399;
  cursor: pointer;
  transition: 0.2s;
}

.upload-box:hover {
  border-color: #409eff;
  color: #409eff;
  background: #f5faff;
}

.upload-box i {
  margin-bottom: 8px;
  font-size: 28px;
}

.upload-box span {
  font-size: 12px;
}

.hidden-file {
  display: none;
}

.image-preview {
  position: relative;
  width: 150px;
  height: 150px;
  border: 1px solid #dcdfe6;
  border-radius: 6px;
  overflow: hidden;
  background: #f5f7fa;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 26px;
  height: 26px;
  padding: 0;
  border: 0;
  border-radius: 50%;
  color: #fff;
  background: rgba(0, 0, 0, 0.6);
  cursor: pointer;
}

.remove-image:hover {
  background: rgba(0, 0, 0, 0.8);
}

.upload-help {
  margin-top: 6px;
  color: #909399;
  font-size: 11px;
}

.pagination {
  padding: 20px 0 5px;
  text-align: right;
}

@media (max-width: 768px) {
  .header {
    align-items: flex-start;
    flex-direction: column;
    gap: 12px;
  }

  .filter {
    display: block;
  }

  .search-input,
  .kategori-select,
  .stok-select,
  .status-select {
    width: 100%;
    margin-bottom: 8px;
  }

  .pagination {
    overflow-x: auto;
  }
}
/* GRID PRODUK */
.produk-grid {
  min-height: 150px;
}

.produk-grid-col {
  margin-bottom: 16px;
}

.produk-card {
  height: 100%;
  border-radius: 8px;
  transition: all 0.2s ease;
  box-shadow: 8px 8px 10px -1px rgba(0, 0, 0, 0.1), 8px 6px 8px -1px rgba(0, 0, 0, 0.06);
}

.produk-card:hover {
  transform: translateY(-3px);
}

.produk-card-image {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 190px;
  overflow: hidden;
  background: #f5f7fa;
}

.produk-grid-image {
  display: block;
  width: 100%;
  height: 190px;
}

.produk-grid-no-image {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 190px;
  color: #c0c4cc;
  font-size: 42px;
  background: #f5f7fa;
}

.produk-status {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 2;
}

.produk-card-content {
  padding: 14px;
}

.produk-kode {
  margin-bottom: 5px;
  color: #909399;
  font-size: 11px;
}

.produk-card-name {
  height: 40px;
  overflow: hidden;
  color: #303133;
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.produk-card-category {
  margin-top: 7px;
  color: #909399;
  font-size: 12px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.produk-card-price {
  margin: 14px 0;
  color: #409eff;
  font-size: 17px;
  font-weight: 700;
}

.produk-card-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
  margin-top: 9px;
  color: #606266;
  font-size: 12px;
}

.produk-card-description {
  height: 34px;
  margin-top: 12px;
  overflow: hidden;
  color: #909399;
  font-size: 11px;
  line-height: 17px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.produk-card-actions {
  display: flex;
  justify-content: space-between;
  gap: 8px;
}

.produk-card-actions .el-button {
  flex: 1;
  margin-left: 0;
}

.produk-empty {
  padding: 50px 0;
  color: #909399;
  text-align: center;
}

.produk-empty i {
  font-size: 45px;
}

.produk-empty p {
  font-size: 13px;
}

.view-switch {
  margin-left: auto;
}

@media (max-width: 768px) {
  .view-switch {
    display: flex;
    width: 100%;
    margin: 8px 0;
  }

  .view-switch .el-button {
    flex: 1;
  }

  .produk-card-image,
  .produk-grid-image,
  .produk-grid-no-image {
    height: 170px;
  }
}

.produk-form-image {
  display: block;
  width: 150px;
  height: 150px;
  border-radius: 6px;
}
</style>
