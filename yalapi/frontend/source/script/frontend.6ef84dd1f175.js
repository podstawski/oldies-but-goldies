qx.Class.define("Urls",
{
    extend : qx.core.Object,

    statics :
    {
        "COURSES"          : "index.php/courses",
        "COURSE_UNITS"     : "index.php/course-units",
        "PROJECTS"         : "index.php/projects",
        "TRAINING_CENTERS" : "index.php/training-centers",
        "QUIZZES"          : "index.php/quizzes",
        "SURVEY"           : "index.php/surveys",
        "REPORT_TEMPLATES" : "index.php/report-templates",
        "REPORTS"          : "index.php/reports",
        "LOGIN"            : "index.php/login",
        "LOGOUT"           : "index.php/logout",
        "LESSONS"          : "index.php/lessons",
        "USERS"            : "index.php/users",
        "COACHES"          : "index.php/coaches",
        "GROUPS"           : "index.php/groups",
        "GROUP_USERS"      : "index.php/group-users",
        "EXAMS"            : "index.php/exams",
        "ROOMS"            : "index.php/rooms",
        "SURVEY_GROUPS"    : "index.php/survey-groups",
        "RESOURCE_TYPES"   : "index.php/resource-types",
        "RESOURCES"        : "index.php/resources",
        "GROUP_EXAMS"      : "index.php/group-exams",
        "GROUP_GRADES"     : "index.php/group-grades",
        "GROUP_PRESENCE"   : "index.php/group-presence",
        "GROUP_COURSES"    : "index.php/group-courses",
        "COURSE_SCHEDULE"  : "index.php/course-schedule",
        "MESSAGES"         : "index.php/messages",

        /**
         * Resolves url from given alias.
         * Converts params into query string.
         *
         * @param {String} alias
         * @param {null|Map} params
         * @param {null|String} pk
         */
        resolve : function(alias, params)
        {
            var url;
            if (Urls[alias] !== undefined) {
                url = Urls[alias];
            } else  {
                url = alias;
            }

            if (params)
            {
                if (!(/\D/.test(params))) {
                    params = { id : params }
                }
                
                if (params.id !== undefined) {
                    url = url + "/" + params.id;
                    delete params.id;
                }

                url = qx.util.Uri.appendParamsToUrl(url, params);
            }

            //RB if we running source version, go one level up
            var prefix = (qx.core.Environment.get("qx.debug") === true && !qx.lang.String.startsWith(alias, "../")) ? '../' : '';

            return prefix + url;
        }
    }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * A factory creating widgets to use for editing table cells.
 */
qx.Interface.define("qx.ui.table.ICellEditorFactory",
{

  members :
  {
    /**
     * Creates a cell editor.
     *
     * The cellInfo map contains the following properties:
     * <ul>
     * <li>value (var): the cell's value.</li>
     * <li>row (int): the model index of the row the cell belongs to.</li>
     * <li>col (int): the model index of the column the cell belongs to.</li>
     * <li>xPos (int): the x position of the cell in the table pane.</li>
     * <li>table (qx.ui.table.Table) reference to the table, the cell belongs to. </li>
     * </ul>
     *
     * @abstract
     * @param cellInfo {Map} A map containing the information about the cell to
     *      create.
     * @return {qx.ui.core.Widget} the widget that should be used as cell editor.
     */
    createCellEditor : function(cellInfo) {
      return true;
    },


    /**
     * Returns the current value of a cell editor.
     *
     * @abstract
     * @param cellEditor {qx.ui.core.Widget} The cell editor formally created by
     *      {@link #createCellEditor}.
     * @return {var} the current value from the editor.
     */
    getCellEditorValue : function(cellEditor) {
      return true;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * An abstract cell editor factory creating text/password/spinner/... fields.
 */
qx.Class.define("qx.ui.table.celleditor.AbstractField",
{
  extend : qx.core.Object,
  implement : qx.ui.table.ICellEditorFactory,
  type : "abstract",


  properties :
  {
    /**
     * function that validates the result
     * the function will be called with the new value and the old value and is
     * supposed to return the value that is set as the table value.
     **/
    validationFunction :
    {
      check : "Function",
      nullable : true,
      init : null
    }
  },


  members :
  {
    /**
     * Factory to create the editor widget
     *
     * @return {qx.ui.core.Widget} The editor widget
     */
    _createEditor : function() {
      throw new Error("Abstract method call!");
    },


    // interface implementation
    createCellEditor : function(cellInfo)
    {
      var cellEditor = this._createEditor();

      cellEditor.originalValue = cellInfo.value;
      if (cellInfo.value === null || cellInfo.value === undefined) {
        cellInfo.value = "";
      }
      cellEditor.setValue("" + cellInfo.value);

      cellEditor.addListener("appear", function() {
        cellEditor.selectAllText();
      });

      return cellEditor;
    },


    // interface implementation
    getCellEditorValue : function(cellEditor)
    {
      var value = cellEditor.getValue();

      // validation function will be called with new and old value
      var validationFunc = this.getValidationFunction();
      if (validationFunc ) {
        value = validationFunc( value, cellEditor.originalValue );
      }

      if (typeof cellEditor.originalValue == "number") {
        value = parseFloat(value);
      }

      return value;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * A cell editor factory creating text fields.
 */
qx.Class.define("qx.ui.table.celleditor.TextField",
{
  extend : qx.ui.table.celleditor.AbstractField,

  members :
  {
    // overridden
    getCellEditorValue : function(cellEditor)
    {
      var value = cellEditor.getValue();

      // validation function will be called with new and old value
      var validationFunc = this.getValidationFunction();
      if (validationFunc ) {
        value = validationFunc( value, cellEditor.originalValue );
      }

      if (typeof cellEditor.originalValue == "number") {
        if (value != null) {
          value = parseFloat(value);
        }
      }
      return value;
    },


    _createEditor : function()
    {
      var cellEditor = new qx.ui.form.TextField();
      cellEditor.setAppearance("table-editor-textfield");
      return cellEditor;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * A cell renderer for header cells.
 */
qx.Interface.define("qx.ui.table.IHeaderRenderer",
{

  members :
  {
    /**
     * Creates a header cell.
     *
     * The cellInfo map contains the following properties:
     * <ul>
     * <li>col (int): the model index of the column.</li>
     * <li>xPos (int): the x position of the column in the table pane.</li>
     * <li>name (string): the name of the column.</li>
     * <li>editable (boolean): whether the column is editable.</li>
     * <li>sorted (boolean): whether the column is sorted.</li>
     * <li>sortedAscending (boolean): whether sorting is ascending.</li>
     * </ul>
     *
     * @abstract
     * @param cellInfo {Map} A map containing the information about the cell to
     *      create.
     * @return {qx.ui.core.Widget} the widget that renders the header cell.
     */
    createHeaderCell : function(cellInfo) {
      return true;
    },


    /**
     * Updates a header cell.
     *
     * @abstract
     * @param cellInfo {Map} A map containing the information about the cell to
     *      create. This map has the same structure as in {@link #createHeaderCell}.
     * @param cellWidget {qx.ui.core.Widget} the widget that renders the header cell. This is
     *      the same widget formally created by {@link #createHeaderCell}.
     * @return {void}
     */
    updateHeaderCell : function(cellInfo, cellWidget) {
      return true;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)
     * Sebastian Werner (wpbasti)

************************************************************************ */

/**
 * The default header cell renderer.
 *
 * @state hovered {table-header-cell}
 */
qx.Class.define("qx.ui.table.headerrenderer.Default",
{
  extend : qx.core.Object,
  implement : qx.ui.table.IHeaderRenderer,





  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {
    /**
     * {String} The state which will be set for header cells of sorted columns.
     */
    STATE_SORTED           : "sorted",


    /**
     * {String} The state which will be set when sorting is ascending.
     */
    STATE_SORTED_ASCENDING : "sortedAscending"
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /**
     * ToolTip to show if the mouse hovers of the icon
     */
    toolTip :
    {
      check : "String",
      init : null,
      nullable : true
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    // overridden
    createHeaderCell : function(cellInfo)
    {
      var widget = new qx.ui.table.headerrenderer.HeaderCell();
      this.updateHeaderCell(cellInfo, widget);

      return widget;
    },


    // overridden
    updateHeaderCell : function(cellInfo, cellWidget)
    {
      var DefaultHeaderCellRenderer = qx.ui.table.headerrenderer.Default;

      // check for localization [BUG #2699]
      if (cellInfo.name && cellInfo.name.translate) {
        cellWidget.setLabel(cellInfo.name.translate());
      } else {
        cellWidget.setLabel(cellInfo.name);
      }

      // Set image tooltip if given
      var widgetToolTip = cellWidget.getToolTip();
      if (this.getToolTip() != null)
      {
        if (widgetToolTip == null)
        {
          // We have no tooltip yet -> Create one
          widgetToolTip = new qx.ui.tooltip.ToolTip(this.getToolTip());
          cellWidget.setToolTip(widgetToolTip);
          // Link disposer to cellwidget to prevent memory leak
          qx.util.DisposeUtil.disposeTriggeredBy(widgetToolTip, cellWidget);
        }
        else
        {
          // Update tooltip text
          widgetToolTip.setLabel(this.getToolTip());
        }
      }

      cellInfo.sorted ?
        cellWidget.addState(DefaultHeaderCellRenderer.STATE_SORTED) :
        cellWidget.removeState(DefaultHeaderCellRenderer.STATE_SORTED);

      cellInfo.sortedAscending ?
        cellWidget.addState(DefaultHeaderCellRenderer.STATE_SORTED_ASCENDING) :
        cellWidget.removeState(DefaultHeaderCellRenderer.STATE_SORTED_ASCENDING);
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The default header cell widget
 *
 * @childControl label {qx.ui.basic.Label} label of the header cell
 * @childControl sort-icon {qx.ui.basic.Image} sort icon of the header cell
 * @childControl icon {qx.ui.basic.Image} icon of the header cell
 */
qx.Class.define("qx.ui.table.headerrenderer.HeaderCell",
{
  extend : qx.ui.container.Composite,

  construct : function()
  {
    this.base(arguments);

    var layout = new qx.ui.layout.Grid();
    layout.setRowFlex(0, 1);
    layout.setColumnFlex(1, 1);
    layout.setColumnFlex(2, 1);
    this.setLayout(layout);
  },

  properties :
  {
    appearance :
    {
      refine : true,
      init : "table-header-cell"
    },

    /** header cell label */
    label :
    {
      check : "String",
      init : null,
      nullable : true,
      apply : "_applyLabel"
    },

    /** The icon URL of the sorting indicator */
    sortIcon :
    {
      check : "String",
      init : null,
      nullable : true,
      apply : "_applySortIcon",
      themeable : true
    },

    /** Icon URL */
    icon :
    {
      check : "String",
      init : null,
      nullable : true,
      apply : "_applyIcon"
    }
  },

  members :
  {
    // property apply
    _applyLabel : function(value, old)
    {
      if (value) {
        this._showChildControl("label").setValue(value);
      } else {
        this._excludeChildControl("label");
      }
    },


    // property apply
    _applySortIcon : function(value, old)
    {
      if (value) {
        this._showChildControl("sort-icon").setSource(value);
      } else {
        this._excludeChildControl("sort-icon");
      }
    },


    // property apply
    _applyIcon : function(value, old)
    {
      if (value) {
        this._showChildControl("icon").setSource(value);
      } else {
        this._excludeChildControl("icon");
      }
    },


    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
        case "label":
          control = new qx.ui.basic.Label(this.getLabel()).set({
            anonymous: true,
            allowShrinkX: true
          });

          this._add(control, {row: 0, column: 1});
          break;

        case "sort-icon":
          control = new qx.ui.basic.Image(this.getSortIcon());
          control.setAnonymous(true);
          this._add(control, {row: 0, column: 2});
          break;

        case "icon":
          control = new qx.ui.basic.Image(this.getIcon()).set({
            anonymous: true,
            allowShrinkX: true
          });
          this._add(control, {row: 0, column: 0});
          break;
      }

      return control || this.base(arguments, id);
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * A model that contains all meta data about columns, such as width, renderer,
 * visibility and order.
 *
 * @see qx.ui.table.ITableModel
 */
qx.Class.define("qx.ui.table.columnmodel.Basic",
{
  extend : qx.core.Object,


  construct : function()
  {
    this.base(arguments);

    this.__overallColumnArr = [];
    this.__visibleColumnArr = [];
  },


  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events : {

    /**
     * Fired when the width of a column has changed. The data property of the event is
     * a map having the following attributes:
     * <ul>
     *   <li>col: The model index of the column the width of which has changed.</li>
     *   <li>newWidth: The new width of the column in pixels.</li>
     *   <li>oldWidth: The old width of the column in pixels.</li>
     * </ul>
     */
    "widthChanged" : "qx.event.type.Data",

    /**
     * Fired when the visibility of a column has changed. This event is equal to
      * "visibilityChanged", but is fired right before.
     */
    "visibilityChangedPre" : "qx.event.type.Data",

    /**
     * Fired when the visibility of a column has changed. The data property of the
     * event is a map having the following attributes:
     * <ul>
     *   <li>col: The model index of the column the visibility of which has changed.</li>
     *   <li>visible: Whether the column is now visible.</li>
     * </ul>
     */
    "visibilityChanged" : "qx.event.type.Data",

    /**
     * Fired when the column order has changed. The data property of the
     * event is a map having the following attributes:
     * <ul>
     *   <li>col: The model index of the column that was moved.</li>
     *   <li>fromOverXPos: The old overall x position of the column.</li>
     *   <li>toOverXPos: The new overall x position of the column.</li>
     * </ul>
     */
    "orderChanged" : "qx.event.type.Data",

    /**
     * Fired when the cell renderer of a column has changed.
     * The data property of the event is a map having the following attributes:
     * <ul>
     *   <li>col: The model index of the column that was moved.</li>
     * </ul>
     */
    "headerCellRendererChanged" : "qx.event.type.Data"
  },




  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {

    /** {Integer} the default width of a column in pixels. */
    DEFAULT_WIDTH           : 100,

    /** {qx.ui.table.headerrenderer.Default} the default header cell renderer. */
    DEFAULT_HEADER_RENDERER : qx.ui.table.headerrenderer.Default,

    /** {qx.ui.table.cellrenderer.Default} the default data cell renderer. */
    DEFAULT_DATA_RENDERER   : qx.ui.table.cellrenderer.Default,

    /** {qx.ui.table.celleditor.TextField} the default editor factory. */
    DEFAULT_EDITOR_FACTORY  : qx.ui.table.celleditor.TextField
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __internalChange : null,
    __colToXPosMap : null,
    __visibleColumnArr : null,
    __overallColumnArr : null,
    __columnDataArr : null,

    __headerRenderer : null,
    __dataRenderer : null,
    __editorFactory : null,


    /**
     * Initializes the column model.
     *
     * @param colCount {Integer}
     *   The number of columns the model should have.
     *
     * @param table {qx.ui.table.Table}
     *   The table to which this column model is attached.
     */
    init : function(colCount, table)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertInteger(colCount, "Invalid argument 'colCount'.");
      }

      this.__columnDataArr = [];

      var width = qx.ui.table.columnmodel.Basic.DEFAULT_WIDTH;
      var headerRenderer = this.__headerRenderer ||  (this.__headerRenderer = new qx.ui.table.columnmodel.Basic.DEFAULT_HEADER_RENDERER());
      var dataRenderer = this.__dataRenderer || (this.__dataRenderer = new qx.ui.table.columnmodel.Basic.DEFAULT_DATA_RENDERER());
      var editorFactory = this.__editorFactory || (this.__editorFactory = new qx.ui.table.columnmodel.Basic.DEFAULT_EDITOR_FACTORY());
      this.__overallColumnArr = [];
      this.__visibleColumnArr = [];

      // Get the initially hidden column array, if one was provided. Older
      // subclasses may not provide the 'table' argument, so we treat them
      // traditionally with no initially hidden columns.
      var initiallyHiddenColumns;

      // Was a table provided to us?
      if (table)
      {
        // Yup. Get its list of initially hidden columns, if the user provided
        // such a list.
        initiallyHiddenColumns = table.getInitiallyHiddenColumns();
      }

      // If no table was specified, or if the user didn't provide a list of
      // initially hidden columns, use an empty list.
      initiallyHiddenColumns = initiallyHiddenColumns || [];


      for (var col=0; col<colCount; col++)
      {
        this.__columnDataArr[col] =
        {
          width          : width,
          headerRenderer : headerRenderer,
          dataRenderer   : dataRenderer,
          editorFactory  : editorFactory
        };

        this.__overallColumnArr[col] = col;
        this.__visibleColumnArr[col] = col;
      }

      this.__colToXPosMap = null;

      // If any columns are initialy hidden, hide them now. Make it an
      // internal change so that events are not generated.
      this.__internalChange = true;
      for (var hidden=0; hidden<initiallyHiddenColumns.length; hidden++)
      {
        this.setColumnVisible(initiallyHiddenColumns[hidden], false);
      }
      this.__internalChange = false;

      for (col=0; col<colCount; col++)
      {
        var data =
        {
          col     : col,
          visible : this.isColumnVisible(col)
        };

        this.fireDataEvent("visibilityChangedPre", data);
        this.fireDataEvent("visibilityChanged", data);
      }
    },


    /**
     * Return the array of visible columns
     *
     * @return {Array} List of all visible columns
     */
    getVisibleColumns : function() {
      return this.__visibleColumnArr != null ? this.__visibleColumnArr : [];
    },


    /**
     * Sets the width of a column.
     *
     * @param col {Integer}
     *   The model index of the column.
     *
     * @param width {Integer}
     *   The new width the column should get in pixels.
     *
     * @param isMouseAction {Boolean}
     *   <i>true</i> if the column width is being changed as a result of a
     *   mouse drag in the header; false or undefined otherwise.
     *
     * @return {void}
     */
    setColumnWidth : function(col, width, isMouseAction)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertInteger(width, "Invalid argument 'width'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      var oldWidth = this.__columnDataArr[col].width;

      if (oldWidth != width)
      {
        this.__columnDataArr[col].width = width;

        var data =
        {
          col           : col,
          newWidth      : width,
          oldWidth      : oldWidth,
          isMouseAction : isMouseAction || false
        };

        this.fireDataEvent("widthChanged", data);
      }
    },


    /**
     * Returns the width of a column.
     *
     * @param col {Integer} the model index of the column.
     * @return {Integer} the width of the column in pixels.
     */
    getColumnWidth : function(col)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      return this.__columnDataArr[col].width;
    },


    /**
     * Sets the header renderer of a column.
     *
     * @param col {Integer} the model index of the column.
     * @param renderer {qx.ui.table.IHeaderRenderer} the new header renderer the column
     *      should get.
     * @return {void}
     */
    setHeaderCellRenderer : function(col, renderer)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertInterface(renderer, qx.ui.table.IHeaderRenderer, "Invalid argument 'renderer'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      var oldRenderer = this.__columnDataArr[col].headerRenderer;
      if (oldRenderer !== this.__headerRenderer) {
        oldRenderer.dispose();
      }

      this.__columnDataArr[col].headerRenderer = renderer;
      this.fireDataEvent("headerCellRendererChanged", {col:col});
    },


    /**
     * Returns the header renderer of a column.
     *
     * @param col {Integer} the model index of the column.
     * @return {qx.ui.table.IHeaderRenderer} the header renderer of the column.
     */
    getHeaderCellRenderer : function(col)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      return this.__columnDataArr[col].headerRenderer;
    },


    /**
     * Sets the data renderer of a column.
     *
     * @param col {Integer} the model index of the column.
     * @param renderer {qx.ui.table.ICellRenderer} the new data renderer
     *   the column should get.
     * @return {qx.ui.table.ICellRenderer?null} If an old rendere was set and
     *   it was not the default rendere, the old rendere is returnd for
     *   pooling or dipsosing.
     */
    setDataCellRenderer : function(col, renderer)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertInterface(renderer, qx.ui.table.ICellRenderer, "Invalid argument 'renderer'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      this.__columnDataArr[col].dataRenderer = renderer;

      var oldRenderer = this.__columnDataArr[col].dataRenderer;
      if (oldRenderer !== this.__dataRenderer) {
        return oldRenderer;
      }
      return null;
    },


    /**
     * Returns the data renderer of a column.
     *
     * @param col {Integer} the model index of the column.
     * @return {qx.ui.table.ICellRenderer} the data renderer of the column.
     */
    getDataCellRenderer : function(col)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      return this.__columnDataArr[col].dataRenderer;
    },


    /**
     * Sets the cell editor factory of a column.
     *
     * @param col {Integer} the model index of the column.
     * @param factory {qx.ui.table.ICellEditorFactory} the new cell editor factory the column should get.
     * @return {void}
     */
    setCellEditorFactory : function(col, factory)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertInterface(factory, qx.ui.table.ICellEditorFactory, "Invalid argument 'factory'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      var oldRenderer = this.__columnDataArr[col].headerRenderer;
      if (oldRenderer !== this.__editorFactory) {
        oldRenderer.dispose();
      }

      this.__columnDataArr[col].editorFactory = factory;
    },


    /**
     * Returns the cell editor factory of a column.
     *
     * @param col {Integer} the model index of the column.
     * @return {qx.ui.table.ICellEditorFactory} the cell editor factory of the column.
     */
    getCellEditorFactory : function(col)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertNotUndefined(this.__columnDataArr[col], "Column not found in table model");
      }

      return this.__columnDataArr[col].editorFactory;
    },


    /**
     * Returns the map that translates model indexes to x positions.
     *
     * The returned map contains for a model index (int) a map having two
     * properties: overX (the overall x position of the column, int) and
     * visX (the visible x position of the column, int). visX is missing for
     * hidden columns.
     *
     * @return {Map} the "column to x position" map.
     */
    _getColToXPosMap : function()
    {
      if (this.__colToXPosMap == null)
      {
        this.__colToXPosMap = {};

        for (var overX=0; overX<this.__overallColumnArr.length; overX++)
        {
          var col = this.__overallColumnArr[overX];
          this.__colToXPosMap[col] = { overX : overX };
        }

        for (var visX=0; visX<this.__visibleColumnArr.length; visX++)
        {
          var col = this.__visibleColumnArr[visX];
          this.__colToXPosMap[col].visX = visX;
        }
      }

      return this.__colToXPosMap;
    },


    /**
     * Returns the number of visible columns.
     *
     * @return {Integer} the number of visible columns.
     */
    getVisibleColumnCount : function() {
      return this.__visibleColumnArr != null ? this.__visibleColumnArr.length : 0;
    },


    /**
     * Returns the model index of a column at a certain visible x position.
     *
     * @param visXPos {Integer} the visible x position of the column.
     * @return {Integer} the model index of the column.
     */
    getVisibleColumnAtX : function(visXPos)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertInteger(visXPos, "Invalid argument 'visXPos'.");
      }

      return this.__visibleColumnArr[visXPos];
    },


    /**
     * Returns the visible x position of a column.
     *
     * @param col {Integer} the model index of the column.
     * @return {Integer} the visible x position of the column.
     */
    getVisibleX : function(col)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertInteger(col, "Invalid argument 'col'.");
      }

      return this._getColToXPosMap()[col].visX;
    },


    /**
     * Returns the overall number of columns (including hidden columns).
     *
     * @return {Integer} the overall number of columns.
     */
    getOverallColumnCount : function() {
      return this.__overallColumnArr.length;
    },


    /**
     * Returns the model index of a column at a certain overall x position.
     *
     * @param overXPos {Integer} the overall x position of the column.
     * @return {Integer} the model index of the column.
     */
    getOverallColumnAtX : function(overXPos)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertInteger(overXPos, "Invalid argument 'overXPos'.");
      }

      return this.__overallColumnArr[overXPos];
    },


    /**
     * Returns the overall x position of a column.
     *
     * @param col {Integer} the model index of the column.
     * @return {Integer} the overall x position of the column.
     */
    getOverallX : function(col)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertInteger(col, "Invalid argument 'col'.");
      }

      return this._getColToXPosMap()[col].overX;
    },


    /**
     * Returns whether a certain column is visible.
     *
     * @param col {Integer} the model index of the column.
     * @return {Boolean} whether the column is visible.
     */
    isColumnVisible : function(col)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertInteger(col, "Invalid argument 'col'.");
      }

      return (this._getColToXPosMap()[col].visX != null);
    },


    /**
     * Sets whether a certain column is visible.
     *
     * @param col {Integer} the model index of the column.
     * @param visible {Boolean} whether the column should be visible.
     * @return {void}
     */
    setColumnVisible : function(col, visible)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(col, "Invalid argument 'col'.");
        this.assertBoolean(visible, "Invalid argument 'visible'.");
      }

      if (visible != this.isColumnVisible(col))
      {
        if (visible)
        {
          var colToXPosMap = this._getColToXPosMap();

          var overX = colToXPosMap[col].overX;

          if (overX == null) {
            throw new Error("Showing column failed: " + col + ". The column is not added to this TablePaneModel.");
          }

          // get the visX of the next visible column after the column to show
          var nextVisX;

          for (var x=overX+1; x<this.__overallColumnArr.length; x++)
          {
            var currCol = this.__overallColumnArr[x];
            var currVisX = colToXPosMap[currCol].visX;

            if (currVisX != null)
            {
              nextVisX = currVisX;
              break;
            }
          }

          // If there comes no visible column any more, then show the column
          // at the end
          if (nextVisX == null) {
            nextVisX = this.__visibleColumnArr.length;
          }

          // Add the column to the visible columns
          this.__visibleColumnArr.splice(nextVisX, 0, col);
        }
        else
        {
          var visX = this.getVisibleX(col);
          this.__visibleColumnArr.splice(visX, 1);
        }

        // Invalidate the __colToXPosMap
        this.__colToXPosMap = null;

        // Inform the listeners
        if (!this.__internalChange)
        {
          var data =
          {
            col     : col,
            visible : visible
          };

          this.fireDataEvent("visibilityChangedPre", data);
          this.fireDataEvent("visibilityChanged", data);
        }
      }
    },


    /**
     * Moves a column.
     *
     * @param fromOverXPos {Integer} the overall x position of the column to move.
     * @param toOverXPos {Integer} the overall x position of where the column should be
     *      moved to.
     */
    moveColumn : function(fromOverXPos, toOverXPos)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        this.assertInteger(fromOverXPos, "Invalid argument 'fromOverXPos'.");
        this.assertInteger(toOverXPos, "Invalid argument 'toOverXPos'.");
      }

      this.__internalChange = true;

      var col = this.__overallColumnArr[fromOverXPos];
      var visible = this.isColumnVisible(col);

      if (visible) {
        this.setColumnVisible(col, false);
      }

      this.__overallColumnArr.splice(fromOverXPos, 1);
      this.__overallColumnArr.splice(toOverXPos, 0, col);

      // Invalidate the __colToXPosMap
      this.__colToXPosMap = null;

      if (visible) {
        this.setColumnVisible(col, true);
      }
      this.__internalChange = false;

      // Inform the listeners
      var data =
      {
        col          : col,
        fromOverXPos : fromOverXPos,
        toOverXPos   : toOverXPos
      };

      this.fireDataEvent("orderChanged", data);
    },


    /**
     * Reorders all columns to new overall positions. Will fire one "orderChanged" event
     * without data afterwards
     *
     * @param newPositions {Integer[]} Array mapping the index of a column in table model to its wanted overall
     *                            position on screen (both zero based). If the table models holds
     *                            col0, col1, col2 and col3 and you give [1,3,2,0], the new column order
     *                            will be col3, col0, col2, col1
     */
    setColumnsOrder : function(newPositions)
    {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertArray(newPositions, "Invalid argument 'newPositions'.");
      }

      if (newPositions.length == this.__overallColumnArr.length)
      {
        this.__internalChange = true;

        // Go through each column an switch visible ones to invisible. Reason is unknown,
        // this just mimicks the behaviour of moveColumn. Possibly useful because setting
        // a column visible later updates a map with its screen coords.
        var isVisible = new Array(newPositions.length);
        for (var colIdx = 0; colIdx < this.__overallColumnArr.length; colIdx++)
        {
          var visible = this.isColumnVisible(colIdx);
          isVisible[colIdx] = visible; //Remember, as this relies on this.__colToXPosMap which is cleared below
          if (visible){
            this.setColumnVisible(colIdx, false);
          }
        }

        // Store new position values
        this.__overallColumnArr = qx.lang.Array.clone(newPositions);

        // Invalidate the __colToXPosMap
        this.__colToXPosMap = null;

        // Go through each column an switch invisible ones back to visible
        for (var colIdx = 0; colIdx < this.__overallColumnArr.length; colIdx++){
          if (isVisible[colIdx]) {
            this.setColumnVisible(colIdx, true);
          }
        }
        this.__internalChange = false;

        // Inform the listeners. Do not add data as all known listeners in qooxdoo
        // only take this event to mean "total repaint necesscary". Fabian will look
        // after deprecating the data part of the orderChanged - event
        this.fireDataEvent("orderChanged");

      } else {
        throw new Error("setColumnsOrder: Invalid number of column positions given, expected "
                        + this.__overallColumnArr.length + ", got " + newPositions.length);
      }
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function()
  {
    for (var i=0; i< this.__columnDataArr.length; i++)
    {
      this.__columnDataArr[i].headerRenderer.dispose();
      this.__columnDataArr[i].dataRenderer.dispose();
      this.__columnDataArr[i].editorFactory.dispose();
    }

    this.__overallColumnArr = this.__visibleColumnArr =
      this.__columnDataArr = this.__colToXPosMap = null;

    this._disposeObjects(
      "__headerRenderer",
      "__dataRenderer",
      "__editorFactory"
    );
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2007 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)

************************************************************************ */

/**
 * A table column model that automatically resizes columns based on a
 * selected behavior.
 *
 * @see qx.ui.table.columnmodel.Basic
 */
qx.Class.define("qx.ui.table.columnmodel.Resize",
{
  extend : qx.ui.table.columnmodel.Basic,
  include : qx.locale.MTranslation,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    // We don't want to recursively call ourself based on our resetting of
    // column sizes.  Track when we're resizing.
    this.__bInProgress = false;

    // Track when the table has appeared.  We want to ignore resize events
    // until then since we won't be able to determine the available width
    // anyway.
    this.__bAppeared = false;
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /**
     * The behavior to use.
     *
     * The provided behavior must extend {@link qx.ui.table.columnmodel.resizebehavior.Abstract} and
     * implement the <i>onAppear</i>, <i>onTableWidthChanged</i>,
     * <i>onColumnWidthChanged</i> and <i>onVisibilityChanged</i>methods.
     */
    behavior :
    {
      check : "qx.ui.table.columnmodel.resizebehavior.Abstract",
      init : null,
      nullable : true,
      apply : "_applyBehavior",
      event : "changeBehavior"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __bAppeared : null,
    __bInProgress : null,
    __table : null,


    // Behavior modifier
    _applyBehavior : function(value, old)
    {
      if (old != null)
      {
        old.dispose();
        old = null;
      }

      // Tell the new behavior how many columns there are
      value._setNumColumns(this.getOverallColumnCount());
      value.setTableColumnModel(this);
    },


    /**
     * Initializes the column model.
     *
     * @param numColumns {Integer} the number of columns the model should have.
     * @param table {qx.ui.table.Table}
     *   The table which this model is used for. This allows us access to
     *   other aspects of the table, as the <i>behavior</i> sees fit.
     */
    init : function(numColumns, table)
    {
      // Call our superclass
      this.base(arguments, numColumns, table);

      if (this.__table == null)
      {
        this.__table = table;
        // We'll do our column resizing when the table appears, ...
        table.addListener("appear", this._onappear, this);

        // ... when the inner width of the table changes, ...
        table.addListener("tableWidthChanged", this._onTableWidthChanged, this);

        // ... when a vertical scroll bar appears or disappears
        table.addListener(
          "verticalScrollBarChanged",
          this._onverticalscrollbarchanged,
          this
        );

        // We want to manipulate the button visibility menu
        table.addListener(
          "columnVisibilityMenuCreateEnd",
          this._addResetColumnWidthButton,
          this
        );

        // ... when columns are resized, ...
        this.addListener("widthChanged", this._oncolumnwidthchanged, this );

        // ... and when a column visibility changes.
        this.addListener("visibilityChanged", this._onvisibilitychanged, this);
      }

      // Set the initial resize behavior
      if (this.getBehavior() == null) {
        this.setBehavior(new qx.ui.table.columnmodel.resizebehavior.Default());
      }

      // Tell the behavior how many columns there are
      this.getBehavior()._setNumColumns(numColumns);
    },


    /**
     * Get the table widget
     *
     * @return {qx.ui.table.Table} the table widget
     */
    getTable : function() {
      return this.__table;
    },


    /**
     * Reset the column widths to their "onappear" defaults.
     *
     * @param event {qx.event.type.Data}
     *   The "columnVisibilityMenuCreateEnd" event indicating that the menu is
     *   being generated.  The data is a map containing properties <i>table</i>
     *   and <i>menu</i>.
     *
     * @return {void}
     */
    _addResetColumnWidthButton : function(event)
    {
      var data = event.getData();
      var columnButton = data.columnButton;
      var menu = data.menu;
      var o;

      // Add a separator between the column names and our reset button
      o = columnButton.factory("separator");
      menu.add(o);

      // Add a button to reset the column widths
      o = columnButton.factory("user-button",
                               {
                                 text : this.tr("Reset column widths")
                               });
      menu.add(o);
      o.addListener("execute", this._onappear, this);
    },


    /**
     * Event handler for the "appear" event.
     *
     * @param event {qx.event.type.Event}
     *   The "onappear" event object.
     *
     * @return {void}
     */
    _onappear : function(event)
    {
      // Is this a recursive call?
      if (this.__bInProgress)
      {
        // Yup.  Ignore it.
        return ;
      }

      this.__bInProgress = true;

      if (qx.core.Environment.get("qx.debug"))
      {
        if (qx.core.Environment.get("qx.tableResizeDebug"))
        {
          this.debug("onappear");
        }
      }

      // this handler is also called by the "execute" event of the menu button
      this.getBehavior().onAppear(event, event.getType() !== "appear");

      this.__table._updateScrollerWidths();
      this.__table._updateScrollBarVisibility();

      this.__bInProgress = false;

      this.__bAppeared = true;
    },


    /**
     * Event handler for the "tableWidthChanged" event.
     *
     * @param event {qx.event.type.Event}
     *   The "onwindowresize" event object.
     *
     * @return {void}
     */
    _onTableWidthChanged : function(event)
    {
      // Is this a recursive call or has the table not yet been rendered?
      if (this.__bInProgress || !this.__bAppeared)
      {
        // Yup.  Ignore it.
        return;
      }

      this.__bInProgress = true;

      if (qx.core.Environment.get("qx.debug"))
      {
        if (qx.core.Environment.get("qx.tableResizeDebug"))
        {
          this.debug("ontablewidthchanged");
        }
      }

      this.getBehavior().onTableWidthChanged(event);
      this.__bInProgress = false;
    },


    /**
     * Event handler for the "verticalScrollBarChanged" event.
     *
     * @param event {qx.event.type.Data}
     *   The "verticalScrollBarChanged" event object.  The data is a boolean
     *   indicating whether a vertical scroll bar is now present.
     *
     * @return {void}
     */
    _onverticalscrollbarchanged : function(event)
    {
      // Is this a recursive call or has the table not yet been rendered?
      if (this.__bInProgress || !this.__bAppeared)
      {
        // Yup.  Ignore it.
        return;
      }

      this.__bInProgress = true;

      if (qx.core.Environment.get("qx.debug"))
      {
        if (qx.core.Environment.get("qx.tableResizeDebug"))
        {
          this.debug("onverticalscrollbarchanged");
        }
      }

      this.getBehavior().onVerticalScrollBarChanged(event);

      qx.event.Timer.once(function()
      {
        if (this.__table && !this.__table.isDisposed())
        {
          this.__table._updateScrollerWidths();
          this.__table._updateScrollBarVisibility();
        }
      }, this, 0);

      this.__bInProgress = false;
    },


    /**
     * Event handler for the "widthChanged" event.
     *
     * @param event {qx.event.type.Data}
     *   The "widthChanged" event object.
     *
     * @return {void}
     */
    _oncolumnwidthchanged : function(event)
    {
      // Is this a recursive call or has the table not yet been rendered?
      if (this.__bInProgress || !this.__bAppeared)
      {
        // Yup.  Ignore it.
        return;
      }

      this.__bInProgress = true;

      if (qx.core.Environment.get("qx.debug"))
      {
        if (qx.core.Environment.get("qx.tableResizeDebug"))
        {
          this.debug("oncolumnwidthchanged");
        }
      }

      this.getBehavior().onColumnWidthChanged(event);
      this.__bInProgress = false;
    },


    /**
     * Event handler for the "visibilityChanged" event.
     *
     * @param event {qx.event.type.Data}
     *   The "visibilityChanged" event object.
     *
     * @return {void}
     */
    _onvisibilitychanged : function(event)
    {
      // Is this a recursive call or has the table not yet been rendered?
      if (this.__bInProgress || !this.__bAppeared)
      {
        // Yup.  Ignore it.
        return;
      }

      this.__bInProgress = true;

      if (qx.core.Environment.get("qx.debug"))
      {
        if (qx.core.Environment.get("qx.tableResizeDebug"))
        {
          this.debug("onvisibilitychanged");
        }
      }

      this.getBehavior().onVisibilityChanged(event);
      this.__bInProgress = false;
    }
  },


 /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    this.__table = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2007-2008 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * All of the resizing information about a column.
 *
 *  This is used internally by qx.ui.table and qx.ui.progressive's table and
 *  may be used for other widgets as well.
 */
qx.Class.define("qx.ui.core.ColumnData",
{
  extend : qx.ui.core.LayoutItem,


  construct : function()
  {
    this.base(arguments);
    this.setColumnWidth("auto");
  },


  members :
  {
    __computedWidth : null,


    // overridden
    renderLayout : function(left, top, width, height) {
      this.__computedWidth = width;
    },


    /**
     * Get the computed width of the column.
     */
    getComputedWidth : function() {
      return this.__computedWidth;
    },


    /**
     * Get the column's flex value
     *
     * @return {Integer} The column's flex value
     */
    getFlex : function()
    {
      return this.getLayoutProperties().flex || 0;
    },


    /**
     * Set the column width. The column width can be one of the following
     * values:
     *
     * * Pixels: e.g. <code>23</code>
     * * Autosized: <code>"auto"</code>
     * * Flex: e.g. <code>"1*"</code>
     * * Percent: e.g. <code>"33%"</code>
     *
     * @param width {Integer|String} The column width
     * @param flex {Integer?0} Optional flex value of the column
     */
    setColumnWidth : function(width, flex)
    {
      var flex = flex || 0;
      var percent = null;

      if (typeof width == "number")
      {
        this.setWidth(width);
      }
      else if (typeof width == "string")
      {
        if (width == "auto") {
          flex = 1;
        }
        else
        {
          var match = width.match(/^[0-9]+(?:\.[0-9]+)?([%\*])$/);
          if (match)
          {
            if (match[1] == "*") {
              flex = parseFloat(width);
            } else {
              percent = width;
            }
          }
        }
      }
      this.setLayoutProperties({
        flex: flex,
        width: percent
      });
    }
  },

  environment :
  {
    "qx.tableResizeDebug" : false
  }
})
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2007 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)

************************************************************************ */

/**
 * An abstract resize behavior.  All resize behaviors should extend this
 * class.
 */
qx.Class.define("qx.ui.table.columnmodel.resizebehavior.Abstract",
{
  type : "abstract",
  extend : qx.core.Object,



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /**
     * Called when the ResizeTableColumnModel is initialized, and upon loading of
     * a new TableModel, to allow the Resize Behaviors to know how many columns
     * are in use.
     *
     * @abstract
     * @param numColumns {Integer} The numbrer of columns in use.
     * @return {void}
     * @throws the abstract function warning.
     */
    _setNumColumns : function(numColumns) {
      throw new Error("_setNumColumns is abstract");
    },


    /**
     * Called when the table has first been rendered.
     *
     * @abstract
     * @param event {var} The <i>onappear</i> event object.
     * @param forceRefresh {Boolean?false} Whether a refresh should be forced
     * @return {void}
     * @throws the abstract function warning.
     */
    onAppear : function(event, forceRefresh) {
      throw new Error("onAppear is abstract");
    },


    /**
     * Called when the table width changes due to either a window size change
     * or a parent object changing size causing the table to change size.
     *
     * @abstract
     * @param event {var} The <i>tableWidthChanged</i> event object.
     * @return {void}
     * @throws the abstract function warning.
     */
    onTableWidthChanged : function(event) {
      throw new Error("onTableWidthChanged is abstract");
    },


    /**
     * Called when the use of vertical scroll bar in the table changes, either
     * from present to not present, or vice versa.
     *
     * @abstract
     * @param event {var} The <i>verticalScrollBarChanged</i> event object.  This event has data,
     *     obtained via event.getValue(), which is a boolean indicating whether a
     *     vertical scroll bar is now present.
     * @return {void}
     * @throws the abstract function warning.
     */
    onVerticalScrollBarChanged : function(event) {
      throw new Error("onVerticalScrollBarChanged is abstract");
    },


    /**
     * Called when a column width is changed.
     *
     * @abstract
     * @param event {var} The <i>widthChanged</i> event object.  This event has data, obtained via
     *     event.getValue(), which is an object with three properties: the column
     *     which changed width (data.col), the old width (data.oldWidth) and the new
     *     width (data.newWidth).
     * @return {void}
     * @throws the abstract function warning.
     */
    onColumnWidthChanged : function(event) {
      throw new Error("onColumnWidthChanged is abstract");
    },


    /**
     * Called when a column visibility is changed.
     *
     * @abstract
     * @param event {var} The <i>visibilityChanged</i> event object.  This event has data, obtained
     *     via event.getValue(), which is an object with two properties: the column
     *     which changed width (data.col) and the new visibility of the column
     *     (data.visible).
     * @return {void}
     * @throws the abstract function warning.
     */
    onVisibilityChanged : function(event) {
      throw new Error("onVisibilityChanged is abstract");
    },

    /**
     * Determine the inner width available to columns in the table.
     *
     * @return {Integer} The available width
     */
    _getAvailableWidth : function()
    {
      var tableColumnModel = this.getTableColumnModel();

      // Get the inner width off the table
      var table = tableColumnModel.getTable();

      var scrollerArr = table._getPaneScrollerArr();
      if (!scrollerArr[0] || !scrollerArr[0].getLayoutParent().getBounds()) {
        return null;
      };
      var scrollerParentWidth = scrollerArr[0].getLayoutParent().getBounds().width;

      var lastScroller = scrollerArr[scrollerArr.length-1];
      scrollerParentWidth -= lastScroller.getPaneInsetRight();

      return scrollerParentWidth;
    }
  }});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2007 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)

************************************************************************ */

/* ************************************************************************

#require(qx.ui.core.ColumnData)

************************************************************************ */

/**
 * The default resize behavior.  Until a resize model is loaded, the default
 * behavior is to:
 * <ol>
 *   <li>
 *     Upon the table initially appearing, and upon any window resize, divide
 *     the table space equally between the visible columns.
 *   </li>
 *   <li>
 *     When a column is increased in width, all columns to its right are
 *     pushed to the right with no change to their widths.  This may push some
 *     columns off the right edge of the table, causing a horizontal scroll
 *     bar to appear.
 *   </li>
 *   <li>
 *     When a column is decreased in width, if the total width of all columns
 *     is <i>greater than</i> the table width, no additional column width
 *     change is made.
 *   </li>
 *   <li>
 *     When a column is decreased in width, if the total width of all columns
 *     is <i>less than</i> the table width, the visible column
 *     immediately to the right of the column which decreased in width has its
 *     width increased to fill the remaining space.
 *   </li>
 * </ol>
 *
 * A resize model may be loaded to provide more guidance on how to adjust
 * column width upon each of the events: initial appear, window resize, and
 * column resize. *** TO BE FILLED IN ***
 */
qx.Class.define("qx.ui.table.columnmodel.resizebehavior.Default",
{
  extend : qx.ui.table.columnmodel.resizebehavior.Abstract,


  construct : function()
  {
    this.base(arguments);

    this.__resizeColumnData = [];

    // This layout is not connected to a widget but to this class. This class
    // must implement the method "getLayoutChildren", which must return all
    // columns (LayoutItems) which should be recalcutated. The call
    // "layout.renderLayout" will call the method "renderLayout" on each column
    // data object
    // The advantage of the use of the normal layout manager is that the
    // samantics of flex and percent are exectly the same as in the widget code.
    this.__layout = new qx.ui.layout.HBox();
    this.__layout.connectToWidget(this);

    this.__deferredComputeColumnsFlexWidth = new qx.util.DeferredCall(
      this._computeColumnsFlexWidth, this
    );
  },


  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /**
     * A function to instantiate a resize behavior column data object.
     */
    newResizeBehaviorColumnData :
    {
      check : "Function",
      init : function(obj)
      {
        return new qx.ui.core.ColumnData();
      }
    },

    /**
     * Whether to reinitialize default widths on each appear event.
     * Typically, one would want to initialize the default widths only upon
     * the first appearance of the table, but the original behavior was to
     * reinitialize it even if the table is hidden and then reshown
     * (e.g. it's in a pageview and the page is switched and then switched
     * back).
     */
    initializeWidthsOnEveryAppear :
    {
      check : "Boolean",
      init  : false
    },

    /**
     * The table column model in use.  Of particular interest is the method
     * <i>getTable</i> which is a reference to the table widget.  This allows
     * access to any other features of the table, for use in calculating widths
     * of columns.
     */
    tableColumnModel :
    {
      check : "qx.ui.table.columnmodel.Resize"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __layout : null,
    __layoutChildren : null,
    __resizeColumnData : null,
    __deferredComputeColumnsFlexWidth : null,

    /**
     * Whether we have initialized widths on the first appear yet
     */
    __widthsInitialized : false,

    /**
     * Set the width of a column.
     *
     * @param col {Integer} The column whose width is to be set
     *
     * @param width {Integer, String}
     *   The width of the specified column.  The width may be specified as
     *   integer number of pixels (e.g. 100), a string representing percentage
     *   of the inner width of the Table (e.g. "25%"), or a string
     *   representing a flex width (e.g. "1*").
     *
     * @param flex {Integer?0} Optional flex value of the column
     *
     * @throws {Error}
     *   Error is thrown if the provided column number is out of the range.
     */
    setWidth : function(col, width, flex)
    {
      // Ensure the column is within range
      if (col >= this.__resizeColumnData.length) {
        throw new Error("Column number out of range");
      }

      // Set the new width
      this.__resizeColumnData[col].setColumnWidth(width, flex);
      this.__deferredComputeColumnsFlexWidth.schedule();
    },


    /**
     * Set the minimum width of a column.
     *
     * @param col {Integer}
     *   The column whose minimum width is to be set
     *
     * @param width {Integer}
     *   The minimum width of the specified column.
     *
     * @return {void}
     *
     * @throws {Error}
     *   Error is thrown if the provided column number is out of the range.
     */
    setMinWidth : function(col, width)
    {
      // Ensure the column is within range
      if (col >= this.__resizeColumnData.length)
      {
        throw new Error("Column number out of range");
      }

      // Set the new width
      this.__resizeColumnData[col].setMinWidth(width);
      this.__deferredComputeColumnsFlexWidth.schedule();
    },


    /**
     * Set the maximum width of a column.
     *
     * @param col {Integer}
     *   The column whose maximum width is to be set
     *
     * @param width {Integer}
     *   The maximum width of the specified column.
     *
     * @return {void}
     *
     * @throws {Error}
     *   Error is thrown if the provided column number is out of the range.
     */
    setMaxWidth : function(col, width)
    {
      // Ensure the column is within range
      if (col >= this.__resizeColumnData.length) {
        throw new Error("Column number out of range");
      }

      // Set the new width
      this.__resizeColumnData[col].setMaxWidth(width);
      this.__deferredComputeColumnsFlexWidth.schedule();
    },


    /**
     * Set any or all of the width, minimum width, and maximum width of a
     * column in a single call.
     *
     * @param col {Integer}
     *   The column whose attributes are to be changed
     *
     * @param map {Map}
     *   A map containing any or all of the property names "width", "minWidth",
     *   and "maxWidth".  The property values are as described for
     *   {@link #setWidth}, {@link #setMinWidth} and {@link #setMaxWidth}
     *   respectively.
     *
     * @return {void}
     *
     * @throws {Error}
     *   Error is thrown if the provided column number is out of the range.
     */
    set : function(col, map)
    {
      for (var prop in map)
      {
        switch(prop)
        {
          case "width":
            this.setWidth(col, map[prop]);
            break;

          case "minWidth":
            this.setMinWidth(col, map[prop]);
            break;

          case "maxWidth":
            this.setMaxWidth(col, map[prop]);
            break;

          default:
            throw new Error("Unknown property: " + prop);
        }
      }
    },

    // overloaded
    onAppear : function(event, forceRefresh)
    {
      // If we haven't initialized widths at least once, or
      // they want us to reinitialize widths on every appear event...
      if (forceRefresh === true || !this.__widthsInitialized || this.getInitializeWidthsOnEveryAppear())
      {
        // Calculate column widths
        this._computeColumnsFlexWidth();

        // Track that we've initialized widths at least once
        this.__widthsInitialized = true;
      }
    },

    // overloaded
    onTableWidthChanged : function(event) {
      this._computeColumnsFlexWidth();
    },

    // overloaded
    onVerticalScrollBarChanged : function(event) {
      this._computeColumnsFlexWidth();
    },

    // overloaded
    onColumnWidthChanged : function(event)
    {
      // Extend the next column to fill blank space
      this._extendNextColumn(event);
    },

    // overloaded
    onVisibilityChanged : function(event)
    {
      // Event data properties: col, visible
      var data = event.getData();

      // If a column just became visible, resize all columns.
      if (data.visible)
      {
        this._computeColumnsFlexWidth();
        return;
      }

      // Extend the last column to fill blank space
      this._extendLastColumn(event);
    },

    // overloaded
    _setNumColumns : function(numColumns)
    {
      var colData = this.__resizeColumnData;
      // Are there now fewer (or the same number of) columns than there were
      // previously?
      if (numColumns <= colData.length)
      {
        // Yup.  Delete the extras.
        colData.splice(numColumns, colData.length);
        return;
      }

      // There are more columns than there were previously.  Allocate more.
      for (var i=colData.length; i<numColumns; i++)
      {
        colData[i] = this.getNewResizeBehaviorColumnData()();
        colData[i].columnNumber = i;
      }
    },


    /**
     * This method is required by the box layout. If returns an array of items
     * to relayout.
     *
     * @return {qx.ui.core.ColumnData[]} The list of column data object to layout.
     */
    getLayoutChildren : function() {
      return this.__layoutChildren;
    },


    /**
     * Computes the width of all flexible children.
     *
     * @return {void}
     */
    _computeColumnsFlexWidth : function()
    {
      this.__deferredComputeColumnsFlexWidth.cancel();
      var width = this._getAvailableWidth();

      if (width === null) {
        return;
      }

      var tableColumnModel = this.getTableColumnModel();
      var visibleColumns = tableColumnModel.getVisibleColumns();
      var visibleColumnsLength = visibleColumns.length;
      var colData = this.__resizeColumnData;
      var i, l;

      if (visibleColumnsLength === 0) {
        return;
      }

      // Create an array of the visible columns
      var columns = [ ];
      for (i=0; i<visibleColumnsLength; i++)
      {
        columns.push(colData[visibleColumns[i]]);
      }
      this.__layoutChildren = columns;
      this.__clearLayoutCaches();

      // Use a horizontal box layout to determine the available width.
      this.__layout.renderLayout(width, 100);

      // Now that we've calculated the width, set it.
      for (i=0,l=columns.length; i<l; i++)
      {
        var colWidth = columns[i].getComputedWidth();
        tableColumnModel.setColumnWidth(visibleColumns[i], colWidth);
      }
    },


    /**
     * Clear all layout caches of the column datas.
     */
    __clearLayoutCaches : function()
    {
      this.__layout.invalidateChildrenCache();
      var children = this.__layoutChildren;
      for (var i=0,l=children.length; i<l; i++) {
        children[i].invalidateLayoutCache();
      }
    },


    /**
     * Extend the visible column to right of the column which just changed
     * width, to fill any available space within the inner width of the table.
     * This means that if the sum of the widths of all columns exceeds the
     * inner width of the table, no change is made.  If, on the other hand,
     * the sum of the widths of all columns is less than the inner width of
     * the table, the visible column to the right of the column which just
     * changed width is extended to take up the width available within the
     * inner width of the table.
     *
     *
     * @param event {qx.event.type.Data}
     *   The event object.
     *
     * @return {void}
     */
    _extendNextColumn : function(event)
    {
      var tableColumnModel = this.getTableColumnModel();

      // Event data properties: col, oldWidth, newWidth
      var data = event.getData();

      var visibleColumns = tableColumnModel.getVisibleColumns();

      // Determine the available width
      var width = this._getAvailableWidth();

      // Determine the number of visible columns
      var numColumns = visibleColumns.length;

      // Did this column become longer than it was?
      if (data.newWidth > data.oldWidth)
      {
        // Yup.  Don't resize anything else.  The other columns will just get
        // pushed off and require scrollbars be added (if not already there).
        return ;
      }

      // This column became shorter.  See if we no longer take up the full
      // space that's available to us.
      var i;
      var nextCol;
      var widthUsed = 0;

      for (i=0; i<numColumns; i++) {
        widthUsed += tableColumnModel.getColumnWidth(visibleColumns[i]);
      }

      // If the used width is less than the available width...
      if (widthUsed < width)
      {
        // ... then determine the next visible column
        for (i=0; i<visibleColumns.length; i++)
        {
          if (visibleColumns[i] == data.col)
          {
            nextCol = visibleColumns[i + 1];
            break;
          }
        }

        if (nextCol)
        {
          // Make the next column take up the available space.
          var newWidth =
            (width - (widthUsed - tableColumnModel.getColumnWidth(nextCol)));
          tableColumnModel.setColumnWidth(nextCol, newWidth);
        }
      }
    },


    /**
     * If a column was just made invisible, extend the last column to fill any
     * available space within the inner width of the table.  This means that
     * if the sum of the widths of all columns exceeds the inner width of the
     * table, no change is made.  If, on the other hand, the sum of the widths
     * of all columns is less than the inner width of the table, the last
     * column is extended to take up the width available within the inner
     * width of the table.
     *
     *
     * @param event {qx.event.type.Data}
     *   The event object.
     *
     * @return {void}
     */
    _extendLastColumn : function(event)
    {
      var tableColumnModel = this.getTableColumnModel();

      // Event data properties: col, visible
      var data = event.getData();

      // If the column just became visible, don't make any width changes
      if (data.visible)
      {
        return;
      }

      // Get the array of visible columns
      var visibleColumns = tableColumnModel.getVisibleColumns();

      // If no columns are visible...
      if (visibleColumns.length == 0)
      {
        return;
      }

      // Determine the available width
      var width = this._getAvailableWidth(tableColumnModel);

      // Determine the number of visible columns
      var numColumns = visibleColumns.length;

      // See if we no longer take up the full space that's available to us.
      var i;
      var lastCol;
      var widthUsed = 0;

      for (i=0; i<numColumns; i++) {
        widthUsed += tableColumnModel.getColumnWidth(visibleColumns[i]);
      }

      // If the used width is less than the available width...
      if (widthUsed < width)
      {
        // ... then get the last visible column
        lastCol = visibleColumns[visibleColumns.length - 1];

        // Make the last column take up the available space.
        var newWidth =
          (width - (widthUsed - tableColumnModel.getColumnWidth(lastCol)));
        tableColumnModel.setColumnWidth(lastCol, newWidth);
      }
    },


    /**
     * Returns an array of the resizing information of a column.
     *
     * @return {qx.ui.core.ColumnData[]} array of the resizing information of a column.
     */
    _getResizeColumnData : function()
    {
      return this.__resizeColumnData;
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function()
  {
    this.__resizeColumnData = this.__layoutChildren = null;
    this._disposeObjects("__layout", "__deferredComputeColumnsFlexWidth");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)
     * Fabian Jakobs (fjakobs)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * Table
 *
 * A detailed description can be found in the package description
 * {@link qx.ui.table}.
 *
 * @childControl statusbar {qx.ui.basic.Label} label to show the status of the table
 * @childControl column-button {qx.ui.table.columnmenu.Button} button to open the column menu
 */
qx.Class.define("qx.ui.table.Table",
{
  extend : qx.ui.core.Widget,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param tableModel {qx.ui.table.ITableModel ? null}
   *   The table model to read the data from.
   *
   * @param custom {Map ? null}
   *   A map provided to override the various supplemental classes allocated
   *   within this constructor.  Each property must be a function which
   *   returns an object instance, as indicated by shown the defaults listed
   *   here:
   *
   *   <dl>
   *     <dt>initiallyHiddenColumns</dt>
   *       <dd>
   *         {Array?}
   *         A list of column numbers that should be initially invisible. Any
   *         column not mentioned will be initially visible, and if no array
   *         is provided, all columns will be initially visible.
   *       </dd>
   *     <dt>selectionManager</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.selection.Manager(obj);
   *         }
   *       </pre></dd>
   *     <dt>selectionModel</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.selection.Model(obj);
   *         }
   *       </pre></dd>
   *     <dt>tableColumnModel</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.columnmodel.Basic(obj);
   *         }
   *       </pre></dd>
   *     <dt>tablePaneModel</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.pane.Model(obj);
   *         }
   *       </pre></dd>
   *     <dt>tablePane</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.pane.Pane(obj);
   *         }
   *       </pre></dd>
   *     <dt>tablePaneHeader</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.pane.Header(obj);
   *         }
   *       </pre></dd>
   *     <dt>tablePaneScroller</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.pane.Scroller(obj);
   *         }
   *       </pre></dd>
   *     <dt>tablePaneModel</dt>
   *       <dd><pre class='javascript'>
   *         function(obj)
   *         {
   *           return new qx.ui.table.pane.Model(obj);
   *         }
   *       </pre></dd>
   *     <dt>columnMenu</dt>
   *       <dd><pre class='javascript'>
   *         function()
   *         {
   *           return new qx.ui.table.columnmenu.Button();
   *         }
   *       </pre></dd>
   *   </dl>
   */
  construct : function(tableModel, custom)
  {
    this.base(arguments);
    //
    // Use default objects if custom objects are not specified
    //
    if (!custom) {
      custom = { };
    }

    if (custom.initiallyHiddenColumns) {
      this.setInitiallyHiddenColumns(custom.initiallyHiddenColumns);
    }

    if (custom.selectionManager) {
      this.setNewSelectionManager(custom.selectionManager);
    }

    if (custom.selectionModel) {
      this.setNewSelectionModel(custom.selectionModel);
    }

    if (custom.tableColumnModel) {
      this.setNewTableColumnModel(custom.tableColumnModel);
    }

    if (custom.tablePane) {
      this.setNewTablePane(custom.tablePane);
    }

    if (custom.tablePaneHeader) {
      this.setNewTablePaneHeader(custom.tablePaneHeader);
    }

    if (custom.tablePaneScroller) {
      this.setNewTablePaneScroller(custom.tablePaneScroller);
    }

    if (custom.tablePaneModel) {
      this.setNewTablePaneModel(custom.tablePaneModel);
    }

    if (custom.columnMenu) {
      this.setNewColumnMenu(custom.columnMenu);
    }

    this._setLayout(new qx.ui.layout.VBox());

    // Create the child widgets
    this.__scrollerParent = new qx.ui.container.Composite(new qx.ui.layout.HBox());
    this._add(this.__scrollerParent, {flex: 1});

    // Allocate a default data row renderer
    this.setDataRowRenderer(new qx.ui.table.rowrenderer.Default(this));

    // Create the models
    this.__selectionManager = this.getNewSelectionManager()(this);
    this.setSelectionModel(this.getNewSelectionModel()(this));
    this.setTableModel(tableModel || this.getEmptyTableModel());

    // create the main meta column
    this.setMetaColumnCounts([ -1 ]);

    // Make focusable
    this.setTabIndex(1);
    this.addListener("keypress", this._onKeyPress);
    this.addListener("focus", this._onFocusChanged);
    this.addListener("blur", this._onFocusChanged);

    // attach the resize listener to the last child of the layout. This
    // ensures that all other children are laid out before
    var spacer = new qx.ui.core.Widget().set({
      height: 0
    });
    this._add(spacer);
    spacer.addListener("resize", this._onResize, this);

    this.__focusedCol = null;
    this.__focusedRow = null;

    // add an event listener which updates the table content on locale change
    if (qx.core.Environment.get("qx.dynlocale")) {
      qx.locale.Manager.getInstance().addListener("changeLocale", this._onChangeLocale, this);
    }

    this.initStatusBarVisible();

    // If the table model has an init() method...
    tableModel = this.getTableModel();
    if (tableModel.init && typeof(tableModel.init) == "function")
    {
      // ... then call it now to allow the table model to affect table
      // properties.
      tableModel.init(this);
    }
  },




  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events :
  {
    /**
     * Dispatched before adding the column list to the column visibility menu.
     * The event data is a map with two properties: table and menu.  Listeners
     * may add additional items to the menu, which appear at the top of the
     * menu.
     */
    "columnVisibilityMenuCreateStart" : "qx.event.type.Data",

    /**
     * Dispatched after adding the column list to the column visibility menu.
     * The event data is a map with two properties: table and menu.  Listeners
     * may add additional items to the menu, which appear at the bottom of the
     * menu.
     */
    "columnVisibilityMenuCreateEnd" : "qx.event.type.Data",

     /**
      * Dispatched when the width of the table has changed.
      */
    "tableWidthChanged" : "qx.event.type.Event",

    /**
     * Dispatched when updating scrollbars discovers that a vertical scrollbar
     * is needed when it previously was not, or vice versa.  The data is a
     * boolean indicating whether a vertical scrollbar is now being used.
     */
    "verticalScrollBarChanged" : "qx.event.type.Data",

    /**
     * Dispatched when a data cell has been clicked.
     */
    "cellClick" : "qx.ui.table.pane.CellEvent",

    /**
     * Dispatched when a data cell has been clicked.
     */
    "cellDblclick" : "qx.ui.table.pane.CellEvent",

    /**
     * Dispatched when the context menu is needed in a data cell
     */
    "cellContextmenu" : "qx.ui.table.pane.CellEvent",

    /**
     * Dispatched after a cell editor is flushed.
     *
     * The data is a map containing this properties:
     * <ul>
     *   <li>row</li>
     *   <li>col</li>
     *   <li>value</li>
     *   <li>oldValue</li>
     * </ul>
     */
    "dataEdited" : "qx.event.type.Data"
  },



  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {
    /** Events that must be redirected to the scrollers. */
    __redirectEvents : { cellClick: 1, cellDblclick: 1, cellContextmenu: 1 }
  },


  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    appearance :
    {
      refine : true,
      init : "table"
    },


    focusable :
    {
      refine : true,
      init : true
    },


    minWidth :
    {
      refine : true,
      init : 50
    },

    /**
     * The list of columns that are initially hidden. This property is set by
     * the constructor, from the value received in
     * custom.initiallyHiddenColumns, and is only used when a column model is
     * initialized. It can be of great benefit in tables with numerous columns
     * where most are not initially visible. The process of creating the
     * headers for all of the columns, only to have those columns discarded
     * shortly thereafter when setColumnVisibility(false) is called, is a
     * waste of (significant, in some browsers) time. Specifying the
     * non-visible columns at constructor time can therefore avoid the initial
     * creation of all of those superfluous widgets.
     */
    initiallyHiddenColumns :
    {
      init : null
    },

    /**
     * Whether the widget contains content which may be selected by the user.
     *
     * If the value set to <code>true</code> the native browser selection can
     * be used for text selection. But it is normally useful for
     * forms fields, longer texts/documents, editors, etc.
     *
     * Note: This has no effect on Table!
     */
    selectable :
    {
      refine : true,
      init : false
    },


    /** The selection model. */
    selectionModel :
    {
      check : "qx.ui.table.selection.Model",
      apply : "_applySelectionModel",
      event : "changeSelectionModel"
    },


    /** The table model. */
    tableModel :
    {
      check : "qx.ui.table.ITableModel",
      apply : "_applyTableModel",
      event : "changeTableModel"
    },


    /** The height of the table rows. */
    rowHeight :
    {
      check : "Number",
      init : 20,
      apply : "_applyRowHeight",
      event : "changeRowHeight",
      themeable : true
    },


    /**
     * Force line height to match row height.  May be disabled if cell
     * renderers being used wish to render multiple lines of data within a
     * cell.  (With the default setting, all but the first of multiple lines
     * of data will not be visible.)
     */
    forceLineHeight :
    {
      check : "Boolean",
      init  : true
    },


    /**
     *  Whether the header cells are visible. When setting this to false,
     *  you'll likely also want to set the {#columnVisibilityButtonVisible}
     *  property to false as well, to entirely remove the header row.
     */
    headerCellsVisible :
    {
      check : "Boolean",
      init : true,
      apply : "_applyHeaderCellsVisible",
      themeable : true
    },


    /** The height of the header cells. */
    headerCellHeight :
    {
      check : "Integer",
      init : 16,
      apply : "_applyHeaderCellHeight",
      event : "changeHeaderCellHeight",
      nullable : true,
      themeable : true
    },


    /** Whether to show the status bar */
    statusBarVisible :
    {
      check : "Boolean",
      init : true,
      apply : "_applyStatusBarVisible"
    },


    /** The Statusbartext, set it, if you want some more Information */
    additionalStatusBarText :
    {
      nullable : true,
      init : null,
      apply : "_applyAdditionalStatusBarText"
    },


    /** Whether to show the column visibility button */
    columnVisibilityButtonVisible :
    {
      check : "Boolean",
      init : true,
      apply : "_applyColumnVisibilityButtonVisible",
      themeable : true
    },


    /**
     * {Integer[]} The number of columns per meta column. If the last array entry is -1,
     * this meta column will get the remaining columns.
     */
    metaColumnCounts :
    {
      check : "Object",
      apply : "_applyMetaColumnCounts"
    },


    /**
     * Whether the focus should moved when the mouse is moved over a cell. If false
     * the focus is only moved on mouse clicks.
     */
    focusCellOnMouseMove :
    {
      check : "Boolean",
      init : false,
      apply : "_applyFocusCellOnMouseMove"
    },

    /**
     * Whether row focus change by keyboard also modifies selection
     */
    rowFocusChangeModifiesSelection :
    {
      check : "Boolean",
      init : true
    },

    /**
     * Whether the cell focus indicator should be shown
     */
    showCellFocusIndicator :
    {
      check : "Boolean",
      init : true,
      apply : "_applyShowCellFocusIndicator"
    },

    /**
     * By default, the "cellContextmenu" event is fired only when a data cell
     * is right-clicked. It is not fired when a right-click occurs in the
     * empty area of the table below the last data row. By turning on this
     * property, "cellContextMenu" events will also be generated when a
     * right-click occurs in that empty area. In such a case, row identifier
     * in the event data will be null, so event handlers can check (row ===
     * null) to handle this case.
     */
    contextMenuFromDataCellsOnly :
    {
      check : "Boolean",
      init : true,
      apply : "_applyContextMenuFromDataCellsOnly"
    },

    /**
     * Whether the table should keep the first visible row complete. If set to false,
     * the first row may be rendered partial, depending on the vertical scroll value.
     */
    keepFirstVisibleRowComplete :
    {
      check : "Boolean",
      init : true,
      apply : "_applyKeepFirstVisibleRowComplete"
    },


    /**
     * Whether the table cells should be updated when only the selection or the
     * focus changed. This slows down the table update but allows to react on a
     * changed selection or a changed focus in a cell renderer.
     */
    alwaysUpdateCells :
    {
      check : "Boolean",
      init : false
    },


    /**
     * Whether to reset the selection when a header cell is clicked. Since
     * most data models do not have provisions to retain a selection after
     * sorting, the default is to reset the selection in this case. Some data
     * models, however, do have the capability to retain the selection, so
     * when using those, this property should be set to false.
     */
    resetSelectionOnHeaderClick :
    {
      check : "Boolean",
      init : true,
      apply : "_applyResetSelectionOnHeaderClick"
    },


    /** The renderer to use for styling the rows. */
    dataRowRenderer :
    {
      check : "qx.ui.table.IRowRenderer",
      init : null,
      nullable : true,
      event : "changeDataRowRenderer"
    },


    /**
     * A function to call when before modal cell editor is opened.
     *
     * @signature function(cellEditor, cellInfo)
     *
     * @param cellEditor {qx.ui.window.Window}
     *   The modal window which has been created for this cell editor
     *
     * @param cellInfo {Map}
     *   Information about the cell for which this cell editor was created.
     *   It contains the following properties:
     *       col, row, xPos, value
     *
     * @return {void}
     */
    modalCellEditorPreOpenFunction :
    {
      check : "Function",
      init : null,
      nullable : true
    },


    /**
     * A function to instantiate a new column menu button.
     */
    newColumnMenu :
    {
      check : "Function",
      init  : function() {
        return new qx.ui.table.columnmenu.Button();
      }
    },


    /**
     * A function to instantiate a selection manager.  this allows subclasses of
     * Table to subclass this internal class.  To take effect, this property must
     * be set before calling the Table constructor.
     */
    newSelectionManager :
    {
      check : "Function",
      init : function(obj) {
        return new qx.ui.table.selection.Manager(obj);
      }
    },


    /**
     * A function to instantiate a selection model.  this allows subclasses of
     * Table to subclass this internal class.  To take effect, this property must
     * be set before calling the Table constructor.
     */
    newSelectionModel :
    {
      check : "Function",
      init : function(obj) {
        return new qx.ui.table.selection.Model(obj);
      }
    },


    /**
     * A function to instantiate a table column model.  This allows subclasses
     * of Table to subclass this internal class.  To take effect, this
     * property must be set before calling the Table constructor.
     */
    newTableColumnModel :
    {
      check : "Function",
      init : function(table) {
        return new qx.ui.table.columnmodel.Basic(table);
      }
    },


    /**
     * A function to instantiate a table pane.  this allows subclasses of
     * Table to subclass this internal class.  To take effect, this property
     * must be set before calling the Table constructor.
     */
    newTablePane :
    {
      check : "Function",
      init : function(obj) {
        return new qx.ui.table.pane.Pane(obj);
      }
    },


    /**
     * A function to instantiate a table pane.  this allows subclasses of
     * Table to subclass this internal class.  To take effect, this property
     * must be set before calling the Table constructor.
     */
    newTablePaneHeader :
    {
      check : "Function",
      init : function(obj) {
        return new qx.ui.table.pane.Header(obj);
      }
    },


    /**
     * A function to instantiate a table pane scroller.  this allows
     * subclasses of Table to subclass this internal class.  To take effect,
     * this property must be set before calling the Table constructor.
     */
    newTablePaneScroller :
    {
      check : "Function",
      init : function(obj) {
        return new qx.ui.table.pane.Scroller(obj);
      }
    },


    /**
     * A function to instantiate a table pane model.  this allows subclasses
     * of Table to subclass this internal class.  To take effect, this
     * property must be set before calling the Table constructor.
     */
    newTablePaneModel :
    {
      check : "Function",
      init : function(columnModel) {
        return new qx.ui.table.pane.Model(columnModel);
      }
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __focusedCol : null,
    __focusedRow : null,

    __scrollerParent : null,

    __selectionManager : null,

    __additionalStatusBarText : null,
    __lastRowCount : null,
    __internalChange : null,

    __columnMenuButtons : null,
    __columnModel : null,
    __emptyTableModel : null,

    __hadVerticalScrollBar : null,

    __timer : null,


    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
      case "statusbar":
        control = new qx.ui.basic.Label();
        control.set(
          {
            allowGrowX: true
          });
        this._add(control);
        break;

      case "column-button":
        control = this.getNewColumnMenu()();
        control.set({
          focusable : false
        });

        // Create the initial menu too
        var menu = control.factory("menu", { table : this });

        // Add a listener to initialize the column menu when it becomes visible
        menu.addListener(
          "appear",
          this._initColumnMenu,
          this
        );

        break;
      }

      return control || this.base(arguments, id);
    },



    // property modifier
    _applySelectionModel : function(value, old)
    {
      this.__selectionManager.setSelectionModel(value);

      if (old != null) {
        old.removeListener("changeSelection", this._onSelectionChanged, this);
      }

      value.addListener("changeSelection", this._onSelectionChanged, this);
    },


    // property modifier
    _applyRowHeight : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].updateVerScrollBarMaximum();
      }
    },


    // property modifier
    _applyHeaderCellsVisible : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++)
      {
        scrollerArr[i]._excludeChildControl("header");
      }
    },


    // property modifier
    _applyHeaderCellHeight : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].getHeader().setHeight(value);
      }
    },


    /**
     * Get an empty table model instance to use for this table. Use this table
     * to configure the table with no table model.
     *
     * @return {qx.ui.table.ITableModel} The empty table model
     */
    getEmptyTableModel : function()
    {
      if (!this.__emptyTableModel)
      {
        this.__emptyTableModel = new qx.ui.table.model.Simple();
        this.__emptyTableModel.setColumns([]);
        this.__emptyTableModel.setData([]);
      }
      return this.__emptyTableModel;
    },


    // property modifier
    _applyTableModel : function(value, old)
    {
      this.getTableColumnModel().init(value.getColumnCount(), this);

      if (old != null)
      {
        old.removeListener(
          "metaDataChanged",
          this._onTableModelMetaDataChanged, this
        );

        old.removeListener(
          "dataChanged",
          this._onTableModelDataChanged,
          this);
      }

      value.addListener(
        "metaDataChanged",
        this._onTableModelMetaDataChanged, this
      );

      value.addListener(
        "dataChanged",
        this._onTableModelDataChanged,
        this);

      // Update the status bar
      this._updateStatusBar();

      this._updateTableData(
        0, value.getRowCount(),
        0, value.getColumnCount()
      );
      this._onTableModelMetaDataChanged();

      // If the table model has an init() method, call it. We don't, however,
      // call it if this is the initial setting of the table model, as the
      // scrollers are not yet initialized. In that case, the init method is
      // called explicitly by the Table constructor.
      if (old && value.init && typeof(value.init) == "function")
      {
        value.init(this);
      }
    },


    /**
     * Get the The table column model.
     *
     * @return {qx.ui.table.columnmodel.Basic} The table's column model
     */
    getTableColumnModel : function()
    {
      if (!this.__columnModel)
      {
        var columnModel = this.__columnModel = this.getNewTableColumnModel()(this);

        columnModel.addListener("visibilityChanged", this._onColVisibilityChanged, this);
        columnModel.addListener("widthChanged", this._onColWidthChanged, this);
        columnModel.addListener("orderChanged", this._onColOrderChanged, this);

        // Get the current table model
        var tableModel = this.getTableModel();
        columnModel.init(tableModel.getColumnCount(), this);

        // Reset the table column model in each table pane model
        var scrollerArr = this._getPaneScrollerArr();

        for (var i=0; i<scrollerArr.length; i++)
        {
          var paneScroller = scrollerArr[i];
          var paneModel = paneScroller.getTablePaneModel();
          paneModel.setTableColumnModel(columnModel);
        }
      }
      return this.__columnModel;
    },


    // property modifier
    _applyStatusBarVisible : function(value, old)
    {
      if (value) {
        this._showChildControl("statusbar");
      } else {
        this._excludeChildControl("statusbar");
      }

      if (value) {
        this._updateStatusBar();
      }
    },


    // property modifier
    _applyAdditionalStatusBarText : function(value, old)
    {
      this.__additionalStatusBarText = value;
      this._updateStatusBar();
    },


    // property modifier
    _applyColumnVisibilityButtonVisible : function(value, old)
    {
      if (value) {
        this._showChildControl("column-button");
      } else {
        this._excludeChildControl("column-button");
      }
    },


    // property modifier
    _applyMetaColumnCounts : function(value, old)
    {
      var metaColumnCounts = value;
      var scrollerArr = this._getPaneScrollerArr();
      var handlers = { };

      if (value > old)
      {
        // Save event listeners on the redirected events so we can re-apply
        // them to new scrollers.
        var manager = qx.event.Registration.getManager(scrollerArr[0]);
        for (var evName in qx.ui.table.Table.__redirectEvents)
        {
          handlers[evName] = { };
          handlers[evName].capture = manager.getListeners(scrollerArr[0],
                                                          evName,
                                                          true);
          handlers[evName].bubble = manager.getListeners(scrollerArr[0],
                                                         evName,
                                                         false);
        }
      }

      // Remove the panes not needed any more
      this._cleanUpMetaColumns(metaColumnCounts.length);

      // Update the old panes
      var leftX = 0;

      for (var i=0; i<scrollerArr.length; i++)
      {
        var paneScroller = scrollerArr[i];
        var paneModel = paneScroller.getTablePaneModel();
        paneModel.setFirstColumnX(leftX);
        paneModel.setMaxColumnCount(metaColumnCounts[i]);
        leftX += metaColumnCounts[i];
      }

      // Add the new panes
      if (metaColumnCounts.length > scrollerArr.length)
      {
        var columnModel = this.getTableColumnModel();

        for (var i=scrollerArr.length; i<metaColumnCounts.length; i++)
        {
          var paneModel = this.getNewTablePaneModel()(columnModel);
          paneModel.setFirstColumnX(leftX);
          paneModel.setMaxColumnCount(metaColumnCounts[i]);
          leftX += metaColumnCounts[i];

          var paneScroller = this.getNewTablePaneScroller()(this);
          paneScroller.setTablePaneModel(paneModel);

          // Register event listener for vertical scrolling
          paneScroller.addListener("changeScrollY", this._onScrollY, this);

          // Apply redirected events to this new scroller
          for (evName in qx.ui.table.Table.__redirectEvents)
          {
            // On first setting of meta columns (constructing phase), there
            // are no handlers to deal with yet.
            if (! handlers[evName])
            {
              break;
            }

            if (handlers[evName].capture &&
                handlers[evName].capture.length > 0)
            {
              var capture = handlers[evName].capture;
              for (var j = 0; j < capture.length; j++)
              {
                // Determine what context to use.  If the context does not
                // exist, we assume that the context is this table.  If it
                // does exist and it equals the first pane scroller (from
                // which we retrieved the listeners) then set the context
                // to be this new pane scroller.  Otherwise leave the context
                // as it was set.
                var context = capture[j].context;
                if (! context)
                {
                  context = this;
                }
                else if (context == scrollerArr[0])
                {
                  context = paneScroller;
                }

                paneScroller.addListener(
                  evName,
                  capture[j].handler,
                  context,
                  true);
              }
            }

            if (handlers[evName].bubble &&
                handlers[evName].bubble.length > 0)
            {
              var bubble = handlers[evName].bubble;
              for (var j = 0; j < bubble.length; j++)
              {
                // Determine what context to use.  If the context does not
                // exist, we assume that the context is this table.  If it
                // does exist and it equals the first pane scroller (from
                // which we retrieved the listeners) then set the context
                // to be this new pane scroller.  Otherwise leave the context
                // as it was set.
                var context = bubble[j].context;
                if (! context)
                {
                  context = this;
                }
                else if (context == scrollerArr[0])
                {
                  context = paneScroller;
                }

                paneScroller.addListener(
                  evName,
                  bubble[j].handler,
                  context,
                  false);
              }
            }
          }

          // last meta column is flexible
          var flex = (i == metaColumnCounts.length - 1) ? 1 : 0;
          this.__scrollerParent.add(paneScroller, {flex: flex});
          scrollerArr = this._getPaneScrollerArr();
        }
      }

      // Update all meta columns
      for (var i=0; i<scrollerArr.length; i++)
      {
        var paneScroller = scrollerArr[i];
        var isLast = (i == (scrollerArr.length - 1));

        // Set the right header height
        paneScroller.getHeader().setHeight(this.getHeaderCellHeight());

        // Put the column visibility button in the top right corner of the last meta column
        paneScroller.setTopRightWidget(isLast ? this.getChildControl("column-button") : null);
      }

      if (!this.isColumnVisibilityButtonVisible()) {
        this._excludeChildControl("column-button");
      }

      this._updateScrollerWidths();
      this._updateScrollBarVisibility();
    },


    // property modifier
    _applyFocusCellOnMouseMove : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].setFocusCellOnMouseMove(value);
      }
    },


    // property modifier
    _applyShowCellFocusIndicator : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].setShowCellFocusIndicator(value);
      }
    },


    // property modifier
    _applyContextMenuFromDataCellsOnly : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].setContextMenuFromDataCellsOnly(value);
      }
    },


    // property modifier
    _applyKeepFirstVisibleRowComplete : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].onKeepFirstVisibleRowCompleteChanged();
      }
    },


    // property modifier
    _applyResetSelectionOnHeaderClick : function(value, old)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].setResetSelectionOnHeaderClick(value);
      }
    },


    /**
     * Returns the selection manager.
     *
     * @return {qx.ui.table.selection.Manager} the selection manager.
     */
    getSelectionManager : function() {
      return this.__selectionManager;
    },


    /**
     * Returns an array containing all TablePaneScrollers in this table.
     *
     * @return {qx.ui.table.pane.Scroller[]} all TablePaneScrollers in this table.
     */
    _getPaneScrollerArr : function() {
      return this.__scrollerParent.getChildren();
    },


    /**
     * Returns a TablePaneScroller of this table.
     *
     * @param metaColumn {Integer} the meta column to get the TablePaneScroller for.
     * @return {qx.ui.table.pane.Scroller} the qx.ui.table.pane.Scroller.
     */
    getPaneScroller : function(metaColumn) {
      return this._getPaneScrollerArr()[metaColumn];
    },


    /**
     * Cleans up the meta columns.
     *
     * @param fromMetaColumn {Integer} the first meta column to clean up. All following
     *      meta columns will be cleaned up, too. All previous meta columns will
     *      stay unchanged. If 0 all meta columns will be cleaned up.
     * @return {void}
     */
    _cleanUpMetaColumns : function(fromMetaColumn)
    {
      var scrollerArr = this._getPaneScrollerArr();

      if (scrollerArr != null)
      {
        for (var i=scrollerArr.length-1; i>=fromMetaColumn; i--)
        {
          scrollerArr[i].destroy();
        }
      }
    },


    /**
     * Event handler. Called when the locale has changed.
     *
     * @param evt {Event} the event.
     * @return {void}
     */
    _onChangeLocale : function(evt)
    {
      this.updateContent();
      this._updateStatusBar();
    },


    /**
     * Event handler. Called when the selection has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onSelectionChanged : function(evt)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].onSelectionChanged();
      }

      this._updateStatusBar();
    },


    /**
     * Event handler. Called when the table model meta data has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onTableModelMetaDataChanged : function(evt)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].onTableModelMetaDataChanged();
      }

      this._updateStatusBar();
    },


    /**
     * Event handler. Called when the table model data has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onTableModelDataChanged : function(evt)
    {
      var data = evt.getData();

      this._updateTableData(
        data.firstRow, data.lastRow,
        data.firstColumn, data.lastColumn,
        data.removeStart, data.removeCount
      );
    },

    /**
     * To update the table if the table model has changed and remove selection.
     *
     * @param firstRow {Integer} The index of the first row that has changed.
     * @param lastRow {Integer} The index of the last row that has changed.
     * @param firstColumn {Integer} The model index of the first column that has changed.
     * @param lastColumn {Integer} The model index of the last column that has changed.
     * @param removeStart {Integer ? null} The first index of the interval (including), to remove selection.
     * @param removeCount {Integer ? null} The count of the interval, to remove selection.
     * @return {void}
     */
    _updateTableData : function(firstRow, lastRow, firstColumn, lastColumn, removeStart, removeCount)
    {
      var scrollerArr = this._getPaneScrollerArr();

      // update selection if rows were removed
      if (removeCount) {
        this.getSelectionModel().removeSelectionInterval(removeStart, removeStart + removeCount);
        // remove focus if the focused row has been removed
        if (this.__focusedRow >= removeStart && this.__focusedRow < (removeStart + removeCount)) {
          this.setFocusedCell();
        }
      }

      for (var i=0; i<scrollerArr.length; i++)
      {
        scrollerArr[i].onTableModelDataChanged(
          firstRow, lastRow,
          firstColumn, lastColumn
        );
      }

      var rowCount = this.getTableModel().getRowCount();

      if (rowCount != this.__lastRowCount)
      {
        this.__lastRowCount = rowCount;

        this._updateScrollBarVisibility();
        this._updateStatusBar();
      }
    },


    /**
     * Event handler. Called when a TablePaneScroller has been scrolled vertically.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onScrollY : function(evt)
    {
      if (!this.__internalChange)
      {
        this.__internalChange = true;

        // Set the same scroll position to all meta columns
        var scrollerArr = this._getPaneScrollerArr();

        for (var i=0; i<scrollerArr.length; i++) {
          scrollerArr[i].setScrollY(evt.getData());
        }

        this.__internalChange = false;
      }
    },


    /**
     * Event handler. Called when a key was pressed.
     *
     * @param evt {qx.event.type.KeySequence} the event.
     * @return {void}
     */
    _onKeyPress : function(evt)
    {
      if (!this.getEnabled()) {
        return;
      }

      // No editing mode
      var oldFocusedRow = this.__focusedRow;
      var consumed = true;

      // Handle keys that are independent from the modifiers
      var identifier = evt.getKeyIdentifier();

      if (this.isEditing())
      {
        // Editing mode
        if (evt.getModifiers() == 0)
        {
          switch(identifier)
          {
            case "Enter":
              this.stopEditing();
              var oldFocusedRow = this.__focusedRow;
              this.moveFocusedCell(0, 1);

              if (this.__focusedRow != oldFocusedRow) {
                consumed = this.startEditing();
              }

              break;

            case "Escape":
              this.cancelEditing();
              this.focus();
              break;

            default:
              consumed = false;
              break;
          }
        }

      }
      else
      {
        // No editing mode
        if (evt.isCtrlPressed())
        {
          // Handle keys that depend on modifiers
          consumed = true;

          switch(identifier)
          {
            case "A": // Ctrl + A
              var rowCount = this.getTableModel().getRowCount();

              if (rowCount > 0) {
                this.getSelectionModel().setSelectionInterval(0, rowCount - 1);
              }

              break;

            default:
              consumed = false;
              break;
          }
        }
        else
        {
          // Handle keys that are independent from the modifiers
          switch(identifier)
          {
            case "Space":
              this.__selectionManager.handleSelectKeyDown(this.__focusedRow, evt);
              break;

            case "F2":
            case "Enter":
              this.startEditing();
              consumed = true;
              break;

            case "Home":
              this.setFocusedCell(this.__focusedCol, 0, true);
              break;

            case "End":
              var rowCount = this.getTableModel().getRowCount();
              this.setFocusedCell(this.__focusedCol, rowCount - 1, true);
              break;

            case "Left":
              this.moveFocusedCell(-1, 0);
              break;

            case "Right":
              this.moveFocusedCell(1, 0);
              break;

            case "Up":
              this.moveFocusedCell(0, -1);
              break;

            case "Down":
              this.moveFocusedCell(0, 1);
              break;

            case "PageUp":
            case "PageDown":
              var scroller = this.getPaneScroller(0);
              var pane = scroller.getTablePane();
              var rowHeight = this.getRowHeight();
              var direction = (identifier == "PageUp") ? -1 : 1;
              rowCount = pane.getVisibleRowCount() - 1;
              scroller.setScrollY(scroller.getScrollY() + direction * rowCount * rowHeight);
              this.moveFocusedCell(0, direction * rowCount);
              break;

            default:
              consumed = false;
          }
        }
      }

      if (oldFocusedRow != this.__focusedRow &&
          this.getRowFocusChangeModifiesSelection())
      {
        // The focus moved -> Let the selection manager handle this event
        this.__selectionManager.handleMoveKeyDown(this.__focusedRow, evt);
      }

      if (consumed)
      {
        evt.preventDefault();
        evt.stopPropagation();
      }
    },


    /**
     * Event handler. Called when the table gets the focus.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onFocusChanged : function(evt)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].onFocusChanged();
      }
    },


    /**
     * Event handler. Called when the visibility of a column has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onColVisibilityChanged : function(evt)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].onColVisibilityChanged();
      }

      var data = evt.getData();
      if (this.__columnMenuButtons != null && data.col != null &&
          data.visible != null) {
        this.__columnMenuButtons[data.col].setVisible(data.visible);
      }

      this._updateScrollerWidths();
      this._updateScrollBarVisibility();
    },


    /**
     * Event handler. Called when the width of a column has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onColWidthChanged : function(evt)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++)
      {
        var data = evt.getData();
        scrollerArr[i].setColumnWidth(data.col, data.newWidth);
      }

      this._updateScrollerWidths();
      this._updateScrollBarVisibility();
    },


    /**
     * Event handler. Called when the column order has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onColOrderChanged : function(evt)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].onColOrderChanged();
      }

      // A column may have been moved between meta columns
      this._updateScrollerWidths();
      this._updateScrollBarVisibility();
    },


    /**
     * Gets the TablePaneScroller at a certain x position in the page. If there is
     * no TablePaneScroller at this position, null is returned.
     *
     * @param pageX {Integer} the position in the page to check (in pixels).
     * @return {qx.ui.table.pane.Scroller} the TablePaneScroller or null.
     */
    getTablePaneScrollerAtPageX : function(pageX)
    {
      var metaCol = this._getMetaColumnAtPageX(pageX);
      return (metaCol != -1) ? this.getPaneScroller(metaCol) : null;
    },


    /**
     * Sets the currently focused cell. A value of <code>null</code> hides the
     * focus cell.
     *
     * @param col {Integer?null} the model index of the focused cell's column.
     * @param row {Integer?null} the model index of the focused cell's row.
     * @param scrollVisible {Boolean ? false} whether to scroll the new focused cell
     *          visible.
     * @return {void}
     */
    setFocusedCell : function(col, row, scrollVisible)
    {
      if (!this.isEditing() && (col != this.__focusedCol || row != this.__focusedRow))
      {
        if (col === null) {
          col = 0;
        }

        this.__focusedCol = col;
        this.__focusedRow = row;

        var scrollerArr = this._getPaneScrollerArr();

        for (var i=0; i<scrollerArr.length; i++) {
          scrollerArr[i].setFocusedCell(col, row);
        }

        if (col !== null && scrollVisible) {
          this.scrollCellVisible(col, row);
        }
      }
    },


    /**
     * Resets (clears) the current selection
     */
    resetSelection : function() {
      this.getSelectionModel().resetSelection();
    },


    /**
     * Resets the focused cell.
     */
    resetCellFocus : function() {
      this.setFocusedCell(null, null, false);
    },


    /**
     * Returns the column of the currently focused cell.
     *
     * @return {Integer} the model index of the focused cell's column.
     */
    getFocusedColumn : function() {
      return this.__focusedCol;
    },


    /**
     * Returns the row of the currently focused cell.
     *
     * @return {Integer} the model index of the focused cell's column.
     */
    getFocusedRow : function() {
      return this.__focusedRow;
    },


    /**
     * Select whether the focused row is highlighted
     *
     * @param bHighlight {Boolean}
     *   Flag indicating whether the focused row should be highlighted.
     *
     * @return {void}
     */
    highlightFocusedRow : function(bHighlight)
    {
      this.getDataRowRenderer().setHighlightFocusRow(bHighlight);
    },


    /**
     * Remove the highlighting of the current focus row.
     *
     * This is used to temporarily remove the highlighting of the currently
     * focused row, and is expected to be used most typically by adding a
     * listener on the "mouseout" event, so that the focus highlighting is
     * suspended when the mouse leaves the table:
     *
     *     table.addListener("mouseout", table.clearFocusedRowHighlight);
     *
     * @param evt {qx.event.type.Mouse} Incoming mouse event
     * @return {void}
     */
    clearFocusedRowHighlight : function(evt)
    {
      if(evt)
      {
        var relatedTarget = evt.getRelatedTarget();
        if (
          relatedTarget instanceof qx.ui.table.pane.Pane ||
          relatedTarget instanceof qx.ui.table.pane.FocusIndicator
         ) {
           return ;
         }
      }

      // Remove focus from any cell that has it
      this.resetCellFocus();

      // Now, for each pane scroller...
      var scrollerArr = this._getPaneScrollerArr();
      for (var i=0; i<scrollerArr.length; i++)
      {
        // ... repaint without focus.
        scrollerArr[i].onFocusChanged();
      }
    },


    /**
     * Moves the focus.
     *
     * @param deltaX {Integer} The delta by which the focus should be moved on the x axis.
     * @param deltaY {Integer} The delta by which the focus should be moved on the y axis.
     * @return {void}
     */
    moveFocusedCell : function(deltaX, deltaY)
    {
      var col = this.__focusedCol;
      var row = this.__focusedRow;

      // could also be undefined [BUG #4676]
      if (col == null || row == null) {
        return;
      }

      if (deltaX != 0)
      {
        var columnModel = this.getTableColumnModel();
        var x = columnModel.getVisibleX(col);
        var colCount = columnModel.getVisibleColumnCount();
        x = qx.lang.Number.limit(x + deltaX, 0, colCount - 1);
        col = columnModel.getVisibleColumnAtX(x);
      }

      if (deltaY != 0)
      {
        var tableModel = this.getTableModel();
        row = qx.lang.Number.limit(row + deltaY, 0, tableModel.getRowCount() - 1);
      }

      this.setFocusedCell(col, row, true);
    },


    /**
     * Scrolls a cell visible.
     *
     * @param col {Integer} the model index of the column the cell belongs to.
     * @param row {Integer} the model index of the row the cell belongs to.
     * @return {void}
     */
    scrollCellVisible : function(col, row)
    {
      // get the dom element
      var elem = this.getContentElement().getDomElement();
      // if the dom element is not available, the table hasn't been rendered
      if (!elem) {
        // postpone the scroll until the table has appeared
        this.addListenerOnce("appear", function() {
          this.scrollCellVisible(col, row);
        }, this);
      }

      var columnModel = this.getTableColumnModel();
      var x = columnModel.getVisibleX(col);

      var metaColumn = this._getMetaColumnAtColumnX(x);

      if (metaColumn != -1) {
        this.getPaneScroller(metaColumn).scrollCellVisible(col, row);
      }
    },


    /**
     * Returns whether currently a cell is editing.
     *
     * @return {var} whether currently a cell is editing.
     */
    isEditing : function()
    {
      if (this.__focusedCol != null)
      {
        var x = this.getTableColumnModel().getVisibleX(this.__focusedCol);
        var metaColumn = this._getMetaColumnAtColumnX(x);
        return this.getPaneScroller(metaColumn).isEditing();
      }
      return false;
    },


    /**
     * Starts editing the currently focused cell. Does nothing if already editing
     * or if the column is not editable.
     *
     * @return {Boolean} whether editing was started
     */
    startEditing : function()
    {
      if (this.__focusedCol != null)
      {
        var x = this.getTableColumnModel().getVisibleX(this.__focusedCol);
        var metaColumn = this._getMetaColumnAtColumnX(x);
        var started = this.getPaneScroller(metaColumn).startEditing();
        return started;
      }

      return false;
    },


    /**
     * Stops editing and writes the editor's value to the model.
     */
    stopEditing : function()
    {
      if (this.__focusedCol != null)
      {
        var x = this.getTableColumnModel().getVisibleX(this.__focusedCol);
        var metaColumn = this._getMetaColumnAtColumnX(x);
        this.getPaneScroller(metaColumn).stopEditing();
      }
    },


    /**
     * Stops editing without writing the editor's value to the model.
     */
    cancelEditing : function()
    {
      if (this.__focusedCol != null)
      {
        var x = this.getTableColumnModel().getVisibleX(this.__focusedCol);
        var metaColumn = this._getMetaColumnAtColumnX(x);
        this.getPaneScroller(metaColumn).cancelEditing();
      }
    },


    /**
     * Update the table content of every attached table pane.
     */
    updateContent : function() {
      var scrollerArr = this._getPaneScrollerArr();
      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].getTablePane().updateContent(true);
      }
    },

    /**
     * Activates the blocker widgets on all column headers and the
     * column button
     */
    blockHeaderElements : function()
    {
      var scrollerArr = this._getPaneScrollerArr();
      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].getHeader().getBlocker().blockContent(20);
      }
      this.getChildControl("column-button").getBlocker().blockContent(20);
    },


    /**
     * Deactivates the blocker widgets on all column headers and the
     * column button
     */
    unblockHeaderElements : function()
    {
      var scrollerArr = this._getPaneScrollerArr();
      for (var i=0; i<scrollerArr.length; i++) {
        scrollerArr[i].getHeader().getBlocker().unblockContent();
      }
      this.getChildControl("column-button").getBlocker().unblockContent();
    },

    /**
     * Gets the meta column at a certain x position in the page. If there is no
     * meta column at this position, -1 is returned.
     *
     * @param pageX {Integer} the position in the page to check (in pixels).
     * @return {Integer} the index of the meta column or -1.
     */
    _getMetaColumnAtPageX : function(pageX)
    {
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++)
      {
        var pos = scrollerArr[i].getContainerLocation();

        if (pageX >= pos.left && pageX <= pos.right) {
          return i;
        }
      }

      return -1;
    },


    /**
     * Returns the meta column a column is shown in. If the column is not shown at
     * all, -1 is returned.
     *
     * @param visXPos {Integer} the visible x position of the column.
     * @return {Integer} the meta column the column is shown in.
     */
    _getMetaColumnAtColumnX : function(visXPos)
    {
      var metaColumnCounts = this.getMetaColumnCounts();
      var rightXPos = 0;

      for (var i=0; i<metaColumnCounts.length; i++)
      {
        var counts = metaColumnCounts[i];
        rightXPos += counts;

        if (counts == -1 || visXPos < rightXPos) {
          return i;
        }
      }

      return -1;
    },


    /**
     * Updates the text shown in the status bar.
     */
    _updateStatusBar : function()
    {
      var tableModel = this.getTableModel();

      if (this.getStatusBarVisible())
      {
        var selectedRowCount = this.getSelectionModel().getSelectedCount();
        var rowCount = tableModel.getRowCount();

        var text;

        if (rowCount >= 0)
        {
          if (selectedRowCount == 0) {
            text = this.trn("one row", "%1 rows", rowCount, rowCount);
          } else {
            text = this.trn("one of one row", "%1 of %2 rows", rowCount, selectedRowCount, rowCount);
          }
        }

        if (this.__additionalStatusBarText)
        {
          if (text) {
            text += this.__additionalStatusBarText;
          } else {
            text = this.__additionalStatusBarText;
          }
        }

        if (text) {
          this.getChildControl("statusbar").setValue(text);
        }
      }
    },


    /**
     * Updates the widths of all scrollers.
     */
    _updateScrollerWidths : function()
    {
      // Give all scrollers except for the last one the wanted width
      // (The last one has a flex with)
      var scrollerArr = this._getPaneScrollerArr();

      for (var i=0; i<scrollerArr.length; i++)
      {
        var isLast = (i == (scrollerArr.length - 1));
        var width = scrollerArr[i].getTablePaneModel().getTotalWidth();
        scrollerArr[i].setPaneWidth(width);

        var flex = isLast ? 1 : 0;
        scrollerArr[i].setLayoutProperties({flex: flex});
      }
    },


    /**
     * Updates the visibility of the scrollbars in the meta columns.
     */
    _updateScrollBarVisibility : function()
    {
      if (!this.getBounds()) {
        return;
      }

      var horBar = qx.ui.table.pane.Scroller.HORIZONTAL_SCROLLBAR;
      var verBar = qx.ui.table.pane.Scroller.VERTICAL_SCROLLBAR;
      var scrollerArr = this._getPaneScrollerArr();

      // Check which scroll bars are needed
      var horNeeded = false;
      var verNeeded = false;

      for (var i=0; i<scrollerArr.length; i++)
      {
        var isLast = (i == (scrollerArr.length - 1));

        // Only show the last vertical scrollbar
        var bars = scrollerArr[i].getNeededScrollBars(horNeeded, !isLast);

        if (bars & horBar) {
          horNeeded = true;
        }

        if (isLast && (bars & verBar)) {
          verNeeded = true;
        }
      }

      // Set the needed scrollbars
      for (var i=0; i<scrollerArr.length; i++)
      {
        var isLast = (i == (scrollerArr.length - 1));

        // Only show the last vertical scrollbar
        scrollerArr[i].setHorizontalScrollBarVisible(horNeeded);

        // If this is the last meta-column...
        if (isLast)
        {
          // ... then get the current (old) use of vertical scroll bar
          if (this.__hadVerticalScrollBar == null) {
            this.__hadVerticalScrollBar = scrollerArr[i].getVerticalScrollBarVisible();
            this.__timer = qx.event.Timer.once(function()
            {
              // reset the last visible state of the vertical scroll bar
              // in a timeout to prevent infinite loops.
              this.__hadVerticalScrollBar = null;
              this.__timer = null;
            }, this, 0);
          }
        }

        scrollerArr[i].setVerticalScrollBarVisible(isLast && verNeeded);

        // If this is the last meta-column and the use of a vertical scroll bar
        // has changed...
        if (isLast && verNeeded != this.__hadVerticalScrollBar)
        {
          // ... then dispatch an event to any awaiting listeners
          this.fireDataEvent("verticalScrollBarChanged", verNeeded);
        }
      }
    },


    /**
     * Initialize the column menu
     */
    _initColumnMenu : function()
    {
      var tableModel = this.getTableModel();
      var columnModel = this.getTableColumnModel();

      var columnButton = this.getChildControl("column-button");

      // Remove all items from the menu. We'll rebuild it here.
      columnButton.empty();

      // Inform listeners who may want to insert menu items at the beginning
      var menu = columnButton.getMenu();
      var data =
      {
        table        : this,
        menu         : menu,
        columnButton : columnButton
      };
      this.fireDataEvent("columnVisibilityMenuCreateStart", data);

      this.__columnMenuButtons = {};
      for (var col=0, l=tableModel.getColumnCount(); col<l; col++)
      {
        var menuButton =
          columnButton.factory("menu-button",
                               {
                                 text     : tableModel.getColumnName(col),
                                 column   : col,
                                 bVisible : columnModel.isColumnVisible(col)
                               });

        qx.core.Assert.assertInterface(menuButton,
                                       qx.ui.table.IColumnMenuItem);

        menuButton.addListener(
          "changeVisible",
          this._createColumnVisibilityCheckBoxHandler(col), this);
        this.__columnMenuButtons[col] = menuButton;
      }

      // Inform listeners who may want to insert menu items at the end
      data =
      {
        table        : this,
        menu         : menu,
        columnButton : columnButton
      };
      this.fireDataEvent("columnVisibilityMenuCreateEnd", data);
    },





    /**
     * Creates a handler for a check box of the column visibility menu.
     *
     * @param col {Integer} the model index of column to create the handler for.
     * @return {Function} The created event handler.
     */
    _createColumnVisibilityCheckBoxHandler : function(col)
    {
      return function(evt)
      {
        var columnModel = this.getTableColumnModel();
        columnModel.setColumnVisible(col, evt.getData());
      };
    },


    /**
     * Sets the width of a column.
     *
     * @param col {Integer} the model index of column.
     * @param width {Integer} the new width in pixels.
     * @return {void}
     */
    setColumnWidth : function(col, width) {
      this.getTableColumnModel().setColumnWidth(col, width);
    },


    /**
     * Resize event handler
     */
    _onResize : function()
    {
      this.fireEvent("tableWidthChanged");
      this._updateScrollerWidths();
      this._updateScrollBarVisibility();
    },


    // overridden
    addListener : function(type, listener, self, capture)
    {
      if (this.self(arguments).__redirectEvents[type])
      {
        // start the id with the type (needed for removing)
        var id = [type];
        for (var i = 0, arr = this._getPaneScrollerArr(); i < arr.length; i++)
        {
          id.push(arr[i].addListener.apply(arr[i], arguments));
        }
        // join the id's of every event with "
        return id.join('"');
      }
      else
      {
        return this.base(arguments, type, listener, self, capture);
      }
    },


    // overridden
    removeListener : function(type, listener, self, capture)
    {
      if (this.self(arguments).__redirectEvents[type])
      {
        for (var i = 0, arr = this._getPaneScrollerArr(); i < arr.length; i++)
        {
          arr[i].removeListener.apply(arr[i], arguments);
        }
      }
      else
      {
        this.base(arguments, type, listener, self, capture);
      }
    },


    // overridden
    removeListenerById : function(id) {
      var ids = id.split('"');
      // type is the first entry of the connected id
      var type = ids.shift();
      if (this.self(arguments).__redirectEvents[type])
      {
        var removed = true;
        for (var i = 0, arr = this._getPaneScrollerArr(); i < arr.length; i++)
        {
          removed = arr[i].removeListenerById.call(arr[i], ids[i]) && removed;
        }
        return removed;
      }
      else
      {
        return this.base(arguments, id);
      }
    },


    destroy : function()
    {
      this.getChildControl("column-button").getMenu().destroy();
      this.base(arguments);
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function()
  {
    // remove the event listener which handled the locale change
    if (qx.core.Environment.get("qx.dynlocale")) {
      qx.locale.Manager.getInstance().removeListener("changeLocale", this._onChangeLocale, this);
    }

    // we allocated these objects on init so we have to clean them up.
    var selectionModel = this.getSelectionModel();
    if (selectionModel) {
      selectionModel.dispose();
    }

    var dataRowRenderer = this.getDataRowRenderer();
    if (dataRowRenderer) {
      dataRowRenderer.dispose();
    }

    this._cleanUpMetaColumns(0);
    this.getTableColumnModel().dispose();
    this._disposeObjects(
      "__selectionManager", "__scrollerParent",
      "__emptyTableModel", "__emptyTableModel",
      "__columnModel", "__timer"
    );
    this._disposeMap("__columnMenuButtons");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * Interface for a row renderer.
 */
qx.Interface.define("qx.ui.table.IRowRenderer",
{
  members :
  {
    /**
     * Updates a data row.
     *
     * The rowInfo map contains the following properties:
     * <ul>
     * <li>rowData (var): contains the row data for the row.
     *   The kind of this object depends on the table model, see
     *   {@link ITableModel#getRowData()}</li>
     * <li>row (int): the model index of the row.</li>
     * <li>selected (boolean): whether a cell in this row is selected.</li>
     * <li>focusedRow (boolean): whether the focused cell is in this row.</li>
     * <li>table (qx.ui.table.Table): the table the row belongs to.</li>
     * </ul>
     *
     * @abstract
     * @param rowInfo {Map} A map containing the information about the row to
     *      update.
     * @param rowElement {element} the DOM element that renders the data row.
     */
    updateDataRowElement : function(rowInfo, rowElement) {},


    /**
     * Get the row's height CSS style taking the box model into account
     *
     * @param height {Integer} The row's (border-box) height in pixel
     */
    getRowHeightStyle : function(height) {},


    /**
     * Create a style string, which will be set as the style property of the row.
     *
     * @param rowInfo {Map} A map containing the information about the row to
     *      update. See {@link #updateDataRowElement} for more information.
     */
    createRowStyle : function(rowInfo) {},


    /**
     * Create a HTML class string, which will be set as the class property of the row.
     *
     * @param rowInfo {Map} A map containing the information about the row to
     *      update. See {@link #updateDataRowElement} for more information.
     */
    getRowClass : function(rowInfo) {}

  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de
     2007 Visionet GmbH, http://www.visionet.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132) STZ-IDA
     * Dietrich Streifert (level420) Visionet

************************************************************************ */

/**
 * The default data row renderer.
 */
qx.Class.define("qx.ui.table.rowrenderer.Default",
{
  extend : qx.core.Object,
  implement : qx.ui.table.IRowRenderer,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    this.__fontStyleString = "";
    this.__fontStyleString = {};

    this._colors = {};

    // link to font theme
    this._renderFont(qx.theme.manager.Font.getInstance().resolve("default"));

    // link to color theme
    var colorMgr = qx.theme.manager.Color.getInstance();
    this._colors.bgcolFocusedSelected = colorMgr.resolve("table-row-background-focused-selected");
    this._colors.bgcolFocused = colorMgr.resolve("table-row-background-focused");
    this._colors.bgcolSelected = colorMgr.resolve("table-row-background-selected");
    this._colors.bgcolEven = colorMgr.resolve("table-row-background-even");
    this._colors.bgcolOdd = colorMgr.resolve("table-row-background-odd");
    this._colors.colSelected = colorMgr.resolve("table-row-selected");
    this._colors.colNormal = colorMgr.resolve("table-row");
    this._colors.horLine = colorMgr.resolve("table-row-line");
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** Whether the focused row should be highlighted. */
    highlightFocusRow :
    {
      check : "Boolean",
      init : true
    }
  },



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    _colors : null,
    __fontStyle : null,
    __fontStyleString : null,


    /**
     * the sum of the vertical insets. This is needed to compute the box model
     * independent size
     */
    _insetY : 1, // borderBottom

    /**
     * Render the new font and update the table pane content
     * to reflect the font change.
     *
     * @param font {qx.bom.Font} The font to use for the table row
     */
    _renderFont : function(font)
    {
      if (font)
      {
        this.__fontStyle = font.getStyles();
        this.__fontStyleString = qx.bom.element.Style.compile(this.__fontStyle);
        this.__fontStyleString = this.__fontStyleString.replace(/"/g, "'");
      }
      else
      {
        this.__fontStyleString = "";
        this.__fontStyle = qx.bom.Font.getDefaultStyles();
      }
    },


    // interface implementation
    updateDataRowElement : function(rowInfo, rowElem)
    {
      var fontStyle = this.__fontStyle;
      var style = rowElem.style;

      // set font styles
      qx.bom.element.Style.setStyles(rowElem, fontStyle);

      if (rowInfo.focusedRow && this.getHighlightFocusRow())
      {
        style.backgroundColor = rowInfo.selected ? this._colors.bgcolFocusedSelected : this._colors.bgcolFocused;
      }
      else
      {
        if (rowInfo.selected)
        {
          style.backgroundColor = this._colors.bgcolSelected;
        }
        else
        {
          style.backgroundColor = (rowInfo.row % 2 == 0) ? this._colors.bgcolEven : this._colors.bgcolOdd;
        }
      }

      style.color = rowInfo.selected ? this._colors.colSelected : this._colors.colNormal;
      style.borderBottom = "1px solid " + this._colors.horLine;
    },


    /**
     * Get the row's height CSS style taking the box model into account
     *
     * @param height {Integer} The row's (border-box) height in pixel
     */
    getRowHeightStyle : function(height)
    {
      if (qx.core.Environment.get("css.boxmodel") == "content") {
        height -= this._insetY;
      }

      return "height:" + height + "px;";
    },


    // interface implementation
    createRowStyle : function(rowInfo)
    {
      var rowStyle = [];
      rowStyle.push(";");
      rowStyle.push(this.__fontStyleString);
      rowStyle.push("background-color:");

      if (rowInfo.focusedRow && this.getHighlightFocusRow())
      {
        rowStyle.push(rowInfo.selected ? this._colors.bgcolFocusedSelected : this._colors.bgcolFocused);
      }
      else
      {
        if (rowInfo.selected)
        {
          rowStyle.push(this._colors.bgcolSelected);
        }
        else
        {
          rowStyle.push((rowInfo.row % 2 == 0) ? this._colors.bgcolEven : this._colors.bgcolOdd);
        }
      }

      rowStyle.push(';color:');
      rowStyle.push(rowInfo.selected ? this._colors.colSelected : this._colors.colNormal);

      rowStyle.push(';border-bottom: 1px solid ', this._colors.horLine);

      return rowStyle.join("");
    },


    getRowClass : function(rowInfo) {
      return "";
    },

    /**
     * Add extra attributes to each row.
     *
     * @param rowInfo {Object}
     *   The following members are available in rowInfo:
     *   <dl>
     *     <dt>table {qx.ui.table.Table}</dt>
     *     <dd>The table object</dd>
     *
     *     <dt>styleHeight {Integer}</dt>
     *     <dd>The height of this (and every) row</dd>
     *
     *     <dt>row {Integer}</dt>
     *     <dd>The number of the row being added</dd>
     *
     *     <dt>selected {Boolean}</dt>
     *     <dd>Whether the row being added is currently selected</dd>
     *
     *     <dt>focusedRow {Boolean}</dt>
     *     <dd>Whether the row being added is currently focused</dd>
     *
     *     <dt>rowData {Array}</dt>
     *     <dd>The array row from the data model of the row being added</dd>
     *   </dl>
     *
     * @return {String}
     *   Any additional attributes and their values that should be added to the
     *   div tag for the row.
     */
    getRowAttributes : function(rowInfo)
    {
      return "";
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    this._colors = this.__fontStyle = this.__fontStyleString = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2009 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)

************************************************************************ */

/**
 * Interface for creating the column visibility menu
 */
qx.Interface.define("qx.ui.table.IColumnMenuButton",
{
  properties :
  {
    /**
     * The menu which is displayed when this button is pressed.
     */
    menu : { }
  },

  members :
  {
    /**
     * Instantiate a sub-widget.
     *
     * @param item {String}
     *   One of the following strings, indicating what type of
     *   column-menu-specific object to instantiate:
     *   <dl>
     *     <dt>menu</dt>
     *     <dd>
     *       Instantiate a menu which will appear when the column visibility
     *       button is pressed. No options are provided in this case.
     *     </dd>
     *     <dt>menu-button</dt>
     *     <dd>
     *       Instantiate a button to correspond to a column within the
     *       table. The options are a map containing <i>text</i>, the name of
     *       the column; <i>column</i>, the column number; and
     *       <i>bVisible</i>, a boolean indicating whether this column is
     *       currently visible. The instantiated return object must implement
     *       interface {@link qx.ui.table.IColumnMenuItem}
     *     </dd>
     *     <dt>user-button</dt>
     *     <dd>
     *       Instantiate a button for other than a column name. This is used,
     *       for example, to add the "Reset column widths" button when the
     *       Resize column model is requested. The options is a map containing
     *       <i>text</i>, the text to present in the button.
     *     </dd>
     *     <dt>separator</dt>
     *     <dd>
     *       Instantiate a separator object to added to the menu. This is
     *       used, for example, to separate the table column name list from
     *       the "Reset column widths" button when the Resize column model is
     *       requested. No options are provided in this case.
     *     </dd>
     *   </dl>
     *
     * @param options {Map}
     *   Options specific to the <i>item</i> being requested.
     *
     * @return {qx.ui.core.Widget}
     *   The instantiated object as specified by <i>item</i>.
     */
    factory : function(item, options)
    {
      return true;
    },

    /**
     * Empty the menu of all items, in preparation for building a new column
     * visibility menu.
     *
     * @return {void}
     */
    empty : function()
    {
      return true;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)

************************************************************************ */

/**
 * A button which opens the connected menu when clicking on it.
 */
qx.Class.define("qx.ui.form.MenuButton",
{
  extend : qx.ui.form.Button,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param label {String} Initial label
   * @param icon {String?null} Initial icon
   * @param menu {qx.ui.menu.Menu} Connect to menu instance
   */
  construct : function(label, icon, menu)
  {
    this.base(arguments, label, icon);

    // Initialize properties
    if (menu != null) {
      this.setMenu(menu);
    }
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** The menu instance to show when clicking on the button */
    menu :
    {
      check : "qx.ui.menu.Menu",
      nullable : true,
      apply : "_applyMenu",
      event : "changeMenu"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      PROPERTY APPLY ROUTINES
    ---------------------------------------------------------------------------
    */

    // property apply
    _applyMenu : function(value, old)
    {
      if (old)
      {
        old.removeListener("changeVisibility", this._onMenuChange, this);
        old.resetOpener();
      }

      if (value)
      {
        value.addListener("changeVisibility", this._onMenuChange, this);
        value.setOpener(this);

        value.removeState("submenu");
        value.removeState("contextmenu");
      }
    },




    /*
    ---------------------------------------------------------------------------
      HELPER METHODS
    ---------------------------------------------------------------------------
    */

    /**
     * Positions and shows the attached menu widget.
     *
     * @param selectFirst {Boolean?false} Whether the first menu button should be selected
     */
    open : function(selectFirst)
    {
      var menu = this.getMenu();

      if (menu)
      {
        // Hide all menus first
        qx.ui.menu.Manager.getInstance().hideAll();

        // Open the attached menu
        menu.setOpener(this);
        menu.open();

        // Select first item
        if (selectFirst)
        {
          var first = menu.getSelectables()[0];
          if (first) {
            menu.setSelectedButton(first);
          }
        }
      }
    },




    /*
    ---------------------------------------------------------------------------
      EVENT LISTENERS
    ---------------------------------------------------------------------------
    */

    /**
     * Listener for visibility property changes of the attached menu
     *
     * @param e {qx.event.type.Data} Property change event
     */
    _onMenuChange : function(e)
    {
      var menu = this.getMenu();

      if (menu.isVisible()) {
        this.addState("pressed");
      } else {
        this.removeState("pressed");
      }
    },


    // overridden
    _onMouseDown : function(e)
    {
      // call the base function to get into the capture phase [BUG #4340]
      this.base(arguments, e);

      // only open on left clicks [BUG #5125]
      if(e.getButton() != "left") {
        return;
      }

      var menu = this.getMenu();
      if (menu)
      {
        // Toggle sub menu visibility
        if (!menu.isVisible()) {
          this.open();
        } else {
          menu.exclude();
        }

        // Event is processed, stop it for others
        e.stopPropagation();
      }
    },


    // overridden
    _onMouseUp : function(e)
    {
      // call base for firing the execute event
      this.base(arguments, e);

      // Just stop propagation to stop menu manager
      // from getting the event
      e.stopPropagation();
    },


    // overridden
    _onMouseOver : function(e)
    {
      // Add hovered state
      this.addState("hovered");
    },


    // overridden
    _onMouseOut : function(e)
    {
      // Just remove the hover state
      this.removeState("hovered");
    },


    // overridden
    _onKeyDown : function(e)
    {
      switch(e.getKeyIdentifier())
      {
        case "Enter":
          this.removeState("abandoned");
          this.addState("pressed");

          var menu = this.getMenu();
          if (menu)
          {
            // Toggle sub menu visibility
            if (!menu.isVisible()) {
              this.open();
            } else {
              menu.exclude();
            }
          }

          e.stopPropagation();
      }
    },


    // overridden
    _onKeyUp : function(e) {
      // no action required here
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)

************************************************************************ */

/**
 * This singleton manages visible menu instances and supports some
 * core features to schedule menu open/close with timeout support.
 *
 * It also manages the whole keyboard support for the currently
 * registered widgets.
 *
 * The zIndex order is also managed by this class.
 */
qx.Class.define("qx.ui.menu.Manager",
{
  type : "singleton",
  extend : qx.core.Object,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    // Create data structure
    this.__objects = [];

    var el = document.body;
    var Registration = qx.event.Registration;

    // React on mousedown/mouseup events, but on native, to support inline applications
    Registration.addListener(window.document.documentElement, "mousedown", this._onMouseDown, this, true);

    // React on keypress events
    Registration.addListener(el, "keydown", this._onKeyUpDown, this, true);
    Registration.addListener(el, "keyup", this._onKeyUpDown, this, true);
    Registration.addListener(el, "keypress", this._onKeyPress, this, true);

    // only use the blur event to hide windows on non touch devices [BUG #4033]
    // When the menu is locaed on top of an iFrame, the select will fail
    if (!qx.core.Environment.get("event.touch")) {
      // Hide all when the window is blurred
      qx.bom.Element.addListener(window, "blur", this.hideAll, this);
    }

    // Create open timer
    this.__openTimer = new qx.event.Timer;
    this.__openTimer.addListener("interval", this._onOpenInterval, this);

    // Create close timer
    this.__closeTimer = new qx.event.Timer;
    this.__closeTimer.addListener("interval", this._onCloseInterval, this);
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __scheduleOpen : null,
    __scheduleClose : null,
    __openTimer : null,
    __closeTimer : null,
    __objects : null,




    /*
    ---------------------------------------------------------------------------
      HELPER METHODS
    ---------------------------------------------------------------------------
    */

    /**
     * Query engine for menu children.
     *
     * @param menu {qx.ui.menu.Menu} Any menu instance
     * @param start {Integer} Child index to start with
     * @param iter {Integer} Iteration count, normally <code>+1</code> or <code>-1</code>
     * @param loop {Boolean?false} Whether to wrap when reaching the begin/end of the list
     * @return {qx.ui.menu.Button} Any menu button or <code>null</code>
     */
    _getChild : function(menu, start, iter, loop)
    {
      var children = menu.getChildren();
      var length = children.length;
      var child;

      for (var i=start; i<length && i>=0; i+=iter)
      {
        child = children[i];
        if (child.isEnabled() && !child.isAnonymous()) {
          return child;
        }
      }

      if (loop)
      {
        i = i == length ? 0 : length-1;
        for (; i!=start; i+=iter)
        {
          child = children[i];
          if (child.isEnabled() && !child.isAnonymous()) {
            return child;
          }
        }
      }

      return null;
    },


    /**
     * Whether the given widget is inside any Menu instance.
     *
     * @param widget {qx.ui.core.Widget} Any widget
     * @return {Boolean} <code>true</code> when the widget is part of any menu
     */
    _isInMenu : function(widget)
    {
      while(widget)
      {
        if (widget instanceof qx.ui.menu.Menu) {
          return true;
        }

        widget = widget.getLayoutParent();
      }

      return false;
    },


    /**
     * Returns an instance of a menu button if the given widget is a child
     *
     * @param widget {qx.ui.core.Widget} any widget
     * @return {qx.ui.menu.Button} Any menu button instance or <code>null</code>
     */
    _getMenuButton : function(widget)
    {
      while(widget)
      {
        if (widget instanceof qx.ui.menu.AbstractButton) {
          return widget;
        }

        widget = widget.getLayoutParent();
      }

      return null;
    },


    /*
    ---------------------------------------------------------------------------
      PUBLIC METHODS
    ---------------------------------------------------------------------------
    */

    /**
     * Adds a menu to the list of visible menus.
     *
     * @param obj {qx.ui.menu.Menu} Any menu instance.
     */
    add : function(obj)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        if (!(obj instanceof qx.ui.menu.Menu)) {
          throw new Error("Object is no menu: " + obj);
        }
      }

      var reg = this.__objects;
      reg.push(obj);
      obj.setZIndex(1e6+reg.length);
    },


    /**
     * Remove a menu from the list of visible menus.
     *
     * @param obj {qx.ui.menu.Menu} Any menu instance.
     */
    remove : function(obj)
    {
      if (qx.core.Environment.get("qx.debug"))
      {
        if (!(obj instanceof qx.ui.menu.Menu)) {
          throw new Error("Object is no menu: " + obj);
        }
      }

      var reg = this.__objects;
      if (reg) {
        qx.lang.Array.remove(reg, obj);
      }
    },


    /**
     * Hides all currently opened menus.
     */
    hideAll : function()
    {
      var reg = this.__objects;
      if (reg)
      {
        for (var i=reg.length-1; i>=0; i--) {
          reg[i].exclude();
        }
      }
    },


    /**
     * Returns the menu which was opened at last (which
     * is the active one this way)
     *
     * @return {qx.ui.menu.Menu} The current active menu or <code>null</code>
     */
    getActiveMenu : function()
    {
      var reg = this.__objects;
      return reg.length > 0 ? reg[reg.length-1] : null;
    },




    /*
    ---------------------------------------------------------------------------
      SCHEDULED OPEN/CLOSE SUPPORT
    ---------------------------------------------------------------------------
    */

    /**
     * Schedules the given menu to be opened after the
     * {@link qx.ui.menu.Menu#openInterval} configured by the
     * menu instance itself.
     *
     * @param menu {qx.ui.menu.Menu} The menu to schedule for open
     */
    scheduleOpen : function(menu)
    {
      // Cancel close of given menu first
      this.cancelClose(menu);

      // When the menu is already visible
      if (menu.isVisible())
      {
        // Cancel all other open requests
        if (this.__scheduleOpen) {
          this.cancelOpen(this.__scheduleOpen);
        }
      }

      // When the menu is not visible and not scheduled already
      // then schedule it for opening
      else if (this.__scheduleOpen != menu)
      {
        // menu.debug("Schedule open");
        this.__scheduleOpen = menu;
        this.__openTimer.restartWith(menu.getOpenInterval());
      }
    },


    /**
     * Schedules the given menu to be closed after the
     * {@link qx.ui.menu.Menu#closeInterval} configured by the
     * menu instance itself.
     *
     * @param menu {qx.ui.menu.Menu} The menu to schedule for close
     */
    scheduleClose : function(menu)
    {
      // Cancel open of the menu first
      this.cancelOpen(menu);

      // When the menu is already invisible
      if (!menu.isVisible())
      {
        // Cancel all other close requests
        if (this.__scheduleClose) {
          this.cancelClose(this.__scheduleClose);
        }
      }

      // When the menu is visible and not scheduled already
      // then schedule it for closing
      else if (this.__scheduleClose != menu)
      {
        // menu.debug("Schedule close");
        this.__scheduleClose = menu;
        this.__closeTimer.restartWith(menu.getCloseInterval());
      }
    },


    /**
     * When the given menu is scheduled for open this pending
     * request is canceled.
     *
     * @param menu {qx.ui.menu.Menu} The menu to cancel for open
     */
    cancelOpen : function(menu)
    {
      if (this.__scheduleOpen == menu)
      {
        // menu.debug("Cancel open");
        this.__openTimer.stop();
        this.__scheduleOpen = null;
      }
    },


    /**
     * When the given menu is scheduled for close this pending
     * request is canceled.
     *
     * @param menu {qx.ui.menu.Menu} The menu to cancel for close
     */
    cancelClose : function(menu)
    {
      if (this.__scheduleClose == menu)
      {
        // menu.debug("Cancel close");
        this.__closeTimer.stop();
        this.__scheduleClose = null;
      }
    },




    /*
    ---------------------------------------------------------------------------
      TIMER EVENT HANDLERS
    ---------------------------------------------------------------------------
    */

    /**
     * Event listener for a pending open request. Configured to the interval
     * of the current menu to open.
     *
     * @param e {qx.event.type.Event} Interval event
     */
    _onOpenInterval : function(e)
    {
      // Stop timer
      this.__openTimer.stop();

      // Open menu and reset flag
      this.__scheduleOpen.open();
      this.__scheduleOpen = null;
    },


    /**
     * Event listener for a pending close request. Configured to the interval
     * of the current menu to close.
     *
     * @param e {qx.event.type.Event} Interval event
     */
    _onCloseInterval : function(e)
    {
      // Stop timer, reset scheduling flag
      this.__closeTimer.stop();

      // Close menu and reset flag
      this.__scheduleClose.exclude();
      this.__scheduleClose = null;
    },




    /*
    ---------------------------------------------------------------------------
      MOUSE EVENT HANDLERS
    ---------------------------------------------------------------------------
    */

    /**
     * Event handler for mousedown events
     *
     * @param e {qx.event.type.Mouse} mousedown event
     */
    _onMouseDown : function(e)
    {
      var target = e.getTarget();
      target = qx.ui.core.Widget.getWidgetByElement(target, true);

      // If the target is 'null' the click appears on a DOM element witch is not
      // a widget. This happens normally with an inline application, when the user
      // clicks not in the inline application. In this case all all currently
      // open menus should be closed.
      if (target == null) {
        this.hideAll();
        return;
      }

      // If the target is the one which has opened the current menu
      // we ignore the mousedown to let the button process the event
      // further with toggling or ignoring the click.
      if (target.getMenu && target.getMenu() && target.getMenu().isVisible()) {
        return;
      }

      // All clicks not inside a menu will hide all currently open menus
      if (this.__objects.length > 0 && !this._isInMenu(target)) {
        this.hideAll();
      }
    },


    /*
    ---------------------------------------------------------------------------
      KEY EVENT HANDLING
    ---------------------------------------------------------------------------
    */

    /**
     * {Map} Map of all keys working on an active menu selection
     * @lint ignoreReferenceField(__selectionKeys)
     */
    __selectionKeys :
    {
      "Enter" : 1,
      "Space" : 1
    },


    /**
     * {Map} Map of all keys working without a selection
     * @lint ignoreReferenceField(__navigationKeys)
     */
    __navigationKeys :
    {
      "Escape" : 1,
      "Up" : 1,
      "Down" : 1,
      "Left" : 1,
      "Right" : 1
    },


    /**
     * Event handler for all keyup/keydown events. Stops all events
     * when any menu is opened.
     *
     * @param e {qx.event.type.KeySequence} Keyboard event
     * @return {void}
     */
    _onKeyUpDown : function(e)
    {
      var menu = this.getActiveMenu();
      if (!menu) {
        return;
      }

      // Stop for all supported key combos
      var iden = e.getKeyIdentifier();
      if (this.__navigationKeys[iden] || (this.__selectionKeys[iden] && menu.getSelectedButton())) {
        e.stopPropagation();
      }
    },


    /**
     * Event handler for all keypress events. Delegates the event to the more
     * specific methods defined in this class.
     *
     * Currently processes the keys: <code>Up</code>, <code>Down</code>,
     * <code>Left</code>, <code>Right</code> and <code>Enter</code>.
     *
     * @param e {qx.event.type.KeySequence} Keyboard event
     * @return {void}
     */
    _onKeyPress : function(e)
    {
      var menu = this.getActiveMenu();
      if (!menu) {
        return;
      }

      var iden = e.getKeyIdentifier();
      var navigation = this.__navigationKeys[iden];
      var selection = this.__selectionKeys[iden];

      if (navigation)
      {
        switch(iden)
        {
          case "Up":
            this._onKeyPressUp(menu);
            break;

          case "Down":
            this._onKeyPressDown(menu);
            break;

          case "Left":
            this._onKeyPressLeft(menu);
            break;

          case "Right":
            this._onKeyPressRight(menu);
            break;

          case "Escape":
            this.hideAll();
            break;
        }

        e.stopPropagation();
        e.preventDefault();
      }
      else if (selection)
      {
        // Do not process these events when no item is hovered
        var button = menu.getSelectedButton();
        if (button)
        {
          switch(iden)
          {
            case "Enter":
              this._onKeyPressEnter(menu, button, e);
              break;

            case "Space":
              this._onKeyPressSpace(menu, button, e);
              break;
          }

          e.stopPropagation();
          e.preventDefault();
        }
      }
    },


    /**
     * Event handler for <code>Up</code> key
     *
     * @param menu {qx.ui.menu.Menu} The active menu
     * @return {void}
     */
    _onKeyPressUp : function(menu)
    {
      // Query for previous child
      var selectedButton = menu.getSelectedButton();
      var children = menu.getChildren();
      var start = selectedButton ? menu.indexOf(selectedButton)-1 : children.length-1;
      var nextItem = this._getChild(menu, start, -1, true);

      // Reconfigure property
      if (nextItem) {
        menu.setSelectedButton(nextItem);
      } else {
        menu.resetSelectedButton();
      }
    },


    /**
     * Event handler for <code>Down</code> key
     *
     * @param menu {qx.ui.menu.Menu} The active menu
     * @return {void}
     */
    _onKeyPressDown : function(menu)
    {
      // Query for next child
      var selectedButton = menu.getSelectedButton();
      var start = selectedButton ? menu.indexOf(selectedButton)+1 : 0;
      var nextItem = this._getChild(menu, start, 1, true);

      // Reconfigure property
      if (nextItem) {
        menu.setSelectedButton(nextItem);
      } else {
        menu.resetSelectedButton();
      }
    },


    /**
     * Event handler for <code>Left</code> key
     *
     * @param menu {qx.ui.menu.Menu} The active menu
     * @return {void}
     */
    _onKeyPressLeft : function(menu)
    {
      var menuOpener = menu.getOpener();
      if (!menuOpener) {
        return;
      }

      // Back to the "parent" menu
      if (menuOpener instanceof qx.ui.menu.AbstractButton)
      {
        var parentMenu = menuOpener.getLayoutParent();

        parentMenu.resetOpenedButton();
        parentMenu.setSelectedButton(menuOpener);
      }

      // Goto the previous toolbar button
      else if (menuOpener instanceof qx.ui.menubar.Button)
      {
        var buttons = menuOpener.getMenuBar().getMenuButtons();
        var index = buttons.indexOf(menuOpener);

        // This should not happen, definitely!
        if (index === -1) {
          return;
        }

        // Get previous button, fallback to end if first arrived
        var prevButton = null;
        var length =  buttons.length;
        for (var i = 1; i <= length; i++)
        {
          var button = buttons[(index - i + length) % length];
          if(button.isEnabled()) {
            prevButton = button;
            break;
          }
        }

        if (prevButton && prevButton != menuOpener) {
          prevButton.open(true);
        }
      }
    },


    /**
     * Event handler for <code>Right</code> key
     *
     * @param menu {qx.ui.menu.Menu} The active menu
     * @return {void}
     */
    _onKeyPressRight : function(menu)
    {
      var selectedButton = menu.getSelectedButton();

      // Open sub-menu of hovered item and select first child
      if (selectedButton)
      {
        var subMenu = selectedButton.getMenu();

        if (subMenu)
        {
          // Open previously hovered item
          menu.setOpenedButton(selectedButton);

          // Hover first item in new submenu
          var first = this._getChild(subMenu, 0, 1);
          if (first) {
            subMenu.setSelectedButton(first);
          }

          return;
        }
      }

      // No hover and no open item
      // When first button has a menu, open it, otherwise only hover it
      else if (!menu.getOpenedButton())
      {
        var first = this._getChild(menu, 0, 1);

        if (first)
        {
          menu.setSelectedButton(first);

          if (first.getMenu()) {
            menu.setOpenedButton(first);
          }

          return;
        }
      }

      // Jump to the next toolbar button
      var menuOpener = menu.getOpener();

      // Look up opener hierarchy for menu button
      if (menuOpener instanceof qx.ui.menu.Button && selectedButton)
      {
        // From one inner selected button try to find the top level
        // menu button which has opened the whole menu chain.
        while (menuOpener)
        {
          menuOpener = menuOpener.getLayoutParent();
          if (menuOpener instanceof qx.ui.menu.Menu)
          {
            menuOpener = menuOpener.getOpener();
            if (menuOpener instanceof qx.ui.menubar.Button) {
              break;
            }
          }
          else
          {
            break;
          }
        }

        if (!menuOpener) {
          return;
        }
      }

      // Ask the toolbar for the next menu button
      if (menuOpener instanceof qx.ui.menubar.Button)
      {
        var buttons = menuOpener.getMenuBar().getMenuButtons();
        var index = buttons.indexOf(menuOpener);

        // This should not happen, definitely!
        if (index === -1) {
          return;
        }

        // Get next button, fallback to first if end arrived
        var nextButton = null;
        var length =  buttons.length;
        for (var i = 1; i <= length; i++)
        {
          var button = buttons[(index + i) % length];
          if(button.isEnabled()) {
            nextButton = button;
            break;
          }
        }

        if (nextButton && nextButton != menuOpener) {
          nextButton.open(true);
        }
      }
    },


    /**
     * Event handler for <code>Enter</code> key
     *
     * @param menu {qx.ui.menu.Menu} The active menu
     * @param button {qx.ui.menu.AbstractButton} The selected button
     * @param e {qx.event.type.KeySequence} The keypress event
     * @return {void}
     */
    _onKeyPressEnter : function(menu, button, e)
    {
      // Route keypress event to the selected button
      if (button.hasListener("keypress"))
      {
        // Clone and reconfigure event
        var clone = e.clone();
        clone.setBubbles(false);
        clone.setTarget(button);

        // Finally dispatch the clone
        button.dispatchEvent(clone);
      }

      // Hide all open menus
      this.hideAll();
    },


    /**
     * Event handler for <code>Space</code> key
     *
     * @param menu {qx.ui.menu.Menu} The active menu
     * @param button {qx.ui.menu.AbstractButton} The selected button
     * @param e {qx.event.type.KeySequence} The keypress event
     * @return {void}
     */
    _onKeyPressSpace : function(menu, button, e)
    {
      // Route keypress event to the selected button
      if (button.hasListener("keypress"))
      {
        // Clone and reconfigure event
        var clone = e.clone();
        clone.setBubbles(false);
        clone.setTarget(button);

        // Finally dispatch the clone
        button.dispatchEvent(clone);
      }
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function()
  {
    var Registration = qx.event.Registration;
    var el = document.body;

    // React on mousedown/mouseup events
    Registration.removeListener(window.document.documentElement, "mousedown", this._onMouseDown, this, true);

    // React on keypress events
    Registration.removeListener(el, "keydown", this._onKeyUpDown, this, true);
    Registration.removeListener(el, "keyup", this._onKeyUpDown, this, true);
    Registration.removeListener(el, "keypress", this._onKeyPress, this, true);

    this._disposeObjects("__openTimer", "__closeTimer");
    this._disposeArray("__objects");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The menu is a popup like control which supports buttons. It comes
 * with full keyboard navigation and an improved timeout based mouse
 * control behavior.
 *
 * This class is the container for all derived instances of
 * {@link qx.ui.menu.AbstractButton}.
 *
 * @childControl slidebar {qx.ui.menu.MenuSlideBar} shows a slidebar to easily navigate inside the menu (if too little space is left)
 */
qx.Class.define("qx.ui.menu.Menu",
{
  extend : qx.ui.core.Widget,

  include : [
    qx.ui.core.MPlacement,
    qx.ui.core.MRemoteChildrenHandling
  ],


  construct : function()
  {
    this.base(arguments);

    // Use hard coded layout
    this._setLayout(new qx.ui.menu.Layout);

    // Automatically add to application's root
    var root = this.getApplicationRoot();
    root.add(this);

    // Register mouse listeners
    this.addListener("mouseover", this._onMouseOver);
    this.addListener("mouseout", this._onMouseOut);

    // add resize listener
    this.addListener("resize", this._onResize, this);
    root.addListener("resize", this._onResize, this);

    this._blocker = new qx.ui.core.Blocker(root);

    // Initialize properties
    this.initVisibility();
    this.initKeepFocus();
    this.initKeepActive();
  },



  properties :
  {
    /*
    ---------------------------------------------------------------------------
      WIDGET PROPERTIES
    ---------------------------------------------------------------------------
    */

    // overridden
    appearance :
    {
      refine : true,
      init : "menu"
    },

    // overridden
    allowGrowX :
    {
      refine : true,
      init: false
    },

    // overridden
    allowGrowY :
    {
      refine : true,
      init: false
    },

    // overridden
    visibility :
    {
      refine : true,
      init : "excluded"
    },

    // overridden
    keepFocus :
    {
      refine : true,
      init : true
    },

    // overridden
    keepActive :
    {
      refine : true,
      init : true
    },


    /*
    ---------------------------------------------------------------------------
      STYLE OPTIONS
    ---------------------------------------------------------------------------
    */

    /** The spacing between each cell of the menu buttons */
    spacingX :
    {
      check : "Integer",
      apply : "_applySpacingX",
      init : 0,
      themeable : true
    },

    /** The spacing between each menu button */
    spacingY :
    {
      check : "Integer",
      apply : "_applySpacingY",
      init : 0,
      themeable : true
    },

    /**
    * Default icon column width if no icons are rendered.
    * This property is ignored as soon as an icon is present.
    */
    iconColumnWidth :
    {
      check : "Integer",
      init : 0,
      themeable : true,
      apply : "_applyIconColumnWidth"
    },

    /** Default arrow column width if no sub menus are rendered */
    arrowColumnWidth :
    {
      check : "Integer",
      init : 0,
      themeable : true,
      apply : "_applyArrowColumnWidth"
    },

    /**
     * Color of the blocker
     */
    blockerColor :
    {
      check : "Color",
      init : null,
      nullable: true,
      apply : "_applyBlockerColor",
      themeable: true
    },

    /**
     * Opacity of the blocker
     */
    blockerOpacity :
    {
      check : "Number",
      init : 1,
      apply : "_applyBlockerOpacity",
      themeable: true
    },


    /*
    ---------------------------------------------------------------------------
      FUNCTIONALITY PROPERTIES
    ---------------------------------------------------------------------------
    */

    /** The currently selected button */
    selectedButton :
    {
      check : "qx.ui.core.Widget",
      nullable : true,
      apply : "_applySelectedButton"
    },

    /** The currently opened button (sub menu is visible) */
    openedButton :
    {
      check : "qx.ui.core.Widget",
      nullable : true,
      apply : "_applyOpenedButton"
    },

    /** Widget that opened the menu */
    opener :
    {
      check : "qx.ui.core.Widget",
      nullable : true
    },




    /*
    ---------------------------------------------------------------------------
      BEHAVIOR PROPERTIES
    ---------------------------------------------------------------------------
    */

    /** Interval in ms after which sub menus should be opened */
    openInterval :
    {
      check : "Integer",
      themeable : true,
      init : 250,
      apply : "_applyOpenInterval"
    },

    /** Interval in ms after which sub menus should be closed  */
    closeInterval :
    {
      check : "Integer",
      themeable : true,
      init : 250,
      apply : "_applyCloseInterval"
    },

    /** Blocks the background if value is <code>true<code> */
    blockBackground :
    {
      check : "Boolean",
      themeable : true,
      init : false
    }
  },



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {

    __scheduledOpen : null,
    __onAfterSlideBarAdd : null,

    /** {qx.ui.core.Blocker} blocker for background blocking */
    _blocker : null,

    /*
    ---------------------------------------------------------------------------
      PUBLIC API
    ---------------------------------------------------------------------------
    */

    /**
     * Opens the menu and configures the opener
     */
    open : function()
    {
      if (this.getOpener() != null)
      {
        this.placeToWidget(this.getOpener());
        this.__updateSlideBar();
        this.show();

        this._placementTarget = this.getOpener();
      } else {
        this.warn("The menu instance needs a configured 'opener' widget!");
      }
    },


    /**
     * Opens the menu at the mouse cursor position
     *
     * @param e {qx.event.type.Mouse}  Mouse event to align to
     */
    openAtMouse : function(e)
    {
      this.placeToMouse(e);
      this.__updateSlideBar();
      this.show();

      this._placementTarget = {
        left: e.getDocumentLeft(),
        top: e.getDocumentTop()
      };
    },


    /**
     * Opens the menu in relation to the given point
     *
     * @param point {Map} Coordinate of any point with the keys <code>left</code>
     *   and <code>top</code>.
     */
    openAtPoint : function(point)
    {
      this.placeToPoint(point);
      this.__updateSlideBar();
      this.show();

      this._placementTarget = point;
    },


    /**
     * Convenience method to add a separator to the menu
     */
    addSeparator : function() {
      this.add(new qx.ui.menu.Separator);
    },


    /**
     * Returns the column sizes detected during the pre-layout phase
     *
     * @return {Array} List of all column widths
     */
    getColumnSizes : function() {
      return this._getMenuLayout().getColumnSizes();
    },


    /**
     * Return all selectable menu items.
     *
     * @return {qx.ui.core.Widget[]} selectable widgets
     */
    getSelectables : function() {
      var result = [];
      var children = this.getChildren();

      for (var i = 0; i < children.length; i++)
      {
        if (children[i].isEnabled()) {
          result.push(children[i]);
        }
      }

      return result;
    },


    /*
    ---------------------------------------------------------------------------
      PROPERTY APPLY ROUTINES
    ---------------------------------------------------------------------------
    */

    // property apply
    _applyIconColumnWidth : function(value, old) {
      this._getMenuLayout().setIconColumnWidth(value);
    },


    // property apply
    _applyArrowColumnWidth : function(value, old) {
      this._getMenuLayout().setArrowColumnWidth(value);
    },


    // property apply
    _applySpacingX : function(value, old) {
      this._getMenuLayout().setColumnSpacing(value);
    },


    // property apply
    _applySpacingY : function(value, old) {
      this._getMenuLayout().setSpacing(value);
    },


    // overridden
    _applyVisibility : function(value, old)
    {
      this.base(arguments, value, old);

      var mgr = qx.ui.menu.Manager.getInstance();

      if (value === "visible")
      {
        // Register to manager (zIndex handling etc.)
        mgr.add(this);

        // Mark opened in parent menu
        var parentMenu = this.getParentMenu();
        if (parentMenu) {
          parentMenu.setOpenedButton(this.getOpener());
        }
      }
      else if (old === "visible")
      {
        // Deregister from manager (zIndex handling etc.)
        mgr.remove(this);

        // Unmark opened in parent menu
        var parentMenu = this.getParentMenu();
        if (parentMenu && parentMenu.getOpenedButton() == this.getOpener()) {
          parentMenu.resetOpenedButton();
        }

        // Clear properties
        this.resetOpenedButton();
        this.resetSelectedButton();
      }

      this.__updateBlockerVisibility();
    },


    /**
     * Updates the blocker's visibility
     */
    __updateBlockerVisibility : function()
    {
      if (this.isVisible())
      {
        if (this.getBlockBackground()) {
          var zIndex = this.getZIndex();
          this._blocker.blockContent(zIndex - 1);
        }
      }
      else
      {
        if (this._blocker.isContentBlocked()) {
          this._blocker.unblockContent();
        }
      }
    },


    /**
     * Get the parent menu. Returns <code>null</code> if the menu doesn't have a
     * parent menu.
     *
     * @return {Menu|null} The parent menu.
     */
    getParentMenu : function()
    {
      var widget = this.getOpener();
      if (!widget || !(widget instanceof qx.ui.menu.AbstractButton)) {
        return null;
      }

      if (widget && widget.getContextMenu() === this) {
        return null;
      }

      while (widget && !(widget instanceof qx.ui.menu.Menu)) {
        widget = widget.getLayoutParent();
      }
      return widget;
    },


    // property apply
    _applySelectedButton : function(value, old)
    {
      if (old) {
        old.removeState("selected");
      }

      if (value) {
        value.addState("selected");
      }
    },


    // property apply
    _applyOpenedButton : function(value, old)
    {
      if (old && old.getMenu()) {
        old.getMenu().exclude();
      }

      if (value) {
        value.getMenu().open();
      }
    },


    // property apply
    _applyBlockerColor : function(value, old) {
      this._blocker.setColor(value);
    },


    // property apply
    _applyBlockerOpacity : function(value, old) {
      this._blocker.setOpacity(value);
    },


    /*
    ---------------------------------------------------------------------------
    SCROLLING SUPPORT
    ---------------------------------------------------------------------------
    */

    // overridden
    getChildrenContainer : function() {
      return this.getChildControl("slidebar", true) || this;
    },


    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
        case "slidebar":
          var control = new qx.ui.menu.MenuSlideBar();

          var layout = this._getLayout();
          this._setLayout(new qx.ui.layout.Grow());

          var slidebarLayout = control.getLayout();
          control.setLayout(layout);
          slidebarLayout.dispose();

          var children = qx.lang.Array.clone(this.getChildren());
          for (var i=0; i<children.length; i++) {
            control.add(children[i]);
          }

          this.removeListener("resize", this._onResize, this);
          control.getChildrenContainer().addListener("resize", this._onResize, this);

          this._add(control);

        break;
      }

      return control || this.base(arguments, id);
    },


    /**
     * Get the menu layout manager
     *
     * @return {Layout} The menu layout manager
     */
    _getMenuLayout : function()
    {
      if (this.hasChildControl("slidebar")) {
        return this.getChildControl("slidebar").getChildrenContainer().getLayout();
      } else {
        return this._getLayout();
      }
    },


    /**
     * Get the menu bounds
     *
     * @return {Map} The menu bounds
     */
    _getMenuBounds : function()
    {
      if (this.hasChildControl("slidebar")) {
        return this.getChildControl("slidebar").getChildrenContainer().getBounds();
      } else {
        return this.getBounds();
      }
    },


    /**
     * Computes the size of the menu. This method is used by the
     * {@link qx.ui.core.MPlacement} mixin.
     */
    _computePlacementSize : function() {
      return this._getMenuBounds();
    },


    /**
     * Updates the visibility of the slidebar based on the menu's current size
     * and position.
     */
    __updateSlideBar : function()
    {
      var menuBounds = this._getMenuBounds();
      if (!menuBounds)
      {
        this.addListenerOnce("resize", this.__updateSlideBar, this)
        return;
      }

      var rootHeight = this.getLayoutParent().getBounds().height;
      var top = this.getLayoutProperties().top;
      var left = this.getLayoutProperties().left;

      // Adding the slidebar must be deferred because this call can happen
      // during the layout flush, which make it impossible to move existing
      // layout to the slidebar
      if (top < 0)
      {
        this._assertSlideBar(function() {
          this.setHeight(menuBounds.height + top);
          this.moveTo(left, 0);
        });
      }
      else if (top + menuBounds.height > rootHeight)
      {
        this._assertSlideBar(function() {
          this.setHeight(rootHeight - top);
        });
      }
      else
      {
        this.setHeight(null);
      }
    },


    /**
     * Schedules the addition of the slidebar and calls the given callback
     * after the slidebar has been added.
     *
     * @param callback {Function} the callback to call
     */
    _assertSlideBar : function(callback)
    {
      if (this.hasChildControl("slidebar")) {
        return callback.call(this);
      }

      this.__onAfterSlideBarAdd = callback;
      qx.ui.core.queue.Widget.add(this);
    },


    // overridden
    syncWidget : function()
    {
      this.getChildControl("slidebar");
      if (this.__onAfterSlideBarAdd)
      {
        this.__onAfterSlideBarAdd.call(this);
        delete this.__onAfterSlideBarAdd;
      }
    },


    /*
    ---------------------------------------------------------------------------
      EVENT HANDLING
    ---------------------------------------------------------------------------
    */

    /**
     * Update position if the menu or the root is resized
     */
    _onResize : function()
    {
      if (this.isVisible())
      {
        var target = this._placementTarget;
        if (!target) {
          return
        } else if (target instanceof qx.ui.core.Widget) {
          this.placeToWidget(target);
        } else if (target.top !== undefined) {
          this.placeToPoint(target);
        } else {
          throw new Error("Unknown target: " + target);
        }
        this.__updateSlideBar();
      }
    },


    /**
     * Event listener for mouseover event.
     *
     * @param e {qx.event.type.Mouse} mouseover event
     * @return {void}
     */
    _onMouseOver : function(e)
    {
      // Cache manager
      var mgr = qx.ui.menu.Manager.getInstance();

      // Be sure this menu is kept
      mgr.cancelClose(this);

      // Change selection
      var target = e.getTarget();
      if (target.isEnabled() && target instanceof qx.ui.menu.AbstractButton)
      {
        // Select button directly
        this.setSelectedButton(target);

        var subMenu = target.getMenu && target.getMenu();
        if (subMenu)
        {
          subMenu.setOpener(target);

          // Finally schedule for opening
          mgr.scheduleOpen(subMenu);

          // Remember scheduled menu for opening
          this.__scheduledOpen = subMenu;
        }
        else
        {
          var opened = this.getOpenedButton();
          if (opened) {
            mgr.scheduleClose(opened.getMenu());
          }

          if (this.__scheduledOpen)
          {
            mgr.cancelOpen(this.__scheduledOpen);
            this.__scheduledOpen = null;
          }
        }
      }
      else if (!this.getOpenedButton())
      {
        // When no button is opened reset the selection
        // Otherwise keep it
        this.resetSelectedButton();
      }
    },


    /**
     * Event listener for mouseout event.
     *
     * @param e {qx.event.type.Mouse} mouseout event
     * @return {void}
     */
    _onMouseOut : function(e)
    {
      // Cache manager
      var mgr = qx.ui.menu.Manager.getInstance();

      // Detect whether the related target is out of the menu
      if (!qx.ui.core.Widget.contains(this, e.getRelatedTarget()))
      {
        // Update selected property
        // Force it to the open sub menu in cases where that is opened
        // Otherwise reset it. Menus which are left by the cursor should
        // not show any selection.
        var opened = this.getOpenedButton();
        opened ? this.setSelectedButton(opened) : this.resetSelectedButton();

        // Cancel a pending close request for the currently
        // opened sub menu
        if (opened) {
          mgr.cancelClose(opened.getMenu());
        }

        // When leaving this menu to the outside, stop
        // all pending requests to open any other sub menu
        if (this.__scheduledOpen) {
          mgr.cancelOpen(this.__scheduledOpen);
        }
      }
    }
  },

  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function()
  {
    if (!qx.core.ObjectRegistry.inShutDown) {
      qx.ui.menu.Manager.getInstance().remove(this);
    }

    this.getApplicationRoot().removeListener("resize", this._onResize, this);
    this._placementTarget = null;
    this._disposeObjects("_blocker");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * Layouter used by the qooxdoo menu's to render their buttons
 *
 * @internal
 */
qx.Class.define("qx.ui.menu.Layout",
{
  extend : qx.ui.layout.VBox,


  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** Spacing between each cell on the menu buttons */
    columnSpacing :
    {
      check : "Integer",
      init : 0,
      apply : "_applyLayoutChange"
    },

    /**
     * Whether a column and which column should automatically span
     * when the following cell is empty. Spanning may be disabled
     * through setting this property to <code>null</code>.
     */
    spanColumn :
    {
      check : "Integer",
      init : 1,
      nullable : true,
      apply : "_applyLayoutChange"
    },

    /** Default icon column width if no icons are rendered */
    iconColumnWidth :
    {
      check : "Integer",
      init : 0,
      themeable : true,
      apply : "_applyLayoutChange"
    },

    /** Default arrow column width if no sub menus are rendered */
    arrowColumnWidth :
    {
      check : "Integer",
      init : 0,
      themeable : true,
      apply : "_applyLayoutChange"
    }
  },



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __columnSizes : null,

    /*
    ---------------------------------------------------------------------------
      LAYOUT INTERFACE
    ---------------------------------------------------------------------------
    */

    // overridden
    _computeSizeHint : function()
    {
      var children = this._getLayoutChildren();
      var child, sizes, spacing;

      var spanColumn = this.getSpanColumn();
      var columnSizes = this.__columnSizes = [0, 0, 0, 0];
      var columnSpacing = this.getColumnSpacing();
      var spanColumnWidth = 0;
      var maxInset = 0;

      // Compute column sizes and insets
      for (var i=0, l=children.length; i<l; i++)
      {
        child = children[i];

        if (child.isAnonymous()) {
          continue;
        }

        sizes = child.getChildrenSizes();

        for (var column=0; column<sizes.length; column++)
        {
          if (spanColumn != null && column == spanColumn && sizes[spanColumn+1] == 0) {
            spanColumnWidth = Math.max(spanColumnWidth, sizes[column]);
          } else {
            columnSizes[column] = Math.max(columnSizes[column], sizes[column])
          }
        }

        var insets = children[i].getInsets();
        maxInset = Math.max(maxInset, insets.left + insets.right);
      }

      // Fix label column width is cases where the maximum button with no shortcut
      // is larger than the maximum button with a shortcut
      if (spanColumn != null && columnSizes[spanColumn] + columnSpacing + columnSizes[spanColumn+1] < spanColumnWidth) {
        columnSizes[spanColumn] = spanColumnWidth - columnSizes[spanColumn+1] - columnSpacing;
      }

      // When merging the cells for label and shortcut
      // ignore the spacing between them
      if (spanColumnWidth == 0) {
        spacing = columnSpacing * 2;
      } else {
        spacing = columnSpacing * 3;
      }

      // Fix zero size icon column
      if (columnSizes[0] == 0) {
        columnSizes[0] = this.getIconColumnWidth();
      }

      // Fix zero size arrow column
      if (columnSizes[3] == 0) {
        columnSizes[3] = this.getArrowColumnWidth();
      }

      var height = this.base(arguments).height;

      // Build hint
      return {
        minHeight: height,
        height : height,
        width : qx.lang.Array.sum(columnSizes) + maxInset + spacing
      };
    },



    /*
    ---------------------------------------------------------------------------
      CUSTOM ADDONS
    ---------------------------------------------------------------------------
    */

    /**
     * Returns the column sizes detected during the pre-layout phase
     *
     * @return {Array} List of all column widths
     */
    getColumnSizes : function() {
      return this.__columnSizes || null;
    }
  },

  /*
   *****************************************************************************
      DESTRUCT
   *****************************************************************************
   */

  destruct : function() {
    this.__columnSizes = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * This widget draws a separator line between two instances of
 * {@link qx.ui.menu.AbstractButton} and is inserted into the
 * {@link qx.ui.menu.Menu}.
 *
 * For convenience reasons there is also
 * a method {@link qx.ui.menu.Menu#addSeparator} to append instances
 * of this class to the menu.
 */
qx.Class.define("qx.ui.menu.Separator",
{
  extend : qx.ui.core.Widget,




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    // overridden
    appearance :
    {
      refine : true,
      init : "menu-separator"
    },

    // overridden
    anonymous :
    {
      refine : true,
      init : true
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The abstract menu button class is used for all type of menu content
 * for example normal buttons, checkboxes or radiobuttons.
 *
 * @childControl icon {qx.ui.basic.Image} icon of the button
 * @childControl label {qx.ui.basic.Label} label of the button
 * @childControl shortcut {qx.ui.basic.Label} shows if specified the shortcut
 * @childControl arrow {qx.ui.basic.Image} shows the arrow to show an additional widget (e.g. popup or submenu)
 */
qx.Class.define("qx.ui.menu.AbstractButton",
{
  extend : qx.ui.core.Widget,
  include : [qx.ui.core.MExecutable],
  implement : [qx.ui.form.IExecutable],
  type : "abstract",


  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    // Use hard coded layout
    this._setLayout(new qx.ui.menu.ButtonLayout);

    // Add listeners
    this.addListener("mouseup", this._onMouseUp);
    this.addListener("keypress", this._onKeyPress);

    // Add command listener
    this.addListener("changeCommand", this._onChangeCommand, this);
  },


  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    // overridden
    blockToolTip :
    {
      refine : true,
      init : true
    },

    /** The label text of the button */
    label :
    {
      check : "String",
      apply : "_applyLabel",
      nullable : true
    },

    /** Whether a sub menu should be shown and which one */
    menu :
    {
      check : "qx.ui.menu.Menu",
      apply : "_applyMenu",
      nullable : true,
      dereference : true
    },

    /** The icon to use */
    icon :
    {
      check : "String",
      apply : "_applyIcon",
      themeable : true,
      nullable : true
    }
  },


  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      WIDGET API
    ---------------------------------------------------------------------------
    */

    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
        case "icon":
          control = new qx.ui.basic.Image;
          control.setAnonymous(true);
          this._add(control, {column:0});
          break;

        case "label":
          control = new qx.ui.basic.Label;
          control.setAnonymous(true);
          this._add(control, {column:1});
          break;

        case "shortcut":
          control = new qx.ui.basic.Label;
          control.setAnonymous(true);
          this._add(control, {column:2});
          break;

        case "arrow":
          control = new qx.ui.basic.Image;
          control.setAnonymous(true);
          this._add(control, {column:3});
          break;
      }

      return control || this.base(arguments, id);
    },


    // overridden
    /**
     * @lint ignoreReferenceField(_forwardStates)
     */
    _forwardStates : {
      selected : 1
    },




    /*
    ---------------------------------------------------------------------------
      LAYOUT UTILS
    ---------------------------------------------------------------------------
    */

    /**
     * Returns the dimensions of all children
     *
     * @return {Array} Preferred width of each child
     */
    getChildrenSizes : function()
    {
      var iconWidth=0, labelWidth=0, shortcutWidth=0, arrowWidth=0;

      if (this._isChildControlVisible("icon"))
      {
        var icon = this.getChildControl("icon");
        iconWidth = icon.getMarginLeft() + icon.getSizeHint().width + icon.getMarginRight();
      }

      if (this._isChildControlVisible("label"))
      {
        var label = this.getChildControl("label");
        labelWidth = label.getMarginLeft() + label.getSizeHint().width + label.getMarginRight();
      }

      if (this._isChildControlVisible("shortcut"))
      {
        var shortcut = this.getChildControl("shortcut");
        shortcutWidth = shortcut.getMarginLeft() + shortcut.getSizeHint().width + shortcut.getMarginRight();
      }

      if (this._isChildControlVisible("arrow"))
      {
        var arrow = this.getChildControl("arrow");
        arrowWidth = arrow.getMarginLeft() + arrow.getSizeHint().width + arrow.getMarginRight();
      }

      return [ iconWidth, labelWidth, shortcutWidth, arrowWidth ];
    },


    /*
    ---------------------------------------------------------------------------
      EVENT LISTENERS
    ---------------------------------------------------------------------------
    */

    /**
     * Event listener for mouseup event
     *
     * @param e {qx.event.type.Mouse} mouseup event
     */
    _onMouseUp : function(e) {
      // pass
    },


    /**
     * Event listener for mouseup event
     *
     * @param e {qx.event.type.KeySequence} keypress event
     */
    _onKeyPress : function(e) {
      // pass
    },


    /**
     * Event listener for command changes. Updates the text of the shortcut.
     *
     * @param e {qx.event.type.Data} Property change event
     */
    _onChangeCommand : function(e)
    {
      var command = e.getData();

      // do nothing if no command is set
      if (command == null) {
        return;
      }

      if (qx.core.Environment.get("qx.dynlocale"))
      {
        var oldCommand = e.getOldData();
        if (!oldCommand) {
          qx.locale.Manager.getInstance().addListener("changeLocale", this._onChangeLocale, this);
        }
        if (!command) {
          qx.locale.Manager.getInstance().removeListener("changeLocale", this._onChangeLocale, this);
        }
      }

      var cmdString = command != null ? command.toString() : "";
      this.getChildControl("shortcut").setValue(cmdString);
    },


    /**
     * Update command string on locale changes
     */
    _onChangeLocale : qx.core.Environment.select("qx.dynlocale",
    {
      "true" : function(e) {
        var command = this.getCommand();
        if (command != null) {
          this.getChildControl("shortcut").setValue(command.toString());
        }
      },

      "false" : null
    }),


    /*
    ---------------------------------------------------------------------------
      PROPERTY APPLY ROUTINES
    ---------------------------------------------------------------------------
    */

    // property apply
    _applyIcon : function(value, old)
    {
      if (value) {
        this._showChildControl("icon").setSource(value);
      } else {
        this._excludeChildControl("icon");
      }
    },

    // property apply
    _applyLabel : function(value, old)
    {
      if (value) {
        this._showChildControl("label").setValue(value);
      } else {
        this._excludeChildControl("label");
      }
    },

    // property apply
    _applyMenu : function(value, old)
    {
      if (old)
      {
        old.resetOpener();
        old.removeState("submenu");
      }

      if (value)
      {
        this._showChildControl("arrow");

        value.setOpener(this);
        value.addState("submenu");
      }
      else
      {
        this._excludeChildControl("arrow");
      }
    }
  },


  /*
   *****************************************************************************
      DESTRUCTOR
   *****************************************************************************
   */

  destruct : function()
  {
    this.removeListener("changeCommand", this._onChangeCommand, this);

    if (this.getMenu())
    {
      if (!qx.core.ObjectRegistry.inShutDown) {
        this.getMenu().destroy();
      }
    }

    if (qx.core.Environment.get("qx.dynlocale")) {
      qx.locale.Manager.getInstance().removeListener("changeLocale", this._onChangeLocale, this);
    }
  }
});

/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * Layout used for the menu buttons which may contain four elements. A icon,
 * a label, a shortcut text and an arrow (for a sub menu)
 *
 * @internal
 */
qx.Class.define("qx.ui.menu.ButtonLayout",
{
  extend : qx.ui.layout.Abstract,



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    // overridden
    verifyLayoutProperty : qx.core.Environment.select("qx.debug",
    {
      "true" : function(item, name, value) {
        this.assert(name=="column", "The property '"+name+"' is not supported by the MenuButton layout!");
      },

      "false" : null
    }),


    // overridden
    renderLayout : function(availWidth, availHeight)
    {
      var children = this._getLayoutChildren();
      var child;
      var column;

      var columnChildren = [];
      for (var i=0, l=children.length; i<l; i++)
      {
        child = children[i];
        column = child.getLayoutProperties().column;
        columnChildren[column] = child;
      }

      var menu = this.__getMenu(children[0]);

      var columns = menu.getColumnSizes();
      var spacing = menu.getSpacingX();

      // stretch label column
      var neededWidth = qx.lang.Array.sum(columns) + spacing * (columns.length - 1);
      if (neededWidth < availWidth) {
        columns[1] += availWidth - neededWidth;
      }


      var left=0, top=0;
      var Util = qx.ui.layout.Util;

      for (var i=0, l=columns.length; i<l; i++)
      {
        child = columnChildren[i];

        if (child)
        {
          var hint = child.getSizeHint();
          var top = Util.computeVerticalAlignOffset(child.getAlignY()||"middle", hint.height, availHeight, 0, 0);
          var offsetLeft = Util.computeHorizontalAlignOffset(child.getAlignX()||"left", hint.width, columns[i], child.getMarginLeft(), child.getMarginRight());
          child.renderLayout(left + offsetLeft, top, hint.width, hint.height);
        }

        left += columns[i] + spacing;
      }
    },


    /**
     * Get the widget's menu
     *
     * @param widget {qx.ui.core.Widget} the widget to get the menu for
     * @return {qx.ui.menu.Menu} the menu
     */
    __getMenu : function(widget)
    {
      while (!(widget instanceof qx.ui.menu.Menu)) {
        widget = widget.getLayoutParent();
      }
      return widget;
    },


    // overridden
    _computeSizeHint : function()
    {
      var children = this._getLayoutChildren();
      var neededHeight = 0;
      var neededWidth = 0;

      for (var i=0, l=children.length; i<l; i++)
      {
        var hint = children[i].getSizeHint();
        neededWidth += hint.width;
        neededHeight = Math.max(neededHeight, hint.height);
      }

      return {
        width : neededWidth,
        height : neededHeight
      }
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's left-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * Container, which provides scrolling in one dimension (vertical or horizontal).
 *
 * @childControl button-forward {qx.ui.form.RepeatButton} button to step forward
 * @childControl button-backward {qx.ui.form.RepeatButton} button to step backward
 * @childControl content {qx.ui.container.Composite} container to hold the content
 * @childControl scrollpane {qx.ui.core.scroll.ScrollPane} the scroll pane holds the content to enable scrolling
 *
 * *Example*
 *
 * Here is a little example of how to use the widget.
 *
 * <pre class='javascript'>
 *   // create slide bar container
 *   slideBar = new qx.ui.container.SlideBar().set({
 *     width: 300
 *   });
 *
 *   // set layout
 *   slideBar.setLayout(new qx.ui.layout.HBox());
 *
 *   // add some widgets
 *   for (var i=0; i<10; i++)
 *   {
 *     slideBar.add((new qx.ui.core.Widget()).set({
 *       backgroundColor : (i % 2 == 0) ? "red" : "blue",
 *       width : 60
 *     }));
 *   }
 *
 *   this.getRoot().add(slideBar);
 * </pre>
 *
 * This example creates a SlideBar and add some widgets with alternating
 * background colors. Since the content is larger than the container, two
 * scroll buttons at the left and the right edge are shown.
 *
 * *External Documentation*
 *
 * <a href='http://manual.qooxdoo.org/1.4/pages/widget/slidebar.html' target='_blank'>
 * Documentation of this widget in the qooxdoo manual.</a>
 */
qx.Class.define("qx.ui.container.SlideBar",
{
  extend : qx.ui.core.Widget,

  include :
  [
    qx.ui.core.MRemoteChildrenHandling,
    qx.ui.core.MRemoteLayoutHandling
  ],



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param orientation {String?"horizontal"} The slide bar orientation
   */
  construct : function(orientation)
  {
    this.base(arguments);

    var scrollPane = this.getChildControl("scrollpane");
    this._add(scrollPane, {flex: 1});

    if (orientation != null) {
      this.setOrientation(orientation);
    } else {
      this.initOrientation();
    }

    this.addListener("mousewheel", this._onMouseWheel, this);
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    // overridden
    appearance :
    {
      refine : true,
      init : "slidebar"
    },

    /** Orientation of the bar */
    orientation :
    {
      check : ["horizontal", "vertical"],
      init : "horizontal",
      apply : "_applyOrientation"
    },

    /** The number of pixels to scroll if the buttons are pressed */
    scrollStep :
    {
      check : "Integer",
      init : 15,
      themeable : true
    }
  },





  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      WIDGET API
    ---------------------------------------------------------------------------
    */

    // overridden
    getChildrenContainer : function() {
      return this.getChildControl("content");
    },


    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
        case "button-forward":
          control = new qx.ui.form.RepeatButton;
          control.addListener("execute", this._onExecuteForward, this);
          control.setFocusable(false);
          this._addAt(control, 2);
          break;

        case "button-backward":
          control = new qx.ui.form.RepeatButton;
          control.addListener("execute", this._onExecuteBackward, this);
          control.setFocusable(false);
          this._addAt(control, 0);
          break;

        case "content":
          control = new qx.ui.container.Composite();

          /*
           * Gecko does not update the scroll position after removing an
           * element. So we have to do this by hand.
           */
          if (qx.core.Environment.get("engine.name") == "gecko") {
            control.addListener("removeChildWidget", this._onRemoveChild, this);
          }

          this.getChildControl("scrollpane").add(control);
          break;

        case "scrollpane":
          control = new qx.ui.core.scroll.ScrollPane();
          control.addListener("update", this._onResize, this);
          control.addListener("scrollX", this._onScroll, this);
          control.addListener("scrollY", this._onScroll, this);
          break;
      }

      return control || this.base(arguments, id);
    },

    // overridden
    /**
     * @lint ignoreReferenceField(_forwardStates)
     */
    _forwardStates :
    {
      barLeft : true,
      barTop : true,
      barRight : true,
      barBottom : true
    },

    /*
    ---------------------------------------------------------------------------
      PUBLIC SCROLL API
    ---------------------------------------------------------------------------
    */

    /**
     * Scrolls the element's content by the given amount.
     *
     * @param offset {Integer?0} Amount to scroll
     * @return {void}
     */
    scrollBy : function(offset)
    {
      var pane = this.getChildControl("scrollpane");
      if (this.getOrientation() === "horizontal") {
        pane.scrollByX(offset);
      } else {
        pane.scrollByY(offset);
      }
    },


    /**
     * Scrolls the element's content to the given coordinate
     *
     * @param value {Integer} The position to scroll to.
     * @return {void}
     */
    scrollTo : function(value)
    {
      var pane = this.getChildControl("scrollpane");
      if (this.getOrientation() === "horizontal") {
        pane.scrollToX(value);
      } else {
        pane.scrollToY(value);
      }
    },


    /*
    ---------------------------------------------------------------------------
      PROPERTY APPLY ROUTINES
    ---------------------------------------------------------------------------
    */
    // overridden
    _applyEnabled : function(value, old, name) {
      this.base(arguments, value, old, name);
      this._updateArrowsEnabled();
    },


    // property apply
    _applyOrientation : function(value, old)
    {
      var oldLayouts = [this.getLayout(), this._getLayout()];
      var buttonForward = this.getChildControl("button-forward");
      var buttonBackward = this.getChildControl("button-backward");

      // old can also be null, so we have to check both explicitly to set
      // the states correctly.
      if (old == "vertical")
      {
        buttonForward.removeState("vertical");
        buttonBackward.removeState("vertical");
        buttonForward.addState("horizontal");
        buttonBackward.addState("horizontal");
      }
      else if (old == "horizontal")
      {
        buttonForward.removeState("horizontal");
        buttonBackward.removeState("horizontal");
        buttonForward.addState("vertical");
        buttonBackward.addState("vertical");
      }


      if (value == "horizontal")
      {
        this._setLayout(new qx.ui.layout.HBox());
        this.setLayout(new qx.ui.layout.HBox());
      }
      else
      {
        this._setLayout(new qx.ui.layout.VBox());
        this.setLayout(new qx.ui.layout.VBox());
      }

      if (oldLayouts[0]) {
        oldLayouts[0].dispose();
      }

      if (oldLayouts[1]) {
        oldLayouts[1].dispose();
      }
    },




    /*
    ---------------------------------------------------------------------------
      EVENT LISTENERS
    ---------------------------------------------------------------------------
    */

    /**
     * Scrolls pane on mousewheel events
     *
     * @param e {qx.event.type.Mouse} the mouse event
     */
    _onMouseWheel : function(e)
    {
      var delta = 0;
      if (this.getOrientation() === "horizontal") {
        delta = e.getWheelDelta("x");
      } else {
        delta = e.getWheelDelta("y");
      }
      this.scrollBy(delta * this.getScrollStep());

      // Stop bubbling and native event
      e.stop();
    },


    /**
     * Update arrow enabled state after scrolling
     */
    _onScroll : function() {
      this._updateArrowsEnabled();
    },


    /**
     * Listener for resize event. This event is fired after the
     * first flush of the element which leads to another queuing
     * when the changes modify the visibility of the scroll buttons.
     *
     * @param e {Event} Event object
     * @return {void}
     */
    _onResize : function(e)
    {
      var content = this.getChildControl("scrollpane").getChildren()[0];
      if (!content) {
        return;
      }

      var innerSize = this.getInnerSize();
      var contentSize = content.getBounds();

      var overflow = (this.getOrientation() === "horizontal") ?
        contentSize.width > innerSize.width :
        contentSize.height > innerSize.height;

      if (overflow) {
        this._showArrows()
        this._updateArrowsEnabled();
      } else {
        this._hideArrows();
      }
    },


    /**
     * Scroll handler for left scrolling
     *
     * @return {void}
     */
    _onExecuteBackward : function() {
      this.scrollBy(-this.getScrollStep());
    },


    /**
     * Scroll handler for right scrolling
     *
     * @return {void}
     */
    _onExecuteForward : function() {
      this.scrollBy(this.getScrollStep());
    },


    /**
     * Helper function for Gecko. Modifies the scroll offset when a child is
     * removed.
     */
    _onRemoveChild : function()
    {
      qx.event.Timer.once(
        function() {
          this.scrollBy(this.getChildControl("scrollpane").getScrollX());
        },
        this,
        50
      );
    },


    /*
    ---------------------------------------------------------------------------
      UTILITIES
    ---------------------------------------------------------------------------
    */

    /**
     * Update arrow enabled state
     */
    _updateArrowsEnabled : function()
    {
      // set the disables state directly because we are overriding the
      // inheritance
      if (!this.getEnabled()) {
        this.getChildControl("button-backward").setEnabled(false);
        this.getChildControl("button-forward").setEnabled(false);
        return;
      }

      var pane = this.getChildControl("scrollpane");

      if (this.getOrientation() === "horizontal")
      {
        var position = pane.getScrollX();
        var max = pane.getScrollMaxX();
      }
      else
      {
        var position = pane.getScrollY();
        var max = pane.getScrollMaxY();
      }

      this.getChildControl("button-backward").setEnabled(position > 0);
      this.getChildControl("button-forward").setEnabled(position < max);
    },


    /**
     * Show the arrows (Called from resize event)
     *
     * @return {void}
     */
    _showArrows : function()
    {
      this._showChildControl("button-forward");
      this._showChildControl("button-backward");
    },


    /**
     * Hide the arrows (Called from resize event)
     *
     * @return {void}
     */
    _hideArrows : function()
    {
      this._excludeChildControl("button-forward");
      this._excludeChildControl("button-backward");

      this.scrollTo(0);
    }
  }

});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2009 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The MenuSlideBar is used to scroll menus if they don't fit on the screen.
 *
 * @childControl button-forward {qx.ui.form.HoverButton} scrolls forward of hovered
 * @childControl button-backward {qx.ui.form.HoverButton} scrolls backward if hovered
 *
 * @internal
 */
qx.Class.define("qx.ui.menu.MenuSlideBar",
{
  extend : qx.ui.container.SlideBar,

  construct : function()
  {
    this.base(arguments, "vertical");
  },

  properties :
  {
    appearance :
    {
      refine : true,
      init : "menu-slidebar"
    }
  },

  members :
  {
    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
        case "button-forward":
          control = new qx.ui.form.HoverButton();
          control.addListener("execute", this._onExecuteForward, this);
          this._addAt(control, 2);
          break;

        case "button-backward":
          control = new qx.ui.form.HoverButton();
          control.addListener("execute", this._onExecuteBackward, this);
          this._addAt(control, 0);
          break;
      }

      return control || this.base(arguments, id);
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2009 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The HoverButton is an {@link qx.ui.basic.Atom}, which fires repeatedly
 * execute events while the mouse is over the widget.
 *
 * The rate at which the execute event is fired accelerates is the mouse keeps
 * inside of the widget. The initial delay and the interval time can be set using
 * the properties {@link #firstInterval} and {@link #interval}. The
 * {@link #execute} events will be fired in a shorter amount of time if the mouse
 * remains over the widget, until the min {@link #minTimer} is reached.
 * The {@link #timerDecrease} property sets the amount of milliseconds which will
 * decreased after every firing.
 *
 * *Example*
 *
 * Here is a little example of how to use the widget.
 *
 * <pre class='javascript'>
 *   var button = new qx.ui.form.HoverButton("Hello World");
 *
 *   button.addListener("execute", function(e) {
 *     alert("Button is hovered");
 *   }, this);
 *
 *   this.getRoot.add(button);
 * </pre>
 *
 * This example creates a button with the label "Hello World" and attaches an
 * event listener to the {@link #execute} event.
 *
 * *External Documentation*
 *
 * <a href='http://manual.qooxdoo.org/1.4/pages/widget/hoverbutton.html' target='_blank'>
 * Documentation of this widget in the qooxdoo manual.</a>
 */
qx.Class.define("qx.ui.form.HoverButton",
{
  extend : qx.ui.basic.Atom,
  include : [qx.ui.core.MExecutable],
  implement : [qx.ui.form.IExecutable],

  /**
   * @param label {String} Label to use
   * @param icon {String?null} Icon to use
   */
  construct : function(label, icon)
  {
    this.base(arguments, label, icon);

    this.addListener("mouseover", this._onMouseOver, this);
    this.addListener("mouseout", this._onMouseOut, this);

    this.__timer = new qx.event.AcceleratingTimer();
    this.__timer.addListener("interval", this._onInterval, this);
  },


  properties :
  {
    // overridden
    appearance :
    {
      refine : true,
      init : "hover-button"
    },

    /**
     * Interval used after the first run of the timer. Usually a smaller value
     * than the "firstInterval" property value to get a faster reaction.
     */
    interval :
    {
      check : "Integer",
      init  : 80
    },

    /**
     * Interval used for the first run of the timer. Usually a greater value
     * than the "interval" property value to a little delayed reaction at the first
     * time.
     */
    firstInterval :
    {
      check : "Integer",
      init  : 200
    },

    /** This configures the minimum value for the timer interval. */
    minTimer :
    {
      check : "Integer",
      init  : 20
    },

    /** Decrease of the timer on each interval (for the next interval) until minTimer reached. */
    timerDecrease :
    {
      check : "Integer",
      init  : 2
    }
  },


  members :
  {
    __timer : null,


    /**
     * Start timer on mouse over
     *
     * @param e {qx.event.type.Mouse} The mouse event
     */
    _onMouseOver : function(e)
    {
      if (!this.isEnabled() || e.getTarget() !== this) {
        return;
      }

      this.__timer.set({
        interval: this.getInterval(),
        firstInterval: this.getFirstInterval(),
        minimum: this.getMinTimer(),
        decrease: this.getTimerDecrease()
      }).start();

      this.addState("hovered");
    },


    /**
     * Stop timer on mouse out
     *
     * @param e {qx.event.type.Mouse} The mouse event
     */
    _onMouseOut : function(e)
    {
      this.__timer.stop();
      this.removeState("hovered");

      if (!this.isEnabled() || e.getTarget() !== this) {
        return;
      }
    },


    /**
     * Fire execute event on timer interval event
     */
    _onInterval : function()
    {
      if (this.isEnabled())
      {
        this.execute();
      } else {
        this.__timer.stop();
      }
    }
  },


  destruct : function() {
    this._disposeObjects("__timer");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)

************************************************************************ */

/**
 * A menubar button
 */
qx.Class.define("qx.ui.menubar.Button",
{
  extend : qx.ui.form.MenuButton,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function(label, icon, menu)
  {
    this.base(arguments, label, icon, menu);

    this.removeListener("keydown", this._onKeyDown);
    this.removeListener("keyup", this._onKeyUp);
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    appearance :
    {
      refine : true,
      init : "menubar-button"
    },

    show :
    {
      refine : true,
      init : "inherit"
    },

    focusable :
    {
      refine : true,
      init : false
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      HELPER METHODS
    ---------------------------------------------------------------------------
    */

    /**
     * Inspects the parent chain to find the MenuBar
     *
     * @return {qx.ui.menubar.MenuBar} MenuBar instance or <code>null</code>.
     */
    getMenuBar : function()
    {
      var parent = this;
      while (parent)
      {
        /* this method is also used by toolbar.MenuButton, so we need to check
           for a ToolBar instance. */
        if (parent instanceof qx.ui.toolbar.ToolBar) {
          return parent;
        }

        parent = parent.getLayoutParent();
      }

      return null;
    },


    // overridden
    open : function(selectFirst) {
      this.base(arguments, selectFirst);

      var menubar = this.getMenuBar();
      menubar._setAllowMenuOpenHover(true);
    },


    /*
    ---------------------------------------------------------------------------
      EVENT LISTENERS
    ---------------------------------------------------------------------------
    */

    /**
     * Listener for visibility property changes of the attached menu
     *
     * @param e {qx.event.type.Data} Property change event
     */
    _onMenuChange : function(e)
    {
      var menu = this.getMenu();
      var menubar = this.getMenuBar();

      if (menu.isVisible())
      {
        this.addState("pressed");

        // Sync with open menu property
        if (menubar) {
          menubar.setOpenMenu(menu);
        }
      }
      else
      {
        this.removeState("pressed");

        // Sync with open menu property
        if (menubar && menubar.getOpenMenu() == menu) {
          menubar.resetOpenMenu();
          menubar._setAllowMenuOpenHover(false);
        }
      }
    },

    // overridden
    _onMouseUp : function(e)
    {
      this.base(arguments, e);

      // Set state 'pressed' to visualize that the menu is open.
      var menu = this.getMenu();
      if (menu && menu.isVisible() && !this.hasState("pressed")) {
        this.addState("pressed");
      }
    },

    /**
     * Event listener for mouseover event
     *
     * @param e {qx.event.type.Mouse} mouseover event object
     */
    _onMouseOver : function(e)
    {
      // Add hovered state
      this.addState("hovered");

      // Open submenu
      if (this.getMenu())
      {
        var menubar = this.getMenuBar();

        if (menubar._isAllowMenuOpenHover())
        {
          // Hide all open menus
          qx.ui.menu.Manager.getInstance().hideAll();

          // Set it again, because hideAll remove it.
          menubar._setAllowMenuOpenHover(true);

          // Then show the attached menu
          if (this.isEnabled()) {
            this.open();
          }
        }
      }
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)
     * Martin Wittemann (martinwittemann)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * The Toolbar class is the main part of the toolbar widget.
 *
 * It can handle added {@link Button}s, {@link CheckBox}es, {@link RadioButton}s
 * and {@link Separator}s in its {@link #add} method. The {@link #addSpacer} method
 * adds a spacer at the current toolbar position. This means that the widgets
 * added after the method call of {@link #addSpacer} are aligned to the right of
 * the toolbar.
 *
 * For more details on the documentation of the toolbar widget, take a look at the
 * documentation of the {@link qx.ui.toolbar}-Package.
 */
qx.Class.define("qx.ui.toolbar.ToolBar",
{
  extend : qx.ui.core.Widget,
  include : qx.ui.core.MChildrenHandling,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    // add needed layout
    this._setLayout(new qx.ui.layout.HBox());

    // initialize the overflow handling
    this.__removedItems = [];
    this.__removePriority = [];
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** Appearance of the widget */
    appearance :
    {
      refine : true,
      init : "toolbar"
    },

    /** Holds the currently open menu (when the toolbar is used for menus) */
    openMenu :
    {
      check : "qx.ui.menu.Menu",
      event : "changeOpenMenu",
      nullable : true
    },

    /** Whether icons, labels, both or none should be shown. */
    show :
    {
      init : "both",
      check : [ "both", "label", "icon" ],
      inheritable : true,
      event : "changeShow"
    },

    /** The spacing between every child of the toolbar */
    spacing :
    {
      nullable : true,
      check : "Integer",
      themeable : true,
      apply : "_applySpacing"
    },

    /**
     * Widget which will be shown if at least one toolbar item is hidden.
     * Keep in mind to add this widget to the toolbar before you set it as
     * indicator!
     */
    overflowIndicator :
    {
      check : "qx.ui.core.Widget",
      nullable : true,
      apply : "_applyOverflowIndicator"
    },

    /** Enables the overflow handling which automatically removes items.*/
    overflowHandling :
    {
      init : false,
      check : "Boolean",
      apply : "_applyOverflowHandling"
    }
  },



  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events :
  {
    /** Fired if an item will be hidden by the {@link #overflowHandling}.*/
    "hideItem" : "qx.event.type.Data",

    /** Fired if an item will be show by the {@link #overflowHandling}.*/
    "showItem" : "qx.event.type.Data"
  },


  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      OVERFLOW HANDLING
    ---------------------------------------------------------------------------
    */

    __removedItems : null,
    __removePriority : null,


    // overridden
    _computeSizeHint : function()
    {
      // get the original hint
      var hint = this.base(arguments);
      if (true && this.getOverflowHandling()) {
        var minWidth = 0;
        // if an overflow widget is given, use its width + spacing as min width
        var overflowWidget = this.getOverflowIndicator();
        if (overflowWidget) {
          minWidth = overflowWidget.getSizeHint().width + this.getSpacing();
        }
        // reset the minWidth because we reduce the count of elements
        hint.minWidth = minWidth;
      }
      return hint;
    },


    /**
     * Resize event handler.
     *
     * @param e {qx.event.type.Data} The resize event.
     */
    _onResize : function(e) {
      this._recalculateOverflow(e.getData().width);
    },


    /**
     * Responsible for calculation the overflow based on the available width.
     *
     * @param width {Integer?null} The available width.
     * @param requiredWidth {Integer?null} The required width for the widget
     *   if available.
     */
    _recalculateOverflow : function(width, requiredWidth)
    {
      // do nothing if overflow handling is not enabled
      if (!this.getOverflowHandling()) {
        return;
      }

      // get all required sizes
      requiredWidth = requiredWidth || this.getSizeHint().width;
      var overflowWidget = this.getOverflowIndicator();
      var overflowWidgetWidth = 0;
      if (overflowWidget) {
        overflowWidgetWidth = overflowWidget.getSizeHint().width;
      }

      if (width == undefined && this.getBounds() != null) {
        width = this.getBounds().width;
      }

      // if we still don't have a width, than we are not added to a parrent
      if (width == undefined) {
        // we should ignore it in that case
        return;
      }

      // if we have not enough space
      if (width < requiredWidth) {
        do {
          // get the next child
          var childToHide = this._getNextToHide();
          // if there is no child to hide, just do nothing
          if (!childToHide) {
            return;
          }
          // get margins or spacing
          var margins = childToHide.getMarginLeft() + childToHide.getMarginRight();
          margins = Math.max(margins, this.getSpacing());
          var childWidth = childToHide.getSizeHint().width + margins;
          this.__hideChild(childToHide);

          // new width is the requiredWidth - the removed childs width
          requiredWidth -= childWidth;

          // show the overflowWidgetWidth
          if (overflowWidget && overflowWidget.getVisibility() != "visible") {
            overflowWidget.setVisibility("visible");
            // if we need to add the overflow indicator, we need to add its width
            requiredWidth += overflowWidgetWidth;
            // add spacing or margins
            var overflowWidgetMargins =
              overflowWidget.getMarginLeft() +
              overflowWidget.getMarginRight();
            requiredWidth += Math.max(overflowWidgetMargins, this.getSpacing());
          }
        } while (requiredWidth > width);

      // if we can possibly show something
      } else if (this.__removedItems.length > 0) {
        do {
          var removedChild = this.__removedItems[0];
          // if we have something we can show
          if (removedChild) {
            // get the margins or spacing
            var margins = removedChild.getMarginLeft() + removedChild.getMarginRight();
            margins = Math.max(margins, this.getSpacing());

            // check if the element has been rendered before [BUG #4542]
            if (removedChild.getDecoratorElement() == null) {
              // if not, apply the decorator element because it can change the
              // width of the child with padding e.g.
              removedChild.syncAppearance();
              // also invalidate the layout cache to trigger size hint
              // recalculation
              removedChild.invalidateLayoutCache();
            }
            var removedChildWidth = removedChild.getSizeHint().width;

            // check if it fits in in case its the last child to replace
            var fits = false;
            // if we can remove the overflow widget if its available
            if (this.__removedItems.length == 1 && overflowWidgetWidth > 0) {
              var addedMargin = margins - this.getSpacing();
              var wouldRequiredWidth =
                requiredWidth -
                overflowWidgetWidth +
                removedChildWidth +
                addedMargin;
              fits = width > wouldRequiredWidth;
            }

            // if it just fits in || it fits in when we remove the overflow widget
            if (width > requiredWidth + removedChildWidth + margins || fits) {
              this.__showChild(removedChild);
              requiredWidth += removedChildWidth;
              // check if we need to remove the overflow widget
              if (overflowWidget && this.__removedItems.length == 0) {
                overflowWidget.setVisibility("excluded");
              }
            } else {
              return;
            }
          }
        } while (width >= requiredWidth && this.__removedItems.length > 0);
      }
    },


    /**
     * Helper to show a toolbar item.
     *
     * @param child {qx.ui.core.Widget} The widget to show.
     */
    __showChild : function(child)
    {
      child.setVisibility("visible");
      this.__removedItems.shift();
      this.fireDataEvent("showItem", child)
    },


    /**
     * Helper to exclude a toolbar item.
     *
     * @param child {qx.ui.core.Widget} The widget to exclude.
     */
    __hideChild : function(child)
    {
      // ignore the call if no child is given
      if (!child) {
        return;
      }
      this.__removedItems.unshift(child);
      child.setVisibility("excluded");
      this.fireDataEvent("hideItem", child);
    },


    /**
     * Responsible for returning the next item to remove. In It checks the
     * priorities added by {@link #setRemovePriority}. If all priorized widgets
     * already excluded, it takes the widget added at last.
     *
     * @return {qx.ui.core.Widget|null} The widget which should be removed next.
     *   If null is returned, no widget is availablew to remove.
     */
    _getNextToHide : function()
    {
      // get the elements by priority
      for (var i = this.__removePriority.length - 1; i >= 0; i--) {
        var item = this.__removePriority[i];
        // maybe a priority is left out and spacers don't have the visibility
        if (item && item.getVisibility && item.getVisibility() == "visible") {
          return item;
        }
      };

      // if there is non found by priority, check all available widgets
      var children = this._getChildren();
      for (var i = children.length -1; i >= 0; i--) {
        var child = children[i]
        // ignore the overflow widget
        if (child == this.getOverflowIndicator()) {
          continue;
        }
        // spacer don't have the visibility
        if (child.getVisibility && child.getVisibility() == "visible") {
          return child;
        }
      };
    },


    /**
     * The removal of the toolbar items is priority based. You can change these
     * priorities with this method. The higher a priority, the earlier it will
     * be excluded. Remmeber to use every priority only once! If you want
     * override an already set priority, use the override parameter.
     * Keep in mind to only use already added items.
     *
     * @param item {qx.ui.core.Widget} The item to give the priority.
     * @param priority {Integer} The priority, higher means removed earlier.
     * @param override {Boolean} true, if the priority should be overridden.
     */
    setRemovePriority : function(item, priority, override)
    {
      // security check for overriding priorities
      if (!override && this.__removePriority[priority] != undefined) {
        throw new Error("Priority already in use!");
      }
      this.__removePriority[priority] = item;
    },


    // property apply
    _applyOverflowHandling : function(value, old)
    {
      // invalidate the own and the parrents layout cach because the size hint changes
      this.invalidateLayoutCache();
      var parent = this.getLayoutParent();
      if (parent) {
        parent.invalidateLayoutCache();
      }

      // recalculate if possible
      var bounds = this.getBounds()
      if (bounds && bounds.width) {
        this._recalculateOverflow(bounds.width);
      }

      // if the handling has been enabled
      if (value) {
        // add the resize listener
        this.addListener("resize", this._onResize, this);

      // if the handlis has been disabled
      } else {
        this.removeListener("resize", this._onResize, this);

        // set the overflow indicator to excluded
        var overflowIndicator = this.getOverflowIndicator();
        if (overflowIndicator) {
          overflowIndicator.setVisibility("excluded");
        }

        // set all buttons back to visible
        for (var i = 0; i < this.__removedItems.length; i++) {
          this.__removedItems[i].setVisibility("visible");
        };
        // reset the removed items
        this.__removedItems = [];
      }
    },


    // property apply
    _applyOverflowIndicator : function(value, old)
    {
      if (old) {
        this._remove(old);
      }

      if (value) {
        // check if its a child of the toolbar
        if (this._indexOf(value) == -1) {
          throw new Error("Widget must be child of the toolbar.");
        }
        // hide the widget
        value.setVisibility("excluded");
      }
    },


    /*
    ---------------------------------------------------------------------------
      MENU OPEN
    ---------------------------------------------------------------------------
    */

    __allowMenuOpenHover : false,

    /**
     * Indicate if a menu could be opened on hover or not.
     *
     * @internal
     * @param value {Boolean} <code>true</code> if a menu could be opened,
     *    <code>false</code> otherwise.
     */
    _setAllowMenuOpenHover : function(value) {
      this.__allowMenuOpenHover = value
    },

    /**
     * Return if a menu could be opened on hover or not.
     *
     * @internal
     * @return {Boolean} <code>true</code> if a menu could be opened,
     *    <code>false</code> otherwise.
     */
    _isAllowMenuOpenHover : function () {
      return this.__allowMenuOpenHover;
    },


    /*
    ---------------------------------------------------------------------------
      PROPERTY APPLY ROUTINES
    ---------------------------------------------------------------------------
    */

    // property apply
    _applySpacing : function(value, old)
    {
      var layout = this._getLayout();
      value == null ? layout.resetSpacing() : layout.setSpacing(value);
    },


    /*
    ---------------------------------------------------------------------------
      CHILD HANDLING
    ---------------------------------------------------------------------------
    */
    // overridden
    _add : function(child, options) {
      this.base(arguments, child, options);
      var newWidth =
        this.getSizeHint().width +
        child.getSizeHint().width +
        2 * this.getSpacing();
      this._recalculateOverflow(null, newWidth);
    },

    // overridden
    _addAt : function(child, index, options) {
      this.base(arguments, child, index, options);
      var newWidth =
        this.getSizeHint().width +
        child.getSizeHint().width +
        2 * this.getSpacing();
      this._recalculateOverflow(null, newWidth);
    },

    // overridden
    _addBefore : function(child, before, options) {
      this.base(arguments, child, before, options);
      var newWidth =
        this.getSizeHint().width +
        child.getSizeHint().width +
        2 * this.getSpacing();
      this._recalculateOverflow(null, newWidth);
    },

    // overridden
    _addAfter : function(child, after, options) {
      this.base(arguments, child, after, options);
      var newWidth =
        this.getSizeHint().width +
        child.getSizeHint().width +
        2 * this.getSpacing();
      this._recalculateOverflow(null, newWidth);
    },

    // overridden
    _remove : function(child) {
      this.base(arguments, child);
      var newWidth =
        this.getSizeHint().width -
        child.getSizeHint().width -
        2 * this.getSpacing();
      this._recalculateOverflow(null, newWidth);
    },

    // overridden
    _removeAt : function(index) {
      var child = this._getChildren()[index];
      this.base(arguments, index);
      var newWidth =
        this.getSizeHint().width -
        child.getSizeHint().width -
        2 * this.getSpacing();
      this._recalculateOverflow(null, newWidth);
    },

    // overridden
    _removeAll : function() {
      this.base(arguments);
      this._recalculateOverflow(null, 0);
    },


    /*
    ---------------------------------------------------------------------------
      UTILITIES
    ---------------------------------------------------------------------------
    */

    /**
     * Add a spacer to the toolbar. The spacer has a flex
     * value of one and will stretch to the available space.
     *
     * @return {qx.ui.core.Spacer} The newly added spacer object. A reference
     *   to the spacer is needed to remove this spacer from the layout.
     */
    addSpacer : function()
    {
      var spacer = new qx.ui.core.Spacer;
      this._add(spacer, {flex:1});
      return spacer;
    },


    /**
     * Adds a separator to the toolbar.
     */
    addSeparator : function() {
      this.add(new qx.ui.toolbar.Separator);
    },


    /**
     * Returns all nested buttons which contains a menu to show. This is mainly
     * used for keyboard support.
     *
     * @return {Array} List of all menu buttons
     */
    getMenuButtons : function()
    {
      var children = this.getChildren();
      var buttons = [];
      var child;

      for (var i=0, l=children.length; i<l; i++)
      {
        child = children[i];

        if (child instanceof qx.ui.menubar.Button) {
          buttons.push(child);
        } else if (child instanceof qx.ui.toolbar.Part) {
          buttons.push.apply(buttons, child.getMenuButtons());
        }
      }

      return buttons;
    }
  },


  destruct : function() {
    if (this.hasListener("resize")) {
      this.removeListener("resize", this._onResize, this);
    }

  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * A Spacer is a "virtual" widget, which can be placed into any layout and takes
 * the space a normal widget of the same size would take.
 *
 * Spacers are invisible and very light weight because they don't require any
 * DOM modifications.
 *
 * *Example*
 *
 * Here is a little example of how to use the widget.
 *
 * <pre class='javascript'>
 *   var container = new qx.ui.container.Composite(new qx.ui.layout.HBox());
 *   container.add(new qx.ui.core.Widget());
 *   container.add(new qx.ui.core.Spacer(50));
 *   container.add(new qx.ui.core.Widget());
 * </pre>
 *
 * This example places two widgets and a spacer into a container with a
 * horizontal box layout. In this scenario the spacer creates an empty area of
 * 50 pixel width between the two widgets.
 *
 * *External Documentation*
 *
 * <a href='http://manual.qooxdoo.org/1.4/pages/widget/spacer.html' target='_blank'>
 * Documentation of this widget in the qooxdoo manual.</a>
 */
qx.Class.define("qx.ui.core.Spacer",
{
  extend : qx.ui.core.LayoutItem,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

 /**
  * @param width {Integer?null} the initial width
  * @param height {Integer?null} the initial height
  */
  construct : function(width, height)
  {
    this.base(arguments);

    // Initialize dimensions
    this.setWidth(width != null ? width : 0);
    this.setHeight(height != null ? height : 0);
  },



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /**
     * Helper method called from the visibility queue to detect outstanding changes
     * to the appearance.
     *
     * @internal
     */
    checkAppearanceNeeds : function() {
      // placeholder to improve compatibility with Widget.
    },


    /**
     * Recursively adds all children to the given queue
     *
     * @param queue {Map} The queue to add widgets to
     */
    addChildrenToQueue : function(queue) {
      // placeholder to improve compatibility with Widget.
    },


    /**
     * Removes this widget from its parent and dispose it.
     *
     * Please note that the widget is not disposed synchronously. The
     * real dispose happens after the next queue flush.
     *
     * @return {void}
     */
    destroy : function()
    {
      if (this.$$disposed) {
        return;
      }

      var parent = this.$$parent;
      if (parent) {
        parent._remove(this);
      }

      qx.ui.core.queue.Dispose.add(this);
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)

************************************************************************ */

/**
 * A widget used for decoration proposes to structure a toolbar. Each
 * Separator renders a line between the buttons around.
 */
qx.Class.define("qx.ui.toolbar.Separator",
{
  extend : qx.ui.core.Widget,





  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    // overridden
    appearance :
    {
      refine : true,
      init : "toolbar-separator"
    },

    // overridden
    anonymous :
    {
      refine : true,
      init : true
    },

    // overridden
    width :
    {
      refine : true,
      init : 0
    },

    // overridden
    height :
    {
      refine : true,
      init : 0
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * A part is a container for multiple toolbar buttons. Each part comes
 * with a handle which may be used in later versions to drag the part
 * around and move it to another position. Currently mainly used
 * for structuring large toolbars beyond the capabilities of the
 * {@link Separator}.
 *
 * @childControl handle {qx.ui.basic.Image} prat handle to visualize the separation
 * @childControl container {qx.ui.toolbar.PartContainer} holds the content of the toolbar part
 */
qx.Class.define("qx.ui.toolbar.Part",
{
  extend : qx.ui.core.Widget,
  include : [qx.ui.core.MRemoteChildrenHandling],



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    // Hard coded HBox layout
    this._setLayout(new qx.ui.layout.HBox);

    // Force creation of the handle
    this._createChildControl("handle");
  },



  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    appearance :
    {
      refine : true,
      init : "toolbar/part"
    },

    /** Whether icons, labels, both or none should be shown. */
    show :
    {
      init : "both",
      check : [ "both", "label", "icon" ],
      inheritable : true,
      event : "changeShow"
    },

    /** The spacing between every child of the toolbar */
    spacing :
    {
      nullable : true,
      check : "Integer",
      themeable : true,
      apply : "_applySpacing"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      WIDGET API
    ---------------------------------------------------------------------------
    */

    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
        case "handle":
          control = new qx.ui.basic.Image();
          control.setAlignY("middle");
          this._add(control);
          break;

        case "container":
          control = new qx.ui.toolbar.PartContainer();
          control.addListener("syncAppearance", this.__onSyncAppearance, this);
          this._add(control);
          break;
      }

      return control || this.base(arguments, id);
    },

    // overridden
    getChildrenContainer : function() {
      return this.getChildControl("container");
    },




    /*
    ---------------------------------------------------------------------------
      PROPERTY APPLY ROUTINES
    ---------------------------------------------------------------------------
    */

    _applySpacing : function(value, old)
    {
      var layout = this.getChildControl("container").getLayout();
      value == null ? layout.resetSpacing() : layout.setSpacing(value);
    },




    /*
    ---------------------------------------------------------------------------
      UTILITIES
    ---------------------------------------------------------------------------
    */
    /**
     * Helper which applies the left, right and middle states.
     */
    __onSyncAppearance : function() {
      // check every child
      var children = this.getChildrenContainer().getChildren();
      for (var i = 0; i < children.length; i++) {
        // if its the first child
        if (i == 0 && i != children.length - 1) {
          children[i].addState("left");
          children[i].removeState("right");
          children[i].removeState("middle");
        // if its the last child
        } else if (i == children.length - 1 && i != 0) {
          children[i].addState("right");
          children[i].removeState("left");
          children[i].removeState("middle");
        // if there is only one child
        } else if (i == 0 && i == children.length - 1) {
          children[i].removeState("left");
          children[i].removeState("middle");
          children[i].removeState("right");
        } else {
          children[i].addState("middle");
          children[i].removeState("right");
          children[i].removeState("left");
        }
      };
    },


    /**
     * Adds a separator to the toolbar part.
     */
    addSeparator : function() {
      this.add(new qx.ui.toolbar.Separator);
    },


    /**
     * Returns all nested buttons which contains a menu to show. This is mainly
     * used for keyboard support.
     *
     * @return {Array} List of all menu buttons
     */
    getMenuButtons : function()
    {
      var children = this.getChildren();
      var buttons = [];
      var child;

      for (var i=0, l=children.length; i<l; i++)
      {
        child = children[i];

        if (child instanceof qx.ui.menubar.Button) {
          buttons.push(child);
        }
      }

      return buttons;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * The container used by {@link Part} to insert the buttons.
 *
 * @internal
 */
qx.Class.define("qx.ui.toolbar.PartContainer",
{
  extend : qx.ui.container.Composite,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);
    this._setLayout(new qx.ui.layout.HBox);
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */
  properties :
  {
    appearance :
    {
      refine : true,
      init : "toolbar/part/container"
    },

    /** Whether icons, labels, both or none should be shown. */
    show :
    {
      init : "both",
      check : [ "both", "label", "icon" ],
      inheritable : true,
      event : "changeShow"
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The real menu button class which supports a command and an icon. All
 * other features are inherited from the {@link qx.ui.menu.AbstractButton}
 * class.
 */
qx.Class.define("qx.ui.menu.Button",
{
  extend : qx.ui.menu.AbstractButton,


  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param label {String} Initial label
   * @param icon {String} Initial icon
   * @param command {qx.ui.core.Command} Initial command (shortcut)
   * @param menu {qx.ui.menu.Menu} Initial sub menu
   */
  construct : function(label, icon, command, menu)
  {
    this.base(arguments);

    // Initialize with incoming arguments
    if (label != null) {
      this.setLabel(label);
    }

    if (icon != null) {
      this.setIcon(icon);
    }

    if (command != null) {
      this.setCommand(command);
    }

    if (menu != null) {
      this.setMenu(menu);
    }
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    // overridden
    appearance :
    {
      refine : true,
      init : "menu-button"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      EVENT HANDLER
    ---------------------------------------------------------------------------
    */


    // overridden
    _onMouseUp : function(e)
    {
      if (e.isLeftPressed())
      {
        this.execute();

        // don't close menus if the button is a sub menu button
        if (this.getMenu()) {
          return;
        }
      } else {
        // don't close menus if the button has a context menu
        if (this.getContextMenu()) {
          return;
        }
      }

      qx.ui.menu.Manager.getInstance().hideAll();
    },


    // overridden
    _onKeyPress : function(e) {
      this.execute();
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2009 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * The traditional qx.ui.menu.MenuButton to access the column visibility menu.
 */
qx.Class.define("qx.ui.table.columnmenu.Button",
{
  extend     : qx.ui.form.MenuButton,
  implement  : qx.ui.table.IColumnMenuButton,

  /**
   * Create a new instance of a column visibility menu button. This button
   * also contains the factory for creating each of the sub-widgets.
   */
  construct : function()
  {
    this.base(arguments);

    // add blocker
    this.__blocker = new qx.ui.core.Blocker(this);
  },

  members :
  {
    __columnMenuButtons : null,
    __blocker : null,

    // Documented in qx.ui.table.IColumnMenu
    factory : function(item, options)
    {
      switch(item)
      {
        case "menu":
          var menu = new qx.ui.menu.Menu();
          this.setMenu(menu);
          return menu;

        case "menu-button":
          var menuButton =
            new qx.ui.table.columnmenu.MenuItem(options.text);
          menuButton.setVisible(options.bVisible);
          this.getMenu().add(menuButton);
          return menuButton;

        case "user-button":
          var button = new qx.ui.menu.Button(options.text);
          button.set(
            {
              appearance: "table-column-reset-button"
            });
          return button;

        case "separator":
          return new qx.ui.menu.Separator();

        default:
          throw new Error("Unrecognized factory request: " + item);
      }
    },


    /**
     * Returns the blocker of the columnmenu button.
     *
     * @return {qx.ui.core.Blocker} the blocker.
     */
    getBlocker : function() {
      return this.__blocker;
    },

    // Documented in qx.ui.table.IColumnMenu
    empty : function()
    {
      var menu = this.getMenu();
      var entries = menu.getChildren();

      for (var i=0,l=entries.length; i<l; i++)
      {
        entries[0].destroy();
      }
    }
  },

  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct: function() {
    this.__blocker.dispose();
  }

});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Martin Wittemann (martinwittemann)

************************************************************************ */

/**
 * Form interface for all form widgets which have boolean as their primary
 * data type like a checkbox.
 */
qx.Interface.define("qx.ui.form.IBooleanForm",
{
  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events :
  {
    /** Fired when the value was modified */
    "changeValue" : "qx.event.type.Data"
  },



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
    ---------------------------------------------------------------------------
      VALUE PROPERTY
    ---------------------------------------------------------------------------
    */

    /**
     * Sets the element's value.
     *
     * @param value {Boolean|null} The new value of the element.
     */
    setValue : function(value) {
      return arguments.length == 1;
    },


    /**
     * Resets the element's value to its initial value.
     */
    resetValue : function() {},


    /**
     * The element's user set value.
     *
     * @return {Boolean|null} The value.
     */
    getValue : function() {}
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Fabian Jakobs (fjakobs)
     * Martin Wittemann (martinwittemann)

************************************************************************ */

/**
 * Renders a special checkbox button inside a menu. The button behaves like
 * a normal {@link qx.ui.form.CheckBox} and shows a check icon when
 * checked; normally shows no icon when not checked (depends on the theme).
 */
qx.Class.define("qx.ui.menu.CheckBox",
{
  extend : qx.ui.menu.AbstractButton,
  implement : [qx.ui.form.IBooleanForm],



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param label {String} Initial label
   * @param menu {qx.ui.menu.Menu} Initial sub menu
   */
  construct : function(label, menu)
  {
    this.base(arguments);

    // Initialize with incoming arguments
    if (label != null) {
      // try to translate every time you create a checkbox [BUG #2699]
      if (label.translate) {
        this.setLabel(label.translate());
      } else {
        this.setLabel(label);
      }
    }

    if (menu != null) {
      this.setMenu(menu);
    }

    this.addListener("execute", this._onExecute, this);
  },



  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    // overridden
    appearance :
    {
      refine : true,
      init : "menu-checkbox"
    },

    /** Whether the button is checked */
    value :
    {
      check : "Boolean",
      init : false,
      apply : "_applyValue",
      event : "changeValue",
      nullable : true
    }
  },





  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    // overridden (from MExecutable to keet the icon out of the binding)
    /**
     * @lint ignoreReferenceField(_bindableProperties)
     */
    _bindableProperties :
    [
      "enabled",
      "label",
      "toolTipText",
      "value",
      "menu"
    ],

    // property apply
    _applyValue : function(value, old)
    {
      value ?
        this.addState("checked") :
        this.removeState("checked");
    },


    /**
     * Handler for the execute event.
     *
     * @param e {qx.event.type.Event} The execute event.
     */
    _onExecute : function(e) {
      this.toggleValue();
    },


    // overridden
    _onMouseUp : function(e)
    {
      if (e.isLeftPressed()) {
        this.execute();
      } else {
        // don't close menus if the button has a context menu
        if (this.getContextMenu()) {
          return;
        }
      }
      qx.ui.menu.Manager.getInstance().hideAll();
    },


    // overridden
    _onKeyPress : function(e) {
      this.execute();
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2009 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)

************************************************************************ */

/**
 * Interface for a column menu item corresponding to a table column.
 */
qx.Interface.define("qx.ui.table.IColumnMenuItem",
{
  properties :
  {
    /**
     * Whether the table column associated with this menu item is visible
     */
    visible : { }
  },

  events :
  {
    /**
     * Dispatched when a column changes visibility state. The event data is a
     * boolean indicating whether the table column associated with this menu
     * item is now visible.
     */
    changeVisible : "qx.event.type.Data"
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2009 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)

************************************************************************ */

/**
 * A menu item.
 */
qx.Class.define("qx.ui.table.columnmenu.MenuItem",
{
  extend     : qx.ui.menu.CheckBox,
  implement  : qx.ui.table.IColumnMenuItem,

  properties :
  {
    /**
     * Whether the table column associated with this menu item is visible.
     */
    visible :
    {
      check : "Boolean",
      init  : true,
      apply : "_applyVisible",
      event : "changeVisible"
    }
  },

  /**
   * Create a new instance of an item for insertion into the table column
   * visibility menu.
   *
   * @param text {String}
   *   Text for the menu item, most typically the name of the column in the
   *   table.
   */
  construct : function(text)
  {
    this.base(arguments, text);

    // Mirror native "value" property in our "visible" property
    this.addListener("changeValue",
                     function(e)
                     {
                       this.bInListener = true;
                       this.setVisible(e.getData());
                       this.bInListener = false;
                     });
  },

  members :
  {
    __bInListener : false,

    /**
     * Keep menu in sync with programmatic changes of visibility
     *
     * @param value {Boolean}
     *   New visibility value
     *
     * @param old {Boolean}
     *   Previous visibility value
     */
    _applyVisible : function(value, old)
    {
      // avoid recursion if called from listener on "changeValue" property
      if (! this.bInListener)
      {
        this.setValue(value);
      }
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * A selection manager. This is a helper class that handles all selection
 * related events and updates a SelectionModel.
 * <p>
 * Widgets that support selection should use this manager. This way the only
 * thing the widget has to do is mapping mouse or key events to indexes and
 * call the corresponding handler method.
 *
 * @see SelectionModel
 */
qx.Class.define("qx.ui.table.selection.Manager",
{
  extend : qx.core.Object,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function() {
    this.base(arguments);
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /**
     * The selection model where to set the selection changes.
     */
    selectionModel :
    {
      check : "qx.ui.table.selection.Model"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __lastMouseDownHandled : null,


    /**
     * Handles the mouse down event.
     *
     * @param index {Integer} the index the mouse is pointing at.
     * @param evt {Map} the mouse event.
     * @return {void}
     */
    handleMouseDown : function(index, evt)
    {
      if (evt.isLeftPressed())
      {
        var selectionModel = this.getSelectionModel();

        if (!selectionModel.isSelectedIndex(index))
        {
          // This index is not selected -> We react when the mouse is pressed (because of drag and drop)
          this._handleSelectEvent(index, evt);
          this.__lastMouseDownHandled = true;
        }
        else
        {
          // This index is already selected -> We react when the mouse is released (because of drag and drop)
          this.__lastMouseDownHandled = false;
        }
      }
      else if (evt.isRightPressed() && evt.getModifiers() == 0)
      {
        var selectionModel = this.getSelectionModel();

        if (!selectionModel.isSelectedIndex(index))
        {
          // This index is not selected -> Set the selection to this index
          selectionModel.setSelectionInterval(index, index);
        }
      }
    },


    /**
     * Handles the mouse up event.
     *
     * @param index {Integer} the index the mouse is pointing at.
     * @param evt {Map} the mouse event.
     * @return {void}
     */
    handleMouseUp : function(index, evt)
    {
      if (evt.isLeftPressed() && !this.__lastMouseDownHandled) {
        this._handleSelectEvent(index, evt);
      }
    },


    /**
     * Handles the mouse click event.
     *
     * @param index {Integer} the index the mouse is pointing at.
     * @param evt {Map} the mouse event.
     * @return {void}
     */
    handleClick : function(index, evt) {},


    /**
     * Handles the key down event that is used as replacement for mouse clicks
     * (Normally space).
     *
     * @param index {Integer} the index that is currently focused.
     * @param evt {Map} the key event.
     * @return {void}
     */
    handleSelectKeyDown : function(index, evt) {
      this._handleSelectEvent(index, evt);
    },


    /**
     * Handles a key down event that moved the focus (E.g. up, down, home, end, ...).
     *
     * @param index {Integer} the index that is currently focused.
     * @param evt {Map} the key event.
     * @return {void}
     */
    handleMoveKeyDown : function(index, evt)
    {
      var selectionModel = this.getSelectionModel();

      switch(evt.getModifiers())
      {
        case 0:
          selectionModel.setSelectionInterval(index, index);
          break;

        case qx.event.type.Dom.SHIFT_MASK:
          var anchor = selectionModel.getAnchorSelectionIndex();

          if (anchor == -1) {
            selectionModel.setSelectionInterval(index, index);
          } else {
            selectionModel.setSelectionInterval(anchor, index);
          }

          break;
      }
    },


    /**
     * Handles a select event.
     *
     * @param index {Integer} the index the event is pointing at.
     * @param evt {Map} the mouse event.
     * @return {void}
     */
    _handleSelectEvent : function(index, evt)
    {
      var selectionModel = this.getSelectionModel();

      var leadIndex = selectionModel.getLeadSelectionIndex();
      var anchorIndex = selectionModel.getAnchorSelectionIndex();

      if (evt.isShiftPressed())
      {
        if (index != leadIndex || selectionModel.isSelectionEmpty())
        {
          // The lead selection index was changed
          if (anchorIndex == -1) {
            anchorIndex = index;
          }

          if (evt.isCtrlOrCommandPressed()) {
            selectionModel.addSelectionInterval(anchorIndex, index);
          } else {
            selectionModel.setSelectionInterval(anchorIndex, index);
          }
        }
      }
      else if (evt.isCtrlOrCommandPressed())
      {
        if (selectionModel.isSelectedIndex(index)) {
          selectionModel.removeSelectionInterval(index, index);
        } else {
          selectionModel.addSelectionInterval(index, index);
        }
      }
      else
      {
        // setSelectionInterval checks to see if the change is really necessary
        selectionModel.setSelectionInterval(index, index);
      }
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)
     * David Perez Carmona (david-perez)

************************************************************************ */

/**
 * A selection model.
 */
qx.Class.define("qx.ui.table.selection.Model",
{
  extend : qx.core.Object,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    this.__selectedRangeArr = [];
    this.__anchorSelectionIndex = -1;
    this.__leadSelectionIndex = -1;
    this.hasBatchModeRefCount = 0;
    this.__hadChangeEventInBatchMode = false;
  },



  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events: {
    /** Fired when the selection has changed. */
    "changeSelection" : "qx.event.type.Event"
  },



  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {

    /** {int} The selection mode "none". Nothing can ever be selected. */
    NO_SELECTION                : 1,

    /** {int} The selection mode "single". This mode only allows one selected item. */
    SINGLE_SELECTION            : 2,


    /**
     * (int) The selection mode "single interval". This mode only allows one
     * continuous interval of selected items.
     */
    SINGLE_INTERVAL_SELECTION   : 3,


    /**
     * (int) The selection mode "multiple interval". This mode only allows any
     * selection.
     */
    MULTIPLE_INTERVAL_SELECTION : 4,


    /**
     * (int) The selection mode "multiple interval". This mode only allows any
     * selection. The difference with the previous one, is that multiple
     * selection is eased. A click on an item, toggles its selection state.
     * On the other hand, MULTIPLE_INTERVAL_SELECTION does this behavior only
     * when Ctrl-clicking an item.
     */
    MULTIPLE_INTERVAL_SELECTION_TOGGLE : 5
  },



  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /**
     * Set the selection mode. Valid values are {@link #NO_SELECTION},
     * {@link #SINGLE_SELECTION}, {@link #SINGLE_INTERVAL_SELECTION},
     * {@link #MULTIPLE_INTERVAL_SELECTION} and
     * {@link #MULTIPLE_INTERVAL_SELECTION_TOGGLE}.
     */
    selectionMode :
    {
      init : 2, //SINGLE_SELECTION,
      check : [1,2,3,4,5],
      //[ NO_SELECTION, SINGLE_SELECTION, SINGLE_INTERVAL_SELECTION, MULTIPLE_INTERVAL_SELECTION, MULTIPLE_INTERVAL_SELECTION_TOGGLE ],
      apply : "_applySelectionMode"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __hadChangeEventInBatchMode : null,
    __anchorSelectionIndex : null,
    __leadSelectionIndex : null,
    __selectedRangeArr : null,


    // selectionMode property modifier
    _applySelectionMode : function(selectionMode) {
      this.resetSelection();
    },


    /**
     *
     * Activates / Deactivates batch mode. In batch mode, no change events will be thrown but
     * will be collected instead. When batch mode is turned off again and any events have
     * been collected, one event is thrown to inform the listeners.
     *
     * This method supports nested calling, i. e. batch mode can be turned more than once.
     * In this case, batch mode will not end until it has been turned off once for each
     * turning on.
     *
     * @param batchMode {Boolean} true to activate batch mode, false to deactivate
     * @return {Boolean} true if batch mode is active, false otherwise
     * @throws Error if batch mode is turned off once more than it has been turned on
     */
    setBatchMode : function(batchMode)
    {
      if (batchMode) {
        this.hasBatchModeRefCount += 1;
      }
      else
      {
        if (this.hasBatchModeRefCount == 0) {
          throw new Error("Try to turn off batch mode althoug it was not turned on.");
        }

        this.hasBatchModeRefCount -= 1;

        if (this.__hadChangeEventInBatchMode)
        {
          this.__hadChangeEventInBatchMode = false;
          this._fireChangeSelection();
        }
      }

      return this.hasBatchMode();
    },


    /**
     *
     * Returns whether batch mode is active. See setter for a description of batch mode.
     *
     * @return {Boolean} true if batch mode is active, false otherwise
     */
    hasBatchMode : function() {
      return this.hasBatchModeRefCount > 0;
    },


    /**
     * Returns the first argument of the last call to {@link #setSelectionInterval()},
     * {@link #addSelectionInterval()} or {@link #removeSelectionInterval()}.
     *
     * @return {Integer} the anchor selection index.
     */
    getAnchorSelectionIndex : function() {
      return this.__anchorSelectionIndex;
    },


    /**
     * Sets the anchor selection index. Only use this function, if you want manipulate
     * the selection manually.
     *
     * @param index {Integer} the index to set.
     */
    _setAnchorSelectionIndex : function(index) {
      this.__anchorSelectionIndex = index;
    },


    /**
     * Returns the second argument of the last call to {@link #setSelectionInterval()},
     * {@link #addSelectionInterval()} or {@link #removeSelectionInterval()}.
     *
     * @return {Integer} the lead selection index.
     */
    getLeadSelectionIndex : function() {
      return this.__leadSelectionIndex;
    },


    /**
     * Sets the lead selection index. Only use this function, if you want manipulate
     * the selection manually.
     *
     * @param index {Integer} the index to set.
     */
    _setLeadSelectionIndex : function(index) {
      this.__leadSelectionIndex = index;
    },


    /**
     * Returns an array that holds all the selected ranges of the table. Each
     * entry is a map holding information about the "minIndex" and "maxIndex" of the
     * selection range.
     *
     * @return {Map[]} array with all the selected ranges.
     */
    _getSelectedRangeArr : function() {
      return this.__selectedRangeArr;
    },


    /**
     * Resets (clears) the selection.
     */
    resetSelection : function()
    {
      if (!this.isSelectionEmpty())
      {
        this._resetSelection();
        this._fireChangeSelection();
      }
    },


    /**
     * Returns whether the selection is empty.
     *
     * @return {Boolean} whether the selection is empty.
     */
    isSelectionEmpty : function() {
      return this.__selectedRangeArr.length == 0;
    },


    /**
     * Returns the number of selected items.
     *
     * @return {Integer} the number of selected items.
     */
    getSelectedCount : function()
    {
      var selectedCount = 0;

      for (var i=0; i<this.__selectedRangeArr.length; i++)
      {
        var range = this.__selectedRangeArr[i];
        selectedCount += range.maxIndex - range.minIndex + 1;
      }

      return selectedCount;
    },


    /**
     * Returns whether an index is selected.
     *
     * @param index {Integer} the index to check.
     * @return {Boolean} whether the index is selected.
     */
    isSelectedIndex : function(index)
    {
      for (var i=0; i<this.__selectedRangeArr.length; i++)
      {
        var range = this.__selectedRangeArr[i];

        if (index >= range.minIndex && index <= range.maxIndex) {
          return true;
        }
      }

      return false;
    },


    /**
     * Returns the selected ranges as an array. Each array element has a
     * <code>minIndex</code> and a <code>maxIndex</code> property.
     *
     * @return {Map[]} the selected ranges.
     */
    getSelectedRanges : function()
    {
      // clone the selection array and the individual elements - this prevents the
      // caller from messing with the internal model
      var retVal = [];

      for (var i=0; i<this.__selectedRangeArr.length; i++)
      {
        retVal.push(
        {
          minIndex : this.__selectedRangeArr[i].minIndex,
          maxIndex : this.__selectedRangeArr[i].maxIndex
        });
      }

      return retVal;
    },


    /**
     * Calls an iterator function for each selected index.
     *
     * Usage Example:
     * <pre class='javascript'>
     * var selectedRowData = [];
     * mySelectionModel.iterateSelection(function(index) {
     *   selectedRowData.push(myTableModel.getRowData(index));
     * });
     * </pre>
     *
     * @param iterator {Function} the function to call for each selected index.
     *          Gets the current index as parameter.
     * @param object {var ? null} the object to use when calling the handler.
     *          (this object will be available via "this" in the iterator)
     * @return {void}
     */
    iterateSelection : function(iterator, object)
    {
      for (var i=0; i<this.__selectedRangeArr.length; i++)
      {
        for (var j=this.__selectedRangeArr[i].minIndex; j<=this.__selectedRangeArr[i].maxIndex; j++) {
          iterator.call(object, j);
        }
      }
    },


    /**
     * Sets the selected interval. This will clear the former selection.
     *
     * @param fromIndex {Integer} the first index of the selection (including).
     * @param toIndex {Integer} the last index of the selection (including).
     * @return {void}
     */
    setSelectionInterval : function(fromIndex, toIndex)
    {
      var me = this.self(arguments);

      switch(this.getSelectionMode())
      {
        case me.NO_SELECTION:
          return;

        case me.SINGLE_SELECTION:
          // Ensure there is actually a change of selection
          if (this.isSelectedIndex(toIndex)) {
            return;
          }

          fromIndex = toIndex;
          break;

        case me.MULTIPLE_INTERVAL_SELECTION_TOGGLE:
          this.setBatchMode(true);
          try
          {
            for (var i = fromIndex; i <= toIndex; i++)
            {
              if (!this.isSelectedIndex(i))
              {
                this._addSelectionInterval(i, i);
              }
              else
              {
                this.removeSelectionInterval(i, i);
              }
            }
          }
          catch (e)
          {
            // IE doesn't execute the "finally" block if no "catch" block is present
            // this hack is used to fix [BUG #3688]
            if (
              qx.core.Environment.get("browser.name") == 'ie' &&
              qx.core.Environment.get("browser.version") <= 7
            ) {
              this.setBatchMode(false);
            }
            throw e;
          }
          finally {
            this.setBatchMode(false);
          }
          this._fireChangeSelection();
          return;
      }

      this._resetSelection();
      this._addSelectionInterval(fromIndex, toIndex);

      this._fireChangeSelection();
    },


    /**
     * Adds a selection interval to the current selection.
     *
     * @param fromIndex {Integer} the first index of the selection (including).
     * @param toIndex {Integer} the last index of the selection (including).
     * @return {void}
     */
    addSelectionInterval : function(fromIndex, toIndex)
    {
      var SelectionModel = qx.ui.table.selection.Model;

      switch(this.getSelectionMode())
      {
        case SelectionModel.NO_SELECTION:
          return;

        case SelectionModel.MULTIPLE_INTERVAL_SELECTION:
        case SelectionModel.MULTIPLE_INTERVAL_SELECTION_TOGGLE:
          this._addSelectionInterval(fromIndex, toIndex);
          this._fireChangeSelection();
          break;

        default:
          this.setSelectionInterval(fromIndex, toIndex);
          break;
      }
    },


    /**
     * Removes an interval from the current selection.
     *
     * @param fromIndex {Integer} the first index of the interval (including).
     * @param toIndex {Integer} the last index of the interval (including).
     * @return {void}
     */
    removeSelectionInterval : function(fromIndex, toIndex)
    {
      this.__anchorSelectionIndex = fromIndex;
      this.__leadSelectionIndex = toIndex;

      var minIndex = Math.min(fromIndex, toIndex);
      var maxIndex = Math.max(fromIndex, toIndex);

      // Crop the affected ranges
      for (var i=0; i<this.__selectedRangeArr.length; i++)
      {
        var range = this.__selectedRangeArr[i];

        if (range.minIndex > maxIndex)
        {
          // We are done
          break;
        }
        else if (range.maxIndex >= minIndex)
        {
          // This range is affected
          var minIsIn = (range.minIndex >= minIndex) && (range.minIndex <= maxIndex);
          var maxIsIn = (range.maxIndex >= minIndex) && (range.maxIndex <= maxIndex);

          if (minIsIn && maxIsIn)
          {
            // This range is removed completely
            this.__selectedRangeArr.splice(i, 1);

            // Check this index another time
            i--;
          }
          else if (minIsIn)
          {
            // The range is cropped from the left
            range.minIndex = maxIndex + 1;
          }
          else if (maxIsIn)
          {
            // The range is cropped from the right
            range.maxIndex = minIndex - 1;
          }
          else
          {
            // The range is split
            var newRange =
            {
              minIndex : maxIndex + 1,
              maxIndex : range.maxIndex
            };

            this.__selectedRangeArr.splice(i + 1, 0, newRange);

            range.maxIndex = minIndex - 1;

            // We are done
            break;
          }
        }
      }

      // this._dumpRanges();
      this._fireChangeSelection();
    },


    /**
     * Resets (clears) the selection, but doesn't inform the listeners.
     */
    _resetSelection : function()
    {
      this.__selectedRangeArr = [];
      this.__anchorSelectionIndex = -1;
      this.__leadSelectionIndex = -1;
    },


    /**
     * Adds a selection interval to the current selection, but doesn't inform
     * the listeners.
     *
     * @param fromIndex {Integer} the first index of the selection (including).
     * @param toIndex {Integer} the last index of the selection (including).
     * @return {void}
     */
    _addSelectionInterval : function(fromIndex, toIndex)
    {
      this.__anchorSelectionIndex = fromIndex;
      this.__leadSelectionIndex = toIndex;

      var minIndex = Math.min(fromIndex, toIndex);
      var maxIndex = Math.max(fromIndex, toIndex);

      // Find the index where the new range should be inserted
      var newRangeIndex = 0;

      for (;newRangeIndex<this.__selectedRangeArr.length; newRangeIndex++)
      {
        var range = this.__selectedRangeArr[newRangeIndex];

        if (range.minIndex > minIndex) {
          break;
        }
      }

      // Add the new range
      this.__selectedRangeArr.splice(newRangeIndex, 0,
      {
        minIndex : minIndex,
        maxIndex : maxIndex
      });

      // Merge overlapping ranges
      var lastRange = this.__selectedRangeArr[0];

      for (var i=1; i<this.__selectedRangeArr.length; i++)
      {
        var range = this.__selectedRangeArr[i];

        if (lastRange.maxIndex + 1 >= range.minIndex)
        {
          // The ranges are overlapping -> merge them
          lastRange.maxIndex = Math.max(lastRange.maxIndex, range.maxIndex);

          // Remove the current range
          this.__selectedRangeArr.splice(i, 1);

          // Check this index another time
          i--;
        }
        else
        {
          lastRange = range;
        }
      }
    },

    // this._dumpRanges();
    /**
     * Logs the current ranges for debug perposes.
     *
     * @return {void}
     */
    _dumpRanges : function()
    {
      var text = "Ranges:";

      for (var i=0; i<this.__selectedRangeArr.length; i++)
      {
        var range = this.__selectedRangeArr[i];
        text += " [" + range.minIndex + ".." + range.maxIndex + "]";
      }

      this.debug(text);
    },


    /**
     * Fires the "changeSelection" event to all registered listeners. If the selection model
     * currently is in batch mode, only one event will be thrown when batch mode is ended.
     *
     * @return {void}
     */
    _fireChangeSelection : function()
    {
      if (this.hasBatchMode())
      {
        // In batch mode, remember event but do not throw (yet)
        this.__hadChangeEventInBatchMode = true;
      }
      else
      {
        // If not in batch mode, throw event
        this.fireEvent("changeSelection");
      }
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    this.__selectedRangeArr = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The table pane that shows a certain section from a table. This class handles
 * the display of the data part of a table and is therefore the base for virtual
 * scrolling.
 */
qx.Class.define("qx.ui.table.pane.Pane",
{
  extend : qx.ui.core.Widget,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param paneScroller {qx.ui.table.pane.Scroller} the TablePaneScroller the header belongs to.
   */
  construct : function(paneScroller)
  {
    this.base(arguments);

    this.__paneScroller = paneScroller;

    this.__lastColCount = 0;
    this.__lastRowCount = 0;

    this.__rowCache = [];
  },


  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */


  events :
  {
    /**
     * Whether the current view port of the pane has not loaded data.
     * The data object of the event indicates if the table pane has to reload
     * data or not. Can be used to give the user feedback of the loading state
     * of the rows.
     */
    "paneReloadsData" : "qx.event.type.Data",

    /**
     * Whenever the content of the table panehas been updated (rendered)
     * trigger a paneUpdated event. This allows the canvas cellrenderer to act
     * once the new cells have been integrated in the dom.
     */
    "paneUpdated" : "qx.event.type.Event"
  },


  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** The index of the first row to show. */
    firstVisibleRow :
    {
      check : "Number",
      init : 0,
      apply : "_applyFirstVisibleRow"
    },


    /** The number of rows to show. */
    visibleRowCount :
    {
      check : "Number",
      init : 0,
      apply : "_applyVisibleRowCount"
    },


    /**
     * Maximum number of cached rows. If the value is <code>-1</code> the cache
     * size is unlimited
     */
    maxCacheLines :
    {
      check : "Number",
      init : 1000,
      apply : "_applyMaxCacheLines"
    },

    // overridden
    allowShrinkX :
    {
      refine : true,
      init : false
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __lastRowCount : null,
    __lastColCount : null,

    __paneScroller : null,
    __tableContainer : null,

    __focusedRow : null,
    __focusedCol : null,

    // sparse array to cache rendered rows
    __rowCache : null,
    __rowCacheCount : 0,


    // property modifier
    _applyFirstVisibleRow : function(value, old) {
      this.updateContent(false, value-old);
    },


    // property modifier
    _applyVisibleRowCount : function(value, old) {
      this.updateContent(true);
    },


    // overridden
    _getContentHint : function()
    {
      // the preferred height is 400 pixel. We don't use rowCount * rowHeight
      // because this is typically too large.
      return {
        width: this.getPaneScroller().getTablePaneModel().getTotalWidth(),
        height: 400
      }
    },


    /**
     * Returns the TablePaneScroller this pane belongs to.
     *
     * @return {qx.ui.table.pane.Scroller} the TablePaneScroller.
     */
    getPaneScroller : function() {
      return this.__paneScroller;
    },


    /**
     * Returns the table this pane belongs to.
     *
     * @return {qx.ui.table.Table} the table.
     */
    getTable : function() {
      return this.__paneScroller.getTable();
    },


    /**
     * Sets the currently focused cell.
     *
     * @param col {Integer?null} the model index of the focused cell's column.
     * @param row {Integer?null} the model index of the focused cell's row.
     * @param massUpdate {Boolean ? false} Whether other updates are planned as well.
     *          If true, no repaint will be done.
     * @return {void}
     */
    setFocusedCell : function(col, row, massUpdate)
    {
      if (col != this.__focusedCol || row != this.__focusedRow)
      {
        var oldRow = this.__focusedRow;
        this.__focusedCol = col;
        this.__focusedRow = row;

        // Update the focused row background
        if (row != oldRow && !massUpdate)
        {
          if (oldRow !== null) {
            this.updateContent(false, null, oldRow, true);
          }
          if (row !== null) {
            this.updateContent(false, null, row, true);
          }
        }
      }
    },


    /**
     * Event handler. Called when the selection has changed.
     */
    onSelectionChanged : function() {
      this.updateContent(false, null, null, true);
    },


    /**
     * Event handler. Called when the table gets or looses the focus.
     */
    onFocusChanged : function() {
      this.updateContent(false, null, null, true);
    },


    /**
     * Sets the column width.
     *
     * @param col {Integer} the column to change the width for.
     * @param width {Integer} the new width.
     * @return {void}
     */
    setColumnWidth : function(col, width) {
      this.updateContent(true);
    },


    /**
     * Event handler. Called the column order has changed.
     *
     * @return {void}
     */
    onColOrderChanged : function() {
      this.updateContent(true);
    },


    /**
     * Event handler. Called when the pane model has changed.
     */
    onPaneModelChanged : function() {
      this.updateContent(true);
    },


    /**
     * Event handler. Called when the table model data has changed.
     *
     * @param firstRow {Integer} The index of the first row that has changed.
     * @param lastRow {Integer} The index of the last row that has changed.
     * @param firstColumn {Integer} The model index of the first column that has changed.
     * @param lastColumn {Integer} The model index of the last column that has changed.
     */
    onTableModelDataChanged : function(firstRow, lastRow, firstColumn, lastColumn)
    {
      this.__rowCacheClear();

      var paneFirstRow = this.getFirstVisibleRow();
      var rowCount = this.getVisibleRowCount();

      if (lastRow == -1 || lastRow >= paneFirstRow && firstRow < paneFirstRow + rowCount)
      {
        // The change intersects this pane
        this.updateContent();
      }
    },


    /**
     * Event handler. Called when the table model meta data has changed.
     *
     * @return {void}
     */
    onTableModelMetaDataChanged : function() {
      this.updateContent(true);
    },


    // property apply method
    _applyMaxCacheLines : function(value, old)
    {
      if (this.__rowCacheCount >= value && value !== -1) {
        this.__rowCacheClear();
      }
    },


    /**
     * Clear the row cache
     */
    __rowCacheClear : function()
    {
      this.__rowCache = [];
      this.__rowCacheCount = 0;
    },


    /**
     * Get a line from the row cache.
     *
     * @param row {Integer} Row index to get
     * @param selected {Boolean} Whether the row is currently selected
     * @param focused {Boolean} Whether the row is currently focused
     * @return {String|null} The cached row or null if a row with the given
     *     index is not cached.
     */
    __rowCacheGet : function(row, selected, focused)
    {
      if (!selected && !focused && this.__rowCache[row]) {
        return this.__rowCache[row];
      } else {
        return null;
      }
    },


    /**
     * Add a line to the row cache.
     *
     * @param row {Integer} Row index to set
     * @param rowString {String} computed row string to cache
     * @param selected {Boolean} Whether the row is currently selected
     * @param focused {Boolean} Whether the row is currently focused
     */
    __rowCacheSet : function(row, rowString, selected, focused)
    {
      var maxCacheLines = this.getMaxCacheLines();
      if (
        !selected &&
        !focused &&
        !this.__rowCache[row] &&
        maxCacheLines > 0
      ) {
        this._applyMaxCacheLines(maxCacheLines);
        this.__rowCache[row] = rowString;
        this.__rowCacheCount += 1;
      }
    },


    /**
     * Updates the content of the pane.
     *
     * @param completeUpdate {Boolean ? false} if true a complete update is performed.
     *      On a complete update all cell widgets are recreated.
     * @param scrollOffset {Integer ? null} If set specifies how many rows to scroll.
     * @param onlyRow {Integer ? null} if set only the specified row will be updated.
     * @param onlySelectionOrFocusChanged {Boolean ? false} if true, cell values won't
     *          be updated. Only the row background will.
     * @return {void}
     */
    updateContent : function(completeUpdate, scrollOffset, onlyRow, onlySelectionOrFocusChanged)
    {
      if (completeUpdate) {
        this.__rowCacheClear();
      }

      //var start = new Date();

      if (scrollOffset && Math.abs(scrollOffset) <= Math.min(10, this.getVisibleRowCount()))
      {
        //this.debug("scroll", scrollOffset);
        this._scrollContent(scrollOffset);
      }
      else if (onlySelectionOrFocusChanged && !this.getTable().getAlwaysUpdateCells())
      {
        //this.debug("update row styles");
        this._updateRowStyles(onlyRow);
      }
      else
      {
        //this.debug("full update");
        this._updateAllRows();
      }

      //this.debug("render time: " + (new Date() - start) + "ms");
    },


    /**
     * If only focus or selection changes it is sufficient to only update the
     * row styles. This method updates the row styles of all visible rows or
     * of just one row.
     *
     * @param onlyRow {Integer|null ? null} If this parameter is set only the row
     *     with this index is updated.
     */
    _updateRowStyles : function(onlyRow)
    {
      var elem = this.getContentElement().getDomElement();

      if (!elem || !elem.firstChild) {
        this._updateAllRows();
        return;
      }

      var table = this.getTable();
      var selectionModel = table.getSelectionModel();
      var tableModel = table.getTableModel();
      var rowRenderer = table.getDataRowRenderer();
      var rowNodes = elem.firstChild.childNodes;
      var cellInfo = { table : table };

      // We don't want to execute the row loop below more than necessary. If
      // onlyrow is not null, we want to do the loop only for that row.
      // In that case, we start at (set the "row" variable to) that row, and
      // stop at (set the "end" variable to the offset of) the next row.
      var row = this.getFirstVisibleRow();
      var y = 0;

      // How many rows do we need to update?
      var end = rowNodes.length;

      if (onlyRow != null)
      {
        // How many rows are we skipping?
        var offset = onlyRow - row;
        if (offset >= 0 && offset < end)
        {
          row = onlyRow;
          y = offset;
          end = offset + 1;
        } else
        {
          return;
        }
      }

      for (; y<end; y++, row++)
      {
        cellInfo.row = row;
        cellInfo.selected = selectionModel.isSelectedIndex(row);
        cellInfo.focusedRow = (this.__focusedRow == row);
        cellInfo.rowData = tableModel.getRowData(row);

        rowRenderer.updateDataRowElement(cellInfo, rowNodes[y]);
      };
    },


    /**
     * Get the HTML table fragment for the given row range.
     *
     * @param firstRow {Integer} Index of the first row
     * @param rowCount {Integer} Number of rows
     * @return {String} The HTML table fragment for the given row range.
     */
    _getRowsHtml : function(firstRow, rowCount)
    {
      var table = this.getTable();

      var selectionModel = table.getSelectionModel();
      var tableModel = table.getTableModel();
      var columnModel = table.getTableColumnModel();
      var paneModel = this.getPaneScroller().getTablePaneModel();
      var rowRenderer = table.getDataRowRenderer();

      tableModel.prefetchRows(firstRow, firstRow + rowCount - 1);

      var rowHeight = table.getRowHeight();
      var colCount = paneModel.getColumnCount();
      var left = 0;
      var cols = [];

      // precompute column properties
      for (var x=0; x<colCount; x++)
      {
        var col = paneModel.getColumnAtX(x);
        var cellWidth = columnModel.getColumnWidth(col);
        cols.push({
          col: col,
          xPos: x,
          editable: tableModel.isColumnEditable(col),
          focusedCol: this.__focusedCol == col,
          styleLeft: left,
          styleWidth: cellWidth
        });

        left += cellWidth;
      }

      var rowsArr = [];
      var paneReloadsData = false;
      for (var row=firstRow; row < firstRow + rowCount; row++)
      {
        var selected = selectionModel.isSelectedIndex(row);
        var focusedRow = (this.__focusedRow == row);

        var cachedRow = this.__rowCacheGet(row, selected, focusedRow);
        if (cachedRow) {
          rowsArr.push(cachedRow);
          continue;
        }

        var rowHtml = [];

        var cellInfo = { table : table };
        cellInfo.styleHeight = rowHeight;

        cellInfo.row = row;
        cellInfo.selected = selected;
        cellInfo.focusedRow = focusedRow;
        cellInfo.rowData = tableModel.getRowData(row);

        if (!cellInfo.rowData) {
          paneReloadsData = true;
        }

        rowHtml.push('<div ');

        var rowAttributes = rowRenderer.getRowAttributes(cellInfo);
        if (rowAttributes) {
          rowHtml.push(rowAttributes);
        }

        var rowClass = rowRenderer.getRowClass(cellInfo);
        if (rowClass) {
          rowHtml.push('class="', rowClass, '" ');
        }

        var rowStyle = rowRenderer.createRowStyle(cellInfo);
        rowStyle += ";position:relative;" + rowRenderer.getRowHeightStyle(rowHeight)+ "width:100%;";
        if (rowStyle) {
          rowHtml.push('style="', rowStyle, '" ');
        }
        rowHtml.push('>');

        var stopLoop = false;
        for (x=0; x<colCount && !stopLoop; x++)
        {
          var col_def = cols[x];
          for (var attr in col_def) {
            cellInfo[attr] = col_def[attr];
          }
          var col = cellInfo.col;

          // Use the "getValue" method of the tableModel to get the cell's
          // value working directly on the "rowData" object
          // (-> cellInfo.rowData[col];) is not a solution because you can't
          // work with the columnIndex -> you have to use the columnId of the
          // columnIndex This is exactly what the method "getValue" does
          cellInfo.value = tableModel.getValue(col, row);
          var cellRenderer = columnModel.getDataCellRenderer(col);

          // Retrieve the current default cell style for this column.
          cellInfo.style = cellRenderer.getDefaultCellStyle();

          // Allow a cell renderer to tell us not to draw any further cells in
          // the row. Older, or traditional cell renderers don't return a
          // value, however, from createDataCellHtml, so assume those are
          // returning false.
          //
          // Tested with http://tinyurl.com/333hyhv
          stopLoop =
            cellRenderer.createDataCellHtml(cellInfo, rowHtml) || false;
        }
        rowHtml.push('</div>');

        var rowString = rowHtml.join("");

        this.__rowCacheSet(row, rowString, selected, focusedRow);
        rowsArr.push(rowString);
      }
      this.fireDataEvent("paneReloadsData", paneReloadsData);
      return rowsArr.join("");
    },


    /**
     * Scrolls the pane's contents by the given offset.
     *
     * @param rowOffset {Integer} Number of lines to scroll. Scrolling up is
     *     represented by a negative offset.
     */
    _scrollContent : function(rowOffset)
    {
      var el = this.getContentElement().getDomElement();
      if (!(el && el.firstChild)) {
        this._updateAllRows();
        return;
      }

      var tableBody = el.firstChild;
      var tableChildNodes = tableBody.childNodes;
      var rowCount = this.getVisibleRowCount();
      var firstRow = this.getFirstVisibleRow();

      var tabelModel = this.getTable().getTableModel();
      var modelRowCount = 0;

      modelRowCount = tabelModel.getRowCount();

      // don't handle this special case here
      if (firstRow + rowCount > modelRowCount) {
        this._updateAllRows();
        return;
      }

      // remove old lines
      var removeRowBase = rowOffset < 0 ? rowCount + rowOffset : 0;
      var addRowBase = rowOffset < 0 ? 0: rowCount - rowOffset;

      for (i=Math.abs(rowOffset)-1; i>=0; i--)
      {
        var rowElem = tableChildNodes[removeRowBase];
        try {
          tableBody.removeChild(rowElem);
        } catch(exp) {
          break;
        }
      }

      // render new lines
      if (!this.__tableContainer) {
        this.__tableContainer = document.createElement("div");
      }
      var tableDummy = '<div>';
      tableDummy += this._getRowsHtml(firstRow + addRowBase, Math.abs(rowOffset));
      tableDummy += '</div>';
      this.__tableContainer.innerHTML = tableDummy;
      var newTableRows = this.__tableContainer.firstChild.childNodes;

      // append new lines
      if (rowOffset > 0)
      {
        for (var i=newTableRows.length-1; i>=0; i--)
        {
          var rowElem = newTableRows[0];
          tableBody.appendChild(rowElem);
        }
      }
      else
      {
        for (var i=newTableRows.length-1; i>=0; i--)
        {
          var rowElem = newTableRows[newTableRows.length-1];
          tableBody.insertBefore(rowElem, tableBody.firstChild);
        }
      }

      // update focus indicator
      if (this.__focusedRow !== null)
      {
        this._updateRowStyles(this.__focusedRow - rowOffset);
        this._updateRowStyles(this.__focusedRow);
      }
      this.fireEvent("paneUpdated");
    },


    /**
     * Updates the content of the pane (implemented using array joins).
     */
    _updateAllRows : function()
    {
      var elem = this.getContentElement().getDomElement();
      if (!elem) {
        // pane has not yet been rendered
        this.addListenerOnce("appear", arguments.callee, this);
        return;
      }

      var table = this.getTable();

      var tableModel = table.getTableModel();
      var paneModel = this.getPaneScroller().getTablePaneModel();

      var colCount = paneModel.getColumnCount();
      var rowHeight = table.getRowHeight();
      var firstRow = this.getFirstVisibleRow();

      var rowCount = this.getVisibleRowCount();
      var modelRowCount = tableModel.getRowCount();

      if (firstRow + rowCount > modelRowCount) {
        rowCount = Math.max(0, modelRowCount - firstRow);
      }

      var rowWidth = paneModel.getTotalWidth();
      var htmlArr;

      // If there are any rows...
      if (rowCount > 0)
      {
        // ... then create a div for them and add the rows to it.
        htmlArr =
          [
            "<div style='",
            "width: 100%;",
            (table.getForceLineHeight()
             ? "line-height: " + rowHeight + "px;"
             : ""),
            "overflow: hidden;",
            "'>",
            this._getRowsHtml(firstRow, rowCount),
            "</div>"
          ];
      }
      else
      {
        // Otherwise, don't create the div, as even an empty div creates a
        // white row in IE.
        htmlArr = [];
      }

      var data = htmlArr.join("");
      elem.innerHTML = data;
      this.setWidth(rowWidth);

      this.__lastColCount = colCount;
      this.__lastRowCount = rowCount;
      this.fireEvent("paneUpdated");
    }

  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    this.__tableContainer = this.__paneScroller = this.__rowCache = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * Shows the header of a table.
 */
qx.Class.define("qx.ui.table.pane.Header",
{
  extend : qx.ui.core.Widget,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param paneScroller {qx.ui.table.pane.Scroller} the TablePaneScroller the header belongs to.
   */
  construct : function(paneScroller)
  {
    this.base(arguments);
    this._setLayout(new qx.ui.layout.HBox());

    // add blocker
    this.__blocker = new qx.ui.core.Blocker(this);

    this.__paneScroller = paneScroller;
  },






  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __paneScroller : null,
    __moveFeedback : null,
    __lastMouseOverColumn : null,
    __blocker : null,

    /**
     * Returns the TablePaneScroller this header belongs to.
     *
     * @return {qx.ui.table.pane.Scroller} the TablePaneScroller.
     */
    getPaneScroller : function() {
      return this.__paneScroller;
    },


    /**
     * Returns the table this header belongs to.
     *
     * @return {qx.ui.table.Table} the table.
     */
    getTable : function() {
      return this.__paneScroller.getTable();
    },

    /**
     * Returns the blocker of the header.
     *
     * @return {qx.ui.core.Blocker} the blocker.
     */
    getBlocker : function() {
      return this.__blocker;
    },

    /**
     * Event handler. Called the column order has changed.
     *
     * @return {void}
     */
    onColOrderChanged : function() {
      this._updateContent(true);
    },


    /**
     * Event handler. Called when the pane model has changed.
     */
    onPaneModelChanged : function() {
      this._updateContent(true);
    },


    /**
     * Event handler. Called when the table model meta data has changed.
     *
     * @return {void}
     */
    onTableModelMetaDataChanged : function() {
      this._updateContent();
    },


    /**
     * Sets the column width. This overrides the width from the column model.
     *
     * @param col {Integer}
     *   The column to change the width for.
     *
     * @param width {Integer}
     *   The new width.
     *
     * @param isMouseAction {Boolean}
     *   <i>true</i> if the column width is being changed as a result of a
     *   mouse drag in the header; false or undefined otherwise.
     *
     * @return {void}
     */
    setColumnWidth : function(col, width, isMouseAction)
    {
      var child = this.getHeaderWidgetAtColumn(col);

      if (child != null) {
        child.setWidth(width);
      }
    },


    /**
     * Sets the column the mouse is currently over.
     *
     * @param col {Integer} the model index of the column the mouse is currently over or
     *      null if the mouse is over no column.
     * @return {void}
     */
    setMouseOverColumn : function(col)
    {
      if (col != this.__lastMouseOverColumn)
      {
        if (this.__lastMouseOverColumn != null)
        {
          var widget = this.getHeaderWidgetAtColumn(this.__lastMouseOverColumn);

          if (widget != null) {
            widget.removeState("hovered");
          }
        }

        if (col != null) {
          this.getHeaderWidgetAtColumn(col).addState("hovered");
        }

        this.__lastMouseOverColumn = col;
      }
    },


    /**
     * Get the header widget for the given column
     *
     * @param col {Integer} The column number
     * @return {qx.ui.table.headerrenderer.HeaderCell} The header cell widget
     */
    getHeaderWidgetAtColumn : function(col)
    {
      var xPos = this.getPaneScroller().getTablePaneModel().getX(col);
      return this._getChildren()[xPos];
    },


    /**
     * Shows the feedback shown while a column is moved by the user.
     *
     * @param col {Integer} the model index of the column to show the move feedback for.
     * @param x {Integer} the x position the left side of the feeback should have
     *      (in pixels, relative to the left side of the header).
     * @return {void}
     */
    showColumnMoveFeedback : function(col, x)
    {
      var pos = this.getContainerLocation();

      if (this.__moveFeedback == null)
      {
        var table = this.getTable();
        var xPos = this.getPaneScroller().getTablePaneModel().getX(col);
        var cellWidget = this._getChildren()[xPos];

        var tableModel = table.getTableModel();
        var columnModel = table.getTableColumnModel();

        var cellInfo =
        {
          xPos  : xPos,
          col   : col,
          name  : tableModel.getColumnName(col),
          table : table
        };

        var cellRenderer = columnModel.getHeaderCellRenderer(col);
        var feedback = cellRenderer.createHeaderCell(cellInfo);

        var size = cellWidget.getBounds();

        // Configure the feedback
        feedback.setWidth(size.width);
        feedback.setHeight(size.height);
        feedback.setZIndex(1000000);
        feedback.setOpacity(0.8);
        feedback.setLayoutProperties({top: pos.top});

        this.getApplicationRoot().add(feedback);
        this.__moveFeedback = feedback;
      }

      this.__moveFeedback.setLayoutProperties({left: pos.left + x});
      this.__moveFeedback.show();
    },


    /**
     * Hides the feedback shown while a column is moved by the user.
     */
    hideColumnMoveFeedback : function()
    {
      if (this.__moveFeedback != null)
      {
        this.__moveFeedback.destroy();
        this.__moveFeedback = null;
      }
    },


    /**
     * Returns whether the column move feedback is currently shown.
     *
     * @return {Boolean} <code>true</code> whether the column move feedback is
     *    currently shown, <code>false</code> otherwise.
     */
    isShowingColumnMoveFeedback : function() {
      return this.__moveFeedback != null;
    },


    /**
     * Updates the content of the header.
     *
     * @param completeUpdate {Boolean} if true a complete update is performed. On a
     *      complete update all header widgets are recreated.
     * @return {void}
     */
    _updateContent : function(completeUpdate)
    {
      var table = this.getTable();
      var tableModel = table.getTableModel();
      var columnModel = table.getTableColumnModel();
      var paneModel = this.getPaneScroller().getTablePaneModel();

      var children = this._getChildren();
      var colCount = paneModel.getColumnCount();

      var sortedColumn = tableModel.getSortColumnIndex();

      // Remove all widgets on the complete update
      if (completeUpdate) {
        this._cleanUpCells();
      }

      // Update the header
      var cellInfo = {};
      cellInfo.sortedAscending = tableModel.isSortAscending();

      for (var x=0; x<colCount; x++)
      {
        var col = paneModel.getColumnAtX(x);
        if (col === undefined) {
          continue;
        }

        var colWidth = columnModel.getColumnWidth(col);

        // TODO: Get real cell renderer
        var cellRenderer = columnModel.getHeaderCellRenderer(col);

        cellInfo.xPos = x;
        cellInfo.col = col;
        cellInfo.name = tableModel.getColumnName(col);
        cellInfo.editable = tableModel.isColumnEditable(col);
        cellInfo.sorted = (col == sortedColumn);
        cellInfo.table = table;

        // Get the cached widget
        var cachedWidget = children[x];

        // Create or update the widget
        if (cachedWidget == null)
        {
          // We have no cached widget -> create it
          cachedWidget = cellRenderer.createHeaderCell(cellInfo);

          cachedWidget.set(
          {
            width  : colWidth
          });

          this._add(cachedWidget);
        }
        else
        {
          // This widget already created before -> recycle it
          cellRenderer.updateHeaderCell(cellInfo, cachedWidget);
        }

        // set the states
        if (x === 0) {
          cachedWidget.addState("first");
          cachedWidget.removeState("last");
        } else if (x === colCount - 1) {
          cachedWidget.removeState("first");
          cachedWidget.addState("last");
        } else {
          cachedWidget.removeState("first");
          cachedWidget.removeState("last");
        }
      }
    },


    /**
     * Cleans up all header cells.
     *
     * @return {void}
     */
    _cleanUpCells : function()
    {
      var children = this._getChildren();

      for (var x=children.length-1; x>=0; x--)
      {
        var cellWidget = children[x];
        cellWidget.destroy();
      }
    }
  },



  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function()
  {
    this.__blocker.dispose();
    this._disposeObjects("__paneScroller");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * Shows a whole meta column. This includes a {@link Header},
 * a {@link Pane} and the needed scroll bars. This class handles the
 * virtual scrolling and does all the mouse event handling.
 *
 * @childControl header {qx.ui.table.pane.Header} header pane
 * @childControl pane {qx.ui.table.pane.Pane} table pane to show the data
 * @childControl focus-indicator {qx.ui.table.pane.FocusIndicator} shows the current focused cell
 * @childControl resize-line {qx.ui.core.Widget} resize line widget
 * @childControl scrollbar-x {qx.ui.core.scroll.ScrollBar?qx.ui.core.scroll.NativeScrollBar}
 *               horizontal scrollbar widget (depends on the "qx.nativeScrollBars" setting which implementation is used)
 * @childControl scrollbar-y {qx.ui.core.scroll.ScrollBar?qx.ui.core.scroll.NativeScrollBar}
 *               vertical scrollbar widget (depends on the "qx.nativeScrollBars" setting which implementation is used)
 */
qx.Class.define("qx.ui.table.pane.Scroller",
{
  extend : qx.ui.core.Widget,
  include : qx.ui.core.scroll.MScrollBarFactory,



  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * @param table {qx.ui.table.Table} the table the scroller belongs to.
   */
  construct : function(table)
  {
    this.base(arguments);

    this.__table = table;

    // init layout
    var grid = new qx.ui.layout.Grid();
    grid.setColumnFlex(0, 1);
    grid.setRowFlex(1, 1);
    this._setLayout(grid);

    // init child controls
    this.__horScrollBar = this._showChildControl("scrollbar-x");
    this.__verScrollBar = this._showChildControl("scrollbar-y");
    this.__header = this._showChildControl("header");
    this.__tablePane = this._showChildControl("pane");

    // the top line containing the header clipper and the top right widget
    this.__top = new qx.ui.container.Composite(new qx.ui.layout.HBox()).set({
      minWidth: 0
    });
    this._add(this.__top, {row: 0, column: 0, colSpan: 2});

    // embed header into a scrollable container
    this.__headerClipper = new qx.ui.table.pane.Clipper();
    this.__headerClipper.add(this.__header);
    this.__headerClipper.addListener("losecapture", this._onChangeCaptureHeader, this);
    this.__headerClipper.addListener("mousemove", this._onMousemoveHeader, this);
    this.__headerClipper.addListener("mousedown", this._onMousedownHeader, this);
    this.__headerClipper.addListener("mouseup", this._onMouseupHeader, this);
    this.__headerClipper.addListener("click", this._onClickHeader, this);
    this.__top.add(this.__headerClipper, {flex: 1});

    // embed pane into a scrollable container
    this.__paneClipper = new qx.ui.table.pane.Clipper();
    this.__paneClipper.add(this.__tablePane);
    this.__paneClipper.addListener("mousewheel", this._onMousewheel, this);
    this.__paneClipper.addListener("mousemove", this._onMousemovePane, this);
    this.__paneClipper.addListener("mousedown", this._onMousedownPane, this);
    this.__paneClipper.addListener("mouseup", this._onMouseupPane, this);
    this.__paneClipper.addListener("click", this._onClickPane, this);
    this.__paneClipper.addListener("contextmenu", this._onContextMenu, this);
    this.__paneClipper.addListener("dblclick", this._onDblclickPane, this);
    this.__paneClipper.addListener("resize", this._onResizePane, this);

    this._add(this.__paneClipper, {row: 1, column: 0});

    // init focus indicator
    this.__focusIndicator = this.getChildControl("focus-indicator");
    // need to run the apply method at least once [BUG #4057]
    this.initShowCellFocusIndicator();

    // force creation of the resize line
    this.getChildControl("resize-line").hide();

    this.addListener("mouseout", this._onMouseout, this);
    this.addListener("appear", this._onAppear, this);
    this.addListener("disappear", this._onDisappear, this);

    this.__timer = new qx.event.Timer();
    this.__timer.addListener("interval", this._oninterval, this);
    this.initScrollTimeout();

  },




  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {

    /** {int} The minimum width a column could get in pixels. */
    MIN_COLUMN_WIDTH         : 10,

    /** {int} The radius of the resize region in pixels. */
    RESIZE_REGION_RADIUS     : 5,


    /**
     * (int) The number of pixels the mouse may move between mouse down and mouse up
     * in order to count as a click.
     */
    CLICK_TOLERANCE          : 5,


    /**
     * (int) The mask for the horizontal scroll bar.
     * May be combined with {@link #VERTICAL_SCROLLBAR}.
     *
     * @see #getNeededScrollBars
     */
    HORIZONTAL_SCROLLBAR     : 1,


    /**
     * (int) The mask for the vertical scroll bar.
     * May be combined with {@link #HORIZONTAL_SCROLLBAR}.
     *
     * @see #getNeededScrollBars
     */
    VERTICAL_SCROLLBAR       : 2
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  events :
  {
    /** Dispatched if the pane is scolled horizontally */
    "changeScrollY" : "qx.event.type.Data",

    /** Dispatched if the pane is scrolled vertically */
    "changeScrollX" : "qx.event.type.Data",

    /**See {@link qx.ui.table.Table#cellClick}.*/
    "cellClick" : "qx.ui.table.pane.CellEvent",

    /*** See {@link qx.ui.table.Table#cellDblclick}.*/
    "cellDblclick" : "qx.ui.table.pane.CellEvent",

    /**See {@link qx.ui.table.Table#cellContextmenu}.*/
    "cellContextmenu" : "qx.ui.table.pane.CellEvent",

    /** Dispatched when a sortable header was clicked */
    "beforeSort" : "qx.event.type.Data"
  },





  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {

    /** Whether to show the horizontal scroll bar */
    horizontalScrollBarVisible :
    {
      check : "Boolean",
      init : false,
      apply : "_applyHorizontalScrollBarVisible",
      event : "changeHorizontalScrollBarVisible"
    },

    /** Whether to show the vertical scroll bar */
    verticalScrollBarVisible :
    {
      check : "Boolean",
      init : false,
      apply : "_applyVerticalScrollBarVisible",
      event : "changeVerticalScrollBarVisible"
    },

    /** The table pane model. */
    tablePaneModel :
    {
      check : "qx.ui.table.pane.Model",
      apply : "_applyTablePaneModel",
      event : "changeTablePaneModel"
    },


    /**
     * Whether column resize should be live. If false, during resize only a line is
     * shown and the real resize happens when the user releases the mouse button.
     */
    liveResize :
    {
      check : "Boolean",
      init : false
    },


    /**
     * Whether the focus should moved when the mouse is moved over a cell. If false
     * the focus is only moved on mouse clicks.
     */
    focusCellOnMouseMove :
    {
      check : "Boolean",
      init : false
    },


    /**
     * Whether to handle selections via the selection manager before setting the
     * focus.  The traditional behavior is to handle selections after setting the
     * focus, but setting the focus means redrawing portions of the table, and
     * some subclasses may want to modify the data to be displayed based on the
     * selection.
     */
    selectBeforeFocus :
    {
      check : "Boolean",
      init : false
    },


    /**
     * Whether the cell focus indicator should be shown
     */
    showCellFocusIndicator :
    {
      check : "Boolean",
      init : true,
      apply : "_applyShowCellFocusIndicator"
    },


    /**
     * By default, the "cellContextmenu" event is fired only when a data cell
     * is right-clicked. It is not fired when a right-click occurs in the
     * empty area of the table below the last data row. By turning on this
     * property, "cellContextMenu" events will also be generated when a
     * right-click occurs in that empty area. In such a case, row identifier
     * in the event data will be null, so event handlers can check (row ===
     * null) to handle this case.
     */
    contextMenuFromDataCellsOnly :
    {
      check : "Boolean",
      init : true
    },


    /**
     * Whether to reset the selection when a header cell is clicked. Since
     * most data models do not have provisions to retain a selection after
     * sorting, the default is to reset the selection in this case. Some data
     * models, however, do have the capability to retain the selection, so
     * when using those, this property should be set to false.
     */
    resetSelectionOnHeaderClick :
    {
      check : "Boolean",
      init : true
    },


    /**
     * Interval time (in milliseconds) for the table update timer.
     * Setting this to 0 clears the timer.
     */
    scrollTimeout :
    {
      check : "Integer",
      init : 100,
      apply : "_applyScrollTimeout"
    },


    appearance :
    {
      refine : true,
      init : "table-scroller"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __lastRowCount : null,
    __table : null,

    __updateInterval : null,
    __updateContentPlanned : null,
    __onintervalWrapper : null,

    __moveColumn : null,
    __lastMoveColPos : null,
    __lastMoveTargetX : null,
    __lastMoveTargetScroller : null,
    __lastMoveMousePageX : null,

    __resizeColumn : null,
    __lastResizeMousePageX : null,
    __lastResizeWidth : null,

    __lastMouseDownCell : null,
    __firedClickEvent : false,
    __ignoreClick : null,
    __lastMousePageX : null,
    __lastMousePageY : null,

    __focusedCol : null,
    __focusedRow : null,

    __cellEditor : null,
    __cellEditorFactory : null,

    __topRightWidget : null,
    __horScrollBar : null,
    __verScrollBar : null,
    __header : null,
    __headerClipper : null,
    __tablePane : null,
    __paneClipper : null,
    __focusIndicator : null,
    __top : null,

    __timer : null,


    /**
     * The right inset of the pane. The right inset is the maximum of the
     * top right widget width and the scrollbar width (if visible).
     *
     * @return {Integer} The right inset of the pane
     */
    getPaneInsetRight : function()
    {
      var topRight = this.getTopRightWidget();
      var topRightWidth =
        topRight && topRight.isVisible() && topRight.getBounds() ?
          topRight.getBounds().width + topRight.getMarginLeft() + topRight.getMarginRight() :
          0;

      var scrollBar = this.__verScrollBar;
      var scrollBarWidth = this.getVerticalScrollBarVisible() ?
        this.getVerticalScrollBarWidth() + scrollBar.getMarginLeft() + scrollBar.getMarginRight() :
        0;

      return Math.max(topRightWidth, scrollBarWidth);
    },


    /**
     * Set the pane's width
     *
     * @param width {Integer} The pane's width
     */
    setPaneWidth : function(width)
    {
      if (this.isVerticalScrollBarVisible()) {
        width += this.getPaneInsetRight();
      }
      this.setWidth(width);
    },


    // overridden
    _createChildControlImpl : function(id, hash)
    {
      var control;

      switch(id)
      {
        case "header":
          control = (this.getTable().getNewTablePaneHeader())(this);
          break;

        case "pane":
          control = (this.getTable().getNewTablePane())(this);
          break;

        case "focus-indicator":
          control = new qx.ui.table.pane.FocusIndicator(this);
          control.setUserBounds(0, 0, 0, 0);
          control.setZIndex(1000);
          control.addListener("mouseup", this._onMouseupFocusIndicator, this);
          this.__paneClipper.add(control);
          control.show();             // must be active for editor to operate
          control.setDecorator(null); // it can be initially invisible, though.
          break;

        case "resize-line":
          control = new qx.ui.core.Widget();
          control.setUserBounds(0, 0, 0, 0);
          control.setZIndex(1000);
          this.__paneClipper.add(control);
          break;

        case "scrollbar-x":
          control = this._createScrollBar("horizontal").set({
            minWidth: 0,
            alignY: "bottom"
          });
          control.addListener("scroll", this._onScrollX, this);
          this._add(control, {row: 2, column: 0});
          break;

        case "scrollbar-y":
          control = this._createScrollBar("vertical");
          control.addListener("scroll", this._onScrollY, this);
          this._add(control, {row: 1, column: 1});
          break;
      }

      return control || this.base(arguments, id);
    },


    // property modifier
    _applyHorizontalScrollBarVisible : function(value, old) {
      this.__horScrollBar.setVisibility(value ? "visible" : "excluded");
    },


    // property modifier
    _applyVerticalScrollBarVisible : function(value, old) {
      this.__verScrollBar.setVisibility(value ? "visible" : "excluded");
    },


    // property modifier
    _applyTablePaneModel : function(value, old)
    {
      if (old != null) {
        old.removeListener("modelChanged", this._onPaneModelChanged, this);
      }

      value.addListener("modelChanged", this._onPaneModelChanged, this);
    },


    // property modifier
    _applyShowCellFocusIndicator : function(value, old)
    {
      if(value) {
        this.__focusIndicator.setDecorator("table-scroller-focus-indicator");
        this._updateFocusIndicator();
      }
      else {
        if(this.__focusIndicator) {
          this.__focusIndicator.setDecorator(null);
        }
      }
    },


    /**
     * Get the current position of the vertical scroll bar.
     *
     * @return {Integer} The current scroll position.
     */
    getScrollY : function() {
      return this.__verScrollBar.getPosition();
    },


    /**
     * Set the current position of the vertical scroll bar.
     *
     * @param scrollY {Integer} The new scroll position.
     * @param renderSync {Boolean?false} Whether the table update should be
     *     performed synchonously.
     */
    setScrollY : function(scrollY, renderSync)
    {
      this.__verScrollBar.scrollTo(scrollY);
      if (renderSync) {
        this._updateContent();
      }
    },


    /**
     * Get the current position of the vertical scroll bar.
     *
     * @return {Integer} The current scroll position.
     */
    getScrollX : function() {
      return this.__horScrollBar.getPosition();
    },


    /**
     * Set the current position of the vertical scroll bar.
     *
     * @param scrollX {Integer} The new scroll position.
     */
    setScrollX : function(scrollX) {
      this.__horScrollBar.scrollTo(scrollX);
    },


    /**
     * Returns the table this scroller belongs to.
     *
     * @return {qx.ui.table.Table} the table.
     */
    getTable : function() {
      return this.__table;
    },


    /**
     * Event handler. Called when the visibility of a column has changed.
     */
    onColVisibilityChanged : function()
    {
      this.updateHorScrollBarMaximum();
      this._updateFocusIndicator();
    },


    /**
     * Sets the column width.
     *
     * @param col {Integer} the column to change the width for.
     * @param width {Integer} the new width.
     * @return {void}
     */
    setColumnWidth : function(col, width)
    {
      this.__header.setColumnWidth(col, width);
      this.__tablePane.setColumnWidth(col, width);

      var paneModel = this.getTablePaneModel();
      var x = paneModel.getX(col);

      if (x != -1)
      {
        // The change was in this scroller
        this.updateHorScrollBarMaximum();
        this._updateFocusIndicator();
      }
    },


    /**
     * Event handler. Called when the column order has changed.
     *
     * @return {void}
     */
    onColOrderChanged : function()
    {
      this.__header.onColOrderChanged();
      this.__tablePane.onColOrderChanged();

      this.updateHorScrollBarMaximum();
    },


    /**
     * Event handler. Called when the table model has changed.
     *
     * @param firstRow {Integer} The index of the first row that has changed.
     * @param lastRow {Integer} The index of the last row that has changed.
     * @param firstColumn {Integer} The model index of the first column that has changed.
     * @param lastColumn {Integer} The model index of the last column that has changed.
     */
    onTableModelDataChanged : function(firstRow, lastRow, firstColumn, lastColumn)
    {
      this.__tablePane.onTableModelDataChanged(firstRow, lastRow, firstColumn, lastColumn);
      var rowCount = this.getTable().getTableModel().getRowCount();

      if (rowCount != this.__lastRowCount)
      {
        this.updateVerScrollBarMaximum();

        if (this.getFocusedRow() >= rowCount)
        {
          if (rowCount == 0) {
            this.setFocusedCell(null, null);
          } else {
            this.setFocusedCell(this.getFocusedColumn(), rowCount - 1);
          }
        }
        this.__lastRowCount = rowCount;
      }
    },


    /**
     * Event handler. Called when the selection has changed.
     */
    onSelectionChanged : function() {
      this.__tablePane.onSelectionChanged();
    },


    /**
     * Event handler. Called when the table gets or looses the focus.
     */
    onFocusChanged : function() {
      this.__tablePane.onFocusChanged();
    },


    /**
     * Event handler. Called when the table model meta data has changed.
     *
     * @return {void}
     */
    onTableModelMetaDataChanged : function()
    {
      this.__header.onTableModelMetaDataChanged();
      this.__tablePane.onTableModelMetaDataChanged();
    },


    /**
     * Event handler. Called when the pane model has changed.
     */
    _onPaneModelChanged : function()
    {
      this.__header.onPaneModelChanged();
      this.__tablePane.onPaneModelChanged();
    },


    /**
     * Event listener for the pane clipper's resize event
     */
    _onResizePane : function()
    {
      this.updateHorScrollBarMaximum();
      this.updateVerScrollBarMaximum();

      // The height has changed -> Update content
      this._updateContent();
      this.__header._updateContent();
      this.__table._updateScrollBarVisibility();
    },


    /**
     * Updates the maximum of the horizontal scroll bar, so it corresponds to the
     * total width of the columns in the table pane.
     */
    updateHorScrollBarMaximum : function()
    {
      var paneSize = this.__paneClipper.getInnerSize();
      if (!paneSize) {
        // will be called on the next resize event again
        return;
      }
      var scrollSize = this.getTablePaneModel().getTotalWidth();

      var scrollBar = this.__horScrollBar;

      if (paneSize.width < scrollSize)
      {
        var max = Math.max(0, scrollSize - paneSize.width);

        scrollBar.setMaximum(max);
        scrollBar.setKnobFactor(paneSize.width / scrollSize);

        var pos = scrollBar.getPosition();
        scrollBar.setPosition(Math.min(pos, max));
      }
      else
      {
        scrollBar.setMaximum(0);
        scrollBar.setKnobFactor(1);
        scrollBar.setPosition(0);
      }
    },


    /**
     * Updates the maximum of the vertical scroll bar, so it corresponds to the
     * number of rows in the table.
     */
    updateVerScrollBarMaximum : function()
    {
      var paneSize = this.__paneClipper.getInnerSize();
      if (!paneSize) {
        // will be called on the next resize event again
        return;
      }

      var tableModel = this.getTable().getTableModel();
      var rowCount = tableModel.getRowCount();

      if (this.getTable().getKeepFirstVisibleRowComplete()) {
        rowCount += 1;
      }

      var rowHeight = this.getTable().getRowHeight();
      var scrollSize = rowCount * rowHeight;
      var scrollBar = this.__verScrollBar;

      if (paneSize.height < scrollSize)
      {
        var max = Math.max(0, scrollSize - paneSize.height);

        scrollBar.setMaximum(max);
        scrollBar.setKnobFactor(paneSize.height / scrollSize);

        var pos = scrollBar.getPosition();
        scrollBar.setPosition(Math.min(pos, max));
      }
      else
      {
        scrollBar.setMaximum(0);
        scrollBar.setKnobFactor(1);
        scrollBar.setPosition(0);
      }
    },


    /**
     * Event handler. Called when the table property "keepFirstVisibleRowComplete"
     * changed.
     */
    onKeepFirstVisibleRowCompleteChanged : function()
    {
      this.updateVerScrollBarMaximum();
      this._updateContent();
    },


    /**
     * Event handler for the scroller's appear event
     */
    _onAppear : function()
    {
      // after the Scroller appears we start the interval again
      this._startInterval(this.getScrollTimeout());
    },


    /**
     * Event handler for the disappear event
     */
    _onDisappear : function()
    {
      // before the scroller disappears we need to stop it
      this._stopInterval();
    },


    /**
     * Event handler. Called when the horizontal scroll bar moved.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onScrollX : function(e)
    {
      var scrollLeft = e.getData();

      this.fireDataEvent("changeScrollX", scrollLeft, e.getOldData());
      this.__headerClipper.scrollToX(scrollLeft);
      this.__paneClipper.scrollToX(scrollLeft);
    },


    /**
     * Event handler. Called when the vertical scroll bar moved.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onScrollY : function(e)
    {
      this.fireDataEvent("changeScrollY", e.getData(), e.getOldData());
      this._postponedUpdateContent();
    },


    /**
     * Event handler. Called when the user moved the mouse wheel.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMousewheel : function(e)
    {
      var table = this.getTable();

      if (!table.getEnabled()) {
        return;
      }

      // vertical scrolling
      var delta = e.getWheelDelta("y");
      // normalize that at least one step is scrolled at a time
      if (delta > 0 && delta < 1) {
        delta = 1;
      } else if (delta < 0 && delta > -1) {
        delta = -1;
      }
      this.__verScrollBar.scrollBySteps(delta);

      // horizontal scrolling
      delta = e.getWheelDelta("x");
      // normalize that at least one step is scrolled at a time
      if (delta > 0 && delta < 1) {
        delta = 1;
      } else if (delta < 0 && delta > -1) {
        delta = -1;
      }
      this.__horScrollBar.scrollBySteps(delta);

      // Update the focus
      if (this.__lastMousePageX && this.getFocusCellOnMouseMove()) {
        this._focusCellAtPagePos(this.__lastMousePageX, this.__lastMousePageY);
      }

      var position = this.__verScrollBar.getPosition();
      var max = this.__verScrollBar.getMaximum();
      // pass the event to the parent if the scrollbar is at an edge
      if (delta < 0 && position <= 0 || delta > 0 && position >= max) {
        return;
      }

      e.stop();
    },


    /**
     * Common column resize logic.
     *
     * @param pageX {Integer} the current mouse x position.
     * @return {void}
     */
    __handleResizeColumn : function(pageX)
    {
      var table = this.getTable();
      // We are currently resizing -> Update the position
      var headerCell = this.__header.getHeaderWidgetAtColumn(this.__resizeColumn);
      var minColumnWidth = headerCell.getSizeHint().minWidth;

      var newWidth = Math.max(minColumnWidth, this.__lastResizeWidth + pageX - this.__lastResizeMousePageX);

      if (this.getLiveResize()) {
        var columnModel = table.getTableColumnModel();
        columnModel.setColumnWidth(this.__resizeColumn, newWidth, true);
      } else {
        this.__header.setColumnWidth(this.__resizeColumn, newWidth, true);

        var paneModel = this.getTablePaneModel();
        this._showResizeLine(paneModel.getColumnLeft(this.__resizeColumn) + newWidth);
      }

      this.__lastResizeMousePageX += newWidth - this.__lastResizeWidth;
      this.__lastResizeWidth = newWidth;
    },


    /**
     * Common column move logic.
     *
     * @param pageX {Integer} the current mouse x position.
     * @return {void}
     *
     */
    __handleMoveColumn : function(pageX)
    {
      // We are moving a column

      // Check whether we moved outside the click tolerance so we can start
      // showing the column move feedback
      // (showing the column move feedback prevents the onclick event)
      var clickTolerance = qx.ui.table.pane.Scroller.CLICK_TOLERANCE;
      if (this.__header.isShowingColumnMoveFeedback()
        || pageX > this.__lastMoveMousePageX + clickTolerance
        || pageX < this.__lastMoveMousePageX - clickTolerance)
      {
        this.__lastMoveColPos += pageX - this.__lastMoveMousePageX;

        this.__header.showColumnMoveFeedback(this.__moveColumn, this.__lastMoveColPos);

        // Get the responsible scroller
        var targetScroller = this.__table.getTablePaneScrollerAtPageX(pageX);
        if (this.__lastMoveTargetScroller && this.__lastMoveTargetScroller != targetScroller) {
          this.__lastMoveTargetScroller.hideColumnMoveFeedback();
        }
        if (targetScroller != null) {
          this.__lastMoveTargetX = targetScroller.showColumnMoveFeedback(pageX);
        } else {
          this.__lastMoveTargetX = null;
        }

        this.__lastMoveTargetScroller = targetScroller;
        this.__lastMoveMousePageX = pageX;
      }
    },


    /**
     * Event handler. Called when the user moved the mouse over the header.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMousemoveHeader : function(e)
    {
      var table = this.getTable();

      if (! table.getEnabled()) {
        return;
      }

      var useResizeCursor = false;
      var mouseOverColumn = null;

      var pageX = e.getDocumentLeft();
      var pageY = e.getDocumentTop();

      // Workaround: In onmousewheel the event has wrong coordinates for pageX
      //       and pageY. So we remember the last move event.
      this.__lastMousePageX = pageX;
      this.__lastMousePageY = pageY;

      if (this.__resizeColumn != null)
      {
        // We are currently resizing -> Update the position
        this.__handleResizeColumn(pageX);
        useResizeCursor = true;
        e.stopPropagation();
      }
      else if (this.__moveColumn != null)
      {
        // We are moving a column
        this.__handleMoveColumn(pageX);
        e.stopPropagation();
      }
      else
      {
        var resizeCol = this._getResizeColumnForPageX(pageX);
        if (resizeCol != -1)
        {
          // The mouse is over a resize region -> Show the right cursor
          useResizeCursor = true;
        }
        else
        {
          var tableModel = table.getTableModel();
          var col = this._getColumnForPageX(pageX);
          if (col != null && tableModel.isColumnSortable(col)) {
            mouseOverColumn = col;
          }
        }
      }

      var cursor = useResizeCursor ? "col-resize" : null;
      this.getApplicationRoot().setGlobalCursor(cursor);
      this.setCursor(cursor);
      this.__header.setMouseOverColumn(mouseOverColumn);
    },


    /**
     * Event handler. Called when the user moved the mouse over the pane.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMousemovePane : function(e)
    {
      var table = this.getTable();

      if (! table.getEnabled()) {
        return;
      }

      //var useResizeCursor = false;

      var pageX = e.getDocumentLeft();
      var pageY = e.getDocumentTop();

      // Workaround: In onmousewheel the event has wrong coordinates for pageX
      //       and pageY. So we remember the last move event.
      this.__lastMousePageX = pageX;
      this.__lastMousePageY = pageY;

      var row = this._getRowForPagePos(pageX, pageY);
      if (row != null && this._getColumnForPageX(pageX) != null) {
        // The mouse is over the data -> update the focus
        if (this.getFocusCellOnMouseMove()) {
          this._focusCellAtPagePos(pageX, pageY);
        }
      }
      this.__header.setMouseOverColumn(null);
    },


    /**
     * Event handler. Called when the user pressed a mouse button over the header.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMousedownHeader : function(e)
    {
      if (! this.getTable().getEnabled()) {
        return;
      }

      var pageX = e.getDocumentLeft();

      // mouse is in header
      var resizeCol = this._getResizeColumnForPageX(pageX);
      if (resizeCol != -1)
      {
        // The mouse is over a resize region -> Start resizing
        this._startResizeHeader(resizeCol, pageX);
        e.stop();
      }
      else
      {
        // The mouse is not in a resize region
        var moveCol = this._getColumnForPageX(pageX);
        if (moveCol != null)
        {
          this._startMoveHeader(moveCol, pageX);
          e.stop();
        }
      }
    },


    /**
     * Start a resize session of the header.
     *
     * @param resizeCol {Integer} the column index
     * @param pageX {Integer} x coordinate of the mouse event
     * @return {void}
     */
    _startResizeHeader : function(resizeCol, pageX)
    {
      var columnModel = this.getTable().getTableColumnModel();

      // The mouse is over a resize region -> Start resizing
      this.__resizeColumn = resizeCol;
      this.__lastResizeMousePageX = pageX;
      this.__lastResizeWidth = columnModel.getColumnWidth(this.__resizeColumn);
      this.__headerClipper.capture();
    },


    /**
     * Start a move session of the header.
     *
     * @param moveCol {Integer} the column index
     * @param pageX {Integer} x coordinate of the mouse event
     * @return {void}
     */
    _startMoveHeader : function(moveCol, pageX)
    {
      // Prepare column moving
      this.__moveColumn = moveCol;
      this.__lastMoveMousePageX = pageX;
      this.__lastMoveColPos = this.getTablePaneModel().getColumnLeft(moveCol);
      this.__headerClipper.capture();
    },



    /**
     * Event handler. Called when the user pressed a mouse button over the pane.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMousedownPane : function(e)
    {
      var table = this.getTable();

      if (! table.getEnabled()) {
        return;
      }

      if (table.isEditing()) {
        table.stopEditing();
      }

      var pageX = e.getDocumentLeft();
      var pageY = e.getDocumentTop();
      var row = this._getRowForPagePos(pageX, pageY);
      var col = this._getColumnForPageX(pageX);

      if (row !== null)
      {
        // The focus indicator blocks the click event on the scroller so we
        // store the current cell and listen for the mouseup event on the
        // focus indicator
        //
        // INVARIANT:
        //  The members of this object always contain the last position of
        //  the cell on which the mousedown event occurred.
        //  *** These values are never cleared! ***.
        //  Different browsers/OS combinations issue events in different
        //  orders, and the context menu event, in particular, can be issued
        //  early or late (Firefox on Linux issues it early; Firefox on
        //  Windows issues it late) so no one may clear these values.
        //
        this.__lastMouseDownCell = {
          row : row,
          col : col
        };

        // On the other hand, we need to know if we've issued the click event
        // so we don't issue it twice, both from mouse-up on the focus
        // indicator, and from the click even on the pane. Both possibilities
        // are necessary, however, to maintain the qooxdoo order of events.
        this.__firedClickEvent = false;

        var selectBeforeFocus = this.getSelectBeforeFocus();

        if (selectBeforeFocus) {
          table.getSelectionManager().handleMouseDown(row, e);
        }

        // The mouse is over the data -> update the focus
        if (! this.getFocusCellOnMouseMove()) {
          this._focusCellAtPagePos(pageX, pageY);
        }

        if (! selectBeforeFocus) {
          table.getSelectionManager().handleMouseDown(row, e);
        }
      }
    },


    /**
     * Event handler for the focus indicator's mouseup event
     *
     * @param e {qx.event.type.Mouse} The mouse event
     */
    _onMouseupFocusIndicator : function(e)
    {
      if (this.__lastMouseDownCell &&
          !this.__firedClickEvent &&
          !this.isEditing() &&
          this.__focusIndicator.getRow() == this.__lastMouseDownCell.row &&
          this.__focusIndicator.getColumn() == this.__lastMouseDownCell.col)
      {
        this.fireEvent("cellClick",
                       qx.ui.table.pane.CellEvent,
                       [
                         this,
                         e,
                         this.__lastMouseDownCell.row,
                         this.__lastMouseDownCell.col
                       ],
                       true);
        this.__firedClickEvent = true;
      } else if (!this.isEditing()) {
        // if no cellClick event should be fired, act like a mousedown which
        // invokes the change of the selection e.g. [BUG #1632]
        this._onMousedownPane(e);
      }
    },


    /**
     * Event handler. Called when the event capturing of the header changed.
     * Stops/finishes an active header resize/move session if it lost capturing
     * during the session to stay in a stable state.
     *
     * @param e {qx.event.type.Data} The data event
     */
    _onChangeCaptureHeader : function(e)
    {
      if (this.__resizeColumn != null) {
        this._stopResizeHeader();
      }

      if (this.__moveColumn != null) {
        this._stopMoveHeader();
      }
    },


    /**
     * Stop a resize session of the header.
     *
     * @return {void}
     */
    _stopResizeHeader : function()
    {
      var columnModel = this.getTable().getTableColumnModel();

      // We are currently resizing -> Finish resizing
      if (! this.getLiveResize()) {
        this._hideResizeLine();
        columnModel.setColumnWidth(this.__resizeColumn,
                                   this.__lastResizeWidth,
                                   true);
      }

      this.__resizeColumn = null;
      this.__headerClipper.releaseCapture();

      this.getApplicationRoot().setGlobalCursor(null);
      this.setCursor(null);

      // handle edit cell if available
      if (this.isEditing()) {
        var height = this.__cellEditor.getBounds().height;
        this.__cellEditor.setUserBounds(0, 0, this.__lastResizeWidth, height);
      }
    },


    /**
     * Stop a move session of the header.
     *
     * @return {void}
     */
    _stopMoveHeader : function()
    {
      var columnModel = this.getTable().getTableColumnModel();
      var paneModel = this.getTablePaneModel();

      // We are moving a column -> Drop the column
      this.__header.hideColumnMoveFeedback();
      if (this.__lastMoveTargetScroller) {
        this.__lastMoveTargetScroller.hideColumnMoveFeedback();
      }

      if (this.__lastMoveTargetX != null)
      {
        var fromVisXPos = paneModel.getFirstColumnX() + paneModel.getX(this.__moveColumn);
        var toVisXPos = this.__lastMoveTargetX;
        if (toVisXPos != fromVisXPos && toVisXPos != fromVisXPos + 1)
        {
          // The column was really moved to another position
          // (and not moved before or after itself, which is a noop)

          // Translate visible positions to overall positions
          var fromCol = columnModel.getVisibleColumnAtX(fromVisXPos);
          var toCol   = columnModel.getVisibleColumnAtX(toVisXPos);
          var fromOverXPos = columnModel.getOverallX(fromCol);
          var toOverXPos = (toCol != null) ? columnModel.getOverallX(toCol) : columnModel.getOverallColumnCount();

          if (toOverXPos > fromOverXPos) {
            // Don't count the column itself
            toOverXPos--;
          }

          // Move the column
          columnModel.moveColumn(fromOverXPos, toOverXPos);

          // update the focus indicator including the editor
          this._updateFocusIndicator();
        }
      }

      this.__moveColumn = null;
      this.__lastMoveTargetX = null;
      this.__headerClipper.releaseCapture();
    },


    /**
     * Event handler. Called when the user released a mouse button over the pane.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMouseupPane : function(e)
    {
      var table = this.getTable();

      if (! table.getEnabled()) {
        return;
      }

      var row = this._getRowForPagePos(e.getDocumentLeft(), e.getDocumentTop());
      if (row != -1 && row != null && this._getColumnForPageX(e.getDocumentLeft()) != null) {
        table.getSelectionManager().handleMouseUp(row, e);
      }
    },


    /**
     * Event handler. Called when the user released a mouse button over the header.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMouseupHeader : function(e)
    {
      var table = this.getTable();

      if (! table.getEnabled()) {
        return;
      }

      if (this.__resizeColumn != null)
      {
        this._stopResizeHeader();
        this.__ignoreClick = true;
        e.stop();
      }
      else if (this.__moveColumn != null)
      {
        this._stopMoveHeader();
        e.stop();
      }
    },


    /**
     * Event handler. Called when the user clicked a mouse button over the header.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onClickHeader : function(e)
    {
      if (this.__ignoreClick)
      {
        this.__ignoreClick = false;
        return;
      }

      var table = this.getTable();

      if (!table.getEnabled()) {
        return;
      }

      var tableModel = table.getTableModel();

      var pageX = e.getDocumentLeft();

      var resizeCol = this._getResizeColumnForPageX(pageX);

      if (resizeCol == -1)
      {
        // mouse is not in a resize region
        var col = this._getColumnForPageX(pageX);

        if (col != null && tableModel.isColumnSortable(col))
        {
          // Sort that column
          var sortCol = tableModel.getSortColumnIndex();
          var ascending = (col != sortCol) ? true : !tableModel.isSortAscending();

          var data =
            {
              column     : col,
              ascending  : ascending,
              clickEvent : e
            };

          if (this.fireDataEvent("beforeSort", data, null, true))
          {
            tableModel.sortByColumn(col, ascending);
            if (this.getResetSelectionOnHeaderClick())
            {
              table.getSelectionModel().resetSelection();
            }
          }
        }
      }

      e.stop();
    },


    /**
     * Event handler. Called when the user clicked a mouse button over the pane.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onClickPane : function(e)
    {
      var table = this.getTable();

      if (!table.getEnabled()) {
        return;
      }

      var pageX = e.getDocumentLeft();
      var pageY = e.getDocumentTop();
      var row = this._getRowForPagePos(pageX, pageY);
      var col = this._getColumnForPageX(pageX);

      if (row != null && col != null)
      {
        table.getSelectionManager().handleClick(row, e);

        if (this.__focusIndicator.isHidden() ||
            (this.__lastMouseDownCell &&
             !this.__firedClickEvent &&
             !this.isEditing() &&
             row == this.__lastMouseDownCell.row &&
             col == this.__lastMouseDownCell.col))
        {
          this.fireEvent("cellClick",
                         qx.ui.table.pane.CellEvent,
                         [this, e, row, col],
                         true);
          this.__firedClickEvent = true;
        }
      }
    },


    /**
     * Event handler. Called when a context menu is invoked in a cell.
     *
     * @param e {qx.event.type.Mouse} the event.
     * @return {void}
     */
    _onContextMenu : function(e)
    {
      var pageX = e.getDocumentLeft();
      var pageY = e.getDocumentTop();
      var row = this._getRowForPagePos(pageX, pageY);
      var col = this._getColumnForPageX(pageX);

      /*
       * The 'row' value will be null if the right-click was in the blank
       * area below the last data row. Some applications desire to receive
       * the context menu event anyway, and can set the property value of
       * contextMenuFromDataCellsOnly to false to achieve that.
       */
      if (row === null && this.getContextMenuFromDataCellsOnly())
      {
        return;
      }

      if (! this.getShowCellFocusIndicator() ||
          row === null ||
          (this.__lastMouseDownCell &&
           row == this.__lastMouseDownCell.row &&
           col == this.__lastMouseDownCell.col))
      {
        this.fireEvent("cellContextmenu",
                       qx.ui.table.pane.CellEvent,
                       [this, e, row, col],
                       true);

        // Now that the cellContextmenu handler has had a chance to build
        // the menu for this cell, display it (if there is one).
        var menu = this.getTable().getContextMenu();
        if (menu)
        {
          // A menu with no children means don't display any context menu
          // including the default context menu even if the default context
          // menu is allowed to be displayed normally. There's no need to
          // actually show an empty menu, though.
          if (menu.getChildren().length > 0) {
            menu.openAtMouse(e);
          }
          else
          {
            menu.exclude();
          }

          // Do not show native menu
          e.preventDefault();
        }
      }
    },


    // overridden
    _onContextMenuOpen : function(e)
    {
      // This is Widget's context menu handler which typically retrieves
      // and displays the menu as soon as it receives a "contextmenu" event.
      // We want to allow the cellContextmenu handler to create the menu,
      // so we'll override this method with a null one, and do the menu
      // placement and display handling in our _onContextMenu method.
    },


    /**
     * Event handler. Called when the user double clicked a mouse button over the pane.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onDblclickPane : function(e)
    {
      var pageX = e.getDocumentLeft();
      var pageY = e.getDocumentTop();


      this._focusCellAtPagePos(pageX, pageY);
      this.startEditing();

      var row = this._getRowForPagePos(pageX, pageY);
      if (row != -1 && row != null) {
        this.fireEvent("cellDblclick", qx.ui.table.pane.CellEvent, [this, e, row], true);
      }
    },


    /**
     * Event handler. Called when the mouse moved out.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onMouseout : function(e)
    {
      var table = this.getTable();

      if (!table.getEnabled()) {
        return;
      }

      // Reset the resize cursor when the mouse leaves the header
      // If currently a column is resized then do nothing
      // (the cursor will be reset on mouseup)
      if (this.__resizeColumn == null)
      {
        this.setCursor(null);
        this.getApplicationRoot().setGlobalCursor(null);
      }

      this.__header.setMouseOverColumn(null);
    },


    /**
     * Shows the resize line.
     *
     * @param x {Integer} the position where to show the line (in pixels, relative to
     *      the left side of the pane).
     * @return {void}
     */
    _showResizeLine : function(x)
    {
      var resizeLine = this._showChildControl("resize-line");

      var width = resizeLine.getWidth();
      var paneBounds = this.__paneClipper.getBounds();
      resizeLine.setUserBounds(
        x - Math.round(width/2), 0, width, paneBounds.height
      );
    },


    /**
     * Hides the resize line.
     */
    _hideResizeLine : function() {
      this._excludeChildControl("resize-line");
    },


    /**
     * Shows the feedback shown while a column is moved by the user.
     *
     * @param pageX {Integer} the x position of the mouse in the page (in pixels).
     * @return {Integer} the visible x position of the column in the whole table.
     */
    showColumnMoveFeedback : function(pageX)
    {
      var paneModel = this.getTablePaneModel();
      var columnModel = this.getTable().getTableColumnModel();
      var paneLeft = this.__tablePane.getContainerLocation().left;
      var colCount = paneModel.getColumnCount();

      var targetXPos = 0;
      var targetX = 0;
      var currX = paneLeft;

      for (var xPos=0; xPos<colCount; xPos++)
      {
        var col = paneModel.getColumnAtX(xPos);
        var colWidth = columnModel.getColumnWidth(col);

        if (pageX < currX + colWidth / 2) {
          break;
        }

        currX += colWidth;
        targetXPos = xPos + 1;
        targetX = currX - paneLeft;
      }

      // Ensure targetX is visible
      var scrollerLeft = this.__paneClipper.getContainerLocation().left;
      var scrollerWidth = this.__paneClipper.getBounds().width;
      var scrollX = scrollerLeft - paneLeft;

      // NOTE: +2/-1 because of feedback width
      targetX = qx.lang.Number.limit(targetX, scrollX + 2, scrollX + scrollerWidth - 1);

      this._showResizeLine(targetX);

      // Return the overall target x position
      return paneModel.getFirstColumnX() + targetXPos;
    },


    /**
     * Hides the feedback shown while a column is moved by the user.
     */
    hideColumnMoveFeedback : function() {
      this._hideResizeLine();
    },


    /**
     * Sets the focus to the cell that's located at the page position
     * <code>pageX</code>/<code>pageY</code>. If there is no cell at that position,
     * nothing happens.
     *
     * @param pageX {Integer} the x position in the page (in pixels).
     * @param pageY {Integer} the y position in the page (in pixels).
     * @return {void}
     */
    _focusCellAtPagePos : function(pageX, pageY)
    {
      var row = this._getRowForPagePos(pageX, pageY);

      if (row != -1 && row != null)
      {
        // The mouse is over the data -> update the focus
        var col = this._getColumnForPageX(pageX);
        this.__table.setFocusedCell(col, row);
      }
    },


    /**
     * Sets the currently focused cell.
     *
     * @param col {Integer} the model index of the focused cell's column.
     * @param row {Integer} the model index of the focused cell's row.
     * @return {void}
     */
    setFocusedCell : function(col, row)
    {
      if (!this.isEditing())
      {
        this.__tablePane.setFocusedCell(col, row, this.__updateContentPlanned);

        this.__focusedCol = col;
        this.__focusedRow = row;

        this._updateFocusIndicator();
      }
    },


    /**
     * Returns the column of currently focused cell.
     *
     * @return {Integer} the model index of the focused cell's column.
     */
    getFocusedColumn : function() {
      return this.__focusedCol;
    },


    /**
     * Returns the row of currently focused cell.
     *
     * @return {Integer} the model index of the focused cell's column.
     */
    getFocusedRow : function() {
      return this.__focusedRow;
    },


    /**
     * Scrolls a cell visible.
     *
     * @param col {Integer} the model index of the column the cell belongs to.
     * @param row {Integer} the model index of the row the cell belongs to.
     * @return {void}
     */
    scrollCellVisible : function(col, row)
    {
      var paneModel = this.getTablePaneModel();
      var xPos = paneModel.getX(col);

      if (xPos != -1)
      {
        var clipperSize = this.__paneClipper.getInnerSize();
        if (!clipperSize) {
          return;
        }

        var columnModel = this.getTable().getTableColumnModel();

        var colLeft = paneModel.getColumnLeft(col);
        var colWidth = columnModel.getColumnWidth(col);
        var rowHeight = this.getTable().getRowHeight();
        var rowTop = row * rowHeight;

        var scrollX = this.getScrollX();
        var scrollY = this.getScrollY();

        // NOTE: We don't use qx.lang.Number.limit, because min should win if max < min
        var minScrollX = Math.min(colLeft, colLeft + colWidth - clipperSize.width);
        var maxScrollX = colLeft;
        this.setScrollX(Math.max(minScrollX, Math.min(maxScrollX, scrollX)));

        var minScrollY = rowTop + rowHeight - clipperSize.height;

        if (this.getTable().getKeepFirstVisibleRowComplete()) {
          minScrollY += rowHeight;
        }

        var maxScrollY = rowTop;
        this.setScrollY(Math.max(minScrollY, Math.min(maxScrollY, scrollY)), true);
      }
    },


    /**
     * Returns whether currently a cell is editing.
     *
     * @return {var} whether currently a cell is editing.
     */
    isEditing : function() {
      return this.__cellEditor != null;
    },


    /**
     * Starts editing the currently focused cell. Does nothing if already
     * editing, if the column is not editable, or if the cell editor for the
     * column ascertains that the particular cell is not editable.
     *
     * @return {Boolean} whether editing was started
     */
    startEditing : function()
    {
      var table = this.getTable();
      var tableModel = table.getTableModel();
      var col = this.__focusedCol;

      if (
        !this.isEditing() &&
        (col != null) &&
        tableModel.isColumnEditable(col)
      ) {
        var row = this.__focusedRow;
        var xPos = this.getTablePaneModel().getX(col);
        var value = tableModel.getValue(col, row);

        // scroll cell into view
        this.scrollCellVisible(xPos, row);

        this.__cellEditorFactory = table.getTableColumnModel().getCellEditorFactory(col);

        var cellInfo =
        {
          col   : col,
          row   : row,
          xPos  : xPos,
          value : value,
          table : table
        };

        // Get a cell editor
        this.__cellEditor = this.__cellEditorFactory.createCellEditor(cellInfo);

        // We handle two types of cell editors: the traditional in-place
        // editor, where the cell editor returned by the factory must fit in
        // the space of the table cell; and a modal window in which the
        // editing takes place.  Additionally, if the cell editor determines
        // that it does not want to edit the particular cell being requested,
        // it may return null to indicate that that cell is not editable.
        if (this.__cellEditor === null)
        {
          // This cell is not editable even though its column is.
          return false;
        }
        else if (this.__cellEditor instanceof qx.ui.window.Window)
        {
          // It's a window.  Ensure that it's modal.
          this.__cellEditor.setModal(true);

          // At least for the time being, we disallow the close button.  It
          // acts differently than a cellEditor.close(), and invokes a bug
          // someplace.  Modal window cell editors should provide their own
          // buttons or means to activate a cellEditor.close() or equivalently
          // cellEditor.hide().
          this.__cellEditor.setShowClose(false);

          // Arrange to be notified when it is closed.
          this.__cellEditor.addListener(
            "close",
            this._onCellEditorModalWindowClose,
            this);

          // If there's a pre-open function defined for the table...
          var f = table.getModalCellEditorPreOpenFunction();
          if (f != null) {
            f(this.__cellEditor, cellInfo);
          }

          // Open it now.
          this.__cellEditor.open();
        }
        else
        {
          // The cell editor is a traditional in-place editor.
          var size = this.__focusIndicator.getInnerSize();
          this.__cellEditor.setUserBounds(0, 0, size.width, size.height);

          // prevent click event from bubbling up to the table
          this.__focusIndicator.addListener("mousedown", function(e)
          {
            this.__lastMouseDownCell = {
              row : this.__focusedRow,
              col : this.__focusedCol
            };
            e.stopPropagation();
          }, this);

          this.__focusIndicator.add(this.__cellEditor);
          this.__focusIndicator.addState("editing");
          this.__focusIndicator.setKeepActive(false);

          // Make the focus indicator visible during editing
          this.__focusIndicator.setDecorator("table-scroller-focus-indicator");

          this.__cellEditor.focus();
          this.__cellEditor.activate();
        }

        return true;
      }

      return false;
    },


    /**
     * Stops editing and writes the editor's value to the model.
     */
    stopEditing : function()
    {
      // If the focus indicator is not being shown normally...
      if (! this.getShowCellFocusIndicator())
      {
        // ... then hide it again
        this.__focusIndicator.setDecorator(null);
      }

      this.flushEditor();
      this.cancelEditing();
    },


    /**
     * Writes the editor's value to the model.
     */
    flushEditor : function()
    {
      if (this.isEditing())
      {
        var value = this.__cellEditorFactory.getCellEditorValue(this.__cellEditor);
        var oldValue = this.getTable().getTableModel().getValue(this.__focusedCol, this.__focusedRow);
        this.getTable().getTableModel().setValue(this.__focusedCol, this.__focusedRow, value);

        this.__table.focus();

        // Fire an event containing the value change.
        this.__table.fireDataEvent("dataEdited",
                                   {
                                     row      : this.__focusedRow,
                                     col      : this.__focusedCol,
                                     oldValue : oldValue,
                                     value    : value
                                   });
      }
    },


    /**
     * Stops editing without writing the editor's value to the model.
     */
    cancelEditing : function()
    {
      if (this.isEditing() && ! this.__cellEditor.pendingDispose)
      {
        if (this._cellEditorIsModalWindow)
        {
          this.__cellEditor.destroy();
          this.__cellEditor = null;
          this.__cellEditorFactory = null;
          this.__cellEditor.pendingDispose = true;
        }
        else
        {
          this.__focusIndicator.removeState("editing");
          this.__focusIndicator.setKeepActive(true);
          this.__cellEditor.destroy();
          this.__cellEditor = null;
          this.__cellEditorFactory = null;
        }
      }
    },


    /**
     * Event handler. Called when the modal window of the cell editor closes.
     *
     * @param e {Map} the event.
     * @return {void}
     */
    _onCellEditorModalWindowClose : function(e) {
      this.stopEditing();
    },


    /**
     * Returns the model index of the column the mouse is over or null if the mouse
     * is not over a column.
     *
     * @param pageX {Integer} the x position of the mouse in the page (in pixels).
     * @return {Integer} the model index of the column the mouse is over.
     */
    _getColumnForPageX : function(pageX)
    {
      var columnModel = this.getTable().getTableColumnModel();
      var paneModel = this.getTablePaneModel();
      var colCount = paneModel.getColumnCount();
      var currX = this.__tablePane.getContentLocation().left;

      for (var x=0; x<colCount; x++)
      {
        var col = paneModel.getColumnAtX(x);
        var colWidth = columnModel.getColumnWidth(col);
        currX += colWidth;

        if (pageX < currX) {
          return col;
        }
      }

      return null;
    },


    /**
     * Returns the model index of the column that should be resized when dragging
     * starts here. Returns -1 if the mouse is in no resize region of any column.
     *
     * @param pageX {Integer} the x position of the mouse in the page (in pixels).
     * @return {Integer} the column index.
     */
    _getResizeColumnForPageX : function(pageX)
    {
      var columnModel = this.getTable().getTableColumnModel();
      var paneModel = this.getTablePaneModel();
      var colCount = paneModel.getColumnCount();
      var currX = this.__header.getContainerLocation().left;
      var regionRadius = qx.ui.table.pane.Scroller.RESIZE_REGION_RADIUS;

      for (var x=0; x<colCount; x++)
      {
        var col = paneModel.getColumnAtX(x);
        var colWidth = columnModel.getColumnWidth(col);
        currX += colWidth;

        if (pageX >= (currX - regionRadius) && pageX <= (currX + regionRadius)) {
          return col;
        }
      }

      return -1;
    },


    /**
     * Returns the model index of the row the mouse is currently over. Returns -1 if
     * the mouse is over the header. Returns null if the mouse is not over any
     * column.
     *
     * @param pageX {Integer} the mouse x position in the page.
     * @param pageY {Integer} the mouse y position in the page.
     * @return {Integer} the model index of the row the mouse is currently over.
     */
    _getRowForPagePos : function(pageX, pageY)
    {
      var panePos = this.__tablePane.getContentLocation();

      if (pageX < panePos.left || pageX > panePos.right)
      {
        // There was no cell or header cell hit
        return null;
      }

      if (pageY >= panePos.top && pageY <= panePos.bottom)
      {
        // This event is in the pane -> Get the row
        var rowHeight = this.getTable().getRowHeight();

        var scrollY = this.__verScrollBar.getPosition();

        if (this.getTable().getKeepFirstVisibleRowComplete()) {
          scrollY = Math.floor(scrollY / rowHeight) * rowHeight;
        }

        var tableY = scrollY + pageY - panePos.top;
        var row = Math.floor(tableY / rowHeight);

        var tableModel = this.getTable().getTableModel();
        var rowCount = tableModel.getRowCount();

        return (row < rowCount) ? row : null;
      }

      var headerPos = this.__header.getContainerLocation();

      if (
        pageY >= headerPos.top &&
        pageY <= headerPos.bottom &&
        pageX <= headerPos.right)
      {
        // This event is in the pane -> Return -1 for the header
        return -1;
      }

      return null;
    },


    /**
     * Sets the widget that should be shown in the top right corner.
     *
     * The widget will not be disposed, when this table scroller is disposed. So the
     * caller has to dispose it.
     *
     * @param widget {qx.ui.core.Widget} The widget to set. May be null.
     * @return {void}
     */
    setTopRightWidget : function(widget)
    {
      var oldWidget = this.__topRightWidget;

      if (oldWidget != null) {
        this.__top.remove(oldWidget);
      }

      if (widget != null) {
        this.__top.add(widget);
      }

      this.__topRightWidget = widget;
    },


    /**
     * Get the top right widget
     *
     * @return {qx.ui.core.Widget} The top right widget.
     */
    getTopRightWidget : function() {
      return this.__topRightWidget;
    },


    /**
     * Returns the header.
     *
     * @return {qx.ui.table.pane.Header} the header.
     */
    getHeader : function() {
      return this.__header;
    },


    /**
     * Returns the table pane.
     *
     * @return {qx.ui.table.pane.Pane} the table pane.
     */
    getTablePane : function() {
      return this.__tablePane;
    },


    /**
     * Get the rendered width of the vertical scroll bar. The return value is
     * <code>0</code> if the scroll bar is invisible or not yet rendered.
     *
     * @internal
     * @return {Integer} The width of the vertical scroll bar
     */
    getVerticalScrollBarWidth : function()
    {
      var scrollBar = this.__verScrollBar;
      return scrollBar.isVisible() ? (scrollBar.getSizeHint().width || 0) : 0;
    },


    /**
     * Returns which scrollbars are needed.
     *
     * @param forceHorizontal {Boolean ? false} Whether to show the horizontal
     *      scrollbar always.
     * @param preventVertical {Boolean ? false} Whether to show the vertical scrollbar
     *      never.
     * @return {Integer} which scrollbars are needed. This may be any combination of
     *      {@link #HORIZONTAL_SCROLLBAR} or {@link #VERTICAL_SCROLLBAR}
     *      (combined by OR).
     */
    getNeededScrollBars : function(forceHorizontal, preventVertical)
    {
      var verScrollBar = this.__verScrollBar;
      var verBarWidth = verScrollBar.getSizeHint().width
        + verScrollBar.getMarginLeft() + verScrollBar.getMarginRight();
      var horScrollBar = this.__horScrollBar;
      var horBarHeight = horScrollBar.getSizeHint().height
        + horScrollBar.getMarginTop() + horScrollBar.getMarginBottom();

      // Get the width and height of the view (without scroll bars)
      var clipperSize = this.__paneClipper.getInnerSize();
      var viewWidth = clipperSize ? clipperSize.width : 0;

      if (this.getVerticalScrollBarVisible()) {
        viewWidth += verBarWidth;
      }

      var viewHeight = clipperSize ? clipperSize.height : 0;

      if (this.getHorizontalScrollBarVisible()) {
        viewHeight += horBarHeight;
      }

      var tableModel = this.getTable().getTableModel();
      var rowCount = tableModel.getRowCount();

      // Get the (virtual) width and height of the pane
      var paneWidth = this.getTablePaneModel().getTotalWidth();
      var paneHeight = this.getTable().getRowHeight() * rowCount;

      // Check which scrollbars are needed
      var horNeeded = false;
      var verNeeded = false;

      if (paneWidth > viewWidth)
      {
        horNeeded = true;

        if (paneHeight > viewHeight - horBarHeight) {
          verNeeded = true;
        }
      }
      else if (paneHeight > viewHeight)
      {
        verNeeded = true;

        if (!preventVertical && (paneWidth > viewWidth - verBarWidth)) {
          horNeeded = true;
        }
      }

      // Create the mask
      var horBar = qx.ui.table.pane.Scroller.HORIZONTAL_SCROLLBAR;
      var verBar = qx.ui.table.pane.Scroller.VERTICAL_SCROLLBAR;
      return ((forceHorizontal || horNeeded) ? horBar : 0) | ((preventVertical || !verNeeded) ? 0 : verBar);
    },


    /**
     * Return the pane clipper. It is sometimes required for special activities
     * such as tracking events for drag&drop.
     *
     * @return {qx.ui.table.pane.Clipper}
     *   The pane clipper for this scroller.
     */
    getPaneClipper : function()
    {
      return this.__paneClipper;
    },

    // property apply method
    _applyScrollTimeout : function(value, old) {
      this._startInterval(value);
    },


    /**
     * Starts the current running interval
     *
     * @param timeout {Integer} The timeout between two table updates
     */
    _startInterval : function (timeout)
    {
      this.__timer.setInterval(timeout);
      this.__timer.start();
    },


    /**
     * stops the current running interval
     */
    _stopInterval : function ()
    {
      this.__timer.stop();
    },


    /**
     * Does a postponed update of the content.
     *
     * @see #_updateContent
     */
    _postponedUpdateContent : function()
    {
      //this.__updateContentPlanned = true;
      this._updateContent();
    },


    /**
     * Timer event handler. Periodically checks whether a table update is
     * required. The update interval is controlled by the {@link #scrollTimeout}
     * property.
     *
     * @signature function()
     */
    _oninterval : qx.event.GlobalError.observeMethod(function()
    {
      if (this.__updateContentPlanned && !this.__tablePane._layoutPending)
      {
        this.__updateContentPlanned = false;
        this._updateContent();
      }
    }),


    /**
     * Updates the content. Sets the right section the table pane should show and
     * does the scrolling.
     */
    _updateContent : function()
    {
      var paneSize = this.__paneClipper.getInnerSize();
      if (!paneSize) {
        return;
      }
      var paneHeight = paneSize.height;

      var scrollX = this.__horScrollBar.getPosition();
      var scrollY = this.__verScrollBar.getPosition();
      var rowHeight = this.getTable().getRowHeight();

      var firstRow = Math.floor(scrollY / rowHeight);
      var oldFirstRow = this.__tablePane.getFirstVisibleRow();
      this.__tablePane.setFirstVisibleRow(firstRow);

      var visibleRowCount = Math.ceil(paneHeight / rowHeight);
      var paneOffset = 0;
      var firstVisibleRowComplete = this.getTable().getKeepFirstVisibleRowComplete();

      if (!firstVisibleRowComplete)
      {

        // NOTE: We don't consider paneOffset, because this may cause alternating
        //       adding and deleting of one row when scrolling. Instead we add one row
        //       in every case.
        visibleRowCount++;

        paneOffset = scrollY % rowHeight;
      }

      this.__tablePane.setVisibleRowCount(visibleRowCount);

      if (firstRow != oldFirstRow) {
        this._updateFocusIndicator();
      }

      this.__paneClipper.scrollToX(scrollX);

      // Avoid expensive calls to setScrollTop if
      // scrolling is not needed
      if (! firstVisibleRowComplete ) {
        this.__paneClipper.scrollToY(paneOffset);
      }
    },

    /**
     * Updates the location and the visibility of the focus indicator.
     *
     * @return {void}
     */
    _updateFocusIndicator : function()
    {
      var table = this.getTable();

      if (!table.getEnabled()) {
        return;
      }

      this.__focusIndicator.moveToCell(this.__focusedCol, this.__focusedRow);
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function()
  {
    this._stopInterval();

    // this object was created by the table on init so we have to clean it up.
    var tablePaneModel = this.getTablePaneModel();
    if (tablePaneModel)
    {
      tablePaneModel.dispose();
    }

    this.__lastMouseDownCell = this.__topRightWidget = this.__table = null;
    this._disposeObjects("__horScrollBar", "__verScrollBar",
                         "__headerClipper", "__paneClipper", "__focusIndicator",
                         "__header", "__tablePane", "__top", "__timer");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * Clipping area for the table header and table pane.
 */
qx.Class.define("qx.ui.table.pane.Clipper",
{
  extend : qx.ui.container.Composite,

  construct : function()
  {
    this.base(arguments, new qx.ui.layout.Grow());
    this.setMinWidth(0);
  },

  members :
  {
    /**
     * Scrolls the element's content to the given left coordinate
     *
     * @param value {Integer} The vertical position to scroll to.
     * @return {void}
     */
    scrollToX : function(value) {
      this.getContentElement().scrollToX(value, false);
    },


    /**
     * Scrolls the element's content to the given top coordinate
     *
     * @param value {Integer} The horizontal position to scroll to.
     * @return {void}
     */
    scrollToY : function(value) {
      this.getContentElement().scrollToY(value, true);
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Fabian Jakobs (fjakobs)

************************************************************************ */

/**
 * The focus indicator widget
 */
qx.Class.define("qx.ui.table.pane.FocusIndicator",
{
  extend : qx.ui.container.Composite,

  /**
   * @param scroller {Scroller} The scroller, which contains this focus indicator
   */
  construct : function(scroller)
  {
    this.base(arguments);
    this.__scroller = scroller;

    this.setKeepActive(true);
    this.addListener("keypress", this._onKeyPress, this);
  },

  properties :
  {
    // overridden
    visibility :
    {
      refine : true,
      init : "excluded"
    },

    /** Table row, where the indicator is placed. */
    row : {
      check : "Integer",
      nullable : true
    },

    /** Table column, where the indicator is placed. */
    column : {
      check : "Integer",
      nullable : true
    }
  },

  members :
  {
    __scroller : null,


    /**
     * Keypress handler. Suppress all key events but "Enter" and "Escape"
     *
     * @param e {qx.event.type.KeySequence} key event
     */
    _onKeyPress : function(e)
    {
      var iden = e.getKeyIdentifier();
      if (iden !== "Escape" && iden !== "Enter") {
        e.stopPropagation();
      }
    },


    /**
     * Move the focus indicator to the given table cell.
     *
     * @param col {Integer?null} The table column
     * @param row {Integer?null} The table row
     */
    moveToCell : function(col, row)
    {
      // check if the focus indicator is shown and if the new column is
      // editable. if not, just exclude the incdicator because the mouse events
      // should go to the cell itself linke with HTML links [BUG #4250]
      if (
        !this.__scroller.getShowCellFocusIndicator() &&
        !this.__scroller.getTable().getTableModel().isColumnEditable(col)
      ) {
        this.exclude();
        return;
      } else {
        this.show();
      }

      if (col == null)
      {
        this.hide();
        this.setRow(null);
        this.setColumn(null);
      }
      else
      {
        var xPos = this.__scroller.getTablePaneModel().getX(col);

        if (xPos == -1)
        {
          this.hide();
          this.setRow(null);
          this.setColumn(null);
        }
        else
        {
          var table = this.__scroller.getTable();
          var columnModel = table.getTableColumnModel();
          var paneModel = this.__scroller.getTablePaneModel();

          var firstRow = this.__scroller.getTablePane().getFirstVisibleRow();
          var rowHeight = table.getRowHeight();

          this.setUserBounds(
              paneModel.getColumnLeft(col) - 2,
              (row - firstRow) * rowHeight - 2,
              columnModel.getColumnWidth(col) + 3,
              rowHeight + 3
          );
          this.show();

          this.setRow(row);
          this.setColumn(col);
        }
      }
    }
  },

  destruct : function () {
     this.__scroller = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * David Perez Carmona (david-perez)

************************************************************************ */

/**
 * A cell event instance contains all data for mouse events related to cells in
 * a table.
 **/
qx.Class.define("qx.ui.table.pane.CellEvent",
{
  extend : qx.event.type.Mouse,


  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** The table row of the event target */
    row :
    {
      check : "Integer",
      nullable: true
    },

    /** The table column of the event target */
    column :
    {
      check : "Integer",
      nullable: true
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /*
     *****************************************************************************
        CONSTRUCTOR
     *****************************************************************************
     */

     /**
      * Initialize the event
      *
      * @param scroller {qx.ui.table.pane.Scroller} The tables pane scroller
      * @param me {qx.event.type.Mouse} The original mouse event
      * @param row {Integer?null} The cell's row index
      * @param column {Integer?null} The cell's column index
      */
    init : function(scroller, me, row, column)
    {
      me.clone(this);
      this.setBubbles(false);

      if (row != null) {
        this.setRow(row);
      } else {
        this.setRow(scroller._getRowForPagePos(this.getDocumentLeft(), this.getDocumentTop()));
      }

      if (column != null) {
        this.setColumn(column);
      } else {
        this.setColumn(scroller._getColumnForPageX(this.getDocumentLeft()));
      }
    },


    // overridden
    clone : function(embryo)
    {
      var clone = this.base(arguments, embryo);

      clone.set({
        row: this.getRow(),
        column: this.getColumn()
      });

      return clone;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Sebastian Werner (wpbasti)
     * Andreas Ecker (ecker)

************************************************************************ */

/**
 * Helper functions for numbers.
 *
 * The native JavaScript Number is not modified by this class.
 *
 */
qx.Class.define("qx.lang.Number",
{
  statics :
  {
    /**
     * Check whether the number is in a given range
     *
     * @param nr {Number} the number to check
     * @param vmin {Integer} lower bound of the range
     * @param vmax {Integer} upper bound of the range
     * @return {Boolean} whether the number is >= vmin and <= vmax
     */
    isInRange : function(nr, vmin, vmax) {
      return nr >= vmin && nr <= vmax;
    },


    /**
     * Check whether the number is between a given range
     *
     * @param nr {Number} the number to check
     * @param vmin {Integer} lower bound of the range
     * @param vmax {Integer} upper bound of the range
     * @return {Boolean} whether the number is > vmin and < vmax
     */
    isBetweenRange : function(nr, vmin, vmax) {
      return nr > vmin && nr < vmax;
    },


    /**
     * Limit the number to a given range
     *
     * * If the number is greater than the upper bound, the upper bound is returned
     * * If the number is smaller than the lower bound, the lower bound is returned
     * * If the number is in the range, the number is returned
     *
     * @param nr {Number} the number to limit
     * @param vmin {Integer} lower bound of the range
     * @param vmax {Integer} upper bound of the range
     * @return {Integer} the limited number
     */
    limit : function(nr, vmin, vmax)
    {
      if (vmax != null && nr > vmax) {
        return vmax;
      } else if (vmin != null && nr < vmin) {
        return vmin;
      } else {
        return nr;
      }
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * The model of a table pane. This model works as proxy to a
 * {@link qx.ui.table.columnmodel.Basic} and manages the visual order of the columns shown in
 * a {@link Pane}.
 */
qx.Class.define("qx.ui.table.pane.Model",
{
  extend : qx.core.Object,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   *
   * @param tableColumnModel {qx.ui.table.columnmodel.Basic} The TableColumnModel of which this
   *    model is the proxy.
   */
  construct : function(tableColumnModel)
  {
    this.base(arguments);

    this.setTableColumnModel(tableColumnModel);
  },




  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events :
  {
    /** Fired when the model changed. */
    "modelChanged" : "qx.event.type.Event"
  },



  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {

    /** {string} The type of the event fired when the model changed. */
    EVENT_TYPE_MODEL_CHANGED : "modelChanged"
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {

    /** The visible x position of the first column this model should contain. */
    firstColumnX :
    {
      check : "Integer",
      init : 0,
      apply : "_applyFirstColumnX"
    },


    /**
     * The maximum number of columns this model should contain. If -1 this model will
     * contain all remaining columns.
     */
    maxColumnCount :
    {
      check : "Number",
      init : -1,
      apply : "_applyMaxColumnCount"
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __columnCount : null,
    __tableColumnModel : null,


    // property modifier
    _applyFirstColumnX : function(value, old)
    {
      this.__columnCount = null;
      this.fireEvent(qx.ui.table.pane.Model.EVENT_TYPE_MODEL_CHANGED);
    },

    // property modifier
    _applyMaxColumnCount : function(value, old)
    {
      this.__columnCount = null;
      this.fireEvent(qx.ui.table.pane.Model.EVENT_TYPE_MODEL_CHANGED);
    },


    /**
     * Connects the table model to the column model
     *
     * @param tableColumnModel {qx.ui.table.columnmodel.Basic} the column model
     */
    setTableColumnModel : function(tableColumnModel)
    {
      if (this.__tableColumnModel) {
        this.__tableColumnModel.removeListener("visibilityChangedPre", this._onColVisibilityChanged, this);
        this.__tableColumnModel.removeListener("headerCellRendererChanged", this._onColVisibilityChanged, this);
      }
      this.__tableColumnModel = tableColumnModel;
      this.__tableColumnModel.addListener("visibilityChangedPre", this._onColVisibilityChanged, this);
      this.__tableColumnModel.addListener("headerCellRendererChanged", this._onHeaderCellRendererChanged, this);
      this.__columnCount = null;
    },


    /**
     * Event handler. Called when the visibility of a column has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onColVisibilityChanged : function(evt)
    {
      this.__columnCount = null;

      // TODO: Check whether the column is in this model (This is a little bit
      //     tricky, because the column could _have been_ in this model, but is
      //     not in it after the change)
      this.fireEvent(qx.ui.table.pane.Model.EVENT_TYPE_MODEL_CHANGED);
    },


    /**
     * Event handler. Called when the cell renderer of a column has changed.
     *
     * @param evt {Map} the event.
     * @return {void}
     */
    _onHeaderCellRendererChanged : function(evt)
    {
      this.fireEvent(qx.ui.table.pane.Model.EVENT_TYPE_MODEL_CHANGED);
    },


    /**
     * Returns the number of columns in this model.
     *
     * @return {Integer} the number of columns in this model.
     */
    getColumnCount : function()
    {
      if (this.__columnCount == null)
      {
        var firstX = this.getFirstColumnX();
        var maxColCount = this.getMaxColumnCount();
        var totalColCount = this.__tableColumnModel.getVisibleColumnCount();

        if (maxColCount == -1 || (firstX + maxColCount) > totalColCount) {
          this.__columnCount = totalColCount - firstX;
        } else {
          this.__columnCount = maxColCount;
        }
      }

      return this.__columnCount;
    },


    /**
     * Returns the model index of the column at the position <code>xPos</code>.
     *
     * @param xPos {Integer} the x position in the table pane of the column.
     * @return {Integer} the model index of the column.
     */
    getColumnAtX : function(xPos)
    {
      var firstX = this.getFirstColumnX();
      return this.__tableColumnModel.getVisibleColumnAtX(firstX + xPos);
    },


    /**
     * Returns the x position of the column <code>col</code>.
     *
     * @param col {Integer} the model index of the column.
     * @return {Integer} the x position in the table pane of the column.
     */
    getX : function(col)
    {
      var firstX = this.getFirstColumnX();
      var maxColCount = this.getMaxColumnCount();

      var x = this.__tableColumnModel.getVisibleX(col) - firstX;

      if (x >= 0 && (maxColCount == -1 || x < maxColCount)) {
        return x;
      } else {
        return -1;
      }
    },


    /**
     * Gets the position of the left side of a column (in pixels, relative to the
     * left side of the table pane).
     *
     * This value corresponds to the sum of the widths of all columns left of the
     * column.
     *
     * @param col {Integer} the model index of the column.
     * @return {var} the position of the left side of the column.
     */
    getColumnLeft : function(col)
    {
      var left = 0;
      var colCount = this.getColumnCount();

      for (var x=0; x<colCount; x++)
      {
        var currCol = this.getColumnAtX(x);

        if (currCol == col) {
          return left;
        }

        left += this.__tableColumnModel.getColumnWidth(currCol);
      }

      return -1;
    },


    /**
     * Returns the total width of all columns in the model.
     *
     * @return {Integer} the total width of all columns in the model.
     */
    getTotalWidth : function()
    {
      var totalWidth = 0;
      var colCount = this.getColumnCount();

      for (var x=0; x<colCount; x++)
      {
        var col = this.getColumnAtX(x);
        totalWidth += this.__tableColumnModel.getColumnWidth(col);
      }

      return totalWidth;
    }
  },




  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    if (this.__tableColumnModel)
    {
      this.__tableColumnModel.removeListener("visibilityChangedPre", this._onColVisibilityChanged, this);
      this.__tableColumnModel.removeListener("headerCellRendererChanged", this._onColVisibilityChanged, this);
    }
    this.__tableColumnModel = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2006 STZ-IDA, Germany, http://www.stz-ida.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Til Schneider (til132)

************************************************************************ */

/**
 * A simple table model that provides an API for changing the model data.
 */
qx.Class.define("qx.ui.table.model.Simple",
{
  extend : qx.ui.table.model.Abstract,


  construct : function()
  {
    this.base(arguments);

    this.__rowArr = [];
    this.__sortColumnIndex = -1;

    // Array of objects, each with property "ascending" and "descending"
    this.__sortMethods = [];

    this.__editableColArr = null;
  },

  properties :
  {
    /**
     * Whether sorting should be case sensitive
     */
    caseSensitiveSorting :
    {
      check : "Boolean",
      init : true
    }
  },


  statics :
  {
    /**
     * Default ascendeing sort method to use if no custom method has been
     * provided.
     *
     * @param row1 {var} first row
     * @param row2 {var} second row
     * @return {Integer} 1 of row1 is > row2, -1 if row1 is < row2, 0 if row1 == row2
     */
    _defaultSortComparatorAscending : function(row1, row2)
    {
      var obj1 = row1[arguments.callee.columnIndex];
      var obj2 = row2[arguments.callee.columnIndex];
      if (qx.lang.Type.isNumber(obj1) && qx.lang.Type.isNumber(obj2)) {
        var result = isNaN(obj1) ? isNaN(obj2) ?  0 : 1 : isNaN(obj2) ? -1 : null;
        if (result != null) {
          return result;
        }
      }
      return (obj1 > obj2) ? 1 : ((obj1 == obj2) ? 0 : -1);
    },


    /**
     * Same as the Default ascending sort method but using case insensitivity
     *
     * @param row1 {var} first row
     * @param row2 {var} second row
     * @return {Integer} 1 of row1 is > row2, -1 if row1 is < row2, 0 if row1 == row2
     */
    _defaultSortComparatorInsensitiveAscending : function(row1, row2)
    {
      var obj1 = (row1[arguments.callee.columnIndex].toLowerCase ?
            row1[arguments.callee.columnIndex].toLowerCase() : row1[arguments.callee.columnIndex]);
      var obj2 = (row2[arguments.callee.columnIndex].toLowerCase ?
            row2[arguments.callee.columnIndex].toLowerCase() : row2[arguments.callee.columnIndex]);

      if (qx.lang.Type.isNumber(obj1) && qx.lang.Type.isNumber(obj2)) {
        var result = isNaN(obj1) ? isNaN(obj2) ?  0 : 1 : isNaN(obj2) ? -1 : null;
        if (result != null) {
          return result;
        }
      }
      return (obj1 > obj2) ? 1 : ((obj1 == obj2) ? 0 : -1);
    },


    /**
     * Default descending sort method to use if no custom method has been
     * provided.
     *
     * @param row1 {var} first row
     * @param row2 {var} second row
     * @return {Integer} 1 of row1 is > row2, -1 if row1 is < row2, 0 if row1 == row2
     */
    _defaultSortComparatorDescending : function(row1, row2)
    {
      var obj1 = row1[arguments.callee.columnIndex];
      var obj2 = row2[arguments.callee.columnIndex];
      if (qx.lang.Type.isNumber(obj1) && qx.lang.Type.isNumber(obj2)) {
        var result = isNaN(obj1) ? isNaN(obj2) ?  0 : 1 : isNaN(obj2) ? -1 : null;
        if (result != null) {
          return result;
        }
      }
      return (obj1 < obj2) ? 1 : ((obj1 == obj2) ? 0 : -1);
    },


    /**
     * Same as the Default descending sort method but using case insensitivity
     *
     * @param row1 {var} first row
     * @param row2 {var} second row
     * @return {Integer} 1 of row1 is > row2, -1 if row1 is < row2, 0 if row1 == row2
     */
    _defaultSortComparatorInsensitiveDescending : function(row1, row2)
    {
      var obj1 = (row1[arguments.callee.columnIndex].toLowerCase ?
          row1[arguments.callee.columnIndex].toLowerCase() : row1[arguments.callee.columnIndex]);
      var obj2 = (row2[arguments.callee.columnIndex].toLowerCase ?
          row2[arguments.callee.columnIndex].toLowerCase() : row2[arguments.callee.columnIndex]);
      if (qx.lang.Type.isNumber(obj1) && qx.lang.Type.isNumber(obj2)) {
        var result = isNaN(obj1) ? isNaN(obj2) ?  0 : 1 : isNaN(obj2) ? -1 : null;
        if (result != null) {
          return result;
        }
      }
      return (obj1 < obj2) ? 1 : ((obj1 == obj2) ? 0 : -1);
    }

  },


  members :
  {
    __rowArr : null,
    __editableColArr : null,
    __sortableColArr : null,
    __sortMethods : null,
    __sortColumnIndex : null,
    __sortAscending : null,


    // overridden
    getRowData : function(rowIndex)
    {
      var rowData = this.__rowArr[rowIndex];
      if (rowData == null || rowData.originalData == null) {
        return rowData;
      } else {
        return rowData.originalData;
      }
    },


    /**
     * Returns the data of one row as map containing the column IDs as key and
     * the cell values as value. Also the meta data is included.
     *
     * @param rowIndex {Integer} the model index of the row.
     * @return {Map} a Map containing the column values.
     */
    getRowDataAsMap : function(rowIndex)
    {
      var rowData = this.__rowArr[rowIndex];

      if (rowData != null) {
        var map = {};
        // get the current set data
        for (var col = 0; col < this.getColumnCount(); col++) {
          map[this.getColumnId(col)] = rowData[col];
        }

        if (rowData.originalData != null) {
          // merge in the meta data
          for (var key in rowData.originalData) {
            if (map[key] == undefined) {
              map[key] = rowData.originalData[key];
            }
          }
        }

        return map;
      }
      // may be null, which is ok
      return (rowData && rowData.originalData) ? rowData.originalData : null;
    },


    /**
     * Gets the whole data as an array of maps.
     *
     * Note: Individual items are retrieved by {@link #getRowDataAsMap}.
     */
    getDataAsMapArray: function() {
      var len = this.getRowCount();
      var data = [];

      for (var i = 0; i < len; i++)
      {
        data.push(this.getRowDataAsMap(i));
      }

      return data;
    },


    /**
     * Sets all columns editable or not editable.
     *
     * @param editable {Boolean} whether all columns are editable.
     * @return {void}
     */
    setEditable : function(editable)
    {
      this.__editableColArr = [];

      for (var col=0; col<this.getColumnCount(); col++) {
        this.__editableColArr[col] = editable;
      }

      this.fireEvent("metaDataChanged");
    },


    /**
     * Sets whether a column is editable.
     *
     * @param columnIndex {Integer} the column of which to set the editable state.
     * @param editable {Boolean} whether the column should be editable.
     * @return {void}
     */
    setColumnEditable : function(columnIndex, editable)
    {
      if (editable != this.isColumnEditable(columnIndex))
      {
        if (this.__editableColArr == null) {
          this.__editableColArr = [];
        }

        this.__editableColArr[columnIndex] = editable;

        this.fireEvent("metaDataChanged");
      }
    },

    // overridden
    isColumnEditable : function(columnIndex) {
      return this.__editableColArr ? (this.__editableColArr[columnIndex] == true) : false;
    },


    /**
     * Sets whether a column is sortable.
     *
     * @param columnIndex {Integer} the column of which to set the sortable state.
     * @param sortable {Boolean} whether the column should be sortable.
     */
    setColumnSortable : function(columnIndex, sortable)
    {
      if (sortable != this.isColumnSortable(columnIndex))
      {
        if (this.__sortableColArr == null) {
          this.__sortableColArr = [];
        }

        this.__sortableColArr[columnIndex] = sortable;
        this.fireEvent("metaDataChanged");
      }
    },


    // overridden
    isColumnSortable : function(columnIndex) {
      return (
        this.__sortableColArr
        ? (this.__sortableColArr[columnIndex] !== false)
        : true
      );
    },

    // overridden
    sortByColumn : function(columnIndex, ascending)
    {
      // NOTE: We use different comparators for ascending and descending,
      //     because comparators should be really fast.
      var comparator;

      var sortMethods = this.__sortMethods[columnIndex];
      if (sortMethods)
      {
        comparator =
          (ascending
           ? sortMethods.ascending
           : sortMethods.descending);
      }
      else
      {
        if (this.getCaseSensitiveSorting())
        {
          comparator =
            (ascending
             ? qx.ui.table.model.Simple._defaultSortComparatorAscending
             : qx.ui.table.model.Simple._defaultSortComparatorDescending);
        }
        else
        {
          comparator =
            (ascending
             ? qx.ui.table.model.Simple._defaultSortComparatorInsensitiveAscending
             : qx.ui.table.model.Simple._defaultSortComparatorInsensitiveDescending);
        }
      }

      comparator.columnIndex = columnIndex;
      this.__rowArr.sort(comparator);

      this.__sortColumnIndex = columnIndex;
      this.__sortAscending = ascending;

      var data =
        {
          columnIndex : columnIndex,
          ascending   : ascending
        };
      this.fireDataEvent("sorted", data);

      this.fireEvent("metaDataChanged");
    },


    /**
     * Specify the methods to use for ascending and descending sorts of a
     * particular column.
     *
     * @param columnIndex {Integer}
     *   The index of the column for which the sort methods are being
     *   provided.
     *
     * @param compare {Function|Map}
     *   If provided as a Function, this is the comparator function to sort in
     *   ascending order. It takes two parameters: the two arrays of row data,
     *   row1 and row2, being compared. It may determine which column of the
     *   row data to sort on by accessing arguments.callee.columnIndex.  The
     *   comparator function must return 1, 0 or -1, when the column in row1
     *   is greater than, equal to, or less than, respectively, the column in
     *   row2.
     *
     *   If this parameter is a Map, it shall have two properties: "ascending"
     *   and "descending". The property value of each is a comparator
     *   function, as described above.
     *
     *   If only the "ascending" function is provided (i.e. this parameter is
     *   a Function, not a Map), then the "descending" function is built
     *   dynamically by passing the two parameters to the "ascending" function
     *   in reversed order. <i>Use of a dynamically-built "descending" function
     *   generates at least one extra function call for each row in the table,
     *   and possibly many more. If the table is expected to have more than
     *   about 1000 rows, you will likely want to provide a map with a custom
     *   "descending" sort function as well as the "ascending" one.</i>
     *
     * @return {void}
     */
    setSortMethods : function(columnIndex, compare)
    {
      var methods;
      if (qx.lang.Type.isFunction(compare))
      {
        methods =
          {
            ascending  : compare,
            descending : function(row1, row2)
            {
              return compare(row2, row1);
            }
          };
      }
      else
      {
        methods = compare;
      }
      this.__sortMethods[columnIndex] = methods;
    },


    /**
     * Returns the sortMethod(s) for a table column.
     *
     * @param columnIndex {Integer} The index of the column for which the sort
     *   methods are being  provided.
     *
     * @return {Map} a map with the two properties "ascending"
     *   and "descending" for the specified column.
     *   The property value of each is a comparator function, as described
     *   in {@link #setSortMethods}.
     */
    getSortMethods : function(columnIndex) {
      return this.__sortMethods[columnIndex];
    },


    /**
     * Clears the sorting.
     */
    clearSorting : function()
    {
      if (this.__sortColumnIndex != -1)
      {
        this.__sortColumnIndex = -1;
        this.__sortAscending = true;

        this.fireEvent("metaDataChanged");
      }
    },

    // overridden
    getSortColumnIndex : function() {
      return this.__sortColumnIndex;
    },

    /**
     * Set the sort column index
     *
     * WARNING: This should be called only by subclasses with intimate
     *          knowledge of what they are doing!
     *
     * @param columnIndex {Integer} index of the column
     */
    _setSortColumnIndex : function(columnIndex)
    {
      this.__sortColumnIndex = columnIndex;
    },

    // overridden
    isSortAscending : function() {
      return this.__sortAscending;
    },

    /**
     * Set whether to sort in ascending order or not.
     *
     * WARNING: This should be called only by subclasses with intimate
     *          knowledge of what they are doing!
     *
     * @param ascending {Boolean}
     *   <i>true</i> for an ascending sort;
     *   <i> false</i> for a descending sort.
     */
    _setSortAscending : function(ascending)
    {
      this.__sortAscending = ascending;
    },

    // overridden
    getRowCount : function() {
      return this.__rowArr.length;
    },

    // overridden
    getValue : function(columnIndex, rowIndex)
    {
      if (rowIndex < 0 || rowIndex >= this.__rowArr.length) {
        throw new Error("this.__rowArr out of bounds: " + rowIndex + " (0.." + this.__rowArr.length + ")");
      }

      return this.__rowArr[rowIndex][columnIndex];
    },

    // overridden
    setValue : function(columnIndex, rowIndex, value)
    {
      if (this.__rowArr[rowIndex][columnIndex] != value)
      {
        this.__rowArr[rowIndex][columnIndex] = value;

        // Inform the listeners
        if (this.hasListener("dataChanged"))
        {
          var data =
          {
            firstRow    : rowIndex,
            lastRow     : rowIndex,
            firstColumn : columnIndex,
            lastColumn  : columnIndex
          };

          this.fireDataEvent("dataChanged", data);
        }

        if (columnIndex == this.__sortColumnIndex) {
          this.clearSorting();
        }
      }
    },


    /**
     * Sets the whole data in a bulk.
     *
     * @param rowArr {var[][]} An array containing an array for each row. Each
     *          row-array contains the values in that row in the order of the columns
     *          in this model.
     * @param clearSorting {Boolean ? true} Whether to clear the sort state.
     * @return {void}
     */
    setData : function(rowArr, clearSorting)
    {
      this.__rowArr = rowArr;

      // Inform the listeners
      if (this.hasListener("dataChanged"))
      {
        var data =
        {
          firstRow    : 0,
          lastRow     : rowArr.length - 1,
          firstColumn : 0,
          lastColumn  : this.getColumnCount() - 1
        };

        this.fireDataEvent("dataChanged", data);
      }

      if (clearSorting !== false) {
        this.clearSorting();
      }
    },


    /**
     * Returns the data of this model.
     *
     * Warning: Do not alter this array! If you want to change the data use
     * {@link #setData}, {@link #setDataAsMapArray} or {@link #setValue} instead.
     *
     * @return {var[][]} An array containing an array for each row. Each
     *           row-array contains the values in that row in the order of the columns
     *           in this model.
     */
    getData : function() {
      return this.__rowArr;
    },


    /**
     * Sets the whole data in a bulk.
     *
     * @param mapArr {Map[]} An array containing a map for each row. Each
     *        row-map contains the column IDs as key and the cell values as value.
     * @param rememberMaps {Boolean ? false} Whether to remember the original maps.
     *        If true {@link #getRowData} will return the original map.
     * @param clearSorting {Boolean ? true} Whether to clear the sort state.
     */
    setDataAsMapArray : function(mapArr, rememberMaps, clearSorting) {
      this.setData(this._mapArray2RowArr(mapArr, rememberMaps), clearSorting);
    },


    /**
     * Adds some rows to the model.
     *
     * Warning: The given array will be altered!
     *
     * @param rowArr {var[][]} An array containing an array for each row. Each
     *          row-array contains the values in that row in the order of the columns
     *          in this model.
     * @param startIndex {Integer ? null} The index where to insert the new rows. If null,
     *          the rows are appended to the end.
     * @param clearSorting {Boolean ? true} Whether to clear the sort state.
     * @return {void}
     */
    addRows : function(rowArr, startIndex, clearSorting)
    {
      if (startIndex == null) {
        startIndex = this.__rowArr.length;
      }

      // Prepare the rowArr so it can be used for apply
      rowArr.splice(0, 0, startIndex, 0);

      // Insert the new rows
      Array.prototype.splice.apply(this.__rowArr, rowArr);

      // Inform the listeners
      var data =
      {
        firstRow    : startIndex,
        lastRow     : this.__rowArr.length - 1,
        firstColumn : 0,
        lastColumn  : this.getColumnCount() - 1
      };
      this.fireDataEvent("dataChanged", data);

      if (clearSorting !== false) {
        this.clearSorting();
      }
    },


    /**
     * Adds some rows to the model.
     *
     * Warning: The given array (mapArr) will be altered!
     *
     * @param mapArr {Map[]} An array containing a map for each row. Each
     *        row-map contains the column IDs as key and the cell values as value.
     * @param startIndex {Integer ? null} The index where to insert the new rows. If null,
     *        the rows are appended to the end.
     * @param rememberMaps {Boolean ? false} Whether to remember the original maps.
     *        If true {@link #getRowData} will return the original map.
     * @param clearSorting {Boolean ? true} Whether to clear the sort state.
     */
    addRowsAsMapArray : function(mapArr, startIndex, rememberMaps, clearSorting) {
      this.addRows(this._mapArray2RowArr(mapArr, rememberMaps), startIndex, clearSorting);
    },


    /**
     * Sets rows in the model. The rows overwrite the old rows starting at
     * <code>startIndex</code> to <code>startIndex+rowArr.length</code>.
     *
     * Warning: The given array will be altered!
     *
     * @param rowArr {var[][]} An array containing an array for each row. Each
     *          row-array contains the values in that row in the order of the columns
     *          in this model.
     * @param startIndex {Integer ? null} The index where to insert the new rows. If null,
     *          the rows are set from the beginning (0).
     * @param clearSorting {Boolean ? true} Whether to clear the sort state.
     * @return {void}
     */
    setRows : function(rowArr, startIndex, clearSorting)
    {
      if (startIndex == null) {
        startIndex = 0;
      }

      // Prepare the rowArr so it can be used for apply
      rowArr.splice(0, 0, startIndex, rowArr.length);

      // Replace rows
      Array.prototype.splice.apply(this.__rowArr, rowArr);

      // Inform the listeners
      var data =
      {
        firstRow    : startIndex,
        lastRow     : this.__rowArr.length - 1,
        firstColumn : 0,
        lastColumn  : this.getColumnCount() - 1
      };
      this.fireDataEvent("dataChanged", data);

      if (clearSorting !== false) {
        this.clearSorting();
      }
    },


    /**
     * Set rows in the model. The rows overwrite the old rows starting at
     * <code>startIndex</code> to <code>startIndex+rowArr.length</code>.
     *
     * Warning: The given array (mapArr) will be altered!
     *
     * @param mapArr {Map[]} An array containing a map for each row. Each
     *        row-map contains the column IDs as key and the cell values as value.
     * @param startIndex {Integer ? null} The index where to insert the new rows. If null,
     *        the rows are appended to the end.
     * @param rememberMaps {Boolean ? false} Whether to remember the original maps.
     *        If true {@link #getRowData} will return the original map.
     * @param clearSorting {Boolean ? true} Whether to clear the sort state.
     */
    setRowsAsMapArray : function(mapArr, startIndex, rememberMaps, clearSorting) {
      this.setRows(this._mapArray2RowArr(mapArr, rememberMaps), startIndex, clearSorting);
    },


    /**
     * Removes some rows from the model.
     *
     * @param startIndex {Integer} the index of the first row to remove.
     * @param howMany {Integer} the number of rows to remove.
     * @param clearSorting {Boolean ? true} Whether to clear the sort state.
     * @return {void}
     */
    removeRows : function(startIndex, howMany, clearSorting)
    {
      this.__rowArr.splice(startIndex, howMany);

      // Inform the listeners
      var data =
      {
        firstRow    : startIndex,
        lastRow     : this.__rowArr.length - 1,
        firstColumn : 0,
        lastColumn  : this.getColumnCount() - 1,
        removeStart : startIndex,
        removeCount : howMany
      };

      this.fireDataEvent("dataChanged", data);
      if (clearSorting !== false) {
        this.clearSorting();
      }
    },


    /**
     * Creates an array of maps to an array of arrays.
     *
     * @param mapArr {Map[]} An array containing a map for each row. Each
     *          row-map contains the column IDs as key and the cell values as value.
     * @param rememberMaps {Boolean ? false} Whether to remember the original maps.
     *        If true {@link #getRowData} will return the original map.
     * @return {var[][]} An array containing an array for each row. Each
     *           row-array contains the values in that row in the order of the columns
     *           in this model.
     */
    _mapArray2RowArr : function(mapArr, rememberMaps)
    {
      var rowCount = mapArr.length;
      var columnCount = this.getColumnCount();
      var dataArr = new Array(rowCount);
      var columnArr;

      for (var i=0; i<rowCount; ++i)
      {
      columnArr = [];
      if (rememberMaps) {
        columnArr.originalData = mapArr[i];
      }

        for (var j=0; j<columnCount; ++j) {
          columnArr[j] = mapArr[i][this.getColumnId(j)];
        }

        dataArr[i] = columnArr;
      }

      return dataArr;
    }
  },


  destruct : function()
  {
    this.__rowArr = this.__editableColArr = this.__sortMethods =
      this.__sortableColArr = null;
  }
});
