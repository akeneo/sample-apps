const express = require('express');
const activateController = require('../controllers/api/app-activation.controller');

const router = express.Router();

router.get('/activate', activateController.appActivate);
router.get('/callback', activateController.appCallback);

module.exports = router;
