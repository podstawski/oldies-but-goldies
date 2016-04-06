qx.Class.define("frontend.app.module.ejournal.Main",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "ui-table-list"
        },

        groupId :
        {
            check : "Integer",
            init : null,
            nullable : true,
            event : "changeGroupId",
            apply : "_applyGroupId"
        },

        courseId :
        {
            check : "Integer",
            init : null,
            nullable : true,
            event : "changeCourseId",
            apply : "_applyCourseId"
        },

        unitId :
        {
            check : "Integer",
            init : null,
            nullable : true,
            event : "changeUnitId",
            apply : "_applyUnitId"
        },

        module :
        {
            check : [ "grades", "presence", "schedule" ],
            init : null,
            apply : "_applyModule"
        }
    },

    events :
    {
        "changeGroupId"   : "qx.event.type.Data",
        "changeCourseId"  : "qx.event.type.Data",
        "changeUnitId"    : "qx.event.type.Data"
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.VBox(10));
        
        this.__modules = {};

        this.add(this.getChildControl("toolbar"));
        this.add(this.getChildControl("info"));
        this.add(this.getChildControl("content"), {flex:1});
    },

    members :
    {
        __table : null,

        __modules : null,
        
        _applyGroupId : function(groupID, old)
        {
            var selectbox = this.getChildControl("selectbox-course");
            selectbox.set({
                enabled : !!groupID,
                source  : []
            });
            if (groupID) {
                var request = new frontend.lib.io.HttpRequest;
                request.setUrl(Urls.resolve("GROUP_COURSES", groupID));
                request.addListenerOnce("success", function(e){
                    var data = request.getResponseJson();
                    selectbox.set({
                        enabled : true,
                        source  : data
                    });
                }, this);
                request.send();
            }
        },

        _applyCourseId : function(courseID, old)
        {
            var selectbox = this.getChildControl("selectbox-unit");
            selectbox.set({
                enabled : !!courseID,
                source  : []
            });

            if (courseID) {
                var request = new frontend.lib.io.HttpRequest;
                request.setUrl(Urls.resolve("COURSE_UNITS", { course_id : courseID }));
                request.addListenerOnce("success", function(e){
                    var data = request.getResponseJson();
                    selectbox.set({
                        enabled : true,
                        source  : new frontend.app.source.Source().set({
                            data    : data,
                            dataKey : "name"
                        })
                    });
                }, this);
                request.send();
            }
        },

        _applyUnitId : function(unitID, old)
        {
            var module = this.getModule();
            if (module && unitID) {
                this.__modules[module].reloadData();
            }
        },
        
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "combotable":
                    control = new frontend.lib.ui.form.ComboTable().set({
                        dataUrl          : Urls.resolve("GROUPS"),
                        dataColumn       : "name",
                        minWidth         : 150,
                        placeholder      : "wybierz grupę...",
                        selectOnlyOption : true
                    });
                    control.getChildControl("list").setMinWidth(400);
                    control.bind("model", this, "groupId");
                    control.bind("value", this.getChildControl("info"), "groupName");
                    break;

                case "selectbox-course":
                    control = new frontend.lib.ui.form.SelectBox().set({
                        defaultOption    : "wybierz szkolenie...",
                        minWidth         : 150,
                        source           : [],
                        enabled          : false,
                        selectOnlyOption : true
                    });
                    this.__bindProp(control, "courseId");
                    control.addListener("changeSelection", function(e){
                        var selection = e.getData()[0];
                        this.getChildControl("info").setCourseName(
                            selection != null && selection.getModel()
                                ? selection.getLabel()
                                : null
                        );
                    }, this);
                    break;

                case "selectbox-unit":
                    control = new frontend.lib.ui.form.SelectBox().set({
                        defaultOption    : "wybierz jednostkę...",
                        minWidth         : 150,
                        source           : [],
                        enabled          : false,
                        selectOnlyOption : true
                    });
                    this.__bindProp(control, "unitId");
                    this.__bindVisibility(control);
                    control.addListener("changeSelection", function(e){
                        var info = this.getChildControl("info");
                        var selection = e.getData()[0];
                        if (selection != null && selection.getModel()) {
                            info.setUnitName(selection.getLabel());
                            info.setTrainerName(control.getSource().getById(selection.getModel()).trainer_name);
                        } else {
                            info.setUnitName(null);
                            info.setTrainerName(null);
                        }
                    }, this);
                    break;

                case "toolbar":
                    control = new qx.ui.toolbar.ToolBar;
                    control.set({
                        appearance : "ui-toolbar"
                    });
                    control.add(this.getChildControl("combotable"));
                    control.add(this.getChildControl("selectbox-course"));
                    control.add(this.getChildControl("selectbox-unit"));
                    control.add(this.getChildControl("report-ejournal"));
                    control.add(this.getChildControl("report-presence"));
                    control.addSpacer();
                    break;

                case "content":
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
                    break;

                case "info":
                    control = new frontend.app.module.ejournal.Info();
                    break;

                case "report-ejournal":
                    control = new qx.ui.form.Button(Tools["tr"]("reportPicker.template.Ejournal"));
                    control.addListener("execute", this._openReportWithDateRange(12), this);
                    this.bind("courseId", control, "enabled", {
                        converter : function (value) {
                            return !!value;
                        }
                    });
                    break;

                case "report-presence":
                    control = new qx.ui.form.Button(Tools["tr"]("reportPicker.template.PresenceList"));
                    control.addListener("execute", this._openReportWithDateRange(1), this);
                    this.bind("courseId", control, "enabled", {
                        converter : function (value) {
                            return !!value;
                        }
                    });
                    break;
            }

            return control || this.base(arguments, id);
        },

        __bindProp : function(selectbox, propName)
        {
            selectbox.bind("selection", this, propName, {
                converter : function(value) {
                    var selection = value[0];
                    return (selection && selection.getModel()) ? selection.getModel() : null;
                }
            });
        },

        __bindVisibility : function(selectbox)
        {
            selectbox.bind("selection", this.getChildControl("content"), "visibility", {
                converter : function(value) {
                    var selection = value[0];
                    return (selection && selection.getModel()) ? "visible" : "excluded";
                }
            });
        },

        _applyModule : function(module, old)
        {
            var content = this.getChildControl("content");

            if (old) {
                this.__modules[old].exclude();
                content.remove(this.__modules[old]);
            }

            if (module) {
                if (this.__modules[module] == null) {
                    var clazz = qx.Class.getByName("frontend.app.module.ejournal." + qx.lang.String.firstUp(module));
                    this.__modules[module] = new clazz(this);
                    this.__modules[module].exclude();
                }

                content.add(this.__modules[module], {flex:1});
                this.__modules[module].addListenerOnce("makeTableFinish", this.__modules[module].show);
                this.__modules[module].reloadData();
            }
        },

        _openReportWithDateRange : function(reportID)
        {
            var report = new frontend.app.module.ejournal.ReportWithDateRange;
            report.setReportId(reportID);
            this.bind("courseId", report, "courseId");

            return function (e)
            {
                var form = report.getForm();
                form.getItem("date_from").resetValue();
                form.getItem("date_to").resetValue();
                report.open();
            }
        }
    }
});