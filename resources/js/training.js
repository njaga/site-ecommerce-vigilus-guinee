const form = document.querySelector('[data-training-form]');
if (form) {
    const type = form.querySelector('[name="request_type"]');
    const sessionField = form.querySelector('[data-session-field]');
    const session = form.querySelector('[name="training_session_id"]');
    const company = form.querySelector('[name="company"]');
    const syncFields = () => {
        const isSession = type.value === 'session';
        if (sessionField) sessionField.hidden = !isSession;
        if (session) { session.disabled = !isSession; session.required = isSession; }
        company.required = type.value === 'entreprise';
        form.querySelector('[data-company-required]').hidden = !company.required;
    };
    type.addEventListener('change', syncFields);
    syncFields();
}
