<template>
    <div>
        <b-row class="mb-2">
            <b-col>
                <pagination size="default" :limit="1" :data="movimientosData" 
                    @pagination-change-page="get_results">
                    <span slot="prev-nav">
                        <b-icon-chevron-left></b-icon-chevron-left>
                    </span>
                    <span slot="next-nav">
                        <b-icon-chevron-right></b-icon-chevron-right>
                    </span>
                </pagination>
            </b-col>
            <b-col class="text-right" sm="4">
                <b-form-select v-model="month" :disabled="load" :options="months"
                ></b-form-select>
                <b-form-select v-model="state" :disabled="load" :options="status"
                ></b-form-select>
            </b-col>
            <b-col sm="2">
                <b-button id="btnPre" @click="search_month()" block
                    class="mt-3" pill :disabled="month == null || state == null || load">
                    <b-icon-search></b-icon-search> Buscar
                </b-button>
            </b-col>
        </b-row>
        <div v-if="count > 0">
            <b-row class="mb-2">
                <b-col>
                    <b>Cantidad:</b> {{ count }}
                </b-col>
                <b-col sm="2">
                    <b-button variant="dark" pill :disabled="load || month == null"
                        :href="`/movimientos/down_by_month/${month}/${state}`" block>
                        <b-icon-download></b-icon-download> Descargar
                    </b-button>
                </b-col>
            </b-row>
            <b-table :items="movimientosData.data" :busy="load" responsive :fields="fields">
                <template v-slot:cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template v-slot:cell(name)="data">
                    {{ data.item.name }}
                </template>
                <template v-slot:cell(price)="data">
                    ${{ data.item.price | numeral('0,0') }}
                </template>
                <template v-slot:cell(total)="data">
                    ${{ data.item.total | numeral('0,0') }}
                </template>
                <template v-slot:cell(school)="data">
                    {{ data.item.school.name }}
                </template>
                <template v-slot:cell(created_at)="data">
                    {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
                </template>
                <template v-slot:cell(show_details)="row">
                    <b-button size="sm" @click="row.toggleDetails" id="btnPre"
                        pill class="mr-2">
                        {{ row.detailsShowing ? 'Ocultar' : 'Mostrar'}}
                    </b-button>
                </template>
                <template #row-details="data">
                    <div v-for="(registro, i) in data.item.registros" v-bind:key="i">
                        <b-row>
                            <b-col>
                                <h6><strong>Deposito</strong></h6>
                                <ul>
                                    <li><b>Fecha de pago</b>: {{ registro.date }}</li>
                                    <li><b>Tipo de pago</b>: {{ registro.type }}</li>
                                    <li><b>Banco</b>: {{ registro.bank }}</li>
                                    <li><b>Referencia</b>: {{ registro.invoice }}</li>
                                    <li><b>Concepto</b>: {{ registro.auto }}</li>
                                    <li><b>Importe</b>: ${{ registro.total }}</li>
                                </ul>
                            </b-col>
                            <b-col v-if="registro.folio">
                                <h6><strong>Referencia Edo. Cta.</strong></h6>
                                <ul>
                                    <li><b>Fecha</b>: {{ registro.folio.fecha }}</li>
                                    <li><b>Concepto</b>: {{ registro.folio.concepto }}</li>
                                    <li><b>Abono</b>: ${{ registro.folio.abono }}</li>
                                </ul>
                            </b-col>
                        </b-row>
                    </div>
                </template>
                <template v-slot:table-busy>
                    <div class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Cargando...</strong>
                    </div>
                </template>
            </b-table>
        </div>
        <b-alert v-else show variant="dark" class="text-center">
            <b-icon-info-circle></b-icon-info-circle> No hay registros para mostrar.
        </b-alert>
    </div>
</template>

<script>
export default {
    data(){
        return {
            month: null,
            numbers: [
                { value: '01', text: 'Enero'},{ value: '02', text: 'Febrero'},
                { value: '03', text: 'Marzo'},{ value: '04', text: 'Abril'},
                { value: '05', text: 'Mayo'},{ value: '06', text: 'Junio'},
                { value: '07', text: 'Julio'},{ value: '08', text: 'Agosto'},
                { value: '09', text: 'Septiembre'},{ value: '10', text: 'Octubre'},
                { value: '11', text: 'Noviembre'},{ value: '12', text: 'Diciembre'}
            ],
            status: [
                { value: null, text: 'Selecciona el status', disabled: true},
                { value: 'accepted', text: 'Aceptados'},
                { value: 'rejected', text: 'Rechazados'},
                { value: 'process', text: 'En proceso'},
            ],
            state: null,
            months: [],
            load: false,
            movimientosData: {},
            count: 0,
            fields: [
                { label: 'N.', key: 'index' },
                { label: 'Fecha de registro', key: 'created_at' },
                { label: 'Escuela', key: 'school' },
                { label: 'Alumno', key: 'name' },
                { label: 'Libro', key: 'book' },
                { label: 'Pago(s)', key: 'show_details' }
            ],
            
        }
    },
    created: function() {
        const now = new Date();
        this.months.push({ value: null, text: 'Selecciona el mes', disabled: true });
        this.numbers.forEach(number => {
            if(parseInt(number.value) <= (now.getMonth()+1)){
                this.months.push(number);
            }
        });
    },
    methods: {
        // BUSQUEDA POR PAGINA
        get_results(page){
            this.search_month(page);
        },
        // BUSQUEDA POR MES Y STATUS
        search_month(page = 1){
            this.load = true;
            axios.get(`/movimientos/by_month?page=${page}`, {params: {month: this.month, state: this.state}}).then(response => {
                this.movimientosData = response.data.movimientos;
                this.count = response.data.count_movimientos;
                this.load = false;
            }).catch(error => {
                this.load = false;
            });
        }
    }

}
</script>

<style>

</style>