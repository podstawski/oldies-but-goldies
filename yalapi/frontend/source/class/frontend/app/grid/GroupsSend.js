qx.Class.define("frontend.app.grid.GroupsSend",
{
    extend : frontend.app.grid.Groups,
    construct: function(surveyId)
    {
        this._tableModelUrlParams = {surveyId: surveyId, method:"toSend"};

        this.base(arguments);
        var idx = this._tableModel.getColumnIndexById('deadline');

        this._tableModel.setColumnEditable(idx, true);
        this._tableModel.setColumnSortable(idx, false);
        var table = this._table;
        var tableColumnModel = table.getTableColumnModel();
        tableColumnModel.setCellEditorFactory(idx, new frontend.lib.ui.table.celleditor.GroupSend);

        table.addListener("dataEdited", this._onTableDataEdited, this);
        table.removeListener("cellDblClick", table._onCellClick(true));

        this._dataFormatters['deadline'] = function(rowData) {
            //var levelRow = source.getById(rowData.deadline);
            if (rowData.deadline == '1970-01-01'){
                return 'Brak';
            }
            
        };
    },
    members :
    {
        _configureActions : function() {
            return;
        },
        _onTableDataEdited : function(e){
        },
        _addActions : false,
        _tableKeys                  : ["name", "members", "advance_level", 'deadline', 'sent','username'],
        _tableColumnNames           : ["Nazwa", 'Uczestników', 'Poziom', 'Termin wypełnienia', 'Wysłano', 'Autor'],
        _alias                      : "SURVEYS",
        _tableModelUrl              : "SURVEY_GROUPS"
    }
});