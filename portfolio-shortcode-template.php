<?php

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$query = new WP_Query(array(
    'post_type'      => 'portafolio',
    'posts_per_page' => intval($atts['posts_per_page']),
    'paged'          => $paged,
));

$output = '<div class="blob-container"><div id="blob"></div></div>';
$output .= '<div class="portfolio-gallery-images" id="portfolio-gallery-images">';

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $post_permalink = get_permalink($post_id);
        $gallery_items = get_post_meta($post_id, '_portfolio_image_gallery', true);
        $gallery_item_ids = explode(',', $gallery_items);
        $github_link = get_post_meta($post_id, '_github_link', true);
        $demo_link = get_post_meta($post_id, '_demo_link', true);

        $output .= '<div class="portfolio-item"><div class="flip-card-inner"> <div id="post-' . esc_attr($post_id) . '" class="' . esc_attr(join(' ', get_post_class())) . '">';

        $output .= '<div class="media-content">';
        if (has_post_thumbnail()) {
            $output .= get_the_post_thumbnail($post_id, 'medium', array('class' => 'image'));
        }
        $output .= '</div>';

        $output .= '<div class="entry-title ctitle">' . get_the_title() . '</div>';

        $output .= '<div class="enlaces">';
        if ($gallery_items) {
            $output .= '<a href="#" class="item-link portfolio-link" id="'. esc_attr($post_id) .'" data-post-id="' . esc_attr($post_id) . '">Galería</a>';
        }

        if ($github_link) {
            $output .= '<a href="' . esc_url($github_link) . '" target="_blank" class="item-link portfolio-item">GitHub</a>';
        }
        if ($demo_link) {
            $output .= '<a href="' . esc_url($demo_link) . '" target="_blank" class="item-link portfolio-item">Demo</a>';
        }
        $output .= '</div>';

        $output .= '<div class="entry-title dtitle">' . get_the_title() . '</div>';
        $excerpt = get_the_excerpt();
        $output .= '<div class="post-excerpt contenido">' . esc_html($excerpt) . '</div>';

        /*
        //Agregar categorías y subcategorías
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
        */

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

        $output .= '<a href="' . esc_url($post_permalink) . '" target="_blank" class="btnDetalles">Leer más &raquo;</a>';

        $output .= '</div></div>';
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
    
    if ($pagination) {
        $output .= '<div id="portfolio-pagination" class="portfolio-pagination">';
        foreach ($pagination as $page_link) {
            $output .= $page_link;
        }
        $output .= '</div>';
    }
    $output .= '</div>';

} else {
    $output .= '<p>No se encontraron portafolios.</p>';
}

$output .= '</div>';

wp_reset_postdata();

echo $output;


