var presentationCycle = {
    
    /*
     * Presentation Cycle - a jQuery Cycle extension
     * Author:  Gaya Kessler
     * URL:     http://www.gayadesign.com
     * Date:	03-11-09
     */
    
    //slide options
    slideTimeout: 8000,
    containerId: "presentation_container",
    
    //cycle options
    cycleFx: 'scrollHorz',
    cycleSpeed: 600,  
    
    //progressbar options
    barHeight: 14,
    barDisplacement: 20,
    barImgLeft: "images/pc_item_left.gif",
    barImgRight: "images/pc_item_right.gif",
    barImgCenter: "images/pc_item_center.gif",
    barImgBarEmpty: "images/pc_bar_empty.gif",
    barImgBarFull: "images/pc_bar_full.gif",
    
    //variables this script need
    itemCount: 0,
    currentItem: 0,
    itemBarWidth: 0,
    barContainer: "",
    barContainerActive: "",
    barContainerOverflow: "",
    disableAnimation: false,
    
    init: function() {
        
        presentationCycle.itemCount = $('#' + presentationCycle.containerId).children().length;

        presentationCycle.barContainer = $("<div></div>");
        $(presentationCycle.barContainer).addClass("pc_bar_container");
        
        var subtrackSpace = (presentationCycle.itemCount * presentationCycle.barHeight);
        var totalWidth = $('#' + presentationCycle.containerId).innerWidth() - presentationCycle.barDisplacement;
        var fillWidth = Math.floor((totalWidth - subtrackSpace) / (presentationCycle.itemCount - 1));
        presentationCycle.itemBarWidth = fillWidth;
        
        for (var i = 0; i < presentationCycle.itemCount; i++) {
            var item = $("<div>&nbsp;</div>").appendTo(presentationCycle.barContainer);
            var extra_bar = true;
            if (i == 0) {
                $(item).addClass("left");
                $(item).css({
                    backgroundImage: "url(data:image/jpg;base64,R0lGODlhHAAOAOYAAH9/fzw8PFtbWwEBAYiIiBUVFWNjYwkJCQICAlBQUF1dXZKSkgoKCgMDAzk5OWtra4uLi2dnZ29vb09PUGxsbE9PT5GRkURERD0+PlBPTzIyMh8fH25ublVVVSEhIWpqaoSEhIWFhUZGRjo6OkBAQIqKihcXFzo5OoKCgnJycnh4eFdXV09QT11cXFFRUUFBQU1NTTk6OYeHh2ZmZlxdXVBQTz9AQAgICCkpKYODgxAQEHR0dE5OTn19fVxcXD09PXR1dXJxcZCQkI6Oj2RjY1lZWVpaWo2NjQQEBD0+PRISEmJiYl5eXnFxcV1cXT09Pjk6Oo6Ojj4+PXZ2dnV1dTc3NyoqKnNzc2FhYT4+Pzo6O4yMjF1dXFRUVG1tbWRkZCUlJURERVNTU4GBgUdHRz8/Py0tLZCQkQwMDIyMjVhYWD4+PjY2NklJSUNDQ1ZWVgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAcAA4AAAf/gHBwDRoRIASIEi8MgoKEhogEioyCB28WS2Y6aGBiJR8FlW8LIVMcFDtjFqBwCDAQawONglYADwyuZyoGBkS8BkFHtxtbZLOzOCUuG0MqXApOLS00CgoUQi4XVDfHs24PFzJFb+Tl5Sk5Agnd3QJAGRUVEyw1CSwT8hkCDuzHAjNtAgocGFDAj36zBHxxw7ChQ4Yr1CCEMwDJiiYY1mB4sqZjkjVSstgIAEBJPwRdUgSQEeaEAwcxHECJcUILBQAFUBhBwK6MBRI5JVRhQ7QoGyYLSMAZsUBNqEYHLkDo0GDpAg5ujAbAEoUqRZI93qwJwOMKhAQHBA0gGcKLAh8RFgCkQTvLxJo3avKK8CCr7t28avb2DQQAOw==)",
                    height: presentationCycle.barHeight + "px",
                    width: presentationCycle.barHeight + "px"
                });
            } else if (i == (presentationCycle.itemCount - 1)) {
                $(item).addClass("right");
                $(item).css({
                    backgroundImage: "url(data:image/jpg;base64,R0lGODlhHAAOAOYAAFlZWWNjYxoaGmdnZ4KCgnp6ejk5OXx8fC0tLWpqan9/f3BwcFBQUE9PTz4+Pi8vL3l5eXZ2dmVlZWFhYRsbG46Ojjo5OVBQTzg4OISEhF1cXQoKCnV1dYeHh4CAgIuLi4GBgYWFhYaGhkJCQm5ubk9QTzExMVVVVVJSUlxcXBQUFCoqKklISQUFBTAwMAMDAz4+PT09PT49PicnJ1FRUQkJCR8fHyMjI1dXVyEhIQcHB2hoaF9fXk9QUI2NjUA/QF1cXF1dXRISEk9PUHNzczo5OnFxcUxMTH5+fiYmJoWGhW1tbWxsbF1dXGBgYBUVFTs7Ow4ODoyMjD0+PlZWVRMTE05OTh4eHo+PkERERHt7e2RkZEBAQGJiYkhISFBPT2RjYwYGBltbWz49PVNTU0dHRxgYGIODgw8PD1RUVD8/P1xdXEZGRjk6OoqKio+Pj1ZWVklJSTY2NkNDQwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAcAA4AAAf/gHSCNjgHbocFKBSCjISGiIqMdBghHDFmGwIjEAoIjJQfWgtMCwdSnYIrFSloknRPA243dKoEOwG4YAEJSrIuEQlhroIbRhK/GTxAGilrTRpBEx0SCVtXw4wPIgQeXXDf4OAJItiuUW8NJUMlFw0XF18NDD1Ub+WSLW9x+/z7LHH/jti7JyjfnIMIEx5k8+bFC4JC3vzgIiOGg4swYMiYMsbLmwVLPJVTQwDJgTQGirQxYMCChZZOCMwYUKAKNgoeTszMMEKOz59yynQ4QUcACCJJXCEoAEFFURAKrACVQ+ZMU0E5SHwIMAdKFgk+BghglLUChwliJkTAIlZSDRM0DgDIZfBAh6u3cefWFRQIADs=)",
                    height: presentationCycle.barHeight + "px",
                    width: presentationCycle.barHeight + "px"
                });
                extra_bar = false;
            } else {
                $(item).addClass("center");
                $(item).css({
                    backgroundImage: "url(data:image/jpg;base64,R0lGODlhHAAOAOYAAD4+PoyMjFlZWSsrK2NjY319fTk5OQgICAMDA1BQT2lpaVxcXXV1dT49PTk5Ok9QT0hISIODg1FRUUtLS3h4eE9PT3l5eVxcXBEREWJiYh4eHikpKRQUFHx8fA8PD2BgYIGBgW1tbTc3Nx8fHzk6Ojg4OGVlZTs7O09PUC4uLnJyclVVVVtbW11dXBAQECYmJmtra4aGho+Pj3BwcH9/fwICAl1dXWxsbEhJSZKSkmRkZExMTDIyMj0+PkBAQIWFhWZmZgUFBXd3dzQ0NBsbG5GRkUpKSRcXF5OTkzo6OV5eXlpaWk9QUG9vb35+fj4+PW5ubnp6ehwcHD8/P4iIiElISQoKCo+PkFxdXT9AQICAgGJjYhMTE1BPTw4ODjo5OQQEBISEhEBAP3Z2doqKi4qKilZWVjY2NkNDQ0lJSQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAcAA4AAAf/gGqCGx8gZYcMaRiCjISGiIqMNRABMENcViMTEQwagpMBVBRNNyo0Mp2CADk7B4yCI2MFHGoASB0mBLq6IWWzIjFmr69HWhnABS0LNlgLygsmARk3Nl7DrzwhEQUXZt7f3zAR1+RqCRUP50wP6ChdKAkJ5ddp9fVV9vY4afPDaP8AAwZEgKCfGjBZfAB40qNBAwAAHkYUM0MBkXloVDiJYsRBEgMGSHxxQMLBFhoDLChwdW1AAAkpLZw4Q7PmGQlXJKhxCUTKqyAGfqzcGUAIBJsGlpAZqiYFhTAXppxIA0UGCw+MnJaZoeSCjg5Frr5yUWKFgLMTXhQcW/asgLRrAgMBADs=)",
                    height: presentationCycle.barHeight + "px",
                    width: presentationCycle.barHeight + "px"
                });
            }            
            $(item).attr('itemNr', (i + 1));
            $(item).css('cursor', 'pointer');  
            $(item).css('position','relative');
            $(item).css('z-index','99999');
            $(item).click(function() {                
               presentationCycle.gotoSlide($(this).attr('itemNr'));
            });
            
            if (extra_bar == true) {
                var item = $("<div>&nbsp;</div>").appendTo(presentationCycle.barContainer);
                $(item).addClass("bar");
                 $(item).css({
                    backgroundImage: "url(data:image/jpg;base64,R0lGODlhBgAOAKIAAAMDA2dnZy0tLW9vbyUlJQAAAAAAAAAAACH5BAAAAAAALAAAAAAGAA4AAAMSWLrcKzCGSZ1tIOvBO/ngJRYJADs=)",
                    height: presentationCycle.barHeight + "px",
                    width: fillWidth + "px"
                });
            }
        }
        
        var overflow = $("<div></div>");
        $(overflow).addClass("pc_bar_container_overflow");
        $(overflow).css({
            overflow: "hidden",
            width: totalWidth + "px"
        });
        var underflow = $("<div></div>");
        $(underflow).addClass("pc_bar_container_underflow").appendTo(overflow);
        
        presentationCycle.barContainerActive = $(presentationCycle.barContainer).clone().appendTo(underflow);
        $(presentationCycle.barContainerActive).removeClass("pc_bar_container");
        $(presentationCycle.barContainerActive).children().each(function () {
            $(this).css({
                backgroundPosition: "right"
            });
            if ($(this).css("background-image").match("R0lGODlhBgAOAKIAAAMDA2dnZy0tLW9vbyUlJQAAAAAAAAAAACH5BAAAAAAALAAAAAAGAA4AAAMSWLrcKzCGSZ1tIOvBO/ngJRYJADs=")) {
                var newImg = $(this).css("background-image").replace("url(data:image/jpg;base64,R0lGODlhBgAOAKIAAAMDA2dnZy0tLW9vbyUlJQAAAAAAAAAAACH5BAAAAAAALAAAAAAGAA4AAAMSWLrcKzCGSZ1tIOvBO/ngJRYJADs=)", "url(data:image/jpg;base64,R0lGODlhBwAOAMQAAFBQUE9QT09QUEA/P0lISVBQT1BPTz9AP09PT0A/QEBAQElJSX19fUNDQy0tLYGBgSUlJQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAHAA4AAAUoYCSOZGk6aOo8bPsYASIABbIQS543fN8kA4XikFAwjkgGZMmEmJ7QEAA7)");
                $(this).css("background-image", newImg);
            }
        });
        $(overflow).css({
            width: presentationCycle.barHeight + "px",
            height: presentationCycle.barHeight + "px"
        });
        
        presentationCycle.barContainerOverflow = overflow;
        
        $('#' + presentationCycle.containerId).cycle({
    		fx: presentationCycle.cycleFx,
            speed: presentationCycle.cycleSpeed,
            timeout: presentationCycle.slideTimeout,
            before: function(currSlideElement, nextSlideElement) { presentationCycle.beforeSlide(currSlideElement, nextSlideElement); }
    	});
        
        presentationCycle.barContainer.appendTo($('#' + presentationCycle.containerId));
        overflow.appendTo($('#' + presentationCycle.containerId));
        
        var i = 0;
        $(".pc_bar_container_overflow .left, .pc_bar_container_overflow .center, .pc_bar_container_overflow .right").each(function () {
            $(this).attr('itemNr', (i + 1));
            $(this).css('cursor', 'pointer');
            $(this).click(function() {
                presentationCycle.gotoSlide($(this).attr('itemNr'));
            });
            i++;
        });
    },
    
    beforeSlide: function(currSlideElement, nextSlideElement) {
        if (presentationCycle.currentItem == 0) {
            presentationCycle.currentItem = 1;
        } else {
            presentationCycle.currentItem = (presentationCycle.itemCount - ($(nextSlideElement).nextAll().length)) + 2;
        }
        presentationCycle.animateProcess();
    },
    
    animateProcess: function() {
        var startWidth = (presentationCycle.itemBarWidth * (presentationCycle.currentItem - 1)) + (presentationCycle.barHeight * presentationCycle.currentItem);
        if (presentationCycle.currentItem != presentationCycle.itemCount) {
            var newWidth = (presentationCycle.itemBarWidth * (presentationCycle.currentItem)) + (presentationCycle.barHeight * (presentationCycle.currentItem + 1));   
        } else {
            var newWidth = presentationCycle.barHeight;
        }
        
        $(presentationCycle.barContainerOverflow).css({
            width: startWidth + "px"
        });
        if (presentationCycle.disableAnimation == false) {
            $(presentationCycle.barContainerOverflow).stop().animate({
                width: newWidth + "px"
            }, (presentationCycle.slideTimeout - 100));   
        }
    },
    
    gotoSlide: function(itemNr) {
        $(presentationCycle.barContainerOverflow).stop();
        presentationCycle.disableAnimation = true;
        $('#' + presentationCycle.containerId).cycle((itemNr - 1));
        $('#' + presentationCycle.containerId).cycle('pause');
    }
    
}



