<?php
$protocol = $_SERVER["SERVER_PROTOCOL"];
if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
$protocol = 'HTTP/1.0';
header( "$protocol 503 Service Unavailable", true, 503 );
header( 'Content-Type: text/html; charset=utf-8' );
?>
<html>
<head>
<title>Maintenance performed by Mach7 Enterprises</title>
</head>

<body>
<h2>Undergoing Scheduled Maintenance</h2>

</body>
</html>
<?php die(); ?>
