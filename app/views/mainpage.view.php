<!DOCTYPE html>
<html lang="en" ng-app="questApp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Quests</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet">
    <link href="<?= ROOT ?>/assets/css/quest.css" rel="stylesheet">
    
    <script src="<?= ROOT ?>/assets/js/app.js"></script>
    <script src="<?= ROOT ?>/assets/js/services/QuestService.js"></script>
    <script src="<?= ROOT ?>/assets/js/controller/QuestController.js"></script>

</head>
<body ng-controller="QuestController">
    <a class="logout-btn" href="<?= ROOT ?>/logout">Logout</a>
    <a class="add-btn" href="<?= ROOT ?>/addquest">Ask for Help</a>
<h1 class="board-title">Notice Board</h1>

<div class="notice-board">
    <div class="nail-bottom-left"></div>
    <div class="nail-bottom-right"></div>

    <div class="quest-paper" ng-repeat="quest in quests">

    <h3>{{ quest.title }}</h3>

    <p>{{ quest.description }}</p>

    <p><strong>XP:</strong> {{ quest.xp_reward }}</p>

    <p><strong>Reward coins:</strong> {{ quest.coins_reward }}</p>

</div>

<p ng-if="quests.length === 0">No quests available.</p>

</div>

</body>
</html>