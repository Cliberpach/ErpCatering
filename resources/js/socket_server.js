import http from 'http';
import { Server } from 'socket.io';
import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';
import https from 'https';

const __filename    = fileURLToPath(import.meta.url);
const __dirname     = path.dirname(__filename);
dotenv.config({ path: path.resolve(__dirname, '.env') });

const PORT          =   process.env.PORT || 3000; 
const environment   =   process.env.VITE_ENV;
const key           =   process.env.SSL_KEY_PATH;
const cert          =   process.env.SSL_CERT_PATH;
const cors_origin   =   process.env.CORS_ORIGIN

console.log('Current Environment:', environment);
console.log('key:', key);
console.log('cert:', cert);
console.log('cors',cors_origin);


const app = express();
app.use(cors());

let server  =   null;

if(environment === 'production'){
    const options = {
        key: fs.readFileSync(key),  // Ruta a tu archivo de >
        cert: fs.readFileSync(cert), // Ruta a tu archivo d>
    };
    server = https.createServer(options, app);
}

if(environment === 'development'){
    server = http.createServer(app);
}

const io = new Server(server, {
    cors: {
        origin: cors_origin, // Usar la variable de entorno
        methods: ['GET', 'POST'],
        credentials: true // Habilitar si necesitas enviar cookies o autenticación
    }
});

// Configuración de CORS para las solicitudes HTTP
const corsOptions = {
    origin: cors_origin, // Usar la variable de entorno
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