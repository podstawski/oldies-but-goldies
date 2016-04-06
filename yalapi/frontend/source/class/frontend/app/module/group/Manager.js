qx.Class.define("frontend.app.module.group.Manager",
{
    extend : frontend.lib.ui.window.Modal,

    events :
    {
        "changeGroupData" : "qx.event.type.Data"
    },

    properties :
    {
        groupData :
        {
            check : "Map",
            init : null,
            nullable : true,
            apply : "_applyGroupData",
            event : "changeGroupData"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setAppearance("group-manager");

        this.__formValidator = new frontend.lib.ui.form.validation.Manager();
        this.__formValidator.addListener("complete", this._save, this);

        this.add(this.getChildControl("info-box"));

//        var layout = new qx.ui.layout.Grid(10, 10);
//        layout.setColumnFlex(0, 1);
//        layout.setColumnFlex(2, 1);

        var separator = new qx.ui.container.Composite(new qx.ui.layout.VBox(10, "middle"));
        var container = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
        container.add(this.getChildControl("all-users-box"), {flex:1});
        container.add(separator);
        container.add(this.getChildControl("group-users-box"), {flex:1});

        separator.add(this.getChildControl("add-selected-button"));
        separator.add(this.getChildControl("remove-selected-button"));

        this.add(container, {flex:1});

        var buttons = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
        buttons.add(this.getChildControl("cancel-button"));
        buttons.add(this.getChildControl("save-button"));

        this.add(buttons);

        this.addListener("changeGroupUsers", this._onChangeGroupUsers, this);
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "info-box":
                    control = new qx.ui.groupbox.GroupBox("Informacje o grupie szkoleniowej");
                    var layout = new qx.ui.layout.Grid(10, 10);
                    layout.setColumnFlex(1, 1);
                    control.setLayout(layout);

                    control.add(new qx.ui.basic.Label("nazwa grupy"), { row : 0, column : 0 });
                    control.add(this.getChildControl("group-name"), { row : 0, column : 1 });
                    control.add(new qx.ui.basic.Label("poziom zaawansowania"), { row : 0, column : 2 });
                    control.add(this.getChildControl("group-level"), { row : 0, column : 3 });
                    layout.setRowAlign(0, "left", "middle");

                    if (frontend.app.Login.getDomain()) {
                        control.add(new qx.ui.basic.Label("identyfikator grupy"), { row : 1, column : 0 });
                        control.add(this.getChildControl("google-group-id"), { row : 1, column : 1 });
                        control.add(new qx.ui.basic.Label("@" + frontend.app.Login.getDomain()), { row : 1, column : 2, colSpan : 2 });
                        layout.setRowAlign(1, "left", "middle");
                    }

                    control.setEnabled(Acl.hasRight("groups.U"));
                    break;

                case "group-name":
                    control = new frontend.lib.ui.form.TextField();
                    control.setRequired(true);
                    this.__formValidator.add(control, [ Validate.string(), Validate.slength(4, 256) ], this);
                    break;

                case "group-level":
                    control = new frontend.lib.ui.form.SelectBox();
                    control.setRequired(true);
                    control.setSource("Levels");
                    break;

                case "google-group-id":
                    control = new frontend.lib.ui.form.TextField();
                    this.bind("groupData", control, "enabled", {
                        converter : function (groupData) {
                            return !groupData || !groupData.google_group_id;
                        }
                    });
                    this.__formValidator.add(control, [
                        Validate.string(),
                        Validate.slength(0, 256),
                        Validate.regex(/^[a-zA-z0-9-_\.]+$/, Tools.tr("group.manager.error:invalid_google_group_id"))
                    ], this);
                    break;

                case "searchbox":
                    control = new frontend.lib.ui.SearchBox();
                    break;

                case "all-users-box":
                    control = new qx.ui.groupbox.GroupBox("Lista uczestników");
                    control.setMinWidth(300);
                    control.setLayout(new qx.ui.layout.VBox(10));
                    var searchbox = this.getChildControl("searchbox#all");
                    var table = this.getChildControl("all-users-table");
                    table.getTableModel().connectToSearchBox(searchbox);
                    control.add(searchbox);
                    control.add(table, {flex:1});
                    break;

                case "all-users-table":
                    var tableModel = new frontend.lib.ui.table.model.Remote();
                    tableModel.setDataUrl(Urls.resolve("USERS"));
                    tableModel.setColumns(["Imię i nazwisko"], ["full_name"]);
                    control = new frontend.lib.ui.table.List();
                    control.setTableModel(tableModel);
                    control.setShowCheckboxes(true);
                    control.setRowHeight(30);
                    control.setRenderer(function(rowData){
                        var table = this.cellInfo.table;
                        var layout = this.__titleContainer.getLayout();
                        layout.setColumnFlex(0, 1);
                        var label = new qx.ui.basic.Label(rowData.full_name);
                        label.setToolTipText(qx.lang.String.format("imię i nazwisko: %1<br/>nazwa użytkownika: %2<br/>email: %3", [
                            rowData.full_name,
                            rowData.username,
                            rowData.email
                        ]));
                        this.__titleContainer.add(label, {row:0, column:0});
                        if (Acl.hasRight("group_users.U")) {
                            var button = new qx.ui.basic.Image("list-add");
                            button.setToolTipText("dodaj użytkownika do grupy");
                            button.setCursor("pointer");
                            button.addListener("click", function(e){
                                table.fireDataEvent("addUserToGroup", rowData);
                            }, this);
                            this.__titleContainer.add(button, {row:0, column:1});
                        }
                    });
                    control.addListener("addUserToGroup", this._onAddUserToGroup, this);
                    break;

                case "group-users-box":
                    control = new qx.ui.groupbox.GroupBox("Uczestnicy w grupie");
                    control.setLayout(new qx.ui.layout.VBox(10));
                    control.setMinWidth(300);
                    var searchbox = this.getChildControl("searchbox#group");
                    var table = this.getChildControl("group-users-table");
                    table.getTableModel().connectToSearchBox(searchbox);
                    control.add(searchbox);
                    control.add(table, {flex:1});
                    break;

                case "group-users-table":
                    var tableModel = new frontend.lib.ui.table.model.Filtered();
                    tableModel.setColumns(["Imię i nazwisko"], ["full_name"]);
                    control = new frontend.lib.ui.table.List();
                    control.setTableModel(tableModel);
                    control.setShowCheckboxes(true);
                    control.setRowHeight(35);
                    var $this = this;
                    control.setRenderer(function(rowData){
                        var table = this.cellInfo.table;
                        var layout = this.__titleContainer.getLayout();
                        layout.setColumnFlex(0, 1);
                        var label = new qx.ui.basic.Label(rowData.full_name);
                        label.setToolTipText(qx.lang.String.format("imię i nazwisko: %1<br/>nazwa użytkownika: %2<br/>email: %3", [
                            rowData.full_name,
                            rowData.username,
                            rowData.email
                        ]));
                        this.__titleContainer.add(label, {row:0, column:0});

                        if (Acl.hasRight("group_users.U")) {
                            var status = new qx.ui.form.CheckBox();
                            status.setValue(!!rowData.status);
                            status.setToolTipText("Zaznacz, aby potwierdzić uczestnika");
                            status.addListener("changeValue", function(e){
                                $this.__groupUsers[rowData.id].status = !!e.getData();
                            }, this);
                            var button = new qx.ui.basic.Image("list-remove");
                            button.setToolTipText("usuń użytkownika z grupy");
                            button.setCursor("pointer");
                            button.addListener("click", function(e){
                                table.fireDataEvent("removeUserFromGroup", rowData);
                            }, this);
                            this.__titleContainer.add(status, {row:0, column:1});
                            this.__titleContainer.add(button, {row:0, column:2});
                        }
                    });
                    control.addListener("removeUserFromGroup", this._onRemoveUserFromGroup, this);
                    break;

                case "add-selected-button":
                    control = new qx.ui.form.Button(">>");
                    control.setEnabled(false);
                    this.getChildControl("all-users-table").addListener("changeRowSelectedCount", function(e){
                        control.setEnabled(Acl.hasRight("group_users.U") && !!e.getData());
                    }, this);
                    control.addListener("execute", this._onAddSelectedClick, this);
                    break;

                case "remove-selected-button":
                    control = new qx.ui.form.Button("<<");
                    control.setEnabled(false);
                    this.getChildControl("group-users-table").addListener("changeRowSelectedCount", function(e){
                        control.setEnabled(Acl.hasRight("group_users.U") && !!e.getData());
                    }, this);
                    control.addListener("execute", this._onRemoveSelectedClick, this);
                    break;

                case "save-button":
                    control = new qx.ui.form.Button("Zapisz");
                    control.addListener("execute", this._onSaveClick, this);
                    break;

                case "cancel-button":
                    control = new qx.ui.form.Button("Anuluj");
                    control.addListener("execute", this._onCancelClick, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        __groupUsers : null,

        __internalChange : null,

        _addUserToGroup : function(rowData)
        {
            if (this.__groupUsers == null) {
                this.__groupUsers = {};
            }
            
            if (this.__groupUsers[rowData.id] == null) {
                rowData.selected_row = false;
                this.__groupUsers[rowData.id] = rowData;

                if (this.__internalChange !== true) {
                    this.fireEvent("changeGroupUsers");
                }
            }
        },

        _removeUserFromGroup : function(rowData)
        {
            if (this.__groupUsers[rowData.id] != null) {
                delete this.__groupUsers[rowData.id];
                
                if (this.__internalChange !== true) {
                    this.fireEvent("changeGroupUsers");
                }
            }
        },

        _onAddUserToGroup : function(e)
        {
            var rowData = e.getData();
            this._addUserToGroup(rowData);
        },

        _onRemoveUserFromGroup : function(e)
        {
            var rowData = e.getData();
            this._removeUserFromGroup(rowData);
        },

        _onAddSelectedClick : function(e)
        {
            var selectedRows = this.getChildControl("all-users-table").getSelectedRows();
            this.__internalChange = true;
            qx.lang.Object.getValues(selectedRows).forEach(this._addUserToGroup, this);
            this.fireEvent("changeGroupUsers");
        },

        _onRemoveSelectedClick : function(e)
        {
            var selectedRows = this.getChildControl("group-users-table").getSelectedRows();
            this.__internalChange = true;
            qx.lang.Object.getValues(selectedRows).forEach(this._removeUserFromGroup, this);
            this.getChildControl("group-users-table").resetSelectedRows();
            this.fireEvent("changeGroupUsers");
        },

        _onChangeGroupUsers : function(e)
        {
            this.__internalChange = false;
            var data = qx.lang.Object.getValues(this.__groupUsers);
            this.getChildControl("group-users-table").getTableModel().setDataAsMapArray(data, true);
        },

        _onSaveClick : function(e)
        {
            this.__formValidator.validate();
        },

        _save : function()
        {
            if (this.__formValidator.isValid()) {
                var users = {};
                qx.lang.Object.getKeys(this.__groupUsers).forEach(function(userID){
                    users[userID] = this.__groupUsers[userID].status ? 1 : 0
                }, this);
                var request = new frontend.lib.io.HttpRequest();
                var groupData = this.getGroupData();
                if (groupData != null) {
                    request.setUrl(Urls.resolve("GROUPS", groupData.id));
                    request.setMethod("PUT");
                } else {
                    request.setUrl(Urls.resolve("GROUPS"));
                    request.setMethod("POST");
                }
                var requestData = {
                    name            : this.getChildControl("group-name").getValue(),
                    advance_level   : this.getChildControl("group-level").getSelection()[0].getModel(),
                    google_group_id : this.getChildControl("google-group-id").getValue(),
                    users           : JSON.stringify(users)
                }
                request.setRequestData(requestData);
                request.addListenerOnce("success", function(e){
                    this.fireEvent("completed");
                    this.close();
                }, this);
                request.send();
            }
        },

        _onCancelClick : function(e)
        {
            this.close();
        },

        _onPopulateRequestSuccess : function(e)
        {
            var data = e.getTarget().getResponseJson();
            if (data) {
                this.__internalChange = true;
                data.forEach(this._addUserToGroup, this);
                this.fireEvent("changeGroupUsers");
            }
        },

        _applyGroupData : function(groupData, old)
        {
            this.__groupUsers = {};
            this.getChildControl("group-users-table").getTableModel().setData([]);
            
            if (groupData) {
                if (groupData.google_group_id && qx.lang.String.contains(groupData.google_group_id, "@")) {
                    groupData.google_group_id = groupData.google_group_id.split("@")[0];
                }
                this.getChildControl("group-name").setValue(groupData.name);
                this.getChildControl("google-group-id").setValue(groupData.google_group_id);
                this.getChildControl("group-level").setModelSelection([ parseInt(groupData.advance_level, 10) ]);

                var request = new frontend.lib.io.HttpRequest();
                request.setUrl(Urls.resolve("GROUPS", { group_user_id : groupData.id }));
                request.addListener("success", this._onPopulateRequestSuccess, this);
                request.send();
            } else {
                this.getChildControl("group-name").setValue("");
                this.getChildControl("google-group-id").setValue("");
                this.getChildControl("group-level").resetSelection();
            }
        }
    }
});