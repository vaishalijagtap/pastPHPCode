<?
class Project extends MainClass {
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
		return urlencode(base64_encode($str));//urlencode(base64_encode(
	} 
	function PagingHeader($limit,$query,$qOffset=0) {
		global $_SESSION,$_GET,$gStartPageNo;
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
			$pages=ceil($tot_rows/$limit);
			$_SESSION['tot_offset']=$pages;
			$offset=0;
			$query.=" LIMIT 0, $limit";
		}
		$gStartPageNo=$offset1;
		return $query;
	}
	function PagingFooter($offset,$class='link_admin') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		//$gPagingExtraPara=$gPagingExtraPara;
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";
		if($_SESSION['tot_offset']>1) {
			$j=$offset-1;
			$k=$offset+1;
			if($offset==0)  $display_page.= '<span class="text">First</span> | <span class="text">Previous</span> | ';
			else $display_page.= "<span class=\"text\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset=0')."' class='".$class."'><strong>First</strong></a></span> | <span class=\"text\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$j)."' class='".$class."'><strong>Previous</strong></a></span> | ";
			$tot_offset=$_SESSION['tot_offset'];
			$display_page.="<select name='paging_numbers' class='select1' onchange=\"javascript: window.location='".$this->MakeUrl($this->mCurrentUrl)."'+this.value;\" >";
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
				$display_page.= "<span class=\"text\"> | <a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$k)."' class='".$class."'><strong>Next</strong></a></span> | <span class=\"text\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($tot_offset-1))."' class='".$class."'><strong>Last</strong></a></span>";
			}
		}
		$_SESSION['tot_offset'];
		return $display_page;
	}
	
	function PagingFooter_front($offset,$class='default_link') {
		//echo $offset;  
		
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		//echo $gPagingExtraPara; exit;
		//$gPagingExtraPara=$gPagingExtraPara;
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";

		if($_SESSION['tot_offset']>1) {
			$j=$offset-1;
			$k=$offset+1;
			if($offset==0)  $display_page.= '<span class="text">First</span> | <span class="text">Previous</span> | ';
			else $display_page.= "<span class=\"white_txt\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset=0')."' class='".$class."'><strong>First</strong></a></span> | <span class=\"white_txt\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$j)."' class='".$class."'><strong>Previous</strong></a></span> | ";
			$tot_offset=$_SESSION['tot_offset'];
			$display_page.="<select name='paging_numbers' class='select1' onchange=\"javascript: window.location='".$this->MakeUrl($this->mCurrentUrl)."'+this.value;\" >";
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
				$display_page.= "<span class=\"white_txt\"> | <a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$k)."' class='".$class."'><strong>Next</strong></a></span> | <span class=\"white_txt\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.($tot_offset-1))."' class='".$class."'><strong>Last</strong></a></span>";
			}
		}
		//echo $_SESSION['tot_offset'];
		return $display_page;
	}
	
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
	function CreateThumbHeight($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		if ($height && ($height_orig < $width_orig)) {
			$height = ($width / $width_orig) * $height_orig;
		} else {
			$width = ($height / $height_orig) * $width_orig;
		}
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
	function GetFileExt ( $imgName ) {
		$efilename = explode('.', $imgName);
		return strtolower($efilename[count($efilename) -1 ])  ;
	}
	function GetAdminEmail() {
		$admin_data=$this->Select(TABLE_ADMIN,"","e_mail","",1);
		if(count($admin_data)) {
			foreach($admin_data as $admin) {
				return ucfirst($admin['e_mail']);
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
	function GetStateDropDown($stateSelected) { 
		global $db;
		$get_result=$this->Select(TABLE_STATE,"country_id=1");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$state_data .= '<option id="'.$result['state_prefix'].'" value="'.$result['state_prefix'].'"';
				if($stateSelected == $result['state_prefix']) $state_data .= ' selected="selected"';
				$state_data .= '>'.$result['state_name'].'</option>';
			}
		}
		return $state_data;
	}
	function GetMainCategoryDropDown_old($categorySelected) 
	{
		global $db;
		$get_result=$this->Select(TABLE_CATEGORY,"is_active=1","","category_name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$cat_data .= '<option value="'.$result['category_id'].'"';
				if($categorySelected == $result['category_id'] ) 
				$cat_data .= ' selected="selected"';
				$cat_data .= '>'.$result['category_name'].'</option>';
			}
		}
		return $cat_data;
	}
	function GetMainCategoryDDLink($categorySelected) 
	{
		global $db;
		$get_result=$this->Select(TABLE_CATEGORY,"is_active=1","","category_name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				if($result['section']=='QS') $section = "Quick Ship";
				else $section = "Catalog Items";
				$cat_data .= '<option value="'.$this->MakeUrl($this->mCurrentUrl,"cat_id=".$result['category_id']).'"';
				//$cat_data .= '<option value="'.$result['category_id'].'"';
				if($categorySelected == $result['category_id'] ) $cat_data .= ' selected="selected"';
				$cat_data .= '>'.stripslashes($result['category_name']).'&nbsp;['.stripslashes($section).']</option>';
			}
		}
		return $cat_data;
	}
	function GetMainAndSubCategoryDropDown($categorySelected) 
	{
		$get_result=$this->Select(TABLE_CATEGORY,"is_active=1","","category_name");	
					
		if(count($get_result)>0) 
		{
			foreach($get_result as $result) 
			{
				if($result['section']=='QS') $section = "Quick Ship";
				else $section = "Catalog Items";
				$subcat=$this->Select(TABLE_SUBCATEGORY,"is_active=1 and category_id=".$result['category_id'],"sub_categoryId,sub_categoryName","sub_categoryName");
				$cat_data .= '<optgroup label="'.htmlentities(stripslashes($result['category_name'])).'&nbsp;['.$section.']">';
				foreach($subcat as $arr)
				{					
					$cat_data .= '<option value="'.$this->MakeUrl($this->mCurrentUrl,"id=".$arr['sub_categoryId']).'"';
					if($categorySelected == $arr['sub_categoryId'] ) $cat_data .= ' selected="selected"';
					$cat_data .= '>'.$arr['sub_categoryName'].'</option>';
				}
			}
			$cat_data .= '</optgroup>';
		}
		return $cat_data;
	}
	function GetMainAndSubCategoryDropDown2($categorySelected) 
	{
		$get_result=$this->Select(TABLE_CATEGORY,"is_active=1","","category_name");						
		if(count($get_result)>0) 
		{
			foreach($get_result as $result) 
			{
				$subcat=$this->Select(TABLE_SUBCATEGORY,"is_active=1 and category_id=".$result['category_id'],"sub_categoryId,sub_categoryName","sub_categoryName");
				$cat_data .= '<optgroup label="'.$result['category_name'].'">';
				foreach($subcat as $arr)
				{			
					$cat_data .= '<option value="'.$arr['sub_categoryId'].'"';	
					if($categorySelected == $arr['sub_categoryId'] ) $cat_data .= ' selected="selected"';
					$cat_data .= '>'.$arr['sub_categoryName'].'</option>';
				}
			}
			$cat_data .= '</optgroup>';
		}
		return $cat_data;
	}
	function UploadCategoryImage($source) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			$new_file=uniqid('cat_');
			$destination = DIR_CATEGORY.$new_file.'.'.$ext;
			if(move_uploaded_file($source,$destination)) {
				//if($width>800 || $height>600)
					$this->CreateThumbHeight($new_file,$ext,DIR_CATEGORY,70,258,DIR_CATEGORY);
					$this->CreateThumbHeight($new_file,$ext,DIR_CATEGORY,100,100,DIR_CATEGORY_THUMBNAIL);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
	}
	function UploadSubCategoryImage($source) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			$new_file=uniqid('subcat_');
			$destination = DIR_SUBCATEGORY.$new_file.'.'.$ext;
			if(move_uploaded_file($source,$destination)) {
				if($width>800 || $height>600)
					$this->CreateThumb($new_file,$ext,DIR_SUBCATEGORY,800,600,DIR_SUBCATEGORY);
					$this->CreateThumb($new_file,$ext,DIR_SUBCATEGORY,200,133,DIR_SUBCATEGORY_THUMBNAIL);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
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
				if($width>800 || $height>600)
					$this->CreateThumb($new_file,$ext,DIR_PRODUCT,800,600,DIR_PRODUCT);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,176,175,DIR_PRODUCT_THUMBNAIL);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,190,135,DIR_PRODUCT_THUMBNAIL_PROMO);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,75,75,DIR_PRODUCT_SMALL);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
	}	
	
	function ImportProductImage($source) { 
		if(file_exists($source)) {
			list($width,$height,$ext)=getimagesize($source);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			$new_file=uniqid('pro_');
			$destination = DIR_PRODUCT.$new_file.'.'.$ext;
			if(copy($source,$destination)) {
				if($width>800 || $height>600)
					$this->CreateThumb($new_file,$ext,DIR_PRODUCT,800,600,DIR_PRODUCT);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,176,175,DIR_PRODUCT_THUMBNAIL);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,190,135,DIR_PRODUCT_THUMBNAIL_PROMO);
				$this->CreateThumb($new_file,$ext,DIR_PRODUCT,75,75,DIR_PRODUCT_SMALL);
				return $new_file.'.'.$ext;
			}
			else
				return false;
		} else {
			return false;
		}
	}	
	
	function GetManageList($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) {
		global $sort_query,$action_query;
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
				$page_content.= " 
					<tr height='25' class='$bg' onmouseover=this.className='tableover' onmouseout=this.className='".$bg."'> 
						<td align='center' valign='top' width='5%'  >".sprintf("%02d",$count)."</td>";		
				$field_count=0;
				$size_count=0;
				foreach($result as $key=>$value) {
					if($key!='id' && $key!='is_active'){ 
						$page_content.= " <td align='left' valign='top' style='padding-left:7px;' >";
						if($countList[$key]['table']!="") {
							$get_result=$this->Select($countList[$key]['table'],str_replace("[id]",$result['id'],$countList[$key]['condition']),$countList[$key]['column'].' as '.$key);
							if($get_result[0][$key]=="")
								$get_result[0][$key]="-";
							$page_content.= str_replace(array("[id]","[val]"),array($result['id'],$get_result[0][$key]),$countList[$key]['pattern']). "</td>";

						}else if($countList[$key]['custom']=='main_category_title') {
								$get_maincat=$this->Select(TABLE_CATEGORY,"category_id='".$result['Category']."'","category_name");
								$page_content.= ucfirst(strip_tags(stripslashes($get_maincat[0]['category_name'])))."</td>";
						}else if($countList[$key]['custom']=='maincat_name') {
								$get_maincat=$this->Select(TABLE_CATEGORY." as c LEFT JOIN ".TABLE_SUBCATEGORY." as sc ON c.category_id=sc.category_id","sc.sub_categoryId='".$result['Category']."'","c.category_name");
								$page_content.= ucfirst(strip_tags(stripslashes($get_maincat[0]['category_name'])))."</td>";
						}else if($countList[$key]['custom']=='subcat_name') {
								$get_subcat=$this->Select(TABLE_SUBCATEGORY,"sub_categoryId='".$result['Sub_Category']."'","sub_categoryName");
								$page_content.= ucfirst(strip_tags(stripslashes($get_subcat[0]['sub_categoryName'])))."</td>";
						}else if($countList[$key]['custom']=='promoprime_items') {
								$get_primeno=$this->Select(TABLE_PRODUCT,"product_id='".$result['Prime_Itam_No']."'","prime_item_no");
								$page_content.= ucfirst(strip_tags(stripslashes($get_primeno[0]['prime_item_no'])))."</td>";
						}else if($countList[$key]['custom']=='promocategory_name') {
								$get_subcat=$this->Select(TABLE_PRODUCT,"product_id='".$result['Category']."'","sub_categoryId");
								$get_maincat=$this->Select(TABLE_CATEGORY." as c LEFT JOIN ".TABLE_SUBCATEGORY." as sc ON c.category_id=sc.category_id","sc.sub_categoryId='".$get_subcat[0]['sub_categoryId']."'","c.category_name");
								$page_content.= ucfirst(strip_tags(stripslashes($get_maincat[0]['category_name'])))."</td>";
						}else if($countList[$key]['custom']=='promosubcategory_name') {
								$get_subcat=$this->Select(TABLE_SUBCATEGORY." as sc LEFT JOIN ".TABLE_PRODUCT." as p ON sc.sub_categoryId=p.sub_categoryId","p.product_id='".$result['Sub_Category']."'","sc.sub_categoryName");
								$page_content.= ucfirst(strip_tags(stripslashes($get_subcat[0]['sub_categoryName'])))."</td>";
						} else {
							$page_content.= stripslashes($result[$key]). "</td>";
						}
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
								
								if($_GET['sort']==$key) {
									$page_header.=' class="ban2" ';
									if($_GET['order']=='a') $order_q="&order=d"; else $order_q='&order=a';
								} else {
									$order_q='&order=a';
								}
								$page_header.=' ><a href="'.$this->MakeUrl($this->mCurrentUrl,"sort=".$key.$order_q.$sort_query).'" class="whitetext">'.str_replace("_"," ",$key).'';
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='<img src="'.SITE_URL.'images/up.gif" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='<img src="'.SITE_URL.'images/down.gif" border=0 align="absmiddle" />';
									
								$page_header.='</a></td>
									 ';
							}
							
							$size_count++;
						}
						$field_count++;
					}
				}
				$action_count=0;
				//$action_query="sort=".$_GET['sort']."&order=".$_GET['order']."&offset=".$_GET['offset']."&";
				foreach($listAction as $aKey => $aValue) {
					if($aValue['type']=='confirm') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".$aKey." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";	
					} elseif($aValue['type']=='condition') {
						if( $result[$aValue['conditionkey']]==$aValue['conditionvalue']) {
							$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".ucfirst($aKey)." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";
						}
					}elseif($aValue['type']=='category') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl("admin/manage_subcategories"."/".$aKey,$action_query.'cat_id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";															
					
					} elseif($aValue['type']=='products') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl("admin/manage_products"."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";
					} else {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";	
					}
					$keyArr[]=$aKey;
					$action_count++;
				}
				if($help_icon=="")
					$help_icon="<tr><td class='title3' colspan='".($action_count+$field_count)."' align='right'>".$this->GetHelpIcons($keyArr)."</td></tr>";
				if($is_page_header=="") {
					if($action_count>0) {
					$page_header.='
						<td width="10%" colspan="'.$action_count.'" align="center"  >Action</td>
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
	
	function GetManageContentList($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) 	  {
		global $sort_query,$action_query;
		if($countList=="") $countList=array();
		global $gStartPageNo;
		if(count($listArray)==0) {
			$page_content.= " 
					<tr align='center'>
						<td colspan='10' valign='top'   class='redheading'>No Result Found</td>
					</tr>
					<!--tr>
			            <td ><a href='manage_content/add' class='link_admin'>Add Content</a></td>
			        </tr-->
					";
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
						 <!--td width="4%"  align="center">#</td-->';
				}
				$page_content.= " 
					<tr height='25' class='$bg' onmouseover=this.className='tableover' onmouseout=this.className='".$bg."'> <!--td align='center' valign='top' width='5%'  >".sprintf("%02d",$count)."</td-->";		
				$field_count=0;
				$size_count=0;
				
				foreach($result as $key=>$value) {
					if($key!='id' && $key!='is_active'){ 
						$page_content.= " <td align='left' valign='top' style='padding-left:7px;' >";
						if($countList[$key]['table']!="") {
							$get_result=$this->Select($countList[$key]['table'],str_replace("[id]",$result['id'],$countList[$key]['condition']),$countList[$key]['column'].' as '.$key);
							//print_r($get_result);
							if($get_result[0][$key]=="")
								$get_result[0][$key]="-";
							$page_content.= str_replace(array("[id]","[val]"),array($result['id'],$get_result[0][$key]),$countList[$key]['pattern']). "</td>";
						} else {
							$page_content.= strip_tags(stripslashes($result[$key])). "</td>";
						}
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
								
								if($_GET['sort']==$key) {
									$page_header.=' class="ban2" ';
									if($_GET['order']=='a') $order_q="&order=d"; else $order_q='&order=a';
								} else {
									$order_q='&order=a';
								}
								$page_header.=' ><a href="'.$this->MakeUrl($this->mCurrentUrl,"sort=".$key.$order_q.$sort_query).'" class="text">'.str_replace("_"," ",$key).'';
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='<img src="'.SITE_URL.'images/up.gif" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='<img src="'.SITE_URL.'images/down.gif" border=0 align="absmiddle" />';
									
								$page_header.='</a></td>
									 ';
							}
							
							$size_count++;
						}
						$field_count++;
					}
				}
				$action_count=0;
				//$action_query="sort=".$_GET['sort']."&order=".$_GET['order']."&offset=".$_GET['offset']."&";
				foreach($listAction as $aKey => $aValue) {
					if($aValue['type']=='confirm') {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".$aKey." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";	
					} elseif($aValue['type']=='condition') {
						if( $result[$aValue['conditionkey']]==$aValue['conditionvalue']) {
							$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".ucfirst($aKey)." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";
						}
					
					} else {
						$page_content.= "
										<td width='3%' align='center' valign='middle' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";	
					}
					$keyArr[]=$aKey;
					$action_count++;
				}
				if($help_icon=="")
					$help_icon="<tr><td colspan='".($action_count+$field_count)."' align='left'>".$this->GetHelpIcons($keyArr)."</td></tr>";
				if($is_page_header=="") {
					if($action_count>0) {
					$page_header.='
						<td width="10%" colspan="'.$action_count.'" align="center"  >Action</td>
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
		
		//print_r($message); exit;
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
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
			/*echo "from". $arrValues['from'];
			echo "to".$arrValues['EMAIL'];*/
			//echo $message;  exit;
			return @mail($arrValues['EMAIL'],$subject,$message,$headers);
		}
	}
	function GetHelpIcons($keyArr)
	{
		$content.='<table border="0" align="right" cellpadding="1" cellspacing="4" class="text">
           <tr>';
		foreach($keyArr as $key)
		{
			$content.='
            <td align="center" valign="bottom"><img src="'.SITE_URL.'images/'.$key.'.gif" alt="'.ucfirst($key).'" title="'.ucfirst($key).'" border="0" /></td>
            ';
			$content2.='
			<td align="center" valign="top" style="padding-left:5px;" class="text">'.ucfirst($key).'</td>
           ';
		}
		$content.='</tr><tr>'.$content2.'</tr></table>';
		return $content;
	}
	//Abhi start
	function MakeFileDelete($path)
	{ 
		@unlink($path);
		return;
	}
	
	function DeleteCategory($id)
	{
		// Deleting Products of this category
		$pro_data=$this->Select(TABLE_SUBCATEGORY,"category_id='".$id."'","sub_categoryId");
		if(count($pro_data)>0) {
			foreach($pro_data as $data)
				$this->DeleteProductByCategory($data['sub_categoryId']);
		}
		// Deleting Sub-Category in this category
			$this->DeleteSubCategoryByCategory($id);
		// Deleting this Category with its images
		$cat_data=$this->Select(TABLE_CATEGORY,"category_id='".$id."'","category_image");
		if(count($cat_data)>0) {
			foreach($cat_data as $data) {
				@unlink(ROOT.DIR_CATEGORY_THUMBNAIL.$data['category_image']);
				@unlink(ROOT.DIR_CATEGORY.$data['category_image']);
			}
		}
		$this->Delete(TABLE_CATEGORY,"category_id='".$id."'");
		return;
	}
	function DeleteSubCategoryByCategory($id)
	{
		$subcat_data=$this->Select(TABLE_SUBCATEGORY,"category_id='".$id."'","sub_categoryImage");
		if(count($subcat_data)>0) {
			foreach($subcat_data as $data) {
				@unlink(ROOT.DIR_SUBCATEGORY_THUMBNAIL.$data['sub_categoryImage']);
				@unlink(ROOT.DIR_SUBCATEGORY.$data['sub_categoryImage']);
			}
		}
		$this->Delete(TABLE_SUBCATEGORY,"category_id='".$id."'");
	}
	function DeleteSubCategory($id)
	{
		$this->DeleteProductByCategory($id);
		$subcat_data=$this->Select(TABLE_SUBCATEGORY,"sub_categoryId='".$id."'","sub_categoryImage");
		if(count($subcat_data)>0) {
			foreach($subcat_data as $data) {
				@unlink(ROOT.DIR_SUBCATEGORY_THUMBNAIL.$data['sub_categoryImage']);
				@unlink(ROOT.DIR_SUBCATEGORY.$data['sub_categoryImage']);
			}
		}
		$this->Delete(TABLE_SUBCATEGORY,"sub_categoryId='".$id."'");
	}
	function DeleteProductByCategory($id)
	{
		$pro_data=$this->Select(TABLE_PRODUCT,"sub_categoryId='".$id."'","product_image");
		if(count($pro_data)>0) {
			foreach($pro_data as $data) {
				/*@unlink(ROOT.DIR_PRODUCT_THUMBNAIL.$data['product_image']);
				@unlink(ROOT.DIR_PRODUCT_SMALL.$data['product_image']);
				@unlink(ROOT.DIR_PRODUCT.$data['product_image']);*/
			}
		}
		$this->Delete(TABLE_PRODUCT,"sub_categoryId='".$id."'");
	}
	function DeleteProduct($id)
	{
		$link_data=$this->Select(TABLE_PRODUCT,"related_product_id='".$id."'","");
		if(count($link_data)>0) {
			foreach($link_data as $data) {
				$this->Update(TABLE_PRODUCT,array('related_product_id'=>'0'),"product_id='".$data['product_id']."'");
			}
		}
		$this->Delete(TABLE_PRODUCT,"product_id='".$id."'");
	}
	
	function GetCategoryDropDown($categorySelected,$arg,$parentId="") { 
		global $db;
		if($arg=="mix"){
			$get_maincat=$this->Select(TABLE_CATEGORY,"parent_id=0 and is_active='1'","*");
			foreach($get_maincat as $maincat_arr)
			{
				$get_subcat=$this->Select(TABLE_CATEGORY,"parent_id='".$maincat_arr['cat_id']."'","*");
				$category_data .= '<option style="background-color:#BFFFCF" value="'.$this->MakeUrl($this->mModuleUrl.'/index/','mcatid='.$maincat_arr['cat_id']).'"';
				if($categorySelected == $maincat_arr['cat_id'] ) $category_data .= ' selected="selected"';
				$category_data .= '>'.$maincat_arr['cat_name'].'</option>';
				foreach($get_subcat as $sub_cat)
				{
					$category_data .= '<option value="'.$this->MakeUrl($this->mModuleUrl.'/index/','mcatid='.$sub_cat['cat_id']).'"';
					if($categorySelected == $sub_cat['cat_id'] ) $category_data .= ' selected="selected"';
					$category_data .= '>&nbsp;&nbsp;&raquo;&nbsp;'.$sub_cat['cat_name'].'</option>';
				}
				
			}
		}
		if($arg=="main")
		{
			$condition = "parent_id=0  and is_active='1'";
			$get_result=$this->Select(TABLE_CATEGORY,$condition,"*"," parent_id");
		
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$category_data .= '<option value="'.$result['cat_id'].'"';
					if($categorySelected == $result['cat_id'] ) $category_data .= ' selected="selected"';
					$category_data .= '>'.$result['cat_name'].'</option>';
				}
			}
		}
		if($arg=="sub")
		{
			if($parentId!="")
			{
				$condition = "parent_id=".$parentId."  and is_active='1'";	
			}
			$get_result=$this->Select(TABLE_CATEGORY,$condition,"*"," parent_id");
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$category_data .= '<option value="'.$result['cat_id'].'"';
					if($categorySelected == $result['cat_id'] ) $category_data .= ' selected="selected"';
					$category_data .= '>'.$result['cat_name'].'</option>';
				}
			}
		}
		if($arg=="onchange_main")
		{
			$condition = "parent_id=0  and is_active='1'";
			$get_result=$this->Select(TABLE_CATEGORY,$condition,"*"," parent_id");
		
			if(count($get_result)>0) {
				foreach($get_result as $result) {
					$category_data .= '<option value="'.$this->MakeUrl($this->mModuleUrl.'/index/','mcatid='.$result['cat_id']).'"';
					if($categorySelected == $result['cat_id'] ) $category_data .= ' selected="selected"';
					$category_data .= '>'.$result['cat_name'].'</option>';
				}
			}
		}
				
		return $category_data;
	}
	
	
	function GetCategoryTree($categorySelected,$parentId="") { 
		if($parentId==""){
			$parentId=0;
		}
		$get_cat=$this->Select(TABLE_CATEGORY,"parent_id=".$parentId." and final_category=0 and is_active='1'","*");
		
		if(count($get_cat)>0){
			foreach($get_cat as $cat_arr)
			{	
				$category_data .= '<option  value="'.$this->MakeUrl($this->mModuleUrl.'/index/','mcatid='.$cat_arr['cat_id']).'"';
				if($categorySelected == $cat_arr['cat_id'] ) $category_data .= ' selected="selected"';
				$category_data .= '>'.$cat_arr['cat_name'].'</option>';
				$category_data .=$this->GetCategoryTree("",$cat_arr['cat_id']);
			}
			
		}
		return $category_data;
	}
	
	//Abhi End
	
	function Ajax_SubCategory($catId) {
	
		$get_result=$this->Select(TABLE_SUBCATEGORY,"category_id='".$catId."' AND is_active='1'","sub_categoryId,sub_categoryName","sub_categoryName");
		$data="";
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data.=$result['sub_categoryId']."@@@@".ucfirst(stripslashes($result['sub_categoryName']))."####";
			}
		}
		return $data;
	}
	function Ajax_SubCategoryChange($catId) {
		$get_result=$this->Select(TABLE_CATEGORY,"parent_id='".$catId."' AND is_active='1'","cat_id,cat_name","cat_name");
		$data="";
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data.=SITE_URL.'admin/manage_seeds/link/'.$result['cat_id']."@@@@".ucfirst($result['cat_name'])."####";
			}
		}
		return $data;
	}
	
	function Ajax_ProductPromo($catId) {
	
		$get_result=$this->Select(TABLE_PRODUCT,"sub_categoryId='".$catId."' AND is_active='1' AND is_promo='0' ","product_id,product_name","product_name");
		$data="";
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data.=$result['product_id']."@@@@".ucfirst($result['product_name'])."####";
			}
		}
		
		$get_result=$this->Select(TABLE_PRODUCT,"sub_categoryId='".$catId."' AND is_active='1' AND is_promo='1' ","product_id,product_name","product_name");
		if(count($get_result)>0) {
			$data.="!!!!";
			foreach($get_result as $result) { 
				$data.=$result['product_id']."@@@@".ucfirst($result['product_name'])."####";
			}
		}
		return $data;
	}
	
	function Ajax_CategoryHome($catId) {
		$category_data=$this->Select(TABLE_CATEGORY,"is_active=1 and category_id='".$catId."'","*","category_name");
		if(count($category_data)>0) {
		
			foreach($category_data as $data) {
				$categorydata.='<table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td rowspan="3" align="left" valign="top" width="114">
					  	<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="19" align="left" valign="top">&nbsp;</td>
                				<td width="95" align="left" valign="top" >';
								
					if($data['category_image']!="")
					$categorydata.='	<img id="tdimg" src="'.SITE_URL.DIR_CATEGORY.$data['category_image'].'"   />';
					
					$categorydata.='</td>
							</tr>
						</table>
					  </td>
                    </tr>
                    <tr>
                      <td height="20" align="left" valign="top" class="title3" style="padding-left:6px;">'.strtoupper(stripslashes($data['category_name'])).' </td>
                    </tr>
                    <tr>
                      <td height="88" align="left" valign="top" class="text2" style="line-height:150%;padding-left:6px;">'.substr(stripslashes($data['category_desc']),0,250).'</td>
                    </tr>
                    <tr>
                      <td align="left" valign="top">&nbsp;</td>
                      <td align="left" valign="top"></td>
                    </tr>
                </table>';
			
			}
		}
		return $categorydata;
		
	}
	function Ajax_ColorSelect($pid) {
		$get_colorids=$this->Select(TABLE_PRODUCT,"product_id='".$pid."' AND is_active='1'","product_color");
		$data="";
		if(count($get_colorids)>0) {
			$color_ids=explode('|',$get_colorids[0]['product_color']);
			for($i=0; $i<count($color_ids); $i++) { 
				$color_data=$this->Select(TABLE_COLORPALETTE,"color_id='".$color_ids[$i]."' AND is_active='1'","*");
				if(count($color_data)>0){
					foreach($color_data as $col_arr){
						$data.=$col_arr['color_id']."@@@@".ucfirst($col_arr['color_name'])."####".$col_arr['color_image']."!!!!";
					}
				}
			}
		}
		return $data;
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
	
	function UploadPDF($source, $prefix="", $Dir) {
		
			$prefix = $prefix."_";
			$new_file=uniqid($prefix);
			$destination = $Dir.$new_file . ".pdf";
			if(move_uploaded_file($source,$destination))
			 {
				return $new_file . '.pdf';
			 }
			else
				return false;
	}
	
	//extra//////////////////////////////////////////////////////////////
	function convertDate($date)
	{
		$months=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Agu','Sep','Oct','Nov','Dec');
		$temp=explode("-",$date);
		$year=$temp[0];
		$day=$temp[2];
		$offset=$temp[1]-1;
		$month=$months[$offset];
		return "$day $month, $year";
		
	}
	
	function GetColorDropDown($colorSelected) 
	{
		global $db;
		$get_result=$this->Select(TABLE_COLORPALETTE,"is_active='1'");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$color_data .= '<option value="'.$result['color_id'].'"';
				if(in_array($result['color_id'], $colorSelected)) 
				$color_data .= ' selected="selected"';
				$color_data .= '>'.$result['color_name'].'</option>';
			}
		}
		return $color_data;
	}	
	
	function GetSubCategoryDropDown($categorySelected,$maincatid) 
	{
		global $db;
		$get_result=$this->Select(TABLE_SUBCATEGORY,"is_active='1' and category_id='".$maincatid."'","","sub_categoryName");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$cat_data .= '<option value="'.$result['sub_categoryId'].'"';
				if($categorySelected == $result['sub_categoryId'] ) 
				$cat_data .= ' selected="selected"';
				$cat_data .= '>'.ucfirst(stripslashes($result['sub_categoryName'])).'</option>';
			}
		}
		return $cat_data;
	}
	
	function GetPromoItem_LeftDropDown($productSelected) 
	{
		global $db;
		$get_result=$this->Select(TABLE_PRODUCT,"is_active='1'");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$product_data .= '<option value="'.$result['product_id'].'"';
				if($result['product_id']==$productSelected) 
					$product_data .= ' selected="selected"';
				$product_data .= '>'.ucfirst($result['product_name']).'</option>';
			}
		}
		return $product_data;
	}
	function GetPromoItem_RightDropDown($productSelected) 
	{
		global $db;
		$get_result=$this->Select(TABLE_PRODUCT,"is_active='1'");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$product_data .= '<option value="'.$result['product_id'].'"';
				if(in_array($result['product_id'], $productSelected)) 
				$product_data .= ' selected="selected"';
				$product_data .= '>'.ucfirst($result['product_name']).'</option>';
			}
		}
		return $product_data;
	}	
	function CategoryMenu(){
		$menu=new Menu(165,18,"#A47B02","#FDDA6F",'#FFCC33',"#FFFFFF","#000000");
		
		$cat_arr=$this->Select(TABLE_CATEGORY,"section='QS' and is_active='1'","","category_name");
		
		if(count($cat_arr)>0) {
			foreach($cat_arr as $cat) {
				
				$scat_arr=$this->Select(TABLE_SUBCATEGORY,"is_active='1' && category_id='".$cat['category_id']."'","","sub_categoryName");
				if(count($scat_arr)>0) {
					$menu->AddMenu($cat['category_id'],addslashes(stripslashes(strtoupper($cat['category_name']))),165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");
					foreach($scat_arr as $scat) {
						$href="products/index/";
						$menu->AddMenuItem("text",$cat['category_id'],strtoupper(addslashes(stripslashes($scat['sub_categoryName']))),$this->MakeUrl($href,"id=".$scat['sub_categoryId']));
					}
				}
			}
		}
		if(count($cat_arr)>0) {
			$menu->AddMenu(0,"root",165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");
			foreach($cat_arr as $cat) {
				$href="javascript: void(0);";
				$scat_arr=$this->Select(TABLE_SUBCATEGORY,"is_active='1' && category_id='".$cat['category_id']."'","","sub_categoryName");
				if(count($scat_arr)>0) {
					$menu->AddMenuItem("menu",0,$cat['category_id'],"javascript: void(0);");
				}else {
					$menu->AddMenuItem("text",0,strtoupper(addslashes(stripslashes($cat['category_name']))),"javascript: void(0);");
				}
			}
		}
		
		$cat_catalog_arr=$this->Select(TABLE_CATEGORY,"section='CAT' and is_active='1'","","category_name");
		if(count($cat_catalog_arr)>0) {
			foreach($cat_catalog_arr as $cat) {
				
				$scat_arr=$this->Select(TABLE_SUBCATEGORY,"is_active='1' && category_id='".$cat['category_id']."'","","sub_categoryName");
				if(count($scat_arr)>0) {
					$menu->AddMenu($cat['category_id'],strtoupper(addslashes(stripslashes($cat['category_name']))),165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");
					foreach($scat_arr as $scat) {
						$href="catalogproducts/index/";
						$menu->AddMenuItem("text",$cat['category_id'],strtoupper(addslashes(stripslashes($scat['sub_categoryName']))),$this->MakeUrl($href,"id=".$scat['sub_categoryId']));
					}
				}
			}
		}
		if(count($cat_catalog_arr)>0) {
			$menu->AddMenu('catalog',"root",165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");
			foreach($cat_catalog_arr as $cat) {
				$href="javascript: void(0);";
				$scat_arr=$this->Select(TABLE_SUBCATEGORY,"is_active='1' && category_id='".$cat['category_id']."'","","sub_categoryName");
				if(count($scat_arr)>0) {
					$menu->AddMenuItem("menu",'catalog',$cat['category_id'],"javascript: void(0);");
				}else {
					$menu->AddMenuItem("text",'catalog',strtoupper(addslashes(stripslashes($cat['category_name']))),"javascript: void(0);");
				}
			}
		}

		
			$menu->AddMenu('locker',"root",165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");
			$menu->AddMenuItem("text",'locker',"METAL LOCKERS",$this->MakeUrl('static/metal_lockers/'));
			$menu->AddMenuItem("text",'locker',"WOOD LOCKERS",$this->MakeUrl('static/wood_lockers/'));
			$menu->AddMenuItem("text",'locker',"PLASTIC LOCKERS",$this->MakeUrl('static/plastic_lockers/'));
			$menu->AddMenuItem("text",'locker',"PHENOLIC LOCKERS",$this->MakeUrl('static/phenolic_lockers/'));
			$menu->AddMenuItem("text",'locker',"CUSTOM LOCKERS",$this->MakeUrl('static/custom_lockers/'));
			$menu->AddMenuItem("text",'locker',"STAINLESS LOCKERS",$this->MakeUrl('static/stainless_lockers/'));
			$menu->AddMenuItem("text",'locker',"ACCESSORIES",$this->MakeUrl('static/accessories/'));
			$menu->AddMenuItem("text",'locker',"TECHNICAL SPECS",$this->MakeUrl('static/technical_specs/'));
			
			
			$menu->AddMenu('subcat',"root",165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");
			$menu->AddMenuItem("text",'subcat',"METAL LOCKERS",$this->MakeUrl('static/metal_lockers/'));
			$menu->AddMenuItem("text",'subcat',"WOOD LOCKERS",$this->MakeUrl('static/wood_lockers/'));
			//-----------------------------------------------------------------------------------------------//
			//-------------------------Sub Menu Category Navigtion-------------------------------------------//
			//-----------------------------------------------------------------------------------------------//
			if($this->mPageName=='catalogproducts' || $this->mPageName=='requestaquote')
				$condition = "section='CAT' and ";
			else
				$condition = "section='QS' and ";
			$subhead_catdata=$this->Select(TABLE_CATEGORY,$condition."is_active=1","*","category_name");
			if(count($subhead_catdata)>0){
				$uppend1=array();
				$uppend2=array();
				$uppend3=array();
				$vmenu_height=3;
				foreach($subhead_catdata as $mcat_arr){
					$subhead_subcatdata=$this->Select(TABLE_SUBCATEGORY,"is_active=1 and category_id='".$mcat_arr['category_id']."'",'','sub_categoryName');
					if(count($subhead_subcatdata)>0){
						$menu->AddMenu('subhead_'.$mcat_arr['category_id'],"root",165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");	
						foreach($subhead_subcatdata as $scat_arr){
							if($this->mPageName=='catalogproducts' || $this->mPageName=='requestaquote')
								$href="catalogproducts/index/";
							else
								$href="products/index/";
							$menu->AddMenuItem("text",'subhead_'.$mcat_arr['category_id'],strtoupper(addslashes(stripslashes($scat_arr['sub_categoryName']))),$this->MakeUrl($href,"id=".$scat_arr['sub_categoryId']));				
						}
						$uppend1 = $uppend1 + array('subhead_'.$mcat_arr['category_id']=>160);
						$uppend2 = $uppend2 + array('subhead_'.$mcat_arr['category_id']=>$vmenu_height);
						$uppend3 = $uppend3 + array('subhead_'.$mcat_arr['category_id']=>'link_subhead_'.$mcat_arr['category_id']);
						
					}
					//$vmenu_height += 30;
				}
			}
			
			//-----------------------------------------------------------------------------------------------//
			//-------------------------Sub Menu Category for Secondary Menu On Home Page---------------------//
			//-----------------------------------------------------------------------------------------------//
			
			
			$condition1 = "section='CAT' and ";
			$subhead_catdata_home=$this->Select(TABLE_CATEGORY,$condition1."is_active=1","*","category_name");
			if(count($subhead_catdata_home)>0){
				$homeuppend1=array();
				$homeuppend2=array();
				$homeuppend3=array();
				$height=3;
				foreach($subhead_catdata_home as $mcat_arr_home){
					$subhead_subcatdata_home=$this->Select(TABLE_SUBCATEGORY,"is_active=1 and category_id='".$mcat_arr_home['category_id']."'",'','sub_categoryName');
					if(count($subhead_subcatdata_home)>0){ 
						$menu->AddMenu('home_'.$mcat_arr_home['category_id'],"root",165,18,"#970093","#FFB3FD",'#227095',"#FFFFFF","#000000");	
						foreach($subhead_subcatdata_home as $scat_arr_home){ 
							$href="catalogproducts/index/";
							$menu->AddMenuItem("text",'home_'.$mcat_arr_home['category_id'],strtoupper(addslashes(stripslashes($scat_arr_home['sub_categoryName']))),$this->MakeUrl($href,"id=".$scat_arr_home['sub_categoryId']));				
						}
						$homeuppend1 = $homeuppend1 + array('home_'.$mcat_arr_home['category_id']=>240);
						$homeuppend2 = $homeuppend2 + array('home_'.$mcat_arr_home['category_id']=>$height);
						$homeuppend3 = $homeuppend3 + array('home_'.$mcat_arr_home['category_id']=>'link_home_'.$mcat_arr_home['category_id']);
					}
					//$height += 60;
				} 
			}
			
		return array('function'=>$menu->GetMenuFunction(),
					'call'=>$menu->GetMenuCall((array('0'=>0, 'catalog'=>0 ,'locker'=>0)+$uppend1+$homeuppend1),
					(array('0'=>18, 'catalog'=>18,'locker'=>18,'subhead'=>5,'home'=>5)+$uppend2+$homeuppend2),
					(array('0'=>'link_store', 'catalog'=>'link_catalog', 'locker'=>'link_lockerdetails', 'subhead'=>'link_subhead','home'=>'link_home')+$uppend3+$homeuppend3)));
	}
	
	function GetHeaderContents() {
		global $menu_data;
		
		if($this->mPageName=='catalogproducts' || $this->mPageName=='requestaquote')
			$condition = "section='CAT' and ";
		else
			$condition = "section='QS' and ";
		$category_data=$this->Select(TABLE_CATEGORY,$condition."is_active=1","*","category_name",6);
		if(count($category_data)>0) {
		
			foreach($category_data as $data) {
				$header_data.='
				<tr>
                      <td height="28" align="left" valign="top" background="'.SITE_URL.'images/inner/link_bg.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="15%" align="left" valign="middle">&nbsp;</td>
                          <td width="85%" height="28" align="left" valign="middle"  onmouseover="'.$menu_data['call']['subhead_'.$data['category_id']].' this.className=\'subnavigation_link_hover\';" onmouseout="MM_startTimeout(); this.className=\'subnavigation_link\';" title="'.strtoupper(htmlentities(stripslashes($data['category_name']))).'" class="subnavigation_link" id="link_subhead_'.$data['category_id'].'">'.strtoupper(substr(stripslashes($data['category_name']),0,20)).'</td>
                        </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td height="2" align="left" valign="top"></td>
                    </tr>
				';
			
			}
		}
		return $header_data;
	}
	
	function OrderEmail_old($orderId,$subjectType='new') {
		$order_data=$this->Select(TABLE_CART_ORDER,"cart_order_id='".$orderId."'","*");
		if(count($order_data)>0) {
			foreach($order_data as $arr) {
				$data_arr=$arr;
				$product_data=$this->Select(TABLE_CART_INVOICE,"cart_order_id='".$orderId."'","*");
				$i=1; 
				if(count($product_data)>0) {
					foreach($product_data as $product) {
					
						if($product['is_assembled']==1){ 
							$assembly_price  = $product['assembly_price'];
							
							//echo $assembly_price."<br>";
							$amt = (($product['price'] * $product['quantity']) + ($assembly_price * $product['quantity']));
							$assembly_price  = sprintf("$%01.2f",$assembly_price);
							$shipment_class = "Assembled";
						//	echo $amt; exit;
						} else { 
							$amt = ($product['price'] * $product['quantity']);
							$assembly_price  = 'N/A';
							$shipment_class = "Unassembled";
						}
						
						//$assembly_price  = $product['assembly_price'];
						$prod_data.='
						 <tr class="text">
								  <td align="center" valign="top" >'.$i.'</td>
								  <td style="padding-left:5px;" valign="top" align="left">'.nl2br(strip_tags(str_replace("<br />","\n",stripslashes($product['product_name'])))).' ('.$product['color_id'].')</td>
								  <td style="padding-right:5px;"  valign="top" align="right">'.$shipment_class.'</td>
								  <td style="padding-right:5px;"  valign="top" align="right">'.sprintf("%03d",$product['quantity']).'</td>
								  <td style="padding-right:3px;"  valign="top" align="right">'.$product['unit_weight'].' lbs</td>
								  <td style="padding-right:3px;"  valign="top" align="right">'.sprintf("$%01.2f",$product['price']).'</td>
								  <td style="padding-right:2px;"  valign="top" align="right">'.$assembly_price.'</td>
								  <td style="padding-right:10px;"  valign="top" align="right">'.sprintf("$%01.2f",$amt).'</td>
								</tr>
						';
						$weight_total+=($product['quantity']*$product['unit_weight']);
						$i++;
					}
				}
			}
		}
		if($subjectType=='' || $subjectType=='new') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Confirmation from ".html_entity_decode(SITE_NAME);
			$data_arr['comments']="-";
		} elseif($subjectType=='status') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Status from ".html_entity_decode(SITE_NAME);
		}
	
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
				</style>
				<table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#D6D6D6" align="center">
				  <tr>
					<td colspan="2" align="left" ><img src="'.SITE_URL.'images/inner/order_header.jpg" /></td>
				  </tr>
				  <tr>
					<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #000000">
						<tr>
						  <td colspan="2" align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">&nbsp;</td>
						</tr>
						<tr>
						  <td colspan="2" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2" >
							  <tr>
								<td width="20%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Order #: </td>
								<td width="80%" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><strong>#FL-'.sprintf("%04d",$data_arr['cart_order_id']).'</strong></td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Order Date:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.date('jS M,Y',strtotime($data_arr['order_date'])).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Status:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.strtoupper($data_arr['status']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Comments:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">'.nl2br(stripslashes($data_arr['comments'])).'</td>
							  </tr>';
					if($subjectType=='new') {
					  $order_message .='<tr>
										<td colspan="2" style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Click <a href="'.$this->MakeUrl("order_activate/index/","order_id=".$data_arr['cart_order_id']).'" class="">here</a> to confirm your order.</td></tr>';
					}
					
					$order_message .='</table></td>
						</tr>
						<tr>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
						</tr>
						<tr>
						  <td align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073"><strong>Billing Information</strong></td>
						  <td align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073"><strong>Shipping Information</strong></td>
						</tr>
						<tr>
						  <td align="right" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2">
							  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><label for="txtusername"> Name: </label></td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['bill_name']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['bill_phone'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_address']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_state']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_city']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_zipcode']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">E-Mail:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><a href="mailto:'.stripslashes($data_arr['e_mail']).'" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['e_mail']).'</a></td>
							  </tr>
							</table></td>
						  <td align="left" style="font-family:verdana; font-size:11px; color:#000000;" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
							  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Name: </td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['ship_name']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['ship_phone'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_address']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_state']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['ship_city'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_zipcode']).'</td>
							  </tr>
							</table></td>
						</tr>
						<tr>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
						</tr>
						<tr>
						  <td colspan="2" align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">Products Ordered </td>
						</tr>
						<tr>
							<td height="2"></td>
						</tr>
						<tr>
						  <td colspan="2" align="right" style="font-family:verdana; font-size:11px; color:#000000;">
						  	<table width="99%" align="center" border="0" cellspacing="1" cellpadding="1" style="font-family:verdana; font-size:11px; color:#000000;">
							  <tr style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">
								<td align="center" width="2%">#</td>
								<td style="padding-left:2px;" align="left" width="22%">Product Name </td>
								<td style="padding-right:2px;" width="17%" align="right">Shipment Class</td>
								<td style="padding-right:2px;" width="4%" align="right">Qty</td>
								<td style="padding-right:2px;" width="13%" align="right">Unit Weight</td>
								<td style="padding-right:2px;" width="12%" align="right">Unit Price</td>
								<td style="padding-right:2px;" width="17%" align="right">Assembly Price</td>
								<td style="padding-right:2px;" align="right"  >Amount</td>
							  </tr>
							  '.$prod_data.'
							  <tr>
								<td colspan="8"><hr color="#000000" size="1" /></td>
							  </tr>
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="2"><strong>Weight:</strong></td>
								<td style="padding-right:10px;" align="right">
								  '.$data_arr['weight_total'].' lbs</td>
								<td align="right" colspan="2"><strong>Sub Total:</strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.$data_arr['subtotal'].'</td>
							  </tr>
							  
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="3"><strong>Shipping:</strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.$data_arr['total_ship'].'</td>
							  </tr>
							 
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="3"><strong>Total Amount: </strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.($data_arr['subtotal']+$data_arr['total_ship']).'</td>
							  </tr>
							  
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td colspan="2">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							  </tr>
							</table></td>
						</tr>
					  </table></td>
				  </tr>
				 
				</table>
				';
			echo $order_message; exit;
			$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
		//	$headers = 'From: '.SITE_NAME.' <' . NOREPLY_EMAIL . '>' . "\r\n";
			//if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
			$headers .= 'Bcc: ' . $this->GetAdminEmail() . " \r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
			return @mail($data_arr['e_mail'],$order_subject,$order_message,$headers);
	}
	
	function OrderEmail($orderId,$subjectType='new') {
		$order_data=$this->Select(TABLE_CART_ORDER,"cart_order_id='".$orderId."'","*");
		if(count($order_data)>0) {
			foreach($order_data as $arr) {
				$data_arr=$arr;
				$product_data=$this->Select(TABLE_CART_INVOICE,"cart_order_id='".$orderId."'","*");
				$i=1; 
				if(count($product_data)>0) {
					foreach($product_data as $product) {
					
						if($product['is_assembled']==1){ 
							$assembly_price  = $product['assembly_price'];
							
							//echo $assembly_price."<br>";
							$amt = (($product['price'] * $product['quantity']) + ($assembly_price * $product['quantity']));
							$assembly_price  = sprintf("$%01.2f",$assembly_price);
							$shipment_class = "Assembled";
						//	echo $amt; exit;
						} else { 
							$amt = ($product['price'] * $product['quantity']);
							$assembly_price  = 'N/A';
							$shipment_class = "Unassembled";
						}
						
						//$assembly_price  = $product['assembly_price'];
						$prod_data.='
						 <tr class="text">
								  <td align="center" valign="top" >'.$i.'</td>
								  <td style="padding-left:5px;" valign="top" align="left">'.nl2br(strip_tags(str_replace("<br />","\n",stripslashes($product['product_name'])))).' ('.$product['color_id'].')</td>
								  <td style="padding-right:5px;"  valign="top" align="right">'.$shipment_class.'</td>
								  <td style="padding-right:5px;"  valign="top" align="right">'.sprintf("%03d",$product['quantity']).'</td>
								  <td style="padding-right:3px;"  valign="top" align="right">'.$product['unit_weight'].' lbs</td>
								  <td style="padding-right:3px;"  valign="top" align="right">'.sprintf("$%01.2f",$product['price']).'</td>
								  <td style="padding-right:2px;"  valign="top" align="right">'.$assembly_price.'</td>
								  <td style="padding-right:10px;"  valign="top" align="right">'.sprintf("$%01.2f",$amt).'</td>
								</tr>
						';
						$weight_total+=($product['quantity']*$product['unit_weight']);
						$i++;
					}
				}
			}
		}
		if($subjectType=='' || $subjectType=='new') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Confirmation from ".html_entity_decode(SITE_NAME);
			$data_arr['comments']="-";
		} elseif($subjectType=='status') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Status from ".html_entity_decode(SITE_NAME);
		} elseif($subjectType=='activate') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Activation from ".html_entity_decode(SITE_NAME);
		}
	
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
				</style>
				<table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#D6D6D6" align="center">
				  <tr>
					<td colspan="2" align="left" ><img src="'.SITE_URL.'images/inner/order_header.jpg" /></td>
				  </tr>
				  <tr>
					<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #000000">
						<tr>
						  <td colspan="2" align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">&nbsp;</td>
						</tr>
						<tr>
						  <td colspan="2" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2" >
							  <tr>
								<td width="20%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Order #: </td>
								<td width="80%" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><strong>#FL-'.sprintf("%04d",$data_arr['cart_order_id']).'</strong></td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Order Date:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.date('jS M,Y',strtotime($data_arr['order_date'])).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Status:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.strtoupper($data_arr['status']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Comments:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">'.nl2br(stripslashes($data_arr['comments'])).'</td>
							  </tr>';
							  if($subjectType=='new') {
							  $order_message .='<tr height="25" style="background-color:#CCCCCC">
												<td valign="middle" colspan="2" style="font-family:verdana; font-size:11px; color:#000000;" align="left"><a href="'.$this->MakeUrl("order_activate/index/","order_id=".$data_arr['cart_order_id']).'" style="font-family:verdana; font-size:11px; color:#000000;">Click here to confirm your order.</a></td>
											  </tr>';
							  } else if($subjectType=='activate') {
							  $order_message .='<tr height="25" style="background-color:#CCCCCC">
													<td colspan="2" style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="middle"><strong>Your Order has been activated successfully. </strong></td>
											  	  </tr>';
							  }
			$order_message.='</table></td>
						</tr>
						<tr>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
						</tr>
						<tr>
						  <td align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073"><strong>Billing Information</strong></td>
						  <td align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073"><strong>Shipping Information</strong></td>
						</tr>
						<tr>
						  <td align="right" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2">
							  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><label for="txtusername"> Name: </label></td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['bill_name']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['bill_phone'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_address']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_state']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_city']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_zipcode']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">E-Mail:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><a href="mailto:'.stripslashes($data_arr['e_mail']).'" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['e_mail']).'</a></td>
							  </tr>
							</table></td>
						  <td align="left" style="font-family:verdana; font-size:11px; color:#000000;" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
							  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Name: </td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['ship_name']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['ship_phone'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_address']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_state']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['ship_city'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_zipcode']).'</td>
							  </tr>
							</table></td>
						</tr>
						<tr>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
						</tr>
						<tr>
						  <td colspan="2" align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">Products Ordered </td>
						</tr>
						<tr>
							<td height="2"></td>
						</tr>
						<tr>
						  <td colspan="2" align="right" style="font-family:verdana; font-size:11px; color:#000000;">
						  	<table width="99%" align="center" border="0" cellspacing="1" cellpadding="1" style="font-family:verdana; font-size:11px; color:#000000;">
							  <tr style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">
								<td align="center" width="2%">#</td>
								<td style="padding-left:2px;" align="left" width="22%">Product Name </td>
								<td style="padding-right:2px;" width="17%" align="right">Shipment Class</td>
								<td style="padding-right:2px;" width="4%" align="right">Qty</td>
								<td style="padding-right:2px;" width="13%" align="right">Unit Weight</td>
								<td style="padding-right:2px;" width="12%" align="right">Unit Price</td>
								<td style="padding-right:2px;" width="17%" align="right">Assembly Price</td>
								<td style="padding-right:2px;" align="right"  >Amount</td>
							  </tr>
							  '.$prod_data.'
							  <tr>
								<td colspan="8"><hr color="#000000" size="1" /></td>
							  </tr>
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="2"><strong>Weight:</strong></td>
								<td style="padding-right:10px;" align="right">
								  '.$data_arr['weight_total'].' lbs</td>
								<td align="right" colspan="2"><strong>Sub Total:</strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.$data_arr['subtotal'].'</td>
							  </tr>
							  
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="3"><strong>Shipping:</strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.$data_arr['total_ship'].'</td>
							  </tr>
							 
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="3"><strong>Total Amount: </strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.($data_arr['subtotal']+$data_arr['total_ship']).'</td>
							  </tr>
							  
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td colspan="2">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							  </tr>
							</table></td>
						</tr>
					  </table></td>
				  </tr>
				 
				</table>
				';
			//echo $order_message; exit;
			$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
			//$headers = 'From: '.SITE_NAME.' <' . NOREPLY_EMAIL . '>' . "\r\n";
			//if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
			//$headers .= 'Bcc: ' . $this->GetAdminEmail() . " \r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
			return @mail($data_arr['e_mail'],$order_subject,$order_message,$headers);
	}
	
	function OrderAdminEmail($orderId,$subjectType='new') {
		$order_data=$this->Select(TABLE_CART_ORDER,"cart_order_id='".$orderId."'","*");
		if(count($order_data)>0) {
			foreach($order_data as $arr) {
				$data_arr=$arr;
				$product_data=$this->Select(TABLE_CART_INVOICE,"cart_order_id='".$orderId."'","*");
				$i=1; 
				if(count($product_data)>0) {
					foreach($product_data as $product) {
					
						if($product['is_assembled']==1){ 
							$assembly_price  = $product['assembly_price'];
							
							//echo $assembly_price."<br>";
							$amt = (($product['price'] * $product['quantity']) + ($assembly_price * $product['quantity']));
							$assembly_price  = sprintf("$%01.2f",$assembly_price);
							$shipment_class = "Assembled";
						//	echo $amt; exit;
						} else { 
							$amt = ($product['price'] * $product['quantity']);
							$assembly_price  = 'N/A';
							$shipment_class = "Unassembled";
						}
						
						//$assembly_price  = $product['assembly_price'];
						$prod_data.='
						 <tr class="text">
								  <td align="center" valign="top" >'.$i.'</td>
								  <td style="padding-left:5px;" valign="top" align="left">'.nl2br(strip_tags(str_replace("<br />","\n",stripslashes($product['product_name'])))).' ('.$product['color_id'].')</td>
								  <td style="padding-right:5px;"  valign="top" align="right">'.$shipment_class.'</td>
								  <td style="padding-right:5px;"  valign="top" align="right">'.sprintf("%03d",$product['quantity']).'</td>
								  <td style="padding-right:3px;"  valign="top" align="right">'.$product['unit_weight'].' lbs</td>
								  <td style="padding-right:3px;"  valign="top" align="right">'.sprintf("$%01.2f",$product['price']).'</td>
								  <td style="padding-right:2px;"  valign="top" align="right">'.$assembly_price.'</td>
								  <td style="padding-right:10px;"  valign="top" align="right">'.sprintf("$%01.2f",$amt).'</td>
								</tr>
						';
						$weight_total+=($product['quantity']*$product['unit_weight']);
						$i++;
					}
				}
			}
		}
		if($subjectType=='' || $subjectType=='new') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Confirmation from ".html_entity_decode(SITE_NAME);
			$data_arr['comments']="-";
		} elseif($subjectType=='status') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Status from ".html_entity_decode(SITE_NAME);
		} elseif($subjectType=='activate') {
			$order_subject="Order #FL-".sprintf("%04d",$data_arr['cart_order_id'])." Activation from ".html_entity_decode(SITE_NAME);
		}
	
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
				</style>
				<table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#D6D6D6" align="center">
				  <tr>
					<td colspan="2" align="left" ><img src="'.SITE_URL.'images/inner/order_header.jpg" /></td>
				  </tr>
				  <tr>
					<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #000000">
						<tr>
						  <td colspan="2" align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">&nbsp;</td>
						</tr>
						<tr>
						  <td colspan="2" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2" >
							  <tr>
								<td width="20%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Order #: </td>
								<td width="80%" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><strong>#FL-'.sprintf("%04d",$data_arr['cart_order_id']).'</strong></td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Order Date:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.date('jS M,Y',strtotime($data_arr['order_date'])).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">Status:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.strtoupper($data_arr['status']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">Comments:</td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="top">'.nl2br(stripslashes($data_arr['comments'])).'</td>
							  </tr>';
							 if($subjectType=='activate') {
							  	$order_message .='<tr height="25" style="background-color:#CCCCCC">
													<td colspan="2" style="font-family:verdana; font-size:11px; color:#000000;" align="left" valign="middle"><strong>This Order has been activated successfully. You can proceed to ship the order.</strong></td>
											  	  </tr>';
							  }
			$order_message.='</table></td>
						</tr>
						<tr>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
						</tr>
						<tr>
						  <td align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073"><strong>Billing Information</strong></td>
						  <td align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073"><strong>Shipping Information</strong></td>
						</tr>
						<tr>
						  <td align="right" style="font-family:verdana; font-size:11px; color:#000000;"><table width="100%" border="0" cellspacing="2" cellpadding="2">
							  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;"><label for="txtusername"> Name: </label></td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['bill_name']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['bill_phone'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_address']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_state']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_city']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['bill_zipcode']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">E-Mail:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><a href="mailto:'.stripslashes($data_arr['e_mail']).'" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['e_mail']).'</a></td>
							  </tr>
							</table></td>
						  <td align="left" style="font-family:verdana; font-size:11px; color:#000000;" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
							  <tr>
								<td width="29%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">Name: </td>
								<td width="71%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.stripslashes($data_arr['ship_name']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtphone1">Phone No.:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['ship_phone'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtaddress">Address:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_address']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtstate">State:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_state']).'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtcity">City:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.$data_arr['ship_city'].'</td>
							  </tr>
							  <tr>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left"><label for="txtzip_code">Zip Code:</label></td>
								<td style="font-family:verdana; font-size:11px; color:#000000;" align="left">'.stripslashes($data_arr['ship_zipcode']).'</td>
							  </tr>
							</table></td>
						</tr>
						<tr>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
						</tr>
						<tr>
						  <td colspan="2" align="center" style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">Products Ordered </td>
						</tr>
						<tr>
							<td height="2"></td>
						</tr>
						<tr>
						  <td colspan="2" align="right" style="font-family:verdana; font-size:11px; color:#000000;">
						  	<table width="99%" align="center" border="0" cellspacing="1" cellpadding="1" style="font-family:verdana; font-size:11px; color:#000000;">
							  <tr style="font-family:verdana; font-size:11px; font-weight:bold; color:#FFFFFF; padding-left:10px; height:22px; background-color:#750073">
								<td align="center" width="2%">#</td>
								<td style="padding-left:2px;" align="left" width="22%">Product Name </td>
								<td style="padding-right:2px;" width="17%" align="right">Shipment Class</td>
								<td style="padding-right:2px;" width="4%" align="right">Qty</td>
								<td style="padding-right:2px;" width="13%" align="right">Unit Weight</td>
								<td style="padding-right:2px;" width="12%" align="right">Unit Price</td>
								<td style="padding-right:2px;" width="17%" align="right">Assembly Price</td>
								<td style="padding-right:2px;" align="right"  >Amount</td>
							  </tr>
							  '.$prod_data.'
							  <tr>
								<td colspan="8"><hr color="#000000" size="1" /></td>
							  </tr>
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="2"><strong>Weight:</strong></td>
								<td style="padding-right:10px;" align="right">
								  '.$data_arr['weight_total'].' lbs</td>
								<td align="right" colspan="2"><strong>Sub Total:</strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.$data_arr['subtotal'].'</td>
							  </tr>
							  
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="3"><strong>Shipping:</strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.$data_arr['total_ship'].'</td>
							  </tr>
							 
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="3"><strong>Total Amount: </strong></td>
								<td style="padding-right:10px;" align="right">$
								  '.($data_arr['subtotal']+$data_arr['total_ship']).'</td>
							  </tr>
							  
							  <tr>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td colspan="2">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							  </tr>
							</table></td>
						</tr>
					  </table></td>
				  </tr>
				 
				</table>
				';
			//echo $order_message; exit;
			$cc = CC_EMAIL;
			$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
			//$headers = 'From: '.SITE_NAME.' <' . NOREPLY_EMAIL . '>' . "\r\n";
			if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
			//$headers .= 'Bcc: ' . $this->GetAdminEmail() . " \r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
			/*echo $order_message;
			echo "<br>".$headers; exit;*/
			return @mail($this->GetAdminEmail(),$order_subject,$order_message,$headers);
	}
	
	function getShippingInfo_old($product_name,$weight_total,$dayton_frieght,$shipping_class) {  
	
		include("includes/xml2array.php");
		include("includes/new_domxml.php"); 
		
		//echo "Product Name=".$product_name;
		
		
		if($dayton_frieght==0) { 
			/*$content='<?xml version="1.0"?><ECommerce action="Request" version="1.1">
				<Requestor>
					<ID>FL_8124</ID>
					<Password>K82A475LM6</Password>
				</Requestor>
			 <Shipment action = "RateEstimate" version = "1.0">
				<ShippingCredentials>
					<ShippingKey>56233F2B2C4155464C59505A5C543053474855444C5D54</ShippingKey>
					<AccountNbr>804973696</AccountNbr>
				</ShippingCredentials>
				<ShipmentDetail>
					<ShipDate>'.date('Y-m-d',time()+86400).'</ShipDate>
					<Service>
						<Code>G</Code>
					</Service>
					<ShipmentType>
						<Code>P</Code>
					</ShipmentType>
					<Weight>'.$weight_total.'</Weight>
					<ContentDesc>'.$product_name.'</ContentDesc>
					<AdditionalProtection>
						<Code>NR</Code>
						<Value></Value>
					</AdditionalProtection>
				</ShipmentDetail>
				<Billing>
					<Party>
						<Code>S</Code>
					</Party>
					<AccountNbr></AccountNbr>
				</Billing>
				<Sender>
					<SentBy>Famous Locker</SentBy>
					<PhoneNbr>206-206-2062</PhoneNbr>
				</Sender>
				<Receiver>
					<Address>
						<CompanyName>'.$_POST['txtsname'].'</CompanyName>
						<Street>'.$_POST['txtsaddress'].'</Street>
						<City>'.$_POST['txtscity'].'</City>
						<State>'.$_POST['txtsstate'].'</State>
						<PostalCode>'.$_POST['txtszip'].'</PostalCode>
						<Country>US</Country>
					</Address>
					<AttnTo>'.$_POST['txtsname'].'</AttnTo>
					<PhoneNbr>'.$_POST['txtsphone'].'</PhoneNbr>
				</Receiver>
				<ShipmentProcessingInstructions>
					<Label>
						<ImageType>JPEG</ImageType>
					</Label>
				</ShipmentProcessingInstructions>
			</Shipment>
			</ECommerce>
			'; 
			
			$head[]="Connection: Keep-Alive";  
			
			$ch=curl_init("HTTPS://eCommerce.airborne.com/ApiLandingTest.asp"); 
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
			curl_setopt($ch,CURLOPT_HTTPPOST,1);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$content);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		
			$result = curl_exec ($ch); 
			$xml_obj = new xml2array($result); 
			$result_arr = $xml_obj->getResult();
			 
		//echo $result_arr['ECommerce']['Shipment']['Result']['Code']['#text']; exit;
			if($result_arr['ECommerce']['Shipment']['Result']['Code']['#text']==203) {
				$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text'] = MARK_UP * $result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text'];
				echo '<script>
							function roundNumber(num, dec) {
								var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
								return result;
							}
							parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text']).';
							parent.document.getElementById("shipping_type").value="DHL";
							parent.document.getElementById("tr_shipping").style.display="";
							parent.document.getElementById("div_shipping_amount").innerHTML="'.sprintf("%01.2f",$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text']).'";
							parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By DHL\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text']).'\\\nShipment By: DHL\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text']).'</font></a>";
							//parent.document.getElementById("tr_shipping_unassembled").style.display="";
							//parent.document.getElementById("div_shipping_amount_unassembled").innerHTML="'.sprintf("%01.2f",$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text']).'";
							
							var total_amt=parent.document.getElementById("sub_total").value;
						
							total_amt=parseFloat(total_amt);
							
							total_amt=total_amt+'.sprintf("%01.2f",$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text']).';
							total_amt=roundNumber(total_amt,2); 
							total_amt = total_amt.toFixed(2);
							parent.document.getElementById("div_total_amount").innerHTML=total_amt;
							
						//	parent.document.getElementById("div_total_amount_unassembled").innerHTML=total_amt;
							
							parent.document.getElementById("ship_referenceno").value="'.$result_arr['ECommerce']['@transmission_reference'].'"; 
							alert("Shipping Amount would be $'.sprintf("%01.2f",$result_arr['ECommerce']['Shipment']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text']).'.\nClick Continue to Make Payment.");
							//parent.document.setShipment(1,"'.$result_arr['ECommerce']['EstimateDetail']['RateEstimate']['TotalChargeEstimate']['#text'].'");
							parent.document.getElementById("btnsubmit").disabled="";
							parent.document.getElementById("payment_proccess").style.display="none";
							parent.document.getElementById("payment_proccess_img").style.display="none";
					  </script>';
			}*/ 
				$ship_url = "http://freight.fedex.com/XMLLTLRating.jsp?regKey=".FedEX_REG_KEY."&as_opco=FXF&as_iamthe=shipper&as_shipterms=prepaid&as_shzip=45404&as_shcntry=US&as_shcity=DAYTON&as_shstate=oh&as_cnzip=".$_POST['txtszip']."&as_cncntry=US&as_cncity=".$_POST['txtscity']."&as_cnstate=".$_POST['txtsstate']."&as_class1=".sprintf("%03d",$shipping_class)."&as_weight1=".$weight_total;
			$ch=curl_init($ship_url); 
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
			curl_setopt($ch,CURLOPT_HTTPPOST,0);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec ($ch); 
			
			
			
			
			
			$xml_obj = new xml2array($result); 
			$result_arr = $xml_obj->getResult();
		 
			if($result_arr['customer-rate-response']['rate-number']['#text']!="") {
				$result_arr['customer-rate-response']['net-freight-charges']['#text'] = MARK_UP * $result_arr['customer-rate-response']['net-freight-charges']['#text'];
				echo '<script>
							function roundNumber(num, dec) {
								var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
								return result;
							}
							parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).';
							parent.document.getElementById("shipping_type").value="FedEx";
							parent.document.getElementById("tr_shipping").style.display="";
							parent.document.getElementById("div_shipping_amount").innerHTML="'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'";
							parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By FedEx\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'\\\nShipment By: FedEx\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'</font></a>";
							//parent.document.getElementById("tr_shipping_unassembled").style.display="";
							//parent.document.getElementById("div_shipping_amount_unassembled").innerHTML="'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'";
							
							var total_amt=parent.document.getElementById("sub_total").value;
						
							total_amt=parseFloat(total_amt);
							
							total_amt=total_amt+'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).';
							total_amt=roundNumber(total_amt,2); 
							total_amt = total_amt.toFixed(2);
							parent.document.getElementById("div_total_amount").innerHTML=total_amt;
							
						//	parent.document.getElementById("div_total_amount_unassembled").innerHTML=total_amt;
							
							parent.document.getElementById("ship_referenceno").value="'.$result_arr['customer-rate-response']['rate-number']['#text'].'"; 
							alert("Shipping Amount would be $'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'.\nClick Continue to Make Payment.");
							//parent.document.setShipment(1,"'.$result_arr['customer-rate-response']['net-freight-charges']['#text'].'");
							parent.document.getElementById("btnsubmit").disabled="";
							parent.document.getElementById("payment_proccess").style.display="none";
							parent.document.getElementById("payment_proccess_img").style.display="none";
					  </script>';
			} else {
				/*echo '<script>
							//alert("'.$result_arr['ECommerce']['Shipment']['Faults']['Fault']['Code']['#text'].': '.$result_arr['ECommerce']['Shipment']['Faults']['Fault']['Desc']['#text'].'");
							//parent.setShipment(1,"'.$result_arr['ECommerce']['Shipment']['Faults']['Fault']['#text'].'");
							//alert("'.$result_arr['ECommerce']['Shipment']['Result']['Desc']['#text'].'");
							alert("Shipping can not be rated.");
					  </script>';*/
				
				
				
				$head[]="Connection: Keep-Alive";  
				$ch=curl_init("http://www.daytonfreight.com/scripts/xml/xmlrates.aspx?usr=".DF_USERNAME."&pwd=".DF_PASSWORD."&ozip=45404&dzip=".$_POST['txtszip']."&cust=".DF_ACCOUNTNO."&terms=T&class=".$shipping_class."&weight=".$weight_total); 
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
				curl_setopt($ch,CURLOPT_HTTPPOST,0);
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
				curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
				$result = curl_exec ($ch); 
				$xml_obj = new xml2array($result); 
				$result_arr = $xml_obj->getResult();
				
				if($result_arr['rateestimate']['total']['#text']!="") {
					$result_arr['rateestimate']['total']['#text']=substr($result_arr['rateestimate']['total']['#text'],1,strlen($result_arr['rateestimate']['total']['#text'])-2);
					$result_arr['rateestimate']['total']['#text'] = MARK_UP * $result_arr['rateestimate']['total']['#text'];
					echo '<script>
								function roundNumber(num, dec) {
									var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
									return result;
								}
								parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).';
								parent.document.getElementById("tr_shipping").style.display="";
								parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By Dayton Freight\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'\\\nShipment By: Dayton Freight\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'</font></a>";
								parent.document.getElementById("shipping_type").value="Dayton Freight";
								//parent.document.getElementById("tr_shipping_unassembled").style.display="";
								//parent.document.getElementById("div_shipping_amount_unassembled").innerHTML="'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'";
								
								var total_amt=parent.document.getElementById("sub_total").value;
							
								total_amt=parseFloat(total_amt);
								
								total_amt=total_amt+'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).';
								total_amt=roundNumber(total_amt,2); 
								total_amt = total_amt.toFixed(2);
								parent.document.getElementById("div_total_amount").innerHTML=total_amt;
								
							//	parent.document.getElementById("div_total_amount_unassembled").innerHTML=total_amt;
								
								parent.document.getElementById("ship_referenceno").value="'.$result_arr['rateestimate']['estimatenumber']['#text'].'"; 
								alert("Shipping Amount would be $'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'.\nClick Continue to Make Payment.");
								//parent.document.setShipment(1,"'.$result_arr['rateestimate']['total']['#text'].'");
								parent.document.getElementById("btnsubmit").disabled="";
								parent.document.getElementById("payment_proccess").style.display="none";
								parent.document.getElementById("payment_proccess_img").style.display="none";
						  </script>';
				} else {
					echo '<script>
								alert("Shipping can not be rated.");
								parent.document.getElementById("btnsubmit").disabled="";
								parent.document.getElementById("payment_proccess").style.display="none";
								parent.document.getElementById("payment_proccess_img").style.display="none";
						  </script>';
					//echo $result_arr['ECommerce']['Shipment']['Result']['Desc']['#text'];
				}
				//echo $result_arr['ECommerce']['Shipment']['Result']['Desc']['#text'];
			}
		} else {

				/*echo "Total Weight=".$weight_total;
				echo "<br>dayton_frieght=".$dayton_frieght;
				echo "<br>shipping_class=".$shipping_class;
				echo "<br>Zip code=".$_POST['txtszip'];*/
			$head[]="Connection: Keep-Alive";  
			$ch=curl_init("http://www.daytonfreight.com/scripts/xml/xmlrates.aspx?usr=".DF_USERNAME."&pwd=".DF_PASSWORD."&ozip=45404&dzip=".$_POST['txtszip']."&cust=".DF_ACCOUNTNO."&terms=T&class=".$shipping_class."&weight=".$weight_total); 

			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
			curl_setopt($ch,CURLOPT_HTTPPOST,0);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec ($ch); 
			$xml_obj = new xml2array($result); 
			$result_arr = $xml_obj->getResult();

			if($result_arr['rateestimate']['total']['#text']!="") {
				$result_arr['rateestimate']['total']['#text']=substr($result_arr['rateestimate']['total']['#text'],1,strlen($result_arr['rateestimate']['total']['#text'])-2);
				$result_arr['rateestimate']['total']['#text'] = MARK_UP * $result_arr['rateestimate']['total']['#text'];
				echo '<script>
							function roundNumber(num, dec) {
								var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
								return result;
							}
							parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).';
							parent.document.getElementById("tr_shipping").style.display="";
							parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By Dayton Freight\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'\\\nShipment By: Dayton Freight\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'</font></a>";
							parent.document.getElementById("shipping_type").value="Dayton Freight";
							//parent.document.getElementById("tr_shipping_unassembled").style.display="";
							//parent.document.getElementById("div_shipping_amount_unassembled").innerHTML="'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'";
							
							var total_amt=parent.document.getElementById("sub_total").value;
						
							total_amt=parseFloat(total_amt);
							
							total_amt=total_amt+'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).';
							total_amt=roundNumber(total_amt,2); 
							total_amt = total_amt.toFixed(2);
							parent.document.getElementById("div_total_amount").innerHTML=total_amt;
							
						//	parent.document.getElementById("div_total_amount_unassembled").innerHTML=total_amt;
							
							parent.document.getElementById("ship_referenceno").value="'.$result_arr['rateestimate']['estimatenumber']['#text'].'"; 
							alert("Shipping Amount would be $'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'.\nClick Continue to Make Payment.");
							//parent.document.setShipment(1,"'.$result_arr['rateestimate']['total']['#text'].'");
							parent.document.getElementById("btnsubmit").disabled="";
							parent.document.getElementById("payment_proccess").style.display="none";
							parent.document.getElementById("payment_proccess_img").style.display="none";
					  </script>';
			} else {
				echo '<script>
							alert("Shipping can not be rated.");
							parent.document.getElementById("btnsubmit").disabled="";
							parent.document.getElementById("payment_proccess").style.display="none";
							parent.document.getElementById("payment_proccess_img").style.display="none";
					  </script>';
				//echo $result_arr['ECommerce']['Shipment']['Result']['Desc']['#text'];
			}
		
		}
	}
	
	// Function for calculate shipment using UPS method
	function GetShippingByUPs($product_name,$weight_total) {  //echo $product_name."--".$weight_total; exit;
			include(DIR_CLASS."ups.php");
			$rate = new Ups;
			$rate->Product($_POST['ship_type']); // See upsProduct() function for codes
			$rate->origin("45805", "US"); // Use ISO country codes!
			$rate->dest($_POST['txtszip'], $this->GetCountryCode2($_POST['selscountry'])); // Use ISO country codes!
			$rate->rate("OCA"); // See the rate() function for codes
			$rate->container("CP"); // See the container() function for codes
			$rate->weight($weight_total);
			$rate->rescom("RES"); // See the rescom() function for codes
			$resp_data = $rate->Quote();
			list($rcode,$amt) = explode("@@@@",$resp_data);
			//echo $rate->Quote();
			if(trim($rcode)==3) {
				echo $amt;
				if($_POST['sub_total_catalog']<100) {
					echo $amt = $amt*1.10;
				}
				if($_POST['shipping_amount_catalog']!="") {
					echo $amt+=$_POST['shipping_amount_catalog'];
				}
				echo '<script>
							function roundNumber(num, dec) {
								var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
								return result;
							}
							parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$amt).';
							parent.document.getElementById("tr_shipping").style.display="";
							parent.document.getElementById("div_shipping_amount").innerHTML="'.sprintf("%01.2f",$amt).'";
							parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By UPS\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$amt).'\\\nShipment By: UPS\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$amt).'</font></a>";
							var total_amt=parent.document.getElementById("sub_total").value;
							total_amt=parseFloat(total_amt);
							total_amt=total_amt+'.sprintf("%01.2f",$amt).';
							total_amt=roundNumber(total_amt,2); 
							total_amt = total_amt.toFixed(2);
							parent.document.getElementById("div_total_amount").innerHTML=total_amt;
							parent.document.getElementById("ship_referenceno").value="'.$amt.'"; 
							alert("Shipping Amount would be $'.sprintf("%01.2f",$amt).'.\nClick Continue to Make Payment.");
							 
							parent.document.getElementById("btnsubmit").disabled="";
							parent.document.getElementById("payment_proccess").style.display="none";
							parent.document.getElementById("payment_proccess_img").style.display="none";
					  </script>';
			} else {
					echo '<script>
								alert("Shipping can not be rated.\nError: '.addslashes($amt).'");
								parent.document.getElementById("btnsubmit").disabled="";
								parent.document.getElementById("payment_proccess").style.display="none";
								parent.document.getElementById("payment_proccess_img").style.display="none";
						  </script>';
			}
	}
	function GetCountryCode2($code) {
		$getdata=$this->Select(TABLE_COUNTRY,"Code='".$code."'","Code2","",1);
		if(count($getdata)>0){
			$code2=$getdata[0]['Code2'];
		}
		return $code2;
	}
	
	 
	function getShippingInfo($product_name,$weight_total,$dayton_frieght,$shipping_class) {  
	
		include("includes/xml2array.php");
		include("includes/new_domxml.php"); 
		
		
		if($dayton_frieght!=1) {
			//Calculate Shipment using UPS method
			include(DIR_CLASS."ups.php");
			$rate = new Ups;
			$rate->Product("GND"); // See upsProduct() function for codes
			$rate->origin("45404", "US"); // Use ISO country codes!
			//$rate->dest($_POST['txtszip'], $this->GetCountryCode2($_POST['selscountry'])); // Use ISO country codes!
			$rate->dest($_POST['txtszip'], "US");
			$rate->rate("RDP"); // See the rate() function for codes
			$rate->container("CP"); // See the container() function for codes
			$rate->weight($weight_total);
			$rate->rescom("RES"); // See the rescom() function for codes
			$resp_data = $rate->Quote();
			list($rcode,$amt) = explode("@@@@",$resp_data);
			//echo $rate->Quote(); 
			if(trim($rcode)==3) {
				
				$amt = MARK_UP * $amt;
				
				/*if($_POST['sub_total_catalog']<100) {
					echo $amt = $amt*1.10;
				}
				if($_POST['shipping_amount_catalog']!="") {
					echo $amt+=$_POST['shipping_amount_catalog'];
				}*/
				echo '<script>
							function roundNumber(num, dec) {
								var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
								return result;
							}
							parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$amt).';
							parent.document.getElementById("shipping_type").value="UPS";
							parent.document.getElementById("tr_shipping").style.display="";
							parent.document.getElementById("div_shipping_amount").innerHTML="'.sprintf("%01.2f",$amt).'";
							parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By UPS\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$amt).'\\\nShipment By: UPS\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$amt).'</font></a>";
							var total_amt=parent.document.getElementById("sub_total").value;
							total_amt=parseFloat(total_amt);
							total_amt=total_amt+'.sprintf("%01.2f",$amt).';
							total_amt=roundNumber(total_amt,2); 
							total_amt = total_amt.toFixed(2);
							parent.document.getElementById("div_total_amount").innerHTML=total_amt;
							parent.document.getElementById("ship_referenceno").value="'.$amt.'"; 
							alert("Shipping Amount would be $'.sprintf("%01.2f",$amt).'.\nClick Continue to Make Payment.");
							 
							parent.document.getElementById("btnsubmit").disabled="";
							parent.document.getElementById("payment_proccess").style.display="none";
							parent.document.getElementById("payment_proccess_img").style.display="none";
					  </script>';
			} else {
					echo '<script>
								alert("Shipping can not be rated.");
								parent.document.getElementById("btnsubmit").disabled="";
								parent.document.getElementById("payment_proccess").style.display="none";
								parent.document.getElementById("payment_proccess_img").style.display="none";
						  </script>';
				}
			
		} else {
			//$ship_url = "http://fedexfreight.fedex.com/XMLLTLRating.jsp?regKey=".FedEX_REG_KEY."&as_opco=FXF&as_iamthe=shipper&as_shipterms=prepaid&as_shzip=45404&as_shcntry=US&as_shcity=DAYTON&as_shstate=oh&as_cnzip=".$_POST['txtszip']."&as_cncntry=US&as_cncity=".$_POST['txtscity']."&as_cnstate=".$_POST['txtsstate']."&as_class1=".sprintf("%03d",$shipping_class)."&as_weight1=".$weight_total."&as_pkgtype1=OTHR";
			
			$ship_url = "http://fedexfreight.fedex.com/XMLLTLRating.jsp?regKey=".FedEX_REG_KEY."&as_acctnbr=413164729&as_opco=FXF&as_iamthe=shipper&as_shipterms=prepaid&as_shzip=45404&as_shcntry=US&as_shcity=DAYTON&as_shstate=oh&as_cnzip=".$_POST['txtszip']."&as_cncntry=US&as_cncity=".$_POST['txtscity']."&as_cnstate=".$_POST['txtsstate']."&as_class1=".sprintf("%03d",$shipping_class)."&as_weight1=".$weight_total."&as_pkgtype1=OTHR";
			

			$ch=curl_init($ship_url); 
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
			curl_setopt($ch,CURLOPT_HTTPPOST,0);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec ($ch); 
			
			$xml_obj = new xml2array($result); 
			$result_arr = $xml_obj->getResult();
			if($result_arr['customer-rate-response']['rate-number']['#text']!="") {
				$result_arr['customer-rate-response']['net-freight-charges']['#text'] = MARK_UP * $result_arr['customer-rate-response']['net-freight-charges']['#text'];
				echo '<script>
							function roundNumber(num, dec) {
								var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
								return result;
							}
							parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).';
							parent.document.getElementById("shipping_type").value="FedEx";
							parent.document.getElementById("tr_shipping").style.display="";
							parent.document.getElementById("div_shipping_amount").innerHTML="'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'";
							parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By FedEx\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'\\\nShipment By: FedEx\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'</font></a>";
							//parent.document.getElementById("tr_shipping_unassembled").style.display="";
							//parent.document.getElementById("div_shipping_amount_unassembled").innerHTML="'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'";
							
							var total_amt=parent.document.getElementById("sub_total").value;
						
							total_amt=parseFloat(total_amt);
							
							total_amt=total_amt+'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).';
							total_amt=roundNumber(total_amt,2); 
							total_amt = total_amt.toFixed(2);
							parent.document.getElementById("div_total_amount").innerHTML=total_amt;
							
						//	parent.document.getElementById("div_total_amount_unassembled").innerHTML=total_amt;
							
							parent.document.getElementById("ship_referenceno").value="'.$result_arr['customer-rate-response']['rate-number']['#text'].'"; 
							alert("Shipping Amount would be $'.sprintf("%01.2f",$result_arr['customer-rate-response']['net-freight-charges']['#text']).'.\nClick Continue to Make Payment.");
							//parent.document.setShipment(1,"'.$result_arr['customer-rate-response']['net-freight-charges']['#text'].'");
							parent.document.getElementById("btnsubmit").disabled="";
							parent.document.getElementById("payment_proccess").style.display="none";
							parent.document.getElementById("payment_proccess_img").style.display="none";
					  </script>';
			} else {
				$head[]="Connection: Keep-Alive";  
				$ch=curl_init("http://www.daytonfreight.com/scripts/xml/xmlrates.aspx?usr=".DF_USERNAME."&pwd=".DF_PASSWORD."&ozip=45404&dzip=".$_POST['txtszip']."&cust=".DF_ACCOUNTNO."&terms=T&class=".$shipping_class."&weight=".$weight_total); 
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
				curl_setopt($ch,CURLOPT_HTTPPOST,0);
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
				curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
				$result = curl_exec ($ch); 
				$xml_obj = new xml2array($result); 
				$result_arr = $xml_obj->getResult();
				
				if($result_arr['rateestimate']['total']['#text']!="") {
					$result_arr['rateestimate']['total']['#text']=substr($result_arr['rateestimate']['total']['#text'],1,strlen($result_arr['rateestimate']['total']['#text'])-2);
					$result_arr['rateestimate']['total']['#text'] = MARK_UP * $result_arr['rateestimate']['total']['#text'];
					echo '<script>
								function roundNumber(num, dec) {
									var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
									return result;
								}
								parent.document.getElementById("shipping_amount").value='.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).';
								parent.document.getElementById("tr_shipping").style.display="";
								parent.document.getElementById("div_shipping_amount").innerHTML="<a href=\'javascript: void(0);\' title=\'By Dayton Freight\' onClick=\'javascript: alert(\\"Shipping Charges: $'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'\\\nShipment By: Dayton Freight\\");\'><font color=\'#000000\'>'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'</font></a>";
								parent.document.getElementById("shipping_type").value="Dayton Freight";
								//parent.document.getElementById("tr_shipping_unassembled").style.display="";
								//parent.document.getElementById("div_shipping_amount_unassembled").innerHTML="'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'";
								
								var total_amt=parent.document.getElementById("sub_total").value;
							
								total_amt=parseFloat(total_amt);
								
								total_amt=total_amt+'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).';
								total_amt=roundNumber(total_amt,2); 
								total_amt = total_amt.toFixed(2);
								parent.document.getElementById("div_total_amount").innerHTML=total_amt;
								
							//	parent.document.getElementById("div_total_amount_unassembled").innerHTML=total_amt;
								
								parent.document.getElementById("ship_referenceno").value="'.$result_arr['rateestimate']['estimatenumber']['#text'].'"; 
								alert("Shipping Amount would be $'.sprintf("%01.2f",$result_arr['rateestimate']['total']['#text']).'.\nClick Continue to Make Payment.");
								//parent.document.setShipment(1,"'.$result_arr['rateestimate']['total']['#text'].'");
								parent.document.getElementById("btnsubmit").disabled="";
								parent.document.getElementById("payment_proccess").style.display="none";
								parent.document.getElementById("payment_proccess_img").style.display="none";
						  </script>';
				} else {
					echo '<script>
								alert("Shipping can not be rated.");
								parent.document.getElementById("btnsubmit").disabled="";
								parent.document.getElementById("payment_proccess").style.display="none";
								parent.document.getElementById("payment_proccess_img").style.display="none";
						  </script>';
				}
			}
		}
	}
	
	function GetSectionDropDown($sectionSelected) 
	{
		$arr_section = array('QS'=>'Quick Ship','CAT'=>'Catalog Items');
		if(count($arr_section)>0) {
			foreach($arr_section as $key=>$value) {
				$section_data .= '<option value="'.$key.'"';
				if($sectionSelected == $key ) 
				$section_data .= ' selected="selected"';
				$section_data .= '>'.$value.'</option>';
			}
		}
		return $section_data;
	}
	function GetSectionLinkDropDown($categorySelected) 
	{
		$arr_section = array('QS'=>'Quick Ship','CAT'=>'Catalog Items');
		if(count($arr_section)>0) {
			foreach($arr_section as $key=>$value) {
				$section_data .= '<option value="'.$this->MakeUrl($this->mCurrentUrl,"section=".$key).'"';
				if($categorySelected == $key ) 
				$section_data .= ' selected="selected"';
				$section_data .= '>'.$value.'</option>';
			}
		}
		return $section_data;
	}
	
	function Ajax_Category($secId) {
		if($secId == "catalog") $secId='CAT';
		else if($secId == "store") $secId='QS';
		$get_result=$this->Select(TABLE_CATEGORY,"section='".$secId."' AND is_active='1'","category_id,category_name","category_name");
		$data="";
		if(count($get_result)>0) {
			foreach($get_result as $result) { 
				$data.=$result['category_id']."@@@@".ucfirst(stripslashes($result['category_name']))."####";
			}
		}
		return $data;
	}
	
	function GetMainCategoryDropDown($categorySelected, $section) 
	{
		global $db; 
		if($section != "")	$condition = "section='".$section."'";
		else	$condition = "section='QS'";
		$get_result=$this->Select(TABLE_CATEGORY,$condition." and is_active=1","","category_name");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$cat_data .= '<option value="'.$result['category_id'].'"';
				if($categorySelected == $result['category_id'] ) 
				$cat_data .= ' selected="selected"';
				$cat_data .= '>'.ucfirst(stripslashes($result['category_name'])).'</option>';
			}
		}
		return $cat_data;
	}
	
	function GetQuickShipDropDown($productSelected) 
	{
		global $db; 
		$condition = "is_ecommerce='1'";
		$get_result=$this->Select(TABLE_PRODUCT,$condition." and is_active=1");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$prod_data .= '<option value="'.$result['product_id'].'"';
				if($productSelected == $result['product_id'] ) 
				$prod_data .= ' selected="selected"';
				$prod_data .= '>'.ucfirst(stripslashes($result['product_name'])).'</option>';
			}
		}
		return $prod_data;
	}
	function GetAssemblyFeeDropDown($feeSelected) {
		global $db;
		$get_result=$this->Select(TABLE_ASSEMBLY,"is_active='1'");						
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$fee_data .= '<option value="'.$result['assemblyfee_id'].'"';
				if($feeSelected == $result['assemblyfee_id'] ) $fee_data .= ' selected="selected"';
				$fee_data .= '>'.ucfirst(stripslashes($result['title'])).'&nbsp;[$ '.$result['amount'].']</option>';
			}
		}
		return $fee_data;
	}
	function GetAssemblyPrice($productId){ 
		$product_arr=$this->Select(TABLE_PRODUCT." as pt1, ".TABLE_ASSEMBLY." as pt2", "pt1.assemblyfee_id=pt2.assemblyfee_id and product_id='".$productId."'",'');		

		if(count($product_arr)>0){
			foreach($product_arr as $product_data){
				$assembly_price = $product_data['amount'];
			}
		} 
		return $assembly_price;
	}
	function ProcessPayment($pay_data) {
		include ROOT."includes/lphp.php";
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
		}
		return array('status'=>$ispaid_status,'transaction_id'=>$result['r_code'],'message'=>$result['r_error']);
	}
	
	function Ajax_Email($userName) {
		$get_result=$this->Select(TABLE_CUSTOMERS,"e_mail='".$userName."'","e_mail");
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
	function CheckUsername($new_username) {
		$get_result=$this->Select(TABLE_CUSTOMERS,"e_mail='".$new_username."'","e_mail");
		if(count($get_result)>0) {
			return false;
		} else {
			return true;
		}
	}
	
	function GetClassDropDown($classSelected) {
		$class_arr = array('50' =>'50', '70' =>'70', '100' =>'100', '125' =>'125' );
		foreach($class_arr as $key=>$value) {
			$class_dropdown .= "<option value='".$key."'";
			if($key==$classSelected)
				$class_dropdown .= "selected=selected";
			$class_dropdown .= ">".$value."</option>";
		}
		return $class_dropdown;
	}
	
	/*function GetColorDivDropDown($colorSelected) 
	{
		$get_result=$this->Select(TABLE_COLORPALETTE,"is_active='1'");						
		
		if(count($get_result)>0) {
			$color_data .='<div style="height:188px; width:250px; overflow:auto; border:1px solid #FF0000;">';
			foreach($get_result as $result) {
				$color_data .= '<div class="text" class="imgdiv" style="background-image:url('.DIR_COLOR_THUMBNAILSMALL.'/'.'');" >'.$result['color_name'].'</div>';
			}
			$color_data .='</div>';
		}
		return $color_data;
	}*/
	
	function Ajax_Login($userInfo) { 
		$user_arr = explode('@@@@',$userInfo);
		$userName = $user_arr[0];
		$userPass = $user_arr[1];
		
		$get_result=$this->Select(TABLE_CUSTOMERS,"e_mail='".$userName."' and password=MD5('".$userPass."')","*");
		$count=0;
		$login_status = 0;
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				if($result['is_active']==0) {
					$errorMessage="Account is Disabled";
				} else {
					//session_start();
					$this->SetSession($result['customer_id'],"user");
					if($this->ValidateSession("user"))
					{
						$_SESSION['sess_user_name']=$result['e_mail'];
						$login_status = 1;
						
						//Setting session for checkout page
						$_SESSION['sess_step_2']["txtname"]=ucfirst(stripslashes($result['first_name']));
						if(trim($result['last_name']))
							$_SESSION['sess_step_2']["txtname"] .= " ".ucfirst(stripslashes($result['last_name']));
						$_SESSION['sess_step_2']["txtaddress"]=stripslashes($result['address']);
						$_SESSION['sess_step_2']["txtemail"]=stripslashes($result['e_mail']);
						$_SESSION['sess_step_2']["txtphone"]=stripslashes($result['phone_no']);
						$_SESSION['sess_step_2']["txtcity"]=stripslashes($result['city']);
						$_SESSION['sess_step_2']["txtstate"]=stripslashes($result['state']);
						$_SESSION['sess_step_2']["txtzip"]=stripslashes($result['zip']);
					} else {
						$errorMessage="Session is Expired, please Re-Login.";
					}
				}
			}
		} else {
			$errorMessage='Invalid Username or Password.';
		}
		
		$data = $login_status."####".$errorMessage."####".$_SESSION['sess_step_2']["txtname"]."####".$_SESSION['sess_step_2']["txtaddress"]."####".$_SESSION['sess_step_2']["txtemail"]."####".$_SESSION['sess_step_2']["txtphone"]."####".$_SESSION['sess_step_2']["txtcity"]."####".$_SESSION['sess_step_2']["txtstate"]."####".$_SESSION['sess_step_2']["txtzip"];
		return $data;
	}
	
	function GetManageList_user($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) {
		global $gStartPageNo, $sort_query;
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
					<tr class="titlemain">
						 <td width="4%" height="20" align="center">#</td>';
				}
				$page_content.= " 
					<tr class='$bg' onmouseover=this.className='tableover' onmouseout=this.className='".$bg."'> 
						<td align='center' valign='top' width='5%'  >".sprintf("%02d",$count)."</td>";		
				$field_count=0;
				$size_count=0;

				//get result from Quantitywise table start
						
				//over
				foreach($result as $key=>$value) {
					if($key!='id' && $key!='is_active'){ 
						if($key=="Description")
						{
							if(strlen($result[$key])>"80")
							{
								$result[$key]=substr($result[$key],0,70)."....";
							}
						}
						if($key=="Product_Name")
						{
							$result[$key]=nl2br(strip_tags(str_replace("<br />","\n",stripslashes($result[$key]))));
						}

						$page_content.= " <td align='left' valign='top' style='padding-left:7px;' >";
						
						if($countList[$key]['table']!="") {
							$get_result=$this->Select($countList[$key]['table'],str_replace("[id]",$result['id'],$countList[$key]['condition']),$countList[$key]['column'].' as '.$key);
							//print_r($get_result);
							if($get_result[0][$key]=="")
								$get_result[0][$key]="-";
							$page_content.= str_replace(array("[id]","[val]"),array($result['id'],$get_result[0][$key]),$countList[$key]['pattern']). "</td>";
						}elseif($countList[$key]['custom']){
							if($countList[$key]['custom']=='quantity_price') {
								list($price,$ptype) = explode("|",$result[$key]);
								if($ptype=='Quantity wise') {
									$qty_arr=$this->Select(TABLE_PRODUCTQUANTITY,"product_id='".$result['id']."'","","quantity_min asc");
									$page_content.="<table width='100%' border=0 cellpadding='2' cellspacing='2' class='text'>";
									$page_content.="<tr><td align='center'><strong>Qty Min</strong></td><td align='center'><strong>Qty Max</strong></td><td align='center'><strong>Price</strong></td></tr>";
									if(count($qty_arr)>0) {
										foreach($qty_arr as $qty) {
											$page_content.="<tr><td align='center'>".sprintf("%03d",$qty['quantity_min'])."</td><td align='center'>";
											$page_content.=$qty['quantity_max']=='-1'?"-":sprintf("%03d",$qty['quantity_max']);
											$page_content.="</td><td align='center'>".sprintf("$%01.2f",$qty['quantity_price'])."</td></tr>";
										}
									}
									$page_content.="</table>";
									$page_content.="</td>";
								} else {
									$page_content.= sprintf("$%01.2f",$result[$key]). "</td>";
								}
							}
						} else {
							$page_content.= stripslashes($result[$key]). "</td>";
						}
						if($is_page_header=="") {
							if($sizeArray[$size_count]!="")
								$width='width="'.$sizeArray[$size_count].'"';
							else
								$width="";
							$page_header.='
								<td  style="padding-left:7px;" '.$width.' ';
							if($countList[$key]['table']!="" || $countList[$key]['custom']!="" || $sortArray[$key]=='N') {
								$page_header.='>'.str_replace("_"," ",$key) . '</td>';
							}
							else {
								
								if($_GET['sort']==$key) {
									$page_header.=' class="subhead" ';
									if($_GET['order']=='a') $order_q="&order=d"; else $order_q='&order=a';
								} else {
									$order_q='&order=a';
								}
								$page_header.=' ><a href="'.$this->MakeUrl($this->mCurrentUrl,"sort=".$key.$order_q.$sort_query).'" class="link_order">'.str_replace("_"," ",$key).'';
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='<img src="'.SITE_URL.'images/up.gif" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='<img src="'.SITE_URL.'images/down.gif" border=0 align="absmiddle" />';
									
								$page_header.='</a></td>
									 ';
							}
							$size_count++;
						}
						$field_count++;
					}
				}

				$action_count=0;
				global $action_query;
				foreach($listAction as $aKey => $aValue) {
					if($aValue['type']=='confirm') {
						$page_content.= "
										<td width='3%' align='center' valign='top' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".$aKey." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";	
					} elseif($aValue['type']=='condition') {
						if( $result[$aValue['conditionkey']]==$aValue['conditionvalue']) {
							$page_content.= "
										<td width='3%' align='center' valign='top' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']))."' onclick=\"javascript: return confirm('Are you sure you want to ".ucfirst($aKey)." this record?'); \"><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";
						}	
					} elseif($aValue['type']=='select') {
						if($result['is_active'] == 1) $selected_open='selected="selected"'; else $selected_open="";
						if($result['is_active'] == 0) $selected_closed='selected="selected"'; else $selected_closed="";
						if($result['is_active'] == 1) $action="deactivate"; else $action="activate"; 
						$page_content.= "
										<td width='3%' align='center' valign='top' >
											<input type='hidden' name='select_id[]' value='".$result['id']."' /><select class='select1' name='select_value[]'"."onchange=\"javascript:window.location='".$this->MakeUrl($this->mModuleUrl."/".$action,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'\";><option value='1' ".$selected_open.">Open</option><option value='0' ".$selected_closed." >Closed</option>
										</td>";	
					} elseif($aValue['type']=='label') {
						if($result['is_active'] == 1) $selected_open='Open'; else $selected_open="closed";
						$page_content.= "
										<td width='3%' align='center' valign='top' class='text'>
											".$selected_open."
										</td>";		
					} else {
						$page_content.= "
										<td width='3%' align='center' valign='top' >
											<a href='".$this->MakeUrl($this->mModuleUrl."/".$aKey,$action_query.'id='.str_replace("[id]",($result['id']),$aValue['value']).'')."'><img src='".SITE_URL."images/".$aKey.".gif'   border='0' alt='".$aKey."' title='".$aKey."'></a>
										</td>";	
					}
					$keyArr[]=$aKey;
					$action_count++;
				}
				if($help_icon=="")
					$help_icon="<tr><td colspan='7' align='right' style='padding-right:10px;'>".$this->GetHelpIcons($keyArr)."</td></tr>";
				if($is_page_header=="") {
					if($action_count>0) {
					$page_header.='
						<td width="10%" colspan="'.$action_count.'" align="center"  >Action</td>
                        ';
					}
					$page_header.='
					</tr>';
				}
				$page_content.= "
									</tr>";
			}
		}
		$content = $help_icon.'<tr><td colspan="7">
								<table width="100%" cellpadding="2" cellspacing="1" border="0" class="border_front" >'.$page_header.$page_content.'</table></td></tr>';
		return $content;	
	}
	function ResizeImage($source) { 
		$ext_arr1 = explode(".",$source);
		$new_file=$ext_arr1[0];
		$ext=$ext_arr1[1];
		
		$source=DIR_PRODUCT.$source;
		//$ext_arr = explode(".",$source);
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			$this->CreateThumb($new_file,$ext,DIR_PRODUCT,176,175,DIR_PRODUCT_THUMBNAIL);
			//$this->CreateThumb($new_file,$ext,DIR_PRODUCT,190,135,DIR_PRODUCT_THUMBNAIL_PROMO);
			$this->CreateThumb($new_file,$ext,DIR_PRODUCT,75,75,DIR_PRODUCT_SMALL);
		} 
	}	
	
	function Ajax_OrderStatus($status) { 
		$status_arr = explode("|",$status);
		$selStatus = $status_arr[0];
		$is_active = $status_arr[1];
		$dbStatus = $status_arr[2];
		
		if($is_active==0) {
			if($selStatus!=$dbStatus)
				$final_status="0####".$dbStatus;	
			else
				$final_status="1####".$dbStatus;	
		} else {
			$final_status="1####".$dbStatus;	
		}
		return $final_status;
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
}


?>
