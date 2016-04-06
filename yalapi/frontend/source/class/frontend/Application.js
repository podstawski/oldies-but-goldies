/* *********************************

#asset(qx/icon/${qx.icontheme}/*)
#asset(frontend/*)
#ignore(loader)
#ignore(loader.userData)

********************************** */

qx.Class.define("frontend.Application",
{
    extend : qx.application.Standalone,

    include : [
        frontend.MMessage,
        frontend.lib.util.MGetSource
    ],

    events :
    {
        "changeBreadcrumbs" : "qx.event.type.Data"
    },

    properties :
    {
        breadcrumbs :
        {
            check : "String",
            init : null,
            nullable : true,
            event : "changeBreadcrumbs"
        },
        
        content :
        {
            check : "String",
            nullable : true,
            init : null,
            apply : "_applyContent"
        },

        menu :
        {
            check : "String",
            nullable : true,
            init : null,
            apply : "_applyMenu"
        }
    },

    members :
    {
        __noCacheClasses : ['app.module.calendar.Calendar', 'app.module.calendar.Menu'],

        __container : null,

        __content : null,

        __menu : null,

        __info : null,

        __contentCache : null,

        __domain : null,

        options : null,

        main : function()
        {
            this.base(arguments);

            var localeManager = qx.locale.Manager.getInstance();
            localeManager.addListener("changeLocale", this._onChangeLocale, this);
            localeManager.setLocale(qx.bom.Cookie.get("locale") || "pl");

            Tools.loadAdditionalTranslations();

            var root = this.getRoot();
            root.setBlockerOpacity(0.4);
            root.setBlockerColor("modal-background");

            var login = frontend.app.Login.getInstance();
            login.addListener("login",  this.__onLoginSuccess, this);
            login.addListener("error",  this.__onLoginError, this);
            login.addListener("logout", this.__onLogout, this);

            if (typeof loader != "undefined" && loader.userData) {
                login.login(loader.userData);
            } else {
                login.sayHello();
            }

            qx.ui.tooltip.Manager.getInstance().__sharedErrorToolTip = new frontend.lib.ui.tooltip.ErrorToolTip;
        },

        getInnerContent : function()
        {
            return this.__content != null && this.__content.hasChildren() 
                 ? this.__content.getChildren()[0]
                 : null;
        },

        getInnerMenu : function()
        {
            return this.__menu != null 
                 ? this.__menu.getChildren()[0]
                 : null;
        },

        __onLoginError : function()
        {
            this.showError("Nieprawidłowy login i/lub hasło!");
        },

        __onLogout : function()
        {
            this.__resetApplication();
            if (this.__domain) {
//                window.close();
            } else if (typeof loader != "undefined") {
                loader.start();
            } else {
                frontend.app.Login.getInstance().open();
            }
        },

        __onLoginSuccess : function()
        {
//            var dialog = new frontend.lib.dialog.Message("Trwa ładowanie aplikacji. Proszę czekać.");
            Acl.init();
            var timer = new qx.event.Timer(100);
            var listenerID = timer.addListener("interval", function(e){
                if (Acl.isReady()) {
                    timer.stop();
                    timer.removeListenerById(listenerID);
                    this.__initApplication();
//                    dialog.close();
                }
            }, this);
            timer.start();
        },

        __resetApplication : function()
        {
            if (this.__container != null) {
                this.getRoot().remove(this.__container);
            }

            this.resetContent();
            this.resetMenu();

            this.__container = this.__content = this.__menu = this.__contentCache = null;

            delete window.yala;
        },

        __initApplication : function(e)
        {
            this.__domain = frontend.app.Login.getDomain();

            var container = new qx.ui.container.Composite(new qx.ui.layout.VBox);
            container.add(new frontend.app.Header());

            this.__info = new qx.ui.basic.Label();
            this.__info.setRich(true);
            this.__info.exclude();
            
            this.__content = new qx.ui.container.Composite(new qx.ui.layout.VBox);
            this.__menu = new qx.ui.container.Scroll();
            this.__menu.setMinWidth(190);

//            this.bind("breadcrumbs", this.__info, "visibility", {
//                converter : function(value) {
//                    return !!value ? "visible" : "excluded";
//                }
//            });
//            this.bind("breadcrumbs", this.__info, "value");

            var content = new qx.ui.container.Composite(new qx.ui.layout.VBox(10))
            content.setPadding(10);
            content.setBackgroundColor("background-application");
            content.add(this.__info);
            content.add(this.__content, {flex:1});

            var splitPanel = new qx.ui.splitpane.Pane();
            splitPanel.add(this.__menu, 0);
            splitPanel.add(new qx.ui.container.Scroll(content), 1);

            container.add(splitPanel, {flex:1});
            this.getRoot().add(this.__container = container, {edge:0});

            this.setMenu("app.Menu");

            window.yala.$$ready = true;
        },

        _onChangeLocale : function(e)
        {
            var locale = e.getData();
            qx.bom.Cookie.set("locale", locale);
        },

        _applyContent : function(value, old)
        {
            if (old) {
                this.__content.removeAll();
            }

            if (value != null && this.__content != null) {
                var parts = value.split("#");
                var content = this.__getClass(parts[0]);
                var foo = parts[1];
                if (foo != null && typeof content[foo] == "function") {
                    if (parts.length > 2) {
                        content[foo].apply(content, parts.slice(2));
                    } else {
                        content[foo]();
                    }
                }
                content.setPadding(0);
                this.__content.add(content, {flex:1});
            }
        },

        _applyMenu : function(value, old)
        {
            if (value != null && this.__menu != null) {
                var content = this.__getClass(value);
                this.__menu.add(content, {flex:1});
            }
        },

        __getClass : function(className)
        {
            if (this.__contentCache == null) {
                this.__contentCache = {};
            }

            if (this.__noCacheClasses.indexOf(className) !== -1 || this.__contentCache[className] == null) {
                var clazz = qx.Class.getByName("frontend." + className);
                if (clazz == null) {
                    throw new Error("Class \"frontend." + className + "\" is not defined");
                }
                this.__contentCache[className] = new clazz();
                if (this.__contentCache[className].setBackgroundColor) {
                    this.__contentCache[className].setBackgroundColor("background-application");
                }
            }
            return this.__contentCache[className];
        }
    }
});