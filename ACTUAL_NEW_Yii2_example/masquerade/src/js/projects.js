import Pagination from "./pagination.js";
import jQuery from "jquery";

export default class Projects {

    constructor({onClick, onDelete, api}) {
        this.onClick = onClick;
        this.onDelete = onDelete;
        this.api = api;
        this.$el = null;
    }

    hide() {
        this.$el.hide();
    }

    show() {
        this.$el.show();
    }

    init($el) {
        this.$el = jQuery($el);
        this.$pagination = jQuery('#masks-pagination');
        this.$tbody = jQuery('#masks-tbody');
        this.$search = jQuery('#masks-search');
        this.$pageIndex = jQuery('#masks-index');
        this.$pageEdit = jQuery('#masks-edit');
        this.searchTimer = null;
        this.$search.on('keyup', (e) => {
            clearTimeout(this.searchTimer);
            if (e.keyCode === 13)
                this.updateTable();
            else {
                this.searchTimer = setTimeout(() => {
                    this.updateTable();
                }, 500);
            }
        });
        this.$pagination.on('click','a[data-page]', (e) => {
            e.preventDefault();
            this.updateTable(jQuery(e.target).attr('data-page'));
        });
        this.updateTable();
    }


    async updateTable(page = 1, perPage = 10) {
        let projects = await this.api.projects({page, perPage, q: this.$search.val()});
        Pagination.update(this.$pagination, projects.meta);
        this.$tbody.children().remove();
        projects.data.forEach(project => {
            let $actions = jQuery('<td/>');
            let $tr = jQuery('<tr/>');
            let $set = jQuery('<td></td>');
            $tr.append('<td><a href="http://pricing.vseinstrumenti.ru/parsing-project/update?id='+project.id+'" target="_blank">'+project.name+'</a></td>');
            $tr.append($set);
            $tr.append($actions);
            project.masks.forEach(mask => {
                $set.append('<span> </span>');
                $set.append(this.createTableMaskItem(project,mask));
            });
            let newMask = {id:'new', name:'Новая маска'};

            $actions.append(this.createTableMaskItem(project, newMask));
            $actions.css('text-align','right');

            this.$tbody.append($tr);
        });
    }

    createTableMaskItem(project, mask) {
        let add = mask.id === 'new' ? '<i class="fa fa-plus"></i> Новая маска' : '<i class="fa fa-edit"></i> ' + mask.name;
        let $mask = jQuery('<a href="#" class="btn btn-outline-secondary btn-xs" data-id="'+mask.id+'"  data-project-id="'+project.id+'">'+add+'</a>');
        $mask.on('click',(e) => {
            e.preventDefault();
            this.onClick(project, mask);
            return false;
        });
        return $mask;
    }
}