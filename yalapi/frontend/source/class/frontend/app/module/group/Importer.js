qx.Class.define("frontend.app.module.group.Importer",
{
    extend : frontend.lib.ui.window.Modal,

    events :
    {
        "changeGroups" : "qx.event.type.Event"
    },

    construct : function()
    {
        this.base(arguments);

        this.setAppearance("group-manager");

        var separator = new qx.ui.container.Composite(new qx.ui.layout.VBox(10, "middle"));
        var container = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
        container.add(this.getChildControl("google-groups-box"), {flex:1});
        container.add(separator);
        container.add(this.getChildControl("yala-groups-box"), {flex:1});

        separator.add(this.getChildControl("add-selected-button"));
//        separator.add(this.getChildControl("remove-selected-button"));

        this.add(container, {flex:1});

        var buttons = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
        buttons.add(this.getChildControl("save-button"));
        this.add(buttons);
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "google-groups-box":
                    control = new qx.ui.groupbox.GroupBox("Grupy w domenie " + frontend.app.Login.getDomain());
                    control.setMinWidth(300);
                    control.setLayout(new qx.ui.layout.VBox(10));
                    var table = this.getChildControl("google-groups-table");
                    control.add(table, {flex:1});
                    break;

                case "google-groups-table":
                    var tableModel = new frontend.lib.ui.table.model.Filtered();
                    tableModel.setColumns(["Nazwa grupy"], ["group_name"]);

                    var request = new frontend.lib.io.HttpRequest;
                    request.setUrl(Urls.resolve("GOOGLE_APPS_RETRIEVE_ALL_GROUPS"));
                    request.addListener("success", function(e){
                        var data = request.getResponseJson();
                        tableModel.setData(data);
                    }, this);
                    request.send();

                    control = new frontend.lib.ui.table.List();
                    control.setTableModel(tableModel);
                    control.setShowCheckboxes(true);
                    control.setRowHeight(35);
                    control.setRenderer(function(rowData){
                        var table = this.cellInfo.table;
                        var layout = this.__titleContainer.getLayout();
                        layout.setColumnFlex(0, 1);
                        var label = new qx.ui.basic.Label(rowData.name);
                        label.setToolTipText(qx.lang.String.format("nazwa grupy: %1<br/>identyfikator grupy: %2", [
                            rowData.name,
                            rowData.google_group_id
                        ]));
                        this.__titleContainer.add(label, { row : 0, column : 0 });
                        if (Acl.hasRight("groups.C")) {
                            var button = new qx.ui.basic.Image("list-add");
                            button.setToolTipText("dodaj grupę do aplikacji");
                            button.setCursor("pointer");
                            button.addListener("click", function(e){
                                table.fireDataEvent("addGroup", rowData);
                            }, this);
                            this.__titleContainer.add(button, { row : 0, column : 1 });
                        }
//                        this.__titleContainer.add(new qx.ui.basic.Label(rowData.google_group_id) , { row : 1, column : 0, colSpan : 2 });
                    });
                    control.addListener("addGroup", this._onAddGroup, this);
                    break;

                case "yala-groups-box":
                    control = new qx.ui.groupbox.GroupBox("Grupy w aplikacji");
                    control.setLayout(new qx.ui.layout.VBox(10));
                    control.setMinWidth(300);
                    var table = this.getChildControl("yala-groups-table");
                    control.add(table, {flex:1});
                    break;

                case "yala-groups-table":
                    var tableModel = new frontend.lib.ui.table.model.Remote();
                    tableModel.setColumns(["Nazwa grupy"], ["name"]);
                    tableModel.setDataUrl(Urls.resolve("GROUPS"));
                    this.addListener("changeGroups", tableModel.reloadData, tableModel);
                    control = new frontend.lib.ui.table.List();
                    control.setTableModel(tableModel);
                    control.setShowCheckboxes(true);
                    control.setRowHeight(35);
                    var $this = this;
                    control.setRenderer(function(rowData){
                        var table = this.cellInfo.table;
                        var layout = this.__titleContainer.getLayout();
                        layout.setColumnFlex(0, 1);
                        var label = new qx.ui.basic.Label(rowData.name);
                        label.setToolTipText(qx.lang.String.format("nazwa grupy: %1<br/>identyfikator grupy: %2", [
                            rowData.name,
                            rowData.google_group_id ? rowData.google_group_id : "brak"
                        ]));
                        this.__titleContainer.add(label, {row:0, column:0});
                        if (Acl.hasRight("groups.D")) {
                            var button = new qx.ui.basic.Image("list-remove");
                            button.setToolTipText("usuń grupę");
                            button.setCursor("pointer");
                            button.addListener("click", function(e){
                                table.fireDataEvent("removeGroup", rowData);
                            }, this);
                            this.__titleContainer.add(button, { row : 0, column : 1 });
                        }
                        if (rowData.google_group_id) {
//                            this.__titleContainer.add(new qx.ui.basic.Label(rowData.google_group_id) , { row : 1, column : 0, colSpan : 2 });
                        }
                    });
                    control.addListener("removeGroup", this._onRemoveGroup, this);
                    break;

                case "add-selected-button":
                    control = new qx.ui.form.Button(">>");
                    control.setEnabled(false);
                    this.getChildControl("google-groups-table").addListener("changeRowSelectedCount", function(e){
                        control.setEnabled(Acl.hasRight("groups.C") && !!e.getData());
                    }, this);
                    control.addListener("execute", this._onAddSelectedClick, this);
                    break;

                case "remove-selected-button":
                    control = new qx.ui.form.Button("<<");
                    control.setEnabled(false);
                    this.getChildControl("yala-groups-table").addListener("changeRowSelectedCount", function(e){
                        control.setEnabled(Acl.hasRight("groups.D") && !!e.getData());
                    }, this);
                    control.addListener("execute", this._onRemoveSelectedClick, this);
                    break;

                case "save-button":
                    control = new qx.ui.form.Button("Zamknij");
                    control.addListener("execute", this._onSaveClick, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        __internalChange : null,

        _addGroup : function(rowData)
        {
            var request = new frontend.lib.io.HttpRequest;
            if (!this.__internalChange) {
                request.addListenerOnce("success", function(e){
                    this.fireEvent("changeGroups");
                }, this);
            }
            request.setUrl(Urls.resolve("GROUPS", {
                from_google : true
            }));
            request.setMethod("POST");
            request.setRequestData(rowData);
            request.send();
        },

        _removeGroup : function(rowData)
        {
            var request = new frontend.lib.io.HttpRequest;
            if (!this.__internalChange) {
                request.addListenerOnce("success", function(e){
                    this.fireEvent("changeGroups");
                }, this);
            }
            request.setUrl(Urls.resolve("GROUPS", rowData.id));
            request.setMethod("DELETE");
            request.send();
        },

        _onAddGroup : function(e)
        {
            this.__internalChange = false;

            var data = e.getData();
            this._addGroup(data);
        },

        _onRemoveGroup : function(e)
        {
            this.__internalChange = false;

            var data = e.getData();
            this._removeGroup(data);
        },

        _onAddSelectedClick : function(e)
        {
            this.__internalChange = true;

            var selectedRows = this.getChildControl("google-groups-table").getSelectedRows();
            qx.lang.Object.getValues(selectedRows).forEach(this._addGroup, this);
            this.fireEvent("changeGroups");
        },

        _onRemoveSelectedClick : function(e)
        {
            this.__internalChange = true;

            var selectedRows = this.getChildControl("yala-groups-table").getSelectedRows();
            qx.lang.Object.getValues(selectedRows).forEach(this._removeGroup, this);
            this.getChildControl("yala-groups-table").resetSelectedRows();
            this.fireEvent("changeGroups");
        },

        _onSaveClick : function(e)
        {
            this.close();
        }
    }
});