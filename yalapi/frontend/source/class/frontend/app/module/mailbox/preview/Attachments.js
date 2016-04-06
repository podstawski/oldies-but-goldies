/* *********************************

#asset(qx/icon/${qx.icontheme}/16/mimetypes/*)
#asset(qx/icon/${qx.icontheme}/16/status/image-missing.png*)

********************************** */

qx.Class.define("frontend.app.module.mailbox.preview.Attachments",
{
    extend : qx.ui.container.Composite,

    events :
    {
        "changeValue" : "qx.event.type.Data"
    },

    properties :
    {
        value :
        {
            check : "Array",
            init : null,
            nullable : true,
            event : "changeValue",
            apply : "_applyValue"
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.Flow(5, 5, "left"));

        this.bind("value", this, "visibility", {
            converter : function(value) {
                return value != null ? "visible" : "excluded";
            }
        });

        this.__nf = new qx.util.format.NumberFormat().set({
            minimumIntegerDigits  : 1,
            minimumFractionDigits : 2,
            maximumFractionDigits : 2,
            postfix : "kb"
        });
    },

    members :
    {
        _applyValue : function(attachments, old)
        {
            if (this.hasChildren()) {
                this.removeAll();
            }
            if (attachments != null && attachments.length > 0) {
                attachments.forEach(function(attachment){
                    this.add(this._createAttachment(attachment));
                }, this);
            }
        },

        __nf : null,

        __resolveIcon : function(attachment)
        {
            switch (attachment.filename.split(".").pop().toLowerCase())
            {
                case "jpg" :
                case "jpeg":
                case "png" :
                case "gif" :
                    return "icon/16/mimetypes/media-image.png";

                case "doc" :
                case "docx":
                case "pdf" :
                    return "icon/16/mimetypes/office-document.png";

                case "ppt" :
                case "pptx":
                    return "icon/16/mimetypes/office-presentation.png";

                case "txt" :
                case "rtf" :
                    return "icon/16/mimetypes/text-plain.png";

                case "xls" :
                case "xlsx":
                    return "icon/16/mimetypes/office-spreadsheet.png";

                case "zip" :
                    return "icon/16/mimetypes/archive.png";

                case "ical":
                case "ics" :
                    return "icon/16/mimetypes/office-calendar.png";

                default:
                    return "icon/16/status/image-missing.png";
            }
        },

        _createAttachment : function(attachment)
        {
            var element = new qx.ui.basic.Atom(
                qx.lang.String.format('<a href="javascript:void(null)">%1 (%2)</a>', [ attachment.filename, this.__nf.format(attachment.size / 1000) ]),
                this.__resolveIcon(attachment)
            ).set({
                rich : true
            });
            element.addListener("click", this._onClick(attachment.hash), this);
            return element;
        },

        _onClick : function(hash)
        {
            return function(e)
            {
                window.open(Urls.resolve("DOWNLOAD", { id : hash }));
            }
        }
    }
});