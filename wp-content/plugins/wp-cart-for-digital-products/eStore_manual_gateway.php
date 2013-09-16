<?php
include_once('../../../wp-load.php');

if(isset($_POST['eStore_manaul_gateway']) && $_POST['eStore_manaul_gateway'] == "process")
{
	global $wp_eStore_config;
	$eStore_on_page_manual_checkout_page_url = $wp_eStore_config->getValue('eStore_on_page_manual_checkout_page_url');
	if(empty($eStore_on_page_manual_checkout_page_url))//Use the stand alone manual checkout form
	{
		include_once('eStore_manual_gateway_functions.php');
		eStore_manual_gateway_api();		
	}	
	else //Use the on page manual checkout form
	{
		eStore_redirect_to_url($eStore_on_page_manual_checkout_page_url);
	}
}
else
{
	exit;
}
?>