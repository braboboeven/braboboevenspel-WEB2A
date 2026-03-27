<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Brabo Boevenspel - Frontend Dev</title>
	<style>
		:root {
			--bg: #121212;
			--panel: #1f1f1f;
			--text: #f3f3f3;
			--muted: #b8b8b8;
			--accent: #f4b400;
		}

		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			font-family: "Montserrat", "Segoe UI", sans-serif;
			background: radial-gradient(circle at top right, #242424, var(--bg));
			color: var(--text);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 24px;
		}

		.container {
			width: min(900px, 100%);
			background: var(--panel);
			border: 1px solid #2c2c2c;
			border-radius: 16px;
			padding: 24px;
			box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
		}

		h1 {
			font-size: clamp(1.5rem, 3vw, 2.2rem);
			margin-bottom: 8px;
		}

		p {
			color: var(--muted);
			margin-bottom: 20px;
		}

		ul {
			display: grid;
			gap: 12px;
			list-style: none;
		}

		a {
			display: block;
			background: #282828;
			border: 1px solid #343434;
			border-radius: 10px;
			padding: 12px 14px;
			color: var(--text);
			text-decoration: none;
			transition: transform 0.15s ease, border-color 0.15s ease;
		}

		a:hover {
			transform: translateY(-1px);
			border-color: var(--accent);
		}

		.tag {
			display: inline-block;
			margin-left: 8px;
			padding: 2px 8px;
			border-radius: 99px;
			font-size: 0.75rem;
			background: #383838;
			color: var(--muted);
		}
	</style>
</head>
<body>
	<main class="container">
		<h1>Brabo Boevenspel Frontend</h1>
		<p>Samengevoegde development branch met de beschikbare frontend-schermen.</p>

		<ul>
			<li><a href="./lerarenDashboard.html">Leraren Dashboard <span class="tag">Front-end-dev</span></a></li>
			<li><a href="./rules.html">Spelregels <span class="tag">Front-end-dev</span></a></li>
			<li><a href="./leerling/leerlingenWachtPagina.html">Leerlingen Wachtscherm <span class="tag">f4863bc</span></a></li>
			<li><a href="./Hintscherm/Brabo/leerlingenHint.html">Hintscherm <span class="tag">Hintscherm</span></a></li>
			<li><a href="./Leaderboard/Applicatie/leaderboard_Student/leaderboard_Student.html">Leaderboard Student <span class="tag">Leaderboard</span></a></li>
		</ul>
	</main>
</body>
</html>
