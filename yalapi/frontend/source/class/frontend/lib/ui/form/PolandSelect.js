qx.Class.define("frontend.lib.ui.form.PolandSelect",
{
    extend : qx.ui.container.Composite,

    implement : [
        qx.ui.form.INumberForm,
        qx.ui.form.IForm
    ],

    include : [
        qx.ui.form.MForm,
        frontend.lib.util.MGetSource
    ],

    events :
    {
        "changeValue" : "qx.event.type.Data"
    },

    properties :
    {
        value :
        {
            check : "Integer",
            init : null,
            nullable : true,
            event : "changeValue",
            apply : "_applyValue"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.VBox(10));
        this.setAppearance("poland-select");

        this.add(this.getChildControl("province"));
        this.add(this.getChildControl("district"));
        this.add(this.getChildControl("community"));

        this.addListener("changeValid", this._onChangeValid, this);
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "province":
                    control = new frontend.lib.ui.form.SelectBox().set({
                        defaultOption    : "- województwo -",
                        minWidth         : 200,
                        selectOnlyOption : true,
                        source           : []
                    });
                    var handler = function()
                    {
                        control.setSource(this._createSource(null));
                        control.addListener("changeSelection", this._onChangeSelection("district"), this);
                    }
                    var source = this.getSourceInstance("Poland");
                    if (source.getData() == null) {
                        source.addListenerOnce("changeData", handler, this);
                    } else {
                        handler();
                    }
                    break;

                case "district":
                    control = new frontend.lib.ui.form.SelectBox().set({
                        defaultOption    : "- powiat -",
                        minWidth         : 200,
                        enabled          : false,
                        selectOnlyOption : true,
                        source           : []
                    });
                    control.addListener("changeSelection", this._onChangeSelection("community"), this);
                    break;

                case "community":
                    control = new frontend.lib.ui.form.SelectBox().set({
                        defaultOption    : "- gmina -",
                        minWidth         : 200,
                        enabled          : false,
                        selectOnlyOption : true,
                        source           : []
                    });
                    control.bind("selection", this, "value", {
                        converter : function(value) {
                            var selection = value[0];
                            if (selection && selection.getModel()) {
                                return selection.getModel()
                            }
                            return null;
                        }
                    });
                    break;
            }

            return control || this.base(arguments, id);
        },

        _createSource : function(parentID)
        {
            var source = this.getSourceInstance("Poland").getByParent(parentID);
            return source;
        },

        _onChangeSelection : function(name)
        {
            var province = this.getChildControl("province");
            return function(e)
            {
                var selection = e.getData()[0];
                var selectbox = this.getChildControl(name);
                if (selection && selection.getModel()) {
                    selectbox.setEnabled(province.getEnabled());
                    selectbox.setSource(this._createSource(selection.getModel()));
                } else {
                    selectbox.setEnabled(false);
                    selectbox.setSource([]);
                }
            }
        },

        _applyValue : function(communityID, old)
        {
            if (communityID == null) {
                return;
            }

            var province  = this.getChildControl("province");
            var district  = this.getChildControl("district");
            var community = this.getChildControl("community");
            
            var Poland = this.getSourceInstance("Poland");

            if (Poland.getData() == null) {
                Poland.addListenerOnce("changeData", function(e){
                    this._applyValue(communityID, old);
                }, this);
                return;
            }

            if (district.getSelectables().length > 1) {
                return;
            }

            var communityData = Poland.getById(communityID);
            var districtData  = Poland.getById(communityData.parent_id);
            var provinceData  = Poland.getById(districtData.parent_id);
            
            province.setModelSelection([provinceData.id]);
            district.setModelSelection([districtData.id]);
            community.setModelSelection([communityData.id]);
        },

        _applyEnabled : function(value, old)
        {
            this.base(arguments, value,old);

            var province  = this.getChildControl("province");
            var district  = this.getChildControl("district");
            var community = this.getChildControl("community");

            if (value) {
                district.setEnabled(!!province.getSelection()[0].getModel());
                community.setEnabled(!!district.getSelection()[0].getModel());
            } else {
                district.setEnabled(false);
                community.setEnabled(false);
            }
        },

        _onChangeValid: function (e)
        {
            var valid = e.getData();
            var parts = ["province", "district", "community"];
            for (var i in parts) {
                var tmp = this.getChildControl(parts[i]);
                var value = !!(tmp.getSelection()[0].getModel());
                if (valid || tmp.isEnabled() == false) {
                    tmp.setValid(true);
                } else {
                    tmp.setValid(value);
                }
            }
        }
    }
});