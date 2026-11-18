// Bootstrap for client-side globals. Inertia handles XHR, so no axios needed here.
window.addEventListener('error', (event) => {
    // Surface uncaught client errors during local development without breaking the app.
    if (import.meta.env.DEV) {
        console.warn('[Syntexis] Uncaught error:', event.error ?? event.message);
    }
});
