<?php
add_action( 'admin_menu', 'cretats_add_themes_page' );

function cretats_add_themes_page() {
  add_menu_page(
    'FSE Templates',
    'FSE Templates',
    'manage_options',
    'cretats-theme-showcase',
    'cretats_render_themes_page',
    CRETATS_URL . 'assets/img/menu-icon.svg',
    21
  );
}

add_action( 'wp_ajax_get_elemento_collections', 'cretats_handle_get_elemento_collections' );
add_action( 'wp_ajax_nopriv_get_elemento_collections', 'cretats_handle_get_elemento_collections' );

function cretats_handle_get_elemento_collections() {
    $response = wp_remote_post( CRETATS_ELEMENTO_API_BASE . '/getCollections', [
        'headers' => [ 'Content-Type' => 'application/json' ],
        'body'    => json_encode( [] ), // send empty body if not needed
    ] );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( [ 'message' => 'API request failed' ] );
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    wp_send_json( $data );
}

add_action( 'wp_ajax_get_elemento_products', 'cretats_handle_get_elemento_products' );
add_action( 'wp_ajax_nopriv_get_elemento_products', 'cretats_handle_get_elemento_products' );

function cretats_handle_get_elemento_products() {
    $body = json_decode( file_get_contents( 'php://input' ), true );

    $response = wp_remote_post( CRETATS_ELEMENTO_API_BASE . '/getFilteredProducts', [
        'headers' => [ 'Content-Type' => 'application/json' ],
        'body'    => json_encode( $body ),
    ] );

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    wp_send_json( $data );
}

function cretats_render_themes_page() {
    
    delete_option( 'cretats_show_activation_popup' );
    ?>
    <div class='container-fluid theme-my-4'>
        <div class='row g-4 creta-import-box-inner-group'>
        <!-- Sidebar -->
            <div class='col-lg-3'>
                <aside class='bg-white p-4 rounded shadow-sm theme-sidebar'>
                    <button class='btn btn-light w-100 mb-3 text-start fw-semibold search-by-cat-box'>Search By Categories</button>
                    <ul id='theme-filter' class='theme-radio-list list-unstyled small text-muted mb-4'></ul>
                    <!-- New div -->
                    <div class="sidebar-promo-box mt-4 p-3 text-center border rounded">
                        <h5 class="promo-title mb-3">Get Theme Bundle</h5>
                        <img src="<?php echo esc_url( CRETATS_THEME_BUNDLE_IMAGE_URL )?> " class="img-fluid mb-3 promo-img" alt="Promo">
                        <p class="promo-text text-muted">SUMMER SALE: <span class="highlight-off">Extra 20% OFF</span> on WordPress Theme Bundle Use Code: <span class="highlight-code">“HEAT20”</span></p>
                        <a href="<?php echo esc_url( CRETATS_ELEMENTO_API_MAIN ) . 'products/wordpress-theme-bundle'; ?>" target="_blank" class="btn btn-sm btn-primary mt-2">Get Theme Bundle For <span class="strike-price">$86</span>  $68</a>
                    </div>
                </aside>
            </div>
            <!-- Content Area -->
            <div class='col-lg-9'>
                <!-- Search bar with icon -->
                <div class='mb-4 position-relative'>
                    <input type='text' id ='theme-search' class='form-control rounded-pill px-4 py-2 creta-theme-searchbar ps-5' placeholder='Search Templates'>
                    <i class='fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted'></i>
                </div>
                <div id='theme-loader' class='text-center py-5' style='display: none;'>
                    <div class='spinner-border text-primary' role='status'>
                        <span class='visually-hidden'>Loading...</span>
                    </div>
                </div>
                <!-- Themes Grid -->
                <div id='theme-grid' class='row g-4 all-theme-box-grid'></div>
                <div id="theme-readmore-container" class="text-center mt-4">
                    <button id="theme-readmore" class="btn btn-outline-secondary rounded-pill">Load More</button>
                </div>
                <!-- Pagination -->
                <nav class='mt-5'>
                    <div id='theme-pagination' class='d-flex justify-content-center mt-4 gap-2'></div>
                </nav>
            </div>
        </div>
    </div>
    <?php
}

