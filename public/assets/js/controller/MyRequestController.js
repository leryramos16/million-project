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

    $scope.loadMyRequests();

});