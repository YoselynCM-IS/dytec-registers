<template>
    <div>
        <p><b>Fecha de registro:</b> {{ student.created_at | moment("YYYY-MM-DD hh:mm:ss") }}</p>
        <b-row>
            <b-col>
                <table class="table">
                    <tbody>
                        <tr class="table-primary">
                            <th colspan="2"><h6><b>Datos personales</b></h6></th>
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
                            <th class="text-right" scope="row">Correo electrónico</th>
                            <td>{{ student.email }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Plantel</th>
                            <td>{{ student.school.name }}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <table class="table">
                    <tbody>
                        <tr class="table-primary">
                            <th colspan="2"><h6><b>Datos de la compra</b></h6></th>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Certificación</th>
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
                        <tr>
                            <th class="text-right" scope="row">IVA 16%</th>
                            <td>${{ ((student.quantity * student.price) * 0.16) | numeral('0,0[.]00') }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Total</th>
                            <td>${{ student.total | numeral('0,0[.]00') }}</td>
                        </tr>
                    </tbody>
                </table>
            </b-col>
            <b-col>
                <table class="table">
                    <tbody>
                        <tr class="table-primary">
                            <th colspan="3"><h6><b>Comprobante(s) de pago</b></h6></th>
                        </tr>
                        <tr>
                            <th colspan="2"><h6><b>Número de cuenta / CLABE Interbancaria:</b></h6></th>
                            <td>{{ student.numcuenta }}</td>
                        </tr>
                        <tr v-if="student.numorden != 'null'">
                            <th colspan="2"><h6><b>Número de pedido:</b></h6></th>
                            <td>{{ student.numorden }}</td>
                        </tr>
                    </tbody>
                    <tbody v-for="(registro, i) in student.registros" v-bind:key="i">
                        <tr class="table-ligth">
                            <th colspan="2"><h6><b>Pago: {{ i + 1 }}</b></h6></th>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Fecha del deposito</th>
                            <td>{{ registro.date }}</td>
                        </tr>
                        <tr v-if="registro.bank !== null">
                            <th class="text-right" scope="row">Banco</th>
                            <td>{{ registro.bank }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Tipo de pago</th>
                            <td>{{ registro.type }}</td>
                        </tr>
                        <tr v-if="registro.type == 'practicaja' || registro.type == 'ventanilla'">
                            <th class="text-right" scope="row">Número de {{ registro.type == 'practicaja' ? 'cajero' : 'sucursal' }}</th>
                            <td>{{ registro.cajero }}</td>
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
                            </th>
                            <td>{{ registro.invoice }}</td>
                        </tr>
                        <tr v-if="registro.type == 'ventanilla'">
                            <th class="text-right" scope="row">Referencia</th>
                            <td>{{ registro.auto }}</td>
                        </tr>
                        <tr v-if="registro.type == 'practicaja'">
                            <th class="text-right" scope="row">Autorización</th>
                            <td>{{ registro.auto }}</td>
                        </tr>
                        <tr v-if="registro.type == 'transferencia'">
                            <th class="text-right" scope="row">Concepto</th>
                            <td>{{ registro.auto }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Importe depositado</th>
                            <td>${{ registro.total | numeral('0,0[.]00') }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" scope="row">Comprobante</th>
                            <td><a :href="student.comprobantes[0].public_url" target="_blank">Ver</a></td>
                        </tr>
                        <tr v-if="student.check === 'accepted' && registro.folio">
                            <th class="text-right" scope="row">Deposito vinculado</th>
                            <td>
                                <label><b>Fecha:</b> {{ registro.folio.fecha }}</label><br>
                                <label><b>Concepto:</b> {{ registro.folio.concepto }}</label><br>
                                <label><b>Abono:</b> ${{ registro.folio.abono | numeral('0,0[.]00') }}</label><br>
                                <label><b>Saldo:</b> ${{ registro.folio.saldo | numeral('0,0[.]00') }}</label>
                            </td>
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