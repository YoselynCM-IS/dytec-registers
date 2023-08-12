<template>
    <div>
        <b-form-text id="input-live-help">Escribe el nombre de la escuela para poder ver los registros</b-form-text>
        <b-row class="mb-3">
            <b-col>
                <b-pagination  v-if="registros.length > 0" class="mt-1" v-model="currentPage" pills
                    :per-page="perPage" :total-rows="registros.length" :disabled="load">
                </b-pagination>
            </b-col>
            <b-col>
                <label><b>Escuela</b></label>
                <b-form-input v-model="escuela.name"
                    @keyup="showSchools()" style="text-transform:uppercase;">
                </b-form-input>
                <div class="list-group" v-if="schools.length" id="listR">
                    <a 
                        href="#" v-bind:key="i" 
                        class="list-group-item list-group-item-action" 
                        v-for="(school, i) in schools" 
                        @click="searchSchool(school)">
                        {{ school.name }}
                    </a>
                </div>
            </b-col>
            <b-col>
                <div v-if="escuela.id && registros.length > 0">
                    <label><b>Buscar por Alumno</b></label>
                    <b-form-input v-model="student"
                        @keyup="searchStudent()" style="text-transform:uppercase;">
                    </b-form-input>
                </div>
            </b-col>
        </b-row>
        <b-table v-if="registros.length > 0" class="mt-3" responsive 
            :items="registros" :fields="fields" :busy="load"
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
            <template v-slot:cell(created_at)="data">
                {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
            </template>
            <template v-slot:cell(delivery)="data">
                <b-button variant="success" pill @click="set_delivery(data.item)">
                    <i class="fa fa-check-square-o"></i>
                </b-button>
            </template>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Actualizando...</strong>
                </div>
            </template>
        </b-table>
        <b-alert v-else show variant="dark" class="text-center">
            <b-icon-info-circle></b-icon-info-circle> No se encontraron registros
        </b-alert>
        <b-modal v-model="openConfirm" size="sm" centered title="CONFIRMAR ENTREGA A">
            <h5 style="color: #3b0069">{{ registro.name }}</h5>
            <template #modal-footer>
                <b-button :disabled="load" variant="success" pill @click="save_delivery()">
                    <i v-if="!load" class="fa fa-check-circle"></i>
                    <b-spinner v-else type="grow" small></b-spinner>
                    {{ !load ? 'OK':'GUARDANDO' }}
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            registros: [],
            fields: [
                { label: 'N.', key: 'index' },
                { label: 'Escuela', key: 'school' },
                { label: 'Alumno', key: 'name' },
                { label: 'Libro', key: 'book' },
                { label: 'Cant.', key: 'quantity' },
                { label: 'Precio', key: 'price' },
                { label: 'Total', key: 'total' }, 
                { label: 'Fecha de registro', key: 'created_at' },
                { label: 'Entregado', key: 'delivery' },
            ],
            currentPage: 1,
            perPage: 25,
            load: false,
            registro: { id: null, name: '', school_id: null },
            openConfirm: false,
            escuela: {},
            schools: [],
            student: '',
        }
    },
    methods: {
        set_delivery(register){
            this.registro.id = register.id;
            this.registro.name = register.name;
            this.registro.school_id = this.escuela.id;
            this.openConfirm = true;
        },
        save_delivery() {
            this.load = true;
            axios.put('/student/update_delivery', this.registro).then(response => {
                this.registros = response.data;
                this.load = false;
                this.openConfirm = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        showSchools(){
            if(this.escuela.name.length > 0 && this.escuela.name !== ''){
                axios.get('/schools/show_schools', {params: {escuela: this.escuela.name}}).then(response => {
                    this.schools = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.schools = [];
            }
        },
        searchSchool(school){
            axios.get('/schools/show_school', {params: {school_id: school.id}}).then(response => {
                this.registros = response.data;
                this.escuela.id = school.id;
                this.escuela.name = school.name;
                this.schools = [];
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchStudent() {
            axios.get('/student/by_school', {params: {student: this.student, school_id: this.escuela.id}}).then(response => {
                this.registros = response.data;
            }).catch(error => {
                // PENDIENTE
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