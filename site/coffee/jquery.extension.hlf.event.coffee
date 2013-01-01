# 
# HLF Event jQuery Extension v1.0  
# Released under the MIT License  
# Written with jQuery 1.7.2  
# 
$ = jQuery
#
# jQuery Event Extension
# ======================

#
# Composed of three parts:
# 
# 1. Extend main namespace with properties to store global state.
# 2. Private functions to implement certain behaviors.
# 3. Adapting the behaviors to custom events.
#

# 
# Hover-intent
# ------------
# Basically a distance check with a delay to throttle mouse-enter. Allows for
# customization based on sensitivity to movement. Unlike the jQuery `mouseenter`
# and `mouseleave` events, these custom ones provide `pageX` and `pageY` values.
#
$.extend true, $.hlf,
  hoverIntent:
    debug: off
    sensitivity: 8
    interval: 300
    toString: (context) ->
      switch context
        when 'data' then 'hlfHoverIntent'
        when 'log'  then 'hover-intent:'
        else 'hlf.HoverIntent'
  mouse:
    x:
      current: 0
      previous: 0
    y:
      current: 0
      previous: 0

do (ns=$.hlf.hoverIntent, m=$.hlf.mouse) ->
  
  nsDat = ns.toString 'data'
  nsLog = ns.toString 'log'
  dat = (name) -> "#{nsDat}#{name}"
  log = if ns.debug is on then $.hlf.log else $.noop
  
  check = (evt) ->
    # `$t` for trigger.
    $t = $ @
    # - Get state.
    intentional = $t.data(dat()) or yes # Default to show at first.
    timer = $t.data(dat('Timer')) or { cleared: no, timeout:null }
    sensitivity = $t.data(dat('Sensitivity')) or ns.sensitivity
    interval = $t.data(dat('Interval')) or ns.interval
    # - Guard.
    if evt.type is 'mouseleave'
      if timer.cleared is no 
        # - Clear state.
        clearTimeout timer.timeout
        $t.removeData(dat('Timer')).removeData(dat())
      # - Trigger.
      $t.trigger 'truemouseleave'
      log nsLog, 'truemouseleave'
      return
    return if timer.cleared is no and timer.timeout?
    # - Test.
    timer.timeout = setTimeout ->
      # log nsLog, 'timeout'
      # - Measure and track.
      intentional = Math.abs(m.x.previous - m.x.current) + Math.abs(m.y.previous - m.y.current) > sensitivity 
      intentional = intentional
      m.x.previous = evt.pageX
      m.y.previous = evt.pageY
      if intentional is yes and evt.type is 'mouseover'
        # - Trigger.
        $t.trigger new $.Event 'truemouseenter', { pageX: m.x.current, pageY: m.y.current }
        log nsLog, 'truemouseenter'
      # - Save state.
      timer.cleared = yes
      $t.data dat(), intentional
      $t.data dat('Timer'), timer
    , interval
    # - Save state.
    timer.cleared = no
    $t.data dat('Timer'), timer
    
  
  track = (evt) ->
    m.x.current = evt.pageX
    m.y.current = evt.pageY
  
  $.event.special.truemouseenter =
    setup: (data, namespaces) ->
      $(@).on
        mouseover:  check
        mousemove:  track
    
    teardown: (data, namespaces) ->
      $(@).off
        mouseover:  check
        mousemove:  track
    
  $.event.special.truemouseleave =
    setup: (data, namespaces) ->
      $(@).on
        mouseleave: check
    
    teardown: (data, namespaces) ->
      $(@).off
        mouseleave: check
    

