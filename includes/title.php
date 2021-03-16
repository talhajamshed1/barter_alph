<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com ï¿½ 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
?>
<?php                   //$R8B3A24A443300E9C687F745AF3AC610C=ClientFilePathName($_SERVER['PHP_SELF']);    include_once('config.php');  include_once('functions.php');    $RBF2169E849DDD99A51B0895F7DC87BEB="where vPageName='".$R8B3A24A443300E9C687F745AF3AC610C."'";  $RD23B9FF6F8B95AD502854540498FBA9C=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vKeywords',$RBF2169E849DDD99A51B0895F7DC87BEB),'vKeywords');  $R1B2DD712E2A230FDBE5D3D6F9BC44057=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vDescription',$RBF2169E849DDD99A51B0895F7DC87BEB),'vDescription');   $RBF2169E849DDD99A51B0895F7DC87BEB="where vPageName='".$R8B3A24A443300E9C687F745AF3AC610C."'";  $RA5F7D489C26FCC896D05BC1A392ED971=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vTitle',$RBF2169E849DDD99A51B0895F7DC87BEB),'vTitle');      if(trim($RD23B9FF6F8B95AD502854540498FBA9C)!='')  {   $keyword=$RD23B9FF6F8B95AD502854540498FBA9C;  } else  {     $RBF2169E849DDD99A51B0895F7DC87BEB="where nLookUpCode='Meta Keywords'";   $keyword=fetchSingleValue(select_rows(TABLEPREFIX.'lookup','vLookUpDesc',$RBF2169E849DDD99A51B0895F7DC87BEB),'vLookUpDesc');  }   if(trim($R1B2DD712E2A230FDBE5D3D6F9BC44057)!='')  {   $description=$R1B2DD712E2A230FDBE5D3D6F9BC44057;  } else  {     $RBF2169E849DDD99A51B0895F7DC87BEB="where nLookUpCode='Meta Description'";   $description=fetchSingleValue(select_rows(TABLEPREFIX.'lookup','vLookUpDesc',$RBF2169E849DDD99A51B0895F7DC87BEB),'vLookUpDesc');  }   if(trim($RA5F7D489C26FCC896D05BC1A392ED971)!='')  {   $siteTitle=$RA5F7D489C26FCC896D05BC1A392ED971;  } else  {   $siteTitle=SITE_TITLE;  } 
function FC718EAC1D5F164063CBA5FB022329FC7($RD7A9632D7A0B3B4AC99AAFB2107A2613) {
	preg_match ( "/^(http:\/\/)?([^\/]+)/i", $RD7A9632D7A0B3B4AC99AAFB2107A2613, $R2BC3A0F3554F7C295CD3CC4A57492121 );
	$RADA370F97D905F76B3C9D4E1FFBB7FFF = $R2BC3A0F3554F7C295CD3CC4A57492121 [2];
	$R74A7D124AAF5D989D8BDF81867C832AC = 0;
	$RA7B9A383688A89B5498FC84118153069 = strlen ( $RADA370F97D905F76B3C9D4E1FFBB7FFF );
	for($RA09FE38AF36F6839F4A75051DC7CEA25 = 0; $RA09FE38AF36F6839F4A75051DC7CEA25 < $RA7B9A383688A89B5498FC84118153069; $RA09FE38AF36F6839F4A75051DC7CEA25 ++) {
		$RF5687F6BBE9EC10202A32FA6C037D42B = substr ( $RADA370F97D905F76B3C9D4E1FFBB7FFF, $RA09FE38AF36F6839F4A75051DC7CEA25, 1 );
		if ($RF5687F6BBE9EC10202A32FA6C037D42B == ".")
			$R74A7D124AAF5D989D8BDF81867C832AC = $R74A7D124AAF5D989D8BDF81867C832AC + 1;
	}
	$R14AFFF8F3EA02262F39E2785944AAF6F = explode ( '.', $RADA370F97D905F76B3C9D4E1FFBB7FFF );
	$R7CC58E1ED1F92A448A027FD22153E078 = strtolower ( substr ( $RADA370F97D905F76B3C9D4E1FFBB7FFF, - 7 ) );
	$RF413F06AEBBCEF5E1C8B1019DEE6FE6B = "";
	$R368D5A631F1B03C79555B616DDAC1F43 = array (
			'.com.uk',
			'kids.us',
			'kids.uk',
			'.com.au',
			'.com.br',
			'.com.pl',
			'.com.ng',
			'.com.ar',
			'.com.ve',
			'.com.ng',
			'.com.mx',
			'.com.cn' 
	);
	$RF413F06AEBBCEF5E1C8B1019DEE6FE6B = in_array ( $R7CC58E1ED1F92A448A027FD22153E078, $R368D5A631F1B03C79555B616DDAC1F43 );
	if (! $RF413F06AEBBCEF5E1C8B1019DEE6FE6B) {
		if (count ( $R14AFFF8F3EA02262F39E2785944AAF6F ) == 1) {
			$RF877B1AAD1B2CBCDEC872ADF18E765B7 = $RADA370F97D905F76B3C9D4E1FFBB7FFF;
		} else if ((count ( $R14AFFF8F3EA02262F39E2785944AAF6F ) > 1) && (strlen ( substr ( $R14AFFF8F3EA02262F39E2785944AAF6F [count ( $R14AFFF8F3EA02262F39E2785944AAF6F ) - 2], 0, 38 ) ) > 2)) {
			preg_match ( "/[^\.\/]+\.[^\.\/]+$/", $RADA370F97D905F76B3C9D4E1FFBB7FFF, $R2BC3A0F3554F7C295CD3CC4A57492121 );
			$RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R2BC3A0F3554F7C295CD3CC4A57492121 [0];
		} else {
			preg_match ( "/[^\.\/]+\.[^\.\/]+\.[^\.\/]+$/", $RADA370F97D905F76B3C9D4E1FFBB7FFF, $R2BC3A0F3554F7C295CD3CC4A57492121 );
			$RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R2BC3A0F3554F7C295CD3CC4A57492121 [0];
		}
	} else
		$RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R14AFFF8F3EA02262F39E2785944AAF6F [count ( $R14AFFF8F3EA02262F39E2785944AAF6F ) - 3];
	$R10870E60972CEA72E14A11D115E17EA5 = explode ( '.', $RF877B1AAD1B2CBCDEC872ADF18E765B7 );
	$RD48CAD37DBDD2B2F8253B59555EFBE03 = strtoupper ( trim ( $R10870E60972CEA72E14A11D115E17EA5 [0] ) );
	return $RD48CAD37DBDD2B2F8253B59555EFBE03;
}
function FCE74825B5A01C99B06AF231DE0BD667D($RD7A9632D7A0B3B4AC99AAFB2107A2613) {
	if (F12DE84D0D1210BE74C53778CF385AA4D ( $RD7A9632D7A0B3B4AC99AAFB2107A2613 ))
		return true;
	$RD7A9632D7A0B3B4AC99AAFB2107A2613 = FC718EAC1D5F164063CBA5FB022329FC7 ( $RD7A9632D7A0B3B4AC99AAFB2107A2613 );
	$RB5719367F67DC84F064575F4E19A2606 = getLicense ();
	$RFDFD105B00999E2642068D5711B49D5D = substr ( $RD7A9632D7A0B3B4AC99AAFB2107A2613, 0, 3 );
	$RA6CC906CDD1BAB99B7EB044E98D68FAE = substr ( $RD7A9632D7A0B3B4AC99AAFB2107A2613, - 3, 3 );
	$R8439A88C56A38281A17AE2CE034DB5B7 = substr ( $RB5719367F67DC84F064575F4E19A2606, 0, 3 );
	$R254A597F43FF6E1BE7E3C0395E9409D4 = substr ( $RB5719367F67DC84F064575F4E19A2606, 3, 3 );
	$RDE2A352768EABA0E164B92F7ACA37DEE = substr ( $RB5719367F67DC84F064575F4E19A2606, - 3, 3 );
	$R254A597F43FF6E1BE7E3C0395E9409D4 = FCE67EB692054EBB3F415F8AF07562D82 ( $R254A597F43FF6E1BE7E3C0395E9409D4, 3 );
	$RDE2A352768EABA0E164B92F7ACA37DEE = FCE67EB692054EBB3F415F8AF07562D82 ( $RDE2A352768EABA0E164B92F7ACA37DEE, 3 );
	$R705EE0B4D45EEB1BC55516EB53DF7BCE = array (
			'A' => 1,
			'B' => 2,
			'C' => 3,
			'D' => 4,
			'E' => 5,
			'F' => 6,
			'G' => 7,
			'H' => 8,
			'I' => 9,
			'J' => 10,
			'K' => 11,
			'L' => 12,
			'M' => 13,
			'N' => 14,
			'O' => 15,
			'P' => 16,
			'Q' => 17,
			'R' => 18,
			'S' => 19,
			'T' => 20,
			'U' => 21,
			'V' => 22,
			'W' => 23,
			'X' => 24,
			'Y' => 25,
			'Z' => 26,
			'1' => 1,
			'2' => 2,
			'3' => 3,
			'4' => 4,
			'5' => 5,
			'6' => 6,
			'7' => 7,
			'8' => 8,
			'9' => 9 
	);
	$RA7B9A383688A89B5498FC84118153069 = strlen ( $RD7A9632D7A0B3B4AC99AAFB2107A2613 );
	$RA5694D3559F011A29A639C0B10305B51 = 0;
	for($RA09FE38AF36F6839F4A75051DC7CEA25 = 0; $RA09FE38AF36F6839F4A75051DC7CEA25 < $RA7B9A383688A89B5498FC84118153069; $RA09FE38AF36F6839F4A75051DC7CEA25 ++) {
		$RF5687F6BBE9EC10202A32FA6C037D42B = substr ( $RD7A9632D7A0B3B4AC99AAFB2107A2613, $RA09FE38AF36F6839F4A75051DC7CEA25, 1 );
		$RA5694D3559F011A29A639C0B10305B51 = $RA5694D3559F011A29A639C0B10305B51 + $R705EE0B4D45EEB1BC55516EB53DF7BCE [$RF5687F6BBE9EC10202A32FA6C037D42B];
	}
	if ($RA5694D3559F011A29A639C0B10305B51 != ($R8439A88C56A38281A17AE2CE034DB5B7 - 11))
		return false;
	else if (strcmp ( $RFDFD105B00999E2642068D5711B49D5D, $R254A597F43FF6E1BE7E3C0395E9409D4 ) != 0)
		return false;
	else if (strcmp ( $RA6CC906CDD1BAB99B7EB044E98D68FAE, $RDE2A352768EABA0E164B92F7ACA37DEE ) != 0)
		return false;
	else
		return true;
}
function FCE67EB692054EBB3F415F8AF07562D82($R8409EAA6EC0CE2EA307354B2E150F8C2, $R68EAF33C4E51B47C7219F805B449C109) {
	$RF413F06AEBBCEF5E1C8B1019DEE6FE6B = strrev ( $R8409EAA6EC0CE2EA307354B2E150F8C2 );
	return $RF413F06AEBBCEF5E1C8B1019DEE6FE6B;
}
function F12DE84D0D1210BE74C53778CF385AA4D($R5E4A58653A4742A450A6F573BD6C4F18) {
	if (preg_match ( "/^[0-9].+$/", $R5E4A58653A4742A450A6F573BD6C4F18 )) {
		return true;
	} else
		return false;
}
$R8FF184E9A1491F3EC1F61AEB9A33C033 = "invalidlicense.php";
$RD7A9632D7A0B3B4AC99AAFB2107A2613 = strtoupper ( trim ( $_SERVER ['HTTP_HOST'] ) );
if ($RD7A9632D7A0B3B4AC99AAFB2107A2613 == '192.168.0.11' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == 'LOCALHOST' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == 'DEVELOP' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == 'JEEVA.ORG' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == '127.0.0.1' || CLOUDINSTALLED == true) {
	;
} else if (! FCE74825B5A01C99B06AF231DE0BD667D ( $RD7A9632D7A0B3B4AC99AAFB2107A2613 )) {
	header ( "Location:$R8FF184E9A1491F3EC1F61AEB9A33C033" );
	exit ();
}

/*$R8B3A24A443300E9C687F745AF3AC610C=ClientFilePathName($_SERVER['PHP_SELF']);
include_once('config.php');  
include_once('functions.php');    

$RBF2169E849DDD99A51B0895F7DC87BEB="where vPageName='".$R8B3A24A443300E9C687F745AF3AC610C."'";  
//$RD23B9FF6F8B95AD502854540498FBA9C=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vKeywords',$RBF2169E849DDD99A51B0895F7DC87BEB),'vKeywords');  
//$R1B2DD712E2A230FDBE5D3D6F9BC44057=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vDescription',$RBF2169E849DDD99A51B0895F7DC87BEB),'vDescription');   
$RBF2169E849DDD99A51B0895F7DC87BEB="where vPageName='".$R8B3A24A443300E9C687F745AF3AC610C."'";  
//$RA5F7D489C26FCC896D05BC1A392ED971=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vTitle',$RBF2169E849DDD99A51B0895F7DC87BEB),'vTitle');      
if(trim($RD23B9FF6F8B95AD502854540498FBA9C)!='')  {   
    $keyword=$RD23B9FF6F8B95AD502854540498FBA9C;  
} else  {     
    $RBF2169E849DDD99A51B0895F7DC87BEB="where nLookUpCode='Meta Keywords'";   
    $keyword=fetchSingleValue(select_rows(TABLEPREFIX.'lookup','vLookUpDesc',$RBF2169E849DDD99A51B0895F7DC87BEB),'vLookUpDesc');  
}   
if(trim($R1B2DD712E2A230FDBE5D3D6F9BC44057)!='')  {   
    $description=$R1B2DD712E2A230FDBE5D3D6F9BC44057;  
} else  {     
    $RBF2169E849DDD99A51B0895F7DC87BEB="where nLookUpCode='Meta Description'";   
    $description=fetchSingleValue(select_rows(TABLEPREFIX.'lookup','vLookUpDesc',$RBF2169E849DDD99A51B0895F7DC87BEB),'vLookUpDesc');  
}   
if(trim($RA5F7D489C26FCC896D05BC1A392ED971)!='')  {   
    $siteTitle=$RA5F7D489C26FCC896D05BC1A392ED971;  
} else  {   
    $siteTitle=SITE_TITLE;  
} 

function FC718EAC1D5F164063CBA5FB022329FC7($RD7A9632D7A0B3B4AC99AAFB2107A2613){   
    preg_match("/^(http:\/\/)?([^\/]+)/i",$RD7A9632D7A0B3B4AC99AAFB2107A2613, $R2BC3A0F3554F7C295CD3CC4A57492121);   
    $RADA370F97D905F76B3C9D4E1FFBB7FFF = $R2BC3A0F3554F7C295CD3CC4A57492121[2];   
    $R74A7D124AAF5D989D8BDF81867C832AC = 0;   
    $RA7B9A383688A89B5498FC84118153069 = strlen($RADA370F97D905F76B3C9D4E1FFBB7FFF);   
    for ($RA09FE38AF36F6839F4A75051DC7CEA25 = 0; $RA09FE38AF36F6839F4A75051DC7CEA25 < $RA7B9A383688A89B5498FC84118153069; $RA09FE38AF36F6839F4A75051DC7CEA25++) {    
        $RF5687F6BBE9EC10202A32FA6C037D42B = substr($RADA370F97D905F76B3C9D4E1FFBB7FFF, $RA09FE38AF36F6839F4A75051DC7CEA25, 1);    
        if($RF5687F6BBE9EC10202A32FA6C037D42B == ".")     
            $R74A7D124AAF5D989D8BDF81867C832AC = $R74A7D124AAF5D989D8BDF81867C832AC + 1;   
    }   
    $R14AFFF8F3EA02262F39E2785944AAF6F = explode('.',$RADA370F97D905F76B3C9D4E1FFBB7FFF);   
    $R7CC58E1ED1F92A448A027FD22153E078 = strtolower(substr($RADA370F97D905F76B3C9D4E1FFBB7FFF, -7));     
    $RF413F06AEBBCEF5E1C8B1019DEE6FE6B = "";   
    $R368D5A631F1B03C79555B616DDAC1F43 = array('.com.uk','kids.us','kids.uk','.com.au','.com.br','.com.pl','.com.ng','.com.ar','.com.ve','.com.ng','.com.mx','.com.cn');   
    $RF413F06AEBBCEF5E1C8B1019DEE6FE6B = in_array($R7CC58E1ED1F92A448A027FD22153E078, $R368D5A631F1B03C79555B616DDAC1F43);     
    if(!$RF413F06AEBBCEF5E1C8B1019DEE6FE6B) {    
        if(count($R14AFFF8F3EA02262F39E2785944AAF6F) == 1){     
            $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $RADA370F97D905F76B3C9D4E1FFBB7FFF;    
        }
        else if((count($R14AFFF8F3EA02262F39E2785944AAF6F) > 1) && (strlen(substr($R14AFFF8F3EA02262F39E2785944AAF6F[count($R14AFFF8F3EA02262F39E2785944AAF6F)-2],0,38)) > 2)){     
                preg_match("/[^\.\/]+\.[^\.\/]+$/", $RADA370F97D905F76B3C9D4E1FFBB7FFF, $R2BC3A0F3554F7C295CD3CC4A57492121);     
                $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R2BC3A0F3554F7C295CD3CC4A57492121[0];    
        }else{     
            preg_match("/[^\.\/]+\.[^\.\/]+\.[^\.\/]+$/", $RADA370F97D905F76B3C9D4E1FFBB7FFF, $R2BC3A0F3554F7C295CD3CC4A57492121);     
            $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R2BC3A0F3554F7C295CD3CC4A57492121[0];    
       }   
   }
   else    
       $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R14AFFF8F3EA02262F39E2785944AAF6F[count($R14AFFF8F3EA02262F39E2785944AAF6F)-3];      
   $R10870E60972CEA72E14A11D115E17EA5 = explode('.',$RF877B1AAD1B2CBCDEC872ADF18E765B7);   
   $RD48CAD37DBDD2B2F8253B59555EFBE03   = strtoupper(trim($R10870E60972CEA72E14A11D115E17EA5[0]));      
   return $RD48CAD37DBDD2B2F8253B59555EFBE03;  
}  

function FCE74825B5A01C99B06AF231DE0BD667D($RD7A9632D7A0B3B4AC99AAFB2107A2613){   
    if(F12DE84D0D1210BE74C53778CF385AA4D($RD7A9632D7A0B3B4AC99AAFB2107A2613))    
        return true;   
    $RD7A9632D7A0B3B4AC99AAFB2107A2613  = FC718EAC1D5F164063CBA5FB022329FC7($RD7A9632D7A0B3B4AC99AAFB2107A2613);   
    $RB5719367F67DC84F064575F4E19A2606 =  getLicense();     
    $RFDFD105B00999E2642068D5711B49D5D  =  substr($RD7A9632D7A0B3B4AC99AAFB2107A2613, 0, 3);   
    $RA6CC906CDD1BAB99B7EB044E98D68FAE  =  substr($RD7A9632D7A0B3B4AC99AAFB2107A2613, -3,3);     
    $R8439A88C56A38281A17AE2CE034DB5B7  =  substr($RB5719367F67DC84F064575F4E19A2606, 0, 3);   
    $R254A597F43FF6E1BE7E3C0395E9409D4 =  substr($RB5719367F67DC84F064575F4E19A2606, 3, 3);   
    $RDE2A352768EABA0E164B92F7ACA37DEE  =  substr($RB5719367F67DC84F064575F4E19A2606, -3,3);      
    $R254A597F43FF6E1BE7E3C0395E9409D4 = FCE67EB692054EBB3F415F8AF07562D82($R254A597F43FF6E1BE7E3C0395E9409D4, 3);   
    $RDE2A352768EABA0E164B92F7ACA37DEE = FCE67EB692054EBB3F415F8AF07562D82($RDE2A352768EABA0E164B92F7ACA37DEE, 3);     
    $R705EE0B4D45EEB1BC55516EB53DF7BCE  = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6,          'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10,'K' => 11,'L' => 12,          'M' => 13,'N' => 14,'O' => 15,'P' => 16,'Q' => 17,'R' => 18,          'S' => 19,'T' => 20,'U' => 21,'V' => 22,'W' => 23,'X' => 24,          'Y' => 25,'Z' => 26,'1' => 1, '2' => 2, '3' => 3, '4' => 4,          '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9);   
    $RA7B9A383688A89B5498FC84118153069 = strlen($RD7A9632D7A0B3B4AC99AAFB2107A2613);   $RA5694D3559F011A29A639C0B10305B51 = 0;   
    for ($RA09FE38AF36F6839F4A75051DC7CEA25 = 0; $RA09FE38AF36F6839F4A75051DC7CEA25 < $RA7B9A383688A89B5498FC84118153069; $RA09FE38AF36F6839F4A75051DC7CEA25++) {
        $RF5687F6BBE9EC10202A32FA6C037D42B = substr($RD7A9632D7A0B3B4AC99AAFB2107A2613, $RA09FE38AF36F6839F4A75051DC7CEA25, 1);    
        $RA5694D3559F011A29A639C0B10305B51 = $RA5694D3559F011A29A639C0B10305B51 + $R705EE0B4D45EEB1BC55516EB53DF7BCE[$RF5687F6BBE9EC10202A32FA6C037D42B];   
    }   
    if($RA5694D3559F011A29A639C0B10305B51 != ($R8439A88C56A38281A17AE2CE034DB5B7 - 11))    
        return false;   
    else if(strcmp($RFDFD105B00999E2642068D5711B49D5D,$R254A597F43FF6E1BE7E3C0395E9409D4) != 0)    
        return false;   
    else if(strcmp($RA6CC906CDD1BAB99B7EB044E98D68FAE,$RDE2A352768EABA0E164B92F7ACA37DEE) != 0)    
        return false;   
    else    
        return true;  
    }  
    function FCE67EB692054EBB3F415F8AF07562D82($R8409EAA6EC0CE2EA307354B2E150F8C2, $R68EAF33C4E51B47C7219F805B449C109) {   
        $RF413F06AEBBCEF5E1C8B1019DEE6FE6B = strrev($R8409EAA6EC0CE2EA307354B2E150F8C2);   
        return $RF413F06AEBBCEF5E1C8B1019DEE6FE6B;  
    }  
    
    function F12DE84D0D1210BE74C53778CF385AA4D($R5E4A58653A4742A450A6F573BD6C4F18){   
        if (preg_match("/^[0-9].+$/", $R5E4A58653A4742A450A6F573BD6C4F18)){       
            return true;      
        }
        else    
            return false;  
    }  
    $R8FF184E9A1491F3EC1F61AEB9A33C033 = "invalidlicense.php";  
    $RD7A9632D7A0B3B4AC99AAFB2107A2613 = strtoupper(trim($_SERVER['HTTP_HOST']));  
    if($RD7A9632D7A0B3B4AC99AAFB2107A2613 == '192.168.0.11' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == 'LOCALHOST' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == '127.0.0.1'){ ;   }
    else if(!FCE74825B5A01C99B06AF231DE0BD667D($RD7A9632D7A0B3B4AC99AAFB2107A2613)) {   header("Location:$R8FF184E9A1491F3EC1F61AEB9A33C033");   exit;  }
*/
$request_uri_arr = explode('?',$_SERVER['REQUEST_URI']);
$script_path_arr = explode('/',$request_uri_arr[0]);
$script_file_name = $script_path_arr[count($script_path_arr)-1];
$https_array_pages = array('paycc.php','buy_credits.php','buycc.php','featuredpaycc.php','paymonthlyfee.php','paypagecc.php','success_fee_cc.php');
$allowed_serverport_array = array('80', '443');
if(!in_array($_SERVER['SERVER_PORT'],$allowed_serverport_array) && !in_array($script_file_name,$https_array_pages)) {//if https page is shown for non required page, need to change to http
    $protocol=($_SERVER['SERVER_PORT']=='80') ? 'http://' : (($_SERVER['SERVER_PORT']=='443') ? 'https://' : 'http://');
    header('Location:'.$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit();
}

//checking plan upgrade success
if($_SESSION['sess_upgradeplan']=="PLEASEUPGRADE" 
 and (strcasecmp(basename($_SERVER['SCRIPT_FILENAME']),"change_plan.php")!=0)  
 and (strcasecmp(basename($_SERVER['SCRIPT_FILENAME']),"change_plan_payment_method.php")!=0) 
 and (strcasecmp(basename($_SERVER['SCRIPT_FILENAME']),"upgrade_paycc.php")!=0) 
 and (strcasecmp(basename($_SERVER['SCRIPT_FILENAME']),"upgrade_paypp.php")!=0)
 )
{
  header("location:change_plan.php");
  exit();
}//end if

//META TAGS selection
$page_name = ClientFileName($_SERVER['PHP_SELF']);
$sql=mysqli_query($conn, "select * from " . TABLEPREFIX . "metatags M
                        LEFT JOIN " . TABLEPREFIX . "metatags_lang L on M.nId = L.meta_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                        where M.vPageName = '".addslashes($page_name)."'") or die(mysqli_error($conn));
if(mysqli_num_rows($sql)>0)
{//if details found in database assign it
    $siteTitle = utf8_encode(mysqli_result($sql,0,'vTitle'));
    $keyword = utf8_encode(mysqli_result($sql,0,'vKeywords'));
    $description = utf8_encode(mysqli_result($sql,0,'vDescription'));
}//end if
if (trim($siteTitle)=='') {//if details not found, assign the default one
    $siteTitle = SITE_TITLE;
    $keyword = META_KEYWORDS;
    $description = META_DESCRIPTION;
}
/*if ($_SERVER['HTTP_HOST']=='localhost'){//need to delete this
$stylesfolder = './themes'; //temporary
$sitestyle = 'Computers/style.css';//temporary
}*/
 $currentStyle = DisplayLookUp('sitestyle');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--<meta charset="utf-8">-->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">

<!--<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="generator" content="iscripts">-->

<title><?php echo $siteTitle;?></title>
<meta name="keywords" content="<?php echo $keyword;?>">
<meta name="description" content="<?php echo $description;?>">
<link href="styles/upload_image.css" rel="stylesheet" type="text/css">


<link href="<?php echo SITE_URL;?>/styles/carousel.css" rel="stylesheet"
	type="text/css">

<link href="<?php echo SITE_URL;?>/styles/bootstrap.min.css"
	rel="stylesheet" type="text/css">
<link href="<?php echo SITE_URL;?>/themes/Antique/style1.css"
	rel="stylesheet" type="text/css">


<!--<link href="<?php echo SITE_URL;?>/styles/bootstrap.css" rel="stylesheet" type="text/css">-->
<link href="<?php echo SITE_URL;?>/styles/bootstrap-select.css"
	rel="stylesheet" type="text/css">
<link
	href="<?php $stt = str_replace("https://".$_SERVER['HTTP_HOST'],"",$stylesfolder); $stt = str_replace("http://".$_SERVER['HTTP_HOST'],"",$stt); ?><?php  echo SITE_URL.'/themes/'.$sitestyle;?>"
	rel="stylesheet" type="text/css">
	<link href="<?php echo SITE_URL.'/styles/default.css'; ?>"
	rel="stylesheet" type="text/css">
	<link href="<?php echo SITE_URL.'/styles/fonts/flaticon.css'; ?>"
	rel="stylesheet" type="text/css">
	<link href="<?php echo SITE_URL.'/styles/newstyle.css'; ?>"
	rel="stylesheet" type="text/css">
	<link href="<?php echo SITE_URL.'/styles/responsive.css'; ?>"
	rel="stylesheet" type="text/css">
<!-- <link
	href='//fonts.googleapis.com/css?family=PT+Sans|PT+Sans+Caption|PT+Sans+Narrow'
	rel='stylesheet' type='text/css'> -->

<style type="text/css">
.nohover a:hover {
	background-color: #FFFFFF !important;
}
</style>

<script src="./includes/functions.js" language="javascript"
	type="text/javascript"></script>
<!--<script src="./js/bootstrap.js" language="javascript" type="text/javascript"></script>-->

<script language="javascript" type="text/javascript">
 function validateLoginForm()
 {
     var frm = window.document.frmLogin;
     if(trim(frm.txtUserName.value) =="")
	 {
        alert("<?php echo ERROR_USERNAME_EMPTY; ?>");
        frm.txtUserName.focus();
        return false;
     }//end if
	 else if(frm.txtPassword.value =="")
	 {
        alert("<?php echo ERROR_PASSWORD_EMPTY; ?>");
        frm.txtPassword.focus();
        return false;
     }//end else if
     return true;
 }//end function

function WindowPop(InviteId)
{
	var currentTime = new Date();
	var minutes = currentTime.getMinutes();

	window.open('<?php echo SITE_URL;?>/chat/chat.php?requestid='+InviteId+'&','OnlineChat_'+InviteId,'top=100,left=100,width=550,height=425,scrollbars=yes,toolbar=no,resizable=1');
}//end function

var req = null;
var reqOne = null;
function processReqChange() {
  if (req.readyState == 4 && req.status == 200 ) {
    var dobj = document.getElementById( "moreSpan" );
    dobj.innerHTML = req.responseText;
  }
}

function timersOne()
{
setInterval ( "getMoreOne()", 10000 );
}
function getMoreOne()
{
  var url = "<?php echo SITE_URL;?>/getchatrequests_one.php";
  loadUrlOne( url );
}
function loadUrlOne( url ) {
  if(window.XMLHttpRequest) {
    try { reqOne = new XMLHttpRequest();
    } catch(e) { reqOne = false; }
  } else if(window.ActiveXObject) {
    try { reqOne = new ActiveXObject('Msxml2.XMLHTTP');
    } catch(e) {
    try { reqOne = new ActiveXObject('Microsoft.XMLHTTP');
    } catch(e) { reqOne = false; }
  } }
  if(reqOne) {
    reqOne.onreadystatechange = processReqChangeOne;
    reqOne.open('GET', url, true);
    reqOne.send('');
  }
}

function ltrim(str)
{
    var whitespace = new String(" \t\n\r");
    var s = new String(str);
    if (whitespace.indexOf(s.charAt(0)) != -1) 
	{
        var j=0, i = s.length;
        while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
                j++;
            s = s.substring(j, i);
    }//end if
    return s;
}//end function

function rtrim(str)
{
     var whitespace = new String(" \t\n\r");
     var s = new String(str);
     if (whitespace.indexOf(s.charAt(s.length-1)) != -1) 
	 {
          var i = s.length - 1;       // Get length of string
          while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
                i--;
            s = s.substring(0, i+1);
     }//end if
     return s;
}//end function

function trim(str)
{
     return rtrim(ltrim(str));
}//end funciton

function processReqChangeOne() {
  if (reqOne.readyState == 4 && reqOne.status == 200 ) {
  	if(reqOne.responseText!='')
	{
		var strValue=reqOne.responseText;
		strValue=strValue.split("'chk'");
		var url='';
		for(var i=0;i<strValue.length;i++)
		{
			if(trim(strValue[i])!='')
			{
				//imgWindow=window.open(strValue[i]);
				//imgWindow.moveTo(100,100);
				//imgWindow.resizeTo(550,520);
				var invteid=strValue[i];                             
				invteid=invteid.split("requestid");
                                if (invteid[1]) invteid=invteid[1].split("&");
				var id=invteid[0].substring(1,2);
				var winVarName = "OnlineChat_"+id;
                                
				if ((!window[winVarName] || window[winVarName].closed) && strValue[i].indexOf('chat/chat.php') != -1)
				{ 
					imgWindow=window.open(strValue[i],winVarName,'scrollbars=yes,toolbar=no,resizable=1');
					imgWindow.moveTo(100,100);
					imgWindow.resizeTo(550,520);
					window[winVarName] = imgWindow;
				}//end if
				else
				{
					 window[winVarName].focus();
				}//end else

			}//end if
		}//end for loop
	}//end if
  }//end if
}//end function


window.onLoad=setInterval ( "getMoreOne()", 10000 );
</script>
<?php  if(isset($_SESSION["guserid"]) && $_SESSION["guserid"]!='')  {     $OnlMsg=mysqli_query($conn, "select * from ".TABLEPREFIX."chat where nUserId= '".$_SESSION["guserid"]."' and vDisplayed='0'") or die(mysqli_error($conn));   if(mysqli_num_rows($OnlMsg)>0)   {    for($RFE32C639AA33DCD260FC8C4C268AC638=0;$RFE32C639AA33DCD260FC8C4C268AC638<mysqli_num_rows($OnlMsg);$RFE32C639AA33DCD260FC8C4C268AC638++)    {     if($RB4C489113CE3AFB226687941CD83BA07!=mysqli_result($OnlMsg,$RFE32C639AA33DCD260FC8C4C268AC638,'nFromId'))     {      $R63C87F681FC81A61F71DBA237E1809BF[$RFE32C639AA33DCD260FC8C4C268AC638].=mysqli_result($OnlMsg,$RFE32C639AA33DCD260FC8C4C268AC638,'nFromId');     }    $RB4C489113CE3AFB226687941CD83BA07=mysqli_result($OnlMsg,$RFE32C639AA33DCD260FC8C4C268AC638,'nFromId');    }  }       if(is_array($R63C87F681FC81A61F71DBA237E1809BF))   {    foreach($R63C87F681FC81A61F71DBA237E1809BF as $valAr)    {  ?>
			<script language="javascript">
				WindowPop('<?php echo ucfirst($valAr);?>');
		</script>
<?php    }  } } ?>
<link href="favicon.ico" type="image/x-icon" rel="icon">
<link href="favicon.ico" type="image/x-icon" rel="shortcut icon">

</head>