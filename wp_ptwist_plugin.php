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

        $value = 'something from somewhere';

        setcookie("Test67Cookie", $value, time()+3600);  /* expire in 1 hour */
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

?>
