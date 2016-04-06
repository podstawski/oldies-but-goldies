/* *********************************

#asset(frontend/logo.jpg)

********************************* */

qx.Class.define("frontend.app.Login",
{
    type : "singleton",
    
    extend : frontend.lib.ui.window.Modal,
    
    events :
    {
        "login"  : "qx.event.type.Data",
        "error"  : "qx.event.type.Event",
        "logout" : "qx.event.type.Event",
        "changeUserInfo" : "qx.event.type.Data"
    },

    properties :
    {
        userInfo :
        {
            check : "Map",
            init : null,
            nullable : true,
            event : "changeUserInfo"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.VBox(10, null, "separator-vertical"));
        this.setCaption(Tools.tr("login.window caption"));
        this.setContentPadding(10);
        this.setShowClose(false);

        var form = this.getChildControl("form");
        this.add(this.getChildControl("logo"));
        this.add(form);

        var miscContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right"));
        miscContainer.add(this.getChildControl("google-link"));

        var bottomContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox);
        bottomContainer.add(this.getChildControl("remind-link"));
        bottomContainer.add(miscContainer, {flex:1});

        this.add(bottomContainer);

        form.getItem("username").addListener("keyup", this._onItemKeyUp, this);
        form.getItem("password").addListener("keyup", this._onItemKeyUp, this);
        this.getChildControl('remind-link').addListener('click', this._openRemindPasswordWindow, this);

        this.addListenerOnce("appear", this._onAppear, this);
//        this.sayHello();
    },

    members :
    {
        _form : null,

        _effect : null,

        _template :
        {
            username : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string()
                ]
            },
            password : {
                type : "PasswordField",
                properties: {
                    required : true
                }
            },
            submit : {
                type : "SubmitButton",
                properties : {
                    label : "Zaloguj"
                }
            }
        },

        sayHello : function()
        {
            var request = new frontend.lib.io.HttpRequest;
            request.setUrl(Urls.resolve("LOGIN") + "?hello");
            request.addListenerOnce("success", function(e){
                var userData = request.getResponseJson();
                if (this.__validateUserData(userData)) {
                    if (userData.remind_password) {
                        this.open();
                        this._showRemindPasswordTooltip(userData.username, userData.password);
                    } else {
                        this.login(userData);
                    }
                } else {
                    this.open();
                }
            }, this);
            request.send();
        },

        _showRemindPasswordTooltip : function(username, password)
        {
            this.getChildControl("form").populate({username: username, password: password});

            var atom = new qx.ui.basic.Atom(qx.lang.String.format("Twoja nazwa użytkownika to: <b>%1</b>, a hasło to: <b>%2</b>", [ username, password ]), "info");
            atom.setRich(true);
            atom.setSelectable(true);

            var popup = new qx.ui.popup.Popup(new qx.ui.layout.HBox);
            popup.setBackgroundColor("#FFFAD3");
            popup.setPadding(10);
            popup.add(atom);
            popup.show();

            qx.event.Timer.once(function(e){
                var ab = qx.core.Init.getApplication().getRoot().getBounds();
                var pb = popup.getBounds();
                var tb = this.getBounds();

                var left = (ab.width - pb.width) / 2;
                var top  = (tb.top - pb.height) - 20;
                popup.moveTo(left, top);
            }, this, 100);
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "form":
                    control = frontend.lib.ui.form.Form.create(qx.lang.Object.clone(this._template), "login").set({
                        url : Urls.resolve("LOGIN") + "?rest"
                    });
                    control.addListener("completed", this._onFormComplete, this);
                    break;

                case "logo":
                    control = new frontend.lib.ui.Logo;
                    break;

                case "remind-link":
                    control = new frontend.lib.ui.basic.Link(Tools.tr("login.remind link label"));
                    break;

                case "google-link":
                    var text = Tools.tr("logWithGoogle");
                    control = new qx.ui.form.Button(text, 'frontend/icons/social_google_box.png');
                    control.addListener("click", this._onGoogleLinkClick, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        _onAppear : function(e)
        {
            if (this._effect == null) {
                this._effect = new qx.fx.effect.combination.Shake(this.getContainerElement().getDomElement());
            }
        },

        _onItemKeyUp : function(e)
        {
            if (e.getKeyIdentifier() == "Enter") {
                this.getChildControl("form").send();
            }
        },

        __validateUserData : function(userData)
        {
            var valid = qx.lang.Type.isObject(userData) && userData.id !== undefined;
            return valid;
        },

        _onFormComplete : function()
        {
            var userData = this.getChildControl("form").getIframeJsonContent();
            if (this.__validateUserData(userData)) {
                this.login(userData);
                this.close();
            } else {
                this.error();
            }
        },

        _openRemindPasswordWindow : function()
        {
            var window = new frontend.app.PasswordReminder();
            window.open();
            window.addListener('success', function() {
                window.close();
            }, this);
        },

        _onGoogleLinkClick : function()
        {
            document.location = Urls.resolve("AUTH_OPEN_ID");
            return;

            var blocker = qx.core.Init.getApplication().getRoot().getBlocker();
            blocker.block();

            var win = window.open(Urls.resolve("AUTH_OPEN_ID") + "?mode=popup", "", "width=400,height=450,top=100,left=100,location=no,menubar=no,status=no");

            var timer = new qx.event.Timer(100);
            timer.addListener("interval", function(e){
                if (win.closed) {
                    blocker.unblock();
                    timer.stop();
                    this.sayHello();
                    this.close();
                }
            }, this);
            timer.start();
        },

        login : function(userData)
        {
            this.setUserInfo(userData);
            this.fireDataEvent("login", userData);
        },

        logout : function()
        {
            var request = new frontend.lib.io.HttpRequest;
            request.setUrl(Urls.resolve("LOGOUT"));
            request.addListener("success", function(e){
                this.setUserInfo(null);
                this.fireEvent("logout");
            }, this);
            request.send();
        },

        error : function()
        {
            if (this._effect) {
                this._effect.start();
            }
            this.fireEvent("error");
        },

        open : function()
        {
//            var options = qx.core.Init.getApplication().options;
//            if (options.googleapps.enabled) {
//                this.getChildControl("form").exclude();
//                this.getChildControl("remind-link").exclude();
//            } else {
//                this.getChildControl("form").show();
//                this.getChildControl("remind-link").show();
//            }
            this.getChildControl("form").reset();
            this.base(arguments);
        }
    },

    statics :
    {
        __self : null,

        getUserInfo : function()
        {
            if (this.__self == null) {
                this.__self = frontend.app.Login.getInstance();
            }
            return this.__self.getUserInfo();
        },

        getId : function()
        {
            return this.getUserInfo().id;
        },

        getUsername : function()
        {
            return this.getUserInfo().username;
        },

        getRole : function()
        {
            return this.getUserInfo().role;
        },

        getRoleId : function()
        {
            return this.getUserInfo().role_id;
        },

        getRoleName: function()
        {
            var obj = frontend.app.source.Roles.getInstance().getById(this.getRoleId());
            return obj.label || null;
        },

        getDomain: function()
        {
            return this.getUserInfo().domain;
        }

    }
});