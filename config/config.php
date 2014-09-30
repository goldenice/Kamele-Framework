<?php
// Set the base URL.
// Leave blank for autodetect.
$config['BASEURL'] = '';


// Set the BASEURL automatically if none was provided
if ($config['BASEURL'] == '') {
	if (isset($_SERVER['HTTP_HOST'])) {
		$config['BASEURL'] = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$config['BASEURL'] .= '://'. $_SERVER['HTTP_HOST'];
		$config['BASEURL'] .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
	} 
	else {
		$config['BASEURL'] = 'http://localhost/';
	}

	define('BASEURL', $config['BASEURL']);
} 
else {
	define('BASEURL', $config['BASEURL']);
}
