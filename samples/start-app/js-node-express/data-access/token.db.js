class TokenDb{
    constructor(prisma) {
        this.prisma = prisma
    }

    async upsert ({access_token, id=undefined}) {
        try {
            return await this.prisma.token.upsert({
                where: {
                    id : id ? id : 0
                },
                update: {
                    accessToken: access_token
                },
                create: {
                    accessToken: access_token
                }
            });
        } catch (e) {
            console.error(e);
            process.exit(1);
        } finally {
            await this.prisma.$disconnect();
        }
    }

    async remove ({id, access_token}) {
        try {
            return await this.prisma.token.update({
                where: { accessToken: access_token },
                data: {
                    profile: {
                        delete: [{id: id}]
                    }
                }
            });
        } catch (e) {
            console.error(e);
            process.exit(1);
        } finally {
            await this.prisma.$disconnect();
        }
    }

    async getToken () {
        try {
            return await this.prisma.token.findFirstOrThrow();
        } catch (e) {
            console.error(e);
            process.exit(1);
        } finally {
            await this.prisma.$disconnect();
        }
    }

    async hasToken () {
        try {
            const result = await this.prisma.token.count();
            return (result > 0);
        } catch (e) {
            console.error(e);
            process.exit(1);
        } finally {
            await this.prisma.$disconnect();
        }
    }
}

module.exports = TokenDb;
