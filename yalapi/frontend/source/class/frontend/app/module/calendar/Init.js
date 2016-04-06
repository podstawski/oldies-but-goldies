/*
 #ignore(scheduler)
 #ignore(scheduler._click)
 #ignore(scheduler._click.buttons)
 #ignore(scheduler.date)
 #ignore(scheduler.config)
 #ignore(scheduler.templates)
 #ignore(scheduler.locale.tooltip)
 #ignore(dhtmlXTooltip)
 #ignore(scheduler._listeners)
 */

qx.Class.define("frontend.app.module.calendar.Init",
    {
        extend : qx.core.Object,

        construct : function(calendarInstance) {
            this.base(arguments);
            this._calendar = calendarInstance;

            this.mergeConfigs();

            this._setTemplates();
            this._setFilters();
            this._setTooltip();
            this._setLightboxMethods();

            if(typeof scheduler._listeners === "undefined") {
                scheduler._listeners = [];
            }

            scheduler._click.buttons.edit = scheduler._click.buttons.details = function(id) { scheduler.showLightbox(id, true, true) };

            scheduler.clearAll();
            scheduler.init( this._config.main.div_name, new Date(), this._config.main.view_mode );
        },

        members :
        {
            _calendar   : null,
            _lastCreatedEventId : null,

            mergeConfigs : function()
            {
                Tools.mergeObjects(scheduler.config,        this._config.main);
                Tools.mergeObjects(dhtmlXTooltip.config,    this._config.tooltip);
                Tools.mergeObjects(scheduler.xy,            this._config.xy);
            },

            _setTooltip : function()
            {
                var format = scheduler.date.date_to_str(scheduler.config.xml_date);
                scheduler.templates.tooltip_text = function(start, end, event) {
                    return (
                        "<table cellspacing='10'>" +
                        "<tr><td><b>" + scheduler.locale.tooltip.event      + "</b></td><td>" + event.text              + "</td></tr>"+
                        "<tr><td><b>" + scheduler.locale.tooltip.room       + "</b></td><td>" + event.roomName          + "</td></tr>"+
                        "<tr><td><b>" + scheduler.locale.tooltip.coach      + "</b></td><td>" + event.coach_name        + "</td></tr>"+
                        "<tr><td><b>" + scheduler.locale.tooltip.date_s     + "</b></td><td>" + format(start)           + "</td></tr>"+
                        "<tr><td><b>" + scheduler.locale.tooltip.date_e     + "</b></td><td>" + format(end)             + "</td></tr>"+
                        "</table>"
                    );
                };
            },

            _setFilters : function()
            {
                scheduler.filter_day = scheduler.filter_week =
                scheduler.filter_month = scheduler.filter_timeline =
                function(that)
                {
                    return(
                        function(ev_id, event)
                        {
                            var map = [
                                { filterName : 'rooms', propertiesName : 'room_id' },
                                { filterName : 'coaches', propertiesName : 'user_id' }
                            ];

                            var value = 0;
                            if(typeof that._calendar._filters !== "undefined" && that._calendar._filters !== null)
                            {
                                for(var i = 0; i < map.length; i++)
                                {
                                    if(that._calendar._filters[map[i].filterName].length > 0)
                                    {
                                        value += that._calendar._filters[map[i].filterName].indexOf(event[map[i].propertiesName]);
                                    }
                                    else { value += -1; }
                                }

                                return (value !== -2);
                            }
                            return true;
                        }
                    );
                }(this);
            },

            _setTemplates : function()
            {
                scheduler.templates.event_class=function(start,end,event){
                    if(event.readOnly === true) {
                        event.color = "#E8E8E8";
                        event.textColor = '#000000';
                    }
                }
            },
 
            _setLightboxMethods : function() {
                scheduler.showLightbox = function(id, onCancelFlag, editMode)
                { scheduler.startLightbox(id, onCancelFlag, editMode); };

                var that = this;
                scheduler.startLightbox = function(id, onCancelFlag, editMode)
                {
                    var event = scheduler.getEvent(id);
                    if(event && event.readOnly && typeof event.roomName !== "undefined" && typeof event.coach_name !== "undefined") { return; }

                    var form = new frontend.app.form.Lesson(event, onCancelFlag, editMode);
                    form.addListenerOnce("completed", function(e)
                    {
                        scheduler.endLightbox();
                        this._calendar.renderCalendar('addLesson');
                    }, that);

                    form.addListenerOnce("cancel", function(event)
                    {
                        scheduler.deleteEvent(event.getData().id);
                        scheduler.render_view_data();
                    }, that);

                    form.center();
                    form.open();
                };

                scheduler.endLightbox = qx.lang.Function.empty;
            },

            _setBehaviours : function()
            {
                this.addListener('changeFilterSelection', function(e) {
                    this._calendar._filters = e.getData();
                    this._calendar.renderCalendar('changeFilter', null, null);
                }, this);

               var listenersToAdd = {};
               listenersToAdd.onEventDeleted = function(that) {
                   return function(id)
                   {
                       if(that._lastCreatedEventId !== id)
                       {
                           var request = new frontend.lib.io.HttpRequest(Urls.resolve('LESSONS', id), 'DELETE');
                           request.addListener('success', function(){
                               new frontend.lib.dialog.Message(Tools['tr']('calendar.init.lesson:deleted'));
                               this._calendar._data.lessons = [];
                               this._calendar.renderCalendar('addLesson');
                           }, that);
                           request.send();
                       }
                   }
               }(this);

                listenersToAdd.onBeforeEventChanged = function(that) {
                    return function(event_object, native_event, is_new) {
                        if(event_object.readOnly) { return false; }
                        if(!is_new)
                        {
                            var df = new qx.util.format.DateFormat("yyyy-MM-dd HH:mm:ss"), values = {};
                            values.start_date = df.format(event_object.start_date);
                            values.end_date = df.format(event_object.end_date);

                            values.room_id = event_object.room_id;
                            values.course_unit_id = event_object.course_unit_id;
                            values.user_id = event_object.user_id;

                            var request = new frontend.lib.io.HttpRequest(Urls.resolve('LESSONS', event_object.id), 'PUT');
                            request.setRequestData(values);
                            request.send();
                        }

                        return true;
                    }
                }(this)

                listenersToAdd.onViewChange = function(that) {
                    return function (mode , date) {
//                        that._calendar.renderCalendar('changeView', mode, date);
                    };
                }(this);

                listenersToAdd.onDblClick = function (event_id, native_event_object){
                    if(scheduler.getEvent(event_id).readOnly) { return false; }
                    scheduler.showLightbox(event_id, true, true);
                };

                listenersToAdd.onEventCreated = function(that) {
                    return function(event_id,event_object){
                        that._lastCreatedEventId = event_id;
                        return true;
                    };
                }(this);

                listenersToAdd.onBeforeDrag = function (event_id, mode, native_event_object){
                    return !((mode === 'resize' || mode === 'move') && scheduler.getEvent(event_id).readOnly);
                };

                listenersToAdd.onBeforeEventDelete = function(event_id,event_object){
                    var event = scheduler.getEvent(event_id);
                    return (!event.readOnly);
                };

                listenersToAdd.onClick = function(event_id, native_event_object){
                   var event = scheduler.getEvent(event_id);
                   return (!event.readOnly);
                };

                for(var i in listenersToAdd)
                {
                    if(scheduler._listeners.indexOf(i) === -1)
                    {
                        scheduler.attachEvent(i, listenersToAdd[i]);
                        scheduler._listeners.push(i);
                    }
                }
            },

            _config :
            {
                main :
                {
                    first_hour                      :   0,
                    last_hour                       :   24,
                    multi_day                       :   true,
                    show_loading                    :   false,
                    time_step                       :   30,
                    edit_on_create                  :   true,
                    details_on_create               :   true,
                    event_duration                  :   45,
                    details_on_dblclick             :   true,
                    xml_date                        :   "%Y-%m-%d %H:%i",
                    div_name                        :   "scheduler_here",
                    view_mode                       :   "week",
                    load_url                        :   "",
                    auto_end_date                   :   false,
                    scroll_hour                     :   8,
                    start_on_monday                 :   true,
                    dblclick_create                 :   true,
                    preserve_scroll                 :   false,
                    icons_select                    :   [ "icon_edit","icon_delete" ]
                },

                tooltip :
                {
                    classNamed          : 'dhtmlXTooltip tooltip',
                    timeout_to_display  : 1100,
                    delta_x             : /*-200*/0,
                    delta_y             : 350
                },

                xy :
                {
                    nav_height : 30,
                    scroll_width : 30,
                    menu_width : 30,

                    scale_height : 20,
                    scale_width : 50,
                    bar_height : 0
                }
            }
        }
    });