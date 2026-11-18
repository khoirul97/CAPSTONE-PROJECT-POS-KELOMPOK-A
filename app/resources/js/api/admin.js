import request from '@/utils/request';

// ============ AUTH ============
export function login(data) {
  return request({
    url: '/auth/login',
    method: 'post',
    data,
  });
}

export function getInfo() {
  return request({
    url: '/auth/user',
    method: 'get',
  });
}

export function logout() {
  return request({
    url: '/auth/logout',
    method: 'post',
  });
}

// ============ USERS ============
export function fetchUsers(params) {
  return request({
    url: '/users',
    method: 'get',
    params,
  });
}

export function getUserStatistics() {
  return request({
    url: '/users/statistics',
    method: 'get',
  });
}

export function fetchUser(id) {
  return request({
    url: `/users/${id}`,
    method: 'get',
  });
}

export function createUser(data) {
  return request({
    url: '/users',
    method: 'post',
    data,
  });
}

export function updateUser(id, data) {
  return request({
    url: `/users/${id}`,
    method: 'put',
    data,
  });
}

export function deleteUser(id) {
  return request({
    url: `/users/${id}`,
    method: 'delete',
  });
}

// ============ GENERIC CRUD FACTORY ============
export function crud(resource) {
  return {
    list: (params) =>
      request({
        url: `/${resource}`,
        method: 'get',
        params,
      }),

    show: (id) =>
      request({
        url: `/${resource}/${id}`,
        method: 'get',
      }),

    create: (data) =>
      request({
        url: `/${resource}`,
        method: 'post',
        data,
      }),

    update: (id, data) =>
      request({
        url: `/${resource}/${id}`,
        method: 'put',
        data,
      }),

    remove: (id) =>
      request({
        url: `/${resource}/${id}`,
        method: 'delete',
      }),
  };
}

// ============ SPECIFIC APIs ============
// export const beritaApi = crud('berita');
export const beritaApi = {
  ...crud('berita'),

  updateWithFile: (id, data) =>
    request({
      url: `/berita/${id}`,
      method: 'post',
      data,
    }),
};
// export const lelangApi = crud('lelang');
export const lelangApi = {
  ...crud('lelang'),

  updateWithFile: (id, data) =>
    request({
      url: `/lelang/${id}`,
      method: 'post',
      data,
    }),
};
// export const produkApi = crud('produk-info');
export const produkApi = {
  ...crud('produk-info'),

  updateWithFile: (id, data) =>
    request({
      url: `/produk-info/${id}`,
      method: 'post',
      data,
    }),
};

// export const brosurApi = crud('brosur');
export const brosurApi = {
  ...crud('brosur'),

  updateWithFile: (id, data) =>
    request({
      url: `/brosur/${id}`,
      method: 'post',
      data,
    }),
};

// export const bannerApi = crud('banner');
export const bannerApi = {
  ...crud('banner'),

  updateWithFile: (id, data) =>
    request({
      url: `/banner/${id}`,
      method: 'post',
      data,
    }),
};

export const faqApi = crud('faqs');
// export const cilemApi = crud('cilem');
export const cilemApi = {
  ...crud('cilem'),

  updateWithFile: (id, data) =>
    request({
      url: `/cilem/${id}`,
      method: 'post',
      data,
    }),
};
// export const penghargaanApi = crud('penghargaan');
export const penghargaanApi = {
  ...crud('penghargaan'),
  updateWithFile: (id, data) =>
    request({
      url: `/penghargaan/${id}`,
      method: 'post',
      data,
    }),
};
// export const galleryApi = crud('gallery');
export const galleryApi = {
  ...crud('gallery'),
  updateWithFile: (id, data) =>
    request({
      url: `/gallery/${id}`,
      method: 'post',
      data,
    }),
  deletePhoto: (id) =>
    request({
      url: `/gallery/photos/${id}`,
      method: 'delete',
    }),
};
export const kantorCabangApi = crud('kantor-cabang');
// export const pageApi = crud('pages');
export const pageApi = {
  ...crud('pages'),

  updateWithFile: (id, data) =>
    request({
      url: `/pages/${id}`,
      method: 'post',
      data,
    }),
};
export const pengaduanApi = crud('pengaduan');
export const settingApi = crud('settings');
export const menuApi = crud('menus');

export const hargaEmasApi = {
  ...crud('harga-emas'),

  bulkUpdate: (items) =>
    request({
      url: '/harga-emas/bulk-update',
      method: 'post',
      data: { items },
    }),
};

export const settingBulkApi = {
  ...settingApi,

  bulkUpdate: (settings) =>
    request({
      url: '/settings/bulk-update',
      method: 'post',
      data: { settings },
    }),

  uploadImage: (id, file) => {
    const formData = new FormData();
    formData.append('file', file);

    return request({
      url: `/settings/${id}/upload`,
      method: 'post',
      data: formData,
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
};

export const laporanApi = {
  ...crud('laporan'),

  kategoriIndex: (params) =>
    request({
      url: '/laporan/kategori',
      method: 'get',
      params,
    }),

  kategoriOptions: () =>
    request({
      url: '/laporan/kategori/options',
      method: 'get',
    }),

  kategoriStore: (data) =>
    request({
      url: '/laporan/kategori',
      method: 'post',
      data,
    }),

  kategoriUpdate: (id, data) =>
    request({
      url: `/laporan/kategori/${id}`,
      method: 'put',
      data,
    }),

  kategoriDestroy: (id) =>
    request({
      url: `/laporan/kategori/${id}`,
      method: 'delete',
    }),

  detailStore: (laporanId, data) =>
    request({
      url: `/laporan/${laporanId}/detail`,
      method: 'post',
      data,
    }),

  /*
   * PENTING:
   *
   * Method ini mengirim FormData yang berisi:
   *   - nama_laporan
   *   - order
   *   - file (opsional)
   *   - _method = 'PUT'
   *
   * PHP tidak bisa membaca file dari request PUT multipart,
   * jadi kita kirim via POST + _method=PUT (method spoofing).
   *
   * Route Laravel tetap:
   *   Route::put('laporan-detail/{detail:id}', ...)
   */
  detailUpdate: (id, data) =>
    request({
      url: `/laporan-detail/${id}`,
      method: 'post',
      data,
    }),

  detailDestroy: (id) =>
    request({
      url: `/laporan-detail/${id}`,
      method: 'delete',
    }),
};

export const activityApi = {
  recent: (params) =>
    request({
      url: '/activity-logs/recent',
      method: 'get',
      params,
    }),

  list: (params) =>
    request({
      url: '/activity-logs',
      method: 'get',
      params,
    }),
};

// ============ PENGURUS ============
export const pengurusApi = {
  ...crud('pengurus'),

  updateWithFile: (id, data) =>
    request({
      url: `/pengurus/${id}`,
      method: 'post',
      data,
    }),
};

// ============ LOGS ============
export const logApi = {
  list: (params) =>
    request({
      url: '/logs',
      method: 'get',
      params,
    }),

  content: (params) =>
    request({
      url: '/logs/content',
      method: 'get',
      params,
    }),

  clear: () =>
    request({
      url: '/logs/clear',
      method: 'post',
    }),

  download: () =>
    request({
      url: '/logs/download',
      method: 'get',
      responseType: 'blob',
    }),
};
