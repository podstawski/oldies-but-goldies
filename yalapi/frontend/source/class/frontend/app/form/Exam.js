/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)

********************************** */

qx.Class.define("frontend.app.form.Exam",
{
    extend : frontend.lib.form.Abstract,

    construct : function(rowData)
    {
        this.base(arguments, rowData);

        if (rowData && rowData.id) {
            var button = new frontend.lib.ui.form.Button("Usuń", "icon/16/actions/edit-delete.png").set({
                alignX : "left"
            });

            this.getForm()._buttonRow.addBefore(button, this.getForm().getItem("cancel"));
            button.addListener("execute", this._onDeleteButtonClick, this);
        }
    },

    members :
    {
        _url        : "GROUP_EXAMS",
        _prefix     : "form.exam",
        _template   :
        {
            name : {
                type : "TextField",
                properties : {
                    required : true,
                    placeholder : "sprawdzian, kartkówka"
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            created_date : {
                type : "DateField",
                properties : {
                    required : true,
                    value : new Date
                }
            }
        },

        _onDeleteButtonClick : function(e)
        {
            if (this._rowData && this._rowData.id) {
                var dialog = new frontend.lib.dialog.Confirm("Na pewno chcesz usunąć sprawdzian?");
                dialog.addListenerOnce("yes", function(e){
                    var request = new frontend.lib.io.HttpRequest().set({
                        url     : Urls.resolve(this._url, this._rowData.id),
                        method  : "DELETE"
                    });
                    request.addListenerOnce("success", function(e){
                        this.showMessage("Sprawdzian został usunięty!");
                        this.getForm().fireEvent("completed");
                        this.close();
                    }, this);
                    request.send();
                }, this);
            }
        }
    }
});