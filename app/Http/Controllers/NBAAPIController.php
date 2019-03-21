<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GrahamCampbell\Flysystem\Facades\Flysystem;
use Illuminate\Support\Facades\View;

class NBAAPIController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(){
    //
  }

  public function returnRSS($endpoint = null, Request $request){
    // Ensure that there is a API endpoint path given
    if(!empty($endpoint)){
      // Grab access token from the app config and make sure it's not empty. Without this, we can't access the NBA's Content API, so we should check this early
      $access_token = config('app.access_token');
      if(!empty($access_token)){
        // Define access token header to pass along in curl
        $access_token_header = 'accessToken: '.$access_token;
        // Define default channel attributes to be passed to the RSS builder
        $channel = (object)[
          'title' => config('rss.title'),
          'link' => config('rss.link'),
          'copyright_owner' => config('rss.copyright_owner'),
          'description' => config('rss.description'),
          'language' => config('rss.language')
        ];
        // Define base API request URL
        $api_request = 'https://api.nba.net/2/'.$endpoint;
        // If query strings have been provided, loop through them and add to request URL
        if(!empty($request->request)){
          // Define an array of query string keys that are used for feed formatting and should not be passed to the NBA's API and map to the associated $channel object property
          $rss_qs = [
            't' => 'title',
            'l' => 'link',
            'c' => 'copyright_owner',
            'd' => 'description',
            'la' => 'language'
          ];
          $is_first = true;
          foreach($request->request as $key => $value){
            // Check if current query string key exists in $rss_qs, and if so, replace the associated $channel attribute with the new value
            if(array_key_exists($key,$rss_qs)){
              $attr = $rss_qs[$key];
              $channel->$attr = $value;
            }
            // Otherwise, if this is the first CAPI query string, prepend key=value with ? and add to the $api_request url
            elseif($is_first){
              $api_request .= '?'.$key.'='.$value;
              $is_first = false;
            }
            // This isn't the first CAPI query string, so prepend key=value with & and add to the $api_request url
            else {
              $api_request .= '&'.$key.'='.$value;
            }
          }
        }
        // Create curl request with $access_token_header http header
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_request);
        $headers = [$access_token_header];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $api_return = curl_exec($ch);
        curl_close ($ch);
        // If data was returned, continue processing
        if($api_return){
          // Attempt to decode data into json object
          $api_obj = @json_decode($api_return);
          // Check if decode was successful and if so, also check that there were no errors returned by the Content API
          if($api_obj !== null && json_last_error() === JSON_ERROR_NONE && !property_exists($api_obj,'error') && property_exists($api_obj,'response') && property_exists($api_obj->response,'result')){
            // Get full URL of the current request to append to RSS feed
            $url = $request->fullUrl();
            // Define $items to pass to RSS builder view
            $items = $api_obj->response->result;
            // Build view (rss.blade.php)
            $feed = View('rss')->with('channel',$channel)->with('items',$items)->with('url',$url);
            // Return the feed with proper headers
            return response($feed,'200')->header('Content-Type','text/xml');
          }
        }
      }
      return 'No CAPI access token provided.';
    }
    // No API endpoint was provided
    return 'No endpoint provided.';
  }
  public function debug(){
    $static_feeds = config('static.feeds');
    $valid_connections = [];
    $get_connections = config('flysystem.connections');
    if(gettype($get_connections) === 'array'){
      $valid_connections = array_keys($get_connections);
    }
    dd($valid_connections);


    // Grab access token from the app config and make sure it's not empty. Without this, we can't access the NBA's Content API, so we should check this first
    $access_token = config('app.access_token');
    if(!empty($access_token)){
      // Get an array of static feeds from the static config
      $static_feeds = config('static.feeds');
      // Check that the static feeds were returned and at least one array item exists
      if($static_feeds && !empty($static_feeds)){
        // Loop through the returned static feed items
        foreach($static_feeds as $feed){
          // Run a series of checks to ensure that enough data exists to successfully request information from the NBA's Content API and at least one output has been provided
          if(
            // Check that the root "api" attribute is defined
            array_key_exists('api',$feed)
            // Check that the "api" attribute has a nested "teams" attribute
            && array_key_exists('teams',$feed['api'])
            // Check that the nested "teams" attribute is not empty
            && !empty($feed['api']['teams'])
            // Check that the "api" attribute has a nested "content_types" attribute
            && array_key_exists('content_types',$feed['api'])
            // Check that the nested "content_types" attribute is not empty
            && !empty($feed['api']['content_types'])
            // Check that the root "rss" attribute is defined
            && array_key_exists('rss',$feed)
            // Check that the "rss" attribute has a nested "outputs" attribute
            && array_key_exists('outputs',$feed['rss'])
            // Check that the nested "outputs" attribute is not empty
            && !empty($feed['rss']['outputs'])
          ){
            // Check the type of the nested "teams" attribute. It should be an array, and if it's an array, implode with a glue string of "," to define $api_teams
            if(gettype($feed['api']['teams']) === 'array'){
              $api_teams = implode(',',$feed['api']['teams']);
            }
            // If you messed up and defined the nested "teams" attribute as a string, that's okay - we'll pass the string along to define $api_teams
            elseif(gettype($feed['api']['teams']) === 'string'){
              $api_teams = $feed['api']['teams'];
            }
            // Check the type of the nested "content_types" attribute. It should be an array, and if it's an array, implode with a glue string of "," to define $api_types
            if(gettype($feed['api']['content_types']) === 'array'){
              $api_types = implode(',',$feed['api']['content_types']);
            }
            // If you messed up and defined the nested "content_types" attribute as a string, that's okay - we'll pass the string along to define $api_types
            elseif(gettype($feed['api']['content_types']) === 'string'){
              $api_types = $feed['api']['content_types'];
            }
            // Check that both critical Content API values were generated ($api_teams and $api_types)
            if(!empty($api_teams) && !empty($api_types)){
              // We have enough data to create a valid Content API request, so define access token header to pass along in curl
              $access_token_header = 'accessToken: '.$access_token;
              // Define the Content API request with defined paths
              $api_request = 'https://api.nba.net/2/'.$api_teams.'/'.$api_types;
              // Check if there are any defined Content API query strings and if there are, loop through to append to the $api_request url
              if(
                // Check that the "api" attribute has a nested "query_strings" attribute
                array_key_exists('query_strings',$feed['api'])
                // Check that the nested "query_strings" attribute is not empty
                && !empty($feed['api']['query_strings'])
              ){
                // Loop through Content API query strings
                foreach($feed['api']['query_strings'] as $key => $value){
                  // This is the first query string, so prepend key=value with ? and add to the $api_request url
                  if($key === key($feed['api']['query_strings'])){
                    $api_request .= '?'.$key.'='.$value;
                  }
                  // This isn't the first query string, so prepend key=value with & and add to the $api_request url
                  else {
                    $api_request .= '&'.$key.'='.$value;
                  }
                }
              }
              // Define default channel attributes to be passed to the RSS builder
              $channel = (object)[
                'title' => config('rss.title'),
                'link' => config('rss.link'),
                'copyright_owner' => config('rss.copyright_owner'),
                'description' => config('rss.description'),
                'language' => config('rss.language')
              ];
              // Check if there are any defined channel configuration values and if there are, loop through and replace the values in $channel
              if(
                // Check that the "rss" attribute has a nested "config" attribute
                array_key_exists('config',$feed['rss'])
                // Check that the nested "config" attribute is not empty
                && !empty($feed['rss']['config'])
              ){
                // Loop through configuration values and replace existing $channel attributes with feed-specific values
                foreach($feed['rss']['config'] as $key => $value){
                  $channel->$key = $value;
                }
              }
              // Create curl request with $access_token_header http header
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL,$api_request);
              $headers = [$access_token_header];
              curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $api_return = curl_exec($ch);
              curl_close ($ch);
              // If data was returned, continue processing
              if($api_return){
                // Attempt to decode data into json object
                $api_obj = @json_decode($api_return);
                // Check if decode was successful and if so, also check that there were no errors returned by the Content API
                if($api_obj !== null && json_last_error() === JSON_ERROR_NONE && !property_exists($api_obj,'error') && property_exists($api_obj,'response') && property_exists($api_obj->response,'result')){
                  // Define $items to pass to RSS builder view
                  $items = $api_obj->response->result;
                  // Loop through outputs to create URL-specific feeds and upload to proper locations
                  foreach($feed['rss']['outputs'] as $output_type => $attr){
                    // Define generic feed filename
                    $filename = 'feed.rss';
                    // Check if filename is specified for the output type - it should be - and make sure the filename is not blank
                    if(array_key_exists('filename',$attr) && !empty($attr['filename'])){
                      // Set $filename to specified filename for output type
                      $filename = $attr['filename'];
                    }
                    // Define URL as a blank string to start
                    $url = '';
                    // Grab public url root of this output type from the Flysystem config
                    $public_root = config('flysystem.connections.'.$output_type.'.public_url_root');
                    // Grab default upload URL path from config
                    $url_path = config('flysystem.connections.'.$output_type.'.public_path');
                    // If default upload URL path is empty, set it to ""
                    if(empty($url_path)){
                      $url_path = '';
                    }
                    // Check to see if a particular upload URL path was specified in this feed config and if so, overwrite default
                    if(array_key_exists('upload_path',$attr)){
                      // Check if first character of given upload path is "/", and if so, remove it
                      if($attr['upload_path'][0] === '/'){
                        $attr['upload_path'] = substr($attr['upload_path'],1);
                      }
                      // Check if last character of given upload path is "/", and if not, add it
                      if(substr($attr['upload_path'],-1) !== '/'){
                        $attr['upload_path'] = $attr['upload_path'].'/';
                      }
                      $url_path = $attr['upload_path'];
                    }
                    // Check to see if the public url root was defined
                    if(!empty($public_root)){
                      // Check if the last character of the public root is "/" and if not, add it before combining root and path
                      if(substr($public_root,-1) !== '/'){
                        $public_root = $public_root.'/';
                      }
                      $url = $public_root.$url_path.$filename;
                    }
                    // Build view (rss.blade.php)
                    $feed = View('rss')->with('channel',$channel)->with('items',$items)->with('url',$url)->header('Content-Type','text/xml');
                    // Start Flysystem connection with specified type
                    $filesystem = Flysystem::connection($output_type);
                    // Upload file to specified location
                    $upload = $filesystem->put($url_path.$filename, $feed);
                    if($upload){
                      echo 'Uploaded '.$filename.' to '.$url.' ('.$output_type.' storage)<br>';
                    }
                    else {
                      echo 'Could not upload '.$filename.' to '.$output_type.' storage.<br>';
                    }
                  }
                  return 'Looped through endpoints. If you\'re seeing this, check the endpoints specified to see if files were uploaded.';
                }
                return 'The Content API was reached but returned an error.';
              }
              return 'Could not reach Content API.';
            }
            return 'Not enough information supplied to query Content API.';
          }
          return 'Not enough information supplied to begin feed generation process.';
        }
      }
      return 'No static feeds have been defined.';
    }
    return 'No CAPI access token provided.';
  }

}
