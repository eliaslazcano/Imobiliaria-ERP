<template>
  <async-container :loading="loading">
    <!-- width="40rem" max-width="100%" -->
    <v-stepper v-model="step" class="mx-auto">
      <v-stepper-header>
        <v-stepper-step :complete="step > 1" step="1">Imóvel</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :complete="step > 2" step="2">Proprietário</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :complete="step > 3" step="3">Locatário</v-stepper-step>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content step="1" class="pa-0">
          <div class="pa-3">
            <p class="title text-center">CADASTRO DO IMÓVEL</p>
            <v-row>
              <v-col cols="12" lg="6">
                <v-card outlined>
                  <v-card-title>Endereço</v-card-title>
                  <v-card-text>
                    <v-text-field
                      label="CEP"
                      v-model="iptImovelCep"
                      type="tel"
                      v-mask="'#####-###'"
                    ></v-text-field>
                    <v-row dense>
                      <v-col cols="12" sm="2">
                        <v-text-field
                          label="UF"
                          v-model="iptImovelUf"
                          v-mask="'AA'"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="10">
                        <v-text-field
                          label="Cidade"
                          v-model="iptImovelCidade"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-text-field
                      label="Bairro"
                      v-model="iptImovelBairro"
                    ></v-text-field>
                    <v-text-field
                      label="Logradouro"
                      v-model="iptImovelLogradouro"
                    ></v-text-field>
                    <v-row dense>
                      <v-col cols="12" sm="6">
                        <v-text-field
                          label="Numero"
                          v-model="iptImovelNumero"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6">
                        <v-text-field
                          label="Complemento"
                          v-model="iptImovelComplemento"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined>
                  <v-card-title>Encargos</v-card-title>
                  <v-card-text>
                    <v-row>
                      <v-col cols="12" sm="6">
                        <v-text-field
                          label="Água"
                          v-model="iptImovelAgua"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6">
                        <v-text-field
                          label="Luz"
                          v-model="iptImovelLuz"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-text-field
                      label="Condomínio"
                      v-model="iptImovelCondominio"
                    ></v-text-field>
                    <v-text-field
                      label="IPTU"
                      v-model="iptImovelIptu"
                    ></v-text-field>
                    <v-text-field
                      label="Seguro do Imóvel"
                      v-model="iptImovelSeguro"
                    ></v-text-field>
                    <v-text-field
                      label="Valor do aluguel"
                      v-model="iptImovelAluguel"
                    ></v-text-field>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-card-actions class="pa-0 mt-5">
              <v-btn small color="secondary" @click="$router.back()">
                <v-icon small class="mr-1">mdi-arrow-left</v-icon> Cancelar
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn small color="primary" @click="concluirStepImovel">
                <v-icon small class="mr-1">mdi-arrow-right</v-icon> Prosseguir
              </v-btn>
            </v-card-actions>
          </div>
        </v-stepper-content>
        <v-stepper-content step="2" class="pa-0">
          <div class="pa-3">
            <p class="title text-center">CADASTRO DE PROPRIETÁRIO</p>
            <v-row>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-toolbar color="info" dark dense flat>
                    <v-toolbar-title>Dados pessoais</v-toolbar-title>
                  </v-toolbar>
                  <v-card-text>
                    <v-form ref="form-proprietario" @submit.prevent>
                      <v-text-field
                        label="CPF"
                        v-model="iptProprietarioCpf"
                        v-mask="'###.###.###-##'"
                        type="tel"
                        :rules="[v => (!!v && v.length === 14) || 'Informe o CPF completo']"
                      ></v-text-field>
                      <v-text-field
                        label="Nome"
                        v-model="iptProprietarioNome"
                        :rules="[v => (!!v && !!v.trim()) || 'Informe o nome completo']"
                      ></v-text-field>
                      <v-text-field
                        label="RG"
                        v-model="iptProprietarioRg"
                      ></v-text-field>
                      <date-field
                        label="Data de nascimento"
                        v-model="iptProprietarioDataNascimento"
                      ></date-field>
                      <v-autocomplete
                        label="Estado Civil"
                        v-model="iptProprietarioEstadoCivil"
                        :items="iptProprietarioEstadoCivilItems"
                        auto-select-first
                        item-value="id"
                        item-text="titulo"
                      ></v-autocomplete>
                      <v-text-field
                        label="Profissão"
                        v-model="iptProprietarioProfissao"
                      ></v-text-field>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-toolbar color="info" dark dense flat>
                    <v-toolbar-title>Dados para contato</v-toolbar-title>
                  </v-toolbar>
                  <v-card-text>
                    <v-card class="mb-5">
                      <v-toolbar color="grey darken-1" dark flat height="40">
                        <v-toolbar-title>E-mails</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="dialogAddProprietarioEmail = true">
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </v-toolbar>
                      <v-simple-table v-if="iptProprietarioEmails.length">
                        <template v-slot:default>
                          <thead>
                          <tr>
                            <th class="text-left">Endereço</th>
                            <th class="text-center">Comercial</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr v-for="(item, index) in iptProprietarioEmails" :key="'email_' + index">
                            <td>{{ item.endereco }}</td>
                            <td class="text-center">
                              <v-chip small :color="item.comercial ? 'success' : 'error'">
                                {{ item.comercial ? 'Sim' : 'Não' }}
                              </v-chip>
                            </td>
                          </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                      <v-card-text v-else class="text-center">Nenhum e-mail informado..</v-card-text>
                    </v-card>

                    <v-card>
                      <v-toolbar color="grey darken-1" dark flat height="40">
                        <v-toolbar-title>Telefones</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="dialogAddProprietarioTelefone = true">
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </v-toolbar>
                      <v-simple-table v-if="iptProprietarioTelefones.length">
                        <template v-slot:default>
                          <thead>
                          <tr>
                            <th class="text-left">Número</th>
                            <th class="text-left">Tipo</th>
                            <th class="text-center">Comercial</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr v-for="(item, index) in iptProprietarioTelefones" :key="'tel_' + index">
                            <td>{{ item.numero }}</td>
                            <td>
                              <v-icon v-if="item.tipo === 1">mdi-phone</v-icon>
                              <v-icon v-else-if="item.tipo === 2">mdi-cellphone</v-icon>
                            </td>
                            <td class="text-center">
                              <v-chip small :color="item.comercial ? 'success' : 'error'">
                                {{ item.comercial ? 'Sim' : 'Não' }}
                              </v-chip>
                            </td>
                          </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                      <v-card-text v-else class="text-center">Nenhum telefone informado..</v-card-text>
                    </v-card>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-toolbar color="info" dark dense flat>
                    <v-toolbar-title>Endereço Residencial</v-toolbar-title>
                  </v-toolbar>
                  <v-card-text>
                    <v-text-field
                      label="CEP"
                      v-model="iptProprietarioCep"
                      type="tel"
                      v-mask="'#####-###'"
                    ></v-text-field>
                    <v-row dense>
                      <v-col cols="12" sm="2">
                        <v-text-field
                          label="UF"
                          v-model="iptProprietarioUf"
                          v-mask="'AA'"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="10">
                        <v-text-field
                          label="Cidade"
                          v-model="iptProprietarioCidade"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-text-field
                      label="Bairro"
                      v-model="iptProprietarioBairro"
                    ></v-text-field>
                    <v-text-field
                      label="Logradouro"
                      v-model="iptProprietarioLogradouro"
                    ></v-text-field>
                    <v-row dense>
                      <v-col cols="12" sm="6">
                        <v-text-field
                          label="Numero"
                          v-model="iptProprietarioNumero"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6">
                        <v-text-field
                          label="Complemento"
                          v-model="iptProprietarioComplemento"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-toolbar color="info" dark dense flat>
                    <v-toolbar-title>Dados depósito</v-toolbar-title>
                  </v-toolbar>
                  <v-card-text>
                    <v-text-field
                      label="Banco"
                      v-model="iptDepositoBanco"
                    ></v-text-field>
                    <v-text-field
                      label="Agencia"
                      v-model="iptDepositoAgencia"
                    ></v-text-field>
                    <v-text-field
                      label="Conta"
                      v-model="iptDepositoConta"
                    ></v-text-field>
                    <v-select
                      label="Tipo de conta"
                      v-model="iptDepositoTipo"
                      :items="[{value: 1, text: 'Conta Corrente'}, {value: 2, text: 'Conta Poupança'}]"
                    ></v-select>
                    <v-checkbox
                      label="Pagamento de IPTU"
                      v-model="iptPagarIptu"
                      hint="Marque para IPTU administrado pela imobiliária"
                      persistent-hint
                    ></v-checkbox>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-card-actions class="pa-0 mt-5">
              <v-btn small color="primary" @click="step = step - 1">
                <v-icon small class="mr-1">mdi-arrow-left</v-icon> Retroceder
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn small color="primary" @click="concluirStepProprietario">
                <v-icon small class="mr-1">mdi-arrow-right</v-icon> Prosseguir
              </v-btn>
            </v-card-actions>
          </div>
        </v-stepper-content>
        <v-stepper-content step="3" class="pa-0">
          <div class="pa-3">
            <v-card-actions class="pa-0 mt-5">
              <v-btn small color="primary" @click="step = step - 1">
                <v-icon small class="mr-1">mdi-arrow-left</v-icon> Retroceder
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn small color="primary">
                <v-icon small class="mr-1">mdi-check</v-icon> Concluir
              </v-btn>
            </v-card-actions>
          </div>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>

    <v-dialog v-model="dialogAddProprietarioEmail" width="24rem">
      <v-card>
        <v-form ref="form-email-proprietario" @submit.prevent="addEmailProprietario">
          <v-card-title>Adicionando e-mail</v-card-title>
          <v-card-text>
            <v-text-field
              label="Endereço de e-mail"
              v-model="iptTmpEmail"
              :rules="iptTmpEmailRules"
              outlined
              dense
            ></v-text-field>
            <v-checkbox
              label="É um e-mail comercial"
              v-model="iptTmpEmailComercial"
              class="mt-0"
              hide-details
              dense
            ></v-checkbox>
          </v-card-text>
          <v-card-actions class="justify-center">
            <v-btn color="primary" outlined small @click="dialogAddProprietarioEmail = false">Cancela</v-btn>
            <v-btn color="primary" depressed small type="submit">Confirma</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogAddProprietarioTelefone" width="24rem">
      <v-card>
        <v-form ref="form-telefone-proprietario" @submit.prevent="addTelefoneProprietario">
          <v-card-title>Adicionando telefone</v-card-title>
          <v-card-text>
            <v-text-field
              label="Número"
              v-model="iptTmpTelefone"
              v-mask="['(##) ####-####','(##) #####-####']"
              type="tel"
              :rules="iptTmpTelefoneRules"
              outlined
              dense
            ></v-text-field>
            <v-select
              label="Tipo de telefone"
              v-model="iptTmpTelefoneTipo"
              :items="[{value: 1, text: 'Fixo'},{value: 2, text: 'Celular'}]"
              hide-details
              outlined
              dense
            ></v-select>
            <v-checkbox
              label="É um telefone comercial"
              v-model="iptTmpTelefoneComercial"
              hide-details
              dense
            ></v-checkbox>
          </v-card-text>
          <v-card-actions class="justify-center">
            <v-btn color="primary" outlined small @click="dialogAddProprietarioTelefone = false">Cancela</v-btn>
            <v-btn color="primary" depressed small type="submit">Confirma</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </async-container>
</template>

<script>
import DateField from '@/components/DateField';
import AsyncContainer from '@/components/AsyncContainer';
import AppWebClient from '@/http/AppWebClient';
export default {
  name: 'LocacaoWizard',
  components: {AsyncContainer, DateField},
  data: () => ({
    loading: true,
    step: 1,
    iptTmpEmail: '',
    iptTmpEmailRules: [
      v => !!v || 'Insira o e-mail',
      v => !v || (!/\s/g.test(v)) || 'Não pode usar espaços',
      v => !v || /.+@.+\..+/.test(v) || 'Insira um e-mail válido',
    ],
    iptTmpEmailComercial: false,
    iptTmpTelefone: '',
    iptTmpTelefoneRules: [
      v => !!v || 'Insira o telefone',
      v => (!!v && v.replace(/\D/g,'').length >= 10) || 'Complete o telefone',
    ],
    iptTmpTelefoneTipo: 1,
    iptTmpTelefoneComercial: false,

    //proprietario
    iptProprietarioId: null, //para pessoa fisica ja conhecida
    iptProprietarioCpf: '',
    iptProprietarioNome: '',
    iptProprietarioRg: '',
    iptProprietarioDataNascimento: '',
    iptProprietarioProfissao: '',
    iptProprietarioEstadoCivil: null,
    iptProprietarioEstadoCivilItems: [],

    iptProprietarioEmails: [],
    dialogAddProprietarioEmail: false,
    iptProprietarioTelefones: [],
    dialogAddProprietarioTelefone: false,

    iptProprietarioEnderecoId: null, //para endereco ja conhecido
    iptProprietarioCep: '',
    iptProprietarioUf: '',
    iptProprietarioCidade: '',
    iptProprietarioBairro: '',
    iptProprietarioLogradouro: '',
    iptProprietarioNumero: '',
    iptProprietarioComplemento: '',

    iptDepositoBanco: '',
    iptDepositoAgencia: '',
    iptDepositoConta: '',
    iptDepositoTipo: 1,
    iptPagarIptu: false,

    iptImovelEnderecoId: null, //para endereco ja conhecido
    iptImovelCep: '',
    iptImovelUf: '',
    iptImovelCidade: '',
    iptImovelBairro: '',
    iptImovelLogradouro: '',
    iptImovelNumero: '',
    iptImovelComplemento: '',
    iptImovelAgua: '',
    iptImovelLuz: '',
    iptImovelCondominio: '',
    iptImovelIptu: '',
    iptImovelSeguro: '',
    iptImovelAluguel: '',
  }),
  async created() {
    try {
      const appWebClient = new AppWebClient();
      this.iptProprietarioEstadoCivilItems = await appWebClient.getEstadosCivis();
    } catch (e) {
      await this.$router.push('/');
    } finally {
      this.loading = false;
    }
  },
  methods: {
    addEmailProprietario() {
      if (!this.$refs['form-email-proprietario'].validate()) return;
      this.iptProprietarioEmails.push({id: null, endereco: this.iptTmpEmail, comercial: this.iptTmpEmailComercial});
      this.$refs['form-email-proprietario'].reset();
      this.dialogAddProprietarioEmail = false;
    },
    addTelefoneProprietario() {
      if (!this.$refs['form-telefone-proprietario'].validate()) return;
      this.iptProprietarioTelefones.push({id: null, numero: this.iptTmpTelefone, tipo: this.iptTmpTelefoneTipo, comercial: this.iptTmpTelefoneComercial});
      this.$refs['form-telefone-proprietario'].reset();
      this.dialogAddProprietarioTelefone = false;
    },
    concluirStepProprietario() {
      if (!this.$refs['form-proprietario'].validate()) {
        this.$store.commit('showSnackbar', {color: 'error', text: 'Preencha os campos obrigatórios que estão em vermelho'});
        return;
      }
      this.step = 3;
    },
    concluirStepImovel() {
      this.step = 2;
    },
  },
  watch: {
    dialogAddProprietarioEmail(v) {
      if (!v) this.$refs['form-email-proprietario'].resetValidation();
    },
    dialogAddProprietarioTelefone(v) {
      if (!v) this.$refs['form-telefone-proprietario'].resetValidation();
    },
  }
}
</script>
