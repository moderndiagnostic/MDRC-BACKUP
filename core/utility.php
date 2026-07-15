<?
class utility extends Singleton
{
	private $uploaded_file;
	public $app;
	public static function &get_instance()
	{
		parent::$my_name = __CLASS__;
		return parent::get_instance();
	}
	function init(){
		$this->app = &app::get_instance();
	}
	function get_uploaded_file(){
		return $this->uploaded_file;
	}
	function upload_file($file)
	{
		$file_info = $this->get_file_info($file['name']);
		$tmpname = time()."_".mt_rand(1000, 2000).".".$file_info->extension;
		if(!move_uploaded_file($file['tmp_name'], ABS_PATH.DS."temp".DS.$tmpname)){
			return false;
		}else{
			$this->uploaded_file = ABS_PATH.DS."temp".DS.$tmpname;
			return true;
		}
	}
	function store_uploaded_file($uploaddir, $uploadfilename="",$chmod=""){
		if($uploadfilename==""){
			$uploadfilename = basename($local_file);
		}
		$tmpname = $this->uploaded_file;
		if(USE_FTP){
			if(!class_exists("ftp")){
				$ftp_class = $this->app->add_module("ftp");
				if($ftp_class != NULL){
					$ftp = new $ftp_class();
				}else{
					trigger_error("Could not load ftp module", E_USER_ERROR);
				}
			}else{
				$ftp = new ftp();
			}
			if(!$ftp->SetServer(FTP_HOST)) {
				$ftp->quit();
				return false;
			}
			if (!$ftp->connect()) {
				$ftp->quit();
				return false;
			}
			if (!$ftp->login(FTP_USERNAME, FTP_PASSWORD)) {
				$ftp->quit();
				return false;
			}
			$ftp->SetType(FTP_AUTOASCII);
			$ftp->Passive(FALSE);
			$ftp->chdir(FTP_WWWDIR.$uploaddir);
			$ftp->pwd();
			if(FALSE === $ftp->put($tmpname, $uploadfilename)){
				if($this->uploaded_file!=$tmpname){
					@unlink($tmpname);
				}
				$ftp->quit();
				return false;
			}else{
				if($this->uploaded_file!=$tmpname){
					@unlink($tmpname);
				}
				if(is_numeric($chmod)){
					$ftp->chmod($uploadfilename, $chmod);
				}
				$ftp->quit();
				return true;
			}
		}else{
			if(copy($tmpname, ABS_PATH.DS.$uploaddir.DS.$uploadfilename)){
				if($this->uploaded_file!=$tmpname){
					@unlink($tmpname);
				}
				return true;
			}else{
				if($this->uploaded_file!=$tmpname){
					@unlink($tmpname);
				}
				return false;
			}
		}
	}
	function remove_uploaded_file(){
		@unlink($this->uploaded_file);
	}
	function HTMLSafeString($Input, $QuotedString=true){
		$Output = "";
		$Output = strip_tags($Input);
		if($QuotedString)
			$Output = str_replace("\"","",$Output);
		return $Output;
	}
	function DateAdd($interval, $number, $date="") {
		if($date!=""){
			$date_time_array = getdate($date);
		}else{
			$date_time_array = getdate();
		}
		$hours = $date_time_array['hours'];
		$minutes = $date_time_array['minutes'];
		$seconds = $date_time_array['seconds'];
		$month = $date_time_array['mon'];
		$day = $date_time_array['mday'];
		$year = $date_time_array['year'];
		switch ($interval) {
			case 'yyyy':
				$year+=$number;
				break;
			case 'q':
				$year+=($number*3);
				break;
			case 'm':
				$month+=$number;
				break;
			case 'y':
			case 'd':
			case 'w':
				$day+=$number;
				break;
			case 'ww':
				$day+=($number*7);
				break;
			case 'h':
				$hours+=$number;
				break;
			case 'n':
				$minutes+=$number;
				break;
			case 's':
				$seconds+=$number;
				break;
		}
		$timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
		return $timestamp;
	}
	function GetPageName(){
		$tmpArray = explode(DS,$_SERVER['SCRIPT_FILENAME']);
		$pagename = $tmpArray[sizeof($tmpArray)-1];
		return $pagename;
	}
	function GetPageURL(){
		$pageURL = 'http://';
		if(array_key_exists("HTTPS", $_SERVER)){
			if(strtoupper($_SERVER["HTTPS"])=="ON"){
				$pageURL = 'https://';
			}
		}
		$pageURL .= $_SERVER['HTTP_HOST']."/".$_SERVER["REQUEST_URI"];
		return $pageURL;
	}
	function GetContentType($file_extension){
		switch(strtolower($file_extension)){
			 case "pdf": $ctype="application/pdf"; break;
			 case "exe": $ctype="application/octet-stream"; break;
			 case "zip": $ctype="application/zip"; break;
			 case "doc": $ctype="application/msword"; break;
			 case "xls": $ctype="application/vnd.ms-excel"; break;
			 case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			 case "gif": $ctype="image/gif"; break;
			 case "png": $ctype="image/png"; break;
			 case "jpeg":
			 case "jpg": $ctype="image/jpg"; break;
			 case "mp3": $ctype="audio/mpeg"; break;
			 case "wav": $ctype="audio/x-wav"; break;
			 case "mpeg":
			 case "mpg":
			 case "mpe": $ctype="video/mpeg"; break;
			 case "mov": $ctype="video/quicktime"; break;
			 case "avi": $ctype="video/x-msvideo"; break;
			 case "php":
			 case "htm":
			 case "html":
			 case "txt": die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;
			 default: $ctype="application/x-download";
		}
		return $ctype;
	}
	function GenerateRandomKey($Length){
		$Key = "";
		$found = false;
		while(strlen($Key)<$Length){
			srand((double)microtime()*1000000);
			$number = rand(50,150);
			if($number>=65 && $number<=90)
				$Key = $Key.chr($number);
			elseif($number>=48 && $number<=57)
				$Key = $Key.chr($number);
		}
		return trim($Key);
	}
	function ParseMailTemplate($Template, $Custom=""){
		$GeneralKywords = array();
		$GeneralKywords["SERVER_ROOT"]=SERVER_ROOT;
		$f = fopen(ABS_PATH.DS.MAIL_TEMPLATE_PATH."/".$Template,"r");
		if(!$f){
			return NULL;
		}
		$TemplateBody = fread($f,filesize(ABS_PATH.DS.MAIL_TEMPLATE_PATH."/".$Template));
		fclose($f);
		$HTMLBody=$TemplateBody;
		if(is_array($Custom)){
			foreach($Custom as $Find=>$ReplaceWith){
				$TemplateBody = str_replace("{".$Find."}",$ReplaceWith,$TemplateBody);
			}
		}
		foreach($GeneralKywords as $Find=>$ReplaceWith){
			$TemplateBody = str_replace("{".$Find."}",$ReplaceWith,$TemplateBody);
		}
		return $TemplateBody;
	}
	function ParseMailText($Text, $Custom=""){
		$GeneralKywords = array();
		$GeneralKywords["SERVER_ROOT"]=SERVER_ROOT;
		$TemplateBody = $Text;
		$HTMLBody=$TemplateBody;
		if(is_array($Custom)){
			foreach($Custom as $Find=>$ReplaceWith){
				$TemplateBody = str_replace("{".$Find."}",$ReplaceWith,$TemplateBody);
			}
		}
		foreach($GeneralKywords as $Find=>$ReplaceWith){
			$TemplateBody = str_replace("{".$Find."}",$ReplaceWith,$TemplateBody);
		}
		return $TemplateBody;
	}
	function DateDiff($endDate, $beginDate){
		$date_parts1[0]=date("m", $beginDate);
		$date_parts1[1]=date("d", $beginDate);
		$date_parts1[2]=date("Y", $beginDate);
		$date_parts2[0]=date("m", $endDate);
		$date_parts2[1]=date("d", $endDate);
		$date_parts2[2]=date("Y", $endDate);
		$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
		$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
		return $end_date - $start_date;
	}
	function TimeDiff($bigTime,$smallTime){
			list($h1,$m1,$s1)=split(":",$bigTime);
			list($h2,$m2,$s2)=split(":",$smallTime);
			$second1=$s1+($h1*3600)+($m1*60);//converting it into seconds
			$second2=$s2+($h2*3600)+($m2*60);
			if ($second1==$second2)
			{
				$resultTime="00:00:00";
				return $resultTime;
				exit();
			}
			if ($second1<$second2) //
			{
				$second1=$second1+(24*60*60);//adding 24 hours to it.
			}
			$second3=$second1-$second2;
			//print $second3;
			if ($second3==0)
			{
				$h3=0;
			}
			else
			{
				$h3=floor($second3/3600);//find total hours
			}
			$remSecond=$second3-($h3*3600);//get remaining seconds
			if ($remSecond==0)
			{
				$m3=0;
			}
			else
			{
				$m3=floor($remSecond/60);// for finding remaining  minutes
			}
			$s3=$remSecond-(60*$m3);
			if($h3==0)//formating result.
			{
				$h3="00";
			}
			if($m3==0)
			{
				$m3="00";
			}
			if($s3==0)
			{
				$s3="00";
			}
			$resultTime=array($h3,$m3,$s3);
			return $resultTime;
	}
	function ChangeDateFormat($Date, $FromFormat, $ToFormat){
		$KnownFormat = array("012"=>"ddmmyyyy","102"=>"mmddyyyy","210"=>"yyyymmdd");
		if(!in_array($FromFormat,$KnownFormat) || !in_array($ToFormat,$KnownFormat)){
			echo "<h3>Error in function \"ConvertDateFormat\" : Unknown Date Format";
			exit;
		}
		$Seperator="";
		if(strpos($Date,"/")===false){
		}else{
			$Seperator="/";
		}
		if(strpos($Date,"\\")===false){
		}else{
			$Seperator="\\";
		}
		if(strpos($Date,"-")===false){
		}else{
			$Seperator="-";
		}
		if($Seperator==""){
			echo "<h3>Error in function \"ChangeDateFormat\" : Unknown Date Seperator";
			exit;
		}
		$DateArr = explode($Seperator,$Date);
		$FromDateSequence = array_search($FromFormat, $KnownFormat);
		$Day = $DateArr[strpos($FromDateSequence,"0")];
		$Month = $DateArr[strpos($FromDateSequence,"1")];
		$Year = $DateArr[strpos($FromDateSequence,"2")];
		$ToDateSequence = array_search($ToFormat, $KnownFormat);
		$NewDate = $DateArr[substr($ToDateSequence,0,1)].$Seperator.$DateArr[substr($ToDateSequence,1,1)].$Seperator.$DateArr[substr($ToDateSequence,2,1)];
		return $NewDate;
	}
	function NormalizeURL($URL, $tolower = true){
		$find = array("/\s+/", "/[-]+/", "/\\\/", "/'/");
		$replace_with = array("-", "-", "", "");
		$URL = preg_replace($find, $replace_with, $URL);
		if($tolower){
			$URL = strtolower($URL);
		}
		return strtolower($URL);
	}
	function ArraySearchRecursive($needle, $haystack){
		foreach($haystack as $value){
			if(is_array($value))
				$match=array_search_r($needle, $value);
			if($value==$needle)
				$match=1;
			if($match)
				return 1;
		}
		return 0;
	}
	function html2txt ( $document ){
			$search = array ("'<script[^>]*?>.*?</script>'si",	// strip out javascript
					"'<[\/\!]*?[^<>]*?>'si",		// strip out html tags
					"'([\r\n])[\s]+'",			// strip out white space
					"'@<![\s\S]*?�??[ \t\n\r]*>@'",
					"'&(quot|#34|#034|#x22);'i",		// replace html entities
					"'&(amp|#38|#038|#x26);'i",		// added hexadecimal values
					"'&(lt|#60|#060|#x3c);'i",
					"'&(gt|#62|#062|#x3e);'i",
					"'&(nbsp|#160|#xa0);'i",
					"'&(iexcl|#161);'i",
					"'&(cent|#162);'i",
					"'&(pound|#163);'i",
					"'&(copy|#169);'i",
					"'&(reg|#174);'i",
					"'&(deg|#176);'i",
					"'&(#39|#039|#x27);'",
					"'&(euro|#8364);'i",			// europe
					"'&a(uml|UML);'",			// german
					"'&o(uml|UML);'",
					"'&u(uml|UML);'",
					"'&A(uml|UML);'",
					"'&O(uml|UML);'",
					"'&U(uml|UML);'",
					"'&szlig;'i",
					);
			$replace = array (	"",
						"",
						" ",
						"\"",
						"&",
						"<",
						">",
						" ",
						chr(161),
						chr(162),
						chr(163),
						chr(169),
						chr(174),
						chr(176),
						chr(39),
						chr(128),
						"ä",
						"ö",
						"ü",
						"�?",
						"�?",
						"�?",
						"�?",
					);
			$text = preg_replace($search,$replace,$document);
			return trim ( $text );
	}
	function get_file_info($file){
		$file_name = basename($file);
		$tmparr = explode(".", $file_name);
		$fileinfo = (object)NULL;
		$file_name = "";
		for($i=0; $i<(count($tmparr)-1); $i++){
			$file_name.=".".$tmparr[$i];
		}
		if(strlen($file_name)>0){
			$file_name = substr($file_name, 1);
		}
		$fileinfo->filename = $file_name;
		$fileinfo->extension = $tmparr[count($tmparr)-1];
		return $fileinfo;
	}
	function random_color(){
		mt_srand((double)microtime()*1000000);
		$c = '';
		while(strlen($c)<6){
			$c .= sprintf("%02X", mt_rand(0, 255));
		}
		return $c;
	}
	function format_currency($number, $decimal_places=2, $decimal_symbol=".", $thousand_seperator=",", $currency_symbol="", $currency_symbol_position='before'){
		if(!is_numeric($number)){
			return $number;
		}else{
			$formatted_number = number_format($number, $decimal_places, $decimal_symbol, $thousand_seperator);
			if($currency_symbol!=""){
				if($currency_symbol_position=='after'){
					$formatted_number = $formatted_number." ".$currency_symbol;
				}else{
					$formatted_number = $currency_symbol." ".$formatted_number;
				}
			}
			return $formatted_number;
		}
	}
	function set_message($message, $type){
		$_SESSION['msg'] = $message;
		$_SESSION['type'] = $type;
	}
	function get_message(){
		if(isset($_SESSION['msg']) && isset($_SESSION['type'])){
			if($_SESSION['type']=='SUCCESS'){
				if(VIR_DIR!="")
				{
					if(VIR_DIR=="admin/")
					{
				$message =  '<div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                        <h4><strong>Success</strong></h4>
                                        <p>'.$_SESSION['msg'].'</p>
                                    </div>';
					}
					else
					{
						$message = '<div class="alert alert-success">
					 <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
					<strong>Success!</strong> '.$_SESSION['msg'].'
					</div>';
					}
				}
				else
				{
					$message =  '<p class="alert alert-success border-0 p-3 pl-5 rounded fs__12"><i class="las la-bell fs__22 mr-2 position-absolute top-15 left-15 text-dark"></i> '.$_SESSION['msg'].'</p>
				';
				}
			}else if($_SESSION['type']=='ERROR'){
				if(VIR_DIR!="")
				{
					if(VIR_DIR=="admin/")
					{
						$message =  '
						<div class="alert alert-danger alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                        <h4><strong>Error</strong></h4>
                                        <p> '.$_SESSION['msg'].'</p>
                                    </div>';
					}
					else
					{
						$message =  '<div class="alert alert-error">
					 <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
					 <strong>Error!</strong> '.$_SESSION['msg'].'
						</div>';
					}
				}
				else
				{
				$message =  '<p class="alert alert-danger border-0 p-3 pl-5 rounded fs__12"><i class="las la-bell fs__22 mr-2 position-absolute top-15 left-15 text-dark"></i> '.$_SESSION['msg'].'</p>
				';
				}
			}else if($_SESSION['type']=='MESSAGE'){
				$message =  '<div class="alert_box r_corners warning m_bottom_10">
					 	<button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
						<i class="fa fa-exclamation-triangle"></i><p>'.$_SESSION['msg'].'</p>
						</div>';
			}
			unset($_SESSION['msg']);
			unset($_SESSION['type']);
			return $message;
		}
	}
	function string_truncate($string,$length){
		$length_of_string = strlen($string);
		if($length_of_string > $length ){
			return substr($string,0,$length)."..";
		}else{
			return $string;
		}
	}
	function xTimeAgo ($oldTime, $newTime, $timeType) {
        $timeCalc = strtotime($newTime) - strtotime($oldTime);
        if ($timeType == "x") {
            if ($timeCalc == 60) {
                $timeType = "m";
            }
            if ($timeCalc == (60*60)) {
                $timeType = "h";
            }
            if ($timeCalc == (60*60*24)) {
                $timeType = "d";
            }
        }
        if ($timeType == "s") {
            $timeCalc .= " seconds ago";
        }
        if ($timeType == "m") {
            $timeCalc = round($timeCalc/60) . " minutes ago";
        }
        if ($timeType == "h") {
            $timeCalc = round($timeCalc/60/60) . " hours ago";
        }
        if ($timeType == "d") {
            $timeCalc = round($timeCalc/60/60/24) . " days ago";
        }
        return $timeCalc;
    }
	function change_weight_display($value){
		$round = $value/1000;
		if($round>=1){
			$num=number_format($round,2);
			$num=$num;
			return $num." Kg";
		}else{
				$num=$value;
			return $num." Gm";
		}
	}
	function change_weight_display_other($value){
		$round = $value/1000;
		if($round>=1){
			$num=number_format($round,2);
			$num=$num;
			return $num." Kg";
		}else{
				$num=$value;
			return (int) $num." Gm";
		}
	}
function seo_url($string)
{
    $string = str_replace(array('[\', \']'), '', $string);
    $string = preg_replace('/\[.*\]/U', '', $string);
    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
    return strtolower(trim($string, '-'));
}
function getExtension($str)
{
        			 $i = strrpos($str,".");
        			 if (!$i) { return ""; }
         			$l = strlen($str) - $i;
         			$ext = substr($str,$i+1,$l);
         			return $ext;
}
function resize_image($uploadedfile_name,$uploadedfile_tmpname,$image_user_config,$user_width1,$user_width2,$user_width3)
{
			$errors=0;
		//$image =$_FILES["file"]["name"];
			$uploadedfile = $uploadedfile_tmpname;
			$file_name = basename($uploadedfile_name);
    		$file_info = $this->get_file_info($file_name);
			if(strtoupper($file_info->extension)=="JPG" || strtoupper($file_info->extension)=="JPEG" || strtoupper($file_info->extension)=="GIF"  || strtoupper($file_info->extension)=="PNG"){
			$new_name = rand(9,99).time().".".$file_info->extension;
					}
 			if ($new_name)
 			{
 			$filename = stripslashes($uploadedfile_name);
 	 		$i = strrpos($filename,".");
        	 if (!$i) { return ""; }
         	 $l = strlen($filename) - $i;
         	 $ext = substr($filename,$i+1,$l);
			$extension = $ext;
 			$extension = strtolower($extension);
 			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
 			{
 			$change='<div class="msgdiv">Unknown Image extension </div> ';
 			$errors=1;
 			}
 			else
 			{
 			$size=filesize($uploadedfile_tmpname);
			if($extension=="jpg" || $extension=="jpeg" )
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefromjpeg($uploadedfile);
			}
			else if($extension=="png")
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefrompng($uploadedfile);
			}
			else
			{
			$src = imagecreatefromgif($uploadedfile);
			}
			echo $scr;
			list($width,$height)=getimagesize($uploadedfile);
			if($width>$user_width1)
					{
					$newwidth=$user_width1;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					else
					{
					$newwidth=$width;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					if($width>$user_width2)
					{
					$newwidth1=$user_width2;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					}
					else
					{
					$newwidth1=$width;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					}
					if($width>$user_width3)
					{
					$newwidth2=$user_width3;
					$newheight2=($height/$width)*$newwidth2;
					$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
					}
					else
					{
					$newwidth2=$width;
					$newheight2=($height/$width)*$newwidth2;
					$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
					}
			imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
    		imagealphablending($tmp, false);
    		imagesavealpha($tmp, true);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			imagecolortransparent($tmp1, imagecolorallocatealpha($tmp1, 0, 0, 0, 127));
    		imagealphablending($tmp1, false);
    		imagesavealpha($tmp1, true);
			imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
			imagecolortransparent($tmp2, imagecolorallocatealpha($tmp2, 0, 0, 0, 127));
    		imagealphablending($tmp2, false);
    		imagesavealpha($tmp2, true);
			imagecopyresampled($tmp2,$src,0,0,0,0,$newwidth2,$newheight2,$width,$height);
			$filename = "../".$image_user_config.$new_name;
			$filename1 = "../".$image_user_config."mediumthumb".$new_name;
			$filename2 = "../".$image_user_config."thumb".$new_name;
			if($extension=="jpg" || $extension=="jpeg" )
			{
			imagejpeg($tmp,$filename,100);
			imagejpeg($tmp1,$filename1,100);
			imagejpeg($tmp2,$filename2,100);
			}
			else if($extension=="png")
			{
			imagepng($tmp,$filename);
			imagepng($tmp1,$filename1);
			imagepng($tmp2,$filename2);
			}
			else
			{
			imagepng($tmp,$filename,100);
			imagepng($tmp1,$filename1,100);
			imagepng($tmp2,$filename2,100);
			}
			imagedestroy($src);
			imagedestroy($tmp);
			imagedestroy($tmp1);
			imagedestroy($tmp2);
}
}
return $new_name;
}
function resize_multi_image($uploadedfile_name,$uploadedfile_tmpname,$image_user_config,$user_width1,$user_width2,$user_width3)
{
			$errors=0;
		//$image =$_FILES["file"]["name"];
			$uploadedfile = $uploadedfile_tmpname;
			$file_name = basename($uploadedfile_name);
    		$file_info = $this->get_file_info($file_name);
			if(strtoupper($file_info->extension)=="JPG" || strtoupper($file_info->extension)=="JPEG"  || strtoupper($file_info->extension)=="GIF"  || strtoupper($file_info->extension)=="PNG"){
			$new_name =time().rand(1125,999).".".$file_info->extension;
					}
 			if ($new_name)
 			{
 			$filename = stripslashes($uploadedfile_name);
 	 		$i = strrpos($filename,".");
        	 if (!$i) { return ""; }
         	 $l = strlen($filename) - $i;
         	 $ext = substr($filename,$i+1,$l);
			$extension = $ext;
 			$extension = strtolower($extension);
 			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
 			{
 			$change='<div class="msgdiv">Unknown Image extension </div> ';
 			$errors=1;
 			}
 			else
 			{
 			$size=filesize($uploadedfile_tmpname);
			if($extension=="jpg" || $extension=="jpeg" )
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefromjpeg($uploadedfile);
			}
			else if($extension=="png")
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefrompng($uploadedfile);
			}
			else
			{
			$src = imagecreatefromgif($uploadedfile);
			}
			echo $scr;
			list($width,$height)=getimagesize($uploadedfile);
			if($width>$user_width1)
					{
					$newwidth=$user_width1;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					else
					{
					$newwidth=$width;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					if($width>$user_width2)
					{
					$newwidth1=$user_width2;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					}
					else
					{
					$newwidth1=$width;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					}
					if($width>$user_width3)
					{
					$newwidth2=$user_width3;
					$newheight2=($height/$width)*$newwidth2;
					$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
					}
					else
					{
					$newwidth2=$width;
					$newheight2=($height/$width)*$newwidth2;
					$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
					}
			imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
    		imagealphablending($tmp, false);
    		imagesavealpha($tmp, true);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			imagecolortransparent($tmp1, imagecolorallocatealpha($tmp1, 0, 0, 0, 127));
    		imagealphablending($tmp1, false);
    		imagesavealpha($tmp1, true);
			imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
			imagecolortransparent($tmp2, imagecolorallocatealpha($tmp2, 0, 0, 0, 127));
    		imagealphablending($tmp2, false);
    		imagesavealpha($tmp2, true);
			imagecopyresampled($tmp2,$src,0,0,0,0,$newwidth2,$newheight2,$width,$height);
			$filename = $image_user_config.$new_name;
			$filename1 = $image_user_config."mediumthumb".$new_name;
			$filename2 = $image_user_config."thumb".$new_name;
			if($extension=="jpg" || $extension=="jpeg" )
			{
			imagejpeg($tmp,$filename,100);
			imagejpeg($tmp1,$filename1,100);
			imagejpeg($tmp2,$filename2,100);
			}
			else if($extension=="png")
			{
			imagepng($tmp,$filename);
			imagepng($tmp1,$filename1);
			imagepng($tmp2,$filename2);
			}
			else
			{
			imagepng($tmp,$filename,100);
			imagepng($tmp1,$filename1,100);
			imagepng($tmp2,$filename2,100);
			}
			imagedestroy($src);
			imagedestroy($tmp);
			imagedestroy($tmp1);
			imagedestroy($tmp2);
}
}
return $new_name;
}
//FOr Single image resize
function resize_single_image($uploadedfile_name,$uploadedfile_tmpname,$image_user_config,$user_width1)
{
			$errors=0;
			//$image =$_FILES["file"]["name"];
			if(!empty($uploadedfile_name))
			{
			$uploadedfile = $uploadedfile_tmpname;
			$file_name = basename($uploadedfile_name);
    		$file_info = $this->get_file_info($file_name);
			if(strtoupper($file_info->extension)=="JPG" || strtoupper($file_info->extension)=="JPEG"  || strtoupper($file_info->extension)=="GIF"  || strtoupper($file_info->extension)=="PNG"){
			$new_name = time().rand(9,99).".".$file_info->extension;
			}
 			if ($new_name)
 			{
 			$filename = stripslashes($uploadedfile_name);
 			 $i = strrpos($filename,".");
        	 if (!$i) { return ""; }
         	 $l = strlen($filename) - $i;
         	 $ext = substr($filename,$i+1,$l);
			$extension = $ext;
 			$extension = strtolower($extension);
 			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
 			{
 			$change='<div class="msgdiv">Unknown Image extension </div> ';
 			$errors=1;
 			}
 			else
 			{
 			$size=filesize($uploadedfile_tmpname);
			if($extension=="jpg" || $extension=="jpeg" )
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefromjpeg($uploadedfile);
			}
			else if($extension=="png")
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefrompng($uploadedfile);
			}
			else
			{
			$src = imagecreatefromgif($uploadedfile);
			}
			//echo $scr;
			list($width,$height)=getimagesize($uploadedfile);
			if($width>$user_width1)
					{
					$newwidth=$user_width1;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					else
					{
					$newwidth=$width;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
			imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
    		imagealphablending($tmp, false);
    		imagesavealpha($tmp, true);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			$filename = "../".$image_user_config.$new_name;
			if($extension=="jpg" || $extension=="jpeg" )
			{
			imagejpeg($tmp,$filename,100);
			}
			else if($extension=="png")
			{
			imagepng($tmp,$filename);
			}
			else
			{
			imagepng($tmp,$filename,100);
			}
			imagedestroy($src);
			imagedestroy($tmp);
			}
}
else
{
}
}
return $new_name;
}
function resize_single_image_front($uploadedfile_name,$uploadedfile_tmpname,$image_user_config,$user_width1)
{
$errors=0;
//$image =$_FILES["file"]["name"];
			$uploadedfile = $uploadedfile_tmpname;
			$file_name = basename($uploadedfile_name);
    		$file_info = $this->get_file_info($file_name);
					if(strtoupper($file_info->extension)=="JPG" || strtoupper($file_info->extension)=="GIF"  || strtoupper($file_info->extension)=="PNG"){
						$new_name = time().".".$file_info->extension;
					}
 			if ($new_name)
 			{
 			$filename = stripslashes($uploadedfile_name);
  			$extension = $this->getExtension($filename);
 			$extension = strtolower($extension);
 			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
 			{
 			$change='
 			<div class="msgdiv">Unknown Image extension </div>
';
 			$errors=1;
 			}
 			else
 			{
 		$size=filesize($uploadedfile_tmpname);
			if($extension=="jpg" || $extension=="jpeg" )
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefromjpeg($uploadedfile);
			}
			else if($extension=="png")
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefrompng($uploadedfile);
			}
			else
			{
			$src = imagecreatefromgif($uploadedfile);
			}
			echo $scr;
			list($width,$height)=getimagesize($uploadedfile);
			if($width>$user_width1)
					{
					$newwidth=$user_width1;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					else
					{
					$newwidth=$width;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
			imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
    		imagealphablending($tmp, false);
    		imagesavealpha($tmp, true);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			//imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			$filename =$image_user_config.$new_name;
			if($extension=="jpg" || $extension=="jpeg" )
			{
			imagejpeg($tmp,$filename,100);
			}
			else if($extension=="png")
			{
			imagepng($tmp,$filename);
			}
			else
			{
			imagepng($tmp,$filename,100);
			}
			//imagejpeg($tmp,$filename,100);
			imagedestroy($src);
			imagedestroy($tmp);
}
}
return $new_name;
}
function is_member_login()
{
	if(isset($_SESSION['MemberID']))
	{
		$login = true;
	}
	else
	{
		$login = false;
	}
	return $login;
}
function thumbnail($image_path,$thumb_path,$image_name,$thumb_width)
{
    $src_img = imagecreatefromjpeg("$image_path/$image_name");
    $origw=imagesx($src_img);
    $origh=imagesy($src_img);
    $new_w = $thumb_width;
    $diff=$origw/$new_w;
    $new_h=$new_w;
    $dst_img = imagecreate($new_w,$new_h);
    imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
    imagejpeg($dst_img, "$thumb_path/$image_name");
    return TRUE;
}
function resize($filename, $width, $height)
	  {
		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename))
		{
			return;
		}
		$info = pathinfo($filename);
		$extension = $info['extension'];
		$old_image = $filename;
		 $new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
			$path = '';
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}
			list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);
			if ($width_orig != $width || $height_orig != $height) {
				//$is=$this->app->load_module("Image");
				/*if($is == NULL)
				{
				echo 'Could not load Image Resizer Module';
			    }*/
				$is=$this->app->load_module("Image");
				$is = new Image(DIR_IMAGE . $old_image);
				$is->resize($width, $height);
				$is->save(DIR_IMAGE . $new_image);
			} else {
				copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
			}
		}
		if (isset($_SERVER["HTTPS"]) && (($_SERVER["HTTPS"] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return $new_image;
		} else {
			return $new_image;
		}
	}
function highlight1($text, $words)
{
    $split_words = explode("" , $words );
    foreach($split_words as $word)
    {
        $word=trim($word);
		$color = "#e5e5e5";
$text =preg_replace("|($word)|Ui" ,"<b class='matched_word'><b>$1</b></b>" , $text );
    }
    return $text;
}
function highlight($str, $keyword) {
$str = preg_replace("/\b([a-z]*${keyword}[a-z]*)\b/i","<b>$1</b>",$str);
return $str;
}
function curPageURL() {
 $pageURL = 'http';
 //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
function removeFromString($str, $item) {
    $parts = explode(',', $str);
    while(($i = array_search($item, $parts)) !== false) {
        unset($parts[$i]);
    }
    return implode(',', $parts);
}
function keygen($length)
{
		   	 $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   			 $charactersLength = strlen($characters);
   			 $key = '';
			for ($i = 0; $i < $length; $i++) {
				$key .= $characters[rand(0, $charactersLength - 1)];
			}
		if(strlen($key)!=$length)
		{
			$this->keygen($length);
		}
		else if(strlen($key)==$length)
		{
			if($key!='')
			{
				$obj_model_user = $this->app->load_model("customer");
				$rs_user = $obj_model_user->execute("SELECT",false,"SELECT ref_key FROM customer WHERE ref_key='".$key."'","");
				if(count($rs_user)>0)
				{
					$this->keygen($length);
				}
				else
				{
					return $key;
				}
			}
			else
			{
				$this->keygen($length);
			}
		}
		else
		{
			$this->keygen($length);
		}
}
function random_password($length)
{
	$key = '';
	list($usec, $sec) = explode(' ', microtime());
	mt_srand((float) $sec + ((float) $usec * 100000));
   	$inputs = array_merge(range('a','z'),range(0,9),range('A','Z'));
   	for($i=0; $i<$length; $i++)
	{
   	    $key .= $inputs[mt_rand(0,61)];
	}
	return $key;
}
function last_id($table_name)
		{
			$obj_model_lid = $this->app->load_model($table_name);
			$rslid=$obj_model_lid->execute("SELECT",false,"","","id DESC LIMIT 1");
			return $rslid[0]['id'];
		}
function unique_slug($table_name,$action,$slug_field,$value,$edit_id=0)
		{
		 if($action=='add'){
			$value_slug=$this->seo_url($value);
			$obj_model_slug = $this->app->load_model($table_name);
			$rsslug=$obj_model_slug->execute("SELECT",false,"","".$slug_field."='".$value_slug."'");
			if(count($rsslug)>0)
			{
				$slug_id=$this->last_id($table_name)+1;
				$slug=$value_slug.'_'.$slug_id;
			}
			else
			{
			$slug=$value_slug;
			}
		 }
		 else
		 {
			$value_slug=$this->seo_url($value);
			$obj_model_slug = $this->app->load_model($table_name);
			$rsslug=$obj_model_slug->execute("SELECT",false,"","id!=".$edit_id." and ".$slug_field."='".$value_slug."'");
			if(count($rsslug)>0)
			{
				$slug_id=$edit_id;
				$slug=$value_slug.'_'.$slug_id;
			}
			else
			{
			$slug=$value_slug;
			}
		 }
		return $slug;
}
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function detect_browser()
{
// Copyright 2013.1.5 Mehdi Jazini mr.jazini@gmail.com
$ExactBrowserNameUA=$_SERVER['HTTP_USER_AGENT'];
If (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "opr/")) {
    // OPERA
    $ExactBrowserNameBR="Opera";
} else if (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "chrome/")) {
    // CHROME
    $ExactBrowserNameBR="Chrome";
} else if (strpos(strtolower($ExactBrowserNameUA), "msie")) {
    // INTERNET EXPLORER
    $ExactBrowserNameBR="Internet Explorer";
} else if (strpos(strtolower($ExactBrowserNameUA), "firefox/")) {
    // FIREFOX
    $ExactBrowserNameBR="Firefox";
} else if (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "opr/")==false and strpos(strtolower($ExactBrowserNameUA), "chrome/")==false) {
    // SAFARI
    $ExactBrowserNameBR="Safari";
} else {
    // OUT OF DATA
    $ExactBrowserNameBR="OUT OF DATA";
};
return $ExactBrowserNameBR;
}
function get_sms_balance()
{
		$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://control.msg91.com/api/balance.php?authkey=368534AnUMuT68h4J6167e05cP1&type=4",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
} else {
}
	return $response;
}
function send_email_data($mail_data)
{
			$obj_model_tabel = $this->app->load_model("generel_settings");
			$rs_data = $obj_model_tabel->execute("SELECT", false, "", "");
			$template_name=$mail_data['template_name'];
			if($template_name=='contact_admin.html')
			{
				$email=$rs_data[0]['to_emails'];
				$cc_emails=$rs_data[0]['cc_emails'];
				$e_data=explode(',',$cc_emails);
			}
			else if($template_name=='career_admin.html')
			{
				$email=$rs_data[0]['career_to_emails'];
				$cc_emails=$rs_data[0]['career_cc_emails'];
				$e_data=explode(',',$cc_emails);
			}
			else if($template_name=='project_admin.html')
			{
				$email=$rs_data[0]['project_to_emails'];
				$cc_emails=$rs_data[0]['project_cc_emails'];
				$e_data=explode(',',$cc_emails);
			}
			/*$email="thedezineuser@gmail.com";
			$cc_emails='';
			$e_data='';*/
			$subject=$mail_data['subject'];
			$body_parameters=$mail_data['body_parameters'];
			  $obj_mailer = $this->app->load_module("mailer\sender");
			  $mail_body = $this->ParseMailTemplate($template_name, $body_parameters);
			  if($mail_body==NULL)
			  {
				  $this->app->display_error(NULL, "Could not parse the mail template");
			  }
			  $obj_mailer->create();
			  $obj_mailer->subject($subject);
			  if($mail_data['file_name']!='')
			   {
				   $obj_mailer->attatch($mail_data['filepath'],$mail_data['file_name']);
				}
			  $obj_mailer->add_to(trim($email));
			  if(count($e_data)>1)
			  {
					for($i=0;$i<count($e_data);$i++)
					{
						 $obj_mailer->add_cc(trim($e_data[$i]));
					}
			   }
			  $obj_mailer->htmlbody($mail_body);
			  $flag = $obj_mailer->send();
		return $flag;
}
//New Function
function send_sms_new($mb,$sms_type,$default_string,$new_string)
{
	$obj_model_tabel = $this->app->load_model("sms_data");
	$rs_data = $obj_model_tabel->execute("SELECT", false, "", "name='".$sms_type."' and status='Active'");
	if(count($rs_data)>0)
	{
		$template_id=$rs_data[0]['template_id'];
		$language=$rs_data[0]['language'];
		$sms_text=$rs_data[0]['sms_text'];
		$sms_text_system=$rs_data[0]['sms_text_system'];
		$message_text=str_replace($default_string, $new_string, $sms_text);
		if($mb!='9510069163' && $mb!='1234567890')
		{
			//Your authentication key
			$authKey = '';
			//Multiple mobiles numbers separated by comma
			$mobileNumber='91'.$mb;
			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId ='';//
			//Your message to send, Add URL encoding here.
			$message = urlencode($message_text);
			//Define route
			$route = "4";
			$postData = array(
				'authkey' => $authKey,
				'mobiles' => $mobileNumber,
				'message' => $message,
				'sender' => $senderId,
				'route' => $route,
				'country' => 91,
				'DLT_TE_ID' =>$template_id
			);
			$url="https://control.msg91.com/api/sendhttp.php";
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postData
			));
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$output = curl_exec($ch);
			if(curl_errno($ch))
			{
				echo 'error:' . curl_error($ch);
			}
			curl_close($ch);
		}
		$total_phone=count(explode(',',$mb));
		$update_field=array();
		$update_field['phones'] = $mb;
		$update_field['sms_count'] = $total_phone;
		$update_field['sms_text'] = $message_text;
		$update_field['sms_msg_id'] = $output;
		$update_field['sms_status'] = $sms_status;
		$update_field['entry_date'] = date('d-m-Y');
		$update_field['entry_date_time'] = date('d-m-Y H:i:s');
		$obj_model_sms_history = $this->app->load_model("sms_history");
		$obj_model_sms_history->map_fields($update_field);
		$obj_model_sms_history->execute("INSERT");
	}
}
function generate_OTP($length)
{
	$chars = '1234567890';
	$chars_length = (strlen($chars) - 1);
	$string = $chars[rand(0, $chars_length)];
	for ($i = 1; $i < $length; $i = strlen($string))
    {
       $r = $chars[rand(0, $chars_length)];
       if ($r != $string[$i - 1]) $string .= $r;
    }
	return $string;
}




function get_user_tokens()
{
	$obj_model_cust = $this->app->load_model("user");
	$rs_gcm=$obj_model_cust->execute("SELECT", false, "", "Token!=''","","Token");
	$registation_ids = array();
	for($i=0; $i<count($rs_gcm); $i++)
    {
		array_push($registation_ids, $rs_gcm[$i]['Token']);
    }
	return $registation_ids;
}
function add_push_notification_gcm($data,$from)
{
	// Android App
	$obj_model_table=$this->app->load_model("generel_settings");
	$rs_data=$obj_model_table->execute("SELECT",false,"","");
	$google_key=$rs_data[0]['google_key'];
	$obj_model_cust = $this->app->load_model("user");
	$rs_gcm=$obj_model_cust->execute("SELECT", false, "", "AndroidToken!=''","","AndroidToken");
	$count=count($rs_gcm);
	//echo $count; exit;
	$total_rep=$count/1000 ;
	$a=0;
	$total_rep=(int)$total_rep+1;
	for($i=0;$i<$total_rep;$i++)
	{
		 $obj_model_user=$this->app->load_model("user");
		 $rs_user = $obj_model_user->execute("SELECT",false,"","AndroidToken!=''","id ASC limit ".$a.",1000");
		 $to='';
		 if(count($rs_user)>0)
		 {
			$to = array();
			foreach($rs_user as $item)
			{
				$this->send_push_notifaction($data,$item['AndroidToken']);
			}
		 }
		// $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
		// $fields = array(
        //    'registration_ids' => $to,
		// 	 'data' => $data
        // );
		// if (!defined('SERVER_KEY'))
		// {
		// 	define("SERVER_KEY", $google_key);
		// }
        // $headers = array(
        //     'Authorization:key=' . SERVER_KEY,
        //     'Content-Type:application/json'
        // );
		// $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // $result = curl_exec($ch);
   		// curl_close($ch);
		$a=$a+1000;
	}
	// For Iphone
	$obj_model_cust_iphone = $this->app->load_model("user");
	$rs_iphone_data=$obj_model_cust_iphone->execute("SELECT", false, "", "IphoneToken!=''","","IphoneToken");
	if(count($rs_iphone_data)>0)
	{
			for($i=0;$i<count($rs_iphone_data);$i++)
			{
				$deviceToken =  $rs_iphone_data[$i]['IphoneToken'];
				//$message = 'A push notification has been sent!';
				$ctx = stream_context_create();
				// ck.pem is your certificate file
				stream_context_set_option($ctx, 'ssl', 'local_cert', 'pushcert.pem');
				stream_context_set_option($ctx, 'ssl', 'passphrase', '');
				// Open a connection to the APNS server
				$fp = stream_socket_client(
					'ssl://gateway.push.apple.com:2195', $err,
					//'ssl://gateway.push.apple.com:2195', $err,
					$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
				if (!$fp)
					exit("Failed to connect: $err $errstr" . PHP_EOL);
				$body['aps'] = array(
				'alert' => $data['message'],
				'typedata' => array(
					'body' =>'Notification',
					'values' =>$data,
				),
				'badge' => '',
				'sound' => 'oven.caf'
				);
				$payload = json_encode($body);
				$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
				$result = fwrite($fp, $msg, strlen($msg));
				fclose($fp);
			}
	}
	return '';
}

//ss
//for api
 function indent($json) {
    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;
    for ($i=0; $i<=$strLen; $i++) {
        // Grab the next character in the string.
        $char = substr($json, $i, 1);
        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;
        // If this character is the end of an element,
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }
        // Add the character to the result string.
        $result .= $char;
        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes)
		{
            $result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
        }
        $prevChar = $char;
    }
    return $result;
}
function encrypt( $data)
{
return base64_encode($data);
}
function decrypt($data)
{
  $value= base64_decode($data);
  return $value;
}








function web_mail_header()
{

		$obj_model_table=$this->app->load_model("generel_settings");
	$rs_data=$obj_model_table->execute("SELECT",false,"","");
	$title=$rs_data[0]['project_title'];
	$website=$rs_data[0]['website'];
	$logo=$rs_data[0]['logo'];
	$logourl=SERVER_ROOT.'/uploads/project_image/351670222225.png';


                $html='<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="x-apple-disable-message-reformatting">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>MDRS</title>
<style>
.o_sans,
.o_heading {
  font-family: Helvetica, Arial, sans-serif; }
.o_heading {
  font-weight: bold; }
.o_sans,
.o_heading,
.o_sans p,
.o_sans li {
  margin-top: 0px;
  margin-bottom: 0px; }
a {
  text-decoration: none;
  outline: none; }
.o_underline {
  text-decoration: underline; }
.o_linethrough {
  text-decoration: line-through; }
.o_nowrap {
  white-space: nowrap; }
.o_caps {
  text-transform: uppercase;
  letter-spacing: 1px; }
.o_nowrap {
  white-space: nowrap; }
.o_text-xxs {
  font-size: 12px;
  line-height: 19px; }
.o_text-xs {
  font-size: 14px;
  line-height: 21px; }
.o_text {
  font-size: 16px;
  line-height: 24px; }
.o_text-md {
  font-size: 19px;
  line-height: 28px; }
.o_text-lg {
  font-size: 24px;
  line-height: 30px; }
h1.o_heading {
  font-size: 36px;
  line-height: 47px; }
h2.o_heading {
  font-size: 30px;
  line-height: 39px; }
h3.o_heading {
  font-size: 24px;
  line-height: 31px; }
h4.o_heading {
  font-size: 18px;
  line-height: 23px; }
body,
.e_body {
  width: 100%;
  margin: 0px;
  padding: 0px;
  -webkit-text-size-adjust: 100%;
  -ms-text-size-adjust: 100%; }
.o_re {
  font-size: 0;
  vertical-align: top; }
.o_block {
  max-width: 632px;
  margin: 0 auto; }
.o_block-lg {
  max-width: 800px;
  margin: 0 auto; }
.o_block-xs {
  max-width: 432px;
  margin: 0 auto; }
.o_col,
.o_col_i {
  display: inline-block;
  vertical-align: top; }
.o_col {
  width: 100%; }
.o_col-1 {
  max-width: 100px; }
.o_col-o {
  max-width: 132px; }
.o_col-2 {
  max-width: 200px; }
.o_col-3 {
  max-width: 300px; }
.o_col-4 {
  max-width: 400px; }
.o_col-oo {
  max-width: 468px; }
.o_col-5 {
  max-width: 500px; }
.o_col-6s {
  max-width: 584px; }
.o_col-6 {
  max-width: 600px; }
img {
  -ms-interpolation-mode: bicubic;
  vertical-align: middle;
  border: 0;
  line-height: 100%;
  height: auto;
  outline: none;
  text-decoration: none; }
.o_img-full {
  width: 100%; }
.o_inline {
  display: inline-block; }
.o_btn {
  mso-padding-alt: 12px 24px; }
  .o_btn a {
    display: block;
    padding: 12px 24px;
    mso-text-raise: 3px; }
.o_btn-o {
  mso-padding-alt: 8px 20px; }
  .o_btn-o a {
    display: block;
    padding: 8px 20px;
    mso-text-raise: 3px; }
.o_btn-xs {
  mso-padding-alt: 7px 16px; }
  .o_btn-xs a {
    display: block;
    padding: 7px 16px;
    mso-text-raise: 3px; }
.o_btn-b {
  mso-padding-alt: 7px 8px; }
  .o_btn-b a {
    display: block;
    padding: 7px 8px;
    font-weight: bold; }
  .o_btn-b span {
    mso-text-raise: 6px;
    display: inline; }
.img_fix {
  mso-text-raise: 6px;
  display: inline; }
.o_bg-light {
  background-color: #dbe5ea; }
.o_bg-ultra_light {
  background-color: #ebf5fa; }
.o_bg-white {
  background-color: #ffffff; }
.o_bg-dark {
  background-color: #242b3d; }
.o_bg-primary {
  background-color: #0c2f54; }
.o_bg-secondary {
  background-color: #424651; }
.o_bg-success {
  background-color: #0ec06e; }
.o_text-primary,
a.o_text-primary span,
a.o_text-primary strong,
.o_text-primary.o_link a {
  color: #0c2f54; }
.o_text-secondary,
a.o_text-secondary span,
a.o_text-secondary strong,
.o_text-secondary.o_link a {
  color: #424651; }
.o_text-dark,
a.o_text-dark span,
a.o_text-dark strong,
.o_text-dark.o_link a {
  color: #242b3d; }
.o_text-dark_light,
a.o_text-dark_light span,
a.o_text-dark_light strong,
.o_text-dark_light.o_link a {
  color: #a0a3ab; }
.o_text-white,
a.o_text-white span,
a.o_text-white strong,
.o_text-white.o_link a {
  color: #ffffff; }
.o_text-light,
a.o_text-light span,
a.o_text-light strong,
.o_text-light.o_link a {
  color: #000; }
.o_text-success,
a.o_text-success span,
a.o_text-success strong,
.o_text-success.o_link a {
  color: #0ec06e; }
.o_b-primary {
  border: 2px solid #126de5; }
.o_bb-primary {
  border-bottom: 1px solid #126de5; }
.o_b-secondary {
  border: 2px solid #424651; }
.o_bx-secondary {
  border: 1px solid #424651; }
.o_bb-secondary {
  border-bottom: 1px solid #424651; }
.o_b-dark {
  border: 2px solid #242b3d; }
.o_b-light {
  border: 1px solid #d3dce0; }
.o_bb-light {
  border-bottom: 1px solid #d3dce0; }
.o_bt-light {
  border-top: 1px solid #d3dce0; }
.o_br-light {
  border-right: 4px solid #d3dce0; }
.o_bb-ultra_light {
  border-bottom: 1px solid #b6c0c7; }
.o_bb-dark_light {
  border-bottom: 1px solid #4a5267; }
.o_bt-dark_light {
  border-top: 1px solid #4a5267; }
.o_b-white {
  border: 2px solid #ffffff; }
.o_bb-white {
  border-bottom: 1px solid #ffffff; }
.o_br {
  border-radius: 4px; }
.o_br-t {
  border-radius: 4px 4px 0px 0px; }
.o_br-b {
  border-radius: 0px 0px 4px 4px; }
.o_br-l {
  border-radius: 4px 0px 0px 4px; }
.o_br-r {
  border-radius: 0px 4px 4px 0px; }
.o_br-max {
  border-radius: 96px; }
.o_hide,
.o_hide-lg {
  display: none;
  font-size: 0;
  max-height: 0;
  width: 0;
  line-height: 0;
  overflow: hidden;
  mso-hide: all;
  visibility: hidden; }
.o_center {
  text-align: center; }
table.o_center {
  margin-left: auto;
  margin-right: auto; }
.o_left {
  text-align: left; }
table.o_left {
  margin-left: 0;
  margin-right: auto; }
.o_right {
  text-align: right; }
table.o_right {
  margin-left: auto;
  margin-right: 0; }
.o_px {
  padding-left: 16px;
  padding-right: 16px; }
.o_px-xs {
  padding-left: 8px;
  padding-right: 8px; }
.o_px-md {
  padding-left: 24px;
  padding-right: 24px; }
.o_px-lg {
  padding-left: 32px;
  padding-right: 32px; }
.o_py {
  padding-top: 16px;
  padding-bottom: 16px; }
.o_py-xs {
  padding-top: 8px;
  padding-bottom: 8px; }
.o_py-md {
  padding-top: 24px;
  padding-bottom: 24px; }
.o_py-lg {
  padding-top: 32px;
  padding-bottom: 32px; }
.o_py-xl {
  padding-top: 2px;
  padding-bottom: 2px; }
.o_pt-xs {
  padding-top: 8px; }
.o_pt {
  padding-top: 16px; }
.o_pt-md {
  padding-top: 24px; }
.o_pt-lg {
  padding-top: 32px; }
.o_pb-xs {
  padding-bottom: 8px; }
.o_pb {
  padding-bottom: 16px; }
.o_pb-md {
  padding-bottom: 24px; }
.o_pb-lg {
  padding-bottom: 15px; }
.o_p-icon {
  padding: 12px; }
.o_body .o_mb-xxs {
  margin-bottom: 4px; }
.o_body .o_mb-xs {
  margin-bottom: 8px; }
.o_body .o_mb {
  margin-bottom: 5px; }
.o_body .o_mb-md {
  margin-bottom: 24px; }
.o_body .o_mb-lg {
  margin-bottom: 32px; }
.o_body .o_mt {
  margin-top: 16px; }
.o_body .o_mt-md {
  margin-top: 24px; }
.o_bg-center {
  background-position: 50% 0;
  background-repeat: no-repeat; }
.o_bg-left {
  background-position: 0 0;
  background-repeat: no-repeat; }
@media (max-width: 449px) {
  .o_col-full {
    max-width: 100% !important; }
  .o_col-half {
    max-width: 50% !important; }
  .o_hide-lg {
    display: inline-block !important;
    font-size: inherit !important;
    max-height: none !important;
    line-height: inherit !important;
    overflow: visible !important;
    width: auto !important;
    visibility: visible !important; }
  .o_hide-xs,
  .o_hide-xs.o_col_i {
    display: none !important;
    font-size: 0 !important;
    max-height: 0 !important;
    width: 0 !important;
    line-height: 0 !important;
    overflow: hidden !important;
    visibility: hidden !important;
    height: 0 !important; }
  .o_xs-center {
    text-align: center !important; }
  .o_xs-left {
    text-align: left !important; }
  .o_xs-right {
    text-align: left !important; }
  table.o_xs-left {
    margin-left: 0 !important;
    margin-right: auto !important;
    float: none !important; }
  table.o_xs-right {
    margin-left: auto !important;
    margin-right: 0 !important;
    float: none !important; }
  table.o_xs-center {
    margin-left: auto !important;
    margin-right: auto !important;
    float: none !important; }
  h1.o_heading {
    font-size: 32px !important;
    line-height: 41px !important; }
  h2.o_heading {
    font-size: 26px !important;
    line-height: 37px !important; }
  h3.o_heading {
    font-size: 20px !important;
    line-height: 30px !important; }
  .o_xs-py-md {
    padding-top: 24px !important;
    padding-bottom: 24px !important; }
  .o_xs-pt-xs {
    padding-top: 8px !important; }
  .o_xs-pb-xs {
    padding-bottom: 8px !important; } }
@media screen {
  @font-face {
    font-family: \'Roboto\';
    font-style: normal;
    font-weight: 400;
    src: local("Roboto"), local("Roboto-Regular"), url(https://fonts.gstatic.com/s/roboto/v18/KFOmCnqEu92Fr1Mu7GxKOzY.woff2) format("woff2");
    unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF; }
  @font-face {
    font-family: \'Roboto\';
    font-style: normal;
    font-weight: 400;
    src: local("Roboto"), local("Roboto-Regular"), url(https://fonts.gstatic.com/s/roboto/v18/KFOmCnqEu92Fr1Mu4mxK.woff2) format("woff2");
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
  @font-face {
    font-family: \'Roboto\';
    font-style: normal;
    font-weight: 700;
    src: local("Roboto Bold"), local("Roboto-Bold"), url(https://fonts.gstatic.com/s/roboto/v18/KFOlCnqEu92Fr1MmWUlfChc4EsA.woff2) format("woff2");
    unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF; }
  @font-face {
    font-family: \'Roboto\';
    font-style: normal;
    font-weight: 700;
    src: local("Roboto Bold"), local("Roboto-Bold"), url(https://fonts.gstatic.com/s/roboto/v18/KFOlCnqEu92Fr1MmWUlfBBc4.woff2) format("woff2");
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
  .o_sans,
  .o_heading {
    font-family: "Roboto", sans-serif !important; }
  .o_heading,
  strong,
  b {
    font-weight: 700 !important; }
  a[x-apple-data-detectors] {
    color: inherit !important;
    text-decoration: none !important; } }
</style>
</head>
<body class="o_body o_bg-white">
<table width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
  <tbody>
    <tr>
      <td class="o_bg-light o_px-xs o_pt-lg o_xs-pt-xs" align="center">
        <table class="o_block-xs" width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
          <tbody>
            <tr>
              <td class="o_bg-white o_px o_py-md o_br-t o_sans o_text" align="center"><p><a class="o_text-primary" href="'.SERVER_ROOT.'"><img src="'.SERVER_ROOT.'/mail_templates/images/logo.png" width="136" height="36" alt="MDRC" style="max-width: 136px;"></a></p></td>
            </tr>
          </tbody>
        </table>
     </td>
    </tr>
  </tbody>
</table>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
      <tbody>
        <tr>
          <td class="o_bg-light o_px-xs" align="center">
            <table class="o_block-xs" width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
              <tbody>
                <tr>
                  <td class="o_bg-primary o_px-md o_py-xl o_xs-py-md o_sans o_text-md o_text-white" align="center">
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>';
return $html;
}

function web_mail_footer()
{
	$obj_model_table=$this->app->load_model("generel_settings");
	$rs_data=$obj_model_table->execute("SELECT",false,"","");
	$app_url=$rs_data[0]['app_url'];
	$app_url_iphone=$rs_data[0]['app_url_iphone'];
	$support_email=$rs_data[0]['contact_email'];
	$website=$rs_data[0]['website'];
	$facebook=$rs_data[0]['facebook_link'];
	$twitter=$rs_data[0]['twitter_link'];
	$linkedin=$rs_data[0]['linkedin_link'];
	$instagram=$rs_data[0]['instagram_link'];

	$logo=$rs_data[0]['logo'];
	$logourl=SERVER_ROOT.'/uploads/project_image/351670222225.png';

 $html='
<table width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
  <tbody>
    <tr>
      <td class="o_bg-light o_px-xs o_pb-lg o_xs-pb-xs" align="center">
          
        <table class="o_block-xs" width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
          <tbody>
            <tr>
              <td class="o_re o_bg-white o_px o_pb-lg o_bt-light" align="center">
                <div class="o_col o_col-1 o_col-full">
                  <div style="font-size: 32px; line-height: 32px; height: 32px;">&nbsp; </div>
                  <div class="o_px-xs o_sans o_text-xs o_center">
                    <p></p>
                  </div>
                </div>
                <div class="o_col o_col-2 o_col-full">
                  <div style="font-size: 24px; line-height: 24px; height: 24px;">&nbsp; </div>
                  <div class="o_px-xs o_sans o_text-xs o_center">
                    <p>
                    <a class="o_text-light" href="'.$facebook.'"><img src="'.SERVER_ROOT.'/mail_templates/images/facebook-light.png" width="30" height="30" alt="facebook" style="max-width: 30px;"></a>
                    <a class="o_text-light" href="'.$twitter.'"><img src="'.SERVER_ROOT.'/mail_templates/images/twitter-light.png" width="30" height="30" alt="twitter" style="max-width: 30px;"></a>
                     <a class="o_text-light" href="'.$instagram.'"><img src="'.SERVER_ROOT.'/mail_templates/images/instagram-light.png" width="30" height="30" alt="instagram" style="max-width: 30px;"></a>
                  </div>
                </div>
                <div class="o_col o_col-1 o_col-full">
                  <div style="font-size: 30px; line-height: 30px; height: 30px;">&nbsp; </div>
                  <div class="o_px-xs o_sans o_text-xs o_center">
                    <p></p>
                  </div>
                </div>
               </td>
            </tr>
            <tr>
              <td class="o_bg-white o_px-md o_pb-lg o_br-b o_sans o_text-xs o_text-light" align="center">
                <p class="o_mb-xs">2023 All right reserved. Modern Diagnostic & Research Centre Pvt. Ltd.</p></td>
            </tr>
          </tbody>
        </table>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
      <tbody>
        <tr>
          <td class="o_bg-light o_px-xs" align="center">
            <table class="o_block-xs" width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
              <tbody>
                <tr>
                  <td class="o_bg-primary o_px-md o_py-xl o_xs-py-md o_sans o_text-md o_text-white" align="center">
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
        <div class="o_hide-xs" style="font-size: 64px; line-height: 64px; height: 64px;">&nbsp; </div></td>
    </tr>
  </tbody>
</table>
</body>
</html>
';
return $html;
}





function excel_cat_name($product_id)
	{
	$category=$this->app->load_model("category");
	$category->join_table("product_category","left", array("category_id"), array("id"=>"category_id"));
	$rs_category=$category->execute("SELECT",false,"","product_category.product_id=".$product_id."");
			$name='';
			for($i=0;$i<count($rs_category);$i++)
			{
				if($i==count($rs_category)-1)
				{
					$name.=$rs_category[$i]['category_name'];
				}
				else
				{
				$name.=$rs_category[$i]['category_name'].',';
				}
			}
			return $name;
		}
			function resize_multi_image_new($uploadedfile_name,$uploadedfile_tmpname,$image_user_config,$user_width1,$user_width2,$user_width3)
{
			$errors=0;
		//$image =$_FILES["file"]["name"];
			$uploadedfile = $uploadedfile_tmpname;
			$file_name = basename($uploadedfile_name);
    		$file_info = $this->get_file_info($file_name);
			if(strtoupper($file_info->extension)=="JPG" || strtoupper($file_info->extension)=="JPEG" || strtoupper($file_info->extension)=="GIF"  || strtoupper($file_info->extension)=="PNG"){
			$new_name =$file_name;
			//echo $new_name; exit;
					}
 			if ($new_name)
 			{
 			$filename = stripslashes($uploadedfile_name);
 	 		$i = strrpos($filename,".");
        	 if (!$i) { return ""; }
         	 $l = strlen($filename) - $i;
         	 $ext = substr($filename,$i+1,$l);
			$extension = $ext;
 			$extension = strtolower($extension);
 			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
 			{
 			$change='<div class="msgdiv">Unknown Image extension </div> ';
 			$errors=1;
 			}
 			else
 			{
 			$size=filesize($uploadedfile_tmpname);
			if($extension=="jpg" || $extension=="jpeg" )
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefromjpeg($uploadedfile);
			}
			else if($extension=="png")
			{
			$uploadedfile = $uploadedfile_tmpname;
			$src = imagecreatefrompng($uploadedfile);
			}
			else
			{
			$src = imagecreatefromgif($uploadedfile);
			}
			echo $scr;
			list($width,$height)=getimagesize($uploadedfile);
			if($width>$user_width1)
					{
					$newwidth=$user_width1;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					else
					{
					$newwidth=$width;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					}
					if($width>$user_width2)
					{
					$newwidth1=$user_width2;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					}
					else
					{
					$newwidth1=$width;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					}
					if($width>$user_width3)
					{
					$newwidth2=$user_width3;
					$newheight2=($height/$width)*$newwidth2;
					$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
					}
					else
					{
					$newwidth2=$width;
					$newheight2=($height/$width)*$newwidth2;
					$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
					}
			imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
    		imagealphablending($tmp, false);
    		imagesavealpha($tmp, true);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			imagecolortransparent($tmp1, imagecolorallocatealpha($tmp1, 0, 0, 0, 127));
    		imagealphablending($tmp1, false);
    		imagesavealpha($tmp1, true);
			imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
			imagecolortransparent($tmp2, imagecolorallocatealpha($tmp2, 0, 0, 0, 127));
    		imagealphablending($tmp2, false);
    		imagesavealpha($tmp2, true);
			imagecopyresampled($tmp2,$src,0,0,0,0,$newwidth2,$newheight2,$width,$height);
			$filename = $image_user_config.$new_name;
			$filename1 = $image_user_config."mediumthumb".$new_name;
			$filename2 = $image_user_config."thumb".$new_name;
			if($extension=="jpg" || $extension=="jpeg" )
			{
			imagejpeg($tmp,$filename,100);
			imagejpeg($tmp1,$filename1,100);
			imagejpeg($tmp2,$filename2,100);
			}
			else if($extension=="png")
			{
			imagepng($tmp,$filename);
			imagepng($tmp1,$filename1);
			imagepng($tmp2,$filename2);
			}
			else
			{
			imagepng($tmp,$filename,100);
			imagepng($tmp1,$filename1,100);
			imagepng($tmp2,$filename2,100);
			}
			imagedestroy($src);
			imagedestroy($tmp);
			imagedestroy($tmp1);
			imagedestroy($tmp2);
}
}
return $new_name;
}
	function image_string()
{
	$rand_val = date('YMDHIS') . rand(11111, 99999);
    return  md5($rand_val);
}


	function order_status_html($status)
	{
				if($status=='Unpaid')
				{
					$ostatus='<span class="label label-info">Pending</span>';
				}
				elseif($status=='Paid')
				{
					$ostatus='<span class="label label-success">Confirmed</span>';
				}
				elseif($status=='Canceled')
				{
					$ostatus='<span class="label label-warning">Canceled</span>';
				}
				elseif($status=='On Delivery')
				{
					$ostatus='<span class="label label-blue">Dispatched</span>';
				}
				elseif($status=='Delivered')
				{
					$ostatus='<span class="label label-blue">Delivered</span>';
				}
				elseif($status=='Tracking Order')
				{
					$ostatus='<span class="label label-blue" style="background:#00BCD4;color:#fff">Tracking Order</span>';
				}
				elseif($status=='Delay')
				{
					$ostatus='<span class="label label-blue" style="background:#000;color:#fff">Delay</span>';
				}
				else
				{
					$ostatus='<span class="label label-blue" style="background:#000;color:#fff">'.$status.'</span>';
				}
				return $ostatus;
	}


function preview_excel($inputFileName,$table_class=NULL)
	{
		try {
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
   		 die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		//Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		//  Loop through each row of the worksheet in turn
		$html='<table class="'.$table_class.'">';
		for ($row = 1; $row <= $highestRow; $row++){
    	 $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                    NULL,
                                    TRUE,
                                    FALSE);
		$html.='<tr>';
        for ($col = 0; $col < $highestColumnIndex; ++ $col) {
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            $val = $cell->getValue();
            $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
           // echo '<td>' . $val . '<br>(Typ ' . $dataType . ')</td>';
			  $html.= '<td>' . $val . '<br></td>';
        }
        $html.='</tr>';
		}
		$html.='</table>';
		return $html;
	}
		function export_excel($ExeclHeads,$data_array,$fields,$filename,$array_field)
{
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	//start of printing column names as names of MySQL fields
	$column = 'A';
	for ($i = 0; $i < count($ExeclHeads); $i++)
	{
		$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $ExeclHeads[$i]);
		$column++;
	}
  //end of adding column names
  //start while loop to get data
  $rowCount = 2;
  foreach($data_array as $row)
 {
		$column = 'A';
		for($j=0; $j<count($fields);$j++)
		{
			if(!isset($row[$fields[$j]]))
				$value = NULL;
			elseif ($row[$fields[$j]] != "")
				$value = strip_tags($row[$fields[$j]]);
			else
				$value = "";
			$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
				foreach($array_field as $ddd => $dddvalue)
				{
				if($ddd==$fields[$j])
				{
					//$pr_title=$dddvalue['prompt_title'];
					//$pr_prompt=$dddvalue['prompt'];
					//$pr_options=$dddvalue['options'];
					$objValidation3 = $objPHPExcel->getActiveSheet()->getCell($column . $rowCount)->getDataValidation();
					$objValidation3->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
					$objValidation3->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
					$objValidation3->setAllowBlank(false);
					$objValidation3->setShowInputMessage(true);
					$objValidation3->setShowDropDown(true);
					//$objValidation3->setPromptTitle($pr_title);
					//$objValidation3->setPrompt($pr_prompt);
					$objValidation3->setErrorTitle('Input error');
					$objValidation3->setError('Value is not in list');
					//$objValidation3->setFormula1('"'.$pr_options.'"');
				}
			  }
			$column++;
		}
		$rowCount++;
		}
		header('Content-Type: application/vnd.ms-excel; charset=utf-8');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		ob_start();
		$objWriter->save('php://output');
}

// New Added Function 2021 BY Rahul
	function o_status_html2020($order_status)
	{
		if($order_status=='Pending')
		{
			$class="badge-secondary";
		}
		else if($order_status=='Confirmed')
		{
			$class="badge-primary";
		}
		else if($order_status=='Packed')
		{
			$class="badge-info";
		}
		else if($order_status=='Dispatched')
		{
			$class="badge-warning";
		}
		else if($order_status=='Delivered')
		{
			$class="badge-success";
		}
		else if($order_status=='Return')
		{
			$class="badge-danger";
		}
		else if($order_status=='Canceled')
		{
			$class="badge-danger";
		}
		else
		{
			$class="badge-dark";
		}
		$order_status='<span class="badge '.$class.'" >'.$order_status.'</span>';
		return $order_status;
	}
	function sort_order($table_name)
	{
		$obj_table =$this->app->load_model($table_name);
		$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name."  where id!=0 and status!='Trash'");
		$totalRecords = $result[0]['allcount'];
		$records = array();
		for($i=1;$i<=$totalRecords+1;$i++)
		{
			$records[$i] = $i;
		}
		 return $records;
	}
	function sort_order_count($table_name)
	{
		$obj_table =$this->app->load_model($table_name);
		$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name."  where id!=0 and status!='Trash'");
		$totalRecords = $result[0]['allcount'];
		return $totalRecords+1;
	}

	function user_status($user_status)
	{
		if($user_status=='Yes')
		{
			$user_status='<span class="badge badge-warning">Yes</span>';
		}
		else
		{
			$user_status='<span class="badge badge-danger">No</span>';
		}
		return $user_status;
	}

	function user_registered_with($registered_with)
	{
		if($registered_with=='website')
		{
			$user_status='<span class="badge badge-warning">Website</span>';
		}
		else if($registered_with=='facebook' || $registered_with=='facebook_app')
		{
			$user_status='<span class="badge badge-secondary">Facebook</span>';
		}
		else if($registered_with=='google' || $registered_with=='google_app')
		{
			$user_status='<span class="badge badge-success">Google</span>';
		}
		else if($registered_with=='iphone')
		{
			$user_status='<span class="badge badge-info">Iphone</span>';
		}
		else if($registered_with=='android_app')
		{
			$user_status='<span class="badge badge-dark">Android</span>';
		}
		else
		{
			$user_status='<span class="badge badge-light">Both</span>';
		}
		return $user_status;
	}
	function product_cat_names($product_id)
	{
		if($product_id>0)
		{
			$obj_model_tble = $this->app->load_model("product_category");
			$obj_model_tble->join_table("category", "left", array("name"), array("category_id"=>"id"));
			$data=$obj_model_tble->execute("SELECT",false,"","product_id='".$product_id."'");
			if(count($data)>0)
			{
				$ccat_array=array();
				for($i=0;$i<count($data);$i++)
				{
				$ccat_array[]=$data[$i]['category_name'];
				}
				$cats=implode(',',$ccat_array);
				return $cats;
			}
			else
			{
				return '';
			}
		}
		else
		{
			return '';
		}
	}
	function cat_listmenu($pid = 0,$act,$product_id)
	{
		if($act=='edit')
		{
		$obj_model_category=$this->app->load_model('category');
		$rs_cat=$obj_model_category->execute("SELECT",false,"SELECT id,name,parentcategory_id FROM category WHERE status='Active' and parentcategory_id='$pid'","");
		$i=0;
		foreach($rs_cat as $cat)
		{
			$obj_model_product_category=$this->app->load_model('category_group_ids');
			$rs_product_cat=$obj_model_product_category->execute("SELECT",false,"","group_id=".$product_id." and category_id=".$cat['id']."");
			if($i%2==0)
			{
			echo '<div class="even">';
			}
			else
			{
			echo '<div class="odd">';
			}
			if($rs_product_cat[0]["category_id"]==$cat['id'])
			{
				$checked='checked="checked"';
			}
			else
			{
				$checked='';
			}
		print' <label class="csscheckbox csscheckbox-primary">
		<input class="csscheckbox csscheckbox-default" type="checkbox" '.$checked.' name="product_category[]" value="'.$cat['id'].'"> <span></span>&nbsp;&nbsp;&nbsp;'.$cat['name'].' </label>';
				if($this->countsubcat($cat['id'])>0)
					{
						echo'<div class="subs">';
						$this->cat_listmenu($cat['id'],$act,$product_id);
						echo'</div>';
					}
					echo '</div>';
				}
			}
			else
			{
			$obj_model_category=$this->app->load_model('category');
			$rs_cat=$obj_model_category->execute("SELECT",false,"SELECT id,name,parentcategory_id FROM category WHERE status='Active' and parentcategory_id='$pid'","");
			$i=0;
			foreach($rs_cat as $cat)
			{
				if($i%2==0)
				{
				echo '<div class="even">';
				}
				else
				{
				echo '<div class="odd">';
				}
		print' <label class="csscheckbox csscheckbox-primary">
		<input type="checkbox" name="product_category[]" value="'.$cat['id'].'"><span></span>&nbsp;&nbsp;&nbsp;'.$cat['name'].' </label>';
			 if($this->countsubcat($cat['id'])>0)
			 {
				echo'<div class="subs">';
				$this->cat_listmenu($cat['id'],'','');
				echo'</div>';
			 }
				echo '</div>';
			 }
		   }
		$i++;
	}

	function get_image_path($image_name,$folder,$type)
	{
			if($image_name!="" && file_exists(ABS_PATH."/uploads/".$folder."/".$image_name))
			{
				$large_image= SERVER_ROOT."/uploads/".$folder."/".$image_name;
				$medium_image= SERVER_ROOT."/uploads/".$folder."/".'mediumthumb'.$image_name;
				$thumb_image= SERVER_ROOT."/uploads/".$folder."/".'thumb'.$image_name;
			}
			else
			{
				$large_image=SERVER_ROOT.'/uploads/default.png';
				$medium_image=SERVER_ROOT.'/uploads/default.png';
				$thumb_image=SERVER_ROOT.'/uploads/default.png';
			}
			if($type=='')
			{
				$data=array();
				$data['large_image']=$large_image;
				$data['medium_image']=$medium_image;
				$data['thumb_image']=$thumb_image;
			}
			else
			{
				if($type=='large')
				{
					$data=$large_image;
				}
				else if($type=='medium')
				{
					$data=$medium_image;
				}
				else if($type=='thumb')
				{
					$data=$thumb_image;
				}
				else
				{
					$data='';
				}
			}
			return $data;
	}

	function get_image_url($image_name,$folder,$type)
	{
			if($image_name!="" && file_exists(ABS_PATH."/uploads/".$folder."/".$image_name))
			{
				$large_image= SERVER_ROOT."/uploads/".$folder."/".$image_name;
				$medium_image= SERVER_ROOT."/uploads/".$folder."/".'mediumthumb'.$image_name;
				$thumb_image= SERVER_ROOT."/uploads/".$folder."/".'thumb'.$image_name;
			}
			else
			{
				$large_image='';
				$medium_image='';
				$thumb_image='';
			}
			if($type=='')
			{
				$data=array();
				$data['large_image']=$large_image;
				$data['medium_image']=$medium_image;
				$data['thumb_image']=$thumb_image;
			}
			else
			{
				if($type=='large')
				{
					$data=$large_image;
				}
				else if($type=='medium')
				{
					$data=$medium_image;
				}
				else if($type=='thumb')
				{
					$data=$thumb_image;
				}
				else
				{
					$data='';
				}
			}
			return $data;
	}

	function set_message2021($message, $type){
		$_SESSION['msg'] = $message;
		$_SESSION['type'] = $type;
	}
	function get_message2021()
	{
		if(VIR_DIR=="admin/")
		{
			if(isset($_SESSION['msg']) && isset($_SESSION['type'])){
					if($_SESSION['type']=='SUCCESS'){
						$message =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
									  <i class="fa fa-check mg-r-10"></i> <strong>SUCCESS </strong> '.$_SESSION['msg'].'
									  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">×</span>
									  </button>
									</div>';
					}else if($_SESSION['type']=='ERROR'){
						$message =  '<div class="alert alert-error alert-dismissible fade show" role="alert">
									  <i class="fa fa-close mg-r-10"></i> <strong>ERROR </strong> '.$_SESSION['msg'].'
									  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">×</span>
									  </button>
									</div>';
					}else if($_SESSION['type']=='MESSAGE'){
						$message =  '<div class="alert alert-warning alert-dismissible fade show" role="alert">
									  <i class="fa fa-bullhorn mg-r-10"></i> <strong>Information </strong> '.$_SESSION['msg'].'
									  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">×</span>
									  </button>
									</div>';
					}
					unset($_SESSION['msg']);
					unset($_SESSION['type']);
					return $message;
				}
		}
		else
		{
		if(isset($_SESSION['msg']) && isset($_SESSION['type']))
		{
			if($_SESSION['type']=='SUCCESS')
			{
				if(VIR_DIR!="")
				{
				$message = '<div class="alert alert-success">
					 <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
					<strong>Success!</strong> '.$_SESSION['msg'].'
				</div>';
				}
				else
				{
				$message =  '<div class="col-sm-12">
				<div class="alert_box r_corners color_green success m_bottom_10">
							<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
							<i class="fa fa-smile-o"></i><p>'.$_SESSION['msg'].' </p>
							</div></div>
				';
				}
			}
			else if($_SESSION['type']=='ERROR')
			{
				if(VIR_DIR!="")
				{
				$message =  '<div class="alert alert-error">
					 <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
					 <strong>Error!</strong> '.$_SESSION['msg'].'
				</div>';
				}
				else
				{
				$message ='
				<div class="col-sm-12">
				<div class="alert_box r_corners error ">
							<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
							<i class="fa fa-exclamation-triangle"></i><p>'.$_SESSION['msg'].'</p>
							</div></div>
							';
				}
			}
			else if($_SESSION['type']=='MESSAGE'){
				$message =  '<div class="col-sm-12"><div class="alert_box r_corners warning m_bottom_10">
					 	<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
						<i class="fa fa-exclamation-triangle"></i><p>'.$_SESSION['msg'].'</p>
						</div></div>';
			}
			unset($_SESSION['msg']);
			unset($_SESSION['type']);
			return $message;
		}
		}
	}

	function numerdisplayformate($number) {
		$num = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $number);
		return $num;
	}

	function resize_multi_image_2020($uploadedfile_name,$uploadedfile_tmpname,$image_user_config,$user_width1,$user_width2,$user_width3)
	{
				$errors=0;
			//$image =$_FILES["file"]["name"];
				$uploadedfile = $uploadedfile_tmpname;
				$file_name = basename($uploadedfile_name);
				$file_info = $this->get_file_info($file_name);
				if(strtoupper($file_info->extension)=="JPG" || strtoupper($file_info->extension)=="JPEG" || strtoupper($file_info->extension)=="GIF"  || strtoupper($file_info->extension)=="PNG"){
				$new_name = rand(1234,999999).$this->image_string().".".$file_info->extension;
						}
				if ($new_name)
				{
				$filename = stripslashes($uploadedfile_name);
				$i = strrpos($filename,".");
				 if (!$i) { return ""; }
				 $l = strlen($filename) - $i;
				 $ext = substr($filename,$i+1,$l);
				$extension = $ext;
				$extension = strtolower($extension);
				if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
				{
				$change='<div class="msgdiv">Unknown Image extension </div> ';
				$errors=1;
				}
				else
				{
				$size=filesize($uploadedfile_tmpname);
				if($extension=="jpg" || $extension=="jpeg" )
				{
				$uploadedfile = $uploadedfile_tmpname;
				$src = imagecreatefromjpeg($uploadedfile);
				}
				else if($extension=="png")
				{
				$uploadedfile = $uploadedfile_tmpname;
				$src = imagecreatefrompng($uploadedfile);
				}
				else
				{
				$src = imagecreatefromgif($uploadedfile);
				}
				echo $scr;
				list($width,$height)=getimagesize($uploadedfile);
				if($width>$user_width1)
						{
						$newwidth=$user_width1;
						$newheight=($height/$width)*$newwidth;
						$tmp=imagecreatetruecolor($newwidth,$newheight);
						}
						else
						{
						$newwidth=$width;
						$newheight=($height/$width)*$newwidth;
						$tmp=imagecreatetruecolor($newwidth,$newheight);
						}
						if($width>$user_width2)
						{
						$newwidth1=$user_width2;
						$newheight1=($height/$width)*$newwidth1;
						$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
						}
						else
						{
						$newwidth1=$width;
						$newheight1=($height/$width)*$newwidth1;
						$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
						}
						if($width>$user_width3)
						{
						$newwidth2=$user_width3;
						$newheight2=($height/$width)*$newwidth2;
						$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
						}
						else
						{
						$newwidth2=$width;
						$newheight2=($height/$width)*$newwidth2;
						$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
						}
				imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
				imagealphablending($tmp, false);
				imagesavealpha($tmp, true);
				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
				imagecolortransparent($tmp1, imagecolorallocatealpha($tmp1, 0, 0, 0, 127));
				imagealphablending($tmp1, false);
				imagesavealpha($tmp1, true);
				imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
				imagecolortransparent($tmp2, imagecolorallocatealpha($tmp2, 0, 0, 0, 127));
				imagealphablending($tmp2, false);
				imagesavealpha($tmp2, true);
				imagecopyresampled($tmp2,$src,0,0,0,0,$newwidth2,$newheight2,$width,$height);
				$filename = $image_user_config.$new_name;
				$filename1 = $image_user_config."mediumthumb".$new_name;
				$filename2 = $image_user_config."thumb".$new_name;
				if($extension=="jpg" || $extension=="jpeg" )
				{
				imagejpeg($tmp,$filename,90);
				imagejpeg($tmp1,$filename1,90);
				imagejpeg($tmp2,$filename2,90);
				}
				else if($extension=="png")
				{
				imagepng($tmp,$filename);
				imagepng($tmp1,$filename1);
				imagepng($tmp2,$filename2);
				}
				else
				{
				imagepng($tmp,$filename,90);
				imagepng($tmp1,$filename1,90);
				imagepng($tmp2,$filename2,90);
				}
				imagedestroy($src);
				imagedestroy($tmp);
				imagedestroy($tmp1);
				imagedestroy($tmp2);
			}
		}
		return $new_name;
	}

	function moneyFormatIndia($number) {
		$num = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $number);
		$num=str_replace(".00","",$num);
		return '<i class="fas fa-rupee-sign"></i> '.$num;
	}

	function GetStateName($state_id)
	{
		if($state_id>0)
		{
			$obj_model_state = $this->app->load_model("state");
			$rs_state = $obj_model_state->execute("SELECT",false,"","id=".$state_id."","id DESC limit 0,1");
			return $rs_state[0]['name'];
		}
	}

	function FileUpload($data=[])
	{
		$filename=$data['filename'];
		$filetmpname=$data['filetmpname'];
		$folder=$data['folder'];
		$filerename   = time()."_".mt_rand(1000, 2000);
		$extension  = pathinfo($filename,PATHINFO_EXTENSION);
		$basename   = $filerename.".".$extension;
		$source       = $filetmpname;
		$destination  = "../uploads/".$folder."/".$basename;
		/* move the file */
		move_uploaded_file($source,$destination);
		return $basename;
	}

	function getTaskStatusHtml($status)
	{
		if($status=='Active')
		{
			$status='<span class="badge badge-primary">Active</span>';
		}
		else if($status=='Inprogress')
		{
			$status='<span class="badge badge-warning">Inprogress</span>';
		}
		else if($status=='Completed')
		{
			$status='<span class="badge badge-success">Completed</span>';
		}
		else
		{
			$status='<span class="badge badge-primary">'.$status.'</span>';
		}
		return $status;
	}

	function get_employee_reporting($employeeId)
	{
		$obj_model_employee = $this->app->load_model("employee");
		$employee = $obj_model_employee->execute("SELECT",false,"","lms_employee_id=".$employeeId."","id DESC limit 0,1");
		return $employee[0];
	}

	function get_client_clientStatus($data){
		$created_at=date('d-m-Y h:i A', strtotime($data['created_at']));
		if($data['clientStatus']=='Field Visit') {
			$badge='<span class="badge badge-warning">Field Visit</span>';
		} else if($data['clientStatus']=='Request for Client') {
			$badge='<span class="badge badge-danger">Request for Client</span>';
		} else {
			$badge='<span class="badge badge-primary">Client</span>';
		}
		return ["badge"=>$badge,"created_at"=>$created_at];
	}

	function get_employee_sample_pickup_status($data){
		
		if($data['status']=='Pending') {
			$badge='<span class="badge badge-warning ml-1">Pending</span>';
		} else if($data['status']=='In Progress') {
			$badge='<span class="badge badge-danger ml-1">In Progress</span>';
		} else {
			$badge='<span class="badge badge-success ml-1">'.$data['status'].'</span>';
		}
		return ["badge"=>$badge];
	}

	function get_employee_leave_status($data){
		
		if($data['status']=='Pending') {
			$badge='<span class="badge badge-warning ml-1">Pending</span>';
		} else if($data['status']=='Reject') {
			$badge='<span class="badge badge-danger ml-1">Reject</span>';
		} else {
			$badge='<span class="badge badge-success ml-1">'.$data['status'].'</span>';
		}
		return ["badge"=>$badge];
	}

	function get_client_logistic_assign_status($data){
		
		if($data['status']=='Pending') {
			$badge='<span class="badge badge-warning ml-1">Pending</span>';
		} else if($data['status']=='In Progress') {
			$badge='<span class="badge badge-danger ml-1">Accept</span>';
		} else {
			$badge='<span class="badge badge-success ml-1">'.$data['status'].'</span>';
		}
		return ["badge"=>$badge];
	}

	function get_employee_sample_dispatch_status($data){
		
		if($data['status']=='Dispatched') {
			$badge='<span class="badge badge-warning ml-1">Dispatched</span>';
		} else if($data['status']=='Trash') {
			$badge='<span class="badge badge-danger ml-1">Trash</span>';
		} else {
			$badge='<span class="badge badge-success ml-1">'.$data['status'].'</span>';
		}
		return ["badge"=>$badge];
	}

	function getDistance($lat1, $lon1, $lat2, $lon2, $unit='K'){
		if($lat1=='' || $lat2=='')
		{
			return 0;
		}

		if (($lat1 == $lat2) && ($lon1 == $lon2)) 
		{
			return 0;
		}
		else 
		{
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			$unit = strtoupper($unit);
		
			if ($unit == "K") {
				return number_format(($miles * 1.609344), 4, '.', ' ').' KM';
			} else if ($unit == "N") {
				return ($miles * 0.8684);
			} else {
				return number_format($miles, 4, ',', ' ');
			}
		}
	}

	function getTimeDiff($data) {
		if($data['startTime']!=''){
		$start_date = $data['startTime'];
		$end_date =  $data['endTime'];

		$start_datetime = new DateTime($start_date);
		$end_datetime = new DateTime($end_date);

		$diff = $start_datetime->diff($end_datetime);

		$minutes = $diff->days * 24 * 60;
		$minutes += $diff->h * 60;
		$minutes += $diff->i;

		return $minutes." Min";
		}else {
			return "";
		}
	}


	function send_push($data)
	{
		// Android App
		$google_key='AAAABxpd1d0:APA91bG6IKvZzI_JMT63mNmzRoAVyMsd44Pn7wEGSg3rNW5kyThvby0YlWWmrZ7Un-wclu_Uln1UB_40txyuORIn79-aXdRdbFp98Qdbs-5xpxEu9F2-1ueY5o4_rSSZr9lcAfodbe23';
		$obj_model = $this->app->load_model("employee");
		$employee=$obj_model->execute("SELECT", false, "", "fcm_token!='' and FIND_IN_SET(id,'".$data['employee_ids']."')");
		if(count($employee)>0)
		{
			$to = array();
			foreach($employee as $item)
			{
				array_push($to, $item['fcm_token']);
			}

			$path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
			$fields = array(
				'registration_ids' => $to,
				'data' => $data['notification']
			);
			$headers = array(
				'Authorization:key=' . $google_key,
				'Content-Type:application/json'
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);
			curl_close($ch);
		}
		return '';
	}

	function getTopEmployee($reporting_employee_lms_id, $level = 1, &$result = []){
		
		if ($level > 5) {
			return $this->removeDuplicatesByColumn($result,'lms_employee_code');
		}

		$obj_model_employee = $this->app->load_model("employee");
		$res_employee=$obj_model_employee->execute("SELECT", false, "","status='Active' and lms_employee_id='".$reporting_employee_lms_id."'","");
		
		if(count($res_employee)>0){
			foreach($res_employee as $employee) {
				
					$result[] = [
						'id' => $employee['id'],
						'lms_employee_id' => $employee['lms_employee_id'],
						'level' => $level,
						'name' => $employee['name'],
						'mobile' => $employee['mobile'],
						'lms_employee_code' => $employee['lms_employee_code']
					];
				
					$this->getTopEmployee($employee['reporting_employee_lms_id'], $level + 1, $result);
				
			}
		}
		return $this->removeDuplicatesByColumn($result,'lms_employee_code');
	}

	function removeDuplicatesByColumn($array, $column) {
		$uniqueValues = [];
		$filteredArray = [];
	
		foreach ($array as $item) {
			// Check if the value in the specific column has already been added
			if (!in_array($item[$column], $uniqueValues)) {
				$uniqueValues[] = $item[$column]; // Add the value to the uniqueValues array
				$filteredArray[] = $item; // Add the row to the filteredArray
			}
		}
	
		return $filteredArray;
	}	

	function getSubEmployee($lms_employee_id, $level = 1, &$result = []){
		
		if ($level > 5) {
			return $result;
		}

		$obj_model_employee = $this->app->load_model("employee");
		$obj_model_employee->join_table("master_designation", "left", array('name'), array("master_designation_id"=>"id"));
		$res_employee=$obj_model_employee->execute("SELECT", false, "","employee.status='Active' and employee.lms_employee_id!='".$lms_employee_id."'  and employee.reporting_employee_lms_id ='".$lms_employee_id."'","");
		
		if(count($res_employee)>0){
			foreach($res_employee as $employee) {
				$result[] = [
					'id' => $employee['id'],
					'lms_employee_id' => $employee['lms_employee_id'],
					'level' => $level,
					'name' => $employee['name'],
					'mobile' => $employee['mobile'],
					'lms_employee_code' => $employee['lms_employee_code'],
					'designation' => $employee['master_designation_name'],
				];
				$this->getSubEmployee($employee['lms_employee_id'], $level + 1, $result);
			}
		}
		return $result;
	}

	function getSubEmployeeAll($lms_employee_id, $level = 1, &$result = []){
		
		if ($level > 5) {
			return $result;
		}
		$children=[];
		$obj_model_employee = $this->app->load_model("employee");
		$obj_model_employee->join_table("master_designation", "left", array('name'), array("master_designation_id"=>"id"));
		$res_employee=$obj_model_employee->execute("SELECT", false, "","employee.status='Active' and employee.lms_employee_id!='".$lms_employee_id."'  and employee.reporting_employee_lms_id ='".$lms_employee_id."'","");
		
		$children = [];

    // Process employees if data is available
    if (!empty($res_employee)) {
        foreach ($res_employee as $employee) {
            // Recursive call to gather child nodes
            $childNodes = $this->getSubEmployeeAll($employee['lms_employee_id'], $level + 1);

            // Add employee details along with children
            $children[] = [
                'id' => $employee['id'],
                'lms_employee_id' => $employee['lms_employee_id'],
                'level' => $level,
                'name' => $employee['name'],
                'mobile' => $employee['mobile'],
				'designation' => $employee['master_designation_name'],
                'lms_employee_code' => $employee['lms_employee_code'],
                'children' => $childNodes,
            ];
        }
    }

    // Append the collected children to the result
    $result = array_merge($result, $children);

    return $children;
	}

	function getClientDetail($result=[],$lms_employee_id=null){
		if(count($result)<=0){
			//return 0;
		}
		$employeeLmsId=count($result)>0?array_column($result,'lms_employee_id'):[];

		if(!empty($lms_employee_id)){
			array_push($employeeLmsId,$lms_employee_id);
		}

		if(count($employeeLmsId)<=0){
			return 0;
		}
		
		$obj_model_employee = $this->app->load_model("client");
		$res_employee=$obj_model_employee->execute("SELECT", false,"SELECT count(id) as totalC FROM client WHERE status='Active' and lms_employee_id In(".implode(',',$employeeLmsId).")");
		
		return $res_employee[0]['totalC'];
	}

	function sendMial($data=[])
	{
		
		/*$template_name='booking_place';
		$send_data_arary=['name'=>'Test Name','order_id'=>'#0001','order_detail'=>$order_detail];
		$to_mail='thedezineapp@gmail.com';
		$subject='Your Booking has been place';
		sendMial(['template_name'=>$template_name,'send_data_arary'=>$send_data_arary,'to_mail'=>$to_mail,'subject'=>$subject]);
		*/
		$bcc_mail='pratikgandhi711@gmail.com';
		$template_name=$data['template_name'].'.html';
		$send_data_arary=$data['send_data_arary'];
		$to_mail=$data['to_mail'];
		$subject=$data['subject'];
		$mail_for=$data['mail_for'];

		if($template_name!='' && count($send_data_arary)>0)
		{
			$obj_model_gs=$this->app->load_model('generel_settings');
			$re_data=$obj_model_gs->execute("SELECT",false,"","","");

			if($mail_for=='Admin')
			{
				$to_mail=$re_data[0]['notification_email'];
				$cc_mail=$re_data[0]['notification_email_cc'];
			}

			$mail_title=$re_data[0]['project_title'];
			$mail_header=$this->web_mail_header();
			$mail_footer=$this->web_mail_footer();
			$url_image=SERVER_ROOT.'/mail_templates/images';
			$mail_data_array=['header'=>$mail_header,'footer'=>$mail_footer,'url_image'=>$url_image];

			$mail_send_data_array=array_merge($send_data_arary,$mail_data_array);

			
			$obj_mailer = $this->app->load_module("mailer\sender");
			$mail_body = $this->ParseMailTemplate($template_name,$mail_send_data_array);
			
			if($mail_body==NULL)
			{
				$this->app->display_error(NULL, "Could not parse the mail template");
			}
			$obj_mailer->create();
			$obj_mailer->subject($subject);
			$obj_mailer->add_to($to_mail);
			if($cc_mail!='')
			{
				$obj_mailer->add_cc($cc_mail);
			}
			//for paynow only
			if($data['template_name']=='direct_payment_order_admin')
			{
				$obj_mailer->add_cc('accounts@mdrcindia.com');
				$obj_mailer->add_cc('info@mdrcindia.com');
			}
			if($data['template_name']=='hrms_sync_admin')
			{
				$obj_mailer->add_to('dhruvyadav@mdrcindia.com');
				$obj_mailer->add_bcc('rohit.razobyte@gmail.com');
			}
			if($bcc_mail!='')
			{
				$obj_mailer->add_bcc($bcc_mail);
			}
			$obj_mailer->htmlbody($mail_body);						
			$flag = $obj_mailer->send();
			
			if($flag)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	function razorpay_create_order($field_list, $url)
	{
		$key=RAZOR_PAY_KEY;
		$secret=RAZOR_PAY_SECRET;
		$field_list = json_encode($field_list);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_USERPWD, "TESTkingclean:94ee0cd7d4ccec219ee97255a9d8cc6912031d01");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $field_list);
		curl_setopt($ch, CURLOPT_USERPWD, $key.':'.$secret);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($response, true);
		$razor_id = $data['id'];
		//$result=json_decode($response, true);
		return $razor_id;
	}

	function send_push_notifaction($data, $androidToken)
	{
		$notifData = [
			'title' => $data['title'] ?? '',
			'body' => $data['message'] ?? '',
			'image' => $data['image'] ?? '',
			'type' => $data['type'] ?? ''
		];
		$serviceAccountPath = ABS_PATH . '/fcm/service_account.json';
		$serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
		$now = time();
		$header = ['alg' => 'RS256', 'typ' => 'JWT'];
		$claims = [
			'iss' => $serviceAccount['client_email'],
			'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
			'aud' => 'https://oauth2.googleapis.com/token',
			'iat' => $now,
			'exp' => $now + 3600,
		];
		$base64UrlHeader = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
		$base64UrlClaims = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');
		$signatureInput = $base64UrlHeader . '.' . $base64UrlClaims;
		$privateKey = $serviceAccount['private_key'];
		$signature = '';
		openssl_sign($signatureInput, $signature, $privateKey, 'sha256');
		$base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
		$jwt = $base64UrlHeader . '.' . $base64UrlClaims . '.' . $base64UrlSignature;
		$tokenRequestData = http_build_query([
			'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
			'assertion' => $jwt,
		]);
		$ch = curl_init('https://oauth2.googleapis.com/token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $tokenRequestData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/x-www-form-urlencoded',
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		$tokenResponse = json_decode($response, true);
		if (!isset($tokenResponse['access_token'])) {
			die("Error fetching access token:\n$response");
		}
		$accessToken = $tokenResponse['access_token'];
		$fcmUrl = 'https://fcm.googleapis.com/v1/projects/' . $serviceAccount['project_id'] . '/messages:send';
		if (!empty($androidToken)) {
			$notification = [
				'message' => [
					'token' => $androidToken,
					'data' => $notifData,
					'android' => [
						'notification' => [
							'sound' => $data['sound'],
						]
					],
				],
			];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $fcmUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Authorization: Bearer ' . $accessToken,
				'Content-Type: application/json',
			]);
			$result = curl_exec($ch);
			//echo $result;exit;
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		}
	}

}
