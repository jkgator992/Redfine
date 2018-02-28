<?
require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';
//Please PUT URL HERE 
$Links	=	array('https://www.redfin.com/county/531/GA/Cherokee-County/filter/include=sold-1wk',
'https://www.redfin.com/county/510/GA/Barrow-County/filter/include=sold-1wk',
'https://www.redfin.com/county/511/GA/Bartow-County/filter/include=sold-1wk',
'https://www.redfin.com/county/521/GA/Butts-County/filter/include=sold-1wk',
'https://www.redfin.com/county/525/GA/Carroll-County/filter/include=sold-1wk',
'https://www.redfin.com/county/534/GA/Clayton-County/filter/include=sold-1wk',
'https://www.redfin.com/county/536/GA/Cobb-County/filter/include=sold-1wk',
'https://www.redfin.com/county/536/GA/Coweta-County/filter/include=sold-1wk',
'https://www.redfin.com/county/559/GA/Fayette-County/filter/include=sold-1wk',
'https://www.redfin.com/county/545/GA/Dawson-County/filter/include=sold-1wk',
'https://www.redfin.com/county/547/GA/DeKalb-County/filter/include=sold-1wk',
'https://www.redfin.com/county/551/GA/Douglas-County/filter/include=sold-1wk',
'https://www.redfin.com/county/561/GA/Forsyth-County/filter/include=sold-1wk',
'https://www.redfin.com/county/563/GA/Fulton-County/filter/include=sold-1wk',
'https://www.redfin.com/county/570/GA/Gwinnett-County/filter/include=sold-1wk',
'https://www.redfin.com/county/572/GA/Hall-County/filter/include=sold-1wk',
'https://www.redfin.com/county/574/GA/Haralson-County/filter/include=sold-1wk',
'https://www.redfin.com/county/577/GA/Heard-County/filter/include=sold-1wk',
'https://www.redfin.com/county/578/GA/Henry-County/filter/include=sold-1wk',
'https://www.redfin.com/county/582/GA/Jasper-County/filter/include=sold-1wk',
'https://www.redfin.com/county/588/GA/Lamar-County/filter/include=sold-1wk',
'https://www.redfin.com/county/602/GA/Meriwether-County/filter/include=sold-1wk',
'https://www.redfin.com/county/610/GA/Newton-County/filter/include=sold-1wk',
'https://www.redfin.com/county/613/GA/Paulding-County/filter/include=sold-1wk',
'https://www.redfin.com/county/615/GA/Pickens-County/filter/include=sold-1wk',
'https://www.redfin.com/county/617/GA/Pike-County/filter/include=sold-1wk',
'https://www.redfin.com/county/625/GA/Rockdale-County/filter/include=sold-1wk',
'https://www.redfin.com/county/629/GA/Spalding-County/filter/include=sold-1wk',
'https://www.redfin.com/county/650/GA/Walton-County/filter/include=sold-1wk'); 
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
	echo "Scraping In progress Dont Stop\n";
	sleep(10);
	if($html)
	{
		$Checkpage	=	$html->find("//[@id='sidepane-header']/div[2]/div/div[1]",0);
		$totalpages = 	str_replace("20 of" ,"",$Checkpage);
		$num 		=	preg_replace("/[^0-9\.]/", '', $totalpages);
		$bindas		= ceil($pagination	=	$num/20);
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
