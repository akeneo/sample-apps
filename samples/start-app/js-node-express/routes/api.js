const express = require('express');
const apiController = require('../controllers/api/api.controller');

const router = express.Router();

router.get('/first-api-call', apiController.firstApiCall);

module.exports = router;
