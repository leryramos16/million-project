app.controller('MyRequestController', function($scope, Quest) {

    $scope.myRequests = [];

    $scope.loadMyRequests = function () {
        var data = {
            method: 'getMyRequests'
        };

        Quest.postApi('/mymillionpesoproject/public/questapi', data)
            .then(function(response) {
                console.log("My Requests:", response.data);

                if (response.data.status) {
                    $scope.myRequests = response.data.data;
                } else {
                    $scope.myRequests = [];
                }
            })
            .catch(function(error) {
                console.error(error);
                Swal.fire("Error", "Failed to load my requests", "error");
            });
    };
        
    $scope.markQuestDone = function (quest_id) {

    Swal.fire({
        title: 'Seal the Contract?',
        text: 'The reward shall be granted to the one who fulfilled this quest.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, complete it!',
        cancelButtonText: 'Cancel',
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
            method: 'markQuestDone',
            quest_id: quest_id
        };

        Quest.postApi('/mymillionpesoproject/public/questapi', data)
            .then(function(response) {

                if (response.data.status) {
                    Swal.fire({
                        title: "Success",
                        text: response.data.message,
                        icon: "success",
                        customClass: {
                            popup: 'medieval-popup',
                            title: 'medieval-title',
                            htmlContainer: 'medieval-text',
                            confirmButton: 'medieval-confirm'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        location.reload();
                    });
                    $scope.loadMyRequests();
                } else {
                    Swal.fire("Failed", response.data.message, "warning");
                }

            })
            .catch(function(error) {
                console.error(error);
                Swal.fire("Error", "Failed to mark quest as done", "error");
            });

    });
};

    $scope.loadMyRequests();

});