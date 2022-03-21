<?php


add_action('wp_footer', function() {
    $abn_posts_select_args = [
        'post_type' => 'abn_banners',
        'orderby' => 'date ',
        'post_status' => 'publish',
        'order' => 'DESC',
        'posts_per_page' => -1,
    ];

    $abn_posts_select_query = new WP_Query($abn_posts_select_args);

    if(count($abn_posts_select_query->posts) >= 1 ) {

        DisplayOnPages(json_encode($abn_posts_select_query->posts));

        DisplayOnPosts(json_encode($abn_posts_select_query->posts));

    }

}, 10);

function DisplayOnPages($banners) {
    $banners = json_decode($banners);

    foreach($banners as $banner) {
        $banner_meta = get_post_meta($banner->ID);

        $page_ids = explode(",", $banner_meta['banners_options_pid_pages'][0]);
        if(count($page_ids) >= 1) {
            if ((get_post_type() === 'page') && (in_array(get_the_ID(), $page_ids))) {

                if($banner_meta['advanced_options_is-popup'][0] == 'popup') {
                    echo DisplayPopUp($banner, $banner_meta);
                } else {
                    echo DisplayUsualBanner($banner, $banner_meta);
                }

            }
        }
    }
}

function DisplayOnPosts($banners) {
    $banners = json_decode($banners);

    foreach($banners as $banner) {
        $banner_meta = get_post_meta($banner->ID);
        $exclude_posts = explode(",", $banner_meta['banners_options_exclude_posts'][0]);

        if($banner_meta['banners_options_display-banner-logic'][0] == "choose") {

            $ids = explode(",", $banner_meta['banners_options_pids'][0]);

            if(count($ids) >= 1) {
                if ((get_post_type() === 'post') && (in_array(get_the_ID(), $ids)) && (!in_array(get_queried_object_id(), $exclude_posts))) {

                    if($banner_meta['advanced_options_is-popup'][0] == 'popup') {
                        echo DisplayPopUp($banner, $banner_meta);
                    } else {
                        echo DisplayUsualBanner($banner, $banner_meta);
                    }

                }
            }

        } else {

            if ((get_post_type() === 'post') && (!in_array(get_queried_object_id(), $exclude_posts))) {

                if($banner_meta['advanced_options_is-popup'][0] == 'popup') {
                    echo DisplayPopUp($banner, $banner_meta);
                } else {
                    echo DisplayUsualBanner($banner, $banner_meta);
                }

            }
        }


    }

}

function DisplayPopUp($banner, $banner_meta) {
    $css = isset($banner_meta['banners_options_bcss'][0]) ? $banner_meta['banners_options_bcss'][0] : "";

    if($banner_meta['advanced_options_is-popup'][0] == 'popup') {
        $delay = $banner_meta['banners_options_popup-display-secs'][0];
        $hide_days = $banner_meta['banners_options_popup-hide-days'][0];
        $link_target = isset($banner_meta['advanced_options_link-target'][0]) ? $banner_meta['advanced_options_link-target'][0] : "";

        return "<div class=\"banner_modal\" style=\"display:none;".$css."\"><span class=\"close\"><span class=\"line\"></span><span class=\"line\"></span></span><a target=\"".$link_target."\" href=\"" .$banner_meta['banners_options_banner-link'][0]. "\"><img src=\"".get_the_post_thumbnail_url($banner->ID)."\"/></a></div>
		<script>
		function loadjQuery(url, success){
		 var script = document.createElement('script');
		 script.src = url;
		 var head = document.getElementsByTagName('head')[0],
		 done = false;
		 head.appendChild(script);
		 // Attach handlers for all browsers
	script.onload = script.onreadystatechange = function() {
		if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
			 done = true;
			 success();
			 script.onload = script.onreadystatechange = null;
			 head.removeChild(script);        
		}
	};
	}
	 if (typeof jQuery == 'undefined'){

	loadjQuery('http://code.jquery.com/jquery-1.10.2.min.js', function() {
			// Write your jQuery Code
		   });
	 } else { 
		jQuery(document).ready( function($) {		
		
		if(getCookie('abn_banners_popup') != ".$banner->ID.") {
			showpanel();
		}					
		
		function showpanel() {	
			setTimeout(function() {
				$('.banner_modal').show();
			}, ".$delay."000);
		}
		
		function setCookie(cname, cvalue, exdays) {
		  const d = new Date();
		  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		  let expires = \"expires=\"+d.toUTCString();
		  document.cookie = cname + \"=\" + cvalue + \";\" + expires + \";path=/\";
		}

		function getCookie(cname) {
		  let name = cname + '=';
		  let ca = document.cookie.split(';');
		  for(let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
			  c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
			  return c.substring(name.length, c.length);
			}
		  }
		  return \"\";
		}


		$('.banner_modal .close').click(function() {
			$('.banner_modal').hide();
			setCookie('abn_banners_popup', ".$banner->ID.", ".$hide_days.");
		});
		
		});
	 }
		</script>";
    }
}

function DisplayUsualBanner($banner, $banner_meta) {
    $css = isset($banner_meta['banners_options_bcss'][0]) ? $banner_meta['banners_options_bcss'][0] : "";
    $link_target = isset($banner_meta['advanced_options_link-target'][0]) ? $banner_meta['advanced_options_link-target'][0] : "";

    if(mb_strlen($banner_meta['banners_options_adv-selector-id'][0]) >= 1) {
        return '<script>
		function loadjQuery(url, success){
			 var script = document.createElement(\'script\');
			 script.src = url;
			 var head = document.getElementsByTagName(\'head\')[0],
			 done = false;
			 head.appendChild(script);
			 // Attach handlers for all browsers
		script.onload = script.onreadystatechange = function() {
			if (!done && (!this.readyState || this.readyState == \'loaded\' || this.readyState == \'complete\')) {
				 done = true;
				 success();
				 script.onload = script.onreadystatechange = null;
				 head.removeChild(script);        
			}
		};
		}
		 if (typeof jQuery == \'undefined\'){

		loadjQuery(\'http://code.jquery.com/jquery-1.10.2.min.js\', function() {
			 
			   });
		 } else { 
			jQuery(document).ready( function($) {
				var banner = "<div style=\'' . $css . '\' class=\'banner\'><a target=\"'.$link_target.'\" href=\'' . $banner_meta['banners_options_banner-link'][0] . '\'><img src=\'' . get_the_post_thumbnail_url($banner->ID) . '\'/></a></div>";
				
				$(banner).insertAfter("' . $banner_meta['banners_options_adv-selector-id'][0] . '");
				});
			}
				</script>';
    } else {
        return '<script>
				function loadjQuery(url, success){
					 var script = document.createElement(\'script\');
					 script.src = url;
					 var head = document.getElementsByTagName(\'head\')[0],
					 done = false;
					 head.appendChild(script);
					 // Attach handlers for all browsers
				script.onload = script.onreadystatechange = function() {
					if (!done && (!this.readyState || this.readyState == \'loaded\' || this.readyState == \'complete\')) {
						 done = true;
						 success();
						 script.onload = script.onreadystatechange = null;
						 head.removeChild(script);        
					}
				};
				}
				 if (typeof jQuery == \'undefined\'){

				loadjQuery(\'http://code.jquery.com/jquery-1.10.2.min.js\', function() {
			 
			   });
		 } else { 
			jQuery(document).ready( function($) {
			var elems = document.getElementsByTagName("p");

			for (var i = 0;i < elems.length; i++){
					var ind = '.$banner_meta['banners_options_add-banner-after-n-paragraph'][0].';
					var banner = "<div style=\''.$css.'\' class=\'banner\'><a target=\"'.$link_target.'\" href=\''. $banner_meta['banners_options_banner-link'][0].'\'><img src=\''. get_the_post_thumbnail_url($banner->ID).'\'/></a></div>";
				if(i == ind) {
					$(banner).insertAfter(elems[ind]);
				}
			}
			});
		 }
	</script>';
    }

}