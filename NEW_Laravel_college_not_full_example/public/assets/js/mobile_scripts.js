
(function ($) {

    'use strict';

    function classReg(className) {
        return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
    }

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

$('body .overlay-disabled').on('click', function () {
    // remove all push-to classes from body
    $('body').removeClass (function (index, css) {
        return (css.match (/\S+\b-push-to\S+/g) || []).join(' ');
    });
    closeSidebar();
});

(function ($) {

    $('.sidebar .nicescroll .wrapper').niceScroll({scrollspeed: 26, cursorcolor:"#429eee", cursorborder: 0, horizrailenabled: false, railoffset: {left:-1}});

    $('.sidebar')
        .mouseover(function() {
            $('html').css('overflow','hidden');
        })
        .mouseout(function() {
            $('html').removeAttr('style');
    });

    $(".nav-sidebar .submenu > a").on('click', function (evt) {

        evt.preventDefault();


        var parent = $(this).closest('.sidebar');
        var submenuOpen = parent.find('.submenu .in');

        // Close Parent Open Submenus
        submenuOpen.collapse('hide');

        // Show Current Submenu
        $(this).next('ul').show().collapse('show');


        // display:none All Previously Opene Submenus
        submenuOpen.hide();

        // Toggle Open Classes
        if ($(this).hasClass("open")) {
            $(this).removeClass("open");
        }

        parent.find('a.open').removeClass('open');
        $(this).addClass('open');


    });

    $('sidebar').find('.collapse').on('shown.bs.collapse', function () {
        $(".sidebar").getNiceScroll().show().onResize();
    });

    $('.sidebar [data-toggle="close"]').on('click', function () {
        // remove all push-to classes from body
        $('body').removeClass (function (index, css) {
            return (css.match (/\S+\b-push-to\S+/g) || []).join(' ');
        });
        closeSidebar();
    });

}(jQuery));

