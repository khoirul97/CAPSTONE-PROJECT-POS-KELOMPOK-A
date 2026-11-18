import request from '@/utils/request';

export function listTransaksi(params) {
  return request({
    url: '/transaksi',
    method: 'get',
    params,
  });
}

export function simpanTransaksi(data) {
  return request({
    url: '/transaksi',
    method: 'post',
    data,
  });
}

export function detailTransaksi(id) {
  return request({
    url: `/transaksi/${id}`,
    method: 'get',
  });
}

export function batalTransaksi(id) {
  return request({
    url: `/transaksi/${id}/batal`,
    method: 'post',
  });
}
