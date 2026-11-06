<div class="healthsync-dashboard">

    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside class="sidebar">

        {{-- BRAND --}}
        <div class="brand">
            <h2>HealthSyncAI</h2>
            <p>Clinical Workflow System</p>
            <small>Admin Dashboard</small>
        </div>


        {{-- NAVIGATION --}}
        <div class="nav-title">
            NAVIGATION
        </div>


        <nav class="navigation">

            <a href="{{ route('dashboard') }}" @class(['nav-item', 'active' => request()->routeIs('dashboard')])>
                <span>▦</span>
                Dashboard
            </a>

            <a href="{{ route('patients') }}" @class(['nav-item', 'active' => request()->routeIs('patients')])>
                <span>♙</span>
                Patients
            </a>

            <a href="{{ route('appointments') }}" @class(['nav-item', 'active' => request()->routeIs('appointments')])>
                <span>□</span>
                Appointments
            </a>

            <a href="{{ route('medical-records') }}" @class(['nav-item', 'active' => request()->routeIs('medical-records')])>
                <span>▤</span>
                Medical Records
            </a>

            <a href="{{ route('laboratory') }}" @class(['nav-item', 'active' => request()->routeIs('laboratory')])>
                <span>♧</span>
                Laboratory
            </a>

        </nav>


        {{-- USER AREA --}}
        <div class="sidebar-user">

            <div class="user-icon">
                ♙
            </div>

            <div>
                <strong>Admin/User</strong>
            </div>

            <button type="button">
                ↪
            </button>

        </div>

    </aside>



    {{-- =====================================================
        MAIN DASHBOARD
    ====================================================== --}}

    <main class="main-content">


        {{-- HEADER --}}
        <header class="dashboard-header">

            <div>

                <h1>
                    Good evening, Admin
                </h1>

                <p>
                    Welcome back. Here's your clinical workflow overview.
                </p>

            </div>


            <button
                type="button"
                class="primary-button"
            >
                <span>♙</span>
                Register Patient
            </button>

        </header>



        {{-- =================================================
            STATISTICS CARDS
        ================================================== --}}

        <section class="stats-grid">


            {{-- TOTAL PATIENTS --}}
            <div class="stat-card">

                <span>
                    Total Patients
                </span>

                <strong>
                    1,248
                </strong>

                <small>
                    +12 this week
                </small>

            </div>


            {{-- APPOINTMENTS --}}
            <div class="stat-card">

                <span>
                    Today's Appointments
                </span>

                <strong>
                    24
                </strong>

                <small>
                    8 pending
                </small>

            </div>


            {{-- LAB REQUESTS --}}
            <div class="stat-card">

                <span>
                    Pending Lab Requests
                </span>

                <strong>
                    7
                </strong>

                <small>
                    3 awaiting results
                </small>

            </div>


            {{-- MEDICAL RECORDS --}}
            <div class="stat-card">

                <span>
                    Medical Records
                </span>

                <strong>
                    892
                </strong>

                <small>
                    24 updated today
                </small>

            </div>

        </section>



        {{-- =================================================
            APPOINTMENTS + LABORATORY
        ================================================== --}}

        <section class="dashboard-grid">


            {{-- =============================================
                TODAY'S APPOINTMENTS
            ============================================== --}}

            <div class="panel appointments-panel">

                <div class="panel-header">

                    <div>

                        <h2>
                            Today's Appointments
                        </h2>

                        <p>
                            8 pending · Monday 7 September
                        </p>

                    </div>

                    <a href="#">
                        View all →
                    </a>

                </div>


                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    TIME
                                </th>

                                <th>
                                    PATIENT
                                </th>

                                <th>
                                    ASSIGNED PROVIDER
                                </th>

                                <th>
                                    TYPE
                                </th>

                                <th>
                                    STATUS
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            {{-- APPOINTMENT 1 --}}
                            <tr>

                                <td>
                                    08:30 AM
                                </td>

                                <td>
                                    <strong>
                                        Maria Santos
                                    </strong>
                                </td>

                                <td>
                                    Dr. Adrian Reyes
                                </td>

                                <td>
                                    General Consultation
                                </td>

                                <td>

                                    <span class="status completed">
                                        Completed
                                    </span>

                                </td>

                            </tr>


                            {{-- APPOINTMENT 2 --}}
                            <tr>

                                <td>
                                    09:15 AM
                                </td>

                                <td>
                                    <strong>
                                        John Dela Cruz
                                    </strong>
                                </td>

                                <td>
                                    Dr. Camille Garcia
                                </td>

                                <td>
                                    Follow-up
                                </td>

                                <td>

                                    <span class="status progress">
                                        In Progress
                                    </span>

                                </td>

                            </tr>


                            {{-- APPOINTMENT 3 --}}
                            <tr>

                                <td>
                                    10:00 AM
                                </td>

                                <td>
                                    <strong>
                                        Angela Ramos
                                    </strong>
                                </td>

                                <td>
                                    Dr. Miguel Torres
                                </td>

                                <td>
                                    Laboratory Review
                                </td>

                                <td>

                                    <span class="status scheduled">
                                        Scheduled
                                    </span>

                                </td>

                            </tr>


                            {{-- APPOINTMENT 4 --}}
                            <tr>

                                <td>
                                    11:30 AM
                                </td>

                                <td>
                                    <strong>
                                        Carlos Mendoza
                                    </strong>
                                </td>

                                <td>
                                    Dr. Sofia Navarro
                                </td>

                                <td>
                                    General Consultation
                                </td>

                                <td>

                                    <span class="status scheduled">
                                        Scheduled
                                    </span>

                                </td>

                            </tr>


                            {{-- APPOINTMENT 5 --}}
                            <tr>

                                <td>
                                    01:30 PM
                                </td>

                                <td>
                                    <strong>
                                        Patricia Flores
                                    </strong>
                                </td>

                                <td>
                                    Dr. Daniel Cruz
                                </td>

                                <td>
                                    Follow-up
                                </td>

                                <td>

                                    <span class="status pending">
                                        Pending
                                    </span>

                                </td>

                            </tr>


                        </tbody>

                    </table>

                </div>

            </div>



            {{-- =============================================
                PENDING LABORATORY REQUESTS
            ============================================== --}}

            <div class="panel lab-panel">

                <div class="panel-header">

                    <div>

                        <h2>
                            Pending Laboratory Request
                        </h2>

                        <p>
                            7 awaiting processing
                        </p>

                    </div>

                    <a href="#">
                        View all →
                    </a>

                </div>


                <div class="lab-list">


                    {{-- LAB 1 --}}
                    <div class="lab-item">

                        <div>

                            <strong>
                                Complete Blood Count
                            </strong>

                            <small>
                                Maria Santos · Today, 08:15
                            </small>

                            <small>
                                LAB-0041
                            </small>

                        </div>

                        <span class="lab-tag routine">
                            Routine
                        </span>

                    </div>


                    {{-- LAB 2 --}}
                    <div class="lab-item">

                        <div>

                            <strong>
                                Lipid Panel
                            </strong>

                            <small>
                                John Dela Cruz · Today, 07:50
                            </small>

                            <small>
                                LAB-0040
                            </small>

                        </div>

                        <span class="lab-tag routine">
                            Routine
                        </span>

                    </div>


                    {{-- LAB 3 --}}
                    <div class="lab-item">

                        <div>

                            <strong>
                                Urinalysis
                            </strong>

                            <small>
                                Angela Ramos · Today, 07:30
                            </small>

                            <small>
                                LAB-0039
                            </small>

                        </div>

                        <span class="lab-tag urgent">
                            Urgent
                        </span>

                    </div>


                    {{-- LAB 4 --}}
                    <div class="lab-item">

                        <div>

                            <strong>
                                HbA1c
                            </strong>

                            <small>
                                Carlos Mendoza · Yesterday
                            </small>

                            <small>
                                LAB-0038
                            </small>

                        </div>

                        <span class="lab-tag routine">
                            Routine
                        </span>

                    </div>


                    {{-- LAB 5 --}}
                    <div class="lab-item">

                        <div>

                            <strong>
                                Thyroid Function
                            </strong>

                            <small>
                                Patricia Flores · Yesterday
                            </small>

                            <small>
                                LAB-0037
                            </small>

                        </div>

                        <span class="lab-tag routine">
                            Routine
                        </span>

                    </div>


                    {{-- LAB 6 --}}
                    <div class="lab-item">

                        <div>

                            <strong>
                                Blood Culture
                            </strong>

                            <small>
                                Luke Henderson · Yesterday
                            </small>

                            <small>
                                LAB-0036
                            </small>

                        </div>

                        <span class="lab-tag urgent">
                            Urgent
                        </span>

                    </div>


                    {{-- LAB 7 --}}
                    <div class="lab-item">

                        <div>

                            <strong>
                                Liver Function
                            </strong>

                            <small>
                                Ruth Ademy · 2 days ago
                            </small>

                            <small>
                                LAB-0035
                            </small>

                        </div>

                        <span class="lab-tag routine">
                            Routine
                        </span>

                    </div>


                </div>

            </div>

        </section>



        {{-- =================================================
            QUICK ACCESS + RECENT ACTIVITY
        ================================================== --}}

        <section class="lower-grid">


            {{-- =============================================
                QUICK ACCESS
            ============================================== --}}

            <div class="panel quick-access">

                <div class="panel-header">

                    <div>

                        <h2>
                            Quick Access
                        </h2>

                        <p>
                            Common clinical workflow actions
                        </p>

                    </div>

                </div>


                <div class="quick-grid">


                    {{-- REGISTER PATIENT --}}
                    <button
                        type="button"
                        class="quick-card register"
                    >

                        <strong>
                            Register Patient
                        </strong>

                        <span>
                            Add a new simulated patient
                        </span>

                    </button>


                    {{-- APPOINTMENT --}}
                    <button
                        type="button"
                        class="quick-card appointment"
                    >

                        <strong>
                            Schedule Appointment
                        </strong>

                        <span>
                            Book a patient appointment
                        </span>

                    </button>


                    {{-- MEDICAL RECORDS --}}
                    <button
                        type="button"
                        class="quick-card records"
                    >

                        <strong>
                            View Medical Records
                        </strong>

                        <span>
                            Browse patient records
                        </span>

                    </button>


                    {{-- LAB REQUEST --}}
                    <button
                        type="button"
                        class="quick-card laboratory"
                    >

                        <strong>
                            Create Lab Request
                        </strong>

                        <span>
                            Order a laboratory test
                        </span>

                    </button>


                </div>

            </div>



            {{-- =============================================
                RECENT ACTIVITY
            ============================================== --}}

            <div class="panel recent-activity">

                <div class="panel-header">

                    <div>

                        <h2>
                            Recent Activity
                        </h2>

                        <p>
                            Latest actions in your session
                        </p>

                    </div>

                </div>


                <div class="activity-list">


                    <div class="activity">

                        <strong>
                            Patient registered
                        </strong>

                        <span>
                            Luke Henderson added to the system
                        </span>

                        <small>
                            8 min ago
                        </small>

                    </div>


                    <div class="activity">

                        <strong>
                            Lab request created
                        </strong>

                        <span>
                            CBC ordered for Maria Santos
                        </span>

                        <small>
                            23 min ago
                        </small>

                    </div>


                    <div class="activity">

                        <strong>
                            Appointment scheduled
                        </strong>

                        <span>
                            Angela Ramos — Wound Dressing, 10:00
                        </span>

                        <small>
                            41 min ago
                        </small>

                    </div>


                    <div class="activity">

                        <strong>
                            Medical record updated
                        </strong>

                        <span>
                            Cardiac notes for Patricia Flores
                        </span>

                        <small>
                            1 hour ago
                        </small>

                    </div>


                    <div class="activity">

                        <strong>
                            Lab results received
                        </strong>

                        <span>
                            Full panel for John Dela Cruz — review pending
                        </span>

                        <small>
                            2 hours ago
                        </small>

                    </div>


                    <div class="activity">

                        <strong>
                            Appointment completed
                        </strong>

                        <span>
                            Follow-up — Carlos Mendoza discharged
                        </span>

                        <small>
                            3 hours ago
                        </small>

                    </div>


                </div>

            </div>

        </section>

    </main>



    {{-- =====================================================
        FLOATING SMART ASSIST
    ====================================================== --}}

    @if($showAssistant)

        <div class="smart-assist">


            {{-- ASSISTANT HEADER --}}
            <div class="assistant-header">

                <div>

                    <span class="assistant-icon">
                        ▣
                    </span>

                    <strong>
                        Smart Assist
                    </strong>

                </div>


                <button
                    type="button"
                    wire:click="toggleAssistant"
                    class="close-assistant"
                >
                    ×
                </button>

            </div>



            {{-- ASSISTANT CONTENT --}}
            <div class="assistant-body">


                {{-- WARNING --}}
                <div class="assistant-note">

                    Note: Smart Assist is a navigation guide only —
                    not a medical diagnostic tool.

                </div>



                {{-- QUICK TOPICS --}}
                <div class="quick-topics">

                    <p>
                        Quick topics
                    </p>


                    <button
                        type="button"
                        wire:click="quickQuestion('How do I use the system?')"
                    >
                        How do I use the system?
                    </button>


                    <button
                        type="button"
                        wire:click="quickQuestion('Help with patient registration')"
                    >
                        Help with patient registration
                    </button>


                    <button
                        type="button"
                        wire:click="quickQuestion('Help with appointments')"
                    >
                        Help with appointments
                    </button>


                    <button
                        type="button"
                        wire:click="quickQuestion('Help with laboratory requests')"
                    >
                        Help with laboratory requests
                    </button>


                    <button
                        type="button"
                        wire:click="quickQuestion('How to view medical records?')"
                    >
                        How to view medical records?
                    </button>

                </div>



                {{-- DEFAULT MESSAGE --}}
                <div class="assistant-message">

                    <p>
                        Hello! I'm Smart Assist — your HealthSyncAI guide.
                        Select a topic or type your question.
                    </p>

                </div>



                {{-- RESPONSE --}}
                @if($assistantResponse)

                    <div class="assistant-response">

                        {{ $assistantResponse }}

                    </div>

                @endif



                {{-- INPUT --}}
                <form
                    wire:submit="askQuestion"
                    class="assistant-input"
                >

                    <input
                        type="text"
                        wire:model="question"
                        placeholder="Type your question..."
                    >

                    <button type="submit">
                        ➤
                    </button>

                </form>


            </div>

        </div>


    @else


        {{-- FLOATING BUTTON WHEN CLOSED --}}
        <button
            type="button"
            wire:click="toggleAssistant"
            class="smart-assist-button"
        >
            ▣ Smart Assist
        </button>


    @endif

</div>