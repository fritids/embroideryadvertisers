<?
$headers='From: "Tyson Brooks" <tyson@embroideryadvertisers.com>' . "\r\n" .
        'Reply-to: "Tyson Brooks" <tyson@embroideryadvertisers.com>' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();


mail($useremail,'Your Embroidery Advertisers Login Info','Your Username is: '.$username.' and your password is: '.$userpw,' You can sign in on the front page of the website. http://embroideryadvertisers.com/',$headers);
