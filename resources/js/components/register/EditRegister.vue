<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-form-group label="Nombre:">
                <b-form-input v-model="form.name" :disabled="load"
                    style="text-transform:uppercase;" required
                ></b-form-input>
                <div v-if="errors && errors.name" class="text-danger">
                    El campo Nombre es requerido y tiene que ser igual o mayor a 6 caracteres.
                </div>
            </b-form-group>
            <b-row>
                <b-col>
                    <b-form-group label="Correo electrónico:">
                        <b-form-input v-model="form.email"
                            type="email" required :disabled="load"
                        ></b-form-input>
                        <div v-if="errors && errors.email" class="text-danger">
                            <b>El campo Correo electrónico es requerido.</b>
                        </div>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group label="Numero de teléfono">
                        <b-form-input v-model="form.telephone" :disabled="load" required
                            minlength="10" maxlength="10"
                        ></b-form-input>
                        <div v-if="errors && errors.telephone" class="text-danger">
                            El campo Numero de teléfono es requerido y tiene que ser igual a 10 digitos.
                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-form-group label="Plantel:">
                        <b-form-select v-model="form.school"
                            :options="schools" @change="selectPlantel()"
                            required :disabled="load"
                        ></b-form-select>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-form-group label="Libro:">
                        <b-form-select v-model="valueBook" @change="selectBook()"
                            :options="books" required :disabled="load"
                        ></b-form-select>
                    </b-form-group>
                </b-col>
                <b-col sm="2">
                    <b-form-group label="Numero de piezas">
                        <b-form-input v-model="form.quantity" @change="setQuantity()"
                            required type="number" :disabled="load"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="2">
                    <b-form-group label="Precio del libro">
                        ${{ form.price | numeral('0,0') }}
                    </b-form-group>
                </b-col>
                <b-col sm="2">
                    <b-form-group label="Total">
                        ${{ form.a_depositar | numeral('0,0') }}
                    </b-form-group>
                </b-col>
            </b-row>
            <div v-if="userid == 7 || userid == 3 || userid == 15 || userid == 17">
                <hr>
                <!-- PAGOS -->
                <b-row>
                    <b-col><h5><b>Datos del pago</b></h5></b-col>
                    <b-col class="text-right">
                        <b-button variant="light" :disabled="load" @click="addComprobante()" pill>
                            <b-icon-plus-circle-fill></b-icon-plus-circle-fill> Agregar otro pago
                        </b-button>
                    </b-col>
                </b-row>
                <div v-for="(comprobante, i) in form.comprobantes" v-bind:key="i">
                    <b-row>
                        <!-- TIPO DE PAGO -->
                        <b-col>
                            <b-form-group v-if="form.school != 52"
                                label="Tipo de pago:">
                                <b-form-select v-model="comprobante.type" :disabled="load"
                                    :options="typesCompleto" required>
                                </b-form-select>
                            </b-form-group>
                            <b-form-group v-else label="Tipo de pago:">
                                <b-form-select v-model="comprobante.type" :disabled="load"
                                    :options="typesTamazunchale" required>
                                </b-form-select>
                            </b-form-group>
                        </b-col>
                        <!-- SELECCIONAR BANCO -->
                        <b-col v-if="comprobante.type == 'transferencia'">
                            <b-form-group label="Banco:" v-b-tooltip.hover title="Desde el cual se realizó el pago">
                                <b-form-select v-model="comprobante.bank" :disabled="load"
                                    :options="banks" required
                                ></b-form-select>
                            </b-form-group>
                            <b-input v-if="comprobante.bank === 'OTRO'" v-model="specifyBank" 
                                v-b-tooltip.hover title="Por favor escribe el nombre del banco"
                                placeholder="Nombre del banco" required style="text-transform:uppercase;"
                                @keyup="posicion = i">
                            </b-input>
                        </b-col>
                        <!-- FOLIO, REFERENCIA -->
                        <b-col>
                            <div v-if="comprobante.type !== 'transferencia'">
                                <b-form-group v-if="comprobante.type == 'practicaja'" label="Folio" 
                                    v-b-tooltip.hover title="Ingresar los cuatro números que aparecen en FOLIO en tu comprobante">
                                    <b-form-input v-model="comprobante.invoice" minlength="4" maxlength="4"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="comprobante.type == 'ventanilla'" label="Movimiento" 
                                    v-b-tooltip.hover title="Ingresar lo que aparece en MOVIMIENTO en tu comprobante de pago">
                                    <b-form-input v-model="comprobante.invoice" minlength="5"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                            </div>
                            <div v-if="comprobante.type === 'transferencia' && comprobante.bank !== null">
                                <b-form-group v-if="comprobante.bank === 'BANCOMER'" label="Folio">
                                    <b-form-input v-model="comprobante.invoice" type="text" minlength="8" maxlength="10"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="comprobante.bank === 'BANCOPPEL'" label="Folio de operación"
                                    v-b-tooltip.hover title="En tu comprobante puede aparecer como Folio de operación o Clave de rastreo">
                                    <b-form-input v-model="comprobante.invoice" type="text" minlength="24" maxlength="24"
                                        :disabled="load" required style="text-transform:uppercase;"
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="comprobante.bank !== 'BANCOPPEL' && comprobante.bank !== 'BANCOMER'" 
                                    label="Referencia" v-b-tooltip.hover title="En tu comprobante puede aparecer como Referencia, Referencia numérica o Numero de referencia">
                                    <b-form-input v-model="comprobante.invoice" minlength="4"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                            </div>
                        </b-col>
                        <!-- CONCEPTO, AUTORIZACIÓN -->
                        <b-col>
                            <b-form-group v-if="comprobante.type == 'practicaja' || comprobante.type == 'oxxo'" label="Autorización">
                                <b-form-input v-model="comprobante.auto" :disabled="load"
                                    style="text-transform:uppercase;" required
                                ></b-form-input>
                            </b-form-group>
                            <div v-if="comprobante.type === 'transferencia' && comprobante.bank !== null">
                                <b-form-group v-if="comprobante.bank === 'BANCOMER'" label="Motivo de pago">
                                    <b-form-input v-model="comprobante.auto" type="text" minlength="3"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="comprobante.bank !== 'BANCOMER'" label="Concepto" 
                                    v-b-tooltip.hover title="En tu comprobante puede aparecer como Concepto, Concepto de pago o Concepto de tranferencia. Te solicitamos escribirlo tal y como aparece (ya sean letras mayúsculas, minúsculas y/o números)">
                                    <b-form-input v-model="comprobante.auto" type="text" minlength="3"
                                        :disabled="load" required
                                    ></b-form-input>
                                </b-form-group>
                            </div>
                        </b-col>
                    </b-row>
                    <!-- IMPORTE Y FECHA DE PAGO -->
                    <b-row>
                        <b-col>
                            <b-form-group label="Importe"
                                v-b-tooltip.hover title="En tu comprobante puede aparecer como Importe, Monto o Cantidad">
                                <b-form-input v-model="comprobante.total" :disabled="load"
                                    required type="number" step="0.01" min="1" 
                                ></b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col>
                            <b-form-group label="Fecha de pago"
                                title="En tu comprobante puede aparecer como Fecha de pago, Fecha y Hora, Fecha de aplicación o Fecha de operación">
                                <b-form-datepicker required :disabled="load" v-model="comprobante.date"></b-form-datepicker>
                            </b-form-group>
                        </b-col>
                    </b-row><hr>
                </div>
            </div>
            <b-row>
                <b-col>
                    
                </b-col>
                <b-col class="text-right">
                    <b-button variant="success" :disabled="load" pill type="submit">
                        <b-icon-check></b-icon-check> Guardar
                    </b-button>
                </b-col>
            </b-row>
        </b-form>
    </div>
</template>

<script>
import booksMixin from '../../mixins/booksMixin';
import typesMixin from '../../mixins/typesMixin';
import banksMixin from '../../mixins/banksMixin';

export default {
    props: ['optSchools', 'form', 'userid'],
    mixins: [booksMixin,typesMixin,banksMixin],
    data() {
        return {
            load: false,
            errors: {},
            schools: [], 
            valueBook: null,
            specifyBank: null,
            posicion: null
        }
    },
    mounted: function(){
        this.schools.push({
            value: null, text: 'Selecciona una opción', disabled: true
        });
        this.optSchools.forEach(school => {
            this.schools.push({
                value: school.id,
                text: `${school.name}`
            });
        });

        axios.get('/schools/get_books', {params: {school_id: this.form.school}}).then(response => {
            this.school_select(response.data);
            this.valueBook = response.data.find(r => r.name == this.form.book);
        }).catch(error => { });
    },
    methods: {
        selectPlantel(){
            axios.get('/schools/get_books', {params: {school_id: this.form.school}}).then(response => {
                this.form.book = null;
                this.school_select(response.data);
            }).catch(error => { });
        },
        selectBook(){
            this.form.book = this.valueBook.name;
            this.form.quantity = 1;
            this.form.price = this.valueBook.pivot.price;
            this.form.a_depositar = parseFloat(this.form.price) * parseInt(this.form.quantity);
        },
        setQuantity(){
            if(parseInt(this.form.quantity) < 1) this.form.quantity = 1;

            this.form.a_depositar = parseFloat(this.form.price) * parseInt(this.form.quantity);
        },
        onSubmit(){
            this.load = true;
            if(this.specifyBank !== null) this.form.comprobantes[this.posicion].bank = this.specifyBank.toUpperCase();
            axios.put('/student/update_preregister', this.form).then(response => {
                this.$emit('updated_student', response.data);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
                if(error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    swal("Revisar datos", "Por favor revisa que todos tus datos esten correctamente e intenta guardar de nuevo.", "warning");
                } 
            });
        },
        addComprobante(){
            this.form.comprobantes.push({
                id: null,
                student_id: this.form.id, type: null,
                invoice: '', auto: '',
                clave: null, total: null,
                date: null, bank: null
            });
        }
    }
}
</script>