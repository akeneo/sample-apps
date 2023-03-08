let doAppActivate = function({
    url,
    LogicErrorException
}) {
    return async function appActivate ({req, res, next}, randomString) {

        const oauth_scopes = [
            'read_channel_localization',
            'read_channel_settings',
        ];

        try {
            const pimUrl = new URL(req.query.pim_url);

            const requestUrl = url.parse(url.format({
                protocol: pimUrl.protocol,
                hostname: pimUrl.hostname,
                port: pimUrl.port,
                pathname: "/connect/apps/v1/authorize",
                query: {
                    response_type: "code",
                    client_id: process.env.CLIENT_ID,
                    scope: oauth_scopes.join(' '),
                    state: randomString
                }
            }));

            res.redirect(requestUrl.href);
        } catch(e) {
            if (e instanceof TypeError) {
                throw new LogicErrorException(
                    "Can't retrieve PIM url, please restart the authorization process."
                );
            } else {
                res.render('error');
            }
        }
    };
}

module.exports = doAppActivate;
