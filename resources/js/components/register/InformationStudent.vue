<template>
    <div>
        <p><b>Fecha de registro:</b> {{ student.created_at | moment("YYYY-MM-DD hh:mm:ss") }}</p>
        <b-row>
            <b-col>
                <table class="table">
                    <tbody>
                        <tr class="table-info">
                            <th colspan="2"><h6><b>Datos de pago</b></h6></th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr class="table-ligth">
                            <th colspan="6"><h6><b>Número de cuenta al que se realizó el deposito:</b> {{ student.numcuenta }}</h6></th>
                        </tr>
                    </tbody>
                    <tbody v-for="(registro, i) in student.registros" v-bind:key="i">
                        <tr class="table-ligth">
                            <th colspan="2"><h6><b>Pago: {{ i + 1 }}</b></h6></th>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Fecha de pago</th>
                            <td>{{ registro.date }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Tipo de pago</th>
                            <td>{{ registro.type }}</td>
                        </tr>
                        <tr v-if="registro.bank !== null">
                            <th class="text-right" scope="row">Banco</th>
                            <td>{{ registro.bank }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">
                                <label v-if="registro.type === 'ventanilla'">Movimiento</label>
                                <label v-if="registro.type === 'practicaja' || 
                                        (registro.type == 'transferencia' && registro.bank == 'BANCOMER')">
                                    Folio
                                </label>
                                <label v-if="registro.type == 'transferencia' && registro.bank != 'BANCOMER'">
                                    Referencia
                                </label>
                                <label v-if="registro.type === 'BANCO AZTECA'">No. Operación</label>
                            </th>
                            <td>{{ registro.invoice }}</td>
                        </tr>
                        <tr v-if="registro.type == 'practicaja'">
                            <th class="text-right" scope="row">Autorización</th>
                            <td>{{ registro.auto }}</td>
                        </tr>
                        <tr v-if="registro.type == 'ventanilla'">
                                <th class="text-right" scope="row">Referencia</th>
                                <td>{{ registro.auto }}</td>
                            </tr>
                        <tr v-if="registro.type == 'BANCO AZTECA'">
                            <th class="text-right" scope="row">Número de autorización</th>
                            <td>{{ registro.auto }}</td>
                        </tr>
                        <tr v-if="registro.type == 'transferencia'">
                            <th class="text-right" scope="row">Concepto</th>
                            <td>{{ registro.auto }}</td>
                        </tr>
                        <tr v-if="registro.type == 'practicaja' || registro.type == 'ventanilla'">
                            <th class="text-right" scope="row">Número de {{registro.type == 'practicaja' ? 'cajero':'sucursal'}}</th>
                            <td>{{ registro.cajero }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Total depositado</th>
                            <td>${{ registro.total | numeral('0,0[.]00') }}</td>
                        </tr>
                        <tr v-if="registro.type !== 'transferencia'">
                            <th class="text-right" scope="row">Lugar donde se<br>realizo el pago</th>
                            <td>{{ registro.plaza }}</td>
                        </tr>
                        <tr v-if="student.check === 'accepted' && registro.folio">
                            <th class="text-right" scope="row">Referencia</th>
                            <td>
                                <label><b>Fecha:</b> {{ registro.folio.fecha }}</label><br>
                                <label><b>Concepto:</b> {{ registro.folio.concepto }}</label><br>
                                <label><b>Abono:</b> ${{ registro.folio.abono | numeral('0,0[.]00') }}</label><br>
                                <label><b>Saldo:</b> ${{ registro.folio.saldo | numeral('0,0[.]00') }}</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <table class="table">
                    <tbody> 
                        <tr class="table-info">
                            <th colspan="2"><h6><b>Comprobante(s) del pago</b></h6></th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr v-for="(comprobante, i) in student.comprobantes" v-bind:key="i">
                            <th class="text-right" scope="row">Comprobante {{ i + 1 }}</th>
                            <td><a :href="comprobante.public_url" target="_blank">Ver</a></td>
                        </tr>
                    </tbody>
                </table>
            </b-col>
            <b-col>
                <table class="table">
                    <tbody>
                        <tr class="table-info">
                            <th colspan="2"><h6><b>Datos del libro</b></h6></th>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Libro</th>
                            <td>{{ student.book }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Cantidad</th>
                            <td>{{ student.quantity }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Precio</th>
                            <td>${{ student.price | numeral('0,0[.]00') }}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <table class="table">
                    <tbody>
                        <tr class="table-info">
                            <th colspan="2"><h6><b>Datos del alumno</b></h6></th>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Nombre</th>
                            <td>{{ student.name }}</td>
                        </tr>
                        <tr v-if="student.telephone !== null">
                            <th class="text-right" scope="row">Número de teléfono</th>
                            <td>{{ student.telephone }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Correo</th>
                            <td>{{ student.email }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Escuela</th>
                            <td>{{ student.school.name }}</td>
                        </tr>
                        <tr v-if="student.school.id == 29 && student.book.includes('HORIZONTES')">
                            <th class="text-right" scope="row">Nombre del maestro</th>
                            <td>{{ student.teacher }}</td>
                        </tr>
                        <tr v-if="student.school.id == 29 && student.book.includes('HORIZONTES')">
                            <th class="text-right" scope="row">Grupo</th>
                            <td>{{ student.group }}</td>
                        </tr>
                    </tbody>
                </table>
            </b-col>
        </b-row>
    </div>
</template>

<script>
export default {
    props: ['student']
}
</script>

<style>

</style>