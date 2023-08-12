<template>
    <div>
        <b-row class="mb-3">
            <b-col>
                <pagination size="default" :limit="1" :data="pagos" 
                    @pagination-change-page="get_results">
                    <span slot="prev-nav">
                        <b-icon-chevron-left></b-icon-chevron-left>
                    </span>
                    <span slot="next-nav">
                        <b-icon-chevron-right></b-icon-chevron-right>
                    </span>
                </pagination>
            </b-col>
            <b-col sm="3">
                <b-button @click="modalComision = true" variant="success" pill block>
                    <i class="fa fa-plus-circle"></i> Registrar comisión
                </b-button>
            </b-col>
        </b-row>
        <b-table v-if="!load" :items="pagos.data" :fields="fields">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(comision_libro)="data">
                ${{ data.item.comision_libro | numeral('0,0[.]00') }}
            </template>
            <template v-slot:cell(total_comision)="data">
                ${{ data.item.total_comision | numeral('0,0[.]00') }}
            </template>
            <template v-slot:cell(total_libros)="data">
                {{ data.item.total_comision | numeral('0,0[.]00') }}
            </template>
            <template v-slot:cell(comprobante)="data">
                <a :href="data.item.public_url" target="_blank">Ver</a>
            </template>
        </b-table>
        <load-component v-else></load-component>
        <!-- MODALS -->
        <!-- REGISTRAR COMISION PARA LA ESCUELA -->
        <b-modal v-model="modalComision" hide-footer title="Registrar comisión" size="xl">
            <registrar-comision-component></registrar-comision-component>
        </b-modal>
    </div>
</template>

<script>
import LoadComponent from '../../funciones/LoadComponent.vue'
import RegistrarComisionComponent from './RegistrarComisionComponent.vue'
export default {
    components: { RegistrarComisionComponent, LoadComponent },
    props: ['role'],
    data(){
        return {
            load: false,
            modalComision: false,
            pagos: {},
            fields: [
                {key: 'index', label: 'N.'},
                {key: 'school.name', label: 'Escuela'},
                {key: 'comision_libro', label: 'Comisión por libro'},
                {key: 'total_libros', label: 'Total de libros'},
                {key: 'total_comision', label: 'Total de comisión'},
                {key: 'comprobante', label: 'Comprobante'}
            ]
        }
    },
    created: function(){
        this.get_results();
    },
    methods: {
        get_results(page = 1){
            this.load = true;
            axios.get(`/revisions/get_pagos?page=${page}`).then(response => {
                this.pagos = response.data;
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