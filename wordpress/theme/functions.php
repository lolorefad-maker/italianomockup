<?php
/**
 * Traduttore — bilingual (Italian / Arabic) theme for an Arabic–Italian translator.
 * Page content is made of editable blocks; header, footer, WhatsApp button and
 * language switch come from this file, menus, the Customizer and Polylang strings.
 */
defined( 'ABSPATH' ) || exit;

define( 'TR_VER', '1.0.0' );

/* ---------- Strings (translated in Lingue › Traduzioni stringhe) ---------- */

function tr_strings() {
	return array(
		'Salta al contenuto',
		'Apri il menu',
		'Chiudi',
		'Lingua',
		'Home',
		'Servizi',
		'Richiedi preventivo',
		'Richiedi un preventivo gratuito',
		'Scrivimi su WhatsApp',
		'WhatsApp',
		'Buongiorno, vorrei un preventivo per una traduzione.',
		'Lo studio',
		'Contatti',
		'Traduzioni, interpretariato e mediazione linguistico-culturale tra arabo e italiano. Lezioni di italiano per arabofoni.',
		'Studio a [Città] · online in tutta Italia',
		'Lun–Ven · 9:00–18:00',
		'P.IVA',
		'Con sede in Italia',
	);
}

function tr( $s ) {
	return function_exists( 'pll__' ) ? pll__( $s ) : $s;
}

/* ---------- Setup ---------- */

function tr_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap';
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'disable-layout-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/editor.css', tr_fonts_url() ) );
	register_nav_menus( array(
		'primary' => 'Menu principale',
		'footer'  => 'Footer – Servizi',
		'legal'   => 'Footer – Lo studio',
	) );
} );

add_action( 'init', function () {
	register_block_style( 'core/button', array( 'name' => 'ghost', 'label' => 'Contorno' ) );
	register_block_style( 'core/button', array( 'name' => 'whatsapp', 'label' => 'WhatsApp' ) );
	if ( function_exists( 'pll_register_string' ) ) {
		foreach ( tr_strings() as $s ) {
			pll_register_string( sanitize_title( mb_substr( $s, 0, 40 ) ), $s, 'Tema Traduttore', mb_strlen( $s ) > 60 );
		}
	}
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'tr-fonts', tr_fonts_url(), array(), null );
	wp_enqueue_style( 'tr-style', get_stylesheet_uri(), array( 'tr-fonts' ), (string) filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_script( 'tr-site', get_theme_file_uri( 'assets/site.js' ), array(), (string) filemtime( get_theme_file_path( 'assets/site.js' ) ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	// The theme styles every block itself.
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles' ) as $h ) {
		wp_dequeue_style( $h );
	}
}, 20 );

// In the editor, write each page in its own direction (Italian LTR, Arabic RTL), whatever the admin language.
add_filter( 'block_editor_settings_all', function ( $settings, $context ) {
	if ( ! empty( $context->post ) && function_exists( 'pll_get_post_language' ) ) {
		$lang = pll_get_post_language( $context->post->ID );
		if ( $lang ) {
			$dir                  = 'ar' === $lang ? 'rtl' : 'ltr';
			$settings['styles'][] = array( 'css' => "body{direction:$dir;text-align:start}" );
		}
	}
	return $settings;
}, 10, 2 );

// Load core block CSS as the single "wp-block-library" handle (removed above) instead of per-block inline styles.
add_filter( 'should_load_separate_core_block_assets', '__return_false' );

add_filter( 'wpcf7_autop_or_not', '__return_false' );

/* ---------- Customizer: contact data ---------- */

function tr_defaults() {
	return array(
		'tr_phone'    => '+39 333 000 0000',
		'tr_whatsapp' => '393330000000',
		'tr_email'    => 'info@nomecognome.it',
		'tr_piva'     => '00000000000',
	);
}

function tr_opt( $key ) {
	$d = tr_defaults();
	return get_theme_mod( $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

add_action( 'customize_register', function ( $c ) {
	$c->add_section( 'tr_contacts', array( 'title' => 'Contatti e WhatsApp', 'priority' => 30 ) );
	$labels = array(
		'tr_phone'    => 'Telefono (come appare sul sito)',
		'tr_whatsapp' => 'Numero WhatsApp (solo cifre, con prefisso 39)',
		'tr_email'    => 'Email',
		'tr_piva'     => 'Partita IVA',
	);
	foreach ( tr_defaults() as $id => $def ) {
		$c->add_setting( $id, array( 'default' => $def, 'sanitize_callback' => 'sanitize_text_field' ) );
		$c->add_control( $id, array( 'label' => $labels[ $id ], 'section' => 'tr_contacts', 'type' => 'text' ) );
	}
} );

/* ---------- Helpers ---------- */

function tr_icon( $name ) {
	static $icons = null;
	if ( null === $icons ) {
		$json  = file_get_contents( get_theme_file_path( 'assets/icons.json' ) );
		$icons = $json ? json_decode( $json, true ) : array();
	}
	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

function tr_home_url() {
	return function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
}

function tr_contact_url() {
	$id = (int) get_option( 'tr_contact_page' );
	if ( $id && function_exists( 'pll_get_post' ) ) {
		$id = pll_get_post( $id ) ?: $id;
	}
	return $id ? get_permalink( $id ) : tr_home_url();
}

function tr_wa_url() {
	return 'https://wa.me/' . preg_replace( '/\D/', '', tr_opt( 'tr_whatsapp' ) ) . '?text=' . rawurlencode( tr( 'Buongiorno, vorrei un preventivo per una traduzione.' ) );
}

function tr_ltr( $s ) {
	return '<bdi dir="ltr">' . esc_html( $s ) . '</bdi>';
}

/* Two arches (Roman + Arab) standing on a tricolore base. The flag never mirrors in RTL. */
function tr_logo() {
	return '<svg class="mark" viewBox="0 0 32 38" fill="none" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path stroke="currentColor" d="M3 33V16a13 13 0 0 1 26 0v17"/><path stroke="currentColor" d="M9 33V19c0-5 3-8.5 7-11.5 4 3 7 6.5 7 11.5v14"/><rect x="1" y="35" width="10" height="2.6" fill="#009246"/><rect x="11" y="35" width="10" height="2.6" fill="#F1F2F1" stroke="currentColor" stroke-opacity=".22" stroke-width=".5"/><rect x="21" y="35" width="10" height="2.6" fill="#CE2B37"/></svg>';
}

function tr_brand() {
	if ( has_custom_logo() ) {
		return get_custom_logo();
	}
	return sprintf(
		'<a class="brand" href="%s">%s<span><span class="brand-name">%s</span><span class="brand-tag">%s</span></span></a>',
		esc_url( tr_home_url() ),
		tr_logo(),
		esc_html( get_bloginfo( 'name' ) ),
		esc_html( get_bloginfo( 'description' ) )
	);
}

function tr_lang_switch() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}
	$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_no_translation' => 0 ) );
	if ( ! $langs ) {
		return;
	}
	echo '<div class="lang" role="group" aria-label="' . esc_attr( tr( 'Lingua' ) ) . '">';
	foreach ( $langs as $l ) {
		$label = 'ar' === $l['slug'] ? 'عربي' : strtoupper( $l['slug'] );
		$flag  = 'it' === $l['slug'] ? '<span class="flag-it" aria-hidden="true"></span>' : '';
		printf(
			'<a href="%s" lang="%s" hreflang="%s"%s>%s%s</a>',
			esc_url( $l['url'] ),
			esc_attr( $l['slug'] ),
			esc_attr( $l['slug'] ),
			$l['current_lang'] ? ' aria-current="true"' : '',
			$flag,
			esc_html( $label )
		);
	}
	echo '</div>';
}

/* ---------- Menus ---------- */

function tr_menu_tree( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}
	$items = wp_get_nav_menu_items( $locations[ $location ], array( 'update_post_term_cache' => false ) );
	if ( ! $items ) {
		return array();
	}
	_wp_menu_item_classes_by_context( $items );
	$by   = array();
	$tree = array();
	foreach ( $items as $it ) {
		$it->children  = array();
		$by[ $it->ID ] = $it;
	}
	foreach ( $items as $it ) {
		if ( $it->menu_item_parent && isset( $by[ $it->menu_item_parent ] ) ) {
			$by[ $it->menu_item_parent ]->children[] = $it;
		} else {
			$tree[] = $it;
		}
	}
	return $tree;
}

function tr_item_icon( $item ) {
	foreach ( (array) $item->classes as $c ) {
		if ( 0 === strpos( $c, 'ic-' ) ) {
			return tr_icon( substr( $c, 3 ) );
		}
	}
	return '';
}

function tr_cur( $item ) {
	return $item->current ? ' aria-current="page"' : '';
}

function tr_nav_desktop() {
	foreach ( tr_menu_tree( 'primary' ) as $it ) {
		if ( ! $it->children ) {
			printf( '<a href="%s"%s>%s</a>', esc_url( $it->url ), tr_cur( $it ), esc_html( $it->title ) );
			continue;
		}
		$active = false;
		foreach ( $it->children as $ch ) {
			$active = $active || $ch->current;
		}
		echo '<div class="dd"><button type="button" class="dd-btn' . ( $active ? ' is-active' : '' ) . '" aria-expanded="false" data-dd>' . esc_html( $it->title ) . tr_icon( 'chev' ) . '</button><div class="dd-panel">';
		foreach ( $it->children as $ch ) {
			printf(
				'<a href="%s"%s><span class="dd-ic">%s</span><span class="dd-t">%s</span><span class="dd-d">%s</span></a>',
				esc_url( $ch->url ),
				tr_cur( $ch ),
				tr_item_icon( $ch ),
				esc_html( $ch->title ),
				esc_html( $ch->description )
			);
		}
		echo '</div></div>';
	}
}

function tr_nav_mobile() {
	foreach ( tr_menu_tree( 'primary' ) as $it ) {
		if ( ! $it->children ) {
			printf( '<a href="%s"%s>%s%s</a>', esc_url( $it->url ), tr_cur( $it ), esc_html( $it->title ), tr_icon( 'arrow' ) );
			continue;
		}
		echo '<p class="mnav-h">' . esc_html( $it->title ) . '</p><div class="mnav-svc">';
		foreach ( $it->children as $ch ) {
			printf( '<a href="%s"%s><span class="mini-arch">%s</span>%s</a>', esc_url( $ch->url ), tr_cur( $ch ), tr_item_icon( $ch ), esc_html( $ch->title ) );
		}
		echo '</div>';
	}
}

function tr_nav_list( $location ) {
	echo '<ul>';
	foreach ( tr_menu_tree( $location ) as $it ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $it->url ), esc_html( $it->title ) );
	}
	echo '</ul>';
}

function tr_breadcrumbs() {
	if ( is_front_page() || ! is_singular() ) {
		return;
	}
	$items = array( array( tr( 'Home' ), tr_home_url() ) );
	if ( get_post_meta( get_the_ID(), '_tr_service', true ) ) {
		$items[] = array( tr( 'Servizi' ), '' );
	}
	$items[] = array( get_the_title(), '' );
	$last    = count( $items ) - 1;
	echo '<nav class="wrap crumbs-wrap" aria-label="Breadcrumb"><ol class="crumbs">';
	foreach ( $items as $i => $c ) {
		echo '<li>' . ( $c[1] ? '<a href="' . esc_url( $c[1] ) . '">' . esc_html( $c[0] ) . '</a>' : '<span' . ( $i === $last ? ' aria-current="page"' : '' ) . '>' . esc_html( $c[0] ) . '</span>' ) . '</li>';
		if ( $i < $last ) {
			echo '<li aria-hidden="true">' . tr_icon( 'chevR' ) . '</li>';
		}
	}
	echo '</ol></nav>';
}

/* Pattern used by the arch illustrations and the dark bands. */
add_action( 'wp_footer', function () {
	echo '<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><pattern id="lat" width="56" height="56" patternUnits="userSpaceOnUse"><g class="lat-g"><rect x="16.5" y="16.5" width="23" height="23"/><rect x="16.5" y="16.5" width="23" height="23" transform="rotate(45 28 28)"/><path d="M28 0v11.74M28 44.26V56M0 28h11.74M44.26 28H56M0 0l16.5 16.5M56 0 39.5 16.5M0 56l16.5-16.5M56 56 39.5 39.5"/></g></pattern></defs></svg>';
}, 5 );
