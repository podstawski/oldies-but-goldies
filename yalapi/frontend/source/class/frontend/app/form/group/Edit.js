qx.Class.define("frontend.app.form.group.Edit",
    {
        extend : frontend.app.form.group.Add,

        include : [
            frontend.MMessage
        ],

        construct : function( groupDetails ) {
            this.base(arguments);
            this._groupDetails = groupDetails;

            this.setCaption(Tools['tr']("form.group.edit:caption"));

            this._getGroupUsersModel();
            this._setGroupDetails();
        },

        members :
        {
            _groupUsers     : null,
            _groupDetails   : null,
    
            _setGroupDetails : function()
            {
                var selectables = this._controls['selectBox'].getSelectables(),
                    item = null;

                for (var i = 0; i < selectables.length; i++) {
                    if (selectables[i].getLabel() == this._groupDetails.advance_level) {
                        item = selectables[i];
                    }
                }

                this._controls['textField'].setValue(this._groupDetails.name);
                if (item) {
                    this._controls['selectBox'].setSelection([item]);
                }
            },

            _getGroupUsersModel : function()
            {
                var request = new frontend.lib.io.HttpRequest( Urls.resolve('GROUPS'), 'GET' );

                request.setRequestData({ 'group_user_id' : this._groupDetails.id });
                request.addListener("success", function() {
                    this._groupUsers = request.getResponseJson();
                    this._setGroupUsersModel();
                }, this );
                request.send();
            },

            _setGroupUsersModel : function()
            {
                this._controls['usersGroupsTable'].getTableModel().addRows(this._groupUsers);
            },

            _addGroup : function()
            {
                var groupLevel = this._controls['selectBox'].getSelection()[0].getModel();

                if( groupLevel == null)
                {
                    new frontend.lib.dialog.Message(Tools.tr("form.group.add.message:select-level"));
                }
                else
                {
                    var groupUsers = this._controls['usersGroupsTable'].getTableModel().getData(),
                        groupName = this._controls['textField'].getValue(),
                        length = groupUsers.length,
                        groupUsersId = [];

                        for( var i = 0; i < length; i++)
                        {
                            groupUsersId.push(groupUsers[i].id);
                        }

                    var data = { name: groupName, advanceLevel : groupLevel, members : groupUsersId.length, users: groupUsersId };

                    var request = new frontend.lib.io.HttpRequest( Urls.resolve('GROUPS', this._groupDetails.id), 'PUT' );
                    request.setRequestData(data);
                    request.addListener("success", function( e ) {
                        new frontend.lib.dialog.Message(Tools['tr']("form.group.edit.message:group-and-users-edited"));
                        this.close();
                        this._validationManager.fireEvent("completed");
                    }, this );
                    request.send();
                }
            }
        }
});