qx.Class.define("frontend.app.grid.Groups",
{
    extend : frontend.lib.grid.Abstract,

    construct: function()
    {
        var source = frontend.app.source.Levels.getInstance();
        this._dataFormatters['advance_level_name'] = function(rowData) {
            var levelRow = source.getById(rowData.advance_level);
            if (levelRow) {
                return levelRow.level;
            } else {
                return rowData.advance_level;
            }
        };
        this._dataFormatters['courses'] = function(rowData) {
            if (!!rowData.courses) {
                return rowData.courses.split("#").join(", ")
            }
            return "";
        };

        var domain = frontend.app.Login.getDomain();
        if (domain) {
            this._tableKeys.push("google_group_id");
            this._tableColumnNames.push("Identyfikator grupy");
        }

        this.base(arguments);

        var toolbar   = this.getChildControl("toolbar");
        var addButton = toolbar.getChildControl("add-button");

        addButton.setToolTipText("Dodaj nową grupę");

        if (!Acl.hasRight("groups.C")) {
            addButton.exclude();
        } else if (frontend.app.Login.getDomain()) {
            var importButton = this.getChildControl("import-button");
            importButton.addListener("execute", this._onImportButtonClick, this);
            toolbar.addAfter(importButton, addButton);
        }
        if (!Acl.hasRight("groups.D")) {
            toolbar.getChildControl("delete-selected-button").exclude();
        }

        toolbar.getChildControl("add-button").setToolTipText("Dodaj nową grupę");
        toolbar.getChildControl("delete-selected-button").setToolTipText("Usuń wybrane grupy");
        toolbar.getChildControl("refresh-button").setToolTipText("Odśwież listę grup");

        this._table.setShowCheckboxes(Acl.hasRight("groups.D"));
        this._table.setShowEditRemove(Acl.hasRight("groups.D") || Acl.hasRight("groups.U") || Acl.hasRight("group_users.U"));

        this._tableModel.setDataFormatter("extra_buttons", function(rowData){
            var buttons = {};
            if (Acl.hasRight("groups.U") || Acl.hasRight("group_users.U")) {
                buttons["edit"] = "button-edit";
            }
            if (Acl.hasRight("groups.D")) {
                buttons["delete"] = "button-delete";
            }
            if (frontend.app.Login.getDomain() && Acl.hasRight("groups.U")) {
                buttons["syncgroup"] = "button-refresh";
            }
            return buttons;
        }, this);

        if (domain) {
            this._tableModel.setDataFormatter("google_group_id", function(rowData){
                if (rowData.google_group_id && rowData.google_group_id.indexOf('@') == -1) {
                    rowData.google_group_id += "@" + domain;
                }
                return rowData.google_group_id;
            });
        }

        this._table.addListener("syncgroupRowClick", this._onSyncGroupRowClick, this);

        this.__groupManager = new frontend.app.module.group.Manager();
        this.__groupManager.addListener("completed", this._tableModel.reloadData, this._tableModel);

        this.__syncModePrompter = new frontend.app.module.group.SyncModePrompt;
        this.__syncModePrompter.addListener("groupSynced", this._tableModel.reloadData, this._tableModel);

        var resizeBehavior = this._table.getTableColumnModel().getBehavior();
        this._table.addListenerOnce("appear", function(e){
            resizeBehavior.setWidth(3, 150);
            resizeBehavior.setWidth(5, 180);
        }, this);
    },

    members :
    {
        _tableModelUrl    : "GROUPS",
        _tableKeys        : ["name", "members", "courses", "advance_level_name"],
        _tableColumnNames : ["Nazwa", "Liczba uczestników", "Szkolenie", "Poziom zaawansowania"],
        addFormClass      : frontend.app.form.group.Add,
        editFormClass     : frontend.app.form.group.Edit,

        __groupManager : null,

        __syncModePrompter : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "import-button":
                    control = new frontend.lib.ui.form.Button(Tools["tr"]("import_groups.button:label"), "button-add");
                    control.setMargin(0);
                    control.setMinWidth(100);
                    control.setHeight(30);
                    control.setToolTipText(Tools["tr"]("import_groups.button:tooltip"));
                    break;
            }

            return control || this.base(arguments, id, hash);
        },

        _onEditRowClick : function(e)
        {
            var rowData = e.getData();
            this.__groupManager.setGroupData(null);
            this.__groupManager.setGroupData(rowData);
            this.__groupManager.open();
        },

        _onClickButtonAdd : function(e)
        {
            this.__groupManager.setGroupData(null);
            this.__groupManager.open();
        },

        _onSyncGroupRowClick : function(e)
        {
            var groupData = e.getData();
            if (!groupData.google_group_id) {
                var prompter = new frontend.app.module.group.GoogleIdPrompt(groupData);
                prompter.addListenerOnce("completed", function(e){
                    this._tableModel.reloadData();
                    this.__syncModePrompter.open(groupData.id);
                }, this);
                prompter.open();
            } else {
                this.__syncModePrompter.open(groupData.id);
            }
        },

        _onImportButtonClick : function(e)
        {
            var importer = new frontend.app.module.group.Importer();
            importer.addListener("changeGroups", this._tableModel.reloadData, this._tableModel);
            importer.center();
            importer.open();
        }
    }
});