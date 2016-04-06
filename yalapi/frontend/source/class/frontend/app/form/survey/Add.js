/* *********************************

#asset(qx/icon/${qx.icontheme}/22/actions/list-remove.png)
#asset(qx/icon/${qx.icontheme}/22/actions/edit-delete.png)
#asset(qx/icon/${qx.icontheme}/22/actions/list-add.png)
#asset(qx/icon/${qx.icontheme}/22/actions/document-save.png)

********************************** */
qx.Class.define("frontend.app.form.survey.Add",{
            extend : frontend.lib.ui.window.Modal,

            include : frontend.MMessage,

            events :
            {
                "reload" : "qx.event.type.Event"
            },

            construct : function(type)
            {
                this.base(arguments);
                this._type = type;

                this.setWidth(800);
                this.setHeight(600);

                this._container = new qx.ui.container.Scroll;
                this._composite = new qx.ui.container.Composite;

                this._composite.setLayout(new qx.ui.layout.VBox(5));
                this._container.add(this._composite);

                var form = new qx.ui.form.Form();
                var name = new qx.ui.form.TextField().set({
                            placeholder : this._type == "survey" ? Tools.tr("survey.create:no_name") : Tools.tr("test.create:no_name"),
                            padding: 3,
                            marginBottom:5
                });
                var description = new qx.ui.form.TextArea().set({
                            placeholder : this._type == "survey" ? Tools.tr("survey.create:description-placeholder") : Tools.tr("test.create:description-placeholder"),
                            padding: 3
                });

                var addButton       = new qx.ui.form.Button(Tools.tr("survey.create:add_question"), "icon/16/actions/list-add.png");
                    addButton.setWidth(120);

                this.setCaption(
                    this._type == "survey" ? Tools.tr("surveys.new") : Tools.tr("tests.new")
                );
                this.setLayout(new qx.ui.layout.VBox(5));

                this._manager = new qx.ui.form.validation.Manager();
                this._manager.add(name);
                this._manager.add(description);

                var groupBox = new qx.ui.groupbox.GroupBox();
                    groupBox.setLayout(new qx.ui.layout.VBox());
                    groupBox.add(name);
                    groupBox.add(description);

                this._objects.survey = {};
                this._objects.survey.name = name;
                this._objects.survey.description = description;
                this._objects.survey.type = type;
                this._objects["questions"] = [];

                addButton.addListener("execute", this.__click, this);
                this._composite.add(groupBox);

                name.setRequired(true);

                this.center();
                this.add(this._container, {flex:1});


                var buttonsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
                buttonsContainer.setPaddingTop(10);

                var saveButton = new qx.ui.form.Button(Tools.tr("survey.create:save"), "icon/16/actions/document-save.png");
                    saveButton.addListener("execute", this.__save, this);

                var cancelButton = new qx.ui.form.Button(Tools.tr("survey.create:cancel"), "icon/16/actions/dialog-cancel.png");
                    cancelButton.addListener("click", function(){ this.close() }, this);


                buttonsContainer.add(addButton);
                buttonsContainer.add(new qx.ui.core.Spacer(), {flex:1});
                buttonsContainer.add(cancelButton);
                buttonsContainer.add(saveButton);

                this.add(buttonsContainer);

                this.setShowClose(false);
                this.setShowMaximize(false);
                this.setShowMinimize(false);


                this.__click();
            },
            members: {
                _objects : {},
                _values : {},
                _manager : null,
                _container : null,
                _composite : null,
                _type : null,
                _delete : function(hash, val)
                {
                    var values = null;
                    if (val){
                        values = val;
                    }else{
                        values = this._objects["questions"];
                    }
                    for (var i = 0; i < values.length; ++i){
                        if (values[i].object !== undefined){
                            if (values[i].object.$$hash === hash){
                                this._manager.remove(values[i].object);
                                qx.lang.Array.removeAt(values, i);
                                return;
                            }
                        }
                        if (values[i] != undefined){
                            if (values[i].$$hash === hash){
                                this._manager.remove(values[i]);
                                qx.lang.Array.removeAt(values, i);
                            }else if (values[i]["possible_answers"] instanceof Array){
                                this._delete(hash, values[i]["possible_answers"]);
                            }
                        }

                    }
                },
                __delQuestion : function(hash)
                {
                    var questions = this._objects["questions"];
                    for (var i = 0; i < questions.length; ++i){
                        if (questions[i].title.$$hash === hash){
                            this._manager.remove(questions[i].title);
                            qx.lang.Array.removeAt(questions, i);
                        }
                    }
                },
                __save : function(e)
                {
                    //validate
                    if (!this._manager.validate()){
                        return false;
                    }

                    this._values = {};
                    this._values["questions"] = [];
                    this._values["survey"] = {};

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

                            if (typeof(this._objects.questions[i].possible_answers[j].object)!="undefined"){
                                content = this._objects.questions[i].possible_answers[j].object.getValue();
                            }

                            this._values.questions[i].possible_answers.push({
                                    "content" : content,
                                    "correct" : correct
                            });
                        }
                    }

                    var req  = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY"), "POST");
                    req.setRequestData({
                        method  : "create",
                        data    : qx.util.Serializer.toJson(this._values)
                    });

                    req.addListener("success", function(e) {
                        qx.core.Init.getApplication().setContent(this._type == "survey" ? 'app.list.Survey' : 'app.list.Test');
                        this.close();
                        this.fireEvent("completed");
                    }, this);
                    req.send();
                },
                __click : function(e)
                {
                    var groupBox = new qx.ui.groupbox.GroupBox(Tools.tr("survey.create:question") + " " + (this._objects.questions.length+1));
                        groupBox.setLayout(new qx.ui.layout.VBox());

                    var previewContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(10));

                    var template =
                    {
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
                    };

                    var form = frontend.lib.ui.form.Form.create(template, "survey.addquestion");

                    var type = form.getItem("type");
                    type.removeListener("mousewheel", type._onMouseWheel, type);

                    this._manager.add(form.getItem("title"));

                    var question = {
                        "title"     : form.getItem("title"),
                        "help"      : form.getItem("help"),
                        "required"  : form.getItem("required"),
                        "type"      : form.getItem("type"),
                        "possible_answers" : []
                    };

                    this._objects["questions"].push(question);

                    form.getItem("type").addListener("changeSelection", this.__changeSelection(previewContainer, question));

                    var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5)).set({marginTop: 5});

                    var rbContainer = new qx.ui.container.Composite(new qx.ui.layout.Basic);
                    var removeButton =  new qx.ui.basic.Image("icon/16/actions/edit-delete.png");

                    rbContainer.add(removeButton);
                    questionContainer.add(form, {flex:1});
                    questionContainer.add(rbContainer);
                    removeButton.addListener("click", this._onRemoveBtnClick(question, groupBox), this);

                    groupBox.add(questionContainer);
                    groupBox.add(previewContainer);
                    this._composite.add(groupBox);
                    this._container.scrollChildIntoView(groupBox);
                },
                _onRemoveBtnClick : function(question, groupBox)
                {
                    var that = this;
                    return function (e)
                    {
                        that.__delQuestion(question.title.$$hash);
                        groupBox.removeAll();

                        this._composite.remove(groupBox);

                    }
                },
                __changeSelection : function(previewContainer, question)
                {
                    var that = this;

                    return function(e) {
                        var questionType = e.__data[0].$$user_model;

                        for (var i = 0; i < question.possible_answers.length; ++i){
                            if (typeof(question.possible_answers[i].object) == "undefined"){
                                that._delete(question.possible_answers[i].$$hash);
                            }else{
                                that._delete(question.possible_answers[i].object.$$hash);
                            }
                        }

                        previewContainer.removeAll();
                        var radioGroup = new qx.ui.form.RadioGroup();
                        question["possible_answers"] = [];
                        switch (questionType)
                        {
                            case "text":
                                var answer = new qx.ui.form.TextField();
                                question["possible_answers"].push(answer);
                                break;

                            case "multichoice":
                            case "checkboxes":
                            case "list":
                                var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                                var answerType = null;

                                if (that._type == "test"){
                                    answerType =  (questionType == "checkboxes") ? new qx.ui.form.CheckBox : new qx.ui.form.RadioButton;
                                    horizontalContainer.add(answerType);
                                }

                                var answer = new qx.ui.form.TextField().set({
                                    placeholder: Tools.tr("survey.create:answer")
                                });

                                answer.setRequired(true);
                                that._manager.add(answer);

                                if (questionType != "checkboxes" && that._type == "test"){
                                    radioGroup.add(answerType);
                                }

                                question["possible_answers"].push({
                                    "object": answer,
                                    "value_object": answerType
                                });

                                horizontalContainer.add(answer, {flex:1});

                                var lastField = new qx.ui.form.Button(null, "icon/16/actions/list-add.png").set({
                                    maxWidth:40,
                                    opacity: 0.7
                                });

                                lastField.addListener("execute", function(e)
                                {
                                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                                    var answerType = null;
                                    if (that._type == "test"){
                                        answerType =  (questionType == "checkboxes") ? new qx.ui.form.CheckBox : new qx.ui.form.RadioButton;
                                        horizontalContainer.add(answerType);
                                    }

                                    var answer = new qx.ui.form.TextField().set({
                                        placeholder: Tools.tr("survey.create:answer"),
                                        marginTop:5
                                    });

                                    answer.setRequired(true);
                                    that._manager.add(answer);

                                    if (questionType != "checkboxes" && that._type == "test"){
                                        radioGroup.add(answerType);
                                    }

                                    question["possible_answers"].push({"object" :answer, "value_object": answerType });

                                    horizontalContainer.add(answer, {flex:1});

                                    var deleteButton = new qx.ui.basic.Image("icon/16/actions/list-remove.png").set({"marginLeft":10});
                                    deleteButton.addListener("click", function()
                                    {
                                        this._delete(answer.$$hash);
                                        horizontalContainer.removeAll();
                                        previewContainer.remove(horizontalContainer);
                                    }, that);
                                    horizontalContainer.add(deleteButton);
                                    previewContainer.addBefore(horizontalContainer, lastField);
                                }, this);

                                previewContainer.add(horizontalContainer);
                                previewContainer.add(lastField);

                                that._container.scrollChildIntoView(previewContainer);
                            break;
                        }
                    }
                }
            }
});