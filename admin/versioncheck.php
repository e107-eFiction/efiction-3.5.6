<?php

// ----------------------------------------------------------------------
// eFiction 3.2
// Copyright (c) 2007 by Tammy Keefer
// Valid HTML 4.01 Transitional
// Based on eFiction 1.1
// Copyright (C) 2003 by Rebecca Smallwood.
// http://efiction.sourceforge.net/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

if(!defined("_CHARSET")) exit( );



	$output .= "<div id=\"pagetitle\">"._VERSIONCHECK."</div>";

	// make sure curl is installed
	if (function_exists('curl_init')) {
		$output .= write_message(_RUNNINGVERSION);
	   // initialize a new curl resource
	   $ch = curl_init();

	   // set the url to fetch
	   curl_setopt($ch, CURLOPT_URL, 'http://www.efiction.org/version.txt');

	   // don't give me the headers just the content
	   curl_setopt($ch, CURLOPT_HEADER, 0);

	   // return the value instead of printing the response to browser
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	   // use a user agent to mimic a browser
	   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');

	   $content = curl_exec($ch);
	   $currentversion = $content;
				$output .= write_message(_CURRENTVERSION. "$currentversion");

				if ($currentversion != $version) {
				$output .= write_message (_UPDATEVERSION);
				}
				else $output .= write_message (_UPTODATE);

	   // remember to always close the session and free all resources
	   curl_close($ch);
	}

	else if(ini_get("allow_url_fopen")) {
		$output .= write_message(_RUNNINGVERSION);

		$location = fopen ("http://efiction.org/version.txt", "r");
		$buffer = '';
		while (!feof ($location)) {
			$buffer .= fgets($location, 4096);
		}
		fclose ($location);

		if ($buffer != '') {
			$currentversion = $buffer;
			$output .= write_message(_CURRENTVERSION. "$currentversion");

			if ($currentversion != $version) {
			$output .= write_message (_UPDATEVERSION);
			}
			else $output .= write_message (_UPTODATE);
		}
	}
	else {	
		$output .= write_message(_VERSIONNOTALLOWED);
	}

?>