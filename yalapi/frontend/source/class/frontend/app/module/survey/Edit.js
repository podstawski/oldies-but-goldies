/*
Refactoring nr 3:
       _init()
       _onGetDataFinished

        _onRemoveAnswerButtonClicked
        _onRemoveQuestionButtonClicked
        _onChangeSelection
        _onAddQuestionButtonClicked
        _appendAnswer
        _appendAddAnswerButton
       * _appendQuestion
         _deleteQuestion
         _deleteAnswer

 */


qx.Class.define("frontend.app.module.survey.Edit",
{
    extend : qx.ui.window.Window,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData)
    {
        this.base(arguments);
        this._init();

        this._form = frontend.lib.ui.form.Form.create("survey.create").set({
            url : Urls.resolve("SURVEY", rowData.id),
            method : "PUT"
        });
        this._form.addListener("completed", this._onComplete, this);

        var gb = new qx.ui.groupbox.GroupBox("Ankieta");
        gb.setLayout(new qx.ui.layout.VBox());
        gb.add(this._form);

        this._composite.add(gb);//this._form);

        this._objects.survey = {};

        this._objects.survey.name = this._form.getItem("name");
        this._objects.survey.description = this._form.getItem("description");
        this._objects.survey.type = this._form.getItem("type");

        var saveButton =new qx.ui.form.Button(Tools.tr("survey.create:save"), "icon/16/actions/document-save.png");
        saveButton.addListener("click", this.__save, this);

        this._composite.add(saveButton);

        var addQuestionButton = new qx.ui.form.Button(Tools.tr("survey.create:add_question"), "icon/16/actions/list-add.png");
        addQuestionButton.addListener("click", this._onAddQuestionButtonClick, this);
        this._composite.add(addQuestionButton);

        var request = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY", rowData.id), "GET");
        request.addListener("success", function() {
            this._afterGetData(request.getResponseJson());
        }, this);
        request.send();

        this.add(this._container);
        this.setWidth(850);
        this.setHeight(600);
        this.center();
    },

    members :
    {
        /* refactor */
        _init : function(){
            this._container =  new qx.ui.container.Scroll();

            this._composite = new qx.ui.container.Composite;
                this._composite.setLayout(new qx.ui.layout.VBox(5));
                this._container.setWidth(800);
                this._container.setHeight(800);

            this._container.add(this._composite);

            this.set({
                layout : new qx.ui.layout.VBox,
                width : 800
            });
            this.setCaption(
                Tools.tr("Edycja ankiety")
            );
            this._objects.survey = {};
            this._objects["questions"] = [];

        },

        _afterGetData : function(data) {
            //Populate form with data
            this._form.setUserData("data", data);
            this._form.populate(data);

            for (var i in data.questions){  //Add inputs for each question
                if (typeof(data.questions[i]) !== 'function'){

                    var answerContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(5)).set({
                        maxWidth:    800,
                        paddingLeft: 10,
                        marginTop: 20
                     });

                    var addQuestionForm = frontend.lib.ui.form.Form.create("survey.addquestion").set({width:735});

                    var question = {
                        "title"     : addQuestionForm.getItem("title").setValue(data.questions[i].title),
                        "help"      : addQuestionForm.getItem("help").setValue(data.questions[i].help),
                        "required"  : addQuestionForm.getItem("required").setValue((1==data.questions[i].required)),
                        "type"      : addQuestionForm.getItem("type").setDefaultOption(data.questions[i].type),
                        "hash"      : addQuestionForm.getItem("title").$$hash,
                        "possible_answers" : []
                    };

                    addQuestionForm.getItem("type").addListener("changeSelection", this._changeSelection(answerContainer, question));

                    var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5)).set({marginTop: 30});
                    questionContainer.setWidth(700);


                    var rbContainer = new qx.ui.container.Composite(new qx.ui.layout.Basic);
                    var removeButton = new qx.ui.form.Button(null, "icon/16/actions/edit-delete.png");
                    rbContainer.add(removeButton);

                    questionContainer.add(addQuestionForm);
                    questionContainer.add(rbContainer);

                    var gb = new qx.ui.groupbox.GroupBox("Pytanie");
                    gb.setLayout(new qx.ui.layout.VBox());

                    removeButton.addListener("click", this._onRemoveBtnClick(question, gb), this);


                    gb.add(questionContainer);
                    gb.add(answerContainer);
                    this._composite.add(gb);

                    if (data.questions[i].possible_answers != undefined){
                           for(var j in data.questions[i].possible_answers){
                            if (typeof(data.questions[i].possible_answers[j]) !== 'function') {
                                this._appendAnswer(data.questions[i].type, question, answerContainer, data.questions[i].possible_answers[j].content);
                            }
                        }
                    }
                    this._appendAddAnswerButton(data.questions[i].type, answerContainer, question);
                    this._objects["questions"].push(question);
                }
            }
         },
        _form : null,
        _container : null,
        _composite : null,
        _objects : {}    ,
        _values :{},
        _data : {},

        _onComplete : function(e)
        {
            this.showMessage("Zmiany zapisane!");
            this.close();
        },
        _onRemoveAnswerButtonClicked : function(answer, horizontalContainer){
             return function (e)
             {
                this._delete(answer.$$hash);
                horizontalContainer.removeAll();
                horizontalContainer.remove();
                horizontalContainer.dispose();
             }
        },
        _onRemoveBtnClick : function(question, gb)
        {
            var that = this;
            return function (e)
            {
                that.__delQuestion(question.hash);
                gb.removeAll();
                this._composite.remove(gb);

            }
        },
        __delQuestion : function(hash){
//
            var questions = this._objects["questions"];
            for (var i = 0; i < questions.length; ++i){
//
                if (questions[i].title.$$hash === hash){
                    qx.lang.Array.removeAt(questions, i);
                }
            }
        },
        _appendAddAnswerButton : function(questionType, answerContainer, question){
                var addMoreAnswersButton = new qx.ui.form.Button(Tools.tr("survey.create:add_answer"), "icon/16/actions/list-add.png").set({
                    maxWidth:150,
                    marginLeft:20,
                    opacity: 0.7
                });

                addMoreAnswersButton.addListener("execute", function(e)
                {
                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.Grid().setColumnWidth(0, 15));

                    if (questionType != "list"){
                        var answerType = (questionType == "multichoice") ? new qx.ui.form.RadioButton : new qx.ui.form.CheckBox;
                        horizontalContainer.add(answerType, {row:0, column:0});
                    }
                    var answer = new qx.ui.form.TextField().set({
                                    width: 700,
                                    placeholder: Tools.tr("survey.create:answer"),
                                    paddingLeft: 10,
                                    marginLeft: 10
                    });
                    question["possible_answers"].push({"object" :answer, "value_object": answerType });

                    horizontalContainer.add(answer, {row:0,column:1});
                    var deleteButton = new qx.ui.form.Button(null, "icon/16/actions/list-remove.png").set({"marginLeft":10});

                    deleteButton.addListener("execute", function()
                    {
                        this._delete(answer.$$hash);
                        horizontalContainer.removeAll();
                    }, this);

                    horizontalContainer.add(deleteButton,{row:0, column:2});

                    answerContainer.addBefore(horizontalContainer,addMoreAnswersButton);
                },this);
                answerContainer.add(addMoreAnswersButton);
        },
        _appendAnswer : function(questionType, question, answerContainer, val){
            switch (questionType){
                case "text":
                    var answer = new qx.ui.form.TextField().set({
                        enabled: false,
                        value: Tools.tr("survey.create:user_answer"),
                        width: 700,
                        paddingLeft: 10,
                        marginLeft: 10
                    });
                    question["possible_answers"].push(answer);
                    answerContainer.add(answer);
                    break;
                case "multichoice":
                case "checkboxes":
                case "list":
                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.Grid().setColumnWidth(0, 15));

                    if (questionType != "list"){
                        var answerType =  (questionType == "multichoice") ? new qx.ui.form.RadioButton : new qx.ui.form.CheckBox;
                        horizontalContainer.add(answerType, {row:0, column:0});
                    }
                    var answer = new qx.ui.form.TextField().set({
                        width: 700,
                        value: val,
                        paddingLeft: 10,
                        marginLeft: 10,
                        placeholder: Tools.tr("survey.create:answer")
                    });

                    question["possible_answers"].push({
                        "object": answer,
                        "value_object": answerType
                    });

                    horizontalContainer.add(
                        answer, {row:0,column:1}
                    );
                    answerContainer.add(horizontalContainer);


                    var removeAnswerButton = new qx.ui.form.Button(null, "icon/16/actions/list-remove.png").set({"marginLeft":10});

                    removeAnswerButton.addListener("click", function()
                    {
                        this._delete(answer.$$hash);

                        horizontalContainer.removeAll();
                        answerContainer.remove(horizontalContainer);
                    }, this);
                    horizontalContainer.add(removeAnswerButton,{row:0, column:2});
                break;
            }
        },
        _delete : function(hash, val)
        {
            var values = null;
            if (val){
                values = val;
            }else{
                values = this._objects["questions"];
            }

            for (var i = 0; i < values.length; ++i){

                if (values[i] !== undefined){
                    if (values[i].object !== undefined){
                        if (values[i].object.$$hash === hash){
                            qx.lang.Array.removeAt(values, i);
                            return;
                        }
                    }
                    if (values[i].hasOwnProperty('hash')){
                        if (values[i].hash === hash){
                            qx.lang.Array.removeAt(values, i);
                            return;
                        }
                    }

                    if (values[i]["possible_answers"] instanceof Array){
                        this._delete(hash, values[i]["possible_answers"]);
                    }
                }

            }

        },
        _changeSelection : function(previewContainer, question)
        {
            var that = this;
            return function(e) {
                var questionType = e.__data[0].$$user_model;
                previewContainer.removeAll();
                that._appendAnswer(questionType, question, previewContainer, "");
                that._appendAddAnswerButton(questionType, previewContainer, question);
            }

        },
        __save : function(e)
        {

            //if (!this._manager.validate()){
                //return false;
            //}

            //rewriting from _objects to json goes here
            this._values = {};
            this._values["questions"] = [];
            this._values["survey"] = {};

            this._values["survey"].name        = this._objects.survey.name.getValue();
            this._values["survey"].description = this._objects.survey.description.getValue();
            this._values["survey"].type        = this._objects.survey.type.getSelection()[0].$$user_model;

            var questionsCount = this._objects.questions.length;
            for (var i = 0; i < questionsCount; i++){

                this._values.questions[i] = {};
                this._values.questions[i].title    = this._objects.questions[i].title.getValue();
                this._values.questions[i].type     = this._objects.questions[i].type.getSelection()[0].$$user_model;
                this._values.questions[i].help     = this._objects.questions[i].help.getValue();
                this._values.questions[i].required = this._objects.questions[i].required.getValue();

                this._values.questions[i].possible_answers = [];

                var answersCount = this._objects.questions[i].possible_answers.length;
                for (var j = 0; j < answersCount; j++){
                    this._values.questions[i].possible_answers.push({
                            "content" : this._objects.questions[i].possible_answers[j].object.getValue(),
                            "correct" : this._objects.questions[i].possible_answers[j].value_object.getValue()
                    });
                }
            }

            /*var req  = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY"), "PUT");
            req.setRequestData({data:qx.util.Serializer.toJson(this._values)});
            req.addListener("success", function(e) {
                qx.core.Init.getApplication().setContent('Survey.List');
                this.close();
            });
            req.send();*/
//

        },
        _onAddQuestionButtonClick : function(){
            var previewContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox()).set({
                        maxWidth:    800,
                        paddingLeft: 10
            });

            var form = frontend.lib.ui.form.Form.create("survey.addquestion").set({width:735});

            var question = {
                "title"     : form.getItem("title"),
                "help"      : form.getItem("help"),
                "required"  : form.getItem("required"),
                "type"      : form.getItem("type"),
                "possible_answers" : []
            };

            this._objects["questions"].push(question);

            form.getItem("type").addListener("changeSelection", this._changeSelection(previewContainer, question));

            var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
            questionContainer.setWidth(800);

           // questionContainer.add(new qx.ui.form.renderer.Single(form).set({'minWidth':400}), {left:20, top:20});

            var rbContainer = new qx.ui.container.Composite(new qx.ui.layout.Basic);
            var removeButton = new qx.ui.form.Button(null, "icon/16/actions/edit-delete.png");
            rbContainer.add(removeButton);


            questionContainer.add(form);
            questionContainer.add(rbContainer);
            var gb = new qx.ui.groupbox.GroupBox("Pytanie");
            gb.setLayout(new qx.ui.layout.VBox());
            gb.add(questionContainer);
            gb.add(previewContainer);
            removeButton.addListener("execute", this._onRemoveBtnClick(question, gb));


            this._composite.add(gb);
        }
    }
});