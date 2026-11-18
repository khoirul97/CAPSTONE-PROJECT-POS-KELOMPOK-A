const state = {
  unreadCount: 0,
  unreadCcCount: 0,
};

const mutations = {
  SET_UNREAD_COUNT(state, value) {
    state.unreadCount = value;
  },
  SET_UNREAD_CC_COUNT(state, value) {
    state.unreadCcCount = value;
  },
};

const actions = {
  setUnreadCount({ commit }, value) {
    commit('SET_UNREAD_COUNT', value);
  },
  setUnreadCcCount({ commit }, value) {
    commit('SET_UNREAD_CC_COUNT', value);
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
