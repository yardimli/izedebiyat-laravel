# Total Author writing workspace

The author dashboard is `/eserlerim`; existing `/eserlerim/{hashedId}/duzenle` links open the new editor. The original article row is the book: IDs, slugs, public URLs, reads, comments, favorites, approval flags, author information, and publication metadata remain attached to `articles`.

The port lives under `app/Writer`, `resources/js/writer`, and `resources/views/writer`. It includes Total Author's manuscript editor, AI conversations and approval proposals, Codex, custom entry types, name datasets, revisions and comparisons, TXT/DOCX import and export, model catalog and favorites, typography, themes, local draft recovery, usage reconciliation, and LLM request/response logs. Turkish translation files are copied into `lang/tr` and `lang/tr.json`; the writer maps İzEdebiyat's `tr_TR`/`en_US` locale to `tr`/`en` without changing the rest of the site.

Public browsing controllers and templates, read tracking, comments, and the existing chat system remain in place. Workspace tables use the `writer_` prefix so their chat messages do not collide with the site's existing chat tables. Saving a manuscript also updates the article's Markdown `main_text`, including when an AI edit is approved or a revision is restored. The public frontend keeps its existing renderer.

## Eser dashboard and publication tools

The workspace labels use “eser.” Publication status is changed on the dashboard; saving Eser Bilgileri in the editor preserves that status. Eser Bilgileri opens automatically. Publishing still requires a child category.

Category and tag AI suggestions use the selected OpenRouter model, with the same demo reservation and personal-key rules as chat. Suggestions populate the form and are applied when the author saves. Image generation restores the site's existing FAL generator and requires its existing FAL_API_KEY and server OpenRouter configuration; FAL image charges remain separate from the writer's OpenRouter demo allowance. Generated images can be previewed, saved, or removed. Stored /storage/ paths and full image URLs are used directly.

Archiving and trash recovery are removed. Previously archived works appear on the dashboard. Deleting a work permanently removes that article and its dependent content after an explicit browser confirmation; shared image files and other works are preserved. AI billing records, monetary spending and pending reconciliation survive deletion. Previously soft-deleted records are not bulk-purged by this update.

Existing installations must apply the billing-link migration before enabling the new deletion behavior:

```sh
php artisan migrate --force --path=database/migrations/2026_09_13_100000_preserve_writer_billing_on_work_deletion.php
npm run build
php artisan optimize:clear
```

This migration changes only the AI-call link, without repeating article conversion. Its rollback deliberately retains the nullable link to preserve detached billing records.

## Configuration

Set the amount in `.env` **before migrating**:

```dotenv
WRITER_DEMO_ALLOWANCE_USD=1
```

The workspace uses the site's existing `OPEN_ROUTER_KEY`. An optional `OPENROUTER_API_KEY` overrides it for the writing workspace. These are server keys; members can save their own encrypted OpenRouter key at `/yazi-atolyesi/account`.

The migration gives existing accounts the configured initial credit. New accounts inherit the current amount until their first demo reservation fixes their allowance. Changing the environment amount changes new grants and subsequent resets; it does not silently rewrite existing grants. Reload cached configuration after changing it.

## Deployment and conversion

Back up the database before applying the schema migration. The checked local database contains about 93,000 articles, so schedule conversion during maintenance and allow sufficient time and disk space for structured documents and original-text backups.

```sh
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan down
php artisan config:clear
php artisan migrate --force --path=database/migrations/2026_09_12_100000_add_total_author_workspace.php
php artisan writer:migrate-articles --dry-run
php artisan migrate --force --path=database/migrations/2026_09_12_100001_convert_articles_to_writer_documents.php
php artisan migrate --force --path=database/migrations/2026_09_13_100000_preserve_writer_billing_on_work_deletion.php
php artisan view:clear
php artisan config:cache
php artisan up
```

Legacy zero timestamps such as `0000-00-00 00:00:00` are preserved. Schema migration, rollback, and document conversion temporarily remove only `NO_ZERO_DATE` and `NO_ZERO_IN_DATE` from the current MySQL connection, retaining strict mode and restoring the original settings in `finally`. No global or application-wide SQL configuration changes are needed. If the schema migration previously failed on a zero-date error in its initial `ALTER TABLE`, update the code and rerun `php artisan migrate`; that failed ALTER did not add the columns.

The explicit migration paths avoid replaying unrelated historical imports. The old migrations do not fully describe the current legacy database; this integration targets the existing production article schema.

The conversion processes 100 records at a time and commits each article separately. It preserves `main_text`, `markdown`, `created_at`, and `updated_at`, records the originals in `writer_original_text` / `writer_original_markdown`, and adds the structured document. Already-converted documents are skipped. After an interruption, rerun the data migration or `php artisan writer:migrate-articles`; edited documents are never overwritten. A failed conversion identifies the article ID.

Headings, emphasis, code, line breaks, and scene breaks become editor formatting. Total Author's document schema has no native lists, tables, blockquotes, links, or inline images: list/table/quote text is retained in paragraphs, while link targets and image descriptions/URLs are retained as text. Original Markdown/HTML stays in both `main_text` and the backup until an actual manuscript edit; the backup remains afterwards. Review works using those unsupported formats before editing and republishing them.

Rolling back the schema removes workspace data and its original-text backup columns, but keeps article IDs and the latest Markdown `main_text`. It deliberately does not replace newer writing with the pre-migration text. Export or back up workspace records before rollback if revisions or AI history must be kept.

## Administrator quotas

Open **Yapay zekâ kotaları** in the existing admin menu or visit `/yazi-atolyesi/admin/budgets`.

The page lists every member's total settled AI spending, demo spending, pending reservations, cumulative limit, and remaining USD. Member-facing allowance indicators show a percentage.

A reset locks the account row and sets:

```text
new limit = lifetime demo spending + pending reservations + configured allowance
remaining percentage = available credit / most recent allowance, capped at 100%
```

For example, spending $0.80 from a $1 grant and resetting produces a $1.80 cumulative limit and $1 available again. Spending history is retained. Pending charges remain reserved, and reconciliation remains idempotent. Resets record the administrator, previous/new limits, amount, and time in `writer_budget_resets`.

Personal-key requests bypass demo credit and continue working after demo exhaustion. Personal keys are encrypted at rest and excluded from serialization and request logs. The existing Laravel scheduler now also runs `writer:reconcile-usage` every five minutes; keep the site's `schedule:run` cron enabled.

## Verification

```sh
php artisan test --filter='WriterIntegrationTest|ImportedWriterWorkspaceTest'
node --test tests/writer-money.test.mjs tests/writer-revision-diff.test.mjs
npm run build
```

The 59 PHP tests use an isolated in-memory SQLite fixture matching the relevant legacy schema. They cover migration, rollback, grant initialization, metadata and image persistence, public route preservation, ownership, revisions, AI proposals, uncertain charges, resets, encryption, and personal keys. Three JavaScript tests cover cost display and revision comparisons. A read-only sample of 110 real articles, including the ten largest, also converted successfully.

Optional browser checks use synthetic Turkish fixtures and mocked API responses; they do not modify the site's database or call an LLM:

```sh
php scripts/preview-writer.php
node scripts/check-writer-browser.cjs
```

Install Playwright in your development tooling or set `WRITER_PLAYWRIGHT_MODULE` to its package directory. Chrome must be available. Screenshots are written to ignored `storage/app/writer-preview/`. The browser check covers the dashboard, editor initialization, publication-details submission, mobile manuscript width, and the admin quota page.

A MySQL-specific regression check reproduces the strict-mode failure and verifies successful ALTER/update operations and SQL-mode restoration using connection-local temporary tables only:

```sh
php scripts/check-writer-zero-dates.php
```

### Retrying the billing-link migration

If the billing migration fails with errno 121 (duplicate constraint name), deploy the updated migration and rerun `php artisan migrate`. It inspects the existing foreign key, skips the already-correct schema, and separates dropping, changing nullability, and adding the new relationship. It can resume if the foreign key was already dropped. No article reconversion or database reset is needed.

`php scripts/check-writer-billing-migration.php` verifies the upgrade and interrupted retries in a randomly named local MySQL/MariaDB test database, then removes only that test database. It refuses remote database hosts.

## Shared account and administration design

Chat, favorites, profile settings, image management and administration use the writer layout and its paper, light and dark palettes. The account page at `/yazi-atolyesi/hesap` combines profile/password forms and personal OpenRouter settings. Administrators find AI quota management there, at `/yazi-atolyesi/admin/kotalar`. The shared toolbar includes favorites, chat, account and an administrator-only menu; the editor header includes only the library, typography and appearance controls.

User-facing administration URLs use Turkish: `/admin/kullanicilar`, `/admin/eserler`, `/admin/okuma-kayitlari`, `/admin/kitap-incelemeleri`, `/admin/kitap-yazarlari`, `/admin/alintilar`, `/admin/yapay-zeka-ayarlari`. Existing English page URLs redirect while preserving query parameters, and old form action URLs continue working. Public reading URLs and internal API endpoints remain compatible.

After deploying, run `npm run build` and `php artisan optimize:clear`. No new database migration is required for this design update. Browser fixtures also cover account, chat, favorites, users and articles at desktop/mobile widths in all three palettes.

## Administrator account viewing

The users table login-as action saves the original administrator ID and the viewed user ID in the server-side session. A banner on writer, account and public site layouts identifies the viewed account and offers `Yönetici hesabıma dön`. The CSRF-protected POST return action (`/yoneticiye-don`) validates the saved identity and rechecks that the original account remains an administrator. Nested switches are blocked; logging out or session expiration ends the saved return context. Identity switches rotate the session ID and CSRF token and clear password-confirmation state.
