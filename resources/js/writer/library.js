import { t, locale } from "./i18n";
import { api, $, action, notify } from "./api";

export function start() {
    const reminderKey = "writer-published-return-" + document.body.dataset.user;
    let justPublished = false;
    try {
        justPublished = sessionStorage.getItem(reminderKey) === "1";
        sessionStorage.removeItem(reminderKey);
    } catch {}
    const createDialog = $("#create-story-dialog");
    action("#create-story", () => createDialog.showModal());
    if (createDialog.querySelector('[role="alert"]')) createDialog.showModal();
    else if (!justPublished) $("#draft-reminder-dialog")?.showModal();
    window.addEventListener("pageshow", (event) => {
        if (event.persisted) window.location.reload();
    });
    const returned = $("#returned-story");
    const publishDialog = $("#publish-story-dialog");
    if (returned) {
        const publishButton = $("#publish-new-story");
        const returnBase = "/yazi-atolyesi/api/books/" + returned.dataset.book;
        let revision = Number(returned.dataset.revision);
        let prepared = false;
        const statusSelect = document.querySelector(
            '[data-publish-book="' + returned.dataset.book + '"]',
        );
        const prepare = async () => {
            const result = await api(returnBase + "/prepare-return", "POST", {});
            revision = result.revision;
            prepared = true;
            if (statusSelect) statusSelect.dataset.revision = revision;
            if (publishDialog)
                $("#publication-progress").textContent = t(
                    "Your story is saved. You can publish it now or leave it as a draft.",
                );
        };
        publishDialog?.showModal();
        if (statusSelect) statusSelect.disabled = true;
        const preparing = prepare()
            .catch((error) => {
                const message = t(
                    "Your story remains saved. Automatic category selection could not finish: :reason",
                    { reason: error.message },
                );
                if (publishDialog) $("#publication-progress").textContent = message;
                else notify(message);
            })
            .finally(() => {
                if (publishButton) publishButton.disabled = false;
                if (statusSelect) statusSelect.disabled = false;
            });
        action("#publish-new-story", async () => {
            publishButton.disabled = true;
            try {
                await preparing;
                if (!prepared) await prepare();
                await api(returnBase, "PATCH", { revision, is_published: true });
                publishDialog.close();
                try {
                    sessionStorage.setItem(reminderKey, "1");
                } catch {}
                window.location.reload();
            } catch (error) {
                $("#publication-progress").textContent = error.message;
            } finally {
                publishButton.disabled = false;
            }
        });
    }
    document.querySelectorAll("[data-publish-book]").forEach((button) => {
        button.onchange = async () => {
            button.disabled = true;
            try {
                await api(`/yazi-atolyesi/api/books/${button.dataset.publishBook}`, "PATCH", {
                    revision: Number(button.dataset.revision),
                    is_published: button.value === "1",
                });
                window.location.reload();
            } catch (e) {
                notify(e.message);
                button.value = button.dataset.published;
                button.disabled = false;
            }
        };
    });
    document.querySelectorAll("[data-delete-book]").forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (
                !confirm(t("Permanently delete this work and its comments? This cannot be undone."))
            )
                event.preventDefault();
        });
    });
    let bookId, revision;
    let readVersion = 0;
    const dialog = $("#library-import-dialog");
    const exportDialog = $("#library-export-dialog");
    let exportBookId;
    document.querySelectorAll("[data-book-files]").forEach((card) => {
        const importButton = card.querySelector("[data-import-book]");
        if (importButton)
            importButton.onclick = async () => {
                try {
                    const state = await api(`/yazi-atolyesi/api/books/${card.dataset.bookFiles}`);
                    bookId = card.dataset.bookFiles;
                    revision = state.book.revision;
                    readVersion++;
                    $("#library-import-file").value = "";
                    $("#library-import-preview").value = "";
                    $("#library-confirm-import").disabled = true;
                    dialog.showModal();
                } catch (e) {
                    notify(e.message);
                }
            };
        card.querySelector("[data-export-book]").onclick = (event) => {
            event.preventDefault();
            exportBookId = card.dataset.bookFiles;
            $("#library-export-format").value = "txt";
            exportDialog.showModal();
        };
    });
    action(
        "#library-export-form",
        (event) => {
            event.preventDefault();
            if (!exportBookId) return;
            const link = document.createElement("a");
            link.href =
                "/yazi-atolyesi/eserler/" +
                exportBookId +
                "/disari-aktar/" +
                $("#library-export-format").value;
            link.download = "";
            document.body.append(link);
            link.click();
            link.remove();
            exportDialog.close();
        },
        "submit",
    );
    action("#library-cancel-import", () => dialog.close());
    action(
        "#library-import-file",
        async () => {
            const version = ++readVersion;
            const file = $("#library-import-file").files[0];
            $("#library-confirm-import").disabled = true;
            if (!file) return;
            if (file.size > 10 * 1024 * 1024)
                throw new Error(t("Please import a file smaller than 10 MB."));
            let text;
            if (file.name.toLowerCase().endsWith(".docx")) {
                const mammoth = await import("mammoth/mammoth.browser");
                text = (
                    await mammoth.extractRawText({
                        arrayBuffer: await file.arrayBuffer(),
                    })
                ).value;
            } else if (file.name.toLowerCase().endsWith(".txt")) text = await file.text();
            else throw new Error(t("Choose a TXT or DOCX story."));
            if (version !== readVersion) return;
            $("#library-import-preview").value = text;
            $("#library-confirm-import").disabled = false;
        },
        "change",
    );
    action("#library-confirm-import", async () => {
        $("#library-confirm-import").disabled = true;
        try {
            const document = {
                type: "doc",
                content: $("#library-import-preview")
                    .value.split(/\r?\n/)
                    .map((text) => ({
                        type: "paragraph",
                        ...(text ? { content: [{ type: "text", text }] } : {}),
                    })),
            };
            await api(`/yazi-atolyesi/api/books/${bookId}`, "PATCH", { revision, document });
            dialog.close();
            window.location.reload();
            notify(t("Story imported. Open the manuscript to continue writing or scan its codex."));
        } finally {
            $("#library-confirm-import").disabled = false;
        }
    });
}
