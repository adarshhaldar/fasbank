function path(prefix, moduleName) {
    return prefix + '/' + moduleName;
}

function pagination(path) {
    return path + '/{currentPage}' + '/{perPage}';
}

const apis = {
    dashboard:{
        detail: path('dashboard', 'detail'),
        transactionDetail : path('dashboard', 'getTransactionDetail') + '/{filter}',
        amountDetail : path('dashboard', 'getAmountDetail') + '/{filter}'
    },
    account: {
        detail: path('account', 'detail'),
        logOut: path('account', 'log-out'),
        logOutAll: path('account', 'log-out-all'),
        delete: path('account', 'delete')
    },
    recent: {
        list: pagination(path('recent', 'list')),
    },
    contact: {
        list: pagination(path('contact', 'list')),
        findNew: path('contact', 'find-new'),
        addNew: path('contact', 'add-new')
    },
    payment: {
        toUserDetail: path('payment', 'to-user-detail') + '/{toUserFbid}',
        list: pagination(path('payment', 'list') + '/{toUserFbid}'),
        pay: path('payment', 'pay'),
        request: path('payment', 'request'),
        payRequest: path('payment', 'pay-request')
    },
    transactions: {
        list: pagination(path('transactions', 'list')),
    },
    notifications: {
        new: path('notifications', 'new'),
        list: pagination(path('notifications', 'list')),
        delete: path('notifications', 'delete') + '/{id}' ,
        deleteAll: path('notifications', 'delete-all')
    }
};

export default function api(name, replace = {}) {
    let moduleName = name.split('.')[0];
    let functionalityName = name.split('.')[1];
    let url = apis[moduleName][functionalityName] ?? null;

    if (Object.keys(replace).length) {
        for (let key in replace) {
            if (url.includes('{' + key + '}')) {
                url = url.replace('{' + key + '}', replace[key]);
            }
        }
    }

    return url;
}
