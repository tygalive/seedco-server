<?php

namespace App\Controllers;

use App\Entities\Site;

class Sync extends BaseController
{
    public function index()
    {
        helper(array('remote',"messenger", "network", "encrypt"));

        #execution window of php script
        define("EXECUTION_WINDOW", intval(getenv("window.period") ?: 1));
        define("EXECUTION_INTERVAL", intval(getenv("window.interval") ?:1));

        #max execute 110% of window period max
        set_time_limit(EXECUTION_WINDOW * 1.5);

        $start = time();

        do {
            $sites =networkStores() ;

            foreach ($sites as $site) {
                messageAdd("network", $sites);

                $siteEntity = new Site($site);

                do {
                    remoteGetContent($siteEntity->link, $siteEntity->encrypt);
                } while (messageHasContent());

                $siteEntity->destroy();
            }

            $wait  = ((time() - $start) % EXECUTION_INTERVAL) * EXECUTION_INTERVAL;

            #is the wait worth it
            if (time() + $wait < $start + EXECUTION_WINDOW) {
                sleep($wait);
            } else {
                break;
            }
        } while (time() < $start + EXECUTION_WINDOW);
    }
}