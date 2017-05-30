<?php

global $mk_options;

$skin_color_60 = mk_color($mk_options['skin_color'], 0.6);

Mk_Static_Files::addGlobalStyle("

.product-loading-icon
{
	background-color:{$skin_color_60};
}

.mk-woocommerce-carousel .the-title,
.mk-woocommerce-carousel .product-title {
	font-size: {$mk_options['p_size']}px !important;
	text-transform: initial;
}
.mk-product-loop.compact-layout .products .item .mk-love-holder .mk-love-this:hover span,
.mk-product-loop.compact-layout .products .item .mk-love-holder .mk-love-this:hover i{
	color: {$mk_options['skin_color']};
}
");

/*
 * Show responsive shopping cart based on Main Navigation Threshold Width option.
 */

$main_navigation_max_width = $mk_options['responsive_nav_width'] . 'px';

Mk_Static_Files::addGlobalStyle("
	@media handheld, only screen and (max-width:{$main_navigation_max_width}) {
		.add-cart-responsive-state {
			display: block;
		}
	}
");


/* Fix for woocommerce products search width
-------------------------------------------------------------- */

$main_content_responsive_state = $mk_options['content_responsive'] . 'px';

Mk_Static_Files::addGlobalStyle('
	#woocommerce-product-search-field {
		box-sizing: border-box;
		width: 65%;
	}
	.woocommerce-product-search input[type="submit"] {
		width: 33%;
		padding-left: 0 !important;
		padding-right: 0 !important;
		text-align: center;
	}
	@media handheld, only screen and (max-width: ' . $main_content_responsive_state . ') {
		#woocommerce-product-search-field {
			width: 74%;
		}
		.woocommerce-product-search input[type="submit"] {
			width: 24%;
		}
	}
');

