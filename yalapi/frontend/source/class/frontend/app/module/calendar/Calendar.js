/*
#
#ignore(scheduler)
#ignore(label)
#ignore(scheduler._click)
*/

qx.Class.define("frontend.app.module.calendar.Calendar",
{
    include : [
        frontend.MMessage
    ],

    extend : qx.ui.container.Composite,

    construct : function()
    {
        this.base(arguments);
        this._init();

        this.setLayout(new qx.ui.layout.VBox(0));
        this.set({
            padding : 0,
            margin  : 0
        });

        this.add( this._createContainerTop() );
        this.add( this._createContainerBottom(), { flex : 1 } );
        this._createTopBar();

        this.addListener('courseUnitsLoaded', this._renderCourseUnits, this);
        this.addListenerOnce("appear", this.__addDhtmlCalendar, this);

        this.addListenerOnce('filterLoaded', function(){
            this._filterLoaded = true;
            scheduler.setCurrentView(new Date());
            this._getAndRenderLessons();
            this._initInstance._setBehaviours();
        }, this);

        this.addListenerOnce("setContext", function() {
            this._controls['courseColor'].set({
                decorator   : new qx.ui.decoration.Single(1,'solid','black'),
                margin      : 0,
                marginRight : 5,
                width   : 24,
                height  : 24,
                backgroundColor : this._context.color,
                toolTipText     : this._context.color
            });
            this._contextSet = true;
            this._getCourseUnitsWithAmount();
        }, this);
    },

    members :
    {
        _controls               : [],
        _context                : null,
        _data                   : null,
        _urls                   : [],
        _view                   : null,
        _filters                : null,

        _filtersUpdated         : false,
        _contextSet                : false,
        _courseDescriptionRendered : false,

        _initInstance           : null,

        _lock : false,
        _lastMode : null,

        _init : function()
        {
            this._filters   = null;
            this._context   = null;
            this._data      = { courseUnits : [], lessons : [], courseUnitsIds : [], date : { s: null, e: null } };
            this._view      = { date: null, mode: null };
            this._urls      = [];

            this._contextSet = false;
            this._courseDescriptionRendered = false;

            this._initInstance = null;
        },

        renderCalendar : function(from, mode, date)
        {
            switch(from)
            {
                case 'addLesson':
                case 'startCalendar':
                    this._getCourseUnitsWithAmount();
                    this._getAndRenderLessons();
                break;

                case 'changeView':
                    if(this._contextSet && this._lastMode !== mode)
                    {
                        this._lastMode = mode;

                        this._setDateRange(date);
                        this._view.mode = mode;
                        this._view.date = date;
                        if(this._data.courseUnitsIds.length === 0) {
                            this._getCourseUnitsWithAmount();
                        }

                        this._getAndRenderLessons();
                    }
                break;

                case 'changeFilter':
                    this._getAndRenderLessons();
                break;

                default:
                    break;
            }
        },

        _setDateRange : function(date)
        {
            var day = date.getDay();

            var daysToWeekStart = day - 1;
            var daysToWeekEnd   = 7 - day;

            this._data.date.s = new Date( +date - (daysToWeekStart * 24 * 60 * 60 * 1000) );
            this._data.date.e = new Date( +date + (daysToWeekEnd   * 24 * 60 * 60 * 1000) );

            this._data.date.s.setHours(0);
            this._data.date.s.setMinutes(0);

            this._data.date.e.setHours(23);
            this._data.date.e.setMinutes(59);
        },

        _getFormatedDate : function(date, isMonthMode, isStart)
        {
            var day     = (isMonthMode) ? ((isStart) ? '01' : '31') : Tools.pad(date.getDate());
            var hour    = (isMonthMode) ? ((isStart) ? '00' : '23') : Tools.pad(date.getHours());
            var minutes = (isMonthMode) ? ((isStart) ? '00' : '59') : Tools.pad(date.getMinutes());

            return  date.getFullYear() + '-' + Tools.pad(date.getMonth() + 1) + '-' + day + ' ' + hour + ':' + minutes;
        },

        _renderCourseDescription : function()
        {
            var code = (this._context.code != "null") ? (" (" + this._context.code + ")") : ('');
            var name = this._context.name + code;
            this._controls['courseName'].setValue(name);

            if(this._context.group_id !== 0 && this._context.group_id !== null)
            {
                var requestData = { 'group_user_id' : this._context.group_id, 'count_flag' : 1 },
                request  = new frontend.lib.io.HttpRequest(Urls.resolve('GROUPS', requestData), 'GET');

                request.addListener('success', function(e){
                    var gNameSize = " -  (" + this._context.group_name + ") - (" + e.getTarget().getResponse() + " os.)";
                    this._controls['courseName'].setValue( this._controls['courseName'].getValue() + gNameSize);
                }, this);
                request.send();
            }

            this._courseDescriptionRendered = true;
        },

        _flushDatesAndUrls : function()
        {
            this._urls = [];
            this._view.date = null;
        },

        setContext : function(data) { this._context = data; this.fireEvent('setContext') },
        getContext : function()     { return this._context; },

        _renderCourseUnits : function()
        {
            var children = this._controls['courseUnitsContainer'].getChildren(),
                setObj = { margin: 0, padding: 0, marginRight: 10 }, label = null;

            for(var i = 0, length = this._data.courseUnits.length; i < length; i++)
            {
                var name    = "label_" + this._data.courseUnits[i].name,
                    value   = this._data.courseUnits[i].name +
                              " [" + this._data.courseUnits[i].remaining_hours + "]";

                if(children.length === 0) {
                    var font = new qx.bom.Font(12, ["Verdana", "sans-serif"]);
                    label   = this._createLabel( name, value, font);
                }
                else {
                    label = this._controls[name];
                    setObj.value = value;
                }

                label.set(setObj);
                this._controls['courseUnitsContainer'].add(label);
            }

            var effect = new qx.fx.effect.combination.Pulsate(this._controls['courseUnitsContainer'].getContainerElement().getDomElement());
            effect.setDuration(2);
            effect.start();

            (!this._courseDescriptionRendered) ? this._renderCourseDescription() : false;
        },

        _getDataUrlObject : function(addToUrl, flags)
        {
            var urlData = {
                courseunits : this._data.courseUnitsIds,
                count       : (addToUrl.count === true),
                group       : (addToUrl.group === true),
                coaches     : addToUrl.coaches && this._filters && this._filters.coaches || [],
                rooms       : addToUrl.rooms   && this._filters && this._filters.rooms   || [],
                flags       : (flags && flags.length > 0) ? flags : []
            };
            if(!addToUrl.count && this._data.date.e && this._data.date.s ) {
                urlData.de = this._getFormatedDate(this._data.date.e, (this._view.mode === 'month') , false);
                urlData.ds = this._getFormatedDate(this._data.date.s, (this._view.mode === 'month') , true);
            }

            for(var i in urlData ) {
                urlData[i] = qx.lang.Json.stringify(urlData[i]);
            }
            return urlData;
        },

        _isUrlExists : function(url)
        {
            var urlExist = false;

            for(var i = 0; i < this._urls.length; i++)
            {
                if(this._urls[i] === url ) {
                    urlExist = true;
					break;
                }
            }

            return urlExist;
        },

        _getAndRenderLessons : function()
        {
            if(this._filterLoaded && this._courseDescriptionRendered && this._lock !== true)
            {
                this._lock = true;

                var urlData     = this._getDataUrlObject( { coaches: true, rooms: true }, ['main']),
                    urlDataAdd  = this._getDataUrlObject( { coaches: true, rooms: true }, ['readOnly']),
                    url     = Urls.resolve("LESSONS", urlData ),
                    urlAdd  = Urls.resolve("LESSONS", urlDataAdd );

                    var request = new frontend.lib.io.HttpRequest(url, 'GET');
                        request.addListener("success", function(e) {
                            var lessons = request.getResponseJson();
                            this._data.lessons = this._data.lessons.concat(lessons);
                            this._removeDuplicatedLessons();
                            var additionalRequest = new frontend.lib.io.HttpRequest(urlAdd, 'GET');
                            additionalRequest.addListener('success', function() {

                                var addLessons = additionalRequest.getResponseJson();
                                this._data.lessons = this._data.lessons.concat(addLessons);

                                this.fireDataEvent('lessonLoad', 'add');

                                scheduler.clearAll();
                                scheduler.parse(qx.lang.Json.stringify(this._data.lessons), 'json');

                                this._lock = false;
                            }, this);
                            additionalRequest.send();
                    }, this);
                    request.send();
            }
        },

        _removeDuplicatedLessons : function()
        {
			var duplicates = {};
            for(var i = 0; i < this._data.lessons.length; i++)
			{
				duplicates[i] += 1;
			}
        },

        _getCourseUnitsWithAmount : function()
        {
            var request = new frontend.lib.io.HttpRequest(Urls.resolve("COURSE_UNITS", { 'course_id' : this._context.id }), 'GET');
            request.addListener("success", function() {
                this._data.courseUnits = request.getResponseJson();
                this._data.courseUnitsIds = [];
                
                for(var i = 0; i < this._data.courseUnits.length; i++) {
                    this._data.courseUnitsIds.push(this._data.courseUnits[i].id);
                }
                
                this.fireEvent('courseUnitsLoaded');
            }, this);
            request.send();
        },

        _createLabel : function(name, value, font)
        {
            this._controls[name] = new qx.ui.basic.Label(value);
            if( font ) { this._controls[name].set({ font : font }); }
            this._controls[name].set({ margin : 0, marginTop : 5 });
            return this._controls[name];
        },

        _createTopBar : function()
        {
            var font = new qx.bom.Font(12, ["Verdana", "sans-serif"]);
            font.setBold(true);

            var container = new qx.ui.container.Composite(new qx.ui.layout.HBox(0));
            container.set({ padding: 0, margin: 0 });
            container.add(this._createLabel('courseColor', '', null));
            container.add(this._createLabel('courseName', '', font));

            this._controls['courseName'].set({ margin: 0, padding: 0 });

            this._controls['containerTop'].add(container);
            this._controls['containerTop'].add( this._createCourseUnitsContainer());
        },

        _createCourseUnitsContainer : function()
        {
            this._controls['courseUnitsContainer'] =
                new qx.ui.container.Composite(new qx.ui.layout.Flow(0, 0, "left")).set({ minHeight: 20 });
            this._controls['courseUnitsContainer'].set({ marginLeft: 35, padding: 0 });

            return this._controls['courseUnitsContainer'];
        },

        _createContainerTop : function()
        {
            var layout = new qx.ui.layout.VBox(0);

            this._controls['containerTop'] = new qx.ui.groupbox.GroupBox();
            this._controls['containerTop'].set({ layout : layout, padding : 0, margin : 0 });
            return this._controls['containerTop'];
        },

        _createContainerBottom : function()
        {
            this._controls['containerBottom'] = new qx.ui.container.Composite().set({
                margin          : 0,
                padding         : 0
            });
            return this._controls['containerBottom'];
        },

        __addDhtmlCalendar : function()
        {
            var localeManager = qx.locale.Manager.getInstance();
            var lang = localeManager.getLocale();

            scheduler.locale = Tools.calendar[lang];

            this._controls['containerBottom'].getContentElement().getDomElement().innerHTML = this.__html;
            this._initInstance = new frontend.app.module.calendar.Init(this);
        },

        __html :
            '<div id="scheduler_here" class="dhx_cal_container" style="width:99.9%; height:99.5%;">' +
                 '<div class="dhx_cal_navline">' +
                   ' <div class="dhx_cal_prev_button">&nbsp;</div>' +
                   ' <div class="dhx_cal_next_button">&nbsp;</div>' +
                   ' <div class="dhx_cal_today_button"></div>' +
                   ' <div class="dhx_cal_date"></div>' +
                   ' <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>' +
                   ' <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>' +
                   ' <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>' +
                '</div>' +
                '<div class="dhx_cal_header">' +
                '</div>' +
                '<div class="dhx_cal_data">' +
                '</div>' +
            '</div>'
    }
});