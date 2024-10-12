
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

const socketUrl = import.meta.env.VITE_SOCKET_URL;

// Configuración de la conexión de Socket.IO
const socket = io(socketUrl); 

// Evento de conexión
socket.on('connect', () => {
    console.log('Conectado al servidor de Socket.IO');
});



window.io = socket; // Opcional: asignar el socket a window para acceder globalmente si es necesario

