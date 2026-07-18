<footer class="site-footer">
    <div class="site-footer-inner">

        <div class="sf-center">
            <span class="sf-tagline">Innovation in Every Notice</span>
        </div>

        <div class="sf-right">
            <span class="sf-dev">Developed by <b>Ahanaf Tahmid Rifat</b></span>
            <a href="https://github.com/rifat9754" target="_blank" rel="noopener" class="sf-gh" title="GitHub">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8Z"/>
                </svg>
                GitHub
            </a>
        </div>
    </div>
    
</footer>

<style>
.site-footer {
    background: linear-gradient(90deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);
    border-top: 2px solid transparent;
    border-image: linear-gradient(90deg, #2563EB, #22C55E, #2563EB) 1;
    font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
    padding: 14px 24px 10px;
}
.site-footer-inner {
    max-width: 1200px; margin: 0 auto;
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
}
.sf-left { display: flex; align-items: center; gap: 9px; }
.sf-icon { font-size: 17px; }
.sf-brand {
    color: #F1F5F9; font-size: 14.5px; font-weight: 700; letter-spacing: 0.2px;
}
.sf-center { flex: 1; text-align: center; min-width: 200px; }
.sf-tagline {
    color: #60A5FA; font-size: 13px; font-style: italic; letter-spacing: 0.3px;
}
.sf-right { display: flex; align-items: center; gap: 14px; }
.sf-dev { color: #94A3B8; font-size: 12.5px; }
.sf-dev b { color: #CBD5E1; font-weight: 600; }
.sf-gh {
    display: inline-flex; align-items: center; gap: 6px;
    color: #CBD5E1; text-decoration: none; font-size: 12.5px; font-weight: 600;
    padding: 5px 12px; border-radius: 999px;
    background: rgba(148,163,184,0.10);
    border: 1px solid rgba(148,163,184,0.18);
    transition: all .2s ease;
}
.sf-gh:hover {
    color: #fff; background: rgba(37,99,235,0.25);
    border-color: rgba(96,165,250,0.5); text-decoration: none;
}
.sf-copy {
    max-width: 1200px; margin: 10px auto 0; padding-top: 9px;
    text-align: center; font-size: 11.5px; color: #64748B;
    border-top: 1px solid rgba(148,163,184,0.10);
}
@media (max-width: 820px) {
    .site-footer-inner { justify-content: center; gap: 10px; }
    .sf-center { order: 3; flex-basis: 100%; }
}
@media (max-width: 480px) {
    .site-footer { padding: 12px 16px 8px; }
    .sf-brand { font-size: 13.5px; }
    .sf-right { flex-direction: column; gap: 8px; }
}
</style>