## AN-1289: Fix Applicants Modal Bug and Database Seeding

**Objective:** Fix the bug in the applicants modal, implement the reject action, hide rejected projects from freelancer profiles, and correctly seed the database with test users.

**Details:**

- **Frontend (`resources/js/Pages/Company/Projects/Index.vue`):**
    - Added `CheckCircleIcon` and `XCircleIcon` from `@heroicons/vue/24/solid` to the 'Approve' and 'Reject' buttons in the applicants modal.
    - Implemented a `rejectApplication` method that sends an Inertia POST request to a new route to handle the rejection of a project application.
    - Fixed a typo: `defineDefineProps` was corrected to `defineProps`.
- **Backend (`routes/web.php`):**
    - Defined a new POST route `/company/projects/applications/{application}/reject` that maps to the `rejectApplication` method in the `ProjectController`.
- **Backend (`app/Http/Controllers/ProjectController.php`):**
    - Created the `rejectApplication` method to update the `status` of a `ProjectApplication` to 'rejected'.
- **Backend (`app/Http/Controllers/FreelancerController.php`):
    - Modified the `profile` method to filter out projects with a 'rejected' status from the freelancer's profile.
- **Database Seeding:**
    - Created `database/seeders/UserSeeder.php` to add three test users (`a@a.com` as admin, `b@b.com` as freelancer, `c@c.com` as company) with the password "iulian".
    - Modified `database/seeders/DatabaseSeeder.php` to call `UserSeeder` and removed the default `User::factory()->create()` call to prevent duplicate entries.
    - Executed `php artisan migrate:fresh --seed` to ensure a clean database and successful seeding of the new users.

**Outcome:** The applicants modal now has icons for actions, the reject functionality is implemented, rejected projects are hidden from freelancer profiles, and the database can be correctly seeded with the specified test users.