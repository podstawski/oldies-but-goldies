qx.Class.define("frontend.lib.ui.form.validation.Manager",
{
    extend : qx.ui.form.validation.Manager,

    members :
    {
        __firstInvalidItem : null,
        
        // SIM overriden
        add: function(formItem, validator, context)
        {
            // check for the form API
            if (!this.__supportsInvalid(formItem)) {
                throw new Error("Added widget not supported.");
            }
            // check for the data type
            if (this.__supportsSingleSelection(formItem)) {
                // check for a validator
                if (validator) {
                    throw new Error("Widgets supporting selection can only be validated " +
                                    "in the form validator");
                }
            }

            if (!qx.lang.Type.isArray(validator)) {
                validator = [ validator ];
            }

            if (validator.length == 0) {
                validator = [ null ];
            }

            for (var i = 0, l = validator.length; i < l; i++)
            {
                var dataEntry =
                {
                    item : formItem,
                    validator : validator[i],
                    valid : null,
                    context : context
                };
                this.__formItems.push(dataEntry);
            }
        },

        // SIM overriden
        remove : function(formItem)
        {
            var items = this.__formItems;

            for (var i = 0, len = items.length; i < len; i++) {
                if (formItem === items[i].item) {
                    items.splice(i--, 1);
                    len--;
                }
            }

            return formItem;
        },

        // SIM overriden
        validate : function()
        {
            this.__firstInvalidItem = null;

            var valid = true;
            this.__syncValid = true; // collaboration of all synchronous validations
            var items = [];

            var validatedItems = {};
            var asyncValidators = false;
            
            // check all validators for the added form items
            for (var i = 0; i < this.__formItems.length; i++)
            {
                var formItem  = this.__formItems[i].item;
                var validator = this.__formItems[i].validator;

                // store the items in case of form validation
                items.push(formItem);

                // SIM skip further validation for items that are already not valid
                if (validatedItems[formItem.toHashCode()] !== false)
                {
                    var validatorResult;
                    // check for the required property
                    validatorResult = this.__validateElementRequired(formItem);

                    // SIM if item is not valid now,
                    // there is no sense in validating it further
                    if (validatorResult)
                    {
                        // only check items with validator
                        if (validator) {
                            // check for asynchronous validation
                            if (this.__isAsyncValidator(validator)) {
                                asyncValidators = true;
                                // used to check if all async validations are done
                                this.__asyncResults[formItem.toHashCode()] = null;
                                validator.validate(formItem, formItem.getValue(), this, this.__formItems[i].context);
                            }
                            else {
                                validatorResult = validatorResult && this.__validateElement(
                                    this.__formItems[i], formItem.getValue()
                                );
                            }
                        }
                    }
                    validatedItems[formItem.toHashCode()] = validatorResult;

                    if (!validatorResult && this.__firstInvalidItem == null) {
                        this.showItemInvalidMessage(
                            this.__firstInvalidItem = formItem
                        );
                    }
                    
                    valid = validatorResult && valid;
                    this.__syncValid = validatorResult && this.__syncValid;
                }
            }

            // check the form validator (be sure to invoke it even if the form
            // items are already false, so keep the order!)
            var formValid = this.__validateForm(items);
            if (qx.lang.Type.isBoolean(formValid)) {
                this.__syncValid = formValid && this.__syncValid;
            }
            valid = formValid && valid;

            this.__setValid(valid);

            // SIM fire "complete" event if there are no async validators
            !asyncValidators && this.fireEvent("complete");
            
            return valid;
        },
        
        // SIM overriden "__validateItem"
        // validation for particular item is stopped after first error
        __validateElement : function(dataEntry, value)
        {
            var formItem  = dataEntry.item;
            var context   = dataEntry.context;
            var validator = dataEntry.validator;

            var validatorResult = null;

            try {
                validatorResult = validator.call(context || this, value, formItem);
                if (validatorResult === undefined) {
                    validatorResult = true;
                }
            }
            catch (e) {
                if (e instanceof qx.core.ValidationError) {
                    validatorResult = false;
                    if (e.message && e.message != qx.type.BaseError.DEFAULTMESSAGE) {
                        var invalidMessage = e.message;
                    } else {
                        var invalidMessage = e.getComment();
                    }
                    formItem.setInvalidMessage(invalidMessage);
                } else {
                    throw e;
                }
            }

            formItem.setValid(validatorResult);
            dataEntry.valid = validatorResult;

            return validatorResult;
        },
        
        // SIM overriden
        reset: function()
        {
            // reset all form items
            for (var i = 0; i < this.__formItems.length; i++) {
                var dataEntry = this.__formItems[i];
                // set the field to valid
                dataEntry.item.setValid(true);
                dataEntry.item.setInvalidMessage("");
                dataEntry.valid = null;
            }
            // set the manager to its inital valid value
            this.__valid = null;
            this.__asyncResults = {};
        },

        // SIM overriden "__validateRequired"
        __validateElementRequired : function(formItem)
        {
            if (formItem.getRequired())
            {
                var validatorResult;
                // if its a widget supporting the selection
                if (this.__supportsSingleSelection(formItem)) {
                    validatorResult = !!(formItem.getModelSelection()[0]);
                // SIM or is file/attachment element
                } else if (this.__supportsFile(formItem)) {
                    validatorResult = !!formItem.getFileName();
                } else if (this.__isComboTable(formItem)) {
                    validatorResult = !!formItem.getModel();
                // otherwise, a value should be supplied
                } else {
                    validatorResult = !!formItem.getValue();
                }

                var message = formItem.getRequiredInvalidMessage() || this.getRequiredFieldMessage();
                if (formItem instanceof frontend.lib.ui.form.PolandSelect) {
                    formItem.setValid(true);
                }
                formItem.setValid(validatorResult);
                formItem.setInvalidMessage(message);
                return validatorResult;
            }
            return true;
        },

        setItemValid: function(formItem, valid)
        {
            // SIM do not do it more than once
            if (this.__asyncResults[formItem.toHashCode()] === null) {
                // store the result
                this.__asyncResults[formItem.toHashCode()] = valid;
                formItem.setValid(valid);
                this.__checkValidationComplete();
            }
        },

        __isComboTable  : function(formItem)
        {
            var clazz = formItem.constructor;
            return qx.Class.isSubClassOf(clazz, frontend.lib.ui.form.ComboTable);
        },

        __supportsFile : function(formItem)
        {
            var clazz = formItem.constructor;
            return qx.Class.hasInterface(clazz, frontend.lib.ui.form.IFile);
        },

        getFirstInvalidItem : function()
        {
            return this.__firstInvalidItem;
        },

        showItemInvalidMessage : function(formItem)
        {
            qx.event.Timer.once(function(){
                if (formItem) {
                    if (!formItem.isSeeable()) {
                        formItem.addListenerOnce("appear", function(e){
                            this.showItemInvalidMessage(formItem);
                        }, this);
                        return;
                    }

                    if (formItem.isFocusable()) {
                        formItem.focus();
                    }
                    var invalidMessage = formItem.getInvalidMessage();
                    var tooltip = qx.ui.tooltip.Manager.getInstance().__sharedErrorToolTip;
                    tooltip.setLabel(invalidMessage);
                    tooltip.placeToWidget(formItem);
                    tooltip.show();
                    formItem.addListenerOnce("mouseover", tooltip.hide, tooltip);
                    formItem.addListenerOnce("disappear", tooltip.hide, tooltip);
                }
            }, this, 0);
        }
    }
});