/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2009 1&1 Internet AG, Germany, http://www.1and1.org

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Alexander Steitz (aback)
     * Michael Haitz (mhaitz)
     * Jonathan Weiß (jonathan_rass)

   Contributors:
     * Petr Kobalicek (e666e)

************************************************************************ */

/* ************************************************************************

#asset(qx/static/blank.html)

************************************************************************ */

/**
 * Rich text editor widget which encapsulates the low-level {@link qx.bom.htmlarea.HtmlArea}
 * component to offer editing features.
 *
 *
 * Optimized for the use at a RIA.
 */
qx.Class.define("qx.ui.embed.HtmlArea",
{
  extend : qx.ui.core.Widget,

  /*
   * IMPORTANT NOTE
   * If you add functionality which manipulates the content of the HtmlArea
   * AND you want make these changes undo-/redo-able you have to make sure
   * to implement methods in the "Manager" and "UndoManager" classes.
   */

  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * Constructor
   *
   * @param value {String} Initial content
   * @param styleInformation {String | Map | null} Optional style information for the editor's document
   *                                               Can be a string or a map (example: { "p" : "padding:2px" }
   * @param source {String} source of the iframe
   *
   * @lint ignoreDeprecated(_keyCodeToIdentifierMap)
   */
  construct : function(value, styleInformation, source)
  {
    this.__postPonedProperties = {};

    // **********************************************************************
    //   INIT
    // **********************************************************************
    this.base(arguments);

    this._setLayout(new qx.ui.layout.Grow);

    this.__addAppearListener();

    this.__initValues = { content: value,
                          styleInfo: styleInformation,
                          source: source };

    qx.event.Registration.addListener(document.body, "mousedown", this.block, this, true);
    qx.event.Registration.addListener(document.body, "mouseup", this.release, this, true);
    qx.event.Registration.addListener(document.body, "losecapture", this.release, this, true);
  },


 /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events:
  {
    /**
     * Thrown when the editor gets an error at loading time.
     */
    "loadingError"     : "qx.event.type.Data",

    /**
     * Only available if messengerMode is active. This event returns the current content of the editor.
     */
    "messengerContent" : "qx.event.type.Data",

    /**
     * This event holds a data map which informs about the formatting at the
     * current cursor position. It holds the following keys:
     *
     * * bold
     * * italic
     * * underline
     * * strikethrough
     * * fontSize
     * * fontFamily
     * * insertUnorderedList
     * * insertOrderedList
     * * justifyLeft
     * * justifyCenter
     * * justifyRight
     * * justifyFull
     *
     * This map can be used to control/update a toolbar states.
     */
    "cursorContext"    : "qx.event.type.Data",

    /**
     * This event is dispatched when the editor is ready to use
     */
    "ready"            : "qx.event.type.Event",

    /**
     * This event is dispatched when the editor is ready to use after it was
     * re-located and re-initialized. Only implemented for Gecko browsers.
     */
    "readyAfterInvalid" : "qx.event.type.Event",

    /**
     * This event is dispatched when the editor gets the focus and his own handling is done
     */
    "focused"          : "qx.event.type.Event",


    /**
     * This event is dispatched when the document receives an "focusout" event
     */
    "focusOut"         : "qx.event.type.Event",

    /**
     * This event is dispatched when the editor gets a right click.
     *
     * Fires a data event with the following data:
     *
     * * x - absolute x coordinate
     * * y - absolute y coordinate
     * * relX - relative x coordinate
     * * relY - relative y coordinate
     * * target - DOM element target
     */
    "contextmenu"      : "qx.event.type.Data",

    /**
     * Holds information about the state of undo/redo
     * Keys are "undo" and "redo".
     * Possible values are 0 and -1 to stay in sync with
     * the kind the "cursorContext" event works.
     * (1 = active/pressed, 0 = possible/not pressed, -1 = disabled)
     */
    "undoRedoState"    : "qx.event.type.Data"
  },



  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {
    /**
     * Checks if the given node is a block node
     *
     * @type static
     * @param node {Node} Node
     * @return {Boolean} whether it is a block node
     */
    isBlockNode : function(node)
    {
      var deprecatedFunction = qx.ui.embed.HtmlArea.isBlockNode;
      var deprecationMessage = "Please use the method 'qx.dom.Node.isBlockNode' instead.";
      qx.log.Logger.deprecatedMethodWarning(deprecatedFunction, deprecationMessage);

      return qx.dom.Node.isBlockNode(node);
    },


    /**
     * Checks if one element is in the list of elements that are allowed to contain a paragraph in HTML
     *
     * @param node {Node} node to check
     * @return {Boolean}
     */
    isParagraphParent : function(node) {
      return qx.bom.htmlarea.HtmlArea.isParagraphParent(node);
    }
  },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** Selected content type. Currently only XHTML is supported. */
    contentType :
    {
      check : "String",
      init  : "xhtml",
      apply : "_applyContentType"
    },


    /**
     * If turned on the editor acts like a messenger widget e.g. if one hits the Enter key the current content gets
     * outputted (via a DataEvent) and the editor clears his content
     */
    messengerMode :
    {
      check : "Boolean",
      init  : false,
      apply : "_applyMessengerMode"
    },


    /**
     * Toggles whether a p element is inserted on each line break or not.
     * A "normal" linebreak can be achieved using the combination "Shift+Enter" anyway
     */
    insertParagraphOnLinebreak :
    {
      check : "Boolean",
      init  : true,
      apply : "_applyInsertParagraphOnLinebreak"
    },


    /**
     * If true we add a linebreak after control+enter
     */
    insertLinebreakOnCtrlEnter :
    {
      check : "Boolean",
      init  : true,
      apply : "_applyInsertLinebreakOnCtrlEnter"
    },


    /**
     * Function to use in postprocessing html. See getHtml() and __getHtml().
     */
    postProcess:
    {
      check: "Function",
      nullable: true,
      init: null,
      apply : "_applyPostProcess"
    },


    /**
     * Toggles whether to use Undo/Redo
     */
    useUndoRedo :
    {
      check : "Boolean",
      init  : true,
      apply : "_applyUseUndoRedo"
    },

    /**
     * appearance
     */
    appearance :
    {
      refine : true,
      init   : "htmlarea"
    },

    /**
     * Default font family to use when e.g. user removes all content
     */
    defaultFontFamily :
    {
      check : "String",
      init : "Verdana",
      apply : "_applyDefaultFontFamily"
    },

    /**
     * Default font family to use when e.g. user removes all content
     */
    defaultFontSize :
    {
      check : "Integer",
      init : 4,
      apply : "_applyDefaultFontSize"
    },

    /**
     * Focusable
     */
    focusable :
    {
      refine : true,
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
    __editorComponent: null,
    __postPonedProperties: null,
    __blockerElement : null,
    __initValues : null,
    __onDOMNodeRemoved : null,


    /**
     * Initializes the blocker element if (yet) not available
     */
    _initBlockerElement : function ()
    {
      if (!this.__blockerElement) {
        this.__blockerElement = this._createBlockerElement();
      }
    },


    /*
    ---------------------------------------------------------------------------
      MODIFIERS
    ---------------------------------------------------------------------------
    */

    _applyContentType : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setContentType(value);
      } else {
        this.__postPonedProperties["ContentType"] = value;
      }
    },


    _applyMessengerMode : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setMessengerMode(value);
      } else {
        this.__postPonedProperties["MessengerMode"] = value;
      }
    },


    _applyInsertParagraphOnLinebreak : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setInsertParagraphOnLinebreak(value);
      } else {
        this.__postPonedProperties["InsertParagraphOnLinebreak"] = value;
      }
    },


    _applyInsertLinebreakOnCtrlEnter : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setInsertLinebreakOnCtrlEnter(value);
      } else {
        this.__postPonedProperties["InsertLinebreakOnCtrlEnter"] = value;
      }
    },


    _applyPostProcess : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setPostProcess(value);
      } else {
        this.__postPonedProperties["PostProcess"] = value;
      }
    },


    _applyUseUndoRedo : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setUseUndoRedo(value);
      } else {
        this.__postPonedProperties["UseUndoRedo"] = value;
      }
    },


    _applyDefaultFontFamily : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setDefaultFontFamily(value);
      } else {
        this.__postPonedProperties["DefaultFontFamily"] = value;
      }
    },


    _applyDefaultFontSize : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setDefaultFontSize(value);
      } else {
        this.__postPonedProperties["DefaultFontSize"] = value;
      }
    },

    // overridden
    _applyNativeContextMenu : function(value, old)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setNativeContextMenu(value);
      } else {
        this.__postPonedProperties["NativeContextMenu"] = value;
      }
    },


    /**
     * Creates <div> element which is aligned over iframe node to avoid losing mouse events.
     *
     * @return {Object} Blocker element node
     */
    _createBlockerElement : function()
    {
      var el = new qx.html.Element("div");

      el.setStyles({
        zIndex   : 20,
        position : "absolute",
        display  : "none"
      });

      // IE needs some extra love here to convince it to block events.
      if ((qx.core.Environment.get("engine.name") == "mshtml"))
      {
        el.setStyles({
          backgroundImage: "url(" + qx.util.ResourceManager.getInstance().toUri("qx/static/blank.gif") + ")",
          backgroundRepeat: "repeat"
        });
      }

      return el;
    },


    /*
    ---------------------------------------------------------------------------
      SETUP
    ---------------------------------------------------------------------------
    */

    /**
     * Adds the "appear" listener for correct startup
     *
     * @return {void}
     */
    __addAppearListener : function() {
      this.addListenerOnce("appear", this.__setupEditorComponent);
    },


    /**
     * Setup the low-level editor component and the listener delegate methods.
     */
    __setupEditorComponent : function()
    {
      var domElement = this.getContentElement().getDomElement();
      this.__editorComponent = new qx.bom.htmlarea.HtmlArea(domElement,
                                                           this.__initValues.content,
                                                           this.__initValues.styleInfo,
                                                           this.__initValues.source);
      this.__applyPostPonedProperties();
      this.__setupDelegateListeners();

      if ((qx.core.Environment.get("engine.name") == "gecko")) {
        this.__setupInvalidateListener();
      }

      this.addListener("appear", this.forceEditable);
    },


    /**
     * Applies the postponed properties to the editor component
     *
     * @return {void}
     */
    __applyPostPonedProperties : function()
    {
      for(var propertyName in this.__postPonedProperties) {
        this.__editorComponent["set" + propertyName](this.__postPonedProperties[propertyName]);
      }
    },


    /**
     * Setup listeners for events of the low-level editing component and fires
     * them at the editing widget.
     */
    __setupDelegateListeners : function()
    {
      this.__editorComponent.addListener("ready", this.__delegateEvent, this);
      this.__editorComponent.addListener("readyAfterInvalid", this.__delegateEvent, this);
      this.__editorComponent.addListener("focused", this.__delegateEvent, this);
      this.__editorComponent.addListener("focusOut", this.__delegateEvent, this);

      this.__editorComponent.addListener("loadingError", this.__delegateDataEvent, this);
      this.__editorComponent.addListener("cursorContext", this.__delegateDataEvent, this);
      this.__editorComponent.addListener("contextmenu", this.__delegateDataEvent, this);
      this.__editorComponent.addListener("undoRedoState", this.__delegateDataEvent, this);
      this.__editorComponent.addListener("messengerContent", this.__delegateDataEvent, this);
    },


    /**
     * Clones the incoming event and fires it at itself to let the application
     * developers listen to the widget instance.
     *
     * @param e {qx.event.type.Event} event instance
     * @return {void}
     */
    __delegateEvent : function(e)
    {
      var clone = e.clone();
      this.fireEvent(clone.getType());
    },


    /**
     * Clones the incoming data event and fires it at itself to let the application
     * developers listen to the widget instance.
     *
     * @param e {qx.event.type.Data} event instance
     * @return {void}
     */
    __delegateDataEvent : function(e)
    {
      var clone = e.clone();
      this.fireDataEvent(clone.getType(), e.getData());
    },


    /**
     * Listens to DOM changes of the container element to get informed when the
     * HtmlArea is moved to another container.
     *
     * This method is only implemented for Gecko browsers.
     */
    __setupInvalidateListener : function()
    {
      this.__onDOMNodeRemoved = qx.lang.Function.bind(this.__invalidateEditor, this);

      var element = this.getContainerElement().getDomElement();
      qx.bom.Event.addNativeListener(element, "DOMNodeRemoved", this.__onDOMNodeRemoved);
    },


    /**
     * Invalidates the editor component if the connected DOM node is removed.
     *
     * @param e {qx.event.type.Event} event instance
     */
    __invalidateEditor : qx.event.GlobalError.observeMethod(function(e)
    {
      if (this.__editorComponent && !this.__editorComponent.isDisposed()) {
        this.__editorComponent.invalidateEditor();
      }
    }),


    /*
    ---------------------------------------------------------------------------
      PUBLIC API
    ---------------------------------------------------------------------------
    */


    // overridden
    renderLayout : function(left, top, width, height)
    {
      this.base(arguments, left, top, width, height);

      var pixel = "px";
      var insets = this.getInsets();

      if (!this.__blockerElement) {
        this._initBlockerElement();
      }

      this.__blockerElement.setStyles({
        left   : insets.left + pixel,
        top    : insets.top + pixel,
        width  : (width - insets.left - insets.right) + pixel,
        height : (height - insets.top - insets.bottom) + pixel
      });
    },


    /**
     * Returns the iframe object which is used to render the content
     *
     * @return {Iframe?null} iframe DOM element or null if the editor is not initialized
     */
    getIframeObject : function() {
      return this.__editorComponent != null ? this.__editorComponent.getIframeObject() : null;
    },

    /**
     * Getter for command manager.
     *
     * @return {htmlarea.manager.Manager?htmlarea.manager.UndoManager?null} manager instance
     * or null if the editor is not initialized
     */
    getCommandManager : function() {
      return this.__editorComponent != null ? this.__editorComponent.getCommandManager() : null;
    },


    /**
     * Setting the value of the editor if it's initialized
     *
     * @param value {String} new content to set
     * @return {void}
     */
    setValue : function(value)
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.setValue(value);
      } else {
        this.__initValues.content = value;
      }
    },


    /**
     * Getting the value of the editor.
     * <b>Attention</b>: The content of the editor is synced
     * at focus/blur events, but not on every input. This method
     * is not delivering the current content in a stable manner.
     * To get the current value of the editor use the {@link #getComputedValue}
     * method instead.
     *
     * @return {String?null} value of the editor or null if it's not initialized
     */
    getValue : function()
    {
      if (this.__editorComponent != null) {
        return this.__editorComponent.getValue();
      } else {
        return this.__initValues.content;
      }
    },


    /**
     * Getting the computed value of the editor.
     * This method returns the current value of the editor traversing
     * the elements below the body element. With this method you always
     * get the current value, but it is much more expensive. So use it
     * carefully.
     *
     * @param skipHtmlEncoding {Boolean ? false} whether the html encoding of text nodes should be skipped
     * @return {String?null} computed value of the editor or null if the editor is not initialized
     */
    getComputedValue : function(skipHtmlEncoding) {
      return this.__editorComponent != null ? this.__editorComponent.getHtml(skipHtmlEncoding) : null;
    },


    /**
     * Returns the complete content of the editor
     *
     * @return {String?null} complete content or null if the editor is not initialized
     */
    getCompleteHtml : function() {
      return this.__editorComponent != null ? this.__editorComponent.getCompleteHtml() : null;
    },


    /**
     * Returns the document of the iframe
     *
     * @return {Object}
     */
    getContentDocument : function() {
      return this.__editorComponent != null ? this.__editorComponent.getContentDocument() : null;
    },

    /**
     * Returns the body of the document
     *
     * @return {Object}
     */
    getContentBody : function() {
      return this.__editorComponent != null ? this.__editorComponent.getContentBody() : null;
    },


    /**
     * Returns the body of the document
     *
     * @return {Object}
     */
    getContentWindow : function() {
      return this.__editorComponent != null ? this.__editorComponent.getContentWindow() : null;
    },


    /**
     * Returns all the words that are contained in a node.
     *
     * @param node {Object} the node element where the text should be retrieved from.
     * @return {String[]} all the words.
     */
    getWords : function(node) {
      return this.__editorComponent != null ? this.__editorComponent.getWords(node) : null;
    },


    /**
     * *** IN DEVELOPMENT! ***
     * Returns all words
     *
     * @return {Map} all words
     */
    getWordsWithElement : function() {
      return this.__editorComponent != null ? this.__editorComponent.getWordsWithElement() : null;
    },


    /**
     * *** IN DEVELOPMENT! ***
     * Returns all text nodes
     *
     * @return {Array} Text nodes
     */
    getTextNodes : function() {
      return this.__editorComponent != null ? this.__editorComponent.getTextNodes() : null;
    },


    /**
     * Whether the editor is ready to accept commands etc.
     *
     * @return {Boolean} ready or not
     */
    isReady : function() {
      return this.__editorComponent != null ? this.__editorComponent.isReady() : false;
    },


    /**
     * Forces the htmlArea to reset the document editable. This method can
     * be useful (especially for Gecko) whenever the HtmlArea was hidden and
     * gets visible again.
     */
    forceEditable : function() {
      if (this.__editorComponent != null) {
        this.__editorComponent.forceEditable();
      }
    },


    /**
     * Service method to check if the component is already loaded.
     * Overrides the base method.
     *
     * @return {Boolean}
     */
    isLoaded : function() {
      return this.__editorComponent != null ? this.__editorComponent.isLoaded() : false;
    },


    /**
     * Whether the document is in editable mode
     *
     * @param value {Boolean} whether the component should be editable
     * @return {void}
     */
    setEditable : function(value) {
      if (this.__editorComponent != null) {
        this.__editorComponent.setEditable(value);
      }
    },


    /**
     * Whether the document is in editable mode
     *
     * @return {Boolean}
     */
    getEditable : function() {
      return this.__editorComponent != null ? this.__editorComponent.getEditable() : false;
    },


    /**
     * Whether the document is in editable mode
     *
     * @return {Boolean}
     */
    isEditable : function() {
      return this.__editorComponent != null ? this.__editorComponent.isEditable() : false;
    },


    /**
     * Inserts html content on the current selection
     *
     * @param value {String} html content
     * @return {Boolean} Success of operation
     */
    insertHtml : function(value) {
      return this.__editorComponent != null ? this.__editorComponent.insertHtml(value) : false;
    },


    /**
     * Removes all formatting styles on the current selection content and resets
     * the font family and size to the default ones. See {@link #defaultFontSize}
     * and {@link #defaultFontFamily}.
     *
     * @return {Boolean} Success of operation
     */
    removeFormat : function() {
      return this.__editorComponent != null ? this.__editorComponent.removeFormat() : false;
    },


    /**
     * Sets the current selection content to bold font style
     *
     * @return {Boolean} Success of operation
     */
    setBold : function() {
      return this.__editorComponent != null ? this.__editorComponent.setBold() : false;
    },


    /**
     * Sets the current selection content to italic font style
     *
     * @return {Boolean} Success of operation
     */
    setItalic : function() {
      return this.__editorComponent != null ? this.__editorComponent.setItalic() : false;
    },


    /**
     * Sets the current selection content to underline font style
     *
     * @return {Boolean} Success of operation
     */
    setUnderline : function() {
      return this.__editorComponent != null ? this.__editorComponent.setUnderline() : false;
    },


    /**
     * Sets the current selection content to strikethrough font style
     *
     * @return {Boolean} Success of operation
     *
     */
    setStrikeThrough : function() {
      return this.__editorComponent != null ? this.__editorComponent.setStrikeThrough() : false;
    },


    /**
     * Sets the current selection content to the specified font size
     *
     * @param value {Number} Font size
     * @return {Boolean} Success of operation
     */
    setFontSize : function(value) {
      return this.__editorComponent != null ? this.__editorComponent.setFontSize(value) : false;
    },


    /**
     * Sets the current selection content to the specified font family
     *
     * @param value {String} Font family
     * @return {Boolean} Success of operation
     */
    setFontFamily : function(value) {
      return this.__editorComponent != null ? this.__editorComponent.setFontFamily(value) : false;
    },


    /**
     * Sets the current selection content to the specified font color
     *
     * @param value {String} Color value (supported are Hex,
     * @return {Boolean} Success of operation
     */
    setTextColor : function(value) {
      return this.__editorComponent != null ? this.__editorComponent.setTextColor(value) : false;
    },


    /**
     * Sets the current selection content to the specified background color
     *
     * @param value {String} Color value (supported are Hex,
     * @return {Boolean} Success of operation
     */
    setTextBackgroundColor : function(value) {
      return this.__editorComponent != null ? this.__editorComponent.setTextBackgroundColor(value) : false;
    },


    /**
     * Left-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyLeft : function() {
      return this.__editorComponent != null ? this.__editorComponent.setJustifyLeft() : false;
    },


    /**
     * Center-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyCenter : function() {
      return this.__editorComponent != null ? this.__editorComponent.setJustifyCenter() : false;
    },


    /**
     * Right-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyRight : function() {
      return this.__editorComponent != null ? this.__editorComponent.setJustifyRight() : false;
    },


    /**
     * Full-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyFull : function() {
      return this.__editorComponent != null ? this.__editorComponent.setJustifyFull() : false;
    },


    /**
     * Indents the current selection
     *
     * @return {Boolean} Success of operation
     */
    insertIndent : function() {
      return this.__editorComponent != null ? this.__editorComponent.insertIndent() : false;
    },


    /**
     * Outdents the current selection
     *
     * @return {Boolean} Success of operation
     */
    insertOutdent : function() {
      return this.__editorComponent != null ? this.__editorComponent.insertOutdent() : false;
    },


    /**
     * Inserts an ordered list
     *
     * @return {Boolean} Success of operation
     */
    insertOrderedList : function() {
      return this.__editorComponent != null ? this.__editorComponent.insertOrderedList() : false;
    },


    /**
     * Inserts an unordered list
     *
     * @return {Boolean} Success of operation
     */
    insertUnorderedList : function() {
      return this.__editorComponent != null ? this.__editorComponent.insertUnorderedList() : false;
    },


    /**
     * Inserts a horizontal ruler
     *
     * @return {Boolean} Success of operation
     */
    insertHorizontalRuler : function() {
      return this.__editorComponent != null ? this.__editorComponent.insertHorizontalRuler() :false;
    },


    /**
     * Insert an image
     *
     * @param attributes {Map} Map of HTML attributes to apply
     * @return {Boolean} Success of operation
     */
    insertImage : function(attributes) {
      return this.__editorComponent != null ? this.__editorComponent.insertImage(attributes) : false;
    },


    /**
     * Inserts a hyperlink
     *
     * @param url {String} URL for the image to be inserted
     * @return {Boolean} Success of operation
     */
    insertHyperLink : function(url) {
      return this.__editorComponent != null ? this.__editorComponent.insertHyperLink(url) : false;
    },

    /**
     * Alias for setBackgroundColor("transparent");
     *
     * @return {Boolean} if succeeded
     */
    removeBackgroundColor : function() {
      return this.__editorComponent != null ? this.__editorComponent.removeBackgroundColor() : false;
    },


    /**
     * Sets the background color of the editor
     *
     * @param value {String} color
     * @return {Boolean} if succeeded
     */
    setBackgroundColor : function(value) {
      return this.__editorComponent != null ? this.__editorComponent.setBackgroundColor(value) : false;
    },


    /**
     * Alias for setBackgroundImage(null);
     *
     * @return {Boolean} if succeeded
     */
    removeBackgroundImage : function () {
      return this.__editorComponent != null ? this.__editorComponent.removeBackgroundImage() : false;
    },


    /**
     * Inserts an background image
     *
     * @param url {String} url of the background image to set
     * @param repeat {String} repeat mode. Possible values are "repeat|repeat-x|repeat-y|no-repeat".
     *                                     Default value is "no-repeat"
     * @param position {String?Array} Position of the background image.
     *                                Possible values are "|top|bottom|center|left|right|right top|left top|left bottom|right bottom" or
     *                                an array consisting of two values for x and
     *                                y coordinate. Both values have to define the
     *                                unit e.g. "px" or "%".
     *                                Default value is "top"
     * @return {Boolean} Success of operation
     */
    setBackgroundImage : function(url, repeat, position) {
      return this.__editorComponent != null ? this.__editorComponent.setBackgroundImage(url, repeat, position) : false;
    },


    /**
     * Selects the whole content
     *
     * @return {Boolean} Success of operation
     */
    selectAll : function() {
      return this.__editorComponent != null ? this.__editorComponent.selectAll() : false;
    },


    /**
     * Undo last operation
     *
     * @return {void}
     */
    undo : function() {
      return this.__editorComponent != null ? this.__editorComponent.undo() : false;
    },


    /**
     * Redo last undo
     *
     * @return {void}
     */
    redo : function() {
      return this.__editorComponent != null ? this.__editorComponent.redo() : false;
    },


    /*
    ---------------------------------------------------------------------------
      CONTENT MANIPLUATION
    ---------------------------------------------------------------------------
    */

    /**
     * Resets the content of the editor
     *
     * @return {void}
     */
    resetHtml : function()
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.resetHtml();
      }
    },


    /**
     * Get html content (call own recursive method)
     *
     * @param skipHtmlEncoding {Boolean ? false} whether the html encoding of text nodes should be skipped
     * @return {String?null} current content of the editor as XHTML or null if not initialized
     */
    getHtml : function(skipHtmlEncoding) {
      return this.__editorComponent != null ? this.__editorComponent.getHtml(skipHtmlEncoding) : null;
    },

    /**
     * Helper function to examine if HTMLArea is empty, except for
     * place holder(s) needed by some browsers.
     *
     * @return {Boolean} True, if area is empty - otherwise false.
     */
    containsOnlyPlaceholder : function() {
      return this.__editorComponent != null ? this.__editorComponent.containsOnlyPlaceHolder() : false;
    },


    /*
      -----------------------------------------------------------------------------
      PROCESS CURSOR CONTEXT
      -----------------------------------------------------------------------------
    */


    /**
     * Returns the information about the current context (focusNode). It's a
     * map with information about "bold", "italic", "underline", etc.
     *
     * @return {Map?null} formatting information about the focusNode or null if not initialized
     */
    getContextInformation : function() {
      return this.__editorComponent != null ? this.__editorComponent.getContextInformation() : null;
    },


    /*
     -----------------------------------------------------------------------------
     SELECTION
     -----------------------------------------------------------------------------
    */

    /**
     * Returns the current selection object
     *
     * @return {Selection?null} Selection object or null if not initialized.
    */
    getSelection : function() {
      return this.__editorComponent != null ? this.__editorComponent.getSelection() : null;
    },


    /**
     * Returns the currently selected text.
     *
     * @return {String?null} Selected plain text or null if not initialized.
     */
    getSelectedText : function() {
      return this.__editorComponent != null ? this.__editorComponent.getSelectedText() : null;
    },


    /**
     * Returns the content of the actual range as text
     *
     * @TODO: need to be implemented correctly
     * @return {String?null} selected text or null if not initialized
     */
    getSelectedHtml : function() {
      return this.__editorComponent != null ? this.__editorComponent.getSelectedHtml() : null;
    },


    /**
     * Clears the current selection
     *
     * @return {void}
     */
    clearSelection : function()
    {
      if (this.__editorComponent != null) {
        this.__editorComponent.clearSelection();
      }
    },


    /*
     -----------------------------------------------------------------------------
     TEXT RANGE
     -----------------------------------------------------------------------------
    */

    /**
     * Returns the range of the current selection
     *
     * @return {Range?null} Range object or null if not initialized
     */
    getRange : function() {
      return this.__editorComponent.getRange();
    },


    /**
     * Safes the current range to let the next command operate on this range.
     * Currently only interesting for IE browsers, since they loose the range /
     * selection whenever an element is clicked which need to have a focus (e.g.
     * a textfield widget).
     *
     * *NOTE:* the next executed command will reset this range.
     *
     * @return {void}
     */
    saveRange : function() {
      this.__editorComponent.saveRange();
    },


    /**
     * Returns the current stored range.
     *
     * @return {Range|null} range object or null
     */
    getSavedRange : function() {
      return this.__editorComponent.getSavedRange();
    },


    /**
     * Resets the current saved range.
     *
     * @return {void}
     */
    resetSavedRange : function() {
      this.__editorComponent.resetSavedRange();
    },


    /*
      -----------------------------------------------------------------------------
      NODES
      -----------------------------------------------------------------------------
    */

    /**
     *  Returns the node where the selection ends
     *
     *  @return {Node?null} focus node or null if not initialized
     */
    getFocusNode : function() {
      return this.__editorComponent != null ? this.__editorComponent.getFocusNode() : null;
    },

    /**
     * Cover the iframe with a transparent blocker div element. This prevents
     * mouse or key events to be handled by the iframe. To release the blocker
     * use {@link #release}.
     *
     */
    block : function()
    {
      if (!this.__blockerElement) {
        this._initBlockerElement();
      }

      if (!this.getContainerElement().hasChild(this.__blockerElement)) {
        this.getContainerElement().add(this.__blockerElement);
      }

      this.__blockerElement.setStyle("display", "block");
    },


    /**
     * Release the blocker set by {@link #block}.
     *
     */
    release : function()
    {
      if (this.__blockerElement) {
        this.__blockerElement.setStyle("display", "none");
      }
    },


    /*
    -----------------------------------------------------------------------------
      FOCUS MANAGEMENT
    -----------------------------------------------------------------------------
    */

    // overridden
    focus : function()
    {
      this.base(arguments);

      this.__focusContent();
    },

    // overridden
    tabFocus : function()
    {
      this.base(arguments);

      this.__focusContent();
    },


    /**
     * Focus the document content
     */
    __focusContent : function()
    {
      if (this.__editorComponent != null) {
        qx.event.Timer.once(function() {
          this.__editorComponent.focusContent();
        }, this, 0);
      }
    }
  },




  /*
  ---------------------------------------------------------------------------
    DESTRUCTOR
  ---------------------------------------------------------------------------
  */

  /**
   * Destructor
   *
   * @return {void}
   */
  destruct : function()
  {
    this.__postPonedProperties = this.__initValues = null;

    qx.event.Registration.removeListener(document.body, "mousedown", this.block, this, true);
    qx.event.Registration.removeListener(document.body, "mouseup", this.release, this, true);
    qx.event.Registration.removeListener(document.body, "losecapture", this.release, this, true);

    var element = this.getContainerElement().getDomElement();
    if (element) {
      qx.bom.Event.removeNativeListener(element, "DOMNodeRemoved", this.__onDOMNodeRemoved);
    }

    this._disposeObjects("__blockerElement", "__editorComponent");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2009 1&1 Internet AG, Germany, http://www.1and1.org

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Alexander Steitz (aback)
     * Michael Haitz (mhaitz)
     * Jonathan Weiß (jonathan_rass)

   Contributors:
     * Petr Kobalicek (e666e)

************************************************************************ */

/* ************************************************************************

#asset(qx/static/blank.html)

************************************************************************ */

/**
 * Low-level Rich text editor which can be used by connecting it to an
 * existing DOM element (DIV node).
 * This component does not contain any {@link qx.ui} code resulting in a
 * smaller footprint.
 *
 *
 * Optimized for the use at a traditional webpage.
 */
qx.Class.define("qx.bom.htmlarea.HtmlArea",
{
  extend : qx.core.Object,

  /*
   * IMPORTANT NOTE
   * If you add functionality which manipulates the content of the HtmlArea
   * AND you want make these changes undo-/redo-able you have to make sure
   * to implement methods in the "Manager" and "UndoManager" classes.
   */

  /*
  *****************************************************************************
     CONSTRUCTOR
  *****************************************************************************
  */

  /**
   * Constructor
   *
   * @param element {Element} DOM element to connect the component to
   * @param value {String} Initial content
   * @param styleInformation {String | Map | null} Optional style information for the editor's document
   *                                               Can be a string or a map (example: { "p" : "padding:2px" }
   * @param source {String} source of the iframe
   *
   * @lint ignoreDeprecated(_keyCodeToIdentifierMap)
   */
  construct : function(element, value, styleInformation, source)
  {
    this.base(arguments);

    var uri = source || qx.util.ResourceManager.getInstance().toUri("qx/static/blank.html");

    this.__connectToDomElement(element);
    this.__initDocumentSkeletonParts();
    this._createAndAddIframe(uri);

    // catch load event
    this._addIframeLoadListener();

    // set the optional style information - if available
    this.__styleInformation = qx.bom.htmlarea.HtmlArea.__formatStyleInformation(styleInformation);

    // Check content
    if (qx.lang.Type.isString(value)) {
      this.__value = value;
    }

    /*
     * "Fix" Keycode to identifier mapping in opera to suit the needs
     * of the editor component
     */
    if ((qx.core.Environment.get("engine.name") == "opera"))
    {
      /*
       * To correct the broken key handling in Opera "fix" the meanings of
       * the several keyCodes manually to the desired Identifiers
       *
       * However for these keys it is bit more complicated:
       *
       * KEY   RESULT   POSSIBLE SOLUTION
       * ************************************
       * $     "Home"   SHIFT + "Home"  -> $
       * (     "Down"   SHIFT + "Down"  -> (
       * '     "Right"  SHIFT + "Right" -> '
       *
       */
      var contentWindow = qx.dom.Node.getWindow(element);
      var keyEventHandler = qx.event.Registration.getManager(contentWindow).getHandler(qx.event.handler.Keyboard);

      /*
       * fix mapping for the keys "#", "-", "P", "S", "X"
       * for other keys there maybe also a problem with the wrong identifier,
       * but here are only these keys fixed which are needed for the smiley handling
       */
      keyEventHandler._keyCodeToIdentifierMap[35]  = "#";
      keyEventHandler._keyCodeToIdentifierMap[45]  = "-";
      keyEventHandler._keyCodeToIdentifierMap[112] = "P";
      keyEventHandler._keyCodeToIdentifierMap[115] = "S";
      keyEventHandler._keyCodeToIdentifierMap[120] = "X";
    }

    /*
     * Build up this commandManager object to stack all commands
     * which are arriving before the "real" commandManager is initialised.
     * Once initialised the stacked commands will be executed.
     */
    this.__commandManager = this.__createStackCommandManager();
  },


 /*
  *****************************************************************************
     EVENTS
  *****************************************************************************
  */

  events:
  {
    /**
     * Thrown when the editor is loaded.
     */
    "load"             : "qx.event.type.Event",

    /**
     * Thrown when the editor gets an error at loading time.
     */
    "loadingError"     : "qx.event.type.Data",

    /**
     * Only available if messengerMode is active. This event returns the current content of the editor.
     */
    "messengerContent" : "qx.event.type.Data",

    /**
     * This event holds a data map which informs about the formatting at the
     * current cursor position. It holds the following keys:
     *
     * * bold
     * * italic
     * * underline
     * * strikethrough
     * * fontSize
     * * fontFamily
     * * insertUnorderedList
     * * insertOrderedList
     * * justifyLeft
     * * justifyCenter
     * * justifyRight
     * * justifyFull
     *
     * This map can be used to control/update a toolbar states.
     */
    "cursorContext"    : "qx.event.type.Data",

    /**
     * This event is dispatched when the editor is ready to use
     */
    "ready"            : "qx.event.type.Event",

    /**
     * This event is dispatched when the editor is ready to use after it was
     * re-located and re-initialized. Only implemented for Gecko browsers.
     */
    "readyAfterInvalid" : "qx.event.type.Event",

    /**
     * This event is dispatched when the editor gets the focus and his own handling is done
     */
    "focused"          : "qx.event.type.Event",


    /**
     * This event is dispatched when the document receives an "focusout" event
     */
    "focusOut"         : "qx.event.type.Event",

    /**
     * This event is dispatched when the editor gets a right click.
     *
     * Fires a data event with the following data:
     *
     * * x - absolute x coordinate
     * * y - absolute y coordinate
     * * relX - relative x coordinate
     * * relY - relative y coordinate
     * * target - DOM element target
     */
    "contextmenu"      : "qx.event.type.Data",

    /**
     * Holds information about the state of undo/redo
     * Keys are "undo" and "redo".
     * Possible values are 0 and -1 to stay in sync with
     * the kind the "cursorContext" event works.
     * (1 = active/pressed, 0 = possible/not pressed, -1 = disabled)
     */
    "undoRedoState"    : "qx.event.type.Data"
  },



  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {
    // Inserted when the property "insertParagraphOnLinebreak" is false
    simpleLinebreak : "<br>",

    EMPTY_DIV : "<div></div>",

    // regex to extract text content from innerHTML
    GetWordsRegExp     : /([^\u0000-\u0040\u005b-\u005f\u007b-\u007f]|['])+/g,
    CleanupWordsRegExp : /[\u0000-\u0040]/gi,

    /** Map with infos about hotkey methods */
    hotkeyInfo :
    {
      bold : { method: "setBold" },
      italic : { method: "setItalic" },
      underline : { method: "setUnderline" },
      undo : { method: "undo" },
      redo : { method: "redo" }
    },

    /**
     * Formats the style information. If the styleInformation was passed
     * in as a map it gets converted to a string.
     *
     * @type static
     * @param styleInformation {Map} CSS styles which should be applied at startup of the editor
     * @return {String}
     */
    __formatStyleInformation : function (styleInformation)
    {
      if (styleInformation == null || styleInformation == "")
      {
        return "";
      }
      else if (typeof styleInformation == "object")
      {
        var str = "";
        for (var i in styleInformation)
        {
          str += i + " { " + styleInformation[i] + " }";
        }
        return str;
      }
      else
      {
        return styleInformation;
      }
    },

    /**
     * Parse style string to map.
     *
     * Example:
     * qx.bom.htmlarea.HtmlArea.__parseStyle("text-align: left; text-weight: bold;");
     *
     * @type static
     * @param str {String} String that contain valid style informations separated by ";"
     * @return {Map} Map of style names and it's values
     */
    __parseStyle: function(str)
    {
      var map = {};
      var a = str.split(";");
      var i;

      for (i = 0; i < a.length; i++)
      {
        var style = a[i], sep = style.indexOf(":");

        if (sep === -1) {
          continue;
        }

        var name =  qx.lang.String.trim(style.substring(0, sep));
        var value = qx.lang.String.trim(style.substring(sep+1, style.length));

        if (name && value) {
          map[name] = value;
        }

      }

      return map;
    },

    /**
     * Get html content (own recursive method)
     *
     * @type static
     * @param root {Node} Root node (starting point)
     * @param outputRoot {Boolean} Controls whether the root node is also added to the output
     * @param skipHtmlEncoding {Boolean ? false} whether the html encoding of text nodes should be skipped
     * @param postProcess {function} optional function to call which is executed with every element processing
     * @return {String} Content of current node
     */
    __getHtml : function(root, outputRoot, skipHtmlEncoding, postProcess)
    {
      // String builder is array for large text content
      var html = [];

      switch(root.nodeType)
      {
        // This is main area for returning html from iframe content. Content
        // from editor can be sometimes ugly, so it's needed to do some
        // postProcess to make it beautiful.
        //
        // The initial version of this function used direct HTML rendering
        // (each tag was rendered). This sometimes caused to render empty
        // elements. I'm introducing here new method - store tag name and
        // attributes and use some logic to render them (or not).

        // Node.ELEMENT_NODE
        case 1:
        // Node.DOCUMENT_FRAGMENT_NODE
        case 11:

          // for() helper variable
          var i;
          // Tag in lowercase
          var tag = root.tagName.toLowerCase();
          // Attributes map
          var attributes = {};
          // Styles map (order is not important here)
          var styles = {};
          // It's self-closing tag ? (<br />, <img />, ...)
          var closed = (!(root.hasChildNodes() || qx.bom.htmlarea.HtmlArea.__needsClosingTag(root)));

          if (outputRoot)
          {
            // --------------------------------------------------------------
            // get all of the children nodes of the div placeholder
            // but DO NOT return the placeholder div elements itself.
            // This special case is only relevant for IE browsers
            // --------------------------------------------------------------

            if ((qx.core.Environment.get("engine.name") == "mshtml"))
            {
              if (tag == "div" && root.className && root.className == "placeholder")
              {
                for (i=root.firstChild; i; i=i.nextSibling)
                {
                  html.push(qx.bom.htmlarea.HtmlArea.__getHtml(i, true, skipHtmlEncoding, postProcess));
                }
                return html.join("");
              }
            }

            // --------------------------------------------------------------
            // Parse attributes
            // --------------------------------------------------------------

            // Attributes list
            var attrs = root.attributes;
            var len = attrs.length;
            // Current attribute
            var a;

            if ((qx.core.Environment.get("engine.name") == "gecko"))
            {
              // we can leave out all auto-generated empty span elements which are marked dirty
              if (tag == "span" && len == 1 && attrs[0].name == "_moz_dirty" && root.childNodes.length == 0) {
                return "";
              }
            }

            for (i = 0; i < len; i++)
            {
              a = attrs[i];

              // TODO: Document this, I don't know what "specified" means
              if (!a.specified) {
                continue;
              }

              // Attribute name and value pair
              var name = qx.dom.Node.getName(a);
              var value = a.nodeValue;

              // Mozilla reports some special tags here; we don't need them.
              if (/(_moz|contenteditable)/.test(name))
              {
                continue;
              }

              if (name != "style")
              {
                if (qx.core.Environment.get("engine.name") == "mshtml")
                {
                  if (name == "id" && root.getAttribute("old_id"))
                  {
                    value = root.getAttribute("old_id");
                  }
                  else if (!isNaN(value))
                  {
                    // IE5: buggy on number values
                    // XXX: IE: String, Object, Number, Boolean, ... !!!
                    // XXX: Moz: String only
                    value = root.getAttribute(name);
                  }
                  else
                  {
                    value = a.nodeValue;
                  }
                }
                else
                {
                  value = a.nodeValue;
                }
              }
              else
              {
                // IE fails to put style in attributes list
                // FIXME: cssText reported by IE is UPPERCASE
                value = root.style.cssText;
              }

              if (/(_moz|^$)/.test(value))
              {
                // Mozilla reports some special tags
                // here; we don't need them.
                continue;
              }

              // Ignore old id
              if (name == "old_id") {
                continue;
              }

              // Ignore attributes with no values
              if (!value) {
                continue;
              }

              // ignore focus marker
              if (name == "id" && value == "__elementToFocus__") {
                continue;
              }

              // Ignore qooxdoo attributes (for example $$hash)
              if (name.charAt(0) === "$") {
                continue;
              }

              // Interesting attrubutes are added to attributes array
              attributes[name] = value;
            }

            // --------------------------------------------------------------
            // Parse styles
            // --------------------------------------------------------------

            if (attributes.style !== undefined)
            {
              styles = qx.bom.htmlarea.HtmlArea.__parseStyle(attributes.style);
              delete attributes.style;
            }

            // --------------------------------------------------------------
            // PostProcess
            // --------------------------------------------------------------

            // Call optional postProcess function to modify tag, attributes
            // or styles in this element.
            if (postProcess)
            {
              // create postprocess-info:
              // - info.domElement - current dom element
              // - info.tag - tag name
              // - info.attributes - attributes map (stored name and value pairs)
              // - info.styles - styles map (stored name and value pairs)
              var info = {
                domElement: root,
                tag: tag,
                attributes: attributes,
                styles: styles
              };

              // call user defined postprocessing function
              postProcess(info);

              // remove reference to dom element (is it needed ? For IE ?)
              info.domElement = null;
              // and get tag back
              tag = info.tag;
            }

            // --------------------------------------------------------------
            // Generate Html
            // --------------------------------------------------------------

            // If tag is empty, we don't want it!
            if (tag)
            {
              // Render begin of tag -> <TAG
              html.push("<", tag);

              // Render attributes -> attr=""
              for (var name in attributes)
              {
                var value = attributes[name];
                html.push(" ", name, '="', value.toString().replace(new RegExp('"', "g"), "'"), '"');
              }

              // Render styles -> style=""
              if (!qx.lang.Object.isEmpty(styles))
              {
                html.push(' style="');
                for (var name in styles)
                {
                  var value = styles[name];
                  html.push(name, ":", value.toString().replace(new RegExp('"', "g"), "'"), ";");
                }
                html.push('"');
              }

              // Render end of tag -> > or />
              html.push(closed ? " />" : ">");
            }
          }

          // Child nodes, recursive call itself

          for (i = root.firstChild; i; i = i.nextSibling)
          {
            html.push(qx.bom.htmlarea.HtmlArea.__getHtml(i, true, skipHtmlEncoding, postProcess));
          }

          // Close

          if (outputRoot && !closed && tag)
          {
            html.push("</", tag, ">");
          }
          break;

        // Node.TEXT_NODE
        case 3:

          html.push(skipHtmlEncoding ? root.data : qx.bom.htmlarea.HtmlArea.__htmlEncode(root.data));
          break;

        // Node.COMMENT_NODE
        case 8:
          // skip comments, for now ?
          html.push("<!--", root.data, "-->");
          break;
      }

      return html.join("");
    },

    // TODO: Map should be better! (Petr)
    /**
     * String containing all tags which need a corresponding closing tag
     */
    closingTags : " SCRIPT STYLE DIV SPAN TR TD TBODY TABLE EM STRONG FONT A P B I U STRIKE H1 H2 H3 H4 H5 H6 ",

    // TODO: No reason that first parameter is element, it should be only string with tag name (Petr)
    /**
     * Checks if given element needs a closing tag
     *
     * @type static
     * @param el {Element} Element to check
     * @return {Boolean} Closing tag needed or not
     */
    __needsClosingTag : function(el) {
      return (qx.bom.htmlarea.HtmlArea.closingTags.indexOf(" " + el.tagName + " ") != -1);
    },


    /**
     * Encodes given string with HTML-Entities
     *
     * @type static
     * @param s {String} String to encode
     * @return {String} Encoded string
     */
    __htmlEncode : function(s)
    {
      s = s.replace(/&/ig, "&amp;");
      s = s.replace(/</ig, "&lt;");
      s = s.replace(/>/ig, "&gt;");
      s = s.replace(/\x22/ig, "&quot;");

      // \x22 means '"' -- prevent errors in editor or compressors
      s = s.replace(/\xA9/ig, "&copy;");

      return s;
    },


    /**
     * Checks if the given node is a block node
     *
     * @type static
     * @param node {Node} Node
     * @return {Boolean} whether it is a block node
     */
    isBlockNode : function(node)
    {
      var deprecatedFunction = qx.bom.htmlarea.HtmlArea.isBlockNode;
      var deprecationMessage = "Please use the method 'qx.dom.Node.isBlockNode' instead.";
      qx.log.Logger.deprecatedMethodWarning(deprecatedFunction, deprecationMessage);

      return qx.dom.Node.isBlockNode(node);
    },


    /**
     * Checks if one element is in the list of elements that are allowed to contain a paragraph in HTML
     *
     * @param node {Node} node to check
     * @return {Boolean}
     */
    isParagraphParent : function(node)
    {
      if (!qx.dom.Node.isElement(node))
      {
        return false;
      }

      node = qx.dom.Node.getName(node);

      return /^(body|td|th|caption|fieldset|div)$/.test(node);
    },


    /**
     * Checks of the given node is headline node.
     *
     * @param node {Node} Node to check
     * @return {Boolean} whether it is a headline node
     */
    isHeadlineNode : function(node)
    {
      if (!qx.dom.Node.isElement(node)) {
        return false;
      }

      var nodeName = qx.dom.Node.getName(node);

      return /^h[1-6]$/.test(nodeName);
    }
 },




  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
    /** Selected content type. Currently only XHTML is supported. */
    contentType :
    {
      check : "String",
      init  : "xhtml"
    },


    /**
     * If turned on the editor acts like a messenger widget e.g. if one hits the Enter key the current content gets
     * outputted (via a DataEvent) and the editor clears his content
     */
    messengerMode :
    {
      check : "Boolean",
      init  : false
    },


    /**
     * Toggles whether a p element is inserted on each line break or not.
     * A "normal" linebreak can be achieved using the combination "Shift+Enter" anyway
     */
    insertParagraphOnLinebreak :
    {
      check : "Boolean",
      init  : true
    },


    /**
     * If true we add a linebreak after control+enter
     */
    insertLinebreakOnCtrlEnter :
    {
      check : "Boolean",
      init  : true
    },


    /**
     * Function to use in postProcessing html. See getHtml() and __getHtml().
     */
    postProcess:
    {
      check: "Function",
      nullable: true,
      init: null
    },


    /**
     * Toggles whether to use Undo/Redo
     */
    useUndoRedo :
    {
      check : "Boolean",
      init  : true
    },


    /**
     * Whether to use the native contextmenu or to block it and use own event
     */
    nativeContextMenu :
    {
      check : "Boolean",
      init : false
    },


    /**
     * Default font family to use when e.g. user removes all content
     */
    defaultFontFamily :
    {
      check : "String",
      init : "Verdana"
    },


    /**
     * Default font size to use when e.g. user removes all content
     */
    defaultFontSize :
    {
      check : "Integer",
      init : 4
    }
  },




  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __widget : null,
    __isReady : false,
    __isInvalid : false,
    __isLoaded : false,
    __isEditable : false,
    __isFirstLineSelected : false,
    __commandManager : null,
    __stackCommandManager : null,
    __currentEvent : null,
    __storedSelectedHtml : null,
    __iframe : null,
    __styleInformation : null,
    __documentSkeletonParts : null,
    __savedRange : null,
    __fireCursorContextOnNextInput : false,
    __mouseUpOnBody : false,


    /**
     * Create a "DIV" element which can be added to the document.
     * This element is the container for the editable iframe element.
     *
     * @param element {Element} DOM element to connect to
     * @return {void}
     */
    __connectToDomElement : function(element)
    {
      if (qx.dom.Node.isElement(element) &&
          qx.dom.Node.isNodeName(element, "div"))
      {
        this.__widget = element;
      }
    },


    /**
     * Creates an iframe element with the given URI and adds it to
     * the container element.
     *
     * @param uri {String} URI of the iframe
     */
    _createAndAddIframe : function(uri)
    {
      this.__iframe = qx.bom.Iframe.create();
      qx.bom.Iframe.setSource(this.__iframe, uri);

      // Omit native dotted outline border
      if ((qx.core.Environment.get("engine.name") == "mshtml")) {
        qx.bom.element.Attribute.set(this.__iframe, "hideFocus", "true");
      } else {
        qx.bom.element.Style.set(this.__iframe, "outline", "none");
      }
      qx.bom.element.Style.setStyles(this.__iframe, { width: "100%",
                                                      height: "100%" });

      qx.dom.Element.insertBegin(this.__iframe, this.__widget);
    },


    /**
     * Returns the document of the iframe.
     *
     * @return {Document}
     */
    _getIframeDocument : function() {
      return qx.bom.Iframe.getDocument(this.__iframe);
    },


    /**
     * Returns the window of the iframe.
     *
     * @return {Window}
     */
    _getIframeWindow : function() {
      return qx.bom.Iframe.getWindow(this.__iframe);
    },


    /**
     * Adds the "load" listener to the iframe.
     *
     * @return {void}
     */
    _addIframeLoadListener : function() {
      qx.event.Registration.addListener(this.__iframe, "load", this._loaded, this);
    },

    /**
     * Initial content which is written dynamically into the iframe's document
     *
     * @return {void}
     */
    __initDocumentSkeletonParts : function()
    {
      this.__documentSkeletonParts =
      {
        "xhtml" :
        {
          doctype : '<!' + 'DOCTYPE html PUBLIC "-/' + '/W3C/' + '/DTD XHTML 1.0 Transitional/' + '/EN" "http:/' + '/www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
          html : '<html xmlns="http:/' + '/www.w3.org/1999/xhtml" xml:lang="en" lang="en">',
          meta: '<title></title><meta http-equiv="Content-type" content="text/html; charset=UTF-8" />',
          style : qx.core.Environment.select("engine.name",
          {
            "mshtml" : 'html { margin:0px; padding:0px; } ' +
                       'body { font-size: 100.01%; font-family:Verdana, Geneva, Arial, Helvetica, sans-serif; width:100%; height:100%; background-color:transparent; overflow:auto; background-image:none; margin:0px; padding:5px; } ',

            "default" : 'html { width:100%; height:100%; margin:0px; padding:0px; overflow-y:auto; overflow-x:auto; } ' +
                        'body { font-size:100.01%; font-family:Verdana, Geneva, Arial, Helvetica, sans-serif; background-color:transparent; overflow:visible; background-image:none; margin:0px; padding:5px; } '
          }),
          contentStyle : 'p { margin:0px; padding:0px; }',
          body : '<body>',
          footer : '</body></html>'
        }
      };
    },


    /** private field which holds the content of the editor  */
    __value        : "",


    /**
     * Returns the iframe object which is used to render the content
     *
     * @return {Iframe} iframe DOM element
     */
    getIframeObject : function() {
      return this.__iframe;
    },

    /**
     * Getter for command manager.
     *
     * @return {qx.bom.htmlarea.manager.Command?qx.bom.htmlarea.manager.UndoRedo} manager instance
     */
    getCommandManager : function() {
      return this.__commandManager;
    },


    /**
     * Setting the value of the editor
     *
     * @param value {String} new content to set
     * @return {void}
     */
    setValue : function(value)
    {
      if (qx.lang.Type.isString(value))
      {
        this.__value = value;

        var doc = this._getIframeDocument();
        if (doc && doc.body) {
          doc.body.innerHTML = this.__generateDefaultContent(value);
        }
      }
    },


    /**
     * Generates the default content and inserts the given string
     *
     * @param value {String} string to insert into the default content
     */
    __generateDefaultContent : function(value)
    {
      // bogus node for Firefox 2.x
      var bogusNode = "";
      if ((qx.core.Environment.get("engine.name") == "gecko"))
      {
        if (qx.core.Environment.get("browser.version") <= 2) {
          bogusNode += '<br _moz_editor_bogus_node="TRUE" _moz_dirty=""/>';
        }
      }

      var zeroWidthNoBreakSpace = value.length == 0 ? "\ufeff" : "";
      var idForFontElement =
        qx.core.Environment.get("engine.name") == "gecko" ||
        qx.core.Environment.get("engine.name") == "webkit" ?
        'id="__elementToFocus__"' : '';

      var defaultContent = '<p>' +
                           '<span style="font-family:' +
                            this.getDefaultFontFamily() + '">' +
                           '<font ' + idForFontElement + ' size="' +
                           this.getDefaultFontSize() +'">' +
                           bogusNode +
                           value +
                           zeroWidthNoBreakSpace +
                           '</font>' +
                           '</span>' +
                           '</p>';

      return defaultContent;
    },


    /**
     * Getting the value of the editor.
     * <b>Attention</b>: The content of the editor is synced
     * at focus/blur events, but not on every input. This method
     * is not delivering the current content in a stable manner.
     * To get the current value of the editor use the {@link #getComputedValue}
     * method instead.
     *
     * @return {String} value of the editor
     */
    getValue : function() {
      return this.__value;
    },


    /**
     * Getting the computed value of the editor.
     * This method returns the current value of the editor traversing
     * the elements below the body element. With this method you always
     * get the current value, but it is much more expensive. So use it
     * carefully.
     *
     * @param skipHtmlEncoding {Boolean ? false} whether the html encoding of text nodes should be skipped
     * @return {String} computed value of the editor
     */
    getComputedValue : function(skipHtmlEncoding) {
      return this.getHtml(skipHtmlEncoding);
    },


    /**
     * Returns the complete content of the editor
     *
     * @return {String}
     */
    getCompleteHtml : function()
    {
      var skeletonParts = this.__documentSkeletonParts[this.getContentType()];

      var completeHtml = skeletonParts.html + '<head>' + skeletonParts.meta +
                         '<style type="text/css">' + skeletonParts.contentStyle + '</style>' +
                         '</head>';

      // use "'" to prevent problems with certain font names encapsulated with '"'
      completeHtml += "<body style='" + this.__getBodyStyleToExport() + "'>";
      completeHtml += this.getHtml() + '</body></html>';

      return completeHtml;
    },


    /**
     * Returns the CSS styles which should be exported as a CSS string.
     * This prevents that styles which are only for internal use appear in the
     * result (e.g. overflow settings).
     *
     * @return {String} CSS string of body styles to export
     */
    __getBodyStyleToExport : function()
    {
      var stylesToExport = [ "backgroundColor", "backgroundImage",
                             "backgroundRepeat", "backgroundPosition",
                             "fontFamily", "fontSize",
                             "marginTop", "marginBottom", "marginLeft", "marginRight",
                             "paddingTop", "paddingBottom", "paddingLeft", "paddingRight" ];

      var Style = qx.bom.element.Style;
      var body = this.getContentBody();
      var bodyStyle = {};
      var styleAttribute, styleValue;
      var modeToUse = qx.core.Environment.get("engine.name") == "mshtml" ? 2 : 1;
      for (var i=0, j=stylesToExport.length; i<j; i++)
      {
        styleAttribute = stylesToExport[i];
        styleValue = Style.get(body, styleAttribute, modeToUse);
        if (styleValue !== undefined && styleValue != "") {
          bodyStyle[styleAttribute] = styleValue;
        }
      }

      return qx.bom.element.Style.compile(bodyStyle);
    },


    /**
     * Returns the document of the iframe
     *
     * @return {Object}
     */
    getContentDocument : function ()
    {
      if (this.__isReady) {
        return this._getIframeDocument();
      }
    },

    /**
     * Returns the body of the document
     *
     * @return {Object}
     */
    getContentBody : function ()
    {
      if (this.__isReady) {
        return this._getIframeDocument().body;
      }
    },


    /**
     * Returns the window of the iframe
     *
     * @return {Node} window node
     */
    getContentWindow : function()
    {
      if (this.__isReady) {
         return this._getIframeWindow();
      }
    },


    /**
     * Returns all the words that are contained in a node.
     *
     * @type member
     * @param node {Object} the node element where the text should be retrieved from.
     * @return {String[]} all the words.
     */
    getWords : function(node)
    {
      if (!node) {
        node = this.getContentBody();
      }

      if (!node) {
        return [];
      }

      // Clone the node
      var nodeClone = node.cloneNode(true);
      var innerHTML = nodeClone.innerHTML;

      // Replace all ">" with space "> " to create new word borders
      innerHTML = innerHTML.replace(/>/gi, "> ");
      // Remove all line breaks
      innerHTML = innerHTML.replace(/\n/gi, " ");
      // Remove all comments
      innerHTML = innerHTML.replace(/<!--.*-->/gi, "");

      nodeClone.innerHTML = innerHTML;
      var text  =
        qx.core.Environment.get("engine.name") == "mshtml" ||
        qx.core.Environment.get("engine.name") == "opera" ?
        nodeClone.innerText : nodeClone.textContent;
      var words = text.match(qx.bom.htmlarea.HtmlArea.GetWordsRegExp);

      return !words ? [] : words;
    },


    /**
     * *** IN DEVELOPMENT! ***
     * Returns all words
     *
     * @return {Map} all words
     */
    getWordsWithElement : function()
    {
      var list = this.getTextNodes();
      var result = {};
      var i, j, words, element, word;

      for(var i=0,len1=list.length; i<len1; ++i)
      {
        element = list[i];
        words = element.nodeValue.split(" ");

        for(var j=0,len2=words.length; j<len2; ++j)
        {
          word = this._cleanupWord(words[j]);

          if(word != null && word.length > 1)
          {
            if (!result[word])
            {
              result[word] = [];
            }

            result[word].push(element);
          }
        }
      }

      return result;
    },


    /**
     * Cleaning up a given word (removing HTML code)
     *
     * @param word {String} Word to clean
     * @return {String}
     */
    _cleanupWord : function(word)
    {
      if (!word)
      {
        return null;
      }

      return word.replace(qx.bom.htmlarea.HtmlArea.CleanupWordsRegExp, "");
    },


    /**
     * *** IN DEVELOPMENT! ***
     * Returns all text nodes
     *
     * @return {Array} Text nodes
     */
    getTextNodes : function() {
      return this._fetchTextNodes(this.getContentBody());
    },


    /**
     * *** IN DEVELOPMENT! ***
     * Helper method for returning all text nodes
     *
     * @param element {Element} element to retrieve all text nodes from
     * @return {Array} Text nodes
     */
    _fetchTextNodes : function(element)
    {
      var result = [];
      var tmp;

      // Element node
      if(element.hasChildNodes)
      {
        for(var i=0; i<element.childNodes.length; i++)
        {
          tmp = this._fetchTextNodes(element.childNodes[i]);
          qx.lang.Array.append(result, tmp);
        }
      }

      // Text node
      if(element.nodeType == 3)
      {
        // Contains real text
        if(element.nodeValue.length > 1)
        {
          result.push(element);
        }
      }

      return result;
    },


    /*
    ---------------------------------------------------------------------------
      INITIALIZATION
    ---------------------------------------------------------------------------
    */

    __loadCounter : 0,


    /**
     * should be removed if someone find a better way to ensure that the document
     * is ready in IE6
     *
     * @return {void}
     */
    __waitForDocumentReady : function()
    {
      var doc = this._getIframeDocument();

      // first we try to get the document
      if (!doc)
      {
        this.__loadCounter++;

        if (this.__loadCounter > 5)
        {
          this.error('cant load HtmlArea. Document is not available. ' + doc);
          this.fireDataEvent("loadingError");
        }
        else
        {
          if (qx.core.Environment.get("qx.debug")) {
            this.error('document not available, try again...');
          }

          qx.event.Timer.once(function()
          {
            this.__waitForDocumentReady();
          }, this, 0);
        }
      }
      else
      {
        // reset counter, now we try to open the document
        this.__loadCounter = 0;
        this._onDocumentIsReady();
      }
    },


    /**
     * Is executed when event "load" is fired
     *
     * @param e {Object} Event object
     * @return {void}
     */
    _loaded : function(e)
    {
      if (this.__isLoaded) {
        return;
      }

      if (this.__isInvalid) {
        this.__resetEditorToValidState();
        return;
      }

      // sometimes IE does some strange things and the document is not available
      // so we wait for it
      if ((qx.core.Environment.get("engine.name") == "mshtml")) {
        this.__waitForDocumentReady();
      } else {
        this._onDocumentIsReady();
      }
    },


    /**
     * Whether the editor is ready to accept commands etc.
     *
     * @return {Boolean} ready or not
     */
    isReady : function() {
      return this.__isReady;
    },


    /**
     * Initializes the command manager, sets the document editable, renders
     * the content and adds a needed event listeners when the document is ready
     * for it.
     * After the successful startup the "ready" event is thrown.
     *
     * @type member
     * @return {void}
     */
    _onDocumentIsReady : function()
    {
      var cm = new qx.bom.htmlarea.manager.Command(this);
      if (this.getUseUndoRedo()) {
        cm = new qx.bom.htmlarea.manager.UndoRedo(cm, this);
      }

      this.__isLoaded = true;

      // For IE the document needs to be set in "designMode"
      // BEFORE the content is rendered.
      if ((qx.core.Environment.get("engine.name") == "mshtml")) {
        this.setEditable(true);
      }

      // Open a new document and insert needed elements plus the initial content
      this.__renderContent();

      if (!(qx.core.Environment.get("engine.name") == "opera")) {
        this.__addListeners();
      }

      // Setting the document editable for all other browser engines
      // AFTER the content is set
      if (!(qx.core.Environment.get("engine.name") == "mshtml")) {
        this.setEditable(true);
      }

      this.__isReady = true;

      // replace the commandManager to be sure the stacked commands are
      // executed at the correct manager
      this.__commandManager = cm;
      cm.setContentDocument(this._getIframeDocument());

      this.__processStackedCommands();

      // Add listeners to opera after the edit mode is activated,
      // otherwise the listeners will be removed
      if ((qx.core.Environment.get("engine.name") == "opera")) {
        this.__addListeners();
      }

      // dispatch the "ready" event at the end of the initialization
      this.fireEvent("ready");
    },


    /**
     * Forces the htmlArea to reset the document editable. This method can
     * be useful (especially for Gecko) whenever the HtmlArea was hidden and
     * gets visible again.
     */
    forceEditable : qx.core.Environment.select("engine.name",
    {
      "gecko" : function()
      {
        var doc = this._getIframeDocument();
        if (doc)
        {
          /*
           * Don't ask my why, but this is the only way I found to get
           * gecko back to a state of an editable document after the htmlArea
           * was hidden and visible again.
           * Yes, and there are differences in Firefox 3.x and Firefox 2.x
           */
          if (parseFloat(qx.core.Environment.get("engine.version")) >= "1.9")
          {
            doc.designMode = "Off";

            doc.body.contentEditable = false;
            doc.body.contentEditable = true;
          }
          else
          {
            doc.body.contentEditable = true;
            this.__setDesignMode(true);
          }
        }
      },

      "default" : qx.lang.Function.empty
    }),


    /**
     * Sets the editor for all gecko browsers into the state "invalid" to be
     * able to re-initialize the editor with the next load of the iframe.
     *
     * This "invalid" state is necessary whenever the whole HtmlArea high-level
     * widget is moved around to another container.
     *
     * Only implemented for Gecko browser.
     *
     * @signature function()
     */
    invalidateEditor : qx.core.Environment.select("engine.name",
    {
      "gecko" : function()
      {
        this.__isLoaded = false;
        this.__isReady = false;
        this.__isInvalid = true;
      },

      "default" : function() {}
    }),


    /**
     * Called when the iframes is loaded and the HtmlArea is in the "invalid"
     * state. Re-initializes the HtmlArea and fires the {@link qx.bom.htmlarea.HtmlArea#readyAfterInvalid}
     * event to offer a time moment for the application developer to execute
     * commands after the re-location.
     *
     * Only implemented for Gecko browser.
     *
     * @signature function()
     */
    __resetEditorToValidState : qx.core.Environment.select("engine.name",
    {
      "gecko" : function()
      {
        this.__renderContent();
        this.__addListeners();

        this.__commandManager.setContentDocument(this._getIframeDocument());

        this.setEditable(true);
        this.forceEditable();

        this.__isLoaded = true;
        this.__isReady = true;
        this.__isInvalid = false;

        this.fireEvent("readyAfterInvalid");
      },

      "default" : function() {}
    }),


    /**
     * Returns style attribute as string of a given element
     *
     * @param elem {Element} Element to check for styles
     * @return {String} Complete style attribute as string
     */
    __getElementStyleAsString : function(elem)
    {
      var style = "";

      if (!elem) {
        return style;
      }

      try
      {
        var styleAttrib = elem.getAttribute("style");

        if (!styleAttrib) {
          return style;
        }

        // IE returns an array when calling getAttribute
        if ((qx.core.Environment.get("engine.name") == "mshtml"))
        {
          style = styleAttrib.cssText;
        }
        else
        {
          style = styleAttrib;
        }
      }
      catch(exc)
      {
        this.error("can't extract style from elem. ");
      }

      return style;
    },


    /**
     * Returns the document skeleton with content usable for the editor
     *
     * @param value {String} body.innerHTML
     * @return {String} content
     */
    __generateDocumentSkeleton : function(value)
    {
      // To hide the horizontal scrollbars in gecko browsers set the
      // "overflow-x" explicit to "hidden"
      // In mshtml browsers this does NOT work. The property "overflow-x"
      // overwrites the value of "overflow-y".
      var overflow = qx.core.Environment.get("engine.name") == "gecko" ?
        " html, body {overflow-x: visible; } " : "";

      var skeletonParts = this.__documentSkeletonParts[this.getContentType()];
      var head = '<head>' + skeletonParts.meta +
                 '<style type="text/css">' + overflow + skeletonParts.style + skeletonParts.contentStyle + this.__styleInformation + '</style>' +
                 '</head>';
      var content = skeletonParts.body + value;

      // When setting the content with a doctype IE7 has one major problem.
      // With EVERY char inserted the editor component hides the text/flickers.
      // To display it again it is necessary to unfocus and focus again the
      // editor component. To avoid this unwanted behaviour it is necessary to
      // set NO DOCTYPE.
      return skeletonParts.html + head + content + skeletonParts.footer;
    },


    /**
     * Opens a new document and sets the content (if available)
     *
     * @return {void}
     */
    __renderContent : function()
    {
      var value = this.__generateDefaultContent(this.getValue());

      if (qx.lang.Type.isString(value))
      {
        var doc = this._getIframeDocument();
        try
        {
          doc.open("text/html", true);
          doc.write(this.__generateDocumentSkeleton(value));
          doc.close();
        }
        catch (e)
        {
          this.error("cant open document on source '"+qx.bom.Iframe.queryCurrentUrl(this.__iframe) +"'", e);
          this.fireDataEvent("loadingError", e);
        }
      }
    },


    /**
     * Adds all needed eventlistener
     *
     * @return {void}
     */
    __addListeners : function()
    {
      this.__addKeyListeners();
      this.__addMouseListeners();
      this.__addFocusListeners();
    },


    /**
     * Add key event listeners to the body element
     */
    __addKeyListeners : function()
    {
      var Registration = qx.event.Registration;
      var doc = this._getIframeDocument();

      Registration.addListener(doc.body, "keypress", this._handleKeyPress, this);
      Registration.addListener(doc.body, "keyup", this._handleKeyUp,    this);
      Registration.addListener(doc.body, "keydown", this._handleKeyDown,  this);
    },


    /**
     * Add focus event listeners.
     */
    __addFocusListeners : function()
    {
      var Registration = qx.event.Registration;
      var doc = this._getIframeDocument();

      var focusBlurTarget = qx.core.Environment.get("engine.name") == "webkit" ? this._getIframeWindow() : doc.body;
      Registration.addListener(focusBlurTarget, "focus", this._handleFocusEvent, this);
      Registration.addListener(focusBlurTarget, "blur", this._handleBlurEvent, this);

      Registration.addListener(doc, "focusout",  this._handleFocusOutEvent, this);
    },


    /**
     * Add mouse event listeners.
     */
    __addMouseListeners : function()
    {
      // The mouse events are primarily needed to examine the current cursor context.
      // The cursor context examines if the current text node is formatted in any
      // manner like bold or italic. An event is thrown to e.g. activate/deactivate
      // toolbar buttons.
      // Additionally the mouseup at document level is necessary for gecko and
      // webkit to reset the focus (see Bug #2896).
      var Registration = qx.event.Registration;
      var doc = this._getIframeDocument();

      var mouseEventName = qx.core.Environment.get("engine.name") == "mshtml" ? "click" : "mouseup";
      Registration.addListener(doc.body, mouseEventName, this._handleMouseUpOnBody, this);
      Registration.addListener(doc.documentElement, mouseEventName, this._handleMouseUpOnDocument, this);

      Registration.addListener(doc.documentElement, "contextmenu", this._handleContextMenuEvent, this);
    },


    /**
     * Helper method to create an object which acts like a command manager
     * instance to collect all commands which are executed BEFORE the command
     * manager instance is ready.
     *
     * @return {Object} stack command manager object
     */
    __createStackCommandManager : function()
    {
      if (this.__stackCommandManager == null)
      {
        this.__stackCommandManager = {
          execute : function(command, value)
          {
            this.stackedCommands = true;
            this.commandStack.push( { command : command, value : value } );
          },

          commandStack : [],
          stackedCommands : false
        };
      }
      this.__stackCommandManager.stackedCommands = false;

      return this.__stackCommandManager;
    },


    /**
     * Process the stacked commands if available.
     * This feature is necessary at startup when the command manager is yet
     * not ready to execute the commands after the initialization.
     */
    __processStackedCommands : function()
    {
      var manager = this.__stackCommandManager;

      if (manager != null && manager.stackedCommands)
      {
        var commandStack = manager.commandStack;
        if (commandStack != null)
        {
          for (var i=0, j=commandStack.length; i<j; i++) {
            this.__commandManager.execute(commandStack[i].command, commandStack[i].value);
          }
        }
      }
    },


    /**
     * Sets the designMode of the document
     *
     * @param onOrOff {Boolean} Set or unset the design mode on the current document
     * @return {void}
     */
    __setDesignMode : function (onOrOff)
    {
      var doc = this._getIframeDocument();

      if (this.__isLoaded && doc)
      {
        try
        {
          if ((qx.core.Environment.get("engine.name") == "gecko"))
          {
            // FF Bug (Backspace etc. doesn't work if we dont set it twice)
            doc.designMode = (onOrOff !== false) ? 'Off' : 'On';
          }

          doc.designMode = (onOrOff !== false) ? 'On' : 'Off';
        }
        catch (e)
        {
          // Fails if the element is not shown actually
          // we set it again in _afterAppear
        }
      }
    },


    /*
    ---------------------------------------------------------------------------
      EDITABLE
    ---------------------------------------------------------------------------
    */

    /**
     * Whether the document is in editable mode
     *
     * @param value {Boolean} Current value
     * @return {void}
     * @throws {Error} Failed to enable rich edit functionality
     */
    setEditable : function(value)
    {
      if (this.__isLoaded)
      {
        this.__setDesignMode(true);

        // For Gecko set additionally "styleWithCSS" to turn on CSS.
        // Fallback for older Gecko engines is "useCSS".
        // see http://www.mozilla.org/editor/midas-spec.html
        if ((qx.core.Environment.get("engine.name") == "gecko"))
        {
          try
          {
            var doc = this._getIframeDocument();
            doc.execCommand("styleWithCSS", false, true);
          }
          catch(ex)
          {
            try
            {
              var doc = this._getIframeDocument();
              doc.execCommand("useCSS", false, false);
            }
            catch(ex)
            {
              if (!this.__isReady)
              {
                this.error("Failed to enable rich edit functionality");
                this.fireDataEvent("loadingError", ex);
              }
              else {
                throw new Error("Failed to enable rich edit functionality");
              }
            }
          }
        }

        this.__isEditable = value;
      }
    },


    /**
     * Whether the document is in editable mode
     *
     * @return {Boolean}
     */
    getEditable : function() {
      return this.__isEditable;
    },


    /**
     * Whether the document is in editable mode
     *
     * @return {Boolean}
     */
    isEditable : function() {
      return this.__isEditable;
    },



    /*
    ---------------------------------------------------------------------------
      EVENT HANDLING
    ---------------------------------------------------------------------------
    */

    /*
     * This flag is only needed by IE to implement the mechanism
     * of a linebreak when pressing "Ctrl+Enter". It is not possible
     * in IE to get to know that both keys are pressed together (at the
     * keypress event). It is only possible to look at the keypress event,
     * set this flag and insert the linebreak at the keyup event.
     */
    __controlPressed : false,


    /**
     * All keyUp events are delegated to this method
     *
     * @param e {Object} Event object
     * @return {void}
     */
    _handleKeyUp : function(e)
    {
      var keyIdentifier = e.getKeyIdentifier().toLowerCase();
      this.__currentEvent = e;

      if ((qx.core.Environment.get("qx.debug")) &&
          qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug")) {
        this.debug(e.getType() + " | " + keyIdentifier);
      }

      // This block inserts a linebreak when the key combination "Ctrl+Enter"
      // was pressed. It is necessary in IE to look after the keypress and the
      // keyup event. The keypress delivers the "Ctrl" key and the keyup the
      // "Enter" key. If the latter occurs right after the first one the
      // linebreak gets inserted.
      if (
        qx.core.Environment.get("engine.name") == "mshtml" ||
        qx.core.Environment.get("engine.name") == "webkit"
      ) {
        if (this.__controlPressed)
        {
          switch(keyIdentifier)
          {
            case "enter":
              if (this.getInsertLinebreakOnCtrlEnter())
              {
                if ((qx.core.Environment.get("engine.name") == "webkit"))
                {
                  this.__insertWebkitLineBreak();

                  e.preventDefault();
                  e.stopPropagation();
                }
                else
                {
                  var rng = this.__createRange(this.getSelection());

                  if (rng)
                  {
                    rng.collapse(true);
                    rng.pasteHTML('<br/><div class="placeholder"></div>');
                  }
                }

                this.__startExamineCursorContext();
              }
            break;

            // The keyUp event of the control key ends the "Ctrl+Enter" session.
            // So it is supported that the user is pressing this combination
            // several times without releasing the "Ctrl" key.
            case "control":
              this.__controlPressed = false;
              return;
            break;
          }
        }
      }
      else if ((qx.core.Environment.get("engine.name") == "gecko"))
      {
        // These keys can change the selection
        switch(keyIdentifier)
        {
          case "left":
          case "right":
          case "up":
          case "down":
          case "pageup":
          case "pagedown":
          case "delete":
          case "end":
          case "backspace":
            this.__isFirstLineSelected = (this.getFocusNode() == this.getContentBody().firstChild);
          break;
        }
      }
    },


    /**
     * Helper function which inserts an linebreak at the selection.
     *
     */
    __insertWebkitLineBreak : function()
    {
      var sel = this.getSelection();
      var helperString = "";

      // Insert bogus node if we are on an empty line:
      if(sel && (sel.focusNode.textContent == "" || sel.focusNode.parentElement.tagName == "LI")) {
        helperString = "<br class='webkit-block-placeholder' />";
      }

      this.__commandManager.execute("inserthtml", helperString + qx.bom.htmlarea.HtmlArea.simpleLinebreak);
    },


    /**
     * All keyDown events are delegated to this method
     *
     * @param e {Object} Event object
     * @return {void}
     */
    _handleKeyDown : qx.core.Environment.select("engine.name",
    {
      "mshtml|webkit" : function(e)
      {
        var keyIdentifier   = e.getKeyIdentifier().toLowerCase();

        if ((qx.core.Environment.get("qx.debug")) &&
            qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug")) {
          //this.debug(e.getType() + " | " + e.getKeyIdentifier().toLowerCase());
        }

        /* Stop the key events "Ctrl+Z" and "Ctrl+Y" for IE (disabling the browsers shortcuts) */
        if (this.__controlPressed && (keyIdentifier == "z" || keyIdentifier == "y" ||
                                      keyIdentifier == "b" || keyIdentifier == "u" ||
                                      keyIdentifier == "i" || keyIdentifier == "k"))
        {
          e.preventDefault();
          e.stopPropagation();
        }

        /*
         * Only set the flag to true
         * Setting it to false is handled in the "keyUp" handler
         * otherwise holding the "Ctrl" key and hitting e.g. "z"
         * will start the browser shortcut at the second time.
         */
        if(keyIdentifier == "control") {
          this.__controlPressed = true;
        }

      },

      "default" : function(e) {}
    }),


    /**
     * All keyPress events are delegated to this method
     *
     * @param e {Object} Event object
     * @return {void}
     */
   _handleKeyPress : function(e)
   {
      var doc = this.getContentDocument();
      var keyIdentifier   = e.getKeyIdentifier().toLowerCase();
      var isCtrlPressed   = e.isCtrlPressed();
      var isShiftPressed  = e.isShiftPressed();
      this.__currentEvent = e;

      if ((qx.core.Environment.get("qx.debug")) &&
          qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug")) {
        this.debug(e.getType() + " | " + keyIdentifier);
      }

      // if a hotkey was executed at an empty selection it is necessary to fire
      // a "cursorContext" event after the first input
      if (this.__fireCursorContextOnNextInput)
      {
        // for IE it's necessary to NOT look at the cursorcontext right after
        // the "Enter" because the corresponding styles / elements are not yet
        // created.
        var fireEvent = !((qx.core.Environment.get("engine.name") == "mshtml") && keyIdentifier == "enter") ||
                        !((qx.core.Environment.get("engine.name") == "gecko") &&  keyIdentifier == "enter");

        if (fireEvent)
        {
          this.__startExamineCursorContext();
          this.__fireCursorContextOnNextInput = false;
        }
      }


      switch(keyIdentifier)
      {
        case "enter":
          // If only "Enter" key was pressed and "messengerMode" is activated
          if (!isShiftPressed && !isCtrlPressed && this.getMessengerMode())
          {
            e.preventDefault();
            e.stopPropagation();

            this.fireDataEvent("messengerContent", this.getComputedValue());
            this.resetHtml();
          }

          // This mechanism is to provide a linebreak when pressing "Ctrl+Enter".
          // The implementation for IE is located at the "control" block and at
          // the "_handleKeyUp" method.
          if (isCtrlPressed)
          {
            if (!this.getInsertLinebreakOnCtrlEnter()) {
              return;
            }

            e.preventDefault();
            e.stopPropagation();

            if ((qx.core.Environment.get("engine.name") == "gecko"))
            {
              if (this.__isSelectionWithinWordBoundary())
              {
                this.insertHtml("<br />");
                this.__startExamineCursorContext();
                return;
              }

              // Insert additionally an empty div element - this ensures that
              // the caret is shown and the cursor moves down a line correctly
              //
              // ATTENTION: the "div" element itself gets not inserted by Gecko,
              // it is only necessary to have anything AFTER the "br" element to
              // get it work.
              this.insertHtml("<br /><div id='placeholder'></div>");
            }
            else if ((qx.core.Environment.get("engine.name") == "opera"))
            {
              // To insert a linebreak for Opera it is necessary to work with
              // ranges and add the <br> element on node-level. The selection
              // of the node afterwards is necessary for Opera to show the
              // cursor correctly.
              var sel = this.getSelection();
              var rng = this.__createRange(sel);

              if (sel && rng)
              {
                var brNode = doc.createElement("br");
                rng.collapse(true);
                rng.insertNode(brNode);
                rng.collapse(true);

                rng.selectNode(brNode);
                sel.addRange(rng);
                rng.collapse(true);
              }
            }

            this.__startExamineCursorContext();
          }

          // Special handling for IE when hitting the "Enter" key instead of
          // letting the IE insert a <p> insert manually a <br> if the
          // corresponding property is set.
          if ((qx.core.Environment.get("engine.name") == "mshtml"))
          {
            if (!this.getInsertParagraphOnLinebreak())
            {
              // Insert a "br" element to force a line break. If the insertion
              // succeeds stop the key event otherwise let the browser handle
              // the linebreak e.g. if the user is currently editing an
              // (un)ordered list.
              if (this.__commandManager.execute("inserthtml", qx.bom.htmlarea.HtmlArea.simpleLinebreak))
              {
                this.__startExamineCursorContext();
                e.preventDefault();
                e.stopPropagation();
              }
            }
          }
          // Special handling for Firefox when hitting the "Enter" key
          else if((qx.core.Environment.get("engine.name") == "gecko"))
          {
            if (this.getInsertParagraphOnLinebreak() &&
                !isShiftPressed && !isCtrlPressed)
            {
              var sel = this.getSelection();

              if (sel)
              {
                var selNode = sel.focusNode;

                // check if the caret is within a word - Gecko can handle it
                if (this.__isSelectionWithinWordBoundary())
                {
                  this.__startExamineCursorContext();
                  return;
                }

                // caret is at an empty line
                if (this.__isFocusNodeAnElement())
                {
                  this.__startExamineCursorContext();
                  return;
                }

                // check if inside a list
                while (!qx.dom.Node.isNodeName(selNode, "body"))
                {
                  if (qx.dom.Node.isNodeName(selNode, "li"))
                  {
                    this.__startExamineCursorContext();
                    return;
                  }
                  selNode = selNode.parentNode;
                }
              }

              this.__commandManager.insertParagraphOnLinebreak();
              e.preventDefault();
              e.stopPropagation();

              this.__startExamineCursorContext();
              this.__fireCursorContextOnNextInput = true;
            }
          }
          else if((qx.core.Environment.get("engine.name") == "webkit"))
          {
            if (this.getInsertParagraphOnLinebreak() && isShiftPressed)
            {
              this.__insertWebkitLineBreak();

              e.preventDefault();
              e.stopPropagation();

              this.__startExamineCursorContext();
           }
          }
          break;


        case "up" :
          // Firefox 2 needs some additional work to select the first line
          // completely in case the selection is already on the first line and
          // "key up" is pressed.
          if (qx.core.Environment.get("engine.name") == "gecko" &&
            qx.core.Environment.get("engine.version") < 1.9 && isShiftPressed)
          {
            var sel = this.getSelection();

            // First line is selected
            if(sel && sel.focusNode == doc.body.firstChild)
            {
              // Check if the first line has been (partly) selected before.
              if(this.__isFirstLineSelected)
              {
                // Check if selection does not enclose the complete line already
                if (sel.focusOffset != 0)
                {
                  // Select the complete line.
                  sel.extend(sel.focusNode, 0);
                }
              }
            }
          }

          this.__startExamineCursorContext();
          break;


        // Firefox 2 needs some extra work to move the cursor (and optionally
        // select text while moving) to first position in the first line.
        case "home":
          if (qx.core.Environment.get("engine.name") == "gecko" &&
            qx.core.Environment.get("engine.version") < 1.9)
          {
            if(isCtrlPressed)
            {
              var sel = this.getSelection();

              // Select text from current position to first character on first line
              if (isShiftPressed)
              {
                // Check if target position is not yet selected
                if (sel && (sel.focusOffset != 0) || (sel.focusNode != doc.body.firstChild))
                {
                  // Extend selection to first child at position 0
                  sel.extend(doc.body.firstChild, 0);
                }
              }
              else
              {
                var elements = null;
                var currentItem;

                // Fetch all text nodes from body element
                if (doc) {
                  elements = doc.evaluate("//text()[string-length(normalize-space(.))>0]", doc.body, null, XPathResult.ANY_TYPE, null);
                }

                if (elements && sel)
                {
                  while(currentItem = elements.iterateNext())
                  {
                    // Skip CSS text nodes
                    if(currentItem && currentItem.parentNode &&
                       currentItem.parentNode.tagName != "STYLE")
                    {
                      // Expand selection to first text node and collapse here
                      try
                      {
                        // Sometimes this does not work...
                        sel.extend(currentItem, 0);
                        if (!this.isSelectionCollapsed()) {
                          sel.collapseToStart();
                        }
                      } catch(e) {}

                      // We have found the correct text node, leave loop here
                      break;
                    }
                  }
                }
              }
            }
          }

          this.__startExamineCursorContext();
        break;

        // For all keys which are able to reposition the cursor start to examine
        // the current cursor context
        case "left":
        case "right":
        case "down":
        case "pageup":
        case "pagedown":
        case "delete":
        case "end":
        case "backspace":
          this.__startExamineCursorContext();
        break;

        // Special shortcuts
        case "b":
          if (isCtrlPressed) {
            this.__executeHotkey('bold', true);
          }
        break;

        case "i":
        case "k":
          if (isCtrlPressed) {
            this.__executeHotkey('italic', true);
          }
        break;

        case "u":
          if (isCtrlPressed) {
            this.__executeHotkey('underline', true);
          }
        break;

        case "z":
          if (isCtrlPressed && !isShiftPressed) {
            this.__executeHotkey('undo', true);
          }
          else if (isCtrlPressed && isShiftPressed)
          {
            this.__executeHotkey('redo', true);
          }
        break;

        case "y":
          if (isCtrlPressed) {
            this.__executeHotkey('redo', true);
          }
          break;

        case "a":
          // Select the whole content if "Ctrl+A" was pressed
          //
          // NOTE: this code is NOT executed for mshtml and webkit. To get to
          // know if "Ctrl+A" is pressed in mshtml/webkit one need to check
          // this within the "keyUp" event. This info is not available
          // within the "keyPress" event in mshtml/webkit.
          if (isCtrlPressed) {
            this.selectAll();
          }
        break;

       }

       this.__currentEvent = null;
    },


    /**
     * Executes a method and prevent default
     *
     * @param hotkeyIdentifier {String} hotkey identifier for lookup
     * @param preventDefault {Boolean} whether do preventDefault or not
     * @return {void}
     */
    __executeHotkey : function (hotkeyIdentifier, preventDefault)
    {
      var method = null;
      var hotkeyInfo = qx.bom.htmlarea.HtmlArea.hotkeyInfo;
      if (hotkeyInfo[hotkeyIdentifier]) {
        method = hotkeyInfo[hotkeyIdentifier].method;
      }

      if (method != null && this[method])
      {
        this[method]();

        if (preventDefault)
        {
          this.__currentEvent.preventDefault();
          this.__currentEvent.stopPropagation();
        }

        if (this.isSelectionCollapsed()) {
          this.__fireCursorContextOnNextInput = true;
        }

        // Whenever a hotkey is pressed update the current cursorContext
        // Since this examination is done within a timeout we can be sure
        // the execution is performed before we're looking at the cursor context.
        this.__startExamineCursorContext();
      }
    },


    /**
     * Eventlistener for focus events
     *
     * @param e {Object} Event object
     * @return {void}
     */
    _handleFocusEvent : function(e)
    {
      this.__storedSelectedHtml = null;

      if (
        qx.core.Environment.get("engine.name") == "gecko" ||
        qx.core.Environment.get("engine.name") == "webkit"
      ) {
        // Remove element to focus, as the editor is focused for the first time
        // and the element is not needed anymore.
        var elementToFocus = this.getContentDocument().getElementById("__elementToFocus__");
        if (elementToFocus) {
          qx.bom.element.Attribute.reset(elementToFocus, "id");
        }
      }

      this.fireEvent("focused");
    },


    /**
     * Eventlistener for blur events
     *
     * @param e {Object} Event object
     * @return {void}
     */
    _handleBlurEvent : function(e) {
      this.__value = this.getComputedValue();
    },


    /**
     * Eventlistener for focusout events - dispatched before "blur"
     *
     * @param e {Object} Event object
     * @return {void}
     */
    _handleFocusOutEvent : function(e)
    {
      this.__controlPressed = false;

      if (this.__storedSelectedHtml == null) {
        this.__storedSelectedHtml = this.getSelectedHtml();
      }

      this.fireEvent("focusOut");
    },


    /**
     * Eventlistener for all mouse events.
     * This method is invoked for mshtml on "click" events and
     * on "mouseup" events for all others.
     *
     * @param e {qx.event.type.Mouse} mouse event instance
     * @return {void}
     */
    _handleMouseUpOnBody : function(e)
    {
      if ((qx.core.Environment.get("qx.debug")) &&
          qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug")) {
        this.debug("handleMouse " + e.getType());
      }
      this.__mouseUpOnBody = true;

      this.__startExamineCursorContext();
    },


    /**
     * Checks if the user has performed a selection and released  the mouse
     * button outside of the editor. If so the body element is re-activated
     * to receive the keypress events correctly.
     *
     * @param e {qx.event.type.Mouse} mouse event instance
     *
     * @signature function(e)
     * @return {void}
     */
    _handleMouseUpOnDocument : qx.core.Environment.select("engine.name", {
      "mshtml" : qx.lang.Function.empty,

      "default" : function(e)
      {
        if (!this.__mouseUpOnBody) {
          qx.bom.Element.activate(this.getContentBody());
        }
        this.__mouseUpOnBody = false;
      }
    }),


    /**
     * If the property {@link #nativeContextMenu} is set to <code>false</code> this handler method
     * stops the browser from displaying the native context menu and fires an own event for the
     * application developers to position their own (qooxdoo) contextmenu.
     *
     * Fires a data event with the following data:
     *
     *   * x - absolute x coordinate
     *   * y - absolute y coordinate
     *   * relX - relative x coordinate
     *   * relY - relative y coordinate
     *   * target - DOM element target
     *
     * Otherwise the native browser contextmenu is shown as usual.
     *
     * @param e {Object} Event object
     */
    _handleContextMenuEvent : function(e)
    {
      // only fire own "contextmenu" event if the native contextmenu should not be used
      if (!this.getNativeContextMenu())
      {
        var relX = e.getViewportLeft();
        var relY = e.getViewportTop();

        var absX = qx.bom.element.Location.getLeft(this.__widget) + relX;
        var absY = qx.bom.element.Location.getTop(this.__widget) + relY;

        var data = {
          x: absX,
          y: absY,
          relX: relX,
          relY: relY,
          target: e.getTarget()
        };

        e.preventDefault();
        e.stopPropagation();

        qx.event.Timer.once(function() {
          this.fireDataEvent("contextmenu", data);
        }, this, 0);
      }
    },


    /*
    ---------------------------------------------------------------------------
      EXEC-COMMANDS
    ---------------------------------------------------------------------------
    */

    /**
     * Service method to check if the component is already loaded.
     * Overrides the base method.
     *
     * @return {Boolean}
     */
    isLoaded : function () {
      return this.__isLoaded;
    },


    /**
     * Inserts html content on the current selection
     *
     * @param value {String} html content
     * @return {Boolean} Success of operation
     */
    insertHtml : function (value) {
      return this.__commandManager.execute("inserthtml", value);
    },


    /**
     * Removes all formatting styles on the current selection content and resets
     * the font family and size to the default ones. See {@link #defaultFontSize}
     * and {@link #defaultFontFamily}.
     *
     * @return {Boolean} Success of operation
     */
    removeFormat : function()
    {
      var value = this.__commandManager.execute("removeformat");

      // reset the default font size and family
      this.__commandManager.execute("fontsize", this.getDefaultFontSize());
      this.__commandManager.execute("fontfamily", this.getDefaultFontFamily());

      return value;
    },


    /**
     * Sets the current selection content to bold font style
     *
     * @return {Boolean} Success of operation
     */
    setBold : function() {
      return this.__commandManager.execute("bold");
    },


    /**
     * Sets the current selection content to italic font style
     *
     * @return {Boolean} Success of operation
     */
    setItalic : function() {
      return this.__commandManager.execute("italic");
    },


    /**
     * Sets the current selection content to underline font style
     *
     * @return {Boolean} Success of operation
     */
    setUnderline : function() {
      return this.__commandManager.execute("underline");
    },


    /**
     * Sets the current selection content to strikethrough font style
     *
     * @return {Boolean} Success of operation
     *
     */
    setStrikeThrough : function() {
      return this.__commandManager.execute("strikethrough");
    },


    /**
     * Sets the current selection content to the specified font size
     *
     * @param value {Number} Font size
     * @return {Boolean} Success of operation
     */
    setFontSize : function(value) {
      return this.__commandManager.execute("fontsize", value);
    },


    /**
     * Sets the current selection content to the specified font family
     *
     * @param value {String} Font family
     * @return {Boolean} Success of operation
     */
    setFontFamily : function(value) {
      return this.__commandManager.execute("fontfamily", value);
    },


    /**
     * Sets the current selection content to the specified font color
     *
     * @param value {String} Color value (supported are Hex,
     * @return {Boolean} Success of operation
     */
    setTextColor : function(value) {
      return this.__commandManager.execute("textcolor", value);
    },


    /**
     * Sets the current selection content to the specified background color
     *
     * @param value {String} Color value (supported are Hex,
     * @return {Boolean} Success of operation
     */
    setTextBackgroundColor : function(value) {
      return this.__commandManager.execute("textbackgroundcolor", value);
    },


    /**
     * Left-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyLeft : function() {
      return this.__commandManager.execute("justifyleft");
    },


    /**
     * Center-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyCenter : function() {
      return this.__commandManager.execute("justifycenter");
    },


    /**
     * Right-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyRight : function() {
      return this.__commandManager.execute("justifyright");
    },


    /**
     * Full-justifies the current selection
     *
     * @return {Boolean} Success of operation
     */
    setJustifyFull : function() {
      return this.__commandManager.execute("justifyfull");
    },


    /**
     * Indents the current selection
     *
     * @return {Boolean} Success of operation
     */
    insertIndent : function() {
      return this.__commandManager.execute("indent");
    },


    /**
     * Outdents the current selection
     *
     * @return {Boolean} Success of operation
     */
    insertOutdent : function() {
      return this.__commandManager.execute("outdent");
    },


    /**
     * Inserts an ordered list
     *
     * @return {Boolean} Success of operation
     */
    insertOrderedList : function() {
      return this.__commandManager.execute("insertorderedlist");
    },


    /**
     * Inserts an unordered list
     *
     * @return {Boolean} Success of operation
     */
    insertUnorderedList : function() {
      return this.__commandManager.execute("insertunorderedlist");
    },


    /**
     * Inserts a horizontal ruler
     *
     * @return {Boolean} Success of operation
     */
    insertHorizontalRuler : function() {
      return this.__commandManager.execute("inserthorizontalrule");
    },


    /**
     * Insert an image
     *
     * @param attributes {Map} Map of HTML attributes to apply
     * @return {Boolean} Success of operation
     */
    insertImage : function(attributes) {
      return this.__commandManager.execute("insertimage", attributes);
    },


    /**
     * Inserts a hyperlink
     *
     * @param url {String} URL for the image to be inserted
     * @return {Boolean} Success of operation
     */
    insertHyperLink : function(url) {
      return this.__commandManager.execute("inserthyperlink", url);
    },

    /**
     * Alias for setBackgroundColor("transparent");
     *
     * @return {Boolean} if succeeded
     */
    removeBackgroundColor : function () {
      this.__commandManager.execute("backgroundcolor", "transparent");
    },


    /**
     * Sets the background color of the editor
     *
     * @param value {String} color
     * @return {Boolean} if succeeded
     */
    setBackgroundColor : function (value) {
      this.__commandManager.execute("backgroundcolor", value);
    },


    /**
     * Alias for setBackgroundImage(null);
     *
     * @return {Boolean} if succeeded
     */
    removeBackgroundImage : function () {
      this.__commandManager.execute("backgroundimage");
    },


    /**
     * Inserts an background image
     *
     * @param url {String} url of the background image to set
     * @param repeat {String} repeat mode. Possible values are "repeat|repeat-x|repeat-y|no-repeat".
     *                                     Default value is "no-repeat"
     * @param position {String?Array} Position of the background image. Possible values are "|top|bottom|center|left|right|right top|left top|left bottom|right bottom" or
     *                                an array consisting of two values for x and
     *                                y coordinate. Both values have to define the
     *                                unit e.g. "px" or "%".
     *                                Default value is "top"
     * @return {Boolean} Success of operation
     */
    setBackgroundImage : function(url, repeat, position) {
      return this.__commandManager.execute("backgroundimage", [ url, repeat, position ]);
    },


    /**
     * Selects the whole content
     *
     * @return {Boolean} Success of operation
     */
    selectAll : function() {
      return this.__commandManager.execute("selectall");
    },


    /**
     * Undo last operation
     *
     * @return {void}
     */
    undo : function()
    {
      if (this.getUseUndoRedo()) {
        return this.__commandManager.execute("undo");
      } else {
        return true;
      }
    },


    /**
     * Redo last undo
     *
     * @return {void}
     */
    redo : function()
    {
      if (this.getUseUndoRedo()) {
        return this.__commandManager.execute("redo");
      } else {
        return true;
      }
    },


    /*
    ---------------------------------------------------------------------------
      CONTENT MANIPLUATION
    ---------------------------------------------------------------------------
    */

    /**
     * Resets the content of the editor
     *
     * @return {void}
     */
    resetHtml : function()
    {
      var doc = this._getIframeDocument();

      // clearing the editor
      while (doc.body.firstChild) {
        doc.body.removeChild(doc.body.firstChild);
      }

      // Gecko needs a p element with a text-node (&nbsp;) to
      // show the caret after clearing out the content. Otherwise
      // the user is able to type ahead but right after the clearing the
      // caret is not visible (-> cursor does not blink)
      if (qx.core.Environment.get("engine.name") == "gecko") {
        doc.body.innerHTML = "<p>&nbsp;</p>";
      }

      // To ensure Webkit is showing a cursor after resetting the
      // content it is necessary to create a new selection and add a range
      else if (qx.core.Environment.get("engine.name") == "webkit")
      {
        var sel = this.getSelection();
        var rng = doc.createRange();

        if (rng && sel) {
          sel.addRange(rng);
        }
      }
    },


    /**
     * Get html content (call own recursive method)
     *
     * @param skipHtmlEncoding {Boolean ? false} whether the html encoding of text nodes should be skipped
     * @return {String} current content of the editor as XHTML
     */
    getHtml : function(skipHtmlEncoding)
    {
      var doc = this._getIframeDocument();

      if (doc == null) {
        return null;
      }

      return qx.bom.htmlarea.HtmlArea.__getHtml(doc.body, false, skipHtmlEncoding, this.getPostProcess());
    },

    /**
     * Helper function to examine if HTMLArea is empty, except for
     * place holder(s) needed by some browsers.
     *
     * @return {Boolean} True, if area is empty - otherwise false.
     */
    containsOnlyPlaceholder : qx.core.Environment.select("engine.name",
    {

      "mshtml" : function()
      {
        var doc = this._getIframeDocument();
        return (doc.body.innerHTML == "<P>&nbsp;</P>");
      },

      "default" : qx.lang.Function.returnFalse
    }),


    /*
      -----------------------------------------------------------------------------
      FOCUS MANAGEMENT
      -----------------------------------------------------------------------------
    */

    /**
     * Convenient function to select an element. The "set" method of qx.bom.Selection is not
     * sufficient here. It does select the element, but does not show the caret.
     *
     * @param element {Element} DOM element to select
     */
    _selectElement : function(element)
    {
      var selection = this.getContentWindow().getSelection();
      var range =  this.getContentDocument().createRange();

      range.setStart(element, 0);
      selection.removeAllRanges();
      selection.addRange(range);
    },


    /**
     * Can be used to set the user focus to the content. Also used when the "TAB" key is used to
     * tab into the component. This method is also called by the {@link qx.ui.embed.HtmlArea} widget.
     *
     * @signature function()
     */
    focusContent : qx.core.Environment.select("engine.name",
    {
      "gecko" : function()
      {
        var contentDocument = this.getContentDocument();
        var elementToFocus = contentDocument.getElementById("__elementToFocus__");

        this.getContentWindow().focus();
        qx.bom.Element.focus(this.getContentBody());

        if (elementToFocus) {
          this._selectElement(elementToFocus);
        } else {
          this.__checkForContentAndSetDefaultContent();
        }
      },

      "webkit" : function()
      {
        qx.bom.Element.focus(this.getContentWindow());
        qx.bom.Element.focus(this.getContentBody());

        var elementToFocus = this.getContentDocument().getElementById("__elementToFocus__");
        if (elementToFocus) {
          qx.bom.element.Attribute.reset(elementToFocus, "id");
        }

        this.__checkForContentAndSetDefaultContent();
      },

      "opera" : function()
      {
        qx.bom.Element.focus(this.getContentWindow());
        qx.bom.Element.focus(this.getContentBody());

        this.__checkForContentAndSetDefaultContent();
      },

      "default" : function()
      {
        qx.bom.Element.focus(this.getContentBody());

        this.__checkForContentAndSetDefaultContent();
      }
    }),


    /**
     * Helper method which checks if content is available and if not sets the default content.
     */
    __checkForContentAndSetDefaultContent : function()
    {
      if (!this.__isContentAvailable()) {
        this.__resetToDefaultContentAndSelect();
      }
    },


    /**
     * Checks whether content is available
     *
     * @signature function()
     */
    __isContentAvailable : qx.core.Environment.select("engine.name",
    {
      "gecko" : function()
      {
        // important to check for all childNodes (text nodes inclusive) rather than only check for
        // child element nodes
        var childs = this.getContentBody().childNodes;

        if (childs.length == 0) {
          return false;
        } else if (childs.length == 1) {
          // consider a BR element with "_moz_dirty" attribute as empty content
          return !(childs[0] && qx.dom.Node.isNodeName(childs[0], "br") &&
                   qx.bom.element.Attribute.get(childs[0], "_moz_dirty") != null);
        } else {
          return true;
        }
      },

      "webkit" : function()
      {
        // important to check for all childNodes (text nodes inclusive) rather than only check for
        // child element nodes
        var childs = this.getContentBody().childNodes;

        if (childs.length == 0) {
          return false;
        } else if (childs.length == 1) {
          // consider a solely BR element as empty content
          return !(childs[0] && qx.dom.Node.isNodeName(childs[0], "br"));
        } else {
          return true;
        }
      },

      "default" : function()
      {
        // important to check for all childNodes (text nodes inclusive) rather than only check for
        // child element nodes
        var childs = this.getContentBody().childNodes;

        if (childs.length == 0) {
          return false;
        } else if (childs.length == 1) {
          return !(childs[0] && qx.dom.Node.isNodeName(childs[0], "p") &&
                   childs[0].firstChild == null);
        } else {
          return true;
        }
      }
    }),


    /**
     * Resets the content and selects the default focus node
     *
     * @signature function()
     */
    __resetToDefaultContentAndSelect : qx.core.Environment.select("engine.name",
    {
      "gecko|webkit" : function()
      {
        this.getContentDocument().body.innerHTML = this.__generateDefaultContent("");

        var elementToFocus = this.getContentDocument().getElementById("__elementToFocus__");
        qx.bom.element.Attribute.reset(elementToFocus, "id");
        this._selectElement(elementToFocus);
      },

      "default" : function()
      {
        var firstParagraph = qx.dom.Hierarchy.getFirstDescendant(this.getContentBody());

        if (qx.dom.Node.isNodeName(firstParagraph, "p"))
        {
          qx.bom.element.Style.set(firstParagraph, "font-family", this.getDefaultFontFamily());
          qx.bom.element.Style.set(firstParagraph, "font-size", this.getDefaultFontSize());
        }
      }
    }),



    /*
      -----------------------------------------------------------------------------
      PROCESS CURSOR CONTEXT
      -----------------------------------------------------------------------------
    */


    /**
     * Returns the information about the current context (focusNode). It's a
     * map with information about "bold", "italic", "underline", etc.
     *
     * @return {Map} formatting information about the focusNode
     */
    getContextInformation : function() {
      return this.__examineCursorContext();
    },

    /**
     * Wrapper method to examine the current context
     *
     * @return {void}
     */
    __startExamineCursorContext : function()
    {
      // setting a timeout is important to get the right result */
      qx.event.Timer.once(function(e) {
        var contextInfo = this.__examineCursorContext();
        this.fireDataEvent("cursorContext", contextInfo);
      }, this, 200);
    },


    /**
     * Examines the current context of the cursor (e.g. over bold text).
     * This method will dispatch the data event "cursorContext" which holds a map
     * with different keys like bold, italic, underline etc.
     * The main purpose for this event is to be able to set the states of your toolbar
     * buttons so you can e.g. visualize that the cursor is currently over bold text.
     *
     * The possible values are
     * -1 = command is not possible at the moment. Used to disable the corresponding buttons
     *  0 = command is possible at the moment. Used to enable the corresponding buttons (e.g. bold/italic/underline etc.)
     *  1 = cursor is over content which already received that command. Used to to activate the corresponding buttons (e.g. bold/italic/underline etc.)
     *
     * @lint ignoreDeprecated(_processingExamineCursorContext)
     * @return {void}
     */
    __examineCursorContext : function()
    {
      if (this._processingExamineCursorContext || this.getEditable() == false) {
        return;
      }
      this._processingExamineCursorContext = true;

      if (!this.__isContentAvailable()) {
        this.__resetToDefaultContentAndSelect();
      }

      var focusNode = this.getFocusNode();
      if (focusNode == null) {
        return;
      }

      if (qx.dom.Node.isText(focusNode)) {
        focusNode = focusNode.parentNode;
      }

      var doc = this._getIframeDocument();
      var focusNodeStyle = (qx.core.Environment.get("engine.name") == "mshtml") ?
                           focusNode.currentStyle :
                           doc.defaultView.getComputedStyle(focusNode, null);

      var isBold = false;
      var isItalic = false;
      var isUnderline = false;
      var isStrikeThrough = false;

      var unorderedList = false;
      var orderedList = false;

      var justifyLeft = false;
      var justifyCenter = false;
      var justifyRight = false;
      var justifyFull = false;

      var fontSize = null;
      var computedFontSize = null;
      var fontFamily = null;

      if (focusNodeStyle != null)
      {
        if ((qx.core.Environment.get("engine.name") == "mshtml"))
        {
          isItalic = focusNodeStyle.fontStyle == "italic";
          isUnderline = focusNodeStyle.textDecoration.indexOf("underline") !== -1;
          isStrikeThrough = focusNodeStyle.textDecoration.indexOf("line-through") !== -1;

          fontSize = focusNodeStyle.fontSize;
          fontFamily = focusNodeStyle.fontFamily;

          justifyLeft = focusNodeStyle.textAlign == "left";
          justifyCenter = focusNodeStyle.textAlign == "center";
          justifyRight = focusNodeStyle.textAlign == "right";
          justifyFull = focusNodeStyle.textAlign == "justify";
        }
        else
        {
          isItalic = focusNodeStyle.getPropertyValue("font-style") == "italic";
          isUnderline = focusNodeStyle.getPropertyValue("text-decoration").indexOf("underline") !== -1;
          isStrikeThrough = focusNodeStyle.getPropertyValue("text-decoration").indexOf("line-through") !== -1;

          fontSize = focusNodeStyle.getPropertyValue("font-size");
          fontFamily = focusNodeStyle.getPropertyValue("font-family");

          justifyLeft = focusNodeStyle.getPropertyValue("text-align") == "left";
          justifyCenter = focusNodeStyle.getPropertyValue("text-align") == "center";
          justifyRight = focusNodeStyle.getPropertyValue("text-align") == "right";
          justifyFull = focusNodeStyle.getPropertyValue("text-align") == "justify";
        }

        if (
          qx.core.Environment.get("engine.name") == "mshtml" ||
          qx.core.Environment.get("engine.name") == "opera"
        ) {
          isBold = focusNodeStyle.fontWeight == 700;
        } else {
          isBold = focusNodeStyle.getPropertyValue("font-weight") == "bold" ||
                   qx.dom.Node.isNodeName(focusNode, "b");
        }
      }

      // Traverse the DOM to get the result, instead of using the CSS-Properties.
      // In this case the CSS-Properties are not useful, e.g. Gecko always reports
      // "disc" for "list-style-type" even if it is normal text. ("disc" is the
      // initial value)
      // Traverse the DOM upwards to determine if the focusNode is inside an
      // ordered/unordered list
      var node = focusNode;

      // only traverse the DOM upwards if were are not already within the body
      // element or at the top of the document
      if (node != null && node.parentNode != null &&
          !qx.dom.Node.isDocument(node.parentNode))
      {
        while (node != null && !qx.dom.Node.isNodeName(node, "body"))
        {
          var nodename = qx.dom.Node.getName(node);

          if (nodename == "ol")
          {
            orderedList = true;
            break;
          }
          else if (nodename == "ul")
          {
            unorderedList = true;
            break;
          }

          if (computedFontSize == null || computedFontSize == "") {
            computedFontSize = qx.bom.element.Attribute.get(node, 'size');
          }

          node = node.parentNode;
        }
      }

      var eventMap = {
        bold : isBold ? 1 : 0,
        italic : isItalic ? 1 : 0,
        underline : isUnderline ? 1 : 0,
        strikethrough : isStrikeThrough ? 1 : 0,
        fontSize : (computedFontSize == null) ? fontSize : computedFontSize,
        fontFamily : fontFamily,
        insertUnorderedList : unorderedList ? 1 : 0,
        insertOrderedList : orderedList ? 1 : 0,
        justifyLeft : justifyLeft ? 1 : 0,
        justifyCenter : justifyCenter ? 1 : 0,
        justifyRight : justifyRight ? 1 : 0,
        justifyFull : justifyFull ? 1 : 0
      };

      this._processingExamineCursorContext = false;

      return eventMap;
    },



    /*
     -----------------------------------------------------------------------------
     SELECTION
     -----------------------------------------------------------------------------
    */

    /**
     * Returns the current selection object
     *
     * @return {Selection} Selection object
    */
    getSelection : qx.core.Environment.select("engine.name",
    {
       "mshtml" : function() {
         return this._getIframeDocument() ? this._getIframeDocument().selection : null;
       },

       "default" : function() {
         return this._getIframeWindow() ? this._getIframeWindow().getSelection() : null;
       }
    }),


    /**
     * Helper method to check if the selection is collapsed
     *
     * @return {Boolean} collapsed status of selection
     */
    isSelectionCollapsed : qx.core.Environment.select("engine.name",
    {
      "mshtml" : function() {
        return this.getSelection() && this.getSelection().type == "None";
      },

      "default": function() {
        return this.getSelection() && this.getSelection().isCollapsed;
      }
    }),


    /**
     * Returns the currently selected text.
     *
     * @return {String} Selected plain text.
     */
    getSelectedText : qx.core.Environment.select("engine.name",
    {
      "mshtml" : function() {
        return this.getRange() ? this.getRange().text : "";
      },

      "default" : function() {
        return this.getRange() ? this.getRange().toString() : "";
      }
    }),


    /**
     * Returns the content of the actual range as text
     *
     * @TODO: need to be implemented correctly
     * @return {String} selected text
     */
    getSelectedHtml : function()
    {
      // if a selection is stored, return it.
      if (this.__storedSelectedHtml != null) {
        return this.__storedSelectedHtml;
      }

      var range = this.getRange();

      if (!range) {
        return "";
      } else {
        return this.__getRangeContents(range);
      }
    },


    /**
     * Browser-specific implementation to get the current range contents
     *
     * @param range {Range object} Native range object
     *
     * @signature function(range)
     * @return {String} range contents
     */
    __getRangeContents : qx.core.Environment.select("engine.name",
    {
      "mshtml" : function(range)
      {
        if (!range) {
          return "";
        }

        return range.item ? range.item(0).outerHTML : range.htmlText;
      },

      "default" : function(range)
      {
        var doc = this._getIframeDocument();

        if (doc && range)
        {
          try
          {
            var tmpBody = doc.createElement("body");
            tmpBody.appendChild(range.cloneContents());

            return tmpBody.innerHTML;
          }
          catch (exc)
          {
            // [BUG #3142]
            // ignore this exception: NOT_FOUND_ERR: DOM Exception 8
          }

          return range + "";
        }

        return "";
      }
    }),



    /**
     * Clears the current selection
     *
     * @return {void}
     */
    clearSelection : qx.core.Environment.select("engine.name",
    {
      "mshtml" : function()
      {
        var sel = this.getSelection();

        if (sel) {
          sel.empty();
        }
      },

      "default" : function()
      {
        var sel = this.getSelection();

        if (sel) {
          sel.collapseToStart();
        }
      }
    }),


    /**
     * Checks if the cursor is within a word boundary.
     * ATTENTION: Currently only implemented for Gecko
     *
     * @signature function()
     * @return {Boolean} within word boundary
     */
    __isSelectionWithinWordBoundary : qx.core.Environment.select("engine.name", {
      "gecko" : function()
      {
        var sel = this.getSelection();
        var focusNode = this.getFocusNode();

        // check if the caret is within a word
        return sel && this.isSelectionCollapsed() && focusNode != null &&
               qx.dom.Node.isText(focusNode) && sel.anchorOffset < focusNode.length;
      },

      "default" : function() {
        return false;
      }
    }),


    /**
     * Check the selection focus node if it's an element.
     * Used a paragraph handling - if the focus node is an
     * element it's not necessary to intercept the paragraph handling.
     *
     * ATTENTION: Currently only implemented for Gecko
     *
     * @signature function()
     * @return {Boolean} selection focus node
     */
    __isFocusNodeAnElement : qx.core.Environment.select("engine.name", {
      "gecko" : function() {
        return qx.dom.Node.isElement(this.getFocusNode());
      },

      "default" : function() {
        return false;
      }
    }),


    /*
     -----------------------------------------------------------------------------
     TEXT RANGE
     -----------------------------------------------------------------------------
    */

    /**
     * Returns the range of the current selection
     *
     * @return {Range?null} Range object or null
     */
    getRange : function() {
      return this.__createRange(this.getSelection());
    },


    /**
     * Returns a range for the current selection
     *
     * @param sel {Selection} current selection object
     *
     * @signature function(sel)
     * @return {Range?null} Range object or null if the document is not available
     */
    __createRange : qx.core.Environment.select("engine.name",
    {
      "mshtml" : function(sel)
      {
        var doc = this._getIframeDocument();

        if (sel)
        {
          try {
            return sel.createRange();
          } catch(ex) {
            return doc ? doc.body.createTextRange() : null;
          }
        } else {
          return doc ? doc.body.createTextRange() : null;
        }
       },

       "default" : function(sel)
       {
         var doc = this._getIframeDocument();

         if (sel)
         {
           try {
             return sel.getRangeAt(0);
           } catch(ex) {
             return doc ? doc.createRange() : null;
           }
         } else {
           return doc ? doc.createRange() : null;
         }
       }
    }),


    /**
     * Saves the current range to let the next command operate on this range.
     * Currently only interesting for IE browsers, since they loose the range /
     * selection whenever an element is clicked which need to have a focus (e.g.
     * a textfield widget).
     *
     * *NOTE:* the next executed command will reset this range.
     *
     * @signature function()
     * @return {void}
     */
    saveRange : qx.core.Environment.select("engine.name", {
      "mshtml" : function() {
        this.__savedRange = this.getRange();
      },

      "default" : function() {}
    }),


    /**
     * Returns the current stored range.
     *
     * @signature function()
     * @return {Range|null} range object or null
     */
    getSavedRange : qx.core.Environment.select("engine.name", {
      "mshtml" : function() {
        return this.__savedRange;
      },

      "default" : function() {}
    }),


    /**
     * Resets the current saved range.
     *
     * @signature function()
     * @return {void}
     */
    resetSavedRange : qx.core.Environment.select("engine.name", {
      "mshtml" : function() {
        this.__savedRange = null;
      },

      "default" : function() {}
    }),


    /*
      -----------------------------------------------------------------------------
      NODES
      -----------------------------------------------------------------------------
    */
    /**
     * Returns the node where the selection ends
     *
     * @signature function()
     * @return {Element?null} Focus node or null if no range is available
     */
    getFocusNode : qx.core.Environment.select("engine.name",
    {
       "mshtml" : function()
       {
         var sel = this.getSelection();
         var rng;

         switch(sel.type)
         {
           case "Text":
           case "None":
             // It seems that even for selection of type "None",
             // there _is_ a correct parent element
             rng = this.__createRange(sel);

             if (rng)
             {
               rng.collapse(false);
               return rng.parentElement();
             } else {
               return null;
             }
           break;

           case "Control":
             rng = this.__createRange(sel);

             if (rng)
             {
               try {
                 rng.collapse(false);
               } catch(ex) {}

               return rng.item(0);
             } else {
               return null;
             }
           break;

           default:
             return this._getIframeDocument().body;
         }
       },

       "default" : function()
       {
         var sel = this.getSelection();

         if (sel && sel.focusNode) {
           return sel.focusNode;
         }

         return this._getIframeDocument().body;
       }
    })
  },


  environment : {
    "qx.bom.htmlarea.HtmlArea.debug" : false
  },


  /*
  ---------------------------------------------------------------------------
    DESTRUCTOR
  ---------------------------------------------------------------------------
  */

  /**
   * Destructor
   *
   * @return {void}
   */
  destruct : function()
  {
    try
    {
      var doc = this._getIframeDocument();
      var Registration = qx.event.Registration;

      Registration.removeListener(doc.body, "keypress", this._handleKeyPress, this);
      Registration.removeListener(doc.body, "keyup", this._handleKeyUp, this);
      Registration.removeListener(doc.body, "keydown", this._handleKeyDown, this);

      var focusBlurTarget = qx.core.Environment.get("engine.name") == "webkit"
        ? this._getIframeWindow() : doc.body;
      Registration.removeListener(focusBlurTarget, "focus", this._handleFocusEvent);
      Registration.removeListener(focusBlurTarget, "blur",  this._handleBlurEvent);
      Registration.removeListener(doc, "focusout", this._handleFocusOutEvent);

      var mouseEventName = qx.core.Environment.get("engine.name") == "mshtml" ?
         "click" : "mouseup";
      Registration.removeListener(doc.body, mouseEventName, this._handleMouseUpOnBody, this);
      Registration.removeListener(doc.documentElement, mouseEventName, this._handleMouseUpOnDocument, this);
      Registration.removeListener(doc.documentElement, "contextmenu", this._handleContextMenuEvent, this);

      if ((qx.core.Environment.get("engine.name") == "mshtml"))
      {
        // Force unload of the iframe. Unload event is not fired when htmlarea is disposed.
        // Needed to dispose event manager (which is reg. on the unload event of the iframe) + to fix "no typing in text fields possible, when editor
        // has the focus and gets disposed when hidden".
        qx.bom.Iframe.setSource(this.__iframe, "about:blank");
      }
    } catch (ex) {};

    if (this.__commandManager instanceof qx.core.Object) {
      this._disposeObjects("__commandManager");
    } else {
      this.__commandManager = null;
    }


    this.__documentSkeletonParts =  this.__iframe = this.__widget = this.__stackCommandManager = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2010 1&1 Internet AG, Germany, http://www.1and1.org

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Alexander Steitz (aback)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * Available commands for the HtmlArea component
 *
 */
qx.Class.define("qx.bom.htmlarea.manager.Command",
{
  extend : qx.core.Object,

  /**
   * Constructor
   *
   * @param editorInstance {qx.bom.htmlarea.HtmlArea} editor instance
   * @return {void}
   */
  construct : function(editorInstance)
  {
    this.base(arguments);

    this.__editorInstance = editorInstance;
    this.__doc            = null;

    this._commands       = null;
    this.__populateCommandList();

    // When executing these commands, IE 6 sometimes selects the last <span> tag
    // completly by mistake. It is necessary to check if the range is still
    // collapsed after executing one of these commands.
    this.__invalidFocusCommands =
    {
      "Bold"          : true,
      "Italic"        : true,
      "Underline"     : true,
      "StrikeThrough" : true
    };

    /**
     * Computed pixel sizes for values size attribute in <font> tag
     */
    this.__fontSizeNames = [ 10, 12, 16, 18, 24, 32, 48 ];

    // In Gecko-browser hyperlinks which are based on *collapsed* selection are
    // inserted as DOM nodes. To keep track of these nodes they are equipped
    // with an unique id (-> "qx_link" + __hyperLinkId)
    this.__hyperLinkId = 0;
  },

  statics :
  {
    /**
     * Possible values for the style property background-position
     */
    __backgroundPosition : "|top|bottom|center|left|right|right top|left top|left bottom|right bottom|",

    /**
     * Possible values for the style property background-repeat
     */
    __backgroundRepeat : "repeat repeat-x repeat-y no-repeat"

  },

  members :
  {
    __doc : null,
    __editorInstance : null,
    __startTyping : false,
    __invalidFocusCommands : null,
    __fontSizeNames : null,
    __hyperLinkId : null,


    /* ****************************************************************
     *                BASIC / INITIALISATION
     * **************************************************************** */

    /**
     * Set the contentDocument on which this manager should execute
     * his commands
     *
     * @param doc {Object} contentDocument of the editor instance
     * @return {void}
     */
    setContentDocument : function(doc) {
      this.__doc = doc;
    },


    /* ****************************************************************
     *                  COMMAND PROCESSING
     * **************************************************************** */

    /**
     * Returns the commandobject of the given command name
     *
     * @param commandName {String} name of the command
     * @return {Object ? null} commandObject or null if no command is available for the given command name
     */
    getCommandObject : function(commandName)
    {
      if (this._commands[commandName]) {
        return this._commands[commandName];
      } else {
        return null;
      }
    },

    /**
     * Populate the internal "commands" object with the available commands and their settings.
     *
     * @return {void}
     */
    __populateCommandList : function()
    {
      this._commands =
      {
        bold                  : { useBuiltin : false, identifier : "Bold", method : "__setBold" },
        italic                : { useBuiltin : false, identifier : "Italic", method : "__setItalic" },
        underline             : { useBuiltin : false, identifier : "Underline", method : "__setUnderline" },
        strikethrough         : { useBuiltin : false, identifier : "StrikeThrough", method : "__setStrikeThrough" },
        fontfamily            : { useBuiltin : true, identifier : "FontName", method : null },
        fontsize              : { useBuiltin : false, identifier : "FontSize", method : "__setFontSize" },

        textcolor             : { useBuiltin : true, identifier : "ForeColor", method : null },
        textbackgroundcolor   : { useBuiltin : false, identifier : null, method : "__setTextBackgroundColor" },

        backgroundcolor       : { useBuiltin : false, identifier : null, method : "__setBackgroundColor" },
        backgroundimage       : { useBuiltin : false, identifier : null, method : "__setBackgroundImage" },

        justifyleft           : { useBuiltin : false, identifier : "JustifyLeft", method : "__setTextAlign" },
        justifyright          : { useBuiltin : false, identifier : "JustifyRight", method : "__setTextAlign" },
        justifycenter         : { useBuiltin : false, identifier : "JustifyCenter", method : "__setTextAlign" },
        justifyfull           : { useBuiltin : false, identifier : "JustifyFull", method : "__setTextAlign" },

        indent                : { useBuiltin : true, identifier : "Indent", method : null },
        outdent               : { useBuiltin : true, identifier : "Outdent", method : null },

        copy                  : { useBuiltin : true, identifier : "Copy", method : null },
        cut                   : { useBuiltin : true, identifier : "Cut", method : null },
        paste                 : { useBuiltin : true, identifier : "Paste", method : null },

        insertorderedlist     : { useBuiltin : false, identifier : "InsertOrderedList", method : "__insertList" },
        insertunorderedlist   : { useBuiltin : false, identifier : "InsertUnorderedList", method : "__insertList" },

        inserthorizontalrule  : { useBuiltin : false, identifier : "InsertHtml", method : "__insertHr" },
        insertimage           : { useBuiltin : false, identifier : "InsertImage", method : "__insertImage" },

        inserthyperlink       : { useBuiltin : false, identifier : "CreateLink", method : "__insertHyperLink" },

        selectall             : { useBuiltin : false, identifier : "SelectAll", method : "__selectAll" },
        selectedtext          : { useBuiltin : false, identifier : null, method : "__getSelectedText" },
        selectedhtml          : { useBuiltin : false, identifier : null, method : "__getSelectedHtml" },

        inserthtml            : { useBuiltin : false, identifier : "InsertHtml", method : "__insertHtml" },
        resethtml             : { useBuiltin : false, identifier : null, method : "__resetHtml" },
        gethtml               : { useBuiltin : false, identifier : null, method : "__getHtml" },
        removeformat          : { useBuiltin : true, identifier : "RemoveFormat", method : null }
      }
    },


    /**
     * Executes the given command
     *
     * @param command {String} Command to execute
     * @param value {String ? Integer ? null} Value of the command (if any)
     * @return {Boolean} Result of operation
     */
    execute : function(command, value)
    {
      if (!this.__editorInstance.isReady())
      {
        this.error("editor not ready! '"+command+"':'"+value+"'");
        return false;
      }

      // Normalize
      command = command.toLowerCase();
      value   = value != null ? value : null;

      // Check if the given command is supported
      if (this._commands[command])
      {
        var result;
        var commandObject = this._commands[command];

        // We have to make sure that the elements inside the selection are
        // inside a paragraph before executing a command. Otherwise executing
        // commands will cause problems for our paragraph handling.
        //
        // EXCEPTION: this interferes with webkit browsers at indent/outdent.
        if (!((qx.core.Environment.get("engine.name") == "webkit") &&
          (command == "indent" || command == "outdent")))
        {
          if (this.__paragraphMissing()) {
            this.__insertHelperParagraph();
          }
        }

        // Pass all useBuiltin commands right to the browser
        if (commandObject.useBuiltin) {
          result = this.__executeCommand(commandObject.identifier, false, value);
        }
        else
        {
          // Call the specialized method
          if (commandObject.method != null && this[commandObject.method]) {
            result = this[commandObject.method].call(this, value, commandObject);
          }
          else {
            this.error("The method '"+ commandObject.method +"' you calling to execute the command '"+ command +"' is not available!");
          }
        }

        this.__editorInstance.resetSavedRange();
        return result;
      }
      else {
        this.error("Command " + command + " is currently not supported!");
      }
    },


    /**
     * Checks if the focus node is not inside a paragraph tag.
     *
     * @return {Boolean} True if no paragraph is found, otherwise false.
     */
    __paragraphMissing : function()
    {
      var Node = qx.dom.Node;
      var focusNode = this.__editorInstance.getFocusNode();
      var insideBlockElement = false;
      var bodyIsFocusNode = false;

      if (focusNode)
      {
        if (Node.isText(focusNode))
        {
          var parents = qx.dom.Hierarchy.getAncestors(focusNode);

          for(var i=0, j=parents.length; i<j; i++)
          {
            if (Node.isNodeName(parents[i], "p") || qx.bom.htmlarea.HtmlArea.isHeadlineNode(parents[i]))
            {
              insideBlockElement = true;
              break;
            }
          }
        }
        else if (Node.isNodeName(focusNode, "body")) {
          bodyIsFocusNode = true;
        }
      }

      return bodyIsFocusNode || !insideBlockElement;
    },


    /**
     * Inserts a paragraph tag around selection or at the insert point
     * using executeCommand.
     *
     */
    __insertHelperParagraph : function() {
      this.__executeCommand("formatBlock", false, "p");
    },


    /**
     * Internal method to deal with special cases when executing commands
     *
     * @param command {String} command to execute
     * @param ui {Boolean} Whether to show an ui when executing a command. Default is false.
     * @param value {String ? Integer ? null} value of the command
     * @return {Boolean} Success of operation
     */
    __executeCommand : function(command, ui, value)
    {
      try
      {
        // The document object is the default target for all execCommands
        var execCommandTarget = this.__doc;

        // Flag indicating if range was empty before executing command. Needed for IE bug.
        var emptyRange = false;

        var range = this.__editorInstance.getRange();

        // Body element must have focus before executing command
        this.__doc.body.focus();

        // IE looses the selection if the user clicks on any other element e.g.
        // a toolbar item. To manipulate the selected text correctly IE has to
        // execute the command on the previously saved Text Range object rather
        // than the document object.
        //
        // Ignore the "SelectAll" command otherwise the range handling would
        // interfere with it.
        if ((qx.core.Environment.get("engine.name") == "mshtml"))
        {
          if(command != "selectall")
          {
            // Select the content of the Text Range object to set the cursor at the right position
            // and to give user feedback. Otherwise IE will set the cursor at the first position of the
            // editor area
            range.select();

            // If the saved Text Range object contains no text collapse it and
            // execute the command at the document object or selected range is
            // a control range with an image inside.
            if(((range.text) && (range.text.length > 0)) ||
               ((range.length == 1) && (range.item(0)) && (range.item(0).tagName == "IMG"))) {
              execCommandTarget = range;
            } else {
              execCommandTarget = this.__doc;
            }

          }

          // IE has the unwanted behavior to select text after executing some commands
          // (see this.__invalidFocusCommands).
          // If this happens, we have to collapse the range afterwards.
          if( ((qx.core.Environment.get("engine.name") == "mshtml")) && (this.__invalidFocusCommands[command]) )
          {
            if (range.text == "") {
              emptyRange = true;
            }
          }

        }

        var result = execCommandTarget.execCommand(command, ui, value);

        if (emptyRange && range.text != "") {
          range.collapse();
        }

        if ((qx.core.Environment.get("qx.debug")) &&
            qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug")) {
          this.debug("execCommand " + command + " with value " + value + " succeded");
        }

        // Mark the next insert of any char as a new undo step
        this.__startTyping = false;
      }
      catch(ex)
      {
        if ((qx.core.Environment.get("qx.debug")) &&
            qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug")) {
          this.debug("execCommand " + command + " with value " + value + " failed");
        }

        return false;
      }

      return result;
    },



    /* ************************************************************
     *          CUSTOM COMMAND IMPLEMENTATION
     * *********************************************************** */


     /**
      * Returns the range to operate on
      *
      * @signature function()
      * @return {Range} native range object
      */
     __getTargetRange : qx.core.Environment.select("engine.name", {
      "mshtml" : function()
      {
        var editor = this.__editorInstance;
        var range = editor.getSavedRange() != null ?
                    editor.getSavedRange() : editor.getRange();

        return range;
      },

      "default" : function() {
        return this.__editorInstance.getRange();
      }
     }),


     /**
      * Inserts custom HTML code at the selection point.
      *
      * @param value {String} HTML code to insert
      * @param commandObject {Object} command information
      * @return {Boolean} Success of the operation
      */
     __insertHtml : qx.core.Environment.select("engine.name",
     {
       "mshtml" : function(value, commandObject)
       {
         var result;

         // Special handling if a "br" element should be inserted
         if (value == qx.bom.htmlarea.HtmlArea.simpleLinebreak) {
           return this.__insertBrOnLinebreak();
         }
         else
         {
           this.__doc.body.focus();

           var sel   = this.__editorInstance.getSelection();
           var range = this.__getTargetRange();

           // DO NOT allow pasteHTML on control selections (like selected images)
           if(range && sel && sel.type != "Control")
           {
             // Try to pasteHTML on the stored range
             try
             {
               range.pasteHTML(value);
               range.collapse(false);
               range.select();
               result = true;
             }
             catch(e) {}
           }
           else {
             result = false;
           }

           this.__editorInstance.resetSavedRange();
           return result;
         }
       },

       "default" : function (value, commandObject)
       {
         // Body element must have focus before executing command
         this.__doc.body.focus();

         return this.__doc.execCommand(commandObject.identifier, false, value);
       }
     }),


     /**
      * Inserts a paragraph when hitting the "enter" key
      *
      * @signature function()
      * @return {Boolean} whether the key event should be stopped or not
      */
     insertParagraphOnLinebreak : qx.core.Environment.select("engine.name",
     {
       "gecko" : function()
       {
         // get the current styles as structure
         var helperStyleStructure = this.__getCurrentStylesGrouped();

         // check for styles to apply at the paragraph
         var paragraphStyle = this.__generateParagraphStyle(helperStyleStructure);

         // generate the span elements to preserve the styling
         var helperStyle = this.__generateHelperString(helperStyleStructure);

         // Generate unique ids to find the elements later
         var spanId = "__placeholder__" + Date.parse(new Date());
         var paragraphId = "__paragraph__" + Date.parse(new Date());

         // A paragraph will only be inserted, if the paragraph before it has content.
         // Therefore we also insert a helper node, then the paragraph and the style
         // nodes after it.
         var htmlToInsert = '';
         var helperString = '<span id="' + spanId + '"></span>';

         htmlToInsert += helperString;
         htmlToInsert += '<p id="' + paragraphId + '" ' + paragraphStyle + '>';
         htmlToInsert += helperStyle + '</p>';

         this.__editorInstance.getCommandManager().addUndoStep("inserthtml", "insertParagraph", this.getCommandObject("inserthtml"));

         this.execute("inserthtml", htmlToInsert);

         this.__hideSuperfluousParagraph();
         qx.bom.element.Attribute.reset(this.__doc.getElementById(spanId), "id");

         // If previous paragraph only contains helperString ->  it was empty.
         // Empty paragraphs are problematic in Gecko -> not properly rendered.
         var paragraphNode = this.__doc.getElementById(paragraphId);
         if(paragraphNode.previousSibling.innerHTML == helperString)
         {
           var helperNodeFragment = this.__generateHelperNodes();
           var brNode = this.__doc.createElement("br");

           var mozDirty = this.__doc.createAttribute("_moz_dirty");
           mozDirty.nodeValue = "";
           brNode.setAttributeNode(mozDirty);

           var type     = this.__doc.createAttribute("type");
           type.nodeValue = "_moz";
           brNode.setAttributeNode(type);

           // Insert a bogus node to set lineheight and style nodes to apply the styles.
           paragraphNode.previousSibling.appendChild(helperNodeFragment);
           paragraphNode.previousSibling.appendChild(brNode);
         }

         qx.bom.element.Attribute.reset(paragraphNode, "id");

         return true;
       },

      /**
       * Gecko does not copy the paragraph's background color, and text
       * alignment so do this manually.
       */
      "webkit" : function()
      {
        var styles = this.getCurrentStyles();
        var elementStyleString = "";

        // We need to copy the background color and text alignment for Webkit
        var relevantStyles = {
          "background-color" : true,
          "text-align": true
        };

        // Iterate over current styles and save relevant ones to string.
        for(var style in styles)
        {
          if (relevantStyles[style]) {
            elementStyleString += style + ":" + styles[style] + ";"
          }
        }

        // Insert the HTML containing the generated style string.
        this.__editorInstance.insertHtml("<p style='" + elementStyleString + "'><br class='webkit-block-placeholder' />");
      },

      "default" : qx.lang.Function.empty
      }),

      /**
        * Apply style attributes which need to to applied at paragraph (block)
        * elements instead of span (inline) elements. To avoid doubling the
        * styles this method does delete the style attribute from the data
        * structure if it can be applied at the paragraph element.
        *
        * @param currentStylesGrouped {Map} Data structure with current styles
        * @return {String} Style attributes for paragraph element
        */
      __generateParagraphStyle : qx.core.Environment.select("engine.name",
      {
        "gecko" : function(currentStylesGrouped)
        {
          var paragraphStyle = 'style="';
          var childElement = currentStylesGrouped.child;

          // text-align has to be applied to the paragraph element to get the
          // correct behaviour since it is the top block element for the text
          if (childElement["text-align"])
          {
            paragraphStyle += 'text-align:' + childElement["text-align"] + ';';
            delete currentStylesGrouped.child["text-align"];
          }

          // To fix Bug #3346 (selecting multiple paragraphs and changing the
          // font family) it is necessary to apply the font-family to the
          // paragraph element to prevent inserting any font-family style to
          // an inner "span" element which then block the font-family style
          // attribute of the "p" element (this will applied by FF when using
          // the "fontFamily" execCommand).
          if (childElement["font-family"])
          {
            paragraphStyle += 'font-family:' + childElement["font-family"] + ';';
            delete currentStylesGrouped.child["font-family"];
          }

          var paddingsToApply = {
              "padding-top" : true,
              "padding-bottom" : true,
              "padding-left" : true,
              "padding-right" : true
          };

          var marginsToApply = {
              "margin-top" : true,
              "margin-bottom" : true,
              "margin-left" : true,
              "margin-right" : true
          };

          for (var styleAttribute in childElement)
          {
            if (paddingsToApply[styleAttribute] || marginsToApply[styleAttribute])
            {
              paragraphStyle += styleAttribute + ':' + childElement[styleAttribute] + ';';
              delete currentStylesGrouped.child[styleAttribute];
            }
          }

          paragraphStyle += '"';

          return paragraphStyle;
        },

        "default" : function() {
          return "";
        }
      }),



      /**
       * Gecko inserts a superfluous paragraph despite our own paragraph
       * handling. If detected we remove this element
       *
       * @return {void}
       * @signature function()
       */
      __hideSuperfluousParagraph : qx.core.Environment.select("engine.name",
      {
        "gecko" : function()
        {
          var sel = this.__editorInstance.getSelection();

          if (!sel || !sel.focusNode) {
            return;
          }

          var focusNode = sel.focusNode;
          var traversalNode = sel.focusNode;

          while (!qx.dom.Node.isNodeName(traversalNode, "p")) {
            traversalNode = traversalNode.parentNode;
          }

          var prevSiblingId = traversalNode.previousSibling.id;
          var nextSiblingId = traversalNode.nextSibling ? traversalNode.nextSibling.id : null;

          if (qx.lang.String.startsWith(prevSiblingId, "__paragraph__") &&
              prevSiblingId == nextSiblingId)
          {
            var nextParagraph = traversalNode.nextSibling;
            var rng = this.__editorInstance.getRange();
            rng.selectNode(nextParagraph);
            sel.addRange(rng);

            var htmlToInsert = qx.bom.htmlarea.HtmlArea.EMPTY_DIV;
            this.__editorInstance.getCommandManager().addUndoStep("inserthtml", htmlToInsert, this.getCommandObject("inserthtml"));

            this.execute("inserthtml", htmlToInsert);

            var secondRange = this.__editorInstance.getRange();

            if (focusNode)
            {
              while (focusNode && focusNode.firstChild &&
                     qx.dom.Node.isElement(focusNode.firstChild))
              {
                focusNode = focusNode.firstChild;
              }
            }

            secondRange.selectNode(focusNode);

            sel.addRange(secondRange);
            secondRange.collapse(true);
          }
        },

        "default" : qx.lang.Function.empty
      }),


     /**
      * ONLY IE
      * Inserts a simple linebreak ('<br>') at the current position.
      *
      * @return {Boolean} Returns true if an br element is inserted
      */
     __insertBrOnLinebreak : qx.core.Environment.select("engine.name",
     {
       "mshtml" : function()
       {
         var rng = this.__editorInstance.getRange();

         // Only insert the "br" element if we are currently NOT inside a list.
         // Return "false" to let the browser handle this (event is not stopped).
         if (rng && !qx.dom.Node.isNodeName(rng.parentElement(), "li"))
         {
           rng.pasteHTML(qx.bom.htmlarea.HtmlArea.simpleLinebreak);
           rng.collapse(false);
           rng.select();

           return true;
         }

         return false;
       },

       "default" : function() {
         return false;
       }
     }),


     /**
      * Helper function to set a text align on a range.
      * In IE we need to explicitly get the current range before executing
      * the font size command on it.
      *
      * @param value {String} text align value
      * @param commandObject {Object} command object
      * @return {Boolean} Success of operation
      */
     __setTextAlign : function(value, commandObject)
     {
       var commandTarget = (qx.core.Environment.get("engine.name") == "mshtml") ? this.__editorInstance.getRange() : this.__doc;

       return commandTarget.execCommand(commandObject.identifier, false, value);
     },


     /**
      * Inserts a list.
      * Ensures that the list is inserted without indents. If any indents are
      * present they are removed before inserting the list.
      * This only applies for IE since other browsers are removing the indents
      * as default.
      *
      * @param value {String} list value
      * @param commandObject {Object} command object
      * @return {Boolean} Success of operation
      */
     __insertList : function(value, commandObject)
     {
       // See http://bugzilla.qooxdoo.org/show_bug.cgi?id=1608 for details
       if ((qx.core.Environment.get("engine.name") == "mshtml"))
       {
         // Get the focusNode as starting node for looking after blockquotes.
         var focusNode = this.__editorInstance.getFocusNode();
         this.__manualOutdent(focusNode);
       }

       // Body element must have focus before executing command
       this.__doc.body.focus();

       var returnValue = this.__doc.execCommand(commandObject.identifier, false, value);

       if ((qx.core.Environment.get("engine.name") == "webkit"))
       {
         // Get the parent of the current focusNode as starting node for
         // looking after blockquotes for webkit.
         var focusNode = this.__editorInstance.getFocusNode();
         this.__manualOutdent(focusNode.parentNode);
       }

       return returnValue;
     },



     /**
      * This little helper method takes a node as argument and looks along the
      * parent hierarchy for any "blockquote" elements and removes them.
      *
      * @param startNode {Node} starting point of the lookup
      * @return {void}
      */
     __manualOutdent : function(startNode)
     {
       var blockquotes = [];
       var parent = startNode.parentNode;

       while (qx.dom.Node.isNodeName(parent, "blockquote"))
       {
         blockquotes.push(parent);
         parent = parent.parentNode;
       }

       // if indents are found move the focusNode to the current parent
       // -> the first parent node which is *no* blockquote
       if (blockquotes.length > 0)
       {
         // move focus node out of blockquotes
         parent.appendChild(startNode);

         // delete blockquote nodes
         // only the last in the array is needed since the it will also remove
         // the child "blockquote" elements
         parent.removeChild(blockquotes[blockquotes.length-1]);
       }
     },



    /**
     * Inserts an image
     *
     * @param attributes {Map} map with attributes which should be applied (e.g. "src", "border", "width" and "height")
     * @param commandObject {Object} command object
     * @return {Boolean} Success of operation
     */
     __insertImage : qx.core.Environment.select("engine.name", {
       "gecko" : function(attributes, commandObject)
       {
         // Only insert an image if the src attribute info is available
         if (attributes.src)
         {
           // Insert image via the execCommand and add the attributes afterwards
           this.__doc.execCommand(commandObject.identifier, false, attributes.src);

           // source is set so remove it from the attributes map
           delete attributes.src;

           // Selection is expected to be the image node
           var sel = this.__editorInstance.getSelection();

           // TODO: need to revert the execCommand if no selection exists?
           if (sel)
           {
             var anchorNode = sel.anchorNode;
             var offset = sel.anchorOffset;
             var img = anchorNode.childNodes[offset-1];

             var attrNode;
             for (var attribute in attributes)
             {
               attrNode = this.__doc.createAttribute(attribute);
               attrNode.nodeValue = attributes[attribute];

               img.setAttributeNode(attrNode);
             }

             // Gecko does not transfer the styles of the previous sibling to the
             // element which comes right after the inserted image.
             // -> we have to insert a corresponding span element for ourselves

             // these elements can have influence on the format
             var formatElements = { "font": true,
                                    "span": true };
             var startNode = null;
             var sibling = true;

             // check if the image is one the same hierarchy level
             // IMPORTANT: if e.g. the user copy-and-pastes a text styled with
             // FONT elements Gecko does add the image inside this font element
             if (qx.dom.Node.isElement(img.previousSibling) &&
                 formatElements[qx.dom.Node.getName(img.previousSibling)]) {
               startNode = img.previousSibling;
             }
             else if (formatElements[qx.dom.Node.getName(img.parentNode)])
             {
               startNode = img.parentNode;
               sibling = false;
             }

             // documentFragment - will hold the span(s)
             var documentFragment = this.__doc.createDocumentFragment();
             var inline;

             if (sibling && startNode != null)
             {
               // if the image is a sibling of the format elements we have to
               // check for the current styles and apply them with span element(s)
               var formatElements = this.__generateImageFormatElements(startNode);

               // append the elements to the documentFragment
               documentFragment.appendChild(formatElements.root);

               // set the inline element to later insert a text node
               inline = formatElements.inline;
             }
             else
             {
               // if the image is within a e.g. "font" element or a "font"
               // element with several nested "span" elements
               // -> just add a "span" element and use the inheritance
               inline = this.__doc.createElement("span");
               documentFragment.appendChild(inline);
             }

             // the inner-most span needs a TextNode for selection
             var inlineText = this.__doc.createTextNode("");
             inline.appendChild(inlineText);

             // get the image parent node
             var imageParent = img.parentNode;

             // image is last child -> append
             // image is anywhere in between -> use nextSibling
             if (img == imageParent.lastChild) {
               imageParent.appendChild(documentFragment);
             } else {
               imageParent.insertBefore(documentFragment, img.nextSibling);
             }

             // get the current range and select the *content* of the new span
             var rng = this.__editorInstance.getRange();
             rng.selectNodeContents(inline);
           }

           return true;
         }
         else {
           return false;
         }
       },

       "mshtml" : function(attributes, commandObject)
       {
         var result = false;

         // Put together the HTML for the image
         var img = "<img ";
         for (var attrName in attributes) {
           img += attrName + "='" + attributes[attrName] + "' ";
         }
         img += "/>";

         // IE *does not* support the "insertHtml" command and
         // the "insertImage" command is not sufficient.
         // We need to add the given attributes to the image, so the
         // only working solution is to use the "pasteHTML" method of the
         // TextRange Object.
         var sel = this.__editorInstance.getSelection();
         var currRange = this.__getTargetRange();

         // DO NOT allow pasteHTML at control selections (like selected images)
         if (sel && sel.type != "Control")
         {
           try
           {
             currRange.select();
             currRange.pasteHTML(img);

             result = true;
           } catch (e) {}
         }

         this.__editorInstance.resetSavedRange();
         return result;
       },

       "default" : function(attributes, commandObject)
       {
         // Only insert an image if the src attribute info is available
         if (attributes.src)
         {
           var img = "<img ";

           for (var attrName in attributes) {
             img += attrName + "='" + attributes[attrName] + "' ";
           }

           img += "/>";

           return this.__doc.execCommand("InsertHtml", false, img);
         }
         else
         {
           return false;
         }
       }
     }),


     /**
      * Generate "span" elements to "save" the formatting after an image
      * was inserted.
      *
      * @param startNode {Node} start node for getting current styles
      * @return {Node} format elements
      */
     __generateImageFormatElements : function(startNode)
     {
       // be sure to check for childs of the previous sibling
       // e.g. a font element with a nested span inside
       while (startNode.firstChild && startNode.firstChild.nodeType == 1)
       {
         startNode = startNode.firstChild;
       }

       // get the current style of this element
       var grouped = this.__getCurrentStylesGrouped(startNode);

       var root, inlineStyle, legacyFont;
       var styles = "";
       var parent = null;
       var inline = null;
       var child = grouped.child;

       while (child)
       {
         // Since non-default font sizes are managed by "font" tags with "size"
         // attributes it is necessary to handle this in a special way
         // if a "legacy-font-size" entry is within the grouped styles it is
         // necessary to create a font element to achieve the correct format
         inline = this.__doc.createElement(child["legacy-font-size"] ? "font" : "span");

         inlineStyle = this.__doc.createAttribute("style");
         inline.setAttributeNode(inlineStyle);

         // apply the styles
         for (var styleKey in child)
         {
           if (styleKey != "child" && styleKey != "legacy-font-size")
           {
             styles += styleKey + ":" + child[styleKey] + ";";
           }
           else if (styleKey == "legacy-font-size")
           {
             legacyFont = this.__doc.createAttribute("size");
             legacyFont.nodeValue = child[styleKey];
             inline.setAttributeNode(legacyFont);
           }
         }
         inlineStyle.nodeValue = styles;

         if (parent != null) {
           parent.appendChild(inline);
         } else {
           root = inline;
         }

         parent = inline;
         child = child.child;
         styles = "";
       }

       return { root : root,
                inline : inline };
     },


     /**
      * Inserts a hyperlink. In Gecko and Opera browser these is achieved by
      * inserting DOM nodes.
      * IE is using the "CreateLink" execCommand.
      *
      * @param url {String} url to insert
      * @param commandObject {Object} command object
      * @return {Boolean} result
      */
     __insertHyperLink : qx.core.Environment.select("engine.name",
     {
       "gecko|opera" : function(url, commandObject)
       {
         var sel = this.__editorInstance.getSelection();
         var rng = this.__editorInstance.getRange();

         // If the selection is collapsed insert a link with the URL as text.
         if (sel.isCollapsed)
         {
           // Only use the link id for links which are based on a collapsed selection
           var linkId = "qx_link" + (++this.__hyperLinkId);

           // Create and insert the link as DOM nodes
           var linkNode = this.__doc.createElement("a");
           var hrefAttr = this.__doc.createAttribute("href");
           var idAttr = this.__doc.createAttribute("id");
           var linkText = this.__doc.createTextNode(url);

           idAttr.nodeValue = linkId;
           linkNode.setAttributeNode(idAttr);

           hrefAttr.nodeValue = url;
           linkNode.setAttributeNode(hrefAttr);

           linkNode.appendChild(linkText);
           rng.insertNode(linkNode);
           rng.selectNode(linkNode);

           sel.collapseToEnd();

           return true;
         }
         else {
           return this.__doc.execCommand(commandObject.identifier, false, url);
         }
       },

       "mshtml" : function(url, commandObject)
       {
         // Check for a valid text range. If it is available (=text selected)
         // insert the link via the "insertLink" execCommand otherwise insert
         // the link with the URL as link text.
         try
         {
           var result;
           var range = this.__getTargetRange();
           var editor = this.__editorInstance;
           var range = editor.getSavedRange() != null ?
                       editor.getSavedRange() : editor.getRange();

           if (range != null && range.text != "") {
             result = range.execCommand(commandObject.identifier, false, url);
           } else {
             result = this.__insertHtml(' <a href="' + url + '">' + url + '</a> ', commandObject);
           }

           this.__editorInstance.resetSavedRange();
           return result;
         }
         catch(e)
         {
           if (qx.core.Environment.get("qx.debug")) {
             this.error("inserthyperlink failed!");
           }
           return false;
         }
       },

       "default" : function(url, commandObject) {
         return this.__doc.execCommand(commandObject.identifier, false, url);
       }
     }),

     /**
      * Internal method to insert an horizontal ruler in the document
      *
      * @param value {String} empty value
      * @param commandObject {Object} command infos
      * @return {Boolean} Success of operation
      */
     __insertHr : function(value, commandObject)
     {
       var htmlText = "<hr />";

       // Gecko needs some extra HTML elements to keep the current style setting
       // after inserting the <hr> tag.
       if ((qx.core.Environment.get("engine.name") == "gecko")) {
         htmlText += this.__generateHelperString();
       }

       return this.__insertHtml(htmlText, commandObject);
     },

     /**
      * Helper function which generates a string containing HTML which can be
      * used to apply the current style to an element.
      *
      * *** ONLY IN USE FOR GECKO ***
      *
      * @param groupedStyles {Map} Data structure with grouped styles.
      *                            Structure of the "__getCurrentStylesGrouped" method.
      * @return {String} String containing tags with special style settings.
      */
     __generateHelperString : function(groupedStyles)
     {
       var formatString = "";
       var spanBegin = '<span style="';
       var closings = [];

       // retrieve the current styles as structure if no parameter is given
       var structure = typeof groupedStyles !== "undefined" ? groupedStyles : this.__getCurrentStylesGrouped();

       // first traverse the "child" chain
       var child = structure.child;
       var legacyFont = false;

       // if no styles are available no need to create an empty "span" element
       if (qx.lang.Object.isEmpty(child)) {
         return "";
       }

       while (child)
       {
         legacyFont = child["legacy-font-size"] != null;

         // Since non-default font sizes are managed by "font" tags with "size"
         // attributes it is necessary to handle this in a special way
         // if a "legacy-font-size" entry is within the grouped styles it is
         // necessary to create a font element to achieve the correct format
         formatString += legacyFont ? '<font style="' : spanBegin;
         for (var style in child) {
           formatString += (style != "child" && style != "legacy-font-size") ? style + ':' + child[style] + ';' : "";
         }
         formatString += legacyFont ? '" size="'+ child["legacy-font-size"] +'">' : '">';

         // memorize the element to close and adjust object structure
         closings.unshift(legacyFont ? "</font>" : "</span>");
         child = child.child;
       }

       // SPECIAL CASE: only one font element
       // Gecko "optimizes" this by removing the empty font element completely
       if (closings.length == 1 && closings[0] == "</font>") {
         formatString += "<span></span>";
       }

       // close the elements
       for (var i=0, j=closings.length; i<j; i++) {
         formatString += closings[i];
       }

       return formatString;
     },


     /**
      * Helper function which generates a documentFragment of <span>-tags
      * which can be used to apply the current style to an element.
      *
      * *** ONLY IN USE FOR GECKO ***
      *
      * @return {DocumentFragment} documentFragment containing styled elements
      */
     __generateHelperNodes : function()
     {
       var fragment = this.__doc.createDocumentFragment();

       // retrieve the current styles as structure
       var structure = this.__getCurrentStylesGrouped();

       // first traverse the "child" chain
       var parent = fragment;
       var child = structure.child;
       var element;
       var legacyFont = false;
       while (child)
       {
         legacyFont = child["legacy-font-size"] != null;

         element = this.__doc.createElement(legacyFont ? "font" : "span");
         parent.appendChild(element);

         // attach styles
         for (var style in child)
         {
           if (style != "child" && style != "legacy-font-size") {
             qx.bom.element.Style.set(element, style, child[style]);
           }
         }

         if (legacyFont)
         {
           var sizeAttr = this.__doc.createAttribute("size");
           sizeAttr.nodeValue = child["legacy-font-size"];
           element.setAttributeNode(sizeAttr);
         }

         parent = element;
         child = child.child;
       }

       return fragment;
     },


     /**
      * Works with the current styles and creates a hierarchy which can be
      * used to create nodes or strings out of the hierarchy.
      *
      * *** ONLY IN USE FOR GECKO ***
      *
      * @param elem {Node ? null} optional element as root node
      * @return {Map} Hierarchy with style information
      */
     __getCurrentStylesGrouped : function(elem)
     {
       var grouped = {};
       var child = null;

       var collectedStyles = this.getCurrentStyles(elem);

       child = grouped.child = {};

       for(var attribute in collectedStyles)
       {
         // "text-decoration" has to processed afterwards
         if (attribute != "text-decoration") {
           child[attribute] = collectedStyles[attribute];
         }
       }

       // Check for any text-decorations -> special handling, because one has
       // create for each text-decoration one corresponding span element to
       // ensure the correct rendering in Gecko
       if (collectedStyles["text-decoration"])
       {
         var textDecorations = collectedStyles["text-decoration"];

         // An extra <span> is needed for every text-decoration value,
         // because the color of a decoration is based on the element's color.
         for(var i=0, j=textDecorations.length; i<j; i++)
         {
           if (child == null) {
             child = grouped.child = {};
           } else {
             child = child.child = {};
           }

           child['color'] = textDecorations[i]['color'];
           child['text-decoration'] = textDecorations[i]['text-decoration'];
         }
       }

       // SPECIAL HANDLING
       // if any "text-decoration" is used it is necessary to add an extra inner
       // child to make sure an inner span is created which holds the color
       if (collectedStyles['color'] && collectedStyles['text-decoration'])
       {
         child = child.child = {};
         child['color'] = collectedStyles['color'];
       }

       return grouped;
     },


     /**
      * Internal helper function which retrieves all style settings, which are set
      * on the focus node and saves them on a span element.
      *
      * @param element {Element ? null} optional element reference the lookup should start
      * @return {Map} map with all style settings with style attributes as keys.
      */
     getCurrentStyles : function(element)
     {
       if (element == null)
       {
         var sel = this.__editorInstance.getSelection();

         if (!sel || sel.focusNode == null) {
           return {};
         }

         // Get HTML element on which the selection has ended
         element = (sel.focusNode.nodeType == 3) ? sel.focusNode.parentNode : sel.focusNode;
       }

       // Get element's ancestors to fetch all style attributes which are inherited
       // by the element to check
       var parents = qx.dom.Hierarchy.getAncestors(element);
       var elementAndParents = qx.lang.Array.insertBefore(parents, element, parents[0]);

       var collectedStyles = this.__collectStylesOfElementCollection(elementAndParents);
       var resultMap = this.__processCollectedStyles(collectedStyles, elementAndParents);

       return resultMap;
     },


     /**
      * Processes the given element collection and collects the applied CSS
      * styles. Does some additional corrections on the styles to retrieve the
      * correct values.
      *
      * @param elementCollection {Array} Array of elements to collect styles from.
      * @return {Map} collected styles in a map.
      */
     __collectStylesOfElementCollection : function(elementCollection)
     {
       var collectedStyles = {};
       var styleAttribute, element;

       for (var i=0, j=elementCollection.length; i<j; i++)
       {
         element = elementCollection[i];

         for (var k=0, l=element.style.length; k<l; k++)
         {
           styleAttribute = element.style[k];
           if (styleAttribute.length > 0 &&
               typeof collectedStyles[styleAttribute] === "undefined") {
             collectedStyles[styleAttribute] = element.style.getPropertyValue(styleAttribute);
           }
         }

         // only process the "FONT" elements to retrieve the font size
         // for the next paragraph. Only the first occurence is important.
         if(element.tagName.toUpperCase() == "FONT" && element.size &&
            collectedStyles["legacy-font-size"] === undefined) {
           collectedStyles["legacy-font-size"] = element.size;
         }
       }

       // The size of the "FONT" element has a higher priority as the CSS
       // font size value
       if (collectedStyles["legacy-font-size"] && collectedStyles["font-size"]) {
         delete collectedStyles["font-size"];
       }

       return collectedStyles;
     },


     /**
      * Walks over the collected styles and gets inherited value of each.
      *
      * @param collectedStyles {Map} All styles which should be processed
      * @param elementAndParents {Array} Array of all elements and their parent element to process
      * @return {Map} processed styles
      */
     __processCollectedStyles : function(collectedStyles, elementAndParents)
     {
       var element = elementAndParents[0];
       var elementComputedStyle = this.__editorInstance.getContentWindow().getComputedStyle(element, null);

       var styleValue;
       var resultMap = {};
       for(var style in collectedStyles)
       {
         // "legacy-font-size" is not valid CSS attribute
         // do not get the computed of it
         if (style != "legacy-font-size") {
           styleValue = elementComputedStyle.getPropertyValue(style);
         } else {
           styleValue = collectedStyles[style];
         }

         // Get the _real_ color if the collected style has the default value
         // "transparent" by retrieving it from the parent element.
         if(style == "background-color" && styleValue == "transparent") {
           resultMap[style] = this.__getBackgroundColor(elementAndParents);
         }
         // collect all "text-decoration" styles along the parent hierarchy
         // to get the correct (with all inherited values) "text-decoration" style
         else if(style == "text-decoration") {
           resultMap[style] = this.__getTextDecorations(elementAndParents);
         } else {
           resultMap[style] = styleValue;
         }
       }

       return resultMap;
     },


     /**
      * Helper function which walks over all given parent
      * elements and stores all text-decoration values and colors.
      *
      * Returns an array containing all computed values of "text-decoration"
      * and "text-color".
      *
      * @param parents {Array} List with element's parents.
      * @return {Array} List containing style information about element's parents.
      */
     __getTextDecorations : function(parents)
     {
       var decorationValue, colorValue, parentDecoration;
       var decorationValues = [];

       var editorWindow = this.__editorInstance.getContentWindow();
       for(var i=0, j=parents.length; i<j; i++)
       {
         parentDecoration = editorWindow.getComputedStyle(parents[i], null);

         decorationValue = parentDecoration.getPropertyValue("text-decoration");
         colorValue = parentDecoration.getPropertyValue("color");

         // Check if text-decoration is valid
         if (decorationValue != "none")
         {
           decorationValues.push({
             'text-decoration': decorationValue,
             'color': colorValue
           });
         }
       }

       return decorationValues;
     },


     /**
      * Helper function which walks over all given parent
      * elements and searches for an valid value for "background-color".
      *
      * Returns the computed value of "background-color" of one parent
      * element, if it contains a _real_ color.
      *
      * @param parents {Array} List with element's parents.
      * @return {String} Background color value.
      */
     __getBackgroundColor : function(parents)
     {
       var elem, parentDecoration, parentStyleValue;

       for(var i=0; i<parents.length; i++)
       {
         elem = parents[i];

         // Retrieve computed style
         parentDecoration = this.__editorInstance.getContentWindow().getComputedStyle(elem, null);
         parentStyleValue = parentDecoration.getPropertyValue("background-color");

         // If any _real_ color is found return this one
         if (parentStyleValue != "transparent") {
           return parentStyleValue;
         }

       }
     },

     /**
      * Internal method to change the font size of the selection.
      * Most of the code is used to change the size of the bullet points
      * synchronous to it's content.
      *
      * @param value {String} font size number (as used for <font> tags)
      * @param commandObject {Object} command infos
      * @return {Boolean} Success of operation
      */
     __setFontSize : function(value, commandObject)
     {
       var sel = this.__editorInstance.getSelection();

       var rng = ((qx.core.Environment.get("engine.name") == "mshtml")) ?
           this.__editorInstance.getRange() :
           rng = sel.getRangeAt(0);

       // <ol> or <ul> tags, which are selected, will be saved here
       var lists = [];

       // Flag indicating whether a whole <li> tag is selected
       var listEntrySelected;

       var listTypes = ["OL", "UL"];
       var tmp, i, j, element;

       // At first the selection is examined to figure out
       // a) whether several lists or
       // b) one single <ol> or <li> tag is selected

       // Fetch selected element node to examine what is inside the selection
       element = ((qx.core.Environment.get("engine.name") == "mshtml")) ?
           rng.parentElement() :
           rng.commonAncestorContainer;

       // If it is the <body> tag, a whole bunch of elements has been selected
       if (element.tagName == "BODY")
       {
         for (var i=0; i<listTypes.length; i++)
         {
           // Search for list elements...
           tmp = element.getElementsByTagName(listTypes[i]);
           for (var j=0; j<tmp.length; j++)
           {
             if (tmp[j]) {
               lists.push(tmp[j]);
             }
           }
         }
       }
       // A list tag has been (possibly only partly) selected
       else if(qx.lang.Array.contains(listTypes, element.tagName)) {
         lists.push(element);
       }

       if(lists.length > 0)
       {
         // Walk through all list elements and check if they are selected
         for(var i=0; i<lists.length; i++)
         {
           var listElement = lists[i];

           // Check if the entire list element has been selected.
           //
           // Note: If more than one element is selected in IE, they are all
           // selected completely. This is a good thing, since IE does not
           // support anchorOffset or nodeOffset. :-)
           listEntrySelected = ((qx.core.Environment.get("engine.name") == "mshtml")) ?
               // Element is selected or <body> tag is selected
               // (in this case, the list item inside the selection is selected, too)
               ( (listElement == element) || (element.tagName == "BODY") ) :

               // In other browsers, we can test more preciously
               sel.containsNode(listElement, false);

           /* Walk through all list entries in list element: */
           for(j=0; j<listElement.childNodes.length; j++)
           {
             var listEntryElement = listElement.childNodes[j];

             /*
              * Anchor node and focus nodes are special:
              * 1. they are always text nodes
              * 2. the selection "stops" on the text nodes, so that it's parent element is not completely selected
              *
              * For these reasons, focus node and anchor node are checked separately
              */
             if(
               /*
                * Whole list is selected
                * Note: IE will never come further than this line
                */
               listEntrySelected ||

               /* Check if the complete focus text node selected */
               (
                   sel.focusNode.nodeValue &&
                   qx.dom.Hierarchy.contains(listEntryElement, sel.focusNode) &&
                   (sel.focusOffset == sel.focusNode.nodeValue.length)
               ) ||

               /* Check if the complete anchor text node selected */
               (
                   qx.dom.Hierarchy.contains(listEntryElement, sel.anchorNode) &&
                   (sel.anchorOffset == 0)
               ) ||

               /* Otherwise, check if the actual <li> tag is completely(!) selected */
               (sel.containsNode(listEntryElement, false))
             )
             {
               /* Set font size on <li> tag */
               listEntryElement.style.fontSize = (this.__fontSizeNames[value] || value) + "px";
             } // if

           } // for
         } // for

       /* No lists are selected */
       }else{

         /* Check if element is inside a list entry */

         /* Retrieve selected element node */
         var parentElement = ((qx.core.Environment.get("engine.name") == "mshtml")) ? element : sel.focusNode;

         /* Get all parents */
         var parents = qx.dom.Hierarchy.getAncestors(parentElement);
         for(i=0; i<parents.length; i++)
         {

           /* Element is a list entry */
           if(parents[i].tagName == "LI") {

             if
             (
               (
                 ((qx.core.Environment.get("engine.name") == "gecko"))
                 &&
                 (
                   /* Selection starts at the beginning... */
                   (sel.anchorOffset == 0) &&

                   /* ... and ends at the end of list entry's content*/
                   (sel.focusNode.nodeValue && (sel.focusOffset == sel.focusNode.nodeValue.length) ) &&

                   /* Selection starts inside the list entry's first child... */
                   qx.dom.Hierarchy.contains(parents[i].firstChild, sel.anchorNode) &&

                   /* ... and ends inside the last child */
                   qx.dom.Hierarchy.contains(parents[i].lastChild, sel.focusNode)
                  )
               )
               ||
               (
                 /* In IE just check if the HTML of the range is equal to the actual list entry */
                 ((qx.core.Environment.get("engine.name") == "mshtml")) &&
                 (rng.htmlText == parents[i].innerHTML)
               )
             ){
               /* Set font size on <li> tag */
               parents[i].style.fontSize = (this.__fontSizeNames[value] || value) + "px";
             }

             /* We only need to modify the nearest <li> tag */
             break;

           } // if
         } // for

       }

       /* Execute command on selection */
       if ((qx.core.Environment.get("engine.name") == "mshtml")) {
         this.__doc.body.focus();
         this.__editorInstance.getRange().select();
         return this.__doc.execCommand("FontSize", false, value);
       }
       /*
        * Gecko uses span tags to save the style settings over block elements.
        * These span tags contain CSS which has a higher priority than the
        * font tags which are inserted via execCommand().
        * For each span tag inside the selection the CSS property has to be
        * removed to hand over the control to the font size value of execCommand().
        */
       else if((qx.core.Environment.get("engine.name") == "gecko"))
       {

         var parent = rng.commonAncestorContainer;

         /* Check if selection is a DOM element */
         if(parent.nodeType === 1)
         {
           /*
            * Remove the font size property if it is available, otherwise it
            * will interfere with the setting of the "font" element.
            * If we try to set the font size with the CSS property we will
            * have to transform the font sizes 1-7 to px values which will
            * never work out correctly.
            */
           var spans = parent.getElementsByTagName("span");
           for (i=0; i<spans.length; i++) {
             if (spans[i].style.fontSize)
             {
               spans[i].style.fontSize = null;
             }
           }
         }
       }

       return this.__doc.execCommand("FontSize", false, value);
     },


     /**
      * Internal method to set a background color for text.
      * Special implementation for Webkit to imitate the behaviour of IE.
      * If the selection is collapsed Webkit sets the background color
      * to the whole word which is currently under the caret.
      * All others (IE, Gecko and Opera) are using the execCommand directly.
      *
      * @param value {String} color to set
      * @param commandObject {Object} command infos
      * @return {Boolean} success of operation
      */
     __setTextBackgroundColor : qx.core.Environment.select("engine.name", {
       "mshtml" : function(value, commandObject)
       {
         // Body element must have focus before executing command
         this.__doc.body.focus();

         return this.__doc.execCommand("BackColor", false, value);
       },

       "gecko|opera" : function(value, commandObject)
       {
         // Body element must have focus before executing command
         this.__doc.body.focus();

         return this.__doc.execCommand("HiliteColor", false, value);
       },

       "webkit" : function(value, commandObject)
       {
         var sel = this.__editorInstance.getSelection();
         var rng = this.__editorInstance.getRange();

         // check for a range
         if (!sel || !sel.isCollapsed)
         {
           // Body element must have focus before executing command
           this.__doc.body.focus();

           this.__doc.execCommand("BackColor", false, value);

           // collapse the selection
           if (sel) {
             sel.collapseToEnd();
           }

           return true;
         }
         else
         {
           // Act like an IE browser
           // -> if the selection is collapsed select the whole word and
           // perform the action on this selection.
           var right  = sel.anchorOffset;
           var left   = sel.anchorOffset;
           var rng    = sel.getRangeAt(0);
           var anchor = sel.anchorNode;

           // Check the left side - stop at a linebreak or a space
           while (left > 0)
           {
             if (anchor.nodeValue.charCodeAt(left) == 160 ||
                 anchor.nodeValue.charCodeAt(left) == 32) {
               break;
             } else {
               left--;
             }
           }

           // Check the right side - stop at a linebreak or a space
           while (right < anchor.nodeValue.length)
           {
             if (anchor.nodeValue.charCodeAt(right) == 160 ||
                 anchor.nodeValue.charCodeAt(right) == 32) {
               break;
             } else {
               right++
             }
           }

           // Set the start and end of the range to cover the whole word
           rng.setStart(sel.anchorNode, sel.anchorNode.nodeValue.charAt(left) == " " ? left + 1 : left);
           rng.setEnd(sel.anchorNode, right);
           sel.addRange(rng);

           // Body element must have focus before executing command
           this.__doc.body.focus();

           this.__doc.execCommand("BackColor", false, value);

           // Collapse the selection
           sel.collapseToEnd();

           return true;
         }
       }
     }),

     /**
      * Internal method to set a background color for the whole document
      *
      * @param value {String} color info
      * @param commandObject {Object} command infos
      * @return {Boolean} Success of operation
      */
     __setBackgroundColor : function(value, commandObject)
     {
       value = value != null && typeof value == "string" ? value : "transparent";
       qx.bom.element.Style.set(this.__doc.body, "backgroundColor", value);

       return true;
     },


     /**
      * Sets the background image of the document
      *
      * @param value {Array} Array consisting of url [0], background-repeat [1] and background-position [2]
      * @param commandObject {Object} command infos
      * @return {Boolean} Success of operation
      */
     __setBackgroundImage : function(value, commandObject)
     {
       var url, repeat, position;
       var Command = qx.bom.htmlarea.manager.Command;

       if (value == null) {
         url = null;
       }
       else
       {
         url      = value[0];
         repeat   = value[1];
         position = value[2];
       }

       // If url is null remove the background image
       if (url == null || typeof url != "string")
       {
         qx.bom.element.Style.set(this.__doc.body, "backgroundImage", "");
         qx.bom.element.Style.set(this.__doc.body, "backgroundRepeat", "");
         qx.bom.element.Style.set(this.__doc.body, "backgroundPosition", "");

         return true;
       }

       // Normalize the url parameter. Especially when doing undo/redo operations
       // the url *can* be passed in as full CSS like 'url(SOMEURL)' rather than
       // just 'SOMEURL'.
       else
       {
         // Quick test for 'url('
         if (url.search(/^url.*\(/) == -1) {
           url = "url(" + url + ")";
         }
       }

       // Return silently if the parameter "repeat" is not valid and report
       //the error in debug mode
       if (repeat != null && !qx.lang.String.contains(Command.__backgroundRepeat, repeat))
       {
         if (qx.core.Environment.get("qx.debug")) {
           this.error("The value '" +repeat + "' is not allowed for parameter 'repeat'. Possible values are '" + Command.__backgroundRepeat + "'");
         }
         return false;
       }
       else {
         repeat = "no-repeat";
       }

       if (position != null)
       {
         if (qx.lang.Type.isString(position) &&
             !qx.lang.String.contains(Command.__backgroundPosition, '|'+position+'|'))
         {
           if (qx.core.Environment.get("qx.debug")) {
             this.error("The value '" + position + "' is not allowed for parameter 'position'. Possible values are '" + Command.__backgroundPosition + "'");
           }
           return false;
         }
         else
         {
           if (qx.lang.Type.isArray(position) && position.length == 2) {
             position = position[0] + " " + position[1];
           }
           else
           {
             if (qx.core.Environment.get("qx.debug")) {
               this.error("If an array is provided for parameter 'position' it has to define two elements!");
             }
             return false;
           }
         }
       } else {
         // Set the default value if no value is given
         position = "top";
       }

       // Don't use the "background" css property to prevent overwriting the
       // current background color
       qx.bom.element.Style.set(this.__doc.body, "backgroundImage", url);
       qx.bom.element.Style.set(this.__doc.body, "backgroundRepeat", repeat);
       qx.bom.element.Style.set(this.__doc.body, "backgroundPosition", position);

       return true;
     },


     /**
      * Selects the whole text.
      * IE uses an own implementation because the execCommand is not reliable.
      *
      * @return {Boolean} Success of operation
      */
     __selectAll : qx.core.Environment.select("engine.name", {
       "mshtml" : function(value, commandObject)
       {
         var rng = this.__doc.body.createTextRange();
         rng.select();

         return true;
       },

       "default" : function(value, commandObject)
       {
         return this.__executeCommand(commandObject.identifier, false, value);
       }
     }),


    /**
     * Returns the content of the actual range as text
     *
     * @return {String} selected text
     */
    __getSelectedText : function() {
      return this.__editorInstance.getSelectedText();
    },


    /**
     * returns the content of the actual range as text
     *
     * @return {String} selected text
     */
    __getSelectedHtml : function() {
      return this.__editorInstance.getSelectedHtml();
    },


    /**
     * Checks the formatting at the beginning of a line and resets the
     * formatting manually if necessary.
     *
     * This workaround fixes the wrong behaviour of Gecko browser which does
     * not remove the formatting itself if the cursor is at the beginning of a
     * new line and the user has not entered any text yet.
     *
     * @param attribute {String} Style attribute to check for
     * @param value {String} Style attribute value to check for
     * @return {Boolean} Whether the formatting was removed manually
     */
    __syncFormattingAtBeginOfLine : function(attribute, value)
    {
      var focusNode = this.__editorInstance.getFocusNode();
      if (focusNode.textContent == "")
      {
        // get all parent elements + including the current focus element
        var ancestors = qx.dom.Hierarchy.getAncestors(focusNode);
        ancestors.unshift(focusNode);

        var Node = qx.dom.Node;
        var Style = qx.bom.element.Style;
        var currentElement = ancestors.shift();
        while (ancestors.length > 0)
        {
          if (Node.getName(currentElement) == "p" || Node.getName(currentElement) == "body") {
            break;
          }

          // Use the local value here to get the style of the current element
          // and NOT the computed value of the element.
          if (Style.get(currentElement, attribute, Style.LOCAL_MODE) == value)
          {
            Style.reset(currentElement, attribute);
            return true;
          }

          currentElement = ancestors.shift();
        }
      }

      return false;
    },


    /**
     * Special implementation for Gecko browser to fix the wrong formatting
     * at the beginning of a new line.
     *
     * @param value {String} command value
     * @param commandObject {Object} command infos
     * @return {Boolean} Success of operation
     *
     * @signature function(value, commandObject)
     */
    __setBold : qx.core.Environment.select("engine.name",
    {
      "gecko" : function(value, commandObject)
      {
        // Checks for any "font-weight: bold" formatting and resets it
        // manually if present
        if (this.__syncFormattingAtBeginOfLine("fontWeight", "bold")) {
          return true;
        } else {
          return this.__executeCommand(commandObject.identifier, false, value);
        }
      },

      "default" : function(value, commandObject) {
        return this.__executeCommand(commandObject.identifier, false, value);
      }
    }),


    /**
     * Special implementation for Gecko browser to fix the wrong formatting
     * at the beginning of a new line.
     *
     * @param value {String} command value
     * @param commandObject {Object} command infos
     * @return {Boolean} Success of operation
     *
     * @signature function(value, commandObject)
     */
    __setItalic : qx.core.Environment.select("engine.name",
    {
      "gecko" : function(value, commandObject)
      {
        // Checks for any "font-style: italic" formatting and resets it
        // manually if present
        if (this.__syncFormattingAtBeginOfLine("fontStyle", "italic")) {
          return true;
        } else {
          return this.__executeCommand(commandObject.identifier, false, value);
        }
      },

      "default" : function(value, commandObject) {
        return this.__executeCommand(commandObject.identifier, false, value);
      }
    }),


     /**
      * Special implementation for Gecko browser to fix the wrong formatting
      * at the beginning of a new line.
      *
      * Special implementation for webkit browser to set the text-decoration
      * underline. In webkit the apply of text-decoration is different to other
      * browsers and cannot be performed with an <code>execCommand</code>
      *
      * @param value {String} color info
      * @param commandObject {Object} command infos
      * @return {Boolean} Success of operation
      * @signature function(value, commandObject)
      */
     __setUnderline  : qx.core.Environment.select("engine.name",
     {
       "gecko" : function(value, commandObject)
       {
         // Checks for any "text-decoration: underline" formatting and resets it
         // manually if present
         if (this.__syncFormattingAtBeginOfLine("textDecoration", "underline")) {
           return true;
         } else {
           return this.__executeCommand(commandObject.identifier, false, value);
         }
       },

       "default" : function(value, commandObject) {
         return this.__executeCommand(commandObject.identifier, false, value);
       }
     }),


     /**
      * Special implementation for Gecko browser to fix the wrong formatting
      * at the beginning of a new line.
      *
      * Special implementation for webkit browser to set the text-decoration
      * strikethrough. In webkit the apply of text-decoration is different to other
      * browsers and cannot be performed with an <code>execCommand</code>
      *
      * @param value {String} color info
      * @param commandObject {Object} command infos
      * @return {Boolean} Success of operation
      *
      * @signature function(value, commandObject)
      */
     __setStrikeThrough  : qx.core.Environment.select("engine.name",
     {
       "gecko" : function(value, commandObject)
       {
         // Checks for any "text-decoration: line-through" formatting and resets
         // it manually if present
         if (this.__syncFormattingAtBeginOfLine("textDecoration", "line-through")) {
           return true;
         } else {
           return this.__executeCommand(commandObject.identifier, false, value);
         }
       },

       "default" : function(value, commandObject) {
         return this.__executeCommand(commandObject.identifier, false, value);
       }
     })
  },


  /**
   * Destructor
   */
  destruct : function()
  {
    this.__doc = this.__editorInstance = this._commands = null;
    this.__invalidFocusCommands = this.__fontSizeNames = null;
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2004-2009 1&1 Internet AG, Germany, http://www.1and1.org

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Alexander Steitz (aback)
     * Jonathan Weiß (jonathan_rass)

************************************************************************ */

/**
 * Decorator for CommandManager instance to implement Undo/Redo functionality
 *
 *
 * @param commandManager {qx.bom.htmlarea.manager.Command} commandManager instance to decorate
 */
qx.Class.define("qx.bom.htmlarea.manager.UndoRedo",
{
  extend : qx.core.Object,

  /**
   * Constructor
   *
   * @param commandManager {htmlarea.command.Manager} command manager instance
   * @param editorInstance {qx.ui.embed.HtmlArea} editor instance
   * @lint ignoreDeprecated(_commands)
   * @return {void}
   */
  construct : function(commandManager, editorInstance)
  {
    this.base(arguments);

    this.__commandManager = commandManager;
    this.__editorInstance = editorInstance;

    this.__undoStack = [];
    this.__redoStack = [];
    this._commands  = null;
    this.__doc = null;
    this.__registeredHandler = {};
    this.__knownActionTypes = { command : true,
                                content : true,
                                custom : true };

    this.__populateCommandList();

    this.__handleKeyPress = qx.lang.Function.bind(this._handleKeyPress, this);
    this.__handleMouseUp = qx.lang.Function.bind(this._handleMouseUp, this);

    if ((qx.core.Environment.get("engine.name") == "mshtml")) {
      this.__handleMouseDown = qx.lang.Function.bind(this._handleMouseDown, this);
    }
  },


  /**
   * @lint ignoreDeprecated(_commands)
   */
  members :
  {
    __redoPossible : false,
    __undoPossible : false,

    __contentChange : false,

    __knownActionTypes : null,

    /* Map with infos about custom registered handler */
    __registeredHandler : null,

    __commandManager : null,
    __doc : null,
    __undoStack : null,
    __redoStack : null,
    __editorInstance : null,
    __handleKeyPress : null,
    __handleMouseUp : null,
    __handleMouseDown : null,
    __currentContent : null,


    /* *******************************************************
     *
     *                 PUBLIC METHODS
     *
     * *******************************************************/

    /**
     * Set the document instance on which the UndoManager should perform his actions.
     *
     * @param doc {Document} document node to work on
     * @return {void}
     */
    setContentDocument : function(doc)
    {
      this.__doc = doc;
      this.__commandManager.setContentDocument(doc);

      qx.event.Registration.addListener(doc.body, "keypress", this.__handleKeyPress, this);

      // monitor internal changes like image resizing etc.
      qx.event.Registration.addListener(doc.body, "mouseup", this.__handleMouseUp, this);

      if ((qx.core.Environment.get("engine.name") == "mshtml"))
      {
        // monitor internal changes like image resizing etc.
        qx.event.Registration.addListener(doc.body, "mousedown", this.__handleMouseDown, this, true);
      }
    },


    /**
     * Inserts a paragraph when hitting the "enter" key.
     * Delegates to the real command manager instance.
     *
     * @type member
     * @return {Boolean} whether the key event should be stopped or not
     */
    insertParagraphOnLinebreak : function() {
      return this.__commandManager.insertParagraphOnLinebreak();
    },


    /**
     * Executes the given command and collects (if necessary) undo information.
     *
     * @param command {String} Command to execute
     * @param value {String ? Integer ? null} Value of the command (if any)
     * @return {Boolean} Result of operation
     */
    execute : function(command, value)
    {
      var result;

      // Normalize
      command = command.toLowerCase();

      // Check for commands handled directly be this manager otherwise pass it
      // along to the command manager and collect undo infos.
      if (this._commands[command])
      {
        // Pass all commands directly to the commandManager if they marked as
        // "passthrough". This way it is possible to execute commands without
        // adding them to the undoStack.
        if (this._commands[command].passthrough) {
          result = this.__commandManager.execute(command, value);
        } else {
          result = this[command].call(this);
        }
      }
      else
      {
        if ((qx.core.Environment.get("engine.name") == "mshtml") ||
            (qx.core.Environment.get("engine.name") == "webkit"))
        {
          this.__collectUndoInfo(command, value, this.__commandManager.getCommandObject(command));

          result = this.__commandManager.execute(command, value);

          // remove last undo step from stack if command wasn't successful
          if (!result) {
            this.__undoStack.pop();
          }
        }
        else
        {
          result = this.__commandManager.execute(command, value);

          if (result) {
            this.__collectUndoInfo(command, value, this.__commandManager.getCommandObject(command));
          }
        }

        if (command == "undo" && this.__undoStack.length == 0)
        {
          this.__undoPossible = false;
          this.__fireUndoRedoStateEvent();
        }
        else if (command == "redo" && this.__redoStack.length == 0)
        {
          this.__redoPossible = false;
          this.__fireUndoRedoStateEvent();
        }
      }

      this.__contentChange = false;

      return result;
    },


    /**
     * Public API method to add an undo step
     *
     * @param command {String} Command identifier
     * @param value {String} value of command
     * @param commandObject {Object} Info object about command
     *
     * @return {void}
     */
    addUndoStep : function(command, value, commandObject) {
      this.__collectUndoInfo(command, value, commandObject);
    },


    /**
     * Register a handler for a customized actionType. This handler methods
     * (undo and redo) are called whenever the UndoManager encounters the
     * given actionType to undo/redo the change.
     *
     * @param actionType {String} actionType to react on with undo and redo methods
     * @param undoHandler {function} undo method
     * @param redoHandler {function} redo method
     * @param context {Object} In this context the methods are called. When no
     *               context is given the context is the UndoManager itself.
     *
     * @return {void}
     *
     */
    registerHandler : function(actionType, undoHandler, redoHandler, context)
    {
      this.__registeredHandler[actionType] = { undo    : undoHandler,
                                               redo    : redoHandler,
                                               context : context };
    },


    /* *******************************************************
     *
     *                  UNDO METHODS
     *
     * *******************************************************/


    /**
     * Service method to check if an undo operation is currently possible
     *
     * @return {Boolean} Whether an undo is possible or not
     */
    isUndoPossible : function() {
      return this.__undoPossible;
    },


    /**
     * Undo facade method. The different types of undo (command/custom/content)
     * are delegated to their specialized implementation.
     *
     * @return {Boolean}
     */
    undo : function()
    {
       var result;

       if (this.__contentChange) {
         this.__addAdditionalContentUndoStep();
       }

       // Look after the change history
       // if any custom change was found undo it manually
       if (this.__undoStack.length > 0)
       {
         var undoStep = this.__undoStack.pop();

         if (this.__knownActionTypes[undoStep.actionType.toLowerCase()])
         {
           switch(undoStep.actionType)
           {
             case "Command":
               result = this.__undoCommand(undoStep);
               break;

             case "Content":
               result = this.__undoContent(undoStep);
               break;

             case "Internal":
               result = this.__undoInternal(undoStep);
               break;

             case "Custom":
               result = this.__undoCustom(undoStep);
               break;
           }
         }
         // Any there any handlers which are registered to this actionType?
         else if(this.__registeredHandler[undoStep.actionType])
         {
            var handler = this.__registeredHandler[undoStep.actionType];
            result = handler.undo.call(handler.context ? handler.context : this, undoStep);

            // add it automatically to the redoStack
            this.__addToRedoStack(undoStep);
         }
         else {
           this.error("actionType " + undoStep.actionType + " is not managed! Please provide a handler method!");
         }

         this.__redoPossible = true;
         this.__fireUndoRedoStateEvent();

         return result;
       }
     },


     /**
      * Undo a custom command like setting a backgroumd image/color. These commands
      * are not supported by the browsers with an execCommand identifier. The command
      * has to be executed manually and therefore the undo mechanism.
      *
      * @param undoInfo {Object} Undo info object
      * @return {Boolean}
      */
    __undoCustom : qx.core.Environment.select("engine.name", {
      "mshtml|webkit" : function(undoInfo)
      {
        var currentContent = this.__doc.body.innerHTML;

        var oldContent = undoInfo.content;
        this.__doc.body.innerHTML = oldContent;

        var redoAction = undoInfo;
        redoAction.content = currentContent;
        this.__addToRedoStack(redoAction);

        return true;
      },

      "default" : function(undoInfo)
      {
        // Fill the info for the (possible) redo
        var redoAction = undoInfo;
        var Style = qx.bom.element.Style;

        // Add the (different) needed parameter for the redo
        switch(undoInfo.command)
        {
          case "backgroundcolor":
            redoAction.parameter = [ Style.get(this.__doc.body, "backgroundColor") ];
          break;

          case "backgroundimage":
            redoAction.parameter = [ Style.get(this.__doc.body, "backgroundImage"),
                                     Style.get(this.__doc.body, "backgroundRepeat"),
                                     Style.get(this.__doc.body, "backgroundPosition") ];
          break;
        }

        this.__addToRedoStack(redoAction);

        // Remove the link manually
        // Only necessary if the link was inserted at a collapsed selection
        if (undoInfo.command == "inserthyperlink")
        {
          if ((qx.core.Environment.get("engine.name") == "gecko"))
          {
            var linkId = "qx_link" + this.__commandManager.__hyperLinkId;
            var link = this.__doc.getElementById(linkId);

            if (link)
            {
              link.parentNode.removeChild(link);

              return true;
            }
            else {
              return false;
            }
          }
        }
        else {
          return this.__commandManager.execute(undoInfo.command, undoInfo.value);
        }
      }
    }),


    /**
     * Undo a browser-supported command.
     *
     * @param undoInfo {Object} Undo info object
     * @return {Boolean}
     * @signature function(undoInfo)
     */
    __undoCommand : qx.core.Environment.select("engine.name", {
      "mshtml|webkit" : function(undoInfo) {},

      "default" : function(undoInfo)
      {
        this.__addToRedoStack(undoInfo);

        if ((qx.core.Environment.get("engine.name") == "gecko"))
        {
          if (undoInfo.command == "inserthtml" &&
              undoInfo.value == qx.bom.htmlarea.HtmlArea.EMPTY_DIV &&
              this.__checkForNextUndoStep("inserthtml", "insertParagraph"))
          {
            this.__executeExtraUndoStep();
          }
        }

        return this.__performUndo();
      }
    }),


    /**
     * Checks the next undo step with specific conditions
     *
     * @type member
     * @param command {String} command name
     * @param value {String} command value
     * @return {Boolean} Whether a next undo step is available
     */
    __checkForNextUndoStep : function(command, value)
    {
      if (this.__undoStack.length > 0)
      {
        var nextUndoStep = this.__undoStack[this.__undoStack.length-1];
        return (nextUndoStep.command == command &&
                nextUndoStep.value == value);
      }

      return false;
    },


    /**
     * Sometimes it's necessary to perform two undo steps. Helper method to
     * to keep the stacks in correct state.
     *
     * @type member
     * @return {void}
     */
    __executeExtraUndoStep : function()
    {
      this.__performUndo();

      if (this.__undoStack.length > 0)
      {
        var nextUndoStep = this.__undoStack.pop();
        this.__addToRedoStack(nextUndoStep);
      }
    },


    /**
     * Undo an internal change like resizing an image/add table cell
     *
     * @param undoInfo {Object} Undo info object
     * @return {Boolean} Success of command
     */
    __undoInternal : function(undoInfo)
    {
      this.__addToRedoStack(undoInfo);

      return this.__performUndo();
    },


    /**
     * Undo content manipulation.
     *
     * @param undoInfo {Object} Undo info object
     * @return {Boolean}
     * @signature function(undoInfo)
     */
    __undoContent : qx.core.Environment.select("engine.name", {
      "gecko" : function(undoInfo)
      {
        this.__addToRedoStack(undoInfo);

        try {
          return this.__performUndo();
        }
        catch(error)
        {
          /* It appears, that an execCommand was bound to an element which is not available when calling 'undo' */
          if (qx.core.Environment.get("qx.debug")) {
            this.error("execCommand failed! Details: " + error)
          }
        }
      },

      "mshtml|webkit" : function(undoInfo) {},

      "default" : function(undoInfo)
      {
        this.__addToRedoStack(undoInfo);

        return this.__performUndo();
      }
    }),


    /**
     * Wrapper method for undo execCommand to prevent any exceptions bubbling
     * up to the user.
     *
     * @return {Boolean} Success of execCommand
     */
    __performUndo : function()
    {
      try {
        return this.__doc.execCommand("Undo", false, null);
      } catch(e) {
        return false;
      }
    },



    /* *******************************************************
     *
     *                  REDO METHODS
     *
     * *******************************************************/

    /**
     * Service method to check if a redo operation is currently possible
     *
     * @return {Boolean} Whether redo is possible or not
     */
    isRedoPossible : function() {
      return this.__redoPossible;
    },


    /**
     * Redo facade method. The different types of redo (command/custom/content)
     * are delegated to their specialized implementation.
     *
     * @return {Boolean}
     */
     redo : function()
     {
       if (this.__redoPossible)
       {
         var result;

         // Look after the change history
         // if any custom change was found redo it manually
         if (this.__redoStack.length > 0)
         {
           var redoStep = this.__redoStack.pop();

           if (this.__knownActionTypes[redoStep.actionType.toLowerCase()])
           {
             switch(redoStep.actionType)
             {
               case "Command":
                 result = this.__redoCommand(redoStep);
                 break;

               case "Content":
                 result = this.__redoContent(redoStep);
                 break;

               case "Internal":
                 result = this.__redoInternal(redoStep);
                 break;

               case "Custom":
                 result = this.__redoCustom(redoStep);
                 break;
             }
           }
           else if(this.__registeredHandler[redoStep.actionType])
           {
              var handler = this.__registeredHandler[redoStep.actionType];
              result = handler.redo.call(handler.context ? handler.context : this, redoStep);

              // add it automatically to the undoStack
              this.__addToUndoStack(redoStep);
           }
           else
           {
             this.error("actionType " + redoStep.actionType + " is not managed! Please provide a handler method!");
           }

           this.__undoPossible = true;
           this.__fireUndoRedoStateEvent();
         }

         return result;
      }
    },


    /**
     * Redo a custom command.
     *
     * @param redoInfo {Object} Redo info object
     * @return {Boolean}
     * @signature function(redoInfo)
     */
    __redoCustom : qx.core.Environment.select("engine.name", {
      "mshtml|webkit" : function(redoInfo)
      {
        var currentContent = this.__doc.body.innerHTML;

        var newContent = redoInfo.content;
        this.__doc.body.innerHTML = newContent;

        var undoInfo = redoInfo;
        undoInfo.content = currentContent;
        this.__addToUndoStack(undoInfo);

        return true;
      },

      "default" : function(redoInfo)
      {
        this.__addToUndoStack(redoInfo);

        return this.__performRedo();
      }
    }),


    /**
     * Redo a browser-supported command.
     *
     * @param redoInfo {Object} Redo info object
     * @return {Boolean}
     * @signature function(redoInfo)
     */
    __redoCommand : qx.core.Environment.select("engine.name", {
      "mshtml|webkit" : function(redoInfo) {},

      "default" : function(redoInfo)
      {
        this.__addToUndoStack(redoInfo);

        var result = this.__performRedo();

        if ((qx.core.Environment.get("engine.name") == "gecko"))
        {
          if (this.__checkForNextRedoStep("inserthtml", qx.bom.htmlarea.HtmlArea.EMPTY_DIV))
          {
            // we need to catch the focused paragraph before the extra redo step
            var focusedParagraph = this.__getFocusedParagraph();

            this.__executeExtraRedoStep();

            if (focusedParagraph != null) {
              this.__correctCaretPositionAfterRedo(focusedParagraph);
            }
          }
        }

        return result;
      }
    }),


    /**
     * Checks the next redo step with specific conditions
     *
     * @type member
     * @param command {String} command name
     * @param value {String} command value
     * @return {Boolean} Whether a next redo step is available
     */
    __checkForNextRedoStep : function(command, value)
    {
      if (this.__redoStack.length > 0)
      {
        var nextRedoStep = this.__redoStack[this.__redoStack.length-1];
        return (nextRedoStep.command == command &&
                nextRedoStep.value == value);
      }

      return false;
    },


    /**
     * Returns the current focused paragraph or null if the no paragraph
     * is within the selection.
     *
     * @return {Element?null} P element or null
     */
    __getFocusedParagraph : function()
    {
      if (this.__editorInstance == null) {
        return null;
      }

      var selection = this.__editorInstance.getSelection();
      var focusNode = selection ? selection.focusNode : null;

      if (focusNode == null) {
        return null;
      }

      try
      {
        while (focusNode.nodeName.toLowerCase() != "p")
        {
          focusNode = focusNode.parentNode;

          if (!focusNode || qx.dom.Node.isNodeName(focusNode, "body")) {
            return null;
          }
        }
      }
      catch (exc)
      {
        return null;
      }

      if (focusNode != null && qx.dom.Node.isNodeName(focusNode, "p")) {
        return focusNode;
      } else {
        return null;
      }
    },


    /**
     * Sometimes it is necessary to perform two redo steps at once. Helper method.
     *
     * @type member
     * @return {void}
     */
    __executeExtraRedoStep : function()
    {
      var nextRedoStep = this.__redoStack.pop();
      this.__addToUndoStack(nextRedoStep);
      this.__performRedo();
    },


    /**
     * Gecko does position the caret at the wrong position after redo commands.
     * Helper method to correct this wrong behaviour.
     *
     * @return {void}
     */
    __correctCaretPositionAfterRedo : qx.core.Environment.select("engine.name", {
      "gecko" : function(currentParagraph)
      {
        if (currentParagraph == this.__editorInstance.getContentBody().lastChild) {
          return;
        }

        var nodeToSelect = currentParagraph.firstChild;
        while (nodeToSelect.firstChild) {
          nodeToSelect = nodeToSelect.firstChild;
        }

        var selection = this.__editorInstance.getSelection();
        var range = this.__editorInstance.getRange();

        if (selection && range)
        {
          range.selectNode(nodeToSelect);
          selection.addRange(range);
          range.collapse(true);
        }
      },

      "default" : qx.lang.Function.empty
    }),


    /**
     * Redo an internal change like resizing an image/add table cell
     *
     * @param redoInfo {Object} Undo info object
     * @return {Boolean} Success of command
     */
    __redoInternal : function(redoInfo)
    {
      this.__addToUndoStack(redoInfo);

      return this.__performRedo();
    },


    /**
     * Redo a content manipulation
     *
     * @param redoInfo {Object} Redo info object
     * @return {Boolean}
     * @signature function(redoInfo)
     */
    __redoContent : qx.core.Environment.select("engine.name", {
      "mshtml|webkit" : function(redoInfo) {},

      "default" : function(redoInfo)
      {
        this.__addToUndoStack(redoInfo);
        return this.__performRedo();
      }
    }),


    /**
     * Wrapper method for redo execCommand to prevent any exceptions bubbling
     * up to the user.
     *
     * @return {Boolean} Success of execCommand
     */
    __performRedo : function()
    {
      try {
        return this.__doc.execCommand("Redo", false, null);
      } catch(e) {
        return false;
      }
    },


    /* *******************************************************
     *
     *             PRIVATE UTILITY METHODS
     *
     * *******************************************************/

    /**
     * Populates the internal command list. This list determines
     * which commands are handled directly by the undo manager and
     * which commands are passed through (without added to the undo/redo
     * history).
     *
     * @return {void}
     */
    __populateCommandList : function()
    {
      this._commands = {
        undo         : { passthrough : false },
        redo         : { passthrough : false }
      };

      /*
       * Actions for which a special undo operation is needed because
       * the browser could not handle them automatically with the "undo"
       * execCommand. This is only needed for non-mshtml as IE uses his own
       * undo mechanism.
       */
      this.__commandManager.getCommandObject("backgroundcolor").customUndo = true;
      this.__commandManager.getCommandObject("backgroundimage").customUndo = true;

      if ((qx.core.Environment.get("engine.name") == "gecko")) {
        // TODO: disable the undo of links which are not created at a text selection.
        //       Check if it's applicable at all to allow inserting links without
        //       a valid text selection
        // this.__commandManager.getCommandObject("inserthyperlink").customUndo = true;
      }
    },


    /**
     * Collects the necessary info about the current action and adds this
     * info to the undo history.
     *
     * @param command {String} command to execute
     * @param value {String ? Integer ? null} Value of the command (if any)
     * @param commandObject {Object} internal commandObject
     * @return {void}
     */
    __collectUndoInfo : qx.core.Environment.select("engine.name", {
      "mshtml|webkit" : function(command, value, commandObject)
      {
        var undoObject = this.getUndoRedoObject();
        undoObject.commandObject = commandObject;
        undoObject.command = command;
        undoObject.value = value;
        undoObject.actionType = "Custom";
        undoObject.content = this.__doc.body.innerHTML;

        this.__updateUndoStack(undoObject);
      },

      "default" : function(command, value, commandObject)
      {
        if (this.__editorInstance == null) {
          return;
        }

        var undoObject = this.getUndoRedoObject();
        undoObject.commandObject = commandObject;
        undoObject.command = command;
        undoObject.value = value;
        undoObject.actionType = "Custom";

        var sel = this.__editorInstance.getSelection();

        if (commandObject.customUndo)
        {
          var parameters = [];
          switch(command)
          {
            case "backgroundcolor":
              parameters.push(qx.bom.element.Style.get(this.__doc.body, "backgroundColor"));
            break;

            case "backgroundimage":
              parameters.push(qx.bom.element.Style.get(this.__doc.body, "backgroundImage"),
                              qx.bom.element.Style.get(this.__doc.body, "backgroundRepeat"),
                              qx.bom.element.Style.get(this.__doc.body, "backgroundPosition"));
            break;

            case "inserthyperlink":
              // If the hyperlinks gets inserted on a selection treat it as a command step
              if (sel && !sel.isCollapsed) {
                undoObject.actionType = "Command";
              }
            break;
          }

          undoObject.parameter  = parameters;
        }
        else
        {
          if ((qx.core.Environment.get("engine.name") == "gecko"))
          {
            /*
             * Ignore commands which normally act on ranges if the current range
             * is collapsed, e.g. Gecko DOES NOT mark setting a collapsed range to
             * bold as an extra action.
             * However commands like inserting an ordered list or table which do not
             * need to act on a range to work should be captured.
             *
             */
            if (sel && sel.isCollapsed)
            {
              switch(command)
              {
                // TODO: create a list of all commands which DO NOT need to act on a range to perform!
                case "insertorderedlist":
                case "insertunorderedlist":
                case "justifyright":
                case "inserthtml":
                case "insertimage":
                  undoObject.actionType = "Command";
                break;

                default:
                  return;
              }
            }
            else {
              undoObject.actionType = "Command";
            }
          }
          else {
            undoObject.actionType = "Command";
          }
        }

        this.__updateUndoStack(undoObject);
      }
    }),


    /**
      * Adds the occurred changes to the undo history and
      * sets a flag for the redo action.
      *
      * @param changeInfo {Object ? String} Infos of the change.
      *                                     Either a map containing details or null for change through a command identifier
      * @return {void}
      */
     __updateUndoStack : function(changeInfo)
     {
       if (this.__contentChange) {
         this.__addAdditionalContentUndoStep();
       }

       this.__addToUndoStack(changeInfo);

       this.__redoPossible = false;
       this.__redoStack    = [];

       this.__fireUndoRedoStateEvent();
     },


     /**
      * Add additional "Content" undo step if the last is no "Content" undo step.
      */
     __addAdditionalContentUndoStep : function()
     {
       var lastUndoStep = this.__undoStack[this.__undoStack.length - 1];
       if (lastUndoStep == null || lastUndoStep.actionType != "Content")
       {
         var undoObject = this.getUndoRedoObject();
         undoObject.actionType = "Content";

         if ((qx.core.Environment.get("engine.name") == "mshtml") ||
             (qx.core.Environment.get("engine.name") == "webkit")) {
           undoObject.content = this.__currentContent;
           undoObject.actionType = "Custom";
           this.__currentContent = null;
         }

         this.__addToUndoStack(undoObject);

         this.__contentChange = false;
       }
     },


     /**
      * Helper method to get an undo object which is added to the undoStack
      *
      * @return {Object} undo object
      */
     getUndoRedoObject : function()
     {
       return {
        actionType: null,
        commandObject: null,
        command: null,
        value: null,
        parameter: null,
        range: null,
        marker: null,
        content: null
      };
     },


     /**
      * Utility method to add an entry to the undoStack.
      *
      * @param changeInfo {Object} Infos of the change
      * @return {void}
      */
     __addToUndoStack : function(changeInfo)
     {
       if ((qx.core.Environment.get("qx.debug")) &&
           qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug"))
       {
         this.debug("ADD TO UNDO STACK");
         this.debug(changeInfo.actionType + " " + changeInfo.command + " " + changeInfo.value);
       }

       this.__undoStack.push(changeInfo);
     },


     /**
      * Utility method to add an entry to the redoStack.
      *
      * @param changeInfo {Object} Infos of the change
      * @return {void}
      */
    __addToRedoStack : function(changeInfo)
    {
      if ((qx.core.Environment.get("qx.debug")) &&
          qx.core.Environment.get("qx.bom.htmlarea.HtmlArea.debug"))
      {
        this.debug("ADD TO REDO STACK");
        this.debug(changeInfo.actionType + " " + changeInfo.command + " " + changeInfo.value);
      }

      this.__redoStack.push(changeInfo);
    },


     /**
     * Key press handler for the undo manager. Only acts on specific events which
     * are important to the undo manager.
     *
     * @param e {qx.event.type.Key} key event instance
     * @return {void}
     */
    _handleKeyPress : function(e)
    {
      var keyIdentifier = e.getKeyIdentifier().toLowerCase();
      var isCtrlPressed = e.isCtrlPressed();

      switch(keyIdentifier)
      {
        case "control":
        case "shift":
        case "left":
        case "right":
        case "up":
        case "down":
        case "pageup":
        case "pagedown":
        case "home":
        case "end":
        case "enter":
          // these keys do not mark a content change by the user
        break;

        case "a":
        case "b":
        case "i":
        case "u":
        case "k":
        case "y":
        case "z":
          // hitting hotkeys do not mark a content change
          if (!isCtrlPressed) {
            this.__markContentChange();
          }
        break;

        default:
          this.__redoPossible = false;
          this.__redoStack = [];
          this.__markContentChange();
       }
    },


    /**
     * A content change which is handled as separate undo step is marked.
     *
     * @return {void}
     */
    __markContentChange : function()
    {
      if (!this.__contentChange)
      {
        this.__contentChange = true;
        this.__undoPossible = true;

        // store current content for adding it to undo stack later
        if ((qx.core.Environment.get("engine.name") == "mshtml") ||
            (qx.core.Environment.get("engine.name") == "webkit")) {
          this.__currentContent = this.__doc.body.innerHTML;
        }

        this.__fireUndoRedoStateEvent();
      }
    },


    /** Holds the selected node for comparing between mouseUp and mouseDown events */
    __selectedNode : null,


    /**
     * Mouse down handler method.
     * Currently only implemented for IE.
     * Used to track internal changes like resizing an image or a table element.
     *
     * @param e {DOM event} mouse event instance
     * @return {void}
     */
    _handleMouseDown : qx.core.Environment.select("engine.name", {
      "mshtml" : function(e)
      {
        var checkNode = e.getOriginalTarget();

        if (qx.dom.Node.isElement(checkNode) &&
            (qx.dom.Node.isNodeName(checkNode, "img") || qx.dom.Node.isNodeName(checkNode, "table")))
        {
          this.__selectedNode = { node : checkNode,
                                  content : checkNode.outerHTML};
        }
        else {
          this.__selectedNode = null;
        }
      },

      "default" : function(e) {
        return true;
      }
    }),


    /**
     * Mouse up handler method.
     * Used to track internal changes like resizing an image or a table element.
     *
     * @param e {DOM event} mouse event instance
     * @return {void}
     */
    _handleMouseUp : qx.core.Environment.select("engine.name",
    {
      "gecko" : function(e)
      {
        if (this.__editorInstance == null) {
          return;
        }

        var sel = this.__editorInstance.getSelection();

        if (!sel)
        {
          this.__selectedNode = null;
          return;
        }

        var anchorNode = sel.anchorNode;
        var checkNode = anchorNode.childNodes[sel.anchorOffset];

        // We have direct access to the currently selected node (e.g. an image)
        if (qx.dom.Node.isNodeName(checkNode, "img"))
        {
          // Store the element if is not available
          // otherwise compare the current image element with the stored one
          if (this.__selectedNode == null) {
            this.__selectedNode = checkNode.cloneNode(true);
          }
          else
          {
            if (this.__selectedNode.style.width != checkNode.style.width ||
                this.__selectedNode.style.height != checkNode.style.height)
            {
              // A change occurred -> add undo step and update the stored element
              this.__addInternalUndoStep();
              this.__selectedNode = checkNode.cloneNode(true);
            }
          }
        }
        else if (qx.dom.Node.isNodeName(anchorNode, "td") ||
                 qx.dom.Node.isNodeName(anchorNode.parentNode, "td"))
        {
          var tableNode = anchorNode.parentNode;

          while (qx.dom.Node.isNodeName(tableNode, "table")) {
            tableNode = tableNode.parentNode;
          }

          // Store the element if is not available
          // otherwise compare the current table element with the stored one
          if (this.__selectedNode == null) {
            this.__selectedNode = tableNode.cloneNode(true);
          }
          else
          {
            /*
             * Comparison is done inside a timeout method
             * to be sure that the changes (like adding a table cell)
             * to the DOM are already done.
             */
            qx.event.Timer.once(function()
            {
              // Compare width and height and innerHTML
              if (tableNode.style.width != this.__selectedNode.style.width ||
                  tableNode.style.height != this.__selectedNode.style.height ||
                  tableNode.innerHTML != this.__selectedNode.innerHTML)
              {
                // A change occurred -> add undo step and update the stored element
                this.__addInternalUndoStep();
                this.__selectedNode = tableNode.cloneNode(true);
              }
            }, this, 0);
          }
        }
        else {
          this.__selectedNode = null;
        }
      },

      "default" : function(e)
      {
        var checkNode = qx.bom.Event.getTarget(e);

        if (this.__selectedNode != null)
        {
          if (checkNode.nodeType == 1)
          {
            /* Check the clicked element otherwise check the childNodes */
            if (checkNode == this.__selectedNode.node)
            {
              if (checkNode.outerHTML != this.__selectedNode.content)
              {
                this.__selectedNode.content = checkNode.outerHTML;
                this.__addInternalUndoStep();
              }
            }
            else
            {
              for (var i=0, j=checkNode.childNodes.length; i<j; i++)
              {
                if (checkNode.childNodes[i] == this.__selectedNode.node)
                {
                  if (checkNode.childNodes[i].outerHTML != this.__selectedNode.content)
                  {
                    this.__selectedNode.content = checkNode.childNodes[i].outerHTML;
                    this.__addInternalUndoStep();
                  }
                }
              }
            }
          }
          else {
            this.__selectedNode = null;
          }
        }
      }
    }),


    /**
     * Adds an internal undo step to the undo stack.
     *
     * @return {void}
     */
    __addInternalUndoStep : qx.core.Environment.select("engine.name", {
      "mshtml|webkit" : function() {
        this.__collectUndoInfo("Internal", null, null);
      },

      "default" : function()
      {
        var undoStep = this.getUndoRedoObject();
        undoStep.actionType = "Internal";

        this.__addToUndoStack(undoStep);
      }
    }),


    /**
     * Fires the "undoRedoState" event to inform external components (like a toolbar)
     * about the current state of the undo/redo.
     * The event itself is fired from the HtmlArea instance and with a
     * timeout to not interfere with the current key event.
     *
     * @return {void}
     */
    __fireUndoRedoStateEvent : function()
    {
      qx.event.Timer.once(function(e)
      {
        // it may happen that this asynchronous function is executed during/after
        // the dispose phase.
        if (this.__editorInstance != null)
        {
          var data = {
            undo : this.isUndoPossible() ? 0 : -1,
            redo : this.isRedoPossible() ? 0 : -1
          };
          this.__editorInstance.fireDataEvent("undoRedoState", data);
        }
      }, this, 200);
    }
  },


  /**
   * Destructor
   */
  destruct : function()
  {
    try
    {
      qx.event.Registration.removeListener(this.__doc.body, "keypress", this.__handleKeyPress);
      qx.event.Registration.removeListener(this.__doc, "mouseup", this.__handleMouseUp);

      if ((qx.core.Environment.get("engine.name") == "mshtml")) {
        qx.event.Registration.removeListener(this.__doc, "mousedown", this.__handleMouseDown);
      }
    }
    catch(e) {}

    this._disposeObjects("__commandManager");
    this.__editorInstance = this.__undoStack = this.__redoStack = this._commands = this.__doc = null;
    this.__knownActionTypes = this.__registeredHandler = null;
  }
});
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
 * Add searchString property to the Simple model. The searching will always happen on the second column.
 */
qx.Class.define("combotable.SearchableModel", {
    extend : qx.ui.table.model.Simple,

    properties : {
        /**
         * The string to search for in the second column of the dataset.
         */
        searchString : {
            init     : null,
            apply    : '_applySearchString',
            nullable : true,
            check    : "String"
        },


        /**
         * Only search from the start of the string
         */
        anchorFront : {
            init  : false,
            check : "Boolean"
        },


        /**
         * Take string as a regular expression
         */
        searchAsRegEx : {
            init  : false,
            check : "Boolean"
        }
    },

    members : {
        __allRows : null,


        /**
         * Overridden from {@link qx.ui.table.model.Simple} with an additional
         * argument tucked at the end to tell the model if the input is to be
         * stored for filtering or is the output of the filtering operation.
         */
        setData : function(rowArr, clearSorting, filtered) {
            if (!filtered) {
                this.__allRows = rowArr;
            }

            this.base(arguments, rowArr, clearSorting);
        },

        /**
         * Overridden from {@link qx.ui.table.model.Simple}.
         */
        addRows : function(rowArr, startIndex, clearSorting) {
            this.setData(this.__allRows);
            this.base(arguments, rowArr, startIndex, clearSorting);
            this._applySearchString(this.getSearchString());
        },

        /**
         * Overridden from {@link qx.ui.table.model.Simple}.
         */
        setRows : function(rowArr, startIndex, clearSorting) {
            this.setData(this.__allRows);
            this.base(arguments, rowArr, startIndex, clearSorting);
            this._applySearchString(this.getSearchString());
        },



        /**
         * Overridden from {@link qx.ui.table.model.Simple}.
         */
        removeRows : function(startIndex, howMany, clearSorting) {
            this.setData(this.__allRows);
            this.base(arguments, startIndex, howMany, clearSorting);
            this._applySearchString(this.getSearchString());
        },


        /**
         * As the search string is changed, re-filter the content of the table
         *
         * @param newString {var} old string
         * @param oldString {var} new string
         * @return {void}
         */
        _applySearchString : function(newString, oldString) {
            if (oldString == newString) {
                return;
            }

            var data = [];
            var d = this.__allRows;
            var anchor = this.getAnchorFront();

            if (this.getSearchAsRegEx()) {
                var rx = new RegExp(newString, "i");

                for (var i = 0; i < d.length; i++) {
                    var idx = d[i][1].search(rx);

                    if (anchor ? idx == 0 : idx != -1) {
                        data.push(d[i]);
                    }
                }
            }
            else {
                for (var i = 0; i < d.length; i++) {
                    var idx = d[i][1].indexOf(newString);

                    if (anchor ? idx == 0 : idx != -1) {
                        data.push(d[i]);
                    }
                }
            }
            this.setData(data, true, true);
        }
    }
});
