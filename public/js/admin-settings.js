(function () {
    const config = window.SettingsAdminConfig;

    async function request(url, method, body) {
        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Accept': 'application/json',
            },
        };

        if (body) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        const response = await fetch(url, options);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(Object.values(data.errors || {}).flat().join(' ') || 'Error');
        }

        return data;
    }

    document.getElementById('holidayForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            const data = await request(config.holidayStoreUrl, 'POST', {
                holiday_date: formData.get('holiday_date'),
                name: formData.get('name'),
            });
            location.reload();
        } catch (err) {
            alert(err.message);
        }
    });

    document.querySelectorAll('.delete-holiday-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('¿Eliminar festivo?')) return;
            try {
                await request(`${config.holidayDeleteUrl}/${this.dataset.id}`, 'DELETE');
                location.reload();
            } catch (err) {
                alert(err.message);
            }
        });
    });

    document.getElementById('blockedDateForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            await request(config.blockedStoreUrl, 'POST', {
                blocked_date: formData.get('blocked_date'),
                reason: formData.get('reason'),
            });
            location.reload();
        } catch (err) {
            alert(err.message);
        }
    });

    document.querySelectorAll('.delete-blocked-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('¿Eliminar fecha bloqueada?')) return;
            try {
                await request(`${config.blockedDeleteUrl}/${this.dataset.id}`, 'DELETE');
                location.reload();
            } catch (err) {
                alert(err.message);
            }
        });
    });
})();
