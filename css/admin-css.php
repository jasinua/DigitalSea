<style>
    .admin-container {
        max-width: 1200px;
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
        border-bottom: 1px solid #eee;
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
</style>