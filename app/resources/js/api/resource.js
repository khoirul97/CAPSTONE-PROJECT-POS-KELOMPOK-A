import request from '@/utils/request';

/**
 * Simple RESTful resource class
 */
class Resource {
  constructor(uri) {
    this.uri = uri;
  }
  list(query) {
    return request({
      url: '/' + this.uri,
      method: 'get',
      params: query,
    });
  }
  get(id) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'get',
    });
  }
  store(resource) {
    return request({
      url: '/' + this.uri,
      method: 'post',
      data: resource,
    });
  }
  storeFormData(data) {
    return request({
      url: '/users',
      method: 'post',
      data,
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  }
  update(id, resource) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'put',
      data: resource,
    });
  }
  updateFormData(id, data) {
    return request({
      url: `/users/${id}`,
      method: 'post', // 🔥 bukan PUT
      data,
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  }
  updatePost(id, resource) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'post',
      data: resource,
    });
  }
  destroy(id) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'delete',
    });
  }
  markAsRead(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/mark-read',
      method: 'post',
    });
  }
  markMultipleAsRead(ids) {
    return request({
      url: '/' + this.uri + '/mark-multiple-read',
      method: 'post',
      data: { ids },
    });
  }
  addCc(mailId, payload) {
    return request({
      url: `/${this.uri}/${mailId}/cc/add`,
      method: 'post',
      data: payload,
    });
  }
  getAvailableCcUsers() {
    return this.request({
      url: `/${this.uri}/available-users`,
      method: 'get',
    });
  }
  getReport(id) {
    return request({
      url: `/mail/report/${id}`,
      method: 'get',
      responseType: 'blob', // 🔥 wajib untuk PDF
    });
  }
}

export { Resource as default };
