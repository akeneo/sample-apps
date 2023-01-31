const http = require('http');
const app = require('./app');

require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` })

app.set('port', process.env.PORT || 80);
const server = http.createServer(app);

server.listen(process.env.PORT || 80);
