###
HLF Foldable jQuery Plugin v1.1
Released under the MIT License
Written with jQuery 1.7.2
###
$ = jQuery
ns = $.hlf

###
Foldable
--------
###
ns.foldable =
  toString: (context) ->
    switch context
      when 'event' then '.hlf.foldable'
      when 'data' then 'hlfFoldable'
      when 'class' then 'js-foldable'
      else 'hlf.foldable'

  defaults: do (pre='js-fold-') ->
    cls: (->
      cls = {}
      _.each ['trigger', 'expanded', 'collapsed', 'head', 'item', 'active', 'disabled'],
        (key) -> cls[key] = "#{pre}#{key}"
      return cls
    )()

    slr: (name) -> ".#{@cls[name]}"
    trigger:
      tpl:       _.template """<a href="#" title="{{label}}">{{icon}}</a>"""
      icon:
        expanded:  '&ndash;'
        collapsed: '+'
      label:
        expanded:  'show featured'
        collapsed: 'show all'
    initialState: 'collapsed'
    togglebleFilter: (idx) -> true


###
Foldables
---------
###
ns.foldables =
  toString: (context) ->
    switch context
      when 'event' then '.hlf.foldables'
      when 'data' then 'hlfFoldables'
      when 'class' then 'js-foldables'
      else 'hlf.foldables'

  defaults: do (pre='js-folds-') ->
    ###
    Options for `$.fn.foldable` can be included in the `foldable` wrapper.
    They will be passed through and applied to all instances.
    ###
    foldable: {}
    autoCollapse: off
    cls:
      menu: "#{pre}menu"
    slr: (name) -> ".#{@cls[name]}"
    toFeature:
      baseNum: 2
      customNums: null


###
Foldable API
------------

###
class Foldable

  constructor: (@$el, @o) ->
    @$head = @$el.find @o.slr 'head'
    @$trigger = @$head.find @o.slr 'trigger'
    @$items = @$el.find @o.slr 'item'
    #
    # Setup trigger.
    # - Render as needed.
    # - Bind to toggle and self-update.
    expand = @o.initialState is 'expanded'
    do (o=@o.trigger) =>
      if not @$trigger.length
        type = if expand then 'expanded' else 'collapsed'
        html = o.tpl { label: o.label[type], icon: o.icon[type] }
        @$trigger = $(html).addClass(@o.cls.trigger).appendTo(@$head)
                           .on 'click', (evt) -> evt.preventDefault()
      @$el.on @_evt('click'), "#{@o.slr 'trigger'}:not(#{@o.slr 'disabled'})", (evt) =>
        evt.preventDefault()
        proceed = @toggleExpanded()
        return unless proceed
        @updateTrigger()


    @toggleExpanded expand


  editOptions: (options) ->
    $.extend (deep=on), @o, options

  toggleExpanded: (expand, silent=off, force=off) ->
    expand ?= if @expanded? then not @expanded else @$el.hasClass "#{@o.cls.collapsed}"
    # - CSS hook.
    @$el.toggleClass(@o.cls.expanded, expand)
        .toggleClass(@o.cls.collapsed, not expand)
    # - Fold hook.
    if silent is off then @$el.trigger @_evt('fold'), [expand]
    # - JS-controlled toggle.
    proceed = @toggleVisibleItems expand, force
    # - Toggle disabled as needed.
    @$el.add(@$trigger).toggleClass @o.cls.disabled, not proceed
    return no if not proceed
    # - Save state.
    @expanded = expand
    # - Update UI as needed.
    if silent is on then @updateTrigger()
    return yes

  toggleVisibleItems: (visible, force=off) ->
    return no if @$el.is(@o.slr 'active') and force is off
    @$items.filter(@o.togglebleFilter).toggle visible
    return yes

  updateTrigger: ->
    do (o=@o.trigger) =>
      type = if @expanded then 'expanded' else 'collapsed'
      @$trigger.html(o.icon[type]).attr('title', o.label[type])



###
Foldables API
-------------
###
class Foldables

  constructor: (@$el, @o) ->
    # - Update options.
    if o.toFeature isnt off
      toFeature = o.toFeature.customNums ? o.toFeature.baseNum
      o.foldable.togglebleFilter = (idx) -> idx >= toFeature
    # - Setup foldable menus.
    @$menus = @$el.find o.slr 'menu'
    @$menus.foldable o.foldable
    o.foldable = @$menus.first().foldable().o
    # - Setup auto-collapse.
    @$el.on Foldable::_evt('fold'), o.slr('menu'), (evt, expand) ->
      # This is the future state.
      return if o.autoCollapse is off or expand is no
      $(@).siblings()
          .not(o.foldable.slr 'disabled')
          .each -> $(@).foldable().toggleExpanded off, (silent=on), (force=on)


  editOptions: (options) ->
    $.extend (deep=on), @o, options


# Export
# ------

$.fn.foldable = ns.createPlugin ns.foldable, Foldable
$.fn.foldables = ns.createPlugin ns.foldables, Foldables
