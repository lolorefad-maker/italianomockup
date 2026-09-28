<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php echo esc_html( tr( 'Salta al contenuto' ) ); ?></a>
<header class="hdr" id="hdr">
	<div class="wrap">
		<?php echo tr_brand(); // phpcs:ignore -- markup built from escaped values. ?>
		<nav class="nav" aria-label="<?php echo esc_attr( tr( 'Apri il menu' ) ); ?>"><?php tr_nav_desktop(); ?></nav>
		<?php tr_lang_switch(); ?>
		<a class="btn btn-primary btn-sm hdr-cta" href="<?php echo esc_url( tr_contact_url() ); ?>"><?php echo esc_html( tr( 'Richiedi preventivo' ) ); ?></a>
		<button type="button" class="menu-btn" aria-expanded="false" aria-controls="mnav" data-menu data-label-open="<?php echo esc_attr( tr( 'Apri il menu' ) ); ?>" data-label-close="<?php echo esc_attr( tr( 'Chiudi' ) ); ?>">
			<span class="i-open"><?php echo tr_icon( 'menu' ); ?></span><span class="i-close" hidden><?php echo tr_icon( 'close' ); ?></span>
			<span class="sr"><?php echo esc_html( tr( 'Apri il menu' ) ); ?></span>
		</button>
	</div>
</header>
<div class="mnav" id="mnav" hidden>
	<div class="wrap">
		<nav class="mnav-links" aria-label="<?php echo esc_attr( tr( 'Apri il menu' ) ); ?>"><?php tr_nav_mobile(); ?></nav>
		<div class="mnav-cta">
			<a class="btn btn-primary" href="<?php echo esc_url( tr_contact_url() ); ?>"><?php echo esc_html( tr( 'Richiedi un preventivo gratuito' ) ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( tr_wa_url() ); ?>" target="_blank" rel="noopener"><?php echo tr_icon( 'wa' ); ?><?php echo esc_html( tr( 'Scrivimi su WhatsApp' ) ); ?></a>
		</div>
		<p class="mnav-meta"><?php echo tr_ltr( tr_opt( 'tr_phone' ) ); ?> · <?php echo tr_ltr( tr_opt( 'tr_email' ) ); ?></p>
	</div>
</div>
<main id="main" tabindex="-1">
