<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - Évaluation des Enseignements</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <!-- Glassmorphism CSS -->
    <link rel="stylesheet" href="{{ asset('css/shared-results-glass.css') }}">

    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5a67d8;
            --primary-light: #a3bffa;
            --secondary: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --gray-light: #e2e8f0;
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
            --header-height: 70px;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--dark);
            min-height: 100vh;
        }

        /* ========================================
           SIDEBAR - Glassmorphism Style
           ======================================== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--gradient-primary);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 25px rgba(102, 126, 234, 0.25);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .sidebar-logo i {
            font-size: 1.8rem;
            color: var(--primary);
        }

        .sidebar-title {
            color: white;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            transition: opacity 0.3s ease;
        }

        .sidebar-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .sidebar-subtitle {
            opacity: 0;
            height: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-logo {
            width: 48px;
            height: 48px;
        }

        /* Menu */
        .sidebar-menu {
            padding: 1rem 0;
            position: relative;
        }

        .menu-section {
            padding: 0.75rem 1.5rem 0.5rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .menu-section {
            opacity: 0;
            height: 0;
            padding: 0;
            overflow: hidden;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.9rem 1.5rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            gap: 0.85rem;
            position: relative;
            margin: 0.15rem 0.5rem;
            border-radius: 0 12px 12px 0;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: white;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .menu-item:hover::before {
            transform: scaleY(1);
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.18);
            color: white;
        }

        .menu-item.active::before {
            transform: scaleY(1);
        }

        .menu-item i {
            width: 22px;
            text-align: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .menu-item span {
            font-size: 0.9rem;
            font-weight: 500;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 0.9rem;
            margin: 0.15rem;
            border-radius: 12px;
        }

        .sidebar.collapsed .menu-item span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .menu-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.25);
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .sidebar.collapsed .menu-badge {
            display: none;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-collapse-btn {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-collapse-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .sidebar-collapse-btn i {
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed .sidebar-collapse-btn i {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .sidebar-collapse-btn span {
            display: none;
        }

        /* ========================================
           MAIN CONTENT
           ======================================== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* ========================================
           NAVBAR - Glassmorphism Style
           ======================================== */
        .main-header {
            height: var(--header-height);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            width: 42px;
            height: 42px;
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .sidebar-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .header-title-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-logo {
            width: 42px;
            height: 42px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .header-logo img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        .header-logo i {
            font-size: 1.3rem;
            color: var(--primary);
        }

        .page-title-wrapper {
            display: flex;
            flex-direction: column;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.02em;
        }

        .page-breadcrumb {
            font-size: 0.75rem;
            color: var(--gray);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-campus {
            background: rgba(102, 126, 234, 0.1);
            padding: 0.6rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .header-campus i {
            color: var(--primary);
        }

        .header-campus strong {
            color: var(--primary);
            font-weight: 600;
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem 0.5rem 0.5rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .user-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .user-btn span {
            font-size: 0.9rem;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            min-width: 240px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid var(--gray-light);
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 1rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-bottom: 1px solid var(--gray-light);
        }

        .dropdown-header-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .dropdown-header-email {
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: 0.2rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: var(--light);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            color: var(--gray);
        }

        .dropdown-item.danger {
            color: var(--danger);
        }

        .dropdown-item.danger i {
            color: var(--danger);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-light);
            margin: 0.25rem 0;
        }

        /* ========================================
           CONTENT AREA
           ======================================== */
        .content-area {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #065f46;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #991b1b;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #1e40af;
        }

        /* Cards */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            background: var(--gradient-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .card-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }
        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: var(--light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--gray);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--gray-light);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-light);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: var(--light);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            font-weight: 500;
            font-family: inherit;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-secondary {
            background: var(--gray);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .badge-primary {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary);
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #92400e;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            color: #1e40af;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-family: inherit;
            border: 2px solid var(--gray-light);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .stat-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .stat-content p {
            color: var(--gray);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--gray);
            cursor: pointer;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--gray-light);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }

            .main-header {
                padding: 0 1rem;
            }

            .header-campus {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* DataTables Override */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
            border-radius: 6px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: var(--primary-dark) !important;
            border: none !important;
        }
        /* Sidebar Overlay (mobile) */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* ========================================
           RESPONSIVE
           ======================================== */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex;
            }

            .header-logo {
                display: flex;
            }

        }

        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }

            .main-header {
                padding: 0 1rem;
            }

            .header-campus {
                display: none;
            }

            .page-title {
                font-size: 1.1rem;
            }

            .user-btn span {
                display: none;
            }

            .user-btn {
                padding: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .header-title-wrapper {
                display: none;
            }

            .main-header {
                height: 60px;
            }
        }

        /* ========================================
           DATATABLES OVERRIDE
           ======================================== */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: var(--primary-dark) !important;
            border: none !important;
        }

        /* Badge */
        .badge-primary {
            background: rgba(102, 126, 234, 0.15);
            color: var(--primary);
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>
<body>
<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('dist/img/logo_isi.jpg') }}" alt="Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <i class="fas fa-graduation-cap" style="display: none;"></i>
        </div>
        <div class="sidebar-title">ISI ENS_EVAL</div>
        <div class="sidebar-subtitle">Gestion Qualité Enseignements</div>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-section">Navigation</div>

        <a href="{{ route('tools') }}" class="menu-item {{ request()->routeIs('tools') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="{{ route('activation') }}" class="menu-item {{ request()->routeIs('activation') ? 'active' : '' }}">
            <i class="fas fa-toggle-on"></i>
            <span>Activer évaluation</span>
        </a>

        <a href="{{ route('etudiants.import') }}" class="menu-item {{ request()->routeIs('etudiants.*') ? 'active' : '' }}">
            <i class="fas fa-file-import"></i>
            <span>Importer étudiants</span>
        </a>

        <div class="menu-section">Résultats</div>

        <a href="{{ route('liste_prof') }}" class="menu-item {{ request()->routeIs('liste_prof') || request()->routeIs('resultat_prof') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Rapports Enseignants</span>
        </a>

        <a href="{{ route('liste_niveau') }}" class="menu-item {{ request()->routeIs('liste_niveau') || request()->routeIs('resultat_niveau') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Résultats Par Niveau</span>
        </a>

        <a href="{{ route('resultat_general') }}" class="menu-item {{ request()->routeIs('resultat_general') || request()->routeIs('resultat') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Résultat Par Classe</span>
        </a>

        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
            <div class="menu-section">Administration</div>

            <a href="{{ route('admin') }}" class="menu-item {{ request()->routeIs('admin') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Utilisateurs</span>
            </a>
        @endif
    </nav>

</aside>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Header -->
    <header class="main-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-title-wrapper">
                <div class="header-logo">
                    <img src="{{ asset('dist/img/logo_isi.jpg') }}" alt="Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-graduation-cap\'></i>';">
                </div>
                <div class="page-title-wrapper">
                    <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                    <span class="page-breadcrumb">ISI ENS_EVAL</span>
                </div>
            </div>
        </div>
        <div class="header-right">
            <div class="header-campus">
                <i class="fas fa-building"></i>
                Campus: <strong>{{ Auth::user()->campus->nomCampus ?? 'Principal' }}</strong>
            </div>
            <div class="user-dropdown">
                <button class="user-btn" id="userDropdownBtn">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </button>
                <div class="dropdown-menu" id="userDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dropdown-header-name">{{ Auth::user()->name }}</div>
                        <div class="dropdown-header-email">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="#" class="dropdown-item" style="pointer-events: none;">
                        <i class="fas fa-shield-alt"></i>
                        <span class="badge-primary">{{ ucfirst(Auth::user()->role) }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="content-area">
        @if (session('status'))
            <div class="alert {{ str_contains(session('status'), 'Erreur') || str_contains(session('status'), 'Impossible') ? 'alert-danger' : 'alert-success' }}">
                <i class="fas {{ str_contains(session('status'), 'Erreur') || str_contains(session('status'), 'Impossible') ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // DOM Elements
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const collapseBtn = document.getElementById('collapseBtn');
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');

    // Check if mobile
    const isMobile = () => window.innerWidth <= 992;

    // Sidebar Toggle (Mobile)
    sidebarToggle?.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            // Save preference
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    });

    // Sidebar Overlay Click
    sidebarOverlay?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('show');
    });

    // Collapse Button (Desktop)
    collapseBtn?.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });

    // Restore sidebar state
    if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }

    // User Dropdown
    userDropdownBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdownMenu.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-dropdown')) {
            userDropdownMenu?.classList.remove('show');
        }
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (isMobile()) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    });

    // Initialize DataTables
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    }
                ]
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>
