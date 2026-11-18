import request from '@/utils/request';

export function dashboard() {
  return request({
    url: '/laporan/dashboard',
    method: 'get',
  });
}

export function laporanHarian(params) {
  return request({
    url: '/laporan/harian',
    method: 'get',
    params,
  });
}

export function laporanPerShift(params) {
  return request({
    url: '/laporan/per-shift',
    method: 'get',
    params,
  });
}

export function laporanPerProduk(params) {
  return request({
    url: '/laporan/per-produk',
    method: 'get',
    params,
  });
}

export function laporanPerKategori(params) {
  return request({
    url: '/laporan/per-kategori',
    method: 'get',
    params,
  });
}

export function laporanLabaRugi(params) {
  return request({
    url: '/laporan/laba-rugi',
    method: 'get',
    params,
  });
}

export function laporanTransaksi(params) {
  return request({
    url: '/laporan/transaksi',
    method: 'get',
    params,
  });
}

export function daftarKasir() {
  return request({
    url: '/laporan/kasir',
    method: 'get',
  });
}

export function laporanStok(params) {
  return request({
    url: '/laporan/stok',
    method: 'get',
    params,
  });
}

export function getLaporanKategori(params) {
  return request({
    url: '/laporan/per-kategori',
    method: 'get',
    params,
  });
}

export function getKategori(params) {
  return request({
    url: '/kategori',
    method: 'get',
    params,
  });
}
