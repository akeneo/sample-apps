require('dotenv').config({ path: `.env.${process.env.NODE_ENV}` });

const Codec = require('../../utils/codec.utils');
const crypto = require("crypto");

test("It encodes a given payload", async () => {

    const payload = 'thisIsATest';
    const key = process.env.SUB_HASH_KEY;

    let object = Codec.encoder(payload, key);

    expect(typeof object.sub).toBe("string");
    expect(typeof object.vector).toBe("string");

    const iv = Buffer.from(object.vector, "hex");
    const decipher = crypto.createDecipheriv(Codec.algorithm, key, iv);
    const data = decipher.update(object.sub, 'hex', 'utf8') + decipher.final('utf8');

    expect(data).toBe(payload);
});

test("It decodes correctly a given hash", async () => {

    const payload = 'thisIsATest';
    const key = process.env.SUB_HASH_KEY;

    let object = Codec.encoder(payload, key);
    let data = Codec.decoder(object.sub, object.vector, key);

    expect(data).toBe(payload);
});
