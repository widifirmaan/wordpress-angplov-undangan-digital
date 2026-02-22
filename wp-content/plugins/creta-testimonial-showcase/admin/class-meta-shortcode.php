<?php
if ( !defined( 'ABSPATH' ) ) exit;
if ( !class_exists( 'Cretats_Testimonial_Meta_ShortCode' ) ) {
    class Cretats_Testimonial_Meta_ShortCode {

        public function __construct() {
            add_action( 'admin_menu', 'cretats_remove_fields_metabox' );
            add_action('save_post',[$this,'cretats_save_testimonial_meta_shortcode']);
            add_action('add_meta_boxes', [$this, 'cretats_add_shortcode_meta_boxes']);
            add_action('wp_ajax_cretats_get_preview_html', [$this, 'cretats_get_preview_html']);

            add_action('wp_ajax_cretats_get_notice_dismiss', [$this, 'cretats_get_notice_dismiss']);

            add_filter('manage_cretats_tms_sc_posts_columns', [$this,'cretats_add_shortcode_column']);
            add_action('manage_cretats_tms_sc_posts_custom_column', [$this,'cretats_render_shortcode_column'], -2, 2);
            // add_action('admin_footer', [$this, 'cretats_shortcode_custom_script']);
            add_action('admin_header', [$this, 'cretats_shortcode_custom_style']);

        }

        public function cretats_add_shortcode_column($columns) {
            $new_columns = [];
            foreach ($columns as $key => $value) {
                if ($key === 'date') {
                    $new_columns['shortcode'] = 'Shortcode';
                }
                $new_columns[$key] = $value;
            }
            return $new_columns;
        }

        public function cretats_render_shortcode_column($column, $post_id) {
            if ($column === 'shortcode') {
                $shortcode = '[cretats_testimonials_sc id="' . esc_attr($post_id) . '"]';
                echo '<span class="cretats-copy-shortcode" data-code="' . esc_attr($shortcode) . '" title="Click to copy">' . esc_html($shortcode) . '</span>';
                
            }
        }

        public function cretats_add_shortcode_meta_boxes() {
            add_meta_box('cretats_preview_box', 'Live Preview', [$this, 'cretats_shortcode_preview_box'], 'cretats_tms_sc', 'normal', 'high');
            add_meta_box('cretats_layout_style_box', 'Customization', [$this, 'cretats_shortcode_layout_style_box'], 'cretats_tms_sc', 'normal', 'default');
            add_meta_box(
                'cretats_shortcode_box',            
                'Short Code',                         
                [$this,'cretats_shortcode_box_render'],     
                'cretats_tms_sc',                 
                'side',                          
                'default'                  
            );
        }

        public function cretats_shortcode_preview_box($post) {
            ?>
            <div id="cretats-live-preview">
                <div class="cretats-preview-area my-4"></div>
            </div>
            <?php
        }

        public function cretats_shortcode_layout_style_box($post) {
            wp_nonce_field('cretats_testimonial_sc_nonce_action', 'cretats_testimonial_sc_nonce');
            $var = cretats_get_shortcode_customization_variables($post->ID);
            $layout = $var['layout'];
            $font = $var['font'];
            $color = $var['color'];
            $bg_color = $var['bg_color'];
            $block_bg_color = $var['block_bg_color'];
            $limit = $var['limit'];
            $columns = $var['columns'];
            $exclude = $var['exclude'];
            $header_font_size = $var['header_font_size'];
            $body_font_size = $var['body_font_size'];
            ?>
            <div class="tabs-wrapper">
                <ul class="tab-st-buttons tab-st-section">
                    <li class="tab-st-button active" data-tab="layout-tab">Layout</li>
                    <li class="tab-st-button" data-tab="style-tab">Style</li>
                    <li class="tab-st-button" data-tab="filter-tab">Filter</li>
                </ul>

                <div class="tab-st-contents">
                    <!-- layouts -->
                    <div class="tab-st-content active" id="layout-tab">
                       
                        <!-- new add -->

                        <div class="row g-4 cretats-layout-selector">

                            <div class="col-auto text-start">
                                <label class="d-flex flex-column align-items-center cretats-layout-option">
                                    <input type="radio" name="cretats_layout" value="layout-5" <?php echo checked($layout, 'layout-5', false); ?>>
                                    <img src="<?php echo esc_url(CRETATS_URL . 'assets/img/layout-5.png'); ?>" alt="Layout 5" class="img-fluid layout-preview">
                                    <small class="layout-label">Layout 1</small>
                                </label>
                            </div>
                        
                            <div class="col-auto text-start">
                                <label class="d-flex flex-column align-items-center cretats-layout-option">
                                    <input type="radio" name="cretats_layout" value="layout-6" <?php echo checked($layout, 'layout-6', false); ?>>
                                    <img src="<?php echo esc_url(CRETATS_URL . 'assets/img/layout-6.png'); ?>" alt="Layout 6" class="img-fluid layout-preview">
                                    <small class="layout-label">Layout 2</small>
                                </label>
                            </div>
                        
                            <div class="col-auto text-start">
                                <label class="d-flex flex-column align-items-center cretats-ts-layout-option">
                                    <input type="radio" name="cretats_layout" value="layout-7" <?php echo checked($layout, 'layout-7', false); ?>>
                                    <img src="<?php echo esc_url(CRETATS_URL . 'assets/img/layout-7.png'); ?>" alt="Layout 7" class="img-fluid layout-preview">
                                    <small class="layout-label">Layout 3</small>
                                </label>
                            </div>
                        
                        </div>


                        <!-- end -->

                        
                    </div>

                    <!-- style -->
                    <div class="tab-st-content" id="style-tab">
                        <div class="row g-4">
                        <div class="col-md-6">
                                <label class="form-label">Columns:</label>
                                <input type="number" name="cretats_columns" value="<?php echo esc_attr($columns); ?>" class="form-control">
                                <small class="text-muted d-block mt-1">In a row, columns will be shown</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Font Style:</label>
                                <select name="cretats_font" class="form-select form-control form-control-color">
                                    <option <?php echo  selected($font, 'Arial', false); ?>>Arial</option>
                                    <option <?php echo  selected($font, 'Helvetica', false); ?>>Helvetica</option>
                                    <option <?php echo  selected($font, 'Georgia', false); ?>>Georgia</option>
                                    <option <?php echo  selected($font, 'Times New Roman', false); ?>>Times New Roman</option>
                                    <option <?php echo  selected($font, 'Trebuchet MS', false); ?>>Trebuchet MS</option>
                                    <option <?php echo  selected($font, 'Verdana', false); ?>>Verdana</option>
                                    <option <?php echo  selected($font, 'Tahoma', false); ?>>Tahoma</option>
                                    <option <?php echo  selected($font, 'Courier New', false); ?>>Courier New</option>
                                    <option <?php echo  selected($font, 'Lucida Grande', false); ?>>Lucida Grande</option>
                                    <option <?php echo  selected($font, 'Segoe UI', false); ?>>Segoe UI</option>
                                </select>
                            </div>



                            <div class="col-md-6">
                                <label class="form-label">Header Font Size:</label>
                                <input type="number" name="cretats_header_font_size" value="<?php echo esc_attr($header_font_size); ?>" class="form-control form-control-color cretats-full-width">
                                <small class="text-muted d-block mt-1">The number will be considered as pixels (px)</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Body Font Size:</label>
                                <input type="number" name="cretats_body_font_size" value="<?php echo esc_attr($body_font_size); ?>" class="form-control form-control-color cretats-full-width">
                                <small class="text-muted d-block mt-1">The number will be considered as pixels (px)</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Color:</label><br>
                                <input type="text" name="cretats_color" value="<?php echo esc_attr($color); ?>" class="form-control color-picker cretats-full-width">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Background Color:</label><br>
                                <input type="text" id="cretats_bg_color" name="cretats_bg_color" value="<?php echo esc_attr($bg_color); ?>" class="bg-color-picker cretats-full-width">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Block Background Color:</label><br>
                                <input type="text" id="cretats_block_bg_color" name="cretats_block_bg_color" value="<?php echo esc_attr($block_bg_color); ?>" class="form-control block-bg-color-picker cretats-full-width">
                            </div>

                            
                        </div>
                    </div>

                    <!-- filter -->
                    <div class="tab-st-content" id="filter-tab">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Limit:</label>
                                <input type="number" name="cretats_limit" value="<?php echo esc_attr($limit); ?>" class="form-control">
                                <small class="text-muted d-block mt-1">How many Testimonials will be shown in front</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Exclude Testimonials:</label>
                                <input type="text" name="cretats_exclude" value="<?php echo esc_attr($exclude); ?>" class="form-control">
                                <small class="text-muted d-block mt-1">Put Testimonial IDs comma-separated (Ex. 120,150)</small>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        <?php
        }

        public function cretats_save_testimonial_meta_shortcode($post_id) {
            if ( ! isset( $_POST['cretats_testimonial_sc_nonce'] ) || !wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['cretats_testimonial_sc_nonce'] )), 'cretats_testimonial_sc_nonce_action' ) ) {
                return;
            }
            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( 'Unauthorized' );
            }

            if (isset($_POST['cretats_layout'])) update_post_meta($post_id, '_cretats_sc_layout', sanitize_text_field(wp_unslash($_POST['cretats_layout'])));
            if (isset($_POST['cretats_font'])) update_post_meta($post_id, '_cretats_sc_font', sanitize_text_field(wp_unslash($_POST['cretats_font'])));
            if (isset($_POST['cretats_color'])) update_post_meta($post_id, '_cretats_sc_color', sanitize_hex_color(wp_unslash($_POST['cretats_color'])));
            if (isset($_POST['cretats_bg_color'])) update_post_meta($post_id, '_cretats_sc_bg_color', sanitize_hex_color(wp_unslash($_POST['cretats_bg_color'])));
            if (isset($_POST['cretats_block_bg_color'])) update_post_meta($post_id, '_cretats_sc_block_bg_color', sanitize_hex_color(wp_unslash($_POST['cretats_block_bg_color'])));
            if (isset($_POST['cretats_limit'])) update_post_meta($post_id, '_cretats_sc_limit', intval(wp_unslash($_POST['cretats_limit'])));
            if (isset($_POST['cretats_columns'])) update_post_meta($post_id, '_cretats_sc_columns', intval(wp_unslash($_POST['cretats_columns'])));
            if (isset($_POST['cretats_exclude'])) update_post_meta($post_id, '_cretats_sc_exclude', sanitize_text_field(wp_unslash($_POST['cretats_exclude'])));
            if (isset($_POST['cretats_header_font_size'])) update_post_meta($post_id, '_cretats_sc_header_font_size', sanitize_text_field(wp_unslash($_POST['cretats_header_font_size'])));
            if (isset($_POST['cretats_body_font_size'])) update_post_meta($post_id, '_cretats_sc_body_font_size', sanitize_text_field(wp_unslash($_POST['cretats_body_font_size'])));

            $limit = isset($_POST['cretats_limit']) ? intval($_POST['cretats_limit']) : 1;
            $exclude_ids = isset($_POST['cretats_exclude']) ? array_map('intval', explode(',', sanitize_text_field(wp_unslash($_POST['cretats_exclude'])))) : [];
            $args = [
                'post_type'      => 'cretats_testimonial',
                'posts_per_page' => $limit,
                'post_status'    => 'publish',
                'post__not_in'   => $exclude_ids,
                'fields'         => 'ids', 
            ];
            $query = new WP_Query($args);
            if (!empty($query->posts)) {
                update_post_meta($post_id, '_cretats_sc_result_ids', implode(',', $query->posts));
            } else {
                delete_post_meta($post_id, '_cretats_sc_result_ids');
            }
            
        }

        public function cretats_shortcode_box_render($post) {
            $shortcode = '[cretats_testimonials_sc id="' . esc_attr($post->ID) . '"]';
            ?>
            <p><strong>Shortcode:</strong> <code style="font-size: 0.9rem;" class="cretats-copy-shortcode"
                    data-code="<?php echo esc_attr($shortcode); ?>">[cretats_testimonials_sc id="<?php echo esc_attr($post->ID); ?>"]</code></p>
                    <small class="text-muted d-block mt-1">First Save or Update ShortCode</small>
            <?php
        }

        public function cretats_get_notice_dismiss() {
            delete_option( 'cretats_show_activation_popup' );

            wp_send_json_success([
                "code" => 200,
                "msg" => "Activation popup preference saved successfully."
            ]);
        }

        public function cretats_get_preview_html() {

            if (
                ! isset( $_POST['cretats_nonce'] ) ||
                ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cretats_nonce'] ) ), 'cretats_preview_action' )
            )
            
            {
                wp_send_json_error( 'Invalid nonce.' );
            }

            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( 'Unauthorized' );
            }

            $layout = isset($_POST['layout']) ? sanitize_text_field(wp_unslash($_POST['layout'])) : '';
            $font = isset($_POST['font']) ? sanitize_text_field(wp_unslash($_POST['font'])) : '';
            $color = isset($_POST['color']) ? sanitize_hex_color(wp_unslash($_POST['color'])) : '';
            $bg_color = isset($_POST['bg_color']) ? sanitize_hex_color(wp_unslash($_POST['bg_color'])) : '';
            $block_bg_color = isset($_POST['block_bg_color']) ? sanitize_hex_color(wp_unslash($_POST['block_bg_color'])) : '';
            $limit = isset($_POST['limit']) ? intval(wp_unslash($_POST['limit'])) : 0;
            $columns = isset($_POST['columns']) ? intval(wp_unslash($_POST['columns'])) : 1;

            $exclude = isset($_POST['exclude']) ? sanitize_text_field(wp_unslash($_POST['exclude'])) : '';
            $exclude_ids = array_filter(array_map('intval', explode(',', $exclude)));

            $header_font_size = isset($_POST['header_font_size']) ? intval(wp_unslash($_POST['header_font_size'])) : 0;
            $body_font_size = isset($_POST['body_font_size']) ? intval(wp_unslash($_POST['body_font_size'])) : 0;

            $args = [
                'post_type' => 'cretats_testimonial',
                'posts_per_page' => $limit ?? 1,
                'post_status'    => 'publish',
                'post__not_in'   => $exclude_ids ?? [],
                'meta_key'       => '_cretats_sequence',
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC'
            ];

            $query = new WP_Query($args);
            $bg_color_lighter = $bg_color ? cretats_lighten_color($bg_color,70) : cretats_lighten_color('#E9D5FF',70);
            $bg_color_darker = $bg_color ? cretats_darken_color($bg_color,30) : cretats_darken_color('#E9D5FF',30);
            // Enqueue assets based on layout
            cretats_get_enqueue_layout_assets($layout,$bg_color,$columns);
            
            $path = CRETATS_PATH . 'templates/' . sanitize_key($layout) . '.php';
            
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<div class="cretats-testimonial-shortcode"' . cretats_get_inline_style(['font_family' => $font,'color' => $color, 'bg_color' => $bg_color, 'block_bg_color' => $block_bg_color]) . '>';

            if (file_exists($path)) {
                include $path;
            } else {
                echo '<p>Layout not found.</p>';
            }
            echo '</div>';

            do_action('admin_footer'); 
            do_action('wp_footer');
            
            wp_die();
        }

        
    }
    new Cretats_Testimonial_Meta_ShortCode();
}