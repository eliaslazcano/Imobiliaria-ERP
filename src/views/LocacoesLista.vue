<template>
  <async-container :loading="loading">
    <v-card>
      <v-card-title>
        Locações
        <v-spacer></v-spacer>
        <v-btn color="success" small to="/locacaowizard">
          <v-icon small class="mr-1">mdi-plus</v-icon> Adicionar
        </v-btn>
      </v-card-title>
      <v-card-text class="pb-0">
        <v-text-field
          label="Filtrar por nome"
          v-model="tableSearch"
          prepend-inner-icon="mdi-magnify"
          class="mb-2"
          hide-details
          outlined
          dense
        ></v-text-field>
      </v-card-text>
      <v-data-table
        :headers="tableHeaders"
        :items="tableItems"
        :search="tableSearch"
        no-data-text="Não há locações"
      >
        <template v-slot:[`item.situacao`]="{item}">
          <v-chip v-if="item.situacao === 1" color="success" small label>EM DIA</v-chip>
          <v-chip v-else-if="item.situacao === 2" color="error" small label>ATRASADO</v-chip>
        </template>
        <template v-slot:[`item.actions`]>
          <div class="d-flex flex-nowrap justify-center">
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn v-bind="attrs" v-on="on" icon color="error">
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Remover</span>
            </v-tooltip>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn v-bind="attrs" v-on="on" icon color="warning" to="/cobrancaslocatario">
                  <v-icon>mdi-currency-usd</v-icon>
                </v-btn>
              </template>
              <span>Cobranças</span>
            </v-tooltip>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn v-bind="attrs" v-on="on" icon color="primary">
                  <v-icon>mdi-arrow-right-thick</v-icon>
                </v-btn>
              </template>
              <span>Ver detalhes</span>
            </v-tooltip>
          </div>
        </template>
      </v-data-table>
    </v-card>
  </async-container>
</template>

<script>
import AsyncContainer from '@/components/AsyncContainer';
export default {
  name: 'LocacoesLista',
  components: {AsyncContainer},
  data: () => ({
    loading: true,
    tableHeaders: [
      {value: 'id', text: 'Identificador', width: '8rem'},
      {value: 'situacao', text: 'Situação', width: '8rem', align: 'center'},
      {value: 'locatario', text: 'Locatário'},
      {value: 'actions', text: 'Botões', width: '8rem', sortable: false, filterable: false, align: 'center'},
    ],
    tableItems: [
      {id: '1', locatario: 'ERILDA MARQUES VIANA', situacao: 1},
      {id: '2', locatario: 'ELIAS LAZCANO CASTRO', situacao: 1},
      {id: '3', locatario: 'CLAUDENICE MARQUES VIANA', situacao: 1},
      {id: '4', locatario: 'EDILSON CHARÃO', situacao: 2},
      {id: '5', locatario: 'MURILO HENRIQUE VIANA', situacao: 1},
      {id: '6', locatario: 'WAGNER FELIPE MIRANDA', situacao: 2},
      {id: '7', locatario: 'FABIENNE DO NASCIMENTO SANTANA DOS REIS', situacao: 1},
    ],
    tableSearch: '',
  }),
  created() {
    try {
      this.loading = false;
    } catch (e) {
      this.$router.push('/');
    }
  },
}
</script>
