const variables = {
    APP_ENV: import.meta.env.VITE_APP_ENV,
    APP_NAME: import.meta.env.VITE_APP_NAME,
    APP_URL: import.meta.env.VITE_APP_URL,
    APP_ASSET_URL: import.meta.env.VITE_APP_ASSET_URL,
    GOOGLE_REDIRECT_URL: import.meta.env.VITE_GOOGLE_REDIRECT_URL
};

export default function env(key) {
    return variables[key] ?? null;
}
