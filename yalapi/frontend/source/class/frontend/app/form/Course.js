qx.Class.define("frontend.app.form.Course",
{
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData)
    {
        this.base(arguments);
        var statusBox = this.getChildControl('form-general').getItem('status');

        //RB set status to planned - second element in source Statuses;
        statusBox.setSelection([statusBox.getSelectables()[1]]);

        this.setCaption(Tools["tr"]("form.course:window " + (rowData ? "edit" : "add")));
        this.setLayout(new qx.ui.layout.VBox(10));
        this.setResizable(true);

        this._rowData = rowData;

        this.add(this.getChildControl("tabview"), {flex:1});
        this.add(this.getChildControl("buttons"));

        this._forms = ["general", "description", "course-units"];

        this._loadData();
    },

    members :
    {
        _template :
        {
            name : {
                type : "TextField",
                properties : {
                    required : true,
                    width : 200,
                    allowStretchX : false
                },
                row : 0,
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            code : {
                type : "TextField",
                properties : {
                    required : true,
                    width : 50,
                    allowStretchX : false
                },
                row : 0,
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            color : {
                type : "ColorPicker",
                properties : {
                    position : "bottom-right"
                },
                row : 0
            },
            training_center_id : {
                type : "ComboTable",
                properties : {
                    placeholder : "wyszukaj ośrodek",
                    required : true,
                    width : 200,
                    dataColumn : function(rowData) {
                        return qx.lang.String.format("%1 (%2, %3 %4)", [rowData.name, rowData.street, rowData.zip_code, rowData.city]);
                    },
                    dataUrl : Urls.resolve("TRAINING_CENTERS")
                },
                colSpan : 5
            },
            group_id : {
                type : "ComboTable",
                properties : {
                    placeholder : "Wyszukaj grupę",
                    required : false,
                    width : 200,
                    dataColumn : 'name',
                    dataUrl : Urls.resolve("GROUPS")
                },
                colSpan : 5
            },
            price : {
                type : "Spinner",
                properties : {
                    maximum : Number.MAX_VALUE,
                    singleStep : 50,
                    width : 100,
                    allowStretchX : false
                },
                colSpan : 1
            },
            level : {
                type : "SelectBox",
                properties : {
                    source : "Levels",
                    width : 200,
                    allowStretchX : false
                },
                colSpan : 5
            },
            project_id : {
                type : "SelectBox",
                properties : {
                    source : "Projects",
                    width : 200,
                    allowStretchX : false
                },
                colSpan : 5
            },
            status : {
                type : "SelectBox",
                properties : {
                    source : "Statuses",
                    width: 200,
                    allowStretchX: false
                },
                colSpan : 5
            },
            show_on_www : {
                type : "CheckBox"
            }
        },
        
        _rowData : null,

        _forms : null,

        _request : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "tabview":
                    control = new qx.ui.tabview.TabView;
                    control.add(this.getChildControl("tab-general"));
                    control.add(this.getChildControl("tab-description"));
                    control.add(this.getChildControl("tab-course-units"));
                    break;
                
                case "tab-general":
                    control = new qx.ui.tabview.Page().set({
                        label  : Tools.tr("form.course.tab:general"),
                        layout : new qx.ui.layout.VBox
                    });
                    control.add(this.getChildControl("form-general"));
                    break;

                case "tab-description":
                    control = new qx.ui.tabview.Page().set({
                        label  : Tools.tr("form.course.tab:description"),
                        layout : new qx.ui.layout.VBox
                    });
                    control.add(this.getChildControl("form-description"));
                    break;

                case "tab-course-units":
                    control = new qx.ui.tabview.Page().set({
                        label  : Tools.tr("form.course.tab:course units"),
                        layout : new qx.ui.layout.VBox
                    });
                    control.add(this.getChildControl("form-course-units"));
                    break;

                case "form-general":
                    control = frontend.lib.ui.form.Form.create(qx.lang.Object.clone(this._template), "form.course").set({
                        submitAfterValidation : false
                    });
                    break;

                case "form-description":
                    control = frontend.lib.ui.form.Form.create({
                        description : {
                            type : "CKEditor",
                            nolabel : true,
                            properties : {
                                toolbar : "advanced"
                            }
                        }
                    }, "form.course").set({
                        submitAfterValidation : false
                    });
                    break;

                case "form-course-units":
                    control = new frontend.app.module.course.Units;
                    break;

                case "ok-button":
                    control = new qx.ui.form.Button(Tools["tr"]("form.course:button " + (this._rowData ? "edit" : "add")), "button-submit");
                    control.addListener("execute", this._onBtnOK, this);
                    break;

                case "cancel-button":
                    control = new qx.ui.form.Button(Tools.tr("form.course:button cancel"), "button-cancel");
                    control.addListener("execute", this._onBtnCancel, this);
                    break;

                case "buttons":
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
                    control.add(this.getChildControl("cancel-button"));
                    control.add(this.getChildControl("ok-button"));
                    break;
            }

            return control || this.base(arguments, id);
        },

        _loadData : function()
        {
            if (this._rowData)
            {
                var id = this._rowData.id;
                var requestCourses = new frontend.lib.io.HttpRequest(Urls.resolve('COURSES', id));
                requestCourses.addListener('success', function(e) {
                    var data = qx.lang.Json.parse(e.getTarget().getResponse());
                    var form = this.getChildControl("form-general");
                    form.setUserData("data", data);
                    form.populate(data);

                    form = this.getChildControl("form-description");
                    form.setUserData("data", data);
                    form.populate(data);
                }, this);
                requestCourses.send();

                var requestCourseUnits = new frontend.lib.io.HttpRequest(Urls.resolve("COURSE_UNITS", { course_id : id }));
                requestCourseUnits.addListener('success', function(e) {
                    var data = qx.lang.Json.parse(e.getTarget().getResponse());
                    var form = this.getChildControl("form-course-units");
                    form.setUserData("data", data);
                    form.populate(data);
                }, this);
                requestCourseUnits.send();
            }
        },

        _onBtnOK : function()
        {
            var validatedCount = 0;
            var tab;

            for (var formIndex = 0, formsCount = this._forms.length; formIndex < formsCount; formIndex++)
            {
                var name      = this._forms[formIndex];
                var form      = this.getChildControl("form-" + name);
                var validator = form.getFormValidator();
                
                validator.addListenerOnce("complete", function(e){
                    if (validator.isValid()) {
                        if (++validatedCount == formsCount) {
                            qx.event.Timer.once(this._save, this, 100);
                        }
                    } else if (tab == null) {
                        this.getChildControl("tabview").setSelection([
                            tab = this.getChildControl("tab-" + name)
                        ]);
                    }
                }, this);
                form.send();
            }
        },

        _save : function()
        {
            if (this._request === null) {
                this._request = new frontend.lib.io.HttpRequest().set({
                    method : "POST",
                    url    : this._rowData ? Urls.resolve("COURSES", this._rowData.id) : Urls.resolve("COURSES")
                });
                this._request.addListener("success", this._onSave, this);
            }
            var data = qx.lang.Object.merge(
                this.getChildControl("form-general").getValues(),
                this.getChildControl("form-description").getValues(),
                this.getChildControl("form-course-units").getValues(),
                this._rowData ? { "_method" : "PUT" } : {}
            );
            this._request.setRequestData(data);
            this._request.send();
        },

        _onSave : function()
        {
            this.showMessage(Tools["tr"]("form.course:" + (this._rowData ? "edited" : "added")));
            this.fireEvent("completed");
            this.close();
        },

        _onBtnCancel : function()
        {
            this.fireEvent("canceled");
            this.close();
        }
    }
});