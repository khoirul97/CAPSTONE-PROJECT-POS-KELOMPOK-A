import store from '@/store';

/**
 * Check current user permission.
 *
 * @param {Array} value
 * @returns {Boolean}
 */
export default function checkPermission(value) {
  if (
    !value ||
    !(value instanceof Array) ||
    value.length === 0
  ) {
    console.error(
      `Need permissions! Like v-permission="['manage permission','edit article']"`
    );

    return false;
  }

  const permissions =
    (store.getters && store.getters.permissions) || [];

  return permissions.some(permission =>
    value.includes(permission)
  );
}
