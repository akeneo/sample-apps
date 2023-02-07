# Start App PHP Symfony

## Requirements
- You’ve got a PIM developer sandbox. If not, please contact us ([https://www.akeneo.com/contact/](https://www.akeneo.com/contact/))
- You’ve installed Docker (20.10.22 or higher)

### Step 1: Get this App on your local machine
Use [GitZip](https://kinolien.github.io/gitzip/): enter [https://github.com/akeneo/sample-apps/samples/start-app/php-symfony](https://github.com/akeneo/sample-apps/samples/start-app/php-symfony) in the form at the top right.
![img.png](images/step1-1.png)

### Step 2: Create a tunnel to expose your local App
Use [ngrok]([https://ngrok.com/](https://ngrok.com/)) to create a tunnel that allows your App to be accessed using a unique HTTPS URL. You need to create a ngrok account and auth token to preview your App.
```
ngrok http 8081
```

### Step 3: Declare your local App as a test App in your sandbox to generate credentials
In your sandbox go to Connect > App store > Create a test App
Paste your callback & activation URLs
```
Name: [My App]
Activate URL: [your-ngrok-url]/activate
Callback: [your-ngrok-url]/callback
```
![img.png](images/step3-1.png)

The App generates the credentials. Copy them for later.

![img.png](images/step3-2.png)

### Step 4: Launch your App
Open a terminal at the root of the folder of your App, enter the following command and let us guide you
```
make install
```

### Step 5: Activate your test App for your sandbox
1. Back in your sandbox go to Connect > App store
2. You see your App in the section “My App”
![img.png](images/step5-1.png)

3. Click on CONNECT
4. Follow the activation process steps until the end

If everything is good, you should see
![img.png](images/step5-2.png)

It means your App is connected. Well done!

### Step 6: Call your first API endpoint

Last but not least, let’s call the PIM API!
Simply go [here](http://localhost:8081/first-api-call).

You should see
![img.png](images/step6-1.png)


You will find a pre-configured HTTP client [here](https://github.com/akeneo/sample-apps/blob/main/samples/start-app/php-symfony/src/UseCase/FirstApiCall.php) . Feel free to use it for your project.

Voilà! You are ready for developing your App!

### What's next?
- Want to get your PIM data to your App? Have a look to our [guided tutorials](https://api.akeneo.com/tutorials/homepage.html#tags=App%20Workflow)
- Need some tools? Have a look to our [dedicated page](https://api.akeneo.com/apps/app-developer-tools.html): we have, with others, a Postman collection that may you during your development.
