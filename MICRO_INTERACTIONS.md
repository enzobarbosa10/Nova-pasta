# MICRO-INTERACTIONS & ANIMATIONS
## Premium Experience Details

---

## DESIGN PHILOSOPHY

**Purpose**: Make every interaction feel intentional, responsive, and delightful

**Principles**:
- **Feedback**: Every action receives immediate visual response
- **Purposeful**: Animations guide attention, don't distract
- **Fast**: Quick enough to feel instant (< 300ms)
- **Smooth**: Natural easing, no janky transitions
- **Consistent**: Same patterns throughout app
- **Accessible**: Can be reduced/disabled for accessibility

---

## 1. BUTTON INTERACTIONS

### 1.1 Primary Button

```
DEFAULT STATE:
├─ Background: Primary color
├─ Text: White
├─ Border-radius: 8px
├─ Padding: 12px 24px
├─ Box-shadow: 0 1px 2px rgba(0,0,0,0.05)
└─ Cursor: pointer

HOVER:
├─ Transform: translateY(-1px)
├─ Box-shadow: 0 4px 6px rgba(0,0,0,0.1)
├─ Background: Primary color (slightly lighter)
├─ Transition: all 0.15s ease-out
└─ Cursor: pointer

ACTIVE (Click):
├─ Transform: scale(0.98)
├─ Box-shadow: 0 1px 2px rgba(0,0,0,0.05)
├─ Transition: all 0.1s ease-in
└─ Visual feedback: Pressed state

LOADING:
├─ Cursor: not-allowed
├─ Opacity: 0.7
├─ Content: Replace text with spinner
│   └─ Spinner animation: Rotating 360° (1s infinite)
└─ Pointer-events: none

SUCCESS:
├─ Background: Green
├─ Icon: ✓ Checkmark (scale in animation)
├─ Duration: 0.3s
├─ Then: Restore to default or redirect
└─ Haptic feedback (mobile)

ERROR:
├─ Background: Red
├─ Icon: ✗ X mark
├─ Shake animation: translateX(-10px → 10px → 0)
├─ Duration: 0.4s
└─ Then: Restore to default
```

**Example Implementation**:
```css
.btn-primary {
  transition: all 150ms ease-out;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.btn-primary:active {
  transform: scale(0.98);
  transition: all 100ms ease-in;
}

.btn-primary.loading {
  cursor: not-allowed;
  pointer-events: none;
  opacity: 0.7;
}

@keyframes btn-success {
  0% { background-color: var(--primary); }
  50% { background-color: var(--success); transform: scale(1.05); }
  100% { background-color: var(--success); transform: scale(1); }
}
```

---

### 1.2 Icon Button

```
DEFAULT:
├─ Icon only (no text)
├─ Size: 40x40px (touch-friendly)
├─ Icon: 20px
├─ Border-radius: 50% (circular)
└─ Background: Transparent

HOVER:
├─ Background: rgba(0,0,0,0.05)
├─ Scale: 1.05
├─ Transition: 0.15s ease-out
└─ Tooltip appears (0.5s delay)

ACTIVE:
├─ Scale: 0.95
├─ Background: rgba(0,0,0,0.1)
└─ Ripple effect from click point

FOCUS (Keyboard):
├─ Outline: 2px solid primary color
├─ Outline-offset: 2px
└─ Accessible focus indicator
```

---

### 1.3 Ghost Button

```
DEFAULT:
├─ Background: Transparent
├─ Border: 1px solid primary
├─ Color: Primary
└─ Padding: 12px 24px

HOVER:
├─ Background: Primary color (10% opacity)
├─ Border-color: Primary (darker)
└─ Transition: 0.2s ease-out

ACTIVE:
├─ Background: Primary (20% opacity)
└─ Scale: 0.98
```

---

## 2. INPUT FIELD INTERACTIONS

### 2.1 Text Input

```
DEFAULT:
├─ Border: 1px solid #E5E7EB
├─ Background: White
├─ Padding: 12px 16px
├─ Border-radius: 8px
├─ Placeholder: Gray (#9CA3AF)
└─ Transition: all 0.2s

FOCUS:
├─ Border: 2px solid Primary
├─ Box-shadow: 0 0 0 3px rgba(primary, 0.1)
├─ Outline: none
├─ Transition: all 0.2s ease-out
└─ Label animates (if floating label)

FILLED:
├─ Border: 1px solid #D1D5DB
├─ Background: White
└─ Label stays in top position

ERROR:
├─ Border: 2px solid Red
├─ Shake animation (subtle)
│   └─ translateX(-5px → 5px → 0) in 0.3s
├─ Error message slides down
│   └─ opacity: 0 → 1, translateY(-5px → 0)
└─ Icon: ⚠ appears on right

SUCCESS:
├─ Border: 1px solid Green
├─ Icon: ✓ appears on right
│   └─ Scale in: scale(0 → 1) in 0.2s
└─ Brief highlight (flash green background)

DISABLED:
├─ Background: #F3F4F6
├─ Cursor: not-allowed
├─ Opacity: 0.6
└─ Border: 1px dashed #D1D5DB
```

**Floating Label Animation**:
```
EMPTY + NOT FOCUSED:
├─ Label position: Inside input (placeholder)
├─ Font-size: 16px
└─ Color: Gray

FOCUSED or FILLED:
├─ Label position: Above input
├─ Transform: translateY(-24px)
├─ Font-size: 12px
├─ Color: Primary (if focused) or Gray (if filled)
├─ Transition: 0.2s ease-out
└─ Background: White (to cover border)
```

---

### 2.2 Select Dropdown

```
CLOSED:
├─ Border: 1px solid #E5E7EB
├─ Chevron icon: ▼
└─ Cursor: pointer

HOVER:
├─ Border: 1px solid #D1D5DB
├─ Background: #FAFAFA
└─ Transition: 0.15s

CLICK / OPEN:
├─ Border: 2px solid Primary
├─ Box-shadow: 0 0 0 3px rgba(primary, 0.1)
├─ Chevron rotates: rotate(0 → 180deg)
├─ Dropdown menu appears:
│   └─ Opacity: 0 → 1
│   └─ TranslateY: -10px → 0
│   └─ Duration: 0.2s ease-out
└─ Z-index: 1000

DROPDOWN MENU:
├─ Background: White
├─ Box-shadow: 0 10px 20px rgba(0,0,0,0.1)
├─ Border-radius: 8px
├─ Max-height: 300px
├─ Overflow: auto (custom scrollbar)
└─ Slide + fade in animation

OPTION (in dropdown):
├─ Padding: 12px 16px
├─ Cursor: pointer
└─ Transition: background 0.1s

OPTION HOVER:
├─ Background: Primary (5% opacity)
└─ Smooth transition

OPTION SELECTED:
├─ Background: Primary (10% opacity)
├─ Checkmark icon: ✓
├─ Font-weight: 600
└─ Color: Primary
```

---

### 2.3 Checkbox

```
UNCHECKED:
├─ Border: 2px solid #D1D5DB
├─ Background: White
├─ Size: 20x20px
├─ Border-radius: 4px
└─ Cursor: pointer

HOVER (unchecked):
├─ Border: 2px solid Primary
├─ Background: Primary (5% opacity)
└─ Transition: 0.15s

CHECKED:
├─ Background: Primary
├─ Border: 2px solid Primary
├─ Checkmark icon: ✓
│   └─ Stroke animation (draw effect)
│   └─ Duration: 0.3s ease-out
└─ Scale pulse: 1 → 1.1 → 1 (0.2s)

INDETERMINATE (for "select all"):
├─ Background: Primary
├─ Icon: — (horizontal line)
└─ Same animation as checked

DISABLED:
├─ Opacity: 0.5
├─ Cursor: not-allowed
└─ Grayscale filter
```

**Checkmark Animation**:
```css
@keyframes check-draw {
  from {
    stroke-dashoffset: 20;
  }
  to {
    stroke-dashoffset: 0;
  }
}

.checkbox-check {
  stroke-dasharray: 20;
  animation: check-draw 0.3s ease-out forwards;
}
```

---

### 2.4 Toggle Switch

```
OFF STATE:
├─ Background: #E5E7EB (gray)
├─ Handle: White circle (left position)
├─ Size: 44px width × 24px height
├─ Handle size: 20px diameter
└─ Border-radius: 12px (pill shape)

HOVER (off):
├─ Background: #D1D5DB (darker gray)
└─ Cursor: pointer

CLICK → ON:
├─ Background: Primary color
├─ Handle slides: translateX(0 → 20px)
├─ Transition: 0.2s ease-out
└─ Haptic feedback (mobile)

ON STATE:
├─ Background: Primary
├─ Handle: White circle (right position)
└─ Optional icon in handle or background

HOVER (on):
├─ Background: Primary (slightly lighter)
└─ Handle scale: 1.05

DISABLED:
├─ Opacity: 0.5
├─ Cursor: not-allowed
└─ No hover effects
```

---

## 3. CARD INTERACTIONS

### 3.1 Standard Card

```
DEFAULT:
├─ Background: White
├─ Border: 1px solid #E5E7EB
├─ Border-radius: 12px
├─ Padding: 24px
├─ Box-shadow: 0 1px 2px rgba(0,0,0,0.05)
└─ Transition: all 0.2s ease-out

HOVER (if clickable):
├─ Transform: translateY(-2px)
├─ Box-shadow: 0 8px 16px rgba(0,0,0,0.1)
├─ Border-color: Primary (subtle)
├─ Cursor: pointer
└─ Transition: all 0.2s ease-out

ACTIVE (click):
├─ Transform: translateY(0)
├─ Box-shadow: 0 4px 8px rgba(0,0,0,0.08)
└─ Transition: all 0.1s ease-in

LOADING:
├─ Skeleton animation
│   └─ Shimmer effect (gradient slide)
│   └─ Background: Linear gradient animation
└─ Pulse subtle opacity change
```

**Shimmer Effect**:
```css
@keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.card-skeleton {
  background: linear-gradient(
    90deg,
    #f0f0f0 0%,
    #f8f8f8 50%,
    #f0f0f0 100%
  );
  background-size: 1000px 100%;
  animation: shimmer 2s infinite;
}
```

---

### 3.2 Expedition Card (Special)

```
DEFAULT:
├─ Image at top (cover)
├─ Content section
├─ Gradient overlay on image
└─ Box-shadow: 0 2px 4px rgba(0,0,0,0.1)

HOVER:
├─ Image: Scale(1 → 1.05)
│   └─ Transform: scale(1.05)
│   └─ Transition: 0.3s ease-out
│   └─ Overflow: hidden (on card)
├─ Card: Elevate
│   └─ translateY(-4px)
│   └─ Box-shadow: 0 12px 24px rgba(0,0,0,0.15)
├─ CTA Button: Slide in from bottom
│   └─ translateY(20px → 0)
│   └─ opacity(0 → 1)
└─ Overlay: Darker gradient

IMAGE LOADING:
├─ Show placeholder color (gradient)
├─ BlurHash or low-res preview
├─ Fade in when loaded
│   └─ opacity: 0 → 1 (0.3s)
└─ Progressive image loading
```

---

## 4. MODAL & DIALOG INTERACTIONS

### 4.1 Modal Opening

```
TRIGGER: Click to open modal
↓
SEQUENCE:

[0ms] Backdrop appears
├─ Opacity: 0 → 0.5
├─ Duration: 0.2s
└─ Background: rgba(0,0,0,0.5)

[50ms] Modal slides in
├─ Transform: scale(0.9) translateY(20px) → scale(1) translateY(0)
├─ Opacity: 0 → 1
├─ Duration: 0.3s
├─ Easing: ease-out (spring-like)
└─ Z-index: 9999

[300ms] Content fades in
├─ Opacity: 0 → 1
├─ Duration: 0.2s
└─ Slight stagger on elements (cascade effect)

[Complete]
├─ Focus trap enabled
├─ Scroll lock on body
├─ ESC key listener active
└─ Backdrop click to close (optional)
```

---

### 4.2 Modal Closing

```
TRIGGER: Click close, ESC, or backdrop
↓
SEQUENCE:

[0ms] Content fades out
├─ Opacity: 1 → 0
└─ Duration: 0.15s

[100ms] Modal slides out
├─ Transform: scale(1) translateY(0) → scale(0.95) translateY(10px)
├─ Opacity: 1 → 0
├─ Duration: 0.2s
└─ Easing: ease-in

[150ms] Backdrop fades
├─ Opacity: 0.5 → 0
└─ Duration: 0.2s

[350ms] Complete
├─ Remove from DOM
├─ Restore scroll
├─ Return focus to trigger element
└─ Clear event listeners
```

---

### 4.3 Bottom Sheet (Mobile)

```
OPENING:
├─ Slides from bottom
├─ Transform: translateY(100%) → translateY(0)
├─ Backdrop: opacity 0 → 0.5
├─ Duration: 0.3s
├─ Easing: ease-out
└─ Can drag to open further

OPENED STATE:
├─ Handle at top (drag indicator)
├─ Swipe down to close
├─ Snap points: [90%, 50%, closed]
└─ Gesture recognition

CLOSING:
├─ Swipe down gesture or tap backdrop
├─ Slides down: translateY(0) → translateY(100%)
├─ Duration: 0.25s
├─ Easing: ease-in
└─ Spring physics if swiped with velocity
```

---

## 5. TOAST NOTIFICATIONS

### 5.1 Toast Appearance

```
TYPES:
├─ Success (Green)
├─ Error (Red)
├─ Warning (Yellow)
├─ Info (Blue)
└─ Neutral (Gray)

ENTRANCE:
├─ Position: Top-right (desktop) or Top-center (mobile)
├─ Animation: Slide + Fade
│   └─ translateX(100%) → translateX(0)  [desktop]
│   └─ translateY(-100%) → translateY(0) [mobile]
├─ Opacity: 0 → 1
├─ Duration: 0.3s
└─ Easing: ease-out

CONTENT:
├─ Icon (type-specific)
├─ Title
├─ Message (optional)
├─ Action button (optional)
├─ Close button
└─ Auto-dismiss timer (visible progress bar)

AUTO-DISMISS:
├─ Default: 5 seconds
├─ Progress bar: width 100% → 0%
├─ Linear animation
├─ Pause on hover
└─ Resume on mouse leave

EXIT:
├─ Animation: Slide + Fade out
│   └─ translateX(0) → translateX(100%)  [desktop]
│   └─ opacity: 1 → 0
├─ Duration: 0.2s
├─ Easing: ease-in
└─ Remove from DOM after animation

STACKING:
├─ Multiple toasts stack vertically
├─ Max visible: 3
├─ Older toasts queue
├─ Stagger: 0.1s delay between each
└─ Smooth height transitions when dismissed
```

---

## 6. LOADING STATES

### 6.1 Skeleton Loading

```
SKELETON PATTERN:
├─ Replicate layout structure
├─ Use gray rectangles for text
├─ Use circles for avatars
├─ Use rectangles for images
└─ Animate with shimmer

SHIMMER ANIMATION:
├─ Gradient: #F0F0F0 → #F8F8F8 → #F0F0F0
├─ Movement: Left to right
├─ Duration: 2s
├─ Infinite loop
└─ Timing: ease-in-out

TRANSITION TO REAL CONTENT:
├─ Content loads
├─ Cross-fade: Skeleton opacity 1 → 0, Content opacity 0 → 1
├─ Duration: 0.3s
└─ Smooth swap (no layout shift)
```

---

### 6.2 Spinner

```
CIRCULAR SPINNER:
├─ SVG circle with stroke-dasharray
├─ Rotate: 360deg infinite
├─ Duration: 1s
├─ Easing: linear
├─ Color: Primary
└─ Size variants: sm (16px), md (24px), lg (40px)

USAGE CONTEXTS:
├─ Button loading: Replace button text
├─ Page loading: Center of screen
├─ Inline loading: Next to content
└─ Card loading: Center of card

ACCESSIBILITY:
├─ aria-label="Loading"
├─ role="status"
└─ Hidden text: "Please wait..."
```

---

### 6.3 Progress Bar

```
DETERMINATE (Known progress):
├─ Bar fills from 0% to 100%
├─ Smooth transition: 0.3s ease-out
├─ Show percentage text
├─ Color: Primary gradient
└─ Height: 8px (slim) or 16px (prominent)

INDETERMINATE (Unknown progress):
├─ Bar slides infinitely
├─ Animation: Shimmer effect
├─ Width: 30% of container
├─ Movement: left to right, loop
└─ Duration: 1.5s

COLORS:
├─ 0-30%: Red (danger)
├─ 31-70%: Yellow (warning)
├─ 71-99%: Primary
└─ 100%: Green (success)
    └─ Brief celebration animation
```

---

## 7. DRAG & DROP INTERACTIONS

### 7.1 Kanban Card Drag

```
DRAG START:
├─ Cursor: grab → grabbing
├─ Card opacity: 1 → 0.8
├─ Card scale: 1 → 1.05
├─ Card rotation: 0 → 2deg
├─ Box-shadow: Increase elevation
└─ Ghost element: Follows cursor

DRAGGING:
├─ Card follows cursor smoothly
├─ Drop zones highlight
│   └─ Background: Primary (10% opacity)
│   └─ Dashed border: 2px dashed Primary
├─ Visual indicator: Can/cannot drop
│   └─ Cursor: copy or no-drop
└─ Other cards shift to make space

DROP:
├─ Card animates to final position
│   └─ Spring physics (slight overshoot)
│   └─ Duration: 0.3s
├─ Restore: opacity, scale, rotation
├─ Drop zone: Flash success
│   └─ Brief green highlight
├─ Update data
└─ Haptic feedback (mobile)

DRAG CANCEL (ESC or invalid drop):
├─ Card animates back to origin
├─ Spring animation
├─ Duration: 0.4s
└─ Drop zone: Remove highlight
```

---

### 7.2 File Upload Drag & Drop

```
DEFAULT STATE:
├─ Dashed border: 2px dashed #D1D5DB
├─ Background: #FAFAFA
├─ Icon: Upload icon
├─ Text: "Drag files here or click to browse"
└─ Padding: 40px

DRAG OVER:
├─ Border: 2px dashed Primary
├─ Background: Primary (5% opacity)
├─ Scale: 1.02
├─ Icon: Animate (pulse)
├─ Text: "Drop files here"
└─ Transition: 0.2s

DROP FILES:
├─ Flash: Primary (20% opacity)
├─ Duration: 0.3s
├─ Show file preview immediately
│   └─ Fade in: opacity 0 → 1
├─ Upload progress bar appears
└─ Success checkmark when complete

FILE PREVIEW:
├─ Thumbnail (if image)
├─ File name
├─ File size
├─ Progress bar (uploading)
├─ Remove button (X)
└─ Status icon (pending/uploading/complete/error)
```

---

## 8. LIST & TABLE INTERACTIONS

### 8.1 List Item Hover

```
DEFAULT:
├─ Background: White
├─ Border-bottom: 1px solid #E5E7EB
└─ Transition: background 0.15s

HOVER:
├─ Background: #F9FAFB
├─ Cursor: pointer (if clickable)
├─ Quick actions: Fade in (right side)
│   └─ Edit, Delete icons
│   └─ opacity: 0 → 1
│   └─ translateX: 10px → 0
└─ Transition: 0.15s ease-out

ACTIVE (selected):
├─ Background: Primary (5% opacity)
├─ Border-left: 3px solid Primary
├─ Font-weight: 600
└─ Checkbox checked (if multi-select)
```

---

### 8.2 Table Row Interactions

```
DEFAULT:
├─ Background: White
├─ Alternate rows: #FAFAFA (zebra striping)
└─ Border-bottom: 1px solid #E5E7EB

HOVER:
├─ Background: Primary (3% opacity)
├─ Cursor: pointer (if clickable)
├─ Actions column: Buttons appear
│   └─ Fade in + slide
└─ Elevation: Subtle shadow

SELECTED (checkbox):
├─ Background: Primary (10% opacity)
├─ Checkbox: Checked
├─ Bulk action bar: Slides down from top
│   └─ "X items selected" + Action buttons
└─ Clear visual differentiation

EXPANDABLE ROW:
├─ Chevron icon: ▶ rotates to ▼
├─ Duration: 0.2s
├─ Detail section: Slides down
│   └─ Height: 0 → auto
│   └─ Opacity: 0 → 1
│   └─ Duration: 0.3s ease-out
└─ Collapse reverses animation

SORTABLE COLUMNS:
├─ Header: Hover shows sort icon
├─ Click: Sort ascending/descending
├─ Icon animation: ↕ → ↑ or ↓
├─ Rows: Rearrange with stagger
│   └─ Each row fades + moves
│   └─ Stagger: 0.05s delay
└─ Smooth transition: 0.3s
```

---

## 9. NAVIGATION INTERACTIONS

### 9.1 Sidebar Menu

```
MENU ITEM (default):
├─ Background: Transparent
├─ Icon + Text
├─ Padding: 12px 16px
├─ Border-radius: 8px
└─ Transition: all 0.15s

HOVER:
├─ Background: rgba(0,0,0,0.05)
├─ Icon: Slightly larger (scale 1.1)
├─ Cursor: pointer
└─ Transition: 0.15s ease-out

ACTIVE (current page):
├─ Background: Primary (10% opacity)
├─ Icon: Primary color
├─ Text: Primary color, font-weight 600
├─ Border-left: 3px solid Primary
└─ Slide-in animation on page load

CLICK:
├─ Brief scale down: 0.98
├─ Ripple effect from click point
├─ Navigate with smooth transition
└─ Loading indicator if needed

COLLAPSED SIDEBAR:
├─ Width: 240px → 64px
├─ Text: Fade out (opacity 0)
├─ Icons: Center align
├─ Tooltip: Show on hover (right side)
│   └─ Delay: 0.5s
└─ Animation: 0.3s ease-out
```

---

### 9.2 Tab Navigation

```
TAB (inactive):
├─ Text: Gray color
├─ Border-bottom: 2px transparent
├─ Padding: 12px 24px
└─ Cursor: pointer

TAB HOVER:
├─ Text: Darker gray
├─ Background: Gray (5% opacity)
└─ Transition: 0.15s

TAB ACTIVE:
├─ Text: Primary color, font-weight 600
├─ Border-bottom: 2px solid Primary
│   └─ Slide animation: width 0 → 100%
│   └─ Duration: 0.2s ease-out
└─ Background: Primary (3% opacity)

TAB SWITCH:
├─ Old content: Fade out + slide left
│   └─ opacity: 1 → 0
│   └─ translateX: 0 → -20px
│   └─ Duration: 0.2s
├─ New content: Fade in + slide right
│   └─ opacity: 0 → 1
│   └─ translateX: 20px → 0
│   └─ Duration: 0.2s
│   └─ Delay: 0.1s (after old content starts)
└─ Active indicator: Slides to new position
    └─ Duration: 0.3s ease-out
```

---

## 10. SEARCH INTERACTIONS

### 10.1 Search Bar

```
DEFAULT:
├─ Width: 240px
├─ Icon: 🔍 left side
├─ Placeholder: "Search..."
└─ Border: 1px solid #E5E7EB

FOCUS:
├─ Width: 240px → 400px (desktop)
├─ Border: 2px solid Primary
├─ Box-shadow: 0 0 0 3px rgba(primary, 0.1)
├─ Duration: 0.2s ease-out
└─ Search suggestions: Slide down

TYPING:
├─ Debounce: 300ms
├─ Loading indicator: Spinner in right
├─ Cancel button (X): Fade in
└─ Live search results update

SEARCH RESULTS:
├─ Dropdown: Slides down
├─ Opacity: 0 → 1
├─ TranslateY: -10px → 0
├─ Max-height: 400px
├─ Overflow: auto
├─ Keyboard navigation: Arrow keys
│   └─ Highlight moves smoothly
│   └─ Background highlight animation
└─ Empty state: "No results found" with icon
```

---

### 10.2 Global Search (Command Palette)

```
TRIGGER: Cmd/Ctrl + K
↓
OPENING:
├─ Backdrop: Fade in (0.2s)
├─ Search modal: Scale + fade
│   └─ scale(0.9) → scale(1)
│   └─ opacity: 0 → 1
│   └─ Duration: 0.3s ease-out
└─ Focus: Auto-focus search input

SEARCH INPUT:
├─ Large, prominent input
├─ Icon: 🔍
├─ Placeholder: "Search or type a command..."
├─ Live results below
└─ Keyboard navigation

RESULTS:
├─ Grouped by category
│   ├─ Recent searches
│   ├─ Expeditions
│   ├─ Leads
│   ├─ Travelers
│   └─ Commands
├─ Each result:
│   ├─ Icon
│   ├─ Title (highlighted matching text)
│   ├─ Description
│   └─ Keyboard shortcut (if applicable)
├─ Hover: Background highlight
├─ Selected (keyboard): Primary background
└─ Stagger animation: Each result fades in
    └─ Delay: index * 0.05s

CLOSE: ESC or backdrop click
├─ Scale down: 1 → 0.95
├─ Fade out: opacity 1 → 0
├─ Duration: 0.2s
└─ Clear search input
```

---

## 11. FORM VALIDATION FEEDBACK

### 11.1 Real-Time Validation

```
VALID INPUT:
├─ Border: Green
├─ Icon: ✓ (checkmark) - fade in
├─ Duration: 0.2s
└─ Brief highlight: Flash green background

INVALID INPUT:
├─ Border: Red
├─ Icon: ⚠ or ✗
├─ Shake animation
│   └─ translateX: 0 → -5px → 5px → 0
│   └─ Duration: 0.3s
├─ Error message: Slide down
│   └─ opacity: 0 → 1
│   └─ translateY: -10px → 0
│   └─ Duration: 0.2s
└─ Color: Red text

WARNING INPUT:
├─ Border: Yellow
├─ Icon: ⚠
├─ Warning message: Yellow background
└─ Transition: 0.2s
```

---

### 11.2 Form Submission

```
ON SUBMIT:
├─ Button: Loading state
│   └─ Spinner replaces text
├─ Form: Pointer-events none
├─ Subtle overlay: Prevent interaction
└─ Wait for response

SUCCESS:
├─ Button: Success state (green + checkmark)
├─ Duration: 0.5s
├─ Success message: Slide down from top
│   └─ Green background
│   └─ Checkmark icon
│   └─ "Success!" message
├─ Form: Fade out (optional)
└─ Redirect or reset form

ERROR:
├─ Button: Error state (red + X)
├─ Duration: 0.5s, then revert
├─ Error message: Slide down
│   └─ Red background
│   └─ Error icon
│   └─ Specific error text
├─ Shake form slightly
├─ Scroll to first error field
├─ Focus first error field
└─ Highlight error fields
```

---

## 12. IMAGE & MEDIA INTERACTIONS

### 12.1 Image Gallery

```
THUMBNAIL:
├─ Cursor: zoom-in
├─ Hover: Overlay with preview icon
│   └─ Opacity: 0 → 0.3
│   └─ Icon: 🔍
│   └─ Slight scale: 1 → 1.05
└─ Transition: 0.2s

CLICK → LIGHTBOX:
├─ Backdrop: Fade in (0.3s)
├─ Image: Zoom from thumbnail
│   └─ Transform: thumbnail position → center
│   └─ Scale: thumbnail size → full size
│   └─ Duration: 0.4s
│   └─ Easing: ease-out (cubic-bezier)
├─ Controls: Fade in
│   └─ Previous/Next arrows
│   └─ Close button
│   └─ Zoom controls
│   └─ Download button
└─ Caption: Slide up from bottom

NAVIGATION:
├─ Arrow click or keyboard: Slide to next/prev
├─ Swipe gesture (mobile): Drag to navigate
├─ Current image: Slide out
│   └─ translateX: 0 → -100% (next)
│   └─ translateX: 0 → 100% (prev)
├─ New image: Slide in
│   └─ translateX: 100% → 0 (next)
│   └─ translateX: -100% → 0 (prev)
├─ Duration: 0.3s
└─ Smooth, fluid motion

ZOOM:
├─ Pinch gesture or button click
├─ Transform: scale(1 → 2)
├─ Pan: Draggable when zoomed
├─ Smooth momentum scrolling
└─ Double-tap: Toggle zoom

CLOSE:
├─ Zoom out to thumbnail position
├─ Reverse of opening animation
├─ Duration: 0.3s
└─ Backdrop fade out
```

---

### 12.2 Video Player

```
PLAY/PAUSE:
├─ Large center button (overlay)
├─ Fade out after 2s of inactivity
├─ Fade in on mouse move
├─ Click anywhere: Toggle play/pause
└─ Spacebar: Toggle play/pause

LOADING:
├─ Spinner in center
├─ Progress bar shows buffered
├─ Subtle pulse animation
└─ Text: "Loading..."

CONTROLS (bottom bar):
├─ Play/Pause button
├─ Time: Current / Duration
├─ Progress bar (seekable)
├─ Volume (slider on hover)
├─ Settings (speed, quality)
├─ Fullscreen toggle
└─ Auto-hide after 3s inactivity

PROGRESS BAR:
├─ Hover: Thumbnail preview
│   └─ Show frame at hover position
│   └─ Time tooltip
├─ Click/drag: Seek
├─ Smooth scrubbing
└─ Buffered indicator (lighter color)
```

---

## 13. DATA VISUALIZATION ANIMATIONS

### 13.1 Chart Entrance

```
LINE CHART:
├─ Path draws from left to right
├─ Stroke-dasharray animation
├─ Duration: 1s ease-out
├─ Points: Appear after line (stagger)
│   └─ Scale: 0 → 1
│   └─ Delay: line completion + index * 0.05s
└─ Tooltip: Available after animation

BAR CHART:
├─ Bars grow from bottom to final height
├─ Transform: scaleY(0 → 1)
├─ Transform-origin: bottom
├─ Duration: 0.5s ease-out
├─ Stagger: index * 0.05s
└─ Value labels: Fade in (0.3s)

DONUT/PIE CHART:
├─ Segments draw clockwise
├─ Stroke-dasharray animation
├─ Duration: 1s ease-out
├─ Stagger by segment: index * 0.1s
└─ Labels: Fade in after segments

HOVER ON DATA POINT:
├─ Point: Scale up (1 → 1.3)
├─ Tooltip: Fade + slide in
│   └─ translateY: 10px → 0
│   └─ opacity: 0 → 1
│   └─ Duration: 0.2s
├─ Connected line: Highlight (if applicable)
└─ Other points: Slightly dim (opacity 0.5)
```

---

### 13.2 Metric Cards

```
COUNTING ANIMATION:
├─ Number counts from 0 to target
├─ Duration: 1s ease-out
├─ Easing: Decelerate (fast start, slow end)
├─ Format: Locale-specific (commas, decimals)
└─ Trigger: On viewport entry (scroll)

TREND INDICATOR:
├─ Arrow icon: ↑ (up) or ↓ (down)
├─ Entrance: Scale + rotate
│   └─ scale: 0 → 1
│   └─ rotate: -45deg → 0 (for ↑)
│   └─ Duration: 0.3s
├─ Color: Green (up) or Red (down)
└─ Percentage: Counts up

SPARKLINE (mini chart):
├─ Draw animation: Left to right
├─ Duration: 0.8s
├─ Easing: ease-out
└─ Delay: After number animation
```

---

## 14. EMPTY STATES & ERRORS

### 14.1 Empty State

```
COMPONENTS:
├─ Illustration (SVG)
│   └─ Entrance: Fade + slight scale
│   └─ Duration: 0.5s
├─ Heading: "No [items] yet"
│   └─ Fade in: 0.3s, delay 0.2s
├─ Description: Helpful explanation
│   └─ Fade in: 0.3s, delay 0.4s
├─ CTA Button: "Create [item]"
│   └─ Fade + slide up
│   └─ Duration: 0.3s, delay 0.6s
└─ Secondary links: Optional guidance

ILLUSTRATION ANIMATION:
├─ Subtle floating motion
├─ translateY: 0 → -5px → 0
├─ Duration: 2s infinite
├─ Easing: ease-in-out
└─ Non-distracting, ambient
```

---

### 14.2 Error State

```
ERROR PAGE:
├─ Error code: Large, prominent (404, 500)
│   └─ Fade in + scale: 0.9 → 1
├─ Error icon: ⚠ or custom illustration
│   └─ Shake animation on load
├─ Message: Clear explanation
├─ Action buttons:
│   ├─ "Go Home"
│   ├─ "Try Again"
│   └─ "Contact Support"
└─ Background: Subtle error color

INLINE ERROR (component):
├─ Icon: ⚠ Red
├─ Border: Red
├─ Background: Red (5% opacity)
├─ Shake animation
├─ Error message: Fade in below
└─ Optional: "Try again" action
```

---

## 15. ACCESSIBILITY & REDUCED MOTION

### 15.1 Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
  
  /* Replace animations with instant state changes */
  .modal-enter {
    transform: none;
    opacity: 1;
  }
  
  /* Keep essential animations (loading states) */
  .spinner {
    animation: spin 1s linear infinite;
  }
}
```

---

### 15.2 Focus States

```
KEYBOARD FOCUS:
├─ Outline: 2px solid Primary
├─ Outline-offset: 2px
├─ Border-radius: Inherit from element
├─ Never remove outline (accessibility)
└─ High contrast mode compatible

FOCUS-VISIBLE (modern browsers):
├─ Show only for keyboard navigation
├─ Hide for mouse clicks
├─ Maintain accessibility
└─ Better UX for both input methods

SKIP LINKS:
├─ Hidden by default
├─ Visible on focus
├─ "Skip to main content"
├─ Position: Top-left, fixed
└─ High z-index
```

---

## ANIMATION PERFORMANCE BEST PRACTICES

### Optimizations

```
PERFORMANT PROPERTIES:
✅ transform (translate, scale, rotate)
✅ opacity
✅ filter (use sparingly)

AVOID ANIMATING:
❌ width, height (causes reflow)
❌ top, left, margin (causes reflow)
❌ border-width (causes repaint)

USE GPU ACCELERATION:
├─ will-change: transform, opacity (sparingly)
├─ transform: translateZ(0) (force GPU layer)
└─ Cleanup: Remove will-change after animation

DEBOUNCE & THROTTLE:
├─ Scroll events: Throttle to 16ms (60fps)
├─ Search input: Debounce 300ms
├─ Window resize: Debounce 150ms
└─ Use requestAnimationFrame for smooth animations
```

---

## MICRO-INTERACTION SUMMARY

✅ **Buttons**: 6 states (default, hover, active, loading, success, error)
✅ **Inputs**: Real-time validation, floating labels, error states
✅ **Cards**: Hover elevate, loading skeletons, image zoom
✅ **Modals**: Smooth open/close with backdrop
✅ **Toasts**: Slide + fade, auto-dismiss, stackable
✅ **Loading**: Skeletons, spinners, progress bars
✅ **Drag & Drop**: Ghost elements, drop zones, spring physics
✅ **Navigation**: Active states, smooth transitions
✅ **Search**: Expandable bar, live results, command palette
✅ **Forms**: Live validation, submission feedback
✅ **Media**: Lightbox, video controls, zoom
✅ **Charts**: Draw animations, interactive tooltips
✅ **Empty/Error**: Helpful illustrations, clear actions
✅ **Accessibility**: Reduced motion, focus states

**Total Micro-Interactions Documented**: 80+

Every interaction is designed to feel **intentional**, **responsive**, and **delightful** while maintaining **performance** and **accessibility**.

---

This level of attention to detail transforms the platform from functional software into a **premium experience** that users love to interact with.
