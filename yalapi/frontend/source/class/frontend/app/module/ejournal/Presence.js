qx.Class.define("frontend.app.module.ejournal.Presence",
{
    extend : frontend.app.module.ejournal.Abstract,

    events :
    {
        "updatedUserPresence" : "qx.event.type.Event"
    },

    construct : function(main)
    {
        this.base(arguments, main);

        this.__df = new frontend.lib.util.format.DbDateFormat();

        this.addListener("makeTableFinish", this._calcAvgPresence, this);
        this.addListener("updatedUserPresence", this._calcAvgPresence, this);
    },

    members :
    {
        _url : "GROUP_PRESENCE",

        __users : null,

        __lessons : null,

        __df : null,

        __tooltipArr :
        [
            "coach_name", "course_name", "unit_name", "tc_name", "tc_adress", "room_name", "start_date", "end_date"
        ],
        
        _makeTable : function(data)
        {
            var canEdit = Acl.hasRight("lesson_presence.U");
            
            if (this.__table) {
                if (canEdit) {
                    this.__table.removeListener("cellClick", this._onTableCellClick, this);
                    this.__table.removeListener("headerCheckboxClick", this._onTableHeaderClick, this);
                }
            }

            var group      = data.group;
            this.__users   = data.users;
            this.__lessons = data.lessons  || {};
            var presence   = data.presence || {};

            var rowsData = [];

            for (var userID in this.__users) {
                var rowData =
                {
                    user_id        : userID,
                    username       : this.__users[userID],
                    presence_avg   : 0
                };
                for (var lessonID in this.__lessons) {
                    rowData[lessonID] = !!(presence[userID] && presence[userID][lessonID])
                }
                rowsData.push(rowData);
            }

            var tableModel = new qx.ui.table.model.Simple;

            var columnNames = ["nazwa użytkownika", "% obecności"];
            var columnIds   = ["username", "presence_avg"];

            var today = (new Date()).valueOf();

            var closest = {};
            closest.columnIndex = null;
            closest.diff = null;

            var order = new qx.data.Array(data.order);
            order.forEach(function(lessonID) {
                var lessonDate = this.__lessons[lessonID].start_date;
                var diff = Math.abs(today - this.__df.parse(lessonDate).valueOf());
                if (closest.columnIndex == null || diff < closest.diff) {
                    closest.columnIndex = columnNames.length;
                    closest.diff = diff;
                }
                columnNames.push(this.__getColumnName(lessonDate));
                columnIds.push(lessonID);
            }, this);

            tableModel.setColumns(columnNames, columnIds);
            tableModel.setDataAsMapArray(rowsData, true);
            tableModel.setCaseSensitiveSorting(false);
            tableModel.sortByColumn(0, true);

            var table = new frontend.lib.list.dynamic.Table(tableModel).set({
                metaColumnCounts : [ 1, -1 ]
            });

            var tableColumnModel = table.getTableColumnModel();

            var avgCellRenderer = new qx.ui.table.cellrenderer.Number;
            var avgNumberFormat = new qx.util.format.NumberFormat;
            avgNumberFormat.set({
                maximumFractionDigits : 0,
                minimumFractionDigits : 0,
                postfix : "%"
            });
            avgCellRenderer.setNumberFormat(avgNumberFormat);
            tableColumnModel.setDataCellRenderer(1, avgCellRenderer);

            for (var columnIndex = 2, columnCount = columnNames.length; columnIndex < columnCount; columnIndex++)
            {
                var headerCellRenderer = new frontend.lib.ui.table.headerrenderer.Boolean;
                var tooltip = this._getTooltipText(this.__lessons[tableModel.getColumnId(columnIndex)]);
                headerCellRenderer.setToolTip(tooltip);
                headerCellRenderer.setCanEdit(canEdit);
                tableColumnModel.setHeaderCellRenderer(columnIndex, headerCellRenderer);
                tableColumnModel.setDataCellRenderer(columnIndex, new qx.ui.table.cellrenderer.Boolean);
                tableColumnModel.setColumnWidth(columnIndex, 90);
            }
            tableColumnModel.setColumnWidth(0, 250);
            table.setHeaderCellHeight(40);

            if (canEdit) {
                table.addListener("cellClick", this._onTableCellClick, this);
                table.addListener("headerCheckboxClick", this._onTableHeaderClick, this);
            }

            if (closest.columnIndex != null) {
                table.setFocusedCell(closest.columnIndex, 0, true);
            }

            return table;
        },

        __getColumnName : function(lessonDate)
        {
            return this.__df.format(lessonDate, "dd MMM")
                 + "<br />"
                 + this.__df.format(lessonDate, "HH:mm");
        },

        _getTooltipText : function(lesson)
        {
            var html = [];
            html.push('<table>');

            this.__tooltipArr.forEach(function(key){
                var value = lesson[key];
                if (value) {
                    html.push(
                        '<tr><td style="text-align:left;">',
                        Tools["tr"]("table.presence:" + key),
                        '</td><td>:</td><td style="text-align:right;">',
                        value,
                        '</td></tr>'
                    );
                }
            }, this);
            html.push('</table>');
            return html.join("");
        },

        _updateUserPresence : function(columnIndex, rowIndex, present)
        {
            var tableModel = this.__table.getTableModel();
            var columnId   = tableModel.getColumnId(columnIndex);

            if (rowIndex != null && present == null) {
                present = !tableModel.getValueById(columnId, rowIndex);
            }

            if (present != null) {
                var request = new frontend.lib.io.HttpRequest(Urls.resolve(this._url), "POST");
                request.addListenerOnce("success", function(e){
                    if (rowIndex != null) {
                        tableModel.setValueById(columnId, rowIndex, present);
                    } else {
                        for (var index = 0, rowCount = tableModel.getRowCount(); index < rowCount; index++) {
                            tableModel.setValueById(columnId, index, present);
                        }
                    }
                    this.fireEvent("updatedUserPresence");
                }, this);
                request.setRequestData({
                    lesson_id : columnId,
                    user_id   : rowIndex != null ? tableModel.getRowDataAsMap(rowIndex).user_id : qx.lang.Object.getKeys(this.__users),
                    present   : present ? 1 : 0
                });
                request.send();
            }
        },

        _calcAvgPresence : function()
        {
            var tableModel = this.__table.getTableModel();
            var tableColumnModel = this.__table.getTableColumnModel();
            var lessonCount = qx.lang.Object.getLength(this.__lessons);

            if (lessonCount) {
                var rowCount = tableModel.getRowCount();
                var userCount = {};
                for (var rowIndex = 0; rowIndex < rowCount; rowIndex++) {
                    var count = 0;
                    for (var lessonID in this.__lessons) {
                        if (userCount[lessonID] == null) {
                            userCount[lessonID] = 0;
                        }
                        if (tableModel.getValueById(lessonID, rowIndex) == true) {
                            count++;
                            userCount[lessonID]++;
                        }
                    }
                    tableModel.setValueById("presence_avg", rowIndex, new Number(count * 100 / lessonCount).toFixed(2));
                }

                for (var lessonID in userCount) {
                    var columnIndex = tableModel.getColumnIndexById(lessonID);
                    var value = (userCount[lessonID] == rowCount ? true : (userCount[lessonID] == 0 ? false : null));
                    tableColumnModel.getHeaderCellRenderer(columnIndex).checkbox.setValue(value);
                }
            }
        },

        _onTableHeaderClick : function(e)
        {
            var columnIndex = e.getData().col;
            var present     = !!e.getData().checked;
            this._updateUserPresence(columnIndex, null, present);
        },

        _onTableCellClick : function(e)
        {
            var rowIndex    = e.getRow();
            var columnIndex = e.getColumn();
            this._updateUserPresence(columnIndex, rowIndex);
        }
    }
});