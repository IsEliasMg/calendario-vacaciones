(function () {
    const config = window.AdminCalendarConfig;
    let calendar;
    let blockedData = {};
    let selectedVacationId = null;
    let activeFilters = {
        employee_id: '',
        month: '',
        year: '',
        search: '',
    };
    const vacationModal = new bootstrap.Modal(document.getElementById('vacationModal'));

    function toDateKey(value) {
        if (!value) return '';
        if (typeof value === 'string') return value.slice(0, 10);
        const year = value.getFullYear();
        const month = String(value.getMonth() + 1).padStart(2, '0');
        const day = String(value.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function readFiltersFromForm() {
        return {
            employee_id: document.getElementById('filter-employee').value || '',
            month: document.getElementById('filter-month').value || '',
            year: document.getElementById('filter-year').value || '',
            search: (document.getElementById('filter-search').value || '').trim(),
        };
    }

    function buildQuery(extra = {}) {
        const filters = { ...activeFilters, ...extra };
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value !== null && value !== undefined && String(value).trim() !== '') {
                params.set(key, value);
            }
        });
        return params.toString();
    }

    function dayCellClassNames(arg) {
        const dateStr = toDateKey(arg.date);
        const classes = [];
        if ((blockedData.fullDays || []).some(f => f.date === dateStr)) {
            classes.push('fc-day-full');
        }
        return classes;
    }

    async function loadLegend() {
        const response = await fetch(config.legendUrl);
        const data = await response.json();
        const container = document.getElementById('color-legend');
        container.innerHTML = '';

        data.employees
            .filter(emp => !activeFilters.employee_id || String(emp.id) === String(activeFilters.employee_id))
            .filter(emp => !activeFilters.search || emp.name.toLowerCase().includes(activeFilters.search.toLowerCase()))
            .forEach(emp => {
                const span = document.createElement('span');
                span.className = 'legend-item';
                span.innerHTML = `<span class="legend-color" style="background:${emp.color}"></span>${emp.name}`;
                if (emp.status === 'blocked') span.innerHTML += ' <small class="text-muted">(bloqueado)</small>';
                container.appendChild(span);
            });
    }

    async function fetchBlocked(start, end) {
        const params = new URLSearchParams({ start, end });
        const response = await fetch(`${config.blockedUrl}?${params}`);
        blockedData = await response.json();
        calendar?.render();
    }

    function loadEvents(info, successCallback, failureCallback) {
        const params = new URLSearchParams({
            start: toDateKey(info.start),
            end: toDateKey(info.end),
        });

        if (activeFilters.employee_id) {
            params.set('employee_id', activeFilters.employee_id);
        }

        if (activeFilters.search) {
            params.set('search', activeFilters.search);
        }

        // Solo aplicar mes/año en la API si NO estamos navegando el calendario
        // (cuando el usuario eligió mes explícitamente, movemos la vista)
        fetch(`${config.eventsUrl}?${params.toString()}`)
            .then(r => r.json())
            .then(data => {
                let events = data.events || [];

                if (activeFilters.month) {
                    const month = String(activeFilters.month).padStart(2, '0');
                    events = events.filter(event => String(event.start).slice(5, 7) === month);
                }

                if (activeFilters.year) {
                    events = events.filter(event => String(event.start).slice(0, 4) === String(activeFilters.year));
                }

                successCallback(events);
            })
            .catch(failureCallback);
    }

    function initCalendar() {
        calendar = new FullCalendar.Calendar(document.getElementById('admin-calendar'), {
            initialView: 'dayGridMonth',
            locale: 'es',
            firstDay: 1,
            height: 'auto',
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                year: 'Año',
            },
            allDayText: 'Todo el día',
            moreLinkText: 'más',
            noEventsText: 'No hay eventos para mostrar',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,multiMonthYear',
            },
            views: {
                multiMonthYear: {
                    type: 'multiMonthYear',
                    duration: { years: 1 },
                    buttonText: 'Año',
                },
                dayGridMonth: { buttonText: 'Mes' },
                dayGridWeek: { buttonText: 'Semana' },
            },
            dayCellClassNames,
            events: loadEvents,
            datesSet: function (info) {
                fetchBlocked(toDateKey(info.start), toDateKey(info.end));
            },
            eventClick: function (info) {
                selectedVacationId = info.event.id;
                document.getElementById('modal-employee-name').textContent = info.event.extendedProps.employeeName;
                document.getElementById('modal-vacation-date').textContent = info.event.startStr;
                vacationModal.show();
            },
            eventDidMount: function (info) {
                info.el.setAttribute('title', info.event.title);
            },
        });

        calendar.render();
    }

    function applyFilters() {
        activeFilters = readFiltersFromForm();

        // Solo mover el calendario si el usuario eligió mes (y opcionalmente año)
        if (activeFilters.month) {
            const year = activeFilters.year || String(new Date().getFullYear());
            calendar.gotoDate(`${year}-${String(activeFilters.month).padStart(2, '0')}-01`);
        } else if (activeFilters.year && !activeFilters.employee_id && !activeFilters.search) {
            calendar.gotoDate(`${activeFilters.year}-01-01`);
        }

        calendar.refetchEvents();
        loadLegend();
        updateExportLinks();
        showFilterFeedback();
    }

    function showFilterFeedback() {
        const feedback = document.getElementById('filter-feedback');
        if (!feedback) return;

        const parts = [];
        if (activeFilters.employee_id) {
            const select = document.getElementById('filter-employee');
            parts.push(select.options[select.selectedIndex].text);
        }
        if (activeFilters.search) parts.push(`"${activeFilters.search}"`);
        if (activeFilters.month) {
            const monthSelect = document.getElementById('filter-month');
            parts.push(monthSelect.options[monthSelect.selectedIndex].text);
        }
        if (activeFilters.year) parts.push(activeFilters.year);

        feedback.textContent = parts.length
            ? `Filtro activo: ${parts.join(' · ')}`
            : 'Mostrando todas las vacaciones';
    }

    function updateExportLinks() {
        const query = buildQuery();
        document.getElementById('export-print').href = `${config.exportPrintUrl}?${query}`;
        document.getElementById('export-pdf').href = `${config.exportPdfUrl}?${query}`;
        document.getElementById('export-excel').href = `${config.exportExcelUrl}?${query}`;
    }

    document.getElementById('apply-filters').addEventListener('click', applyFilters);
    document.getElementById('filter-employee').addEventListener('change', applyFilters);
    document.getElementById('filter-month').addEventListener('change', applyFilters);
    document.getElementById('filter-year').addEventListener('change', applyFilters);
    document.getElementById('filter-search').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyFilters();
        }
    });

    document.getElementById('clear-filters')?.addEventListener('click', function () {
        document.getElementById('filter-employee').value = '';
        document.getElementById('filter-month').value = '';
        document.getElementById('filter-year').value = '';
        document.getElementById('filter-search').value = '';
        applyFilters();
    });

    document.getElementById('delete-vacation-btn').addEventListener('click', async function () {
        if (!selectedVacationId) return;
        if (!confirm('¿Eliminar esta vacación?')) return;

        const response = await fetch(`${config.deleteVacationUrl}/${selectedVacationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Accept': 'application/json',
            },
        });

        if (response.ok) {
            vacationModal.hide();
            calendar.refetchEvents();
            loadLegend();
        }
    });

    document.getElementById('export-print').addEventListener('click', function (e) {
        e.preventDefault();
        window.open(`${config.exportPrintUrl}?${buildQuery()}`, '_blank');
    });

    document.getElementById('export-pdf').addEventListener('click', function (e) {
        e.preventDefault();
        window.location.href = `${config.exportPdfUrl}?${buildQuery()}`;
    });

    document.getElementById('export-excel').addEventListener('click', function (e) {
        e.preventDefault();
        window.location.href = `${config.exportExcelUrl}?${buildQuery()}`;
    });

    initCalendar();
    activeFilters = readFiltersFromForm();
    // Al inicio no forzar año como filtro activo si solo está preseleccionado visualmente
    activeFilters.year = '';
    document.getElementById('filter-year').value = '';
    loadLegend();
    updateExportLinks();
    showFilterFeedback();

    setInterval(() => {
        calendar?.refetchEvents();
        const view = calendar?.view;
        if (view) {
            fetchBlocked(toDateKey(view.currentStart), toDateKey(view.currentEnd));
        }
        loadLegend();
    }, 30000);
})();
