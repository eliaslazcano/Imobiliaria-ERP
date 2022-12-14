import Vue from 'vue'
import Vuex from 'vuex'
import router from '@/router'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    snackbarShow: false,
    snackbarColor: 'primary',
    snackbarText: '',
    token: null,
  },
  getters: {
    sessionPayload(state) {
      if (!state.token) return null;
      const partes = state.token.split('.');
      if (partes.length !== 3) return null;
      try {
        return JSON.parse(window.atob(partes[1]));
      } catch (e) {
        return null;
      }
    },
  },
  mutations: {
    initialiseStore(state) {
      if (localStorage.getItem('store')) {
        const stateMemorizado = JSON.parse(localStorage.getItem('store'));
        stateMemorizado.snackbarShow = false;
        this.replaceState(Object.assign(state, stateMemorizado));
      }
      this.subscribe((mutation, newState) => {
        localStorage.setItem('store', JSON.stringify(newState));
      });
    },
    showSnackbar(state, snackbar) {
      state.snackbarColor = snackbar.color;
      state.snackbarText = snackbar.text;
      state.snackbarShow = true;
    },
    hideSnackbar(state) {
      state.snackbarShow = false;
    },
    setToken(state, token) {
      state.token = token;
    },
  },
  actions: {
    logout({ commit }) {
      commit('setToken', null);
      return router.push('/login');
    },
  },
  modules: {
  }
})
