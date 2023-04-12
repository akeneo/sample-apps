# Start App PHP Symfony

## Requirements
- You’ve got a PIM developer sandbox. If not, please contact us ([https://www.akeneo.com/contact/](https://www.akeneo.com/contact/))
- You’ve installed Docker (20.10.22 or higher)
- You’ve installed Docker Compose V2 plugin (see the official installation tutorial [here](https://docs.docker.com/compose/install/))


### Step 1: Get this App on your local machine
Get the whole Sample-Apps repository and navigate to the PHP Symfony start app folder
```
git clone git@github.com:akeneo/sample-apps.git
cd samples/start-app/php-symfony
```

### Step 2 (optional): Create a tunnel to expose your local App
Use [ngrok]([https://ngrok.com/](https://ngrok.com/)) to create a tunnel that allows your App to be accessed using a unique HTTPS URL. You need to create a ngrok account and auth token to preview your App.
```
ngrok http 8081
```
We recommend to generate an [authtoken](https://dashboard.ngrok.com/get-started/your-authtoken) too. If you do not, you may need to restart the installation and connection process.

If everything goes well the command will output your public URL for your local App :

![img.png](../../../common/images/step2-1.png)

### Step 3: Declare your local App as a test App in your sandbox to generate credentials
1. In your sandbox go to Connect > App store > Create a test App
2. Paste your callback & activation URLs
```
Name: [My App]
Activate URL: [your-url]/activate
Callback: [your-url]/callback
```
![img.png](../../../common/images/step3-1.png)

The App generates the credentials. Copy them for later.

![img.png](../../../common/images/step3-2.png)

### Step 4: OpenID Connect

OpenID Connect is a simple identity layer on top of the OAuth 2.0 protocol, implemented in Akeneo PXM Studio to authenticate users.
This is an optional feature in Apps, you can also use your own Authentication. To learn more, check the complete [documentation](https://api.akeneo.com/apps/authentication-and-authorization.html#getting-started-with-openid-connect).

Next step will let you choose whether you want to use this feature in your app, or not. 
OpenId related code is clearly identified within the app to allow a fast and easy modification.

### Step 5: Launch your App
Open a terminal at the root of the folder of your App, enter the following command and let us guide you
```
make install
```
For information, this command launch scripts locate into `common/bin/install` in the root folder:

- [checkRequirements.sh](https://github.com/akeneo/sample-apps/blob/main/common/bin/install/checkRequirements.sh)
- [dotEnvFileCreator.sh](https://github.com/akeneo/sample-apps/blob/main/common/bin/install/dotEnvFileCreator.sh)

### Step 6: Activate your test App for your sandbox
1. Back in your sandbox go to Connect > App store
2. You see your App in the section “My App”
![img.png](../../../common/images/step5-1.png)

3. Click on CONNECT
4. Follow the activation process steps until the end

If everything is good, you should see
![img.png](../../../common/images/step5-2_php-symfony.png)

It means your App is connected. Well done!

### Step 7: Call your first API endpoint

Last but not least, let’s call the PIM API!
Simply go [here](http://localhost:8081/first-api-call).

You should see
![img.png](../../../common/images/step6-1.png)


You will find a pre-configured HTTP client [here](https://github.com/akeneo/sample-apps/blob/main/samples/start-app/php-symfony/src/UseCase/FirstApiCall.php) . Feel free to use it for your project.

Voilà! You are ready for developing your App!

### update the access scopes

If a user updates the access scopes of an already connected app, the access scope update process will require app users to provide consent for the addition of new scopes. You can notify the PIM that the app requires an authorization update using this endpoint:

```
[your-url]/notify-authorization-update
```

### What's next?
- Want to get your PIM data to your App? Have a look to our [guided tutorials](https://api.akeneo.com/tutorials/homepage.html#tags=App%20Workflow)
- Need some tools? Have a look to our [dedicated page](https://api.akeneo.com/apps/app-developer-tools.html): we have, with others, a Postman collection that may you during your development.
