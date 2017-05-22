$ = jQuery

if 'appName' of Silexhibit then Silexhibit.appName = 'site'
ST = Silexhibit
ST.setupVideoSlideshowCoordination = ->

  numPlaying = 0
  $vimeos = $('iframe[src^="//player.vimeo.com"]')
  $vimeos.each ->
    player = Froogaloop @
    player.addEvent 'ready', =>
      player.addEvent 'pause', (player_id) ->
        numPlaying--
        if numPlaying is 0
          ST.Slideshow.delegate.events.trigger 'resume'
      player.addEvent 'play', (player_id) ->
        numPlaying++
        if numPlaying is $vimeos.length
          ST.Slideshow.delegate.events.trigger 'suspend'

ST.setupVideoSlideshowCoordination()
