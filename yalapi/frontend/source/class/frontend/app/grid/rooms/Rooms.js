qx.Class.define("frontend.app.grid.rooms.Rooms",
{
    extend : frontend.lib.grid.Abstract,

    construct : function()
    {
        this.base(arguments);
        this.initialize();
    },

    properties :
    {
        rowId :
        {
            check : "PositiveInteger",
            init : null,
            nullable : true,
            event : "changeRowId"
        }
    },

    members :
    {
        initialize : function()
        {
            this._lastId = 0;
        },

        _TCId                       : null,
        _lastId                     : null,
        _tableModel                 : null,
        
        _tableModelUrl              : "ROOMS",
        _tableKeys                  : [ "name", "symbol", "available_space", "description"  ],
        _tableColumnNames           : [ "Nazwa", "Symbol", "Liczba miejsc", 'Opis' ],

        addFormClass     : frontend.app.form.Room,
        editFormClass    : frontend.app.form.Room,

        processDataObj : function(dataObj)
        {
            delete dataObj[0].extra_buttons;
            delete dataObj[0].selectedRow;
            delete dataObj[0].selected_row;

            return dataObj;
        }
    }
});