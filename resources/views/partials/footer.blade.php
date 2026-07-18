<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="site-footer-brand">
            <span class="site-footer-icon">📢</span>
            <div>
                <div class="site-footer-title">KUET CSE Notice Board</div>
                <div class="site-footer-sub">Department of Computer Science &amp; Engineering</div>
            </div>
        </div>

        <div class="site-footer-tagline">
            Innovation in Every Notice &bull; Developed by Ahanaf Tahmid Rifat
        </div>

        <div class="site-footer-links">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('public.notices') }}">Notices</a>
            <a href="{{ route('login') }}">Login</a>
            <a href="https://github.com/rifat9754" target="_blank" rel="noopener">GitHub</a>
        </div>

        <div class="site-footer-bottom">
            &copy; {{ date('Y') }} Khulna University of Engineering &amp; Technology. All rights reserved.
        </div>
    </div>
</footer>

<style>
.site-footer {
    background: #0F172A;
    color: #94A3B8;
    padding: 32px 20px 20px;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    font-size: 14px;
    margin-top: 40px;
}
.site-footer-inner { max-width: 1000px; margin: 0 auto; }
.site-footer-brand {
    display: flex; align-items: center; gap: 12px;
    justify-content: center; margin-bottom: 12px;
}
.site-footer-icon { font-size: 28px; }
.site-footer-title { color: #fff; font-size: 17px; font-weight: 700; }
.site-footer-sub { font-size: 13px; color: #94A3B8; margin-top: 2px; }
.site-footer-tagline {
    text-align: center; font-size: 13.5px; color: #60A5FA;
    margin-bottom: 18px; letter-spacing: 0.2px;
}
.site-footer-links {
    display: flex; gap: 22px; justify-content: center;
    flex-wrap: wrap; margin-bottom: 18px;
}
.site-footer-links a {
    color: #CBD5E1; text-decoration: none; font-size: 14px;
    transition: color .2s;
}
.site-footer-links a:hover { color: #60A5FA; }
.site-footer-bottom {
    text-align: center; font-size: 13px; color: #64748B;
    border-top: 1px solid rgba(148,163,184,0.15);
    padding-top: 14px;
}
@media (max-width: 480px) {
    .site-footer-brand { flex-direction: column; text-align: center; gap: 8px; }
    .site-footer-links { gap: 14px; }
    .site-footer-tagline { font-size: 12.5px; }
}
</style>