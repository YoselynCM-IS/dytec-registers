<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-form-group label="Nombre de la categoria:">
                <b-form-input v-model="form.categorie" required :disabled="load"
                    style="text-transform:uppercase;">
                </b-form-input>
            </b-form-group>
            <b-form-group label="Escuela:">
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
            <div class="text-right">
                <b-button pill :disabled="load" id="btnPre" type="submit">
                    <b-icon-plus-circle></b-icon-plus-circle> Guardar
                </b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
import searchSchoolMixin from '../../../mixins/searchSchoolMixin';
export default {
    props: ['form', 'edit'],
    mixins: [searchSchoolMixin],
    data(){
        return {
            load: false
        }
    },
    methods: {
        onSubmit(){
            this.load = true;
            if(!this.edit){
                axios.post('/revisions/save_categorie', this.form).then(response => {
                    this.$emit('save_categorie', response.data);
                    this.load = false;
                }).catch(error => {
                    // PENDIENTE
                    this.load = false;
                });
            } else {
                axios.put('/revisions/update_categorie', this.form).then(response => {
                    this.$emit('save_categorie', response.data);
                    this.load = false;
                }).catch(error => {
                    // PENDIENTE
                    this.load = false;
                });
            }
        },
        selectSchool(school){
            this.form.school_id = school.id;
            this.school = school.name;
            this.schools = [];
        }
    }
}
</script>

<style>

</style>