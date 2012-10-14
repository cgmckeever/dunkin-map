<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title id="Header_HtmlTitle">Find a Dunkin' Donuts Store Location Near You</title>

<style>
.storelocheader { background-color:#666666; font-size:10px; font-weight:bold; color:#ffffff; }
/* ns */ .storelocon { background-color:#f0f0f0; }
/* ns */ .storelocoff { background-color:#ffffff; }
</style>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAhfhbuVR7rFf3fcsHGd_vVBR7MHHc7buk8cF7XoAc-SWQMy8DLRQRTi7d1-BGfpSkhlehtUar8z1lFQ"
      type="text/javascript"></script>
<script type="text/javascript">

    //<![CDATA[

var map;
var overlays = new Array();

function load() {
  if (GBrowserIsCompatible()) {
    map = new GMap2(document.getElementById("map"));
    map.setCenter(new GLatLng(37.4419, -122.1419), 13);
    map.addControl(new GSmallMapControl());
  }
}

function sendRequest(search){

  GDownloadUrl("request.php?s=" + search, function(data, responseCode) {
  if (data == '0'){
   alert('No Matches Found');
   return;
  }
  var xml = GXml.parse(data);
  var results = GXml.value(xml.documentElement.getElementsByTagName("results")[0]);
  var count = GXml.value(xml.documentElement.getElementsByTagName("count")[0]);
  var lat = parseFloat(GXml.value(xml.documentElement.getElementsByTagName("lat")[0]));
  var lon = parseFloat(GXml.value(xml.documentElement.getElementsByTagName("lon")[0]));
  map.setCenter(new GLatLng(lat,lon));

  var markers = xml.documentElement.getElementsByTagName("marker");
  overlays = new Array();
  for (var i = 0; i < markers.length; i++) {
    var address = (GXml.value(markers[i].getElementsByTagName("address")[0]));
    var city = (GXml.value(markers[i].getElementsByTagName("city")[0]));
    var state = (GXml.value(markers[i].getElementsByTagName("state")[0]));
    var phone = (GXml.value(markers[i].getElementsByTagName("phone")[0]));
    var latitude = parseFloat(GXml.value(markers[i].getElementsByTagName("latitude")[0]));
    var longitude = parseFloat(GXml.value(markers[i].getElementsByTagName("longitude")[0]));
    var distance = parseFloat(GXml.value(markers[i].getElementsByTagName("distance")[0]));
    var point = new GLatLng(latitude,longitude);
    var t1 = "<table><tr><td align=left>" + address + "<br>" + city + ", " + state + "<br>" + phone + "</td></tr></table>";
    var t2 = '<br>This could be hours, info about Baskin Robins, etc.';
    map.addOverlay(createMarker(point, t1 , t2, i + 1));
  }

  _gel('results').innerHTML = results;

  });
}


// Creates a marker at the given point with the given number label
function createMarker(point, t1, t2, number) {

  var marker = new GMarker(point);
  marker.number = number;
  marker.infoTabs = [
      new GInfoWindowTab("D+D", t1),
      new GInfoWindowTab("Store Info.", t2)
  ];

  GEvent.addListener(marker, "click", function() {
        _gel(marker.number + "__DD").style.fontWeight = "bold";
        marker.openInfoWindowTabsHtml(marker.infoTabs); });

  GEvent.addListener(marker, "infowindowclose", function() {
       _gel(marker.number + "__DD").style.fontWeight = ""; });

  overlays.push(marker);
  return marker;

}

function showMarker(number){
  for (i=0; i < overlays.length; i++) {
    if (overlays[i].number == number) {
      found = true;
      marker = overlays[i];
    }
  }
  marker.openInfoWindowTabsHtml(marker.infoTabs);
  _gel(number + "__DD").style.fontWeight = "bold";

}

function getObjMethodClosure(object, method) {
  // shorthand object reference
  return function(arg) {
    return object[method](arg);
  }
}

var _gel = getObjMethodClosure(document, "getElementById");

    //]]>
</script>

</head>
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" style="font-size:8pt" onload="load()" onunload="GUnload()">

            View the map and listings below to find the most convenient Dunkin' Donuts location.<br />

            <br />

            <form onsubmit="javascript: sendRequest(_gel('search').value);return false;">
            <table><tr>
            <td><input type=text id='search' size=50 /></td><td><input type=submit value='Search' /></td>
            </tr>
            <tr>
            <td colspan=2>"Chicago, IL", "1600 Amphitheatre Parkway Mountain View, CA ", "10566"</td></td>
            </tr>
            </table>
            </form><br />

            <br /><div id="map" style="width: 400px; height: 250px"></div>
            <br />

                <div id=results></div>

              <br />
              <br />


</body>
</html>
