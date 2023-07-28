<template>
    <div>
        <b-form-input v-model="school" :disabled="load" @keyup="showSchools()"
            style="text-transform:uppercase;" placeholder="Buscar escuela">
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
    </div>
</template>

<script>
import searchSchoolMixin from '../../../mixins/searchSchoolMixin';
export default {
    props: ['load'],
    mixins: [searchSchoolMixin],
    methods: {
        selectSchool(school){
            axios.get('/revisions/categories_byschool', {params: {school_id: school.id}})
                .then(response => {
                    this.school = school.name;
                    this.schools = [];
                    this.$emit('resultCategories', response.data, school.id);
                });
        }
    }
}
</script>

<style>

</style>