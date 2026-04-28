<!-- app/views/addquest.view.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quest</title>
    <link href="<?= ROOT ?>/assets/css/quest.css" rel="stylesheet">
</head>
<body class="quest-form-page">
    <a class="back-btn" href="<?= ROOT ?>/mainpage">&#8592;</a>

    <h1 class="board-title">Submit a Quest Request</h1>

    <div class="quest-form-card">
        <form action="<?= ROOT ?>/addquest" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="title">Quest Title</label>
                <input type="text" name="title" id="title" required placeholder="Enter quest title...">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="6" required placeholder="Describe your request..."></textarea>
            </div>

            <div class="payment-box">
                <strong>Payment Fee: ₱20</strong>
                <p>
                    Send payment to:<br>
                    GCash: <b>09XXXXXXXXX</b><br>
                    Maya: <b>09XXXXXXXXX</b>
                </p>
            </div>

            <div class="form-group">
                <label for="payment_proof">Upload Payment Screenshot</label>
                <input type="file" name="payment_proof" id="payment_proof" required>
            </div>

            <button class="submit-quest-btn" type="submit">
                Submit Quest
            </button>

        </form>
    </div>
</body>
</html>