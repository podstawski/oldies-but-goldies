/* *********************************

#asset(qx/icon/${qx.icontheme}/22/actions/list-remove.png)
#asset(qx/icon/${qx.icontheme}/22/actions/edit-delete.png)
#asset(qx/icon/${qx.icontheme}/22/actions/list-add.png)
#asset(qx/icon/${qx.icontheme}/22/actions/document-save.png)

********************************** */

qx.Class.define("frontend.app.module.survey.Create",{
            extend : qx.ui.window.Window,

            include : frontend.MMessage,

            events :
            {
                "reload" : "qx.event.type.Event"
            },

            construct : function()
            {
                this._container =  new qx.ui.container.Scroll;
                this._composite = new qx.ui.container.Composite;
                this._composite.setLayout(new qx.ui.layout.VBox(5));

                this._container.setWidth(800);
                this._container.setHeight(600);
                this._container.add(this._composite);

                //TODO: formularz jest tworzony tutaj, a nie z form template
                var form = new qx.ui.form.Form();

                var saveButton = new qx.ui.form.Button(Tools.tr("survey.create:save"), "icon/16/actions/document-save.png");
                var name = new qx.ui.form.TextField().set({
                            placeholder : Tools.tr("survey.create:no_name"),
                            padding: 3
                });

                var description = new qx.ui.form.TextArea().set({
                            placeholder : Tools.tr("survey.create:description-placeholder"),
                            padding: 3
                });
                var type = new qx.ui.form.SelectBox();
                    type.add(new qx.ui.form.ListItem(Tools.tr("survey.create:type_survey"),null, "survey"));
                    type.add(new qx.ui.form.ListItem(Tools.tr("survey.create:type_test"),  null, "test"));

                var addButton       = new qx.ui.form.Button(Tools.tr("survey.create:add_question"), "icon/16/actions/list-add.png");
                var addContainer    = new qx.ui.container.Composite(new qx.ui.layout.Canvas);
                var saveContainer   = new qx.ui.container.Composite(new qx.ui.layout.Canvas);

                saveButton.setWidth(150);
                name.setRequired(true);

                this.base(arguments);
                this.setLayout(new qx.ui.layout.VBox(5));
                saveButton.addListener("execute", this.__save, this);

                this._manager = new qx.ui.form.validation.Manager();
                this._manager.add(name);

                //Survey name
                this._objects.survey = {};
                this._objects.survey.name = name;
                this._objects.survey.description = description;
                this._objects.survey.type = type;



                addButton.setWidth(150);
                saveButton.setWidth(150);
                saveContainer.add(saveButton);
                addContainer.add(addButton);

                this._composite.add(name);
                this._composite.add(description);
                this._composite.add(type);
                this._composite.add(saveButton);
                this._composite.add(addContainer);

                this._objects["questions"] = [];
                
                addButton.addListener("execute", this.__click, this);
                this.setWidth(800);
                this.setHeight(600);
                this.center();
                this.add(this._container);
            },
            members: {
                _objects : {},
                _values : {},
                _manager : null,
                _container : null,
                _composite : null,
                /**
                            *  Deletes input from this._values according to hash
                            * @param data
                            */
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
                                qx.lang.Array.removeAt(values, i);
                                return;
                            }
                        }
                        if (values[i] != undefined){
                            if (values[i].$$hash === hash){
                                qx.lang.Array.removeAt(values, i);
                            }else if (values[i]["possible_answers"] instanceof Array){
                                this._delete(hash, values[i]["possible_answers"]);
                            }
                        }

                    }
                },
                //Unneded if we use "hash" attribute in question array
                __delQuestion : function(hash)
                {
                    var questions = this._objects["questions"];
                    for (var i = 0; i < questions.length; ++i){
                        if (questions[i].title.$$hash === hash){
                            qx.lang.Array.removeAt(questions, i);
                        }
                    }
                },
                //Rewrite needed to send request
                __save : function(e)
                {
                    //validate
                    if (!this._manager.validate()){
                        return false;
                    }

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

                    var req  = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY"), "POST");
                    req.setRequestData({data:qx.util.Serializer.toJson(this._values)});
                    req.addListener("success", function(e) {
                        qx.core.Init.getApplication().setContent('Survey.List');
                        this.close();
                    });
                    req.send();
                },
                __click : function(e)
                {
                    var previewContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox()).set({
                                maxWidth:    800,
                                paddingLeft: 10
                    });

                    var form = frontend.lib.ui.form.Form.create("survey.addquestion");

                    var question = {
                        "title"     : form.getItem("title"),
                        "help"      : form.getItem("help"),
                        "required"  : form.getItem("required"),
                        "type"      : form.getItem("type"),
                        "possible_answers" : []
                    };

                    this._objects["questions"].push(question);

                    form.getItem("type").addListener("changeSelection", this.__changeSelection(previewContainer, question)); //!!!

                    var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                    questionContainer.setWidth(800);

                   // questionContainer.add(new qx.ui.form.renderer.Single(form).set({'minWidth':400}), {left:20, top:20});

                    var rbContainer = new qx.ui.container.Composite(new qx.ui.layout.Basic);
                    var removeButton = new qx.ui.form.Button(null, "icon/16/actions/edit-delete.png").set({
                        marginLeft: 450});
                    rbContainer.add(removeButton);


                    questionContainer.add(form);
                    questionContainer.add(rbContainer);

                    removeButton.addListener("execute", function(e)
                    {
                        this.__delQuestion(question.title.$$hash);
                        questionContainer.removeAll();
                        previewContainer.removeAll();
                        this._composite.remove(questionContainer);
                        this._composite.remove(previewContainer);
                    }, this);

                    this._composite.add(questionContainer);
                    this._composite.add(previewContainer);
                },
                __changeSelection : function(previewContainer, question)
                {
                    var that = this;
                    return function(e) {
                        var questionType = e.__data[0].$$user_model;
                        previewContainer.removeAll();

                        question["possible_answers"] = [];
                        switch (questionType){
                            case "text":
                                var answer = new qx.ui.form.TextField().set({
                                    enabled: false,
                                    value: Tools.tr("survey.create:user_answer"),
                                    width: 700
                                });

                                question["possible_answers"].push(answer);
                                previewContainer.add(answer);
                                break;
                            case "multichoice":
                            case "checkboxes":
                            case "list":
                                var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.Grid());
                                if (questionType != "list"){
                                    var answerType =  (questionType == "multichoice") ? new qx.ui.form.RadioButton : new qx.ui.form.CheckBox;
                                    horizontalContainer.add(answerType, {row:0, column:0});
                                }

                                var answer = new qx.ui.form.TextField().set({
                                                width: 700,
                                                placeholder: Tools.tr("survey.create:answer")
                                            });

                                question["possible_answers"].push({
                                    "object": answer,
                                    "value_object": answerType
                                });

                                horizontalContainer.add(
                                    answer, {row:0,column:1}
                                );

                                var lastField = new qx.ui.form.Button(Tools.tr("survey.create:add_answer"), "icon/16/actions/list-add.png").set({
                                            maxWidth:150,
                                            opacity: 0.7
                                });

                                lastField.addListener("execute", function(e)
                                {
                                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.Grid());

                                    if (questionType != "list"){
                                        var answerType = (questionType == "multichoice") ? new qx.ui.form.RadioButton : new qx.ui.form.CheckBox;
                                        horizontalContainer.add(answerType, {row:0, column:0});
                                    }
                                    var answer = new qx.ui.form.TextField().set({
                                                    width: 700,
                                                    placeholder: Tools.tr("survey.create:answer"),
                                                    marginTop:5
                                    });
                                    question["possible_answers"].push({"object" :answer, "value_object": answerType });

                                    horizontalContainer.add(answer, {row:0,column:1});
                                    var deleteButton = new qx.ui.form.Button(null, "icon/16/actions/list-remove.png").set({"marginLeft":10});

                                    deleteButton.addListener("execute", function()
                                    {
                                        //this.__del(answer.$$hash);

                                        this._delete(answer.$$hash);


                                        horizontalContainer.removeAll();
                                        previewContainer.remove(horizontalContainer);
                                    }, that);
                                    horizontalContainer.add(deleteButton,{row:0, column:2});
                                    previewContainer.addBefore(horizontalContainer,lastField);
                                },this);
                                previewContainer.add(horizontalContainer);
                                previewContainer.add(lastField);
                            break;
                        }
                    }

                }
            }
});