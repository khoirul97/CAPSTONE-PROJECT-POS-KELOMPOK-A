import request from '@/utils/request';

export function getStokMutasi(params) {
  return request({
    url: '/stok-mutasi',
    method: 'get',
    params,
  });
}

export function stokMasuk(data) {
  return request({
    url: '/stok-mutasi/masuk',
    method: 'post',
    data,
  });
}

export function penyesuaianStok(data) {
  return request({
    url: '/stok-mutasi/penyesuaian',
    method: 'post',
    data,
  });
}
