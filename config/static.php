<?php

return [
  'feeds' => [

    [
      'api' => [
        'teams' => ['celtics','hawks','blazers'],
        'content_types' => ['video','article'],
        'query_strings' => [
          'count' => '30',
          'sort' => 'rel'
        ]
      ],
      'rss' => [
        'config' => [
          'title' => 'Title For This Feed',
          'link' => 'https://www.nba.com/',
          'copyright_owner' => 'Testy Testerton',
          'description' => 'This is a description of the feed to be shown in the RSS feed.',
          'language' => 'en-us'
        ],
        'outputs' => [
          'ftp' => [
            'upload_path' => 'rss/test',
            'filename' => 'newsfeed.rss'
          ],
          'local' => [
            'upload_path' => 'static',
            'filename' => 'localfeed.rss'
          ]
        ]
      ]
    ],

  ]
];
