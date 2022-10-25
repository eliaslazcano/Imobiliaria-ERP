<template>
  <v-text-field
    v-model="iptDate"
    v-mask="'##/##/####'"
    type="tel"
    placeholder="__/__/____"
    :label="label"
    :class="inputClasses"
    :rules="rulesComputed"
    :disabled="disabled"
    :outlined="outlined"
    :dense="dense"
    :hide-details="hideDetails"
    :prepend-inner-icon="prependInnerIcon ? prependInnerIcon : undefined"
  >
    <template v-slot:append>
      <v-menu
        v-if="inline"
        ref="menu"
        v-model="modal"
        :close-on-content-click="false"
        :return-value.sync="iptDatePicker"
        transition="slide-y-transition"
        offset-y
        left
        min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon small v-bind="attrs" v-on="on">
            <v-icon>mdi-calendar</v-icon>
          </v-btn>
        </template>
        <v-date-picker
          v-model="iptDatePicker"
          :max="maxHoje ? hoje : max"
          @input="save"
          scrollable
          no-title
        >
        </v-date-picker>
      </v-menu>
      <v-dialog v-else ref="dialog" v-model="modal" :return-value.sync="iptDatePicker" persistent width="290px">
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon small v-bind="attrs" v-on="on">
            <v-icon>mdi-calendar</v-icon>
          </v-btn>
        </template>
        <v-date-picker
          v-model="iptDatePicker"
          :max="maxHoje ? hoje : max"
          @input="noButtons ? save() : undefined"
          scrollable
        >
          <v-spacer></v-spacer>
          <div v-if="!noButtons">
            <v-btn text color="primary" @click="modal = false">Cancelar</v-btn>
            <v-btn text color="primary" @click="save">OK</v-btn>
          </div>
        </v-date-picker>
      </v-dialog>
    </template>
  </v-text-field>
</template>

<script>
import moment from '@/plugins/moment';
export default {
  name: 'DateField',
  props: {
    'forceInline': {default: false, type: Boolean},
    'noButtons': {default: false, type: Boolean},
    'outlined': {default: false, type: Boolean},
    'dense': {default: false, type: Boolean},
    'disabled': {default: false, type: Boolean},
    'hideDetails': {default: false, type: Boolean},
    'prependInnerIcon': {default: null, type: String},
    'label': {default: '', type: String},
    'value': {default: '', type: String},
    'inputClasses': {default: '', type: String},
    'rules': {default: () => [], type: Array},
    'max': {default: null, type: String},
    'maxHoje': {default: false, type: Boolean},
    'required': {default: false, type: Boolean},
  },
  data: () => ({
    iptDate: '', //Format: DD/MM/YYYY
    iptDatePicker: moment().format('YYYY-MM-DD'),
    hoje: moment().format('YYYY-MM-DD'),
    modal: false,
  }),
  computed: {
    isValid() {
      if (this.iptDate.length !== 10) return false;
      return moment(this.iptDate, 'DD-MM-YYYY').isValid();
    },
    rulesComputed() {
      const rules = [
        ...this.rules,
        v => !v || v.length === 10 || 'Data incompleta',
        v => !v || this.isValid || 'Data inválida'
      ];
      if (this.required) rules.push(v => !!v || 'Insira a data');
      return rules;
    },
    inline() {
      return this.forceInline || this.$vuetify.breakpoint.smAndUp;
    },
  },
  methods: {
    save() {
      if (this.inline) this.$refs.menu.save(this.iptDatePicker);
      else this.$refs.dialog.save(this.iptDatePicker);
      this.iptDate = moment(this.iptDatePicker, 'YYYY-MM-DD').format('DD/MM/YYYY');
      this.$emit('input', this.iptDatePicker);
      this.$emit('change', this.iptDatePicker);
    },
  },
  watch: {
    value(v) {
      if (v !== this.date) this.date = v;
    },
    iptDate(v) {
      if (v && v.length === 10 && moment(v, 'DD-MM-YYYY').isValid()) {
        const newDate = moment(v, 'DD-MM-YYYY').format('YYYY-MM-DD');
        if (newDate !== this.iptDatePicker) {
          this.iptDatePicker = newDate;
          this.$emit('input', newDate);
          this.$emit('change', newDate);
        }
      }
    },
  },
  created() {
    if (this.value) this.iptDate = this.value;
  },
}
</script>
