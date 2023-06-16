const pim_instance = function(req, res, next) {
    const myPimInstance = process.env.AKENEO_PIM_URL;
    res.json({'pim-instance': myPimInstance});
}

module.exports = { pim_instance };