<template>
  <section class="app-main">
    <!-- IDENTITAS KEDAI -->
    <div class="kedai-header" style="display: flex; justify-content: center; margin-top: -2%;">
      <div class="kedai-header__accent" />

      <div class="kedai-identity">
        <!-- Logo kedai dari API -->
        <div class="kedai-logo-wrapper">
          <el-image
            v-if="logoKedai && !logoGagal"
            :src="logoUrl"
            :preview-src-list="[logoUrl]"
            fit="contain"
            class="kedai-logo"
            @error="logoGagalDimuat"
          />

          <div v-else class="kedai-logo-fallback">
            <i class="el-icon-shopping-bag-2" />
          </div>
        </div>

        <!-- Informasi kedai -->
        <div class="kedai-info">
          <div class="kedai-overline">
            <span class="status-dot" />
            SISTEM KASIR
          </div>

          <h1 class="kedai-name">
            {{ namaKedai }}
          </h1>

          <p v-if="alamatKedai" class="kedai-address">
            <i class="el-icon-location-outline" />
            {{ alamatKedai }}
          </p>

          <div class="kedai-divider" />

          <!-- <p class="kedai-caption">
            Selamat datang di sistem manajemen kedai Anda.
          </p> -->
        </div>
      </div>

      <!-- <div class="kedai-header__decoration decoration-one" /> -->
      <!-- <div class="kedai-header__decoration decoration-two" /> -->
    </div>

    <!-- KONTEN HALAMAN -->
    <transition name="fade-transform" mode="out-in">
      <keep-alive :include="cachedViews">
        <router-view :key="key" />
      </keep-alive>
    </transition>
  </section>
</template>

<script>
import { getPengaturanStruk } from '@/api/master';

export default {
  name: 'AppMain',

  data() {
    return {
      namaKedai: 'NAMA KEDAI',
      alamatKedai: '',
      logoKedai: '',
      logoGagal: false,
    };
  },

  computed: {
    cachedViews() {
      return this.$store.state.tagsView.cachedViews;
    },

    key() {
      return this.$route.fullPath;
    },

    // Mengikuti format penyimpanan logo:
    // /storage/ + nilai logo_kedai dari API.
    logoUrl() {
      if (!this.logoKedai) {
        return '';
      }

      // Mendukung URL lengkap jika API memang mengembalikannya.
      if (/^https?:\/\//i.test(this.logoKedai)) {
        return this.logoKedai;
      }

      const path = String(this.logoKedai)
        .replace(/^\/+/, '')
        .replace(/^storage\//, '');

      return `/storage/${path}`;
    },
  },

  created() {
    this.muatPengaturanKedai();
  },

  methods: {
    async muatPengaturanKedai() {
      try {
        const res = await getPengaturanStruk();

        let data = [];

        if (Array.isArray(res)) {
          data = res;
        } else if (res && Array.isArray(res.data)) {
          data = res.data;
        } else if (res && res.data && Array.isArray(res.data.data)) {
          data = res.data.data;
        }

        const pengaturan = {};

        data.forEach(item => {
          if (item && item.key) {
            pengaturan[item.key] = item.value;
          }
        });

        this.namaKedai =
          pengaturan.nama_kedai ||
          pengaturan.struk_header ||
          'NAMA KEDAI';

        this.alamatKedai = pengaturan.alamat_kedai || '';
        this.logoKedai = pengaturan.logo_kedai || '';
        this.logoGagal = false;
      } catch (error) {
        console.error(
          'Gagal memuat pengaturan identitas kedai:',
          error
        );
      }
    },

    logoGagalDimuat() {
      this.logoGagal = true;

      console.warn(
        'Logo kedai gagal dimuat:',
        this.logoUrl
      );
    },
  },
};
</script>

<style lang="scss" scoped>
.app-main {
  min-height: calc(100vh - 10vh);
  width: 100%;
  position: relative;
  overflow: hidden;
  box-sizing: border-box;
  padding: 20px 24px 30px;
  background: #f5f7fb;
}

/* =========================================
   HEADER IDENTITAS KEDAI
   ========================================= */

.kedai-header {
  position: relative;
  overflow: hidden;
  // margin-bottom: 10px;
  padding: 28px 20px;
  // border: 0.3px solid #e6ebf5;
  border-radius: 18px;
  //background: linear-gradient(120deg, #ffffff 0%, #f8fbff 65%, #edf5ff 100%);
  // box-shadow: 0 8px 28px rgba(35, 58, 95, 0.06);
  box-sizing: border-box;
}

.kedai-header__accent {
  position: absolute;
  top: 0;
  left: 0;
  width: 5px;
  height: 100%;
  // background: linear-gradient(180deg, #2563eb 0%, #38bdf8 100%);
}

.kedai-identity {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  gap: 10px;
}

/* =========================================
   LOGO
   ========================================= */

.kedai-logo-wrapper {
  flex: 0 0 60px;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  box-sizing: border-box;
  // border: 1px solid #e5edf8;
  border-radius: 18px;
  // background: #ffffff;
  box-shadow: 0 6px 18px rgba(35, 58, 95, 0.07);
}

.kedai-logo {
  display: block;
  width: 100%;
  height: 100%;
}

.kedai-logo-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  border-radius: 12px;
  color: #2563eb;
  background: linear-gradient(135deg, #eff6ff, #dbeafe);
  font-size: 38px;
}

/* =========================================
   NAMA DAN INFORMASI KEDAI
   ========================================= */

.kedai-info {
  min-width: 0;
  flex: 1;
}

.kedai-overline {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 8px;
  color: #64748b;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 2px;
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
}

.kedai-name {
  margin: 0;
  color: #172b4d;
  font-size: 19px;
  font-weight: 800;
  line-height: 1.35;
  letter-spacing: -0.6px;
  overflow-wrap: anywhere;
}

.kedai-address {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  margin: 5px 0 0;
  color: #64748b;
  font-size: 11px;
  line-height: 1.6;
}

.kedai-address i {
  margin-top: 3px;
  color: #3b82f6;
}

// .kedai-divider {
//   width: 42px;
//   height: 3px;
//   margin: 14px 0 10px;
//   border-radius: 5px;
//   background: linear-gradient(90deg, #2563eb, #38bdf8);
// }

.kedai-caption {
  margin: 0;
  color: #8492a6;
  font-size: 12px;
  line-height: 1.6;
}

/* =========================================
   DEKORASI BACKGROUND
   ========================================= */

.kedai-header__decoration {
  position: absolute;
  z-index: 1;
  pointer-events: none;
  border: 1px solid rgba(59, 130, 246, 0.08);
  border-radius: 50%;
}

// .decoration-one {
//   top: -95px;
//   right: -45px;
//   width: 240px;
//   height: 240px;
// }

// .decoration-two {
//   top: -50px;
//   right: 45px;
//   width: 150px;
//   height: 150px;
// }

/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 768px) {
  .app-main {
    padding: 14px 12px 24px;
  }

  .kedai-header {
    padding: 22px 18px;
    margin-bottom: 18px;
    border-radius: 14px;
  }

  .kedai-identity {
    gap: 15px;
  }

  .kedai-logo-wrapper {
    flex-basis: 78px;
    width: 78px;
    height: 78px;
    padding: 7px;
    border-radius: 13px;
  }

  .kedai-logo-fallback {
    font-size: 30px;
  }

  .kedai-name {
    font-size: 20px;
    letter-spacing: -0.3px;
  }

  .kedai-overline {
    font-size: 9px;
    letter-spacing: 1.5px;
  }

  .kedai-address {
    font-size: 12px;
  }

  .kedai-caption {
    font-size: 11px;
  }

  .decoration-one {
    right: -120px;
  }

  .decoration-two {
    right: -45px;
  }
}

@media (max-width: 420px) {
  .kedai-header {
    padding: 18px 14px;
  }

  .kedai-identity {
    align-items: flex-start;
    gap: 12px;
  }

  .kedai-logo-wrapper {
    flex-basis: 62px;
    width: 62px;
    height: 62px;
    padding: 5px;
  }

  .kedai-name {
    font-size: 17px;
  }

  .kedai-divider {
    margin: 10px 0 8px;
  }
}
</style>
