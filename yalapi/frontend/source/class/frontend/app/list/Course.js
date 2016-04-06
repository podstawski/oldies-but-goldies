qx.Class.define("frontend.app.list.Course",
{
    extend : frontend.lib.list.Abstract,

    construct : function()
    {
        this.base(arguments);

        var toolbar = this.getChildControl("toolbar");
        toolbar.getChildControl("filter")
               .setSource("Projects")
               .setLabel("Projekt");

        if (!Acl.hasRight("courses.C")) {
            toolbar.getChildControl("add-button").exclude();
        }
        if (!Acl.hasRight("courses.D")) {
            toolbar.getChildControl("delete-selected-button").exclude();
        }

        this.addTab("Bieżące",    this._createTable(1));
        this.addTab("Planowane",  this._createTable(2));
        this.addTab("Archiwalne", this._createTable(3));
    },

    members:
    {
        _createTable: function(status)
        {
            var table, tableModel;

            tableModel = new frontend.lib.ui.table.model.Remote().set({
                dataUrl : Urls.resolve("COURSES"),
                filterKey : "project_id"
            });
            tableModel.setColumns(
                ["Nazwa", "Kod", "Cena", "Projekt", "Ośrodek", "Data rozpoczęcia", "Data zakończenia"],
                ["name", "code", "price", "project_name", "training_center_name", "start_date", "end_date"]
            );
            tableModel.setParam("courses.status", status);
            tableModel.setDataFormatter("training_center_full_adress", function(rowData){
                return qx.lang.String.format("%1, %2, %3", [rowData.training_center_name, rowData.street, rowData.city]);
            }, this);
            tableModel.setDataFormatter("project_name_and_code", function(rowData){
                return qx.lang.String.format("%1 (%2)", [rowData.project_name, rowData.project_code]);
            }, this);
            tableModel.setDataFormatter("price_currency", function(rowData){
                return frontend.lib.util.format.Currency.format(rowData.price);
            }, this);

            var groupManager = new frontend.app.module.group.Manager();
            table = new frontend.lib.ui.table.List().set({
                renderer : function(rowData)
                {
                    this.setMaxButtonsInRow(5);
                    
                    if (rowData.color) {
                        var title = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
                        title.add(new frontend.lib.ui.form.ColorPicker().set({
                            value : rowData.color,
                            enabled : false
                        }));
                        title.add(new qx.ui.basic.Label(rowData.name));
                        this.addTitle(title);
                    } else {
                        this.addTitle(rowData.name);
                    }

                    if (rowData.start_date && rowData.end_date) {
                        this.addLeft(rowData.start_date + " - " + rowData.end_date, "Termin");
                    }
                    this.addLeft(rowData.training_center_full_adress, "Ośrodek");
                    
                    if (rowData.group_name) {
                        var group = new frontend.lib.ui.basic.Link(rowData.group_name);
                        group.addListener("click", function(e){
                            groupManager.setGroupData({
                                id : rowData.group_id,
                                name : rowData.group_name,
                                advance_level : rowData.level
                            });
                            groupManager.open();
                        }, this);
                        this.addLeft(group, "Grupa szkoleniowa");
                    } else {
                        this.addLeft("Nie przypisano", "Grupa szkoleniowa");
                    }

                    this.addRight(rowData.project_name_and_code, "Projekt");
                    this.addRight(rowData.price_currency, "Cena");
                    
//                    this.addLeft(rowData.project_name_and_code, 'Projekt');
//                    this.addLeft(rowData.code, "Kod");
//                    this.addLeft(rowData.training_center_full_adress, 'Ośrodek');
//                    this.addLeft(rowData.group_name || 'Nie przypisano', 'Grupa szkoleniowa');
//
//                    this.addRight(rowData.start_date || 'Nie ustawiono', "Początek szkolenia");
//                    this.addRight(rowData.end_date || 'Nie ustawiono', "Koniec szkolenia");
//                    this.addRight(rowData.price_currency, "Cena");

                    if (Acl.hasRight("courses.U")) {
                        this.addButton("edit");
                    }
                    if (Acl.hasRight("courses.D")) {
                        this.addButton("delete");
                    }
                    if (Acl.hasRight("lessons.U")) {
                        this.addButton("calendar");
                    }
                    if (rowData.group_name) {
                        this.addButton("generateReport");
                    }

                    this.addButton("googleCalendar");
                },
                rowHeight       : 120,
                tableModel      : tableModel,
                showCheckboxes  : Acl.hasRight("courses.D"),
                addFormClass    : "frontend.app.form.Course",
                editFormClass   : "frontend.app.form.Course"
            });

            table.addListener("googleCalendarRowClick", function(e) {
                var hash = e.getData().hash;
                var url = 'http://www.google.com/calendar/render?cid=' +
                          'http%3A%2F%2F' + document.location.hostname + '%2Findex.php%2Fcalendar%2F%3Fcourse%3D' + hash +
                          '%26domain%3D' + frontend.app.Login.getDomain();
                window.open(url);
            }, this);

            table.addListener("calendarRowClick", function(e) {
                var app = qx.core.Init.getApplication();
                app.setBreadcrumbs(null);
                app.setContent('app.module.calendar.Calendar');
                app.getInnerContent().setContext(e.getData());
                app.setMenu('app.module.calendar.Menu');
            }, this);

            table.addListener("generateReportRowClick", function(e) {
                var data = e.getData();
                frontend.app.module.report.ReportPicker.openForm({
                    group_id: data.group_id,
                    course_id: data.id,
                    project_id: data.project_id
                });
            }, this);

            return table;
        },

        _openAddEditWindow : function(rowData)
        {
            var win = new frontend.app.form.Course(rowData);
            win.addListener("completed", function(e){
                var status = win.getChildControl("form-general").getValues().status;
                var tabview = this.getChildControl("tabview");
                var tabpage = tabview.getChildren()[status - 1];
                if (tabview.isSelected(tabpage)) {
                    this.reloadData();
                } else {
                    tabview.setSelection([tabpage]);
                }
            }, this);
            win.open();
        },

        _onAddButtonClick : function(e)
        {
            this._openAddEditWindow();
        },

        _onEditRowClick : function(e)
        {
            var rowData = e.getData();
            this._openAddEditWindow(rowData);
        }
    }
});