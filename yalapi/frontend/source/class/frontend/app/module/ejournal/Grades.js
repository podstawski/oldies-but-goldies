qx.Class.define("frontend.app.module.ejournal.Grades",
{
    extend : frontend.app.module.ejournal.Abstract,

    include : [
        frontend.lib.ui.MSearchBox,
        frontend.lib.util.MGetSource
    ],

    construct : function(main)
    {
        this.base(arguments, main);

        var toolbar   = main.getChildControl("toolbar");
        var searchbox = this.getChildControl("searchbox");
        var addbutton = this.getChildControl("addbutton");

        toolbar.add(searchbox);
        toolbar.add(addbutton);

        this.bind("visibility", searchbox, "visibility");
        if (Acl.hasRight("exam_grades.C")) {
            this.bind("visibility", addbutton, "visibility");
        }

        var options = { converter : function(value) { return !!value; } }
        main.bind("unitId", searchbox, "enabled", options);
        main.bind("unitId", addbutton, "enabled", options);

        this.addListener("makeTableFinish", this._calculateAvgGrades, this);
    },

    members :
    {
        _url : "GROUP_GRADES",
        
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "addbutton":
                    control = new frontend.lib.ui.form.Button("Dodaj", "icon/16/actions/list-add.png").set({
                        margin   : 0,
                        minWidth : 100,
                        enabled  : false,
                        toolTipText : "Dodaj kategorię oceny"
                    });
                    control.exclude();
                    control.addListener("execute", this._onAddButtonClick, this);
                    break;

                case "searchbox":
                    control = new frontend.lib.ui.SearchBox().set({
                        enabled     : false,
                        placeholder : "wyszukaj sprawdzian..."
                    });
                    break;
            }

            return control || this.base(arguments, id);
        },

        _onAddButtonClick : function(e)
        {
            var win = new frontend.app.form.Exam;
            win.getForm().addListener("completed", this.reloadData, this);
            win.getForm().createHiddenInput("course_unit_id", this.__main.getUnitId());
            win.open();
        },

        _makeTable : function(data)
        {
            var canEdit = Acl.hasRight("exam_grades.U");
            
            if (this.__table) {
                if (canEdit) {
                    this.__table.removeListener("gradeHeaderClick", this._onGradeHeaderClick, this);
                    this.__table.removeListener("dataEdited", this._onTableDataEdited, this);
                    this.__table.removeListener("dataEdited", this._calculateAvgGrades, this);
                }
            }

            var group  = data.group;
            var users  = data.users;
            var exams  = data.exams  || {};
            var grades = data.grades || {};

            var rowsData = [];

            for (var userID in users) {
                var rowData = {
                    user_id   : userID,
                    username  : users[userID],
                    grade_avg : "-"
                };
                var grade;
                for (var examID in exams) {
                    grade = 0;
                    if (grades[userID] && grades[userID][examID]) {
                        grade = grades[userID][examID];
                    }
                    rowData[examID] = grade;
                }
                rowsData.push(rowData);
            }

            var df = new frontend.lib.util.format.DbDateFormat();

            var tableModel = new qx.ui.table.model.Simple;

            var columnNames = ["nazwa użytkownika", "średnia ocen"];
            var columnIds   = ["username", "grade_avg"];

            var order = new qx.data.Array(data.order);
            order.forEach(function(examID){
                columnNames.push(df.format(exams[examID].created_date + " 00:00:00", "dd MMM") + "<br />" + exams[examID].name);
                columnIds.push(examID);
            }, this);

            tableModel.setColumns(columnNames, columnIds);
            tableModel.setDataAsMapArray(rowsData, true);
            tableModel.setCaseSensitiveSorting(false);
            tableModel.sortByColumn(0, true);

            var table = new frontend.lib.list.dynamic.Table(tableModel).set({
                metaColumnCounts : [ 2, -1 ]
            });
            var tableColumnModel = table.getTableColumnModel();
            var tmp = new qx.ui.table.cellrenderer.Html("center", null, null, "bold");
            tmp.addNumericCondition(">", 0);
            tableColumnModel.setDataCellRenderer(1, tmp);

            for (var columnIndex = 2, columnCount = columnNames.length; columnIndex < columnCount; columnIndex++)
            {
                if (canEdit) {
                    tableModel.setColumnEditable(columnIndex, true);
                    tableColumnModel.setCellEditorFactory(columnIndex, new frontend.lib.ui.table.celleditor.Grade);
                }
                tableColumnModel.setDataCellRenderer(columnIndex,  new frontend.lib.ui.table.cellrenderer.Grade);

                var headerCellRenderer = new frontend.lib.ui.table.headerrenderer.Grade();
                headerCellRenderer.setExam(exams[tableModel.getColumnId(columnIndex)]);
                headerCellRenderer.setCanEdit(canEdit);
                tableColumnModel.setHeaderCellRenderer(columnIndex, headerCellRenderer);
                tableColumnModel.setColumnWidth(columnIndex, 90);
            }

            tableColumnModel.setColumnWidth(0, 250);
            table.setHeaderCellHeight(40);

            if (canEdit) {
                table.addListener("gradeHeaderClick", this._onGradeHeaderClick, this);
                table.addListener("dataEdited", this._onTableDataEdited, this);
                table.addListener("dataEdited", this._calculateAvgGrades, this);
            }

            this.disconnectFromSearchBox();
            var searchbox = this.getChildControl("searchbox").set({
                enabled : true
            });
            this.connectToSearchBox(searchbox);

            return table;
        },

        _onGradeHeaderClick : function(e)
        {
            var columnIndex = e.getData();
            if (columnIndex < 2) {
                return;
            }
            var exam = this.__table.getTableColumnModel().getHeaderCellRenderer(columnIndex).getExam();

            var win = new frontend.app.form.Exam(exam);
            win.getForm().addListener("completed", this.reloadData, this);
            win.open();
        },

        _onChangeSearchValue : function(value, old)
        {
            var value = this.getSearchValue();

            var tableModel       = this.__table.getTableModel();
            var tableColumnModel = this.__table.getTableColumnModel();

            for (var columnIndex = 2, columnCount = tableModel.getColumnCount(); columnIndex < columnCount; columnIndex++)
            {
                var columnName = tableModel.getColumnName(columnIndex);
                var tooltip    = qx.lang.String.stripTags(tableColumnModel.getHeaderCellRenderer(columnIndex).getToolTip() || "");

                tableColumnModel.setColumnVisible(columnIndex,
                    !value || qx.lang.String.contains(columnName, value) || qx.lang.String.contains(tooltip, value)
                );
            }
        },

        _calculateAvgGrades : function()
        {
            var tableModel  = this.__table.getTableModel();
            var rowsData    = tableModel.getDataAsMapArray();
            var columnCount = tableModel.getColumnCount();

            var examAvg = {};

            rowsData.forEach(function(rowData, rowIndex){
                var sum = 0, count = 0, columnId;
                for (var columnIndex = 2; columnIndex < columnCount; columnIndex++) {
                    columnId = tableModel.getColumnId(columnIndex);
                    if (examAvg[columnId] == null) {
                        examAvg[columnId] = new qx.data.Array;
                    }
                    if (rowData[columnId]) {
                        examAvg[columnId].push(new Number(rowData[columnId]));
                        sum += new Number(rowData[columnId]);
                        count++;
                    }
                }
                tableModel.setValueById("grade_avg", rowIndex, count ? new Number(sum / count).toFixed(2) : '-');
            }, this);

            var tableColumnModel = this.__table.getTableColumnModel();

            for (var examID in examAvg) {
                var gradeCount = examAvg[examID].getLength();
                if (gradeCount > 0) {
                    var avg = new Number(
                        examAvg[examID].sum() / gradeCount
                    ).toFixed(2);
                    var headerCellRenderer = tableColumnModel.getHeaderCellRenderer(
                        tableModel.getColumnIndexById(examID)
                    );
                    var exam = headerCellRenderer.getExam();
                    headerCellRenderer.setToolTip(
                        qx.lang.String.format(
                            "%1 - średnia ocen: %2 (%3)", [
                                exam.name, avg, this.getSourceInstance("Grades").getClosestGrade(avg)
                            ]
                        )
                    );
                }
            }
        },

        _onTableDataEdited : function(e)
        {
            var data = e.getData();
            var tableModel = this.__table.getTableModel();

            var request = new frontend.lib.io.HttpRequest().set({
                url : Urls.resolve(this._url),
                method : "POST",
                requestData :
                {
                    user_id : tableModel.getRowData(data.row).user_id,
                    exam_id : tableModel.getColumnId(data.col),
                    grade   : data.value
                },
                showLoadingDialog : true
            });
            request.send();
        }
    }
});