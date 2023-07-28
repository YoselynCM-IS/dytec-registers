<template>
    <div>
        <b-row>
            <b-col>
                <pagination size="default" :limit="1" :data="studentsData" 
                    @pagination-change-page="getResults">
                    <span slot="prev-nav">
                        <b-icon-chevron-left></b-icon-chevron-left>
                    </span>
                    <span slot="next-nav">
                        <b-icon-chevron-right></b-icon-chevron-right>
                    </span>
                </pagination>
            </b-col>
            <b-col>
                <b-form-group label="Buscar por escuela:">
                    <b-form-input v-model="school" :disabled="load" @keyup="showSchools()"
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
                </b-form-group>
            </b-col>
            <b-col sm="2" class="text-right">
                <b-button variant="success" pill :disabled="load" block 
                    @click="newCategorie()">
                    <b-icon-plus-circle></b-icon-plus-circle> Crear categoria
                </b-button>
                <b-button id="btnPre" pill @click="show_categories()"
                    :disabled="selected.length == 0 || load" block class="mt-1">
                    <b-icon-check></b-icon-check> Guardar
                </b-button>
            </b-col>
        </b-row>
        <div v-if="school_id !== null">
            <b-table v-if="studentsData.data"
                :items="studentsData.data" :fields="fields" responsive
                :busy="load" ref="selectableTable" selectable :select-mode="selectMode"
                        @row-selected="onRowSelected">
                <template v-slot:cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template v-slot:cell(created_at)="data">
                    {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
                </template>
                <template v-slot:cell(selected)="{ rowSelected }">
                    <template v-if="rowSelected">
                        <b-icon-check-square-fill></b-icon-check-square-fill>
                    </template>
                    <template v-else>
                        <b-icon-square></b-icon-square>
                    </template>
                </template>
                <template v-slot:table-busy>
                    <div class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Cargando...</strong>
                    </div>
                </template>
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="4"></b-th>
                        <b-th class="text-right">
                            Seleccionar todo
                        </b-th>
                        <b-th>
                            <b-button @click="selectAllRows" pill 
                                size="sm" variant="secondary">
                                <b-icon-check-square-fill></b-icon-check-square-fill>
                            </b-button>
                        </b-th>
                    </b-tr>
                </template>
            </b-table>
            <b-alert v-else show variant="dark" class="text-center mt-5">
                <b-icon-info-circle></b-icon-info-circle> No se encontraron registros.
            </b-alert>
        </div>
        <b-alert v-else show variant="primary" class="text-center mt-5">
            <b-icon-info-circle></b-icon-info-circle> Busca la escuela que deseas agregar al corte.
        </b-alert>

        <!-- MODALS -->
        <b-modal v-model="modalNEC" title="Crear categoria" hide-footer>
            <ne-categorie-component :form="categorie" :edit="false"
                @save_categorie="save_categorie"></ne-categorie-component>
        </b-modal>
        <b-modal v-model="modalSave" title="Mover registros" hide-footer>
            <b-form @submit.prevent="saveRevisions">
                <b-form-group label="Categoria:">
                    <b-form-select v-model="form.categorie_id"
                        :options="categories" required
                    ></b-form-select>
                </b-form-group>
                <div class="text-right">
                    <b-button pill :disabled="load || form.categorie_id == null" id="btnPre" type="submit">
                        <b-icon-plus-circle></b-icon-plus-circle> Guardar
                    </b-button>
                </div>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
import searchSchoolMixin from '../../mixins/searchSchoolMixin';
export default {
    mixins: [searchSchoolMixin],
    data(){
        return {
            studentsData: {},
            load: false,
            fields: [
                {label: 'N.', key: 'index'},
                {label: 'Escuela', key: 'school.name' },
                {label: 'Alumno', key: 'name' },
                {label: 'Libro', key: 'book' },
                {label: 'Fecha de registro', key: 'created_at' },
                {label: 'Revisado', key: 'selected'}
            ],
            selectMode: 'multi',
            selected: [],
            school_id: null,
            modalNEC: false,
            modalSave: false,
            categories: [],
            form: { categorie_id: null, selected: [] },
            categorie: { id: null, categorie: null, school_id: null },
        }
    },
    // created: function(){
    //     this.show_all();
    // },
    methods: {
        getResults(page = 1){
            // if(this.school_id == null)
            //     this.show_all(page);
            // else 
                this.show_school(page);
            
        },
        show_all(page = 1){
            this.load = true;
            axios.get(`/revisions/index?page=${page}`)
                .then(response => {
                    this.studentsData = response.data; 
                    this.load = false;   
                });
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
        // AGREGAR CATEGORIA
        newCategorie(){
            this.categorie = { id: null, categorie: null, school_id: null };
            this.modalNEC = true;;
        },
        // GUARDAR CATEGORIA
        save_categorie(categorie){
            this.modalNEC = false;
            swal("OK", `La categoria se guardo correctamente.`, "success");
        },
        // MOSTRAR CATEGORIAS
        show_categories(){
            this.categories = [];
            axios.get('/revisions/show_categories').then(response => {
                let cat = response.data;
                this.categories.push({
                    value: null, text: 'Selecciona una opción', disabled: true
                });
                cat.forEach(c => {
                    this.categories.push({ value: c.id, text: c.categorie });
                });
                this.form.categorie_id = null;
                this.modalSave = true;
            }).catch(error => {
                // PENDIENTE
            });
        },
        // GUARDAR REVISIONES
        saveRevisions(){
            this.load = true;
            this.form.selected = this.selected;
            axios.put('/revisions/save', this.form).then(response => {
                this.getResults();
                this.load = false;
                this.modalSave = false;
                swal("OK", `Los registros se movieron correctamente.`, "success");
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        selectSchool(school){
            this.school_id = school.id;
            this.school = school.name;
            this.show_school();
        },
        show_school(page = 1){
            this.load = true;
            axios.get(`/revisions/show?page=${page}`, {params: {school_id: this.school_id}}).then(response => {
                this.schools = [];
                this.studentsData = response.data;
                this.load = false;
            }).catch(error => {
                // this.load = false;
            });
        }
    }
}
</script>

<style>

</style>

<style scoped>
    #listR{
        position: absolute;
        z-index: 100;
    }
</style>