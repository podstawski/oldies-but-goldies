qx.Class.define("frontend.app.module.report.ReportPicker",
{
    extend : frontend.lib.form.Abstract,
    
    construct: function(params, exactMatch)
    {
        this._template.name.properties.source = this._transformTemplates(params ? this._generator.findPossibleTemplates(params, exactMatch) : this._generator._config.templates);
        this.base(arguments);
        var submit = this._form.getSubmitButton(),
            templateNameInput = this._form.getItem('name'),
            templateFormatInput = this._form.getItem('report_format');

        templateNameInput.addListener('changeSelection', this._enableSubmit, this);
        templateFormatInput.addListener('changeSelection', this._enableSubmit, this);

        submit.removeListener("execute", this._form.send, this._form);
        submit.addListener('execute', function() {
            submit.setLabel(Tools.tr('form.report_picker:generating_report'));
            submit.setEnabled(false);
            var templateID = templateNameInput.getSelection()[0].getModel(),
                report_format = templateFormatInput.getSelection()[0].getModel();

            if (!params) {
                params = {};
                params['preview'] = '';
            }

            params['id'] = templateID;
            params['report_format'] = report_format;
            window.location.href = Urls.resolve("REPORTS", params);
        }, this);
    },
    statics: {
        openForm: function(params, exactMatch) {
            var form = new frontend.app.module.report.ReportPicker(params, exactMatch);
            form.open();
        }
    },
    members :
    {
        _generator  : frontend.app.module.report.PossibleReportsGenerator,
        _url        : 'REPORTS',
        _prefix     : 'form.report_picker',
        _template   :
        {
            name : {
                type : "SelectBox",
                properties : {
                    source : null
                }
            },
            report_format: {
                type: "RadioGroup",
                properties: {
                    orientation: 'horizontal',
                    source: [
                        {id: 'pdf', label: 'PDF'},
                        {id: 'xls', label: 'MS Excel'}/*,
                        {id: 'doc', label: 'MS Word'}*/
                    ]
                }
            }
        },
        _transformTemplates: function(templates) {
            var transformed = [];
            for (var key in templates) {
                transformed.push({id: key, label: Tools['tr']('reportPicker.template.' + templates[key])});
            }
            return transformed;
        },
        _enableSubmit : function() {
            var button = this._form.getSubmitButton();
            button.setEnabled(true);
            button.setLabel(Tools.tr('form.report_picker:button add'));
        }
    }

});