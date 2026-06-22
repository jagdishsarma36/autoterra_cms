# AutoTerra CMS — Editor Guide

How to create, edit, and manage website content through the admin panel.

**Admin Panel:** `/admin`

---

## Site Settings

Navigate to **CMS → Site Settings**.

### General
- **site_name** — Site name shown in header/footer (default: AutoTerra)
- **site_email** — Contact email
- **company_address** — Company address

### Header
- **header_logo_a** — First part of text logo (e.g. "AUTO")
- **header_logo_t** — Second part of text logo (e.g. "TERRA", shown in cyan)
- **header_logo_image** — Image logo URL (overrides text if set)
- **header_nav_links** — Navigation links as JSON array: `[{"label":"Products","url":"/products"}, ...]`
- **header_login_text** — Login button text
- **header_cta_text** — Call-to-action button text (e.g. "Buy Now")
- **header_cta_url** — Call-to-action button URL

### Footer
- **footer_logo_a / footer_logo_t / footer_logo_image** — Footer logo
- **footer_description** — Footer description text
- **footer_links** — JSON array: `[{"label":"About","url":"/about"}, ...]`
- **footer_copyright** — Copyright text

### Custom Code
- **custom_header_css** — CSS injected in `<head>`
- **custom_header_js** — JS injected in `<head>` (GTM, consent)
- **custom_footer_js** — JS before `</body>` (analytics, chat)

### Media Upload
- **media_allowed_types** — Comma-separated MIME types
- **media_max_size** — Max upload size in MB

---

## Content Blocks (Page Content)

The site uses a **key-value content block system**. Each page is built from blocks stored as `{page, key, value, type}`.

### How It Works

1. Every page on the site pulls content from content blocks
2. Each block has a **page** (which page it belongs to), a **key** (identifier), a **value** (content), and a **type** (how it renders)
3. Blocks are cached for 1 hour — cache clears automatically when you save

### Editing Content Blocks

Go to **CMS → Page Content (CMS)**.

1. Click **Create** (or click a record to edit)
2. Select the **Page** from the dropdown
3. Enter the **Key** (e.g. `hero.heading`)
4. Select the **Type** (see types below)
5. Enter the **Value** (content)
6. Save

### Content Block Types

| Type | Use For | When to Use |
|------|---------|-------------|
| **text** | Plain text | Headings, short paragraphs, labels |
| **html** | Raw HTML (no wrapper) | Custom HTML snippets, embeds |
| **html_inline** | Raw HTML (wrapped in styled div) | Rich content with consistent spacing |
| **richtext** | Rich text editor | Long-form content with formatting |
| **json** | Structured data | Lists of items, FAQ, testimonials, features |

### Available Pages

| Page Key | Controls |
|----------|----------|
| `global` | Footer columns (resources, company, legal links) |
| `home` | Homepage sections |
| `about` | About page |
| `products` | Products page |
| `pricing` | Pricing page |
| `buy` | Buy page |
| `contact` | Contact page |
| `quote` | Quote request page |
| `pro` | AutoTerra Pro page |
| `pro_spatial` | AutoTerra Pro Spatial page |
| `solutions` | Solutions page |
| `resources` | Resources page |
| `eula` | EULA page |
| `cookies` | Cookie policy page |

### Common Block Keys by Page

#### Homepage (`home`)

| Key | Content | Type |
|-----|---------|------|
| `hero.pill_text` | Small badge text above heading | text |
| `hero.heading` | Main hero heading | text |
| `hero.description` | Hero description paragraph | text |
| `hero.button_primary_text` | Primary button text | text |
| `hero.button_primary_url` | Primary button URL | text |
| `hero.button_secondary_text` | Secondary button text | text |
| `hero.button_secondary_url` | Secondary button URL | text |
| `stats` | Stats cards | json |
| `features` | Feature cards grid | json |
| `faq` | FAQ accordion | json |
| `testimonials` | Testimonial cards | json |
| `cta.heading` | CTA section heading | text |
| `cta.description` | CTA section description | text |
| `cta.button_primary_text` | CTA button text | text |
| `cta.button_primary_url` | CTA button URL | text |

#### Any Page — Standard Sections

| Key | Content | Type |
|-----|---------|------|
| `hero.pill_text` | Badge text | text |
| `hero.heading` | Section heading | text |
| `hero.description` | Section description | text |
| `hero.button_primary_text` | Button text | text |
| `hero.button_primary_url` | Button URL | text |
| `section.eyebrow` | Small label above heading | text |
| `section.heading` | Section heading | text |
| `section.description` | Section description | text |
| `form` | Embed a form (value = form slug) | text |

### JSON Block Examples

**Stats block:**
```json
[
  {"number": "500+", "label": "Users Worldwide"},
  {"number": "50M", "label": "Acres Mapped"},
  {"number": "99.9%", "label": "Uptime"}
]
```

**FAQ block:**
```json
[
  {"question": "How do I install?", "answer": "Download from the resources page."},
  {"question": "Is there a free trial?", "answer": "Yes, 14-day free trial available."}
]
```

**Features block:**
```json
[
  {"title": "Feature One", "description": "Description of feature one."},
  {"title": "Feature Two", "description": "Description of feature two."}
]
```

**Testimonials block:**
```json
[
  {"quote": "Great software!", "author_name": "John Smith", "author_org": "Acme Corp"}
]
```

### Adding a New Key

1. Go to **CMS → Page Content (CMS) → Create**
2. Select the page (e.g. `home`)
3. Type the key (e.g. `hero.heading`)
4. Select the type
5. Enter the value
6. Save

The key you create must match what the page template expects. Use the tables above as reference.

---

## Creating CMS Pages

Go to **CMS → Pages** to create standalone pages.

### Steps

1. Click **Create**
2. Enter the **Title** — the slug auto-generates (e.g. "Getting Started" → `getting-started`)
3. Add **Content Blocks** via the repeater:
   - Enter a key (e.g. `hero.heading`)
   - Select type (text, html, json, etc.)
   - Enter the value
4. Fill in **SEO fields**: meta title, meta description
5. Set **Featured Image** URL if needed
6. Toggle **is Published** on
7. Set **Published At** date (for scheduled publishing)
8. Save

The page is now live at `/{slug}` (e.g. `/getting-started`).

### Nested URLs

Use slashes in the slug for nested paths: `docs/getting-started` → URL: `/docs/getting-started`

---

## Media Library

Go to **CMS → Media Library**.

### Uploading

1. Click **Create**
2. Drag and drop or click the upload area
3. Add details:
   - **Name** — display name (auto-filled from filename)
   - **Alt Text** — descriptive text for screen readers and SEO
   - **Title** — shown on hover
   - **Folder** — organize with paths like `/blog`, `/products`
4. Save

### Copying URLs

Click the **Copy URL** button on any media item to copy its public URL. Paste this URL into content blocks or page settings.

### File Limits

Configured in **Site Settings → Media Upload**:
- **Allowed Types** — images, videos, PDFs, Word docs, Excel, CSV, archives
- **Max Size** — default 50MB

---

## Form Builder

Go to **CMS → Forms**.

### Creating a Form

1. Click **Create**
2. Enter **Name** (slug auto-generates)
3. Set **Submit Button Text** (default: "Submit")
4. Set **Success Message** (default: "Thank you! Your submission has been received.")
5. Set **Notification Email** to receive submission alerts
6. Add **Fields** via the repeater:
   - Enter **Label** (display text)
   - Enter **Name** (machine name, e.g. `full_name`)
   - Select **Type** (see below)
   - Toggle **Required** if needed
   - Set **Width** to Full or Half
   - Add **Placeholder** and **Help Text** if needed
7. Save

### Embedding a Form on a Page

**Option 1 — Content block:**
1. Go to **CMS → Page Content (CMS) → Create**
2. Set Page to the target page (e.g. `contact`)
3. Set Key to `form`
4. Set Type to `text`
5. Set Value to the form slug (e.g. `contact-us`)
6. Save

**Option 2 — Direct URL:**
The form is accessible at `/form/{slug}` (e.g. `/form/contact-us`)

### Field Types

| Type | Description |
|------|-------------|
| text | Text input |
| email | Email input (auto-validates format) |
| number | Number input |
| phone | Phone input (tel field) |
| textarea | Multi-line text area |
| select | Dropdown (add options one per line in Options field) |
| radio | Radio buttons (add options one per line) |
| checkbox | Checkboxes (add options one per line, multi-select) |
| date | Date picker |
| time | Time picker |
| file | File upload |
| hidden | Hidden field (value comes from the Placeholder field) |

### Viewing Submissions

Go to **CMS → Submissions**.

- Filter by form or read status
- Click a submission to view full details
- Submissions include: name, email, all field data, IP address, timestamp

---

## Blog

Go to **CMS → Blog Posts**.

### Creating a Post

1. Click **Create**
2. Enter **Title** (slug auto-generates)
3. Write **Content** in the HTML editor
4. Set **Category** (e.g. "News", "Tutorial", "Release")
5. Add **Tags** (press Enter after each tag)
6. Add **Featured Image** URL
7. Set **Author Name**
8. Toggle **is Published** on
9. Set **Published At** for scheduling
10. Add **SEO fields**: meta title, meta description
11. Save

**URL:** `/blog/{slug}`

---

## Quick Reference — Content Structure

```
Home Page
  ├── hero.pill_text          → "Introducing AutoTerra"
  ├── hero.heading             → "Professional Geospatial Surveying"
  ├── hero.description         → "Survey, design, and analyze..."
  ├── hero.button_primary_text → "Start Free Trial"
  ├── hero.button_primary_url  → "/buy"
  ├── stats                    → [{"number":"500+","label":"Users"},...]
  ├── features                 → [{"title":"Feature 1","description":"..."},...]
  ├── faq                      → [{"question":"...","answer":"..."},...]
  ├── testimonials             → [{"quote":"...","author_name":"..."},...]
  ├── cta.heading              → "Ready to Get Started?"
  ├── cta.button_primary_text  → "Buy Now"
  └── cta.button_primary_url   → "/buy"

About Page
  ├── hero.heading    → "About AutoTerra"
  ├── hero.description → "Founded in..."
  ├── story.heading   → "Our Story"
  ├── story.content   → (richtext or html)
  ├── values          → json array
  └── team            → json array
```

---

## Tips

- **Cache clears on save** — changes appear immediately after saving
- **Preview before publishing** — keep `is Published` off until ready
- **Use consistent keys** — follow the naming convention: `section.element` (e.g. `hero.heading`, `cta.button_primary_text`)
- **JSON formatting** — validate JSON before saving (use https://jsonlint.com if unsure)
- **Images** — upload to Media Library first, then copy the URL into your content block
- **Back up important content** — copy JSON values to a text file before editing
