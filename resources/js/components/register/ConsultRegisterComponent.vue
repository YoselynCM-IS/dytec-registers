<template>
    <div>
        <h4 class="text-left">
            <b>{{ student.check === 'accepted' ? 'Registro':'Pre-registro' }}</b>
        </h4>
        <b>Status:</b> 
        <b-badge v-if="student.check === 'accepted'" pill variant="success">
            <b-icon-check2-circle></b-icon-check2-circle> Aceptado
        </b-badge>
        <b-badge v-if="student.check === 'process'" pill variant="secondary">
            <b-icon-three-dots></b-icon-three-dots> Proceso
        </b-badge>
        <b-badge v-if="student.check === 'rejected'" pill variant="danger">
            <b-icon-x-circle-fill></b-icon-x-circle-fill> Rechazado
        </b-badge>
        <h6><b>Fecha de registro:</b> {{ student.created_at | moment("YYYY-MM-DD hh:mm:ss") }}</h6>
        <hr>
        <h5><b>Datos del alumno</b></h5>
        <ul type="none">
            <li><b>Nombre:</b> {{ student.name }}</li>
            <li><b>Correo electronico:</b> {{ student.email }}</li>
            <li><b>Escuela:</b> {{ student.school.name }}</li>
        </ul>
        <hr>
        <h5><b>Datos del libro</b></h5>
        <ul type="none">
            <li><b>Libro:</b> {{ student.registro.book }}</li>
            <li><b>Numero de piezas:</b> {{ student.registro.quantity }}</li>
            <li><b>Precio:</b> {{ student.registro.price }}</li>
        </ul>
        <hr>
        <h5><b>Datos del pago</b></h5>
        <ul type="none">
            <li><b>Tipo de pago:</b> {{ student.registro.type }}</li>
            <li>
                <b>{{ student.registro.type === 'ventanilla' ? 'Movimiento':'Folio' }}:</b> 
                {{ student.registro.folio }}
            </li>
            <li v-if="student.registro.type === 'practicaja'">
                <b>Autorización:</b> {{ student.registro.auto }}
            </li>
            <li><b>Total depositado:</b> {{ student.registro.total }}</li>
            <li><b>Fecha del pago:</b> {{ student.registro.date }}</li>
            <li><b>Plaza o sucursal donde se realizo el pago:</b> {{ student.registro.plaza }}</li>
            <li><b>Comprobante: </b> <a :href="student.registro.comprobante.public_url" target="_blank">Ver</a></li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['register'],
    data(){
        return {
            student: this.register
        }
    }
}
</script>
