<?php

$search = $_REQUEST['s'];
$status = geocode($search);

if ($status == '0'){
 print '0';
 exit();
}

$coord = explode(',',$status);
$lon = $coord[0];
$lat = $coord[1];

$url = 'http://api.local.yahoo.com/LocalSearchService/V2/localSearch';
$appid = 'dunkinlocate';

$q = "?query=".rawurlencode('Dunkin Donuts')
     . "&latitude=" . $lat
     . "&longitude=" . $lon
     . "&results=15&start=1&appid=" . $appid;

$url .= $q;

$xml = simplexml_load_file($url);

$attributes = $xml->attributes();
$res = $xml->Result;

$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml .= "<xml>";

$xml .= create_tag('count',$attributes['totalResultsReturned']);
$xml .= create_tag('lat',$lat);
$xml .= create_tag('lon',$lon);

$results ="
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
<tr>
<td valign='middle'><span class='productheader'><strong><span id='totalRec'>{$attributes['totalResultsReturned']}</span></strong> Locations found</span> </td>
</tr>
</table>
<br />
<table cellpadding='2' cellspacing='0' border='0'>
<tr>
<td valign='top' align='left' class='storelocheader'>&nbsp;</td>
<td valign='top' align='left' class='storelocheader'>&nbsp;</td>
<td valign='top' align='left' class='storelocheader'>Address</td>
<td valign='top' align='left' class='storelocheader'>Phone</td>
<td valign='top' align='left' class='storelocheader'>Distance</td>
</tr>";

foreach($res AS $dd){
  $xml .= '<marker>';
  $xml .= create_tag('address',$dd->Address);
  $xml .= create_tag('city',$dd->City);
  $xml .= create_tag('state',$dd->State);
  $xml .= create_tag('phone',$dd->Phone);
  $xml .= create_tag('latitude',$dd->Latitude);
  $xml .= create_tag('longitude',$dd->Longitude);
  $xml .= create_tag('distance',$dd->Distance);
  $xml .= '</marker>';

  $count ++;
  $class = ($count%2 != 0) ? 'storelocoff' : 'storelocon';
  $results .= "<tr>
               <td valign='top' align='left' class='{$class}'><span class='bodytext'>{$count}</span></td>
               <td valign='top' align='left' class='{$class}'>&nbsp;</td>
               <td valign='top' align='left' class='{$class}'><span class='bodytext'>
               <span id='{$count}__DD' onclick='javascript: showMarker($count)' style='text-decoration:underline;cursor:pointer;'>{$dd->Address} {$dd->City} {$dd->State}</span>
               </span></td>
               <td valign='top' align='left' class='{$class}'><span class='bodytext'>{$dd->Phone}</span></td>
               <td valign='top' align='left' class='{$class}'><span class='bodytext'>{$dd->Distance} mi.</span></td>
               </tr>";
}


$results .= "</table>";

$xml .= create_tag('results',$results);
$xml .= "</xml>";
header("content-type: text/xml");
print $xml;


function geocode($search){
  $api_key = "ABQIAAAAocjzbdv8J7kIcqnxO2pYCBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxTpHBgcNYJL_9n6Mni_R-BhUQR8JA";
  $url = "http://maps.google.com/maps/geo?output=xml&key={$api_key}&q=" . rawurlencode($search);

  $xml = simplexml_load_file($url);

  if (!is_object($xml->Response)) return $search;

  $status = $xml->Response->Status->code;
  if ($status != 200) return 0;

  $coords = strval($xml->Response->Placemark->Point->coordinates);

  return $coords;
}


function create_tag($name, $value, $type = '') {
  if (strlen($type) != 0) {
    $type = " type='{$type}'"; }
  return "<{$name}{$type}><![CDATA[".strip_non_ascii($value)."]]></{$name}>\n";
}


function strip_non_ascii($string) {
  $string = strval($string);
  for ($i=0; $i < strlen($string); $i++) {
    if (ord($string[$i]) >= 32 AND ord($string[$i]) <=126) {
      $result .= $string[$i]; } }
  return $result;
}


?>
