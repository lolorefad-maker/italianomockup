<?php
get_header();
while ( have_posts() ) {
	the_post();
	tr_breadcrumbs();
	the_content();
}
get_footer();
