import request from '@/utils/request';

// =====================================================
// KATEGORI
// =====================================================

export function getKategori(params) {
  return request({
    url: '/kategori',
    method: 'get',
    params,
  });
}

export function getKategoriTransaksi(params) {
  return request({
    url: '/kategori/transaksi',
    method: 'get',
    params,
  });
}

export function getKategoriDetail(id) {
  return request({
    url: `/kategori/${id}`,
    method: 'get',
  });
}

export function createKategori(data) {
  return request({
    url: '/kategori',
    method: 'post',
    data,
  });
}

export function updateKategori(id, data) {
  return request({
    url: `/kategori/${id}`,
    method: 'put',
    data,
  });
}

export function deleteKategori(id) {
  return request({
    url: `/kategori/${id}`,
    method: 'delete',
  });
}

// =====================================================
// PRODUK
// =====================================================

export function getProduk(params) {
  return request({
    url: '/produk',
    method: 'get',
    params,
  });
}

export function getProdukDetail(id) {
  return request({
    url: `/produk/${id}`,
    method: 'get',
  });
}

export function createProduk(data) {
  return request({
    url: '/produk',
    method: 'post',
    data,
  });
}

export function updateProduk(id, data) {
  return request({
    url: `/produk/${id}`,
    method: data instanceof FormData ? 'post' : 'put',
    data,
  });
}

export function deleteProduk(id) {
  return request({
    url: `/produk/${id}`,
    method: 'delete',
  });
}

// =====================================================
// PENGATURAN
// =====================================================

export function getPengaturan() {
  return request({
    url: '/pengaturan',
    method: 'get',
  });
}

export function getPengaturanStruk() {
  return request({
    url: '/pengaturan/struk',
    method: 'get',
  });
}

export function updatePengaturan(data) {
  return request({
    url: '/pengaturan',
    method: 'post',
    data,
  });
}

export function uploadLogoKedai(file) {
  const formData = new FormData();
  formData.append('logo', file);
  return request({
    url: '/pengaturan/logo',
    method: 'post',
    data: formData,
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });
}

export function deleteLogoKedai() {
  return request({
    url: '/pengaturan/logo',
    method: 'delete',
  });
}
