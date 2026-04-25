app.controller('QuestController', function($scope, Quest) {

    $scope.quests = [];

    $scope.loadQuests = function() {
        Quest.getApi('/mymillionpesoproject/public/questapi', {})
            .then(function(res) {
                if(Array.isArray(res.data)) {
                    $scope.quests = res.data;
                } else {
                    console.error("Expected array, got:", res.data);
                    $scope.quests = [];
                }
            })
            .catch(function(err) {
                console.error("Quest API error:", err);
                Swal.fire("Error")
            });
    };

    $scope.loadQuests();


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
            confirmButtonText: "Yes, accept it"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Accepting...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var data = {
                    quest_id: quest_id,
                    method: "acceptQuest"
                };
                
                Quest.postApi('/mymillionpesoproject/public/questapi', data)
                    .then(function(res) {

                        if (res.data.status) {
                            Swal.fire("Success", "Quest accepted", "success");

                            $scope.loadQuests();
                        } else {
                            Swal.fire("Failed", res.data.message, "error");
                        }
                    })
                    .catch(function(err) {
                        console.error(err);
                        Swal.fire("Error", "Something went wrong", "error");
                    });
            }
        });
    };


})