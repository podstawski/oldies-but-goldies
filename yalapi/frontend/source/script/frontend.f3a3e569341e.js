/* ************************************************************************

 Copyright:
 Tobias Oetiker, OETIKER+PARTNER AG, www.oetiker.ch
 Mustafa Sak, SAK systems, www.saksys.de

 License:
 LGPL: http://www.gnu.org/licenses/lgpl.html
 EPL: http://www.eclipse.org/org/documents/epl-v10.php
 See the LICENSE file in the project's top-level directory for details.

 Authors:
 * Tobias Oetiker (oetiker)
 * Mustafa Sak

 ************************************************************************ */

/**
 * If a traditional selectbox covers lots of options, it becomes pretty upractical
 * to navigate. This widget lets the user enter part of the item of interest and
 * filters the drop down box accordingly.
 * It uses single column {@link qx.ui.table.Table} to present the dropdown box.
 * The table model must provide a {setSearchString} method. If you have static data, you
 * may want to try the included {@link combotable.SearchableModel}.
 * Combined with {@link qx.ui.table.model.Remote} it is possible to
 * provide access to huge datasets.
 * The model in use must provide two columns. The first column containing
 * the id/key of the row and the second column the searchable data.
 *
 * @throws {Error} An error if the table model does not proved a {setSearchString} method.
 */
qx.Class.define("combotable.ComboTable", {
    extend : qx.ui.form.ComboBox,
    include : [ qx.ui.form.MModelProperty ],

    /**
     * @param tableModel {qx.ui.table.ITableModel ? null}
     *   A table model with {setSearchString} method and two columns as described above.
     */
    construct : function(tableModel) {
        this.base(arguments);

        if (!tableModel.setSearchString) {
            throw new Error("tableModel must have a setSearchString method. Create your own model or use combotable.SearchableModel!");
        }

        this.__tableModel = tableModel;

        var tf = this.getChildControl("textfield");
        tf.setLiveUpdate(true);
        tf.addListener("input", this._onTextFieldInput, this);
        this.__timerMgr = qx.util.TimerManager.getInstance();
    },

    properties : {
        /**
         * Is the content of the table presently being reloaded ?
         */
        loading : {
            init  : false,
            check : "Boolean",
            apply : "_applyLoading"
        }
    },

    members : {
        __timerMgr : null,
        __tableModel : null,
        __updateTimer : null,
        __table : null,
        __highlighter: null,

        /**
         * As the popup data is recalculated adjust the selection and if the popup is already closed set
         * the field content.
         *
         * @return {void}
         */
        _onTableDataChanged : function() {
            this.setLoading(false);
            var tm = this.__tableModel;
            var table = this.__table;
            var rc = tm.getRowCount();
            var sm = table.getSelectionModel();
            sm.resetSelection();

            if (rc > 0) {
                if (this.getValue()) {
                    sm.setSelectionInterval(0, 0);
                    table.setFocusedCell(1, 0, true);
                    this.setValid(true);
                }
            }
            else {
                if (this.getRequired()) {
                    this.setValid(false);
                }
            }

            if (!this.getChildControl("popup").isVisible()) {
                var row = this.getSelectedRowData();

                if (row) {
                    this.setModel(row.key);
                    this.setValue(row.value);
                }
                else {
                    this.setModel(null);
                }
            }
        },


        /**
         * Show loading notice as the table reloads.
         *
         * @param value {var} new value
         * @param old {var} old value
         * @return {void}
         */
        _applyLoading : function(value, old) {
            this.__table.setVisibility(value ? 'hidden' : 'visible');
            qx.ui.core.queue.Visibility.flush();
            qx.html.Element.flush();
        },


        /**
         * Create the child chontrols.
         *
         * @param id {var} widget id
         * @param hash {Map} hash
         * @return {var} control
         */
        _createChildControlImpl : function(id, hash) {
            var control;

            switch (id) {
                case "list":
                    control = this.__makeTable();
                    break;
            }

            return control || this.base(arguments, id);
        },


        /**
         * Creat the table widget
         *
         * @return {Widget} table widget
         */
        __makeTable : function() {
            // Instantiate an instance of our local remote data model
            var tm = this.__tableModel;

            var custom = {
                tableColumnModel : function(obj) {
                    return new qx.ui.table.columnmodel.Resize(obj);
                },

                tablePaneHeader : function(obj) {
                    return new combotable.NoHeader(obj);
                },

                initiallyHiddenColumns : [ 0 ]
            };

            // Instantiate a table
            var container = new qx.ui.container.Composite(new qx.ui.layout.Canvas).set({
                height     : this.getMaxListHeight(),
                allowGrowX : true,
                allowGrowY : true
            });

            container.add(new qx.ui.basic.Label(this.tr('Filtering ...')).set({
                padding    : [ 3, 3, 3, 3 ],
                allowGrowX : true,
                allowGrowY : true,
                enabled    : false
            }));

            var table = this.__table = new qx.ui.table.Table(tm, custom).set({
                focusable         : false,
                keepFocus         : true,
                height            : null,
                width             : null,
                allowGrowX        : true,
                allowGrowY        : true,
                decorator         : null,
                alwaysUpdateCells : true
            });

            // once the user starts modifying the text of the combo box
            // start watching for table changes
            var textfield = this.getChildControl('textfield');

            textfield.addListenerOnce('input', function(e) {
                tm.addListener('dataChanged', this._onTableDataChanged, this);
            }, this);

            var armClick = function() {
                textfield.addListenerOnce('click', function(e) {
                    if (! textfield.hasState("selected")) {
                        textfield.selectAllText();
                    }
                });
            };

            armClick();
            textfield.addListener('focusout', armClick, this);

            table.getDataRowRenderer().setHighlightFocusRow(true);

            table.set({
                showCellFocusIndicator        : false,
                headerCellsVisible            : false,
                columnVisibilityButtonVisible : false,
                focusCellOnMouseMove          : true
            });

            var tcm = table.getTableColumnModel();
            this.__highlighter = new combotable.CellHighlighter();
            tcm.setDataCellRenderer(1, this.__highlighter);
            container.add(table, { edge : 0 });
            return container;
        },

        /**
         * reset the value of combobox
         */
        resetValue : function() {
            this.setValue(null);
            this.setModel(null);
            var tm = this.__tableModel;
            this.__highlighter.setSearchString(null);
            tm.setSearchString(null);
        },


        // overridden
        _onClick : function(e) {
            var target = e.getTarget();

            if (target == this.getChildControl("button")) {
                this.open();
            }
        },


        // overridden
        _onKeyPress : function(e) {
            var popup = this.getChildControl("popup");
            var iden = e.getKeyIdentifier();

            switch (iden) {
                case "Down":
                case "Up":
                    if (this.getLoading()) {
                        e.stop();
                        e.stopPropagation();
                        return;
                    }
                    if (!popup.isVisible()) {
                        this.open();
                    }

                    this['row' + iden]();
                    e.stop();
                    e.stopPropagation();
                    break;

                case "Enter":
                case "Escape":
                case "Tab":
                    if (this.getLoading()) {
                        e.stop();
                        e.stopPropagation();
                        return;
                    }
                    if (popup.isVisible()) {
                        e.stop();
                        e.stopPropagation();
                        this.close();
                    }
                    break;
            }
        },


        /**
         * Scroll down one row
         *
         * @return {void}
         */
        rowDown : function() {
            var row = this.getSelectedRowData();
            var table = this.__table;

            if (!row) {
                table.setFocusedCell(1, 0, true);
                table.getSelectionModel().setSelectionInterval(0, 0);
            }
            else {
                if (row.rowId + 1 < this.__tableModel.getRowCount()) {
                    table.setFocusedCell(1, row.rowId + 1, true);
                    table.getSelectionModel().setSelectionInterval(row.rowId + 1, row.rowId + 1);
                }
            }
        },


        /**
         * Scroll up one row
         *
         * @return {void}
         */
        rowUp : function() {
            var row = this.getSelectedRowData();
            var table = this.__table;

            if (!row) {
                table.setFocusedCell(1, 0, true);
                table.getSelectionModel().setSelectionInterval(0, 0);
            }
            else {
                if (row.rowId - 1 >= 0) {
                    table.setFocusedCell(1, row.rowId - 1, true);
                    table.getSelectionModel().setSelectionInterval(row.rowId - 1, row.rowId - 1);
                }
            }
        },


        // overridden
        _onListChangeSelection : function(e) {
        },


        // overridden
        _onPopupChangeVisibility : function(e) {
            var visibility = e.getData();

            if (visibility == 'hidden') {
                this.getChildControl("button").removeState("selected");
                var row = this.getSelectedRowData();

                if (row) {
                    this.setModel(row.key);
                    this.setValue(row.value);
                }
                else {
                    if (this.getValue()) {
                        this.setModel(null);
                        this.setValid(false);
                    }
                    else {
                        if (this.getRequired()) {
                            this.setValid(false);
                        }
                    }
                }
            }
            else {
                this.getChildControl("button").addState("selected");
            }
        },


        // overridden
        _onTextFieldInput : function(e) {
            var value = e.getData();
            var table = this.__table;
            this.open();
            var sm = table.getSelectionModel();
            sm.resetSelection();
            table.setFocusedCell(null, null, false);

            if (this.__updateTimer) {
                this.__timerMgr.stop(this.__updateTimer);
            }

            this.__updateTimer = this.__timerMgr.start(function(userData, timerId) {
                this.__updateTimer = null;

                if (this.__tableModel.getSearchString() != value) {
                    this.setLoading(true);
                    this.__highlighter.setSearchString(value);
                    this.__tableModel.setSearchString(value);
                }
            },
                    null, this, null, 150);

            this.fireDataEvent("input", value, e.getOldData());
        },


        // overridden
        _onTextFieldChangeValue : function(e) {
            this.fireDataEvent("changeValue", e.getData(), e.getOldData());
        },


        /**
         * get id and data curently selected
         *
         * @return {var} map with id and data and rowId keys
         */
        getSelectedRowData : function() {
            var table = this.__table;
            var sel = table.getSelectionModel().getSelectedRanges();
            var tm = this.__tableModel;
            for (var i = 0; i < sel.length; i++) {
                var interval = sel[i];

                for (var s = interval.minIndex; s <= interval.maxIndex; s++) {
                    var key = tm.getValue(0, s);
                    var value = tm.getValue(1, s);

                    return {
                        rowId : s,
                        key   : key,
                        value : value
                    };
                }
            }
            return null;
        }
    }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2008 Derrell Lipman

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Derrell Lipman (derrell)

************************************************************************ */

/**
 * Timer manipulation for handling multiple timed callbacks with the use of
 * only a single native timer object.
 *
 * Use of these timers is via the methods start() and stop().  Examples:
 * <pre class='javascript'>
 * var timer = qx.util.TimerManager.getInstance();
 *
 * // Start a 5-second recurrent timer.
 * // Note that the first expiration is after 3 seconds
 * // (last parameter is 3000) but each subsequent expiration is
 * // at 5 second intervals.
 * timer.start(function(userData, timerId)
 *             {
 *               this.debug("Recurrent 5-second timer: " + timerId);
 *             },
 *             5000,
 *             this,
 *             null,
 *             3000);
 *
 * // Start a 1-second one-shot timer
 * timer.start(function(userData, timerId)
 *             {
 *               this.debug("One-shot 1-second timer: " + timerId);
 *             },
 *             0,
 *             this,
 *             null,
 *             1000);
 *
 * // Start a 2-second recurrent timer that stops itself after
 * // three iterations
 * timer.start(function(userData, timerId)
 *             {
 *               this.debug("Recurrent 2-second timer with limit 3:" +
 *                          timerId);
 *               if (++userData.count == 3)
 *               {
 *                 this.debug("Stopping recurrent 2-second timer");
 *                 timer.stop(timerId);
 *               }
 *             },
 *             2000,
 *             this,
 *             { count : 0 });
 *
 * // Start an immediate one-shot timer
 * timer.start(function(userData, timerId)
 *             {
 *               this.debug("Immediate one-shot timer: " + timerId);
 *             });
 * </pre>
 */
qx.Class.define("qx.util.TimerManager",
{
  extend : qx.core.Object,
  type   : "singleton",

  statics :
  {
    /** Time-ordered queue of timers */
    __timerQueue : [],

    /** Saved data for each timer */
    __timerData  : {},

    /** Next timer id value is determined by incrementing this */
    __timerId    : 0
  },

  members :
  {
    /** Whether we're currently listening on the interval timer event */
    __timerListenerActive : false,

    /**
     * Start a new timer
     *
     * @param callback {Function}
     *   Function to be called upon expiration of the timer.  The function is
     *   passed these parameters:
     *   <dl>
     *     <dt>userData</dt>
     *       <dd>The user data provided to the start() method</dd>
     *     <dt>timerId</dt>
     *       <dd>The timer id, as was returned by the start() method</dd>
     *   </dl>
     *
     * @param recurTime {Integer|null}
     *   If null, the timer will not recur.  Once the callback function
     *   returns the first time, the timer will be removed from the timer
     *   queue.  If non-null, upon return from the callback function, the
     *   timer will be reset to this number of milliseconds.
     *
     * @param context {qx.core.Object|null}
     *   Context (this) the callback function is called with.  If not
     *   provided, this Timer singleton object is used.
     *
     * @param userData {Any}
     *   Data which is passed to the callback function upon timer expiry
     *
     * @param initialTime {Integer|null}
     *   Milliseconds before the callback function is called the very first
     *   time.  If not specified and recurTime is specified, then recurTime
     *   will be used as initialTime; otherwise initialTime will default
     *   to zero.
     *
     * @return {Integer}
     *   The timer id of this unique timer.  It may be provided to the stop()
     *   method to cancel a timer before expiration.
     */
    start : function(callback, recurTime, context, userData, initialTime)
    {
      // Get the expiration time for this timer
      if (! initialTime)
      {
        initialTime = recurTime || 0;
      }

      var expireAt = (new Date()).getTime() + initialTime;

      // Save the callback, user data, and requested recurrency time as well
      // as the current expiry time
      this.self(arguments).__timerData[++this.self(arguments).__timerId] =
        {
          callback  : callback,
          userData  : userData || null,
          expireAt  : expireAt,
          recurTime : recurTime,
          context   : context || this
        };

      // Insert this new timer on the time-ordered timer queue
      this.__insertNewTimer(expireAt, this.self(arguments).__timerId);

      // Give 'em the timer id
      return this.self(arguments).__timerId;
    },

    /**
     * Stop a running timer
     *
     * @param timerId {Integer}
     *   A timer id previously returned by start()
     *
     * @return {Boolean}
     *   <i>true</i> if the specified timer id was found (and removed);
     *   <i>false</i> if no such timer was found (i.e. it had already expired)
     */
    stop : function(timerId)
    {
      // Find this timer id in the time-ordered list
      var timerQueue = this.self(arguments).__timerQueue;
      var length = timerQueue.length;
      for (var i = 0; i < length; i++)
      {
        // Is this the one we're looking for?
        if (timerQueue[i] == timerId)
        {
          // Yup.  Remove it.
          timerQueue.splice(i, 1);

          // We found it so no need to continue looping through the queue
          break;
        }
      }

      // Ensure it's gone from the timer data map as well
      delete this.self(arguments).__timerData[timerId];

      // If there are no more timers pending...
      if (timerQueue.length == 0 && this.__timerListenerActive)
      {
        // ... then stop listening for the periodic timer
        qx.event.Idle.getInstance().removeListener("interval",
                                                   this.__processQueue,
                                                   this);
        this.__timerListenerActive = false;
      }
    },

    /**
     * Insert a timer on the time-ordered list of active timers.
     *
     * @param expireAt {Integer}
     *   Milliseconds from now when this timer should expire
     *
     * @param timerId {Integer}
     *   Id of the timer to be time-ordered
     *
     * @return {void}
     */
    __insertNewTimer : function(expireAt, timerId)
    {
      // The timer queue is time-ordered so that processing timers need not
      // search the queue; rather, it can simply look at the first element
      // and if not yet ready to fire, be done.  Search the queue for the
      // appropriate place to insert this timer.
      var timerQueue = this.self(arguments).__timerQueue;
      var timerData = this.self(arguments).__timerData;
      var length = timerQueue.length;
      for (var i = 0; i < length; i++)
      {
        // Have we reached a later time?
        if (timerData[timerQueue[i]].expireAt > expireAt)
        {
          // Yup.  Insert our new timer id before this element.
          timerQueue.splice(i, 0, timerId);

          // No need to loop through the queue further
          break;
        }
      }

      // Did we find someplace in the middle of the queue for it?
      if (timerQueue.length == length)
      {
        // Nope.  Insert it at the end.
        timerQueue.push(timerId);
      }

      // If this is the first element on the queue...
      if (! this.__timerListenerActive)
      {
        // ... then start listening for the periodic timer.
        qx.event.Idle.getInstance().addListener("interval",
                                                this.__processQueue,
                                                this);
        this.__timerListenerActive = true;
      }

    },

    /**
     * Process the queue of timers.  Call the registered callback function for
     * any timer which has expired.  If the timer is marked as recurrent, the
     * timer is restarted with the recurrent timeout following completion of
     * the callback function.
     *
     * @return {void}
     */
    __processQueue : function()
    {
      // Get the current time
      var timeNow = (new Date()).getTime();

      // While there are timer elements that need processing...
      var timerQueue = this.self(arguments).__timerQueue;
      var timerData = this.self(arguments).__timerData;

      // Is it time to process the first timer element yet?
      while (timerQueue.length > 0 &&
             timerData[timerQueue[0]].expireAt <= timeNow)
      {
        // Yup.  Do it.  First, remove element from the queue.
        var expiredTimerId = timerQueue.shift();

        // Call the handler function for this timer
        var expiredTimerData = timerData[expiredTimerId];
        expiredTimerData.callback.call(expiredTimerData.context,
                                       expiredTimerData.userData,
                                       expiredTimerId);

        // If this is a recurrent timer which wasn't stopped by the callback...
        if (expiredTimerData.recurTime && timerData[expiredTimerId])
        {
          // ... then restart it.
          var now = (new Date()).getTime();
          expiredTimerData.expireAt = now + expiredTimerData.recurTime;

          // Insert this timer back on the time-ordered timer queue
          this.__insertNewTimer(expiredTimerData.expireAt, expiredTimerId);
        }
        else
        {
          // If it's not a recurrent timer, we can purge its data too.
          delete timerData[expiredTimerId];
        }
      }

      // If there are no more timers pending...
      if (timerQueue.length == 0 && this.__timerListenerActive)
      {
        // ... then stop listening for the periodic timer
        qx.event.Idle.getInstance().removeListener("interval",
                                                   this.__processQueue,
                                                   this);
        this.__timerListenerActive = false;
      }
    }
  }
});
/* ************************************************************************

 Copyright:
 Tobias Oetiker, OETIKER+PARTNER AG, www.oetiker.ch

 License:
 LGPL: http://www.gnu.org/licenses/lgpl.html
 EPL: http://www.eclipse.org/org/documents/epl-v10.php
 See the LICENSE file in the project's top-level directory for details.

 Authors:
 * Tobias Oetiker (oetiker)

 ************************************************************************ */

/**
 * A Non-Header-Header
 */
qx.Class.define("combotable.NoHeader", {
    extend : qx.ui.table.pane.Header,

    construct : function(paneScroller) {
        this.base(arguments, paneScroller);

        this.__privateScroller = paneScroller;
    },

    members : {
        __privateScroller : null,

        /**
         * Overridden from {@link qx.ui.core.Widget.getContainerLocation} so that it
         * works with the header disabled.
         */
        getContainerLocation : function(mode) {
            var domEl = this.getContainerElement().getDomElement();

            if (domEl) {
                return qx.bom.element.Location.get(domEl, mode);
            }
            else {
                domEl = this.__privateScroller.getContainerElement().getDomElement();

                if (domEl) {
                    var loc = qx.bom.element.Location.get(domEl, mode);
                    loc.bottom = loc.top;
                    return loc;
                }
                else {
                    return {
                        left   : 0,
                        right  : 0,
                        top    : 0,
                        bottom : 0
                    };
                }
            }
        }
    }
});
