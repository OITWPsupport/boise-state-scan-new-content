<?php
	# require 'vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
	# require 'vendor/autoload.php';

	// This page is called by the Boise State Scan New Content plugin.
	// That plugin triggers an accessibility scan of each new or updated page and post by sending 
	// a request to this page. This page calls pa11y scanner (running on this server) 
	// and emails the results (if errors are found) to WP Support (and optionally to the site admin).
	 
	// Extract some querystring parameters:
	$target = $_GET["target"]; // The URL of the WP page we'll scan.
	$a11y_contact_email = urldecode($_GET["a11y_contact_email"]);
	$a11y_auto_scan = $_GET["a11y_auto_scan"]; // If this is empty, we'll still email wp-support.

	// This should hold the OIT email address that should receive notifications
	// (most likely wp-support@boisestate.edu).
	$oit_email = "wp-support-group@boisestate.edu";

	// Run the pa11y scanner against $target and store results in $output
	$output = shell_exec("/usr/local/bin/pa11y --ignore 'warning;notice' --reporter csv $target");

	$lines = explode(PHP_EOL, $output);
	$output_array = array();
	foreach ($lines as $line) {
		$output_array[] = str_getcsv($line);
	}

	// We'll search $output for this string to determine whether pa11y found errors:
	$pattern = '/0 errors/';

	// If "0 errors" appears, we are $error_free.
	$error_free = preg_match($pattern, $output);
	if (!$error_free) {
		// The Boise State Accessibility Options menu on WP (which appears once the
		// Scan New Content plugin is activated) has a checkbox that reads 
		// "Automatically scan pages and posts for accessibility issues."
		// If that was checked, we'll send this email to the address they provided AND 
		// to $oit_email. If they didn't check that box, send only to $oit_email. 
		$to = (empty($a11y_auto_scan)) ? $oit_email : $a11y_contact_email;
		$subject = "Accessibility errors: $target";
		$message = "<html>";
		$message .= "<head></head>";
		$message .= "<body>";
		$message .= "The following WordPress content (<a href=\"$target\">$target</a>) was recently added or edited, and contains accessibility errors. Please review this page and consult Siteimprove for details. If you need assistance, please contact wp-support@boisestate.edu.<br /><br />";

		$count = 0;
		$message .= "<ol>";
		foreach ($output_array as $key => $value) {
			if ($count) { // Don't print the first row. It's just headers.
				if (count($value) > 1) {
					unset($array);
					$array = explode(".", $value[1]);
					$error_code = $array[4];
					$message .= "<li><strong>Error:</strong> ";
					$message .= $value[2] . "<br />";
					$message .= $value[1] . "<br />";
					// $message .= $value[4] . " <br />";
					$message .= "https://www.w3.org/WAI/GL/2015/WD-WCAG20-TECHS-20150714/" . $error_code . "<br /><br /></li>";
				}
			}
			$count++;
		}
		$message .= "</ol>";

		$message .= "</body></html>";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		if (!empty($a11y_auto_scan)) { 
			$headers .= "Bcc: $oit_email";
		}
		mail ( $to, $subject, $message, $headers );
	}
	echo $message; // This might be useful if we're testing via the browser.
?>
