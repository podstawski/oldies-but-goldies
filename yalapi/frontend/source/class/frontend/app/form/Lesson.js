/**
 *  #asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)
 *  #asset(qx/icon/${qx.icontheme}/16/actions/list-add.png)
 *
 */

qx.Class.define("frontend.app.form.Lesson",
    {
        extend : frontend.lib.ui.window.Modal,

        include : [
            frontend.MMessage
        ],

        construct : function(event, onCancelFlag, editMode) {
            this.base(arguments);
            this._initialize();

            this._editMode = (editMode === true);
            this._noRollbackEventsAfterCancel = (onCancelFlag === true);
            
            this._event = event;

            this.set({
                caption   : Tools['tr'](this._prefix + "caption"),
                layout    : new qx.ui.layout.HBox(10),
                resizable : false,
                showClose : false
            });

            this.add(this._createContainer());

            this._createSelectBoxes();
            this._createDatePickers();
            this._createTimeFields();

            if(!this._editMode) {
                this._createRecurringCheckBox();
                this._createLessonRecurring();
            }

            this._createButtonsContainer();

            this._setBehaviours('add');
            this._setSelectCourse();
        },

        members :
        {
            _prefix                 : 'form.lesson:',
            _event                  : null,
            _calendar               : null,
            _data                   : {},

            _noRollbackEventsAfterCancel : null,
            _editMode                    : false,

            _controls               : [],

            // CYF: courses should be always first!
            _labels                 : [ 'courses', 'course_units', 'coaches', 'rooms'],

            _tabIndexCounter        : null,
            _gridLessonRowCounter   : null,
            _gridRecurringRowCounter : null,

            _recValidationManager   : null,

            _initialize : function()
            {
                this._recValidationManager = new qx.ui.form.validation.Manager();
                this._gridRecurringRowCounter = 0;
                this._validator = new qx.ui.form.validation.Manager();
                this._tabIndexCounter = 1;
                this._gridLessonRowCounter = 0;
                this._event = null;
                this._openedFromMenu = false;
                this._calendar = qx.core.Init.getApplication().getInnerContent();
            },

            _populate : function()
            {
                if(typeof this._event === "undefined" || this._event === null)
                {
                    this._tryPopulateCurrentDateTime();

                    if(!this._tryPopulateFromCache())
                    {
                          this._tryPopulateFromMenu();
                    }
                }
                else
                {
                    if(this._editMode)
                    {
                        this._tryPopulateDate();
                        this._tryPopulateFromEvent();
                    }
                    else
                    {
                        this._tryPopulateDate();
                        if(!this._tryPopulateFromCache())
                        {
                            this._tryPopulateFromEvent();
                        }
                    }
                }
            },

            _tryPopulateCurrentDateTime : function()
            {
                var date = new Date(),
                    time =
                    {
                        s: { h: date.getHours(),  m: date.getMinutes()    },
                        e: { h: null,             m: null                 }
                    };

                time.s.m = 5 * Math.ceil(time.s.m/5);
                if(time.s.m === 60) { time.s.m = 0; time.s.h += 1; }
                time.s.h = (time.s.h === 24) ? time.s.h = 0 : time.s.h;

                time.e.m = (time.s.m + 45);
                time.e.h = time.s.h + ((time.e.m >= 60) ? 1 : 0);
                time.e.h = (time.e.h === 24) ? time.e.h = 0 : time.e.h;
                time.e.m = (time.e.m >= 60) ? time.e.m - 60 : time.e.m;

                this._controls['datePicker'].setValue(date);
                this._controls['timeS'].setValue(time.s.h + ":" + time.s.m);
                this._controls['timeE'].setValue(time.e.h + ":" + time.e.m);
            },

            _tryPopulateFromMenu : function()
            {
                var menu = qx.core.Init.getApplication().getInnerMenu();

                for(var i = 0, length = menu.filterNames.length; i < length; i++)
                {
                    if(typeof menu._controls['list' + menu.filterNames[i]].getCheckedIds()[0] !== "undefined") {
                        this._controls[menu.filterNames[i].toLowerCase()].setModelSelection([menu._controls[ 'list' + menu.filterNames[i] ].getCheckedIds()[0]])
                    }
                }
            },

            _tryPopulateDate : function()
            {
                this._controls['datePicker'].setValue(this._event.start_date);
                this._controls['timeS'].setValue(this._event.start_date.getHours() + ":" + this._event.start_date.getMinutes());
                this._controls['timeE'].setValue(this._event.end_date.getHours() + ":" + this._event.end_date.getMinutes());
            },

            _tryPopulateFromEvent : function()
            {
                var toPopulate = {
                    'course_unit_id' : 'course_units',
                    'user_id' : 'coaches',
                    'room_id' : 'rooms'
                };

                for(var i in toPopulate) {
                    if(this._event[i])
                    { this._controls[toPopulate[i]].setModelSelection([this._event[i]]); }
                }
            },

            _tryPopulateFromCache : function()
            {
                if (typeof window.cache === "object")
                {
                    if (window.cache[this._labels[0]] === this._calendar.getContext().id)
                    {
                        this._setBehaviours('remove');
                        for (var i = 1; i < this._labels.length; i++) {
                            this._controls[this._labels[i]].setModelSelection([window.cache[this._labels[i]]]);
                        }
                        this._setBehaviours('add');
                    }

                    return true;
                }
                else { return false; }
            },

            _createRecurringCheckBox : function()
            {
                this._gridLessonRowCounter += 1;
                this._controls['recurringCheckbox'] = new frontend.lib.ui.form.CheckBox(Tools['tr']("calendar.lesson.recurring:is"));
                this._controls['container'].add(this._controls['recurringCheckbox'], { column: 0, row: this._gridLessonRowCounter, colSpan: 2 } );
            },

            _setSelectCourse : function() {
                var source = new frontend.app.source.Source().set({
                    data    : [this._calendar.getContext()],
                    dataKey : 'name'
                });

                this._setSelectboxState('courses', false, source);
            },

            _setSelectboxState : function(name, state, value) {
                if (typeof state !== "undefined") {
                    this._controls[name].setEnabled(state);
                }
                if (typeof value !== "undefined") {
                    this._controls[name].setSource(value);
                }
            },

            _createLessonRecurring : function()
            {
                var layout = new qx.ui.layout.Grid(15, 15);
                this._controls['lessonRecurring'] = new qx.ui.groupbox.GroupBox();
                this._controls['lessonRecurring'].set({
                    layout : layout,
                    visibility : 'excluded'
                });
                this.add(this._controls['lessonRecurring']);

                var labels = [
                    { name: 'repeat.how',             method: '_createRepeatHowSelectBox' },
                    { name: 'repeat.how.howMuch',     method: '_createRepeatHowMuchText'  },
                    { name: 'repeat.in',              method: '_createWeekDayCheckboxes'  },
                    { name: 'repeat.finish',          method: '_createWhenFinishRadios'   }
                ];

                for(var i = 0; i < labels.length; i++) {
                    this._addWidgetToRecurringContainer(labels[i].name, labels[i].method);
                }
            },

            _createRepeatHowMuchText : function()
            {
                this._controls['repeat.how.howMuch'] = new qx.ui.form.TextField();
                this._controls['repeat.how.howMuch'].set({ visibility: 'excluded', value: "1", required: true, valid: false });
                this._controls['repeat.how.howMuch.label'].set({ visibility: 'excluded' });

                this._controls['repeat.how.howMuch'].validate = function(that) {
                    return function()
                    {
                        var value = that._controls['repeat.how.howMuch'].getValue();
                        var validateState = (isFinite(value) && (value < 54) && value !== '');

                        that._controls['repeat.how.howMuch'].setValid(validateState);
                        that._controls['repeat.how.howMuch'].setInvalidMessage(Tools['tr']('validate.messages.not_number_smaller_then', 54));
                    }
                }(this);

                this._controls['repeat.how.howMuch'].addListener('input', this._controls['repeat.how.howMuch'].validate, this);
                this._recValidationManager.add(this._controls['repeat.how.howMuch'], qx.util.Validate.number());
                
                return this._controls['repeat.how.howMuch'];
            },

            _createRepeatHowSelectBox : function()
            {
                this._controls['repeat.how'] = new frontend.lib.ui.form.SelectBox();

                var sourceArr = [
                    { id: 0, label: 'Codziennie'                            },
                    { id: 1, label: 'Codziennie (pon - pt)'                 },
                    { id: 2, label: 'Wszystkie poniedziałki, środy, piątki' },
                    { id: 3, label: 'Wszystkie wtorki i czwartki'           },
                    { id: 4, label: 'Tygodniami'                            },
                    { id: 5, label: 'Co miesiąc'                            }
                ];

                this._controls['repeat.how'].setSource(this._getSourceFromData(sourceArr, 'label'));
                this._controls['repeat.how'].setSelection([ this._controls['repeat.how'].getSelectables()[0] ]);
                this._controls['repeat.how'].addListener('changeSelection', function() {
                    var selection = this._controls['repeat.how'].getSelection()[0].getModel();

                    var visibility = (selection == 4 ) ? 'visible' : 'excluded',
                        setObj = { visibility : visibility, valid : true, value : "1", required: true };
                    (this._controls['repeat.how.howMuch'])       ? this._controls['repeat.how.howMuch'].set(setObj) : false;
                    (this._controls['repeat.how.howMuch.label']) ? this._controls['repeat.how.howMuch.label'].setVisibility(visibility) : false;

                    var daysVisibility = ([1, 2, 3, 5].indexOf(selection) !== -1) ? "excluded" : "visible";

                    this._controls['repeat.in'].setVisibility(daysVisibility);
                    this._controls['repeat.in.label'].setVisibility(daysVisibility);

                }, this);

                return this._controls['repeat.how'];
            },

            _createWhenFinishRadios : function()
            {
                var radioContainer = new qx.ui.container.Composite(new qx.ui.layout.Grid(5, 5)),
                    labels = ['repeat.finish.after.x.times', 'repeat.finish.in.day'];
                this._controls['radioBtnGroup'] = new qx.ui.form.RadioButtonGroup(new qx.ui.layout.VBox(5));

                this._controls['radioBtnGroup'].add(
                    this._controls[labels[0] + '.radio'] =
                    new qx.ui.form.RadioButton(Tools['tr'](this._prefix + labels[0]))
                );

                this._controls['radioBtnGroup'].add(
                    this._controls[labels[1] + '.radio'] =
                    new qx.ui.form.RadioButton(Tools['tr'](this._prefix + labels[1]))
                );

                radioContainer.add(this._controls['radioBtnGroup'], { column: 0, row: 0, rowSpan: 2 });
                radioContainer.add(this._controls[labels[0] + '.textField'] = new qx.ui.form.TextField(), { column: 1, row: 0 });
                radioContainer.add(this._controls[labels[1] + '.dateField'] = new frontend.lib.ui.form.DateField(), { column: 1, row: 1 });

                this._controls[labels[1] + '.dateField'].validate = function(that)
                {
                    return function()
                    {
                        if( that._controls[labels[1] + '.dateField'].getValue() === null )
                        {
                            that._controls[labels[1] + '.dateField'].setValid(false);
                            that._controls[labels[1] + '.dateField'].setInvalidMessage(Tools['tr'](''));
                        }
                    }
                }(this);

                this._controls[labels[1] + '.dateField'].setEnabled(false);

                this._controls['radioBtnGroup'].addListener('changeSelection', function(){
                    var setObj = { valid : true, value : null };

                    var value = this._controls[labels[0] + '.radio'].getValue();
                    this._controls[labels[1] + '.dateField'].setEnabled(!value);
                    this._controls[labels[0] + '.textField'].setEnabled(value);

                    this._controls[labels[0] + '.textField'].set(setObj);
                    this._controls[labels[1] + '.dateField'].set(setObj);
                }, this);

                var textFieldName = labels[0] + '.textField';
                this._controls[textFieldName].set({ padding: 0, margin: 0, height: 20});
                this._controls[labels[0] + '.radio'].set({ marginBottom: 7 });
                this._controls[textFieldName].validate = function(that){
                    return function()
                    {
                        var value = that._controls[textFieldName].getValue();
                        var validateState = (isFinite(value) && (value < 999) && value !== '');
                        that._controls[textFieldName].setValid(validateState);
                        that._controls[textFieldName].setInvalidMessage(Tools['tr']('validate.messages.not_number_smaller_then', 999));
                    }
                }(this);

                this._controls[textFieldName].addListener('input', this._controls[textFieldName].validate, this);
                return radioContainer;
            },

            _createWeekDayCheckboxes : function()
            {
                var layout = new qx.ui.layout.HBox(5);
                var checkBoxesContainer = new qx.ui.container.Composite(layout);

                var days = [];
                days = days.concat(Tools.calendar.pl.date.day_short);
                days.splice(days.length - 1, 0, days.splice(0, 1)[0]);
                
                for(var i = 0, length = days.length; i < length; i++)
                {
                    var day = (i == 7) ? 0 : i;
                    this._controls[day + 'DayCheckBox'] = new qx.ui.form.CheckBox(days[day]);
                    checkBoxesContainer.add(this._controls[day + 'DayCheckBox']);
                }
                return checkBoxesContainer;
            },

            _addWidgetToRecurringContainer : function(label, method)
            {
                var labelName = label + '.label';
                this._controls[labelName] = new qx.ui.basic.Label(Tools['tr'](this._prefix + label));
                this._controls['lessonRecurring'].add( this._controls[labelName], { column: 0, row: this._gridRecurringRowCounter} );

                this._controls[label] = this[method]();
                this._controls['lessonRecurring'].add( this._controls[label], { column: 1, row : this._gridRecurringRowCounter} );
                
                this._gridRecurringRowCounter += 1;
            },

            _createSelectBoxes : function() {
                for (var i = 0, length = this._labels.length; i < length; i++)
                {
                    var selectBox = this._createSelectBox(this._labels[i]);

                    this._controls['container'].add(
                        new qx.ui.basic.Label(Tools['tr'](this._prefix + this._labels[i])),
                        { column : 0, row : this._gridLessonRowCounter}
                    );
                    
                    this._controls['container'].add(selectBox, { column : 1, row : this._gridLessonRowCounter, colSpan : 2 });
                    this._gridLessonRowCounter++;
                }
            },

            _createSelectBox : function(label) {
                this._controls[label] = new frontend.lib.ui.form.SelectBox();
                this._controls[label].set({
                    required    : true,
                    tabIndex    : this._tabIndexCounter++
                });
                return this._controls[label];
            },

            _createContainer : function() {
                this._controls['container'] = new qx.ui.container.Composite(new qx.ui.layout.Grid(30, 15));
                return this._controls['container'];
            },

            _createButtonsContainer : function() {
                this._gridLessonRowCounter++;
                var container = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));

                container.add(this._createButton("buttonCancel"), { flex : 1 });
                container.add(this._createButton("buttonSave"), { flex : 1 });

                this._controls['container'].add(container, { column : 1, row : this._gridLessonRowCounter });
            },

            _createButton : function(label) {
                this._controls[label] = new qx.ui.form.Button(Tools['tr'](this._prefix + label));
                return this._controls[label];
            },

            _createDatePicker : function() {
                this._controls['datePicker'] = new frontend.lib.ui.form.DateField();
                return this._controls['datePicker'];
            },

            _createDatePickers : function() {
                var labels = ['date'], i = 0, length = labels.length;
                for (i; i < length; i++) {
                    var datepicker = this._createDatePicker(labels[i]);

                    this._controls['container'].add(new qx.ui.basic.Label(Tools['tr'](this._prefix + labels[i])), { column : 0, row : this._gridLessonRowCounter});
                    this._controls['container'].add(datepicker, { column : 1, row : this._gridLessonRowCounter, colSpan : 2 });
                    this._gridLessonRowCounter++;
                }
            },

            _createTimeFields : function() {
                this._gridLessonRowCounter++;
                this._controls['container'].add(new qx.ui.basic.Label(Tools['tr'](this._prefix + "hourStart")), { column : 0, row : this._gridLessonRowCounter});
                this._controls['container'].add(this._createTimeField('timeS'), { column : 1, row : this._gridLessonRowCounter, colSpan : 2 });

                this._gridLessonRowCounter++;
                this._controls['container'].add(new qx.ui.basic.Label(Tools['tr'](this._prefix + "hourEnd")), { column : 0, row : this._gridLessonRowCounter});
                this._controls['container'].add(this._createTimeField('timeE'), { column : 1, row : this._gridLessonRowCounter, colSpan : 2 });
            },

            _createTimeField : function(name) {
                this._controls[name] = new frontend.lib.ui.form.TimeFieldSpinner();
                this._controls[name].getChildControl('hourfield').setEditable(false);
                this._controls[name].getChildControl('minutefield').setEditable(false);
                return this._controls[name];
            },

            _setBehaviours : function(removeMode)
            {
                var mode = removeMode + 'Listener';
                var controls = {
                    'buttonSave'    : { type: 'execute', callback: '_onSaveButtonClick'             },
                    'buttonCancel'  : { type: 'execute', callback: '_onCancelButtonClick'           },
                    'courses'       : { type: 'changeSelection', callback: '_onModifiedSelectCourses'       },
                    'course_units'  : { type: 'changeSelection', callback: '_onModifiedSelectCourseUnits'   }
                };

                (!this._editMode) ?
                    controls['recurringCheckbox'] = { type: 'execute', callback: '_onRecurringCheckboxChecked' } : false;

                for (var key in controls) {
                    this._controls[key][mode](controls[key].type, this[controls[key].callback], this);
                }

                this[mode]('dataLoadingFinished', this._populate, this);
            },

            _getCourse: function(courseId) {
                var request = new frontend.lib.io.HttpRequest(Urls.resolve('COURSES', courseId), 'GET');
                request.addListener("success", function() {
                    this._getRooms(request.getResponseJson().training_center_id);
                }, this);
                request.send();
            },

            _getRooms : function(trainingCenterId) {
                var request = new frontend.lib.io.HttpRequest(Urls.resolve('ROOMS'), 'GET');
                request.setRequestData({ training_center_id : trainingCenterId });
                request.addListener("success", function() {
                    this._setSelectboxState('rooms', true, this._getSourceFromData(request.getResponseJson(), 'name'));
                    this.fireEvent('dataLoadingFinished');
                }, this);
                request.send();
            },

            _getSourceFromData : function(data, dataKey) {
                return new frontend.app.source.Source().set({
                    data    : (typeof data.length !== "undefined") ? data : [data],
                    dataKey : dataKey
                });
            },

            _onRecurringCheckboxChecked : function()
            {
                if( this._controls['recurringCheckbox'].getValue() )
                {
                    this._controls['lessonRecurring'].setVisibility('visible');
                    this.center();
                }
                else
                {
                    this._controls['lessonRecurring'].setVisibility('excluded');
                }
            },

            _onModifiedSelectCourses  : function() {
                var courseId = this._controls['courses'].getSelection()[0].getModel();

                this._data.courseUnits = this._calendar._data.courseUnits;
                this._setSelectboxState('course_units', true, this._getSourceFromData(this._calendar._data.courseUnits, 'name'));
                
                this._getCourse(courseId);
            },

            _onModifiedSelectCourseUnits : function() {
                var id = this._controls['course_units'].getSelection()[0].getModel(), userId = 0;
                for (var i = 0, length = this._calendar._data.courseUnits.length; i < length; i++)
                {
                    if (this._calendar._data.courseUnits[i].id == id) {
                        userId = this._calendar._data.courseUnits[i].user_id;
                        break;
                    }
                }

                var requestCoaches = new frontend.lib.io.HttpRequest(Urls.resolve('COACHES', userId), 'GET');
                requestCoaches.addListener("success", function()
                {
                    var data = requestCoaches.getResponseJson();
                    data = (typeof data.length !== "undefined") ? data : [data];
                    var source = new frontend.app.source.Source().set({
                        data    : data,
                        dataKey : 'username'
                    });

                    this._setSelectboxState('coaches', true, source);
                }, this);
                requestCoaches.send();
            },

            _onCancelButtonClick : function() {
                if (this._noRollbackEventsAfterCancel) {
                    this.close();
                }
                else {
                    this.fireDataEvent('cancel', this._event, this);
                    this.close();
                }
            },

            _getFormData : function() {
                var values = {};
                values.start_date = this._prepareDate('start');
                values.end_date = this._prepareDate('end');
                values.room_id = this._controls['rooms'].getSelection()[0].getModel();
                values.course_unit_id = this._controls['course_units'].getSelection()[0].getModel();
                values.user_id = this._controls['coaches'].getSelection()[0].getModel();

                return values;
            },

            _saveFormStateInCache : function() {
                window.cache = {};

                for (var i = 0; i < this._labels.length; i++) {
                    window.cache[this._labels[i]] = this._controls[this._labels[i]].getSelection()[0].getModel();
                }
            },
            
            _validateRecurringDetails : function()
            {
                var value = true;
                if(this._controls['repeat.how'].getSelection()[0].getModel() == 4) {
                    this._controls['repeat.how.howMuch'].validate();
                    value = this._controls['repeat.how.howMuch'].getValid();
                }

                if(this._controls['repeat.finish.after.x.times.radio'].getValue()) {
                    this._controls['repeat.finish.after.x.times.textField'].validate();
                    value = this._controls['repeat.finish.after.x.times.textField'].getValid();
                }

                if(this._controls['repeat.finish.in.day.radio'].getValue()) {
                    this._controls['repeat.finish.in.day.dateField'].validate();
                    value = this._controls['repeat.finish.in.day.dateField'].getValid();
                }
                return value;
            },

            _getRecurringDetails : function()
            {
                if (!this._validateRecurringDetails()) {
                    return null;
                }
                
                var recDetails = {};
                recDetails.repeatHow = this._controls['repeat.how'].getSelection()[0].getModel();
                recDetails.repeatIn = [];

                for(var i = 0, length = 7; i < length; i++) {
                    (this._controls[i + 'DayCheckBox'].getValue()) ? (recDetails.repeatIn.push(i + 1)) : false;
                }

                recDetails.repeatHowMuch = (recDetails.repeatHow === 4) ? this._controls['repeat.how.howMuch'].getValue() : null;

                recDetails.repeatFinishAfterXTimes = (this._controls['repeat.finish.after.x.times.radio'].getValue()) ?
                    this._controls['repeat.finish.after.x.times.textField'].getValue() : null;

                recDetails.repeatFinishInDay = (this._controls['repeat.finish.in.day.radio'].getValue()) ?
                    this._controls['repeat.finish.in.day.dateField'].getValue() : null;

                return recDetails;
            },

            _onSaveButtonClick : function() {
                var values = this._getFormData(),
                    length = this._calendar._data.courseUnits.length;

                if(!this._editMode && this._controls['recurringCheckbox'].getValue()) {
                    if (!this._validateRecurringDetails()) {
                        return false;
                    }
                }

                values.recurring = (!this._editMode && this._controls['recurringCheckbox'].getValue()) ?
                    qx.lang.Json.stringify(this._getRecurringDetails()) : null;

                for (var i = 0; i < length; i++)
                {
                    if (values.course_unit_id === this._calendar._data.courseUnits[i].id)
                    {
                        if (this._calendar._data.courseUnits[i].remaining_hours < 1 && (!this._editMode)) {
                            new frontend.lib.dialog.Error("calendar.lesson:hour_amount_limit");
                        }
                        else
                        {
                            var request = new frontend.lib.io.HttpRequest('', '', false);
                            request.set({
                                method          : this._editMode ? 'PUT' : 'POST',
                                url             : this._editMode ?
                                                    Urls.resolve("LESSONS", this._event.id) :
                                                    Urls.resolve("LESSONS"),
                                requestData     : values
                            });

                            request.addListener('load', function(){
                                switch(request.getStatus())
                                {
                                    case 200:
                                    case 201:
                                        this._saveFormStateInCache();
                                        this.fireEvent('completed');
                                        this.close();
                                    break;

                                    case 406:
                                        var response = request.getResponseJson();
                                        new frontend.lib.dialog.Error(this._getErrorsFromResponse(response));
                                    break;

                                    default:
                                    break;
                                }
                            }, this);
                            request.send();
                        }
                        break;
                    }
                }
            },

            _getErrorsFromResponse : function(response)
            {
                var responseText = '';

                if(response['course_unit_amount_limited']) {
                    responseText += "<strong>" +
                        Tools['tr']("calendar.lesson.collisions:course_unit_amount_limited") + "</strong>";
                }
                if(response['is_completed_lesson_edit']) {
                    responseText += "<strong>" +
                        Tools['tr']("calendar.lesson.collisions:edit_completed") + "</strong>";
                }
                if(response['is_creating_lesson_backward']) {
                    responseText += "<strong>" +
                        Tools['tr']("calendar.lesson.collisions:backward_date") + "</strong>";
                }
                if(response.is_end_date_before_start) {
                    responseText += "<hr><strong>" +
                        Tools['tr']("calendar.lesson.collisions:end_before_start") + "</strong>";
                }

                if(typeof response.collisions !== "undefined")
                {
                    var collisionText = '<hr><strong>' + Tools['tr']("calendar.lesson.collisions:found")
                        + '</strong><br><table cellspacing="10"><tr>' +
                        '<td><strong>Typ kolizji</strong></td>' +
                        '<td><strong>Data początkowa</strong></td>' +
                        '<td><strong>Data końcowa</strong></td></tr>';

                    var responseLabels = {};

                    responseLabels['coach']         = Tools['tr']('calendar.lesson.collisions:coach');
                    responseLabels['room']          = Tools['tr']('calendar.lesson.collisions:room');
                    responseLabels['course_unit']   = Tools['tr']('calendar.lesson.collisions:course_unit');

                    for(var i = 0, length = response.collisions.length; i < length; i++)
                    {
                        collisionText += '<tr><td>' + responseLabels[response.collisions[i].type] +
                        '</td><td>' + response.collisions[i].dateS +
                        '</td><td>' + response.collisions[i].dateE + '</td></tr>';
                    }

                    collisionText += '</table>';
                    responseText += collisionText;
                }
                return responseText;
            },

            _prepareDate : function(mode) {
                var date = this._controls['datePicker'].getValue();
                var dateString = date.getFullYear() + '-' + Tools.pad(( date.getMonth()) + 1) + '-' + Tools.pad(date.getDate()) + ' ';

                dateString += Tools.pad(this._controls[( mode == 'start' ) ? 'timeS' : 'timeE'].getValue());

                return dateString;
            }
        }
    });