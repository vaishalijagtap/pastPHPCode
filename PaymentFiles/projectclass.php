<?
class Project extends MainClass {
	function Project($uri) {
		$this->MainClass($uri);
	}
	//-----------------------------------------------------------------------------------------------//
	// Method for Decryption for IDs.
	//-----------------------------------------------------------------------------------------------//
	function Decode($str) {
		return (base64_decode($str));//urldecode(base64_decode(
	}
	//-----------------------------------------------------------------------------------------------//
	// Method for encryption for IDs.
	//-----------------------------------------------------------------------------------------------//
	function Encode($str) {		 
		return (base64_encode($str));//urlencode(base64_encode(
	} 
	function PagingHeader($limit,$query,$qOffset=0,$query_count="") {
		
		global $_SESSION,$_GET,$gStartPageNo;
		if($_SESSION['tot_offset']!="" && $qOffset>0 && $qOffset<=$_SESSION['tot_offset']) {
			$tot_rows=$this->Query($query,'count');
			$_SESSION['tot_rows']=$tot_rows;
			if($qOffset<=$_SESSION['tot_offset']) {
				$offset=ceil($qOffset);
				$offset1=ceil($qOffset * $limit);
				$query.=" LIMIT $offset1, $limit";
				if(($offset1+$limit)<=$_SESSION['tot_rows'])
					$_SESSION['record_no_display']=($offset1+1)." - ".($offset1+$limit)." of ".$_SESSION['tot_rows'];
				else 
					$_SESSION['record_no_display']=($offset1+1)." - ".($_SESSION['tot_rows'])." of ".$_SESSION['tot_rows'];
			} else {
				$offset=0;
				$query.=" LIMIT 0, $limit";
				if(($limit)<=$_SESSION['tot_rows'])
					$_SESSION['record_no_display']="1 - ".$limit." of ".$_SESSION['tot_rows'];
				else
					$_SESSION['record_no_display']="1 - ".$_SESSION['tot_rows']." of ".$_SESSION['tot_rows'];
			}
		} else {
			if($query_count!="")
				$tot_rows=$this->Query($query_count,'paging_count');
			else
				$tot_rows=$this->Query($query,'count');
			$pages=ceil($tot_rows/$limit);
			$_SESSION['tot_offset']=$pages;
			$_SESSION['tot_rows']=$tot_rows;
			$offset=0;
			$query.=" LIMIT 0, $limit";
			if(($limit)<=$_SESSION['tot_rows'])
				$_SESSION['record_no_display']="1 - ".$limit." of ".$tot_rows;
			else
				$_SESSION['record_no_display']="1 - ".$tot_rows." of ".$tot_rows;
		}
		$gStartPageNo=$offset1;
		return $query;
	}
	function PagingFooter($offset,$class='link_admin') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;

		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";
		if($_SESSION['tot_offset']>1) {
			$j=$offset-1;
			$k=$offset+1;
			
			$tot_offset=$_SESSION['tot_offset'];
			$display_page.="<div class=\"divContent\"><div class=\"corners\"><img src=\"".SITE_URL."images/admin/paging_curve_left.jpg\" /></div><div class=\"paging\"><select name='paging_numbers' onchange=\"javascript: window.location='".$this->MakeUrl($this->mCurrentUrl)."'+this.value;\" >";
			for($i=0;$i<$tot_offset;$i++) {
				$m=$i+1;
				if($i==$offset) $sel.= 'selected';
				else $sel="";
				$display_page.= '<option value="'.$this->Encode($gPagingExtraPara.'offset='.$i).'" '.$sel.'>' . "" . sprintf("%03d",$m) . '</option> ';  
			}
			$display_page.="</select></div>
                <div class=\"corners\"><img src=\"".SITE_URL."images/admin/paging_curve_right.jpg\" /></div></div>";
				
			$display_page .= '<div class="divContent">
            	<div class="corners"><img src="'.SITE_URL.'images/admin/paging_curve_left.jpg" /></div>
                <div class="paging">
                <ul class="paginationMenu">';
				
			
			if($offset==0)  $display_page.= '<li id="pgPrevious"><<</li>';
			else $display_page.= "<li id=\"pgPrevious\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$j)."'><strong><<</strong></a></li>";
			if($offset==($tot_offset-1)) {
				$display_page.= '<li id="pgNext">>></li>';
			} else {
				$display_page.= "<li id=\"pgNext\"> <a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$k)."'  ><strong>>></strong></a></li>";
			}
			$display_page .= '</ul>
                </div>
                <div class="corners"><img src="'.SITE_URL.'images/admin/paging_curve_right.jpg" /></div>
            </div>
            <div class="clear"></div>';
		}
		$_SESSION['tot_offset'];
		return $display_page;
	}
	function PagingFooterFront($offset,$class='link_admin') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;

		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";
		if($_SESSION['tot_offset']>1) {
			$j=$offset-1;
			$k=$offset+1;
			if($offset==0)  $display_page.= '<div class="linksOne">First</div> <div class="linksOne">Previous</div>   ';
			else $display_page.= "<div class=\"linksOne\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset=0')."' class='".$class."'><strong>First</strong></a></div> <div class=\"linksOne\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$j)."' class='".$class."'><strong>Previous</strong></a></div> ";
			$tot_offset=$_SESSION['tot_offset'];
			$display_page.="<div class=\"linksTwo\"><select name='paging_numbers' class='textarea' onchange=\"javascript: window.location='".$this->MakeUrl($this->mCurrentUrl)."'+this.value;\" >";
			for($i=0;$i<$tot_offset;$i++) {
				$m=$i+1;
				if($i==$offset) $sel.= 'selected';
				else $sel="";
				$display_page.= '<option value="'.$this->Encode($gPagingExtraPara.'offset='.$i).'" '.$sel.'>' . "<a href='$page_name?offset=$i$extra_name' class='$class'>" . sprintf("%03d",$m) . '</option> ';  
			}
			$display_page.="</select></div>";
			if($offset==($tot_offset-1)) {
				$display_page.= ' <div class="linksOne">Next</div> <div class="linksOne">Last</div>';
			} else {
				$display_page.= " <div class=\"linksOne\"> <a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$k)."' class='".$class."'><strong>Next</strong></a></div> <div class=\"linksOne\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($tot_offset-1))."' class='".$class."'><strong>Last</strong></a></div>";
			}
		}
		return $display_page;
	}
	
	function CreateThumb($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="",$thumbFix=false,$bg=false) {
		$filename = $folder . $photo . "." . $ext;
		$thumb_height=$height;
		$thumb_width=$width;
		
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);	
		
		if($ext=="gif") $image = imagecreatefromgif($filename);
		elseif($ext=="jpg") $image = imagecreatefromjpeg($filename);
		elseif($ext=="png") $image = imagecreatefrompng($filename);
		
		if($thumbFix) {
			$height = $thumb_height;
			$width = $thumb_width;
			$image_p = imagecreatetruecolor($width, $height);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		} else {
			// Set a maximum height and width
			if ($width && ($width_orig < $height_orig)) {
				$new_width = ($height / $height_orig) * $width_orig;
				$new_height=$height;
				if($new_width>$width) {
					$new_height = ($width / $width_orig) * $height_orig;
					$new_width=$width;
				}
			} else {
				$new_height = ($width / $width_orig) * $height_orig;
				$new_width=$width;
				if($new_height>$height) {
					$new_width = ($height / $height_orig) * $width_orig;
					$new_height=$height;
				}
			}
			if($new_height>$height) { $new_height=$height; }
			
			$height=$new_height;
			$width=$new_width;
			// Resample
			$image_p = imagecreatetruecolor($width, $height);
			if ($ext == 'png' || $ext == 'gif'){
				$blending = true;
				if($ext=='png')	$blending = false;
			  // allocate a color for thumbnail
				$background = imagecolorallocate($image_p, 0, 0, 0);
				// define a color as transparent
				imagecolortransparent($image_p, $background);
				// set the blending mode for thumbnail
				imagealphablending($image_p, $blending);
				// set the flag to save alpha channel
				imagesavealpha($image_p, true);
			}
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		}
		
		if($bg) {
			if($width<$thumb_width || $height<$thumb_height) {
				$im = imagecreatetruecolor($thumb_width,$thumb_height);
				/*if ($ext == 'png' || $ext == 'gif'){
					$blending = true;
					if($ext=='png')	$blending = false;
					
				  // allocate a color for thumbnail
					$background = imagecolorallocate($im, 255, 255, 255);
					// define a color as transparent
					imagecolortransparent($im, $background);
					// set the blending mode for thumbnail
					imagealphablending($im, $blending);
					// set the flag to save alpha channel
					imagesavealpha($im, true);
				}*/
				
				$background_color = imagecolorallocatealpha($im, 255, 255, 255, 75);
				//$this->imagefillalpha($im, $background_color);
				imagefill($im,0,0,$background_color);
				$temp_width	=	round($thumb_width/2) - round($width/2);
				$temp_height	=	round($thumb_height/2) - round($height/2);
				imagecopymerge($im, $image_p,$temp_width, $temp_height, 0, 0, $width, $height, 100);
				$image_p = $im;
				//$image_p = $this->image_overlap($im, $image_p);
			}
		}  
		
		if($ext=="gif") $image_thumb=imagegif($image_p, $thumbFolder .$photo . ".gif");
		elseif($ext=="jpg") $image_thumb=imagejpeg($image_p, $thumbFolder .$photo . ".jpg",100);
		elseif($ext=="png") $image_thumb=imagepng($image_p,$thumbFolder .$photo . ".png");
		imagedestroy($image_p);
		imagedestroy($im);
	}
	function ImageFillAlpha($image, $color)
    {
        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $color);
    }
	function image_overlap($background, $foreground){
	   $insertWidth = imagesx($foreground);
	   $insertHeight = imagesy($foreground);
	
	   $imageWidth = imagesx($background);
	   $imageHeight = imagesy($background);
	
	   $overlapX = $imageWidth-$insertWidth-5;
	   $overlapY = $imageHeight-$insertHeight-5;
		imagecolortransparent($foreground,	imagecolorat($foreground,0,0));
		imagecopymerge($background,$foreground,	$overlapX,$overlapY,0,0,$insertWidth,$insertHeight,100);   
		return $background;
	}
	
	function CreateThumbHeight($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		//if ($height && ($height_orig < $width_orig)) {
		//	$height = ($width / $width_orig) * $height_orig;
		//} else {
		//$width = ($height / $height_orig) * $width_orig;
		if ($width && ($width_orig < $height_orig)) {
			$new_width = ($height / $height_orig) * $width_orig;
			$new_height=$height;
		} else {
			$new_height = ($width / $width_orig) * $height_orig;
			$new_width=$width;
		}
		//echo $new_height."===".$height; die;
		if($new_height>$height) {
			$new_width = ($height / $height_orig) * $width_orig;
			$new_height=$height;
		}
		//if($new_height<=$height) 
		$height=$new_height;
		$width=$new_width;
		//}
		// Resample
		$image_p = imagecreatetruecolor($width, $height);
		if(strtolower($ext)=="gif") $image = imagecreatefromgif($filename);
		elseif($ext=="jpg") $image = imagecreatefromjpeg($filename);
		elseif($ext=="png") $image = imagecreatefrompng($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// Output
		if($ext=="gif") $image_thumb=imagegif($image_p, $thumbFolder .$photo . ".gif");
		elseif($ext=="jpg") $image_thumb=imagejpeg($image_p, $thumbFolder .$photo . ".jpg",100);
		elseif($ext=="png") $image_thumb=imagepng($image_p,$thumbFolder .$photo . ".png");
	}
	
	function CreateThumbCrop($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		if ($width && ($width_orig < $height_orig)) {
			$widthn = ($height / $height_orig) * $width_orig;
			$heightn=$height;
		} else {
			$heightn = ($width / $width_orig) * $height_orig;
			$widthn=$width;
		}
		if($widthn<$width) {
			$height_r=ceil(($width / $width_orig) * $height_orig);
			$width_r=$width;
		}elseif($heightn<$height) {
			$width_r = ceil(($height / $height_orig) * $width_orig);
			$height_r=$height;
		}
		else {
			$height_r=$heightn;
			$width_r=$widthn;
		}
		// Resample
		$image_t = imagecreatetruecolor($width_r, $height_r);
		if(strtolower($ext)=="gif") $image = imagecreatefromgif($filename);
		elseif($ext=="jpg") $image = imagecreatefromjpeg($filename);
		elseif($ext=="png") $image = imagecreatefrompng($filename);
		imagecopyresampled($image_t, $image, 0, 0, 0, 0, $width_r, $height_r, $width_orig, $height_orig);
		imagedestroy($image);
		$h_start=0;
		$w_start=0;
		if($width_r > $width) $w_start=ceil(($width_r-$width)/2); else $w_start=0;
		if($height_r > $height) $h_start=ceil(($height_r-$height)/2); else $h_start=0;
		$image_p = imagecreatetruecolor($width, $height);
		imagecopyresampled($image_p, $image_t, 0, 0, $w_start, $h_start, $width, $height, $width, $height);
		imagedestroy($image_t);
		// Output
		if($ext=="gif") $image_thumb=imagegif($image_p, $thumbFolder .$photo . ".gif");
		elseif($ext=="jpg") $image_thumb=imagejpeg($image_p, $thumbFolder .$photo . ".jpg",100);
		elseif($ext=="png") $image_thumb=imagepng($image_p,$thumbFolder .$photo . ".png");
		imagedestroy($image_p);
	}
	function CreateThumbFix($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		 
		// Resample
		$image_p = imagecreatetruecolor($width, $height);
		if(strtolower($ext)=="gif") $image = imagecreatefromgif($filename);
		elseif($ext=="jpg") $image = imagecreatefromjpeg($filename);
		elseif($ext=="png") $image = imagecreatefrompng($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// Output
		if($ext=="gif") $image_thumb=imagegif($image_p, $thumbFolder .$photo . ".gif");
		elseif($ext=="jpg") $image_thumb=imagejpeg($image_p, $thumbFolder .$photo . ".jpg",100);
		elseif($ext=="png") $image_thumb=imagepng($image_p,$thumbFolder .$photo . ".png");
	}
	
	function GetFileExt ( $imgName ) {
		$efilename = explode('.', $imgName);
		return strtolower($efilename[count($efilename) -1 ])  ;
	}
	function GetAdminEmail() {
		//$admin_data=$this->Select(TABLE_ADMIN,"","e_mail","",1);
		$admin_data = $this->Select(TABLE_EMP_EMAILS,"is_super='1'","email");
		if(count($admin_data)) {
			foreach($admin_data as $admin) {
				return ucfirst($admin['email']);
			}
		}
		else return ADMIN_EMAIL;
	}
	function GetNewPassword($len=8)
	{
		$allowable_characters = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz23456789";
		mt_srand((double)microtime()*1000000);
	
		$pass = "";
		$length=$len;
		$ps_len = strlen($allowable_characters);
		$ps_st=0;
		for($i = 0; $i < $length; $i++) 
		{
			$pass .= $allowable_characters[mt_rand($ps_st, $ps_len - 1)];
		} 
		return strtolower($pass);
	}
	
	function GetHelpIcons($keyArr,$action_query)
	{
		$content.='<table border="0" align="right" cellpadding="4" cellspacing="4" class="text" >
           <tr>';
		  // print_r($keyArr);	
		foreach($keyArr as $key=>$value)
		{
			$key1=$key;
			$prefix = "";
			
			if($value['type']=='confirm') {
			
			} elseif($value['type']=='condition_alternate') {
				$prefix = "<!--Click to <br />-->";
				if($value['alternate']=='new')  $title = "Mark as New";
				else if($value['alternate']=='hide_new')  $title = "Unmark as New";
				else if($value['alternate']=='featured')  $title = "Mark as featured";
				else if($value['alternate']=='hide_featured')  $title = "Unmark as featured";
				else if($value['alternate']=='inactive')  $title = "In-Activate";
				else $title = $value['alternate'];
				$title=str_replace("_"," ",$title);				
				$key_title_alt = "Click to " .ucwords($title);			
				$content.='
				
				<td align="center" valign="bottom" class="actionButton" title="'.ucfirst($title).'" onMouseOver="this.className=\'actionButton_hover\';" onMouseOut="this.className=\'actionButton\';"  >
					<table border="0" align="center" cellpadding="0" cellspacing="0" class="text" >
           				<tr>
							<td align="center" valign="bottom"><img src="'.SITE_URL.'images/icons/'.$value['alternate'].'.png" alt="'.ucfirst($key_title_alt).'"  border="0" /></td>
						</tr>
						<tr>
							<td align="center" valign="top" style="padding-top:5px; min-width:40px;"> '.$prefix.ucfirst($title).'</td>
						</tr>
					</table>
				</td>';
			} else {
			
			}
			if($key=='new')  $title = "Mark as New";
			else if($key=='hide_new')  $title = "Unmark as New";
			else if($key=='featured')  $title = "Mark as featured";
			else if($key=='hide_featured')  $title = "Unmark as featured";
			else if($key=='active')  $title = "Activate";
			else $title = $key;
			$title=str_replace("_"," ",$title);
			
			$key_title_alt = "Click to " .ucwords($title);		
			if(($this->mPageName == "orders" || $this->mPageName == "catalogs") && $title=="delete"){
				$title = "archive";
			}
 			$content.='
				
				<td align="center" valign="bottom" class="actionButton" title="'.ucfirst($title).'" onMouseOver="this.className=\'actionButton_hover\';" onMouseOut="this.className=\'actionButton\';"  >
					<table border="0" align="center" cellpadding="0" cellspacing="0" class="text" >
           				<tr>
							<td align="center" valign="bottom"><img src="'.SITE_URL.'images/icons/'.$key1.'.png" alt="'.ucfirst($key_title_alt).'"  border="0" /></td>
						</tr>
						<tr>
							<td align="center" valign="top" style="padding-top:5px;min-width:40px;"> '.$prefix.ucfirst($title).'</td>
						</tr>
					</table>
				</td>';
			 
		}		
		$content.='</tr></table>';
		return $content;
	}
		
	function GetManageListAjax($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) {
		global $sort_query,$action_query, $customList;
		if($countList=="") $countList=array();
		global $gStartPageNo;
		$page_header_tag='<div><div style="height:20px;text-align:right;"><strong><span id="spanRecordCount">0 Record.</span></strong></div><div class="clear"></div><table cellspacing="0" cellpadding="0" border="1" bordercolor="#d6d6d6" class="listingTbl" width="100%">';
		if(count($listArray)==0) {
			$page_content.= " 
					<tr align='center'>
						<td colspan='10' valign='top' class='redheading'>No Result Found</td>
					</tr>";
	   	} else {
			
			$color="bg_light";
			$count = $gStartPageNo;
			foreach($listArray as $result) {
				$count++;
				if($count%2==0) $bg = "list_row";
				else $bg = "list_row";
				$no++;
				if($is_page_header=="") {
					$page_header.='
					<tr class="rowHeader">
						 <th width="4%" align="center"><strong>#</strong></th>';
				}
				$page_content.= " 
					<tr id='list_row_".$no."' style='display:none;' > 
						<td align='center' valign='top' width='5%' id='modManage_".$no."_No'>".sprintf("%d",$count)."</td><input type='hidden' name='modManage_".$no."_id' id='modManage_".$no."_id' value='".$result['id']."'/>";		
				$field_count=1;
				$size_count=0;
				foreach($result as $key=>$value) { 
					if($key!='id' && $key!='is_active' && $key!='is_paid' && $key!='auto_notification' && $key!='is_manageable'){ 
						$page_content.= " <td align='left' valign='top' style='padding-left:7px;' id='modManage_".$no."_".$key."'>";
						
						$page_content.= stripslashes($result[$key]). "</td>";
						 
						if($is_page_header=="") {
							if($sizeArray[$size_count]!="")
								$width='width="'.$sizeArray[$size_count].'"';
							else
								$width="";
							
							if($key=="E_Mail")
								$replace_string=str_replace("_","-",$key);
							else
								$replace_string=str_replace("_"," ",$key);
							
							$page_header.='
								<th style="padding-left:7px;" '.$width.' ';
							if($countList[$key]['table']!="" || $sortArray[$key]=='N') {
								$page_header.='  ><strong>'.$replace_string. '</strong></td>';
							}
							else {
								if($_GET['sort'] == "range_from"){
									$_GET['sort'] = "Price_Range";
								}
								if($key=="E_Mail")
									$replace_string=str_replace("_","-",$key);
								else
									$replace_string=str_replace("_"," ",$key);
								if($_GET['sort']==$key) {
									$page_header.=' ';
									if($_GET['order']=='a') $order_q="&order=d"; else $order_q='&order=a';
								} else {
									$order_q='&order=a';
								}
								if($key=='Added_Date') $key_sort = 'add_date'; else $key_sort=$key;
								$page_header.=' ><a href="javascript: void(0);" onclick="javascript: ModuleObj.ChangeSort(\''.$key_sort.'\',\'asc\');">'.$replace_string.'';
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='<img src="'.SITE_URL.'images/up.gif" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='<img src="'.SITE_URL.'images/down.gif" border=0 align="absmiddle" />';
									
								$page_header.='</a></th>
									 ';
							}
							
							$size_count++;
						}
						$field_count++;
					}
				}
				$action_count=0;
				$action_content_inner='';
				foreach($listAction as $aKey => $aValue) {
					$confirm_msg = $aValue['message']!=""?$aValue['message']:"Are you sure you want to ".ucfirst($aKey)." this record?";
					if($aKey=='new')  $title = "Mark as New";
					else if($aKey=='hide_new')  $title = "Unmark as New";
					else if($aKey=='featured')  $title = "Mark as featured";
					else if($aKey=='hide_featured')  $title = "Unmark as featured";
					else if($aKey=='active')  $title = "Activate";
					else $title = $aKey;
					
					$key_title = "Click to " .ucwords(str_replace("_"," ",$title));					
					if($aValue['type']=='confirm') {
						if(($this->mPageName == "orders" || $this->mPageName == "catalogs")  && $aKey=="delete"){
							$key_title = "Click to Archive";
							$confirm_msg = "Are you sure you want to Archive selected record";
						}
							$page_content.= "
											<td width='3%' align='center' valign='middle'  condition=0  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."' actionconditionkey='".$aValue['conditionkey']."' >
												<a id='modManage_".$no."_anchor_".($action_count+1)."' href='javascript:ModuleObj.showHidden($(\"modManage_".$no."_id\").value,\"".$aKey."\");' onclick=\"javascript: return confirm('".$confirm_msg."'); \"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a><span style='display:none;' id='modManage_".$no."_noaction_".($action_count+1)."'>&ndash;</span>
											</td>";
					} elseif($aValue['type']=='condition') {
						
							$page_content.= "
										<td width='3%' align='center' valign='middle' id='modManage_".$no."_action_".($action_count+1)."' condition=1 actionconditionkey='".$aValue['conditionkey']."' actionconditionvalue='".$aValue['conditionvalue']."'  >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href=\"javascript:ModuleObj.showHidden($('modManage_".$no."_id').value,'".$aKey."','','modManage_".$no."_action2_".($action_count+1)."','".$no."');\" onclick=\"javascript: return confirm('".$confirm_msg."'); \"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";
					} elseif($aValue['type']=='condition_alternate') {
							$confirm_msg_alt = $aValue['alternate_message']!=""?$aValue['alternate_message']:"Are you sure you want to ".ucfirst($aValue['alternate'])." this record?";
							if($aValue['alternate']=='new')  $title = "Mark as New";
							else if($aValue['alternate']=='hide_new')  $title = "Unmark as New";
							else if($aValue['alternate']=='featured')  $title = "Mark as featured";
							else if($aValue['alternate']=='hide_featured')  $title = "Unmark as featured";
							else if($aValue['alternate']=='inactive')  $title = "In-Activate";
							else $title = $aValue['alternate'];
							
							$key_title_alt = "Click to " .ucwords(str_replace("_"," ",$title));							
							if($aValue['confirm']) {
								$confirm_alert = "onclick=\"javascript: return confirm('".$confirm_msg."'); \"";
								$confirm_alert_alt = "onclick=\"javascript: return confirm('".$confirm_msg_alt."'); \"";
							}
							$page_content.= "
										<td width='3%' align='center' valign='middle' id='modManage_".$no."_action_".($action_count+1)."' condition=3 condition_alternate='".$aValue['alternate']."' condition_alternate_msg='".$aValue['alternate_message']."' actionconditionkey='".$aValue['conditionkey']."' actionconditionvalue='".$aValue['conditionvalue']."'  >
											<a  id='modManage_".$no."_action2_".($action_count+1)."' href=\"javascript:ModuleObj.showHidden($('modManage_".$no."_id').value,'".$aKey."','','modManage_".$no."_action2_".($action_count+1)."','".$no."');\" ".$confirm_alert."><img src='".SITE_URL."images/icons/".$aKey.".png'    border='0' alt='".$key_title."' title='".$key_title."'></a>
											<a  id='modManage_".$no."_action2_".($action_count+1)."_alt' href=\"javascript:ModuleObj.showHidden($('modManage_".$no."_id').value,'".$aValue['alternate']."','','modManage_".$no."_action2_".($action_count+1)."','".$no."');\" ".$confirm_alert_alt."><img src='".SITE_URL."images/icons/".$aValue['alternate'].".png'    border='0' alt='".$key_title_alt."' title='".$key_title_alt."'></a>
										</td>";
					} elseif($aValue['type']=='link2') {
						$page_content.= "
										<td width='3%' align='center' valign='middle'  condition=2  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."' >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href='javascript:ModuleObj.showHidden($(\"modManage_".$no."_id\").value,\"".$aKey."\");'  ><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} elseif($aValue['type']=='link3') {
						$page_content.= "
										<td width='3%' align='center' valign='middle'  condition=2  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."' >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href='javascript:ModuleObj.showHidden($(\"modManage_".$no."_id\").value,\"".$aKey."\",\"".$aValue['redirecturl']."\");'  ><img src='".SITE_URL."images/icons/".$key_title.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} elseif($aValue['type']=='link4') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=0  id='modManage_".$no."_action_".($action_count+1)."' >
											<a href=\"javascript:ModuleObj.showAddEdit($('modManage_".$no."_id').value+'|activity');\"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} elseif($aValue['type']=='link5') {
						$key_title = "Click to Edit Bargain Book";
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=2  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."'  >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href='javascript:".$this->mPageName.".SwitchSections(\"BargainBook\",$(\"modManage_".$no."_id\").value,\"".$aKey."\");'  ><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} else {
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=0  id='modManage_".$no."_action_".($action_count+1)."' >
											<a href=\"javascript:ModuleObj.showAddEdit($('modManage_".$no."_id').value);\"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					}
					$keyArr[]=$aKey;
					$action_count++;
				}
				 
				 
				if($is_page_header=="") {
					if($action_count>0) {
					$page_header.='
						<th width="10%" colspan="'.$action_count.'" align="center"  >Action<input type="hidden" name="modManage_action_count" id="modManage_action_count" value="'.$action_count.'" /></th>
                        ';
					}
					$page_header.='
					</tr>';
					$is_page_header="yes";
				}
				$page_content.= "
									</tr>";
				$page_content.= " 
					<tr align='center' id='modNoRecord' style='display:none;'>
						<td colspan='".($action_count+$field_count)."' valign='top' class='redheading' >No Result Found</td>
					</tr>";
				$page_content.= " 
					<tr align='center' id='modLoading'>
						<td colspan='".($action_count+$field_count)."' valign='top' class='redheading' >Loading..</td>
					</tr>";
			}
		}
		if(count($listArray)<PAGING_LIMIT) {
			for($i=count($listArray);$i<PAGING_LIMIT;$i++) {
				$no++;
				$page_content.= " 
					<tr id='list_row_".$no."' style='display:none;' > 
						<td align='center' valign='top' width='5%' id='modManage_".$no."_No'>".sprintf("%d",$no)."</td><input type='hidden' id='modManage_".$no."_id' name='modManage_".$no."_id' value=''/>";
				foreach($listArray[0] as $key=>$value) { 
					if($key!='id' && $key!='is_active' && $key!='is_paid' && $key!='auto_notification' && $key!='is_manageable'){ 
						$page_content.= "<td align='left' valign='top' style='padding-left:7px;' id='modManage_".$no."_".$key."'></td>";
					}
				}
				$action_count=0;
				foreach($listAction as $aKey => $aValue) {
					$confirm_msg = $aValue['message']!=""?$aValue['message']:"Are you sure you want to ".ucfirst($aKey)." this record?";
					if($aKey=='new')  $title = "Mark as New";
					else if($aKey=='hide_new')  $title = "Unmark as New";
					else if($aKey=='featured')  $title = "Mark as featured";
					else if($aKey=='hide_featured')  $title = "Unmark as featured";
					else if($aKey=='active')  $title = "Activate";
					else $title = $aKey;
					
					$key_title = "Click to " . ucwords(str_replace("_"," ",$title));
					if($aValue['type']=='confirm') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=0  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."' actionconditionkey='".$aValue['conditionkey']."'  >
											<a id='modManage_".$no."_anchor_".($action_count+1)."' href='javascript:ModuleObj.showHidden($(\"modManage_".$no."_id\").value,\"".$aKey."\");' onclick=\"javascript: return confirm('".$confirm_msg."'); \"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a><span style='display:none;' id='modManage_".$no."_noaction_".($action_count+1)."'>&ndash;</span>
										</td>";	
					} elseif($aValue['type']=='condition') {
						 
							/* Comment AB
							$page_content.= "
										<td width='3%' align='center' valign='middle'  id='modManage_".$no."_action_".($action_count+1)."' condition=1 actionconditionkey='".$aValue['conditionkey']."' actionconditionvalue='".$aValue['conditionvalue']."'   >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href=\"javascript:ModuleObj.showHidden($('modManage_".$no."_id').value,'".$aKey."');\" onclick=\"javascript: return confirm('Are you sure you want to ".ucfirst($aKey)." this record?'); \"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";
							Comment AB End*/
							$page_content.= "
										<td width='3%' align='center' valign='middle'  id='modManage_".$no."_action_".($action_count+1)."' condition=1 actionconditionkey='".$aValue['conditionkey']."' actionconditionvalue='".$aValue['conditionvalue']."'   >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href=\"javascript:ModuleObj.showHidden($('modManage_".$no."_id').value,'".$aKey."','','modManage_".$no."_action2_".($action_count+1)."','".$no."');\" onclick=\"javascript: return confirm('".$confirm_msg."'); \"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";			
										
										
						 
					} elseif($aValue['type']=='condition_alternate') {
							$confirm_msg_alt = $aValue['alternate_message']!=""?$aValue['alternate_message']:"Are you sure you want to ".ucfirst($aValue['alternate'])." this record?";
							if($aValue['alternate']=='new')  $title = "Display on What&acute;s New Page";
							else if($aValue['alternate']=='hide_new')  $title = "Hide from What&acute;s New Page";
							else if($aValue['alternate']=='featured')  $title = "Mark as featured";
							else if($aValue['alternate']=='hide_featured')  $title = "Unmark as featured";
							else if($aValue['alternate']=='inactive')  $title = "In-Activate";
							else $title = $aValue['alternate'];
							
							$key_title_alt = "Click to " .ucwords(str_replace("_"," ",$title));
							if($aValue['confirm']) {
								$confirm_alert = "onclick=\"javascript: return confirm('".$confirm_msg."'); \"";
								$confirm_alert_alt = "onclick=\"javascript: return confirm('".$confirm_msg_alt."'); \"";
							}
							$page_content.= "
										<td width='3%' align='center' valign='middle' id='modManage_".$no."_action_".($action_count+1)."' condition=3 condition_alternate='".$aValue['alternate']."' condition_alternate_msg='".$aValue['alternate_message']."' actionconditionkey='".$aValue['conditionkey']."' actionconditionvalue='".$aValue['conditionvalue']."'  >
											<a  id='modManage_".$no."_action2_".($action_count+1)."' href=\"javascript:ModuleObj.showHidden($('modManage_".$no."_id').value,'".$aKey."','','modManage_".$no."_action2_".($action_count+1)."','".$no."');\" ".$confirm_alert."><img src='".SITE_URL."images/icons/".$aKey.".png'    border='0' alt='".$key_title."' title='".$key_title."'></a>
											<a  id='modManage_".$no."_action2_".($action_count+1)."_alt' href=\"javascript:ModuleObj.showHidden($('modManage_".$no."_id').value,'".$aValue['alternate']."','','modManage_".$no."_action2_".($action_count+1)."','".$no."');\" ".$confirm_alert_alt."><img src='".SITE_URL."images/icons/".$aValue['alternate'].".png'    border='0' alt='".$key_title_alt."' title='".$key_title_alt."'></a>
										</td>";
					} elseif($aValue['type']=='link2') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=2  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."'  >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href='javascript:ModuleObj.showHidden($(\"modManage_".$no."_id\").value,\"".$aKey."\");'  ><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} elseif($aValue['type']=='link3') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=2  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."'  >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href='javascript:ModuleObj.showHidden($(\"modManage_".$no."_id\").value,\"".$aKey."\",\"".$aValue['redirecturl']."\");'  ><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} elseif($aValue['type']=='link4') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=0  id='modManage_".$no."_action_".($action_count+1)."' >
											<a href=\"javascript:ModuleObj.showAddEdit($('modManage_".$no."_id').value+'|activity');\"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} elseif($aValue['type']=='link5') {
						$key_title = "Click to Edit Bargain Book";
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=2  id='modManage_".$no."_action_".($action_count+1)."' action_name='".$aKey."'  >
											<a id='modManage_".$no."_action2_".($action_count+1)."' href='javascript:".$this->mPageName.".SwitchSections(\"BargainBook\",$(\"modManage_".$no."_id\").value,\"".$aKey."\");'  ><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					} else {
						$page_content.= "
										<td width='3%' align='center' valign='middle' condition=0  id='modManage_".$no."_action_".($action_count+1)."'  >
											<a href=\"javascript:ModuleObj.showAddEdit($('modManage_".$no."_id').value);\"><img src='".SITE_URL."images/icons/".$aKey.".png'   border='0' alt='".$key_title."' title='".$key_title."'></a>
										</td>";	
					}
					$keyArr[]=$aKey;
					$action_count++;
				}
				$page_content.= "</tr>";
			}
		}
		$page_content.=$action_content;
		global $gPagingExtraPara;
		if($gPagingExtraPara=="")
			$gPagingExtraPara="sort=".$_GET['sort']."&order=".$_GET['order'];
		
		$page_content.="</table><div class=\"clear\"></div></div> <div class=\"clear\"></div>";
		if(count($listArray)>0) {
			/*$page_content.='<div class="pagination">
        	       	'.$this->PagingFooter($_GET['offset']).'</div>';*/
			$page_content.='
		<div class="pagination" id="modPagination" style="display:none; padding-top:10px;">
        	<div class="divContent">
            	<div class="corners"><img src="'.SITE_URL.'images/admin/paging_curve_left.jpg" /></div>
                <div class="paging">
                <select id="modPaginationDD" onchange="ModuleObj.GoToPage(this.value);">
                	
                </select>
                </div>
                <div class="corners"><img src="'.SITE_URL.'images/admin/paging_curve_right.jpg" /></div>
            </div>
            <div class="divContent">
            	<div class="corners"><img src="'.SITE_URL.'images/admin/paging_curve_left.jpg" /></div>
                <div class="paging">
                <ul class="paginationMenu" id="modPaginationNo">
                	
                </ul>
                </div>
                <div class="corners"><img src="'.SITE_URL.'images/admin/paging_curve_right.jpg" /></div>
            </div>
            <div class="clear"></div>
         </div>';
		} 
		return '<div>'.$this->GetHelpIcons($listAction).'<div class="clear"></div></div> '.$page_header_tag.$page_header.$page_content;
	}
	
		
	function SendMail($subject,$message,$arrValues,$type="html",$bcc=array(),$cc=array(),$attachment=array()) {
		foreach($arrValues as $key=>$value) {
			$message=str_replace("{".$key."}",$value,$message);
			$message=str_replace("{".strtoupper($key)."}",$value,$message);
			$message=str_replace("{".strtolower($key)."}",$value,$message);
		}
		$constant_arr=get_defined_constants(true);
		
		foreach($constant_arr['user'] as $key=>$value) {
			if($key=="SIGNATURE" && $type=="text/html") $value=nl2br($value); 
			$message=str_replace("{".$key."}",$value,$message);
			$message=str_replace("{".strtoupper($key)."}",$value,$message);
			$message=str_replace("{".strtolower($key)."}",$value,$message);
		}
		
		//print_r($message); exit;
		$bcc=implode(', ',$bcc);
		$cc=implode(', ',$cc);
		$headers = 'From: '.SITE_NAME.' <' . $arrValues['from'] . '>' . "\r\n";
		if($bcc!="") $headers .= 'Bcc: ' . $bcc . " \r\n";
		if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
		//print_r($attachment);exit;
		if(count($attachment)>0) {
			$mime_boundary = "B_".md5(time())."";
			$headers .="Content-type: multipart/mixed;
	".'boundary="'.$mime_boundary.'"' . "\r\n";
			$headers .="--".$mime_boundary."\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n\r\n";
			$headers .= $message;
			foreach($attachment as $key_att=>$att) {
				$fname = substr(strrchr($att, "/"), 1);
				$data = file_get_contents($att);
				//echo mime_content_type($att);//$content_id = "part$i." . sprintf("%09d", crc32($fname)) . strrchr($this->to_address, "@");
				$i++;
				$headers .= 	"\r\n"."--".$mime_boundary."\r\n"."Content-Type: application/pdf; name=\"$key_att\"\r\n" .
								  "Content-Transfer-Encoding: base64\r\n" .
								  "Content-Disposition: attachment;\n" .
								  " filename=\"$key_att\"\r\n" .
								  "\r\n" .
								  chunk_split( base64_encode($data), 68, "\n");
			}			
			$headers .='\r\n--'.$mime_boundary.'--';				
			return @mail($arrValues['EMAIL'],$subject,"",$headers);
		} else {
			//echo $arrValues['EMAIL']."====<br>".$subject."====<br>".nl2br($message)."====<br>".$headers; die;
			/*echo "Email=============>".$arrValues['EMAIL'];
			echo "<br />";
			echo "Subject==============>".$subject;
			echo "<br>";
			echo "Body=============>".$message;
			echo "<br>";
			echo "Headers============>".$headers;exit;*/
			$headers .= 'Content-type: '.$type.'; charset=iso-8859-1' . "";				 		
			return @mail($arrValues['EMAIL'],$subject,$message,$headers);
		}
	}

	function MakeFileDelete($path)
	{ 
		@unlink($path);
		return;
	}
	
	function UploadImage($source, $prefix="", $mainDir, $thumbnailDir, $imgwidth='100', $imgheight='100') {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			/*elseif($ext==4)
				$ext="bmp";*/
			else
				return false;
			$prefix = $prefix."_";
			$new_file=uniqid($prefix);
			$destination = $mainDir.$new_file.'.'.$ext;
			if(move_uploaded_file($source,$destination)) {
				if($width>800 || $height>600)
					$this->CreateThumb($new_file,$ext,$mainDir,800,600,$mainDir);
				$this->CreateThumb($new_file,$ext,$mainDir,$imgwidth,$imgheight,$thumbnailDir);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	
	function UploadImageFix($source, $prefix="img", $mainDir, $thumbnailDir, $smallDir) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$new_file=uniqid($prefix.'_');
			$destination = $mainDir.$new_file.'.'.$ext;
			if(move_uploaded_file($source,$destination)) {
				if($width>600 || $height>450)
					$this->CreateThumb($new_file,$ext,$mainDir,600,450,$mainDir);
					$this->CreateThumbFix($new_file,$ext,$mainDir,150,150,$thumbnailDir);
					$this->CreateThumbFix($new_file,$ext,$mainDir,75,75,$smallDir);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	
	//extra//////////////////////////////////////////////////////////////
	
	
	function Ajax_Email($userName) {
		$get_result=$this->Select(TABLE_CUSTOMER,"e_mail='".$userName."'","e_mail");
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data="0####".$userName;
				$number_range_s=00;
				$number_range_e=99;
				$sep_arr=array('','.');
				$user_names="";
				for($i=0;$i<=99;$i++) {
					if($ucount>=4) break;
					$new_no=sprintf("%02d",rand($number_range_s,$number_range_e));
					$sep_no=rand(0,2);
					if($this->CheckUsername($userName.$sep_arr[$sep_no].$new_no) && $ucount<2) {
						$user_names.="<a href='javascript: void(0);' onclick='javascript: change_username(\"".$userName.$sep_arr[$sep_no].$new_no."\");' class='home' >".$userName.$sep_arr[$sep_no].$new_no."</a><br />";
						$ucount++;
					} elseif ($ucount==2 && $vfi!="no") {
						if($vfi) {
							$n_username=$userName.'.fl';
						} else {
							$n_username='fl.'.$userName;
						}
						if($this->CheckUsername($n_username)) {
							$user_names.="<a href='javascript: void(0);' onclick='javascript: change_username(\"".$n_username."\");' class='home' >".$n_username."</a><br />";
							$ucount++;
						}
						if(!$vfi) $vfi=true; else $vfi="no";
					}  else {
						$new_no=sprintf("%02d",rand(1950,(date('Y')+10)));
						$sep_no=rand(0,2);
						if($this->CheckUsername($userName.$sep_arr[$sep_no].$new_no)) {
							$user_names.="<a href='javascript: void(0);' onclick='javascript: change_username(\"".$userName.$sep_arr[$sep_no].$new_no."\");' class='home' >".$userName.$sep_arr[$sep_no].$new_no."</a><br />";
							$ucount++;
						}
					}
				}
				$data.="####".$user_names;
			}
		}
		else 
			$data="1####".$userName;
		return $data;
	}
	//---------------------------------------------------------------------------------------------------------//
	//------------ Method Display Message-----------------------------------------------------------------------//
	//---------------------------------------------------------------------------------------------------------//	
	function GetMsg($msg='', $msg_type='notice'){
		if($msg_type=='err'){
			$class = "message_error";
			$message = "Error: ".$msg;
		}
		else{
			$class = "message_information";
			$message = $msg;
		}

		if($msg=="") $data="";
		else{
			$data = '<div class="message_div"><div class="'.$class.'">
						'.$message.'
					</div></div>';
		}
		
		return $data;
	} 
	
	function GetMsgDetails($id) {
		$page=$this->mPageName;
		switch($page) {
			case "admin_users": //echo $id; exit;
				if($id!="") {
					$getRes = $this->Select(TABLE_ADMIN,'admin_id IN ('.$id.')');
					if(count($getRes)>0) {
						foreach($getRes as $data_arr) {
							//$result.="<strong>".$sep.ucfirst(stripslashes($data_arr['contact_name']))."</strong>";
							$result.=$sep.ucfirst(stripslashes($data_arr['name']));
							$sep=", ";
						}
					}
				} else 
					$result="";
				return $result;	
				break;
				
			default:
				$result="";
				return $result;
				break;
		}
	}
	
	function GetLoginPage() {
		ob_start();
		@include(ADMIN_HTML."login.html");
		return ob_get_clean();
	}
	function GetLoaderBox() {
		ob_start();
		@include(ADMIN_HTML."loader.html");
		return ob_get_clean();
	}
	function GetAdminLoginBoolen() {
		if($this->ValidateSession('admin')) {
			return 1;
		} else {
			return 0;
		}
	}
	function GetCurrentPage() {
		$_GET['url'];
	}
	function ParseCSVLine($csv) {
		$csv_len = strlen($csv);
		$field_arr = array();
		$field_start_flag = true;
		$field_end_flag = false;
		$field_quote_flag=false;
		$field_end_final_flag = true;
		$field_offset = 0;
		for($i=0; $i<$csv_len; $i++) {
			if($csv[$i]=='"') {
				if($field_end_final_flag) {
					$field_start_flag = true;
					$field_quote_flag=true;
					$field_end_final_flag=false;
				} else {
					if($field_end_flag) {
						$field_arr[$field_offset].="\"";
						$field_end_flag=false;
					} else {
						$field_end_flag = true;
						$field_start_flag = false;
					}
				}
			} elseif($csv[$i]==',' && $field_end_flag && $field_quote_flag) {
				$field_end_final_flag=true;
				$field_quote_flag=false;
				$field_start_flag = true;
				$field_end_flag = false;
				$field_offset++;
				$field_arr[$field_offset]="";
			} elseif($csv[$i]==',' && !$field_quote_flag && $field_start_flag) {
				$field_end_final_flag=true;
				$field_quote_flag=false;
				$field_end_flag = false;
				
				$field_offset++;
				$field_arr[$field_offset]="";
				$field_start_flag = true;
			} else {
				$field_arr[$field_offset].=$csv[$i];
			}
		}
		return $field_arr;
	}
	function GetCountryCode2($code) {
		$getdata=$this->Select(TABLE_COUNTRY,"Code='".$code."'","Code2","",1);
		if(count($getdata)>0){
			$code2=$getdata[0]['Code2'];
		}
		return $code2;
	}
	function GetCountryCode($country) {
		$getdata=$this->Select(TABLE_COUNTRY,"Name='".$country."'","Code");
		if(count($getdata)>0){
			$code=$getdata[0]['Code'];
		} else {
			$code="";
		}
		return $code;
	}
	// get Country ID
	function GetCountryID($country) {
		$getdata=$this->Select(TABLE_COUNTRY,"Code2='".strtoupper($country)."'","country_id");
		if(count($getdata)>0){
			$code=$getdata[0]['country_id'];
		} else {
			$code="";
		}
		return $code;
	}
	function GetAdminPermissionList($selectPermissions) {
		if(!is_array($selectPermissions)) $selectPermissions=array();
		$module_details = $this->Select(TABLE_CONFIG,"page_type='admin' and session_type='admin' and page_title!=''","page_name, page_title","page_title");
		if(count($module_details)>0) {
			$i=0;
			$module_data ='<tr>';
			foreach($module_details as $module) {
				if($i%3==0 && $i>0) {
					$module_data .='</tr><tr>';
				}
				if(in_array($module['page_name'],$selectPermissions)) $sel="checked"; else $sel=""; 
				$module_data .= '<td class="text" align="right"><input type="checkbox" name="chk_permissions[]" id="chk_permissions_'.$module['page_name'].'" value="'.$module['page_name'].'" '.$sel.' /></td>
								<td class="text" align="left"><label for="chk_permissions_'.$module['page_name'].'" >'.$module['page_title'].'</label></td>
							';
				$i++;
			}
			$module_data .='</tr>';
		}
		return $module_data;
	}
	
	function CreateThumbRatio($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="",$bg=false,$bg_transp=false) {
		$filename = $folder . $photo . "." . $ext;
		$thumb_height=$height;
		$thumb_width=$width;
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		///if($width_orig <= $width && $height_orig <= $height){
			///$height=$height_orig;
			///$width=$width_orig;
		///} else {
			// Set a maximum height and width
			if ($width && ($width_orig < $height_orig)) {
				$new_width = ($height / $height_orig) * $width_orig;
				$new_height=$height;
			} else {
				$new_height = ($width / $width_orig) * $height_orig;
				$new_width=$width;
			}
			if($new_height>$height) {
				$height_fix=1;
				$req_height = $height;
			}
			$height=$new_height;
			$width=$new_width;
		///}
		// Resample
		$image_p = imagecreatetruecolor($width, $height);
		if(strtolower($ext)=="gif") $image = imagecreatefromgif($filename);
		elseif($ext=="jpg") $image = imagecreatefromjpeg($filename);
		elseif($ext=="png") $image = imagecreatefrompng($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		
		if($height_fix==1) {
			$height=$req_height;
			$width = ($height / $new_height) * $new_width;
			$image = $image_p;
			$image_p = imagecreatetruecolor($width, $height);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $new_width, $new_height);
		}
		if($bg) {
			if($width<$thumb_width || $height<$thumb_height) {
				
				$temp_width	=	round($thumb_width/2) - round($width/2);
				$temp_height	=	round($thumb_height/2) - round($height/2);
				
				$im = imagecreatetruecolor($thumb_width,$thumb_height);
				if(!$bg_transp){
					$background_color = imagecolorallocate($im, 255, 255, 255);
					imagefill($im,0,0,$background_color);
					imagecopymerge($im, $image_p,$temp_width, $temp_height, 0, 0, $width, $height, 100);
				} else {
					$background_color = imagecolorallocate($im, 0, 0, 0);
					//imagecolortransparent($im, imagecolorat($im,0,0));      
					imagecolortransparent($im, $background_color);      
					imagealphablending($im, false);
					///$this->imageComposeAlpha($im, $image_p, $temp_width, $temp_height, $width, $height );
					imagecopymerge($im, $image_p,$temp_width, $temp_height, 0, 0, $width, $height, 100);
					///imagecopy($im, $image_p, 0, 0, $temp_width, $temp_height, $thumb_width, $thumb_height);
					
				}
				
				$image_p = $im;
			}
		}  
		if(!$bg_transp){
			if($ext=="gif") $image_thumb=imagegif($image_p, $thumbFolder .$photo . ".gif");
			elseif($ext=="jpg") $image_thumb=imagejpeg($image_p, $thumbFolder .$photo . ".jpg",100);
			elseif($ext=="png") $image_thumb=imagepng($image_p,$thumbFolder .$photo . ".png");
		} else {
			$image_thumb=imagepng($image_p,$thumbFolder .$photo . ".png");
		}
		imagedestroy($image_p);
		imagedestroy($image);
	}
	
	
	function imageComposeAlpha( &$src, &$ovr, $ovr_x, $ovr_y, $ovr_w = false, $ovr_h = false) {
		if( $ovr_w && $ovr_h )
			$ovr = $this->imageResizeAlpha( $ovr, $ovr_w, $ovr_h );
		
		/* Noew compose the 2 images */
		imagecopy($src, $ovr, $ovr_x, $ovr_y, 0, 0, imagesx($ovr), imagesy($ovr) );
	}
	function imageResizeAlpha(&$src, $w, $h)
	{
		/* create a new image with the new width and height */
		$temp = imagecreatetruecolor($w, $h);
		
		/* making the new image transparent */
		$background = imagecolorallocate($temp, 0, 0, 0);
		ImageColorTransparent($temp, $background); // make the new temp image all transparent
		imagealphablending($temp, false); // turn off the alpha blending to keep the alpha channel
		
		/* Resize the PNG file */
		/* use imagecopyresized to gain some performance but loose some quality */
		//imagecopyresized($temp, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));
		/* use imagecopyresampled if you concern more about the quality */
		imagecopyresampled($temp, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));
		return $temp;
	}
	
	function ValidateLoginFailedEntry($username,$admin_type=""){
		
		$settingresult = $this->Select(TABLE_SETTINGS,"","captcha_failed_attempts","",1);
		if(count($settingresult)>0){
			$captcha_failed_attempts = $settingresult[0]['captcha_failed_attempts'];	
		} else {
			$captcha_failed_attempts = 0;
		}
		if($captcha_failed_attempts != -1){
			if($admin_type=="client"){
				$get_result=$this->Select(TABLE_CLIENTS,"username='".$username."' AND is_deleted=0 AND accessurl='".addslashes($this->mArgs[0])."' AND failed_attempt >= '".$captcha_failed_attempts."' AND TIMESTAMPDIFF(MINUTE,failed_attempt_time, NOW()) < 15","client_id","",1);
			} else {
				$get_result=$this->Select(TABLE_ADMIN,"user_name='".$username."' AND failed_attempt >= '".$captcha_failed_attempts."' AND TIMESTAMPDIFF(MINUTE,failed_attempt_time, NOW()) < 15","admin_id","",1);
			}
			if(count($get_result)>0){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	function UpdateLoginFailedEntry($adminid,$admin_type=""){
		$updatearr = array();
		$updatearr['failed_attempt'] = 'failed_attempt+1';
		$updatearr['failed_attempt_time'] = "NOW()";
		if($admin_type=="client"){
			$this->Update(TABLE_CLIENTS,$updatearr,"client_id='".$adminid."'");
		} else {
			$this->Update(TABLE_ADMIN,$updatearr,"admin_id='".$adminid."'");
		}
			
	}
	function ResetLoginFailedEntry($adminid,$admin_type=""){
		$updatearr = array();
		$updatearr['failed_attempt'] = 0;
		if($admin_type=="client"){
			$this->Update(TABLE_CLIENTS,$updatearr,"client_id='".$adminid."'");
		} else {
			$this->Update(TABLE_ADMIN,$updatearr,"admin_id='".$adminid."'");
		}
	}
	
	function GetUSStateDropDown() {
		global $db;
		$get_result=$this->Select(TABLE_STATE,"","DISTINCT(state_prefix),state_name","state_name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$data .= '<option value="'.$result['state_prefix'].'"';				
				$data .= '>'.ucwords(strtolower($result['state_name'])).'</option>';
			}
		}
		return $data;
	}
	
	function GetUSStateDropDownReg($stateSelected) {
		global $db;
		$get_result=$this->Select(TABLE_STATE,"","DISTINCT(state_prefix),state_name","state_name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$data .= '<option value="'.$result['state_prefix'].'"';	
				if($stateSelected == $result['state_prefix'] ) $data .= ' selected="selected"';			
				$data .= '>'.ucwords(strtolower($result['state_name'])).'</option>';
			}
		}
		return $data;
	}
	function getStateName($id){
		$get_result=$this->Select(TABLE_STATE,"id='".$id."'","fullname","",1);						
		if(count($get_result)>0){
			return ucwords(strtolower($get_result[0]['fullname'])); 
		}
	}
	
	function GetUSCityDropDown($citySelected, $stateid) {
		global $db;
		$get_result=$this->Select(TABLE_CITY,"tblStateId='".$stateid."'","*","name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$data .= '<option value="'.$result['id'].'"';
				if($citySelected == $result['id'] ) $data .= ' selected="selected"';
				$data .= '>'.ucwords(strtolower($result['name'])).'</option>';
			}
		}
		return $data;
	}
	function getCityName($id){
		$get_result=$this->Select(TABLE_CITY,"id='".$id."'","name","",1);						
		if(count($get_result)>0){
			return ucwords(strtolower($get_result[0]['name'])); 
		}
	}
	
	
	function GetDateFormatsDD($selected,$type){
		$get_result = $this->Select(TABLE_DATE_FORMATS,"is_active=1 and type='".$type."'","*");
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$data .= '<option value="'.$result['date_format_id'].'"';
				//if($selected == $result['date_format_id'] ) $data .= ' selected="selected"';
				$data .= '>'.date($result['date_format_php']).' '.$result['comments'].'</option>';
			}
		}
		return $data;
	}
	
	function GetTimeFormatDD($selected){
		$arr = array("12"=>"12 Hours", "24"=>"24 Hours");	
		foreach($arr as $key=>$val) {
			$data .= '<option value="'.$key.'"';
			//if($selected == $result['date_format_id'] ) $data .= ' selected="selected"';
			$data .= '>'.$val.'</option>';
		}
		return $data;
	}
	
	// status 
	function GetStatusDD(){
		//$arr = array("pending"=>"Pending", "approved"=>"Approved/Processing",'partly_shipped'=>'Partly Shipped','shipped'=>'Shipped','denied'=>'Denied','delivered'=>'Delivered');
		$arr = array("new_order"=>"New Order", "downloaded"=>"Downloaded",'cancelled'=>'Cancelled','backorder'=>'BackOrdered','partly_shipped'=>'Partly Shipped','shipped'=>'Shipped','paypal_hold'=>'PayPal Hold');	
		foreach($arr as $key=>$val) {
			$data .= '<option value="'.$key.'"';
			$data .= '>'.$val.'</option>';
		}
		return $data;
	}
	function GetBBStatusDD(){
		//$arr = array("pending"=>"Pending", "approved"=>"Approved/Processing",'partly_shipped'=>'Partly Shipped','shipped'=>'Shipped','denied'=>'Denied','delivered'=>'Delivered');
		$arr = array("new_order"=>"New Order", "downloaded"=>"Downloaded",'cancelled'=>'Cancelled','backorder'=>'BackOrdered','shipped'=>'Shipped');	
		foreach($arr as $key=>$val) {
			$data .= '<option value="'.$key.'"';
			$data .= '>'.$val.'</option>';
		}
		return $data;
	}
	function GetTime($timeformat, $platform){
		if($timeformat == 12){
			if($platform == "php")
				return  "h:i A";
			else if($platform == "mysql")
				return  "%h%i %p";
			else 
				return  "h:i A";	
		} else if($timeformat == 24){
			if($platform == "php")
				return  "H:i";
			else if($platform == "mysql")
				return  "%H%i";
			else 
				return  "H:i";
		}
	}
	
	function GetPHPDate($date_input){
		$get_result = $this->Select(TABLE_SETTINGS,"","date_format_id","","1");
		if(count($get_result)>0){
			$get_datedata = $this->Select(TABLE_DATE_FORMATS,"is_active=1 and date_format_id='".$get_result[0]['date_format_id']."'","date_format_php");
			if(count($get_datedata)>0) {
				$date_converted = date($get_datedata[0]['date_format_php'],strtotime($date_input));
			} else {
				$date_converted = date("Y-m-d",strtotime($date_input));
			}
		} else {
			$date_converted = date("Y-m-d",strtotime($date_input));
		}
		return $date_converted;
	}
	function GetPHPDateFormat(){
		$get_result = $this->Select(TABLE_SETTINGS,"","date_format_id","","1");
		if(count($get_result)>0){
			$get_datedata = $this->Select(TABLE_DATE_FORMATS,"is_active=1 and date_format_id='".$get_result[0]['date_format_id']."'","date_format_php");
			if(count($get_datedata)>0) {
				$date_format = $get_datedata[0]['date_format_php'];
			} else {
				$date_format = "Y-m-d";
			}
		} else {
			$date_format = "Y-m-d";
		}
		return $date_format;
	}
	
	function GetMYSQLDateFormat(){
		$get_result = $this->Select(TABLE_SETTINGS,"","date_format_id","","1");
		if(count($get_result)>0){
			$get_datedata = $this->Select(TABLE_DATE_FORMATS,"is_active=1 and date_format_id='".$get_result[0]['date_format_id']."'","date_format_mysql");
			if(count($get_datedata)>0) {
				$date_format = $get_datedata[0]['date_format_mysql'];
			} else {
				$date_format = "%Y-%m-%d";
			}
		} else {
			$date_format = "%Y-%m-%d";
		}
		return $date_format;
	}
	
	function GetSSLDD($selected="disabled"){
		$get_result = array("disabled"=>"Disabled", "enabled"=>"Enabled");
		foreach($get_result as $key=>$val) {
			$data .= '<option value="'.$key.'"';
			//if($selected == $key ) $data .= ' selected="selected"';
			$data .= '>'.$val.'</option>';
		}
		return $data;
	}
	function GetDisabledFrontEndDD($selected){
		$get_result = array("under_construction"=>"Under Construction","under_maintainance"=>"Under Maintenance");
		foreach($get_result as $key=>$val) {
			$data .= '<option value="'.$key.'"';
			//if($selected == $key ) $data .= ' selected="selected"';
			$data .= '>'.$val.'</option>';
		}
		return $data;
	}
	
	function GetCaptchaDD($selected){
		$arr = array("-1"=>"Always In-Active","0"=>"Always Active","1"=>"Activate after 1 failed attempt","2"=>"Activate after 2 failed attempt","3"=>"Activate after 3 failed attempt");
		foreach($arr as $key=>$val) {
			$data .= '<option value="'.$key.'"';
			if($selected == $key ) $data .= ' selected="selected"';
			$data .= '>'.$val.'</option>';
		}
		return $data;
	}
	
	function Ajax_MultiFormFields($param){
		// param idnex  :      0   |   1   |    2    |   3    |   4     |  5   | 6
		// param format :input_type|inputid|unique_id|required|real_name|js_exp|note
		$param_arr = explode("|",$param);
		if($param_arr[0] == "file"){
			$inputfield = $this->Ajax_AddFormField('file',$param_arr[1]."_".$param_arr[2],"","inputBox2",$param_arr[3],$param_arr[4],$param_arr[5],'maxlength=255 height=25',"","",$param_arr[6]);
			$inputfield .=' <input type="hidden" name="'.$param_arr[1].'_hidden[]" value="'.$param_arr[2].'" / >'; 
		} else if($param_arr[0] == "text"){
			$inputfield = $this->Ajax_AddFormField('text',$param_arr[1]."_".$param_arr[2],"","inputBox2",$param_arr[3],$param_arr[4],$param_arr[5],'maxlength=255 height=25',"","",$param_arr[6]);
			$inputfield .=' <input type="hidden" name="'.$param_arr[1].'_hidden[]" value="'.$param_arr[2].'" / >'; 
		}
		$html = '<div class="row" id="tr_addmore_dynamic_'.$param_arr[1]."_".$param_arr[2].'">
					<div class="column1">&nbsp;</div>
					<div class="column2">'.$inputfield.'</div>
					<div class="column2" style="vertical-align:middle;"><a href="javascript: void(0);" class="remove_link" onclick="javascript: remove_element_'.$param_arr[1].'(\'tr_addmore_dynamic_'.$param_arr[1].'_\','.$param_arr[2].');">[X Remove]</a></div>
					<div class="clear"></div>
				 </div>';
		///<div class="column2"><input type="button" name="btnremove" id="btnremove" value="Remove" class="button" onclick="javascript: remove_element_'.$param_arr[1].'(\'tr_addmore_dynamic_'.$param_arr[1].'_\','.$param_arr[2].');"  / ></div>
		$content = str_replace("\r\n"," ", $html);
		return $content;
	}
	
	function GetSettingArray(){
		$result = $this->Select(TABLE_SETTINGS,"","*","",1);
		$arr = array();
		if(count($result)>0){
			foreach($result as $data){	}
			foreach($data as $key=>$val){
				if($key != "setting_id"){
					if($key == "date_format_id"){
						$date_format_result = $this->Select(TABLE_DATE_FORMATS,"type='date' AND is_active=1 AND date_format_id ='".$val."'","*","",1);
						if(count($date_format_result)>0){
							$arr['date_format_php']=$date_format_result[0]['date_format_php'];
							$arr['date_format_mysql']=$date_format_result[0]['date_format_mysql'];
						}
					} else if($key == "datetime_format_id"){
						$date_format_result = $this->Select(TABLE_DATE_FORMATS,"type='time' AND is_active=1 AND date_format_id ='".$val."'","*","",1);
						if(count($date_format_result)>0){
							$arr['datetime_format_php']=$date_format_result[0]['date_format_php'];
							$arr['datetime_format_mysql']=$date_format_result[0]['date_format_mysql'];
						}
					} else if($key == "application_title" && $this->is_client_url && $this->mArgs[1]=="admin"){
						$arr[$key]=CLIENT_APPLICATION_TITLE." Admin Panel :: Powered By ".$val; 
						$arr['master_admin_sitename']=$val;
					} else if($key == "application_title" && $this->is_client_url && $this->mArgs[1]!="admin"){
						$arr[$key]=CLIENT_APPLICATION_TITLE." :: Powered By ".$val; 
					} else if($key == "application_title" && !$this->is_client_url && $this->mArgs[0]=="admin"){
						$arr[$key]=$val." :: Admin Panel"; 
					} else {
						$arr[$key]=$val;
					}
				}
			}
		}
		return $arr;
	}
	
	function convertDefaultDate($date)
	{ 
		//echo DATE_FORMAT; die;
		$months1=array('January','February','March','April','May','June','July','August','September','October','November','December');
		$months2=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Agu','Sep','Oct','Nov','Dec');
		$cdateString="";
		if(DATE_FORMAT == "m-d-Y"){
			$cstr = explode("-",$date);
			$cdateString = $cstr[2]."-".$cstr[0]."-".$cstr[1];		 
		 } else if(DATE_FORMAT == "Y-m-d"){
			$cstr = explode("-",$date);
			$cdateString = $cstr[0]."-".$cstr[1]."-".$cstr[2];			 
		 } else if(DATE_FORMAT == "d-m-Y"){
			$cstr = explode("-",$date);
			$cdateString = $cstr[2]."-".$cstr[1]."-".$cstr[0];			 
		 } else if(DATE_FORMAT == "j M, Y"){
			$cstr = explode(" ",$date);
			$cmonth = substr($cstr[1],0,(strlen($cstr[1])-1));
			$cmonth = array_search($cmonth,$months2)+1;
			$cdateString = $cstr[2]."-".$cmonth."-".$cstr[0];		 
		 } else if(DATE_FORMAT == "jS M, Y"){
			$cstr = explode(" ",$date);
			$cmonth = substr($cstr[1],0,(strlen($cstr[1])-1));
			$cmonth = array_search($cmonth,$months2)+1;
			$cdateString = $cstr[2]."-".$cmonth."-".substr($cstr[0],0,(strlen($cstr[0])-2));	 
		 } else if(DATE_FORMAT == "j F, Y"){
			$cstr = explode(" ",$date);
			$cmonth = substr($cstr[1],0,(strlen($cstr[1])-1));
			$cmonth = array_search($cmonth,$months1)+1;
			$cdateString = $cstr[2]."-".$cmonth."-".$cstr[0];		 
		 } else if(DATE_FORMAT == "jS F, Y"){
			$cstr = explode(" ",$date);
			$cmonth = substr($cstr[1],0,(strlen($cstr[1])-1));
			$cmonth = array_search($cmonth,$months1)+1;
			$cdateString = $cstr[2]."-".$cmonth."-".substr($cstr[0],0,(strlen($cstr[0])-2));		 
		 } else {
			 $cdateString = $date;
		 }
		 return $cdateString;
		
	}
	
	function GetAddEditCategoryTree($categorySelected,$parentId="",$prefix="",$onlyArr=false) { 
		$get_cat=$this->Select(TABLE_CATEGORY,"parent_id='".$parentId."' and is_deleted=0","*","category");
		if($parentId!=0)
			$prefix.="-  - ";
		if(!$onlyArr){
			if(count($get_cat)>0){
				foreach($get_cat as $cat_arr)
				{	
					$cat_data .= $cat_arr['category_id']."@@@@".$prefix.ucfirst(htmlentities($cat_arr['category'],ENT_QUOTES,'utf-8'))."@@@@".$cat_arr['parent_id'];
					$i++;
					$cat_data .= "####".$this->GetAddEditCategoryTree($categorySelected,$cat_arr['category_id'],$prefix,"");
				}
			} else {
				$cat_data = '';
			}
			return $cat_data;
			//return $category_data;
		} else {
			return $get_cat;
		}
	} //// function ends here
	
	///////// Function to get Filename from source
	function GetFileName ( $fileName ) {
		$info = pathinfo($fileName);
		$file_name =  basename($fileName,'.'.$info['extension']);
		return $file_name;
	}
	
	//////// Funciton to upload images
	function UploadThumbImage($source, $mainDir, $thumbnailDir, $thumbnailSmallDir, $imgwidth='100', $imgheight='100',$thumbFix=false,$bg=false,$heightFix=false) {
		//echo "===".$bg."@@@"; die;
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			$ext = strtolower($ext);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$fileName = $this->GetFileName($source);
			$destination = $mainDir.$fileName.'.'.$ext;
			
			if(copy($source,$destination)) {
				@unlink($source);
				
				if($width>800 || $height>600)
					$this->CreateThumb($fileName,$ext,$mainDir,800,600,$mainDir,$thumbFix,$bg); 
				if($heightFix) {
					$this->CreateThumb($fileName,$ext,$mainDir,65,65,$thumbnailSmallDir,$thumbFix,$bg);
					if($width>$imgwidth || $height>$imgheight) {
						$this->CreateThumbHeight($fileName,$ext,$mainDir,$imgwidth,$imgheight,$thumbnailDir);
					} else
						copy($mainDir.$fileName.'.'.$ext,$thumbnailDir.$fileName.'.'.$ext);
				} else {
					$this->CreateThumb($fileName,$ext,$mainDir,65,65,$thumbnailSmallDir,$thumbFix,$bg);
					if($width>$imgwidth || $height>$imgheight) {
						$this->CreateThumb($fileName,$ext,$mainDir,$imgwidth,$imgheight,$thumbnailDir,$thumbFix,$bg);
					} else { 
						if($thumbFix) {
							$this->CreateThumb($fileName,$ext,$mainDir,$imgwidth,$imgheight,$thumbnailDir,$thumbFix,$bg);
						}
						else { 
							copy($mainDir.$fileName.'.'.$ext,$thumbnailDir.$fileName.'.'.$ext);
						}
					}
				}
				
				return true;
			}
			else
				return false;
		} else {
			return false;
		}
	} //////////// Function ends here
	
	/*function GetCategoryLinkTree($catId, $flag="") {	
		$cat = array();
		$getRes = $this->Select(TABLE_CATEGORY,"category_id = '".$catId."'");
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$cat_name = htmlentities(stripslashes($res['category']));
				if($res['parent_id']!=0) {
					//if($catId==$_GET['cat_id'])
						//$cat[] = $cat_name;
					//else
					$cat[] = ' <a class="navTree" href="javascript: void(0);" onclick="javascript:ModuleObj.showHidden('.$res['category_id'].',\'category\');">'.$cat_name.'</a>';
					$parent_id = $res['parent_id'];
					$cat[] = $this->GetCategoryLinkTree($parent_id, $flag);
				} else { 
					//if($catId==$_GET['cat_id'])
						//$cat[] = $cat_name;
					//else	
					//if($flag!='product')
						$cat[] = ' <a class="navTree" href="javascript: void(0);" onclick="javascript:ModuleObj.showHidden('.$res['category_id'].',\'category\');">'.$cat_name.'</a>';
					//else
						//$cat[] = $cat_name;
				}
			}
		} 
		$cat = array_reverse($cat);
		$cat_tree = implode(" >> ",$cat);
		
		return $cat_tree;
	}*/
	function GetCategoryLinkTree($catId,$ret_id=true) {
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id");
		$cat_name = array();
		if(count($getRes)>0)  {
			$i=0;
			foreach($getRes as $result) {
				//if($result['parent_id']!=0) {
					$ret_arr = $this->GetCategoryLinkTree($result['parent_id'],false);	
					if(count($ret_arr)>0) {
						foreach($ret_arr as $ret) {
							$cat_name[$i] .= $ret;	
							if($result['parent_id']!=0) {
								$cat_name[$i].= $this->GetAdminLinkCategoryTitle($result['parent_id'])." >> ";
							}
							if($ret_id) $cat_name[$i] .=  $this->GetAdminLinkCategoryTitle($catId);
							$i++;
						}
						$i--;
					} else {
						if($result['parent_id']!=0) {
							$cat_name[$i].= $this->GetAdminLinkCategoryTitle($result['parent_id'])." >> ";
						}
						if($ret_id) $cat_name[$i] .= $this->GetAdminLinkCategoryTitle($catId);
					}
					
				//} 
				$i++;
			}
		}  
		return $cat_name;
	}
	function GetAdminLinkCategoryTitle($catId) {
		$ret_val = '<a class="navTree" href="javascript: void(0);" onclick="javascript:ModuleObj.showHidden('.$catId.',\'category\');">'.$this->GetCategoryTitle($catId).'</a>';
		return $ret_val;
	}
	
	function GetCategoryLinkTreeFrontEnd($catId, $selCatId=0) {	
		$cat = array();
		$getRes = $this->Select(TABLE_CATEGORY_REL. ' as CAR LEFT JOIN '. TABLE_CATEGORY .' as CA ON CAR.category_id=CA.category_id'," CA.category_id='".$catId."'", "CAR.category_id,CA.category,CAR.parent_id");	
		$i = 1;	
		if(count($getRes)>0) {
			//$category_header = ' <a class="navTree first" href="'.$this->MakeUrl('product-browse/').'">Top</a> ';
			foreach($getRes as $res) {
				$cat_name = htmlentities(stripslashes($res['category']),ENT_QUOTES,'utf-8');
				if($res['parent_id']!=0) {													
					$cat[] = ' <a class="navTree" href="'.$this->MakeUrl($this->mPageName.'/index/',"cat_id=".$res['category_id'],1).'">'.ucwords(strtolower($cat_name)).'</a>';					
					$parent_id = $res['parent_id'];
					$cat[] = $this->GetCategoryLinkTreeFrontEnd($parent_id);
					
					
				} else {
					//$cat[] = $this->GetMultiCategoryTree($catId);
					//$cat[] = $this->GetCategoryLinkTreeFrontEnd($catId); 
					//echo count($getRes);									
					$cat[] = ' <a class="navTree" href="'.$this->MakeUrl($this->mPageName.'/index/',"cat_id=".$res['category_id'],1).'">'.ucwords(strtolower($cat_name)).'</a>';						
				}
				$i++;	
			}
		}
		//print_r($cat); die;	
		
		
		$cat = array_reverse($cat);		
		if($selCatId!=0)	$cat[] = $this->GetSelectedCategoryLink($selCatId);	
		$cat_tree = implode("  ",$cat);		
		return $cat_tree;
	}
	
	function GetSelectedCategoryLink($catId) {
		$getRes = $this->Select(TABLE_CATEGORY," category_id='".$catId."'", "category");
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$cat_name = htmlentities(stripslashes($res['category']),ENT_QUOTES,'utf-8');
				$cat_link = ' <a class="navTree" href="'.$this->MakeUrl($this->mPageName.'/index/',"cat_id=".$catId,1).'">'.ucwords(strtolower($cat_name)).'</a>';				
			}
		}
		return $cat_link;
	}
	
	function GetParentId($catId) {
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id = '".$catId."'","parent_id");
		$parent_arr = array();
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$parent_arr[] = $res['parent_id'];
			}
		}
		return $parent_arr;
	}
	function GetCategoriesAndSubcategories(){
		$displayCat = array();		
		$catData = $this->Select(TABLE_CATEGORY." as ct LEFT JOIN ".TABLE_CATEGORY_REL." as rt ON ct.category_id = rt.category_id ", " rt.parent_id = 0 AND ct.is_active = 1 AND ct.is_deleted = 0", "ct.category_id, ct.category, ct.title"," ct.category ");
		$result = '<ul class="menu">';
		if(count($catData) > 0) {
			foreach($catData as $key=>$val){
				//$displayCat[$val['category_id']]['category_name'] = $val['category'];
				$subcatData = $this->Select(TABLE_CATEGORY." as ct LEFT JOIN ".TABLE_CATEGORY_REL." as rt ON ct.category_id = rt.category_id ", " rt.parent_id = ".$val['category_id']." AND ct.is_active = 1 AND ct.is_deleted = 0", "ct.category_id, ct.category, ct.title"," ct.category ");
				//$subcatData = $this->Select(TABLE_CATEGORY, " parent_id = ".$val['category_id']. " AND is_active = 1 AND is_deleted = 0", "*"," category ");			
				$result .= '<li id="'.$val['category_id'].'"><a href="'.$this->MakeUrl('product-browse/index/',"cat_id=".$val['category_id'],1).'" title="'.htmlentities(ucwords(strtolower($val['category'])),ENT_QUOTES,'utf-8').'">'.ucwords(strtolower($val['title'])).'<!--[if gte IE 7]><!--></a><!--<![endif]--><!--[if lte IE 6]><table><tr><td><![endif]-->';
				if(count($subcatData) > 0) {
					$result .= '<ul>';
					foreach($subcatData as $subKey=>$subVal) {
						//$displayCat[$val['category_id']]['subcategory_name'][$subVal['category_id']] = $subVal['category'];					
						$result .='<li id="'.$subVal['category_id'].'"><a href="'.$this->MakeUrl('product-browse/index/',"cat_id=".$subVal['category_id'],1).'" class="first" title="'.htmlentities(ucwords(strtolower($subVal['category'])),ENT_QUOTES,'utf-8').'">'.ucwords(strtolower($subVal['category'])).'</a></li>';					
					}
					$result .= '</ul><!--[if lte IE 6]></td></tr></table></a><![endif]-->';
				}
				$result .= '</li>';
			}
		}else { $result .= 'No Categories'; }
		$result .= '</ul>';
		return $result;				
	}
	
	function DeleteCategory($catId){
		$getRes = $this->Select(TABLE_CATEGORY_REL,"parent_id='".$catId."'","category_id");
		if(count($getRes)>0) {
			foreach($getRes as $result) {
				$this->Delete(TABLE_CATEGORY_REL,"category_id='".$result['category_id']."' and parent_id=".$catId."");
				$get_result = $this->Select(TABLE_CATEGORY_REL,"category_id='".$result['category_id']."'","count(category_id) as c_count");
				if($get_result[0]['c_count']==0) {
					$this->DeleteCategory($result['category_id']);
					$this->Update(TABLE_CATEGORY,array('is_deleted'=>'1','updated_date'=>'NOW()'),"category_id IN (".$result['category_id'].")");
				}
			}
		}
		
	}
	function GetHomePageTestimonials(){
		//SELECT *  FROM (  SELECT * FROM rr_testimonials ORDER BY `add_date` DESC LIMIT 20 )a ORDER BY RAND() LIMIT 1;
		//$testData = $this->Select(TABLE_TESTIMONIALS, " is_active = 1 AND is_deleted = 0 ", "*", " RAND()");		
		$testData = $this->Select("(  SELECT * FROM rr_testimonials ORDER BY `add_date` DESC LIMIT 20 )a ", " is_active = 1 AND is_deleted = 0 ", "*", " RAND()",1);		
		$result = array();
		$i = 0;
		if(count($testData) > 0) {
			foreach($testData as $key=>$value) {			
				$result[$i]['submitted_by'] = ucfirst($value['submitted_by']);
				if($value['content']) {
					$value['content'] = strip_tags($value['content']);
					if(strlen($value['content']) > 125) {
						$subStr = substr($value['content'],0,125)." ...";
						$value['content'] = '<span>&#8220;</span>'.$subStr.'<span>&#8221;</span>'.' &nbsp;<a id="testimonialReadMore" href="'.$this->MakeUrl('testimonials/').'">Read More</a>';
					}else {
						$value['content'] = '<span>&#8220;</span>'.$value['content'].'<span>&#8221;</span>';
					}
				}else {
					$value['content'] = "-";
				}
				$result[$i]['content'] = $value['content'];						
				$i++;			
			}
		}
		
		return $result;	
	}
	function GetCustomerTestimonials(){
		$testData = $this->Select(TABLE_TESTIMONIALS, " is_active = 1 AND is_deleted = 0 ", "*", " add_date DESC", 20);
		if(count($testData) > 0) {
			foreach($testData as $val) {
				$result .= '<span class="bold">'.$val['submitted_by'].':</span> '.stripslashes($val['content']).'<br/><br/>';
			}
		}else {
			$result = 'No Testimonials to display';
		}
		return $result;
	}	
	function GetHomePageBrands(){
		$brandData = $this->Select(TABLE_BRAND, " is_active = 1 AND is_deleted = 0 AND display_homepage='y' ", "*"," brand_name ASC");
		$data = "";
		if(count($brandData) > 0) {
			$totBrandCount = count($brandData);
			$tot_columns = 5;
			$rows = ceil($totBrandCount/$tot_columns);
			$i = 0;	
			$j = 0;
			$columns = 1;
			$data='<div class="bottomLinks">
						<div class="linkHeading">Homeschool Products by Brand:</div>';
			$data.='<div class="column'.$columns.'">';	
			foreach($brandData as $key=>$val){	
				/*echo "brand name===========>".$val['brand_name'];
				echo "<br />";	*/		
				$data .='<a href="'.$this->MakeUrl('product-search/index','search=brands&searchText='.base64_encode($val['brand_name'])).'">'.ucfirst($val['brand_name']).'</a><br />';
				$i++;
				$j++;
				if($j%$rows==0 && $i<$totBrandCount) {
					$j=0;
					$columns++;
					$tot_columns--;
					$rows = ceil(($totBrandCount-$i)/$tot_columns);
					$data.='</div><div class="column'.$columns.'">';
				}										
			}	
			$data.='</div>';
			$data.='<div class="clear"></div>           
              		</div>';			
		}
		return $data;
	}
	function GetEmploymentOpportunities(){
		$opportunityData = $this->Select(TABLE_EMPLOYMENT, "  is_active = 1 AND is_deleted = 0 ","*","emp_id DESC");
		$result = "";		
		if(count($opportunityData) > 0){
			$i = 0;			
			foreach($opportunityData as$key=>$val){
				if($i == 0) $className = "sub-H1";
				else $className = "sub-H2";
				$i++;				
				$result.='<div class="'.$className.'">'.ucfirst($val['title']).'</div>
								<div class="RR-Text">'.$val['description'].'</div>
						  <div class="RR-Contact-Info">
							<div class="formTable3">
								  <div class="tableRow3">
									<div class="column-1"><a href="'.$this->MakeUrl('employment-opportunities/apply-now/',"id=".$val['emp_id'],1).'">Apply Now</a></div>
									<div class="column-2">or direct submissions to:</div>
									<div class="column-3">
									Attn: Steve Listwan, Warehouse Manager<br/>
									Rainbow Resource Center, Inc.<br/>
									655 Township Rd. 500E<br/>
									Toulon, IL 61483</div>
								  </div>
							</div>
							</div>';	
			}
		}else {
			$result='';
		}
		return $result;
	}
	function GetFeaturedProductDetails(){
		/*$parentDisabled = $this->GetParentDisabledCategories(0);
		$disabledCategories = $this->GetDisabledCategories();
		$mainCategory = explode(",",rtrim($parentDisabled,","));	
		$finalUpdatedArray = array_unique(array_merge($mainCategory, $disabledCategories));
		$disabled_cat_array = array();	
		if(count($finalUpdatedArray) > 0) {
			foreach($finalUpdatedArray as $fKey=>$fVal){				
				$disabled_cat_array[$fVal] = "'".$fVal."'";
			}
			$disabled_categories_list = implode(",",$disabled_cat_array);
			$disabled_condition = " AND (prodcat.category_id NOT IN (".$disabled_categories_list.")) ";
		}else {
			$disabled_condition = "";
		}*/
		$disabled_condition = "";
		//$productData = $this->SelectPaging(TABLE_PRODUCTS.' as prod LEFT JOIN '.TABLE_PRODUCT_CATEGORIES.' as prodcat ON prod.product_id = prodcat.product_id', "prod.is_deleted=0 AND prod.is_active=1 AND prod.featured_product=1".$disabled_condition,"DISTINCT prod.product_id, prod.product_title, prod.product_number, prod.isbn, prod.grade_id, prod.retail_price,prod.author, prod.rainbow_price,prod.product_image,prod.description","prod.product_title");
		$productData = $this->SelectPaging(TABLE_PRODUCTS.' as prod LEFT JOIN '.TABLE_PRODUCT_CATEGORIES.' as prodcat ON prod.product_id = prodcat.product_id LEFT JOIN '.TABLE_ISBN.' as isb ON prod.product_id = isb.product_id',"prod.is_deleted=0 AND prod.is_active=1 AND prod.featured_product=1".$disabled_condition,"DISTINCT prod.product_id, prod.product_title, prod.product_number, isb.isbn_no, prod.retail_price,isb.author_name, prod.rainbow_price,prod.product_image,prod.description, isb.is_active as isbn_active","prod.product_title");
		//$productData=$this->Select(TABLE_PRODUCTS, " is_active = 1 AND is_deleted = 0 AND featured_product = 1 ", "*", "product_title");
		$result = array();
		$i = 0;
		foreach($productData as $key=>$value) {
			$readMore = false;
			if($value['description'] == "") {
				$catDesc = $this->GetCatIdDescription($value['product_id']);
				if(trim($catDesc) != "") {
					$value['description'] = $catDesc;
				}				
			} 		
			if(trim($value['description'])) {
				$value['description'] = strip_tags($value['description']);
				if(strlen($value['description']) > 125) {
					$subStr = substr($value['description'],0,125)." ...";
					$value['description'] = $subStr.' <br/><a href="'.$this->MakeUrl('product-browse/details',"product_id=".$value['product_id'],1).'">Read More</a>';
				}else {
					$value['description'] = $value['description'];
				}
			}
			$result[$i]['product_url'] = $this->MakeUrl('product-browse/details/',"product_id=".$value['product_id'],1);			
			$result[$i]['product_title'] = ucfirst($value['product_title']);
			$result[$i]['read_more'] = $readMore;
			$result[$i]['product_id'] = ucfirst($value['product_id']);
			$result[$i]['product_number'] = $value['product_number']?sprintf("%06s",$value['product_number']):'-';
			$result[$i]['isbn_no'] = ($value['isbn_active']==1 && $value['isbn_no'])?$value['isbn_no']:'';
			$result[$i]['author_name'] = ($value['isbn_active']==1 && $value['author_name'])?$value['author_name']:'';
			$result[$i]['retail_price'] = $value['retail_price']?"$".sprintf("%01.2f", $value['retail_price']):'-';
			$result[$i]['rainbow_price'] = $value['rainbow_price']?"$".sprintf("%01.2f", $value['rainbow_price']):'-';
			$result[$i]['description'] = $value['description'];
			$result[$i]['product_image'] = is_file(DIR_PRODUCT.$value['product_image'])?SITE_URL.DIR_PRODUCT.$value['product_image']:SITE_URL.DIR_PRODUCT.'default.jpg';
			$result[$i]['product_image_small'] = is_file(DIR_PRODUCT_SMALL.$value['product_image'])?SITE_URL.DIR_PRODUCT_SMALL.$value['product_image']:SITE_URL.DIR_PRODUCT_SMALL.'default.jpg';
			$result[$i]['product_image_medium'] = is_file(DIR_PRODUCT_MEDIUM.$value['product_image'])?SITE_URL.DIR_PRODUCT_MEDIUM.$value['product_image']:SITE_URL.DIR_PRODUCT_MEDIUM.'default.jpg';
			$i++;			
		}
		return $result;
	}
	function GetExhibitScheduleData(){
		//$currentYear = date("Y");
		$minDate = $currentYear.'-1-1';
		$maxDate = $currentYear.'-12-31';		
		//$condition = " AND start_date >= "."'".$minDate."'". " OR end_date>= "."'".$minDate."'";		
		$condition = "";
		$scheduleData	=	$this->Select(TABLE_EVENTS, " is_active = 1 AND is_deleted = 0 ". $condition,"*", "end_date");
		$yearArr = array();	
		foreach($scheduleData as $yKey=>$yYear){
			$splitDate = split('-',$yYear['end_date']);			
			if(!$yearArr[$splitDate[0]])
			{				
				$yearArr[$splitDate[0]] = $splitDate[0]; 
			}
		}
		$collectData = array();
		$i = 0;
			
		foreach($yearArr as $key=>$val){
			$minDate1 = $val.'-1-1';
			$maxDate1 = $val.'-12-31';
			$result.='<div class="curve">
							<div class="leftCurve"><img src="'.SITE_URL.'images/inner/topLeftCurve2.jpg"></div>
							<div class="rightCurve"><img src="'.SITE_URL.'images/inner/topRightCurve2.jpg"></div>
						</div>';
			$result.='<div class="tableContent">';	
			$result.='<div class="tableContentHeading">'.$val.' Exhibit Season</div>';
			$condition = " AND (end_date BETWEEN "."'".$minDate1."'"." AND "."'".$maxDate1."') OR (start_date BETWEEN "."'".$minDate1."'"." AND "."'".$maxDate1."')";			
			$scheduleDataUpdated	=	$this->Select(TABLE_EVENTS, " is_active = 1 AND is_deleted = 0 ".$condition,"*", "start_date");
			
			foreach($scheduleDataUpdated as $dataKey=>$dataValue){
				
				$splitStartDate = split('-',$dataValue['start_date']);
				$eventStartDate = $splitStartDate['1'].'/'.$splitStartDate[2];
				$splitEndDate = split('-',$dataValue['end_date']);
				$eventEndDate = $splitEndDate['1'].'/'.$splitEndDate[2];
				if($dataValue['status'] == "C"){					
					$collectData[$val][$dataValue['status']][$i]['event_date'] = $eventStartDate.'&nbsp;-&nbsp;'.$eventEndDate;
					$collectData[$val][$dataValue['status']][$i]['title'] =   $dataValue['title'];
					$collectData[$val][$dataValue['status']][$i]['venue'] =   $dataValue['venue'];
					$collectData[$val][$dataValue['status']][$i]['phone'] =   $dataValue['phone'];
					$collectData[$val][$dataValue['status']][$i]['website'] =   $dataValue['website'];
				}
				if($dataValue['status'] == "NI"){
					if($dataValue['phone']){
						$ni_phone_display =  $dataValue['phone'].'<br/>';
					}else{
						$ni_phone_display='';
					}
					$collectData[$val][$dataValue['status']][$i]['event_date'] = $eventStartDate.'&nbsp;-&nbsp;'.$eventEndDate;
					$collectData[$val][$dataValue['status']][$i]['title'] =   $dataValue['title'];
					$collectData[$val][$dataValue['status']][$i]['venue'] =   $dataValue['venue'];
					$collectData[$val][$dataValue['status']][$i]['phone'] =    $dataValue['phone'];
					$collectData[$val][$dataValue['status']][$i]['website'] =   $dataValue['website'];
				}
				if($dataValue['status'] == "A"){
					if($dataValue['phone']){
						$applied_phone_display =  $dataValue['phone'].'<br/>';
					}else{
						$applied_phone_display='';
					}
					$collectData[$val][$dataValue['status']][$i]['event_date'] = $eventStartDate.'&nbsp;-&nbsp;'.$eventEndDate;
					$collectData[$val][$dataValue['status']][$i]['title'] =   $dataValue['title'];
					$collectData[$val][$dataValue['status']][$i]['venue'] =   $dataValue['venue'];
					$collectData[$val][$dataValue['status']][$i]['phone'] =   $dataValue['phone'];
					$collectData[$val][$dataValue['status']][$i]['website'] =   $dataValue['website'];
				}
				if($dataValue['status'] == "IA"){
					if($dataValue['phone']){
						$ia_phone_display =  $dataValue['phone'].'<br/>';
					}else{
						$ia_phone_display='';
					}
					$collectData[$val][$dataValue['status']][$i]['event_date'] = $eventStartDate.'&nbsp;-&nbsp;'.$eventEndDate;
					$collectData[$val][$dataValue['status']][$i]['title'] =   $dataValue['title'];
					$collectData[$val][$dataValue['status']][$i]['venue'] =   $dataValue['venue'];
					$collectData[$val][$dataValue['status']][$i]['phone'] =   $dataValue['phone'];
					$collectData[$val][$dataValue['status']][$i]['website'] =   $dataValue['website'];
				}
				$i++;
			}
			if($collectData[$key]['C'] > 0) {					
			$result.='<div class="tableContentSub-Text">Exhibits we are confirmed to attend</div>';
			$result.='<div class="tableContentText1">
						<div class="formTable">';
							foreach($collectData[$key]['C'] as $cKey=>$cVal){
								if($cVal['phone']){
									$confirmed_phone_display = $cVal['phone'].'<br/>';	
								}else{
									$confirmed_phone_display = "";
								}							
								$result.='<div class="tableRow-2">
											<div class="RR-column-1">'.$cVal['event_date'].'<br/></div>
											<div class="RR-column-2">'.$cVal['title'].'<br/></div>
											<div class="RR-column-3">'.$cVal['venue'].'</div>
											<div class="RR-column-4">'.$confirmed_phone_display.'<a href="'.$cVal['website'].'" target="_blank">Website</a></div>
										</div>';
							}
			$result.=	'</div>								
					</div>';
			}
			if(count($collectData[$key]['A']) > 0) {			
			$result.='<div class="tableContentSub-Text">Exhibits we have applied to, but are not confirmed</div>';
			$result.='<div class="tableContentText1">
						<div class="formTable">';											
							foreach($collectData[$key]['A'] as $aKey=>$aVal){
								if($aVal['phone']){
									$applied_phone_display = $aVal['phone'].'<br/>';	
								}else{
									$applied_phone_display = "";
								}	
								$result.='<div class="tableRow-2">
											<div class="RR-column-1">'.$aVal['event_date'].'<br/></div>
											<div class="RR-column-2">'.$aVal['title'].'<br/></div>
											<div class="RR-column-3">'.$aVal['venue'].'</div>
											<div class="RR-column-4">'.$applied_phone_display.'<a href="'.$aVal['website'].'" target="_blank">Website</a></div>
										</div>';
							}																
			$result.=	'</div>								
					</div>';
			}
			if(count($collectData[$key]['IA']) > 0) {	
			$result.='<div class="tableContentSub-Text">Not Yet Applied - But intending to exhibit.</div>';
			$result.='<div class="tableContentText1">
						<div class="formTable">';						
							foreach($collectData[$key]['IA'] as $iaKey=>$iaVal){
								if($iaVal['phone']){
									$ia_phone_display = $iaVal['phone'].'<br/>';	
								}else{
									$ia_phone_display = "";
								}	
								$result.='<div class="tableRow-2">
											<div class="RR-column-1">'.$iaVal['event_date'].'<br/></div>
											<div class="RR-column-2">'.$iaVal['title'].'<br/></div>
											<div class="RR-column-3">'.$iaVal['venue'].'</div>
											<div class="RR-column-4">'.$ia_phone_display.'<a href="'.$iaVal['website'].'" target="_blank">Website</a></div>
										</div>';
							}															
			$result.=	'</div>								
					</div>';
			}			
			if(count($collectData[$key]['NI']) > 0) {
			$result.='<div class="tableContentSub-Text">Not Invited. We have requested an invitation, but have not been invited to the exhibits below. Attendance is by invitation only</div>';
			$result.='<div class="tableContentText1">
						<div class="formTable">';							
								foreach($collectData[$key]['NI'] as $niKey=>$niVal){
									if($niVal['phone']){
										$ni_phone_display = $niVal['phone'].'<br/>';	
									}else{
										$ni_phone_display = "";
									}
									$result.='<div class="tableRow-2">
											<div class="RR-column-1">'.$niVal['event_date'].'<br/></div>
											<div class="RR-column-2">'.$niVal['title'].'<br/></div>
											<div class="RR-column-3">'.$niVal['venue'].'</div>
											<div class="RR-column-4">'.$ni_phone_display.'<a href="'.$niVal['website'].'" target="_blank">Website</a></div>
										</div>';
								}
															
			$result.=	'</div>								
					</div>';
			}
			$result.='</div>';
			$result.='<div class="curve">
					  	<div class="leftCurve"><img src="'.SITE_URL.'images/inner/topLeftCurve2.jpg"></div>
						<div class="rightCurve"><img src="'.SITE_URL.'images/inner/topRightCurve2.jpg"></div>
					  </div>';
			$result.='<div class="exhibitScheduleSpacer"></div>';
		}
			
		return $result;
		/*foreach($scheduleDataUpdated as $key=>$value){
				if($value['end_date'] <= $minDate || $value['end_date']>= $maxDate){
					echo "Display Event Whose End Date is less than Current Date";
					echo "<br>";
				}
				else if($value['start_date'] >= $minDate && $value['start_date'] <= $maxDate){
					$rdp = split('-',$value['end_date']);
					if($rdp[0] > $currentYear){
						echo "Display Event whose end year is greater than todays year however start year is equal to current year";
						echo "<br>";
					}
				}				
			}		*/	
		/*foreach($scheduleData as $key=>$val){
			$start_year = explode('-',$val['start_date']);
			$end_year = explode('-',$val['end_date']);			
			if($start_year[2] == $end_year[2] && $val['start_date'] ){				
				//echo "I am in Start year ".$start_year[2];
				//echo "<br>";				
				echo "I am in Start year ".$start_year[2];
				echo "<br>";
			}
			if($start_year[2] != $end_year[2]){
				echo "I am in Start year ".$start_year[2];
				echo "<br>";
				echo "I am in End year ".$end_year[2];
				echo "<br>";
			}
		}*/
	}
	function GetExhibitTypes(){
		$exhibitData	=	$this->Select(TABLE_EXHIBIT_TYPE, " is_active = 1 AND is_deleted = 0 ","*");
		$result = array();
		foreach($exhibitData as $key=>$value){
			$result[$value['type_id']] = $value['description'];
		}
		return $result;
	}
	function GetExhibitScheduleDataUpdated(){
		//$currentYear = date("Y");
		$minDate = $currentYear.'-1-1';
		$maxDate = $currentYear.'-12-31';		
		//$condition = " AND start_date >= "."'".$minDate."'". " OR end_date>= "."'".$minDate."'";		
		$condition = "";
		$scheduleData	=	$this->Select(TABLE_EXHIBIT_SCHEDULE, " is_active = 1 AND is_deleted = 0 ". $condition,"*", "end_date");
		$yearArr = array();	
		foreach($scheduleData as $yKey=>$yYear){
			/*$splitEndDate = split('-',$yYear['end_date']);			
			if(!$yearArr[$splitEndDate[0]])
			{				
				$yearArr[$splitEndDate[0]] = $splitEndDate[0]; 
			}*/
			$splitStartDate = split('-',$yYear['start_date']);			
			if(!$yearArr[$splitStartDate[0]])
			{				
				$yearArr[$splitStartDate[0]] = $splitStartDate[0]; 
			}
		}
		sort($yearArr);				
		$collectData = array();
		$i = 0;			
		foreach($yearArr as $key=>$val){
			$collectData[$val]['years'] = $val;
			$minDate1 = $val.'-1-1';
			$maxDate1 = $val.'-12-31';			
			//$exhibitType = array('IA'=>'Intending To Apply','NI'=>'Not Invited','C'=>'Confirmed','A'=>'Applied');
			$exhibitType = $this->GetExhibitTypes();
			$testArr = array();
			foreach($exhibitType as $eKey=>$eVal){
				//$condition = " AND ((end_date BETWEEN "."'".$minDate1."'"." AND "."'".$maxDate1."')) AND status='".$eKey."'";
				$condition = " AND type='".$eKey."' AND ((end_date BETWEEN "."'".$minDate1."'"." AND "."'".$maxDate1."') OR (start_date BETWEEN "."'".$minDate1."'"." AND "."'".$maxDate1."'))";
				//$collectData[$val]['types'][$eKey] = $eVal;			
				$scheduleDataUpdated	=	$this->Select(TABLE_EXHIBIT_SCHEDULE, " is_active = 1 AND is_deleted = 0 ".$condition,"*", "start_date");									
				//echo "<pre>";
				//print_r($scheduleDataUpdated);
				$collectData[$val][$eKey]['type_text'] = $eVal;
				if(count($scheduleDataUpdated>0)){
					foreach($scheduleDataUpdated as $sKey=>$sVal){
						$splitStartDate = split('-',$sVal['start_date']);
						$eventStartDate = $splitStartDate['1'].'/'.$splitStartDate[2];
						$splitEndDate = split('-',$sVal['end_date']);
						$eventEndDate = $splitEndDate['1'].'/'.$splitEndDate[2];						
						$collectData[$val][$eKey]['events'][$sVal['event_id']]['date'] = $eventStartDate.'&nbsp;-&nbsp;'.$eventEndDate;
						$collectData[$val][$eKey]['events'][$sVal['event_id']]['title'] = $sVal['title'];
						$collectData[$val][$eKey]['events'][$sVal['event_id']]['phone'] = $sVal['phone'];
						$collectData[$val][$eKey]['events'][$sVal['event_id']]['venue'] = $sVal['venue'];
						$collectData[$val][$eKey]['events'][$sVal['event_id']]['building'] = $sVal['building'];
						$collectData[$val][$eKey]['events'][$sVal['event_id']]['website'] = $sVal['website'];												
					}
				}		
			}		
		}			
		foreach($collectData as $cKey=>$cValue){
			$result.='<div class="curve">
						<div class="leftCurve"><img src="'.SITE_URL.'images/inner/topLeftCurve2.jpg"></div>
						<div class="rightCurve"><img src="'.SITE_URL.'images/inner/topRightCurve2.jpg"></div>
					</div>';	
			$result.='<div class="tableContent">';	
			$result.='<div class="tableContentHeading">'.$cValue['years'].' Exhibit Season</div>';
			foreach($cValue as $rKey=>$rVal){
				if($rKey != "years"){
					if(count($rVal['events']) > 0){
						$result.='<div class="tableContentSub-Text">'.$rVal['type_text'].'</div>';
						foreach($rVal['events'] as $eKey=>$eVal){
							$result.='<div class="tableRow-2">
												<div class="RR-column-1">'.$eVal['date'].'<br/></div>
												<div class="RR-column-2">'.$eVal['title'].'<br/></div>
												<div class="RR-column-3">'.$eVal['venue'].'<br/>'.$eVal['building'].'</div>
												<div class="RR-column-4">'.$eVal['phone'].'<br/><a href="'.$eVal['website'].'" target="_blank">Website</a></div>
											</div>';
						}
					}
				}
			}
			$result.='</div>';
			$result.='<div class="curve">
					<div class="leftCurve"><img src="'.SITE_URL.'images/inner/topLeftCurve2.jpg"></div>
					<div class="rightCurve"><img src="'.SITE_URL.'images/inner/topRightCurve2.jpg"></div>
				  </div>';
			$result.='<div class="exhibitScheduleSpacer"></div>';
		}	
		//echo "<pre>";
		//print_r($collectData);			
		return $result;		
	}
	/*function GetEmploymentTitle($Selected,$onlyArr=false) {		
		$get_result=$this->Select(TABLE_EMPLOYMENT,"is_active='1' and is_deleted=0","*", "title DESC");
		if(!$onlyArr) {
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$title_data .= '<option value="'.$result['emp_id'].'"';
					if($Selected==$result['emp_id'])  $title_data .= 'selected="selected"';
					$title_data .= '>'.htmlentities(ucfirst(stripslashes($result['title']))).'</option>';
				}
			}
			return $title_data;
		} else {
			return $get_result;
		}
	}*/
	function Ajax_GetEmploymentTitles($noSelect=0) {		
		$get_result=$this->Select(TABLE_EMPLOYMENT,"is_active='1' and is_deleted=0","*", "title DESC");	 
		if(count($get_result)>0) {
			$title_data = "<option value=''>--Select--</option>";
			foreach($get_result as $result) {
				if($noSelect != $result['emp_id']) {
					$title_data .= '<option value="'.$result['emp_id'].'"';					
					$title_data .= '>'.htmlentities(ucfirst(stripslashes($result['title']))).'</option>';
				}
			}
		}
		return $title_data;		 
	}
	function GetEmploymentTitle($Selected,$noSelect=0) {		
		$get_result=$this->Select(TABLE_EMPLOYMENT,"is_active='1' and is_deleted=0","*", "title DESC");	 
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				if($noSelect != $result['emp_id']) {
					$title_data .= '<option value="'.$result['emp_id'].'"';
					if($Selected==$result['emp_id'])  $title_data .= 'selected="selected"';
					$title_data .= '>'.htmlentities(ucfirst(stripslashes($result['title']))).'</option>';
				}
			}
		}
		return $title_data;		 
	}
	function GetEmploymentTitleFromId($id){
		$result = $this->Select(TABLE_EMPLOYMENT," emp_id=".$id,"*");
		return ucfirst($result[0]['title']);
	}
	function GetProductCategories($p_id){
		$result = $this->Select(TABLE_CATEGORY, " parent_id =".$p_id." AND is_active = 1 AND is_deleted = 0", "*"," category ");
		$content = "";
		$i = 0;
		foreach($result as $key=>$val){			
			if($i%2 == 0){
				$class_product = "RR-Product-List1";
			}else {
				$class_product = "RR-Product-List2";
			}
			$i++;
			$content.='<div class="'.$class_product.'"><a href="javascript:void(0);">'.ucfirst($val['category']."[0]").'</a></div>';
		}
		return $content;
	}	
	function UploadPDF($source, $prefix="", $Dir) {
		
		$prefix = $prefix."_";
		$new_file=uniqid($prefix);
		$destination = $Dir.$new_file . ".pdf";
		if(copy($source,$destination)){
			@unlink($source);
			return $new_file . '.pdf';
		}
		else
			return false;
	}
	function UploadDOC($source, $prefix="", $Dir, $ext) {
		
		$prefix = $prefix."_";
		$new_file=uniqid($prefix);
		$destination = $Dir.$new_file.'.'.$ext;
		if(copy($source,$destination)){
			@unlink($source);
			return $new_file.'.'.$ext;
		}
		else
			return false;
	}	
	
	
	
	function GetExhibitTypeDD($selected,$onlyArr=false){
		$get_result = $this->Select(TABLE_EXHIBIT_TYPE,"is_deleted=0","*","title");
		if(!$onlyArr){
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$data .= '<option value="'.$result['type_id'].'"';
					if($selected == $result['type_id'] ) $data .= ' selected="selected"';
					$data .= '>'.htmlentities(ucfirst(stripslashes($result['title']))).'</option>';
				}
			}
			return $data;
		} else {
			return $get_result;
		}
	}
	
	function GetCountryDropDown($selected,$onlyArr=false){
		if(!$selected)	$selected=229;
		$get_result=$this->Select(TABLE_COUNTRY,"","*","Name");						
		if(!$onlyArr){
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$data .= '<option value="'.$result['country_id'].'"';
					if($selected == $result['country_id'] ) $data .= ' selected="selected"';
					$data .= '>'.htmlentities(ucfirst(stripslashes($result['Name']))).'</option>';
				}
			}
			return $data;
		}else {
			return $get_result;
		}
	}
	
	function GetGradeDD($selected,$onlyArr=false){
		$get_result = $this->Select(TABLE_GRADE,"is_deleted=0","*","orderno");
		if(!$onlyArr){
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$data .= '<option value="'.$result['grade_id'].'"';
					if($selected == $result['grade_id'] ) $data .= ' selected="selected"';
					$data .= '>'.htmlentities(ucfirst(stripslashes($result['grade_name']))).'</option>';
				}
			}
			return $data;
		} else {
			return $get_result;
		}
	}
	
	function GetBrandDD($selected,$onlyArr=false){
		$get_result = $this->Select(TABLE_BRAND,"is_deleted=0","*","brand_name");
		if(!$onlyArr){
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$data .= '<option value="'.$result['brand_id'].'"';
					if($selected == $result['brand_id'] ) $data .= ' selected="selected"';
					$data .= '>'.htmlentities(ucfirst(stripslashes($result['brand_name']))).'</option>';
				}
			}
			return $data;
		} else {
			return $get_result;
		}
	}
	
	function GetCategoryDD($selected,$onlyArr=false){
		$get_result = $this->Select(TABLE_CATEGORY,"is_deleted=0","*","category");
		if(!$onlyArr){
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$data .= '<option value="'.$result['category_id'].'"';
					if($selected == $result['category_id'] ) $data .= ' selected="selected"';
					$data .= '>'.htmlentities(ucfirst(stripslashes($result['category']))).'</option>';
				}
			}
			return $data;
		} else {
			return $get_result;
		}
	}
	
	function GetAssociateProductDD($selected,$onlyArr=false){
		if(trim($selected)=="")
			$selected	=	"229";
		$get_result = $this->Select(TABLE_PRODUCTS,"is_deleted=0","product_id, product_title, CONCAT(LPAD(product_number,6,'0')) as product_number","product_title");
		if(!$onlyArr){
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$data .= '<option value="'.$result['product_id'].'"';
					if($selected == $result['product_id'] ) $data .= ' selected="selected"';
					$data .= '>'.htmlentities(ucfirst(stripslashes($result['product_title']))).'</option>';
				}
			}
			return $data;
		} else {
			return $get_result;
		}
	}
	
	// check email address if already exist
	function ValidateCustomerEmailId($email) {
		$customer_arr=$this->Select(TABLE_CUSTOMER,"TRIM(e_mail)='".trim($email)."' AND is_deleted=0 ","customer_id");	
		if(count($customer_arr)>0) {
			return false;
		} else {
			return true;
		}
	}
		
	function GetWhatsNewProducts(){
		$prod_result = $this->Select(TABLE_PRODUCTS,"is_deleted=0 AND is_active=1 AND new_product=1 ","*","product_title");	
		if(count($prod_result) > 0) {			
			$i = 0;	
			$j = 0;			
			$columns = 2;
			$result ='<div class="RR-Product-TR">';		
			foreach($prod_result as $key=>$val){
				if($val['product_image']!= "" && file_exists(DIR_PRODUCT_MEDIUM.$val['product_image'])) {
					$prod_image = SITE_URL.DIR_PRODUCT_MEDIUM.$val['product_image'];
				}else {
					$prod_image = SITE_URL.DIR_PRODUCT_MEDIUM."default.jpg";
				}
				$grade_result =  $this->Select(TABLE_GRADE,"is_deleted=0 AND is_active=1 AND grade_id=".$val['grade_id'],"*","grade_name");
				if(count($grade_result) > 0) {
					$grade_name = $grade_result[0]['grade_name'];
				}else {
					$grade_name = "-";
				}
				if($i % 2 == 0) {
					$space_id = "";
				}else {
					$space_id = "space";
				}
				$result.='<div class="RR-Product-TD" id='.$space_id.'>
							<div class="RR-Product-TopCurve">
								<div class="leftCurve"><img src="'.SITE_URL.'images/ProductBrowse-TopLeftCurve.jpg" style="display:block;"></div>
								<div class="rightCurve"><img src="'.SITE_URL.'images/ProductBrowse-TopRightCurve.jpg" style="display:block;"></div>
							</div>
							<div class="RR-Product-Text">
								<div class="RR-H1">'.ucfirst($val['product_title']).'<br> <br></div>
								<div class="RR-Product-ImgDiv">
									<div><a href="product.htm"><img src="'.$prod_image.'" width="81" height="107"/></a></div>
								</div>
								<div class="RR-Product-Disc">
									<div class="RR-Row">Item #:'.$val['product_number'].'</div>
									<div class="RR-Row">ISBN:'.$val['isbn'].'</div>
									<div class="RR-Row">Grades: '.$grade_name.'</div>
									<div class="RR-Row">Author: Marilyn Burns</div>
									<div class="RR-Row">Retail: $'.$val['retail_price'].'</div>
									<div class="RR-Row"><strong>Rainbow Price: $'.$val['rainbow_price'].'</strong></div>
							  </div>
							  <div class="RR-AddCartBtn">
									<div class="RR-Product-Available"></div>
							  </div>
							  <div class="RR-H1"><br><br></div>
							  <div class="RR-AddCartBtn">
								<div class="RR-Add"><a href="javascript:void(0);"></a></div>
									<div class="RR-Product-Available"><a href="javascript:void(0);"><img src="'.SITE_URL.'images/inner/add-wishList.jpg" width="125" height="32" /></a></div>
							  </div>
							</div>
							<div class="RR-Product-BottomCurve">
								<div class="leftCurve12"><img src="'.SITE_URL.'images/ProductBrowse-BottomLeftCurve.jpg" style="display:block;"></div>
								<div class="rightCurve12"><img src="'.SITE_URL.'images/ProductBrowse-BottomRightCurve.jpg" style="display:block;"></div>
							</div>
						</div>';						
				$i++;
				$j++;
				if($j%$columns==0 && $i<count($prod_result)) {
					$j=0;				
					$result.='</div><div class="RR-Product-TR">';
				}		
			}
		}
		$result .= '</div>';
		return $result;	
	}
	function PagingFooterRRBottom($offset,$class='view-link') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		//$gPagingExtraPara=$gPagingExtraPara;
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";
		$content="";
		$content_select="";
		if(trim($offset)=="") $offset=0;
		$tot_pages = $_SESSION['tot_offset'];		
		/*echo "Offset================>".($offset+1);
		echo "<br>";
		echo "Total Offset==============>".$tot_pages;*/
		if($_SESSION['tot_offset']>1) {
			$content_select.='';						
			$start_page = 0;
			$end_page = $tot_pages-1; 
			for($i=$start_page;$i<=$end_page;$i++) {
				if($offset==$i) $selected=' selected="selected"'; else $selected="";
				$content_s.='<option value="'.$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($i).'').'" '.$selected.'>'.($i+1).'</option>';
			}
		$content_select.='
				  <div class="RR-BB-Text4">Page:</div>
					<div class="RR-BB-Select4">
					  <select name="page_number" class="pageNum" onChange="javascript:window.location=this.value;">
							'.$content_s.'
					  </select>
					</div>
				';		 
		}	
		$result = $content_select.$content;
		return $result;
	}	
	function PagingFooterRRUpdated($offset,$class='view-link') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		//$gPagingExtraPara=$gPagingExtraPara;
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";
		$content="";
		if(trim($offset)=="") $offset=0;
		$tot_pages = $_SESSION['tot_offset'];
		if($_SESSION['tot_offset']>1) {
			$content.='<a  class="pre-page"  '.($offset>0?'':'style="visibility:hidden;"').' href="'.$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset=0').'">&nbsp;</a><a class="preArrow1" '.($offset>0?'':'style="visibility:hidden;"').' href="'.$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($offset-1).'').'">&nbsp;</a>';			
			$start_page = $offset - 2;
			if($start_page<0) $start_page=0;
			$end_page = $start_page + 4;
			if($end_page>=($tot_pages-1)) {
				$end_page = $tot_pages-1;
				if(($end_page-4)>0) $start_page=$end_page-4;
				else $start_page=0;
			}
			for($i=$start_page;$i<=$end_page;$i++) {
				if($offset==$i) $selected=" current"; else $selected="";
				$content.='<a class="'.$selected.'" href="'.$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($i).'').'">'.($i+1).'</a>';
			}
			$content.='<a class="nextArrow1" '.($offset<$tot_pages-1?'':'style="visibility:hidden;"').' href="'.$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($offset+1).'').'">&nbsp;</a><a class="next-page" '.($offset<$tot_pages-1?'':'style="visibility:hidden;"').' href="'.$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($tot_pages-1).'').'">&nbsp;</a>';
			 
		}
		return $content;
	}
	function PagingFooterRRTop($offset,$class='view-link'){			
		global $_SESSION,$_SERVER,$gPagingExtraPara;		
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";
		$content="";
		$content_select="";
		if(trim($offset)=="") $offset=0;
		$tot_pages = $_SESSION['tot_offset'];				
		if($_SESSION['tot_offset']>1) {					
			$start_page = $offset - 2;
			if($start_page<0) $start_page=0;
			$end_page = $start_page + 4;
			if($end_page>=($tot_pages-1)) {
				$end_page = $tot_pages-1;
				if(($end_page-4)>0) $start_page=$end_page-4;
				else $start_page=0;
			}
			for($i=$start_page;$i<=$end_page;$i++) {
				if($offset==$i) $selected=' selected="selected"'; else $selected="";
				$content_s.='<option value="'.$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($i).'').'" '.$selected.'>'.($i+1).'</option>';
			}
			$content_select.='<div class="RR-BBColumn1">
						<div class="RR-BB-Text1">Page:</div>
						<div class="RR-BB-Select1">
						  <select name="page_number" class="pageNum2" onChange="javascript:window.location=this.value;">
								'.$content_s.'
						  </select>
						</div>
						</div>';			 
		}		
		$result = $content_select;
		return $result;	
	}
	function PagingFooterRRSort($catId,$sort,$order,$url){
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		$selected = "";		
		if($sort == "product_title" && $order == "ASC"){
			$title_selected_asc = 'selected="selected"';
		}if($sort == "product_title" && $order == "DESC"){
			$title_selected_desc = 'selected="selected"';
		}if($sort == "grade_id" && $order == "ASC"){
			$grade_selected_asc = 'selected="selected"';
		}if($sort == "grade_id" && $order == "DESC"){
			$grade_selected_desc = 'selected="selected"';
		}if($sort == "rainbow_price" && $order == "ASC"){
			$price_selected_asc = 'selected="selected"';
		}if($sort == "rainbow_price" && $order == "DESC"){
			$price_selected_desc = 'selected="selected"';
		}
		
		$content = '<div class="RR-BBColumn2">
							<div class="RR-BB-Text">Sort by</div>
						  <div class="RR-BB-Select">
							<select name="page_number" class="pageNum2" id="sortProduct">
									<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=product_title&order=ASC',1).'" '.$title_selected_asc.'>Title (ascending)</option>
									<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=product_title&order=DESC',1).'" '.$title_selected_desc.'>Title (descending)</option>
									<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=grade_id&order=ASC',1).'" '.$grade_selected_asc.'>Grade (ascending)</option>
									<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=grade_id&order=DESC',1).'" '.$grade_selected_desc.'>Grade (descending)</option>
									<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=rainbow_price&order=ASC',1).'" '.$price_selected_asc.'>Price (ascending)</option>
									<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=rainbow_price&order=DESC',1).'" '.$price_selected_desc.'>Price (descending)</option>
							</select>
						  </div>
							<div class="RR-Submit"><input type="image" src="'.SITE_URL.'images/inner/go.jpg" value="submit" onClick="javascript:window.location=document.getElementById(\'sortProduct\').value;" /></div>
						</div>';
		return $content;
	}
	function PagingFooterRRSortBy($catId,$sort,$order,$url){
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		$selected = "";		
		if($sort == "product_title"){
			$title_selected_asc = 'selected="selected"';
		}if($sort == "grade_id"){
			$grade_selected_asc = 'selected="selected"';
		}if($sort == "rainbow_price"){
			$price_selected_asc = 'selected="selected"';
		}		
		$content = '<div class="RR-BBColumn1">
						<div class="RR-BB-Text1">Sort by</div>
						<div class="RR-BB-Select1">
							<select name="page_number" class="pageNum2" id="sortProduct" onChange="javascript:window.location=this.value;">
								<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=product_title&order='.$order.'',1).'" '.$title_selected_asc.'>Title</option>									
								<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=grade_id&order='.$order.'',1).'" '.$grade_selected_asc.'>Grade</option>									
								<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort=rainbow_price&order='.$order.'',1).'" '.$price_selected_asc.'>Price</option>									
							</select>
						</div>							
					</div>';
		return $content;
	}
	
	function PagingFooterRRSortOrder($catId,$sort,$order,$url){
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		$selected = "";		
		if($order == "ASC"){
			$selected_asc = 'selected="selected"';
		}if($order == "DESC"){
			$selected_desc = 'selected="selected"';
		}		
		$content = '<div class="RR-BBColumn2">
						<div class="RR-BB-Text2">Sort Order</div>
						<div class="RR-BB-Select2">
							<select name="page_number" class="pageNum2" id="sortProduct" onChange="javascript:window.location=this.value;">
								<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort='.$sort.'&order=ASC',1).'" '.$selected_asc.'>Ascending</option>									
								<option value="'.$this->MakeUrl($url,'cat_id='.$catId.'&sort='.$sort.'&order=DESC',1).'" '.$selected_desc.'>Descending</option>										
							</select>
						</div>							
					</div>';
		return $content;
	}
	
	function GetProductSortList(){
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		$content_sort = '<div class="RR-BBColumn2">
							<div class="RR-BB-Text">Sort by</div>
						  <div class="RR-BB-Select">
							<select name="page_number" class="pageNum2">
									<option>Title (ascending)</option>
							</select>
						  </div>
							<div class="RR-Submit"><input type="image" src="'.SITE_URL.'images/inner/go.jpg" value="submit" /></div>
						</div>';
		return $content_sort;
	}
	function GetCatIdArray($cat_id){
		$get_cat=$this->Select(TABLE_CATEGORY_REL.' as CAR LEFT JOIN '.TABLE_CATEGORY.' as CA ON CAR.category_id=CA.category_id',"CAR.parent_id='".$cat_id."' AND CA.is_deleted=0 AND CA.is_active=1","CAR.category_id");					
		if(count($get_cat)>0){
			foreach($get_cat as $cat_arr)
			{	
				$catId .= $cat_arr['category_id'];								
				$catId .= ",".$this->GetCatIdArray($cat_arr['category_id']);				
			}
		}		
		return $catId;		
	}
	function GetProductDetails($get_products,$productId,$is_bargain,$catId,$sort,$order,$offset){
		//echo "<pre>";
		//print_r($get_products);
		if(count($get_products) > 0){
			$type = $is_bargain=='yes' ? 'book' : 'product';
			
			if($is_bargain == "yes" && $this->mPageName =="bargain-books-products") {
				$add_to_cart_button = '<span id="addCartGreen"><a href="javascript:void(0);" class="add-to-cart" rel="'.$type.'-'.$productId.'" onclick="return false;"></a></span>';
			}else {
				//if($get_products[0]['out_of_stock'] == 0 && $get_products[0]['lqflag'] == 0)
				$add_to_cart_button = '<a href="javascript:void(0);" class="add-to-cart" rel="'.$type.'-'.$productId.'" onclick="return false;">Add to Cart</a>';
			}	
			if($get_products[0]['out_of_stock'] == 1) $get_products[0]['lqflag'] = 5;
			$stock_msg = $this->GetStockStatusMsg($get_products[0]['lqflag']);		
			if($is_bargain == "yes" && $this->mPageName =="bargain-books-products" ){
				if($get_products[0]['quantity']>$get_products[0]['purchased_quantity'])
					$btn_stock = '<div class="RR-AddCartBtn">'.$add_to_cart_button.'</div>';
				$yes_bargain = 1;
				$rainbowPrice = 'Rainbow Price: $'.sprintf("%01.2f", $get_products[0]['rainbow_price']).'';
				$bargainBookPrice = '<span class="asterik">Bargain Book Price: $'.sprintf("%01.2f", $get_products[0]['bargain_book_price']).'</span>';
			}else {
				if($get_products[0]['lqflag']>4){
					$extra_stock_css = "padding:10px 0px 0px 10px";
				}
				$btn_stock = (($get_products[0]['out_of_stock']==0 && ($get_products[0]['lqflag']==0 || ($get_products[0]['lqflag'] != 0 && $get_products[0]['avl_quantity'] != 0)))?'<div class="RR-AddCartBtn">'.$add_to_cart_button.'</div>':'').(($get_products[0]['out_of_stock']==1 || $get_products[0]['avl_quantity']==0)?'<div><span class="stock-msg"  style="text-align:left;'.$extra_stock_css.'">'.$stock_msg.'</span></div>':'');
				$yes_bargain = 0;
				$rainbowPrice = '<span class="asterik">Rainbow Price: $'.sprintf("%01.2f", $get_products[0]['rainbow_price']).'</span>';
				$bargainBookPrice = 'Bargain Book Price: $'.sprintf("%01.2f", $get_products[0]['bargain_book_price']).'';
			}
			
			$grade_name = $this->GetGradeString($get_products[0]['grade_start_id'],$get_products[0]['grade_end_id']);			
			if(is_file(DIR_PRODUCT.$get_products[0]['product_image'])) {
				$mediumImage = SITE_URL.DIR_PRODUCT_MEDIUM.$get_products[0]['product_image'];
				$largeImage = SITE_URL.DIR_PRODUCT.$get_products[0]['product_image'];
				$imageTitle = $get_products[0]['product_title'];
				$mediumPrimaryImage = '<a href="'.$largeImage.'" title="'.$imageTitle.'" rel="lightbox[]"><img src="'.$mediumImage.'" alt="" title="'.$imageTitle.'" /></a>';
			}else {
				$mediumImage = SITE_URL.DIR_PRODUCT_MEDIUM.'default.jpg';
				$imageTitle = $get_products[0]['product_title'];
				$mediumPrimaryImage = '<img src="'.$mediumImage.'" alt="" title="'.$imageTitle.'" />';
			}			
			 
			
			$result = '<div class="RR-ProductMain">
						<div class="RR-Product-Img">'.$mediumPrimaryImage.'</div>
						<div class="RR-Product-Discrip">
							<div class="RR-Product-Heading">'.ucfirst($get_products[0]['product_title']).'</div>
							<div class="RR-Product-Content">
								<div class="RR-Product-Text">
									<div class="RR-Product-Arrow"></div>
									<div class="RR-Product-ItemText">Item #: '.sprintf("%06s",$get_products[0]['product_number']).'</div>
								</div>
								'.( $get_products[0]['isbn_active'] == 1 && (trim($get_products[0]['isbn_no'])!='')?'<div class="RR-Product-Text">
									<div class="RR-Product-Arrow"></div>
									<div class="RR-Product-ItemText">ISBN: '.($get_products[0]['isbn_no']).'</div>
								</div>':'').'
								'.( $get_products[0]['isbn_active'] == 1 && (trim($get_products[0]['ean'])!='')?'<div class="RR-Product-Text">
									<div class="RR-Product-Arrow"></div>
									<div class="RR-Product-ItemText">EAN: '.($get_products[0]['ean']).'</div>
								</div>':'').'
								'.(trim($grade_name)!=''?'<div class="RR-Product-Text">
									<div class="RR-Product-Arrow"></div>
									<div class="RR-Product-ItemText">Grades: '.$grade_name.'</div>
								</div>':'').'
								'.( $get_products[0]['isbn_active'] == 1 && (trim($get_products[0]['author_name'])!='')?'<div class="RR-Product-Text">
									<div class="RR-Product-Arrow"></div>
									<div class="RR-Product-ItemText" title="'.ucfirst($get_products[0]['author_name']).'">Author: '.ucfirst($get_products[0]['author_name']).'</div>
								</div>':'').'
								'.($get_products[0]['retail_price']>0!=''?'<div class="RR-Product-Text">
										<div class="RR-Product-Arrow"></div>
										<div class="RR-Product-ItemText">Retail: $'.sprintf("%01.2f", $get_products[0]['retail_price']).'</div>
									</div>':'').'';
								if($get_products[0]['rainbow_price']>0) {
									$result.=' 
									'.($get_products[0]['rainbow_price']>0!=''?'<div class="RR-Product-Text">
										<div class="RR-Product-Arrow"></div>
										<div class="RR-Product-ItemText"> '.$rainbowPrice.'</div>
									</div>':'').'';
								}else if($get_products[0]['rainbow_price'] == 0) {
									$result.= '<div class="RR-Product-Text">
													<div class="RR-Product-Arrow"></div>
													<div class="RR-Product-ItemText">Rainbow Price: FREE</div>
												</div>';
								}
								if($get_products[0]['bargain_book_price'] >0  && $this->mPageName =="bargain-books-products") {							 
									$result.='<div class="RR-Product-Text">
													<div class="RR-Product-Arrow"></div>
													<div class="RR-Product-ItemText">'.($get_products[0]['quantity']-$get_products[0]['purchased_quantity']).' available </div>
												</div>
												'.($yes_bargain==1?'<div class="RR-Product-Text">
													<div class="RR-Product-Arrow"></div>
													<div class="RR-Product-ItemText">'.$bargainBookPrice.'</div>
												</div>':'').'';
								}else if($get_products[0]['bargain_book_price'] == 0 && $this->mPageName =="bargain-books-products") {
									$result.= '<div class="RR-Product-Text">
													<div class="RR-Product-Arrow"></div>
													<div class="RR-Product-ItemText"> '.$get_products[0]['quantity'].' available </div>
												</div>
												<div class="RR-Product-Text">
													<div class="RR-Product-Arrow"></div>
													<div class="RR-Product-ItemText"><span class="asterik">Bargain Book Price: FREE</span></div>
												</div>';
								}
								$result.=''.($get_products[0]['is_backorder']==1 && $is_bargain=='no'?'<div class="RR-Product-Text">
									<div class="RR-Product-ItemText"><span class="stock-msg-updated"><strong>Publisher Backorder: due '.$get_products[0]['bo_due_date'].'</strong></span></div>
								</div>':'').'
								'.(trim($get_products[0]['note1'])!=""?'<div class="clear"></div><div class="note-details"><span class="stock-msg"  style="text-align:left;">'.$get_products[0]['note1'].'</span></div>':'').'
								'.(trim($get_products[0]['note2'])!=""?'<div class="clear"></div><div class="note-details"><span class="stock-msg"  style="text-align:left;">'.$get_products[0]['note2'].'</span></div>':'').'
								'.$btn_stock.'
							</div>
						</div>
					</div>';											
			if($get_products[0]['is_bargain'] == 1 && $this->mPageName !="bargain-books-products" && ($get_products[0]['quantity']-$get_products[0]['purchased_quantity'])>0 ) {
				$result .='<div class="RR-EmptyDiv"></div><div class="RR-EmptyDiv"></div><div class="list-bragain-msg" style="text-align:center;">
										<a href="'.$this->MakeUrl('bargain-books-products/details/',"product_id=".$get_products[0]['product_id']."&offset=".$offset.'&sort='.$sort.'&product_id='.$productId.'&order='.$order.'&cat_id='.$catId.'&is_bargain=yes&backurl='.$this->mPageName.'/details',1).'">'.($get_products[0]['quantity']-$get_products[0]['purchased_quantity']).' Bargain Version is available @ '.($get_products[0]['bargain_book_price'] > 0 ? '$'.sprintf("%01.2f",$get_products[0]['bargain_book_price']):'FREE').'</a>
								  </div>';
			}
			if($yes_bargain == 0) { 
				$result.='<div class="RR-Product-view">';
							if(is_file(DIR_PRODUCT_SMALL.$get_products[0]['product_image']))
							$result.='<div class="RR-Product-SH1">Click to Enlarge</div>';
							$result.='<div class="RR-Product-Small-Img">';
							if(is_file(DIR_PRODUCT_SMALL.$get_products[0]['product_image'])) {
								$primaryImage = SITE_URL.DIR_PRODUCT_SMALL.$get_products[0]['product_image'];
								$primaryLargeImage = SITE_URL.DIR_PRODUCT.$get_products[0]['product_image'];
								$imageTitle = $get_products[0]['product_title'];
								$result.='<div class="RR-Product-Thumb"><table height="100%" width="100%" cellpadding=0 cellspacing=0 border=0><tr><td align=center valign=middle><a href="'.$primaryLargeImage.'" title="'.$imageTitle.'" rel="lightbox[]"><img src="'.$primaryImage.'" alt="" title="'.$imageTitle.'" /></a></td></td></table></div>';
							}						
							$productImages = $this->Select(TABLE_PRODUCT_IMAGES,"product_id=".$get_products[0]['product_id'],"*");
							foreach($productImages as $secondary_images){
								if(is_file(DIR_PRODUCT_SMALL.$secondary_images['image_name'])) {
									$secondaryImage = SITE_URL.DIR_PRODUCT_SMALL.$secondary_images['image_name'];
									$secondaryLargeImage = SITE_URL.DIR_PRODUCT.$secondary_images['image_name'];
									$secondaryImageTitle = $secondary_images['real_name'];									
									$result.='<div class="RR-Product-Thumb"><table height="100%" width="100%" cellpadding=0 cellspacing=0 border=0><tr><td align=center valign=middle><a href="'.$secondaryLargeImage.'" title="'.$secondaryImageTitle.'" rel="lightbox[]"><img src="'.$secondaryImage.'" alt="Back" title="'.$secondaryImageTitle.'" /></a></td></td></table></div>';	
								}						
							}																									
							$result.='</div>
						</div>';
			}else {
				$result.='<div class="RR-EmptyDiv"></div><div class="RR-EmptyDiv"></div>';
			}
			if($get_products[0]['caution']!='') {
				$result.= '<div style="float: left">'.$this->GetCautionData($get_products[0]['caution']).'</div>';
			}
			if($get_products[0]['description'] == "") {
				$productDescription = $this->GetCatIdDescription($productId);				
			}else {
				$productDescription = $get_products[0]['description'];
			}
			$result.='<div class="RR-Text" style="text-align:justify;">'.$productDescription.'</div>';					
						
		}
		return $result;	
	}
	function GetCatIdDescription($prodId){
		$catIds = $this->Select(TABLE_PRODUCT_CATEGORIES ,"product_id=".$prodId."","category_id");
		foreach($catIds as $val){
			/*$parentCatIds = $this->GetCategoryAscendent($val['category_id']);
			$parentCatIds[] = $val['category_id'];
			$parentCatIds = array_reverse($parentCatIds);*/
			if($val['category_id'] != "") {
				$parentResult = $this->GetAscendentParentCategories($val['category_id']);			
				$parentCatIds = explode(",",$parentResult);				
				//array_reverse($parentCatIds);				
				if(count($parentCatIds) > 0) {		
					foreach($parentCatIds as $catVal) {
						$catResult = $this->Select(TABLE_CATEGORY,"category_id='".$catVal."'","description");						
						if(trim($catResult[0]['description'])!="") {
							return $catResult[0]['description'];
						}
					}
				}
			}	
		}	 
	}	 
	function GetAscendentParentCategories($catId){
		$cat = array();
		$getRes = $this->Select(TABLE_CATEGORY_REL.' as CAR LEFT JOIN '.TABLE_CATEGORY.' as CA ON CAR.category_id=CA.category_id'," CAR.category_id = '".$catId."'","CAR.category_id,CAR.parent_id");	
		$i = 1;
		$sep = "";	 	
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				 
				if($res['parent_id']!=0) {
					if($res['category_id'] != ""){													
						$sep = ",";
						$cat[]  = $res['category_id'];
					}					
					$parent_id = $res['parent_id'];
					$cat[] = $this->GetAscendentParentCategories($parent_id);				
				} else {				 							
					$cat[] = $res['category_id'];						
				}
				$i++;								
			}
		}	
		//$cat = array_reverse($cat);
		$cat_tree = implode(",",$cat);		 		
		return $cat_tree;
	}
	function GetCategoryUsingProduct($productId){
		$result = $this->Select(TABLE_PRODUCTS.' as prod LEFT JOIN '.TABLE_PRODUCT_CATEGORIES.' as prodcat ON prod.product_id = prodcat.product_id',"prod.product_id=".$productId."","prodcat.category_id as category_id");		
		foreach($result as $key=>$val){
			$cat_arr = $this->GetMultiCategoryTreeRec($val['category_id']);
			$category_header = '';
			if(count($cat_arr)>0) {
				foreach($cat_arr as $val) {
					$breadCrumb .= $sep.'<div style="border-bottom:1px solid #C9F0FF;margin-bottom:4px; padding-bottom:4px;"><a class="navTree first" href="'.$this->MakeUrl($this->mPageName).'">Top</a> '.$val.'<div class="clear"></div></div>';
					$sep = '';
				}
			}
		}
		return $breadCrumb;
	}
	function GetCategoryLinkTreeFrontEndUpdated($acendentCategories) {	
		$cat = array();		
		$i = 1;		
		if(count($acendentCategories)>0) {
			foreach($acendentCategories as $res) {
				if($res) {
					$getRes = $this->Select(TABLE_CATEGORY,"category_id = '".$res."'","category ");	
					$cat_name = htmlentities(stripslashes($getRes[0]['category']),ENT_QUOTES,'utf-8');
					$cat[] = ' <a class="navTree" href="'.$this->MakeUrl($this->mPageName.'/index/',"cat_id=".$res,1).'">'.ucwords(strtolower($cat_name)).'</a>';				
					$i++;	
				}							
			}
		}			
		$cat_tree = implode("  ",$cat);		
		return $cat_tree;
	}
	function GetReturnParentId($ascCat){	
		foreach($ascCat as $result){
			if($result){
				$catResult = $this->Select(TABLE_CATEGORY," category_id=".$result." AND is_deleted=0 AND is_active=1","count(category_id) as count");
				if($catResult[0]['count'] == 0) {
					return false;
				}
				$finalResult = $result;
			}
		}
		return $finalResult;
	}
	
	function GetCategoryUsingProductAdmin($productId){
		$result = $this->Select(TABLE_PRODUCTS.' as prod LEFT JOIN '.TABLE_PRODUCT_CATEGORIES.' as prodcat ON prod.product_id = prodcat.product_id',"prod.product_id='".$productId."'","prodcat.category_id as category_id","prodcat.id");
		 $cat_tree =array();
		foreach($result as $key=>$val){
			$category_tree = $this->GetCategoryTree($val['category_id'],true,' <img src="'.SITE_URL.'images/admin/cat_arraow.jpg" width="20px;" height="10px" align="absmiddle" > ');
			if(trim($category_tree)!='') {
				$cat_tree[] = array('id'=>$val['category_id'],'breadcrumb'=>$category_tree);			
			}
		}
		return $cat_tree;
	}
	
	function GetCategoryUsingCategory($productId){
		$result = $this->Select(TABLE_PRODUCTS.' as prod LEFT JOIN '.TABLE_PRODUCT_CATEGORIES.' as prodcat ON prod.product_id = prodcat.product_id',"prod.product_id='".$productId."'","prodcat.category_id as category_id","prodcat.id");
		 $cat_tree =array();
		foreach($result as $key=>$val){
			$category_tree = $this->GetCategoryTree($val['category_id'],true,' <img src="'.SITE_URL.'images/admin/cat_arraow.jpg" width="20px;" height="10px" align="absmiddle" > ');
			if(trim($category_tree)!='') {
				$cat_tree[] = array('id'=>$val['category_id'],'breadcrumb'=>$category_tree);			
			}
		}
		return $cat_tree;
	}
	
	function GetCategoryLinkTreeFrontEndAdmin($catId) {	
		$cat = array();
		$getRes = $this->Select(TABLE_CATEGORY,"category_id = '".$catId."'");	
		$i = 1;		
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$cat_name = htmlentities(stripslashes($res['category']));
				if($res['parent_id']!=0) {													
					$cat[] = '<img src="'.SITE_URL.'images/admin/cat_arraow.jpg" width="20px;" height="10px" align="absmiddle" >'.ucwords(strtolower($cat_name));					
					$parent_id = $res['parent_id'];
					$cat[] = $this->GetCategoryLinkTreeFrontEndAdmin($parent_id);
					
					
				} else {
					//echo count($getRes);									
					$cat[] = '<span style="font-weight:bold;font-size:11px; font-style:italic">'.ucwords(strtolower($cat_name)).'</span>';						
				}
				$i++;								
			}
		}	
		$cat = array_reverse($cat);			
		$cat_tree = implode("  ",$cat);		
		return $cat_tree;
	}
	
	
	function GetCatIdForSearch($cat_id){
		/*$get_cat=$this->Select(TABLE_CATEGORY,"category_id='".$cat_id."' and is_deleted=0","parent_ids");	
		if(count($get_cat)>0){
			foreach($get_cat as $cat_arr)
			{	
 				$i++;
				$descen = $this->GetCategoryDescendent($cat_id,$cat_arr['parent_ids']);
				if($descen!="")
					$catId .= $descen;
			}
		} 
		return $catId;*/	
		$get_cat=$this->Select(TABLE_CATEGORY_REL .' as CAR',"CAR.parent_id='".$cat_id."'","CAR.category_id");					
		if(count($get_cat)>0){
			foreach($get_cat as $cat_arr)
			{	
				$catId .= $cat_arr['category_id'];								
				$catId .= ",".$this->GetCatIdForSearch($cat_arr['category_id']);				
			}
		}		
		return $catId;	
	}
	function GetCustomerReview($productId,$is_bargain){
		if($is_bargain == "yes"){
			$yes_bargain = 1;
		}else {
			$yes_bargain = 0;
		}
		$customerReviewResult = $this->Select(TABLE_CUSTOMER_REVIEW,"product_id='".$productId."' and is_deleted=0 AND is_active=1","*,DATE_FORMAT(add_date,'".$this->WebSettings['date_format_mysql']."') AS add_date,add_date as check_date ",'add_date ASC');
		if(count($customerReviewResult)>0) {
			$result .= '<div class="curve">
							<div class="leftCurve"><img src="'.SITE_URL.'images/inner/topLeftCurve2.jpg"></div>
							<div class="rightCurve"><img src="'.SITE_URL.'images/inner/topRightCurve2.jpg"></div>
						</div>
						<div class="tableContent">
							<div class="tableContentHeading">Customer Review</div>
							<div class="tableContentText">';	
			foreach($customerReviewResult as $key=>$val){
				$result .= '<div class="formTableApplyNow">';
				$customerName = ucfirst($val['first_name']).'&nbsp;'.ucfirst($val['last_name']);
				if($val['location']){
					$location = "&nbsp;from&nbsp;".$val['location'];
				}
				//$date  = date('m/d/Y',strtotime($val['add_date']));			
				$date  = $val['add_date'];
				$date_format = ' wrote the following';				
				if(strtotime($val['check_date']) != "") {				
					$date_format .= ' on '.$date;
				}
				$date_format.=': ';
				$result .= '<span class="bold">'.$customerName.$location.$date_format.'</span><br/>';
				$result .= nl2br($val['review']);
				$result .= '</div><div class="RR-EmptyDiv"></div>';
			}
			$result .= '</div>
					</div>
					<div class="curve">
						<div class="leftCurve"><img src="'.SITE_URL.'images/inner/topLeftCurve2.jpg"></div>
						<div class="rightCurve"><img src="'.SITE_URL.'images/inner/topRightCurve2.jpg"></div>
					</div>';
		}
		return $result;
	}
	
	function GetSearchBySelect($selected,$onlyArr=false){
		$get_result = array(1=>'Keyword',2=>'Title',3=>'Grade',4=>'Brand',5=>'Item Number',6=>'Author',7=>'ISBN');
		if(!$onlyArr){
			if(count($get_result)>0) {
				foreach($get_result as $key=>$value) {
					$data .= '<option value="'.$key.'"';
					if($selected == $key ) $data .= ' selected="selected"';
					$data .= '>'.htmlentities(ucfirst(stripslashes($value))).'</option>';
				}
			}
			return $data;
		} else {
			return $get_result;
		}
	}
	
	function GetProductTitle($prodId) {
		$getRes = $this->Select(TABLE_PRODUCTS,"product_id='".$prodId."'","product_title");
		return $getRes[0]['product_title'];
	}
	
	function HighlightWords($text, $words){
		$split_words = explode( " " , $words );
		foreach ($split_words as $word)
		{
			$color = "#FFFF66";
			$text = @preg_replace("|($word)|Ui" ,
					   "<span style=\"background:#FFFF66;\"><b>$1</b></span>" , $text );
		}	
	   return $text;
	}	
	function HighLightWord($text,$searchFor){
		$formatedWord = @preg_replace("/(>|^)([^<]+)(?=<|$)/esx","'\\1' . str_replace('" . strtolower($searchFor) . "', '<span style=\"background:#FFFF66;\">" . strtolower($searchFor) . "</span>', '\\2')",$text);	
		$formatedWord = @preg_replace("/(>|^)([^<]+)(?=<|$)/esx","'\\1' . str_replace('" . strtoupper($searchFor) . "', '<span style=\"background:#FFFF66;\">" . strtoupper($searchFor) . "</span>', '\\2')",$formatedWord);
		$formatedWord = @preg_replace("/(>|^)([^<]+)(?=<|$)/esx","'\\1' . str_replace('" . ucfirst(strtolower($searchFor)) . "', '<span style=\"background:#FFFF66;\">" . ucfirst(strtolower($searchFor)) . "</span>', '\\2')",$formatedWord);
		return $formatedWord;
	}
	function GetProductListing($prod_result,$catId=0,$sort="product_title",$order="ASC",$offset=0,$is_bargain="no"){
		if(count($prod_result) > 0) {			
			$i = 0;	
			$j = 0;			
			$columns = 2;
			$result ='<div class="RR-Product-TR-new">';
			$result.='<div class="RR-Product-TableText">Click product title for product reviews, where available</div>';					
			foreach($prod_result as $pkey=>$pval){
				//echo "Out of stock=========>".$pval['out_of_stock'];
				//echo "<br>";
				if($pval['product_image']!= "" && is_file(DIR_PRODUCT_LISTING.$pval['product_image'])) {
					$prod_image = SITE_URL.DIR_PRODUCT_LISTING.$pval['product_image'];
				}else {
					$prod_image = SITE_URL.DIR_PRODUCT_LISTING."default.jpg";
				}
				$grade_name = $this->GetGradeString($pval['grade_start_id'],$pval['grade_end_id']);
				//echo "===============".$grade_name;
				if($i % 2 == 0) {
					$space_id = "";
				}else {
					$space_id = "space";
				}
				if($this->mPageName=='bargain-books-products')
					$type='book';
				else 
					$type='product';
				/*$bargain_book_result = $this->Select(TABLE_BARGAIN_BOOKS, " associate_product_id=".$pval['product_id']."  AND is_active = 1 AND is_deleted = 0", "*");
				$bargain = "";
				if(count($bargain_book_result) > 0) {
					//$bargain = "1 Bargain Book Version is available";
				}*/
				
				if($this->mPageName == "product-search") {
					if($_SESSION['txt_search_by'] == 1){				 				
						//$formatedTitle = $this->HighlightWords($pval['product_title'],$_SESSION['txt_search_text']);			 
						//$formatedTitle = preg_replace("/(>|^)([^<]+)(?=<|$)/esx","'\\1' . str_replace('" . $_SESSION['txt_search_text'] . "', '<span style=\"background:#FFFF66;\">" . ($_SESSION['txt_search_text']) . "</span>', '\\2')",$pval['product_title']);
						$formatedTitle = ucfirst($this->HighlightWord($pval['product_title'],$_SESSION['txt_search_text']));		
						$formatedItemNumber = $this->HighlightWord(sprintf("%06s",$pval['product_number']),$_SESSION['txt_search_text']);						
						$grade_name = $this->HighlightWords($grade_name,$_SESSION['txt_search_text']);				 
						//$formatedAuthorName = $this->HighlightWords(ucfirst(substr($pval['author_name'],0,40)),$_SESSION['txt_search_text']);					 
						$formatedAuthorName = $this->HighlightWord(ucfirst(substr($pval['author_name'],0,40)),$_SESSION['txt_search_text']);	
						$formatedISBN = $this->HighlightWords($pval['isbn_no'],$_SESSION['txt_search_text']);
					}else if($_SESSION['txt_search_by'] == 2) {
						$formatedTitle = ucfirst($this->HighlightWord($pval['product_title'],$_SESSION['txt_search_text']));	
						$formatedItemNumber = sprintf("%06s",$pval['product_number']);
						$formatedAuthorName = ucfirst($pval['author_name']);
						$formatedISBN = $pval['isbn_no'];						
					}else if($_SESSION['txt_search_by'] == 3) {
						$formatedTitle = ucfirst($pval['product_title']);
						$grade_name = $this->HighlightWords($grade_name,$_SESSION['txt_search_text']);	
						$formatedItemNumber = sprintf("%06s",$pval['product_number']);
						$formatedAuthorName = ucfirst($pval['author_name']);
						$formatedISBN = $pval['isbn_no'];
					}else if($_SESSION['txt_search_by'] == 4) {
						$formatedTitle = ucfirst($pval['product_title']);
						$formatedItemNumber = sprintf("%06s",$pval['product_number']);
						$formatedAuthorName = ucfirst($pval['author_name']);
						$formatedISBN = $pval['isbn_no'];
					}else if($_SESSION['txt_search_by'] == 5) {
						$formatedTitle = ucfirst($pval['product_title']);
						$formatedItemNumber = $this->HighlightWord(sprintf("%06s",$pval['product_number']),$_SESSION['txt_search_text']);
						$formatedAuthorName = ucfirst($pval['author_name']);
						$formatedISBN = $pval['isbn_no'];
					}else if($_SESSION['txt_search_by'] == 6){
						$formatedTitle = ucfirst($pval['product_title']);
						$formatedItemNumber = sprintf("%06s",$pval['product_number']);
						$formatedAuthorName = $this->HighlightWord(ucfirst(substr($pval['author_name'],0,40)),$_SESSION['txt_search_text']);
						$formatedISBN = $pval['isbn_no'];
					}else if($_SESSION['txt_search_by'] == 7) {
						$formatedTitle = ucfirst($pval['product_title']);
						$formatedItemNumber = sprintf("%06s",$pval['product_number']);
						$formatedAuthorName = ucfirst($pval['author_name']);
						$formatedISBN = $this->HighlightWords($pval['isbn_no'],$_SESSION['txt_search_text']);
					}
				}else {
					$formatedTitle = ucfirst($pval['product_title']);
					$formatedItemNumber = sprintf("%06s",$pval['product_number']);
					$formatedAuthorName = ucfirst($pval['author_name']);
					$formatedISBN = $pval['isbn_no'];
				}			
				if($is_bargain=="yes") {
					$list_class="green";
					$post_fix_class = "-green";
				} else {
					$list_class="blue";
					$post_fix_class = "";
				}
				$availability_msg = $this->GetAvailabilityStatusMsg($pval['out_of_stock']);				
				if($pval['out_of_stock'] == 1) $pval['lqflag'] = 5;
				$stock_msg = $this->GetStockStatusMsg($pval['lqflag']);							
				$result.='<form name="frm_'.$pval['product_id'].'" method="POST" action="'.$this->MakeUrl('cart/add/',$extra_para).'">'.$this->AddFormField('hidden',"id",$pval['product_id'],"",0).'
						'.$this->AddFormField('hidden',"type","book","",0).'
						<div class="RR-Product-TD-new '.$list_class.'" id='.$space_id.'>							
							<div class="RR-Product-Text">
								<div class="product-heading"><a href="'.$this->MakeUrl('product-browse/details/',"product_id=".$pval['product_id']."&offset=".$offset.'&sort='.$sort.'&order='.$order.'&cat_id='.$catId.'&is_bargain='.$is_bargain.'&backurl='.$this->mPageName.'/index',1).'" alt="'.htmlentities(ucfirst($pval['product_title'])).'" title="'.htmlentities(ucfirst($pval['product_title'])).'">'.$formatedTitle.'</a></div>								
								<div class="product-content">
									<div class="product-img">
										<table height="100%" width="100%" cellpadding=0 cellspacing=0 border=0><tr><td align="center" valign="middle"><a href="'.$this->MakeUrl('product-browse/details/',"product_id=".$pval['product_id']."&offset=".$offset.'&sort='.$sort.'&order='.$order.'&cat_id='.$catId.'&is_bargain='.$is_bargain.'&backurl='.$this->mPageName.'/index',1).'"><img src="'.$prod_image.'"  alt="'.htmlentities(ucfirst($pval['product_title'])).'" title="'.htmlentities(ucfirst($pval['product_title'])).'" /></a></td></tr></table>
										<div class="list-view-details"><a href="'.$this->MakeUrl('product-browse/details/',"product_id=".$pval['product_id']."&offset=".$offset.'&sort='.$sort.'&order='.$order.'&cat_id='.$catId.'&is_bargain='.$is_bargain.'&backurl='.$this->mPageName.'/index',1).'">More Info</a></div>
									</div>
									<div class="Item-description">
										<div class="description-row"><strong>Item #:</strong>'.$formatedItemNumber.'</div>
										'.($pval['isbn_active']==1 &&(trim($pval['isbn_no'])!='')?'<div class="description-row"><strong>ISBN:</strong>'.$formatedISBN.'</div>':'').'									
										'.(trim($grade_name)!=''?'<div class="description-row"><strong>Grades:</strong> '.trim($grade_name).'</div>':'').'
										'.($pval['isbn_active']==1 && (trim($pval['author_name'])!='')?'<div class="description-row" title="'.ucfirst($pval['author_name']).'"><strong>Author:</strong> '.$formatedAuthorName.'</div>':'').'
										'.($pval['retail_price']>0?'<div class="description-row"><strong>Retail:</strong> $'.sprintf("%01.2f",$pval['retail_price']).'</div>':'').'';
										if($pval['rainbow_price'] >0) { 
											$result.=' 
											'.($pval['rainbow_price']>0?'<div class="description-row"><strong>Rainbow Price:</strong> $'.sprintf("%01.2f",$pval['rainbow_price']).'</div>':'').'';
										}else {
											$result.='<div class="description-row"><strong>Rainbow Price:</strong> FREE</div>';
										}
										$result.=''.($is_bargain=="yes"?'<div class="description-row"><strong>Bargain Price:</strong> <strong>$'.sprintf("%01.2f",$pval['bargain_book_price']).'</strong></div>':'').'
									</div>			
									<div class="clear"></div>						
							  </div>
							  <div class="clear"></div>
							  <div class="list-bragain-msg">							  
							  '.($pval['is_bargain']==1 && $is_bargain=="no" && ($pval['quantity']-$pval['purchased_quantity'])>0?'<a href="'.$this->MakeUrl('bargain-books-products/details/',"product_id=".$pval['product_id']."&offset=".$offset.'&sort='.$sort.'&order='.$order.'&cat_id='.$catId.'&is_bargain=yes&backurl='.$this->mPageName.'/index',1).'">'.($pval['quantity']-$pval['purchased_quantity']).' Bargain Version is available @ '.($pval['bargain_book_price'] > 0 ? '$'.sprintf("%01.2f",$pval['bargain_book_price']):'FREE').'</a>':'').'
							  </div>							 		  
							  '.($pval['is_backorder']==1?'
							  <div class="list-bragain-msg"><span class="stock-msg"><strong>Publisher Backorder: due '.$pval['bo_due_date'].'</strong></span></div>':'')
							  .'
							   <div class="view-details-btn">'.(($pval['out_of_stock']==0 && ($pval['lqflag']==0 || ($pval['lqflag'] != 0 && $pval['avl_quantity'] != 0)))?'
                                  	<div class="btn-1"><button class="button3-medium'.$post_fix_class.'  add-to-wishlist" onclick="return false;" rel="product-'.$pval['product_id'].'">Add to Wish List</button></div>
                                    <div class="btn-2"><button class="button3-small'.$post_fix_class.' add-to-cart" onclick="return false;" rel="'.$type.'-'.$pval['product_id'].'">Add to Cart</button></div>
                                  ':'').(($pval['out_of_stock']==1 ||  $pval['avl_quantity'] == 0)?'<span class="stock-msg">'.$stock_msg.'</span>':'').'</div>						  
							</div>						
						</div>
					</form>';						
				$i++;
				$j++;
				if($j%$columns==0 && $i<count($prod_result)) {
					$j=0;				
					$result.='</div><div class="RR-Product-TR-new">';
				}		
			}
			$result .= '</div>';
		}else {
			//$result.='<div class="noProducts">No products to display</div>';
		}
		return $result;
	}
	
	function GetProductListingBargainBooks($prod_result,$catId=0,$sort="product_title",$order="ASC",$offset=0,$is_bargain="no"){
		if(count($prod_result) > 0) {			
			$i = 0;	
			$j = 0;			
			$columns = 2;
			$result ='<div class="RR-Product-TR-new">';
			$result.='<div class="RR-Product-TableText">Click product title for product reviews, where available</div>';					
			foreach($prod_result as $pkey=>$pval){
				//echo "Out of stock=========>".$pval['out_of_stock'];
				//echo "<br>";
				if($pval['product_image']!= "" && is_file(DIR_PRODUCT_LISTING.$pval['product_image'])) {
					$prod_image = SITE_URL.DIR_PRODUCT_LISTING.$pval['product_image'];
				}else {
					$prod_image = SITE_URL.DIR_PRODUCT_LISTING."default.jpg";
				}
				//print_r($pval); die;
				//echo $pval['grade_start_id']."===".$pval['grade_end_id']; die;
				$grade_name = $this->GetGradeString($pval['grade_start_id'],$pval['grade_end_id']);			
				
				if($i % 2 == 0) {
					$space_id = "";
				}else {
					$space_id = "space";
				}
				if($this->mPageName=='bargain-books-products')
					$type='book';
				else 
					$type='product';				
				if($this->mPageName == "product-search") {					
					$containsReplaced = strtolower($_SESSION['txt_search_text']);
					$containsReplaced_Cap =  ucfirst(strtolower($_SESSION['txt_search_text']));
					$containsToBeReplaced = "<strong><span style='background:#FFFF66'>".strtolower($_SESSION['txt_search_text'])."</span></strong>";
					$containsToBeReplaced_Cap = "<strong><span style='background:#FFFF66'>".ucfirst(strtolower($_SESSION['txt_search_text']))."</span></strong>";
					$formatedTitle = $this->getFormatedContent($containsReplaced,$containsToBeReplaced,$pval['product_title']);
					$formatedTitle = $this->getFormatedContent($containsReplaced_Cap,$containsToBeReplaced_Cap,$formatedTitle);
				}else {
					$formatedTitle = ucfirst($pval['product_title']);
				}			
				if($is_bargain=="yes") {
					$list_class="green";
					$post_fix_class = "-green";
				} else {
					$list_class="blue";
					$post_fix_class = "";
				}
				$authorName =  substr($pval['author_name'],0,40);
				$result.='<form name="frm_'.$pval['product_id'].'" method="POST" action="'.$this->MakeUrl('cart/add/',$extra_para).'">'.$this->AddFormField('hidden',"id",$pval['product_id'],"",0).'
						'.$this->AddFormField('hidden',"type","book","",0).'
						<div class="RR-Product-TD-new '.$list_class.'" id='.$space_id.'>							
							<div class="RR-Product-Text">
								<div class="product-heading"><a href="'.$this->MakeUrl('bargain-books-products/details/',"product_id=".$pval['product_id']."&offset=".$offset.'&sort='.$sort.'&order='.$order.'&cat_id='.$catId.'&is_bargain='.$is_bargain.'&backurl='.$this->mPageName.'/index',1).'" alt="'.htmlentities(ucfirst($pval['product_title'])).'" title="'.htmlentities(ucfirst($pval['product_title'])).'">'.$formatedTitle.'</a></div>								
								<div class="product-content">
									<div class="product-img">
										<table height="100%" width="100%" cellpadding=0 cellspacing=0 border=0><tr><td align="center" valign="middle"><a href="'.$this->MakeUrl('bargain-books-products/details/',"product_id=".$pval['product_id']."&offset=".$offset.'&sort='.$sort.'&order='.$order.'&cat_id='.$catId.'&is_bargain='.$is_bargain.'&backurl='.$this->mPageName.'/index',1).'"><img src="'.$prod_image.'"  alt="'.htmlentities(ucfirst($pval['product_title'])).'" title="'.htmlentities(ucfirst($pval['product_title'])).'" /></a></td></tr></table>
										<div class="list-view-details"><a href="'.$this->MakeUrl('bargain-books-products/details/',"product_id=".$pval['product_id']."&offset=".$offset.'&sort='.$sort.'&order='.$order.'&cat_id='.$catId.'&is_bargain='.$is_bargain.'&backurl='.$this->mPageName.'/index',1).'">View Details</a></div>
									</div>
									<div class="Item-description">
										<div class="description-row"><strong>Item #:</strong>'.sprintf("%0.6s",$pval['product_number']).'</div>
										'.($pval['isbn_active']==1 &&(trim($pval['isbn_no'])!='')?'<div class="description-row"><strong>ISBN:</strong>'.$pval['isbn_no'].'</div>':'').'									
										'.(trim($grade_name)!=''?'<div class="description-row"><strong>Grades:</strong> '.ucfirst($grade_name).'</div>':'').'
										'.($pval['isbn_active']==1 && (trim($pval['author_name'])!='')?'<div class="description-row-author" title="'.ucfirst($pval['author_name']).'"><strong>Author:</strong> '.ucfirst($authorName).'</div>':'').'
										'.($pval['retail_price']>0?'<div class="description-row"><strong>Retail:</strong> $'.sprintf("%01.2f",$pval['retail_price']).'</div>':'').'
										<div class="description-row"><strong>Rainbow Price:</strong> '.($pval['rainbow_price']>0?'$'.sprintf("%01.2f", $pval['rainbow_price']):'<strong>FREE</strong>').'</div>';
										if($pval['bargain_book_price'] > 0) {
										$result.=''.($is_bargain=="yes"?'<div class="description-row"><strong>Bargain Price:</strong> <strong>$'.sprintf("%01.2f",$pval['bargain_book_price']).'</strong></div>':'').'';
										}else {
											$result.=''.($is_bargain=="yes"?'<div class="description-row"><strong>Bargain Price:</strong> <strong>FREE</strong></div>':'').'';
										}
									$result.='</div>			
									<div class="clear"></div>						
							  </div>
							  <div class="clear"></div>							 
							  <div class="list-bragain-msg" style="text-align:center;">
									<strong>'.($pval['quantity']-$pval['purchased_quantity']).' available</strong>
							  </div>
							   <div class="view-details-btn">
                                  <!--	<div class="btn-1"><button class="button3-medium'.$post_fix_class.'  add-to-wishlist" onclick="return false;" rel="product-'.$pval['product_id'].'">Add to Wish List</button></div>-->
                                    <div class="btn-2"><button class="button3-small'.$post_fix_class.' add-to-cart" onclick="return false;" rel="'.$type.'-'.$pval['product_id'].'">Add to Cart</button></div>
                                  </div>						  
							</div>						
						</div>
					</form>';						
				$i++;
				$j++;
				if($j%$columns==0 && $i<count($prod_result)) {
					$j=0;				
					$result.='</div><div class="RR-Product-TR-new">';
				}		
			}
			$result .= '</div>';
		}else {
			//$result.='<div class="noProducts">No products to display</div>';
		}
		return $result;
	}
	
	function GetEmpEmails($select_arr, $onlyarr=false, $is_super=false) {
		if($is_super)  $condition = "is_super='0'";    else $condition = "";
		$getRes = $this->Select(TABLE_EMP_EMAILS,$condition,"emp_email_id, IF(is_super=1,CONCAT(email,' [Super Admin]'),email) as email","is_super desc,email asc");
		if(!$onlyarr) {
			if(count($getRes)>0) {
				$data = "";
				foreach($getRes as $result) {
					$data .= "<option  value='".$result['emp_email_id']."'";
					if(in_array($result['emp_email_id'],$select_arr)) $data .= "selected='selected'";
					$data .= ">".stripslashes(htmlentities($result['email']))."</option>";
				}
			}
			return $data;
		} else {
			return $getRes;
		}
	}
	function GetParentCategoryId($catId) {
		$get_cat=$this->Select(TABLE_CATEGORY_REL . ' as CAR LEFT JOIN '. TABLE_CATEGORY.' as CA ON CAR.category_id=CA.category_id ',"CAR.category_id='".$catId."' AND CA.is_deleted=0 AND CA.is_active=1","CAR.parent_id,CAR.category_id");	
		if(count($get_cat)>0){
			foreach($get_cat as $cat_arr)
			{	
				if($cat_arr['parent_id'] != 0) {								
					$cat_id = $this->GetParentCategoryId($cat_arr['parent_id']);
				}else {
					$cat_id = $cat_arr['category_id'];
				}
			}
		} 
		return $cat_id;	
	}
	function GetParentCategoryName($catId){
		$get_cat=$this->Select(TABLE_CATEGORY,"category_id='".$catId."' and is_deleted=0","category");
		return $get_cat[0]['category'];	
	}	
	/// Function to upload Consultant image to different dimensions
	function UploadCunsultantImage($source,$thumbFix=false,$bg=false,$heightFix=false) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			$ext = strtolower($ext);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$fileName = $this->GetFileName($source);
			$destination = DIR_CONSULTANT.$fileName.'.'.$ext;
			
			if(copy($source,$destination)) {
				@unlink($source);
				
				if($width>600 || $height>800)
					$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,600,800,DIR_CONSULTANT,$thumbFix,$bg); 
				if($heightFix) {
					$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH1,CONSULTANT_HEIGHT1,DIR_CONSULTANT_SMALL,$thumbFix,$bg);
					/// Upload image for 
					if($width>CONSULTANT_WIDTH1 || $height>CONSULTANT_HEIGHT1) {
						$this->CreateThumbHeight($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH1,CONSULTANT_HEIGHT1,DIR_CONSULTANT_THUMBNAIL);
					} else
						copy(DIR_CONSULTANT.$fileName.'.'.$ext,DIR_CONSULTANT_THUMBNAIL.$fileName.'.'.$ext);
						
						
				} else {
					$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH1,CONSULTANT_HEIGHT1,DIR_CONSULTANT_SMALL,$thumbFix,$bg);
					if($width>CONSULTANT_WIDTH1 || $height>CONSULTANT_HEIGHT1) {
						$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH1,CONSULTANT_HEIGHT1,DIR_CONSULTANT_THUMBNAIL,$thumbFix,$bg);
					} else { 
						if($thumbFix) {
							$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH1,CONSULTANT_HEIGHT1,DIR_CONSULTANT_THUMBNAIL,$thumbFix,$bg);
						}
						else { 
							copy(DIR_CONSULTANT.$fileName.'.'.$ext,DIR_CONSULTANT_THUMBNAIL.$fileName.'.'.$ext);
						}
					}
				}
				
				return true;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	function UploadCunsultantImage2($source,$thumbFix=false,$bg=false,$heightFix=false) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			$ext = strtolower($ext);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$fileName = $this->GetFileName($source);
			$destination = DIR_CONSULTANT.$fileName.'.'.$ext;
			
			if(copy($source,$destination)) {
				@unlink($source);
				
				if($width>600 || $height>800)
					$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,600,800,DIR_CONSULTANT,$thumbFix,$bg); 
				if($heightFix) {
					$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH2_SMALL,CONSULTANT_HEIGHT2_SMALL,DIR_CONSULTANT_SMALL,$thumbFix,$bg);
					/// Upload image for 
					if($width>CONSULTANT_WIDTH1 || $height>CONSULTANT_HEIGHT1) {
						$this->CreateThumbHeight($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH2,CONSULTANT_HEIGHT2,DIR_CONSULTANT_THUMBNAIL);
					} else
						copy(DIR_CONSULTANT.$fileName.'.'.$ext,DIR_CONSULTANT_THUMBNAIL.$fileName.'.'.$ext);
						
						
				} else {
					$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH2_SMALL,CONSULTANT_HEIGHT2_SMALL,DIR_CONSULTANT_SMALL,$thumbFix,$bg);
					if($width>CONSULTANT_WIDTH1 || $height>CONSULTANT_HEIGHT1) {
						$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH2,CONSULTANT_HEIGHT2,DIR_CONSULTANT_THUMBNAIL,$thumbFix,$bg);
					} else { 
						if($thumbFix) {
							$this->CreateThumb($fileName,$ext,DIR_CONSULTANT,CONSULTANT_WIDTH2,CONSULTANT_HEIGHT2,DIR_CONSULTANT_THUMBNAIL,$thumbFix,$bg);
						}
						else { 
							copy(DIR_CONSULTANT.$fileName.'.'.$ext,$thumbnailDir.$fileName.'.'.$ext);
						}
					}
				}
				
				return true;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	/// Function to upload product image to different dimensions
	function UploadProductImage($source,$thumbFix=false,$bg=false,$heightFix=false) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			$ext = strtolower($ext);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$fileName = $this->GetFileName($source);
			$destination = DIR_PRODUCT.$fileName.'.'.$ext;
			
			if(copy($source,$destination)) {
				@unlink($source);
				
				if($width>600 || $height>800)
					$this->CreateThumb($fileName,$ext,DIR_PRODUCT,600,800,DIR_PRODUCT,$thumbFix,$bg); 
				if($heightFix) {
					$this->CreateThumb($fileName,$ext,DIR_PRODUCT,PRODUCT_SMALL_WIDTH,PRODUCT_SMALL_HEIGHT,DIR_PRODUCT_SMALL,$thumbFix,$bg);
					/// Upload image for 
					if($width>PRODUCT_WIDTH || $height>PRODUCT_HEIGHT) {
						$this->CreateThumbHeight($fileName,$ext,DIR_PRODUCT,PRODUCT_WIDTH,PRODUCT_HEIGHT,DIR_PRODUCT_MEDIUM);
					} else {
						copy(DIR_PRODUCT.$fileName.'.'.$ext,DIR_PRODUCT_MEDIUM.$fileName.'.'.$ext);
					}
					if($width>PRODUCT_LISTING_WIDTH || $height>PRODUCT_LISTING_HEIGHT) {
						$this->CreateThumbHeight($fileName,$ext,DIR_PRODUCT,PRODUCT_LISTING_WIDTH,PRODUCT_LISTING_HEIGHT,DIR_PRODUCT_LISTING);
					} else {
						copy(DIR_PRODUCT.$fileName.'.'.$ext,DIR_PRODUCT_LISTING.$fileName.'.'.$ext);
					}
					
						
						
				} else {
					$this->CreateThumb($fileName,$ext,DIR_PRODUCT,PRODUCT_SMALL_WIDTH,PRODUCT_SMALL_HEIGHT,DIR_PRODUCT_SMALL,$thumbFix,$bg);
					if($width>PRODUCT_WIDTH || $height>PRODUCT_HEIGHT) {
						$this->CreateThumb($fileName,$ext,DIR_PRODUCT,PRODUCT_WIDTH,PRODUCT_HEIGHT,DIR_PRODUCT_MEDIUM,$thumbFix,$bg);
					} else { 
						if($thumbFix) {
							$this->CreateThumb($fileName,$ext,DIR_PRODUCT,PRODUCT_WIDTH,PRODUCT_HEIGHT,DIR_PRODUCT_MEDIUM,$thumbFix,$bg);
						}
						else { 
							copy(DIR_PRODUCT.$fileName.'.'.$ext,DIR_PRODUCT_MEDIUM.$fileName.'.'.$ext);
						}
					}
					
					if($width>PRODUCT_LISTING_WIDTH || $height>PRODUCT_LISTING_HEIGHT) {
						$this->CreateThumb($fileName,$ext,DIR_PRODUCT,PRODUCT_LISTING_WIDTH,PRODUCT_LISTING_HEIGHT,DIR_PRODUCT_LISTING,$thumbFix,$bg);
					} else { 
						if($thumbFix) {
							$this->CreateThumb($fileName,$ext,DIR_PRODUCT,PRODUCT_LISTING_WIDTH,PRODUCT_LISTING_HEIGHT,DIR_PRODUCT_LISTING,$thumbFix,$bg);
						}
						else { 
							copy(DIR_PRODUCT.$fileName.'.'.$ext,DIR_PRODUCT_LISTING.$fileName.'.'.$ext);
						}
					}
					
				}
				
				return true;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	
	/// Function to upload product image to different dimensions
	function UploadAdditionalProductImage($source,$thumbFix=false,$bg=false,$heightFix=false) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			$ext = strtolower($ext);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$fileName = $this->GetFileName($source);
			$destination = DIR_PRODUCT.$fileName.'.'.$ext;
			
			if(copy($source,$destination)) {
				@unlink($source);
				
				if($width>600 || $height>800)
					$this->CreateThumb($fileName,$ext,DIR_PRODUCT,600,800,DIR_PRODUCT,$thumbFix,$bg); 
				if($heightFix) {
					$this->CreateThumbHeight($fileName,$ext,DIR_PRODUCT,PRODUCT_SMALL_WIDTH,PRODUCT_SMALL_HEIGHT,DIR_PRODUCT_SMALL,$thumbFix,$bg);
				} else {
					$this->CreateThumb($fileName,$ext,DIR_PRODUCT,PRODUCT_SMALL_WIDTH,PRODUCT_SMALL_HEIGHT,DIR_PRODUCT_SMALL,$thumbFix,$bg);
				}
				
				return true;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	function GetSelectedSearchCriteria($id){		
		$get_result = array(1=>'Keyword',2=>'Title',3=>'Grade',4=>'Brand',5=>'Item Number',6=>'Author',7=>'ISBN');		
		return $get_result[$id];
	}
	function GetCategoryDescription($id){
		$result = $this->Select(TABLE_CATEGORY," category_id=".$id,"description");		
		if(count($result) > 0) 
			$description = $result[0]['description'];
		else
			$description = "";
		return $description;		
	}
	function GetParentDisabledCategories($cat_id){	
		$get_cat=$this->Select(TABLE_CATEGORY,"parent_id='".$cat_id."' and (is_deleted=1 OR is_active=0)","category_id");
		$category = "";
		foreach($get_cat as $key=>$val){
			$result_categories  = $this->GetCatIdArray($val['category_id']);
			if($result_categories != "")
				$category .= $val['category_id'].','.$result_categories;			
		}		
		return $category;
	}
	function GetDisabledCategories(){
		$get_cat=$this->Select(TABLE_CATEGORY," (is_deleted=1 OR is_active=0)","category_id");
		$category = array();
		foreach($get_cat as $result) {
			$category[$result['category_id']] = $result['category_id'];
		}
		return $category;
	}
	function GetQuickShoppingHTML($products){
		$totalRow = 10;
		$totalColoumns = 4;
		$total = 1;	
		for($i = 0; $i<$totalRow; $i++) {
			$result .= '<div class="RR-TR">';
				for($j=0; $j<$totalColoumns;$j++) {
					$result .= '<div class="RR-TD">'.$this->AddFormField('text','txt_product['.$total.']',$products[$total],"RR-input",0,'Product_'.$total,'JS_BLANK','maxlength=6','','','','').'</div>';
					$total++;
				}
			$result.='</div>';
		}		
		return $result;
	}
	function getFormatedContent($containsReplaced,$containsToBeReplaced,$content)
	{
		$content = str_replace($containsReplaced,$containsToBeReplaced,$content);
		return $content;
	}
	
	function GetCategoryTreeAdmin($catId) {	
		$cat = array();
		$getRes = $this->Select(TABLE_CATEGORY,"category_id = '".$catId."'");	
		$i = 1;		
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$cat_name = htmlentities(stripslashes($res['category']));
				if($res['parent_id']!=0) {													
					$cat[] = ' <a class="navTree" href="'.$this->MakeUrl('product-browse/index/',"cat_id=".$res['category_id'],1).'">'.ucwords(strtolower($cat_name)).'</a>';					
					$parent_id = $res['parent_id'];
					$cat[] = $this->GetCategoryLinkTreeFrontEnd($parent_id);
					
					
				} else {
					//echo count($getRes);									
					$cat[] = ' <a class="navTree" href="'.$this->MakeUrl('product-browse/index/',"cat_id=".$res['category_id'],1).'">'.ucwords(strtolower($cat_name)).'</a>';						
				}
				$i++;								
			}
		}	
		$cat = array_reverse($cat);			
		$cat_tree = implode("  ",$cat);		
		return $cat_tree;
	}
	
	function UploadFile($source, $destination) { 
		if(is_file($source)) {
			if(rename($source,$destination)) {
				return true;
			}
			else
				return false;
		} else {
			return false;
		}
	}	
	function GetAdditionalInformation($productId,$brand_id){
		//echo "Product Id============".$productId;
		//echo "<br />";
		//echo "Brand Id=============".$brand_id;
		$isbnResult = $this->Select(TABLE_ISBN, " is_active=1 AND is_deleted=0 AND product_id=".$productId,"*");
		if($brand_id != "") {
			$brandResult = $this->Select(TABLE_BRAND, " is_active=1 AND is_deleted=0 AND brand_id=".$brand_id,"brand_name");
			if(count($brandResult)>0) {
				$brand_name = $brandResult[0]['brand_name']; 
				$isbnResult[0]['publisher'] = $brand_name;
			}else {
				$brand_name = "";
			}
		}
		$result = "";		
		if(count($isbnResult) > 0){
						
			foreach($isbnResult as $key=>$val) {					
			$result .= ''.(trim($val['contributor'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Contributor:</strong> '.$val['contributor'].'</div>
						</div>':'').'
						'.(trim($val['publisher'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Publisher:</strong> '.$brand_name.'</div>
						</div>':'').'
						'.(trim($val['publish_date'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Publish Date:</strong> '.$val['publish_date'].'</div>
						</div>':'').'
						'.(trim($val['binding'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Binding:</strong> '.$val['binding'].'</div>
						</div>':'').'
						'.(trim($val['pages'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Pages:</strong> '.$val['pages'].'</div>
						</div>':'').'
						'.(trim($val['dimensions'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Dimensions:</strong> '.$val['dimensions'].'</div>
						</div>':'').'
						'.(trim($val['edition'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Edition:</strong> '.$val['edition'].'</div>
						</div>':'').'
						'.(trim($val['imprint'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Imprint:</strong> '.$val['imprint'].'</div>
						</div>':'').'
						'.(trim($val['duration'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Duration:</strong> '.$val['duration'].'</div>
						</div>':'').'
						'.(trim($val['language'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Language:</strong> '.$val['language'].'</div>
						</div>':'').'
						'.(trim($val['series_title'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Series Title:</strong> '.$val['series_title'].'</div>
						</div>':'').'
						'.(trim($val['ages'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Age Range:</strong> '.$val['ages'].'</div>
						</div>':'').'
						'.(trim($val['grades'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Grade Range:</strong> '.$val['grades'].'</div>
						</div>':'').'
						'.(trim($val['reading_level'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Reading Level:</strong> '.$val['reading_level'].'</div>
						</div>':'').'
						'.(trim($val['edu_level'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Education Level:</strong> '.$val['edu_level'].'</div>
						</div>':'').'
						'.(trim($val['audience'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Audience:</strong> '.$val['audience'].'</div>
						</div>':'').'
						'.(trim($val['awards'])!=''?'<div class="RR-Product-Text">
							<div class="RR-Product-Arrow"></div>
							<div class="RR-Product-ItemText"><strong>Awards:</strong> '.$val['awards'].'</div>
						</div>':'').'';
			}			
			if(trim($result) == "") {								
				//$result .= "No Additional Information";
				return $result;
			}
			return $result;
		}
				
	}
	
	function GetCountryName($countryId){
		$get_result=$this->Select(TABLE_COUNTRY,"country_id='".$countryId."'","Name","Name");						
		return htmlentities(ucfirst(stripslashes($get_result[0]['Name'])));
	}
	function ValidateEmail($e_mail) {
		$getRes = $this->Select(TABLE_CUSTOMER,"e_mail='".$e_mail."' AND is_deleted=0","customer_id");
		if(count($getRes)>0 ){
			return $getRes[0]['customer_id'];
		} else {
			return "";
		}
	
	}
	function GetProductsPerPage(){
		$get_result=$this->Select(TABLE_EXTRA_SETTING,"","product_per_page");
		if(count($get_result) > 0 && $get_result[0]['product_per_page'] != 0) {
			return $get_result[0]['product_per_page'];
		}else {
			return 10;
		}	
	}
	
	function EmailNotyfication($formType){
		$get_result=$this->Select(TABLE_ADMIN_NOTYFICATION_EMAILS," form_type='".$formType."' ","emp_email_ids","",1);						
		if(count($get_result)>0)
		{
			foreach($get_result as $data)
			$email_id_arr	=	$data;
		}
		$emails	=	$this->EmailNotyficationList($email_id_arr['emp_email_ids']);
		return	$emails;
	}
	
	function EmailNotyficationList($empIds){
		$emails = array();
		if($empIds!='') {
			$get_result=$this->Select(TABLE_EMP_EMAILS," emp_email_id IN(".$empIds.") ","email");
			if(count($get_result)>0)
			{
				foreach($get_result as $data)
				$emails[]	=	$data['email'];
			}
		} 
		return implode(',',$emails);
	}
	
	function GetExtraSettingArray(){
		$result = $this->Select(TABLE_EXTRA_SETTING,"","*","",1);
		$arr = array();
		if(count($result)>0){
			foreach($result as $data){	}
			foreach($data as $key=>$val){
				if($key != "extra_setting_id ") {
					$arr[$key]=$val;
				}
			}
		}
		return $arr;
	}
	
	function ProcessPayment($pay_data) {
		/*include ROOT."includes/lphp.php";
		$mylphp=new lphp;
	
		# constants https://staging.linkpt.net/lpc/servlet/lppay.
		$myorder["host"]       = "secure.linkpt.net";
		$myorder["port"]       = "1129";
		$myorder["keyfile"]    = ROOT."includes".DS.$pay_data['store_no'].".pem";
		$myorder["configfile"] = $pay_data['store_no'];
		# transaction details
		$myorder["ordertype"]         = "SALE";
		$myorder["result"]            = YOURPAY_PAYMENT_MODE;
		$myorder["oid"]               = $pay_data['orderid'];
	
	
		//$myorder["action"] = "SUBMIT";
		//$myorder["installments"] = "12";
		//$myorder["threshold"] = "3";
		//$myorder["startdate"] = "immediate";
		//$myorder["periodicity"] = "monthly";
		# totals
		$myorder["subtotal"]    = $pay_data["subtotal"];
		$myorder["tax"]         = 0.00;
		$myorder["shipping"]    = $pay_data['shipping'];
		//$myorder["vattax"]      = 0;
		$myorder["chargetotal"] = $pay_data["subtotal"]+$pay_data['shipping'];
		
		# card info
		$expmonth=$pay_data["expirymonth"];
		$expyear=$pay_data["expiryyear"];
		$myorder["cardnumber"]   = $pay_data["cardno"];
		$myorder["cardexpmonth"] = $expmonth;
		$myorder["cardexpyear"]  = $expyear;
		if( $pay_data["cvvcode"]!="")
		{
			$myorder["cvmindicator"] = "provided";
			$myorder["cvmvalue"]     = $pay_data["cvmvalue"];
		}
		else
			$myorder["cvmindicator"] = "not_provided";
		
	
		# BILLING INFO
		$myorder["name"]     = $pay_data["nameoncard"];
		$myorder["address1"] = $pay_data["address"];
		$myorder["city"]     = $pay_data["city"];
		$myorder["state"]    = $pay_data["state"];
		$myorder["country"]  = "US";
		$myorder["phone"]    = $pay_data["phone"];
		$myorder["email"]    = $pay_data["email"];
		$myorder["addrnum"]  = "123";
		$myorder["zip"]      = $pay_data["zip"];
		
		# SHIPPING INFO
		$myorder["sname"]     = $pay_data["sname"];
		$myorder["saddress1"] = $pay_data["saddress"];
		$myorder["saddress2"] = $pay_data["saddress2"];
		$myorder["scity"]     = $pay_data["scity"];
		$myorder["sstate"]    = $pay_data["sstate"];
		$myorder["szip"]      = $pay_data["szip"];
		$myorder["scountry"]  = "US";
		
		if ($pay_data["debugging"])
			$myorder["debugging"]="true";

		#   Send transaction. Use one of two possible methods 
		//	$result = $mylphp->process($myorder);       # use shared library model
		 
		$result = $mylphp->curl_process($myorder);  # use curl methods
	
	//print_r($result);
		if ($result["r_approved"] != "APPROVED")    // transaction failed, print the reason
		{
			$ispaid_status = 0;
			$declined_error=$result['r_error'];
		}
		else	// success
		{		
			$ispaid_status = 1;
			$t_code=$result['r_code'];
		}*/
		$payment_status['message']="Transaction Denied.";
		$ispaid_status = 1;
		$result['r_code']=1111;
		return array('status'=>$ispaid_status,'transaction_id'=>$result['r_code'],'message'=>$result['r_error']);
	}
	
	function OrderEmail($orderId,$subjectType='new',$userType='user') {
		$cartOrderMailStatus ='';
		$cartInvoiceMailStatus ='';
		$order_data=$this->Select(TABLE_CART_ORDER,"cart_order_id='".$orderId."'","*","");
		$i=1;  $j=1;
 		if(count($order_data)>0) {
			$cartOrderMailStatus = 1;
			foreach($order_data as $arr) {
				$data_arr=$arr;
				$product_data=$this->Select(TABLE_CART_INVOICE,"cart_order_id='".$orderId."'","*","order_invoice_id");
				if(count($product_data)>0) {
					$cartInvoiceMailStatus = 1;
				
					foreach($product_data as $product) { 
							if($sep!=0) { 
								$prod_data[$product['type']].='
										<tr>
										  <td height="2" colspan="5"></td>
										</tr>
										<tr>
										  <td height="1" colspan="5"><hr color="#000000" size="1"/></td>
										</tr>
										<tr>
										  <td height="2" colspan="5" ></td>
										</tr>
								';
							} else {
								$sep = 1;
							}
							
							$amt = (($product['price'] * $product['quantity']));
							
							////Gettting size details of product
							$specification =''; $sep='';
							if($type=='admin') {
								if($product['item_no']){
									$specification = $sep.'Product #: '.$product['item_no'];
									$sep = "<br>";
								}
							}
							
							if($product['type']=='product')  $count = $i;  else  $count = $j;
							$prod_data[$product['type']].='
							 <tr class="text">
									  <td align="center" valign="top" >'.$count.'</td>
									  <td style="padding-left:5px;" valign="top" align="left">'.nl2br(strip_tags(str_replace("<br />","\n",stripslashes($product['product_name'])))).'<br/>'.$specification.'</td>
									  <td style="padding-right:5px;" valign="top" align="center">'.sprintf("%03d",$product['quantity']).'</td>
									  <td style="padding-right:3px;" valign="top" align="center">'.sprintf("$%01.2f",$product['price']).'</td>
									  <td style="padding-right:10px;" valign="top" align="center">'.sprintf("$%01.2f",$amt).'</td>
									</tr>';
							if($product['type']=='product')  $i++;  else  $j++;
					}//endfoeach
				}//endif
			}//endforeach
		}//endif
		
		ob_start();
		include(ROOT.'email-templates/order.php');
		$order_email = ob_get_clean();
		
		$signature = $this->WebSettings['signature'];
		$signature = str_replace("{SITE_NAME}",SITE_NAME,$signature);
		$signature = str_replace("{SITE_URL}",SITE_URL,$signature);
		
		
		$orderMailBody = $this->getOrderEmailTemplate($data_arr, $prod_data, $mode);
		
		
		if($userType == 'user'){ 
			$order_email = str_replace("{FIRST_NAME}",ucfirst($data_arr['bill_first_name']),$order_email);
			$order_email = str_replace("{LAST_NAME}"," ".ucfirst($data_arr['bill_last_name']),$order_email);
			
			$order_content = nl2br($this->WebSettings['order_email_user_format']);
			$order_email = str_replace("{ORDER_DETAILS}",$order_content,$order_email);
			$order_email = str_replace("{ORDER_CONTENT}",$orderMailBody,$order_email);
			//$order_email = str_replace("{ORDER_TEXT}","You will find your Order Details below.",$order_email);
		}else {
			$order_email = str_replace("{FIRST_NAME}","Administrator",$order_email);
			$order_email = str_replace("{LAST_NAME}","",$order_email);
			
			$order_content = nl2br($this->WebSettings['order_email_admin_format']);
			$order_email = str_replace("{ORDER_DETAILS}",$order_content,$order_email);
			$order_email = str_replace("{ORDER_CONTENT}",$orderMailBody,$order_email);
			//$order_email = str_replace("{ORDER_TEXT}","Please find Order Details below.",$order_email);
		}
		$order_email = str_replace("{SIGNATURE}",nl2br($signature),$order_email);
		
		
		if($userType=='user') {
			$email = $data_arr['e_mail'];
		} else {
			$email = $this->EmailNotyfication('order');
		}
		
		if($subjectType=='' || $subjectType=='new') {
			//$order_subject="Order ".$this->FormatOrderId($data_arr['cart_order_id'])." Confirmation from ".html_entity_decode(SITE_NAME);
			if($userType=='user')  $order_subject=$this->WebSettings['order_email_user_subject']; 
			else 	$order_subject=$this->WebSettings['order_email_admin_subject'];
			
			$data_arr['comments']="-";
			$order_email = str_replace("{ORDER_IAMGE}",'<img src="'.SITE_URL.'images/email/email-order-success.jpg" border="0" alt="Order Placed Successfully." title="Order Placed Successfully." />',$order_email);
		} elseif($subjectType=='status') {
			$order_subject="Order ".$this->FormatOrderId($data_arr['cart_order_id'])." Status from ".html_entity_decode(SITE_NAME);
			$order_email = str_replace("{ORDER_IAMGE}",'<img src="'.SITE_URL.'images/email/email-order-updated.jpg" border="0" alt="Order Updated Successfully." title="Order Updated Successfully." />',$order_email);
		} 
		
		//$order_subject = $mail_subject;
		
		$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
		//echo BCC_EMAIL; die;
		if(defined('BCC_EMAIL') && BCC_EMAIL!="")
			$headers .= 'Bcc: ' . BCC_EMAIL . " \r\n";
			
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
		
		/*echo "Email=============>".$email;
		echo "<br />";
		echo "Subject==============>".$order_subject;
		echo "<br>";
		echo "Body=============>".$order_email;
		echo "<br>";
		echo "Headers============>".$headers;	die; */
		//echo $order_email; die; //."<br />".$email."<br />".$order_subject; die;
		if($cartInvoiceMailStatus!='' && $cartOrderMailStatus!=''){
			return @mail($email,$order_subject,$order_email,$headers);
		}else{
			return @mail(DEVELOPER_EMAIL,'Order Err:'.$order_subject ,$order_email,$headers);
		}
		//echo $orderMailBody; echo "<br>";	
	}//end function
	
	function getOrderEmailTemplate($data_arr, $prod_data, $mode=''){ //print_r($data_arr); die;
		$billName 	 = $data_arr['bill_name'];
		$orderId 	 = '<strong>'.$this->FormatOrderId($data_arr['cart_order_id']).'</strong>';
		$orderDate 	 = date('jS M,Y',strtotime($data_arr['order_date']));
		$status		 = strtoupper(str_replace("_"," ",$data_arr['status']));
		$comments	 = nl2br(stripslashes($data_arr['comments']));
		
		$billCompanyName = stripslashes($data_arr['bill_company_name']);
		$billFirstName = stripslashes($data_arr['bill_first_name']);
		$billLastName = stripslashes($data_arr['bill_last_name']);
		$billAddress = stripslashes($data_arr['bill_address']);
		$billAddress2 = stripslashes($data_arr['bill_address_2']);
		$billCity = stripslashes($data_arr['bill_city']);
		$billState = stripslashes($data_arr['bill_state']);
		$billZipCode = stripslashes($data_arr['bill_zipcode']);
		$billCountry = stripslashes($data_arr['bill_country']);
		$billPhone = stripslashes($data_arr['bill_phone']);
		$billFax = stripslashes($data_arr['bill_fax']);
		$email = stripslashes($data_arr['e_mail']);
		
		$shipCompanyName = stripslashes($data_arr['ship_company_name']);
		$shipFirstName = stripslashes($data_arr['ship_first_name']);
		$shipLastName = stripslashes($data_arr['ship_last_name']);
		$shipAddress = stripslashes($data_arr['ship_address']);
		$shipAddress2 = stripslashes($data_arr['ship_address_2']);
		$shipCity = stripslashes($data_arr['ship_city']);
		$shipState = stripslashes($data_arr['ship_state']);
		$shipZipCode = stripslashes($data_arr['ship_zipcode']);
		$shipCountry = stripslashes($data_arr['ship_country']);
		
		
		//$shippingMethodsArr = $this->getShippingMethod();
		$shippingMethod = stripslashes($data_arr['ship_type']);
		$paymentMethod = (trim($data_arr['payment_type'])=='paypal'?'Paypal':'Transfer Credit Card');
		$paypalTransactionId = (trim($data_arr['payment_type'])=='paypal'?$data_arr['gateway_transaction_id']:'');
		$paymentStatus = (trim($data_arr['is_paid'])=='1'?'PAID':'UNPAID');
		//echo $paymentMethod; die;
		$subTotal	 = $data_arr['subtotal'];
		
		/**********************************************************************************
			Calculating shipping
		***********************************************************************************/
		if($data_arr['total_ship']>0){
			$shipping = $data_arr['total_ship'];
		}else{
			$shipping = '0.00';
		}
		
		if($data_arr['payment_type']=='paypal' && trim($data_arr['is_paid'])==1) 
		 	$paySuccess = 1;
		else 
			$paySuccess = 0;
		/**********************************************************************************
			Calculating sale tax
		***********************************************************************************/
		/*$total = $subTotal + $quote - $promoCodeDiscount;
		$applicableTax = ($total*($saletax/100));
		$cart_total = $total + $applicableTax;*/
		//$total_amt = $subTotal + $data_arr['total_ship'];
		$total_amt = $subTotal;
		if($data_arr['sale_tax']>0){
			$saleTax = $data_arr['sale_tax'];
			$saleTaxPrice = sprintf('%01.2f', ($total_amt*$saleTax/100));
		} else {
			$saleTax = '0';
			$saleTaxPrice='0.00';
		}
		
		$order_expedite = 0;
		if($data_arr['is_expedite_order']==1) {
			$order_expedite = 1;
			$expedite_charge = sprintf('%01.2f',$data_arr['expedite_charge']);
		}
		$orderTotal  = $data_arr['order_total'];
		
									
		$order_message='<table width="650" border="0" cellspacing="0" cellpadding="0" bgcolor="#E8E8E8" align="center">
					  <tr>
						<td colspan="2" align="left" ></td>
					  </tr>
					  <tr>
						<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #D4D4D4">
							<tr height="22" bgcolor="#6190BF">
							  <td colspan="2" align="center" width="100%" height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;">&nbsp;Order Details</td>
							</tr>
							<tr>
							  <td colspan="2" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2" >
								  <tr>
									<td width="25%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Order No: </td>
									<td width="75%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$orderId.'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Order Date:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$orderDate.'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Shipping Method:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shippingMethod.'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Payment Type:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$paymentMethod.'</td>
								  </tr>'.
								  (trim($paypalTransactionId) && $paySuccess==1?'<tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Paypal Transaction Id:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$paypalTransactionId.'</td>
								  </tr>':'').'
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Payment Status:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$paymentStatus.'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Status:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$status.'</td>
								  </tr>';
				if($comments){	  
					$order_message.='<tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Comments:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">'.$comments.'</td>
								  </tr>';
				}
				
				$order_message.='</table></td>
							</tr>
							
							<tr>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
							</tr>
							<tr>
							  <td width="50%" align="center" height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;"><strong>Billing Information</strong></td>
							  <td width="50%" align="center" height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;"><strong>Shipping Information</strong></td>
							</tr>
							<tr>
							  <td align="right" valign="top" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2">
								  <tr>
									<td width="32%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Company Name:</td>
									<td width="69%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($billCompanyName)?$billCompanyName:'-').'</td>
								  </tr>
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">First Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($billFirstName)?$billFirstName:'-').'</td>
								  </tr>
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">Last Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($billLastName)?$billLastName:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Street Address:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billAddress)?$billAddress:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Address 2:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billAddress2)?$billAddress2:'-').'</td>
								  </tr>
								   <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">City:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billCity)?$billCity:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">State/Province:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billState)?$billState:'-').'</td>
								  </tr>
								   <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Zip/Postal Code:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billZipCode)?$billZipCode:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Country:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billCountry)?$this->GetCountryName($billCountry):'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">E-Mail:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($email)?$email:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Telephone:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billPhone)?$billPhone:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Fax:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($billFax)?$billFax:'-').'</td>
								  </tr>
								  
								</table></td>
							  <td align="left" style="font-family:verdana; font-size:11px; color:#000000;" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
								  <tr>
									<td width="32%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Company Name:</td>
									<td width="69%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($shipCompanyName)?$shipCompanyName:'-').'</td>
								  </tr>
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">First Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($shipFirstName)?$shipFirstName:'-').'</td>
								  </tr>
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">Last Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($shipLastName)?$shipLastName:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Street Address:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($shipAddress)?$shipAddress:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Address 2:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($shipAddress2)?$shipAddress2:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">City:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($shipCity)?$shipCity:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">State/Province:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($shipState)?$shipState:'-').'</td>
								  </tr>
								  
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Zip/Postal Code:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($shipZipCode)?$shipZipCode:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Country:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($shipCountry)?$this->GetCountryName($shipCountry):'-').'</td>
								  </tr>
								</table></td>
							</tr>
							<tr>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
							</tr>
							<tr><td colspan="2" height="5"></td></tr>
							<tr><td colspan="2" height="5"></td></tr>		 
							<tr>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
							</tr>
							<tr>
							  <td colspan="2" align="center" height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;">Products Ordered </td>
							</tr>
							<tr>
								<td height="2"></td>
							</tr>
							<tr>
							  <td colspan="2" align="right" style="font-family:verdana; font-size:11px; color:#000000;">
								<table width="99%" align="center" border="0" cellspacing="1" cellpadding="1" style="font-family:verdana; font-size:11px; color:#000000;">
								  <tr height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;">
									<td align="center" width="5%">#</td>
									<td align="center" width="40%">Product Name </td>
									<td width="15%" align="center">Qty</td>
									<td width="15%" align="center">Unit Price</td>
									<td align="center" width="25%">Amount</td>
								  </tr>
								  '.$prod_data['product'].'
								  '.(trim($prod_data['book'])!=''?'<tr class="heading" style="color:#FFFFFF; background-color:#808080;">
									<td align="center" colspan="5">Bargain Books</td>
								  </tr>
								  '.$prod_data['book']:'').'
								  <tr>
									<td colspan="6"><hr color="#000000" size="2" />&nbsp;</td>
								  </tr>
								  <tr>
								  	<td colspan="2">&nbsp;</td>
									<td align="right" colspan="2"><strong>Sub Total:</strong></td>
									<td style="padding-right:10px;" align="center">$'.$subTotal.'</td>
								  </tr>
								  '.(trim($saleTax)!=0?'
								  <tr>
									<td colspan="2">&nbsp;</td>
									<td colspan="2" align="right"><strong>Sale Tax:</strong>('.$saleTax.'%)</td>
									<td style="padding-right:10px;" align="center">+$'.$saleTaxPrice.'</td>
								  </tr>':'').'
								  '.($order_expedite==1 ? '<tr>
										<td colspan="2">&nbsp;</td>
										<td colspan="2" align="right"><strong>Expedite Charge:</strong></td>
										<td style="padding-right:10px;" align="center">+$'.$expedite_charge.'</td>
									  </tr>':'').'
								  '.($shipping!='' ? '<tr>
									<td colspan="2">&nbsp;</td>
									<td colspan="2" align="right"><strong>Shipping Charge:</strong></td>
									<td style="padding-right:10px;" align="center">+$'.$shipping.'</td>
								  </tr>':'').'
								  <tr>
									<td colspan="2">&nbsp;</td>
									<td colspan="2" align="right"><strong>Order Total: </strong></td>
									<td style="padding-right:10px;" align="center">$'.($orderTotal).'</td>
								  </tr>
								  <tr>
									<td colspan="5">&nbsp;</td>
								  </tr>
								</table></td>
							</tr>
						  </table></td>
					  </tr>';
			if($mode=='manual'){	  
				$order_message.='<tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Note:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Please process this order manually.</td>
							  </tr>';
			}
			$order_message .='</table>';
					
			return $order_message;
	}

	function GetMyWishlistGroupDropDown($userId,$groupId) {
		$get_result=$this->Select(TABLE_WISHLIST_GROUP,"customer_id='".$userId."' and is_deleted=0","*","group_name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$data .= '<option value="'.$result['group_id'].'"';				
				if($result['group_id']==$groupId) $data .= ' selected="selected" ';
				$data .= '>'.ucwords(strtolower($result['group_name'])).'</option>';
			}
		}
		return $data;
	}
	function GetUpdatedMyWishlistGroupDropDown($userId,$groupId) {
		$get_result=$this->Select(TABLE_WISHLIST_GROUP,"customer_id='".$userId."' and is_deleted=0 and group_id<>".$groupId,"*","group_name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$data .= '<option value="'.$result['group_id'].'"';				
				//if($result['group_id']==$groupId) $data .= ' selected="selected" ';
				$data .= '>'.ucwords(strtolower($result['group_name'])).'</option>';
			}
		}
		return $data;
	}
	function AddWishListGroup($userId,$groupName,$postfix=1) {
		$postfix_str = $postfix>1?(" (".$postfix.")"):'';
		$get_result=$this->Select(TABLE_WISHLIST_GROUP,"customer_id='".$userId."' and is_deleted=0 and group_name='".addslashes($groupName.$postfix_str)."'","*","group_name");
		if(count($get_result)==0) {
			$data_arr=array('group_name'=>"'".addslashes($groupName.$postfix_str)."'",'customer_id'=>"'".$userId."'",'is_active'=>'1','add_date'=>'NOW()','updated_date'=>'NOW()');
			return $this->Insert(TABLE_WISHLIST_GROUP,$data_arr);
		} else {
			$postfix++;
			return $this->AddWishListGroup($userId,$groupName,$postfix);
		}
		
	}
	function GetWishlistProductDetails($product_id) {
		
		$product_arr=$this->Select(TABLE_PRODUCTS,"product_id='".$product_id."'",'product_title as name,product_image,rainbow_price,description,product_number,is_backorder,out_of_stock');
		if(count($product_arr)>0) {
			foreach($product_arr as $product) { 
				
				$product['product_id'] = $product_id;
				$product['product_name']=ucfirst($product['name']);
				$product['name']="<strong>".ucfirst($product['name'])."</strong>";
				if($product['description']!="") {
					$product['name'].='<br /><span style="color: #888888;">'.substr(strip_tags(stripslashes($product['description'])),0,50).'..</span>';
					$product['desc'] =substr(strip_tags(stripslashes($product['description'])),0,90).'...';
				}
				return $product;
			}
		}
	}
	function GetMyDefaultGroup($userId) {
		$get_result=$this->Select(TABLE_WISHLIST_GROUP,"customer_id='".$userId."' and is_deleted=0 and group_name='Default'","group_id","group_name");
		if(count($get_result)==0) {
			return $this->AddWishListGroup($userId,'Default');
		} else {
			return $get_result[0]['group_id'];
		}
	}
	function GetOrderSettingArray(){
		$result = $this->Select(TABLE_ORDER_SETTING,"","*","",1);
		$arr = array();
		if(count($result)>0){
			foreach($result as $data){	}
			foreach($data as $key=>$val){
				if($key != "order_setting_id") {
					$arr[$key]=$val;
				}
			}
		}
		return $arr;
	}
	function EmptyWishlist($group_id){
		$this->Delete(TABLE_WISHLIST,"group_id='".$group_id."'");	
	}
	function DeleteFromWishlist($post_arr) {
		if(count($post_arr['wishlistChk'])>0) {			
			foreach($post_arr['wishlistChk'] as $data) {
				$this->Delete(TABLE_WISHLIST,"group_id='".$post_arr['txt-my-group']."' and product_id='".$data."'");				
			}
		}
	}
	function UpdateWishlist($post_arr) { //print_r($post_arr); die;
		if(count($post_arr['product_id'])>0) {
			$i=0;			
			foreach($post_arr['product_id'] as $data) {
				$data_arr=array('quantity'=>($post_arr['qty_'.$data]));				
				$this->Update(TABLE_WISHLIST,$data_arr,"group_id='".$post_arr['txt-my-group']."' and product_id='".$data."'");
				$i++;
			}
		}
	}
	
	function GetCountryCode2FromId($id) {
		$getdata=$this->Select(TABLE_COUNTRY,"country_id='".$id."'","Code2","",1);
		if(count($getdata)>0){
			$code2=$getdata[0]['Code2'];
		}
		return $code2;
	}
	
	function GetCarrierOptions($post_arr) {
		/******************* Code for Cristmas Shipping ************/
		$mnth = date('n');
		$dy = date('d');
		$hr = date('G');
		$cdgrnd = $this->OrderSettings['ground'];
		$cd3day = $this->OrderSettings['3day'];
		$cd2day = $this->OrderSettings['2day'];
		$cd1day = $this->OrderSettings['1day'];
		
		$yr = date('Y');
		/***********************************************************/
		
		
		$merch = $post_arr['merch'];   $nmerch = $post_arr['nmerch'];
		$BillZip = $post_arr['txt_zip_code'];
		$ShipZip = $post_arr['txt_szip_code'];
		$sflag = 0;  $sflag2 = 0;
		if($post_arr['txt_country']=='229') {
			$billRes = $this->Select(TABLE_STATE,"state_prefix ='".$post_arr['txt_state']."' and '".$BillZip."' between zipstart and zipend");
			if(count($billRes)>0) {
				$sflag = $billRes[0]['outside'];
			}
		}
		
		if($post_arr['txt_scountry']=='229') {
			$shipRes = $this->Select(TABLE_STATE,"state_prefix ='".$post_arr['txt_sstate']."' and '".$ShipZip."' between zipstart and zipend");
			if(count($shipRes)>0) {
				$sflag2 = $shipRes[0]['outside'];
			}
		}
		
		$shipping = 0;
		$FreeShip = 1;
		
		$base = 3.75;
		
		if(date('m')==12) {
			$free_shipping = $this->OrderSettings['dec_free_shipping'];
		} else {
			$free_shipping = $this->OrderSettings['free_shipping'];
		}
		
		$ShipCountry = $this->GetCountryCode2FromId($post_arr['txt_scountry']);
		$ShipState = $post_arr['txt_sstate'];
		//// Check for minimum amount for free shipping
		if (!(($ShipCountry == 'US' || $ShipCountry == 'VI') && $merch > $free_shipping)) $FreeShip = 0;
				
		if($post_arr['txt_scountry']==229) {
			if (((($sflag2 == 0 || ($ShipState == 'AA' || $ShipState == 'AE' || $ShipState == 'AP'))) || (($sflag == 0) || ($ShipState == 'AA' || $ShipState == 'AE' || $ShipState == 'AP'))) && $nmerch > 24.99 && $nmerch > 0 && ($mnth == 11 || ($mnth == CRISTMAS_MONTH && ($dy < 16 || ($dy == 16 && $hr < 16))))) $FreeShip = 1;
		}
				
		if ($merch > 24.99 && $merch < 50.00) $base = $merch * 0.15;
		if ($merch > 49.99 && $merch < 100.00) $base = $merch * 0.12;
		if ($merch > 99.99 && $merch < 200.00) $base = $merch * 0.10;
		if ($merch > 199.99 && $merch < 300.00) $base = $merch * 0.08;
		if ($merch > 299.99 && $merch < 500.00) $base = $merch * 0.06;
		if ($merch > 499.99) $base = $merch * 0.04;
		if ($FreeShip == 0) $shipping = $base;
		
		$priority = $base * 1.5;
		$upsground = $shipping; 
		if ($merch < 35.00) $upsground += 1;
		if ($merch > 34.99 && $sflag == 0 && $FreeShip == 0) $shipping += 1;
		$ups3day = $base * 1.5;
		$ups2day = $base * 3;
		$nextdayair = $base * 6;
		$foreign = 0;
	
		if ($ShipCountry == 'CA') $CanadaAir = $base * 2;
		if ($ShipCountry != 'CA' && $ShipCountry != 'US') {
			$ForeignSurface = $base * 2.5;
			$ForeignAir = $base * 5;
			$foreign = 1;
		}
		
		
		//echo $ShipCountry; die;
		$onchange_fn = ' onchange="javascript: processShippingCharge(this);"';
		if ((($sflag == 1) || ($sflag2 == 1)) && !isset($foreign) && $ShipCountry != 'CA') {
			$shipId = 'txt_ship_type1';
			$options .='<div class="RR-Type">
							<div class="RR-ShipCol1"><input type="radio" value="Book Rate (4-6 weeks)" rel="'.number_format($shipping, 2).'" name="txt_ship_type" id="txt_ship_type1" '.$onchange_fn.' checked="checked"></div>
							<div class="RR-ShipCol2"><label for="txt_ship_type1">Book Rate (4-6 weeks)</label></div>
							<div class="RR-ShipCol3">$'.number_format($shipping, 2).'</div>
						</div>
						<div class="RR-Type">
							<div class="RR-ShipCol1"><input type="radio" value="Priority (3-7 days)" rel="'.number_format($priority, 2).'" name="txt_ship_type" id="txt_ship_type2" '.$onchange_fn.'></div>
							<div class="RR-ShipCol2"><label for="txt_ship_type2">Priority (3-7 days)</label></div>
							<div class="RR-ShipCol3">$'.number_format($priority, 2).'</div>
						</div>';
		}
		elseif ($ShipCountry == 'US') {
			if ($merch < 35.00) {
				$shipId = 'txt_ship_type3';
				$options .='<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="Book Rate (1-3 weeks)" rel="'.number_format($shipping, 2).'" name="txt_ship_type" id="txt_ship_type3" '.$onchange_fn.' checked="checked"></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type3">Book Rate (1-3 weeks)</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($shipping, 2).'</div>
							</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="UPS Ground/Parcel Select (our choice)" rel="'.number_format($upsground, 2).'" name="txt_ship_type" id="txt_ship_type4" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type4">UPS Ground/Parcel Select (our choice)</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($upsground, 2).'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == ($cdgrnd - 3) && $hr > 16) || ($dy > ($cdgrnd - 3) && $dy < $cdgrnd) || ($dy == $cdgrnd && $hr < 16))) {
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="Priority (3-7 days)" rel="'.number_format($priority, 2).'" name="txt_ship_type" id="txt_ship_type5" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type5">Priority (3-7 days)</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($priority, 2).'</div>
							</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="UPS 3-Day" rel="'.number_format($ups3day, 2).'" name="txt_ship_type" id="txt_ship_type6" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type6">UPS 3-Day</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($ups3day, 2).'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == $cdgrnd && $hr > 15) || ($dy == $cd3day && $hr < 16))) {
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="UPS 2-Day" rel="'.number_format($ups2day, 2).'" name="txt_ship_type" id="txt_ship_type7" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type7">UPS 2-Day</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($ups2day, 2).'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == $cd3day && $hr > 15) || $dy > $cd3day && $dy < $cd2day) || ($dy == $cd2day && $hr < 16)) {
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='
							</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="Next-Day Air" rel="'.number_format($nextdayair, 2).'" name="txt_ship_type" id="txt_ship_type8" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type8">Next-Day Air</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($nextdayair, 2).'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == $cd2day && $hr > 15) || ($dy == $cd1day && $hr < 16))) {
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='</div>';
				if ($mnth == CRISTMAS_MONTH && (($dy == $cd2day && $hr > 15) || ($dy == $cd1day && $hr < 16))) {
					$options .='<div class="RR-Type"><strong>NOTE:</strong> This is the last day to order to receive by Christmas</div>';
				}
			} else {
				$shipId = 'txt_ship_type9';
				$options .='<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="UPS Ground/Parcel Select (our choice)" rel="'.number_format($upsground, 2).'" name="txt_ship_type" id="txt_ship_type9" '.$onchange_fn.' checked="checked"></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type9">UPS Ground/Parcel Select (our choice)</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($upsground, 2).'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == ($cdgrnd - 3) && $hr > 15) || ($dy > ($cdgrnd - 3) && $dy < $cdgrnd) || ($dy == $cdgrnd && $hr < 16))) {
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='
							</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="UPS 3-Day" rel="'.number_format($ups3day, 2, '.', ',').'" name="txt_ship_type" id="txt_ship_type10" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type10">UPS 3-Day</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($ups3day, 2, '.', ',').'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == $cdgrnd && $hr > 15) || ($dy == $cd3day && $hr < 16))) { 
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='
							</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="UPS 2-Day" rel="'.number_format($ups2day, 2, '.', ',').'" name="txt_ship_type" id="txt_ship_type11" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type11">UPS 2-Day</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($ups2day, 2, '.', ',').'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == $cd3day && $hr > 15) || ($dy > $cd3day && $dy < $cd2day) || ($dy == $cd2day && $hr < 16))) {
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='
							</div>
							<div class="RR-Type">
								<div class="RR-ShipCol1"><input type="radio" value="Next-Day Air" rel="'.number_format($nextdayair, 2, '.', ',').'" name="txt_ship_type" id="txt_ship_type12" '.$onchange_fn.'></div>
								<div class="RR-ShipCol2"><label for="txt_ship_type12">Next-Day Air</label></div>
							  	<div class="RR-ShipCol3">$'.number_format($nextdayair, 2, '.', ',').'</div>';
								if ($mnth == CRISTMAS_MONTH && (($dy == $cd2day && $hr > 15) || ($dy == $cd1day && $hr < 16))) { 
									$options .='<div class="RR-ShipCol4">&lt;--  To receive by Christmas</div>';
								}
				$options .='
							</div>
				';
				if ($mnth == CRISTMAS_MONTH && (($dy == $cd2day && $hr > 15) || ($dy == $cd1day && $hr < 16))) {
					$options .='<div class="RR-Type"><strong>NOTE:</strong> This is the last day to order to receive by Christmas</div>';
				}
			}
		}
		
		if ($ShipCountry == 'CA') {
			$shipId = 'txt_ship_type13';
			$options .='<div class="RR-Type">
							<div class="RR-ShipCol1"><input type="radio" value="Canada Air" rel="'.number_format($CanadaAir, 2).'" name="txt_ship_type" id="txt_ship_type13" checked="checked"  '.$onchange_fn.'></div>
							<div class="RR-ShipCol2"><label for="txt_ship_type13">Canada Air</label></div>
						  	<div class="RR-ShipCol3">$'.number_format($CanadaAir, 2).'</div>
						</div>';
		}
		elseif ($ShipCountry != 'US') {
			/*$options .='<div class="RR-Type">
							<div class="RR-ShipCol1"><input type="radio" value=""  name="txt_ship_type" id="txt_ship_type" '.$onchange_fn.'></div>
							<div class="RR-ShipCol2">Foreign Surface (2-5 months)</div>
						  	<div class="RR-ShipCol3">$'.number_format($ForeignSurface, 2).'</div>
						</div>';*/
			$shipId = 'txt_ship_type14';		
			$options .='<div class="RR-Type">
							<div><strong>Foreign Surface is unavailable until further notice per USPS changes effective 5/14/07</strong></div>
						</div>
						<div class="RR-Type">
							<div class="RR-ShipCol1"><input type="radio" value="Foreign Air (3-8 weeks)" rel="'.number_format($ForeignAir, 2).'" name="txt_ship_type" id="txt_ship_type14" checked="checked" '.$onchange_fn.'></div>
							<div class="RR-ShipCol2"><label for="txt_ship_type14">Foreign Air (3-8 weeks)</label></div>
						  	<div class="RR-ShipCol3">$'.number_format($ForeignAir, 2).'</div>
						</div>';			
						
						
		}

 		if($shipId!='') {
			$options .= ' <script>  window.onload =  function() { processShippingCharge($(\''.$shipId.'\')); } </script>';
		}
		
		return $options;
	}
	
	
	function getMonthDropDown($selectedmonth){
		$monthdata .= '<option value="">--Select--</option>';
 		for($i=1; $i <= 12; $i++) {
			$monthdata .= '<option value="'.sprintf("%02d",$i).'"';
			if($selectedmonth==sprintf("%02d",$i))  $monthdata .= 'selected="selected"';
			$monthdata .= '>'.date('F',mktime(0, 0, 0, $i+1, 0, 0, 0)).'</option>';
			//mktime(0,0,0,$i)
		}
 		return $monthdata;
	}
	
	function getYearDropDown($selectedyear){
		$yeardata .= '<option value="">--Select--</option>';
 		for($i=date('Y'); $i >= (date('Y')-10); $i--) {
			$yeardata .= '<option value="'.$i.'"';
			if($selectedyear==$i)  $yeardata .= 'selected="selected"';
			$yeardata .= '>'.$i.'</option>';
		}
 		return $yeardata;
	}
	
	function FormatOrderId($orderId) {
		return sprintf("%08d",$orderId);
	}
	
	function EmptyCartInformation() {
		$getRes = $this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'","*");	
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				if($res['type']=='book') {
					$this->Update(TABLE_PRODUCTS,array('purchased_quantity'=>'(purchased_quantity-1)'),"product_id='".$res['product_id']."'");
				}
			}
		}
	 	$session_data=$this->Delete(TABLE_CART_SESSION,"session_id='".session_id()."'");
	}
	
	function GetCustomerName($id)
	{
		$getResult=$this->Select(TABLE_CUSTOMER, " customer_id=".$id, "UPPER(CONCAT(first_name,' ',last_name)) as Customer_Name");
		return $getResult[0]['Customer_Name'];
	}
	
	function InsertCustomerInfo($post_arr, $customer_id) {
		$org_arr = $post_arr;
		// Defining array for assigning the data type of the field values.
		$arr_type=array('txt_e_mail'=>'STRING', 'txt_company_name'=>'STRING','txt_first_name'=>'STRING','txt_last_name'=>'STRING','txt_street_address'=>'STRING','txt_street_address_2'=>'STRING','txt_country'=>'STRING','txt_state'=>'STRING','txt_city'=>'STRING','txt_zip_code'=>'STRING','txt_phone'=>'STRING','txt_fax'=>'STRING','add_date'=>'NOW()','updated_date'=>'NOW()','customer_id'=>"'".$customer_id."'");
		$post_arr=$this->MySqlFormat($post_arr,$arr_type);

		if($org_arr['txt_country']!="229")
			$post_arr['txt_state']=	"'".addslashes(trim($org_arr['txt_state_other']))."'";

		$keyvalue_arr=array('txt_e_mail'=>'e_mail','txt_company_name'=>'company_name','txt_first_name'=>'first_name','txt_last_name'=>'last_name','txt_street_address'=>'street_address','txt_street_address_2'=>'street_address_2','txt_country'=>'country','txt_state'=>'state','txt_city'=>'city','txt_zip_code'=>'zip_code','txt_phone'=>'phone','txt_fax'=>'fax');
		$cust_id = $this->Insert(TABLE_CUSTOMER_INFO,$post_arr,$keyvalue_arr);
		
	}

	function GetCategoryDescendent($catId,$parentIds){	
		if($catId!='') {
			$searchCat = "'".$parentIds.$catId."-%"."'";		
			$catResult = $this->Select(TABLE_CATEGORY," category_id=".$catId." OR parent_ids LIKE ".$searchCat." AND is_deleted=0 AND is_active=1","category_id,parent_ids");		
			$catArray = array();		
			foreach($catResult  as $value){
				$catArray[] = $value['category_id'];
			}
			if(count($catArray) > 0)
				$categoryListing = implode(", ",$catArray);
		}
		return $categoryListing;	
	}
	function GetCategoryAscendent($catId){
		if($catId!='') {
			$catResult = $this->Select(TABLE_CATEGORY," category_id=".$catId." AND is_deleted=0 AND is_active=1","category_id,parent_ids");		
			$catArray = array();		
			foreach($catResult  as $value){
				if($value['parent_ids'][strlen($value['parent_ids'])-1]=='-') $value['parent_ids'] = substr($value['parent_ids'],0,strlen($value['parent_ids'])-1);
				if(trim($value['parent_ids'])!='') 
					$catArray = explode("-",$value['parent_ids']);
				
			}	
		}	
		return $catArray;
	}

	function GetParentIdStr($parentId) {
		if($parentId==0) return "";
		$get_cat=$this->Select(TABLE_CATEGORY,"category_id='".$parentId."' and is_deleted=0","parent_ids");
		return $get_cat[0]['parent_ids'].$parentId."-";
	}
	/*function GetCatTreeAscendent($parent_id,$cat_id,$parent_ids,$block_id) {
		if($parent_id!=0 && $parent_id==$block_id) return "";
		$get_categories = $this->Select(TABLE_CATEGORY,'parent_id="'.$parent_id.'" and is_deleted=0','*','category');
		$tot_cat = count($get_categories);
		$i=0;
		if($tot_cat>0) {
			$data.='<li class="expandable '.($parent_id==0?'none':'line').'" id="tree-subcat-'.$parent_id.'">';
			foreach($get_categories as $category) {
				if($category['category_id']!=$block_id) {
					$i++;
					$li_class='';
					if($tot_cat==$i) $li_class.=' half';
					
						$check_child = $this->Select(TABLE_CATEGORY,'parent_id="'.$category['category_id'].'" and category_id!="'.$block_id.'" and is_deleted=0','count(category_id) as cat_count');
						if($check_child[0]['cat_count']>0) {
							if(!in_array($category['category_id'],$parent_ids)) $li_class.=' collapsed';
						} else $li_class.=' final';
					 
					if($cat_id==$category['category_id']) $li_class .= ' active';
					$data.='<ul>
									<li class="'.$li_class.'">
										<a href="javascript:void(0);" id="tree-cat-'.$category['category_id'].'" rel="tree-parent-'.$category['parent_ids'].'">&nbsp;</a><label>'.ucwords(strtolower($category['category'])).'</label>
									</li>';
					if(in_array($category['category_id'],$parent_ids) && $category['category_id']!=$block_id) {	
						$data.=$this->GetCatTreeAscendent($category['category_id'],$cat_id,$parent_ids,$block_id);
					}
					$data.='				
								</ul>
							';
				}
			}
			$data.='</li>';
		}
		return $data;
	}*/
	function GetCatTreeAscendent($parent_id,$cat_id,$parent_ids,$block_id) {
		if(!is_array($block_id)) $block_id = explode(",",$block_id);
		if($parent_id!=0 && in_array($parent_id,$block_id)) return "";
		$get_categories = $this->Select(TABLE_CATEGORY." as cat LEFT JOIN ".TABLE_CATEGORY_REL." as cat_rel ON cat.category_id = cat_rel.category_id",'cat_rel.parent_id="'.$parent_id.'" and cat.is_deleted=0','cat.*, cat_rel.parent_id as pid','cat.category');
		$tot_cat = count($get_categories);
		$i=0;
		if($tot_cat>0) {
			$data.='<li class="expandable '.($parent_id==0?'none':'line').'" id="tree-subcat-'.$parent_id.'">';
			foreach($get_categories as $category) {
				if(!in_array($category['category_id'],$block_id)) {
					$i++;
					$li_class='';
					if($tot_cat==$i) $li_class.=' half';
					
						$check_child = $this->Select(TABLE_CATEGORY." as cat LEFT JOIN ".TABLE_CATEGORY_REL." as cat_rel ON cat.category_id = cat_rel.category_id",'cat_rel.parent_id="'.$category['category_id'].'" and cat_rel.category_id NOT IN ('.(implode(",",$block_id)).') and cat.is_deleted=0','count(cat.category_id) as cat_count');
						if($check_child[0]['cat_count']>0) {
							if(!in_array($category['category_id'],$parent_ids)) $li_class.=' collapsed';
						} else $li_class.=' final';
					 
					if($cat_id==$category['category_id']) $li_class .= ' active';
					$data.='<ul>
									<li class="'.$li_class.'">
										<a href="javascript:void(0);" id="tree-cat-'.$category['category_id'].'" rel="tree-parent-'.$category['pid'].'">&nbsp;</a><label>'.ucwords(strtolower($category['category'])).'</label>
									</li>';
					if(in_array($category['category_id'],$parent_ids) && !in_array($category['category_id'],$block_id)) {	
						$data.=$this->GetCatTreeAscendent($category['category_id'],$cat_id,$parent_ids,$block_id);
					}
					$data.='				
								</ul>
							';
				}
			}
			$data.='</li>';
		}
		return $data;
	}
	function GetCategoryTree($catId,$include_cat=false,$sep_str = " -> ") {
		$catArr = $this->GetCategoryAscendent($catId);
		$sep = "";
		$cat_tree = "";
		if($include_cat) $catArr[] = $catId;
		if(count($catArr)>0) {
			$catStr = implode(", ",$catArr);
			if($catStr!='') {
				$getRes = $this->Select(TABLE_CATEGORY,"category_id IN (".$catStr.") and is_deleted=0","category");
				foreach($getRes as $cat) {
					$cat_tree .= $sep.ucwords(strtolower($cat['category']));
					$sep = $sep_str;
				}
			}
		}
		return $cat_tree;
	}
	
	function GiftCertificateEmail($orderId,$subjectType='new',$userType='user') {
		$order_data=$this->Select(TABLE_GIFT_CERTIFICATES,"gift_id='".$orderId."'","*","");
		$i=1;  $j=1;
 		if(count($order_data)>0) {
			$cartOrderMailStatus = 1;
			foreach($order_data as $arr) {
				$data_arr=$arr;
			}//endforeach
		}//endif
		
		ob_start();
		include(ROOT.'email-templates/gift.php');
		$gift_email = ob_get_clean();
		
		$signature = $this->WebSettings['signature'];
		$signature = str_replace("{SITE_NAME}",SITE_NAME,$signature);
		$signature = str_replace("{SITE_URL}",SITE_URL,$signature);
		
		
		$orderMailBody = $this->getGiftCertificateEmailTemplate($data_arr, $mode);
		
		$gift_email = str_replace("{GIFT_CONTENT}",$orderMailBody,$gift_email);		
		if($userType == 'user') {
			$gift_email = str_replace("{FIRST_NAME}",ucfirst($data_arr['first_name']),$gift_email);
			$gift_email = str_replace("{LAST_NAME}"," ".ucfirst($data_arr['last_name']),$gift_email);			
			
			$gift_content = nl2br($this->WebSettings['gift_email_user_format']);
			$gift_email = str_replace("{GIFT_DETAILS}",$gift_content,$gift_email);
			$gift_email = str_replace("{GIFT_CERTIFICATE_CONTENT}",$orderMailBody,$gift_email);
			
			//$order_email = str_replace("{GIFT_TEXT}","You will find your Gift Certificate request Details below.",$order_email);
		}else {
			$gift_email = str_replace("{FIRST_NAME}","Administrator",$gift_email);
			$gift_email = str_replace("{LAST_NAME}","",$gift_email);
			
			$gift_content = nl2br($this->WebSettings['gift_email_admin_format']);
			$gift_email = str_replace("{GIFT_DETAILS}",$gift_content,$gift_email);
			$gift_email = str_replace("{GIFT_CERTIFICATE_CONTENT}",$orderMailBody,$gift_email);
			
			//$order_email = str_replace("{GIFT_TEXT}","Please find Gift Certificate request Details below.",$order_email);
		}		
		$gift_email = str_replace("{SIGNATURE}",nl2br($signature),$gift_email);	
		if($userType=='user') {
			$email = $data_arr['e_mail'];
		} else {
			$email = $this->EmailNotyfication('gift');
		}	
		if($subjectType=='' || $subjectType=='new') {
			//$order_subject="Gift Request ".$this->FormatGiftId($data_arr['gift_id'])." Confirmation from ".html_entity_decode(SITE_NAME);
			if($userType=='user')  $gift_subject=$this->WebSettings['gift_email_user_subject']; 
			else 	$gift_subject=$this->WebSettings['gift_email_admin_subject'];
		
			$data_arr['comments']="-";
			$gift_email = str_replace("{GIFT_IAMGE}",'<img src="'.SITE_URL.'images/email/email-gift-success.jpg" border="0" alt="Gift Certificate Request Received." title="Gift Certificate Request Received." />',$gift_email);
		} elseif($subjectType=='status') {
			$gift_subject="Gift Request ".$this->FormatGiftId($data_arr['gift_id'])." Status from ".html_entity_decode(SITE_NAME);
			$gift_email = str_replace("{GIFT_IAMGE}",'<img src="'.SITE_URL.'images/email/email-gift-updated.jpg" border="0" alt="Gift Certificate Request Status Updated." title="Gift Certificate Request Status Updated." />',$gift_email);
		}		
		//$order_subject = $mail_subject;	
		$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
		if(defined('BCC_EMAIL') && BCC_EMAIL!="")
			$headers .= 'Bcc: ' . BCC_EMAIL . " \r\n";
			
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
		
		/*echo "Email=============>".$email;
		echo "<br />";
		echo "Subject==============>".$gift_subject;
		echo "<br>";
		echo "Body=============>".$gift_email;
		echo "<br>";
		echo "Headers============>".$headers;	die;*/	
		//echo $order_email; die;//."<br />".$email."<br />".$order_subject; die;
		return @mail($email,$gift_subject,$gift_email,$headers);
		//echo $orderMailBody; echo "<br>";	
	}//end function
	
	function getGiftCertificateEmailTemplate($data_arr, $mode=''){ //print_r($data_arr); die;
		$orderId 	 = '<strong>'.$this->FormatGiftId($data_arr['gift_id']).'</strong>';
		$giftAmt  = sprintf("%01.2f",$data_arr['gift_amount']);
		$orderDate 	 = date('jS M,Y',strtotime($data_arr['add_date']));
		$status		 = strtoupper($data_arr['status']);
		$comments	 = nl2br(stripslashes($data_arr['comments']));
		
		$FirstName = stripslashes($data_arr['first_name']);
		$LastName = stripslashes($data_arr['last_name']);
		$Address = stripslashes($data_arr['address']);
		$Address2 = stripslashes($data_arr['address2']);
		$City = stripslashes($data_arr['city']);
		$State = stripslashes($data_arr['state']);
		$ZipCode = stripslashes($data_arr['zip_code']);
		$Country = stripslashes($data_arr['country']);
		$Phone = stripslashes($data_arr['phone']);
		$Fax = stripslashes($data_arr['fax']);
		$email = stripslashes($data_arr['e_mail']);
		
		$rFirstName = stripslashes($data_arr['r_first_name']);
		$rLastName = stripslashes($data_arr['r_last_name']);
		$rAddress = stripslashes($data_arr['r_address']);
		$rAddress2 = stripslashes($data_arr['r_address2']);
		$rCity = stripslashes($data_arr['r_city']);
		$rState = stripslashes($data_arr['r_state']);
		$rZipCode = stripslashes($data_arr['r_zip_code']);
		$rCountry = stripslashes($data_arr['r_country']);
		$paymentMethod = (trim($data_arr['payment_type'])=='PP'?'Paypal':'Transfer Credit Card');
		$paypalTransactionId = (trim($data_arr['payment_type'])=='PP'?$data_arr['transaction_id']:'');
		//print_r($data_arr);die;
		$paymentStatus = (trim($data_arr['is_paid'])=='1'?'PAID':'UNPAID');

		if($data_arr['payment_type']=='PP' && trim($data_arr['is_paid'])==1)  	$paySuccess = 1;
		else   $paySuccess = 0;

		$order_message='<table width="650" border="0" cellspacing="0" cellpadding="0" bgcolor="#E8E8E8" align="center">
					  <tr>
						<td colspan="2" align="left" ></td>
					  </tr>
					  <tr>
						<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #D4D4D4">
							<tr height="22" bgcolor="#6190BF">
							  <td colspan="2" align="center" width="100%" height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;">&nbsp;Gift Certificate Request Details</td>
							</tr>
							<tr>
							  <td colspan="2" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2" >
								  <tr>
									<td width="25%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Request No: </td>
									<td width="75%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$orderId.'</td>
								  </tr>
								  <tr>
									<td width="25%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Gift Amount: </td>
									<td width="75%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">$'.$giftAmt.'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Request Date:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$orderDate.'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Payment Type:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$paymentMethod.'</td>
								  </tr>'.
								  (trim($paypalTransactionId) && $paySuccess==1?'<tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Paypal Transaction Id:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$paypalTransactionId.'</td>
								  </tr>':'').'
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Payment Status:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$paymentStatus.'</td>
								  </tr>';
				if($comments){	  
					$order_message.='<tr>
										<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Comments:</td>
										<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">'.$comments.'</td>
									 </tr>';
				}
				$order_message.='</table></td>
							</tr>
							
							<tr>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
							</tr>
							<tr>
							  <td width="50%" align="center" height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;"><strong>Sender Information</strong></td>
							  <td width="50%" align="center" height="22" bgcolor="#6190BF" style="font-family:verdana; font-size:11px; font-weight:bold;color:#FFFFFF;padding-left:10px;height:22px; background-color:#6190BF;"><strong>Recipient Information</strong></td>
							</tr>
							<tr>
							  <td align="right" valign="top" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2">
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">First Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($FirstName)?$FirstName:'-').'</td>
								  </tr>
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">Last Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($LastName)?$LastName:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Street Address:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($Address)?$Address:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Address 2:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($Address2)?$Address2:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">City:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($City)?$City:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">State/Province:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($State)?$State:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Zip/Postal Code:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($ZipCode)?$ZipCode:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Country:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($Country)?$this->GetCountryName($Country):'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">E-Mail:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($email)?$email:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Telephone:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($Phone)?$Phone:'-').'</td>
								  </tr>
								</table></td>
							  <td align="left" style="font-family:verdana; font-size:11px; color:#000000;" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">First Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($rFirstName)?$rFirstName:'-').'</td>
								  </tr>
								  <tr>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">Last Name:</td>
									<td align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.(trim($rLastName)?$rLastName:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Street Address:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($rAddress)?$rAddress:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Address 2:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($rAddress2)?$rAddress2:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">City:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($rCity)?$rCity:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">State/Province:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($rState)?$rState:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Zip/Postal Code:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($rZipCode)?$rZipCode:'-').'</td>
								  </tr>
								  <tr>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Country:</td>
									<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.(trim($rCountry)?$this->GetCountryName($rCountry):'-').'</td>
								  </tr>
								</table></td>
							</tr>
							<tr>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
							  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
							</tr>
							<tr><td colspan="2" height="10"></td></tr>
						  </table></td>
					  </tr>';
			$order_message .='</table>';
					
			return $order_message;
	}
	
	function FormatGiftId($giftId) {
		return  "Gift-".sprintf("%04d",$giftId);
	}
	
	function GetProductName($productId) {
		$get_result = $this->Select(TABLE_PRODUCTS,"is_deleted=0 AND product_id='".$productId."'"," product_title, CONCAT(LPAD(product_number,6,'0')) as product_number");
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$data = htmlentities(ucfirst(stripslashes($result['product_title'])))." [".$result['product_number']."]";
			}
		}
		return $data;
		
	}
	function GetCreditCardInfo($order_details) {
 		$fontfile = "./fonts/arial.ttf";
		$fontfileitalic = "./fonts/ariali.ttf";
  		$fontfilebold = "./fonts/arialbd.ttf";
		$size = 10;
		$h = 93;
		$w = 450;
		$im  =  imagecreate($w, $h);
		$fill = imagecolorallocate($im, 168, 211, 230);
		$light = imagecolorallocate($im, 246, 246, 246);
		$corners = imagecolorallocate($im, 153, 153, 102);
		$dark = imagecolorallocate($im, 51, 51 , 0);
		$black = imagecolorallocate($im , 79, 79 , 79);
		$brown = imagecolorallocate($im , 193, 160 , 91);
	
		$colors = imagecolorallocate($im, 255, 255, 255);
		
 		imagefill($im, 0, 0, $light);
		header("Content-Type: image/png");
		header("Cache-Control: no-cache, must-revalidate");
		 
		imagettftext($im, $size, 0, 0, 12, $black, $fontfilebold, "Name on Card: ");
		imagettftext($im, $size, 0, 120, 12, $black, $fontfile,strtoupper($order_details['card_holder_name']));
		
		imagettftext($im, $size, 0, 0, 32, $black, $fontfilebold, "Card Type: ");
		imagettftext($im, $size, 0, 120, 32, $black, $fontfile, $order_details['card_type']);
		
		imagettftext($im, $size, 0, 0, 52, $black, $fontfilebold, "Card Number: ");
		imagettftext($im, $size, 0, 120, 52, $black, $fontfile, $order_details['card_number']);
		
		imagettftext($im, $size, 0, 0, 72, $black, $fontfilebold, "Expiration Date: ");
		imagettftext($im, $size, 0, 120, 72, $black, $fontfile, sprintf("%02d",$order_details['card_expmonth'])." / ".$order_details['card_expyear']. ' [MM/YYYY]');
		
		imagettftext($im, $size, 0, 0, 92, $black, $fontfilebold, "CVV Code: ");
		imagettftext($im, $size, 0, 120, 92, $black, $fontfile, $order_details['card_cvv']);
	
		//imageline($im, 0, 0, $w - 1, 0, $black);
		//imageline($im, 0, 0, 0, $h - 1, $black);
		//imageline($im, 0, $h - 1, $w - 1, $h - 1, $black);
		//imageline($im, $w - 1, 0, $w - 1, $h - 1, $black);
	
		ImagePNG($im);
		imagedestroy($im);
		
	
 	}
	function GetEmploymentReceiverEmail($id){
		$getEmpEmails = $this->Select(TABLE_EMPLOYMENT,"emp_id='".$id."'","receiver_emails");
		$tempArr = array();
		if($getEmpEmails[0]['receiver_emails'] != "") {
			$empIds = explode(",",$getEmpEmails[0]['receiver_emails']);
			foreach($empIds as $result) {
				$empEmailsResult = $this->Select(TABLE_EMP_EMAILS,"emp_email_id='".$result."'","email");
				if(count($empEmailsResult) > 0) {
					$tempArr[] = $empEmailsResult[0]['email'];
				}
			}
			$imEmails = implode(",",$tempArr);			 
		}else {
			$imEmails = $this->GetAdminEmail(); 	
		}
		return $imEmails;		
	}
	
	function GetSuperAdminEmailId() {
		$getEmails = $this->Select(TABLE_EMP_EMAILS,"is_super='1'","email");
		if(count($getEmails)>0) {
			return $getEmails[0]['email'];
		} else return "";
	}
	
	function GetAvailabilityDropDown($selected,$onlyArr=false) {
		$avl_arr = array(0=>'Available', 1=>'Not Available');
		if(!$onlyArr){
			foreach($avl_arr as $key=>$value) {
				$data .= '<option value="'.$value.'"';
				if($selected == $key ) $data .= ' selected="selected"';
				$data .= '>'.$value.'</option>';
			}
			//echo $data; exit;
			return $data;
		} else {
			return $avl_arr;
		}
	}
	function GetlqflagDropDown($selected,$onlyArr=false) {
		$avl_arr = array(0=>'Off', 1=>'Out of Print', 2=>'Out of Stock',3=>'Not Yet Published', 4=>'No Longer Available', 5=>'Low Sales', 6=>'Other');
		if(!$onlyArr){
			foreach($avl_arr as $key=>$value) {
				$data .= '<option value="'.$value.'"';
				if($selected == $key ) $data .= ' selected="selected"';
				$data .= '>'.$value.'</option>';
			}
			//echo $data; exit;
			return $data;
		} else {
			return $avl_arr;
		}
	}
	
	function GetCautionsDropDown($selected,$onlyArr=false) {
		$c_arr = array(0=>'Small Parts', 1=>'Uninflated ballons', 2=>'This Toy is a small ball',3=>'Toy contains a small ball', 4=>'This Toy is a marble', 5=>'Toy contains a marble');
		if(!$onlyArr){
			foreach($c_arr as $key=>$value) {
				$data .= '<option value="'.$value.'"';
				if($selected == $key ) $data .= ' selected="selected"';
				$data .= '>'.$value.'</option>';
			}
			//echo $data; exit;
			return $data;
		} else {
			$i=0;
			foreach($c_arr as $key=>$value) {
				$getArr[$i]['val'] = ($key+1);
				$getArr[$i]['name'] = $value;
				$i++;
			}
			return $getArr;
		}
	}
	function GetAvailabilityStatusMsg($avail) {
		$avl_arr = array(1=>':: Not Available ::');
		return $avl_arr[$avail];
	}
	function GetStockStatusMsg($msgId) {
		/*$avl_arr = array(0=>'Available', 1=>'Product is out of print or discontinued by publisher/manufacturer', 2=>'Product is out of stock according to publisher/manufacturer. Future availability is uncertain.',3=>'Product has not yet been published/released. Check back for availability.', 4=>'Product is no longer available to us.');
		if($msgId>4) return " :: Not Available :: ";
		else 	return $avl_arr[$msgId];*/
		$avl_arr = array(1=>'Product is out of print or discontinued by publisher/manufacturer', 2=>'Product is out of stock according to publisher/manufacturer. Future availability is uncertain.',3=>'Product has not yet been published/released. Check back for availability.', 4=>'Product is no longer available to us.');
		if($msgId>4) return " :: Not Available :: ";
		else return $avl_arr[$msgId];
	}
	function GetStockStatusMsgProducts($msgId) {	
		$avl_arr = array(0=>'Off',1=>'Out of Print', 2=>'Out of Stock',3=>'Not Yet Published', 4=>'No Longer Available', 5=>'Low Sales', 6=>'Other');	
		return $avl_arr[$msgId];
	}
	function GetAvailableStatusMsgId($msg){
		$avl_arr = array(0=>'available',1=>'not available');		
		$msg = str_replace("  "," ",strtolower(trim($msg)));
		$msg = str_replace("  "," ",$msg);
		$msg = str_replace("  "," ",$msg);
		$msg = str_replace("  "," ",$msg);
		$msg = str_replace("  "," ",$msg);
		$msg_bol = false;
		foreach($avl_arr as $key=>$avl) {
			if($msg_bol) continue;
			if($avl == $msg) {
				$msgid = $key;
				$msg_bol = true;
			}
		}		
		return $msgid;
	}
	function GetStockStatusMsgId($msg) {
		$avl_arr = array(0=>'off',1=>'out of print', 2=>'out of stock',3=>'not yet published', 4=>'no longer available', 5=>'low sales', 6=>'other');
		//if($msgId>4) return " :: Not Available :: ";
		//else 	return $avl_arr[$msgId];
		$msg = str_replace("  "," ",strtolower(trim($msg)));
		$msg = str_replace("  "," ",$msg);
		$msg = str_replace("  "," ",$msg);
		$msg = str_replace("  "," ",$msg);
		$msg = str_replace("  "," ",$msg);
		$msg_bol = false;
		foreach($avl_arr as $key=>$avl) {
			if($msg_bol) continue;
			if($avl == $msg) {
				$msgid = $key;
				$msg_bol = true;
			}
		}
		if($msg_bol == false) $msgid = 6;
		return $msgid;
	}
	
	function GetGradeName($grade_id) {
		$grade_result =  $this->Select(TABLE_GRADE,"is_deleted=0 AND is_active=1 AND grade_id='".$grade_id."'","grade_name");
		return $grade_result[0]['grade_name'];
	}
	function GetGradeString($st_grade_id, $end_grade_id) {
		$st_grade = $this->GetGradeName($st_grade_id);
		$end_grade = $this->GetGradeName($end_grade_id);
		if($st_grade_id != $end_grade_id) {
			if($st_grade!='') {
				$grade_name = $st_grade;
				$sep = ' - ';
			}
			if($end_grade!='') {
				$grade_name .= $sep.$end_grade;
			}
		}else {
			$grade_name = $st_grade;
		}
		return $grade_name;
	}
	
	function GetCautionData($caution_ids) {
		$cautionArr = explode("|",$caution_ids);
		
		foreach($cautionArr as $val) {
			$data .='<img src="'.SITE_URL.'images/caution/caution'.$val.'.gif" border="0" />&nbsp;';
		}
		return $data;
	}
	
	function VerifyUSAddressByUPS($input) {
		include_once(DIR_CLASS.'upsaddress.php');
		$ups = new upsaddress(UPS_ACCESS_KEY, UPS_USERID, UPS_PASSWORD);
		$ups->setAddr1($input['address1']);
		$ups->setAddr2($input['address2']);
	    $ups->setCity($input['city']);
	    $ups->setState($input['state']);
	    $ups->setZip($input['zip_code']);
		$response1 = $this->object_to_array($ups->getResponse());
		$av1 = 0;
		$av2 = 1;
		
		return $response1;
	}
	
	function capitalize($word) {
	    $word = strtolower($word);
	    $word = join("\"", array_map('ucwords', explode("\"", $word)));
	    $word = join("'", array_map('ucwords', explode("'", $word)));
	    $word = join("-", array_map('ucwords', explode("-", $word)));
	    $word = join("Mac", array_map('ucwords', explode("Mac", $word)));
	    $word = join("Mc", array_map('ucwords', explode("Mc", $word)));
	    return $word;
	} // end function
	
	function object_to_array($data) {
		if(is_array($data) || is_object($data)) {
			$result = array(); 
			foreach($data as $key => $value) $result[$key] = $this->object_to_array($value); 
				return $result;
			} // end if
		return $data;
	} // end function
	
	
	function RegisterNewUser($post_arr) {
		$ins_arr = $post_arr;
		
		$post_arr['txt_password'] = $ins_arr['txt_password'] = $this->GetNewPassword();
		
		$post_arr['email_verification_code'] = $verification_code = uniqid('RR');
		// Defining array for assigning the data type of the field values.
			
		$arr_type=array('txt_e_mail'=>'STRING', 'txt_password'=>'MD5','txt_company_name'=>'STRING','txt_first_name'=>'STRING','txt_last_name'=>'STRING','txt_street_address'=>'STRING','txt_street_address_2'=>'STRING','txt_country'=>'STRING','txt_state'=>'STRING','txt_city'=>'STRING','txt_zip_code'=>'STRING','txt_phone'=>'STRING','txt_fax'=>'STRING','add_date'=>'NOW()','updated_date'=>'NOW()', 'is_active' => "'1'",'email_verification_code'=>'STRING');
			 
		if($post_arr['txt_country']!=229)
			$post_arr['txt_state']=	"'".addslashes(trim($post_arr['txt_state_other']))."'"; 
		
		$post_arr=$this->MySqlFormat($post_arr,$arr_type); 		
		$keyvalue_arr=array('txt_e_mail'=>'e_mail', 'txt_password'=>'password','txt_company_name'=>'company_name','txt_first_name'=>'first_name','txt_last_name'=>'last_name','txt_street_address'=>'street_address','txt_street_address_2'=>'street_address_2','txt_country'=>'country','txt_state'=>'state','txt_city'=>'city','txt_zip_code'=>'zip_code','txt_phone'=>'phone','txt_fax'=>'fax','email_verification_code'=>'email_verification_code');

		$user_check=$this->Select(TABLE_CUSTOMER,"e_mail=".$post_arr['txt_e_mail']." and is_deleted=0","COUNT(*) as cust_count");
		if($user_check[0]['cust_count']==0) {
			$cust_id = $this->Insert(TABLE_CUSTOMER,$post_arr,$keyvalue_arr);
			/// Insert default entries into Customer Info table for populating Billing, Shipping information on checkout page.
			$this->InsertCustomerInfo($ins_arr, $cust_id); 
			$this->AddWishListGroup($cust_id,"Default");
					
			// Send mail to registered customer
			$customer_mail_format = $this->WebSettings['client_register_user'];	
					
			if($ins_arr['txt_state_other']!="")
				$state	=	stripslashes(trim($ins_arr['txt_state_other']));
			else
				$state	=	stripslashes($ins_arr['txt_state']);
				
			
			$first_name = 	ucfirst(stripslashes($ins_arr['txt_first_name']));
			$last_name	=	ucfirst(stripslashes($ins_arr['txt_last_name']));
			$email		=	stripslashes($ins_arr['txt_e_mail']);
			$phone		=	stripslashes($ins_arr['txt_phone']);
			$city		=	stripslashes($ins_arr['txt_city']);
			$zip		=	stripslashes($ins_arr['txt_zip_code']);
			
			$mail_arr=array(
								'FIRST_NAME'=>$first_name,
								'LAST_NAME'=>$last_name,
								'E_MAIL'=>$email, 
								'PHONE'=>$phone, 
								'CITY'=>$city, 
								'STATE'=>$state, 
								'ZIP'=>$zip, 
								'from'=>NOREPLY_EMAIL,
								'EMAIL'=>$email
							);
			$mail_arr['SITE_URL']=SITE_URL;
			$mail_arr['SITE_NAME']=SITE_NAME;
			$mail_arr['SITE_NAME']=$this->WebSettings['signature'];					
			global $gRegistrationMail, $gRegistrationAdminMail;
			//$this->SendMail("Welcome to ".SITE_NAME,$gRegistrationMail,$mail_arr);
			$subject= $this->WebSettings['registration_form_subject'];
			ob_start();
			include(ROOT.'email-templates/registration.php');
			$registration_email = ob_get_clean();
			$registration_email = str_replace("{REGISTRATION_CONTENT}",nl2br($customer_mail_format),$registration_email);
			$registration_email = str_replace("{USERNAME}",$ins_arr['txt_e_mail'],$registration_email);
			$registration_email = str_replace("{PASSWORD}",$ins_arr['txt_password'],$registration_email);
			$registration_email = str_replace("{REGISTRATION_CONFIRM}",$this->MakeUrl('registration/confirm/'.$verification_code),$registration_email);
			 
			$this->SendMail($subject,$registration_email,$mail_arr,'text/html',array(),array());
			
			//Sending Mail to administrator
			$admin_mail_format = $this->WebSettings['client_register_admin'];
			
			
			
			
			/*=====================================================================================================
				1. This function EmailNotyfication('registration') is used for fetching emails from emp_emails table.
				2. Emails list display according passing parameter eg: "registration"
				   All emails display from registeration page only which is set in admin panel.
				3. Use below parametr which is form types
				   "include","registration","password","product","question","service","contact","catalog"
			=======================================================================================================*/
			$notuficationsEmails	=	$this->EmailNotyfication('registration');
			$mail_arr['EMAIL'] = $notuficationsEmails;
			
			$subject= $this->WebSettings['registration_form_subject_admin'];
			//$this->SendMail("New Member Registered to ".SITE_NAME,$gRegistrationAdminMail,$mail_arr);
			$this->SendMail($subject,nl2br($admin_mail_format),$mail_arr,'text/html',array(),array());
			
			return $cust_id;
		}
	}
	function validateCartData(){		 
		// This code is used to update bargain books purchased quantity for products table.
		$checkBargainResult = $this->Select(TABLE_CART_SESSION," date_add(updated_date,interval 24 hour) < NOW() AND type='book'");
		if(count($checkBargainResult) > 0) {
			foreach($checkBargainResult as $value) {
				$this->Update(TABLE_PRODUCTS,array('purchased_quantity'=>'(purchased_quantity-1)'),"product_id='".$value['product_id']."'");
			}	
		}
		
		//This code is used to delete cart session data more than 24 hours
		$this->Delete(TABLE_CART_SESSION,"date_add(updated_date,interval 24 hour) < NOW()");	
	}	
	
	function GetMultiCategoryTree($catId) {
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id");
		$cat_name=array();
		if(count($getRes)>0)  {
			$i = 0;
			foreach($getRes as $result) {
				if($result['parent_id']!=0) {
					$ret_arr = $this->GetMultiCategoryTreeRec($result['parent_id']);	
					if(count($ret_arr)>0) {
						foreach($ret_arr as $ret) {
							$cat_name[$i] .= $ret;	
							$cat_name[$i].=' -> '.$result['parent_id'];
							$i++;
						}
						$i--;
					} else {
						$cat_name[$i].=' -> '.$result['parent_id'];
					}
				}
				
				$i++;
			}
		}
		return $cat_name;
	}
	function GetMultiCategoryTreeRec($catId,$ret_id=true) {
		$getRes = $this->Select(TABLE_CATEGORY_REL.' as CAR LEFT JOIN '.TABLE_CATEGORY.' as CA ON CAR.category_id=CA.category_id'," CAR.category_id='".$catId."'","CAR.parent_id,CA.is_active,CAR.category_id");
		$cat_name = array();
		if(count($getRes)>0)  {
			$i=0;
			foreach($getRes as $result) {				
				$ret_arr = $this->GetMultiCategoryTreeRec($result['parent_id'],false);			
				if(count($ret_arr)>0) {					
					foreach($ret_arr as $ret) {											
						$cat_name[$i] .= $ret;	
						if($result['parent_id']!=0) {
							$cat_name[$i].= $this->GetLinkCategoryTitle($result['parent_id']);
						}
						if($ret_id) $cat_name[$i] .=  $this->GetLinkCategoryTitle($catId);
						$i++;
					}
					$i--;
				} else {
					if($result['parent_id']!=0) {
						$cat_name[$i].= $this->GetLinkCategoryTitle($result['parent_id']);
					}
					if($ret_id) $cat_name[$i] .= $this->GetLinkCategoryTitle($catId);
				}				 
				$i++;				
			}
		}  
		return $cat_name;
	}
	function GetLinkCategoryTitle($catId) {
		$ret_val = '<a class="navTree" href="'.$this->MakeUrl($this->mPageName.'/index/',"cat_id=".$catId,1).'">'.$this->GetCategoryTitle($catId).'</a>';
		return $ret_val;
	}
	function GetTotalParentCount($catId) {
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","COUNT(*) as cnt");
		return $getRes[0]['cnt'];
	
	}
	/*function GetMultiCategoryTree($catId) {
		$tot_parent = $this->GetTotalParentCount($catId);
		if($tot_parent > 1) {
			$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","rel_id, parent_id");
			if(count($getRes)>0)  {
				foreach($getRes as $result) {
					$this->GetMultiCategoryTree($result['parent_id']);
				}
			}
		}
	
	}*/
	
	function CreateInputArr($catId) {
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id");
		if(count($getRes)>0) {
			$i = 0;
			foreach($getRes as $res) {
				$cat_arr .= $catId."####".$res['parent_id'];
				$cat_arr .= "||||".$this->CreateInputArr($res['parent_id']);	
				$i++;
			}
		}
		return $cat_arr; 
	}
	
	function GetCategoryTitle($catId) {
		$getRes = $this->Select(TABLE_CATEGORY,"category_id='".$catId."'","category");
		return ucwords(strtolower($getRes[0]['category']));
	}
	
	function GetCategoryLinkTreeFrontEnd1($catId, $selCatId=0) {	
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id, category_id as id");
		$cat_name=array();
		if(count($getRes)>0)  {
			$i = 0;
			foreach($getRes as $result) {
				$cat_name = $this->GetCategoryLinkTreeFrontRec($result['parent_id']);	
				$cat_name[] = $result;
				$i++;
			}
		}
		return $cat_name;
	}
	function GetCategoryLinkTreeFrontRec($catId, $selCatId=0) {	
	
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id, category_id as id");
		$cat_name = array();
		if(count($getRes)>0)  {
			$i=0;
			
			foreach($getRes as $result) { //print_r($result); die;
				/*if($result['parent_id']==0) {
					$cat_name[$i] = $result;
					//$cat_name[$i]['parent_id'] = 0;
					//$cat_name[$i]['id'] = $catId;
					$cat_name[$i] = $ret;
				} else {*/
					$cat_name[$i] = $this->GetCategoryLinkTreeFrontRec($result['parent_id']);	
					$cat_name[$i] = $result;
				//}	
				$i++;
			}
		} else {
			$cat_name[$i] = $ret;
		}
		return $cat_name;
	}
	
	function CreateTree($list) { //print_r($list); die;
		$tree = array();
		$nodes = array();
		
		foreach ($list as $node) { //print_r($node); die;
		$nodes[$node['id']] = array('id' => $node['id']);
		//print_r($nodes); die;
		echo $node['parent_id']; die;
		if (is_null($node['parent_id'])) {
		$tree[$node['id']] = &$nodes[$node['id']]; echo "dsf"; die;
		}else
		{
		if (!isset($nodes[$node['parent_id']]))
		$nodes[$node['parent_id']] = array();
		$nodes[$node['parent_id']][$node['id']] = &$nodes[$node['id']];
		}
		}
		return $tree;
	}
	function GetProductCount($catId,$pids='') {
		if(trim($pids)!='') {
			$prod_condition = 'and prod_cat.product_id NOT IN ('.$pids.')';
		} else $prod_condition = '';
		$get_result = $this->Select(TABLE_PRODUCT_CATEGORIES." as prod_cat LEFT JOIN ".TABLE_PRODUCTS." as prod ON prod_cat.product_id = prod.product_id","prod_cat.category_id=".$catId." and prod.is_active=1 and prod.is_deleted=0 ".$prod_condition,"DISTINCT prod.product_id as id");
		$new_arr = array();
		if(count($get_result)>0) {
			foreach($get_result as $result) $new_arr[]=$result['id'];
		}
		if(trim($pids)!='' && count($new_arr)>0) $pids.=', ';
		return array('count'=>count($get_result),'pids'=>$pids.implode(", ",$new_arr));
	}
	function GetBargainProductCount($catId,$pids='') {
		if(trim($pids)!='') {
			$prod_condition = 'and prod_cat.product_id NOT IN ('.$pids.')';
		} else $prod_condition = '';
		$get_result = $this->Select(TABLE_PRODUCT_CATEGORIES." as prod_cat LEFT JOIN ".TABLE_PRODUCTS." as prod ON prod_cat.product_id = prod.product_id","prod_cat.category_id=".$catId." and prod.is_bargain=1 and prod.quantity > prod.purchased_quantity and prod.is_active=1 and prod.is_deleted=0 ".$prod_condition,"DISTINCT prod.product_id as id");
		$new_arr = array();
		if(count($get_result)>0) {
			foreach($get_result as $result) $new_arr[]=$result['id'];
		}
		if(trim($pids)!='' && count($new_arr)>0) $pids.=', ';
		return array('count'=>count($get_result),'pids'=>$pids.implode(", ",$new_arr));
	}
	function RefreshCategoryCount($catId,$pids='',$bpids='') {
		$get_result = $this->Select(TABLE_CATEGORY_REL." as cat_rel LEFT JOIN ".TABLE_CATEGORY." as cat ON cat_rel.category_id = cat.category_id","cat_rel.parent_id=".$catId." and is_active=1 and is_deleted=0","cat.category_id as id");
		//print_r($get_result);
		//exit;
		$pid = $pids;
		$bpid = $bpids;
		if(count($get_result)>0) {
			$pcount = 0;
			$bcount = 0;
			foreach($get_result as $result) {
				$p_arr = $this->RefreshCategoryCount($result['id'],$pids,$bpids);
				$pcount+=$p_arr['count'];
				//$pid = $p_arr['pids'];
				if(trim($pid)!='' && $p_arr['pids']!='') $pid.=', ';
				$pid.=$p_arr['pids'];
				
				$bcount+=$p_arr['bcount'];
				//$pid = $p_arr['pids'];
				if(trim($bpid)!='' && $p_arr['bpids']!='') $bpid.=', ';
				$bpid.=$p_arr['bpids'];
			}
		} 
		if(trim($pid)!='')
			$pid_arr = array_unique(explode(", ",$pid));
		else $pid_arr = array();
		$pid = implode(", ",$pid_arr);
		$pcount = count($pid_arr);
		$p_arr = $this->GetProductCount($catId,$pid);
		$pcount += $p_arr['count'];
		
		if(trim($bpid)!='')
			$bpid_arr = array_unique(explode(", ",$bpid));
		else $bpid_arr = array();
		$bpid = implode(", ",$bpid_arr);
		$bcount = count($bpid_arr);
 		
		$bp_arr = $this->GetBargainProductCount($catId,$bpid);
		$bcount += $bp_arr['count'];
		
		
		$this->Update(TABLE_CATEGORY_COUNT,array('products_count'=>$pcount,'bargain_count'=>$bcount),'category_id="'.$catId.'"');
		//$this->Update(TABLE_PRODUCTS_IN_CATEGORY,array('product_ids'=>'"'.$p_arr['pids'].'"','bargain_book_ids'=>'"'.$bp_arr['pids'].'"'),'category_id="'.$catId.'"');
		
		return array('count'=>$pcount,'pids'=>$p_arr['pids'],'bcount'=>$bcount,'bpids'=>$bp_arr['pids']);
	}
	function GetCatSubject($catId) {
		$get_result = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id");
		$cat_arr=array();
		$i=0;
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				if($result['parent_id']==0) {
					return $catId;
				} else {
					$cat_ids = $this->GetCatSubject($result['parent_id']);
					if(is_array($cat_ids)) {
						if(count($cat_ids)>0) {
							foreach($cat_ids as $id) {
								$cat_arr[$i]=$id;
								$i++;
							}
						}
					} else {
						$cat_arr[$i]=$cat_ids;
						$i++;
					}
				}
			}
		}
		return $cat_arr;
	}
	function RefreshProductCount($prodId) {
		$get_result = $this->Select(TABLE_PRODUCT_CATEGORIES."","product_id='".$prodId."'","category_id");
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$this->RefreshSubCategoryCount($result['category_id']);
			}
		}
	}
	
	function RefreshSubCategoryCount($catId) {
		$sub_arr = $this->GetCatSubject($catId);
		if(count($sub_arr)>0) {
			foreach($sub_arr as $sub) {
				$this->RefreshCategoryCount($sub);
			}
		}
	}
	
	function GetMultiBreadCrumb($catId,$ret_id=true) {
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id");
		$cat_name = array();
		
		if(count($getRes)>0)  {
			$i=0;
			foreach($getRes as $result) {
				//if($result['parent_id']!=0) {
					$ret_arr = $this->GetMultiBreadCrumb($result['parent_id'],false);	
					if(count($ret_arr)>0) {
						foreach($ret_arr as $ret) {
							$cat_name[$i] .= $ret;	
							if($result['parent_id']!=0) {
								$cat_name[$i].= '<img src="'.SITE_URL.'images/admin/cat_arraow.jpg" width="20px;" height="10px" align="absmiddle" >'.$this->GetCategoryTitle($result['parent_id']);
							}
							if($ret_id) $cat_name[$i] .=  '<img src="'.SITE_URL.'images/admin/cat_arraow.jpg" width="20px;" height="10px" align="absmiddle" >'.$this->GetCategoryTitle($catId);
							$i++;
						}
						$i--;
					} else {
						
						if($result['parent_id']!=0) {
							$cat_name[$i].= $this->GetCategoryTitle($result['parent_id']);
							$sep ='<img src="'.SITE_URL.'images/admin/cat_arraow.jpg" width="20px;" height="10px" align="absmiddle" >';
						}
						if($ret_id) $cat_name[$i] .= $sep.$this->GetCategoryTitle($catId);
					}
					
				//} 
				$i++;
			}
		}  
		return $cat_name;
	}
	function GetCategoryBreadCrumb($catId) {
		$getRes = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id");
		$ret_arr = array();
		if(count($getRes)>0)  {
			$i = 0;
			foreach($getRes as $result) {
				$ret_arr[$i]['id'] = $result['parent_id'];
				$breadcrumb = $this->GetMultiBreadCrumb($result['parent_id']);
				if(count($breadcrumb)==0) $breadcrumb=array('Top');
				$ret_arr[$i]['breadcrumb'] = implode("<br />",$breadcrumb);
				$i++;
			}
		}
		return $ret_arr;
	}
	function GetProductCategoryBreadCrumb($pId) {
		$getRes = $this->Select(TABLE_PRODUCT_CATEGORIES,"product_id='".$pId."'","category_id");
		$ret_arr = array();
		if(count($getRes)>0)  {
			$i = 0;
			foreach($getRes as $result) {
				$ret_arr[$i]['id'] = $result['category_id'];
				$breadcrumb = $this->GetMultiBreadCrumb($result['category_id']);
				if(count($breadcrumb)==0) $breadcrumb=array('Top');
				$ret_arr[$i]['breadcrumb'] = implode("<br />",$breadcrumb);
				$i++;
			}
		}
		return $ret_arr;
	}
	function GetCategoryParentIds($catId) {
		$get_result = $this->Select(TABLE_CATEGORY_REL,"category_id='".$catId."'","parent_id",'','1');
		$cat_arr=array();
		$i=0;
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				if($result['parent_id']!=0) {
					$cat_ids = $this->GetCategoryParentIds($result['parent_id']);
					$cat_arr = $cat_ids;
					$cat_arr[]=$result['parent_id'];
				}
			}
		}
		return $cat_arr;
	}
	
	function GetCatalogStatusDD(){
		//$arr = array("pending"=>"Pending", "approved"=>"Approved/Processing",'partly_shipped'=>'Partly Shipped','shipped'=>'Shipped','denied'=>'Denied','delivered'=>'Delivered');
		$arr = array("N"=>"New Request", "D"=>"Downloaded");	
		foreach($arr as $key=>$val) {
			$data .= '<option value="'.$key.'"';
			$data .= '>'.$val.'</option>';
		}
		return $data;
	}
	function GetStateNameByPrefix($state_prefix) {
		$getRes = $this->Select(TABLE_STATE,"state_prefix='".$state_prefix."'","");
		return $getRes[0]['state_name'];
	}
	
	function FormatCRId($crId) {
		return "CR-".sprintf("%06d",$crId);
	}
}
?>