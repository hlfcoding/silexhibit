###
Selectable DOM Component
###

$ = jQuery

$.fn.selectable = do ->

  # Boilerplate:
  attr = attributeNamespace = (name) -> "selectable-#{name}"
  evt = eventNamespace = (name) -> "#{name}.selectable"
  instance = $.createInstanceFn attr('instance')
  register = $.createPluginApplyFn 'selectable'
  mixins = {}
  # End.

  supportedTypes = [
    'dropdown'
    'radio-group'
  ]

  defaults =
    shouldHideRadios: yes
    selectors:
      options: ':radio, :checkbox, select'
      labels: 'label'

  create = ($el, opts) ->
    # Create instance.
    selectable = $.createUIComponent plugin, arguments...
    # Apply mixins.
    $.applyMixins selectable,
      'selection', $.mixins.type(attr, supportedTypes, mixins)
    return unless selectable.doesSupportTypes()
    $.applyMixins selectable, selectable.typeMixins()...
    # Init.
    selectable.init()

  # Boilerplate:
  plugin = $.createPluginFn { create, defaults, instance }
  _.extend plugin, { componentName: 'selectable', attr, evt, defaults, instance, register, mixins }
  # End.

  $.createMixin mixins, 'base',

    # Own
    # ---
    init: ->
      @trigger 'will-init'
      @select()
      @bind()
      @trigger 'did-init'

    bind: ->
      _.bindAll @, 'handleValueChange'
      @$el.on 'click', @sels.labels, (e) =>
        $label = $ e.target
        @optionForLabel $label
          .trigger 'click'
      @$el.on 'change', @sels.options, @handleValueChange

    handleCommand: (sender, command) ->
      @trigger 'before-command', { command }
      switch command.type
        when 'update' then @selectOption command.userInfo.option
        else
      @trigger 'after-command', { command }

    handleValueChange: (sender) ->
      return unless (sender instanceof $.Event)
      @$option = $ sender.target
      option = @$option.val()
      userInfo = { option }
      @trigger 'will-commit', userInfo
      # @event commit.<ns>
      @trigger 'commit', userInfo

    # Actions
    # -------
    selectedOption: ->
      @$options
        .filter ':checked, :selected'
        .first()

    selectOption: (option) ->
      @$options
        .filter "[value=#{option}]"
        .trigger 'click'

    optionForLabel: ($label) ->
      selector = @sels.options
      $option = $label.closest selector
      $option = $label.siblings selector if not $option.length

    labelForOption: ($option) ->
      selector = @sels.labels
      $label = $option.closest selector
      $label = $option.siblings selector if not $label.length

  $.createMixin mixins, 'radio-group',

    # Action Overrides
    # ----------------

    # Onces
    # -----
    decorateOptions: ->
    decorate: ->
      @on 'did-init', => @initRadioGroup()

    # Own
    # ---
    initRadioGroup: ->
      @$options.hide() if @opts.shouldHideRadios
      @markLabelSelected @labelForOption @selectedOption()
      @bindRadioGroup()

    bindRadioGroup: ->
      @$el.on 'change', @sels.options, (e) =>
        $option = $ e.target
        $label = @labelForOption $option
        @markLabelSelected $label

    # Actions
    # -------
    markLabelSelected: ($label) ->
      className = attr 'selected'
      @$labels.removeClass className
      $label.addClass className

  # TODO: Use select2.
  $.createMixin mixins, 'dropdown',

    # Action Overrides
    # ----------------
    selectOption: (option) -> @$options.val option

    # Onces
    # -----
    decorateOptions: ->
    decorate: ->
      @on 'did-init', => @initDropdown()

    # Own
    # ---
    initDropdown: ->

    # Actions
    # -------

  plugin
