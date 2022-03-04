<?php
define( 'PRIME_RATES_NO_OFFERS_PAGE_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'pr-no-offers'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_NO_OFFERS_PAGE_CSS_URL', PRIME_RATES_NO_OFFERS_PAGE_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_NO_OFFERS_PAGE_JS_URL', PRIME_RATES_NO_OFFERS_PAGE_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function pre_qualified_no_offers_shortcode(){
	?>
	<script type="text/javascript">
		window.FirstName = '<?php echo $_COOKIE["FirstName"]; ?>';
		window.LastName = '<?php echo $_COOKIE["LastName"]; ?>';
		window.Email = '<?php echo $_COOKIE["Email"]; ?>';
		window.Phone = '<?php echo $_COOKIE["Phone"]; ?>';
		//window.Phone = '8586105327';
		window.IPAddress = '<?php echo $_COOKIE["IPAddress"]; ?>';
		window.AnnualIncome = '<?php echo $_COOKIE["AnnualIncome"]; ?>';
		window.LoanAmount = '<?php echo $_COOKIE["LoanAmount"]; ?>';
		window.State = '<?php echo $_COOKIE["State"]; ?>';
		window.Housing = '<?php echo $_COOKIE["Housing"]; ?>';
		window.CreditScore = '<?php echo $_COOKIE["CreditScore"]; ?>';
		window.AgreeTCPA = '<?php echo $_COOKIE["AgreeTCPA"]; ?>';
		window.TotalDebt = '<?php echo $_COOKIE["TotalDebt"]; ?>';
		window.parent.postMessage('suppressionIndicator:' + '<?php echo $_COOKIE["suppressionIndicator"]; ?>', '*');
		//window.TotalDebt = '12500';
		window.AccreditedStatus = '<?php echo $_COOKIE["AccreditedStatus"]; ?>';
		window.FreedomStatus = '<?php echo $_COOKIE["FreedomStatus"]; ?>';
		window.lenderResponseRaw = '<?php echo $_COOKIE["LenderResponse"]; ?>';
		window.lenderResponse = JSON.parse(window.lenderResponseRaw);
	</script>
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/AlternateOffer/alternateOffers-primerates.css" rel="stylesheet">
	<div class="main-cont" id="root"></div>
	<?php	 
}

function prime_rates_no_offers_page_enqueue() {
	global $post;
	if(has_shortcode( $post->post_content, 'pre-qualified-no-offers') && (isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 0)){
		$action = 'mandrill_incomplete';
	  wp_enqueue_script('prime_rates_loan_applications', PRIME_RATES_NO_OFFERS_PAGE_JS_URL.'main.61c919a0.js', array(), 0,false );  
	} 
}

function prime_rates_no_offers_page_shortcodes_init(){
	add_shortcode('pre-qualified-no-offers', 'pre_qualified_no_offers_shortcode');
}

function update_offer_count(){
	global $configData;
	$offerid = $_POST['offerid'];
	if($offerid == '1' || $offerid == 1){
		// Setting Timezone to Eastern Timezone
		date_default_timezone_set('America/New_York');
		$accredited_curl = new Curl();
		$accredited_url = 'https://www.postsynchron.com/af/-/xln_cp_host010?SK_01=hVzSd&dpc_affid=2112';
		$accredited_data = array();
		$accredited_data['EMAIL_01'] = urlencode($_COOKIE["Email"]);
		$accredited_data['FIRSTNAME_01'] = $_COOKIE["FirstName"];
		$accredited_data['LASTNAME_01'] = $_COOKIE["LastName"];
		if($_SERVER["HTTP_HOST"] == "dev.primerates.com" || $_SERVER["HTTP_HOST"] == "qa.primerates.com"){
			$accredited_data['PHONE_DAY_FULL_01'] = '8586105327';
		}else{
			$accredited_data['PHONE_DAY_FULL_01'] = $_COOKIE["Phone"];;
		}
		$accredited_data['STATE_01'] = $_POST["state"];
		if($_SERVER["HTTP_HOST"] == "dev.primerates.com" || $_SERVER["HTTP_HOST"] == "qa.primerates.com"){
			$accredited_data['TOTAL_DEBT_01'] = '12500';
		}else{
			$accredited_data['TOTAL_DEBT_01'] = '19999';
		}
		$accredited_data['USER_IP_ADDRESS'] = $_COOKIE["IPAddress"];
		$accredited_data['s1'] = $_POST['s1'];
		
		$encode_string = 'EMAIL_01='.urlencode($_COOKIE["Email"]).'&FIRSTNAME_01='.urlencode($_COOKIE["FirstName"]).'&LASTNAME_01='.urlencode($_COOKIE["LastName"]).'&PHONE_DAY_FULL_01='.urlencode($accredited_data['PHONE_DAY_FULL_01']).'&STATE_01='.urlencode($_POST["state"]).'&TOTAL_DEBT_01='.urlencode($accredited_data['TOTAL_DEBT_01']).'&USER_IP_ADDRESS='.urlencode($_COOKIE["IPAddress"]).'&s1='.urlencode($_POST['s1']);
		
		$result = $accredited_curl->callApi($accredited_url, 'POST','application/x-www-form-urlencoded',$encode_string,false);
		
		$response = json_encode($result);
		
		if (strpos($response, 'accepted') !== false) {
			// Fetching and checking the Accredited Debt Relief count and time
			$accredited_count = get_field('accredited_debt_relief_count','option');
			$accredited_last_call = get_field('accredited_debt_relief_last_call_date','option');
			
			// Check for the capacity limit for Accredited and update according to logic
			if($accredited_last_call == ""){
				$accredited_last_call = "now";
			}
			$accredited_last_call_month = date('Y-m', strtotime($accredited_last_call));
			$accredited_current_month = date('Y-m', strtotime("now"));
			
			if($accredited_last_call_month != $accredited_current_month){
				update_field('accredited_debt_relief_count',1,'option');
				update_field('accredited_debt_relief_last_call_date',$accredited_current_month,'option');
			}else{
				update_field('accredited_debt_relief_count',$accredited_count+1,'option');
				update_field('accredited_debt_relief_last_call_date',$accredited_current_month,'option');
			}
			echo 'success';
		}else{
			echo 'fail';
		}
		exit;
	}
	if($offerid == '18' || $offerid == 18){
		// Setting Timezone to Pacific Timezone
		date_default_timezone_set('America/Los_Angeles');
		// Fetching and checking the Freedom Debt Relief count and time
		$freedom_count = get_field('freedom_debt_relief_count','option');
		$freedom_last_call = get_field('freedom_debt_relief_last_call_date','option');
		
		// Check for the capacity limit for Freedom and update according to logic
		if($freedom_last_call == ""){
			$freedom_last_call = "now";
		}
		$freedom_last_call_day = date('Y-m-d', strtotime($freedom_last_call));
		$freedom_current_day = date('Y-m-d', strtotime("now"));
		
		if($freedom_last_call_day != $freedom_current_day){
			update_field('freedom_debt_relief_count',1,'option');
			update_field('freedom_debt_relief_last_call_date',$freedom_current_day,'option');
		}else{
			update_field('freedom_debt_relief_count',$freedom_count+1,'option');
			update_field('freedom_debt_relief_last_call_date',$freedom_current_day,'option');
		}
	}	
}

function add_clicks_net_tab()
{
    // Not our page, do nothing
    if( !is_page( 'no-offers' ) || @$_GET['AFID'] == '430380' || @$_GET['AFID'] == '434151' || strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false  || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"codepen.io") !== false)
        return;

    ?>
	<script type="application/javascript">
	  function getIP(json) {
		//document.write("My public IP address is: ", json.ip);
		window.clientIP = json.ip;
	  }
	</script>
	<script type="application/javascript" src="https://api.ipify.org?format=jsonp&callback=getIP"></script>
	<script type="text/javascript" src="https://cdn.fcmrktplace.com/scripts/clicksnet.js"></script>
	<script type="text/javascript">
		var affcid = "1103268";
		var key = "fslQqPqaEy01";
		var zip = clicksNetGetQueryStringParam('zip');

		//	Subids are used to track conversions and traffic
		var subid1 = '';
		//	Subids are used to track conversions and traffic
		var subid2 = '';

		//Optional preview parameter
		var creative_id = clicksNetGetQueryStringParam('preview');

		//Optional Query Parameters:
		//	showHeader=[true||false] -> will show or hide header (Default: true)
		//	showFooter=[true||false] -> will show or hide footer (Default: true)
		var showHeader = false;
		var showFooter = false;
		var ip = window.clientIP; //client's IP here

		function getCookie(cname) {
		  var name = cname + "=";
		  var decodedCookie = decodeURIComponent(document.cookie);
		  var ca = decodedCookie.split(';');
		  for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
			  c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
			  return c.substring(name.length, c.length);
			}
		  }
		  return "";
		}

		function goNewWin() {
			//alert("Testing");
			document.cookie = "nw=1; path=/"; //set the cookie so we don't open the tabs multiple times
			document.write("<script type='text/javascript' src='" + clicksNetGetProtocol() + "cdn.fcmrktplace.com/listing/?affcamid=" + affcid + "&zip=" + zip + "&ip=" + ip + "&key=" + key + "&creative_id=" + creative_id + "&subid1=" + subid1 + "&subid2=" + subid2 + "'><" + "/script>");
			var newWin = window.open(location.href, '_blank');
			this.location.href = "<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/sponsored-offers'; ?>";
		}
	</script>
	
	
    <script type="text/javascript">
        document.getElementsByTagName('body')[0].onload = function() { if (getCookie('nw') != '1') goNewWin(); };        
    </script>
    <?php           
};

add_action('init', 'prime_rates_no_offers_page_shortcodes_init');
add_action( 'wp_ajax_update_offer_count', 'update_offer_count' );
add_action( 'wp_ajax_nopriv_update_offer_count', 'update_offer_count' );
add_action( 'wp_enqueue_scripts', 'prime_rates_no_offers_page_enqueue' );
//add_action( 'wp_footer', 'add_clicks_net_tab' );