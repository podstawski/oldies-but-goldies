qx.Class.define("frontend.app.module.user.Profile",
{
    type : "singleton",
    
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    events :
    {
        "completed" : "qx.event.type.Event"
    },

    construct : function()
    {
        this.base(arguments);

        this.setCaption("Ustawienia profilu użytkownika");
        this.setHeight(600);
        this.setWidth(650);

        this.__request = new frontend.lib.io.HttpRequest;
        this.__request.addListener("success", this._onRequestSuccess, this);

        this.__forms = {};

        var tabview = this.getChildControl("tabview");
        tabview.add(this.getChildControl("tab#personal"));
        tabview.add(this.getChildControl("tab#contact"));
        tabview.add(this.getChildControl("tab#work"));
        tabview.add(this.getChildControl("tab#tax"));
        tabview.add(this.getChildControl("tab#zus"));
        this.add(tabview, {flex:1});

        var buttonsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
        buttonsContainer.add(this.getChildControl("button-print"));
        buttonsContainer.add(this.getChildControl("button-cancel"));
        buttonsContainer.add(this.getChildControl("button-submit"));

        this.add(buttonsContainer);

        this.addListener("appear", this.reloadData, this);
    },

    members :
    {
        _prefix : "user.profile",

        __request : null,

        __forms : null,

        __optionalForms : [ "work", "tax", "zus" ],

        __templates :
        {
            personal : {
                first_name : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                last_name : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                sex : {
                    type : "SelectBox",
                    properties : {
                        source : [
                            { id : "M", label : "mężczyzna" },
                            { id : "F", label : "kobieta" }
                        ],
                        width : 150,
                        allowStretchX : false
                    }
                },
                national_identity : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    },
                    validators : [
                        Validate.pesel()
                    ]
                },
                birth_date : {
                    type : "DateField",
                    properties : {
                        required : true,
                        width : 100,
                        allowStretchX : false
                    }
                },
                birth_place : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                education : {
                    type : "SelectBox",
                    properties : {
                        source : "Education",
                        width : 150,
                        allowStretchX : false
                    }
                },
                care_children_up_to_seven : {
                    type : "CheckBox",
                    properties : {
                        width : 50,
                        allowStretchX : false,
                        allowStretchY : false,
                        alignY : "middle"
                    }
                },
                care_dependant_person : {
                    type : "CheckBox",
                    properties : {
                        width : 50,
                        allowStretchX : false,
                        allowStretchY : false,
                        alignY : "middle"
                    }
                },
                personal_status : {
                    type : "SelectBox",
                    properties : {
                        source : "PersonalStatus",
                        width : 150,
                        allowStretchX : false
                    }
                },
                group_headmaster : {
                    type : "CheckBox",
                    properties : {
                        label : "dyrektor/wicedyrektor",
                        alignY : "middle"
                    }
                },
                group_project_leader : {
                    type : "CheckBox",
                    properties : {
                        label : "lider szkolnego projektu",
                        alignY : "middle"
                    }
                },
                group_guardian : {
                    type : "CheckBox",
                    properties : {
                        label : "opiekun zespołu uczniowskiego",
                        alignY : "middle"
                    }
                },
                group_student : {
                    type : "CheckBox",
                    properties : {
                        label : "uczeń",
                        alignY : "middle"
                    }
                },
                group_education_staff : {
                    type : "CheckBox",
                    properties : {
                        label : "kadra oświatowa JST",
                        alignY : "middle"
                    }
                },
                teacher_of : {
                    type : "TextField",
                    properties : {
                        width : 150,
                        allowStretchX : false,
                        placeholder : "tylko nauczyciel"
                    }
                }
            },
            contact : {
                poland_id : {
                    type : "PolandSelect",
                    properties : {
                        required : true,
                        width : 200,
                        allowStretchX : false
                    }
                },
                address_city : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                address_zip_code : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    },
                    validators : [
                        Validate.zipCode()
                    ]
                },
                address_street : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                address_house_nr : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 50,
                        allowStretchX : false
                    }
                },
                address_flat_nr : {
                    type : "TextField",
                    properties : {
                        width : 50,
                        allowStretchX : false
                    }
                },
                region : {
                    type : "SelectBox",
                    properties : {
                        source : "Region",
                        width : 150,
                        allowStretchX : false
                    }
                },
                administration_region : {
                    type : "SelectBox",
                    properties : {
                        source : "AdministrationRegion",
                        width : 150,
                        allowStretchX : false,
                        allowStretchY : false
                    }
                },
                phone_number : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 100,
                        allowStretchX : false,
                        requiredInvalidMessage : "Proszę podać telefon stacjonarny lub telefon komórkowy"
                    },
                    validators : [
                        Validate.phoneNumber()
                    ]
                },
                mobile_number : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 100,
                        allowStretchX : false,
                        requiredInvalidMessage : "Proszę podać telefon stacjonarny lub telefon komórkowy"
                    },
                    validators : [
                        Validate.phoneNumber()
                    ]
                },
                fax_number : {
                    type : "TextField",
                    properties : {
                        width : 100,
                        allowStretchX : false
                    },
                    validators : [
                        Validate.phoneNumber()
                    ]
                }
            },
            work : {
                work_name : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                work_poland_id : {
                    type : "PolandSelect",
                    properties : {
                        required : true,
                        width : 200,
                        allowStretchX : false
                    }
                },
                work_city : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                work_zip_code : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    },
                    validators : [
                        Validate.zipCode()
                    ]
                },
                work_street : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                work_tax_identification_number : {
                    type : "TextField",
                    properties : {
                        width : 150,
                        allowStretchX : false
                    }
                }
            },
            tax : {
                tax_identification_number : {
                    type : "TextField",
                    properties : {
                        enabled : false,
                        width : 150,
                        allowStretchX : false
                    }
                },
                tax_office : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                tax_office_poland_id : {
                    type : "PolandSelect",
                    properties : {
                        required : true,
                        width : 200,
                        allowStretchX : false
                    }
                },
                tax_office_city : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                tax_office_zip_code : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    },
                    validators : [
                        Validate.zipCode()
                    ]
                },
                tax_office_address : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                tax_office_house_nr : {
                    type : "TextField",
                    properties : {
                        width : 50,
                        allowStretchX : false
                    }
                },
                tax_office_country : {
                    type : "TextField",
                    properties : {
                        width : 150,
                        allowStretchX : false
                    }
                },
                tax_office_post_city : {
                    type : "TextField",
                    properties : {
                        width : 150,
                        allowStretchX : false
                    }
                },
                identification_name : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                identification_number : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false,
                        toolTipText : Tools.tr("user.profile.tax:identification_number_example")
                    }
                },
                identification_publisher : {
                    type : "TextField",
                    properties : {
                        width : 150,
                        allowStretchX : false
                    }
                },
                father_name : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                mother_name : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 150,
                        allowStretchX : false
                    }
                },
                nfz : {
                    type : "TextField",
                    properties : {
                        width : 150,
                        allowStretchX : false
                    }
                },
                bank : {
                    type : "TextField",
                    properties : {
                        required : true,
                        width : 230,
                        allowStretchX : false
                    },
                    validators : [
                        Validate.nrb()
                    ]
                }
            },
            zus : {
                zus : {
                    type : "RadioGroup",
                    nolabel : true,
                    properties : {
                        source : "ZUS",
                        orientation : "vertical"
                    }
                }
            }
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "tabview":
                    control = new qx.ui.tabview.TabView();
                    break;

                case "tab":
                    control = new qx.ui.tabview.Page(Tools["tr"](this._prefix + ".tab:" + hash));
                    control.setLayout(new qx.ui.layout.VBox(10, "top", "separator-vertical"));
                    this.__forms[hash] = this._createForm(hash);
                    if (qx.lang.Array.contains(this.__optionalForms, hash)) {
                        var checkbox = this.getChildControl("checkbox#" + hash);
                        checkbox.bind("value", this.__forms[hash], "enabled", {
                            converter : function (value) {
                                return !value;
                            }
                        });
                        control.add(checkbox);
                    }
                    control.add(
                        new qx.ui.container.Scroll(
                            this.__forms[hash]
                        ), {flex:1}
                    );
                    this.__forms[hash].setUserData("tab", control);
                    break;

                case "checkbox":
                    control = new frontend.lib.ui.form.CheckBox("Nie dotyczy");
                    break;

                case "button-submit":
                    control = new qx.ui.form.Button(Tools["tr"](this._prefix + ":" + id), "button-submit");
                    control.addListener("execute", this._onButtonSaveClick, this);
                    break;

                case "button-cancel":
                    control = new qx.ui.form.Button(Tools["tr"](this._prefix + ":" + id), "button-cancel");
                    control.addListener("execute", this._onButtonCancelClick, this);
                    break;

                case "button-print":
                    control = new qx.ui.form.Button(Tools["tr"](this._prefix + ":" + id));
                    control.addListener("execute", this._onButtonPrintClick, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        _createForm : function(hash)
        {
            var template = this.__templates[hash];
            var form = frontend.lib.ui.form.Form.create(template, this._prefix + "." + hash);
            form.setUserData("tab", hash);
            form.setSubmitAfterValidation(false);
            form.getLayout().setColumnWidth(0, 130);
            return form;
        },

        getForm : function(name)
        {
            return this.__forms[name];
        },

        reloadData : function()
        {
            this.__request.setMethod("GET");
            this.__request.resetRequestData();
            this.__request.send();
        },

        populate : function(rowData)
        {
            this.getForm("personal").populate(rowData);

            this.getForm("contact").getItem("poland_id").getChildControl("province").setModelSelection([null]);
            this.getForm("contact").getItem("phone_number").setValid(true);
            this.getForm("contact").getItem("mobile_number").setValid(true);
            this.getForm("contact").populate(rowData);

            this.getForm("work").getItem("work_poland_id").getChildControl("province").setModelSelection([null]);
            this.getForm("work").populate(rowData);
            
            this.getForm("tax").getItem("tax_office_poland_id").getChildControl("province").setModelSelection([null]);
            this.getForm("tax").populate(rowData);

            this.getForm("zus").populate(rowData);

            if (rowData["disabled_forms"]) {
                rowData["disabled_forms"].split("#").forEach(function(name){
                    if (qx.lang.Array.contains(this.__optionalForms, name)) {
                        this.getChildControl("checkbox#" + name).setValue(true);
                    }
                }, this);
            }
        },

        getEnabledFormNames : function()
        {
            var formNames  = qx.lang.Object.getKeys(this.__forms);
            var formCount  = qx.lang.Object.getLength(this.__forms);

            var result = [];

            for (var i = 0; i < formCount; i++) {
                var name = formNames[i];
                if (this.__forms[name].isEnabled()) {
                    result.push(name);
                }
            }

            return result;
        },

        getValues : function()
        {
            var formNames = this.getEnabledFormNames();
            var formCount = formNames.length;
            var result = {};

            for (var i = 0; i < formCount; i++) {
                var name = formNames[i];
                result = qx.lang.Object.merge(result, this.__forms[name].getValues());
            }

            result["disabled_forms"] = qx.lang.Array.exclude(
                qx.lang.Object.getKeys(this.__forms),
                formNames
            ).join("#");

            return result;
        },

        _onRequestSuccess : function(e)
        {
            var data = this.__request.getResponseJson();
            if (data) {
                if (data.message != null) {
                    this.showError(data.message);
                } else if (data.user_id != null) {
                    this.fireEvent("completed");
                    this.populate(data);
                    if (this.__request.getMethod() == "POST") {
                        this.showMessage("Zmiany zostały zapisane!");
                        this.close();
                    }
                } else {
                    // SIM apply defaults
                    this.populate({
                        zus : 4
                    });
                }
                this.getChildControl("button-print").setEnabled(!!data.id);
            }
        },

        _onButtonSaveClick : function(e)
        {
            var validatedCount = 0;
            var tab;

            var formNames = this.getEnabledFormNames();
            var formCount = formNames.length;

            this._checkPhoneNumbers();

            for (var i = 0; i < formCount; i++)
            {
                var name      = formNames[i];
                var validator = this.__forms[name].getFormValidator();

                validator.addListenerOnce("complete", function(e){
                    if (validator.isValid()) {
                        if (++validatedCount == formCount) {
                            this._save();
                        }
                    } else if (tab == null) {
                        this.getChildControl("tabview").setSelection([
                            tab = this.getChildControl("tab#" + name)
                        ]);
                    }
                }, this);
                this.__forms[name].send();
            }
        },

        _checkPhoneNumbers : function()
        {
            var formPersonal = this.getForm("contact");
            var phoneNumber  = formPersonal.getItem("phone_number");
            var mobileNumber = formPersonal.getItem("mobile_number");

            phoneNumber.setRequired(false);
            phoneNumber.setValid(true);
            
            mobileNumber.setRequired(false);
            mobileNumber.setValid(true);
            
            if (!(phoneNumber.getValue() || mobileNumber.getValue())) {
                phoneNumber.setRequired(true);
                mobileNumber.setRequired(true);
            }
        },

        _save : function()
        {
            var data = this.getValues();
            this.__request.setMethod("PUT");
            this.__request.setRequestData(data);
            this.__request.send();
        },

        _onButtonCancelClick : function(e)
        {
            this.close();
        },

        _onButtonPrintClick : function(e)
        {
            var win = window.open(Urls.resolve("REPORTS", {
                id : 10,
                report_format : "pdf",
                user_id : this.__userID || frontend.app.Login.getId()
            }));
        },

        open : function(userID)
        {
            this.__userID = userID || frontend.app.Login.getId();
            this.__request.setUrl(Urls.resolve("USER_PROFILE", this.__userID));

            for (var i = 0, L = this.__optionalForms.length; i < L; i++) {
                var name = this.__optionalForms[i];
                this.getChildControl("checkbox#" + name).setValue(false);
            }
            this.getChildControl("tabview").setSelection([this.getChildControl("tab#personal")]);

            this.base(arguments);
        }
    }
});