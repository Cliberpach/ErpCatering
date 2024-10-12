import http from 'http';
import { Server } from 'socket.io';
import express from 'express';
import cors from 'cors';

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: 'http://127.0.0.1:8000', // Permitir el origen específico
        methods: ['GET', 'POST'],
        credentials: true // Habilitar si necesitas enviar cookies o autenticación
    }
});

// Configuración de CORS para las solicitudes HTTP
const corsOptions = {
    origin: 'http://127.0.0.1:8000',
    methods: ['GET', 'POST'],
    allowedHeaders: ['Content-Type'],
    credentials: true
};

app.use(cors(corsOptions));
app.use(express.json());

app.post('/mensaje', (req, res) => {
    const { mensaje, requerimiento } = req.body;
    console.log('Mensaje recibido:', mensaje, requerimiento);
    io.emit('nuevoRequerimiento', { mensaje, requerimiento });
    res.status(200).send('Mensaje enviado');
});

io.on('connection', (socket) => {
    console.log('Usuario conectado');

    socket.on('disconnect', () => {
        console.log('Usuario desconectado');
    });
});

server.listen(3000, () => {
    console.log('Servidor escuchando en el puerto 3000');
});
