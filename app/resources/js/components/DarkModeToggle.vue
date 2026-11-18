<template>
  <div @click="toggleDarkMode">
    <i :class="isDarkMode ? 'el-icon-moon' : 'el-icon-sunny'" class="darkmode-icon" />
  </div>
</template>

<script>
export default {
  name: 'DarkModeToggle',
  data() {
    return {
      isDarkMode: localStorage.getItem('darkMode') === 'true',
    };
  },
  mounted() {
    this.applyDarkMode();
  },
  methods: {
    toggleDarkMode() {
      this.isDarkMode = !this.isDarkMode;
      localStorage.setItem('darkMode', this.isDarkMode);
      this.applyDarkMode();
    },
    applyDarkMode() {
      if (this.isDarkMode) {
        document.documentElement.classList.add('dark-mode'); // Ganti body ke html
      } else {
        document.documentElement.classList.remove('dark-mode');
      }
    },
  },
};
</script>

<style scoped>
.darkmode-icon {
  font-size: 24px;
  cursor: pointer;
  transition: color 0.3s;
}

.darkmode-icon:hover {
  color: #f39c12;
}
</style>
