(() => {
    const endpoint = document.currentScript?.dataset.readUrl;
    if (!endpoint) return;

    let timer;
    let attempted = false;
    const cancel = () => window.clearTimeout(timer);
    const start = () => {
        cancel();
        if (attempted) return;
        timer = window.setTimeout(() => {
            attempted = true;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token) return;
            fetch(endpoint, {
                method: "POST",
                credentials: "same-origin",
                headers: { "X-CSRF-TOKEN": token, Accept: "application/json" },
            }).catch(() => {
                /* A read-counter failure must not interrupt reading. */
            });
        }, 10000);
    };

    window.addEventListener("pagehide", cancel);
    window.addEventListener("pageshow", (event) => {
        if (event.persisted) start();
    });
    start();
})();
