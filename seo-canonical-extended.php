<?php
/**
 * Plugin Name:				SEO Canonical Extended
 * Plugin URI:				https://github.com/indocreativemedia/seo-canonical-extended
 * Description:				Extends insertion of rel canonical link into wp_head for all non-singular pages.
 * Version:						1.0.0
 * Requires PHP:			7.4
 * Requires CP:				1.7
 * Author:						IndoCreativeMedia
 * Author URI:				https://www.indocreativemedia.com/
 * License:						GPL v2 or later
 * License URI:				LICENSE
 * Text Domain:				seo-canonical-extended
 */

defined( 'ABSPATH' ) || exit;

add_action('wp_head', function () {
	// Core already handles single posts, pages, and attachments.
	if ( is_singular() ) {
		return;
	}

	// Best practice: no canonical on search results.
	if ( is_search() ) {
		return;
	}

	$canonical = '';

	// Blog posts index
	if ( is_home() ) {
		$page_for_posts = get_option( 'page_for_posts' );
		if ( $page_for_posts ) {
			// Case: a static Page is set as “Posts page”
			$canonical = get_permalink( $page_for_posts );
		} else {
			// Case: front page = default blog index
			$canonical = home_url( '/' );
		}

	// Category / Tag / Custom taxonomy archive
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			$canonical = $link;
		}

	// Author archive
	} elseif ( is_author() ) {
		$author_id = get_queried_object_id();
		$canonical = get_author_posts_url( $author_id );

	// Date archive
	} elseif ( is_date() ) {
		if ( is_day() ) {
			$canonical = get_day_link(
				get_query_var( 'year' ),
				get_query_var( 'monthnum' ),
				get_query_var( 'day' )
			);
		} elseif ( is_month() ) {
			$canonical = get_month_link(
				get_query_var( 'year' ),
				get_query_var( 'monthnum' )
			);
		} elseif ( is_year() ) {
			$canonical = get_year_link(
				get_query_var( 'year' )
			);
		}

	// Post type archive
	} elseif ( is_post_type_archive() ) {
		$canonical = get_post_type_archive_link( get_query_var( 'post_type' ) );
	}

	// Handle paged archives (page 2, 3, …)
	$paged = get_query_var( 'paged', 0 );
	if ( $paged >= 2 ) {
		$canonical = get_pagenum_link( $paged );
	}

	if ( $canonical ) {
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	}
});