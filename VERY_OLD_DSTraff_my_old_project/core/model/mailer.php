<?php
class mailer
{
    
    public function send($who, $from, $subject, $message)
    {
        
        $header = "From: " . $from . " \r\n";
        $header .= "Content-type: text/html; charset=\"utf-8\"";
        $result = mail($who, $subject, $message . "<br /><br /><hr> 
	<span>&copy; 2015 Vizortab</span>
	<br /><span>Sent " . date('F j, Y, D G:i:s T') . "</span>", $header);
        
        
        return $result;
        
        
        
    }
    
}