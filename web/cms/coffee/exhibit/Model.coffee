###
Exhibit Model
###

Exhibit = Silexhibit.Exhibit

class Exhibit.Model extends Backbone.Model

  # Inherited
  # ---------

  urlRoot: '/st-exhibit/'

  # Own
  # ---

  @selectableKeys = [
    'format'
    'max_image_size'
    'thumbnail_size'
  ]

  # Inherited
  # ---------

  toJSON: ->
    data = super
    if @has 'section'
      data.section = @get('section').attributes
      data.section.title = data.section.name
    data.is_published = !!data.status
    data

  validate: (attributes, options) ->

  # Own
  # ---

  backgroundImageUrl: -> "#{@urlRoot}#{@id}/background-image"

  getWithOptions: (attributeOptionsKey) ->
    model = @
    attributeKey = _.singularize attributeOptionsKey
    attributeOptions = for attributeOption in app.model.getExhibit attributeOptionsKey
      attributeName = if _.isObject attributeOption then attributeOption.name else attributeOption
      attributeValue = if _.isObject attributeOption then attributeOption.value else attributeOption
      name: attributeName
      value: attributeValue
      selected: `attributeValue == model.get(attributeKey)`

  saveChanged: (externalAttributes, options) ->
    changedAttributes = @changedAttributes externalAttributes
    if @isNew()
      $xhr = @save()
    else if changedAttributes isnt no
      changed = _.extend {}, changedAttributes, { id: @id }
      options = _.extend {}, options, { patch: yes }
      $xhr = @save changed, options
    $xhr
