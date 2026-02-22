<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('Cretats_Testimonial_Meta_Boxes')){
class Cretats_Testimonial_Meta_Boxes{
    public function __construct(){
        add_action('admin_menu', 'cretats_remove_fields_metabox');
        add_action('save_post',[$this,'cretats_save_testimonial_meta_box']);
        add_action('add_meta_boxes', [$this,'cretats_add_testimonial_meta_box']);
        add_filter('manage_cretats_testimonial_posts_columns',[$this,'cretats_add_id_column']);
        add_action('manage_cretats_testimonial_posts_custom_column',[$this,'cretats_renter_id_column'],10,2);
        // add_action('admin_footer', [$this, 'cretats_id_copy_script']);

    }

    public function cretats_add_id_column($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'date') {
                $new_columns['id'] = 'ID';
            }
            $new_columns[$key] = $value;
        }
    
        return $new_columns;
    }

    public function cretats_renter_id_column($column , $post_id){
        if ($column === 'id') {
            $id = $post_id;
            echo '<span class="cretats-copy-shortcode" data-code="' . esc_attr($id) . '" title="Click to copy"> ID: ' . esc_html($id) . '</span>';
            
        }
    }

    public function cretats_add_testimonial_meta_box() {
        add_meta_box(
            'cretats_testimonial_meta_box',
            'Testimonial Info',
            [$this, 'cretats_testimonial_meta_info_render'],
            'cretats_testimonial',
            'normal',
            'high'
        );
        
        add_meta_box(
            'cretats_sequence_meta_box',             
            'Sequence',                          
            [$this,'cretats_render_sequence_meta_box_render'],    
            'cretats_testimonial',                   
            'side',                           
            'default'                        
        );
    }
    
    public function cretats_testimonial_meta_info_render($post) {

        wp_nonce_field('cretats_testimonial_nonce_action', 'cretats_testimonial_nonce');
        $designation = '';
        $location    = '';
        $rating      = '';
        $info        = '';
    
        if ($post && $post->ID && $post->post_status !== 'auto-draft') {
            $designation = get_post_meta($post->ID, '_cretats_designation', true);
            $location    = get_post_meta($post->ID, '_cretats_location', true);
            $rating      = get_post_meta($post->ID, '_cretats_rating', true);
            $info        = get_post_meta($post->ID, '_cretats_info', true);
        }
        ?>
        <table class="form-table">
            <tr>
                <th><label for="cretats_designation">Designation</label></th>
                <td><input type="text" name="cretats_designation" id="cretats_designation" class="regular-text" value="<?php echo esc_attr($designation); ?>" /></td>
            </tr>
            <tr>
                <th><label for="cretats_location">Location</label></th>
                <td><input type="text" name="cretats_location" id="cretats_location" class="regular-text" value="<?php echo esc_attr($location); ?>" /></td>
            </tr>
            <tr>
                <th><label for="cretats_rating">Rating (1-5)</label></th>
                <td>
                    <div class="star-rating-wrapper" >
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <span class="dashicons <?php echo ($rating >= $i) ? 'dashicons-star-filled' : 'dashicons-star-empty'; ?> cretats-star" data-value="<?php echo esc_attr($i); ?>" ></span>
                        <?php endfor; ?>
                    </div>
                    <input type="number" step="0.1" max="5" min="0" name="cretats_rating" id="cretats_rating" value="<?php echo esc_attr($rating); ?>"  />
                    <small class="text-muted d-block mt-1">You can also give ratings in decimals (e.g., 4.6, 2.8)</small>
                </td>
            </tr>

            <tr>
                <th><label for="cretats_info">Information</label></th>
                <td><textarea name="cretats_info" id="cretats_info" class="large-text" rows="4"><?php echo esc_textarea($info); ?></textarea></td>
            </tr>
        </table>
        <?php
    }
    
    public function cretats_render_sequence_meta_box_render($post) {
        $sequence = 0;
        if ($post && $post->ID && $post->post_status !== 'auto-draft') {
         $sequence = get_post_meta($post->ID, '_cretats_sequence', true);
        }
        ?>
        <label for="cretats_sequence">Order Number</label>
        <input type="number" name="cretats_sequence" id="cretats_sequence" value="<?php echo esc_attr($sequence); ?>" min="0" />
        <p class="description">Lower number appears first.</p>
        <?php
    }

    public function cretats_save_testimonial_meta_box($post_id) {

        if (!isset($_POST['cretats_testimonial_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['cretats_testimonial_nonce'])), 'cretats_testimonial_nonce_action')) {
            return;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
        if (isset($_POST['cretats_designation'])) {
            update_post_meta($post_id, '_cretats_designation', sanitize_text_field(wp_unslash($_POST['cretats_designation'])));
        }
    
        if (isset($_POST['cretats_location'])) {
            update_post_meta($post_id, '_cretats_location', sanitize_text_field(wp_unslash($_POST['cretats_location'])));
        }
    
        if (isset($_POST['cretats_rating'])) {
            update_post_meta($post_id, '_cretats_rating', floatval($_POST['cretats_rating']));
        }
    
        if (isset($_POST['cretats_info'])) {
            update_post_meta($post_id, '_cretats_info', sanitize_textarea_field(wp_unslash($_POST['cretats_info'])));
        }
    
        if (isset($_POST['cretats_image'])) {
            update_post_meta($post_id, '_cretats_image', esc_url_raw(wp_unslash($_POST['cretats_image'])));
        }

        if (isset($_POST['cretats_sequence'])) {
            update_post_meta($post_id, '_cretats_sequence', intval($_POST['cretats_sequence']));
        }
    }
    
}
new Cretats_Testimonial_Meta_Boxes();

}