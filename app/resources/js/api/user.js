import request from '@/utils/request';
import Resource from '@/api/resource';

class UserResource extends Resource {
  constructor() {
    super('users');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }

  updatePermission(id, permissions) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'put',
      data: permissions,
    });
  }

  register(resource) {
    return request({
      url: '/' + this.uri + '/register',
      method: 'post',
      data: resource,
    });
  }

  listRegistrasi(query) {
    return request({
      url: '/' + this.uri + '/registrations/list',
      method: 'get',
      params: query,
    });
  }

  updatePassword(id, resource) {
    return request({
      url: '/' + this.uri + '/update_password/' + id,
      method: 'put',
      data: resource,
    });
  }

  updateRegistrasi(id, resource) {
    return request({
      url: '/' + this.uri + '/update_registrations/' + id,
      method: 'put',
      data: resource,
    });
  }

  updateStatus(id, resource) {
    return request({
      url: '/' + this.uri + '/update_status/' + id,
      method: 'put',
      data: resource,
    });
  }

  getSignatureUsers(query) {
    return request({
      url: '/' + this.uri + '/for-signature',
      method: 'get',
      params: query,
    });
  }
}

export { UserResource as default };
