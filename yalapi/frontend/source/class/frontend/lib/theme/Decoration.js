/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

qx.Theme.define("frontend.lib.theme.Decoration",
{
    extend : aristo.theme.Decoration,

    decorations :
    {
        "single-border-black" :
        {
            decorator : qx.ui.decoration.Single,
            style :
            {
                color : "#000000",
                width : 1
            }
        },

        "google-like-row" :
        {
            include : "single-border-black",
            style :
            {
                width : [0, 0, 1, 0]
            }
        },

        "google-like-container" :
        {
            include : "single-border-black"
        },

        "button-with-shadow" :
        {
            decorator : [
                qx.ui.decoration.MSingleBorder,
                qx.ui.decoration.MBorderRadius,
                qx.ui.decoration.MBoxShadow,
                qx.ui.decoration.MBackgroundColor
            ],
            style :
            {
                color : "#000000",
                width : 1,
                radius : 0,
                shadowLength: 3,
                shadowColor: "shadow",
                shadowBlurRadius: 3
            }
        },

        "button-with-shadow-hovered" :
        {
            include : "button-with-shadow",
            style :
            {
                shadowLength: 0
            }
        },

        "toolbar-with-border" :
        {
            decorator : [
                qx.ui.decoration.MSingleBorder,
                qx.ui.decoration.MBorderRadius,
                qx.ui.decoration.MLinearBackgroundGradient
            ],
            style :
            {
                color : "#C0C0C0",
                width : 1,
                radius : 3,
                orientation : "vertical",
                gradientStart : [ "toolbar-start", 30 ],
                gradientEnd : [ "toolbar-end", 70 ]
            }
        },

        "wizard-step-bar-first" :
        {
            decorator : [
                qx.ui.decoration.MSingleBorder,
                qx.ui.decoration.MBackgroundColor
            ],
            style :
            {
                color : "#000000",
                width : [1, 1, 1, 1]
            }
        },

        "wizard-step-bar" :
        {
            decorator : [
                qx.ui.decoration.MSingleBorder,
                qx.ui.decoration.MBorderRadius,
                qx.ui.decoration.MBackgroundColor
            ],
            style :
            {
                color : "#000000",
                width : [1, 1, 1, 1],
                radius : 3
            }
        },

        "input-css" :
        {
            decorator : [
                qx.ui.decoration.MDoubleBorder,
                qx.ui.decoration.MBackgroundColor
            ],

            style :
            {
                color : "border-input",
                innerColor : "border-inner-input",
                innerWidth: 1,
                width : 1,
                backgroundColor : "background-light"
            }
        },

        "border-invalid-css" :
        {
            include : "input-css",

            style :
            {
                color : "border-invalid",
                innerColor : "border-background-invalid",
                backgroundColor : "border-background-invalid"
            }
        },

        "input-focused-css" :
        {
            include : "input-css",

            style :
            {
                innerColor : "border-focused"
            }
        },

        "input-focused-invalid-css" :
        {
            include : "input-focused-css",

            style :
            {
                innerColor : "input-focused-inner-invalid",
                backgroundColor : "border-background-invalid"
            }
        },

        "border-bottom" :
        {
            include : "main",

            style :
            {
                width : [ 0, 0, 1, 0 ]
            }
        },

        "wizard-title" :
        {
            decorator : [
                qx.ui.decoration.MSingleBorder,
                qx.ui.decoration.MBorderRadius,
                qx.ui.decoration.MBackgroundColor,
                qx.ui.decoration.MBoxShadow
            ],
            style : {
                width : 5,
                color : "#772B90",
                radius : 40,
                backgroundColor : "#772B90",
                shadowColor : "shadow",
                shadowBlurRadius : 4,
                shadowLength : 4
            }
        },

        "wizard-title-ok" :
        {
            include : "wizard-title",
            
            style : {
                width: 5,
                color : "green",
                backgroundColor : "#FFF"
            }
        },

        "wizard-label" :
        {
            include : "wizard-title",
            
            style : {
                width : 1,
                color : "#000",
                radius : 3,
                backgroundColor : "green"
            }
        },

        /* missing decorators from Aristo theme */

        "separator-vertical" :
        {
            decorator: qx.ui.decoration.Single,

            style :
            {
                widthTop : 1,
                colorTop : "border-separator"
            }
        },

        "separator-horizontal" :
        {
            decorator: qx.ui.decoration.Single,

            style :
            {
                widthLeft : 1,
                colorLeft : "border-separator"
            }
        },

        "selected-css" :
        {
            decorator : [
                qx.ui.decoration.MLinearBackgroundGradient
            ],

            style :
            {
                startColorPosition : 0,
                endColorPosition : 100,
                startColor : "selected-start",
                endColor : "selected-end"
            }
        },

        "button-focused" :
        {
            decorator : qx.ui.decoration.Grid,

            style :
            {
                baseImage : "decoration/form/button-focused.png",
                insets    : 2
            }
        }
    }
});