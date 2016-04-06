qx.Class.define("frontend.app.form.survey.DetailedResults",
{
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData, type)
    {
        this._type = type;

        this.base(arguments);
        this._init();

        var someContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(0));

        var request = new frontend.lib.io.HttpRequest( Urls.resolve("SURVEY", rowData.survey_id), 'GET' );

        request.setRequestData({
            method: "detailedResults",
            surveyId: rowData.survey_id,
            userId: rowData.user_id,
            groupId:rowData.group_id
        });

        request.setParser("json");
        request.addListener("success", function(e) {
            var data = e.getTarget().getResponse();

            if (data.length != 0){
                this.open();

                this.setCaption((data.info.mainType == 'survey' ? 'Ankieta: ' : 'Test: ') + data.info.survey_name);

                this._afterGetData(data.data);
            }else{
                this.showMessage(Tools.tr("surveys.user.no_results"));
            }
        }, this);

        request.send();

        this.add(this._container);

        var cancelButton = new qx.ui.form.Button(Tools.tr("survey.results:close"), "icon/16/actions/dialog-close.png");
            cancelButton.addListener("click", function(){this.close()}, this);

        var buttonsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(2, "right")).set({width:200,height:30});
            buttonsContainer.add(cancelButton);

        this.add(buttonsContainer);

        this.setWidth(650);
        this.setHeight(600);
        this.center();
    },

    members :
    {
        _form : null,

        _container : null,
        _composite : null,
        _objects : [],
        _values :{},
        _data : {},
        _id : {},
        _type : null,
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

        },

        _afterGetData : function(data) {
            this._id = data['id'];

            for (var i in data){  //Add inputs for each question
                if (typeof(data[i]) !== 'function'){
                  var answerContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(5)).set({
                        maxWidth:    600,
                        paddingLeft: 10
                     });


                    var questionContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                        questionContainer.setWidth(600);

                    var groupBox = new qx.ui.groupbox.GroupBox(data[i][0].question_name);
                        groupBox.setLayout(new qx.ui.layout.VBox());

                        groupBox.add(questionContainer);
                        groupBox.add(answerContainer);

                    this._composite.add(groupBox);

                  for (var j in data[i]){
                      if (typeof(data[i][j]) !== 'function'){
                        this._appendAnswer(
                        data[i][j].type,
                        {id : data[i][j].id},
                        answerContainer,
                        {
                            val: (data[i][j].content == null ? data[i][j].answer_content : data[i][j].content), 
                            userSelected: (data[i][j].answer_id == null ? false : true),
                            correct:(data[i][j].content == null ? undefined : data[i][j].correct)
                        },
                        data[i][j].content,
                        null
                    );
                      }
                  }
                }
            }
         },

        _appendAnswer : function(questionType, question, answerContainer, data, id, radioGroup){
                var horizontalContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(2));
                var answer = new qx.ui.basic.Label(data.val).set({
                    width: 500,
                    paddingLeft: 10,
                    marginLeft: 10
                });
                
                if (data.correct != undefined){
                    if (data.userSelected){
                        horizontalContainer.add(
                            new qx.ui.basic.Image("icon/16/apps/preferences-accessibility.png")
                        );
                    }
                    horizontalContainer.add(
                        data.correct ? new qx.ui.basic.Image("icon/16/actions/dialog-apply.png") : new qx.ui.basic.Image("icon/16/actions/edit-delete.png")
                    );
                }
                horizontalContainer.add( answer );

                answerContainer.add(horizontalContainer);
        }
    }
});