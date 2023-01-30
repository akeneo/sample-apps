let makeAppActivate = function({
    config,
    url
}) {
    return async function appActivate ({req, res, next}, randomString) {

        const pimUrl = new URL(req.query.pim_url);

        const requestUrl = url.parse(url.format({
            protocol: pimUrl.protocol,
            hostname: pimUrl.hostname,
            pathname: config.get('akeneo.authorize_url'),
            query: {
                response_type: "code",
                client_id: process.env.CLIENT_ID,
                scope: "read_assets",
                state: randomString
            }
        }));

        res.redirect(requestUrl.href);
    };
}

module.exports = makeAppActivate;