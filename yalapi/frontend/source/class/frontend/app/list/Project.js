qx.Class.define("frontend.app.list.Project",
{
    extend : frontend.lib.list.Abstract,

    construct : function()
    {
        this.base(arguments);

        var toolbar = this.getChildControl("toolbar");
        if (!Acl.hasRight("projects.C")) {
            toolbar.getChildControl("add-button").exclude();
        }
        if (!Acl.hasRight("projects.D")) {
            toolbar.getChildControl("delete-selected-button").exclude();
        }

        this.addTab("Bieżące",    this._createTable(1));
        this.addTab("Planowane",  this._createTable(2));
        this.addTab("Archiwalne", this._createTable(3));

        this.addListener("deleted", this._updateSource, this);
    },

    members :
    {
        _createTable : function(status)
        {
            var table, tableModel;
            
            tableModel = new frontend.lib.ui.table.model.Remote().set({
                dataUrl : Urls.resolve("PROJECTS")
            });
            tableModel.setParam("status", status);
            tableModel.setColumns(
                ["Nazwa", "Kod", "Data utworzenia", "Data rozpoczęcia", "Data zakończenia"],
                ["name", "code", "created_date", "start_date", "end_date"]
            );
            tableModel.setDataFormatter("name_and_code", function(rowData){
                return qx.lang.String.format("%1 (%2)", [rowData.name, rowData.code]);
            });
            table = new frontend.lib.ui.table.List().set({
                renderer        : function(rowData)
                {
                    this.addTitle(rowData.name);

                    this.addLeft(rowData.code, "Kod");
                    this.addLeft(rowData.name_and_code)

                    this.addRight(rowData.start_date, "Rozpoczęcie");
                    this.addRight(rowData.end_date, "Zakończenie");

                    if (Acl.hasRight("projects.U")) {
                        this.addButton("edit");
                    }
                    if (Acl.hasRight("projects.D")) {
                        this.addButton("delete");
                    }
                },
                rowHeight       : 130,
                tableModel      : tableModel,
                addFormClass    : "frontend.app.form.Project",
                editFormClass   : "frontend.app.form.Project",
                showCheckboxes  : Acl.hasRight("projects.D")
            });
            return table;
        },

        _updateSource : function()
        {
            frontend.app.source.Projects.getInstance().reload();
        }
    }
});