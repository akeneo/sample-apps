let doAppActivate = function({
    config,
    url
}) {
    return async function appActivate ({req, res, next}, randomString) {

        const oauth_scopes = [
            'read_channel_localization',
            'read_channel_settings',
        ];

        const pimUrl = new URL(req.query.pim_url);

        const requestUrl = url.parse(url.format({
            protocol: pimUrl.protocol,
            hostname: pimUrl.hostname,
            port: pimUrl.port,
            pathname: config.get('akeneo.authorize_url'),
            query: {
                response_type: "code",
                client_id: process.env.CLIENT_ID,
                scope: oauth_scopes.join(' '),
                state: randomString
            }
        }));

        res.redirect(requestUrl.href);
    };
}

module.exports = doAppActivate;
