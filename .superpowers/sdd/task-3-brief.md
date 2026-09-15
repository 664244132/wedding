### Task 3: Refactor `services.php` Presentation Zone & UI/UX (Luxury Wedding Standards)

**Files:**
- Modify: `services.php:26-166`
- Modify: `CSS/style_services.css`

**Interfaces:**
- Consumes: `$services` array containing 5 items for `ชุดไทย` and 5 items for `ชุดไทยสากล`
- Produces: Semantic HTML5 view with luxury wedding cards, responsive modals, radio selection, and JS redirect to `booking.php`

- [ ] **Step 1: Enhance CSS in `CSS/style_services.css` for Luxury Wedding styling**

Add luxury wedding variables, card styles, and selection badge styles:
- Luxury color variables matching `CSSCodingGuide.md`: `--gold-primary: #c5a059`, `--dark-wedding: #4a3b2b`, `--soft-cream: #fdfaf5`
- Card hover elevation (`transform: translateY(-8px)`)
- Selected dress card highlight: border 2px solid gold, gold shadow, selection checkmark badge
- Luxury modal headers and buttons

- [ ] **Step 2: Refactor HTML & Modals in `services.php`**

Ensure:
- Universal output escaping using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- Modal dialog attributes: `aria-labelledby`, `aria-hidden="true"`, `tabindex="-1"`
- Responsive images with `img-fluid`, `alt` attribute, and `loading="lazy"`
- Radio buttons for selecting dresses with clear thumbnail, caption, and price badge
- Clean Vanilla JavaScript (ES6+) passing `selected_item`, `service_type`, and `price` to `booking.php`
- Fallback image handlers (`onerror`)

- [ ] **Step 3: Validate PHP syntax and output**

Run: `C:\xampp\php\php.exe -l services.php`
Expected: `No syntax errors detected in services.php`

- [ ] **Step 4: Commit**

```bash
git add services.php CSS/style_services.css
git commit -m "feat(services): enhance luxury wedding UI, accessible modals, and dress selection"
```
