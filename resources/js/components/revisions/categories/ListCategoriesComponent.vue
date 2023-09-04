<template>
    <div>
        <b-row v-if="!loadall">
            <b-col sm="3">
                <search-school :load="load" @resultCategories="resultCategories"></search-school>
                <b-list-group v-for="(categorie, i) in categories" v-bind:key="i">
                    <b-list-group-item @click="show_registers(categorie, i)" href="#"
                        variant="secondary">
                        {{categorie.categorie}}
                    </b-list-group-item>
                </b-list-group>
            </b-col>
            <b-col>
                <b-row>
                    <b-col sm="3">
                        <b-button variant="warning" pill size="sm" :disabled="categorie_id == null"
                            @click="editCorte()">
                            <i class="fa fa-pencil"></i>
                        </b-button>
                        <b-button v-if="role == 'manager'"
                            :disabled="categorie_id == null || registersData.total > 0"
                            variant="danger" pill size="sm" @click="deleteCorte()">
                            <i class="fa fa-close"></i>
                        </b-button>
                        <b-button v-if="role == 'manager'" :disabled="categorie_id == null"
                            variant="dark" pill size="sm" @click="archivarCorte()">
                            <i class="fa fa-archive"></i>
                        </b-button>
                    </b-col>
                    <b-col sm="4">
                        <b-form-group label="Buscar registro:">
                            <b-form-input v-model="sStudent" :disabled="load || !registersData.data" 
                                @keyup="showStudents(categorie_id)"
                                style="text-transform:uppercase;">
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
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <pagination size="default" :limit="1" :data="registersData" 
                            @pagination-change-page="getResults">
                            <span slot="prev-nav">
                                <b-icon-chevron-left></b-icon-chevron-left>
                            </span>
                            <span slot="next-nav">
                                <b-icon-chevron-right></b-icon-chevron-right>
                            </span>
                        </pagination>
                        <!-- <b-button v-if="registersData.data" variant="dark" pill
                        :href="`/revisions/download_categorie/${categorie_id}`">
                        <b-icon-download></b-icon-download> Descargar
                    </b-button> -->
                    </b-col>
                </b-row>
                <div v-if="!load">
                    <b-table v-if="registersData.data"
                        :items="registersData.data" :fields="fields" 
                        responsive>
                        <template v-slot:cell(index)="data">
                            {{ data.index + 1 }}
                        </template>
                        <template v-slot:cell(created_at)="data">
                            {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
                        </template>
                        <template v-slot:cell(information)="data">
                            <b-button pill id="btnPre" @click="showInfo(data.item)">
                                <b-icon-info-circle></b-icon-info-circle>
                            </b-button>
                        </template>
                    </b-table>
                    <b-alert show variant="secondary" v-else class="text-center">
                        Presiona sobre el corte para ver los registros.
                    </b-alert>
                </div>
                <load-component v-else></load-component>
            </b-col>
        </b-row>
        <load-component v-else></load-component>
        <!-- MODALS -->
        <!-- INFORMACION EL ESTUDIANTE -->
        <b-modal v-model="modalInfo" hide-footer :title="student.name" size="xl">
            <information-student :student="student"></information-student>
        </b-modal>
        <!-- EDITAR CORTE -->
        <b-modal v-model="modalNEC" title="Editar corte" hide-footer>
            <ne-categorie-component :form="form" :edit="true"
                @save_categorie="save_categorie"></ne-categorie-component>
        </b-modal>
    </div>
</template>

<script>
import LoadComponent from '../../funciones/LoadComponent.vue';
import SearchSchool from '../partials/SearchSchool.vue';
export default {
  components: { SearchSchool, LoadComponent },
    props: ['role'],
    data(){
        return {
            categories: [],
            registersData: {},
            fields: [
                { label: 'N.', key: 'index' },
                // { label: 'Escuela', key: 'school.name' },
                { label: 'Nombre', key: 'name' },
                { label: 'Certificación', key: 'book' },
                { label: 'Correo electrónico', key: 'email' },
                { label: 'Información', key: 'information' },
            ],
            student: {},
            modalInfo: false,
            categorie_id: null,
            sStudent: null,
            students: [],
            modalNEC: false,
            form: {
                id: null,
                categorie: null
            },
            position: null,
            loadall: false,
            load: false,
            categorie_name: null
        }
    },
    created: function (){
        this.show_categories();
    },
    methods: {
        // MOSTRAR PAGINADO
        getResults(page = 1){
            this.http_registers(page);
        },
        // MOSTRAR LAS CAEGORIAS
        show_categories(){
            this.loadall = true;
            axios.get('/revisions/show_categories')
                .then(response => {
                    this.categories = response.data;
                    this.loadall = false;   
                });
        },
        // MOSTRAR REGISTROS
        show_registers(categorie, i){
            this.registersData = {};
            this.categorie_id = categorie.id;
            this.categorie_name = categorie.categorie;
            this.position = i;
            this.sStudent = null;
            this.http_registers();
        },
        // HTTP DE REGISTERS
        http_registers(page = 1){
            this.load = true;
            axios.get(`/revisions/by_categorie?page=${page}`, {params: {categorie_id: this.categorie_id}})
                .then(response => {
                    this.registersData = response.data;
                    this.load = false;   
                });
        },
        // MOSTRAR EL RESULTADO DE LA BUSQUEDA POR ESCUELA
        resultCategories(categories){
            this.categories = categories;
        },
        // MOSTRAR INFORMACIÓN DEL ALUMNO
        showInfo(student){
            axios.get('/student/show_registers', {params: {student_id: student.id}}).then(response => {
                this.student = response.data;
                this.modalInfo = !this.modalInfo;
            }).catch(error => {
                // PENDIENTE
            });
        },
        // MOSTRAR ESTUDIANTES POR CATEGORIA
        showStudents(categorie_id){
            if(this.sStudent.length > 0){
                axios.get('/revisions/by_student', {params: {categorie_id: categorie_id, student: this.sStudent}}).then(response => {
                    this.students = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.students = [];
            }
        },
        // ELEGIR ESTUDIANTE
        selectStudent(student){
            this.load = true;
            axios.get('/revisions/show_student', {params: {student_id: student.id}}).then(response => {
                this.students = [];
                this.registersData = response.data
                this.sStudent = student.name;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // EDITAR CORTE
        editCorte(){
            this.form.id = this.categorie_id;
            this.form.categorie = this.categorie_name;
            this.modalNEC = true;
        },
        // CATEGORIA MODIFICADA
        save_categorie(){
            this.categories[this.position].categorie = this.form.categorie;
            this.modalNEC = false;
            swal("Guardado", "El corte se actualizo correctamente", "success");
        },
        // ELIMINAR CORTE
        deleteCorte(){
            this.load = true;
            axios.delete('/revisions/delete_categorie', {params: {categorie_id: this.categorie_id}}).then(response => {
                swal("Eliminado", "El corte se elimino correctamente.", "success")
                .then((value) => {
                    location.reload();
                });
                this.load = false;
            }).catch(error => {
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al eliminar el libro, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        // ARCHIVAR CORTE
        archivarCorte(){
            this.load = true;
            let form = { categorie_id: this.categorie_id };
            axios.put('/revisions/archive_categorie', form).then(response => {
                swal("OK", "El corte se archivo correctamente.", "success")
                .then((value) => {
                    location.reload();
                });
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al asignar el libro, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        
    }
}
</script>

<style scoped>
    #listR{
        position: absolute;
        z-index: 100;
    }
</style>