<template>
    <div>
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
            <template v-slot:cell(descargar)="data">
                <b-button pill variant="dark" :href="`/files/download_file/${data.item.id}`">
                    <b-icon-download></b-icon-download>
                </b-button>
            </template>
            <template v-slot:cell(download)="data">
                <b-badge v-if="data.item.download" variant="success">
                    <b-icon-check2-circle></b-icon-check2-circle>
                </b-badge>
                <b-badge v-else variant="secondary">
                    <b-icon-x-circle-fill></b-icon-x-circle-fill>
                </b-badge>
            </template>
        </b-table>
        <b-alert v-else show variant="dark">
            <b-icon-info-circle></b-icon-info-circle> Aun no se suben archivos
        </b-alert>
    </div>
</template>

<script>
export default {
    props: ['registers'],
    data(){
        return {
            files: this.registers,
            fields: [
                { key: 'index', label: 'N.' },
                { key: 'created_at', label: 'Se subio el:' },
                { key: 'name', label: 'Nombre del archivo' },
                { key: 'view', label: 'Ver archivo' },
                { key: 'descargar', label: 'Descargar' },
                { key: 'download', label: 'Descargado' },

            ]
        }
    }
}
</script>