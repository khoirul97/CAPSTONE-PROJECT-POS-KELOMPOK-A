import request from '@/utils/request';

// export function getActiveUsers() {
//   return request({
//     url: '/active-users',
//     method: 'get',
//   });
// }

export function getActiveUsers(params) {
  return request({
    url: '/active-users',
    method: 'get',
    params: params, // This sends the parameters as query string
  });
}

export function logoutUser(userId) {
  return request({
    url: `/active-users/logout/${userId}`,
    method: 'post',
  });
}

export function logoutAllUsers() {
  return request({
    url: '/active-users/logout-all',
    method: 'post',
  });
}

export function resetLogoutAllUsers() {
  return request({
    url: '/active-users/reset-logout-all',
    method: 'post',
  });
}

export function activateUser(userId) {
  return request({
    url: `/active-users/activate/${userId}`,
    method: 'post',
  });
}

// TAMBAHKAN FUNCTION DEACTIVATE
export function deactivateUser(userId) {
  return request({
    url: `/active-users/deactivate/${userId}`,
    method: 'post',
  });
}
