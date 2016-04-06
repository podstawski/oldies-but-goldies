qx.Class.define("Acl",
{
    type : "static",
    
    statics :
    {
        __isReady : false,

        __allowedResources : null,
        
        init : function()
        {
            this.reset();
            
            var request = new frontend.lib.io.HttpRequest;
            request.setUrl(Urls.resolve("ACL"));
            request.addListenerOnce("success", function(e){
                var response = request.getResponseJson();

                window.yala = {};
                window.yala.googleapps = response['googleapps'];

                var aclMap = response['acl'];
                qx.lang.Object.getKeys(aclMap).forEach(function(resourceName){
                    qx.lang.String.toArray(aclMap[resourceName]).forEach(function(resourceRight){
                        var resourceID = resourceName + "." + resourceRight;
                        this.__allowedResources.push(resourceID);
                    }, this);
                }, this);

                this.__isReady = true;
            }, this);
            request.send();
        },

        reset : function()
        {
            this.__isReady = false;
            this.__allowedResources = new qx.data.Array;
        },

        isReady : function()
        {
            return this.__isReady;
        },

        hasRight : function(resourceID)
        {
            return resourceID == null || this.__allowedResources.contains(resourceID);
        }
    }
});