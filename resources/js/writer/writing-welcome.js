import { t } from "./i18n";
import { api } from "./api";
export function showWritingWelcome(initialModel) {
    const dialog = document.querySelector("#writing-welcome");
    const chat = document.querySelector("#chat-input");
    const picker = document.querySelector("#model-picker");
    const setModel = (name) => {
        document.querySelector("#welcome-model").textContent = name
            ? t("You’ll be writing with :v0.", { v0: name })
            : t("Choose an AI model from the model picker before sending your first message.");
    };
    setModel(initialModel);
    let highlightTimer;
    let hiddenForSession = dialog.dataset.hiddenForSession === "1";
    let saving = false;
    const open = () => {
        if (hiddenForSession) return;
        clearTimeout(highlightTimer);
        chat.classList.remove("welcome-highlight");
        picker.classList.remove("welcome-highlight");
        if (!dialog.open) dialog.showModal();
    };
    dialog.addEventListener("close", () => {
        if (window.matchMedia("(max-width: 760px)").matches) {
            document.querySelector("#show-chat").click();
        }
        requestAnimationFrame(() => {
            chat.classList.add("welcome-highlight");
            picker.classList.add("welcome-highlight");
            clearTimeout(highlightTimer);
            highlightTimer = setTimeout(() => {
                chat.classList.remove("welcome-highlight");
                picker.classList.remove("welcome-highlight");
            }, 2400);
            chat.focus({ preventScroll: true });
            chat.setSelectionRange(chat.value.length, chat.value.length);
            chat.scrollIntoView({ block: "nearest" });
        });
    });
    const close = async () => {
        if (saving) return;
        const checkbox = document.querySelector("#welcome-hide-session");
        const errorMessage = document.querySelector("#welcome-preference-error");
        if (checkbox.checked) {
            saving = true;
            errorMessage.hidden = true;
            try {
                await api(dialog.dataset.preferenceUrl, "POST", { hidden: true });
                hiddenForSession = true;
            } catch (error) {
                errorMessage.textContent = error.message;
                errorMessage.hidden = false;
                return;
            } finally {
                saving = false;
            }
        }
        dialog.close();
    };
    dialog
        .querySelectorAll("[data-welcome-close]")
        .forEach((button) => button.addEventListener("click", close));
    dialog.addEventListener("cancel", (event) => {
        event.preventDefault();
        close();
    });
    dialog.setAttribute("closedby", "closerequest");
    window.addEventListener("pageshow", async (event) => {
        if (!event.persisted) return;
        try {
            const preference = await api(dialog.dataset.preferenceUrl);
            hiddenForSession = preference.hidden;
            if (hiddenForSession && dialog.open) dialog.close();
            open();
        } catch {
            /* Keep the last known session preference when offline. */
        }
    });
    open();
    return { setModel };
}
