let doAppCallback = function({
    strings,
    crypto,
    querystring,
    httpsClient,
    Codec,
    tokenDb,
    LogicError,
}) {
    return async function appCallback ({req, res, next}, randomString, openIdCallback=null) {

        if (!req.query.hasOwnProperty('state') || req.query.state !== randomString) {
            res.render('error');
            throw new LogicError('Invalid state');
        }

        const codeIdentifier = strings.bin2hex(crypto.randomBytes(30));
        const codeChallenge = crypto.createHash('sha256').update(codeIdentifier + process.env.CLIENT_SECRET).digest('hex');

        const data = querystring.stringify({
            client_id: process.env.CLIENT_ID,
            code: req.query.code,
            grant_type: "authorization_code",
            code_identifier: codeIdentifier,
            code_challenge: codeChallenge
        });

        const options = {
            path: "/connect/apps/v1/oauth2/token",
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        };

        const response = await httpsClient.request(options, data);

        const access_token = response.access_token;

        if (access_token !== undefined) {
            tokenDb.upsert({access_token});

            if (!await tokenDb.hasToken()) {
                res.render('no_access_token');
                throw new LogicError('Missing access token in database');
            } else {
                if (process.env.OPENID_AUTHENTICATION === "1" && openIdCallback instanceof Function) {
                    const id_token = response.id_token;
                    const user = await openIdCallback(id_token);
                    const encodedCookie = Codec.encoder(user.sub, process.env.SUB_HASH_KEY)
                    res.cookie('sub', encodedCookie.sub);
                    res.cookie('vector', encodedCookie.vector);
                    res.render('access_token', {user: user});
                } else {
                    res.render('access_token');
                }
            }
        } else {
            res.render('no_access_token');
            throw new LogicError('Missing access token in response');
        }
    };
}

module.exports = doAppCallback;
