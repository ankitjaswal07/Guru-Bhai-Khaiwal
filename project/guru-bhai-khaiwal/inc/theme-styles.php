<?php
/** ACF style controls. Values are validated on save AND when generating CSS. */
defined( 'ABSPATH' ) || exit;
function s20_style_schema(): array {
    $schema = array();
    $colors = array(
        'Palette' => array( 'surface_bg'=>'#ffffff','surface_text'=>'#111111','cream'=>'#f7f4ef','light_accent'=>'#604000','background'=>'#f7f4ef','text'=>'#292524','accent'=>'#9c6927','accent_hover'=>'#7b4e18','muted'=>'#675e58','border'=>'#e7dfd5','link'=>'#772d36' ),
        'Header & footer' => array( 'header_bg'=>'#ffffff','header_text'=>'#332527','announcement_bg'=>'#47232a','announcement_text'=>'#ffffff','footer_bg'=>'#342027','footer_text'=>'#ffffff','marquee_bg'=>'#efe6d8','marquee_text'=>'#111111' ),
        'Cards & buttons' => array( 'card_bg'=>'#57252f','card_text'=>'#ffffff','strip_bg'=>'#57252f','strip_text'=>'#ffffff','button_bg'=>'#772d36','button_text'=>'#ffffff','button_hover'=>'#57252f' ),
        'Tables' => array( 'table_bg'=>'#ffffff','table_text'=>'#17202a','table_header_bg'=>'#f5c62f','table_header_text'=>'#111111','table_alt'=>'#f9fae5','table_border'=>'#d1d5db','table_link'=>'#604000' ),
    );
    foreach ( $colors as $tab=>$items ) { foreach ( $items as $name=>$default ) { $schema[$name] = array('tab'=>$tab,'type'=>'color_picker','default'=>$default); } }
    foreach ( array(
        'body_size'=>array(16,12,24,'Typography'), 'heading_size'=>array(34,20,64,'Typography'), 'line_height'=>array(1.6,1.2,2.2,'Typography'),
        'container_width'=>array(1240,800,1920,'Layout & spacing'),'page_gutter'=>array(20,8,80,'Layout & spacing'),'section_spacing'=>array(36,0,100,'Layout & spacing'),'grid_gap'=>array(16,4,48,'Layout & spacing'),
        'header_padding'=>array(18,4,60,'Header & footer'),'nav_size'=>array(14,12,24,'Header & footer'),'footer_padding'=>array(48,12,120,'Header & footer'),
        'card_radius'=>array(15,0,40,'Cards & buttons'),'card_padding'=>array(24,8,64,'Cards & buttons'),'button_radius'=>array(8,0,40,'Cards & buttons'),'button_padding'=>array(14,8,30,'Cards & buttons'),'border_width'=>array(1,0,5,'Cards & buttons'),
        'table_size'=>array(14,12,22,'Tables'),'table_padding'=>array(14,6,32,'Tables'),
        'mobile_body_size'=>array(15,12,22,'Responsive'),'mobile_heading_size'=>array(25,18,40,'Responsive'),'mobile_gutter'=>array(12,8,32,'Responsive'),'mobile_table_size'=>array(12,10,20,'Responsive'),
    ) as $name=>$v ) { $schema[$name] = array('type'=>'number','default'=>$v[0],'min'=>$v[1],'max'=>$v[2],'tab'=>$v[3]); }
    foreach ( array('body_font','heading_font') as $name ) { $schema[$name] = array('type'=>'select','tab'=>'Typography','default'=>'system','choices'=>array('system'=>'System + local Devanagari','serif'=>'Serif + local Devanagari','poppins'=>'Poppins (enable Google Fonts in General)','oswald'=>'Oswald (enable Google Fonts in General)')); }
    $schema['card_shadow'] = array('type'=>'select','tab'=>'Cards & buttons','default'=>'soft','choices'=>array('none'=>'None','soft'=>'Soft','strong'=>'Strong'));
    $schema['table_source'] = array('type'=>'select','tab'=>'Tables','default'=>'plugin','choices'=>array('theme'=>'Use Theme Styles for results tables','plugin'=>'Use Results Manager → Settings styles'));
    return $schema;
}
function s20_style_value( string $name, $value ) {
    $s = s20_style_schema()[$name] ?? null;
    if ( ! $s || ! is_scalar( $value ) ) { return $s['default'] ?? ''; }
    if ( 'color_picker' === $s['type'] ) { return sanitize_hex_color( $value ) ?: $s['default']; }
    if ( 'number' === $s['type'] ) { return is_numeric($value) ? max($s['min'], min($s['max'], (float)$value)) : $s['default']; }
    return isset($s['choices'][$value]) ? $value : $s['default'];
}
add_action( 'acf/init', function () {
    if ( ! function_exists('acf_add_local_field_group') ) { return; }
    $fields=array(); $schema=s20_style_schema();
    foreach ( array('Palette','Typography','Layout & spacing','Header & footer','Cards & buttons','Tables','Responsive') as $tab ) {
        $fields[]=s20_acf_tab('styles',$tab);
        foreach($schema as $name=>$s){
            if($s['tab']!==$tab)continue;
            $args=array('default_value'=>$s['default'],'wrapper'=>array('width'=>'50'));
            if(isset($s['min']))$args+=array('min'=>$s['min'],'max'=>$s['max'],'step'=>'line_height'===$name?0.1:1,'append'=>'line_height'===$name?'':'px');
            if(isset($s['choices']))$args['choices']=$s['choices'];
            $fields[]=s20_acf_f('styles','style_'.$name,ucwords(str_replace('_',' ',$name)),$s['type'],$args);
        }
    }
    array_unshift($fields,s20_acf_message('styles','help','These controls apply immediately after saving. Choose accessible foreground/background pairs. Theme table colors override plugin table colors only when Table Source is set to Theme. No arbitrary CSS is accepted.'));
    acf_add_local_field_group(array('key'=>'group_s20_styles','title'=>'Theme Styles','fields'=>$fields,'location'=>s20_acf_options_location('s20-styles')));
});
foreach(s20_style_schema() as $name=>$spec){
    add_filter('acf/update_value/name=style_'.$name,static function($value)use($name){return s20_style_value($name,$value);});
}
function s20_style_css(): string {
    $css=':root{'; $values=array();
    $fonts=array('system'=>'"GBK Devanagari",system-ui,sans-serif','serif'=>'Georgia,"GBK Devanagari",serif','poppins'=>'Poppins,"GBK Devanagari",sans-serif','oswald'=>'Oswald,"GBK Devanagari",sans-serif');
    foreach(s20_style_schema() as $name=>$s){
        $v=s20_style_value($name,s20_get_option('style_'.$name,$s['default']));$values[$name]=$v;
        if('table_source'===$name)continue;
        if(str_ends_with($name,'_font'))$v=$fonts[$v];
        elseif('card_shadow'===$name)$v=array('none'=>'none','soft'=>'0 4px 18px #00000020','strong'=>'0 10px 32px #00000060')[$v];
        elseif('number'===$s['type']&&'line_height'!==$name)$v.='px';
        $css.='--gbk-style-'.str_replace('_','-',$name).':'.$v.';';
    }
    $css.='--s20-black:var(--gbk-style-background);--s20-pure-black:var(--gbk-style-background);--s20-card:var(--gbk-style-card-bg);--s20-white:#ffffff;--s20-cream:var(--gbk-style-cream);--s20-gold:var(--gbk-style-accent);--s20-amber:var(--gbk-style-accent-hover);--s20-border-soft:var(--gbk-style-border);--s20-font-body:var(--gbk-style-body-font);--s20-font-heading:var(--gbk-style-heading-font);--s20-font-accent:var(--gbk-style-body-font);--s20-radius:var(--gbk-style-card-radius);--s20-btn-radius:var(--gbk-style-button-radius);}';
    if('theme'===$values['table_source']){
        $css.='.gbk-table-wrap,.gbk-table,.srm-wrap,.srm-table{--srm-table-bg:var(--gbk-style-table-bg);--srm-text-color:var(--gbk-style-table-text);--srm-header-bg:var(--gbk-style-table-header-bg);--srm-header-color:var(--gbk-style-table-header-text);--srm-alt-row:var(--gbk-style-table-alt);--srm-border-color:var(--gbk-style-table-border);--srm-cell-padding:var(--gbk-style-table-padding);--srm-font-size:var(--gbk-style-table-size);--srm-header-font-size:var(--gbk-style-table-size);--srm-mobile-font-size:var(--gbk-style-mobile-table-size)}.gbk-table a{color:var(--gbk-style-table-link)}';
    }
    return $css;
}
add_action('wp_enqueue_scripts',function(){
    wp_enqueue_style('gbk-theme-styles',S20_URI.'/assets/css/theme-styles.css',array('s20-responsive'),S20_VERSION);
    wp_enqueue_style('gbk-editorial',S20_URI.'/assets/css/editorial.css',array('gbk-theme-styles'),S20_VERSION);
    wp_add_inline_style('gbk-theme-styles',s20_style_css());
},30);
