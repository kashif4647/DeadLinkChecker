<?php
/**
 * 
 */
class DeadLinkChecker
{
	public function __construct()
	{
		register_activation_hook( WH_PLUGIN_DIR_URL, array( $this, 'createBrokenLinksTable') );
		add_filter( 'the_content', array( $this, 'filterPostContent' ) );
		add_action( 'admin_menu', array( $this, 'WHPPage' ) );
	}

	function filterPostContent( $content ) {
		if ( ! is_admin() ) {
			$main_content   = $content;
			$catch_dead_link= array();
	
			preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $content, $result);
	
			if(!empty($result)){
				foreach ($result['href'] as $key => $value) {
					$ylink      = $this->removeHtmlTags($value);
	
					if($ylink !='' && $ylink != '"'){
                
						if (strpos($ylink, 'youtube.com') !== false || strpos($ylink, 'youtu.be')!== false) {
							if (strpos($ylink, 'youtube.com/watch?v') !== false) {
								preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $ylink, $uid);
	 
								$tubeid     = $uid[1];
							}else if (strpos($ylink, 'youtu.be') !== false) {
								preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $ylink, $uid);
								
								$tubeid     = $uid[1];
							}else{
								$reslt      = explode('https://www.youtube.com/', $ylink);
								$tubeid     = $reslt[1];
								$link       = $content;
							}
	
								$res       	= $this->checkYouTubeLink($tubeid);
	
							if( empty($res) ){
								$catch_dead_link[$key] = $ylink;
							}else{
								$content = $content;
							}
						}
					}
				}
	
				if(!empty($catch_dead_link)){
					foreach ($catch_dead_link as $ind => $link) {
	
						preg_match_all('/<a[^>]+href=[\'|"]([^>]+)[^>]+>([^<]+)<\/a>/i', $main_content, $links_catch);
	
						$name_orig  = $this->removeHtmlTags($links_catch[0][$ind]);
						$url_orig   = $links_catch[1][$ind];
	
						$content    = str_replace('<a href="'.$url_orig.'">'.$name_orig.'</a>', "<strong>[dead link]</strong>", $content);
	
						// this is optional in case above line not work
						$content 	= str_replace($link, "#[dead link]", $content);
	
						global $wpdb, $post;
						$table      = $wpdb->prefix.'broken_links';
						$results    = $wpdb->get_results( "SELECT * FROM $table WHERE dead_link = '$link'");
	
						if(empty($results)){
							$wpdb->insert($table, array(
								'post_id' 	=> $post->ID,
								'dead_link' => $link,
							));
						}
					}
				}
	
				return $content;
			}else{
				return $content;
			}
		}
	}

	// Create table on activation
	public function createBrokenLinksTable()
	{
		global $table_prefix, $wpdb;

		$tblname 		= 'broken_links';
		$table_name 	= $table_prefix . "$tblname ";

		#Check to see if the table exists already, if not, then create it
		if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name ) {

			$sql = "CREATE TABLE $table_name (
					ID mediumint(9) NOT NULL AUTO_INCREMENT,
					`post_id` int(11) NOT NULL,
					`dead_link` varchar(128) NOT NULL,
					PRIMARY KEY  (ID)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			add_option('my_db_version', $my_products_db_version);
		}
	}

	function checkYouTubeLink($videoId) {
		$videoId 	= self::removeHtmlTags($videoId);
	    $url 		= "https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$videoId&format=json";
	    // return $url;
        $curl 		= curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $body 		= curl_exec($curl);
        curl_close($curl);

        return json_decode($body, true);
	}

	function removeHtmlTags($string) {
	    // ----- remove HTML TAGs ----- 
	    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
	    // ----- remove control characters ----- 
	    $string = str_replace("\r", '', $string);
	    $string = str_replace("\n", ' ', $string);
	    $string = str_replace("\t", ' ', $string);
	    // ----- remove multiple spaces ----- 
	    $string = trim(preg_replace('/ {2,}/', ' ', $string));
	    return $string; 
	}

	/***********************************************/
	/************Registering Plugin Page***********/
	function WHPPage()
	{
		add_menu_page(
			'Broken Links',     // page title
			'Broken Links',     // menu title
			'manage_options',   // capability
			'include-text',     // menu slug
			array($this, 'WHPageContent')     // callback function
		);
	}

	function WHPageContent()
	{
		global $title;

		echo '<div class="wrap">';
		echo "<h1>$title</h1>";

		echo "<p class='description'>Broken links here!</p>";

		global $wpdb, $post;
		$table 		= $wpdb->prefix.'broken_links';
		$results 	= $wpdb->get_results( "SELECT * FROM $table"); // Query to fetch data from database table and storing in $results
		if(!empty($results)){
			require_once( WH_PLUGIN_DIR . 'templates/settings.php' );
			echo createTemplate($results);
		}
		echo '</div>';
	}
}
new DeadLinkChecker();