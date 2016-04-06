qx.Class.define("frontend.app.form.report.Form",
{
    extend : frontend.lib.form.Abstract,

    construct: function(rowData)
    {
        delete rowData.path;
        this.base(arguments, rowData);
    },

    members :
    {
        _caption    : 'form.report.edit:window',
        _prefix     : 'form.report.edit',
        _url        : 'REPORT_TEMPLATES',
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
                   Validate.slength(2, 256)
               ]
            },
            description : {
                type : "TextArea",
                properties : {
                    autoSize : true
                }
            },
            template_file : {
                type: "File"
            }
        }
    }
});