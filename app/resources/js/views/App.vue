<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
import '@/assets/dark-mode.css';

export default {
  name: 'App',
};
</script>

<style>
.el-table .cell {
	white-space: normal !important;
	word-break: break-word !important;
}
h1, h2, h3, h4, h5, h6, p, span {
	white-space: normal !important;
	word-break: break-word !important;
}
.el-dialog {
  /* === BORDER RADIUS DASAR === */
  border-radius: 12px !important;
  overflow: hidden; /* Penting agar semua sudut konsisten melengkung */

  /* === DROP SHADOW (Box Shadow Premium) === */
  /* Efek shadow lembut dan modern (pilih salah satu) */
  box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.25),
              0 0 0 1px rgba(255, 255, 255, 0.05) !important;

  /* Alternatif shadow lebih dramatis (uncomment jika ingin) */
  /* box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; */

  /* Alternatif shadow dengan efek glow (uncomment jika ingin) */
  /* box-shadow: 0 0 20px rgba(64, 158, 255, 0.3),
                 0 10px 30px -5px rgba(0, 0, 0, 0.2) !important; */

  /* === HEADER STYLE === */
  .el-dialog__header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px 24px;
    border-radius: 12px 12px 0 0;
    margin-right: 0;

    /* Style judul header */
    .el-dialog__title {
      color: white;
      font-size: 18px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
  }

  /* === TOMBOL CLOSE (X) === */
  .el-dialog__headerbtn {
    top: 20px;
    right: 20px;

    .el-dialog__close {
      color: white;
      font-size: 18px;
      font-weight: bold;

      &:hover {
        color: rgba(255, 255, 255, 0.8);
      }
    }
  }

  /* === BODY STYLE === */
  .el-dialog__body {
    padding: 24px;
    /* background: #ffffff; */
    color: #606266;
    font-size: 14px;
    line-height: 1.6;
  }

  /* === FOOTER STYLE === */
  .el-dialog__footer {
    padding: 16px 24px 24px;
    /* background: #fafafa; */
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 12px 12px;
  }
}
</style>

