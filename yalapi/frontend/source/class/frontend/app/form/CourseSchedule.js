qx.Class.define("frontend.app.form.CourseSchedule",
{
    extend : frontend.lib.form.Abstract,
    
    members :
    {
        _url        : Urls.resolve("COURSE_SCHEDULE"),
        _prefix     : "form.course_schedule",
        _template   :
        {
            subject : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            schedule : {
                type : "TextArea",
                properties : {
                    required : true,
                    minimalLineHeight : 20,
                    minWidth : 400
                },
                validators : [
                    Validate.string()
                ]
            }
        }
    }
});