import request from '@/utils/request';

export function cekSesiAktif() {
  return request({
    url: '/sesi-kasir/aktif',
    method: 'get',
  });
}

export function bukaKas(data) {
  return request({
    url: '/sesi-kasir/buka',
    method: 'post',
    data,
  });
}

export function tutupKas(id, data) {
  return request({
    url: `/sesi-kasir/${id}/tutup`,
    method: 'post',
    data,
  });
}

export function riwayatSesi(params) {
  return request({
    url: '/sesi-kasir',
    method: 'get',
    params,
  });
}

export function detailSesi(id) {
  return request({
    url: `/sesi-kasir/${id}`,
    method: 'get',
  });
}
