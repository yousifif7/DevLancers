# DevLancer — Project Analysis & Feature Roadmap

> **Purpose:** Reference document for AI agents and developers working on DevLancer.  
> **Last updated:** June 2026  
> **Stack:** Laravel 10, PHP 8.1+, Blade, Bootstrap 5, Stripe, Laravel Sanctum

---

## 1. Project Overview

**DevLancer** is a small Laravel freelancer marketplace MVP. It supports a dual marketplace where:

- **Workers** (`acc_type = 1`) post **Gigs** — services they offer
- **Clients** (`acc_type = 2`) post **Jobs** — work they need done

There are only **4 domain models**. There are no separate `Job`, `Contract`, `Proposal`, `Milestone`, or `Review` models. Those concepts are approximated with simpler naming and flows.

### Tech Stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 10 |
| PHP | 8.1+ |
| Frontend | Blade + Bootstrap 5 |
| Payments | Stripe Checkout (`stripe/stripe-php`) |
| API | Laravel Sanctum (scaffold only) |
| Debugging | Clockwork |

---

## 2. Terminology Mapping

| Marketplace Concept | DevLancer Implementation |
|---------------------|--------------------------|
| Jobs | `gigs` where `gig_type = 'job'` |
| Freelancers / Workers | Users with `acc_type = 1` |
| Clients | Users with `acc_type = 2` |
| Proposals | `requests` (free-text messages, not formal bids) |
| Contracts | `tasks` (status + price + payment_flag) |
| Payments | Stripe Checkout + `tasks.payment_flag` |
| Notifications | UI page listing received `requests` (not Laravel Notifications) |
| Milestones | **Not implemented** |
| Reviews | **Not implemented** |

---

## 3. Database Schema

### Tables

| Table | Purpose |
|-------|---------|
| `users` | Auth + profile (`acc_type`, `bio`, `address`, `gender`) |
| `gigs` | Listings (both worker gigs and client jobs) |
| `requests` | Messages / inquiries between users |
| `tasks` | Hired work / pseudo-contracts |
| `personal_access_tokens` | Laravel Sanctum |
| `password_reset_tokens` | Laravel default |
| `failed_jobs` | Laravel default |

### Key Model Fields

**`users`**
- `name`, `email`, `password`, `gender`, `bio`, `address`, `acc_type` (1=Worker, 2=Client)

**`gigs`**
- `user_id`, `gig_type` (`gig` or `job`), `title`, `tag`, `description`, `email`, `salary`, `image`

**`requests`**
- `user_id`, `gig_id`, `reciever`, `sender`, `message`

**`tasks`**
- `user_id` (worker), `gig_id`, `status`, `owner` (client user id), `content`, `price`, `payment_flag`

---

## 4. Model Relationships

```
User
 ├── hasMany Gigs (via user_id)
 ├── hasMany Requests (via user_id)
 └── hasMany Tasks (via user_id)  ← BUG: currently points to Requests

Gigs
 ├── belongsTo User
 ├── hasMany Requests (via gig_id)
 └── hasMany Tasks (via gig_id)   ← BUG: currently points to Requests

Requests
 ├── belongsTo User
 └── belongsTo Gigs

Tasks
 ├── belongsTo User (worker)
 └── belongsTo Gigs
 └── (no belongsTo for owner as User)
```

### Known Model Bugs

- `User::tasks()` in `app/Models/User.php` returns `hasMany(Requests::class)` — should be `Tasks::class`
- `Gigs::tasks()` in `app/Models/Gigs.php` returns `hasMany(Requests::class)` — should be `Tasks::class`
- `Tasks` model has no `owner()` relationship to `User`

---

## 5. Current Business Flow

```
Gigs/Jobs (listings)
    → Requests (messages / informal interest)
        → Tasks (hire record)
            → Stripe payment (optional, flawed)
                → Client closes task (end)
```

### Phase 1 — Registration & Roles

1. User signs up at `/signup` with `acc_type`: Worker (1) or Client (2)
2. Profile at `/gigs/profile`; public profile at `/users/{id}`

### Phase 2 — Posting Listings

3. Workers post Gigs (`gig_type = gig`)
4. Clients post Jobs (`gig_type = job`)
5. CRUD: create `/gigs/create`, show `/gigs/{gig}`, edit, delete
6. Homepage `/` lists all gigs with search (`?search=`), tag (`?tag=`), type (`?type=gig|job`) filters

### Phase 3 — Inquiry (Proposal-like)

7. Non-owner visits a listing and submits a **Request** (message) via form on gig detail page → `POST /requests`
8. Recipient sees messages at `/user/notifications/{id}`
9. Sender sees sent messages at `/user/sent/{id}`
10. Reply/chat via `/reply/{userId}`

### Phase 4 — Hire (Contract Creation)

11. On the notifications page, either party can click **Accept** → `POST /tasks/create`
12. Creates a **Task** with `status = Pending`, `price = gig.salary`, `content = message text`
13. Role assignment:
    - **Client** accepting on their job: `user_id` = worker (sender), `owner` = client
    - **Worker** accepting on their gig: `user_id` = worker (self), `owner` = client (sender)

### Phase 5 — Payment

14. Client views tasks at `/user/tasks/{id}` → `cltasks.blade.php`
15. While `status = Pending` and `payment_flag = 0`, client can **Checkout** → `POST /session` (Stripe)
16. `payment_flag` is set to `1` **before** Stripe redirect (not after webhook confirmation)
17. Success page `/success` shows a flash message only

### Phase 6 — Completion / Ending

18. Client can **Close** a pending task → `POST /task/edit/{task}` sets `status = Done`
19. Final states (client view):
    - `Done` + `payment_flag = 0` → shown as **"Cancelled"**
    - `Done` + `payment_flag = 1` → shown as **"Done"** (completed)
    - `Pending` + paid → worker sees "Payment reached out"; client can still close
20. Worker view (`wotasks.blade.php`): read-only status; **cannot** mark work complete or deliver files

---

## 6. Routes Reference

### Authentication & Users

| Route | Method | Feature |
|-------|--------|---------|
| `/signup` | GET | Registration form |
| `/users` | POST | Create user |
| `/login` | GET | Login form |
| `/users/authenticate` | POST | Login |
| `/logout` | POST | Logout |
| `/gigs/profile` | GET | Own profile + listings |
| `/users/{id}` | GET | Public user profile |
| `/users/{user}` | PUT | Edit profile |
| `/user/notifications/{id}` | GET | Received messages + hire button |
| `/user/sent/{id}` | GET | Sent messages |

### Listings (Gigs/Jobs)

| Route | Method | Feature |
|-------|--------|---------|
| `/` | GET | Browse/search listings |
| `/gigs/create` | GET | Create listing form |
| `/gigs` | POST | Store listing |
| `/gigs/{gig}` | GET | Listing detail + contact form |
| `/gigs/{gig}/edit` | GET | Edit listing form |
| `/gigs/{gig}` | PUT | Update listing |
| `/gigs/{gig}` | POST | Delete listing |

### Messaging

| Route | Method | Feature |
|-------|--------|---------|
| `/requests` | POST | Send message |
| `/reply/{msg}` | GET | Reply + chat UI |
| `/request/{id}` | POST | Delete one message |
| `/request/sent/{id}` | POST | Bulk delete sent |
| `/request/recieved/{id}` | POST | Bulk delete received |

### Tasks & Payments

| Route | Method | Feature | Auth |
|-------|--------|---------|------|
| `/user/tasks/{id}` | GET | Task list | ✅ |
| `/tasks/create` | POST | Hire / create task | ❌ **Missing** |
| `/task/edit/{task}` | POST | Update task (close/cancel) | ❌ **Missing** |
| `/session` | POST | Stripe checkout | ❌ **Missing** |
| `/success` | GET | Payment success | — |
| `/checkout` | GET | Payment cancel | — |

### API

| Route | Method | Feature |
|-------|--------|---------|
| `/api/user` | GET | Sanctum user (scaffold only) |

---

## 7. Key File Locations

| Purpose | Path |
|---------|------|
| Web routes | `routes/web.php` |
| API routes | `routes/api.php` |
| Models | `app/Models/` |
| Controllers | `app/Http/Controllers/` |
| Migrations | `database/migrations/` |
| Views | `resources/views/` |
| Hire logic (view) | `resources/views/messages/notifications.blade.php` |
| Client tasks view | `resources/views/tasks/cltasks.blade.php` |
| Worker tasks view | `resources/views/tasks/wotasks.blade.php` |
| Stripe controller | `app/Http/Controllers/StripeController.php` |
| Tasks controller | `app/Http/Controllers/TasksController.php` |
| Requests controller | `app/Http/Controllers/RequestsController.php` |
| Gig controller | `app/Http/Controllers/GigController.php` |
| User controller | `app/Http/Controllers/UserController.php` |

---

## 8. Features That Exist Today

- Dual marketplace: workers post services, clients post jobs (same `gigs` table)
- User roles via `acc_type`
- Listing CRUD with image upload, tags, salary, search/filter
- Messaging system with received/sent views and threaded reply UI
- One-click "hire" from a message → creates a task
- Basic task lifecycle: `Pending` → `Done` or cancelled
- Stripe Checkout integration (client pays listed salary)
- Profile management (bio, gender, country/address)
- Navbar badges for message count and task count
- Database seeder with sample user + 6 gigs
- Pagination on homepage (8 per page)

---

## 9. Known Bugs & Security Issues

### Critical — Fix Before New Features

| Issue | Location | Details |
|-------|----------|---------|
| Wrong model relationship | `app/Models/User.php` | `tasks()` points to `Requests` instead of `Tasks` |
| Wrong model relationship | `app/Models/Gigs.php` | `tasks()` points to `Requests` instead of `Tasks` |
| Payment flag set too early | `app/Http/Controllers/StripeController.php` | `payment_flag = 1` set before Stripe confirms payment |
| No Stripe webhook | — | No verification of completed payments |
| No auth on task routes | `routes/web.php` | `/tasks/create`, `/task/edit/{task}` lack `auth` middleware |
| No auth on payment route | `routes/web.php` | `/session` lacks `auth` middleware |
| No ownership checks | `TasksController` | Any user can create/edit any task |
| IDOR vulnerability | `UserController` | `/user/notifications/{id}` and `/user/tasks/{id}` don't verify `$id === Auth::id()` |
| Message deletion | `RequestsController` | Any authenticated user can delete any message by ID |
| Gender update bug | `UserController::update` | Writes `'Gender'` (capital G) but DB column is `gender` |
| Duplicate hires | `notifications.blade.php` | Accept can be clicked multiple times → duplicate tasks |
| DM hire blocked | Hire flow | Messages without `gig_id` (DM from profile) cannot be accepted into a task |

### Code Quality Issues

- Many empty/stub controller methods (`index`, `show`, `edit`, `update`, `destroy`)
- Views use inline `<?php App\Models\... ?>` queries instead of controller-passed data
- No Form Requests, Policies, or Gates for authorization
- Commented-out code throughout controllers and views
- `README.md` is still the default Laravel readme
- `GigsFactory` missing required fields (`gig_type`, `salary`, `user_id`)
- `.env.example` has no `STRIPE_KEY` / `STRIPE_SECRET` entries
- No tests beyond default Laravel example tests

---

## 10. Completely Missing Features

| Feature | Status |
|---------|--------|
| Formal proposals (bid amount, cover letter, expiry, accept/reject) | ❌ |
| Contracts (terms, start/end dates, signatures) | ❌ |
| Milestones (phased delivery, partial payments) | ❌ |
| Reviews / ratings | ❌ |
| Disputes / refunds | ❌ |
| Escrow / worker payouts (Stripe Connect) | ❌ |
| Deliverables / file uploads for completed work | ❌ |
| Job status (open, in progress, filled, closed) | ❌ |
| Email or push notifications | ❌ |
| Real-time chat | ❌ |
| Admin panel | ❌ |
| API for mobile/SPA | ❌ |
| Payment records table | ❌ |

---

## 11. Lifecycle Gap Matrix

| Lifecycle Step | Current State | Recommended Addition |
|----------------|---------------|----------------------|
| Client posts job | ✅ Working | Job status, categories, deadlines |
| Worker discovers & applies | ⚠️ Message only | Formal proposals with bid & timeline |
| Client reviews proposals | ⚠️ Inbox only | Proposal comparison, reject/shortlist |
| Client awards / hires | ⚠️ One-click, duplicates | Accept one proposal → create contract |
| Contract terms agreed | ❌ Missing | Contract page with scope & dates |
| Milestone plan | ❌ Missing | Milestone table + UI |
| Escrow / fund project | ⚠️ Stripe, flawed | Escrow + webhook-confirmed payments |
| Worker delivers work | ❌ Missing | File upload + submit for review |
| Client approves delivery | ⚠️ "Close" only | Approve → release payment |
| Payment released to worker | ❌ Missing | Stripe Connect payouts |
| Review & rating | ❌ Missing | Ratings + contract archive |
| Contract archived / job closed | ❌ Missing | Auto-close job on hire |

---

## 12. Recommended Enhancements by Stage

### Stage 1 — Job / Gig Listing

**Add:**
- Job status on listings: `open`, `in_progress`, `filled`, `closed`, `expired`
- Application counter (how many workers applied)
- Skills & categories (replace single `tag` with structured data)
- Budget types: fixed price, hourly, range
- Deadline / timeline fields
- Auto-close job when hire is accepted

### Stage 2 — Proposals / Applications

**Add:**
- `proposals` table: `gig_id`, `freelancer_id`, `cover_letter`, `bid_amount`, `delivery_days`, `status`
- Proposal statuses: `pending`, `shortlisted`, `accepted`, `rejected`, `withdrawn`
- Proposal inbox for clients with side-by-side comparison
- Reject / shortlist actions
- One accepted proposal per job (prevent duplicate hires)

### Stage 3 — Contract Creation

**Add:**
- Extend or rename `Tasks` into `contracts` with: `start_date`, `end_date`, `terms`, `scope_of_work`, `proposal_id`
- Contract statuses: `draft`, `active`, `in_review`, `completed`, `cancelled`, `disputed`
- Both parties must agree before work starts
- Contract summary page both can view

### Stage 4 — Milestones & Deliverables

**Add:**
- `milestones` table: title, amount, due date, status
- Deliverable uploads (files, links, notes from worker)
- Worker "Submit for review" action
- Client revision requests before approval
- Partial payments per milestone (optional)

### Stage 5 — Payments & Escrow

**Fix first:**
- Set `payment_flag` only after Stripe webhook confirms payment
- Add `auth` middleware to payment and task routes
- Add ownership checks on all task operations

**Add:**
- `payments` table: `task_id`, `stripe_session_id`, `amount`, `status`, `paid_at`
- Stripe webhook handler for `checkout.session.completed`
- Escrow model (hold funds until client approves delivery)
- Stripe Connect for freelancer payouts
- Refunds / basic dispute flow

### Stage 6 — Contract Completion & Reviews

**Add:**
- Two-step completion: (1) worker submits deliverables, (2) client approves → payment released
- `reviews` table: `contract_id`, `reviewer_id`, `rating`, `comment`, `type`
- Public freelancer rating on profile
- Contract archive / history page
- Email notifications via Laravel `Notifiable`

### Stage 7 — Messaging & Notifications

**Add:**
- Laravel Notifications (database + email) for: new proposal, hire, payment, delivery, completion
- In-app notification bell
- Conversation threads tied to `gig_id` / `contract_id`
- Optional: real-time chat (Laravel Echo + Pusher)

### Stage 8 — Security & Code Quality

**Fix:**
- Model relationship bugs (`User::tasks()`, `Gigs::tasks()`)
- Authorization on all user-specific routes (verify `$id === Auth::id()`)
- Add Policies and Form Requests
- Move DB queries from Blade views to controllers
- Fix gender update bug in `UserController`
- Add `STRIPE_KEY` / `STRIPE_SECRET` to `.env.example`

---

## 13. Implementation Phases (Suggested Order)

### Phase 1 — Stabilize (1–2 weeks) ✅ COMPLETED

1. ✅ Fix model relationships (`User::tasks()`, `Gigs::tasks()`)
2. ✅ Add `auth` middleware to task/payment routes
3. ✅ Add ownership checks (IDOR fixes)
4. ✅ Fix Stripe flow (webhook + `payments` table)
5. ✅ Prevent duplicate hires from same message (`request_id` on tasks)
6. ✅ Fix gender update bug

**Phase 1 implementation notes (June 2026):**
- Added `payments` table and `Payment` model
- Added `request_id` (unique) on `tasks` to link hires to messages
- `payment_flag` is now set only after Stripe confirms payment (success redirect + webhook)
- Stripe webhook route: `POST /stripe/webhook` (CSRF exempt)
- Controllers now pass data to views instead of inline DB queries in Blade
- New env vars: `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`

### Phase 2 — Proposals (2–3 weeks) ✅ COMPLETED

7. ✅ Create `proposals` model + migration
8. ✅ Build client proposal review UI
9. ✅ Add job status (`open` / `filled` / `closed`)
10. ✅ Hire only from accepted proposal

**Phase 2 implementation notes (June 2026):**
- Added `proposals` table: `gig_id`, `user_id`, `cover_letter`, `bid_amount`, `delivery_days`, `status`
- Proposal statuses: `pending`, `shortlisted`, `accepted`, `rejected`, `withdrawn`
- Added `status` on `gigs` (`open`, `filled`, `closed`); homepage shows open listings only
- Added `proposal_id` (unique) on `tasks`
- Workers submit proposals on **jobs**; clients submit proposals on **gigs**
- Routes: `/user/proposals/{id}` (inbox), `/user/my-proposals/{id}`, `/gigs/{gig}/proposals`, `POST /proposals/{proposal}/hire`
- Hiring via message Accept removed; hire only through proposal review
- On hire: task created, gig marked `filled`, other pending proposals auto-rejected

### Phase 3 — Contract & Delivery (3–4 weeks) ✅ COMPLETED

11. ✅ Add contract terms and status workflow
12. ✅ Worker deliverable submission
13. ✅ Client approval before payment release

**Phase 3 implementation notes (June 2026):**
- Extended `tasks` with contract fields: `scope_of_work`, `terms`, `start_date`, `end_date`, `worker_accepted_at`, `client_accepted_at`
- Contract statuses: `draft`, `active`, `in_review`, `completed`, `cancelled`, `disputed`
- Added `deliverables` table: notes, file upload, link, status (`submitted`, `revision_requested`, `approved`)
- Workflow: hire → `draft` (client auto-accepts) → worker accepts → `active` → worker submits → `in_review` → client approves → `completed` → payment unlocked
- Client can request revision (returns contract to `active`)
- Payment (Stripe) only allowed when status is `completed`
- Contract detail page: `GET /tasks/{task}` with accept, cancel, deliverable, and payment actions
- Legacy `Pending`/`Done` statuses migrated automatically

### Phase 4 — Trust & Growth (ongoing) ✅ COMPLETED

14. ✅ Reviews & ratings
15. ✅ Milestones + partial payments
16. ✅ Email/in-app notifications
17. ✅ Stripe Connect payouts
18. ✅ Admin panel (users, disputes)

**Phase 4 implementation notes (June 2026):**
- `reviews` table: rating 1–5, comment, client↔worker types; shown on profiles
- `milestones` table: client splits contract into partial payments; pay per approved milestone
- Laravel database + email notifications via `AppNotification`; inbox at `/user/alerts/{id}`
- `payouts` table + Stripe Connect onboarding at `/connect/onboard`; auto-transfer when connected
- `disputes` table; either party can open; admin resolves at `/admin/disputes`
- Admin dashboard at `/admin` (first user auto-promoted to admin)
- Navbar: Messages, Alerts, Admin (if admin)

---

## 14. Proposed New Database Tables (Future)

### `proposals`

```
id, gig_id, freelancer_id, cover_letter, bid_amount, delivery_days,
status (pending|shortlisted|accepted|rejected|withdrawn), timestamps
```

### `contracts` (or extend `tasks`)

```
id, proposal_id, gig_id, worker_id, client_id, scope_of_work, terms,
start_date, end_date, status (draft|active|in_review|completed|cancelled|disputed),
total_amount, timestamps
```

### `milestones`

```
id, contract_id, title, description, amount, due_date,
status (pending|in_progress|submitted|approved|paid), timestamps
```

### `deliverables`

```
id, contract_id, milestone_id (nullable), worker_id, file_path, notes,
status (submitted|approved|revision_requested), timestamps
```

### `payments`

```
id, contract_id, stripe_session_id, stripe_payment_intent_id, amount,
currency, status (pending|completed|failed|refunded), paid_at, timestamps
```

### `reviews`

```
id, contract_id, reviewer_id, reviewee_id, rating (1-5), comment,
type (client_to_worker|worker_to_client), timestamps
```

---

## 15. Agent Instructions

When working on DevLancer:

1. **Read this file first** before making changes
2. **Fix Phase 1 bugs** before adding new features unless explicitly told otherwise
3. **Follow existing conventions:** Blade views, Bootstrap 5, inline PHP queries in views (refactor gradually)
4. **User roles:** `acc_type = 1` (Worker), `acc_type = 2` (Client)
5. **Listing types:** `gig_type = 'gig'` (service), `gig_type = 'job'` (client need)
6. **Task statuses:** `Pending`, `Done` (with `payment_flag` determining Cancelled vs Done)
7. **Do not over-engineer** — this is an MVP; match the simplicity of existing code unless upgrading a whole module
8. **Minimize scope** — focused changes over large refactors unless requested
9. **No commits** unless explicitly asked by the user

---

## 16. Mental Model Summary

DevLancer is an **early-stage MVP** with a working demo loop:

```
Post listing → Send message → Accept/Hire → Pay (optional) → Client closes
```

It is **not** a full contract-management platform. Most production-grade pieces (proposals, contracts, milestones, reviews, secure payments, authorization) are absent or incomplete.

The highest-impact path to a real marketplace:

1. **Formal proposals** instead of raw messages
2. **Real contracts** with clear status
3. **Deliverables + approval** before completion
4. **Secure payments** (webhooks, escrow, worker payouts)
5. **Reviews** to build trust
