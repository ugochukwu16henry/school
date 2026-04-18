<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RiseFlow | School management built for African schools</title>
    <meta name="description" content="RiseFlow helps African schools run operations, fees, and results in one secure multi-tenant platform.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Sora:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7f8f6;
            --surface: #ffffff;
            --text: #11231a;
            --muted: #4a5c53;
            --line: #d8dfda;
            --brand: #08735f;
            --brand-strong: #075346;
            --accent: #e6fff8;
            --chip: #eef4f1;
            --shadow: 0 12px 30px rgba(12, 34, 24, 0.08);
        }

        body.night {
            --bg: #0f1513;
            --surface: #161f1b;
            --text: #e9f3ee;
            --muted: #9cb3a8;
            --line: #263530;
            --brand: #46d9ba;
            --brand-strong: #31b59a;
            --accent: #142f2a;
            --chip: #1a2723;
            --shadow: 0 16px 34px rgba(0, 0, 0, 0.35);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: Manrope, sans-serif;
            background: radial-gradient(circle at 10% 0%, #e8f8f1 0%, var(--bg) 35%), var(--bg);
            color: var(--text);
            line-height: 1.55;
        }

        body.night {
            background: radial-gradient(circle at 20% 0%, #183128 0%, var(--bg) 40%), var(--bg);
        }

        .container {
            width: min(1120px, 92vw);
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(8px);
            background: color-mix(in srgb, var(--bg) 85%, transparent);
            border-bottom: 1px solid var(--line);
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 0;
        }

        .brand {
            font-family: Sora, sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 0.02em;
        }

        .nav-links {
            display: flex;
            gap: 18px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            font-size: 0.93rem;
        }

        .nav-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            border: 1px solid var(--line);
            background: var(--surface);
            color: var(--text);
            border-radius: 10px;
            padding: 10px 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn.primary {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .btn.icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            padding: 0;
            font-size: 1.05rem;
        }

        .hero {
            padding: 52px 0 30px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 24px;
            align-items: stretch;
        }

        .eyebrow {
            display: inline-block;
            background: var(--chip);
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: 0.82rem;
            padding: 8px 12px;
            margin-bottom: 14px;
            color: var(--muted);
            font-weight: 700;
        }

        h1, h2, h3 { font-family: Sora, sans-serif; margin: 0 0 14px; line-height: 1.2; }
        h1 { font-size: clamp(1.9rem, 4vw, 3.1rem); }
        h2 { font-size: clamp(1.45rem, 3vw, 2.2rem); }
        h3 { font-size: 1.12rem; }

        .hero p.lead {
            margin: 0;
            color: var(--muted);
            max-width: 66ch;
            font-size: 1rem;
        }

        .hero-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hero-bullets {
            margin-top: 18px;
            padding: 0;
            list-style: none;
            color: var(--muted);
            display: grid;
            gap: 8px;
            font-size: 0.93rem;
        }

        .hero-bullets li::before {
            content: "•";
            margin-right: 8px;
            color: var(--brand);
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .hero-focus {
            display: grid;
            gap: 8px;
            margin-bottom: 16px;
        }

        .chip {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 0.83rem;
            background: var(--chip);
            color: var(--muted);
            font-weight: 700;
            transition: all .2s ease;
        }

        .chip.active {
            background: var(--accent);
            color: var(--text);
            border-color: var(--brand);
        }

        .logos {
            margin-top: 26px;
        }

        .logos .items {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .logo-pill {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            font-size: 0.82rem;
            background: var(--surface);
            color: var(--muted);
            font-weight: 700;
        }

        .quotes {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .quote {
            border-left: 3px solid var(--brand);
            background: color-mix(in srgb, var(--surface) 92%, var(--accent));
            padding: 12px 12px 12px 14px;
            border-radius: 10px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .section { padding: 34px 0; }

        .card-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .stat {
            font-size: 1.4rem;
            font-weight: 800;
            font-family: Sora, sans-serif;
            margin-bottom: 6px;
        }

        .muted { color: var(--muted); }

        .pricing-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 16px;
        }

        .range-wrap { margin-top: 12px; }

        input[type="range"] {
            width: 100%;
            accent-color: var(--brand);
        }

        .range-labels {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 0.76rem;
            color: var(--muted);
        }

        .price-big {
            font-family: Sora, sans-serif;
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            margin: 8px 0 2px;
            font-weight: 800;
        }

        .subtle {
            font-size: 0.84rem;
            color: var(--muted);
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            min-width: 600px;
        }

        th, td {
            border-bottom: 1px solid var(--line);
            padding: 10px;
            text-align: left;
            font-size: 0.9rem;
        }

        th { color: var(--muted); font-weight: 700; }

        .steps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .step-no {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin-bottom: 8px;
        }

        details {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px;
            background: var(--surface);
        }

        details + details { margin-top: 8px; }

        summary {
            font-weight: 700;
            cursor: pointer;
            font-size: 0.95rem;
        }

        footer {
            border-top: 1px solid var(--line);
            padding: 26px 0 70px;
            margin-top: 26px;
        }

        .wa-button {
            position: fixed;
            right: 16px;
            bottom: 14px;
            z-index: 30;
            background: #25d366;
            color: #fff;
            border-radius: 999px;
            padding: 12px 15px;
            text-decoration: none;
            font-weight: 800;
            box-shadow: 0 12px 22px rgba(0, 0, 0, 0.2);
            font-size: 0.88rem;
        }

        .preview-line {
            font-size: 0.86rem;
            color: var(--muted);
            padding: 8px 0;
            border-bottom: 1px dashed var(--line);
        }

        @media (max-width: 980px) {
            .hero-grid,
            .pricing-grid,
            .steps,
            .card-grid {
                grid-template-columns: 1fr;
            }

            .topbar-inner { align-items: flex-start; }
            .logos .items { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .nav-links { display: none; }
            .btn { font-size: 0.84rem; }
            .container { width: min(1120px, 94vw); }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div class="brand">RiseFlow</div>
            <nav class="nav-links" aria-label="Main Navigation">
                <a href="#how-it-works">How it works</a>
                <a href="#pricing">Pricing</a>
                <a href="#compare">Compare</a>
                <a href="#affiliate">Affiliate program</a>
            </nav>
            <div class="nav-actions">
                <button id="theme-toggle" class="btn" type="button" aria-label="Toggle night view">🌙 Night view</button>
                <a class="btn" href="{{ route('login') }}">Log in</a>
                <a class="btn primary" href="{{ route('school.signup.create') }}">Register your school</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-grid">
                <div>
                    <span class="eyebrow">Built for African schools • First 50 students free</span>
                    <h1>Run your entire school operations in one secure platform.</h1>
                    <p class="lead">
                        Reduce admin workload, improve fee visibility, and keep parents and teachers aligned with real-time school data.
                        Built for Nigerian and African schools.
                    </p>
                    <div class="hero-actions">
                        <a class="btn primary" href="{{ route('school.signup.create') }}">Register your school in 5 minutes</a>
                        <a class="btn" href="{{ route('login') }}">Already using RiseFlow? Log in</a>
                    </div>
                    <ul class="hero-bullets">
                        <li>First 50 students free after you register your school</li>
                        <li>Paystack-powered billing in Naira</li>
                        <li>Role-based access and daily backups</li>
                    </ul>
                    <div class="logos">
                        <div class="muted" style="font-weight: 700;">Trusted by growing schools across West and East Africa</div>
                        <div class="items">
                            <div class="logo-pill">LagosLearningGroup</div>
                            <div class="logo-pill">AbujaSTEMAcademy</div>
                            <div class="logo-pill">NairobiFutureSchools</div>
                            <div class="logo-pill">AccraBridgeCollege</div>
                        </div>
                    </div>
                    <div class="quotes">
                        <div class="quote">“Result publishing time dropped from days to hours.”<br><strong>School owner, Lagos</strong></div>
                        <div class="quote">“Parents now get fee and report visibility in one place.”<br><strong>Administrator, Abuja</strong></div>
                    </div>
                </div>

                <aside class="panel">
                    <h3 style="margin-bottom: 6px;">RiseFlow dashboard preview</h3>
                    <div class="muted" style="font-size: 0.88rem; margin-bottom: 10px;">Play 30s product preview</div>
                    <div class="hero-focus" id="hero-focus-list">
                        <div class="chip active">Real-time results</div>
                        <div class="chip">“Chinedu • A in Math”</div>
                        <div class="chip">Parent view</div>
                        <div class="chip">WhatsApp-ready updates</div>
                        <div class="chip">Control room</div>
                        <div class="chip">One screen, all schools</div>
                        <div class="chip">Multi-tenant platform</div>
                    </div>
                    <div class="subtle">Rotating hero focus every 3 minutes.</div>
                </aside>
            </div>
        </section>

        <section class="section" id="gateway">
            <div class="container card-grid">
                <article class="panel">
                    <h3>Register Gateway</h3>
                    <p class="muted">New school owners can create a secure school workspace in minutes.</p>
                    <a class="btn primary" href="{{ route('school.signup.create') }}">Register your school</a>
                </article>
                <article class="panel">
                    <h3>Sign-in Gateway</h3>
                    <p class="muted">Existing admins, teachers, parents and students can sign in from one secure entry point.</p>
                    <a class="btn" href="{{ route('login') }}">Go to sign in</a>
                </article>
                <article class="panel">
                    <h3>Need onboarding support?</h3>
                    <p class="muted">If you need help setting up your first school account, chat with our team and we will guide you end-to-end.</p>
                    <a class="btn" href="https://wa.me/2340000000000" target="_blank" rel="noopener noreferrer">Talk to onboarding</a>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2>One platform, three superpowers</h2>
                <p class="muted">Owners, teachers, and parents each get a tailored experience inside one multi-tenant platform.</p>
                <div class="card-grid" style="margin-top: 14px;">
                    <article class="panel">
                        <h3>For owners</h3>
                        <p class="muted">Real-time metrics across students, teachers, and fees. Lock result printing until debts are cleared, and stay on top of your billing.</p>
                    </article>
                    <article class="panel">
                        <h3>For teachers</h3>
                        <p class="muted">Fast grid entry, automatic totals, and one-click transcript generation with access limited only to students in their classes.</p>
                    </article>
                    <article class="panel">
                        <h3>For parents</h3>
                        <p class="muted">Family view for multiple children, secure access codes, and instant PDF report cards on any smartphone.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="pricing">
            <div class="container pricing-grid">
                <div class="panel">
                    <h2>Transparent pricing for growing schools</h2>
                    <p class="muted">First 50 students are lifetime free. From student 51, you pay a one-time activation and a small monthly fee.</p>

                    <div class="range-wrap">
                        <label for="student-count" style="font-weight: 700;">Number of students</label>
                        <div style="font-family: Sora, sans-serif; font-size: 1.4rem; margin-top: 4px;"><span id="student-count-value">50</span></div>
                        <input id="student-count" type="range" min="0" max="1000" step="1" value="50">
                        <div class="range-labels">
                            <span>0</span>
                            <span>50 (Free Limit)</span>
                            <span>500</span>
                            <span>1000+</span>
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <h3>Monthly subscription</h3>
                    <div class="price-big" id="monthly-price">₦0 /month</div>
                    <div class="subtle" id="monthly-note">(0 billable students at ₦100 each)</div>

                    <h3 style="margin-top: 18px;">One-time activation</h3>
                    <div class="price-big" id="activation-price">₦0</div>
                    <div class="subtle">₦500 per new student added after first 50</div>

                    <div class="panel" style="margin-top: 16px; background: var(--accent);">
                        <strong id="tier-badge">100% Free Tier Active</strong>
                        <div class="subtle" id="tier-note">Get started with 50 students</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container card-grid">
                <article class="panel">
                    <h3>Fast grid entry preview</h3>
                    <div class="preview-line"><strong>Student</strong> &nbsp; Math &nbsp; English &nbsp; Science</div>
                    <div class="preview-line">Amina Okoro &nbsp; 78 &nbsp; 81 &nbsp; 84</div>
                    <div class="preview-line">Tunde Bello &nbsp; 71 &nbsp; 68 &nbsp; 74</div>
                </article>
                <article class="panel">
                    <h3>Built for African realities</h3>
                    <p class="muted">RiseFlow understands how schools really work across Nigeria, Ghana, Kenya and beyond. We support dense ranking, competency-based assessments, and flexible fee models that match your community.</p>
                </article>
                <article class="panel">
                    <h3>Safe, hosted and future-proof</h3>
                    <p class="muted">Cloud-hosted on modern infrastructure with daily backups. Your records are safe for future generations and accessible from anywhere.</p>
                </article>
            </div>

            <div class="container card-grid" style="margin-top: 14px;">
                <article class="panel">
                    <h3>Secondary: Score and rank engine</h3>
                    <p class="muted">30% CA + 70% exam, automatic dense ranking, and subject-by-subject performance snapshots. Export-ready report cards with your school logo and RiseFlow seal.</p>
                </article>
                <article class="panel">
                    <h3>Primary: Class assessments</h3>
                    <p class="muted">Track handwriting, punctuality, reading fluency, social habits and more with custom assessment categories that fit your curriculum.</p>
                </article>
                <article class="panel">
                    <h3>Parents stay in the loop</h3>
                    <p class="muted">One parent account, many children. View pictures, teachers, results and fees in one clean dashboard, on any smartphone.</p>
                </article>
            </div>
        </section>

        <section class="section" id="how-it-works">
            <div class="container">
                <h2>How schools get started</h2>
                <p class="muted">Three simple steps to bring your school onto RiseFlow.</p>
                <div class="steps" style="margin-top: 14px;">
                    <article class="panel">
                        <span class="step-no">1</span>
                        <h3>Register your school</h3>
                        <p class="muted">Click “Register your school” and fill in a short form. You get a secure link for your teachers and parents.</p>
                    </article>
                    <article class="panel">
                        <span class="step-no">2</span>
                        <h3>Import or add students</h3>
                        <p class="muted">Upload an Excel file or add students one by one. RiseFlow checks for duplicates and keeps your data clean.</p>
                    </article>
                    <article class="panel">
                        <span class="step-no">3</span>
                        <h3>Share links with staff and parents</h3>
                        <p class="muted">Share your school link so teachers and parents can sign up with their own profiles and photos.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="compare">
            <div class="container panel table-wrap">
                <h2>RiseFlow vs. Paper</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Paper</th>
                            <th>RiseFlow</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Records and storage</td>
                            <td>Easy to lose, hard to search, no parent view.</td>
                            <td>Cloud-safe, instant search, family dashboard.</td>
                        </tr>
                        <tr>
                            <td>Results</td>
                            <td>Manual calculation, no QR verification.</td>
                            <td>Automatic grading, QR-verified transcripts.</td>
                        </tr>
                        <tr>
                            <td>Communication</td>
                            <td>Phone calls and paper notes.</td>
                            <td>WhatsApp-ready updates and parent portal.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="section" id="affiliate">
            <div class="container card-grid">
                <article class="panel">
                    <h3>Resources for school teams</h3>
                    <p><a href="#" class="muted">Teacher quick start (PDF)</a></p>
                    <p><a href="#" class="muted">Grading reference (PDF)</a></p>
                    <p><a href="#" class="muted">Affiliate program</a></p>
                </article>
                <article class="panel" style="grid-column: span 2;">
                    <h3>Get our guide on digitalizing your school</h3>
                    <p class="muted">Practical steps Nigerian school owners are using to move from paper to a secure, parent-friendly platform.</p>
                    <form method="POST" action="#" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <input type="email" name="email" placeholder="Your work email" required style="flex: 1 1 260px; border:1px solid var(--line); background:var(--surface); color:var(--text); border-radius:10px; padding:11px 12px;">
                        <button class="btn primary" type="submit">Email me the guide</button>
                    </form>
                    <p class="subtle">By submitting, you agree to receive product emails. You can unsubscribe anytime.</p>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2>Frequently asked questions</h2>
                <details>
                    <summary>How fast can a school go live on RiseFlow?</summary>
                    <p class="muted">Most schools complete registration, add students, and start sharing parent access within the same day.</p>
                </details>
                <details>
                    <summary>Does RiseFlow support African school fee and grading workflows?</summary>
                    <p class="muted">Yes. RiseFlow is built with flexible grading logic, role-based access, and fee visibility matching Nigerian and African school operations.</p>
                </details>
                <details>
                    <summary>Can parents use RiseFlow on mobile phones?</summary>
                    <p class="muted">Yes. Parent views are mobile-friendly and work on everyday smartphones with low-friction access.</p>
                </details>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <h3 style="margin-bottom: 8px;">RiseFlow — School management built for African schools.</h3>
            <p class="muted">Day and night view for busy school owners and parents.</p>
            <p class="subtle">Terms of Service · Privacy and Data Processing</p>
        </div>
    </footer>

    <a class="wa-button" href="https://wa.me/2340000000000" target="_blank" rel="noopener noreferrer">WA Chat with an expert</a>

    <script>
        (function () {
            const body = document.body;
            const toggle = document.getElementById('theme-toggle');
            const storageKey = 'riseflow-theme';
            const savedTheme = localStorage.getItem(storageKey);
            if (savedTheme === 'night') {
                body.classList.add('night');
                toggle.textContent = '☀️ Day view';
            }

            toggle.addEventListener('click', function () {
                body.classList.toggle('night');
                const isNight = body.classList.contains('night');
                toggle.textContent = isNight ? '☀️ Day view' : '🌙 Night view';
                localStorage.setItem(storageKey, isNight ? 'night' : 'day');
            });

            const freeLimit = 50;
            const monthlyPerStudent = 100;
            const activationPerNewStudent = 500;
            const slider = document.getElementById('student-count');
            const countEl = document.getElementById('student-count-value');
            const monthlyEl = document.getElementById('monthly-price');
            const monthlyNoteEl = document.getElementById('monthly-note');
            const activationEl = document.getElementById('activation-price');
            const tierBadgeEl = document.getElementById('tier-badge');
            const tierNoteEl = document.getElementById('tier-note');

            function formatNaira(value) {
                return '₦' + Number(value).toLocaleString();
            }

            function updatePricing() {
                const students = Number(slider.value);
                const billable = Math.max(0, students - freeLimit);
                const monthly = billable * monthlyPerStudent;
                const activation = billable * activationPerNewStudent;

                countEl.textContent = String(students);
                monthlyEl.textContent = formatNaira(monthly) + ' /month';
                monthlyNoteEl.textContent = '(' + billable + ' billable students at ₦100 each)';
                activationEl.textContent = formatNaira(activation);

                if (students <= freeLimit) {
                    tierBadgeEl.textContent = '100% Free Tier Active';
                    tierNoteEl.textContent = 'Get started with 50 students';
                } else {
                    tierBadgeEl.textContent = 'Billing Active Beyond Free Tier';
                    tierNoteEl.textContent = 'Student ' + (freeLimit + 1) + ' and above are billed';
                }
            }

            slider.addEventListener('input', updatePricing);
            updatePricing();

            const chips = Array.from(document.querySelectorAll('#hero-focus-list .chip'));
            let activeIndex = 0;
            setInterval(function () {
                chips[activeIndex].classList.remove('active');
                activeIndex = (activeIndex + 1) % chips.length;
                chips[activeIndex].classList.add('active');
            }, 180000);
        })();
    </script>
</body>
</html>
