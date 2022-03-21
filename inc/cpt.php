<?php
add_action("init", function () {
    $labels = array(
        'name'                => __( 'Ads banners' ),
        'singular_name'       => __( 'Ads banner'),
        'menu_name'           => __( 'Ads banners'),
        'parent_item_colon'   => __( 'Parent banner'),
        'all_items'           => __( 'All banners'),
        'view_item'           => __( 'View banner'),
        'add_new_item'        => __( 'Add New banner'),
        'add_new'             => __( 'Add New'),
        'edit_item'           => __( 'Edit banner'),
        'update_item'         => __( 'Update banner'),
        'search_items'        => __( 'Search banner'),
        'not_found'           => __( 'Not Found'),
        'not_found_in_trash'  => __( 'Not found in Trash')
    );
    $args = array(
        'label'               => __( 'Ads banners'),
        'description'         => __( 'Ads banners by Nixwood'),
        'labels'              => $labels,
        'supports'            => array( 'title', 'author', 'thumbnail', 'revisions', 'custom-fields'),
        'public'              => false,
        'hierarchical'        => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'has_archive'         => false,
        'can_export'          => true,
        'exclude_from_search' => true,
        'yarpp_support'       => true,
        'taxonomies' 	      => array('post_tag'),
        'publicly_queryable'  => true,
        'capability_type'     => 'post'
    );
    register_post_type( 'abn_banners', $args );
});