<?php

namespace App\Http\Controllers;

class WebController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  public function catchAll($all = null){
    return response('File not found.','404');
  }
  
}
