import jQuery from "jquery";
import popper from "popper.js";
import bootstrap from "bootstrap";
import Projects from "./projects";
import Edit from "./edit";
import Api from "./api";
import Mask from "./mask";

export  default class Index {

    constructor() {
        this.api = new Api();
        this.edit = new Edit({
            api: this.api,
            onBack: async () => {
                await this.openList();
            }
        });
        this.projects = new Projects({
            api: this.api,
            onClick: async (clickedProject, clickedMask) => {
                window.location.hash = 'masks&'+clickedProject.id+'&'+clickedMask.id;
            }
        });
    }

    init() {
        if (document.getElementById('testing-iframe')) {
            this.testingMode();
        } else {
            this.normalMode();
            this.loadHash();
        }
    }

    loadHash() {

        const parts = window.location.hash.substr(1).split('&');

        if (Array.isArray(parts) && parts.length > 0) {
            if (parts[0] === 'masks' && parts.length > 2) {
                this.openMasks(parts[1], parts[2]);
            } else {
                if (parts[0] === 'projects' && parts.length > 1) {
                    this.openList(parts[1]);
                } else {
                    this.openList();
                }
            }
        }
    }

    normalMode() {
        this.edit.init(jQuery('#masks-edit'));
        this.projects.init(jQuery('#projects'));

        window.onpopstate = (event) => {
            this.loadHash();
        };
    }

    testingMode() {
        this.testingData = JSON.parse(decodeURIComponent(escape(atob(window.location.hash.substr(1)))));
        jQuery('title, .title').html(this.testingData.name);
        this.$tabs = jQuery('#testing-tabs');
        this.$paringResults = jQuery('#parsing-results');

        this.$tabs.find('li').hide();
        this.$tabs.find('a[data-tab="parsed"]').parent().show();
        jQuery.each(this.testingData,(key, url) => {
            this.$tabs.find('a[data-tab="'+key+'"]').attr('href', url);
            this.$tabs.find('a[data-tab="'+key+'"]').parent().show();
        });
        if (this.testingData.noVnc) {
            this.testingModeActivateTab('noVnc');
        } else {
            this.testingModeActivateTab('log');
        }

        setTimeout(() => {
            this.testingModeActivateTab(this.$tabs.find('a.active').attr('data-tab'));
        }, 1000);
        setTimeout(() => {
            this.testingModeActivateTab(this.$tabs.find('a.active').attr('data-tab'));
        }, 3000);

        this.$tabs.find('a[data-tab]').on('click',(e)=>{
            e.preventDefault();
            this.testingModeActivateTab(jQuery(e.target).attr('data-tab'));
            return false;
        });
    }

    testingModeActivateTab(tab) {
        this.$tabs.find('a').removeClass('active');
        this.$tabs.find('a[data-tab="' + tab + '"]').addClass('active');
        if (tab === 'parsed') {
            this.loadParsedData();
            this.$paringResults.show();
        } else {
            this.$paringResults.hide();
            document.getElementById('testing-iframe').src = this.$tabs.find('a[data-tab="' + tab + '"]').attr('href');
        }
    }

    loadParsedData() {


        const htmlEntities = {
            /**
             * Converts a string to its html characters completely.
             *
             * @param {String} str String with unescaped HTML characters
             **/
            encode : function(str) {
                let buf = [];

                for (let i = str.length-1;i >= 0;i--) {
                    buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
                }

                return buf.join('');
            },
            /**
             * Converts an html characterSet into its original character.
             *
             * @param {String} str htmlSet entities
             **/
            decode : function(str) {
                return str.replace(/&#(\d+);/g, function(match, dec) {
                    return String.fromCharCode(dec);
                });
            }
        };

        if (!this.testingData.parsed) {
            return;
        }
        jQuery.get(this.testingData.parsed, (body) => {
            if (body) {
                const results = body.split("\n");
                let fields = Mask.parsedFields();
                let headerDone = false;
                let $table = jQuery('<table class="table"><thead><tr></tr></thead><tbody></tbody></table>');
                let $header = $table.find('thead tr');
                let $body = $table.find('tbody');
                this.$paringResults.children().remove();
                this.$paringResults.append($table);

                let heads = [];
                for (let i = 0; i < results.length; i++) {
                    let chunk = [];
                    try {
                        chunk = JSON.parse(results[i]);
                    } catch (e) {
                        chunk = [];
                    }
                    for (let j = 0; j < chunk.length; j++) {
                        for (let k in chunk[j]) {
                            if (chunk[j].hasOwnProperty(k)) {
                                if (!heads.includes(k)) {
                                    heads.push(k);
                                }
                            }
                        }
                    }
                }

                $header.append('<th>#</th>');
                heads.forEach((key) => {
                    let hName =  fields[key] || key;
                    $header.append('<th>' + hName + '</th>');
                });

                for (let i = 0; i < results.length; i++) {
                    let chunk = [];
                    try {
                        chunk = JSON.parse(results[i]);
                    } catch (e) {
                        chunk = [];
                    }
                    for (let j = 0; j < chunk.length; j++) {
                        let num = (chunk.length*i) + j + 1;
                        let $tr = jQuery('<tr></tr>');
                        $tr.append('<td>' + num + '</td>');
                        heads.forEach((k) => {
                            if (chunk[j].hasOwnProperty(k)) {
                                if (k === 'out_of_stock') {
                                    $tr.append('<td>' +(chunk[j][k] ? 'Нет' : 'Да') + '</td>');
                                } else if (k === 'url') {
                                    $tr.append('<td><a href="' + encodeURI(chunk[j][k]) + '" target="_blank"><i class="fa fa-link"></i> ссылка</a></td>');
                                } else {
                                    $tr.append('<td>' + htmlEntities.encode((chunk[j][k] || 'null').toString()) + '</td>');
                                }
                            }else {
                                $tr.append('<td>-</td>');
                            }
                        });
                        $body.append($tr);
                    }

                }
            }
        });
    }

    async openList(page) {
        page = page || 1;
        jQuery('title, .title').html('Проекты парсинга и маски');
        await this.projects.updateTable(page);
        this.projects.show();
        this.edit.hide();
    }

    async openMasks(projectId, masksId) {
        this.projects.hide();
        this.edit.show();
        await this.edit.openMasks(projectId, masksId);
    }
}


jQuery(async function() {
    const index = new Index;
    index.init();
});
