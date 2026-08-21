<style>
    /* Modern Minimalist Global Overhauls */
    :root {
        --devcalc-transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Silky Table Hover & Row Styling */
    .fi-ta-table tbody tr {
        transition: var(--devcalc-transition);
    }
    .fi-ta-table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.03) !important;
    }
    .dark .fi-ta-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }

    /* Polished Modal Dialogs with Glassmorphism */
    .fi-modal-window {
        backdrop-filter: blur(16px) saturate(180%);
        border-radius: 16px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    }

    /* Modern Table Action Buttons */
    .fi-ta-actions .fi-btn {
        transition: var(--devcalc-transition);
    }
    .fi-ta-actions .fi-btn:hover {
        transform: translateY(-1px);
    }

    /* Refined Badges with Subtle Glow */
    .fi-badge {
        font-weight: 600 !important;
        letter-spacing: 0.2px;
        transition: var(--devcalc-transition);
    }

    /* Table Filter Tabs Smooth Elevation */
    .fi-tabs-item-active {
        font-weight: 700 !important;
    }

    /* Smooth Input Focus Rings */
    .fi-input-wrp {
        transition: var(--devcalc-transition);
        border-radius: 8px !important;
    }
    .fi-input-wrp:focus-within {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }

    /* Elegant Custom Scrollbar */
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

</style>
