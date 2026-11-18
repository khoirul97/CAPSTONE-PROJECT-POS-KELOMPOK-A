<template>
  <div>
    <div :class="{'hidden':hidden}" class="pagination-container-desktop">
      <el-pagination
        :background="background"
        :current-page.sync="currentPage"
        :page-size.sync="pageSize"
        :layout="layoutDesktop"
        :page-sizes="pageSizes"
        :total="total"
        v-bind="$attrs"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <div :class="{'hidden':hidden}" class="pagination-container-mobile">
      <el-pagination
        :background="background"
        :current-page.sync="currentPage"
        :page-size.sync="pageSize"
        :layout="layoutMobile"
        :page-sizes="pageSizes"
        :total="total"
        v-bind="$attrs"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
</template>

<script>
import { scrollTo } from '@/utils/scroll-to';

export default {
  name: 'Pagination',
  props: {
    total: {
      required: true,
      type: Number,
    },
    page: {
      type: Number,
      default: 1,
    },
    limit: {
      type: Number,
      default: 10,
    },
    pageSizes: {
      type: Array,
      default() {
        return [10, 15, 20, 30, 50, 100, 500, 1000];
      },
    },
    layoutDesktop: {
      type: String,
      default: 'total, sizes, prev, pager, next, jumper',
    },
    layoutMobile: {
      type: String,
      default: 'total, sizes, prev, next',
    },
    background: {
      type: Boolean,
      default: true,
    },
    autoScroll: {
      type: Boolean,
      default: true,
    },
    hidden: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    currentPage: {
      get() {
        return this.page;
      },
      set(val) {
        this.$emit('update:page', val);
      },
    },
    pageSize: {
      get() {
        return this.limit;
      },
      set(val) {
        this.$emit('update:limit', val);
      },
    },
  },
  methods: {
    handleSizeChange(val) {
      this.$emit('pagination', { page: 1, limit: val });
      if (this.autoScroll) {
        scrollTo(0, 800);
      }
    },
    handleCurrentChange(val) {
      this.$emit('pagination', { page: val, limit: this.pageSize });
      if (this.autoScroll) {
        scrollTo(0, 800);
      }
    },
  },
};
</script>

<style scoped>
/* backup default css */
/* .pagination-container {
  background: #fff;
  padding: 32px 16px;
}
.pagination-container.hidden {
  display: none;
} */

@media screen and (max-width: 699px) {
  .pagination-container-desktop {
    background: #fff;
    /* padding: 32px 16px; */
    margin-top: 20px;
    display: none;
  }
  .pagination-container-mobile {
    background: #fff;
    /* padding: 32px 16px; */
    margin-top: 20px;
    text-align: center;
    overflow-x: auto;
  }
  .pagination-container.hidden {
    display: none;
  }
}

@media screen and (min-width: 700px) {
  .pagination-container-desktop {
    background: #fff;
    /* padding: 32px 16px; */
    margin-top: 20px;
    text-align: center;
    overflow-x: auto;
  }
  .pagination-container-mobile {
    background: #fff;
    /* padding: 32px 16px; */
    margin-top: 20px;
    display: none;
  }
  .pagination-container.hidden {
    display: none;
  }
}
</style>
