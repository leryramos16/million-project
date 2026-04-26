<!DOCTYPE html>
<html lang="en" ng-app="questApp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests</title>

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet">
    <link href="<?= ROOT ?>/assets/css/quest.css" rel="stylesheet">

    <script src="<?= ROOT ?>/assets/js/app.js"></script>
    <script src="<?= ROOT ?>/assets/js/services/Quest.js"></script>
    <script src="<?= ROOT ?>/assets/js/controller/MyRequestController.js"></script>
</head>

<body ng-controller="MyRequestController" class="journal-body">

<a class="back-btn" href="<?= ROOT ?>/mainpage">← Back</a>

<h1 class="journal-title">My Requests</h1>

<div class="journal-container">

    <div class="journal-entry" ng-repeat="request in myRequests">

        <h3>{{ request.title }}</h3>

        <p class="journal-desc">
            {{ request.description }}
        </p>

        <div class="journal-meta">
            <p>
                <strong>Status:</strong>
                <span class="status {{ request.status }}">
                    {{ request.status }}
                </span>
            </p>

            <p ng-if="request.accepted_by">
                <strong>Accepted by:</strong> {{ request.accepted_by_name }}
            </p>

            <p ng-if="!request.accepted_by">
                <strong>Accepted by:</strong> No one yet
            </p>
        </div>

    </div>

    <p class="empty-request" ng-if="myRequests.length === 0">
        No requests yet.
    </p>

</div>

</body>
</html>