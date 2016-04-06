qx.Class.define("frontend.lib.io.RequestManager",
{
    extend : qx.core.Object,

    construct : function()
    {
        this.base(arguments);
    },

    members :
    {
        __onEndCallback     : null,
        __context           : null,
        __requestsCounter   : 0,
        __requestsLoaded    : 0,

        _generateUrl : function( dataObj )
        {
            if (typeof dataObj.url !== "undefined") {
                return dataObj.url;
            }
            
            return "/index.php/" + dataObj.modelName + (
                dataObj.id === undefined ? "" : "/" + dataObj.id
            );
        },

        deleteData : function( dataObj )
        {
            var request = new frontend.lib.io.HttpRequest( dataObj.url, 'DELETE' );
            request.addListener("success", function( e ) {
                this.__context && dataObj.callback && dataObj.callback.apply( this.__context, null );
            }, this );
            request.send();
        },

        updateData : function( dataObj, values, context )
        {
            var request = new frontend.lib.io.HttpRequest( this._generateUrl( dataObj ), 'PUT' );
            request.setRequestData( values );
            request.addListener("success", function( e ) {
                context && dataObj.modelCallback && dataObj.modelCallback.apply( context, null );
            }, this );
            request.send();
        },
		
        setData   : function( dataObj, values, context )
		{
            var request = new frontend.lib.io.HttpRequest( this._generateUrl( dataObj ), 'POST' );
            request.setRequestData( values );
            request.addListener("success", function( e ) {
                context && dataObj.modelCallback && dataObj.modelCallback.apply( context, null );
            }, this );
            request.send();
		},

        _getDataCallback : function( dataObj )
        {
            var response = null;
            var request = new frontend.lib.io.HttpRequest( this._generateUrl( dataObj ), 'GET' );
            request.addListener("success", function( e ) {
                this.__requestsLoaded += 1;

                response =  [request.getResponseJson()];
                response.prototype = qx.data.Array;

                this.__context && dataObj.modelCallback && dataObj.modelCallback.apply( this.__context, response) ;
                this._allRequestsLoaded();
            }, this );
            request.send();
        },

        getData : function( dataObjects, context )
        {
            this.__onEndCallback = dataObjects.onEnd;
            this.__context = context;

            if(typeof context == "undefined") { }

            if(!qx.lang.Type.isArray(dataObjects.data))
            {
                this.__requestsCounter = qx.lang.Object.getKeys( dataObjects.data ).length;
                for(var i in dataObjects.data)
                { if(typeof dataObjects.data[i] == "object") { this._getDataCallback(dataObjects.data[i]) } }
            }
            else
            {
                this.__requestsCounter = dataObjects.data.length;
                dataObjects.data.prototype = qx.data.Array;
                dataObjects.data.forEach( this._getDataCallback, this );
            }
        },

        _allRequestsLoaded : function()
        {
            if( this.__requestsCounter == this.__requestsLoaded )
            {
                typeof this.__onEndCallback == "undefined" ?
                    this._defaultOnEndCallback() : this.__onEndCallback.apply( this.__context, null );

                this.__requestsCounter = 0;
                this.__requestsLoaded = 0;
            }
        },
        
        _defaultOnEndCallback : function()
        {
        }

    }
});