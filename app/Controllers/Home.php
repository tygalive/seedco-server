<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {

        $data = array();

        return view('home', $data);
    }
}
