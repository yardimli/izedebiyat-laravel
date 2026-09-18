import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";
import vm from "node:vm";

const source = fs
    .readFileSync(new URL("../resources/js/writer/library.js", import.meta.url), "utf8")
    .replace(/^import .*;\r?\n/gm, "")
    .replace("export function start", "function start");
function page({ returning = false, failure = false, justPublished = false } = {}) {
    const events = {},
        calls = [],
        storage = new Map();
    if (justPublished) storage.set("writer-published-return-7", "1");
    const node = () => ({
        shown: 0,
        disabled: false,
        dataset: {},
        querySelector: () => null,
        showModal() {
            this.shown++;
        },
        close() {
            this.shown--;
        },
    });
    const elements = { "#create-story-dialog": node(), "#draft-reminder-dialog": node() };
    if (returning) {
        elements["#returned-story"] = { dataset: { book: "12", revision: "1" } };
        elements["#publish-story-dialog"] = node();
        elements["#publish-new-story"] = node();
        elements["#publish-new-story"].disabled = true;
        elements["#publication-progress"] = node();
        delete elements["#draft-reminder-dialog"];
    }
    vm.runInNewContext(source + "\nstart();", {
        document: {
            body: { dataset: { user: "7" } },
            querySelectorAll: () => [],
            querySelector: () => null,
        },
        window: {
            location: {
                reload() {
                    calls.push("reload");
                },
            },
            addEventListener(name, fn) {
                events[name] = fn;
            },
        },
        sessionStorage: {
            getItem: (k) => storage.get(k),
            removeItem: (k) => storage.delete(k),
            setItem: (k, v) => storage.set(k, v),
        },
        $: (selector) => elements[selector] || null,
        action: (selector, fn) => {
            events[selector] = fn;
        },
        t: (key) => key,
        notify() {},
        api: async (url, method, body) => {
            calls.push({ url, method, body });
            if (failure) throw new Error("Category service unavailable");
            return { revision: 2, category_id: 5 };
        },
    });
    return { elements, events, calls, storage };
}
const settled = () => new Promise((resolve) => setImmediate(resolve));

test("normal entry reminds about drafts and creation opens the title dialog", () => {
    const p = page();
    assert.equal(p.elements["#draft-reminder-dialog"].shown, 1);
    assert.equal(p.elements["#create-story-dialog"].shown, 0);
    p.events["#create-story"]();
    assert.equal(p.elements["#create-story-dialog"].shown, 1);
});

test("return prepares the category without publishing until the author agrees", async () => {
    const p = page({ returning: true });
    assert.equal(p.elements["#publish-story-dialog"].shown, 1);
    assert.equal(p.elements["#publish-new-story"].disabled, true);
    await settled();
    assert.equal(p.calls.length, 1);
    assert.match(p.calls[0].url, /prepare-return$/);
    await p.events["#publish-new-story"]();
    assert.equal(p.calls[1].method, "PATCH");
    assert.equal(p.calls[1].body.revision, 2);
    assert.equal(p.calls[1].body.is_published, true);
    assert.equal(p.storage.get("writer-published-return-7"), "1");
});

test("category failure keeps the story unpublished and allows retry", async () => {
    const p = page({ returning: true, failure: true });
    await settled();
    await p.events["#publish-new-story"]();
    assert.equal(p.calls.filter((call) => call.method === "PATCH").length, 0);
    assert.equal(p.elements["#publish-story-dialog"].shown, 1);
    assert.equal(p.elements["#publish-new-story"].disabled, false);
    assert.match(p.elements["#publication-progress"].textContent, /unavailable/);
});

test("publication refresh suppresses the draft reminder only once", () => {
    const p = page({ justPublished: true });
    assert.equal(p.elements["#draft-reminder-dialog"].shown, 0);
    assert.equal(p.storage.has("writer-published-return-7"), false);
    p.events.pageshow({ persisted: true });
    assert.deepEqual(p.calls, ["reload"]);
});
