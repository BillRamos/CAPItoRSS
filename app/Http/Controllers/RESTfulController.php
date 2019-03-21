<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RESTfulController extends Controller
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

}
