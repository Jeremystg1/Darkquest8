<?php
//https://www.tutorialspoint.com/php/php_sending_emails.htm#:~:text=PHP%20makes%20use%20of%20mail,%2C%20message%2C%20headers%2C%20parameters%20)%3B
         $to = "xyz@somedomain.com";
         $subject = "This is subject";
         
         $message = "<b>This is HTML message.</b>";
         $message .= "<h1>This is headline.</h1>";
         
         $header = "From:contact@darquest8.com \r\n";
         $header .= "Cc:support@darquest8.com \r\n";
         $header .= "MIME-Version: 1.0\r\n";
         $header .= "Content-type: text/html\r\n";
         
         //$retval = mail ($to,$subject,$message,$header);
         
         //if( $retval == true ) {
         //   echo "Message sent successfully...";
         //}else {
         //   echo "Message could not be sent...";
         //}

         function envoyerEmail($to,$sub,$msg){
            global $header;
            $r = mail($to,$sub,$msg,$header);
            return $r;
         }
?>