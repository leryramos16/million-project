app.controller('MyRequestController', function($scope, $timeout, Quest) {

    $scope.myRequests = [];
    $scope.currentFilter = 'pending';
    $scope.page = 1;
    $scope.limit = 3;          // <-- show only 3 per page
    $scope.totalPages = 1;
    $scope.flipClass = '';
    $scope.isFlipping = false;

    $scope.do_filter = function(status) {
        $scope.currentFilter = status;
        $scope.page = 1;
        $scope.loadMyRequests();
    };

    $scope.loadMyRequests = function () {
        var data = {
            method: 'getMyRequests',
            status: $scope.currentFilter,
            page: $scope.page,
            limit: $scope.limit
        };

        // return the promise so turnPage() can wait for the data
        return Quest.postApi('/mymillionpesoproject/public/questapi', data)
            .then(function(response) {
                if (response.data.status) {
                    $scope.myRequests = response.data.data;
                    $scope.totalPages = response.data.totalPages;
                } else {
                    $scope.myRequests = [];
                    $scope.totalPages = 1;
                }
            })
            .catch(function(error) {
                console.error(error);
                Swal.fire("Error", "Failed to load my requests", "error");
            });
    };

    function turnPage(direction) {
        if ($scope.isFlipping) return;   // ignore spam clicks mid-turn
        $scope.isFlipping = true;

        // Phase 1: fold the current page away
        $scope.flipClass = (direction === 'next') ? 'page-out-next' : 'page-out-prev';

        $timeout(function() {
            // Phase 2: swap page number + load new data (page is hidden now)
            $scope.page += (direction === 'next') ? 1 : -1;

            $scope.loadMyRequests().then(function() {
                // Phase 3: bring the new page in
                $scope.flipClass = (direction === 'next') ? 'page-in-next' : 'page-in-prev';

                $timeout(function() {
                    $scope.flipClass = '';
                    $scope.isFlipping = false;
                }, 350);
            });
        }, 350);
    }

    $scope.nextPage = function() {
        if ($scope.page < $scope.totalPages) turnPage('next');
    };

    $scope.prevPage = function() {
        if ($scope.page > 1) turnPage('prev');
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