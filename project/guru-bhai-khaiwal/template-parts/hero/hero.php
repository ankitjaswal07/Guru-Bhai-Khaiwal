<?php
defined('ABSPATH')||exit;
$heading=s20_get_option('hero_heading','');$image=s20_get_option('hero_image');
$live='';if(s20_option_enabled('hero_live_status_enabled',true))$live=s20_shortcode_output(s20_get_option('hero_live_status_shortcode','')?:s20_get_option('live_status_shortcode',''));
?>
<section class="s20-section s20-hero" id="s20-hero"><div class="s20-container"><div class="s20-dark-card s20-hero-card">
<div class="gbk-hero-copy">
<p class="gbk-eyebrow"><?php s20_text(s20_get_option('hero_eyebrow','')); ?></p>
<?php if($strip=s20_get_option("hero_strip_image")):s20_image($strip,"large",array("class"=>"gbk-hero-strip-image"));endif; ?><h1><?php s20_text(nl2br(str_replace('\\n',"\n",(string)$heading))); ?></h1>
<div class="s20-hero-desc"><?php s20_rich_text(s20_get_option('hero_description','')); ?></div>
<?php if(s20_option_enabled('hero_pill_enabled',true)): ?><p class="s20-pill"><?php $pill=s20_get_option('hero_pill_url','');if($pill): ?><a href="<?php echo esc_url(s20_link_href($pill)); ?>"><?php endif;if($icon=s20_get_option("hero_pill_icon_image")){s20_image($icon,"thumbnail",array("class"=>"s20-pill-img"));}elseif("none"!==s20_get_option("hero_pill_icon","none")){s20_the_icon(s20_get_option("hero_pill_icon","megaphone"));}s20_text(s20_get_option('hero_pill_text',''));if($pill): ?></a><?php endif; ?></p><?php endif; ?>
<div class="s20-hero-actions"><?php s20_button(s20_get_option('hero_button_text',''),s20_link_href(s20_get_option('hero_button_url','')));s20_button(s20_get_option('hero_secondary_button_text',''),s20_link_href(s20_get_option('hero_secondary_button_url','')),array('class'=>'s20-btn--outline')); ?></div>
<?php if(s20_option_enabled('hero_show_clock',true)): ?><div class="gbk-clock"><span class="gbk-status-dot" aria-hidden="true"></span><span data-s20-clock><?php echo esc_html(wp_date('d/m/Y H:i:s')); ?></span> <span><?php echo esc_html(wp_timezone_string()); ?></span></div><?php endif; ?>
</div>
<?php if($live||$image): ?><div class="gbk-hero-aside"><?php if($image):s20_image($image,'large',array('loading'=>'eager'));endif; ?><?php if($live): ?><div class="s20-live-status s20-shortcode"><?php echo $live; ?></div><?php endif; ?></div><?php endif; ?>
</div></div></section>
