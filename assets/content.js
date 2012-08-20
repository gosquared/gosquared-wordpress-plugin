(function() {
    var ready = function() {
        var target = $('.top-content-widget');
        var grab = function() {
            $.get('?grab=top_content', function(data) {
                if(data && data.indexOf('li') !== -1) {
                    target.replaceWith(data);
                }
            });
        };
        
        grab();
        setInterval(grab, 2000);
        
        return true;
    },
    
    hasjQuery = false;
    
    //  Only works with jQuery, unfortunately
    if(window.jQuery) {
        hasjQuery = true;
        ready.call(window.jQuery);
    } else {
        var w = function() {
            if(hasjQuery) {
                clearTimeout(interval);
                return;
            } else {
                if(window.jQuery) {
                    hasjQuery = true;
                    ready.call(window.jQuery);
                }
            }
        }
        
        var interval = setInterval(w, 100);
    }
})();