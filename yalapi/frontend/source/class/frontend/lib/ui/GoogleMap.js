/*
#ignore(google.maps)
#ignore(google.maps.Map)
#ignore(google.maps.MapTypeId)
#ignore(google.maps.Marker)
#ignore(google.maps.event)
 */

qx.Class.define("frontend.lib.ui.GoogleMap",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        placeData :
        {
            check : "Map",
            init : null,
            apply : "_applyPlaceData"
        },

        latLng :
        {
            check : "Object",
            init : null,
            apply : "_applyLatLng"
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.VBox);
        this.setMinWidth(300);
        this.setMinHeight(300);
        this.add(this.getChildControl("google-map"), {flex:1});
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "google-map":
                    control = new qx.ui.core.Widget();
                    break;
            }

            return control || this.base(arguments, id);
        },
        
        _applyPlaceData : function(data, old)
        {
            var $this = this;
            new google.maps.Geocoder().geocode({
                address : qx.lang.String.format("%1, %2", [data.street, data.city])
            }, function(data, status){
                if (status === "OK") {
                    $this.setLatLng(data[0].geometry.location);
                }
            });
        },

        _applyLatLng : function(data, old)
        {
            var googleMap = this.getChildControl("google-map");
            if (!googleMap.isSeeable()) {
                googleMap.addListenerOnce("appear", function(e){
                    this._loadMap();
                }, this);
                return;
            }

            this._loadMap();
        },

        _loadMap : function()
        {
            var mapControl = this.getChildControl("google-map");
            var googleMap  = mapControl.getContentElement().getDomElement();
            var latLng     = this.getLatLng();
            var placeData  = this.getPlaceData();
            
            var map = new google.maps.Map(googleMap, {
                zoom : 15,
                center : latLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            google.maps.event.addListenerOnce(map, "center_changed", function() {
                window.setTimeout(function() {
                    var zIndex = mapControl.getContentElement().getStyle('zIndex');
                    mapControl.getContentElement().getDomElement().style.zIndex = zIndex;
                }, 500);
            });

            if (placeData) {
                var marker = new google.maps.Marker({
                    position : latLng,
                    map : map,
                    title : qx.lang.String.format("%1, %2, %3", [ placeData.name, placeData.street, placeData.city ])
                });
            }
        }
    }
});