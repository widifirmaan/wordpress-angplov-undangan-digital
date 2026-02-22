<?php
if (!defined( 'ABSPATH' )) {
    exit; 
}
$bg_color_lighter = $bg_color ? cretats_lighten_color($bg_color,70) : '#943C87';
$bg_color_darker = $bg_color ? cretats_darken_color($bg_color,30) : '#943C87';
?>
<?php if ( $query->have_posts() ) : ?>
<div class="grid-container container">
    <div class="row">
        <div class="col-md-12">
            <div id="testimonial-slider" class="testimonial-slider owl-carousel owl-theme">
                <?php
                $count = 0;
                while ( $query->have_posts() ) : $query->the_post();
                    $var = cretats_get_shortcode_customization_variables( get_the_ID(), $columns );
                    $designation   = $var['designation'];
                    $location      = $var['location'];
                    $rating        = $var['rating'];
                    $info          = $var['info'];
                    $image_url     = $var['image_url'];
                    $columns       = $var['columns'];
                    $bootstrap_col = $var['bootstrap_col'];

                    if ( $count % 4 === 0 ) {
                        echo '<div class="item"><div class="testimonial-group">';
                    }
                    ?>

                    <div class="testimonial-card"  <?php echo esc_attr ( cretats_get_inline_style( ['bg_color' => $bg_color] )); ?>>

                    <div class="smart-slider-main-div-layout-6">
                    <div class="profile-pic" <?php echo esc_attr ( cretats_get_inline_style([ 'box_shadow' => '0 0 10px 0 ' . $bg_color_lighter])); ?>>
                        <?php
                        $image_to_use = $image_url ? $image_url : CRETATS_DUMMYIMG_URL;
                        ?>
                        <img src="<?php echo esc_url( $image_to_use ); ?>" alt="" loading="lazy">
                        </div>
                        <div class="testimonial-content">
                            <h5 <?php echo esc_attr ( cretats_get_inline_style(['font_size' => $header_font_size, 'color' => $color])); ?> class="smart-slider-title-layout-6">
                                <?php the_title(); ?>
                            </h5>
                            <span>
                                <?php if ( $designation || $location ) : ?>
                                    <small <?php echo esc_attr ( cretats_get_inline_style(['font_size'=>$body_font_size,'color'=> $color])); ?> class="smart-slider-location-layout-6">
                                        <?php echo esc_html( $designation ); ?> | <?php echo esc_html( $location ); ?>
                                    </small>
                                <?php endif; ?>
                            </span>
                            <p <?php echo esc_attr (cretats_get_inline_style(['font_size' => $body_font_size, 'color' => $color])); ?> class="smart-slider-description-layout-6">
                            <?php echo wp_kses_post( strip_tags( get_the_content(), '<strong><em><a><span><div><br>' ) ); 
                                if($info && $info != ''){?>
                                <?php echo esc_html( $info ); }?>
                            </p>
                        </div>
                    </div>
                       
                        <?php if ( $rating >= 0 ) : ?>
                            <div class="rating" >
                                <div class="smart-slider-rating-layout-6" <?php echo esc_attr (cretats_get_inline_style(['font_size' => $body_font_size, 'color' => $color])); ?>>
                                <i class="bi bi-star-fill"></i>
                                <?php echo wp_kses_post( $rating ); ?>

                                </div>
                               
                                <span class="ms-1" <?php echo esc_attr ( cretats_get_inline_style(['font_size' => $body_font_size, 'color' => $color])); ?>>Rating</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                    $count++;
                    if ( $count % 4 === 0 || $count === $query->post_count ) {
                        echo '</div></div>';
                    }
                endwhile;
                ?>
            </div>

            <!-- Custom Nav Buttons -->
            <div class="nav-buttons" <?php echo esc_attr (cretats_get_inline_style(['bg_color' => $bg_color])); ?>>
                <div class="circle-btn left" <?php echo esc_attr ( cretats_get_inline_style([ 'border_color' => $color, 'border'=> '2px solid'.$color ,'color'=> $bg_color_darker ])); ?>>
                    <i class="bi bi-chevron-double-left" <?php echo esc_attr ( cretats_get_inline_style([ 'color' => $color])); ?>></i>
                </div>
                <div class="circle-btn right" <?php echo esc_attr ( cretats_get_inline_style([ 'border_color' => $color,  'border'=> '2px solid'.$color ,'color'=> $bg_color_darker ])); ?>>
                    <i class="bi bi-chevron-double-right" <?php echo esc_attr ( cretats_get_inline_style([ 'color' => $color])); ?>></i>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; wp_reset_postdata(); ?>





