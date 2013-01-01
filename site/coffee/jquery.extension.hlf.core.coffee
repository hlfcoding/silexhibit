# 
# HLF Core jQuery Extension v1.0  
# Released under the MIT License  
# Written with jQuery 1.7.2  
#
$ = jQuery

if not _? then throw "UnderscoreJS required."

_.templateSettings = interpolate: /\{\{(.+?)\}\}/g

$.hlf =
  createPlugin: (ns, apiClass, asSingleton=no) ->
    ns.apiClass = apiClass
    nsEvt = ns.toString 'event'
    nsDat = ns.toString 'data'
    return (opt, $ctx) ->
      $el = null # Set to right scope.
      boilerplate = ->
        $root = if asSingleton is no then $el else $ctx
        $root.addClass ns.toString 'class'
        apiClass::_evt ?= (name) -> "#{name}#{nsEvt}"
        apiClass::_dat ?= (name) -> "#{nsDat}#{name}"
        apiClass::_log ?= if ns.debug is on then $.hlf.log else $.noop
        apiClass::_nsLog ?= ns.toString 'log'
        $root.data ns.toString(), new apiClass $el, opt, $ctx
      
      $ctx ?= $ 'body'
      # - Try returning existing plugin api if no options are passed in.
      api = @first().data ns.toString()
      return api if api? and not opt?
      # - Re-apply plugin.
      opt = $.extend (deep=on), {}, ns.defaults, opt
      
      if asSingleton is no
        return @each ->
          $el = $(@)
          boilerplate()
        
      else
        $el = @
        boilerplate()
      
    
  
  debug: on # Turn this off when going to production.
  toString: -> 'hlf'

$.hlf.log = if $.hlf.debug is off then $.noop else
  (if console.log.bind then console.log.bind(console) else console.log)
