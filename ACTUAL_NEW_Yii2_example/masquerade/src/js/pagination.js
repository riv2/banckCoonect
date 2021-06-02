export default class Pagination  {

    static update($paginator, meta) {
        $paginator.children().remove();
        if (meta.pageCount === 1) {
            $paginator.hide();
            return;
        }
        $paginator.show();
        let cp = meta.page;
        let start = cp - 2;
        let end   = cp + 3;
        let extra = 0;
        if (start < 0) {
            extra = -start;
            start = 0;
        }
        end += extra;
        extra = 0;
        if (end >= meta.pageCount) {
            extra = end - meta.pageCount;
            end = meta.pageCount;
        }
        start -= extra;
        if (start < 0) {
            start = 0;
        }
        if (start > 0) {
            $paginator.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
            $paginator.append('<li class="page-item disabled"><a class="page-link">...</a></li>');
        }
        for (let i = start; i < end; i++) {
            let isActive = '';
            let page = i + 1;
            if (page === meta.page) {
                isActive = 'active'
            }
            $paginator.append('<li class="page-item '+isActive+'"><a class="page-link" href="#" data-page="'+page+'">'+page+'</a></li>');
        }
        if (end <=  meta.pageCount - 2) {
            $paginator.append('<li class="page-item disabled"><a class="page-link">...</a></li>');
            $paginator.append('<li class="page-item"><a class="page-link" href="#" data-page="'+meta.pageCount+'">'+meta.pageCount+'</a></li>');
        }
    }
}