const { PrismaClient } = require('@prisma/client')
const TokenDb = require("./token.db");

const prisma = new PrismaClient();

const tokenDb = new TokenDb(prisma);

module.exports = {
    tokenDb
};
