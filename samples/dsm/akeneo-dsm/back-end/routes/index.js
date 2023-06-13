const express = require('express');
const appActivation = require('./app-activation.js');
const api = require('./api.js');
const homepageController = require("../controllers/homepage.controller.js");

const router = express.Router();

/* GET home page. */
router.get('/', homepageController.index);

router.use(appActivation);
router.use(api);

module.exports = router;
