qx.Class.define("frontend.lib.util.Template",
{
    extend : qx.core.Object,

    properties :
    {
        content :
        {
            check : "String",
            nullable : true,
            apply : "_applyContent"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.run = qx.lang.Function.bind(this._noContent, this);
    },

    members :
    {
        _applyContent : function(value, old)
        {
            if (value) {
                this.run = qx.lang.Function.bind(this._format, this);
            } else {
                this.run = qx.lang.Function.bind(this._noContent, this);
            }
        },

        _noContent : function()
        {
            throw new Error("Please define any content first!");
        },

        _format : function(map)
        {
            var content = this.getContent();
            qx.lang.Object.getKeys(map).forEach(function(key){
                var value = map[key];
                content = content.replace(new RegExp("{" + key + "}", "g"), value)
                                 .replace(new RegExp("{tr:" + key + "}", "g"), Tools["tr"](key));
            });
            return content;
        }
    }
});