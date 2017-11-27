<?php

//
// Create store custom post type
//
function dpsto_store_init() {

	$labels = array(
		'name'                  =>   __( 'Stores', 'dpsto' ),
		'singular_name'         =>   __( 'Store', 'dpsto' ),
		'add_new_item'          =>   __( 'Add New Store', 'dpsto' ),
		'all_items'             =>   __( 'All Stores', 'dpsto' ),
		'edit_item'             =>   __( 'Edit Store', 'dpsto' ),
		'new_item'              =>   __( 'New Store', 'dpsto' ),
		'view_item'             =>   __( 'View Store', 'dpsto' ),
		'not_found'             =>   __( 'No Stores Found', 'dpsto' ),
		'not_found_in_trash'    =>   __( 'No Stores Found in Trash', 'dpsto' )
	);

	$supports = array(
		'title',
		'thumbnail'
	);

	$args = array(
		'label' => 'Stores',
		'labels' => $labels,
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array('slug' => 'stores'),
		'query_var' => true,
		'menu_icon' => 'dashicons-store',
		'supports' => $supports,
        'has_archive' => true
	);
	register_post_type( 'store', $args );
}
add_action( 'init', 'dpsto_store_init' );


//
// Create store metabox
//
function dpsto_store_metabox_init() {

	$prefix = '_dpsto_store_';

    $countries = include( dirname(__FILE__) . "/../admin/countries.php");

    // Sidebar
    $cmb2 = new_cmb2_box( array(
        'id'               => 'dpsto_store_metabox',
		'title'            => __( 'Store Detail', 'dpsto' ),
		'object_types'     => array( 'store' ),
		'show_names'       => true,
    ));

   	    $cmb2->add_field( array(
	        'name'       => __( 'Url', 'dpsto' ),
	        'desc'       => __( '', 'dpsto' ),
	        'id'         => $prefix . 'url',
	        'type'       => 'text_url',
	    ));

	    $cmb2->add_field( array(
	        'name'       => __( 'Locator', 'dpsto' ),
	        'desc'       => __( '', 'dpsto' ),
	        'id'         => $prefix . 'locator_url',
	        'type'       => 'text_url',
	    ));

        $cmb2->add_field( array(
            'name'       => __( 'Country', 'dpsto' ),
	        'desc'       => __( '', 'dpsto' ),
	        'id'         => $prefix . 'country',
	        'type'       => 'select',
            'show_option_none' => false,
            'options'          => $countries,
        ));

};
add_action( 'cmb2_init', 'dpsto_store_metabox_init' );


//
// Create custom admin store headers
//
function dpsto_store_custom_columns_head( $defaults ) {

    $defaults['featured_image'] = __( 'Logo', 'dpsto' );
    $defaults['store-country'] = __( 'Country', 'dpsto' );
	  $defaults['store-url'] = __( 'Store URL', 'dpsto' );

	return $defaults;

}
add_filter( 'manage_edit-store_columns', 'dpsto_store_custom_columns_head', 10 );


//
// Fill custom admin store columns
//
function dpsto_store_custom_columns_content( $column_name, $post_id ) {

    if ( 'featured_image' == $column_name ) {
        $post_featured_image = dpsto_get_featured_image($post_id);
        if ($post_featured_image) {
            echo '<img width="70px" src="' . $post_featured_image . '" />';
        }
    }

	if ( 'store-country' == $column_name ) {
        $countries = include( dirname(__FILE__) . "/../admin/countries.php");
		$store_country = get_post_meta( $post_id, '_dpsto_store_country', true );
        $store_country_label = isset( $countries[ $store_country ] ) ? $countries[ $store_country ] : $store_country;
		echo $store_country_label;
	}

    if ( 'store-url' == $column_name ) {
		$store_url = get_post_meta( $post_id, '_dpsto_store_url', true );
		echo $store_url;
	}

}
add_action( 'manage_store_posts_custom_column', 'dpsto_store_custom_columns_content', 10, 2 );


//
// Fill custom admin store columns
//
function dpsto_sortable_columns() {
  return array(
    'store-country' => 'store-country'
  );
}
add_filter( "manage_edit-store_sortable_columns", "dpsto_sortable_columns" );


//
// Sort custom admin store columns
//
function manage_wp_posts_be_qe_pre_get_posts( $query ) {

   if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {

      switch( $orderby ) {

         case 'store-country':

            $query->set( 'meta_key', '_dpsto_store_country' );
            $query->set( 'orderby', 'meta_value' );
            break;

      }
   }
}
add_action( 'pre_get_posts', 'manage_wp_posts_be_qe_pre_get_posts', 1 );


//
// init store archive page
//
function dpsto_stores_archive($template) {

  if(is_post_type_archive('store')) {
    $theme_files = array('stores-archive.php');
    $exists_in_theme = locate_template($theme_files, false);
    if($exists_in_theme == ''){
      return plugin_dir_path(__FILE__) . '../templates/stores-archive.php';
    }
  }
  return $template;
}
add_filter('archive_template','dpsto_stores_archive');
