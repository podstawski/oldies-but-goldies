qx.Class.define("frontend.lib.ui.table.cellrenderer.ListCell",
{
    extend : qx.ui.table.cellrenderer.Abstract,

    properties :
    {
        container :
        {
            check : "qx.ui.core.Widget",
            apply : "_applyContainer"
        },

        maxButtonsInRow :
        {
            check : "PositiveInteger",
            init : 3
        }
    },

    members :
    {
        cellInfo   : null,

        __leftContainer : null,

        __rightContainer : null,

        __buttonsContainer : null,

        __titleContainer : null,

        __leftRow  : null,

        __rightRow : null,

        __buttonRow : null,

        __mapIcons :
        {
            "edit"              : "icon/22/actions/edit-paste.png",
            "delete"            : "icon/22/actions/edit-delete.png",
            "dearchiveSurvey"   : "icon/22/actions/edit-delete.png",
            "sendSurvey"        : "icon/22/actions/document-send.png",
            "sendQuiz"          : "icon/22/actions/document-send.png",
            "finishSurvey"      : "icon/22/actions/edit-delete.png",
            "fillSurvey"        : "icon/22/actions/document-properties.png",
            "surveyResults"     : "icon/22/actions/view-sort-ascending.png",
            "quizResults"       : "icon/22/actions/view-sort-ascending.png",
            "surveyAddToLibrary": "icon/22/actions/list-add.png",
            "copyToMySurveys"   : "icon/22/actions/edit-copy.png",
            "copyToMyTests"     : "icon/22/actions/edit-copy.png",
            "copyToProject"     : "icon/22/actions/edit-copy.png",
            "archiveSurvey"     : "icon/22/actions/list-add.png",
            "detailedResults"   : "icon/22/actions/view-sort-ascending.png",
            "averageResults"    : "icon/22/actions/view-sort-ascending.png",
            "details"           : "icon/22/actions/edit-select-all.png",
            "summary"           : "icon/22/actions/edit-select-all.png",
            "groupSummary"      : "icon/22/actions/edit-select-all.png",
            "download"          : "icon/22/actions/mail-receive.png",
            "generateReport"    : "icon/22/actions/mail-receive.png",
            "resumeSurvey"      : "icon/22/actions/view-refresh.png",
            "calendar"          : "icon/22/mimetypes/office-calendar.png",
            "runQuiz"           : "icon/22/categories/games.png",
            "googleCalendar"    : "icon/22/actions/address-book-new.png"
        },

        __mapEvents :
        {

        },

        __nextColumn : 1,

        _applyContainer : function(value, old)
        {
            this.__leftContainer = this._createDefaultContainer(function(){
                this.setColumnFlex(1, 1);
                this.setColumnAlign(0, "left", "middle");
                this.setColumnAlign(1, "left", "middle");
            });
            this.__rightContainer = this._createDefaultContainer(function(){
                this.setColumnAlign(0, "left", "middle");
                this.setColumnAlign(1, "left", "middle");
            });
            this.__buttonsContainer = this._createDefaultContainer(function(){
                this.setSpacingX(10);
            });
            this.__titleContainer   = this._createDefaultContainer();

            var layout = new qx.ui.layout.Grid();
            layout.setSpacing(5);
            layout.setColumnFlex(2, 1);
            layout.setColumnWidth(0, 25);
            value.setLayout(layout);

            var rightContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(5));
            rightContainer.add(this.__buttonsContainer);
            var dynks = new qx.ui.container.Composite(new qx.ui.layout.HBox(0, "right"));
            dynks.add(this.__rightContainer);
            rightContainer.add(dynks);

            value.add(this.__titleContainer, { row : 0, column : 1, colSpan : 3 });
            value.add(this.__leftContainer, { row : 1, column : 1 });
            value.add(rightContainer, { row : 1, column : 3 });

            this.cellInfo    = null;
            this.__leftRow   = 0;
            this.__rightRow  = 0;
            this.__buttonRow = [ 0 ];

            if (!old)
            {
                this.createDataCellHtml = qx.lang.Function.bind(function(cellInfo)
                {
                    this.cellInfo = cellInfo;
                    var rowData   = cellInfo.rowData;

                    if (!rowData || rowData.id === undefined) {
                        return;
                    }
                    
                    var table = this.cellInfo.table;
                    table.getRenderer().call(this, rowData);

                    if (table.getShowCheckboxes()) {
                        var checkbox = new frontend.lib.ui.form.CheckBox().set({
                            value : table.isSelectedRow(rowData.id),
                            anonymous : true
                        });

                        this._add(this.getContainer(), checkbox, 0, 0, 1, this.__leftRow == 0 && this.__rightRow == 0 ? 1 : 2);
                    }
                }, this);
            }
        },

        _createDefaultContainer : function(callback)
        {
            var layout = new qx.ui.layout.Grid();
            layout.setSpacing(5);
            if (typeof callback == "function") {
                callback.call(layout);
            }
            return (new qx.ui.container.Composite(layout));
        },

        createDataCellHtml : function()
        {
            throw new Error("Container is not set!");
        },

        getRowsCount : function()
        {
            return Math.max(this.__leftRow, this.__rightRow) - 1;
        },

        _add : function(container, value, row, column, colSpan, rowSpan)
        {
            var element = value;

            if (typeof value === "string" || typeof value === "number") {
                element = new qx.ui.basic.Label(new String(value));
            }

            container.add(element, {
                row     : row,
                column  : column,
                colSpan : colSpan || 1,
                rowSpan : rowSpan || 1
            });

            return element;
        },

        addTitle : function(value)
        {
            this._add(this.__titleContainer, value, 0, 0).set({
                appearance : "list-title"
            });
        },

        addLeft : function(value, label)
        {
            if (label) {
                this._add(this.__leftContainer, label + ":", this.__leftRow, 0);
                this._add(this.__leftContainer, value, this.__leftRow, 1);
            } else {
                this._add(this.__leftContainer, value, this.__leftRow, 0, 2);
            }
            this.__leftRow++;
        },

        addRight : function(value, label)
        {
            if (label) {
                this._add(this.__rightContainer, label + ":", this.__rightRow, 0);
                this._add(this.__rightContainer, value, this.__rightRow, 1);
            } else {
                this._add(this.__rightContainer, value, this.__rightRow, 0, 2);
            }
            this.__rightRow++;
        },

        addButton : function(value)
        {
            var key;
            if ((key = qx.lang.Object.getKeyFromValue(this.__mapEvents, value + "RowClick")) && key != value) {
                throw new Error(qx.lang.String.format("Event '%1' is already assign to other button named '%2'!", [value + "RowClick", key]));
            }
            var table   = this.cellInfo.table;
            var rowData = this.cellInfo.rowData;

//            var button  = new qx.ui.form.Button(Tools["tr"](value), this.__mapIcons[value]).set({
//                appearance : "table-action-button"
//            });

            var button  = new qx.ui.basic.Image(this.__mapIcons[value]);
            button.setToolTipText(Tools["tr"](value));
            button.setCursor("pointer");

            button.addListener("click", function(e){
                table.fireDataEvent(this.__mapEvents[value] || (value + "RowClick"), rowData);
            }, this);

            var row = this.__buttonRow.length - 1;
            if (this.__buttonRow[row] == this.getMaxButtonsInRow()) {
                this.__buttonRow.push(0);
                row++;
            }
            this._add(this.__buttonsContainer, button, row, this.__buttonRow[row]++);
        }
    }
});