const { PrismaClient } = require('@prisma/client')
const TokenDb = require("./token.db");
const UserDb = require("./user.db");

const prisma = new PrismaClient();

const tokenDb = new TokenDb(prisma);
const userDb = new UserDb(prisma);

module.exports = {
    tokenDb,
    userDb
};
