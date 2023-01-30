const express = require('express');
const appActivation = require('./app-activation.js');

const router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  res.render('index', { title: 'Express' });
});

router.use(appActivation);

module.exports = router;
