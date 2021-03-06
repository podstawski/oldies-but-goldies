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
 * The data model of a table.
 */
qx.Interface.define("qx.ui.table.ITableModel",
{
  /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events : {
    /**
     * Fired when the table data changed (the stuff shown in the table body).
     * The data property of the event may be null or a map having the following attributes:
     * <ul>
     *   <li>firstRow: The index of the first row that has changed.</li>
     *   <li>lastRow: The index of the last row that has changed.</li>
     *   <li>firstColumn: The model index of the first column that has changed.</li>
     *   <li>lastColumn: The model index of the last column that has changed.</li>
     * </ul>
     */
    "dataChanged" : "qx.event.type.Data",

    /**
     * Fired when the meta data changed (the stuff shown in the table header).
     */
    "metaDataChanged" : "qx.event.type.Event",

    /**
     * Fired after the table is sorted (but before the metaDataChanged event)
     */
    "sorted" : "qx.event.type.Data"
  },


  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /**
     * Returns the number of rows in the model.
     *
     * @abstract
     * @return {Integer} the number of rows.
     */
    getRowCount : function() {},


    /**
     *
     * Returns the data of one row. This function may be overridden by models which hold
     * all data of a row in one object. By using this function, clients have a way of
     * quickly retrieving the entire row data.
     *
     * <b>Important:</b>Models which do not have their row data accessible in one object
     * may return null.
     *
     * @param rowIndex {Integer} the model index of the row.
     * @return {Object} the row data as an object or null if the model does not support row data
     *                    objects. The details on the object returned are determined by the model
     *                    implementation only.
     */
    getRowData : function(rowIndex) {},


    /**
     * Returns the number of columns in the model.
     *
     * @abstract
     * @return {Integer} the number of columns.
     */
    getColumnCount : function() {},


    /**
     * Returns the ID of column. The ID may be used to identify columns
     * independent from their index in the model. E.g. for being aware of added
     * columns when saving the width of a column.
     *
     * @abstract
     * @param columnIndex {Integer} the index of the column.
     * @return {String} the ID of the column.
     */
    getColumnId : function(columnIndex) {},


    /**
     * Returns the index of a column.
     *
     * @abstract
     * @param columnId {String} the ID of the column.
     * @return {Integer} the index of the column.
     */
    getColumnIndexById : function(columnId) {},


    /**
     * Returns the name of a column. This name will be shown to the user in the
     * table header.
     *
     * @abstract
     * @param columnIndex {Integer} the index of the column.
     * @return {String} the name of the column.
     */
    getColumnName : function(columnIndex) {},


    /**
     * Returns whether a column is editable.
     *
     * @param columnIndex {Integer} the column to check.
     * @return {Boolean} whether the column is editable.
     */
    isColumnEditable : function(columnIndex) {},


    /**
     * Returns whether a column is sortable.
     *
     * @param columnIndex {Integer} the column to check.
     * @return {Boolean} whether the column is sortable.
     */
    isColumnSortable : function(columnIndex) {},


    /**
     * Sorts the model by a column.
     *
     * @param columnIndex {Integer} the column to sort by.
     * @param ascending {Boolean} whether to sort ascending.
     * @return {void}
     */
    sortByColumn : function(columnIndex, ascending) {},


    /**
     * Returns the column index the model is sorted by. If the model is not sorted
     * -1 is returned.
     *
     * @return {Integer} the column index the model is sorted by.
     */
    getSortColumnIndex : function() {},


    /**
     * Returns whether the model is sorted ascending.
     *
     * @return {Boolean} whether the model is sorted ascending.
     */
    isSortAscending : function() {},


    /**
     * Prefetches some rows. This is a hint to the model that the specified rows
     * will be read soon.
     *
     * @param firstRowIndex {Integer} the index of first row.
     * @param lastRowIndex {Integer} the index of last row.
     * @return {void}
     */
    prefetchRows : function(firstRowIndex, lastRowIndex) {},


    /**
     * Returns a cell value by column index.
     *
     * @abstract
     * @param columnIndex {Integer} the index of the column.
     * @param rowIndex {Integer} the index of the row.
     * @return {var} The value of the cell.
     * @see #getValueById
     */
    getValue : function(columnIndex, rowIndex) {},


    /**
     * Returns a cell value by column ID.
     *
     * Whenever you have the choice, use {@link #getValue()} instead,
     * because this should be faster.
     *
     * @param columnId {String} the ID of the column.
     * @param rowIndex {Integer} the index of the row.
     * @return {var} the value of the cell.
     */
    getValueById : function(columnId, rowIndex) {},


    /**
     * Sets a cell value by column index.
     *
     * @abstract
     * @param columnIndex {Integer} The index of the column.
     * @param rowIndex {Integer} the index of the row.
     * @param value {var} The new value.
     * @return {void}
     * @see #setValueById
     */
    setValue : function(columnIndex, rowIndex, value) {},


    /**
     * Sets a cell value by column ID.
     *
     * Whenever you have the choice, use {@link #setValue()} instead,
     * because this should be faster.
     *
     * @param columnId {String} The ID of the column.
     * @param rowIndex {Integer} The index of the row.
     * @param value {var} The new value.
     */
    setValueById : function(columnId, rowIndex, value) {}
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
 * An abstract table model that performs the column handling, so subclasses only
 * need to care for row handling.
 */
qx.Class.define("qx.ui.table.model.Abstract",
{
  type : "abstract",
  extend : qx.core.Object,
  implement : qx.ui.table.ITableModel,


  events :
  {
    /**
     * Fired when the table data changed (the stuff shown in the table body).
     * The data property of the event will be a map having the following
     * attributes:
     * <ul>
     *   <li>firstRow: The index of the first row that has changed.</li>
     *   <li>lastRow: The index of the last row that has changed.</li>
     *   <li>firstColumn: The model index of the first column that has changed.</li>
     *   <li>lastColumn: The model index of the last column that has changed.</li>
     * </ul>
     *
     * Additionally, if the data changed as a result of rows being removed
     * from the data model, then these additional attributes will be in the
     * data:
     * <ul>
     *   <li>removeStart: The model index of the first row that was removed.</li>
     *   <li>removeCount: The number of rows that were removed.</li>
     * </ul>
     */
    "dataChanged" : "qx.event.type.Data",

    /**
     * Fired when the meta data changed (the stuff shown in the table header).
     */
    "metaDataChanged" : "qx.event.type.Event",

    /**
     * Fired after the table is sorted (but before the metaDataChanged event)
     */
    "sorted" : "qx.event.type.Data"
  },


  construct : function()
  {
    this.base(arguments);

    this.__columnIdArr = [];
    this.__columnNameArr = [];
    this.__columnIndexMap = {};
  },


  members :
  {
    __columnIdArr : null,
    __columnNameArr : null,
    __columnIndexMap : null,
    __internalChange : null,


    /**
     * Initialize the table model <--> table interaction. The table model is
     * passed to the table constructor, but the table model doesn't otherwise
     * know anything about the table nor can it operate on table
     * properties. This function provides the capability for the table model
     * to specify characteristics of the table. It is called when the table
     * model is applied to the table.
     *
     * @param table {qx.ui.table.Table}
     *   The table to which this model is attached
     */
    init : function(table) {
      // default implementation has nothing to do
    },

    /**
     * Abstract method
     * @throws {Error} An error if this method is called.
     */
    getRowCount : function() {
      throw new Error("getRowCount is abstract");
    },

    getRowData : function(rowIndex) {
      return null;
    },

    isColumnEditable : function(columnIndex) {
      return false;
    },

    isColumnSortable : function(columnIndex) {
      return false;
    },

    sortByColumn : function(columnIndex, ascending) {
    },

    getSortColumnIndex : function() {
      return -1;
    },

    isSortAscending : function() {
      return true;
    },

    prefetchRows : function(firstRowIndex, lastRowIndex) {
    },

    /**
     * Abstract method
     *
     * @param columnIndex {Integer} the index of the column
     * @param rowIndex {Integer} the index of the row
     *
     * @throws {Error} An error if this method is called.
     */
    getValue : function(columnIndex, rowIndex) {
      throw new Error("getValue is abstract");
    },

    getValueById : function(columnId, rowIndex) {
      return this.getValue(this.getColumnIndexById(columnId), rowIndex);
    },

    /**
     * Abstract method
     *
     * @param columnIndex {Integer} index of the column
     * @param rowIndex {Integer} index of the row
     * @param value {Var} Value to be set
     *
     * @throws {Error} An error if this method is called.
     */
    setValue : function(columnIndex, rowIndex, value) {
      throw new Error("setValue is abstract");
    },

    setValueById : function(columnId, rowIndex, value) {
      this.setValue(this.getColumnIndexById(columnId), rowIndex, value);
    },

    // overridden
    getColumnCount : function() {
      return this.__columnIdArr.length;
    },

    // overridden
    getColumnIndexById : function(columnId) {
      return this.__columnIndexMap[columnId];
    },

    // overridden
    getColumnId : function(columnIndex) {
      return this.__columnIdArr[columnIndex];
    },

    // overridden
    getColumnName : function(columnIndex) {
      return this.__columnNameArr[columnIndex];
    },


    /**
     * Sets the column IDs. These IDs may be used internally to identify a
     * column.
     *
     * Note: This will clear previously set column names.
     *
     *
     * @param columnIdArr {String[]} the IDs of the columns.
     * @return {void}
     * @see #setColumns
     */
    setColumnIds : function(columnIdArr)
    {
      this.__columnIdArr = columnIdArr;

      // Create the reverse map
      this.__columnIndexMap = {};

      for (var i=0; i<columnIdArr.length; i++) {
        this.__columnIndexMap[columnIdArr[i]] = i;
      }

      this.__columnNameArr = new Array(columnIdArr.length);

      // Inform the listeners
      if (!this.__internalChange) {
        this.fireEvent("metaDataChanged");
      }
    },


    /**
     * Sets the column names. These names will be shown to the user.
     *
     * Note: The column IDs have to be defined before.
     *
     *
     * @param columnNameArr {String[]} the names of the columns.
     * @return {void}
     * @throws {Error} If the amount of given columns is different from the table.
     * @see #setColumnIds
     */
    setColumnNamesByIndex : function(columnNameArr)
    {
      if (this.__columnIdArr.length != columnNameArr.length) {
        throw new Error("this.__columnIdArr and columnNameArr have different length: " + this.__columnIdArr.length + " != " + columnNameArr.length);
      }

      this.__columnNameArr = columnNameArr;

      // Inform the listeners
      this.fireEvent("metaDataChanged");
    },


    /**
     * Sets the column names. These names will be shown to the user.
     *
     * Note: The column IDs have to be defined before.
     *
     *
     * @param columnNameMap {Map} a map containing the column IDs as keys and the
     *          column name as values.
     * @return {void}
     * @see #setColumnIds
     */
    setColumnNamesById : function(columnNameMap)
    {
      this.__columnNameArr = new Array(this.__columnIdArr.length);

      for (var i=0; i<this.__columnIdArr.length; ++i) {
        this.__columnNameArr[i] = columnNameMap[this.__columnIdArr[i]];
      }
    },


    /**
     * Sets the column names (and optionally IDs)
     *
     * Note: You can not change the _number_ of columns this way.  The number
     *       of columns is highly intertwined in the entire table operation,
     *       and dynamically changing it would require as much work as just
     *       recreating your table.  If you must change the number of columns
     *       in a table then you should remove the table and add a new one.
     *
     * @param columnNameArr {String[]}
     *   The column names. These names will be shown to the user.
     *
     * @param columnIdArr {String[] ? null}
     *   The column IDs. These IDs may be used internally to identify a
     *   column. If null, the column names are used as IDs unless ID values
     *   have already been set. If ID values have already been set, they will
     *   continue to be used if no ID values are explicitly provided here.
     *
     * @throws {Error} If the amount of given columns is different from the table.
     *
     * @return {void}
     */
    setColumns : function(columnNameArr, columnIdArr)
    {
      var bSetIds = this.__columnIdArr.length == 0 || columnIdArr;

      if (columnIdArr == null) {
        if (this.__columnIdArr.length == 0) {
          columnIdArr = columnNameArr;
        } else {
          columnIdArr = this.__columnIdArr;
        }
      }

      if (columnIdArr.length != columnNameArr.length) {
        throw new Error("columnIdArr and columnNameArr have different length: " + columnIdArr.length + " != " + columnNameArr.length);
      }

      if (bSetIds)
      {
        this.__internalChange = true;
        this.setColumnIds(columnIdArr);
        this.__internalChange = false;
      }

      this.setColumnNamesByIndex(columnNameArr);
    }
  },


  destruct : function() {
    this.__columnIdArr = this.__columnNameArr = this.__columnIndexMap = null;
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
 * A table model that loads its data from a backend.
 * <p>
 * Only a subset of the available rows, those which are within or near the
 * currently visible area, are loaded. If a quick scroll operation occurs,
 * rows will soon be displayed using asynchronous loading in the background.
 * All loaded data is managed through a cache which automatically removes
 * the oldest used rows when it gets full.
 * <p>
 * This class is abstract: The actual loading of row data must be done by
 * subclasses.
 */
qx.Class.define("qx.ui.table.model.Remote",
{
  extend : qx.ui.table.model.Abstract,




  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  construct : function()
  {
    this.base(arguments);

    this.__sortColumnIndex = -1;
    this.__sortAscending = true;
    this.__rowCount = -1;

    this.__lruCounter = 0;

    // Holds the index of the first block that is currently loading.
    // Is -1 if there is currently no request on its way.
    this.__firstLoadingBlock = -1;

    // Holds the index of the first row that should be loaded when the response of
    // the current request arrives. Is -1 we need no following request.
    this.__firstRowToLoad = -1;

    // Counterpart to __firstRowToLoad
    this.__lastRowToLoad = -1;

    // Holds whether the current request will bring obsolete data. When true the
    // response of the current request will be ignored.
    this.__ignoreCurrentRequest = false;

    this.__rowBlockCache = {};
    this.__rowBlockCount = 0;

    this.__sortableColArr = null;
    this.__editableColArr = null;
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {

    /** The number of rows that are stored in one cache block. */
    blockSize :
    {
      check : "Integer",
      init : 50
    },

    /** The maximum number of row blocks kept in the cache. */
    maxCachedBlockCount :
    {
      check : "Integer",
      init : 15
    },


    /**
     * Whether to clear the cache when some rows are removed.
     * If true the rows are removed locally in the cache.
     */
    clearCacheOnRemove :
    {
      check : "Boolean",
      init : false
    },

    /**
     * Whether to block remote requests for the row count while a request for
     * the row count is pending. Row counts are requested at various times and
     * from various parts of the code, resulting in numerous requests to the
     * user-provided _loadRowCount() method, often while other requests are
     * already pending. The default behavior now ignores requests to load a
     * new row count if such a request is already pending. It is therefore now
     * conceivable that the row count changes between an initial request for
     * the row count and a later (ignored) request. Since the chance of this
     * is low, the desirability of reducing the server requests outweighs the
     * slight possibility of an altered count (which will, by the way, be
     * detected soon thereafter upon the next request for the row count). If
     * the old behavior is desired, set this property to false.
     */
    blockConcurrentLoadRowCount:
    {
      check : "Boolean",
      init  : true
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __rowCount : null,
    __ignoreCurrentRequest : null,

    __lruCounter : null,
    __firstLoadingBlock : null,
    __firstRowToLoad : null,
    __lastRowToLoad : null,
    __rowBlockCache : null,
    __rowBlockCount : null,

    __sortColumnIndex : null,
    __sortAscending : null,

    __editableColArr : null,
    __sortableColArr : null,

    __loadRowCountRequestRunning : false,


    /**
     * Returns whether the current request is ignored by the model.
     *
     * @return {Boolean} true when the current request is ignored by the model.
     */
    _getIgnoreCurrentRequest : function()
    {
      return this.__ignoreCurrentRequest;
    },


    // overridden
    getRowCount : function()
    {
      if (this.__rowCount == -1)
      {
        if (! this.__loadRowCountRequestRunning ||
            ! this.getBlockConcurrentLoadRowCount())
        {
          this.__loadRowCountRequestRunning = true;
          this._loadRowCount();
        }

        // NOTE: _loadRowCount may set this.__rowCount
        return (this.__rowCount == -1) ? 0 : this.__rowCount;
      }
      else
      {
        return this.__rowCount;
      }
    },


    /**
     * Implementing classes have to call {@link #_onRowCountLoaded} when the
     * server response arrived. That method has to be called! Even when there
     * was an error.
     *
     * @abstract
     * @return {void}
     * @throws the abstract function warning.
     */
    _loadRowCount : function() {
      throw new Error("_loadRowCount is abstract");
    },


    /**
     * Sets the row count.
     *
     * Has to be called by {@link #_loadRowCount}.
     *
     * @param rowCount {Integer} the number of rows in this model or null if loading.
     * @return {void}
     */
    _onRowCountLoaded : function(rowCount)
    {
      if (this.getBlockConcurrentLoadRowCount())
      {
        // There's no longer a loadRowCount() in progress
        this.__loadRowCountRequestRunning = false;
      }

      // this.debug("row count loaded: " + rowCount);
      if (rowCount == null || rowCount < 0) {
        rowCount = 0;
      }

      this.__rowCount = Number(rowCount);

      // Inform the listeners
      var data =
      {
        firstRow    : 0,
        lastRow     : rowCount - 1,
        firstColumn : 0,
        lastColumn  : this.getColumnCount() - 1
      };

      this.fireDataEvent("dataChanged", data);
    },


    /**
     * Reloads the model and clears the local cache.
     *
     * @return {void}
     */
    reloadData : function()
    {
      this.clearCache();

      // If there is currently a request on its way, then this request will bring
      // obsolete data -> Ignore it
      if (this.__firstLoadingBlock != -1) {
        var cancelingSuceed = this._cancelCurrentRequest();
        if (cancelingSuceed) {
          // The request was canceled -> We're not loading any blocks any more
          this.__firstLoadingBlock = -1;
          this.__ignoreCurrentRequest = false;
        } else {
          // The request was not canceled -> Ignore it
          this.__ignoreCurrentRequest = true;
        }
      }

      // Forget a possibly outstanding request
      // (_loadRowCount will tell the listeners anyway, that the whole table
      // changed)
      //
      // NOTE: This will inform the listeners as soon as the new row count is
      // known
      this.__firstRowToLoad = -1;
      this.__lastRowToLoad = -1;
      this.__loadRowCountRequestRunning = true;
      this._loadRowCount();
    },


    /**
     * Clears the cache.
     *
     * @return {void}
     */
    clearCache : function()
    {
      this.__rowBlockCache = {};
      this.__rowBlockCount = 0;
    },


    /**
     * Returns the current state of the cache.
     * <p>
     * Do not change anything in the returned data. This breaks the model state.
     * Use this method only together with {@link #restoreCacheContent} for backing
     * up state for a later restore.
     *
     * @return {Map} the current cache state.
     */
    getCacheContent : function() {
      return {
        sortColumnIndex : this.__sortColumnIndex,
        sortAscending   : this.__sortAscending,
        rowCount        : this.__rowCount,
        lruCounter      : this.__lruCounter,
        rowBlockCache   : this.__rowBlockCache,
        rowBlockCount   : this.__rowBlockCount
      };
    },


    /**
     * Restores a cache state created by {@link #getCacheContent}.
     *
     * @param cacheContent {Map} An old cache state.
     */
    restoreCacheContent : function(cacheContent)
    {
      // If there is currently a request on its way, then this request will bring
      // obsolete data -> Ignore it
      if (this.__firstLoadingBlock != -1)
      {
        // Try to cancel the current request
        var cancelingSuceed = this._cancelCurrentRequest();

        if (cancelingSuceed)
        {
          // The request was canceled -> We're not loading any blocks any more
          this.__firstLoadingBlock = -1;
          this.__ignoreCurrentRequest = false;
        }
        else
        {
          // The request was not canceled -> Ignore it
          this.__ignoreCurrentRequest = true;
        }
      }

      // Restore the cache content
      this.__sortColumnIndex = cacheContent.sortColumnIndex;
      this.__sortAscending = cacheContent.sortAscending;
      this.__rowCount = cacheContent.rowCount;
      this.__lruCounter = cacheContent.lruCounter;
      this.__rowBlockCache = cacheContent.rowBlockCache;
      this.__rowBlockCount = cacheContent.rowBlockCount;

      // Inform the listeners
      var data =
      {
        firstRow    : 0,
        lastRow     : this.__rowCount - 1,
        firstColumn : 0,
        lastColumn  : this.getColumnCount() - 1
      };

      this.fireDataEvent("dataChanged", data);
    },


    /**
     * Cancels the current request if possible.
     *
     * Should be overridden by subclasses if they are able to cancel requests. This
     * allows sending a new request directly after a call of {@link #reloadData}.
     *
     * @return {Boolean} whether the request was canceled.
     */
    _cancelCurrentRequest : function() {
      return false;
    },


    /**
     * Iterates through all cached rows.
     *
     * The iterator will be called for each cached row with two parameters: The row
     * index of the current row (Integer) and the row data of that row (var[]). If
     * the iterator returns something this will be used as new row data.
     *
     * The iterator is called in the same order as the rows are in the model
     * (the row index is always ascending).
     *
     * @param iterator {Function} The iterator function to call.
     * @param object {Object} context of the iterator
     * @return {void}
     */
    iterateCachedRows : function(iterator, object)
    {
      var blockSize = this.getBlockSize();
      var blockCount = Math.ceil(this.getRowCount() / blockSize);

      // Remove the row and move the rows of all following blocks
      for (var block=0; block<=blockCount; block++)
      {
        var blockData = this.__rowBlockCache[block];

        if (blockData != null)
        {
          var rowOffset = block * blockSize;
          var rowDataArr = blockData.rowDataArr;

          for (var relRow=0; relRow<rowDataArr.length; relRow++)
          {
            // Call the iterator for this row
            var rowData = rowDataArr[relRow];
            var newRowData = iterator.call(object, rowOffset + relRow, rowData);

            if (newRowData != null) {
              rowDataArr[relRow] = newRowData;
            }
          }
        }
      }
    },

    // overridden
    prefetchRows : function(firstRowIndex, lastRowIndex)
    {
      // this.debug("Prefetch wanted: " + firstRowIndex + ".." + lastRowIndex);
      if (this.__firstLoadingBlock == -1)
      {
        var blockSize = this.getBlockSize();
        var totalBlockCount = Math.ceil(this.__rowCount / blockSize);

        // There is currently no request running -> Start a new one
        // NOTE: We load one more block above and below to have a smooth
        //       scrolling into the next block without blank cells
        var firstBlock = parseInt(firstRowIndex / blockSize, 10) - 1;

        if (firstBlock < 0) {
          firstBlock = 0;
        }

        var lastBlock = parseInt(lastRowIndex / blockSize, 10) + 1;

        if (lastBlock >= totalBlockCount) {
          lastBlock = totalBlockCount - 1;
        }

        // Check which blocks we have to load
        var firstBlockToLoad = -1;
        var lastBlockToLoad = -1;

        for (var block=firstBlock; block<=lastBlock; block++)
        {
          if (this.__rowBlockCache[block] == null || this.__rowBlockCache[block].isDirty)
          {
            // We don't have this block
            if (firstBlockToLoad == -1) {
              firstBlockToLoad = block;
            }

            lastBlockToLoad = block;
          }
        }

        // Load the blocks
        if (firstBlockToLoad != -1)
        {
          this.__firstRowToLoad = -1;
          this.__lastRowToLoad = -1;

          this.__firstLoadingBlock = firstBlockToLoad;

          // this.debug("Starting server request. rows: " + firstRowIndex + ".." + lastRowIndex + ", blocks: " + firstBlockToLoad + ".." + lastBlockToLoad);
          this._loadRowData(firstBlockToLoad * blockSize, (lastBlockToLoad + 1) * blockSize - 1);
        }
      }
      else
      {
        // There is already a request running -> Remember this request
        // so it can be executed after the current one is finished.
        this.__firstRowToLoad = firstRowIndex;
        this.__lastRowToLoad = lastRowIndex;
      }
    },


    /**
     * Loads some row data from the server.
     *
     * Implementing classes have to call {@link #_onRowDataLoaded} when the server
     * response arrived. That method has to be called! Even when there was an error.
     *
     * @abstract
     * @param firstRow {Integer} The index of the first row to load.
     * @param lastRow {Integer} The index of the last row to load.
     * @return {void}
     * @throws the abstract function warning.
     */
    _loadRowData : function(firstRow, lastRow) {
      throw new Error("_loadRowCount is abstract");
    },


    /**
     * Sets row data.
     *
     * Has to be called by {@link #_loadRowData}.
     *
     * @param rowDataArr {Map[]} the loaded row data or null if there was an error.
     * @return {void}
     */
    _onRowDataLoaded : function(rowDataArr)
    {
      if (rowDataArr != null && !this.__ignoreCurrentRequest)
      {
        var blockSize = this.getBlockSize();
        var blockCount = Math.ceil(rowDataArr.length / blockSize);

        if (blockCount == 1)
        {
          // We got one block -> Use the rowData directly
          this._setRowBlockData(this.__firstLoadingBlock, rowDataArr);
        }
        else
        {
          // We got more than one block -> We've to split the rowData
          for (var i=0; i<blockCount; i++)
          {
            var rowOffset = i * blockSize;
            var blockRowData = [];
            var mailCount = Math.min(blockSize, rowDataArr.length - rowOffset);

            for (var row=0; row<mailCount; row++) {
              blockRowData.push(rowDataArr[rowOffset + row]);
            }

            this._setRowBlockData(this.__firstLoadingBlock + i, blockRowData);
          }
        }

        // this.debug("Got server answer. blocks: " + this.__firstLoadingBlock + ".." + (this.__firstLoadingBlock + blockCount - 1) + ". mail count: " + rowDataArr.length + " block count:" + blockCount);
        // Inform the listeners
        var data =
        {
          firstRow    : this.__firstLoadingBlock * blockSize,
          lastRow     : (this.__firstLoadingBlock + blockCount + 1) * blockSize - 1,
          firstColumn : 0,
          lastColumn  : this.getColumnCount() - 1
        };

        this.fireDataEvent("dataChanged", data);
      }

      // We're not loading any blocks any more
      this.__firstLoadingBlock = -1;
      this.__ignoreCurrentRequest = false;

      // Check whether we have to start a new request
      if (this.__firstRowToLoad != -1) {
        this.prefetchRows(this.__firstRowToLoad, this.__lastRowToLoad);
      }
    },


    /**
     * Sets the data of one block.
     *
     * @param block {Integer} the index of the block.
     * @param rowDataArr {var[][]} the data to set.
     * @return {void}
     */
    _setRowBlockData : function(block, rowDataArr)
    {
      if (this.__rowBlockCache[block] == null)
      {

        // This is a new block -> Check whether we have to remove another block first
        this.__rowBlockCount++;

        while (this.__rowBlockCount > this.getMaxCachedBlockCount())
        {
          // Find the last recently used block
          // NOTE: We never remove block 0 and 1
          var lruBlock;
          var minLru = this.__lruCounter;

          for (var currBlock in this.__rowBlockCache)
          {
            var currLru = this.__rowBlockCache[currBlock].lru;

            if (currLru < minLru && currBlock > 1)
            {
              minLru = currLru;
              lruBlock = currBlock;
            }
          }

          // Remove that block
          // this.debug("Removing block: " + lruBlock + ". current LRU: " + this.__lruCounter);
          delete this.__rowBlockCache[lruBlock];
          this.__rowBlockCount--;
        }
      }

      this.__rowBlockCache[block] =
      {
        lru        : ++this.__lruCounter,
        rowDataArr : rowDataArr
      };
    },


    /**
     * Removes a row from the model.
     *
     * @param rowIndex {Integer} the index of the row to remove.
     * @return {void}
     */
    removeRow : function(rowIndex)
    {
      if (this.getClearCacheOnRemove())
      {
        this.clearCache();

        // Inform the listeners
        var data =
        {
          firstRow    : 0,
          lastRow     : this.getRowCount() - 1,
          firstColumn : 0,
          lastColumn  : this.getColumnCount() - 1
        };

        this.fireDataEvent("dataChanged", data);
      }
      else
      {
        var blockSize = this.getBlockSize();
        var blockCount = Math.ceil(this.getRowCount() / blockSize);
        var startBlock = parseInt(rowIndex / blockSize, 10);

        // Remove the row and move the rows of all following blocks
        for (var block=startBlock; block<=blockCount; block++)
        {
          var blockData = this.__rowBlockCache[block];

          if (blockData != null)
          {
            // Remove the row in the start block
            // NOTE: In the other blocks the first row is removed
            //       (This is the row that was)
            var removeIndex = 0;

            if (block == startBlock) {
              removeIndex = rowIndex - block * blockSize;
            }

            blockData.rowDataArr.splice(removeIndex, 1);

            if (block == blockCount - 1)
            {
              // This is the last block
              if (blockData.rowDataArr.length == 0)
              {
                // It is empty now -> Remove it
                delete this.__rowBlockCache[block];
              }
            }
            else
            {
              // Try to copy the first row of the next block to the end of this block
              // so this block can stays clean
              var nextBlockData = this.__rowBlockCache[block + 1];

              if (nextBlockData != null) {
                blockData.rowDataArr.push(nextBlockData.rowDataArr[0]);
              }
              else
              {
                // There is no row to move -> Mark this block as dirty
                blockData.isDirty = true;
              }
            }
          }
        }

        if (this.__rowCount != -1) {
          this.__rowCount--;
        }

        // Inform the listeners
        if (this.hasListener("dataChanged"))
        {
          var data =
          {
            firstRow    : rowIndex,
            lastRow     : this.getRowCount() - 1,
            firstColumn : 0,
            lastColumn  : this.getColumnCount() - 1
          };

          this.fireDataEvent("dataChanged", data);
        }
      }
    },


    /**
     *
     * See overridden method for details.
     *
     * @param rowIndex {Integer} the model index of the row.
     * @return {Object} Map containing a value for each column.
     */
    getRowData : function(rowIndex)
    {
      var blockSize = this.getBlockSize();
      var block = parseInt(rowIndex / blockSize, 10);
      var blockData = this.__rowBlockCache[block];

      if (blockData == null)
      {
        // This block is not (yet) loaded
        return null;
      }
      else
      {
        var rowData = blockData.rowDataArr[rowIndex - (block * blockSize)];

        // Update the last recently used counter
        if (blockData.lru != this.__lruCounter) {
          blockData.lru = ++this.__lruCounter;
        }

        return rowData;
      }
    },

    // overridden
    getValue : function(columnIndex, rowIndex)
    {
      var rowData = this.getRowData(rowIndex);

      if (rowData == null) {
        return null;
      }
      else
      {
        var columnId = this.getColumnId(columnIndex);
        return rowData[columnId];
      }
    },

    // overridden
    setValue : function(columnIndex, rowIndex, value)
    {
      var rowData = this.getRowData(rowIndex);

      if (rowData == null) {
        // row has not yet been loaded or does not exist
        return;
      }
      else
      {
        var columnId = this.getColumnId(columnIndex);
        rowData[columnId] = value;

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
      }
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
    isColumnEditable : function(columnIndex)
    {
      return (this.__editableColArr
              ? (this.__editableColArr[columnIndex] == true)
              : false);
    },

   /**
     * Sets whether a column is sortable.
     *
     * @param columnIndex {Integer} the column of which to set the sortable state.
     * @param sortable {Boolean} whether the column should be sortable.
     * @return {void}
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
    isColumnSortable : function(columnIndex)
    {
      return (
        this.__sortableColArr
        ? (this.__sortableColArr[columnIndex] !== false)
        : true
      );
    },

    // overridden
    sortByColumn : function(columnIndex, ascending)
    {
      if (this.__sortColumnIndex != columnIndex || this.__sortAscending != ascending)
      {
        this.__sortColumnIndex = columnIndex;
        this.__sortAscending = ascending;

        this.clearCache();

        // Inform the listeners
        this.fireEvent("metaDataChanged");
      }
    },

    // overridden
    getSortColumnIndex : function() {
      return this.__sortColumnIndex;
    },

    // overridden
    isSortAscending : function() {
      return this.__sortAscending;
    },

    /**
     * Sets the sorted column without sorting the data.
     * Use this method, if you want to mark the column as the sorted column,
     * (e.g. for appearance reason), but the sorting of the data will be done
     * in another step.
     *
     * @param sortColumnIndex {Integer} the column, which shall be marked as the sorted column.
     * @return {void}
     */
    setSortColumnIndexWithoutSortingData : function(sortColumnIndex)
    {
      this.__sortColumnIndex = sortColumnIndex;
    },

    /**
     * Sets the direction of the sorting without sorting the data.
     * Use this method, if you want to set the direction of sorting, (e.g
     * for appearance reason), but the sorting of the data will be done in
     * another step.
     *
     * @param sortAscending {boolean} whether the sorting direction is ascending
     *        (true) or not (false).
     * @return {void}
     */
    setSortAscendingWithoutSortingData : function (sortAscending)
    {
      this.__sortAscending = sortAscending;
    }

  },

  destruct : function() {
    this.__sortableColArr = this.__editableColArr = this.__rowBlockCache = null;
  }
});
