<?

   /***************************************************************
		Project Name	: Captured Landscape
		Copyright 		: Ameriteck Web Service
		Purpose			: Add new Graphics
		Language		: php
   /***************************************************************/
ini_set('memory_limit','100M');
	//including class file
	  	require("includes/commonClass.php") ;
		require('includes/function.inc.php') ;
		require('fckeditor.php') ;
	
	//creating object of the class
	
	 $ClassObject = new commonclass ;
	 $pagename="design";
	 if(!isset($_SESSION['mem_id']))
	 {
	 	 $page="login.php?flag=first";	
	 	 $ClassObject->redirect($page);
	 }
	 
	 
	 
	
     $ClassObject->getGraphicsTable() ;
	  
	//get posted value
		$postedData=$ClassObject->getRequestedData();
	//print_r($_FILES);
	
	//get posted data
	  if($postedData['action']=="addGraphics")
	  {
	  
	  if(!isset($_SESSION['mem_id']))
	 {
	 	 $page="login.php?flag=first";	
	 	 $ClassObject->redirect($page);
	 }
	  	if($postedData['txtWhyYuru']!="")
		{
			$ClassObject->getWhyYuruTable();
			$answerData['why_yuru']=$postedData['txtWhyYuru'];
			$answerData['type']="graphics";
			$ClassObject->userPostedData=$answerData;
			$insert_answer=$ClassObject->insertData();
			
		}
	  
	   if($postedData['txtWhatInspired']!="")
	  	{
			$ClassObject->getWhatInspiredTable();
			$answerData1['what_inspired']=$postedData['txtWhatInspired'];
			$answerData1['type']="graphics";
			$ClassObject->userPostedData=$answerData1;
			$insert_answer1=$ClassObject->insertData();
			
		}
	  
	  
	  	  $error=0;
		   /* $as=$_FILES['imgGraphics']['type'];
			//echo "$as"; exit;
		//print_r($_FILES);
			 //if(!($as=="image/tiff" || $as=="application/octet-stream"))
			 if($as!="application/octet-stream" && $as!="image/tiff" && $as!="image/bmp" && $as!="image/pjpeg"  
			         && $as!="image/jpeg"  && $as!="image/jpg"  )
		    {
			  $error++;
			  echo "<script language='javascript' type='text/javascript' >alert('Please Upload only .tif or .jpg or.bmp images.');			              </script>";
		    }
			*/
		$ClassObject->getGraphicsTable();
	  	$pageData['title']=$postedData['txtTitle'];
		
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
						create_thumb($big_image,$ext,"../userData/GraphicsImages/mainImage/","../userData/GraphicsImages/mainImage/",600,450);
					}
					create_thumb($big_image,$ext,"userData/GraphicsImages/mainImage/","userData/GraphicsImages/thumbnail/",200,200);
					create_thumb($big_image,$ext,"userData/GraphicsImages/mainImage/","userData/GraphicsImages/thumbnailSmall/",125,125);
					$pageData['graphics_image']=$big_image. '.' . $ext;
				}
			    /*$big_image = getUniqFileName( $_FILES['imgGraphics']['name'],"userData/GraphicsImages/tiff/");		
				$file_path="userData/GraphicsImages/tiff/".$big_image;				
				copy($_FILES['imgGraphics']['tmp_name'],$file_path);	
				//Copy JPG file to main folder by converting .tiff to .jpg				
				$file_main="userData/GraphicsImages/mainImage/".justFileName($big_image).'.jpg';						
				$cmd = "convert -quality 100 $file_path $file_main";
				exec($cmd);
				//Copy JPG thumbnail file to thumbnail folder by converting .tiff to .jpg					
				$file_thumb="userData/GraphicsImages/thumbnail/".justFileName($big_image).'.jpg';				
				$cmd = "convert -quality 100  -resize 250  $file_main $file_thumb";				
                exec($cmd);
				//Copy JPG thumbnail file to thumbnail folder by converting .tiff to .jpg					
				$file_thumb="userData/GraphicsImages/thumbnailSmall/".justFileName($big_image).'.jpg';				
				$cmd = "convert -quality 100  -resize 100  $file_main $file_thumb";				
                exec($cmd);					

				$pageData['graphics_image']=justFileName($big_image);*/
				
			}
		$date=date('Y-m-d  H:i:s');	
	    $pageData['description']=$postedData['description'];
		$pageData['mem_id']=$_SESSION['sess_userid'];
		$pageData['status']=0;
		$pageData['approval']=0;
		$pageData['date_g']=$date;
		
		$ClassObject->userPostedData=$pageData;
		
		//if(copy($temp_name,$file_path))
		$insert_query=$ClassObject->insertData();
		//print $insert_query;
				if($insert_query=="success")
				{
						   //on sucessfull insertion of data redirect to this page
					$page="manageGraphics.php?flg=3"; 
					$ClassObject->redirect($page);
				}
				else
				{
			
					//if data is not inserted redirect to this page
					$page="addGraphics.php"; 
					$ClassObject->redirect($page);
				}//else
		    }//error=false
	}//ifposted_data 
		include("html/header.html");
		//validating session 
	  $getSetSession = $ClassObject->validateSession() ;
	
	//includeing middle page
	  if($getSetSession)
		  include("html/addGraphics.html");
	 else
	  {	$errorMessage = "Cannot Validate Session." ;
	    header("location: index.php");}//else
	
	//includeing footer page
	 include("html/footer.html");

?>
