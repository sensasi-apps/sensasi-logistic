<?php

namespace App\Http\Controllers;

class AppSystemController extends Controller
{
  public function ipAddrIndex()
  {
    return view('pages.system.ip-addr');
  }
}
