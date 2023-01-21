<?php

/*******************************************************************
  The function retrieves the new address along with the private key. 
  This Feature can be used to generate automated links 
  for quick payments (https://grlc.eu/pay/?pid=api_code)

  All you need to run this script is hosting with php
  Easy automatic payments without intermediaries :)
  
  Written by: tomiiiii
  Mialto: t0mi[:-)]protonmail.com 
********************************************************************/ 


function new_addrss_grlc ()
{
    $buffer = @file_get_contents("https://explorer.grlc.eu/api.php?op=new_addrss");
    if ($buffer != "")
    {
        $tmp_array = json_decode($buffer, 1);
        if ($tmp_array["grlc_address"] <> "" && $tmp_array["privkey"] <> "")
        {
            return array("0" => $tmp_array["grlc_address"], "1" => $tmp_array["privkey"]); 
        }   
         else
        {
            return 0; 
        } 
    }
     else
    {
        return 0;
    }
}

function generate_payment_address ($payment_address, $amount, $content_displayed_after_purchase)
{
    $buffer = @file_get_contents("https://grlc.eu/pay/?pid=api_get&amount=$amount&addr=".$payment_address."&code=".$content_displayed_after_purchase);
    $tmp_array = json_decode($buffer, 1);
    if ($tmp_array['link_id'] <> "")
    {
        return $tmp_array['link_id'];
    }
     else
    {
        return 0;
    }
}

/******************************************************************
  An example of generating quick payments in GRLC via a www gateway
*******************************************************************/ 

if ($address = new_addrss_grlc())
{

    /******************************************************************
      Download the new address along with the private key 
    *******************************************************************/ 

    echo "Address: ".$address['0'];
    echo "\n<br>\n";
    echo "Privkey: ".$address['1'];
    echo "\n<br>\n";

    /******************************************************************
      Create a directory where you will store addresses and private keys. 
      The directory must have write permission.
    *******************************************************************/ 
    @mkdir("./save_addr/", 0777);

    if ($f = fopen("./save_addr/".$address['0'], "a"))
    {
        fwrite($f, $address['1']);
        fclose($f);  
    }
    @chmod("./save_addr/".$address['0'], 0000); 


    /******************************************************************
      Generate a link to quick payments 
    *******************************************************************/ 
    $amount = 10;
    $content_displayed_after_purchase = 'secret code or url';

    if ($payment_link = generate_payment_address($address['0'], $amount, $content_displayed_after_purchase))
    {
        echo "Payment link: ".$payment_link;
    } 
     else
    {
        echo "Failed to generate payment address"; exit;
    }

}
 else
{
    echo "Oops, an error occurred..."; exit;
}

?>
