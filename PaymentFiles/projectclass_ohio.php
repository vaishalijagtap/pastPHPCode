<?php

//-------------------------------------------------------------------------------------------------------------------------------
// Menu Class to buildup Dynamic Header Navigations Drop Down Menu.
//-------------------------------------------------------------------------------------------------------------------------------
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
	//-----------------------------------------------------n------------------------------------------//
	function Encode($str) {
			//return $str;
		return urlencode(base64_encode($str));//urlencode(base64_encode(
	} 
	
	function Captcha ($page_name) 
	{ 
		global $_SESSION;
		if($_SESSION['sess_captcha_'.str_replace(' ','_',$page_name)]) 
		{
			
		}
	}
	function selfURL() 
	{ 
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; 
		$protocol = $this->strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; 
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); 
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; 
	}
	function strleft($s1, $s2) 
	{ 
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
				
				$tot_rows=$this->Query($query,'count');
				
				$res=$this->Query($query."  LIMIT 0, $limit",'res');
				if(count($res)>0) {
					foreach($res as $r){
						$id_arr[] = $r['id'];
					}
				}
				
				$_SESSION['tot_rows']=$tot_rows;
				$query.=" LIMIT $offset1, $limit";
				
				if(($offset1+$limit)<=$_SESSION['tot_rows'])
					$_SESSION['record_no_display']=($offset1+1)." - ".($offset1+$limit)." of ".$_SESSION['tot_rows'];
				else 
					$_SESSION['record_no_display']=($offset1+1)." - ".($_SESSION['tot_rows'])." of ".$_SESSION['tot_rows'];
			} else {
				$offset=0;
				$query.=" LIMIT 0, $limit";
				
				$tot_rows=$this->Query($query,'count');
				
				$res=$this->Query($query."  LIMIT 0, $limit",'res');
				if(count($res)>0) {
					foreach($res as $r)	$id_arr[] = $r['id'];
				}
				
				$_SESSION['tot_rows']=$tot_rows;
				
				if(($limit)<=$_SESSION['tot_rows'])
					$_SESSION['record_no_display']="1 - ".$limit." of ".$_SESSION['tot_rows'];
				else
					$_SESSION['record_no_display']="1 - ".$_SESSION['tot_rows']." of ".$_SESSION['tot_rows'];
			}
		} else {
			$tot_rows=$this->Query($query,'count');

			//echo $query;
			$res=$this->Query($query."  LIMIT 0, $limit",'res');
			if(count($res)>0) {
				foreach($res as $r){
					$id_arr[] = $r['id'];
				}
			}
			

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
	
	
	
	//-----------------------------------------------------------------------------------------------//
	// Method Pagination Display------------- FORMAT: Previous - [DROPDOWN of all page numbers available] - Next  
	//-----------------------------------------------------------------------------------------------//
	function PagingFooter($offset,$class='link') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;
		if($gPagingExtraPara!="")
			$gPagingExtraPara.="&";
		$display_page="";

		if($_SESSION['tot_offset']>1) 
		{
			$j=$offset-1;
			$k=$offset+1;
			$display_page.= '<div class="row3">';
			if($offset==0)  $display_page.= '<div class="col-7"><span id="leftArrow">Prev</span></div>';
			else $display_page.= "<div class=\"col-7\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$j)."' id=\"leftArrow\" >Prev</a></div>";
			$tot_offset=$_SESSION['tot_offset'];
			$display_page.="<div class=\"col-8\"><select name='paging_numbers' onchange=\"javascript: window.location='".$this->MakeUrl($this->mCurrentUrl)."'+this.value;\" >";
			for($i=0;$i<$tot_offset;$i++) {
				$m=$i+1;
				if($i==$offset) $sel.= 'selected';
				else $sel="";
				$display_page.= '<option value="'.$this->Encode($gPagingExtraPara.'offset='.$i).'" '.$sel.'>' . "<a href='$page_name?offset=$i$extra_name' class='$class'>" . sprintf("%03d",$m) . '</option> ';  
			}
			$display_page.="</select></div>";
			if($offset==($tot_offset-1)) {
				$display_page.= '<div class="col-9"><span id="rightArrow">Next</span></div>';
			} else {
				$display_page.= "<div class=\"col-9\"><a href='".$this->MakeUrl($this->mCurrentUrl,$gPagingExtraPara.'offset='.$k)."'  id=\"rightArrow\" >Next</a></div>";
			}
			$display_page.= "</div>";
		}
		
		return $display_page;
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
	
	function GetAdminEmailDetail() {
		$admin_data=$this->Select(TABLE_ADMIN,"","e_mail,email_from_name","",1);
		if(count($admin_data)) {
			foreach($admin_data as $admin) {
				return $admin;
			}
		}
		//else return ADMIN_EMAIL;
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
	
	function getToolbarIcons($toolbarArr){
		$toolbarIcons .= '<table height="60" cellpadding="4" cellspacing="4" border="0" align="right"><tr>';
			foreach($toolbarArr as $icon){
				if($icon=='close'){
					$button = '<img id="btn'.$icon.'" onclick="javascript:toolbarAction(\''.$icon.'\',this)" src="'.SITE_URL.'images/icons/toolbar_'.$icon.'.jpg" style="cursor:pointer; ">';
				}else{
					//$button = '<input id="btn'.$icon.'" type="image" src="'.SITE_URL.'images/icons/toolbar_'.$icon.'.jpg" style="visibility:hidden;position:absolute;">';
					$button = '<input id="btn'.$icon.'" onclick="javascript:toolbarAction(\''.$icon.'\',this)" type="submit" value="" class="'.$icon.'_btn" >';
				}//
				$toolbarIcons.='<td valign="middle" align="center"><table align="center" valign="bottom" class="actionButton" onMouseOver="this.className=\'actionButton_hover\';" onMouseOut="this.className=\'actionButton\';" cellpadding="0" cellspacing="0" border="0" align="center" width="50" height="50"><tr><td>'.$button.'</td></tr></table></td>';
			}
		$toolbarIcons.='</tr></table>';
		
		return $toolbarIcons;
		
	}
	
	function getToolbar($toolbarArr){
		
		$toolbar = '<table width="100%" height="60" cellpadding="0" cellspacing="4" border="0" align=""><tr><td class="text" valign=middle style="padding-left:2px;"><table width="100%" border="0" cellpadding="0" cellspacing="0" align="left"><tr><td width="50"><img src="'.SITE_URL.'images/admin/'.$this->mPageName.'.jpg" /></td><td class="headingText_tools">'.$this->page_heading.$this->sub_page.'</td></tr></table></td><td>'.$this->getToolbarIcons($toolbarArr).'</td></tr></table>';		
	
		$toolbarStructure="<div class='spacerDiv1'></div>
			<div class='containArea' id='borderId'>
			<!--[if lt IE 7]>
				<div class='bottomCurves'>
					<div class='leftCurve'><img src='".SITE_URL."images/admin/topLeftCurve.jpg' /></div>
					<div class='rightCurve'><img src='".SITE_URL."images/admin/topRightCurve.jpg' /></div>
				</div>
			<![endif]-->
			<div class='textDiv'>
				<div class='innerTextDiv'>".$toolbar."</div>
			<div class='clear'></div>
			</div>
			<!--[if lt IE 7]>
				<div class='bottomCurves'>
					<div class='leftCurve'><img src='".SITE_URL."images/admin/bottomLeftCurve.jpg' /></div>
					<div class='rightCurve'><img src='".SITE_URL."images/admin/bottomRightCurve.jpg' /></div>
				</div>
			<![endif]-->
			</div>
			<div class='spacerDiv'></div>";
		return $toolbarStructure;	
	}
	
	function GetManageList($listArray,$listAction,$sizeArray=array(),$countList=array(),$sortArray=array()) {
		global $sort_query,$action_query,$customList;
		
		if (array_key_exists('up', $listAction)) {$showDragImage=true;} else {$showDragImage=false;}
		
		if($countList=="") 
		{
			$countList=array();
		}
		if($customList=="") 
		{
			$customList=array();
		}
		global $gStartPageNo;
		if($this->mPageName=='employment') { $custom_width=' style="width:400px;"'; }
		if(count($listArray)==0) 
		{
				if($this->mPageName=='category' && $_GET['cat_id']!=''){
					$pageListAction = array('add'=>array('type'=>'button'), 'close'=>array('type'=>'close'));
				} elseif($this->mPageName!='subscriber' && $this->mPageName!='payment' && $this->mPageName!='forms') {
					$pageListAction = array('add'=>array('type'=>'button'));
				} //if()
				//}
				$page_content.="<tr align='center'>
						<td colspan='10' valign='top' class='redheading'>";
				$page_content.="<div class='spacerDiv1'></div>
					<div class='containArea' id='borderId'>
					<!--[if lt IE 7]>
						<div class='bottomCurves'>
							<div class='leftCurve'><img src='".SITE_URL."images/admin/topLeftCurve.jpg' /></div>
							<div class='rightCurve'><img src='".SITE_URL."images/admin/topRightCurve.jpg' /></div>
						</div>
					<![endif]-->
					<div class='textDiv'>
						<div class='innerTextDiv'>
							<table width='100%' height='60' cellpadding='0' cellspacing='4' border='0' align=''>
								<tr>
									<td colspan='".($action_count+$field_count)."' align='left'>
										<table width='100%' border=0 height='100%' cellpadding=0 cellspacing=0 >
											<tr>
												<td class='text' valign=middle style='padding-left:2px;'>
													<table width='100%' border='0' cellpadding='0' cellspacing='0'>
														<tr>
															<td width='2%'>
																<img src='".SITE_URL."images/admin/".$this->mPageName.".jpg' /></td>
															<td class='headingText' ".$custom_width.">&nbsp;".$this->page_heading."</td>
														</tr>
													</table>
												</td>
												<td>".$this->GetHelpIcons($pageListAction, $action_query)."</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
					<div class='clear'></div>
					</div>
					<!--[if lt IE 7]>
						<div class='bottomCurves'>
							<div class='leftCurve'><img src='".SITE_URL."images/admin/bottomLeftCurve.jpg' /></div>
							<div class='rightCurve'><img src='".SITE_URL."images/admin/bottomRightCurve.jpg' /></div>
						</div>
					<![endif]-->
					</div>
					<div class='spacerDiv'></div>";
				$page_content.="</td></tr>";	
				
				if($this->mPageName != 'category'){	
					$page_content.= " 
							<tr align='center'>
								<td colspan='10' valign='top' class='redheading'>No Result Found</td>
							</tr>";
				} else {
					$page_content.= " 
							<tr align='center'>
								<td colspan='10' valign='top' class='redheading'>No Pages Added</td>
							</tr>";
				}
			
	   	} 
		else 
		{
			$color="bg_light";
			
			$count = $gStartPageNo;
			//echo $count;
			
			foreach($listArray as $result) 
			{
				/*echo '<pre>';
				print_r($result);
				echo '</pre>';*/
				$count++;
				//echo $count; die;
				if($count%2==0) 
				{
					$bg = "dataclassalternate";
				}
				else 
				{
					$bg = "dataclass";
				}
				$no++;
				//echo $no; die;
				
				if($is_page_header=="") 
				{
					$page_header='
					<tr class="ban3 nodrag nodrop" height="22" >
					';
				}
				
				$action_count=1;
				
				if($showDragImage==true){
					$style = 'border-left:1px solid #C7C7C7;';$style1 = '';
				}else{
					$style1 = 'border-left:1px solid #C7C7C7;';$style = '';
				}	
				
				
				if($is_page_header=="") 
				{
					if($action_count>0) 
					{ 
						if($showDragImage==true){
							$page_header.='<td width="1%" style="'.$style.'border-top:1px solid #C7C7C7;" class="grid" align="center">&nbsp;</td>';
						}
						$page_header.='<td width="4%" style="'.$style1.'border-top:1px solid #C7C7C7;" class="grid" align="center"  >
							<input type="checkbox" name="chk_all" id="chk_all" value="yes" onClick="javascript: selectRowAll(this);" />
							
						</td>
                        ';
					}
					 
				}
				
				$page_content.= " 
					<tr height='25' class='$bg' onmouseover=this.className='tableover' onmouseout=this.className='".$bg."' onClick='javascript: selectRow(".$result['id'].",\"".$bg."\");' id='tr_list_".$result['id']."' > 
						";	
					if($showDragImage==true){
						$page_content.= "<td width='1%' id='list_".$result['id']."' style='$style' class='grid dragHandle' title='Drag and drop to change position'>&nbsp;</td>";
					}
					$page_content.=	"<td width='4%' style='$style1' class='grid' align='center' valign='middle' height='28' >
											<input type='hidden' name='hid_move_status".$result['id']."' id='hid_move_status".$result['id']."' value='".$result['is_navigation']."'>
											<input type='checkbox' name='record_ids[]' id='record_id_".$result['id']."' value='".$result['id']."' onClick='javascript: selectRow(".$result['id'].",\"".$bg."\");' /></a>
										</td>";	
				$field_count=0;
				$size_count=0;
				
				foreach($result as $key=>$value) 
				{
					//print_r($_GET);
					if($key!='id' && $key!='is_active' &&  $key!='count_pages' && $key!='is_navigation')
					{ 
						if($key=='Status' || $key=='Action' || $key == 'No_of_Subnavigation' || $key=='Template_Type' )
							$align = 'center';
						else
							$align = 'left';
						
						$page_content.= " <td align='".$align."' class='grid' valign='middle' style='padding-left:7px;' >";
						
						
						
						if($countList[$key]['custom']=='template') {
							if($result['count_pages']==0)
								$page_content.= $this->GetTemplateStatus($result['Template_Type'])."</td>";
							else
								$page_content.= "-</td>";
						} else if($countList[$key]['custom']=='service') {
							$serviceArr = $this->getServiceOptions($result['Service_Name']);
							$service_name = $serviceArr['service_name'];
							if($service_name=='')	$service_name = 'Direct Pay';															
							
							$page_content.= $service_name. "</td>";;
							
						}else{
							if($_GET['act']=="search" && $key=='Page_Name'  && $_GET['search_id']==$result['id']) {
								$page_content.= "<span class='highlight'>".stripslashes($result[$key]). "</span></td>";
							}elseif($key=='Total_Donation_Amount'){
								$page_content.= '$'.stripslashes($result[$key]). "</td>";
							}else{
								$page_content.= stripslashes($result[$key]). "</td>";
							}
						}
						
					
												
						if($is_page_header=="") 
						{
							if($sizeArray[$size_count]!="")
							{
								$width='width="'.$sizeArray[$size_count].'"';
							}
							else
							{
								$width="";
							}
							
							$page_header.='
								<td style="border-top:1px solid #C7C7C7; padding-left:7px;"  class="grid" '.$width.' ';
							if($countList[$key]['table']!="" || $sortArray[$key]=='N') 
							{
								if($key=="Sr")
								{
									$key="Sr.No.";
								}
								if($key == 'Template_Type') $key = 'Template-Type';
								$page_header.='>'.str_replace("_"," ",$key) . '</td>';
							}
							else 
							{
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
									
								$page_header.=' ><a href="'.$this->MakeUrl($this->mCurrentUrl,"sort=".$key.$order_q.$sort_query).'" class="whitetext">'.$replace_string.'';
								
								if($_GET['sort']==$key && $_GET['order']=='a')
									$page_header.='&nbsp;<img src="'.SITE_URL.'images/icons/up.gif" border=0 align="absmiddle" />';
								elseif($_GET['sort']==$key)
									$page_header.='&nbsp;<img src="'.SITE_URL.'images/icons/down.gif" border=0 align="absmiddle" />';
									
								$page_header.='</a></td>';
							}
							
							$size_count++;
						}
						$field_count++;
					}
					// code starts
					
					// Code ends
					
				}
			
				if($help_icon=="")
				{
					$help_icon="\n
						<tr>
							<td colspan='".($action_count+$field_count)."' align='left'>
								<div class='spacerDiv1'></div>
								<div class='containArea' id='borderId'>
								<!--[if lt IE 7]>
									<div class='bottomCurves'>
										<div class='leftCurve'><img src='".SITE_URL."images/admin/topLeftCurve.jpg' /></div>
										<div class='rightCurve'><img src='".SITE_URL."images/admin/topRightCurve.jpg' /></div>
									</div>
								<![endif]-->
								<div class='textDiv'>
									<div class='innerTextDiv'>									
										<table background='#ffffff' width='100%' border=0 height='100%' cellpadding=0 cellspacing=0 >
											<tr>
												<td class='text' valign=middle>
													<table width='100%' border='0' cellpadding='0' cellspacing='0'>
														<tr><td width='2%'><img src='".SITE_URL."images/admin/".$this->mPageName.".jpg' /></td>
															<td class='headingText' ".$custom_width.">&nbsp;".$this->page_heading."</td>
														</tr>
													</table>
												</td>
												<td>".$this->GetHelpIcons($listAction,$action_query)."</td>
											</tr>
										</table>
									</div>
								<div class='clear'></div>
								</div>
								<!--[if lt IE 7]>
									<div class='bottomCurves'>
										<div class='leftCurve'><img src='".SITE_URL."images/admin/bottomLeftCurve.jpg' /></div>
										<div class='rightCurve'><img src='".SITE_URL."images/admin/bottomRightCurve.jpg' /></div>
									</div>
								<![endif]-->
								</div>
								<div class='spacerDiv'></div>
							</td>
						</tr>";
				}
				if($is_page_header=="") 
				{
					 
					$page_header.='
					</tr>';
				}
				
				$page_content.= "
									</tr>
				";
			}
			$page_content.="
			<tr class='nodrag nodrop'>
					<td align=right valign=top class=text colspan='".(($action_count+$field_count)+1)."'>
						<input type='submit' name='bt_submit' value='' style='visibility:hidden;' />View ".$_SESSION['record_no_display']."
					</td>
				</tr>
			";
			/*$page_content.="
			<tr>
					<td align=center valign=top class=text colspan='".($action_count+$field_count)."'>
						<div class='limit'>Display #<select name='limit' id='limit' class='inputbox' size='1' onchange='submitform();'><option value='5' >5</option><option value='10' >10</option><option value='15' >15</option><option value='20' >20</option><option value='25'  selected='selected'>25</option><option value='30' >30</option><option value='50' >50</option><option value='100' >100</option><option value='0' >all</option></select></div>
					</td>
				</tr>
			";*/
			
		}
		
		$page_table.="<tr><td colspan='".($action_count+$field_count)."'> <table  id='table-1' border='0' cellpadding=0 cellspacing=0 align='center' width='100%'>".$page_header.$page_content."</table></td></tr>";
		
		return $help_icon.$page_table;
	}
	function GetHelpIcons($keyArr,$action_query)
	{
		/*echo '<pre>';
		print_r($keyArr);
		echo '</pre>';*/
		$content.='<table border="0" align="right" cellpadding="4" cellspacing="4" class="text" >
           <tr>';
		  
		foreach($keyArr as $key=>$value)
		{
			if($value['type']=='confirm') 
			{
			} 
			elseif($value['type']=='confirm') 
			{
			} 
			else 
			{
			}
			
			$key1=$key;
			
			$key=str_replace("_"," ",$key);
			$title = $key;
			$title = str_replace(array('up','down','sendemail'),array('move up','move down','Send E-mail'),$title);
			$action_key = str_replace(array('up','down'),array('move up','move down'),$key);
			
			if(($this->mAdminPermissions[$this->mPageName][$key] == true || $key=='close') || $_SESSION['sess_superadmin']=='1') {
				$content.='
					
					<td align="center" valign="bottom" class="actionButton" title="Click to '.ucwords($title).'" onMouseOver="this.className=\'actionButton_hover\';" onMouseOut="this.className=\'actionButton\';" onClick="javascript: submitAction(\''.$value['type'].'\',\''.addslashes($value['message']).'\',\''.$value['multi'].'\',\''.$this->MakeUrl($this->mModuleUrl."/".$key1,$action_query).'\',\''.$action_key.'\');">
						<table border="0" align="center" cellpadding="0" cellspacing="0" class="text" width="45">
							<tr>
								<td align="center" valign="bottom"><img src="'.SITE_URL.'images/icons/'.$key1.'.jpg" alt="'.ucfirst($key).'"  border="0" /></td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding-left:5px;">'.ucfirst($key).'</td>
							</tr>
						</table>
					</td>';
			}			 
		}
		$content.='</tr><tr>'.$content2.'</tr></table>';
		return $content;
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
			//echo $message; die;
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
			return @mail($arrValues['EMAIL'],$subject,$message,$headers);
		}
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
	//------------ Method Display Message-----------------------------------------------------------------------//
	//---------------------------------------------------------------------------------------------------------//	
	function GetMsg($msg='', $msg_type='notice'){
		if($msg_type=='err'){
			$class = "message_error";
			$message = "".$msg;
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


/////////////////////////////Generalize  Function GetListingDropDown starts here - surya - //////////////////////////////////
	////Redirect////
	function GetListingDropDown($tbl, $optionValue, $optionDisplayValue, $selectedValue='', $qstring, $condition = '') { 
		$get_result=$this->Select($tbl, $condition, $optionDisplayValue.','.$optionValue, $optionDisplayValue);
		//print_r($get_result);
		if(count($get_result)>0) {
			foreach($get_result as $result) {
				$dropDown .= '<option value="'.$this->MakeUrl($this->mCurrentUrl, $qstring.'='.$result[$optionValue]).'"';
				if($selectedValue == $result[$optionValue])$dropDown .= 'selected="selected"';
				$dropDown .= '>'.htmlentities(stripslashes($result[$optionDisplayValue])).'</option>';
			}
		}
		return $dropDown;
	}//end function GetListingDropDown
	
	/////////////////////////////Generalize function GetFormBasedDropDown starts here - surya -//////////////////////////////////
	//1.$tbl=table name
	//2.$optionValue=value
	//3.$optionDisplayValue=display value
	//4.$selectedValue=selectedvalue
	//5.$condition= where condition parameters
	function GetFormBasedDropDown($tbl, $optionValue, $optionDisplayValue, $selectedValue='', $condition='') { 
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
	}//end function GetFormBasedDropDown
	//////////////////////////////function GetSimpleDropDown ends here - surya -///////////////////////////////////////////
	
	
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
			
			$getRes = $this->Select($table, $primaryKey.' IN ('.$id.')');
					
			if(count($getRes)>0) {
				foreach($getRes as $data_arr) {
 					$result.=$sep.ucfirst(stripslashes(htmlentities($data_arr[$fieldName])));
					$sep=", ";
				}
			}
		} else{ 
			$result="";
		}
		return $result;	
	}//end function getmessagetitle
	
	function getUserNotifications($RecordTitle1, $id, $dbTable, $primaryKey, $field = ''){
		if($_SESSION['is_redirected']==0) return true;
		$RecordTitle1 = ''; //comment this line to display custom RecordTitle1;
		global $notification_type;
		
		$title = $this->GetMsgDetails($id, $dbTable, $primaryKey, $field);			

		if($_GET['act']=='added') {
			$this->info_message="#info#".$RecordTitle1." Record added successfully.";
		}
		elseif($_GET['act']=='updated') {
			$this->info_message="#info#".$RecordTitle1." Record updated successfully.";
			//$this->info_message="#info#".$RecordTitle1."  \"<strong>".$title."</strong>\" information updated successfully.";
		}
		elseif($_GET['act']=='pageadded') {
			$this->info_message="#info#Page Content added successfully.";
		}
		elseif($_GET['act']=='pageupdated') {
			$this->info_message="#info#Page Content updated successfully.";
		}
		elseif($_GET['act']=='deactivated') {
			
			$rec_deactive = $this->GetMsgDetails($_GET['deactive_id'], $dbTable, $primaryKey, $field);
			$rec_active = $this->GetMsgDetails($_GET['active_id'], $dbTable, $primaryKey, $field);
			if(!empty($_GET['deactive_id'])) {
				$id_arr=explode(",",$_GET['deactive_id']); 
			}
			if(!empty($_GET['active_id'])) {
				$idactive_arr=explode(",",$_GET['active_id']);
			}
			$total = count($id_arr)+count($idactive_arr);
			if($total>1) {
				$this->info_message="#info#Records de-activated successfully.";
			} else {
				$this->info_message="#info#Record de-activated successfully.";
			}
			/*if($rec_active!='' && $rec_deactive=='')
				$arr = $rec_active;
			if($rec_deactive!='' && $rec_active=='')
				$arr = $rec_deactive;
			if($rec_active!='' && $rec_deactive!='')
				$arr = $rec_active.', '.$rec_deactive ;*/
			
			/*if($rec_active!="")
				$this->info_message="#info#".$RecordTitle."  \"<strong>".$rec_active."</strong>\" de-activated successfully.<br>";
			if($rec_deactive!="") {
				if(count($id_arr)>1)
					$this->info_message="#info#".$RecordTitle."  \"<strong>".$rec_deactive."</strong>\" are already de-active.";
				else
					$this->info_message="#info#".$RecordTitle."  \"<strong>".$rec_deactive."</strong>\" is already de-active.";
			}*/
		}
		elseif($_GET['act']=='activated') {
			$rec_deactive = $this->GetMsgDetails($_GET['deactive_id'], $dbTable, $primaryKey, $field);
			$rec_active = $this->GetMsgDetails($_GET['active_id'], $dbTable, $primaryKey, $field);
			if(!empty($_GET['active_id'])) {
				$id_arr=explode(",", $_GET['active_id']);
			}
			if(!empty($_GET['deactive_id'])) {
				$idactive_arr=explode(",",$_GET['deactive_id']);
			}
			$total = count($id_arr)+count($idactive_arr);
			if($total>1) {
				$this->info_message="#info#Records activated successfully.";
			} else {
				$this->info_message="#info#Record activated successfully.";
			}
			/*if($rec_deactive!='' && $rec_active=='')
				$arr = $rec_deactive;
			if($rec_active!='' && $rec_deactive=='')
				$arr = $rec_active;
			if($rec_active!='' && $rec_deactive!='')	
				$arr = $rec_active.', '.$rec_deactive ;*/
				
				
			/*if($rec_deactive!="")
				$this->info_message="#info#".$RecordTitle."  \"<strong>".$rec_deactive."</strong>\" activated successfully.<br>";
			
			if($rec_active!="") {
				if(count($id_arr)>1)
					$this->info_message="#info#".$RecordTitle."  \"<strong>".$rec_active."</strong>\" are already active.";
				else
					$this->info_message="#info#".$RecordTitle."  \"<strong>".$rec_active."</strong>\" is already active.";
			}*/
		}
		elseif($_GET['act']=='deleted') { 
			//$error_message="Conference \"<strong>".$_GET['tabtitle']."</strong>\" removed successfully.";
			if($_GET['rcnt']>1)$RecordTitle = 'Records'; else $RecordTitle = 'Record';			
			$this->info_message="#info#".$RecordTitle."  removed successfully.";
		}elseif($_GET['act']=='moveup') { 
			$this->info_message="#info#Record moved up successfully.";
			//$this->info_message="#info#".$RecordTitle." \"<strong>".$title."</strong>\" moved up successfully.";
		}elseif($_GET['act']=='movedown') { 
			$this->info_message="#info#Record moved down successfully.";
			//$this->info_message="#info#".$RecordTitle." \"<strong>".$title."</strong>\" moved down successfully.";
		}elseif($_GET['act']=='error_up') { 
			$this->info_message="#error#Record cannot be moved up.";
			//$this->info_message="#error#".$RecordTitle." \"<strong>".$title."</strong>\" cannot be moved up.";
		} elseif($_GET['act']=='error_down') { 
			$this->info_message="#error#Record cannot be moved down.";
			//$this->info_message="#error#".$RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
		} elseif($_GET['act']=='moved') { 
			$this->info_message="#info#Record moved successfully.";
			//$this->info_message="#error#".$RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
		}elseif($_GET['act']=='moverr') { 
			$this->info_message="#error#Link page can not be moved.";
			//$this->info_message="#error#".$RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
		}elseif($_GET['act']=='invalid') { 
			$this->info_message="#error#Invalid Information Provided.";
			//$this->info_message="#error#".$RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
		} elseif($_GET['act']=='sent') { 
			$this->info_message="#info#New Password sent to user\'s E-mail address.";
			//$this->info_message="#error#".$RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
		} elseif($_GET['act']=='notsent') { 
			$this->info_message="#error#Your request not completed Successfully.";
			//$this->info_message="#error#".$RecordTitle." \"<strong>".$title."</strong>\" cannot be moved down.";
		}
		
		$_SESSION['is_redirected']=0;
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

	//// Function for displaying category tree with links for category listing page
	function GetCategoryLinkTree($catId) {	
		$getRes = $this->Select(TABLE_CATEGORY,"cat_id = '".$catId."'");

		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$cat_name = htmlentities(stripslashes($res['cat_name']));
				if($res['parent_id']!=0) {
					if($catId==$_GET['cat_id'])
						$cat[] = "<div class='nav_text'>".$cat_name."</div>";
					else
						$cat[] = "<div class='nav_text'><a class='tree_link' href='".$this->MakeUrl('admin/category/index/','cat_id='.$res['cat_id'])."'>".$cat_name."</a></div>";
					$parent_id = $res['parent_id'];
					$cat[] = $this->GetCategoryLinkTree($parent_id);
				} else { 
					if($catId==$_GET['cat_id'])
						$cat[] = "<div class='nav_text'>".$cat_name."</div>";
					else	
						$cat[] = "<div class='nav_text'><a class='tree_link' href='".$this->MakeUrl('admin/category/index/','cat_id='.$res['cat_id'])."'>".$cat_name."</a></div>";
				}
			}
		} 
		$cat = array_reverse($cat);
		$cat_tree = implode(" <div class='nav_img'><img src='".SITE_URL."images/inner/arrow.gif' /></div> ",$cat);
		return $cat_tree;
	}
	
	//// Function for displaying category tree for add/edit category page
	function GetCategoryTree($catId) {	
		$getRes = $this->Select(TABLE_CATEGORY,"cat_id = '".$catId."'");

		if(count($getRes)>0) {
			foreach($getRes as $res) {
				if($res['parent_id']!=0) {
					$cat[] = "<div class='nav_text'>".$res['cat_name']."</div>";
					$parent_id = $res['parent_id'];
					$cat[] = "<div class='nav_text'>".$this->GetCategoryTree($parent_id)."</div>";
				} else {
					$cat[] = "<div class='nav_text'>".$res['cat_name']."</div>";
				}
				
			}
		}
		$cat = array_reverse($cat);
		$cat_tree = implode(" <div class='nav_img'><img src='".SITE_URL."images/inner/arrow.gif' /></div> ",$cat);
		return $cat_tree;
	}
	
	
	///// Funciton for deleting categrory, sub-categories upto infinite level
	function deleteCategory($catId) {
		$getResult = $this->Select(TABLE_CATEGORY,"cat_id IN (".$catId.")");
		if(count($getResult) > 0) {
			foreach($getResult as $result) {
				$getRes = $this->Select(TABLE_CATEGORY,"parent_id ='".$result['cat_id']."'");
				if(count($getRes) > 0) {
					foreach($getRes as $res) {
						$this->Update(TABLE_CATEGORY,array('position'=>' position-1'),"position > '".$res['position']."' AND parent_id = '".$res['parent_id']."'");
						$this->deleteCategory($res['cat_id']);
					} // end foreach
				}  // end if
				$this->Update(TABLE_CATEGORY,array('position'=>' position-1'),"position > '".$result['position']."' AND parent_id = '".$result['parent_id']."'");
				
				// Delete From Category Table
				$this->Delete(TABLE_CATEGORY,"cat_id ='".$result['cat_id']."'");
				
				// Delete From Category Table
				$this->Delete(TABLE_PAGE,"cat_id IN (".$result['cat_id'].")");
			} // end foreach
			
		} // end if
	} //end of deleteCategory function
	
	
	
	///// Funciton for deleting categrory, sub-categories upto infinite level
	function displayCategoryTree($catId) {
		$getResult = $this->Select(TABLE_CATEGORY, "parent_id='".$catId."'");
		if(count($getResult) > 0) {
			$sep = "&nbsp;";
			foreach($getResult as $result) {
				$cat .=  $sep."Categoy->".$result['cat_name']."<br>";
				$getRes = $this->Select(TABLE_CATEGORY,"parent_id ='".$result['cat_id']."'");
				if(count($getRes) > 0) {
					foreach($getRes as $res) {
						
						$cat .=  $sep.$sep.$res['cat_name']."<br>";
						$cat .=  $sep.$sep.$this->displayCategoryTree($res['cat_id']);	
					} // end foreach
					
				}  // end if
				
				
			} // end foreach
		} // end if
		return $cat;
	} //end of displayCategoryTree function
	
	function createCategoryTree($parentId=0, $subIdArr) {
		//$condition = " AND is_navigation='y'"; 
		$parentCategories = $this->Select(TABLE_CATEGORY, "parent_id='".$parentId."'".$condition);
		$root_elm='<input type="radio" class="input_radio" value="0" require="0" realname="Select Category" id="root" name="rdoModule">&nbsp;<span class="root_element"><strong><label for="root">Root</label></strong></span>'; 	
		if(count($parentCategories) > 0) {
			$cat.='<ul>';
			if($parentId==0) $cat.=$root_elm;
			foreach($parentCategories as $category) {
					
				if($category['cat_id']==$_GET['id']){
					$cat.= '<li><strong>&nbsp;'.htmlentities(stripslashes($category['cat_name'])).'</strong>';
				} elseif(in_array($category['cat_id'],$subIdArr)) {
					$cat.= '<li>&nbsp;'.htmlentities(stripslashes($category['cat_name']));
				} else {	
					$cat.= '<li><input type="radio" class="input_radio" value="'.$category['cat_id'].'" require="0" realname="Select Category" name="rdoModule" id="rdoModule_'.$category['cat_id'].'">&nbsp;<label for="rdoModule_'.$category['cat_id'].'">'.htmlentities(stripslashes($category['cat_name'])).'</label>';
				}
					
				$cat.=$this->createCategoryTree($category['cat_id'], $subIdArr);
				
				$cat.= '</li>';
					
			}
			$cat.='</ul>';
		}	
		return $cat;
	}
	function GetParentId($catId) {
		$getResult = $this->Select(TABLE_CATEGORY, "cat_id='".$catId."'");
		return $getResult[0]['parent_id'];
	}
	
	function getUpdatedPosition($moveToId, $catId) {
		
		$getRes = $this->Select(TABLE_CATEGORY,"cat_id='".$catId."'","position");
		$position = $getRes[0]['position'];
		
		$parent_id = $this->GetParentId($catId);
		
		$getResult=$this->Select(TABLE_CATEGORY,"cat_id ='".$catId."'","position, parent_id");
			foreach($getResult as $result){
			
				if($position!=0){
					$query="Update ". TABLE_CATEGORY ." SET position = position-1 WHERE position > '".$result['position']."' and parent_id='".$result['parent_id']."'";
					$this->ExecuteQuery($query);
				}
		}
		
		$maxId = $this->Select(TABLE_CATEGORY,"parent_id='".$moveToId."'","position","position desc", 1);
		$upd_position = $maxId[0]['position'] + 1;
		return $upd_position;
	}
	
	function getTemplateDropDown($selectedTemplate) {
		$getRes = $this->Select(TABLE_TEMPLATE);
		if(count($getRes)>0) {
			foreach($getRes as $result) {
				$options .= '<option value="'.$result['template_id'].'"';
				if($result['template_id']==$selectedTemplate) $options .= ' selected="selected"';
				$options .= ' >'.ucwords($result['template_name']).'</option>';
			}
		} // end if
		return $options;
	}
	
	function CategoryMenu(){
		$menu=new Menu(200,38,"#FFFFFF","#000000",'#FFCC33',"#FFFFFF","#FFFFFF");
		$menu->AddMenu("0","root",165,18,"#FE0000","#000000",'#FFFFFF',"#FFFFFF","#FFFFFF",11);
		//-----------------------------------------------------------------------------------------------//
		//-------------------------Sub Menu Category Navigtion-------------------------------------------//
		//-----------------------------------------------------------------------------------------------//
			$count = 1;
			$subhead_catdata=$this->Select(TABLE_CATEGORY." as pt LEFT JOIN ".TABLE_CATEGORY." as ct ON pt.cat_id=ct.parent_id" ,"pt.parent_id=0 and pt.is_active='1' GROUP BY pt.cat_id","count(ct.cat_id) as cnt_child, pt.cat_id, pt.cat_name","cnt_child desc");
			if(count($subhead_catdata)>0){
				foreach($subhead_catdata as $main_cat) {
					$cat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' and parent_id='".$main_cat['cat_id']."'","","cat_name");
			
					if(count($cat_arr)>0) {
						foreach($cat_arr as $cat) {
							
							$scat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' && parent_id='".$cat['cat_id']."'","","cat_name");
							$cat_count=0;
							if(count($scat_arr)>17) {
								$menu->AddMenu($cat['cat_id']."2","More",165,18,"#FF7374","#666666",'#FFFFFF',"#FFFFFF","#FFFFFF",11,-100,-300);
								$menuid=$cat['cat_id']."2";
								 
								foreach($scat_arr as $scat) {
									$href="listings/index/";
									if($cat_count>15) {
										 $menu->AddMenuItem("text",$menuid,ucwords($scat['cat_name']),$this->MakeUrl($href,"cat_id=".$scat['cat_id']));
									}
									$cat_count++;
								}
								if($active_submenu!="") {
									$menu->AddMenuItem("menu",$active_menu,$active_submenu,"javascript: void(0);");
									$active_submenu="";
									$active_menu="";
								}
							}
						}
					}
					if(count($cat_arr)>0) {
						foreach($cat_arr as $cat) {
							
							$scat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' && parent_id='".$cat['cat_id']."'","","cat_name");
							$cat_count=0;
							if(count($scat_arr)>0) {
								$menu->AddMenu($cat['cat_id'],"main".ucwords($cat['cat_name']),165,18,"#FF7374","#666666",'#FFFFFF',"#FFFFFF","#FFFFFF",11,-2,-300);
								$menuid=$cat['cat_id'];
								$submenu_added=0;
								foreach($scat_arr as $scat) {
									$href="listings/index/";
									if($cat_count>50) {
										if($submenu_added==0) {
											$menu->AddMenuItem("menu",$menuid,$menuid."2","javascript: void(0);");	
											$submenu_added=1;
										}
									} else {
										//$menu->AddMenuItem("menu",$menuid,$menuid."2","javascript: void(0);");	
										//$menu->AddMenuItem("text",$menuid,"test".ucwords($scat['cat_name']),$this->MakeUrl($href,"cat_id=".$scat['cat_id']));
										$menu->AddMenuItem("menu",$scat['cat_id'],$cat['cat_id'],"javascript: void(1);");
										
										/*
										$menuid1=$scat['cat_id'];
										//$menu->AddMenuItem("text",$menuid,"here1".ucwords($sscat['cat_name']),$this->MakeUrl($href,"cat_id=".$scat['cat_id']));
										//$menu->AddMenu($scat['cat_id'],"here".ucwords($scat['cat_name']),165,18,"#FF7374","#666666",'#FFFFFF',"#FFFFFF","#FFFFFF",11,-2,-300);
										//$menuid=$scat['cat_id'];
										$sscat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' && parent_id='".$scat['cat_id']."'");
										if(count($sscat_arr)>0) {
											
											foreach($sscat_arr as $sscat)  {
												/*$menu->AddMenu($scat['cat_id'],"main@".ucwords($scat['cat_name']),165,18,"#FF7374","#666666",'#FFFFFF',"#FFFFFF","#FFFFFF",11,-2,-300);
											$menuid=$scat['cat_id'];
												//$menu->AddMenuItem("text",$menuid,"test".ucwords($sscat['cat_name']),$this->MakeUrl($href,"cat_id=".$scat['cat_id']));
											}
											
											//$menu->AddMenuItem("menu","store".$res['cat_id'],$res['cat_id'],"javascript: void(0);");
										} else {
											
										}
										*/
										
									}
									$cat_count++;
								}
								 
							}
						}
					}
					if(count($cat_arr)>0) {
						$menu->AddMenu("store".$main_cat['cat_id'],"root",165,18,"#FD3F3F","#666666",'#FFFFFF',"#FFFFFF","#FFFFFF",13);
						foreach($cat_arr as $cat) {
							$href="javascript: void(0);";
							$scat_arr=$this->Select(TABLE_CATEGORY,"is_active='1' && parent_id='".$cat['cat_id']."'");
							if(count($scat_arr)>0) {
								//$menu->AddMenuItem("menu","store".$main_cat['cat_id'],$cat['cat_id'],"javascript: void(0);");
								$menu->AddMenuItem("menu","store".$main_cat['cat_id'],$cat['cat_id'],"javascript: void(0);");
								
								
							}else {
								$href_subcat="listings/index/";
								$menu->AddMenuItem("text","store".$main_cat['cat_id'],ucwords($cat['cat_name']),$this->MakeUrl($href_subcat,"cat_id=".$cat['cat_id']));
							}
						}
					}
					
					$menu_x["store".$main_cat['cat_id']] =  0;
					$menu_y["store".$main_cat['cat_id']] =  30;
					$menu_obj["store".$main_cat['cat_id']] =  'link_subhead_'.$main_cat['cat_id'];
				}
			}
	
		return array('function'=>$menu->GetMenuFunction(),'call'=>$menu->GetMenuCall($menu_x,$menu_y,$menu_obj));
	}
	
	function GetSubNavigation($parentId, $counter) {
		
		$cat_arr = $this->Select(TABLE_CATEGORY,"parent_id='".$parentId."' and is_active='1'","cat_id, cat_name,parent_id","position");
		/********** Code for setting of dynamic width according to max length of subnavigation **************/
		$char_count = $this->GetMaximumLength($parentId);
		$nav_width = $char_count*11;	
		//echo $nav_width; die;
		if($nav_width>=150) $style = "style='width:".$nav_width."px;'";	else $style=" style=''";
//		if($counter==1) $style .= "style='left:240px;'";
		/************ Code ends here ***************************/
		if(count($cat_arr)>0) {
			
			$str_nav = '<ul>';
			if($counter>0)
				$class = ' class="top-border"';
			foreach($cat_arr as $cat) {
				$cat_name = ucfirst(htmlentities(stripslashes($cat['cat_name'])));
				$cat_name = str_replace('&AMP;','&nbsp;',$cat_name); //convering in lowercase for W3C
				$count = $this->getSubCatCount($cat['cat_id']);
				$seoUrlPageTree = implode('/', $this->getSeoUrlTree($cat['cat_id']));
				if($count == 0)
					$link = $this->MakeUrl($seoUrlPageTree.'/page/index/','cat_id='.$cat['cat_id']);
				else
					$link = 'javascript: void(0);';
				
				$display_name = '<a '.$class.' href="'.$link.'" '.$style.'>'.$cat_name.'</a>';

				$str_nav .= '<li>'.$display_name;
				$counter++;
				if($count>0)	$str_nav .= $this->GetSubNavigation($cat['cat_id'], $counter);
				$str_nav .= '</li>';
			}
			$str_nav .= '</ul>';
		} else {
			$str_nav='';
		}
		return $str_nav;
	}
	
	
	function getSeoUrlTree($cat_id) {
		$cat_arr = $this->Select(TABLE_CATEGORY,"cat_id='".$cat_id."' and is_active='1' and is_navigation='y'","cat_id, cat_name,parent_id","position");
		if(count($cat_arr)>0) {
			foreach($cat_arr as $cat) {
				$cat_name[] = $this->clean_url($cat['cat_name']);
				if($cat['parent_id']!=0){
					$cat_name =  array_merge($this->getSeoUrlTree($cat['parent_id']),$cat_name);
				}
			}
		}
		return $cat_name;
	}
	
	function GetMaximumLength($catId) {
		$cat_arr = $this->Select(TABLE_CATEGORY,"parent_id='".$catId."' and is_active='1'","cat_name");
		if(count($cat_arr)>0) {
			foreach($cat_arr as $cat) {
				$len[] = strlen($cat['cat_name']);
			}
		}
		return max($len);
	}
	
	function getSubCatCount($parentId) {
		$cat_arr = $this->Select(TABLE_CATEGORY,"parent_id='".$parentId."' and is_active='1'","COUNT(*) as cnt");
		return $cat_arr[0]['cnt'];
	}
	
	function getImageFiles($pageId){
		$getImage = $this->Select(TABLE_IMAGE,"cat_id='".$pageId."'","*","image_id");
		if(count($getImage)>0) {
			foreach($getImage as $result)  {
				$title = htmlentities(stripslashes($result['title']));
				if(file_exists(ROOT.DIR_IMAGE_THUMBNAIL.$result['image'])) {
					$img_data .='<div class="div_page_content2_images"><a href="'.SITE_URL.DIR_IMAGE.$result['image'].'" class="lightbox"><img src="'.SITE_URL.DIR_IMAGE_THUMBNAIL.$result['image'].'" alt="'.$title.'" title="'.$title.'" border="0" /></a></div>';
				}
			}
		}
		return $img_data;
	}
	//// Function for displaying category tree for add/edit category page
	function GetMainCategoryId($catId) {	
		$getRes = $this->Select(TABLE_CATEGORY,"cat_id = '".$catId."'");

		if(count($getRes)>0) {
			foreach($getRes as $res) {
				if($res['parent_id']!=0) {
					$cat[] = $res['cat_id'];
					$parent_id = $res['parent_id'];
					$cat[] = $this->GetMainCategoryId($parent_id);
				} else {
					$cat[] = $res['cat_id'];
				}
				
			}
		}
		//$cat = array_reverse($cat);
		$cat_tree = implode(",",$cat);
		return $cat_tree;
	}
	// drop down for paging
	function GetPagingDropDown($typeSelected='') {
		$type_arr=array('5'=>'5','10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30','40'=>'40','50'=>'50','100'=>'100');	
		foreach($type_arr as $key=>$value) {
			$type_data .= '<option value="'.$value.'"';
			if($typeSelected == $value ) $type_data .= ' selected="selected"';
			$type_data .= '>'.$value.'</option>';		
		}
		return $type_data;
	}
	/*=======================================================================================
		1. This function is used to fetching  general meta description
		2. Table used for this function is ctl_web_setting 
		   which is in config file define as  TABLE_WEB_SETTING
	*==========================================================================================*/
	function webSetting(){ 
		$settingsArr = $this->Select(TABLE_WEB_SETTING,"","*");
		foreach($settingsArr as $setting){
			foreach($setting as $key=>$value){ 
				$this->$key = $value;
			}			
		}
	}//end function webSettings
	
	/*=======================================================================================
		1. This function is used to fetching meta description for spesific page
		2. Also fetching Serch google description [this date is used for this project only]
		2. Table used for this function is ctl_page whis is in confi file define as  TABLE_PAGE
	*==========================================================================================*/
	function pageSpesificMetaContent($cat_id)
	{
		if($cat_id!='')
		{
			$user_data = $this->Select(TABLE_PAGE,"cat_id='$cat_id'","google_data,meta_desc,meta_title");
			if(count($user_data) >0 )
			{	
				$this->mSearchGoogle		=	$user_data[0]['google_data'];
				$this->mMetaDescription 	= 	$user_data[0]['meta_desc'];
				if(trim($user_data[0]['meta_title'])!='')
					$this->mMetaTitle =  $this->getSeoCompatibleString($user_data[0]['meta_title']).' - '.$this->mMetaTitle ;
 			} 
		}	
	}// end of pageSpesificMetaContent($cat_id)
	
	function PagingFooterFront($offset,$class='link_admin') {
		global $_SESSION,$_SERVER,$gPagingExtraPara;
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
		return $display_page;
	}
	
	
	//////////////////////// Function for updating SEO variables in config table of specified CMS page /////////////////
	function UpdateSEO($page_name, $seo_arr) {
		$keyvalue_arr=array('txtmetatitle'=>'meta_title', 'txtmetadesc'=>'meta_description', 'txtgoogle'=>'google_data');
		$arr_type=array('txtmetatitle'=>'STRING', 'txtmetadesc'=>'STRING', 'txtgoogle'=>'STRING');
		$seo_arr=$this->MySqlFormat($seo_arr,$arr_type);
		$this->Update(TABLE_CONFIG,$seo_arr," page_name='".$page_name."' and page_type='user'","",$keyvalue_arr);
	}//////////////////// function ends
	
	///////////////////////// Function for checking if pdf, images available if page content and gallery data is blank ///
	function checkContentAvailable($pageId) {
		$getImage = $this->Select(TABLE_IMAGE,"cat_id='".$pageId."'","count(*) as cnt_images");
		$getPdf = $this->Select(TABLE_PDF,"is_active='1' and cat_id='".$pageId."'","count(*) as cnt_pdf");
		$total_count = $getImage[0]['cnt_images'] + $getImage[0]['cnt_pdf'];
		return $total_count;
	}//////////////////// function ends
	
	//////////// Function for displaying admin pages with possible module lists  in add/edit Admin User
	function GetModulesList($selectedArray) {
		$getRes = $this->Select(TABLE_CONFIG,"page_type='admin' and session_type='admin' and page_title!=''","page_title, page_name, sub_modules","page_title");
		$module_data='';
		if(count($getRes)>0) {
			$i=0;
			$module_data='<tr>';
			foreach($getRes as $result) {
				if($i==2) {
					$module_data .='</tr><tr>
						<td colspan=2>
							<hr color="#cccccc" size=1 />
						</td>
					</tr><tr>';	
					$i=0;
				}
				$i++;
				$module_data .= '<td class="textbold" valign="top" width="300" ><input type="checkbox" name="chkModule[]" value="'.$result['page_name'].'" id="Chk_'.$result['page_name'].'"';
				if($selectedArray[$result['page_name']]['allowed']) {
					$module_data .= ' checked="checked"';
					$module_active = '';
				} else $module_active = 'style="display:none;"';
				$module_data .= ' onclick="javascript: showhideSubPermissions(this,\''.$result['page_name'].'\');" />&nbsp;';
				$module_data .= '<label for="Chk_'.$result['page_name'].'">'.ucwords(stripslashes($result['page_title'])).'</label>
				<div '.$module_active.' id="div_sub_per_'.$result['page_name'].'">
					<table border="0" cellspacing="0" cellpadding="0" class="subpertext">
						<tr>
							<td><img src="'.SITE_URL.'images/bullet.gif" /></td>
							<td><input type="checkbox" name="chkModuleRead" value="read" id="Chk_'.$result['page_name'].'_read"';
							$module_data .= ' disabled checked="checked" />&nbsp;</td>
							<td><label for="Chk_'.$result['page_name'].'_read">Read</label>&nbsp;</td>';
							
					$sub_modules = explode(",",$result['sub_modules']);
					foreach($sub_modules as $sub_mod) {
						if(trim($sub_mod)!="") {
						
						$module_data .= '
								<td><input type="checkbox" name="chkModuleAccess'.$result['page_name'].'[]" value="'.$sub_mod.'" id="Chk_'.$result['page_name'].'_'.$sub_mod.'"';
								if($selectedArray[$result['page_name']][strtolower($sub_mod)]) $module_data .= ' checked="checked"';
								$module_data .= ' />&nbsp;</td>
								<td><label for="Chk_'.$result['page_name'].'_'.$sub_mod.'">'.ucwords($sub_mod).'</label>&nbsp;</td>
						';
						}
					}
				$module_data .= '
						</tr>
					</table>
				</div>
				</td>';
			}
			$module_data .='</tr>';
		}
		return $module_data;
		
	} ////////////// Function ends
	
	//////// Function for validating page access Permissions
	function ValidatePermissions($check_type='all') { 
		/*if($check_type=='page') $condition = " and module_name='".$this->mPageName."'";
		else 	$condition = '';
		$permissions=$this->Select(TABLE_ADMIN_PERMISSIONS,"admin_id='".$_SESSION['sess_admin_id']."'".$condition,"*");
		
		if(count($permissions)>0) { 
			$this->mAdminPermissions=array();
			foreach($permissions as $permission) {
				$permission['permissions']=str_replace(array("-"," "),array("","_"),$permission['permissions']);
				$sub_mod_allowed = explode(",",strtolower($permission['permissions']));
				$this->mAdminPermissions[$permission['module_name']]['allowed'] = true;
				$this->mAdminPermissions[$permission['module_name']]['read'] = true;
				$page_config=$this->Select(TABLE_CONFIG,"page_name='".$permission['module_name']."' and page_type='admin'","sub_modules");
				foreach($page_config as $page) {
					$sub_modules = explode(",",strtolower($page['sub_modules']));
					foreach($sub_modules as $sub_mod) {
						$sub_mod=str_replace(array("-"," "),array("","_"),$sub_mod);
						///// Default Setting: Set $this->mAdminPermissions['admin_users']['edit'] = true; for My Profile page of SubAdmins
						$this->mAdminPermissions['admin_users']['edit'] = true;
						if($sub_mod!="read") {
						
								if(in_array($sub_mod,$sub_mod_allowed)) {
									$this->mAdminPermissions[$permission['module_name']][$sub_mod] = true;
									if($sub_mod=="edit") { 
										$this->mAdminPermissions[$permission['module_name']]['activate'] = true;
										$this->mAdminPermissions[$permission['module_name']]['deactivate'] = true;
										$this->mAdminPermissions[$permission['module_name']]['up'] = true;
										$this->mAdminPermissions[$permission['module_name']]['down'] = true;
									} 
								} else {
									$this->mAdminPermissions[$permission['module_name']][$sub_mod] = false;
									if($sub_mod=="edit") { 
										$this->mAdminPermissions[$permission['module_name']]['activate'] = false;
										$this->mAdminPermissions[$permission['module_name']]['deactivate'] = false;
										$this->mAdminPermissions[$permission['module_name']]['up'] = false;
										$this->mAdminPermissions[$permission['module_name']]['down'] = false;
									} 
									//return 0;
								}
							}
						}
					}
				}
				return 1;
			} */
			if($_SESSION['sess_superadmin']=='1') {
				$page_config=$this->Select(TABLE_CONFIG,"page_type='admin'","page_name, sub_modules");
				foreach($page_config as $page) { //print_r($page);
				//echo $page['sub_modules']."<br>";
					$sub_modules = explode(",",strtolower($page['sub_modules']));
					foreach($sub_modules as $sub_mod) {  //echo $page['page_name']."==".$sub_mod;
						$this->mAdminPermissions[$page['page_name']]['allowed'] = true;
						$this->mAdminPermissions[$page['page_name']]['read'] = true;
						$this->mAdminPermissions[$page['page_name']][$sub_mod] = true;
						$this->mAdminPermissions[$page['page_name']]['activate'] = true;
						$this->mAdminPermissions[$page['page_name']]['deactivate'] = true;
						$this->mAdminPermissions[$page['page_name']]['up'] = true;
						$this->mAdminPermissions[$page['page_name']]['down'] = true;
					}
				}
				return 1;
			}
			return 0;
		}   /////////// Function ends
		
	    ////////  Function for checking id add page is for landing page or not... return true for parent category or category of type landing
		function ValidateLandingPage($parentId) {
			if($parentId) {
				$getRes = $this->Select(TABLE_CATEGORY,"cat_id='".$parentId."'",'is_landing');
				if($getRes[0]['is_landing']=='y') {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
			return false;
		} ///////// Function ends
		
		////// Function for delete Landing Page Contents before adding submenu
		function DeleteLandingPageContent($catId) {
			$this->Delete(TABLE_PAGE,"cat_id='".$catId."'");
		}
		
		
		//////////// Function to display landing page section with title and description on home page....
		function getLandingPageData() {
			$getRes = $this->Select(TABLE_CATEGORY." as ct LEFT JOIN ".TABLE_PAGE." as pt ON ct.cat_id=pt.cat_id","display_homepage='y' and ct.is_active='1'","pt.cat_id, page_id, cat_name, heading, description","position");
			$count = count($getRes);
			if(count($getRes)>0) {
				$data = '<div class="content">';
				$i = 1;
				$j = 0;
				foreach($getRes as $result) {
					$j++;
					if($i==1 && $count!=1) $class = 'first'; else $class='second';
					$seoUrlPageName = $this->clean_url($result['cat_name']);
					$data .='<div class="'.$class.'"><strong>'.stripslashes(htmlentities($result['heading'])).'</strong><br />
								'.stripslashes(htmlentities($result['description'])).'<br />
								<a target="_blank" href="'.$this->MakeUrl($seoUrlPageName.'/page/index/',"cat_id=".$result['cat_id']).'">Learn More</a></div>';
					
					if($i==2 && $j!=$count) {
						$i = 0;
						$data .= '</div>
								  <div class="clear"></div>
								  <div class="middle"></div>
								  <div class="clear"></div>
								  <div>';
					}
					$i++;
				}
				$data .='</div>';		  
			}
			return $data;
		}
		
		////////// Function to Return Parent Caegory name from database
		function GetParentCategory($catId) {
			$getResult = $this->Select(TABLE_CATEGORY, "cat_id='".$catId."'","cat_name");
			return $getResult[0]['cat_name'];
		}
		
		
	/*************************************************************************************************
		Online visitor code starts here
	*************************************************************************************************/
	
	function updateOnlineUsersCount(){
		$timestamp = date("U");
		$sessionID = session_id();
		
		$getRes = $this->Select(TABLE_USER_ONLINE,"sessionID='".$sessionID."'","*");
		if(count($getRes)>0) {
			$this->Update(TABLE_USER_ONLINE,array('timestamp'=>"'".$timestamp."'",'sessionID'=>"'".$sessionID."'"), "sessionID='".$sessionID."'");
		} else {			
			$post_arr	=	array('timestamp'=>"'".$timestamp."'",'sessionID'=>"'".$sessionID."'");
			$this->Insert(TABLE_USER_ONLINE,$post_arr);
		}
	
	}
	
	function count_users() {
		$getRes = $this->Select(TABLE_USER_ONLINE,"","COUNT(*) as cnt");
		return  $getRes[0]['cnt'];
	}
	
	/*************************************************************************************************
		Online visitor code ends here
	*************************************************************************************************/	
	//// Function for displaying buttons in left panel on landing pages
	function GetLandingPageFormData($catId) { 
		$getRes = $this->Select(TABLE_FORM_PAGE." as pt LEFT JOIN ".TABLE_FORMS." as ft ON pt.form_id=ft.form_id","pt.cat_id='".$catId."'","ft.form_id, ft.link_text","ft.form_id");
		if(count($getRes)) {
			foreach($getRes as $res) { 
				$formData .= '<div class="btn" onclick="javascript: OpenForm(\''.$this->MakeUrl('form/index/','form_id='.$res['form_id'].'&page_id='.$_GET['cat_id']).'\');">'.$res['link_text'].'</div>
							  <div class="btn_gap"></div>';
			}
		}
		return $formData;
	}//// ends
	
	
	////////// function for Get Sample Submission Forms Layout
	function GetSampleSubmissionForms($catId, $total_pdf) {
		$getPdf = $this->Select(TABLE_LANDING_PDF,"cat_id='".$catId."'","*","pdf_id"); 
		if(count($getPdf)>0) {
			foreach($getPdf as $pdf) { 
				$title_arr[] = $pdf['pdf_title'];
				$pdf_arr[] = $pdf['pdf_file'];
				$id_arr[] = $pdf['pdf_id'];
			}
		}
		//print_r($pdf_arr); die;
		$data = '<table width="100%" cellpadding="1" cellspacing="1" border="0">
					<tr><td colspan="4" align="left"><U><strong>Note:</strong></U> You may upload .pdf only.</td></tr>
					<tr><td height="10"></td></tr>';
		for($i=1; $i<=$total_pdf; $i++) {
			$data .= $this->AddFormField('hidden','pdf_old[]',$pdf_arr[$i-1]);
			$data .= $this->AddFormField('hidden','pdfid[]',$id_arr[$i-1]);
			$data .= '<tr>
						<td width="14%" class="textbold"><label for="txtpdftitle'.$i.'">PDF Title '.$i.': </label></td>
						<td ><input type="text" name="txtpdftitle[]" id="txtpdftitle'.$i.'" value="'.htmlentities(stripslashes($title_arr[$i-1])).'" class="input-3" errorClassName="input-3_error" realname="PDF Title '.$i.'" require="0" regexp="JS_BLANK" maxlength=255 /></td>
					  
						
						<td width="18%" class="textbold"><label for="txtpdffile'.$i.'">Upload PDF '.$i.': </label></td>
						<td width="35%"><input type="file" name="txtpdffile[]" id="txtpdffile'.$i.'" value="" class="input-4" errorclassname="input-4_error" realname="Upload PDF '.$i.'" require="0" regexp="JS_FILE_PDF" maxlength=255  /></td>
					  </tr>';
			
			if($pdf_arr[$i-1]!='' && file_exists(ROOT.DIR_LANDING_PDF.$pdf_arr[$i-1])) { 
					$data .= '<tr>
								<td colspan="3">&nbsp;</td>
								<td><a href="'.SITE_URL.DIR_LANDING_PDF.$pdf_arr[$i-1].'" target="_blank" id="'.$id_arr[$i-1].'" class="link"><img border="0" src="'.SITE_URL.'images/pdf_small.gif" title="View uploaded file"/></a>&nbsp;&nbsp;<a href="" id="'.$id_arr[$i-1].'" class="remove_pdf"><img src="'.SITE_URL.'images/icons/delete.gif" title="Remove" border="0" /></a></td>
 							  </tr>';
							} 
				if($i!=$total_pdf) {
					$data .= '<tr><td height="10"></td></tr>
							 <tr>
								<td colspan="4" class="seperator">&nbsp;</td>
 							 </tr>
							 <tr><td height="10"></td></tr>';
				}
						 } 
				$data .= '</table>';
			return $data;
	}
	// displays landing PDF files 
	function landingPdf($catId)
	{
		$getPdf = $this->Select(TABLE_LANDING_PDF,"cat_id='".$catId."'","*","pdf_id");
		if(count($getPdf)>0)
		{
			$pdf_content	.='<div class="btn_large"><span class="download_text">Download Sample</span> <br /><span class="download_text">Submission Forms </span><br />';
			foreach($getPdf as $pdf) 
			{ 
				$pdf_content	.=	'<a href="'.SITE_URL.DIR_LANDING_PDF.$pdf['pdf_file'].'" target="_blank">'.$pdf['pdf_title'].'</a><br />';
			}
			$pdf_content	.='</div>';
		} 
		return $pdf_content;
	}
	
	////////
	function GetpageTypeDropDown($typeSelected) {		
		$type_arr = array('template'=>'Templates', 'landing'=>'Landing Pages');
		foreach($type_arr as $key=>$value) {
			$type_data .= '<option value="'.$this->MakeUrl('admin/'.$this->mPageName.'/index/',$action_query.'type='.$key).'"' ;
			if($typeSelected == $key) $type_data .= ' selected="selected"';
			$type_data .= '>'.$value.'</option>' ;
		}
		return $type_data;
	}
	
	function GetIsLandingPage($catId) {
		$getResult = $this->Select(TABLE_CATEGORY, "cat_id='".$catId."'","is_landing");
		return $getResult[0]['is_landing'];
	}
	function GetIsNavigationPage($catId) {
		$getResult = $this->Select(TABLE_CATEGORY, "cat_id='".$catId."'","is_navigation");
		return $getResult[0]['is_navigation'];
	}
	
	function GetTemplateStatus($catId) {
		$getRes = $this->Select(TABLE_PAGE,"cat_id='".$catId."'","template_id");
		if(count($getRes)>0) {
			if($getRes[0]['template_id']==1) {
				$status = '<img src="'.SITE_URL.'images/icons/template1.gif" title="Template 1" />';
			} else if($getRes[0]['template_id']==2)  {
				$status = '<img src="'.SITE_URL.'images/icons/template2.gif" title="Template 2" />';
			} 
		} else {
			$status = '-';
			//$status = '<img src="'.SITE_URL.'images/icons/under_construction.gif" />';
		}
		
		//if($this->GetIsNavigationPage($catId)=='n') $status .='&nbsp;<img src="'.SITE_URL.'images/icons/link.gif" title="Link Page" />';
		
		return $status;
	}
	function trimArray($arr){
		foreach($arr as $element){									
			if((trim($element)!='')){
				$newArr[] = $element;
			}			
		}//end for
		return $newArr;
	}
	///// Funcition to return option of dropdown of forms
	function GetFormsLinkDropDown($selectedForm) {
		/*$formRes = $this->Select(TABLE_FORMS,"","form_id, form_name");
		foreach($formRes as $form_arr) {
			$form_data .= '<option value="'.$this->MakeUrl('admin/'.$this->mPageName.'/index/',$action_query.'form_id='.$form_arr['form_id']).'"' ;
			if($selectedForm == $form_arr['form_id']) $form_data .= ' selected="selected"';
			$form_data .= '>'.$form_arr['form_name'].'</option>' ;
		}
		return $form_data;*/
		$formArr = array('contactus'=>'Contact Us','survey'=>'Survey Form');
		foreach($formArr as $key=>$value) {
			$form_data .= '<option value="'.$this->MakeUrl('admin/'.$this->mPageName.'/index/',$action_query.'form='.$key).'"' ;
			if($selectedForm == $key) $form_data .= ' selected="selected"';
			$form_data .= '>'.$value.'</option>' ;
		}
		return $form_data;
	}
	
	///// Funcition to return submit type from forms table
	function GetFormType($formId) {
		$formRes = $this->Select(TABLE_FORMS,"form_id='".$formId."'","submit_type");
		return $formRes[0]['submit_type'];
	}
	
	/////////// Funcion to delete Form data from Database
	function deleteForm($pageId) {
		//// Delete from REquest a Quote table
		$this->Delete(TABLE_QUOTETION,"id IN (".$pageId.")");
		
		//// Delete from Analysis REquest table
		$this->Delete(TABLE_ANALYSIS_REQUEST,"insert_id IN (".$pageId.")");
		
		//// Delete from Schedule a Consultation table
		$this->Delete(TABLE_SCHEDULE_CONSULTATION,"id IN (".$pageId.")");
	}
		
	
	function getPageName($pageId) {
		$getRes = $this->Select(TABLE_CATEGORY, "cat_id='".$pageId."'","cat_name");
		return $getRes[0]['cat_name'];
	}
	
	///// Funcition to return form name from forms table
	function GetFormName($formId) {
		$formRes = $this->Select(TABLE_FORMS,"form_id='".$formId."'","form_name");
		return $formRes[0]['form_name'];
	}
	
	function displayValue($value) {
		if(trim($value)=='')
			return "-";
		else 
			return stripslashes($value);
	}
	
	function GetInformationType($info_id) {
		$getRes = $this->Select(TABLE_INFO_TYPE,"type_id='".$info_id."'","info_type");
		return $getRes[0]['info_type'];
	}
	
	function GetCategoryName($catId) {
		$getRes = $this->Select(TABLE_CATEGORY,"cat_id='".$catId."'","cat_name");
		return $getRes[0]['cat_name'];
	}
	
	function UpdateLinkUrls($oldUrl, $newUrl) {
		$this->Update(TABLE_PAGE,array('page_content'=>'REPLACE(page_content, "'.$oldUrl.'", "'.$newUrl.'")'),"page_content LIKE '%".$oldUrl."%'");
	}
	
	function GetPagingOffset($pId, $catId) {
		$i = 1;
		$j = 0;
		$getRes = $this->Select(TABLE_CATEGORY,"parent_id='".$pId."'","cat_id","position");
		if(count($getRes)>0) {
			foreach($getRes as $r) {
				if($catId==$r['cat_id']) $j = $i;
				$i++;
			}
		}
		
		$offset = ceil(($j / $this->paging_limit));
		
		if($offset>=1) 	$offset = $offset-1;
		return $offset;
	}
	function getChildIdArray($catId) {
		$subCategories = $this->Select(TABLE_CATEGORY, "parent_id='".$catId."'");
		//$subcat = array();
		if(count($subCategories)>0) {
			foreach($subCategories as $res) {
				$subcat .= ",".$res['cat_id'];
				$subcat .= $this->getChildIdArray($res['cat_id']);
			}
		}
		return $subcat;
	}
	
	function formatOrderId($orderId) {
		$oId = ORDERID_PREFIX.sprintf("%06d",$orderId);
		return $oId;
	}
	
	function GetEthnicity($ethnicityId) {
		$getRes = $this->Select(TABLE_ETHNICITY, "ethnicity_id='".$ethnicityId."'");
		return $getRes[0]['ethnicity'];
	}
	
	function GetColleges($collId) {
//		echo $collId; die;
		$getRes = $this->Select(TABLE_COLLEGES, "college_id='".$collId."'");
		//print_r($getRes); die;
		return $getRes[0]['college'];
	}
	
	function GetMajors($majorId) {
		$getRes = $this->Select(TABLE_MAJORS, "majors_id='".$majorId."'");
		return $getRes[0]['majors'];
	}
	function GetCountyDD($county) {
		//echo $county; die;
		$getRes = $this->Select(TABLE_COUNTY, "", "*");
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$options .= '<option value="'.$res['county'].'" ';
				if($county==$res['county']) $options .= ' selected="selected" ';
				$options .= ' >';			
				$options .= $res['county'];			
				$options .= ' </option>';			
			}
		}
		return $options;
	}
	function GetEthnicityDD($selethnicityId) {
		$getRes = $this->Select(TABLE_ETHNICITY, "", "*");
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$options .= '<option value="'.$res['ethnicity_id'].'" ';
				if($selethnicityId==$res['ethnicity_id']) $options .= ' selected="selected" ';
				$options .= ' >';			
				$options .= $res['ethnicity'];			
				$options .= ' </option>';			
			}
		}
		return $options;
	}
	function GetCollegesDD($selcollId) {
		$getRes = $this->Select(TABLE_COLLEGES, "", "*");
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$options .= '<option value="'.$res['college_id'].'" ';
				if($selcollId==$res['college_id']) $options .= ' selected="selected" ';
				$options .= ' >';			
				$options .= $res['college'];			
				$options .= ' </option>';			
			}
		}
		return $options;
	}
	
	function getNumberOfScholars($donattionAmount){
		$getRes = $this->Select(TABLE_AMT_RANGE, $donattionAmount." BETWEEN `min_amt` AND `max_amt` ","no_of_students");
		$resArr = $this->getMaxDonationAmount();
		if(count($getRes)>0) {
			$no_students = $getRes[0]['no_of_students'];
			$status = 'success';
			$show_sign = '';
		}else if(count($getRes)==0 && $donattionAmount > $resArr['max_amt']){
			$no_students = ($resArr['no_of_students']+1);
			$show_sign = '+';
			$status = 'success';
		}
		$ret_arr = array('status'=>$status, 'no_students'=>$no_students, 'show_sign'=>$show_sign);
		//print_r($ret_arr); die;
		return $ret_arr;
	}
	
	function getMaxDonationAmount() {
		$getRes = $this->Select(TABLE_AMT_RANGE, "","max_amt, no_of_students","  max_amt desc","1");
		foreach($getRes as $res) {
			$row = $res;
		}
		return $row;
	}
	
	function generateSimPaymentForm($donationAmount, $insert_id){
		$loginID = AUTH_LOGINID;
		$transactionKey = AUTH_TRANSACTION_KEY;
		$relay_url = AUTH_RELAY_URL;
		
		$description    = isset($_REQUEST["description"]) ? $_REQUEST["description"] : $description;
		$invoice       = $this->formatOrderId($insert_id);
		$sequence      = rand(1, 1000);
		$timeStamp     = time ();
		if( phpversion() >= '5.1.2' ){
			$fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $donationAmount ."^", $transactionKey); 
		}else{ 
			$fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" .$donationAmount . "^", $transactionKey));
		}
		
		$sim_fields .= "<form name='frm_payment' id='frm_payment' method='post' action='".AUTH_POST_URL."'>";
		$sim_fields .= "<INPUT type='hidden' name='x_login' value='$loginID' />";
		$sim_fields .= "<INPUT type='hidden' name='x_amount' value='$donationAmount' />";
		$sim_fields .= "<INPUT type='hidden' name='x_insert_id' value='$insert_id' />";
		//$sim_fields .= "<INPUT type='hidden' name='x_description' value='$description' />";
		$sim_fields .= "<INPUT type='hidden' name='x_invoice_num' value='$invoice' />";
		$sim_fields .= "<INPUT type='hidden' name='x_fp_sequence' value='$sequence' />";
		$sim_fields .= "<INPUT type='hidden' name='x_fp_timestamp' value='$timeStamp' />";
		$sim_fields .= "<INPUT type='hidden' name='x_fp_hash' value='$fingerprint' />";
		$sim_fields .= "<INPUT type='hidden' name='x_test_request' value='$testMode' />";
		$sim_fields .= "<INPUT type='hidden' name='x_show_form' value='PAYMENT_FORM' />";
		//$sim_fields .= "<input type='submit' value='$label' />";
		//$sim_fields .= "<input type='hidden' name='x_relay_response' value='TRUE' />";
		//$sim_fields .= "<input type='hidden' name='x_relay_URL' value='$relay_url' />";
		$sim_fields .= "</form>";
	
		return $sim_fields;
	}
	
	function GetMajorsDD($selmajorId) {
		$getRes = $this->Select(TABLE_MAJORS, "", "*");
		if(count($getRes)>0) {
			foreach($getRes as $res) {
				$options .= '<option value="'.$res['majors_id'].'" ';
				if($selmajorId==$res['majors_id']) $options .= ' selected="selected" ';
				$options .= ' >';			
				$options .= $res['majors'];			
				$options .= ' </option>';			
			}
		}
		return $options;
	}
	
	function GetPaymentForm() {
		$payment_form = file_get_contents(SITE_URL.'html/payment_form.html');
		$sess_arr = $_SESSION['sess_donation'];
		//print_r($sess_arr); die;
		$search_arr = array('[FORM_ACTION]','[SITE_URL]','[SESS_NAME]','[SESS_SNAME]','[SESS_ADDRESS]','[SESS_PHONE1]','[SESS_PHONE2]','[SESS_PHONE3]','[SESS_FAX1]','[SESS_FAX2]','[SESS_FAX3]','[SESS_EMAIL]');
		$replace_arr = array($this->MakeUrl('payment/step2/'),SITE_URL,$sess_arr['txt_name'],$sess_arr['txt_sname'],$sess_arr['txt_address'],$sess_arr['txt_phone1'],$sess_arr['txt_phone2'],$sess_arr['txt_phone3'],$sess_arr['txt_fax1'],$sess_arr['txt_fax2'],$sess_arr['txt_fax3'],$sess_arr['txt_email']);
		$payment_form = str_replace($search_arr,$replace_arr,$payment_form);
		return $payment_form;
	}
	
	function SendReceipt($orderId, $type, $subjectType='') {
		$transactionArr=$this->Select(TABLE_TRANSACTIONS,"transaction_id='".$orderId."'","*","");
		$user_email = $transactionArr[0]['email'];
		$service_id = $transactionArr[0]['service_id'];
		
		$transactionMailBody = $this->getTransactionEmailTemplate($transactionArr, $type, $subjectType);		
		
		$emailBody = $transactionMailBody;
		
		if($subjectType=='' || $subjectType=='new') {
			$order_subject="Payment Confirmation from ".html_entity_decode(SITE_NAME);
				$data_arr['comments']="-";
		} elseif($subjectType=='notify') {
			$order_subject="Order ".$this->formatOrderId($orderId)." Status from ".html_entity_decode(SITE_NAME);
		} 
		if($type=='user'){
			$email = $user_email;
		}elseif($type=='admin'){
			$email = $this->GetAdminEmail();
			if((trim($transactionArr[0]['questions'])!='' || trim($transactionArr[0]['comments'])!='') && $subjectType!='notify') {
				$this->SendQuestionAndComments($orderId, $transactionArr[0]['name'], $transactionArr[0]['questions'], $transactionArr[0]['comments']);
			}
		}
		$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
		//if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
		if(BCC_EMAIL!="")
			$headers .= 'Bcc: ' . BCC_EMAIL . " \r\n";
			
		///////////// Set headers variable for attachment	
		
		if(count($attachment)>0) {
			$mime_boundary = "B_".md5(time())."";
			$headers .="Content-type: multipart/mixed;
	".'boundary="'.$mime_boundary.'"' . "\r\n";
			$headers .="--".$mime_boundary."\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			//echo $message; exit;
			$headers .= $emailBody;
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
			$emailBody = "";			
		} else {
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}
		//echo $emailBody."<br />".$email.'<br />'.$order_subject; die;
		return @mail($email,$order_subject,$emailBody,$headers);
	}
	
	
	function SendQuestionAndComments($order_no, $donar, $question, $comments){
		$question = $question ? stripslashes($question) : '-';
		$comments = $comments ? stripslashes($comments) : '-';		
		$emailBody = '<style type="text/css">
								.order_border {
								border:1px solid #000000;
								}
								.text{
									font-family:Verdana;
									font-size: 11px;
									color:#000000;
								}
					  </style>
					<table width="700" border="0" cellspacing="0" cellpadding="0" align="center" class="order_border">
						<tr>
							<td><img src="'.SITE_URL.'images/mail-header.jpg" alt="" title="" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="text" style="padding-left:3px;">We have recorded following question and comments for '.ucwords($donar).' scholarship program:</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="text" style="padding-left:3px;"><strong>Order# </strong>'.$this->formatOrderId($order_no).'</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="text" style="padding-left:3px;"><strong>Question: </strong>'.nl2br(stripslashes($question)).'</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="text" style="padding-left:3px;"><strong>Comments: </strong>'.nl2br(stripslashes($comments)).'</td>
						</tr>
						<tr>
							<td height="30">&nbsp;</td>
						</tr>
						<tr>
							<td class="text" style="padding-left:3px;">'.nl2br(stripslashes($this->signature)).'</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
					';
		$subject= ucwords($donar)." Scholarship Program";
		$email = $this->comments_e_mail;
		if($email==''){
			$email = $this->GetAdminEmail();;
		}
		$headers = 'From: '.html_entity_decode(SITE_NAME).' <' . NOREPLY_EMAIL . '>' . "\r\n";
		//if($cc!="") $headers .= 'Cc: ' . $cc . " \r\n";
		if(BCC_EMAIL!="")
			$headers .= 'Bcc: ' . BCC_EMAIL . " \r\n";
			

		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		return @mail($email,$subject,$emailBody,$headers);
	}
	
	function getTransactionEmailTemplate($transactionArr, $type, $subjectType){
		$ref_id = $transactionArr[0]['ref_id'];
		$transaction_type = $transactionArr[0]['transaction_type'];
		if($transaction_type!='check') {
			$transaction_type = 'Credit Card';
			if($transactionArr[0]['is_processed']=='y')	$status = 'Success'; else $status = 'Fail';
		} else {
			$transaction_type = 'Check';
			$status = ucfirst($transactionArr[0]['transaction_status']);
		}
		$transaction_type = ucwords(str_replace('_','',$transaction_type));
		$tranaction_id	= $this->formatOrderId($transactionArr[0]['transaction_id']);
		$transaction_date = $transactionArr[0]['transaction_date'];
		$amount = number_format($transactionArr[0]['donation_amount'],2);
		$card_holder_name = $transactionArr[0]['card_holder_name'];
		$card_holder_address = $transactionArr[0]['card_holder_address'];
		
		$name = $transactionArr[0]['name'];
		$scholarship_name = $transactionArr[0]['scholarship_name'];
		$email = $transactionArr[0]['email'];
		$donation_amount = number_format($transactionArr[0]['donation_amount'],2);
		$donation_amount_after_deduction = number_format($transactionArr[0]['donation_amount_after_deduction'],2);
		$admin_fee_percent = $transactionArr[0]['admin_fee_percent'];
		$admin_fee = number_format($transactionArr[0]['admin_fee'],2);
		$no_of_students = $transactionArr[0]['no_of_students'];
		$show_sign = $transactionArr[0]['show_sign'];
		$criteria = $transactionArr[0]['criteria'];

		$county = $transactionArr[0]['county'];
		$ethnicity = $transactionArr[0]['ethnicity_id'];
		$college = $transactionArr[0]['college_id'];
		$majors = $transactionArr[0]['majors_id'];
		$highschool = $transactionArr[0]['highschool'];
		$gender = $transactionArr[0]['gender'];
		$religious_affiliation = $transactionArr[0]['religious_affiliation'];
		
		if($county) $criteria_b = '<li> County: '.$county.'</li>';
		if($gender) $criteria_b .= '<li>Gender: '.str_replace(array('m','f'),array('Male','Female'),$gender).'</li>';
		if($ethnicity) $criteria_b .= '<li>Ethnicity: '.$ethnicity.'</li>';
		if($college) $criteria_b .= '<li>Colleges: '.$college.'</li>';
		if($majors)$criteria_b .= '<li>Majors: '.$majors.'</li>';
		if($highschool) $criteria_b .= '<li>High School: '.$highschool.'</li>';
		if($religious_affiliation) $criteria_b .= '<li>Religious Affiliation: '.$religious_affiliation.'</li>';
	
		$comments = $transactionArr[0]['comments'];
		$questions = $transactionArr[0]['questions'];
		
		$gateway_transaction_id = $transactionArr[0]['gateway_transaction_id'];
		$city = $transactionArr[0]['city'];
		$state = $transactionArr[0]['state'];
		$zip = $transactionArr[0]['zip'];
		$fax = $transactionArr[0]['fax'];
		$address = $transactionArr[0]['address'];
		$phone = $transactionArr[0]['phone'];
		$comment2 = $transactionArr[0]['comment2'];	
		$comments_to_donar = $transactionArr[0]['comments_to_donar'];	
		
		if($type=='user' && $subjectType!='notify'){
			$msg_string = 'Hello '.ucwords($name).', <br />Thank You. Your payment has been successfully received with the following details. Please quote your transaction reference number for any queries relating to this request.';
		}elseif($type=='admin'  && $subjectType!='notify'){
			$msg_string = 'Hello Administrator, <br /><br />We have recorded the following Payment information for you:';
		}elseif($type=='user' && $subjectType=='notify'){
			$msg_string = 'Hello '.ucwords($name).', <br />We have recorded the following Payment Notification information for you:';
		}elseif($type=='admin'  && $subjectType=='notify'){
			$msg_string = 'Hello Administrator, <br /><br />We have recorded the following Payment Notification information for you:';
		}
			
		$transactionEmailTemplate='<style type="text/css">
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
						text-decoration:none;
					}
					.order_link_green:Hover {
						cursor:pointer; 
						font-family: verdana; 
						font-size: 8pt; 
						font-weight:bold; 
						text-decoration:none;
					}
					.heading{
						font-family:Verdana, Arial, Helvetica, sans-serif;
						font-size: 12px;
						color:#FFFFFF;
						padding-left:5px;
						background-color:#7F4537;
						font-weight:bold;
					}
					.textContent{
						font-family:Verdana;
						font-size: 11px;
						padding-top: 10px; padding-left:5px;
						color:#000000;
					}
					.headingLeft{
						font-family:Verdana, Arial, Helvetica, sans-serif;
						font-size: 11px;
						padding-top: 5px; padding-left:5px;
						color:#666666;
						font-weight:bold;
					}
				</style>
				<table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="'.$bgColor.'" align="center" class="order_border">
				  <tr>
						<td><img src="'.SITE_URL.'images/mail-header.jpg" alt="" title="" /></td>
				 </tr>
				  <tr>
					  <td colspan="2" width="100%" align="left" class="textContent">'.$msg_string.'</td>
				  </tr>
				  <tr>
					  <td colspan="2" height=8></td>
				  </tr>
				  <tr>
					<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
						<tr>
						  <td width="100%" align="left" class="heading" bgcolor="#750073" colspan="2" valign="middle" height="27"><strong>Payment Information</strong></td>
						</tr>
						
						<tr>
						  <td colspan="2" height=8>
							<table width="100%" border="0" cellspacing="2" cellpadding="2">';
		$transactionEmailTemplate.='
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Order No:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">['.$tranaction_id.']</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Transaction Type:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$transaction_type.'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Transaction Status:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$status.'</td>
							  </tr>'.(($transaction_type!='Check' && $gateway_transaction_id!='') ?'
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Transaction ID :</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$gateway_transaction_id.'</td>
							  </tr>' : '').'
						  		<tr>
								<td  width="40%" align="left" class="headingLeft">Transaction Date:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($transaction_date).'</td>
							  </tr>'.(($subjectType=='notify')?'
							  <tr>
								<td  width="40%" align="left" class="headingLeft" valign="top">Comments:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue(nl2br($comments_to_donar)).'</td>
							  </tr>
							  ' : '').'
							  <tr>
								<td height="1" bgcolor="#7F4537" colspan="2"></td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Name:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue(ucwords($name)).'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Scholarship Name:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue(ucwords($scholarship_name)).'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Total Donation:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">$'.$donation_amount.'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Scholarship:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">$'.$donation_amount_after_deduction.'</td>
							  </tr>
							  
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Admin Fee In Percent:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$admin_fee_percent.'%</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Admin Fee:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">$'.$admin_fee.'</td>
							  </tr>
							   <tr>
								<td  width="40%" align="left" class="headingLeft">Number Of Students:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($no_of_students.$show_sign).'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Address:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($address).'</td>
							  </tr>
							  
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Phone:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($phone).'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Fax:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($fax).'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Email:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($email).'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">Selected Criteria:</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue(str_replace(array('optiona','optionb'),array('Option A','Option B'),$criteria)).'</td>
							  </tr>
							  <tr>
								<td  width="40%" align="left" class="headingLeft">&nbsp;</td>
								<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">
									<ul>
										<li>Qualify for financial aid<br>
										<li>Enrolled full-time in a bachelors degree program at an OFIC member college or university<br>
										<li>US citizen<br>
										<li>Minimum 2.8/4.0 GPA
										'.$criteria_b.'
									</ul>	
								</td>
							  </tr>';
							  
		/*if($criteria=='optionb') {
			if($county!='' || $gender!='' || $ethnicity!='' || $college!='' || $majors!='' || $highschool!='' || $religious_affiliation!=''){
			$transactionEmailTemplate.='<tr><td>&nbsp;</td><td>
									  <table width="100%" border="0">
									  	  <tr>
										  	<td colspan="2" align="left" class="headingLeft">Additional Criteria</td>
										  </tr>';
										  
			if($county){
				$transactionEmailTemplate.='<tr>
												<td width="40%" align="left" class="headingLeft">County:</td>
												<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($county).'</td>
											  </tr>';
			}							  
			if($gender){
				$transactionEmailTemplate.='<tr>
												<td  width="40%" align="left" class="headingLeft">Gender:</td>
												<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue(str_replace(array('m','f'),array('Male','Female'),$gender)).'</td>
											  </tr>';
			}							  
			if($ethnicity){
				$transactionEmailTemplate.='<tr>
												<td  width="40%" align="left" class="headingLeft">Ethnicity:</td>
												<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($ethnicity).'</td>
											  </tr>';
			}
			if($college){							  
				$transactionEmailTemplate.='<tr>
												<td  width="40%" align="left" class="headingLeft">Colleges:</td>
												<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($college).'</td>
											  </tr>';
			}
			if($majors){							  
				$transactionEmailTemplate.='<tr>
												<td  width="40%" align="left" class="headingLeft">Majors:</td>
												<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($majors).'</td>
											  </tr>';
			}							  
			if($highschool){
				$transactionEmailTemplate.='<tr>
												<td  width="40%" align="left" class="headingLeft">High School:</td>
												<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($highschool).'</td>
											  </tr>';
			}
			if($religious_affiliation){							
				$transactionEmailTemplate.='<tr>
												<td  width="40%" align="left" class="headingLeft">Religious Affiliation:</td>
												<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.$this->displayValue($religious_affiliation).'</td>
											  </tr>';
			}							  
			$transactionEmailTemplate.='</table></td></tr>
									  ';
			}
		}*/
		$transactionEmailTemplate.='<tr>
										<td height="1" bgcolor="#7F4537" colspan="2"></td>
									</tr>
									  <tr>
										<td valign="top" width="40%" align="left" class="headingLeft">Comments:</td>
										<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.nl2br(stripslashes($this->displayValue($comments))).'</td>
									  </tr>
									  <tr>
										<td valign="top" width="40%" align="left" class="headingLeft">Questions:</td>
										<td width="60%" align="left" style="font-family:verdana; font-size:11px; color:#000000;">'.nl2br(stripslashes($this->displayValue($questions))).'</td>
									  </tr>';
		$transactionEmailTemplate.='</table></td>
						</tr>
						<tr>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="right">&nbsp;</td>
						  <td style="font-family:verdana; font-size:11px; color:#000000;" align="left">&nbsp;</td>
						</tr>
					  </table></td>
				  </tr>
				  <tr>
					<td colspan="2" height=20></td>
				  </tr>
				  <tr class="textContent">
					<td colspan="2" style="padding-left:7px;">'.nl2br(stripslashes($this->signature)).'</td>
				  </tr>
				  <tr>
					<td colspan="2" height=30></td>
				  </tr>
				  	</table>';
		//echo $transactionEmailTemplate;die;		
		return $transactionEmailTemplate;
	}//end function getTransactionEmailTemplate
	
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
	
	//////////////////function uploadOnEdit ends here //////////////////////////////////
	
	//-----------------------------------------------------------------------------------------------//
	// Method Image GD Library Ratio Resize / Thumbnail Creation ------------- 
	//-----------------------------------------------------------------------------------------------//
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
	
	function CreateFixThumbForLeftPanel($photo,$ext,$folder,$width='100',$height='100',$thumbFolder="") {
		$filename = $folder . $photo . "." . $ext;
		

		if($thumbFolder=="")
			$thumbFolder=$folder."thumbnail/";
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filename);
		// Set a maximum height and width
		if($width_orig > $width) {
			$height = ($width* $height_orig )/$width_orig;	// 161 is fix width of product image.
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
	
	function UploadImage($source,$thumbFix=false,$bg=false,$heightFix=false) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			$ext = strtolower($ext);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$prefix = "img_";
			$fileName=uniqid($prefix);
			
			//$fileName = $this->GetFileName($source);
			$destination = DIR_TESTIMONIAL.$fileName.'.'.$ext;
			
			if(move_uploaded_file($source,$destination)) {
				//@unlink($source);
				
				if($width>600 || $height>800)
					$this->CreateThumb($fileName,$ext,DIR_TESTIMONIAL,600,800,DIR_TESTIMONIAL,$thumbFix,$bg); 
				if($heightFix) {
					$this->CreateThumb($fileName,$ext,DIR_TESTIMONIAL,75,75,DIR_TESTIMONIAL_SMALL,$thumbFix,$bg);
					/// Upload image for 
					if($width>160 || $height>160) {
						$this->CreateThumbHeight($fileName,$ext,DIR_TESTIMONIAL,160,160,DIR_TESTIMONIAL_THUMBNAIL);
					} else
						copy(DIR_TESTIMONIAL.$fileName.'.'.$ext,DIR_TESTIMONIAL_THUMBNAIL.$fileName.'.'.$ext);
						
						
				} else {
					$this->CreateThumb($fileName,$ext,DIR_TESTIMONIAL,75,75,DIR_TESTIMONIAL_SMALL,$thumbFix,$bg);
					if($width>429 || $height>358) {
						$this->CreateThumb($fileName,$ext,DIR_TESTIMONIAL,429,358,DIR_TESTIMONIAL_THUMBNAIL,$thumbFix,$bg);
					} else { 
						if($thumbFix) {
							$this->CreateThumb($fileName,$ext,DIR_TESTIMONIAL,160,160,DIR_TESTIMONIAL_THUMBNAIL,$thumbFix,$bg);
						}
						else { 
							copy(DIR_TESTIMONIAL.$fileName.'.'.$ext,DIR_TESTIMONIAL_THUMBNAIL.$fileName.'.'.$ext);
						}
					}
				}
				
				return  $fileName.'.'.$ext; 
			}
			else
				return false;
		} else {
			return false;
		}
	}
	
	function UploadVideoImage($source,$thumbFix=false,$bg=false,$heightFix=false) {
		if(is_file($source)) {
			list($width,$height,$ext)=getimagesize($source);
			$ext = strtolower($ext);
			if($ext==1)
				$ext="gif";
			elseif($ext==2)
				$ext="jpg";
			elseif($ext==3)
				$ext="png";
			
			$prefix = "img_";
			$fileName=uniqid($prefix);
			
			//$fileName = $this->GetFileName($source);
			$destination = DIR_VIDEO_IMAGE.$fileName.'.'.$ext;
			
			if(move_uploaded_file($source,$destination)) {
				//@unlink($source);
				
				if($width>600 || $height>800)
					$this->CreateThumb($fileName,$ext,DIR_VIDEO_IMAGE,600,800,DIR_VIDEO_IMAGE,$thumbFix,$bg); 
				if($heightFix) {
					$this->CreateThumb($fileName,$ext,DIR_VIDEO_IMAGE,75,75,DIR_VIDEO_IMAGE_SMALL,$thumbFix,$bg);
					/// Upload image for 
					if($width>276 || $height>180) {
						$this->CreateThumbHeight($fileName,$ext,DIR_VIDEO_IMAGE,276,180,DIR_VIDEO_IMAGE_THUMBNAIL);
					} else
						copy(DIR_VIDEO_IMAGE.$fileName.'.'.$ext,DIR_VIDEO_IMAGE_THUMBNAIL.$fileName.'.'.$ext);
				} else {
					$this->CreateThumb($fileName,$ext,DIR_VIDEO_IMAGE,75,75,DIR_VIDEO_IMAGE_SMALL,$thumbFix,$bg);
					if($width>276 || $height>180) {
						$this->CreateThumb($fileName,$ext,DIR_VIDEO_IMAGE,276,180,DIR_VIDEO_IMAGE_THUMBNAIL,$thumbFix,$bg);
					} else { 
						if($thumbFix) {
							$this->CreateThumb($fileName,$ext,DIR_VIDEO_IMAGE,276,180,DIR_VIDEO_IMAGE_THUMBNAIL,$thumbFix,$bg);
						}
						else { 
							copy(DIR_VIDEO_IMAGE.$fileName.'.'.$ext,DIR_VIDEO_IMAGE_THUMBNAIL.$fileName.'.'.$ext);
						}
					}
				}
				
				return  $fileName.'.'.$ext; 
			}
			else
				return false;
		} else {
			return false;
		}
	}
	
	function GetStatusDD($selected) {
		$status_arr = array('pending'=>'Pending','processing'=>'Processing','approved'=>'Approved');
		foreach($status_arr as $key=>$value) {
			$options .= '<option value="'.$key.'"';
			if($key==$selected) $options .= ' selected="selected" ';
			$options .= '>'.$value.'</option>';
		}
		return $options;
	}
	
	function UploadProductVideo($source, $ext, $prefix="", $Dir) {
		$prefix = $prefix."_";
		$new_file=uniqid($prefix);
		
		$destination = $Dir.$new_file .".". $ext;
		if(move_uploaded_file($source,$destination))
		{
			return $new_file .".". $ext;
		}
		else
			return false;
	}
	
	function using_ie()
	{
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$ub = False;
		if(preg_match('/MSIE/i',$u_agent))
		{
			$ub = True;
		}
	   
		return $ub;
	}
}
?>