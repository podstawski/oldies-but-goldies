qx.Class.define("frontend.lib.ui.form.Form",
{
    extend : qx.ui.container.Composite,

    include : [
        frontend.MMessage
    ],

    construct : function()
    {
        this.base(arguments);

        var layout = new qx.ui.layout.Grid();
        layout.setSpacing(6);
        this.setLayout(layout);

        this._items   = {};
        this._options = {};
        this._columns = {};
        this._hidden  = {};

        var root = qx.core.Init.getApplication().getRoot();
        this.__blocker = new frontend.lib.ui.core.Blocker(root);

        this._createIframe();

        this.getContentElement().setAttribute("method", "POST").include();

        this._formValidator  = new frontend.lib.ui.form.validation.Manager();
        this._formController = new qx.data.controller.Object();
        this._formResetter   = new qx.ui.form.Resetter();

        this.addListenerOnce("appear", function(e){
            if (this._maxColumnIndex == 1) {
                layout.setColumnFlex(0, 1);
            }
        }, this);
    },

    events :
    {
        "completed" : "qx.event.type.Event",
        "changeSubmitAfterValidation" : "qx.event.type.Data",
        "changeCallback" : "qx.event.type.Data",
        "changeUrl" : "qx.event.type.Data",
        "changeMethod" : "qx.event.type.Data"
    },

    properties :
    {
        url:
        {
            check : "String",
            init : "",
            apply : "_applyUrl",
            event : "changeUrl"
        },

        method :
        {
            check : [ "POST", "PUT" ],
            init : "POST",
            nullable : false,
            apply : "_applyMethod",
            event : "changeMethod"
        },

        submitAfterValidation :
        {
            check : "Boolean",
            init : true,
            nullable : false,
            event : "changeSubmitAfterValidation",
            apply : "_applySubmitAfterValidation"
        },

        callback :
        {
            check : "Function",
            event : "changeCallback",
            apply : "_applyCallback"
        }
    },

    members :
    {
        _iframe : null,

        _formValidator : null,

        _formController : null,

        _items : null,

        _options : null,

        _buttonRow : null,

        _row : 0,

        _columns : null,

        _hidden : null,

        _maxColumnIndex : 0,

        _submitButton: null,

        _formResetter : null,

        _applyUrl : function(url, old)
        {
            this.getContentElement().setAttribute("action", url).include();
        },

        _applyMethod : function(method, old)
        {
            this._createHiddenInput(null, "_method").getDomElement().value = method;
        },

        _applySubmitAfterValidation : function(value, old)
        {

        },

        _applyCallback : function(callback, oldCallback)
        {
            if (oldCallback) {
                this.removeListener("completed", oldCallback, this);
            }

            if (callback) {
                this.addListener("completed", callback, this);
            }
        },
        
        _createIframe : function()
        {
            var iframeName = "frame_" + (new Date).valueOf();

            this._iframe = document.createElement("iframe");
            this._iframe.id = this._iframe.name = iframeName;
            this._iframe.style.display = "none";

            //RB Fix for IE's progress bar bug: http://www.rizalalmashoor.com/blog/ie-progress-bar-loading-forever-for-iframe/
            if (window.attachEvent) {
                var iframeInitialized = false,
                    that = this;

                var onload = function(e) {
                    //RB hack, because onload is triggered also on attaching iframe to DOM
                    if (iframeInitialized === false) {
                        iframeInitialized = true;
                        return;
                    }
                    that._onIframeLoad(window.event);
                    //RB stop IE's progress bar
                    window.setTimeout(function() {
                        //RB unbind function to avoid looping
                        that._iframe.detachEvent('onload', onload);
                        that._iframe.src = Urls.resolve("blank.html");
                        //RB attach it again, so it can work after failed validation
                        that._iframe.attachEvent('onload', onload);
                    }, 100);
                }
                this._iframe.attachEvent('onload', onload);
            } else {
                this._iframe.onload = qx.lang.Function.bind(this._onIframeLoad, this);
            }

            document.body.appendChild(this._iframe);
            this.getContentElement().setAttribute("target", iframeName);
        },

        _onIframeLoad : function(e)
        {
            if (this.__blocker.isBlocked()) {
                this.__blocker.unblock();
                var error;
                try {
                    error = this.getIframeJsonContent().message;
                } catch (ex) {
                    error = this.getIframeTextContent();
                }
                if (error) {
                    this.showError(error);
                } else {
                    this.fireEvent("completed");
                }
            }
        },

        _createContentElement: function()
        {
            var element = new qx.html.Element("form");
            return element;
        },

        __supportsSelection : function(formItem)
        {
            var clazz = formItem.constructor;
            return qx.Class.hasInterface(clazz, qx.ui.core.ISingleSelection);
        },

        __isComboTable  : function(formItem)
        {
            var clazz = formItem.constructor;
            return qx.Class.isSubClassOf(clazz, frontend.lib.ui.form.ComboTable);
        },

        __isFile : function(formItem)
        {
            var clazz = formItem.constructor;
            return qx.Class.hasInterface(clazz, frontend.lib.ui.form.IFile);
        },

        __supportsDate : function(formItem)
        {
            var clazz = formItem.constructor;
            return qx.Class.hasInterface(clazz, qx.ui.form.IDateForm);
        },

        __supportsBoolean : function(formItem)
        {
            var clazz = formItem.constructor;
            return qx.Class.hasInterface(clazz, qx.ui.form.IBooleanForm);
        },

        __supportsValue : function(formItem)
        {
            var clazz = formItem.constructor;
            return (
                qx.Class.hasInterface(clazz, qx.ui.form.IColorForm) ||
                qx.Class.hasInterface(clazz, qx.ui.form.INumberForm) ||
                qx.Class.hasInterface(clazz, qx.ui.form.IStringForm)
            );
        },

        _createHiddenInput : function(formItem, name)
        {
            if (this._hidden[name] === undefined)
            {
                var input;
                try {
                    input = formItem._createChildControl("hiddeninput");
                } catch (ex) {
                    input = document.createElement("input");
                    input.type = "hidden";
                }

                if (!input) {
                    return;
                }

                input.name = name;

                this._hidden[name] = new qx.html.Element();
                this._hidden[name].useElement(input);

                this.getContentElement().add(this._hidden[name]);
            }
            
            return this._hidden[name];
        },

        createHiddenInput : function(name, value)
        {
            return this._createHiddenInput(null, name).setAttribute("value", value);
        },

        _createLabel : function(label, formItem)
        {
            var clazz = label.constructor;
            var element = qx.Class.isSubClassOf(clazz, qx.ui.core.Widget) ? label : new qx.ui.basic.Label(this._createLabelText(label, formItem)).set({
                rich  : true,
                buddy : formItem
            });
            formItem.bind("visibility", element, "visibility");
            return element;
        },

        _createLabelText : function(label, formItem)
        {
            var required = " ";
            if (formItem.isRequired()) {
                required = " <span style=\"color:#FF0000;\">*</span> ";
            }

            return label + required;
        },

        _getNextRow : function()
        {
            var row = 0;
            while (this._columns[row] !== undefined) { row++; }
            return row;
        },

        __firstFocusableItem : null,

        add : function(formItem, name, label, validator, options)
        {
            options = options || {};

            if (this.__firstFocusableItem == null && formItem.isFocusable() && formItem.isReadOnly && !formItem.isReadOnly()) {
                this.__firstFocusableItem = formItem;
                this.addListenerOnce("appear", formItem.focus, formItem);
            }
            
            this._items[name]   = formItem;
            this._options[name] = options;

            var row = 0;

            if (options.row !== undefined) {
                row = options.row;
            } else {
                row = this._getNextRow();
            }
            
            var column  = options.column  || this._columns[row] || 0;
            var colSpan = options.colSpan || 1;

            if (options.nolabel === true) {
                colSpan = colSpan || 2;
            } else if (label != null) {
                this.getLayout().setColumnAlign(column, "left", "middle");
                this._add(this._createLabel(label, formItem), {row : row, column : column++});
                this.getLayout().setColumnFlex(column, 1);
            }
            
            this._add(formItem, {row : row, column : column++, colSpan : colSpan});

            this._columns[row] = column + colSpan - 1;
            this._maxColumnIndex = Math.max(this._maxColumnIndex, this._columns[row]);

            this._formValidator.add(formItem, validator, this);
            try {
                this._formResetter.add(formItem);
            } catch (ex) {

            }

            this._createHiddenInput(formItem, name);
            this._setupBindings(formItem, name);

            if (this.__isFile(formItem)) {
                this.getContentElement().setAttribute("enctype", "multipart/form-data").include();
            }
        },

        _setupBindings : function(formItem, name)
        {
            var targetProperty, bindOptions = {}, bindReverseOptions = null, value = null;

            if (this.__supportsSelection(formItem)) {
                targetProperty = "modelSelection[0]";
                value = formItem.getModelSelection()[0];
            } else if (this.__isComboTable(formItem)) {
                targetProperty = "model";
                value = formItem.getModel();
            } else if (this.__supportsDate(formItem)) {
                targetProperty = "value";
                bindOptions = {
                    converter : function(value, model, sourceObject, targetObject) {
                        if (value instanceof Date) {
                            value = targetObject.getDateFormat().format(value);
                        }
                        if (value) {
                            value = targetObject.getDateFormat().parse(value);
                        }
                        return value;
                    }
                };
                bindReverseOptions = {
                    converter : function(value, model, sourceObject, targetObject) {
                        if (value) {
                            value = sourceObject.getDateFormat().format(value);
                        }
                        return value;
                    }
                };
                value = formItem.getValue();
            } else if (this.__supportsBoolean(formItem)) {
                targetProperty = "value";
                bindOptions = {
                    converter : function(value, model, sourceObject, targetObject) {
                        return !!value;
                    }
                };
                bindReverseOptions = {
                    converter : function(value, model, sourceObject, targetObject) {
                        return !!value ? 1 : 0;
                    }
                };
                value = formItem.getValue();
            } else if (this.__supportsValue(formItem)) {
                targetProperty = "value";
                value = formItem.getValue();
            }

            this._updateFormModel(name, value);

            if (targetProperty) {
                bindOptions.onUpdate = qx.lang.Function.bind(this._onUpdate(name), this);
                this._formController.addTarget(formItem, targetProperty, name, true, bindOptions, bindReverseOptions);
            }
        },

        _onUpdate : function(name)
        {
            return function(sourceObject, targetObject, value)
            {
                if (this._hidden[name]) {
                    this._hidden[name].getDomElement().value = sourceObject.get(name);
                }
            }
        },

        addButton : function(button, options)
        {
            options = options || {};

            if (options.row != null) {
                var row    = options.row;
                var column = this._columns[row] || 0;
                
                this._add(button, {row : row, column : column, colSpan : options.colSpan ? options.colSpan : 1});
                
                this._columns[row]   = ++column;
                this._maxColumnIndex = Math.max(this._maxColumnIndex, column);
            } else {
                if (this._buttonRow == null) {
                    this._buttonRow = new qx.ui.container.Composite().set({
                        layout : new qx.ui.layout.HBox().set({
                            alignX : "right",
                            spacing : 6
                        }),
                        marginTop : 5
                    });
                    var row = options.buttonRow || this._getNextRow();
                    var statusbar = new qx.ui.basic.Label("Proszę czekać...").set({
                        alignX     : "left",
                        alignY     : "middle",
                        visibility : "hidden"
                    });
                    this.__blocker.addListener("block", statusbar.show, statusbar);
                    this.__blocker.addListener("unblock", statusbar.hide, statusbar);
                    this._add(statusbar, {row : row, column : 0});
                    this._add(this._buttonRow, {row : row, column : 1, colSpan : this._maxColumnIndex - 1});
                }
                this._buttonRow.add(button);
            }

            if (!button.getLabel()) {
                button.setLabel(
                    "button:" + button.classname.split(".").pop()
                );
            }

            if (qx.Class.isSubClassOf(button.constructor, frontend.lib.ui.form.SubmitButton)) {
                button.addListener("execute", this.send, this);
                if (this._submitButton === null) {
                    this._submitButton = button;
                }
            } else if (qx.Class.isSubClassOf(button.constructor, frontend.lib.ui.form.CancelButton)) {
                button.addListener("execute", function() {
                    this.fireEvent("canceled");
                }, this);
            }

            if (options.name != null) {
                this._items[options.name] = button;
            }
        },

        getSubmitButton: function()
        {
            return this._submitButton;
        },

        getFormValidator : function()
        {
            return this._formValidator;
        },

        __blocker : null,

        send : function()
        {
            this.__blocker.block();
            this._formValidator.addListenerOnce("complete", this._onValidationComplete, this);
            this._formValidator.validate();
        },

        _onValidationComplete : function()
        {
            var valid = this._formValidator.isValid();
            if (valid) {
                var submit = this.getSubmitAfterValidation();
                var form   = this.getContentElement().getDomElement();
                if (form && submit) {
                    form.submit();
                    return;
                }

                this.fireEvent("completed");
            }
            this.__blocker.unblock();
        },

        getItems : function()
        {
            return this._items;
        },

        getItem : function(name)
        {
            return this._items[name];
        },

        getIframe : function()
        {
            return this._iframe;
        },

        getIframeBody: function()
        {
            return qx.bom.Iframe.getBody(this._iframe);
        },

        getIframeHtmlContent: function()
        {
            var vBody = this.getIframeBody();
            return vBody ? vBody.innerHTML : null;
        },

        getIframeTextContent: function()
        {
            var vBody = this.getIframeBody();

            if (!vBody) {
                return null;
            }

            return vBody.innerHTML;

            // Mshtml returns the content inside a PRE
            // element if we use plain text
            if (vBody.firstChild && vBody.firstChild.tagName.toLowerCase() == "pre") {
                return vBody.firstChild.innerHTML;
            } else {
                return vBody.innerHTML;
            }
        },

        getIframeJsonContent: function()
        {
            var responsetext = this.getIframeTextContent();
            if (responsetext) {
                try {
                    return JSON.parse(responsetext);
                }
                catch (ex) {}
            }
            return null;
        },

        _getFormModel : function()
        {
            var formModel = this._formController.getModel();
            if (formModel) {
                formModel = JSON.parse(
                    qx.util.Serializer.toJson(
                        formModel
                    )
                );
            } else {
                formModel = {};
            }
            return formModel;
        },

        _setFormModel : function(formModel)
        {
            qx.core.Assert.assertMap(formModel);
            
            this._formController.setModel(
                qx.data.marshal.Json.createModel(
                    formModel
                )
            );
        },

        _updateFormModel : function(keyOrMap, value)
        {
            var formModel = this._getFormModel();

            if (qx.lang.Type.isObject(keyOrMap)) {
                var keys = qx.lang.Object.getKeys(keyOrMap);
                qx.lang.Object.getKeys(keyOrMap).forEach(function(key){
                    if (value !== true || (value === true && formModel[key] !== undefined)) {
                        formModel[key] = keyOrMap[key];
                    }
                }, this);
            } else {
                formModel[keyOrMap] = value !== undefined ? value : null;
            }
            this._setFormModel(formModel);

            this._formResetter.redefine();
        },

        getModel : function()
        {
            return this._getFormModel();
        },

        getValues : function()
        {
            return this._getFormModel();
        },

        populate : function(data)
        {
            qx.core.Assert.assertMap(data);

            this._formValidator.reset();
            
            qx.event.Timer.once(function(){
                this._updateFormModel(data, true);
            }, this, 100);

            return this;
        },

        reset : function()
        {
            this._formResetter.reset();
        }
    },

    statics :
    {
        getTemplate : function(templateName)
        {
            var baseTemplate = frontend.FormTemplate.TEMPLATES[templateName];
            
            if (typeof baseTemplate.extend === "string") {
                var extendedTemplate = frontend.lib.ui.form.Form.getTemplate(baseTemplate.extend);
                qx.lang.Object.mergeWith(extendedTemplate, baseTemplate, true);
                delete extendedTemplate.extend;
                frontend.FormTemplate.TEMPLATES[templateName] = extendedTemplate;
                return extendedTemplate;
            }

            return baseTemplate;
        },

        create : function(template, prefix)
        {
            var form = new frontend.lib.ui.form.Form();
            
            if (typeof template === "string") {
                if (prefix == null) {
                    prefix = template;
                }
                template = frontend.lib.ui.form.Form.getTemplate(template);
            }

            if (prefix == null) {
                prefix = "prefix.not.defined";
            }
            prefix += ":";
            
            qx.core.Assert.assertMap(template);
            
            for (var name in template)
            {
                var options = template[name];

                if (name == "extend" || !options) {
                    continue;
                }

                qx.core.Assert.assertMap(options);
                qx.core.Assert.assertKeyInMap("type", options);

                var element;

                if (typeof options.type === "function") {
                    element = new options.type();
                } else if (typeof options.type === "string") {
                    element = new frontend.lib.ui.form[options.type]();
                } else {
                    throw new Error("Invalid element type!");
                }

                if (options.properties) {
                    element.set(options.properties);
                }

                if (qx.Class.isSubClassOf(element.constructor, qx.ui.form.Button)) {
                    options.name = name;
                    form.addButton(element, options);
                } else {
                    var label = options.label || Tools["tr"](prefix + name);
                    form.add(element, name, label, options.validators || null, options);
                }
            }

            return form;
        }
    }
});