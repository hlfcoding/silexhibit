###
Item Collection
###

$ = jQuery
Plugin = Silexhibit.ExternalData
console = Plugin.console

class Plugin.ItemCollection extends Backbone.Collection

  # Inherited
  # ---------

  url: ->
    # TODO: Simplify.
    l = window.location
    "#{l.protocol}//#{l.host}/st-external-data/#{@feedName}/"

  # Own
  # ---

  feedName: null
  feedDataType: null
  feedFormat: null
  feedIsXml: no
  feedOrder: null
  feedUnit: null

  maxItems: null

  # Inherited
  # ---------

  initialize: (models, opts) ->
    @model = Plugin.ItemModel
    @maxItems = opts.maxItems if opts.maxItems

  parse: (data, opts) ->
    if @feedIsXml
      data = @getParser().xml2json data
    if _.isArray data
      items = data
    else
      for possibleKey in [@feedUnit, _.pluralize(@feedUnit)]
        if not data[possibleKey]
          for own k, v of data
            continue unless _.isObject v
            if possibleKey of v
              data = v
              itemsKey = possibleKey
              break
        else itemsKey = possibleKey
        break if itemsKey?
      unless data?
        return console.error "Can't find items container."
      items = data[itemsKey]
    console.info "Items", items
    items = _.take items, @maxItems

  # Own
  # ---

  getParser: -> @parser ?= new X2JS()

  setFeedName: (name) ->
    return if name is @feedName
    @feedName = name
    @feedFormat = Plugin.FEED_FORMATS[name]
    @feedDataType = switch @feedFormat
      when 'atom' then 'xml'
      else @feedFormat
    @feedIsXml = @feedFormat in ['atom', 'xml']
    if Plugin.FEED_ORDERS[name]?
      @feedOrder = Plugin.FEED_ORDERS[name]
      @comparator = switch @feedOrder.sort
        when 'descending'
          (modelA, modelB) ->
            valueA = modelA.get @feedOrder.by
            valueB = modelB.get @feedOrder.by
            if valueA > valueB then -1 # A before B.
            else if valueA < valueB then 1 # A after B.
            else if valueA == valueB then 0 # Equal rank.
        else null
    @feedUnit = Plugin.FEED_UNITS[name]

  fetchFeed: (name) ->
    if @setFeedName name
      @fetch dataType: @feedDataType

