/* *********************************

#asset(frontend/flags/*)

********************************* */

qx.Class.define("frontend.app.Header",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "app-header"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.Canvas);
        this.setBackgroundColor("#FFF");

        this.add(this._createChildControl("logo"), { top : 0, left : 0 });
//        this.add(this._createChildControl("selectbox"), { top : 10, right : 10 });

        var info = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
        info.add(new qx.ui.basic.Label("Zalogowany:"));
        info.add(this.getChildControl("account"));

        var pseudotoolbar = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right", "separator-horizontal"));
        pseudotoolbar.add(info);
        pseudotoolbar.add(this.getChildControl("profile"));
        pseudotoolbar.add(this.getChildControl("logout-button"));

        this.add(pseudotoolbar, { bottom : 10, right : 10 });

        if (qx.core.Environment.get("qx.debug") == true) {
            var debug = new qx.ui.form.Button("konsola");
            debug.addListener("execute", function(e){
                qx.log.appender.Console.show();
            }, this);
            this.add(debug, { bottom : 10, left : 10 });
        }

        var googleapps = window.yala.googleapps || {};

        if (googleapps.enabled)
        {
            var external_links = googleapps.external_links;
            if (external_links && qx.lang.Type.isArray(external_links)) {
                var L = external_links.length;
                var button = new qx.ui.form.MenuButton;
                button.setAppearance("splitbutton/arrow");
                var menu = new qx.ui.menu.Menu;
                external_links.forEach(function(link, index){
                    var entry = new qx.ui.menu.Button(link.text);
                    entry.addListener("execute", function(e){
                        window.open(link.url);
                    }, this);
                    if (link.title) {
                        entry.setToolTipText(link.title);
                    }
                    if (link.icon) {
                        entry.setIcon("data:image/png;base64," + link.icon);
                    }
                    menu.add(entry);
                    if (index + 1 < L) {
                        menu.addSeparator();
                    }
                }, this);
                button.setMenu(menu);
                this.add(button, { top : 66, left : 300});
            }

            this.addListenerOnce("appear", this._onAppear(googleapps), this);
        }

    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;
            var login = frontend.app.Login.getInstance();
            switch (id)
            {
                case "logo":
                    control = new frontend.lib.ui.Logo;
                    break;

                case "selectbox":
                    control = new frontend.lib.ui.form.SelectBox().set({
                        source : "Languages"
                    });
                    control.setModelSelection([qx.locale.Manager.getInstance().getLocale()]);
                    control.addListener("changeSelection", this._onChangeLanguage, this);
                    break;

                case "account":
                    var userInfo = login.getUserInfo();
                    var account = new frontend.lib.ui.basic.Link(userInfo.is_google ? userInfo.email : userInfo.username);
                    account.setToolTipText("kliknij, aby edytować dane o koncie");
                    account.addListener("click", this._onAccountClick, this);
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox);

                    var label = new qx.ui.basic.Label();
                    login.bind("userInfo", label, "value", {
                        converter : function(userInfo){
                            return (userInfo ? (userInfo.first_name + " " + userInfo.last_name) :  "Anonim") + " (";
                        }
                    });

                    control.add(label);
                    control.add(account);
                    control.add(new qx.ui.basic.Label(")"));
                    break;

                case "profile":
                    var control = new frontend.lib.ui.basic.Link("profil").set({
                        toolTipText : "kliknij, aby edytować profil"
                    });
                    control.addListener("click", this._onProfileClick, this);
                    break;

                case "logout-button":
                    var control = new frontend.lib.ui.basic.Link(Tools.tr("header:logout"));
                    control.addListener("click", this._onLogoutClick, this);
                    break;
            }

            return control || this.base(arguments, id, hash);
        },

        _onLogoutClick : function()
        {
            frontend.app.Login.getInstance().logout();
        },

        _onChangeLanguage : function(e)
        {
            var language = e.getData()[0].getModel();
            qx.locale.Manager.getInstance().setLocale(language);
        },

        _onProfileClick : function(e)
        {
            frontend.app.module.user.Profile.getInstance().open();
        },

        _onAccountClick : function(e)
        {
            var login = frontend.app.Login.getInstance();
            var userInfo = login.getUserInfo();
            delete userInfo.plain_password;

            var form = new frontend.app.form.user.Edit(userInfo);
            form.getForm().addListener("completed", function(e){
                login.setUserInfo(null);
                userInfo = qx.lang.Object.merge(userInfo, form.getForm().getValues());
                login.setUserInfo(userInfo);
            }, this);
            form.center();
            form.open();
        },

        _onAppear : function(googleapps)
        {
            var roleName = frontend.app.Login.getRoleName();
            return function (e)
            {
                if (!googleapps.access_token && roleName != "user") {
                    var popup = new qx.ui.popup.Popup(new qx.ui.layout.HBox(10));
                    popup.setPadding(10);
                    popup.setOffsetBottom(10);
                    popup.setBackgroundColor("#FFFAD3");
                    popup.placeToWidget(this.getChildControl("logout-button"));
                    popup.setPosition("top-right");
                    popup.setAutoHide(false);
                    var close = new qx.ui.form.Button();
                    close.setAppearance("window/close-button");
                    close.addListener("execute", popup.hide, popup);
                    close.setAlignY("middle");
                    close.setToolTipText("zamknij");
                    var label = new qx.ui.basic.Label();
                    if (roleName == "admin") {
                        label.setValue(Tools["tr"]("no_access_token:admin", Urls.resolve("RENEW_ACCESS_TOKEN")));
                        label.setRich(true);
                    } else {
                        label.setValue(Tools["tr"]("no_access_token:info"));
                    }
                    label.setAlignY("middle");
                    label.setFont("medium");
                    label.setTextColor("invalid");
                    popup.add(label);
                    popup.add(close);
                    popup.show();
                }
            }
        }
    }
});