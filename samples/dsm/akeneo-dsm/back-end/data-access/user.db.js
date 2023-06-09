class UserDb {
    constructor(prisma) {
        this.prisma = prisma
    }

    async upsert ({sub, firstname, lastname, email}) {
        try {
            const user = await this.prisma.user.findUnique({
                where: {
                    sub: sub,
                },
            });
            return await this.prisma.user.upsert({
                where: {
                    id : user != null ? user.id : 0
                },
                update: {
                    sub: sub,
                    firstname: firstname,
                    lastname: lastname,
                    email: email
                },
                create: {
                    sub: sub,
                    firstname: firstname,
                    lastname: lastname,
                    email: email
                }
            });
        } catch (e) {
            console.error(e);
            process.exit(1);
        } finally {
            await this.prisma.$disconnect();
        }
    }

    async remove ({id, sub}) {
        try {
            return await this.prisma.user.update({
                where: { sub: sub },
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

    async getUser(sub) {
        try {
            return await this.prisma.user.findUniqueOrThrow({
                where: {
                    sub: sub,
                },
            });
        } catch (e) {
            console.error(e);
            return null;
        } finally {
            await this.prisma.$disconnect();
        }
    }
}

module.exports = UserDb;
