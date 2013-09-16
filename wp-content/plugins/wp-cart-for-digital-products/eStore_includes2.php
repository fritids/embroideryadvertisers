<?php
include_once('eStore_post_payment_processing_helper.php');

function eStore_send_free_download1($name, $to_email_address, $download)
{
	if(WP_ESTORE_DO_NOT_SEND_EMAIL_FROM_SQUEEZE_FORM==='1'){//Don't send the email for the squeeze form submission
		return true;
	}
	$attachment = '';
	$from_email_address = get_option('eStore_download_email_address');
	$headers = 'From: '.$from_email_address . "\r\n";
	$email_subj = ESTORE_FREE_DOWNLOAD_SUBJECT;
	$email_body = ESTORE_DEAR.' '.$name.
				  "\n\n".ESTORE_FREE_DOWNLOAD_EMAIL_BODY.
 				  "\n".$download.
				  "\n\n".ESTORE_THANK_YOU;
    if (get_option('eStore_use_wp_mail'))
    {
        wp_mail($to_email_address, $email_subj, $email_body, $headers);
        return true;
    }
    else
    {
	    if(@eStore_send_mail($to_email_address,$email_body,$email_subj,$from_email_address,$attachment))
	    {
	    	return true;
	    }
	    else
	    {
	    	return false;
	    }
    }
}

function free_download_pseudo_payment_data($cust_name, $cust_email)
{
	// This function returns pseudo payment_data that can be passed to the PDF Stamper addon.  It is called by both the Ajax
	$unique_id = "Free-Download-".uniqid();
	list($firstname,$lastname) = explode(' ',$cust_name);
	$payment_data = array(
		'customer_name' => $cust_name,
		'payer_email' => $cust_email,
		'first_name' => $firstname,
		'last_name' => $lastname,
		'contact_phone' => 'N/A or Not Provided',
		'address' => $cust_email,
		'payer_business_name' => $cust_name,
		'txn_id' => $unique_id,
	);
	return $payment_data;
}
