<?php print '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>{{ $channel->title }}</title>
    <link>{{ $channel->link }}</link>
    <description>{{ $channel->description }}</description>
    @if(!empty($url))
    <atom:link href="{{ $url }}" rel="self" type="application/rss+xml" />
    @endif
    <language>{{ $channel->language }}</language>
    <copyright>Copyright {{ date('Y') }}{{ !empty($channel->copyright_owner) ? ' '.$channel->copyright_owner : '' }}</copyright>
    @foreach($items as $item)
    <item>
      <title>{{ $item->title }}</title>
      <link>https://www.nba.com{{ $item->url }}</link>
      @if($item->type === 'video' || $item->type === 'wsc')
      <description>{{ $item->description }}</description>
      @elseif($item->type === 'article' || $item->type === 'page' || $item->type === 'gallery')
      <description>{{ $item->teaser }}</description>
      @else
      <description>No description given.</description>
      @endif
      @if($item->type === 'video' || $item->type === 'wsc' || $item->type === 'article')
      @if(property_exists($item,'listImage'))
      @if(property_exists($item->listImage,'large'))
      <enclosure url="{{ $item->listImage->large }}" length="0" type="image/jpeg" />
      @elseif(property_exists($item->listImage,'mobile'))
      <enclosure url="{{ $item->listImage->mobile }}" length="0" type="image/jpeg" />
      @elseif(property_exists($item->listImage,'thumbnail'))
      <enclosure url="{{ $item->listImage->thumbnail }}" length="0" type="image/jpeg" />
      @elseif(property_exists($item->listImage,'raw') && property_exists($item->listImage->raw,'url'))
      <enclosure url="{{ $item->listImage->raw->url }}" length="0" type="image/jpeg" />
      @endif
      @endif
      @elseif($item->type === 'gallery')
      @if(property_exists($item,'media') && !empty($item->media))
      @if(property_exists($item->media[0],'mobile'))
      <enclosure url="{{ $item->media[0]->mobile }}" length="0" type="image/jpeg" />
      @elseif(property_exists($item->media[0],'source'))
      <enclosure url="{{ $item->media[0]->source }}" length="0" type="image/jpeg" />
      @elseif(property_exists($item->media[0],'raw') && property_exists($item->media[0]->raw,'url'))
      <enclosure url="{{ $item->media[0]->raw->url }}" length="0" type="image/jpeg" />
      @endif
      @endif
      @endif
      <guid isPermaLink="false">{{ $item->uuid }}</guid>
      <pubDate>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($item->published))->toRssString() }}</pubDate>
    </item>
    @endforeach
  </channel>
</rss>
