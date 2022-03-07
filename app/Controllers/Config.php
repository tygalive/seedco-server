<?php

namespace App\Controllers;

class Config extends BaseController
{
    public function index()
    {

        /**
         * Encryption
         * 
         * Secure Key
         * target url
         * encryption key expiry
         * 
         * encrpt encryption key request with secure
         * encrpt data with encrytion key
         * 
         * 
         * Approach
         * 1. Outside Request
         * validate encrption
         * parse product, orders list (basic list)
         * send stock levels, order diff request
         * save returned order list
         * wait for new data
         * 
         * 2. Inside Request
         * check if key valid (request new)
         * send data request, with known orders
         * return products, order diff
         * send stock levels
         * wait for new data
         * 
         * Data
         * Login with remote e-shop credentials
         * save products remote and local status (reduce unnecessary traffic)
         * 
         */
        $data = array();

        return view('home', $data);
    }
}