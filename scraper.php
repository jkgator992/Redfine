<?
require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';
//Please PUT URL HERE 
$Links	=	array('https://www.redfin.com/county/521/GA/Butts-County/filter/include=sold-1wk');
$cHeadres = array(
      'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language: en-US,en;q=0.5',
      'Connection: Keep-Alive',
      'Pragma: no-cache',
      'Cache-Control: no-cache'
     );
     function dlPage($link) {
        global $cHeadres;
        $ch = curl_init();
        if($ch){
         curl_setopt($ch, CURLOPT_URL, $link);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeadres);
         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
         curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($ch, CURLOPT_HEADER, false);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
         curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
         $str = curl_exec($ch);
         curl_close($ch);
         $dom = new simple_html_dom();
         $dom->load($str);
         return $dom;
        }
       }
	   
	   
 
 
for ($mainpage = 0; $mainpage < sizeof($Links); $mainpage++)
{
	$Mainpage	=	$Links[$mainpage];
	
	$html	=	dlPage($Mainpage);
	
	sleep(10);
	if($html)
	{
		$Checkpage	=	$html->find("//[@id='sidepane-header']/div[2]/div/div[1]",0);
		$totalpages = 	str_replace("20 of" ,"",$Checkpage);
		$num 		=	preg_replace("/[^0-9\.]/", '', $totalpages);
		$bindas		= ceil($pagination	=	$num/20);
		echo "Main Page => $Mainpage Total Records = $num \n";
		for ($i = 0; $i <= $bindas; $i++)
		{
			$innerlink	=	$Mainpage.'/page-'.$i;
			$pages		=	dlpage($innerlink);
			sleep(10);
			
			if($pages)
			{
			for($j = 0; $j <= $num; $j++) 
				{				
					$sold 			=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/div[@class='topleft']",0)->plaintext;
					$fulladdress	=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/a[2]/div[1]/div[2]",0)->plaintext;
					//Street
					$streetline		=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/a[2]/div[1]/div[2]/span[@data-rf-test-id='abp-streetLine']",0)->plaintext;
					
					$CityStateZip	=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/a[2]/div[1]/div[2]/span[@class='cityStateZip']",0)->plaintext;
					$coma 			= 	explode(", ", $CityStateZip);
  					
					//This is for City
					$city			=	$coma [0];
					
					//This is for State.
					$space			=	$coma[1];
					$spacetwo		=	explode(" ", $space);
					$state			=	$spacetwo[0];
					
					//This is for postal code
					$postalcode		=	 $spacetwo[1];
					 
					
					
				
				
				
					$profileurl		=	$pages->find("//*[@id='MapHomeCard_$j']//div/a[@class='ViewDetailsButtonWrapper']",0)->href;
					$price			=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/a[2]/div[1]/div[1]/span[2]",0)->plaintext;
					$listingurl		=	'https://www.redfin.com'.$profileurl;
							
					if($price != '' || $price != null)
					{
						$record = array( 'listingurl' =>$listingurl, 
						'price' 		=> $price,
						'fulladdress'	=> $fulladdress, 
						'streetline' 	=> $streetline, 
						'city' 			=> $city, 
						'state' 		=> $state, 
						'postalcode' 	=> $postalcode, 
						'sold' => $sold,
						'mainpage' => $innerlink);
						scraperwiki::save(array('listingurl','price','fulladdress','streetline','city','state','postalcode','sold','mainpage'), $record);
					}
					
					
					
				}
				
			}
		}
		
	}
  
}
?>
