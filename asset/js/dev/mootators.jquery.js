Class.Mutators.jQuery = function(name){
    var self = this;
    jQuery.fn[name] = function(arg){
        var instance = this.data(name);
        if (typeof arg === 'string'){
            var prop = instance[arg];
            if (typeof prop === 'function'){
                var returns = prop.apply(instance, Array.slice(arguments, 1));
                return (returns == instance) ? this : returns;
            } else if (arguments.length == 1){
                return prop;
            }
            instance[arg] = arguments[1];
        } else {
            if (instance) return instance;
            this.data(name, new self(this.selector, arg));
        }
        return this;
    };
};