<?php

$xmlmovie = <<<XML
<?xml version='1.0' standalone='yes'?>
<movies>
 <movie>
  <title>PHP: Behind the Parser</title>
  <characters>
   <character>
    <name>Ms. Coder</name>
    <actor>Onlivia Actora</actor>
   </character>
   <character>
    <name>Mr. Coder</name>
    <actor>El Act&#211;r</actor>
   </character>
  </characters>
  <plot>
   So, this language. It's like, a programming language. Or is it a
   scripting language? All is revealed in this thrilling horror spoof
   of a documentary.
  </plot>
  <great-lines>
   <line>PHP solves all my web problems</line>
  </great-lines>
  <rating type="thumbs">7</rating>
  <rating type="stars">5</rating>
 </movie>
</movies>
XML;


#$movies = new SimpleXMLElement($xmlstr);

#echo $movies->movie[0]->plot;



$xmlstr = <<<XML
<InboundMessage>
  <Id>ca147760-c56e-402e-b147-42c321270801</Id>
  <MessageId>d6031abb-0f66-402a-b153-1f4bb4c41e84</MessageId>
  <AccountId>eba1f9ad-c677-4f36-995c-ffed742cca98</AccountId>
  <MessageText>G</MessageText>
  <From>447506404235</From>
  <To>447860025250</To>
</InboundMessage>
XML;


$messagein = new SimpleXMLElement($xmlstr);

print "messageID: $messagein->Id[0]\n";
print "from: $message_from 	= $messagein->From[0]\n";
print "message_to: $messagein->To[0]\n";
print "message_txt: $messagein->MessageText[0]\n";

?>
