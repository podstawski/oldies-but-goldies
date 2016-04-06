/* *********************************

#asset(qx/icon/${qx.icontheme}/22/actions/list-add.png)
#asset(qx/icon/${qx.icontheme}/22/actions/view-refresh.png)
#asset(qx/icon/${qx.icontheme}/22/actions/edit-delete.png)

#asset(qx/icon/${qx.icontheme}/16/actions/edit-paste.png)
#asset(qx/icon/${qx.icontheme}/16/actions/view-refresh.png)
#asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)

********************************** */

qx.Class.define("frontend.lib.list.Abstract",
{
    extend : qx.ui.container.Composite,

    type : "abstract",

    include : [
        frontend.MMessage,
        frontend.lib.util.MGetSource
    ],

    events :
    {
        "added"   : "qx.event.type.Event",
        "deleted" : "qx.event.type.Event",
        "edited"  : "qx.event.type.Event"
    },

    properties :
    {
        appearance :
        {
            refine : true,
            init : "ui-table-list"
        },

        reloadDataOnTabChange :
        {
            check : "Boolean",
            init : true
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.VBox);

        this.add(this._createChildControl("toolbar"));
        this.add(this._createChildControl("tabview"), {flex:1});
    },

    members :
    {
        addTab : function(label, table)
        {
            var tab = new qx.ui.tabview.Page(label);
            tab.setLayout(new qx.ui.layout.VBox);
            tab.add(table, {flex:1});
            tab.setUserData("table", table);

            table.addListener("editRowClick", this._onEditRowClick, this);
            table.addListener("deleteRowClick", this._onDeleteRowClick, this);
            table.addListener("changeRowSelectedCount", this._toggleDeleteSelectedButton(table), this);
            table.addListener("appear", this._toggleDeleteSelectedButton(table), this);
            table.addListener("appear", this._onAppear(table), this);

            this.getChildControl("tabview").add(tab);

            return tab;
        },

        _onAppear : function(table)
        {
            var deleteSelectedButton = this.getChildControl("toolbar").getChildControl("delete-selected-button");
            return function(e)
            {
                var listenerId = table.bind("showCheckboxes", deleteSelectedButton, "visibility", {
                    converter : function(value) {
                        return value ? "visible" : "excluded"
                    }
                });
                table.addListenerOnce("disapear", function(e){
                    table.removeListenerById(listenerId);
                }, this);
            }
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "toolbar":
                    control = new frontend.lib.ui.Toolbar();
                    control.getChildControl("add-button").addListener("execute", this._onAddButtonClick, this);
                    control.getChildControl("delete-selected-button").addListener("execute", this._onDeleteSelectedButtonClick, this);
                    control.getChildControl("refresh-button").addListener("execute", this._onRefreshButtonClick, this);
                    break;

                case "tabview":
                    control = new qx.ui.tabview.TabView().set({
                        marginTop : 10
                    });
                    control.addListener("changeSelection", this._onChangeTabSelection, this);
                    break;

            }

            return control || this.base(arguments, id);
        },

        _onChangeTabSelection : function(e)
        {
            var old = e.getOldData()[0];
            if (old) {
                old.getUserData("table").getTableModel().disconnectFromFilter().disconnectFromSearchBox();
            }

            var tableModel = this.getTableModel();
            var searchbox  = this.getChildControl("toolbar").getChildControl("searchbox");
            var filter     = this.getChildControl("toolbar").getChildControl("filter");

            tableModel.connectToSearchBox(searchbox).connectToFilter(filter);
        },

        _toggleDeleteSelectedButton : function(table)
        {
            var deleteSelectedButton = this.getChildControl("toolbar").getChildControl("delete-selected-button");
            return function(e)
            {
                deleteSelectedButton.setEnabled(
                    table.getSelectedRowCount() > 0
                );
            }
        },

        reloadData : function(resetSelection)
        {
            qx.event.Timer.once(function(){
                this.getTableModel().reloadData();
                if (resetSelection) {
                    this.getTable().resetSelectedRows();
                }
            }, this, 500);
        },

        getTable : function()
        {
            return this.getChildControl("tabview").getSelection()[0].getUserData("table");
        },

        getTableModel : function()
        {
            return this.getTable().getTableModel();
        },

        __hasMTableMixin : function(element)
        {
            return qx.Class.hasMixin(element.constructor, frontend.lib.ui.table.MTable);
        },

        __tryDefault : function(action, e)
        {
            var rowData;
            var result = false;
            var table  = this.getTable();

            switch (action)
            {
                case "delete":
                    rowData = e.getData();
                    if (result = !!rowData.id)
                    {
                        var dialog = new frontend.lib.dialog.Confirm("Na pewno chcesz usunąć tę pozycję?");
                        dialog.addListenerOnce("yes", function(e){
                            var request = new frontend.lib.io.HttpRequest().set({
                                url     : Urls.resolve(this.getTableModel().getDataUrl(), rowData.id),
                                method  : "DELETE"
                            });
                            request.addListenerOnce("success", function(e){
                                this.showMessage("Pozycja została usunięta!");
                                this.fireEvent("deleted");
                                this.reloadData(true);
                            }, this);
                            request.send();
                        }, this);
                    }
                    break;

                case "delete-selected":
                    var selectedRows      = table.getSelectedRows();
                    var selectedRowsCount = table.getSelectedRowCount();
                    
                    if (result = selectedRowsCount > 0)
                    {
                        var dialog = new frontend.lib.dialog.Confirm("Na pewno chcesz usunąć zaznaczone pozycje?");
                        dialog.addListenerOnce("yes", function(e) {
                            var loading = new frontend.lib.dialog.Dialog("Proszę czekać...");
                            var dataUrl = this.getTableModel().getDataUrl();
                            var request = new frontend.lib.io.HttpRequest().set({
                                method  : "DELETE",
                                showLoadingDialog : false
                            });

                            request.addListener("success", function(e){
                                if (current == selectedRowsCount) {
                                    timer.stop();
                                    loading.close();
                                    this.showMessage("Pozycje zostały usunięte!");
                                    this.fireEvent("deleted");
                                    this.reloadData(true);
                                }
                            }, this);
                            request.addListener("fail", function(e){
                                timer.stop();
                                loading.close();
                            }, this);

                            var keys    = qx.lang.Object.getKeys(selectedRows);
                            var current = 0;
                            var timer   = new qx.event.Timer(100);
                            timer.addListener("interval", function(e){
                                if (current > 0 && !request.isDone()) {
                                    return;
                                }
                                var key = keys[current++];
                                request.setUrl(Urls.resolve(dataUrl, selectedRows[key].id));
                                request.send();
                            }, this);
                            timer.start();
                        }, this);
                    }
                    break;

                case "edit":
                    rowData = e.getData();
                case "add":
                    if (this.__hasMTableMixin(table))
                    {
                        var formClass = result = table.get(action + "FormClass");
                        if (formClass)
                        {
                            var clazz = qx.Class.getByName(formClass);
                            var win = new clazz(rowData);
                            win.addListener("completed", function(e){
                                this.fireEvent(action + "ed");
                                this.reloadData();
                            }, this);
                            win.open();
                        }
                    }
                    break;
            }

            return result;
        },

        _onAddButtonClick : function(e)
        {
            if (!this.__tryDefault("add", e)) {
                throw new Error("Abstract _onAddButtonClick method call");
            }
        },

        _onDeleteSelectedButtonClick : function(e)
        {
            if (!this.__tryDefault("delete-selected", e)) {
                throw new Error("Abstract _onDeleteSelectedButtonClick method call");
            }
        },

        _onEditRowClick : function(e)
        {
            if (!this.__tryDefault("edit", e)) {
                throw new Error("Abstract _onEditRowClick method call");
            }
        },

        _onDeleteRowClick : function(e)
        {
            if (!this.__tryDefault("delete", e)) {
                throw new Error("Abstract _onDeleteRowClick method call");
            }
        },

        _onRefreshButtonClick : function(e)
        {
            var searchbox = this.getChildControl("toolbar").getChildControl("searchbox");
            if (searchbox.getSearchValue()) {
                searchbox.clearTextField();
            } else {
                this.getTableModel().reloadData();
            }
        }
    }
});