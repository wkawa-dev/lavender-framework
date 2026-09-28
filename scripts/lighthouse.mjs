// Lighthouse gate: runs current Lighthouse (13+) against a running server,
// mobile preset, three runs per page. Accessibility, best practices, SEO, and
// agentic browsing must be 100 on every run; performance is the median run
// and must reach 95 (shared CI runners are noisy; local runs read 97-100).
// Why not @lhci/cli: it pins Lighthouse 12, which predates the Content-Signal
// robots.txt directive and the agentic browsing category.
//
//   node scripts/lighthouse.mjs [baseUrl] [path ...]
import { execFileSync } from 'node:child_process';
import { mkdirSync, readFileSync } from 'node:fs';

const [base = 'http://localhost:8080', ...args] = process.argv.slice(2);
const paths = args.length ? args : ['/', '/docs/api', '/contact', '/privacy'];
const runs = Number(process.env.LIGHTHOUSE_RUNS ?? 3);
const out = '.lighthouse';
const strict = ['accessibility', 'best-practices', 'seo', 'agentic-browsing'];

mkdirSync(out, { recursive: true });
let failed = false;

for (const path of paths) {
    const reports = [];

    for (let i = 1; i <= runs; i++) {
        const name = `${out}/${path.replace(/\W+/g, '-').replace(/^-|-$/g, '') || 'home'}-${i}`;
        execFileSync('npx', ['--yes', 'lighthouse@13', base + path, '--quiet', '--output=json', '--output=html', `--output-path=${name}`, '--chrome-flags=--headless=new --no-sandbox'], { stdio: 'inherit' });
        reports.push(JSON.parse(readFileSync(`${name}.report.json`, 'utf8')));
    }

    const score = (report, id) => Math.round((report.categories[id]?.score ?? 0) * 100);
    const perf = reports.map((report) => score(report, 'performance')).sort((a, b) => a - b)[Math.floor(runs / 2)];
    const worst = Object.fromEntries(strict.map((id) => [id, Math.min(...reports.map((report) => score(report, id)))]));
    const ok = perf >= 95 && Object.values(worst).every((value) => value === 100);

    failed ||= !ok;
    console.log(`${ok ? 'pass' : 'FAIL'}  ${path.padEnd(12)} performance ${perf}  ${strict.map((id) => `${id} ${worst[id]}`).join('  ')}`);
}

console.log(`Reports: ${out}/*.report.html`);
process.exit(failed ? 1 : 0);
