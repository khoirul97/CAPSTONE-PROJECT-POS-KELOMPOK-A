import request from '@/utils/request';

export function getUserStatistics() {
  return request({
    url: 'statistics/user',
    method: 'get',
  });
}

export function getKategoriStatistics(params) {
  return request({
    url: 'statistics/kategori',
    method: 'get',
    params,
  });
}

export function getProdukStatistics(params) {
  return request({
    url: 'statistics/produk',
    method: 'get',
    params,
  });
}

export function getTransaksiStatistics(params) {
  return request({
    url: 'statistics/transaksi',
    method: 'get',
    params,
  });
}
