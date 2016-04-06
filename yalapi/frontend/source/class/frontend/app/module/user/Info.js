qx.Class.define("frontend.app.module.user.Info",
{
    extend : frontend.lib.ui.window.Modal,

    properties :
    {
        data :
        {
            check : "Map",
            init : null,
            nullable : true,
            apply : "_applyData"
        }
    },

    construct : function(rowData)
    {
        this.base(arguments);
        
        this.setCaption("Informacje o szkoleniach użytkownika: " + rowData.username);
        this.setLayout(new qx.ui.layout.VBox(10));

        this.add(this.getChildControl("content"), {flex:1});

        var bottomContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
        bottomContainer.add(new qx.ui.basic.Label("Szkolenia:").set({alignY:"middle"}));
        bottomContainer.add(this.getChildControl("selectbox"));
        bottomContainer.add(new qx.ui.core.Spacer, {flex:1});
        bottomContainer.add(this.getChildControl("button-close"));
        this.add(bottomContainer);
        
        this.__rowData = rowData;
        this.__request = new frontend.lib.io.HttpRequest;
        this.__request.addListener("success", this._onRequestSuccess, this);

        this.addListener("appear", this.reloadData, this);
        
        this.open();
    },

    members :
    {
        __request : null,

        __rowData : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "content":
                    control = new qx.ui.container.Stack();
                    control.setDynamic(true);
                    control.addAt(this.getChildControl("label#0"), 0);
                    break;

                case "selectbox":
                    control = new frontend.lib.ui.form.SelectBox();
                    control.setSource("Statuses");
                    control.addListener("changeSelection", this.reloadData, this);
                    break;

                case "button-close":
                    control = new qx.ui.form.Button("Zamknij", "button-close");
                    control.addListener("execute", this.close, this);
                    break;

                case "label":
                    control = new qx.ui.container.Composite(new qx.ui.layout.VBox(10, "middle"));
                    this._updateSize(control);
                    var label = new qx.ui.basic.Label("<b>brak danych</b>");
                    label.setRich(true);
                    label.setAlignX("center");
                    control.add(label, {flex:1});
                    break;
            }

            return control || this.base(arguments, id);
        },

        reloadData : function()
        {
            var status  = this.getChildControl("selectbox").getSelection()[0].getModel();
            var content = this.getChildControl("content");
            var item    = content.getChildren()[status];
            
            if (item) {
                content.setSelection([item]);
            } else {
                this.__request.setUrl(Urls.resolve("USER_INFO", {
                    id : this.__rowData.id,
                    status : status
                }));
                this.__request.send();
            }
        },
        
        _onRequestSuccess : function(e)
        {
            var status  = this.getChildControl("selectbox").getSelection()[0].getModel();
            var content = this.getChildControl("content");
            var item    = this.getChildControl("label#" + status);

            var data = e.getTarget().getResponseJson();
            if (this._checkData(data)) {
                item = new qx.ui.treevirtual.TreeVirtual(["nazwa", "kod / obecność", "grupa / trener", "data", "miejsce"]);
                this._updateSize(item);
                item.getTableColumnModel().setDataCellRenderer(1, new qx.ui.table.cellrenderer.Dynamic(function(cellinfo){
                    if (qx.lang.Type.isBoolean(cellinfo.rowData[1])) {
                        return new qx.ui.table.cellrenderer.Boolean;
                    }
                    return new qx.ui.table.cellrenderer.Default;
                }));
                item.getTableColumnModel().setDataCellRenderer(2, new qx.ui.table.cellrenderer.Html);
                item.getTableColumnModel().setDataCellRenderer(4, new qx.ui.table.cellrenderer.Html);
                item.setStatusBarVisible(false);

                var resizeBehavior = item.getTableColumnModel().getBehavior();
                resizeBehavior.setWidth(0, 300);
                resizeBehavior.setWidth(1, 105);
                resizeBehavior.setWidth(2, 200);
                resizeBehavior.setWidth(3, 170);

                var treeModel = item.getDataModel();

                var pd = new qx.util.format.DateFormat("yyyy-MM-dd HH:mm:ss");
                var fd = new qx.util.format.DateFormat("dd-MM-yyyy");
                var ft = new qx.util.format.DateFormat("HH:mm");
                var sf = function (value, prefix) {
                    var t;
                    if (prefix) {
                        t = "%2: <b title='%1'>%1</b>";
                    } else {
                        t = "<b title='%1'>%1</b>";
                    }
                    return qx.lang.String.format(t, arguments);
                }

                for (var project_id in data) {
                    var pData = data[project_id];
                    var pBranch = treeModel.addBranch(null, sf(pData.name, "projekt"), true);
                    treeModel.setColumnData(pBranch, 1, pData.code);

                    if (this._checkData(pData.courses)) {
                        for (var course_id in pData.courses) {
                            var cData = pData.courses[course_id];
                            var cBranch = treeModel.addBranch(pBranch, sf(cData.name, "szkolenie"), true);

                            treeModel.setColumnData(cBranch, 1, cData.code);
                            treeModel.setColumnData(cBranch, 2, sf(cData.group.name));
                            treeModel.setColumnData(cBranch, 3, fd.format(pd.parse(cData.start_date)) + " - " + fd.format(pd.parse(cData.end_date)));

                            if (cData.training_center) {
                                treeModel.setColumnData(cBranch, 4, qx.lang.String.format("<b title='%1\n%2\n%3, %4'>%1</b>", [
                                    cData.training_center.name,
                                    cData.training_center.street,
                                    cData.training_center.zip_code,
                                    cData.training_center.city
                                ]));
                            }

                            if (this._checkData(cData.units)) {
                                for (var unit_id in cData.units) {
                                    var uData = cData.units[unit_id];
                                    var uBranch = treeModel.addBranch(cBranch, sf(uData.name, "jednostka"), true);

                                    if (this._checkData(uData.lessons)) {
                                        var i = 1;
                                        for (var lesson_id in uData.lessons) {
                                            var lData = uData.lessons[lesson_id];
                                            var lLeaf = treeModel.addLeaf(uBranch, lData.subject || ("Lekcja " + i));

                                            treeModel.setColumnData(lLeaf, 3, fd.format(pd.parse(lData.start_date)) + ", " + ft.format(pd.parse(lData.start_date)) + " - " + ft.format(pd.parse(lData.end_date)));
                                            if (lData.trainer) {
                                                treeModel.setColumnData(lLeaf, 2, lData.trainer.first_name + " " + lData.trainer.last_name);
                                            }

                                            if (lData.room) {
                                                treeModel.setColumnData(lLeaf, 4, lData.room);
                                            }
                                            
                                            treeModel.setColumnData(lLeaf, 1, !!lData.present);
                                            i++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                treeModel.setData();
            }

            if (content.getChildren()[status]) {
                content.removeAt(status);
            }
            
            content.addAt(item, status);
            content.setSelection([item]);
        },

        _checkData : function(data)
        {
            return qx.lang.Type.isObject(data) && !qx.lang.Object.isEmpty(data);
        },

        _updateSize : function(item)
        {
            item.setWidth(950);
            item.setHeight(450);
        }
    }
});