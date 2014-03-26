###
jQuery Component Helpers
Released under the MIT License
Written with jQuery 1.10.2
###

$ = jQuery

$.createPluginFn = (deps) ->
  createComponentOrRunCommand = ($el, opts, command) ->
    it = deps.instance $el
    if not _.isObject it
      opts = _.defaults (opts or {}), deps.defaults
      optsCopy = $.extend yes, {}, opts
      deps.create $el, optsCopy
    else if command? and _.isFunction it.handleCommand
      # Handle commands as needed.
      userInfo = arguments[1]
      userInfo = userInfo $el if _.isFunction userInfo
      it.handleCommand null,
        type: command
        userInfo: userInfo
  (arg) ->
    if _.isString arg then command = arg else opts = arg
    @.each -> createComponentOrRunCommand $(@), opts, command

$.createPluginApplyFn = (attrName) ->
  fnName = _.string.camelize attrName
  selector = "[data-#{attrName}]"
  ($context, args...) ->
    $context ?= $ 'body'
    $els = $context.find selector
    $els[fnName].apply $els, args

$.createInstanceFn = (dataKey) ->
  ($el, props) ->
    return $el.data dataKey, props if props?
    $el.data dataKey

$.createUIComponent = (plugin, $el, opts) ->
  # Create instance.
  component = { $el, opts }
  component.sels = opts.selectors if opts.selectors?
  component.componentName = plugin.componentName
  # Apply mixins.
  $.applyMixins component,
    $.mixins.customEvent(plugin.evt),
    $.mixins.customData(plugin.attr),
    plugin.mixins.base
  # Store instance.
  plugin.instance $el, component
  component
