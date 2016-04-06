/* ************************************************************************

Copyright:

License:

Authors:

************************************************************************ */

qx.Theme.define("frontend.lib.theme.Appearance",
{
    extend : aristo.theme.Appearance,

    appearances :
    {
        "button" :
        {
            base : true,

            style : function(states)
            {
                return {
                    cursor : "pointer"
                }
            }
        },

        "window" :
        {
            base : true,

            style : function(states)
            {
                return {
                    showMinimize : false
                };
            }
        },

        "window-modal" :
        {
            alias : "window",
            include : "window",

            style : function(states)
            {
                return {
                    showMaximize : false
                }
            }
        },

        "request-loading" :
        {
            style : function(states)
            {
                return {
                    font : "big"
                }
            }
        },

        "button-with-shadow" :
        {
            style : function(states)
            {
                var decorator = "button-with-shadow", margin = [0, 3, 3, 0], textColor = "#FFFFFF";
                if (states.hovered) {
                    textColor = "#CCCCCC";
                }
                if (states.pressed) {
                    decorator = "button-with-shadow-hovered";
                    margin = [3, 0, 0, 3];
                }
                return {
                    decorator : decorator,
                    font : "medium",
                    textColor : textColor,
                    padding : [5, 10],
                    margin : margin,
                    center : true,
                    cursor : "pointer"
                }
            }
        },

        "create-button" :
        {
            include : "button-with-shadow",

            style : function(states)
            {
                return {
                    font : "big",
                    backgroundColor : "#006600"
                }
            }
        },

        "google-like-title" :
        {
            style : function(states)
            {
                return {
                    height : 25,
                    font : "big"
                }
            }
        },

        "wizard-step-bar" :
        {
            style : function(states)
            {
                var decorator = "wizard-step-bar", backgroundColor = undefined, textColor = "#FFFFFF";
                if (states["current"]) {
                    backgroundColor = "#FF6600";
                    textColor = "#000000";
                } else if (states["previous"]) {
                    backgroundColor = "#006600";
                } else {
                    backgroundColor = "#FF0000";
                }
                if (states["first"]) {
                    //decorator = "wizard-step-bar-first"
                }
                return {
                    decorator : decorator,
                    font : "bold",
                    textColor : textColor,
                    padding : 5,
                    marginLeft : 5,
                    backgroundColor : backgroundColor
                }
            }
        },

        "wizard-step-label" :
        {
            style : function(states)
            {
                return {
                    textAlign : "center",
                    allowGrowX : true
                }
            }
        },

        "wizard-button-next" :
        {
            include : "create-button",

            style : function(states)
            {
                return {
                    font : "bold",
                    width: 150
                }
            }
        },

        "wizard-button-previous" :
        {
            include : "wizard-button-next",

            style : function(states)
            {
                return {

                }
            }
        },

        "color-picker" :
        {
            style : function(states)
            {
                var cursor;
                if (!states.disabled) {
                    cursor = "pointer";
                }
                return {
                    decorator : "main",
                    cursor : cursor
                }
            }
        },

        "timefield" : "combobox",

        "attachment" :
        {
            style : function(states)
            {
                return {

                }
            }
        },

        "attachment-icon" :
        {
            include : "image",

            style : function(states)
            {
                return {
                    alignY : "middle",
                    marginLeft : 10,
                    cursor : "pointer"
                }
            }
        },

        "tinymce" :
        {
            include : "textarea",

            style : function(states)
            {
                return {
                    decorator : null
                }
            }
        },

        "compose-window" :
        {
            alias : "window",
            include : "window",

            style : function(states)
            {
                return {
                    showMinimize : false,
                    minWidth : 500,
                    minHeight : 300
                }
            }
        },

        "preview-images-icon" :
        {
            style : function(states)
            {
                return {
                    cursor : "pointer"
                }
            }
        },

        "list-title" :
        {
            style : function(states)
            {
                return {
                    height : 25,
                    font : "big"
                }
            }
        },

        "table-header-cell/checkbox" : "checkbox",

        "table-action-button" :
        {
            include : "button",

            style : function(states)
            {
                return {
                    textColor : "black",
                    cursor : "pointer"
                }
            }
        },

        "ui-table-list" :
        {
            include : "widget",

            style : function(states)
            {
                return {
                    padding : 10
                }
            }
        },

        "ui-table-list/combotable" : "combobox",
        "ui-table-list/toolbar"    : "ui-toolbar",
        "ui-table-list/tabview"    : "tabview",
        "ui-table-list/searchbox"  : "ui-searchbox",
        "ui-table-list/table"      : "table",
        "ui-table-list/window"     : "window",

        "ui-table-list/selectbox"         : "selectbox",
        "ui-table-list/selectbox-course"  : "selectbox",
        "ui-table-list/selectbox-unit"    : "selectbox",

        "ui-toolbar" :
        {
            include : "toolbar",

            style : function(states)
            {
                return {
                    decorator : "toolbar-with-border",
                    padding   : 10,
                    spacing   : 10,
                    height    : 50
                }
            }
        },

        "ui-toolbar/searchbox" : "ui-searchbox",

        "ui-searchbox" :
        {
            include : "textfield",

            style : function(states)
            {
                return {
                    margin : 0
                }
            }
        },

        "ui-searchbox/textfield" : "widget",

        "ui-filter" :
        {
            include : "widget",

            style : function(states)
            {
                return {

                }
            }
        },


        "ui-filter/selectbox" : "selectbox",

        "app-header" :
        {
            include : "widget",

            style : function(states)
            {
                return {
                    
                }
            }
        },

        "app-header/selectbox" : "selectbox",

        "window/toolbar"            : "ui-toolbar",
        "window/searchbox"          : "ui-searchbox",
        "window/tabview"            : "tabview",
        "window/tab-general"        : "tabview-page",
        "window/tab-description"    : "tabview-page",
        "window/tab-course-units"   : "tabview-page",
        "window/tab-groups"         : "tabview-page",
        "window/tab-users"          : "tabview-page",
        "window/tab-trainers"       : "tabview-page",
        "window/tab-leaders"        : "tabview-page",

        "window/form-course-units/list"     : "widget",
        "window/form-course-units/scroller" : "scrollarea",

        "multi-items-list"          : "widget",
        "multi-items-list/header"   : "widget",
        "multi-items-list/list"     : "widget",
        "multi-items-list/footer"   : "widget",

        "multi-items-row"               : "widget",
        "multi-items-row/name"          : "textfield",
        "multi-items-row/hour-amount"   : "spinner",
        "multi-items-row/coach-id"      : "combobox",
        "multi-items-row/button"        : "image",

        "timefieldspinner"              : "textfield",
        "timefieldspinner/hourfield"    : "spinner",
        "timefieldspinner/minutefield"  : "spinner",


        "text-editor" : "widget",
        "text-editor/toolbar" :
        {
            include : "toolbar",

            style : function(states)
            {
                return {
                    decorator : "border-bottom"
                }
            }
        },

        "dashboard" : "widget",
        "dashboard/calendar" : "datechooser",
        "dashboard/calendar/day" :
        {
            style : function(states)
            {
                var decorator = states.disabled ? undefined : states.selected ? "selected" : undefined;
                if (decorator && qx.core.Environment.get("css.gradients")) {
                    decorator += "-css";
                }
                
                return {
                    decorator : decorator,
                    textColor : states.disabled ? "text-disabled" : states.selected ? "text-selected" : states.otherMonth ? "text-light" : undefined,
                    textAlign : "center",
//                    font      : states.today ? "bold" : undefined,
                    padding   : [ 2, 4 ]
                };
            }
        },
        "dashboard/incoming-events-box" : "groupbox",
        "dashboard/new-messages-box"    : "groupbox",
        "dashboard/new-messages-table"  : "table",
        "dashboard/google-map-window"   : "window",

        "widget/searchbox" : "ui-searchbox",

        "window-modal/tab#personal" : "tabview-page",
        "window-modal/tab#contact" : "tabview-page",
        "window-modal/tab#work" : "tabview-page",
        "window-modal/tab#tax" : "tabview-page",
        "window-modal/tab#zus" : "tabview-page",

        "poland-select" : "widget",
        "poland-select/province" : "selectbox",
        "poland-select/district" : "selectbox",
        "poland-select/community" : "selectbox",

        "button-cancel" :
        {
            include : "button",
            alias : "button",

            style : function(states)
            {
                return {
                    icon : "button-cancel"
                }
            }
        },

        "button-submit" :
        {
            include : "button",
            alias : "button",

            style : function(states)
            {
                return {
                    icon : "button-submit"
                }
            }
        },

        "application-menu" :
        {
            include : "tree"
        },

        "tree-file" :
        {
            include : "tree-item",
            alias : "tree-item",

            style : function(states)
            {
                return {
                    icon : "tree-file"
                };
            }
        },

        "step" :
        {
            style : function(states)
            {
                var icon = "step"
                return {

                }
            }
        },

        "dashboard-data-entry" :
        {
            style : function(states)
            {
                return {
                    cursor : "pointer",
                    font : "big",
                    textColor : "text-title"
                }
            }
        },

        "group-manager" : "window-modal",
        "group-manager/info-box" : "groupbox",
        "group-manager/all-users-box" : "groupbox",
        "group-manager/group-users-box" : "groupbox",
        "group-manager/group-name" : "textfield",
        "group-manager/group-level" : "selectbox",
        "group-manager/searchbox#group" : "ui-searchbox",
        "group-manager/searchbox#all" : "ui-searchbox",
        "group-manager/all-users-table" : "table",
        "group-manager/group-users-table" : "table",
        "group-manager/from-apps-users-table" : "table",
        "group-manager/in-application-users-table" : "table",

        "widget/step" : "wizard-step",
        "widget/arrow" : "image",

        "wizard-step" :
        {
            include : "widget",

            style : function(states)
            {
                return {
                    allowGrowX : false,
                    padding: 10
                }
            }
        },
        
        "wizard-step/title" :
        {
            style : function(states)
            {
                var decorator = "wizard-title", textColor = "#FFF";
                if (states.ok) {
                    decorator += "-ok";
                    textColor = "#000";
                }

                return {
                    alignX : "center",
                    decorator : decorator,
                    textColor : textColor,
                    textAlign : "center",
                    font : "wizard-step-title",
                    width : 80,
                    height : 80
                }
            }
        },

        "wizard-step/label" :
        {
            style : function(states)
            {
                return {
                    alignX : "center",
                    decorator : "wizard-label",
                    textColor : "#000",
                    textAlign : "center",
                    font : "medium",
                    padding : 5,
                    minWidth : 100
                }
            }
        },

        "window-modal/selectbox" : "selectbox",

        /* fixes for Aristo theme */

        "combobox" :
        {
            base : true,
            
            style : function(states)
            {
                var focused = !!states.focused;
                var invalid = !!states.invalid;

                return {
                    shadow : invalid ? "red-shadow" : focused ? "shadow" : undefined
                };
            }
        },

        "combobox/textfield" :
        {
            include : "widget",

            style : function(states)
            {
                return {
                    padding : 3,
                    decorator : undefined
                };
            }
        },

        "datefield" :
        {
            include : "combobox",

            style : function(states)
            {
                return {
                    marginLeft : 2
                }
            }
        },

        "datechooser" :
        {
            base : true,

            style : function(states)
            {
                return {
                    minWidth : 230
                }
            }
        },

        "table" :
        {
            base : true,

            style : function(states)
            {
                return {
                    headerCellHeight : 25
                }
            }
        },

        "selectbox" :
        {
            base : true,
            style : function (states)
            {
                var shadow = undefined;
                if (states.invalid && !states.disabled) {
                    shadow = "red-shadow";
                }
                return {
                    shadow : shadow
                }
            }
        }
    }
});