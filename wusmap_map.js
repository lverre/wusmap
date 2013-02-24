/*
    Copyright 2013 Laurian Verre
	
    This file is part of Wusmap.

    Wusmap is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Wusmap is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Wusmap.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @package Wusmap
 * @version 1.0
 * @author Laurian Verre
 * @description Map Scripts
 */

var showninfo;

function createMap(map_div_id, zoom, center_lat, center_lon, navigation_control, mapType_control, scale_control, map_type_id, route_color, route_opacity, route_weight, points) {
	var map_options = {
		zoom: zoom,
		center: new google.maps.LatLng(center_lat, center_lon),
		navigationControl: navigation_control,
		mapTypeControl: mapType_control,
		scaleControl: scale_control,
		mapTypeId: map_type_id
	};
	var map = new google.maps.Map(document.getElementById(map_div_id), map_options);
	var route = new google.maps.Polyline({
	    path: points,
	    strokeColor: route_color,
	    strokeOpacity: route_opacity,
	    strokeWeight: route_weight,
	    map: map
	});
	return map;
}

function addPoint(points, latitude, longitude) {
	points.push(new google.maps.LatLng(latitude, longitude));
}

function getMarker(map, latitude, longitude, name, title, content) {
    var marker = new google.maps.Marker({
	    position: new google.maps.LatLng(latitude, longitude),
	    title: title,
	    map: map
	});
    var info = new InfoBox(infoOptions);
	
    var boxText = document.createElement("div");
    boxText.className = "wusmap-infobox-content";
	
    var boxTextTitle = document.createElement("div");
    boxTextTitle.className = "wusmap-infobox-title";
    boxTextTitle.innerHTML = name;
    boxText.appendChild(boxTextTitle);
	
    var boxTextDescription = document.createElement("div");
    boxTextDescription.className = "wusmap-infobox-description";
	boxTextDescription.innerHTML = content;
    boxText.appendChild(boxTextDescription);
	
    info.setContent(boxText);
	
    google.maps.event.addListener(marker, 'click', function() {
	    if (showninfo != null) showninfo.close();
	    showninfo = info;
	    info.open(map, marker);
	});
	
    return marker;
}

function addEvent(obj, evType, fn) {
  if (obj.addEventListener){
    obj.addEventListener(evType, fn, false);
    return true;
  } else if (obj.attachEvent) {
    var r = obj.attachEvent('on'+evType, fn);
    return r;
  } else {
    return false;
  }
}

var infoOptions = {
    disableAutoPan: false
    ,boxStyle: { 
	opacity: 0.9
	,width: "300px"
    }
    ,pixelOffset: new google.maps.Size(-120, 0)
    ,enableEventPropagation: true
};
