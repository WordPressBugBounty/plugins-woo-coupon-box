<?php
/**
 * Function include all files in folder
 *
 * @param $path   Directory address
 * @param $ext    array file extension what will include
 * @param $prefix string Class prefix
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'vi_include_folder' ) ) {
	function vi_include_folder( $path, $prefix = '', $ext = array( 'php' ) ) {

		/*Include all files in payment folder*/
		if ( ! is_array( $ext ) ) {
			$ext = explode( ',', $ext );
			$ext = array_map( 'trim', $ext );
		}
		$sfiles = scandir( $path );
		foreach ( $sfiles as $sfile ) {
			if ( $sfile != '.' && $sfile != '..' ) {
				if ( is_file( $path . "/" . $sfile ) ) {
					$ext_file  = pathinfo( $path . "/" . $sfile );
					$file_name = $ext_file['filename'];
					if ( $ext_file['extension'] ) {
						if ( in_array( $ext_file['extension'], $ext ) ) {
							$class = preg_replace( '/\W/i', '_', $prefix . ucfirst( $file_name ) );

							if ( ! class_exists( $class ) ) {
								require_once $path . $sfile;
								if ( class_exists( $class ) ) {
									new $class();
								}
							}
						}
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'woocommerce_version_check' ) ) {
	function woocommerce_version_check( $version = '3.0' ) {
		global $woocommerce;

		if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wcb_prepare_subscribe_email' ) ) {
	/**
	 * Sanitize, normalize, and reject wildcard-capable subscribe emails.
	 *
	 * @param mixed $raw Raw email from request (may be slashed).
	 * @return string Normalized email, or empty string when invalid.
	 */
	function wcb_prepare_subscribe_email( $raw ) {
		$email = '';
		if ( is_string( $raw ) || is_numeric( $raw ) ) {
			$email = sanitize_email( wp_unslash( (string) $raw ) );
			$email = strtolower( trim( $email ) );
		}
		if ( '' === $email || false !== strpos( $email, '*' ) || ! is_email( $email ) ) {
			$email = '';
		}

		$email = (string) apply_filters( 'wcb_prepare_subscribe_email', $email, $raw );
		if ( '' === $email || false !== strpos( $email, '*' ) || ! is_email( $email ) ) {
			$email = '';
		}
		do_action( 'wcb_prepared_subscribe_email', $email, $raw );

		return $email;
	}
}

if ( ! function_exists( 'wcb_subscribe_rate_limited' ) ) {
	/**
	 * Whether subscribe is rate-limited for IP and/or email.
	 *
	 * @param string $ip    Client IP.
	 * @param string $email Subscriber email (may be empty before validation).
	 * @return bool True when over limit.
	 */
	function wcb_subscribe_rate_limited( $ip, $email = '' ) {
		$window = (int) apply_filters( 'wcb_subscribe_rate_window', HOUR_IN_SECONDS );
		if ( $window < 60 ) {
			$window = HOUR_IN_SECONDS;
		}
		$ip_limit    = (int) apply_filters( 'wcb_subscribe_rate_limit_ip', 10 );
		$email_limit = (int) apply_filters( 'wcb_subscribe_rate_limit_email', 5 );

		$over = false;
		if ( $ip && $ip_limit > 0 ) {
			$ip_key = 'wcb_rl_ip_' . md5( (string) $ip );
			$count  = (int) get_transient( $ip_key );
			if ( $count >= $ip_limit ) {
				$over = true;
			} else {
				set_transient( $ip_key, $count + 1, $window );
			}
		}
		$email = is_string( $email ) ? strtolower( trim( $email ) ) : '';
		if ( $email && is_email( $email ) && $email_limit > 0 ) {
			$em_key = 'wcb_rl_em_' . md5( $email );
			$count  = (int) get_transient( $em_key );
			if ( $count >= $email_limit ) {
				$over = true;
			} elseif ( ! $over ) {
				set_transient( $em_key, $count + 1, $window );
			}
		}

		$over = (bool) apply_filters( 'wcb_subscribe_rate_limited', $over, $ip, $email );
		do_action( 'wcb_subscribe_rate_limited_checked', $over, $ip, $email );

		return $over;
	}
}