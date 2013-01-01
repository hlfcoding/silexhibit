#
# HLF Tip jQuery Plugin v2.0.1
# Released under the MIT License
# Written with jQuery 1.7.2
#
$ = jQuery
ns = $.hlf
#
# Tooltip Plugins
# ===============

#
# Tip
# ---
# Basic tooltip plugin with fading. Fades in and out based on give delays. Awake
# and asleep states can be read and are set after fade animations. This plugin
# requires css display logic for the classes. The API class has hooks; delegation
# is used instead of events due to call frequency.
#
# The tip object is shared by the input jQuery collection.
#
# Requires the `hoverIntent` special events, since they can be customized to
# accept delays and provide pageX and pageY event properties.
#
# Options:
#
# - `ms.duration`- Duration of sleep and wake animations.
# - `ms.delay` - Delay before sleeping and waking.
# - `cls.stem` - Empty to remove the stem.
# - `cls.follow` - Empty to disable cursor following.
# - `safeToggle` - Prevents orphan tips, since timers are sometimes unreliable.
#
ns.tip =
  debug: on
  toString: (context) ->
    switch context
      when 'event'  then '.hlf.tip'
      when 'data'   then 'hlfTip'
      when 'class'  then 'js-tips'
      when 'log'    then 'hlf-tip:'
      else 'hlf.tip'

  defaults: do (pre='js-tip-') ->
    ms:
      duration:
        in: 200
        out: 200
      delay:
        in: 300
        out: 300
    cursorHeight: 6
    defaultDirection: ['south', 'east']
    safeToggle: on
    autoDirection: on
    cls: (->
      cls = {}
      _.each ['inner', 'content', 'stem', 'north', 'east', 'south', 'west', 'follow', 'trigger'],
        (key) -> cls[key] = "#{pre}#{key}"
      cls.tip = 'js-tip'
      return cls
    )()

#
# Snap-Tip
# --------
# Extends the base tip and adds snapping-to-trigger-element behavior. By default
# locks into place. If one of the snap-to-axis options is turned off, the tip will
# slide along the remaining locked axis.
#
# The tip object is shared by the input jQuery collection.
#
# Options:
#
# - `snap.xSnap` - Set empty to disable snapping along x-axis. Off by default.
# - `snap.ySnap` - Set empty to disable snapping along y-axis. Off by default.
# - `snap.snap` - Set empty to disable snapping to trigger. Builds on top of
#   axis-snapping. Off by default.
#
ns.snapTip =
  debug: off
  toString: (context) ->
    switch context
      when 'event'  then '.hlf.snapTip'
      when 'data'   then 'hlfSnapTip'
      when 'class'  then 'js-snap-tips'
      when 'log'    then 'hlf-snap-tip:'
      else 'hlf.snapTip'

  defaults: do (pre='js-snap-tip-') ->
    $.extend true, {}, ns.tip.defaults,
      snap:
        toTrigger: on
        toXAxis: off
        toYAxis: off
      cls: (->
        cls =
          snap: {}
        _.each
          toXAxis:   'x-side'
          toYAxis:   'y-side'
          toTrigger: 'trigger'
        , (val, key) -> cls.snap[key] = "#{pre}#{val}"
        cls.tip = 'js-tip js-snap-tip'
        return cls
      )()

#
# Tip API
# -------
#
class Tip

  constructor: (@$ts, @o, @$ctx) ->
    _.bindAll @, '_onTriggerMouseMove', '_setBounds'
    @$tip = $ '<div>'
    @doStem = @o.cls.stem isnt ''
    @doFollow = @o.cls.follow isnt ''
    # - Toggle state: `awake`, `asleep`, `waking`, `sleeping`.
    @_state = 'asleep'
    @_$tCurrent = null
    # - Process tip
    @_render()
    @_bind()
    # - Process triggers
    @$ts.each (idx, el) =>
      $t = $ el
      $t.addClass @o.cls.trigger
      @_saveTriggerContent $t
      @_bindTrigger $t
      @_updateDirectionByTrigger $t


  # ###Protected

  _defaultHtml: ->
    do (c=@o.cls) =>
      cDir = $.trim _.reduce @o.defaultDirection, ((cls, dir) => "#{cls} #{c[dir]}"), ''
      containerClass = $.trim [c.tip, c.follow, cDir].join ' '
      stemHtml = "<div class='#{c.stem}'></div>" if @doStem is on
      # - Not using block strings b/c Docco 0.3.0 can't correctly parse them.
      html = "<div class=\"#{containerClass}\"><div class=\"#{c.inner}\">#{stemHtml}<div class='#{c.content}'>"+
             "</div></div></div>"

  _saveTriggerContent: ($t) ->
    title = $t.attr 'title'
    if title then $t.data(@_dat('Content'), title).removeAttr 'title'

  # - Link the trigger to the tip for:
  #   1. mouseenter, mouseleave (uses special events)
  #   2. mousemove
  _bindTrigger: ($t) ->
    $t.on @_evt('truemouseenter'), (evt) =>
      @_log @_nsLog, evt
      @_onTriggerMouseMove evt
    $t.on @_evt('truemouseleave'), (evt) => @sleepByTrigger $t
    if @doFollow is on
      $t.on 'mousemove', @_onTriggerMouseMove

  # - Bind to the tip on hover so the toggling makes an exception.
  _bind: () ->
    @$tip
      .on 'mouseenter', (evt) =>
        @_log @_nsLog, 'enter tip'
        if @_$tCurrent?
          @_$tCurrent.data 'hlfIsActive', yes
          @wakeByTrigger @_$tCurrent
      .on 'mouseleave', (evt) =>
        @_log @_nsLog, 'leave tip'
        if @_$tCurrent?
          @_$tCurrent.data 'hlfIsActive', no
          @sleepByTrigger @_$tCurrent
    # - Handle adapting to window resize.
    if @o.autoDirection is on
      $(window).resize _.debounce @_setBounds, 300

  # - The tip should only need to be rendered once.
  _render: () ->
    return no if @$tip.html().length
    html = @htmlOnRender()
    if not (html? and html.length) then html = @_defaultHtml()
    @$tip = $(html).addClass @o.cls.follow
    @$tip.prependTo @$ctx

  # - The tip content will change as it's being refreshed / initialized.
  _inflateByTrigger: ($t) ->
    do (c=@o.cls) =>
      dir = if $t.data(@_dat 'Direction') then $t.data(@_dat 'Direction').split(' ') else @o.defaultDirection
      @_log @_nsLog, 'update direction class', dir
      @$tip.find(".#{c.content}").text($t.data @_dat 'Content').end()
           .removeClass([c.north, c.south, c.east, c.west].join ' ')
           .addClass($.trim _.reduce dir, ((cls, dir) => "#{cls} #{c[dir]}"), '')


  # - The main toggle handler.
  _onTriggerMouseMove: (evt) ->
    return no if not evt.pageX?
    $t = if ($t = $(evt.target)) and $t.hasClass(@o.cls.trigger) then $t else $t.closest(@o.cls.trigger)
    return no if not $t.length
    @wakeByTrigger $t, evt, =>
      offset =
        top: evt.pageY
        left: evt.pageX
      offset = @offsetOnTriggerMouseMove(evt, offset, $t) or offset
      if @isDirection 'north', $t then offset.top -= @$tip.outerHeight() + @o.cursorHeight
      if @isDirection 'west',  $t then offset.left -= @$tip.outerWidth()
      if @isDirection 'south', $t then offset.top += @o.cursorHeight
      offset.top += @o.cursorHeight
      @$tip.css offset
      @_log @_nsLog, '_onTriggerMouseMove', @_state, offset

  # - Auto-direction support. Given the context boundary, choose the best
  #   direction. The data is stored with the trigger and gets accessed elsewhere.
  _updateDirectionByTrigger: ($t) ->
    return no if @o.autoDirection is off
    # - Check if adapting is needed. Adapt and store as needed.
    checkDir = (dir) =>
      if not @_bounds? then @_setBounds()
      ok = yes
      switch dir
        when 'south' then ok = (edge = tPosition.top + tHeight + size.height) and @_bounds.bottom > edge
        when 'east'  then ok = (edge = tPosition.left + size.width)  and @_bounds.right > edge
        when 'north' then ok = (edge = tPosition.top - size.height) and @_bounds.top < edge
        when 'west'  then ok = (edge = tPosition.left - size.width) and @_bounds.left < edge
      @_log @_nsLog, 'checkDir', "'#{$t.html()}'", dir, edge, size
      if not ok
        switch dir
          when 'south' then newDir[0] = 'north'
          when 'east'  then newDir[1] = 'west'
          when 'north' then newDir[0] = 'south'
          when 'west'  then newDir[1] = 'east'
        $t.data @_dat('Direction'), newDir.join ' '
    # - Prepare for checking subroutine.
    tPosition = $t.position()
    tWidth    = $t.outerWidth()
    tHeight   = $t.outerHeight()
    size      = @sizeForTrigger $t
    newDir = _.clone @o.defaultDirection
    # - Check each direction.
    checkDir dir for dir in @o.defaultDirection

  _setBounds: ->
    $ctx = if @$ctx.is('body') then $(window) else @$ctx
    @_bounds =
      top:    parseInt @$ctx.css('padding-top'), 10
      left:   parseInt @$ctx.css('padding-left'), 10
      bottom: $ctx.innerHeight()
      right:  @$ctx.innerWidth()

  # ###Public

  # Accessors
  options: -> @o
  tip: -> @$tip
  # - Does a stealth render to find tip size. The data is stored with the
  #   trigger and gets accessed elsewhere.
  sizeForTrigger: ($t, force=no) ->
    # - Try cached.
    size =
      width:  $t.data @_dat 'Width'
      height: $t.data @_dat 'Height'
    return size if size.width and size.height
    # - Otherwise new.
    @$tip.find(".#{@o.cls.content}").text($t.data @_dat 'Content').end()
      .css
        display: 'block',
        visibility: 'hidden'
    $t.data @_dat('Width'),  (size.width = @$tip.outerWidth())
    $t.data @_dat('Height'), (size.height = @$tip.outerHeight())
    @$tip.css
      display: 'none',
      visibility: 'visible'
    size

  # - Direction is actually an array.
  isDirection: (dir, $t) -> (@$tip.hasClass @o.cls[dir]) or
    ((not $t? or not $t.data @_dat 'Direction') and _.include @o.defaultDirection, dir)

  # Methods

  # - The main toggler. Takes in a callback, which is usually to update position.
  #   The toggling and main changes only happen if the delay is passed.
  #   1. Store current trigger info.
  #   2. Go directly to the position updating if no toggling is needed.
  #   3. Don't toggle if awake or waking, or if event isn't `truemouseenter`.
  #   4. If we are in the middle of sleeping, stop and speed up our waking
  #      transition.
  #   5. Update our trigger cache.
  wakeByTrigger: ($t, evt, cb) ->
    # - Check.
    triggerChanged = not $t.is @_$tCurrent
    if triggerChanged
      @_inflateByTrigger $t
      @_$tCurrent = $t
    # - Guard.
    if @_state is 'awake' and cb?
      cb()
      @_log @_nsLog, 'quick update'
      return yes
    if evt? then @_log @_nsLog, evt.type
    return no if @_state in ['awake', 'waking']
    # - Prepare.
    delay = @o.ms.delay.in
    duration = @o.ms.duration.in
    wake = =>
      @onShow triggerChanged, evt
      @$tip.fadeIn duration, =>
        if triggerChanged
          cb() if cb?
        if @o.safeToggle is on then @$tip.siblings(@o.cls.tip).fadeOut()
        @afterShow triggerChanged, evt
        @_state = 'awake'
    # - Run.
    if @_state is 'sleeping'
      @_log @_nsLog, 'clear sleep'
      clearTimeout @_sleepCountdown
      duration = 0
      wake()
    else if (evt? and evt.type is 'truemouseenter')
      triggerChanged = yes
      @_state = 'waking'
      @_wakeCountdown = setTimeout wake, delay
    # - Success.
    yes

  # - Much simpler toggler. As long as tip isn't truly visible, sleep is unneeded.
  sleepByTrigger: ($t) ->
    return no if @_state isnt 'awake'
    @_state = 'sleeping'
    clearTimeout @_wakeCountdown
    @_sleepCountdown = setTimeout =>
      @onHide()
      @$tip.fadeOut @o.ms.duration.out, =>
        @_state = 'asleep'
        @afterHide()

    , @o.ms.delay.out
    # - Success.
    yes

  # Hooks
  onShow: (triggerChanged, evt) -> return
  onHide: $.noop
  afterShow: (triggerChanged, evt) -> return
  afterHide: $.noop
  htmlOnRender: $.noop
  offsetOnTriggerMouseMove: (evt, offset, $t) -> no

#
# SnapTip API
# -----------
#
class SnapTip extends Tip

  constructor: ($ts, o, $ctx) ->
    super $ts, o, $ctx
    if @o.snap.toTrigger is off
      @o.snap.toTrigger = @o.snap.toXAxis is on or @o.snap.toYAxis is on
    if @o.snap.toXAxis is on then @o.cursorHeight = 0
    if @o.snap.toYAxis is on then @o.cursorHeight = 2
    @_offsetStart = null
    # - Add snapping config as classes.
    _.each @o.snap, (active, prop) => if active then @$tip.addClass @o.cls.snap[prop]

  # ###Protected

  # - The main positioner. Uses the trigger offset as the base.
  #   TODO - Still needs to support all the directions.
  _moveToTrigger: ($t, baseOffset) ->
    # @_log @_nsLog, baseOffset
    offset = $t.offset()
    if @o.snap.toXAxis is yes
      if @isDirection 'south' then offset.top += $t.outerHeight()
      if @o.snap.toYAxis is no
        offset.left = baseOffset.left - (@$tip.outerWidth() - 12)/ 2
    if @o.snap.toYAxis is yes
      if @isDirection 'east' then offset.left += $t.outerWidth()
      if @o.snap.toXAxis is no
        offset.top = baseOffset.top - $t.outerHeight() / 2
    offset

  # - Bind to get initial position for snapping. This is only for snapping
  #   without snapping to the trigger, which is only what's currently supported.
  #   See `afterShow` hook.
  _bindTrigger: ($t) ->
    super $t
    $t.on @_evt('truemouseleave'), (evt) => @_offsetStart = null

  # ###Public

  # Hooked

  # - Make the tip invisible while it's being positioned, then reveal it.
  onShow: (triggerChanged, evt) ->
    if triggerChanged is yes
      @$tip.css 'visibility', 'hidden'

  afterShow: (triggerChanged, evt) ->
    if triggerChanged is yes
      @$tip.css 'visibility', 'visible'
      @_offsetStart =
        top: evt.pageY
        left: evt.pageX

  # - Main positioning handler.
  offsetOnTriggerMouseMove: (evt, offset, $t) ->
    newOffset = _.clone offset
    if @o.snap.toTrigger is on
      newOffset = @_moveToTrigger $t, newOffset
    else
      if @o.snap.toXAxis is on
        newOffset.top = @_offsetStart.top
        @_log @_nsLog, 'xSnap'
      if @o.snap.toYAxis is on
        newOffset.left = @_offsetStart.left
        @_log @_nsLog, 'ySnap'
    newOffset

# Export
# ------
# Both are exported with the `asSingleton` flag set to true.
$.fn.tip = ns.createPlugin ns.tip, Tip, yes
$.fn.snapTip = ns.createPlugin ns.snapTip, SnapTip, yes
