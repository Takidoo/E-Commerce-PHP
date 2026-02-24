<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

requireAdmin($_SESSION['user_id'], $pdo);
syncSessionRole($_SESSION['user_id'], $pdo);

$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$article_id) {
    header('Location: articles.php');
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, s.quantity as stock FROM articles a 
                      LEFT JOIN stock s ON a.id = s.article_id 
                      WHERE a.id = :id");
$stmt->execute(['id' => $article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: articles.php');
    exit;
}

$message = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image_link = $article['image_link'];

    if (empty($name) || $price <= 0) {
        $message = "Veuillez remplir tous les champs correctement.";
        $msgType = "error";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowed_types)) {
                $message = "Format d'image invalide. Accepté : JPG, PNG, GIF, WebP";
                $msgType = "error";
            } elseif ($file['size'] > $max_size) {
                $message = "L'image dépasse 5MB.";
                $msgType = "error";
            } else {
                $upload_dir = '../assets/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                if ($article['image_link'] && file_exists($article['image_link'])) {
                    unlink($article['image_link']);
                }

                $filename = uniqid() . '_' . basename($file['name']);
                $filepath = $upload_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $image_link = $filepath;
                } else {
                    $message = "Erreur lors du téléchargement de l'image.";
                    $msgType = "error";
                }
            }
        }

        if ($msgType !== "error") {
            try {
                $pdo->beginTransaction();

                $updateStmt = $pdo->prepare("UPDATE articles SET name = :name, description = :desc, price = :price, image_link = :img WHERE id = :id");
                $updateStmt->execute([
                    'name' => $name,
                    'desc' => $description,
                    'price' => $price,
                    'img' => $image_link,
                    'id' => $article_id
                ]);

                $updateStock = $pdo->prepare("UPDATE stock SET quantity = :qty WHERE article_id = :aid");
                $updateStock->execute(['qty' => $stock, 'aid' => $article_id]);

                $pdo->commit();
                $message = "Article mis à jour avec succès !";
                $msgType = "success";

                $stmt->execute(['id' => $article_id]);
                $article = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "Erreur lors de la mise à jour : " . $e->getMessage();
                $msgType = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer Article - Admin - EShop</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .image-preview {
            margin-top: 1rem;
            max-width: 300px;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 2px solid var(--border);
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
        }

        .file-input-label {
            display: block;
            padding: 2rem;
            background-color: var(--bg-hover);
            border: 2px dashed var(--accent);
            border-radius: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background-color: rgba(168, 85, 247, 0.1);
        }

        .file-input-label.has-file {
            border-color: var(--success);
            background-color: rgba(16, 185, 129, 0.1);
        }

        .file-name {
            color: var(--text-secondary);
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Éditer Article</h1>
            <a href="articles.php" class="btn btn-ghost">← Retour</a>
        </div>

        <?php if ($message): ?>
            <div class="form-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card" style="max-width: 700px;">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre de l'article</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5"><?= htmlspecialchars($article['description']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Prix (€)</label>
                        <input type="number" step="0.01" name="price" value="<?= $article['price'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?= $article['stock'] ?? 0 ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Image de l'article</label>
                    
                    <?php if ($article['image_link'] && file_exists($article['image_link'])): ?>
                        <div class="image-preview" style="margin-bottom: 1rem;">
                            <img src="<?= htmlspecialchars('../' . $article['image_link']) ?>" alt="Image actuelle">
                        </div>
                        <p class="text-tertiary" style="margin-bottom: 1rem; font-size: 0.9rem;">Image actuelle</p>
                    <?php endif; ?>

                    <div class="file-input-wrapper">
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                        <label for="image" class="file-input-label">
                            📸 Cliquez ou glissez votre image ici<br>
                            <span style="font-size: 0.85rem; color: var(--text-tertiary);">(JPG, PNG, GIF, WebP • Max 5MB)</span>
                            <span id="file-name" class="file-name"></span>
                        </label>
                    </div>
                    <div id="preview"></div>
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1.5rem;">
                    Mettre à jour l'article
                </button>
            </form>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('image');
        const fileLabel = document.querySelector('.file-input-label');
        const fileName = document.getElementById('file-name');
        const preview = document.getElementById('preview');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileName.textContent = file.name;
                fileLabel.classList.add('has-file');

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<div class="image-preview"><img src="' + e.target.result + '" alt="Aperçu"></div>';
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = '';
                fileLabel.classList.remove('has-file');
                preview.innerHTML = '';
            }
        });

        const dragArea = document.querySelector('.file-input-wrapper');

        dragArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileLabel.style.backgroundColor = 'rgba(168, 85, 247, 0.2)';
        });

        dragArea.addEventListener('dragleave', () => {
            fileLabel.style.backgroundColor = '';
        });

        dragArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileLabel.style.backgroundColor = '';
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
    </script>

</body>
</html>
