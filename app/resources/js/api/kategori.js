import request from '@/utils/request';

/**
 * Get list kategori (dengan pagination & search).
 *
 * @param {Object} params - Query params
 * @param {string} [params.q]         - Search keyword
 * @param {number} [params.page]      - Page number
 * @param {number} [params.per_page]  - Items per page
 * @returns {Promise}
 */
export function getKategori(params) {
  return request({
    url: '/kategori',
    method: 'get',
    params,
  });
}

export function getKategoriStok(params) {
  return request({
    url: '/kategori/stok',
    method: 'get',
    params,
  });
}

/**
 * Get detail kategori by ID.
 *
 * @param {number|string} id
 * @returns {Promise}
 */
export function getKategoriDetail(id) {
  return request({
    url: `/kategori/${id}`,
    method: 'get',
  });
}

/**
 * Create new kategori.
 *
 * @param {Object} data
 * @param {string} data.nama
 * @param {string} [data.deskripsi]
 * @returns {Promise}
 */
export function createKategori(data) {
  return request({
    url: '/kategori',
    method: 'post',
    data,
  });
}

/**
 * Update kategori.
 *
 * @param {number|string} id
 * @param {Object} data
 * @returns {Promise}
 */
export function updateKategori(id, data) {
  return request({
    url: `/kategori/${id}`,
    method: 'put',
    data,
  });
}

/**
 * Delete kategori.
 *
 * @param {number|string} id
 * @returns {Promise}
 */
export function deleteKategori(id) {
  return request({
    url: `/kategori/${id}`,
    method: 'delete',
  });
}
