qx.Class.define("frontend.app.module.user.Manager",
{
    extend : frontend.lib.ui.window.Modal,

    properties :
    {
        groupData :
        {
            check : "Map",
            init : null,
            nullable : true,
            apply : "_applyGroupData"
        }
    },

    construct : function(roleID)
    {
        this.base(arguments);

        this.setAppearance("group-manager");
        this.setCaption(Tools["tr"]("import_users.window:caption"));
        this.__roleID = roleID;
        this.__googleUsers = [];
        this.__formValidator = new frontend.lib.ui.form.validation.Manager();

        var layout = new qx.ui.layout.Grid(10, 10);
        layout.setColumnFlex(0, 1);
        layout.setColumnFlex(2, 1);

        this.setLayout(layout);
        var selectboxContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
        var role = new qx.ui.basic.Label("Rola:");
        role.setAlignY("middle");
        var selectbox = this.getChildControl("selectbox");
        selectbox.bind("visibility", role, "visibility");
        selectboxContainer.add(role);
        selectboxContainer.add(selectbox);
        this.add(selectboxContainer, {row : 0, column : 2});
        this.add(this.getChildControl("from-apps-users-box"), {row : 1, column : 0});
        this.add(this.getChildControl("in-application-users-box"), {row : 1, column : 2});

        var separator = new qx.ui.container.Composite(new qx.ui.layout.VBox(10, "middle"));
        separator.add(this.getChildControl("add-selected-button"));
//        separator.add(this.getChildControl("remove-selected-button"));

        this.add(separator, {row : 1, column : 1});

        var buttons = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
        buttons.add(this.getChildControl("finish-button"));

        this.add(buttons, {row : 2, column : 0, colSpan : 3});
//        this.addListener("changeAddedUsers", this._onChangeAddedUsers, this);
    },

    members :
    {
        __roleID : null,
        __googleUsers : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "group-name":
                    control = new frontend.lib.ui.form.TextField();
                    control.setRequired(true);
                    this.__formValidator.add(control, [ Validate.string(), Validate.slength(4, 256) ], this);
                    break;

                case "selectbox":
                    control = new frontend.lib.ui.form.SelectBox();
                    control.setRequired(true);
                    control.setSource("Roles");

                    var item = new qx.ui.form.ListItem("Wszystkie");
                    item.setModel(10);
                    control.addAt(item, 0);
                    control.setSelection([item]);

                    control.addListener("changeSelection", this._usersRoleSelectboxChange, this);
                    if (this.__roleID) {
                        control.exclude();
                    }
                    break;

                case "searchbox":
                    control = new frontend.lib.ui.SearchBox();
                    break;

                case "from-apps-users-box":
                    control = new qx.ui.groupbox.GroupBox("Użytkownicy domeny " + frontend.app.Login.getDomain());
                    control.setMinWidth(350);
                    control.setLayout(new qx.ui.layout.VBox(10));
                    var searchbox = this.getChildControl("searchbox#all");
                    var table = this.getChildControl("from-apps-users-table");
                    table.getTableModel().connectToSearchBox(searchbox);
                    control.add(searchbox);
                    control.add(table, {flex:1});
                    break;

                case "from-apps-users-table":
                    var tableModel = new frontend.lib.ui.table.model.Filtered(),
                        url = Urls.resolve("GOOGLE_APPS_RETRIEVE_ALL_USERS"),
                        request = new frontend.lib.io.HttpRequest(url, "GET");

                    request.addListener('success', function(){
                        var data = request.getResponseJson();
                        tableModel.setData(data);
                    }, this);
                    request.send();

                    tableModel.setColumns(["Nazwa użytkownika"], ["username"]);
                    control = new frontend.lib.ui.table.List();
                    control.setTableModel(tableModel);
                    control.setShowCheckboxes(true);
                    control.setRowHeight(35);
                    control.setRenderer(function(rowData){
                        var table = this.cellInfo.table;
                        var button = new qx.ui.basic.Image("list-add");
                        button.setToolTipText("dodaj użytkownika do grupy");
                        button.setCursor("pointer");
                        button.addListener("click", function(e){
                            table.fireDataEvent("addUserToApplication", rowData);
                        }, this);
                        var layout = this.__titleContainer.getLayout();
                        layout.setColumnFlex(0, 1);
                        var label = new qx.ui.basic.Label(rowData.username);
                        label.setToolTipText(qx.lang.String.format("imię i nazwisko: %1 %2<br/>nazwa użytkownika: %3", [
                            rowData.first_name,
                            rowData.last_name,
                            rowData.username
                        ]));
                        this.__titleContainer.add(label, {row:0, column:0});
                        // this.__titleContainer.add(button, {row:0, column:1});
                    });
                    control.addListener("addUserToApplication", this._addUserToApplication, this);

                    break;

                case "in-application-users-box":
                    var legend = "Użytkownicy aplikacji";
                    if (this.__roleID) {
                        legend = legend + " (" + frontend.app.source.Roles.getInstance().getById(this.__roleID).label + ")";
                    }
                    control = new qx.ui.groupbox.GroupBox(legend);
                    control.setLayout(new qx.ui.layout.VBox(10));
                    control.setMinWidth(350);
                    var searchbox = this.getChildControl("searchbox#group");
                    var table = this.getChildControl("in-application-users-table");
                    table.getTableModel().connectToSearchBox(searchbox);
                    control.add(searchbox);
                    control.add(table, {flex:1});
                    break;

                case "in-application-users-table":
                    var tableModel = new frontend.lib.ui.table.model.Filtered();
                    var url = Urls.resolve("USERS", {
                        role_id   : this.__roleID || '',
                        is_google : 1
                    });

                    tableModel.reloadData = function() {
                        var request = new frontend.lib.io.HttpRequest(url, "GET");

                       request.addListener('success', function(){
                           this.setData(request.getResponseJson());
                           this.fireEvent('in-apps-users-loaded');
                       }, this);
                       request.send();
                    };

                    tableModel.addListener('in-apps-users-loaded', function() {
                        this._resetRolesSelectbox();
//                        this._setDisabledExistingUsers();
                        this._groupUsersByRoles();
                    }, this );

                   tableModel.reloadData();
                   tableModel.setColumns(["Nazwa użytkownika"], ["username"]);

                   control = new frontend.lib.ui.table.List();
                   control.setTableModel(tableModel);
                   control.setShowCheckboxes(true);
                   control.setRowHeight(35);
                   control.setRenderer(function(rowData){
                       var table = this.cellInfo.table;
                       var button = new qx.ui.basic.Image("list-remove");
                       button.setToolTipText("dodaj użytkownika do grupy");
                       button.setCursor("pointer");
                       button.addListener("click", function(e){
                           table.fireDataEvent("removeUserFromApplication", rowData);
                       }, this);
                       var layout = this.__titleContainer.getLayout();
                       layout.setColumnFlex(0, 1);
                       var label = new qx.ui.basic.Label(rowData.username);
                       label.setToolTipText(qx.lang.String.format("imię i nazwisko: %1 %2<br/>nazwa użytkownika: %3", [
                           rowData.first_name,
                           rowData.last_name,
                           rowData.username
                       ]));
                       this.__titleContainer.add(label, {row:0, column:0});
                       //this.__titleContainer.add(button, {row:0, column:1});
                   });
                    control.addListener('removeUserFromApplication', this._removeUserFromApplication, this);
                    break;

                case "add-selected-button":
                    control = new qx.ui.form.Button(">>");
                    control.setEnabled(false);
                    control.setToolTipText("Dodaj wybranych użytkowników do aplikacji");
                    this.getChildControl("from-apps-users-table").addListener("changeRowSelectedCount", function(e){
                        control.setEnabled(!!e.getData());
                    }, this);
                    control.addListener("execute", this._onAddSelectedClick, this);
                    break;

                case "remove-selected-button":
                    control = new qx.ui.form.Button("<<");
                    control.setEnabled(false);
                    control.setToolTipText("Usuń wybranych użytkowników z aplikacji");
                    this.getChildControl("in-application-users-table").addListener("changeRowSelectedCount", function(e){
                        control.setEnabled(!!e.getData());
                    }, this);
                    control.addListener("execute", this._onRemoveSelectedClick, this);
                    break;

                case "finish-button":
                    control = new qx.ui.form.Button("Zakończ");
                    control.addListener("execute", this._onFinishClick, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        __internalChange : null,

        __usersInApps : {},
        __currentRole : 1,

        _resetRolesSelectbox : function()
        {
            this.getChildControl("selectbox").setModelSelection([10]);
        },

        _setUsersByCurrentRole : function()
        {
            this.getChildControl('in-application-users-table').getTableModel().setData(
                this.__usersInApps[this.__currentRole]
            );
        },

        _groupUsersByRoles : function()
        {
            var users = this.getChildControl('in-application-users-table').getTableModel().getData();
            var roles = frontend.app.source.Roles.getInstance().getData();

            for(var i in roles) {
                this.__usersInApps[roles[i].id] = [];
            }

            for(i in users) {
                if(typeof this.__usersInApps[users[i].role_id] !== "undefined") {
                    this.__usersInApps[users[i].role_id].push(users[i]);
                }
            }
            this.__usersInApps["10"] = users;
        },

        _usersRoleSelectboxChange : function()
        {
            this.__currentRole = this.getChildControl("selectbox").getSelection()[0].getModel();
            this._setUsersByCurrentRole();
        },

        _setDisabledExistingUsers : function()
        {
            var appsUsers = this.getChildControl("from-apps-users-table").getTableModel().getData();
            var inAppsUsers = this.getChildControl("in-application-users-table").getTableModel().getData();

            for (var i in appsUsers)
            {
                for (var j in inAppsUsers)
                {
                    if(inAppsUsers[j].username === appsUsers[i].username) {
                        delete appsUsers[i];
                        break;
                    }
                }
            }

            var users = [];
            for (var i in appsUsers)
            {
                if(typeof appsUsers[i] !== "undefined")
                {
                    users.push(appsUsers[i]);
                }
            }
            this.getChildControl("from-apps-users-table").getTableModel().setData(users);

        },

        _addUserToApplication : function(rowData)
        {
            if (typeof rowData.getData !== "undefined") {
                rowData = rowData.getData();
            }

            if (this.__roleID) {
                rowData["role_id"] = this.__roleID;
            }

            rowData["import_from_apps"] = true;

            var request = new frontend.lib.io.HttpRequest(Urls.resolve("USERS"), "POST");
            request.setRequestData(rowData);
            request.addListener("success", function(){
                this.getChildControl("in-application-users-table").getTableModel().reloadData();
                this.fireEvent("completed");
            }, this);
            request.send();
        },

        _removeUserFromApplication : function(rowData)
        {
            if(typeof rowData.getData !== "undefined") {
                rowData = rowData.getData();
            }

            var request = new frontend.lib.io.HttpRequest(Urls.resolve("USERS", rowData.id), "DELETE");
            request.addListener("success", function(){
                this.getChildControl("in-application-users-table").getTableModel().reloadData();
                this.fireEvent("completed");
            }, this);
            request.send();
        },

        _onAddSelectedClick : function(e)
        {
            var selectedRows = this.getChildControl("from-apps-users-table").getSelectedRows();
            qx.lang.Object.getValues(selectedRows).forEach(this._addUserToApplication, this);
        },

        _onRemoveSelectedClick : function(e)
        {
            var selectedRows = this.getChildControl("in-application-users-table").getSelectedRows();
            qx.lang.Object.getValues(selectedRows).forEach(this._removeUserFromGroup, this);
        },

        _onFinishClick : function(e)
        {
            this.close();
        }
    }
});