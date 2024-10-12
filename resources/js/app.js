
import './bootstrap';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css'; 
import { io } from 'socket.io-client'; 

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
};

window.toastr   = toastr;

// Configuración de la conexión de Socket.IO
const socket = io('http://127.0.0.1:3000'); // Cambia el puerto si es necesario

// Evento de conexión
socket.on('connect', () => {
    console.log('Conectado al servidor de Socket.IO');
});

// Evento personalizado para escuchar mensajes desde el servidor


window.io = socket; // Opcional: asignar el socket a window para acceder globalmente si es necesario

