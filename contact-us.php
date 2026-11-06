<?php
$pageTitle = "Contact Us | AKC Packaging Technology & Research Centre";
$metaDescription = "Reach out to AKC's technical specialists for testing specifications, quotes, and sample submissions.";
include 'includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<!-- Custom Stylesheet -->
<style>
    /* ==========================================================================
           GLOBAL VARIABLES & RESET
           ========================================================================== */
    :root {
        --primary-blue: #0056b3;
        --text-dark: #1e1e1e;
        --text-gray: #555555;
        --white: #ffffff;
        --bg-light: #f8f9fa;
        --border-color: #e0e0e0;
        --light-blue: #00b4d8;
        --accent-red: #e63946;
        --dark-bg: #1a1f2b;
        --text-light: #666666;
        --container-width: 1200px;
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        font-family: 'Montserrat', sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
        overflow-x: hidden;
        max-width: 100%;
        background-color: var(--white);
    }

    button,
    input,
    select,
    textarea {
        font-family: inherit;
        color: inherit;
    }

    a {
        text-decoration: none;
        color: inherit;
        transition: var(--transition);
    }

    ul {
        list-style: none;
    }

    img {
        max-width: 100%;
        height: auto;
        display: block;
    }

    .container {
        max-width: var(--container-width);
        margin: 0 auto;
        padding: 0 20px;
    }

    .text-center {
        text-align: center;
    }

    /* Utilities */
    .text-blue {
        color: var(--light-blue);
    }

    /* ==========================================================================
           CORE BUTTON & TYPOGRAPHY STYLES
           ========================================================================== */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 28px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-primary {
        background-color: var(--primary-blue);
        color: var(--white);
        border: 2px solid var(--primary-blue);
    }

    .btn-primary:hover {
        background-color: transparent;
        color: var(--primary-blue);
    }

    .btn-transparent {
        background-color: transparent;
        color: var(--white);
        border: 2px solid var(--white);
    }

    .btn-transparent:hover {
        background-color: var(--white);
        color: var(--primary-blue);
    }

    .btn-outline {
        border: 1px solid #ddd;
        background: transparent;
        color: var(--text-dark);
    }

    .btn-outline:hover {
        background: #f5f5f5;
    }

    .btn-white {
        background-color: var(--white);
        color: var(--primary-blue);
        border: 2px solid var(--white);
    }

    .btn-white:hover {
        background-color: transparent;
        color: var(--white);
    }

    .btn-outline-white {
        background-color: transparent;
        color: var(--white);
        border: 2px solid var(--white);
    }

    .btn-outline-white:hover {
        background-color: var(--white);
        color: var(--primary-blue);
    }

    .btn-icon-bi {
        margin-right: 8px;
        font-size: 1.1rem;
        vertical-align: middle;
    }

    .btn-full {
        width: 100%;
    }

    /* Section Subtitle & Title */
    .section-subtitle {
        display: flex;
        align-items: center;
        font-size: 14px;
        font-weight: 500;
        color: var(--accent-red);
        letter-spacing: 0.5px;
        margin-top: 0;
        margin-bottom: 8px;
    }

    .section-subtitle.center {
        justify-content: center;
    }

    .section-subtitle .line {
        display: inline-block;
        width: 30px;
        height: 2px;
        background-color: var(--accent-red);
        margin: 0 10px;
    }

    .section-title {
        font-size: 32px;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 8px;
        color: #081D33;
    }

    .section-desc {
        max-width: 700px;
        margin: 0 auto 50px;
        font-size: 16px;
        line-height: 1.6;
        color: var(--text-light);
    }

    /* ==========================================================================
           HEADER & NAVIGATION STYLES
           ========================================================================== */
    /* Top Bar */


    /* Header */







    /* ==========================================================================
           CONTACT PAGE-SPECIFIC SECTIONS & CUSTOM GRID LAYOUTS
           ========================================================================== */
    .contact-page section {
        padding: 80px 0;
    }

    /* ---- HERO BANNER ---- */
    .contact-hero {
        position: relative;
        background-image: url('assets/images/contact_us/contact_us_banner.webp');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 60vh;
        display: flex;
        align-items: center;
        color: var(--white);
        overflow: hidden;
        padding: 100px 0 !important;
    }

    .contact-hero::before {
        display: none;
    }

    .contact-hero-inner {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .contact-hero-badge {
        display: inline-block;
        padding: 8px 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 1px;
        color: var(--light-blue);
        margin-bottom: 25px;
    }

    .contact-hero-title {
        font-size: 42px;
        font-weight: 600;
        line-height: 1.2;
        margin-bottom: 25px;
        color: var(--white);
    }

    .contact-hero-desc {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.85);
        max-width: 650px;
        margin: 0 auto 40px;
        font-weight: 400;
    }

    .contact-hero-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    /* ---- 5 CONTACT CARDS ---- */
    .contact-info-cards {
        background-color: var(--white);
        padding-bottom: 80px !important;
    }

    .contact-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-top: -50px;
        position: relative;
        z-index: 10;
    }

    .info-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        min-height: 200px;
        gap: 0px;
    }

    .info-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 35px rgba(0, 86, 179, 0.08);
        border-color: rgba(0, 86, 179, 0.25);
    }

    .info-card-icon {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.4rem;
        transition: all 0.3s ease;
    }

    .info-card:nth-child(1) .info-card-icon {
        background: #fee2e2;
        color: #ef4444;
    }

    .info-card:nth-child(2) .info-card-icon {
        background: #dbeafe;
        color: #3b82f6;
    }

    .info-card:nth-child(3) .info-card-icon {
        background: #f3e8ff;
        color: #a855f7;
    }

    .info-card:nth-child(4) .info-card-icon {
        background: #ccfbf1;
        color: #0d9488;
    }

    .info-card:nth-child(5) .info-card-icon {
        background: #e0f2fe;
        color: #0284c7;
    }

    .info-card-title {
        font-size: 12px;
        font-weight: 500;
        color: #94a3b8;
        letter-spacing: 1px;
        margin-bottom: 5px;
        text-align: center !important;
        width: 100%;
        display: block;
    }

    .info-card-text {
        font-size: 14px;
        color: var(--text-dark);
        font-weight: 500;
        line-height: 1.5;
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center !important;
        width: 100%;
    }

    .info-card-link {
        font-size: 13px;
        font-weight: 500;
        color: var(--primary-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: var(--transition);
        width: 100%;
        text-align: center !important;
        margin-top: auto;
    }

    .info-card-link:hover {
        color: #004499;
        gap: 10px;
    }

    /* ---- INQUIRY & VALUES SECTION ---- */
    .contact-inquiry {
        background-color: var(--bg-light);
    }

    .inquiry-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 40px;
        align-items: start;
    }

    .inquiry-form-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.02);
    }

    .contact-form-header {
        margin-bottom: 30px;
    }

    .contact-form-header h3 {
        font-size: 24px;
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 5px;
    }

    .contact-form-header p {
        font-size: 14px;
        color: var(--text-light);
    }

    .contact-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
        text-align: left;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 500;
        color: #475569;
        letter-spacing: 0.5px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 14px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background-color: #fff;
        color: var(--text-dark);
        outline: none;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #041C3A 0%, #0A5CCF 100%);
        color: var(--white);
        border: none;
        border-radius: 8px;
        padding: 16px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: var(--transition);
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #0A5CCF 0%, #041C3A 100%);
        box-shadow: 0 8px 20px rgba(10, 92, 207, 0.2);
    }

    /* Value Cards Right Column */
    .inquiry-values {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .value-dark-card {
        background: linear-gradient(135deg, #041C3A 0%, #0A5CCF 100%);
        border-radius: 16px;
        padding: 40px 30px;
        color: var(--white);
        text-align: center;
        box-shadow: 0 15px 30px rgba(4, 28, 58, 0.15);
    }

    .value-dark-card-icon {
        font-size: 3rem;
        color: var(--light-blue);
        margin-bottom: 20px;
    }

    .value-dark-card h4 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .value-dark-card p {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.5;
    }

    .value-row-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.01);
        transition: var(--transition);
        text-align: left;
    }

    .value-row-card:hover {
        transform: translateX(5px);
        border-color: rgba(0, 86, 179, 0.15);
    }

    .value-row-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: linear-gradient(135deg, #041C3A 0%, #0A5CCF 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .value-row-info h5 {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .value-row-info p {
        font-size: 13px;
        color: var(--text-light);
        margin-bottom: 0;
        line-height: 1.4;
    }

    /* ---- OFFICE LOCATION ---- */
    .contact-location {
        background-color: var(--white);
    }

    .location-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 40px;
        align-items: stretch;
    }

    .location-map-container {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        min-height: 350px;
    }

    .location-map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

    .location-details-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: left;
    }

    .location-header {
        margin-bottom: 30px;
    }

    .location-header h3 {
        font-size: 24px;
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .location-list {
        display: flex;
        flex-direction: column;
        gap: 25px;
        margin-bottom: 35px;
    }

    .location-item {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .location-item-icon {
        font-size: 1.2rem;
        color: var(--primary-blue);
        margin-top: 2px;
    }

    .location-item-text {
        font-size: 14px;
        color: var(--text-dark);
        line-height: 1.5;
    }

    .location-item-text strong {
        display: block;
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    /* ---- BUSINESS HOURS ---- */
    .contact-hours {
        background-color: var(--bg-light);
    }

    .hours-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
    }

    .hours-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        text-align: left;
    }

    .hours-title-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 30px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 20px;
    }

    .hours-title-row i {
        font-size: 1.4rem;
        color: var(--primary-blue);
    }

    .hours-title-row h3 {
        font-size: 20px;
        font-weight: 600;
        color: #0f172a;
    }

    .schedule-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .schedule-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
    }

    .schedule-day {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        color: var(--text-dark);
    }

    .schedule-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .schedule-dot.open {
        background-color: #22c55e;
    }

    .schedule-dot.closed {
        background-color: #ef4444;
    }

    .schedule-time {
        font-weight: 500;
        color: #0f172a;
    }

    .schedule-time.closed {
        color: #ef4444;
    }

    .channels-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 25px;
    }

    .channel-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
    }

    .channel-info {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        color: var(--text-dark);
    }

    .channel-status {
        background-color: #ecfdf5;
        color: #059669;
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    .channel-status.always {
        background-color: #eff6ff;
        color: var(--primary-blue);
    }

    .hours-note-box {
        background-color: #fffbeb;
        border: 1px dashed #f59e0b;
        border-radius: 8px;
        padding: 16px 20px;
        display: flex;
        gap: 15px;
        align-items: flex-start;
        margin-top: 25px;
    }

    .hours-note-box i {
        color: #d97706;
        font-size: 1.1rem;
        margin-top: 2px;
    }

    .hours-note-box p {
        font-size: 13px;
        color: #b45309;
        line-height: 1.4;
        margin-bottom: 0;
        font-weight: 500;
    }

    /* ---- FAQ SECTION ---- */
    .contact-faq {
        background-color: var(--white);
    }

    .faq-grid {
        display: grid;
        grid-template-columns: 1fr 1.3fr;
        gap: 60px;
        align-items: start;
    }

    .faq-intro {
        text-align: left;
    }

    .faq-intro .section-subtitle {
        margin-top: 0;
        margin-bottom: 8px;
    }

    .faq-intro .section-title {
        font-size: 32px;
        font-weight: 600;
        line-height: 1.2;
        margin-top: 0;
        margin-bottom: 8px;
    }

    .faq-intro-desc {
        font-size: 16px;
        color: var(--text-light);
        line-height: 1.6;
        margin-bottom: 35px;
    }

    .faq-cta-card {
        background: linear-gradient(135deg, #041C3A 0%, #0A5CCF 100%);
        color: var(--white);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(4, 28, 58, 0.08);
    }

    .faq-cta-card h4 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .faq-cta-card p {
        font-size: 13.5px;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 20px;
        line-height: 1.4;
    }

    .faq-cta-card .btn {
        background-color: var(--white);
        color: #003366;
        border: 2px solid var(--white);
        padding: 10px 20px;
        font-size: 13px;
        border-radius: 6px;
        font-weight: 500;
    }

    .faq-cta-card .btn:hover {
        background-color: transparent;
        color: var(--white);
    }

    /* Accordion Styles */
    .faq-accordion {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .accordion-item {
        background-color: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        text-align: left;
    }

    .accordion-item:hover {
        border-color: rgba(0, 86, 179, 0.2);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
    }

    .accordion-item details {
        width: 100%;
    }

    .accordion-item summary {
        padding: 22px 25px;
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        list-style: none;
        outline: none;
        user-select: none;
    }

    .accordion-item summary::-webkit-details-marker {
        display: none;
    }

    .accordion-item summary::after {
        content: '\F64F';
        font-family: 'bootstrap-icons';
        font-size: 1.1rem;
        color: var(--primary-blue);
        transition: transform 0.3s ease;
    }

    .accordion-item details[open] summary::after {
        content: '\F65F';
        transform: rotate(180deg);
    }

    .accordion-content {
        padding: 0 25px 22px 25px;
        font-size: 14px;
        color: #475569;
        line-height: 1.6;
        border-top: 1px solid #f1f5f9;
        padding-top: 15px;
        background-color: #fafbfc;
    }

    /* ==========================================================================
           FOOTER STYLES
           ========================================================================== */
    .footer {
        background-color: #0c1a26;
        color: var(--white);
        font-size: 14px;
    }



    .footer-tagline {
        opacity: 0.6;
        font-size: 13px;
    }

    .footer-badges {
        display: flex;
        gap: 15px;
    }

    .badge {
        background: rgba(255, 255, 255, 0.05);
        padding: 8px 15px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .badge .dot {
        width: 6px;
        height: 6px;
        background: var(--light-blue);
        border-radius: 50%;
    }















    .contact-item {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }

    .contact-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--light-blue);
        font-size: 1.1rem;
    }

    .contact-text {
        display: flex;
        flex-direction: column;
    }

    .contact-text .label {
        font-size: 11px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.4);
        margin-bottom: 5px;
    }

    .contact-text .value {
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        line-height: 1.4;
    }

    .contact-text .sub {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 3px;
    }






    /* ==========================================================================
           RESPONSIVE BREAKPOINTS
           ========================================================================== */
    @media (max-width: 1200px) {
        .contact-cards-grid {
            grid-template-columns: repeat(3, 1fr);
            margin-top: -30px;
            gap: 16px;
        }
    }

    @media (max-width: 992px) {
        .contact-hero-title {
            font-size: 44px;
        }

        .inquiry-grid,
        .location-grid,
        .hours-grid,
        .faq-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .contact-cards-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    @media (max-width: 768px) {
        .contact-cards-grid {
            grid-template-columns: 1fr;
            margin-top: -20px;
        }
    }

    @media (max-width: 640px) {
        .contact-hero-title {
            font-size: 32px;
        }

        .contact-hero-buttons {
            flex-direction: column;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            gap: 10px;
        }

        .contact-cards-grid {
            grid-template-columns: 1fr;
        }

        .contact-form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .form-group.full-width {
            grid-column: span 1;
        }

        .inquiry-form-card {
            padding: 25px;
        }

        .info-card {
            min-height: auto;
        }

        .location-details-card,
        .hours-card {
            padding: 25px;
        }
    }

    @media (max-width: 576px) {}
</style>

<div class="contact-page">

    <!-- Hero Banner Section -->
    <section class="contact-hero">
        <div class="container">
            <div class="contact-hero-inner">
                <span class="contact-hero-badge">AKC PACKAGING TECHNOLOGY & RESEARCH CENTRE</span>
                <h1 class="contact-hero-title">Let's Connect & Build<br>Better Solutions</h1>
                <p class="contact-hero-desc">Connect with our experts for testing, quality assessment, and
                    certification queries. We are ready to assist you build a safer, better world.</p>
                <div class="contact-hero-buttons">
                    <a href="javascript:void(0)"
                        onclick="document.getElementById('inquiry').scrollIntoView({behavior: 'smooth'})"
                        class="btn btn-white">Request a Test</a>
                    <a href="tel:+918860407220" class="btn btn-outline-white"><i
                            class="bi bi-telephone-fill btn-icon-bi"></i> Call Us Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Cards Section -->
    <section class="contact-info-cards">
        <div class="container">
            <div class="contact-cards-grid reveal-group">

                <!-- Card 1: Office Location -->
                <div class="info-card reveal-child">
                    <div class="info-card-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="info-card-title">Office Location</div>
                    <div class="info-card-text">
                        <span>Plot 108, Phase 2, Sector 155,</span>
                        <span>Noida, Uttar Pradesh 201304</span>
                    </div>
                    <a href="javascript:void(0)"
                        onclick="document.getElementById('map').scrollIntoView({behavior: 'smooth'})"
                        class="info-card-link">View Location <i class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Card 2: Email -->
                <div class="info-card reveal-child">
                    <div class="info-card-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div class="info-card-title">Email Us</div>
                    <div class="info-card-text">
                        info@akc-ptarc.com
                    </div>
                    <a href="mailto:info@akc-ptarc.com" class="info-card-link">Email Us <i
                            class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Card 3: Phone -->
                <div class="info-card reveal-child">
                    <div class="info-card-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div class="info-card-title">Phone Number</div>
                    <div class="info-card-text">
                        +91 88604 07220<br>
                        Mon-Sat, 9:00 AM to 6:30 PM
                    </div>
                    <a href="tel:+918860407220" class="info-card-link">Call Now <i class="bi bi-arrow-right"></i></a>
                </div>



            </div>
        </div>
    </section>

    <!-- Send Us an Inquiry Form Section -->
    <section class="contact-inquiry" id="inquiry">
        <div class="container text-center">

            <div class="reveal" style="margin-bottom: 40px;">
                <div class="section-subtitle center">
                    <span class="line"></span> Reach Us <span class="line"></span>
                </div>
                <h2 class="section-title">Send Us an Inquiry</h2>
                <p class="section-desc">Reach out with your testing specifications and our technical specialists will
                    respond within 24 hours.</p>
            </div>

            <div class="inquiry-grid reveal-group">

                <!-- Left: Interactive Form -->
                <div class="inquiry-form-card reveal-left">
                    <div class="contact-form-header">
                        <h3>Inquiry Details</h3>
                        <p>All fields are required unless marked optional</p>
                    </div>

                    <form action="#" method="POST">
                        <div class="contact-form-row">
                            <div class="form-group">
                                <label for="fullname">Full Name</label>
                                <input type="text" id="fullname" placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="company">Company Name</label>
                                <input type="text" id="company" placeholder="Enter your company name" required>
                            </div>
                        </div>

                        <div class="contact-form-row">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" placeholder="name@company.com" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" placeholder="Enter contact number" required>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="service">Select Testing Service</label>
                            <select id="service" required>
                                <option value="" disabled selected>Select service category</option>
                                <option value="plastics">Plastics & Resin Testing</option>
                                <option value="pet">PET Bottle Testing</option>
                                <option value="chemicals">Chemical Testing & Analysis</option>
                                <option value="woven">Woven & Non-Woven Testing</option>
                                <option value="medical">Medical Devices Compliance</option>
                                <option value="other">Other Accreditations</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="message">Message / Inquiry Details</label>
                            <textarea id="message"
                                placeholder="Provide detailed testing specifications, material types, standards to comply (e.g. ISO, ASTM), and sample quantities..."
                                required></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Submit Inquiry</span> <i class="bi bi-chevron-right"></i>
                        </button>
                    </form>
                </div>

                <!-- Right: Values Column -->
                <div class="inquiry-values reveal-right">

                    <!-- Dark Premium Card -->
                    <div class="value-dark-card">
                        <div class="value-dark-card-icon">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <h4>Industrial Testing Experts</h4>
                        <p>Fully certified laboratory testing, inspection, certification, and training services
                            tailored to global manufacturing standards.</p>
                    </div>

                    <!-- Row Card 1 -->
                    <div class="value-row-card">
                        <div class="value-row-icon">
                            <i class="bi bi-lightning-charge-fill" style="color: #ffd166;"></i>
                        </div>
                        <div class="value-row-info">
                            <strong
                                style="display: block; font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.5px; margin-bottom: 4px;">Response
                                Time</strong>
                            <h5 style="margin-bottom: 4px;">Within 24 Business Hours</h5>
                            <p>Urgent queries via phone: 2-4 hrs</p>
                        </div>
                    </div>

                    <!-- Row Card 2 -->
                    <div class="value-row-card">
                        <div class="value-row-icon">
                            <i class="bi bi-clipboard2-check-fill" style="color: #fff;"></i>
                        </div>
                        <div class="value-row-info">
                            <strong
                                style="display: block; font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.5px; margin-bottom: 4px;">What
                                Happens Next</strong>
                            <h5 style="margin-bottom: 4px;">Free Technical Consultation</h5>
                            <p>Scope review + detailed quote provided</p>
                        </div>
                    </div>

                    <!-- Row Card 3 -->
                    <div class="value-row-card">
                        <div class="value-row-icon">
                            <i class="bi bi-lock-fill" style="color: #ffd166;"></i>
                        </div>
                        <div class="value-row-info">
                            <strong
                                style="display: block; font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.5px; margin-bottom: 4px;">Confidentiality</strong>
                            <h5 style="margin-bottom: 4px;">NDA Available on Request</h5>
                            <p>Your data is safe and never shared</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- Map & Location Details Section -->
    <section class="contact-location" id="map">
        <div class="container">
            <div class="location-grid reveal-group">

                <!-- Left: Embedded Map -->
                <div class="location-map-container reveal-left">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14031.598374784537!2d77.44506212773935!3d28.452442978237144!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cc20e22ffd7fd%3A0xb7d2cc733a642ff!2sSector%20155%2C%20Noida%2C%20Uttar%20Pradesh%20201304!5e0!3m2!1sen!2sin!4v1793789265792!5m2!1sen!2sin"
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>

                <!-- Right: Detailed Address -->
                <div class="location-details-card reveal-right">
                    <div class="location-header">
                        <div class="section-subtitle">
                            <span class="line"></span> Location
                        </div>
                        <h3>Our Office Location</h3>
                    </div>

                    <div class="location-list">

                        <div class="location-item">
                            <div class="location-item-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <div class="location-item-text">
                                <strong>Headquarters Address</strong>
                                Plot 108, Phase 2, Sector 155,<br>
                                Noida, Uttar Pradesh 201304
                            </div>
                        </div>

                        <div class="location-item">
                            <div class="location-item-icon"><i class="bi bi-telephone-fill"></i></div>
                            <div class="location-item-text">
                                <strong>Phone Inquiries</strong>
                                <a href="tel:+918860407220" style="color: inherit; text-decoration: none;">+91 88604
                                    07220</a>
                            </div>
                        </div>

                        <div class="location-item">
                            <div class="location-item-icon"><i class="bi bi-envelope-fill"></i></div>
                            <div class="location-item-text">
                                <strong>Email Address</strong>
                                info@akc-ptarc.com
                            </div>
                        </div>

                    </div>

                    <a href="https://www.google.com/maps/search/?api=1&query=Plot+108,+Phase+2,+Sector+155,+Noida,+Uttar+Pradesh+201304"
                        target="_blank" class="btn btn-primary btn-full">
                        <i class="bi bi-map btn-icon-bi"></i> Get Directions
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Business Hours Section -->
    <section class="contact-hours">
        <div class="container text-center">

            <div class="reveal" style="margin-bottom: 40px;">
                <div class="section-subtitle center">
                    <span class="line"></span> Schedule <span class="line"></span>
                </div>
                <h2 class="section-title">Business Hours</h2>
                <p class="section-desc">Our administrative and testing laboratory staff operate under the following
                    standard schedules.</p>
            </div>

            <div class="hours-grid reveal-group">

                <!-- Weekly Schedule -->
                <div class="hours-card reveal-left">
                    <div class="hours-title-row">
                        <i class="bi bi-calendar-week"></i>
                        <h3>Weekly Schedule</h3>
                    </div>

                    <div class="schedule-list">
                        <div class="schedule-item">
                            <span class="schedule-day"><i class="schedule-dot open"></i> Monday</span>
                            <span class="schedule-time">9:00 AM - 6:30 PM</span>
                        </div>
                        <div class="schedule-item">
                            <span class="schedule-day"><i class="schedule-dot open"></i> Tuesday</span>
                            <span class="schedule-time">9:00 AM - 6:30 PM</span>
                        </div>
                        <div class="schedule-item">
                            <span class="schedule-day"><i class="schedule-dot open"></i> Wednesday</span>
                            <span class="schedule-time">9:00 AM - 6:30 PM</span>
                        </div>
                        <div class="schedule-item">
                            <span class="schedule-day"><i class="schedule-dot open"></i> Thursday</span>
                            <span class="schedule-time">9:00 AM - 6:30 PM</span>
                        </div>
                        <div class="schedule-item">
                            <span class="schedule-day"><i class="schedule-dot open"></i> Friday</span>
                            <span class="schedule-time">9:00 AM - 6:30 PM</span>
                        </div>
                        <div class="schedule-item">
                            <span class="schedule-day"><i class="schedule-dot open"></i> Saturday</span>
                            <span class="schedule-time">9:00 AM - 6:30 PM</span>
                        </div>
                        <div class="schedule-item">
                            <span class="schedule-day"><i class="schedule-dot closed"></i> Sunday</span>
                            <span class="schedule-time closed">Closed</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Channels -->
                <div class="hours-card reveal-right">
                    <div class="hours-title-row">
                        <i class="bi bi-chat-left-text"></i>
                        <h3>Contact Channels</h3>
                    </div>

                    <div class="channels-list">
                        <div class="channel-item">
                            <span class="channel-info"><i class="bi bi-telephone"></i> Phone Support</span>
                            <span class="channel-status">Active</span>
                        </div>
                        <div class="channel-item">
                            <span class="channel-info"><i class="bi bi-envelope"></i> Email Support</span>
                            <span class="channel-status">Active</span>
                        </div>
                    </div>

                    <div class="hours-note-box">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <p>Our corporate office and laboratory are closed during Federal Public Holidays in
                            India. For urgent matters, please contact us via email support.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Frequently Asked Questions Section -->
    <section class="contact-faq">
        <div class="container">
            <!-- Centered Header -->
            <div class="faq-header reveal" style="text-align: center; max-width: 800px; margin: 0 auto 50px;">
                <div class="section-subtitle center"
                    style="justify-content: center; display: flex; align-items: center; color: var(--accent-red); font-weight: 500; letter-spacing: 0.5px; margin-top: 0; margin-bottom: 8px;">
                    <span class="line"
                        style="display: inline-block; width: 30px; height: 2px; background-color: var(--accent-red); margin: 0 10px;"></span>
                    FAQs
                    <span class="line"
                        style="display: inline-block; width: 30px; height: 2px; background-color: var(--accent-red); margin: 0 10px;"></span>
                </div>
                <h2 class="section-title">
                    Frequently Asked Questions</h2>
                <p class="faq-intro-desc" style="color: var(--text-light); margin-bottom: 30px;">Find quick answers to
                    common questions about our testing services, booking, sample submission guidelines, and
                    certification turnaround times.</p>
            </div>

            <!-- 2-Column FAQs -->
            <style>
                .faq-columns {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px;
                    margin-bottom: 60px;
                }

                .faq-col .accordion-item {
                    margin-bottom: 16px;
                }

                @media (max-width: 992px) {
                    .faq-columns {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
            <div class="faq-columns reveal-group">
                <!-- Column 1 -->
                <div class="faq-col reveal-left">
                    <div class="accordion-item">
                        <details open>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Testing
                                        Capabilities</span>
                                    <span>What testing services does AKC offer?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                AKC provides comprehensive testing across polymers, plastics, packaging, rubber, paper,
                                textiles, chemicals, composites, medical devices and allied materials. Our expertise
                                spans physical, mechanical, chemical, thermal, rheological, electrical and analytical
                                testing in accordance with national and international standards.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Testing
                                        Standards</span>
                                    <span>Which standards can AKC test against?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Our laboratories perform testing in accordance with BIS, ISO, ASTM, IEC, EN and
                                customer-specific specifications, subject to the applicable scope of accreditation.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Technical
                                        Guidance</span>
                                    <span>I'm unsure which tests apply to my product. Can AKC help?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Absolutely. Our technical experts work closely with customers to identify the most
                                appropriate test methods, standards and testing programmes based on the product,
                                application and regulatory requirements.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Sample
                                        Submission</span>
                                    <span>How do I submit a sample for testing?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Simply contact us through our enquiry form, email or phone. Our team will guide you on
                                sample quantity, documentation, applicable standards, quotation and shipping
                                instructions before testing begins.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Digital
                                        Sample Tracking</span>
                                    <span>Can I track the progress of my sample online?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Yes. AKC provides digital sample tracking, allowing customers to monitor the status of
                                their samples from receipt through testing to final report issuance. This ensures
                                complete transparency, traceability and faster communication throughout the testing
                                process.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Turnaround
                                        Time</span>
                                    <span>How long does testing usually take?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Turnaround time depends on the product, test methods and applicable standards. Once the
                                testing scope is finalized, we provide a committed delivery schedule so customers can
                                plan their projects with confidence.
                            </div>
                        </details>
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="faq-col reveal-right">
                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Quality
                                        & Accreditation</span>
                                    <span>Are AKC laboratories NABL accredited?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Yes. Our laboratories operate in accordance with ISO/IEC 17025 and maintain NABL
                                accreditation for approved scopes, ensuring technically valid, reliable and
                                internationally recognized test results.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Reports
                                        & Documentation</span>
                                    <span>What information is included in the test report?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Every report includes the applicable standard, test methodology, measured results,
                                observations and, where applicable, acceptance criteria. Reports are reviewed and
                                authorized by qualified technical personnel before release.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Confidentiality</span>
                                    <span>How does AKC protect confidential product information?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                We maintain strict confidentiality throughout the testing process. Customer samples,
                                formulations, technical data and reports are handled through controlled processes and
                                secure digital systems to safeguard sensitive information.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Product
                                        Development</span>
                                    <span>Can AKC support product development and failure analysis?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Yes. In addition to compliance testing, we assist with material characterization,
                                benchmarking, root cause analysis, product validation, performance evaluation and
                                technical support to accelerate product development and improve quality.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Training
                                        & Consultancy</span>
                                    <span>Does AKC provide technical training and consultancy?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                Yes. We conduct customized training programmes, workshops and technical consultancy
                                covering polymer science, packaging technology, laboratory best practices, quality
                                systems, testing standards and regulatory compliance for professionals and
                                organizations.
                            </div>
                        </details>
                    </div>

                    <div class="accordion-item">
                        <details>
                            <summary>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span
                                        style="font-size: 12px; font-weight: 500; color: var(--primary-blue); letter-spacing: 0.3px;">Customer
                                        Support</span>
                                    <span>How can I request a quotation or speak with a technical expert?</span>
                                </div>
                            </summary>
                            <div class="accordion-content">
                                You can submit an enquiry through our website, email or contact our customer support
                                team directly. Our specialists will understand your requirements, recommend suitable
                                testing solutions and provide a detailed quotation with the expected turnaround time.
                            </div>
                        </details>
                    </div>

                    <!-- Column CTA -->
                    <div class="faq-cta-card"
                        style="background: var(--primary-blue); color: white; padding: 40px 30px; border-radius: 12px; text-align: left; box-shadow: 0 10px 30px rgba(0, 86, 179, 0.2); margin-top: 16px;">
                        <h4 style="font-size: 22px; font-weight: 600; margin-bottom: 12px;">Still have questions?</h4>
                        <p style="margin-bottom: 20px; color: rgba(255,255,255,0.9); font-size: 15px;">Our technical
                            team is happy to walk you through any specific requirements or concerns you may have.</p>
                        <a href="tel:+918860407220" class="btn btn-white"
                            style="background: white; color: var(--primary-blue); padding: 12px 24px; border-radius: 8px; font-weight: 500; text-decoration: none; display: inline-block;">Ask
                            Our Team &rarr;</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<!-- Footer -->

<?php include 'includes/footer.php'; ?>