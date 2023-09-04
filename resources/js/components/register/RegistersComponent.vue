<template>
    <div>
        <b-row>
            <b-col sm="6">
                <b-pagination class="mt-1" v-model="currentPage" pills v-if="registros.length > 0"
                    :per-page="perPage" :total-rows="registros.length" :disabled="load">
                </b-pagination>
            </b-col>
            <b-col sm="3" class="text-right">
                <b-button :disabled="load" variant="dark" pill 
                    @click="moreSearch()" block>
                    <b-icon-search></b-icon-search> Búsquedas
                </b-button>
            </b-col>
            <b-col sm="3">
                <b-button v-if="role === 'reviewer' || role === 'manager'" :disabled="load" 
                    id="btnPre" pill @click="updateStatus()" block>
                    <b-icon-arrow-clockwise></b-icon-arrow-clockwise> Validar pagos
                </b-button>
            </b-col>
        </b-row>
            <!-- <b-col sm="3" class="text-right">
                <b-button variant="secondary" pill block @click="modalDownload = true">
                    <b-icon-download></b-icon-download> Descargas
                </b-button>
            </b-col>
            <b-col sm="3" class="text-right">
                <b-button v-if="role === 'manager'"
                    variant="dark" block pill @click="debugAccepted()">
                    <b-icon-arrow-clockwise></b-icon-arrow-clockwise> Depurar aceptados
                </b-button>
            </b-col>
            
        </b-row> -->
        <!-- <b-col sm="6">
            <b-row>
                <b-col>
                    <b-form-datepicker v-model="number_rejected"></b-form-datepicker>
                </b-col>
                <b-col>
                    <b-button class="mt-1" :disabled="load || number_rejected == null" block
                        variant="danger" pill @click="updateRejected()">
                        <b-icon-arrow-clockwise></b-icon-arrow-clockwise> Revisar rechazados
                    </b-button>
                </b-col>
            </b-row>
        </b-col> -->
        <b-table v-if="registros.length > 0" class="mt-3" responsive 
            :items="registros" :busy="load" :fields="fields"
            :per-page="perPage" :current-page="currentPage">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(name)="data">
                {{ data.item.name }}
            </template>
            <template v-slot:cell(price)="data">
                ${{ data.item.price | numeral('0,0[.]00') }}
            </template>
            <template v-slot:cell(total)="data">
                ${{ data.item.total | numeral('0,0[.]00') }}
            </template>
            <template v-slot:cell(school)="data">
                {{ data.item.school.name }}
            </template>
            <template v-slot:cell(information)="data">
                <b-button pill id="btnPre" @click="showInfo(data.item)">
                    <b-icon-info-circle></b-icon-info-circle>
                </b-button>
            </template>
            <template v-slot:cell(created_at)="data">
                {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
            </template>
            <template v-slot:cell(check)="data">
                <!-- PENDIENTE POR TIPO DE CURSO -->
                <div v-if="data.item.check === 'accepted'" >
                    <div v-if="!data.item.book.includes('DIGITAL')">
                        <div v-if="data.item.delivery == 0">
                            <b-badge pill variant="warning">
                                <i class="fa fa-exclamation-triangle"></i> Libro no entregado
                            </b-badge>
                        </div>
                        <b-badge v-else pill variant="success">
                            <i class="fa fa-check"></i> Libro entregado
                        </b-badge>
                    </div>
                    <div v-else>
                        <b-badge v-if="data.item.codes" variant="success">
                            <i class="fa fa-check"></i> Código enviado
                        </b-badge>
                        <b-badge v-else pill variant="warning">
                            <i class="fa fa-exclamation-triangle"></i> Código no enviado
                        </b-badge>
                    </div>
                </div>
                <!-- PENDIENTE POR TIPO DE CURSO -->
                <b-badge v-if="data.item.check === 'process'" pill variant="secondary">
                    <b-icon-three-dots></b-icon-three-dots> Proceso
                </b-badge>
                <div v-if="data.item.check === 'rejected'">
                    <b-badge pill variant="danger">
                        <b-icon-x-circle-fill></b-icon-x-circle-fill> Rechazado
                    </b-badge>
                    <b-button v-if="role === 'reviewer' || role === 'manager'" block
                        :disabled="load" variant="outline-danger" pill size="sm" @click="debugRejected(data.item)">
                        <b-icon-trash></b-icon-trash> Eliminar
                    </b-button>
                    <b-button v-if="role === 'manager'" :disabled="load" block
                        variant="outline-success" pill size="sm" @click="selectStatus(data.item)">
                        <b-icon-arrow-clockwise></b-icon-arrow-clockwise> Aceptar
                    </b-button>
                </div>
                <b-button v-if="(role === 'manager' || role === 'reviewer') && data.item.check === 'accepted'" 
                    variant="outline-dark" pill size="sm" @click="resend_mail(data.item)" block>
                    <i class="fa fa-share"></i> Reenviar correo
                </b-button>
            </template>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Actualizando...</strong>
                </div>
            </template>
        </b-table>
        <b-alert v-else show variant="dark" class="text-center mt-5">
            <b-icon-info-circle></b-icon-info-circle> No hay registros.
        </b-alert>
        <b-modal v-model="modalShow" hide-footer :title="student.name" size="xl">
            <!-- <b-row class="mb-2">
                <b-col>
                    
                </b-col>
                <b-col sm="2" class="text-right">
                    <b-button v-if="!viewEdit && student.codes == 0 && student.delivery == 0" 
                        variant="warning" pill @click="editRegister()">
                        <b-icon-pencil></b-icon-pencil> Editar
                    </b-button>
                    <b-button v-if="viewEdit" variant="secondary" pill :disabled="load"
                        @click="viewEdit = false">
                        <b-icon-arrow-left-circle-fill></b-icon-arrow-left-circle-fill> Regresar
                    </b-button>
                </b-col>
            </b-row> -->
            <div v-if="!viewEdit">
                <information-student :student="student"></information-student>
            </div>
            <div v-else>
                <edit-register :form="form_student" :optSchools="schools" :userid="userid" :role="role" @updated_student="updated_student">
                </edit-register>
            </div>
        </b-modal>
        <b-modal v-model="modalShow2" hide-footer title="Buscar por:">
            <label><b>Nombre</b></label>
            <b-row>
                <b-col>
                    <b-form-input v-model="sStudent"
                        @keyup="showStudents()" style="text-transform:uppercase;">
                    </b-form-input>
                    <div class="list-group" v-if="students.length" id="listR">
                        <a 
                            href="#" v-bind:key="i" 
                            class="list-group-item list-group-item-action" 
                            v-for="(student, i) in students" 
                            @click="selectStudent(student)">
                            {{ student.name }}
                        </a>
                    </div>
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchStudent()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Certificación</b></label>
            <b-row>
                <b-col>
                    <b-form-input v-model="sLibro"
                    @keyup="showBooks()" style="text-transform:uppercase;">
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
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchBook()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <b>Plantel</b>
            <b-row>
                <b-col>
                    <b-form-input v-model="escuela"
                        @keyup="showSchools()" style="text-transform:uppercase;">
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
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchSchool()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Status</b></label>
            <b-row>
                <b-col>
                    <b-form-select v-model="value_status" :options="status">
                    </b-form-select>
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchStatus()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Tipo de pago:</b></label>
            <b-row>
                <b-col>
                    <b-form-select v-model="sType" :options="types"></b-form-select>
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchType()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Fecha de pago</b></label>
            <b-row>
                <b-col>
                    <b-form-datepicker v-model="fecha">
                    </b-form-datepicker>
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchDate()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row>
            <!-- <label><b>Total depositado</b></label>
            <b-row>
                <b-col>
                    <b-form-input v-model="sTotal" type="number">
                    </b-form-input>
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchTotal()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row> -->
            <!-- <label><b>Folio / Movimiento</b></label> 
            <b-row>
                <b-col>
                    <b-form-input v-model="sFolio"></b-form-input>
                </b-col>
                <b-col sm="4">
                    <b-button pill id="btnPre" @click="searchFolio()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr> -->
        </b-modal>
        <b-modal v-model="modalShow3" hide-footer :title="form_std.name" size="xl">
            <search-folio @foliosSelected="foliosSelected"></search-folio>
            <hr>
            <b-form @submit.prevent="updateRegister">
                <h6><b>Asignar folios</b></h6>
                <b-table responsive :items="std_registros" :fields="fieldsReg">
                    <template v-slot:cell(index)="data">
                        {{ data.index + 1 }}
                    </template>
                    <template v-slot:cell(total)="data">
                        ${{ data.item.total | numeral('0,0[.]00') }}
                    </template>
                    <template v-slot:cell(selected)="data">
                         <b-form-select :disabled="seleccionados.length == 0" 
                            v-model="data.item.folio_id" :options="seleccionados"
                        ></b-form-select>
                    </template>
                </b-table>
                <div class="text-right">
                    <b-button pill :disabled="load" variant="success" type="submit">
                        <b-icon-check2-circle></b-icon-check2-circle> Guardar
                    </b-button>
                </div>
            </b-form>
        </b-modal>
        <b-modal v-model="openConfirm" size="sm" centered title="CONFIRMAR ENTREGA A">
            <h5 style="color: #3b0069">{{ std_delivery.name }}</h5>
            <template #modal-footer>
                <b-button :disabled="load" variant="success" pill @click="save_delivery()">
                    <i v-if="!load" class="fa fa-check-circle"></i>
                    <b-spinner v-else type="grow" small></b-spinner>
                    {{ !load ? 'OK':'GUARDANDO' }}
                </b-button>
            </template>
        </b-modal>
        <b-modal v-model="modalDownload" title="Descargas" hide-footer>
            <b-row>
                <b-col>
                    <label>Registros por fecha</label>
                    <b-row>
                        <b-col sm="2" class="text-right">De:</b-col>
                        <b-col>
                            <b-form-datepicker v-model="fecha1"></b-form-datepicker>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col sm="2" class="text-right">A:</b-col>
                        <b-col>
                            <b-form-datepicker v-model="fecha2"></b-form-datepicker>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="4">
                    <b-button class="mt-4" pill block variant="dark"
                        :href="`/registros/by_day/${fecha1}/${fecha2}`">
                        <b-icon-download></b-icon-download> Descargar
                    </b-button>
                </b-col>
            </b-row>
            <b-row class="mt-5">
                <b-col>Busqueda por escuela</b-col>
                <b-col sm="4">
                    <b-button :disabled="load || temporal1 == null" variant="dark" pill 
                        :href="`/registros/download/${temporal1}`" block>
                        <b-icon-download></b-icon-download> Descargar
                    </b-button>
                </b-col>
            </b-row><hr>
            <b-row>
                <b-col>Busqueda por status</b-col>
                <b-col sm="4">
                    <b-button :disabled="load || value_status == null" variant="dark" pill 
                        :href="`/registros/download_status/${value_status}`" block>
                        <b-icon-download></b-icon-download> Descargar
                    </b-button>
                </b-col>
            </b-row>
        </b-modal>
    </div>
</template>

<script>
import swal from 'sweetalert';
import banksMixin from '../../mixins/banksMixin';
export default {
    props: ['registers', 'role', 'userid'],
    mixins: [banksMixin],
    data(){
        return {
            registros: this.registers,
            fields: [
                { label: 'N.', key: 'index' },
                { label: 'Fecha de registro', key: 'created_at' },
                // { label: 'Escuela', key: 'school' },
                { label: 'Nombre', key: 'name' },
                { label: 'Certificación', key: 'book' },
                { label: 'Cant.', key: 'quantity' },
                { label: 'Precio', key: 'price' },
                { label: 'Total', key: 'total' },
                { label: '', key: 'information' },
                { label: '', key: 'check' }
            ],
            modalShow: false,
            modalShow2: false,
            modalShow3: false,
            student: {},
            school: {},
            sFolio: null,
            sTotal: null,
            currentPage: 1,
            perPage: 25,
            escuela: '',
            schools: [],
            fecha: null,
            fecha1: null,
            fecha2: null,
            sType: null,
            sSchool: null,
            types: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'practicaja', text: 'DEPOSITO EN PRACTICAJA'},
                { value: 'ventanilla', text: 'DEPOSITO EN VENTILLA'},
                { value: 'transferencia', text: 'TRANSFERENCIA'},
            ],
            sLibro: '',
            books: [],
            load: false,
            temporal1: null,
            temporal2: null,
            value_status: null,
            status: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'accepted', text: 'Aceptado' },
                { value: 'rejected', text: 'Rechazado' },
                { value: 'process', text: 'Proceso' }
            ],
            sStudent: null,
            students: [],
            form: {},
            folios: [],
            seleccionados: [{
                    value: null,
                    text: 'Selecciona una opción',
                    disabled: true
            }],
            fieldsSel: [
                { key: 'index', label: 'N.' },
                { key: 'fecha', label: 'Fecha' },
                { key: 'concepto', label: 'Concepto' },
                { key: 'abono', label: 'Abono' },
                { key: 'registro', label: 'Registro' },
                { key: 'delete', label: 'Quitar' }
            ],
            fieldsReg: [
                { key: 'index', label: 'N.' },
                { key: 'date', label: 'Fecha' },
                { key: 'type', label: 'Tipo' },
                { key: 'bank', label: 'Banco' },
                { key: 'invoice', label: 'Folio / Referencia' },
                { key: 'auto', label: 'Concepto / Autorización' },
                { key: 'total', label: 'Total' },
                { key: 'selected', label: 'Asignar folio' }
            ],
            form_std: {},
            std_registros: [],
            std_delivery: { id: null, name: '' },
            openConfirm: false,
            sBank: null,
            viewEdit: false,
            form_student: {
                id: null, name: null, email: null, telephone: null, school: null,
                book: null, quantity: null, price: null, a_depositar: null
            },
            number_rejected: null,
            modalDownload: false,
            f_inicio: null,
            f_final: null
        }
    },
    methods: {
        moreSearch(){
            this.schools = [];
            this.students = [];
            this.sSchool = null;
            this.escuela = null;
            this.fecha = null;
            this.sLibro = '';
            this.temporal1 = null;
            this.temporal2 = null;
            this.modalShow2 = !this.modalShow2;
        },
        updateStatus(){
            this.load = true;
            axios.put('/registros/update_status').then(response => {
                this.registros = response.data;
                this.load = false;
                swal("OK", "Se validaron máximo 25 registros en proceso.", "success");
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Problema al validar.", "Ocurrió un problema al validar los registros en proceso, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        showInfo(student){
            this.viewEdit = false;
            axios.get('/student/show_registers', {params: {student_id: student.id}}).then(response => {
                this.student = response.data;
                this.school = this.student.school;
                this.modalShow = !this.modalShow;
            }).catch(error => {
                // PENDIENTE
            });
        },
        showSchools(){
            if(this.escuela.length > 0 && this.escuela !== ' '){
                axios.get('/schools/show_schools', {params: {escuela: this.escuela}}).then(response => {
                    this.schools = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.schools = [];
            }
        },
        selectSchool(school){
            this.sSchool = school;
            this.escuela = school.name;
            this.schools = [];
        },
        searchSchool(){
            axios.get('/schools/show_school', {params: {school_id: this.sSchool.id}}).then(response => {
                if(response.data.length === 0){
                    this.makeToast(`No hay registros de ${this.sSchool.name}`);
                    this.schools = [];
                } else {
                    this.registros = response.data;
                    this.temporal1 = this.sSchool.id;
                    this.modalShow2 = false;
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchType(){
            axios.get('/registros/by_type', {params: {type: this.sType}}).then(response => {
                if(response.data.length > 0){
                    this.modalShow2 = false;
                    this.sType = null;
                    this.registros = response.data;
                } else {
                     this.makeToast(`No hay registros de pagos realizados en ${this.sType}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchFolio(){
            axios.get('/registros/by_folio', {params: {folio: this.sFolio}}).then(response => {
                if(response.data.length > 0){
                    this.modalShow2 = false;
                    this.sFolio = null;
                    this.registros = response.data;
                } else {
                    this.makeToast(`No hay registro del folio ${this.sFolio}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchTotal(){
            axios.get('/registros/by_total', {params: {total: this.sTotal}}).then(response => {
                if(response.data.length > 0){
                    this.modalShow2 = false;
                    this.sTotal = null;
                    this.registros = response.data;
                } else {
                    this.makeToast(`No hay registro de $${this.sTotal}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        showBooks(){
            if(this.sLibro.length > 0 && this.sLibro !== ' '){
                axios.get('/show_books', {params: {book: this.sLibro}}).then(response => {
                    this.books = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.books = [];
            }
        },
        selectBook(book){
            this.sLibro = book.name;
            this.books = [];
        },
        searchBook(){
            axios.get('/registros/by_book', {params: {book: this.sLibro}}).then(response => {
                if(response.data.length > 0){
                    this.registros = response.data;
                    this.modalShow2 = false;
                } else {
                     this.makeToast(`No hay registros de ${this.sLibro}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchDate(){
            axios.get('/registros/by_date', {params: {fecha: this.fecha}}).then(response => {
                if(response.data.length > 0){
                    this.registros = response.data;
                    this.modalShow2 = false;
                } else {
                     this.makeToast(`No hay registros de pagos realizados el ${this.fecha}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        updateRejected(){
            this.load = true;
            let form = { number_rejected: this.number_rejected };
            axios.put('/registros/update_rejected', form).then(response => {
                this.registros = response.data;
                this.load = false;
                swal("OK", "Revisión de rechazados, terminada correctamente.", "success");
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al actualizar, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        makeToast(message){
            this.$bvToast.toast(message, {
                title: 'Mensaje',
                toaster: 'b-toaster-top-center',
                solid: true,
                appendToast: false
            });
        },
        debugRejected(student){
            this.load = true;
            axios.delete('/student/delete', {params: {student_id: student.id}}).then(response => {
                this.registros = response.data;
                swal("OK", "El registro se ha sido eliminado.", "success");
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al eliminar el registro, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });   
        },
        searchStatus(){
            this.load = true;
            axios.get('/registros/by_status', {params: {status: this.value_status}}).then(response => {
               this.registros = response.data;
               this.modalShow2 = false;
               this.load = false;
            }).catch(error => {
                this.load = false;
            });
        },
        showStudents(){
            if(this.sStudent.length > 0 && this.sStudent !== ' '){
                axios.get('/student/show_students', {params: {student: this.sStudent}}).then(response => {
                    this.students = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.students = [];
            }
        },
        searchStudent(){
            axios.get('/registros/by_student', {params: {student: this.sStudent}}).then(response => {
                if(response.data.length > 0){
                    this.sStudent = null;
                    this.registros = response.data;
                    this.modalShow2 = false;
                } else {
                     this.makeToast(`No hay registro de ${this.sStudent}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchBank() {
            axios.get('/registros/by_bank', {params: {bank: this.sBank}}).then(response => {
                if(response.data.length > 0){
                    this.sBank = null;
                    this.registros = response.data;
                    this.modalShow2 = false;
                } else {
                     this.makeToast(`No hay registros del banco ${this.sBank}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        selectStudent(student){
            this.sStudent = student.name;
            this.students = [];
        },
        selectStatus(student){
            axios.get('/student/show_registers', {params: {student_id: student.id}}).then(response => {
                this.form_std.student_id = student.id;
                this.form_std.name = student.name;
                this.std_registros = response.data.registros;
                this.modalShow3 = true;
            }).catch(error => {
                // PENDIENTE
            });
        },
        foliosSelected(folios){
            // this.seleccionados = [];
            // if(folios.length === this.std_registros.length){
                // this.seleccionados.push({
                //     value: null,
                //     text: 'Selecciona una opción',
                //     disabled: true
                // });
                folios.forEach(folio => {
                    this.seleccionados.push({
                        value: folio.id,
                        text: folio.concepto
                    });
                });
            // }
        },
        deleteSelect(i){
            this.seleccionados.splice(i,1);
        },
        updateRegister(){
            this.load = true;
            this.form_std.registros = this.std_registros;
            axios.put('/student/update_status', this.form_std).then(response => {
                this.registros = response.data;
                this.modalShow3 = false;
                swal("OK", "Aceptado correctamente.", "success");
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                this.modalShow3 = false;
                swal("Ocurrió un problema", "Ocurrió un problema al aceptar, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        set_delivery(register) {
            this.std_delivery.id = register.id;
            this.std_delivery.name = register.name;
            this.openConfirm = true;
        },
        save_delivery() {
            this.load = true;
            axios.put('/student/update_delivery', this.std_delivery).then(response => {
                this.registros = response.data;
                this.load = false;
                this.openConfirm = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        resend_mail(student){
            this.load = true;
            axios.put('/registros/resend_mail', student).then(response => {
                this.load = false;
                swal("OK", "El correo se envió correctamente.", "success");
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al enviar el correo, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        debugAccepted(){
            this.load = true;
            axios.delete('/student/debug_accepted').then(response => {
                this.registros = response.data.students;
                swal("OK", `Han sido eliminados ${response.data.num_debug} registros rechazados que se encontraban aceptados.`, "success");
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al aceptar, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        editRegister(){
            axios.get('/schools/index').then(response => {
                this.schools = response.data;

                let comprobantes = [];
                this.student.registros.forEach(r => {
                    let comprobante = {
                        id: r.id,
                        student_id: r.student_id,
                        type: r.type,
                        invoice: r.invoice,
                        auto: r.auto,
                        clave: r.clave,
                        total: r.total,
                        date: r.date,
                        bank: r.bank
                    };
                    comprobantes.push(comprobante);
                });
                this.form_student = {
                    id: this.student.id,
                    name: this.student.name, 
                    email: this.student.email, 
                    telephone: this.student.telephone, 
                    school: this.student.school_id,
                    book: this.student.book, 
                    quantity: this.student.quantity, 
                    price: this.student.price, 
                    a_depositar: this.student.total,
                    comprobantes: comprobantes
                };
                this.viewEdit = true;
            }).catch(error => {
                // PENDIENTE
            });
        },
        updated_student(student){
            this.student = student;
            this.viewEdit = false;
            swal("OK", "Los datos se han actualizado correctamente.", "success");
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