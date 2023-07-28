<template>
    <div>
        <form @submit="onSubmit" enctype="multipart/form-data" method="POST" class="mb-5">
            <b-row>
                <b-col>
                    <search-school :load="load" @resultCategories="resultCategories"></search-school>
                </b-col>
                <b-col sm="2">
                    <b-button @click="obtenerTotalLibros()" :disabled="(selected.length == 0) || load"
                        variant="dark" pill block class="mb-1">
                        Total de libros
                    </b-button>
                </b-col>
                <b-col sm="2" class="text-right">
                    <b-button type="submit" variant="success" block pill 
                        :disabled="(!form.file) || (form.comision_libro == 0) || load">
                        <i class="fa fa-check"></i> Guardar
                    </b-button>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col sm="4">
                    <div>
                        <b-row>
                            <b-col><b>Total de los cortes</b>:</b-col>
                            <b-col class="text-right">${{ form.total | numeral('0,0') }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><b>Total de libros</b>:</b-col>
                            <b-col class="text-right">{{ form.total_libros | numeral('0,0') }}</b-col>
                        </b-row>
                    </div>
                </b-col>
                <b-col sm="4">
                    <!-- <b-button :disabled="(form.comision_libro <= 0) || load" class="mb-1"
                        variant="dark" size="sm" pill block @click="calcularTotal()">
                        Calcular total de comisión
                    </b-button> -->
                    <b-row>
                        <b-col><b>Comisión por libro</b>:</b-col>
                        <b-col class="text-right">
                            <b-form-input v-model="form.comision_libro" @change="calcularTotal()"
                                :disabled="(form.total_libros <= 0) || load"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col><b>Total de comisión</b>:</b-col>
                        <b-col class="text-right">${{ form.total_comision | numeral('0,0') }}</b-col>
                    </b-row>
                </b-col>
                <b-col sm="4">
                    <!-- <input :disabled="(form.total_comision <= 0) || load" type="file" id="archivoType" 
                        v-on:change="fileChange" required>
                    <label for="archivoType"><b-icon-upload></b-icon-upload> Subir comprobante</label>
                    <p v-if="form.file">
                        Comprobante: <b>{{ form.file.name }}</b>
                    </p> -->
                    <b-form-group label="Subir comprobante">
                        <b-form-file :disabled="(form.total_comision <= 0) || load"
                            v-on:change="fileChange" required
                            v-model="form.file" ref="file-input"
                            id="comprobanteType"></b-form-file>
                    </b-form-group>
                </b-col>
            </b-row>
        
            <b-table v-if="cortes.length > 0"
                :items="cortes" :fields="fields"  class="mt-3"
                :select-mode="selectMode" responsive="sm" ref="selectableTable"
                selectable @row-selected="onRowSelected">
                <template v-slot:cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template v-slot:cell(created_at)="data">
                    {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
                </template>
            </b-table>
        </form>
    </div>
</template>

<script>
import SearchSchool from '../partials/SearchSchool.vue'
export default {
  components: { SearchSchool },
  data() {
    return {
        load: false,
        cortes: [],
        fields: [
            {key: 'index', label: 'N.'},
            {key: 'categorie', label: 'Corte'},
            {key: 'creado_por', label: 'Creado por'},
            {key: 'created_at', label: 'Creado el'}
        ],
        selectMode: 'multi',
        selected: [],
        form: {
            school_id: null,
            total: 0,
            total_libros: 0,
            total_comision: 0,
            comision_libro: 0,
            file: null,
            ids: []
        }
    }
  },
  methods: {
    resultCategories(cortes, school_id){
        this.cortes = cortes;
        this.form.school_id = school_id;
    },
    onRowSelected(items) {
        this.selected = items;
        this.setValues();
    },
    setValues(){
        this.form.total = 0;
        this.form.total_libros = 0;
        this.form.total_comision = 0;
        this.form.comision_libro = 0;
        this.form.file = null;
        this.$refs['file-input'].reset()
    },
    obtenerTotalLibros(){
        this.form.ids = [];
        this.selected.forEach(s => {
            this.form.ids.push(s.id);
        });
        this.load = true;
        axios.put('/revisions/calculate_libros', this.form)
            .then(response => {
                this.form.total = response.data.total;
                this.form.total_libros = response.data.total_libros;
                this.form.total_comision = 0;
                this.form.comision_libro = 0;
                this.load = false;   
            });
    },
    calcularTotal(){
        this.form.total_comision = parseFloat(this.form.comision_libro) * this.form.total_libros;
    },
    fileChange(e){
        var fileInput = document.getElementById('comprobanteType');
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        
        if(allowedExtensions.exec(fileInput.value)){
            this.form.file = e.target.files[0];  
        } else {
            swal("Revisar formato de archivo", "Formato de archivo no permitido, solo puede ser en formato imagen (jpg, jpeg, png)", "warning");
        }
    },
    onSubmit(e){
        e.preventDefault();
        this.load = true;
        
        let formData = new FormData();
        formData.append('file', this.form.file, this.form.file.name);
        formData.append('school_id', this.form.school_id);
        formData.append('total', this.form.total);
        formData.append('total_libros', this.form.total_libros);
        formData.append('total_comision', this.form.total_comision);
        formData.append('comision_libro', this.form.comision_libro);
        for (var i = 0; i < this.form.ids.length; i++) {
            let id = this.form.ids[i];
            formData.append('ids[]', id);
        }
        axios.post('/revisions/save_pago', formData, {headers: { 'content-type': 'multipart/form-data' } }).then(response => {
            this.load = false;
            swal('OK', 'El pago se guardo correctamente.', "success")
                .then((value) => {
                    location.reload();
                });;
        }).catch(error => {
            this.load = false;
        });
    }
  }

}
</script>

<style>

</style>