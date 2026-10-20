<?php
/**
 * A comprehensive accordion widget that renders the category tree of a
 * selected or auto-detected taxonomy tree with nested subcategories.
 *
 * @since 5.2
 */

class md_accordion_widget extends WP_Widget {

	private $data = array();
	private $terms_order = array();

	/**
	 * Create widget attributes and fire any needed actions.
	 *
	 * @since 5.2
	 */

	public function __construct() {
		$this->terms_order = array(
			'count' => __( 'Posts count', 'md-accordion' ),
			'slug' => __( 'Slug', 'md-accordion' ),
			'id' => __( 'Category ID', 'md-accordion' ),
			'term_order' => __( 'Category Order', 'md-accordion' ),
			'latest' => __( 'Latest post', 'md-accordion' )
		);

		parent::__construct( 'md_accordion_widget', __( 'MD &rarr; Accordion Nav', 'md-accordion' ), array(
			'description' => __( 'List category links in a highly organized accordion widget.', 'md-accordion' ),
			'customize_selective_refresh' => true
		) );
	}

	/**
	 * Build the frontend template with lots of sorting possibilities.
	 *
	 * @since 5.2
	 */

	public function widget( $args, $val ) {
		$filter_post_ids = array();
		$page_id = get_queried_object_id();
		$page_taxonomies = get_object_taxonomies( md_get_post_type() );

		// Determine taxonomy automatically or from user setting

		$tax = 'category';

		if ( ! empty( $val['taxonomy'] ) )
			$tax = $val['taxonomy'];
		elseif ( ! empty( $page_taxonomies ) )
			$tax = $page_taxonomies[0];

		$taxonomy = get_taxonomy( $tax );
		$post_type = $taxonomy->object_type;

		// Setup term query args

		$terms_args = array( 'taxonomy' => $tax );

		if ( ! empty( $val['direction'] ) )
			$terms_args['order'] = 'DESC';

		$by_latest = ( $val['order'] ?? '' ) === 'latest';

		if ( ! empty( $val['order'] ) && ! $by_latest )
			$terms_args['orderby'] = sanitize_key( $val['order'] );

		if ( ! empty( $val['exclude'] ) )
			$terms_args['exclude'] = esc_html( $val['exclude'] );

		// Only list posts that share the page's term in the chosen filter taxonomy. A page without one shows nothing.

		$filter_term_id = 0;

		if ( ! empty( $val['auto_filter'] ) && ! empty( $val['filter_taxonomy'] ) ) {
			$page_terms = get_the_terms( $page_id, $val['filter_taxonomy'] );

			if ( empty( $page_terms ) || is_wp_error( $page_terms ) )
				return;

			$filter_term = $page_terms[0];
			$filter_term_id = $filter_term->term_id;
			$filter_posts = new WP_Query( array(
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'fields' => 'ids',
				'tax_query' => array( array(
					'taxonomy' => $filter_term->taxonomy,
					'field' => 'term_id',
					'terms' => $filter_term_id
				) )
			) );

			$filter_post_ids = $filter_posts->posts;
		}

		// Get terms scoped by any relevant filter

		if ( ! empty( $filter_post_ids ) ) {
			$ancestor_ids = array();
			$terms_data = wp_get_object_terms( $filter_post_ids, $tax, $terms_args );

			foreach ( $terms_data as $term )
				if ( $term->parent )
					$ancestor_ids = array_merge( $ancestor_ids, get_ancestors( $term->term_id, $tax, 'taxonomy' ) );

			if ( $ancestor_ids ) {
				$existing_ids = array_column( $terms_data, 'term_id' );
				$missing = array_diff( array_unique( $ancestor_ids ), $existing_ids );

				if ( $missing ) {
					$ancestors = get_terms( array( 'taxonomy' => $tax, 'include' => $missing, 'hide_empty' => false ) );
					$terms_data = array_merge( $terms_data, $ancestors );
				}
			}
		}
		elseif ( $filter_term_id )
			$terms_data = array();
		else {
			$terms_args['pad_counts'] = true;
			$terms_data = get_terms( $terms_args );
		}

		// Group terms by parent ID, used recursively

		$terms_by_parent = array();

		foreach ( $terms_data as $term )
			$terms_by_parent[$term->parent][] = $term;

		$max_depth = ! empty( $val['settings']['parent'] ) ? 1 : 0;

		// Get all posts across all terms in one query, then group by term

		$posts_by_term = $counts_by_term = $newest_by_term = array();

		if ( ! empty( $terms_data ) ) {
			$limit = $filter_post_ids ? null : ( ! empty( $val['posts_per_category'] ) ? $val['posts_per_category'] : 5 );
			$prefetch_args = array(
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'fields' => 'ids',
				'no_found_rows' => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query' => array( array(
					'taxonomy' => $tax,
					'field' => 'term_id',
					'terms' => array_column( $terms_data, 'term_id' ),
					'operator' => 'IN'
				) )
			);

			if ( ! empty( $filter_post_ids ) )
				$prefetch_args['post__in'] = $filter_post_ids;

			$prefetch = new WP_Query( $prefetch_args );
			$all_post_ids = $prefetch->posts;

			if ( ! empty( $all_post_ids ) ) {
				$object_terms = wp_get_object_terms( $all_post_ids, $tax, array(
					'fields' => 'all_with_object_id',
					'orderby' => 'none'
				) );

				$terms_by_post = array();

				foreach ( $object_terms as $object_term )
					$terms_by_post[$object_term->object_id][] = $object_term->term_id;

				// Posts come newest first, so each term's list is in date order and its first post is its newest

				foreach ( $all_post_ids as $position => $post_id )
					foreach ( $terms_by_post[$post_id] ?? array() as $term_id ) {
						$posts_by_term[$term_id][] = $post_id;

						if ( ! isset( $newest_by_term[$term_id] ) )
							$newest_by_term[$term_id] = $position;
					}

				foreach ( $posts_by_term as $term_id => $term_post_ids ) {
					$counts_by_term[$term_id] = count( $term_post_ids );
					$posts_by_term[$term_id] = array_slice( $term_post_ids, 0, $limit );

					// A parent's newest post is the newest across its children

					foreach ( get_ancestors( $term_id, $tax, 'taxonomy' ) as $ancestor_id )
						if ( ! isset( $newest_by_term[$ancestor_id] ) || $newest_by_term[$term_id] < $newest_by_term[$ancestor_id] )
							$newest_by_term[$ancestor_id] = $newest_by_term[$term_id];
				}

				// Roll each term's own count up into all of its ancestors

				foreach ( $counts_by_term as $term_id => $count )
					foreach ( get_ancestors( $term_id, $tax, 'taxonomy' ) as $ancestor_id )
						$counts_by_term[$ancestor_id] = ( $counts_by_term[$ancestor_id] ?? 0 ) + $count;

				// Batch-load post objects only for what's actually displayed

				$displayed_ids = array();

				foreach ( $posts_by_term as $term_id => $ids )
					foreach ( $ids as $id )
						$displayed_ids[] = $id;

				_prime_post_caches( $displayed_ids, true, false );
			}
		}

		// Sort each level by its newest post

		if ( $by_latest ) {
			$last = count( $all_post_ids ?? array() );

			foreach ( $terms_by_parent as $pid => $siblings ) {
				$ranks = array();
				$sorted = array();

				foreach ( $siblings as $index => $term )
					$ranks[$index] = $newest_by_term[$term->term_id] ?? $last;

				asort( $ranks );

				foreach ( array_keys( $ranks ) as $index )
					$sorted[] = $siblings[$index];

				$terms_by_parent[$pid] = ! empty( $val['direction'] ) ? array_reverse( $sorted ) : $sorted;
			}
		}

		// When set to open the page's category, detect it and sort it to the top

		$current_term_id = 0;
		$current_ancestors = array();

		if ( ! empty( $val['settings']['open'] ) ) {

			// Get current term on category page

			if ( is_tax() || is_category() ) {
				$queried = get_queried_object();

				if ( $queried && ! empty( $queried->taxonomy ) && $queried->taxonomy === $tax )
					$current_term_id = $queried->term_id;
			}

			// Or if on single, get this post's term

			elseif ( is_singular() ) {
				$page_terms = get_the_terms( $page_id, $tax );

				if ( ! empty( $page_terms ) && ! is_wp_error( $page_terms ) )
					$current_term_id = $page_terms[0]->term_id;
			}

			// Determine any child categories from found term, if any

			if ( $current_term_id ) {
				$current_ancestors = array_flip( get_ancestors( $current_term_id, $tax, 'taxonomy' ) );

				foreach ( array_keys( $terms_by_parent ) as $pid )
					foreach ( $terms_by_parent[$pid] as $i => $sibling ) {
						if ( $sibling->term_id === $current_term_id || isset( $current_ancestors[$sibling->term_id] ) ) {
							array_unshift( $terms_by_parent[$pid], array_splice( $terms_by_parent[$pid], $i, 1 )[0] );

							break;
						}
					}
			}
		}

		// Show only the first few top-level categories. The page's category is already at the top when open.

		$hidden_terms = 0;
		$terms_limit = absint( $val['terms_limit'] ?? 0 );

		if ( $terms_limit && count( $terms_by_parent[0] ?? array() ) > $terms_limit ) {
			$total_terms = count( $terms_by_parent[0] );
			$hidden_terms = $total_terms - $terms_limit;
			$terms_by_parent[0] = array_slice( $terms_by_parent[0], 0, $terms_limit );
		}

		// Now that we have key info about this page, make it accessible to the whole class

		$this->data = array(
			'widget_id' => $args['widget_id'],
			'val' => $val,
			'tax' => $tax,
			'post_type' => $post_type,
			'filter_post_ids' => $filter_post_ids,
			'page_id' => $page_id,
			'current_term_id' => $current_term_id,
			'current_ancestors' => $current_ancestors,
			'by_parent' => $terms_by_parent,
			'max_depth' => $max_depth,
			'posts_by_term' => $posts_by_term,
			'counts_by_term' => $counts_by_term
		);

		// Render the accordion markup, or nothing at all when no category has posts

		ob_start();

		$this->render_terms();

		$items = ob_get_clean();

		if ( ! $items )
			return;

		echo $args['before_widget'];

		if ( ! empty( $val['title'] ) )
			echo $args['before_title'] . $val['title'] . $args['after_title'];

		echo '<div class="accordion' . ( ! empty( $val['classes'] ) ? ' ' . esc_attr( $val['classes'] ) : '' ) . '">' . $items . '</div>';

		if ( $hidden_terms && ( $archive = get_post_type_archive_link( $post_type[0] ) ) )
			echo '<p class="accordion-see-all small mt-half mb-none"><a href="' . esc_url( $archive ) . '">' . esc_html( sprintf( __( 'See all %1$s %2$s', 'md-accordion' ), number_format_i18n( $total_terms ), strtolower( $taxonomy->labels->name ) ) ) . ' &rarr;</a></p>';

		echo $args['after_widget'];
	}

	/**
	 * Recursively render accordion items for a term and its children.
	 *
	 * @since 6.0
	 */

	private function render_terms( $parent_id = 0, $depth = 1 ) {
		$data = $this->data;

		if ( empty( $data['by_parent'][$parent_id] ) )
			return;

		$val = $data['val'];

		if ( $depth > 1 )
			echo '<div class="accordion accordion-nested">';

		$filtered = ! empty( $data['filter_post_ids'] );
		$only_one = $filtered && $depth === 1 && count( $data['by_parent'][0] ) === 1;

		foreach ( $data['by_parent'][$parent_id] as $term ) {
			$post_ids = $data['posts_by_term'][$term->term_id] ?? array();
			$can_nest = ( $data['max_depth'] === 0 || $depth < $data['max_depth'] ) && ! empty( $data['by_parent'][$term->term_id] );

			if ( empty( $post_ids ) && ! $can_nest )
				continue;

			$count = $filtered ? ( $data['counts_by_term'][$term->term_id] ?? 0 ) : $term->count;
			$see_more = ! empty( $val['see_more'] )
				? strtr( $val['see_more'], array( '{count}' => $count, '{category}' => $term->name ) )
				: sprintf( __( 'See more in %s &rarr;', 'md-accordion' ), "<strong>$term->name</strong>" );

			$in_path = $term->term_id === $data['current_term_id'] || isset( $data['current_ancestors'][$term->term_id] );
			$open = $only_one || ( ! empty( $val['settings']['open'] ) && $in_path ) ? ' open' : '';
			$name = ' name="' . esc_attr( $data['widget_id'] . ( $depth > 1 ? '_' . $parent_id : '' ) ) . '"';

			echo
				 "<details$name class=\"accordion-item" . ( $can_nest ? ' has-children' : '' ) . "\"$open>".
				 '<summary class="accordion-title">'.
				 '<span class="accordion-label">' . ( $depth > 1 ? md_icon( 'folder', array( 'classes' => 'accordion-label-icon' ) ) : '' ) . sanitize_text_field( $term->name ).
				 ( ! empty( $val['settings']['show_count'] ) ? ' <span class="accordion-count small has-muted-color">(' . $count . ')</span>' : '' ).
				 '</span>'.
				 '</summary>';

			if ( $can_nest )
				$this->render_terms( $term->term_id, $depth + 1 );

			if ( ! empty( $post_ids ) ) {
				echo '<ul class="accordion-content' . ( $depth > 1 ? ' small' : '' ) . '">';

				foreach ( $post_ids as $post_id ) {
					$current = $data['page_id'] == $post_id ? ' current-menu-item' : '';
					echo '<li class="menu-item' . $current . '"><a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a></li>';
				}

				if ( ! $filtered )
					echo '<li class="menu-item small"><a href="' . esc_url( get_term_link( $term->term_id ) ) . '">' . wp_kses_post( $see_more ) . '</a></li>';

				echo '</ul>';
			}

			echo '</details>';
		}

		if ( $depth > 1 )
			echo '</div>';
	}

	/**
	 * Sanitize saved data.
	 *
	 * @since 5.2
	 */

	public function update( $new, $val ) {
		$valid_taxonomies = array_keys( get_taxonomies( array( 'public' => true ) ) );
		$order = array_keys( $this->terms_order );

		foreach ( array( 'title', 'see_more', 'exclude', 'classes' ) as $text )
			$val[$text] = sanitize_text_field( $new[$text] );

		foreach ( array( 'posts_per_category', 'terms_limit' ) as $number )
			$val[$number] = absint( $new[$number] ) ?: '';

		$val['settings'] = array();

		foreach ( array( 'open', 'show_count', 'parent' ) as $key )
			if ( ! empty( $new['settings'][$key] ) )
				$val['settings'][$key] = true;

		$val['taxonomy'] = in_array( $new['taxonomy'], $valid_taxonomies, true ) ? $new['taxonomy'] : '';
		$val['filter_taxonomy'] = in_array( $new['filter_taxonomy'], $valid_taxonomies, true ) ? $new['filter_taxonomy'] : '';
		$val['auto_filter'] = $val['filter_taxonomy'] ? true : '';
		unset( $val['filter_term'] );
		$val['direction'] = in_array( $new['direction'], array( 'DESC' ), true ) ? $new['direction'] : '';
		$val['order'] = in_array( $new['order'], $order, true ) ? $new['order'] : '';

		return $val;
	}

	/**
	 * Build widget form settings.
	 *
	 * @since 5.2
	 */

	public function form( $val ) {
		$val = wp_parse_args( (array) $val, array(
			'title' => '',
			'see_more' => '',
			'taxonomy' => '',
			'filter_taxonomy' => '',
			'posts_per_category' => '',
			'terms_limit' => '',
			'direction' => '',
			'order' => '',
			'exclude' => '',
			'classes' => '',
			'settings' => array()
		) );

		$taxonomies = get_taxonomies( array( 'public' => true ) );

		include md_template( 'dropins', 'accordion/widget', true );
	}

}
