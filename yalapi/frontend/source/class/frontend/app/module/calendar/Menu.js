/*
#ignore(scheduler)
*/

qx.Class.define("frontend.app.module.calendar.Menu",
{
    extend : qx.ui.container.Composite,

    construct : function() {
        this.base(arguments);
        this.setLayout(new qx.ui.layout.VBox);

        var layout = new qx.ui.layout.VBox(5);
        this.__content = new qx.ui.container.Composite(layout).set({ padding : 10 });

        this.addListener('filtersLoaded', function() {
            if(this._filters.loaded.coaches && this._filters.loaded.rooms) {
                this._calendar.fireEvent('filtersLoaded');
            }
        }, this);

        this._calendar = qx.core.Init.getApplication().getInnerContent();

        this._calendar.addListenerOnce('courseUnitsLoaded', function() {
            this.__addGroupBoxes();
            this.__addUpperMenuSection();

            this.__addCoachButton();

            this._getRooms();
            this._getCoaches();

            this.__content.add(this._controls['boxRooms']);
            this.__content.add(this._controls['boxCoaches']);
        }, this);

        this.add(this.__content, { flex : 1 });
    },

    members :
    {
        filterNames : ['Coaches', 'Rooms'],
        _filters    : { loaded: {} },
        _controls : [],

        _calendar       : null,
        _initInstance   : null,

        _getCoaches : function()
        {
            var request = new frontend.lib.io.HttpRequest(Urls.resolve('COACHES'), 'GET');
            request.addListener("success", function() {
                this._filters.loaded.coaches = true;
                this._calendar.fireEvent('filterLoaded');

                this._setBox(request.getResponseJson(), 'Coaches', 'username');
            }, this);
            request.send();
        },

        _getRooms : function()
        {
            var id = this._calendar.getContext().training_center_id;
            var request = new frontend.lib.io.HttpRequest(Urls.resolve('ROOMS'), 'GET');
            request.set( { requestData : { 'training_center_id' : id } });
            request.addListener("success", function() {
                this._filters.loaded.rooms = true;
                this._calendar.fireEvent('filterLoaded');

                this._setBox(request.getResponseJson(), 'Rooms', 'name');
            }, this);
            request.send();
        },

        __addUpperMenuSection : function()
        {
            this._controls['btnBack'] = new qx.ui.form.Button(Tools['tr']('calendar.menu.button:back'));
            this._controls['btnBack'].addListener("execute", function(e) {
                qx.core.Init.getApplication().setMenu("app.Menu");
            });

            this._controls['btnAddLesson'] = new qx.ui.form.Button(Tools['tr']('calendar.menu.button:add-lesson'));
            this._controls['btnAddLesson'].addListener("execute", function(e) { scheduler.showLightbox(null, true, false); });

            this.__content.add(this._controls['btnBack']);
            this.__content.add(this._controls['btnAddLesson']);
        },

        __addRoomButton : function()
        {
            this._controls['btnAddRooms'] = new qx.ui.form.Button(Tools.tr("calendar.menu:rooms-add"));
            this._controls['btnAddRooms'].set({ width : 250, marginTop : 15 });
        },

        __addCoachButton : function()
        {
            this._controls['btnAddCoaches'] = new qx.ui.form.Button(Tools.tr("calendar.menu:coaches-add"));
            this._controls['btnAddCoaches'].setMarginTop(15);
            this._controls['btnAddCoaches'].addListener('execute', function(e) {
                var form = new frontend.app.form.user.Add();
                var item = form.getForm().getItem('role_id');

                item.setModelSelection([5]);
                item.setEnabled(false);

                form.center();
                form.open();
            }, this);
        },

        __addGroupBoxes : function()
        {
            var context = this._calendar.getContext();
            var gBoxName = context.training_center_code + ", " + context.city;
            this._controls['boxRooms'] = new qx.ui.groupbox.GroupBox(gBoxName);
            this._controls['boxRooms'].setLayout(new qx.ui.layout.VBox());

            this._controls['boxRoomsTooltip'] = new qx.ui.tooltip.ToolTip(context.training_center_name + ' - ' + context.training_center_full_adress);
            this._controls['boxRoomsTooltip'].set({
                showTimeout : 50,
                position    : "top-right",
                placeMethod : "widget"
            });
            this._controls['boxRooms'].set({ toolTip : this._controls['boxRoomsTooltip'] });

            this._controls['boxCoaches'] = new qx.ui.groupbox.GroupBox(Tools.tr("calendar.menu:coaches"));
            this._controls['boxCoaches'].setLayout(new qx.ui.layout.VBox());
        },

        _createList : function(name, dataKey, data)
        {
            var i, length;
            if(name === 'listCoaches')
            {
                for(i = 0, length = data.length; i < length; i++ )
                {
                    data[i].value = (Tools.objIndexOf(this._calendar._data.courseUnits, 'user_id', data[i].id) !== -1);
                }
            }
            else
            {
                for(i = 0, length = data.length; i < length; i++ ) { data[i].value = true; }
            }

            var source = new frontend.app.source.Source().set({
                dataKey : dataKey,
                data    : data
            });

            this._controls[name] = new frontend.lib.ui.form.CheckList(true);
            this._controls[name].setSource(source);
            this._controls[name].addListener('click', function() {
                this._calendar._initInstance.fireDataEvent('changeFilterSelection', this.getFilters(), this);
            }, this);

            return this._controls[name];
        },

        getFilters : function()
        {
            var filters = {}, prefix = "list";
            for(var i = 0; i < this.filterNames.length; i++)
            {
                filters[this.filterNames[i].toLowerCase()] = (typeof this._controls[prefix + this.filterNames[i]] !== "undefined") ?
                    this._controls[prefix + this.filterNames[i]].getCheckedIds() : null;
            }
            return filters
        },

        _setBox : function( responseData, name, listDataKey )
        {
            this._controls['box' + name].add( this._createList('list' + name, listDataKey, responseData) );
//            this._controls['box' + name].add( this._controls['btnAdd' + name] );

            this.fireEvent('requestFinished');
        }
    }
});