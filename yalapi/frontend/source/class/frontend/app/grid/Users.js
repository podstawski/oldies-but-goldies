qx.Class.define("frontend.app.grid.Users",
{
    extend : frontend.lib.grid.Abstract,

    construct : function()
    {
        this.base(arguments);

        var rolesSource = frontend.app.source.Roles.getInstance();
        var toolbar = this.getChildControl("toolbar");
        toolbar.getChildControl("filter")
               .setSource(rolesSource)
               .setLabel("Rola");

        toolbar.getChildControl("add-button").setToolTipText("Dodaj użytkownika spoza domeny Googla");
        toolbar.getChildControl("delete-selected-button").setToolTipText("Usuń wybranych użytkowników");
        toolbar.getChildControl("refresh-button").setToolTipText("Odśwież listę użytkowników");

        this._tableModel.setDataFormatter("extra_buttons", function(rowData){
            return {
                "edit" : "button-edit",
                "profile" : "user-profile",
                "delete" : "button-delete",
                "usercourseinfo" : "help-faq"
            }
        });
        this._tableModel.setDataFormatter("role_name", function(rowData){
            return rolesSource.getById(rowData.role_id).label;
        }, this);

        var table = this.getChildControl("table");
        table.addListener("profileRowClick", this._onProfileRowClick, this);
        table.addListener("usercourseinfoRowClick", this._onInfoRowClick, this);


        this._tableModel.connectToFilter(toolbar.getChildControl("filter"));
        this._tableModel.setFilterKey("role_id");
        this._tableModel.setColumnSortable(2, false);
        this._tableModel.sortByColumn(3, this._tableModel.isSortAscending());

        var addButton = toolbar.getChildControl("add-button");
        if (!Acl.hasRight("users.C")) {
            addButton.exclude();
        } else if (frontend.app.Login.getDomain()) {
            var importButton = this.getChildControl("import-button");
            importButton.addListener("execute", this._onImportButtonClick, this);
            toolbar.addAfter(importButton, addButton);
        }
        if (Acl.hasRight("users.D")) {
            this._table.setShowCheckboxes(true);
        } else {
            this._table.setShowCheckboxes(false);
            toolbar.getChildControl("delete-selected-button").exclude();
        }
        this._table.setShowEditRemove(Acl.hasRight("users.U"));
        this._table.setColumnVisibilityButtonVisible(true);
        var resizeBehavior = this._table.getTableColumnModel().getBehavior();
        this._table.addListenerOnce("appear", function(e){
            resizeBehavior.setWidth(1, 130);
            resizeBehavior.setWidth(2, 50);
            resizeBehavior.setWidth(5, 100);
        }, this);
    },

    members :
    {
        _addActions : true,
        _tableModelUrl              : "USERS",
        _tableKeys                  : ["_lp", "full_name", "username", "role_name", "plain_password", "email", "national_identity", "full_address", "update_date"],
        _tableColumnNames           : ["LP", "Imię i nazwisko", "Nazwa użytkownika", "Rola", "Hasło", "E-mail", "PESEL", "Adres", "Ostatnia zmiana"],

        addFormClass     : frontend.app.form.user.Add,
        editFormClass    : frontend.app.form.user.Edit,

        _tableCustom :
        {
            initiallyHiddenColumns : [ 6, 8, 9, 10 ]
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "import-button":
                    control = new frontend.lib.ui.form.Button(Tools["tr"]("import_users.button:label"), "button-add");
                    control.setMargin(0);
                    control.setMinWidth(100);
                    control.setHeight(30);
                    control.setToolTipText(Tools["tr"]("import_users.button:tooltip"));
                    break;
            }

            return control || this.base(arguments, id, hash);
        },

        _onProfileRowClick : function(e)
        {
            var rowData = e.getData();
            if (rowData) {
                frontend.app.module.user.Profile.getInstance().open(rowData.id);
            }
        },

        _onInfoRowClick : function(e)
        {
            var rowData = e.getData();
            if (rowData) {
                var info = new frontend.app.module.user.Info(rowData);
            }
        },

        _onImportButtonClick : function(e)
        {
            var manager = new frontend.app.module.user.Manager();
            manager.addListener("completed", this._tableModel.reloadData, this._tableModel);
            manager.center();
            manager.open();
        }
    }
});