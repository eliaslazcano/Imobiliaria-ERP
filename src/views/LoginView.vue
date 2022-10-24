<template>
  <v-container class="fill-height">
    <v-card width="22rem" class="mx-auto">
      <v-form ref="form-login" @submit.prevent="submitLogin" :disabled="disableLoginForm">
        <v-card-text>
          <div class="text-center mb-5">
            <img src="../assets/logo.png" alt="" style="width: 5rem">
            <p class="title" v-if="!!appNome">{{ appNome }}</p>
          </div>
          <v-text-field
            label="Login"
            prepend-inner-icon="mdi-account"
            class="mt-2"
            dense
            outlined
            v-model="iptLogin"
            :rules="iptLoginRules"
            :placeholder="!inputsAutofilled ? ' ' : 'Insira o usuário'"
            :persistent-placeholder="!inputsAutofilled"
            autocomplete="username"
          ></v-text-field>
          <v-text-field
            label="Senha"
            prepend-inner-icon="mdi-lock"
            class="mt-2"
            dense
            outlined
            v-model="iptSenha"
            :rules="iptSenhaRules"
            :placeholder="!inputsAutofilled ? ' ' : 'Insira sua senha'"
            :persistent-placeholder="!inputsAutofilled"
            :type="iptSenhaMostrar ? 'text' : 'password'"
            :append-icon="iptSenhaMostrar ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append="iptSenhaMostrar = !iptSenhaMostrar"
            autocomplete="current-password"
          ></v-text-field>
        </v-card-text>
        <v-card-actions class="justify-center px-4">
          <v-btn
            class="mb-2"
            :color="$vuetify.theme.dark ? 'secondary' : 'primary'"
            :disabled="disableLoginForm"
            :loading="loadingLogin"
            small
            depressed
            block
            type="submit"
          >Entrar</v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
    <v-btn color="primary" fab fixed top right @click="$vuetify.theme.dark = !$vuetify.theme.dark">
      <v-icon v-if="$vuetify.theme.dark">mdi-lightbulb-on</v-icon>
      <v-icon v-else>mdi-lightbulb-off</v-icon>
    </v-btn>
  </v-container>
</template>

<script>
import http from '@/plugins/axios';
import {config} from '@/config';
import JsonPackage from '../../package.json';
export default {
  name: 'LoginView',
  data: () => ({
    appNome: config.appNome,
    inputsAutofilled: false,
    disableLoginForm: false,
    loadingLogin: false,
    iptLogin: '',
    iptLoginRules: [
      v => (!!v && !!v.trim()) || 'Insira o nome de usuário',
      v => (!/\s/g.test(v)) || 'Espaços não são permitidos',
      v => (!!v && v.trim().length >= 3) || 'O nome está pequeno demais'
    ],
    iptSenha: '',
    iptSenhaRules: [
      v => !!v || 'Insira a senha',
      v => (!/\s/g.test(v)) || 'A senha não pode conter espaços',
      v => (v && v.length >= 4) || 'Precisa ter pelo menos 4 caracteres',
    ],
    iptSenhaMostrar: false,
  }),
  methods: {
    async submitLogin() {
      if (!this.$refs['form-login'].validate()) return;
      this.loadingLogin = true;
      try {
        const dados = {
          login: this.iptLogin,
          senha: this.iptSenha,
          versao: JsonPackage.version,
        };
        const {data} = await http().post('login', dados);
        if (data.token) this.$store.commit('setToken', data.token);
        await this.$router.push('/');
      } finally {
        this.loadingLogin = false;
      }
    },
  },
  watch: {
    iptLogin() {
      this.inputsAutofilled = true;
    },
  },
}
</script>
