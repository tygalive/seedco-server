<?php

namespace App\Models;

use CodeIgniter\Model;

class Stock extends Model
{
    protected $table         = 'ICILOC';
    protected $allowedFields = [];
    protected $returnType    = 'App\Entities\Stock';
    protected $useTimestamps = false;

    public function getStocks($locations = array())
    {

        $this->select(["ICILOC.ITEMNO", "LOCATION", "QTYONHAND", "CURRENCY", "UNITPRICE"]);

        $this->join("ICPRICP", "ICILOC.ITEMNO=ICPRICP.ITEMNO", "inner");
        $this->where("LOCATION in ('" . implode("', '", $locations) . "')");
        $this->where("QTYONHAND >= 0");
        $this->where("DPRICETYPE ='1'");
        $this->where("PRICELIST = 'R'");
        $this->where("CURRENCY='ZMK'");

        /**
         * add database
         * 
         * *add select
         * *add join clause
         * *Add where clause
         * 
         * 
         */

        /**
         * SELECT $lim ICILOC.ITEMNO, LOCATION, QTYONHAND, CURRENCY, UNITPRICE FROM ICILOC INNER JOIN ICPRICP ON ICILOC.ITEMNO=ICPRICP.ITEMNO WHERE LOCATION in ('" . implode("', '", $locations) . "') AND QTYONHAND >= 0 AND DPRICETYPE ='1' AND PRICELIST = 'R' AND CURRENCY='ZMK'  ORDER BY LOCATION, ITEMNO
         * 
         * SELECT $lim  ICILOC.ITEMNO, LOCATION, QTYONHAND, CURRENCY, UNITPRICE FROM ICILOC  INNER JOIN ICPRICP ON ICILOC.ITEMNO=ICPRICP.ITEMNO  WHERE LOCATION IN ('" . implode("', '", $locations) . "') AND QTYONHAND >= 0 AND (LEN(ICILOC.ITEMNO)>=7 AND LEN(ICILOC.ITEMNO)<=9) AND DPRICETYPE='1' AND PRICELIST='DEFPRC' AND CURRENCY='ZMW' ORDER BY ITEMNO, LOCATION "
         */

        $client = \Config\Services::curlrequest();

        $headers = array(
            "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language" => "en-us,en;q=0.5",
            "Accept-Charset" => "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
            "Keep-Alive" => "115",
            "Connection" => "keep-alive",
            "Cache-Control" => "max-age=0",
        );

        $response  = $client->get($this->url, array(
            'headers' => $headers,
            'user_agent' => "Zimrate/1.0",
            'verify' => false
        ));

        $this->site = ($response->getStatusCode() < 400) ? $this->site = $response->getJSON() : false;
    }
}