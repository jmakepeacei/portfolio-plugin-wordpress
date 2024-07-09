<?php

/**
 * Plugin Name: Makepeace Portfolio
 * Plugin URI: https://makepeacecorp.com
 * Description: Plugin para la creacion de portafolio de proyectos.
 * Version: 1.0
 * Author: Jose Ma. Makepeace
 * Author URI: https://makepeacecorp.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; 
}

function disable_image_crop($sizes) {
    $sizes['thumbnail']['crop'] = false;
    $sizes['medium']['crop'] = false;
    $sizes['large']['crop'] = false;
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'disable_image_crop');

function create_portfolio_post_type()
{
    $labels = array(
        'name'                  => _x('Portfolios', 'Post Type General Name', 'textdomain'),
        'singular_name'         => _x('Portfolio', 'Post Type Singular Name', 'textdomain'),
        'menu_name'             => _x('Portfolios', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Portfolio', 'Add New on Toolbar', 'textdomain'),
        'archives'              => __('Portfolio Archives', 'textdomain'),
        'attributes'            => __('Portfolio Attributes', 'textdomain'),
        'parent_item_colon'     => __('Parent Portfolio:', 'textdomain'),
        'all_items'             => __('All Portfolios', 'textdomain'),
        'add_new_item'          => __('Add New Portfolio', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'new_item'              => __('New Portfolio', 'textdomain'),
        'edit_item'             => __('Edit Portfolio', 'textdomain'),
        'update_item'           => __('Update Portfolio', 'textdomain'),
        'view_item'             => __('View Portfolio', 'textdomain'),
        'view_items'            => __('View Portfolios', 'textdomain'),
        'search_items'          => __('Search Portfolio', 'textdomain'),
        'not_found'             => __('Not found', 'textdomain'),
        'not_found_in_trash'    => __('Not found in Trash', 'textdomain'),
        'featured_image'        => __('Featured Image', 'textdomain'),
        'set_featured_image'    => __('Set featured image', 'textdomain'),
        'remove_featured_image' => __('Remove featured image', 'textdomain'),
        'use_featured_image'    => __('Use as featured image', 'textdomain'),
        'insert_into_item'      => __('Insert into portfolio', 'textdomain'),
        'uploaded_to_this_item' => __('Uploaded to this portfolio', 'textdomain'),
        'items_list'            => __('Portfolios list', 'textdomain'),
        'items_list_navigation' => __('Portfolios list navigation', 'textdomain'),
        'filter_items_list'     => __('Filter portfolios list', 'textdomain'),
    );

    $args = array(
        'label'                 => __('Portfolio', 'textdomain'),
        'description'           => __('A custom post type for portfolios', 'textdomain'),
        'labels'                => $labels,
        'menu_icon'             => 'dashicons-portfolio',
        'supports'              => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'taxonomies'            => array('portfolio_category'),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'hierarchical'          => false,
        'exclude_from_search'   => false,
        'show_in_rest'          => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'rewrite'               => array('slug' => 'portfolio'),
    );

    register_post_type('portafolio', $args);
}
add_action('init', 'create_portfolio_post_type');


function create_portfolio_taxonomy()
{
    $labels = array(
        'name'              => _x('Portfolio Categories', 'taxonomy general name'),
        'singular_name'     => _x('Portfolio Category', 'taxonomy singular name'),
        'search_items'      => __('Search Portfolio Categories'),
        'all_items'         => __('All Portfolio Categories'),
        'parent_item'       => __('Parent Portfolio Category'),
        'parent_item_colon' => __('Parent Portfolio Category:'),
        'edit_item'         => __('Edit Portfolio Category'),
        'update_item'       => __('Update Portfolio Category'),
        'add_new_item'      => __('Add New Portfolio Category'),
        'new_item_name'     => __('New Portfolio Category Name'),
        'menu_name'         => __('Portfolio Categories'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'portfolio-category'),
        'show_in_rest'          => true,
    );

    register_taxonomy('portfolio_category', array('portafolio'), $args);
}
add_action('init', 'create_portfolio_taxonomy', 0);

function portfolio_plugin_activate()
{
    create_portfolio_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'portfolio_plugin_activate');


function portfolio_plugin_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'portfolio_plugin_deactivate');

function add_portfolio_metaboxes()
{
    add_meta_box(
        'portfolio_images',
        'Portfolio Images',
        'portfolio_image_gallery_meta_box_callback',
        'portafolio',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_portfolio_metaboxes');

function portfolio_image_gallery_meta_box_callback($post)
{
    wp_nonce_field('save_portfolio_image_gallery', 'portfolio_image_gallery_nonce');

    $images = get_post_meta($post->ID, '_portfolio_image_gallery', true);
    $images = json_decode($images, true) ?: [];

    echo '<div id="portfolio-image-gallery-container">';
    echo '<ul class="portfolio-image-gallery-items">';
    if (!empty($images)) {

        foreach ($images as $image) {            
            $image_url = esc_url(wp_get_attachment_image_url($image, 'thumbnail'));
            echo '<li class="image" data-attachment_id="' . $image . '" style="max-width:100px;margin:5px;">';
            echo '<img src="' . $image_url . '" style="max-width:100px;margin:5px;" />';
            echo '<a href="#" class="remove-icon">×</a>';
            echo '</li>';
        }
    }
    echo '</ul>';
    echo '</div>';
    echo '<input type="hidden" id="text_portfolio_image_gallery" name="text_portfolio_image_gallery" value="' . esc_attr(json_encode($images)) . '" />';
    echo '<input type="button" id="portfolio_image_gallery_button" class="button" value="Add Images" />';
    echo '<input type="button" class="button button-primary save-portfolio-image" value="Save Images" />';
}

function portfolio_save_image_meta_box_data($post_id)
{
    if (!isset($_POST['portfolio_image_gallery_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['portfolio_image_gallery_nonce'], 'save_portfolio_image_gallery')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['text_portfolio_image_gallery'])) {        
        $images = json_decode(stripslashes($_POST['text_portfolio_image_gallery']), true);
        update_post_meta($post_id, '_portfolio_image_gallery', json_encode($images));
    }
}
add_action('save_post', 'portfolio_save_image_meta_box_data');

function portfolio_add_icon_meta_box()
{
    add_meta_box(
        'portfolio_icon_gallery',
        'Portfolio Icons',
        'portfolio_icon_gallery_meta_box_callback',
        'portafolio',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'portfolio_add_icon_meta_box');

function portfolio_icon_gallery_meta_box_callback($post)
{
    wp_nonce_field('save_portfolio_icon_gallery', 'portfolio_icon_gallery_nonce');

    $icons = get_post_meta($post->ID, '_portfolio_icon_gallery', true);
    $icons = json_decode($icons, true) ?: [];

    echo '<div id="portfolio-icon-gallery-container">';
    echo '<ul class="portfolio-icon-gallery-items">';
    if (!empty($icons)) {

        foreach ($icons as $icon) {
            $image_id = esc_attr($icon['image_id']);
            $image_url = esc_url(wp_get_attachment_image_url($image_id, 'thumbnail'));
            $name = esc_attr($icon['name']);
            $link = esc_url($icon['link']);
            echo '<li class="icon" data-attachment_id="' . $image_id . '" style="max-width:100px;margin:5px;">';
            echo '<img src="' . $image_url . '" style="max-width:100px;margin:5px;" />';
            echo '<input type="text" name="icon_names[]" value="' . $name . '" placeholder="Icon Name" />';
            echo '<input type="text" name="icon_links[]" value="' . $link . '" placeholder="Icon Link" />';
            echo '<a href="#" class="remove-icon">×</a>';
            echo '</li>';
        }
    }
    echo '</ul>';
    echo '</div>';
    echo '<input type="hidden" id="text_portfolio_icon_gallery" name="text_portfolio_icon_gallery" value="' . esc_attr(json_encode($icons)) . '" />';
    echo '<a href="#" class="add-icons button">Add Icons</a>';
    echo '<input type="button" class="button button-primary save-portfolio-icons" value="Save Icons" />';
}

function portfolio_save_icon_meta_box_data($post_id)
{
    if (!isset($_POST['portfolio_icon_gallery_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['portfolio_icon_gallery_nonce'], 'save_portfolio_icon_gallery')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['text_portfolio_icon_gallery'])) {
        $icons = json_decode(stripslashes($_POST['text_portfolio_icon_gallery']), true);
        update_post_meta($post_id, '_portfolio_icon_gallery', json_encode($icons));
    }
}
add_action('save_post', 'portfolio_save_icon_meta_box_data');

function add_blog_post_to_query($query)
{
    if ($query->is_home() && $query->is_main_query()) {
        $query->set('post_type', array('post', 'portafolio_posts'));
        $query->set('posts_per_page',16);
    }
}
add_action('pre_get_posts', 'add_blog_post_to_query');

function portafolio_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'posts_per_page' => 16,
    ), $atts, 'portafolio_posts');

    ob_start();

    include(plugin_dir_path(__FILE__) . 'portfolio-tabs-template.php');

    include(plugin_dir_path(__FILE__) . 'portfolio-shortcode-template.php');

    return ob_get_clean();
}
add_shortcode('portafolio_posts', 'portafolio_shortcode');


function portfolio_add_meta_boxes()
{
    add_meta_box(
        'portfolio_links_meta_box', 
        'GitHub - Demo: Links', 
        'portfolio_links_meta_box_callback', 
        'portafolio', 
        'normal', 
        'high' 
    );
}
add_action('add_meta_boxes', 'portfolio_add_meta_boxes');

function portfolio_links_meta_box_callback($post)
{
    wp_nonce_field('save_portfolio_meta', 'portfolio_meta_nonce');

    $github_link = get_post_meta($post->ID, '_github_link', true);
    $demo_link = get_post_meta($post->ID, '_demo_link', true);

    echo '<label for="github_link">Enlace a GitHub:</label>';
    echo '<input type="text" id="github_link" name="github_link" value="' . esc_attr($github_link) . '" style="width: 100%;" />';

    echo '<label for="demo_link">Enlace al Demo:</label>';
    echo '<input type="text" id="demo_link" name="demo_link" value="' . esc_attr($demo_link) . '" style="width: 100%;" />';
}

function portfolio_save_links_meta_box_data($post_id)
{
    if (!isset($_POST['portfolio_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['portfolio_meta_nonce'], 'save_portfolio_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['github_link'])) {
        update_post_meta($post_id, '_github_link', sanitize_text_field($_POST['github_link']));
    }

    if (isset($_POST['demo_link'])) {
        update_post_meta($post_id, '_demo_link', sanitize_text_field($_POST['demo_link']));
    }
}
add_action('save_post', 'portfolio_save_links_meta_box_data');


function portfolio_enqueue_scripts()
{
    global $typenow;     
    if ($typenow == 'portafolio') {
        wp_enqueue_media();
        wp_enqueue_script('portfolio-admin-js', plugin_dir_url(__FILE__) . 'js/portfolio-admin.js', array('jquery'), '1.0', true);
        wp_enqueue_style('portfolio-admin-css', plugin_dir_url(__FILE__) . 'css/portfolio-admin.css', array(), '1.0');
    }
}
add_action('admin_enqueue_scripts', 'portfolio_enqueue_scripts');


function filter_portfolios()
{
    $paged = $_POST['paged'] ? intval($_POST['paged']) : 1;
    $term_id = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;

    $args = array(
        'post_type' => 'portafolio',
        'posts_per_page' => 16,
        'paged'          => $paged,
    );

    if ($term_id != 0) {
        $args = array(
            'post_type' => 'portafolio',
            'paged'     => $paged,
            'posts_per_page' => 16,
            'tax_query' => array(
                array(
                    'taxonomy' => 'portfolio_category',
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ),
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $post_permalink = get_permalink($post_id);
            $gallery_items = get_post_meta($post_id, '_portfolio_image_gallery', true); // Ajustar esto al nombre del campo de imágenes del portafolio
            $gallery_item_ids = explode(',', $gallery_items);
            $github_link = get_post_meta($post_id, '_github_link', true);
            $demo_link = get_post_meta($post_id, '_demo_link', true);

            $output .= '<div class="portfolio-item"><div class="flip-card-inner"> <div id="post-' . esc_attr($post_id) . '" class="portafolio ' . esc_attr(join(' ', get_post_class())) . '">';

            $output .= '<div class="media-content">';
            if (has_post_thumbnail()) {
                $output .= get_the_post_thumbnail($post_id, 'medium', array('class' => 'image'));
            }
            $output .= '</div>';

            $output .= '<div class="enlaces">';
            if ($gallery_items) {
                $output .= '<a href="#" class="item-link portfolio-link" id="' . esc_attr($post_id) . '" data-post-id="' . esc_attr($post_id) . '">Galería</a>';
            }

            if ($github_link) {
                $output .= '<a href="' . esc_url($github_link) . '" target="_blank" class="item-link portfolio-item">GitHub</a>';
            }
            if ($demo_link) {
                $output .= '<a href="' . esc_url($demo_link) . '" target="_blank" class="item-link portfolio-item">Demo</a>';
            }
            $output .= '</div>';

            $output .= '<div>Categorias y Subcategorias';
            $terms = get_the_terms($post_id, 'portfolio_category');

            if ($terms && !is_wp_error($terms)) {
                $categories = array();
                foreach ($terms as $term) {
                    if ($term->parent != 0) {
                        $parent = get_term($term->parent, 'portfolio_category');
                        if (!is_wp_error($parent) && !empty($parent->name)) {
                            $categories[] = $parent->name . ' -> ' . $term->name;
                        } else {
                            error_log('Error al obtener el término: ' . $parent->get_error_message());
                        }
                    } else {
                        $categories[] = $term->name;
                    }
                }
                $categories_list = join(', ', $categories);
                $output .= '<p class="portfolio-categories">Categories: ' . esc_html($categories_list) . '</p>';
            }
            $output .= '</div>';



            if (!empty($gallery_items)) {
                $gallery_items = trim($gallery_items, '[]');
                $gallery_item_ids = explode(',', $gallery_items);
                $gallery_item_ids = array_filter($gallery_item_ids, function ($id) {
                    return is_numeric($id) && !empty(trim($id));
                });

                $gallery_item_ids = array_unique($gallery_item_ids);
                $output .= '<div class="gallery-hidden gallery-item" id="gallery-item-' . esc_attr($post_id) . '">';
                foreach ($gallery_item_ids as $gallery_item_id) {
                    $image_url = wp_get_attachment_image_url($gallery_item_id, 'medium');
                    if ($image_url) {
                        $output .= '<a href="' . esc_url($image_url) . '" class="gallery-item" data-lightbox="portfolio-gallery-' . $post_id . '">';
                        $output .= '<img src="' . esc_url($image_url) . '" style="max-width:100px;margin:5px;" alt="" />';
                        $output .= '</a>';
                    } else {
                        echo 'URL de imagen no válida para ID ' . $gallery_item_id;
                    }
                }
                $output .= '</div>';
            }

            $output .= '</div>';

            $output .= '<div class="gwlogo">';
            if ($github_link) {
                $output .= '<img src="' . plugin_dir_url(__FILE__) . 'img/githublogo.png" width="24px">';
            }
            if ($github_link) {
                $output .= '<img src="' . plugin_dir_url(__FILE__) . 'img/weblogo.png" width="24px">';
            }
            $output .= '</div>';            

            $output .= '<div class="liconos">';
            $icon_items = get_post_meta($post_id, '_portfolio_icon_gallery', true);
            if (!empty($icon_items)) {
                $icon_items = json_decode($icon_items, true);
                $output .= '<div class="portfolio-icons">';
                foreach ($icon_items as $icon_item) {
                    $icon_url = wp_get_attachment_image_url($icon_item['image_id'], 'thumbnail');
                    $icon_name = esc_html($icon_item['name']);
                    $icon_link = esc_url($icon_item['link']);
                    $output .= '<div class="portfolio-icon">';
                    if (!empty($icon_link)) {
                        $output .= '<a href="' . $icon_link . '" target="_blank">';
                    }
                    $output .= '<img src="' . $icon_url . '" style="width="24px" height="24px"" alt="' . $icon_name . '">';
                    $output .= '<span class="icon-name">' . $icon_name . '</span>';
                    if (!empty($icon_link)) {
                        $output .= '</a>';
                    }
                    $output .= '</div>';
                }
                $output .= '</div>';
            }
            $output .= '</div>';

            $output .= '<div class="ctitulo"><div class="table-cell">' . get_the_title() . '</div></div>';
            $output .= '<div class="entry-title dtitle">' . get_the_title() . '</div>';
            $excerpt = get_the_excerpt();
            $output .= '<div class="post-excerpt contenido">' . esc_html($excerpt) . '</div>';

            $output .= '<a href="' . esc_url($post_permalink) . '" target="_blank" class="btnDetalles">Leer más &raquo;</a>';

            $output .= '</div></div>';
        }
    } else {
        $output .= '<p>No se encontraron portafolios.</p>';
    }

    $output .= '<div class="pagination">';
    $big = 999999999;
    $pagination = paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, $paged),
        'total'     => $query->max_num_pages,
        'prev_text' => __('« Anterior'),
        'next_text' => __('Siguiente »'),
        'type'      => 'array',
    ));

    $pagination = str_replace('/wp-admin/admin-ajax.php', '/', $pagination);
    if ($pagination) {
        $output .= '<div id="portfolio-pagination" class="portfolio-pagination">';
        foreach ($pagination as $page_link) {
            $output .= $page_link;
        }
        $output .= '</div>';
    }
    $output .= '</div>';

    wp_reset_postdata();
    echo $output;

    die();
}
add_action('wp_ajax_filter_portfolios', 'filter_portfolios');
add_action('wp_ajax_nopriv_filter_portfolios', 'filter_portfolios');

function portfolio_enqueue_frontend_scripts()
{
    wp_enqueue_style('bootstrap-css', plugin_dir_url(__FILE__)  . '/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', plugin_dir_url(__FILE__)  . '/js/bootstrap.min.js', array('jquery'), '5.3.3', true);

    wp_enqueue_script('portfolio-frontend', plugin_dir_url(__FILE__) . 'js/portfolio-frontend.js', array('jquery'), '1.0', true);

    wp_enqueue_script('tab-filter-js', plugin_dir_url(__FILE__) . 'js/tab-filter.js', array('jquery'), '1.0', true);
    wp_localize_script('tab-filter-js', 'ajaxurl', admin_url('admin-ajax.php'));

    wp_enqueue_style('portfolio-frontend-css', plugin_dir_url(__FILE__) . 'css/portfolio-frontend.css', array(), '1.0');

    wp_enqueue_script('portfolio-ajax-pagination', plugin_dir_url(__FILE__) . 'js/portfolio-ajax-pagination.js', array('jquery'), '1.0', true);
    wp_localize_script('portfolio-ajax-pagination', 'portfolioAjax', admin_url('admin-ajax.php'));
    
}
add_action('wp_enqueue_scripts', 'portfolio_enqueue_frontend_scripts');

