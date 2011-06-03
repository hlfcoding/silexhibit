/**
 * PJP Indexhibit Menu Navigation
 * @author      peng@pengxwang.com
 * @param       {jQuery object} set of matched elements
 * @version     1.0
 * @requires    jQuery 1.2+
 */

(function ($)
{
    // register plugin
    $.fn.pjpNAccordion = function (options) 
    {
        var name = 'pjpna',
            defaults = 
            { selector: 
                { classEnabled: name
                , classButton: name + 'Button'
                , classExpanded: name + 'Expanded'
                , classCollapsed: name + 'Collapsed'
                , classDisabled: name + 'Disabled'
                , header: 'li.section-title'
                , menu: 'ul:not(ul ul)'
                , item: 'li'
                , currentItem: 'li.active'
                }
            , element: 
                { button: 
                    { wrapper: '<a href="#"></a>'
                    , expand: '+'
                    , collapse: '&ndash;'
                    , title: {more: 'more work', less: 'less work'}
                    }
                }
            , num: 
                { show_base: 2
                , show_custom: {}
                }
            , autoCollapse: false
            };
        options = $.extend(true, defaults, options);
        return new $.PJPNAccordion(options, $(this));
    };
    
    // plugin object
    $.PJPNAccordion = function (options, $container) 
    {
        // stores
        var   $menus = $container.find(options.selector.menu)
            , $headers = $menus.find(options.selector.header)
            , $hiddens = []
            , $buttons = $()
            ;
        // functions
        var cb_toggle = function(event) 
        {
            // vars
            var index = $buttons.index(event.target);
            var expand = $hiddens[index].is(':hidden') ? true : false;
            // start
            $hiddens[index].toggle();
            hideHiddenExcept(index);
            $menus.eq(index).attr('class', (expand ? options.selector.classExpanded : options.selector.classCollapsed));
            updateButton($(event.target), expand);
        };  
        var updateButton = function($button, expanded) 
        {
            $button
                .html((expanded ? options.element.button.collapse : options.element.button.expand))
                .attr('title', (expanded ? options.element.button.title.less : options.element.button.title.more))
                .trigger($.Event('title_changed'));
        };
        var hideHiddenExcept = function(index) 
        {
            if (options.autoCollapse) 
            {                
                for (var i = 0; i < $hiddens.length; i++) 
                {
                    if (i != index) 
                    {
                        $hiddens[i].filter(':visible').hide();
                        $buttons.eq(i)
                            .html(options.element.button.expand)
                            .attr('title', options.element.button.title.more)
                            .trigger($.Event('title_changed'));
                    }
                }
            }
        };
        // procedure
        $container.addClass(options.selector.classEnabled);
        $menus.each(function (index) 
            {
                // init
                var show_num = (options.num.show_custom[(index + 1).toString()] != null)
                    ? options.num.show_custom[(index + 1).toString()]
                    : options.num.show_base;
                var html_button = $(options.element.button.wrapper)
                    .html(options.element.button.expand)
                    .attr(
                        { 'title': options.element.button.title.more
                        , 'class': options.selector.classButton
                        }
                    );
                var $menu = $(this);
                    $menu.hasCurrentItem = $menu.find(options.selector.currentItem).length;
                var $button = $headers.eq(index).append(html_button).find('.' + options.selector.classButton);
                $hiddens[index] = $menu.children('li:gt(' + show_num + ')');
                if ( ! options.autoCollapse 
                    || (options.autoCollapse && ! $menu.hasCurrentItem)) 
                {
                    $hiddens[index].hide();
                    if ($hiddens[index].length == 0) 
                    {
                        $menu.addClass(options.selector.classDisabled);
                    }
                }
                else if ($menu.hasCurrentItem) // still expanded
                {
                    updateButton($button, true);
                }
                $menu.addClass(options.selector.classCollapsed);
                // event
                $button
                    .filter(':not(.' + options.selector.classDisabled + ') .' + options.selector.classButton)
                    .click(cb_toggle);
            }
        );
        $buttons = $menus.find('.' + options.selector.classButton);
        return $container;
    };
}
)(jQuery);