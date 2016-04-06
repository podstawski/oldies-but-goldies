qx.Class.define("frontend.lib.ui.form.Spinner",
{
    extend : qx.ui.form.Spinner,

    members :
    {
        _countDown: function()
        {
            if (this.__pageDownMode) {
                var newValue = this.getValue() - this.getPageStep();
            } else {
                var newValue = this.getValue() - this.getSingleStep();
            }

            if (this.getWrap()) {
                if (newValue < this.getMinimum()) {
                    newValue = this.getMaximum() - this.getMinimum() - newValue;
                }
            } else if (this.getValue() == this.getMaximum()) {
                if (this.__pageDownMode) {
                    newValue = this.getMinimum() + this.getPageStep() * (Math.floor(newValue / this.getPageStep() + 1));
                } else {
                    newValue = this.getMinimum() + this.getSingleStep() * (Math.floor(newValue / this.getSingleStep() + 1));
                }
            }

            this.gotoValue(newValue);
        }
    }
});