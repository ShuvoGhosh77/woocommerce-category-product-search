<?php

// AJAX Handler for Product Search
// AJAX Handler for Product Search and Default Load
add_action('wp_ajax_product_search_by_category', 'product_search_by_category');
add_action('wp_ajax_nopriv_product_search_by_category', 'product_search_by_category');
function product_search_by_category() {
    global $wpdb;

    $search = sanitize_text_field($_POST['search'] ?? '');
    $category = sanitize_text_field($_POST['category'] ?? '');
    $paged = intval($_POST['paged'] ?? 1); // Get current page number

    $query_args = array(
        'post_type' => 'product',
        'posts_per_page' => 6,
        'paged' => $paged,
        'orderby' => 'title',
        'order' => 'DSC',
        'product_cat' => $category,
    );

    if (!empty($search)) {
        $query_args['s'] = $search;
    }

    $query = new WP_Query($query_args);

    // Strict filter (if needed)
    if (!empty($search)) {
        $strict_posts = array();
        foreach ($query->posts as $post) {
            if (stripos($post->post_title, $search) !== false) {
                $strict_posts[] = $post;
            }
        }
        $query->posts = $strict_posts;
        $query->post_count = count($strict_posts);
    }

    if ( $query->have_posts() ) :
        ?>
        <ul class="product-list">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <li class="product-item">
                     <?php
                   $product_image = get_post_meta(get_the_ID(), 'thumbnailurl', true);
                ?>
				    <div class="product-media-wrapper" style="position: relative;">
                            <?php
                            // Get the main WooCommerce product image
                            $product = wc_get_product( get_the_ID() );
                            if ( $product ) {
                                echo $product->get_image( 'woocommerce_thumbnail', ['class' => 'product-thumbnail'] );
                            }
                            ?>
                     </div>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php
                        // Display the product price
                        $product = wc_get_product( get_the_ID() );
                        if ( $product ) {
                            echo '<div class="product-price">' . $product->get_price_html() . '</div>';
                        }
                    ?>

                    
                </li>
            <?php endwhile; ?>
        </ul>

        <?php
        // Pagination
        $total_pages = $query->max_num_pages;
        if ( $total_pages > 1 ) :
            ?>
            <div class="product-pagination">
                <?php for ( $i = 1; $i <= $total_pages; $i++ ) : 
                    $active_class = ( $i === $paged ) ? 'active' : '';
                    ?>
                    <button class="pagination-button <?php echo esc_attr( $active_class ); ?>" data-page="<?php echo esc_attr( $i ); ?>">
                        <?php echo esc_html( $i ); ?>
                    </button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <?php
    else :
        echo '<p>No products found.</p>';
    endif;

    wp_reset_postdata();

    wp_die();
}



function category_product_search_shortcode($atts) {
    $atts = shortcode_atts( array(
        'category' => '',
    ), $atts, 'category_product_search' );

    $category = sanitize_text_field( $atts['category'] );

    ob_start();
    ?>

    <div class="product-search-wrapper" data-category="<?php echo esc_attr($category); ?>">
        <input type="text" class="product-search-input" placeholder="Search Products..." />
        <div class="product-search-results"></div>
    </div>

    <style>
       
    </style>

<script>
jQuery(document).ready(function($) {
    const wrapper = $('.product-search-wrapper');
    const input = wrapper.find('.product-search-input');
    const resultsDiv = wrapper.find('.product-search-results');
    const category = wrapper.data('category');
    let paged = 1;
    let search = '';

    function fetchProducts(loadMore = false) {
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'product_search_by_category',
                search: search,
                category: category,
                paged: paged
            },
            beforeSend: function() {
                resultsDiv.html('<div class="loading"></div>');
            },
            success: function(response) {
                resultsDiv.html(response);
            }
        });
    }
    fetchProducts();
    input.on('keyup', function() {
        search = $(this).val().trim();
        paged = 1; // reset
        if (search.length >= 3 || search.length == 0) {
            fetchProducts();
        }
    });
    $(document).on('click', '.pagination-button', function() {
        paged = $(this).data('page');
        fetchProducts();
    });
});
</script>



    <?php
    return ob_get_clean();
}
add_shortcode('category_product_search', 'category_product_search_shortcode'); 