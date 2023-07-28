<template>
    <div>
        <b-form @submit.prevent="!edit ? onSubmit():onUpdate()">
            <b-form-group label="Titulo del libro:">
                <b-form-input v-model="book.name" required :disabled="load"
                    style="text-transform:uppercase;">
                </b-form-input>
                <div v-if="errors && errors.name" class="text-danger">El libro ya se encuentra registrado.</div>
            </b-form-group>
            <b-form-group label="Editorial:">
                <b-form-select v-model="book.editorial"
                    :options="editoriales" required
                ></b-form-select>
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
                    swal("Ocurrió un problema", "Ocurrió un problema al guardar el libro, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
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
                    swal("Ocurrió un problema", "Ocurrió un problema al actualizar el libro, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
                }
            });
        }
    }
}
</script>