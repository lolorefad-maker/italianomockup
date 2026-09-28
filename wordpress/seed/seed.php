<?php
/**
 * Seeds the bilingual site: languages, pages (IT + AR) as editable blocks, synced CTA,
 * contact forms, menus, SEO meta and translated theme strings.
 * Run:  docker compose run --rm cli eval-file /seed/seed.php
 * Re-runnable: removes what the previous run created, then rebuilds it.
 */

if ( ! function_exists( 'PLL' ) ) {
	WP_CLI::error( 'Polylang is not active.' );
}
require_once ABSPATH . 'wp-admin/includes/image.php';

$data = json_decode( file_get_contents( '/seed/content.json' ), true );
$S    = $data['S'];
$DOC  = $data['DOC_SVG'];
$LAT  = $data['LAT'];
$ART  = $data['ART'];

const TR_SVC   = array( 'traduzioni', 'asseverazioni', 'interpretariato', 'mediazione' );
const TR_ALL   = array( 'traduzioni', 'asseverazioni', 'interpretariato', 'mediazione', 'lezioni' );
const TR_ICON  = array( 'traduzioni' => 'doc', 'asseverazioni' => 'seal', 'interpretariato' => 'headset', 'mediazione' => 'bubbles', 'lezioni' => 'book' );
const TR_PHONE = '+39 333 000 0000';
const TR_EMAIL = 'info@nomecognome.it';
const TR_WA    = '393330000000';

$SLUGS = array(
	'it' => array( 'home' => 'home', 'chi-sono' => 'chi-sono', 'traduzioni' => 'traduzioni', 'asseverazioni' => 'asseverazioni', 'interpretariato' => 'interpretariato', 'mediazione' => 'mediazione-linguistico-culturale', 'lezioni' => 'lezioni-di-italiano', 'contatti' => 'contatti', 'privacy' => 'privacy-policy', 'cookie' => 'cookie-policy' ),
	'ar' => array( 'home' => 'ar-home', 'chi-sono' => 'man-ana', 'traduzioni' => 'tarjama', 'asseverazioni' => 'tarjama-muhallafa', 'interpretariato' => 'tarjama-fawriya', 'mediazione' => 'wasata-thaqafiya', 'lezioni' => 'durus-italiya', 'contatti' => 'tawasul', 'privacy' => 'siyasat-al-khususiya', 'cookie' => 'milaffat-al-irtibat' ),
);
$LEGAL = array(
	'it' => array( 'privacy' => array( 'Privacy policy', "Testo da completare: l'informativa privacy verrà generata con un servizio dedicato (per esempio Iubenda o Complianz) prima della pubblicazione." ), 'cookie' => array( 'Cookie policy', 'Testo da completare: la cookie policy verrà generata insieme al banner dei cookie prima della pubblicazione.' ) ),
	'ar' => array( 'privacy' => array( 'سياسة الخصوصية', 'نص قيد الإعداد: سيتم إنشاء سياسة الخصوصية بأداة مخصصة (مثل Iubenda أو Complianz) قبل نشر الموقع.' ), 'cookie' => array( 'سياسة ملفات الارتباط', 'نص قيد الإعداد: سيتم إنشاء سياسة ملفات الارتباط مع شريط الموافقة قبل نشر الموقع.' ) ),
);

/* ---------- Polylang helpers ---------- */

function tr_pll_set( $key, $val ) {
	$o = PLL()->options;
	if ( is_object( $o ) ) {
		$r = method_exists( $o, 'set' ) ? $o->set( $key, $val ) : ( $o[ $key ] = $val );
		if ( is_wp_error( $r ) && $r->has_errors() ) {
			WP_CLI::warning( "Polylang option $key: " . $r->get_error_message() );
		}
		if ( method_exists( $o, 'save' ) ) {
			$o->save();
		}
	} else {
		PLL()->options[ $key ] = $val;
		update_option( 'polylang', PLL()->options );
	}
}

function tr_add_lang( $args ) {
	$model = PLL()->model;
	if ( $model->get_language( $args['slug'] ) ) {
		return;
	}
	if ( method_exists( $model, 'add_language' ) ) {
		$r = $model->add_language( $args );
	} else {
		$r = $model->languages->add( $args );
	}
	if ( is_wp_error( $r ) ) {
		WP_CLI::error( 'Language ' . $args['slug'] . ': ' . $r->get_error_message() );
	}
	if ( method_exists( $model, 'clean_languages_cache' ) ) {
		$model->clean_languages_cache();
	}
	WP_CLI::log( 'Language added: ' . $args['slug'] );
}

/* ---------- Block markup helpers ---------- */

function tb( $name, $attrs, $html ) {
	$a = $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
	return "<!-- wp:$name$a -->\n$html\n<!-- /wp:$name -->\n";
}
function g( $cls, $inner, $tag = 'div' ) {
	$a = array();
	if ( 'div' !== $tag ) {
		$a['tagName'] = $tag;
	}
	if ( $cls ) {
		$a['className'] = $cls;
	}
	return tb( 'group', $a, "<$tag class=\"wp-block-group" . ( $cls ? " $cls" : '' ) . "\">\n$inner</$tag>" );
}
function hh( $lvl, $text, $cls = '' ) {
	$a = array();
	if ( 2 !== $lvl ) {
		$a['level'] = $lvl;
	}
	if ( $cls ) {
		$a['className'] = $cls;
	}
	$text = str_replace( '<em class="hl">', '<em>', $text );
	return tb( 'heading', $a, "<h$lvl class=\"wp-block-heading" . ( $cls ? " $cls" : '' ) . "\">$text</h$lvl>" );
}
function pp( $text, $cls = '' ) {
	return tb( 'paragraph', $cls ? array( 'className' => $cls ) : array(), '<p' . ( $cls ? " class=\"$cls\"" : '' ) . ">$text</p>" );
}
function html_b( $html ) {
	return tb( 'html', array(), $html );
}
function btn( $text, $url, $style = '', $blank = false ) {
	$a = array();
	if ( $style ) {
		$a['className'] = "is-style-$style";
	}
	if ( $blank ) {
		$a['linkTarget'] = '_blank';
		$a['rel']        = 'noreferrer noopener';
	}
	$t = $blank ? ' target="_blank" rel="noreferrer noopener"' : '';
	return tb( 'button', $a, '<div class="wp-block-button' . ( $style ? " is-style-$style" : '' ) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . "\"$t>$text</a></div>" );
}
function btns( $inner, $cls = 'cta-row' ) {
	return tb( 'buttons', $cls ? array( 'className' => $cls ) : array(), '<div class="wp-block-buttons' . ( $cls ? " $cls" : '' ) . "\">\n$inner</div>" );
}
function det( $q, $a, $open = false ) {
	return tb( 'details', $open ? array( 'showContent' => true ) : array(), '<details class="wp-block-details"' . ( $open ? ' open' : '' ) . "><summary>$q</summary>" . pp( $a ) . '</details>' );
}
function secs( $list ) {
	$o = '';
	$i = 0;
	foreach ( array_filter( $list ) as $x ) {
		$o .= g( 'sec' . ( 0 === $i % 2 ? ' alt' : '' ), $x, 'section' );
		$i++;
	}
	return $o;
}
function eb( $pair, $L, $extra = '' ) {
	$o = 'it' === $L ? 'ar' : 'it';
	$s = '<span>' . $pair[0] . '</span>';
	if ( ! empty( $pair[1] ) ) {
		$s .= '<span class="pair" lang="' . $o . '" dir="' . ( 'ar' === $o ? 'rtl' : 'ltr' ) . '">' . $pair[1] . '</span>';
	}
	return pp( $s . $extra, 'eyebrow' );
}
function head2( $eb, $title, $lead = '' ) {
	return g( 'sec-head', g( '', $eb . hh( 2, $title ) ) . ( $lead ? pp( $lead, 'lead' ) : '' ) );
}
function ltr( $s ) {
	return '<bdi dir="ltr">' . $s . '</bdi>';
}

/* ---------- Content helpers ---------- */

function wa_url( $S, $L ) {
	return 'https://wa.me/' . TR_WA . '?text=' . rawurlencode( $S[ $L ]['ui']['waText'] );
}
function cta_btns( $S, $L, $U ) {
	$u = $S[ $L ]['ui'];
	return btns( btn( $u['ctaLong'], $U[ $L ]['contatti'] ) . btn( $u['waLong'], wa_url( $S, $L ), 'whatsapp', true ) );
}
function portrait_b( $S, $L, $img ) {
	return tb(
		'image',
		array( 'id' => $img['id'], 'sizeSlug' => 'full', 'linkDestination' => 'none', 'className' => 'arch-img' ),
		'<figure class="wp-block-image size-full arch-img"><img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $S[ $L ]['ui']['portrait'] ) . '" class="wp-image-' . $img['id'] . '"/></figure>'
	);
}
function type_arch( $w, $LAT ) {
	return '<div class="arch arch-type" aria-hidden="true">' . $LAT . '<span class="tw-ar" lang="ar" dir="rtl">' . $w['ar'] . '</span><span class="tw-mid"><i></i>AR · IT<i></i></span><span class="tw-it" lang="it">' . $w['it'] . '</span></div>';
}
function steps_sec( $t, $L, $title, $lead, $steps ) {
	$o = '';
	foreach ( $steps as $i => $s ) {
		$o .= g( 'step', pp( (string) ( $i + 1 ), 'step-n' ) . hh( 3, $s[0] ) . pp( $s[1] ) );
	}
	return g( 'wrap', head2( eb( $t['eb']['how'], $L ), $title, $lead ) . g( 'steps' . ( 4 === count( $steps ) ? ' n-4' : '' ), $o ) );
}
function faq_sec( $S, $L, $items ) {
	$t = $S[ $L ];
	$u = $t['ui'];
	$d = '';
	foreach ( $items as $i => $qa ) {
		$d .= det( $qa[0], $qa[1], 0 === $i );
	}
	return g( 'wrap faq', g( 'faq-side', eb( $t['eb']['faq'], $L ) . hh( 2, $u['faq'] ) . pp( $u['faqLead'], 'lead' ) . btns( btn( $u['waLong'], wa_url( $S, $L ), 'whatsapp', true ), '' ) ) . g( 'acc', $d ) );
}
function cta_band( $S, $L, $U, $LAT ) {
	$u = $S[ $L ]['ui'];
	return g( 'cta-band', html_b( $LAT ) . g( 'wrap cta-in', g( '', hh( 2, $u['ctaT'] ) . pp( $u['ctaS'] ) ) . cta_btns( $S, $L, $U ) ), 'section' );
}

/* ---------- Page builders ---------- */

// Round seal with the Italian cockade (coccarda tricolore, official 2006 colors).
function seal_svg() {
	return '<svg class="seal" viewBox="0 0 120 120" role="img" aria-label="Italia" style="direction:ltr"><defs><path id="sealRing" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0"/></defs>'
		. '<circle class="seal-bg" cx="60" cy="60" r="58"/><circle class="seal-in" cx="60" cy="60" r="37"/>'
		. '<text class="seal-t"><textPath href="#sealRing" textLength="286" lengthAdjust="spacing">TRADUTTORE · INTERPRETE · ARABO · ITALIANO ·</textPath></text>'
		. '<circle cx="60" cy="60" r="28" fill="#009246"/><circle cx="60" cy="60" r="18.5" fill="#F1F2F1"/><circle cx="60" cy="60" r="9" fill="#CE2B37"/></svg>';
}

const TR_EXTRA = array(
	'it' => array(
		'avail'  => 'Disponibile per nuovi incarichi',
		'credsL' => 'Credenziali',
		'creds'  => array( 'CTU · Tribunale di [Città]', 'Certificazione DITALS', 'Madrelingua araba', 'Dati protetti · GDPR' ),
	),
	'ar' => array(
		'avail'  => 'متاح لمهام جديدة',
		'credsL' => 'الاعتمادات',
		'creds'  => array( 'خبير لدى محكمة [المدينة] (CTU)', 'شهادة DITALS', 'العربية لغتي الأم', 'بيانات محمية · GDPR' ),
	),
);

function page_home( $S, $L, $U, $img, $DOC, $cta ) {
	$t = $S[ $L ];
	$h = $t['home'];
	$u = $t['ui'];
	$ex = TR_EXTRA[ $L ];
	$hero = g( 'hero', g( 'wrap',
		g( 'hero-grid',
			g( '', pp( $ex['avail'], 'avail' ) . pp( '<span>' . $h['eyebrow'] . '</span>', 'eyebrow' ) . hh( 1, $h['h1'] ) . pp( $h['lead'], 'lead' ) . cta_btns( $S, $L, $U ) . pp( $u['micro'], 'micro' ) )
			. g( 'hero-art', portrait_b( $S, $L, $img ) . html_b( seal_svg() ) . pp( $u['portrait'], 'arch-cap' ) . g( 'float-doc', pp( $u['floatT'], 't' ) . pp( $u['floatS'], 's' ) ) . pp( $u['pairWord'], 'float-pair' ) )
		)
		. g( 'trust', pp( $h['trustL'], 'trust-l' ) . pp( implode( ' · ', $h['trust'] ), 'trust-list' ) )
	), 'section' );

	$tiles = '';
	foreach ( TR_ALL as $s ) {
		$x      = $t['svc'][ $s ];
		$tiles .= g( 'tile ic-' . TR_ICON[ $s ], hh( 3, '<a href="' . esc_url( $U[ $L ][ $s ] ) . '">' . $x['name'] . '</a>' ) . pp( $x['tile'] ) . pp( $x['ex'], 'ex' ) . pp( $u['discover'], 'more' ) );
	}
	$svc = g( 'sec alt', g( 'wrap', head2( eb( $t['eb']['svc'], $L ), $h['svcT'], $h['svcL'] ) . g( 'tiles', $tiles ) ), 'section' );

	$stats = '';
	foreach ( $h['stats'] as $st ) {
		$stats .= g( 'stat', pp( ltr( $st[0] ), 'num' ) . pp( $st[1], 'lbl' ) );
	}
	$creds = pp( $ex['credsL'], 'creds-l' );
	foreach ( $ex['creds'] as $c ) {
		$creds .= pp( $c, 'cred' );
	}
	// Proof directly after the hero (Trust & Authority pattern): figures + credentials.
	$band = g( 'band', html_b( '<svg class="band-lat" aria-hidden="true"><rect width="100%" height="100%" fill="url(#lat)"/></svg>' ) . g( 'wrap', head2( eb( $t['eb']['why'], $L ), $h['whyT'], $h['whyL'] ) . g( 'stats', $stats ) . g( 'creds', $creds ) ), 'section' );

	$checks = '';
	foreach ( $h['docs'] as $d ) {
		$checks .= pp( $d );
	}
	$docs = g( 'sec', g( 'wrap split', g( '', eb( $t['eb']['docs'], $L ) . hh( 2, $h['docsT'] ) . pp( $h['docsL'], 'lead' ) . g( 'checks', $checks ) . pp( '<a href="' . esc_url( $U[ $L ]['asseverazioni'] ) . '">' . $h['docsLink'] . '</a>', 'link-arrow' ) ) . html_b( '<div class="doc-panel">' . $DOC . '</div>' ) ), 'section' );

	$q = '';
	foreach ( $h['quotes'] as $qt ) {
		$q .= g( 'quote', pp( $qt[0], 'qt' ) . pp( '<strong>' . $qt[1] . '</strong>' . $qt[2], 'who' ) );
	}
	$quotes = g( 'wrap', g( 'sec-head', g( '', eb( $t['eb']['quotes'], $L, '<span class="tag">' . $u['sample'] . '</span>' ) . hh( 2, $h['quotesT'] ) ) ) . g( 'quotes', $q ) );

	return $hero . $band . $svc . $docs . secs( array( steps_sec( $t, $L, $h['howT'], $h['howL'], $t['steps'] ), $quotes, faq_sec( $S, $L, $h['faq'] ) ) ) . $cta;
}

function page_service( $S, $L, $U, $s, $DOC, $LAT, $ART, $cta ) {
	$t = $S[ $L ];
	$x = $t['svc'][ $s ];
	$u = $t['ui'];
	$art = 'asseverazioni' === $s ? '<div class="doc-panel">' . $DOC . '</div>' : type_arch( $ART[ $s ], $LAT );
	$facts = '';
	foreach ( $x['facts'] as $f ) {
		$facts .= g( 'fact', pp( $f[0], 'dt' ) . pp( $f[1], 'dd' ) );
	}
	$hero = g( 'phero', g( 'wrap',
		g( 'phero-grid', g( '', eb( array( $x['name'], $x['pair'] ), $L ) . hh( 1, $x['h1'] ) . pp( $x['lead'], 'lead' ) . cta_btns( $S, $L, $U ) . pp( $u['micro'], 'micro' ) ) . g( 'phero-art', html_b( $art ) ) )
		. g( 'brief', pp( $u['brief'], 'brief-l' ) . g( 'facts', $facts ) )
	), 'section' );

	$items = '';
	foreach ( $x['items'] as $it ) {
		$items .= g( 'item', hh( 3, $it[0] ) . pp( $it[1] ) );
	}
	list( $nt, $nd, $nl, $nlt ) = $x['note'];
	$aside = '';
	if ( ! empty( $x['vocab'] ) ) {
		$ar   = 'ar' === $L;
		$rows = g( 'vocab-row vocab-head', $ar ? pp( $x['vocabT'][1] ) . pp( $x['vocabT'][0] ) : pp( $x['vocabT'][0] ) . pp( $x['vocabT'][1] ) );
		foreach ( $x['vocab'] as $v ) {
			$it_p = pp( '<span lang="it" dir="ltr">' . $v[0] . '</span>', 'v-it' );
			$ar_p = pp( '<span lang="ar" dir="rtl">' . $v[1] . '</span>', 'v-ar' );
			$rows .= g( 'vocab-row', $ar ? $ar_p . $it_p : $it_p . $ar_p );
		}
		$aside = g( 'vocab', $rows );
	}
	$note = g( 'note', g( '', eb( array( 'ar' === $L ? 'من المهم أن تعرف' : 'Da sapere' ), $L ) . hh( 3, $nt ) ) . g( '', pp( $nd ) . ( $nl ? pp( '<a href="' . esc_url( $U[ $L ][ $nl ] ) . '">' . $nlt . '</a>', 'link-arrow' ) : '' ) . $aside ) );
	$what = g( 'wrap', g( 'sec-head', g( '', eb( $t['eb']['detail'], $L ) . hh( 2, $x['whatT'] ) ) ) . g( 'items', $items ) . $note );

	$steps = $x['stepsT'] ? steps_sec( $t, $L, $x['stepsT'], '', $x['steps'] ? $x['steps'] : $t['steps'] ) : null;

	$others = '';
	foreach ( TR_ALL as $o ) {
		if ( $o === $s ) {
			continue;
		}
		$y       = $t['svc'][ $o ];
		$others .= g( 'other ic-' . TR_ICON[ $o ], hh( 3, '<a href="' . esc_url( $U[ $L ][ $o ] ) . '">' . $y['name'] . '</a>' ) . pp( $y['short'], 'd' ) . pp( $u['discover'], 'more' ) );
	}
	$oth = g( 'wrap', g( 'sec-head', g( '', eb( $t['eb']['other'], $L ) . hh( 2, $u['other'] ) ) ) . g( 'others', $others ) );

	return $hero . secs( array( $what, $steps, faq_sec( $S, $L, $x['faq'] ), $oth ) ) . $cta;
}

function page_about( $S, $L, $U, $img, $cta ) {
	$t = $S[ $L ];
	$a = $t['about'];
	$u = $t['ui'];
	$bio = '';
	foreach ( $a['bio'] as $b ) {
		$bio .= pp( $b );
	}
	$hero = g( 'phero', g( 'wrap', g( 'about-grid',
		g( 'about-art', portrait_b( $S, $L, $img ) . pp( $u['portrait'], 'arch-cap' ) )
		. g( '', eb( $a['eb'], $L ) . hh( 1, $a['h1'] ) . g( 'bio', $bio ) . pp( $u['brand'], 'sign' ) . cta_btns( $S, $L, $U ) )
	) ), 'section' );
	$tl = '';
	foreach ( $a['tl'] as $r ) {
		$tl .= g( 'tl-row', pp( $r[0], 'tl-y' ) . g( '', pp( $r[1], 'tl-t' ) . pp( $r[2], 'tl-d' ) ) );
	}
	$vals = '';
	foreach ( $a['values'] as $v ) {
		$vals .= g( 'value', hh( 3, $v[0] ) . pp( $v[1] ) );
	}
	$langs = '';
	foreach ( $a['langs'] as $l ) {
		$langs .= g( 'lang-card', pp( '<span lang="' . $l[1] . '" dir="' . ( 'ar' === $l[1] ? 'rtl' : 'ltr' ) . '">' . $l[0] . '</span>', 'lang-native' ) . pp( $l[2], 'lang-name' ) . pp( $l[3], 'lang-level' ) );
	}
	return $hero . secs( array(
		g( 'wrap tl-wrap', g( '', eb( $a['tlEb'], $L ) . hh( 2, $a['tlT'] ) . pp( $a['tlL'], 'lead' ) ) . g( 'timeline', $tl ) ),
		g( 'wrap', g( 'sec-head', g( '', eb( $a['valEb'], $L ) . hh( 2, $a['valT'] ) ) ) . g( 'values', $vals ) ),
		g( 'wrap', g( 'sec-head', g( '', eb( $a['langEb'], $L ) . hh( 2, $a['langT'] ) ) ) . g( 'langs', $langs ) ),
	) ) . $cta;
}

function page_contact( $S, $L, $shortcode ) {
	$t = $S[ $L ];
	$c = $t['contact'];
	$cards = g( 'ccard ccard-wa ic-wa', pp( $c['waK'], 'k' ) . pp( 'WhatsApp · ' . ltr( TR_PHONE ), 'v' ) . btns( btn( $c['waBtn'], wa_url( $S, $L ), 'whatsapp', true ), '' ) )
		. g( 'ccard ic-phone', pp( $c['phoneK'], 'k' ) . pp( ltr( TR_PHONE ), 'v' ) )
		. g( 'ccard ic-mail', pp( $c['emailK'], 'k' ) . pp( ltr( TR_EMAIL ), 'v' ) )
		. g( 'ccard ic-pin', pp( $c['studioK'], 'k' ) . pp( $c['studioV'], 'v' ) . pp( $c['studioS'], 'sub' ) )
		. g( 'ccard ic-clock', pp( $c['hoursK'], 'k' ) . pp( $c['hoursV'], 'v' ) . pp( $c['hoursS'], 'sub' ) )
		. pp( $c['privacy'], 'privacy-note' );
	return g( 'phero', g( 'wrap',
		g( 'contact-head', eb( $c['eb'], $L ) . hh( 1, $c['h1'] ) . pp( $c['lead'], 'lead' ) )
		. g( 'contact-grid', g( 'form-card', tb( 'shortcode', array(), $shortcode ) ) . g( 'cinfo', $cards ) )
	), 'section' );
}

function page_legal( $title, $text ) {
	return g( 'phero', g( 'wrap', g( 'contact-head', hh( 1, $title ) . pp( $text, 'lead' ) ) ), 'section' );
}

/* ---------- Contact Form 7 ---------- */

function cf7_form_markup( $c ) {
	$opts = function ( $arr ) {
		return '"' . implode( '" "', $arr ) . '"';
	};
	return '<div class="fgrid">
<div class="field"><label for="f-name">' . $c['name'] . ' <span class="req">*</span></label>[text* your-name id:f-name autocomplete:name placeholder "' . $c['namePh'] . '"]</div>
<div class="field"><label for="f-email">' . $c['email'] . ' <span class="req">*</span></label>[email* your-email id:f-email autocomplete:email placeholder "' . $c['emailPh'] . '"]</div>
<div class="field"><label for="f-phone">' . $c['phone'] . ' <span class="opt">(' . $c['optional'] . ')</span></label>[tel your-phone id:f-phone autocomplete:tel placeholder "+39 …"]</div>
<div class="field"><label for="f-service">' . $c['service'] . '</label>[select your-service id:f-service ' . $opts( $c['services'] ) . ']</div>
<div class="field"><span class="lbl">' . $c['dirL'] . '</span>[radio your-dir use_label_element default:1 ' . $opts( $c['dirO'] ) . ']</div>
<div class="field"><label for="f-date">' . $c['date'] . ' <span class="opt">(' . $c['optional'] . ')</span></label>[date your-date id:f-date]</div>
<div class="field full"><label for="f-file">' . $c['fileL'] . ' <span class="opt">(' . $c['optional'] . ')</span></label>[file your-file id:f-file limit:10mb filetypes:pdf|jpg|jpeg|png|doc|docx]<span class="hint">' . $c['fileHint'] . '</span></div>
<div class="field full"><label for="f-msg">' . $c['msg'] . ' <span class="req">*</span></label>[textarea* your-message id:f-msg placeholder "' . $c['msgPh'] . '"]</div>
<div class="field full">[acceptance consent]' . $c['consent'] . '[/acceptance]</div>
</div>
<div class="form-foot">[submit class:btn class:btn-primary "' . $c['submit'] . '"]<span class="secure">' . $c['secure'] . '</span></div>';
}

function cf7_create( $S, $L ) {
	$c     = $S[ $L ]['contact'];
	$title = 'ar' === $L ? 'Richiesta preventivo (AR)' : 'Richiesta preventivo (IT)';
	$cf    = WPCF7_ContactForm::get_template( array( 'locale' => 'ar' === $L ? 'ar' : 'it_IT', 'title' => $title ) );
	$p     = $cf->get_properties();
	$p['form'] = cf7_form_markup( $c );
	$p['mail']['recipient']          = '[_site_admin_email]';
	$p['mail']['sender']             = '[_site_title] <wordpress@nomecognome.test>';
	$p['mail']['subject']            = '[Preventivo ' . strtoupper( $L ) . '] [your-service] – [your-name]';
	$p['mail']['additional_headers'] = 'Reply-To: [your-email]';
	$p['mail']['attachments']        = '[your-file]';
	$p['mail']['body']               = "Nome: [your-name]\nEmail: [your-email]\nTelefono: [your-phone]\nServizio: [your-service]\nCombinazione: [your-dir]\nScadenza: [your-date]\n\n[your-message]\n\n--\nInviato dal modulo del sito ([_site_url]) · lingua: " . strtoupper( $L );
	$p['mail_2']['active']           = false;
	$cf->set_properties( $p );
	$cf->save();
	$id = method_exists( $cf, 'hash' ) && $cf->hash() ? $cf->hash() : $cf->id();
	return array( 'post' => $cf->id(), 'shortcode' => '[contact-form-7 id="' . $id . '" title="' . $title . '"]' );
}

/* ---------- Placeholder portrait (replace from the editor) ---------- */

function portrait_placeholder() {
	$w  = 800;
	$h  = 1000;
	$im = imagecreatetruecolor( $w, $h );
	for ( $y = 0; $y < $h; $y++ ) {
		$k = min( 1, $y / ( $h * 0.8 ) );
		$c = imagecolorallocate( $im, (int) ( 0x2B + ( 0x12 - 0x2B ) * $k ), (int) ( 0x5B + ( 0x2E - 0x5B ) * $k ), (int) ( 0x48 + ( 0x24 - 0x48 ) * $k ) );
		imageline( $im, 0, $y, $w, $y, $c );
	}
	imagesetthickness( $im, 2 );
	$line = imagecolorallocatealpha( $im, 233, 241, 236, 106 );
	$cell = 112;
	for ( $cy = 0; $cy <= $h; $cy += $cell ) {
		for ( $cx = 0; $cx <= $w; $cx += $cell ) {
			$m = $cx + $cell / 2;
			$n = $cy + $cell / 2;
			$a = 23;
			$d = 32.5;
			imagepolygon( $im, array( $m - $a, $n - $a, $m + $a, $n - $a, $m + $a, $n + $a, $m - $a, $n + $a ), $line );
			imagepolygon( $im, array( $m, $n - $d, $m + $d, $n, $m, $n + $d, $m - $d, $n ), $line );
			imageline( $im, $m, $cy, $m, $n - $d, $line );
			imageline( $im, $m, $n + $d, $m, $cy + $cell, $line );
			imageline( $im, $cx, $n, $m - $d, $n, $line );
			imageline( $im, $m + $d, $n, $cx + $cell, $n, $line );
		}
	}
	$sil = imagecolorallocatealpha( $im, 238, 243, 239, 116 );
	imagefilledellipse( $im, 400, 470, 256, 256, $sil );
	imagefilledellipse( $im, 400, 1090, 560, 600, $sil );
	$up   = wp_upload_dir();
	$file = trailingslashit( $up['path'] ) . 'ritratto-segnaposto.jpg';
	imagejpeg( $im, $file, 86 );
	imagedestroy( $im );
	$id = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => 'Ritratto (segnaposto)', 'post_status' => 'inherit' ), $file );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	return array( 'id' => $id, 'url' => wp_get_attachment_url( $id ) );
}

/* ================= Run ================= */

// 1. Clean up a previous run.
$prev = get_option( 'tr_seed_ids', array() );
foreach ( (array) ( $prev['posts'] ?? array() ) as $pid ) {
	wp_delete_post( $pid, true );
}
foreach ( (array) ( $prev['menus'] ?? array() ) as $mid ) {
	wp_delete_nav_menu( $mid );
}
// WordPress' own sample content (queried directly: Polylang filters get_posts by language).
global $wpdb;
$defaults = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('post','page') AND post_name IN ('hello-world','sample-page','privacy-policy')" );
foreach ( $defaults as $pid ) {
	wp_delete_post( (int) $pid, true );
}
$made = array( 'posts' => array(), 'menus' => array() );

// 2. Languages and Polylang settings.
tr_add_lang( array( 'name' => 'Italiano', 'slug' => 'it', 'locale' => 'it_IT', 'rtl' => false, 'term_group' => 0, 'flag' => 'it' ) );
tr_add_lang( array( 'name' => 'العربية', 'slug' => 'ar', 'locale' => 'ar', 'rtl' => true, 'term_group' => 1, 'flag' => 'arab' ) );
tr_pll_set( 'default_lang', 'it' );
tr_pll_set( 'hide_default', true );
tr_pll_set( 'force_lang', 1 );
tr_pll_set( 'rewrite', true );
tr_pll_set( 'browser', false );
tr_pll_set( 'redirect_lang', true );
tr_pll_set( 'media_support', false );

update_option( 'blogname', 'Nome Cognome' );
update_option( 'blogdescription', 'Traduttore · Interprete' );
update_option( 'timezone_string', 'Europe/Rome' );

// 3. Portrait placeholder.
$img                = portrait_placeholder();
$made['posts'][]    = $img['id'];

// 4. Pages (empty first, so every URL is known before content is written).
$pages = array( 'home', 'chi-sono', 'traduzioni', 'asseverazioni', 'interpretariato', 'mediazione', 'lezioni', 'contatti', 'privacy', 'cookie' );
$IDS   = array();
$U     = array();
foreach ( array( 'it', 'ar' ) as $L ) {
	foreach ( $pages as $i => $pg ) {
		if ( isset( $LEGAL[ $L ][ $pg ] ) ) {
			$title = $LEGAL[ $L ][ $pg ][0];
		} elseif ( in_array( $pg, TR_ALL, true ) ) {
			$title = $S[ $L ]['svc'][ $pg ]['name'];
		} else {
			$title = $S[ $L ]['nav'][ $pg ];
		}
		$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $SLUGS[ $L ][ $pg ], 'menu_order' => $i, 'post_content' => '' ), true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
		pll_set_post_language( $id, $L );
		if ( in_array( $pg, TR_SVC, true ) ) {
			update_post_meta( $id, '_tr_service', 1 );
		}
		$IDS[ $L ][ $pg ] = $id;
		$made['posts'][]  = $id;
	}
}
foreach ( $pages as $pg ) {
	pll_save_post_translations( array( 'it' => $IDS['it'][ $pg ], 'ar' => $IDS['ar'][ $pg ] ) );
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $IDS['it']['home'] );
update_option( 'wp_page_for_privacy_policy', $IDS['it']['privacy'] );
update_option( 'tr_contact_page', $IDS['it']['contatti'] );
foreach ( array( 'it', 'ar' ) as $L ) {
	foreach ( $pages as $pg ) {
		$U[ $L ][ $pg ] = get_permalink( $IDS[ $L ][ $pg ] );
	}
}

// 5. Contact forms and the synced "CTA" pattern (edit once, updates every page).
$CTA = array();
$SC  = array();
foreach ( array( 'it', 'ar' ) as $L ) {
	$f               = cf7_create( $S, $L );
	$SC[ $L ]        = $f['shortcode'];
	$made['posts'][] = $f['post'];
	$bid             = wp_insert_post( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'post_title' => 'ar' === $L ? 'Banda finale – preventivo (AR)' : 'Banda finale – preventivo (IT)', 'post_content' => cta_band( $S, $L, $U, $LAT ) ) );
	$CTA[ $L ]       = "<!-- wp:block {\"ref\":$bid} /-->\n";
	$made['posts'][] = $bid;
}

// 6. Page content + SEO meta.
foreach ( array( 'it', 'ar' ) as $L ) {
	foreach ( $pages as $pg ) {
		if ( 'home' === $pg ) {
			$html = page_home( $S, $L, $U, $img, $DOC, $CTA[ $L ] );
		} elseif ( 'chi-sono' === $pg ) {
			$html = page_about( $S, $L, $U, $img, $CTA[ $L ] );
		} elseif ( 'contatti' === $pg ) {
			$html = page_contact( $S, $L, $SC[ $L ] );
		} elseif ( isset( $LEGAL[ $L ][ $pg ] ) ) {
			$html = page_legal( $LEGAL[ $L ][ $pg ][0], $LEGAL[ $L ][ $pg ][1] );
		} else {
			$html = page_service( $S, $L, $U, $pg, $DOC, $LAT, $ART, $CTA[ $L ] );
		}
		$id = $IDS[ $L ][ $pg ];
		wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $html ) ) );
		if ( isset( $S[ $L ]['seo'][ $pg ] ) ) {
			list( $st, $sd, $kw ) = $S[ $L ]['seo'][ $pg ];
			update_post_meta( $id, 'rank_math_title', $st );
			update_post_meta( $id, 'rank_math_description', $sd );
			update_post_meta( $id, 'rank_math_focus_keyword', implode( ',', $kw ) );
		}
	}
}

// 7. Menus, one set per language.
$locations = array();
foreach ( array( 'it', 'ar' ) as $L ) {
	$t   = $S[ $L ];
	$sfx = strtoupper( $L );
	$add = function ( $menu, $pg, $title, $parent = 0, $cls = '', $desc = '' ) use ( $IDS, $L ) {
		return wp_update_nav_menu_item( $menu, 0, array(
			'menu-item-title'       => $title,
			'menu-item-object'      => 'page',
			'menu-item-object-id'   => $IDS[ $L ][ $pg ],
			'menu-item-type'        => 'post_type',
			'menu-item-status'      => 'publish',
			'menu-item-parent-id'   => $parent,
			'menu-item-classes'     => $cls,
			'menu-item-description' => $desc,
		) );
	};
	$primary = wp_create_nav_menu( "Menu principale ($sfx)" );
	$add( $primary, 'home', $t['nav']['home'] );
	$add( $primary, 'chi-sono', $t['nav']['chi-sono'] );
	$svc_parent = wp_update_nav_menu_item( $primary, 0, array( 'menu-item-title' => $t['nav']['services'], 'menu-item-url' => '#', 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
	foreach ( TR_SVC as $s ) {
		$add( $primary, $s, $t['svc'][ $s ]['name'], $svc_parent, 'ic-' . TR_ICON[ $s ], $t['svc'][ $s ]['short'] );
	}
	$add( $primary, 'lezioni', $t['nav']['lezioni'] );
	$add( $primary, 'contatti', $t['nav']['contatti'] );

	$footer = wp_create_nav_menu( "Footer servizi ($sfx)" );
	foreach ( TR_ALL as $s ) {
		$add( $footer, $s, $t['svc'][ $s ]['name'] );
	}
	$legal = wp_create_nav_menu( "Footer studio ($sfx)" );
	$add( $legal, 'home', $t['nav']['home'] );
	$add( $legal, 'chi-sono', $t['nav']['chi-sono'] );
	$add( $legal, 'contatti', $t['nav']['contatti'] );
	$add( $legal, 'privacy', $LEGAL[ $L ]['privacy'][0] );
	$add( $legal, 'cookie', $LEGAL[ $L ]['cookie'][0] );

	$locations['primary'][ $L ] = $primary;
	$locations['footer'][ $L ]  = $footer;
	$locations['legal'][ $L ]   = $legal;
	array_push( $made['menus'], $primary, $footer, $legal );
}
set_theme_mod( 'nav_menu_locations', array( 'primary' => $locations['primary']['it'], 'footer' => $locations['footer']['it'], 'legal' => $locations['legal']['it'] ) );
tr_pll_set( 'nav_menus', array( get_stylesheet() => $locations ) );

// 8. Translated strings (Lingue › Traduzioni stringhe).
$ar_strings = array(
	'Nome Cognome'                                         => 'الاسم واللقب',
	'Traduttore · Interprete'                              => 'مترجم · مترجم فوري',
	'Salta al contenuto'                                   => 'تخطَّ إلى المحتوى',
	'Apri il menu'                                         => 'افتح القائمة',
	'Chiudi'                                               => 'إغلاق',
	'Lingua'                                               => 'اللغة',
	'Home'                                                 => 'الرئيسية',
	'Servizi'                                              => 'الخدمات',
	'Richiedi preventivo'                                  => 'اطلب عرض سعر',
	'Richiedi un preventivo gratuito'                      => 'اطلب عرض سعر مجاني',
	'Scrivimi su WhatsApp'                                 => 'راسلني على واتساب',
	'WhatsApp'                                             => 'واتساب',
	'Buongiorno, vorrei un preventivo per una traduzione.' => $S['ar']['ui']['waText'],
	'Lo studio'                                            => 'عنّي',
	'Contatti'                                             => 'التواصل',
	$S['it']['ui']['footTag']                              => $S['ar']['ui']['footTag'],
	'Studio a [Città] · online in tutta Italia'            => $S['ar']['ui']['area'],
	'Lun–Ven · 9:00–18:00'                                 => $S['ar']['ui']['hours'],
	'P.IVA'                                                => 'الرقم الضريبي P.IVA',
	'Con sede in Italia'                                   => 'مقيم في إيطاليا',
);
$lang_ar = PLL()->model->get_language( 'ar' );
$mo      = new PLL_MO();
$mo->import_from_db( $lang_ar );
foreach ( $ar_strings as $orig => $tr ) {
	$mo->add_entry( $mo->make_entry( $orig, $tr ) );
}
$mo->export_to_db( $lang_ar );

update_option( 'tr_seed_ids', $made );
WP_CLI::success( sprintf( 'Seeded %d pages, 2 forms, 6 menus. Home IT: %s  Home AR: %s', count( $pages ) * 2, $U['it']['home'], $U['ar']['home'] ) );
