import Redactor from './redactor/redactor.usm.min.js';
import './redactor/_langs/ru.js';

new Redactor('.js-wysiwyg', {
    lang: document.documentElement.lang,
    buttons: ['html', 'format', 'bold', 'italic', 'lists', 'link', 'ul'],
    formatting: ['h1', 'h2', 'p', 'pre'],
    minHeight: '300px',
    linkSize: 100,
    structure: true,
    notranslate: true,
    pasteLinkTarget: '_blank',
});
