###
Item View
###

$ = jQuery
Plugin = Silexhibit.ExternalData
console = Plugin.console

class Plugin.ItemView extends Backbone.View

  # Inherited
  # ---------

  tagName: 'li'
  className: 'st-feed-item'
  template: -> @templates[@type] or @templates.generic

  # Own
  # ---

  type: null
  templates: Plugin.templates.item

  @pairs:
    url:
      pattern: /([a-z]+:\/\/[-_.\w\/]+)/g
      replacement: "<a class='inline' target='_blank' href='$1'>$1</a>"
    hashTag: (vars) ->
      pattern: /(#([-_\w]{2,}))/ig
      replacement: "<a class='inline' target='_blank' href='#{vars.baseurl}$2'>$1</a>"
    atMention: (vars) ->
      pattern: /(@([-_\w]+))/ig
      replacement: "<a class='inline' target='_blank' href='#{vars.baseurl}$2'>$1</a>"

  # Inherited
  # ---------

  initialize: (options) ->

  render: ->
    attrs = @model.attributes
    vars = switch @type
      # For models based off of xml, `_<attr>` means the attribute's an
      # attribute, not a node.
      when 'github'
        title: @replaceAll attrs.title.toString(), Plugin.options.userNames...
        date: @enrichedDate attrs.published
        link: if attrs.link then attrs.link._href else null
      when 'twitter'
        text: @replaceAll attrs.text,
          { name: 'url' },
          { name: 'hashTag', baseurl: 'http://twitter.com/search?q=%23' },
          { name: 'atMention', baseurl: 'http://twitter.com/' }
        link: "http://twitter.com/#{attrs.user.screen_name}/status/#{attrs.id_str}"
      when 'tumblr'
        title: _.string.prune (attrs.title or attrs.text), 35, '&hellip;'
        date: @enrichedDate attrs.date
        link: attrs.post_url
        note_count: attrs.note_count
        tags: @inlinedList attrs.tags
      when 'userscripts'
        title: attrs._title
        date: @enrichedDate attrs._script_updated_at
        install_count: attrs._installs
        link: "http://userscripts.org/scripts/show/#{attrs._id}"
      else
        html: if Plugin.delegate.renderItem then Plugin.delegate.renderItem.call(@, attrs) else { html: '' }
    html = @template() vars
    @$el.html html
    @

  # Own
  # ---

  setType: (feedName) ->
    return if feedName is @type
    @type = feedName

  replaceAll: (content, args...) ->
    #console.log args
    for arg in args
      if _.isString arg
        substr = arg
        content = content.replace substr, ''
      else
        pair = Plugin.ItemView.pairs[arg.name]
        if _.isFunction pair then pair = pair arg
        content = content.replace pair.pattern, pair.replacement
    content

  enrichedDate: (dateString) ->
    pub: dateString
    human: Plugin.date(dateString).fromNow()

  inlinedList: (list) ->
    truncated = list[0..2]
    truncated.join ', '

