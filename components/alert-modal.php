<style>
    .alert-modal {
        display: none;
        width: 25%;
        z-index: 14;
        position: fixed;
        bottom: 2rem;
        left: 2rem;
        flex-direction: column-reverse;
        gap: 0.5rem;
    }

    .alert {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        margin: 0;
        border-radius: 1rem;
        border: none;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-out;
        animation: slideIn 0.3s ease-out;
    }

    .alert-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .alert-text {
        flex: 1;
    }

    .alert-message {
        margin-bottom: 0;
        font-size: 1rem;
    }

    .alert.success {
        background-color: var(--success);
        color: var(--bgt);
    }

    .alert.error {
        background-color: var(--error);
        color: var(--bgt);
    }

    .alert.warning {
        background-color: var(--warning);
        color: #000000;
    }

    .alert.info {
        background-color: var(--info);
        color: var(--primary);
    }

    @keyframes slideIn {
        from {
            transform: translateY(-10px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .fade-out {
        opacity: 0;
        transition: opacity 0.5s ease-out;
    }

    @media (max-width: 1096px) {
        .alert-logo {
            font-size: 1rem;
        }
        .alert-message {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 860px) {
        .alert-modal {
            width: 250px;
            left: 1rem;
            bottom: 1rem;
        }
    }


</style>

<div class="alert-modal" id="alert-modal">
</div>
