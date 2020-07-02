<?php
/***************************************************************
		Project Name	: Captured Landscape
		Copyright 		: Ameriteck Web Service
		Purpose			: Editing the Graphics
		Language		: php
  **************************************************************/
ini_set('memory_limit','100M');
	//including class file
	  require("includes/commonClass.php") ;
		require('includes/function.inc.php') ;
	  require('fckeditor.php') ;
	//creating object of the class
	  $ClassObject = new commonclass ;
	
	
	//get table name
	  $ClassObject->getGraphicsTable() ;
	 
	//query condition
	  $condition="graphics_id='$_GET[graphics_id]'  ";
	
	//get record set
	  $recordSet=$ClassObject->selectSql($condition);
	
	//get affected rows
	  $affectCount = $ClassObject->getAffectedRows($recordSet) ;
		
	//get number of records
	  $userData = $ClassObject->fetchData($recordSet) ;	
	  
	  if(count($userData) > 0)
	  {
	  	$title=$userData[0]['title'];
		$image_name=$userData[0]['graphics_image'];
		$description=$userData[0]['description'];
	  }//ifcount
	  
	  
	 //get posted value
	   $postedData=$ClassObject->getRequestedData();	
	    
		
		
	 if($postedData['action']=="edit")
	 {
	 	
	 	$articleData['title']=$postedData['title'];
		$articleData['description']=$postedData['description'];
		 $error=0;
		    if($_FILES['imgGraphics']['name']!="")
			{
			   $as=$_FILES['imgGraphics']['type'];
			//print_r($_FILES);
				/* if(!($as=="image/tiff" || $as=="application/octet-stream"))
				{
				  $error++;
				  echo "<script language='javascript' type='text/javascript' >alert('Please Upload only .tif images.');			              </script>";
				}*/
		   }
		    
	
		
		if($error==0)
			{
				if($_FILES['imgGraphics']['name'])
				{
					
					$big_image=uniqid("photo");
					$ext=getFileExt($_FILES['imgGraphics']['name']);
					$file_path="userData/GraphicsImages/mainImage/".$big_image . "." .$ext;				
					if(move_uploaded_file($_FILES['imgGraphics']['tmp_name'],$file_path))
					{	
						list($width,$height)=getimagesize($file_path);
						if($width>600 || $height>450)
						{
							create_thumb($big_image,$ext,"userData/GraphicsImages/mainImage/","userData/GraphicsImages/mainImage/",600,450);
						}
						
						create_thumb($big_image,$ext,"userData/GraphicsImages/mainImage/","userData/GraphicsImages/thumbnail/",200,200);
						create_thumb($big_image,$ext,"userData/GraphicsImages/mainImage/","userData/GraphicsImages/thumbnailSmall/",125,125);
						$articleData['graphics_image']=$big_image. '.' . $ext;
					}
				}
				/*if($_FILES['imgGraphics']['name'])
				{
					
					$big_image = getUniqFileName( $_FILES['imgGraphics']['name'],"userData/GraphicsImages/tiff/");		
					$file_path="userData/GraphicsImages/tiff/".$big_image;				
					copy($_FILES['imgGraphics']['tmp_name'],$file_path);	
					//Copy JPG file to main folder by converting .tiff to .jpg				
					$file_main="userData/GraphicsImages/mainImage/".justFileName($big_image).'.jpg';						
					$cmd = "convert -quality 100   $file_path $file_main";
					exec($cmd);
					//Copy JPG thumbnail file to thumbnail folder by converting .tiff to .jpg					
					$file_thumb="userData/GraphicsImages/thumbnail/".justFileName($big_image).'.jpg';				
					$cmd = "convert -quality 100  -resize 150  $file_main $file_thumb";				
					exec($cmd);
					//Copy JPG thumbnail file to thumbnail folder by converting .tiff to .jpg					
					$file_thumb="userData/GraphicsImages/thumbnailSmall/".justFileName($big_image).'.jpg';				
					$cmd = "convert -quality 100  -resize 50  $file_main $file_thumb";				
					exec($cmd);					

					$articleData['graphics_image']=justFileName($big_image);
					
				}*/
		
			
				$ClassObject->userPostedData=$articleData;
				//print_r($articleData);
				//Query condition		
				  $condition="graphics_id='$_GET[graphics_id]' ";		
				//execute the query
				 $update_query=$ClassObject->updateData($condition);
	
		
					if($update_query=="success")
					{
					
						$ClassObject->redirect('manageGraphics.php?flg=updated');
						
					}
					else
						$errorMessage="Not Updated Sucessfully";
			
	     }//error=false
	}//ifposteddata
	  include("html/header.html");

	//validating session 
	  $getSetSession = $ClassObject->validateSession() ;

	//includeing middle page
	  if($getSetSession)
	 {
		include("html/editGraphics.html");}
	  else
	  {	$errorMessage = "Cannot Validate Session." ;
	    $ClassObject->redirect("index.php");}//else
		  
	//includeing footer page
	 include("html/footer.html");
?>
