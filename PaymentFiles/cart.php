<?php
ob_start();
$this->validateCartData(); 
if($_SESSION['sess_user_id'] != "") $custCartId = $_SESSION['sess_user_id'];
else $custCartId = 0;
//This code is used to set cart session variable according to user.
if($custCartId!=0) {
	$checkOldSession=$this->Select(TABLE_CART_SESSION," date_add(updated_date,interval 24 hour) > NOW() AND customer_id='".$custCartId."' ","*"," updated_date DESC",1);
} else {
	$checkOldSession=array();
}

/*echo "<pre>";
print_r($check_old_session);*/
if(count($checkOldSession) > 0) {
	$cart=new Cart($checkOldSession[0]['session_id'],$this);
	$this->cartSessionId = $checkOldSession[0]['session_id'];	 
}else {	
	$cart=new Cart(session_id(),$this);
	$this->cartSessionId = session_id();	
}
switch($this->mSubModule)
{
	case "validate_test":
		$BillCountry = $_SESSION['sess_step_2']['txt_country'];
		$BillAddress = $this->capitalize($_SESSION['sess_step_2']['txt_street_address']);
		$BillAddress2= $this->capitalize($_SESSION['sess_step_2']['txt_street_address_2']);
		$BillCity = $this->capitalize($_SESSION['sess_step_2']['txt_city']);
		$BillState = strtoupper($_SESSION['sess_step_2']['txt_state']);
		$BillZip = strtoupper($_SESSION['sess_step_2']['txt_zip_code']);
		
		$ShipCountry = $_SESSION['sess_step_2']['txt_country'];
		$ShipAddress = $this->capitalize($_SESSION['sess_step_2']['txt_street_address']);
		$ShipAddress2= $this->capitalize($_SESSION['sess_step_2']['txt_street_address_2']);
		
		$ShipCity = $this->capitalize($_SESSION['sess_step_2']['txt_city']);
		$ShipState = strtoupper($_SESSION['sess_step_2']['txt_state']);
		$ShipZip = strtoupper($_SESSION['sess_step_2']['txt_zip_code']);
		
		$billing_input = array('address1'=>$BillAddress, 'address2'=>$BillAddress2, 'city'=>$BillCity,'state'=>$BillState, 'zip_code'=>$BillZip);
		$billing_response = $this->VerifyUSAddressByUPS($billing_input);
		
		$shipping_input = array('address1'=>$ShipAddress, 'address2'=>$ShipAddress2, 'city'=>$ShipCity,'state'=>$ShipState, 'zip_code'=>$ShipZip);
		$shipping_response = $this->VerifyUSAddressByUPS($shipping_input);
		//echo "<pre>"; print_r($response); echo "</pre>"; die;
		$av1 = 0;
		$av2 = 0;
		$response .='<img src="'.SITE_URL.'images/inner/upslogo.jpg" /><h2>Address Verification by UPS</h2>';
		foreach ($billing_response['list'] as $var) {
			if ($BillAddress == $this->capitalize($var['addr1']) && $BillCity == $this->capitalize($var['city']) && $BillState == $var['state'] && ($BillZip == $var['zip'] || $BillZip == $var['zip'] . "-" . $var['zipExt'])) $av1 = 1;
		} // each returned address
		if ($BillCountry != '229') { 
			$response .='<h2>Foreign Destination</h2>';
		}
		elseif($av1==1) {
			$response .='<h2>VERIFIED!</h2>';
		} else {
			$response ='<table><tr><td valign="top"><strong>Billing Address</strong></td></tr><tr><td valign="top"><label for="choice">Choose From the Following '.(count($response1['list']) + 1).' Addresses:</label></td></tr><tr><td>&nbsp;</td></tr><tr><td><input type="radio" name="choice" id="choice" value="'.$BillAddress . '|' . $BillAddress2 . '|' . $BillCity . '|' . $BillState . '|' . $BillZip.'" />'.$BillAddress . ', ';
			//echo $BillAddress2; die;
			if ($BillAddress2 != '') { $response .= $BillAddress2 . ", "; $response .= $BillCity . ", " . $BillState . " " . $BillZip . ' (your entry)<br />'; }
			
			foreach ($billing_response['list'] as $key1 => $var) {
				//$response .='<br /><br />';
				if ($key1 == 0) $response .='<strong>'; 
				if (strpos($var['addr1'], '-') !== false) { 
					$response .='<input type="button" value="Edit your entry"  /> ';
				 } else { 
					$response .='<input type="radio" name="choice" value="'.$this->capitalize($var['addr1']) . '|'; 
					$response .= $BillAddress2 . '|' . $this->capitalize($var['city']) . '|' . $var['state'] . '|' . $var['zip'] . '|' . $var['zipExt'];
					if ($key1 == 0) { $response .=' checked="checked'; } 
					$response .=' />'; 
				} 
				$response .= $this->capitalize($var['addr1']) . ', '; 
				if ($BillAddress2 != '') $response .=$BillAddress2 . ', '; 
				$response .=$this->capitalize($var['city']) . ', ' . $var['state'] . ' ' . $var['zip']; 
				if ($var['zipExt'] != '') $response .= '-' . $var['zipExt']; 
				if ($key == 0) $response .=' (UPS Response)</strong>'; 
				$response .=' <br />'; 
			} 
			$response .='</td></tr></table>';
		}
		
		foreach ($shipping_response['list'] as $var) {
			if ($ShipAddress == $this->capitalize($var['addr1']) && $ShipCity == $this->capitalize($var['city']) && $ShipState == $var['state'] && ($ShipZip == $var['zip'] || $ShipZip == $var['zip'] . "-" . $var['zipExt'])) $av1 = 1;
		} // each returned address
		
		if ($ShipCountry != '229') { 
			$response .='<h2>Foreign Destination</h2>';
		}
		elseif($av1==1) {
			$response .='<h2>VERIFIED!</h2>';
		} else {
			$response .='<table><tr><td valign="top"><label for="choice">Choose From the Following '.(count($response1['list']) + 1).' Addresses:</label></td></tr><tr><td>&nbsp;</td></tr><tr><td><input type="radio" name="choice" id="choice" value="'.$ShipAddress . '|' . $ShipAddress2 . '|' . $ShipCity . '|' . $ShipState . '|' . $ShipZip.'" />'.$ShipAddress . ', ';
			//echo $BillAddress2; die;
			if ($ShipAddress2 != '') { $response .= $ShipAddress2 . ", "; $response .= $ShipCity . ", " . $ShipState . " " . $ShipZip . ' (your entry)<br />'; }
			
			foreach ($shipping_response['list'] as $key1 => $var) {
				//$response .='<br /><br />';
				if ($key1 == 0) $response .='<strong>'; 
				if (strpos($var['addr1'], '-') !== false) { 
					$response .='<input type="button" value="Edit your entry"  /> ';
				 } else { 
					$response .='<input type="radio" name="choice" value="'.$this->capitalize($var['addr1']) . '|'; 
					$response .= $BillAddress2 . '|' . $this->capitalize($var['city']) . '|' . $var['state'] . '|' . $var['zip'] . '|' . $var['zipExt'];
					if ($key1 == 0) { $response .=' checked="checked'; } 
					$response .=' />'; 
				} 
				$response .= $this->capitalize($var['addr1']) . ', '; 
				if ($BillAddress2 != '') $response .=$BillAddress2 . ', '; 
				$response .=$this->capitalize($var['city']) . ', ' . $var['state'] . ' ' . $var['zip']; 
				if ($var['zipExt'] != '') $response .= '-' . $var['zipExt']; 
				if ($key == 0) $response .=' (UPS Response)</strong>'; 
				$response .=' <br />'; 
			} 
			$response .='</td></tr></table>';
		}
	echo $response; die;
	break;
	
	case "validate":
		if($_POST['action']=='verify') {
			extract($_POST);
			if ($choice_bill != '') {
				$addr = explode('|', $choice_bill);
				$_SESSION['sess_step_2']['txt_street_address'] = $addr[0];
				$_SESSION['sess_step_2']['txt_street_address_2'] = $addr[1];
				$_SESSION['sess_step_2']['txt_city'] = $addr[2];
				$_SESSION['sess_step_2']['txt_state'] = $addr[3];
				$_SESSION['sess_step_2']['txt_zip_code'] = $addr[4];
				if ($addr[5] != '') $_SESSION['sess_step_2']['txt_zip_code'] .= "-" . $addr[5];
				unset($addr);
			}
			if ($choice_ship != '') {
				$addr = explode('|', $choice_ship);
				$_SESSION['sess_step_2']['txt_sstreet_address'] = $addr[0];
				$_SESSION['sess_step_2']['txt_sstreet_address_2'] = $addr[1];
				$_SESSION['sess_step_2']['txt_scity'] = $addr[2];
				$_SESSION['sess_step_2']['txt_sstate'] = $addr[3];
				$_SESSION['sess_step_2']['txt_szip_code'] = $addr[4];
				if ($addr[5] != '') $_SESSION['sess_step_2']['txt_szip_code'] .= "-" . $addr[5];
				unset($addr);
			}
			$this->AjaxJsonOvlyResponse('redirect','Address Verification',$this->MakeUrl('cart/index/','step=3'));
			exit;
		}
		if($_GET['ovly']=='1') {
		
			foreach($_POST as $key=>$value) {
				if(substr($key,0,3)=='txt') {
					$_SESSION['sess_step_2'][$key]=$value;
				}
			}
			//// Billing Information
			$BillCountry = $_SESSION['sess_step_2']['txt_country'];
			$BillAddress = $this->capitalize($_SESSION['sess_step_2']['txt_street_address']);
			$BillAddress2= $this->capitalize($_SESSION['sess_step_2']['txt_street_address_2']);
			$BillCity = $this->capitalize($_SESSION['sess_step_2']['txt_city']);
			$BillState = strtoupper($_SESSION['sess_step_2']['txt_state']);
			$BillZip = strtoupper($_SESSION['sess_step_2']['txt_zip_code']);
			
			//// Shipping Information
			$ShipCountry = $_SESSION['sess_step_2']['txt_scountry'];
			$ShipAddress = $this->capitalize($_SESSION['sess_step_2']['txt_sstreet_address']);
			$ShipAddress2= $this->capitalize($_SESSION['sess_step_2']['txt_sstreet_address_2']);
			$ShipCity = $this->capitalize($_SESSION['sess_step_2']['txt_scity']);
			$ShipState = strtoupper($_SESSION['sess_step_2']['txt_sstate']);
			$ShipZip = strtoupper($_SESSION['sess_step_2']['txt_szip_code']);
			
			if ($BillCountry == '229') { 
				$billing_input = array('address1'=>$BillAddress, 'address2'=>$BillAddress2, 'city'=>$BillCity,'state'=>$BillState, 'zip_code'=>$BillZip);
				$billing_response = $this->VerifyUSAddressByUPS($billing_input);
			}
			
			if ($ShipCountry == '229') { 
				$shipping_input = array('address1'=>$ShipAddress, 'address2'=>$ShipAddress2, 'city'=>$ShipCity,'state'=>$ShipState, 'zip_code'=>$ShipZip);
				$shipping_response = $this->VerifyUSAddressByUPS($shipping_input);
			}
			
			$av1 = 0;
			$av2 = 0;
			
			$response = '<form name="address-form" id="address-form" noscroll=1 action="" method="post" onsubmit="javascript: Rainbow.overlay.load(\''.$this->MakeUrl('cart/validate/').'\',\'post\',this);return false;" >
						<input type="hidden" name="action" value="verify">';
			
			$response .='<div>
							<div class="rr-ups-main">
								<div class="rr-ups-head1">
									<div class="rr-ups-col1"><img src="'.SITE_URL.'images/inner/upslogo.jpg" border="0"></div>
									<div class="rr-ups-col2"><strong>Address Verification by UPS</strong></div>
								</div>
								<div class="clear"></div>';
			
			if(count($billing_response['list'])>0) {
				foreach ($billing_response['list'] as $var) {
					if ($BillAddress == $this->capitalize($var['addr1']) && $BillCity == $this->capitalize($var['city']) && $BillState == $var['state'] && ($BillZip == $var['zip'] || $BillZip == $var['zip'] . "-" . $var['zipExt'])) $av1 = 1;
				} // each returned address
			}
			
			$response .='<div class="rr-ups-heading">Billing Address</div>';
			if ($BillCountry != '229') { 
				$response .='<div class="rr-ups-head2">
								<div class="rr-ups-row1">Foreign Destination</div>
							</div>';
			}
			elseif($av1==1) {
				$response .='<div class="rr-ups-head2">
								<div class="rr-ups-row1">VERIFIED!</div>
							</div>';
			} else {
				$count1 = count($billing_response['list']) + 1;
				$response .='<div class="rr-ups-head2">
								<div class="rr-ups-row1">Choose From the Following '.($count1>1?$count1:'').($count1>1?' Addresses':' Address').': </div>
								<div class="rr-ups-row2"><input type="radio" name="choice_bill" id="choice_bill" value="'.$BillAddress . '|' . $BillAddress2 . '|' . $BillCity . '|' . $BillState . '|' . $BillZip.'" checked="checked" /> '.$BillAddress . ', ';

			 	if ($BillAddress2 != '')  
					$response .= $BillAddress2 . ", "; 
				$response .= $BillCity . ", " . $BillState . " " . $BillZip . ' (your entry)</div>';
				
				if(count($billing_response['list'])>0) {
					foreach ($billing_response['list'] as $key1 => $var) {
						$response .= '<div class="rr-ups-row2">';
						
						if ($key1 == 0) $response .='<strong>'; 
						
						if (strpos($var['addr1'], '-') !== false) { 
							$response .='<a href="javascript:void(0);" onclick="javascript: Rainbow.overlay.close(); return false;">Edit your entry</a> ';
						 } else { 
							$response .='<input type="radio" name="choice_bill" value="'.$this->capitalize($var['addr1']) . '|'; 
							$response .= $BillAddress2 . '|' . $this->capitalize($var['city']) . '|' . $var['state'] . '|' . $var['zip'] . '|' . $var['zipExt'] .'"';
							if ($key1 == 0) { $response .=' checked="checked"'; } 
							$response .=' /> '; 
						} 
						$response .= $this->capitalize($var['addr1']) . ', '; 
						if ($BillAddress2 != '') $response .=$BillAddress2 . ', '; 
						$response .=$this->capitalize($var['city']) . ', ' . $var['state'] . ' ' . $var['zip']; 
						if ($var['zipExt'] != '') $response .= '-' . $var['zipExt']; 
						if ($key == 0) $response .=' (UPS Response)</strong>'; 
						$response .=' </div>'; 
					} 
				}
				$response .='</div>';
			}
			
			if(count($shipping_response['list'])>0) {
				foreach ($shipping_response['list'] as $var) {
					if ($ShipAddress == $this->capitalize($var['addr1']) && $ShipCity == $this->capitalize($var['city']) && $ShipState == $var['state'] && ($ShipZip == $var['zip'] || $ShipZip == $var['zip'] . "-" . $var['zipExt'])) $av2 = 1;
				} // each returned address
			}
			
			$response .='<div class="rr-ups-sep">&nbsp;</div>';
			$response .='<div class="rr-ups-heading">Shipping Address</div>';
			
			if ($ShipCountry != '229') { 
				$response .='<div class="rr-ups-head2">
								<div class="rr-ups-row1">Foreign Destination</div>
							</div>';
			}
			elseif($av2==1) {
				$response .='<div class="rr-ups-head2">
								<div class="rr-ups-row1">VERIFIED!</div>
							</div>';
			} else {
				$count2 = count($shipping_response['list']) + 1;
				$response .='<div class="rr-ups-head2">
								<div class="rr-ups-row1"><label for="choice">Choose From the Following '.($count2>1?$count2:'').($count2>1?' Addresses':' Address').':</label></div>
								<div class="rr-ups-row2"><input type="radio" name="choice_ship" id="choice_ship" value="'.$ShipAddress . '|' . $ShipAddress2 . '|' . $ShipCity . '|' . $ShipState . '|' . $ShipZip.'" checked="checked" /> '.$ShipAddress . ', ';

			 	if ($ShipAddress2 != '')  
					$response .= $ShipAddress2 . ", "; 
				$response .= $ShipCity . ", " . $ShipState . " " . $ShipZip . ' (your entry)</div>'; 
				
				if(count($shipping_response['list'])>0) {
					foreach ($shipping_response['list'] as $key1 => $var) {
						$response .= '<div class="rr-ups-row2">';
						if ($key1 == 0) $response .='<strong>'; 
						if (strpos($var['addr1'], '-') !== false) { 
							$response .='<a href="javascript:void(0);" onclick="javascript: Rainbow.overlay.close(); return false;">Edit your entry</a> ';
						 } else { 
							$response .='<input type="radio" name="choice_ship" value="'.$this->capitalize($var['addr1']) . '|'; 
							$response .= $BillAddress2 . '|' . $this->capitalize($var['city']) . '|' . $var['state'] . '|' . $var['zip'] . '|' . $var['zipExt'] .'"';
							if ($key1 == 0) { $response .=' checked="checked"'; } 
							$response .=' /> '; 
						} 
						$response .= $this->capitalize($var['addr1']) . ', '; 
						if ($BillAddress2 != '') $response .=$BillAddress2 . ', '; 
						$response .=$this->capitalize($var['city']) . ', ' . $var['state'] . ' ' . $var['zip']; 
						if ($var['zipExt'] != '') $response .= '-' . $var['zipExt']; 
						if ($key == 0) $response .=' (UPS Response)</strong>'; 
						$response .=' </div>'; 
					}
				} 
				$response .='</div>';
			}
			$response .='</div>';
			$response .='<div style="text-align:center;">
							<button id="btn-continue1" class="button3-small">Continue</button>&nbsp;<button id="btn-continue2" class="button3-small" onclick="javascript: Rainbow.overlay.close(); return false;">Cancel</button>
						</div>';
			$response .='</div></form>';		

			$this->AjaxJsonOvlyResponse('content','Address Verification',$response);
			exit;
		}
	break;
	case "add":
		
		if($this->mArgs[2]!="") {
			$flag = $cart->AddToCart($this->mArgs[2], $this->mArgs[3]);
			$product_name = ucfirst(stripslashes($this->GetProductTitle($this->mArgs[2])));
			
			if($flag=='success') 
				$msg = 'Product "<strong>'.$product_name.'</strong>" successfully added to Cart.';
			elseif($flag=='dberror') 
				$msg = 'Product "<strong>'.$product_name.'</strong>" can not be added to Cart. Quantity not Available.';
			else
				$msg = 'Product "<strong>'.$product_name.'</strong>" can not be added to Cart.';
			$response='<div>
					   		<div class="overlay-msg">'.$msg.'</div>
							<div class="overlay-confirm-msg">Click "<strong>Continue</strong>" to Continue Shopping or Click "<strong>Shopping Cart</strong>" to View Cart.</div>
							<div class="overlay-buttons" style="width:270px;padding-top:15px;">
								<div class="overlay-continue-btn"><button class="button3-small" onclick="javascript:Rainbow.overlay.close();">Continue</button></div>
								<div class="overlay-continue-btn"><button class="button3-medium" onclick="javascript:Rainbow.SC.redirectToCart();">Shopping Cart</button></div>
							</div>
					   </div>';
			$this->AjaxJsonOvlyResponse('content','Add to Cart',$response);
		}
		//$this->RedirectUrl('cart/index/');
		break;
	
	case "update": //print_r($_POST); die;
		if($_POST['act']==$this->mPageName) {
			$cart->UpdateCart($_POST);
		}
		$this->RedirectUrl('cart/index/','act=update');
		break;
	
	case "empty":
		if($_POST['act']==$this->mPageName) {
			$cart->EmptyCart();
		}
		$this->RedirectUrl('cart/index/','act=empty');
		break;
	case "delete":
		if($_POST['act']==$this->mPageName) {
			$cart->DeleteFromCart($_POST);
		}
		
		/*if($_GET['id']!="") {
			$cart->DeleteFromCart($_GET['id']);
		}*/
		$this->RedirectUrl('cart/index/','act=delete');
		break;
	case "copyfromwishlist":			
		if($_POST['action-submit']=="my-wishlist") {
			$post_arr = $_POST;
			$post_arr['chkprod'] = $_POST['wishlistChk'];
			$post_arr['txt-my-group'] = $_POST['txt-my-group'];		
			$cart->DeleteFromCartUsingWishlist($post_arr);		 
			foreach($post_arr['chkprod'] as $result) {
				$getQuantity = $this->Select(TABLE_WISHLIST," product_id='".$result."' AND group_id='".$post_arr['txt-my-group']."' AND is_deleted=0 AND is_active=1","quantity");				
				//$flag = $cart->AddToCart($result, 'product',$post_arr['qty_'.$result]);
				$flag = $cart->AddToCart($result, 'product',$getQuantity[0]['quantity']);				 
			}			
			$this->RedirectUrl('my-wishlist/index/','action=copytocart&group_id='.$post_arr['txt-my-group']);
			exit;
		}
		break;
	case "copytowishlist":
		//ob_start();	
		//echo "<pre>";
		//print_r($_POST);
		$cart_error = "";
		$cart_success = "";
		$prefix = "";
		$prefix_error = "";	
		if($_POST['action-submit']=="copy-to-wishlist") {
			//ob_start();		
			/*echo "<br>";
			print_r($cartResult);*/
			if($_POST['txt-wishlist-group']=="-1") $_POST['txt-wishlist-group'] = $this->AddWishListGroup($_SESSION['sess_user_id'],$_POST['txt-wishlist-group-new']);					
			$post_arr = $_POST;
			//echo "<pre>";
			//print_r($post_arr);
			$cartIdArr = explode(",",$post_arr['cart-id']);
			if(count($cartIdArr) > 0) {
				$i = 0;
				foreach($cartIdArr as $result){
					$cartResult = $this->Select(TABLE_CART_SESSION,"cart_id=".$result,"product_id,quantity,type");					
					if($cartResult[0]['type'] == "book"){
						if($cart_error != "") $prefix_error = ", ";	
						$cart_error .= $prefix_error.ucfirst($this->GetProductTitle($cartResult[0]['product_id']));	
					}else {
						$prdexistResult = $this->Select(TABLE_WISHLIST,"product_id=".$cartResult[0]['product_id']." and group_id=".$post_arr['txt-wishlist-group'],"wishlist_id");						
						if(count($prdexistResult) > 0) {
							$this->Delete(TABLE_WISHLIST,"wishlist_id=".$prdexistResult[0]['wishlist_id']);	
						}
						$data_arr=array('group_id'=>"'".$post_arr['txt-wishlist-group']."'",'product_id'=>"'".$cartResult[0]['product_id']."'",'quantity'=>"'".$cartResult[0]['quantity']."'",'is_active'=>'1','add_date'=>'NOW()','updated_date'=>'NOW()');
						$this->Insert(TABLE_WISHLIST,$data_arr);
						if($cart_success != "") $prefix = ", ";	
						$cart_success .= $prefix.ucfirst($this->GetProductTitle($cartResult[0]['product_id']));
						$i++;	
					}
					
				}
			}
			if($cart_error != "")
			$error = '<div class="login-error">You can not add "<strong>'.$cart_error.'</strong>" into selected wishlist group.<br/>Bargain books can not be added to wishlist groups.</div>';			
			$msg = 'Product "<strong>'.$cart_success.'</strong>" successfully added to your wishlist.';				
			if($i > 0)
				$msg = $error."<br/>".$msg;
			else 
				$msg = $error;
			$response='<div>
								<div class="overlay-msg">'.$msg.'</div>
								<div class="overlay-confirm-msg">Click "<strong>Continue</strong>" to Continue Shopping or Click "<strong>My Wishlist</strong>" to your Wishlist.</div>
								<div class="overlay-buttons" style="width:300px;">
									<div class="overlay-continue-btn"><button class="button3-medium" onclick="javascript:Rainbow.overlay.close();">Continue</button></div>
									<div class="overlay-continue-btn"><button class="button3-medium" onclick="javascript:window.location=\''.$this->MakeUrl('my-wishlist/').'\';">My Wishlist</button></div>
								</div>
							</div>
						';	
			$this->AjaxJsonOvlyResponse('content','Move to Group',$response);
			exit;	
		}	
		$group_dd = $this->GetMyWishlistGroupDropDown($_SESSION['sess_user_id'],0);
		$commaProducts = implode(",",$_POST['chkprod']);
		$response='
				<form name="frm-add-to-wishlist" id="frm-add-to-wishlist" onsubmit="return false;">
					<input type="hidden" name="action-submit" value="copy-to-wishlist"/>
					<input type="hidden" name="cart-id" value="'.$commaProducts.'"/>					
					<div>
						'.$error.'
						<div class="overlay-msg" style="padding-bottom:0px;">'.$msg.'</div>
						<div class="overlay-confirm-msg" style="padding-top:0px;margin-bottom:20px;" >
							<strong>Group:</strong> 
							<select name="txt-wishlist-group" id="txt-wishlist-group" class="searchBy" onchange="if(this.value==\'-1\') {$(\'txt-wishlist-group-new\').show().focus();} else {$(\'txt-wishlist-group-new\').hide();}"  >
								'.$group_dd.'
								<option value="-1">Add New</option>
							</select>
							<input type"text" class="input1" name="txt-wishlist-group-new" id="txt-wishlist-group-new" value="'.$_POST['txt-wishlist-group-new'].'" style="display:none;" />
						</div>
						<div class="overlay-buttons" style="width:300px;">
							<div>
								<div class="overlay-continue-btn"><button class="button3-medium" onclick="javascript:if($(\'txt-wishlist-group\').value!=\'-1\' || $(\'txt-wishlist-group-new\').value!=\'\') { Rainbow.overlay.load(\''.$this->MakeUrl('cart/copytowishlist/').'\',\'post\',\'frm-add-to-wishlist\');return false;} else {alert(\'Please enter new group name.\');return false;}">Add to Wishlist</button></div>
								<div class="overlay-continue-btn"><button class="button3-medium" onclick="javascript:Rainbow.overlay.close();return false;">Cancel</button></div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
				   </div>
				</form>';
		$this->AjaxJsonOvlyResponse('content','Move to Group',$response);
		exit;
		break;
	case "order":
		if($_POST['action']==$this->mPageName)
		{
			if($this->ValidateSession('user')) {
				$customerId = $_SESSION['sess_user_id'];
			} else {
				$user_id = $this->ValidateEmail($_POST['txt_e_mail']);
				if($user_id != '') {
					$customerId = $user_id;
				} else {
					$customerId = $this->RegisterNewUser($_POST);
				}
			}
			
			
			
			
			
			/// Code for updating last Billing and Shipping information in table customer info.
			$ins_arr = $_POST;
			$arr_type=array('txt_e_mail'=>'STRING', 'txt_company_name'=>'STRING','txt_first_name'=>'STRING','txt_last_name'=>'STRING','txt_street_address'=>'STRING','txt_street_address_2'=>'STRING','txt_country'=>'STRING','txt_state'=>'STRING','txt_city'=>'STRING','txt_zip_code'=>'STRING','txt_phone'=>'STRING','txt_fax'=>'STRING','txt_scompany_name'=>'STRING','txt_sfirst_name'=>'STRING','txt_slast_name'=>'STRING','txt_sstreet_address'=>'STRING','txt_sstreet_address_2'=>'STRING','txt_scountry'=>'STRING','txt_sstate'=>'STRING','txt_scity'=>'STRING','txt_szip_code'=>'STRING','updated_date'=>'NOW()','customer_id'=>"'".$customerId."'");
			
			$ins_arr=$this->MySqlFormat($ins_arr,$arr_type);
			if($_POST['txt_country']!="229")
				$ins_arr['txt_state']=	"'".addslashes(trim($_POST['txt_state_other']))."'";
				
			
			$keyvalue_arr=array('txt_e_mail'=>'e_mail','txt_company_name'=>'company_name','txt_first_name'=>'first_name','txt_last_name'=>'last_name','txt_street_address'=>'street_address','txt_street_address_2'=>'street_address_2','txt_country'=>'country','txt_state'=>'state','txt_city'=>'city','txt_zip_code'=>'zip_code','txt_phone'=>'phone','txt_fax'=>'fax','txt_scompany_name'=>'scompany_name','txt_sfirst_name'=>'sfirst_name','txt_slast_name'=>'slast_name','txt_sstreet_address'=>'sstreet_address','txt_sstreet_address_2'=>'sstreet_address_2','txt_scountry'=>'scountry','txt_sstate'=>'sstate','txt_scity'=>'scity','txt_szip_code'=>'szip_code');
			$getRes = $this->Select(TABLE_CUSTOMER_INFO,"customer_id='".$customerId."'","COUNT(*) as cnt");
			if($getRes[0]['cnt']>0) {
				$this->Update(TABLE_CUSTOMER_INFO,$ins_arr,"customer_id='".$customerId."'","",$keyvalue_arr);
			} else {
				$this->Insert(TABLE_CUSTOMER_INFO,$ins_arr,$keyvalue_arr);
			}
			
			$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$custCartId."'","*","cart_id");
			if(count($session_data)>0) {
				foreach($session_data as $data) {
					$product_info=$cart->GetProductDetails($data['product_id']);
					if($product_info['is_active']=="0" || $product_info['is_deleted']=="1") {
						$this->RedirectUrl('cart/index/');
						exit;
					}
				}
			}
			////// Code ends here			 		 
			if($_POST['txt_country'] == 229) {
				$bill_state = $_POST['txt_state'];
			}else if($_POST['txt_country'] != 229) {
				$bill_state = $_POST['txt_state_other'];
			}
			if($_POST['txt_scountry'] == 229) {
				$ship_state = $_POST['txt_sstate'];
			}else if($_POST['txt_scountry'] != 229) {
				$ship_state = $_POST['txt_sstate_other'];
			}		 
			$post_arr['bill_company_name']=$this->MysqlString(stripslashes($_POST['txt_company_name']));
			$post_arr['bill_first_name']=$this->MysqlString(stripslashes($_POST['txt_first_name']));
			$post_arr['bill_last_name']=$this->MysqlString(stripslashes($_POST['txt_last_name']));
			$post_arr['bill_address']=$this->MysqlString(stripslashes($_POST['txt_street_address']));
			$post_arr['bill_address_2']=$this->MysqlString(stripslashes($_POST['txt_street_address_2']));
			$post_arr['bill_city']=$this->MysqlString(stripslashes($_POST['txt_city']));
			$post_arr['bill_state']=$this->MysqlString($bill_state);
			$post_arr['bill_zipcode']=$this->MysqlString(stripslashes($_POST['txt_zip_code']));
			$post_arr['bill_country']=$this->MysqlString($_POST['txt_country']);
			$post_arr['bill_phone']=$this->MysqlString(stripslashes($_POST['txt_phone']));
			$post_arr['bill_fax']=$this->MysqlString(stripslashes($_POST['txt_fax']));
			
			$post_arr['ship_company_name']=$this->MysqlString(stripslashes($_POST['txt_scompany_name']));
			$post_arr['ship_first_name']=$this->MysqlString(stripslashes($_POST['txt_sfirst_name']));
			$post_arr['ship_last_name']=$this->MysqlString(stripslashes($_POST['txt_slast_name']));
			$post_arr['ship_address']=$this->MysqlString(stripslashes($_POST['txt_sstreet_address']));
			$post_arr['ship_address_2']=$this->MysqlString(stripslashes($_POST['txt_sstreet_address_2']));
			$post_arr['ship_city']=$this->MysqlString(stripslashes($_POST['txt_scity']));
			$post_arr['ship_state']=$this->MysqlString($ship_state);
			$post_arr['ship_zipcode']=$this->MysqlString(stripslashes($_POST['txt_szip_code']));
			$post_arr['ship_country']=$this->MysqlString($_POST['txt_scountry']);
			$post_arr['additional_comments']=$this->MysqlString($_POST['txt_additional_comments']);
			$post_arr['e_mail']=$this->MysqlString($_POST['txt_e_mail']);
			$post_arr['customer_id']="'".$customerId."'";
			//echo $post_arr['bill_name']."<br>"; 
			//echo $post_arr['ship_name']; exit;
			//$post_arr['subtotal']="'0.00'";
			//$post_arr['customer_id']="'".$_SESSION['sess_user_id']."'";
			$post_arr['product_total']="'0.00'";
			$post_arr['total_tax']="'0.00'";
			$post_arr['order_type'] = "'".$_POST['order_type']."'";
			$post_arr['total_ship']=$this->MysqlFloat($_POST['shipping_amount']);
			$post_arr['ship_type']=$this->MysqlString($_POST['txt_ship_type']);
			$post_arr['order_total']="'0.00'";
			$post_arr['is_expedite_order']=$this->MysqlString($_POST['txt_expedite_order']);
			$post_arr['expedite_charge']=$this->MysqlFloat($_POST['expedite_charge']);
			$post_arr['payment_type']==$_POST['txt_payment_type'];
			$post_arr['status']=$_POST['txt_payment_type']=='paypal'?"'new_order'":"'new_order'";
			$post_arr['order_date']="NOW()";
			$post_arr['payment_gateway']="'PayPal'";
			
		 

			if($_POST['txt_payment_type']=='paypal') {
				
				/*if($payment_status['status']==1){
					$cardno = $_POST['txtccnumber'];
					$cardno_len = strlen($cardno);
					for($i=0; $i<$cardno_len; $i++) {
						if($cardno[$i]!=" " && $cardno[$i]!="-") {
							$newcardno+="X";
						} else  {
							$newcardno+=$cardno[$i];
						}
					}
				}*/
				$post_arr['card_type']="''";
				$post_arr['card_number']="''";
				$post_arr['card_holder_name']="''";
				$post_arr['card_expmonth']="''";
				$post_arr['card_expyear']="''";
				$post_arr['card_cvv']="''"; 
				$post_arr['is_paid']="0";
				$post_arr['is_processing']="1";
			} else {
				$post_arr['card_type']=$this->MysqlString($_POST['txtcctype']);
				$post_arr['card_holder_name']=$this->MysqlString($_POST['txtccname']);
				$post_arr['card_number']=$this->MysqlString($_POST['txtccnumber']);
				$post_arr['card_expmonth']=$this->MysqlInteger(str_pad($_POST['txt_expmonth'],2,"0",STR_PAD_LEFT));
				$post_arr['card_expyear']=$this->MysqlInteger($_POST['txt_expyear']);
				$post_arr['card_cvv']=$this->MysqlString($_POST['txtccv']);
				$post_arr['is_paid']="0";
				$post_arr['is_processing']="0";
			}
			$post_arr['payment_type']=$this->MysqlString($_POST['txt_payment_type']);
			$post_arr['gateway_transaction_id']=$this->MysqlString("");
			
			$order_id=$this->Insert(TABLE_CART_ORDER,$post_arr);
			/*if($_SESSION['sess_user_id'] != "") {
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$_SESSION['sess_user_id']."' and  date_add(add_date,interval 24 hour) > NOW()","*","cart_id");
			}else { */
			$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$custCartId."'","*","cart_id");
			//}
			$sub_total = 0;
			$cart_total = 0;
			$bb_status = '';
			if(count($session_data)>0) {
				foreach($session_data as $data) {
					$product_info=$cart->GetProductDetails($data['product_id']);
					if($product_info['is_backorder']==1 || $product_info['out_of_stock']==0 || $data['type']=='book') {
						$post_arr=array();
						
						if($data['type']=='product') $price = $product_info['rainbow_price'];
						else $price = $product_info['bargain_book_price'];
						
						$post_arr['cart_order_id']=$order_id;
						$post_arr['product_id']=$this->MysqlString($data['product_id']);
						$post_arr['product_number']=$this->MysqlString($product_info['product_number']);
						$post_arr['product_name']=$this->MysqlString($product_info['name']);
						$post_arr['quantity']=$data['quantity'];
						$post_arr['price']=$this->MysqlString(sprintf("%01.2f",$price));
						$post_arr['type']=$this->MysqlString($data['type']);
						$invoice_id=$this->Insert(TABLE_CART_INVOICE,$post_arr);
						if($data['type']=='book') $bb_status='new_order';
						$sub_total+=($data['quantity']*$price);
						$cart_total+=($data['quantity']*$price);
					}
				}
			} 
			if($_POST['txt_catalog_request']=='yes') {
				$post_arr=array();
				$post_arr['cart_order_id']=$order_id;
				$post_arr['product_id']=$this->MysqlString('0');
				$post_arr['product_number']=$this->MysqlString('000001');
				$post_arr['product_name']=$this->MysqlString('<strong>Retail Catalog</strong>');
				$post_arr['quantity']=1;
				$post_arr['price']=$this->MysqlString(sprintf("%01.2f",'0.00'));
				$post_arr['type']=$this->MysqlString('product');
				$invoice_id=$this->Insert(TABLE_CART_INVOICE,$post_arr);
			}
			$post_arr=array();
			//$post_arr['subtotal']=sprintf("%01.2f",$sub_total);
			//$post_arr['weight_total']=sprintf("%01.2f",$weight_total);
			//$post_arr['product_total']=sprintf("%01.2f",$sub_total);
			//$post_arr['order_total']=sprintf("%01.2f",$cart_total);
			if($_POST['txt_expedite_order']==1)
				$expedite_charge = $_POST['expedite_charge'];
			else 
				$expedite_charge = 0.00;
			$sales_tax = $_POST['sales_tax'];
			//$post_arr['order_type'] = $_POST['order_type'];			
			$post_arr['sale_tax']=sprintf("%01.2f",$_POST['tax_rate']);
			$post_arr['subtotal']=sprintf("%01.2f",$sub_total);
			$post_arr['product_total']=sprintf("%01.2f",$_POST['sub_total']);
			$post_arr['order_total']=sprintf("%01.2f",($sub_total+$_POST['shipping_amount']+$sales_tax+$expedite_charge));
			$post_arr['updated_date']='NOW()';
			$post_arr['bb_status']=$this->MysqlString($bb_status);
			
			$this->Update(TABLE_CART_ORDER,$post_arr,"cart_order_id='".$order_id."'");
			//$this->Delete(TABLE_CART_SESSION,"session_id='".session_id()."'");

			if($_POST['txt_payment_type']=='paypal') {
				$_SESSION["ss_last_orderno_refer"] = "sc";
				include('html/cart_paypal.html');
			} else {
				$this->Delete(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."'");
				$this->OrderEmail($order_id,'new','user');
				$this->OrderEmail($order_id,'new','admin');
				$this->RedirectUrl('cart/index/','step=4&oid='.$order_id);
			}
		}
		 break;
	default:
		/*if($_SESSION['sess_user_id'] != "") {
			$shopmore_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$_SESSION['sess_user_id']."' and  date_add(add_date,interval 24 hour) > NOW()","*","cart_id");
		}else { */
			$shopmore_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$custCartId."'",'product_id','cart_id desc',1);
		//}
		/*echo "cart session id==============".$this->cartSessionId;
		echo "<br />";
		echo "customer Id=============".$custCartId;*/
		if(count($shopmore_data)>0)
		{
			$shopmore=SITE_URL;
		}
		
		if($_GET['step']==4) {
			/*if(!$this->ValidateSession('user')) {
				$this->RedirectUrl('cart/');
			}*/
			$title = "Payment Successful !!";
			if($_GET['oid']) {
				$err = "You have reached this page in error with incomplete order information. Perhaps you have clicked on your browser&rsquo;s back button after completing your order. If you were attempting to alter a completed order, please contact us with the details using the link below. Otherwise, please begin the checkout process again once you have merchandise in your cart.";
				$err_title = "ERROR - Incomplete Order";
				$getRes = $this->Select(TABLE_CART_ORDER,"cart_order_id='".$_GET['oid']."' AND is_deleted=0","payment_type, is_paid, is_expedite_order");
				if(count($getRes)>0) {
					$PmtType = $getRes[0]['payment_type'];
					$isExpedite = $getRes[0]['is_expedite_order'];
				} else {
					$emssg = $err;
					$title = $err_title;
				}
			} else {
				$emssg = $err;
				$title = $err_title;
			}
			include('html/cart_4.html');
		} elseif($_GET['step']==3) { 
			if($_GET['act']=='failed') {
				$msg = "Your transaction has been declined. Please click on <strong>\"Pay Now\"</strong> button to make your payment.";
			 }
			/*if(!$this->ValidateSession('user')) {
				$this->RedirectUrl('cart/');
			} else { $isLoggedin = 1;  }*/
			
			/*foreach($_POST as $key=>$value) {
				if(substr($key,0,3)=='txt') {
					$_SESSION['sess_step_2'][$key]=$value;
				}
			}*/
			/*if($_SESSION['sess_user_id'] != "") {
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$_SESSION['sess_user_id']."' and  date_add(add_date,interval 24 hour) > NOW()","*","cart_id");
			}else { */
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$custCartId."'","*","cart_id");
			//}
			$i=0;
			$cart_empty=true;
			if(count($session_data)>0) {
				$nmerch = 0; $dbmerch = 0;
				foreach($session_data as $data) {
					$product_id = $data['product_id'];
					$product_info=$cart->GetProductDetails($data['product_id']);
					if($product_info['is_active']=="0" || $product_info['is_deleted']=="1") {
						$this->RedirectUrl('cart/index/','act='.$_GET['act']);
						exit;
					}
					if($product_info['is_backorder']==1 || $product_info['out_of_stock']==0 || $data['type']=='book') {
						if($product_info['product_image']!= "" && file_exists(DIR_PRODUCT_SMALL.$product_info['product_image'])) {
							$prod_image = SITE_URL.DIR_PRODUCT_SMALL.$product_info['product_image'];
						}else {
							$prod_image = SITE_URL.DIR_PRODUCT_SMALL."default.jpg";
						}
						
						if($data['type']=='product') $price = $product_info['rainbow_price'];
						else $price = $product_info['bargain_book_price'];
	
						if($data['type']=='product') $nmerch +=($data['quantity']*$price);
						else $dbmerch+=($data['quantity']*$price);
						
						$detail_link = $data['type']=='book' ? $this->MakeUrl('bargain-books-products/details/',"product_id=".$data['product_id']."&is_bargain=yes") : $this->MakeUrl('product-browse/details/',"product_id=".$data['product_id']);
						//echo "Types===".$data['type'];
						$cart_data[$data['type']] .= $this->AddFormField('hidden',"productid",$product_info['product_id'],"",0).'
													'.$this->AddFormField('hidden',"product_id[]",$data['product_id'],"",0).'
													<div class="cart-row">	
														<div class="cart-td1">&nbsp;</div>
														<div class="cart-td2">
															<div class="code">'.$product_info['product_number'].'</div>
															<div class="code-img"><a href="'.$detail_link.'"><img src="'.$prod_image.'" title="'.$product_info['product_name'].'" alt="'.$product_info['product_name'].'" border="0" /></a></div>
														</div>
														<div class="cart-td3">
															<div class="des-heading"><a href="'.$detail_link.'">'.$product_info['product_name'].'</a></div>
															<div>'.$product_info['desc'].'</div>
														</div>
														<div class="cart-td4">
															<div class="qty"><span class="checkout-qty">'.$data['quantity'].'</span></div>
														</div>
														<div class="cart-td5">$'.sprintf("%01.2f",$price).'</div>
														<div class="cart-td6">$'.sprintf("%01.2f",($data['quantity']*$price)).'</div>
													</div>';
						
						
						$sub_total += ($data['quantity']*$price);
						//$shipping_amt = 20.50;
						$cart_empty=false;
					} 
					
					if($_SESSION['sess_step_2']['txt_scountry']=='229' && $_SESSION['sess_step_2']['txt_sstate']=='IL') {
						$tax_rate = $this->OrderSettings['sale_tax'];
						$sales_tax = round($sub_total * $tax_rate) / 100;
						$cart_total = $sub_total+$sales_tax;
					} else {
						$cart_total = $sub_total;
					}
						
				}
			}
			//die;
			///echo $cart_data['product']; die;
			//echo "here".$cart_empty."qqq"; die;
			if($cart_empty==true) { 
				$this->RedirectUrl('cart/index/');
				exit;
			}
			
			/********* Code for needed conditions ********/
			$cmerch = $nmerch + $dbmerch;
			$combined = 0; $dbonly = 0; $newonly = 0;
			
			if ($cart_data['book'] != '') {
				if ($cart_data['product'] == '') { $dbonly = 1; $order_type="B";}
				else { $combined = 1; $order_type="C"; }
				} // damaged books found
			elseif ($cart_data['product'] != '') { $newonly = 1; $order_type="N";}
			
			
			
			/*if ($dbmerch > 0) {
				if ($nmerch == 0) $dbonly = 1;
				else $combined = 1;
				} // damaged books found
			elseif ($nmerch > 0) $newonly = 1;*/
			/**********************************************/
			 
			$merch = $nmerch;
			if ($combined == 1) $merch += $dbmerch;
			if ($dbonly == 1) $merch = $dbmerch;
			
			$post_arr = $_SESSION['sess_step_2'];
			$post_arr['merch'] = $merch;
			$post_arr['nmerch'] = $nmerch;
			
			$carrierOptions = $this->GetCarrierOptions($post_arr);
			//echo "Total===".$nmerch; die;
			include('html/cart_3.html');
		} elseif($_GET['step']=='validate') {
			
			
			
		} elseif($_GET['step']==2) {
			if(!$this->ValidateSession('user')) {
				//$this->RedirectUrl('login/index/',"redirect=".$this->MakeUrl('cart/index/','step=2'));
				$form_action = $this->MakeUrl('cart/index/','step=2');
			} else {
				$form_action = $this->MakeUrl('cart/index/','step=3');
			}
			$form_action = $this->MakeUrl('cart/index/','step=2');
			//
			if(empty($_SESSION['sess_step_2']) && $this->ValidateSession('user')) { 
				$getRes = $this->Select(TABLE_CUSTOMER_INFO,"customer_id='".$_SESSION['sess_user_id']."'","company_name, first_name, last_name, street_address, street_address_2, city, state, zip_code, country, e_mail, phone, fax, scompany_name, sfirst_name, slast_name, sstreet_address, sstreet_address_2, scity, sstate, szip_code, scountry");
				if(count($getRes)>0) {
					foreach($getRes as $row) { 
						$result = $row;
					}	
				} else {
					$getRes = $this->Select(TABLE_CUSTOMER,"customer_id='".$_SESSION['sess_user_id']."'","company_name, first_name, last_name, street_address, street_address_2, city, state, zip_code, country, e_mail, phone, fax");
					foreach($getRes as $row) { 
						$result = $row;
					}
				}
				foreach($result as $key => $value) {
					if($key=='country') {
						$_SESSION['sess_step_2']['txt_'.$key] = $value;
						if($value=='229')	$_SESSION['sess_step_2']['txt_state'] = $result['state'];
						else 	$_SESSION['sess_step_2']['txt_state_other'] = $result['state'];
					} elseif($key=='scountry') {
						$_SESSION['sess_step_2']['txt_'.$key] = $value;
						if($value=='229')	$_SESSION['sess_step_2']['txt_sstate'] = $result['sstate'];
						else 	$_SESSION['sess_step_2']['txt_sstate_other'] = $result['sstate'];
					} else { 
						$_SESSION['sess_step_2']['txt_'.$key] = $value;
					}
				}
				
			}
			$valildateAddress = 0;
			/*if(!empty($_POST)) {
				
				foreach($_POST as $key=>$value) {
					if(substr($key,0,3)=='txt') {
						$_SESSION['sess_step_2'][$key]=$value;
					}
				}
				$openLoginBox = false;
				$registerUser = false;
				if($this->ValidateEmail($_POST['txt_e_mail']))
				{
					$openLoginBox = true;
				} else {
					//// Registration Code Here
					$registerUser = true;
				}
				if($_POST['action']=='cart') {
					$valildateAddress = 1;
				}
			}*/
			/*if($_SESSION['sess_user_id'] != "") {
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$_SESSION['sess_user_id']."' and  date_add(add_date,interval 24 hour) > NOW()","*","cart_id");
			}else { */
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$custCartId."'","*","cart_id");
			//}
			$i=0;
			$cart_empty = true;
			if(count($session_data)>0) {
				foreach($session_data as $data) {
					$product_id = $data['product_id'];
					$product_info=$cart->GetProductDetails($data['product_id']);
					if($product_info['is_active']=="0" || $product_info['is_deleted']=="1") {
						$this->RedirectUrl('cart/index/');
						exit;
					}
					if($product_info['is_backorder']==1 || $product_info['out_of_stock']==0 || $data['type']=='book') {
						if($product_info['product_image']!= "" && file_exists(DIR_PRODUCT_SMALL.$product_info['product_image'])) {
							$prod_image = SITE_URL.DIR_PRODUCT_SMALL.$product_info['product_image'];
						}else {
							$prod_image = SITE_URL.DIR_PRODUCT_SMALL."default.jpg";
						}
						if($data['type']=='product') $price = $product_info['rainbow_price'];
						else $price = $product_info['bargain_book_price'];
						
						$detail_link = $data['type']=='book' ? $this->MakeUrl('bargain-books-products/details/',"product_id=".$data['product_id']."&is_bargain=yes") : $this->MakeUrl('product-browse/details/',"product_id=".$data['product_id']);
						
						$cart_data[$data['type']] .= $this->AddFormField('hidden',"productid",$product_info['product_id'],"",0).'
													'.$this->AddFormField('hidden',"product_id[]",$data['product_id'],"",0).'
													<div class="cart-row">	
														<div class="cart-td1">&nbsp;</div>
														<div class="cart-td2">
															<div class="code">'.$product_info['product_number'].'</div>
															<div class="code-img"><a href="'.$detail_link.'"><img src="'.$prod_image.'" alt="'.$product_info['product_name'].'" title="'.$product_info['product_name'].'" border="0" /></a></div>
														</div>
														<div class="cart-td3">
															<div class="des-heading"><a href="'.$detail_link.'">'.$product_info['product_name'].'</a></div>
															<div>'.$product_info['desc'].'</div>
														</div>
														<div class="cart-td4">
															<div class="qty"><span class="checkout-qty">'.$data['quantity'].'</span></div>
														</div>
														<div class="cart-td5">$'.sprintf("%01.2f",$price).'</div>
														<div class="cart-td6">$'.sprintf("%01.2f",($data['quantity']*$price)).'</div>
													</div>';
						
   					 	$cart_empty=false;
						$sub_total += ($data['quantity']*$price);
						//$shipping_amt = 20.50;
						$tax_rate = 10;
						//$sales_tax = ($sub_total*($tax_rate/100));
						$cart_total = $sub_total;//+$sales_tax;
					} 
				}
			}
			
			if($cart_empty==true) { 
				$this->RedirectUrl('cart/index/');
				exit;
			}
			$country_dropdown = $this->GetCountryDropDown($_SESSION['sess_step_2']['txt_country']);
			$scountry_dropdown = $this->GetCountryDropDown($_SESSION['sess_step_2']['txt_scountry']);
			
			$state_dropdown = $this->GetUSStateDropDownReg($_SESSION['sess_step_2']['txt_state']);
			$sstate_dropdown = $this->GetUSStateDropDownReg($_SESSION['sess_step_2']['txt_sstate']);
			
			include('html/cart_2.html');
		} else {
			/*if($_SESSION['sess_user_id'] != "") {
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$_SESSION['sess_user_id']."' and  date_add(add_date,interval 24 hour) > NOW()","*","cart_id");
			}else {*/ 
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$custCartId."'","*","cart_id");
			//}
			//echo "I am here==========".$this->cartSessionId;
			$i=0;
			$copyToWishlist = array();
			$cart_empty = true;
			$prod_error = false;
			$prod_error_ids = array();
			if(count($session_data)>0) {
				foreach($session_data as $data) { 
					$product_id = $data['product_id'];
					$product_info=$cart->GetProductDetails($data['product_id']);
					if($product_info['is_active']=="0" || $product_info['is_deleted']=="1") {
						$prod_error=true;
						$prod_error_ids[] = $product_info['product_number'];
						$this->Delete(TABLE_CART_SESSION,"session_id='".$this->cartSessionId."' AND customer_id='".$custCartId."' and product_id = '".$data['product_id']."'");
						continue;
					}
					 //// Condition for checking out of stock
					if($product_info['is_backorder']==1 || $product_info['out_of_stock']==0 || $data['type']=='book') {
						if($product_info['product_image']!= "" && file_exists(DIR_PRODUCT_SMALL.$product_info['product_image'])) {
							$prod_image = SITE_URL.DIR_PRODUCT_SMALL.$product_info['product_image'];
						}else {
							$prod_image = SITE_URL.DIR_PRODUCT_SMALL."default.jpg";
						}
						
						$is_readonly = $data['type']=='book' ? ' readonly="" ' : '';
						$detail_link = $data['type']=='book' ? $this->MakeUrl('bargain-books-products/details/',"product_id=".$data['product_id']."&is_bargain=yes") : $this->MakeUrl('product-browse/details/',"product_id=".$data['product_id']);
						$quantity = $data['type']=='book' ? $this->AddFormField('text',"bookqty_".$data['product_id']."",$data['quantity'],"qty-input",0,'Quantity','',' readonly="" maxlength="3"') : $this->AddFormField('text',"qty_".$data['product_id']."",$data['quantity'],"qty-input",0,'Quantity','',' maxlength="3" onChange="javascript: chk_int(this);"').$this->AddFormField('hidden',"product_id[]",$data['product_id'],"",0);
						
						if($data['type']=='product') $price = $product_info['rainbow_price'];
						else $price = $product_info['bargain_book_price'];
						
						$cart_data[$data['type']] .= $this->AddFormField('hidden',"productid",$product_info['product_id'],"",0).'
													'.$this->AddFormField('hidden',"cart_id[]",$data['cart_id'],"",0).'
													'.$this->AddFormField('hidden',"type_".$data['cart_id'],$data['type'],"",0).'
													'.$this->AddFormField('hidden',"pid_".$data['cart_id'],$data['product_id'],"",0).'
													<div class="cart-row">
														<div class="cart-td1"><input type="checkbox" name="chkprod[]" id="chkprod" value="'.$data['cart_id'].'" class="checkbox"></div>
														<div class="cart-td2">
															<div class="code">'.$product_info['product_number'].'</div>
															<div class="code-img"><a href="'.$detail_link.'"><img src="'.$prod_image.'" alt="'.$product_info['product_name'].'" title="'.$product_info['product_name'].'" border="0" /></a></div>
														</div>
														<div class="cart-td3">
															<div class="des-heading"><a href="'.$detail_link.'">'.$product_info['product_name'].'</a></div>
															<div>'.$product_info['desc'].'</div>
														</div>
														<div class="cart-td4">
															<div class="qty">'.$quantity.'</div>
														</div>
														<div class="cart-td5">$'.sprintf("%01.2f",$price).'</div>
														<div class="cart-td6">$'.sprintf("%01.2f",($data['quantity']*$price)).'</div>
													</div>';
						
						
						$sub_total += ($data['quantity']*$price);
						//$shipping_amt = 20.50;
						$tax_rate = 10;
						//$sales_tax = ($sub_total*($tax_rate/100));
						$cart_total = $sub_total;//+$sales_tax;
						$cart_empty=false;
						$onClick = " javascript: window.location='".$this->MakeUrl('cart/index/','step=2')."';";
					} //// Condition for checking out of stock ends here.
					
					/*if($this->ValidateSession('user')) {
						//$checkoutId = "checkout";
						//$onClick = " javascript: window.location='".$this->MakeUrl('cart/index/','step=2')."';";
					} else	{
						//$checkoutId = "login-box";
						//$onClick = " javascript: Rainbow.overlay.open(this,'".$this->MakeUrl('login/cart@index@'.$this->Encode('step=2'))."'); return false;";
					}*/
				}
			}
			
			if($_GET['act']=='update') $msg = "Cart Information updated successfully.";
			if($_GET['act']=='delete') $msg = "Item(s) deleted successfully.";
			if($_GET['act']=='empty') $msg = "All Items deleted successfully.";
			if($_GET['act']=='failed') {
				$msg = "Your transaction has been declined.";
			 }
			if($prod_error) {
				if($msg!="") $msg.='<br />';
				if(count($prod_error_ids)==1) $sep = 'is'; else $sep = 'are';
				$msg .= "Product number <strong>".implode(", ",$prod_error_ids)."</strong> ".$sep." no longer available with us. Sorry for inconvenience.";
			}
			include('html/cart_1.html');
		}
		break;
}
$buffered_output = ob_get_clean();
?>