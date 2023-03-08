const https = require('https');

class HttpsClient{
    constructor() {

        const pimUrl = new URL(process.env.AKENEO_PIM_URL);

        let userAgent = 'AkeneoSampleApp/js-node-express';
        userAgent += (process.env.APPLICATION_VERSION)? ' Version/' + process.env.APPLICATION_VERSION : '';
        userAgent += (process.env.DOCKER_VERSION)? ' Docker/' + process.env.DOCKER_VERSION : '';

        this.options = {
            host: pimUrl.hostname,
            port: pimUrl.port,
            headers: {
                "User-Agent": userAgent
            }
        }
    }

    setToken(token) {
        this.options.headers = {...this.options.headers, "Authorization": `Bearer ${token.accessToken}`}
    }

    request(options={}, callback) {

        const merged_options = {...this.options, ...options}

        return https.request(merged_options, callback);
    }

}

module.exports = HttpsClient;
