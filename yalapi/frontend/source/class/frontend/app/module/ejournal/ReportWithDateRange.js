qx.Class.define("frontend.app.module.ejournal.ReportWithDateRange",
{
    extend : frontend.lib.form.Abstract,

    properties :
    {
        reportId :
        {
            check : "Integer",
            init : null,
            nullable : true,
            event : "changeReportId"
        },

        courseId :
        {
            check : "Integer",
            init : null,
            nullable : true,
            event : "changeCourseId"
        }
    },

    events :
    {
        "changeCourseId" : "qx.event.type.Data",
        "changeReportId" : "qx.event.type.Data"
    },

    construct : function()
    {
        this.base(arguments);

        var form = this.getForm();
        form.setSubmitAfterValidation(false);
        form.getItem("report_format").addListener("changeSelection", this._enableSubmit, this);
        form.getItem("date_from").addListener("changeValue", this._enableSubmit, this);
        form.getItem("date_to").addListener("changeValue", this._enableSubmit, this);

        var prefix = this._prefix;
        this.bind("reportId", this, "caption", {
            converter : function(value) {
                return Tools["tr"](prefix + ":window add:" + value);
            }
        });

        this.addListener("appear", this._enableSubmit, this);
    },

    members :
    {
        _url      : "REPORTS",
        _prefix   : "form.report_picker",
        _template :
        {
            report_format : {
                type : "RadioGroup",
                properties : {
                    orientation : "horizontal",
                    source : [
                        { id : "pdf", label : "PDF" },
                        { id : "xls", label : "MS Excel" }
                    ]
                }
            },
            date_from : {
                type : "DateField",
                properties : {
                    width : 150,
                    allowStretchX : false
                }
            },
            date_to : {
                type : "DateField",
                properties : {
                    width : 150,
                    allowStretchX : false
                }
            }
        },

        _enableSubmit : function ()
        {
            var submit = this.getForm().getSubmitButton();
            submit.setEnabled(true);
            submit.setShow("both");
            submit.setLabel(Tools["tr"](this._prefix + ":button add"));
        },

        _onComplete : function(e)
        {
            var submit = this.getForm().getSubmitButton();
            submit.setEnabled(false);
            submit.setShow("label");
            submit.setLabel(Tools["tr"](this._prefix + ":generating_report"));

            var df = new frontend.lib.util.format.DateFormat("dd-MM-yyyy");
            var params = this.getForm().getValues();
            params["id"] = this.getReportId();
            params["course_id"] = this.getCourseId();
            if (params["date_from"]) {
                params["date_from"] = df.format(params["date_from"], "yyyy-MM-dd");
            } else {
                params["date_from"] = "1970-01-01";
            }
            if (params["date_to"]) {
                params["date_to"] = df.format(params["date_to"], "yyyy-MM-dd");
            } else {
                params["date_to"] = "2099-12-31";
            }

            window.location.href = Urls.resolve("REPORTS", params);
        }
    }
});