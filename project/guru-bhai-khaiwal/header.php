<?php defined( 'ABSPATH' ) || exit; ?><!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<div class="utility-bar"><div class="container"><span><?php echo esc_html( gbk_option( 'notice' ) ); ?></span><span class="utility-date"><?php echo esc_html( wp_date( 'D, j M Y' ) ); ?></span></div></div>
<header class="site-header"><div class="container header-inner">
<div class="brand"><?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?><a class="brand-symbol" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( gbk_brand() . ' home' ); ?>"><?php echo esc_html( gbk_option( 'monogram' ) ); ?></a><?php } ?><a class="brand-name" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( gbk_brand() ); ?><small><?php echo esc_html( get_bloginfo( 'description' ) ?: 'साफ़ जानकारी · सही संदर्भ' ); ?></small></a></div>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">Menu <span aria-hidden="true">☰</span></button>
<nav id="primary-nav" class="primary-nav" aria-label="Main navigation"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'depth' => 2, 'fallback_cb' => 'gbk_nav_fallback' ) ); ?></nav>
</div></header><main id="main">
