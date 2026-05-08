# VISUAL FLOW DIAGRAMS
## Complete System Architecture & User Journeys

---

## 1. SYSTEM ARCHITECTURE OVERVIEW

```mermaid
graph TB
    subgraph "Frontend Layer"
        WEB[Web Application<br/>React + TypeScript]
        MOBILE[Mobile App<br/>React Native]
        PORTAL[Traveler Portal<br/>Branded Experience]
    end
    
    subgraph "Backend Layer"
        API[REST API<br/>Laravel]
        WS[WebSocket Server<br/>Real-time Updates]
        QUEUE[Job Queue<br/>Background Tasks]
    end
    
    subgraph "Data Layer"
        DB[(PostgreSQL<br/>Primary Database)]
        REDIS[(Redis<br/>Cache & Sessions)]
        S3[(Cloud Storage<br/>Media Files)]
    end
    
    subgraph "External Services"
        WHATSAPP[WhatsApp<br/>Business API]
        STRIPE[Stripe<br/>Payments]
        EMAIL[SendGrid<br/>Transactional Email]
        SMS[Twilio<br/>SMS Notifications]
    end
    
    WEB --> API
    MOBILE --> API
    PORTAL --> API
    
    API --> DB
    API --> REDIS
    API --> S3
    API --> QUEUE
    
    WS --> REDIS
    
    QUEUE --> WHATSAPP
    QUEUE --> STRIPE
    QUEUE --> EMAIL
    QUEUE --> SMS
    
    style WEB fill:#3b82f6,stroke:#1e40af,color:#fff
    style MOBILE fill:#3b82f6,stroke:#1e40af,color:#fff
    style PORTAL fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style API fill:#10b981,stroke:#059669,color:#fff
    style DB fill:#ef4444,stroke:#dc2626,color:#fff
```

---

## 2. INFORMATION ARCHITECTURE

```mermaid
graph TD
    ROOT[Platform Root]
    
    ROOT --> AUTH[Authentication]
    ROOT --> ADMIN[Administrator]
    ROOT --> OPS[Operator]
    ROOT --> GUIDE[Guide]
    ROOT --> TRAVELER[Traveler]
    ROOT --> PARTNER[Partner]
    
    ADMIN --> DASH_A[Dashboard]
    ADMIN --> CRM[CRM Pipeline]
    ADMIN --> EXP_A[Expeditions]
    ADMIN --> OPS_A[Operations]
    ADMIN --> FIN[Financial]
    ADMIN --> CONTENT[Content Bank]
    ADMIN --> REPORTS[Analytics & Reports]
    ADMIN --> TEAM[Team Management]
    ADMIN --> SETTINGS[Settings]
    
    CRM --> LEADS[Leads List]
    CRM --> PIPELINE[Kanban Pipeline]
    CRM --> LEAD_DETAIL[Lead Detail]
    
    EXP_A --> EXP_LIST[Expeditions List]
    EXP_A --> EXP_CREATE[Create Expedition]
    EXP_A --> EXP_DETAIL[Expedition Detail]
    
    EXP_DETAIL --> EXP_OVERVIEW[Overview]
    EXP_DETAIL --> EXP_TRAVELERS[Travelers]
    EXP_DETAIL --> EXP_ITINERARY[Itinerary]
    EXP_DETAIL --> EXP_LOGISTICS[Logistics]
    EXP_DETAIL --> EXP_OPS[Operations]
    EXP_DETAIL --> EXP_FIN[Financial]
    
    OPS --> TASKS[My Tasks]
    OPS --> DASH_O[Operations Dashboard]
    
    GUIDE --> ACTIVE_EXP[Active Expedition]
    GUIDE --> UPCOMING[Upcoming]
    GUIDE --> HISTORY[Past Expeditions]
    
    TRAVELER --> DASH_T[My Expedition]
    TRAVELER --> ITINERARY_T[Itinerary]
    TRAVELER --> CHECKLIST[Pre-Departure Checklist]
    TRAVELER --> DOCS[Documents]
    TRAVELER --> GROUP[My Group]
    TRAVELER --> ALBUM[Photo Album]
    
    style ROOT fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style ADMIN fill:#3b82f6,stroke:#1e40af,color:#fff
    style OPS fill:#10b981,stroke:#059669,color:#fff
    style GUIDE fill:#f59e0b,stroke:#d97706,color:#fff
    style TRAVELER fill:#ec4899,stroke:#db2777,color:#fff
    style PARTNER fill:#6366f1,stroke:#4f46e5,color:#fff
```

---

## 3. LEAD TO TRAVELER CONVERSION FLOW

```mermaid
flowchart TD
    START([New Lead Captured]) --> AUTO1[Auto-Create Lead Record]
    AUTO1 --> AUTO2[Assign to Team Member]
    AUTO2 --> AUTO3[Send Welcome Message]
    AUTO3 --> AUTO4[Create Follow-up Task]
    
    AUTO4 --> CONTACT{First Contact<br/>Made?}
    
    CONTACT -->|No Response<br/>48hrs| REMINDER[Send Follow-up #2]
    REMINDER --> CONTACT
    
    CONTACT -->|Yes| PROPOSAL{Proposal<br/>Sent?}
    
    PROPOSAL -->|Interested| NEGOTIATE[Negotiation Stage]
    PROPOSAL -->|Not Ready| NURTURE[Nurture Campaign]
    
    NEGOTIATE --> WON{Deal<br/>Won?}
    
    WON -->|Yes| CONVERT[Convert to Traveler]
    WON -->|No| LOST[Mark as Lost]
    
    CONVERT --> PAYMENT[Generate Invoice]
    PAYMENT --> PAY_RECEIVED{Payment<br/>Confirmed?}
    
    PAY_RECEIVED -->|Yes| PORTAL[Grant Portal Access]
    PAY_RECEIVED -->|No| PAY_REMINDER[Payment Reminders]
    PAY_REMINDER --> PAY_RECEIVED
    
    PORTAL --> ONBOARD[Traveler Onboarding]
    ONBOARD --> DOCS_REQ[Request Documents]
    DOCS_REQ --> PRE_DEP[Pre-Departure Sequence]
    PRE_DEP --> READY([Ready for Expedition])
    
    style START fill:#10b981,stroke:#059669,color:#fff
    style CONVERT fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style PORTAL fill:#3b82f6,stroke:#1e40af,color:#fff
    style READY fill:#10b981,stroke:#059669,color:#fff
```

---

## 4. EXPEDITION LIFECYCLE

```mermaid
flowchart TB
    CREATE([Expedition Created]) --> SETUP[Setup Phase]
    
    SETUP --> CONFIG[Configure Details]
    CONFIG --> ITINERARY[Build Itinerary]
    ITINERARY --> LOGISTICS[Setup Logistics]
    LOGISTICS --> GUIDE_ASSIGN[Assign Guide]
    GUIDE_ASSIGN --> PUBLISH{Publish?}
    
    PUBLISH -->|Draft| DRAFT_STATE[Draft State]
    PUBLISH -->|Public| OPEN_BOOKINGS[Open for Bookings]
    
    DRAFT_STATE --> OPEN_BOOKINGS
    
    OPEN_BOOKINGS --> BOOKING_PHASE[Booking Phase]
    
    BOOKING_PHASE --> CHECK_CAP{Capacity<br/>Reached?}
    CHECK_CAP -->|No| BOOKING_PHASE
    CHECK_CAP -->|Yes| FULL[Fully Booked]
    
    FULL --> PRE_DEP[Pre-Departure Phase<br/>60-0 days]
    
    PRE_DEP --> D60[60 Days: Logistics Confirmation]
    D60 --> D30[30 Days: Send Travel Guide]
    D30 --> D14[14 Days: Create WhatsApp Group]
    D14 --> D7[7 Days: Final Details]
    D7 --> D1[1 Day: Final Checklist]
    
    D1 --> DEPARTURE([Departure Day])
    
    DEPARTURE --> ACTIVE[Active Expedition<br/>Daily Updates]
    
    ACTIVE --> CHECK_COMPLETE{Expedition<br/>Complete?}
    CHECK_COMPLETE -->|In Progress| ACTIVE
    CHECK_COMPLETE -->|Yes| RETURN([Return Day])
    
    RETURN --> POST[Post-Expedition Phase]
    
    POST --> P1[Day +1: Thank You Email]
    P1 --> P3[Day +3: Feedback Survey]
    P3 --> P7[Day +7: Send Photo Album]
    P7 --> P14[Day +14: Future Opportunities]
    P14 --> P30[Day +30: Archive & Analytics]
    
    P30 --> COMPLETE([Completed & Archived])
    
    style CREATE fill:#10b981,stroke:#059669,color:#fff
    style DEPARTURE fill:#f59e0b,stroke:#d97706,color:#fff
    style ACTIVE fill:#ef4444,stroke:#dc2626,color:#fff
    style RETURN fill:#f59e0b,stroke:#d97706,color:#fff
    style COMPLETE fill:#6366f1,stroke:#4f46e5,color:#fff
```

---

## 5. ADMINISTRATOR DAILY WORKFLOW

```mermaid
flowchart LR
    START([Start Day]) --> LOGIN[Login]
    LOGIN --> DASHBOARD[View Dashboard]
    
    DASHBOARD --> METRICS[Review Key Metrics]
    METRICS --> PIPELINE[Check CRM Pipeline]
    PIPELINE --> EXPEDITIONS[Review Expeditions]
    
    EXPEDITIONS --> CHECK_TASKS{Urgent<br/>Tasks?}
    
    CHECK_TASKS -->|Yes| HANDLE_TASKS[Handle Priority Tasks]
    HANDLE_TASKS --> CHECK_LEADS{New<br/>Leads?}
    
    CHECK_TASKS -->|No| CHECK_LEADS
    
    CHECK_LEADS -->|Yes| REVIEW_LEADS[Review & Assign]
    REVIEW_LEADS --> CHECK_PAYMENTS
    
    CHECK_LEADS -->|No| CHECK_PAYMENTS{Pending<br/>Payments?}
    
    CHECK_PAYMENTS -->|Yes| REVIEW_PAY[Review & Follow-up]
    REVIEW_PAY --> CHECK_OPS
    
    CHECK_PAYMENTS -->|No| CHECK_OPS{Operations<br/>Issues?}
    
    CHECK_OPS -->|Yes| RESOLVE[Resolve Issues]
    RESOLVE --> REPORTS
    
    CHECK_OPS -->|No| REPORTS[Review Reports]
    
    REPORTS --> STRATEGY[Strategic Planning]
    STRATEGY --> END([End Day])
    
    style START fill:#10b981,stroke:#059669,color:#fff
    style DASHBOARD fill:#3b82f6,stroke:#1e40af,color:#fff
    style END fill:#6366f1,stroke:#4f46e5,color:#fff
```

---

## 6. GUIDE EXPEDITION FLOW

```mermaid
flowchart TD
    ASSIGN([Assigned to Expedition]) --> PREP[Preparation Phase]
    
    PREP --> REVIEW[Review Expedition Details]
    REVIEW --> TRAVELERS[Study Traveler Profiles]
    TRAVELERS --> LOGISTICS[Review Logistics]
    LOGISTICS --> BRIEF[Attend Pre-Departure Briefing]
    
    BRIEF --> READY[Ready for Departure]
    
    READY --> DEP_DAY([Departure Day])
    
    DEP_DAY --> CHECKIN[Traveler Check-in]
    CHECKIN --> BRIEF_TRAVELERS[Brief Group]
    BRIEF_TRAVELERS --> START_EXP[Start Expedition]
    
    START_EXP --> DAILY[Daily Routine]
    
    DAILY --> MORNING[Morning Briefing]
    MORNING --> ACTIVITIES[Execute Activities]
    ACTIVITIES --> LOG[Log Daily Report]
    LOG --> PHOTOS[Upload Photos]
    PHOTOS --> EVENING[Evening Debrief]
    
    EVENING --> CHECK_ISSUES{Any<br/>Issues?}
    
    CHECK_ISSUES -->|Yes| REPORT_ISSUE[Report to HQ]
    REPORT_ISSUE --> RESOLVE_ISSUE[Resolve Issue]
    RESOLVE_ISSUE --> NEXT_DAY
    
    CHECK_ISSUES -->|No| NEXT_DAY{Last<br/>Day?}
    
    NEXT_DAY -->|No| DAILY
    NEXT_DAY -->|Yes| RETURN([Return Day])
    
    RETURN --> FINAL_CHECK[Final Check-out]
    FINAL_CHECK --> FAREWELL[Farewell to Group]
    FAREWELL --> FINAL_REPORT[Submit Final Report]
    FINAL_REPORT --> COMPLETE([Expedition Complete])
    
    style ASSIGN fill:#10b981,stroke:#059669,color:#fff
    style DEP_DAY fill:#f59e0b,stroke:#d97706,color:#fff
    style DAILY fill:#3b82f6,stroke:#1e40af,color:#fff
    style RETURN fill:#f59e0b,stroke:#d97706,color:#fff
    style COMPLETE fill:#6366f1,stroke:#4f46e5,color:#fff
```

---

## 7. TRAVELER JOURNEY

```mermaid
flowchart TB
    START([Interested in Expedition]) --> INQUIRY[Submit Inquiry]
    
    INQUIRY --> CONTACT[Receive Contact]
    CONTACT --> INFO[Receive Information]
    INFO --> DECIDE{Want to<br/>Book?}
    
    DECIDE -->|No| NURTURE[Stay on Nurture List]
    DECIDE -->|Yes| BOOK[Confirm Booking]
    
    BOOK --> INVOICE[Receive Invoice]
    INVOICE --> PAYMENT[Make Payment]
    
    PAYMENT --> CONFIRM[Booking Confirmed]
    CONFIRM --> PORTAL[Access Portal]
    
    PORTAL --> EXPLORE[Explore Expedition Details]
    EXPLORE --> DOCS[Upload Documents]
    DOCS --> CHECKLIST[Complete Checklist]
    CHECKLIST --> WAIT[Countdown to Departure]
    
    WAIT --> REMINDERS[Receive Reminders]
    REMINDERS --> GROUP[Join WhatsApp Group]
    GROUP --> FINAL_PREP[Final Preparations]
    
    FINAL_PREP --> DEPARTURE([Departure Day])
    
    DEPARTURE --> TRAVEL[Travel to Destination]
    TRAVEL --> EXPEDITION[Experience Expedition]
    
    EXPEDITION --> ENJOY[Daily Activities]
    ENJOY --> PHOTOS[Share Photos]
    PHOTOS --> NEXT_DAY{Last<br/>Day?}
    
    NEXT_DAY -->|No| ENJOY
    NEXT_DAY -->|Yes| RETURN([Return Home])
    
    RETURN --> THANKS[Receive Thank You]
    THANKS --> SURVEY[Complete Survey]
    SURVEY --> ALBUM[Receive Photo Album]
    ALBUM --> REVIEW[Leave Review]
    REVIEW --> SHARE[Share Experience]
    SHARE --> FUTURE[Future Expeditions?]
    
    FUTURE -->|Yes| START
    FUTURE -->|No| ALUMNI[Join Alumni Community]
    
    style START fill:#10b981,stroke:#059669,color:#fff
    style PORTAL fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style DEPARTURE fill:#f59e0b,stroke:#d97706,color:#fff
    style EXPEDITION fill:#ec4899,stroke:#db2777,color:#fff
    style RETURN fill:#f59e0b,stroke:#d97706,color:#fff
    style ALUMNI fill:#6366f1,stroke:#4f46e5,color:#fff
```

---

## 8. PAYMENT PROCESSING FLOW

```mermaid
flowchart TD
    START([Traveler Confirmed]) --> INVOICE[Generate Invoice]
    
    INVOICE --> SEND[Send Payment Link]
    SEND --> WAIT{Payment<br/>Received?}
    
    WAIT -->|Yes| WEBHOOK[Stripe Webhook]
    WAIT -->|Waiting| REMINDER[Send Reminders]
    REMINDER --> WAIT
    
    WEBHOOK --> VERIFY[Verify Payment]
    VERIFY --> UPDATE[Update Records]
    UPDATE --> RECEIPT[Generate Receipt]
    
    RECEIPT --> NOTIFY_TRAVELER[Email Receipt to Traveler]
    NOTIFY_TRAVELER --> NOTIFY_TEAM[Notify Operations Team]
    
    NOTIFY_TEAM --> CHECK_TYPE{Full<br/>Payment?}
    
    CHECK_TYPE -->|Yes| FULL[Mark as Paid in Full]
    CHECK_TYPE -->|No| PARTIAL[Mark as Deposit Paid]
    
    FULL --> PORTAL[Grant Full Portal Access]
    PORTAL --> WHATSAPP[Add to WhatsApp Group]
    WHATSAPP --> DOCS[Trigger Document Collection]
    DOCS --> COMPLETE([Payment Complete])
    
    PARTIAL --> SCHEDULE[Schedule Final Payment]
    SCHEDULE --> REMIND[Reminder Sequence]
    REMIND --> FINAL{Final<br/>Paid?}
    
    FINAL -->|Yes| FULL
    FINAL -->|No| OVERDUE{Overdue?}
    
    OVERDUE -->|Yes| ESCALATE[Escalate to Manager]
    OVERDUE -->|No| REMIND
    
    ESCALATE --> CONTACT[Personal Contact]
    CONTACT --> RESOLVE{Resolved?}
    
    RESOLVE -->|Yes| FULL
    RESOLVE -->|No| CANCEL[Cancel Booking]
    
    style START fill:#10b981,stroke:#059669,color:#fff
    style WEBHOOK fill:#3b82f6,stroke:#1e40af,color:#fff
    style COMPLETE fill:#10b981,stroke:#059669,color:#fff
    style CANCEL fill:#ef4444,stroke:#dc2626,color:#fff
```

---

## 9. AUTOMATION TRIGGER MAP

```mermaid
flowchart LR
    subgraph "Triggers"
        T1[New Lead]
        T2[Payment Received]
        T3[Document Uploaded]
        T4[Expedition Created]
        T5[60 Days Before]
        T6[30 Days Before]
        T7[7 Days Before]
        T8[Departure Day]
        T9[Daily During]
        T10[Return Day]
        T11[Post-Expedition]
    end
    
    subgraph "Automated Actions"
        A1[Welcome Message]
        A2[Assign Team Member]
        A3[Portal Access]
        A4[Receipt Generation]
        A5[Send Travel Guide]
        A6[Create WhatsApp Group]
        A7[Final Reminders]
        A8[Guide Briefing]
        A9[Daily Log Prompt]
        A10[Thank You Email]
        A11[Feedback Survey]
        A12[Photo Album]
    end
    
    T1 --> A1
    T1 --> A2
    T2 --> A3
    T2 --> A4
    T4 --> A2
    T5 --> A5
    T6 --> A5
    T7 --> A6
    T7 --> A7
    T8 --> A8
    T9 --> A9
    T10 --> A10
    T11 --> A11
    T11 --> A12
    
    style T1 fill:#10b981,stroke:#059669,color:#fff
    style T2 fill:#3b82f6,stroke:#1e40af,color:#fff
    style T8 fill:#f59e0b,stroke:#d97706,color:#fff
    style T10 fill:#ec4899,stroke:#db2777,color:#fff
```

---

## 10. DATA FLOW ARCHITECTURE

```mermaid
flowchart TD
    subgraph "User Actions"
        USER[User Interaction]
    end
    
    subgraph "Frontend"
        UI[UI Component]
        STATE[State Management<br/>Redux/Context]
        API_CLIENT[API Client]
    end
    
    subgraph "Backend"
        ROUTES[API Routes]
        CONTROLLER[Controller]
        SERVICE[Service Layer]
        VALIDATION[Validation]
    end
    
    subgraph "Data Layer"
        MODELS[Eloquent Models]
        DB[(Database)]
        CACHE[(Redis Cache)]
    end
    
    subgraph "Background"
        JOBS[Queued Jobs]
        EVENTS[Event System]
        NOTIFICATIONS[Notifications]
    end
    
    USER --> UI
    UI --> STATE
    STATE --> API_CLIENT
    API_CLIENT --> ROUTES
    
    ROUTES --> CONTROLLER
    CONTROLLER --> VALIDATION
    VALIDATION --> SERVICE
    
    SERVICE --> MODELS
    MODELS --> DB
    MODELS --> CACHE
    
    SERVICE --> EVENTS
    EVENTS --> JOBS
    JOBS --> NOTIFICATIONS
    
    NOTIFICATIONS -.-> USER
    
    CACHE -.Fast Read.-> SERVICE
    
    style USER fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style DB fill:#ef4444,stroke:#dc2626,color:#fff
    style JOBS fill:#f59e0b,stroke:#d97706,color:#fff
```

---

## 11. ROLE-BASED ACCESS CONTROL

```mermaid
flowchart TB
    subgraph "Roles"
        SUPER[Super Admin]
        ADMIN[Admin]
        OPERATOR[Operator]
        GUIDE[Guide]
        PARTNER[Partner]
        TRAVELER[Traveler]
    end
    
    subgraph "Permissions"
        P1[Manage Everything]
        P2[Manage Team]
        P3[View Financials]
        P4[Manage Expeditions]
        P5[Manage CRM]
        P6[Execute Operations]
        P7[Guide Expeditions]
        P8[View Own Data]
        P9[Provide Services]
    end
    
    SUPER --> P1
    
    ADMIN --> P2
    ADMIN --> P3
    ADMIN --> P4
    ADMIN --> P5
    ADMIN --> P6
    
    OPERATOR --> P4
    OPERATOR --> P5
    OPERATOR --> P6
    
    GUIDE --> P7
    GUIDE --> P8
    
    PARTNER --> P9
    PARTNER --> P8
    
    TRAVELER --> P8
    
    style SUPER fill:#ef4444,stroke:#dc2626,color:#fff
    style ADMIN fill:#3b82f6,stroke:#1e40af,color:#fff
    style OPERATOR fill:#10b981,stroke:#059669,color:#fff
    style GUIDE fill:#f59e0b,stroke:#d97706,color:#fff
    style TRAVELER fill:#ec4899,stroke:#db2777,color:#fff
```

---

## 12. NOTIFICATION ROUTING

```mermaid
flowchart LR
    EVENT([System Event]) --> DECIDE{Event Type}
    
    DECIDE -->|Critical| CRITICAL[Critical Path]
    DECIDE -->|Important| IMPORTANT[Important Path]
    DECIDE -->|Normal| NORMAL[Normal Path]
    
    CRITICAL --> SMS[SMS]
    CRITICAL --> PUSH[Push Notification]
    CRITICAL --> EMAIL[Email]
    CRITICAL --> APP[In-App Alert]
    
    IMPORTANT --> PUSH
    IMPORTANT --> EMAIL
    IMPORTANT --> APP
    
    NORMAL --> APP
    NORMAL --> EMAIL_DIGEST[Email Digest]
    
    SMS --> USER([User Receives])
    PUSH --> USER
    EMAIL --> USER
    APP --> USER
    EMAIL_DIGEST --> USER
    
    style EVENT fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style CRITICAL fill:#ef4444,stroke:#dc2626,color:#fff
    style IMPORTANT fill:#f59e0b,stroke:#d97706,color:#fff
    style USER fill:#10b981,stroke:#059669,color:#fff
```

---

## 13. MOBILE APP NAVIGATION

```mermaid
flowchart TB
    SPLASH([Splash Screen]) --> AUTH{Authenticated?}
    
    AUTH -->|No| LOGIN[Login Screen]
    AUTH -->|Yes| ROLE{User Role?}
    
    LOGIN --> ROLE
    
    ROLE -->|Admin| ADMIN_HOME[Admin Dashboard]
    ROLE -->|Operator| OPS_HOME[Operator Tasks]
    ROLE -->|Guide| GUIDE_HOME[Guide Expeditions]
    ROLE -->|Traveler| TRAV_HOME[Traveler Portal]
    
    subgraph "Admin Navigation"
        ADMIN_HOME
        ADMIN_CRM[CRM]
        ADMIN_EXP[Expeditions]
        ADMIN_OPS[Operations]
        ADMIN_REPORTS[Reports]
    end
    
    subgraph "Guide Navigation"
        GUIDE_HOME
        GUIDE_ACTIVE[Active Expedition]
        GUIDE_DAY[Daily Log]
        GUIDE_GROUP[Group Info]
        GUIDE_CONTACT[Contact HQ]
    end
    
    subgraph "Traveler Navigation"
        TRAV_HOME
        TRAV_ITINERARY[Itinerary]
        TRAV_CHECKLIST[Checklist]
        TRAV_GROUP[My Group]
        TRAV_DOCS[Documents]
        TRAV_ALBUM[Photos]
    end
    
    ADMIN_HOME --> ADMIN_CRM
    ADMIN_HOME --> ADMIN_EXP
    ADMIN_HOME --> ADMIN_OPS
    ADMIN_HOME --> ADMIN_REPORTS
    
    GUIDE_HOME --> GUIDE_ACTIVE
    GUIDE_ACTIVE --> GUIDE_DAY
    GUIDE_ACTIVE --> GUIDE_GROUP
    GUIDE_ACTIVE --> GUIDE_CONTACT
    
    TRAV_HOME --> TRAV_ITINERARY
    TRAV_HOME --> TRAV_CHECKLIST
    TRAV_HOME --> TRAV_GROUP
    TRAV_HOME --> TRAV_DOCS
    TRAV_HOME --> TRAV_ALBUM
    
    style SPLASH fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style ADMIN_HOME fill:#3b82f6,stroke:#1e40af,color:#fff
    style GUIDE_HOME fill:#f59e0b,stroke:#d97706,color:#fff
    style TRAV_HOME fill:#ec4899,stroke:#db2777,color:#fff
```

---

## 14. ERROR HANDLING FLOW

```mermaid
flowchart TD
    ERROR([Error Occurs]) --> TYPE{Error Type?}
    
    TYPE -->|Network| NETWORK[Network Error]
    TYPE -->|Validation| VALIDATION[Validation Error]
    TYPE -->|Server| SERVER[Server Error]
    TYPE -->|Client| CLIENT[Client Error]
    
    NETWORK --> RETRY{Auto<br/>Retry?}
    RETRY -->|Yes| ATTEMPT[Retry Request]
    ATTEMPT --> SUCCESS{Success?}
    SUCCESS -->|Yes| RESOLVE([Resolved])
    SUCCESS -->|No| OFFLINE[Offline Mode]
    OFFLINE --> QUEUE[Queue Actions]
    QUEUE --> SYNC[Sync When Online]
    
    RETRY -->|No| SHOW_NET[Show Network Error]
    SHOW_NET --> USER_RETRY{User<br/>Retry?}
    USER_RETRY -->|Yes| ATTEMPT
    USER_RETRY -->|No| CANCEL([Cancelled])
    
    VALIDATION --> HIGHLIGHT[Highlight Fields]
    HIGHLIGHT --> MESSAGE[Show Error Messages]
    MESSAGE --> USER_FIX[User Fixes Input]
    USER_FIX --> RESOLVE
    
    SERVER --> LOG[Log Error]
    LOG --> NOTIFY_TEAM[Notify Dev Team]
    NOTIFY_TEAM --> SHOW_500[Show Friendly Message]
    SHOW_500 --> SUPPORT{Contact<br/>Support?}
    SUPPORT -->|Yes| TICKET[Create Support Ticket]
    SUPPORT -->|No| CANCEL
    
    CLIENT --> LOG_CLIENT[Log to Console]
    LOG_CLIENT --> FALLBACK[Fallback UI]
    FALLBACK --> RESOLVE
    
    style ERROR fill:#ef4444,stroke:#dc2626,color:#fff
    style RESOLVE fill:#10b981,stroke:#059669,color:#fff
```

---

## 15. SEARCH & FILTER ARCHITECTURE

```mermaid
flowchart LR
    USER_INPUT[User Types Search] --> DEBOUNCE[Debounce 300ms]
    
    DEBOUNCE --> PARSE[Parse Query]
    PARSE --> BUILD[Build Search Criteria]
    
    BUILD --> CHECK_CACHE{In<br/>Cache?}
    
    CHECK_CACHE -->|Yes| RETURN_CACHE[Return Cached Results]
    CHECK_CACHE -->|No| SEARCH_DB[Search Database]
    
    SEARCH_DB --> ELASTICSEARCH{Use<br/>Elasticsearch?}
    
    ELASTICSEARCH -->|Yes| ES_SEARCH[Full-Text Search]
    ELASTICSEARCH -->|No| DB_SEARCH[SQL LIKE Query]
    
    ES_SEARCH --> RANK[Rank Results]
    DB_SEARCH --> RANK
    
    RANK --> FILTER[Apply Filters]
    FILTER --> PAGINATE[Paginate Results]
    PAGINATE --> CACHE[Cache Results]
    CACHE --> RETURN[Return to Frontend]
    
    RETURN_CACHE --> RETURN
    
    RETURN --> DISPLAY[Display Results]
    DISPLAY --> HIGHLIGHT[Highlight Matches]
    
    style USER_INPUT fill:#8b5cf6,stroke:#6d28d9,color:#fff
    style DISPLAY fill:#10b981,stroke:#059669,color:#fff
```

---

## VISUAL FLOW SUMMARY

✅ **15 Comprehensive Diagrams Created**

1. System Architecture Overview
2. Information Architecture
3. Lead to Traveler Conversion
4. Expedition Lifecycle
5. Administrator Daily Workflow
6. Guide Expedition Flow
7. Traveler Journey
8. Payment Processing
9. Automation Trigger Map
10. Data Flow Architecture
11. Role-Based Access Control
12. Notification Routing
13. Mobile App Navigation
14. Error Handling Flow
15. Search & Filter Architecture

These diagrams provide a complete visual understanding of:
- System structure
- User journeys
- Data flow
- Automation logic
- Navigation patterns
- Error handling
- Access control
- Process workflows

---

**Usage**: These Mermaid diagrams can be rendered in:
- GitHub/GitLab (native support)
- Documentation tools (Docusaurus, MkDocs)
- Notion, Confluence
- VS Code (with Mermaid preview extension)
- Online editors (mermaid.live)

They serve as living documentation that evolves with the platform.
