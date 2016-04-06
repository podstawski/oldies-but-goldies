qx.Class.define("frontend.app.form.Quiz",
{
    extend : frontend.lib.form.Abstract,

    members :
    {
        _url        : Urls.resolve("QUIZZES"),
        _prefix     : "form.quiz",
        _template   :
        {
            name :
            {
                type : "TextField",
                properties : {
                    required : true,
                    maxLength : 64
                },
                validators : [
                    Validate.string(),
                    Validate.slength(4, 64)
                ]
            },
            description :
            {
                type : "TextArea",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string()
                ]
            },
            time_limit :
            {
                type : "Spinner",
                properties : {
                    required : true,
                    maximum : 86400
                }
            },
            url :
            {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.url()
                ]
            }
        }
    }
});