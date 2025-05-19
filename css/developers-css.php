<style>
    .page-wrapper {
        width: 100%;
        margin: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .developers-wrapper {
        width: 95%;
        margin: auto;
        background: var(--ivory-color);
        border-radius: 0;
        box-shadow: none;
        padding: 32px 0;
    }

    .developers-wrapper h1 {
        text-align: center;
        color: var(--button-color);
        margin-bottom: 32px;
    }

    .repo-link {
        text-align: center;
        margin-bottom: 32px;
    }

    .repo-link a {
        color: var(--button-color);
        font-weight: 600;
        text-decoration: underline;
    }

    .dev-row {
        display: flex;
        flex-direction: row;
        gap: 32px;
        justify-content: center;
        align-items: stretch;
        width: 100%;
        padding: 0 2px;
        box-sizing: border-box;
    }

    .dev-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 40px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1 1 0;
        min-width: 0;
        max-width: 100%;
    }

    .dev-card img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 18px;
    }

    .dev-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 18px;
        width: 100%;
    }

    .dev-card h3 {
        margin: 0 0 10px 0;
        color: var(--navy-color);
        font-size: 1.8rem;
        text-align: center;
    }

    .dev-card p {
        color: var(--page-text-color);
        text-align: left;
        font-size: 1.15rem;
        margin-bottom: 0;
    }

    .dev-links {
        margin-top: 18px;
        display: flex;
        gap: 18px;
        justify-content: center;
    }

    .dev-links a {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--button-color);
        color: white;
        padding: 8px 15px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.2s;
        font-size: 0.9rem;
    }

    .dev-links a:hover {
        background: var(--ivory-color);
        color: var(--button-color);
    }

    .dev-links .icon {
        font-size: 1.2em;
    }

    @media (max-width: 900px) {
        .dev-row {
            flex-direction: column;
            gap: 24px;
            align-items: center;
            padding: 0 4vw;
        }
        .dev-card {
            max-width: 400px;
            width: 100%;
        }
    }
</style> 