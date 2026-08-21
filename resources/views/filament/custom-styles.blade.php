<style>
    /* ==========================================================================
       DevCalc Ultra-Modern UI Design System: Buttons & Interactive Micro-Animations
       ========================================================================== */

    :root {
        --devcalc-transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        --devcalc-ease: cubic-bezier(0.16, 1, 0.3, 1);
        --devcalc-input-bg: #ffffff;
        --devcalc-input-border: #cbd5e1;
        --devcalc-input-color: #0f172a;
    }

    .dark, html.dark, body.dark, [data-theme="dark"] {
        --devcalc-input-bg: #1e293b;
        --devcalc-input-border: #334155;
        --devcalc-input-color: #f8fafc;
    }

    /* 1. Universal Base Button Architecture (.fi-btn, .fi-icon-btn, .fi-ac-action) */
    .fi-btn,
    .fi-icon-btn,
    .fi-ac-action,
    .fi-modal-footer-actions .fi-btn {
        position: relative;
        font-weight: 650 !important;
        letter-spacing: 0.2px !important;
        border-radius: 8px !important;
        transition: transform 0.2s var(--devcalc-ease), 
                    box-shadow 0.22s var(--devcalc-ease), 
                    background 0.2s ease, 
                    border-color 0.2s ease, 
                    color 0.2s ease !important;
    }

    .fi-btn:active,
    .fi-icon-btn:active,
    .fi-ac-action:active {
        transform: translateY(0.5px) scale(0.985) !important;
    }

    .fi-btn .fi-btn-icon,
    .fi-icon-btn svg,
    .fi-ac-action svg {
        transition: transform 0.22s var(--devcalc-ease);
    }

    .fi-btn:hover .fi-btn-icon,
    .fi-icon-btn:hover svg,
    .fi-ac-action:hover svg {
        transform: scale(1.08);
    }

    /* 2. Primary Hero Action Buttons Across All Menus (Indigo Gradient & Ambient Glow) */
    .fi-btn-color-primary,
    .fi-btn[type="submit"],
    .fi-ac-action.fi-color-primary,
    .fi-ta-header-actions .fi-btn-color-primary,
    .fi-modal-footer-actions .fi-btn-color-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25) !important;
    }

    .fi-btn-color-primary span,
    .fi-btn-color-primary svg,
    .fi-ac-action.fi-color-primary span,
    .fi-ac-action.fi-color-primary svg {
        color: #ffffff !important;
        fill: #ffffff !important;
    }

    .fi-btn-color-primary:hover,
    .fi-btn[type="submit"]:hover,
    .fi-ac-action.fi-color-primary:hover,
    .fi-ta-header-actions .fi-btn-color-primary:hover,
    .fi-modal-footer-actions .fi-btn-color-primary:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4) !important;
    }

    /* 3. Success Action Buttons (Emerald Gradient & Vibrant Glow) */
    .fi-btn-color-success,
    .fi-ac-action.fi-color-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25) !important;
    }

    .fi-btn-color-success span,
    .fi-btn-color-success svg,
    .fi-ac-action.fi-color-success span,
    .fi-ac-action.fi-color-success svg {
        color: #ffffff !important;
        fill: #ffffff !important;
    }

    .fi-btn-color-success:hover,
    .fi-ac-action.fi-color-success:hover {
        background: linear-gradient(135deg, #047857 0%, #059669 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4) !important;
    }

    /* Table Column Text Success Cell System (Zero Dark Box) */
    .fi-ta-cell .fi-color-success,
    .fi-ta-cell .fi-badge-color-success,
    .fi-ta-cell .fi-ta-text-item-color-success,
    .fi-ta-cell [class*="color-success"] {
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
        color: #059669 !important;
        font-weight: 700 !important;
    }

    .fi-ta-cell .fi-color-success *,
    .fi-ta-cell .fi-badge-color-success *,
    .fi-ta-cell .fi-ta-text-item-color-success *,
    .fi-ta-cell [class*="color-success"] * {
        background: transparent !important;
        background-color: transparent !important;
        color: #059669 !important;
    }

    .dark .fi-ta-cell .fi-color-success,
    .dark .fi-ta-cell .fi-badge-color-success,
    .dark .fi-ta-cell .fi-ta-text-item-color-success,
    .dark .fi-ta-cell [class*="color-success"],
    .dark .fi-ta-cell .fi-color-success *,
    .dark .fi-ta-cell .fi-badge-color-success *,
    .dark .fi-ta-cell [class*="color-success"] * {
        background: transparent !important;
        background-color: transparent !important;
        color: #34d399 !important;
    }

    /* 4. Danger Action Buttons (Rose Gradient & Crimson Glow) */
    .fi-btn-color-danger,
    .fi-ac-action.fi-color-danger {
        background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(225, 29, 72, 0.25) !important;
    }

    .fi-btn-color-danger span,
    .fi-btn-color-danger svg,
    .fi-ac-action.fi-color-danger span,
    .fi-ac-action.fi-color-danger svg {
        color: #ffffff !important;
        fill: #ffffff !important;
    }

    .fi-btn-color-danger:hover,
    .fi-ac-action.fi-color-danger:hover {
        background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 14px rgba(225, 29, 72, 0.4) !important;
    }

    /* 5. Proportional Table Row Quick Action Buttons (.fi-ta-actions) */
    .fi-ta-actions .fi-btn,
    .fi-ta-actions .fi-ac-action,
    .fi-ta-cell .fi-btn,
    .fi-ta-cell .fi-ac-action {
        padding: 5px 12px !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        border-radius: 6px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        line-height: 1.25 !important;
    }

    .fi-ta-actions .fi-btn-color-primary,
    .fi-ta-actions .fi-color-primary,
    .fi-ta-cell .fi-btn-color-primary,
    .fi-ta-cell .fi-color-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
        color: #ffffff !important;
    }

    .fi-ta-actions .fi-btn-color-success,
    .fi-ta-actions .fi-color-success,
    .fi-ta-cell .fi-btn-color-success,
    .fi-ta-cell .fi-color-success {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
    }

    .fi-ta-actions .fi-btn span,
    .fi-ta-actions .fi-btn svg,
    .fi-ta-actions .fi-ac-action span,
    .fi-ta-actions .fi-ac-action svg,
    .fi-ta-cell .fi-btn span,
    .fi-ta-cell .fi-btn svg {
        color: #ffffff !important;
        fill: #ffffff !important;
    }

    /* 5. Neutral / Outlined / Secondary Buttons Across All Menus (Panduan Button & Neutral Actions) */
    .fi-btn-color-gray,
    .fi-btn-outlined,
    .fi-ac-action.fi-color-gray,
    .fi-ta-filters-trigger,
    .fi-icon-btn-color-gray {
        background: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    }

    .fi-btn-color-gray:hover,
    .fi-btn-outlined:hover,
    .fi-ac-action.fi-color-gray:hover,
    .fi-ta-filters-trigger:hover,
    .fi-icon-btn-color-gray:hover {
        background: #f1f5f9 !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08) !important;
    }

    .dark .fi-btn-color-gray,
    .dark .fi-btn-outlined,
    .dark .fi-ac-action.fi-color-gray,
    .dark .fi-ta-filters-trigger,
    .dark .fi-icon-btn-color-gray,
    html.dark .fi-btn-color-gray,
    html.dark .fi-btn-outlined,
    html.dark .fi-ac-action.fi-color-gray,
    html.dark .fi-ta-filters-trigger,
    html.dark .fi-icon-btn-color-gray,
    body.dark .fi-btn-color-gray,
    body.dark .fi-btn-outlined,
    body.dark .fi-ac-action.fi-color-gray,
    body.dark .fi-ta-filters-trigger,
    body.dark .fi-icon-btn-color-gray {
        background: rgba(30, 41, 59, 0.6) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #cbd5e1 !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
    }

    .dark .fi-btn-color-gray:hover,
    .dark .fi-btn-outlined:hover,
    .dark .fi-ac-action.fi-color-gray:hover,
    .dark .fi-ta-filters-trigger:hover,
    .dark .fi-icon-btn-color-gray:hover,
    html.dark .fi-btn-color-gray:hover,
    html.dark .fi-btn-outlined:hover,
    html.dark .fi-ac-action.fi-color-gray:hover,
    html.dark .fi-ta-filters-trigger:hover,
    html.dark .fi-icon-btn-color-gray:hover,
    body.dark .fi-btn-color-gray:hover,
    body.dark .fi-btn-outlined:hover,
    body.dark .fi-ac-action.fi-color-gray:hover,
    body.dark .fi-ta-filters-trigger:hover,
    body.dark .fi-icon-btn-color-gray:hover {
        background: rgba(51, 65, 85, 0.8) !important;
        border-color: rgba(99, 102, 241, 0.45) !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25), 0 0 12px rgba(99, 102, 241, 0.2) !important;
    }

    /* 6. Action Group Dropdown Triggers & Table Quick Action Buttons */
    .fi-ta-actions .fi-btn,
    .fi-dropdown-trigger .fi-btn {
        border-radius: 8px !important;
    }

    /* Proportional 3-Dots Table Action Trigger */
    .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn,
    .fi-ta-actions .fi-dropdown-trigger button,
    .fi-ta-cell .fi-dropdown-trigger button {
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
        border-radius: 6px !important;
        padding: 5px !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }

    .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn svg,
    .fi-ta-actions .fi-dropdown-trigger button svg,
    .fi-ta-cell .fi-dropdown-trigger button svg {
        color: #475569 !important;
        fill: #475569 !important;
    }

    .dark .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn,
    .dark .fi-ta-actions .fi-dropdown-trigger button,
    .dark .fi-ta-cell .fi-dropdown-trigger button,
    html.dark .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn,
    html.dark .fi-ta-actions .fi-dropdown-trigger button,
    html.dark .fi-ta-cell .fi-dropdown-trigger button,
    body.dark .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn,
    body.dark .fi-ta-actions .fi-dropdown-trigger button,
    body.dark .fi-ta-cell .fi-dropdown-trigger button {
        background: rgba(30, 41, 59, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #cbd5e1 !important;
    }

    .dark .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn svg,
    .dark .fi-ta-actions .fi-dropdown-trigger button svg,
    .dark .fi-ta-cell .fi-dropdown-trigger button svg,
    html.dark .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn svg,
    html.dark .fi-ta-actions .fi-dropdown-trigger button svg,
    html.dark .fi-ta-cell .fi-dropdown-trigger button svg,
    body.dark .fi-ta-actions .fi-dropdown-trigger .fi-icon-btn svg,
    body.dark .fi-ta-actions .fi-dropdown-trigger button svg,
    body.dark .fi-ta-cell .fi-dropdown-trigger button svg {
        color: #cbd5e1 !important;
        fill: #cbd5e1 !important;
    }

    .fi-dropdown-list-item {
        transition: var(--devcalc-transition) !important;
        border-radius: 6px !important;
        margin: 2px 4px !important;
    }

    .fi-dropdown-list-item:hover {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(59, 130, 246, 0.12) 100%) !important;
        color: #818cf8 !important;
    }

    /* 7. Segmented Filter Tabs & Navigation Buttons (.fi-tabs) */
    .fi-tabs {
        background: #f1f5f9 !important;
        padding: 4px !important;
        border-radius: 12px !important;
        border: 1px solid #cbd5e1 !important;
    }

    .dark .fi-tabs,
    html.dark .fi-tabs,
    body.dark .fi-tabs {
        background: rgba(15, 23, 42, 0.6) !important;
        padding: 4px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        backdrop-filter: blur(12px) !important;
    }

    .fi-tabs-item {
        border-radius: 8px !important;
        transition: var(--devcalc-transition) !important;
        font-weight: 600 !important;
        padding: 6px 12px !important;
        color: #475569 !important;
    }

    .fi-tabs-item span,
    .fi-tabs-item .fi-tabs-item-label {
        color: #475569 !important;
    }

    .dark .fi-tabs-item,
    .dark .fi-tabs-item span,
    html.dark .fi-tabs-item,
    body.dark .fi-tabs-item {
        color: #94a3b8 !important;
    }

    .fi-tabs-item:hover:not(.fi-tabs-item-active) {
        background: rgba(0, 0, 0, 0.05) !important;
    }

    .fi-tabs-item:hover:not(.fi-tabs-item-active) span {
        color: #0f172a !important;
    }

    .dark .fi-tabs-item:hover:not(.fi-tabs-item-active),
    html.dark .fi-tabs-item:hover:not(.fi-tabs-item-active),
    body.dark .fi-tabs-item:hover:not(.fi-tabs-item-active) {
        background: rgba(255, 255, 255, 0.05) !important;
    }

    .dark .fi-tabs-item:hover:not(.fi-tabs-item-active) span {
        color: #f1f5f9 !important;
    }

    .fi-tabs-item-active {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 10px rgba(79, 70, 229, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
    }

    .fi-tabs-item-active *,
    .fi-tabs-item-active span,
    .fi-tabs-item-active .fi-tabs-item-label {
        background: transparent !important;
        color: #ffffff !important;
    }

    .fi-tabs-item-active .fi-badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    .fi-tabs-item-active .fi-badge * {
        background: transparent !important;
        color: #ffffff !important;
    }

    /* 8. Silky Table Row & Card Hover */
    .fi-ta-table tbody tr {
        transition: var(--devcalc-transition);
    }

    .fi-ta-table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.03) !important;
    }

    .dark .fi-ta-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }

    /* 9. Polished Glassmorphism Modals */
    .fi-modal-window {
        backdrop-filter: blur(16px) saturate(180%);
        border-radius: 16px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* 10. Smooth Input Focus Rings */
    .fi-input-wrp {
        transition: var(--devcalc-transition);
        border-radius: 8px !important;
    }

    .fi-input-wrp:focus-within {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        border-color: rgba(99, 102, 241, 0.6) !important;
    }

    /* 11. Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.6);
    }

    /* 12. Quick Calculator Widget & Form Theme Engine (100% Light Mode & Dark Mode Harmony) */
    
    /* Input, Select & Textarea Default (Light Mode) */
    .devcalc-calc-input {
        width: 100%;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
    }

    .devcalc-calc-input option {
        background-color: #ffffff;
        color: #0f172a;
    }

    /* Dark Mode Overrides (Explicit !important for Dark Mode) */
    .dark .devcalc-calc-input,
    html.dark .devcalc-calc-input,
    body.dark .devcalc-calc-input,
    .fi-body.dark .devcalc-calc-input,
    [data-theme="dark"] .devcalc-calc-input,
    .dark input.devcalc-calc-input,
    .dark select.devcalc-calc-input,
    .dark textarea.devcalc-calc-input {
        background-color: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #f8fafc !important;
    }

    .dark .devcalc-calc-input option,
    html.dark .devcalc-calc-input option,
    body.dark .devcalc-calc-input option,
    .fi-body.dark .devcalc-calc-input option,
    [data-theme="dark"] .devcalc-calc-input option {
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }

    /* General Filament Form Input & Textarea Dark Mode Guard */
    .dark .fi-input,
    .dark .fi-select-input,
    .dark textarea {
        background-color: #1e293b !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }

    /* Switcher Container */
    .devcalc-calc-switcher {
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
    }
    .dark .devcalc-calc-switcher,
    html.dark .devcalc-calc-switcher,
    body.dark .devcalc-calc-switcher {
        background-color: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .devcalc-tab-inactive {
        color: #475569;
    }
    .dark .devcalc-tab-inactive,
    html.dark .devcalc-tab-inactive,
    body.dark .devcalc-tab-inactive {
        color: #94a3b8 !important;
    }

    /* Form Labels */
    .devcalc-calc-label {
        color: #334155;
        font-weight: 600;
    }
    .dark .devcalc-calc-label,
    html.dark .devcalc-calc-label,
    body.dark .devcalc-calc-label {
        color: #94a3b8 !important;
        font-weight: 600 !important;
    }

    /* One-Off Result Card (Beli Putus) */
    .devcalc-card-oneoff {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 1.5px solid #818cf8;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
    }
    .dark .devcalc-card-oneoff,
    html.dark .devcalc-card-oneoff,
    body.dark .devcalc-card-oneoff {
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.35) 0%, rgba(79, 70, 229, 0.25) 100%) !important;
        border: 1.5px solid rgba(99, 102, 241, 0.5) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    }

    .devcalc-card-oneoff-title {
        color: #3730a3;
        font-weight: 800;
    }
    .dark .devcalc-card-oneoff-title,
    html.dark .devcalc-card-oneoff-title,
    body.dark .devcalc-card-oneoff-title {
        color: #93c5fd !important;
        font-weight: 700 !important;
    }

    .devcalc-card-oneoff-badge {
        background-color: #4f46e5;
        color: #ffffff;
    }
    .dark .devcalc-card-oneoff-badge,
    html.dark .devcalc-card-oneoff-badge,
    body.dark .devcalc-card-oneoff-badge {
        background-color: rgba(99, 102, 241, 0.3) !important;
        color: #c7d2fe !important;
    }

    .devcalc-card-oneoff-price {
        color: #1e1b4b;
        font-weight: 900;
    }
    .dark .devcalc-card-oneoff-price,
    html.dark .devcalc-card-oneoff-price,
    body.dark .devcalc-card-oneoff-price {
        color: #60a5fa !important;
        font-weight: 900 !important;
    }

    .devcalc-card-oneoff-sub {
        color: #4338ca;
        font-weight: 700;
    }
    .dark .devcalc-card-oneoff-sub,
    html.dark .devcalc-card-oneoff-sub,
    body.dark .devcalc-card-oneoff-sub {
        color: #cbd5e1 !important;
        font-weight: 500 !important;
    }

    /* SaaS Result Card (Langganan SaaS) */
    .devcalc-card-saas {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1.5px solid #34d399;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.12);
    }
    .dark .devcalc-card-saas,
    html.dark .devcalc-card-saas,
    body.dark .devcalc-card-saas {
        background: linear-gradient(135deg, rgba(6, 95, 70, 0.35) 0%, rgba(16, 185, 129, 0.25) 100%) !important;
        border: 1.5px solid rgba(16, 185, 129, 0.5) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    }

    .devcalc-card-saas-title {
        color: #065f46;
        font-weight: 800;
    }
    .dark .devcalc-card-saas-title,
    html.dark .devcalc-card-saas-title,
    body.dark .devcalc-card-saas-title {
        color: #6ee7b7 !important;
        font-weight: 700 !important;
    }

    .devcalc-card-saas-badge {
        background-color: #059669;
        color: #ffffff;
    }
    .dark .devcalc-card-saas-badge,
    html.dark .devcalc-card-saas-badge,
    body.dark .devcalc-card-saas-badge {
        background-color: rgba(16, 185, 129, 0.3) !important;
        color: #a7f3d0 !important;
    }

    .devcalc-card-saas-price {
        color: #064e3b;
        font-weight: 900;
    }
    .dark .devcalc-card-saas-price,
    html.dark .devcalc-card-saas-price,
    body.dark .devcalc-card-saas-price {
        color: #34d399 !important;
        font-weight: 900 !important;
    }

    .devcalc-card-saas-sub {
        color: #047857;
        font-weight: 700;
    }
    .dark .devcalc-card-saas-sub,
    html.dark .devcalc-card-saas-sub,
    body.dark .devcalc-card-saas-sub {
        color: #a7f3d0 !important;
        font-weight: 500 !important;
    }
</style>
