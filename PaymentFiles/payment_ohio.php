<?php
//session_start();
//ini_set('session.cache_limiter', 'private'); //private_no_expire
//session_start();

//initialize minimum donation amount for this session
if($_SESSION['sess_donation']['minimum_donation_ammount']==''){
	$_SESSION['sess_donation']['minimum_donation_ammount'] = $this->minimum_donation_ammount;
}
//$this->SendReceipt(18); die;
switch($this->mSubModule) {
	case "step1":
		$payment_form = $this->GetPaymentForm();
		break;
	
	case "step2":
		if ($this->using_ie()) {
			header( 'Cache-Control: private, max-age=10800, pre-check=10800' );
		}
		//header( 'Cache-Control: private' );
		//header( 'Cache-Control: private, must-revalidate');
		// disable any caching by the browser
		/*header('Expires: Mon, 14 Oct 2002 05:00:00 GMT');              // Date in the past
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT'); // always modified
		header('Cache-Control: private, must-revalidate');  // HTTP 1.1
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache'); */
		if($_POST['action']==$this->mPageName) {
			$_SESSION['sess_donation']['txt_phone']="";
			$_SESSION['sess_donation']['txt_fax']="";
			$_SESSION['sess_donation']['step1'] = 'completed';
			foreach($_POST as $key=>$value) {
				if(substr($key,0,4)=='txt_') {
					$_SESSION['sess_donation'][$key] = $value;
				}
			}
			$phone_arr = array($_SESSION['sess_donation']['txt_phone1'],$_SESSION['sess_donation']['txt_phone2'],$_SESSION['sess_donation']['txt_phone3']);
			if($_SESSION['sess_donation']['txt_phone1']!='') $_SESSION['sess_donation']['txt_phone'] = implode("-",$phone_arr); else $_SESSION['sess_donation']['txt_phone'] = "";
			$fax_arr = array($_SESSION['sess_donation']['txt_fax1'],$_SESSION['sess_donation']['txt_fax2'],$_SESSION['sess_donation']['txt_fax3']);
			if($_SESSION['sess_donation']['txt_fax1']!='') $_SESSION['sess_donation']['txt_fax'] = implode("-",$fax_arr); else $_SESSION['sess_donation']['txt_fax'] = "";
			
		}
		
		if($_SESSION['sess_donation']['step1']=='') {
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/step1/');
		}
		if($_SESSION['under_process']==1){
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/step1/');
		}
		//if($_SESSION['sess_donation']['txt_no_of_students']!='') {
		//$js_call = " getNoOfStudents($('#frm_info_2'),denationamtResponse,".$_SESSION['sess_donation']['txt_amount'].");";
		//}
		//$_POST = array();
		break;

	case "step3":
		if ($this->using_ie()) {
			//header( 'Cache-Control: private, max-age=10800, pre-check=10800' );
		}
		//header( 'Cache-Control: private, max-age=10800, pre-check=10800' );
		//header( 'Cache-Control: private');
		if($_POST['action']=='update_students') {
			$_SESSION['sess_donation']['step2'] = 'completed';
			foreach($_POST as $key=>$value) {
				if(substr($key,0,4)=='txt_') {
					$_SESSION['sess_donation'][$key] = $value;
				}
			}
		}
		//print_r($_SESSION['sess_donation']);
		$_SESSION['sess_donation']['txt_no_of_students']=$_SESSION['sess_donation']['txt_no_students'].$_SESSION['sess_donation']['txt_show_sign'];
		$county_dropdown = $this->GetCountyDD($_SESSION['sess_donation']['txt_country']);
		$ethnicity_dropdown = $this->GetEthnicityDD($_SESSION['sess_donation']['txt_ethnicity']);
		$colleges_dropdown = $this->GetCollegesDD($_SESSION['sess_donation']['txt_colleges']);
		$majors_dropdown = $this->GetMajorsDD($_SESSION['sess_donation']['txt_majors']);

		if($_SESSION['sess_donation']['step1']=='' || $_SESSION['sess_donation']['step2']=='') {
			$this->RedirectUrl('payment/step1/');
		}
		if($_SESSION['under_process']==1){
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/step1/');
		}
		
		//$js_call = '';
		//if($_SESSION['sess_donation']['txt_criteria']=='optionb')	$js_call = ' makeSelected(1);';
		break;
	
	case "review":
		if ($this->using_ie()) {
			header( 'Cache-Control: private, max-age=10800, pre-check=10800' );
		}
		//populating session with post data
		if($_POST['action']=='review') {
			$_SESSION['sess_donation']['step3']='completed';
			foreach($_POST as $key=>$value) {
				if(substr($key,0,4)=='txt_') {
					$_SESSION['sess_donation'][$key] = $value;
				}
			}
			if($_POST['txt_gender']=='')	$_SESSION['sess_donation']['txt_gender'] = "";
		} 

		if($_SESSION['sess_donation']['step1']=='' || $_SESSION['sess_donation']['step2']=='' || $_SESSION['sess_donation']['step3']=='') {
			$this->RedirectUrl('payment/step1/');
		}
		
		///checkign if the users clicks on back button
		if($_SESSION['under_process']==1){
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/step1/');
		}
		
		//print_r($_SESSION['sess_donation']);die;
		$response = $this->getNumberOfScholars($_SESSION['sess_donation']['txt_amount']);
		$donationAmount = trim($_SESSION['sess_donation']['txt_amount']);
		$_SESSION['sess_donation']['txt_no_students'] = $response['no_students'];
		$_SESSION['sess_donation']['txt_admin_fee_percent'] = $this->admin_fee_percent;
		$admin_fee = ($donationAmount*$this->admin_fee_percent/100);
		$distribute_amt = ($donationAmount-$admin_fee);
		
		$country =  $_SESSION['sess_donation']['txt_country'];
		
		$gender =  str_replace(array('m','f'), array('Male','Female'), strtolower($_SESSION['sess_donation']['txt_gender']));
		
		$ethnicity_dropdown = $this->GetEthnicity($_SESSION['sess_donation']['txt_ethnicity']);
		$colleges_dropdown = $this->GetColleges($_SESSION['sess_donation']['txt_colleges']);
		$majors_dropdown = $this->GetMajors($_SESSION['sess_donation']['txt_majors']);
		
		$high_school =  $_SESSION['sess_donation']['txt_high_school'];
		$religious_affilation =  $_SESSION['sess_donation']['txt_rel_affl'];
		
		if($country!='') {
			$criteria = '<div class="aligment-row" >
							<div class="aligment-left-div" >County:</div>
							<div class="aligment-right-div">'.$country.'</div>
						</div>';
		}
		if($gender!='') {
			$criteria .='<div class="aligment-row">
							<div class="aligment-left-div">Gender:</div>
							<div class="aligment-right-div">'.$gender.'</div>
						</div>';
		}
		if($ethnicity_dropdown!='') {
			$criteria .='<div class="aligment-row">
							<div class="aligment-left-div">Ethnicity:</div>
							<div class="aligment-right-div">'.$ethnicity_dropdown.'</div>
						</div>';
		}
		if($colleges_dropdown!='') {
			$criteria .='<div class="aligment-row">
							<div class="aligment-left-div">Colleges:</div>
							<div class="aligment-right-div">'.$colleges_dropdown.'</div>
						</div>';
		}
		if($majors_dropdown!='') {
			$criteria .='<div class="aligment-row">
							<div class="aligment-left-div">Majors:</div>
							<div class="aligment-right-div">'.$majors_dropdown.'</div>
						</div>';
		}
		if($high_school!='') {
			$criteria .='<div class="aligment-row">
							<div class="aligment-left-div">High School:</div>
							<div class="aligment-right-div">'.$high_school.'</div>
						</div>';
		}
		if($religious_affilation!='') {
			$criteria .='<div class="aligment-row">
							<div class="aligment-left-div">Religious Affiliation:</div>
							<div class="aligment-right-div">'.$religious_affilation.'</div>
						</div>';
		}
		break;
	
	case "selection":
		if($_POST['action']=='selection') {
			$_SESSION['sess_donation']['review']='completed';
		}
		
		if($_SESSION['sess_donation']['step1']=='' || $_SESSION['sess_donation']['step2']=='' || $_SESSION['sess_donation']['step3']=='' || $_SESSION['sess_donation']['review']=='') {
			$this->RedirectUrl('payment/step1/');
		}
		
		///checkign if the users clicks on back button
		if($_SESSION['under_process']==1){
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/step1/');
		}
		break;
	
	case "process": 
		///checkign if the users clicks on back button
		if($_SESSION['under_process']==1){
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/step1/');
		}
		if($_SESSION['under_process']!=1){
			$_SESSION['under_process'] = 1;
			/**************** Preparing data to insert in transaction table ************************/
			$donationAmount = trim($_SESSION['sess_donation']['txt_amount']);
			if($donationAmount < trim($_SESSION['sess_donation']['minimum_donation_ammount'])){
				$this->RedirectUrl('payment/step2/', 'amt_err=1');
			}
			
			$response = $this->getNumberOfScholars($donationAmount);
			$no_students = $response['no_students'];
			$show_sign = $response['show_sign'];
			$admin_fee = ($donationAmount*$this->admin_fee_percent/100);
			$donation_amount_after_deduction = $donationAmount-$admin_fee;
			$admin_fee_percent = $_SESSION['sess_donation']['txt_admin_fee_percent'];
			
			$admin_fee = sprintf("%0.2f",$admin_fee);
			$donation_amount_after_deduction = sprintf("%0.2f",$donation_amount_after_deduction);
			$donationAmount = sprintf("%0.2f",$donationAmount);

			$post_arr = $_SESSION['sess_donation'];
			$ethnicity= $this->GetEthnicity($_SESSION['sess_donation']['txt_ethnicity']);
			$colleges= $this->GetColleges($_SESSION['sess_donation']['txt_colleges']);
			$majors= $this->GetMajors($_SESSION['sess_donation']['txt_majors']);
			$post_arr['txt_ethnicity'] = $ethnicity;
			$post_arr['txt_colleges'] = $colleges;
			$post_arr['txt_majors'] = $majors;
			$transaction_type  = $_POST['txt_payment_type'];

			$arr_type=array('txt_name' => 'STRING',
							'txt_sname'=>'STRING',
							'txt_address'=>'STRING',
							'txt_phone'=>'STRING',
							'txt_fax'=>'STRING',
							'is_processed'=>'\'n\'',
							'txt_email'=>'STRING',
							'donation_amount'=>$donationAmount,
							'donation_amount_after_deduction'=>$donation_amount_after_deduction,
							'admin_fee'=>$admin_fee,
							'admin_fee_percent'=>$admin_fee_percent,
							'no_of_students'=>$no_students,
							'show_sign'=>"'".$show_sign."'",
							'txt_criteria'=>'STRING',
							'txt_country'=>'STRING',
							'txt_gender'=>'STRING',
							'txt_ethnicity'=>'STRING',
							'txt_colleges'=>'STRING',
							'txt_majors'=>'STRING',
							'txt_high_school'=>'STRING',
							'txt_rel_affl'=>'STRING',
							'txt_comments'=>'STRING',
							'txt_questions'=>'STRING',
							'transaction_date'=>'NOW()',
							'transaction_type'=>"'".$transaction_type ."'",
							'ip_address'=>"'".$_SERVER['REMOTE_ADDR']."'"
						);
			$keyvalue_arr=array('txt_name' => 'name',
							'txt_sname'=>'scholarship_name',
							'txt_address'=>'address',
							'txt_phone'=>'phone',
							'txt_fax'=>'fax',
							'txt_email'=>'email',
							'txt_criteria'=>'criteria',
							'txt_country'=>'county',
							'txt_gender'=>'gender',
							'txt_ethnicity'=>'ethnicity_id',
							'txt_colleges'=>'college_id',
							'txt_majors'=>'majors_id',
							'txt_high_school'=>'highschool',
							'txt_rel_affl'=>'religious_affiliation',
							'txt_comments'=>'comments',
							'txt_questions'=>'questions'
							);
			$post_arr=$this->MySqlFormat($post_arr,$arr_type);
			$insert_id = $this->Insert(TABLE_TRANSACTIONS,$post_arr,$keyvalue_arr);		
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			/**************** Checking if Payment is submitting by check or credit card ************************/
			if($_POST['txt_payment_type']=='credit_card') {
				/**************** Posting values to payment gateway ************************/
				$simFields = $this->generateSimPaymentForm($donationAmount, $insert_id);
				$output = $simFields;
				$output .= '<script type="text/javascript">
							window.onload = function (){ document.frm_payment.submit();}
						  </script>';			
			} else {
				$this->Update(TABLE_TRANSACTIONS,array('is_processed'=>'\'y\''),"transaction_id='".$insert_id."'");
				$this->SendReceipt($insert_id,'user');
				$this->SendReceipt($insert_id,'admin');
				unset($_SESSION['sess_donation']);
				unset($_SESSION['under_process']);
				$this->RedirectUrl('payment/thanks/', 'status=success&oid='.$insert_id);
			}
			
		}	
		break;	
	
	case "receipt":		
		$insert_id = trim($_POST['x_insert_id']);
		$trans_id = $_POST['x_trans_id'];
		$card_number = $_POST['x_account_number'];
		$card_type = $_POST['x_card_type'];
		$response_reason_text = $_POST['x_response_reason_text'];
		if($_POST['x_response_code']==1){
			$this->Update(TABLE_TRANSACTIONS,array('is_processed'=>'\'y\'','cart_type'=>"'".$card_type."'",'card_number'=>"'".$card_number."'",'gateway_transaction_id'=>"'".$trans_id."'"),"transaction_id='".$insert_id."'");
			$this->SendReceipt($insert_id,'user');
			$this->SendReceipt($insert_id,'admin');
			
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/thanks/', 'status=success&oid='.$insert_id);
		}else{
			$this->Update(TABLE_TRANSACTIONS,array('response_reason_text'=>"'".$response_reason_text."'", 'cart_type'=>"'".$card_type."'",'card_number'=>"'".$card_number."'",'gateway_transaction_id'=>"'".$trans_id."'"),"transaction_id='".$insert_id."'");		
			unset($_SESSION['sess_donation']);
			unset($_SESSION['under_process']);
			$this->RedirectUrl('payment/thanks/', 'status=fail&err='.$response_reason_text);
		}			
	break;
	
	case "thanks":
		unset($_SESSION['sess_donation']);
		unset($_SESSION['under_process']);
		
		$id = trim($_GET['oid']);
		if($id!=''){
			$row = $this->Select(TABLE_TRANSACTIONS, "transaction_id='".$id."' AND is_processed='y'","transaction_id, gateway_transaction_id, name, scholarship_name, donation_amount, donation_amount_after_deduction, admin_fee, no_of_students, show_sign, criteria, county, gender, ethnicity_id, college_id, majors_id, highschool, religious_affiliation");
			
			$transaction_id = $row[0]['transaction_id'];
			$gateway_transaction_id = $row[0]['gateway_transaction_id'];
			$name = ucwords(stripslashes($row[0]['name']));
			$scholarship_name = ucwords(stripslashes($row[0]['scholarship_name']));
			$donation_amount = $row[0]['donation_amount'];
			$donation_amount_after_deduction = $row[0]['donation_amount_after_deduction'];
			$admin_fee = $row[0]['admin_fee'];
			$no_of_students = $row[0]['no_of_students'].$row[0]['show_sign'];
			//$criteria = $row[0]['criteria'];
			
			$country = stripslashes(ucfirst($row[0]['county']));
			$gender =  str_replace(array('m','f'), array('Male','Female'), strtolower($row[0]['gender']));
			$ethnicity_dropdown = stripslashes(ucfirst($row[0]['ethnicity_id']));
			$colleges_dropdown = stripslashes(ucfirst($row[0]['college_id']));
			$majors_dropdown = stripslashes(ucfirst($row[0]['majors_id']));
			$high_school = stripslashes(ucfirst($row[0]['highschool']));
			$religious_affilation = stripslashes(ucfirst($row[0]['religious_affiliation']));	
			
			if($country!='') {
				$criteria = '<div class="aligment-row" >
							<div class="aligment-left-div" >County:</div>
							<div class="aligment-right-div">'.$country.'</div>
						</div>';
			}
			if($gender!='') {
				$criteria .='<div class="aligment-row">
								<div class="aligment-left-div">Gender:</div>
								<div class="aligment-right-div">'.$gender.'</div>
							</div>';
			}
			if($ethnicity_dropdown!='') {
				$criteria .='<div class="aligment-row">
								<div class="aligment-left-div">Ethnicity:</div>
								<div class="aligment-right-div">'.$ethnicity_dropdown.'</div>
							</div>';
			}
			if($colleges_dropdown!='') {
				$criteria .='<div class="aligment-row">
								<div class="aligment-left-div">Colleges:</div>
								<div class="aligment-right-div">'.$colleges_dropdown.'</div>
							</div>';
			}
			if($majors_dropdown!='') {
				$criteria .='<div class="aligment-row">
								<div class="aligment-left-div">Majors:</div>
								<div class="aligment-right-div">'.$majors_dropdown.'</div>
							</div>';
			}
			if($high_school!='') {
				$criteria .='<div class="aligment-row">
								<div class="aligment-left-div">High School:</div>
								<div class="aligment-right-div">'.$high_school.'</div>
							</div>';
			}
			if($religious_affilation!='') {
				$criteria .='<div class="aligment-row">
								<div class="aligment-left-div">Religious Affiliation:</div>
								<div class="aligment-right-div">'.$religious_affilation.'</div>
							</div>';
			}
		
		}
		
		break;
	
	
	case "checkamt":
		$display_msg = 0;
		$no_students = 0;
		$status = 'fail';
		$donationAmount = trim($_POST['txt_amount']);
		$formattedAmount = number_format($donationAmount,2);
		//$donationAmount = str_replace(",","",trim($_POST['txt_amount']));
		//$donationAmount = (float)$donationAmount;
		if($_POST['action']=="update_students"){
			if($donationAmount < $_SESSION['sess_donation']['minimum_donation_ammount']) {
				$display_msg = 'InValid Amount Entered.';
			} else {
				$response = $this->getNumberOfScholars($donationAmount);
				$display_msg = 'No. Of Students are '.$response['no_students'].$response['show_sign'];
				$num_students = $response['no_students'].$response['show_sign'];
				$status = 'success';
			}			
		}
		$json = array('amount'=>$donationAmount,'num_students'=>$num_students,'donation_amt'=>$formattedAmount,'num_of_students'=>$response['no_students'],'show_sign'=>$response['show_sign'],'status'=>$status);
		echo json_encode($json);die;
		break;
	
	default:
		if($_POST['action']==$this->mPageName) {
			foreach($_POST as $key=>$value) {
				if(substr($key,0,4)=='txt_') {
					$_SESSION['sess_donation'][$key] = $value;
				}
			}
		}
	break;
}
?>