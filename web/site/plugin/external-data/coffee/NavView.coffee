###
Feed Navigation View
###

$ = jQuery
Plugin = Silexhibit.ExternalData

class Plugin.NavView extends Backbone.View

  # Inherited
  # ---------

  events: ->
    events = {}
    if @navigateByHover is on
      events["mouseenter .st-feed-nav-trigger-wrap"] = 'startOpenCountdown'
      events["mouseleave .st-feed-nav-trigger-wrap"] = 'endOpenCountdown'
    else
      events["click [data-st-service-name]"] = 'open'
    events
  template: Plugin.templates.nav

  # Own
  # ---

  isCustom: no
  navigateByHover: null
  hoverNavTransactionMs: null
  _hoverNavMsStart: 0
  _hoverNavMsCredit: 0
  _hoverNavTimeout: null

  $triggers: null

  # Inherited
  # ---------

  constructor: (options) ->
    @navigateByHover = options.navigateByHover if options.navigateByHover
    @hoverNavTransactionMs = options.hoverNavTransactionMs if options.hoverNavTransactionMs
    super options

  initialize: (options) ->
    @isCustom = @$el.length and @$el.has("[data-st-service-name]").length
    @render()

  render: ->
    if @navigateByHover is on
      @$triggers = @$ '[data-st-service-name]'
      @$triggers.parent().addClass 'st-feed-nav-trigger-wrap'
    @

  # Own
  # ---

  open: (e) ->
    e.preventDefault() if e.type is 'click'
    $trigger = $ e.target
    name = $trigger.data 'st-service-name'
    @collection.fetchFeed name

  startOpenCountdown: (e) ->
    # Start transaction. Silently fail if invalid.
    # TODO: Prefetching?
    ms = @hoverNavTransactionMs
    $triggerWrap = $ e.currentTarget
    ms -= @_hoverNavMsCredit if $triggerWrap.hasClass 'st-in-progress'
    return if ms <= 0
    $triggerWrap.addClass 'st-in-progress'
    @_hoverNavTimeout = setTimeout => #onTransactionSuccess
      @_hoverNavMsCredit = 0
      $triggerWraps = @$triggers.parent()
      $triggerWraps.removeClass 'st-selected'
      setTimeout -> #onTransactionReversalCompletion
        $triggerWraps.not($triggerWrap).removeClass 'st-in-progress'
      , @hoverNavTransactionMs
      $triggerWrap.addClass 'st-selected'
      e.target = $triggerWrap.find('[data-st-service-name]:first-child').get(0)
      @open e
    , ms
    @_hoverNavMsStart = (new Date()).getTime()

  endOpenCountdown: (e) ->
    # Cancel transaction. Silently fail if redundant.
    clearTimeout @_hoverNavTimeout
    @_hoverNavMsCredit = (new Date()).getTime() - @_hoverNavMsStart
    @_hoverNavMsCredit = 0 if @_hoverNavMsCredit > @hoverNavTransactionMs
    if @_hoverNavMsCredit
      $triggerWrap = $ e.currentTarget
      setTimeout -> #onCanceledTransactionReversalCompletion
        $triggerWrap.removeClass 'st-in-progress'
        @_hoverNavMsCredit = 0
      , @_hoverNavMsCredit

