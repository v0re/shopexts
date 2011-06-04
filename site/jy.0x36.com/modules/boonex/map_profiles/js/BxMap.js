
function BxMap (map, sGetDataUrl, sInstanceName, sElementId) {
    this._map = map;
    this._sInstanceName = sInstanceName;
    this._sGetDataUrl = sGetDataUrl;
    this._sSaveDataUrl = false;
    this._sSaveLocationUrl = false;
    this._sShadowUrl = false;
    this._sElementId = sElementId;
    this._iPage = 1;
    this._sIdProfilesList = '';
    this._oMarkers = new Object();
}

BxMap.prototype.updateLocations = function () {

    this.onStartLoadProfilesMap ();

    var zoom = this._map.getZoom();
    var center = this._map.getCenter();
	var bounds = this._map.getBounds();
	var southWest = bounds.getSouthWest();
	var northEast = bounds.getNorthEast();    
	var span = bounds.toSpan();
	var isFull = span.lng() > 277 || zoom < 3;
    var $this = this;		

    var sUrl = this._sGetDataUrl;
    sUrl = sUrl.replace('{zoom}', zoom);
    sUrl = sUrl.replace('{instance}', this._sInstanceName);
    sUrl = sUrl.replace('{lat_min}', southWest.lat());
    sUrl = sUrl.replace('{lat_max}', northEast.lat());
    sUrl = sUrl.replace('{lng_min}', isFull ? -180 : southWest.lng());
    sUrl = sUrl.replace('{lng_max}', isFull ?  180 : northEast.lng());
    sUrl = sUrl.replace('{lat_center}', center.lat());
    sUrl = sUrl.replace('{lng_center}', center.lng());
    sUrl = sUrl.replace('{page}', this._iPage);
    sUrl = sUrl.replace('{ts}', (new Date()).getTime());

    this.loading(1);
	$.getJSON(sUrl, function(data) {        

        $this._oMarkers = null;
        $this._oMarkers = new Object();
        $this._map.clearOverlays();

        var l = data.length;

        for (var i=0 ; i < l ; ++i ) {

            var o = data[i];

            var fLat = parseFloat(o.lat);
            var fLng = parseFloat(o.lng);

            var point = new GLatLng(fLat, fLng);
            var marker = $this.createMarker(point, o.data, o.icon.url, {w:o.icon.w, h:o.icon.h, sw:o.icon.sw, sh:o.icon.sh, sd:o.icon.sd});
            $this._map.addOverlay(marker);

            if (o.usernames === undefined)
                continue;
            var ll = o.usernames.length;
            for (var ii=0 ; ii < ll ; ++ii )                 
                $this._oMarkers[o.usernames[ii]] = marker;

         }

        $this.onLoadProfilesMap ();
        $this.loading(0);
	});
}


BxMap.prototype.loading = function (b) {

    if (!this._sElementId) 
        return;

    bx_map_loading (this._sElementId, b);
}

BxMap.prototype.createMarker = function (point, html, image, options) {  

    var marker;

    if (image) {
        var icon = new GIcon();
        icon.image = image;
        if (options.sw > 0)
           icon.shadow = (this._sShadowUrl && options.sd && 1 == parseInt(options.sd) ? this._sShadowUrl : image.replace('.png', '_shadow.png'));
        icon.iconSize = new GSize(options.w, options.h);
        if (options.sw > 0)
            icon.mapshadowSize = new GSize(options.sw, options.sh);
        icon.iconAnchor = new GPoint(options.w/2, options.h/2);
        icon.infoWindowAnchor = new GPoint(options.w/2, 0);
        marker = new GMarker(point, icon);
    } else {
    	marker = new GMarker(point);
    }

	if (html && html.length) {        
	    GEvent.addListener(marker, "click", function() {
                marker.openInfoWindowHtml(html, {maxWidth:300});
        });
    }

    return marker;
}

BxMap.prototype.magnify = function (fLat, fLng, iZoom) {
    this._map.setCenter(new GLatLng(fLat, fLng), iZoom);
}

BxMap.prototype.setShadowUrl = function (sUrl) {
    this._sShadowUrl = sUrl;
}

BxMap.prototype.setSaveLocationUrl = function (sUrl) {
    this._sSaveLocationUrl = sUrl;
}

BxMap.prototype.setSaveDataUrl = function (sUrl) {

    this._sSaveDataUrl = sUrl;

    if (false == sUrl || '' == sUrl) {

		GEvent.clearListeners(this._map, "click");
		GEvent.clearListeners(this._map, "zoomend");
		GEvent.clearListeners(this._map, "maptypechanged");

    } else {

    	var $this = this;

		var hh = function(marker, point) {			                        
			var sMapType = 'normal';
			switch ($this._map.getCurrentMapType())
			{
				case G_SATELLITE_MAP: sMapType = 'satellite'; break;
				case G_HYBRID_MAP: sMapType = 'hybrid'; break;
			};
            $this.saveData ('null', 'null', $this._map.getZoom(), sMapType);
		};

		var h = function(marker, point) {            
            if (!point) return;                                    
            $this.saveData (point.lat(), point.lng(), 'null', 'null');
		    $this._map.clearOverlays();
			$this._map.addOverlay($this.createMarker(point));
		};

		GEvent.addListener(this._map, "click", h);
		GEvent.addListener(this._map, "zoomend", hh);
		GEvent.addListener(this._map, "maptypechanged", hh);
    }
}

BxMap.prototype.saveData = function (fLat, fLng, iZoom, sMapType) { 

    var sUrl = this._sSaveDataUrl;
    sUrl = sUrl.replace('{zoom}', iZoom);
    sUrl = sUrl.replace('{map_type}', sMapType);
    sUrl = sUrl.replace('{lat}', fLat);
    sUrl = sUrl.replace('{lng}', fLng);
    sUrl = sUrl.replace('{instance}', this._sInstanceName);
    sUrl = sUrl.replace('{ts}', (new Date()).getTime());

    if ('null' == fLat || 'null' == fLng) {

        sUrl = sUrl.replace('{address}', 'null');
        sUrl = sUrl.replace('{country}', 'null');
        this.loading(1);
        $.get(sUrl, function(responseText) {
            $this.loading(0);
        }); 

    } else {

        var geocoder = new GClientGeocoder();
        var $this = this;    
        this.loading(1);    
        geocoder.getLocations(new GLatLng(fLat, fLng), function (response) {

            var sAddress = 'null';
            var sCountry = 'null';

            if (response && response.Status.code == 200) {
                for (var i in response.Placemark) {
                    var place = response.Placemark[i];
                    var accuracy = parseInt(place.AddressDetails.Accuracy);
                    if (accuracy < 4) {
                        sAddress = place.address;
                        sCountry = place.AddressDetails.Country.CountryNameCode;
                        break; 
                    }
                }
            }

            sUrl = sUrl.replace('{address}', sAddress);
            sUrl = sUrl.replace('{country}', sCountry);
            $.get(sUrl, function(responseText) {
                $this.onUpdateAddress (sAddress, sCountry);
                $this.loading(0);
            });
        
        });

    }
}

BxMap.prototype.saveLocation = function () {

    if (!this._sSaveLocationUrl || '' == this._sSaveLocationUrl)
        return false;

    var sUrl = this._sSaveLocationUrl;
    var sMapType = 'normal';
	switch (this._map.getCurrentMapType())	{
	    case G_SATELLITE_MAP: sMapType = 'satellite'; break;
		case G_HYBRID_MAP: sMapType = 'hybrid'; break;
	};        
    sUrl = sUrl.replace('{zoom}', this._map.getZoom());
    sUrl = sUrl.replace('{map_type}', sMapType);
    sUrl = sUrl.replace('{lat}', this._map.getCenter().lat());
    sUrl = sUrl.replace('{lng}', this._map.getCenter().lng());
    sUrl = sUrl.replace('{instance}', this._sInstanceName);
    sUrl = sUrl.replace('{ts}', (new Date()).getTime());

    this.loading(1);
    var $this = this;    
        $.get(sUrl, function(responseText) {
            $this.loading(0);
            if ('ok' == responseText)
                alert ('Location has been saved');
            else
                alert ('Location saving failed');
        });
    return false;
}

BxMap.prototype.changePage = function (sIdProfiles, sUrl, iChange) {

    if (1 == this._iPage && iChange < 0) {
        return;
    }

    if ((this._iPage + iChange) <= 1) {
        $('#'+sIdProfiles).parents('.pageable_items').find('.paginate_btn:first').addClass('notactive');
    } else {
        $('#'+sIdProfiles).parents('.pageable_items').find('.paginate_btn:first').removeClass('notactive');
    }

    if (this._isProfilesMapLoaded && iChange > 0 && 0 == $('#'+sIdProfiles+' .searchrow_block_simple').size()) {        
        return;
    }

    this._iPage += iChange;

    this.updateLocations();
}

BxMap.prototype.loadProfilesList = function (sIdProfiles, sUrl) {
    this.setProfilesListId(sIdProfiles);
    this.onStartLoadProfilesList ();
    sUrl = sUrl.replace('{page}', this._iPage);
    sUrl = sUrl.replace('{ts}', (new Date()).getTime());    
    var $this = this;
    getHtmlData(sIdProfiles, sUrl, function () {
        $this.onLoadProfilesList ();
    });
}

BxMap.prototype.attachProfilesListToMap = function (sIdProfiles) {
    var $this = this;
    $('#'+sIdProfiles+' .searchrow_block_simple .thumbnail_image img').bind("click", function(e) {
        var s = $(this).parents('.searchrow_block_simple').find('.thumb_username a').html();
        if (undefined !== $this._oMarkers[s])
            GEvent.trigger($this._oMarkers[s], "click");            
        e.preventDefault();
        e.stopPropagation();
    });
}

BxMap.prototype.onStartLoadProfilesList = function () {
    this._isProfilesListLoaded = 0;
}

BxMap.prototype.onLoadProfilesList = function () {
    this._isProfilesListLoaded = 1;
    if (1 == this._isProfilesListLoaded && 1 == this._isProfilesMapLoaded) {
        this.attachProfilesListToMap (this._sIdProfilesList);
    }
    var sIdProfiles = this._sIdProfilesList;
    if (0 == $('#'+sIdProfiles+' .searchrow_block_simple').size()) {        
        $('#'+sIdProfiles).parents('.pageable_items').find('.paginate_btn:last').addClass('notactive');
        return;
    } else {
        $('#'+sIdProfiles).parents('.pageable_items').find('.paginate_btn:last').removeClass('notactive');
    }    
}


BxMap.prototype.onStartLoadProfilesMap = function () {
    this._isProfilesMapLoaded = 0;
    if (window.glBxMapProfilesMapOnLoadCallback === undefined) 
        return;
    glBxMapProfilesMapOnLoadCallback();
}

BxMap.prototype.onLoadProfilesMap = function () {
    this._isProfilesMapLoaded = 1;
    if (1 == this._isProfilesListLoaded && 1 == this._isProfilesMapLoaded) {
        this.attachProfilesListToMap (this._sIdProfilesList);
    }    
}

BxMap.prototype.onUpdateAddress = function (sAddress, sCountry) {
    $('#bx_map_curr_loc').html (sAddress); 
}

BxMap.prototype.setProfilesListId = function (s) {
    this._sIdProfilesList = s;
}

function bx_map_loading (sId, b) {
    bx_loading(sId, b);
}
