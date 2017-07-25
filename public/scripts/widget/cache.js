(function ($) {
    $.cache = {
        storage : window.sessionStorage,
        set : function (key, value) {
            value = JSON.stringify(value);
            this.storage ? this.storage.setItem(key,value) : null;
        },
        get : function (key) {
            var value = this.storage ? this.storage.getItem(key) : null;
            if(value){
                value = JSON.parse(value);
            }
            return value;
        },
        remove : function (key) {
            this.storage ? this.storage.removeItem(key) : null;
        },
        clear : function () {
            this.storage ? this.storage.clear() : null;
        }
    };
    $.localCache = {
        storage : window.localStorage,
        set : function (key, value) {
            value = JSON.stringify(value);
            this.storage ? this.storage.setItem(key,value) : null;
        },
        get : function (key) {
            var value = this.storage ? this.storage.getItem(key) : null;
            if(value){
                value = JSON.parse(value);
            }
            return value;
        },
        remove : function (key) {
            this.storage ? this.storage.removeItem(key) : null;
        },
        clear : function () {
            this.storage ? this.storage.clear() : null;
        }
    };

})($);


