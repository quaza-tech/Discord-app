<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Test Session</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #313338;
            color: white;
        }

        .info-box {
            background-color: #2b2d31;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .success {
            color: #3ba55c;
        }

        .error {
            color: #ed4245;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #40444b;
        }

        th {
            background-color: #40444b;
        }

        .code {
            background-color: #1e1f22;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <h1>🔍 Test de Session PHP</h1>

    <div class="info-box">
        <h2>Session actuelle</h2>

        <?php if (isset($_SESSION['user']) && isset($_SESSION['username'])): ?>
            <p class="success">✅ Session active détectée</p>
            <table>
                <tr>
                    <th>Variable</th>
                    <th>Valeur</th>
                </tr>
                <tr>
                    <td><code>$_SESSION['user']</code></td>
                    <td><strong>
                            <?php echo htmlspecialchars($_SESSION['user']); ?>
                        </strong></td>
                </tr>
                <tr>
                    <td><code>$_SESSION['username']</code></td>
                    <td><strong>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </strong></td>
                </tr>
                <tr>
                    <td><code>session_id()</code></td>
                    <td>
                        <?php echo session_id(); ?>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <p class="error">❌ Aucune session active</p>
            <p>Vous devez vous connecter d'abord.</p>
        <?php endif; ?>
    </div>

    <div class="info-box">
        <h2>Toutes les variables de session</h2>
        <div class="code">
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
    </div>

    <div class="info-box">
        <h2>Informations PHP</h2>
        <table>
            <tr>
                <td>Session Save Path</td>
                <td>
                    <?php echo session_save_path(); ?>
                </td>
            </tr>
            <tr>
                <td>Session Name</td>
                <td>
                    <?php echo session_name(); ?>
                </td>
            </tr>
            <tr>
                <td>Session Cookie Params</td>
                <td>
                    <?php echo json_encode(session_get_cookie_params()); ?>
                </td>
            </tr>
        </table>
    </div>

    <?php
    // Connexion à la base de données pour vérifier
    $serveur = "localhost";
    $port = "5432";
    $utilisateur = "noa";
    $motDePasse = "leonoa09";
    $nomBase = "discord";

    try {
        $connexion = new PDO(
            "pgsql:host=$serveur;port=$port;dbname=$nomBase",
            $utilisateur,
            $motDePasse
        );
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_SESSION['user'])) {
            $requete = $connexion->prepare("SELECT id, nom, email FROM users WHERE id = ?");
            $requete->execute([$_SESSION['user']]);
            $user = $requete->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo '<div class="info-box">';
                echo '<h2>Utilisateur en base de données</h2>';
                echo '<p class="success">✅ Correspondance trouvée</p>';
                echo '<table>';
                echo '<tr><th>Champ</th><th>Valeur</th></tr>';
                echo '<tr><td>ID</td><td>' . htmlspecialchars($user['id']) . '</td></tr>';
                echo '<tr><td>Nom</td><td>' . htmlspecialchars($user['nom']) . '</td></tr>';
                echo '<tr><td>Email</td><td>' . htmlspecialchars($user['email']) . '</td></tr>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div class="info-box">';
                echo '<p class="error">❌ L\'ID de session ne correspond à aucun utilisateur en base</p>';
                echo '</div>';
            }
        }

        // Afficher l'état des tokens
        echo '<div class="info-box">';
        echo '<h2>État des tokens de réinitialisation</h2>';
        $tokensQuery = $connexion->query(
            "SELECT id, nom, 
                    reset_token IS NOT NULL as a_token,
                    reset_expiration,
                    CASE 
                        WHEN reset_token IS NULL THEN 'Pas de token'
                        WHEN reset_expiration > CURRENT_TIMESTAMP THEN 'Token VALIDE'
                        ELSE 'Token EXPIRE'
                    END as statut
             FROM users 
             ORDER BY id"
        );

        echo '<table>';
        echo '<tr><th>ID</th><th>Nom</th><th>A un token</th><th>Expiration</th><th>Statut</th></tr>';
        while ($row = $tokensQuery->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nom']) . '</td>';
            echo '<td>' . ($row['a_token'] ? '✅ Oui' : '❌ Non') . '</td>';
            echo '<td>' . ($row['reset_expiration'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['statut']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

    } catch (PDOException $e) {
        echo '<div class="info-box">';
        echo '<p class="error">❌ Erreur de connexion à la base de données</p>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>

    <div class="info-box">
        <h2>Actions</h2>
        <p><a href="log_out.php" style="color: #5865f2;">Se déconnecter</a></p>
        <p><a href="../salon.html" style="color: #5865f2;">Aller au salon</a></p>
        <p><a href="../index.html" style="color: #5865f2;">Page de connexion</a></p>
    </div>
</body>

</html>