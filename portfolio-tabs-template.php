<div class="tabs-container" id="portfolio-tabs">
    <div class="tabs">
        <?php
        $terms = get_terms(array(
            'taxonomy' => 'portfolio_category',
            'parent' => 0,
            'hide_empty' => false,
        ));

        echo '<a href="/" class="tab activa" target="_self" rel="home" aria-current="page">Todos</a>';
        foreach ($terms as $term) {
            echo '<div class="tab" data-term-id="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</div>';
        }
        ?>
    </div>
</div>
