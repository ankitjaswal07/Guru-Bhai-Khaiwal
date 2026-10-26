<?php
/**
 * Document head + site header.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="s20-site" class="s20-site">
	<a class="s20-skip-link screen-reader-text" href="#s20-main"><?php esc_html_e( 'Skip to content', 'guru-bhai-khaiwal' ); ?></a>

	<?php get_template_part( 'template-parts/header/top-bar' ); ?>
	<?php get_template_part( 'template-parts/header/main-header' ); ?>
	<?php get_template_part( 'template-parts/header/marquee' ); ?>

	<main id="s20-main" class="s20-main">
