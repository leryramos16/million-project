<!-- app/views/addquest.view.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quest</title>
    <link href="<?= ROOT ?>/assets/css/quest.css" rel="stylesheet">
</head>
<body>
    <a class="back-btn" href="<?= ROOT ?>/mainpage" style="text-decoration: none; font-size: 20px; font-weight: bold;">&#8592;</a>

    <h1 class="board-title">Submit a Quest Request</h1>

    <div class="notice-board" style="max-width:600px; margin:50px auto; padding:30px;">
        <form action="<?= ROOT ?>/addquest" method="POST">
            <div style="margin-bottom:15px;">
                <label for="title"><strong>Quest Title</strong></label><br>
                <input type="text" name="title" id="title" required style="width:100%; padding:8px;">
            </div>

            <div style="margin-bottom:15px;">
                <label for="description"><strong>Description</strong></label><br>
                <textarea name="description" id="description" rows="6" required style="width:100%; padding:8px;"></textarea>
            </div>
            <button type="submit" style="padding:10px 20px; background:#2e1b0e; color:#f1e0b6; font-weight:bold; border:none; border-radius:5px; cursor:pointer;">
                Submit Quest
            </button>
        </form>
    </div>
</body>
</html>