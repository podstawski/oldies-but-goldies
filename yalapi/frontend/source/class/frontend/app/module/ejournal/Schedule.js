qx.Class.define("frontend.app.module.ejournal.Schedule",
{
    extend : frontend.app.module.ejournal.Abstract,

    construct : function(main)
    {
        this.base(arguments, main);
    },

    members :
    {
        _url : "COURSE_SCHEDULE",

        _makeTable : function()
        {
            var tableModel = new frontend.lib.ui.table.model.Remote;

            var columnNames = ["data lekcji", "trener", "temat"];
            var columnIds   = ["lesson_date", "coach_name", "subject"];

            tableModel.setDataUrl(Urls.resolve(this._url));
            tableModel.setColumns(columnNames, columnIds);
            tableModel.setDataFormatter("coach_name", function(rowData){
                return rowData.user_id ? qx.lang.String.format("%1 %2 (%3)", [ rowData.first_name, rowData.last_name, rowData.username ]) : "nie ustawiono";
            });

            var table = new frontend.lib.ui.table.Grid(tableModel).set({
                showCheckboxes : false,
                showEditRemove : Acl.hasRight("course_schedule.U")
            });
            table.addListener("editRowClick", this._onEditRowClick, this);

            tableModel.setDataFormatter("extra_buttons", function(rowData){
                return {
                    "edit" : "icon/16/actions/edit-paste.png"
                }
            }, this);

            tableModel.setColumnNamesById({
                "extra_buttons" : "edytuj"
            });

            tableModel.sortByColumn(tableModel.getColumnIndexById("lesson_date"), false);

            table.addListenerOnce("appear", function(e){
                var cb = table.getTableColumnModel().getBehavior();
                cb.setWidth(tableModel.getColumnIndexById("extra_buttons"), 50);
                cb.setWidth(tableModel.getColumnIndexById("lesson_date"), 150);
                cb.setWidth(tableModel.getColumnIndexById("coach_name"), 200);
            }, this);

            return table;
        },

        _onEditRowClick : function(e)
        {
            var rowData = e.getData();
            if (rowData.schedule_id == null) {
                this._openForm({
                    lesson_id : rowData.lesson_id
                });
            } else {
                var request = new frontend.lib.io.HttpRequest;
                request.set({
                    url : Urls.resolve(this._url, rowData.schedule_id)
                });
                request.addListenerOnce("success", function(e){
                    var data = request.getResponseJson();
                    this._openForm(data);
                }, this);
                request.send();
            }
        },

        _openForm : function(rowData)
        {
            var tableModel = this.__table.getTableModel();
            var win  = new frontend.app.form.CourseSchedule(rowData);
            var form = win.getForm();
            form.addListener("completed", tableModel.reloadData, tableModel);
            form.createHiddenInput("lesson_id", rowData.lesson_id);
            win.open();
        },

        reloadData : function()
        {
            var unitID = this.__main.getUnitId();
            if (unitID != null)
            {
                if (this.__table == null) {
                    this.add(this.__table = this._makeTable(), {flex:1});
                }

                var groupID = this.__main.getGroupId();
                var tableModel = this.__table.getTableModel();
                tableModel.setParam("group_id", groupID);
                tableModel.setParam("course_unit_id", unitID);
                tableModel.addListenerOnce("dataChanged", function(){
                    this.fireEvent("makeTableFinish");
                }, this);
                tableModel.reloadData();
            }
        }
    }
});