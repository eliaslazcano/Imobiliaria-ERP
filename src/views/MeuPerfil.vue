<template>
  <async-container :loading="loading">
    <v-card width="32rem" class="mx-auto" v-if="!!conta">
      <v-card-title class="justify-space-between">
        Sua conta de acesso
        <v-menu left bottom offset-y class="d-print-none">
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-on="on" v-bind="attrs">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list class="py-0" dense>
            <v-list-item @click="dialogTrocarSenha = true">
              <v-list-item-icon>
                <v-icon>mdi-key</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Trocar senha</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-card-title>
      <v-form ref="form-perfil" @submit.prevent="submitPerfil" :disabled="loadingPerfil">
        <v-card-text>
          <v-text-field
            label="Login"
            class="mt-2"
            outlined
            dense
            v-model="iptLogin"
            :rules="iptLoginRules"
          ></v-text-field>
          <v-text-field
            label="Nome"
            class="mt-2"
            outlined
            dense
            v-model="iptNome"
            :rules="iptNomeRules"
          ></v-text-field>
          <v-text-field
            label="E-mail"
            class="mt-2"
            outlined
            dense
            v-model="iptEmail"
            :rules="iptEmailRules"
          ></v-text-field>
          <p class="caption mb-0">Sua conta foi criada em {{ parseDate(conta.criado_em) }}</p>
        </v-card-text>
        <v-card-actions class="justify-center">
          <v-btn small color="primary" type="submit" :loading="loadingPerfil">Salvar alterações</v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
    <v-dialog v-model="dialogTrocarSenha" width="24rem">
      <v-card>
        <v-form ref="form-senha" @submit.prevent="submitTrocarSenha" :disabled="loadingAlteracaoSenha">
          <v-card-title>
            <v-icon class="mr-1">mdi-shield-key</v-icon> Alteração de senha
          </v-card-title>
          <v-card-text class="pb-0">
            <v-text-field
              label="Senha atual"
              v-model="iptSenhaAtual"
              :rules="iptSenhaAtualRules"
              outlined
              dense
              :type="iptSenhaAtualMostrar ? 'text' : 'password'"
              :append-icon="iptSenhaAtualMostrar ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append="iptSenhaAtualMostrar = !iptSenhaAtualMostrar"
              autocomplete="current-password"
            ></v-text-field>
            <v-text-field
              label="Nova senha"
              v-model="iptSenhaNova"
              :rules="iptSenhaNovaRules"
              outlined
              dense
              :type="iptSenhaNovaMostrar ? 'text' : 'password'"
              :append-icon="iptSenhaNovaMostrar ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append="iptSenhaNovaMostrar = !iptSenhaNovaMostrar"
              autocomplete="new-password"
            ></v-text-field>
            <v-text-field
              label="Repetir a nova senha"
              v-model="iptSenhaNovaRepeticao"
              :rules="iptSenhaNovaRepeticaoRules"
              outlined
              dense
              :type="iptSenhaNovaRepeticaoMostrar ? 'text' : 'password'"
              :append-icon="iptSenhaNovaRepeticaoMostrar ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append="iptSenhaNovaRepeticaoMostrar = !iptSenhaNovaRepeticaoMostrar"
              autocomplete="new-password"
            ></v-text-field>
          </v-card-text>
          <v-card-actions class="justify-center">
            <v-btn color="primary" outlined small :disabled="loadingAlteracaoSenha" @click="dialogTrocarSenha = false">
              Fechar
            </v-btn>
            <v-btn color="primary" depressed small :loading="loadingAlteracaoSenha" type="submit">Trocar Senha</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </async-container>
</template>

<script>
import AsyncContainer from '@/components/AsyncContainer';
import http from '@/plugins/axios';
import StringHelper from '@/helper/StringHelper';
export default {
  name: 'MeuPerfil',
  components: {AsyncContainer},
  data: () => ({
    loading: true,
    conta: null,
    iptLogin: '',
    iptLoginRules: [
      v => (!!v && !!v.trim()) || 'Informe o login',
      v => (v && v.length <= 32) || 'O login tem um limite de 32 caracteres',
      v => (!!v && (!/\s/g.test(v))) || 'Não pode usar espaços',
    ],
    iptNome: '',
    iptNomeRules: [
      v => (!!v && !!v.trim()) || 'Informe o nome',
      v => (v && v.length <= 64) || 'O nome tem um limite de 64 caracteres',
    ],
    iptEmail: '',
    iptEmailRules: [
      v => !v || /.+@.+\..+/.test(v) || 'Insira um e-mail válido',
      v => (!!v && (!/\s/g.test(v))) || 'Não pode usar espaços',
    ],
    loadingPerfil: false,

    dialogTrocarSenha: false,
    iptSenhaAtual: '',
    iptSenhaAtualRules: [
      v => !!v || 'Insira a senha atual',
    ],
    iptSenhaAtualMostrar: false,
    iptSenhaNova: '',
    iptSenhaNovaMostrar: false,
    iptSenhaNovaRepeticao: '',
    iptSenhaNovaRepeticaoMostrar: false,
    loadingAlteracaoSenha: false,
  }),
  computed: {
    iptSenhaNovaRules() {
      return [
        v => !!v || 'Insira a nova senha',
        v => (!/\s/g.test(v)) || 'A senha não pode conter espaços',
        v => (v && v.length >= 4) || 'Precisa ter pelo menos 4 caracteres',
        v => (v && v.length <= 16) || 'A senha tem um limite de 16 caracteres',
        () => this.iptSenhaAtual !== this.iptSenhaNova || 'A nova senha tem que ser diferente da sua atual'
      ];
    },
    iptSenhaNovaRepeticaoRules() {
      return [
        v => !!v || 'Repita a nova senha',
        () => this.iptSenhaNova === this.iptSenhaNovaRepeticao || 'As senhas não estão iguais'
      ];
    },
  },
  methods: {
    async submitPerfil() {
      if (!this.$refs['form-perfil'].validate()) return;
      this.loadingPerfil = true;
      try {
        await http().post('usuario/perfil', {login: this.iptLogin, nome: this.iptNome, email: this.iptEmail});
        this.$store.commit('showSnackbar', {color: 'success', text: 'Perfil atualizado!'});
      } finally {
        this.loadingPerfil = false;
      }
    },
    async submitTrocarSenha() {
      if (!this.$refs['form-senha'].validate()) return;
      this.loadingAlteracaoSenha = true;
      try {
        await http().post('usuario/trocar_senha', {senhaAtual: this.iptSenhaAtual, senhaNova: this.iptSenhaNova});
        this.$refs['form-senha'].reset();
        this.$store.commit('showSnackbar', {color: 'success', text: 'Senha alterada!'});
        this.dialogTrocarSenha = false;
      } finally {
        this.loadingAlteracaoSenha = false;
      }
    },
    parseDate(date) {
      return StringHelper.formatDate(date, '/')
    },
  },
  async created() {
    try {
      const {data} = await http().get('usuario/perfil');
      this.conta = data;
      this.iptLogin = data.login;
      this.iptNome = data.nome;
      this.iptEmail = data.email;
    } catch (e) {
      await this.$router.push('/');
    } finally {
      this.loading = false;
    }
  },
}
</script>
