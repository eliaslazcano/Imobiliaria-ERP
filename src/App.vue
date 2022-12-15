<template>
  <v-app>
    <v-app-bar
      v-if="!$route.meta.hideAppBar"
      app
      clipped-left
      dense
      dark
      :color="$vuetify.theme.dark ? undefined : 'primary'"
      class="d-print-none"
    >
      <v-app-bar-nav-icon @click.stop="showMenu = !showMenu" />
      <v-toolbar-title>{{ $route.meta.title ? $route.meta.title : appNome }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon v-if="$route.params.back || $route.meta.back" :to="$route.params.back || $route.meta.back" replace>
        <v-icon>mdi-arrow-left</v-icon>
      </v-btn>
      <v-menu left bottom class="d-print-none">
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon v-on="on" v-bind="attrs">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0" dense>
          <v-list-item to="/perfil">
            <v-list-item-icon>
              <v-icon>mdi-account</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Seu perfil</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item @click="$vuetify.theme.dark = !$vuetify.theme.dark">
            <v-list-item-icon>
              <v-icon v-if="$vuetify.theme.dark">mdi-white-balance-sunny</v-icon>
              <v-icon v-else>mdi-weather-night</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Modo {{ $vuetify.theme.dark ? 'claro' : 'escuro' }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item @click="$store.dispatch('logout')">
            <v-list-item-icon>
              <v-icon color="error">mdi-connection</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Desconectar</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-navigation-drawer
      v-if="!$route.meta.hideNavigationDrawer"
      app
      clipped
      v-model="showMenu"
    >
      <template v-slot:prepend v-if="$store.getters.sessionPayload">
        <v-list-item dense to="/perfil">
          <v-list-item-avatar class="justify-center">
            <v-avatar color="primary">
              <v-icon color="white">mdi-account</v-icon>
            </v-avatar>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>{{ $store.getters.sessionPayload.nome }}</v-list-item-title>
            <v-list-item-subtitle v-if="$store.getters.sessionPayload.organizacao">{{ $store.getters.sessionPayload.organizacao.nome }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
      </template>
      <v-list nav dense>
        <v-list-item-group color="primary">
          <v-list-item to="/">
            <v-list-item-icon>
              <v-icon>mdi-home</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Início</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item to="/locacoes">
            <v-list-item-icon>
              <v-icon>mdi-file-sign</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Locações</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
    <v-main :style="{ backgroundColor: !$vuetify.theme.dark ? '#f5f6f8' : null }">
      <transition name="fade-transition" @after-leave="$vuetify.goTo(1)" mode="out-in">
        <router-view/>
      </transition>
    </v-main>
    <v-footer
      v-if="!$route.meta.hideFooter"
      app
      dark
      :color="$vuetify.theme.dark ? undefined : 'primary'"
      class="justify-space-between caption py-1"
    >
      <span v-if="$store.getters.sessionPayload">SSID {{ $store.getters.sessionPayload.sessao }}</span>
      <span v-else>&copy; Vega Softwares</span>
      <span>versão {{ version }}</span>
    </v-footer>
    <v-snackbar
      app
      :timeout="3000"
      :color="$store.state.snackbarColor"
      :value="$store.state.snackbarShow"
      @input="$store.commit('hideSnackbar')"
    >
      <div class="d-flex">
        <v-icon small class="mr-2" v-if="$store.state.snackbarColor === 'warning'">mdi-alert</v-icon>
        <v-icon small class="mr-2" v-else-if="$store.state.snackbarColor === 'success'">mdi-check</v-icon>
        <v-icon small class="mr-2" v-else-if="$store.state.snackbarColor === 'error'">mdi-alert-octagon</v-icon>
        <v-icon small class="mr-2" v-else-if="$store.state.snackbarColor === 'info'">mdi-information</v-icon>
        <div style="text-transform: uppercase; font-weight: 600">{{$store.state.snackbarText}}</div>
      </div>
    </v-snackbar>
  </v-app>
</template>

<script>
import JsonPackage from '../package.json';
import {config} from '@/config';
export default {
  name: 'App',
  data: () => ({
    appNome: config.appNome,
    showMenu: null,
    version: JsonPackage.version,
  }),
  computed: {
    darkMode() {
      return this.$vuetify.theme.dark;
    }
  },
  watch: {
    darkMode() {
      this.refreshTheme();
    },
  },
  methods: {
    refreshTheme() {
      document.querySelector('meta[name="theme-color"]').setAttribute('content',  this.$vuetify.theme.dark ? '#272727' : '#1976D2');
      localStorage.setItem('darkMode', this.$vuetify.theme.dark ? '1' : '0');
    },
  },
  async created() {
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode != null) this.$vuetify.theme.dark = darkMode === '1';
  },
}
</script>

<style>
.v-application--is-ltr .v-data-footer__select div.v-select {
  margin-left: 4px;
}
.v-application--is-ltr div.v-data-footer__select {
  margin-right: auto;
  margin-left: 6px;
}
.v-application--is-ltr div.v-data-footer__pagination {
  margin-left: 6px;
  margin-right: 0;
}
thead.v-data-table-header th {
  white-space: nowrap;
}
.v-dialog:not(.v-dialog--fullscreen) {
  margin: .8rem !important;
}
</style>
