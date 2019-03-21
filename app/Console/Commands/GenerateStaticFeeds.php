<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GrahamCampbell\Flysystem\Facades\Flysystem;
use Illuminate\Support\Facades\View;

class GenerateStaticFeeds extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'command:generate_static_feeds {--test}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generates static RSS files and uploads them to specified storage locations as defined in config/static.php.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $this->line("\n".'==========================================='."\n\n".'Beginning static feed generation service...'."\n\n".'==========================================='."\n\n");
    // Define testing boolean as false to start
    $testing = false;
    // Check for --test flag and set testing boolean to true if it exists
    if($this->option('test')){
      $testing = true;
      $this->line("\n".'Found test flag. Will cycle through defined feeds to ensure that feed generation and upload will work but will not actually upload any files.'."\n\n");
    }
    // Grab access token from the app config and make sure it's not empty. Without this, we can't access the NBA's Content API, so we should check this first
    $this->line('Checking for access token...');
    $access_token = config('app.access_token');
    if(!empty($access_token)){
      $this->info('Access token found.'."\n\n");
      // Get an array of static feeds from the static config
      $static_feeds = config('static.feeds');
      // Check that the static feeds were returned and at least one array item exists
      $this->line('Checking for static feed definitions.');
      if($static_feeds && !empty($static_feeds)){
        $static_feed_count = count($static_feeds);
        $this->info('Found '.$static_feed_count.' static feed definition'.($static_feed_count === 1 ? '' : 's').'.'."\n");
        // Define a blank array of valid output types for validation later
        $valid_outputs = [];
        // Get defined Flysystem connections from config
        $get_connections = config('flysystem.connections');
        // A valid config would return an array of connection types, so check to see if an array has been returned. If so, add the valid connection types to $valid_outputs
        if(gettype($get_connections) === 'array'){
          $valid_outputs = array_keys($get_connections);
        }
        // Loop through the returned static feed items
        foreach($static_feeds as $feed){
          // Run a series of checks to ensure that enough data exists to successfully request information from the NBA's Content API and at least one output has been provided
          $this->line('Checking that the minimum CAPI data requirements are met and there is at least one output defined.');
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
            $this->info('CAPI data minimum met and at least one output is defined.'."\n");
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
            $this->line('Checking that the given data can be turned into a valid request.');
            if(!empty($api_teams) && !empty($api_types)){
              $this->info('Valid Content API request formed.'."\n");
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
              $this->line('Sending query to Content API.');
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL,$api_request);
              $headers = [$access_token_header];
              curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $api_return = curl_exec($ch);
              curl_close ($ch);
              // If data was returned, continue processing
              if($api_return){
                $this->info('Reponse received from Content API.'."\n");
                // Attempt to decode data into json object
                $api_obj = @json_decode($api_return);
                // Check if decode was successful and if so, also check that there were no errors returned by the Content API
                $this->line('Checking that the Content API response successfully returned content data.');
                if($api_obj !== null && json_last_error() === JSON_ERROR_NONE && !property_exists($api_obj,'error') && property_exists($api_obj,'response') && property_exists($api_obj->response,'result')){
                  $this->info('The Content API response contains content data.'."\n");
                  // Define $items to pass to RSS builder view
                  $items = $api_obj->response->result;
                  // Loop through outputs to create URL-specific feeds and upload to proper locations
                  $output_count = count($feed['rss']['outputs']);
                  $this->line('Found '.$output_count.' output endpoint'.($output_count === 1 ? '' : 's').'. Generating unique RSS feed for each endpoint.'."\n");
                  foreach($feed['rss']['outputs'] as $output_type => $attr){
                    // Check that the provided output has a valid Flysystem connection
                    $this->line('Checking that "'.$output_type.'" is a valid output endpoint.');
                    if(in_array($output_type,$valid_outputs)){
                      $this->info('"'.$output_type.'" is a valid output endpoint.'."\n");
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
                      $feed = View('rss')->with('channel',$channel)->with('items',$items)->with('url',$url);
                      $this->line($filename. ' generated for "'.$output_type.'" endpoint.'."\n");
                      // Start Flysystem connection with specified type
                      $filesystem = Flysystem::connection($output_type);
                      // Check whether we are in a testing environment. If so, run connection tests. If not, attempt to upload files
                      if($testing){
                        $this->line('Attempting to connect to "'.$output_type.'" endpoint. This script is running in test mode so no actual uploads will occur.');
                        // Use try/catch logic to try and connect to the current endpoint and gracefully report an error on fail instead of throwing an exception and halting the entire function
                        try {
                          if($output_type === 'local' || $filesystem->getAdapter()->getConnection()){
                            // Connected to the endpoint, let the people know!
                            $this->info('Successfully connected to the to the "'.$output_type.'" endpoint.'."\n");
                          }
                        }
                        catch (\Exception $e){
                          // Could not connect to endpoint, return an error to the console and continue
                          $this->error('Could not establish a connection to the "'.$output_type.'" endpoint.');
                          $this->error($e->getMessage()."\n");
                        }
                      }
                      else{
                        $this->line('Attempting to upload '.$filename.' to "'.$output_type.'" endpoint.');
                        // Use try/catch logic to try and upload the generated feed to the current endpoint and gracefully report an error on fail instead of throwing an exception and halting the entire function
                        try {
                          $upload = $filesystem->put($url_path.$filename, $feed);
                          // Got past the upload without throwing an exception, which means credentials and connection was valid
                          if($upload){
                            // Successfully uploaded to the endpoint, let the people know!
                            $this->info('Uploaded '.$filename.' to '.$url.'!'."\n");
                          }
                          else {
                            // Connected to the endpoint but could not upload the file - this will likely only get reached on a timeout
                            $this->error('Could not upload '.$filename.'.'."\n");
                          }
                        }
                        catch (\Exception $e){
                          // Could not connect to endpoint, return an error to the console and continue
                          $this->error('Could not establish a connection to the "'.$output_type.'" endpoint.');
                          $this->error($e->getMessage()."\n");
                        }
                      }
                    }
                    else{
                      $this->error('"'.$output_type.'" is not a valid output endpoint. Skipping feed generation.'."\n");
                    }
                  }
                }
                else{
                  $this->error('The Content API was reached but returned an error.'."\n");
                }
              }
              else{
                $this->error('Could not reach Content API.'."\n");
              }
            }
            else{
              $this->error('Could not generate a valid Content API query with the given data.'."\n");
            }
          }
          else{
            $this->error('Not enough information supplied to begin feed generation process.'."\n");
          }
          $this->line("\n");
        }
      }
      else{
        $this->error('No static feeds have been defined.'."\n");
      }
    }
    else{
      $this->error('Access token not found.'."\n");
    }
    $this->info('Done.');
  }
}
