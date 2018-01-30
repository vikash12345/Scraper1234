<?
require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';
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
	   
	   
 
 
$Links	=	array('https://www.redfin.com/county/559/GA/Fayette-County/filter/include=sold-1wk');
//'https://www.redfin.com/county/536/GA/Cobb-County/filter/include=sold-1wk',

for ($mainpage = 0; $mainpage < sizeof($Links); $mainpage++)
{
	$Mainpage	=	$Links[$mainpage];
	$html	=	dlPage($Mainpage);
	if($html)
	{
		$Checkpage	=	$html->find("//[@id='sidepane-header']/div[2]/div/div[1]",0);
		$totalpages = 	str_replace("20 of" ,"",$Checkpage);
		$num 		=	preg_replace("/[^0-9\.]/", '', $totalpages);
		$bindas		= ceil($pagination	=	$num/20);
		for ($i = 1; $i <= $bindas; $i++)
		{
			$innerlink	=	$Links[$mainpage].'/page-'.$i;
			$pages		=	dlpage($innerlink);
			if($pages)
			{
				
				for($j = 0; $j <= 19; $j++) 
				{
					$sold 			=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/div[2]/span",0)->plaintext;
					$address		=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/a[2]/div[1]/div[2]",0)->plaintext;
					$listingurl		=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[2]/div[2]/a",0)->href;
					$price			=	$pages->find("//*[@id='MapHomeCard_$j']/div/div[1]/a[2]/div[1]/div[1]/span[2]",0)->plaintext;
					$link			=	'https://www.redfin.com/'.$listingurl;
					
					if($address == "")
					{
					$record = array( 'listingurl' =>$link, 
		   			'price' => $price,
		  			 'address' => $address, 
		   			'sold' => $sold,
					'mainpage' => $Mainpage);
					           scraperwiki::save(array('listingurl','price','address','sold','mainpage'), $record);
					}
					
				}
				
			}
		}
		
	}
  
}
?>
