<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'md-accordion' ); ?>:</label><br />
    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $val['title'] ); ?>" class="widefat" />
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php echo __( 'Category type', 'md-accordion' ); ?>:</label><br />
    <select id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
        <option value=""><?php echo __( 'Auto-detect categories', 'md-accordion' ); ?></option>
        <?php foreach ( $taxonomies as $count => $tax ) :
            $taxonomy = get_taxonomy( $tax );
        ?>
         <option value="<?php echo esc_attr( $tax ); ?>"<?php echo selected( $val['taxonomy'], esc_attr( $tax ), false ); ?>><?php echo esc_html( $taxonomy->labels->singular_name ); ?></option>
        <?php endforeach; ?>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'direction' ); ?>"><?php echo __( 'List direction', 'md-accordion' ); ?>:</label><br />
    <select id="<?php echo $this->get_field_id( 'direction' ); ?>" name="<?php echo $this->get_field_name( 'direction' ); ?>">
        <option value=""><?php echo __( 'Ascending (default)', 'md-accordion' ); ?></option>
        <option value="DESC"<?php echo selected( $val['direction'], 'DESC', false ); ?>><?php echo __( 'Descending', 'md-accordion' ); ?></option>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php echo __( 'Order by', 'md-accordion' ); ?>:</label><br />
    <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
        <option value=""><?php echo __( 'Name (default)', 'md-accordion' ); ?></option>
        <?php foreach ( $this->terms_order as $term_slug => $term_name ) : ?>
            <option value="<?php echo esc_attr( $term_slug ); ?>"<?php echo selected( $val['order'], esc_attr( $term_slug ), false ); ?>><?php echo esc_html( $term_name ); ?></option>
        <?php endforeach; ?>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'posts_per_category' ); ?>"><?php echo __( 'Posts per category', 'md-accordion' ); ?>:</label><br />
    <input type="number" id="<?php echo $this->get_field_id( 'posts_per_category' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_category' ); ?>" value="<?php echo esc_attr( $val['posts_per_category'] ); ?>" class="widefat" placeholder="5" style="width: 25%;" />
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'terms_limit' ); ?>"><?php echo __( 'Categories to show', 'md-accordion' ); ?>:</label><br />
    <input type="number" id="<?php echo $this->get_field_id( 'terms_limit' ); ?>" name="<?php echo $this->get_field_name( 'terms_limit' ); ?>" value="<?php echo esc_attr( $val['terms_limit'] ); ?>" class="widefat" style="width: 25%;" /><br />
    <span><?php echo __( 'Leave empty to show every category. When there are more, a See all link follows the list.', 'md-accordion' ); ?></span>
</p>

<div>
    <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php echo __( 'Exclude', 'md-accordion' ); ?>:</label>
    <p><input type="text" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $val['exclude'] ); ?>" placeholder="23, 45, 345" class="widefat" /></p>
    <span><?php echo __( 'Exclude category IDs, comma-separated.', 'md-accordion' ); ?></span>
</div>

<hr />

<p>
    <label for="<?php echo $this->get_field_id( 'filter_taxonomy' ); ?>"><?php echo __( 'Only show posts that share the page\'s', 'md-accordion' ); ?>:</label><br />
    <select id="<?php echo $this->get_field_id( 'filter_taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'filter_taxonomy' ); ?>">
        <option value=""><?php echo __( 'Off', 'md-accordion' ); ?></option>
        <?php foreach ( $taxonomies as $tax_slug ) :
            $filter_taxonomy = get_taxonomy( $tax_slug );
        ?>
        <option value="<?php echo esc_attr( $tax_slug ); ?>"<?php echo selected( ! empty( $val['auto_filter'] ) ? $val['filter_taxonomy'] : '', $tax_slug, false ); ?>><?php echo esc_html( $filter_taxonomy->labels->singular_name ); ?></option>
        <?php endforeach; ?>
    </select><br />
    <span><?php echo __( 'For example, list only the docs that share a product page\'s category. Every matching post shows, and a lone category opens.', 'md-accordion' ); ?></span>
</p>

<hr />

<p><strong><?php echo __( 'Settings', 'md-accordion' ); ?></strong></p>

<p>
    <input type="checkbox" id="<?php echo $this->get_field_id( 'settings_open' ); ?>" name="<?php echo $this->get_field_name( 'settings' ); ?>[open]" value="1"<?php checked( ! empty( $val['settings']['open'] ), true ); ?> />
    <label for="<?php echo $this->get_field_id( 'settings_open' ); ?>"><?php echo __( 'Open the current page\'s category', 'md-accordion' ); ?></label>
</p>

<p>
    <input type="checkbox" id="<?php echo $this->get_field_id( 'settings_parent' ); ?>" name="<?php echo $this->get_field_name( 'settings' ); ?>[parent]" value="1"<?php checked( ! empty( $val['settings']['parent'] ), true ); ?> />
    <label for="<?php echo $this->get_field_id( 'settings_parent' ); ?>"><?php echo __( 'Show parent categories only', 'md-accordion' ); ?></label>
</p>

<p>
    <input type="checkbox" id="<?php echo $this->get_field_id( 'settings_show_count' ); ?>" name="<?php echo $this->get_field_name( 'settings' ); ?>[show_count]" value="1"<?php checked( ! empty( $val['settings']['show_count'] ), true ); ?> />
    <label for="<?php echo $this->get_field_id( 'settings_show_count' ); ?>"><?php echo __( 'Show post count per category', 'md-accordion' ); ?></label>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'see_more' ); ?>"><?php echo __( 'See more text', 'md-accordion' ); ?>:</label><br />
    <input type="text" id="<?php echo $this->get_field_id( 'see_more' ); ?>" name="<?php echo $this->get_field_name( 'see_more' ); ?>" value="<?php echo esc_attr( $val['see_more'] ); ?>" class="widefat" />
    <span><?php echo __( '<b>Default:</b> <i>All {count} {category} &rarr;</i>', 'md-accordion' ); ?></span>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'classes' ); ?>"><?php echo __( 'CSS classes', 'md-accordion' ); ?>:</label><br />
    <input type="text" id="<?php echo $this->get_field_id( 'classes' ); ?>" name="<?php echo $this->get_field_name( 'classes' ); ?>" value="<?php echo esc_attr( $val['classes'] ); ?>" class="widefat" placeholder="my-class another-class" />
</p>
