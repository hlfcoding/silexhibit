/**
 * Feed view controller
 * @author Peng Wang
 * @version 1
 * @requires jQuery 1.4+, jQuery.tmpl, feed API scripts, feed templates, cookie plugin
 * @todo queue box-height animation after fadeOut before fadeIn
 * @todo write tests
 */
if (window.jQuery) {
(function ($) {

$.pjp = $.pjp || {};
$.pjp.pager = {
    options: {
        
    }
};

})(jQuery); 
// check for dependencies
if (window.Site) { 
(function ($, $$) {

$$.fn = $.extend($$.fn || {}, {
    'feed': {}
});

var buildFeed = $$.fn.feed.build = function (cb) {
    var nCompleted = 0, 
        nToComplete = 0,
        feeds = [],
        result;
    $.each(Site.feedApiData, function (name, fields) {
        var serviceUrl = Site.serviceUrl + '/feed.' + name + '.php?' + $.param(fields),
            feed;
        nToComplete += 1;
        $.getJSON(serviceUrl, function (dao) {
            dao = enhanceData(dao, name);
            feed = $('#tpl-' + name + '-feed').render(dao);
            feeds[$$.feedOrder.indexOf(name)] = ($(feed).html());
            nCompleted += 1;
            if (nCompleted === nToComplete) {
                result = $('#tpl-feeds-container').render({
                    'feeds': feeds.join('\n')
                });
                cb($(result).html());
            }
        });
    });
};
/**
 * @todo hard-coded github handle
 * @todo hackish handle replace in title
 */
var enhanceData = $$.fn.feed.enhance = function (dao, name) {
    switch (name) {
        case 'posterous':
            $.each(dao.post, function () {
                // create accessors
                $.extend(this, {
                    humanDate: function () {
                        var d = new Date(this.date);
                        return d.toDateString().replace(/\d{4}$/, '');
                    },
                    tags: function () {
                        return this.tag.join(', ');
                    }
                });
            });
            break;
        case 'twitter':
            $.each(dao.status, function () {
                this.text = this.text
                    .replace(/([a-z]+:\/\/[-_.\w\/]+)/g, '<a class="inline" href="$1">$1</a>')
                    // tags: http://search.twitter.com/search?q=%23
                    .replace(/(#([-_\w]{2,}))/ig, '<a class="inline" href="http://search.twitter.com/search?q=%23$2">$1</a>')
                    // mentions: http://twitter.com/
                    .replace(/(@([-_\w]+))/ig, '<a class="inline" href="http://twitter.com/$2">$1</a>');
            });
            break;
        case 'github':
            dao.entry = dao.entry.slice(0, $$.feedApiData.github['custom_count']);
            $.each(dao.entry, function () {
                var $content = $(this.content);
                $('a.committer, a.compare-link, br:last', $content)
                    .remove();
                $('br', $content)
                    .replaceWith('<span class="delim">|</span>');
                $('*:not(span)', $content)
                    .addClass('inline');
                $('.message', $content)
                    .addClass('meta');
                this.content = $content.html();
                this.title = this.title.replace('hlfcoding ', '');
            });
            break;
    }
    return dao;
};

$(document).ready(function () {

    var $container = $('#content #ouFeed:eq(0) .in'),
        $trigger = $('a.jFeed:eq(0)', $container),
        $oldContent = $('.bd.mnLinked:eq(0)', $container),
        $remember = $trigger.clone().hide()
            .toggleClass('jFeed jFeedRemember')
            .find('.jOn').html('stay').end()
            .find('.jOff').html('leave').hide().end()
            .insertBefore($trigger), 
        cookieName = 'pengxwang_feed_preferred',
        cookieValue = $.cookie(cookieName),
        cookieOptions = {
            'domain': window.location.hostname,
            'path': window.location.pathname.substr(1),
            'secure': (window.location.protocol == 'https')
        },
        $feed,
        feedOn = function () {
            $trigger.find('.jOn').hide().end().find('.jOff').show();
            $oldContent.fadeOut('fast', function () { 
                $feed.fadeIn('fast'); 
                $remember.fadeIn('fast');
            });
        },
        feedOff = function () {
            $trigger.find('.jOn').show().end().find('.jOff').hide();
            $feed.fadeOut('fast', function () { 
                $oldContent.fadeIn('fast');
                $remember.fadeOut('fast');
            });
        },
        feedInit = function () {
            buildFeed(function (html) {
                $feed = $(html).appendTo($container);
                feedOn();
            });
        };
        
    $trigger.bind('click', function (evt) {
        evt.preventDefault();
        if ($('.jOn:visible', $trigger).length > 0) {
            if ($feed === undefined) {
                feedInit();
            } else {
                feedOn();
            }
        } else {
            feedOff();
        }
    });
    /**
     * The logic here was near impossible
     */
    if (cookieValue === null) {
        $.cookie(cookieName, false, cookieOptions);
    }
    if ($.cookie(cookieName) === 'true') {
        $trigger.trigger('click');
        $remember.find('.jOn').hide().end()
            .find('.jOff').show();
    }
    $remember.bind('click', function (evt) {
        evt.preventDefault();
        cookieValue = $.cookie(cookieName);
        $.cookie(cookieName, (cookieValue === 'true') ? false : true, cookieOptions);
        $remember
            .find('.jOn')[(cookieValue === 'true') ? 'show' : 'hide']().end() // back on
            .find('.jOff')[(cookieValue === 'true') ? 'hide' : 'show'](); // back off
    });
});

})(jQuery, Site); 
}}