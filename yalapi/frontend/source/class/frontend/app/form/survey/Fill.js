qx.Class.define("frontend.app.form.survey.Fill",
{
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData, type)
    {
        this.base(arguments);
        this._type = type;
        this._init();

        this.setShowClose(false);
        this.setShowMaximize(false);
        this.setShowMinimize(false);

        this.setCaption(
            rowData.name
        );

        if (rowData['description'] != null){
            var someContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(0));
                someContainer.add(new qx.ui.basic.Label(rowData['description']));

            var groupBox = new qx.ui.groupbox.GroupBox();
                groupBox.setLayout(new qx.ui.layout.VBox(0));
                groupBox.add(someContainer);

            this._composite.add(groupBox);
        }

        var request = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY", rowData.id), "GET");
        request.addListener("success", function() {
            this._afterGetData(request.getResponseJson());
        }, this);
        request.send();

        this.add(this._container);

        var saveButton = new qx.ui.form.Button(Tools.tr("survey.create:send"), "icon/16/actions/document-save.png");
            saveButton.addListener("click", this.__save, this);

        var cancelButton = new qx.ui.form.Button(Tools.tr("survey.create:cancel"), "icon/16/actions/dialog-cancel.png");
            cancelButton.addListener("click", function(){this.close()}, this);

        var buttonsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(2, "right")).set({width:200,height:30});
            buttonsContainer.add(cancelButton);
            buttonsContainer.add(saveButton);
        this.add(buttonsContainer);

        this.setWidth(650);
        this.setHeight(600);
        this.center();
    },

    members :
    {
        _form : null,
        _type : null,
        _container : null,
        _composite : null,
        _objects : [],
        _values :{},
        _data : {},
        _id : {},
        _manager : null,

        _init : function(){
            this._container =  new qx.ui.container.Scroll();

            this._composite = new qx.ui.container.Composite;
            this._composite.setLayout(new qx.ui.layout.VBox(5));
            this._container.setWidth(650);
            this._container.setHeight(540);
            this._container.add(this._composite);

            this.set({
                layout : new qx.ui.layout.VBox,
                width : 500
            });


            this._objects = [];
            this._manager = new qx.ui.form.validation.Manager();
        },

        _afterGetData : function(data) {
            this._id = data['id'];

            for (var i in data.questions){  //Add inputs for each question
                if (typeof(data.questions[i]) !== 'function'){

                    var answerContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(5)).set({
                        maxWidth:    600,
                        paddingLeft: 10
                     });



                    var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                        questionContainer.setWidth(600);

                    if (data.questions[i].help != null){
                        var qContainer =new qx.ui.container.Composite(new qx.ui.layout.VBox(5));
                        qContainer.add(new qx.ui.basic.Label( data.questions[i].help));
                        questionContainer.add(qContainer);
                    }

                    var groupBox = new qx.ui.groupbox.GroupBox(data.questions[i].title);
                        groupBox.setLayout(new qx.ui.layout.VBox());

                        groupBox.add(questionContainer);
                        groupBox.add(answerContainer);

                    this._composite.add(groupBox);


                    if (data.questions[i].possible_answers != undefined){

                            if (data.questions[i].type == 'list'){
                                var sb = new qx.ui.form.SelectBox();

                                for(var j=0; j < data.questions[i].possible_answers.length; ++j){
                                    var li = new qx.ui.form.ListItem(
                                            String(data.questions[i].possible_answers[j].content),
                                            null,
                                            data.questions[i].possible_answers[j].id
                                        );
                                    li.databaseId = data.questions[i].possible_answers[j].id;
                                    sb.add(li);

                                    answerContainer.add(sb);
                                }
                                this._objects.push({questionId:data.questions[i].id ,answerId: undefined, data:sb});
                            }else{
                                var radioGroup = new qx.ui.form.RadioGroup();
                                radioGroup.setAllowEmptySelection(true);
                                radioGroup.setRequired(true);

                                if (data.questions[i].type == "multichoice"){
                                    this._manager.add(radioGroup);
                                }


                                for(var j in data.questions[i].possible_answers){
                                    if (typeof(data.questions[i].possible_answers[j]) !== 'function') {
                                        this._appendAnswer(
                                            data.questions[i].type,
                                            {id : data.questions[i].id},
                                            answerContainer,
                                            data.questions[i].possible_answers[j].content,
                                            data.questions[i].possible_answers[j].id,
                                            radioGroup
                                        );

                                    }
                                }

                            }
                    }else{
                        this._appendAnswer(
                            data.questions[i].type,
                            { id : data.questions[i].id },
                            answerContainer,
                            null,
                            null,
                            radioGroup
                        );

                    }
                }
            }
         },

        _onComplete : function(e)
        {
            this.showMessage(this._type =="survey" ? Tools.tr("survey.filled") : Tools.tr("test.filled"));
            this.close();
        },

        _appendAnswer : function(questionType, question, answerContainer, val, id, radioGroup){
            switch (questionType){
                case "text":
                    var answer = new qx.ui.form.TextField().set({
                        width: 500,
                        paddingLeft: 10,
                        placeholder: Tools.tr("survey.create:answer"),
                        marginLeft: 10
                    });

                    answer.setRequired(true);

                    answer.databaseId = id;

                    this._objects.push({
                        questionId  : question.id,
                        answerId    : id,
                        data        : answer
                    });

                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.Grid().setColumnWidth(0, 15));
                    horizontalContainer.add(
                        answer, {
                            row     : 0,
                            column  : 1
                        }
                    );
                    answerContainer.add(horizontalContainer);

                    this._manager.add(answer);

                    break;

                case "multichoice":
                case "checkboxes":
                case "list":
                    var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.Grid().setColumnWidth(0, 15));

                    if (questionType != "list"){
                        var answerType =  (questionType == "multichoice") ? new qx.ui.form.RadioButton : new qx.ui.form.CheckBox;
                        horizontalContainer.add(answerType, {row:0, column:0});

                        if (questionType == "multichoice"){
                            radioGroup.add(answerType);
                        }
                    }

                    var answer = new qx.ui.basic.Label(val).set({
                        width: 500,
                        paddingLeft: 10,
                        marginLeft: 10
                    });

                    horizontalContainer.add(
                        answer, {
                            row     : 0,
                            column  : 1
                        }
                    );

                    answerContainer.add(horizontalContainer);

                    answerType.databaseId = id;

                    this._objects.push({questionId:question.id ,answerId: id, data:answerType});

                    //radioGroup.setRequired(true);


                break;
            }
        },

        __save : function(e)
        {

            if (!this._manager.validate()){
                return false;
            }

            var data = [];
            for (var i = 0; i < this._objects.length; ++i){

                var item = this._objects[i];
                if (typeof(item.data.getValue) == "function"){
                    if (item.data.getValue() != false){
                        data.push({answerId: item.answerId, questionId: item.questionId, value: item.data.getValue()});
                    }
                }else{
                    data.push({answerId: item.data.getSelection()[0].databaseId, questionId: item.questionId, value: true});
                }

            }

            var req  = new frontend.lib.io.HttpRequest(Urls.resolve("SURVEY"), "POST");
            req.setRequestData({method:"fill", data:qx.util.Serializer.toJson({surveyId: this._id, data:data})});

            req.addListener("success", function(e) {
                this._onComplete();
                this.fireEvent('completed');
            }, this);

            req.send();
        }

    }
});