function showNotice(obj, type, message){
    obj
        .text(message)
        .attr("class", type)
        .show()
        .animate({opacity: 0}, 0)
        .animate({opacity: 1}, 200)
        .animate({opacity: 1}, 2000)
        .animate({opacity: 0}, 500, function(){ obj.hide() })
    ;
}


String.prototype.replaceAll = function(search, replace){
    return this.split(search).join(replace);
}

$.fn.exists = function(){return ($(this).length > 0);}
$.exists = function(selector) {return ($(selector).length > 0);}

$.extend({
    getUrlVars: function(){
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++){
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getUrlVar: function(name){
        return $.getUrlVars()[name];
    },
    getMaxZ : function(){
        //alert(1);
    }
});

//jQuery.fn.extend({
//    getMaxZ : function(){
//        return Math.max.apply(null, jQuery(this).map(function(){
//            var z;
//            return isNaN(z = parseInt(jQuery(this).css("z-index"), 10)) ? 0 : z;
//        }));
//    }
//});

$.preloadImages = function () {
    if (typeof arguments[arguments.length - 1] == 'function') {
        var callback = arguments[arguments.length - 1];
    } else {
        var callback = false;
    }
    if (typeof arguments[0] == 'object') {
        var images = arguments[0];
        var n = images.length;
    } else {
        var images = arguments;
        var n = images.length - 1;
    }
    var not_loaded = n;
    for (var i = 0; i < n; i++) {
        jQuery(new Image()).attr('src', images[i]).load(function() {
            if (--not_loaded < 1 && typeof callback == 'function') {
                callback();
            }
        });
    }
}

//var loadieProgress = 0;
//$.ajaxSetup({
//    beforeSend: function(){
//        if($('body .loadie').length == 0){
//            $('body').loadie();
//        }
//        if(loadieProgress >= 0.9) {
//            $('.loadie').fadeIn();
//        }
//        loadieProgress = 0;
//        if($('body').loadie){
//            $('body').loadie(loadieProgress);
//        }
//        
//    },
//    complete: function(){
//        loadieProgress = 1;
//        if($('body').loadie){
//            $('body').loadie(loadieProgress);
//        }
//    }
//    
//});

function objInfo(obj, tab, level){
    var content = '';
    if(typeof tab == 'undefined')
        tab = "\t";
    for(var name in obj){
        var p = obj[name];
        
        var v = '';
        if(typeof p == 'object')
            if(level < 8)
                v = objInfo(p, tab+"\t", level+1);
            else
                v = '...';
        else
            v = p;
        
        content += tab + name + ": " + v +"\n";
    }
    
    return content;
}

function ucfirst (str) {
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}

