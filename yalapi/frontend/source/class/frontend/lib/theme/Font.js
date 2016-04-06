/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

qx.Theme.define("frontend.lib.theme.Font",
{
    extend : aristo.theme.Font,

    fonts :
    {
        "default" :
        {
            size : 11,
            lineHeight : 1.4,
            family : [ "Verdana", "Tahoma" ]
        },

        "bold" :
        {
            size : 11,
            lineHeight : 1.4,
            family : [ "Verdana", "Tahoma" ],
            bold : true
        },

        "small" :
        {
            size : 10,
            lineHeight : 1.4,
            family : [ "Verdana", "Tahoma" ]
        },

        "big" :
        {
            size : 15,
            lineHeight : 1.4,
            family : [ "Verdana", "Tahoma" ],
            bold : true
        },

        "monospace" :
        {
            size : 11,
            lineHeight : 1.4,
            family : [ "Consolas" ]
        },

        "medium" :
        {
            size : 13,
            lineHeight : 1.4,
            family : [ "Verdana", "Tahoma" ],
            bold : true
        },

        "wizard-step-title" :
        {
            size : 50,
            lineHeight : 1.3,
            family : [ "Verdana", "Tahoma" ],
            bold : true
        }
    }
});