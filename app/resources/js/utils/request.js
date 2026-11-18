import '@/bootstrap';
import { Message } from 'element-ui';
import router from '@/router';
import store from '@/store';

// Create axios instance
const service = window.axios.create({
  baseURL: process.env.MIX_BASE_API,
  timeout: 0,
});

// Response interceptor
service.interceptors.response.use(
  response => response.data,
  error => {
    // ==========================
    // ABAIKAN NETWORK ERROR MURNI
    // (request dibatalkan/putus karena tab/app di-background,
    // koneksi timeout sesaat, request di-cancel, dll)
    // ==========================
    if (!error.response) {
      // Tidak perlu tampilkan toast "Network Error" ke user
      return Promise.reject(error);
    }

    const status = error.response?.status;
    const responseData = error.response?.data;

    // Ambil pesan error dari response jika ada
    let errorMessage = responseData?.message || responseData?.error || error.message;

    // ==========================
    // AUTH GAGAL (401 / 419)
    // ==========================
    if ([401, 419].includes(status)) {
      // Cek apakah ini force logout
      if (errorMessage && errorMessage.includes('di-logout secara paksa')) {
        store.dispatch('user/forceLogout');
        Message({
          message: errorMessage,
          type: 'warning',
          duration: 4000,
        });
        router.replace('/login');
        return Promise.reject(error);
      }

      // Cek pesan error lainnya dari server (email/password salah, akun inactive, rejected, disabled)
      if (errorMessage && errorMessage !== 'Unauthenticated.') {
        Message({
          message: errorMessage,
          type: 'error',
          duration: 5000,
        });
        return Promise.reject(error);
      }

      // Jika tidak ada pesan spesifik atau Unauthenticated, anggap sesi berakhir
      store.dispatch('user/forceLogout');
      Message({
        message: 'Sesi login Anda telah berakhir. Silakan login kembali.',
        type: 'warning',
        duration: 4000,
      });
      router.replace('/login');
      return Promise.reject(error);
    }

    // ==========================
    // ERROR LAINNYA (500, 403, dll)
    // ==========================
    if (responseData?.errors) {
      const validationErrors = responseData.errors;
      let firstError = '';
      for (const key in validationErrors) {
        if (validationErrors[key][0]) {
          firstError = validationErrors[key][0];
          break;
        }
      }
      errorMessage = firstError || errorMessage;
    }

    Message({
      message: errorMessage,
      type: 'error',
      duration: 5000,
    });

    return Promise.reject(error);
  }
);

export default service;
