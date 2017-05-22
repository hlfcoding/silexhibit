###
jQuery Mixins
=============
Released under the MIT License  
Written with jQuery 1.10+  
Extra dependencies: `_`
###

# Mixins are another approach to encapsulating object-oriented behavior. This
# set of helper functions are to fill the gap of a generic mixin system left by
# jQuery's highly-specified plugin system. General mixins are also provided to
# add helper methods for even more flexible extensions between mixins.

# Aliases.
$ = jQuery

# createMixin
# -----------
# Given a collection of `mixins`, add a new mixin with given `name` and `mixin`
# method collection. Conventionally, each logical package of software should be
# written as a collection of mixins, with one named 'base'.
$.createMixin = (mixins, name, mixin) ->
  mixins[name] = mixin
  # Tag the mixed-in method with the mixin name to improve inspecting instances.
  method.mixin = name for own k, method of mixin
  mixin

# applyMixin
# ----------
# Given a `context` to decorate with a valid `mixin`, run any run-once hooks
# after applying a mixin copy without the hooks. `context` is conventionally a
# class instance.
$.applyMixin = (context, mixin) ->
  # If `mixin` is a string, check the general `$.mixins` collection for the mixin.
  mixin = $.mixins[mixin] if _.isString mixin
  return unless mixin?
  # Get run-once methods and filter a clean mixin copy. Run-once methods are
  # what's specified in `$.applyMixin.onceNames` and implemented by the mixin.
  onceNames = $.applyMixin.onceNames
  onces = (mixin[name] for name in onceNames when _.isFunction mixin[name])
  mixinToApply = _.omit mixin, onceNames
  # Apply mixin and call onces with explicit context.
  _.extend context, mixinToApply
  _.bind(once, context)() for once in onces

# Supported decorators:

# - `decorate`: Allow more complex extending of the instance. For example,
#   methods and properties can be removed, handlers can be added to triggered
#   events for more complex extending of existing methods.
# - `decorateOptions`: Allow extending the context's options, which are
#   conventionally a property named `opts`.
$.applyMixin.onceNames = [
  'decorate'
  'decorateOptions'
]

# applyMixins
# -----------
# Given a `context` (class) to decorate with `mixins`, which should be passed in
# order of application, call `$.applyMixin` for each mixin. Conventionally, this
# should be used instead of `$.applyMixin`.
$.applyMixins = (context, mixins...) -> $.applyMixin context, mixin for mixin in mixins

# mixins
# ------
# The general mixin collection that's provided for writing foundation-level
# jQuery mixins. Conventionally, other mixins not shared between different
# logical packages do not belong here.
$.mixins ?= {}

# mixins.customEvent
# ------------------
# Given an event-name translator that makes an event-name follow jQuery
# conventions, as well as that the context has a property `$el`, generate a
# mixin that applies convenience wrappers around the jQuery custom event API to
# simplify event API calls as much as possible.
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

# mixins.customData
# -----------------
# Given a data-attribute-name translator that makes a property-name follow
# jQuery conventions, as well as that the context has a property `$el`, generate
# a mixin that applies convenience wrappers around the jQuery data API to
# simplify data API calls as much as possible.
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

# mixins.selection
# ----------------
# Given the context has a property `$el` and a property `sels` (selectors),
# define cached selector results for each name-selector pair.
$.mixins.selection =
  select: -> @["$#{name}"] = @$el.find selector for name, selector of @sels

# mixins.type
# -----------
# Given a data-attribute-name translator that makes a property-name follow
# jQuery conventions, as well as that the context has a property `$el`, generate
# a mixin that provides helpers for managing a string-based type layer for the
# context based on the type data-attribute.

# - `doesSupportTypes`: Lazily calls `registerTypes`. Checks using
#   `supportedTypes`, which conventionally should be static.
# - `registerTypes`: If needed, sets up `_types` property based on type data
#   attribute from `attr`.
# - `typeMixins`: Dynamically get mixins from `mixins` collection based on the
#   context's type configuration.
$.mixins.type = (attr, supportedTypes, mixins) ->
  doesSupportTypes: ->
    @registerTypes() unless @_types?
    not _.difference(@_types, supportedTypes).length
  registerTypes: -> @_types = @$el.data(attr('type')).split(' ')
  typeMixins: ->
    mixins[type] for type in @_types
