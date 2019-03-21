<?php

return [

  /*
  |--------------------------------------------------------------------------
  | RSS Output Configuration
  |--------------------------------------------------------------------------
  |
  | Here are the default values to be passed along to the RSS builder view as
  | object attributes. You can set the default values for these attributes in
  | your .env file.
  |
  | If you are using the static file generation service, the values provided
  | for each feed in config/static.php will, if defined, override these
  | default values.
  |
  | If you are using the RESTful API service, the values provided in your
  | .env file will be used for every generated feed UNLESS you pass along
  | new values in the request URL as query strings. The query strings will
  | not be passed to the NBA'S Content API but will be used to customize
  | the returned RSS feed. The query string keys are defined below. I don't
  | recommend exposing these query strings to the public, as someone could
  | change the title, description, copyright info, etc. in the returned RSS
  | feed and use that feed as their own - if you care about that.
  |
  | The attributes are:
  | - title
  |   - Defines the title of the returned RSS feed
  |   - .env value: RSS_DEFAULT_TITLE
  |   - RESTful query string key: t (ex. t=My News Feed)
  | - link
  |   - Defines the URL of the channel owner's site in the returned RSS feed
  |   - .env value: RSS_DEFAULT_LINK
  |   - RESTful query string key: l (ex. l=https://www.nba.com/)
  | - copyright_owner
  |   - Defines the owner of the RSS feed's copyright
  |   - .env value: RSS_DEFAULT_COPYRIGHT_OWNER
  |   - RESTful query string key: c (ex. c=Team Name)
  | - description
  |   - Defines the description of the RSS feed
  |   - .env value: RSS_DEFAULT_DESCRIPTION
  |   - RESTful query string key: d (ex. d=The most recent news from my site)
  | - language
  |   - Defines the language of the RSS feed
  |   - .env value: RSS_DEFAULT_LANGUAGE
  |   - RESTful query string key: la (ex. la=en-us)
  |
  */

  'title' => env('RSS_DEFAULT_TITLE','Recent Content'),

  'link' => env('RSS_DEFAULT_LINK',''),

  'copyright_owner' => env('RSS_DEFAULT_COPYRIGHT_OWNER',''),

  'description' => env('RSS_DEFAULT_DESCRIPTION','The most recent content from our site.'),

  'language' => env('RSS_DEFAULT_LANGUAGE','en-us'),

];
