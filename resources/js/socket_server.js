import http from 'http';
import { Server } from 'socket.io';
import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';
import https from 'https';

const PORT = process.env.PORT || 3000; 


// Obtener el nombre del archivo y el directorio
const __filename = fileURLToPath(new URL(import.meta.url));
const __dirname = path.dirname(__filename);

// Convertir a ruta absoluta y evitar duplicados
const envFile = process.env.NODE_ENV === 'production' 
    ? path.resolve(__dirname, '.env.production') 
    : path.resolve(__dirname, '.env.development');


//========== CARGANDO VARIABLES ========
dotenv.config({ path: envFile }); 


//==== MOSTRAR EN CONSOLA LAS VARIABLES DE ENTORNO =======
//console.log('Variables de entorno:', process.env); 
//======== IMPRIMIR EN CONSOLA EL ARCHIVO .ENV Q SE ESTÁ LEYENDO =========
/*
fs.readFile(envFile, 'utf8', (err, data) => {
    if (err) {
      console.error('Error al leer el archivo .env:', err);
    } else {
      console.log('Contenido del archivo .env:\n', data);
    }
});
*/


const app = express();
const options = {
    key: fs.readFileSync('/etc/letsencrypt/live/www.obramaster.online/privkey.pem'),  // Ruta a tu archivo de >
    cert: fs.readFileSync('/etc/letsencrypt/live/www.obramaster.online/fullchain.pem'), // Ruta a tu archivo d>
};
const server = https.createServer(options, app);
//const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: process.env.CORS_ORIGIN, // Usar la variable de entorno
        methods: ['GET', 'POST'],
        credentials: true // Habilitar si necesitas enviar cookies o autenticación
    }
});

// Configuración de CORS para las solicitudes HTTP
const corsOptions = {
    origin: process.env.CORS_ORIGIN, // Usar la variable de entorno
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