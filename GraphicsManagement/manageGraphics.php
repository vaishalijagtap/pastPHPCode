<?php

/***************************************************************
		Project Name	: Captured Landscape
		Copyright 		: Ameriteck Web Service
		Purpose			: Manage Graphics
		Language		: php
   /***************************************************************/
	//including class file
	  require('includes/commonClass.php') ;
	
	//creating object of the class
	  $ClassObject = new commonclass ;
	
	//get the posted variables
	  $postData = $ClassObject->getRequestedData() ;
	  
	 /*if(!isset($_SESSION['mem_id']))
	 {
	 	 $page="login.php?flag=first";	
	 	 $ClassObject->redirect($page);
	 }*/
	//get table name
	$ClassObject->getGraphicsTable() ;
	
		//empty error message
	  $errorMessage="";
	
	
	//print error/message
	 if($_GET['flg']==3)
		 $errorMessage="Graphics Inserted Successfully.";
	elseif($_GET['flg']=='del')
	     $errorMessage="Graphics deleted Successfully.";
	else	//errorMessage	
	$_GET['flg']=='updated' ? $errorMessage="Graphics  Updated Successfully." : $errorMessage='' ;
	
	
	
		
	//Deleting the advertise
	  if(isset($_REQUEST['Delete']) && $_REQUEST["chk_frmid"])
	   {
			$picture_id = implode(",",$_REQUEST["chk_frmid"]);			 
			$condition="graphics_id IN (".$picture_id.")";
			
			$ClassObject->getGraphicsTable() ;
			$uprecordSet = $ClassObject->selectSql($condition);
			$messageList = $ClassObject->fetchData($uprecordSet);
		
			//Unlink all Images
			// $file_path="userData/GraphicsImages/tiff/".$messageList[0]['graphics_image'].".tif";
			// unlink($file_path);
			 $file_path="userData/GraphicsImages/mainImage/".$messageList[0]['graphics_image'];
			 unlink($file_path);
			 $file_path="userData/GraphicsImages/thumbnail/".$messageList[0]['graphics_image'];
			 unlink($file_path);
			 $file_path="userData/GraphicsImages/thumbnailSmall/".$messageList[0]['graphics_image'];
			 unlink($file_path);
			
			$recordSet = $ClassObject->deleteData($condition) ;			
			$recordSet=="success" ? $errorMessage="Graphics deleted Successfully." : $errorMessage="Graphics not deleted." ;
			$ClassObject->redirect("manageGraphics.php?flg=del");
		
	   }//if_delete

		//query for displaying the Graphics listing order by the ratings 
		if(isset($_SESSION['sess_userid']))
	 	{
	  		 $condition="mem_id='".$_SESSION['sess_userid']."'"; 
		}
		else{
			$condition="";
		}
	    $recordSet = $ClassObject->selectSql($condition) ;
	 
	   $affectCount = $ClassObject->getAffectedRows($recordSet) ;
	
 /*************Paging code**********************/
	  
	 require('includes/class_paging.php') ;

	$Paging = new Paging();

	$limit = '10'; //no records to be displayed per page
 
	$str_num_record = $Paging->DisplayTotalRecord($limit, $_REQUEST['page'], $affectCount);
  	$Paging->PagingHeader($recordSet,$limit);
	empty($_REQUEST['page'])?$page=0:$page=$_REQUEST['page'];
		  	 $messagecount=$page*$limit+1 + $pg;
	
	
 /********************End*********************/ 
	

	  if( $affectCount == 0 )
	  {
	      $formContent = "<tr>
		                     <td colspan='7' align='center' width='100%' class='redtext'>No Record Found.</td>
						  </tr>" ;
	  }
	  else
	  {
	  	$color="#252525";
	  	 $messageList = $ClassObject->fetchData($recordSet);			
		 $Page_Start!=0 ? $pg=$Page_Start - 1 : $Page_Start=0;		 
		 empty($_REQUEST['page'])?$page=0:$page=$_REQUEST['page'];
		  //print_r($messageList);
		  foreach( $messageList as $key=>$messageDetails )
		  {
		 		
			if($color=="#252525")
				$color="#2F2F2F";
			else
				$color="#252525";
			if($messageDetails['status']==1)
			{
				$approval = "Approved";
			}
			else
			{
				$approval = "Unapproved";
			}		
		  //status of the product
		   
		  $RATING = 5;
			$ratings=($messageDetails['rating']/$RATING)*100;
		  	
			
		 	if($messagecount%2==0)  
			    $class = "class='bg_light'" ;
			 else				  
			    $class = "class=''" ;
			
			    
				
			 $productcolor = 'black_text';
			 
			  $formContent .= "<tr>
								<td align='center' class='".$productcolor."'  >".$messagecount."</td>
								<td align='left' class='".$productcolor."' style='padding-left:10px'><img src='userData/GraphicsImages/thumbnailSmall/".$messageDetails['graphics_image']."'></td>
								<td align='left' class='".$productcolor."' style='padding-left:10px'>".ucfirst($messageDetails['title'])."</td>
								<td align='left' class='".$productcolor."' style='padding-left:10px'>".$ratings."</td>
								<td align='center' class='".$productcolor."'>$approval</td>";
			if(isset($_SESSION['mem_id']))
			{
				$formContent .= "<td >
								<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>
								<td align='center'> <a href='editGraphics.php?graphics_id=$messageDetails[graphics_id]&page=".$_REQUEST['page']."' class='link_action' >Edit</a></td>
									<td  class='pipe_black'>|</td>
								<td align='center'><a href='viewGraphics.php?graphics_id=$messageDetails[graphics_id]&page=".$_REQUEST['page']."' class='link_action'>View</a></td>
								</tr>
								</table></td><td align='center'>		
		                      <input type='checkbox'  name='chk_frmid[]' id='chk_all' value=".$messageDetails['graphics_id']."  >	
								
								</td>";
				}
				else
				{
					$formContent .= "<td>&nbsp;</td><td >&nbsp;</td>";
									
				}
					$formContent .= "</tr>
								</td>
							    </tr>";
			 
			 $messagecount++ ;
			 
			  if ($Paging->IsBreak($limit))
			  		break;
		  }//for
	  }//else

	if($affectCount > $limit)
	{

		$formContent .= "<tr ><td align='center' colspan=7 class='page_no'>".$Paging->PagingFooter($recordSet,'frm')."</td></tr><tr><td>&nbsp;</td></tr>";
	}	
	include("html/header.html");		  
	//validating session 
	  $getSetSession = $ClassObject->validateSession() ;

	//includeing middle page
	 if($getSetSession)
		include("html/manageGraphics.html");
	  else{ 
	  	$errorMessage = "Cannot Validate Session." ;
	   $ClassObject->redirect("index.php") ;
	   exit;
	  }//else
		  
	//includeing footer page
	  include("html/footer.html");
?>