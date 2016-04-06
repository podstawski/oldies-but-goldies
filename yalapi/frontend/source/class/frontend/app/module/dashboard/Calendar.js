/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/help-about.png)

********************************** */

qx.Class.define("frontend.app.module.dashboard.Calendar",
{
    extend : qx.ui.control.DateChooser,

    events :
    {
        "changeMouseOverDay" : "qx.event.type.Data"
    },

    construct : function()
    {
        this.__addEvents = {};
        this.__df = new frontend.lib.util.format.DateFormat();

        this.base(arguments);

        this.setValue(new Date());
        this.setWidth(220);
        this.setAllowStretchY(false);
    },

    members :
    {
        __addEvents : null,

        __df : null,

        __parseDateString : function(dateStr)
        {
            var eventDate = this.__df.parse(dateStr);

            return {
                day     : eventDate.getDate(),
                month   : eventDate.getMonth(),
                year    : eventDate.getFullYear(),
                hours   : eventDate.getHours(),
                minutes : eventDate.getMinutes(),
                seconds : eventDate.getSeconds()
            }
        },

        addEvent : function(event)
        {
            for (var i = 0, l = arguments.length; i < l; i++) {
                var event = arguments[i];
                event.eventDate = this.__parseDateString(event.start_date);
                this.__addEvents[event.start_date] = event;
            }
            this._updateDatePane();
            return this;
        },

        removeEvent : function(event)
        {
            for (var i = 0, l = arguments.length; i < l; i++) {
                var event = arguments[i];
                delete this.__addEvents[event.start_date];
            }
            this._updateDatePane();
            return this;
        },

        resetEvents : function(noupdate)
        {
            this.__addEvents = {};
            if (noupdate !== true) {
                this._updateDatePane();
            }
            return this;
        },

        setEvents : function()
        {
            this.resetEvents(true);
            this.addEvent.apply(this, arguments);
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;
            
            switch (id)
            {
                case "day":
                    control = this.base(arguments, id, hash);
                    control.getContentElement().setStyle('overflow', 'visible');
                    control.addListener("mouseover", function(e){
                        var date = control.getUserData("date");
                        this.fireDataEvent("changeMouseOverDay", date);
                    }, this);
                    break;
            }

            return control || this.base(arguments, id, hash);
        },

        _onDayDblClicked : function(e)
        {
            // SIM do nothing here...
        },

        // SIM overriden
        _updateDatePane : function()
        {
            var DateChooser = qx.ui.control.DateChooser;

            var today = new Date();
            var todayYear = today.getFullYear();
            var todayMonth = today.getMonth();
            var todayDayOfMonth = today.getDate();

            var selDate = this.getValue();
            var selYear = (selDate == null) ? -1 : selDate.getFullYear();
            var selMonth = (selDate == null) ? -1 : selDate.getMonth();
            var selDayOfMonth = (selDate == null) ? -1 : selDate.getDate();

            var shownMonth = this.getShownMonth();
            var shownYear = this.getShownYear();

            var startOfWeek = qx.locale.Date.getWeekStart();

            // Create a help date that points to the first of the current month
            var helpDate = new Date(this.getShownYear(), this.getShownMonth(), 1);

            // SIM fix label format (was displaying "lipca 2011" instead of "lipiec 2011")
            this.getChildControl("month-year-label").setValue(qx.locale.Date.getMonthName("wide", shownMonth, null, "stand-alone") + " " + shownYear);

            // Show the day names
            var firstDayOfWeek = helpDate.getDay();
            var firstSundayInMonth = 1 + ((7 - firstDayOfWeek) % 7);
            var weekDayFormat = new qx.util.format.DateFormat(DateChooser.WEEKDAY_FORMAT);

            for (var i = 0; i < 7; i++)
            {
                var day = (i + startOfWeek) % 7;

                var dayLabel = this.__weekdayLabelArr[i];

                helpDate.setDate(firstSundayInMonth + day);
                dayLabel.setValue(weekDayFormat.format(helpDate));

                if (qx.locale.Date.isWeekend(day)) {
                    dayLabel.addState("weekend");
                } else {
                    dayLabel.removeState("weekend");
                }
            }

            // Show the days
            helpDate = new Date(shownYear, shownMonth, 1, 12, 0, 0);
            var nrDaysOfLastMonth = (7 + firstDayOfWeek - startOfWeek) % 7;
            helpDate.setDate(helpDate.getDate() - nrDaysOfLastMonth);

            var weekFormat = new qx.util.format.DateFormat(DateChooser.WEEK_FORMAT);

            var addEventDates = qx.lang.Object.getKeys(this.__addEvents);
            var addEventCount = addEventDates.length;

            for (var week = 0; week < 6; week++)
            {
                this.__weekLabelArr[week].setValue(weekFormat.format(helpDate));

                for (var i = 0; i < 7; i++)
                {
                    var dayLabel = this.__dayLabelArr[week * 7 + i];

                    var year = helpDate.getFullYear();
                    var month = helpDate.getMonth();
                    var dayOfMonth = helpDate.getDate();

                    var isSelectedDate = (selYear == year && selMonth == month && selDayOfMonth == dayOfMonth);

                    if (isSelectedDate) {
                        dayLabel.addState("selected");
                    } else {
                        dayLabel.removeState("selected");
                    }

                    if (month != shownMonth) {
                        dayLabel.addState("otherMonth");
                    } else {
                        dayLabel.removeState("otherMonth");
                    }

                    var isToday = (year == todayYear && month == todayMonth && dayOfMonth == todayDayOfMonth);

                    if (isToday) {
                        dayLabel.addState("today");
                    } else {
                        dayLabel.removeState("today");
                    }

                    dayLabel.setCursor("default");
                    dayLabel.setToolTip(null);
                    dayLabel.getContentElement().setStyle("font-weight", "normal");

                    // SIM check for added incoming events
                    for (var k = 0; k < addEventCount; k++)
                    {
                        var addEventDate = addEventDates[k];
                        var event = this.__addEvents[addEventDate];

                        if (event.eventDate
                        &&  event.eventDate.year  == year
                        &&  event.eventDate.month == month
                        &&  event.eventDate.day   == dayOfMonth
                        ) {
                            this._applyDayToolTipText(dayLabel, event);

                            dayLabel.setCursor("help");
                            dayLabel.getContentElement().setStyle("font-weight", "bold");
                        }
                    }

                    dayLabel.setValue("" + dayOfMonth);
                    dayLabel.dateTime = helpDate.getTime();
                    dayLabel.setUserData("date", this.__df.format(helpDate, "dd-MM-yyyy"));

                    // Go to the next day
                    helpDate.setDate(helpDate.getDate() + 1);
                }
            }

            weekDayFormat.dispose();
            weekFormat.dispose();
        },

        _applyDayToolTipText : function(dayLabel, event)
        {
            var atom  = new qx.ui.basic.Atom(qx.lang.String.format("%1 - %2, %3 (%4)", [
                this.__df.format(event.start_date, "HH:mm"),
                this.__df.format(event.end_date, "HH:mm"),
                event.unit_name,
                event.course_name
            ])).set({
                rich : true
            });

            var tooltip = dayLabel.getToolTip();
            if (tooltip == null) {
                tooltip = new qx.ui.tooltip.ToolTip().set({
                    placeMethod : "widget",
                    position    : "top-right",
                    hideTimeout : 60000
                });
                tooltip.setLayout(new qx.ui.layout.VBox(2, null, "separator-vertical"));
                // SIM on construct, tooltip creates an empty atom element which we dont need
                tooltip.removeAll();
            }
            tooltip.add(atom);

            dayLabel.setToolTip(tooltip);
        }
    }
});