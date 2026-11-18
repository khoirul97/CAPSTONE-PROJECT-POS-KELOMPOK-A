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
