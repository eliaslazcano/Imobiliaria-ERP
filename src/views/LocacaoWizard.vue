<template>
  <async-container :loading="loading">
    <!-- width="40rem" max-width="100%" -->
    <v-stepper v-model="step" class="mx-auto" width="56rem" max-width="100%">
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
                    <v-form @submit.prevent :disabled="iptImovelCepLoading">
                      <v-text-field
                        label="CEP"
                        v-model="iptImovelCep"
                        v-mask="'##.###-###'"
                        type="tel"
                        :loading="iptImovelCepLoading"
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
                    </v-form>
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
              <v-col cols="12">
                <v-card outlined height="100%">
                  <v-card-title>Dados pessoais</v-card-title>
                  <v-card-text>
                    <v-form ref="form-proprietario" @submit.prevent>
                      <v-row dense>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="CPF"
                            v-model="iptProprietarioCpf"
                            v-mask="'###.###.###-##'"
                            type="tel"
                            :rules="[v => (!!v && v.length === 14) || 'Informe o CPF completo']"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="Nome"
                            v-model="iptProprietarioNome"
                            :rules="[v => (!!v && !!v.trim()) || 'Informe o nome completo']"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="RG"
                            v-model="iptProprietarioRg"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <date-field
                            label="Data de nascimento"
                            v-model="iptProprietarioDataNascimento"
                          ></date-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-autocomplete
                            label="Estado Civil"
                            v-model="iptProprietarioEstadoCivil"
                            :items="iptProprietarioEstadoCivilItems"
                            auto-select-first
                            item-value="id"
                            item-text="titulo"
                          ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="Profissão"
                            v-model="iptProprietarioProfissao"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-card-title>
                    E-mails
                    <v-spacer></v-spacer>
                    <v-btn icon @click="dialogAddProprietarioEmail = true">
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                  </v-card-title>
                  <v-divider></v-divider>
                  <v-simple-table v-if="iptProprietarioEmails.length">
                    <template v-slot:default>
                      <thead>
                      <tr>
                        <th class="text-left">Endereço</th>
                        <th class="text-center">Comercial</th>
                        <th class="text-right"></th>
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
                        <td class="text-right">
                          <v-icon color="error" @click="iptProprietarioEmails.splice(index,1)">mdi-close</v-icon>
                        </td>
                      </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                  <v-card-text v-else class="text-center">Nenhum e-mail informado..</v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-card-title>
                    Telefones
                    <v-spacer></v-spacer>
                    <v-btn icon @click="dialogAddProprietarioTelefone = true">
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                  </v-card-title>
                  <v-divider></v-divider>
                  <v-simple-table v-if="iptProprietarioTelefones.length">
                    <template v-slot:default>
                      <thead>
                      <tr>
                        <th class="text-left">Número</th>
                        <th class="text-left">Tipo</th>
                        <th class="text-center">Comercial</th>
                        <th class="text-right"></th>
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
                        <td class="text-right">
                          <v-icon color="error" @click="iptProprietarioTelefones.splice(index,1)">mdi-close</v-icon>
                        </td>
                      </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                  <v-card-text v-else class="text-center">Nenhum telefone informado..</v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-card-title>Endereço Residencial</v-card-title>
                  <v-card-text>
                    <v-form @submit.prevent :disabled="iptProprietarioCepLoading">
                      <v-text-field
                        label="CEP"
                        v-model="iptProprietarioCep"
                        v-mask="'##.###-###'"
                        type="tel"
                        :loading="iptProprietarioCepLoading"
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
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" lg="6">
                <v-card outlined height="100%">
                  <v-card-title>Dados depósito</v-card-title>
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
            <p class="title text-center">CADASTRO DE LOCATÁRIO</p>
            <v-scroll-x-transition mode="out-in">
              <v-row :key="1" v-if="!iptLocatarioTipo">
                <v-col cols="12" md="6">
                  <v-card>
                    <v-list two-line color="green lighten-5" class="py-0">
                      <v-list-item @click="iptLocatarioTipo = 1">
                        <v-list-item-avatar class="justify-center">
                          <v-avatar color="success">
                            <v-icon color="white">mdi-account</v-icon>
                          </v-avatar>
                        </v-list-item-avatar>
                        <v-list-item-content>
                          <v-list-item-title>Pessoa Física</v-list-item-title>
                          <v-list-item-subtitle>O locatário é pessoa física</v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                </v-col>
                <v-col cols="12" md="6">
                  <v-card>
                    <v-list two-line color="blue lighten-5" class="py-0">
                      <v-list-item @click="iptLocatarioTipo = 2">
                        <v-list-item-avatar class="justify-center">
                          <v-avatar color="primary">
                            <v-icon color="white">mdi-office-building</v-icon>
                          </v-avatar>
                        </v-list-item-avatar>
                        <v-list-item-content>
                          <v-list-item-title>Pessoa Jurídica</v-list-item-title>
                          <v-list-item-subtitle>O locatário é pessoa jurídica</v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                </v-col>
              </v-row>
              <v-row :key="2" v-else-if="iptLocatarioTipo === 1">
                <v-col cols="12">
                  <v-card outlined>
                    <v-card-title>Dados pessoais</v-card-title>
                    <v-card-text>
                      <v-row dense>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="CPF"
                            v-model="iptLocatarioCpf"
                            v-mask="'###.###.###-##'"
                            type="tel"
                            :rules="[v => (!!v && v.length === 14) || 'Informe o CPF completo']"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="Nome"
                            v-model="iptLocatarioNome"
                            :rules="[v => (!!v && !!v.trim()) || 'Informe o nome completo']"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="RG"
                            v-model="iptLocatarioRg"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <date-field
                            label="Data de nascimento"
                            v-model="iptLocatarioDataNascimento"
                          ></date-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-autocomplete
                            label="Estado Civil"
                            v-model="iptLocatarioEstadoCivil"
                            :items="iptProprietarioEstadoCivilItems"
                            auto-select-first
                            item-value="id"
                            item-text="titulo"
                          ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="Profissão"
                            v-model="iptLocatarioProfissao"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                          <v-text-field
                            label="Renda mensal"
                            v-model="iptLocatarioRenda"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="12">
                  <v-card outlined>
                    <v-card-title>Trabalho atual</v-card-title>
                    <v-card-text>
                      <v-row dense>
                        <v-col cols="12" md="6">
                          <v-text-field
                            label="Nome da empresa"
                            v-model="iptProfissaoEmpresa"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <date-field
                            label="Data de admissão"
                            v-model="iptProfissaoAdmissao"
                          ></date-field>
                        </v-col>
                      </v-row>
                      <v-divider class="mt-3 mb-5"></v-divider>
                      <p class="subtitle-2 mb-0">Endereço da empresa:</p>
                      <v-form @submit.prevent :disabled="iptProfissaoCepLoading">
                        <v-text-field
                          label="CEP"
                          v-model="iptProfissaoCep"
                          v-mask="'##.###-###'"
                          type="tel"
                          :loading="iptProfissaoCepLoading"
                        ></v-text-field>
                        <v-row dense>
                          <v-col cols="12" sm="2">
                            <v-text-field
                              label="UF"
                              v-model="iptProfissaoUf"
                              v-mask="'AA'"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="10">
                            <v-text-field
                              label="Cidade"
                              v-model="iptProfissaoCidade"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                              label="Bairro"
                              v-model="iptProfissaoBairro"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                              label="Logradouro"
                              v-model="iptProfissaoLogradouro"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6">
                            <v-text-field
                              label="Numero"
                              v-model="iptProfissaoNumero"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6">
                            <v-text-field
                              label="Complemento"
                              v-model="iptProfissaoComplemento"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-form>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-scroll-x-transition>
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
              :autofocus="$vuetify.breakpoint.mdAndUp"
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
              :autofocus="$vuetify.breakpoint.mdAndUp"
              outlined
              dense
            ></v-text-field>
            <v-select
              label="Tipo de telefone"
              v-model="iptTmpTelefoneTipo"
              :items="[{value: 1, text: 'Fixo'},{value: 2, text: 'Celular'}]"
              :rules="[v => !!v || 'Selecione o tipo de telefone']"
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
import StringHelper from '@/helper/StringHelper';
import CepWebClient from '@/http/CepWebClient';
export default {
  name: 'LocacaoWizard',
  components: {AsyncContainer, DateField},
  data: () => ({
    loading: true,
    step: 3,
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
    iptProprietarioCepLoading: false,
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

    //Imovel
    iptImovelEnderecoId: null, //para endereco ja conhecido
    iptImovelCep: '',
    iptImovelCepLoading: false,
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

    //Locatario
    iptLocatarioTipo: null, //1:PF, 2:PJ
    iptLocatarioCpf: '',
    iptLocatarioNome: '',
    iptLocatarioRg: '',
    iptLocatarioDataNascimento: '',
    iptLocatarioEstadoCivil: null,
    iptLocatarioProfissao: '',
    iptLocatarioRenda: '',

    iptProfissaoEmpresa: '',
    iptProfissaoAdmissao: '',
    iptProfissaoCep: '',
    iptProfissaoCepLoading: false,
    iptProfissaoUf: '',
    iptProfissaoCidade: '',
    iptProfissaoBairro: '',
    iptProfissaoLogradouro: '',
    iptProfissaoNumero: '',
    iptProfissaoComplemento: '',

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
      this.iptTmpTelefone = '';
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
    iptImovelCep(v) {
      if (!v || this.iptImovelCepLoading) return;
      const cep = StringHelper.extractNumbers(v);
      if (cep.length !== 8) return;
      this.iptImovelCepLoading = true;
      CepWebClient.smart(cep)
        .then(endereco => {
          if (endereco) {
            this.iptImovelUf = endereco.estado ? endereco.estado.toUpperCase() : '';
            this.iptImovelCidade = endereco.cidade ? endereco.cidade.toUpperCase() : '';
            this.iptImovelBairro = endereco.bairro ? endereco.bairro.toUpperCase() : '';
            this.iptImovelLogradouro = endereco.logradouro ? endereco.logradouro.toUpperCase() : '';
          }
        }).finally(() => this.iptImovelCepLoading = false);
    },
    iptProprietarioCep(v) {
      if (!v || this.iptProprietarioCepLoading) return;
      const cep = StringHelper.extractNumbers(v);
      if (cep.length !== 8) return;
      this.iptProprietarioCepLoading = true;
      CepWebClient.smart(cep)
        .then(endereco => {
          if (endereco) {
            this.iptProprietarioUf = endereco.estado ? endereco.estado.toUpperCase() : '';
            this.iptProprietarioCidade = endereco.cidade ? endereco.cidade.toUpperCase() : '';
            this.iptProprietarioBairro = endereco.bairro ? endereco.bairro.toUpperCase() : '';
            this.iptProprietarioLogradouro = endereco.logradouro ? endereco.logradouro.toUpperCase() : '';
          }
        }).finally(() => this.iptProprietarioCepLoading = false);
    },
    iptTmpTelefone(v) {
      if (!v) return;
      const nr = StringHelper.extractNumbers(v);
      if (nr.length >= 11) this.iptTmpTelefoneTipo = 2;
    },
  }
}
</script>
