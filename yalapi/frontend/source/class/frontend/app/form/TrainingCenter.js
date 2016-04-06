qx.Class.define("frontend.app.form.TrainingCenter",
{
    extend : frontend.lib.form.Abstract,

    members :
    {
        _prefix:    "form.trainingcenter",
        _url:       Urls.resolve("TRAINING_CENTERS"),

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
            code : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            street : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            zip_code : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.regex("^[0-9]{2}-[0-9]{3}$", "Nieprawidłowy kod pocztowy")
                ]
            },
            city : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.slength(2, 256)
                ]
            },
            room_amount : {
                type : "Spinner",
                properties : {
                    required : true,
                    singleStep : 2,
                    maximum : Number.MAX_VALUE
                }
            },
            seats_amount : {
                type : "Spinner",
                properties : {
                    required : true,
                    singleStep : 50,
                    maximum : Number.MAX_VALUE
                }
            },
            manager : {
                type : "TextField"
            },
            url : {
                type : "TextField",
                properties: {
                    required: true
                },
                validators : [
                    Validate.url()
                ]
            }
        }
    }
});