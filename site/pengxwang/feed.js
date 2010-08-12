/**
 * Feed view controller
 * @author Peng Wang
 * @version 1
 * @requires jQuery 1.4+, jQuery.tmpl, feed API scripts, feed templates
 * @todo queue box-height animation after fadeOut before fadeIn
 */

;(function ($) {

$.pjp = $.pjp || {};
$.pjp.pager = {
    options: {
        
    }
};

    
})(jQuery); 
// check for dependencies
if (window.Site && Site.serviceUrl && Site.feedApiData && jQuery.tmpl) { 
(function ($) {

var buildFeeds = function (cb) {
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
            feeds.push($(feed).html());
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


var enhanceData = function (dao, name) {
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
                // http://twitter.com/
                // http://search.twitter.com/search?q=%23
                this.text = this.text
                    .replace(/(#([^#:\s]+))/g, '<a class="inline" href="http://search.twitter.com/search?q=%23$2">$1</a>')
                    .replace(/(@([^@:\s]+))/g, '<a class="inline" href="http://twitter.com/$2">$1</a>');
            });
            break;
    }
    return dao;
};

$(document).ready(function () {

    var $container = $('#content #ouFeed:eq(0) .in'),
        $trigger = $('a.jFeed:eq(0)', $container),
        $oldContent = $('.bd.mnLinked:eq(0)', $container),
        $feed,
        feedOn = function () {
            $trigger.find('.jOn').hide().end().find('.jOff').show();
            $oldContent.fadeOut('fast', function(){ $feed.fadeIn('fast') });
        },
        feedOff = function () {
            $trigger.find('.jOn').show().end().find('.jOff').hide();
            $feed.fadeOut('fast', function(){ $oldContent.fadeIn('fast') });
        },
        feedInit = function () {
            buildFeeds(function (html) {
                $feed = $(html).appendTo($container);
                console.log($feed);
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

});

})(jQuery); 
}

