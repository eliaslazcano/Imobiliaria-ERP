<template>
  <v-container>
    <v-card>
      <v-card-title>Cobranças do locatário</v-card-title>
      <v-card-subtitle>Edmilson Marques Charão</v-card-subtitle>
      <v-data-table
        :headers="tableHeaders"
        :items="tableItems"
        no-data-text="Não há cobranças"
        sort-by="emissao"
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
          <small v-else class="grey--text text-center">PENDENTE</small>
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
  name: 'CobrancasLocatario',
  data: () => ({
    moment,
    tableHeaders: [
      {value: 'emissao', text: 'Dt Emissão', width: '8rem'},
      {value: 'situacao', text: 'Situação', width: '8rem', align: 'center'},
      {value: 'valor', text: 'Valor', width: '8rem'},
      {value: 'vencimento', text: 'Dt Vencimento', width: '7rem'},
      {value: 'pagamento', text: 'Dt Pagamento', width: '7rem'},
      {value: 'actions', text: 'Botões', width: '8rem', sortable: false, filterable: false, align: 'center'},
    ],
    tableItems: [
      {id: '1', emissao: '2022-12-03', situacao: 1, vencimento: '2023-01-03', valor: 780.75},
      {id: '1', emissao: '2022-11-03', situacao: 2, vencimento: '2022-12-03', valor: 780.75},
      {id: '1', emissao: '2022-10-03', situacao: 2, vencimento: '2022-11-03', valor: 780.75},
      {id: '1', emissao: '2022-09-03', situacao: 3, vencimento: '2022-10-03', valor: 780.75},
      {id: '2', emissao: '2022-08-03', situacao: 3, vencimento: '2022-09-03', valor: 780.75},
      {id: '3', emissao: '2022-07-03', situacao: 3, vencimento: '2022-08-03', valor: 780.75},
      {id: '4', emissao: '2022-06-03', situacao: 3, vencimento: '2022-07-03', valor: 780.75},
      {id: '5', emissao: '2022-05-03', situacao: 3, vencimento: '2022-06-03', valor: 780.75},
      {id: '6', emissao: '2022-04-03', situacao: 3, vencimento: '2022-05-03', valor: 780.75},
      {id: '7', emissao: '2022-03-03', situacao: 3, vencimento: '2022-04-03', valor: 780.75},
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
}
</script>

<style scoped>

</style>