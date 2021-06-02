import Api from "./api.js";
import jQuery from "jquery";
import Mask from "./mask";

export default class EditTest {

    constructor({api, maskEdit}) {
        this.api = api;
        this.maskEdit = maskEdit;
    }

    init($el) {
        this.$el            = jQuery($el);
        this.resultsChecker = null;
        this.$paringResults = this.$el.find('#msk-parsing-results');
        this.$btnRunTest    = this.$el.find('#msk-run-test');
        this.$projectForm   = this.$el.find('#project-form');
        this.$btnDownloadQueue    = this.$el.find('#download-queue');
        this.$btnDownloadSettings    = this.$el.find('#download-settings');

        this.$btnRunTest.on('click',(e) => {
            e.preventDefault();
            this.runTest();
        });
        this.$btnDownloadQueue.on('click',(e) => {
            e.preventDefault();
            this.download('queue-test', JSON.stringify(this.maskEdit.buildQueue(), null, 4));
        });
        this.$btnDownloadSettings.on('click',(e) => {
            e.preventDefault();
            this.download('settings-test', JSON.stringify(this.buildProjectSettings(), null, 4));
        });

        this.project = this.api.dummyParsing();
    }

    download(filename, text) {
        let element = document.createElement('a');
        element.setAttribute('href', 'data:application/json;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();
        document.body.removeChild(element);
    }

    async runTest() {

        const res = await this.api.test({
            settings: this.buildProjectSettings(),
            queue: this.maskEdit.buildQueue(),
            noProxy: jQuery('#flag-noProxy').prop('checked'),
            html: jQuery('#flag-html').prop('checked'),
            debug: jQuery('#flag-debug').prop('checked'),
        });

        res.name =  this.maskEdit.getName();

        let w = window.open("testing.html#"+btoa(unescape(encodeURIComponent(JSON.stringify(res)))));

        w.testingData = res;
    }

    buildProjectSettings() {
        for (let k in this.project) {
            if (this.project.hasOwnProperty(k) && !(['proxies','userAgents','blockedDomains','vpn'].includes(k))) {
                if (this.$projectForm.find('[name="project['+k+']"]').length > 0) {
                    let val = this.$projectForm.find('[name="project[' + k + ']"]').val();
                    if (jQuery.isNumeric(val)) {
                        this.project[k] = parseInt(val);
                    } else {
                        this.project[k] = val;
                    }
                }
            }
        }
        let provider = this.$projectForm.find('[name="project[vpn][provider]"]').val();
        if (provider) {
            this.project.vpn = {};
            this.project.vpn.provider = this.$projectForm.find('[name="project[vpn][provider]"]').val();
            this.project.vpn.username = this.$projectForm.find('[name="project[vpn][username]"]').val();
            this.project.vpn.password = this.$projectForm.find('[name="project[vpn][password]"]').val();
            this.project.vpn.config = this.$projectForm.find('[name="project[vpn][config]"]').val();
        } else {
            this.project.vpn = null;
        }
        this.project.proxies        = this.$projectForm.find('[name="project[proxies]"]').val().split("\n");
        this.project.userAgents     = this.$projectForm.find('[name="project[userAgents]"]').val().split("\n");
        this.project.torEnabled     = this.$el.find('#flag-torEnabled').prop('checked');
        this.project.projectMasks   = {};
        this.project.projectMasks[this.maskEdit.getId()] = this.maskEdit.buildMask();
        return this.project;
    }

    async openProject(id) {
        if (id) {
            this.project = await this.api.projects({id});
        } else {
            this.project = this.api.dummyParsing();
        }

        this.$projectForm.find('input,select,textarea').val('');

        for (let k in this.project) {
            if (this.project.hasOwnProperty(k)) {
                if (!Array.isArray(this.project[k]) && typeof this.project[k] !== 'object') {
                    this.$projectForm.find('[name="project['+k+']"]').val(this.project[k]);
                }
            }
        }

        for (let k in this.project.vpn) {
            if (this.project.vpn.hasOwnProperty(k)) {
                this.$projectForm.find('[name="project[vpn]['+k+']"]').val(this.project.vpn[k]);
            }
        }
        if (this.project.proxies.length > 100) {
            this.project.proxies = this.project.proxies.slice(0, 100);
        }
        this.$projectForm.find('[name="project[proxies]"]').val(this.project.proxies.join("\n"));
        if (this.project.userAgents.length > 100) {
            this.project.userAgents =  this.project.userAgents.slice(0, 100);
        }
        this.$projectForm.find('[name="project[userAgents]"]').val(this.project.userAgents.join("\n"));

        return this.project;
    }


}