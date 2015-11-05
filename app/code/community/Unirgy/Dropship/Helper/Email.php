<?php
class Unirgy_Dropship_Helper_Email extends Mage_Core_Helper_Abstract {

    function emailWithAttachment($to, $pdf) {
        $sendermail = 'no-reply@bloomnation.com';
        
        // email fields: to, from, subject, and so on
        $from = "$sendermail"; 
        $subject = "Fax"; 
        $message = " ";
        $headers = "From: $from";
        $filename = 'attachment-order.pdf';
        
        // boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
     
        // headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
     
        // multipart boundary 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
     
        // preparing attachments
        $message .= "--{$mime_boundary}\n";
        $data =    $pdf->render();
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: application/pdf; name=\"".$filename."\"\n" . 
        "Content-Description: ".$filename."\n" .
        "Content-Disposition: attachment;\n" . " filename=\"".$filename."\"; \n" . 
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                
        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $sendermail;
        $ok = @mail($to, $subject, $message, $headers, $returnpath);         
    }
    
}
