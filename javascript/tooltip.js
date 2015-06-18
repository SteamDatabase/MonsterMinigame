/* Requires jQuery
 *
 * This plugin will create div.jsTooltip elements (or configure your own!) in body for every tooltip on the page. Some
 * basic CSS is applied automagically, but you'll want to style it on your own from there. This code will be applied to
 * every element in your .v_tooltip() selector, so giving it a common selector like '.tooltip' is ideal.
 *
 * Options:
 * - location: Where the tooltip should spawn in relation to it's parent
 * - offsetN: How many pixels to add
 * - trackMouse: Should we track the mouse cursor instead of the parent?
 * - suppressOnClick: Should we hide if a user clicks the target?
 * - suppressWhileToggled: Should we ignore events if the target has the 'toggled' class?
 * - tooltipClass: css class to apply to tooltip elements
 * - fadeSpeed:	Time (in milliseconds) to spend fading in/out. Set to 0 to disable.
 * - allowHover: Should we keep the tooltip open if we mouse directly on to the tooltip? (Your tooltip will need to spawn inside it's owner's box for this to work)
 * - horizontalSnap: Generally useless property that lets you snap to the nearest n pixels. Used for supernav, ensures we always align the nav against the diagonal lines.
 * - tooltipParent: More generally useless properties for supernav: Lets us specify which element to parent the tooltips to. YOU PROBABLY DON'T NEED THIS.
 * - correctForScreenSize: Adjust tooltip position to ensure it doesn't render outside of the viewport
 * - sizeCorrectionXPadding: How far we should keep the tooltip from the window edge
 * - useClickEvent: Should we use the mouse click event instead of hover?
 * - inheritParentMinWidth: Should we set min-width based on our parent's width?
 * - parentActiveCSSClass: What CSS class should we add to our parent while we're visible?
 */
/* <script> */
(function( $ ){
	var methods = {

		init : function( options ) {

			var settings = $.extend( {
				'location'			: 'top',
				'offsetX'			: 0,
				'offsetY'			: -10,
				'trackMouse'		: false,
				'trackMouseCentered': true,
				'suppressOnClick'	: true,
				'suppressWhileToggled': true,
				'tooltipClass'		: 'jsTooltip',
				'fadeSpeed'			: 150,
				'allowHover'		: true,
				'horizontalSnap'	: false,
				'tooltipParent'		: 'body',
				'correctForScreenSize': true,
				'sizeCorrectionXPadding': 15,
				'useClickEvent'		: false,
				'useContextMenuEvent'	: false,
				'preventDefault'	: true,
				'inheritParentMinWidth'	: false,
				'parentActiveCSSClass'	: false,
				'dataName'			: 'tooltipContent',
				'funcName'			: 'tooltipFunc'
			}, options);




			return this.each(function(){


				if( settings.useClickEvent )
				{
					$(this).bind('click.tooltip', methods.show);
				} else if( settings.useContextMenuEvent )
				{
					$(this).bind('contextmenu.tooltip', methods.show);
				} else {
					$(this).bind('mouseenter.tooltip', methods.show);
				}
				$(this).bind('mouseleave.tooltip', methods.hide);


				$(this).data('tooltip.settings', settings);
			});

		},
		destroy : function() {

			return this.each(function(){
				$(window).unbind('.tooltip');
			})

		},
		gettooltip : function( element, settings ) {
			var toolDiv = $(element).data("tooltip.element");
			if ( !toolDiv )
			{
				toolDiv = $('<div />');

				if( settings.suppressOnClick )
				{
					toolDiv.bind('click.tooltip', jQuery.proxy(methods.hide, element));
				}
				toolDiv.hide();
				toolDiv.addClass(settings.tooltipClass)
				toolDiv.css({
					position: 'absolute',
					'z-index': 1500
				});
				var type = $(element).data('tooltip-type');
				if( type == 'text')
					toolDiv.text( $(element).data(settings.dataName) );
				else
					toolDiv.html( $(element).data(settings.dataName) );
				$( settings.tooltipParent || this.parentNode ).get(0).appendChild(toolDiv.get(0));
				$(element).data("tooltip.element", toolDiv);
			}

			var func = $(element).data( settings.funcName );
			if( func )
			{
				if( type == 'text')
					toolDiv.text( window[func](element) );
				else
					toolDiv.html( window[func](element) );
			}
			return toolDiv;
		},
		updateposition : function() {
		    var newPosition = {};
		    var settings = $(this).data('tooltip.settings');
		    var toolDiv = methods.gettooltip( this, settings );

		    var parentPosition = $(this).offset();
		    if( settings.tooltipParent != 'body' )
		        parentPosition = $(this).position();

		    switch( settings.location )
		    {
		        case 'top':
		            newPosition = {
		                left: parentPosition.left + settings.offsetX,
		                top: parentPosition.top - toolDiv.outerHeight() + settings.offsetY
		            };
		            break;

		        case 'bottom':
		            var newLeft = parentPosition.left;
		            if( settings.horizontalSnap )
		                newLeft = newLeft - newLeft % settings.horizontalSnap + settings.offsetX;
		            else
		                newLeft += settings.offsetX;

		            newPosition = {
		                left: newLeft,
		                top: parentPosition.top + $(this).outerHeight() + settings.offsetY
		            };
		            break;
		    }
		    // Correct for window size
		    if( settings.correctForScreenSize )
		    {
		        var rightEdge = newPosition.left + toolDiv.width();
		        var windowRightEdge = $(window).width() - settings.sizeCorrectionXPadding + $(window).scrollLeft();
		        var windowLeftEdge =  $(window).scrollLeft() + settings.sizeCorrectionXPadding;

		        if( rightEdge > windowRightEdge )
		            newPosition.left = windowRightEdge - toolDiv.width() - settings.sizeCorrectionXPadding;

		        if( newPosition.left < windowLeftEdge )
		            newPosition.left = windowLeftEdge;
		    }

		    toolDiv.css(newPosition);		},
		reposition : function(event) {
			var newPosition = {};
			var settings = $(this).data('tooltip.settings');
			var toolDiv = methods.gettooltip( this, settings );

			var parentPosition = $(this).offset();
			if( settings.tooltipParent != 'body' )
				parentPosition = $(this).position();

			if( settings.trackMouse )
			{
				if ( settings.trackMouseCentered )
					newPosition.left = event.pageX - toolDiv.outerWidth() / 2;
				else
					newPosition.left = event.pageX + settings.offsetY;

				if ( settings.location == 'top' )
					newPosition.top = event.pageY - toolDiv.outerHeight() + settings.offsetY;
				else
					newPosition.top = event.pageY + settings.offsetY;

			} else {
				switch( settings.location )
				{
					case 'top':
						newPosition = {
							left: parentPosition.left + settings.offsetX,
							top: parentPosition.top - toolDiv.outerHeight() + settings.offsetY
						};
						break;

					case 'bottom':
						var newLeft = parentPosition.left;
						if( settings.horizontalSnap )
							newLeft = newLeft - newLeft % settings.horizontalSnap + settings.offsetX;
						else
							newLeft += settings.offsetX;

						newPosition = {
							left: newLeft,
							top: parentPosition.top + $(this).outerHeight() + settings.offsetY
						};
						break;
					case 'mouse':
						console.log(event);
						newPosition = {
							left: event.pageX + settings.offsetX,
							top: event.pageY + settings.offsetY
						}
				}
			}
			// Correct for window size
			if( settings.correctForScreenSize )
			{
				var rightEdge = newPosition.left + toolDiv.width();
				var windowRightEdge = $(window).width() - settings.sizeCorrectionXPadding + $(window).scrollLeft();
				var windowLeftEdge =  $(window).scrollLeft() + settings.sizeCorrectionXPadding;

				if( rightEdge > windowRightEdge )
					newPosition.left = windowRightEdge - toolDiv.width() - settings.sizeCorrectionXPadding;

				if( newPosition.left < windowLeftEdge )
					newPosition.left = windowLeftEdge;
			}

			toolDiv.css(newPosition);
		},
		show : function(event) {
			var settings = $(this).data('tooltip.settings');
			var toolDiv = methods.gettooltip( this, settings );

			if( toolDiv.is(':empty') )
				return;

			if( event.type == "click" && event.currentTarget != this )
				return;

			if( settings.suppressWhileToggled && $(this).hasClass('toggled') )
				return false;

			if( settings.preventDefault )
				event.preventDefault();

			if( settings.parentActiveCSSClass )
				$(this).addClass(settings.parentActiveCSSClass);

			if( settings.inheritParentMinWidth )
			{
				var parentWidth = $(this).outerWidth();
				var localPadding = toolDiv.outerWidth() - toolDiv.width();
				toolDiv.css({'min-width': + (parentWidth - localPadding) + "px"});
			}

			if( settings.fadeSpeed > 0 )
			{
				toolDiv.stop(true, true);
				toolDiv.fadeTo( settings.fadeSpeed, 1 );
			}
			else
				toolDiv.show();

			if( settings.allowHover )
			{
				if( settings.useClickEvent )
					toolDiv.bind('click.tooltip', jQuery.proxy(methods.show, this));
				else if( settings.useContextMenuEvent )
					toolDiv.bind('contextmenu.tooltip', jQuery.proxy(methods.show, this));
				else
					toolDiv.bind('mouseenter.tooltip', jQuery.proxy(methods.show, this));
				toolDiv.bind('mouseleave.tooltip', jQuery.proxy(methods.hide, this));
			}

			if( settings.trackMouse )
				$(this).bind('mousemove.tooltip', methods.reposition);
			else
				jQuery.proxy(methods.reposition, this)(event);

		},
		hide : function(event) {
			var toolDiv = $(this).data('tooltip.element');
			var settings = $(this).data('tooltip.settings');

			// the element may not have been created yet - in which case there is nothing to hide
			if ( !toolDiv || !toolDiv.length )
				return;

			if( event['toElement'] == undefined )
				event['toElement'] = event['relatedTarget'];

			if( event.type != 'click' && ( event['toElement'] == toolDiv[0] || event['toElement'] == this || toolDiv[0].contains( event['toElement'] ) ) )
				return;

			if( settings.trackMouse )
				$(this).unbind('mousemove.tooltip');

			toolDiv.unbind('mouseenter.tooltip');
			toolDiv.unbind('mouseleave.tooltip');

			if( settings.parentActiveCSSClass )
				$(this).removeClass(settings.parentActiveCSSClass);

			$(this).removeData('tooltip.element');
			if( settings.fadeSpeed > 0 && toolDiv.is(':visible') )
			{
				toolDiv.stop();
				toolDiv.fadeTo( settings.fadeSpeed, 0, function() { methods.destroytooltip( toolDiv ) } );
			}
			else
			{
				methods.destroytooltip( toolDiv );
			}
		},
		destroytooltip: function( toolDiv )
		{
			if ( toolDiv )
			{
				$(toolDiv).remove();
			}
		}
	};

	$.fn.v_tooltip = function( method ) {

		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
		}

	};

})( jQuery );


