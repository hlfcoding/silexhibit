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

/**
 * @todo xml and json conflict with headers in php 
 */
var buildFeed = $$.fn.feed.build = function (cb) {
    var nCompleted = 0, 
        nToComplete = 0,
        feeds = [],
        result;
    $.each(Site.feedApiData, function (name, fields) {
        var serviceUrl = Site.serviceUrl + '/feed.' + name + '.php',
            feed;
        fields = $.extend({}, fields);
        $.each(fields, function (key, value) {
            if (/^custom_/.test(key)) {
                delete fields[key];
            }
        });
        nToComplete += 1;
        $.ajax({
            'dataType': $$.feedApiData[name]['custom_as_xml'] ? 'html' : 'json',
            'type': 'GET',
            'data': decodeURIComponent($.param(fields, true)),
            'url': serviceUrl, 
            'success': function (data, status, xhr) {
                data = enhanceData(data, name);
                if (data && data.item && !data.item.length) { return; }
                feed = $('#tpl-' + name + '-feed').render(data);
                feeds[$$.feedOrder.indexOf(name)] = ($(feed).html());
            },
            'error': function (status, xhr, error) {},
            'complete': function (xhr, status) {
                nCompleted += 1;
                if (nCompleted === nToComplete && !!feeds.length) {
                    result = $('#tpl-feeds-container').render({
                        'feeds': feeds.join('\n')
                    });
                    cb($(result).html());
                }
            }
        });
    });
};
/**
 * @todo hard-coded github handle
 * @todo hackish handle replace in title
 */
var enhanceData = $$.fn.feed.enhance = function (data, name) {
    switch (name) {
        case 'posterous':
            $.each(data.post, function () {
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
            $.each(data.status, function () {
                this.text = this.text
                    .replace(/([a-z]+:\/\/[-_.\w\/]+)/g, '<a class="inline" target="_blank" href="$1">$1</a>')
                    // tags: http://search.twitter.com/search?q=%23
                    .replace(/(#([-_\w]{2,}))/ig, '<a class="inline" target="_blank" href="http://search.twitter.com/search?q=%23$2">$1</a>')
                    // mentions: http://twitter.com/
                    .replace(/(@([-_\w]+))/ig, '<a class="inline" target="_blank" href="http://twitter.com/$2">$1</a>');
            });
            break;
        case 'github':
            data.entry = data.entry.slice(0, $$.feedApiData[name]['custom_count']);
            $.each(data.entry, function () {
                var $content = $(this.content);
                $('a.committer, a.compare-link, br:last', $content)
                    .remove();
                $('br', $content)
                    .replaceWith('<span class="delim">|</span>');
                $('*:not(span)', $content)
                    .addClass('inline');
                $('.message', $content)
                    .addClass('meta');
                $('a', $content).attr('target', '_blank');
                this.content = $content.html();
                this.title = this.title.replace('hlfcoding ', '');
            });
            break;
        case 'deviantart':
            var $items = $(data).find('item').slice(0, $$.feedApiData[name]['custom_count']);
            data = {'item': $items.map(function (idx, elm) {
                var $item = $(elm),
                    // hard-coded specs
                    $thumb = $('[url]:eq(2)', $item),
                    date = $('pubDate', $item).text();
                // debugger;
                // DA's xml formatting is really something
                return {
                    'title': $('[type="plain"]', $item).text(),
                    'permalink': $('guid[ispermalink="true"]', $item).text(),
                    'thumb': {
                        'url': $thumb.attr('url'),
                        'width': $thumb.attr('width'),
                        'height': $thumb.attr('height')
                    },
                    // 'description': $('description', $item).html(),
                    'date': {
                        'pub': date,
                        'human': (new Date(date)).toDateString()
                    }
                };
            }).get() };
            break;
    }
    return data;
};

$(document).ready(function () {

    var $container = $('#content #ouFeed:eq(0) .in'),
        $trigger = $('a.jFeed:eq(0)', $container),
        $oldContent = $('.bd.mn-v-a:eq(0)', $container),
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