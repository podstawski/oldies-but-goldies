/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Jonathan Weiß (jonathan_rass)

   ======================================================================

   This class contains code based on the following work:

   * script.aculo.us
       http://script.aculo.us/
       Version 1.8.1

     Copyright:
       (c) 2008 Thomas Fuchs

     License:
       MIT: http://www.opensource.org/licenses/mit-license.php

     Author:
       Thomas Fuchs

************************************************************************ */

/**
 * Basic class for all core and combination effects.
 */
qx.Class.define("qx.fx.Base",
{

  extend : qx.core.Object,

  /*
    *****************************************************************************
       CONSTRUCTOR
    *****************************************************************************
  */

  /**
   * @param element {Object} The DOM element
   */
  construct : function(element)
  {
    this.base(arguments);

    this.setQueue( qx.fx.queue.Manager.getInstance().getDefaultQueue() );
    this.__state = qx.fx.Base.EffectState.IDLE;

    this.__element = element;
  },


  /*
   *****************************************************************************
      EVENTS
   *****************************************************************************
   */

   events:
   {
     /**
      * This event is fired when effect starts.
      */
     "setup"  : "qx.event.type.Event",

     /**
      * This event is fired every time a frame is rendered.
      */
     "update" : "qx.event.type.Event",

     /**
      * This event is fired when effect ends.
      */
      "finish" : "qx.event.type.Event"
   },

  /*
  *****************************************************************************
     PROPERTIES
  *****************************************************************************
  */

  properties :
  {
     /**
      * Number of seconds the effect should run.
      */
     duration :
     {
       init   : 0.5,
       check  : "Number",
       apply : "_applyDuration"
     },

     /**
      * Number frames per seconds the effect should be rendered with.
      */
     fps :
     {
       init   : 100,
       check  : "Number"
     },

     /**
      * Flag indicating if effect should run parallel with others.
      */
     sync :
     {
       init   : false,
       check  : "Boolean"
     },

     /**
      * Initial value of effect-specific property (color, opacity, position, etc.).
      */
     from :
     {
       init   : 0,
       check  : "Number"
     },

     /**
      * End value of effect-specific property. When this value is reached, the effect will end.
      */
     to :
     {
       init   : 1,
       check  : "Number"
     },

     /**
      * Number of seconds the effect should wait before start.
      */
     delay :
     {
       init   : 0.0,
       check  : "Number"
     },

     /**
      * Name of queue the effect should run in.
      */
     queue :
     {
       check : "Object",
       dereference : true
     },

     /**
      * Function which modifies the effect-specific property during the transition
      * between "from" and "to" value.
      */
     transition :
     {
       init   : "linear",

       // keep this in sync with qx.fx.Transition!
       check  : ["linear", "easeInQuad", "easeOutQuad", "sinodial", "reverse", "flicker", "wobble", "pulse", "spring", "none", "full"]
     }

  },

  /*
  *****************************************************************************
     STATICS
  *****************************************************************************
  */

  statics :
  {

    /**
     * State in which each effect can be
     */
    EffectState :
    {
      IDLE      : 'idle',
      PREPARING : 'preparing',
      RUNNING   : 'running'
    }

  },


  /*
   *****************************************************************************
      MEMBERS
   *****************************************************************************
   */

  members :
  {

    __state : null,
    __currentFrame : null,
    __startOn : null,
    __finishOn : null,
    __fromToDelta : null,
    __totalTime : null,
    __totalFrames : null,
    __position : null,
    __element : null,

    /**
     * Returns the effect's DOM element
     * @return {Object} the element
     */
    _getElement : function() {
      return this.__element;
    },

    /**
     * Sets the element to be animated.
     * @param element {Object} the element
     */
    _setElement : function(element) {
      this.__element = element;
    },

    /**
     * Apply method for duration. Should be overwritten if needed.
     * @param value {Number} Current value
     * @param old {Number} Previous value
     **/
    _applyDuration : function(value, old){},

    /**
     * This internal function is used to update
     * properties before the effect starts.
     */
    init : function()
    {
      this.__state        = qx.fx.Base.EffectState.PREPARING;
      this.__currentFrame = 0;
      this.__startOn      = this.getDelay() * 1000 + (new Date().getTime());
      this.__finishOn     = this.__startOn + (this.getDuration() * 1000);
      this.__fromToDelta  = this.getTo() - this.getFrom();
      this.__totalTime    = this.__finishOn - this.__startOn;
      this.__totalFrames  = this.getFps() * this.getDuration();
    },

    /**
     * This internal function is called before
     * "beforeFinished" and before the effect
     * actually ends.
     */
    beforeFinishInternal : function(){},

    /**
     * This internal function is called before
     * the effect actually ends.
     */
    beforeFinish : function(){},

    /**
     * This internal function is called before
     * "afterFinished" and after the effect
     * actually has ended.
     */
    afterFinishInternal : function(){},

    /**
     * This internal function is called after
     * the effect actually has ended.
     */
    afterFinish : function(){},

    /**
     * This internal function is called before
     * "beforeSetup" and before the effect's
     * "setup" method gets called.
     */
    beforeSetupInternal : function(){},

    /**
     * This internal function is called before
     * the effect's "setup" method gets called.
     */
    beforeSetup : function(){},

    /**
     * This internal function is called before
     * "afterSetup" and after the effect's
     * "setup" method has been called.
     */
    afterSetupInternal : function(){},

    /**
     * This internal function is called after
     * the effect's "setup" method has been called.
     */
    afterSetup : function(){},


    /**
     * This internal function is called before
     * "beforeUpdateInternal" and each time before
     * the effect's "update" method is called.
     */
    beforeUpdateInternal : function(){},

    /**
     * This internal function is each time before
     * the effect's "update" method is called.
     */
    beforeUpdate : function(){},

    /**
     * This internal function is called before
     * "afterUpdate" and each time after
     * the effect's "update" method is called.
     */
    afterUpdateInternal : function(){},

    /**
     * This internal function is called
     * each time after the effect's "update" method is called.
     */
    afterUpdate : function(){},

    /**
     * This internal function is called before
     * "beforeStartInternal" and before the effect
     * actually starts.
     */
    beforeStartInternal : function(){},

    /**
     * This internal function is called
     * before the effect actually starts.
     */
     beforeStart : function(){},


   /**
    * This internal function is called
    * before the effect starts to configure
    * the element or prepare other effects.
    *
    * Fires "setup" event.
    *
    */
    setup : function() {
      this.fireEvent("setup");
    },


    /**
     * This internal function is called
     * each time the effect performs an
     * step of the animation.
     *
     * Sub classes will overwrite this to
     * perform the actual changes on element
     * properties.
     *
     * @param position {Number} Animation setup
     * as Number between 0 and 1.
     *
     */
    update : function(position)
    {
    },


    /**
     * This internal function is called
     * when the effect has finished.
     *
     * Fires "finish" event.
     *
     */
    finish : function()
    {
      this.fireEvent("finish");
    },


    /**
     * Starts the effect
     */
    start : function()
    {

      if (this.__state != qx.fx.Base.EffectState.IDLE) {
        // Return a value to use this in overwritten start() methods
        return false;
      }

      this.init();

      this.beforeStartInternal();
      this.beforeStart();

      if (!this.getSync()) {
        this.getQueue().add(this);
      }

      return true;
    },


    /**
     * Ends the effect
     */
    end : function()
    {

      // render with "1.0" to have an intended finish state
      this.render(1.0);
      this.cancel();

      this.beforeFinishInternal();
      this.beforeFinish();

      this.finish();

      this.afterFinishInternal();
      this.afterFinish();
    },

    /**
     * Calls update(), or invokes the effect, if not running.
     *
     * @param pos {Number} Effect's step on duration between
     * 0 (just started) and 1 (finished). The longer the duration
     * is, the lower is each step.
     *
     * Fires "update" event.
     */
    render : function(pos)
    {

      if(this.__state == qx.fx.Base.EffectState.PREPARING)
      {
        this.__state = qx.fx.Base.EffectState.RUNNING;

        this.beforeSetupInternal();
        this.beforeSetup();

        this.setup();

        this.afterSetupInternal();
        this.afterSetup();
      }

      if(this.__state == qx.fx.Base.EffectState.RUNNING)
      {

        // adjust position depending on transition function
        this.__position = qx.fx.Transition.get(this.getTransition())(pos) * this.__fromToDelta + this.getFrom();

        this.beforeUpdateInternal();
        this.beforeUpdate();

        this.update(this.__position);

        this.afterUpdateInternal();
        this.afterUpdate();

        if (this.hasListener("update")) {
          this.fireEvent("update");
        }
      }
    },


    /**
     * Invokes update() if effect's remaining duration is
     * bigger than zero, or ends the effect otherwise.
     *
     * @param timePos {Number} Effect's step on duration between
     * 0 (just started) and 1 (finished). The longer the duration
     * is, the lower is each step.
     */
    loop : function(timePos)
    {
      // check if effect should be rendered now
      if (timePos >= this.__startOn)
      {

        // check if effect effect finish
        if (timePos >= this.__finishOn) {
          this.end();
        }

        var pos   = (timePos - this.__startOn) / this.__totalTime;
        var frame = Math.round(pos * this.__totalFrames);

        // check if effect has to be drawn in this frame
        if (frame > this.__currentFrame)
        {
          this.render(pos);
          this.__currentFrame = frame;
        }

      }
    },


    /**
    * Removes effect from queue and sets state to finished.
    */
    cancel : function()
    {
      if (!this.getSync()) {
        this.getQueue().remove(this);
      }

      this.__state = qx.fx.Base.EffectState.IDLE;
    },

    /**
    * Resets the state to default.
    */
    resetState : function() {
      this.__state = qx.fx.Base.EffectState.IDLE;
    },


    /**
     * Returns whether the effect is active
     *
     * @return {Boolean} Whether the effect is active.
     */
    isActive : function() {
      return this.__state === qx.fx.Base.EffectState.RUNNING ||
             this.__state === qx.fx.Base.EffectState.PREPARING;
    }
  },


  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    this.__element = this.__state = null;
  }

});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Jonathan Weiß (jonathan_rass)

   ======================================================================

   This class contains code based on the following work:

   * script.aculo.us
       http://script.aculo.us/
       Version 1.8.1

     Copyright:
       (c) 2008 Thomas Fuchs

     License:
       MIT: http://www.opensource.org/licenses/mit-license.php

     Author:
       Thomas Fuchs

************************************************************************ */

/**
 * Manager for access to effect queues.
 */
qx.Class.define("qx.fx.queue.Manager",
{
  extend : qx.core.Object,
  type : "singleton",

  construct : function()
  {
    this.base(arguments);
    this.__instances = {};
  },

  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    __instances : null,

    /**
     * Returns existing queue by name or creates a new queue object and returns it.
     * @param queueName {String} Name of queue.
     * @return {Class} The queue object.
     */
    getQueue : function(queueName)
    {
     if(typeof(this.__instances[queueName]) == "object") {
       return this.__instances[queueName];
     } else {
       return this.__instances[queueName] = new qx.fx.queue.Queue;
     }
    },

    /**
     * Returns existing default queue or creates a new queue object and returns it.
     * @return {Class} The queue object.
     */
    getDefaultQueue : function() {
      return this.getQueue("__default");
    }

  },


  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    this._disposeMap("__instances");
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Jonathan Weiß (jonathan_rass)

   ======================================================================

   This class contains code based on the following work:

   * script.aculo.us
       http://script.aculo.us/
       Version 1.8.1

     Copyright:
       (c) 2008 Thomas Fuchs

     License:
       MIT: http://www.opensource.org/licenses/mit-license.php

     Author:
       Thomas Fuchs

************************************************************************ */

/**
 * This queue manages ordering and rendering of effects.
 */
qx.Class.define("qx.fx.queue.Queue",
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
    this.__effects = [];
  },


  /*
   *****************************************************************************
      PROPERTIES
   *****************************************************************************
   */

   properties :
   {
      /**
       * Maximal number of effects that can run simultaneously.
       */
      limit :
      {
        init   : Infinity,
        check  : "Number"
      }

   },

  /*
   *****************************************************************************
      MEMBERS
   *****************************************************************************
   */


   members :
   {

     __interval : null,
     __effects  : null,

    /**
     * This method adds the given effect to the queue and starts the timer (if necessary).
     * @param effect {Object} The effect.
     */
    add : function(effect)
    {
      var timestamp = new Date().getTime();

      effect._startOn  += timestamp;
      effect._finishOn += timestamp;

      if (this.__effects.length < this.getLimit()) {
        this.__effects.push(effect)
      } else {
        effect.resetState();
      }

      if (!this.__interval) {
        this.__interval = qx.lang.Function.periodical(this.loop, 15, this);
      }
    },

    /**
     * This method removes the given effect from the queue.
     * @param effect {Object} The effect.
     */
    remove : function(effect)
    {
      qx.lang.Array.remove(this.__effects, effect);

      if (this.__effects.length == 0)
      {
        window.clearInterval(this.__interval);
        delete this.__interval;
      }
    },

    /**
     * This method executes all effects in queue.
     */
    loop: function()
    {
      var timePos = new Date().getTime();

      for (var i=0, len=this.__effects.length; i<len; i++) {
        this.__effects[i] && this.__effects[i].loop(timePos);
      }
    }

  },


  /*
  *****************************************************************************
    DESTRUCTOR
  *****************************************************************************
  */

  destruct : function() {
    this.__effects = null;
  }

});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Jonathan Weiß (jonathan_rass)

   ======================================================================

   This class contains code based on the following work:

   * script.aculo.us
       http://script.aculo.us/
       Version 1.8.1

     Copyright:
       2008 Thomas Fuchs

     License:
       MIT: http://www.opensource.org/licenses/mit-license.php

     Author:
       Thomas Fuchs

   ----------------------------------------------------------------------

     Copyright (c) 2005-2007 Thomas Fuchs
       (http://script.aculo.us, http://mir.aculo.us)

     Permission is hereby granted, free of charge, to any person
     obtaining a copy of this software and associated documentation
     files (the "Software"), to deal in the Software without restriction,
     including without limitation the rights to use, copy, modify, merge,
     publish, distribute, sublicense, and/or sell copies of the Software,
     and to permit persons to whom the Software is furnished to do so,
     subject to the following conditions:

     The above copyright notice and this permission notice shall be
     included in all copies or substantial portions of the Software.

     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
     EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
     MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
     NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
     HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
     WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
     DEALINGS IN THE SOFTWARE.

   ======================================================================

   This class contains code based on the following work:

   * Easing equations
       http://www.robertpenner.com/easing/

     Copyright:
       2001 Robert Penner

     License:
       BSD: http://www.opensource.org/licenses/bsd-license.php

     Author:
       Robert Penner

   ----------------------------------------------------------------------

     http://www.robertpenner.com/easing_terms_of_use.html

     Copyright © 2001 Robert Penner

     All rights reserved.

     Redistribution and use in source and binary forms, with or without
     modification, are permitted provided that the following conditions
     are met:

     * Redistributions of source code must retain the above copyright
       notice, this list of conditions and the following disclaimer.
     * Redistributions in binary form must reproduce the above copyright
       notice, this list of conditions and the following disclaimer in
       the documentation and/or other materials provided with the
       distribution.
     * Neither the name of the author nor the names of contributors may
       be used to endorse or promote products derived from this software
       without specific prior written permission.

     THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
     "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
     LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
     FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
     COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
     INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
     (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
     SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
     HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
     STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
     ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
     OF THE POSSIBILITY OF SUCH DAMAGE.

************************************************************************ */

/**
 * Static class containing all transition functions.
 */
qx.Class.define("qx.fx.Transition",
{
  type : "static",

  statics :
  {
    /**
     * Maps function name to function.
     *
     * @param functionName {String} Name off the function.
     * @return {Function|Boolean} Function belonging to the name or false,
     * function does not exist
     */
    get : function(functionName)
    {
      return qx.fx.Transition[functionName] || false;
    },

    /**
     * Returns the given effect position without
     * changing it. This is the default transition
     * function for most effects.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    linear : function(pos)
    {
      return pos;
    },

    /**
     * Using this function will accelerate the effect's
     * impact exponentially.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    easeInQuad : function (pos)
    {
      return Math.pow(2, 10 * (pos - 1));
    },

    /**
     * Using this function will slow down the
     * effect's impact exponentially.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    easeOutQuad : function (pos)
    {
      return (-Math.pow(2, -10 * pos) + 1);
    },

    /**
     * Using this function will accelerate the
     * effect's impact sinusoidal.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    sinodial : function(pos)
    {
      return ( -Math.cos(pos * Math.PI) / 2 ) + 0.5;
    },

    /**
     * Using this function will reverse the
     * effect's impact.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    reverse: function(pos)
    {
      return 1 - pos;
    },

    /**
     * Using this function will alternate the
     * effect's impact between start end value.
     *
     * Looks only nice on color effects.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    flicker : function(pos)
    {
      var pos = ( (-Math.cos(pos * Math.PI) / 4) + 0.75) + Math.random() / 4;
      return pos > 1 ? 1 : pos;
    },

    /**
     * Using this function will bounce the
     * effect's impact forwards and backwards
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    wobble : function(pos)
    {
      return ( -Math.cos(pos * Math.PI * (9 * pos)) / 2) + 0.5;
    },

    /**
     * Using this function will alternate rapidly the
     * effect's impact between start end value.
     *
     * Looks only nice on color effects.
     *
     * @param pos {Number} Effect position in duration
     * @param pulses {Number} Amount of pulses
     * @return {Number} Modified effect position
     */
    pulse : function(pos, pulses)
    {

      pulses = (typeof(pulses) == "Number") ? pulses : 5;

      return (
        Math.round((pos % (1/pulses)) * pulses) == 0 ?
              Math.floor((pos * pulses * 2) - (pos * pulses * 2)) :
          1 - Math.floor((pos * pulses * 2) - (pos * pulses * 2))
        );
    },

    /**
     * Using this function will overshoot the
     * target value and then move back the impact's
     * impact.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    spring : function(pos)
    {
      return 1 - (Math.cos(pos * 4.5 * Math.PI) * Math.exp(-pos * 6));
    },

    /**
     * Using this function the effect's impact will be zero.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    none : function(pos)
    {
      return 0;
    },

    /**
     * Using this function the effect's impact will be
     * as if it has reached the end position.
     *
     * @param pos {Number} Effect position in duration
     * @return {Number} Modified effect position
     */
    full : function(pos)
    {
      return 1;
    }
  }
});
/* ************************************************************************

   qooxdoo - the new era of web development

   http://qooxdoo.org

   Copyright:
     2008 1&1 Internet AG, Germany, http://www.1und1.de

   License:
     LGPL: http://www.gnu.org/licenses/lgpl.html
     EPL: http://www.eclipse.org/org/documents/epl-v10.php
     See the LICENSE file in the project's top-level directory for details.

   Authors:
     * Jonathan Weiß (jonathan_rass)

   ======================================================================

   This class contains code based on the following work:

   * script.aculo.us
       http://script.aculo.us/
       Version 1.8.1

     Copyright:
       (c) 2008 Thomas Fuchs

     License:
       MIT: http://www.opensource.org/licenses/mit-license.php

     Author:
       Thomas Fuchs

************************************************************************ */

/**
 * Core effect "Fade"
 *
 * Fades in the specified element: it changes to opacity from a given value to
 * another. If target value is 0, it will hide the element, if value is 1, it
 * will show it using the “display” property.
 * You can toggle this behavior using the "modifyDisplay" property:
 * {@link qx.fx.effect.core.Fade#modifyDisplay}.
 */

qx.Class.define("qx.fx.effect.core.Fade",
{

  extend : qx.fx.Base,


  /*
   *****************************************************************************
      PROPERTIES
   *****************************************************************************
   */

   properties :
   {
      /**
       * Flag indicating if the CSS attribute "display"
       * should be modified by effect
       */
      modifyDisplay :
      {
        init : true,
        check : "Boolean"
      },

      /**
       * Initial opacity value.
       */
      from :
      {
        init   : 1.0,
        refine : true
      },

      /**
       * Final opacity value.
       */
      to :
      {
        init   : 0.0,
        refine : true
      }

   },


  /*
   *****************************************************************************
      MEMBERS
   *****************************************************************************
   */

  members :
  {

    update : function(position)
    {
      this.base(arguments);

      if (qx.core.Environment.get("engine.name") == "mshtml" && position == 1) {
        // For IE it't better to remove the opacity filter instead of using it.
        qx.bom.element.Opacity.reset(this._getElement());
      } else {
        qx.bom.element.Opacity.set(this._getElement(), position);
      }
    },


    beforeSetup : function()
    {
      this.base(arguments);
      var element = this._getElement();

      if ( (this.getModifyDisplay()) && (this.getTo() > 0) ){
        qx.bom.element.Style.set(element, "display", "block");
      }
      qx.bom.element.Opacity.set(element, this.getFrom());
    },


    afterFinishInternal : function()
    {
      if ( (this.getModifyDisplay()) && (this.getTo() == 0) ){
        qx.bom.element.Style.set(this._getElement(), "display", "none");
      }
    }

  }

});
