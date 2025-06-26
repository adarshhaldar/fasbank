const assets = {
    images: {
        logo: '/assets/images/logo.svg',
        warningIcon: '/assets/images/warning-icon.svg',
        loginPageTab: '/assets/images/login-page-tab.svg',
        noTransactionEmptyState: '/assets/images/no-transaction-empty-state.svg',
        noSearchEmptyState: '/assets/images/no-search-found-empty-state.svg',
        searchPersonEmptyState: '/assets/images/search-person-empty-state.svg',
        noPaymentEmptyState: '/assets/images/no-payment-empty-state.svg',
        noNotificationEmptyState: '/assets/images/no-notification-empty-state.svg',
        defaultUserAvatar: '/assets/images/default-profile-avatar.svg'
    }
};

export default function asset(name, ) {
    let folderName = name.split('.')[0];
    let fileName = name.split('.')[1];
    let url = assets[folderName][fileName] ?? null;

    return url;
}
