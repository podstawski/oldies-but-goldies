qx.Mixin.define("frontend.lib.util.MGetSource",
{
    members :
    {
        __cachedSourceInstances : {},

        getSourceInstance : function(name)
        {
            if (this.__cachedSourceInstances[name] === undefined) {
                this.__cachedSourceInstances[name] = frontend.app.source[name].getInstance();
            }

            return this.__cachedSourceInstances[name];
        }
    }
});