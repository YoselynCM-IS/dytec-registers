<template>
    <div>
        <!-- FORM -->
        <form @submit="onSubmit" enctype="multipart/form-data" method="POST">
            <!-- Datos del alumno -->
            <div>
                <h5><b>Datos personales</b></h5>
                <b-row>
                    <b-col>
                        <b-form-group>
                            <label>Nombre</label>
                            <b-form-input v-model="form.name" :disabled="load"
                                style="text-transform:uppercase;" required
                            ></b-form-input>
                            <div v-if="errors && errors.name" class="text-danger">
                                Nombre requerido, igual o mayor a 3 caracteres.
                            </div>
                        </b-form-group>
                        <b-form-group>
                            <label>Apellido paterno</label>
                            <b-form-input v-model="form.lastname" :disabled="load"
                                style="text-transform:uppercase;" required
                            ></b-form-input>
                            <div v-if="errors && errors.lastname" class="text-danger">
                                Apellido paterno requerido, igual o mayor a 3 caracteres.
                            </div>
                        </b-form-group>
                        <b-form-group>
                            <label>Apellido materno</label>
                            <b-form-input v-model="form.lastname2" :disabled="load"
                                style="text-transform:uppercase;" required
                            ></b-form-input>
                            <div v-if="errors && errors.lastname2" class="text-danger">
                                Apellido materno requerido, igual o mayor a 3 caracteres.
                            </div>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group>
                            <label>Correo electrónico</label>
                            <b-form-input v-model="form.email"
                                type="email" required :disabled="load"
                            ></b-form-input>
                            <div v-if="errors && errors.email" class="text-danger">
                                <b>Correo electrónico requerido.</b>
                            </div>
                        </b-form-group>
                        <b-form-group>
                            <label>Numero de teléfono</label>
                            <b-form-input v-model="form.telephone" :disabled="load" required
                                minlength="10" maxlength="10"
                            ></b-form-input>
                            <div v-if="errors && errors.telephone" class="text-danger">
                                Numero de teléfono requerido, igual a 10 digitos.
                            </div>
                        </b-form-group>
                    </b-col>
                </b-row>
                <hr>
            </div>
            <!-- Datos de la compra -->
            <div>
                <h5><b>Datos de la compra</b></h5>
                <b-row>
                    <b-col sm="6">
                        <b-form-group>
                            <label><b>Certificación</b></label>
                            <b-form-input v-model="queryBook" placeholder="Buscar..."
                                @keyup="showBooks()" style="text-transform:uppercase;">
                            </b-form-input>
                            <div class="list-group" v-if="books.length" id="listR">
                                <a 
                                    href="#" v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(book, i) in books" 
                                    @click="selectBook(book)">
                                    {{ book.name }}
                                </a>
                            </div>
                        </b-form-group>
                        <label>{{ form.school }}</label>
                    </b-col>
                    <b-col sm="4">
                        <b-row class="mt-3">
                            <b-col class="text-right"><label>Cantidad</label></b-col>
                            <b-col sm="5">
                                <b-form-input v-model="form.quantity" @change="setQuantity()"
                                    required type="number" :disabled="load || selBook"
                                ></b-form-input>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col class="text-right"><label>Precio</label></b-col>
                            <b-col sm="5">${{ form.price | numeral('0,0') }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col class="text-right"><label>Total</label></b-col>
                            <b-col sm="5">${{ form.a_depositar | numeral('0,0') }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <hr>
                <b-alert variant="secondary" show>
                    <i class="fa fa-info-circle"></i> Los datos que ingresaras a continuación debes obtenerlos de tu orden de compra y comprobante de pago.
                </b-alert>
                <h5><b>Comprobante(s) de pago</b></h5>
                <b-row>
                    <b-col sm="5">
                        <b-form-group>
                            <label>Número de cuenta al que se realizó el deposito</label>
                            <b-form-input v-model="form.cuenta" :disabled="load || statusCuenta"
                                type="number" required
                            ></b-form-input>
                            <!-- NUMERO DE CUENTA AL CUAL SE DEPOSITO -->
                            <!-- <b-button class="mt-4" @click="checkCuenta()" pill
                                id="btnPre" block :disabled="load || statusCuenta">
                                <b-icon-arrow-right-circle-fill></b-icon-arrow-right-circle-fill> Continuar
                            </b-button> -->
                        </b-form-group>
                    </b-col>
                    <b-col sm="5">
                        <b-form-group>
                            <label>Número de orden</label>
                            <b-form-input v-model="form.orden" :disabled="load"
                                type="number" required
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col sm="2" class="text-right">
                        <b-button class="mt-4" id="btnAddPago" :disabled="load" @click="addComprobante()" pill block>
                            <b-icon-plus-circle-fill></b-icon-plus-circle-fill> Agregar pago
                        </b-button>
                    </b-col>
                </b-row>
                <div v-for="(comprobante, i) in form.comprobantes" v-bind:key="i">
                    <b-row>
                        <b-col><label><b>Pago {{ i + 1 }}</b></label></b-col>
                        <b-col sm="2" class="text-right" v-if="i > 0">
                            <b-button id="btnDeletePago" pill @click="deleteComprobante(i)" block>
                                <b-icon-dash-circle-fill></b-icon-dash-circle-fill> Eliminar
                            </b-button>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-form-group>
                                <label>Tipo de pago</label>
                                <b-form-select v-model="comprobante.type" :disabled="load"
                                    :options="typesCompleto" required>
                                </b-form-select>
                            </b-form-group>
                        </b-col>
                        <b-col>
                            <b-form-group>
                                <label>Fecha de pago</label>
                                <b-form-datepicker required :disabled="load" v-model="comprobante.date"></b-form-datepicker>
                            </b-form-group>
                        </b-col>
                        <b-col>
                            <b-form-group v-if="comprobante.type == 'practicaja' || comprobante.type == 'ventanilla'" 
                                id="tooltip-target-numero">
                                <label>{{ `Número de ${comprobante.type == 'practicaja' ? 'cajero' : 'sucursal'}` }}</label>
                                <b-form-input v-model="comprobante.cajero" minlength="4" maxlength="4"
                                    :disabled="load" required
                                ></b-form-input>
                                <b-tooltip target="tooltip-target-numero" triggers="hover">
                                    <p v-if="comprobante.type == 'ventanilla'">En tu comprobante aparece como <b>SUCURSAL</b> debajo de <b>FECHA / HORA</b>.</p>
                                    <p v-if="comprobante.type == 'practicaja'">En tu comprobante aparece como <b>CAJERO</b> en la parte superior, a lado de <b>FECHA HORA</b>.</p>
                                </b-tooltip>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-form-group v-if="comprobante.type == 'practicaja'"
                                v-b-tooltip.hover title="Ingresar los cuatro números que aparecen en FOLIO en tu comprobante">
                                <label>Folio</label>
                                <b-form-input v-model="comprobante.folio" minlength="4" maxlength="4"
                                    :disabled="load" required
                                ></b-form-input>
                            </b-form-group>
                            <b-form-group v-if="comprobante.type == 'ventanilla'" 
                                v-b-tooltip.hover title="Ingresar lo que aparece en MOVIMIENTO en tu comprobante de pago">
                                <label>Movimiento</label>
                                <b-form-input v-model="comprobante.folio" minlength="5"
                                    :disabled="load" required
                                ></b-form-input>
                            </b-form-group>
                            <div v-if="comprobante.type === 'transferencia'">
                                <b-form-group v-b-tooltip.hover title="Desde el cual se realizó el pago">
                                    <label>Banco</label>
                                    <b-form-select v-model="comprobante.bank" :disabled="load"
                                        :options="banks" required
                                    ></b-form-select>
                                </b-form-group>
                                <b-input v-if="comprobante.bank === 'OTRO'" v-model="specifyBank" 
                                    v-b-tooltip.hover title="Desde el cual se realizó el pago"
                                    placeholder="Nombre del banco" required style="text-transform:uppercase;"
                                    @keyup="posicion = i">
                                </b-input>
                            </div>
                        </b-col>
                        <b-col>
                            <b-form-group v-if="comprobante.type == 'practicaja'">
                                <label>Autorización</label>
                                <b-form-input v-model="comprobante.auto" :disabled="load"
                                    style="text-transform:uppercase;" required
                                ></b-form-input>
                            </b-form-group>
                            <b-form-group v-if="comprobante.type == 'ventanilla'">
                                <label>Referencia</label>
                                <b-form-input v-model="comprobante.auto" :disabled="load"
                                    style="text-transform:uppercase;" required  minlength="4"
                                ></b-form-input>
                            </b-form-group>
                            <div v-if="comprobante.type === 'transferencia'">
                                <b-form-group v-if="comprobante.bank === 'BANCOMER'">
                                    <label>Folio</label>
                                    <b-form-input v-model="comprobante.folio" type="text" minlength="8" maxlength="10"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="comprobante.bank === 'BANCOPPEL'"
                                    v-b-tooltip.hover title="En tu comprobante puede aparecer como Folio de operación o Clave de rastreo">
                                    <label>Folio de operación</label>
                                    <b-form-input v-model="comprobante.folio" type="text" minlength="24" maxlength="24"
                                        :disabled="load" required style="text-transform:uppercase;"
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="comprobante.bank !== null && comprobante.bank !== 'BANCOPPEL' && comprobante.bank !== 'BANCOMER'" 
                                    v-b-tooltip.hover title="En tu comprobante puede aparecer como Referencia, Referencia numérica o Numero de referencia">
                                    <label>Referencia</label>
                                    <b-form-input v-model="comprobante.folio" minlength="4"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                            </div>
                        </b-col>
                        <b-col>
                            <div v-if="comprobante.type === 'transferencia' && comprobante.bank !== null">
                                <b-form-group v-if="comprobante.bank === 'BANCOMER'">
                                    <label>Motivo de pago</label>
                                    <b-form-input v-model="comprobante.auto" type="text" minlength="3"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="comprobante.bank !== 'BANCOMER'"
                                    v-b-tooltip.hover title="En tu comprobante puede aparecer como Concepto, Concepto de pago o Concepto de tranferencia. Te solicitamos escribirlo tal y como aparece (ya sean letras mayúsculas, minúsculas y/o números)">
                                    <label>Concepto</label>
                                    <b-form-input v-model="comprobante.auto" type="text" minlength="3"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col sm="4">
                            <b-form-group v-b-tooltip.hover title="En tu comprobante puede aparecer como Importe, Monto o Cantidad">
                                <label>Importe</label>
                                <b-form-input v-model="comprobante.total" :disabled="load"
                                    required type="number" step="0.01" min="1" 
                                ></b-form-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                </div>
                <b-row class="mt-3">
                    <b-col sm="4">
                        <input :disabled="load" type="file" id="archivoType" 
                            v-on:change="fileChange" required>
                        <label for="archivoType"><b-icon-upload></b-icon-upload> Subir comprobante</label>
                        <p v-if="form.file">
                            Comprobante: <b>{{ form.file.name }}</b>
                        </p>
                        <div v-if="errors && errors.file" class="text-danger">
                            Comprobante requerido, con un tamaño máximo de 3MB y solo en formato jpg, png, jpeg ó pdf
                        </div>
                    </b-col>
                    <b-col>
                        <b-alert show variant="secondary">
                            <ul>
                                <li>Si realizaste más de un pago, sube la foto / archivo donde aparezcan los comprobantes.</li>
                                <li>Solo formato: <b>.jpg</b> / <b>.png</b> / <b>.jpeg</b> / <b>.pdf</b></li>
                                <li>Tamaño máximo: <b>3 MB</b></li>
                            </ul>
                        </b-alert>
                    </b-col>
                </b-row>
            </div>
            
            <!-- GUARDAR -->
            <div>
                <b-row class="mt-3">
                    <b-col>
                        <b-alert show variant="light">
                            <p>
                                Antes de guardar tu registro de pago, verifica que los datos que ingresaste sean correctos, de lo contrario no podremos validar tu pago.
                            </p>
                        </b-alert>
                    </b-col>
                    <b-col class="text-right" sm="2">
                        <b-button class="mt-3" pill block :disabled="load" type="submit" id="btnPre">
                            <i class="fa fa-check-circle"></i> Guardar
                        </b-button>
                    </b-col>
                </b-row>
                <b-alert v-if="load" show variant="primary" class="text-center">
                    <p>
                        <b-spinner type="grow" label="Loading..."></b-spinner> 
                        Por favor no cierres esta página, tus datos están siendo guardados.
                    </p>
                </b-alert>
            </div>
        </form>
    </div>
</template>

<script>
// SWEETALERT
import swal from 'sweetalert';
import banksMixin from '../../mixins/banksMixin';
import booksMixin from '../../mixins/booksMixin';
import typesMixin from '../../mixins/typesMixin';
export default {
    props: ['registers', 'tipo'],
    mixins: [banksMixin,booksMixin,typesMixin],
    data(){
        return {
            // csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            load: false,
            types_externa: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'oxxo', text: 'OXXO'}
            ],
            specifyBank: null,
            form: {
                name: '', lastname: '', lastname2: '', email: '',
                telephone: null, school_id: null, school: null, book: null,
                quantity: 1, price: 0, a_depositar: 0,
                file: null,
                comprobantes: [{
                    type: null, folio: '', auto: '', clave: null, 
                    bank: null, total: null, date: null, plaza: '', cajero: ''
                }],
                orden: null, cuenta: null
            },
            optSchools: this.registers,
            schools: [],
            errors: {},
            selSchool: true,
            books: [],
            selBook: true,
            valueBook: null, //POSIBLEMENTE ELIMINAR
            queryBook: null,
            position: null,
            a_depositar: null,
            cuenta: null,
            statusCuenta: false,
            errors: {},
            posicion: null,
            bancomer1: '0172427206',
            bancomer2: '012180001724272063',
            banamex1: '5204165073723995',
            banamex2: '002180701156103586',
            banco_azteca1: '09330153687444',
            banco_azteca2: '5343810206998814',
            bancoppel1: '19000207503',
            bancoppel2: '4169160498193532',
            nctame: '0189525114'
        }
    },
    filters: {
        
    },
    created: function(){
        this.schools.push({
            value: null,
            text: 'Selecciona una opción',
            disabled: true
        });
        this.optSchools.forEach(school => {
            this.schools.push({
                value: school.id,
                text: `${school.name}`
            });
        });
    },
    methods: {
        fileChange(e){
            var fileInput = document.getElementById('archivoType');
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.pdf)$/i;
            
            if(allowedExtensions.exec(fileInput.value)){
                this.form.file = e.target.files[0];  
            } else {
                swal("Revisar formato de archivo", "Formato de archivo no permitido, solo puede ser en formato imagen (jpg, jpeg, png)", "warning");
            }
        },
        onSubmit(e){
            e.preventDefault();
            this.load = true;
            this.acum_total();

            if (this.a_depositar >= this.form.a_depositar) {
                if (this.form.file) {
                    this.save_all();
                } else {
                    swal("Comprobante(s) de pago", "Subir foto / archivo de comprobante(s) de pago.", "warning");
                    this.load = false;
                }
            } else {
                swal("Comprobante(s) de pago", "El total de pagos registrados tiene que ser igual o mayor al total de tu compra.", "warning");
                this.load = false;
            }
        },
        save_all(){
            let fd = this.attributes();
            console.log(fd);
            axios.post('/student/preregister', fd).then(response => {
                if(response.data === 3){
                    swal("Listo", "Tus datos se guardaron correctamente. Aproximadamente en un lapso de 48 a 72 horas hábiles te haremos llegar un correo electrónico donde te notificaremos si tu registro de pago ha sido validado. Gracias.", "success")
                        .then((value) => {
                            location.href = '/student/register';
                        });
                }
                if(response.data === 1) {
                    swal("Proceso", "Tienes un registro que continua en proceso para ser validado. Aproximadamente en un lapso de 48 a 72 horas hábiles te haremos llegar un correo electrónico donde te notificaremos si tu registro de pago ha sido validado. Gracias.", "info");
                } 
                if(response.data === 2) {
                    swal("Aceptado", "Tu registro ya ha sido validado y fue aceptado, si no te llego un correo electrónico, por favor contáctate al 56 2741 1481 o al 56 2741 0930.", "info");
                }
                this.errors = {};
                this.load = false;
            }).catch(error => {
                if(error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    swal("Revisa tus datos", "Revisa que todos tus datos sean correctos e intenta guardar de nuevo.", "warning");
                } else {
                    swal("Ocurrió un problema", "Intenta guardar de nuevo tu registro.", "warning");
                }
                this.load = false;
            });
        },
        attributes(){
            let formData = new FormData();
            if(this.specifyBank !== null) this.form.comprobantes[this.posicion].bank = this.specifyBank.toUpperCase();
            formData.append('name', this.form.name);
            formData.append('lastname', this.form.lastname);
            formData.append('lastname2', this.form.lastname2);
            formData.append('email', this.form.email);
            formData.append('telephone', this.form.telephone);
            formData.append('school_id', this.form.school_id);
            formData.append('school', this.form.school); 
            formData.append('book', this.form.book);
            formData.append('quantity', this.form.quantity);
            formData.append('price', this.form.price);
            formData.append('a_depositar', this.form.a_depositar);
            formData.append('file', this.form.file);
            formData.append('teacher', this.form.teacher);
            formData.append('group', this.form.group);
            formData.append('numcuenta', this.form.cuenta);
            formData.append('orden', this.form.orden);
            formData.append('comprobantes', JSON.stringify(this.form.comprobantes));
            
            return formData;
        },
        selectPlantel(){
            this.selSchool = true;
            axios.get('/schools/get_books', {params: {school_id: this.form.school}}).then(response => {
                this.form.book = null;
                this.school_select(response.data);
                this.selSchool = false;
            }).catch(error => {
                this.selSchool = false;
            });
        },
        showBooks() {
            if (this.queryBook.length > 0) {
                axios.get('/books/show_books', { params: { book: this.queryBook } }).then(response => {
                    this.books = response.data;
                }).catch(error => {
                    this.books = [];
                });
            } else {
                this.books = [];
            }
        },
        selectBook(book) {
            this.form.school_id = book.schools[0].id;
            this.form.school = book.schools[0].name;
            this.form.price = book.schools[0].pivot.price;
            //PENDIENTE, CAMBIAR YA QUE LA RELACION ES MUCHOS A MUCHOS
            this.form.book = book.name;
            this.form.quantity = 1;
            this.form.a_depositar = parseFloat(this.form.price) * parseInt(this.form.quantity);
            this.selBook = false;
            this.queryBook = book.name;
            this.books = [];
        },
        addComprobante(){
            this.form.comprobantes.push({
                type: null, folio: '', auto: '', clave: null,
                bank: null, total: null, date: null, plaza: '', file: null, cajero: ''
            });
        },
        setQuantity(){
            if(parseInt(this.form.quantity) < 1) this.form.quantity = 1;

            this.form.a_depositar = parseFloat(this.form.price) * parseInt(this.form.quantity);
        },
        acum_total(){
            this.a_depositar = 0;
            this.form.comprobantes.forEach(comprobante => {
                this.a_depositar += parseFloat(comprobante.total);
            });
        },
        deleteComprobante(i){
            this.form.comprobantes.splice(i, 1);
        },
        deleteFile(i){
            this.form.files.splice(i, 1);
        },
        checkCuenta(){
            // 0172427206
            if(this.cuenta === this.bancomer1 || this.cuenta === this.bancomer2 || 
                this.cuenta === this.banamex1 || this.cuenta === this.banamex2 ||
                this.cuenta === this.bancoppel1 || this.cuenta === this.banco_azteca1 ||
                this.cuenta === this.bancoppel2 || this.cuenta === this.banco_azteca2 || this.cuenta === this.nctame) {
                    this.statusCuenta = true;
            } 
            else {
                this.statusCuenta = false;
                swal("Numero de cuenta incorrecto", "El numero de cuenta que ingresaste no corresponde al nuestro.", "error");
            }

            if(this.cuenta === this.bancoppel1 || this.cuenta === this.bancoppel2) this.set_comprobante1('BANCOPPEL');
            // if(this.cuenta === this.banco_azteca1 || this.cuenta === this.banco_azteca2) this.set_comprobante1('BANCOAZTECA');
        },
        set_comprobante1(type_bank){
            this.form.comprobantes[0].type = type_bank;
            this.form.comprobantes[0].bank = type_bank;
            this.form.comprobantes[0].folio = 'REV. MANUAL ME';
            this.form.comprobantes[0].auto = 'REV. MANUAL ME';
            this.form.comprobantes[0].clave = 'REV. MANUAL ME';
            this.form.comprobantes[0].total = 0;
            this.form.comprobantes[0].date = '2021-09-09';
            this.form.comprobantes[0].plaza = 'REV. MANUAL ME';
            this.form.comprobantes[0].file = null;
        },
        validate_cta(){
            if(this.cuenta === this.banamex1 || this.cuenta === this.banamex2) return false
            return true;
        }
    }
}
</script>