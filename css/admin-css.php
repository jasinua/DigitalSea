<style>
    .page-wrapper {
        width: 100%;
        min-height: 100vh;
        background-color: var(--page-background-color);
    }

    .admin-container {
        max-width: 1200px;
        width: 100%;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .admin-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        color: var(--page-text-color);
    }

    .admin-header i {
        font-size: 2rem;
        color: var(--page-text-color);
    }

    .content {
        color: var(--page-text-color);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .users-table th, .users-table td {
        padding: 1rem;
        text-align: left;
    }

    .users-table th, .users-table tr {
        border-bottom: 1px solid #eee;
    }

    .users-table tr {
        align-items: center;
    }

    .users-table th {
        background: #f8f9fa;
        font-weight: 600;
    }

    .admin-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .admin-0 { 
        background: #e9ecef; 
        color: #495057; 
    }

    .admin-1 { 
        background: #cff4fc; 
        color: #055160; 
    }

    .admin-2 { 
        background: #d1e7dd; 
        color: #0f5132; 
    }

    .admin-action-form {
        display: flex;
        justify-content: space-between;
        margin-left: 10px;
        gap: 10px;
    }

    tbody td:last-child {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    tbody td span {
        width: 100%;
        text-align: center;
    }

    .admin-action-btn {
        padding: 0.3rem 0.8rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        margin-bottom: 2px;
        margin-right: 2px;
        background: #f1f3f4;
        color: #232a2f;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }

    .admin-action-btn:hover {
        background: #dde6ed;
        color: #153147;
    }

    .admin-action-btn[value="0"] { 
        background: #e9ecef; 
        color: #495057; 
    }

    .admin-action-btn[value="1"] { 
        background: #cff4fc; 
        color: #055160; 
    }

    .admin-action-btn[value="2"] { 
        background: #d1e7dd; 
        color: #0f5132; 
    }

    .admin-action-btn[name="deleteUser"] {
        background: #e74c3c !important;
        color: #fff !important;
        margin-left: 8px;
    }

    .admin-action-btn[name="deleteUser"]:hover {
        background: #c0392b !important;
        color: #fff !important;
    }

    .admin-action-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .admin-action-btn {
        margin: 0 !important;
        min-width: 90px;
        text-align: center;
    }

    @media (max-width: 1030px) {
        thead th:nth-child(2),
        tbody td:nth-child(2) {
            display: none;
        }
    }

    /* Desktop: show buttons, hide dropdown */
    .desktop-admin-actions { 
        display: flex; 
    }

    .mobile-admin-actions { 
        display: none; 
    }

    /* Mobile: show dropdown, hide buttons */
    @media (max-width: 930px) {
        .desktop-admin-actions { 
            display: none !important; 
        }

        .mobile-admin-actions { 
            display: flex !important; 
            flex-direction: row; 
            gap: 5px; 
        }

        .admin-action-select { 
            padding: 6px; 
            min-width: max-content;
            border-radius: 12px;
        }
    }

    @media (max-width: 830px) {
        .admin-badge {
            display: none;
        }

        .users-table tbody td:nth-child(3) {
            padding: 1rem 0rem;
        }

        
        .users-table tbody td:nth-child(4) {
            padding: 1rem 1rem 1rem 0rem;
        }

        .admin-action-form {
            margin-left: 0;
            justify-content: space-between;
            width: 100%;
        }
    }

    @media (max-width: 540px) {
        .admin-action-btn {
            min-width: unset;
        }

        .users-table {
            font-size: 0.9rem;
        }

        .admin-action-select {
            font-size: 0.8rem;
        }

        thead th:nth-child(1),
        tbody td:nth-child(1) {
            display: none;
        }

        .users-table tbody td:nth-child(3) {
            padding: 1rem;
        }
    }

    @media (max-width: 440px) {
        .users-table td:nth-child(3) {
            word-break: break-all;
            white-space: normal;
        }
    }
</style>