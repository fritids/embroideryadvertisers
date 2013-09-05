<?php
/*
Template Name: Test Page
*/
get_header();
?><?php
// Example using the array form of $headers
// assumes $to, $subject, $message have already been defined earlier...

$headers[] = 'From: tyson@embroideryadvertisers.com';
//$headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
//$headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address

wp_mail( 'tyson@tysonbrooks.net', $subject, $message );
?>