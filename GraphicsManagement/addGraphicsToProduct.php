<?php

/***************************************************************
		Project Name	: Captured Landscape		
		Purpose			: Add Graphics To Product Lists
		Language		: php
   /***************************************************************/
	  require('../includes/commonClass.php') ;
	  //creating object of the class
		 $ClassObject = new commonclass('1','') ;
	  require_once('function.php');
	require('../includes/function.inc.php') ;
	require("../includes/script_new.php") ;
	include("fckeditor.php");
	
	//get table name
	$ClassObject->getGraphicsTable() ;
	//empty error message
	  $errorMessage="";
	  
	  function checkcolor($arr,$color)
	  {
	  	if(in_array($color,$arr))
			return 'selected="selected"';
	  }
	//getting the values from database  
	  if(!empty($_GET['graphics_id']))
	  { 
	  	$condition=" graphics_id='".$_GET['graphics_id']."' ";
		$recordSet = $ClassObject->selectSql($condition) ;
		
		//get number of records
	  	    $userData = $ClassObject->fetchData($recordSet) ;	
			$mem_id = $userData[0]['mem_id'];
			$Graphicsname=$userData[0]['title'];
			$description=$userData[0]['description'];
			$rating = $userData[0]['rating'];
			$Graphicsimg=$userData[0]['graphics_image'];			
			
			
			//get category table
			 $ClassObject->getCategoryTable();
			$condition="parent_id=0 order by category_name";
			 $rs_cat=$ClassObject->selectSql($condition);		
			 $categoryList = $ClassObject->fetchData($rs_cat) ;
			 //print_r($categoryList);
				 foreach( $categoryList as $key=>$categoryDetails )
				 {
					 $value=$categoryDetails['category_id'];
					 $text=$categoryDetails['category_name'];				 
					 $cmbcategory .= $ClassObject->populateComboList($value,$text,$selected);
					 
				 }//for		
			
	  }//if
	  $condition1="parent_id!=0 order by category_name";
	$recordSet1=$ClassObject->selectSql($condition1);
	
	$categoryList1 = $ClassObject->fetchData($recordSet1) ;
	foreach( $categoryList as $key=>$categoryDetails ){
	 		
		 $value=$categoryDetails[category_id];
		 $selected=$_GET[parent_id]; 
		 $text=$categoryDetails[category_name];
		 $subcat_data .=$ClassObject->getSubCategories($categoryDetails[category_id],$text,$selected);
		 //
         
		 //$subcat_data .= $ClassObject->populateComboList($value,$text,$selected);
	 }
	
	 //get posted value
	   $postedData=$ClassObject->getRequestedData();	
	    //print_r($postedData);
	 if(isset($postedData['action']) && $postedData['action']=="upproduct")
	 {
	 //print_r($postedData);
	 	$ClassObject->getCategoryTable();
	 	$cond="category_name LIKE '".$_POST['cmb_subcategory']."' and parent_id=".$_POST['cmb_category'];
		$recordSet=$ClassObject->selectSql($cond);
		$categoryList = $ClassObject->fetchData($recordSet) ;
		
		//get table name
			$ClassObject->getProductTable() ;
			$productData['product_name']=$postedData['txtproductname'];
			$productData['product_desc']=$postedData['description'];
			$productData['subcategory_id']=$postedData['cmb_category'];
			$productData['scategory_id']=$postedData['cmb_subcategory'];
			//$categoryList[0]['category_id'];
			$productData['price']=$postedData['txtprice'];
			$productData['rating ']=$rating;
						
			$productData['member_id']=$mem_id;
			
			$date=date('Y-m-d  H:i:s');	
			$productData['date_p']=$date;
			if($postedData['chk_featured']=="on")
			   $productData['featured']=1;
			 else
			   $productData['featured']=0;
			$productData['weight']=$postedData['txtweight'] ;
			$productData['normalprice']=$postedData['txtnormalprice'] ;
			$productData['product_keyword1']=$postedData['txtkeyword1'];
			$productData['product_keyword2']=$postedData['txtkeyword2'];
			$productData['product_keyword3']=$postedData['txtkeyword3'];
			$productData['product_quantity']=$postedData['textquantity'];
			$productData['color']=implode(",",$postedData['product_color']);
			$productData['size']=implode(",",$postedData['product_size']);
			//for getting the image details
				if($HTTP_POST_FILES['productimg']['name']!=="")
				{
						$temp_name=$HTTP_POST_FILES['productimg']['tmp_name'];
						$file_name=$HTTP_POST_FILES['productimg']['name'];
						$file_path="../userData/productimages/".$file_name;
						
					  //code to copy the product image
						$big_image = getUniqFileName( $_FILES['productimg']['name'],"../userData/productimages/");			
						$file_path="../userData/productimages/".$big_image;
						copy($temp_name,$file_path);	
						$file_path="../userData/productimages/thumbnail/".$big_image;
						createThumbnail($temp_name, $file_path, 80, $_FILES['productimg']['type'], "80");				
						$file_path="../userData/productimages/thumbnailsmall/".$big_image;
						createThumbnail($temp_name, $file_path, 40, $_FILES['productimg']['type'], "40");				
						//end of code to copy the product image
						  $productData['product_image']=$file_name;
				}//end_if
				else // if image is not uploaded copy the Grphics .jpg image to product filder
				 {
				    $file_path_from="../userData/GraphicsImages/mainImage/".$Graphicsimg;
				    $file_path_to="../userData/productimages/".$Graphicsimg;
				    copy($file_path_from,$file_path_to);	
					$file_path_to="../userData/productimages/thumbnail/".$Graphicsimg;				
					createThumbnail($file_path_from, $file_path_to, 80, "image/jpeg", "80");
					$file_path_to="../userData/productimages/thumbnailsmall/".$Graphicsimg;
					createThumbnail($file_path_from, $file_path_to, 40, "image/jpeg", "40");
				    $productData['product_image']=$Graphicsimg;
				 }
			 $ClassObject->userPostedData=$productData;		
			
			//execute the query
			$insert_query=$ClassObject->insertData();
		
				if($insert_query=="success")
				{
				  ///On sucessfull addition Update added_to_product ,so that Graphics is added only once in products
				   //get table name
			          $ClassObject->getGraphicsTable();
					  $condition=" graphics_id='".$_GET['graphics_id']."' ";
					  $GraphicsData['added_to_product']=1;
					  $ClassObject->userPostedData=$GraphicsData;					 
					  //execute the query
			          $update_query=$ClassObject->updateData($condition);	
					  		 
					//on sucessfull insertion of data redirect to this page
					$page="manageGraphics.php?flg=added" ;
					$ClassObject->redirect($page);
			   }
				else
				{
					//if data is not inserted redirect to this page
					$errorMessage="Not Updated Sucessfully";
					
				}//else
		
		
		
	 }//ifposteddata
	
	//validating session 
	  $getSetSession = $ClassObject->validateSession() ;
	
	//includeing middle page
	  if($getSetSession)
		include("../admin/html/admin_addGraphicsToProduct.html");
	  else
	  { 
	  	$errorMessage = "Cannot Validate Session." ;
	   $ClassObject->redirect("index.php");
	  }//else
		  
	//includeing footer page
	 include("../admin/html/footer.html");
	 
?>    
