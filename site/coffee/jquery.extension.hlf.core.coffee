###
HLF Core jQuery Extension v1.0
Released under the MIT License
Written with jQuery 1.7.2
###
$ = jQuery

if not _? then throw "UnderscoreJS required."

_.templateSettings = interpolate: /\{\{(.+?)\}\}/g

$.hlf =
  toString: -> 'hlf'
  createPlugin: (ns, apiClass) ->
    ns.apiClass = apiClass
    return (opt, $ctx) ->
      $ctx ?= $ 'body'
      # - Try returning existing plugin api if no options are passed in.
      api = @first().data ns.toString()
      return api if api? and not opt?
      # - Re-apply plugin.
      opt = $.extend (deep=on), {}, ns.defaults, opt
      return @each ->
        $el = $(@).addClass ns.toString 'class'
        apiClass::_evt ?= (name) -> "#{name}#{ns.toString 'event'}"
        apiClass::_dat ?= (name) -> "#{ns.toString 'data'}#{name}"
        $el.data ns.toString(), new apiClass $el, opt, $ctx
      
    
  