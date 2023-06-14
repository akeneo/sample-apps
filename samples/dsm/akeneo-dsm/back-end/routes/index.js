const express = require('express');
const appActivation = require('./app-activation.js');
const api = require('./api.js');
const homepageController = require("../controllers/homepage.controller.js");
const pimController = require("../controllers/pim.controller.js");

const router = express.Router();

/* GET home page. */
router.get('/', homepageController.index);

router.get('/my-pim-instance', pimController.instance);

router.use(appActivation);
router.use(api);

module.exports = router;
