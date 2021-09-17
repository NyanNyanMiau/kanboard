KB.on('dom.ready', function() {
    function tooltip(mytarget)
    {
    	if ( mytarget.__target && document.contains(mytarget.__target) ) return true;

    	// remove other same target tooltip
    	mytarget.__destroy = function()
    	{
    		if ( mytarget.__onChild && document.contains(mytarget.__onChild) ){
    			return false;
    		}
    		// remove
            if (mytarget.__target) {
            	mytarget.__target.parentNode && mytarget.__target.parentNode.removeChild(mytarget.__target);
            	mytarget.__target = null;
            }
        }
    	mytarget.__onTooltip = false;
    	mytarget.__onChild = false;
    	mytarget.__mouseOnTooltip = function() {
    		mytarget.__onTooltip = true;
        }

        var containerElement = document.createElement("div");
        containerElement.__opener = mytarget;
        containerElement.addEventListener("mouseleave", mytarget.__destroy, false);
        containerElement.addEventListener("mouseenter", mytarget.__mouseOnTooltip, false);
        containerElement.classList.add('tooltip-container');

        mytarget.__target = containerElement;

    	// have open container
    	var containers = document.getElementsByClassName("tooltip-container");
    	if ( containers.length ) {
    		// remove containers where opener is not part of
    		$.each(containers, function(idx, container){
    			if ( mytarget.__target && mytarget.__target === container ){
    				return true;
    			}
    			if ( ! (container && container.contains( mytarget )) ) {
    				container.__opener.__destroy();
    			}else{
    				container.__opener.__onChild = containerElement;
    			}
    		});
    	}

        create(mytarget);
        
        document.body.appendChild(containerElement);

        
        mytarget.addEventListener('mouseout', function(e) {
        	if ( !mytarget.__onTooltip ) return false;
            setTimeout(function() {
            	mytarget.__destroy();
            }, 500);
        });
    }


    
    function create(element) {
        var contentElement = element.querySelector("script");
        if (contentElement) {
            render(element, contentElement.innerHTML);
            return;
        }

        var link = element.dataset.href;
        if (link) {
            fetch(link, function (html) {
                if (html) {
                    render(element, html);
                }
            });
        }
    }

    function fetch(url, callback) {
        var request = new XMLHttpRequest();
        request.open("GET", url, true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.onreadystatechange = function () {
            if (request.readyState === XMLHttpRequest.DONE) {
                if (request.status === 200) {
                    callback(request.responseText);
                }
            }
        };
        request.send(null);
    }

    function render(element, html) {
        var containerElement = element.__target;
        if (!containerElement) return false;
        containerElement.innerHTML = html;

        var elementRect = element.getBoundingClientRect();
        var top = elementRect.top + window.scrollY + elementRect.height;
        containerElement.style.top = top + "px";

        if (elementRect.left > (window.innerWidth - 600)) {
            var right = window.innerWidth - elementRect.right - window.scrollX;
            containerElement.style.right = right + "px";
        } else {
            var left = elementRect.left + window.scrollX;
            containerElement.style.left = left + "px";
        }
    }

    // for dynamically added elements, we add our event listeners to the doc body
    // we need to use mouseover, because mouseenter only triggers on the body in this case
    document.body.addEventListener('mouseover', function(e) {
        if (e.target.classList.contains('tooltip')) {
            tooltip(e.target);
        }
        // to catch the case where the event doesn't fire on tooltip but on the i-subelement
        //    (this seems to depend on how you move your mouse over the element ...)
        if (e.target.classList.contains('fa') && e.target.parentNode.classList.contains('tooltip')) {
        	tooltip(e.target.parentNode);
        }
    });
    
    document.body.addEventListener('click', function(event) {
    	var c = document.getElementsByClassName("tooltip-container");
    	if (c.length){
    		// remove all
    		$.each(document.getElementsByClassName("tooltip-container"), function(idx, el){
    			if ( ! (el && el.contains(event.target)) ) {
    				if ( el && el.__opener){
	   					el.__opener && el.__opener.__destroy();
    				}
    			}
    		});
    	}
    });

});
