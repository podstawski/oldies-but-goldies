qx.Class.define("frontend.lib.ui.container.Stack",
{
    extend : qx.ui.container.Stack,

    properties :
    {
        rotationEnabled :
        {
            check : "Boolean",
            nullable : false,
            init : true
        }
    },
    
    members :
    {
        previous : function()
        {
            var selected = this.getSelection()[0];
            var selectedIndex = this.indexOf(selected);
            var children = this.getChildren();
            var prev;
            if (selectedIndex == 0) {
                if (this.getRotationEnabled()) {
                    prev = children[children.length - 1];
                } else {
                    prev = selected;
                }
            } else {
                prev = children[selectedIndex - 1];
            }

            this.setSelection([prev]);
        },

        next : function()
        {
            var selected = this.getSelection()[0];
            var selectedIndex = this.indexOf(selected);
            var children = this.getChildren();
            var next;
            if (selectedIndex == children.length - 1) {
                if (this.getRotationEnabled()) {
                    next = children[0];
                } else {
                    next = selected;
                }
            } else {
                next = children[selectedIndex + 1];
            }

            this.setSelection([next]);
        },
        isNext : function()
        {
            var selected = this.getSelection()[0];
            return this.indexOf(selected) < this.getChildren().length - 1;
        },
        getSelectedIndex : function()
        {
            var selected = this.getSelection()[0];
            return this.indexOf(selected);
        }
    }
})