import fs from "node:fs/promises";
import path from "node:path";
import { Formatter } from "blade-formatter";

const check = process.argv.includes("--check");
const options = JSON.parse(await fs.readFile(".bladeformatterrc.json", "utf8"));
async function walk(directory) {
    const files = [];
    for (const item of await fs.readdir(directory, { withFileTypes: true })) {
        const file = path.join(directory, item.name);
        if (item.isDirectory()) files.push(...(await walk(file)));
        else if (file.endsWith(".blade.php")) files.push(file);
    }
    return files;
}
let changed = 0;
for (const file of await walk("resources/views")) {
    const original = await fs.readFile(file, "utf8");
    let formatted = original;
    let stable = false;
    for (let pass = 0; pass < 4; pass++) {
        const next = await new Formatter(options).formatContent(formatted);
        if (original.trim() && !next.trim()) throw new Error("Formatter emptied " + file);
        if (next === formatted) {
            stable = true;
            break;
        }
        formatted = next;
    }
    if (!stable) throw new Error("Formatter did not stabilize: " + file);
    if (formatted === original) continue;
    changed++;
    if (check) {
        console.log(file);
        continue;
    }
    // Never truncate a source file if a write fails on Windows.
    const temporary = file + ".format-tmp";
    await fs.writeFile(temporary, formatted);
    await fs.rename(temporary, file);
}
console.log(
    check ? changed + " Blade templates need formatting." : changed + " Blade templates formatted.",
);
if (check && changed) process.exitCode = 1;
