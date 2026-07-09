(function () {
    const config = window.EmployeeAdminConfig;
    const toastEl = document.getElementById('adminToast');
    const toast = toastEl ? new bootstrap.Toast(toastEl) : null;

    function showToast(message) {
        if (!toast) return;
        document.getElementById('adminToastBody').textContent = message;
        toast.show();
    }

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
            const message = data.message || Object.values(data.errors || {}).flat().join(' ');
            throw new Error(message || 'Error en la operación');
        }

        return data;
    }

    document.getElementById('createEmployeeForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            await request(config.storeUrl, 'POST', {
                name: formData.get('name'),
                color: formData.get('color'),
            });
            bootstrap.Modal.getInstance(document.getElementById('createEmployeeModal')).hide();
            location.reload();
        } catch (err) {
            showToast(err.message);
        }
    });

    document.querySelectorAll('.edit-employee-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const employee = JSON.parse(this.dataset.employee);
            document.getElementById('edit-employee-id').value = employee.id;
            document.getElementById('edit-employee-name').value = employee.name;
            document.getElementById('edit-employee-color').value = employee.color;
            new bootstrap.Modal(document.getElementById('editEmployeeModal')).show();
        });
    });

    document.getElementById('editEmployeeForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('edit-employee-id').value;
        const formData = new FormData(this);
        try {
            await request(`${config.updateUrl}/${id}`, 'PUT', {
                name: formData.get('name'),
                color: formData.get('color'),
            });
            bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal')).hide();
            location.reload();
        } catch (err) {
            showToast(err.message);
        }
    });

    document.querySelectorAll('.delete-employee-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('¿Eliminar este empleado?')) return;
            try {
                await request(`${config.deleteUrl}/${this.dataset.id}`, 'DELETE');
                location.reload();
            } catch (err) {
                showToast(err.message);
            }
        });
    });

    document.querySelectorAll('.block-employee-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            try {
                await request(`${config.blockUrl}/${this.dataset.id}/block`, 'POST');
                location.reload();
            } catch (err) {
                showToast(err.message);
            }
        });
    });

    document.querySelectorAll('.reactivate-employee-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            try {
                await request(`${config.reactivateUrl}/${this.dataset.id}/reactivate`, 'POST');
                location.reload();
            } catch (err) {
                showToast(err.message);
            }
        });
    });

    document.querySelectorAll('.history-employee-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const response = await fetch(`${config.historyUrl}/${this.dataset.id}/history`, {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json();
            document.getElementById('history-employee-name').textContent = data.employee.name;

            const content = document.getElementById('history-content');
            if (data.vacations.length === 0) {
                content.innerHTML = '<p class="text-muted">Sin vacaciones registradas.</p>';
            } else {
                content.innerHTML = '<ul class="list-group">' + data.vacations.map(v =>
                    `<li class="list-group-item d-flex justify-content-between">
                        <span>${v.vacation_date}</span>
                        <small class="text-muted">${new Date(v.created_at).toLocaleString('es-MX')}</small>
                    </li>`
                ).join('') + '</ul>';
            }

            new bootstrap.Modal(document.getElementById('historyModal')).show();
        });
    });
})();
