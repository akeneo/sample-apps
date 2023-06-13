const Codec = require('../utils/codec.utils');
const userDb = require('../data-access/user.db');
const {PrismaClient} = require("@prisma/client");

const homepage = async function(req, res, next) {
    if (process.env.OPENID_AUTHENTICATION === "1"
        && req.cookies.sub !== undefined
        && req.cookies.vector !== undefined) {
        const sub = Codec.decoder(req.cookies.sub, req.cookies.vector, process.env.SUB_HASH_KEY);
        const prisma = new PrismaClient();
        const db = new userDb(prisma);
        const user = await db.getUser(sub);
        res.render('index', {user: user});
    } else {
        res.render('index');
    }
}

module.exports = {
    index: (req, res, next) => homepage(req, res, next)
}
