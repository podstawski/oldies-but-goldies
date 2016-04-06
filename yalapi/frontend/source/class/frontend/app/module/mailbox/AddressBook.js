/* *********************************

#asset(qx/icon/${qx.icontheme}/22/actions/edit-delete.png)

********************************** */

qx.Class.define("frontend.app.module.mailbox.AddressBook",
{
    extend : frontend.lib.ui.window.Modal,

    construct : function()
    {
        this.base(arguments);

        this.setMinWidth(600);
        
        this.add(this.getChildControl("toolbar"));
        this.add(this.getChildControl("tabview"), {flex:1});
        var btnContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
        btnContainer.add(this.getChildControl("close-btn"));
        this.add(btnContainer);
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "toolbar":
                    control = new qx.ui.toolbar.ToolBar;
                    control.addSpacer();
                    control.add(this.getChildControl("searchbox"));
                    break;

                case "searchbox":
                    control = new frontend.lib.ui.SearchBox;
                    break;

                case "tabview":
                    control = new qx.ui.tabview.TabView;
                    control.addListener("changeSelection", this._onChangeTabSelection, this);
                    control.add(this.getChildControl("tab-groups"));
                    control.add(this.getChildControl("tab-users"));
                    control.add(this.getChildControl("tab-trainers"));
                    break;

                case "tab-groups":
                    control = this.__makeTabPage(
                        "groups", Urls.resolve("GROUPS"), ["Nazwa", "Liczba użytkowników"], ["name", "members"], function(rowData){
                        return rowData.name;
                    });
                    break;

                case "tab-users":
                    control = this.__makeTabPage("users", Urls.resolve("USERS", {
                        "pager[role_id]" : 2
                    }), ["Imię i nazwisko", "Nazwa użytkownika"], ["full_name", "username"], function(rowData){
                        return rowData.username;
                    });
                    break;

                case "tab-trainers":
                    control = this.__makeTabPage("trainers", Urls.resolve("USERS", {
                        "pager[role_id]" : 5
                    }), ["Imię i nazwisko", "Nazwa użytkownika"], ["full_name", "username"], function(rowData){
                        return rowData.username;
                    });
                    break;

                case "close-btn":
                    control = new frontend.lib.ui.form.Button("Wybierz");
                    control.addListener("execute", this.close, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        __makeTabPage : function(category, dataUrl, columnNames, columnIds, dataFormatter)
        {
            var tab = new qx.ui.tabview.Page().set({
                label  : Tools["tr"]("addressbook.tab:" + category),
                layout : new qx.ui.layout.VBox
            });
            var tableModel = new frontend.lib.ui.table.model.Remote().set({
                dataUrl : dataUrl
            });
            tableModel.setColumns(columnNames, columnIds);
            tableModel.setDataFormatter("display_name", dataFormatter);
            var table = new frontend.lib.ui.table.Grid().set({
                tableModel             : tableModel,
                showCheckboxes         : true,
                showEditRemove         : false,
                autoReloadDataOnAppear : false
            });
            table.addListener("changeRowSelected", function(e){
                this.fireDataEvent("changeRecipient", [ category, e.getData().rowData ]);
            }, this);
            tab.add(table);
            return tab;
        },

        _onChangeTabSelection : function(e)
        {
            var old = e.getOldData()[0];
            if (old) {
                old.getChildren()[0].getTableModel().disconnectFromSearchBox();
            }

            var tableModel = e.getData()[0].getChildren()[0].getTableModel();
            var searchbox  = this.getChildControl("searchbox");

            tableModel.connectToSearchBox(searchbox);
        }
    }
});