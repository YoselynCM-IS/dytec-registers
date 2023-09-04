<template>
    <div>
        <b-form @submit.prevent="!edit ? onSubmit():onUpdate()">
            <b-form-group label="Escuela certificadora:">
                <b-form-select v-model="book.editorial"
                    :options="editoriales" required
                ></b-form-select>
            </b-form-group>
            <b-form-group label="Nombre de la certificación:">
                <b-form-input v-model="book.name" required :disabled="load"
                    style="text-transform:uppercase;">
                </b-form-input>
                <div v-if="errors && errors.name" class="text-danger">La certificación ya se encuentra registrada.</div>
            </b-form-group>
            <b-form-group label="Precio">
                <b-form-input v-model="book.price" required type="number" 
                    step="0.01" min="1" :disabled="load"
                ></b-form-input>
                <div v-if="errors && errors.price" class="text-danger">El precio debe ser mayor a 0.</div>
            </b-form-group>
            <div class="text-right">
                <b-button pill :disabled="load" id="btnPre" type="submit">
                    <b-icon-check></b-icon-check> Guardar
                </b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
export default {
    props: ['book', 'edit'],
    data(){
        return {
            load: false,
            errors: {},
            editoriales: []
        }
    },
    created: function(){
        axios.get('/books/get_editoriales').then(response => {
            this.editoriales = [];
            let edit = response.data;
            this.editoriales.push({
                value: null,
                text: 'Selecciona una opción',
                disabled: true
            });
            edit.forEach(e => {
                this.editoriales.push({
                    value: e.name,
                    text: e.name
                });
            });
        }).catch(error => {
            // PENDIENTE
        });
    },
    methods: {
        onSubmit(){
            this.load = true;
            axios.post('/books/new_book', this.book).then(response => {
                this.load = false;
                this.$emit('updateBooks', response.data);
            })
            .catch(error => {
                this.load = false;
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    swal("Ocurrió un problema", "Ocurrió un problema al guardar la certificación, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
                }
            });
        },
        onUpdate(){
            this.load = true;
            axios.put('/books/update_book', this.book).then(response => {
                this.load = false;
                this.$emit('updateBooks', response.data);
            })
            .catch(error => {
                this.load = false;
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    swal("Ocurrió un problema", "Ocurrió un problema al actualizar la certificación, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
                }
            });
        }
    }
}
</script>