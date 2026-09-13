import { t, locale } from "./i18n";
import { api, $, action, notify } from "./api";

export function start() {
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
    document.querySelectorAll("[data-book-files]").forEach((card) => {
        card.querySelector("[data-import-book]").onclick = async () => {
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
        card.querySelector("[data-export-book]").onclick = () => {
            const link = document.createElement("a");
            link.href = `/yazi-atolyesi/eserler/${card.dataset.bookFiles}/disari-aktar/${card.querySelector("select").value}`;
            link.download = "";
            link.click();
        };
    });
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
            notify(t("Story imported. Open the manuscript to continue writing or scan its codex."));
        } finally {
            $("#library-confirm-import").disabled = false;
        }
    });
}
