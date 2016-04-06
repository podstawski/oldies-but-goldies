qx.Class.define("frontend.app.form.Project",
{
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData)
    {
        this.base(arguments);

        this.setCaption(Tools["tr"]("form.project:window " + (rowData ? "edit" : "add")));
        this.setLayout(new qx.ui.layout.VBox(10));
        this.setResizable(true);
        this.setWidth(600);

        this._rowData = rowData;

        this.add(this.getChildControl("tabview"), {flex:1});
        this.add(this.getChildControl("buttons"));

        var popup = this.getChildControl("form-extra-fields-popup");
        popup.addListenerOnce("appear", function() {
            popup.moveTo(10, 10);
        }, this);
        this.getChildControl("form-extra-fields-intro").addListener("click", popup.show, popup);
        this.getChildControl("tab-extra-fields").addListener("disappear", popup.hide, popup);

        this._loadData();
    },

    members :
    {
        _template   :
        {
            name : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            code : {
                type : "TextField",
                properties : {
                    required : true,
                    maxLength : 64
                },
                validators : [
                    Validate.slength(2, 256)
                ]
            },
            description : {
                type : "TextArea",
                properties : {
                    autoSize : true
                }
            },
            start_date : {
                type: 'DateField',
                properties: {
                    required : true,
                    value: new Date(),
                    dateFormat: new qx.util.format.DateFormat('dd-MM-yyyy'),
                    allowStretchX : false,
                    width : 120
                }
            },
            end_date : {
                type: 'DateField',
                properties: {
                    required : true,
                    dateFormat: new qx.util.format.DateFormat('dd-MM-yyyy'),
                    allowStretchX : false,
                    width : 120
                }
            },
            status : {
                type : "SelectBox",
                properties : {
                    source : "Statuses",
                    allowStretchX : false,
                    width : 120
                }
            },
            is_default : {
                type : "CheckBox"
            }
        },

        _extraFieldsToolTip : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "tabview":
                    control = new qx.ui.tabview.TabView;
                    control.add(this.getChildControl("tab-general"));
                    control.add(this.getChildControl("tab-leaders"));
                    control.add(this.getChildControl("tab-extra-fields"));
                    break;

                case "tab-general":
                    control = new qx.ui.tabview.Page().set({
                        label  : Tools.tr("form.project.tab:general"),
                        layout : new qx.ui.layout.VBox
                    });
                    control.add(this.getChildControl("form-general"));
                    break;

                case "tab-leaders":
                    control = new qx.ui.tabview.Page().set({
                        label  : Tools.tr("form.project.tab:leaders"),
                        layout : new qx.ui.layout.VBox
                    });
                    var info = new qx.ui.basic.Label
                    control.add(this.getChildControl("form-leaders"));
                    break;

                case "tab-extra-fields":
                    control = new qx.ui.tabview.Page;
                    control.setLayout(new qx.ui.layout.VBox(10, "top", "separator-vertical"));
                    control.setLabel(Tools.tr("form.project.tab:extra_fields"));
                    control.add(this.getChildControl("form-extra-fields-intro"));
                    control.add(this.getChildControl("form-extra-fields"));
                    break;

                case "form-general":
                    control = frontend.lib.ui.form.Form.create(qx.lang.Object.clone(this._template), "form.project").set({
                        submitAfterValidation : false
                    });
                    break;

                case "form-leaders":
                    control = new frontend.lib.ui.form.CheckList;
                    break;

                case "form-extra-fields":
                    control = frontend.lib.ui.form.Form.create({
                        extra_fields : {
                            type : "TextArea",
                            properties : {
                                autoSize : true,
                                minimalLineHeight : 8
                            },
                            nolabel : true
                        }
                    }, "form.project").set({
                        submitAfterValidation : false
                    });
                    break;

                case "form-extra-fields-tooltip":
                    control = new qx.ui.tooltip.ToolTip;
                    control.setMaxWidth(400);
                    control.setAutoHide(false);
                    control.setRich(true);
                    control.setLabel(Tools.tr("form.project:extra_fields_info"));
                    control.removeListener("mouseover", control._onMouseOver, control);
                    break;

                case "form-extra-fields-popup":
                    control = new frontend.lib.ui.popup.Popup;
                    control.setLayout(new qx.ui.layout.VBox);
                    control.setAutoHide(false);
                    control.setMovable(true);
                    var label = new qx.ui.basic.Label;
                    label.setRich(true);
                    label.setValue(Tools.tr("form.project:extra_fields_info"));
                    control.add(label);
                    break;

                case "form-extra-fields-intro":
                    control = new qx.ui.basic.Atom;
                    control.setIcon("help-faq");
                    control.setRich(true);
                    control.setLabel(Tools.tr("form.project:extra_fields_intro"));
                    break;

                case "buttons":
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
                    control.add(this.getChildControl("cancel-button"));
                    control.add(this.getChildControl("ok-button"));
                    break;

                case "ok-button":
                    control = new qx.ui.form.Button(Tools["tr"]("form.project:button " + (this._rowData ? "edit" : "add")), "button-submit");
                    control.addListener("execute", this._onBtnOK, this);
                    break;

                case "cancel-button":
                    control = new qx.ui.form.Button(Tools.tr("form.project:button cancel"), "button-cancel");
                    control.addListener("execute", this._onBtnCancel, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        _loadData : function()
        {
            var id;
            if (this._rowData)
            {
                id = this._rowData.id;
                var projectRequest = new frontend.lib.io.HttpRequest(Urls.resolve("PROJECTS", id));
                projectRequest.addListenerOnce("success", function(e) {
                    var data = qx.lang.Json.parse(e.getTarget().getResponse());
                    var form = this.getChildControl("form-general");
                    form.setUserData("data", data);
                    form.populate(data);
                    this.getChildControl("form-extra-fields").populate(data);
                }, this);
                projectRequest.send();
            }

            var leadersRequest = new frontend.lib.io.HttpRequest(Urls.resolve("PROJECT_LEADERS", id));
            leadersRequest.addListenerOnce("success", function(e) {
                var leaders = qx.lang.Json.parse(e.getTarget().getResponse());
                var source = new frontend.app.source.Source;
                source.setData(leaders);
                source.setDataKey("username");
                var form = this.getChildControl("form-leaders");
                form.setUserData("data", leaders);
                form.setSource(source);
            }, this);
            leadersRequest.send();
        },

        _onBtnOK : function()
        {
            var form = this.getChildControl("form-general");
            var validator = form.getFormValidator();
            validator.addListenerOnce("complete", function(e){
                if (validator.isValid()) {
                    this._save();
                } else {
                    this.getChildControl("tabview").setSelection([
                        this.getChildControl("tab-general")
                    ]);
                }
            }, this);
            form.send();
        },

        _save : function()
        {
            if (this._request == null) {
                this._request = new frontend.lib.io.HttpRequest().set({
                    method : this._rowData ? "PUT" : "POST",
                    url    : this._rowData ? Urls.resolve("PROJECTS", this._rowData.id) : Urls.resolve("PROJECTS")
                });
                this._request.addListener("success", this._onSave, this);
            }
            var requestData = this.getChildControl("form-general").getValues();
            requestData["leaders"] = qx.lang.Json.stringify(this.getChildControl("form-leaders").getCheckedIds());
            requestData["extra_fields"] = this.getChildControl("form-extra-fields").getItem("extra_fields").getValue();
            this._request.setRequestData(requestData);
            this._request.send();
        },

        _onSave : function()
        {
            this.showMessage(Tools["tr"]("form.project:" + (this._rowData ? "edited" : "added")));
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