let doOpenIdConnect = function({
    httpsClient,
    jwt,
    userDb
}) {
    return async function openIdConnect(token_id) {
        const pim_url = new URL(process.env.AKENEO_PIM_URL);

        const public_key = await fetchOpenIdPublicKey();
        const claims = await extractClaimsFromSignedToken(token_id, public_key, pim_url.href);
        const user = getUserProfileFromTokenClaims(claims);

        userDb.upsert(user);

        return user;
    }

    async function fetchOpenIdPublicKey() {
        const options = {
            path: "/connect/apps/v1/openid/public-key",
            method: 'GET',
            headers: {
            }
        };

        return new Promise((resolve, reject) => {
            const httpreq = httpsClient.request(options, function (response) {

                response.setEncoding('utf8');
                let data = "";

                response.on('data', (chunk) => {
                    data += chunk;
                });

                response.on('end', () => {
                    const body = JSON.parse(data);
                    resolve(body.public_key);
                });
            });

            httpreq.on('error', (error) => {
                reject(error);
            });

            httpreq.end();
        });
    }

    async function extractClaimsFromSignedToken(token_id, signature, issuer) {
        //trim the last / in url
        issuer = issuer.replace(/\/$/, "");

        return new Promise((resolve, reject) => {
            jwt.verify(token_id, signature, { algorithms: ['RS256'], issuer: issuer }, function (err, payload) {
                if (err === null) {
                    resolve(payload);
                } else {
                    reject(err);
                }
            });
        });
    }

    function getUserProfileFromTokenClaims(claims) {
        return {
            'sub': claims.sub,
            'firstname': claims.firstname,
            'lastname': claims.lastname,
            'email': claims.email
        };
    }
}

module.exports = doOpenIdConnect;
