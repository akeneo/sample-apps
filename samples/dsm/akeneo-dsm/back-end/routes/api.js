const express = require('express');
const pimController = require("../controllers/api/pim.controller.js");

const router = express.Router();

router.get('/my-pim-instance', pimController.instance);
router.get('/some-products', pimController.productsRetrieve);

module.exports = router;
