qx.Class.define("frontend.app.source.Grades",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id : "null", label : "- brak oceny -" },
            { id : 1.00, label : "1"  },
            { id : 1.50, label : "1+" },
            { id : 1.75, label : "2-" },
            { id : 2.00, label : "2"  },
            { id : 2.50, label : "2+" },
            { id : 2.75, label : "3-" },
            { id : 3.00, label : "3"  },
            { id : 3.50, label : "3+" },
            { id : 3.75, label : "4-" },
            { id : 4.00, label : "4"  },
            { id : 4.50, label : "4+" },
            { id : 4.75, label : "5-" },
            { id : 5.00, label : "5"  },
            { id : 5.50, label : "5+" },
            { id : 5.75, label : "6-" },
            { id : 6.00, label : "6"  }
        ];

        this.setData(data);
    },

    members :
    {
        getClosestGrade : function(grade)
        {
            var closest;
            this.getData().forEach(function(dataEntry){
                if (dataEntry.id != null && dataEntry.id != "null") {
                    if (closest == null || Math.abs(dataEntry.id - grade) < Math.abs(closest - grade)) {
                        closest = dataEntry.id;
                    }
                }
            }, this);
            return this.getById(closest).label;
        }
    }
});