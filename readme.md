# CAPItoRSS
The CAPItoRSS service is a lightweight, API-only app that takes NBA Content API requests and returns a RSS feed via a RESTful API or static file uploads.

## Built With
* [Lumen Framework](https://lumen.laravel.com/) - Micro-framework built on top of PHP
* [Flysystem](https://github.com/thephpleague/flysystem) - Filesystem abstraction package

## Getting Started
CAPItoRSS is built on top of the [Lumen Framework](https://lumen.laravel.com/), so a familiarity with PHP is helpful, though I did my best to make the tool foolproof and easy to edit.

### Prerequisites
You'll need to have [PHP version 7.1.3 or above](http://php.net/downloads.php) installed to run CAPItoRSS.

If you're planning on using the static feed generation service, you'll also need make sure that your command line version of PHP is version 7.1.3 or above.

I would imagine you have Git installed on your machine already, but if not, install it. Note that you may need to precede `apt-get` with `sudo` if the active account doesn't have superuser access.
```shell
$ apt-get update
$ apt-get install git
```

You will need to install [Composer](https://getcomposer.org/) if you do not have it installed already. You can follow the [installation instructions](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) on Composer's site or follow the instructions below.

#### Installing Composer via curl
First make sure your package manager cache is up to date. Note that you may need to precede `apt-get` with `sudo` if the active account doesn't have superuser access.
```shell
$ apt-get update
```
Install curl if you do not have it installed already.
```shell
$ apt-get install curl
```
Download and install Composer into your `/usr/local/bin` directory so that it can be used anywhere on your system.
```shell
$ curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```
You should now have Composer installed globally. If you're having trouble calling `composer` from your terminal, you probably don't have `/usr/local/bin` registered in your PATH file. Check, and add it if you need to.

### Installing CAPItoRSS
First, clone this repository into your local dev environment.
```shell
$ git clone https://github.com/BillRamos/CAPItoRSS.git
```
Navigate to the root project folder and install the project dependencies.
```shell
$ composer install
```
### Notes for Installing on a Production Server
If you are installing CAPItoRSS on your production server, you'll need to set up routing and you may need to set directory permissions.

Configuring routing will be different depending on your server technology, and if you're not familiar with the process, it can be a bit daunting. It's easy once you've done it, but you should read up on how to configure a server for Laravel/Lumen based on your stack.

Here are some walkthroughs for...
* [Nginx](https://medium.com/@asked_io/how-to-install-php-7-2-x-nginx-1-10-x-laravel-5-6-f9e30ee30eff)
* [Apache](https://www.howtoforge.com/tutorial/install-laravel-on-ubuntu-for-apache/)

If you need to set up directory permissions, you should first find out the default user your server is using (the Apache and Nginx default is usually `www-data`). Assuming that `www-data` is the user on your server, you should navigate to the root project file and run the following commands in your terminal.
```shell
$ sudo chown -R www-data storage
$ sudo chown -R www-data bootstrap/cache
```

## Configuring CAPItoRSS

Once Composer has finished installing dependencies, you can start setting up your environment variables. In the root of your project there is a `.env.example` file. You'll need to make a copy of this file and call it `.env`. You can do that through your file browser or by running this in your terminal:
```shell
$ cp .env.example .env
```
Open up `.env` in your favorite text editor and let's get to work. There are several variables here that will need to be configured before CAPItoRSS will work. Here's a breakdown of all of the variables.

#### <center>App Variables</center>
<center>Variables to control the general functionality of CAPItoRSS.</center>

|Variable|Type|Default|Required|Description|
|--|:--:|:--:|:--:|--|
|**APP_NAME**|string|CAPItoRSS||The name of the app.|
|**APP_ENV**|string|production|✓|The app environment. This should be `production` on your live server and `local` on your local server.|
|**APP_KEY**|string||✓|A random, 32-character alpha-numeric string that is used to encrypt any data the app may store on a client machine. Although the app will run without setting this, I'm marking it as required because it's a _very bad idea_ to store any data unencrypted.|
|**APP_DEBUG**|boolean|false|✓|Turns debug logging on and off. This is `false` by default but you should set to `true` when running locally.|
|**APP_URL**|string||✓|The domain that your app will be running at. This is used to generate feed link information, so this must be set accurately in production.|
|**APP_TIMEZONE**|string|America/New_York||The timezone that your feeds should be calculated against. This value should be in [tz database format](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones).|
|**API_ENABLED**|boolean|true|✓|Turns the RESTful API service on or off. This is `true` by default but can be turned off if you want to use the static feed generation service exclusively.|
|**STATIC_ENABLED**|boolean|false|✓|Turns the static feed generation service on or off. This is `false` by default. If you plan on using this service, make sure you read the instructions further down this document to ensure that you've set up your environment appropriately.|
|**CAPI_ACCESS_TOKEN**|string||✓|The access token for the NBA's Content API. If this isn't set accurately, nothing will work. You can find this value in the NBA's Content API documentation.|

#### <center>RSS Variables</center>
<center>Variables to define default values for generated RSS feeds.</center>

|Variable|Type|Default|Required|Description|
|--|:--:|:--:|:--:|--|
|**RSS_DEFAULT_TITLE**|string|||Default title of your generated RSS feeds. You can change this value on a per-feed basis by using query strings while using the RESTful API or by specifying a title in the [config/static.php](https://github.com/BillRamos/CAPItoRSS/blob/master/config/static.php) definitions file when using the static feed generation service.|
|**RSS_DEFAULT_LINK**|string|||Default organization link of your generated RSS feeds. Per-feed customization works the same as noted in **RSS_DEFAULT_TITLE**.
|**RSS_DEFAULT_COPYRIGHT_OWNER**|string|||Default copyright owner of your generated RSS feeds. Per-feed customization works the same as noted in **RSS_DEFAULT_TITLE**.|
|**RSS_DEFAULT_DESCRIPTION**|string|||Default feed description of your generated RSS feeds. Per-feed customization works the same as noted in **RSS_DEFAULT_TITLE**.|
|**RSS_DEFAULT_LINK**|string|||Default organization link of your generated RSS feeds. Per-feed customization works the same as noted in **RSS_DEFAULT_TITLE**.|
|**RSS_DEFAULT_LANGUAGE**|string|en-us||Default language of your generated RSS feeds. Per-feed customization works the same as noted in **RSS_DEFAULT_TITLE**.|

#### <center>Output Endpoint Variables*</center>
<center>Variables used to configure output/upload endpoints for the static file generation service. *You can leave these blank if you are not using the static file generation service.</center>

|Variable|Type|Default|Required|Description|
|--|:--:|:--:|:--:|--|
|**LOCAL_UPLOAD_PATH**|string|static||The default path to upload static feeds to when using the `local` upload driver (ex. a value of `some/path` would upload `sample.rss` to `https://yourserver.com/some/path/sample.rss`).|
|**FTP_HOST**|string|||The IP address or hostname of your FTP server for use with the `ftp` upload driver.|
|**FTP_PORT**|string|21||The port to use when connecting to your FTP server.|
|**FTP_USERNAME**|string|||The username use when connecting to your FTP server.|
|**FTP_PASSWORD**|string|||The password use when connecting to your FTP server.|
|**FTP_PUBLIC_URL_ROOT**|string|||The public root URL path that your FTP server will upload files to. For example, the value for this attribute if using the Turner CDN would be `https://www.nba.com/.element/media/2.0/teamsites/teamname`|
|**FTP_UPLOAD_PATH**|string|||The path to the default upload location on your FTP server. For example, a value of `rss` if using the Turner CDN would upload files to `https://www.nba.com/.element/media/2.0/teamsites/teamname/rss`.|
|**SFTP_HOST**|string|||The IP address or hostname of your SFTP server for use with the `sftp` upload driver.|
|**SFTP_PORT**|string|22||The port to use when connecting to your SFTP server.|
|**SFTP_USERNAME**|string|||The username use when connecting to your SFTP server.|
|**SFTP_PASSWORD**|string|||The password use when connecting to your SFTP server.|
|**SFTP_KEY_PATH**|string|||The path to your SSH key if you are using key authentication on your SFTP server.|
|**SFTP_PUBLIC_URL_ROOT**|string|||The public root URL path that your SFTP server will upload files to (ex. `https://yourserver.com/`).|
|**SFTP_UPLOAD_PATH**|string|||The path to the default upload location on your SFTP server. For example, a value of `feeds/rss` if using the public URL root above would upload files to `https://yourserver.com/feeds/rss`.|
|**AWS_ACCESS_KEY_ID**|string|||The access key for an AWS account with S3 access.|
|**AWS_SECRET_ACCESS_KEY**|string|||The secret access key for an AWS account with S3 access.|
|**AWS_BUCKET**|string|||The unique identifier for your AWS S3 bucket.|
|**AWS_DEFAULT_REGION**|string|||The default region that your S3 bucket lives in.|
|**AWS_VERSION**|string|latest||The version of the AWS API you are using. This defaults to `latest`.|
|**AWS_PUBLIC_URL_ROOT**|string|||The public root URL path to your S3 bucket.|
|**AWS_UPLOAD_PATH**|string|||The path to the default upload location on your S3 bucket.|
|**AWS_UPLOAD_PATH**|string|||The path to the default upload location on your S3 bucket.|

The remaining variables in `.env` are not used in CAPItoRSS. I've kept them there in case you extend the service on your own.

#### Adding Multiple Output Endpoints
If you would like to use several output/upload endpoints that use the same protocol (`ftp`,`sftp`,`awss3`, etc.) you can certainly do so. You will have to edit the [config/flysystem.php](https://github.com/BillRamos/CAPItoRSS/blob/master/config/flysystem.php) file and add custom entries to the `connections` attribute. You can then specify those custom entries in your [config/static.php](https://github.com/BillRamos/CAPItoRSS/blob/master/config/static.php) configuration file.

#### Defining Custom Static Feeds
To create and define custom feeds to be generated by the static file generation service, you will need to add entries into the `feeds` array in [config/static.php]https://github.com/BillRamos/CAPItoRSS/blob/master/config/static.php.

Each feed should be its own array item. Each feed can have multiple output endpoints so that you can upload the generated feeds to more than one location at once.

The file should be self-explanatory, but I will document the feed array shortly.

#### Setting Up the Task Scheduler for Static File Generation Service
You'll need to set up the Task Scheduler if you plan on using the static file generation service. The Task Scheduler is a service that runs once every minute to check if any tasks need to be executed.

You'll need to add a Cron entry for the Scheduler so it can handle those tasks. To add the Cron entry, enter the command below in your terminal.
```shell
$ crontab -e
```
You should now be editing your crontab file. Add the following line to that file, replacing `/path/to/your/project` with the path to the root of your CAPItoRSS project (same directory as this readme).
```shell
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

#### Defining Your Static Feed Generation Interval
Now that you've set up the Task Scheduler, you should configure the interval at which feeds are generated and uploaded to suit your needs.

You will need to make edits to the [app/Console/Kernel.php](https://github.com/BillRamos/CAPItoRSS/blob/master/app/Console/Kernel.php) file. You should [read the Task Scheduler documentation](https://laravel.com/docs/5.8/scheduling#schedule-frequency-options) to see which methods you can use to schedule your tasks.

#### Testing Static Feed Generation
You can test that the static feed generation service is working as expected by running the GenerateStaticFeeds command with the `--test` flag in the root of the project.
```shell
$ php artisan command:generate_static_feeds --test
```
This will run through your configured feed definitions and associated output endpoints to make sure they are working as expected.  When the `--test` flag is used, this function will not upload any files to your endpoints, but will make sure that a connection is established.

If you would like to test that the uploads are working as expected, you can run the command without the `--test` flag to actually upload the generated feeds to your output endpoints. This is helpful in debugging permissions at the endpoint, as sometimes you can connect to an output location but you may not have permission to write to it.
```shell
$ php artisan command:generate_static_feeds
```

## Using the RESTful API
The RESTful API service of CAPItoRSS is very easy to use.

If the service is turned on, you simply need to visit `/api/CAPI_PATH` on your server where **CAPI_PATH** = the path you would normally use with the Content API. The path in this instance is anything that comes after `https://api.nba.net/2/` when using the Content API normally.

Path and query strings work exactly the same way as they do when using the Content API. Here's an example of a normal Content API request and the associated request to generate an RSS feed using the RESTful API service:
* Content API: `https://api.nba.net/2/teams/video,imported_video/?offset=0&count=99&title=Round 2`
* CAPItoRSS: `https://yourserver.com/api/teams/video,imported_video/?offset=0&count=99&title=Round 2`

There are a few additional query strings that you can pass to the RESTful API service to customize the returned RSS feed.

I don't recommend exposing these query strings to the public, as someone could change the title, description, copyright info, etc. in the returned RSS feed and use that feed as their own - if you care about that.

The query strings are listed below.

|Query String|Example|Description|
|--|--|--|
|t|`t=Flint Tropics News`|Title of the generated RSS feed.|
|l|`l=https://www.flinttropics.com`|Organization link of the generated feed.|
|c|`c=Flint Tropics`|Copyright owner of the generated RSS feed.|
|d|`d=The official news feed of the Flint Tropics`|Description of the generated RSS feed.|
|la|`la=en-us`|Language of the generated RSS feed.|

## Extra
If you find this useful, have issues, have any suggestions or would like to contribute, give me a shout! I'm happy to talk through anything that might pop up.

Thanks for using CAPItoRSS!
