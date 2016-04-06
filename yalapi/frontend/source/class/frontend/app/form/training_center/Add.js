/**
 *  #asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)
 *  #asset(qx/icon/${qx.icontheme}/16/actions/list-add.png)
 *
 */

qx.Class.define("frontend.app.form.training_center.Add",
{
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    construct : function()
    {
        this.base(arguments);
        this._initialize();

        this.set({
            caption   : Tools.tr("form.training_center.add:caption"),
            layout    : new qx.ui.layout.VBox(5),
            resizable : true,
            minWidth  : Tools.dimensions(0.35, 0.35).w
        });
        
        this.add(this._createTabView());

        this._createTabViewPage("pageGeneral");
        this._createTabViewPage("pageDescription");
        this._createTabViewPage("pageResources").setLayout( new qx.ui.layout.VBox());
        this._createTabViewPage("pageRooms").add(this._createGridRooms());

        this._controls['pageGeneral'].add(this._createFormGeneral());
        this._controls['pageDescription'].add(this._createFormDescription());

        this._generalFormValidator = this._controls['formGeneral'].getFormValidator();
        this._descriptionFormValidator = this._controls['formDescription'].getFormValidator();

        this._controls['pageResources'].add(        this._createLabels()                );
        this._controls['pageResources'].add(        this._createScrollContainer()       );

        this._controls['scrollContainer'].add(      this._createResourcesContainer()    );
        this._controls['resourcesContainer'].add(   this._createAddResBtnContainer()    );

        this._createButton("AddResource");
        this._controls['AddResource'].set({ label : null, icon : 'icon/16/actions/list-add.png' });
        this._controls['addResBtnContainer'].add( this._controls['AddResource'] );

        this._controls['tabView'].add( this._controls['pageGeneral']     );
        this._controls['tabView'].add( this._controls['pageDescription'] );
        this._controls['tabView'].add( this._controls['pageResources']   );
        this._controls['tabView'].add( this._controls['pageRooms']       );

        this.add(this._createButtonsContainer(), {flex : 1});

        this._addBehaviours();
    },

    members :
    {
        _resourcesFormValidator   : null,
        _generalFormValidator     : null,
        _descriptionFormValidator : null,
        _tabIndexCounter          : null,

        _controls : [],
        
        _resources : null,

        _lastIndex : null,
        _lastNO    : null,

        getForm : function()
        {
            return this._generalFormValidator;
        },

        _initialize : function()
        {
            this._tabIndexCounter = 1;
            this._lastIndex = 0;
            this._lastNO = 1;

            this._resources = [];

            this._resourcesFormValidator = new qx.ui.form.validation.Manager();
        },

        _createButtonsContainer : function()
        {
            var container = new qx.ui.container.Composite(new qx.ui.layout.HBox(5, "right"));
            container.add(this._createButton("Cancel"));
            container.add(this._createButton("Save"));
            return container;
        },

        _createButton: function(label)
        {
            this._controls[label] = new qx.ui.form.Button(Tools['tr']("form.training_center.add.button:" + label));
            return this._controls[label];
        },

        _createTabView : function()
        {
            this._controls['tabView'] = new qx.ui.tabview.TabView();
            return this._controls['tabView'];
        },

        _createTabViewPage : function(caption)
        {
            this._controls[caption] = new qx.ui.tabview.Page(Tools['tr']("form.training_center.add:" + caption));
            this._controls[caption].setLayout(new qx.ui.layout.VBox());
            return this._controls[caption];
        },

        _createFormGeneral : function()
        {
            this._controls['formGeneral'] = frontend.lib.ui.form.Form.create(this._template, "form.training_center.add").set({
                submitAfterValidation : false
            });
            return this._controls['formGeneral'];
        },

        _createFormDescription : function()
        {
            this._controls['formDescription'] = frontend.lib.ui.form.Form.create({
                description : {
                    type : "CKEditor",
                    nolabel : true,
                    properties : {
                        
                    }
                }
            }, "form.training_center.add").set({
                submitAfterValidation : false
            });
            return this._controls['formDescription'];
        },

        _createResourcesContainer : function()
        {
            this._controls['resourcesContainer'] = new qx.ui.container.Composite( new qx.ui.layout.VBox(5) );
            return this._controls['resourcesContainer'];
        },

        _createAddResBtnContainer : function()
        {
            this._controls['addResBtnContainer'] = new qx.ui.container.Composite( new qx.ui.layout.Basic() );
            this._controls['addResBtnContainer'].set({ alignX : "left", margin : 0, marginTop: 10 });
            return this._controls['addResBtnContainer'];
        },

        _createLabels : function()
        {
            var layout = new qx.ui.layout.HBox(10),
                labels = ["NO", "name", "quantity"],
                label, i;

            var container = new qx.ui.container.Composite( layout );

            for(i = 0; i < labels.length; i++)
            {
                label = new qx.ui.basic.Label(Tools['tr']("form.training_center.add.label:" + labels[i]));
                label.set({ alignX : "left" });

                container.add(label, { width : (i == 0) ? "5%" : "45%" });
            }
            return container;
        },

        _createGridRooms : function()
        {
            this._controls['gridRooms'] = new frontend.app.grid.rooms.Add();
            this._controls['gridRooms'].getChildControl("toolbar").getChildControl("refresh-button").setEnabled(false);
            this._controls['gridRooms'].set({ maxHeight: 250, margin : 0, padding : 0 });

            return this._controls['gridRooms'];
        },
        
        _createScrollContainer : function()
        {
            this._controls['scrollContainer'] = new qx.ui.container.Scroll();
            this._controls['scrollContainer'].set({
                minWidth  : 10,
                minHeight : 10,
                marginBottom    : 10,
                contentPadding  : 10
            });

            return this._controls['scrollContainer'];
        },

        _addBehaviours : function()
        {
            this._controls['AddResource'].addListener("execute", this._addRowToPage, this);
            this._controls['pageResources'].addListener("deleteButtonClick", this._onButtonDeleteClick, this);

            this._controls['Cancel'].addListener("execute", this.close, this);
            this._controls['Save'].addListener("execute", this._onButtonSaveClick, this);

            this.addListener("appear", function(){
                var sizes = this._controls['pageGeneral'].getBounds();
                this._controls['scrollContainer'].setWidth( sizes.width );
                this._controls['scrollContainer'].setHeight( sizes.height );
            }, this);
        },

        _onFormValid : function()
        {
            if( this._generalFormValidator.getValid() == true ) { this._saveData(); }
            else
            {
                var items = this._generalFormValidator.getItems(),
                    tabView = this._controls['tabView'];
                
                for(var i = 0; i < items.length; i++)
                {
                    if(items[i].getValid() == false)
                    {
                        if(items[i].isResourceFlag == true)
                        {
                            tabView.setSelection( [this._controls['pageResources']] );
                        }
                        else { tabView.setSelection( this._controls['pageGeneral'] ); }

                        break;
                    }
                }
            }

        },

        _onButtonSaveClick : function()
        {
            var formsCount = 3;
            var formsValidated = 0;
            var $this = this;
            var tab;

            var callback = function(tabPage)
            {
                return function()
                {
                    if (this.getValid()) {
                        if (++formsValidated == formsCount) {
                            if ($this._controls['gridRooms']._tableModel.getRowCount() == 0) {
                                var addButton = $this._controls['gridRooms'].getChildControl("toolbar").getChildControl("add-button");
                                var tooltip = new qx.ui.tooltip.ToolTip("proszę dodać conajmniej jedną salę");
                                tooltip.setAppearance("tooltip-error")
                                tooltip.setOpener(addButton);
                                tooltip.syncAppearance();
                                tooltip.placeToWidget(addButton);
                                tooltip.show();
                                addButton.addListenerOnce("mouseover", tooltip.hide, tooltip);
                                addButton.addListenerOnce("disappear", tooltip.hide, tooltip);
                                $this._controls['tabView'].setSelection([ $this._controls['pageRooms'] ]);
                            } else {
                                $this._saveData();
                            }
                        }
                    } else if (tab == null) {
                        $this._controls['tabView'].setSelection([tab = tabPage]);
                    }
                }
            }

            this._generalFormValidator.addListenerOnce("complete", callback(this._controls["pageGeneral"]));
            this._generalFormValidator.validate();

            this._descriptionFormValidator.addListenerOnce("complete", callback(this._controls["pageDescription"]));
            this._descriptionFormValidator.validate();

            this._resourcesFormValidator.addListenerOnce("complete", callback(this._controls["pageResources"]));
            this._resourcesFormValidator.validate();

        },

        _onButtonDeleteClick : function(index)
        {
            return function()
            {
                for(var i in this._resources[index].resourceRow )
                {
                    this._generalFormValidator.remove( this._resources[index].resourceRow[i]);
                    this._resourcesFormValidator.remove( this._resources[index].resourceRow);
                }

                this._controls['resourcesContainer'].remove( this._resources[index] );
                delete this._resources[index];
                
                this._refreshNOLabels();
            }
        },

        _createResourceRow : function()
        {
            var deleteButton = new qx.ui.form.Button();
            var rowContainer = new qx.ui.container.Composite( new qx.ui.layout.HBox(10) );

            var name = new frontend.lib.ui.form.ComboTable();
            name.set({
                dataUrl     : Urls.resolve("RESOURCE_TYPES"),
                dataColumn  : "name",
                required    : true,
                tabIndex    : this._tabIndexCounter++
            });
            name.isResourceFlag = true;

            var quantity = new qx.ui.form.TextField();
            quantity.set({
                required    : true,
                tabIndex    : this._tabIndexCounter++
            });
            quantity.isResourceFlag = true;

            deleteButton.set({
                icon        : "icon/16/actions/edit-delete.png",
                marginTop   : 5,
                tabIndex    : this._tabIndexCounter++
            });

            deleteButton.addListener("execute", this._onButtonDeleteClick(this._lastIndex), this );

            this._resourcesFormValidator.add(name);
            this._resourcesFormValidator.add(quantity, Validate.number());

            rowContainer.resourceRow = {
                no          : new qx.ui.basic.Label(this._formatLabel(this._lastNO)),
                name        : name,
                quantity    : quantity,
                deleteBtn   : deleteButton
            };

            for( var i in rowContainer.resourceRow )
            {
                rowContainer.resourceRow[i].setMargin(0);
                rowContainer.add( rowContainer.resourceRow[i], { flex : (i == 'deleteBtn') ? 0 : 1 } );
            }

            rowContainer.resourceRow.name.set({ marginTop : 5, maxHeight : 40 });
            rowContainer.resourceRow.quantity.set({ marginTop: 7 });
            rowContainer.resourceRow.no.set({ marginTop: 7 });
            
            return rowContainer;
        },

        _saveData : function()
        {
            var data = {};
            data.training_center = qx.lang.Object.merge(
                this._controls['formGeneral'].getValues(),
                this._controls['formDescription'].getValues()
            );
            data.resources = [];

            var length = this._resources.length;
            for(var i = 0; i < length; i++)
            {
                if(typeof this._resources[i] !== "undefined")
                {
                    data.resources[i] = {
                        type : this._resources[i].resourceRow.name.getModel(),
                        quantity : this._resources[i].resourceRow.quantity.getValue()
                    };
                }
            }

            data.rooms = this._controls['gridRooms'].getTable().getTableModel().getData();

            for(var i = 0; i < data.rooms.length; i++)
            {
                delete data.rooms[i].extra_buttons;
                delete data.rooms[i].selected_row;
                delete data.rooms[i].id;
            }
            
            var request = new frontend.lib.io.HttpRequest(Urls.resolve("TRAINING_CENTERS"), "POST");
            request.setRequestData({data:qx.lang.Json.stringify(data)});
            request.addListener("success", function(e){
                new frontend.lib.dialog.Message(Tools.tr("form.training_center.add:added"));
                this.close();
                this.fireEvent("completed");
            }, this);
            request.send();
        },

        _formatLabel : function(i) {  return ((i < 10 ? ' ' : '') + i).toString(); },
        
        _refreshNOLabels : function()
        {
            var length = this._resources.length;
            var i = 0;
            this._lastNO = i + 1;

            for( i; i < length; i++ )
            {
                if( typeof(this._resources[i]) !== "undefined" )
                {
                    this._resources[i].resourceRow.no.setValue(this._formatLabel(this._lastNO));
                    this._lastNO++;
                }
            }
        },

        _addRowToPage : function()
        {
            this._resources[this._lastIndex] = this._createResourceRow();

            this._controls['resourcesContainer'].addBefore( this._resources[this._lastIndex], this._controls['addResBtnContainer'] );

            this._lastNO++;
            this._lastIndex++;
        },

        _template :
        {
            name : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ] 
            },
            code : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string()
                ]
            },
            street : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            zip_code : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.regex("^[0-9]{2}-[0-9]{3}$", "Nieprawidłowy kod pocztowy"),
                    Validate.slength(2, 256)
                ]
            },
            city : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.slength(2, 256)
                ]
            },
//            room_amount : {
//                type : "Spinner",
//                properties : {
//                    required : true,
//                    singleStep : 2,
//                    maximum : Number.MAX_VALUE
//                }
//            },
//            seats_amount : {
//                type : "Spinner",
//                properties : {
//                    required : true,
//                    singleStep : 50,
//                    maximum : Number.MAX_VALUE
//                }
//            },
            manager : {
                type : "TextField"
            },
            phone_number : {
                type : "TextField"
            },
            url : {
                type : "TextField",
                properties: {
                    required: false
                },
                validators : [
                    Validate.url()
                ]
            }
        }
    }
})