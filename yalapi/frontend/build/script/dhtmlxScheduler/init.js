var dhtmlxScheduler = function(calendarConfig)
{
    function initCalendar( calendarConfig )
    {
        scheduler.dialog = calendarConfig.dialog;

        for ( var attribute in calendarConfig.mainConfig )
        { scheduler.config[attribute] = calendarConfig.mainConfig[attribute]; }

        for ( attribute in calendarConfig.tooltipConfig )
        { dhtmlXTooltip.config[attribute] = calendarConfig.tooltipConfig[attribute]; }

        scheduler.obj                       = {};
        scheduler.obj.callback              = {};
        scheduler.obj.data                  = {};
        scheduler.obj.isBeforeLightbox      = false;

        scheduler.init( calendarConfig.mainConfig.div_name, calendarConfig.mainConfig.start_date, calendarConfig.mainConfig.view_mode );

        setListeners();
        setLocalization();
        setTooltip();
        setLightboxMethods();

        var request = new frontend.lib.io.HttpRequest(Urls.resolve("LESSONS"), 'GET');
        request.addListener("success", function(e){
            scheduler.parse( e.getTarget().getResponse(), 'json');
        }, this);
        request.send();
        console.log(scheduler.locale);
    }

    function setTooltip()
    {
        var format = scheduler.date.date_to_str( scheduler.config.xml_date );
        scheduler.templates.tooltip_text = function(start,end,event)
        {
            return "<b>" +              scheduler.locale.tooltip.coach + "</b> "+     event.coach_name+
                   "<br/><b>" +         scheduler.locale.tooltip.event + "</b> "+     event.text+
                   "<br/><br/><b>" +    scheduler.locale.tooltip.date_s + "</b> "+    format(start)+
                   "<br/><b>" +         scheduler.locale.tooltip.date_e + "</b> "+    format(end);
        };
    }

    function setLightboxMethods()
    {
        scheduler.showLightbox = function(id)
        {
            var ev = scheduler.getEvent(id);
            scheduler.startLightbox(ev);
        };

        scheduler.startLightbox = function(ev)
        {
            var form = new frontend.app.form.Lesson(ev);
            form.populate();

            form.addListener("completed", function(){
                scheduler.endLightbox()
            }, this);
            
            form.addListener("cancel", function(event){
                var data = event.getData();
                scheduler._roll_back_dates(data);
                scheduler.render_view_data();
            }, this);

            form.center();
            form.open();
        };

        scheduler.endLightbox = function()
        {
            var request = new frontend.lib.io.HttpRequest(Urls.resolve("LESSONS"), "GET");
            request.addListener("success", function(e){
                scheduler.clearAll();
                scheduler.parse(e.getTarget().getResponse(), 'json');
            }, this);
            request.send();
        }
    }

    function setLocalization()
    {
        var localeManager = qx.locale.Manager.getInstance();
        var lang = localeManager.getLocale();

        scheduler.locale = Tools.calendar[lang];
        console.log(scheduler);
        console.log(scheduler.locale);
    }

    function setListeners()
    {
        scheduler.attachEvent("onEventAdded", function(id) {});
        scheduler.attachEvent("onEventChanged", function(id)
        {

        });

        scheduler.attachEvent("onEventDeleted", function(id)
        {
            console.log('foo');
              var ev = scheduler.getEvent(id);
            console.log(ev);
//            var deleteObj = scheduler.obj.lessonDataObj;
//            deleteObj.id = id;
//            scheduler.obj.requester.deleteData( deleteObj, this );
        });
    }

    initCalendar(calendarConfig);
};
