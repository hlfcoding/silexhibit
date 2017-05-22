###
jQuery Component Helpers
========================
Released under the MIT License  
Written for jQuery 1.10+  
Extra dependencies: `_`, `_.string`, `$.applyMixins`
###

# Components, same as modules or widgets, are just reusable jQuery functionality
# that represent and manage a specific part of the DOM. So they're mostly for
# UI.

# Aliases.
$ = jQuery

# createPluginFn
# --------------
# Given dependencies needed by the plugin function, generate a conventional,
# component-based jQuery plugin function. Requires dependencies:

# - `create` should create and set up the instance. Conventionally, it should
#   wrap a call to `$.createUIComponent`.
# - `defaults` should specify all available options and provide the default
#   option values. Any options without default value should be either `null` or
#   `undefined`.
# - `instance` should return the component instance for an element.
#   Conventionally, it should be generated with a call to `$.createInstanceFn`.
$.createPluginFn = (deps) ->
  # Given a jQuery element, bind it to a new component if it hasn't been yet. If
  # it has, and the component can `handleCommand`, then run the command.
  createComponentOrRunCommand = ($el, opts, command) ->
    # Get the `instance` using the dependency. Note that the naming convention
    # for getting referencing a component instance is `it`. `this` is reserved
    # (or just misleading in CoffeeScript), `self` cannot be used in certain
    # cases, and `that` is more vague.
    it = deps.instance $el
    # If there is no instance, conventionally generate one using the `defaults`
    # and `create` dependencies. Extend the options and pass a safely-mutable
    # copy into the constructor.
    if not _.isObject it
      opts = _.defaults (opts or {}), deps.defaults
      optsCopy = $.extend yes, {}, opts
      deps.create $el, optsCopy
    # Or if provided a command, the component instance should `handleCommand` if
    # possible. `handleCommand` is expected to take, in order, a `command`
    # object with an optional `userInfo` object, and an optional `sender`
    # reference. If the former is a function, it will be evaluated into its
    # returned value.
    else if command? and _.isFunction it.handleCommand
      command.userInfo = command.userInfo $el if _.isFunction command.userInfo
      sender = null
      it.handleCommand command, sender
  # Generate jQuery plugin function.
  # If the first argument is a string, the command path is assumed.
  ->
    if _.isString arguments[0]
      command =
        type: arguments[0]
        userInfo: arguments[1]
    else
      opts = arguments[0]
    @.each -> createComponentOrRunCommand $(@), opts, command

# createPluginApplyFn
# -------------------
# Given the data-attribute name for the jQuery component, generate a
# conventional jQuery plugin that 'applies' the component plugin function
# throughout a given jQuery selection `context` to any elements with the data-
# attribute. This apply function will pass-through any additional arguments to
# the component plugin function. `context` will default to the DOM body.
# `attrName` should be conventionally the same as the jQuery plugin function
# name when camelized. The apply function should conventionally be a 'static'
# `register` property on the component plugin function.
$.createPluginApplyFn = (attrName) ->
  fnName = _.string.camelize attrName
  selector = "[data-#{attrName}]"
  ($context, args...) ->
    $context ?= $ 'body'
    $els = $context.find selector
    $els[fnName].apply $els, args

# createInstanceFn
# ----------------
# Given the `dataKey` (conventionally the data-attribute name) for the jQuery
# component, generate a conventional accessor for the component instance using
# the jQuery data API and the attached element. If the instance is provided as
# `props`, set the data entry for the instance.
$.createInstanceFn = (dataKey) ->
  ($el, props) ->
    return $el.data dataKey, props if props?
    $el.data dataKey

# createUIComponent
# -----------------
# Given the component plugin function, and the pass-through arguments of the
# conventional component's `create` method, create a base component instance.
$.createUIComponent = (plugin, $el, opts) ->
  # Base instance has the conventional jQuery properties `$el`, `opts`, and
  # `sels`, as well as `componentName`. `plugin` should have a conventional 
  # `componentName`.
  component = { $el, opts }
  component.sels = opts.selectors if opts.selectors?
  component.componentName = plugin.componentName
  # Apply base mixins. `plugin` should have a conventional `mixins` collection
  # with a `base` mixin. The latter gets applied on top of custom extensions to
  # jQuery events and data APIs.
  $.applyMixins component,
    $.mixins.customEvent(plugin.evt),
    $.mixins.customData(plugin.attr),
    plugin.mixins.base
  # Store instance. `plugin` should have a conventional `instance` function via
  # `createInstanceFn`.
  plugin.instance $el, component
  component
