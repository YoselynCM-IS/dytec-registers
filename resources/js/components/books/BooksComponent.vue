<template>
    <div>
        <b-row>
            <b-col>
                <b-pagination v-model="currentPage" pills 
                    :per-page="perPage" :total-rows="books.length">
                </b-pagination>
            </b-col>
            <b-col>
                <b-form-group>
                    <b-form-input v-model="book_name" @keyup="showBooks()"
                        style="text-transform:uppercase;" placeholder="Buscar libro">
                    </b-form-input>
                </b-form-group>
            </b-col>
            <b-col sm="3" class="text-right">
                <b-button pill id="btnPre" @click="newBook()">
                     <b-icon-plus-circle></b-icon-plus-circle> Agregar libro
                </b-button>
            </b-col>
        </b-row>
        <b-table :items="books" :fields="fields" ref="table"
            :per-page="perPage" :current-page="currentPage">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(actions)="data">
                <b-button pill variant="warning" class="text-white"
                    @click="editBook(data.index, data.item)">
                    <b-icon-pencil-square></b-icon-pencil-square>
                </b-button>
                <b-button :disabled="data.item.schools_count > 0"
                    pill variant="danger" class="text-white"
                    @click="deleteBook(data.index, data.item)">
                    <b-icon-x></b-icon-x>
                </b-button>
            </template>
            <template v-slot:cell(assign)="data">
                <b-button pill variant="secondary" @click="assignBook(data.item)">
                    <b-icon-plus-circle></b-icon-plus-circle>
                </b-button>
            </template>
            <template v-slot:cell(show)="data">
                <b-button pill id="btnPre" @click="showSchools(data.item)">
                    <b-icon-info-circle-fill></b-icon-info-circle-fill>
                </b-button>
            </template>
        </b-table>

        <!-- MODALS -->
        <b-modal ref="modal-book" hide-footer title="">
            <new-edit-book :book="book" :edit="edit" @updateBooks="updateBooks">
            </new-edit-book>
        </b-modal>
        <b-modal ref="modal-assign" hide-footer :title="book_name">
            <b-form @submit.prevent="saveAssign">
                <b-form-group label="Plantel:">
                    <b-form-select v-model="form.school_id"
                        :options="schools" required :disabled="load"
                    ></b-form-select>
                </b-form-group>
                <b-form-group label="Precio del libro">
                    <b-form-input v-model="form.price"
                        required type="number" :disabled="load"
                    ></b-form-input>
                </b-form-group>
                <div class="text-right">
                    <b-button pill :disabled="load" id="btnPre" type="submit">
                        <b-icon-plus-circle></b-icon-plus-circle> Guardar
                    </b-button>
                </div>
            </b-form>
        </b-modal>
        <b-modal ref="modal-show" hide-footer :title="book_name" size="lg">
            <div v-if="ePrice" class="mb-3">
                <h6><b>Editar</b></h6>
                <b-form @submit.prevent="savePrice">
                    <b-row>
                        <b-col>
                            <label>{{ eBook.name }}</label>
                        </b-col>
                        <b-col sm="4">
                            <b-input type="number" v-model="eBook.price"></b-input>
                        </b-col>
                    </b-row>
                    <div class="text-right mt-1">
                        <b-button pill :disabled="load" id="btnPre" type="submit">
                            <b-icon-plus-circle></b-icon-plus-circle> Guardar
                        </b-button>
                    </div>
                </b-form>
            </div>
            <b-alert
                :show="dismissCountDown" dismissible variant="success"
                @dismissed="dismissCountDown=0"
                @dismiss-count-down="countDownChanged">
                <b-icon-check></b-icon-check> El precio se actualizo correctamente
            </b-alert>
            <b-table v-if="schools.length > 0" :items="schools" :fields="fieldsS">
                <template v-slot:cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template v-slot:cell(price)="data">
                    ${{ data.item.pivot.price | numeral('0,0') }}
                </template>
                <template v-slot:cell(edit)="data">
                    <b-button pill variant="warning" class="text-white"
                        @click="editPrice(data.index, data.item)">
                        <b-icon-pencil-square></b-icon-pencil-square>
                    </b-button>
                </template>
            </b-table>
            <b-alert v-else show variant="dark" class="text-center">
                <b-icon-info-circle></b-icon-info-circle> No se han asignado escuelas
            </b-alert>
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
            books: this.registers,
            currentPage: 1,
            perPage: 25,
            fields: [
                {key:'index', label:'N.'},
                {key:'name', label:'Libro'},
                {key:'editorial', label:'Editorial'},
                {key:'actions', label:'Editar / Eliminar'},
                {key:'show', label:'Escuelas'},
                {key:'assign', label:'Asignar escuela'}
            ],
            book: { id: null, name: '', editorial: null },
            edit: false,
            position: null,
            load: false,
            schools: [],
            form: { book_id: null, school_id: null, price: null },
            fieldsS: [
                {key:'index', label:'N.'},
                {key:'name', label:'Plantel'},
                {key:'price', label:'Precio'},
                {key:'edit', label:'Editar'},
            ],
            ePrice: false,
            eBook: { school_id: null, book_id: null, name: '', price: null },
            editoriales: [],
            dismissCountDown: 0,
            book_name: ''
        }
    },
    methods: {
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        newBook(){
            this.book = { id: null, name: '', editorial: null };
            this.edit = false;
            this.$refs['modal-book'].show();
        },
        editBook(position, book){
            this.book = { id: book.id, name: book.name, editorial: book.editorial };
            this.position = position;
            this.edit = true;
            this.$refs['modal-book'].show();
        },
        updateBooks(book){
            if(!this.edit){
                this.books.unshift(book);
                swal("Guardado", "El libro se guardo correctamente.", "success");
            } else {
                this.books[this.position].name = book.name;
                this.books[this.position].editorial = book.editorial;
                this.$refs.table.refresh();
                swal("Actualizado", "El libro se actualizo correctamente.", "success");
            }
            this.$refs['modal-book'].hide();
        },
        assignBook(book){
            this.schools = [];
            axios.get('/schools/get_schools', {params: {book_id: book.id}}).then(response => {
                this.book_select(response.data);
                this.form.book_id = book.id;
                this.book_name = book.name;
                this.$refs['modal-assign'].show();
            }).catch(error => {
                // PENDIENTE
            });
        },
        book_select(schools){
            this.schools.push({
                value: null,
                text: 'Selecciona una opción',
                disabled: true
            });
            schools.forEach(school => {
                this.schools.push({
                    value: school.id,
                    text: `${school.name}`
                });
            });
        },
        saveAssign(){
            this.load = true;
            axios.post('/books/assign_book', this.form).then(response => {
                this.form = { book_id: null, school_id: null, price: null };
                this.load = false;
                this.$refs['modal-assign'].hide();
                swal("Guardado", "El libro se asignó correctamente.", "success");
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al asignar el libro, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        showSchools(book){
            this.schools = [];
            this.ePrice = false;
            axios.get('/books/get_schools', {params: {book_id: book.id}}).then(response => {
                this.schools = response.data;
                this.book_name = book.name;
                this.$refs['modal-show'].show();
            }).catch(error => {
                // PENDIENTE
            });
        },
        editPrice(position, book){
            this.eBook = { 
                school_id: book.pivot.school_id, 
                book_id: book.pivot.book_id, name: book.name, 
                price: book.pivot.price };
            this.position = position;
            this.ePrice = true;
        },
        deleteBook(position, book){
            this.load = true;
            axios.delete('/books/delete', {params: {book_id: book.id}}).then(response => {
                this.books.splice(position, 1);
                this.load = false;
                swal("Eliminado", "El libro se elimino correctamente.", "success");
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al eliminar el libro, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        savePrice(){
            this.load = true;
            axios.put('/books/update_price', this.eBook).then(response => {
                this.schools = response.data;
                this.ePrice = false;
                this.dismissCountDown = 5;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                swal("Ocurrió un problema", "Ocurrió un problema al actualizar el precio, por favor verifica tu conexión a internet e intenta de nuevo. Si el error persiste refresca la pagina y vuelve acceder al sistema.", "warning");
            });
        },
        showBooks(){
            axios.get('/books/show_books', {params: {book: this.book_name}}).then(response => {
                this.books = response.data;
            }).catch(error => {
                // PENDIENTE
            });
        }
    }
}
</script>