qx.Class.define("frontend.app.form.survey.AverageResults",
{
    extend : qx.ui.window.Window,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData, type)
    {
        this.base(arguments);
        this._type = type;
        this._init();
        var someContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(0));

        var table, tableModel;
        tableModel = new frontend.lib.ui.table.model.Remote().set({
            dataUrl : Urls.resolve("SURVEY", {id:rowData.id, method: "averageResults"})
        });
        tableModel.setColumns(
            ["Ankieta", "Wynik"],
            ["name", "percent"]
        );
        table = new frontend.lib.ui.table.Table().set({
            tableModel : tableModel,
            showCheckboxes : false,
            showEditRemove : false
        });
        this.add(table);

//        table.addListener("cellDblclick", function(e){
//            var oldRowData = rowData;
//            var columnIndex = e.getColumn();
//            var rowIndex    = e.getRow();
//            var rowData     = tableModel.getRowData(rowIndex);
//
//            var detailed = new frontend.app.form.survey.DetailedResults({
//                id: rowData.user_id,
//                survey_id: rowData.id,
//                group_id: oldRowData.group_id
//            }, this._type);
//            detailed.show();
//        }, this);

        var cancelButton = new qx.ui.form.Button(Tools.tr("survey.results:close"), "icon/16/actions/dialog-close.png");
            cancelButton.addListener("click", function(){this.close()}, this);

        var buttonsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(2, "right")).set({width:200,height:30});
            buttonsContainer.add(cancelButton);

        this.add(buttonsContainer);

        this.setWidth(650);
        this.center();
        this.open();
    },

    members :
    {
        _type : null,
        _form : null,
        _container : null,
        _composite : null,
        _id : {},

        _init : function(){
            this._container =  new qx.ui.container.Scroll();

            this._composite = new qx.ui.container.Composite;
            this._composite.setLayout(new qx.ui.layout.VBox(5));
            this._container.setWidth(650);
            this._container.setHeight(540);
            this._container.add(this._composite);

            this.set({
                layout : new qx.ui.layout.VBox,
                width : 500
            });

            this.setCaption(
                this._type == "survey" ? Tools.tr("Przekrój wypełnionych ankiet użytkownika") :
                    Tools.tr("Przekrój wypełnionych testów użytkownika")
            );
        }
    }
});