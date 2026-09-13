import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";
import vm from "node:vm";

const source = fs.readFileSync(new URL("../public/js/article-read.js", import.meta.url), "utf8");
function page() {
    let now = 0;
    let id = 0;
    const timers = new Map();
    const events = {};
    const calls = [];
    vm.runInNewContext(source, {
        document: {
            currentScript: { dataset: { readUrl: "/yapit/12/read" } },
            querySelector: () => ({ content: "csrf" }),
        },
        window: {
            setTimeout: (fn, delay) => {
                timers.set(++id, { fn, at: now + delay });
                return id;
            },
            clearTimeout: (id) => timers.delete(id),
            addEventListener: (name, fn) => {
                events[name] = fn;
            },
        },
        fetch: (url, options) => {
            calls.push({ url, options });
            return Promise.resolve();
        },
    });
    return {
        calls,
        events,
        advance(ms) {
            now += ms;
            for (const [key, timer] of timers)
                if (timer.at <= now) {
                    timers.delete(key);
                    timer.fn();
                }
        },
    };
}

test("records once after ten seconds without requiring scrolling", () => {
    const p = page();
    p.advance(9999);
    assert.equal(p.calls.length, 0);
    p.advance(1);
    assert.equal(p.calls.length, 1);
    assert.equal(p.calls[0].url, "/yapit/12/read");
    assert.equal(p.calls[0].options.headers["X-CSRF-TOKEN"], "csrf");
    p.advance(10000);
    p.events.pageshow({ persisted: true });
    p.advance(10000);
    assert.equal(p.calls.length, 1);
});

test("leaving early cancels the read; a restored page starts a fresh timer", () => {
    const p = page();
    p.advance(9000);
    p.events.pagehide();
    p.advance(5000);
    assert.equal(p.calls.length, 0);
    p.events.pageshow({ persisted: true });
    p.advance(9999);
    assert.equal(p.calls.length, 0);
    p.advance(1);
    assert.equal(p.calls.length, 1);
});
