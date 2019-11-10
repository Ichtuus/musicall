import apiCategories from '../../api/publicationCategory';

const UPDATE_CATEGORIES = 'UPDATE_CATEGORIES';
const UPDATE_IS_LOADING = 'UPDATE_IS_LOADING';

const state = {
  isLoading: false,
  categories: [],
};

const getters = {
  isLoading(state) {
    return state.isLoading;
  },
  categories(state) {
    return state.categories;
  }
};

const mutations = {
  [UPDATE_CATEGORIES](state, categories) {
    state.categories = categories;
  },
  [UPDATE_IS_LOADING](state, isLoading) {
    state.isLoading = isLoading;
  }
};

const actions = {
  async getCategories({commit}) {
    commit(UPDATE_IS_LOADING, true);
    const resp = await apiCategories.getAllByType({});
    console.log(resp);
    commit(UPDATE_CATEGORIES, resp);
    commit(UPDATE_IS_LOADING, false);
  }
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}