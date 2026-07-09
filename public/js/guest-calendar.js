(function () {
    const config = window.GuestCalendarConfig;
    const selectedDates = new Set(config.existingDates || []);
    let employeeColor = config.employeeColor || '#9CA3AF';
    let blockedData = { fullDays: [], holidays: [], blocked: [], config: config.calendarConfig };
    let calendar;

    const saveBtn = document.getElementById('save-btn');
    const datesList = document.getElementById('selected-dates-list');
    const datesInputs = document.getElementById('dates-inputs');
    const noDatesMsg = document.getElementById('no-dates-msg');
    const vacationForm = document.getElementById('vacation-form');
    const colorInput = document.getElementById('employee-color-input');
    const colorPreview = document.getElementById('current-color-preview');
    const colorFeedback = document.getElementById('color-feedback');
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmModal = new bootstrap.Modal(confirmModalEl);

    function toDateKey(value) {
        if (!value) return '';
        if (typeof value === 'string') {
            return value.slice(0, 10);
        }
        const year = value.getFullYear();
        const month = String(value.getMonth() + 1).padStart(2, '0');
        const day = String(value.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatDate(dateStr) {
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    function syncFormInputs() {
        datesInputs.innerHTML = '';
        [...selectedDates].sort().forEach(date => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'dates[]';
            input.value = date;
            datesInputs.appendChild(input);
        });

        let colorField = vacationForm.querySelector('input[name="color"]');
        if (!colorField) {
            colorField = document.createElement('input');
            colorField.type = 'hidden';
            colorField.name = 'color';
            vacationForm.appendChild(colorField);
        }
        colorField.value = employeeColor;
    }

    function updateSelectedUI() {
        datesList.innerHTML = '';
        syncFormInputs();

        if (selectedDates.size === 0) {
            if (noDatesMsg) {
                datesList.appendChild(noDatesMsg);
                noDatesMsg.style.display = 'inline';
            }
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Guardar cambios';
            return;
        }

        if (noDatesMsg) noDatesMsg.style.display = 'none';
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Guardar vacaciones';

        [...selectedDates].sort().forEach(date => {
            const badge = document.createElement('span');
            badge.className = 'selected-day-badge';
            badge.style.backgroundColor = employeeColor;
            badge.style.color = '#fff';
            badge.innerHTML = `${formatDate(date)} <button type="button" class="btn-close btn-close-white btn-close-sm ms-1" data-date="${date}" aria-label="Quitar"></button>`;
            datesList.appendChild(badge);
        });
    }

    function isBlockedDate(dateStr) {
        const date = new Date(dateStr + 'T12:00:00');
        const cfg = blockedData.config || config.calendarConfig;

        if (date < new Date(new Date().toDateString())) return true;
        if (!cfg.allowSaturdays && date.getDay() === 6) return true;
        if (!cfg.allowSundays && date.getDay() === 0) return true;
        if (!(cfg.availableYears || []).includes(date.getFullYear())) return true;

        const holidayDates = (blockedData.holidays || []).map(h => h.date);
        const blockedDates = (blockedData.blocked || []).map(b => b.date);
        const fullDates = (blockedData.fullDays || []).map(f => f.date);

        if (holidayDates.includes(dateStr)) return true;
        if (blockedDates.includes(dateStr)) return true;
        if (fullDates.includes(dateStr) && !selectedDates.has(dateStr)) return true;

        return false;
    }

    function dayCellClassNames(arg) {
        const dateStr = toDateKey(arg.date);
        const classes = [];

        if (isBlockedDate(dateStr) && !selectedDates.has(dateStr)) {
            classes.push('fc-day-disabled');
        }

        if ((blockedData.fullDays || []).some(f => f.date === dateStr) && !selectedDates.has(dateStr)) {
            classes.push('fc-day-full');
        }

        if (selectedDates.has(dateStr)) {
            classes.push('fc-day-selected');
        }

        return classes;
    }

    function dayCellDidMount(arg) {
        const dateStr = toDateKey(arg.date);
        if (!selectedDates.has(dateStr)) return;

        arg.el.style.setProperty('--selected-color', employeeColor);
        arg.el.classList.add('fc-day-selected');
    }

    async function fetchBlocked(start, end) {
        const params = new URLSearchParams({ start, end });
        const response = await fetch(`${config.blockedUrl}?${params}`);
        blockedData = await response.json();
        calendar?.render();
    }

    function buildOwnEvents() {
        return [...selectedDates].sort().map(date => ({
            id: `own-${date}`,
            title: 'Mis vacaciones',
            start: date,
            allDay: true,
            backgroundColor: employeeColor,
            borderColor: employeeColor,
            textColor: '#ffffff',
            display: 'block',
        }));
    }

    function refreshCalendarSelection() {
        updateSelectedUI();
        if (!calendar) return;
        calendar.removeAllEvents();
        calendar.addEventSource(buildOwnEvents());
        calendar.render();
    }

    function applyColor(color) {
        employeeColor = color.toUpperCase();
        if (colorInput) colorInput.value = employeeColor;
        if (colorPreview) colorPreview.style.background = employeeColor;

        refreshCalendarSelection();
    }

    async function saveColor() {
        const color = (colorInput?.value || employeeColor).toUpperCase();

        try {
            const response = await fetch(config.colorUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                },
                body: JSON.stringify({ color }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'No se pudo guardar el color');
            }

            applyColor(data.color || color);
            if (colorFeedback) {
                colorFeedback.textContent = 'Color guardado correctamente.';
                colorFeedback.className = 'small mt-2 text-success';
            }
        } catch (error) {
            if (colorFeedback) {
                colorFeedback.textContent = error.message;
                colorFeedback.className = 'small mt-2 text-danger';
            }
        }
    }

    function toggleDate(dateStr) {
        if (isBlockedDate(dateStr) && !selectedDates.has(dateStr)) {
            return;
        }

        if (selectedDates.has(dateStr)) {
            selectedDates.delete(dateStr);
        } else {
            if (selectedDates.size >= config.maxDays) {
                alert(`Solo puedes seleccionar hasta ${config.maxDays} días.`);
                return;
            }
            selectedDates.add(dateStr);
        }

        refreshCalendarSelection();
    }

    function initCalendar() {
        const el = document.getElementById('guest-calendar');
        calendar = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'es',
            firstDay: 1,
            height: 'auto',
            selectable: false,
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                year: 'Año',
            },
            allDayText: 'Todo el día',
            moreLinkText: 'más',
            noEventsText: 'No hay eventos',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: '',
            },
            validRange: {
                start: toDateKey(new Date()),
            },
            dayCellClassNames,
            dayCellDidMount,
            events: buildOwnEvents(),
            datesSet: function (info) {
                fetchBlocked(toDateKey(info.start), toDateKey(info.end));
            },
            dateClick: function (info) {
                toggleDate(info.dateStr);
            },
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                toggleDate(info.event.startStr);
            },
            eventDidMount: function (info) {
                info.el.setAttribute('title', 'Clic para quitar');
                info.el.style.cursor = 'pointer';
            },
        });

        calendar.render();
    }

    datesList.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-date]');
        if (!btn) return;
        selectedDates.delete(btn.dataset.date);
        refreshCalendarSelection();
    });

    colorInput?.addEventListener('input', function () {
        applyColor(this.value);
    });

    document.getElementById('save-color-btn')?.addEventListener('click', saveColor);

    saveBtn.addEventListener('click', function () {
        syncFormInputs();

        const list = document.getElementById('confirm-dates-list');
        list.innerHTML = '';

        if (selectedDates.size === 0) {
            const li = document.createElement('li');
            li.textContent = 'Se eliminarán todas tus vacaciones futuras.';
            list.appendChild(li);
        } else {
            [...selectedDates].sort().forEach(date => {
                const li = document.createElement('li');
                li.textContent = formatDate(date);
                list.appendChild(li);
            });
        }

        confirmModal.show();
    });

    document.getElementById('confirm-save-btn').addEventListener('click', function () {
        syncFormInputs();
        vacationForm.submit();
    });

    updateSelectedUI();
    initCalendar();

    setInterval(() => {
        if (!calendar) return;
        const view = calendar.view;
        fetchBlocked(toDateKey(view.currentStart), toDateKey(view.currentEnd));
    }, 30000);
})();
