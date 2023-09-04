<template>
    <div>
        <b-row class="mb-2">
            <b-col>
                <pagination size="default" :limit="1" :data="foliosData" 
                    @pagination-change-page="get_results">
                    <span slot="prev-nav">
                        <b-icon-chevron-left></b-icon-chevron-left>
                    </span>
                    <span slot="next-nav">
                        <b-icon-chevron-right></b-icon-chevron-right>
                    </span>
                </pagination>
            </b-col>
            <b-col sm="2" class="text-right">
                <b-button pill id="btnPre" @click="modalSearch = true">
                    <b-icon-search></b-icon-search> Busquedas
                </b-button>
            </b-col>
            <b-col sm="3" class="text-right">
                <b-button id="btnPre" pill 
                    @click="modalShow = !modalShow">
                    <b-icon-plus-circle></b-icon-plus-circle> Subir depósitos
                </b-button>
            </b-col>
        </b-row>
        <b-table :items="foliosData.data" :fields="fields"
            :busy="load">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(abono)="data">
                ${{ data.item.abono | numeral('0,0[.]00') }}
            </template>
            <template v-slot:cell(saldo)="data">
                ${{ data.item.saldo | numeral('0,0[.]00') }}
            </template>
            <template v-slot:cell(occupied)="data">
                <b-badge v-if="data.item.occupied" variant="success">
                    <b-icon-check2-circle></b-icon-check2-circle>
                </b-badge>
                <b-badge v-else variant="secondary">
                    <b-icon-x-circle-fill></b-icon-x-circle-fill>
                </b-badge>
            </template>
            <template v-slot:cell(marcado_por)="data">
                <div v-if="data.item.occupied">
                    <label>{{ data.item.marcado_por == null ? 'Sistema':data.item.marcado_por }}</label>
                </div>
                <b-button v-else pill @click="marcarFolio(data.item, data.index)"
                    id="btnPre" size="sm" :disabled="load">
                    <b-icon-check></b-icon-check> Marcar
                </b-button>
            </template>
            <template v-slot:cell(created_at)="data">
                {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
            </template>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Cargando...</strong>
                </div>
            </template>
        </b-table>
        <b-modal v-model="modalShow" hide-footer title="Subir depósitos">
            <b-alert v-if="errorFormat" show variant="warning">
                <b-icon-info-circle></b-icon-info-circle> Formato de archivo no permitido
            </b-alert>
            <form @submit="onSubmit" enctype="multipart/form-data">
                <input 
                    :disabled="load" type="file" required id="archivoType"
                    class="custom-file" v-on:change="fileChange">
                <label for="archivoType"><b-icon-upload></b-icon-upload> Seleccionar archivo</label> <br>
                <label v-if="file"><b>Archivo:</b> {{ file ? file.name : '' }}</label>
                <b-row>
                    <b-col>
                        <b-alert v-if="load" show variant="info">
                            No cierres este recuadro hasta que el archivo termine de cargar
                        </b-alert>
                    </b-col>
                    <b-col class="text-right" sm="4">
                        <b-button pill :disabled="load || file == null" class="mt-3" id="btnPre" type="submit">
                            <i v-if="!load" class="fa fa-plus-circle"></i>
                            <b-spinner v-else type="grow" small></b-spinner>
                            {{ !load ? 'Guardar':'Cargando' }}
                        </b-button>
                    </b-col>
                </b-row>
            </form>
        </b-modal>
        <b-modal v-model="modalSearch" hide-footer title="Buscar por:" size="lg">
            <b-row class="mb-2">
                <b-col>Fecha</b-col>
                <b-col>
                    <b-form-datepicker v-model="fecha" locale="es">
                    </b-form-datepicker>
                </b-col>
                <b-col sm="2">
                    <b-button id="btnPre" pill @click="byDate()" 
                        size="sm" :disabled="fecha == null">
                        <i class="fa fa-search"></i>
                    </b-button>
                </b-col>
            </b-row>
            <b-row class="mb-2">
                <b-col>Abono</b-col>
                <b-col>
                    <b-input v-model="abono" type="number"></b-input>
                </b-col>
                <b-col sm="2">
                    <b-button id="btnPre" pill @click="byDateAbono()" 
                    :disabled="abono < 1" size="sm">
                        <i class="fa fa-search"></i>
                    </b-button>
                </b-col>
            </b-row>
            <b-row class="mb-2">
                <b-col>Concepto</b-col>
                <b-col>
                    <b-input v-model="referencia"></b-input>
                </b-col>
                <b-col sm="2">
                    <b-button id="btnPre" pill @click="byReferencia()" 
                    :disabled="fecha == null || abono < 1 || referencia == null" size="sm">
                        <i class="fa fa-search"></i>
                    </b-button>
                </b-col>
            </b-row>
            <!-- <hr>
            <b-row v-if="role == 'manager'" class="mb-2">
                <b-col>Banco</b-col>
                <b-col>
                    <b-form-select v-model="bank" :disabled="load"
                        :options="banks"
                    ></b-form-select>
                </b-col>
                <b-col sm="2">
                    <b-button id="btnPre" pill :disabled="bank == null" 
                        @click="byBank()" size="sm">
                        <i class="fa fa-search"></i>
                    </b-button>
                </b-col>
            </b-row> -->
        </b-modal>
        <b-modal v-model="modalMarcar" hide-footer hide-header size="sm" centered>
            <div class="text-center">
                <strong>¿Estás seguro(a) de marcar como ocupado este depósito?</strong><br>
                <b-button @click="saveOcupado()" variant="success" pill>Si</b-button>
            </div>
        </b-modal>
    </div>
</template>

<script>
import banksMixin from '../mixins/banksMixin';
export default {
    props: ['role', 'userid'],
    mixins: [banksMixin],
    data(){
        return {
            foliosData: {},
            fields: [
                {key: 'index', label: 'N.'}, 
                'fecha', 'concepto', 'abono',
                {key: 'occupied', label: 'Registrado'},
                {key: 'marcado_por', label: 'Marcado por'},
                {key: 'created_at', label: 'Se subio el:'}
            ],
            load:false,
            file: null,
            errorFormat: null,
            bank: null,
            fecha: null,
            abono: 0,
            referencia: null,
            modalShow: false,
            modalSearch: false,
            sDate: false,
            sDAbono: false,
            sReferencia: false,
            sBank: false,
            modalMarcar: false,
            folio: { id: null },
            position: null
        }
    },
    created: function(){
        this.get_results();
    },
    methods: {
        // OBTENER LOS FOLIOS
        get_results(page = 1){
            if(!this.sDate && !this.sDAbono && !this.sReferencia && !this.sBank) 
                this.http_bymonth(page);
            if(this.sDate) this.http_bydate(page); 
            if(this.sDAbono) this.http_bydateabono(page);
            if(this.sReferencia) this.http_byreferencia(page);
            if(this.sBank) this.http_bybank(page);
        },
        // HTTP TODOS LOS FOLIOS
        http_bymonth(page){
            this.load = true;
            axios.get(`/folios/by_month?page=${page}`).then(response => {
                this.foliosData = response.data;
                this.load = false;
            }).catch(error => {
                this.load = false;
            });
        },
        // POR FECHA
        byDate() {
            this.http_bydate();
        },
        // POR FECHA Y ABONO
        byDateAbono(){
            this.http_bydateabono();
        },
        // POR REFERENCIAS
        byReferencia(){
            this.http_byreferencia();
        },
        // POR BANCO
        byBank(){
            this.http_bybank();
        },
        // BUSCAR HTTP POR FECHA
        http_bydate(page = 1){
            this.load = true;
            axios.get(`/folios/by_date?page=${page}`, {params: {fecha: this.fecha}}).then(response => {
                this.foliosData = response.data;
                this.load = false;
                this.types_search(true, false, false, false);
            }).catch(error => {
                this.load = false;
            });
        },
        // BUSCAR HTTP POR FECHA Y ABONO
        http_bydateabono(page = 1){
            this.load = true;
            axios.get(`/folios/by_date_abono?page=${page}`, {params: {fecha: this.fecha, abono: this.abono}}).then(response => {
                this.foliosData = response.data;
                this.load = false;
                this.types_search(false, true, false, false);
            }).catch(error => {
                this.load = false;
            });
        },
        http_byreferencia(page = 1){
            this.load = true;
            axios.get(`/folios/by_referencia?page=${page}`, {params: {fecha: this.fecha, abono: this.abono, referencia: this.referencia}}).then(response => {
                this.foliosData = response.data;
                this.load = false;
                this.types_search(false, false, true, false);
            }).catch(error => {
                this.load = false;
            });
        },
        http_bybank(page = 1){
            this.load = true;
            axios.get(`/folios/by_bank?page=${page}`, {params: {bank: this.bank}}).then(response => {
                this.foliosData = response.data;
                this.load = false;
                this.types_search(false, false, false, true);
            }).catch(error => {
                this.load = false;
            });
        },
        // TIPOS DE BUSQUEDA
        types_search(sDate, sDAbono, sReferencia, sBank){
            this.sDate = sDate;
            this.sDAbono = sDAbono;
            this.sReferencia = sReferencia;
            this.sBank = sBank;
            this.modalSearch = false;
        },
        fileChange(e){
            this.file = e.target.files[0];
        },
        onSubmit(e){
            e.preventDefault();
            var fileInput = document.getElementById('archivoType');
            var allowedExtensions = /(\.xlsx)$/i;
            this.load = true;
            if(allowedExtensions.exec(fileInput.value)){
                this.errorFormat = false;
                let formData = new FormData();
                formData.append('file', this.file);

                axios.post('/folios/load_folios', formData, { headers: { 'content-type': 'multipart/form-data' } })
                .then(response => {
                    // this.http_bymonth();
                    // this.modalShow = false;
                    swal(`${response.data.guardados} de ${response.data.total}`, `${response.data.guardados} de ${response.data.total} depósitos se subieron correctamente.`, "success")
                    .then((value) => {
                        location.reload();
                    });
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                    swal("Ocurrió un problema", "Ocurrió un problema al subir todos los depósitos. Verifica que los datos del archivo estén correctos e intenta de nuevo por favor. También verifica tu conexión a internet. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
                });
            } else {
                this.errorFormat = true;
                this.load = false;
            }
        },
        marcarFolio(folio, position){
            this.folio.id = folio.id;
            this.position = position;
            this.modalMarcar = true;
        },
        saveOcupado(){
            this.load = true;
            axios.put('/folios/marcar_ocupado', this.folio).then(response => {
                this.foliosData.data[this.position].marcado_por = response.data.marcado_por;
                this.foliosData.data[this.position].occupied = true;
                this.modalMarcar = false;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        }
    }
}
</script>