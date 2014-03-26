###
jQuery Mixins
Released under the MIT License
Written with jQuery 1.10.2
###

$ = jQuery

$.createMixin = (mixins, name, mixin) ->
  mixins[name] = mixin
  method.mixin = name for own k, method of mixin
  mixin

$.applyMixin = (context, mixin) ->
  mixin = $.mixins[mixin] if _.isString mixin
  return unless mixin?
  # Get onces.
  onceNames = $.applyMixin.onceNames
  onces = (mixin[name] for name in onceNames when _.isFunction mixin[name])
  mixinToApply = _.omit mixin, onceNames
  # Apply mixin.
  _.extend context, mixinToApply
  # Call onces (decorators).
  _.bind(once, context)() for once in onces

# Supported decorators.
$.applyMixin.onceNames = [
  'decorate'
  'decorateOptions'
]

$.applyMixins = (context, mixins...) -> $.applyMixin context, mixin for mixin in mixins

$.mixins ?= {}

$.mixins.customEvent = (evt) ->
  on: (name) ->
    name = evt name if name?
    @$el.on.apply @$el, arguments
  off: (name) ->
    name = evt name if name?
    @$el.off.apply @$el, arguments
  trigger: (name, userInfo) ->
    type = evt name
    @$el.trigger { type, userInfo }

$.mixins.customData = (attr) ->
  data: () ->
    if arguments.length
      first = arguments[0]
      if _.isString first
        arguments[0] = attr first
      else if _.isObject first
        pairs = {}
        pairs[attr(k)] = v for own k, v of first
        arguments[0] = pairs
    @$el.data.apply @$el, arguments

$.mixins.selection =
  select: -> @["$#{name}"] = @$el.find selector for name, selector of @sels

$.mixins.type = (attr, supportedTypes, mixins) ->
  doesSupportTypes: ->
    @registerTypes() unless @_types?
    not _.difference(@_types, supportedTypes).length
  registerTypes: -> @_types = @$el.data(attr('type')).split(' ')
  typeMixins: ->
    mixins[type] for type in @_types
