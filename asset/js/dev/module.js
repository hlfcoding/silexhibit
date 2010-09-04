if (typeof Object.inherit !== 'function') {
(function () {
    function subClass () {};
    Object.inherit = function (superClass) {
        subClass.prototype = new superClass;
        return new subClass();
    };
})();
}
if (window.jQuery) {
    window.org = jQuery.extend(true, window.org || {}, {
        'indexhibit': { 'classes': {}, 'pages': {}, 'options': {}, 'globals': {} }
    });
(function ($, nsC, nsP, nsO, nsG) { // jQuery, namespaced: classes, pages, options
    $.fn.module = function () {
        return this.data(this.data('className'));
    };
    $.module = function (name) {
        $.fn[name] = function (opt) {
            if (this.length) {
                return this.each(function () {
                    var instance = $.data(this, name),
                        className = name[0].toUpperCase() + name.slice(1);
                    if (instance) { 
                        instance[opt].apply(instance, opt);
                    } else {                        
                        opt = $.extend({}, nsO[name], opt);
                        instance = Object.inherit(nsC[className]);
                        instance.init(this, opt);
                        $.data(this, name, instance)
                        $.data(this, 'className', name);
                    }
                });
            }
        };
    };
})(jQuery, org.indexhibit.classes, org.indexhibit.pages, org.indexhibit.options, org.indexhibit.globals); }