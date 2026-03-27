# Planning and Task Division

## Assumptions
- Duration: 8 weeks
- Contact hours: 6 hours per week (48 hours total)
- One integrated application delivered at the end

## Team Roles
- Team A (Backend/API): data models, seeders, scoring rules, API endpoints
- Team B (Frontend/UX): game screens, docent dashboard, layout polish
- Team C (Integration/QA): end-to-end flows, test coverage, bug fixing, release prep

## Weekly Plan

### Week 1
- Align scope and rules, decide data sources
- Seeders for Verdachte/Misdaad/Opdrachten
- Basic frontend wiring for /spel and /docent
- QA: baseline smoke tests

### Week 2
- Implement hints (normal + Big Boss)
- API endpoints for spel status, hints, leaderboard
- Frontend: hint UI and leaderboard UI
- QA: API tests + hints flow

### Week 3
- Big Boss opdrachten and scoring rules
- Docent controls for sending hints and start/stop
- Frontend: polish for game layout
- QA: big boss rules verification

### Week 4 (Midpoint Check)
- Integration checkpoint with full flow demo
- Fix mismatches in rules or data
- Improve error messaging and validation
- QA: regression test round

### Week 5
- Performance: query evaluation safety and feedback
- UI refinements and responsive fixes
- API parity check for all screens
- QA: run full test suite

### Week 6
- Add missing lesson content, finalize hints
- Seed additional data if needed
- QA: end-to-end flows

### Week 7
- Stabilize, bugfix, and reduce tech debt
- Verify scoring and leaderboard accuracy
- QA: pre-release testing

### Week 8
- Final polish, deployment checks
- Documentation and handoff
- Final demo and evaluation

## Integration Milestones
- M1 (Week 2): Core API + hints pipeline
- M2 (Week 4): Full playable flow
- M3 (Week 6): Content complete
- M4 (Week 8): Release candidate
