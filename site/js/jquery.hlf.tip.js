/**
 * PJP ToolTip 
 * contains PJU Hover function
 * NOTE Namespace: $('foo').myPlugin(options{p_foo:bar}) { var myPrivate; }
 * NOTE For conflicting property namespaces: plugin -> p_foo  utility -> u_foo
 * TODO window control, dynamic stems
 * TODO snap trigger, dynamic side
 * TODO dynamic dims
 * TODO x-browser
 * @param       {jQuery object}     set of matched elements
 * @see         http://cherne.net/brian/resources/jquery.hoverIntent.html
 * @requires    jQuery 1.2+
 * @package     Peng's JQuery Plugins
 * @subpackage  Peng's WordPress Frontend
 * @version     1.0
 * @author      peng@pengxwang.com
 */

(function ($) /* declaration */ // self-invoking function, jQuery passed as alias, allows chaining
{
    $.fn.pjpTip = function (options) // assign jQuery prototype custom function
    {
        var defaults = // default implementation is to replace tooltips
            { filters:              undefined
            , p_type:              'attribute'
            , p_selector_class:    'pjpTipTrigger' // TODO for other content
            , p_attr:              'title'
            , tip_class:           'jTip'
            , tip_inner_class:     'in'
            , tip_id:              'pjpTip'
            , tip_parent:          'body'
            , stem_class:          'jStem'
            , do_stem:              true
            , do_follow:            true
            , do_lock_x:            false
            , do_lock_y:            true
            , do_snap:              true
            , do_fade:              true
            , fade_in:              200
            , fade_out:             100
            , off_x:                0
            , off_y:                24
            , mouse:                8
            , interval:             300
            , timeout:              0
            };
        options = $.extend({}, defaults, options); // merge settings
        
        return this.each( function (i) // allow chaining
        {
                // trace(i);
                $this = $(this);
                new $.PJPTip(options, $this);
                
            }
        );
    }; // _end registration

    $.pjuHover = function (e, trigger) // default jQuery behavior
    {
        // trace('pjuHover');
        var p =     (e.type == "mouseover" ? e.fromElement : e.toElement)
                 || e.relatedTarget;
        while ( p && p != trigger ) 
        { 
            try { p = p.parentNode; } 
            catch (e) { p = trigger; } 
        }
        if ( p == trigger ) 
        { 
            return false; 
        }
    };
    
    $.PJPTip = function (options, $trigger) // pseudo-class; separating from plugin scope allows more access
    {    
            /* properties */
        
        var trigger = // dom element ?
            { isHover: 
                { timer:    undefined
                , state:    false
                }
            };
        var tip = // extra layer of organization
            { myContent:    undefined
            , myLeft:       undefined // in relation to <body>
            , myTop:        undefined
            , isSet:        false
            , my$:          undefined
            , my$In:        undefined
            };
        var mouse = 
            { currX:        undefined 
            , currY:        undefined
            , prevX:        undefined
            , prevY:        undefined
            , sensitivity:  options.mouse
            };
        
            /* methods */
        
        var setContent = function () /* called upon init */
        {
            // trace('setContent');
            switch (options.p_type) 
            {
                case 'attribute':
                        // get
                    tip.myContent = $trigger.attr(options.p_attr);
                    $trigger.attr(options.p_attr, ''); // prevent default in ALL cases
                        // exit cases
                    if (tip.myContent == '') { return false; } // no content
                    if (options.filters && $trigger.is(options.filters)) { return false; } // is an exception
                        // prevent children
                    $trigger.find('*[' + options.p_attr + ']').attr(options.p_attr, '');
                        // parse
                    if (tip.myContent.search(/http:\/\//) != -1) { // just a link
                        tip.myContent = tip.myContent.replace(/(http:\/\/www\.)|(http:\/\/)/i, '');
                    }
                    // TODO if alt and ie, remove
                    
                break; case 'selector': // TODO for other content
                
                    
                
                break;
            }
        };
        var checkMouse = function (pE) /* crux of smart-hover */
        {
            // trace('checkMouse');
            trigger.isHover.timer = clearTimeout(trigger.isHover.timer);
            if (   (Math.abs(mouse.prevX - mouse.currX) + Math.abs(mouse.prevY - mouse.currY) )
                 < mouse.sensitivity
            ) // if mouse is moving fast enough
            {
                trigger.isHover.state = true;
                drawTip();
            } 
            else 
            {
                cacheMouse();
                setCheck(pE);
            }
        };
        var setCheck = function (pE) /* checking helper */
        {
            // trace('setCheck');
            trigger.isHover.timer = setTimeout( 
                function () { checkMouse(pE); },  
                options.interval
            );
        };
        var cacheMouse = function (pE) /* checking helper */
        {
            // trace('cacheMouse');
            if (pE) {
                mouse.prevX = pE.pageX;
                mouse.prevY = pE.pageY;
            } 
            else 
            {
                mouse.prevX = mouse.currX;
                mouse.prevY = mouse.currY;
            }
        };
        var trackMouse = function (e) /* constantly monitors input */
        {
            // trace('trackMouse');
            mouse.currX = e.pageX;
            mouse.currY = e.pageY;
            moveTip();
        };
        var drawTip = function () /* the over function, injects html */ // only done once per event
        { 
            // trace('drawTip');
            tip.my$ = $('<div></div>')
                .appendTo(options.tip_parent)
                .html(tip.myContent)
                .attr(
                    { id: options.tip_id
                    , className: options.tip_class
                    }
                )
                ;
                // extra html
                // inner
            tip.my$In = tip.my$ // set and return
                .wrapInner('<div></div>')
                .children('div')
                    .attr('class', options.tip_inner_class)
                ;
                // options
            if (options.do_stem == true) 
            {
                tip.my$ // set and return
                    .prepend('<div></div>')
                    .children('div')
                        .not('.' + options.tip_inner_class)
                        .attr('class', options.stem_class)
                ;
            }
            if (    tip.my$In.css('max-width')
                 && parseInt(tip.my$In.width() ) >= parseInt(tip.my$In.css('max-width') )	 
            ) // implement css shortcoming if needed
            {
                tip.my$In.css('width', parseInt(tip.my$In.css('max-width')));
            }
                // set initial css position
            moveTip();
                // fade
            if (options.do_fade == true) 
            { 
                tip.my$.hide().fadeIn(options.fade_in); 
            }
                // done
            tip.isSet = true;
        };
        var moveTip = function () /* conditionally updates css properties */
        {
            // trace('moveTip');
            if (trigger.isHover.state == true && options.do_follow == true) 
            {
                if (!(options.do_lock_x == true && tip.isSet == true) ) 
                {
                    tip.myLeft = mouse.currX + options.off_x;
                }
                if (!(options.do_lock_y == true && tip.isSet == true) ) 
                {
                    tip.myTop = mouse.currY + options.off_y;
                }
                tip.my$.css(
                    { 'top':    tip.myTop
                    , 'left':   tip.myLeft
                    }
                );
            }
        };
        var clearTip = function () // only done once per event
        {
            // trace('clearTip');
                /* reset timer */
            trigger.isHover.timer = clearTimeout(trigger.isHover.timer);
            trigger.isHover.state = false;
            if (tip.isSet == true) 
            {
                tip.isSet = false;
                if (options.do_fade) 
                {
                    tip.my$.fadeOut(options.fade_out, function () { tip.my$.remove(); });
                    return;
                } 
                else 
                {
                    tip.my$.remove();
                }
            }
        };
        var handleHover = function (e) 
        {
            // trace('handleHover');
                // legacy
            var pE = $.extend({}, e);
            if ($.pjuHover &&  $.pjuHover(e, this) == false)
            {
                return false;  // ignore children onMouseOver/onMouseOut
            }
                // reset timer
            if (trigger.isHover.timer) 
            { 
                trigger.isHover.timer = clearTimeout(trigger.isHover.timer); 
            }
            if (e.type == 'mouseover') 
            {
                    // update cache
                cacheMouse(pE);
                    // update current
                $trigger.bind('mousemove', trackMouse);
                    // set timer for over procedure
                if (trigger.isHover.state == false) 
                {
                    setCheck(pE);
                }
            } 
            else if (e.type == 'mouseout') 
            {
                    // expensive event
                $trigger.unbind('mousemove', trackMouse);
                    // set timer for out procedure
                if (trigger.isHover.state == true) { 
                    trigger.isHover.timer = setTimeout(
                          function () { clearTip(); }
                        , options.timeout
                    ); 
                }
            }
            // don't prevent default; keep original hover states
        };
        
            /* procedure */
        
        if (setContent() == false) // if no content, exit
        { 
            return $trigger; 
        } 
        // drawTip();
        return $trigger.mouseover(handleHover).mouseout(handleHover);			
    }; // _end tip class
    
}
)(jQuery);