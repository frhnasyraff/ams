<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }

    .pagination>.active>a {
        background-color: #08073dff !important;
    }

    #equipments_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #equipments_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    .scrollable-form {
        max-height: 500px;
        /* Set a height for the scrollable area */
        overflow-y: auto;
        /* Enable vertical scrolling */
        overflow-x: hidden;
        padding-right: 15px;
        /* Optional: to prevent content from hiding behind scrollbar */
    }

    .content-div {
        display: none;
    }

    .expiry-box.blue::before {
        content: '' !important;
        position: absolute !important;
        top: 17px !important;
        right: 0px !important;
        width: 100px !important;
        height: 130px !important;
        background-image: url(/design/img/Union-white.png) !important;
        background-size: contain !important;
        background-repeat: no-repeat !important;
        opacity: 4%;
    }

    .expiry-box::before {
        content: '' !important;
        position: absolute !important;
        top: 17px !important;
        right: 0px !important;
        width: 100px !important;
        height: 130px !important;
        background-image: url(/design/img/Union.png) !important;
        background-size: contain !important;
        background-repeat: no-repeat !important;
        opacity: 4%;
    }

    .expiry-box h2 {
        color: #80A874 !important;
        font-size: -webkit-xxx-large !important;
        font-weight: bold !important;
        margin-left: 0% !important;
        position: relative !important;
        z-index: 10 !important;
    }

    .searchable-dropdown {
        position: relative;
        width: 100%;
    }

    .dropdown-togg {
        border: 1px solid #ccc;
        padding: 8px;
        background-color: #fff;
        cursor: pointer;
    }

    .dropdown-search {
        position: absolute;
        background-color: #fff;
        border: 1px solid #ccc;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
    }

    .dropdown-options .dropdown-item {
        padding: 8px;
        cursor: pointer;
    }

    .dropdown-options .dropdown-item:hover {
        background-color: #f1f1f1;
    }

    /* Components page polish - keep old structure, restyle to match Assets page */
    html.ims-components-index body {
        background: #050b16 !important;
    }

    html.ims-components-index .content-wrapper,
    html.ims-components-index #content,
    html.ims-components-index .container-fluid {
        background: transparent !important;
    }

    html.ims-components-index .float-right.text_successo.btn.btn-default.btn_border {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 46px;
        padding: 0 20px !important;
        margin: 18px 28px 18px 0 !important;
        border: 0 !important;
        border-right: 1px solid #1a3e67 !important;
        border-radius: 12px !important;
        color: #fff !important;
        background: linear-gradient(135deg, #2f7cff 0%, #23b8f4 100%) !important;
        box-shadow: 0 16px 34px rgba(26, 129, 255, .33) !important;
        font-weight: 900 !important;
        letter-spacing: .01em;
    }

    html.ims-components-index .col-lg-12.order-lg-0.order-md-1 > .row {
        margin: 0 !important;
    }

    html.ims-components-index .row > .card.shadow.mb-4.tabradius {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
        margin: 0 !important;
        border: 1px solid rgba(42, 157, 255, .36) !important;
        border-radius: 24px !important;
        background: linear-gradient(135deg, rgba(5, 15, 31, .92), rgba(12, 42, 82, .78)) !important;
        box-shadow: 0 26px 62px rgba(0, 0, 0, .35) !important;
        overflow: hidden !important;
    }

    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body {
        padding: 30px 34px 36px !important;
    }

    html.ims-components-index .item_type_filter,
    html.ims-components-index .item_group_filter {
        width: 100%;
        display: flex !important;
        flex-wrap: wrap;
        gap: 14px 24px;
        align-items: center;
        justify-content: center;
        border: 0 !important;
        background: transparent !important;
    }

    html.ims-components-index .item_type_filter .btn,
    html.ims-components-index .item_group_filter .btn {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 20px !important;
        border: 0 !important;
        border-radius: 999px !important;
        color: #b9c7d8 !important;
        background: transparent !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        letter-spacing: .02em;
        box-shadow: none !important;
    }

    html.ims-components-index .item_type_filter .btn:hover,
    html.ims-components-index .item_group_filter .btn:hover {
        color: #fff !important;
        background: rgba(35, 184, 244, .12) !important;
    }

    html.ims-components-index .item_type_filter .btn.active,
    html.ims-components-index .item_group_filter .btn.active {
        color: #fff !important;
        background: linear-gradient(135deg, #2f7cff 0%, #23b8f4 100%) !important;
        box-shadow: 0 14px 30px rgba(31, 143, 255, .32) !important;
    }

    html.ims-components-index .item_group_filter {
        margin-top: 22px !important;
        justify-content: space-around;
    }

    html.ims-components-index .col-lg-12.mt-25 {
        padding: 0 !important;
        margin-top: 42px !important;
    }

    html.ims-components-index .col-lg-12.mt-25 .card-body {
        padding: 0 !important;
    }

    html.ims-components-index .col-lg-12.mt-25 .d-flex {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(150px, 1fr));
        gap: 18px !important;
        align-items: stretch !important;
        text-align: left !important;
    }

    html.ims-components-index .col-lg-12.mt-25 .flex-fill {
        width: 100% !important;
        max-width: none !important;
        flex: none !important;
    }

    html.ims-components-index .expiry-box {
        min-height: 148px !important;
        padding: 20px 22px !important;
        border: 1px solid rgba(42, 157, 255, .48) !important;
        border-radius: 22px !important;
        background: linear-gradient(145deg, rgba(20, 70, 142, .92), rgba(9, 36, 76, .9)) !important;
        box-shadow: 0 20px 42px rgba(0, 0, 0, .28) !important;
        overflow: hidden !important;
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
    }

    html.ims-components-index .expiry-box::before {
        opacity: .05 !important;
        right: -8px !important;
        top: 10px !important;
    }

    html.ims-components-index .expiry-box::after {
        content: "";
        position: absolute;
        right: -35px;
        top: -45px;
        width: 135px;
        height: 135px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(35, 184, 244, .22), transparent 68%);
        pointer-events: none;
    }

    html.ims-components-index .expiry-box h4 {
        position: relative !important;
        z-index: 2 !important;
        margin: 0 !important;
        color: #fff !important;
        font-size: 15px !important;
        line-height: 1.2 !important;
        font-weight: 900 !important;
        text-align: left !important;
        word-break: normal !important;
    }

    html.ims-components-index .expiry-box h2 {
        position: relative !important;
        z-index: 2 !important;
        margin: 12px 0 0 !important;
        color: #fff !important;
        font-size: 46px !important;
        line-height: 1 !important;
        font-weight: 900 !important;
        text-align: right !important;
        text-shadow: 0 10px 24px rgba(0, 0, 0, .4);
    }

    html.ims-components-index .table-responsive {
        margin-top: 30px !important;
        padding: 18px !important;
        border: 1px solid rgba(42, 157, 255, .36) !important;
        border-radius: 24px !important;
        background: rgba(4, 16, 32, .72) !important;
        box-shadow: none !important;
        overflow-x: auto !important;
    }

    html.ims-components-index table#assets {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        color: #eaf4ff !important;
    }

    html.ims-components-index table#assets thead th:first-child {
        border-radius: 14px 0 0 14px !important;
    }

    html.ims-components-index table#assets thead th:last-child {
        border-radius: 0 14px 14px 0 !important;
    }

    html.ims-components-index table#assets tbody td {
        padding: 16px 14px !important;
        border-color: rgba(76, 151, 255, .12) !important;
        color: #dcecff !important;
        vertical-align: middle !important;
    }

    html.ims-components-index table#assets tbody tr {
        background: rgba(5, 17, 33, .62) !important;
    }

    html.ims-components-index #assets_wrapper .row:first-child {
        align-items: center !important;
        margin: 0 0 14px !important;
    }

    html.ims-components-index #assets_wrapper .dataTables_length,
    html.ims-components-index #assets_wrapper .dataTables_filter,
    html.ims-components-index #assets_wrapper .dataTables_info {
        color: #fff !important;
        font-weight: 800 !important;
    }

    html.ims-components-index #assets_wrapper input,
    html.ims-components-index #assets_wrapper select {
        min-height: 38px;
        color: #fff !important;
        background: #071426 !important;
        border: 1px solid rgba(76, 151, 255, .55) !important;
        border-radius: 13px !important;
        padding: 8px 12px !important;
    }

    html.ims-components-index #assets_wrapper .pagination {
        justify-content: flex-end !important;
        gap: 6px;
    }

    html.ims-components-index #assets_wrapper .page-link {
        min-width: 42px;
        padding: 9px 12px !important;
        text-align: center;
        color: #dbeafe !important;
        background: #0c2f67 !important;
        border: 1px solid rgba(58, 142, 255, .5) !important;
        border-radius: 12px !important;
        font-weight: 900 !important;
    }

    html.ims-components-index #assets_wrapper .page-item.active .page-link {
        color: #fff !important;
        background: linear-gradient(135deg, #2f7cff, #23b8f4) !important;
    }

    @media (max-width: 1400px) {
        html.ims-components-index .col-lg-12.mt-25 .d-flex {
            grid-template-columns: repeat(3, minmax(150px, 1fr));
        }
    }

    @media (max-width: 900px) {
        html.ims-components-index .card.shadow.mb-4.tabradius > .card-body {
            padding: 22px 18px 28px !important;
        }

        html.ims-components-index .col-lg-12.mt-25 .d-flex {
            grid-template-columns: repeat(2, minmax(135px, 1fr));
        }
    }

    @media (max-width: 560px) {
        html.ims-components-index .col-lg-12.mt-25 .d-flex {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Scope Components page UI refinements to this view only.
    document.documentElement.classList.add('ims-components-index');
</script>

<style id="components-redesign-final">
    html.ims-components-index,
    html.ims-components-index body {
        background: #020713 !important;
        color: #f8fbff !important;
        overflow-x: hidden !important;
    }

    html.ims-components-index .content-wrapper,
    html.ims-components-index .content,
    html.ims-components-index .container-fluid,
    html.ims-components-index .page-content,
    html.ims-components-index .main-panel {
        background: transparent !important;
    }

    html.ims-components-index .container-fluid {
        width: 100% !important;
        max-width: none !important;
        padding-left: clamp(18px, 2.2vw, 38px) !important;
        padding-right: clamp(18px, 2.2vw, 38px) !important;
    }

    html.ims-components-index .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    html.ims-components-index .col-lg-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    html.ims-components-index .components-action-bar,
    html.ims-components-index .card.shadow.mb-4.tabradius {
        width: min(100%, 1500px) !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    html.ims-components-index .components-action-bar {
        margin-top: 48px !important;
        margin-bottom: 16px !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 12px !important;
    }

    html.ims-components-index .component-primary-action,
    html.ims-components-index a[href="#addModal"][data-target="#addModal"] {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        min-height: 44px !important;
        padding: 0 20px !important;
        border: 0 !important;
        border-radius: 14px !important;
        color: #ffffff !important;
        font-weight: 900 !important;
        letter-spacing: .01em !important;
        text-decoration: none !important;
        background: linear-gradient(135deg, #2f7dff 0%, #18c2ff 100%) !important;
        box-shadow: 0 14px 30px rgba(24, 144, 255, .28) !important;
        transition: transform .16s ease, box-shadow .16s ease, filter .16s ease !important;
    }

    html.ims-components-index .component-primary-action:hover,
    html.ims-components-index a[href="#addModal"][data-target="#addModal"]:hover {
        transform: translateY(-1px) !important;
        filter: brightness(1.08) !important;
        box-shadow: 0 18px 38px rgba(24, 144, 255, .35) !important;
    }

    html.ims-components-index .card.shadow.mb-4.tabradius {
        margin-top: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body {
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    html.ims-components-index .card.shadow.mb-4.tabradius .text-center {
        text-align: left !important;
    }

    html.ims-components-index .item_type_filter,
    html.ims-components-index .item_group_filter {
        width: 100% !important;
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 12px 20px !important;
        margin: 0 0 18px !important;
        padding: 22px 24px !important;
        border: 1px solid rgba(50, 157, 255, .36) !important;
        border-radius: 24px !important;
        background: linear-gradient(135deg, rgba(6, 17, 34, .98), rgba(10, 35, 70, .78)) !important;
        box-shadow: 0 20px 46px rgba(0, 0, 0, .18) !important;
    }

    html.ims-components-index .item_type_filter::before,
    html.ims-components-index .item_group_filter::before {
        flex: 0 0 100% !important;
        display: flex !important;
        align-items: center !important;
        gap: 9px !important;
        color: #34d8ff !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase !important;
    }

    html.ims-components-index .item_type_filter::before {
        content: "\f1b3  Component Type";
        font-family: "FontAwesome", "Poppins", sans-serif !important;
    }

    html.ims-components-index .item_group_filter::before {
        content: "\f07c  Status";
        font-family: "FontAwesome", "Poppins", sans-serif !important;
    }

    html.ims-components-index .item_type_filter .btn,
    html.ims-components-index .item_group_filter .btn {
        min-width: 124px !important;
        height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        padding: 0 18px !important;
        border: 0 !important;
        border-radius: 999px !important;
        color: rgba(226, 237, 255, .78) !important;
        background: transparent !important;
        box-shadow: none !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        text-transform: uppercase !important;
        letter-spacing: .02em !important;
        opacity: 1 !important;
        white-space: nowrap !important;
    }

    html.ims-components-index .item_type_filter .btn:hover,
    html.ims-components-index .item_group_filter .btn:hover {
        color: #ffffff !important;
        background: rgba(44, 130, 255, .14) !important;
    }

    html.ims-components-index .item_type_filter .btn.active,
    html.ims-components-index .item_group_filter .btn.active,
    html.ims-components-index .item_type_filter .btn.btn-primary,
    html.ims-components-index .item_group_filter .btn.btn-primary {
        color: #ffffff !important;
        background: linear-gradient(135deg, #2e7bff 0%, #1fc3ff 100%) !important;
        box-shadow: 0 14px 30px rgba(30, 144, 255, .26) !important;
    }

    html.ims-components-index .item_type_filter .btn i,
    html.ims-components-index .item_group_filter .btn i {
        color: #26d9ff !important;
        font-size: 13px !important;
    }

    html.ims-components-index .item_type_filter .btn.active i,
    html.ims-components-index .item_group_filter .btn.active i,
    html.ims-components-index .item_type_filter .btn.btn-primary i,
    html.ims-components-index .item_group_filter .btn.btn-primary i {
        color: #ffffff !important;
    }

    html.ims-components-index .item_group_filter .btn {
        flex: 1 1 0 !important;
        min-width: 0 !important;
        max-width: none !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    html.ims-components-index .item_group_filter {
        overflow: visible !important;
    }

    html.ims-components-index #orders-list .btn.btn-primary,
    html.ims-components-index #orders-list .btn.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #2e7bff 0%, #1fc3ff 100%) !important;
        box-shadow: 0 14px 30px rgba(30, 144, 255, .26) !important;
    }

    html.ims-components-index #orders-list .btn.btn-primary i,
    html.ims-components-index #orders-list .btn.active i {
        color: #ffffff !important;
    }

    html.ims-components-index .col-lg-12.mt-25 {
        width: 100% !important;
        margin: 24px 0 0 !important;
        padding: 0 !important;
    }

    html.ims-components-index .col-lg-12.mt-25 .mb-4,
    html.ims-components-index .col-lg-12.mt-25 .card-body {
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    html.ims-components-index .col-lg-12.mt-25 .d-flex {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: repeat(5, minmax(160px, 1fr)) !important;
        gap: 16px !important;
        align-items: stretch !important;
    }

    html.ims-components-index .expiry-box.flex-fill {
        flex: none !important;
        width: auto !important;
        max-width: none !important;
        min-width: 0 !important;
    }

    html.ims-components-index .expiry-box {
        position: relative !important;
        min-height: 148px !important;
        padding: 18px 20px !important;
        border: 1px solid rgba(41, 160, 255, .42) !important;
        border-radius: 22px !important;
        overflow: hidden !important;
        color: #ffffff !important;
        background:
            radial-gradient(circle at 80% 12%, rgba(44, 148, 255, .18), transparent 34%),
            linear-gradient(145deg, rgba(14, 48, 96, .96), rgba(7, 22, 43, .98)) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .05), 0 18px 36px rgba(0, 0, 0, .18) !important;
    }

    html.ims-components-index .expiry-box::before {
        content: "\f1b3";
        font-family: "FontAwesome" !important;
        position: absolute !important;
        left: 18px !important;
        top: 18px !important;
        width: 42px !important;
        height: 42px !important;
        display: grid !important;
        place-items: center !important;
        border-radius: 14px !important;
        color: #35dcff !important;
        background: rgba(30, 134, 255, .16) !important;
        border: 1px solid rgba(56, 188, 255, .22) !important;
    }

    html.ims-components-index .expiry-box.green {
        border-color: rgba(0, 214, 165, .48) !important;
    }

    html.ims-components-index .expiry-box.blue {
        border-color: rgba(42, 153, 255, .45) !important;
    }

    html.ims-components-index .expiry-box h4 {
        margin: 64px 0 0 !important;
        color: #f7fbff !important;
        font-size: 15px !important;
        font-weight: 950 !important;
        line-height: 1.15 !important;
        text-align: left !important;
        letter-spacing: -.01em !important;
    }

    html.ims-components-index .expiry-box h2 {
        position: absolute !important;
        right: 20px !important;
        bottom: 12px !important;
        margin: 0 !important;
        color: #ffffff !important;
        font-size: clamp(36px, 3.4vw, 52px) !important;
        font-weight: 1000 !important;
        line-height: .95 !important;
        text-shadow: 0 10px 24px rgba(0, 0, 0, .28) !important;
    }

    html.ims-components-index .table-responsive {
        width: 100% !important;
        margin-top: 26px !important;
        padding: 18px !important;
        border: 1px solid rgba(50, 157, 255, .36) !important;
        border-radius: 24px !important;
        background:
            radial-gradient(circle at 100% 0%, rgba(34, 107, 220, .18), transparent 34%),
            linear-gradient(145deg, rgba(5, 15, 31, .98), rgba(9, 31, 61, .82)) !important;
        box-shadow: 0 22px 48px rgba(0, 0, 0, .18) !important;
        overflow-x: auto !important;
    }

    html.ims-components-index #assets_wrapper {
        width: 100% !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    html.ims-components-index #assets_wrapper .row:first-child {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
        margin-bottom: 14px !important;
    }

    html.ims-components-index #assets_wrapper label,
    html.ims-components-index #assets_wrapper .dataTables_info {
        color: #f7fbff !important;
        font-weight: 800 !important;
    }

    html.ims-components-index #assets_wrapper select,
    html.ims-components-index #assets_wrapper input[type="search"] {
        height: 38px !important;
        min-width: 78px !important;
        border: 1px solid rgba(93, 169, 255, .5) !important;
        border-radius: 13px !important;
        color: #ffffff !important;
        background: rgba(3, 14, 29, .86) !important;
        outline: 0 !important;
        box-shadow: none !important;
    }

    html.ims-components-index #assets {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        color: #f7fbff !important;
        background: transparent !important;
    }

    #assets thead th {
        height: 54px !important;
        padding: 14px 16px !important;
        border: 0 !important;
        border-right: 1px solid #1a3e67 !important;
        color: #ffffff !important;
        background: linear-gradient(180deg, #102846 0%, #081a31 100%) !important;
        font-size: 14px !important;
        font-weight: 950 !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
    }

    #assets thead th:first-child {
        border-top-left-radius: 14px !important;
        border-bottom-left-radius: 14px !important;
    }

    #assets thead th:last-child {
        border-top-right-radius: 14px !important;
        border-bottom-right-radius: 14px !important;
    }

    html.ims-components-index #assets tbody tr {
        background: rgba(5, 17, 34, .92) !important;
    }

    html.ims-components-index #assets tbody td {
        padding: 16px !important;
        border-color: rgba(90, 140, 210, .14) !important;
        color: #dfeaff !important;
        font-weight: 750 !important;
        vertical-align: middle !important;
    }

    html.ims-components-index #assets tbody tr:hover td {
        background: rgba(24, 116, 255, .08) !important;
    }

    html.ims-components-index #assets .btn,
    html.ims-components-index #assets button {
        border-radius: 12px !important;
    }

    html.ims-components-index .dataTables_paginate {
        display: flex !important;
        justify-content: flex-end !important;
        gap: 8px !important;
        margin-top: 12px !important;
    }

    html.ims-components-index .dataTables_paginate .paginate_button,
    html.ims-components-index .page-link {
        min-width: 42px !important;
        height: 38px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid rgba(75, 153, 255, .45) !important;
        border-radius: 12px !important;
        color: #d8e8ff !important;
        background: rgba(14, 48, 96, .74) !important;
        font-weight: 900 !important;
        text-decoration: none !important;
    }

    html.ims-components-index .dataTables_paginate .paginate_button.current,
    html.ims-components-index .page-item.active .page-link {
        color: #ffffff !important;
        background: linear-gradient(135deg, #2e7bff, #1fc3ff) !important;
    }

    html.ims-components-index .component-add-modal .modal-dialog {
        max-width: 980px !important;
    }

    html.ims-components-index .component-add-modal .modal-content {
        border: 1px solid rgba(57, 171, 255, .46) !important;
        border-radius: 26px !important;
        color: #f7fbff !important;
        background:
            radial-gradient(circle at 80% 0%, rgba(41, 126, 255, .18), transparent 36%),
            linear-gradient(145deg, rgba(4, 12, 27, .98), rgba(9, 32, 64, .98)) !important;
        box-shadow: 0 30px 80px rgba(0, 0, 0, .55) !important;
        overflow: hidden !important;
    }

    html.ims-components-index .component-add-modal .modal-header,
    html.ims-components-index .component-add-modal .modal-footer {
        border-color: rgba(94, 166, 255, .18) !important;
        background: rgba(13, 45, 88, .48) !important;
    }

    html.ims-components-index .component-add-modal .modal-title {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        color: #ffffff !important;
        font-weight: 950 !important;
    }

    html.ims-components-index .component-add-modal label {
        color: #e8f3ff !important;
        font-weight: 850 !important;
    }

    html.ims-components-index .component-add-modal .form-control,
    html.ims-components-index .component-add-modal select,
    html.ims-components-index .component-add-modal textarea,
    html.ims-components-index .component-add-modal .select2-selection {
        min-height: 42px !important;
        border: 1px solid rgba(105, 174, 255, .42) !important;
        border-radius: 13px !important;
        color: #ffffff !important;
        background: rgba(4, 15, 31, .88) !important;
        box-shadow: none !important;
    }

    html.ims-components-index .component-add-modal .form-control::placeholder {
        color: rgba(218, 231, 255, .55) !important;
    }

    html.ims-components-index .component-add-modal input[type="file"].form-control {
        width: auto !important;
        min-height: 0 !important;
        height: auto !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    html.ims-components-index .component-add-modal input[type="file"]::file-selector-button {
        margin-right: 10px !important;
        padding: 10px 14px !important;
        border: 0 !important;
        border-radius: 9px !important;
        color: #ffffff !important;
        background: #1689f5 !important;
        cursor: pointer !important;
    }

    html.ims-components-index .component-add-modal .btn-primary,
    html.ims-components-index .component-add-modal .btn-success {
        border-right: 1px solid #1a3e67 !important;
        border-radius: 13px !important;
        background: linear-gradient(135deg, #2e7bff, #1fc3ff) !important;
        color: #ffffff !important;
        font-weight: 900 !important;
    }

    html.ims-components-index .component-add-modal .btn-secondary,
    html.ims-components-index .component-add-modal .btn-default {
        border: 1px solid rgba(120, 177, 255, .32) !important;
        border-radius: 13px !important;
        background: rgba(12, 31, 58, .9) !important;
        color: #e9f3ff !important;
        font-weight: 850 !important;
    }

    /* Match Status filters to the complete Component Type cards. Legacy
       btn-group flex sizing was shrinking the button around its centre. */
    html.ims-components-index body .component-filter-panel #orders-list.item_group_filter {
        width: auto !important;
        margin: 0 14px 15px !important;
        padding: 0 !important;
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)) !important;
        gap: 9px !important;
        align-items: stretch !important;
        justify-content: stretch !important;
        overflow: visible !important;
    }

    html.ims-components-index body .component-filter-panel #orders-list.item_group_filter > .btn {
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        min-height: 43px !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 14px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        flex: none !important;
        overflow: hidden !important;
        border: 1px solid rgba(48, 112, 173, .28) !important;
        border-radius: 12px !important;
        color: #b7c9df !important;
        background: rgba(5, 20, 39, .58) !important;
        box-shadow: none !important;
        white-space: normal !important;
        box-sizing: border-box !important;
    }

    html.ims-components-index body .component-filter-panel #orders-list.item_group_filter > .btn.active,
    html.ims-components-index body .component-filter-panel #orders-list.item_group_filter > .btn.btn-primary {
        border-color: rgba(78, 217, 255, .72) !important;
        color: #fff !important;
        background: linear-gradient(135deg, #337bf5, #1dbce9) !important;
        box-shadow: 0 8px 22px rgba(28, 155, 229, .22) !important;
        opacity: 1 !important;
    }

    html.ims-components-index body .component-filter-panel #orders-list.item_group_filter > .btn::before,
    html.ims-components-index body .component-filter-panel #orders-list.item_group_filter > .btn::after {
        content: none !important;
        display: none !important;
    }

    @media (max-width: 1200px) {
        html.ims-components-index .col-lg-12.mt-25 .d-flex {
            grid-template-columns: repeat(3, minmax(160px, 1fr)) !important;
        }
    }

    @media (max-width: 768px) {
        html.ims-components-index body .component-filter-panel #orders-list.item_group_filter {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        html.ims-components-index .components-action-bar {
            justify-content: stretch !important;
        }

        html.ims-components-index .component-primary-action {
            width: 100% !important;
        }

        html.ims-components-index .col-lg-12.mt-25 .d-flex {
            grid-template-columns: 1fr !important;
        }

        html.ims-components-index #assets_wrapper .row:first-child {
            align-items: stretch !important;
            flex-direction: column !important;
        }
    }

    @media (max-width: 480px) {
        html.ims-components-index body .component-filter-panel #orders-list.item_group_filter {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<style id="components-remove-outer-backplate">
    html.ims-components-index .card.shadow.mb-4.tabradius,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .text-center,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .col-lg-12,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .col-lg-12 > .mb-4,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .col-lg-12 > .mb-4 > .card-body {
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        outline: 0 !important;
    }

    html.ims-components-index .card.shadow.mb-4.tabradius::before,
    html.ims-components-index .card.shadow.mb-4.tabradius::after,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body::before,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body::after,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .text-center::before,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .text-center::after,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .col-lg-12::before,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .col-lg-12::after {
        content: none !important;
        display: none !important;
    }

    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .text-center,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .col-lg-12 {
        padding: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    html.ims-components-index .item_type_filter,
    html.ims-components-index .item_group_filter,
    html.ims-components-index .card.shadow.mb-4.tabradius > .card-body > .col-lg-12.mt-25,
    html.ims-components-index .table-responsive {
        width: min(100%, 1500px) !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
</style>

<script id="components-redesign-final-js">
    (function() {
        function ready(fn) {
            if (document.readyState !== 'loading') {
                fn();
            } else {
                document.addEventListener('DOMContentLoaded', fn);
            }
        }

        ready(function() {
            document.documentElement.classList.add('ims-components-index');

            var mainCard = document.querySelector('html.ims-components-index .card.shadow.mb-4.tabradius');
            var addBtn = document.querySelector('html.ims-components-index a[href="#addModal"][data-target="#addModal"]');

            if (mainCard) {
                mainCard.style.setProperty('background', 'transparent', 'important');
                mainCard.style.setProperty('border', '0', 'important');
                mainCard.style.setProperty('border-radius', '0', 'important');
                mainCard.style.setProperty('box-shadow', 'none', 'important');
                mainCard.style.setProperty('outline', '0', 'important');

                var mainCardBody = mainCard.querySelector(':scope > .card-body');
                if (mainCardBody) {
                    mainCardBody.style.setProperty('background', 'transparent', 'important');
                    mainCardBody.style.setProperty('border', '0', 'important');
                    mainCardBody.style.setProperty('box-shadow', 'none', 'important');
                }
            }

            if (addBtn) {
                addBtn.classList.add('component-primary-action');
                addBtn.innerHTML = '<i class="fa fa-plus"></i><span>New Component</span>';

                if (mainCard && !addBtn.closest('.component-registry-hero') && !document.querySelector('html.ims-components-index .components-action-bar')) {
                    var actionBar = document.createElement('div');
                    actionBar.className = 'components-action-bar';
                    mainCard.parentNode.insertBefore(actionBar, mainCard);
                    actionBar.appendChild(addBtn);
                }
            }

            var addModal = document.getElementById('addModal');
            if (addModal) {
                addModal.classList.add('component-add-modal');
                var title = addModal.querySelector('.modal-title');
                if (title && !title.dataset.componentRedesigned) {
                    title.innerHTML = '<i class="fa fa-cubes"></i> New Component';
                    title.dataset.componentRedesigned = '1';
                }
            }

            document.querySelectorAll('html.ims-components-index .item_type_filter .btn').forEach(function(btn) {
                if (!btn.querySelector('i')) {
                    btn.insertAdjacentHTML('afterbegin', '<i class="fa fa-cube"></i>');
                }
            });

            document.querySelectorAll('html.ims-components-index .item_group_filter .btn').forEach(function(btn) {
                if (btn.querySelector('i')) return;
                var text = (btn.textContent || '').toLowerCase();
                var icon = 'fa-folder';
                if (text.indexOf('service') !== -1) icon = 'fa-check-circle';
                if (text.indexOf('maintenance') !== -1) icon = 'fa-wrench';
                if (text.indexOf('store') !== -1) icon = 'fa-archive';
                btn.insertAdjacentHTML('afterbegin', '<i class="fa ' + icon + '"></i>');
            });
        });
    })();
</script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<section class="component-registry-hero">
    <div class="component-hero-copy">
        <span class="component-hero-icon"><i class="fa fa-cubes"></i></span>
        <div>
            <small>Component Management</small>
            <h2>Component Registry</h2>
            <p>Manage component records, availability and assigned assets from one clear workspace.</p>
        </div>
    </div>
    <?php if ($this->user_model->has_perm("add_equipments")) { ?>
        <a class="float-right text_successo btn btn-default btn_border component-primary-action" href="#addModal" data-toggle="modal"
            data-target="#addModal" title="Add new component"><i class="fa fa-plus"></i><span>New Component</span></a>
    <?php } ?>
</section>
<div class="row component-registry-content">
    <!-- Left side: Asset List and Filters -->
    <div class="col-lg-12 order-lg-0 order-md-1">
        <div class="row">
            <div class="card shadow mb-4 tabradius component-registry-shell">
                <div class="card-body">
                    <div class="text-center component-filter-panel">
                        <?php
                        $typeFilter = $_GET['type_filter'] ?? '';
                        $activeFilter = $_GET['filter'] ?? '';
                        ?>

                        <div class="component-panel-heading">
                            <div>
                                <small>Quick Filters</small>
                                <h3>Find the right component</h3>
                                <p>Filter the directory by component type or current operational status.</p>
                            </div>
                            <span><i class="fa fa-bolt"></i> Instant results</span>
                        </div>

                        <!-- TYPE FILTER SECTION -->
                        <div class="component-filter-title"><i class="fa fa-cubes"></i><span>Component Type</span></div>
                        <div class="btn-group item_type_filter small mt-2" role="group" aria-label="Equipments filter actions">
                            <button type="button" class="btn btn-sm <?= $typeFilter === '' ? 'btn-primary active' : '' ?>" <?= $typeFilter === '' ? 'disabled' : '' ?> data-filter="" title="Show all Asset types" style="font-weight: 600;">
                                <i class="fa fa-th-large"></i><span>All Types</span>
                            </button>

                            <!-- Dynamic Type Buttons -->
                            <?php foreach ($this->steve->item_types() as $t): ?>
                                <?php $isActive = (string) $typeFilter == (string) $t->id; ?>
                                <button type="button"
                                    class="btn btn-sm text-uppercase tip medium-bold <?= $isActive ? 'btn-primary active' : '' ?>"

                                    data-filter="<?= $t->id; ?>"
                                    title="Show only <?= $t->name; ?>">
                                    <?= $t->name ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <br />

                        <!-- ITEM STATUS FILTER SECTION -->
                        <div class="component-filter-title component-status-title"><i class="fa fa-folder-open"></i><span>Status</span></div>
                        <div id="orders-list"
                            class="project-tab btn-group item_group_filter small mt-2" role="group"
                            aria-label="Equipment groups filter">

                            <!-- 'All' Button -->
                            <button id="item-status-all" type="button"
                                class="nav-item nav-link btn btn-sm <?= $activeFilter === '' ? 'active btn-primary' : '' ?>"
                                <?= $activeFilter === '' ? 'disabled' : '' ?>
                                data-filter="" title="Show all equipment groups" style="font-weight: 600;">
                                <i class="fa fa-list icon"></i><span>All Statuses</span>
                            </button>

                            <!-- Dynamic Asset Item Buttons -->
                            <?php foreach ($itemStatus as $t) {
                                $isActive = strtoupper($activeFilter) === strtoupper($t->name); ?>
                                <button type="button"
                                    class="nav-item nav-link btn btn-sm text-uppercase tip medium-bold <?= $isActive ? 'active btn-primary' : '' ?>"
                                    data-filter="<?= $t->name; ?>" title="Show only <?= $t->name; ?>">
                                    <i class="fa fa-folder icon"></i> <?= $t->name; ?>
                                </button>
                            <?php } ?>
                        </div>

                        <br />
                    </div>

                    <div class="col-lg-12 mt-25 order-lg-0 order-md-0">
                        <div class="mb-4" style="margin-bottom: 20px;">
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-between text-center gap-2">

                                    <div class="flex-fill text-center" style="max-width: 19%;">
                                        <div class="expiry-box green component-stat tone-cyan">
                                            <span class="component-stat-icon"><i class="fa fa-cubes"></i></span>
                                            <h4>Total Components</h4>
                                            <h2 id="totalAssets">0</h2>
                                            <p>Registered inventory</p>
                                        </div>
                                    </div>

                                    <div class="flex-fill text-center" style="max-width: 19%;">
                                        <div class="expiry-box blue component-stat tone-green">
                                            <span class="component-stat-icon"><i class="fa fa-check-circle"></i></span>
                                            <h4>Serviceable</h4>
                                            <h2 id="totalItemsInUse">0</h2>
                                            <p>Ready for operation</p>
                                        </div>
                                    </div>

                                    <div class="flex-fill text-center" style="max-width: 19%;">
                                        <div class="expiry-box green component-stat tone-purple">
                                            <span class="component-stat-icon"><i class="fa fa-archive"></i></span>
                                            <h4>In Store</h4>
                                            <h2 id="storelocationItemCount">0</h2>
                                            <p>Available in storage</p>
                                        </div>
                                    </div>

                                    <div class="flex-fill text-center" style="max-width: 19%;">
                                        <div class="expiry-box blue component-stat tone-red">
                                            <span class="component-stat-icon"><i class="fa fa-times-circle"></i></span>
                                            <h4>Unserviceable</h4>
                                            <h2 id="faultyItemCount">0</h2>
                                            <p>Requires attention</p>
                                        </div>
                                    </div>

                                    <div class="flex-fill text-center" style="max-width: 19%;">
                                        <div class="expiry-box green component-stat tone-amber">
                                            <span class="component-stat-icon"><i class="fa fa-wrench"></i></span>
                                            <h4>In Maintenance</h4>
                                            <h2 id="totalAssetsInMaintenance">0</h2>
                                            <p>Work underway</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="component-table-heading">
                        <div>
                            <small>Component Directory</small>
                            <h3>Registered Components</h3>
                            <p>Review identity, manufacturer, assignment, location and current status.</p>
                        </div>
                        <span><i class="fa fa-database"></i> Live registry</span>
                    </div>
                    <div class="table-responsive component-table-card">
                        <!-- <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                            <form action="<?= site_url('assets/generateQrPDF') ?>" method="POST">
                                <button
                                    style="width: 50px; background-color: #262121 !important; border-radius: 50px; border: 0px;"
                                    type="submit" class="btn btn-primary btn-sm mb-3"><i
                                        class="fa fa-download"></i></button>
                            </form>
                        </div> -->
                        <table class="table" id="assets" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <!-- <th><a href="javascript:void(0)" id="select_all_checkboxes">Select All</a></th> -->

                                    <th>Name</th>
                                    <th>Vendor Part Number</th>
                                    <th>Manufacturer Name</th>
                                    <!-- <th>Manufacturer Part Number</th>
                                    <th>Manufacturer Drawing Number</th> -->
                                    <th>Asset Name</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- eye icone button modal  -->
    <div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Asset Types</h5>
                    <button type="button" class="btn-close hideEyeModal" data-bs-dismiss="modal"
                        aria-label="Close">X</button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <!-- Dynamic content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary hideEyeModal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>






<script>
    window.assets = <?php echo json_encode($assets); ?>;
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="modal fade" tabindex="-1" role="dialog" id="addMileageModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add mileage - <span class="equipment_registration"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("assets/add_mileage"); ?>" method="post">
                <div class="modal-body row">

                    <?= $this->steve->form_group_label_input("number", "mileage", "Current mileage", "col-sm-12", 1, '', 10); ?>

                    <?= $this->steve->form_group_label_input("text", "record_date", "Record date", "col-sm-12 date_picker_now", 1, '', 10); ?>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" class="equipment_id" />
                    <button type="submit" class="btn btn-success">Add mileage</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="addScheduledMaintenanceModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add scheduled maintenance - <span class="equipment_registration"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("assets/add_scheduled_maintenance"); ?>" method="post">
                <div class="modal-body">

                    <?= $this->steve->form_group_label_input("text", "next_maintenance_date", "Next scheduled maintenance date", "date_picker", 0, '', 10); ?>

                    <?= $this->steve->form_group_label_input("number", "next_maintenance_mileage", "Next scheduled maintenance mileage"); ?>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" class="equipment_id" />
                    <button type="submit" class="btn btn-success">Add scheduled maintenance</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if ($this->user_model->has_perm("add_equipments")) { ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Component</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form class="form-horizontal scrollable-form" action="<?= site_url("items/add"); ?>" method="post"
                    enctype="multipart/form-data">
                    <div class="modal-body row">
                        <!-- Container for items (Ensure this container exists) -->
                        <div id="itemContainer">
                            <div class="itemSection">
                                <div class="modal-body row">


                                    <div class="col-sm-4 form-group">
                                        <label for="asset_id">Asset</label>
                                        <select name="equipment_name[]" id="asset_id" class="form-control searchable-dropdown">
                                            <option value="<?= $items->asset_id ?>">Select Asset</option>
                                            <?php foreach ($equipments as $pn): ?>
                                                <option value="<?= $pn->equipment_id ?>"
                                                    <?= ($pn->equipment_id == $items->asset_id) ? 'selected' : ''; ?>>
                                                    <?= $pn->equipment_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <?= $this->steve->form_group_label_input("text", "item[]", "Component", "col-sm-4", 0, $info->item, 125); ?>
                                    <!-- Serial Number -->
                                    <?= $this->steve->form_group_label_input("text", "serial_number[]", "Serial Number", "col-sm-4", 1); ?>


                                    <!-- Vendor Part Number Dropdown -->
                                    <div class="col-sm-4 form-group">
                                        <label for="vendor_part_number">Vendor Part Number</label>
                                        <select name="vendor_part_number[]" class="form-control">
                                            <option value="">Select Vendor Part Number</option>
                                            <?php foreach ($part_number as $part): ?>
                                                <option value="<?= $part['part_number']; ?>"
                                                    <?= ($part['part_number'] == $info->vendor_part_number) ? 'selected' : ''; ?>>
                                                    <?= $part['part_number']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Manufacturer Name -->
                                    <!-- <?= $this->steve->form_group_label_input("text", "manufacturer_name[]", "Manufacturer Name", "col-sm-4", 0, $info->manufacturer_name, 125); ?> -->


                                    <!-- Manufacturer Name with Searchable Dropdown -->
                                    <div class="form-group col-sm-4 uppercase">
                                        <label for="manufacturer_dropdown">Manufacturer Name</label><br />
                                        <select name="manufacturer_name[]" id="manufacturer_name"
                                            class="form-control searchable-dropdown">
                                            <option value="">--Select--</option>
                                            <?php foreach ($manufacturer_number as $mn): ?>
                                                <option value="<?= $mn->manufacturer_name ?>"
                                                    <?= ($mn->manufacturer_name == $items->manufacturer_name) ? 'selected' : ''; ?>>
                                                    <?= $mn->manufacturer_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>


                                    <!-- Manufacturer Drawing Number Dropdown -->
                                    <!-- <div class="col-sm-4 form-group">
                                        <label for="manufacturer_drawing_number">Manufacturer Drawing #</label>
                                        <select name="manufacturer_drawing_number[]" class="form-control">
                                            <option value="">--Select--</option>
                                            <?php foreach ($drawing_number as $drawing): ?>
                                                <option value="<?= $drawing['drawing_number']; ?>"
                                                    <?= ($drawing['drawing_number'] == $info->manufacturer_drawing_number) ? 'selected' : ''; ?>>
                                                    <?= $drawing['drawing_number']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div> -->

                                    <div class="form-group col-sm-4 uppercase">
                                        <label>Component Status</label><br />
                                        <select class="form-control" class="p-0" name="item_status[]">
                                            <option value="0">--Select--</option>
                                            <?php foreach ($itemStatus as $is) { ?>
                                                <option value="<?= $is->id ?>"><?= $is->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-4 uppercase">
                                        <label>Store Location</label><br />
                                        <select class="form-control" class="p-0" name="store_location_item[]">
                                            <option value="0">--Select--</option>
                                            <?php foreach ($storeLocation as $sl) { ?>
                                                <option value="<?= $sl->id ?>"><?= $sl->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-4 uppercase">
                                        <label>Component Type</label><br />
                                        <select class="form-control item-type-calibration" name="item_type[]">
                                            <option value="0">--Select--</option>
                                            <?php foreach ($itemTypes as $it) { ?>
                                                <option value="<?= $it->id ?>"><?= $it->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- Item Picture Upload -->
                                    <div class="col-sm-4 form-group">
                                        <label for="item_picture">Component Picture</label>
                                        <input type="file" name="item_picture[]" accept="image/*" class="form-control" />
                                    </div>

                                    <div class="form-group col-sm-4 uppercase" id="faulty_type_field_item">
                                        <label>Faulty Type</label><br />
                                        <select class="form-control" id="faulty_type_item" class="p-0"
                                            name="faulty_type_item[]">
                                            <option value="">--Select--</option>
                                            <?php foreach ($faulty as $f) { ?>
                                                <option value="<?= $f->id; ?>"><?= $f->fault_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- celebration -->

                                    <div class="form-group col-sm-4 uppercase calibration_date_item" id="calibration_date_item"
                                        style="display: none;">
                                        <label>1st Calibration Date</label><br />
                                        <input type="date" class="form-control" name="calibration_date_item[]"
                                            placeholder="1st Calibration Date">
                                    </div>

                                    <div class="form-group col-sm-4 uppercase" id="frequency_day_item" style="display: none;">
                                        <label>Frequency In Days</label><br />
                                        <input type="text" class="form-control" name="frequency_day_item[]" placeholder="90">
                                    </div>

                                    <div class="form-group col-sm-4 uppercase" id="reminder_day_item" style="display: none;">
                                        <label>Reminder In Days</label><br />
                                        <input type="text" class="form-control" name="reminder_day_item[]" placeholder="7">
                                    </div>

                                    <!-- maintenance -->

                                    <div class="form-group col-sm-4 uppercase maintenance_date_item" id="maintenance_date_item"
                                        style="display: none;">
                                        <label>Maintenance Date</label><br />
                                        <input type="date" class="form-control" name="maintenance_date_item[]"
                                            placeholder="Maintenance Date">
                                    </div>

                                    <div class="form-group col-sm-4 uppercase" id="frequency_year_item" style="display: none;">
                                        <label>Frequency In Years</label><br />
                                        <input type="text" class="form-control" name="frequency_year_item[]" placeholder="90">
                                    </div>

                                    <div class="form-group col-sm-4 uppercase" id="maintenance_reminder_day_item" style="display: none;">
                                        <label>Reminder In Days</label><br />
                                        <input type="text" class="form-control" name="maintenance_reminder_day_item[]" placeholder="30">
                                    </div>


                                    <div class="col-md-12"></div>
                                    <div class="col-md-6">
                                        <label for="">Check for Faulty type</label>
                                        <input type="checkbox" id="faulty_type_toggle_item">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button to Add More Items -->
                    <!-- <div class="col-sm-4">
                        <button type="button" class="btn btn-primary" id="addItemButton">Add More Items</button>
                    </div> -->

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
<?php } ?>


<!-- JavaScript to handle adding/removing item fields -->
<script>
    // Wait for the DOM to load
    document.addEventListener("DOMContentLoaded", function() {

        // Get references to the item container and the "Add More Items" button
        var itemContainer = document.getElementById('itemContainer');
        var addItemButton = document.getElementById('addItemButton');

        // Function to add remove button functionality
        function addRemoveButtonEvent(section) {
            var removeButton = section.querySelector('.removeItemButton');
            removeButton.addEventListener('click', function() {
                // Ensure original fields are not removed
                if (section !== originalItemSection) {
                    section.remove(); // Remove the cloned item section
                }
            });
        }

        // Get the first item section (original) and flag it as non-deletable
        var originalItemSection = document.querySelector('.itemSection');

        // Event listener for the "Add More Items" button
        addItemButton.addEventListener('click', function() {
            // Clone the original item section
            var newItemSection = originalItemSection.cloneNode(true);

            // Clear input fields in the cloned section
            newItemSection.querySelectorAll('input').forEach(input => input.value = '');

            // Reset select dropdowns
            newItemSection.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

            // Ensure #calibration_asset_item is unchecked by default

            // Remove any existing remove buttons from cloned section
            newItemSection.querySelectorAll('.removeItemButton').forEach(button => button.remove());
            const calibrationDate = newItemSection.querySelector('#calibration_date_item');
            const frequencyDay = newItemSection.querySelector('#frequency_day_item');
            const reminderDay = newItemSection.querySelector('#reminder_day_item');

            const maintenanceDate = newItemSection.querySelector('#maintenance_date_item');
            const frequencyYear = newItemSection.querySelector('#frequency_year_item');
            const MaintenanceReminderDay = newItemSection.querySelector('#maintenance_reminder_day_item');

            calibrationDate.style.display = 'none';
            frequencyDay.style.display = 'none';
            reminderDay.style.display = 'none';

            maintenanceDate.style.display = 'none';
            frequencyYear.style.display = 'none';
            MaintenanceReminderDay.style.display = 'none';
            // Append the cloned item section to the container
            itemContainer.appendChild(newItemSection);

            // Add remove button for cloned item section
            var removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.classList.add('btn', 'btn-danger', 'removeItemButton', 'form-group');
            removeButton.textContent = 'X';
            newItemSection.querySelector('.modal-body').appendChild(removeButton);

            // Remove the section when the "X" button is clicked
            removeButton.addEventListener('click', function() {
                newItemSection.remove();
            });

            // Toggle faulty type field visibility based on checkbox
            var faultyCheckbox = newItemSection.querySelector('#faulty_type_toggle_item');
            faultyCheckbox.addEventListener('change', function() {
                var faultyTypeField = newItemSection.querySelector('#faulty_type_field_item');
                faultyTypeField.style.display = faultyCheckbox.checked ? 'block' : 'none';
            });


        });


        // Disable removal for the original item section
        var initialItemSections = document.querySelectorAll('.itemSection');
        initialItemSections.forEach(function(section) {
            // Only apply the delete functionality if it's a cloned section
            if (section !== originalItemSection) {
                addRemoveButtonEvent(section);
            }
        });
    });
</script>

<!-- jQuery Script to Enable Dropdown and Filtering -->
<script>
    $(document).ready(function() {

        // Initialize Select2 on Asset dropdown
        $('#asset_id').select2({
            placeholder: "Select Asset", // Optional
            allowClear: true // Allow clearing the selection
        });

        // Initialize Select2 on Manufacturer Name dropdown
        $('#manufacturer_name').select2({
            placeholder: "Select Manufacturer", // Optional
            allowClear: true // Allow clearing the selection
        });

        // Optional: If you need to show a custom "Manufacturer Name" on selection
        $('#manufacturer_name').on('change', function() {
            var selectedText = $(this).find('option:selected').text();
            $('#manufacturer_dropdown .selected-text').text(selectedText);
        });

        // Toggle Manufacturer Dropdown visibility
        $('#manufacturer_dropdown').on('click', function(e) {
            e.stopPropagation();
            $('#manufacturer_searchable_dropdown .dropdown-search').toggle();
        });

        // Toggle Asset Dropdown visibility (if needed)
        $('#asset_id').on('click', function(e) {
            e.stopPropagation();
            $('#asset_searchable_dropdown .dropdown-search').toggle();
        });

        // Manufacturer Search Filter
        $('#manufacturer_search').on('keyup', function(e) {
            e.stopPropagation(); // Prevent closing the dropdown
            var searchText = $(this).val().toLowerCase();
            $('#manufacturer_options .dropdown-item').each(function() {
                var optionText = $(this).text().toLowerCase();
                $(this).toggle(optionText.includes(searchText));
            });
        });

        // Asset Search Filter (if needed)
        $('#asset_search').on('keyup', function(e) {
            e.stopPropagation(); // Prevent closing the dropdown
            var searchText = $(this).val().toLowerCase();
            $('#asset_options .dropdown-item').each(function() {
                var optionText = $(this).text().toLowerCase();
                $(this).toggle(optionText.includes(searchText));
            });
        });

        // Select Manufacturer Option
        $('#manufacturer_options .dropdown-item').on('click', function(e) {
            e.stopPropagation();
            var selectedValue = $(this).data('value');
            var selectedText = $(this).text();

            $('#manufacturer_dropdown .selected-text').text(selectedText);
            $('#manufacturer_name').val(selectedValue);

            $('#manufacturer_searchable_dropdown .dropdown-search').hide();
        });

        // Select Asset Option
        $('#asset_id').on('change', function(e) {
            e.stopPropagation();
            var selectedValue = $(this).val();
            var selectedText = $(this).find('option:selected').text();

            $('#asset_id').val(selectedValue);
        });

        // Close dropdown when clicking outside of any searchable-dropdown
        $(document).on('click', function(e) {
            // Only close if the click is outside the dropdowns
            if (!$(e.target).closest('.searchable-dropdown').length) {
                $('.dropdown-search').hide();
            }
        });
    });
</script>
