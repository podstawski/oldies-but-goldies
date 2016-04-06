qx.Class.define("frontend.app.form.quiz.Send",
{
    extend : frontend.app.form.survey.Send,

    construct : function(rowData, type)
    {
        this.base(arguments, rowData, type);
        this.setCaption(Tools['tr']("form.quiz.send:caption"));
    },

    members :
    {
        _gridClass          : frontend.app.grid.Groups,
        _send : function()
        {
            var groupUsers = this._controls['gridGroups'].getTable().getSelectedRows(),
                groups = [],
                data = {};


            for(var item in groupUsers){
                if (typeof(item) !== "function"){
                    groups.push({id: groupUsers[item].id});
                }
            }

            data.groups = groups;
            data.quiz_id = this._id;

            var request = new frontend.lib.io.HttpRequest(Urls.resolve("QUIZ_USERS"), "POST");
            request.setRequestData({data:qx.util.Serializer.toJson(data)});

            request.addListener("success", function( e ) {
                this.showMessage(this._type == "survey" ? Tools.tr("survey.sent") : Tools.tr("test.send"));
                this.close();
                this._validationManager.fireEvent("completed");
            }, this );

            request.send();
        }
    }
});