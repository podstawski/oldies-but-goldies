/* *********************************

#asset(qx/icon/${qx.icontheme}/22/actions/mail-send.png)
#asset(qx/icon/${qx.icontheme}/22/actions/edit-delete.png)
#asset(qx/icon/${qx.icontheme}/16/actions/list-add.png)

********************************** */

qx.Class.define("frontend.app.module.mailbox.Compose",
{
    extend : frontend.lib.form.Abstract,

    events :
    {
        "messageSent" : "qx.event.type.Event"
    },

    construct : function()
    {
        this.base(arguments);

        var form = this.getForm();

        this.__addressBook   = null;
        this.__recipientList = {};

        var addButton = new qx.ui.basic.Image("icon/16/actions/list-add.png").set({
            toolTipText : "Kliknij, aby dodać odbiorcę",
            cursor : "pointer"
        });
        addButton.addListener("click", this._onAddRecipientClick, this);

        form._add(addButton, { row : 0, column : 2 });
        form._getLayout().setColumnAlign(2, "center", "middle");

        form.setSubmitAfterValidation(false);
        form.addListener("completed", function(e){
            qx.event.Timer.once(function(){
                form.getContentElement().getDomElement().submit();
                this.fireEvent("messageSent");
            }, this, 100);
        }, this);
    },

    members :
    {
        _url        : Urls.resolve("MESSAGES"),
        _prefix     : "form.compose",
        _template   :
        {
            recipient_list : {
                type : "TextArea",
                properties : {
                    readOnly            : true,
                    required            : true,
                    minimalLineHeight   : 1,
                    autoSize            : true,
                    enabled             : false
                },
                validators : [
                    Validate.string()
                ]
            },
            subject : {
                type : "TextField",
                colSpan : 2,
                properties : {
                    required    : true,
                    maxLength   : 64
                },
                validators : [
                    Validate.string(),
                    Validate.slength(1, 256)
                ]
            },
            attachment : {
                type : "Attachment",
                colSpan : 2
            },
            body : {
                type : "CKEditor",
                colSpan : 3,
                nolabel : true,
                properties : {
                    required : true
                }
            },
            cancel : {
                type : "CancelButton",
                buttonRow : 5,
                properties : {
                    icon    : "button-cancel",
                    label   : "Anuluj"
                }
            },
            submit : {
                type : "SubmitButton",
                properties : {
                    icon    : "mail-send",
                    label   : "Wyślij wiadomość"
                }
            }
        },

        __addressBook : null,

        __recipientList : null,

        _onAddRecipientClick : function()
        {
            if (this.__addressBook == null) {
                this.__addressBook = new frontend.app.module.mailbox.AddressBook;
                this.__addressBook.addListener("changeRecipient", this._onChangeRecipient, this);

                this.__recipientList = {};
            }
            this.__addressBook.open();
        },

        _onChangeRecipient : function(e)
        {
            var args = e.getData();
            this.changeRecipient.apply(this, args);
        },

        changeRecipient : function(category, rowData)
        {
            if (this.__recipientList[category] == null) {
                this.__recipientList[category] = {};
            }

            if (this.__recipientList[category][rowData.id] != null) {
                delete this.__recipientList[category][rowData.id];
            } else {
                this.__recipientList[category][rowData.id] = rowData.display_name || rowData.sender || rowData.username;
            }

            var recipientList = new qx.data.Array;
            var groupIds      = new qx.data.Array;
            var userIds       = new qx.data.Array;

            for (var category in this.__recipientList)
            {
                if (qx.lang.Object.getLength(this.__recipientList[category]) == 0) {
                    continue;
                }

                var recipients = qx.lang.Object.getValues(this.__recipientList[category]);
                var ids        = qx.lang.Object.getKeys(this.__recipientList[category]);

                recipientList.append(recipients);

                if (category == "groups") {
                    groupIds.append(ids);
                } else {
                    userIds.append(ids);
                }
            }
            this._form.getItem("recipient_list").setValue(recipientList.join(", "));
            this._form.createHiddenInput("groups", groupIds.toArray().toString());
            this._form.createHiddenInput("users", userIds.toArray().toString());

            return this;
        }
    }
});