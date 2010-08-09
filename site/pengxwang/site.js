// jQuery.noConflict(); // because we're in indexhibit // but won't work with jquery.template.js
Modernizr.run_tests();
var Site = Site || {};

jQuery(document).ready(function ($) {
    $('#navigation').pjpNAccordion({
        'autoCollapse': true,
        'num': {
            'show_custom':{1:1, 2:1, 3:1, 4:1, 5:1}
        }
    });
    var opt = {
        'tip_class': 'pjpTip',
        'stem_class': 'pjpTipStem'
    };
    $('[title]').pjpTip(opt);
    $('*').bind('title_changed', function (evt) {
        $(evt.target).pjpTip(opt);
    });
    $('.jPrint').bind('click', function (evt) {
        window.print();
        evt.preventDefault();
    });
});