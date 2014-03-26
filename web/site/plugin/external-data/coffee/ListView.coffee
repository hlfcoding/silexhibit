###
Feed List View
###

$ = jQuery
Plugin = Silexhibit.ExternalData
console = Plugin.console

class Plugin.ListView extends Backbone.View

  # Inherited
  # ---------

  className: 'st-feed'

  # Own
  # ---

  feedClassName: null
  itemViews: null

  # Inherited
  # ---------

  initialize: (options) ->
    @itemViews = []
    @listenTo @collection, 'sync', @render
    @render()

  render: ->
    @empty()
    els = []
    @setFeedClassName @collection.feedName
    for item in @collection.models
      itemView = @appendItem item
      els.push itemView.el
    @$el.append els
    @

  # Own
  # ---

  setFeedClassName: (feedName) ->
    className = "st-feed-#{feedName}"
    if @feedClassName? then @$el.removeClass @feedClassName
    @feedClassName = className
    @$el.addClass @feedClassName

  appendItem: (item) ->
    itemView = new Plugin.ItemView model: item
    @itemViews.push itemView
    itemView.setType @collection.feedName
    itemView.render()

  empty: ->
    @$el.empty ".#{Plugin.ItemView::className}"
    @itemViews = []
