<?php
/*
Plugin Name: Boise State Scan New Content
Plugin URI: https://github.com/OITWPsupport/boise-state-scan-new-content/releases/latest
Description: Triggers an accessibility scan of each new or updated page and post. Adds an admin menu and form for maintaining associated info.
Version: 1.0.1
Author: David Lentz
Author URI: https://webguide.boisestate.edu/
 */

defined( 'ABSPATH' ) or die( 'No hackers' );

if( ! class_exists( 'Boise_State_Scan_New_Content_Plugin_Updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}

$updater = new Boise_State_Scan_New_Content_Plugin_Updater( __FILE__ );
$updater->set_username( 'OITWPsupport' );
$updater->set_repository( 'boise-state-scan-new-content' );
$updater->initialize();


////////////////////////////////////////////////////////////
// This section calls a remote URL that scans the new or updated
// WordPress page.
////////////////////////////////////////////////////////////

function boise_state_scan_new_content( $ID ){

	// scancsv.php is included in this repository. 
	// It needs to be placed somewhere outside WordPress, on any available PHP server.
	$scanner_url = 'http://52.201.144.56/scancsv.php';
	$admin_email = get_option( 'admin_email' );
	$a11y_contact_email = get_option( 'a11y_contact_email' );
	$a11y_auto_scan = get_option( 'a11y_auto_scan' );

	// Mail is being sent by the remote server upon receiving this request.
	// We're not using wp_mail or PHP mail on our WP instance to send this mail.
	$url = $scanner_url;
	$url .= '?target=' . get_permalink( $ID );
	$url .= '&a11y_contact_email=' . $a11y_contact_email;
	$url .= '&a11y_auto_scan=' . $a11y_auto_scan;
	$response = wp_remote_get( $url );

}

add_filter('publish_post', 'boise_state_scan_new_content');
add_filter('publish_page', 'boise_state_scan_new_content');

////////////////////////////////////////////////////////////
// This section creates a WordPress menu under Settings 
// to manage accessibility ("a11y") options
////////////////////////////////////////////////////////////
 
// add_options_page ( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
function boise_state_a11y_custom_admin_menu() {
    add_options_page(
        'Boise State a11y options',
        'Boise State a11y options',
        'manage_options',
        'bsu-a11y-plugin',
        'boise_state_a11y_options_page'
    );
}

function boise_state_a11y_options_page() {

   // Check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	// Set the names of the form fields:
	$opt_name1 = 'a11y_contact_email';
	$opt_name2 = 'a11y_auto_scan';

	// The message to show when the form is submitted. Starts empty.
	$submitMessage = '';

	$opt_val2 = 0;
	
	// If the form was submitted, update WordPress with the new values.
	if (isset($_POST["submitted"]) && $_POST["submitted"] == '1') {
		$opt_val1 = $_POST[$opt_name1];
		update_option ($opt_name1, $opt_val1);
		$opt_val2 = isset($_POST[$opt_name2]) ? 1 : 0;
		update_option ($opt_name2, $opt_val2);
		$submitMessage = "Settings updated. Thank you.";
	}

	// Get values from WordPress to populate the form.
	$opt_val1 = get_option( $opt_name1 );
	if (empty($opt_val1)) { 
		$opt_val1 = get_option( 'admin_email' );
	}
	$opt_val2 = get_option( $opt_name2 );

    ?>
    <div class="wrap">
        <h2>Boise State Accessibility Options</h2>
	<strong><?php echo $submitMessage; ?></strong>
	<form method="post" action="">
	<input type="hidden" name="submitted" value=1>
        <input type="checkbox" name="<?php echo $opt_name2; ?>" value=1 <?php if($opt_val2 == 1){ echo " checked"; } ?>>Automatically scan pages and posts for accessibility errors<br />
	Send notice of errors to: <input type="text" name="<?php echo $opt_name1; ?>" value="<?php echo $opt_val1; ?>" size=50><br />
	<input type="submit" value="Submit">
	</form>
    </div>
    <?php
}

// Comment out this line to disable the entire Boise State A11y Options menu
add_action( 'admin_menu', 'boise_state_a11y_custom_admin_menu' );

