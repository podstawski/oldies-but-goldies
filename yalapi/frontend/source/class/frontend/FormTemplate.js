
qx.Class.define("frontend.FormTemplate",
{
    statics :
    {
        TEMPLATES :
        {
            "survey.addquestion" :
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
                        "required" : true
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
            },

            "survey.create" :
            {
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
                        "required" : true
                    }
                }
            }
        }
    }
});