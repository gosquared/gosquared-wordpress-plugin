(function($) {
    $.addEventListener && $.addEventListener('DOMContentLoaded', function() {
    
        var target = $.getElementById('live-visitors'),
            endpoint = '?live_visitors';

        var fetch = function(callback) {
            var now = +(new Date).getTime();
            var fn = 'callback' + now;
            var script = $.createElement('script');
                script.src = endpoint + '&callback=' + fn;
                
            $.getElementsByTagName('head')[0].appendChild(script);
            
            //  Once we've grabbed it, fill in the text
            window[fn] = function(data) {
                target.childNodes[0].innerText = data.overview ? data.overview.active : 1; // you'll never have no visitors online
            };
            
            //  Garbage cleaning
            script.parentNode.removeChild(script);
            delete script;
        };
        
        fetch();
        setInterval(fetch, 2000);
    });
})(document);