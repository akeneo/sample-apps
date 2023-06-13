let doOpenIdConnect = function({
    httpsClient,
    jwt,
    userDb,
   LogicError
}) {
    return async function openIdConnect(token_id) {

        if (typeof token_id === undefined || token_id === null) {
            throw new LogicError('Token is not supposed to be null or undefined');
        }

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

        const response = await httpsClient.request(options);

        return response.public_key;
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
