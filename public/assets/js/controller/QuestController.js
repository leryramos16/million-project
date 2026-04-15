app.controller('QuestController', function($scope, $http, Quest) { // Removed Swal from injection if using global
    
    $scope.quests = [];

    $scope.loadQuests = function() {
        Quest.getApi('/mymillionpesoproject/public/questapi', {}).then(function(res) {
            $scope.quests = res.data;
        });
    };

    $scope.loadQuests();

    $scope.do_accept_quest = function(quest_id) {
        if (!quest_id) {
            Swal.fire("Error", "No quest found!", "error");
            return;
        }

        // Loading State
        Swal.fire({
            title: "Accepting quest...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var data = {
            quest_id: quest_id,
            class: "QuestController",
            method: "acceptQuest"
        };

        Quest.postApi("/mymillionpesoproject/public/questapi", data)
            .then(function(response) {
                // Success State
                Swal.fire({
                    icon: "success",
                    title: "Accepted!",
                    text: "Quest successfully accepted"
                });
                $scope.loadQuests();
            })
            .catch(function(err) { // Use .catch for cleaner error handling
                // Error State
                Swal.fire({
                    icon: "error",
                    title: "Failed",
                    text: "Could not accept quest"
                });
            });
    };
});
