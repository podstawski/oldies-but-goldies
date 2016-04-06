qx.Class.define("frontend.app.list.Survey",
    {
        extend: frontend.lib.list.AbstractNotTableOnly,

        include: [
            frontend.MMessage
        ],

        events:
        {
            "open" : "qx.event.type.Data",
            "edit" : "qx.event.type.Data",
            "define" : "qx.event.type.Data",
            "delete" : "qx.event.type.Data"
        },

        construct : function()
        {
            this.base(arguments);
            this.initRoles();

            /*var filter = this.getChildControl("toolbar").getChildControl("filter");
            filter.setLabel("Pokaż ankiety ze szkolenia");

            var request = new frontend.lib.io.HttpRequest;
            request.setUrl(Urls.resolve("COURSES"));
            request.addListenerOnce("success", function(e) {
                var data = request.getResponseJson();
                filter.set({
                    enabled : false, //TODO: true when it works
                    source  : new frontend.app.source.Source().set({
                        data    : data,
                        dataKey : "name"
                    })
                });
            }, this);
            request.send();
            */

            this.initDate();

            if (this._role == "user") {
                this._createSurveysTab(0, false, Tools.tr("surveys.tab.awaiting")); //my surveys
                this._createSurveysTab(0, true, Tools.tr("surveys.tab.filled")); //my surveys
            } else {
                this._createSurveysTab(0); //my surveys
                this._createSurveysTab(1); //survey archive
                this._createLibraryTab();
            }

            if (this._role == "user") {
                this.getChildControl("toolbar").getChildControl("delete-selected-button").addListener("appear", function(e) {
                    this.setVisibility("excluded");
                });

                this.getChildControl("toolbar").getChildControl("add-button").setVisibility("excluded");
            }
        },

        members:
        {
            _userData    : null,
            _currentDate : null,
            _role        : null,
            _type        : "survey",

            initDate : function() {
                var d = new Date();
                var month = ('0' + (d.getMonth() + 1)).slice(-2);
                this._currentDate = d.getFullYear() + "-" + month + "-" + d.getDate();
            },

            initRoles : function() {
                this._userData = frontend.app.Login.getUserInfo();
                this._role     = frontend.app.source.Roles.getInstance().getRoleName(this._userData.role_id);
            },
            sendRequest : function(options, closure) {
                if (typeof(options.context) == "undefined"){
                    options.context = this;
                }
                if (typeof(options.method) == "undefined"){
                    options.method = "POST";
                }
                if (typeof(options.data) == "undefined"){
                    options.data = {};
                }

                var request = new frontend.lib.io.HttpRequest;
                request.setUrl(options.url);
                request.setMethod(options.method);
                request.addListenerOnce("success", closure, options.context); //,this
                request.send();
            },


            _createSurveysTab : function(archive, filled, tabTitle) {
                var table, tableModel;
                tableModel = new frontend.lib.ui.table.model.Remote().set({
                    dataUrl : Urls.resolve("SURVEY", {archived: archive, filled:filled, type:this._type})
                });

                tableModel.setColumns(
                    ["Nazwa"],
                    ["id"]
                );

                var that = this;
                table = new frontend.lib.ui.table.List().set({
                    renderer : function(rowData) {
                        this.addTitle(rowData.name);
                        if (rowData.description != null) {
                            this.addLeft(rowData.description, Tools.tr("surveys.type.description"));
                        }
                        
                        this.addLeft(rowData.created_date, Tools.tr("surveys.type.created"));

                        if (rowData.username != null) {
                            this.addLeft(rowData.username, Tools.tr("surveys.type.created_by"));
                        }

                        if (that._role != "user") {
                            //buttons for coach, trainer, admin
                            this.addButton("delete", {row:0, column:1});
                            this.addButton("edit", {row:0, column:2});


                            if (rowData.completed == null){
                                if (!archive){
                                    this.addButton("sendSurvey", {row:0, column:4});
                                    this.addButton("finishSurvey", {row:0, column:3});
                                }

                            }else{
                                this.addButton("resumeSurvey", {row:0, column:3});
                                this.addLeft(rowData.completed, Tools.tr("surveys.completed"));
                            }

                            this.addButton("surveyResults", {row:1, column:1});
                            this.addButton("surveyAddToLibrary", {row:1, column:2});
                            this.addButton(archive ? "dearchiveSurvey" : "archiveSurvey", {row:1, column:3});
                        } else {
                            //buttons for user

                            //if there's a deadline, display it
                            if (rowData.deadline != null && rowData.deadline != "1970-01-01") {
                                this.addLeft(rowData.deadline , Tools.tr("surveys.type.deadline"));
                            }
                            if (rowData.completed != null && rowData.completed != 0){
                                this.addLeft(
                                    (that._type == "survey" ? "Ankieta została zakończona" : "Test został zakończony") , "Uwaga");
                            }

                            //if user didn't fill the survey/test
                            if (!filled) {

                                //and there's no deadline, or deadline is in future, and coach/trainer didn't finished the survey
                                if ((rowData.deadline == null || rowData.deadline == '1970-01-01' || new Date(rowData.deadline) > new Date(that._currentDate)) && rowData.completed == null) {
                                    this.addButton("fillSurvey");
                                }
                            }else{
                                //if user filled the survey and survey ain't completed by coach/trainer, let him show results\
                                //if survey wasn't filled by user, and it was finished by coach, user cannot see the results
                                if (rowData.completed != null){
                                    this.addButton("detailedResults");
                                }
                            }
                        }
                    },
                    rowHeight       : 150,
                    tableModel      : tableModel,
                    addFormClass    : "frontend.app.form.survey.Add",
                    editFormClass   : "frontend.app.form.survey.Edit"
                });

                table.addListener("detailedResultsRowClick", function(e) {
                    var rowData = e.getData();
                    rowData.survey_id = rowData.id;
                        
                    if (this._role == "user"){
                        rowData.user_id = this._userData.id;
                    }
                    
                    var resultsWindow = new frontend.app.form.survey.DetailedResults(rowData, this._type);
                }, this);

                table.addListener("fillSurveyRowClick", function(e) {
                    var rowData = e.getData();
                    var fillWindow = new frontend.app.form.survey.Fill(rowData, this._type);
                    fillWindow.show();

                    fillWindow.addListener("completed", function(e) {
                        this._focusOnTab(1);
                        this.reloadData();
                    }, this);
                }, this);

                table.addListener("sendSurveyRowClick", function(e) {
                    var rowData = e.getData();
                    var fillWindow = new frontend.app.form.survey.Send(rowData, this._type);
                    fillWindow.show();
                }, this);

                table.addListener("finishSurveyRowClick", this._finishResume('finish'), this);
                table.addListener("resumeSurveyRowClick", this._finishResume('resume'), this);

                table.addListener("surveyResultsRowClick", function(e) {
                    var rowData = e.getData();
                    that._loadResultsTab(rowData);
                });

                table.addListener("surveyAddToLibraryRowClick", this._addToLibrary, this);

                if (archive) {
                    table.addListener("dearchiveSurveyRowClick", this._archive, this);
                } else {
                    table.addListener("archiveSurveyRowClick", this._archive, this);
                }

                this.addTab(typeof(tabTitle) == "undefined" ?
                    (archive ?
                        Tools.tr("surveys.tab.archive") :
                        (this._type == "survey" ?
                            Tools.tr("surveys.tab.my_surveys") :
                            Tools.tr("surveys.tab.my_tests")
                            )
                        ) : tabTitle,
                    table);

            },

            _onAddButtonClick : function(e) {
                var addWindow = new frontend.app.form.survey.Add(this._type);

                addWindow.addListener("completed", function(e) {
                    this.reloadData();
                }, this);
                addWindow.show();
            },

            _onEditRowClick : function(e) {
                var rowData = e.getData();

                var editWindow = new frontend.app.form.survey.Edit(rowData, this._type);
                editWindow.addListener("completed", function(e) {
                    this.reloadData();
                }, this);
                editWindow.show();
            },

            _onDeleteRowClick : function(e) {
                var rowData = e.getData();
                var dialog = new frontend.lib.dialog.Confirm("Na pewno chcesz usunąć tę pozycję?");

                dialog.addListenerOnce("yes", function(e) {
                    var url = Urls.resolve('SURVEY', {
                      id: rowData.id,
                      method: (this._isLibraryTab() && rowData.library == 1 ? "removeFromLibrary" : null)
                    })
                    
                    this.sendRequest(
                        {
                            url : url,
                            method: "DELETE",
                            context: this
                        },
                        function(e){
                            this.showMessage("Rekord został usunięty!");
                            this.reloadData();
                        }
                    );
                }, this);

            },
            _finishResume : function(what){

                return function (e){
                    var rowData = e.getData();
                    this.sendRequest(
                        {
                            url : Urls.resolve("SURVEY",{
                                surveyId: rowData.id,
                                method: what
                            }),
                            method: "POST"
                        },
                        function(e){
                            this.showMessage(
                                this._type == "survey" ?
                                    (what == "finish" ? Tools.tr("Zakończono ankietę.") : Tools.tr("Wznowiono ankietę.")) :
                                    (what == "finish" ? Tools.tr("Zakończono test") : Tools.tr("Wznowiono test."))
                            );
                            this.reloadData();
                        }
                    )
                }

            },
            _archive : function(e) {
                var that = this;
                var rowData = e.getData();

                var data = {archive:(rowData.archived ? 0 : 1), id:rowData.id};
                this.sendRequest(
                    {
                        url : Urls.resolve("SURVEY",{
                            id: rowData.id,
                            type: this._type,
                            data: qx.util.Serializer.toJson(data)
                        }),
                        method: "PUT",
                        context: that
                    },
                    function(e){
                        if (!rowData.archived) {
                            this.showMessage("Dodano do archiwum.");
                            this._focusOnTab(1);
                        } else {
                            this.showMessage("Przeniesiono z archiwum.");
                        }
                        this.reloadData();
                    }
                );
            },

            _addToLibrary : function(e) 
            {
                var rowData = e.getData();
                this.sendRequest(
                    {
                        url: Urls.resolve("SURVEY", {surveyId: rowData.id,method:"addToLibrary"}),
                        method: "POST",
                        context: this
                    },
                    function(e){
                        this.showMessage("Dodano do biblioteki.");
                        this._focusOnTab(2);
                        this.reloadData();
                    }
                );
            },

            _createLibraryTab : function() 
            {
                var table, tableModel, roles = frontend.app.source.Roles.getInstance();
                tableModel = new frontend.lib.ui.table.model.Remote().set({
                    dataUrl : Urls.resolve("SURVEY", {method: "library", type: this._type})
                });
                tableModel.setColumns(
                    ["ID", "Nazwa"],
                    ["id", "name"]
                );
                var that = this;
                table = new frontend.lib.ui.table.List().set({
                    renderer : function(rowData) {
                        this.addTitle(rowData.name);
                        rowData.library = 1;
                        if (rowData.description != null) {
                            this.addLeft(rowData.description, Tools.tr("surveys.type.description"));
                        }
                        this.addLeft(rowData.created_date, Tools.tr("surveys.type.created"));
                        if(rowData.username != null){
                            this.addLeft(rowData.username, Tools.tr("surveys.type.created_by"));
                        }

                        if (that._role == "admin") {
                            this.addButton("delete");
                        }
                        if(that._roles.join('.').indexOf(that._role) !== false){
                            this.addButton(this._type == "survey" ? "copyToMySurveys" : "copyToMyTests");
                        }
                    },
                    rowHeight       : 130,
                    tableModel      : tableModel,
                    editFormClass   : "frontend.app.form.survey.Edit"
                });

                table.addListener("sendSurveyRowClick", function(e) {
                    var rowData = e.getData();
                    var fillWindow = new frontend.app.form.survey.Send(rowData);
                    fillWindow.show();
                });

                table.addListener(this._type == "survey" ? "copyToMySurveysRowClick" : "copyToMyTestsRowClick", function(e) {
                    var rowData = e.getData();

                    this.sendRequest(
                        {
                            url: Urls.resolve("SURVEY", {surveyId: rowData.id,method:"copyFromLibrary"}),
                            method: "POST"
                        },
                        function(e){
                            this.showMessage("Skopiowano z biblioteki.");
                            this._focusOnTab(0);
                            this.reloadData();
                        }
                    );
                }, this);
                this.addTab(Tools.tr("surveys.tab.survey_library"), table);
            },


            _loadResultsTab : function(rowData) {
                var resultsTable, resultsModel, that = this;
                
                resultsModel = new frontend.lib.ui.table.model.Remote().set({
                    dataUrl : Urls.resolve("SURVEY_GROUPS", {
                        surveyId : rowData.id,
                        type : this._type
                    })
                });
                resultsModel.addListener("loadedRowCountFailed", function(e){
                    this.showMessage("Brak wyników");    
                },this);
               
                resultsModel.setUserData("surveyId", rowData.id);
                resultsModel.setColumns(
                    ["Nazwa grupy"],
                    ["name"]
                );
                
                resultsTable = new frontend.lib.ui.table.List().set({
                    renderer : function(rowData) {
                        this.addTitle(rowData.name);

                        if (that._type == "test") {
                            this.addLeft(
                                (
                                    rowData.average_score != null ?
                                        (rowData.average_score + "%") :
                                        Tools["tr"]("surveys.list.nodata").toString() //if test is not filled by anyone or contains only open questions
                                    ),
                                Tools.tr("surveys.list.average")
                            );
                        }
                        if (rowData.advance_level != null){
                            this.addLeft(rowData.advance_level, Tools.tr("surveys.list.advance_level"));
                        }

                        this.addLeft(rowData.replies_count, Tools.tr("surveys.list.replies_count"));

                        if (that._role != "user") {
                            this.addButton("groupSummary");
                            this.addButton('generateReport');
                        }
                    },
                    rowHeight       : 150,
                    tableModel      : resultsModel,
                    showCheckboxes  : false
                });
                 
                resultsTable.addListener("groupSummaryRowClick", function(e) 
                {
                    var rowData = e.getData();
                    if (that._type == "test") {
                        that._loadUsersTab(rowData);
                    } else {
                        that._loadSurveyResultsTab(rowData);
                    }
                });

                resultsTable.addListener("generateReportRowClick", function(e) {
                    var data = e.getData();
                    frontend.app.module.report.ReportPicker.openForm({
                        group_id: data.group_id,
                        survey_id: data.survey_id
                    }, true);
                }, this);

//                resultsTable.addListener("detailsRowClick", function(e) {
//                    var rowData = e.getData();
//                    if (that._type == "test") {
//                        that._loadUsersTab(rowData);
//                    } else {
//                        that._loadSurveyResultsTab(rowData);
//                    }
//
//                    Tutaj ma sie pokazywac z podzialem na uzytkownikow.
//                });

                this.addTab(Tools.tr("surveys.tab.results") + ": " + rowData.name, resultsTable);

                var pages = this.getChildControl("tabview").getChildren();
                pages[pages.length - 1].setShowCloseButton(true);

                this.getChildControl("tabview").setSelection([pages[pages.length - 1]]);

            },
            _loadUsersTab : function(rowData) 
            {
                var table, tableModel, that = this;
                tableModel = new frontend.lib.ui.table.model.Remote().set({
                    dataUrl : Urls.resolve("SURVEY", {
                        method: "listForGroupAndSurvey",
                        surveyId: rowData.survey_id,
                        groupId: rowData.group_id,
                        type: that._type
                    })
                });

                tableModel.setColumns(
                    ["Login"],
                    ["username"]
                );
                var oldRowData = rowData;
                table = new frontend.lib.ui.table.List().set({
                    renderer : function(rowData) {
                        this.addTitle(rowData.username);
                        if (rowData.created !== null){
                            this.addLeft((rowData.created).substring(0, 16), "Wypełniono");
                        }
                        if (that._type == "test") {

                            this.addLeft(
                                (
                                    rowData.average_score != null ?
                                        (rowData.average_score + "%") :
                                        Tools.tr("surveys.list.nodata").toString() //if test is not filled by anyone or contains only open questions
                                    ),
                                Tools.tr("surveys.list.average")
                            );
                        }
                        
                        this.addButton("detailedResults"); //wyniki szczegolowe dla tej ankiety przez danego uzytkownika

                        this.addButton("averageResults"); //wyniki srednie dla wszystkich ankiet tego uzytkownika

                    },
                    rowHeight       : 100,
                    tableModel      : tableModel,
                    showCheckboxes  : false

                });
                var surveyId = rowData.survey_id;

                table.addListener("detailedResultsRowClick", function(e) {
                    var rowData = e.getData();
                    rowData.survey_id = surveyId;
                    rowData.group_id = oldRowData.group_id;
                    rowData.user_id = rowData.id;

                    var resultsWindow = new frontend.app.form.survey.DetailedResults(rowData, this._type);
                }, this);

                table.addListener("averageResultsRowClick", function(e) {
                    var rowData = e.getData();
                    rowData.survey_id = surveyId;
                    rowData.group_id = oldRowData.group_id;
                    var resultsWindow = new frontend.app.form.survey.AverageResults(rowData, this._type);
                });


                var tab = this.addTab(Tools["tr"](Tools.tr("surveys.tab.group_results") + ": " + rowData.name), table);

                var pages = this.getChildControl("tabview").getChildren();
                pages[pages.length - 1].setShowCloseButton(true);

                this.getChildControl("tabview").setSelection([pages[pages.length - 1]]);

            },
            _loadSurveyResultsTab : function(rowData) 
            {

                this.sendRequest(
                    {
                        url: Urls.resolve("SURVEY", {
                            method: "averageSurveyResults",
                            surveyId: rowData.survey_id,
                            groupId: rowData.id,
                            type: this._type
                        }),
                        method: "GET"
                    },
                    this.generateResults(rowData)
                );
            },
            generateResults: function(rowData) 
            {
                return function(e){
                    var data = qx.lang.Json.parse(e.getTarget().getResponse());

                  
                    var size = Tools.dimensions(0.55, 0.6);

                    var cnt = new qx.ui.container.Composite(new qx.ui.layout.VBox());
                    var page = new qx.ui.tabview.Page(Tools.tr("surveys.tab.group_results") + ": " + rowData.name);
                    
                    var scroll = new qx.ui.container.Scroll().set({
                        minHeight: size.h
                    });

                    scroll.add(cnt);

                    page.setLayout(new qx.ui.layout.VBox());

                    for (var i in data) {
                        cnt.add(new qx.ui.basic.Label().set({value:"<b>" +
                            (this._type == "survey" ? Tools.tr("surveys.type.survey") : Tools.tr("surveys.type.test")) + ": "
                            + data[i][0].name + "</b><br /> <br />",rich:true}));

                        break;
                    }

                    for (var i in data) {
                        if (typeof(data[i]) !== 'function') {
                            var label = new qx.ui.basic.Label().set({value:"<br /><b>" + data[i][0].title + "</b>",rich:true});
                            label._id = i;
                            // label.addListener("click", function(e) {
                            //
                            // });
                            cnt.add(label);
                            for (var j = 0; j < data[i].length; ++j) {
                                cnt.add(
                                    new qx.ui.basic.Label(
                                        (data[i][j].content == null ? "<i>" + Tools.tr("Pytanie otwarte") + "</i>": data[i][j].content)+
                                        "  (" + data[i][j].replies_count + " odpowiedzi)"
                                    ).set({rich:true})
                                );
                            }
                        }
                    }

                    page.add(scroll);

                    this.getChildControl("tabview").add(page);

                    var pages = this.getChildControl("tabview").getChildren();
                    pages[pages.length - 1].setShowCloseButton(true);

                    this.getChildControl("tabview").setSelection([page]);
                }

            },

            _focusOnTab : function(number) 
            {
                var pages = this.getChildControl('tabview').getChildren();
                this.getChildControl('tabview').setSelection([pages[number]]);
            },

            _getCurrentTab : function()
            {
                var pages = this.getChildControl('tabview').getChildren();
                var currentTab = this.getChildControl('tabview').getSelection();
                    currentTab = currentTab[0];
                var currentPage = null;
                for(var i = 0; i < pages.length; ++i){
                    if (pages[i] == currentTab){
                        currentPage = i;
                    }
                }
                return currentPage;
            },
            _isMySurveysTab : function()
            {
                return 0 == this._getCurrentTab();
            },
            _isArchiveTab : function()
            {
                return 1 == this._getCurrentTab();
                
            },
            _isLibraryTab : function()
            {
               return 2 == this._getCurrentTab(); 
            }
        }
    });