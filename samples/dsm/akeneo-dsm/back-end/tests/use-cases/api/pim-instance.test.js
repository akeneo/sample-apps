const {instance} = require("../../../controllers/api/pim.controller");

test("It sends a json with the pim instance", async () => {
    const req = {};
    const res = { json: {},
        json: function(input) { this.json = input } 
    };
    const next = jest.fn();

    instance(req, res, next);

    expect(res.json).toEqual({'pim-instance': process.env.AKENEO_PIM_URL});
});
