###
Editable DOM Component
###

$ = jQuery

$.fn.editable = do ->

  # Boilerplate:
  attr = attributeNamespace = (name) -> "editable-#{name}"
  evt = eventNamespace = (name) -> "#{name}.editable"
  instance = $.createInstanceFn attr('instance')
  register = $.createPluginApplyFn 'editable'
  mixins = {}
  # End.

  supportedTypes = [
    'color-picker'
    'editor'
    'file-uploader'
    'inline'
  ]

  defaults =
    selectors:
      text:       '.text'
      input:      '.input input'
      inputWrap:  '.input'

  create = ($el, opts) ->
    # Create instance.
    editable = $.createUIComponent plugin, arguments...
    # Apply mixins.
    $.applyMixins editable,
      'selection', $.mixins.type(attr, supportedTypes, mixins)
    return unless editable.doesSupportTypes()
    $.applyMixins editable, editable.typeMixins()...
    # Init.
    editable.init()

  # Boilerplate:
  plugin = $.createPluginFn { create, defaults, instance }
  _.extend plugin, { componentName: 'editable', attr, evt, defaults, instance, register, mixins }
  # End.

  # Base Mixin
  # ==========
  $.createMixin mixins, 'base',

    # Own
    # ---
    init: ->
      @trigger 'will-init'
      @select()
      @bind()
      @toggleEditing off if @toggleEditing?
      @trigger 'did-init'

    bind: ->
      _.bindAll @, 'handleValueChange'
      if @toggleEditing?
        @$el.on 'click', @sels.text, (e) => @toggleEditing on
        @$el.on 'blur', @sels.input, (e) => @toggleEditing off
      @$el.on 'change', @sels.input, @handleValueChange

    handleCommand: (sender, command) ->
      @trigger 'before-command', { command }
      switch command.type
        when 'update' then @renderText command.userInfo.text
        else
      @trigger 'after-command', { command }

    handleValueChange: (sender) ->
      text = @inputValue()
      text = @textOnValueChange text
      userInfo = { text }
      @trigger 'will-commit', userInfo
      # @event commit.<ns>
      @trigger 'commit', userInfo

    # Actions
    # -------
    toggleEditing: (state) ->
      @isEditing = state
      if state is on
        @$text.hide()
        @$inputWrap.show()
        @$input.focus()
      else if state is off
        @$text.show()
        @$inputWrap.hide()
      # @event toggle-edit.<ns>
      @trigger 'toggle-edit', { state }

    renderText: (text) -> @$text.text text

    inputValue: -> @$input.val()

    textOnValueChange: (text) -> text

  # Inline Mixin
  # ============
  $.createMixin mixins, 'inline',

    # Action Overrides
    # ----------------
    textOnValueChange: (text) -> if not text.length then @$input.attr 'placeholder' else text

    # Onces
    # -----
    decorateOptions: ->
    decorate: ->
      @on 'did-init', => @initInline()
      @on 'will-commit', (e) => @renderText e.userInfo.text
      @on 'after-command', (e) =>
        command = e.userInfo.command
        switch command.type
          when 'update' then @updatePlaceholder()
          else

    # Own
    # ---
    initInline: ->
      @updatePlaceholder()

    # Actions
    # -------
    updatePlaceholder: -> @$input.attr 'placeholder', @$text.text()

  $.createMixin mixins, 'editor',

    # Action Overrides
    # ----------------
    inputValue: () ->
      switch @editorName
        when 'CodeMirror' then @editor.getValue()
        else ''

    renderText: (text) ->
      switch @editorName
        when 'CodeMirror' then @editor.setValue text
        else

    # Onces
    # -----
    decorateOptions: ->
      @opts.selectors.markup = '.editor-markup'
    decorate: ->
      delete @toggleEditing # Unneeded action.
      @on 'will-init', =>
      @on 'did-init', => @initEditor()

    # Own
    # ---
    initEditor: ->
      @editorName = @data 'editor'
      switch @editorName
        when 'CodeMirror'
          opts = @data 'editor-options'
          opts.value = @$markup.text()
          location = (el) =>
            @$editor = el
            @$markup.replaceWith @$editor
          @editor = CodeMirror location, opts
        else didInit = no
      @bindEditor()
      didInit isnt no

    bindEditor: ->
      switch @editorName
        when 'CodeMirror'
          @editor.on 'blur', @handleValueChange
        else

  $.createMixin mixins, 'color-picker',

    # Action Overrides
    # ----------------

    # Onces
    # -----
    decorateOptions: ->
      @opts.selectors.well = '.color-well'
    decorate: ->
      @on 'will-commit', (e) => @renderColor e.userInfo.text
      @on 'did-init', => @initColorPicker()

    # Own
    # ---
    initColorPicker: ->
      _.bindAll @, 'handleColorPickerChange'
      @pickerName = @data 'color-picker'
      switch @pickerName
        when 'Spectrum'
          opts = @data 'color-picker-options'
          opts.color = "##{@$input.val()}"
          opts.change = @handleColorPickerChange
          @$well.spectrum opts
        else didInit = no
      @bindColorPicker()
      didInit isnt no

    bindColorPicker: ->

    handleColorPickerChange: (color) ->
      switch @pickerName
        when 'Spectrum' then color = color.toHexString().substring(1)
      @$input
        .val color
        .trigger 'change'

    # Actions
    # -------
    renderColor: (color) ->
      switch @pickerName
        when 'Spectrum' then @$well.spectrum 'set', color

  $.createMixin mixins, 'file-uploader',

    # Action Overrides
    # ----------------

    # Onces
    # -----
    decorateOptions: ->
      @opts.selectors.text = '.preview > figcaption'
      @opts.selectors.thumb = '.preview > .thumb'
    decorate: ->
      delete @toggleEditing # Unneeded action.
      @on 'will-commit', (e) =>
      @on 'did-init', => @initFileUploader()

    # Own
    # ---
    initFileUploader: ->
      @uploaderName = @data 'file-uploader'
      switch @uploaderName
        when 'jQueryFileUpload'
          opts = @data 'file-uploader-options'
          @$input.fileupload opts
        else didInit = no
      @bindFileUploader()
      didInit isnt no

    bindFileUploader: ->

    # Actions
    # -------

  plugin
