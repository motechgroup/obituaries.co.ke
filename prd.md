
# Obituaries.co.ke MVP Platform

You are a senior Laravel developer and UI/UX engineer.

Build a clean, minimalist, professional obituary publishing platform for:

https://obituaries.co.ke

The platform allows families and friends to submit obituary notices, pay via M-Pesa, and have verified obituaries published after admin approval.

This is an MVP.

Keep it simple.

Do not build a social network.
Do not build user accounts.
Do not add unnecessary complexity.

The core workflow is:

Submit obituary → Pay → Admin verifies → Publish.

---

# Technology Stack

Existing project:

Laravel
Tailwind CSS

Use:

Backend:
- Laravel
- PHP 8.3+

Frontend:
- Blade
- Tailwind CSS
- Alpine.js where required

Database:
- MySQL

Payment:
- Safaricom M-Pesa STK Push API

---

# Design Philosophy

The website should feel:

- Respectful
- Trustworthy
- Minimalist
- Elegant
- Professional
- Peaceful


Avoid:

- Dark funeral themes
- Complex layouts
- Corporate landing pages
- Excessive animations


Design inspiration:

- Apple simplicity
- Medium typography
- Modern editorial websites

---

# Visual Style

Colors:

Primary:
Deep navy blue

Background:
White / soft gray

Accent:
Subtle gold

Text:
Dark gray


Use:

- Large whitespace
- Clean typography
- Beautiful obituary cards
- Simple navigation
- Mobile-first responsive design

---

# Public Website

## Homepage

Create a simple elegant homepage.

Sections:

## Hero

Title:

"Remembering Lives. Sharing Memories."

Subtitle:

"Create and preserve a lasting tribute for your loved ones."

Buttons:

- Submit Obituary
- Search Obituaries


---

## Latest Obituaries

Display obituary cards.

Card contains:

- Photo
- Name
- Birth date
- Death date
- Location
- View obituary button


---

## Search

Allow visitors to search:

- Name
- County
- Year


---

## Footer

Include:

- About
- Contact
- Terms
- Privacy Policy

---

# Obituary Submission

No user registration required.

Anyone can submit an obituary.

Create a multi-step submission form.

---

## Step 1: Deceased Information

Fields:

- Full name
- Profile photo
- Date of birth
- Date of death
- County
- Town
- Biography


---

## Step 2: Funeral Information

Fields:

- Funeral date
- Burial location
- Church/service location
- Funeral programme upload (PDF)


---

## Step 3: Submitter Information

Required:

- Full name
- Phone number
- Email (optional)

Relationship:

Options:

- Child
- Spouse
- Parent
- Relative
- Friend
- Organization


Agreement checkbox:

"I confirm that I have permission from the family to submit this obituary."

---

# Payment Workflow

After completing obituary submission:

Create pending obituary record.

Redirect to payment.

Package:

Basic Obituary:
KES 500


Payment process:

1. User enters M-Pesa phone number.
2. System sends STK Push.
3. Receive M-Pesa callback.
4. Verify transaction.
5. Store payment details.
6. Change obituary status.


Payment does NOT automatically publish.

---

# Payment Database

Create payments table:

Fields:

- id
- obituary_id
- phone_number
- amount
- merchant_request_id
- checkout_request_id
- mpesa_receipt_number
- status
- timestamps


Statuses:

pending

completed

failed

---

# Admin Approval Workflow

After payment success:

Status:

pending_verification


Admin reviews from dashboard.

Admin can:

- View obituary details
- View payment details
- Call submitter
- Verify information
- Approve
- Reject


---

# Verification Process

Admin verifies:

- Submitter name
- Phone number
- Relationship
- Confirmation from family


Admin may request:

- National ID number
- Additional confirmation


Do not require ID upload by default.

Do not store unnecessary sensitive information.


Store only:

verification_status

verified_by

verified_at

---

# Obituary Status

Use:

draft

pending_payment

payment_confirmed

pending_verification

published

rejected


---

# Public Obituary Page

Each obituary gets a unique URL.

Example:

/obituary/john-kamau


Display:

- Profile photo
- Full name
- Birth date
- Death date
- Age
- Biography
- Funeral details
- Programme download
- Location


Actions:

- Share WhatsApp
- Share Facebook
- Copy link


---

# Search System

Visitors can search published obituaries.

Search by:

- Name
- County
- Year


Only approved/published obituaries appear.

---

# Admin Dashboard

Create a simple admin panel.

Dashboard:

Show:

- Total obituaries
- Pending verification
- Published obituaries
- Payments received


Modules:

## Obituaries

Admin can:

- View
- Edit
- Approve
- Reject
- Delete


## Payments

View:

- Transactions
- Amounts
- Status


## Verification

View:

- Submitter details
- Verification status
- Notes


---

# Database Tables

Create:

## obituaries

Fields:

- id
- full_name
- slug
- photo
- date_of_birth
- date_of_death
- county
- town
- biography
- funeral_details
- programme_file
- submitter_name
- submitter_phone
- relationship
- status
- verification_status
- verified_at
- timestamps


## payments

Fields:

- id
- obituary_id
- phone_number
- amount
- mpesa_receipt_number
- checkout_request_id
- status
- timestamps


## admins

Fields:

- id
- name
- email
- password


---

# SEO Requirements

Every obituary page must have:

Dynamic title:

{Name} Obituary | Obituaries.co.ke


Meta description:

"Read the obituary, life story, funeral details and memories of {name}."


Implement:

- Sitemap
- SEO-friendly URLs
- Open Graph sharing tags

---

# Security

Implement:

- Laravel validation
- CSRF protection
- File upload validation
- Image size restrictions
- Rate limiting
- Secure M-Pesa callback handling
- Admin authentication


---

# Development Rules

Before coding:

1. Analyze the requirements.
2. Explain database design.
3. Explain architecture.
4. Provide implementation plan.

Then build module by module.

After each module explain:

- Files created
- Database changes
- Testing steps


---

# Final Goal

Create a simple, trustworthy, beautiful obituary publishing platform.

The experience should communicate:

"Every life deserves to be remembered."
```


