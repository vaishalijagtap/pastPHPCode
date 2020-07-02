<?
$this->AddCssUrl(SITE_URL.'css/CalendarControl.css');
$this->AddJavascriptUrl(SITE_URL.'js/calendarcontrol.js');
/*if(!$this->ValidateSession('user')) {
	$_SESSION['SESS_PRODUCT_DATA'] = $_POST;
	$this->RedirectUrl('login/index/',"redirect=".$this->MakeUrl('cart/add/','step=1'));
}*/
$cart=new Cart(session_id(),$this);

switch($this->mSubModule)
{
	case "add":
		if($_SESSION['SESS_PRODUCT_DATA']){ 
			//$_POST = $_SESSION['SESS_PRODUCT_DATA'];
			//unset($_SESSION['SESS_PRODUCT_DATA']);
		}
		if($_POST['id']!="") {
			$cart->AddToCart($_POST['id']);
		}
		$this->RedirectUrl('cart/index/');
		break;
	case "update":
		if($_POST['act']==$this->mPageName) {
			$cart->UpdateCart($_POST);
		}
		$this->RedirectUrl('cart/index/');
		break;
	case "delete":
		$_SESSION['sess_step_shiptype']['selshippingclass'.$_GET['id']] = "";
		if($_GET['id']!="") {
			$cart->DeleteFromCart(trim($_GET['id']), trim($_GET['pid']));
		}
		$this->RedirectUrl('cart/index/');
		break;
	
	
	case "shipping":
	
	$ship_city		=	stripslashes($_SESSION['sess_step_2']['txtscity']);
	if($_SESSION['sess_step_2']['txtsstate']) 
		$ship_state	=	$_SESSION['sess_step_2']['txtsstate'];
	else
		$ship_state	=	$_SESSION['sess_step_2']['txtsotherstate'];
	$ship_zipcode	=	stripslashes($_SESSION['sess_step_2']['txtszip']);
	$ship_country	=	$_SESSION['sess_step_2']['txtscountry'];
	
	$weight_total 	= trim($_POST['total_weight']);
	$cart_total 	= trim($_POST['cart_total']);
	$sub_total		= trim($_POST['sub_total']);
	$saletax		= trim($_POST['sales_tax']);
	$promo_discount = trim($_POST['promo_discount']);
	$countryCode = $this->getCountryAbb($ship_country);
	if($_POST['selService'] != 'ISP'){
		/************************* Assigning required parameter values to request array *********************************/
		$shipConfirmRequestArray = array('shipToCompanyName'=>addslashes($_SESSION['sess_step_2']['txtsname']),
									'shipToAttentionName'=>addslashes($_SESSION['sess_step_2']['txtsname']),
									'shipToPhoneNumber'=>addslashes($_SESSION['sess_step_2']['txtsphone']),
									'shipToAddressLine1'=>addslashes($_SESSION['sess_step_2']['txtsaddress']),
									'shipToCity'=>$ship_city,
									'shipToStateProvinceCode'=>$ship_state,
									'shipToCountryCode'=>$countryCode,
									'shipToPostalCode'=>$ship_zipcode,
									'serviceCode'=>$this->getShippingMethodCode($_POST['selService']),	
									'serviceDescription'=>'',
									'pickupDate'=>'20100422',
									'weight'=>$weight_total);
		
		/*************************** Function GetShippingAmount returns response array  *********************************/							
		$shipArr = $this->GetShippingAmount($shipConfirmRequestArray);
		$quote = $shipArr['shipping_charge'];
   	}else{
		$quote = '0.00';
	}
	
	//echo $quote; die;
	/*************************************************************************************
		if the customer is not qualify for free shipping then add full charges 
	**************************************************************************************/
	
	if(is_numeric($quote)){
		///////// if customer purchase above minimum shipping amount set by admin then basic ground shipping is free
		if(($cart->getAmountToQualifyFreeShipping($sub_total)==0) && ($_POST['selService'] == 'GND') || $_POST['selService'] == 'ISP'){
			$quote = '0.00';
		}	
		$total_amt = $sub_total + $quote;
		$applicableTax = ($total_amt*($saletax/100));
		$cart_total = $total_amt + $applicableTax - $promo_discount;
	?>
		<script language="javascript">
			parent.document.getElementById('btn_submit_payment').disabled="";
			//parent.document.getElementById("btn_submit_payment").setAttribute("class", "btn_complete_order");
			parent.document.getElementById("btn_submit_payment").className="btn_complete_order";
			
			parent.document.getElementById('shipping_err').innerHTML= "";
			parent.document.getElementById('div_shipping_charges').innerHTML = "+$<?=sprintf('%01.2f', $quote)?>";
			parent.document.getElementById('div_sales_tax').innerHTML = "+$<?=sprintf('%01.2f', $applicableTax)?>";
			parent.document.getElementById('shippingrow').style.display = '';
			parent.document.getElementById('selected_shipping').value= '<?=$_POST['selService']?>';
			parent.document.getElementById('div_cart_total').innerHTML= "$<?=sprintf('%01.2f', $cart_total)?>";
		</script>
	<?
	}else{
		$shipping_error = $shipArr['shipping_err'];
		
		$total_amt = $sub_total - $promo_discount;
		$applicableTax = ($total_amt*($saletax/100));
		$cart_total = $total_amt + $applicableTax;
	?>
		<script>
			parent.document.getElementById('shipping_err').innerHTML= "<?=$shipping_error?>";
			
			parent.document.getElementById('btn_submit_payment').disabled="yes";
			parent.document.getElementById('div_sales_tax').innerHTML = "+$<?=sprintf('%01.2f', $applicableTax)?>";
			//parent.document.getElementById("btn_submit_payment").setAttribute("class", "btn_complete_order_disable");
			parent.document.getElementById("btn_submit_payment").className="btn_complete_order_disable";
			
			parent.document.getElementById('shippingrow').style.display = 'none';
			parent.document.getElementById('selected_shipping').value= '';
			//parent.document.getElementById('div_payment').style.display = 'none';
			parent.document.getElementById('div_cart_total').innerHTML= "$<?=sprintf('%01.2f', $cart_total)?>";
		</script>
	<?
	}
	?>
		<script language="javascript">
			//parent.document.getElementById('btn_submit_payment').disabled="yes";
			parent.document.getElementById('shipping_proccess').style.display="none";
			parent.document.getElementById('shipping_proccess_img').style.display="none";
		</script>
	<?
	exit;
	break;
	case "order":
	
		///redirecting to SSL
		$this->redirectToHTTPS();
		
		if($_POST['action']==$this->mPageName)
		{
		//////// Checking and updating Promotional code values
			if(trim($_POST['hpromo'])){
				$promocode = trim($_POST['hpromo']);
				$promoerr = $cart->validatePromotionalCode($promocode);
				if($promoerr==''){
					$promoArr = $this->Select(TABLE_PROMOCODE, "promo_code='".$promocode."' AND exp_date >= CURDATE()", "promo_code_id, quantity, discount_rate, exp_date, promo_type");
					
					
					if($promoArr[0]['promo_type']=='L'){ //update and set is used value to 1
						
						$usedPromoCount = $cart->usedPromoCodeCount($promoArr[0]['promo_code_id']);
						if(($usedPromoCount>=$promoArr[0]['quantity']) && ($promoArr[0]['quantity'] > 0)){
							?>
							<script language="javascript">
								parent.window.location = '<?=$this->MakeUrl('cart/index/','step=3&promoerr=1')?>';
							</script>
							<?	
						}else{
						$this->Update(TABLE_PROMOCODE_CUSTOMER,array('is_used'=>'\'1\''),"customer_id ='".$_SESSION['sess_user_id']."' and promo_code_id='".$promoArr[0]['promo_code_id']."'");
						}
					}elseif($promoArr[0]['promo_type']=='O'){ //inser  and set is used to 1 if open type
						$data_arr=array('promo_code_id'=>"'".$promoArr[0]['promo_code_id']."'",'customer_id'=>"'".$_SESSION['sess_user_id']."'",'is_used'=>'\'1\'');
						
						$usedPromoCount = $cart->usedPromoCodeCount($promoArr[0]['promo_code_id']);
						if(($usedPromoCount>=$promoArr[0]['quantity']) && ($promoArr[0]['quantity']!=0)){
							?>
							<script language="javascript">
								parent.window.location = '<?=$this->MakeUrl('cart/index/','step=3&promoerr=1')?>';
							</script>
							<?
						}else{
							$promoinsertid = $this->Insert(TABLE_PROMOCODE_CUSTOMER,$data_arr);
						}
					}
				}else{ // promoerr
					//redirect to payment page and display invalid promo code message
					?>
					<script language="javascript">
						parent.window.location = '<?=$this->MakeUrl('cart/index/','step=3&promoerr=1')?>';
					</script>
					<?
					exit;
				}
			}//end if of main
			
			
			$bill_name 		= 	$_SESSION['sess_step_2']['txtname'].' '.$_SESSION['sess_step_2']['txtlname'];
			$bill_address 	= 	addslashes($_SESSION['sess_step_2']['txtaddress']);
			$bill_city 		= 	addslashes($_SESSION['sess_step_2']['txtcity']);
			if($_SESSION['sess_step_2']['txtstate'])
				$bill_state		=	$_SESSION['sess_step_2']['txtstate'];
			else
				$bill_state		=	addslashes($_SESSION['sess_step_2']['txtotherstate']);
			$bill_zipcode	=	addslashes($_SESSION['sess_step_2']['txtzip']);
			$bill_country	=	$_SESSION['sess_step_2']['txtcountry'];
			$bill_phone		=	addslashes($_SESSION['sess_step_2']['txtphone']);
			
			$ship_name 		= 	$_SESSION['sess_step_2']['txtsname'].' '.$_SESSION['sess_step_2']['txtlsname'];
			$ship_name		=	addslashes($ship_name);
			$ship_address	=	addslashes($_SESSION['sess_step_2']['txtsaddress']);
			$ship_city		=	addslashes($_SESSION['sess_step_2']['txtscity']);
			if($_SESSION['sess_step_2']['txtsstate'])
				$ship_state		=	$_SESSION['sess_step_2']['txtsstate'];
			else
				$ship_state		=	addslashes($_SESSION['sess_step_2']['txtsotherstate']);
				
			$ship_zipcode	=	addslashes($_SESSION['sess_step_2']['txtszip']);
			$ship_country	=	$_SESSION['sess_step_2']['txtscountry'];
			$ship_phone		=	addslashes($_SESSION['sess_step_2']['txtsphone']);
			$e_mail			=	addslashes($_SESSION['sess_step_2']['txtemail']);
			
			$customer_id	=	"'".$_SESSION['sess_user_id']."'";
			$product_total	=	"'0.00'";
			$total_tax		=	"'0.00'";
			$order_total	=	"'0.00'";
			$status			=	"'approved'";
			$order_date		=	"NOW()";
			$payment_gateway=	"'Authorize Net'";
			
			/////////////////////////////PREPARING BILLING DETAILS FOR DATABASE/////////////////////////////////////////////////
			$post_arr['bill_name']			=	$this->MysqlString($bill_name);
			$post_arr['bill_address']		=	$this->MysqlString($bill_address);
			$post_arr['bill_city']			=	$this->MysqlString($bill_city);
			$post_arr['bill_state']			=	$this->MysqlString($bill_state);
			$post_arr['bill_zipcode']		=	$this->MysqlString($bill_zipcode);
			$post_arr['bill_country']		=	$this->MysqlString($bill_country);
			$post_arr['bill_phone']			=	$this->MysqlString($bill_phone);
			
			/////////////////////////////PREPARING SHIPPING DETAILS FOR DATABASE/////////////////////////////////////////////////
			
			$post_arr['ship_name']			=	$this->MysqlString($ship_name);
			$post_arr['ship_address']		=	$this->MysqlString($ship_address);
			$post_arr['ship_city']			=	$this->MysqlString($ship_city);
			$post_arr['ship_state']			=	$this->MysqlString($ship_state);
			$post_arr['ship_zipcode']		=	$this->MysqlString($ship_zipcode);
			$post_arr['ship_country']		=	$this->MysqlString($ship_country);
			$post_arr['ship_phone']			=	$this->MysqlString($ship_phone);
			$post_arr['e_mail']				=	$this->MysqlString($e_mail);
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$post_arr['customer_id']		=	$customer_id;
			$post_arr['product_total']		=	$product_total;
			$post_arr['total_tax']			=	$total_tax;
			
			$post_arr['order_total']		=	$order_total;
			$post_arr['status']				=	$status;
			$post_arr['order_date']			=	$order_date;
			$post_arr['payment_gateway']	=	$payment_gateway;
			
			//////////////////////////////Validating and Calculating shippiing amount////////////////////////////////////////////
			
			$parr = $cart->getOrderDetails();	///the function will return discounted subtotal
			$weight_total = $parr['weight_total'];
			$sub_total = $parr['sub_total'];
			 
			$starLevelDiscount = $parr['starLevelDiscount'];
			
			$countryCode = $this->getCountryAbb($ship_country);
	
			if($_POST['selected_shipping'] != 'ISP'){
				/************************* Assigning required parameter values to request array *********************************/
				$shipConfirmRequestArray = array('shipToCompanyName'=>$ship_name,
											'shipToAttentionName'=>$ship_name,
											'shipToPhoneNumber'=>$ship_phone,
											'shipToAddressLine1'=>$ship_address,
											'shipToCity'=>$ship_city,
											'shipToStateProvinceCode'=>$ship_state,
											'shipToCountryCode'=>$countryCode,
											'shipToPostalCode'=>$ship_zipcode,
											'serviceCode'=>$this->getShippingMethodCode($_POST['selected_shipping']),	
											'serviceDescription'=>'',
											'pickupDate'=>'20100422',
											'weight'=>$weight_total);
											
				/*************************** Function GetShippingAmount returns response array  *********************************/							
				$shipArr = $this->GetShippingAmount($shipConfirmRequestArray);
				$quote = $shipArr['shipping_charge'];
			}else{
				$quote = '0.00';
			}
			if(!is_numeric($quote)){
				?>
					<script language="javascript">
						
						parent.document.getElementById('payment_proccess').style.display="none";
						parent.document.getElementById('payment_proccess_img').style.display="none";
						alert("Transaction Denied.");
					</script>
					<?
					exit;	
			}
			/*************************************************************************************
				if the customer is not qualify for free shipping then add full charges 
			**************************************************************************************/
			
			///////// if customer purchase above minimum shipping amount set by admin then basic ground shipping is free
			if(($cart->getAmountToQualifyFreeShipping($sub_total)==0) && ($_POST['selected_shipping'] == 'GND') || $_POST['selected_shipping'] == 'ISP'){
				$shippingCharges = '0.00';
			}else{
				$shippingCharges = $quote;
			}	
			
			/*************************************************************************************** 
				Applying Promotional Code Discount 
			***************************************************************************************/
			if($_POST['hpromo']){
				$promocode = trim($_POST['hpromo']);
				$promoArr = $this->Select(TABLE_PROMOCODE, "promo_code='".$promocode."' AND exp_date >= CURDATE()", "promo_code_id, quantity, discount_rate, exp_date, promo_type");
				
				$promoDiscount = $promoArr[0]['discount_rate'];
				$promoDiscountedPrice = ($sub_total*($promoDiscount/100));
				
			}//end if 
			
			//////////////////------CACULATING TAX AND DISCOUNTS--------//////////////////////////
			/*************************************************************************************
				getCalculatedSaleTaxForCustomer function calculates how much sale tax is 
				applicable for customer based on tax exempt or state and return tax in percentage 
			**************************************************************************************/
			$saletax = $cart->getCalculatedSaleTaxForCustomer($bill_state, $ship_state, $_SESSION['sess_user_id'], $sub_total);
			/*************************************************************************************
				based on the tax percentage return by  getCalculatedSaleTaxForCustomer function
				calculate the applicable tax on subtotal amount or cart
			**************************************************************************************/
			// code added for applying sales tax on addition of all prices
			$promoDiscountedPrice = sprintf("%01.2f",$promoDiscountedPrice);
			$total_amt = $sub_total + $shippingCharges;
			if($saletax>0){
				$applicableTax = ($total_amt*($saletax/100));
			}else{
				$applicableTax = 0;
			}
				
			/*************************************************************************************
				Calculating Cart Grand Total
			**************************************************************************************/
			$cartGrandTotal = ($sub_total+$applicableTax+$shippingCharges) - $promoDiscountedPrice; 
			
			/*************************************************************************************** 
				PREPARING ARRAY FOR PAYMENT GATEWAY
			***************************************************************************************/
			$pay_data=array();
			$pay_data['orderid']=uniqid('ord_');
			$pay_data['expirymonth']=addslashes($_POST['txtexpirymonth']);
			$pay_data['expiryyear']=addslashes($_POST['txtexpiryyear']);
			$pay_data['cardno']=addslashes($_POST['txtccnumber']);
			$pay_data['nameoncard']=addslashes($_POST['txtccname']);
			$pay_data['cvvcode']=addslashes($_POST['txtcccvv']);
			$pay_data['grand_total']=	$cartGrandTotal;
			
			$billernameArr = explode(' ', addslashes($_SESSION['sess_step_2']['txtname']));
			
			$pay_data['first_name']	=	$billernameArr[0];
			$pay_data['last_name']	=	$billernameArr[count($billernameArr)-1];
			$pay_data['address']=	$bill_address;
			$pay_data['city']	=	$bill_city;
			$pay_data['state']	=	$bill_state;
			$pay_data['country']=	$bill_country;
			$pay_data['phone']	=	$bill_phone;
			$pay_data['email']	=	$e_mail;
			$pay_data['zip']	=	$bill_zipcode;
			
			
			////PROCESSING PAYMENT DATA
			$processResponseArr = $this->ProcessPayment($pay_data);
			if($processResponseArr['status']=='APPROVED'){
					//$post_arr['gateway_order_id']=$this->MysqlString($_POST['orderid']);
					//echo $post_arr['bill_name']; exit;
					$order_id=$this->Insert(TABLE_CART_ORDER,$post_arr);
					
					$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'","*","cart_id");
					if(count($session_data)>0) {
						foreach($session_data as $data) {
							$product=$cart->GetProductDetails($data['product_id']);
							$weight	= $product['weight'];
							$weight_unit = $product['weight_unit'];
							if($weight_unit=='Ounce'){
								$weight = round(($weight/16), 2);
							}
							$weight_unit = ' lbs';
							
							$productPrice = $data['price'];
							$sellingPrice = $data['price'];
							
							/*************************************************************************************
								getStarLevelDiscount function return calculated discount price on customer starlevel.
							**************************************************************************************/
							$starLevelDiscountedPrice = 0;
							$starlevelDiscountArr = $cart->getStarLevelDiscount($_SESSION['sess_user_id'], $productPrice);
							$starLevelDiscount = $starlevelDiscountArr['discount'];
							$starLevelDiscountedPrice = $starlevelDiscountArr['dicountedPrice'];
							/*******************************Setting star level Discount****************************************/
							if($starLevelDiscountedPrice > 0 && $productPrice > $starLevelDiscountedPrice){
								$productPrice = ($productPrice - $starLevelDiscountedPrice);
							}
							
							$post_arr=array();
							$post_arr['cart_order_id']=$order_id;
							$post_arr['product_id']=$this->MysqlString($data['product_id']);
							$post_arr['item_no']=$this->MysqlString($product['item_no']);
							$post_arr['product_name']=$this->MysqlString($product['product_name']);
							$post_arr['color_id']=$product['color_id'];
							$post_arr['color_name']=$this->MysqlString($product['color_name']);
							$post_arr['quantity']=$data['quantity'];
							$post_arr['weight']=$this->MysqlString($weight);
							$post_arr['weight_unit']=$this->MysqlString($weight_unit);
							$post_arr['size']=$this->MysqlString($product['size']);
							$post_arr['size_unit']=$this->MysqlString($product['size_unit']);
							$post_arr['selling_price']=$this->MysqlFloat(sprintf("%01.2f",$sellingPrice));
							$post_arr['price']=$this->MysqlString(sprintf("%01.2f",$productPrice));
							$invoice_id=$this->Insert(TABLE_CART_ORDER_INVOICE,$post_arr);
							$product_total+=($data['quantity']*$data['price']);
						}
					} 
					
					$post_arr=array();
					$post_arr['sale_tax']=$this->MysqlFloat($saletax);
					$post_arr['star_level_discount']=$this->MysqlFloat($starLevelDiscount);
					$post_arr['promocode_discount']=$this->MysqlFloat($promoDiscount);
					$post_arr['subtotal']=$this->MysqlFloat($sub_total);
					$post_arr['shipping_method']=$this->MysqlString($_POST['selected_shipping']);
					$post_arr['weight_total']=$this->MysqlFloat($weight_total);
					$post_arr['total_ship']=$this->MysqlFloat($shippingCharges);
					$post_arr['product_total']=$this->MysqlFloat($product_total);
					$post_arr['gateway_order_id'] = $this->MysqlString($processResponseArr['transactionId']);
					$post_arr['ip'] = $this->MysqlString($_SERVER['REMOTE_ADDR']);
					$post_arr['order_total']=$this->MysqlFloat($cartGrandTotal);
					
					$this->Update(TABLE_CART_ORDER,$post_arr,"cart_order_id='".$order_id."'");
					$this->Delete(TABLE_CART_SESSION,"session_id='".session_id()."'");
					$this->Update(TABLE_CART_ORDER,array('is_active'=>"'1'",'user_activated'=>"'1'"),"cart_order_id='".$order_id."'");
					$this->createOneirOrderFile($order_id);
					/////// Setting Value of $mode variable to manual if ship country is other than USA //////
					//$mode = '';
					//if($ship_country!='USA')	$mode = 'manual';
					$this->OrderEmail($order_id,'new', 'user');
					$this->OrderEmail($order_id,'new', 'admin',$mode);
					
					?>
					<script language="javascript">
						parent.window.location = '<?=$this->MakeUrl('cart/index/','step=4&oid='.sprintf("%04d",$order_id));?>';
					</script>
					<?
					exit;
				} else {
					
					if($promoArr[0]['promo_type']=='L'){ //update and set is used value to 0
						$this->Update(TABLE_PROMOCODE_CUSTOMER,array('is_used'=>'\'0\''),"customer_id ='".$_SESSION['sess_user_id']."' and promo_code_id='".$promoArr[0]['promo_code_id']."'");
					}elseif($promoArr[0]['promo_type']=='O'){ // Delete if open type
						$promoinsertid = $this->Delete(TABLE_PROMOCODE_CUSTOMER,"customer_id ='".$_SESSION['sess_user_id']."' AND promo_code_id='".$promoArr[0]['promo_code_id']."'");
					}
					
					?>
					<script language="javascript">
						parent.document.getElementById('txtctype').style.visibility='visible';
						parent.document.getElementById('btn_submit_payment').disabled="";
						//parent.document.getElementById("btn_submit_payment").setAttribute("class", "btn_complete_order");
						parent.document.getElementById("btn_submit_payment").className="btn_complete_order";
						
						parent.document.getElementById('payment_proccess').style.display="none";
						parent.document.getElementById('payment_proccess_img').style.display="none";
						alert("Transaction Failed:<?=addslashes($processResponseArr['responseReason'])?>");
					</script>
					<?
					exit;
			} 
		}

	default: 
		if($_GET['step']==4) { //step4 payment notification
			
			include('html/cart_4.html');
			
			if(!$this->ValidateSession('user')) {
				$_SESSION['sess_step_2']="";
			} 
			$_SESSION['sess_step_shiptype']="";
		} elseif($_GET['step']==3) { //step3 payment page
			////redirecting to SSL page
			$this->redirectToHTTPS();
			
			if(!$this->ValidateSession('user')) {
				$this->RedirectUrl('login/index/',"redirect=".$this->MakeUrl('cart/index/','step=2'));
			}
			
			///updating session values from step 2
			foreach($_POST as $key=>$value) {
				if(substr($key,0,3)=='txt') {
					$_SESSION['sess_step_2'][$key]=$value;
				}
			}
		
			///feching cart data
			$cartDataArr = $cart->getCartProducts();
			// If data is not present, redirect to cart page (step1)
			if($cartDataArr['cart_emty']==1)  {
				$this->RedirectUrl('cart/index/');
			}
			///setting values
			$sub_total = $cartDataArr['sub_total'];
			$weight_total = $cartDataArr['weight_total'];
			$minimumAmountToQualifyFreeShipping = $cartDataArr['minimumAmountToQualifyFreeShipping'];
			$amountToQualifyFreeShipping = $cartDataArr['amountToQualifyFreeShipping'];
			
			$saletax = $cartDataArr['saletax']; ////tax in percent
			$applicableTax = $cartDataArr['applicableTax']; //tax in amount
			//if($cartDataArr['shippingCharges']==0) $shippingCharges = 'Free'; else $shippingCharges = '$'.$cartDataArr['shippingCharges'];
			$shippingCharges = $cartDataArr['shippingCharges'];
			
			
			$starLevelDiscountedPrice = $cartDataArr['starLevelDiscountedPrice'];
			$starLevelDiscount = $cartDataArr['starLevelDiscount'];
			
			///promotional code discount
			$promoDiscountedPrice = $cartDataArr['promoDiscountedPrice'];
			$promoDiscount = $cartDataArr['promoDiscount'];
			$promoerr = $cartDataArr['promoerr'];
			
			
			$cart_total = $cartDataArr['cart_total'];
			$cart_data = $cartDataArr['cart_data']; 
			
			$credit_card_data = $this->getCreditCardTypeData();
			include('html/cart_3.html');
		} elseif($_GET['step']==2) { //step2 checkout page
			$this->redirectToHTTPS();
			
			if(!$this->ValidateSession('user')) {
				$this->RedirectUrl('login/index/',"redirect=".$this->MakeUrl('cart/index/','step=2'));
			} else {
				/********** Updating Customer_id in Cart session table for sending mail if not completed order ********/
				//print_r($_SESSION); exit;
				$data_arr=array('customer_id'=>"'".$_SESSION['sess_user_id']."'");
				$this->Update(TABLE_CART_SESSION,$data_arr,"session_id='".$cart->mSessionId."'");
				/******************************************************************************************************/
			}
			if($this->ValidateSession('user') && empty($_SESSION['sess_step_2'])) { 
				$user_data=$this->Select(TABLE_CUSTOMERS,"customer_id='".$_SESSION['sess_user_id']."'","*");
				$name = "";
				if(count($user_data)>0) {
					foreach($user_data as $arr) {
						$data_arr=$arr;
					}
				}
				
				$name = ucfirst(stripslashes($data_arr['first_name']));
				if(trim($data_arr['last_name']))
					$name = $name.' '.ucfirst(stripslashes($data_arr['last_name']));
					
				$_SESSION['sess_step_2']["txtname"] = $name;
				$_SESSION['sess_step_2']["txtaddress"] = stripslashes($data_arr['address']);
				$_SESSION['sess_step_2']["txtemail"] = stripslashes($data_arr['email']);
				$_SESSION['sess_step_2']["txtphone"] = stripslashes($data_arr['phone']);
				$_SESSION['sess_step_2']["txtcity"] = stripslashes($data_arr['city']);
				$_SESSION['sess_step_2']["txtstate"] = stripslashes($data_arr['state']);
				$_SESSION['sess_step_2']["txtzip"] = stripslashes($data_arr['zip']);
				$_SESSION['sess_step_2']["txtbname"] = stripslashes($data_arr['business_name']);
				
				$_SESSION['sess_step_2']["txtcountry"] = stripslashes($data_arr['country']);
				$_SESSION['sess_step_2']["txtstate"] = stripslashes($data_arr['state']);
				if($data_arr['country']!='USA')
					$_SESSION['sess_step_2']["txtotherstate"] = stripslashes($data_arr['state']);
				
				///shipping details
				$sname = ucfirst(stripslashes($data_arr['ship_name']));
				
				$_SESSION['sess_step_2']["txtsname"] = $sname;
				$_SESSION['sess_step_2']["txtsaddress"] = stripslashes($data_arr['ship_address']);
				$_SESSION['sess_step_2']["txtscity"] = stripslashes($data_arr['ship_city']);
				$_SESSION['sess_step_2']["txtsstate"] = stripslashes($data_arr['ship_state']);
				$_SESSION['sess_step_2']["txtszip"] = stripslashes($data_arr['ship_zip']);
				
				$_SESSION['sess_step_2']["txtscountry"] = stripslashes($data_arr['ship_country']);
				$_SESSION['sess_step_2']["txtsstate"] = stripslashes($data_arr['ship_state']);
				if($data_arr['ship_country']!='USA')
					$_SESSION['sess_step_2']["txtsotherstate"] = stripslashes($data_arr['ship_state']);
				
			}
			//print_r($_SESSION);
			///feching cart data
			$cartDataArr = $cart->getCartProducts(); ////function of cart class 
			// If data is not present, redirect to cart page (step1)
			if($cartDataArr['cart_emty']==1)  {
				$this->RedirectUrl('cart/index/');
			}
			$sub_total = $cartDataArr['sub_total'];//sub total
			$cart_total = $cartDataArr['cart_total']; ///Grand total
			$cart_total_step2 = $cartDataArr['cart_total_step2']; //sub sub total may have star level disocunt
			$weight_total = $cartDataArr['weight_total'];
			$minimumAmountToQualifyFreeShipping = $cartDataArr['minimumAmountToQualifyFreeShipping'];
			$amountToQualifyFreeShipping = $cartDataArr['amountToQualifyFreeShipping'];
			//$amountToQualifyFreeShipping = $amountToQualifyFreeShipping==0 ? 'Free' : '$'.$amountToQualifyFreeShipping;
			$saletax = $cartDataArr['saletax']; ////tax in percent
			$applicableTax = $cartDataArr['applicableTax']; //tax in amount
			//if($cartDataArr['shippingCharges']==0) $shippingCharges = 'Free'; else $shippingCharges = '$'.$cartDataArr['shippingCharges'];
			$shippingCharges = $cartDataArr['shippingCharges'];
			///starlevel discount variables
			$starLevelDiscountedPrice = $cartDataArr['starLevelDiscountedPrice'];
			$starLevelDiscount = $cartDataArr['starLevelDiscount'];
			
			//////updating discount values in cart session table
			
			//$discountArr = $this->Select(TABLE_CART_SESSION_DISCOUNT, "session_id");
			//$data_arr=array('session_id'=>"'".$this->mSessionId."'",'starlevel_discount'=>"'".$starLevelDiscount."'");
			//$this->mProject->Insert(TABLE_CART_SESSION_DISCOUNT,$data_arr);
			
			$cart_data = $cartDataArr['cart_data']; 
			
			$billing_state_data = $this->GetStateDropDown($_SESSION['sess_step_2']['txtstate']);
			$shipping_state_data = $this->GetStateDropDown($_SESSION['sess_step_2']["txtsstate"]);
		
			$country_data = $this->GetCountryDropDown($_SESSION['sess_step_2']['txtcountry']);
			$state_data=$this->GetStateBasedDropDown($_SESSION['sess_step_2']["txtstate"],"USA","user");
			
			$country_ship_data = $this->GetCountryDropDown($_SESSION['sess_step_2']['txtscountry']);
			$state_ship_data=$this->GetStateBasedDropDown($_SESSION['sess_step_2']["txtsstate"],"USA","user");
			
			include('html/cart_2.html');
			
		} else { 
			//step 1 after adding item to the cart showing cart items where user can edit the cart
			$cartDataArr = $cart->getCartProductsEditable(); ////function of cart class 
			$sub_total = $cartDataArr['sub_total'];
			$cart_total = $cartDataArr['cart_total'];
			$weight_total = $cartDataArr['weight_total'];
			$minimumAmountToQualifyFreeShipping = $cartDataArr['minimumAmountToQualifyFreeShipping'];
			$amountToQualifyFreeShipping = $cartDataArr['amountToQualifyFreeShipping'];
			///if it is 0 then display as free shipping
			//$amountToQualifyFreeShipping = $amountToQualifyFreeShipping==0 ? 'Free' : '$'.$amountToQualifyFreeShipping;
			$shippingCharges = $cartDataArr['shippingCharges'];
			//star level discount
			$starLevelDiscountedPrice = $cartDataArr['starLevelDiscountedPrice'];
			$starLevelDiscount = $cartDataArr['starLevelDiscount'];
			
			$cart_data = $cartDataArr['cart_data']; 
			$cart_emty = $cartDataArr['cart_emty']; 
			include('html/cart_1.html');
		}
		break;
}
?>