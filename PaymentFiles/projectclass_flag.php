<?php
//-------------------------------------------------------------------------------------------------------------------------------
// Menu Class to buildup Dynamic Header Navigations Drop Down Menu.
//-------------------------------------------------------------------------------------------------------------------------------
class Project extends MainClass{
	function Project($uri) { 
		$this->MainClass($uri);
	}
	//-----------------------------------------------------------------------------------------------//
	// Method for Decryption for IDs.
	//-----------------------------------------------------------------------------------------------//
	function Decode($str) {
		return urldecode(base64_decode($str));//urldecode(base64_decode(
	}
	
	//-----------------------------------------------------------------------------------------------//
	// Method for encryption for IDs.
	//-----------------------------------------------------------------------------------------------//
	function Encode($str) {
			//return $str;
		return urlencode(base64_encode($str));//urlencode(base64_encode(
	} 
	
	function Captcha ($page_name) { 
		global $_SESSION;
		if($_SESSION['sess_captcha_'.str_replace(' ','_',$page_name)]) {
			
		}
	}
	function selfURL() { 
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; 
		$protocol = $this->strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; 
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); 
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; 
	}
	function strleft($s1, $s2) { 
		return substr($s1, 0, strpos($s1, $s2)); 
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Pagination Query Maker------------- 
	//-----------------------------------------------------------------------------------------------//
	function PagingHeader($limit,$query,$qOffset=0) {
	//	echo "Limit = ".$limit . ", Query = ".$query.", Offset = ".$qOffset;
		
		global $_SESSION,$_GET,$gStartPageNo; 
		$sess_tot_offset = $_SESSION['tot_offset'];

		if($sess_tot_offset!="" && $qOffset>0 && $qOffset<=$sess_tot_offset) {
			if($qOffset<=$sess_tot_offset) {
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
	
	function PagingHeader_old($limit,$query,$qOffset=0) {
		global $_SESSION,$_GET,$gStartPageNo;
		$tot_rows=$this->Query($query,'count');
		
		$_SESSION['tot_rows'] = $tot_rows ;
		$pages=ceil($tot_rows/$limit);
		$_SESSION['tot_offset']=$pages;
		
	//	echo $_SESSION['tot_offset'];
		if($_SESSION['tot_offset']!="" && $qOffset>0 && $qOffset<=$_SESSION['tot_offset']) {
			if($qOffset<=$_SESSION['tot_offset']) {
				$offset=ceil($qOffset);
				$offset1=ceil($qOffset * $limit);
				$query.=" LIMIT $offset1, $limit";
			} else {
				$offset=0;
				$query.=" LIMIT 0, $limit";
			}
		} else {
			$tot_rows=$this->Query($query,'count');
			//echo $tot_rows.'comes';
			$_SESSION['tot_rows'] = $tot_rows ;
			
			$pages=ceil($tot_rows/$limit);
			$_SESSION['tot_offset']=$pages;
			$offset=0;
			$query.=" LIMIT 0, $limit";
		}
		 $gStartPageNo=$offset1;
		 //$res = mysql_query($query);
		 //$numRows = mysql_num_rows($res);
		//echo (($offset*5)+1).' to '.((($offset*5)) + $numRows).' of '.$tot_rows;
		return $query;
	}
	
	
	function PagingFooterForSEO($offset,$class='links') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		$categoryTree = $this->formatUrl($this->GetCategoryTreeForNavigation(trim($_GET['collection_id'])));
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";

		if($_SESSION['tot_offset']>1) {
			$j=$offset-1;
			$k=$offset+1;
			if($offset==0)  $display_page.= '<span class="text">First</span> | <span class="text">Previous</span> | ';
			else $display_page.= "<span class=\"text\"><a href='".$this->MakeUrl($categoryTree.'/'.$this->mCurrentUrl,$gPagingExtraPara.'offset=0')."' class='".$class."'>First</a></span> | <span class=\"text\"><a href='".$this->MakeUrl($categoryTree.'/'.$this->mCurrentUrl,$gPagingExtraPara.'offset='.$j)."' class='".$class."'>Previous</a></span> | ";
			$tot_offset=$_SESSION['tot_offset'];
			$display_page.="<select name='paging_numbers' class='select_small' onchange=\"javascript: window.location='".$this->MakeUrl($categoryTree.'/'.$this->mCurrentUrl)."'+this.value;\" >";
			for($i=0;$i<$tot_offset;$i++) {
				$m=$i+1;
				if($i==$offset) $sel.= 'selected';
				else $sel="";
				$display_page.= '<option value="'.$this->Encode($gPagingExtraPara.'offset='.$i).'" '.$sel.'>' . "<a href='$page_name?offset=$i$extra_name' class='$class'>" . sprintf("%03d",$m) . '</option> ';  
			}
			$display_page.="</select>";
			if($offset==($tot_offset-1)) {
				$display_page.= '<span class="text"> | Next</span> |  <span class="text">Last</span>';
			} else {
				$display_page.= "<span class=\"text\"> | <a href='".$this->MakeUrl($categoryTree.'/'.$this->mCurrentUrl,$gPagingExtraPara.'offset='.$k)."' class='".$class."'>Next</a></span> | <span class=\"text\"><a href='".$this->MakeUrl($categoryTree.'/'.$this->mCurrentUrl,$gPagingExtraPara.'offset='.($tot_offset-1))."' class='".$class."'>Last</a></span>";
			}
		}
		return $display_page;
	}
	
	
	//-----------------------------------------------------------------------------------------------//
	// Method Pagination Display------------- FORMAT: Previous - [DROPDOWN of all page numbers available] - Next  
	//-----------------------------------------------------------------------------------------------//
	function PagingFooter($offset,$class='links') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";

		if($_SESSION['tot_offset']>1) {
			$j=$offset-1;
			$k=$offset+1;
			if($offset==0)  $display_page.= '<span class="text">First</span> | <span class="text">Previous</span> | ';
			else $display_page.= "<span class=\"text\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset=0')."' class='".$class."'>First</a></span> | <span class=\"text\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$j)."' class='".$class."'>Previous</a></span> | ";
			$tot_offset=$_SESSION['tot_offset'];
			$display_page.="<select name='paging_numbers' class='select_small' onchange=\"javascript: window.location='".$this->MakeUrl($this->mCurrentUrl)."'+this.value;\" >";
			for($i=0;$i<$tot_offset;$i++) {
				$m=$i+1;
				if($i==$offset) $sel.= 'selected';
				else $sel="";
				$display_page.= '<option value="'.$this->Encode($gPagingExtraPara.'offset='.$i).'" '.$sel.'>' . "<a href='$page_name?offset=$i$extra_name' class='$class'>" . sprintf("%03d",$m) . '</option> ';  
			}
			$display_page.="</select>";
			if($offset==($tot_offset-1)) {
				$display_page.= '<span class="text"> | Next</span> |  <span class="text">Last</span>';
			} else {
				$display_page.= "<span class=\"text\"> | <a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$k)."' class='".$class."'>Next</a></span> | <span class=\"text\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($tot_offset-1))."' class='".$class."'>Last</a></span>";
			}
		}
		return $display_page;
	}
	
	//-----------------------------------------------------------------------------------------------//
	// Method Image GD Library Ratio Resize / Thumbnail Creation ------------- 
	//-----------------------------------------------------------------------------------------------//
	function CreateThumb($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		if ($width && ($width_orig < $height_orig)) {
			$width = ($height / $height_orig) * $width_orig;
		} else {
			$height = ($width / $width_orig) * $height_orig;
		}
		//echo $width."====".$height."<br>";
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
		imagedestroy($image_p);
		
	}
	
	
	function CreateFixThumbForProduct($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		

		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		if($width_orig > $width) { 
			$height = ($width* $height_orig )/$width_orig;	// 300 is fix width of product image.
		}else{
			$height = $height_orig;
			$width = $width_orig;
		} 
		//echo $width."====".$height."<br>";
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
		imagedestroy($image_p);
		
	}
	
	//-----------------------------------------------------------------------------------------------//
	// Method Image GD Library Ratio / Fix Resize, Crop / Thumbnail Creation ------------- 
	//-----------------------------------------------------------------------------------------------//
	function CreateThumbFix($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
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
		} elseif($heightn<$height) {
			$width_r = ceil(($height / $height_orig) * $width_orig);
			$height_r=$height;
		} else {
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
	
	function UploadShowImage($source, $prefix="", $mainDir, $thumbnailDir, $thumbnail_small_Dir, $imgwidth='100', $imgheight='100') {
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
			
			//echo $thumbnailDir;die;
			if(move_uploaded_file($source,$destination)) {
				if($width>600 || $height>450)
					$this->CreateThumb($new_file,$ext,$mainDir,600,450,$mainDir);
						
				if($mainDir=="userdata/collection/"){			
					$this->CreateThumb($new_file,$ext,$mainDir,80,80,$thumbnail_small_Dir);	
					$this->CreateThumb($new_file,$ext,$mainDir,$imgwidth,$imgheight,$thumbnailDir);	
				}else if($mainDir=="userdata/products/") {
					$this->CreateFixThumbForProduct($new_file,$ext,$mainDir,PROD_THUMB_WIDTH,PROD_THUMB_HEIGHT,$thumbnailDir);	
					$this->CreateThumb($new_file,$ext,$mainDir,PROD_THUMB_SMALL_WIDTH,PROD_THUMB_SMALL_HEIGHT,$thumbnail_small_Dir);	
				}else{
					$this->CreateThumb($new_file,$ext,$mainDir,100,100,$thumbnail_small_Dir);
					$this->CreateThumb($new_file,$ext,$mainDir,$imgwidth,$imgheight,$thumbnailDir);					
				}
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
		
	}
	
	function CreateThumbHeightFix($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		/*if ($width && ($width_orig < $height_orig)) {
			$width = ($height / $height_orig) * $width_orig;
		} else {
			$height = ($width / $width_orig) * $height_orig;
		}*/
		// Resample
		$image_p = imagecreatetruecolor($width, $height);
		if(strtolower($ext)=="gif") $image = imagecreatefromgif($filename);
		elseif($ext=="jpg") $image = imagecreatefromjpeg($filename);
		elseif($ext=="png") $image = imagecreatefrompng($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// Output
		@unlink($thumbFolder .$photo . ".".$ext);
		if($ext=="gif") $image_thumb=imagegif($image_p, $thumbFolder .$photo . ".gif");
		elseif($ext=="jpg") $image_thumb=imagejpeg($image_p, $thumbFolder .$photo . ".jpg",100);
		elseif($ext=="png") $image_thumb=imagepng($image_p,$thumbFolder .$photo . ".png");
	}
	
	//-----------------------------------------------------------------------------------------------//
	// Method File Extension ------------- 
	//-----------------------------------------------------------------------------------------------//
	function GetFileExt ( $imgName ) {
		$efilename = explode('.', $imgName);
		return strtolower($efilename[count($efilename) -1 ])  ;
	}
	
	
	
	//-----------------------------------------------------------------------------------------------//
	// Method Admin Email Retrieve ------------- 
	//-----------------------------------------------------------------------------------------------//
	function GetAdminEmail() {
		$admin_data=$this->Select(TABLE_ADMIN,"","e_mail","",1);
		if(count($admin_data)) {
			foreach($admin_data as $admin) {
				return ucfirst($admin['e_mail']);
			}
		}
		else return ADMIN_EMAIL;
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Generate New Password ------------- 
	//-----------------------------------------------------------------------------------------------//
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
	//-----------------------------------------------------------------------------------------------//
	// Method State Dropdown Data ------------- 
	//-----------------------------------------------------------------------------------------------//
	function GetStateDropDown($stateSelected) {
		global $db;
		$get_result=$this->Select(TABLE_STATE,"country_id=1");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$state_data .= '<option value="'.$result['state_prefix'].'"';
				if($stateSelected == $result['state_prefix'] ) $state_data .= ' selected="selected"';
				$state_data .= '>'.$result['state_name'].'</option>';
			}
		}
		return $state_data;
	}
	
	function getCountryAbb($code){
		$get_result=$this->Select(TABLE_COUNTRY,"Code='".$code."'", 'Code2');	
		return $get_result[0]['Code2'];
	}
	
	function GetCountryDropDown($countrySelected,$type) { 
		global $db;
		if($countrySelected == "" && $type == "user"){
			$countrySelected = "USA";
		}
		$get_result=$this->Select(TABLE_COUNTRY,"","*","Name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$country_data .= '<option id="'.$result['Code'].'" value="'.$result['Code'].'"';
				if($countrySelected == $result['Code']) $country_data .= ' selected="selected"';
				$country_data .= '>'.$result['Name'].'</option>';
			}
		}
		return $country_data;
	}
	
	function GetManageList_old($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) {
		global $sort_query,$action_query,$customList;
		if($countList=="") $countList=array();
		if($customList=="") $customList=array();
		global $gStartPageNo;
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
				if($count%2==0) $bg = "dataclass";
				else $bg = "dataclassalternate";
				$no++;
				
				if($is_page_header=="") {
					$page_header='
					<tr class="ban3" height="22">
					';
				}
				 $action_count=1;
				if($is_page_header=="") {
					if($action_count>0) { 
					$page_header.='
						<td width="4%"  class="grid" colspan="'.$action_count.'" align="center"  >
							<input type="checkbox" name="chk_all" id="chk_all" value="yes" onClick="javascript: selectRowAll(this);" />
						</td>
                        ';
					}
					 
				}
				
				$page_content.= " 
					<tr height='25' class='$bg' onmouseover=this.className='tableover' onmouseout=this.className='".$bg."' onClick='javascript: selectRow(".$result['id'].",\"".$bg."\");' id='tr_list_".$result['id']."'> 
						";	
					$page_content.= "
										<td width='4%' class='grid' align='center' valign='middle' style='border-left:1px solid #D0D7E5;'  height='28' >
											<input type='hidden' name='hid_type".$result['id']."' id='hid_type".$result['id']."' value='".$result['Promo_Type']."'>
											<input type='checkbox' name='record_ids[]' id='record_id_".$result['id']."' value='".$result['id']."' onClick='javascript: selectRow(".$result['id'].",\"".$bg."\");' /></a>
										</td>";	
				$field_count=0;
				$size_count=0;
				foreach($result as $key=>$value) {
					if($key!='id' && $key!='is_active' && $key!='is_paid' && $key!='auto_notification' && $key!='cnt_subcat' && $key!='cnt_prod' && $key!='Promo_Status'){ 
						$page_content.= " <td align='left' class='grid' valign='middle' style='padding-left:7px;' >";
						if($countList['price']['custom']=='Price_Details'){
							if($key=='Price_Details'){
								if($result[$key]=='Multiple'){
									$qtyWiseData = $this->Select(TABLE_MULTIPLE_SIZE, "mproduct_id='".$result['id']."'","*", 'id');
									$page_content.="<table align='center' bgcolor='#FFFFFF' class='text' width='100%' border='0' cellpadding='2' cellspacing='1' class='text'>";
									$page_content.="<tr bgcolor='#CCCCCC' align='center'><td><b>Size</b></td><td><b>Measurement</b></td><td><b>Retail</b></td><td><b>Corporate</b></td><td><b>NPO</b></td>";
										if(count($qtyWiseData)>0) {
											foreach($qtyWiseData as $qty) {
												$page_content.="<tr bgcolor='#DEDEDE'><td align='center'>".$qty['flag_size']."</td>";
												$page_content.="<td align='center'>".$qty['size_measurement']."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['retail_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['corporate_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['npo_price'])."</td>";
												$page_content.="</tr>";
											}//end foreach
										}//end if
								}else{
										$products = $this->Select(TABLE_PRODUCTS, "product_id='".$result['id']."'","retail_price,corporate_price,npo_price", '');
										$page_content.="<table bgcolor='#FFFFFF' align='center' class='text' width='100%' border='0' cellpadding='2' cellspacing='1' class='text'>";
										$page_content.="<tr bgcolor='#CCCCCC' align='center'><td><b>Retail</b></td><td><b>Corporate</b></td><td><b>NPO</b></td>";
										if(count($products)>0) {
											foreach($products as $product) {
												$page_content.="<tr bgcolor='#DEDEDE'><td align='center'>".sprintf("$%01.2f",$product['retail_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$product['corporate_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$product['npo_price'])."</td>";
												$page_content.="</tr>";
											}//end foreach
										}//endif
								}//end if multiple
								$page_content.="</table></td>";
								$result[$key] = '';
								
							}//end if field price detail
						
						}//endif price detail block
						
						
						$page_content.= stripslashes($result[$key]). "</td>";
						
						if($is_page_header=="") {
							if($sizeArray[$size_count]!="")
								$width='width="'.$sizeArray[$size_count].'"';
							else
								$width="";
							$page_header.='
								<td  class="grid" style="padding-left:7px;" '.$width.' ';
							if($countList[$key]['table']!="" || $sortArray[$key]=='N') {
								if($key=="Sr")
									$key="Sr.No.";
									
								$page_header.='>'.str_replace("_"," ",$key) . '</td>';
							}
							else {
								
								//echo $_GET['sort']."<br />";
								if($_GET['sort']==$key) {
									$page_header.=' class="ban2" ';
									if($_GET['order']=='a') $order_q="&order=d"; else $order_q='&order=a';
								} else {
									$order_q='&order=a';
								}
								if($key=="E_Mail")
									$replace_string=str_replace("_","-",$key);
								else
									$replace_string=str_replace("_"," ",$key);
									
								//if($_GET['sort']=="Amount_Sold"){
								//	$_GET['sort']="SUM(co.order_total)";			
								//}
								
								$page_header.=' ><a href="'.$this->MakeUrl($this->mCurrentUrl,"sort=".$key.$order_q.$sort_query).'" class="whitetext">'.$replace_string.'';
								
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='<img src="'.SITE_URL.'images/icons/up.gif" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='<img src="'.SITE_URL.'images/icons/down.gif" border=0 align="absmiddle" />';
									
								$page_header.='</a></td>
									 ';
							}
							
							$size_count++;
						}
						$field_count++;
					}
					// code starts
					
					// Code ends
					
				}
				 
				if($help_icon=="")
					$help_icon="\n
						<tr>
							<td colspan='".($action_count+$field_count)."' align='left'>
								<table width='100%' border=0 height='100%' cellpadding=0 cellspacing=0 >
									<tr>
										<td class='text' valign=bottom>".$_SESSION['record_no_display']." </td>
										<td>
											".$this->GetHelpIcons($listAction,$action_query)."
										</td>
									</tr>
								</table>
							</td>
						</tr>";
				if($is_page_header=="") {
					 
					$page_header.='
					</tr>';
				}
				$page_content.= "
									</tr>
				";
			}
			$page_content.="
			<tr>
					<td align=right valign=top class=text colspan='".($action_count+$field_count)."'>
						<input type='submit' name='bt_submit' value='' style='visibility:hidden;' />
						".$_SESSION['record_no_display']."
					</td>
				</tr>
			";
			
		}
		return $help_icon.$page_header.$page_content;
	}
	function GetHelpIcons_old($keyArr,$action_query)
	{
		$content.='<table border="0" align="right" cellpadding="4" cellspacing="4" class="text" >
           <tr>';
		  
		foreach($keyArr as $key=>$value)
		{
			if($value['type']=='confirm') {
			
			} elseif($value['type']=='confirm') {
			
			} else {
			
			}
			$key1=$key;
			$key=str_replace("_"," ",$key);
			$key=str_replace("sendemail","Send E-mail",$key);
			$content.='
				
				<td align="center" valign="bottom" class="actionButton" title="'.ucfirst($key).'" onMouseOver="this.className=\'actionButton_hover\';" onMouseOut="this.className=\'actionButton\';" onClick="javascript: submitAction(\''.$key.'\',\''.$value['type'].'\',\''.addslashes($value['message']).'\',\''.$value['multi'].'\',\''.$this->MakeUrl($this->mModuleUrl."/".$key1,$action_query).'\');">
					<table border="0" align="center" cellpadding="0" cellspacing="0" class="text" width="65">
           				<tr>
							<td align="center" valign="bottom"><img src="'.SITE_URL.'images/icons/'.$key1.'.png" alt="'.ucfirst($key).'"  border="0" /></td>
						</tr>
						<tr>
							<td align="center" valign="top" style="padding-left:5px;">'.ucfirst($key).'</td>
						</tr>
					</table>
				</td>';
			 
		}
		$content.='</tr><tr>'.$content2.'</tr></table>';
		return $content;
	}
	
	
	function GetManageList($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) {
		global $sort_query,$action_query,$customList;
		if($countList=="") $countList=array();
		if($customList=="") $customList=array();
		global $gStartPageNo;
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
				if($count%2==0) $bg = "dataclass";
				else $bg = "dataclassalternate";
				$no++;
				
				if($is_page_header=="") {
					$page_header='
					<tr class="ban3" height="30">
					';
				}
				 $action_count=1;
				if($is_page_header=="") {
					if($action_count>0) { 
					$page_header.='
						<td width="4%"  class="grid" colspan="'.$action_count.'" align="center"   >
							<input type="checkbox" name="chk_all" id="chk_all" value="yes" onClick="javascript: selectRowAll(this);" />
						</td>
                        ';
					}
					 
				}
				
				$page_content.= " 
					<tr height='23' class='$bg' onmouseover=this.className='tableover' onmouseout=this.className='".$bg."' onClick='javascript: selectRow(".$result['id'].",\"".$bg."\");' id='tr_list_".$result['id']."'> 
						";	
					$page_content.= "
										<td width='4%' class='grid' align='center' valign='middle' style='border-left:1px solid #666666;'  height='23' >
											<input type='hidden' name='hid_type".$result['id']."' id='hid_type".$result['id']."' value='".$result['Promo_Type']."'>
											<input type='checkbox' name='record_ids[]' id='record_id_".$result['id']."' value='".$result['id']."' onClick='javascript: selectRow(".$result['id'].",\"".$bg."\");' /></a>
										</td>";	
				$field_count=0;
				$size_count=0;
				foreach($result as $key=>$value) {
					if($key!='id' && $key!='is_active' && $key!='is_paid' && $key!='auto_notification' && $key!='cnt_subcat' && $key!='cnt_prod' && $key!='Promo_Status'){ 
						$page_content.= " <td align='left' class='grid' valign='middle' style='padding-left:7px;' >";
						if($countList['price']['custom']=='Price_Details'){
							if($key=='Price_Details'){
								if($result[$key]=='Multiple'){
									$qtyWiseData = $this->Select(TABLE_MULTIPLE_SIZE, "mproduct_id='".$result['id']."'","*", 'id');
									$page_content.="<table align='center' bgcolor='#FFFFFF' class='text' width='100%' border='0' cellpadding='2' cellspacing='1' class='text'>";
									$page_content.="<tr bgcolor='#CCCCCC' align='center'><td><b>Size</b></td><td><b>Measurement</b></td><td><b>Retail</b></td><td><b>Corporate</b></td><td><b>NPO</b></td>";
										if(count($qtyWiseData)>0) {
											foreach($qtyWiseData as $qty) {
												$page_content.="<tr bgcolor='#DEDEDE'><td align='center'>".$qty['flag_size']."</td>";
												$page_content.="<td align='center'>".$qty['size_measurement']."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['retail_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['corporate_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['npo_price'])."</td>";
												$page_content.="</tr>";
											}//end foreach
										}//end if
								}else{
										$products = $this->Select(TABLE_PRODUCTS, "product_id='".$result['id']."'","retail_price,corporate_price,npo_price", '');
										$page_content.="<table bgcolor='#FFFFFF' align='center' class='text' width='100%' border='0' cellpadding='2' cellspacing='1' class='text'>";
										$page_content.="<tr bgcolor='#CCCCCC' align='center'><td><b>Retail</b></td><td><b>Corporate</b></td><td><b>NPO</b></td>";
										if(count($products)>0) {
											foreach($products as $product) {
												$page_content.="<tr bgcolor='#DEDEDE'><td align='center'>".sprintf("$%01.2f",$product['retail_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$product['corporate_price'])."</td>";
												$page_content.="<td align='center'>".sprintf("$%01.2f",$product['npo_price'])."</td>";
												$page_content.="</tr>";
											}//end foreach
										}//endif
								}//end if multiple
								$page_content.="</table></td>";
								$result[$key] = '';
								
							}//end if field price detail
						
						}//endif price detail block
						
						
						$page_content.= stripslashes($result[$key]). "</td>";
						
						if($is_page_header=="") {
							if($sizeArray[$size_count]!="")
								$width='width="'.$sizeArray[$size_count].'"';
							else
								$width="";
							$page_header.='
								<td  class="grid" style="padding-left:7px;" '.$width.' ';
							if($countList[$key]['table']!="" || $sortArray[$key]=='N') {
								if($key=="Sr")
									$key="Sr.No.";
									
								$page_header.='>'.str_replace("_"," ",$key) . '</td>';
							}
							else {
								
								//echo $_GET['sort']."<br />";
								if($_GET['sort']==$key) {
									$page_header.=' class="ban2" ';
									if($_GET['order']=='a') $order_q="&order=d"; else $order_q='&order=a';
								} else {
									$order_q='&order=a';
								}
								if($key=="E_Mail")
									$replace_string=str_replace("_","-",$key);
								else
									$replace_string=str_replace("_"," ",$key);
									
								//if($_GET['sort']=="Amount_Sold"){
								//	$_GET['sort']="SUM(co.order_total)";			
								//}
								
								$page_header.=' ><a href="'.$this->MakeUrl($this->mCurrentUrl,"sort=".$key.$order_q.$sort_query).'" class="whitetext">'.$replace_string.'';
								
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='<img src="'.SITE_URL.'images/icons/up.gif" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='<img src="'.SITE_URL.'images/icons/down.gif" border=0 align="absmiddle" />';
									
								$page_header.='</a></td>
									 ';
							}
							
							$size_count++;
						}
						$field_count++;
					}
					// code starts
					
					// Code ends
					
				}
				 
				if($help_icon=="")
					$help_icon="\n
						<tr>
							<td colspan='".($action_count+$field_count)."' align='left'>
								<table width='100%' border=0 height='100%' cellpadding=0 cellspacing=0 >
									<tr>
										<td class='text' valign=bottom>".$_SESSION['record_no_display']." </td>
										<td>
											".$this->GetHelpIcons($listAction,$action_query)."
										</td>
									</tr>
								</table>
							</td>
						</tr>";
				if($is_page_header=="") {
					 
					$page_header.='
					</tr>';
				}
				$page_content.= "
									</tr>
				";
			}
			$page_content.="
			<tr>
					<td align=right valign=top class=text colspan='".($action_count+$field_count)."'>
						<input type='submit' name='bt_submit' value='' style='visibility:hidden;' />
						".$_SESSION['record_no_display']."
					</td>
				</tr>
			";
			
		}
		return $help_icon.$page_header.$page_content;
	}
	function GetHelpIcons($keyArr,$action_query)
	{
		$content.='<table border="0" align="right" cellpadding="2" cellspacing="2" >
           <tr>';
		  
		foreach($keyArr as $key=>$value)
		{
			if($value['type']=='confirm') {
			
			} elseif($value['type']=='confirm') {
			
			} else {
			
			}
			$key1=$key;
			$key=str_replace("_"," ",$key);
			$key=str_replace("sendemail","Send E-mail",$key);
			$content.='
				
				<td align="center" valign="bottom" title="'.ucfirst($key).'" >
					<table border="0" align="center" cellpadding="0" cellspacing="0" class="text" width="65">
           				<tr>
							<td align="center" valign="bottom"><img src="'.SITE_URL.'images/icons/'.$key1.'.jpg" alt="'.ucfirst($key).'"  border="0" onMouseOver="this.className=\'actionButton_hover\';" onMouseOut="this.className=\'actionButton\';" onClick="javascript: submitAction(\''.$key.'\',\''.$value['type'].'\',\''.addslashes($value['message']).'\',\''.$value['multi'].'\',\''.$this->MakeUrl($this->mModuleUrl."/".$key1,$action_query).'\');" /></td>
						</tr>
						<tr>
							<td align="center" valign="top" style="padding-left:5px;"><span  class="link_help" onMouseOver="this.className=\'actionButton_hover\';" onMouseOut="this.className=\'actionButton\';" onClick="javascript: submitAction(\''.$key.'\',\''.$value['type'].'\',\''.addslashes($value['message']).'\',\''.$value['multi'].'\',\''.$this->MakeUrl($this->mModuleUrl."/".$key1,$action_query).'\');">'.ucfirst($key).'</span></td>
						</tr>
					</table>
				</td>';
			 
		}
		$content.='</tr><tr>'.$content2.'</tr></table>';
		return $content;
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Manage List / Listing / Display Records ------------- 
	//-----------------------------------------------------------------------------------------------//
	function GetManageList1($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) {
		global $sort_query,$action_query,$sort_key, $tot_rows;
		if($countList=="") $countList=array();
		global $gStartPageNo;
		if(count($listArray)==0) {
			$page_content.= " 
					<tr align='center'>
						<td colspan='10' valign='top'   class='redheading'>No Result Found</td>
					</tr>";
	   	} else {
			$color="bg_light";
			$count = $gStartPageNo;
			foreach($listArray as $result) {
				$count++;
				if($count%2==0) $bg = "dataclass";
				else $bg = "dataclassalternate";
				$no++;
				if($is_page_header=="") {
					$page_header='
					<tr class="ban3" height="22">
						 <td width="4%"  align="center">#</td>';
				}
				$page_content.= "<tr height='25' class='$bg' onmouseover=this.className='tableover' onmouseout=this.className='".$bg."'><td align='center' valign='top' width='5%'  >".sprintf("%02d",$count)."</td>";		
				$field_count=0;
				$size_count=0;
				foreach($result as $key=>$value) { 
					if($key!='id' && $key!='is_active' && $key!='is_approved'){   
						if($key=="Image"){
							$page_content.= " <td align='middle' valign='top' style='padding-left:4px;'>";	
						}else{
							$page_content.= " <td align='left' valign='top' style='padding-left:7px;' >";
						}
						
						/*if($key=='Retail_Price'){$result[$key] = '$'.$result[$key];}
						if($key=='Corporate_Price'){$result[$key] = '$'.$result[$key];}
						if($key=='Non_Profit_Price'){$result[$key] = '$'.$result[$key];}
						if($key=='Account_Type'){
							if($result[$key] == 'R')
								$result[$key] = 'Retail';
							if($result[$key] == 'C')
								$result[$key] = 'Corporate';
							if($result[$key] == 'N')
								$result[$key] = 'Non-Profir';							
						}//end if account type
						*/
						
						if($countList['price']['custom']=='Quantity_Wise'){
						
							if($key=='Retail_Price'){
								list($pType, $fPrice) = explode('|', $result[$key]);
								if($pType=='Multipe'){
									$qtyWiseData = $this->Select(TABLE_SIZE_MULTIPLE, "product_id='".$result['id']."'","retail_price,corporate_price, npo_price", 'id');	
									$page_content.="<table width='100%' border=0 cellpadding='2' cellspacing='2' class='text'>";
									$page_content.="<tr><td align='center'><strong>Retail</strong></td><td align='center'><strong>Corporate</strong></td></tr>";
									if(count($qtyWiseData)>0) {
										foreach($qtyWiseData as $qty) {
											$page_content.="<tr><td align='center'>".sprintf("%03d",$qty['retail_price'])."</td>";
											//$page_content.=$qty['quantity_max']=='-1'?"-":sprintf("%03d",$qty['quantity_max']);
											$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['corporate_price'])." ".$measurement."</td></tr>";
										}//end foreach
									}//endif
									$page_content.="</table></td>";
									$result[$key] = '';
								}else{								
									$page_content .= ''.sprintf("$%01.2f",$fPrice).'</td>';
									$result[$key] = '';
								}//endif quantity wise
							}//endif key=retialprice
							
							
							if($key=='Corporate_Price'){
								list($pType, $fPrice) = explode('|', $result[$key]);
								if($pType=='Quantity Wise'){
									$qtyWiseData = $this->Select(TABLE_QUANTITY_WISE, "product_id='".$result['id']."' AND customer_type='Corporate' ","quantity_min,quantity_price", 'quantity_id');
									$page_content.="<table width='100%' border=0 cellpadding='2' cellspacing='2' class='text'>";
									$page_content.="<tr><td align='center'><strong>Qty Min</strong></td><td align='center'><strong>Price</strong></td></tr>";
									if(count($qtyWiseData)>0) {
										foreach($qtyWiseData as $qty) {
											$page_content.="<tr><td align='center'>".sprintf("%03d",$qty['quantity_min'])."</td>";
											//$page_content.=$qty['quantity_max']=='-1'?"-":sprintf("%03d",$qty['quantity_max']);
											$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['quantity_price'])." ".$measurement."</td></tr>";
										}//end foreach
									}//endif
									$page_content.="</table></td>";
									$result[$key] = '';
								}else{								
									$page_content .= ''.sprintf("$%01.2f",$fPrice).'</td>';
									$result[$key] = '';
								}//endif quantity wise
							}//endif key=corporate
							
							
							
							if($key=='Non_Profit_Price'){
								
								list($pType, $fPrice) = explode('|', $result[$key]);
								if($pType=='Quantity Wise'){
									$qtyWiseData = $this->Select(TABLE_QUANTITY_WISE, "product_id='".$result['id']."' AND customer_type='Non Profit' ","quantity_min,quantity_price", 'quantity_id');
									$page_content.="<table width='100%' border=0 cellpadding='2' cellspacing='2' class='text'>";
									$page_content.="<tr><td align='center'><strong>Qty Min</strong></td><td align='center'><strong>Price</strong></td></tr>";
									if(count($qtyWiseData)>0) {
										foreach($qtyWiseData as $qty) {
											$page_content.="<tr><td align='center'>".sprintf("%03d",$qty['quantity_min'])."</td>";
											//$page_content.=$qty['quantity_max']=='-1'?"-":sprintf("%03d",$qty['quantity_max']);
											$page_content.="<td align='center'>".sprintf("$%01.2f",$qty['quantity_price'])." ".$measurement."</td></tr>";
										}//end foreach
									}//endif
									$page_content.="</table></td>";
									$result[$key] = '';
								}else{								
									$page_content .= sprintf("$%01.2f",$fPrice).'</td>';
									$result[$key] = '';
								}//endif quantity wise
							}//endif key=retialprice
								
						}//endif countlist

						///promotional code
						if($key=='Discount_Rate'){
							$result[$key] = $result[$key].'%';
						}
						if($key=='Status'){
							if($result[$key] == 0){$status = 'Not Used';}
							if($result[$key] == 1){$status = 'Used';}
							if($result[$key]=='processing'){$status = 'Processing';}
							$result[$key] = $status;															
						}//end if status	
						
						
						
						//end promotional code
							$page_content.= stripslashes($result[$key]). "</td>";
				
						if($is_page_header=="") {
							if($sizeArray[$size_count]!="")
								$width='width="'.$sizeArray[$size_count].'"';
							else
								$width="";
							$page_header.='
								<td  style="padding-left:7px;" '.$width.' ';
							if($countList[$key]['table']!="" || $sortArray[$key]=='N') {
								$page_header.='>'.str_replace("_"," ",$key) . '</td>';
							}
							else {
								
								if($_GET['sort']==$key) 
								{
									$page_header.=' class="ban3" ';
									$bandlink='white_bold_text';
									if($_GET['order']=='a') $order_q="&order=d"; else $order_q='&order=a';
								} 
								else 
								{
									$bandlink='white_bold_text';
									$order_q='&order=a';
								}
								
							//echo $action_query;
							$page_header.=' ><a href="'.$this->MakeUrl($this->mCurrentUrl,"sort=".$key.$order_q.$sort_query).'" class="'.$bandlink.'">'.str_replace("_"," ",$key).'';
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='<img src="'.SITE_URL.'images/Up.gif" title="Move Up" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='<img src="'.SITE_URL.'images/Down.gif" title="Move Down" border=0 align="absmiddle" />';
									
								$page_header.='</a></td>
									 ';
							}
							
							$size_count++;
						}
						$field_count++;
					}
				}
				$action_count=0;
				//$action_query="sort=".$_GET['sort']."&order=".$_GET['order']."&offset=".$_GET['offset']."&cat_id=".$_GET['faqCategory']."&";
				foreach($listAction as $aKey => $aValue) { 
				
					if($aValue['type']=='confirm' && $aValue['custom']=='') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".$aKey." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".ucfirst($aKey)."' title='".ucfirst($aKey)."'></a>
										</td>";	
					} elseif($aValue['type']=='confirm' && $aValue['custom']=='yes') {  
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('".$aValue['message']."'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".ucfirst($aKey)."' title='".ucfirst($aKey)."'></a>
										</td>";	
					
					} elseif($aValue['type']=='condition') {  
						if( $result[$aValue['conditionkey']]==$aValue['conditionvalue']) {
							$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".ucfirst($aKey)." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".ucfirst($aKey)."' title='".ucfirst($aKey)."'></a>
										</td>";
						}	
					} elseif($aValue['type']=='condition') { 
						if( $result[$aValue['conditionkey']]==$aValue['conditionvalue']) {
							$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".ucfirst($aKey)." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".ucfirst($aKey)."' title='".ucfirst($aKey)."'></a>
										</td>";
						}
					} elseif($aValue['type']=='category') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl("admin/manage_subcategories"."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";															
					} elseif($aValue['type']=='changeform') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl("admin/manage_changeforms"."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";
					} elseif($aValue['type']=='products') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl("admin/manage_products"."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";
					} else {  
							
							//echo $count;	
						if(($count == 1 && $aKey=='Up') || ($_SESSION['tot_rows'] == $count && $aKey=='Down' ) ){
							$page_content.= "<td width='3%' align='center' valign='middle'>	--</td>";	
						}else{
							if($aKey=='Up'){$title = 'Move Up';}							
							elseif($aKey=='Down'){$title = 'Move Down';}else{$title = $aKey;}
								
							$page_content.= "				
										<td  align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif' border='0' alt='".ucfirst($aKey)."' title='".ucfirst($title)."'></a>
										</td>";
						
						}
					}
					
					$keyArr[]=$aKey; 
					$action_count++;
					
				}
				
				if($help_icon=="")
					$help_icon="<tr><td colspan='".($action_count+$field_count+1)."' align='left'>".$this->GetHelpIcons($keyArr)."</td></tr>";
				if($is_page_header=="") {
					if($action_count>0) {
					$page_header.='
						<td width="10%" colspan="'.$action_count.'" align="center" class="tblheader_text2">Action</td>
                        ';
					}
					$page_header.='
					</tr>';
				}
				$page_content.= "
									</tr>";
			}
		}
		
		
		return $help_icon.$page_header.$page_content;
	}
	
	//-----------------------------------------------------------------------------------------------//
	// Method Send Email / E-mail with Attachments / Attachement / HTML / TEXT ------------- 
	//-----------------------------------------------------------------------------------------------//
	function SendMail($subject,$message,$arrValues,$type="html",$bcc=array(),$cc=array(),$attachment=array()) {
		
		foreach($arrValues as $key=>$value) {
			$message=str_replace("[".$key."]",$value,$message);
			$message=str_replace("[".strtoupper($key)."]",$value,$message);
			$message=str_replace("[".strtolower($key)."]",$value,$message);
		}
		$constant_arr=get_defined_constants(true);
		foreach($constant_arr['user'] as $key=>$value) {
			$message=str_replace("[".$key."]",$value,$message);
			$message=str_replace("[".strtoupper($key)."]",$value,$message);
			$message=str_replace("[".strtolower($key)."]",$value,$message);
		}
		$bcc=implode(', ',$bcc);
		$cc=implode(', ',$cc);
			$headers = 'From: '.SITE_NAME.' <' . $arrValues['from'] . '>' . "\r\n";	
		if($bcc!="") $headers .= 'Bcc: ' . $bcc . " \r\n";
		if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
		
		if(count($attachment)>0) {
			$mime_boundary = "B_".md5(time())."";
			$headers .="Content-type: multipart/mixed;
	".'boundary="'.$mime_boundary.'"' . "\r\n";
			$headers .="--".$mime_boundary."\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			//echo $message; exit;
			$headers .= $message;
			foreach($attachment as $key_att=>$att) {
				foreach ($att as $key_att=>$att){
					$fname = substr(strrchr($att, "/"), 1);
					
					$data = file_get_contents($att);
					
					//echo mime_content_type($att);//$content_id = "part$i." . sprintf("%09d", crc32($fname)) . strrchr($this->to_address, "@");
					$i++;
					$headers .= 	"\r\n"."--".$mime_boundary."\r\n"."Content-Type: application/octet; name=\"$key_att\"\r\n" .
									  "Content-Transfer-Encoding: base64\r\n" .
									  "Content-Disposition: attachment;\n" .
									  " filename=\"$key_att\"\r\n" .
									  "\r\n" .
									  chunk_split( base64_encode($data), 68, "\n");
				}//end foreach
			}//end foreach
			
			//echo $headers;
			$headers .='\r\n--'.$mime_boundary.'--';			
			return @mail($arrValues['EMAIL'],$subject,"",$headers);
		} else {
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			return @mail($arrValues['EMAIL'],$subject,$message,$headers);
		}
	}
	
	function SendMail_old($subject,$message,$arrValues,$type="html",$bcc=array(),$cc=array(),$attachment=array()) {
		
		foreach($arrValues as $key=>$value) {
			$message=str_replace("[".$key."]",$value,$message);
			$message=str_replace("[".strtoupper($key)."]",$value,$message);
			$message=str_replace("[".strtolower($key)."]",$value,$message);
		}
		$constant_arr=get_defined_constants(true);
		foreach($constant_arr['user'] as $key=>$value) {
			$message=str_replace("[".$key."]",$value,$message);
			$message=str_replace("[".strtoupper($key)."]",$value,$message);
			$message=str_replace("[".strtolower($key)."]",$value,$message);
		}
		$bcc=implode(', ',$bcc);
		$cc=implode(', ',$cc);
		$headers = 'From: '.SITE_NAME.' <' . $arrValues['from'] . '>' . "\r\n";
		if($bcc!="") $headers .= 'Bcc: ' . $bcc . " \r\n";
		if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
		
		if(count($attachment)>0) {
			$mime_boundary = "B_".md5(time())."";
			$headers .="Content-type: multipart/mixed;
	".'boundary="'.$mime_boundary.'"' . "\r\n";
			$headers .="--".$mime_boundary."\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n\r\n";
			$headers .= $message;
			foreach($attachment as $key_att=>$att) {
				foreach ($att as $key_att=>$att){
					$fname = substr(strrchr($att, "/"), 1);
					
					$data = file_get_contents($att);
					
					//echo mime_content_type($att);//$content_id = "part$i." . sprintf("%09d", crc32($fname)) . strrchr($this->to_address, "@");
					$i++;
					$headers .= 	"\r\n"."--".$mime_boundary."\r\n"."Content-Type: application/octet; name=\"$key_att\"\r\n" .
									  "Content-Transfer-Encoding: base64\r\n" .
									  "Content-Disposition: attachment;\n" .
									  " filename=\"$key_att\"\r\n" .
									  "\r\n" .
									  chunk_split( base64_encode($data), 68, "\n");
				}//end foreach
			}//end foreach
			
			//echo $headers;
			$headers .='\r\n--'.$mime_boundary.'--';			
			return @mail($arrValues['EMAIL'],$subject,"",$headers);
		} else {
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
			return @mail($arrValues['EMAIL'],$subject,$message,$headers);
		}
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Help Icons Used in ManageList ------------- 
	//-----------------------------------------------------------------------------------------------//
	function GetHelpIcons1($keyArr)
	{
		$content.='<table border="0" align="right" cellpadding="1" cellspacing="4" class="text">
           <tr>';
		foreach($keyArr as $key)
		{
			if($key=='Up'){ $title = 'Move Up';}
			elseif($key=='Down'){ $title = 'Move Down';}
			else{$title = $key;}
			
			
			
			$content.='
            <td align="center" valign="bottom"><img src="'.SITE_URL.'images/'.$key.'.gif" title="'.ucfirst($title).'" border="0" /></td>
            ';
			$content2.='
			<td align="center" valign="top" style="padding-left:5px;">'.ucfirst($title).'</td>
           ';
		}
		$content.='</tr><tr>'.$content2.'</tr></table>';
		$title = '';
		return $content;
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Delete / Unlink File / Files ------------- 
	//-----------------------------------------------------------------------------------------------//
	function MakeFileDelete($path)
	{
		@unlink($path);
		return;
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Delete Products E-commerce / Cart ------------- 
	//-----------------------------------------------------------------------------------------------//
	function DeleteProduct($id)
	{
		$getRes_Featured=$this->Select(TABLE_FEATURED,"product_id IN(".$id.")", "product_id");
		if(count($getRes_Featured)>0) {
			foreach($getRes_Featured as $result_f) {
				$featured_arr[]=$result_f['product_id'];
			}
			$featured_ids=implode(", ",$featured_arr);
		}
		if($featured_ids!="")
			$condition = " AND product_id NOT IN(".$featured_ids.")";
		// Delete From Images table & unlink images
		$img_arr=$this->Select(TABLE_PRODUCT_IMAGES,"product_id IN(".$id.") ".$condition,"image_id,image");
		if(count($img_arr)>0) {
			foreach($img_arr as $img) {
				@unlink(ROOT.DIR_PRODUCT_THUMBNAIL.$img['image']);
				@unlink(ROOT.DIR_PRODUCT_SMALL.$img['image']);
				@unlink(ROOT.DIR_PRODUCT.$img['image']);
				$this->Delete(TABLE_PRODUCT_IMAGES,"image_id='".$img['image_id']."'");
			}
		}
		// Delete from Related Products table
		$this->Delete(TABLE_RELATED_PRODUCTS,"product_id IN(".$id.")  ".$condition);
		// Delete from Products Sizes table
		$this->Delete(TABLE_PRODUCT_SIZES,"product_id IN(".$id.")  ".$condition);
		// Delete from Product table
		
		$pro_arr=$this->Select(TABLE_PRODUCTS,"product_id IN(".$id.") ".$condition,"cat_id, position");
		if(count($pro_arr)>0) {
			foreach($pro_arr as $data_arr) {
				$this->Update(TABLE_PRODUCTS,array('position'=>' position-1'),"position > '".$data_arr['position']."' and cat_id = '".$data_arr['cat_id']."'");
				$this->Delete(TABLE_PRODUCTS,"product_id IN(".$id.")  ".$condition);
			}
		}
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Delete Products by Category Id E-commerce / Cart ------------- 
	//-----------------------------------------------------------------------------------------------//
	function DeleteProductByCategory($catId)
	{
		// Delete From Images table & unlink images
		$getRes = $this->Select(TABLE_PRODUCTS,"cat_id='".$catId."'","*");
		if(count($getRes)>0) {
			foreach($getRes as $result) {
				$id = $result['product_id'];
				$img_arr=$this->Select(TABLE_PRODUCT_IMAGES,"product_id='".$id."'","image_id,image");
				if(count($img_arr)>0) {
					foreach($img_arr as $img) {
						@unlink(ROOT.DIR_PRODUCT_THUMBNAIL.$img['image']);
						@unlink(ROOT.DIR_PRODUCT_SMALL.$img['image']);
						@unlink(ROOT.DIR_PRODUCT.$img['image']);
						$this->Delete(TABLE_PRODUCT_IMAGES,"image_id='".$img['image_id']."'");
					}
				}
				// Delete from Related Products table
				$this->Delete(TABLE_RELATED_PRODUCTS,"product_id IN(".$id.")");
				// Delete from Products Sizes table
				$this->Delete(TABLE_PRODUCT_SIZES,"product_id IN(".$id.")");
				// Delete from Product table
				$this->Delete(TABLE_PRODUCTS,"product_id IN(".$id.")");
			}
		}
	}
	//-----------------------------------------------------------------------------------------------//
	// Method Delete Photo Gallery Images ------------- 
	//-----------------------------------------------------------------------------------------------//
	function DeletePhotoGallery($id)
	{
		$image_arr=$this->Select(TABLE_PHOTOGALLERY,"image_id='".$id."'");
		if(count($image_arr)>0) {
			foreach($image_arr as $img) {
				@unlink(DIR_PHOTO_GALLERY.$img['gallery_image']);
				@unlink(DIR_PHOTO_GALLERY_THUMBNAIL.$img['gallery_image']);
				@unlink(DIR_PHOTO_GALLERY_THUMBNAIL_SMALL.$img['gallery_image']);
			}
		}
		$this->Delete(TABLE_PHOTOGALLERY,"image_id='".$id."'");
	}
	
	// $arg choices  are :   1)0        : Main Category Dropsown
	//                       2)Except 0 : Sub Category Dropdown 
	function GetCategoryDropDown($categorySelected, $arg) { 
		global $db;
		$get_result=$this->Select(TABLE_CATEGORIES,"is_active='1'","category_name, category_id");
		//print_r($get_result);
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$category_data .= '<option value="'.$this->MakeUrl($this->mModuleUrl.'/index/',"mcatId=".$result['category_id']).'"';
				if($categorySelected == $result['category_id'] ) $category_data .= ' selected="selected"';
				$category_data .= '>'.$result['category_name'].'</option>';
			}
		}
		return $category_data;
	}
	
	
	
	function GetSimpleCategoryDropDown($categorySelected, $arg) { 
		global $db;
		$get_result=$this->Select(TABLE_CATEGORY,"parent_id=$arg  and is_active='1'");
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$category_data .= '<option value="'.$result['cat_id'].'"';
				if($categorySelected == $result['cat_id'] ) $category_data .= ' selected="selected"';
				$category_data .= '>'.$result['cat_name'].'</option>';
			}
		}
		return $category_data;
	}
	
	
	
	
	function Ajax_SubCategory($catId) {
		$get_result=$this->Select(TABLE_CATEGORY,"parent_id='".$catId."' AND parent_id!='0' AND is_active='1' ","cat_id,cat_name","cat_name");
		//$data="";
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				//if()
				$data.=$result['cat_id']."@@@@".ucfirst($result['cat_name'])."####";
			}
		}
		return $data;
	}
	function Ajax_SubCategoryPattern($catId) {
		$get_result=$this->Select(TABLE_CATEGORY,"parent_id='".$catId."' AND parent_id!='0' AND is_active='1' AND is_layout='1'","cat_id,cat_name","cat_name");
		//$data="";
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				//if()
				$data.=$result['cat_id']."@@@@".ucfirst($result['cat_name'])."####";
			}
		}
		return $data;
	}
	
	function Ajax_Username($userName) {
		$get_result=$this->Select(TABLE_CUSTOMERS,"email='".$userName."'","email");
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data="0####".$userName;
				$number_range_s=00;
				$number_range_e=99;
				$sep_arr=array('','_','.');
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
							$n_username=$userName.'.vfi';
						} else {
							$n_username='vfi.'.$userName;
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
	
	function CheckUsername($new_username) {
		$get_result=$this->Select(TABLE_CUSTOMERS,"email='".$new_username."'","email");
		if(count($get_result)>0) {
			return false;
		} else {
			return true;
		}
	}
	
	function Ajax_Email_old($userName) {
		$get_result=$this->Select(TABLE_CUSTOMERS,"email='".$userName."'","email");
		if(count($get_result)>0) {
			//foreach($get_result as $result) { 
				//if()
				$data="0";
			//}
		}
		else 
			$data="1";
		return $data;

	}
	function Ajax_Email_Profile($userName) {
		$get_result=$this->Select(TABLE_CUSTOMERS,"e_mail='".$userName."' and customer_id !='".$_SESSION['sess_user_id']."'","e_mail");
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data="0";
			}
		}
		else 
			$data="1";
		return $data;
	}
	
	function UploadProductImage($source) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			$new_file=uniqid('pro_');
			$destination = DIR_PRODUCT.$new_file.'.'.$ext;
			if(move_uploaded_file($source,$destination)) {
				if($width>600 || $height>450)
					$this->CreateThumb($new_file,$ext,DIR_PRODUCT,600,450,DIR_PRODUCT);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,150,150,DIR_PRODUCT_THUMBNAIL);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,75,75,DIR_PRODUCT_SMALL);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
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
			$data = '<div class="message_div" align="left"><div class="'.$class.'">
						'.$message.'
					</div></div>';
		}
		
		return $data;
	} 
	

	function getTitle($table, $id_field, $id, $show_field){
		$get_result=$this->Select($table, $id_field."='".$id."'","","");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$title = stripslashes($result[$show_field]);
			}
		}
		
		return $title;	
	}
	
	function GetStateBasedDropDown($stateSelected,$countrySelected,$type) { 
	
		if($type == "user"){
			if($countrySelected == "USA" || $countrySelected == ""){
				$cid = "1";
			} else {
				$cid = "";
			}
		} 
		$get_result=$this->Select(TABLE_STATE,"country_id='".$cid."'");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$state_data .= '<option id="'.$result['state_prefix'].'" value="'.$result['state_prefix'].'"';
				if($stateSelected == $result['state_prefix']) $state_data .= ' selected="selected"';
				$state_data .= '>'.$result['state_name'].'</option>';
			}
		}
		return $state_data;
	}
	
	
function DeleteSubTableFields($catid)
	{
		//get the SubCatid from Sub_Category Table using main Category id"
		$get_SubCatresult=$this->Select(TABLE_SUBCATEGORIES,"category_id='".$catid."'","","subcategory_id");
		if(count($get_SubCatresult)>0) 
		{
			foreach($get_SubCatresult as $subCatid) 
			{
				//get the Collection id from Collection Table using sub-category id
				$get_Collectionresult=$this->Select(TABLE_COLLECTION,"subcategory_id='".$subCatid['subcategory_id']."'","","collection_id");
				foreach($get_Collectionresult as $collectionid)
				{
					//get the Product id from Product table using collecion id and delete Product,Collection and Sub-Category id
						//Product start
							$get_Productresult=$this->Select(TABLE_PRODUCTS,"subcategory_id='".$subCatid['subcategory_id']."'","","product_id");
							foreach($get_Productresult as $arr)
							{
								$product_image=$arr['item_image'];
								$product_other_image=$arr['item_other_images'];
							}
							@unlink(DIR_PRODUCT.$product_image);
							@unlink(DIR_PRODUCT_THUMBNAIL.$product_image);
							@unlink(DIR_PRODUCT_SMALL.$product_image);
							
							$otherImagesArr = explode('@@@', $product_other_image);
							foreach($otherImagesArr as $img){
								@unlink(DIR_PRODUCT.$img);
								@unlink(DIR_PRODUCT_THUMBNAIL.$img);
								@unlink(DIR_PRODUCT_SMALL.$img);
								
							}
							$this->Delete(TABLE_PRODUCTS,"subcategory_id='".$subCatid['subcategory_id']."'");
						//end
						//Collection start
							$this->deleteRelatedFiles(TABLE_COLLECTION, 'collection_id', $collectionid['collection_id'], 'collection_image', DIR_COLLECTION, DIR_COLLECTION_THUMBNAIL, DIR_COLLECTION_SMALL);
							$this->Delete(TABLE_COLLECTION,"subcategory_id='".$subCatid['subcategory_id']."'");
						//end
						
				}
				//Sub-Category start
					$this->Delete(TABLE_SUBCATEGORIES,"category_id='".$catid."'");
				//end
			}
		}
	}//end function DeleteSubTableFields
	
	function DeleteCollectionFields($subcatid)
	{
		//get the SubCatid from Sub_Category Table using main Category id"
		$get_Collectionresult=$this->Select(TABLE_COLLECTION,"subcategory_id='".$subcatid."'","","collection_id");
		if(count($get_Collectionresult)>0){
			
			foreach($get_Collectionresult as $collectionid) 
			{
				$this->deleteRelatedFiles(TABLE_COLLECTION, 'collection_id', $collectionid['collection_id'], 'collection_image', DIR_COLLECTION, DIR_COLLECTION_THUMBNAIL, DIR_COLLECTION_SMALL);
			}
			$this->Delete(TABLE_COLLECTION,"subcategory_id='".$subcatid."'");
		}//end if
		//Product start
		$get_Productresult=$this->Select(TABLE_PRODUCTS,"subcategory_id='".$subcatid."'","","product_id");

		if(count($get_Productresult)>0){
			foreach($get_Productresult as $arr){
				$product_image=$arr['item_image'];
				$product_other_image=$arr['item_other_images'];
				
				@unlink(DIR_PRODUCT.$product_image);
				@unlink(DIR_PRODUCT_THUMBNAIL.$product_image);
				@unlink(DIR_PRODUCT_SMALL.$product_image);
				
				$otherImagesArr = explode('@@@', $product_other_image);
				foreach($otherImagesArr as $img){
					@unlink(DIR_PRODUCT.$img);
					@unlink(DIR_PRODUCT_THUMBNAIL.$img);
					@unlink(DIR_PRODUCT_SMALL.$img);							
				}//end foreach2
				$this->Delete(TABLE_PRODUCTS,"subcategory_id='".$subcatid."'");
			}//end foreach1
		
		}//end if
		
	}///end of function DeleteCollectionFields	
	function ParseCSVLine($csv) {
		$csv_len = strlen($csv);
		$field_arr = array();
		$field_start_flag = true;
		$field_end_flag = false;
		$field_quote_flag=false;
		$field_end_final_flag = true;
		$field_offset = 0;
		for($i=0; $i<$csv_len; $i++) {
			if($csv[0]==",") { $field_arr[0]=""; }
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
				$field_arr[$field_offset]=" ";
				$field_start_flag = true;
			} else {
				$field_arr[$field_offset].=$csv[$i];
			}
		}
		return $field_arr;
	}



function csvProductValidation_old($subcategory, $collection, $item_name, $item_number, $retail_price_type, $unit_weight, $unit_of_measurement){
		$retail_price = trim($retail_price);
		global $errorlog;
		if(trim($subcategory)=='') return false;			
		if(trim($item_name)=='') return false;
		if(trim($item_number)=='') return false;
		if(trim($retail_price_type)=='') return false;
		if($unit_weight=='') return false;
		if($unit_of_measurement=='') return false;
		
		if(strtolower($retail_price_type)!='fixed' && strtolower($retail_price_type)!='quantity wise'){
		  return false;
		}
		
		/*if(strtolower($retail_price_type)=='fixed'){
			if($retail_price == 0 || $retail_price < 0 || $retail_price == '')
				return false;
		}*/


		if($collection!=""){///if collection name is given then fetch this collection id and subcategoryid
			$coll_arr=$this->Select(TABLE_COLLECTION,"TRIM(collection_name)='".addslashes(trim($collection))."'","collection_id, subcategory_id");
			if($coll_arr[0]['collection_id']!="") {
				$collection_id = $coll_arr[0]['collection_id'];
				$subcategory_id= $coll_arr[0]['subcategory_id'];				
			}else{
				return false;//if collection name do not match in database return false
			}
		}										
		
		$subcat_arr=$this->Select(TABLE_SUBCATEGORIES,"TRIM(subcategory_name)='".addslashes(trim($subcategory))."'","subcategory_id");
			if($subcat_arr[0]['subcategory_id']!="") {
					$subcategory_id_new = $subcat_arr[0]['subcategory_id'];
			}else{
					//$errorlog = 'No subcategory exit with this name';
					return false;//if subcategoryid do not match with database value return false
			}
		
		

		///if collection name is given then check weither this collection belongs to this same subcategory or not, if collection is not given then return true
		if($collection!=''){
			if(trim($subcategory_id_new)!=trim($subcategory_id)){
				return false;///collection do not belongs to this subcategory
			}
		}	
		
		///do not insert duplicate values
		$pro_arr=$this->Select(TABLE_PRODUCTS,"TRIM(item_number)='".addslashes(trim($item_number))."' AND subcategory_id='".$subcategory_id."'","product_id");	
		if($pro_arr[0]['product_id']!=''){		
			return false;
		}
		
		$dataArr = array($subcategory_id, $item_number, $collection_id);
		return $dataArr;
}
	
	
///////////////////////////////////Project related function -- surya --/////////////////////////////////////////////////////////	
function CopyImageSourceToDestination($source) {
	if(is_file($source)) {
		list($width,$height,$ext)=getimagesize($source);
		
		if($ext==1)
			$ext="gif";
		elseif($ext==2)
			$ext="jpg";
		elseif($ext==3)
			$ext="png";
		
		$new_file=uniqid('prd_');
		$destination = DIR_PRODUCT.$new_file.'.'.$ext;
		
		if(copy($source,$destination)) {
			if($width>600 || $height>450)
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,600,450,DIR_PRODUCT);
			
			$this->CreateFixThumbForProduct($new_file,$ext,DIR_PRODUCT,PROD_THUMB_WIDTH,PROD_THUMB_HEIGHT,DIR_PRODUCT_THUMBNAIL);				
			
			$this->CreateThumb($new_file,$ext,DIR_PRODUCT,PROD_THUMB_SMALL_WIDTH,PROD_THUMB_SMALL_HEIGHT,DIR_PRODUCT_SMALL);
			return $new_file.'.'.$ext;
		}
		else
			return false;
	} else {
		return false;
	}
}

function Ajax_populateEmailDD($type){
if($type!=''){
	$get_result=$this->Select(TABLE_CUSTOMERS,"customer_type='".$type."' AND is_active='1' ","email","email");
			$data = '';
			if(count($get_result)>0) {
				foreach($get_result as $result) { 
					$data.=$result['email']."@@@@".($result['email'])."####";
				}
		}
	}else{
		$data = '';
	}
		return $data;
		
}

function Ajax_populateForm($id){
//populating form with data
				$qty_data_aj = '';
				$qty_data_C  = '';
				$productData = $this->Select(TABLE_PRODUCTS, "product_id='".$id."'","*");
				if(count($productData)>0){
					foreach($productData as $arr){
						$row=$arr;
					}
				}

			//////////Retail Customers Quantity wise price starts here////////////////////////////////////////////////
			if($row['retail_price_type']=='Quantity Wise'){
				
				$qtyWiseData = $this->Select(TABLE_QUANTITY_WISE, "product_id='".$id."' AND customer_type='Retail' ","*", 'quantity_id');
				$i=0;
				$qty_data_aj .= "changeStateRetail('tr_qtywise_retail');";
				foreach($qtyWiseData as $retail){					
					ob_start();
				
?>					document.getElementById('txtRetailQty<?=$i?>').value="<?=$retail['quantity_min']?>";
					document.getElementById('txtRetailPrice<?=$i?>').value="<?=$retail['quantity_price']?>";
<?	 
					$qty_data_aj.=ob_get_clean();
					$i++;
				}//end foreach
			}elseif($row['retail_price_type']=='Fixed'){
				$qty_data_aj .= "changeStateRetail('tr_fixed_retail');";
			}
			//echo $qty_data_aj;
			//////////Retail Customers Quantity wise price ends here////////////////////////////////////////////////	

		//////////Corporate Customers Quantity wise price  starts here////////////////////////////////////////////////
			/*if($row['corporate_price_type']=='Quantity Wise'){
				
				$qtyWiseData = $this->Select(TABLE_QUANTITY_WISE, "product_id='".$id."' AND customer_type='Corporate' ","*", 'quantity_id');
				//echo '<pre>';
				//print_r($qtyWiseData);
				$i=0;
				$qty_data_C1 = "changeStateCorporate('tr_qtywise_corporate');";
				foreach($qtyWiseData as $corporate){					
					ob_start();
					
?>					document.getElementById('txtCorporateQty<?=$i?>').value="<?=$corporate['quantity_min']?>";
					document.getElementById('txtCorporatePrice<?=$i?>').value="<?=$corporate['quantity_price']?>";
<?	 
					$qty_data_C1.=ob_get_clean();
					$i++;
				}//end foreach
			}elseif($row['corporate_price_type']=='Fixed'){
				$qty_data_C1 .= "changeStateCorporate('tr_fixed_corporate');";
			}
			

			if($row['corporate_price_type']==''){
				$qty_data_C1 .= 'resetCorporate();';
				
			}		
			echo $qty_data_C1;*/
		
			//////////Retail Customers Quantity wise price ends here////////////////////////////////////////////////
			
		/*		//////////Non Profit Customers Quantity wise price  starts here////////////////////////////////////////////////
			if($row['non_profit_price_type']=='Quantity Wise'){
				
				$qtyWiseData = $this->Select(TABLE_QUANTITY_WISE, "product_id='".$id."' AND customer_type='Non Profit' ","*", 'quantity_id');
				$i=0;
				$qty_data_N = "changeStateNonProfit('tr_qtywise_nonprofit');";
				foreach($qtyWiseData as $nonProfit){					
					ob_start();
					if($i>0){
?>						addMoreNonProfit();
<?					}
?>					document.getElementById('txtNonProfitQty<?=$i?>').value="<?=$nonProfit['quantity_min']?>";
					document.getElementById('txtNonProfitPrice<?=$i?>').value="<?=$nonProfit['quantity_price']?>";
<?	 
					$qty_data_N.=ob_get_clean();
					$i++;
				}//end foreach
			}elseif($row['non_profit_price_type']=='Fixed'){
				$qty_data_N .= "changeStateNonProfit('tr_fixed_nonprofit');";
			} 
			
			if($row['non_profit_price_type']==''){
				$qty_data_N = 'resetNonProfit();';
				
			}	*/		
			//////Non Profit Customers Quantity wise price ends here////////////////////////////////////////////////

}

function simpleFileUpload($fileArr, $prefix, $destination) {
		$filename = $fileArr['name'];
		$path = pathinfo($filename);
		$extention = strtolower($path['extension']);
		//$extention=='jpg' || $extention=='jpeg' || $extention=='gif'||
		if($extention=='pdf' ||  $extention=='doc' || $extention=='xls' || $extention=='ppt'){
			$new_file=uniqid($prefix);
			$destination = $destination.$new_file.'.'.$extention;
			if(move_uploaded_file($fileArr['tmp_name'],$destination)) {
				return $filename.'###'.$new_file.'.'.$extention;
			}else{
				return false;
			}//end if moveupload
		}else{
			return false;
		}//end if extenstion check
}//end function simplefileupload


function simpleFileUploadOnEdit($fileArr, $txtOldImageFileName, $prefix, $destination){

		if(!$txtImageFileName = $this->simpleFileUpload($fileArr, $prefix, $destination)) {
			$txtImageFileName = $_POST[$txtOldImageFileName];
		}else{
			// deleting old images from folders
			@unlink($destination.$_POST[$txtOldImageFileName]);
		}//end if	
		
		return $txtImageFileName;
	
	}//end function

/////////////////////////////function GetDropDown starts here - surya - //////////////////////////////////
	////Redirect////
	function GetDropDown($tbl, $optionValue, $optionDisplayValue, $selectedValue='', $qstring) { 
		$get_result=$this->Select($tbl,"is_active='1'", $optionDisplayValue.','.$optionValue, $optionDisplayValue);
		//print_r($get_result);
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$dropDown .= '<option value="'.$this->MakeUrl($this->mCurrentUrl, $qstring.'='.$result[$optionValue]).'"';				if($selectedValue == $result[$optionValue])$dropDown .= 'selected="selected"';
				$dropDown .= '>'.htmlentities(stripslashes($result[$optionDisplayValue])).'</option>';
			}
		}
		//echo $dropDown;
		
		return $dropDown;
	}//end function GetDropDown
	
	/////////////////////////////function GetSimpleDropDown starts here - surya -//////////////////////////////////
	//1.$tbl=table name
	//2.$optionValue=value
	//3.$optionDisplayValue=display value
	//4.$selectedValue=selectedvalue
	//5.$param= where condition parameters
	function GetSimpleDropDown($tbl, $optionValue, $optionDisplayValue, $selectedValue='', $param='') { 
		if($param!=''){	$condition = $param." ";}else{$condition = "";}
		$get_result=$this->Select($tbl,$condition, $optionDisplayValue.','.$optionValue, $optionDisplayValue);
		//print_r($get_result);
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$dropDown .= '<option value="'.$result[$optionValue].'"';
				if($selectedValue == $result[$optionValue] ) $dropDown .= ' selected="selected"';
				$dropDown .= '>'.htmlentities(stripslashes($result[$optionDisplayValue])).'</option>';
			}
		}
		return $dropDown;
	}//end function GetSimpleDropDown
	//////////////////////////////function GetSimpleDropDown ends here - surya -///////////////////////////////////////////
	
	function Ajax_populateSubCategory($catId){
		if(trim($catId)) {
			$get_result=$this->Select(TABLE_CATEGORY,"parent_id='".$catId."'","cat_id,cat_name","cat_name");
			if(count($get_result)>0) {
				foreach($get_result as $result) { 
					$data.=$result['cat_id']."@@@@".($result['cat_name'])."####";
				}
			}
			return $data;
		}
	}//end function Ajax_populateSubCategory
	
	function Ajax_populateProducts($catId){
		$get_result=$this->Select(TABLE_PRODUCTS,"cat_id='".$catId."'","product_id,item_name","item_name");
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data.=$result['product_id']."@@@@".($result['item_name'])."####";
			}
		}
		return $data;
	}//end function Ajax_populateProducts
	function Ajax_populateCollection($subCategoryId){
			$get_result=$this->Select(TABLE_COLLECTION,"subcategory_id='".$subCategoryId."' AND is_active='1' ","collection_id,collection_name","collection_name");
				if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data.=$result['collection_id']."@@@@".($result['collection_name'])."####";
			}
		}
		return $data;
	}//end function Ajax_populateCollection
	
	//////showSelectedCollectionDetails will show the selcted collection details on collection page at front end
	////// Here cat_id is collection id
	function Ajax_showSelectedCollectionDetails($cat_id){
			$get_result=$this->Select(TABLE_CATEGORY,"cat_id='".$cat_id."' AND is_active='1' ","cat_id,cat_name,cat_image,cat_id","cat_name");
			if(count($get_result)>0) {
				foreach($get_result as $result) { 
				//$productsincollection = $this->getProductCountInCollection($result['cat_id']);
				$data.='<div style="border:1 px solid #000000;text-align:center"><div><img border="0" src="'.SITE_URL.DIR_COLLECTION_THUMBNAIL.$result['cat_image'].'"></div><div><br><strong>'.$result['cat_name'].'</strong></div><div>';
			}
		}
		return $data;
	}//end function showSelectedCollectionDetails
	
	/****************************************************************************************************
		function Ajax_showSelectedSizeDetails  
		get Item Number, Price, Color of the selected size from combo or for fixed(sigle size) product		
	******************************************************************************************************/
function Ajax_showSelectedSizeDetails($size_id, $is_fixed=''){
	$ProductSizeArr = $this->Select(TABLE_PRODUCT_SIZES,"size_id='".$size_id."'", "item_no, price, size, size_unit, color_id","position");
	if(count($ProductSizeArr)>0) {
		 $str = '<table border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td><span class="cart_bold">Item Number: </span>'.$ProductSizeArr[0]['item_no'].'</td>
					</tr>';
					
		
		$ProductRealPrice = $ProductSizeArr[0]['price'];
		
		if($_SESSION['sess_user_id']){
			$cart=new Cart(session_id(),$this);
			$discountArr = $cart->getStarLevelDiscount($_SESSION['sess_user_id'], $ProductSizeArr[0]['price']);
			if($discountArr['dicountedPrice']>0){
				$yourPrice = ($ProductRealPrice - $discountArr['dicountedPrice']); 
				$ProductRealPrice = '<strike>'.$ProductRealPrice.'</strike>';
				
			}
		 }
		 
		 
		 
		$str .=  '<tr>
					<td><span class="cart_bold">Price: </span>$'.$ProductRealPrice.'</td>
				</tr>';
		
		if($yourPrice){		
			$str .=  '<tr>
						<td><span class="cart_bold">Your Price: </span>$'.sprintf('%01.2f', $yourPrice).'</td>
					</tr>';
		}				
		 
		 if($is_fixed=='y'){ ///// checking if the fixed type product has size or not. if yes then display the size
			 if($ProductSizeArr[0]['size']){
				$str .= '<tr>
							<td><span class="cart_bold">Size: </span>'.$ProductSizeArr[0]['size'].' '.$ProductSizeArr[0]['size_unit'].'</td>
						</tr>';
			 }
		 }

		 $color_img = $this->getColorImage($ProductSizeArr[0]['color_id']); 
		 if($ProductSizeArr[0]['color_id'] && $color_img){  ///// checking if the color is there 
			$str .= '<tr>
						<td><span class="cart_bold">Color: </span>'.$color_img.'</td>
					</tr>';
		 }	
			
			$str .= '<tr>
						<td height="40" valign="bottom">
							<input type="hidden" name="id" value="'.$size_id.'">
							<input type="submit" value="" class="btn_addtocart">
						</td>
					</tr>
				</table>';
		}
	return $str;
}//end function showSelectedSizeDetails

	function getColorImage($color_id){
		$colorImageArr = $this->Select(TABLE_COLORS,"id='".$color_id."'", "*","");
		if(count($colorImageArr)>0)
			return '<img border="0" width="15" height="12" src="'.SITE_URL.DIR_COLOR_SMALL.$colorImageArr[0]['color_image'].'">';
	}/// getColorImage function ends here

	
	/////function to get number of product available in a collection
	function getProductCountInCollection($cat_id){
		$get_result=$this->Select(TABLE_PRODUCTS,"cat_id='".$cat_id."' AND is_active='1' "," COUNT(*) AS pcnt ","");
		if(count($get_result)>0){
			return $get_result[0]['pcnt'];
		}else{
			return 0;
		}
	}
	
	///////////////////// Function Ajax_RemoveImg for removing images in a field as an array -- Surya --/////////////////////
	function Ajax_RemoveImg($str) {
		list($id,$filename) = explode('####', $str);
		$user_data=$this->Select(TABLE_PRODUCTS,"product_id='".$id."'","item_other_images");
		$fileArr = explode('@@@', $user_data[0]['item_other_images']);

		foreach($fileArr as $imgfile){
			if($imgfile!=$filename){
				$newFileName[] = $imgfile;
			}else{
				@unlink(DIR_PRODUCT.$filename);
				@unlink(DIR_PRODUCT_THUMBNAIL.$filename);
				@unlink(DIR_PRODUCT_SMALL.$filename);
			}
		}
	
		if(is_array($newFileName))
			$newFileName = implode('@@@', $newFileName);
			
		 $query="Update ". TABLE_PRODUCTS ." SET item_other_images  = '".$newFileName."' WHERE product_id='".$id."'";
		 $this->ExecuteQuery($query);
		 
		 return $newFileName;
	}////////////////////////// End of function Ajax_RemoveImg ////////////////////////////////////////
	
	
	///////////////////// Function Ajax_simpleFileRemove for removing images in a field as an array -- Surya --/////////////////////
	function Ajax_simpleFileRemove($str) {
		list($id,$filename) = explode('#@#@', $str);
		$user_data=$this->Select(TABLE_MASS_EMAIL,"id='".$id."'","file_attached");
		$fileArr = explode('@@@', $user_data[0]['file_attached']);
		foreach($fileArr as $imgfile){
			if($imgfile!=$filename){
				$newFileName[] = $imgfile;
			}else{
				$realName = explode('###',$imgfile);
				@unlink(DIR_ATTACHMENT.$realName[1]);
			}
		}
	
		
		if(is_array($newFileName))
			$newFileName = implode('@@@', $newFileName);
			
		$query="Update ". TABLE_MASS_EMAIL ." SET file_attached  = '".$newFileName."' WHERE id='".$id."'";
		$this->ExecuteQuery($query);
		 
		 return $newFileName;
	}////////////////////////// End of function Ajax_simpleFileRemove ////////////////////////////////////////
	
	
	/////trim array function - surya - ///// 
	function trimArray($arr){
		foreach($arr as $element){									
			if((trim($element)!='')){
				$trimCsvTorxSizeArr[] = $element;
			}			
		}//end for
		return $trimCsvTorxSizeArr;
	}
	///end trim array function/////
	///////////////////function uploadOnEdit starts here - surya - ///////////////////////////////
	/*	1.$txtImageFileName 	2.$txtOldImageFileName	3.$prefix	4.$mainDir	5.$thumbnailDir
	6.$thumbnailSmallDir	7.$width	8.$height */
	function uploadFileOnEdit($txtImageFileName, $txtOldImageFileName, $prefix, $mainDir, $thumbnailDir, $thumbnailSmallDir,$width, $height){

		if(!$txtImageFileName = $this->UploadShowImage($_FILES[$txtImageFileName]['tmp_name'], $prefix, $mainDir,$thumbnailDir,$thumbnailSmallDir,$width,$height)) {
			$txtImageFileName = $_POST[$txtOldImageFileName];
		}else{
			// deleting old images from folders
			@unlink($mainDir.$_POST[$txtOldImageFileName]);
			
			if($thumbnailDir)
				@unlink($thumbnailDir.$_POST[$txtOldImageFileName]);
			if($thumbnailSmallDir)
				@unlink($thumbnailSmallDir.$_POST[$txtOldImageFileName]);
		}//end if	
		
		return $txtImageFileName;
	
	}//end function
	//////////////////function uploadOnEdit ends here //////////////////////////////////
	
	/////////////////deleteRelatedFiles - surya -///////////////////////////////////////////////
	/*	1.$table 2.$primaryKey	3.$primaryKeyValue	4.$fieldName	5.$dirMain	6.$dirThumb	7.$dirSmall 
	8. $explodeWith - give a exploding value for deleting multiple images in a single field 
	*/
	function deleteRelatedFiles($table, $primaryKey, $primaryKeyValue, $fieldName, $dirMain, $dirThumb='', $dirSmall='', $explodeWith=''){
		$user_data = $this->Select($table,$primaryKey." = '".$primaryKeyValue."' ", $fieldName);
		//print_r($user_data);
		if($explodeWith!=''){
			$imgArr = explode($explodeWith, $user_data[0][$fieldName]);
			foreach($imgArr as $filename){
				@unlink($dirMain.$filename);
				if(is_file($dirThumb.$filename))
					@unlink($dirThumb.$filename);
				if(is_file($dirSmall.$filename))
					@unlink($dirSmall.$filename);	
			}//end foreach
		}else{
			@unlink($dirMain.$user_data[0][$fieldName]);
			if(is_file($dirThumb.$user_data[0][$fieldName]))
				@unlink($dirThumb.$user_data[0][$fieldName]);
			if(is_file($dirSmall.$user_data[0][$fieldName]))
				@unlink($dirSmall.$user_data[0][$fieldName]);	
		}
	}/////////////////deleteRelatedFiles ends here //////////////////////////////////////////
/////////////////////////////////////////////////////Project function ends here///////////////////////////////////////////////////

	function getStaticSizeMeasurementValues(){
		$options = array(
						'None'=>'None',
						'Feet'=>'Feet',
						'Inch'=>'Inch',
						);	
		return $options;		
	}//end function getMeasurementValues
	
	function getStaticProductTypeValues(){
		$options = array(
						'F'=>'Flag',
						'FP'=>'Flag Poles',
						'HA'=>'Hardware & Accessories',
						'CA'=>'Clothing & Apparel'
						);	
		return $options;		
	}
	
	//////////////////////////////////////////////////////function getDynamicDropDown Surya/////////////////////////////////////////////////////
	function getStaticDropDown($arrValues, $selected, $type, $qstring=''){
		if($type=='') echo 'type is not defined'; else $DDType = $type;
		if($DDType=='listing')
			$dropDownValues = $this->getStaticListingDropDown($arrValues, $selected, $qstring);
		if($DDType=='form')
			$dropDownValues = $this->getStaticFormBasedDropDown($arrValues, $selected);
			
		return $dropDownValues;	
	}//end function getStaticDropDown
	//////////////////////////////////////////////////function getDynamicDropDown ends here///////////////////////////////////////////////
	//////////////////////////////////////////////////function getStaticFormBasedDropDown////////////////////////////////////////////////
	function getStaticFormBasedDropDown($staticArr, $selected=''){		
		//echo 'va'.$selected.'se';
		foreach($staticArr as $key => $value){
			$dropDown .= '<option value="'.trim($key).'"';
				if($selected == $key)$dropDown .= ' selected="selected"';
			$dropDown .= '>'.htmlentities(stripslashes($value)).'</option>';
		}
		return $dropDown; 
	}//end function getFormBasedDropDown
	///////////////////////////////////////////function getStaticFormBasedDropDown ends here////////////////////////////////////////////
	/////////////////////////////////////////////function getStaticListingDropDown///////////////////////////////////////////////////////
	function getStaticListingDropDown($staticArr, $selected='',$qstring){		
		foreach($staticArr as $key => $value){
			$dropDown .= '<option value="'.$this->MakeUrl($this->mCurrentUrl, $qstring.'='.trim($key)).'"';															 							 			if($selected == $key)$dropDown .= ' selected="selected"';
				$dropDown .= '>'.htmlentities(stripslashes($value)).'</option>';
		}
		return $dropDown; 
	}//end function getStaticListingDropDown
	////////////////////////////////////function getStaticListingDropDown ends here////////////////////////////////////////////////////

	function GetMsgDetails($id, $table, $primaryKey, $fieldName) {
		
		$page=$this->mPageName;
		if($id!="") {
			
			$getRes = $this->Select($table, $primaryKey.' IN ('.$id.')',$fieldName.' as field');
					
			if(count($getRes)>0) {
				foreach($getRes as $data_arr) {
 					$result.=$sep.ucfirst(stripslashes(htmlentities($data_arr['field'])));
					$sep=", ";
				}
			}
		} else{ 
			$result="";
		}
		return $result;	
	}//end function getmessagetitle
	
	function getUserNotifications_old($RecordTitle, $id, $dbTable, $primaryKey, $field = ''){
		
		$title = $this->GetMsgDetails($id, $dbTable, $primaryKey, $field);			

		if($_GET['act']=='added') {
			$error_message= $RecordTitle." information added successfully.";
		}
		elseif($_GET['act']=='updated') {
			$error_message= $RecordTitle."  \"<strong>".$title."</strong>\" information updated successfully.";
		}
		elseif($_GET['act']=='deactivated') {
			
			$rec_deactive = $this->GetMsgDetails($_GET['deactive_id'], $dbTable, $primaryKey, $field);
			$rec_active = $this->GetMsgDetails($_GET['active_id'], $dbTable, $primaryKey, $field);
			$id_arr=explode(",",$_GET['deactive_id']);
			
			if($rec_active!='' && $rec_deactive=='')
				$arr = $rec_active;
			if($rec_deactive!='' && $rec_active=='')
				$arr = $rec_deactive;
			if($rec_active!='' && $rec_deactive!='')
				$arr = $rec_active.', '.$rec_deactive ;
			
			//if($rec_active!="")
				$error_message= $RecordTitle."  \"<strong>".$arr."</strong>\" de-activated successfully.<br>";
			/*if($rec_deactive!="") {
				if(count($id_arr)>1)
					$error_message.= $RecordTitle."  \"<strong>".$rec_deactive."</strong>\" are already de-active.";
				else
					$error_message.= $RecordTitle."  \"<strong>".$rec_deactive."</strong>\" is already de-active.";
			}*/
		}
		elseif($_GET['act']=='activated') {
			$rec_deactive = $this->GetMsgDetails($_GET['deactive_id'], $dbTable, $primaryKey, $field);
			$rec_active = $this->GetMsgDetails($_GET['active_id'], $dbTable, $primaryKey, $field);

			$id_arr=explode(",", $_GET['active_id']);
			
			if($rec_deactive!='' && $rec_active=='')
				$arr = $rec_deactive;
			if($rec_active!='' && $rec_deactive=='')
				$arr = $rec_active;
			if($rec_active!='' && $rec_deactive!='')	
				$arr = $rec_active.', '.$rec_deactive ;
				
				
			//if($rec_deactive!="")
				$error_message= $RecordTitle."  \"<strong>".$arr."</strong>\" activated successfully.<br>";
			
			/*if($rec_active!="") {
				if(count($id_arr)>1)
					$error_message.= $RecordTitle."  \"<strong>".$rec_active."</strong>\" are already active.";
				else
					$error_message.= $RecordTitle."  \"<strong>".$rec_active."</strong>\" is already active.";
			}*/
		}
		elseif($_GET['act']=='deleted') {
			//$error_message="Conference \"<strong>".$_GET['tabtitle']."</strong>\" removed successfully.";
			$error_message = $RecordTitle."  removed successfully.";
		}elseif($_GET['act']=='moveup') { 
				$error_message=$RecordTitle." \"<strong>".$title."</strong>\" moved up successfully.";
		}elseif($_GET['act']=='movedown') { 
				$error_message= $RecordTitle." \"<strong>".$title."</strong>\" moved down successfully.";
		}elseif($_GET['act']=='error_up') { 
				$err = "err";
				$error_message = $RecordTitle." \"<strong>".$title."</strong>\" cannot be moved up.";
		}elseif($_GET['act']=='error_down') { 
				$err = "err";
				$error_message =  $RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
		}
		
		return $error_message;	
	}//end function notification
	
	function getUserNotifications($RecordTitle1, $id, $dbTable, $primaryKey, $field = ''){
		global $notification_type;
		
		$title = $this->GetMsgDetails($id, $dbTable, $primaryKey, $field);			

		if($_GET['act']=='added') {
			$error_message= $RecordTitle1." information added successfully.";
		}
		elseif($_GET['act']=='updated') {
			$error_message= $RecordTitle1."  \"<strong>".$title."</strong>\" information updated successfully.";
		}
		elseif($_GET['act']=='deactivated') {
			
			$rec_deactive = $this->GetMsgDetails($_GET['deactive_id'], $dbTable, $primaryKey, $field);
			$rec_active = $this->GetMsgDetails($_GET['active_id'], $dbTable, $primaryKey, $field);
			// Featured Items
			$rec_featured = $this->GetMsgDetails($_GET['featured_ids'], $dbTable, $primaryKey, $field);
			// Quick Link collections
			$rec_quicklink = $this->GetMsgDetails($_GET['quicklink_ids'], $dbTable, $primaryKey, $field);
			
			$id_arr=explode(",",$_GET['deactive_id']);
			
			/*if($rec_active!='' && $rec_deactive=='')
				$arr = $rec_active;
			if($rec_deactive!='' && $rec_active=='')
				$arr = $rec_deactive;
			if($rec_active!='' && $rec_deactive!='')
				$arr = $rec_active.', '.$rec_deactive ;*/
			
			if($rec_active!="")
				$error_message= $RecordTitle."  \"<strong>".$rec_active."</strong>\" de-activated successfully.<br>";
			if($rec_deactive!="") {
				if(count($id_arr)>1)
					$error_message.= $RecordTitle."  \"<strong>".$rec_deactive."</strong>\" are already de-active.<br>";
				else
					$error_message.= $RecordTitle."  \"<strong>".$rec_deactive."</strong>\" is already de-active.<br>";
			}
			if($rec_featured!="")
				$error_message .= $RecordTitle."  \"<strong>".$rec_featured."</strong>\" can not be de-activated as this is featured product.<br>";
			if($rec_quicklink!="")
				$error_message .= $RecordTitle."  \"<strong>".$rec_quicklink."</strong>\" can not be de-activated as this is Quick Link.<br>";
					
		}
		elseif($_GET['act']=='activated') {
			$rec_deactive = $this->GetMsgDetails($_GET['deactive_id'], $dbTable, $primaryKey, $field);
			$rec_active = $this->GetMsgDetails($_GET['active_id'], $dbTable, $primaryKey, $field);

			$id_arr=explode(",", $_GET['active_id']);
			
			/*if($rec_deactive!='' && $rec_active=='')
				$arr = $rec_deactive;
			if($rec_active!='' && $rec_deactive=='')
				$arr = $rec_active;
			if($rec_active!='' && $rec_deactive!='')	
				$arr = $rec_active.', '.$rec_deactive ;*/
				
				
			if($rec_deactive!="")
				$error_message= $RecordTitle."  \"<strong>".$rec_deactive."</strong>\" activated successfully.<br>";
			
			if($rec_active!="") {
				if(count($id_arr)>1)
					$error_message.= $RecordTitle."  \"<strong>".$rec_active."</strong>\" are already active.";
				else
					$error_message.= $RecordTitle."  \"<strong>".$rec_active."</strong>\" is already active.";
			}
		}
		elseif($_GET['act']=='deleted') {
			//$error_message="Conference \"<strong>".$_GET['tabtitle']."</strong>\" removed successfully.";
			if($_GET['rcnt']>1)$RecordTitle = 'Records'; else $RecordTitle = 'Record';			
			$error_message = $RecordTitle."  removed successfully.";
		}elseif($_GET['act']=='delete_prod') {
			
			$rec_featured = $this->GetMsgDetails($_GET['featured_id'], $dbTable, $primaryKey, $field);
			//print_r($_GET); die();
			if($_GET['rcnt']!=0) {
				if($_GET['rcnt']>1)	$RecordTitle = 'Records'; else $RecordTitle = 'Record';			
				$error_message = $RecordTitle." removed successfully.<br>";
			}
			if($rec_featured!="")
				$error_message .= $RecordTitle."  \"<strong>".$rec_featured."</strong>\" can not be deleted as this is featured product.";
			
		}elseif($_GET['act']=='delete_collection') {
			$rec_link = $this->GetMsgDetails($_GET['link_id'], $dbTable, $primaryKey, $field);
			//print_r($_GET); die();
			if($_GET['count']!=0) {
				if($_GET['rcnt']>1)	$RecordTitle = 'Records'; else $RecordTitle = 'Record';			
				$error_message = $RecordTitle." removed successfully.<br>";
			}
			if($rec_link!="")
				$error_message .= $RecordTitle."  \"<strong>".$rec_link."</strong>\" can not be deleted as this is Quick link.";
			
		}
		
		
		elseif($_GET['act']=='moveup') { 
			if($title)
				$error_message=$RecordTitle." \"<strong>".$title."</strong>\" moved up successfully.";
		}elseif($_GET['act']=='movedown') { 
			if($title)
				$error_message= $RecordTitle." \"<strong>".$title."</strong>\" moved down successfully.";
		}elseif($_GET['act']=='error_up') { 
			if($title) {
				$notification_type = "err";
				$error_message = $RecordTitle." \"<strong>".$title."</strong>\" cannot be moved up.";
			}
		}elseif($_GET['act']=='error_down') { 
			if($title) {
				$notification_type = "err";
				$error_message =  $RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
			}
		}elseif($_GET['act']=='sent') { 
			if($title)
				$error_message =  $RecordTitle." \"<strong>".$title."</strong>\" sent successfully.";
		}
			
			
		return $error_message;	
	}//end function notification
	
	function activateRecords($id, $dbTable, $primaryKey){
		$id = implode(",",$_POST['record_ids']);
		// Code for fetching records already activated
		$getRes_Active=$this->Select($dbTable,"is_active='1' and ".$primaryKey." IN (".$id.")", $primaryKey);
		if(count($getRes_Active)>0) {
			foreach($getRes_Active as $result_a) {
				$active_arr[]=$result_a[$primaryKey];
			}
		}
		$active_ids=implode(", ",$active_arr);
		$getRes_Deactive=$this->Select($dbTable,"is_active='0' and ".$primaryKey." IN (".$id.")", $primaryKey);
		if(count($getRes_Deactive)>0) {
			foreach($getRes_Deactive as $result_d) {
				$this->Update($dbTable,array('is_active'=>"'1'"), $primaryKey." ='".$result_d[$primaryKey]."'");
				$deactive_arr[]=$result_d[$primaryKey];
			}
		}
		
		$deactive_ids=implode(", ",$deactive_arr);
		
		return array($active_ids,$deactive_ids); 
	}//end function activateRecords

	function deactivateRecords($id, $dbTable, $primaryKey){
		$id = implode(",",$_POST['record_ids']);
		// Code for fetching records already activated
		$getRes_Active=$this->Select($dbTable,"is_active='0' and ".$primaryKey." IN (".$id.")", $primaryKey);
		if(count($getRes_Active)>0) {
			foreach($getRes_Active as $result_a) {
				$active_arr[]=$result_a[$primaryKey];
			}
		}
		$deactive_ids=implode(", ",$active_arr);
		
		$getRes_Deactive=$this->Select($dbTable,"is_active='1' and ".$primaryKey." IN (".$id.")", $primaryKey);
		if(count($getRes_Deactive)>0) {
			foreach($getRes_Deactive as $result_d) {
				$this->Update($dbTable,array('is_active'=>"'0'"), $primaryKey."='".$result_d[$primaryKey]."'");
				$deactive_arr[]=$result_d[$primaryKey];
			}
		}
		
		$active_ids=implode(", ",$deactive_arr);
		
		return array($active_ids,$deactive_ids); 
			
	}//end function deactivateRecords
	//<!----------------Ajax function for removing images ----------------------->
	
	function Ajax_RemoveImage($str) {
			//0-table 1-primarykey 2-primarykeyvalue 3-directory 4-file field
			$arr = explode('|', $str);
			if($arr[0]=='VET'){
				$table = TABLE_VETERANS;
			}
			$primaryKey = $arr[1];
			$primaryKeyValue = $arr[2];
			if($arr[3]=='VET'){
				$directory = DIR_VETERANS; 
				$directoryThumb = DIR_VETERANS_THUMBNAIL;
				$directorySmall = DIR_VETERANS_SMALL;
				$type = 'vetimg';
				$extra = ", image_title=''";				
			}
			
			$fileField = $arr[4];
			$update_field = $fileField."= '' ".$extra;		
			
			$user_data=$this->Select($table ,$primaryKey. " = '".$primaryKeyValue."'", $fileField );
			$file = $user_data[0][$fileField];
			@unlink($directory.$file);
			if($directoryThumb)
				@unlink($directoryThumb.$file);
			if($directorySmall)
				@unlink($directorySmall.$file);			
			
			$query = "Update ". $table ." SET  ".$update_field." WHERE id='".$primaryKeyValue."'";	
			$this->ExecuteQuery($query);
			
			return $type;		 
		}
		
		function GetMainCategoryLinkDropDown($categorySelected) 
		{
			global $db;
			$get_result=$this->Select(TABLE_CATEGORY,"parent_id=0","","cat_name");						
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$cat_data .= '<option value="'.$this->MakeUrl($this->mCurrentUrl,"cat_id=".$result['cat_id']).'"';
					if($categorySelected == $result['cat_id'] ) 
					$cat_data .= ' selected="selected"';
					$cat_data .= '>'.htmlentities(stripslashes($result['cat_name'])).'</option>';
				}
			}
			return $cat_data;
		}
		
		function GetMainCategoryDropDown($categorySelected) 
		{
			global $db;
			$get_result=$this->Select(TABLE_CATEGORY,"parent_id=0","","cat_name");						
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$cat_data .= '<option value="'.$result['cat_id'].'"';
					if($categorySelected == $result['cat_id'] ) 
					$cat_data .= ' selected="selected"';
					$cat_data .= '>'.htmlentities(stripslashes($result['cat_name'])).'</option>';
				}
			}
			return $cat_data;
		}
		
		function GetSubCategoryPosition($subcatId, $selectedcatId) {
			$getRes = $this->Select(TABLE_CATEGORY,"cat_id='".$subcatId."'","*");
			$parent_id = $getRes[0]['parent_id'];
			$upd_position = $getRes[0]['position'];
	
			if(count($getRes)) {
				if($getRes[0]['parent_id']==$selectedcatId) {
					$position = $getRes[0]['position'];
				} else {
					$maxId = $this->Select(TABLE_CATEGORY,"parent_id='".$selectedcatId."'","position","position desc", 1);
					$position = $maxId[0]['position'] + 1;
					
					// Code to update position of other subcategories
					$getRes = $this->Select(TABLE_CATEGORY,"parent_id ='".$parent_id."' and position > '".$upd_position."'","*","position desc");
					if(count($getRes)>0) {
						foreach($getRes as $result){
							$this->Update(TABLE_CATEGORY,array('position'=>' position-1'),"cat_id ='".$result['cat_id']."'");
						}
					}
					
				}
			}
			return $position;		
		}
		function GetSubcatParentId($catId) {
			$get_result=$this->Select(TABLE_CATEGORY,"cat_id='".$catId."'","parent_id","cat_name");
			if(count($get_result)>0) {
				$result=$get_result[0]['parent_id'];
			} else {
				$result="";
			}
			return $result;
		}
		
		function GetSubcatCategoryDropDown($parentId, $categorySelected) 
		{
			$get_result=$this->Select(TABLE_CATEGORY,"parent_id='".$parentId."'","","cat_name");						
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$cat_data .= '<option value="'.$result['cat_id'].'"';
					if($categorySelected == $result['cat_id'] ) 
					$cat_data .= ' selected="selected"';
					$cat_data .= '>'.htmlentities(stripslashes($result['cat_name'])).'</option>';
				}
			}
			return $cat_data;
		}
		
		function GetCategryLinkDropDown($categorySelected) 
		{ 
			$get_result=$this->Select(TABLE_CATEGORY,"parent_id=0","","cat_name");						
			if(count($get_result)>0) 
			{
				foreach($get_result as $result) 
				{
					$subcat=$this->Select(TABLE_CATEGORY,"parent_id=".$result['cat_id'],"cat_id,cat_name","cat_name");
					
					$cat_data .= '<optgroup label="-'.htmlentities(stripslashes($result['cat_name'])).'">';
					foreach($subcat as $arr)
					{
						$get_cnt=$this->Select(TABLE_CATEGORY,"parent_id=".$arr['cat_id'],"");
						$cat_data .= '<option value="'.$this->MakeUrl($this->mModuleUrl.'/index/',$action_query."cat_id=".$arr['cat_id']).'"';
						if($categorySelected == $arr['cat_id'] ) $cat_data .= ' selected="selected"';
							$cat_data .= '>&nbsp;->&nbsp;'.htmlentities(stripslashes($arr['cat_name']))."&nbsp;[".count($get_cnt)."]".'</option>';
					}
					$cat_data .= '</optgroup>';
					
				}
				
			}
			return $cat_data;
		}
		
		function deleteCategory($catId) {
			
			$link_arr = $this->GetQuickLinkIdArray();
			$cat_arr = explode(",",$catId);
			foreach($cat_arr as $key=>$value) {
				if(!in_array($value,$link_arr)) {
					$cat_id = $value;
					// Deleting category
					$getRes = $this->Select(TABLE_CATEGORY,"cat_id='".$cat_id."'","*");
					if(count($getRes)>0) {
						foreach($getRes as $result) {
							$cat_data=$this->Select(TABLE_CATEGORY,"parent_id='".$result['cat_id']."'","*");
							if(count($cat_data)>0) {
								foreach($cat_data as $data_arr) {
									@unlink(ROOT.DIR_COLLECTION.$data_arr['cat_image']);
									@unlink(ROOT.DIR_COLLECTION_THUMBNAIL.$data_arr['cat_image']);
									@unlink(ROOT.DIR_COLLECTION_SMALL.$data_arr['cat_image']);
									// Deleting Products under this collection
									$this->DeleteProductByCategory($data_arr['cat_id']);
									
									$this->Update(TABLE_CATEGORY,array('position'=>' position-1'),"position > '".$data_arr['position']."' and parent_id = '".$data_arr['parent_id']."'");
									$this->deleteCategory($data_arr['cat_id']);
								}
							}
							$this->Update(TABLE_CATEGORY,array('position'=>' position-1'),"position > '".$result['position']."' and parent_id = '".$result['parent_id']."'");
							// Deleting this Category
							@unlink(ROOT.DIR_COLLECTION.$result['cat_image']);
							@unlink(ROOT.DIR_COLLECTION_THUMBNAIL.$result['cat_image']);
							@unlink(ROOT.DIR_COLLECTION_SMALL.$result['cat_image']);
							$this->DeleteProductByCategory($result['cat_id']);
							$this->Delete(TABLE_CATEGORY,"cat_id='".$result['cat_id']."'");
							
						}
					}
				} // end if
			} // end foreach
		}
		
		function GetProductDropDown($productSelected, $catId) {
			$get_result=$this->Select(TABLE_PRODUCTS,"is_active='1' and cat_id='".$catId."'","","item_name");						
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$prod_data .= '<option value="'.$result['product_id'].'"';
					if($productSelected == $result['product_id'] ) 
					$prod_data .= ' selected="selected"';
					$prod_data .= '>'.htmlentities(stripslashes($result['item_name'])).'</option>';
				}
			}
			return $prod_data;
		}
		
		function GetCollectionDropDown($collectionSelected) {
			$get_result=$this->Select(TABLE_CATEGORY,"parent_id=0","","cat_name");						
			if(count($get_result)>0) 
			{
				foreach($get_result as $result) 
				{
//				print_r($result); die;
					$subcat=$this->Select(TABLE_CATEGORY,"parent_id=".$result['cat_id'],"cat_id,cat_name","cat_name");
					
					foreach($subcat as $arr)
					{
						$getCollection=$this->Select(TABLE_CATEGORY,"parent_id=".$arr['cat_id'],"");
						if(count($getCollection)>0) {
							foreach($getCollection as $res) {
								
								$cat_data .= '<option value="'.$res['cat_id'].'"';
								if($collectionSelected == $res['cat_id'] ) $cat_data .= ' selected="selected"';
								$cat_data .= '>'.htmlentities(stripslashes($result['cat_name'])).'&nbsp;->&nbsp;'.htmlentities(stripslashes($arr['cat_name'])).'&nbsp;->&nbsp;'.htmlentities(stripslashes($res['cat_name'])).'</option>';
							}
						}
					}
				}
			}
			return $cat_data;
		}
		function GetPromoTypeDropDown($typeSelected) {
			$type_arr = array('L'=>'Limited','O'=>'Open');
			foreach($type_arr as $key=>$value) { 
				$data .= '<option value="'.$key.'"';
				if($typeSelected==$key) $data .='selected="selected"';
				$data .= '>'.$value.'</option>';
			}
			
			return $data;
		}
		function GetCustomerTypeDropDown($typeSelected) {
			$type_arr = array("'R'"=>"Retail","'C'"=>"Corporate","'NP'"=>"Non-Profit","'G'"=>"Government");
			foreach($type_arr as $key=>$value) { 
				$data .= '<option value="'.$key.'"';
				if(in_array($key, $typeSelected)) $data .='selected="selected"';
				$data .= '>'.$value.'</option>';
			}
			
			return $data;
		}
		function checkPromotionalCode($custId, $promo_code) {
			$getRes = $this->Select(TABLE_PROMOCODE_CUSTOMER,"promo_code='".$promo_code."' AND customer_id ='".$custId."'");
			if(count($getRes)>0) {
				return true;
			} else {
				return false;
			}
		}
		function SendPromoCode($condition, $ins_arr, $mail_arr, $promo_id) {
			$getRes=$this->Select(TABLE_CUSTOMERS,$condition." GROUP BY email","*");				
			$content = "";
			if(count($getRes)>0) {
				foreach($getRes as $result) {
					$mail_arr['CONTACT_NAME']=ucfirst(stripslashes($result['first_name']));
					$mail_arr['EMAIL']=$result['email'];
					$content .= '"'.str_replace('"','""',trim($result['email'])).'"'."\n";
					$ins_arr['customer_id'] = $result['customer_id'];
					$ins_arr['mail_sent'] = "'1'";
					$ins_arr['is_used'] = "'0'";
					global $gPromoCodeMail;
					// Checks if same promo code is alsready sent to this customer or not
					//if(!$this->checkPromotionalCode($result['customer_id'], $promo_code)) {
						//if($this->SendMail("Promotional Code from ".SITE_NAME,$gPromoCodeMail,$mail_arr))
						$getResult = $this->Select(TABLE_PROMOCODE_CUSTOMER, "promo_code_id='".$ins_arr['promo_code_id']."' and customer_id ='".$result['customer_id']."'");
						if(count($getResult)==0)
							$this->Insert(TABLE_PROMOCODE_CUSTOMER,$ins_arr);
						// Update sent status for this promocode (is_sent) in TABLE_PROMOCODE
						$this->Update(TABLE_PROMOCODE,array('is_sent'=>"'1'"),"promo_code_id='".$promo_id."'");
					//}
				}
			}
			return $content;
		}
		function GetCollectionId($productId) {
			$getRes = $this->Select(TABLE_PRODUCTS,"product_id='".$productId."'","cat_id");
			return $getRes[0]['cat_id'];
		}	
		function GetStarLevelDropDown($starlevelSelected) {
			$getRes = $this->Select(TABLE_STAR_LEVEL,"is_active='1'","starlevel_id, starlevel_title, CONCAT(discount,' %') as discount_rate");
			if(count($getRes)>0) {
				$data = "";
				foreach($getRes as $result) {
					$data .= "<option value='".$result['starlevel_id']."'";
					if($starlevelSelected == $result['starlevel_id']) $data .= "selected='selected'";
					$data .= ">".stripslashes(htmlentities($result['starlevel_title']))."</option>";
				}
			}
			return $data;
		}
		function GetTitleDropdown($titleSelected) {
			$title_arr = array('Mr'=>'Mr.','Mrs'=>'Mrs.','Ms'=>'Miss');
			foreach($title_arr as $key=>$value) {
				$data .= "<option value='".$key."'";
				if($titleSelected == $key) $data .= "selected='selected'";
				$data .= ">".$value."</option>";
			}
			return $data;
		}
		
		function GetCustomer_TypeDropDown($typeSelected) {
			$type_arr = array("R"=>"Retail","C"=>"Corporate","NP"=>"Non-Profit","G"=>"Government");
			foreach($type_arr as $key=>$value) { 
				$data .= '<option value="'.$key.'"';
				if($typeSelected==$key) $data .='selected="selected"';
				$data .= '>'.$value.'</option>';
			}
			
			return $data;
		}
		
		function GetRelatedProducts($productId, $selprod_arr) {
			$getRes = $this->Select(TABLE_PRODUCTS,"product_id!='".$productId."' and is_active='1'","product_id, item_name","item_name");
			if(count($getRes)>0) {
				$data = "";
				foreach($getRes as $result) {
					$data .= "<option  value='".$result['product_id']."'";
					if(in_array($result['product_id'],$selprod_arr)) $data .= "selected='selected'";
					$data .= ">".stripslashes(htmlentities($result['item_name']))."</option>";
				}
			}
			return $data;
		}
		
		// Remove image function for edit product page
		function Ajax_RemoveProductImg($str) {
			$arr = explode("|",$str);
			$img_id = $arr[1];
			$p_id = $arr[2];
			$getRes=$this->Select(TABLE_PRODUCT_IMAGES,"image_id='".$img_id."'");
			if(count($getRes)>0) 
			{
				$img_del = $getRes['0']['image'];
				@unlink(ROOT.DIR_PRODUCT.$getRes['0']['image']);
				@unlink(ROOT.DIR_PRODUCT_THUMBNAIL.$getRes['0']['image']);
				@unlink(ROOT.DIR_PRODUCT_SMALL.$getRes['0']['image']);
			}
			
			@$this->Delete(TABLE_PRODUCT_IMAGES,"image_id='".$img_id."'"); 
			$getRes_img=$this->Select(TABLE_PRODUCT_IMAGES,"product_id='".$p_id."'");
			return $arr[0]."|".count($getRes_img)."|".$img_del;
		}
		
		function Ajax_PopulateValues($sizeId) {
			$getRes = $this->Select(TABLE_PRODUCT_SIZES,"size_id='".$sizeId."'","*","position");
			if(count($getRes)) {
				foreach($getRes as $result) {
					return $result['item_no']."####".$result['size']."####".$result['size_unit']."####".$result['weight']."####".$result['weight_unit']."####".$result['price']."####".$result['color_id']."####".$result['is_clearance'];
				}
			} else 
				return "";	
		}
		
		function Ajax_populateProductListing($id) {
			$sizearr=$this->Select(TABLE_PRODUCT_SIZES." as st LEFT JOIN ".TABLE_COLORS." as ct ON st.color_id=ct.id","product_id='".$id."'","*","st.position");
			
			$size_data = '<table width="100%" cellpadding="1" cellspacing="1" border="0" class="text">
							<tr>
								<td align="center" colspan="10" class="ban3">Product Details</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>
								<table width="100%" cellpadding="1" cellspacing="1" border="0" class="border">
								
								<tr class="ban3">
									<td width="5%" align="center"><strong>#</strong></td>
									<td width="12%" align="center"><strong>Product #</strong></td>
									<td width="10%" align="center"><strong>Size</strong></td>
									<td width="12%" align="center"><strong>Size Unit</strong></td>
									<td width="8%" align="center"><strong>Weight</strong></td>
									<td width="13%" align="center"><strong>Weight Unit</strong></td>
									<td width="8%" align="center"><strong>Color</strong></td>
									<td width="8%" align="center"><strong>Price</strong></td>
									<td width="12%" align="center"><strong>Sale Item</strong></td>
									<td align="center"><strong>Action</strong></td>
								</tr>';
			$count = count($sizearr);
			if(count($sizearr)) {
				$i=1;
				foreach($sizearr as $res) {
					if($i%2==0) $bg = "dataclass";
					else $bg = "dataclassalternate";
					
					if($res['is_clearance']=='1')	$clearance = 'Yes'; else $clearance = 'No';
					if($res['color_name'])
						$color_name=stripslashes($res['color_name']);
					else	$color_name="-";
					if($res['color_image'])
						$img = '<img vspace="2" height="14" width="20" src="'.SITE_URL.DIR_COLOR_THUMBNAIL.$res['color_image'].'"/>';
					else $img = "";
					if(trim($res['size']))
						$size = stripslashes($res['size']);
					else 
						$size = '-';
					if(trim($res['size_unit']))
						$size_unit = stripslashes($res['size_unit']);
					else 
						$size_unit = '-';
					$size_data .= '<tr class="'.$bg.'">
									<td height="35" class="detail_col" width="5%">'.$i.'</td>
									<td class="detail_col" width="12%">'.stripslashes($res['item_no']).'</td>
									<td class="detail_col" width="10%">'.$size.'</td>
									<td class="detail_col" width="12%">'.$size_unit.'</td>
									<td class="detail_col" width="10%">'.stripslashes($res['weight']).'</td>
									<td class="detail_col" width="15%">'.stripslashes($res['weight_unit']).'</td>
									<td class="detail_col" width="8%">'.$color_name.'<br />'.$img.'</td>
									<td class="detail_col" width="8%">$'.$res['price'].'</td>
									<td class="detail_col" width="12%">'.$clearance.'</td>
									<td class="detail_col">
										<table width="100%" cellpadding="2" cellspacing="2" border="0">
											<tr>';
					if($i!=1) {
						$size_data .= '<td width="50%" align="center"><a href="javascript:void(0);" onclick="javascript: ajaxLoader(\'ProductListing\');moovUp('.$res['size_id'].','.$id.')"><img src="'.SITE_URL.'images/arrow_up.gif" border="0" title="Up" alt="Up" /></a></td>';
					} else {
						$size_data .= '<td width="50%" align="center">&nbsp;</td>';
					}
					if($i!=$count) {
						$size_data .= '<td width="50%" align="center"><a href="javascript:void(0);" onclick="javascript:ajaxLoader(\'ProductListing\'); moovDown('.$res['size_id'].','.$id.')"><img src="'.SITE_URL.'images/arrow_down.gif" border="0" title="Down" alt="Down" /></a></td>';
					} else {
						$size_data .= '<td width="50%" align="center">&nbsp;</td>';
					}
					$size_data .= '<td width="50%" align="center"><a href="javascript:void(0);" onclick="javascript: showProductDetailBox('.$res['size_id'].');"><img src="'.SITE_URL.'images/edit.gif" border="0" title="Edit" alt="Edit" /></a></td><td width="50%" align="center"><a href="javascript:void(0);" onclick="javascript: if(confirm(\'Are you sure you want to Delete this record?\')) { ajaxLoader(\'ProductListing\'); deleteDetailRecord('.$res['size_id'].','.$id.'); }"><img src="'.SITE_URL.'images/delete.gif" border="0" title="Delete" alt="Delete" /></a></td>
											</tr>
										</table>
									</td>
								</tr>';
					$i++;
				}
			} else {
				$size_data .= '<tr><td colspan="10" align="center" class="redheading">No Records found.</td></tr>';
			}
			$size_data .= '</table></td></tr></table>';
			return $size_data;
		}
		function Ajax_moovProductUp($pId) {
			$id_arr = explode("||||",$pId);
			$sizeId = $id_arr[0];
			$prodId = $id_arr[1];
			
			$getResult=$this->Select(TABLE_PRODUCT_SIZES,"size_id='".$sizeId."'","*");
			foreach($getResult as $result){
				$position = $result['position'] - 1;

				if($position!=0){
					$query="Update ". TABLE_PRODUCT_SIZES ." SET position = position+1 WHERE position='".$position."' and product_id='".$result['product_id']."'";
					$this->ExecuteQuery($query);
					$query="Update ". TABLE_PRODUCT_SIZES ." SET position = position-1 WHERE size_id='".$sizeId."'";
					$this->ExecuteQuery($query);
				}
			}
			$getRes = $this->Select(TABLE_PRODUCT_SIZES,"product_id='".$prodId."'");
			$cnt = count($getRes);
			return "editpage";
		}
		
		function Ajax_moovProductDown($pId) {
			$id_arr = explode("||||",$pId);
			$sizeId = $id_arr[0];
			$prodId = $id_arr[1];
			
			$getResult=$this->Select(TABLE_PRODUCT_SIZES,"size_id='".$sizeId."'","*");
			$maxPos = $this->Select(TABLE_PRODUCT_SIZES,"product_id='".$prodId."'","position","position desc", 1);
				foreach($getResult as $result){
				
				$position = $result['position'] + 1;
				
				if(($position-1)!=$maxPos[0]['position']) {
					$query="Update ". TABLE_PRODUCT_SIZES ." SET position = position-1 WHERE position='".($result['position'] + 1)."' and product_id='".$result['product_id']."'";
					$this->ExecuteQuery($query);
					$query="Update ". TABLE_PRODUCT_SIZES ." SET position = position+1 WHERE size_id='".$sizeId."'";
					$this->ExecuteQuery($query);
				}
			}
			$getRes = $this->Select(TABLE_PRODUCT_SIZES,"product_id='".$prodId."'");
			$cnt = count($getRes);
			return "editpage";
		}
		
		function Ajax_deleteProductDetail($pId) {
			$id_arr = explode("||||",$pId);
			$sizeId=$id_arr[0];
			$id=$id_arr[1];
			
			$getRes=$this->Select(TABLE_PRODUCT_SIZES,"size_id='".$sizeId."'","*");
			if(count($getRes)>0) {
				foreach($getRes as $result) {
					$getdata=$this->Delete(TABLE_PRODUCT_SIZES,"size_id='".$result['size_id']."'");
					$this->Update(TABLE_PRODUCT_SIZES,array('position'=>' position-1'),"position > '".$result['position']."' and product_id = '".$result['product_id']."'");
				}
			}
			
			// Update Product table 
			$getRes = $this->Select(TABLE_PRODUCT_SIZES,"product_id='".$id."'");
			$cnt = count($getRes);
			$getdata=$this->Update(TABLE_PRODUCTS,array('size_count'=>$cnt),"product_id='".$id."'");
			return $cnt;
		}
		
		function Ajax_populateOptionData($size_id) {
			if($size_id) {
				$getRes = $this->Select(TABLE_PRODUCT_SIZES,"size_id='".$size_id."'","color_id","position");
				$selectedcolorId = $getRes[0]['color_id'];
			}
			$options='';
			$imgdata='';
			$options_div='<div  style="height: 130px; width: 175px; overflow:auto;"><div class="dropdownmenu">';

				$color_data=$this->Select(TABLE_COLORS, "","*", 'color_name');
				if(count($color_data)>0){
					foreach($color_data as $color_res) {
						$options .='<option value="'.$color_res['id'].'" ';
						$class_name="dropdownitem";
						if($color_res['id']==$selectedcolorId){
							$options_div.='<script> dropdownSelect("ddItem_'.$product_arr['product_id'].'_'.$color_res['id'].'",\''.$color_res['id'].'\',\''.$product_arr['product_id'].'\')</script>';
							$options .='selected="selected"';
							$class_name="dropdownitem_hover";
						}
						$options_div.='
							<div class="'.$class_name.'" id="ddItem_'.$product_arr['product_id'].'_'.$color_res['id'].'" onClick="javascript: dropdownSelect(this,\''.$color_res['id'].'\',\''.$product_arr['product_id'].'\')" onMouseover="javascript: this.className=\'dropdownitem_hover\';" onMouseout="javascript: this.className=\''.$class_name.'\';"  >
								<div style="float: left; width: 130px; line-height:20px; overflow:hidden;" id="ddItemText_'.$product_arr['product_id'].'_'.$color_res['id'].'">'.ucfirst($color_res['color_name']).'</div>
								<div style="float: right;width: 20px;  overflow:hidden;"><img src="'.SITE_URL.DIR_COLOR_THUMBNAIL.$color_res['color_image'].'" width=20 height=20 /></div>
							</div>';
						$options_div.='<div class="dropdownSep"></div>';
						$options .='>'.ucfirst($color_res['color_name']).'</option>';
						$imgdata .='<tr ';
						if($color_res['id']==$selectedcolorId){
							$imgdata .='';
						}else{
							$imgdata .='style="display:none;"';
						}
						$image_path = SITE_URL.DIR_COLOR_THUMBNAIL.$color_res['color_image'];
						$imgdata .='id="tr_'.$product_arr['product_id'].'_'.$color_res['id'].'">
										<td align="center" colspan="2" class="product_header" valign="top" width="100%"><strong>'.ucfirst($color_res['color_name']).'</strong><br />
										<img src="'.SITE_URL.DIR_COLOR_THUMBNAIL.$color_res['color_image'].'" width="100" height="100" /></td>
									</tr>
									';
					}
				}
			$options_div.='</div></div>';
			return $options_div;
		}
		
		// Function for assigning this promocode to selected customers if promo type is Limited
		function AssignPromoCode($condition, $promo_id) {
			$getRes=$this->Select(TABLE_CUSTOMERS,$condition." GROUP BY email","*");				
			$content = "";
			if(count($getRes)>0) {
				foreach($getRes as $result) {
					$ins_arr['customer_id'] = $result['customer_id'];
					$ins_arr['mail_sent'] = "'1'";
					$ins_arr['is_used'] = "'0'";
					$ins_arr['promo_code_id']=$promo_id;
					global $gPromoCodeMail;
					// Checks if same promo code is alsready sent to this customer or not
					$getResult = $this->Select(TABLE_PROMOCODE_CUSTOMER, "promo_code_id='".$promo_id."' and customer_id ='".$result['customer_id']."'");
					if(count($getResult)==0)
						$this->Insert(TABLE_PROMOCODE_CUSTOMER,$ins_arr);
				}
			}
		}
		
		function ExportCSV($condition) {
			$getRes=$this->Select(TABLE_CUSTOMERS,$condition." GROUP BY email","*");				
			$content = "";
			if(count($getRes)>0) {
				foreach($getRes as $result) {
					$content .= '"'.str_replace('"','""',trim($result['email'])).'"'."\n";
				}
			}
			return $content;
		}
		
	function GetSimpleDropDown_Array($tbl, $optionValue, $optionDisplayValue, $selectedValue='', $param='') { 
		if($param!=''){	$condition = $param." ";}else{$condition = "";}
		$get_result=$this->Select($tbl,$condition, $optionDisplayValue.','.$optionValue, $optionDisplayValue);
		//print_r($get_result);
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$dropDown .= '<option value="'.$result[$optionValue].'"';
				if(in_array($result[$optionValue], $selectedValue)) $dropDown .= ' selected="selected"';
				$dropDown .= '>'.htmlentities(stripslashes($result[$optionDisplayValue])).'</option>';
			}
		}
		return $dropDown;
	}//end function GetSimpleDropDown
	
	function formatToDBDate($dateField) {
		$date_arr = explode("-",$dateField);
		return $date_arr['2']."-".$date_arr['0']."-".$date_arr['1'];
	}
	
	function formatToCalenderDate($dateField) {
		$date_arr = explode("-",$dateField);
		return $date_arr['1']."-".$date_arr['2']."-".$date_arr['0'];
	}
	
	function CategoryMenu(){
		$menu=new Menu(200,20,"#bf9f00","#8f6101",'#bf9f00',"#FFFFFF","#FFFFFF");
		//new Menu("root",164,20,"arial",12,font color "#FFFFFF",fontColorHilite="#FFFFFF",bg="#bf9f00",bgh="#8f6101",halign="left",valign="middle",pad=10,space=0,to=1000,-5,7,true,true,true,0,true,true);
		
		$menu->AddMenu("0","root",165,20,"#bf9f00","#8f6101",'#bf9f00',"#FFFFFF","#FFFFFF",12);
		//-----------------------------------------------------------------------------------------------//
		//-------------------------Sub Menu Category Navigtion-------------------------------------------//
		//-----------------------------------------------------------------------------------------------//
			$count = 1;
			$subhead_catdata=$this->Select(TABLE_CATEGORY." as pt LEFT JOIN ".TABLE_CATEGORY." as ct ON pt.cat_id=ct.parent_id" ,"pt.parent_id=0 and pt.link_to_landing='0' and pt.is_active='1' GROUP BY pt.cat_id","count(ct.cat_id) as cnt_child, pt.cat_id, pt.cat_name","cnt_child desc");
			if(count($subhead_catdata)>0){
				foreach($subhead_catdata as $main_cat) {
					$cat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' and parent_id='".$main_cat['cat_id']."'","","position");
			
					if(count($cat_arr)>0) {
						foreach($cat_arr as $cat) {
							
							$scat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' && parent_id='".$cat['cat_id']."'","","position");
							$cat_count=0;
							if(count($scat_arr)>0) {
								$menu->AddMenu($cat['cat_id']."2","More",165,20,"#bf9f00","#8f6101",'#bf9f00',"#FFFFFF","#FFFFFF",12,-100,-300);
								$menuid=$cat['cat_id']."2";
								 
								foreach($scat_arr as $scat) {
									$categoryTree = $this->GetCategoryTreeForNavigation($cat['cat_id']);
									$href=$categoryTree."/collections/index/";
									if($cat_count>15) {
										 $menu->AddMenuItem("text",$menuid,ucwords(addslashes($scat['cat_name'])),$this->MakeUrl($href,"subcat_id=".$scat['cat_id']));
									}
									$cat_count++;
								}
								if($active_submenu!="") {
									$categoryTree = $this->GetCategoryTreeForNavigation($cat['cat_id']);
									$href_collection = $categoryTree."/collections/index/";
									$menu->AddMenuItem("menu",$active_menu,$active_submenu,$this->MakeUrl($href_collection,"subcat_id=".$scat['cat_id']));
									$active_submenu="";
									$active_menu="";
								}
							}
						}
					}
					if(count($cat_arr)>0) {
						foreach($cat_arr as $cat) {
							
							$scat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' && parent_id='".$cat['cat_id']."'","","position");
							$cat_count=0;
							if(count($scat_arr)>0) {
								$menu->AddMenu($cat['cat_id'],ucwords(addslashes($cat['cat_name'])),165,20,"#bf9f00","#8f6101",'#bf9f00',"#FFFFFF","#FFFFFF",12,-2,-300);
								$menuid=$cat['cat_id'];
								$submenu_added=0;
								foreach($scat_arr as $scat) {
									$categoryTree = $this->GetCategoryTreeForNavigation($scat['cat_id']);
									$href = $categoryTree."/products/index/";
									if($cat_count>15) {
										if($submenu_added==0) {
											$categoryTree = $this->GetCategoryTreeForNavigation($cat['cat_id']);
											$href_collection = $categoryTree."/collections/index/";
											$menu->AddMenuItem("menu",$menuid,$menuid."2",$this->MakeUrl($href_collection,"subcat_id=".$scat['cat_id']));	
											$submenu_added=1;
										}
									} else {
										$menu->AddMenuItem("text",$menuid,ucwords(addslashes($scat['cat_name'])),$this->MakeUrl($href,"collection_id=".$scat['cat_id']));
									}
									$cat_count++;
								}
								 
							}
						}
					}
					
					if(count($cat_arr)>0) {
						$menu->AddMenu("header".$main_cat['cat_id'],"root",165,20,"#bf9f00","#8f6101",'#bf9f00',"#FFFFFF","#FFFFFF",12);
						foreach($cat_arr as $cat) {
							$href="javascript: void(0);";
							$scat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' && parent_id='".$cat['cat_id']."'");
							if(count($scat_arr)>0) {
								$categoryTree = $this->GetCategoryTreeForNavigation($cat['cat_id']);
								$href_collection = $categoryTree."/collections/index/";
								$menu->AddMenuItem("menu","header".$main_cat['cat_id'],$cat['cat_id'],$this->MakeUrl($href_collection,"subcat_id=".$cat['cat_id']));
							}else {
								$categoryTree = $this->GetCategoryTreeForNavigation($cat['cat_id']);
								$href_subcat = $categoryTree."/collections/index/";
								$menu->AddMenuItem("text","header".$main_cat['cat_id'],ucwords(addslashes($cat['cat_name'])),$this->MakeUrl($href_subcat,"subcat_id=".$cat['cat_id']));
							}
						}
					}
					
					
					$menu_x["header".$main_cat['cat_id']] =  0;
					$menu_y["header".$main_cat['cat_id']] =  28;
					$menu_obj["header".$main_cat['cat_id']] =  'link_subhead_'.$main_cat['cat_id'];
					/*$uppend1 = $uppend1 + array('subhead_'.$mcat_arr['category_id']=>160);
					$uppend2 = $uppend2 + array('subhead_'.$mcat_arr['category_id']=>$vmenu_height);
					$uppend3 = $uppend3 + array('subhead_'.$mcat_arr['category_id']=>'link_subhead_'.$mcat_arr['category_id']);*/
				}
			}
		
			$menu->AddMenu('resource',"root",164,20,"#bf9f00","#8f6101",'#bf9f00',"#FFFFFF","#FFFFFF");
			$menu->AddMenuItem("text",'resource',"Flag Lady Presentations",$this->MakeUrl('static/presentations/'));
			$menu->AddMenuItem("text",'resource',"General Display",$this->MakeUrl('static/general_display/'));
			$menu->AddMenuItem("text",'resource',"How To Fold The Flag",$this->MakeUrl('static/how_fold/'));
			$menu->AddMenuItem("text",'resource',"Links",$this->MakeUrl('static/links/'));
				
			return array('function'=>$menu->GetMenuFunction(),
					'call'=>$menu->GetMenuCall((array('0'=>0, 'resource'=>0)+$menu_x),
					(array('0'=>28, 'resource'=>28)+$menu_y),
					(array('0'=>'link_subhead', 'resource'=>'link_resourcedetails')+$menu_obj)));
					
		//return array('function'=>$menu->GetMenuFunction(),'call'=>$menu->GetMenuCall($menu_x,$menu_y,$menu_obj));
	}
	
	// function for array of letters for displaying links on landing page
	function GetLettersArray() {
		$alpha_arr = array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z');
		return $alpha_arr;
	}
	
	function GetLettersLinks($category) {
		$alpha_arr = $this->GetLettersArray();
		foreach($alpha_arr as $key=>$value) {
			$alpha_data .= '<a href="'.$this->MakeUrl($category.'/subcategory/index/','cat_id='.$_GET['cat_id'].'&letter='.$value).'" class="links_landing">'.$value.'</a>';
		}
		$alpha_data .= '<a href="'.$this->MakeUrl($category.'/subcategory/index/','cat_id='.$_GET['cat_id']).'" class="links_landing">All</a>';
		$alpha_data .= '<a href="'.$this->MakeUrl($category.'/subcategory/index/','cat_id='.$_GET['cat_id'].'&letter=other').'" class="links_landing">Other</a>';
		return $alpha_data;
	}
	
	function GetRecordsPerColumn($cat_id) { 
			if($cat_id) {
			$i=0;
			$alpha_arr = $this->GetLettersArray();
			foreach($alpha_arr as $key=>$value) {
				$getRes = $this->Select(TABLE_CATEGORY,"parent_id='".$cat_id."' and cat_name LIKE '".$value."%' and is_active='1'","*","position");
				if(count($getRes)>0) {
					$i++;
				}
			}
		}
		if($i>0) 
			return ceil(($i+1)/3);
			
		else
			return 1;
	}
	
	
	
	function Ajax_countProductDetail($pId) {
		$getRes = $this->Select(TABLE_PRODUCT_SIZES,"product_id='".$pId."'", " COUNT(*) AS cnt ");
		$cnt = $getRes[0]['cnt'];
		return $cnt;
	}
	
	function GetParentId($catId) {
		$get_result=$this->Select(TABLE_CATEGORY,"cat_id='".$catId."'","parent_id, cat_id","cat_name");
		if(count($get_result)>0) {
			if($get_result[0]['parent_id']==0)
				$result = $catId;
			else {
				$get_res=$this->Select(TABLE_CATEGORY,"cat_id='".$get_result[0]['parent_id']."'","parent_id, cat_id","cat_name");
				if($get_res[0]['parent_id']==0) {
					$result = $get_res[0]['cat_id'];
				} else {
					$getRes=$this->Select(TABLE_CATEGORY,"cat_id='".$get_res[0]['parent_id']."'","parent_id, cat_id","cat_name");
					if($getRes[0]['parent_id']==0) {
						$result = $getRes[0]['cat_id'];
					} else {
						$result = $getRes[0]['parent_id'];
					}
				}
			}
		} else {
			$result="";
		}
		return $result;
	}
	
	function GetCategoryId($pId) { 
		$getRes = $this->Select(TABLE_PRODUCTS,'product_id='.$pId);
		return $getRes[0]['cat_id'];
	}
	
	function Ajax_deleteProductRecords($pId) {
		$id_arr = explode("||||",$pId);
		$val=$id_arr[0];
		$pid=$id_arr[1];
		
		if($val=='fix') $val='y';
		else $val='n';
		$getRes = $this->Delete(TABLE_PRODUCT_SIZES,"product_id='".$pid."'");
		// Update Product table 
		$getRes = $this->Select(TABLE_PRODUCT_SIZES,"product_id='".$pid."'");
		$cnt = count($getRes);
		$this->Update(TABLE_PRODUCTS,array('size_count'=>$cnt,'is_fixed'=>"'".$val."'"),"product_id='".$pid."'");
		return $pid."||||".$val;
	}
	
	function GetCollectionTitle($collectionId) {
		$getRes = $this->Select(TABLE_CATEGORY,"cat_id='".$collectionId."'","cat_name");
		return $getRes[0]['cat_name'];
	}	
	
	function GetOtherSubCatData($cat_id) {
		$alpha_arr = $this->GetLettersArray(); 
		foreach($alpha_arr as $key=>$value) {
			$upd_arr[$key] = "'".$value."'";
		}
		$alpha_str = implode(",",$upd_arr);
		//echo $alpha_str;
		$getRes = $this->Select(TABLE_CATEGORY,"parent_id='".$cat_id."' and UPPER(SUBSTRING(`cat_name`,1,1)) NOT IN (".$alpha_str.") and is_active='1'","*","position");
		return $getRes;
	}
	
	function Ajax_Email($userName) {
		$get_result=$this->Select(TABLE_CUSTOMERS,"email='".$userName."'","email");
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
	
	function GetParentCatId($catId) {
		$getRes = $this->Select(TABLE_CATEGORY,"cat_id='".$catId."'","parent_id");
		return $getRes[0]['parent_id'];
	}
	
	function GetCategoryTree_old($cat_id) {
		$parent_id = $this->GetParentCatId($cat_id);
		if($parent_id==0) {
			$getRes=$this->Select(TABLE_CATEGORY,"cat_id='".$cat_id."'","*");
			if(count($getRes)>0) {
				$maincatid=$getRes[0]['cat_id'];
				$heading_maincat = ucfirst(htmlentities(stripslashes($getRes[0]['cat_name'])));
				//$category_tree = "Products >> ".$heading_maincat;
				$category_tree = 'Products >> <a href="'.$this->MakeUrl('subcategory/index/','id='.$maincatid).'" class="link_heading" >'.$heading_maincat.'</a>';
			}
		} else {
			$getRes=$this->Select(TABLE_CATEGORY." AS mcat LEFT JOIN ".TABLE_CATEGORY." AS scat ON mcat.cat_id=scat.parent_id","scat.is_active='1' and scat.cat_id='".$cat_id."'","mcat.cat_id, scat.cat_id as scat_id,mcat.cat_name as maincat,scat.cat_name as subcat");
			if(count($getRes)>0) {
				$maincatid=$getRes[0]['cat_id'];
				$scat_id = $getRes[0]['scat_id'];
				$heading_maincat = ucfirst(stripslashes($getRes[0]['maincat']));
				$heading_subcat = ucfirst(stripslashes($getRes[0]['subcat']));
				//$heading_maincat='<a href="'.$this->MakeUrl("category/index/","id=".$maincatid).'" class="link1"><strong>'.ucfirst($heading_data[0]['maincat']).'</strong></a>';
				//$heading_subcat='<a href="'.$this->MakeUrl("products/index/","id=".$scat_id).'" class="link1"><strong>'.ucfirst($heading_data[0]['subcat']).'</strong></a>';*/
				$category_tree = 'Products &gt;&gt; <a href="'.$this->MakeUrl('subcategory/index/','id='.$maincatid).'" class="link_heading" >'.$heading_maincat.'</a> &gt;&gt; <a href="'.$this->MakeUrl('products/index/','id='.$scat_id).'" class="link_heading">'.$heading_subcat."</a>";
			} else {
				$category_tree = "";
			}
		}
		return $category_tree;
	}
	
	function getCreditCardTypeData() {
		$type_arr = array('Visa'=>'Visa','Mastercard'=>'Mastercard','Discover'=>'Discover','American Express'=>'American Express');
		foreach($type_arr as $key=>$value) {
			$type_data .= '<option value="'.$key.'"';
			//if($typeSelected == $key ) $type_data .= ' selected="selected"';
			$type_data .= '>'.$value.'</option>';		
		}
		return $type_data;
	}
	
	function GetCategoryTree($category_id){
		$parent_id = $this->GetParentCatId($category_id);
		if($parent_id==0) {
			// Clicking on Main Category
			$getRes=$this->Select(TABLE_CATEGORY,"cat_id='".$category_id."'","*");
			if(count($getRes)>0) {
				$maincatid=$getRes[0]['cat_id'];
				$heading_maincat = ucfirst($getRes[0]['cat_name']);
				//$category_tree = "Products >> ".$heading_maincat;
				if($getRes[0]['link_to_landing']==1)
					$str_tree = '<a href="'.$this->MakeUrl($this->formatUrl($heading_maincat).'/subcategory/index/','cat_id='.$maincatid).'" class="links" ><h1 class="tree">'.$heading_maincat.'</h1></a>';
				else 
					$str_tree = $heading_maincat;
			}
		} else {
			$pid = $this->GetParentCatId($parent_id);
			if($pid==0) {
				// Clicking on Sub Category
				$cat_arr=$this->Select(TABLE_CATEGORY." as pt LEFT JOIN ".TABLE_CATEGORY." as ct ON pt.cat_id=ct.parent_id" ,"ct.cat_id=".$category_id." and pt.parent_id=0 and pt.is_active='1' GROUP BY pt.cat_id","pt.cat_id as maincatid, pt.cat_name as parent_cat, pt.link_to_landing,  ct.cat_name as sub_cat","");
				if(count($cat_arr)){
					foreach($cat_arr as $arr){
						$maincatid = $arr['maincatid'];
						if($arr['parent_cat']!="") {
							$str_tree .= stripslashes($arr['parent_cat']);
						}
						$navTree = $this->formatUrl($arr['sub_cat'].'/'.$str_tree);
						if($arr['sub_cat']!="")
							$str_tree .= ' / <a href="'.$this->MakeUrl($navTree.'/collections/index/','subcat_id='.$category_id).'" class="links" ><h1 class="tree">'.stripslashes($arr['sub_cat']).'</h1></a>';
					}
				}
			} else {
				// Clicking on Collection
				$subsubcat_arr=$this->Select(TABLE_CATEGORY." as pt, ".TABLE_CATEGORY." as ct1, ".TABLE_CATEGORY." as ct2" ," pt.parent_id=0 and ct2.parent_id=ct1.cat_id and ct1.parent_id = pt.cat_id and (ct1.cat_id=".$category_id." OR ct2.cat_id = ".$category_id.") and  pt.is_active='1' GROUP BY pt.cat_id","pt.cat_name as parent_cat, ct1.cat_id as scatid, ct1.cat_name as sub_cat, ct2.cat_name as subsubcat","");
				
				if(count($subsubcat_arr)){
					foreach($subsubcat_arr as $subsubarr){  
						/*********SEO Code Starts here ***********/
							if($this->mPageName!='product_details'){
								$susubcat = '<h1 class="tree">'.stripslashes($subsubarr['subsubcat']).'</h1>';
							}else{
								$susubcat = stripslashes($subsubarr['subsubcat']);
							}
						/*********SEO Code ends here ***********/														
						
						
						if($subsubarr['parent_cat']!="")
							$str_tree .= stripslashes($subsubarr['parent_cat']);
						if($subsubarr['sub_cat']!="")
							$str_tree .= ' / <a href="'.$this->MakeUrl($this->formatUrl($subsubarr['sub_cat']).'/'.$this->formatUrl($subsubarr['parent_cat']).'/collections/index/','subcat_id='.$subsubarr['scatid']).'" class="links" >'.stripslashes($subsubarr['sub_cat']).'</a>';
						if($subsubarr['subsubcat']!=""){
							$navTree = $this->formatUrl($subsubarr['subsubcat'].'/'.$subsubarr['sub_cat'].'/'.stripslashes($subsubarr['parent_cat']));
							$str_tree .= ' / <a href="'.$this->MakeUrl($navTree.'/products/index/','collection_id='.$category_id).'" class="links" >'.$susubcat.'</a>';
						}							
					}
				}
			}
		}
		return $str_tree;		
	}
	// Get Star level info of customer logged in
	function GetStarLevelInfo() {
		$getRes = $this->Select(TABLE_CUSTOMERS,"customer_id='".$_SESSION['sess_user_id']."'","");
		if($getRes[0]['starlevel_id']!=0) {
			$getRes_star = $this->Select(TABLE_STAR_LEVEL,"starlevel_id='".$getRes[0]['starlevel_id']."' and is_active='1'","*");
			if(count($getRes_star)>0) {
				return $getRes_star[0]['starlevel_title']." (".$getRes_star[0]['discount']."%)";
			} else {
				return "";
			}
		} else {
			return "";
		}
	}
	
	// Get Parent Product Info
	function GetParentProductInfo($product_id) {
		$getRes = $this->Select(TABLE_PRODUCTS,"product_id='".$product_id."' and is_active='1'","*");
		if(count($getRes)>0)  {
			foreach($getRes as $result) {
				$row = $result;
			}
			return $row;
		} else {
			return "";
		}
	}
	
	function deactivateProductRecords($id, $dbTable, $primaryKey){
		$id = implode(",",$_POST['record_ids']);
		// Code for fetching records already deactivated
		$getRes_Active=$this->Select($dbTable,"is_active='0' and ".$primaryKey." IN (".$id.")", $primaryKey);
		if(count($getRes_Active)>0) {
			foreach($getRes_Active as $result_a) {
				$active_arr[]=$result_a[$primaryKey];
			}
		}
		$deactive_ids=implode(", ",$active_arr);
		
		$getRes_Deactive=$this->Select($dbTable,"is_active='1' and ".$primaryKey." IN (".$id.")", $primaryKey);
		if(count($getRes_Deactive)>0) {
			foreach($getRes_Deactive as $result_d) {
				$getRes_Featured=$this->Select(TABLE_FEATURED,"product_id ='".$result_d['product_id']."'", "product_id");
				if(count($getRes_Featured)>0) {
					foreach($getRes_Featured as $result_f) {
						$featured_arr[]=$result_f['product_id'];
					}
					$featured_ids=implode(", ",$featured_arr);
				} else {
					$this->Update($dbTable,array('is_active'=>"'0'"), $primaryKey."='".$result_d[$primaryKey]."'");
					$deactive_arr[]=$result_d[$primaryKey];
				}
				
			}
		}
		
		$active_ids=implode(", ",$deactive_arr);
		return array($active_ids,$deactive_ids,$featured_ids); 
			
	}//end function deactivateRecords
	
		////////showing price of selected size in cart
	function Ajax_showFeaturedSelectedSizeDetails($size_id){
		$ProductSizeArr = $this->Select(TABLE_PRODUCT_SIZES,"size_id='".$size_id."'", "*","position");
		if(count($ProductSizeArr)>0) {
			 $str = '<tr>
						<td align="left" valign="top" class="t1"><span class="t2"><input type="hidden" name="id" value="'.$size_id.'">'.$ProductSizeArr[0]['size'].'&nbsp;'.$ProductSizeArr[0]['size_unit'].'</span>
							</td>
					 </tr>';
					 
			$ProductRealPrice = $ProductSizeArr[0]['price'];
			if($_SESSION['sess_user_id']){
				$cart=new Cart(session_id(),$this);
				$discountArr = $cart->getStarLevelDiscount($_SESSION['sess_user_id'], $ProductSizeArr[0]['price']);
				if($discountArr['dicountedPrice']>0){
					$yourPrice = ($ProductRealPrice - $discountArr['dicountedPrice']); 
					$ProductRealPrice = '<strike>'.$ProductRealPrice.'</strike>';
					
				}
			 }
			$str .= '<tr>
						<td align="left" valign="middle" class="t3">$'.$ProductRealPrice.'</td>
					 </tr>';
			if($yourPrice)
				$str .= '<tr>
						<td align="left" valign="middle" class="t3">$'.$yourPrice.'</td>
					 </tr>';
			}
		return $str;
	}//end function showSelectedSizeDetails
	
		////////showing price of selected size on home page
	function Ajax_showFeaturedPrice($size_id){
		$ProductSizeArr = $this->Select(TABLE_PRODUCT_SIZES,"size_id='".$size_id."'", "*","position");
		if(count($ProductSizeArr)>0) {
			$str = '<table border="0" cellpadding="2" cellspacing="0">
						<tr><td><input type="hidden" name="id" value="'.$size_id.'"></td></tr>';
			
			$ProductRealPrice = $ProductSizeArr[0]['price'];
			if($_SESSION['sess_user_id']){
				$cart=new Cart(session_id(),$this);
				$discountArr = $cart->getStarLevelDiscount($_SESSION['sess_user_id'], $ProductSizeArr[0]['price']);
				if($discountArr['dicountedPrice']>0){
					$yourPrice = ($ProductRealPrice - $discountArr['dicountedPrice']); 
					$ProductRealPrice = '<strike>'.$ProductRealPrice.'</strike>';
					
				}
			}
			$str .= '<tr>
						<td>$'.$ProductRealPrice.'</td>
					</tr>
						';
			if($yourPrice) {
				$str .= '<tr>
						<td>$'.$yourPrice.'</td>
					</tr>
						'; 
			}
			
			$str .= '
				</table>';
			}
		return $str;
	}//end function showSelectedSizeDetails
	
	function GetQuickLinkIdArray() {
		$getRes = $this->Select(TABLE_QUICK_LINK);
		if(count($getRes)>0) {
			foreach($getRes as $result) {
				$row = array($result['collection_id1'],$result['collection_id2'],$result['collection_id3'],$result['collection_id4'],$result['collection_id5'],$result['collection_id6']);
			}
		}
		return $row;
	}
	
	function deactivateCollectionRecords($id, $dbTable, $primaryKey){
		// Code for fetching records already deactivated
		$getRes_Active=$this->Select($dbTable,"is_active='0' and ".$primaryKey." IN (".$id.")", $primaryKey);
		if(count($getRes_Active)>0) {
			foreach($getRes_Active as $result_a) {
				$active_arr[]=$result_a[$primaryKey];
			}
		}
		$deactive_ids=implode(", ",$active_arr);
		$getRes_Deactive=$this->Select($dbTable,"is_active='1' and ".$primaryKey." IN (".$id.")", $primaryKey);
		
		if(count($getRes_Deactive)>0) {
			$link_arr = $this->GetQuickLinkIdArray();
			
			foreach($getRes_Deactive as $result_d) {
				if(in_array($result_d['cat_id'],$link_arr)) {
					$quicklink_arr[]=$result_d['cat_id'];
				} else {
					$this->Update($dbTable,array('is_active'=>"'0'"), $primaryKey."='".$result_d[$primaryKey]."'");
					$deactive_arr[]=$result_d[$primaryKey];
				}
			}
		}
		
		$quicklink_ids=implode(", ",$quicklink_arr);
		$active_ids=implode(", ",$deactive_arr);
		return array($active_ids,$deactive_ids,$quicklink_ids); 
			
	}//end function deactivateRecords
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function OrderEmail($orderId,$subjectType='new', $type, $mode='') {
	$cartOrderMailStatus ='';
	$cartInvoiceMailStatus ='';
	$order_data=$this->Select(TABLE_CART_ORDER,"cart_order_id='".$orderId."'","*","");
	$i=1;
	if(count($order_data)>0) {
		$cartOrderMailStatus = 1;
		foreach($order_data as $arr) {
			$data_arr=$arr;
			$product_data=$this->Select(TABLE_CART_ORDER_INVOICE,"cart_order_id='".$orderId."'","*","order_invoice_id");
			if(count($product_data)>0) {
				$cartInvoiceMailStatus = 1;
			
				foreach($product_data as $product) {
						if($sep!=0) {
							$prod_data.='
									<tr>
									  <td height="2" colspan="6"></td>
									</tr>
									<tr>
									  <td height="1" colspan="6"><hr color="#000000" size="1"/></td>
									</tr>
									<tr>
									  <td height="2" colspan="6" ></td>
									</tr>
							';
						} else {
							$sep = 1;
						}
						
						$amt = (($product['price'] * $product['quantity']));
						$data_arr['total_weight'] += ($product['quantity'] * $product['weight']);
						
						////Gettting size details of product
						$specification =''; $sep='';
						if($type=='admin') {
							if($product['item_no']){
								$specification = $sep.'Product #: '.$product['item_no'];
								$sep = "<br>";
							}
						}
						if($product['size']){
							$specification .= $sep.'Size: '.$product['size'].' '.$product['size_unit'];
							$sep = '<br>';
						}
						////Gettting color details of product
						if($product['color_name']){
							$specification .= $sep.'Color: '.$product['color_name'];
						}
						
						
						$prod_data.='
						 <tr class="text">
								  <td align="center" valign="top" >'.$i.'</td>
								  <td style="padding-left:5px;" valign="top" align="left">'.nl2br(strip_tags(str_replace("<br />","\n",stripslashes($product['product_name'])))).'<br/>'.$specification.'</td>
								  <td style="padding-right:5px;" valign="top" align="right">'.sprintf("%03d",$product['quantity']).'</td>
								  <td style="padding-right:5px;" valign="top" align="right">'.$product['weight'].' '.$product['weight_unit'].'</td>
								  <td style="padding-right:3px;" valign="top" align="right">'.sprintf("$%01.2f",$product['price']).'</td>
								  <td style="padding-right:10px;" valign="top" align="right">'.sprintf("$%01.2f",$amt).'</td>
								</tr>';
						$i++;
				}//endfoeach
			}//endif
		}//endforeach
	}//endif
	
	$orderMailBody = $this->getOrderEmailTemplate($data_arr, $prod_data, $mode);
	
	if($subjectType=='' || $subjectType=='new') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Confirmation from ".html_entity_decode(SITE_NAME);
			$data_arr['comments']="-";
	} elseif($subjectType=='status') {
		$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Status from ".html_entity_decode(SITE_NAME);
	} 
	
	if($type=='user'){
		$email = $data_arr['e_mail'];
	}elseif($type=='admin'){
		$email = $this->GetAdminEmail();
	}
	
	$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
	//if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
	if(BCC_EMAIL!="")
		$headers .= 'Bcc: ' . BCC_EMAIL . " \r\n";
		
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
	
	if($cartInvoiceMailStatus!='' && $cartOrderMailStatus!=''){
		return @mail($email,$order_subject,$orderMailBody,$headers);
	}else{
		return @mail(DEVELOPER_EMAIL,'Order Err:'.$order_subject ,$orderMailBody,$headers);
	}
	//echo $orderMailBody; echo "<br>";	
}//end function


function getOrderEmailTemplate($data_arr, $prod_data, $mode=''){
	$billName 	 = $data_arr['bill_name'];
	$orderId 	 = '<strong>#FL-'.sprintf("%04d",$data_arr['cart_order_id']).'</strong>';
	$orderDate 	 = date('jS M,Y',strtotime($data_arr['order_date']));
	$status		 = strtoupper($data_arr['status']);
	$comments	 = nl2br(stripslashes($data_arr['comments']));
	$billName	 = stripslashes($data_arr['bill_name']);
	$billPhone	 = $data_arr['bill_phone'];
	$billAddress = stripslashes($data_arr['bill_address']);	
	$billCountry = stripslashes($data_arr['bill_country']);
	$billState	 = stripslashes($data_arr['bill_state']);
	$billCity	 = stripslashes($data_arr['bill_city']);
	$billZip	 = stripslashes($data_arr['bill_zipcode']);
	$email		 = stripslashes($data_arr['e_mail']);
	$shipName	 = stripslashes($data_arr['ship_name']);
	$shipPhone	 = $data_arr['ship_phone'];
	$shipAddress = stripslashes($data_arr['ship_address']);
	$shipCountry = stripslashes($data_arr['ship_country']);
	$shipState	 = stripslashes($data_arr['ship_state']);
	$shipCity	 = $data_arr['ship_city'];
	$shipZip	 = stripslashes($data_arr['ship_zipcode']);
	
	$shippingMethodsArr = $this->getShippingMethod();
	$shippingMethod = $shippingMethodsArr[$data_arr['shipping_method']];
	
	
	$subTotal	 = $data_arr['subtotal'];
	
	/**********************************************************************************
		Calculating star level discount
	***********************************************************************************/
	if($data_arr['star_level_discount']>0){
		$starLevelDiscount = $data_arr['star_level_discount'];
		$starLevelDiscountedPrice =  sprintf('%01.2f',($subTotal*$starLevelDiscount/100));
	}
	/**********************************************************************************
		Calculating promotional code discount
	***********************************************************************************/
	if($data_arr['promocode_discount']>0){
		$promoCodeDiscount = $data_arr['promocode_discount'];
		$promoCodeDiscountedPrice = sprintf('%01.2f', ($subTotal*($promoCodeDiscount/100)));
	}
	/**********************************************************************************
		Calculating shipping
	***********************************************************************************/
	if($data_arr['total_ship']>0){
		$shipping = '+$'.$data_arr['total_ship'];
	}else{
		$shipping = '+$0.00';
	}
	
	/**********************************************************************************
		Calculating sale tax
	***********************************************************************************/
	/*$total = $subTotal + $quote - $promoCodeDiscount;
	$applicableTax = ($total*($saletax/100));
	$cart_total = $total + $applicableTax;*/
	$total_amt = $subTotal + $data_arr['total_ship'];
	if($data_arr['sale_tax']>0){
		$saleTax = $data_arr['sale_tax'];
		$saleTaxPrice = sprintf('%01.2f', ($total_amt*$saleTax/100));
	} else {
		$saleTax = '0';
		$saleTaxPrice='0.00';
	}
	
	/**********************************************************************************
		Calculating Widght
	***********************************************************************************/
	
	$totalWeight = sprintf('%01.2f', $data_arr['total_weight']);
	$weightUnit = $data_arr['weight_unit'];
	
	$totalWeight =  $totalWeight.' lbs'; 
	
	$orderTotal  = $data_arr['order_total'];
	
	/// Changing bg color of email if mode is manual
	if($mode=='manual')  $bgColor = '#F3F3F3';
	else  $bgColor = '#D6D6D6';
								
	$order_message='
				<style type="text/css">
					.order_text {
						font-family: verdana;
						font-size: 11px;
						color: #000000;
					}
					.order_ban3 {
						font-family: verdana;
						font-size: 11px;
						font-weight:bold;
						color: #FFFFFF;
						padding-left:10px;
						height:22px;
						background-color:#750073;
					 
					}
					.order_border {
						border:1px solid #000000;
					}
					.order_link_green { 
						cursor:pointer; 
						font-family: Verdana; 
						font-size: 8pt; 
						font-weight:bold; 
						Color:#FDF8E1;
						text-decoration:none;
					}
					.order_link_green:Hover {
						cursor:pointer; 
						font-family: verdana; 
						font-size: 8pt; 
						font-weight:bold; 
						color:#CCCCCC;
						text-decoration:none;
					}
					.heading{
						font-family:verdana; 
						font-size:11px; 
						font-weight:bold;
						color:#FFFFFF;
						padding-left:10px;
						height:22px; 
						background-color:#115BA2;
					}
				</style>
				<table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="'.$bgColor.'" align="center">
				  <tr>
					<td colspan="2" align="left" ></td>
				  </tr>
				  <tr>
					<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #000000">
						<tr>
						  <td colspan="2" align="center" class="heading">&nbsp;Order Details</td>
						</tr>
						<tr>
						  <td colspan="2" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2" >
							  <tr>
								<td width="20%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Order #: </td>
								<td width="80%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$orderId.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Order Date:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$orderDate.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Status:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$status.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Shipping Method:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shippingMethod.'</td>
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
						  <td width="50%" align="center" class="heading"><strong>Billing Information</strong></td>
						  <td width="50%" align="center" class="heading" ><strong>Recipient’s Information</strong></td>
						</tr>
						<tr>
						  <td align="right" valign="top" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2">
							  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><label for="txtusername"> Name: </label></td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$billName.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$billPhone.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$billAddress.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">Country:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$billCountry.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$billState.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$billCity.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$billZip.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">E-Mail:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$email.'</td>
							  </tr>
							</table></td>
						  <td align="left" style="font-family:verdana; font-size:11px; color:#000000;" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
						  	  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Name: </td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$shipName.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shipPhone.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shipAddress.'</td>
							  </tr>
							   <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">Country:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shipCountry.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shipState.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shipCity.'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$shipZip.'</td>
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
						  <td colspan="2" align="center" class="heading">Products Ordered </td>
						</tr>
						<tr>
							<td height="2"></td>
						</tr>
						<tr>
						  <td colspan="2" align="right" style="font-family:verdana; font-size:11px; color:#000000;">
						  	<table width="99%" align="center" border="0" cellspacing="1" cellpadding="1" style="font-family:verdana; font-size:11px; color:#000000;">
							  <tr class="heading">
								<td align="center" style="color:#FFFFFF" width="2%">#</td>
								<td style="padding-left:2px;color:#FFFFFF" align="left" width="22%">Product Name </td>
								<td style="padding-right:2px;color:#FFFFFF" width="4%" align="right">Qty</td>
								<td style="padding-right:2px;color:#FFFFFF" width="12%" align="right">Weight</td>
								<td style="padding-right:2px;color:#FFFFFF" width="12%" align="right">Unit Price</td>
								<td style="padding-right:2px;color:#FFFFFF" align="right" width="8%">Amount</td>
							  </tr>
							  '.$prod_data.'
							  <tr>
								<td colspan="6"><hr color="#000000" size="2" />&nbsp;</td>
							  </tr>
							  <tr>
								<td align="center">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td colspan="2" align="right">Total Weight: '.$totalWeight.'</td>
								<td align="right"><strong>Sub Total:</strong></td>
								<td style="padding-right:10px;" align="right">$'.$subTotal.'</td>
							  </tr>';
			if($promoCodeDiscount > 0 && $promoCodeDiscountedPrice > 0){
			$order_message.=  '<tr>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td colspan="2" align="right"><strong>Promo Code Discount:</strong>('.$promoCodeDiscount.'%)</td>
								<td style="padding-right:10px;" align="right">-$'.$promoCodeDiscountedPrice.'</td>
							  </tr>';
			}				  
			$order_message.=  '<tr>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="right"><strong>Shipping:</strong></td>
								<td style="padding-right:10px;" align="right">'.$shipping.'</td>
							  </tr>
							  <tr>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="right"><strong>Sale Tax:</strong>('.$saleTax.'%)</td>
								<td style="padding-right:10px;" align="right">+$'.$saleTaxPrice.'</td>
							  </tr>
							  <tr>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="right"><strong>Order Total: </strong></td>
								<td style="padding-right:10px;" align="right">$'.($orderTotal).'</td>
							  </tr>
							  
							  <tr>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							  </tr>
							  
							</table></td>
						</tr>
					  </table></td>
				  </tr>';
		if($mode=='manual'){	  
			$order_message.='<tr>
							<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Note:</td>
							<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Please process this order manually at UPS website.</td>
						  </tr>';
		}
		$order_message .='</table>';
				
		return $order_message;

		}
		
		
function getShippingMethod(){
	$arr = array(
				'ISP'=>'In Store Pickup',
				'GND'=>'Ground',
				'3DS'=>'3 Day Select',
				'2DA'=>'2nd Day Air',
				'2DM'=>'2nd Day Air AM',
				'1DP'=>'Next Day Air Saver',
				'1DA'=>'Next Day Air',
				'1DM'=>'Next Day Air Early AM',
				/*'STD'=>'Canada Standard',*/
				'XPR'=>'Worldwide Express',
				'XDM'=>'Worldwide Express Plus',
				'XPD'=>'Worldwide Expedited',
				'WXS'=>'Worldwide Saver'
				);
	
	return $arr;
}

/////////////////////////////////// Function for returning service code of selected shipping method /////////////////////////
function getShippingMethodCode($method){
	$shippingMethodArr = array('ISP'=>'In Store Pickup',
						'1DM'=>'14',
						'1DA'=>'01',
						'1DP'=>'13',
						'2DM'=>'59',
						'2DA'=>'02',
						'3DS'=>'12',
						'GND'=>'03',
						'XPR'=>'07',
						'XDM'=>'54',
						'XPD'=>'08',
						'WXS'=>'65'
						);
	return $shippingMethodArr[$method];
	
}
////////////////////////////////////////////// Function ends //////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////Function Order Email Ends Here////////////////////////////////////////////////	

		/********************* Function for dropdown of weight unit in add product detail*************************/
		/*********************************************************************************************************/
		function GetWeightUnitDropDown() {
			$weight_arr = array('Pound'=>'Pound','Ounce'=>'Ounce');
			foreach($weight_arr as $key=>$value) {
				$data .= '<option id="'.$key.'" value="'.$key.'"';
				if($key==$selected)
					$data .= ' selected="selected"';
				$data .= '>'.$value.'</option>';
			}
			return $data;
		}
		
		function csvProductValidation($category, $subcategory, $collection){
			// Check if Category given is valid or not
			$cat_arr=$this->Select(TABLE_CATEGORY,"TRIM(cat_name)='".addslashes(trim($category))."' and parent_id='0'","cat_id");
			if($cat_arr[0]['cat_id']!="") {
				$category_id_new = $cat_arr[0]['cat_id'];
			} else {
				$errorlog .= 'No category exit with this name';
				return false;//if subcategoryid do not match with database value return false
			}
			
			// Check if Sub Category given is valid or not
			$subcat_arr=$this->Select(TABLE_CATEGORY,"TRIM(cat_name)='".addslashes(trim($subcategory))."' and parent_id ='".$category_id_new."'","cat_id, parent_id");
			if($subcat_arr[0]['cat_id']!="") {
				$subcategory_id_new = $subcat_arr[0]['cat_id'];
				$category_id = $subcat_arr[0]['parent_id'];
			} else {
				$errorlog .= 'No subcategory exit with this name';
				return false;//if subcategoryid do not match with database value return false
			}
			
			if($collection!=""){///if collection name is given then fetch this collection id and subcategoryid
				$coll_arr=$this->Select(TABLE_CATEGORY,"TRIM(cat_name)='".addslashes(trim($collection))."' and parent_id='".$subcategory_id_new."'","cat_id, parent_id");
				if($coll_arr[0]['cat_id']!="") {
					$collection_id = $coll_arr[0]['cat_id'];
					$subcategory_id= $coll_arr[0]['parent_id'];				
				} else {
					$errorlog .= 'No collection exit with this name';
					return false;//if collection name do not match in database return false
				}
			}
	
			///if collection name is given then check either this collection belongs to this same subcategory or not, if collection is not given then return true
			//echo trim($category_id_new)."====".trim($category_id);
			if($collection!=''){
				if(trim($subcategory_id_new)!=trim($subcategory_id)){
					$errorlog .= "collection do not belongs to this subcategory";
					return false;///collection do not belongs to this subcategory
				}
				if(trim($category_id_new)!=trim($category_id)){
					$errorlog .= "subcategory do not belongs to this category";
					return false;///subcategory do not belongs to this category
				}
			}	
			//echo "Error".$errorlog;
			$dataArr = array($category_id, $subcategory_id, $collection_id);
			return $dataArr;
	}
	
	function ValidateProductItemName($collection_id, $item_name)  {
		///do not insert duplicate values
		$pro_arr=$this->Select(TABLE_PRODUCTS,"TRIM(item_name)='".addslashes(trim($item_name))."' AND cat_id='".$collection_id."'","product_id");	
		if($pro_arr[0]['product_id']!=''){		
			return false;
		} else
			return true;
	}
	
	function ValidateRelProductItemName($collection_id, $item_name, $product_id)  {
		///do not insert duplicate values
		$pro_arr=$this->Select(TABLE_PRODUCTS,"TRIM(item_name)='".addslashes(trim($item_name))."' AND cat_id='".$collection_id."' AND product_id !='".$product_id."'","product_id");	
		if($pro_arr[0]['product_id']==''){		
			return false;
		} else
			return $pro_arr[0]['product_id'];
	}
	
	function ValidateProductDetail($product_id, $item_no) {
		$pro_arr=$this->Select(TABLE_PRODUCT_SIZES,"TRIM(item_no)='".addslashes(trim($item_no))."' AND product_id ='".$product_id."'","product_id");	
		if($pro_arr[0]['product_id']!=''){		
			return false;
		} else 
			return true;
	}

	function GetProductTypeId($product_type) {
		$pro_type_arr=$this->Select(TABLE_PRODUCT_TYPES,"TRIM(product_type)='".addslashes(trim($product_type))."'","type_id");	
		return $pro_type_arr[0]['type_id'];
	}
	
	function getColorId($color){
		$colorImageArr = $this->Select(TABLE_COLORS,"color_name='".addslashes($color)."'", "id");
		return $colorImageArr[0]['id'];
	}/// getColorImage function ends here

	
	function ProcessPayment($pay_data){
		include_once(DIR_CLASS.'paymentclass.php');
		$CC_FirstName	=	$pay_data['first_name'];
		$CC_LastName	=	$pay_data['last_name'];
		$CC_Email		=	$pay_data['email'];		
		$CC_Address		=	$pay_data['address'];
		$CC_City		=	$pay_data['city'];
		$CC_State		=	$pay_data['state'];
		$CC_Country		=	$pay_data['country'];
		
		$CC_ZipCode		=	$pay_data['zip']; 
		$CC_Telephone	=	$pay_data['phone'];
		$CC_No 			=	$pay_data['cardno']; 
		$CC_CVV			=	$pay_data['cvvcode'];
		$CC_Exp 		=	$pay_data['expirymonth'].$pay_data['expiryyear'];		
		$amount			=	$pay_data['grand_total'];
		
		
		$AuthorizeNet = new AuthorizeNetBilling();
		$AuthorizeNet->setDebugMode(false);
		$AuthorizeNet->setTestMode(AUTH_DEBUG_MODE);
		$AuthorizeNet->setTransactionUrl(AUTH_TRANSACTION_URL);
		$AuthorizeNet->SetCredentials(AUTH_LOGIN_ID,AUTH_TRANSACTION_KEY);
		$AuthorizeNet->SetTransactionType('AUTH_CAPTURE');
		$AuthorizeNet->SetMethodType('CC');
		$AuthorizeNet->SetAmount($amount);
		$AuthorizeNet->SetCCNumber($CC_No);
		$AuthorizeNet->SetExpDate($CC_Exp);
		$AuthorizeNet->SetTransactionDescription('');
		$AuthorizeNet->CustomerBilling($CC_FirstName, $CC_LastName, $CC_Email, $CC_Address, $CC_City, $CC_State, $CC_ZipCode, $CC_Telephone, '', $CC_Country, '', '', '', '');
		//$AuthorizeNet->CustomerShipping($first_name, $last_name, $address, $city, $state, $zip, $country=NULL, $company=NULL)
		
		$AuthResponse = $AuthorizeNet->ProcessTransaction();
		
		$AuthApproved = $AuthorizeNet->ApprovalResponse($AuthResponse);
		if ($AuthApproved == "APPROVED") {
			$Auth_TransactionID = $AuthorizeNet->GetTransactionID($AuthResponse);
			$Auth_ApprovalCode = $AuthorizeNet->GetApprovalCode($AuthResponse);
			$Auth_Response = $AuthorizeNet->GetResponseReason($AuthResponse);
			$Auth_AVSResponse = $AuthorizeNet->GetAVSResponse($AuthResponse);
			$Auth_AuthResponse = explode($AuthorizeNet->delim_char, $AuthResponse);
			$CCLength =  strlen($CC_CCNumber) - 4;
			$Last4CC = substr($CC_CCNumber, $CCLength, 4);
			
			return array('status'=>$AuthApproved, 'transactionId'=>$Auth_TransactionID);
			
		}else{
			$Auth_Response = $AuthorizeNet->GetResponseReason($AuthResponse);
			$Auth_Response = str_replace('(TESTMODE)', '', $Auth_Response);
			return array('status'=>'FAILED', 'responseReason'=>$Auth_Response);
		}
	
	}
	
	/*********************** Get Main Category Id if category exists for import sub category ********************/
	/************************************************************************************************************/
	function getMainCategoryId($catName) {
		$getRes = $this->Select(TABLE_CATEGORY,"cat_name='".addslashes($catName)."' AND parent_id='0'","cat_id");
		if(count($getRes)>0) {
			return $getRes[0]['cat_id'];
		} else {
			return "";
		}
	}
	/*************************************** getMainCategoryId() ends  ******************************************/
	
	/*********************** Validate Sub Category Name exists in category or not *******************************/
	/************************************************************************************************************/
	function csvSubCategoryValidation($catId,$subCatName) {
		$getRes = $this->Select(TABLE_CATEGORY,"cat_name='".addslashes($subCatName)."' AND parent_id='".$catId."'","cat_id");
		if(count($getRes)>0) {
			return $getRes[0]['cat_id'];
		} else {
			return "";
		}
	}
	/*************************************** getMainCategoryId() ends  ******************************************/
	
	function CopyCollectionImageSourceToDestination($source) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$new_file=uniqid('coll_');
			$destination = DIR_COLLECTION.$new_file.'.'.$ext;
			
			if(copy($source,$destination)) {
				if($width>600 || $height>450)
					$this->CreateThumb($new_file,$ext,DIR_COLLECTION,600,450,DIR_COLLECTION);
				$this->CreateThumb($new_file,$ext,DIR_COLLECTION,187,187,DIR_COLLECTION_THUMBNAIL);				
				$this->CreateThumb($new_file,$ext,DIR_COLLECTION,90,90,DIR_COLLECTION_SMALL);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	/**************************** Functions for import CSV of Customers **************************/
	function getStaticAccountTypeValues(){
		$options = array(
						'R'=>'Retail',
						'C'=>'Corporate',
						'NP'=>'Non-Profit',
						'G'=>'Government'
						);	
		return $options;		
	}
	
	function ValidateCustomerEmailId($email) {
		$customer_arr=$this->Select(TABLE_CUSTOMERS,"TRIM(email)='".trim($email)."'","customer_id");	
		if(count($customer_arr)>0) {
			return false;
		} else {
			return true;
		}
	}
	
	function GetStarLevelId($starlevel) {
		$getRes = $this->Select(TABLE_STAR_LEVEL,"is_active='1' AND TRIM(starlevel_title)='".addslashes(trim($starlevel))."'","starlevel_id");
		if(count($getRes)>0) {
			return $getRes[0]['starlevel_id'];
		} else {
			return "";
		}
	}
	
	// Code for updating position of TABLE_PRODUCT_SIZES
	function UpdatePosition() { 
		$productIdArr = $this->Select(TABLE_PRODUCTS,'', "product_id");
		foreach($productIdArr as $pid){
			$sizeidArr = $this->Select(TABLE_PRODUCT_SIZES,"product_id='".$pid['product_id']."'", "size_id");		
			$i=1;
			foreach($sizeidArr as $sizeId){
				$this->Update(TABLE_PRODUCT_SIZES,array('position'=>$i), "size_id='".$sizeId['size_id']."'");
				$i++;
			}
		}
	}
	/**************************** Functions for import CSV of Customers ends here **************************/
	
	// Code for resizing images of TABLE_PRODUCT_IMAGES
	function ResizeImage() { 
		$getRes = $this->Select(TABLE_PRODUCT_IMAGES,"","image");
		if(count($getRes)>0) {
			foreach($getRes as $result) {
				$img_path=trim(DIR_PRODUCT.trim($result['image']));
				$this->CopyResizeImageSourceToDestination($img_path, $result['image']);
			}
		}
	}
	
	function CopyResizeImageSourceToDestination($source, $img_name) {
		
		if(is_file($source)) {
			
			list($width,$height,$ext)=getimagesize($source);
			
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$efilename = explode('.', $img_name);
			$new_file = $efilename[0];
			
			$destination = DIR_PRODUCT.$new_file.'.'.$ext;
				
			$this->CreateFixThumbForProduct($new_file,$ext,DIR_PRODUCT,PROD_THUMB_WIDTH,PROD_THUMB_HEIGHT,DIR_PRODUCT_THUMBNAIL);

		} else {
			return false;
		}
	}
	// Code for resizing images of TABLE_PRODUCT_IMAGES ends here
	


	function createOneirOrderFile($cartOrderId){
		$cartOrderArr=$this->Select(TABLE_CART_ORDER,"cart_order_id='".$cartOrderId."'","cart_order_id, ship_name, ship_address, ship_city, ship_state, ship_country, ship_zipcode, bill_name, bill_address, bill_state,bill_zipcode, bill_country, order_date, total_ship, bill_phone, e_mail, sale_tax,subtotal,order_total");
							
		$filename = 'wo'.str_pad($cartOrderId,6,'0',STR_PAD_LEFT);
		$filename = ONEIR_ORDER_DIRECTORY.$filename.'.txt';
		$shipToCode = '';
		
		$shipToName = substr($cartOrderArr[0]['ship_name'],0,30);
		$shipToAddress1 = substr($cartOrderArr[0]['ship_address'],0,30);
		$shipToAddress2 = substr($cartOrderArr[0]['ship_address'],31,60);
		$shipToAddress3 = substr($cartOrderArr[0]['ship_city'],0,30);
		$shipToProvince = strtoupper(substr($cartOrderArr[0]['ship_state'],0,2));
		$shipToCountry = strtoupper(substr($cartOrderArr[0]['ship_country'],0,3));
		$shipToPostalcode = substr($cartOrderArr[0]['ship_zipcode'],0,14);
		
		$billToCode = '';
		$billToName = substr($cartOrderArr[0]['bill_name'],0,30);
		$billToAddress1	= substr($cartOrderArr[0]['bill_address'],0,30);
		$billToAddress2 = substr($cartOrderArr[0]['bill_address'],31,60);
		$billToAddress3 = substr($cartOrderArr[0]['bill_address'],61,90);
		$billToProvince = strtoupper(substr($cartOrderArr[0]['bill_state'],0,2));
		$billToCountry	= strtoupper(substr($cartOrderArr[0]['bill_country'],0,3));
		$billToPostalCode = substr($cartOrderArr[0]['bill_zipcode'],0,14);
		
		$orderDateArr = explode('',$cartOrderArr[0]['order_date']);
		$orderDate = str_replace('-','',$orderDateArr[0]);
		$orderDate = substr(date('Ymd'),0,8);
		
		$comment = substr($comment,0,30);
		$shippingAmount = substr($cartOrderArr[0]['total_ship'],0,10);
		$phone = substr($cartOrderArr[0]['bill_phone'],0,14);
		$email=substr($cartOrderArr[0]['e_mail'],0,70);
		
		$overallDiscountPercent = substr('0.00',0,10);
		$invoicePstAfterDiscount = substr('',0,10); //INVOICE.PST.AFTER.DISC
		//converting sale tax percent in amount
		$saleTax = (($cartOrderArr[0]['subtotal']*$cartOrderArr[0]['sale_tax'])/100); 
		$invoiceGstAfterDiscount = substr(sprintf('%01.2f', $saleTax),0,10); //INVOICE.GST.AFTER.DISC  = SaleTax * 
		$inoviceAfterDiscTax = substr($cartOrderArr[0]['order_total'],0,10);//INVOICE.AFTER.DISC.&.TAX = Order Total *
		$creditCard = substr('',0,6); 
		
		/******************************************************************************************
			Header FIELDS CONTENTS
		*******************************************************************************************/
		$content = '';
		$content = 'H';
		$content .= str_pad($cartOrderId, 6,'0', STR_PAD_LEFT);
		$content .=  str_pad($shipToCode,6);
		
		$content .= str_pad($shipToName,30);
		$content .= str_pad($shipToAddress1,30);
		$content .= str_pad($shipToAddress2,30);
		$content .= str_pad($shipToAddress3,30);
		$content .= str_pad($shipToProvince,2);
		$content .= str_pad($shipToCountry,3);
		$content .= str_pad($shipToPostalcode,14);
		
		
		
		$content .= str_pad($billToCode,6);
		$content .= str_pad($billToName,30);
		$content .= str_pad($billToAddress1,30);
		$content .= str_pad($billToAddress2,30);
		$content .= str_pad($billToAddress3,30);
		$content .= str_pad($billToProvince,2);
		$content .= str_pad($billToCountry,3);
		$content .= str_pad($billToPostalCode,14);
		
		
		$content .= str_pad($orderDate,8);
		$content .= str_pad($comment,30);
		$content .= str_pad($shippingAmount,10);
		$content .= str_pad($phone,14);
		$content .= str_pad($email,70);
		
		
		$content .= str_pad($overallDiscountPercent,10);	//OVERALL.DISCOUNT.PERCENT 
		$content .= str_pad($invoicePstAfterDiscount,10); 	//INVOICE.PST.AFTER.DISC
		$content .= str_pad($invoiceGstAfterDiscount,10); 	//INVOICE.GST.AFTER.DISC  = SaleTax *
		$content .= str_pad($inoviceAfterDiscTax,10); 		//INVOICE.AFTER.DISC.&.TAX = Order Total *
		$content .= str_pad($creditCard,6);
		$content .= chr(13).chr(10);

		/******************************************************************************************
			DETAIL FIELDS CONTENTS
		*******************************************************************************************/
		$cartInvoiceArr=$this->Select(TABLE_CART_ORDER_INVOICE,"cart_order_id='".$cartOrderId."'","product_id,size_unit,product_name,size,color_name,quantity,price");
		foreach($cartInvoiceArr as $result){
			$sizeUnit = '';
			$productSizeArr=$this->Select(TABLE_PRODUCT_SIZES,"size_id='".$result['product_id']."'","item_no");

			$itemNumber = trim($productSizeArr[0]['item_no']);
			$sizeUnit = trim(strtolower($result['size_unit']));
			if($sizeUnit=='feet')$sizeUnit = '\''; elseif($sizeUnit=='inches')$sizeUnit = '"'; else $sizeUnit = $sizeUnit;
			
			$productDesc = trim($result['product_name']).' '.trim($result['size']).$sizeUnit.' - '.trim($result['color_name']);
			$productDesc = substr($productDesc,0,35);
			$quantity = sprintf('%01.2f', $result['quantity']);
			$unitPrice = $result['price'];
			$totalPrice = sprintf('%01.2f',($quantity*$unitPrice));
			
			$content .= 'D';	
			$content .= str_pad($cartOrderId, 6,'0', STR_PAD_LEFT);
			$content .= str_pad($itemNumber,12); 					//Product Code/Number
			$content .= str_pad($productDesc, 35); 					//Product Descriptions(Name+Size+Color)
			$content .= str_pad($quantity, 10); 					//QUANTITY
			$content .= str_pad($unitPrice, 10);					//Unit Price/SELLING.PRICE
			$content .= str_pad('0.00', 10);						//DISCOUNT.PERCENT  
			$content .= str_pad($totalPrice, 10);					//EXTENSION.BEFORE.DISC.TAX
			$content .= str_pad($totalPrice, 10);					//Total Price EXTENSION.BEFORE.TAX
			$content .= str_pad($totalPrice, 10);					//EXTENSION.AFTER.DISC.TAX 
			$content .= chr(13).chr(10);
		}
		
		/*******************************ASCII file content ends here*************************************/
		
		if(!$handle = fopen($filename,"w")){
			$err = 'cannot open file';
		}else{
			fwrite($handle,$content);
		}
	
	}//end function createOneirOrderFile
	
	// Function for update product position
	function UpdateProductPosition() { 
		$productIdArr = $this->Select(TABLE_CATEGORY,"parent_id='0'", "cat_id");
		if(count($productIdArr)) {
			foreach($productIdArr as $res) {
				$productIdArr_Sub = $this->Select(TABLE_CATEGORY,"parent_id='".$res['cat_id']."'", "cat_id");
				if(count($productIdArr_Sub)>0) {
					foreach($productIdArr_Sub as $res_sub) {
						$productIdArr_Col = $this->Select(TABLE_CATEGORY,"parent_id='".$res_sub['cat_id']."'", "cat_id");
						if(count($productIdArr_Col)>0) {
							foreach($productIdArr_Col as $res_col) {
								$productIdArr_Prod = $this->Select(TABLE_PRODUCTS,"cat_id='".$res_col['cat_id']."'", "product_id");
								if(count($productIdArr_Prod)>0) {
									$i=1;
									foreach($productIdArr_Prod as $result) {	
										$this->Update(TABLE_PRODUCTS,array('position'=>$i), "product_id='".$result['product_id']."'");
										$i++;
									}
								}
							}
						}
					}
				}
			}
		}
	} // end Function for update product position
	
	// Get Simple Category tree display on manage products at admin panel
	function GetSimpleCategoryTree($category_id){
		$parent_id = $this->GetParentCatId($category_id);
		if($parent_id==0) {
			// Clicking on Main Category
			$getRes=$this->Select(TABLE_CATEGORY,"cat_id='".$category_id."'","*");
			if(count($getRes)>0) {
				$maincatid=$getRes[0]['cat_id'];
				$heading_maincat = ucfirst(htmlentities(stripslashes($getRes[0]['cat_name'])));
				//$category_tree = "Products >> ".$heading_maincat;
				if($getRes[0]['link_to_landing']==1)
					$str_tree = $heading_maincat;
				else 
					$str_tree = $heading_maincat;
			}
		} else {
			$pid = $this->GetParentCatId($parent_id);
			if($pid==0) {
				// Clicking on Sub Category
				$cat_arr=$this->Select(TABLE_CATEGORY." as pt LEFT JOIN ".TABLE_CATEGORY." as ct ON pt.cat_id=ct.parent_id" ,"ct.cat_id=".$category_id." and pt.parent_id=0 and pt.is_active='1' GROUP BY pt.cat_id","pt.cat_id as maincatid, pt.cat_name as parent_cat, pt.link_to_landing,  ct.cat_name as sub_cat","");
				if(count($cat_arr)){
					foreach($cat_arr as $arr){
						$maincatid = $arr['maincatid'];
						if($arr['parent_cat']!="") {
							$str_tree .= stripslashes($arr['parent_cat']);
						}
						if($arr['sub_cat']!="")
							$str_tree .= ' / '.stripslashes($arr['sub_cat']);
					}
				}
			} else {
				// Clicking on Collection
				$subsubcat_arr=$this->Select(TABLE_CATEGORY." as pt, ".TABLE_CATEGORY." as ct1, ".TABLE_CATEGORY." as ct2" ," pt.parent_id=0 and ct2.parent_id=ct1.cat_id and ct1.parent_id = pt.cat_id and (ct1.cat_id=".$category_id." OR ct2.cat_id = ".$category_id.") and  pt.is_active='1' GROUP BY pt.cat_id","pt.cat_name as parent_cat, ct1.cat_id as scatid, ct1.cat_name as sub_cat, ct2.cat_name as subsubcat","");
								
				if(count($subsubcat_arr)){
					foreach($subsubcat_arr as $subsubarr){  
						if($subsubarr['parent_cat']!="")
							$str_tree .= stripslashes($subsubarr['parent_cat']);
						if($subsubarr['sub_cat']!="")
							$str_tree .= ' / '.stripslashes($subsubarr['sub_cat']);
						if($subsubarr['subsubcat']!="")
							$str_tree .= ' / '.stripslashes($subsubarr['subsubcat']);
					}
				}
			}
		}
		return $str_tree;		
	}
	// Get Simple Category tree display on manage products at admin panel ends here
	
	function redirectToHTTPS(){
		///skipping local ip server for testing	
		 if(SITE_MODE=='PROD'){
			 if($_SERVER['HTTPS']!=="on"){
				foreach($_POST as $key => $value){
					$_SESSION['HTTPS_REQ'][$key]=$value;
				}
				$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				header("Location:$redirect");
			}
			
			if($_SESSION['HTTPS_REQ']){
				foreach($_SESSION['HTTPS_REQ'] as $key=>$value){
					$_POST[$key] = $_SESSION['HTTPS_REQ'][$key];
				}
				unset($_SESSION['HTTPS_REQ']);
			}
		}//end check local ip
	}

	function GetProductPosition($pId, $selectedcatId) {
		$getRes = $this->Select(TABLE_PRODUCTS,"product_id='".$pId."'","*");
		$cat_id = $getRes[0]['cat_id'];
		$upd_position = $getRes[0]['position'];
		if(count($getRes)) {
			if($getRes[0]['cat_id']==$selectedcatId) {
				$position = $getRes[0]['position'];
			} else {
				$maxId = $this->Select(TABLE_PRODUCTS,"cat_id='".$selectedcatId."'","position","position desc", 1);
				$position = $maxId[0]['position'] + 1;
				
				// Code to update position of other products
				$getRes1 = $this->Select(TABLE_PRODUCTS,"cat_id ='".$cat_id."' and position > '".$upd_position."'","*","position desc");
				if(count($getRes1)>0) {
					foreach($getRes1 as $result){
						$this->Update(TABLE_PRODUCTS,array('position'=>' position-1'),"product_id ='".$result['product_id']."'");
					}
				}
				
			}
		}
		return $position;		
	}//end function getProductPosition
	
	// Function for having Category Tree in Meta Title
	function GetTitleCategoryTree($category_id){
		$parent_id = $this->GetParentCatId($category_id);
		if($parent_id==0) {
			// Clicking on Main Category
			$getRes=$this->Select(TABLE_CATEGORY,"cat_id='".$category_id."'","*");
			if(count($getRes)>0) {
				$maincatid=$getRes[0]['cat_id'];
				$heading_maincat = ucfirst(htmlentities(stripslashes($getRes[0]['cat_name'])));
				//$category_tree = "Products >> ".$heading_maincat;
				$str_tree[] = $heading_maincat;
			}
		} else {
			$pid = $this->GetParentCatId($parent_id);
			if($pid==0) {
				// Clicking on Sub Category
				$cat_arr=$this->Select(TABLE_CATEGORY." as pt LEFT JOIN ".TABLE_CATEGORY." as ct ON pt.cat_id=ct.parent_id" ,"ct.cat_id=".$category_id." and pt.parent_id=0 and pt.is_active='1' GROUP BY pt.cat_id","pt.cat_id as maincatid, pt.cat_name as parent_cat, pt.link_to_landing,  ct.cat_name as sub_cat","");
				if(count($cat_arr)){
					foreach($cat_arr as $arr){
						$maincatid = $arr['maincatid'];
						if($arr['parent_cat']!="") {
							$str_tree[] = stripslashes($arr['parent_cat']);
						}
						if($arr['sub_cat']!="")
							$str_tree[] = stripslashes($arr['sub_cat']);
					}
				}
			} else {
				// Clicking on Collection
				$subsubcat_arr=$this->Select(TABLE_CATEGORY." as pt, ".TABLE_CATEGORY." as ct1, ".TABLE_CATEGORY." as ct2" ," pt.parent_id=0 and ct2.parent_id=ct1.cat_id and ct1.parent_id = pt.cat_id and (ct1.cat_id=".$category_id." OR ct2.cat_id = ".$category_id.") and  pt.is_active='1' GROUP BY pt.cat_id","pt.cat_name as parent_cat, ct1.cat_id as scatid, ct1.cat_name as sub_cat, ct2.cat_name as subsubcat","");
								
				if(count($subsubcat_arr)){
					foreach($subsubcat_arr as $subsubarr){  
						if($subsubarr['parent_cat']!="")
							$str_tree[] = stripslashes($subsubarr['parent_cat']);
						if($subsubarr['sub_cat']!="")
							$str_tree[] = stripslashes($subsubarr['sub_cat']);
						if($subsubarr['subsubcat']!="")
							$str_tree[] = stripslashes($subsubarr['subsubcat']);
					}
				}
			}
		}
		$tree = implode(', ',array_reverse($str_tree));
		return $tree;		
	}//end function get categoryTitleCategoryTree
	
	
	
	/**************************************************************************************************/
	/* Function will create add transaction data and add item script 
	/**************************************************************************************************/
	function ecommerceTracking($cartOrderId){
		$cartOrderArr=$this->Select(TABLE_CART_ORDER,"cart_order_id='".$cartOrderId."'","cart_order_id, ship_city, ship_state, ship_country, total_ship, sale_tax,order_total, subtotal");
		
		$saleTax = sprintf('%01.2F',((($cartOrderArr[0]['subtotal']+$cartOrderArr[0]['total_ship'])*$cartOrderArr[0]['sale_tax'])/100)); 
		$orderTotal = sprintf('%01.2f', $cartOrderArr[0]['order_total']);
		$shippingAmount = sprintf('%01.2f', $cartOrderArr[0]['total_ship']);
		$shipCity = addslashes(trim($cartOrderArr[0]['ship_city']));
		$shipToProvince = addslashes(trim($cartOrderArr[0]['ship_state']));
		$shipToCountry = trim($cartOrderArr[0]['ship_country']);
				
		/********* Preparing addTrans variables ************/
		$addTransVars = 'var pageTracker = _gat._getTracker(\'UA-15880637-1\');
						 pageTracker._trackPageview();';
		
		$addTransVars = 'pageTracker._addTrans("'.$cartOrderId.'","The Flag Lady", "'.$orderTotal.'", "'.$saleTax.'","'.$shippingAmount.'","'.$shipCity.'","'.$shipToProvince.'","'.$shipToCountry.'");';		
		
		/********* Preparing addItem variables ************/		
		$cartInvoiceArr=$this->Select(TABLE_CART_ORDER_INVOICE,"cart_order_id='".$cartOrderId."'","product_id,product_name,quantity,price");
		foreach($cartInvoiceArr as $result){
			$productSizeArr=$this->Select(TABLE_PRODUCT_SIZES,"size_id='".$result['product_id']."'","item_no");
			
			$itemNumber = addslashes(trim($productSizeArr[0]['item_no']));
			$productName = addslashes(trim($result['product_name']));
			$quantity = sprintf('%01.2f', $result['quantity']);
			$unitPrice = $result['price'];
			//$totalPrice = sprintf('%01.2f',($quantity*$unitPrice));
			
			$addItemVars .= 'pageTracker._addItem("'.$cartOrderId.'",
												"'.$itemNumber.'",
												"'.$productName.'",
												"",
												"'.$unitPrice.'",
												"'.$quantity.'");';
		}
		$submitTransaction = 'pageTracker._trackTrans();';
		
		return $addTransVars.$addItemVars.$submitTransaction;
		
	}//end function ecommerceTracking
	
	function GetShippingAmount($shipConfirmRequestArray) {
		include_once(DIR_CLASS.'ups_rates.php');
		$shipmentObj = new shipment();
		$shipmentDigest = '';
		
		$shipConfirmRequestXML = $shipmentObj->setShipConfirmRequestXML($shipConfirmRequestArray);
		$shipConfirmResponseArr = $shipmentObj->sendShipConfirmRequestXML($shipConfirmRequestXML,1);
		
		$returnArr = array();
		//echo $shipConfirmRequestXML; 
		//echo "<br>====================================== ShippingAmount ===================================<br>";
		//print_r($shipConfirmResponseArr);
		//echo "<br>==========================================================================================<br>";
		$returnArr['shipping_charge'] = $shipConfirmResponseArr['shipping_charge'];
		$returnArr['shipping_err'] = $shipConfirmResponseArr['shipping_error']['ERRORDESCRIPTION'];
		
		return $returnArr;
	
	}
	
	function GetProductListing($collectionId) {
		$productArr = $this->Select(TABLE_PRODUCTS,"cat_id='".$collectionId."' AND size_count > 0 AND is_active='1'", "*","position");
		$prodNameArr = array();
		if(count($productArr)>0) {
			foreach($productArr as $result) {
				$prodNameArr[] = stripslashes(htmlentities($result['item_name']));
			}
		}
		$prodListing = implode(", ",$prodNameArr);
		return $prodListing;
	}
	
	//below code added on 23 Sep 2010 for SEO 
	function GetCategoryTreeForNavigation($category_id){
		$parent_id = $this->GetParentCatId($category_id);
		if($parent_id==0) {
			// Clicking on Main Category
			$getRes=$this->Select(TABLE_CATEGORY,"cat_id='".$category_id."'","*");
			if(count($getRes)>0) {
				$maincatid=$getRes[0]['cat_id'];
				$heading_maincat = ucfirst(htmlentities($this->formatUrl($getRes[0]['cat_name'])));
				//$category_tree = "Products >> ".$heading_maincat;
				if($getRes[0]['link_to_landing']==1)
					$str_tree = $heading_maincat;
				else 
					$str_tree = $heading_maincat;
			}
		} else {
			$pid = $this->GetParentCatId($parent_id);
			if($pid==0) {
				// Clicking on Sub Category
				$cat_arr=$this->Select(TABLE_CATEGORY." as pt LEFT JOIN ".TABLE_CATEGORY." as ct ON pt.cat_id=ct.parent_id" ,"ct.cat_id=".$category_id." and pt.parent_id=0 and pt.is_active='1' GROUP BY pt.cat_id","pt.cat_id as maincatid, pt.cat_name as parent_cat, pt.link_to_landing,  ct.cat_name as sub_cat","");
				if(count($cat_arr)){
					foreach($cat_arr as $arr){
						$maincatid = $arr['maincatid'];
						if($arr['parent_cat']!="") {
							$str_tree[] = $this->formatUrl($arr['parent_cat']);
						}
						if($arr['sub_cat']!="")
							$str_tree[] = $this->formatUrl($arr['sub_cat']);
					}
				}
			} else {
				// Clicking on Collection
				$subsubcat_arr=$this->Select(TABLE_CATEGORY." as pt, ".TABLE_CATEGORY." as ct1, ".TABLE_CATEGORY." as ct2" ," pt.parent_id=0 and ct2.parent_id=ct1.cat_id and ct1.parent_id = pt.cat_id and (ct1.cat_id=".$category_id." OR ct2.cat_id = ".$category_id.") and  pt.is_active='1' GROUP BY pt.cat_id","pt.cat_name as parent_cat, ct1.cat_id as scatid, ct1.cat_name as sub_cat, ct2.cat_name as subsubcat","");
				if(count($subsubcat_arr)){
					foreach($subsubcat_arr as $subsubarr){  
						if($subsubarr['parent_cat']!="")
							$str_tree []= $this->formatUrl($subsubarr['parent_cat']);
						if($subsubarr['sub_cat']!="")
							$str_tree []= $this->formatUrl($subsubarr['sub_cat']);
						if($subsubarr['subsubcat']!="")
							$str_tree []= $this->formatUrl($subsubarr['subsubcat']);
					}
				}
			}
		}
			
			$tree = implode('/', array_reverse($str_tree));
		return $tree;		
	}//end function  GetCategoryTreeForNavigation
	
	function checkActive($collectionId){
		$dataArr=$this->Select(TABLE_CATEGORY,"cat_id='".$collectionId."' AND is_active='1'","parent_id, cat_id");
		if(count($dataArr)>0){
			if($dataArr[0]['parent_id']==0){
				return 'active';
			}else{
				return $this->checkActive($dataArr[0]['parent_id']);
			}
		}else{
			return 'inactive';
		}
	}
	
	function formatUrl($str){	
		$str = str_replace(' - ',' ',$str);
		return stripslashes(str_replace(array(' ','"',"'",'(',')','&'),array('-','','','','','and'),$str));;
	}//end function formatURL
	
	
	/************************ Product CSV Export related function starts here - Oct 19 2010 ****************************************/
	function getProductImagesForCSV($product_id){
		$imgData = $this->Select(TABLE_PRODUCT_IMAGES, "product_id='".$product_id."'","image, image_alt","image_id");
		$flag = 0;	
			if(count($imgData)>0){
				foreach($imgData as $img){
					$imgArr[] = $img['image'];
					$imgAltArr[] = trim($img['image_alt']);
					$imgUrlArr[] = SITE_URL.DIR_PRODUCT.$img['image'];
					
					if(trim($img['image_alt'])!=''){
						$flag=1;
					}
				} 
				$images = implode("####", $imgArr);
				$images_url = implode("####", $imgUrlArr);
				
				if($flag==1)
					$images_alt = implode("|", $imgAltArr);
				return array('images'=>$images,'images_alt'=>$images_alt, 'images_url'=>$images_url);
			} 
	}//end function getProductImagesForCSV
	
	function getCategoryTreeForCSV($cat_id){
			$cat_arr = $this->Select(TABLE_CATEGORY." col INNER JOIN ".TABLE_CATEGORY." sub ON(sub.cat_id=col.parent_id) INNER JOIN ".TABLE_CATEGORY." cat on (sub.parent_id=cat.cat_id)", "col.cat_id='".$cat_id."' ","cat.cat_name category, sub.cat_name subcategory, col.cat_name collection"); 
			if(count($cat_arr)>0){
				return $str = $cat_arr[0]['category'].'|'.$cat_arr[0]['subcategory'].'|'.$cat_arr[0]['collection'].'|';
			}
	}
	
	function getRelatedProductsForCSV($product_id){
		//get id of all related products
		$related_productsArr = $this->Select(TABLE_PRODUCTS." P INNER JOIN ".TABLE_RELATED_PRODUCTS." RP ON(P.product_id=RP.rel_prod_id) ", "RP.product_id='".$product_id."'","P.item_name, P.cat_id","related_id");
		if(count($related_productsArr)>0){
			foreach($related_productsArr as $related_product){
				$productArr[] = $this->getCategoryTreeForCSV($related_product['cat_id']).$related_product['item_name'];
			}
			//$this->getCategoryTreeForCSV($related_product['cat_id']);
			$related_product = implode('####', $productArr);
			return $related_product;				
		}
	}//end function getRelatedProductesForCSV
	
	function getProductSizeAndDetails($product_id){
		$pSizesArr = $this->Select(TABLE_PRODUCT_SIZES." PS LEFT JOIN ". TABLE_COLORS." C ON (PS.color_id=C.id) ", "product_id='".$product_id."'","item_no, size, size_unit, weight, weight_unit, color_name, price, REPLACE(REPLACE(is_clearance, '0', 'No'),'1', 'Yes') is_clearance","PS.position"); 
		if(count($pSizesArr)>0){
			foreach($pSizesArr as $psize){
				$pDetailsArr[] = $psize['item_no'].'|'.$psize['size'].'|'.$psize['size_unit'].'|'.$psize['weight'].'|'.$psize['weight_unit'].'|'.$psize['color_name'].'|'.$psize['price'].'|'.$psize['is_clearance'];
			}			
			return implode('####',$pDetailsArr);			
		}
	}//end function getProductSizeAndDetails
	
	function exportProductCSV(){ 
		//$file=DIR_CSV_EXPORT."product_csv.csv";  
		$file="product_csv.csv";  
		# include parseCSV class.
		require_once(DIR_CLASS.'parsecsv.lib.php');
		# create new parseCSV object.
		$csv = new parseCSV();		
		//if admin want only active records only
		if($_POST['active_records']){
			$active="is_active='1' ";
		}
		//$content = "Product_Type,Main_Category,Sub_Category,Collection, Item_Name, Description, Images, Related_Products, Multiple_Size, Product_Details (Model Number|Size|Size Unit|Weight|Weight Unit|Color|Price| Clearance Item####Another Detail)\n";
		//get all categories
		$category_arr = $this->Select(TABLE_CATEGORY, "parent_id='0' ".$active,"cat_name, cat_id","cat_name"); 
		foreach($category_arr as $cat){
			//get all subcategories of this category
			$subcategory_arr = $this->Select(TABLE_CATEGORY, "parent_id='".$cat['cat_id']."' ".$active,"cat_name, cat_id","cat_name asc"); 
			foreach($subcategory_arr as $subcat){
				//get all collection of this sub-category
				$collection_arr = $this->Select(TABLE_CATEGORY, "parent_id='".$subcat['cat_id']."' ".$active,"cat_name, cat_id","cat_name asc"); 
				foreach($collection_arr as $collection){
				$collection_name = $collection['cat_name'];
					//get all products of this collection
					$joinQuery = TABLE_PRODUCTS." P LEFT JOIN ".TABLE_PRODUCT_TYPES." PT ON (P.product_type=PT.type_id) ";
					$product_filter = "cat_id='".$collection['cat_id']."'".$active;
					$product_arr = $this->Select($joinQuery, $product_filter,"product_id, item_name, PT.product_type, P.is_fixed, P.description, P.seo_title, P.seo_description, P.keywords","P.position asc"); 
					foreach($product_arr as $product){
						$product_type = trim($product['product_type']);
						$main_category= trim($cat['cat_name']);
						$subcategory_name = trim($subcat['cat_name']);
						$collection_name = trim($collection['cat_name']);
						$product_name = trim($product['item_name']);
						$product_desc= nl2br($product['description']);
						$product_desc=str_replace("\r\n",'',$product_desc);
						$product_desc=str_replace("\r",'',$product_desc);
						$product_id = trim($product['product_id']);
						$is_fixed = trim(strtolower($product['is_fixed']));
						
						$seo_title=nl2br(str_replace(array("\r\n", "\r"), array('',''), $product['seo_title']));
						$seo_keywords=nl2br(str_replace(array("\r\n", "\r"), array('',''), $product['keywords']));
						$seo_description=nl2br(str_replace(array("\r\n", "\r"), array('','') ,$product['seo_description']));
						
						//database changed. change all is_fixed=Y to multiple_size=N and is_fixed=N to multiple_size=Y
						if($is_fixed=='n') $multiple_size = 'Y'; else $multiple_size = 'N';
						$product_images_data = $this->getProductImagesForCSV($product_id);
						$product_images = $product_images_data['images'];
						$product_images_alt = $product_images_data['images_alt'];
						$image_url = $product_images_data['images_url'];
						$related_products = $this->getRelatedProductsForCSV($product_id);
						$product_details = $this->getProductSizeAndDetails($product_id);
						$data[] = array('Product_Type'=>$product_type, 'Main_Category'=>$main_category, 'Sub_Category'=>$subcategory_name, 'Collection'=>$collection_name, 'Item_Name'=>$product_name, 'Description'=>html_entity_decode($product_desc),'Images'=>$product_images,'Image_Alt(Image1_Alt|Image_Alt2|Image_Alt3)'=>$product_images_alt, 'Related_Products'=>$related_products, 'Multiple_Size'=>$multiple_size, 'Product_Details (Model Number|Size|Size Unit|Weight|Weight Unit|Color|Price| Clearance Item####Another Detail)'=>$product_details, 'SEO_Title'=>$seo_title, 'SEO_Keywords'=>$seo_keywords, 'SEO_Description'=>$seo_description, 'Image_URL'=>$image_url);
					}//product
				}//collection
			}//subcategory
		}//category

		$fields = array('Product_Type','Main_Category','Sub_Category','Collection', 'Item_Name', 'Description', 'Images', 'Image_Alt(Image1_Alt|Image_Alt2|Image_Alt3)' ,'Related_Products', 'Multiple_Size', 'Product_Details (Model Number|Size|Size Unit|Weight|Weight Unit|Color|Price| Clearance Item####Another Detail)','SEO_Title', 'SEO_Keywords', 'SEO_Description', 'Image_URL');
		$csv->output (true, $file, $data, $fields);
		/*
		$content=html_entity_decode($content);
		$handle = fopen ($file, "w+");
		fwrite($handle, $content); 
		fclose($handle);
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Disposition: attachment; filename=\"product_csv.csv\";" );
		header("Content-Transfer-Encoding: base64");
		header('Content-type: text/comma-separated-values');
		readfile($file);
		*/
		//@unlink($file);
		exit;
	}
	/************************ Product CSV Export related function ends here ****************************************/
}///////end of Class
?>