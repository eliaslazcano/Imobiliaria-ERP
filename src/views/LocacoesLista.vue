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
      <v-data-table
        :headers="tableHeaders"
        :items="tableItems"
        no-data-text="Não há locações"
      ></v-data-table>
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
    tableHeaders: [],
    tableItems: [],
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
