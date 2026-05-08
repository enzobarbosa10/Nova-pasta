# USER FLOWS
## Complete Journey Mapping for All User Roles

---

## 1. AGENCY ADMINISTRATOR FLOW

**Role**: Strategic oversight, business management, team coordination  
**Primary Device**: Desktop (70%), Mobile (30%)  
**Experience Goal**: Feel in complete control of the operation

---

### 1.1 ONBOARDING FLOW

```
START: User receives invitation email
↓
Click "Get Started"
↓
SCREEN: Welcome
├── "Welcome to [BRAND_NAME]"
├── Brief value proposition
└── [Continue] button
↓
SCREEN: Account Creation
├── Full Name
├── Email (pre-filled)
├── Password
├── Phone (with country selector)
└── [Create Account] button
↓
Email verification sent
↓
SCREEN: Email Verification
├── "Check your inbox"
├── Resend option
└── Auto-detect when verified
↓
SCREEN: Brand Setup (Step 1/5)
├── "Let's set up your agency"
├── Agency Name
├── Website (optional)
├── [Continue] button
↓
SCREEN: Brand Identity (Step 2/5)
├── Logo Upload (drag & drop area)
│   └── Preview in sidebar
├── Primary Color Picker
│   └── Live preview of UI
├── [Continue] button
↓
SCREEN: WhatsApp Integration (Step 3/5)
├── "Connect your WhatsApp Business"
├── Phone number
├── QR Code scan
├── [Skip for now] / [Connect] buttons
↓
SCREEN: Team Setup (Step 4/5)
├── "Invite your team"
├── Email input + Role selector
│   ├── Operator
│   ├── Guide
│   └── Partner
├── [Add another] link
├── [Skip] / [Send Invitations] buttons
↓
SCREEN: First Expedition (Step 5/5)
├── "Create your first expedition"
├── Expedition Name
├── Destination
├── Start Date
├── Duration (days)
├── Capacity
├── [Create] / [Skip - Do this later] buttons
↓
SCREEN: Onboarding Complete
├── Success animation
├── "You're all set!"
├── Quick tips carousel
└── [Go to Dashboard] button
```

**Design Notes**:
- Progress indicator at top (1/5, 2/5, etc.)
- Allow skipping optional steps
- Auto-save progress
- Beautiful illustrations for each step
- Celebration animation on completion
- Estimated time: "2 minutes to get started"

---

### 1.2 DAILY DASHBOARD VIEW

```
SCREEN: Administrator Dashboard

┌─────────────────────────────────────────────────────┐
│ HEADER                                               │
│ "Good morning, [Name]" + Weather + Date             │
│ Quick Actions: [+ New Lead] [+ Expedition] [+ Task] │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ KEY METRICS (4 cards in row)                        │
│ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ │
│ │Revenue  │ │Leads    │ │Occupancy│ │NPS Score│ │
│ │$125,400 │ │23 active│ │78%      │ │9.2/10   │ │
│ │+12% ↑   │ │+5 this  │ │Next: 85%│ │+0.3 ↑   │ │
│ │         │ │week     │ │         │ │         │ │
│ └─────────┘ └─────────┘ └─────────┘ └─────────┘ │
└─────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ UPCOMING EXPEDITIONS     │ PIPELINE SNAPSHOT        │
│                          │                          │
│ ┌──────────────────────┐ │ Hot Leads        12     │
│ │ Patagonia Trek       │ │ Proposal Sent     8     │
│ │ May 15-22 · 12/15    │ │ Negotiation       5     │
│ │ Guide: João Silva    │ │ Won This Week     3     │
│ │ [View Details]       │ │                          │
│ └──────────────────────┘ │ Conversion: 24%  ↑      │
│                          │ [View Full Pipeline]     │
│ ┌──────────────────────┐ │                          │
│ │ Amazon Expedition    │ │                          │
│ │ Jun 3-10 · 8/20      │ │                          │
│ │ Guide: Maria Costa   │ │                          │
│ │ [View Details]       │ │                          │
│ └──────────────────────┘ │                          │
│                          │                          │
│ [View All Expeditions]   │                          │
└──────────────────────────┴──────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ OPERATIONAL STATUS       │ RECENT ACTIVITY          │
│                          │                          │
│ ✓ All reservations OK    │ • Lead converted to...   │
│ ⚠ 2 pending approvals    │ • Payment received...    │
│ ✓ Logistics confirmed    │ • New expedition...      │
│ • 3 tasks due today      │ • Guide assigned...      │
│                          │ • Testimonial added...   │
│ [View Operations]        │                          │
└──────────────────────────┴──────────────────────────┘
```

**Interactions**:
- Hover on metric cards shows mini trend chart
- Click metric opens detailed view
- Expedition cards are draggable (for quick reorder)
- Real-time updates via WebSocket
- Pull-to-refresh on mobile
- Customizable widget layout (future)

---

### 1.3 CRM PIPELINE FLOW

```
CLICK: Sidebar → CRM
↓
SCREEN: CRM Pipeline (Kanban View)

┌─────────────────────────────────────────────────────┐
│ [+ New Lead] [Import] [Filter] [Export] [Search]    │
└─────────────────────────────────────────────────────┘

┌────────┬────────┬────────┬────────┬────────┬────────┐
│New     │Contact │Proposal│Negotiat│Won     │Lost    │
│(12)    │Made(8) │Sent(5) │ion(3)  │(45)    │(12)    │
├────────┼────────┼────────┼────────┼────────┼────────┤
│┌──────┐│┌──────┐│┌──────┐│┌──────┐│┌──────┐│┌──────┐│
││Lead 1││││Lead 6│││Lead11│││Lead14│││Lead17│││Lead20││
││      ││││      │││      │││      │││      │││      ││
││Carlos││││Ana   │││Pedro │││Julia │││Marco │││Lucas ││
││Silva ││││Costa │││Lopes │││Rocha │││Santos│││Souza ││
││      ││││      │││      │││      │││      │││      ││
││Patag.││││Amaz. │││Patag.│││Amaz. │││Patag.│││Amaz. ││
││Trek  ││││Exp.  │││Trek  │││Exp.  │││Trek  │││Exp.  ││
││      ││││      │││      │││      │││      │││      ││
││$3.5k ││││$5.2k │││$3.5k │││$5.2k │││$3.5k │││$5.2k ││
││      ││││      │││      │││      │││      │││      ││
││📱💬  ││││📱💬  │││📱💬  │││📱💬  │││✓     │││✗     ││
│└──────┘│└──────┘│└──────┘│└──────┘│└──────┘│└──────┘│
│        │        │        │        │        │        │
│┌──────┐│┌──────┐│┌──────┐│        │        │        │
││Lead 2││││Lead 7│││Lead12│││        │        │        │
│└──────┘│└──────┘│└──────┘│        │        │        │
└────────┴────────┴────────┴────────┴────────┴────────┘
```

**Lead Card Components**:
- Contact name
- Desired expedition
- Estimated value
- Priority indicator (color-coded)
- Last contact date
- Quick actions (call, message, email)
- Next follow-up date

**Actions**:
```
CLICK: Lead Card
↓
SCREEN: Lead Detail (Side Panel)

┌─────────────────────────┐
│ [←] Carlos Silva        │
│ carlos@email.com        │
│ +55 11 98765-4321       │
├─────────────────────────┤
│ TABS:                   │
│ [Overview] [Activity]   │
│ [Files] [Notes]         │
├─────────────────────────┤
│ Expedition Interest:    │
│ → Patagonia Trek        │
│                         │
│ Estimated Value:        │
│ → $3,500                │
│                         │
│ Source:                 │
│ → Instagram Ad          │
│                         │
│ Priority: ⭐⭐⭐         │
│                         │
│ Next Follow-up:         │
│ → May 8, 10:00 AM       │
│   [Reschedule]          │
│                         │
│ Assigned to:            │
│ → Maria (Operator)      │
├─────────────────────────┤
│ QUICK ACTIONS           │
│ [📞 Call]               │
│ [💬 WhatsApp]           │
│ [📧 Email]              │
│ [📄 Send Proposal]      │
│ [✓ Convert to Traveler] │
├─────────────────────────┤
│ ACTIVITY TIMELINE       │
│ ○ Follow-up scheduled   │
│   Today, 9:15 AM        │
│                         │
│ ○ Sent information      │
│   Yesterday, 2:30 PM    │
│                         │
│ ○ First contact         │
│   May 5, 4:45 PM        │
│                         │
│ [Add Note]              │
└─────────────────────────┘
```

**Drag & Drop Behavior**:
- Drag card between columns
- Visual feedback (ghost card)
- Auto-update stage
- Trigger automation based on stage
- Confirmation for important moves

**Bulk Actions**:
- Select multiple leads
- Assign to team member
- Add tag
- Send bulk email
- Export selection
- Delete/Archive

---

### 1.4 EXPEDITION MANAGEMENT FLOW

```
CLICK: Sidebar → Expeditions
↓
SCREEN: Expeditions Overview

┌─────────────────────────────────────────────────────┐
│ VIEW: [Calendar] [List] [Grid]    [+ New Expedition]│
└─────────────────────────────────────────────────────┘

CALENDAR VIEW:
┌─────────────────────────────────────────────────────┐
│  May 2026                    [Today] [◄] [►]        │
├────┬────┬────┬────┬────┬────┬────────────────────┤
│Sun │Mon │Tue │Wed │Thu │Fri │Sat                   │
├────┼────┼────┼────┼────┼────┼────────────────────┤
│    │    │    │    │ 1  │ 2  │ 3                    │
│    │    │    │    │    │    │                      │
├────┼────┼────┼────┼────┼────┼────────────────────┤
│ 4  │ 5  │ 6  │ 7  │ 8  │ 9  │ 10                   │
│    │    │    │    │    │    │                      │
├────┼────┼────┼────┼────┼────┼────────────────────┤
│ 11 │ 12 │ 13 │ 14 │ 15 │ 16 │ 17                   │
│    │    │    │    │════════════                    │
│    │    │    │    │Patagonia  │                    │
│    │    │    │    │Trek 12/15 │                    │
│    │    │    │    │════════════                    │
├────┼────┼────┼────┼────┼────┼────────────────────┤
│ 18 │ 19 │ 20 │ 21 │ 22 │ 23 │ 24                   │
│    │    │    │    │    │    │                      │
├────┼────┼────┼────┼────┼────┼────────────────────┤
│ 25 │ 26 │ 27 │ 28 │ 29 │ 30 │ 31                   │
│    │    │    │    │    │    │                      │
└────┴────┴────┴────┴────┴────┴────────────────────┘
```

**Creating New Expedition**:
```
CLICK: [+ New Expedition]
↓
MODAL: New Expedition (Multi-step)

STEP 1: Basic Information
┌─────────────────────────┐
│ CREATE EXPEDITION       │
│ Step 1 of 5             │
├─────────────────────────┤
│ Expedition Name*        │
│ [_________________]     │
│                         │
│ Destination*            │
│ [_________________]     │
│                         │
│ Dates*                  │
│ [Start] → [End]         │
│ (Date picker)           │
│                         │
│ Duration                │
│ → 7 days (auto-calc)    │
│                         │
│ [Cancel] [Continue →]   │
└─────────────────────────┘
↓
STEP 2: Capacity & Pricing
┌─────────────────────────┐
│ CREATE EXPEDITION       │
│ Step 2 of 5             │
├─────────────────────────┤
│ Maximum Capacity*       │
│ [___] travelers         │
│                         │
│ Minimum to Confirm      │
│ [___] travelers         │
│                         │
│ Price per Person*       │
│ [___] BRL               │
│                         │
│ Early Bird Discount     │
│ [___]% until [date]     │
│                         │
│ [← Back] [Continue →]   │
└─────────────────────────┘
↓
STEP 3: Itinerary
┌─────────────────────────┐
│ CREATE EXPEDITION       │
│ Step 3 of 5             │
├─────────────────────────┤
│ BUILD ITINERARY         │
│                         │
│ Day 1                   │
│ [Add activities]        │
│                         │
│ Day 2                   │
│ [Add activities]        │
│                         │
│ ...                     │
│                         │
│ [+ Add Day]             │
│                         │
│ [Use Template]          │
│                         │
│ [← Back] [Continue →]   │
└─────────────────────────┘
↓
STEP 4: Logistics
┌─────────────────────────┐
│ CREATE EXPEDITION       │
│ Step 4 of 5             │
├─────────────────────────┤
│ Accommodation           │
│ [Select property] [+]   │
│                         │
│ Transportation          │
│ [Select vehicle] [+]    │
│                         │
│ Guide Assignment        │
│ [Select guide ▼]        │
│                         │
│ Meals Included          │
│ ☑ Breakfast             │
│ ☑ Lunch                 │
│ ☑ Dinner                │
│                         │
│ [← Back] [Continue →]   │
└─────────────────────────┘
↓
STEP 5: Review & Publish
┌─────────────────────────┐
│ CREATE EXPEDITION       │
│ Step 5 of 5             │
├─────────────────────────┤
│ REVIEW DETAILS          │
│                         │
│ ✓ Basic info complete   │
│ ✓ Pricing set           │
│ ✓ Itinerary ready       │
│ ✓ Logistics confirmed   │
│                         │
│ Visibility:             │
│ ○ Private (not listed)  │
│ ● Public (accept bookings)│
│                         │
│ [← Back] [Save Draft]   │
│ [Publish Expedition]    │
└─────────────────────────┘
↓
SUCCESS ANIMATION
↓
REDIRECT: Expedition Detail Page
```

**Expedition Detail View**:
```
SCREEN: Expedition Detail

┌─────────────────────────────────────────────────────┐
│ [←] Patagonia Trek                                   │
│ May 15-22, 2026 · 7 days                            │
│                                                      │
│ TABS: [Overview] [Travelers] [Itinerary] [Logistics]│
│       [Operations] [Financial] [Content]             │
└─────────────────────────────────────────────────────┘

TAB: Overview
┌──────────────────────────┬──────────────────────────┐
│ HERO IMAGE               │ CAPACITY METER           │
│ (Cover photo)            │ ┌──────────────────────┐ │
│                          │ │ 12/15 Confirmed      │ │
│                          │ │ ████████░░░░░ 80%    │ │
│                          │ │ [View Travelers]     │ │
│                          │ └──────────────────────┘ │
│                          │                          │
│                          │ STATUS                   │
│                          │ ● Confirmed              │
│                          │                          │
│                          │ GUIDE                    │
│                          │ 👤 João Silva            │
│                          │ [Contact]                │
│                          │                          │
│                          │ QUICK ACTIONS            │
│                          │ [Add Traveler]           │
│                          │ [Send Update]            │
│                          │ [View Operations]        │
│                          │ [Edit Details]           │
└──────────────────────────┴──────────────────────────┘

┌──────────────────────────────────────────────────────┐
│ KEY METRICS                                          │
│ ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────┐│
│ │Revenue    │ │Margin     │ │Occupancy  │ │Avg Age││
│ │$42,000    │ │$15,400    │ │80%        │ │38     ││
│ │           │ │36.7%      │ │           │ │       ││
│ └───────────┘ └───────────┘ └───────────┘ └───────┘│
└──────────────────────────────────────────────────────┘

TAB: Travelers
┌─────────────────────────────────────────────────────┐
│ TRAVELER LIST (12 confirmed, 0 waitlist)            │
│                                                      │
│ [+ Add Traveler] [Export List] [Send Group Message] │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 👤 Ana Silva                    ● Paid in full  │ │
│ │    ana@email.com · +55 11 9999-9999             │ │
│ │    [View Profile] [Contact] [Documents]         │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 👤 Carlos Santos                ⚠ Payment pending│ │
│ │    carlos@email.com · +55 11 8888-8888          │ │
│ │    [View Profile] [Contact] [Documents]         │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ... (remaining travelers)                            │
└─────────────────────────────────────────────────────┘

TAB: Operations
┌─────────────────────────────────────────────────────┐
│ OPERATIONAL CHECKLIST                                │
│                                                      │
│ ⏱ 7 days until departure                            │
│                                                      │
│ PRE-DEPARTURE (85% complete)                        │
│ ✓ Confirm accommodation                             │
│ ✓ Book transportation                               │
│ ✓ Prepare itinerary                                 │
│ ✓ Send traveler manual                              │
│ ⚠ Collect emergency contacts (10/12)                │
│ ☐ Final headcount confirmation                      │
│                                                      │
│ DEPARTURE DAY                                        │
│ ☐ Guide briefing                                     │
│ ☐ Equipment check                                    │
│ ☐ Traveler check-in                                  │
│                                                      │
│ [View Full Operations Dashboard]                     │
└─────────────────────────────────────────────────────┘
```

---

### 1.5 OPERATIONS DASHBOARD FLOW

```
CLICK: Sidebar → Operations
↓
SCREEN: Operations Dashboard

┌─────────────────────────────────────────────────────┐
│ OPERATIONS CENTER                                    │
│                                                      │
│ VIEW: [All] [Today] [This Week] [Upcoming]          │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ ACTIVE EXPEDITIONS (2)                               │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 🟢 IN PROGRESS                                   │ │
│ │ Amazon Expedition · Day 3 of 7                  │ │
│ │ Guide: Maria Costa · 8 travelers                │ │
│ │ Last update: 2 hours ago                        │ │
│ │ [View Live Status] [Contact Guide]              │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 🟡 DEPARTING SOON                                │ │
│ │ Patagonia Trek · In 7 days                      │ │
│ │ Guide: João Silva · 12/15 capacity              │ │
│ │ Checklist: 85% complete                         │ │
│ │ [View Checklist] [Expedition Details]           │ │
│ └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ TASKS DUE TODAY (3)      │ PENDING APPROVALS (2)    │
│                          │                          │
│ ☐ Confirm hotel booking  │ ⚠ Transportation change  │
│   Patagonia Trek         │   Amazon Expedition      │
│   Assigned: Maria        │   [Review & Approve]     │
│                          │                          │
│ ☐ Send travel documents  │ ⚠ Menu modification      │
│   Amazon Expedition      │   Patagonia Trek         │
│   Assigned: You          │   [Review & Approve]     │
│                          │                          │
│ ☐ Equipment check        │                          │
│   Patagonia Trek         │                          │
│   Assigned: João         │                          │
│                          │                          │
│ [View All Tasks]         │                          │
└──────────────────────────┴──────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ INCIDENTS & ALERTS                                   │
│                                                      │
│ ✓ Weather alert resolved - Amazon Expedition        │
│   Today, 11:30 AM                                    │
│                                                      │
│ (No active incidents)                                │
│                                                      │
│ [View History]                                       │
└─────────────────────────────────────────────────────┘
```

---

### 1.6 FINANCIAL DASHBOARD FLOW

```
CLICK: Sidebar → Financial
↓
SCREEN: Financial Overview

┌─────────────────────────────────────────────────────┐
│ FINANCIAL DASHBOARD                                  │
│                                                      │
│ Period: [May 2026 ▼] [Export Report]               │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ KEY METRICS                                          │
│ ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────┐│
│ │Revenue    │ │Profit     │ │Margin     │ │CAC    ││
│ │$125,400   │ │$48,200    │ │38.4%      │ │$450   ││
│ │+12% ↑     │ │+8% ↑      │ │+2.1pp ↑   │ │-$50 ↑ ││
│ └───────────┘ └───────────┘ └───────────┘ └───────┘│
└─────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ REVENUE TREND            │ PAYMENT STATUS           │
│ (Line chart)             │                          │
│ Last 12 months           │ ● Paid: $98,500 (78%)    │
│                          │ ● Pending: $18,200 (15%) │
│                          │ ⚠ Overdue: $8,700 (7%)   │
│                          │                          │
│                          │ [View Payments]          │
└──────────────────────────┴──────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ EXPEDITION PROFITABILITY                             │
│                                                      │
│ Expedition           Revenue   Cost    Margin  %    │
│ ─────────────────────────────────────────────────── │
│ Patagonia Trek       $42,000  $26,800  $15,200 36%  │
│ Amazon Expedition    $41,600  $28,500  $13,100 31%  │
│ Chapada Trek         $28,500  $18,200  $10,300 36%  │
│ ...                                                  │
│                                                      │
│ [View All]                                           │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ UPCOMING RECEIVABLES                                 │
│                                                      │
│ May 15 - Final payment (Patagonia) $12,400          │
│ May 20 - Deposit (Amazon Jun) $8,300                │
│ Jun 1 - Final payment (Amazon May) $10,200          │
│                                                      │
│ [View Payment Schedule]                              │
└─────────────────────────────────────────────────────┘
```

---

### 1.7 CONTENT & MEDIA BANK FLOW

```
CLICK: Sidebar → Content
↓
SCREEN: Media Bank

┌─────────────────────────────────────────────────────┐
│ MEDIA BANK                                           │
│                                                      │
│ [Upload] [Create Album] [Filter ▼] [Grid/List]     │
│                                                      │
│ Search: [____________________________] 🔍            │
└─────────────────────────────────────────────────────┘

GRID VIEW (Pinterest-style masonry):
┌─────────────────────────────────────────────────────┐
│ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐       │
│ │        │ │        │ │        │ │        │       │
│ │ Photo1 │ │ Photo2 │ │ Video1 │ │ Photo3 │       │
│ │        │ │        │ │ ▶      │ │        │       │
│ │Patagonia│ │Amazon  │ │Chapada │ │Patagonia│       │
│ └────────┘ └────────┘ └────────┘ └────────┘       │
│ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐       │
│ │        │ │        │ │        │ │        │       │
│ │ Photo4 │ │ Video2 │ │ Photo5 │ │ Photo6 │       │
│ │        │ │ ▶      │ │        │ │        │       │
│ │Amazon  │ │Patagonia│ │Chapada │ │Amazon  │       │
│ └────────┘ └────────┘ └────────┘ └────────┘       │
│                                                      │
│ Infinite scroll...                                   │
└─────────────────────────────────────────────────────┘

CLICK: Photo
↓
MODAL: Media Detail
┌─────────────────────────────────────────────────────┐
│ [←] [Share] [Download] [Delete]                     │
│                                                      │
│        ┌──────────────────────┐                     │
│        │                      │                     │
│        │   LARGE PREVIEW      │                     │
│        │                      │                     │
│        └──────────────────────┘                     │
│                                                      │
│ [◄ Previous] [Next ►]                               │
│                                                      │
│ Patagonia Trek · Day 3                              │
│ Captured: May 17, 2026 · 2:34 PM                    │
│ By: Guide João Silva                                 │
│                                                      │
│ Tags: [patagonia] [glacier] [group] [+]             │
│                                                      │
│ Used in:                                             │
│ • Instagram Post (May 18)                            │
│ • Post-trip Album                                    │
│                                                      │
│ [Add to Album] [Use in Post] [Send to Travelers]    │
└─────────────────────────────────────────────────────┘

UPLOAD FLOW:
┌─────────────────────────────────────────────────────┐
│ UPLOAD MEDIA                                         │
│                                                      │
│ ┌───────────────────────────────────────────────┐   │
│ │                                                │   │
│ │         Drag & drop files here                │   │
│ │              or click to browse                │   │
│ │                                                │   │
│ │    Supports: JPG, PNG, MP4, MOV               │   │
│ │    Max size: 50MB per file                    │   │
│ │                                                │   │
│ └───────────────────────────────────────────────┘   │
│                                                      │
│ Auto-tag with:                                       │
│ ☑ Expedition name (if uploading from expedition)    │
│ ☑ Date captured (from EXIF)                         │
│ ☑ Location (from GPS)                               │
│                                                      │
│ [Cancel] [Upload]                                    │
└─────────────────────────────────────────────────────┘
```

---

### 1.8 REPORTS & ANALYTICS FLOW

```
CLICK: Sidebar → Reports
↓
SCREEN: Analytics Dashboard

┌─────────────────────────────────────────────────────┐
│ ANALYTICS & INSIGHTS                                 │
│                                                      │
│ Period: [Last 90 days ▼] [Compare to: Previous ▼]  │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ CONVERSION FUNNEL                                    │
│                                                      │
│ Leads           ████████████████████ 120 (100%)     │
│                                                      │
│ Contacted       ███████████████░░░░░  89 (74%)      │
│                                                      │
│ Proposal Sent   ████████████░░░░░░░░  65 (54%)      │
│                                                      │
│ Negotiation     ████████░░░░░░░░░░░░  42 (35%)      │
│                                                      │
│ Won             ████░░░░░░░░░░░░░░░░  28 (23%)      │
│                                                      │
│ Overall Conversion: 23% (industry avg: 18%)         │
└─────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ LEAD SOURCE              │ OCCUPANCY TREND          │
│                          │                          │
│ Instagram:      45%      │ (Line chart)             │
│ Referrals:      28%      │                          │
│ Google Search:  18%      │ Current: 78%             │
│ Direct:          9%      │ Target: 85%              │
│                          │                          │
│ Best ROI: Referrals      │ Forecast: ↑ 82% (Jun)    │
└──────────────────────────┴──────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ KEY INSIGHTS (AI-generated)                          │
│                                                      │
│ 💡 Referrals convert 2.3x better than paid ads      │
│    → Consider launching referral incentive program   │
│                                                      │
│ 💡 Average booking window: 45 days                   │
│    → Send early-bird offers 60 days before departure │
│                                                      │
│ 💡 Instagram leads have highest lifetime value       │
│    → Increase Instagram ad budget by 20%             │
│                                                      │
│ [View All Insights]                                  │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ TRAVELER METRICS                                     │
│                                                      │
│ Repeat Rate:        42% (↑ 8%)                      │
│ Referral Rate:      38% (↑ 12%)                     │
│ Average Spend:      $3,450 (↑ 5%)                   │
│ NPS Score:          9.2/10 (↑ 0.3)                  │
│                                                      │
│ [Detailed Report]                                    │
└─────────────────────────────────────────────────────┘
```

---

### 1.9 SETTINGS & CONFIGURATION FLOW

```
CLICK: Sidebar → Settings
↓
SCREEN: Settings

SIDEBAR MENU:
- Company Profile
- Brand Identity
- Team & Permissions
- Integrations
- Notifications
- Billing
- API & Webhooks
- Security

TAB: Company Profile
┌─────────────────────────────────────────────────────┐
│ COMPANY INFORMATION                                  │
│                                                      │
│ Agency Name                                          │
│ [_____________________________________]              │
│                                                      │
│ Website                                              │
│ [_____________________________________]              │
│                                                      │
│ Email                                                │
│ [_____________________________________]              │
│                                                      │
│ Phone                                                │
│ [+55] [_________________________________]            │
│                                                      │
│ Address                                              │
│ [_____________________________________]              │
│ [_____________________________________]              │
│                                                      │
│ [Cancel] [Save Changes]                              │
└─────────────────────────────────────────────────────┘

TAB: Brand Identity
┌─────────────────────────────────────────────────────┐
│ BRAND CUSTOMIZATION                                  │
│                                                      │
│ Logo                                                 │
│ ┌──────────┐                                         │
│ │          │ [Change Logo]                           │
│ │  LOGO    │ [Remove]                                │
│ │          │                                         │
│ └──────────┘                                         │
│                                                      │
│ Brand Colors                                         │
│ Primary Color   [#0066FF] 🎨                         │
│ Secondary Color [#00CC88] 🎨                         │
│                                                      │
│ Preview:                                             │
│ ┌───────────────────────────────────────────────┐   │
│ │ Button [Primary]  Button [Secondary]          │   │
│ │ ■ Primary text   ■ Secondary text             │   │
│ └───────────────────────────────────────────────┘   │
│                                                      │
│ Traveler Portal Customization                        │
│ ☑ Show agency logo                                   │
│ ☑ Use brand colors                                   │
│ ☐ Custom domain (Enterprise)                        │
│                                                      │
│ [Reset to Default] [Save Changes]                    │
└─────────────────────────────────────────────────────┘

TAB: Team & Permissions
┌─────────────────────────────────────────────────────┐
│ TEAM MEMBERS                                         │
│                                                      │
│ [+ Invite Team Member]                               │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 👤 Maria Costa (You)                             │ │
│ │    maria@agency.com · Admin                     │ │
│ │    [Edit] cannot remove yourself                │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 👤 João Silva                                    │ │
│ │    joao@agency.com · Guide                      │ │
│ │    [Edit Role] [Remove]                         │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 👤 Ana Oliveira                                  │ │
│ │    ana@agency.com · Operator                    │ │
│ │    [Edit Role] [Remove]                         │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ [View Permission Matrix]                             │
└─────────────────────────────────────────────────────┘

TAB: Integrations
┌─────────────────────────────────────────────────────┐
│ CONNECTED SERVICES                                   │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ WhatsApp Business                    ✓ Connected│ │
│ │ Send messages directly from the platform        │ │
│ │ [Configure] [Disconnect]                        │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Stripe Payments                      ✓ Connected│ │
│ │ Accept credit cards and bank transfers          │ │
│ │ [Configure] [Disconnect]                        │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Google Analytics                     ○ Available│ │
│ │ Track traveler portal engagement                │ │
│ │ [Connect]                                       │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Mailchimp                            ○ Available│ │
│ │ Sync contacts and send campaigns                │ │
│ │ [Connect]                                       │ │
│ └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

---

## ADMINISTRATOR FLOW SUMMARY

**Core Workflows Mapped**:
1. ✓ Onboarding (5-step process)
2. ✓ Dashboard (command center)
3. ✓ CRM Pipeline (drag & drop)
4. ✓ Expedition Management (creation to execution)
5. ✓ Operations Center (task management)
6. ✓ Financial Dashboard (revenue & profitability)
7. ✓ Content Bank (media organization)
8. ✓ Analytics & Reports (insights)
9. ✓ Settings (configuration)

**Key UX Principles**:
- Information density balanced with clarity
- Quick actions always accessible
- Progressive disclosure
- Real-time updates
- Keyboard shortcuts
- Contextual help
- Beautiful, premium aesthetic

---

## 2. INTERNAL OPERATOR FLOW

**Role**: Execute daily operations  
**Primary Device**: Desktop (60%), Mobile (40%)  
**Experience Goal**: Fast, efficient task completion

---

### 2.1 OPERATOR DASHBOARD

```
LOGIN → Role-specific dashboard
↓
SCREEN: Operator Dashboard

┌─────────────────────────────────────────────────────┐
│ Good morning, Ana                                    │
│ You have 8 tasks due today                          │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ MY TASKS TODAY                      [View All (23)] │
│                                                      │
│ ☐ HIGH PRIORITY                                      │
│ Confirm hotel booking - Patagonia Trek              │
│ Due: Today, 2:00 PM                                  │
│ [Mark Complete] [View Details]                       │
│                                                      │
│ ☐ MEDIUM PRIORITY                                    │
│ Send travel documents - Amazon Expedition            │
│ Due: Today, 5:00 PM                                  │
│ [Mark Complete] [View Details]                       │
│                                                      │
│ ☐ LOW PRIORITY                                       │
│ Update traveler profile - Carlos Silva              │
│ Due: Today, 6:00 PM                                  │
│ [Mark Complete] [View Details]                       │
│                                                      │
│ + 5 more tasks                                       │
└─────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ ACTIVE EXPEDITIONS       │ PENDING CONFIRMATIONS    │
│                          │                          │
│ 🟢 Amazon (In Progress)  │ ⚠ 3 accommodation        │
│ 🟡 Patagonia (7 days)    │ ⚠ 2 transportation       │
│ 🔵 Chapada (14 days)     │ ✓ All meals confirmed    │
│                          │                          │
│ [View All]               │ [Process Queue]          │
└──────────────────────────┴──────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ RECENT ACTIVITY                                      │
│                                                      │
│ ✓ Task completed: Confirm transportation (10 min ago)│
│ • New lead assigned to you: Pedro Lima (25 min ago) │
│ ✓ Payment received: Ana Silva (1 hour ago)          │
│ • Guide update: Amazon Expedition Day 3 (2 hours ago)│
│                                                      │
│ [View All Activity]                                  │
└─────────────────────────────────────────────────────┘
```

**Operator-Specific Features**:
- Task-focused interface
- Clear priorities
- Quick-complete actions
- Minimal navigation depth
- Status indicators everywhere

---

### 2.2 OPERATOR CHECKLIST EXECUTION

```
CLICK: Task → "Confirm hotel booking"
↓
SCREEN: Task Detail

┌─────────────────────────────────────────────────────┐
│ [←] CONFIRM HOTEL BOOKING                           │
│                                                      │
│ Expedition: Patagonia Trek                           │
│ Dates: May 15-22, 2026                              │
│ Due: Today, 2:00 PM (in 3 hours)                    │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ PROPERTY DETAILS                                     │
│                                                      │
│ Hotel: Patagonia Lodge                               │
│ Contact: +54 9 2966 123456                           │
│ Email: reservas@patagonia-lodge.com                  │
│                                                      │
│ [Call] [Email] [WhatsApp]                           │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ BOOKING INFORMATION                                  │
│                                                      │
│ Rooms: 6 double rooms                                │
│ Guests: 12 travelers                                 │
│ Check-in: May 15, 2:00 PM                           │
│ Check-out: May 22, 11:00 AM                         │
│                                                      │
│ Special Requests:                                    │
│ - 2 vegetarian meal options                          │
│ - Early breakfast (6:00 AM)                          │
│                                                      │
│ [Copy Details]                                       │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ CONFIRMATION CHECKLIST                               │
│                                                      │
│ ☐ Called/emailed property                           │
│ ☐ Confirmed room availability                        │
│ ☐ Confirmed special requests                         │
│ ☐ Received confirmation number                       │
│ ☐ Updated reservation system                         │
│                                                      │
│ Confirmation Number:                                 │
│ [________________]                                   │
│                                                      │
│ Notes (optional):                                    │
│ [________________________________________]           │
│ [________________________________________]           │
│                                                      │
│ [Mark as Complete] [Need Help]                      │
└─────────────────────────────────────────────────────┘
```

**Operator Flow Optimization**:
- All info on one screen
- Click-to-call/email
- Copy details button
- Inline checklist
- Quick complete

---

### 2.3 OPERATOR TRAVELER MANAGEMENT

```
SCREEN: Traveler Profile (Operator View)

┌─────────────────────────────────────────────────────┐
│ [←] Ana Silva                                        │
│ ana@email.com · +55 11 9999-9999                    │
│                                                      │
│ [Call] [Email] [WhatsApp] [Edit]                    │
└─────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ EXPEDITION               │ PAYMENT STATUS           │
│ Patagonia Trek           │ ● Paid in Full           │
│ May 15-22, 2026          │ $3,500 (May 1)          │
│                          │                          │
│ [View Expedition]        │ [View Receipt]           │
└──────────────────────────┴──────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ DOCUMENTS REQUIRED                                   │
│                                                      │
│ ✓ Passport copy uploaded                            │
│ ✓ Emergency contact provided                        │
│ ✓ Medical form completed                            │
│ ✓ Insurance details provided                        │
│ ✓ Travel waiver signed                              │
│                                                      │
│ Status: ✅ ALL DOCUMENTS COMPLETE                   │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ COMMUNICATIONS SENT                                  │
│                                                      │
│ ✓ Booking confirmation (May 1)                      │
│ ✓ Payment receipt (May 1)                           │
│ ✓ Traveler manual (May 5)                           │
│ ✓ Pre-departure reminder (Scheduled for May 8)      │
│                                                      │
│ [Send Message] [View All]                           │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ SPECIAL REQUIREMENTS                                 │
│                                                      │
│ • Vegetarian meals                                   │
│ • Room on ground floor (mobility)                    │
│                                                      │
│ [Edit Requirements]                                  │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ OPERATOR ACTIONS                                     │
│                                                      │
│ [Send Portal Access]                                 │
│ [Request Documents]                                  │
│ [Add to Group WhatsApp]                              │
│ [Generate Invoice]                                   │
│ [Add Note]                                           │
└─────────────────────────────────────────────────────┘
```

---

## 3. GUIDE FLOW

**Role**: Field operations, expedition leadership  
**Primary Device**: Mobile (95%)  
**Experience Goal**: Essential info, quick updates, offline-capable

---

### 3.1 GUIDE MOBILE DASHBOARD

```
SCREEN: Guide Dashboard (Mobile)

┌─────────────────────┐
│ ☰  EXPEDITIONS  👤  │
├─────────────────────┤
│                     │
│ CURRENT EXPEDITION  │
│                     │
│ ┌─────────────────┐ │
│ │ 🟢 ACTIVE       │ │
│ │                 │ │
│ │ Amazon          │ │
│ │ Expedition      │ │
│ │                 │ │
│ │ Day 3 of 7      │ │
│ │ 8 travelers     │ │
│ │                 │ │
│ │ [Daily Log]     │ │
│ │ [Group Info]    │ │
│ │ [Itinerary]     │ │
│ │ [Contact HQ]    │ │
│ └─────────────────┘ │
│                     │
│ UPCOMING            │
│                     │
│ ┌─────────────────┐ │
│ │ Patagonia Trek  │ │
│ │ In 25 days      │ │
│ │ 12 travelers    │ │
│ │ [View Details]  │ │
│ └─────────────────┘ │
│                     │
│ PAST EXPEDITIONS    │
│ [View History]      │
│                     │
└─────────────────────┘
```

---

### 3.2 GUIDE EXPEDITION DETAIL (MOBILE)

```
CLICK: Active Expedition
↓
SCREEN: Expedition Detail (Guide View)

┌─────────────────────┐
│ [←] Amazon Expedition│
│ Day 3 of 7           │
├─────────────────────┤
│ TABS:               │
│ [Today][Group][Info]│
├─────────────────────┤
│                     │
│ TODAY'S ITINERARY   │
│                     │
│ ✓ 6:00 AM           │
│ Breakfast at lodge  │
│                     │
│ ✓ 7:30 AM           │
│ Depart for trail    │
│                     │
│ 🔵 10:00 AM (NOW)   │
│ Jungle hike         │
│ Duration: 3 hours   │
│ [Mark Complete]     │
│                     │
│ ○ 2:00 PM           │
│ Lunch at viewpoint  │
│                     │
│ ○ 4:00 PM           │
│ Return to lodge     │
│                     │
│ ○ 7:00 PM           │
│ Dinner & briefing   │
│                     │
├─────────────────────┤
│ QUICK ACTIONS       │
│                     │
│ [📷 Upload Photos]  │
│ [📝 Log Incident]   │
│ [💬 Contact HQ]     │
│ [✓ Daily Report]    │
│                     │
└─────────────────────┘

TAB: Group
┌─────────────────────┐
│ TRAVELERS (8)       │
│                     │
│ ┌─────────────────┐ │
│ │ 👤 Ana Silva    │ │
│ │ +55 11 999...   │ │
│ │ Vegetarian      │ │
│ │ [Call][Notes]   │ │
│ └─────────────────┘ │
│                     │
│ ┌─────────────────┐ │
│ │ 👤 Carlos Santos│ │
│ │ +55 11 888...   │ │
│ │ No restrictions │ │
│ │ [Call][Notes]   │ │
│ └─────────────────┘ │
│                     │
│ ... (6 more)        │
│                     │
│ [Group WhatsApp]    │
│ [Emergency Contacts]│
│                     │
└─────────────────────┘

TAB: Info
┌─────────────────────┐
│ EXPEDITION INFO     │
│                     │
│ Full Itinerary      │
│ [View/Download]     │
│                     │
│ Accommodation       │
│ Amazon Ecolodge     │
│ [Contact][Map]      │
│                     │
│ Emergency           │
│ HQ: +55 11 5555...  │
│ Hospital: +55 92... │
│ [Call HQ]           │
│                     │
│ Weather             │
│ ⛅ 28°C, Partly     │
│ cloudy              │
│                     │
│ Documents           │
│ [Medical Kit]       │
│ [Safety Protocol]   │
│ [Traveler Profiles] │
│                     │
└─────────────────────┘
```

---

### 3.3 GUIDE DAILY LOG

```
CLICK: [Daily Report]
↓
SCREEN: Daily Log Entry

┌─────────────────────┐
│ [←] DAILY REPORT    │
│ Day 3 - May 17      │
├─────────────────────┤
│                     │
│ Status              │
│ ● All good          │
│ ○ Minor issue       │
│ ○ Serious issue     │
│                     │
│ Activities          │
│ ✓ Morning hike      │
│ ✓ Lunch at viewpoint│
│ ✓ Afternoon activity│
│ ✓ Evening dinner    │
│                     │
│ Group Status        │
│ ✓ Everyone present  │
│ ✓ No health issues  │
│ ✓ Good spirits      │
│                     │
│ Weather             │
│ ⛅ Partly cloudy    │
│ 🌡 28°C / 82°F      │
│                     │
│ Notes (optional)    │
│ ┌─────────────────┐ │
│ │Great energy from│ │
│ │the group today. │ │
│ │Wildlife sightings│ │
│ │exceeded expectat│ │
│ └─────────────────┘ │
│                     │
│ Photos (3)          │
│ [📷] [📷] [📷]      │
│ [Add More]          │
│                     │
│ [Submit Report]     │
│                     │
└─────────────────────┘
```

**Guide Flow Optimization**:
- Large, touch-friendly buttons
- Minimal text input
- Camera integration
- Offline capability
- Quick status updates
- Emergency contact prominent

---

### 3.4 GUIDE INCIDENT LOGGING

```
CLICK: [Log Incident]
↓
SCREEN: Incident Report

┌─────────────────────┐
│ [←] LOG INCIDENT    │
├─────────────────────┤
│                     │
│ Severity            │
│ ● Minor             │
│ ○ Moderate          │
│ ○ Critical          │
│                     │
│ Type                │
│ [Select type ▼]     │
│ - Medical           │
│ - Equipment         │
│ - Weather           │
│ - Logistics         │
│ - Other             │
│                     │
│ Who is involved?    │
│ [Select traveler ▼] │
│ ☐ All travelers     │
│                     │
│ What happened?      │
│ ┌─────────────────┐ │
│ │                 │ │
│ │                 │ │
│ │                 │ │
│ └─────────────────┘ │
│                     │
│ Action taken        │
│ ┌─────────────────┐ │
│ │                 │ │
│ │                 │ │
│ └─────────────────┘ │
│                     │
│ Photos (optional)   │
│ [📷 Add Photo]      │
│                     │
│ ☑ Notify HQ immediately │
│                     │
│ [Cancel] [Submit]   │
│                     │
└─────────────────────┘
```

**Auto-Actions Based on Severity**:
- **Minor**: Logged, visible to ops team
- **Moderate**: Instant notification to ops
- **Critical**: Instant call trigger + SMS to multiple stakeholders

---

## 4. TRAVELER PORTAL FLOW

**Role**: Customer experience  
**Primary Device**: Mobile (90%)  
**Experience Goal**: Premium, inspiring, informative

---

### 4.1 TRAVELER PORTAL LOGIN

```
RECEIVE: Email with portal access
↓
CLICK: "Access Your Expedition"
↓
SCREEN: Portal Login

┌─────────────────────┐
│                     │
│   [AGENCY LOGO]     │
│                     │
│ Welcome to your     │
│ Patagonia Trek      │
│                     │
│ Email               │
│ [_________________] │
│                     │
│ Password            │
│ [_________________] │
│                     │
│ [Log In]            │
│                     │
│ Forgot password?    │
│                     │
└─────────────────────┘
```

---

### 4.2 TRAVELER DASHBOARD (PREMIUM EXPERIENCE)

```
SCREEN: Traveler Dashboard

┌─────────────────────┐
│ ☰    [LOGO]     👤  │
├─────────────────────┤
│                     │
│ [EXPEDITION IMAGE]  │
│ Beautiful landscape │
│ photo               │
│                     │
│ PATAGONIA TREK      │
│ May 15-22, 2026     │
│                     │
│ ⏱ 8 days until      │
│ departure           │
│                     │
├─────────────────────┤
│ QUICK ACCESS        │
│                     │
│ ┌────────┬────────┐ │
│ │📅      │🗺       │ │
│ │Itinerary│Route  │ │
│ └────────┴────────┘ │
│                     │
│ ┌────────┬────────┐ │
│ │✅      │👥      │ │
│ │Checklist│Group  │ │
│ └────────┴────────┘ │
│                     │
│ ┌────────┬────────┐ │
│ │💰      │📄      │ │
│ │Payment │Docs    │ │
│ └────────┴────────┘ │
│                     │
├─────────────────────┤
│ WHAT TO DO NOW      │
│                     │
│ ⚠ 2 pending items   │
│                     │
│ ☐ Upload passport   │
│    copy             │
│    [Upload Now]     │
│                     │
│ ☐ Complete medical  │
│    form             │
│    [Fill Form]      │
│                     │
├─────────────────────┤
│ DESTINATION GUIDE   │
│                     │
│ ☀ Weather Forecast  │
│ 🎒 What to Pack     │
│ 💡 Travel Tips      │
│ 📸 Inspiration      │
│                     │
└─────────────────────┘
```

**Design Philosophy**:
- Beautiful imagery
- Emotional connection
- Clear next steps
- Premium aesthetic
- Inspiring content
- No clutter

---

### 4.3 TRAVELER ITINERARY VIEW

```
CLICK: Itinerary
↓
SCREEN: Expedition Itinerary

┌─────────────────────┐
│ [←] ITINERARY       │
├─────────────────────┤
│                     │
│ [Cover Photo]       │
│                     │
│ PATAGONIA TREK      │
│ 7 Days of Adventure │
│                     │
│ [Download PDF]      │
│                     │
├─────────────────────┤
│                     │
│ DAY 1 · May 15      │
│ Arrival & Welcome   │
│                     │
│ [Image]             │
│                     │
│ 2:00 PM             │
│ Check-in at lodge   │
│ Patagonia Lodge     │
│                     │
│ 4:00 PM             │
│ Welcome briefing    │
│ Meet your guide &   │
│ fellow travelers    │
│                     │
│ 7:00 PM             │
│ Welcome dinner      │
│                     │
│ [View on Map]       │
│                     │
├─────────────────────┤
│                     │
│ DAY 2 · May 16      │
│ Glacier Hike        │
│                     │
│ [Image]             │
│                     │
│ 6:00 AM             │
│ Early breakfast     │
│                     │
│ 7:30 AM             │
│ Depart for glacier  │
│ Duration: 6 hours   │
│ Difficulty: Moderate│
│                     │
│ What to bring:      │
│ • Hiking boots      │
│ • Water bottle      │
│ • Sunscreen         │
│ • Camera            │
│                     │
│ 2:00 PM             │
│ Picnic lunch with   │
│ glacier view        │
│                     │
│ 6:00 PM             │
│ Return to lodge     │
│                     │
│ [View on Map]       │
│                     │
├─────────────────────┤
│                     │
│ (Continue for all   │
│  7 days...)         │
│                     │
└─────────────────────┘
```

**Traveler Itinerary Features**:
- Beautiful photos for each day
- Timeline format
- What to bring lists
- Difficulty indicators
- Map integration
- Downloadable PDF
- Offline access

---

### 4.4 TRAVELER CHECKLIST

```
SCREEN: Pre-Departure Checklist

┌─────────────────────┐
│ [←] CHECKLIST       │
│ 6 of 10 complete    │
├─────────────────────┤
│                     │
│ 60% ████████░░░     │
│                     │
│ BEFORE DEPARTURE    │
│                     │
│ ✓ Book flights      │
│   Completed May 2   │
│                     │
│ ✓ Travel insurance  │
│   Completed May 3   │
│                     │
│ ✓ Passport valid    │
│   Checked May 4     │
│                     │
│ ⚠ Upload passport   │
│   copy              │
│   [Upload Now]      │
│                     │
│ ⚠ Emergency contact │
│   [Add Contact]     │
│                     │
│ ☐ Pack hiking boots │
│   [Mark Done]       │
│                     │
│ ☐ Download offline  │
│   maps              │
│   [Download]        │
│                     │
│ ☐ Check weather     │
│   [View Forecast]   │
│                     │
├─────────────────────┤
│ PACKING LIST        │
│                     │
│ Essential Gear      │
│ ☐ Hiking boots      │
│ ☐ Backpack (30L)    │
│ ☐ Rain jacket       │
│ ☐ Water bottle      │
│ ☐ Headlamp          │
│                     │
│ Clothing            │
│ ☐ Thermal base layer│
│ ☐ Fleece jacket     │
│ ☐ Hiking pants (2)  │
│ ☐ Warm hat          │
│ ☐ Gloves            │
│                     │
│ [View Full List]    │
│                     │
└─────────────────────┘
```

---

### 4.5 TRAVELER GROUP VIEW

```
SCREEN: Your Group

┌─────────────────────┐
│ [←] YOUR GROUP      │
│ 12 travelers        │
├─────────────────────┤
│                     │
│ YOUR GUIDE          │
│                     │
│ ┌─────────────────┐ │
│ │    [Photo]      │ │
│ │                 │ │
│ │  João Silva     │ │
│ │  Lead Guide     │ │
│ │                 │ │
│ │  15 years exp.  │ │
│ │  200+ expeditions│ │
│ │                 │ │
│ │  [WhatsApp]     │ │
│ └─────────────────┘ │
│                     │
│ FELLOW TRAVELERS    │
│                     │
│ ┌─────────────────┐ │
│ │ 👤 Ana S.       │ │
│ │ São Paulo, BR   │ │
│ └─────────────────┘ │
│                     │
│ ┌─────────────────┐ │
│ │ 👤 Carlos M.    │ │
│ │ Rio de Janeiro  │ │
│ └─────────────────┘ │
│                     │
│ ... (10 more)       │
│                     │
│ [Join Group WhatsApp]│
│                     │
└─────────────────────┘
```

---

### 4.6 POST-EXPEDITION EXPERIENCE

```
AFTER EXPEDITION ENDS
↓
PUSH NOTIFICATION: "Thank you for an amazing journey!"
↓
SCREEN: Expedition Complete

┌─────────────────────┐
│  [Beautiful photo]  │
│  from expedition    │
│                     │
│ PATAGONIA TREK      │
│ May 15-22, 2026     │
│                     │
│ What an incredible  │
│ adventure! 🏔        │
│                     │
├─────────────────────┤
│                     │
│ YOUR EXPEDITION     │
│ ALBUM               │
│                     │
│ [Photo][Photo][Photo]│
│ [Photo][Photo][Photo]│
│                     │
│ 127 photos & videos │
│                     │
│ [View Album]        │
│ [Download All]      │
│                     │
├─────────────────────┤
│                     │
│ SHARE YOUR STORY    │
│                     │
│ How was your        │
│ experience?         │
│                     │
│ [Leave Review]      │
│                     │
├─────────────────────┤
│                     │
│ NEXT ADVENTURE?     │
│                     │
│ ┌─────────────────┐ │
│ │ Amazon Expedition│ │
│ │ June 3-10       │ │
│ │ [Learn More]    │ │
│ └─────────────────┘ │
│                     │
│ [View All Trips]    │
│                     │
└─────────────────────┘
```

---

## 5. OPERATIONAL PARTNER FLOW

**Role**: External service providers (accommodations, transport, etc.)  
**Primary Device**: Mixed  
**Experience Goal**: Simple, focused access to relevant expeditions

---

### 5.1 PARTNER DASHBOARD

```
LOGIN as Partner
↓
SCREEN: Partner Dashboard

┌─────────────────────────────────────────────────────┐
│ Welcome, Patagonia Lodge                             │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ UPCOMING RESERVATIONS                                │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Patagonia Trek                                   │ │
│ │ Check-in: May 15 · Check-out: May 22            │ │
│ │ Guests: 12 (6 double rooms)                     │ │
│ │ Status: ✓ Confirmed                             │ │
│ │                                                  │ │
│ │ Special Requests:                                │ │
│ │ • 2 vegetarian meals                             │ │
│ │ • Early breakfast (6:00 AM)                      │ │
│ │ • Ground floor room (1 guest)                    │ │
│ │                                                  │ │
│ │ [View Details] [Contact Agency] [Mark Complete] │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Patagonia Trek                                   │ │
│ │ Check-in: Jun 12 · Check-out: Jun 19            │ │
│ │ Guests: 15 (8 rooms)                            │ │
│ │ Status: ⏳ Pending confirmation                  │ │
│ │                                                  │ │
│ │ [Confirm Availability] [Decline] [Contact]      │ │
│ └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┐
│ CALENDAR                 │ DOCUMENTS                │
│                          │                          │
│ May 2026                 │ Invoice #PT-2026-05      │
│ [Monthly view]           │ [Download]               │
│                          │                          │
│ Bookings marked          │ W-9 Form                 │
│ on calendar              │ [Upload]                 │
│                          │                          │
│ [View Full Calendar]     │ [All Documents]          │
└──────────────────────────┴──────────────────────────┘
```

---

## NAVIGATION ARCHITECTURE

### Desktop Sidebar Structure
```
┌──────────────────┐
│ [LOGO]           │
├──────────────────┤
│ 🏠 Dashboard     │
│ 👥 CRM           │
│ 🗺  Expeditions  │
│ ⚙  Operations    │
│ 💰 Financial     │
│ 📸 Content       │
│ 📊 Reports       │
│ 👤 Team          │
│ ⚙  Settings      │
├──────────────────┤
│ 💬 WhatsApp      │
│ 🔔 Notifications │
├──────────────────┤
│ [Profile] [Help] │
└──────────────────┘
```

### Mobile Bottom Navigation

```
┌────────────────────────┐
│ [Home] [Tasks] [Exp] [More] │
└────────────────────────┘
```

**Context-Aware Navigation**:
- Administrator: Full sidebar
- Operator: Task-focused
- Guide: Expedition-focused
- Traveler: Portal-specific
- Partner: Limited, focused view

---

## COMPLETE USER FLOW SUMMARY

✅ **5 User Roles Fully Mapped**
1. Administrator (strategic oversight)
2. Operator (daily execution)
3. Guide (field operations)
4. Traveler (premium experience)
5. Partner (external coordination)

✅ **Key Screens Documented**
- 40+ unique screens
- Role-specific dashboards
- Mobile-optimized interfaces
- Desktop power-user views

✅ **Interaction Patterns Defined**
- Drag & drop workflows
- Quick actions
- Bulk operations
- Contextual menus
- Progressive disclosure

✅ **Experience Principles Applied**
- Premium aesthetic
- Zero friction
- Task clarity
- Beautiful design
- Fast performance
- Offline capability

---

This comprehensive user flow ensures every role has an optimized experience tailored to their needs and device preferences while maintaining visual and functional consistency across the platform.
