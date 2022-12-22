<template>
  <v-container>
    <v-card>
      <v-card-title>Cobranças</v-card-title>
      <v-card-text class="pb-0">
        <v-select
          label="Filtrar por situação"
          v-model="iptFiltroSituacao"
          :items="iptFiltroSituacaoItems"
          prepend-inner-icon="mdi-filter"
          class="mb-3"
          hide-details
          outlined
          dense
        ></v-select>
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
        :items="tableItemsComputed"
        :search="tableSearch"
        no-data-text="Não há cobranças"
        sort-by="situacao"
        sort-desc
      >
        <template v-slot:[`item.emissao`]="{item}">{{ moment(item.emissao).format('DD/MM/YYYY') }}</template>
        <template v-slot:[`item.situacao`]="{item}">
          <v-chip v-if="item.situacao === 1" color="warning" small label>PENDENTE</v-chip>
          <v-chip v-else-if="item.situacao === 2" color="error" small label>VENCIDO</v-chip>
          <v-chip v-else-if="item.situacao === 3" color="success" small label>PAGO</v-chip>
        </template>
        <template v-slot:[`item.valor`]="{item}">
          <span :class="{'error--text': item.situacao === 2, 'success--text': item.situacao === 3}">R$ {{ item.valor.toFixed(2) }}</span>
        </template>
        <template v-slot:[`item.vencimento`]="{item}">{{ moment(item.vencimento).format('DD/MM/YYYY') }}</template>
        <template v-slot:[`item.pagamento`]="{item}">
          <span v-if="item.situacao === 3">{{ moment(item.vencimento).format('DD/MM/YYYY') }}</span>
          <div v-else class="grey--text text-center">---</div>
        </template>
        <template v-slot:[`item.actions`]="{item}">
          <div class="d-flex flex-nowrap justify-center">
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-if="!item.pago"
                  v-bind="attrs"
                  v-on="on"
                  color="success"
                  icon
                >
                  <v-icon>mdi-checkbox-marked-circle-outline</v-icon>
                </v-btn>
              </template>
              <span>Marcar pago</span>
            </v-tooltip>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon color="teal" v-bind="attrs" v-on="on">
                  <v-icon>mdi-file-document</v-icon>
                </v-btn>
              </template>
              <span>Imprimir Boleto</span>
            </v-tooltip>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon color="primary" v-bind="attrs" v-on="on">
                  <v-icon>mdi-qrcode-scan</v-icon>
                </v-btn>
              </template>
              <span>Código Pix</span>
            </v-tooltip>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon color="info" v-bind="attrs" v-on="on">
                  <v-icon>mdi-email</v-icon>
                </v-btn>
              </template>
              <span>Enviar por e-mail</span>
            </v-tooltip>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon color="warning" v-bind="attrs" v-on="on">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span>Alterar</span>
            </v-tooltip>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon color="error" v-bind="attrs" v-on="on">
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Apagar</span>
            </v-tooltip>
          </div>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>

<script>
import moment from '@/plugins/moment';
export default {
  name: 'CobrancasLista',
  data: () => ({
    moment,
    tableHeaders: [
      {value: 'emissao', text: 'Dt Emissão', width: '8rem'},
      {value: 'situacao', text: 'Situação', width: '8rem', align: 'center'},
      {value: 'devedor', text: 'Devedor'},
      {value: 'valor', text: 'Valor'},
      {value: 'vencimento', text: 'Dt Vencimento'},
      {value: 'pagamento', text: 'Dt Pagamento', width: '7rem'},
      {value: 'actions', text: 'Botões', width: '8rem', sortable: false, filterable: false, align: 'center'},
    ],
    tableItems: [
      {id: '1', emissao: '2022-09-03', situacao: 3, vencimento: '2022-09-03', devedor: 'ERILDA MARQUES VIANA', valor: 780.75},
      {id: '2', emissao: '2022-11-15', situacao: 3, vencimento: '2022-12-15', devedor: 'ELIAS LAZCANO CASTRO', valor: 700.40},
      {id: '3', emissao: '2022-10-17', situacao: 1, vencimento: '2022-10-04', devedor: 'CLAUDENICE MARQUES VIANA', valor: 1200},
      {id: '4', emissao: '2022-07-07', situacao: 2, vencimento: '2022-07-03', devedor: 'EDILSON CHARÃO', valor: 1350},
      {id: '5', emissao: '2022-09-09', situacao: 1, vencimento: '2022-10-09', devedor: 'MURILO HENRIQUE VIANA', valor: 2400},
      {id: '6', emissao: '2022-11-20', situacao: 2, vencimento: '2022-12-20', devedor: 'WAGNER FELIPE MIRANDA', valor: 9000},
      {id: '7', emissao: '2022-11-19', situacao: 1, vencimento: '2022-12-19', devedor: 'FABIENNE DO NASCIMENTO SANTANA DOS REIS', valor: 1970},
      {id: '8', emissao: '2022-09-03', situacao: 3, vencimento: '2022-09-03', devedor: 'ERILDA MARQUES VIANA', valor: 780.75},
      {id: '9', emissao: '2022-11-15', situacao: 3, vencimento: '2022-12-15', devedor: 'ELIAS LAZCANO CASTRO', valor: 700.40},
      {id: '10', emissao: '2022-10-17', situacao: 1, vencimento: '2022-10-04', devedor: 'CLAUDENICE MARQUES VIANA', valor: 1200},
      {id: '11', emissao: '2022-07-07', situacao: 2, vencimento: '2022-07-03', devedor: 'EDILSON CHARÃO', valor: 1350},
      {id: '12', emissao: '2022-09-09', situacao: 1, vencimento: '2022-10-09', devedor: 'MURILO HENRIQUE VIANA', valor: 2400},
      {id: '13', emissao: '2022-11-20', situacao: 2, vencimento: '2022-12-20', devedor: 'WAGNER FELIPE MIRANDA', valor: 9000},
      {id: '14', emissao: '2022-11-19', situacao: 1, vencimento: '2022-12-19', devedor: 'FABIENNE DO NASCIMENTO SANTANA DOS REIS', valor: 1970},
      {id: '15', emissao: '2022-09-03', situacao: 3, vencimento: '2022-09-03', devedor: 'ERILDA MARQUES VIANA', valor: 780.75},
      {id: '16', emissao: '2022-11-15', situacao: 3, vencimento: '2022-12-15', devedor: 'ELIAS LAZCANO CASTRO', valor: 700.40},
      {id: '17', emissao: '2022-10-17', situacao: 1, vencimento: '2022-10-04', devedor: 'CLAUDENICE MARQUES VIANA', valor: 1200},
      {id: '18', emissao: '2022-07-07', situacao: 2, vencimento: '2022-07-03', devedor: 'EDILSON CHARÃO', valor: 1350},
      {id: '19', emissao: '2022-09-09', situacao: 1, vencimento: '2022-10-09', devedor: 'MURILO HENRIQUE VIANA', valor: 2400},
      {id: '20', emissao: '2022-11-20', situacao: 2, vencimento: '2022-12-20', devedor: 'WAGNER FELIPE MIRANDA', valor: 9000},
      {id: '21', emissao: '2022-11-19', situacao: 1, vencimento: '2022-12-19', devedor: 'FABIENNE DO NASCIMENTO SANTANA DOS REIS', valor: 1970},
    ],
    tableSearch: '',
    iptFiltroSituacao: null,
    iptFiltroSituacaoItems: [
      {value: null, text: 'QUALQUER SITUAÇÃO (TODOS)'},
      {value: 3, text: 'SOMENTE PAGOS'},
      {value: 1, text: 'SOMENTE PENDENTES A VENCER'},
      {value: 2, text: 'SOMENTE PENDENTES VENCIDOS'},
    ],
  }),
  computed: {
    tableItemsComputed() {
      if (this.iptFiltroSituacao) return this.tableItems.filter(i => i.situacao === this.iptFiltroSituacao);
      return this.tableItems;
    },
  },
}
</script>
