<? 
error_reporting(0);

//$adminType = 'superadmin' ;
   class commonclass
     {

		var $include_header ;
       ############################################# Constructor ###################################
			
		 function commonclass($include_header=1,$title='')
		   {
		   		
				

		       //intialising variables
			   $this->table_name = "" ;     //variable for table name
			   
			   $this->emailid = "" ;        //variable for email id
			   $this->subject = "" ;        //variable for email subject
     		   $this->body = "" ;			//variable for email body
			   $this->headers = "" ;		//variables for headres
			   $this->result = "" ;			//variables for result getting from query
			   $this->rpp = 10 ;			//variables for record per page to be display
			   $this->stlimit = 0 ;			//variables for paging as start limit
			   $this->getPrice = 0 ;		
			   $this->sql_condition = "" ;	//variable for sql query
			   $this->userPostedData = array() ; //whatever the data pasted get this into array
			   $this->include_header = $include_header;
			   $this->end_count=0;
			   $this->start_count=0;
			   
			   //database details
			   $this->database_name = "" ;
			   $this->database_host = "" ;
			   $this->database_user = "" ;
			   $this->database_password = "" ;
     		   
			   //session start , clear output , clear cache
			   session_cache_limiter('private, must-revalidate');
			   ob_start() ;
			   session_name("captured_landscape_SESSION");
			   session_start();
			  $page=basename($_SERVER['PHP_SELF']);
				if($page!="login.php" && !ereg( "admin/" , $_SERVER[PHP_SELF] )){
					if($_SERVER['QUERY_STRING']!="")
						$page.="?" . $_SERVER['QUERY_STRING'];
					$page=urlencode($page);
					$_SESSION['sess_page_name']=$page;
				}
			  
			
			  
			   //including configuration file
			   if(file_exists("includes/config.php"))
					require("includes/config.php") ;
			   else if(file_exists("../includes/config.php"))
				    require("../includes/config.php") ;


             
			   //getting database details
			   $this->database_name = DATABASE_NAME ;
			   $this->database_host = DATABASE_HOST ;
			   $this->database_user = DATABASE_USERNAME ;
			   $this->database_password = DATABASE_PASSWORD ;
				
			   //opening connection for mysql
			   $this->OpenConnection();
			   
			   //validating sessions
			   if($_SESSION['sess_username']=="admin" )
			   {
				  $adminTopMenu = "../admin/html/admin_topmenu.html" ;
				  
			   }
			   elseif($_SESSION['sess_username'] && $_SESSION['sess_username']!="admin")
				   $adminTopMenu = "../admin/html/admin_topmenu2.html" ;
				   else
				   $adminTopMenu = "../admin/html/blank.html" ;
				//$adminTopMenu = "" ;
				
				
				if(isset($_SESSION['mem_id']) && !empty($_SESSION['mem_id']))
			   {
			   		//$TopMenu = "user_html/topMenu.php" ;
					$TopMenu = "startheader.php";
					
					
			   }
			 else
			   {
			   		//$TopMenu = "user_html/topMenu1.php" ;
					 $TopMenu = "startheader.php" ; 
					
			   }
				
			   //including top header
			   if($this->include_header){
				   if( ereg( "admin/" , $_SERVER[PHP_SELF] ) ){

						include("../admin/session.php") ;
						
				} else
						include("html/header_new.html");
				}
			   
			   return $this->table_name ;

		   }

		############################################### Constructor End ################################

        ############################################# Database functions ###################################
        
/***********************Tables used*******************************************/	
		
			   function getAdminTable() //Facility Images 
			   {
				   $this->table_name = "admin_info" ;
				   return $this->table_name ;
			   }	
			   function getAdminPermissionsTable() //Facility Images 
			   {
				   $this->table_name = "admin_permisiions " ;
				   return $this->table_name ;
			   }	
			   
			   function getMenuTable() //Facility Images 
			   {
				   $this->table_name = "menu " ;
				   return $this->table_name ;
			   }				  
			  
				function getCartTable()      //cart table
			   {
				  $this->table_name = "cart" ;
				  return $this->table_name ;
			   } 
		   
		     function getMemberTable() //Member Table
			   {
				   $this->table_name = "member_info" ;
				   return $this->table_name ;
			   }
			    function getStateTable() //State Table
			   {
				   $this->table_name = "state" ;
				   return $this->table_name ;
			   }
			   function getOrderTable() //Order Table
			   {
				   $this->table_name = "order_details" ;
				   return $this->table_name ;
			   }
			   
			     function getOrderStatusTable()      //Order Status table
		  		{
		    	 	 $this->table_name = "order_status " ;
			 		 return $this->table_name ;
		 		 }
			   
			    function getWishlistTable()      //Wishlist table
		  		{
		    	 	 $this->table_name = "wishlist" ;
			 		 return $this->table_name ;
		 		 }
			   
			    function getLinksTable()      //Links table
		  		{
		    	 	 $this->table_name = "links" ;
			 		 return $this->table_name ;
		 		 }
				 
				 function getWhyYuruTable()      //Why Yuru table
		  		{
		    	 	 $this->table_name = "why_yuru" ;
			 		 return $this->table_name ;
		 		 }
			   
			    function getWhatInspiredTable()      //what_inspired table
		  		{
		    	 	 $this->table_name = "what_inspired" ;
			 		 return $this->table_name ;
		 		 }
			    
			   
			   function getPayment_InfoTable() //admin Payment Table
			   {
				   $this->table_name = "payment_info " ;
				   return $this->table_name ;
				  			  
			   }
			   
			   function getBillingTable()      //bill info table
		 	   {
		      		$this->table_name = "billing_information" ;
			  		return $this->table_name ;
		  	   }
		  	
		 		function getShippingTable()      //shipping info table
		 		{
		      		$this->table_name = "shipping_information" ;
			  		return $this->table_name ;
		     	 }
				 
				 function getShippingRateTable()      //Shipping rates table
		  		 {	
		      		$this->table_name = "shipping_rates" ;
			  		return $this->table_name ;
		  		 }
			   
			   function getPagesTable() //admin Pages Table
			   {
				   $this->table_name = "pages " ;
				   return $this->table_name ;
			   }
			   function getNewsletterTable() //News Letter Table
			   {
				   $this->table_name = "newsletter_info" ;
				   return $this->table_name ;
			   }
			   function getMailingListTable() //Mailing Table
			   {
				   $this->table_name = "mailing_list " ;
				   return $this->table_name ;
			   }
			   function getCountryTable() //country  Table
			   {
				   $this->table_name = "country" ;
				   return $this->table_name ;
			   }
			   function getCategoryTable() //category_info   Table
			   {
				   $this->table_name = "category_master" ;
				   return $this->table_name ;
			   }
			   function getProductTable() //product_details   Table
			   {
				   $this->table_name = "product_details " ;
				   return $this->table_name ;
			   }
			   function getMailMsgTable() //Mail Msg  Table
		  		{
					 $this->table_name = "mail_message_master" ;
					 return $this->table_name;		   
		  		}
				function getPeopleTable() //People  Table
		  		{
					 $this->table_name = "people" ;
					 return $this->table_name; 
		  		}
				function getPoemTable() //Poem  Table
		  		{
					 $this->table_name = "poem" ;
					 return $this->table_name; 
		  		}
				function getImageTable() //Image  Table
		  		{
					 $this->table_name = "images" ;
					 return $this->table_name; 
		  		}
				function getYourShirtTable() //Your Shirt  Table
		  		{
					 $this->table_name = "yourShirt" ;
					 return $this->table_name; 
		  		}
				function getVideoTable() //Video  Table
		  		{
					 $this->table_name = "videos" ;
					 return $this->table_name; 
		  		}			   
			  function getCoupanTable() //coupon_code    Table
			   {
				   $this->table_name = "coupon_code  " ;
				   return $this->table_name ;
			   } 
			   
			   function getGraphicsTable() //Graphics    Table
			   {
				   $this->table_name = "Graphics" ;
				   return $this->table_name ;
			   } 
			   
			   function getReplyMailTable() //Graphics    Table
			   {
				   $this->table_name = "reply_master" ;
				   return $this->table_name ;
			   } 
			   
			    function getPollTable() //Graphics    Table
			   {
				   $this->table_name = "poll" ;
				   return $this->table_name ;
			   } 
			   
			    function getCharityTable() //Charity    Table
			   {
				   $this->table_name = "charity" ;
				   return $this->table_name ;
			   } 
			   
			     function getyouCuesTable() //Charity    Table
			   {
				   $this->table_name = "youcue " ;
				   return $this->table_name ;
			   } 
			   
			   
			    function getQuotesTable() //Quotes    Table
			   {
				   $this->table_name = "quotes " ;
				   return $this->table_name ;
			   } 
			   
			   function getQuestionsTable() //Questions    Table
			   {
				   $this->table_name = "questions " ;
				   return $this->table_name ;
			   } 
			    function getBannerTable() //Questions    Table
			   {
				   $this->table_name = "banner" ;
				   return $this->table_name ;
			   } 
			    function getRatingsTable() //Questions    Table
			   {
				   $this->table_name = "ratings" ;
				   return $this->table_name ;
			   } 
			   
			    function getMessageBoardMemberTable() //Questions    Table
			   {
				   $this->table_name = "messagemembers" ;
				   return $this->table_name ;
			   } 
			   
			    function getWearTable() //Questions    Table
			   {
				   $this->table_name = "wear_list" ;
				   return $this->table_name ;
			   } 
			   
			   function getGraphicsBoardTable() //Questions    Table
			   {
				   $this->table_name = "graphics_board" ;
				   return $this->table_name ;
			   }
			   
			   function getYouCueBoardTable() //Questions    Table
			   {
				   $this->table_name = "YouCue_board" ;
				   return $this->table_name ;
			   } 
			   function getPeopleBoardTable() //Questions    Table
			   {
				   $this->table_name = "people_board" ;
				   return $this->table_name ;
			   } 
			   function getPictureBoardTable() //Questions    Table
			   {
				   $this->table_name = "picture_board" ;
				   return $this->table_name ;
			   }
			   
			   function getPoemBoardTable() //Questions    Table
			   {
				   $this->table_name = "poem_board" ;
				   return $this->table_name ;
			   } 
			   function getCahrityBoardTable() //Questions    Table
			   {
				   $this->table_name = "charity_board" ;
				   return $this->table_name ;
			   } 
			    function getVoteCahrityBoardTable() //Questions    Table
			   {
				   $this->table_name = "vote_charity_board" ;
				   return $this->table_name ;
			   } 
			   function getWhatElseBoardTable() //Questions    Table
			   {
				   $this->table_name = "whatElse_board" ;
				   return $this->table_name ;
			   } 
			   function gethowtoSpreadboardTable() //Questions    Table
			   {
				   $this->table_name = "howtoSpread_board" ;
				   return $this->table_name ;
			   } 
			  
			  function getPotOutTable() //Questions    Table
			   {
				   $this->table_name = "opt_out" ;
				   return $this->table_name ;
			   } 
/********************************End Tables*********************************************/		   
		//name of State
		function getStateName($state)
		{
		$sql="select state_name FROM state WHERE state_prefix='$state'";
		$result = mysql_query($sql) or die(mysql_error()) ;
		$data=mysql_fetch_array($result);
		return $data[state_name];					
			
		}
		
		//name of country
		function getCountryName($country)
		{
		$sql="select country_name  FROM country WHERE country_id='$country'";
		$result = mysql_query($sql) or die(mysql_error()) ;
		$data=mysql_fetch_array($result);
		return $data[country_name];					
			
		}
		
		//get count of subcategory
		function getSubCount($parent_id)
		{
			global $db;
			$query="SELECT count(*) as count_s from category_master WHERE parent_id='".$parent_id."' and is_active=1";
			$get_count=$db->executeQuery($query,true);
			if(count($get_count)>0)
			{
				foreach($get_count as $count_t)
				{
					return $count_t['count_s'];
				}
			}
			else
				return 0;
		}
		//fetching all the data
		  function fetchData($recordSet) 
		  {
				$data = array() ;

				while( $resultData = mysql_fetch_assoc($recordSet) )
			    {
					$data[] = $resultData ;
			    }

				return $data ;
		  }
		//getting affected rows by last query
		  function getAffectedRows($recordSet)
		  {
			  $totalCount = mysql_num_rows($recordSet) ;


			  return $totalCount ;
		  }
		  
		  
		  //getting last inserted id
		  function getLastInsertedID()
		  {
			  $totalCount = mysql_insert_id() ;


			  return $totalCount ;
		  }
		  
		  //CONECTING DATABASE
		 function OpenConnection()
		   {
		       
			   $db_link = mysql_connect( $this->database_host , $this->database_user , $this->database_password ) 
						  or die("Could not connect to database server : ".mysql_error() ) ;
			   
			   mysql_select_db($this->database_name) or die ("Can't find the database");
			   
			   return $db_link ;
		   }
		   
		   
		//firing query 
		  function selectSql($condition='')
		  {
			 
			  if(trim($condition)){
			 $sql = "SELECT * FROM ".$this->table_name." WHERE ".$condition  ;  
					
				}
			  else
				 $sql = "SELECT * FROM ".$this->table_name ; 
				//echo $sql;
				//die;
				 $result = mysql_query($sql) or die(mysql_error()) ;
				//echo $sql;			
			  return $result ;
		  }
		 
			   
		//firing query 
		  function selectSql_orderby($condition='',$orderby='')
		  {
			  
			  if(trim($condition))
				 $sql = "SELECT * FROM ".$this->table_name." WHERE ".$condition ;
			  else
				  $sql = "SELECT * FROM ".$this->table_name ;
				
			 if(trim($orderby))
				 $sql .= " $orderby" ;

				 //echo $sql; exit; 
				
				 $result = mysql_query($sql) ;
							
			  return $result ;
		  }
		  
		  
		  //firing query 
		  function selectDistinctSql($fields='',$condition='')
		  {
			 
			  if(trim($condition)){
			  		  $sql = "SELECT distinct ".$fields." FROM ".$this->table_name." WHERE ".$condition  ;  
					
				}
			  else
				 $sql = "SELECT distinct ".$fields." FROM ".$this->table_name ; 
				//echo $sql;
				//die;
				 $result = mysql_query($sql) or die(mysql_error()) ;
							
			  return $result ;
		  }
		//Delete query by rajesh
		  function deleteData($condition)
		  {
			  if(trim($condition))
			 $sql = "DELETE FROM ".$this->table_name." WHERE ".$condition ; 
			 
			 	 $result = mysql_query($sql) ;
			   		
			   if(mysql_error())
			   {
				  echo mysql_error() ;
				  die() ;
			   }
			  else
			   {
				  $delete = "success" ;
			   }			
			  return $delete ;
		  }

		 //updating data in database
		  function updateData($condition)
		   {
			  $cols = "" ;
			  $vals = "" ;
			
			  foreach( $this->userPostedData as $key=>$value )
			   {
				  $cols .=  $key ."='".$value."'," ;
			   }

			   $cols = substr($cols , 0 , strlen($cols)-1) ;

			  $this->sql = "UPDATE ".$this->table_name." SET ".$cols." WHERE ".$condition ;  
						   $result = mysql_query($this->sql) ;
					//die();			
			  if(mysql_error())
			   {
				   echo mysql_error() ;
				   //die() ;
			   }
			  else
			   {
				  $insert = "success" ;
			   }

			   return $insert ;
		   }
		 
		  function delete($condition = '')
		   {
		   		if($condition != "")
				{
		   			$this->sql = "DELETE FROM ".$this->table_name." WHERE ".$condition ; 
				}
				
				if(!mysql_query($this->sql))
			    {
				   echo mysql_error() ;
				   die() ;
			    }
			    else
			    {
				  $insert = "success" ;
			    }
		   }
		 
		 //inserting data in database
		  function insertData()
		   {
			  $cols = "" ;
			  $vals = "" ;
			
			  foreach( $this->userPostedData as $key=>$value )
			   {
			    
				  if($key!="PHPSESSID")
				   {
					  $cols .= str_replace("txt" , "" , $key)."," ;
					  
					  $vals .= "'".$value ."'," ;
				   }
			   }
		       
			  $cols = substr($cols , 0 , strlen($cols)-1) ;
			  $vals = substr($vals , 0 , strlen($vals)-1) ;

			 $this->sql = "INSERT INTO ".$this->table_name. " ( ".$cols." ) VALUES "." ( ". $vals ." ) "  ;  
			
			
			
			  if(!mysql_query($this->sql))
			   {
				   echo mysql_error() ;
				   die() ;
			   }
			  else
			   {
				  $insert = "success" ;
			   }

			   return $insert ;
		   }
		function getCategoryDropDown($cat_selected="")
		{
			
			$query = "SELECT * FROM category_master where parent_id=0 order by category_name";				
			//$db=new database();
			$getresult=$this->executeQuery($query,true);		
			$data="";						
			foreach($getresult as $result)
			{
				$data .= '<option value="'.$result['cat_id'].'"';
				if($cat_selected == $result['cat_id'] )
					$data .= ' selected="selected"';
				$data .= '>'.ucfirst($result['category_name']).'</option>';
			}
			return $data;
		}

		function getSubCategoryDropDown($parent_id,$cat_selected="")
		{
			
			$query = "SELECT * FROM category_master where parent_id='".$parent_id."' order by category_name";				
			//$db=new database();
			$getresult=$this->executeQuery($query,true);		
			$data="";						
			foreach($getresult as $result)
			{
				$data .= '<option value="'.$result['category_id'].'"';
				if($cat_selected == $result['category_id'] )
					$data .= ' selected="selected"';
				$data .= '>'.ucfirst($result['category_name']).'</option>';
			}
			return $data;
		}
		function executeQuery($query,$select = false)
		{
			$result=mysql_query($query) or die(mysql_error());
			$this->lastId=mysql_insert_id();
			$res=array();
			if($select){
				while($res1 = mysql_fetch_array($result)) {
					$res[] = $res1;
				}
				$this->rows=mysql_num_rows($result);
				return $res;
			}
		}
		
		 ############################################# Database functions End ###################################

		 ############################################## genral functions ########################################

       
		//checking session exists or not
		 /*function validateSession()
		 {
		 //echo $_SERVER[PHP_SELF] ;
		 
			 if( ereg( "admin/" , $_SERVER[PHP_SELF] ) )
			 {
				 
				 if( $_SESSION[sess_adminId] )
				 {
					 $sessionSet = true ;
				 }
				 else
				 {
					 $sessionSet = false ;
				 }
			 }
			 else
			 {
				 if( $_SESSION[sess_userName] )
				 {
					 $sessionSet = true ;
				 }
				 else
				 {
					 $sessionSet = false ;
				 }
			 }*/
			 /******comment by raj*/
			/* if($_SESSION['sess_userName'] && !$_SESSION['mem_id'] )
				 {
					 $sessionSet = true ;
					  return $sessionSet ;
				 }			 
			
			   if( $_SESSION['mem_id'] && !$_SESSION['sess_userName'] )			   
				 {
					 $sessionSet = true ;
					  return $sessionSet ;
				 }
				 else
				 {
					 $sessionSet = false ;
					  return $sessionSet ;
				 }
			*/

			//echo $sessionSet;
			/*return $sessionSet ;
		 }*/
		 
		 function validateSession()
		 {
		     
			 if(isset($_SESSION['mem_id']) && !ereg( "admin/" , $_SERVER['PHP_SELF'] ))
				 {
				 	
					 return  true ;
				 }
				 if( ereg( "admin/" , $_SERVER[PHP_SELF] ) )
			 	{
				
				 if( $_SESSION[sess_admin_id] )
				 {
				  
					 $sessionSet = true ;
				 }
				 else
				 {
					 $sessionSet = false ;
				 }
			 }
				  
			
			//echo $sessionSet;
			 return $sessionSet ;
		 }
		  //sending mail
		 function sendmail($mailType='')
		   {
		      if($mailType==1)
	  	       {
				   $this->headers  =  "MIME-Version: 1.0\n";
				   $this->headers .= "Content-type: text/html; charset: iso-8859-1;\nContent-Disposition: inline;\n";
				   $this->headers .= "From: Captured Landscape Technical Support Team:".ADMIN_EMAIL." < " .ADMIN_EMAIL. " >;\n";
				   $this->headers .= "Cc: ".ADMIN_EMAIL.";\r\n";
				}
			  else
			   {
				   $this->headers  = 'MIME-Version: 1.0' . "\r\n";
				   $this->headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				   $this->headers .= "From: Captured Landscape Technical Support Team:".ADMIN_EMAIL." < " .ADMIN_EMAIL. " >;\n";
				   $this->headers .= "Cc: ".ADMIN_EMAIL.";\r\n";
			   }
               
			   //echo $this->emailid."<br>".$this->subject."<br>".$this->body ;exit;
			   @mail($this->emailid , $this->subject , $this->body , $this->headers ) ;

			   return 0 ;
		   }

		//setting up the sessions
		 function setSessions($userFlag='',$userData)
		 {
		 session_name("captured_landscape_SESSION");
		 session_start();
		 //echo $userFlag;
		 //print_r($userData); 
		 //die();
			 if($userFlag)
			 {
			 	
				 $_SESSION['sess_adminId'] = $userData[0]['admin_id'] ;
				 $_SESSION['sess_userName'] = $userData[0]['admin_user'] ;
				 $_SESSION['admin_email'] = $userData[0]['admin_email'] ;
				 $_SESSION['admin_user'] = $userData[0]['admin_user'] ;
				 $_SESSION['admin_type'] = $userData[0]['type'] ;
				
				 
				 if(!isset($userData[0][admin_user]))
				 {
					$_SESSION['sess_userid'] = $userData[0]['mem_id'] ;
					$_SESSION['sess_username'] = $userData[0]['user_name'] ;
				    $_SESSION['sess_password'] = $userData[0]['user_pass'] ;
					$_SESSION['user_fname'] = $userData[0]['first_name'] ;
					$_SESSION['user_email'] = $userData[0]['e_mail'] ;
					//edit by abhi
					$_SESSION['user_name']  =  $userData[0]['user_name'];
					$_SESSION['mem_id']= $userData[0]['mem_id']; 
					$_SESSION['first_name']  =  $userData[0]['first_name'];
					
				}
				else
				{
					
					$_SESSION['sess_username'] = $userData[0]['admin_user'] ;
					$_SESSION['sess_adminId'] = $userData[0]['admin_id'] ;
					$_SESSION['admin_email'] = $userData[0]['admin_email'] ;
					$_SESSION['admin_user'] = $userData[0]['admin_user'] ;
					$_SESSION['admin_type'] = $userData[0]['type'] ;
				}
			 }
			
			//print_r($_SESSION);die();
			 return 0 ;
		 }

		//setting up the sessions
		 function EndSessions()
		 {
			 session_start();
		     session_unset();
			 session_destroy();
			

			 return 0 ;
		 }
		
		//create list box for date
         function populateComboList($value='',$text='',$selected='')
		   {
			   if($value==$selected)  $sel = "selected" ;
			   else                   $sel = "" ;

			    $resultData = "<option value='".$value."' ".$sel.">".ucfirst($text)."</option>" ; 
			   
			   return $resultData ;
		   }
		    function getSubCategories($cat_id,$cat_name,$selected="")
		   {
			     $query="SELECT * FROM category_master WHERE parent_id='$cat_id' order by category_name";
				 $getResult=mysql_query($query);
				 if(mysql_num_rows($getResult)>0)
				 {
				 	while($result=mysql_fetch_array($getResult))
					{
						$cat_data.=$this->getSubCategories($result['category_id'],$cat_name." -> " .ucfirst($result['category_name']),$selected);
					}
				 }
				 else
				 {
				 	$cat_data.=$this->populateComboList($cat_id,$cat_name,$selected);
				 }
				 return $cat_data;
				 
		   }
		    function getSubCategories_ajax($cat_id,$cat_name)
		   {
			     $query="SELECT * FROM category_master WHERE parent_id='$cat_id' order by category_name";
				 $getResult=mysql_query($query);
				 if(mysql_num_rows($getResult)>0)
				 {
				 	while($result=mysql_fetch_array($getResult))
					{
						$cat_data.=$this->getSubCategories_ajax($result['category_id'],$cat_name." -> " .ucfirst($result['category_name']));
					}
				 }
				 else
				 {
				 	$cat_data.=$cat_id."@@@@".$cat_name."!!!!";//$cat_data.=$this->populateComboList($cat_id,$cat_name,$selected);
				 }
				 return $cat_data;
				 
		   }
		// display order status combo
		function getOrder($result, $id)
		   {
			//echo $id;
			
			  while($data = mysql_fetch_assoc($result))
			   { 
				   if($id != "")
				   {
				   		if($data[status_id]==$id)
						{   $sel = "selected" ;
							
						}
				   		else                              $sel = "" ;
					}
					else
					{
						 $sel = "" ;
					}
					$resultData .= "<option value='".$data[status_id]."' ".$sel.">".$data[status_name]."</option>" ;
				   
			   }
				   return $resultData ;
		   }




		// display color combo
		function getColor($result, $color)
		   {
			 //echo $id;
			  while($data = mysql_fetch_assoc($result))
			   { 
				   if($color != "")
				   {
				   		if($data[status_id]==$id)   $sel = "Selected" ;
				   		else                              $sel = "" ;
					}
					else
					{
						 $sel = "" ;
					}
					$resultData .= "<option value='".$data[status_id]."' ".$sel.">".$data[status_name]."</option>" ;
				   
			   }
				   return $resultData ;
		   }
		 //geting all the variables which are post/get
		 function getRequestedData()
		   {
			   if($_GET && empty($_POST))
				   $GetVar = $_GET ;
				else
				   $GetVar = $_POST ;

				$GetVar = $GetVar ;

				return $GetVar ;
		   }

		 //redirecting user to another page
         function redirect($page)
		  {
			 echo "<script>window.location='$page'</script>" ;
			 exit;
		  }
		  
		   //firing query 
		  function selectSqlPaging($condition='',$stlimit='',$orderBy='')
		  {
			 
			  if(trim($stlimit))
				  $RecordLimit = " LIMIT ".$stlimit .",".$this->rpp  ;
			  else if($stlimit == "0")
				  $RecordLimit = " LIMIT ".$stlimit .",".$this->rpp  ;
			  else
				  $RecordLimit = "" ;

			  if(trim($orderBy))
				  $RecordorderBy = " ORDER BY ".$orderBy ;
			  else
				  $RecordorderBy = "" ;
			  
			  if(trim($condition))
				 $sql = "SELECT * FROM ".$this->table_name." WHERE ".$condition.$RecordorderBy.$RecordLimit ; 
			  else
				  $sql = "SELECT * FROM ".$this->table_name.$RecordorderBy.$RecordLimit ;
		
			  $result = mysql_query($sql) ;
			  
			  return $result ;
		  }
		  //get page limits
		 function getPageLimit($pageLimit)
		 {
			 if(trim($pageLimit))
				 $startLimit = ($pageLimit-1) * $this->rpp ;
			 else
				 $startLimit = 0 ;

			 return $startLimit ;
		 }
		  //get list number
			 function ListNumber($pageNumber)
			 {
				  if($pageNumber)
					  $pageCount = ++$pageNumber ;
				  else
					  $pageCount = 1 ;
	
				  return $pageCount ;
			 }
		  
		  
		  function displayPaging($num,$currentPage)
		   {
		    	
				//$this->rpp;
                //echo $num>$this->cct;				
			 if($num>$this->rpp)
			   {
				   $ct = $num / $this->rpp ;
				   $ct = ceil($ct) ;
				  			   
				   if( $currentPage > 1 )
						$pagingPrevious = "<a class='blacklink' href='javascript: getpage(".($currentPage-1).");'>Previous</a> &nbsp;&nbsp;&nbsp;&nbsp;" ;
				   else
					    $pagingPrevious = "Previous &nbsp;&nbsp;&nbsp;&nbsp;" ;

				   if($currentPage < $ct && $ct >= 5)
					    $pagingEnd = " ..." ;
				   else
					    $pagingEnd = "" ;

				   if($currentPage > 6 )
					   $pagingStart = "... " ;
				   else
					   $pagingStart = "" ;
				   
				   if( $currentPage < 5 && $ct >= 5)
				   {
					  $cct = 5 ;
					  $start = 1 ;
				   }
				   else if($currentPage < 5 && $ct < 5)
				   {
					  $cct = $ct ;
					  $start = 1 ;
				   }
				   else
				   {
					  $start = $currentPage - 5 ;

					  if($start == 0 ) $start = 1 ;
					  $cct = $currentPage ;
				   }
					$this->start_count=($currentPage-1)*$this->rpp;
					$this->end_count=$this->start_count+$this->rpp;
					if($this->end_count>$num)
						$this->end_count=$num;
					if($this->start_count<=0 && $this->end_count>0)
						$this->start_count=1;
					
				   for($i=$start;$i<=$cct;$i++)
					 {  
						if($currentPage == $i || !trim($currentPage) && $i == 1)
							$paging .= " <b>$i&nbsp;</b> " ;
					    else
							$paging .= "<a class='blacklink' href='javascript: getpage($i);'>$i&nbsp;</a>" ;
							
					 }
				  
				  if( trim($currentPage) == "" ) $currentPage++ ;

				  if( $currentPage < $ct )
					  $pagingNext = "&nbsp;&nbsp;&nbsp;&nbsp; <a class='blacklink' href='javascript: getpage(".($currentPage+1).");'>Next</a>" ;
				  else
					  $pagingNext = "&nbsp;&nbsp;&nbsp;&nbsp; Next" ;

				  $paging = $pagingPrevious.$pagingStart.$paging.$pagingEnd.$pagingNext ;
			   }
			  else
			   {
				  $paging = "" ;
			   }
			 		   			   
			  return $paging ;
		   }
		   
		   
		   function getCountry($result, $country_id='')
		   {
			 
			  while($data = mysql_fetch_assoc($result))
			   {
				   if($country_id != "")
				   {
				   		if($data[country_id ]==$country_id)   $sel = "Selected" ;
				   		else                              $sel = "" ;
					}
					else
					{
						 $sel = "" ;
					}
					$resultData .= "<option value='".$data[country_id]."' ".$sel.">".$data[country_name]."</option>" ;
				   
			   }
				   return $resultData ;
		   }
		   
    function getip() 
	{
	   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	  $ip = getenv("HTTP_CLIENT_IP");
	
	   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	   $ip = getenv("HTTP_X_FORWARDED_FOR");
	
	   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	   $ip = getenv("REMOTE_ADDR");
	
	   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	   $ip = $_SERVER['REMOTE_ADDR'];
	
	   else
	   $ip = "unknown"; /* worst case...I should never be expecting this. */
	
	   return($ip);
	}
	function getCatProductCount($cat_id)
	{
		$query="SELECT COUNT(*) as cat_count FROM category_master WHERE parent_id='$cat_id'";
		$getResult=mysql_query($query);
		$cat_count=0;
		if(mysql_num_rows($getResult)>0)
		{
			while($result=mysql_fetch_array($getResult))
			{
				$cat_count=$result['cat_count'];
			}
		}	
		if($cat_count==0)
		{
			$query="SELECT COUNT(*) as cat_count FROM product_details WHERE scategory_id='$cat_id' and status=1 and trash='N'";
			$getResult=mysql_query($query);
			$cat_count=0;
			if(mysql_num_rows($getResult)>0)
			{
				while($result=mysql_fetch_array($getResult))
				{
					$p_count=$result['cat_count'];
				}
			}	
			return "($p_count images)";
		}
		else
		{
			return "($cat_count images)";
		}
	}
	function getNextProduct($cat,$sub,$pageno)
	{
		$pageno=(int) $pageno;
		if($pageno=="")
			$pageno=0;
		$pageno++;
		if(trim($_SESSION['sess_search_query'])=="")
			$_SESSION['sess_search_query']=" status=1 and trash='N' order by featured desc";
		$query="SELECT product_id FROM product_details WHERE ".$_SESSION['sess_search_query']." LIMIT $pageno,1";
		$getResult=mysql_query($query);
		if(mysql_num_rows($getResult)>0)
		{
			while($result = mysql_fetch_array($getResult))
			{
				return $result['product_id'];
			}
		}
		else
			return -1;
	}
		 ############################################  end genaral #############################################

	 }
?>