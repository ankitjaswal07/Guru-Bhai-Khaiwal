<?php
/** Shared source-reviewed page layout with valid block nesting. */
defined('ABSPATH')||exit;
$args=wp_parse_args($args??array(),array('blocks'=>array(),'class'=>''));
$post_id=get_the_ID();$intro=s20_get_field('page_intro',$post_id,s20_get_option('results_intro',''));
$override=s20_get_field('page_heading',$post_id,'');$width=max(320,min(1920,(int)s20_get_option('results_container_width',1500)));$printed=0;
?>
<article id="post-<?php echo absint($post_id); ?>" <?php post_class('s20-section s20-page-section s20-results-page '.$args['class']); ?>>
<div class="s20-container s20-container--wide" style="max-width:<?php echo absint($width); ?>px">
<?php foreach($args['blocks'] as $index=>$block):
$block=wp_parse_args($block,array('page_field'=>'','option_field'=>'','heading_option'=>'','heading'=>''));
$shortcode=s20_resolve_shortcode($block['page_field'],$block['option_field']);
if(!$shortcode && str_contains($args['class'],'game-results')){
 $game=absint(get_post_meta($post_id,'_gbk_game_id',true));
 if($game)$shortcode='[srm_game_history game_id="'.$game.'"]';
}
$output=s20_shortcode_output($shortcode);if(!$output)$output=s20_shortcode_fallback();
$heading=0===$index&&$override?$override:($block['heading']?:($block['heading_option']?s20_get_option($block['heading_option'],get_the_title()):get_the_title()));
s20_section_heading($heading,0===$index?$intro:'',0===$index?'h1':'h2');
if($output):$printed++;s20_scroll_hint(); ?>
<div class="s20-results-block s20-shortcode s20-table-scroll"><?php echo $output; /* Trusted plugin output; its fields are escaped by the plugin. */ ?></div>
<?php endif;endforeach;
if(get_the_content()): ?><div class="s20-entry-content"><?php the_content(); ?></div><?php endif;
s20_render_shortcode(s20_get_field('page_extra_shortcode',$post_id,''),'s20-results-extra');s20_render_page_sections($post_id);
if(!$printed&&current_user_can('manage_options'))echo '<p class="s20-admin-hint">Configure this page’s Results Page Settings or the global Shortcodes page.</p>';
s20_page_notice(); ?>
</div></article>
