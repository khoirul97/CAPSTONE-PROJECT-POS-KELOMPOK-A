<template>
  <div class="log-viewer">
    <!-- Header -->
    <div class="log-header">
      <div class="header-left">
        <div class="log-title">
          <span class="icon">📄</span>
          <h2>laravel.log</h2>
        </div>
        <div class="log-stats">
          <span class="stat">{{ logInfo.total_lines || 0 }} lines</span>
          <span class="stat">{{ logInfo.size || '0 B' }}</span>
          <span class="stat">Modified: {{ logInfo.last_modified || '-' }}</span>
        </div>
      </div>
      <div class="header-right">
        <button class="btn-refresh" title="Refresh" @click="refreshLogs">
          <span class="icon">⟳</span>
        </button>
        <button class="btn-clear" title="Clear log" @click="clearLog">
          <span class="icon">🗑</span>
        </button>
        <button class="btn-download" title="Download" @click="downloadLog">
          <span class="icon">⬇</span>
        </button>
      </div>
    </div>

    <!-- Controls -->
    <div class="log-controls">
      <div class="control-group">
        <label>Search</label>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search..."
          class="search-input"
        >
      </div>
      <div class="control-group">
        <label>Type</label>
        <select v-model="filterType" class="filter-select">
          <option value="all">All</option>
          <option value="error">❌ Error</option>
          <option value="warning">⚠️ Warning</option>
          <option value="info">ℹ️ Info</option>
          <option value="debug">🐛 Debug</option>
          <option value="notice">📢 Notice</option>
          <option value="alert">🚨 Alert</option>
          <option value="emergency">🆘 Emergency</option>
          <option value="critical">💀 Critical</option>
        </select>
      </div>
      <div class="control-group">
        <label>Order</label>
        <button class="btn-sort" @click="toggleSortOrder">
          {{ sortOrder === 'asc' ? '⬆ Newest First' : '⬇ Oldest First' }}
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner" />
      <p>Loading logs...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="error-message">
      <span class="icon">⚠</span>
      <p>{{ error }}</p>
    </div>

    <!-- Empty -->
    <div v-else-if="filteredLines.length === 0" class="empty-state">
      <p>No logs found</p>
    </div>

    <!-- Log Content -->
    <div v-else ref="logContent" class="log-content" @scroll="handleScroll">
      <div
        v-for="(line, index) in filteredLines"
        :key="index"
        class="log-line"
        :class="'log-' + line.type"
        @dblclick="copyLine(line.content)"
      >
        <span class="line-number">{{ index + 1 }}</span>
        <span class="line-content" v-html="highlightSearch(line.content)" />
      </div>
    </div>

    <!-- Tombol Scroll ke Bawah -->
    <button
      v-if="showScrollButton"
      class="btn-scroll-bottom"
      title="Scroll to bottom"
      @click="scrollToBottom"
    >
      <span class="icon">⬇</span>
      <span class="label">Bottom</span>
    </button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LogViewer',
  data() {
    return {
      logInfo: {
        exists: false,
        size: '0 B',
        last_modified: '',
        total_lines: 0,
        path: '',
      },
      allLogLines: [],
      filteredLines: [],
      loading: false,
      error: null,
      filterType: 'all',
      searchQuery: '',
      sortOrder: 'desc',
      autoRefresh: false,
      refreshInterval: null,
      showScrollButton: false,
    };
  },
  watch: {
    filterType() {
      this.applyFilters();
    },
    searchQuery() {
      this.applyFilters();
    },
    sortOrder() {
      this.applyFilters();
    },
  },
  mounted() {
    this.loadLogInfo();
    this.loadLogs();

    if (this.autoRefresh) {
      this.refreshInterval = setInterval(() => {
        this.loadLogs();
        this.loadLogInfo();
      }, 30000);
    }
  },
  beforeDestroy() {
    if (this.refreshInterval) {
      clearInterval(this.refreshInterval);
    }
  },
  methods: {
    async loadLogInfo() {
      try {
        const response = await axios.get('/api/logs');
        this.logInfo = response.data;
      } catch (error) {
        console.error('Error loading log info:', error);
      }
    },
    async loadLogs() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/api/logs/content', {
          params: {
            sort: this.sortOrder,
          },
        });

        this.allLogLines = response.data.lines;
        this.logInfo.total_lines = response.data.total;

        this.applyFilters();

        // Auto scroll ke bawah setelah load (untuk data terbaru)
        if (this.sortOrder === 'desc') {
          this.$nextTick(() => {
            const content = this.$refs.logContent;
            if (content) {
              content.scrollTop = content.scrollHeight;
              // Sembunyikan tombol karena sudah di bawah
              this.showScrollButton = false;
            }
          });
        }
      } catch (error) {
        this.error = 'Failed to load logs: ' + (error.response?.data?.message || error.message);
        console.error('Error loading logs:', error);
      } finally {
        this.loading = false;
      }
    },
    applyFilters() {
      let lines = [...this.allLogLines];

      // Filter by type
      if (this.filterType !== 'all') {
        lines = lines.filter(line => line.type === this.filterType);
      }

      // Filter by search query
      if (this.searchQuery.trim() !== '') {
        const query = this.searchQuery.toLowerCase().trim();
        lines = lines.filter(line =>
          line.content.toLowerCase().includes(query)
        );
      }

      this.filteredLines = lines;
    },
    refreshLogs() {
      this.loadLogInfo();
      this.loadLogs();
    },
    toggleSortOrder() {
      this.sortOrder = this.sortOrder === 'desc' ? 'asc' : 'desc';
      this.loadLogs();
    },
    highlightSearch(text) {
      if (this.searchQuery.trim() === '') {
        return text;
      }

      const query = this.searchQuery.trim();
      const regex = new RegExp(query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
      return text.replace(regex, match => `<mark class="highlight">${match}</mark>`);
    },
    copyLine(content) {
      navigator.clipboard?.writeText(content).then(() => {
        // Optional: show toast notification
        console.log('Copied to clipboard');
      }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = content;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
      });
    },
    async clearLog() {
      if (!confirm('Are you sure you want to clear the log file?')) {
        return;
      }

      try {
        await axios.delete('/api/logs/clear');
        this.loadLogInfo();
        this.loadLogs();
      } catch (error) {
        this.error = 'Failed to clear log: ' + (error.response?.data?.message || error.message);
        console.error('Error clearing log:', error);
      }
    },
    async downloadLog() {
      try {
        const response = await axios.get('/api/logs/download', {
          responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'laravel.log');
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
      } catch (error) {
        this.error = 'Failed to download log: ' + (error.response?.data?.message || error.message);
        console.error('Error downloading log:', error);
      }
    },
    handleScroll() {
      const content = this.$refs.logContent;
      if (!content) {
        return;
      }

      const scrollPosition = content.scrollTop + content.clientHeight;
      const maxScroll = content.scrollHeight;

      // Tampilkan tombol jika jarak dari bawah > 100px
      this.showScrollButton = (maxScroll - scrollPosition) > 100;
    },
    scrollToBottom() {
      const content = this.$refs.logContent;
      if (content) {
        content.scrollTo({
          top: content.scrollHeight,
          behavior: 'smooth',
        });
        // Sembunyikan tombol setelah scroll selesai
        setTimeout(() => {
          this.showScrollButton = false;
        }, 300);
      }
    },
  },
};
</script>

<style scoped>
.log-viewer {
  background: #000;
  color: #fff;
  font-family: 'Consolas', 'Courier New', monospace;
  height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 0;
  margin: 0;
}

/* Header */
.log-header {
  background: #252526;
  border-bottom: 1px solid #3e3e42;
  padding: 10px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.log-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #d4d4d4;
}

.log-title h2 {
  margin: 0;
  font-size: 14px;
  font-weight: normal;
}

.log-title .icon {
  font-size: 16px;
}

.log-stats {
  display: flex;
  gap: 16px;
  font-size: 12px;
  color: #858585;
}

.log-stats .stat {
  display: flex;
  align-items: center;
}

.header-right {
  display: flex;
  gap: 8px;
}

.header-right button {
  background: transparent;
  border: none;
  color: #858585;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 16px;
  transition: all 0.2s;
}

.header-right button:hover {
  background: #3e3e42;
  color: #d4d4d4;
}

/* Controls */
.log-controls {
  background: #252526;
  border-bottom: 1px solid #3e3e42;
  padding: 8px 20px;
  display: flex;
  gap: 16px;
  align-items: flex-end;
  flex-shrink: 0;
  flex-wrap: wrap;
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.control-group label {
  font-size: 11px;
  color: #858585;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.search-input,
.filter-select {
  background: #3c3c3c;
  border: 1px solid #3e3e42;
  color: #d4d4d4;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 13px;
  font-family: inherit;
  min-width: 120px;
}

.search-input:focus,
.filter-select:focus {
  outline: none;
  border-color: #007acc;
}

.search-input {
  min-width: 200px;
}

.search-input::placeholder {
  color: #858585;
}

.btn-sort {
  background: #0e639c;
  border: none;
  color: white;
  padding: 4px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  font-family: inherit;
  transition: background 0.2s;
  height: 28px;
  white-space: nowrap;
}

.btn-sort:hover {
  background: #1177bb;
}

/* Log Content */
.log-content {
  flex: 1;
  overflow-y: auto;
  padding: 0;
  background: #1e1e1e;
  position: relative;
}

.log-line {
  display: flex;
  padding: 0 20px;
  min-height: 22px;
  align-items: flex-start;
  font-size: 13px;
  line-height: 1.6;
  border-bottom: none;
  cursor: default;
}

.log-line:hover {
  background: #2a2d2e;
}

.line-number {
  min-width: 60px;
  color: #858585;
  user-select: none;
  padding-right: 20px;
  text-align: right;
  font-size: 12px;
  opacity: 0.7;
}

.line-content {
  flex: 1;
  word-break: break-all;
  white-space: pre-wrap;
  padding: 1px 0;
}

/* Log Types - VSCode style */
.log-error .line-content {
  color: #f48771;
}

.log-warning .line-content {
  color: #cca700;
}

.log-info .line-content {
  color: #d4d4d4;
}

.log-debug .line-content {
  color: #569cd6;
}

.log-notice .line-content {
  color: #c586c0;
}

.log-alert .line-content {
  color: #f48771;
  font-weight: bold;
}

.log-emergency .line-content {
  color: #f48771;
  font-weight: bold;
}

.log-critical .line-content {
  color: #f48771;
  font-weight: bold;
}

.highlight {
  background: #515c6a;
  color: #d4d4d4;
  padding: 0 2px;
  border-radius: 2px;
}

/* Tombol Scroll ke Bawah */
.btn-scroll-bottom {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: #0e639c;
  color: white;
  border: none;
  border-radius: 50px;
  padding: 12px 20px;
  cursor: pointer;
  font-family: inherit;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.5);
  transition: all 0.3s ease;
  z-index: 1000;
  animation: fadeInUp 0.3s ease;
}

.btn-scroll-bottom:hover {
  background: #1177bb;
  transform: scale(1.05);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
}

.btn-scroll-bottom:active {
  transform: scale(0.95);
}

.btn-scroll-bottom .icon {
  font-size: 18px;
}

.btn-scroll-bottom .label {
  font-size: 13px;
  font-weight: 500;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Loading */
.loading-overlay {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  background: #1e1e1e;
}

.spinner {
  border: 3px solid #3e3e42;
  border-top: 3px solid #007acc;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error & Empty */
.error-message,
.empty-state {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: #1e1e1e;
  color: #d4d4d4;
}

.error-message .icon {
  font-size: 20px;
}

.error-message p {
  margin: 0;
  color: #f48771;
}

.empty-state p {
  margin: 0;
  color: #858585;
}

/* Scrollbar styling */
.log-content::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

.log-content::-webkit-scrollbar-track {
  background: #1e1e1e;
}

.log-content::-webkit-scrollbar-thumb {
  background: #424242;
  border-radius: 5px;
}

.log-content::-webkit-scrollbar-thumb:hover {
  background: #4a4a4a;
}

/* Responsive */
@media (max-width: 768px) {
  .log-header {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    padding: 10px;
  }

  .header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .log-stats {
    flex-wrap: wrap;
    gap: 8px;
  }

  .log-controls {
    padding: 8px 10px;
    gap: 8px;
  }

  .control-group {
    flex: 1;
    min-width: 100px;
  }

  .search-input {
    min-width: unset;
    width: 100%;
  }

  .log-line {
    padding: 0 10px;
    font-size: 12px;
  }

  .line-number {
    min-width: 40px;
    padding-right: 10px;
    font-size: 11px;
  }

  .btn-scroll-bottom {
    bottom: 20px;
    right: 20px;
    padding: 10px 14px;
    font-size: 12px;
  }

  .btn-scroll-bottom .label {
    display: none;
  }

  .btn-scroll-bottom .icon {
    font-size: 20px;
  }
}
</style>
