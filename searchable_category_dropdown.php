<?php
/* this code change the default taxonomy to searchable category dropdown */

// 1. Enqueue Select2.js in the WordPress Admin
function enqueue_select2_for_event_category() {
    global $typenow;
    
    // Load only for "event" post type
    if ( $typenow === 'event' ) {
        wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', array( 'jquery' ), null, true );
        wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css', array(), null );

        // Custom JS to initialize Select2
        wp_add_inline_script( 'select2-js', '
            jQuery(document).ready(function($) {
                $("#event_category_search").select2({
                    placeholder: "Search Event Categories",
                    allowClear: true,
                    width: "100%"
                });
            });
        ' );
    }
}
add_action( 'admin_enqueue_scripts', 'enqueue_select2_for_event_category' );

// 2. Replace the Default Taxonomy Meta Box
function replace_event_category_metabox() {
    remove_meta_box( 'event-categorydiv', 'event', 'side' ); // Remove default meta box
    add_meta_box(
        'event_category_metabox', 
        'Event Categories (Searchable)', 
        'event_category_dropdown_callback', 
        'event', 
        'side', 
        'default'
    );
}
add_action( 'add_meta_boxes', 'replace_event_category_metabox' );

// Callback function to display the dropdown
function event_category_dropdown_callback( $post ) {
    $taxonomy = 'event-category';
    $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
    $selected_terms = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

    echo '<select name="event_category[]" id="event_category_search" multiple="multiple" style="width: 100%;">';
    foreach ( $terms as $term ) {
        echo '<option value="' . esc_attr( $term->term_id ) . '" ' . ( in_array( $term->term_id, $selected_terms ) ? 'selected' : '' ) . '>';
        echo esc_html( $term->name );
        echo '</option>';
    }
    echo '</select>';
}

// 3. Save the Selected Taxonomy Terms
function save_event_category_terms( $post_id ) {
    if ( isset( $_POST['event_category'] ) ) {
        $term_ids = array_map( 'intval', $_POST['event_category'] ); // Sanitize input
        wp_set_object_terms( $post_id, $term_ids, 'event-category' );
    }
}
add_action( 'save_post', 'save_event_category_terms' );
