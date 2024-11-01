<?php
/*
Plugin Name: SL Map
Plugin URI: http://signpostmarv.name/sl-map/
Description: Embed Second Life Maps in your blog posts!
Version: 0.1.1
Author: SignpostMarv Martin
Author URI: http://signpostmarv.name/
 Copyright 2010 SignpostMarv Martin  (email : sl-map.wp@signpostmarv.name)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class Marvulous_SL_Map
{
	public function plugins_loaded()
	{
		wp_register_script('Marvulous_SL_Map',$this->plugin_file('js'),array('jquery'));
		wp_register_style('Marvulous_SL_Map',$this->plugin_file('css'));
		add_action('wp_print_scripts',array($this,'print_scripts'));
		add_action('wp_print_styles',array($this,'print_styles'));
		add_action('wp_head',array($this,'js'));
		add_shortcode('sl-map',array($this,'shortcode'));
	}
	public function plugin_file($file)
	{
		return untrailingslashit(trailingslashit(trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) )) . 'marvulous.sl-map.wp.' . $file);
	}
	public function print_scripts()
	{
		wp_enqueue_script('Marvulous_SL_Map');
	}
	public function print_styles()
	{
		wp_enqueue_style('Marvulous_SL_Map');
	}
	public function shortcode($atts,$content='')
	{
		extract(shortcode_atts(array(
			'source' => 'http://agni.sl.mapapi.net/',
			'region'=>'',
			'x'=>'128',
			'y'=>'128',
			'z'=>'0',
		),$atts));
		$api_sources = apply_filters('Marvulous_SL_Map::api_sources',array());
		$url = $source . rawurlencode($region) . '/' . esc_attr($x) . '/' . esc_attr($y) . '/' . esc_attr($z);
		if(trim($content) == '')
		{
			$content = $source . $region . '/' . '/' . esc_attr($x) . '/' . esc_attr($y) . '/' . esc_attr($z);
		}
		if(isset($api_sources[$source]) == false)
		{
			return;
		}
		else
		{
			return '<a href="' . $url . '">' . $content . '</a>';
		}
	}
	public function add_default_api_sources($value)
	{
		$value['http://slurl.com/secondlife/']     = 'http://agni.sl.mapapi.net/api/name2coords/_regionname_';
		$value['http://www.slurl.com/secondlife/'] = 'http://agni.sl.mapapi.net/api/name2coords/_regionname_';
		$value['http://agni.sl.mapapi.net/']       = 'http://agni.sl.mapapi.net/api/name2coords/_regionname_';
		$value['http://agni.sl.mapapi.net/map/']   = 'http://agni.sl.mapapi.net/api/name2coords/_regionname_';
		return $value;
	}
	public function add_default_region_regex($value)
	{
		$value['http://slurl.com/secondlife/']     = '^http://slurl.com/secondlife/(.+)';
		$value['http://www.slurl.com/secondlife/'] = '^http://www.slurl.com/secondlife/(.+)';
		$value['http://agni.sl.mapapi.net/']       = 'http://agni.sl.mapapi.net/(.+)';
		$value['http://agni.sl.mapapi.net/map/']   = 'http://agni.sl.mapapi.net/(.+)';
		return $value;
	}
	public function js()
	{
		$api_sources  = json_encode(apply_filters('Marvulous_SL_Map::api_sources',array()));
		$region_regex = json_encode(apply_filters('Marvulous_SL_Map::region_regex',array()));
		echo <<<EOT
<script type="text/javascript">
jQuery(document).ready(function(){
marvulous.sl_map.wp.api_sources  = $api_sources;
marvulous.sl_map.wp.region_regex = $region_regex;
marvulous.sl_map.wp.init();
});
</script>
EOT;
	}
}
$Marvulous_SL_Map = new Marvulous_SL_Map;
add_action('plugins_loaded',array($Marvulous_SL_Map,'plugins_loaded'));
add_filter('Marvulous_SL_Map::api_sources',array($Marvulous_SL_Map,'add_default_api_sources'));
add_filter('Marvulous_SL_Map::region_regex',array($Marvulous_SL_Map,'add_default_region_regex'));