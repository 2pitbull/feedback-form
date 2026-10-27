const form = document.querySelector('#feedback-form');

if (form) {
    const fieldNames = [
        'subject',
        'name',
        'phone',
        'email',
        'message',
        'captcha_answer',
        'personal_data_consent',
    ];

    const fields = fieldNames.map((name) => form.elements.namedItem(name));

    const phoneField = form.elements.namedItem('phone');
    const messageField = form.elements.namedItem('message');
    const messageCounter = document.querySelector('#message-counter');
    const formMessage = document.querySelector('#form-message');
    const submitButton = form.querySelector('button[type="submit"]');

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/u;

    /**
     * Форматирует российский номер телефона.
     */
    function formatRussianPhone(value) {
        let digits = value.replace(/\D/g, '');

        // Принимаем ввод как с +7, так и с 8.
        if (digits.startsWith('7') || digits.startsWith('8')) {
            digits = digits.slice(1);
        }

        // Российский номер содержит 10 цифр после кода страны.
        digits = digits.slice(0, 10);

        if (!digits.length) {
            return '';
        }

        let formatted = `+7 (${digits.slice(0, 3)}`;

        if (digits.length >= 3) {
            formatted += ')';
        }

        if (digits.length > 3) {
            formatted += ` ${digits.slice(3, 6)}`;
        }

        if (digits.length > 6) {
            formatted += `-${digits.slice(6, 8)}`;
        }

        if (digits.length > 8) {
            formatted += `-${digits.slice(8, 10)}`;
        }

        return formatted;
    }

    /**
     * Удаляет ошибку конкретного поля.
     */
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        field.setAttribute('aria-invalid', 'false');

        const errorElement = document.getElementById(
            `${field.name}-error`,
        );

        if (errorElement) {
            errorElement.textContent = '';
        }
    }

    /**
     * Отображает ошибку конкретного поля.
     */
    function setFieldError(field, message) {
        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');

        const errorElement = document.getElementById(
            `${field.name}-error`,
        );

        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    /**
     * Проверяет одно поле.
     *
     * Возвращает текст ошибки либо пустую строку.
     */
    function validateField(field) {
        const value = field.type === 'checkbox'
            ? field.checked
            : field.value.trim();

        if (field.type === 'checkbox' && !value) {
            return 'Необходимо согласие на обработку персональных данных.';
        }

        if (field.type !== 'checkbox' && !value) {
            if (field.name === 'subject') {
                return 'Выберите тему обращения.';
            }

            return 'Это поле обязательно для заполнения.';
        }

        if (
            field.type !== 'checkbox'
            && field.name !== 'message'
            && field.value.length > 255
        ) {
            return 'Максимальная длина поля — 255 символов.';
        }

        if (field.name === 'message' && field.value.length > 4096) {
            return 'Сообщение не должно превышать 4096 символов.';
        }

        if (field.name === 'phone') {
            const digits = field.value.replace(/\D/g, '');

            if (digits.length !== 11 || !digits.startsWith('7')) {
                return 'Введите корректный российский номер телефона.';
            }
        }

        if (field.name === 'email' && !emailPattern.test(value)) {
            return 'Введите корректный адрес электронной почты.';
        }

        return '';
    }

    /**
     * Обновляет счётчик символов сообщения.
     */
    function updateMessageCounter() {
        messageCounter.textContent =
            `${messageField.value.length} / 4096`;
    }

    /**
     * Очищает общее уведомление формы.
     */
    function clearFormMessage() {
        formMessage.textContent = '';
        formMessage.className = 'form-message';
    }

    // Маска телефона.
    phoneField.addEventListener('input', () => {
        phoneField.value = formatRussianPhone(phoneField.value);

        // При вводе продолжаем печатать с конца номера.
        phoneField.setSelectionRange(
            phoneField.value.length,
            phoneField.value.length,
        );

        clearFieldError(phoneField);
        clearFormMessage();
    });

    // Обработка остальных полей.
    fields.forEach((field) => {
        if (field.name === 'phone') {
            return;
        }

        const eventName =
            field.type === 'checkbox' || field.tagName === 'SELECT'
                ? 'change'
                : 'input';

        field.addEventListener(eventName, () => {
            clearFieldError(field);
            clearFormMessage();

            if (field.name === 'message') {
                updateMessageCounter();
            }
        });
    });

    // Начальное значение счётчика.
    updateMessageCounter();

    // Проверка перед отправкой.
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        fields.forEach(clearFieldError);
        clearFormMessage();

        let firstInvalidField = null;

        fields.forEach((field) => {
            const error = validateField(field);

            if (error) {
                setFieldError(field, error);

                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            }
        });

        if (firstInvalidField) {
            formMessage.textContent =
                'Проверьте форму и исправьте отмеченные ошибки.';
            formMessage.classList.add('is-error');

            firstInvalidField.focus();

            return;
        }

        submitButton.disabled = true;
        submitButton.setAttribute('aria-busy', 'true');

        formMessage.textContent = 'Отправляем данные на сервер...';

        try {
            const response = await fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const result = await response.json();

            if (response.status === 429) {
                formMessage.textContent =
                    'Слишком много попыток отправки. Подождите минуту и попробуйте снова.';

                formMessage.classList.add('is-error');

                return;
            }

            if (response.status === 422 && result.errors) {
                Object.entries(result.errors).forEach(([name, messages]) => {
                    const field = form.elements.namedItem(name);

                    if (field && field.type !== 'hidden') {
                        setFieldError(field, messages[0]);
                    }
                });

                formMessage.textContent =
                    'Сервер обнаружил ошибки. Проверьте отмеченные поля.';
                formMessage.classList.add('is-error');

                const firstServerInvalidField = fields.find((field) =>
                    field.classList.contains('is-invalid'),
                );

                firstServerInvalidField?.focus();

                return;
            }

            if (!response.ok) {
                formMessage.textContent =
                    result.message || 'Не удалось отправить сообщение. Попробуйте позже.';

                formMessage.classList.add('is-error');

                return;
            }

            const captchaQuestion = document.querySelector('#captcha-question');
            const captchaField = form.elements.namedItem('captcha_answer');

            if (result.captchaQuestion && captchaQuestion) {
                captchaQuestion.textContent = result.captchaQuestion;
                captchaField.value = '';
                clearFieldError(captchaField);
            }

            formMessage.textContent = result.message;
            formMessage.classList.add('is-success');
        } catch (error) {
            console.error('Feedback request failed:', error);

            formMessage.textContent =
                'Не удалось обработать запрос. Попробуйте ещё раз.';
            formMessage.classList.add('is-error');
        } finally {
            submitButton.disabled = false;
            submitButton.removeAttribute('aria-busy');
        }
    });
}
