const express = require('express');
const appActivation = require('./app-activation.js');
const api = require('./api.js');

const router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  res.render('index', { title: 'Express' });
});

router.use(appActivation);
router.use(api);

module.exports = router;
