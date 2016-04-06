qx.Class.define("frontend.app.list.SurveyResultUserList",
{
    extend : frontend.lib.list.Abstract,

    include : [
        frontend.MMessage
    ],

     events :
    {
        "open" : "qx.event.type.Data",
        "edit" : "qx.event.type.Data",
        "define" : "qx.event.type.Data",
        "delete" : "qx.event.type.Data"
    },

    construct : function(rowData)
    {
        this.base(arguments);
        var table, tableModel;

        tableModel = new frontend.lib.ui.table.model.Remote().set({
            dataUrl : Urls.resolve("SURVEY", {method: "listForGroupAndSurvey"})
        });

        tableModel.setColumns(
             ["Login", "Akcje"],
            ["username", "name"]
        );


        table = new frontend.lib.ui.table.List().set({
            renderer        : function(rowData)
            {
                    this.addTitle(rowData.username);
            },
            rowHeight       : 30,
            tableModel      : tableModel    ,
            showCheckboxes:false

        });
       table.addListener("cellClick", function(e){
           var rowData = e.getData();
           var fillWindow = new frontend.app.form.survey.Send(rowData);
           fillWindow.show();
       });


        this.addTab(Tools.tr("Wyniki grupy"), table);
    }
});