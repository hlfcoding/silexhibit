###
Slideshow Exhibit
###

$ = jQuery
_attr = Silexhibit.ns1
_evt = Silexhibit.ns2
_delegate = null
_sel = null

NS = Silexhibit.Slideshow =
  ACTION_COMMANDS: [
    'play'
    'next'
    'prev'
    'previous'
  ]
  delegate: null,
  events: $ {}

  createSlideshow: ->
    $el = $ @
    opts = _delegate.options $el

    NS.updateCloak.call $el, on

    $el.addClass 'flexslider'

    if opts.slideshow isnt off
      $el.addClass 'st-playing'

    $el.flexslider $.extend yes,
      selector: _sel '.st-slides > li', $el
      animation: 'slide'
      smoothHeight: yes
      slideshowSpeed: 5000
      animationSpeed: 400
      # Usability features.
      pauseOnHover: yes
      video: yes
      # Primary controls.
      controlNav: no
      directionNav: no
      # Special properties.
      controlsContainer: _sel '.nav', $el
      # Callback API.
      start: (slider) ->
        NS.updateUIForSlide.call $el, 0
        NS.updateCloak.call $el, off
      before: (slider) ->
        NS.updateUIForSlide.call $el, slider.animatingTo
    , opts

    slider = $el.data 'flexslider'
    slider.play = _.wrap slider.play, (oldResume) ->
      return if model.prop 'locked'
      model.trigger { type: 'pause', did: off }
      oldResume slider, arguments...
    slider.pause = _.wrap slider.pause, (oldPause) ->
      return if model.prop 'locked'
      model.trigger { type: 'pause', did: on }
      oldPause slider, arguments...

    model = NS.setupSlideshowModel.call $el
    NS.setupSlideshowUI.call $el

  setupSlideshowModel: ->
    $el = @
    slider = $el.data 'flexslider'
    opts = _delegate.options $el
    key = _attr 'model'

    model = $el.data key
    model ?= $ {}
    $el.data key, model

    if opts.slideshow isnt off
      model.off()
      model.on 'pause', (e) -> $el.toggleClass 'st-playing', not e.did
      # Handle delegate events.
      model.prop 'was-playing', no
      model.prop 'locked', no
      _delegate.events.on
        'suspend': (e) ->
          model.prop 'was-playing', slider.playing
          if slider.playing
            slider.pause()
            model.prop 'locked', yes
        'resume': (e) ->
          shouldPlay = not slider.playing and model.prop 'was-playing'
          if shouldPlay
            model.prop 'locked', no
            slider.play()

    model

  setupSlideshowUI: ->
    $el = @
    $nav = $el.find _sel '.st-nav', $el
    if _delegate.hasNav($el)
      $nav.on _evt('click'), _sel('.st-btn-prev', $nav), (e) ->
        $el.flexslider 'prev'
        e.preventDefault() and no
      $nav.on _evt('click'), _sel('.st-btn-next', $nav), (e) ->
        $el.flexslider 'next'
        e.preventDefault() and no
    else $nav.off().remove()

    $metaWrap = $el.find '.st-metas'
    if _delegate.hasCaption($el)
      $viewport = $el.find '.flex-viewport'
      $metaWrap.children().each ->
        $el_ = $ @
        $el_.html $.trim $el_.html()
      $metaWrap.detach().insertAfter $viewport
    else $metaWrap.off().remove()

  updateUIForSlide: (idx) ->
    $el = @
    if _delegate.hasNav($el)
      $counter = $el.find '.st-slide-counter'
      $counter.text (idx + 1)
    if _delegate.hasCaption($el)
      $metas = $el.find '.meta'
      $metas.hide().eq(idx).not(':empty').show()

  updateCloak: (cloak) ->
    $el = @
    if _delegate.hasCloak($el)
      $el.toggleClass 'cloaked', cloak

  oldPlugin: $.fn.flexslider
  plugin: do ->
    $.fn.flexslider = _.wrap $.fn.flexslider, (oldPlugin, arg={}) ->
      oldPlugin.call @, arg
      $el = @
      slider = $el.data 'flexslider'
      if typeof arg is 'string'
        command = arg
        opts = slider.vars
        # Auto-resume extension.
        shouldAutoresume = opts.slideshow is on and opts.pauseOnAction is on and not slider.playing
        if shouldAutoresume and command in NS.ACTION_COMMANDS
          key = _attr 'resume-timeout'
          timeout = slider.data key
          if timeout? then window.clearTimeout timeout
          timeout = window.setTimeout slider.play, opts.slideshowSpeed
          slider.data key, timeout
      @

$ ->
  NS.delegate = _delegate = $.extend yes,
    events: $ {}
    options: ($el) ->
      slideshow: on
    hasCloak: ($el) -> yes
    hasNav: ($el) -> yes
    hasCaption: ($el) -> yes
    selector: (selector, $el) -> selector
  , NS.delegate
  _sel = _delegate.selector

  $slideshows = $ '.st-slideshow'
  $slideshows.each NS.createSlideshow
