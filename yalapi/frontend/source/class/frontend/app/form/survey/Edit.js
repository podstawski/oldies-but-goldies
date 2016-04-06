qx.Class.define("frontend.app.form.survey.Edit",
{
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData, type)
    {
        this.base(arguments);
        this._type = type;
        this.setWidth(800);
        this.setHeight(600);
        this._init();

        this._form = frontend.lib.ui.form.Form.create(this._surveyTemplate).set({
            url : Urls.resolve("SURVEY", rowData.id),
            method : "PUT"
        });

        this._form.getLayout().setSpacing(0);

        var name = this._form.getItem('name');
            name.set({marginBottom:5});
            name.setRequired(true);

        var groupBox = new qx.ui.groupbox.GroupBox();
            groupBox.setLayout(new qx.ui.layout.VBox());
            groupBox.add(this._form);

        this._manager.add(name);
        this._composite.add(groupBox);

        this._objects.survey = {};
        this._objects.survey.name = this._form.getItem("name");
        this._objects.survey.description = this._form.getItem("description");

        var request = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY", rowData.id), "GET");
        request.addListener("success", function() {
            this._afterGetData(request.getResponseJson());
        }, this);
        request.send();

        this.add(this._container, {flex:1});

        var addQuestionButton = new qx.ui.form.Button(Tools.tr("survey.create:add_question"), "icon/16/actions/list-add.png");
            addQuestionButton.addListener("click", this._onAddQuestionButtonClick, this);

        var buttonsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
        buttonsContainer.setPaddingTop(10);

        var saveButton = new qx.ui.form.Button(Tools.tr("survey.create:save"), "icon/16/actions/document-save.png");
            saveButton.addListener("execute", this.__save, this);

        var cancelButton = new qx.ui.form.Button(Tools.tr("survey.create:cancel"), "icon/16/actions/dialog-cancel.png");
            cancelButton.addListener("click", function(){ this.close() }, this);

        buttonsContainer.add(addQuestionButton);
        buttonsContainer.add(new qx.ui.core.Spacer(), {flex:1});
        buttonsContainer.add(cancelButton);
        buttonsContainer.add(saveButton);
        this.add(buttonsContainer);

        this.setShowMinimize(false);
        this.setShowMaximize(false);
        this.center();
    },

    members :
    {
        _type : null,
        _form : null,
        _container : null,
        _composite : null,
        _objects : {}    ,
        _values :{},
        _data : {},
        _id : {},
        _manager : {},
        _surveyTemplate : {
            name: {
                "type" : "TextField",
                properties: {
                    "required" : true
                },
                "nolabel" : true,
                "validators" : [
                    Validate.string(),
                    Validate.slength(6, 32)
                ]
            },
            description: {
                "type" : "TextArea",
                "nolabel" : true,
                properties: {
                    "required" : false
                }
            }
        },
        _questionTemplate: {
            title: {
                "type" : "TextField",
                properties: {
                    "required" : true
                },
                "validators" : [
                    Validate.string(),
                    Validate.slength(6, 32)
                ]
            },
            help: {
                "type" : "TextField",
                properties: {
                    "required" : false
                }
            },
            required: {
                "type" : "CheckBox"
            },
            type: {
                "type" : "SelectBox",

                properties: {
                    source: "QuestionTypes",
                    required : true
                }
            }
        } ,
        _init : function(){
            this._container =  new qx.ui.container.Scroll();

            this._composite = new qx.ui.container.Composite;
            this._composite.setLayout(new qx.ui.layout.VBox(5));

            this._container.add(this._composite);

            this.set({
                layout : new qx.ui.layout.VBox
            });

            this._objects.survey = {};
            this._objects["questions"] = [];
            this._manager = new qx.ui.form.validation.Manager();
        },

        _afterGetData : function(data) {
            this._form.setUserData("data", data);
            this._form.populate(data);
            this._id = data['id'];

            this.setCaption(Tools.tr("Edycja: ") + data.name);
            for (var i in data.questions){  //Add inputs for each question
                if (typeof(data.questions[i]) !== 'function'){

                    var answerContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(5)).set({
                        marginTop: 5
                     });

                    var addQuestionForm = frontend.lib.ui.form.Form.create(this._questionTemplate, "survey.addquestion");

                    var title = addQuestionForm.getItem("title");
                        title.setValue(data.questions[i].title);
                        title.setRequired(true);
                        this._manager.add(title);

                    var help = addQuestionForm.getItem("help");
                        help.setValue(data.questions[i].help);

                    var required = addQuestionForm.getItem("required");
                        required.setValue((1==data.questions[i].required));

                    var type = addQuestionForm.getItem("type");
                        type.setDefaultOption(data.questions[i].type);

                    type.removeListener("mousewheel", type._onMouseWheel, type);

                    var question = {
                        "title"     : title,
                        "help"      : help,
                        "required"  : required,
                        "type"      : type,
                        "hash"      : addQuestionForm.getItem("title").$$hash,
                        "possible_answers" : []
                    };


                    addQuestionForm.getItem("type").addListener("changeSelection", this._changeSelection(answerContainer, question));

                    var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                    var removeButton = new qx.ui.basic.Image("icon/16/actions/edit-delete.png").set({marginLeft:15});
                    questionContainer.add(addQuestionForm, {flex:1});
                    questionContainer.add(removeButton);

                    var groupBox = new qx.ui.groupbox.GroupBox(Tools.tr("survey.create:question"));
                        groupBox.setLayout(new qx.ui.layout.VBox());

                    removeButton.addListener("click", this._onRemoveBtnClick(question, groupBox), this);

                    groupBox.add(questionContainer);
                    groupBox.add(answerContainer);
                    this._container.scrollChildIntoView(groupBox);
                    this._composite.add(groupBox);

                    if (data.questions[i].possible_answers != undefined){
                       var radioGroup = new qx.ui.form.RadioGroup();

                       for(var j in data.questions[i].possible_answers){
                            if (typeof(data.questions[i].possible_answers[j]) !== 'function') {
                                this._appendAnswer(
                                    data.questions[i].type,
                                    question,
                                    answerContainer,
                                    data.questions[i].possible_answers[j].content,
                                    data.questions[i].possible_answers[j].id,
                                    radioGroup,
                                    data.questions[i].possible_answers[j].correct,
                                    j
                                );
                            }
                        }
                    }

                    var radioGroup = new qx.ui.form.RadioGroup();

                    if (data.questions[i].type != "text"){
                        this._appendAddAnswerButton(data.questions[i].type, answerContainer, question, radioGroup);
                    }
                    this._objects["questions"].push(question);
                }
            }
         },
        _onComplete : function(e)
        {
            this.showMessage("Zmiany zapisane!");
            this.close();
            this.fireEvent("completed");
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
        _onRemoveBtnClick : function(question, groupBox)
        {
            var that = this;
            return function (e)
            {
                that.__delQuestion(question.hash);
                groupBox.removeAll();
                this._composite.remove(groupBox);

            }
        },
        __delQuestion : function(hash){
            var questions = this._objects["questions"];
            for (var i = 0; i < questions.length; ++i){
                if (questions[i].title.$$hash === hash){
                    this._manager.remove(questions[i].title);
                    qx.lang.Array.removeAt(questions, i);
                }
            }
        },
        _appendAddAnswerButton : function(questionType, answerContainer, question, radioGroup){

                var addMoreAnswersButton = new qx.ui.form.Button(null, "icon/16/actions/list-add.png").set({
                    maxWidth:40
                });

                addMoreAnswersButton.addListener("execute", function(e)
                {
                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                    var answerType = null;
                    if (this._type == "test"){
                        answerType = (questionType == "checkboxes") ? new qx.ui.form.CheckBox : new qx.ui.form.RadioButton ;
                        horizontalContainer.add(answerType);
                        if (questionType != "checkboxes"){
                            radioGroup.add(answerType);
                        }
                    }

                    var answer = new qx.ui.form.TextField().set({
                        placeholder: Tools.tr("survey.create:answer"),
                        marginTop:5
                    });

                    question["possible_answers"].push({"object" :answer, "value_object": answerType });

                    horizontalContainer.add(answer, {flex:1});

                    var deleteButton = new qx.ui.basic.Image("icon/16/actions/list-remove.png").set({"marginLeft":10,"marginTop":5});
                    deleteButton.addListener("click", function()
                    {
                        this._delete(answer.$$hash);
                        horizontalContainer.removeAll();
                    }, this);

                    horizontalContainer.add(deleteButton);

                    answerContainer.addBefore(horizontalContainer,addMoreAnswersButton);
                    this._container.scrollChildIntoView(answerContainer);
                },this);
                answerContainer.add(addMoreAnswersButton);
        },
        _appendAnswer : function(questionType, question, answerContainer, val, id, radioGroup, correct, index){
            switch (questionType){
                case "text":

                    var answer = new qx.ui.form.TextField().set({
                        enabled: false,
                        value: Tools.tr("survey.create:user_answer")
                    });
                    answer.setRequired(true);

                    question["possible_answers"].push({"object": answer, "value":""});
                    break;

                case "multichoice":
                case "checkboxes":
                case "list":

                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                    var answerType = null;
                    if (this._type == "test"){
                        answerType =  (questionType == "checkboxes") ? new qx.ui.form.CheckBox : new qx.ui.form.RadioButton ;
                        answerType.setValue(correct==1);
                        horizontalContainer.add(answerType);

                        if (questionType != "checkboxes"){
                            radioGroup.add(answerType);
                        }
                    }

                    var answer = new qx.ui.form.TextField().set({
                        value: val,
                        placeholder: Tools.tr("survey.create:answer")
                    });
                    answer.setRequired(true);
                    this._manager.add(answer);

                    question["possible_answers"].push({
                        "object": answer,
                        "value_object": answerType,
                        "id" : id
                    });

                    horizontalContainer.add(answer, {flex:1});

                    answerContainer.add(horizontalContainer);
                    this._container.scrollChildIntoView(answerContainer);

                    if (index != 0){
                        var removeAnswerButton = new qx.ui.basic.Image("icon/16/actions/list-remove.png").set({"marginLeft":10,"marginTop":5});
                        removeAnswerButton.addListener("click", function()
                        {
                            this._delete(answer.$$hash);

                            horizontalContainer.removeAll();
                            answerContainer.remove(horizontalContainer);
                        }, this);
                        horizontalContainer.add(removeAnswerButton);
                    }
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
            for (var i = 0; i < values.length; i++){

                if (values[i] !== undefined && values[i] !== null)
                {
                    if (values[i].object !== undefined){
                        if (values[i].object.$$hash === hash){
                            this._manager.remove(values[i].object);
                            var removed = qx.lang.Array.removeAt(values, i);
                            return;
                        }
                    }
                    if (values[i].hasOwnProperty('hash')){
                        if (values[i].hash === hash){
                            this._manager.remove(values[i]);
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

                for (var i = 0; i < question.possible_answers.length; i++){
                    if (question.possible_answers[i] !== null){
                        that._delete(question.possible_answers[i].object.$$hash);
                    }
                    question.possible_answers[i] = null;
                }

                previewContainer.removeAll();
                var radioGroup = new qx.ui.form.RadioGroup();


                that._appendAnswer(questionType, question, previewContainer, "", null, radioGroup,false, 0);
                if (questionType != "text"){
                    that._appendAddAnswerButton(questionType, previewContainer, question, radioGroup);
                }
            }
        },
        __save : function(e)
        {

            if (!this._manager.validate()){
                return false;
            }

            this._values = {};
            this._values["questions"] = [];
            this._values["survey"] = {};
            this._values["survey"].id          = this._id;
            this._values["survey"].name        = this._objects.survey.name.getValue();
            this._values["survey"].description = this._objects.survey.description.getValue();
            this._values["survey"].type        = this._type;

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
                    var content = null, correct = null;

                    if (this._type == "test"){
                        if (typeof(this._objects.questions[i].possible_answers[j].value_object)!="undefined"){
                            correct = this._objects.questions[i].possible_answers[j].value_object.getValue();
                        }
                    }
                    if ((this._objects.questions[i].possible_answers[j]) != null){

                        if (typeof(this._objects.questions[i].possible_answers[j].object)!="undefined"){
                            content = this._objects.questions[i].possible_answers[j].object.getValue();
                        }
                    }
                    this._values.questions[i].possible_answers.push({
                            "content" : content,
                            "correct" : correct
                    });
                }
            }

            var req  = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY"), "PUT");
            req.setRequestData({
                data : qx.util.Serializer.toJson(this._values)
            });

            req.addListener("success", this._onComplete, this);

            req.send();
        },
        _onAddQuestionButtonClick : function(){
            var previewContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(5));

            var form = frontend.lib.ui.form.Form.create(this._questionTemplate, "survey.addquestion");

            var type = form.getItem("type");
            type.removeListener("mousewheel", type._onMouseWheel, type);


            var question = {
                "title"     : form.getItem("title"),
                "help"      : form.getItem("help"),
                "required"  : form.getItem("required"),
                "type"      : form.getItem("type"),
                "possible_answers" : []
            };

            this._manager.add(form.getItem("title"));

            this._objects["questions"].push(question);
            form.getItem("type").addListener("changeSelection", this._changeSelection(previewContainer, question));

            var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
            var removeButton = new qx.ui.basic.Image("icon/16/actions/edit-delete.png").set({marginLeft:15});
            questionContainer.add(form, {flex:1});
            questionContainer.add(removeButton);

            var groupBox = new qx.ui.groupbox.GroupBox(Tools.tr("survey.create:question"));
                groupBox.setLayout(new qx.ui.layout.VBox());
                groupBox.add(questionContainer);
                groupBox.add(previewContainer);


            removeButton.addListener("execute", this._onRemoveBtnClick(question, groupBox));
            this._composite.add(groupBox);
            this._container.scrollChildIntoView(groupBox);
        }
    }
});