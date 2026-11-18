import request from '@/utils/request';

export function listProduk(params) {
  return request({
    url: '/produk',
    method: 'get',
    params,
  });
}

export function cariProduk(q, page = 1) {
  return request({
    url: '/produk',
    method: 'get',
    params: {
      q,
      page,
      per_page: 20,
      is_active: 1,
    },
  });
}

export function cariProdukTransaksi(q, page = 1, kategoriId = null) {
  return request({
    url: '/produk/transaksi',
    method: 'get',
    params: {
      q,
      page,
      per_page: 20,
      is_active: 1,
      kategori_id: kategoriId || undefined,
    },
  });
}

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
  const isFormData = data instanceof FormData;

  if (isFormData) {
    // Laravel method spoofing
    data.append('_method', 'PUT');

    return request({
      url: `/produk/${id}`,
      method: 'post',
      data,
    });
  }

  return request({
    url: `/produk/${id}`,
    method: 'put',
    data,
  });
}

export function deleteProduk(id) {
  return request({
    url: `/produk/${id}`,
    method: 'delete',
  });
}

