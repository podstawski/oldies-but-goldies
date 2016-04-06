/* ************************************************************************

   Copyright:
     2010 Guilherme R. Aiolfi

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php

   Authors:
     * Guilherme R. Aiolfi (guilhermeaiolfi)
     * John Spackman (john.spackman@zenesis.com)

   ======================================================================

   This class contains code and resources based on the following work:

   * Aristo
     http://github.com/280north/aristo

     License:
       http://creativecommons.org/licenses/by-sa/3.0/us/

     Authors:
       * 280 North, Inc., http://280north.com/
       * Sofa, http://madebysofa.com/

************************************************************************ */
/**
 * Mixin for the text shadow CSS property.
 * This mixin is usually used by {@link qx.ui.decoration.DynamicDecorator}.
 *
 */
qx.Mixin.define("aristo.decorators.MTextShadow", {
	properties: {
		
		textShadowX: {
			check: "Integer",
			nullable: true,
			apply: "_applyTextShadow"
		},
		
		textShadowY: {
			check: "Integer",
			nullable: true,
			apply: "_applyTextShadow"
		},
		
		textShadowBlur: {
			check: "Integer",
			nullable: true,
			apply: "_applyTextShadow"
		},
		
		textShadowColor: {
			check: "Color",
			nullable: true,
			apply: "_applyTextShadow"
		}
	},
	
	members: {
		_styleTextShadow: function(styles) {
			var Color = qx.theme.manager.Color.getInstance();
			var color = Color.resolve(this.getTextShadowColor());

			if (color != null) {
				var shadowX = this.getTextShadowX()||0,
					shadowY = this.getTextShadowY()||0,
					shadowBlur = this.getTextShadowBlur()||0;

				if (qx.core.Environment.get("engine.name") == "mshtml") {
					styles["filter"] = "Shadow(Color=" + color + ", Direction=135, Strength=" + shadowBlur + ")";
				} else {
					var value = color + " " + shadowX + "px " + shadowY + "px " + shadowBlur + "px";
					styles["text-shadow"] = value;
				}
				styles["font-size"] = "20px";
			}
		},
		

		_applyTextShadow : function() {
			if (qx.core.Environment.get("qx.debug")) {
				if (this._isInitialized()) {
					throw new Error("This decorator is already in-use. Modification is not possible anymore!");
				}
			}
		}
	}
});