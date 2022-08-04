<?php

namespace App\Controllers;

use App\Entities\Site;

/**
 * Synchronization Controller Class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Sync extends BaseController
{

	/**
	 * Handle synchronization
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function index()
	{
		helper(['remote', 'messenger', 'network', 'encrypt']);

		// execution window of php script
		define('EXECUTION_WINDOW', intval(getenv('window.period') ?: 1));
		define('EXECUTION_INTERVAL', intval(getenv('window.interval') ?: 1));

		// max execute 110% of window period max
		set_time_limit(max(EXECUTION_WINDOW, ini_get('max_execution_time')) * 1.5);

		$start = time();

		do
		{
			$sites = network_stores();

			foreach ($sites as $site)
			{
				message_add('network', network_get_store());

				$siteEntity = new Site($site);

				do
				{
					remote_get_content($siteEntity->link, $siteEntity->encrypt);
				}
				while (message_has_content());

				$siteEntity->destroy();
			}

			$wait = ((time() - $start) % EXECUTION_INTERVAL) * EXECUTION_INTERVAL;

			// is the wait worth it's salt
			if (time() + $wait < $start + EXECUTION_WINDOW)
			{
				sleep($wait);
			}
			else
			{
				break;
			}
		}
		while (time() < $start + EXECUTION_WINDOW);
	}
}
