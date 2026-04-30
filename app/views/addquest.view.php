<!DOCTYPE html>
<html lang="en" ng-app="questApp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quest</title>

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <script src="<?= ROOT ?>/assets/js/app.js"></script>
    <script src="<?= ROOT ?>/assets/js/services/Quest.js"></script>
    <script src="<?= ROOT ?>/assets/js/controller/AddQuestController.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="<?= ROOT ?>/assets/css/quest.css" rel="stylesheet">
</head>
<body class="quest-form-page" ng-controller="AddQuestController">
    <a class="back-btn" href="<?= ROOT ?>/mainpage">&#8592;</a>

    <h1 class="board-title">Submit a Quest Request</h1>

    <div class="quest-form-card">
        <form ng-submit="submitQuest()" enctype="multipart/form-data">

            <div class="form-group">
                <label for="title">Quest Title</label>
                <input type="text" ng-model="quest.title" required placeholder="Enter quest title...">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea ng-model="quest.description" rows="6" required placeholder="Describe your request..."></textarea>
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
                <input type="file" file-model="quest.paymentProof" required>
            </div>

            <button class="submit-quest-btn" type="submit">
                Submit Quest
            </button>

        </form>
    </div>
</body>
</html>