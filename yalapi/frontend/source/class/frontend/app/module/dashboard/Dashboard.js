/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/go-previous.png)
#asset(qx/icon/${qx.icontheme}/16/actions/go-next.png)

********************************** */

qx.Class.define("frontend.app.module.dashboard.Dashboard",
{
    extend : qx.ui.container.Composite,

    events :
    {
        "changeIncomingEvents" : "qx.event.type.Data",
        "changeDashboardData"  : "qx.event.type.Data"
    },

    properties :
    {
        appearance :
        {
            refine : true,
            init : "dashboard"
        },

        incomingEvents :
        {
            check : "Map",
            init : null,
            nullable : true,
            event : "changeIncomingEvents",
            apply : "_applyIncomingEvents"
        },

        dashboardData :
        {
            check : "Map",
            init : null,
            nullable : true,
            event : "changeDashboardData",
            apply : "_applyDashboardData"
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.VBox(10));

        var eventsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
        eventsContainer.add(this.getChildControl("incoming-events-box"), {flex:1});
        eventsContainer.add(this.getChildControl("calendar"));

        this.add(eventsContainer);
//        this.add(this.getChildControl("new-messages-box"));
        this.add(this.getChildControl("dashboard-data"));

        this.initDashboardData();

        this.__df = new frontend.lib.util.format.DateFormat();

        this.__eventsRequest = new frontend.lib.io.HttpRequest;
        this.__eventsRequest.addListener("success", this._onEventsRequestSuccess, this);

        this.__dashboardRequest = new frontend.lib.io.HttpRequest;
        this.__dashboardRequest.setUrl(Urls.resolve("DASHBOARD"));
        this.__dashboardRequest.addListener("success", this._onDashboardRequestSuccess, this);

        this.addListener("appear", this.reloadEvents, this);

        this.__wizards = {};
    },

    members :
    {
        __eventsRequest : null,

        __df : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "calendar":
                    control = new frontend.app.module.dashboard.Calendar().set({
                        marginTop : 18
                    });
                    control.addListener("changeShownMonth",   this.reloadEvents, this);
                    control.addListener("changeShownYear",    this.reloadEvents, this);
                    control.addListener("changeValue",        this.reloadEvents, this);
                    control.addListener("changeMouseOverDay", this._onChangeMouseOverDay, this);
                    break;

                case "incoming-events-box":
                    control = new qx.ui.groupbox.GroupBox();
                    control.setLayout(new qx.ui.layout.VBox(5, null, "separator-vertical"));
                    break;

                case "dashboard-data":
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, null, "separator-horizontal"));
                    break;

                case "new-messages-box":
                    control = new qx.ui.groupbox.GroupBox("Nowe wiadomości");
                    control.setLayout(new qx.ui.layout.VBox);
                    var legend = control.getChildControl("legend");
                    var tooltip = new qx.ui.tooltip.ToolTip("Kliknij, aby przejść do odebranych wiadomości");
                    tooltip.setShowTimeout(0);
                    legend.setToolTip(tooltip);
                    legend.setCursor("pointer");
                    legend.addListener("click", this._onMessagesBoxLegendClick, this);
                    control.add(this.getChildControl("new-messages-table"));
                    break;

                case "new-messages-table":
                    var tableModel = new qx.ui.table.model.Simple();
                    tableModel.setColumns(["Nadawca", "Temat", "Data wysłania"]);
                    control = new frontend.lib.ui.table.Table(tableModel);
                    control.setHeight(150);
                    control.setColumnVisibilityButtonVisible(false);
                    break;

                case "google-map-window":
                    control = new qx.ui.window.Window;
                    control.setLayout(new qx.ui.layout.VBox);
                    control.setShowMaximize(false);
                    control.setResizable(false);
                    control.add(this.getChildControl("google-map"));
                    break;

                case "google-map":
                    control = new frontend.lib.ui.GoogleMap();
                    break;
            }

            return control || this.base(arguments, id);
        },

        _applyIncomingEvents : function(events, old)
        {
            var calendar = this.getChildControl("calendar");
            var incomingEvents = this.getChildControl("incoming-events-box");
            if (old) {
                calendar.resetEvents();
                incomingEvents.removeAll();
            }

            if (events != null) {
                var eventCount = events.lessons.length;
                if (eventCount == 0) {
                    incomingEvents.add(new qx.ui.basic.Atom("brak zdarzeń").set({
                        alignY : "middle",
                        center : true
                    }), {flex:1});
                } else if (qx.lang.Type.isObject(events.lessons) && !qx.lang.Object.isEmpty(events.lessons)) {
                    var sf = qx.lang.String;

                    var lessons = events.lessons;
                    var trainingCenters = events.tcs;
                    var trainers = events.trainers;
                    var rooms = events.rooms;
                    var courses = events.courses;
                    var courseUnits = events.units;
                    var calendarEvents = [];

                    qx.lang.Object.getKeys(lessons).forEach(function(lessonID){
                        var lesson = lessons[lessonID];
                        var courseUnit = courseUnits[lesson.unit_id];
                        var course = courses[courseUnit.course_id];
                        var room = rooms[lesson.room_id];
                        var trainingCenter = trainingCenters[room.tc_id];
                        var trainer = trainers[lesson.trainer_id];

                        if (incomingEvents.getChildren().length <= 5) {
                            var label = [
                                this.__df.format(lesson.start_date, "dd MMM, HH:mm"),
                                sf.format("Szkolenie: <b>%1</b> (%2)", [ courseUnit.name, course.name ]),
                                sf.format("Miejsce: <b>%1</b>, %2", [ trainingCenter.name, room.name ]),
                                sf.format("Adres: %1, %2", [ trainingCenter.street, trainingCenter.city ]),
                                sf.format("Prowadzący: %1 %2", [ trainer.first_name, trainer.last_name ])
                            ];

//                            if (lesson.exams.length > 0) {
//                                label.push(
//                                    "<b>MOŻLIWE SPARWDZIANY</b>: \"" + lesson.exams.join("\", \"") + "\""
//                                );
//                            }

                            var atom = new qx.ui.basic.Atom(label.join(" - ")).set({
                                rich : true,
                                iconPosition : "left",
                                height : 18
                            });
                            atom.setUserData("date", this.__df.format(lesson.start_date, "dd-MM-yyyy"));
                            atom.addListener("click", this._onAtomClick(trainingCenter), this);
                            incomingEvents.add(atom);
                        }

                        var calendarEvent = {
                            start_date : lesson.start_date,
                            end_date : lesson.end_date,
                            course_name : course.name,
                            unit_name : courseUnit.name
                        };
                        calendarEvents.push(calendarEvent);
                    }, this);

                    calendar.addEvent.apply(calendar, calendarEvents);
                }
            }
        },

        __wizards : null,

        __eventTypes :
        {
            messages : [ "nowe wiadomości (%1)", "app.module.mailbox.Inbox" ],
            survey   : [ "nowe ankiety (%1)", "app.list.Survey" ],
            test     : [ "nowe testy (%1)", "app.list.Test" ],
            quiz     : [ "nowe quizy (%1)", "app.list.Quiz" ]
        },

        _applyDashboardData : function(data, old)
        {
            var container = this.getChildControl("dashboard-data");
            container.removeAll();

            if (data == null) {
                return;
            }

            var control;
            qx.lang.Object.getKeys(data).forEach(function(evenType){
                if (data[evenType] > 0 && this.__eventTypes[evenType] != null) {
                    control = new qx.ui.basic.Label(Tools["tr"](this.__eventTypes[evenType][0], data[evenType]));
                    control.setAppearance("dashboard-data-entry");
                    control.addListenerOnce("click", function(e){
                        qx.core.Init.getApplication().getInnerMenu().selectByContent(this.__eventTypes[evenType][1]);
                    }, this);
                    container.add(control);
                }
            }, this);

            if (data.wizards) {
                data.wizards.forEach(function(wizardName){
                    if (this.__wizards[wizardName] == null) {
                        var clazz = qx.Class.getByName("frontend.app.module.wizard." + wizardName);
                        var wizard = new clazz();
                        wizard.addListenerOnce("completed", function(e) {
                            qx.event.Timer.once(wizard.exclude, wizard, 2000);
                        }, this);
                        this.add(wizard);
                        this.__wizards[wizardName] = wizard;
                    }
                }, this);
            }
        },

        _onEventsRequestSuccess : function(e)
        {
            var data = this.__eventsRequest.getResponseJson();
            this.setIncomingEvents(data);
        },

        _onDashboardRequestSuccess : function(e)
        {
            var data = this.__dashboardRequest.getResponseJson();
            this.setDashboardData(data);
        },

        reloadEvents : function()
        {
            var calendar = this.getChildControl("calendar");
            var month    = calendar.getShownMonth();
            var year     = calendar.getShownYear();
            var day      = calendar.getValue();

            this.__eventsRequest.setUrl(Urls.resolve("DASHBOARD_EVENTS", {
                day   : (day = (day != null && month == day.getMonth() && year == day.getFullYear()) ? day.getDate() : 1),
                month : month + 1,
                year  : year
            }));
            this.__eventsRequest.send();

            var sf = qx.lang.String;
            this.getChildControl("incoming-events-box").setLegend(
                sf.format("%1 %2 - %3", [ day, qx.locale.Date.getMonthName("wide", month, null, "stand-alone"), "najbliższe szkolenia" ])
            );

            this.__dashboardRequest.send();
        },

        _onChangeMouseOverDay : function(e)
        {
            var date = e.getData();
            this.getChildControl("incoming-events-box").getChildren().forEach(function(event) {
                if (date == event.getUserData("date")) {
                    event.setIcon("icon/16/actions/go-next.png");
                } else {
                    event.setIcon(null);
                }
            }, this);
        },

        _onMessagesBoxLegendClick : function(e)
        {
            qx.core.Init.getApplication().getInnerMenu().selectByContent("app.module.mailbox.Inbox");
        },


        _onAtomClick : function(trainingCenter)
        {
            var googleWindow = this.getChildControl("google-map-window");
            var googleMap = this.getChildControl("google-map");
            var application = qx.core.Init.getApplication().getRoot();

            return function(e)
            {
                googleMap.setPlaceData(trainingCenter);
                googleWindow.setCaption("Lokalizacja ośrodka \"" + trainingCenter.name + "\"");
                googleWindow.moveTo(application.getBounds().width - 350, 120);
                googleWindow.open();
            }
        }
    }
});