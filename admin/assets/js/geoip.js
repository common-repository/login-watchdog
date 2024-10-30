/**
 * Watchdog JavaScript
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
jQuery(document).ready(function($) {
	
	/**
	 * For every element (img) with class iploader makes ajax call to geoip api
	 */
	$(".iploader").each(function() {
		
		//get ip address from data attribute
		var ip = $(this).data("ip");
		
		//get the closest elements with classes country-name and region-name
		var columnCountry = $(this).closest(".country-name");
		var columnRegion = $(this).parent().siblings(".region-name");
		
		/**
		 * Ajax call to geo ip api
		 * 
		 * On success show result and save geo information to database
		 */
		$.ajax({
			url: "https://freegeoip.net/json/"+ip,
			type: "GET",
			success: function(data) {
				showResult(data, false, columnCountry, columnRegion);
				saveResult(data, ip);				 
			},
			error: function() {
				showResult(null, true, columnCountry, columnRegion);
			}	
		});
	});
	
	/**
	 * Call ajax to save data to Watchdog database
	 * 
	 * @access public
	 * @param data JSON from geo ip api
	 * @param ip IP address of record
	 */
	function saveResult(data, ip){
		$.ajax({
			url: params.ajaxUrl,
			type: "POST",
			data: { 
				action: 'save_geo_ip', 
				data: data 
			}
		});
	}
		
	/**
	 * Show result of geo ip api
	 * @access public
	 * @param data JSON from geo ip api
	 * @param error True on failure, false on success)
	 * @param columnCountry Element
	 * @param columnRegion Element
	 */
	function showResult(data, error, columnCountry, columnRegion){
		//if is not error show result
		if(error === false){
			columnCountry.html(
					"<a href='//www.openstreetmap.org/#map=15/"+data.latitude+"/"+data.longitude+"'"+ 
					   "target='_blank' "+" title='Map'>"+
						data.country_name+
					"</a>"
			);
			columnRegion.html(
				"<a href='//www.openstreetmap.org/#map=15/"+data.latitude+"/"+data.longitude+"'"+ 
				   "target='_blank' "+" title='Map'>"+
					data.region_name+
				"</a>"
			);	
		} else {
			columnCountry.html("---");
			columnRegion.html("---");
		}
	}
});
