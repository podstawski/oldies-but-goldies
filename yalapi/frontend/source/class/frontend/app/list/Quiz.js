    qx.Class.define("frontend.app.list.Quiz",
{
    extend : frontend.lib.list.Abstract,

    construct : function()
    {
        this.base(arguments);

        var table, tableModel;
        
        tableModel = new frontend.lib.ui.table.model.Remote().set({
            dataUrl : Urls.resolve("QUIZZES")
        });
        tableModel.setColumns(
            [Tools.tr('general:name'), Tools.tr("quiz.list.time_limit")],
            ["name", "time_limit"]
        );

        var role = frontend.app.Login.getRoleName();

        table = new frontend.lib.ui.table.List().set({
            renderer        : function(rowData)
            {
                this.addTitle(rowData.name);

                this.addLeft(rowData.description);
                this.addLeft(rowData.time_limit, Tools.tr('quiz.list.time_limit'));

                if (Acl.hasRight('quizzes.U')) {
                    this.addButton("edit");
                    this.addLeft(rowData.url, Tools.tr('quiz.list.www'));
                }

                if (Acl.hasRight('quizzes.D')) {
                    this.addButton("delete");
                }

                if (Acl.hasRight('quiz_users.C')) {
                    this.addButton('sendQuiz');
                }

                if (Acl.hasRight('quiz_scores.R')) {
                    this.addButton('quizResults')
                    //RB litle hack, to show scores only to people. who really has it
                }

                if (Acl.hasRight('quiz_scores.C')) {
                    this.addButton("runQuiz");
                }

                if (Acl.hasRight('quiz_scores.R')) {
                    this.addLeft(rowData.start_time || '', Tools.tr('quiz.list.start_time'))
                    this.addLeft(rowData.score || '', Tools.tr('quiz.list.score'))
                    this.addLeft(rowData.level || '', Tools.tr('quiz.list.level'))
                }
            },
            rowHeight       : 160,
            tableModel      : tableModel,
            addFormClass    : "frontend.app.form.Quiz",
            editFormClass   : "frontend.app.form.Quiz"
        });

        table.addListener("runQuizRowClick", function(e) {
            var url = e.getData().url + "?user_id=" + frontend.app.Login.getId() + '&quiz_id=' + e.getData().id;
            window.open(url, 'Quiz', 'menubar=no,location=no,resizable=yes,scrollbars=yes,status=no');
        }, this);

        table.addListener("sendQuizRowClick", function(e) {
            var form = new frontend.app.form.quiz.Send(e.getData(), 'survey');
            form.open();
        }, this);

        table.addListener("quizResultsRowClick", function(e) {
            this._showResultsTab(e.getData());
        }, this);

        this.addTab(Tools.tr('quiz.list.tab:all'), table);
    },

    members: {
        _addFieldIfValueSet : function(rowData, fieldName, position) {
            if (rowData[fieldName]) {
                var method = 'add' + position;
                this[method](rowData[fieldName], Tools['tr']('quiz.list.' + fieldName));
            }
        },
        _showResultsTab : function(rowData) {
            var table, tableModel, that = this;
            tableModel = new frontend.lib.ui.table.model.Remote().set({
                dataUrl : Urls.resolve("QUIZ_SCORES", {
                    quiz_id: rowData.id
                })
            });

            tableModel.setColumns(
                [Tools.tr('quiz.list.username'), Tools.tr('quiz.list.group_name'), Tools.tr('quiz.list.level'),
                 Tools.tr('quiz.list.score'), Tools.tr('quiz.list.total_time'), Tools.tr('quiz.list.start_time')],
                ['username', 'group_name', 'level', 'score', 'total_time', 'start_time']
            );
            table = new frontend.lib.ui.table.Grid().set({
                tableModel      : tableModel,
                showCheckboxes  : false,
                showEditRemove  : false
            });

            var tab = this.addTab(Tools.tr("quiz.tab:results") + ": " + rowData.name, table);
            var pages = this.getChildControl("tabview").getChildren();
            this.getChildControl("tabview").setSelection([pages[pages.length - 1]]);

        }
    }
});