<?php
get_header();
echo '<section class="sec"><div class="wrap">';
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		echo '<article class="post-item"><h2><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2>';
		the_excerpt();
		echo '</article>';
	}
} else {
	echo '<h1>404</h1>';
}
echo '</div></section>';
get_footer();
