<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Обратная связь</title>
</head>
<body>
<main class="page">
    <section class="feedback-card" aria-labelledby="page-title">
        <div class="feedback-card__header">
            ОБРАТНАЯ СВЯЗЬ
        </div>

        <form
            id="feedback-form"
            class="feedback-form"
            action="{{ route('feedback.store') }}"
            method="POST"
            novalidate
        >
            @csrf

            <input type="hidden" name="action" value="message">

            <div class="honeypot" aria-hidden="true">
                <label for="website">Оставьте поле пустым</label>

                <input
                    type="text"
                    id="website"
                    name="website"
                    tabindex="-1"
                    autocomplete="off"
                >
            </div>

            <div class="form-field form-field--full">
                <label for="subject">Тема обращения</label>

                <select
                    id="subject"
                    name="subject"
                    class="form-control"
                    required
                >
                    <option value="" selected disabled>
                        Выберите тему обращения
                    </option>
                    <option value="consultation">Консультация</option>
                    <option value="technical">Технический вопрос</option>
                    <option value="cooperation">Сотрудничество</option>
                    <option value="other">Другое</option>
                </select>

                <p class="field-error" id="subject-error" aria-live="polite"></p>
            </div>

            <div class="form-field">
                <label for="name">ФИО</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    class="form-control"
                    placeholder="Иванов Иван Иванович"
                    autocomplete="name"
                    maxlength="255"
                    required
                >

                <p class="field-error" id="name-error" aria-live="polite"></p>
            </div>

            <div class="form-field">
                <label for="phone">Телефон</label>

                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    class="form-control"
                    placeholder="+7 (999) 123-45-67"
                    autocomplete="tel"
                    inputmode="tel"
                    maxlength="255"
                    required
                >

                <p class="field-error" id="phone-error" aria-live="polite"></p>
            </div>

            <div class="form-field form-field--full">
                <label for="email">E-mail</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    class="form-control"
                    placeholder="example@mail.ru"
                    autocomplete="email"
                    maxlength="255"
                    required
                >

                <p class="field-error" id="email-error" aria-live="polite"></p>
            </div>

            <div class="form-field form-field--full">
                <label for="message">Сообщение</label>

                <textarea
                    id="message"
                    name="message"
                    class="form-control form-control--textarea"
                    placeholder="Расскажите подробнее о вашем вопросе..."
                    rows="5"
                    maxlength="4096"
                    required
                ></textarea>

                <div class="field-footer">
                    <p class="field-error" id="message-error" aria-live="polite"></p>
                    <span class="character-counter" id="message-counter">
                            0 / 4096
                    </span>
                </div>
            </div>

            <div class="form-field form-field--full">
                <label for="captcha_answer">Проверка от автоматических отправок</label>

                <div class="captcha-question" id="captcha-question">
                    {{ $captchaQuestion }}
                </div>

                <input
                    id="captcha_answer"
                    name="captcha_answer"
                    type="text"
                    class="form-control"
                    placeholder="Введите ответ"
                    maxlength="255"
                    required
                >

                <p class="field-error" id="captcha_answer-error" aria-live="polite"></p>
            </div>

            <div class="form-field form-field--full">
                <label class="consent">
                    <input
                        type="checkbox"
                        id="personal_data_consent"
                        name="personal_data_consent"
                        value="1"
                        required
                    >

                    <span>
                        Я согласен(на) на обработку персональных данных
                    </span>
                </label>

                <p
                    class="field-error"
                    id="personal_data_consent-error"
                    aria-live="polite"
                ></p>
            </div>

            <div
                class="form-message"
                id="form-message"
                role="status"
                aria-live="polite"
            ></div>

            <button type="submit" class="submit-button">
                Отправить сообщение
            </button>

            <p class="form-note">
                Все поля обязательны для заполнения.
            </p>
        </form>
    </section>
</main>
</body>
</html>
