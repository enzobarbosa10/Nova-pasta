# UX ARCHITECTURE
## Premium Expedition Management System

**Brand Positioning**: The definitive operating system for premium expedition brands  
**Experience Philosophy**: Modern enterprise software meets premium lifestyle product

---

## DESIGN PRINCIPLES

### Core Values
- **Sophistication**: Every interaction feels refined and intentional
- **Control**: Complete visibility and command over operations
- **Fluidity**: Seamless transitions, zero friction
- **Exclusivity**: Premium aesthetic that reflects brand positioning
- **Organization**: Absolute clarity and structure
- **Modern Exploration**: Inspire adventure while maintaining professionalism

### Design References
- **Linear**: Clean, fast, keyboard-first navigation
- **Notion**: Flexible, powerful information architecture
- **Airbnb**: Emotional connection, beautiful imagery
- **Stripe**: Dashboard clarity, data visualization
- **Arc**: Modern UI patterns, thoughtful interactions
- **Apple**: Polish, attention to detail, premium feel

---

## INFORMATION ARCHITECTURE

### Primary User Roles
1. **Agency Administrator** - Strategic oversight and management
2. **Internal Operator** - Daily operational execution
3. **Guide** - Field operations and expedition leadership
4. **Traveler/Client** - Premium customer experience
5. **Operational Partners** - External service providers

### Application States
```
UNAUTHENTICATED
├── Landing
├── Login
├── Register
└── Password Recovery

AUTHENTICATED
├── Onboarding (first-time)
├── Dashboard (role-specific)
├── Core Modules
│   ├── CRM
│   ├── Expeditions
│   ├── Operations
│   ├── Financial
│   ├── Content
│   └── Reports
└── Settings
```

---

## CORE MODULE STRUCTURE

### 1. CRM Module
**Purpose**: Manage leads and convert to travelers

**Screens**:
- Pipeline View (Kanban)
- Lead Detail
- Follow-up Timeline
- Conversion Analytics
- Communication History

**Key Actions**:
- Create lead
- Move through stages
- Schedule follow-up
- Convert to traveler
- Track interactions

### 2. Expeditions Module
**Purpose**: Plan, configure, and manage expeditions

**Screens**:
- Expedition Calendar
- Expedition Detail
- Capacity Management
- Itinerary Builder
- Traveler List
- Logistics Dashboard

**Key Actions**:
- Create expedition
- Set capacity
- Build itinerary
- Assign guide
- Manage logistics
- Track occupancy

### 3. Operations Module
**Purpose**: Execute daily operations

**Screens**:
- Operations Dashboard
- Checklist Management
- Task Board
- Incident Log
- Status Timeline

**Key Actions**:
- Complete checklist items
- Update status
- Log incidents
- Confirm bookings
- Approve operations

### 4. Financial Module
**Purpose**: Track revenue and profitability

**Screens**:
- Financial Dashboard
- Payment Tracking
- Expedition Profitability
- Cash Flow
- Revenue Analytics

**Key Actions**:
- View payments
- Calculate margins
- Track expenses
- Generate reports

### 5. Content Module
**Purpose**: Organize media and testimonials

**Screens**:
- Media Bank (Grid View)
- Album Management
- Testimonials
- Media Upload
- Content Organization

**Key Actions**:
- Upload media
- Tag content
- Create albums
- Manage testimonials
- Share content

### 6. Reports Module
**Purpose**: Strategic insights and analytics

**Screens**:
- Analytics Dashboard
- Conversion Funnel
- Occupancy Report
- CAC Calculator
- Referral Tracking

**Key Actions**:
- Generate reports
- Export data
- Compare periods
- Track KPIs

---

## NAVIGATION STRUCTURE

### Desktop Layout

```
┌─────────────────────────────────────────────────────┐
│ TOPBAR                                               │
│ [Logo] [Global Search] [Quick Actions] [Notifications] [Profile] │
├──────────┬──────────────────────────────────────────┤
│          │                                           │
│ SIDEBAR  │         MAIN CONTENT AREA                 │
│          │                                           │
│ Dashboard│                                           │
│ CRM      │                                           │
│ Expeditions                                          │
│ Operations                                           │
│ Financial│                                           │
│ Content  │                                           │
│ Reports  │                                           │
│ Team     │                                           │
│ Settings │                                           │
│          │                                           │
└──────────┴──────────────────────────────────────────┘
```

### Mobile Layout

```
┌─────────────────────┐
│ TOPBAR              │
│ [Menu] [Logo] [Profile] │
├─────────────────────┤
│                     │
│  MAIN CONTENT       │
│                     │
│                     │
│                     │
│                     │
│                     │
│                     │
├─────────────────────┤
│ BOTTOM NAV          │
│ [Home][Ops][Expeditions][More] │
└─────────────────────┘
```

---

## RESPONSIVE BEHAVIOR

### Breakpoints
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px
- **Large Desktop**: > 1440px

### Mobile-First Components
- Touch-optimized hit areas (minimum 44px)
- Swipe gestures for navigation
- Bottom sheet modals
- Floating action buttons
- Simplified forms
- Progressive disclosure

### Priority by Role

**Mobile-Optimized**:
- Guide interface (80% mobile usage)
- Operator tasks (60% mobile usage)
- Traveler portal (90% mobile usage)

**Desktop-Optimized**:
- Administrator dashboard
- Financial reports
- CRM pipeline
- Content organization

---

## COLOR SYSTEM

### Semantic Colors
- **Primary**: Brand color (configurable per agency)
- **Success**: Confirmation, completion
- **Warning**: Attention needed
- **Error**: Critical issues
- **Neutral**: Text, backgrounds, borders

### Dark Mode Support
- Full dark mode implementation
- System preference detection
- Manual toggle available

---

## TYPOGRAPHY SCALE

### Hierarchy
- **Display**: Hero sections, large numbers
- **Heading 1**: Page titles
- **Heading 2**: Section titles
- **Heading 3**: Card titles
- **Body**: Default text
- **Caption**: Meta information
- **Label**: Form labels

### Fonts
- **Primary**: Inter (sans-serif)
- **Monospace**: JetBrains Mono (data, codes)
- **Optional**: Custom brand font support

---

## COMPONENT LIBRARY

### Core Components
- Button (primary, secondary, ghost, danger)
- Input (text, number, date, select, multiselect)
- Card (standard, elevated, bordered)
- Modal (center, drawer, bottom sheet)
- Toast (success, error, info, warning)
- Avatar (user, group)
- Badge (status, count, label)
- Progress (linear, circular, ring)
- Table (sortable, filterable, expandable)
- Calendar (month, week, day)
- Kanban (draggable columns and cards)
- Timeline (vertical, horizontal)
- Stats Card (metric display)
- Empty State (no data, error)
- Loading (skeleton, spinner, progress)

### Complex Components
- Global Search (command palette)
- Rich Text Editor
- Image Upload (drag & drop, preview)
- Date Range Picker
- Capacity Meter
- Traveler Card
- Expedition Card
- Lead Card
- Activity Feed
- Comment Thread

---

## ANIMATION PRINCIPLES

### Timing Functions
- **Ease-out**: Elements entering (0.2s)
- **Ease-in**: Elements exiting (0.15s)
- **Spring**: Interactive elements
- **Linear**: Loading states

### Motion Guidelines
- Subtle, not distracting
- Purposeful, not decorative
- Fast, not slow
- Smooth, not janky

### Key Animations
- Page transitions (fade + slide)
- Modal entrance (scale + fade)
- Card hover (elevation + scale)
- Button press (scale down)
- Loading states (skeleton shimmer)
- Success feedback (checkmark animation)
- Error shake
- Drag and drop (ghost + snap)

---

## DATA VISUALIZATION

### Dashboard Metrics
- **Card Style**: Clean, prominent numbers
- **Trend Indicators**: Up/down arrows with percentage
- **Mini Charts**: Sparklines for quick trends
- **Color Coding**: Green (good), Red (attention), Gray (neutral)

### Chart Types
- Line charts (trends over time)
- Bar charts (comparisons)
- Donut charts (proportions)
- Area charts (cumulative data)
- Heatmaps (calendar data)

### Table Design
- Sortable columns
- Filterable data
- Expandable rows
- Inline actions
- Pagination
- Row selection

---

## ACCESSIBILITY

### WCAG 2.1 Level AA
- Sufficient color contrast
- Keyboard navigation
- Focus indicators
- Screen reader support
- Alternative text
- Form labels
- Error messaging

### Keyboard Shortcuts
- `Cmd/Ctrl + K`: Global search
- `Cmd/Ctrl + N`: New (contextual)
- `Cmd/Ctrl + S`: Save
- `Esc`: Close modal
- `?`: Show shortcuts help

---

## PERFORMANCE TARGETS

### Load Times
- First Contentful Paint: < 1s
- Time to Interactive: < 2s
- Route transitions: < 300ms

### Optimization
- Code splitting by route
- Lazy loading images
- Virtualized lists
- Debounced search
- Optimistic updates
- Cached API responses

---

## ERROR HANDLING

### Error States
- **Inline Validation**: Real-time form feedback
- **Toast Notifications**: Non-blocking alerts
- **Error Pages**: 404, 500, offline
- **Retry Actions**: Failed requests
- **Offline Mode**: Local data access

### Empty States
- Illustrative graphics
- Clear explanation
- Primary action (CTA)
- Secondary guidance

---

## NOTIFICATION SYSTEM

### Types
- **System**: Platform updates, maintenance
- **Operational**: Task completion, deadlines
- **Social**: Comments, mentions
- **Financial**: Payment confirmations
- **Marketing**: Tips, feature announcements

### Channels
- In-app (notification center)
- Email (digest, urgent)
- Push (mobile)
- WhatsApp (optional integration)

### Priority Levels
- **Urgent**: Requires immediate action
- **High**: Important, review soon
- **Normal**: Standard notification
- **Low**: Can be batched

---

## SEARCH & FILTERS

### Global Search
- Universal search bar (Cmd/Ctrl + K)
- Search across all entities
- Keyboard navigation
- Recent searches
- Suggested results
- Quick actions

### Entity-Specific Filters
- **Expeditions**: Date, status, guide, capacity
- **Leads**: Stage, source, priority, date
- **Travelers**: Status, expedition, tags
- **Content**: Type, date, expedition, tags

### Advanced Search
- Multiple criteria
- Saved searches
- Export results

---

## PERMISSION SYSTEM

### Role Hierarchy
```
SUPER ADMIN (Agency Owner)
├── ADMIN (Manager)
│   ├── OPERATOR (Staff)
│   └── GUIDE (Field)
└── PARTNER (External)
```

### Permission Levels
- **View**: Read-only access
- **Edit**: Modify existing
- **Create**: Add new items
- **Delete**: Remove items
- **Manage**: Full control + settings

### Feature Access Matrix
See detailed permission matrix in ROLE_PERMISSIONS.md

---

## INTEGRATION POINTS

### External Services
- **WhatsApp Business API**: Communication
- **Payment Gateways**: Stripe, PayPal
- **Email Service**: Transactional emails
- **SMS Gateway**: Notifications
- **Cloud Storage**: Media hosting
- **Analytics**: Google Analytics, Mixpanel
- **CRM Integration**: Optional sync

### API Architecture
- RESTful API
- JWT authentication
- Rate limiting
- Webhook support
- Webhook events for automations

---

## SECURITY & PRIVACY

### Authentication
- Email + Password
- Two-factor authentication (optional)
- SSO support (enterprise)
- Session management
- Password requirements

### Data Protection
- Encrypted at rest
- HTTPS only
- GDPR compliant
- Data export
- Account deletion
- Audit logs

---

## LOCALIZATION

### Language Support
- Portuguese (primary)
- English
- Spanish
- RTL support (future)

### Regional Settings
- Date format
- Time format
- Currency
- Number format
- Time zones

---

## ONBOARDING STRATEGY

### First-Time Experience
- Progressive disclosure
- Contextual tips
- Interactive tutorials
- Sample data
- Quick wins
- Milestone celebrations

### Role-Specific Onboarding
Each role gets customized onboarding flow
(See detailed flows in USER_FLOWS.md)

---

This architecture ensures a cohesive, premium experience across all touchpoints while maintaining operational efficiency and scalability.
