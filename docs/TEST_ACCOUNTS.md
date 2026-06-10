# DevLancer — Launch Seed Accounts

> Run `php artisan migrate:fresh --seed` to reset the database with launch-ready demo data.

**All seeded accounts use the same password:** `password123`  
(Override with `SEED_PASSWORD` in `.env` before seeding.)

## Accounts

| Role | Email | Name | What to test |
|------|-------|------|--------------|
| **Admin** | `admin@devlancer.com` | Admin User | `/admin` dashboard, settings, support, disputes, review moderation |
| **Client** | `client@devlancer.com` | Mike Johnson | Post jobs, review proposals, hire, pay, leave reviews |
| **Client** | `client2@devlancer.com` | Lisa Park | Active contract with Sarah (milestones + deliverable) |
| **Worker** | `worker@devlancer.com` | Sarah Chen | Full freelancer profile, Laravel gig with images, proposals |
| **Worker** | `worker2@devlancer.com` | Ahmed Hassan | Design gig, 5★ approved reviews on profile |
| **Worker** | `worker3@devlancer.com` | James Okonkwo | DevOps gig, pending blind review flow |

## Seeded data includes

- **Site settings** — launch hero title/subtitle and tagline
- **6 open listings** (3 jobs, 3 gigs) with **images** and **file attachments** in `public/uploads/`
- **1 filled job** with accepted/rejected proposals
- **Worker profiles** — headline, skills, experience, education, portfolio/GitHub links, hourly rate
- **Contracts** in multiple states: draft, active (milestones + deliverable), disputed, completed, paid
- **Reviews** — approved public reviews, pending moderation, blind review (client submitted, worker pending)
- **Chat history**, **support ticket**, and **open dispute** for admin demos

## Quick test flows

### Browse homepage
1. Visit `/` — see populated jobs/gigs with images
2. Switch to **Talent** search — see worker profiles with skills & headlines

### Client hires a freelancer
1. Login as `client@devlancer.com`
2. Open **Proposals** → review bids on "Build E-commerce Website"
3. Shortlist / Hire → worker accepts contract

### Milestones & deliverables
1. Login as `client2@devlancer.com` or `worker@devlancer.com`
2. Open the WordPress migration contract → view milestones and submitted deliverable

### Admin moderation
1. Login as `admin@devlancer.com`
2. Visit `/admin/reviews` — pending reviews to approve/deny
3. Visit `/admin/disputes` — open e-commerce dispute

### Change seed password (recommended for production)
```env
SEED_PASSWORD=your-strong-password-here
```
Then run: `php artisan migrate:fresh --seed`
