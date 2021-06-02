/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

if (document.getElementById('app')) {
    import('./vueapp' /* webpackChunkName: "js/chunks/vueapp" */);
}

if (document.getElementById('reportChart')) {
    import('./report' /* webpackChunkName: "js/chunks/report" */);
}

if (document.querySelectorAll('.js-wysiwyg').length) {
    import('./wysiwyg' /* webpackChunkName: "js/chunks/wysiwyg" */);
}


// Кликабельные tr
if (document.querySelectorAll('.js-has-clickable-tr').length) {
    var trs = document.querySelectorAll('.js-has-clickable-tr tbody tr');
    for (let index = 0; index < trs.length; index++) {
        trs[index].onclick = function (event) {
            if (['A', 'BUTTON'].includes(event.target.tagName) === false) {
                var link = event.currentTarget.querySelector('.js-click-tr');
                link.click();
            }
        };
    }
    trs = null;
}

if (document.querySelectorAll('.js-clipboard-write').length) {
    var clipboardButtons = document.querySelectorAll('.js-clipboard-write');
    for (let index = 0; index < clipboardButtons.length; index++) {
        clipboardButtons[index].onclick = function (event) {
            navigator.clipboard.writeText(event.target.dataset.clipboard).then(function () {
                var oldInnerHTML = clipboardButtons[index].innerHTML;

                clipboardButtons[index].innerHTML = 'OK!';
                setTimeout(function () {
                    clipboardButtons[index].innerHTML = oldInnerHTML;
                }, 1000);
            }, function (err) {
                console.error(err);
                clipboardButtons[index].innerHTML = 'ERROR!';
                setTimeout(function () {
                    clipboardButtons[index].innerHTML = oldInnerHTML;
                }, 1000);
            });
        };
    }
    trs = null;
}
