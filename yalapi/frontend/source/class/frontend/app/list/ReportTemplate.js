qx.Class.define("frontend.app.list.ReportTemplate",
{
    extend : frontend.lib.list.Abstract,

    construct : function()
    {
        this.base(arguments);

        var toolbar = this.getChildControl("toolbar");

        toolbar.addAfter(this.getChildControl("preview-button"), toolbar.getChildControl("refresh-button"));

        toolbar.getChildControl("filter")
               .setSource("Projects")
               .setLabel("Projekt");

        this.getChildControl('toolbar').getChildControl('add-button').setEnabled(false);

        var table, tableModel;
        
        tableModel = new frontend.lib.ui.table.model.Remote().set({
            dataUrl : Urls.resolve("REPORT_TEMPLATES"),
            filterKey: 'project_id'
        });
        tableModel.setColumns(
            ["Nazwa", "Opis"],
            ["name", "description"]
        );
        table = new frontend.lib.ui.table.List().set({
            renderer        : function(rowData)
            {
                this.addTitle(rowData.name);

                this.addLeft(rowData.description, "Opis");

                this.addLeft(!rowData.project_name ? 'Wzorzec' : ('Projekt: ' + rowData.project_name));

                if (rowData.project_id) {
                    this.addButton("edit");
                    this.addButton("delete");
                    this.addButton("download");
                } else {
                    this.addButton("copyToProject");
                }
            },
            rowHeight       : 100,
            tableModel      : tableModel,
            editFormClass   : "frontend.app.form.report.Form"
        });

        table.addListener("copyToProjectRowClick", function(e) {
            var projectWindow = new frontend.app.form.report.Copy(e.getData());
            projectWindow.open();
            projectWindow.addListener('completed', function() {
                tableModel.reloadData();
            }, this);
        }, this);

        table.addListener("downloadRowClick", function(e) {
            window.location.href = Urls.resolve('REPORT_TEMPLATES', {id:e.getData().id, download: 1});
        }, this);


        this.addTab("Raporty", table);
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "preview-button":
                    control = new frontend.lib.ui.form.Button("Podgląd");
                    control.addListener("execute", this._onPreviewClick, this);
                    break;
            }

            return control || this.base(arguments, id, hash);
        },

        _onPreviewClick : function(e)
        {
            frontend.app.module.report.ReportPicker.openForm();
        }
    }
});