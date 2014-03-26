###
Exhibit Collection
###

Exhibit = Silexhibit.Exhibit
Section = Silexhibit.Section

class Exhibit.Collection extends Backbone.Collection

  # Inherited
  # ---------

  url: '/st-exhibit/'

  # Own
  # ---

  sections: null

  # Inherited
  # ---------

  initialize: (models, options) ->
    @model = Exhibit.Model
    @sections = new Section.Collection

  parse: (data, options) ->
    sections = _.chain(data)
      .pluck('section')
      .map(JSON.stringify)
      .unique()
      .map(JSON.parse)
      .value()
    @sections.add new Section.Model section for section in sections
    delete exhibit.section for exhibit in data
    #console.log data
    data

  # Own
  # ---

  sectionExhibits: (section) -> @where { section_name: section.get 'folder_name' }
  exhibitSection: (exhibit) -> @sections.findWhere { folder_name: exhibit.get 'section_name' }
