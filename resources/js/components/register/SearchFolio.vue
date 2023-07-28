<template>
    <div>
        <b-row>
            <b-col>
                <b-form-group label="Banco:">
                    <b-form-select v-model="bank" :disabled="load"
                        :options="banks" required
                    ></b-form-select>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group label="Fecha">
                    <b-form-datepicker v-model="fecha" :disabled="load" locale="es"></b-form-datepicker>
                </b-form-group>
            </b-col>
            <b-col sm="2">
                <b-form-group label="Abono">
                    <b-form-input v-model="abono" :disabled="load" type="number"></b-form-input>
                </b-form-group>
            </b-col>
            <b-col sm="2">
                 <b-button class="mt-4" variant="success" pill @click="searchFolio()" :disabled="load">
                    <b-icon-search></b-icon-search> Buscar
                </b-button>
            </b-col>
        </b-row>
        <hr>
        <b-table v-if="folios.length > 0" :items="folios" :fields="fields" responsive
            selectable :select-mode="selectMode" @row-selected="onRowSelected" :busy="load">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(abono)="data">
                ${{ data.item.abono | numeral('0,0') }}
            </template>
            <template v-slot:cell(saldo)="data">
                ${{ data.item.saldo | numeral('0,0') }}
            </template>
            <template v-slot:cell(occupied)="data">
                <b-badge v-if="data.item.occupied" variant="success">
                    <b-icon-check2-circle></b-icon-check2-circle>
                </b-badge>
                <b-badge v-else variant="secondary">
                    <b-icon-x-circle-fill></b-icon-x-circle-fill>
                </b-badge>
            </template>
            <template v-slot:cell(created_at)="data">
                {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
            </template>
            <template #table-busy>
                <div class="text-center text-primary my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Cargando...</strong>
                </div>
            </template>
            <template #cell(selected)="{ rowSelected }">
                <template v-if="rowSelected">
                    <span aria-hidden="true">&check;</span>
                    <span class="sr-only">Selected</span>
                </template>
                <template v-else>
                    <span aria-hidden="true">&nbsp;</span>
                    <span class="sr-only">Not selected</span>
                </template>
            </template>
        </b-table>
        <b-alert v-else show variant="dark" class="text-center">
            <b-icon-info-circle></b-icon-info-circle> No hay registros para mostrar
        </b-alert>
        <b-button :disabled="load || selected.length === 0" block variant="success" 
            pill @click="onContinue()">
            <b-icon-arrow-right-circle></b-icon-arrow-right-circle> Continuar
        </b-button>
    </div>
</template>

<script>
export default {
    data(){
        return {
            fecha: null,
            abono: null,
            concepto: null,
            folios: [],
            load: false,
            fields: [
                { key: 'index', label: 'N.' },
                { key: 'fecha', label: 'Fecha' },
                { key: 'concepto', label: 'Concepto' },
                { key: 'abono', label: 'Abono' },
                { key: 'saldo', label: 'Saldo' },
                { key: 'occupied', label: 'Registrado' }
            ],
            banks: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'BANAMEX', text: 'BANAMEX'},
                { value: 'AZTECA', text: 'BANCO AZTECA'},
                { value: 'BANCOMER', text: 'BANCOMER'},
                { value: 'BANCOPPEL', text: 'BANCOPPEL'},
                { value: 'BAJIO', text: 'BANBAJIO'},
                { value: 'BANORTE', text: 'BANORTE'},
                { value: 'HSBC', text: 'HSBC'},
                { value: 'SANTANDER', text: 'SANTANDER'},
                { value: 'SCOTIABANK', text: 'SCOTIABANK'},
                { value: 'OTRO', text: 'OTRO'},
            ],
            bank: null,
            selectMode: 'multi',
            selected: []
        }
    },
    methods: {
        searchFolio() {
            this.load = true;
            axios.get('/folios/search_folios', {
                params: {fecha: this.fecha, abono: this.abono, bank: this.bank
            }}).then(response => {
                this.folios = response.data;
                this.load = false;
            }).catch(error => {
                this.load = false;
            });
        },
        onRowSelected(items) {
            this.selected = items
        },
        onContinue(){
            this.$emit('foliosSelected', this.selected);
        }
    }
}
</script>