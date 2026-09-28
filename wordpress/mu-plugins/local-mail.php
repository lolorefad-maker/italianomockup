<?php
/**
 * Plugin Name: Local mail (Mailpit)
 * Description: Local development only. Sends all site email to the Mailpit container (http://localhost:8025). Delete this file on the live server and use WP Mail SMTP instead.
 */
add_action( 'phpmailer_init', function ( $mailer ) {
	$mailer->isSMTP();
	$mailer->Host     = 'mailpit';
	$mailer->Port     = 1025;
	$mailer->SMTPAuth = false;
	$mailer->SMTPAutoTLS = false;
} );
