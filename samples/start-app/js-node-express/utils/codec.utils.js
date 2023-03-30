const crypto = require('crypto');

class Codec {

    static algorithm = "aes-256-ctr";

    static encoder(payload) {
        const key = process.env.SUB_HASH_KEY;
        const iv_length = crypto.getCipherInfo(Codec.algorithm).ivLength;
        const iv = crypto.randomBytes(iv_length);
        const cipher = crypto.createCipheriv(Codec.algorithm, key, iv)
        const encodedSub = cipher.update(payload, 'utf8', 'hex') + cipher.final('hex')
        const encodedIv = iv.toString('hex');
        return { sub: encodedSub, vector: encodedIv };
    }

    static decoder(data, encodedIv) {
        const key = process.env.SUB_HASH_KEY;
        const iv = Buffer.from(encodedIv, "hex");
        const decipher = crypto.createDecipheriv(Codec.algorithm, key, iv);
        return decipher.update(data, 'hex', 'utf8') + decipher.final('utf8');
    }
}

module.exports = Codec;
