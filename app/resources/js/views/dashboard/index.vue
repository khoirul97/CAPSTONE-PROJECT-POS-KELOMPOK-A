<template>
  <div class="dashboard-editor-container">
    <!-- Stat Cards -->
    <el-row :gutter="20">
      <el-col v-for="stat in stats" :key="stat.key" :xs="24" :sm="12" :md="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-content">
            <div class="stat-icon" :style="{ background: stat.color }">
              <i :class="stat.icon" />
            </div>
            <div class="stat-info">
              <div class="stat-value">{{ stat.value }}</div>
              <div class="stat-label">{{ stat.label }}</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- Recent + Quick Links -->
    <el-row :gutter="20" class="mt-4">
      <el-col :xs="24" :md="16">
        <el-card v-role="['admin']" shadow="hover" class="recent-card">

          <div slot="header" class="card-header">
            <span>
              <i class="el-icon-time" />
              Recent Activities
            </span>
          </div>

          <el-table
            v-loading="loadingRecent"
            :data="recent"
            empty-text="Belum ada aktivitas"
            style="width: 100%"
          >

            <el-table-column
              label="User"
              min-width="150"
            >
              <template slot-scope="{ row }">
                <div class="activity-user">
                  <el-avatar
                    :size="32"
                    icon="el-icon-user-solid"
                  />

                  <span>
                    {{ row.user || 'System' }}
                  </span>
                </div>
              </template>
            </el-table-column>

            <el-table-column
              label="Aktivitas"
              min-width="300"
            >
              <template slot-scope="{ row }">

                <div class="activity-description">
                  {{ row.action }}
                </div>

                <el-tag
                  v-if="row.module"
                  size="mini"
                  type="info"
                  class="activity-module"
                >
                  {{ row.module }}
                </el-tag>

              </template>
            </el-table-column>

            <el-table-column
              label="Waktu"
              width="180"
              align="right"
            >
              <template slot-scope="{ row }">
                <span class="activity-time">
                  {{ formatDate(row.time) }}
                </span>
              </template>
            </el-table-column>

          </el-table>

          <el-pagination
            v-if="recentTotal > 0"
            class="recent-pagination"
            background
            layout="total, prev, pager, next"
            :current-page="recentPage"
            :page-size="recentLimit"
            :total="recentTotal"
            @current-change="handleRecentPageChange"
          />

        </el-card>
      </el-col>

      <el-col :xs="24" :md="8">
        <el-card shadow="hover">
          <div slot="header">Quick Links</div>
          <div class="quick-links">
            <el-button v-permission="['manage kategori']" type="primary" icon="el-icon-plus" @click="$router.push('/master/kategori')">
              New Kategori
            </el-button>
            <el-button v-permission="['manage produk']" type="success" icon="el-icon-plus" @click="$router.push('/master/produk')">
              New Produk
            </el-button>
            <el-button v-permission="['manage stok']" type="danger" icon="el-icon-plus" @click="$router.push('/master/stok')">
              New Stok
            </el-button>
            <el-button v-permission="['open sesi kasir']" type="default" icon="el-icon-plus" @click="$router.push('/kasir/open-kas')">
              Buka / Tutup Kas
            </el-button>
            <el-button v-permission="['manage transaksi']" type="warning" icon="el-icon-plus" @click="$router.push('/kasir/transaksi')">
              New Transaksi
            </el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import permission from '@/directive/permission';
import checkPermission from '@/utils/permission';
import role from '@/directive/role';

import { activityApi } from '@/api/admin';
// import { getKategori } from '@/api/kategori';
// import { getProduk } from '@/api/produk';
// import { listTransaksi } from '@/api/transaksi';

import { getUserStatistics, getKategoriStatistics, getProdukStatistics, getTransaksiStatistics } from '@/api/statistics';

export default {
  name: 'AdminDashboard',
  directives: {
    permission,
    role,
  },
  data() {
    return {
      loadingRecent: false,

      recent: [],
      recentPage: 1,
      recentLimit: 15,
      recentTotal: 0,
      stats: [
        { key: 'users', label: 'Users', value: 0, icon: 'el-icon-user', color: '#409EFF' },
        { key: 'kategori', label: 'Kategori', value: 0, icon: 'el-icon-collection', color: '#67C23A' },
        { key: 'produk', label: 'Produk', value: 0, icon: 'el-icon-goods', color: '#E6A23C' },
        { key: 'transaksi', label: 'Transaksi', value: 0, icon: 'el-icon-shopping-cart-full', color: '#F56C6C' },
      ],
    };
  },
  computed: {
    isAdmin() {
      const roles = this.$store.getters.roles || [];
      return roles.includes('admin');
    },
  },
  created() {
    this.loadStats();
    if (this.isAdmin) {
      this.loadRecent();
    }
  },
  methods: {
    // ---------- HELPERS ----------
    extractTotal(res) {
      if (!res) {
        return 0;
      }
      if (Array.isArray(res)) {
        return res.length;
      }
      if (typeof res.total === 'number') {
        return res.total;
      }
      if (res.data && typeof res.data.total === 'number') {
        return res.data.total;
      }
      if (Array.isArray(res.data)) {
        return res.data.length;
      }
      return 0;
    },
    formatDate(value) {
      if (!value) {
        return '-';
      }
      const d = new Date(value);
      if (Number.isNaN(d.getTime())) {
        return value;
      }
      return d.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },

    // ---------- STATS ----------
    async loadStats() {
      const sources = {};

      if (checkPermission(['view menu dashboard'])) {
        sources.users = () => getUserStatistics({ limit: 0 });
      }

      if (checkPermission(['view menu dashboard'])) {
        sources.kategori = () => getKategoriStatistics({ per_page: 1 });
      }

      if (checkPermission(['view menu dashboard'])) {
        sources.produk = () => getProdukStatistics({ per_page: 1 });
      }

      if (checkPermission(['view menu dashboard'])) {
        sources.transaksi = () => getTransaksiStatistics({ per_page: 1 });
      }

      const keys = Object.keys(sources);

      if (keys.length === 0) {
        return;
      }

      const results = await Promise.allSettled(
        keys.map(key => sources[key]())
      );

      const totals = {};

      results.forEach((result, index) => {
        const key = keys[index];

        totals[key] =
          result.status === 'fulfilled'
            ? this.extractTotal(result.value)
            : 0;

        if (result.status === 'rejected') {
          console.warn(
            `[dashboard] gagal memuat stat "${key}":`,
            result.reason
          );
        }
      });

      this.stats = this.stats.map(stat => ({
        ...stat,
        value: totals[stat.key] ?? 0,
      }));
    },

    // ---------- RECENT ACTIVITIES ----------
    async loadRecent(page = 1) {
      this.loadingRecent = true;

      try {
        const res = await activityApi.recent({
          page,
          limit: this.recentLimit,
        });

        let pagination = res;

        if (
          res &&
          res.data &&
          !Array.isArray(res.data) &&
          typeof res.data === 'object' &&
          typeof res.data.current_page !== 'undefined'
        ) {
          pagination = res.data;
        }

        const items = Array.isArray(pagination.data)
          ? pagination.data
          : (Array.isArray(res) ? res : []);

        this.recent = items.map((item) => ({
          id: item.id,
          user: item.user ? item.user.name : 'System',
          action: item.description || '-',
          module: item.module || '',
          actionType: item.action || '',
          time: item.created_at || null,
        }));

        this.recentPage = Number(pagination.current_page || page);
        this.recentLimit = Number(pagination.per_page || this.recentLimit);
        this.recentTotal = Number(pagination.total || 0);
      } catch (e) {
        console.error('Gagal memuat Recent Activities:', e);
        this.recent = [];
        this.recentPage = 1;
        this.recentTotal = 0;
      } finally {
        this.loadingRecent = false;
      }
    },
    handleRecentPageChange(page) {
      this.loadRecent(page);
    },
  },
};
</script>

<style scoped>
.dashboard-editor-container {
  padding: 20px;
}

.stat-card {
  margin-bottom: 20px;
}

.stat-content {
  display: flex;
  align-items: center;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 28px;
  margin-right: 16px;
  flex-shrink: 0;
}

.stat-info {
  min-width: 0;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  line-height: 1.2;
}

.stat-label {
  color: #909399;
  font-size: 14px;
  margin-top: 4px;
}

.mt-4 {
  margin-top: 20px;
}

.quick-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.quick-links .el-button + .el-button {
  margin-left: 0;
}

.recent-card {
  width: 100%;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 600;
}

.card-header i {
  margin-right: 6px;
}

.activity-user {
  display: flex;
  align-items: center;
  gap: 10px;
}

.activity-user span {
  font-weight: 500;
}

.activity-description {
  line-height: 20px;
  margin-bottom: 4px;
}

.activity-module {
  margin-top: 2px;
}

.activity-time {
  color: #909399;
  font-size: 12px;
}

@media (max-width: 768px) {
  .activity-user {
    gap: 6px;
  }

  .activity-user .el-avatar {
    width: 28px !important;
    height: 28px !important;
    line-height: 28px !important;
  }

  .activity-description {
    white-space: normal;
    word-break: break-word;
  }
}
.recent-pagination {
  margin-top: 20px;
  padding: 10px 0 5px;
  text-align: center;
}
@media (max-width: 768px) {
  .activity-user {
    gap: 6px;
  }

  .activity-user .el-avatar {
    width: 28px !important;
    height: 28px !important;
    line-height: 28px !important;
  }

  .activity-description {
    white-space: normal;
    word-break: break-word;
  }

  .recent-pagination {
    text-align: center;
    overflow-x: auto;
    white-space: nowrap;
  }
}
</style>
