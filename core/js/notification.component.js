$(document).ready(() => {
    $(document).on('click', '.notif .notif-close', (e) => {
        e = e || window.event;
        let notifNode = e.target.parentNode;
        while (!(notifNode.classList.contains('notif'))) notifNode = notifNode.parentNode;

        NotificationManagement.pop(notifNode);
    });
});

class NotificationManagement {
    constructor() {}

    static pop(notifNode) {
        notifNode.classList.add('fade');
        setTimeout((() => { notifNode.remove(); }), 300);
    }

    static put(data) {
        if (typeof data['header'] == undefined) throw 'Notification Header parameter is missing';
        if (typeof data['message'] == undefined) throw 'Notification Message parameter is missing';
        if (typeof data['type'] == undefined) throw 'Notifcation Type parameter is missing';

        let notifContianer = this.makeContainer();
        let notifNode, notifHeaderNode, notifTitleNode, notifCloseNode, notifBodyNode;

        notifNode = document.createElement('div');
        notifNode.classList.add('notif');
        switch (data['type']) {
            case 'success' : 
                notifNode.classList.add('notif-success');
                break;
            case 'info' : 
                notifNode.classList.add('notif-info');
                break;
            case 'warning' : 
                notifNode.classList.add('notif-warning');
                break;
            case 'danger' : 
                notifNode.classList.add('notif-danger');
                break;
            default :
                notifNode.classList.add('notif-default');
        }

        notifHeaderNode = document.createElement('div');
        notifHeaderNode.classList.add('notif-header');
        notifNode.append(notifHeaderNode);

        notifTitleNode = document.createElement('h4');
        notifTitleNode.classList.add('notif-title');
        notifTitleNode.textContent = data['header'];
        notifHeaderNode.append(notifTitleNode);

        notifCloseNode = document.createElement('button');
        notifCloseNode.classList.add('btn');
        notifCloseNode.classList.add('btn-default');
        notifCloseNode.classList.add('notif-close');
        notifCloseNode.innerHTML = '<i class="fa fa-times"></i>';
        notifHeaderNode.append(notifCloseNode);

        notifBodyNode = document.createElement('div');
        notifBodyNode.classList.add('notif-body');
        notifBodyNode.innerHTML = data['message'];
        notifNode.append(notifBodyNode);

        notifContianer.prepend(notifNode);

        setTimeout((() => { NotificationManagement.pop(notifNode) }), 15000);

    }

    static makeContainer() {
        let bodyNode = document.querySelector('body');
        let notifContainer = bodyNode.querySelector('.notif-container');
        if (notifContainer == null) {
            notifContainer = document.createElement('div');
            notifContainer.classList.add('notif-container');
            bodyNode.append(notifContainer);
        }
        return notifContainer;
    }
}