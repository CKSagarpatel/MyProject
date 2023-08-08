<?php
function add_custom_metabox() {
    add_meta_box(
        'global-notice',
        __( 'Global Notice', 'sitepoint' ),
        'custom_metabox_callback'
    );
}
add_action( 'add_meta_boxes_squash', 'add_custom_metabox' );

function squash_cpt() {

    $args = array(
        'label'                => 'Squash',
        'public'               => true,
        'register_meta_box_cb' => 'add_custom_metabox'
    );

    register_post_type( 'squash', $args );

    register_taxonomy(
        'squash-category',
        'squash',
        array(
            'label' => __( 'Category' ),
            'rewrite' => array( 'slug' => 'squash-category' ),
            'hierarchical' => true,
            'public' => false,
            'rewrite' => false,
            'show_admin_column' => true,
            'show_ui' => true,
            'show_in_rest' => true,
        )
    );
}

add_action( 'init', 'squash_cpt' );


function custom_metabox_callback( $post ) {
    $value1 = get_post_meta( $post->ID, 'date', true );
    $value2 = get_post_meta( $post->ID, 'match_venue', true );
    $value3 = get_post_meta( $post->ID, 'match_manager', true );
    $value4 = get_post_meta( $post->ID, 'time', true );

    echo '<label for="fname">Date:</label>';
    echo '<input type="text" style="width:100%" id="date" name="date" value="' . esc_attr( $value1 ) . '"><br><br>';
    
    echo '<label for="fname">Match and Venue:</label>';
    echo '<input type="text" style="width:100%" id="match_venue" name="match_venue" value="' . esc_attr( $value2 ) . '"><br><br>';

    echo '<label for="fname">Match Manager:</label>';
    echo '<input type="text" style="width:100%" id="match_manager" name="match_manager" value="' . esc_attr( $value3 ) . '"><br><br>';

    echo '<label for="fname">Time:</label>';
    echo '<input type="text" style="width:100%" id="time" name="time" value="' . esc_attr( $value4 ) . '"><br><br>';
}

function save_custom_metabox_data( $post_id ) {
    if ( ! isset( $_POST['date'] ) ) {
        return;
    }
    if ( ! isset( $_POST['match_venue'] ) ) {
        return;
    }
    if ( ! isset( $_POST['match_manager'] ) ) {
        return;
    }
    if ( ! isset( $_POST['time'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_date = sanitize_text_field( $_POST['date'] );
    $my_match_venue = sanitize_text_field( $_POST['match_venue'] );
    $my_match_manager = sanitize_text_field( $_POST['match_manager'] );
    $my_time = sanitize_text_field( $_POST['time'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, 'date', $my_date );
    update_post_meta( $post_id, 'match_venue', $my_match_venue );
    update_post_meta( $post_id, 'match_manager', $my_match_manager );
    update_post_meta( $post_id, 'time', $my_time );
}
add_action( 'save_post', 'save_custom_metabox_data' );
?>