<?php

if(defined('GOOGLEMAPS')) {
	/* google map shortcode
			*** Using [googlemap id="somemapid" coordinates="1 ,1" zoom="17" height="500px" infobox="<p>Some Infobox Content</p>"]
			*** if need street view, please add 'streetview="true"';
			*** if you need satellite view in 45 angle add 'tilt="45"';
	*/
	function google_map_js($atts, $content) {
		static $instance = 0;
		extract(shortcode_atts(array(
			'id'               => 'map_canvas',
			'coordinates'      => '1, 1',
			'zoom'             => 15,
			'height'           => '350px',
			'zoomcontrol'      => 'false',
			'scrollwheel'      => 'false',
			'scalecontrol'     => 'false',
			'disabledefaultui' => 'false',
			'infobox'          => '',
			'satellite'        => '',
			'tilt'             => '',
			'icon'             => theme() . '/images/marker.png',
			'streetview'       => ''
		), $atts));
		$mapid = str_replace('-', '_', $id) . '_' . $instance;

		$map = ! $streetview ? '<div class="googlemap" id="' . $mapid . '" ' . ($height ? 'style="height:' . $height . '"' : '') . '></div><script>
    var ' . $mapid . ';
    function initialize_' . $mapid . '() {
        var myLatlng = new google.maps.LatLng(' . $coordinates . ');
        var mapOptions = {
            ' . ($satellite ? 'mapTypeId: google.maps.MapTypeId.SATELLITE,' : '') . '
            zoom: ' . $zoom . ',
            center: myLatlng,
            zoomControl: ' . $zoomcontrol . ',
            scrollwheel: ' . $scrollwheel . ',
            scaleControl: ' . $scalecontrol . ',
            disableDefaultUI: ' . $disabledefaultui . '
            ' . ($content ? ',styles:' . $content : '') . '
        };
        var ' . $mapid . ' = new google.maps.Map(document.getElementById("' . $mapid . '"), mapOptions);
        ' . ($tilt ? $mapid . '.setTilt(' . $tilt . ');' : '') . '
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: ' . $mapid . ',
            ' . ($icon ? 'icon:"' . $icon . '",' : '') . '
            animation: google.maps.Animation.DROP
        });
        ' . ($infobox ? 'marker.info = new google.maps.InfoWindow({content: \'' . str_replace(array("\r", "\n"), "", $infobox) . '\'});
        google.maps.event.addListener(marker, "click", function() {marker.info.open(' . $mapid . ', marker);});' : '') . '

        google.maps.event.addListener(' . $mapid . ', "center_changed", function() {
            window.setTimeout(function() {
                ' . $mapid . '.panTo(marker.getPosition());
            }, 15000);
        });
    };
    
    $.when( loadGoogleMaps( 3, "' . GOOGLEMAPS . '", "' . wpa_html_lang(false) . '" ) ).then(function() {
      initialize_' . $mapid . '();
    });
    </script>' : do_streetView_map($mapid, $coordinates, $height, $streetview);
		$instance++;

		return $map;
	}

	add_shortcode('googlemap', 'google_map_js');

	function do_streetView_map($id, $pos, $height, $streetview) {
		return '<div class="googlemap" id="street_' . $id . '" ' . ($height ? 'style="height:' . $height . '"' : '') . '></div>
		<script>
        function street_init_' . $id . '() {
        var geocoder =  new google.maps.Geocoder();
        geocoder.geocode( { "address": "' . $streetview . '" }, function(results, status) {
            var lookTo = results[0].geometry.location;
            if (status == google.maps.GeocoderStatus.OK) {
                  var panoOptions = {
                    position: lookTo,
                    panControl: false,
                    addressControl: false,
                    linksControl: false,
                    zoomControlOptions: false
                  };
                  var pano = new  google.maps.StreetViewPanorama(document.getElementById("street_' . $id . '"),panoOptions);
                  var service = new google.maps.StreetViewService;
                  service.getPanoramaByLocation(pano.getPosition(), 50, function(panoData) {
                    if (panoData != null) {
                      var panoCenter = panoData.location.latLng;
                      var heading = google.maps.geometry.spherical.computeHeading(panoCenter, lookTo);
                      var pov = pano.getPov();
                      pov.heading = heading;
                      pano.setPov(pov);
                      var marker = new google.maps.Marker({
                        map: pano,
                        position: lookTo
                      });
                    } else {
                      alert("Not Found");
                    }
                  });
            } else {
                alert("Could not find your address");
            }
        });
      }
      $.when( loadGoogleMaps( 3, "' . GOOGLEMAPS . '", "' . wpa_html_lang(false) . '" ) ).then(function() {
	      street_init_' . $id . '();
	    }); </script>';
	}
} //end GOOGLEMAPS

function content_btn($atts, $content) {
	$attrbts = '';
	foreach($atts as $k => $v) {
		if($k !== 'class') {
			$attrbts .= ' ' . $k . '="' . $v . '"';
		}
	}
	$content = str_replace(array('’', '”'), array("'", '"'), html_entity_decode($content, ENT_HTML5));

	return '<a class="button ' . (isset($atts['class']) ? $atts['class'] : '') . '"' . $attrbts . '>' . $content . '</a>';
}

add_shortcode("button", "content_btn");

function tree_children($absolute = false, $page_id = 0) {
	global $wp_query;
	if($wp_query->is_posts_page) {
		$post = get_post(BLOG_ID);
	} else {
		if($page_id !== 0) {
			$post = get_post($page_id);
		} else {
			global $post;
		}
	}
	$ex_pages = null;
	$ex_args  = array(
		'posts_per_page' => -1,
		'post_type'      => 'page',
		'meta_key'       => 'hide_page',
		'meta_value'     => true
	);
	$excluded = new WP_Query($ex_args);
	if($excluded->have_posts()): while($excluded->have_posts()) : $excluded->the_post();
		$ex_pages .= get_the_ID() . ',';
	endwhile;
		$ex_pages = substr($ex_pages, 0, -1);
	endif;
	wp_reset_query();
	$childlist = get_pages('child_of=' . $post->ID . ($ex_pages ? '&exclude=' . $ex_pages : ''));
	$children  = '';
	if($post->post_parent) {
		$ancestors = get_post_ancestors($post->ID);
		$reverse   = array_reverse($ancestors);
		$abs       = $reverse[0];
		$children  .= '<ul class="submenu">';
		$children  .= wp_list_pages("title_li=&child_of=" . $abs . "&echo=0&sort_column=menu_order" . ($ex_pages ? '&exclude=' . $ex_pages : ''));
		$children  .= '</ul>';
		echo $children;
	} elseif($childlist) {
		echo '<ul class="submenu">' . wp_list_pages("title_li=&child_of=" . $post->ID . "&echo=0&sort_column=menu_order" . ($ex_pages ? '&exclude=' . $ex_pages : '')) . '</ul>';
	}
}

//remove <p> and <br /> from shortcodes
add_filter('the_content', 'shortcode_empty_paragraph_fix');
function shortcode_empty_paragraph_fix($content) {
	$array   = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']'
	);
	$content = strtr($content, $array);

	return $content;
}

function socialist() {
	$soci = get_field('socialist', 'option');
	$soc  = '';
	if($soci) {
		$soc .= '<div class="socialist">';
		foreach($soci as $so) {
			$soc .= '<a class="icon-' . $so['icon'] . '" target="_blank" href="' . $so['link'] . '" rel="nofollow"></a>';
		}
		$soc .= '</div>';
	}

	return $soc;
}

add_shortcode("social", "socialist");