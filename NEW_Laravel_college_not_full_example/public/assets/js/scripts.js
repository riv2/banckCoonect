(function ($) {

    $('form').on('focus', 'input[type=number]', function (e) {
        $(this).on('mousewheel.disableScroll', function (e) {
            e.preventDefault()
        })
    });
    $('form').on('blur', 'input[type=number]', function (e) {
        $(this).off('mousewheel.disableScroll')
    });

    $(window).bind('enterBreakpoint992', function () {

        // if open on mobile close on desktop
        if ($('body.sidebar-push-toright').length)  $('#showLeftPush').trigger('click');

    });

}(jQuery));

/*!
 * classie - class helper functions
 * from bonzo https://github.com/ded/bonzo
 *
 * classie.has( elem, 'my-class' ) -> true/false
 * classie.add( elem, 'my-new-class' )
 * classie.remove( elem, 'my-unwanted-class' )
 * classie.toggle( elem, 'my-class' )
 */

/*jshint browser: true, strict: true, undef: true */

(function ($) {

    'use strict';
    // class helper functions from bonzo https://github.com/ded/bonzo

    function classReg(className) {
        return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
    }

    // classList support for class management
    // altho to be fair, the api sucks because it won't accept multiple classes at once
    var hasClass, addClass, removeClass;

    if ('classList' in document.documentElement) {
        hasClass = function (elem, c) {
            return elem.classList.contains(c);
        };
        addClass = function (elem, c) {
            elem.classList.add(c);
        };
        removeClass = function (elem, c) {
            elem.classList.remove(c);
        };
    }
    else {
        hasClass = function (elem, c) {
            return classReg(c).test(elem.className);
        };
        addClass = function (elem, c) {
            if (! hasClass(elem, c)) {
                elem.className = elem.className + ' ' + c;
            }
        };
        removeClass = function (elem, c) {
            elem.className = elem.className.replace(classReg(c), ' ');
        };
    }

    function toggleClass(elem, c) {
        var fn = hasClass(elem, c) ? removeClass : addClass;
        fn(elem, c);
    }

    window.classie = {
        // full names
        hasClass: hasClass,
        addClass: addClass,
        removeClass: removeClass,
        toggleClass: toggleClass,
        // short names
        has: hasClass,
        add: addClass,
        remove: removeClass,
        toggle: toggleClass
    };

}(jQuery));

(function ($) {

    var showLeftPush = document.getElementById('showLeftPush'),
        showRightPush = document.getElementById('showRightPush'),
        showUserPush = document.getElementById('showUserPush'),
        menuRight = document.getElementById( 'sidebar-right' ),
        menuLeft = document.getElementById( 'sidebar-left' ),
        body = document.body;

    // Left Sidebar
    if (showLeftPush !== null) {
        showLeftPush.onclick = function () {
            closeSidebar('sidebar-left');

            // some sidebars don't need an overlay (ex. chat) and we need to remove body hanging class
            $('body').removeClass('no-overlay');

            classie.toggle(this, 'active');
            classie.toggle(body, 'sidebar-push-toright');
            classie.toggle(menuLeft, 'sidebar-left-open');
        };
    }

}(jQuery));

function closeSidebar(exceptId) {
    // close all open sidebars
    $(".sidebar:not(#"+exceptId+")").removeClass(function (index, css) {
        return (css.match(/\S+\b-open/g) || []).join(' ');
    });

}