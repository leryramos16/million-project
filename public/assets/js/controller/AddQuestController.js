app.controller('AddQuestController', function($scope, Quest) {

    $scope.quest = {
        title: '',
        description: ''
    };

    $scope.submitQuest = function() {
        console.log("paymentProof:", $scope.quest.paymentProof);
        if (!$scope.quest.paymentProof) {
            Swal.fire("Warning", "Please upload payment screenshot", "warning");
            return;
        }

        var formData = new FormData();

        formData.append('method', 'addQuest');
        formData.append('title', $scope.quest.title);
        formData.append('description', $scope.quest.description);
        formData.append('payment_proof', $scope.quest.paymentProof);

        Quest.postFormData('/mymillionpesoproject/public/questapi', formData)
            .then(function(response) {
                console.log("This is the response.data", response.data);

                if (response.data.status) {
                    Swal.fire("Success", response.data.message, "success")
                        .then(function() {
                             window.location.href = '/mymillionpesoproject/public/mainpage';
                        });
                } else {
                    Swal.fire("Failed", response.data.message, "warning");
                }
            })
            .catch(function(error){
                console.error(error);
                Swal.fire("Error", "Failed to submit quest", "error");
            });
    };
});