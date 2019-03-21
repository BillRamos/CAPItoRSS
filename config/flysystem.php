<?php

declare(strict_types=1);

return [

  /*
  |--------------------------------------------------------------------------
  | Flysystem (File Storage) Configuration
  |--------------------------------------------------------------------------
  |
  | CAPItoRSS automatically includes packages to support the core Flysystem
  | (local and FTP storage), SFTP and S3 drivers.
  |
  | Those packages are:
  | - Flysystem (core/local/FTP): graham-campbell/flysystem
  | - SFTP: league/flysystem-sftp
  | - Amazon S3: league/flysystem-aws-s3-v3
  |
  | You can extend Flysystem to support other platforms such as Microsoft
  | Azure, Dropbox, Rackspace and more.
  |
  | If you would like to implement a cached adapter, you will need to require
  | the CachedAdapter package (league/flysystem-cached-adapter) and set your
  | configuration variables. I've included the config/cache.php file in the
  | event that you want to set that up, and you'll need to uncomment the
  | cache binders in bootstrap/app.php.
  |
  | DOCUMENTATION:
  | - Flysystem: https://github.com/GrahamCampbell/Laravel-Flysystem
  | - Cache in Lumen/Laravel: https://laravel.com/docs/cache
  |
  */

  /*
  | This file is part of Laravel Flysystem.
  |
  | (c) Graham Campbell <graham@alt-three.com>
  |
  | For the full copyright and license information, please view the LICENSE
  | file that was distributed with this source code.
  |
  */

  /*
  |--------------------------------------------------------------------------
  | Default Connection Name
  |--------------------------------------------------------------------------
  |
  | Here you may specify which of the connections below you wish to use as
  | your default connection for all work. Of course, you may use many
  | connections at once using the manager class.
  |
  */

  'default' => 'local',

  /*
  |--------------------------------------------------------------------------
  | Flysystem Connections
  |--------------------------------------------------------------------------
  |
  | Here are each of the connections setup for your application. Examples of
  | configuring each supported driver is shown below. You can of course have
  | multiple connections per driver.
  |
  */

  'connections' => [

    'local' => [
      'driver' => 'local',
      'path' => base_path().'/public',
      'visibility' => 'public',
      'public_url_root' => env('APP_URL',''),
      'public_path' => '/'.env('LOCAL_UPLOAD_PATH',''),
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'ftp' => [
      'driver' => 'ftp',
      'host' => env('FTP_HOST'),
      'port' => env('FTP_PORT',21),
      'username' => env('FTP_USERNAME'),
      'password' => env('FTP_PASSWORD'),
      'public_url_root' => env('FTP_PUBLIC_URL_ROOT',''),
      'public_path' => '/'.env('FTP_UPLOAD_PATH',''),
      // 'root' => '/path/to/root',
      // 'passive' => true,
      // 'ssl' => true,
      // 'timeout' => 20,
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'sftp' => [
      'driver' => 'sftp',
      'host' => env('SFTP_HOST'),
      'port' => env('SFTP_PORT',22),
      'username' => env('SFTP_USERNAME'),
      'password' => env('SFTP_PASSWORD'),
      'public_url_root' => env('SFTP_PUBLIC_URL_ROOT',''),
      'public_path' => '/'.env('SFTP_UPLOAD_PATH',''),
      'privateKey' => env('SFTP_KEY_PATH',null),
      // 'root' => '/path/to/root',
      // 'timeout' => 20,
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    's3' => [
      'driver' => 'awss3',
      'key' => env('AWS_ACCESS_KEY_ID'),
      'secret' => env('AWS_SECRET_ACCESS_KEY'),
      'bucket' => env('AWS_BUCKET'),
      'region' => env('AWS_DEFAULT_REGION'),
      'version' => env('AWS_VERSION','latest'),
      'public_url_root' => env('AWS_PUBLIC_URL_ROOT',''),
      'public_path' => '/'.env('AWS_UPLOAD_PATH',''),
      'endpoint' => env('AWS_URL'),
      // 'bucket_endpoint' => false,
      // 'calculate_md5' => true,
      // 'scheme' => 'https',
      // 'endpoint' => 'your-url',
      // 'prefix' => 'your-prefix',
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'null' => [
      'driver' => 'null',
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    /*
    'azure' => [
      'driver' => 'azure',
      'account-name' => 'your-account-name',
      'api-key' => 'your-api-key',
      'container' => 'your-container',
      'public_url_root' => 'https://www.yoursite.com/upload/endpoint',
      'public_path' => '/path/to/upload/directory/',
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'dropbox' => [
      'driver' => 'dropbox',
      'token' => 'your-token',
      'public_url_root' => 'https://www.yoursite.com/upload/endpoint',
      'public_path' => '/path/to/upload/directory/',
      // 'prefix' => 'your-prefix',
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'gridfs' => [
      'driver' => 'gridfs',
      'server' => 'mongodb://localhost:27017',
      'database' => 'your-database',
      'public_url_root' => 'https://www.yoursite.com/upload/endpoint',
      'public_path' => '/path/to/upload/directory/',
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'rackspace' => [
      'driver' => 'rackspace',
      'endpoint' => 'your-endpoint',
      'region' => 'your-region',
      'username' => 'your-username',
      'apiKey' => 'your-api-key',
      'container' => 'your-container',
      'public_url_root' => 'https://www.yoursite.com/upload/endpoint',
      'public_path' => '/path/to/upload/directory/',
      // 'internal' => false,
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'replicate' => [
      'driver' => 'replicate',
      'source' => 'your-source-adapter',
      'replica' => 'your-replica-adapter',
      'public_url_root' => 'https://www.yoursite.com/upload/endpoint',
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],

    'webdav' => [
      'driver' => 'webdav',
      'baseUri' => 'http://example.org/dav/',
      'userName' => 'your-username',
      'password' => 'your-password',
      'public_url_root' => 'https://www.yoursite.com/upload/endpoint',
      'public_path' => '/path/to/upload/directory/',
      // 'visibility' => 'public',
      // 'pirate'     => false,
      // 'eventable'  => true,
      // 'cache'      => 'foo'
    ],

    'zip' => [
      'driver' => 'zip',
      'path' => storage_path('files.zip'),
      'public_url_root' => 'https://www.yoursite.com/upload/endpoint',
      'public_path' => '/path/to/upload/directory/',
      // 'visibility' => 'public',
      // 'pirate' => false,
      // 'eventable' => true,
      // 'cache' => 'foo'
    ],
    */
  ],

  /*
  |--------------------------------------------------------------------------
  | Flysystem Cache
  |--------------------------------------------------------------------------
  |
  | Here are each of the cache configurations setup for your application.
  | There are currently two drivers: illuminate and adapter. Examples of
  | configuration are included. You can of course have multiple connections
  | per driver as shown.
  |
  */

  'cache' => [
    'foo' => [
      'driver' => 'illuminate',
      'connector' => null, // null means use default driver
      'key' => 'foo',
      // 'ttl' => 300
    ],

    'bar' => [
      'driver' => 'illuminate',
      'connector' => 'redis', // config/cache.php
      'key' => 'bar',
      'ttl' => 600,
    ],

    'adapter' => [
      'driver' => 'adapter',
      'adapter' => 'local', // as defined in connections
      'file' => 'flysystem.json',
      'ttl' => 600,
    ],

  ],

];
