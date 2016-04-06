qx.Class.define("frontend.app.form.coach.Add",
{
    extend : qx.ui.window.Window,

    include : [
        frontend.MMessage
    ],

    events :
    {
        
    },

    construct : function( idCourses )
    {
        this.base(arguments);


        this._url       = Urls.resolve('COACHES');
        this._caption   = Tools.tr("calendar.addcoach.addcoach");

        this.getForm();
    },

    members :
    {
        _template :
        {
            name : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            surname : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            submit : {
                type : "SubmitButton",
                properties : {
                    label : "Dodaj trenera"
                }
            }
        },

        _onComplete : function()
        {
            this.showMessage("Dodano trenera");
            this.close();
        }
    }
});