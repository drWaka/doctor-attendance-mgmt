class NavigationManagement {
    constructor() {}

    static updateNav(navContainerClass) {
        let navContainer = document.querySelector(navContainerClass);
        let prev = navContainer.querySelector('.prev-btn');
        let next = navContainer.querySelector('.next-btn');

        let pagination = this.loadPaginationConfig(navContainerClass);
        
        // Prev Page Management
        if (pagination['page'] <= 1) {
            // Trick to prevent the Ajax request from not submitting
            setTimeout(() => {this.buttonOnOff(prev, 'off')}, 100);
        } else {
            this.buttonOnOff(prev, 'on')
        }

        // Next Page Management
        let maxPage = Math.ceil(pageConfig['total'] / pageConfig['limit']);

        if (pagination['page'] == maxPage) {
            // Trick to prevent the Ajax request from not submitting
            setTimeout(() => {this.buttonOnOff(next, 'off')}, 100);
        } else {
            this.buttonOnOff(next, 'on')
        }
    }

    static next(navContainerClass) {
        let pagination = this.loadPaginationConfig(navContainerClass);
        let maxPage = Math.ceil(pagination['total'] / pagination['limit']);
        
        if (pagination['page'] < maxPage) pagination['page']++;

        this.setPaginationConfig(navContainerClass, pagination);
        callbackFunc();
    }
    static prev(navContainerClass, callbackFunc) {
        let pagination = this.loadPaginationConfig(navContainerClass);
        
        if (pagination['page'] > 1) pagination['page']--;
        
        this.setPaginationConfig(navContainerClass, pagination);
        callbackFunc();
    }

    static loadPaginationConfig(navContainerClass) {
        let navContainer = document.querySelector(navContainerClass);
        if (navContainer == undefined) throw "\""+ navContainerClass + "\" element not found";
        
        let page = navContainer.getAttribute('data-page');
        if (page == undefined) throw "Warning: [data-page] attribute not found";

        let total = navContainer.getAttribute('data-total');
        if (total == undefined) throw "Warning: [data-total] attribute not found";

        let limit = navContainer.getAttribute('data-limit');
        if (limit == undefined) throw "Warning: [data-limit] attribute not found";

        return {
            page: page,
            total: total,
            limit: limit,
        }
    }

    static setPaginationConfig(navContainerClass, configClass) {
        let navContainer = document.querySelector(navContainerClass);

        if (configClass['page'] == undefined) throw "Warning: page property is not found at config object parameter";
        if (configClass['total'] == undefined) throw "Warning: total property is not found at config object parameter";
        if (configClass['limit'] == undefined) throw "Warning: limit property is not found at config object parameter";
        
        navContainer.setAttribute('data-page', configClass['page']);
        navContainer.setAttribute('data-total', configClass['total']);
        navContainer.setAttribute('data-limit', configClass['limit']);
    }

    static buttonOnOff(btnElem, switchFLag = 'on') {
        if (switchFLag.toLowerCase() != 'on' && switchFLag.toLowerCase() != 'off') 
            throw "\"" + switchFLag + "is an invalid Switch flag";

        if (switchFLag.toLowerCase() == 'on') {
            btnElem.removeAttribute('disabled');
            return switchFLag;
        }

        btnElem.setAttribute('disabled', 'disabled');
        return switchFLag;
    }

}