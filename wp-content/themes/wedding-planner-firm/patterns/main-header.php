<?php
 /**
  * Title: Main Header
  * Slug: wedding-planner-firm/main-header
  */
?>

<!-- wp:group {"className":"header-wrap","style":{"spacing":{"padding":{"right":"0px","left":"0px","top":"0","bottom":"0"}}},"backgroundColor":"heading","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-group header-wrap has-heading-background-color has-background" style="padding-top:0;padding-right:0px;padding-bottom:0;padding-left:0px"><!-- wp:group {"className":"header-inner","style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"layout":{"type":"constrained","contentSize":"85%"}} -->
<div class="wp-block-group header-inner" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"verticalAlignment":"center","className":"top-bar","style":{"border":{"bottom":{"color":"#ffffff4f","width":"1px"},"top":[],"right":[],"left":[]},"spacing":{"padding":{"bottom":"2px","top":"0px"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center top-bar" style="border-bottom-color:#ffffff4f;border-bottom-width:1px;padding-top:0px;padding-bottom:2px"><!-- wp:column {"verticalAlignment":"center","width":"25%","className":"top-mail-box"} -->
<div class="wp-block-column is-vertically-aligned-center top-mail-box" style="flex-basis:25%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"id":22,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo get_parent_theme_file_uri( '/assets/images/mail.png' ); ?>" alt="" class="wp-image-22"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontFamily":"jost"} -->
<p class="has-background-color has-text-color has-link-color has-jost-font-family" style="font-size:16px;font-style:normal;font-weight:600">wedding12@example.com</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"20%","className":"top-call-box"} -->
<div class="wp-block-column is-vertically-aligned-center top-call-box" style="flex-basis:20%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"id":20,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo get_parent_theme_file_uri( '/assets/images/call.png' ); ?>" alt="" class="wp-image-20"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontFamily":"jost"} -->
<p class="has-background-color has-text-color has-link-color has-jost-font-family" style="font-size:16px;font-style:normal;font-weight:600">+(00) 123 456 789</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"35%","className":"blank-col"} -->
<div class="wp-block-column blank-col" style="flex-basis:35%"></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"20%","className":"top-socail-box"} -->
<div class="wp-block-column is-vertically-aligned-center top-socail-box" style="flex-basis:20%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:social-links {"iconColor":"heading","iconColorValue":"#000000","iconBackgroundColor":"background","iconBackgroundColorValue":"#FFFFFF","size":"has-small-icon-size","className":"is-style-default","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color has-icon-background-color is-style-default"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /-->

<!-- wp:social-link {"url":"#","service":"youtube"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","className":"lower-header","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center lower-header" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"center","width":"30%","className":"header-logo-box"} -->
<div class="wp-block-column is-vertically-aligned-center header-logo-box" style="flex-basis:30%"><!-- wp:site-title {"style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"typography":{"fontStyle":"normal","fontWeight":"800","fontSize":"25px"}},"textColor":"background"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55%","className":"header-menu-box"} -->
<div class="wp-block-column is-vertically-aligned-center header-menu-box" style="flex-basis:55%"><!-- wp:navigation {"textColor":"background","overlayBackgroundColor":"primary","overlayTextColor":"white","metadata":{"ignoredHookedBlocks":["woocommerce/customer-account"]},"style":{"typography":{"fontSize":"15px","fontStyle":"normal","fontWeight":"400","textTransform":"uppercase"},"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:navigation-link {"label":"Home","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Service","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->


<!-- wp:navigation-link {"label":"Pages","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Blog","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Contact Us","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Buy Now","type":"","url":"https://www.cretathemes.com/products/wedding-wordpress-theme","kind":"custom","isTopLevelLink":true, "opensInNewTab":true} /-->
<!-- /wp:navigation --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"15%","className":"header-search-box"} -->
<div class="wp-block-column is-vertically-aligned-center header-search-box" style="flex-basis:15%"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"15px"},"spacing":{"padding":{"left":"var:preset|spacing|40","right":"var:preset|spacing|40","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"fontFamily":"jost"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-jost-font-family has-custom-font-size wp-element-button" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40);font-size:15px;font-style:normal;font-weight:600">Register now</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->