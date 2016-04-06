/* *********************************

#asset(qx/icon/${qx.icontheme}/22/places/folder-open.png)
#asset(qx/icon/${qx.icontheme}/22/mimetypes/office-document.png)
#asset(qx/icon/${qx.icontheme}/22/places/folder.png)
#asset(qx/icon/${qx.icontheme}/22/apps/internet-mail.png)
#asset(qx/icon/${qx.icontheme}/22/actions/format-justify-fill.png)

********************************** */

qx.Class.define("frontend.app.Menu",
{
    extend : qx.ui.tree.Tree,

    construct : function()
    {
        this.base(arguments);

        this.__buildTree();

        this.setHideRoot(true);
        this.setRootOpenClose(true);
        this.setSelectionMode("one");
        this.setOpenMode("click");

        this.__selectFirstItem();

        this.addListenerOnce("appear", function(e){
            this.addListener("appear", this._onAppear, this);
        }, this);
    },

    members :
    {
        __items :
        [
            {
                label : "Pulpit",
                content : "app.module.dashboard.Dashboard",
                icon : "menu-dashboard"
            },
            {
                label : "Projekty",
                content : "app.list.Project",
                resourceId : "projects.R",
                icon : "menu-projects"
            },
            {
                label : "Ośrodki",
                content : "app.list.TrainingCenter",
                resourceId : "training_centers.R",
                icon : "menu-training-centers"
            },
            {
                label : "Szkolenia",
                content : "app.list.Course",
                resourceId : "courses.R",
                icon : "menu-courses"
            },
            {
                label : "Grupy szkoleniowe",
                content : "app.grid.Groups",
                resourceId : "groups.R",
                icon : "menu-groups"
            },
            {
                label : "E-dziennik",
                icon : "menu-ejournal",
                children :
                [
                    {
                        label : "oceny",
                        content : "app.module.ejournal.Main#setModule#grades",
                        resourceId : "exam_grades.R"
                    },
                    {
                        label : "obecności",
                        content : "app.module.ejournal.Main#setModule#presence",
                        resourceId : "lesson_presence.R"
                    },
                    {
                        label : "przebieg zajęć",
                        content : "app.module.ejournal.Main#setModule#schedule",
                        resourceId : "course_schedule.R"
                    }
                ]
            },
            {
                label : "Testy, ankiety, quizy",
                resourceId : "quizzes.R",
                icon : "menu-tqa",
                children :
                [
                    {
                        label : "Testy",
                        content : "app.list.Test"
                    },
                    {
                        label : "Ankiety",
                        content : "app.list.Survey"
                    }/*,
                    {
                        label : "Quizy",
                        content : "app.list.Quiz"
                    }*/
                ]
            },
            {
                label : "Wiadomości",
                resourceId : "messages.R",
                icon : "menu-messages",
                children :
                [
                    {
                        label : "odebrane",
                        content : "app.module.mailbox.Inbox"
                    },
                    {
                        label : "wysłane",
                        content : "app.module.mailbox.Outbox"
                    },
                    {
                        label : "usunięte",
                        content : "app.module.mailbox.Trash"
                    }
                ]
            },
            {
                label : "Administracja",
                resourceId : "users.U",
                icon : "menu-admin-tools",
                children :
                [
                    {
                        label : "Użytkownicy",
                        content : "app.grid.Users"
                    },
                    {
                        label : "Zasoby",
                        content : "app.grid.ResourceTypes"
                    },
                    {
                        label : "Raporty",
                        content : "app.list.ReportTemplate"
                    }
                ]
            }
        ],

        __buildTree : function()
        {
            var root = new qx.ui.tree.TreeFolder("root");
            root.setOpen(true);
            this.__prepareTreeItems(0, root, new qx.data.Array(this.__items));
            this.setRoot(root);
        },

        __levels : null,

        __prepareTreeItems : function(level, root, items)
        {
            if (level == 0) {
                this.__levels = {};
            }
            if (this.__levels[level] == null) {
                this.__levels[level] = new qx.data.Array;
            }
            var isNode, node;
            items.forEach(function(item){
                isNode = item.children != null && !!item.children.length;
                if (isNode) {
                    node = new frontend.lib.ui.tree.TreeFolder();
                    node.addListener("changeOpen", this._onChangeOpen(node), this);
                    this.__prepareTreeItems(level + 1, node, new qx.data.Array(item.children));
                } else {
                    node = new frontend.lib.ui.tree.TreeFile();
                }
                qx.lang.Object.getKeys(item).forEach(function(key){
                    if (qx.Class.hasProperty(node.constructor, key)) {
                        node["set" + qx.lang.String.firstUp(key)](item[key]);
                    }
                }, this);
                node.setUserData("level", level);
                node.setUserData("index", this.__levels[level].getLength());

                if (Acl.hasRight(node.getResourceId())
                && (!isNode || node.hasChildren())
                ) {
                    this.__levels[level].push(node);
                    root.add(node);
                }
            }, this);
        },

        __selectFirstItem : function()
        {
            var item = this.getRoot().getChildren()[0];
            while (item)
            {
                if (qx.Class.isSubClassOf(item.constructor, frontend.lib.ui.tree.TreeFile)) {
                    this.setSelection([item]);
                    break;
                }
                item.setOpen(true);
                item = item.getChildren()[0];
            }
        },

        selectItem : function(itemNumber, itemChildNumber)
        {
            var item = this.getRoot().getChildren()[itemNumber].getChildren()[itemChildNumber];
            this.setSelection([item]);
        },

        selectByContent : function(contentID)
        {
            this.getItems(true).forEach(function(item){
                if (item.getContent() == contentID) {
                    this.setSelection([ item ]);
                    return false;
                }
            }, this);
        },

        __getBreadcrumb : function(treeItem)
        {
            var breadcrumb = treeItem.getLabel().toLowerCase();
            var root = this.getRoot();
            var parent;

            while (parent = treeItem.getParent())
            {
                if (parent == treeItem || parent == root) {
                    break;
                }
                treeItem = parent;
                breadcrumb = parent.getLabel().toLowerCase() + " > " + breadcrumb;
            }
            return breadcrumb;
        },

        _onChangeOpen : function(treeItem)
        {
            return function(e)
            {
                if (e.getTarget() == treeItem && e.getData() == true)
                {
                    this.__closeOtherFolders(treeItem);
                }
            }
        },

        __closeOtherFolders : function(treeItem)
        {
            this.__levels[treeItem.getUserData("level")].forEach(function(item){
                if (item != treeItem && item.isOpenable() && item.isOpen()) {
                    item.setOpen(false);
                }
            }, this);
        },

        __checkForContentAndMenu : function(treeItem)
        {
            if (!treeItem) {
                return;
            }

            var application = qx.core.Init.getApplication();
            var widget;

            if (qx.Class.hasProperty(treeItem.constructor, "content") && (widget = treeItem.getContent())) {
                application.setBreadcrumbs(this.__getBreadcrumb(treeItem));
                application.setContent(widget);
            }

            if (qx.Class.hasProperty(treeItem.constructor, "menu") && (widget = treeItem.getMenu())) {
                application.setBreadcrumbs(null);
                application.setMenu(widget);
            }
        },

        _onChangeSelection : function(e)
        {
            this.base(arguments, e);
            this.__checkForContentAndMenu(e.getData()[0]);
        },

        _onAppear : function(e)
        {
            this.__checkForContentAndMenu(this.getSelection()[0]);
        }
    }
});