<?php
// Set the base URL.
// Leave blank for autodetect.
$config['BASEURL'] = '';


// Set the BASEURL automatically if none was provided
if ($config['BASEURL'] == '') {
	if (isset($_SERVER['HTTP_HOST'])) {
		$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$base_url .= '://'. $_SERVER['HTTP_HOST'];
		$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
	} else {
		$base_url = 'http://localhost/';
	}

	$config['BASEURL'] = $base_url;
	define('BASEURL', $base_url);
} else {
	define('BASEURL', $config['BASEURL']);
}
