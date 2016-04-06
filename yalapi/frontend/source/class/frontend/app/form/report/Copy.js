qx.Class.define("frontend.app.form.report.Copy",
{
    extend : frontend.lib.form.Abstract,
    construct: function(params)
    {
        this._url = Urls.resolve('REPORT_TEMPLATES', {id: params.id, copy: 1});
        this.base(arguments);
    },
    members :
    {
        _caption    : 'form.report.copy:window',
        _prefix     : 'form.report.copy',
        _template   :
        {
            project_id: {
                type : "SelectBox",
                properties : {
                    source : 'Projects'
                }
            }
        }
    }
});