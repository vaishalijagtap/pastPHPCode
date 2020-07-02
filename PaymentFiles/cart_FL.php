<?

$cart=new Cart(session_id(),$this);
switch($this->mSubModule)
{
	case "promolink":
		$qty=$this->mArgs[2];
		$pid=$this->mArgs[3];
		$colorid=$this->mArgs[4];
		$this->RedirectUrl($this->mModuleUrl.'/add/','bypromo=yes&qty='.$qty.'&pid='.$pid.'&colorid='.$colorid);
		break;	
	case "add":
		if($_POST['id']!="") {
			
			$cart->AddToCart($_POST['id']."|".$_POST['hidcolorid_'.$_POST['id']]);

		}else if($_GET['bypromo']=='yes'){
			$cart->AddToCart($_GET['pid']."|".$_GET['colorid']);
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
			$cart->DeleteFromCart($_GET['id']);
		}
		$this->RedirectUrl('cart/index/');
		break;
	case "order":
		if($_POST['action']==$this->mPageName)
		{
			//print_r($_POST); exit;
			$post_arr['bill_name']=$this->MysqlString(stripslashes($_POST['txtname']));
			$post_arr['bill_address']=$this->MysqlString(stripslashes($_POST['txtaddress']));
			$post_arr['bill_city']=$this->MysqlString(stripslashes($_POST['txtcity']));
			$post_arr['bill_state']=$this->MysqlString($_POST['txtstate']);
			$post_arr['bill_zipcode']=$this->MysqlString(stripslashes($_POST['txtzip']));
			$post_arr['bill_country']=$this->MysqlString("US");
			$post_arr['bill_phone']=$this->MysqlString($_POST['txtphone']);
			$post_arr['ship_name']=$this->MysqlString(stripslashes($_POST['txtsname']));
			$post_arr['ship_address']=$this->MysqlString(stripslashes($_POST['txtsaddress']));
			$post_arr['ship_city']=$this->MysqlString(stripslashes($_POST['txtscity']));
			$post_arr['ship_state']=$this->MysqlString($_POST['txtsstate']);
			$post_arr['ship_zipcode']=$this->MysqlString(stripslashes($_POST['txtszip']));
			$post_arr['ship_country']=$this->MysqlString("US");
			$post_arr['ship_phone']=$this->MysqlString($_POST['txtsphone']);
			$post_arr['e_mail']=$this->MysqlString($_POST['txtemail']);
			$post_arr['customer_id']="'".$_SESSION['sess_user_id']."'";
			//echo $post_arr['bill_name']."<br>";
			//echo $post_arr['ship_name']; exit;
			//$post_arr['subtotal']="'0.00'";
			//$post_arr['customer_id']="'".$_SESSION['sess_user_id']."'";
			$post_arr['product_total']="'0.00'";
			$post_arr['total_tax']="'0.00'";
			
			$post_arr['total_ship']=$this->MysqlFloat($_POST['shipping_amount']);
			$post_arr['is_ship_assembled']=$this->MysqlString($_POST['selshippingclass']);
			$post_arr['ship_referenceno']=$this->MysqlString($_POST['ship_referenceno']);
			
			$post_arr['ship_type']=$this->MysqlString($_POST['shipping_type']);
			$post_arr['order_total']="'0.00'";
			$post_arr['status']="'approved'";
			$post_arr['order_date']="NOW()";
			$post_arr['payment_gateway']="'YourPay'";
			
			//------------------------------------------------------------------------------------------//
			//------------------------------Payment Information Array-----------------------------------//
			//------------------------------------------------------------------------------------------//
			$pay_data=array();
			$pay_data['store_no']=YOURPAY_STORE_NO;
			$pay_data['orderid']=uniqid('ord_');
			$pay_data['subtotal']=$_POST['sub_total'];
			$pay_data['shipping']=$_POST['shipping_amount'];
			$pay_data['expirymonth']=$_POST['txtexpirymonth'];
			$pay_data['expiryyear']=$_POST['txtexpiryyear'];
			$pay_data['cardno']=$_POST['txtccnumber'];
			$pay_data['nameoncard']=$_POST['txtccname'];
			$pay_data['cvvcode']=$_POST['txtcccvv'];
			
			$pay_data['name']=$_POST['txtname'];
			$pay_data['address']=$_POST['txtaddress'];
			$pay_data['city']=$_POST['txtcity'];
			$pay_data['state']=$_POST['txtstate'];
			$pay_data['phone']=$_POST['txtphone'];
			$pay_data['email']=$_POST['e_mail'];
			$pay_data['zip']=$_POST['txtzip'];
			
			$pay_data['sname']=$_POST['txtsname'];
			$pay_data['saddress']=$_POST['txtsaddress'];
			$pay_data['scity']=$_POST['txtscity'];
			$pay_data['sstate']=$_POST['txtsstate'];
			$pay_data['sphone']=$_POST['txtsphone'];
			$pay_data['szip']=$_POST['txtszip'];

			if($_POST['payment_flag']==0) {
				$payment_status=$this->ProcessPayment($pay_data);
				//$payment_status=1; 
				if($payment_status['status']==1){
					?>
					<script language="javascript">
						parent.document.getElementById('btn_submit_payment').disabled="";
						parent.document.viewcart.payment_flag.value="1";
						parent.document.viewcart.orderid.value="<?=$pay_data['orderid']?>";
						var cardno=parent.document.viewcart.txtccnumber.value;
						var cardno_len=cardno.length;
						var newcardno="";
						for(var i=0;i<cardno_len-4;i++) {
							if(cardno.charAt(i)!=" " && cardno.charAt(i)!="-")
								newcardno+="X";
							else newcardno+=cardno.charAt(i);
						}
						for(var i=cardno_len-4;i<cardno_len;i++) {
							newcardno+=cardno.charAt(i);
						}
						parent.document.viewcart.txtccnumber.value=newcardno;
						parent.document.viewcart.txtexpirymonth.value="XX";
						parent.document.viewcart.txtexpiryyear.value="XX";
						parent.document.viewcart.txtcccvv.value="XXXX";
						//alert("Transaction Successfull! \n <? //$payment_status['transaction_id']?>");
						parent.document.viewcart.target="";
						parent.document.viewcart.submit();
					</script>
					<?
				} else {
					?>
					<script language="javascript">
						parent.document.getElementById('btn_submit_payment').disabled="";
						parent.document.getElementById('payment_proccess').style.display="none";
						parent.document.getElementById('payment_proccess_img').style.display="none";
						alert("Transaction Denied \n <?=addslashes($payment_status['message'])?>");
					</script>
					<?
				}
				exit;
			} 
			$post_arr['gateway_order_id']=$this->MysqlString($_POST['orderid']);
			
			$order_id=$this->Insert(TABLE_CART_ORDER,$post_arr);
			
			$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'");
			if(count($session_data)>0) {
				foreach($session_data as $data) {
					$product_name=$cart->GetProductDetails($data['product_id']);
					$post_arr=array();
					$post_arr['cart_order_id']=$order_id;
					$post_arr['product_id']=$this->MysqlString($data['product_id']);
					$post_arr['product_name']=$this->MysqlString($product_name['name']);
					$post_arr['quantity']=$data['quantity'];
					$post_arr['unit_weight']=$product_name['unit_weight'];
					$post_arr['price']=$this->MysqlString(sprintf("%01.2f",$data['price']));
					$color_arr=explode('|',$data['product_id']);
					$color_id=$color_arr[1];
					$color_result=$this->Select(TABLE_COLORPALETTE,"color_id='".$color_id."'","color_idno");
					$color_unique=$color_result[0]['color_idno'];
					$post_arr['color_id']=$this->MysqlString($color_unique);
					$post_arr['is_assembled']=$data['is_assembled'];  
					if($post_arr['is_assembled']!=0){
						$post_arr['assembly_price']=$this->GetAssemblyPrice($product_name['product_id']);
					}
					if($post_arr['assembly_price']==""){
						$post_arr['assembly_price']=0.00;
					}
					$invoice_id=$this->Insert(TABLE_CART_INVOICE,$post_arr);
					
					$sub_total+=($data['quantity']*$data['price']);
					$weight_total+=($data['quantity']*$product_name['unit_weight']);
					$cart_total+=($data['quantity']*$data['price']);
				}
			} 
			$post_arr=array();
			//$post_arr['subtotal']=sprintf("%01.2f",$sub_total);
			//$post_arr['weight_total']=sprintf("%01.2f",$weight_total);
			//$post_arr['product_total']=sprintf("%01.2f",$sub_total);
			//$post_arr['order_total']=sprintf("%01.2f",$cart_total);
			
			$post_arr['subtotal']=sprintf("%01.2f",$_POST['sub_total']);
			$post_arr['weight_total']=$weight_total;
			$post_arr['product_total']=sprintf("%01.2f",$_POST['sub_total']);
			$post_arr['order_total']=sprintf("%01.2f",($_POST['sub_total']+$_POST['shipping_amount']));
			
			
			
			$this->Update(TABLE_CART_ORDER,$post_arr,"cart_order_id='".$order_id."'");
			$this->Delete(TABLE_CART_SESSION,"session_id='".session_id()."'");
			
			$getRes_cust = $this->Select(TABLE_CUSTOMERS,"customer_id='".$_SESSION['sess_user_id']."'","");
			if(count($getRes_cust)>0) {
				if($getRes_cust[0]['order_confirmed']==0)   {
					$this->OrderEmail($order_id,'new');
					$this->OrderAdminEmail($order_id,'new');
					$this->Update(TABLE_CART_ORDER,array('is_active'=>"'0'",'user_activated'=>"'0'"),"cart_order_id='".$order_id."'");
				}
				else  {
					$this->OrderEmail($order_id,'activate');
					$this->OrderAdminEmail($order_id,'activate');
					$this->Update(TABLE_CART_ORDER,array('is_active'=>"'1'",'user_activated'=>"'1'"),"cart_order_id='".$order_id."'");
				}
			}
			
			
			
			$this->RedirectUrl('cart/index/','step=4&oid='.sprintf("%04d",$order_id));
		}
		 
			
		 
	default: 
		//print_r($_GET);
		$shopmore_data=$this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'",'product_id','cart_id desc',1);
		if(count($shopmore_data)>0)
		{
			$collection_data=$this->Select(TABLE_PRODUCT,"product_id='".$shopmore_data[0]['product_id']."'",'sub_categoryId',"",1);
			if(count($collection_data)>0)
			{
				$shopmore=$this->MakeUrl('products/index/','id='.$collection_data[0]['sub_categoryId']);
			}
			else
			{
				$shopmore=$this->MakeUrl("","");
			}
		}
		
		if($_GET['step']==4) {
			/*if(!$this->ValidateSession('user')) {
				$this->RedirectUrl('login/index/',"redirect=".$this->MakeUrl('cart/index/','step=2'));
			}*/ 
			include('html/cart_4.html');
			if(!$this->ValidateSession('user')) {
				$_SESSION['sess_step_2']="";
			} 
			$_SESSION['sess_step_shiptype']="";
			/*echo "<pre>"; print_r($_SESSION); echo "</pre>"; exit;*/
		} elseif($_GET['step']==3) { 
			if(!$this->ValidateSession('user')) {
				$this->RedirectUrl('login/index/',"redirect=".$this->MakeUrl('cart/index/','step=3'));
			}
			$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'");
			if(count($session_data)>0) {
				foreach($session_data as $data) {
					$i++;
					//print_r($_POST);  exit;
					//echo $data['product_id']; //exit;
					
					$product_name=$cart->GetProductDetails($data['product_id']);
//					print_r($product_name); exit;
//			echo $_POST['selshippingclass'.$data["product_id"]]; 
					if($_POST['selshippingclass'.$data["product_id"]]==1)
					{
						$is_assembled = 1;
						$upd_arr['is_assembled'] = $this->MysqlInteger($is_assembled);
						$this->Update(TABLE_CART_SESSION,$upd_arr,"session_id='".session_id()."' and product_id='".$data['product_id']."'");
					}
					//echo $data['product_id'];
					$assembly_charge  = $cart->GetAssemblyPrice($product_name['product_id']);
					$price_unassembled = $data['quantity']*$data['price'];
					$price_assembled = $price_unassembled + ($assembly_charge * $data['quantity']);
					if($_POST['selshippingclass'.$data["product_id"]]==1){
						$price = $price_assembled;
					} else {
						$price = $price_unassembled;
					}
					$product_dimension = $cart->GetProductDimensions($data['product_id'], $_POST['selshippingclass'.$data["product_id"]]);
					
					if($assembly_charge==""){
						$assembly_charge=0.00;
					}
					$cart_data.='
						 <tr  class="'.$class.'">
							<td align="center" valign="middle" class="text"><input type="hidden" name="product_id[]" value="'.$data['product_id'].'" />'.$i.'</td>
							<td align="center" valign="middle" class="text"><img src="'.$product_name['image'].'" title="'.ucfirst(stripslashes($product_name['product_name'])).'"  /></td>
							<td align="left" style="padding-left:5px;" valign="middle" class="text">
								<table width="100%" cellpadding="0">
									<tr><td align="left" style="padding-left:5px;" valign="middle" class="text">
										'.ucfirst(stripslashes($product_name['name'])).'
									</td></tr>
									<tr><td align="left" style="padding-left:5px;" valign="middle" class="text"><strong>Dimensions:</strong></td></tr>
									<tr><td>
										'.$product_dimension.'
									</td></tr>
								</table>
							</td>
							<td align="center" valign="middle" class="text">'.$data['quantity'].'</td>
							<td valign="middle" class="text" style="padding-right:15px;" align="right">'.$product_name['unit_weight'].' lbs</td>
							<td valign="middle" class="text" style="padding-right:15px;" align="right">$'.sprintf("%01.2f",$data['price']).'</td>
							';
							if($_POST['selshippingclass'.$data["product_id"]]==1){
							$cart_data.='
							<td valign="middle" class="text" style="padding-right:15px;" align="right"><input type="hidden" name="asembly_charge[]" value="'.sprintf("%01.2f",$assembly_charge).'" />
							$'.sprintf("%01.2f",$assembly_charge).'</td>';
							} else {
							$cart_data.='
							<td valign="middle" class="text" style="padding-right:15px;" align="right">N/A</td>';
							}			
							$cart_data.='				
							<td valign="middle" class="text" style="padding-right:15px;" align="right">$'.sprintf("%01.2f",$price).' </td>
						</tr>
					';
					$sub_total=($_POST['sub_total']);
					$weight_total+=($data['quantity']*$product_name['unit_weight']);
					$cart_total=($sub_total)+$_POST['shipping_amount'];
				} //exit;
			}
			else
			{
				$this->RedirectUrl('cart/index/');
				exit;
			}
			//$cust_data = $this->Select(TABLE_CUSTOMERS,"customer_id='".$_SESSION['sess_user_id']."'","card_info_status","",1);
			//if(count($cust_data) > 0){
			//	$card_info = $cust_data[0]['card_info_status'];
			//}
			include('html/cart_3.html');
		} elseif($_GET['step']==2) {

			if(!$this->ValidateSession('user')) {
				$this->RedirectUrl('login/index/',"redirect=".$this->MakeUrl('cart/index/','step=2'));
			}


			/*if($this->ValidateSession('user') && empty($_SESSION['sess_step_2']) ) {
				$user_data=$this->Select(TABLE_CUSTOMERS,"customer_id='".$_SESSION['sess_user_id']."'","*");
				$name = "";
				if(count($user_data)>0) {
					foreach($user_data as $arr) {
						$data_arr=$arr;
					}
				}
				$name = ucfirst(stripslashes($data_arr['first_name']));
				if(trim($data_arr['last_name']))
					$name .= ' '.ucfirst(stripslashes($data_arr['last_name']));
					//echo $name; exit;
				$_SESSION['sess_step_2']["txtname"] = $name;
				$_SESSION['sess_step_2']["txtaddress"] = stripslashes($data_arr['address']);
				$_SESSION['sess_step_2']["txtemail"] = stripslashes($data_arr['e_mail']);
				$_SESSION['sess_step_2']["txtphone"] = stripslashes($data_arr['phone_no']);
				$_SESSION['sess_step_2']["txtcity"] = stripslashes($data_arr['city']);
				$_SESSION['sess_step_2']["txtstate"] = stripslashes($data_arr['state']);
				$_SESSION['sess_step_2']["txtzip"] = stripslashes($data_arr['zip']);
			}*/
			
			if($_GET['shipment']==1) {
				foreach($_POST as $key=>$value) {
					if(substr($key,0,3)=='txt') {
						$_SESSION['sess_step_2'][$key]=$value;
					}
				}
				foreach($_POST as $key=>$value) {
					if(substr($key,0,3)=='sel') { 
						$_SESSION['sess_step_shiptype'][$key]=$value;
					}
				}
				
				 //print_r($_SESSION['sess_step_2']);
				$dayton_frieght=0;
				$assembly_override=0;
				$class_arr="";
				// Condition 1: Check if the products are assemled or not, If yes then use Dayton Frieght
				foreach($_POST['product_id'] as $pids) {
					$pid = explode("|",$pids);
					$product_arr=$cart->GetProductDetails($pids);
					if($_POST['selshippingclass'.$pids]==1) {
						$class_arr[] = $product_arr['freight_assembled'];
					}
					else {
						$class_arr[] = $product_arr['freight_unassembled'];
					}
						
					
					if($_POST['selshippingclass'.$pids]==1 && $product_arr['assembly_override']!='y') {
						$dayton_frieght=1;
					}
					 

				}
				$shipping_class = max($class_arr);
				// Condition 2: If all products are unassembles then Check if the products total weight is Greater then 200 pounds or not, If yes then use Dayton Frieght
				$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'");
				if(count($session_data)>0) {
					if($dayton_frieght==0) {
						foreach($session_data as $data) {
							$product=$cart->GetProductDetails($data['product_id']);
							$weight_total+=($data['quantity']*$product['unit_weight']);
							if(($data['quantity']*$product['unit_weight']) > 100) {
								$dayton_frieght=1;
							}
						}
						if($weight_total>200) {
							$dayton_frieght=1;
						}
					}
				}
			//	print_r($session_data);
				if(count($session_data)>0) {
					$i=0;
					$weight_total=0;
					foreach($session_data as $data) { 
						$product=$cart->GetProductDetails($data['product_id']);
						$weight_total+=($data['quantity']*$product['unit_weight']);
						$product_name.=$sep.ucfirst(strip_tags(stripslashes($product['name'])));
					}
					
					$this->getShippingInfo($product_name,$weight_total,$dayton_frieght,$shipping_class);
				}
				exit;
			}
			
			/*$mem_arr=$this->Select(TABLE_CUSTOMERS,"customer_id='".$_SESSION['sess_user_id']."'");
			if(count($mem_arr)>0) {
				foreach($mem_arr as $member) {
					$member=$member;
				}
			}*/
			$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'");
			if(count($session_data)>0) { ?>
				<script> 
					var arr_pid = new Array();
				</script>
				<?
				$i = 0;
				$j = 0;
				$sub_total = 0.00;
				foreach($session_data as $data) {
					$i++; 
					//$product_id_arr = explode("|",$data['product_id']);
					?>	
					<script>
						arr_pid[<?=$j?>]='<?=$data['product_id']?>';	
					</script>	
					<?
					$j++;
			
					$product_name=$cart->GetProductDetails($data['product_id']);
					$product_dimension = $cart->GetProductDimensions($data['product_id'], 2);
					$assembly_charge  = $cart->GetAssemblyPrice($data['product_id']);
					$sub_total += $data['quantity']*$data['price'];
					$price_assembled = $price_unassembled + ($assembly_charge * $data['quantity']);
					if($assembly_charge=="")
					{
						$assembly_charge=0.00;
					}
					
					$arr_type = array('0'=>'Unassembled','1'=>'Assembled');
					if(count($arr_type)>0) { 
						$shipping_dropdown ="";
						
						foreach($arr_type as $key=>$value) {
							$shipping_dropdown .= '<option value="'.$key.'"'; 
							if($_SESSION['sess_step_shiptype']['selshippingclass'.$data['product_id']] == $key ) 
								$shipping_dropdown .= ' selected="selected"';
							$shipping_dropdown .= '>'.$value.'</option>';
						}
					}
					//echo $data['product_id'];
					$cart_data.='
						 <tr  class="'.$class.'">
							<td align="center" valign="middle" class="text"><input type="hidden" name="product_id[]" value="'.$data['product_id'].'" />'.$i.'</td>
							<td align="center" valign="middle" class="text"><img src="'.$product_name['image'].'" title="'.$product_name['product_name'].'"  /></td>
							<td align="left" style="padding-left:5px;" valign="middle" class="text">
								<table width="100%" cellpadding="0">
									<tr><td align="left" style="padding-left:5px;" valign="middle" class="text">
										'.ucfirst(stripslashes($product_name['name'])).'
									</td></tr>
									<tr><td align="left" style="padding-left:5px;" valign="middle" class="text"><strong>Dimensions:</strong></td></tr>
									<tr><td>
										'.$product_dimension.'
									</td></tr>
								</table>
							</td>
							<td align="left" style="padding-left:5px;" valign="middle" class="text">
								<select name="selshippingclass'.$data['product_id'].'" class="select_shipping" onchange="javascript: setShipment(this.value,\''.$data['product_id'].'\', '.$data['quantity'].', \''.sprintf("%01.2f",$data['price']).'\', \''.sprintf("%01.2f",$assembly_charge).'\');">
									'.$shipping_dropdown.'
								</select>
							</td>
							<td align="center" valign="middle"  class="text">'.$data['quantity'].'</td>
							<td valign="middle" class="text" style="padding-right:15px;" align="right">'.$product_name['unit_weight'].' lbs</td>
							<td valign="middle" class="text" style="padding-right:15px;" align="right">$'.sprintf("%01.2f",$data['price']).'</td>
							
							<td valign="middle"  class="text" style="padding-right:25px;" align="right">
								<span id="divUnassembled'.$data['product_id'].'" style="display:;">N/A</span>
								<span id="divDoller'.$data['product_id'].'" style="display:none">$</span><span id="divAssembled'.$data['product_id'].'" style="display:none;">'.sprintf("%01.2f",$assembly_charge).'</span>
							</td>
							<td valign="middle" class="text" style="padding-right:15px;" align="right">$<span id="divPrice'.$data['product_id'].'">'.sprintf("%01.2f",($data['quantity']*$data['price'])).' </span></td>
						</tr>
						<tr><td height="5"></td></tr>
					';
					
					$weight_total+=($data['quantity']*$product_name['unit_weight']);
					
					$sub_total_assembled+=$price_assembled;
					$cart_total_assembled+=$price_assembled;
					
					$sub_total_unassembled+=$price_unassembled;
					$cart_total_unassembled+=$price_unassembled;
					
					//$sub_total+=($data['quantity']*$data['price']);
					//$weight_total+=($data['quantity']*$product_name['unit_weight']);
					//$cart_total+=($data['quantity']*$data['price']);
				}
			}
			else
			{
				$this->RedirectUrl('cart/index/');
				exit;
			}
			//echo $_SESSION['sess_step_2']["txtstate"];
			$billing_state_data = $this->GetStateDropDown($_SESSION['sess_step_2']['txtstate']);
			$shipping_state_data = $this->GetStateDropDown($_SESSION['sess_step_2']["txtsstate"]);
			include('html/cart_2.html');
		} else { 
			$session_data=$this->Select(TABLE_CART_SESSION,"session_id='".session_id()."'");
			$i=0;
			
			if(count($session_data)>0) {
				foreach($session_data as $data) {
					$product_id = $data['product_id'];
					
					$product_name=$cart->GetProductDetails($data['product_id']);
					
					if($product_name['qty_note']!="") $qty_note="<br />(Mutliple of ".$product_name['qty_note'].")"; else $qty_note="";
					if($i!=0) {
						$cart_data.='
								<tr>
								  <td height="2" colspan="7" ></td>
								</tr>
								<tr>
								  <td height="1" colspan="7" class="cart_seperator"></td>
								</tr>
								<tr>
								  <td height="2" colspan="7" ></td>
								</tr>
						';
					} else {
						$i++;
					}
					$dname=explode("<br />",stripslashes($product_name['name']));
					$dname=ucfirst(strip_tags($dname[0]));
					$color = $product_name[color_id];
					$url = $this->MakeUrl('requestaquote_qs/index/','pid='.$product_name['product_id'].'&color_id='.$color.'&scat='.$product_name['sub_categoryId']);
					$cart_data.='
						 <tr  class="'.$class.'">'.
						 	$this->AddFormField('hidden',"hidcolorid_".$product_name['product_id'],$product_name['color_id'],"",0).'
							'.$this->AddFormField('hidden',"hidcolorname_".$product_name['product_id'],$product_name['color'],"",0).'
							'.$this->AddFormField('hidden',"productid",$product_name['product_id'],"",0).'
							'.$this->AddFormField('hidden',"scat",$product_name['sub_categoryId'],"",0).'
							<td align="center" valign="middle"><input type="hidden" name="product_id[]"  id="product_id" value="'.$data['product_id'].'" /><a href="javascript: if(confirm(\'Are you sure you want to delete this item?\')) { window.location=\''.$this->MakeUrl('cart/delete/','id='.$data['product_id']).'\'; }" ><img src="'.SITE_URL.'images/del.gif" alt="Remove" style="cursor:pointer;" border="0" /></a></td>
							<td align="center" valign="middle" class="text"><img src="'.$product_name['image'].'" title="'.$product_name['product_name'].'"  /></td>
							<td align="left" style="padding-left:5px;" valign="middle" class="text">
										<span id="pro_name_'.$data['product_id'].'" style="display:none;">'.$dname.'</span>'.ucfirst(stripslashes($product_name['name'])).'
									</td>
							<td align="center" valign="middle"  class="text">'.$this->AddFormField('text',"qty_".$data['product_id']."",$data['quantity'],"small_input_field",0,'Quamtity','',' maxlength="4" onChange="javascript: chk_int(this,\''.$product_name['qty_note'].'\',\''.$product_name['qty_max'].'\',\''.$url.'\' )"').$qty_note.'</td>
							<td valign="middle"  class="text" style="padding-right:15px;" align="right">'.$product_name['unit_weight'].' lbs</td>
							<td valign="middle"  class="text" style="padding-right:15px;" align="right">$'.sprintf("%01.2f",$data['price']).'</td>
							<td  valign="middle"  class="text" style="padding-right:15px;" align="right">$'.sprintf("%01.2f",($data['quantity']*$data['price'])).' </td>
						</tr>';
						
					
					$sub_total+=($data['quantity']*$data['price']);
					$weight_total+=($data['quantity']*$product_name['unit_weight']);
					$cart_total+=($data['quantity']*$data['price']);
				}
			}
			else
			{
				$cart_data='<tr><td colspan="7" class="redheading" align="center">No Item(s) found.</td><tr>';
				$cart_emty=true;
			}
			include('html/cart_1.html');
		}
		//$this->AddFormField(,,,,,
		break;
}
?>