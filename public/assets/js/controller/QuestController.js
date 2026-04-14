app.controller('QuestController', function($scope, QuestService) {

    $scope.quests = [];

    $scope.loadQuests = function() {
        QuestService.getQuests().then(function(res) {
            console.log("FINALLY I DID AN API CALLED WEB! lets GO LERY");
            $scope.quests = res.data;
        });
    };

    $scope.loadQuests();
});