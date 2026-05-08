// ===========================
// Calendar Page Logic
// ===========================

let currentDate = new Date();
let expeditions = [];

// Load expeditions for calendar
async function loadCalendarData() {
    try {
        const response = await api.getExpeditions();
        
        if (response && response.data) {
            expeditions = response.data;
        }
        
        renderCalendar();
    } catch (error) {
        console.error('Error loading calendar data:', error);
        renderCalendar();
    }
}

// Render calendar
function renderCalendar() {
    const container = document.getElementById('calendar');
    
    if (!container) return;
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    let html = `
        <div class="calendar-header">
            <button onclick="previousMonth()" class="btn-secondary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <h2>${monthNames[month]} ${year}</h2>
            <button onclick="nextMonth()" class="btn-secondary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
        <div class="calendar-grid">
            <div class="calendar-day-header">Sun</div>
            <div class="calendar-day-header">Mon</div>
            <div class="calendar-day-header">Tue</div>
            <div class="calendar-day-header">Wed</div>
            <div class="calendar-day-header">Thu</div>
            <div class="calendar-day-header">Fri</div>
            <div class="calendar-day-header">Sat</div>
    `;
    
    // Empty cells before first day
    for (let i = 0; i < startingDayOfWeek; i++) {
        html += '<div class="calendar-day empty"></div>';
    }
    
    // Days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const isToday = date.toDateString() === new Date().toDateString();
        
        html += `
            <div class="calendar-day ${isToday ? 'today' : ''}">
                <div class="calendar-day-number">${day}</div>
                <div class="calendar-events">
                    ${getEventsForDay(date)}
                </div>
            </div>
        `;
    }
    
    html += '</div>';
    container.innerHTML = html;
}

// Get events for a specific day
function getEventsForDay(date) {
    // This would filter expeditions that occur on this date
    // For now, return empty
    return '';
}

// Navigate months
function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('calendar')) {
        loadCalendarData();
    }
});

// Add calendar styles
const style = document.createElement('style');
style.textContent = `
    .calendar-container {
        background: white;
        border-radius: 24px;
        padding: 32px;
        box-shadow: var(--shadow-sm);
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }
    
    .calendar-header h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--gray-900);
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: var(--gray-200);
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .calendar-day-header {
        background: var(--gray-50);
        padding: 12px;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-600);
    }
    
    .calendar-day {
        background: white;
        min-height: 100px;
        padding: 12px;
        position: relative;
    }
    
    .calendar-day.empty {
        background: var(--gray-50);
    }
    
    .calendar-day.today {
        background: var(--primary-light);
    }
    
    .calendar-day-number {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
    }
    
    .calendar-day.today .calendar-day-number {
        color: var(--primary);
        font-weight: 700;
    }
    
    .calendar-events {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
`;
document.head.appendChild(style);

// Export functions
window.loadCalendarData = loadCalendarData;
window.previousMonth = previousMonth;
window.nextMonth = nextMonth;
