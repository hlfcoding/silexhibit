// Generated by CoffeeScript 1.3.3
(function() {
  var $;

  $ = jQuery;

  $.extend(true, $.hlf, {
    hoverIntent: {
      debug: true,
      sensitivity: 8,
      interval: 300,
      toString: function(context) {
        switch (context) {
          case 'data':
            return 'hlfHoverIntent';
          case 'log':
            return 'hover-intent:';
          default:
            return 'hlf.HoverIntent';
        }
      }
    },
    mouse: {
      x: {
        current: 0,
        previous: 0
      },
      y: {
        current: 0,
        previous: 0
      }
    }
  });

  (function(ns, m) {
    var check, dat, log, nsDat, nsLog, track;
    nsDat = ns.toString('data');
    nsLog = ns.toString('log');
    dat = function(name) {
      return "" + nsDat + name;
    };
    log = ns.debug === true ? $.hlf.log : $.noop;
    check = function(evt) {
      var $t, intentional, interval, sensitivity, timer;
      $t = $(this);
      intentional = $t.data(dat()) || true;
      timer = $t.data(dat('Timer')) || {
        cleared: false,
        timeout: null
      };
      sensitivity = $t.data(dat('Sensitivity')) || ns.sensitivity;
      interval = $t.data(dat('Interval')) || ns.interval;
      if (evt.type === 'mouseleave') {
        if (timer.cleared === false) {
          clearTimeout(timer.timeout);
          $t.removeData(dat('Timer')).removeData(dat());
        }
        $t.trigger('truemouseleave');
        log(nsLog, 'truemouseleave');
        return;
      }
      if (timer.cleared === false && (timer.timeout != null)) {
        return;
      }
      timer.timeout = setTimeout(function() {
        intentional = Math.abs(m.x.previous - m.x.current) + Math.abs(m.y.previous - m.y.current) > sensitivity;
        intentional = intentional;
        m.x.previous = evt.pageX;
        m.y.previous = evt.pageY;
        if (intentional === true && evt.type === 'mouseover') {
          $t.trigger(new $.Event('truemouseenter', {
            pageX: m.x.current,
            pageY: m.y.current
          }));
          log(nsLog, 'truemouseenter');
        }
        timer.cleared = true;
        $t.data(dat(), intentional);
        return $t.data(dat('Timer'), timer);
      }, interval);
      timer.cleared = false;
      return $t.data(dat('Timer'), timer);
    };
    track = function(evt) {
      m.x.current = evt.pageX;
      return m.y.current = evt.pageY;
    };
    $.event.special.truemouseenter = {
      setup: function(data, namespaces) {
        return $(this).on({
          mouseover: check,
          mousemove: track
        });
      },
      teardown: function(data, namespaces) {
        return $(this).off({
          mouseover: check,
          mousemove: track
        });
      }
    };
    return $.event.special.truemouseleave = {
      setup: function(data, namespaces) {
        return $(this).on({
          mouseleave: check
        });
      },
      teardown: function(data, namespaces) {
        return $(this).off({
          mouseleave: check
        });
      }
    };
  })($.hlf.hoverIntent, $.hlf.mouse);

}).call(this);
