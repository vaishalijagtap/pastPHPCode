<?php
ob_start();
switch($this->mSubModule) {
	case "success":

		$orderno = $_SESSION["ss_last_orderno"];
		
		if($this->ExtraSettings['paypal_mode']=='y') {
			$ppAcc = $this->ExtraSettings['account_type_test'];
			$url = PAYPAL_TEST_URL; //Test	
			$at = $this->ExtraSettings['identity_token_test'];; //PDT Identity Token
		} else {
			$ppAcc = $this->ExtraSettings['account_type_live'];
			$url = PAYPAL_LIVE_URL; // Live
			$at = $this->ExtraSettings['account_type_live']; //PDT Identity Token
		}
		
		//$ppAcc = "seller_1294294903_biz@ssinfotech.biz";
		//$at = "Szoa2Yn5QlN34rMjrHbOIVL2wC0RQlTkemK6WFEZek2UHez0x9rBb-NFRle"; //PDT Identity Token
		//$url = "https://www.sandbox.paypal.com/cgi-bin/webscr"; //Test
		//$url = "https://www.paypal.com/cgi-bin/webscr"; //Live
		$tx = $_REQUEST["tx"]; //this value is return by PayPal
		$cmd = "_notify-synch";
		$post = "tx=$tx&at=$at&cmd=$cmd";
		
		//Send request to PayPal server using CURL
		$ch = curl_init ($url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
		
		$result = curl_exec ($ch); //returned result is key-value pair string
		$error = curl_error($ch);
		
		if (curl_errno($ch) != 0) {//CURL error
			exit("ERROR: Failed updating order. PayPal PDT service failed.");
		}
		//echo $result;
		//exit;
		$longstr = str_replace("\r", "", $result);
		//echo "<br />";
		$lines = split("\n", $longstr);
		//echo "<br />";
		/*echo "<pre>";
		print_r($lines);
		echo "</pre>";*/
		//parse the result string and store information to array
		if ($lines[0] == "SUCCESS") {
			//successful payment
			$ppInfo = array();
			for ($i=1; $i<count($lines); $i++) {
				$parts = split("=", $lines[$i]);
				if (count($parts)==2) {
					$ppInfo[$parts[0]] = urldecode($parts[1]);
				}
			}
		
			$curtime = gmdate("d/m/Y H:i:s");
			//capture the PayPal returned information as order remarks
			
			//Update database using $orderno, set status to Paid
			//Send confirmation email to buyer and notification email to merchant
			//Redirect to thankyou page
			
			if($ppInfo['custom']=='shopping-cart') {
				$order_id = $ppInfo["item_number"];
				
				$post_arr = array();
				$post_arr['gateway_transaction_id'] = $this->MysqlString($ppInfo["txn_id"]);
				$post_arr['is_paid'] = "1";
				$post_arr['updated_date'] = "NOW()";
				$post_arr['status'] = "'new_order'";
				$post_arr['is_processing']="0";
				$this->Update(TABLE_CART_ORDER,$post_arr,"cart_order_id='".$order_id."'");
				
				$this->OrderEmail($order_id,'new','user');
				$this->OrderEmail($order_id,'new','admin');
				
				$post_arr = array();
				$post_arr['order_id'] = $this->MysqlString($order_id);
				$post_arr['transaction_date'] = "NOW()";
				$post_arr['transaction_id'] = $this->MysqlString($ppInfo["txn_id"]);
				$post_arr['response'] = $this->MysqlString(serialize($ppInfo));
				
				$this->Insert(TABLE_PAYPAL_TRANSACTIONS,$post_arr);
				/// Delete All Cart Session Data..
				if($_SESSION['sess_user_id']!="")
					$this->Delete(TABLE_CART_SESSION,"customer_id='".$_SESSION['sess_user_id']."'");
				else
					$this->Delete(TABLE_CART_SESSION,"session_id='".session_id()."' and customer_id=0");
				$this->RedirectUrl('cart/index/','step=4&oid='.$order_id);
			} elseif($ppInfo['custom']=='gift-certificate') {
				$gift_id = str_replace('Gift-','',$ppInfo["item_number"]);
				$post_arr = array();
				$post_arr['transaction_id'] = $this->MysqlString($ppInfo["txn_id"]);
				$post_arr['is_paid'] = "1";
				$post_arr['updated_date'] = "NOW()";
		
				$this->Update(TABLE_GIFT_CERTIFICATES,$post_arr,"gift_id='".$gift_id."'");
				
				$this->GiftCertificateEmail($gift_id,'new','user');
				$this->GiftCertificateEmail($gift_id,'new','admin');
				$_SESSION['gift-certificate']="";
				$this->RedirectUrl('gift-certificates/final/','act=success&gift_id='.$gift_id);
				
			}
		} else {//Payment failed
			//Delete order information
			//Redirect to failed page
			if($_SESSION["ss_last_orderno_refer"]=='gift') {
				$gift_id = $_SESSION['ss_gc_orderno'];
				$this->Delete(TABLE_GIFT_CERTIFICATES,"gift_id='".$gift_id."'");
				$this->RedirectUrl('gift-certificates/step-2/','act=failed');
			}
			
			$order_id = $_SESSION["ss_last_orderno"];
			if($_SESSION["ss_last_orderno_refer"]=='sc') {
				$this->Delete(TABLE_CART_ORDER,"cart_order_id='".$order_id."'");
				$this->Delete(TABLE_CART_INVOICE,"cart_order_id='".$order_id."'");
				$this->RedirectUrl('cart/index/','act=failed&step=3');
			} else {
				$this->RedirectUrl('my-orders/detail/','act=failed&id='.$order_id);
			}
			
			
		} 
		exit;
		break;
	case "failed":
		$order_id = $_SESSION["ss_last_orderno"];
		if($_SESSION["ss_last_orderno_refer"]=='sc') {
			$this->Delete(TABLE_CART_ORDER,"cart_order_id='".$order_id."'");
			$this->Delete(TABLE_CART_INVOICE,"cart_order_id='".$order_id."'");
			$this->RedirectUrl('cart/index/','act=failed&step=3');
		} else {
			$this->RedirectUrl('my-orders/detail/','act=failed&id='.$order_id);
		}
		
		break;
	case "gift-failed":
		$gift_id = $_SESSION['ss_gc_orderno'];
		$this->Delete(TABLE_GIFT_CERTIFICATES,"gift_id='".$gift_id."'");
		$this->RedirectUrl('gift-certificates/step-2/','act=failed');
		break;
		
	default:
		/*//echo "here";exit;
		$desc = "All Products details";
		$orderno = "00009890";
		$nettotal = "99.99";
		$_SESSION["ss_last_orderno"] = $orderno;
		
		//Save order information to database using the unique order number with status set as Pending...
		
		
		$url = "https://www.sandbox.paypal.com/cgi-bin/webscr"; //Test
		//$url = "https://www.paypal.com/cgi-bin/webscr"; //Live
		$ppAcc = "seller_1294294903_biz@ssinfotech.biz"; //PayPal account email
		$cancelURL = "http://192.168.20.5/rainbow/process-payment/failed/";
		$returnURL = "http://192.168.20.5/rainbow/process-payment/success/";
		
		$buffer =
		"<form action='$url' method='post' name='frmPayPal' id='frmPayPal'>\n".
		"<input type='hidden' name='business' value='$ppAcc'>\n".
		"<input type='hidden' name='cmd' value='_xclick'>\n".
		"<input type='hidden' name='custom' value='shopping-cart'>\n".
		"<input type='hidden' name='item_name' value='$desc'>\n".
		"<input type='hidden' name='item_number' value='$orderno'>\n".
		"<input type='hidden' name='amount' value='$nettotal'>\n".
		"<input type='hidden' name='no_shipping' value='1'>\n".
		"<input type='hidden' name='currency_code' value='USD'>\n".
		"<input type='hidden' name='handling' value='0'>\n".
		"<input type='hidden' name='cancel_return' value='$cancelURL'>\n".
		"<input type='hidden' name='return' value='$returnURL'>\n".
		"</form>\n".
		"<script language='javascript'>document.frmPayPal.submit();</script>\n";
		
		echo($buffer);*/
}
$buffered_output = ob_get_clean();
?>