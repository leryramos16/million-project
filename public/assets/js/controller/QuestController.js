app.controller('QuestController', function($scope, Quest) {

    $scope.quests = [];
    $scope.player = {};

    $scope.getRequiredXp = function() {
        var level = parseInt($scope.player.level || 1);
        return level * 100;
    };

    $scope.getXpPercent = function() {
        var xp = parseInt($scope.player.xp || 0);
        var requiredXp = $scope.getRequiredXp();

        if (requiredXp <= 0) return 0;

        return Math.min(100, (xp / requiredXp) * 100);
    };

    $scope.loadUserStats = function() {
        var data = {
            method: 'getUserStats'
        };

        Quest.postApi('/mymillionpesoproject/public/questapi', data)
            .then(function(response) {
                console.log("User Stats:", response.data);

                if (response.data.status) {
                    $scope.player = response.data.data;
                }
            })
            .catch(function(error) {
                console.error("User stats error:", error);
            });
    };

    $scope.loadQuests = function() {
        Quest.getApi('/mymillionpesoproject/public/questapi', {})
            .then(function(res) {
                if (Array.isArray(res.data)) {
                    $scope.quests = res.data;
                } else {
                    console.error("Expected array, got:", res.data);
                    $scope.quests = [];
                }
            })
            .catch(function(err) {
                console.error("Quest API error:", err);
                Swal.fire("Error", "Failed to load quests", "error");
            });
    };

    $scope.do_accept_quest = function(quest_id) {
        if (!quest_id) {
            Swal.fire("Error", "No quest selected", "error");
            return;
        }

        Swal.fire({
            title: "Accept this quest?",
            text: "You will take this quest",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, accept it",
            customClass: {
                popup: 'medieval-popup',
                title: 'medieval-title',
                htmlContainer: 'medieval-text',
                confirmButton: 'medieval-confirm',
                cancelButton: 'medieval-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (!result.isConfirmed) return;

            var data = {
                quest_id: quest_id,
                method: "acceptQuest"
            };

            Quest.postApi('/mymillionpesoproject/public/questapi', data)
                .then(function(res) {
                    if (res.data.status) {
                        Swal.fire({
                            title: "Quest Accepted!",
                            text: "The contract is now bound.",
                            icon: "success",
                            customClass: {
                                popup: 'medieval-popup',
                                title: 'medieval-title',
                                htmlContainer: 'medieval-text',
                                confirmButton: 'medieval-confirm'
                            },
                            buttonsStyling: false
                        });

                        $scope.loadQuests();
                        $scope.loadUserStats();
                    } else {
                        Swal.fire("Failed", res.data.message, "error");
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    Swal.fire("Error", "Something went wrong", "error");
                });
        });
    };

    $scope.loadUserStats();
    $scope.loadQuests();

});