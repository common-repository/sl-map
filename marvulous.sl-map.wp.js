if(marvulous === undefined)
{
	var marvulous = {};
}
if(marvulous.sl_map == undefined)
{
	marvulous.sl_map = {};
}
marvulous.sl_map.wp = {
	api_sources  : {},
	region_regex : {},
	init : function(){
		for(var source in marvulous.sl_map.wp.api_sources)
		{
			var links = jQuery('a[href^=\'' + source + '\']');
			if(links.length > 0 && marvulous.sl_map.wp.region_regex[source] != undefined)
			{
				var regex = new RegExp(marvulous.sl_map.wp.region_regex[source]);
				links.each(function(){
					var matches = regex.exec(jQuery(this).attr('href'));
					if(matches.length == 2)
					{
						var region = matches[1];
						if(region.indexOf('\/') > -1)
						{
							region = region.substr(0,region.indexOf('\/'));
						}
						var anchor = this;
						jQuery.ajax({
							url : marvulous.sl_map.wp.api_sources[source].replace(/_regionname_/,region),
							cache : false,
							dataType : 'json',
							success: function(d){
								jQuery(anchor).addClass('sl-map');
								jQuery(anchor).append('<img class="sl-map" src="http://map.secondlife.com.s3.amazonaws.com/map-1-' + d.x + '-' + d.y + '-objects.jpg" />');
							}
						});
					}
				});
			}
		}
	}
}