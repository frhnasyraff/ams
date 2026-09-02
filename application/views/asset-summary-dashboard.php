<style>
    .breakdown-container {
        max-height: 180px;
        /* set how tall before scroll kicks in */
        overflow-y: auto;
        /* enable vertical scrollbar */
        padding: 8px;
        border: 1px solid #081a38ff;
        border-radius: 6px;
        background: #012461ff;
        -webkit-overflow-scrolling: touch;
        /* smooth scroll on mobile */
    }

    .breakdown-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 8px;
        font-size: 14px;
    }
    .card-equal {
        flex: 1;              
        min-height: 390px;    
        width: 100%;          
    }
    .donut-absolute-center {
    position: absolute;
    transform: translate(-50%, -50%);
    top: 60%;
    left: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    }
    .donut-absolute-center p {
        font-size: 32px;
        font-weight: bold;
        color: white;
        margin: 0;
        text-align: center;
    }

    body:has(.asset-summary-ref-section) .summary-ref-chart-grid,
    .asset-summary-ref-section .summary-ref-chart-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 24px !important;
        align-items: stretch !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card,
    .asset-summary-ref-section .summary-ref-card,
    body:has(.asset-summary-ref-section) .fleet-insights-card,
    .asset-summary-ref-section .fleet-insights-card {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 348px !important;
        padding: 0 !important;
        position: relative !important;
        overflow: hidden !important;
        background:
            radial-gradient(circle at 92% 16%, rgba(47,128,255,.16), transparent 28%),
            linear-gradient(145deg, rgba(7,19,43,.98), rgba(10,31,74,.82)) !important;
        box-shadow: 0 18px 40px rgba(0,0,0,.26), inset 0 0 0 1px rgba(56,189,248,.04) !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card::after,
    .asset-summary-ref-section .summary-ref-card::after {
        content: '' !important;
        position: absolute !important;
        right: -62px !important;
        bottom: -62px !important;
        width: 150px !important;
        height: 150px !important;
        border-radius: 50% !important;
        background: rgba(56,189,248,.08) !important;
        pointer-events: none !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card-head,
    .asset-summary-ref-section .summary-ref-card-head {
        display: flex !important;
        align-items: flex-start !important;
        gap: 12px !important;
        min-height: 96px !important;
        padding: 20px 24px 18px !important;
        margin: 0 !important;
        border-bottom: 1px solid rgba(96,165,250,.22) !important;
        background: linear-gradient(145deg, rgba(11,42,91,.56), rgba(7,19,43,.12)) !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card-head span,
    .asset-summary-ref-section .summary-ref-card-head span {
        width: 44px !important;
        height: 44px !important;
        flex: 0 0 44px !important;
        border-radius: 14px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card-head strong,
    .asset-summary-ref-section .summary-ref-card-head strong {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
        color: #ffffff !important;
        font-size: 1.34rem !important;
        line-height: 1.08 !important;
        letter-spacing: .02em !important;
        min-width: 0 !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card-head strong::before,
    .asset-summary-ref-section .summary-ref-card-head strong::before {
        color: #35d8ff !important;
        font-size: .74rem !important;
        font-weight: 900 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card-head strong::after,
    .asset-summary-ref-section .summary-ref-card-head strong::after {
        color: #9dbff2 !important;
        font-size: .82rem !important;
        font-weight: 600 !important;
        letter-spacing: .01em !important;
        line-height: 1.35 !important;
        text-transform: none !important;
    }

    .asset-summary-ref-section .summary-ref-card:nth-child(1) .summary-ref-card-head strong::before { content: 'Inventory'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(2) .summary-ref-card-head strong::before { content: 'Coverage'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(3) .summary-ref-card-head strong::before { content: 'Availability'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(4) .summary-ref-card-head strong::before { content: 'Attention'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(5) .summary-ref-card-head strong::before { content: 'Maintenance'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(6) .summary-ref-card-head strong::before { content: 'Fleet'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(1) .summary-ref-card-head strong::after { content: 'Current asset quantity by equipment type.'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(2) .summary-ref-card-head strong::after { content: 'Where assets are currently assigned.'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(3) .summary-ref-card-head strong::after { content: 'Serviceable assets ready for operation.'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(4) .summary-ref-card-head strong::after { content: 'Unserviceable assets requiring attention.'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(5) .summary-ref-card-head strong::after { content: 'Corrective and preventive activity summary.'; }
    .asset-summary-ref-section .summary-ref-card:nth-child(6) .summary-ref-card-head strong::after { content: 'Fleet health at a glance.'; }

    body:has(.asset-summary-ref-section) .summary-ref-card-body,
    .asset-summary-ref-section .summary-ref-card-body {
        display: grid !important;
        grid-template-columns: minmax(260px, .9fr) minmax(300px, 1.1fr) !important;
        gap: 28px !important;
        align-items: center !important;
        min-height: 210px !important;
        padding: 30px 24px !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-chart-wrap,
    .asset-summary-ref-section .summary-ref-chart-wrap {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        max-width: 285px !important;
        aspect-ratio: 1 / 1 !important;
        min-height: 230px !important;
        margin: 0 auto !important;
        background:
            radial-gradient(circle at 50% 50%, rgba(5, 18, 37, .88) 0 41%, transparent 42%),
            radial-gradient(circle at 50% 50%, rgba(56,189,248,.10), transparent 72%) !important;
        border-radius: 0 !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-chart-wrap canvas,
    .asset-summary-ref-section .summary-ref-chart-wrap canvas {
        width: 100% !important;
        height: 100% !important;
        max-width: 230px !important;
        max-height: 230px !important;
        margin: 0 auto !important;
        background: transparent !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-chart-wrap .donut-absolute-center,
    .asset-summary-ref-section .summary-ref-chart-wrap .donut-absolute-center {
        position: absolute !important;
        inset: 50% auto auto 50% !important;
        transform: translate(-50%, -50%) !important;
        width: auto !important;
        height: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-chart-wrap .donut-absolute-center p,
    .asset-summary-ref-section .summary-ref-chart-wrap .donut-absolute-center p {
        width: 66px !important;
        height: 66px !important;
        border-radius: 999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        background: rgba(5, 18, 37, .92) !important;
        border: 1px solid rgba(56,189,248,.38) !important;
        color: #ffffff !important;
        font-size: 1.72rem !important;
        font-weight: 900 !important;
        box-shadow: 0 14px 30px rgba(0,0,0,.32), inset 0 1px 0 rgba(255,255,255,.08) !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .breakdown-container,
    .asset-summary-ref-section .summary-ref-card .breakdown-container,
    body:has(.asset-summary-ref-section) .summary-ref-card .breakdown-list,
    .asset-summary-ref-section .summary-ref-card .breakdown-list {
        width: 100% !important;
        min-width: 0 !important;
        max-height: 178px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 0 6px 0 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .breakdown-headings,
    .asset-summary-ref-section .summary-ref-card .breakdown-headings,
    body:has(.asset-summary-ref-section) .summary-ref-card .breakdown-item,
    .asset-summary-ref-section .summary-ref-card .breakdown-item {
        display: grid !important;
        grid-template-columns: 14px minmax(0, 1fr) auto !important;
        gap: 12px !important;
        align-items: center !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .breakdown-headings,
    .asset-summary-ref-section .summary-ref-card .breakdown-headings,
    body:has(.asset-summary-ref-section) .summary-ref-card hr,
    .asset-summary-ref-section .summary-ref-card hr {
        display: none !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .summary-metric-pill,
    .asset-summary-ref-section .summary-ref-card .summary-metric-pill {
        min-height: 42px !important;
        margin: 0 0 12px !important;
        padding: 10px 14px !important;
        border-radius: 10px !important;
        background: rgba(8, 25, 49, .78) !important;
        border: 1px solid rgba(96,165,250,.24) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.04) !important;
        color: #dbeafe !important;
        font-size: .86rem !important;
        font-weight: 800 !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .summary-metric-pill:hover,
    .asset-summary-ref-section .summary-ref-card .summary-metric-pill:hover {
        border-color: rgba(56,189,248,.46) !important;
        background: rgba(15,42,86,.88) !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .summary-metric-dot,
    .asset-summary-ref-section .summary-ref-card .summary-metric-dot {
        width: 10px !important;
        height: 10px !important;
        border-radius: 999px !important;
        box-shadow: 0 0 14px currentColor !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .summary-metric-pill > .type,
    .asset-summary-ref-section .summary-ref-card .summary-metric-pill > .type {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        color: #c7ddff !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-card .summary-metric-pill > .total,
    .asset-summary-ref-section .summary-ref-card .summary-metric-pill > .total {
        text-align: right !important;
        white-space: nowrap !important;
        color: #ffffff !important;
        font-size: 1rem !important;
        font-weight: 900 !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-footer,
    .asset-summary-ref-section .summary-ref-footer,
    body:has(.asset-summary-ref-section) .summary-ref-card:not(.fleet-insights-card) .fleet-status-strip,
    .asset-summary-ref-section .summary-ref-card:not(.fleet-insights-card) .fleet-status-strip {
        position: absolute !important;
        top: 20px !important;
        right: 22px !important;
        width: auto !important;
        min-height: 34px !important;
        margin: 0 !important;
        padding: 8px 14px !important;
        border-radius: 999px !important;
        background: rgba(15,42,86,.78) !important;
        border: 1px solid rgba(96,165,250,.26) !important;
        color: #c7ddff !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.04) !important;
    }

    body:has(.asset-summary-ref-section) .fleet-insights-card .fleet-status-strip,
    .asset-summary-ref-section .fleet-insights-card .fleet-status-strip {
        position: static !important;
        width: 100% !important;
        min-height: 54px !important;
        margin: 14px 0 0 !important;
        padding: 12px 14px !important;
        border-radius: 14px !important;
        background: rgba(8, 25, 49, .78) !important;
        border: 1px solid rgba(96,165,250,.24) !important;
        color: #dbeafe !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.04) !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-footer span,
    .asset-summary-ref-section .summary-ref-footer span {
        display: none !important;
    }

    body:has(.asset-summary-ref-section) .summary-ref-footer small,
    .asset-summary-ref-section .summary-ref-footer small,
    body:has(.asset-summary-ref-section) .fleet-status-strip small,
    .asset-summary-ref-section .fleet-status-strip small {
        color: #dbeafe !important;
        font-size: .8rem !important;
        font-weight: 800 !important;
        white-space: nowrap !important;
    }

    body:has(.asset-summary-ref-section) .fleet-insights-card .fleet-insight-grid,
    .asset-summary-ref-section .fleet-insights-card .fleet-insight-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    @media (max-width: 1100px) {
        body:has(.asset-summary-ref-section) .summary-ref-chart-grid,
        .asset-summary-ref-section .summary-ref-chart-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 760px) {
        body:has(.asset-summary-ref-section) .summary-ref-card-body,
        .asset-summary-ref-section .summary-ref-card-body {
            grid-template-columns: 1fr !important;
        }

        body:has(.asset-summary-ref-section) .fleet-insights-card .fleet-insight-grid,
        .asset-summary-ref-section .fleet-insights-card .fleet-insight-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* Asset Summary command centre — aligned with the Corrective Maintenance UI. */
    .asset-command-page {
        --asset-panel: #081a33;
        --asset-panel-deep: #061326;
        --asset-line: rgba(56, 139, 216, .34);
        width: 100%;
        max-width: 1480px;
        margin: 18px auto 56px;
        display: grid;
        gap: 20px;
        color: #eaf4ff;
    }

    .asset-command-hero {
        position: relative;
        min-height: 126px;
        padding: 25px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        overflow: hidden;
        border: 1px solid rgba(48, 143, 230, .46);
        border-radius: 22px;
        background:
            radial-gradient(circle at 78% 0%, rgba(47, 128, 255, .22), transparent 36%),
            linear-gradient(118deg, #07182f 0%, #0a2345 63%, #123b75 100%);
        box-shadow: 0 24px 50px rgba(0, 0, 0, .3), inset 0 1px 0 rgba(255, 255, 255, .05);
    }

    .asset-command-hero::after {
        content: '';
        position: absolute;
        right: 18%;
        bottom: -74px;
        width: 190px;
        height: 190px;
        border: 36px solid rgba(53, 213, 255, .055);
        border-radius: 50%;
        pointer-events: none;
    }

    .asset-command-hero__copy,
    .asset-command-hero__status {
        position: relative;
        z-index: 1;
    }

    .asset-command-hero__copy {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .asset-command-hero__icon,
    .asset-command-kpi__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        color: #35d5ff;
        border: 1px solid rgba(53, 213, 255, .42);
        background: linear-gradient(145deg, rgba(12, 64, 108, .9), rgba(5, 29, 59, .94));
    }

    .asset-command-hero__icon {
        width: 58px;
        height: 58px;
        border-radius: 17px;
        font-size: 1.45rem;
        box-shadow: 0 12px 28px rgba(17, 162, 218, .18);
    }

    .asset-command-eyebrow,
    .asset-command-kpi__label,
    .asset-section-kicker,
    .asset-panel-kicker {
        display: block;
        color: #55d9ff;
        font-size: .68rem;
        font-weight: 900;
        letter-spacing: .105em;
        text-transform: uppercase;
    }

    .asset-command-hero h2 {
        margin: 4px 0 5px;
        color: #fff !important;
        font-size: clamp(1.5rem, 2.2vw, 2rem);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: .02em;
    }

    .asset-command-hero p,
    .asset-section-heading p,
    .asset-command-panel__head p,
    .summary-ref-card-head p {
        margin: 0;
        color: #9bb7db !important;
        font-size: .82rem;
        line-height: 1.5;
    }

    .asset-command-hero__status {
        min-width: 275px;
        padding: 15px 18px;
        display: grid;
        gap: 3px;
        border: 1px solid rgba(123, 190, 255, .26);
        border-radius: 15px;
        background: rgba(3, 18, 38, .48);
        backdrop-filter: blur(8px);
    }

    .asset-command-live {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #47e6b1;
        font-size: .68rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .asset-command-live i {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2dd4a7;
        box-shadow: 0 0 0 4px rgba(45, 212, 167, .12), 0 0 15px rgba(45, 212, 167, .55);
    }

    .asset-command-hero__status strong { color: #fff; font-size: .9rem; font-weight: 800; }
    .asset-command-hero__status small { color: #8faed4; font-size: .7rem; }

    .asset-command-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
    }

    .asset-command-kpi {
        --kpi-accent: #35d5ff;
        --kpi-border: rgba(53, 213, 255, .3);
        --kpi-glow: rgba(53, 213, 255, .09);
        position: relative;
        min-height: 112px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        overflow: hidden;
        border: 1px solid var(--kpi-border);
        border-radius: 18px;
        background: linear-gradient(145deg, rgba(9, 31, 61, .98), rgba(6, 20, 41, .98));
        box-shadow: 0 16px 32px rgba(0, 0, 0, .22), inset 0 1px 0 rgba(255, 255, 255, .035);
    }

    .asset-command-kpi::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -45px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: var(--kpi-glow);
    }

    .asset-command-kpi__icon {
        width: 45px;
        height: 45px;
        border-radius: 14px;
        color: var(--kpi-accent);
        border-color: var(--kpi-border);
        background: var(--kpi-glow);
    }

    .asset-command-kpi strong {
        display: block;
        margin: 2px 0 0;
        color: var(--kpi-accent);
        font-size: 1.75rem;
        font-weight: 900;
        line-height: 1;
    }

    .asset-command-kpi small { color: #829dbc; font-size: .68rem; }
    .asset-command-kpi--green { --kpi-accent: #42dfb4; --kpi-border: rgba(45, 212, 167, .35); --kpi-glow: rgba(45, 212, 167, .09); }
    .asset-command-kpi--violet { --kpi-accent: #a883ff; --kpi-border: rgba(155, 109, 255, .35); --kpi-glow: rgba(155, 109, 255, .09); }
    .asset-command-kpi--amber { --kpi-accent: #ffb629; --kpi-border: rgba(245, 158, 11, .35); --kpi-glow: rgba(245, 158, 11, .09); }

    .asset-command-legacy-alerts,
    .asset-command-legacy-kpis { display: none !important; }

    .asset-command-layout,
    .asset-command-layout > .asset-summary-ref-section,
    .asset-command-layout > .asset-summary-ref-section > .col-12,
    .asset-command-layout .summary-ref-shell {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .asset-section-heading {
        min-height: 86px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border: 1px solid var(--asset-line);
        border-radius: 20px;
        background: linear-gradient(90deg, rgba(15, 47, 83, .82), rgba(8, 28, 57, .42));
        box-shadow: 0 18px 38px rgba(0,0,0,.2), inset 0 1px 0 rgba(255,255,255,.04);
    }

    .asset-section-heading__copy { display: flex; align-items: center; gap: 14px; }
    .asset-section-heading__icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        color: #bba3ff;
        border: 1px solid rgba(155,109,255,.4);
        background: rgba(96,61,169,.28);
        font-size: 1.2rem;
    }

    .asset-section-heading h3,
    .asset-command-panel__head h3 {
        margin: 2px 0 3px;
        color: #f4f8ff !important;
        font-size: 1.08rem;
        font-weight: 900;
        letter-spacing: .02em;
    }

    .asset-section-heading__badge,
    .summary-ref-card-head em,
    .asset-command-panel__badge {
        flex: 0 0 auto;
        padding: 8px 11px;
        border: 1px solid rgba(53, 213, 255, .28);
        border-radius: 999px;
        color: #67ddff;
        background: rgba(14, 71, 111, .35);
        font-size: .68rem;
        font-style: normal;
        font-weight: 800;
        white-space: nowrap;
    }

    .asset-command-page .summary-ref-chart-grid {
        margin-top: 20px !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
    }

    .asset-command-page .summary-ref-card,
    .asset-command-page .fleet-insights-card,
    .asset-command-panel {
        min-width: 0 !important;
        min-height: 0 !important;
        overflow: hidden !important;
        border: 1px solid var(--asset-line) !important;
        border-radius: 20px !important;
        background: linear-gradient(145deg, rgba(8, 27, 53, .98), rgba(5, 17, 36, .99)) !important;
        box-shadow: 0 20px 42px rgba(0, 0, 0, .25), inset 0 1px 0 rgba(255, 255, 255, .035) !important;
    }

    .asset-command-page .summary-ref-card-purple { border-color: rgba(155,109,255,.42) !important; }
    .asset-command-page .summary-ref-card-red { border-color: rgba(244,63,94,.46) !important; }

    .asset-command-page .summary-ref-card::after { display: none !important; }

    .asset-command-page .summary-ref-card-head {
        min-height: 98px !important;
        padding: 18px 20px !important;
        display: grid !important;
        grid-template-columns: 46px minmax(0, 1fr) auto !important;
        align-items: center !important;
        gap: 14px !important;
        border-bottom: 1px solid rgba(55, 119, 184, .25) !important;
        background: linear-gradient(90deg, rgba(15, 47, 83, .68), rgba(8, 28, 57, .16)) !important;
    }

    .asset-command-page .summary-ref-card-head > span {
        width: 46px !important;
        height: 46px !important;
        border-radius: 14px !important;
        color: #35d5ff !important;
        border: 1px solid rgba(53,213,255,.32) !important;
        background: rgba(14,71,111,.38) !important;
    }

    .asset-command-page .summary-ref-card-head > div { min-width: 0; }
    .asset-command-page .summary-ref-card-head small {
        display: block;
        margin-bottom: 2px;
        color: #55d9ff !important;
        font-size: .68rem;
        font-weight: 900;
        letter-spacing: .105em;
        text-transform: uppercase;
    }

    .asset-command-page .summary-ref-card-head strong {
        display: block !important;
        color: #f4f8ff !important;
        font-size: 1.05rem !important;
        line-height: 1.2 !important;
    }

    .asset-command-page .summary-ref-card-head strong::before,
    .asset-command-page .summary-ref-card-head strong::after { display: none !important; }

    .asset-command-page .summary-ref-card-body {
        min-height: 270px !important;
        padding: 22px !important;
        display: grid !important;
        grid-template-columns: minmax(210px, .86fr) minmax(240px, 1.14fr) !important;
        align-items: center !important;
        gap: 22px !important;
    }

    .asset-command-page .summary-ref-chart-wrap {
        width: 100% !important;
        max-width: 250px !important;
        min-height: 210px !important;
        aspect-ratio: 1 / 1 !important;
        margin: 0 auto !important;
        background: #041126 !important;
    }

    .asset-command-page .summary-ref-chart-wrap canvas {
        max-width: 210px !important;
        max-height: 210px !important;
    }

    .asset-command-page .summary-ref-chart-wrap .donut-absolute-center p {
        width: auto !important;
        height: auto !important;
        min-width: 70px !important;
        min-height: 70px !important;
        padding: 10px !important;
        border: 0 !important;
        background: transparent !important;
        font-size: 1.65rem !important;
        box-shadow: none !important;
    }

    .asset-command-page .breakdown-container {
        max-height: 216px !important;
        padding: 0 7px 0 0 !important;
        scrollbar-width: thin;
        scrollbar-color: #245d98 #07162b;
    }

    .asset-command-page .summary-metric-pill {
        min-height: 44px !important;
        margin: 0 0 9px !important;
        padding: 9px 12px !important;
        border: 1px solid rgba(62, 124, 186, .25) !important;
        border-radius: 11px !important;
        background: rgba(8, 29, 56, .72) !important;
    }

    .asset-command-page .summary-ref-footer { display: none !important; }

    .asset-command-page .fleet-insights-card .fleet-insight-grid {
        min-height: 270px;
        padding: 22px !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    .asset-command-page .fleet-insight-box {
        min-height: 104px;
        padding: 15px !important;
        display: grid;
        grid-template-columns: 42px 1fr;
        grid-template-rows: auto auto auto;
        column-gap: 12px;
        align-items: center;
        border: 1px solid rgba(62,124,186,.24) !important;
        border-radius: 14px !important;
        background: rgba(8,29,56,.7) !important;
    }

    .asset-command-page .fleet-insight-box .fleet-icon { grid-row: 1 / 4; }
    .asset-command-page .fleet-insight-box small { color: #8faed4 !important; font-size: .68rem !important; }
    .asset-command-page .fleet-insight-box strong { color: #fff !important; font-size: 1.45rem !important; line-height: 1 !important; }
    .asset-command-page .fleet-insight-box em { color: #6f91b8 !important; font-size: .64rem !important; font-style: normal !important; }
    .asset-command-page .fleet-status-strip {
        margin: 0 22px 22px !important;
        padding: 11px 13px !important;
        border: 1px solid rgba(45,212,167,.28) !important;
        border-radius: 12px !important;
        background: rgba(21,114,89,.16) !important;
    }

    .asset-component-panel { padding: 0; }
    .asset-command-panel__head {
        min-height: 88px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        border-bottom: 1px solid rgba(55, 119, 184, .25);
        background: linear-gradient(90deg, rgba(15, 47, 83, .68), rgba(8, 28, 57, .16));
    }

    .asset-component-grid {
        padding: 18px 20px;
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
    }

    .asset-component-metric {
        min-height: 84px;
        padding: 13px;
        display: flex;
        align-items: center;
        gap: 11px;
        border: 1px solid rgba(62,124,186,.24);
        border-radius: 13px;
        background: rgba(8,29,56,.68);
    }

    .asset-component-metric i { color: #55d9ff; font-size: 1rem; }
    .asset-component-metric span { display: block; color: #8faed4; font-size: .66rem; font-weight: 800; }
    .asset-component-metric strong { display: block; color: #fff; font-size: 1.25rem; font-weight: 900; }

    .asset-command-operations {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(360px, .75fr);
        gap: 20px;
    }

    .asset-command-table-panel,
    .asset-command-map-panel { margin: 0 !important; padding: 0 !important; max-width: none !important; }
    .asset-command-table-host { padding: 16px 18px 18px; overflow-x: auto; }
    .asset-command-map-body { padding: 18px; }
    .asset-command-map-body #map { width: 100% !important; min-height: 350px; border-radius: 15px; overflow: hidden; }
    .asset-command-map-body .quake-info {
        margin-top: 12px;
        padding: 12px 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px 16px;
        border: 1px solid rgba(62,124,186,.24);
        border-radius: 12px;
        background: rgba(8,29,56,.68);
        color: #a9c4e3;
        font-size: .72rem;
    }

    @media (max-width: 1199px) {
        .asset-command-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .asset-component-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .asset-command-operations { grid-template-columns: 1fr; }
    }

    @media (max-width: 1050px) {
        .asset-command-page .summary-ref-chart-grid { grid-template-columns: 1fr !important; }
    }

    @media (max-width: 760px) {
        .asset-command-page { margin-top: 8px; gap: 14px; }
        .asset-command-hero { padding: 20px; align-items: flex-start; flex-direction: column; }
        .asset-command-hero__status { width: 100%; min-width: 0; }
        .asset-command-kpi-grid { grid-template-columns: 1fr; gap: 10px; }
        .asset-section-heading,
        .asset-command-panel__head { align-items: flex-start; flex-direction: column; }
        .asset-command-page .summary-ref-card-head { grid-template-columns: 46px minmax(0, 1fr) !important; }
        .asset-command-page .summary-ref-card-head em { grid-column: 1 / -1; margin-left: 60px; }
        .asset-command-page .summary-ref-card-body { grid-template-columns: 1fr !important; }
        .asset-component-grid,
        .asset-command-page .fleet-insights-card .fleet-insight-grid { grid-template-columns: 1fr !important; }
        .asset-command-map-body .quake-info { grid-template-columns: 1fr; }
    }

    /* Final layout guard: override the fixed legacy dashboard heights. */
    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-chart-grid {
        align-items: stretch !important;
        grid-auto-rows: auto !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card,
    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        align-self: stretch !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card-body {
        height: auto !important;
        min-height: 310px !important;
        max-height: none !important;
        overflow: visible !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-chart-wrap {
        flex: 0 0 auto !important;
        overflow: visible !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card .fleet-insight-grid {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card .fleet-status-strip {
        position: static !important;
        display: flex !important;
        align-items: center !important;
        width: auto !important;
        height: auto !important;
        min-height: 58px !important;
        margin: 0 22px 22px !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-operations {
        width: 100% !important;
        min-width: 0 !important;
        align-items: start !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel,
    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-map-panel {
        width: 100% !important;
        min-width: 0 !important;
        height: auto !important;
        max-height: none !important;
        align-self: start !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-host {
        min-width: 0 !important;
        overflow-x: auto !important;
        overflow-y: visible !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow: visible !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-map-body {
        position: relative !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-map-body #map {
        position: relative !important;
        inset: auto !important;
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 350px !important;
        min-height: 350px !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-map-body .quake-info {
        position: static !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 12px 0 0 !important;
        box-sizing: border-box !important;
    }

    /* Keep the location register footer and every pagination control visible. */
    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper > .row:last-child {
        width: 100% !important;
        margin: 14px 0 0 !important;
        display: grid !important;
        grid-template-columns: minmax(210px, 1fr) auto !important;
        align-items: center !important;
        gap: 14px !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper > .row:last-child > [class*="col-"] {
        width: auto !important;
        max-width: none !important;
        min-width: 0 !important;
        padding: 0 !important;
        flex: none !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_info {
        margin: 0 !important;
        padding: 0 !important;
        white-space: nowrap !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate {
        width: auto !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        white-space: nowrap !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .pagination {
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        flex-wrap: nowrap !important;
        gap: 6px !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .paginate_button,
    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .page-item {
        display: inline-flex !important;
        flex: 0 0 auto !important;
        margin: 0 !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .page-link {
        min-width: 42px !important;
        height: 42px !important;
        padding: 0 11px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid rgba(72, 139, 207, .34) !important;
        border-radius: 11px !important;
        color: #a9c2e2 !important;
        background: #091d37 !important;
        box-shadow: none !important;
        font-size: .72rem !important;
        font-weight: 800 !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .previous .page-link,
    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .next .page-link {
        min-width: 76px !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .page-item.active .page-link {
        color: #ffffff !important;
        border-color: #4aa4ff !important;
        background: linear-gradient(135deg, #2f6ff2, #28b8f1) !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .page-item.disabled {
        display: inline-flex !important;
        opacity: .38 !important;
    }

    /* Give the chart breakdown pills equal breathing room on both edges. */
    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card .breakdown-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 2px 12px 4px !important;
        box-sizing: border-box !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card .summary-metric-pill {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 0 10px !important;
        box-sizing: border-box !important;
    }

    /* Each card already has its total badge in the header. The legacy footer
       was also being promoted into the same position, creating a duplicate. */
    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card > .summary-ref-footer {
        display: none !important;
    }

    /* Status title and supporting copy must be separate readable lines. */
    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card .fleet-status-strip > div {
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 4px !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card .fleet-status-strip strong,
    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card .fleet-status-strip small {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card .fleet-status-strip strong {
        color: #ffffff !important;
        font-size: 1rem !important;
        line-height: 1.2 !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .fleet-insights-card .fleet-status-strip small {
        color: #8faed4 !important;
        font-size: .72rem !important;
        line-height: 1.4 !important;
    }

    /* Pin every card total/status badge to the far-right edge of its header. */
    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card-head {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: 46px minmax(0, 1fr) auto !important;
        align-items: center !important;
        box-sizing: border-box !important;
    }

    body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card-head > em {
        margin: 0 !important;
        justify-self: end !important;
        align-self: center !important;
    }

    @media (max-width: 760px) {
        body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card-head {
            grid-template-columns: 46px minmax(0, 1fr) !important;
        }

        body:has(.asset-summary-ref-section) .asset-command-page .summary-ref-card-head > em {
            grid-column: 2 !important;
            justify-self: end !important;
        }
    }

    @media (max-width: 900px) {
        body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper > .row:last-child {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        body:has(.asset-summary-ref-section) .asset-command-page .asset-command-table-panel #home_wrapper .dataTables_paginate .pagination {
            justify-content: flex-start !important;
            overflow-x: auto !important;
            padding-bottom: 5px !important;
        }
    }

</style>
<section class="asset-command-page" aria-labelledby="asset-command-title">
    <header class="asset-command-hero">
        <div class="asset-command-hero__copy">
            <div class="asset-command-hero__icon" aria-hidden="true"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="asset-command-eyebrow">Asset Intelligence</span>
                <h2 id="asset-command-title">Fleet Operations Overview</h2>
                <p>Track inventory, availability, location coverage and maintenance health from one workspace.</p>
            </div>
        </div>
        <div class="asset-command-hero__status">
            <span class="asset-command-live"><i></i> Live overview</span>
            <strong>Asset status monitor</strong>
            <small>Figures update from the latest registered asset records</small>
        </div>
    </header>

    <div class="asset-command-kpi-grid" aria-label="Asset overview totals">
        <article class="asset-command-kpi">
            <span class="asset-command-kpi__icon"><i class="fas fa-cubes"></i></span>
            <div><span class="asset-command-kpi__label">Total Assets</span><strong><?= $totalAssets ?></strong><small>All registered fleet assets</small></div>
        </article>
        <article class="asset-command-kpi asset-command-kpi--green">
            <span class="asset-command-kpi__icon"><i class="fas fa-shield-alt"></i></span>
            <div><span class="asset-command-kpi__label">Serviceable</span><strong><?= $totalAssetsServiceable ?></strong><small>Ready for operation</small></div>
        </article>
        <article class="asset-command-kpi asset-command-kpi--violet">
            <span class="asset-command-kpi__icon"><i class="fas fa-map-marker-alt"></i></span>
            <div><span class="asset-command-kpi__label">Locations</span><strong><?= $totalLocations ?></strong><small>Active fleet locations</small></div>
        </article>
        <article class="asset-command-kpi asset-command-kpi--amber">
            <span class="asset-command-kpi__icon"><i class="fas fa-tools"></i></span>
            <div><span class="asset-command-kpi__label">In Maintenance</span><strong><?= $totalAssetsInMaintenance ?? 0 ?></strong><small>Assets currently under maintenance</small></div>
        </article>
    </div>

    <article class="asset-command-panel asset-component-panel">
        <div class="asset-command-panel__head">
            <div><span class="asset-panel-kicker">Component Readiness</span><h3>Component Snapshot</h3><p>Current condition of components linked to the fleet.</p></div>
            <span class="asset-command-panel__badge"><i class="fas fa-boxes"></i> <?= $total_items ?> components</span>
        </div>
        <div class="asset-component-grid">
            <a href="<?= site_url('items') ?>" class="asset-component-metric text-decoration-none"><i class="fas fa-cubes"></i><div><span>Total Components</span><strong><?= $total_items ?></strong></div></a>
            <a href="<?= site_url('items?filter=' . urlencode('STORE')) ?>" class="asset-component-metric text-decoration-none"><i class="fas fa-warehouse"></i><div><span>In Store</span><strong><?= $storelocationItemCount ?></strong></div></a>
            <a href="<?= site_url('items?filter=' . urlencode('SERVICEABLE')) ?>" class="asset-component-metric text-decoration-none"><i class="fas fa-check-circle"></i><div><span>Serviceable</span><strong><?= $ServiceableCount ?></strong></div></a>
            <a href="<?= site_url('items?filter=' . urlencode('UNSERVICEABLE')) ?>" class="asset-component-metric text-decoration-none"><i class="fas fa-times-circle"></i><div><span>Unserviceable</span><strong><?= $UnserviceableCount ?></strong></div></a>
            <a href="<?= site_url('items?filter=' . urlencode('MAINTENANCE')) ?>" class="asset-component-metric text-decoration-none"><i class="fas fa-wrench"></i><div><span>Maintenance</span><strong><?= $MaintinenceItemCount ?></strong></div></a>
        </div>
    </article>

<div class="row asset-command-layout">
    <div class="col-md-12 asset-command-legacy-alerts">
        <div class="row mb-3">
            <!-- <div class="col-lg-4 col-md-6 mb-2">

                <a href="<?= site_url('Assets_Item_calibration/index') ?>" class="d-block text-decoration-none">
                    <div class="alert-box <?= $alertMessage > 0 ? 'red' : 'green' ?>">
                        <div class="left">
                            <h2>Asset Calibration</h2>
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="right">
                            <h6 class="counter"><?= $alertMessage ?></h6>
                        </div>
                    </div>
                </a>

            </div>

            <div class="col-lg-4 col-md-6 mb-2">

                <a href="<?= site_url('Assets_Item_calibration/index') ?>" class="d-block text-decoration-none">
                    <div class="alert-box <?= $itemalertMessage > 0 ? 'red' : 'green' ?>">
                        <div class="left">
                            <h2>Item Calibration</h2>
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="right">
                            <h6 class="counter"><?= $itemalertMessage ?></h6>
                        </div>
                    </div>
                </a>

            </div> -->

            <div class="col-lg-4 col-md-6 mb-2">

                <a href="<?= site_url('Assets_Item_maintenance?filter=corrective') ?>" class="d-block text-decoration-none">
                    <div class="alert-box <?= $asset_maintenanceAlertMessage > 0 ? 'red' : 'green' ?>">
                        <div class="left">
                            <h2>Asset Maintenance</h2>
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="right">
                            <h6 class="counter"><?= $asset_maintenanceAlertMessage ?></h6>
                        </div>
                    </div>
                </a>

            </div>
            <div class="col-lg-4 col-md-6 mb-2 asset-summary-kpi-toggle-col">
                <?php // Toggle button added so the legacy KPI boxes stay available without pushing the dashboard down. ?>
                <button type="button" class="asset-summary-kpi-toggle" id="assetSummaryKpiToggle" aria-expanded="false">
                    <i class="fas fa-th-large"></i>
                    <span>Show KPI</span>
                    <i class="fas fa-chevron-down toggle-chevron"></i>
                </button>
            </div>
        </div>

        <!-- <?php if (!empty($item_maintenanceAlertMessage)): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <span class="fas fa-exclamation-triangle mr-4"></span>
                <div>
                    <?php echo $item_maintenanceAlertMessage; ?>
                </div>
            </div>
        <?php endif; ?> -->

    </div>

    <div class="col-lg-12 asset-command-legacy-kpis">

        <div class="row mb-3 justify-content-around">
            <div class="col-12">
                <h4 class="pie-chart-heading">Assets</h4>
            </div>
            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('assets') ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>Asset Quantity</h4>
                        <h2><?= $totalAssets ?></h2>

                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('Location_summary') ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4>Location Quantity</h4>
                        <h2><?= $totalLocations ?></h2>
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('assets?filter=' . urlencode("SERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>Serviceable</h4>
                        <h2><?= $totalAssetsServiceable ?></h2>
                        <!-- <div class="percentage-changes">
                            <span class="up">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-up">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                                90%
                            </span>
                            <p class="white-line">|</p>
                            <span class="down">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#e74c3c" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-down">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <polyline points="19 12 12 19 5 12"></polyline>
                                </svg>
                                10%
                            </span>
                        </div> -->
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('assets?filter=' . urlencode("UNSERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue" id="faulty-box">
                        <h4>UnServiceable</h4>
                        <!-- <i class="fas fa-exclamation-triangle"></i> -->
                        <h2></h2>
                        <!-- <h2><?= $faulty_assets ?></h2> -->
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('assets?filter=' . urlencode("MAINTENANCE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green" id="maintenance-box">
                        <h4>Asset In Maintenance</h4>
                        <h2><?= $totalAssetsInMaintenance ?></h2>
                    </div>
                </a>
            </div>
        </div>



        <div class="row mb-3 justify-content-around">
            <div class="col-12">
                <h4 class="pie-chart-heading">Components</h4>
            </div>
            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items') ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4> Quantity</h4>
                        <h2><?= $total_items ?></h2>               
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items?filter=' . urlencode("STORE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>Store</h4>
                        <h2><?= $storelocationItemCount ?></h2>
                        
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items?filter=' . urlencode("SERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4>Serviceable</h4>
                        <h2><?= $ServiceableCount ?></h2>

                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items?filter=' . urlencode("UNSERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>UnServiceable</h4>
                        <h2><?= $UnserviceableCount ?></h2>
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items?filter=' . urlencode("MAINTENANCE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4>Maintenance</h4>
                        <h2><?= $MaintinenceItemCount ?></h2>
                    </div>
                </a>
            </div>


        </div>


            </div>

        <div class="row asset-summary-ref-section">
            <div class="col-12">
                <section class="summary-ref-shell">
                    <div class="asset-section-heading">
                        <div class="asset-section-heading__copy">
                            <div class="asset-section-heading__icon"><i class="fas fa-chart-pie"></i></div>
                            <div>
                                <span class="asset-section-kicker">Fleet Distribution</span>
                                <h3>Asset Health &amp; Coverage</h3>
                                <p>Live distribution across types, locations and operational condition.</p>
                            </div>
                        </div>
                        <span class="asset-section-heading__badge"><i class="fas fa-sync-alt"></i> Live data</span>
                    </div>

                    <div class="summary-ref-chart-grid">
                        <div class="summary-ref-card summary-ref-card-cyan">
                            <div class="summary-ref-card-head"><span><i class="fas fa-cube"></i></span><div><small>Inventory</small><strong>Asset Quantity</strong><p>Current asset quantity by equipment type.</p></div><em><i class="fas fa-wave-square"></i> <?= $totalAssets ?> total</em></div>
                            <div class="summary-ref-card-body">
                                <div class="summary-ref-chart-wrap"><canvas id="pie-chart-quantity"></canvas><div class="donut-absolute-center text-center"><p id="pie-chart-asset-quantity"></p></div></div>
                                <div id="assets-quantity" class="breakdown-container"></div>
                            </div>
                            <div class="summary-ref-footer"><i class="fas fa-wave-square"></i><span>All assets</span><small><?= $totalAssets ?> Total</small></div>
                        </div>

                        <div class="summary-ref-card summary-ref-card-purple">
                            <div class="summary-ref-card-head"><span><i class="fas fa-map-marker-alt"></i></span><div><small>Coverage</small><strong>Assets by Location</strong><p>Where assets are currently assigned.</p></div><em><i class="fas fa-globe"></i> <?= $totalLocations ?> locations</em></div>
                            <div class="summary-ref-card-body">
                                <div class="summary-ref-chart-wrap"><canvas id="pie-chart-location"></canvas><div class="donut-absolute-center text-center"><p id="pie-chart-asset-location"></p></div></div>
                                <div id="breakdown-list-location" class="breakdown-container"></div>
                            </div>
                            <div class="summary-ref-footer"><i class="fas fa-globe"></i><span>Across locations</span><small><?= $totalLocations ?> Locations</small></div>
                        </div>

                        <div class="summary-ref-card summary-ref-card-blue">
                            <div class="summary-ref-card-head"><span><i class="fas fa-shield-alt"></i></span><div><small>Availability</small><strong>Serviceable Assets</strong><p>Assets ready and available for operation.</p></div><em><i class="fas fa-check-circle"></i> <?= $totalAssetsServiceable ?> ready</em></div>
                            <div class="summary-ref-card-body">
                                <div class="summary-ref-chart-wrap"><canvas id="pie-chart-asset"></canvas><div class="donut-absolute-center text-center"><p id="pie-chart-asset-total"></p></div></div>
                                <div id="breakdown-list-asset-summary" class="breakdown-container"></div>
                            </div>
                            <div class="summary-ref-footer"><i class="fas fa-shield-alt"></i><span>Serviceable assets</span><small><?= $totalAssetsServiceable ?> Total</small></div>
                        </div>

                        <div class="summary-ref-card summary-ref-card-red">
                            <div class="summary-ref-card-head"><span><i class="fas fa-exclamation-triangle"></i></span><div><small>Attention</small><strong>Unserviceable Assets</strong><p>Assets requiring inspection or repair.</p></div><em><i class="fas fa-exclamation-circle"></i> <b id="asset-summary-faulty-badge">0</b> affected</em></div>
                            <div class="summary-ref-card-body">
                                <div class="summary-ref-chart-wrap summary-ref-empty-chart"><canvas id="pie-chart-faulty"></canvas><div class="donut-absolute-center text-center"><p id="pie-chart-asset-faulty"></p></div></div>
                                <div id="breakdown-list-faulty" class="breakdown-container"></div>
                            </div>
                            <div class="summary-ref-footer"><i class="fas fa-shield-alt"></i><span>No unserviceable assets found.</span></div>
                        </div>

                        <div class="summary-ref-card summary-ref-card-purple">
                            <div class="summary-ref-card-head"><span><i class="fas fa-wrench"></i></span><div><small>Maintenance</small><strong>Maintenance Activity</strong><p>Corrective and preventive work by asset type.</p></div><em><i class="fas fa-tools"></i> <b id="asset-summary-maintenance-badge">0</b> jobs</em></div>
                            <div class="summary-ref-card-body">
                                <div class="summary-ref-chart-wrap summary-ref-empty-chart"><canvas id="pie-chart-maintenance"></canvas><div class="donut-absolute-center text-center"><p id="pie-chart-asset-maintenance"></p></div></div>
                                <div id="breakdown-list-maintenance" class="breakdown-container"></div>
                            </div>
                            <div class="summary-ref-footer"><i class="fas fa-tools"></i><span>Maintenance activities recorded.</span></div>
                        </div>

                        <div class="summary-ref-card summary-ref-card-blue fleet-insights-card">
                            <?php // Legacy store chart IDs kept hidden so store-summary.js can still initialise safely. ?>
                            <div class="legacy-store-chart d-none"><canvas id="pie-chart-store-summary"></canvas><p id="pie-chart-store-summary-total"></p><div id="breakdown-list-store-summary"></div></div>
                            <div class="summary-ref-card-head"><span><i class="fas fa-chart-line"></i></span><div><small>Performance</small><strong>Fleet Insights</strong><p>Key readiness indicators at a glance.</p></div><em><i class="fas fa-bolt"></i> Live health</em></div>
                            <div class="fleet-insight-grid">
                                <div class="fleet-insight-box"><span class="fleet-icon cyan"><i class="fas fa-cube"></i></span><small>Total Assets</small><strong><?= $totalAssets ?></strong><em>All registered assets</em></div>
                                <div class="fleet-insight-box"><span class="fleet-icon green"><i class="fas fa-shield-alt"></i></span><small>Serviceable %</small><strong><?= $totalAssets > 0 ? round(($totalAssetsServiceable / $totalAssets) * 100) : 0 ?>%</strong><em><?= $totalAssetsServiceable ?> of <?= $totalAssets ?> assets</em></div>
                                <div class="fleet-insight-box"><span class="fleet-icon amber"><i class="fas fa-map-marker-alt"></i></span><small>Locations</small><strong><?= $totalLocations ?></strong><em>Across all locations</em></div>
                                <div class="fleet-insight-box"><span class="fleet-icon violet"><i class="fas fa-tools"></i></span><small>Maintenance</small><strong><?= $totalAssetsInMaintenance ?? 0 ?></strong><em>Activities recorded</em></div>
                            </div>
                            <div class="fleet-status-strip"><i class="fas fa-sparkles"></i><div><strong>All systems operational</strong><small>Excellent fleet status across the board.</small></div></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <div class="col-12">
        <div class="asset-command-operations">
    <article class="asset-command-panel asset-command-table-panel">
        <div class="asset-command-panel__head">
            <div><span class="asset-panel-kicker">Fleet Directory</span><h3>Asset Location Register</h3><p>Latest system name, assigned location and operational status.</p></div>
            <span class="asset-command-panel__badge"><i class="fas fa-list"></i> Live register</span>
        </div>
        <div class="asset-command-table-host">
        <table class="table" id="home" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>System Name</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        </div>
    </article>

    <article class="asset-command-panel asset-command-map-panel">
        <div class="asset-command-panel__head">
            <div><span class="asset-panel-kicker">Geographic View</span><h3>Fleet Location Map</h3><p>Select a map marker to review the assigned asset details.</p></div>
            <span class="asset-command-panel__badge"><i class="fas fa-map-marked-alt"></i> Interactive</span>
        </div>
        <div class="asset-command-map-body">
        <div id="map"></div>
        <div class='quake-info'>
            <!-- <div><strong>Magnitude:</strong> <span id='mag'></span></div> -->
            <div><strong>Location:</strong> <span id='loc'></span></div>
            <div><strong>Asset Type:</strong> <span id='asset_type'></span></div>
            <div><strong>Asset Name:</strong> <span id='asset_name'></span></div>
            <div><strong>Asset Number:</strong> <span id='asset_num'></span></div>
            <!-- <div><strong>Date:</strong> <span id='date'></span></div> -->
        </div>
        </div>
    </article>
        </div>
    </div>
</div>
</section>
