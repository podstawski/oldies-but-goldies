qx.Class.define("frontend.app.form.ResourceType",
{
    extend : frontend.lib.form.Abstract,

    members :
    {
        _url        : Urls.resolve("RESOURCE_TYPES"),
        _prefix     : "form.resource_type",
        _caption    : "form.resource_type.window",
        _template   :
        {
            name : {
                type : "TextField",
                properties : {
                    required : true,
                    maxLength : 64
                },
                validators : [
                    Validate.string(),
                    Validate.slength(1, 256)
                ]
            }
        }
    }
});