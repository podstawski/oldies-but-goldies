qx.Mixin.define("frontend.lib.ui.MSearchBox",
{
    construct : function()
    {
        this.__filterTimer = new qx.event.Timer(500);
    },


    members :
    {
        __searchBox : null,

        __filterTimer : null,

        connectToSearchBox : function(searchBox, clearTextField)
        {
            qx.core.Assert.assertInterface(searchBox, frontend.lib.ui.ISearchBox);

            this.disconnectFromSearchBox();

            this.__filterTimer.addListener("interval", this._onTimerInterval, this);

            if (clearTextField === true || searchBox.getClearSearchBoxOnConnect() === true) {
                searchBox.clearTextField();
            }

            var textfield = searchBox.getTextField();
            if (textfield.getLiveUpdate()) {
                textfield.addListener("input", this._restartTimer, this);
            } else {
                textfield.addListener("changeValue", this._restartTimer, this);
            }

            this.__searchBox = searchBox;
            
            return this;
        },

        disconnectFromSearchBox : function()
        {
            if (this.__searchBox) {
                var textfield = this.__searchBox.getTextField();
                if (textfield.getLiveUpdate()) {
                    textfield.removeListener("input", this._restartTimer, this);
                } else {
                    textfield.removeListener("changeValue", this._restartTimer, this);
                }
                this.__searchBox = null;
            }

            if (this.__filterTimer) {
                this.__filterTimer.stop();
                this.__filterTimer.removeListener("interval", this._onTimerInterval, this);
            }
            return this;
        },

        _onTimerInterval : function(e)
        {
            this.__filterTimer.stop();
            this._onChangeSearchValue();
        },

        getSearchValue : function()
        {
            if (this.__searchBox) {
                return this.__searchBox.getTextField().getValue();
            }
            return null;
        },

        _restartTimer : function()
        {
            this.__filterTimer.restart();
        }
    }
});