const https = require('https');

class HttpsClient{
    constructor() {

        const pimUrl = new URL(process.env.AKENEO_PIM_URL);

        let userAgent = this.buildUserAgent(process.env.APPLICATION_VERSION, process.env.DOCKER_VERSION);

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

    buildUserAgent(app_version = undefined, docker_version = undefined) {

        let ret = 'AkeneoSampleApp/js-node-express';
        ret += (app_version)? ' Version/' + app_version : '';
        ret += (docker_version)? ' Docker/' + docker_version : '';

        return ret;
    }

}

module.exports = HttpsClient;
