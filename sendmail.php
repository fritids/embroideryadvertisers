<?

if (isset($_POST['to'])) { $to=$_POST['to']; } else { $to='tyson@embroideryadvertisers.com';}
if (isset($_POST['sender_name'])) {$name=$_POST['sender_name'];}
if (isset($_POST['sender_email'])) {$from=$_POST['sender_email'];}
if (isset($_POST['subject'])) {$subject='Embroidery Advertisers - '.$_POST['subject'];}
if (isset($_POST['phone'])) {$phone=$_POST['phone'];}
if (isset($_POST['message'])) {$message=$_POST['message'];}

$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$header .= 'Reply-To: '.$name.' <'.$from.'>';

mail($to,$subject, 'A customer has tried to contact you via Embroidery Advertisers.<br>Here is their message:<br><br>'.$message.'<br><br>Here is the customers information:<br>Customer Name: '.$name.'<br>Customer Email: '.$from.'<br>Customer Phone Number: '.$phone.'<br><br>You may also reply to this email to reach them.', $header);



//$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';


//if ($_POST['to']==NULL) { header("Location:".$refering_url."?message=Your message was received successfully."); } else { header("Location:".$refering_url."&message=Your message was received successfully."); }

?>