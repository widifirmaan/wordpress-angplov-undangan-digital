<?php
if (!defined( 'ABSPATH' )) {
    exit; 
}
$bg_color_lighter = cretats_lighten_color($bg_color ?: '#b3d9ff', 70);
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <div class="smart-slider-top-heading"
                     <?php echo esc_attr( cretats_get_inline_style(['font_size' => $header_font_size, 'color' => $color])); ?>>
                    Where Dreams Take Flight
                </div>
                <div class="smart-slider-top-desc py-3"
                     <?php echo esc_attr( cretats_get_inline_style(['font_size' => $header_font_size, 'color' => $color])); ?>>
                    Our community shares their journey through strength, balance, and belief.
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($query->have_posts()) : ?>
    <div class="testimonial-scroll-container">
        <?php while ($query->have_posts()) : $query->the_post(); 
            $var = cretats_get_shortcode_customization_variables(get_the_ID(), $columns);
            $designation = $var['designation'];
            $location    = $var['location'];
            $rating      = $var['rating'];
            $info        = $var['info'];
            $image_url   = $var['image_url'];

            $bg_color = get_post_meta($post_id, '_cretats_sc_bg_color', true) ?: null;
        ?>
            <div class='testimonial-item' <?php echo esc_attr (cretats_get_inline_style(['background' => $bg_color_lighter])); ?>>
                <div class="profile-img-div">
                    <img src="<?php echo esc_url($image_url ?: CRETATS_DUMMYIMG_URL); ?>" alt="Person"
                         class="profile-img smart-slider-img-layout-7" />
                </div>
                <div class="testimonial-box" <?php echo esc_attr ( cretats_get_inline_style(['bg_color' => $bg_color])); ?>>
                    <div class="quote-icon">“</div>
                    <?php if ($rating >= 0) : ?>
                        <div class="rating">
                            <?php cretats_get_star_avg_rating($rating, '#FFF2BE', '', 20, 0); ?>
                        </div>
                    <?php endif; ?>
                    <h5 <?php echo esc_attr (cretats_get_inline_style(['font_size' => $header_font_size, 'color' => $color])); ?>
                        class="smart-slider-title-layout-7">
                        <?php the_title(); ?>
                        <?php if ($designation || $location) : ?>
                            <span <?php echo esc_attr (cretats_get_inline_style(['font_size'=>$body_font_size,'color'=> $color])); ?>>
                                (<?php echo esc_html($designation); ?> | <?php echo esc_html($location); ?>)
                            </span>
                        <?php endif; ?>
                    </h5>
                    <p <?php echo esc_attr (cretats_get_inline_style(['font_size' => $body_font_size, 'color' => $color])); ?>
                       class="smart-slider-description-layout-7">
                        <?php echo wp_kses_post(strip_tags(get_the_content(), '<strong><em><a><span><div><br>')); ?>
                        <?php if ($info) : ?>
                            <br/>
                            <span <?php echo esc_attr ( cretats_get_inline_style(['font_size'=>$body_font_size,'color'=> $color])); ?>>
                                <?php echo esc_html($info); ?>
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; wp_reset_postdata(); ?>