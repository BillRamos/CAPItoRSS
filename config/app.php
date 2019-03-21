<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Application Name
  |--------------------------------------------------------------------------
  |
  | This value is the name of your application. This value is used when the
  | framework needs to place the application's name in a notification or
  | any other location as required by the application or its packages.
  |
  */

  'name' => env('APP_NAME', 'CAPItoRSS'),

  /*
  |--------------------------------------------------------------------------
  | Application Environment
  |--------------------------------------------------------------------------
  |
  | This value determines the "environment" your application is currently
  | running in. This may determine how you prefer to configure various
  | services the application utilizes. Set this in your ".env" file.
  |
  */

  'env' => env('APP_ENV', 'production'),

  /*
  |--------------------------------------------------------------------------
  | Application Debug Mode
  |--------------------------------------------------------------------------
  |
  | When your application is in debug mode, detailed error messages with
  | stack traces will be shown on every error that occurs within your
  | application. If disabled, a simple generic error page is shown.
  |
  */

  'debug' => env('APP_DEBUG', false),

  /*
  |--------------------------------------------------------------------------
  | Application URL
  |--------------------------------------------------------------------------
  |
  | This URL is used by the console to properly generate URLs when using
  | the Artisan command line tool. You should set this to the root of
  | your application so that it is used when running Artisan tasks.
  |
  */

  'url' => env('APP_URL', 'https://capitorss.yourdomain.com'),

  /*
  |--------------------------------------------------------------------------
  | Application Timezone
  |--------------------------------------------------------------------------
  |
  | Here you may specify the default timezone for your application, which
  | will be used by the PHP date and date-time functions. This is set to
  | Eastern Time (New York) by default - change for your team if applicable.
  |
  */

  'timezone' => env('APP_TIMEZONE', 'America/New_York'),

  /*
  |--------------------------------------------------------------------------
  | Encryption Key
  |--------------------------------------------------------------------------
  |
  | This key is used by the Illuminate encrypter service and should be set
  | to a random, 32 character string, otherwise these encrypted strings
  | will not be safe. Please do this before deploying an application!
  |
  */

  'key' => env('APP_KEY'),

  'cipher' => 'AES-256-CBC',

  /*
  |--------------------------------------------------------------------------
  | Content API Access Token
  |--------------------------------------------------------------------------
  |
  | This is the access token for the NBA Content API.
  | You can find this in the Content API documentation provided by the NBA.
  |
  */
  'access_token' => env('CAPI_ACCESS_TOKEN'),

  /*
  |--------------------------------------------------------------------------
  | RESTful API Service
  |--------------------------------------------------------------------------
  |
  | This will turn the core RESTful API service of CAPItoRSS on or off.
  | This service is on by default and is the most flexible way to run the
  | service, but it is the most resource-intensive since the service fires off
  | a request to the NBA's Content API and generates an RSS feed to return for
  | every request. If speed and reliability are a concern, you may want to set
  | up the static/flat-file based service. You can run either service alone or
  | both in tandem if you prefer for fallback purposes (ex. client-side script
  | requests a flat file from a server/CDN, and if that fails, hits the RESTful
  | endpoint as a backup).
  |
  */

  'api' => env('API_ENABLED', true),

  /*
  |--------------------------------------------------------------------------
  | Static File Generation Service
  |--------------------------------------------------------------------------
  |
  | This will turn the static file generation service of CAPItoRSS on or off.
  | You must specify a file storage disk on which to store those flat files.
  | A storage disk can be local (directory on this server), an FTP server or
  | or an AWS S3 bucket. If this service is on and configured, flat files will
  | be generated at a specified interval and uploaded to your chosen storage
  | disk. This is off by default in favor of the RESTful API service.
  |
  */
  'static' => env('STATIC_ENABLED', false),
];
