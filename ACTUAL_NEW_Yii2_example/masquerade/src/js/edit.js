import Api from "./api.js";
import Pagination from "./pagination.js";
import jQuery from "jquery";
import Mask from "./mask";
import EditTest from "./edit-test";

export default class List {

    constructor({onBack,api}) {
        this.onBack = onBack;
        this.api = api;
        this.$el = null;
        this.editTest = new EditTest({api, maskEdit: this});
        this.id = null;
    }

    hide() {
        this.$el.hide();
    }

    show() {
        this.$el.show();
    }

    init($el) {
        this.$el    = jQuery($el);
        this.$back  = this.$el.find('#masks-back');
        this.$back.on('click',() => {
            this.onBack();
        });
        this.$save  = this.$el.find('#msk-save');
        this.$save.on('click', async () => {
            await this.saveMask();
        });
        this.$delete  = this.$el.find('#msk-delete');
        this.$delete.on('click', async () => {
            if (confirm('Точно удалить эту маску?')) {
                await this.deleteMask();
                this.onBack();
            }
        });

        this.$fieldAddName = this.$el.find('#msk-add-field-name');
        this.$fieldAddType = this.$el.find('#msk-add-field-type');
        this.$fieldAddBtn = this.$el.find('#msk-add-field-btn');
        this.$fieldList = this.$el.find('#msk-fields');

        this.$boundsAddType = this.$el.find('#msk-add-bounds-type');
        this.$boundsAddBtn = this.$el.find('#msk-add-bounds-btn');
        this.$boundsList = this.$el.find('#msk-bounds');

        this.$http404AddType = this.$el.find('#msk-add-http404-type');
        this.$http404AddBtn = this.$el.find('#msk-add-http404-btn');
        this.$http404List = this.$el.find('#msk-http404');

        this.$stockAddOut = this.$el.find('#msk-add-stock-out');
        this.$stockAddType = this.$el.find('#msk-add-stock-type');
        this.$stockAddBtn = this.$el.find('#msk-add-stock-btn');
        this.$stockList = this.$el.find('#msk-stock');

        this.$actionsAddKind = this.$el.find('#msk-add-actions-kind');
        this.$actionsAddType = this.$el.find('#msk-add-actions-type');
        this.$actionsAddBtn = this.$el.find('#msk-add-actions-btn');
        this.$actionsList = this.$el.find('#msk-actions');

        this.$repalcesAddBtn = this.$el.find('#msk-add-replaces-btn');
        this.$repalcesList = this.$el.find('#msk-replaces');

        this.$dateFormatAddBtn = this.$el.find('#msk-add-dateformat-btn');
        this.$dateFormatList = this.$el.find('#msk-dateformat');

        this.$dateParseAddBtn = this.$el.find('#msk-add-dateparse-btn');
        this.$dateParseAddKind = this.$el.find('#msk-add-dateparse-kind');
        this.$dateParseList = this.$el.find('#msk-dateparse');

        this.$scriptsAddBtn = this.$el.find('#msk-add-scripts-btn');
        this.$scriptsList = this.$el.find('#msk-scripts');


        jQuery.each(Mask.types(), (val,txt) => {
            this.$fieldAddType.append('<option value="'+val+'">'+txt+'</option>');
            this.$boundsAddType.append('<option value="'+val+'">'+txt+'</option>');
            this.$http404AddType.append('<option value="'+val+'">'+txt+'</option>');
            if (val === 'range') {
                this.$actionsAddType.append('<option value="'+val+'">Текст</option>');
                this.$stockAddType.append('<option value="'+val+'">Текст</option>');
            } else {
                this.$actionsAddType.append('<option value="'+val+'">'+txt+'</option>');
                this.$stockAddType.append('<option value="'+val+'">'+txt+'</option>');
            }
        });
        jQuery.each(Mask.fields(), (val,txt) => {
            this.$fieldAddName.append('<option value="'+val+'">'+txt+'</option>');
        });
        jQuery.each(Mask.actions(), (val,txt) => {
            this.$actionsAddKind.append('<option value="'+val+'">'+txt+'</option>');
        });
        jQuery.each(Mask.dateParseKinds(), (val,txt) => {
            this.$dateParseAddKind.append('<option value="'+val+'">'+txt+'</option>');
        });

        this.initFieldsUi();

        this.editTest.init($el);
    }

    initFieldsUi() {

        this.$fieldAddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('fields', this.$fieldAddName.val(), {
                type: this.$fieldAddType.val()
            })).render(this.$fieldList);
        });

        this.$boundsAddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('bounds', null, {
                type: this.$boundsAddType.val()
            })).render(this.$boundsList);
        });
        this.$http404AddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('bounds', null, {
                type: this.$http404AddType.val()
            })).render(this.$http404List);
        });

        this.$stockAddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('stock', null, {
                type: this.$stockAddType.val(),
                out: this.$stockAddOut.val() === 'out',
            })).render(this.$stockList);
        });

        this.$actionsAddBtn.on('click',(e)=>{
            e.preventDefault();
            const key = this.$actionsAddKind.val();
            (new Mask('actions', key, {
                type: key === 'hrefs' ? this.$actionsAddType.val() : null,
            })).render(this.$actionsList);
        });
        this.$actionsAddKind.on('change',(e)=>{
            e.preventDefault();
            if (e.target.value === 'hrefs') {
                this.$actionsAddType.show();
            } else {
                this.$actionsAddType.hide();
            }
        });

        this.$repalcesAddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('replaces', null, {
                type: 'range',
            })).render(this.$repalcesList);
        });

        this.$dateFormatAddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('dateformat', null, '')).render(this.$dateFormatList);
        });

        this.$dateParseAddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('dateparse', this.$dateParseAddKind.val(), {
                from:'',to:''
            })).render(this.$dateFormatList);
        });

        this.$scriptsAddBtn.on('click',(e)=>{
            e.preventDefault();
            (new Mask('scripts', null, '')).render(this.$scriptsList);
        });

    }

    async deleteMask() {
        let result = await this.api.masksDelete(this.getId());
        alert('Маска удалена!');
    }

    async saveMask() {
        const masks = this.buildMask();
        let maskRecord = {
            id: this.getId(),
            name: masks.name,
            domain: masks.domain,
            masks: JSON.stringify(masks),
            test_urls: this.$el.find('[name="test_urls"]').val(),
        };
        let result = await this.api.masksAdd(this.getId(), this.getProjectId(), maskRecord);
        if (result) {
            this.$el.find('#msk-id').val(result.id);
            alert('Маска сохранена успешно!');
        } else {
            alert('Ошибка!');
        }
    }

    buildMask() {
        let masks = this.api.dummyMasks();

        let m = {
            bounds: this.$boundsList.children(),
            http404: this.$http404List.children(),
            stock: this.$stockList.children(),
            replaces: this.$repalcesList.children(),
            scripts: this.$scriptsList.children()
        };

        for (let group in m) {
            m[group].each((i, el)=>{
                let row = el.mask.result();
                if (row.data) {
                    masks[group].push(row.data);
                }
            });
        }
        this.$fieldList.children().each((i, el)=>{
            let row = el.mask.result();
            if (row.data) {
                if (!masks.fields[row.key]) {
                    masks.fields[row.key] = [];
                }
                masks.fields[row.key].push(row.data);
            }
        });
        this.$actionsList.children().each((i, el)=>{
            let row = el.mask.result();
            if (row.data) {
                masks[row.key].push(row.data);
            }
        });
        this.$dateFormatList.children().each((i, el)=>{
            let row = el.mask.result();
            if (row.data) {
                masks.dateParse.dateFormat.push(row.data);
            }
        });
        this.$dateParseList.children().each((i, el)=>{
            let row = el.mask.result();
            if (row.data) {
                masks.dateParse[row.key].push(row.data);
            }
        });

        masks.name = this.$el.find('[name=name]').val();
        masks.domain = this.$el.find('[name=domain]').val();

        masks.wait.untilState = this.$el.find('[name="wait[untilState]"]').val();
        masks.wait.forElement = this.$el.find('[name="wait[forElement]"]').val();
        masks.wait.beforeStart = parseInt(this.$el.find('[name="wait[beforeStart]"]').val(),10);
        masks.wait.scroll = parseInt(this.$el.find('[name="wait[scroll]"]').val(),10);
        masks.captcha.enabled = this.$el.find('[name="captcha[enabled]"]').prop('checked');
        masks.captcha.imgSelector = this.$el.find('[name="captcha[imgSelector]"]').val();
        masks.captcha.inputSelector = this.$el.find('[name="captcha[inputSelector]"]').val();
        masks.captcha.buttonSelector = this.$el.find('[name="captcha[buttonSelector]"]').val();

        console.log(masks);
        return masks;
    }

    buildQueue() {
        let queue = [];
        const testUrls = this.$el.find('[name="test_urls"]').val().split("\n");
        for (let i = 0; i < testUrls.length && i < 100 ; i++) {
            if (testUrls[i]) {
                queue.push({
                    url: testUrls[i]
                });
            }
        }
        return queue;
    }

    async openMasks(projectId, masksId) {
        const project = await this.editTest.openProject(projectId);

        let maskRecord = null;
        if (masksId !== 'new') {
            maskRecord = await this.api.masks({id:masksId});
        } else {
            maskRecord = {
                name: 'Новая маска ' + project.name,
                domain: '',
                test_urls: '',
                masks: this.api.dummyMasks()
            };
        }
        let title = project.name + ' → ' + maskRecord.name;
        jQuery('title, .title').html(title);

        const masks = maskRecord.masks;

        console.log(maskRecord);

        this.$el.find('#msk-id').val(maskRecord.id);
        this.$el.find('#project-id').val(project.id);

        this.$el.find('[name=name]').val(maskRecord.name);
        this.$el.find('[name=domain]').val(maskRecord.domain);
        this.$el.find('[name="wait[untilState]"]').val(masks.wait.untilState);
        this.$el.find('[name="wait[forElement]"]').val(masks.wait.forElement);
        this.$el.find('[name="wait[beforeStart]"]').val(masks.wait.beforeStart);
        this.$el.find('[name="wait[scroll]"]').val(masks.wait.scroll);
        this.$el.find('[name="test_urls"]').val(maskRecord.test_urls);
        this.$el.find('[name="test_cookies"]').val(maskRecord.test_cookies);
        if (masks.captcha) {
            this.$el.find('[name="captcha[enabled]"]').prop('checked', masks.captcha.enabled || masks.captcha.imgSelector);
            this.$el.find('[name="captcha[imgSelector]"]').val(masks.captcha.imgSelector);
            this.$el.find('[name="captcha[inputSelector]"]').val(masks.captcha.inputSelector);
            this.$el.find('[name="captcha[buttonSelector]"]').val(masks.captcha.buttonSelector);
        }

        this.$fieldList.children().remove();
        this.$boundsList.children().remove();
        this.$http404List.children().remove();
        this.$stockList.children().remove();
        this.$actionsList.children().remove();
        this.$repalcesList.children().remove();
        this.$dateFormatList.children().remove();
        this.$dateParseList.children().remove();
        this.$scriptsList.children().remove();

        jQuery.each(masks.fields, (key, field) => {
            field.forEach(mask => {
                (new Mask('fields', key, mask)).render(this.$fieldList);
            });
        });
        masks.bounds.forEach(mask => {
            (new Mask('bounds', null, mask)).render(this.$boundsList);
        });
        masks.http404 = masks.http404 || [];
        masks.http404.forEach(mask => {
            (new Mask('bounds', null, mask)).render(this.$http404List);
        });
        masks.stock.forEach(mask => {
            (new Mask('stock', null , mask)).render(this.$stockList);
        });
        masks.clicks.forEach(mask => {
            (new Mask('actions', 'clicks' , mask)).render(this.$actionsList);
        });
        masks.clicksSoft = masks.clicksSoft || [];
        masks.clicksSoft.forEach(mask => {
            (new Mask('actions', 'clicksSoft' , mask)).render(this.$actionsList);
        });
        masks.hrefs.forEach(mask => {
            (new Mask('actions', 'hrefs' , mask)).render(this.$actionsList);
        });
        masks.replaces.forEach(mask => {
            (new Mask('replaces', null , mask)).render(this.$repalcesList);
        });
        masks.dateParse.dateFormat.forEach(mask => {
            (new Mask('dateformat', null , mask)).render(this.$dateFormatList);
        });
        masks.dateParse.replace.forEach(mask => {
            (new Mask('dateparse', 'replace' , mask)).render(this.$dateParseList);
        });
        masks.dateParse.match.forEach(mask => {
            (new Mask('dateparse',  'match' , mask)).render(this.$dateParseList);
        });
        masks.scripts.forEach(mask => {
            (new Mask('scripts', null, mask)).render(this.$scriptsList);
        });

        // this.$pageEdit.append(JSON.stringify(mask));
    }

    getProjectId() {
        return this.$el.find('#project-id').val();
    }
    getId() {
        return this.$el.find('#msk-id').val();
    }
    getName() {
        return this.$el.find('[name=name]').val();
    }



}