qx.Class.define("frontend.app.form.group.Add",
{
    extend : frontend.lib.ui.window.Modal,
    
    include : [
        frontend.MMessage
    ],

    construct : function()
    {
        this.base(arguments);
        this._initalize();

        this.setCaption(Tools['tr']("form.group.add:caption"));
        this.setLayout(new qx.ui.layout.VBox(5));

        this._createMainContainer();
        this._createGroupBoxInfo();
        this._createGroupBoxUsers();
        this._createGroupBoxUsersGroups();

        var container = new qx.ui.container.Composite(new qx.ui.layout.HBox(5, "right"));
        container.add(this._createButton('Cancel'));
        container.add(this._createButton('Save'));

        this._container.add(this._controls['groupBoxInfo']);
        this._container.add(this._controls['groupBoxUsersGroups']);
        this._container.add(this._controls['groupBoxUsers']);
        this._container.add(container);

        this._addBehaviours();

        console.log("dasdasdas");
    },

    members :
    {
        _validationManager  : new qx.ui.form.validation.Manager(),

        _selectedRows : null,
        _selectedRowsKeys : null,

        _controls           : null,
        _container          : null,
        _tableDefaultHeight : 150,

        _initalize : function()
        {
           this.setResizable(true);
           this._selectedRows = {};
           this._selectedRowsKeys = [];
           this._controls = [];
           this._container = null;

           this._validationManager = new qx.ui.form.validation.Manager();
        },

        getForm : function()
        {
            return this._validationManager;
        },

        _createMainContainer : function()
        {
            var dim = Tools.dimensions(0.75, 0.65);
            var scroll = new qx.ui.container.Scroll().set({
                width   : dim.w,
                height  : dim.h
            });

            this._container = new qx.ui.container.Composite(new qx.ui.layout.VBox(5));
            this.add(this._container);
        },

        _createGroupBoxInfo : function()
        {
            this._controls['groupBoxInfo'] = new qx.ui.groupbox.GroupBox(Tools.tr("form.group.add.groupbox.usersInfo:caption"));
            this._controls['groupBoxInfo'].setLayout(new qx.ui.layout.VBox(10));

            var container;
            container = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
            container.add(new qx.ui.basic.Label(Tools.tr("form.group.add.groupbox.usersInfo:name")));
            container.add(this._createTextField());
            container.add(new qx.ui.basic.Label(Tools.tr("form.group.add.groupbox.usersInfo:level")));
            container.add(this._createSelectBox());
            this._controls['groupBoxInfo'].add(container);

            if (this._getShowGoogleGroupIdField()) {
                container = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
                container.add(new qx.ui.basic.Label(Tools.tr("form.group.add.groupbox.usersInfo:google_group_id")));
                container.add(this._createGoogleGroupIdField());
                container.add(new qx.ui.basic.Label("@" + frontend.app.Login.getInstance().getDomain()));
                this._controls['groupBoxInfo'].add(container);
            }

            return this._controls['groupBoxInfo'];
        },

        _createGroupBoxUsers : function()
        {
            this._controls['groupBoxUsers'] = new qx.ui.groupbox.GroupBox(Tools.tr("form.group.add.groupbox.users:caption"));
            var gridLayout = new qx.ui.layout.VBox(5);

            this._controls['groupBoxUsers'].setLayout(gridLayout);

            this._controls['groupBoxUsers'].add(this._createGridUsers());
            this._controls['groupBoxUsers'].add(this._createButton('Add'));

            return this._controls['groupBoxUsers'];

        },

        _createGridUsers : function()
        {
            this._controls['gridUsers'] = new frontend.app.grid.Users();
            var table = this._controls['gridUsers'].getChildControl("table");

            this._controls['gridUsers'].setMargin(0);
            table.setHeight(this._tableDefaultHeight);

            return this._controls['gridUsers'];
        },

        _createGroupBoxUsersGroups : function()
        {
            this._controls['groupBoxUsersGroups'] = new qx.ui.groupbox.GroupBox(Tools.tr("form.group.add.groupbox.usersGroups:caption"));
            var gridLayout = new qx.ui.layout.VBox(5);

            this._controls['groupBoxUsersGroups'].setLayout(gridLayout);

            this._controls['groupBoxUsersGroups'].add(this._createUsersGroupsTable());
            this._controls['groupBoxUsersGroups'].add(this._createButton('Delete'));

            return this._controls['groupBoxUsersGroups'];
        },

        _createUsersGroupsTable : function()
        {
            this._controls['usersGroupsTable'] = new frontend.lib.ui.table.Grid();
            var tableModel = new frontend.lib.ui.table.model.Simple(),
                tableModelUsers =  this._controls['gridUsers'].getChildControl("table").getTableModel(),
                columns = tableModelUsers.getColumns(),
                keys = tableModelUsers.getKeys();

            tableModel.setColumns(columns, keys);
            this._controls['usersGroupsTable'].setTableModel(tableModel);
            this._controls['usersGroupsTable'].setHeight(this._tableDefaultHeight);
            this._controls['usersGroupsTable'].setRowHeight(35);
            this._controls['usersGroupsTable'].setShowEditRemove(false);
            return this._controls['usersGroupsTable'];
        },

        _createButton: function(label)
        {
            label = "button" + label;
            this._controls[label] = new qx.ui.form.Button(Tools['tr']("form.group.add." + label));
            return this._controls[label];
        },

        _createSelectBox : function()
        {
            this._controls['selectBox'] = new frontend.lib.ui.form.SelectBox();

            this._controls['selectBox'].setMargin(0);
            this._controls['selectBox'].setMaxWidth(Tools.dimensions(0.75, 0.65).w - 35);
            this._controls['selectBox'].setSource( "Levels" );
            this._controls['selectBox'].setRequired(true);

            return this._controls['selectBox'];
        },

        _createTextField : function()
        {
            this._controls['textField'] = new qx.ui.form.TextField();

            this._controls['textField'].setRequired(true);
            this._controls['textField'].setMargin(0);

            this._validationManager.add(this._controls['textField']);
            return this._controls['textField'];
        },

        _getShowGoogleGroupIdField : function()
        {
            return frontend.app.Login.getDomain() && this.classname.split(".").pop() == "Add";
        },

        _createGoogleGroupIdField : function()
        {
            this._controls['googleGroupIdField'] = new qx.ui.form.TextField();
            this._controls['googleGroupIdField'].setMargin(0);
            this._controls['googleGroupIdField'].setRequired(true);

            this._validationManager.add(this._controls['googleGroupIdField']);

            return this._controls['googleGroupIdField'];
        },

        _addGroup : function()
        {
            var groupLevel = this._controls['selectBox'].getSelection()[0].getModel();

            if( groupLevel == null)
            {
                new frontend.lib.dialog.Message(Tools.tr("form.group.add.message:select-level"));
            }
            else
            {
                var groupUsers = this._controls['usersGroupsTable'].getTableModel().getData(),
                    groupName = this._controls['textField'].getValue(),
                    length = groupUsers.length,
                    groupUsersId = [];

                    for( var i = 0; i < length; i++)
                    {
                        groupUsersId.push(groupUsers[i].id);
                    }

                    var data = { name: groupName, advanceLevel : groupLevel, members : groupUsersId.length, users: groupUsersId };

                var request = new frontend.lib.io.HttpRequest( Urls.resolve('GROUPS'), 'POST' );
                request.setRequestData(data);
                request.addListener("success", function( e ) {
                    new frontend.lib.dialog.Message(Tools['tr']("form.group.add.message:group-and-users-added"));
                    this.close();
                    this._validationManager.fireEvent("completed");
                }, this );
                request.send();
            }
        },

        _addBehaviours : function()
        {
            var gridUsersTable      = this._controls['gridUsers'].getChildControl("table");
            var usersGroupsTable    = this._controls['usersGroupsTable'];

            gridUsersTable.addListener("headerCheckboxClick", this._onHeaderCheckboxClick, this);
            gridUsersTable.addListener("cellClick", this._onGridUsersTableCellClick, this);
            gridUsersTable.addListener("cellDblclick", this._onGridUsersTableCellClick, this);

            usersGroupsTable.addListener("cellClick", this._onUserGroupsTableCellClick, this);
            usersGroupsTable.addListener("cellDblclick", this._onUserGroupsTableCellClick, this);

            this._controls['buttonAdd'].addListener('execute', this._onButtonAddClick, this);
            this._controls['buttonDelete'].addListener('execute', this._onButtonDeleteClick, this);

            this._controls['buttonCancel'].addListener('execute', this._onButtonCancelClick, this);
            this._controls['buttonSave'].addListener('execute', this._onButtonSaveClick, this);

            this._validationManager.addListener("complete", function() {
                if( this._validationManager.isValid() ) {
                    var value = this._controls['selectBox'].getSelection()[0].getModel();
                    if(value === "null") { this._controls['selectBox'].setValid(false); return; } else { this._addGroup(); }
                }
            }, this);
        },

        _onButtonSaveClick : function()
        {
            this._validationManager.validate();
        },

        _onButtonCancelClick : function()
        {
            this.close();
        },

        _onHeaderCheckboxClick : function(e)
        {
            var gridUsersTableModel = this._controls['gridUsers'].getChildControl("table").getTableModel();

            for( var i = 0; i < gridUsersTableModel.getRowCount(); i++)
            {
                var row = gridUsersTableModel.getRowData(i);

                if (typeof(this._selectedRows[row.id]) !== 'undefined') {
                    delete this._selectedRows[row.id];
                }
                else {
                    this._selectedRows[row.id] = row;
                }
            }
        },

        _onGridUsersTableCellClick : function(e)
        {
            var gridUsersTableModel = this._controls['gridUsers'].getChildControl("table").getTableModel();

            if((e.getType() == "cellDblclick") || ((e.getType() == "cellClick") && (e.getColumn() === 0)))
            {
                var row = gridUsersTableModel.getRowData(e.getRow());
    
                if (typeof(this._selectedRows[row.id]) !== 'undefined') {
                    delete this._selectedRows[row.id];
                }
                else {
                    this._selectedRows[row.id] = row;
                }
            }
        },

        _onUserGroupsTableCellClick : function(e)
        {
            var rowIndex = e.getRow();

            if((e.getType() == "cellDblclick") || ((e.getType() == "cellClick") && (e.getColumn() === 0)))
            {
                if (this._selectedRowsKeys.indexOf(rowIndex) !== -1) {
                    this._selectedRowsKeys.splice(rowIndex, 1);
                }
                else {
                    this._selectedRowsKeys.push(rowIndex);
                }
            }
        },

        _onButtonAddClick : function()
        {
            var selectedRows = [],
                usersGroupsTableModel = this._controls['usersGroupsTable'].getTableModel(),
                gridUsersTableModel = this._controls['gridUsers'].getChildControl("table").getTableModel(),
                data = qx.lang.Array.clone(usersGroupsTableModel.getData()),
                bufferData = [], isset = false;

            for(var i in this._selectedRows )
            {
                if( typeof this._selectedRows[i] === "object" )
                {
                    isset = false;
                    for( var j = 0; j < data.length; j++ ) {
                        (data[j].id === this._selectedRows[i].id) ? (isset = true) : false;
                    }
                    (!isset) ? bufferData.push(this._selectedRows[i]) : false;
                }
            }

            data = (bufferData.length > 0) ? data.concat(bufferData) : data;
            usersGroupsTableModel.setData(data);
        },

        _onButtonDeleteClick : function()
        {
            var usersGroupsTable = this._controls['usersGroupsTable'],
                usersGroupsTableModel = usersGroupsTable.getTableModel(),

                selectedRows = usersGroupsTable.getSelectedRows(),
                data = usersGroupsTableModel.getData(),
                lengthData = data.length,
                lengthSelected = selectedRows.length;

            for(var i = 0; i < lengthData; i++)
            {
                for(var j in selectedRows)
                {
                    if(data[i].id == selectedRows[j].id )
                    {
                        usersGroupsTable.fireDataEvent("changeRowSelected", {
                            selected : false,
                            rowData  : data[i],
                            rowIndex : i
                        });

                        usersGroupsTableModel.removeRows(i, 1);

                        i--;
                        lengthData--;

                        break;
                    }
                }
            }
        }
    }
})