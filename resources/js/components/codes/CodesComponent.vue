<template>
    <div>
        <div>
            <b-row>
                <b-col>
                    <b-form-group label="Buscar por registro:">
                        <b-form-input v-model="qStudent"
                            @keyup="student_byschool()"
                            style="text-transform:uppercase;">
                        </b-form-input>
                    </b-form-group>
                    <!-- <b-form-group label="Buscar por escuela:">
                        <b-form-input v-model="school" @keyup="showSchools()"
                            style="text-transform:uppercase;">
                        </b-form-input>
                        <div class="list-group" v-if="schools.length" id="listR">
                            <a 
                                href="#" v-bind:key="i" 
                                class="list-group-item list-group-item-action" 
                                v-for="(school, i) in schools" 
                                @click="selectSchool(school)">
                                {{ school.name }}
                            </a>
                        </div>
                    </b-form-group> -->
                </b-col>
                <b-col>
                    <b-form-group label="Buscar por certificación:">
                        <b-form-input v-model="book" @keyup="showBooks()"
                            style="text-transform:uppercase;">
                        </b-form-input>
                        <div class="list-group" v-if="books.length" id="listR">
                            <a 
                                href="#" v-bind:key="i" 
                                class="list-group-item list-group-item-action" 
                                v-for="(book, i) in books" 
                                @click="selectBook(book)">
                                {{ book.name }}
                            </a>
                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
        </div>
        <!-- <b-tabs v-model="tabActivo" content-class="mt-3" fill class="tab-stds">
            <b-tab title="Digital">
                <b-row class="mb-2">
                    <b-col>
                        <b-pagination v-model="currentPage1" pills
                            :per-page="perPage" :total-rows="digitales.length" :disabled="load">
                        </b-pagination>
                    </b-col>
                    <b-col class="text-right">
                        <b-button :disabled="load || validar_entregas()" pill id="btnPre" 
                            @click="modalShow = !modalShow; stsNE = [];">
                            <b-icon-plus-circle></b-icon-plus-circle> Subir codigos
                        </b-button>
                    </b-col>
                    <b-col class="text-right">
                        <b-button :disabled="load || validar_entregas()"
                            pill variant="secondary" :href="`/student/download_emails/${school}/${book}`">
                            <b-icon-download></b-icon-download> Digital (Pendiente)
                        </b-button>
                    </b-col>
                </b-row>
                <b-table v-if="digitales.length > 0" :items="digitales" :fields="fieldsDigital" 
                    :per-page="perPage" :current-page="currentPage1" :busy="load"
                    responsive class="mb-3">
                    <template v-slot:cell(index)="data">
                        {{ data.index + 1 }}
                    </template>
                    <template v-slot:cell(school_id)="data">
                        {{ data.item.school.name }}
                    </template>
                    <template v-slot:cell(codes)="data">
                        <b-badge v-if="data.item.codes" pill variant="success">
                            <b-icon-check2-circle></b-icon-check2-circle> Enviado
                        </b-badge>
                        <b-badge v-else pill variant="secondary">
                            <b-icon-exclamation-triangle></b-icon-exclamation-triangle> No enviado
                        </b-badge>
                        <ul v-if="data.item.user_codes !== null">
                            <li>{{ data.item.user_codes }}</li>
                            <li>{{ data.item.date_codes | moment("YYYY-MM-DD hh:mm:ss") }}</li>
                        </ul>
                        <label v-if="data.item.send_codes > 0">
                            Enviados
                            <b-badge pill variant="info">
                                {{ data.item.send_codes }}
                            </b-badge>
                        </label>
                    </template>
                </b-table>
                <b-alert v-else show variant="dark" class="text-center">
                    <b-icon-info-circle></b-icon-info-circle> No se encontraron registros
                </b-alert>
            </b-tab>
            <b-tab title="Fisico"> -->
                <b-row class="mb-2">
                    <b-col>
                        <b-pagination v-model="currentPage2" pills
                            :per-page="perPage" :total-rows="students.length" :disabled="load">
                        </b-pagination>
                    </b-col>
                    <!-- <b-col sm="2" class="text-right">
                        <b-button :disabled="load" pill id="btnPre" block
                            @click="moreDownloads()">
                            <b-icon-download></b-icon-download> Descargas
                        </b-button>
                    </b-col> -->
                    <b-col sm="2" class="text-right">
                        <b-button id="btnPre" pill :disabled="load || this.selected.length == 0" block
                            @click="mark_delivery()">
                            <b-icon-check></b-icon-check> Marcar entrega
                        </b-button>
                    </b-col>
                    <b-col sm="2">
                        <b-button @click="selectAllRows" pill block
                            size="sm" id="btnPre" :disabled="students.length == 0">
                            <b-icon-check-square-fill></b-icon-check-square-fill> Seleccionar todo
                        </b-button>
                        <b-button @click="clearSelected" pill size="sm" block
                            variant="secondary" :disabled="load || this.selected.length == 0">
                            <b-icon-x></b-icon-x> Borrar selección
                        </b-button>
                    </b-col>
                </b-row>
                <b-table v-if="students.length > 0" :items="students" :fields="fieldsFisicos" class="mb-3"
                    :per-page="perPage" :current-page="currentPage2" :busy="load"
                    ref="selectableTable" selectable :select-mode="selectMode"
                    @row-selected="onRowSelected">
                    <template v-slot:cell(index)="data">
                        {{ data.index + 1 }}
                    </template>
                    <template v-slot:cell(school_id)="data">
                        {{ data.item.school.name }}
                    </template>
                    <template v-slot:cell(codes)="data">
                        <div v-if="data.item.delivery">
                            <b-badge pill variant="success">
                                <b-icon-check2-circle></b-icon-check2-circle> Entregado
                            </b-badge>
                            <p v-if="data.item.user_delivery !== null">
                                {{ data.item.user_delivery }} / {{ data.item.date_delivery | moment("YYYY-MM-DD hh:mm:ss") }}
                            </p>
                        </div>
                        <b-badge v-else pill variant="secondary">
                            <b-icon-exclamation-triangle></b-icon-exclamation-triangle> No entregado
                        </b-badge>
                    </template>
                    <template v-slot:cell(selected)="{ rowSelected }">
                        <template v-if="rowSelected">
                            <b-icon-check-square-fill></b-icon-check-square-fill>
                        </template>
                        <template v-else>
                            <b-icon-square></b-icon-square>
                        </template>
                    </template>
                </b-table>
                <b-alert v-else show variant="dark" class="text-center">
                    <b-icon-info-circle></b-icon-info-circle> No se encontraron registros
                </b-alert>
            <!-- </b-tab>
        </b-tabs> -->
        
        <!-- SUBIR ARCHIVO PARA ENVIAR CODIGOS -->
        <b-modal v-model="modalShow" hide-footer title="Subir archivo">
            <b-alert v-if="errorFormat" show variant="warning">Formato de archivo no permitido</b-alert>
            <form @submit="onSubmit" enctype="multipart/form-data">
                <input 
                    :disabled="load" type="file" required id="archivoType"
                    class="custom-file" v-on:change="fileChange">
                <label for="archivoType"><b-icon-upload></b-icon-upload> Seleccionar archivo</label>
                <br><b>Archivo:</b> {{ file ? file.name : 'No se ha seleccionado archivo' }}
                <div class="text-right mt-3">
                    <b-button pill :disabled="load || file == null" variant="success" type="submit">
                        <b-icon-forward></b-icon-forward> Enviar
                    </b-button>
                </div>
                <b-alert class="mt-2" v-if="load" show variant="info">
                    <b-spinner small type="grow"></b-spinner> Subiendo...<br>
                    No cierre el recuadro hasta que se termine de subir el archivo.
                </b-alert>
            </form>
            <div v-if="stsNE.length > 0">
                <hr>
                <p>Algunos códigos <b>NO</b> pudieron ser enviados. (Los alumnos que <b>NO</b> aparezcan en esta lista, se enviaron correctamente)</p>
                <ol>
                    <li v-bind:key="i" v-for="(s, i) in stsNE" >
                        {{ s }}
                    </li>
                </ol>
            </div>
        </b-modal>
        <!-- MAS BUSQUEDAS Y DESCARGAS -->
        <b-modal v-model="modalDownloads" hide-footer title="Descargas" size="xl">
            <div class="col-md-4">
                <h6><strong>Fisico y Digital</strong></h6>
                <b-row class="mb-2">
                    <b-col>Pendiente</b-col>
                    <b-col>
                        <b-button :disabled="load || validar_entregas()"
                            pill variant="dark" :href="`/student/download_delivery/pendiente/${school}/${book}`">
                            <b-icon-download></b-icon-download> Descargar
                        </b-button>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>Entregado</b-col>
                    <b-col>
                        <b-button :disabled="load || validar_entregas()"
                            pill variant="secondary" :href="`/student/download_delivery/entregado/${school}/${book}`">
                            <b-icon-download></b-icon-download> Descargar
                        </b-button>
                    </b-col>
                </b-row>
            </div><hr>
            <div class="container">
                <h6><strong>Por fecha de entrega</strong></h6>
                <b-row>
                    <b-col sm="3">
                        <b-form-group label="Tipo de libro">
                            <b-form-select v-model="type_book" :disabled="load"
                                :options="type_books"
                            ></b-form-select>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-row>
                            <b-col> 
                                <b-form-group label="De:">
                                    <b-form-datepicker v-model="f_inicio"></b-form-datepicker>
                                </b-form-group>
                            </b-col>
                            <b-col> 
                                <b-form-group label="A:">
                                    <b-form-datepicker v-model="f_final"></b-form-datepicker>
                                </b-form-group>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col sm="2">
                        <b-button pill id="btnPre" block :disabled="validar_dates() || load"
                            @click="type_book == 'digital' ? codesDates():entregasDates()">
                            <b-icon-search></b-icon-search> Visualizar
                        </b-button>
                        <b-button pill variant="dark" block :disabled="validar_dates() || load"
                            :href="`/student/download_dates/${type_book}/${f_inicio}/${f_final}`">
                            <b-icon-download></b-icon-download> Descargar
                        </b-button>
                    </b-col>
                </b-row>
            </div>
        </b-modal>
    </div>
</template>

<script>
// SWEETALERT
import swal from 'sweetalert';
export default {
    props: ['registers'],
    data(){
        return {
            modalShow: false,
            errorFormat: '',
            load: false,
            file: null,
            digitales: [],
            fisicos: [],
            students: this.registers,
            items: [],
            fieldsDigital: [
                {key: 'index', label: 'N.'},
                {key: 'school_id', label: 'Escuela'},
                {key: 'book', label: 'Libro'},
                {key: 'name', label: 'Nombre'},
                {key: 'email', label: 'Correo electrónico'},
                {key: 'codes', label: 'Información códigos'}
            ],
            fieldsFisicos: [
                { key: 'index', label: 'N.' },
                { key: 'name', label: 'Nombre' },
                { key: 'email', label: 'Correo electrónico' },
                {key: 'book', label: 'Certificación'},
                {key: 'codes', label: 'Status'},
                {key: 'selected', label: ''}
            ],  
            school: null,
            book: null,
            schools: [],
            books: [],
            currentPage1: 1,
            currentPage2: 1,
            perPage: 50,
            selectMode: 'multi',
            selected: [],
            school_id: null,
            modalDownloads: false,
            f_inicio: null,
            f_final: null,
            busqBook: false,
            busqSchool: false,
            type_book: null,
            type_books: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'fisico', text: 'Físico'},
                { value: 'digital', text: 'Digital'},
            ],
            tabActivo: 0,
            qStudent: null,
            stsNE: []
        }
    },
    methods: {
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
                formData.append('school_id', this.school_id);
                formData.append('file', this.file);
                axios.post('/student/send_codes', formData, { headers: { 'content-type': 'multipart/form-data' } })
                .then(response => {
                    this.stsNE = response.data;
                    if(this.stsNE.length == 0){
                        this.modalShow = false;
                        // this.digitales = response.data;
                        swal("OK", "Los codigos fueron enviados correctamente.", "success")
                            .then((value) => {
                                location.reload();
                            });
                    } 
                    this.file = null;
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                    swal("Ocurrio un problema", "Verifica que los datos del archivo este correctos e intenta de nuevo.", "warning");
                });
            } else {
                this.errorFormat = true;
                this.load = false;
            }
        },
        showSchools(){
            if(this.school.length > 0 && this.school !== ' '){
                axios.get('/schools/show_schools', {params: {escuela: this.school}}).then(response => {
                    this.schools = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.schools = [];
            }
        },
        showBooks(){
            if(this.book.length > 0 && this.book !== ' '){
                axios.get('/show_books', {params: {book: this.book}}).then(response => {
                    this.books = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.books = [];
            }
        },
        selectSchool(school){
            axios.get('/schools/schools_to_email', {params: {school_id: school.id}}).then(response => {
                this.schools = [];
                this.school = school.name;
                this.digitales = response.data.digitales;
                this.fisicos = response.data.fisicos;
                this.school_id = school.id;
                this.qStudent = null;
                this.validar_busquedas(true, false);
            }).catch(error => {
                // PENDIENTE
            });
        },
        selectBook(book){
            axios.get('/student/books_to_email', {params: {book: book.name}}).then(response => {
                this.books = [];
                this.book = book.name;
                this.students = response.data;
                this.qStudent = null;
                this.validar_busquedas(false, true);
            }).catch(error => {
                // PENDIENTE
            });
        },
        validar_dates(){
            return this.type_book == null || this.f_inicio == null || this.f_final == null;
        },
        validar_busquedas(val_school, val_book){
            this.busqSchool = val_school;
            this.busqBook = val_book;

            if(!this.busqSchool) this.school = null;
            if(!this.busqBook) this.book = null;
        },
        validar_entregas(){
            if(this.fisicos.length == 0 && this.digitales.length > 0) 
                this.tabActivo = 0;
            if(this.digitales.length == 0 && this.fisicos.length > 0)
                this.tabActivo = 1;
            
            return (this.busqBook == false && this.busqSchool == false) || (this.digitales.length == 0 && this.fisicos.length == 0);
        },
        onRowSelected(items) {
            this.selected = items
        },
        selectAllRows(){
            this.$refs.selectableTable.selectAllRows()
        },
        clearSelected() {
            this.$refs.selectableTable.clearSelected()
        },
        mark_delivery(){
            this.load = true;
            let form = { selected: this.selected }
            axios.put('/student/mark_delivery', form).then(response => {
                swal("OK", "Los registros se marcaron correctamente.", "success")
                    .then((value) => {
                        location.reload();
                    });
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        moreDownloads(){
            this.modalDownloads = true;
            this.f_inicio = null;
            this.f_final = null;
        },
        codesDates() {
            this.load = true;
            axios.get('/student/codes_dates', {params: {inicio: this.f_inicio, final: this.f_final}}).then(response => {
                this.validar_busquedas(false, false);
                this.digitales = response.data;
                this.tabActivo = 0;
                this.modalDownloads = false;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        entregasDates(){
            this.load = true;
            axios.get('/student/delivery_dates', {params: {inicio: this.f_inicio, final: this.f_final}}).then(response => {
                this.validar_busquedas(false, false);
                this.fisicos = response.data;
                this.tabActivo = 1;
                this.modalDownloads = false;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        student_byschool(){
            this.load = true;
            axios.get('/student/by_school_ne', {params: {student: this.qStudent, school_id: this.school_id }}).then(response => {
                // this.validar_busquedas(false, false);
                this.students = response.data;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        }
    }
}
</script>

<style scoped>
    #listR{
        position: absolute;
        z-index: 100;
    }
</style>