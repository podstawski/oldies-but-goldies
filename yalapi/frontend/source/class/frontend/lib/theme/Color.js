/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

qx.Theme.define("frontend.lib.theme.Color",
{
    extend : aristo.theme.Color,

    colors :
    {
        "modal-background" : "#999999",
        
        "border-background-invalid" : "#FFBBBB",

        /* missing colors from Aristo theme */
        
        "selected-start" : "#004DAD",
        "selected-end" : "#00368A",

        "shadow" : qx.core.Environment.get("css.rgba") ? "rgba(0, 0, 0, 0.4)" : "#999999",

        "toolbar-start" : "#EFEFEF",
        "toolbar-end" : "#DDDDDD"
    }
});