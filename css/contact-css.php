<style>
    .contact-wrapper {
        width: 800px;
        margin: 40px auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        padding: 32px 24px;
    }

    .contact-wrapper h2 {
        text-align: center;
        color: var(--button-color);
        margin-bottom: 24px;
    }

    .contact-info {
        margin-bottom: 32px;
        color: var(--page-text-color);
        font-size: 1.1rem;
    }

    .contact-form .form-group {
        margin-bottom: 18px;
    }

    .contact-form label {
        display: block;
        margin-bottom: 6px;
        color: var(--page-text-color);
        font-weight: 500;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        background: var(--ivory-color);
    }

    .contact-form textarea {
        resize: vertical;
        min-height: 100px;
    }

    .contact-form .btn {
        background: var(--button-color);
        color: #fff;
        border: none;
        padding: 12px 0;
        border-radius: 6px;
        font-size: 1.1rem;
        width: 100%;
        cursor: pointer;
        transition: background 0.2s;
    }

    .contact-form .btn:hover {
        background: var(--button-color-hover);
    }

    @media screen and (max-width: 850px) {
        .contact-wrapper {
            width: 90%;
        }
    }

    @media screen and (max-width: 600px) {
        .contact-info {
            font-size: 1rem;
        }
    }

    @media screen and (max-width: 400px) {
        .contact-info {
            font-size: 0.8rem;
        }

        .contact-form label {
            font-size: 0.8rem;
        }

        .contact-form input,
        .contact-form textarea {
            font-size: 0.8rem;
        }

        .contact-form .btn {
            font-size: 0.9rem;
        }
    }
</style> 