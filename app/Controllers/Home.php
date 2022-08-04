<?php

/**
 * Home Controller File
 */

namespace App\Controllers;

/**
 * Home Controller Class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Home extends BaseController
{

	/**
	 * Display landing page
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return mixed
	 */
	public function index()
	{
		$data = [];

		return view('home', $data);
	}
}
