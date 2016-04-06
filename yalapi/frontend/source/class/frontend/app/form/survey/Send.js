qx.Class.define("frontend.app.form.survey.Send",
{
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData, type)
    {
        this._id = rowData.id;
        this._type = type;
        this.base(arguments);
        this._initialize();

        this.setCaption(Tools['tr']("form.survey.send:caption"));
        this.setLayout(new qx.ui.layout.VBox(5));

        this._createMainContainer();
        this._createGroupBoxGroups(rowData.id);

        var container = new qx.ui.container.Composite(new qx.ui.layout.HBox(5, "right"));

        container.add(this._createButton('Cancel'));
        container.add(this._createButton('Send'));

        this._container.add(container);

        this._addBehaviours();
        this._controls['buttonSend'].set({enabled:false});
    },

    members :
    {
        _validationManager  : null,
        _id : null,
        _selectedRows : null,
        _selectedRowsKeys : null,
        _type : null,
        _controls           : null,
        _container          : null,
        _tableDefaultHeight : 150,
        _gridClass          : frontend.app.grid.GroupsSend,

        _initialize : function()
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
            var dim = Tools.dimensions(0.5, 0.5);
            this.set({
                width   : dim.w
            });
            var scroll = new qx.ui.container.Scroll().set({
                width   : dim.w,
                height  : dim.h
            });

            this._container = new qx.ui.container.Composite(new qx.ui.layout.VBox(5));
            this.add(this._container);
        },

        _createGroupBoxGroups : function(surveyId)
        {
            this._controls['groupBoxUsers'] = new qx.ui.groupbox.GroupBox(Tools.tr("form.survey.group.select:caption"));
            var gridLayout = new qx.ui.layout.VBox(5);

            this._controls['groupBoxUsers'].setLayout(gridLayout);

            this._controls['groupBoxUsers'].add(this._createGridGroups(surveyId));

            this._container.add(this._controls['groupBoxUsers']);
            return this._controls['groupBoxUsers'];

        },

        _createGridGroups : function(surveyId)
        {
            var grid = this._controls['gridGroups'] = new this._gridClass(surveyId);
            var table = this._controls['gridGroups'].getChildControl("table");
            table.addListener('changeRowSelectedCount', function(e){
                var enabled = false;
                this._controls['buttonSend'].set({enabled:false});

                for(var item in table.getSelectedRows()){
                    if (typeof(item) !== "function"){
                        this._controls['buttonSend'].set({enabled:true});
                    }
                }

            }, this);
            table.set({showEditRemove: false});
            this._controls['gridGroups'].setMargin(0);
            table.setHeight(this._tableDefaultHeight);


            grid.getChildControl("toolbar").getChildControl("delete-selected-button").addListener("appear", function(e){
                this.setVisibility("excluded");
            });

            grid.getChildControl("toolbar").getChildControl("add-button").setVisibility("excluded");

            grid.getChildControl("toolbar").getChildControl("searchbox").set({placeholder: "wyszukaj po grupie..."});


            return this._controls['gridGroups'];
        },

        _createButton: function(label)
        {
            label = "button" + label;
            this._controls[label] = new qx.ui.form.Button(Tools['tr']("form.group.add." + label));
            return this._controls[label];
        },

        _send : function()
        {
            var groupUsers = this._controls['gridGroups'].getTable().getSelectedRows();
            var groups = [];
            var format = new qx.util.format.DateFormat("d-M-y");

            for(var item in groupUsers){
                if (typeof(item) !== "function"){
                    groups.push({id: groupUsers[item].id,
                                deadline: format.format(new Date(groupUsers[item].deadline))
                    });
                }
            }

            var data = {};
            data.groups = groups;
            data.surveyId = this._id;

            var request = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY"), "POST");
            request.setRequestData({method:"send", data:qx.util.Serializer.toJson(data)});

            request.addListener("success", function( e ) {
                this.showMessage(this._type == "survey" ? Tools.tr("survey.sent") : Tools.tr("test.send"));
                this.close();
                this._validationManager.fireEvent("completed");
            }, this );

            request.send();
        },

        _addBehaviours : function()
        {
            var usersGroupsTable    = this._controls['usersGroupsTable'];

            this._controls['buttonCancel'].addListener('execute', this._onButtonCancelClick, this);
            this._controls['buttonSend'].addListener('execute', this._onButtonSaveClick, this);

            this._validationManager.addListener("complete", function() {
                if( this._validationManager.isValid() ) { this._send(); }
            }, this);
        },

        _onButtonSaveClick : function()
        {
            this._validationManager.validate();
        },

        _onButtonCancelClick : function()
        {
            this.close();
        }
    }
})