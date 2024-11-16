<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GALERIE DE MARIAGE</title>
    <link rel="stylesheet" href="galerie.css" />
</head>

<body>
    

    <main>
        <!-- Upload Form -->
        <section class="upload-section">
            <h2>Vos photos de mariage</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="fileUpload">Choisissez des images :</label>
                <input type="file" name="fileUpload[]" id="fileUpload" accept="image/*" multiple required>
                <button type="submit" name="upload">Télécharger les Photos</button>
            </form>
        </section>

        <section class="gallery-section">
    <h2>Galerie de Photo de mariage</h2>
    
    <form method="post" class="delete-selected-form">
        <div class="gallery-carousel">
            <?php
            $images = glob("uploads/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
            foreach ($images as $image) {
                $imageName = basename($image);
                echo "
                <div class='gallery-item'>
                    <img src='$image' alt='Wedding Photo' />
                    <label>
                        <input type='radio' name='imageToDelete' value='$imageName'>
                        Sélectionner
                    </label>
                    <button type='submit' name='deleteSelected' value='$imageName'>Supprimer</button>
                </div>";
            }
            ?>
        </div>
    </form>
</section>

    </main>

    <footer>
        <p>&copy; 2023 Wedding Memories. All rights reserved.</p>
    </footer>

    <script>
        const carousel = document.querySelector('.gallery-carousel');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        prevBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: -200, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: 200, behavior: 'smooth' });
        });
    </script>
</body>

</html>
<?php
$images = glob("uploads/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
if (empty($images)) {
    echo "<p>Aucune image disponible.</p>";
}
?>


<?php
if (isset($_POST['upload'])) {
    $targetDir = "uploads/";
    $uploadOk = 1;
    $imageFileType = "";

    // Vérifier si des fichiers ont été téléchargés
    if (isset($_FILES['fileUpload']) && !empty($_FILES['fileUpload']['name'][0])) {
        $files = $_FILES['fileUpload'];

        foreach ($files['name'] as $key => $filename) {
            $targetFile = $targetDir . basename($filename);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Vérifier si le fichier est une image
            $check = getimagesize($files["tmp_name"][$key]);
            if ($check === false) {
                echo "Le fichier n'est pas une image : $filename";
                $uploadOk = 0;
                continue;
            }

            // Vérifier la taille de l'image (limite à 2MB)
            if ($files["size"][$key] > 2000000) {
                echo "Le fichier $filename est trop lourd. La taille maximale autorisée est de 2MB.";
                $uploadOk = 0;
                continue;
            }

            // Autoriser certains formats d'images
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés pour $filename.";
                $uploadOk = 0;
                continue;
            }

            // Si toutes les vérifications passent, essayer de déplacer le fichier
            if ($uploadOk == 1) {
                if (move_uploaded_file($files["tmp_name"][$key], $targetFile)) {
                    echo "Le fichier $filename a été téléchargé avec succès.";
                } else {
                    echo "Désolé, une erreur est survenue lors du téléchargement du fichier $filename.";
                }
            }
        }
    } else {
        echo "Aucun fichier n'a été sélectionné.";
    }
}


if (isset($_POST['deleteSelected'])) {
    $imageToDelete = "uploads/" . $_POST['deleteSelected'];

    if (file_exists($imageToDelete)) {
        unlink($imageToDelete); // Supprime l'image
        echo "<p>L'image " . htmlspecialchars($_POST['deleteSelected']) . " a été supprimée.</p>";
    } else {
        echo "<p>Erreur : L'image n'existe pas.</p>";
    }

    // Recharge la page pour mettre à jour la galerie
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}