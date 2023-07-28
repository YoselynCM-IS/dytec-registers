<template>
    <div>
        <b-row class="mb-3">
            <b-col>
                <b-pagination v-model="currentPage" pills 
                    :per-page="perPage" :total-rows="files.length">
                </b-pagination>
            </b-col>
            <b-col class="text-right">
                <b-button 
                    pill variant="success" 
                    @click="modalShow = !modalShow">
                    <b-icon-plus-circle></b-icon-plus-circle> Subir archivo
                </b-button>
            </b-col>
        </b-row>
        <b-table v-if="files.length > 0" :items="files" :fields="fields">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(created_at)="data">
                {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
            </template>
            <template v-slot:cell(view)="data">
                <b-button pill variant="info" target="_blank"
                    :href="data.item.public_url">
                    <b-icon-box-arrow-up-right></b-icon-box-arrow-up-right>
                </b-button>
            </template>
        </b-table>
        <b-alert v-else show variant="dark">
            <b-icon-info-circle></b-icon-info-circle> Presionar sobre el boton verde para subir archivo.
        </b-alert>
        <b-modal v-model="modalShow" hide-footer title="Subir archivo">
            <b-alert v-if="errorFormat" show variant="warning">Formato de archivo no permitido</b-alert>
            <form @submit="onSubmit" enctype="multipart/form-data">
                <input 
                    :disabled="load" type="file" required id="archivoType"
                    class="custom-file" v-on:change="fileChange">
                <label for="archivoType"><b-icon-upload></b-icon-upload> Seleccionar archivo</label>
                <br><label><b>Archivo:</b> {{ file ? file.name : 'No se ha seleccionado archivo' }}</label>
                <div class="text-right mt-3">
                    <b-button pill :disabled="load" variant="success" type="submit">
                        <i class="fa fa-plus-circle"> Subir</i> 
                    </b-button>
                </div>
                <b-alert class="mt-2" v-if="load" show variant="info">
                    <b-spinner small type="grow"></b-spinner> Subiendo...<br>
                    No cierre el recuadro hasta que se termine de subir el archivo.
                </b-alert>
            </form>
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
            load: false,
            file: null,
            errorFormat: false,
            files: this.registers,
            fields: [
                { key: 'index', label: 'N.' },
                { key: 'created_at', label: 'Se subio el:' },
                { key: 'name', label: 'Nombre del archivo' },
                { key: 'view', label: 'Ver archivo' }
            ],
            currentPage: 1,
            perPage: 25
        }
    },
    methods: {
        fileChange(e){
            this.file = e.target.files[0];
        },
        onSubmit(e){
            e.preventDefault();

            var fileInput = document.getElementById('archivoType');
            var allowedExtensions = /(\.txt)$/i;
            this.load = true;
            if(allowedExtensions.exec(fileInput.value)){
                this.errorFormat = false;
                let formData = new FormData();
                formData.append('file', this.file);

                axios.post('/files/save_file', formData, { headers: { 'content-type': 'multipart/form-data' } })
                    .then(response => {
                        this.files.push(response.data);
                        this.modalShow = false;
                        swal("Guardado", "El archivo se guardo correctamente. Gracias.", "success");
                        this.load = false;
                    })
                    .catch(error => {
                        
                    });
            } else {
                this.errorFormat = true;
                this.load = false;
            }
        },
    }
}
</script>