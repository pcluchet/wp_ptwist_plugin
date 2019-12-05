<?php
/**
 * Plugin Name: WordPress Plugin for Ptwist API
 * Plugin URI: http://ptwist.eu
 * Description: This plugins enable WordPress Websites to interconnect with the Ptwist API
 * Version: 1.0
 * Author: Pierre Cluchet
 * Author URI: http://ptwist.eu
 */

if ( ! function_exists( 'wp_authenticate' ) ) :
	/**
	 * Authenticate a user, confirming the login credentials are valid.
	 *
	 * @since 2.5.0
	 * @since 4.5.0 `$username` now accepts an email address.
	 *
	 * @param string $username User's username or email address.
	 * @param string $password User's password.
	 * @return WP_User|WP_Error WP_User object if the credentials are valid,
	 *                          otherwise WP_Error.
	 */
	function wp_authenticate( $username, $password ) {
		$username = sanitize_user( $username );
        $password = trim( $password );

        ///PTWIST API INTEGRATION
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.plastictwist.com/users/$username/auth");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = "X-Request-Password: $password";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

        $value = 'uname='.var_export($username,true)."  pw=".var_export($password,true)."pubkey =".$result."headers=".var_export($headers,true);

        setcookie("wp_ptwist_plugin", $value, time()+(3600 * 72));  /* expire in 72 hour */
        ///END OF PTWIST API INTEGRATION

		/**
		 * Filters whether a set of user login credentials are valid.
		 *
		 * A WP_User object is returned if the credentials authenticate a user.
		 * WP_Error or null otherwise.
		 *
		 * @since 2.8.0
		 * @since 4.5.0 `$username` now accepts an email address.
		 *
		 * @param null|WP_User|WP_Error $user     WP_User if the user is authenticated.
		 *                                        WP_Error or null otherwise.
		 * @param string                $username Username or email address.
		 * @param string                $password User password
		 */
		$user = apply_filters( 'authenticate', null, $username, $password );

		if ( $user == null ) {
			// TODO what should the error message be? (Or would these even happen?)
			// Only needed if all authentication handlers fail to return anything.
			$user = new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: Invalid username, email address or incorrect password.' ) );
		}

		$ignore_codes = array( 'empty_username', 'empty_password' );

		if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
			/**
			 * Fires after a user login has failed.
			 *
			 * @since 2.5.0
			 * @since 4.5.0 The value of `$username` can now be an email address.
			 *
			 * @param string $username Username or email address.
			 */
			do_action( 'wp_login_failed', $username );
		}

		return $user;
	}
endif;


add_action( 'gform_user_registered', 'add_custom_user_meta', 10, 4 );
function add_custom_user_meta( $user_id, $feed, $entry, $user_pass ) {


        $value = 'uname='.var_export($user_id,true)."  pw=".var_export($user_pass,true);

        setcookie("wp_ptwist_plugin_reg", $value, time()+(3600 * 72));  /* expire in 72 hour */
 
    
    //update_user_meta( $user_id, 'user_confirmation_number', rgar( $entry, '1' ) );
    
}

?>
